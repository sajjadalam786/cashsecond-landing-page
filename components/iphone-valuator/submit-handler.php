<?php
/**
 * CashSecond iPhone Valuation Component — Backend Submit Handler
 *
 * Reusable, self-contained POST handler for the valuation questionnaire.
 * Handles: CSRF, honeypot, rate-limiting, JSONL logging, Google Sheets sync.
 *
 * Accepts the same POST fields as forms/buyback-questionnaire.php but
 * operates independently so this component can be dropped into any site.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Only POST allowed.']);
    exit;
}

$action           = isset($_POST['action']) ? trim($_POST['action']) : '';
$is_feedback_action = ($action === 'feedback' || $action === 'update_feedback');

// --- Rate Limiting (skip for feedback actions) ---
if (!$is_feedback_action) {
    $now = time();
    if (isset($_SESSION['last_valuator_submit']) && ($now - $_SESSION['last_valuator_submit']) < 3) {
        http_response_code(429);
        echo json_encode(['status' => 'error', 'message' => 'Please wait a few seconds before submitting again.']);
        exit;
    }
}

// --- CSRF Verification ---
if (!$is_feedback_action) {
    $csrf = isset($_POST['csrf_token']) ? trim($_POST['csrf_token']) : '';
    if (!empty($_SESSION['csrf_token']) && !empty($csrf)) {
        if (!hash_equals($_SESSION['csrf_token'], $csrf)) {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'Security token expired. Please refresh and try again.']);
            exit;
        }
    }
}

// --- Honeypot Trap ---
if (!$is_feedback_action && !empty($_POST['website_hp'])) {
    echo json_encode([
        'status'  => 'success',
        'message' => 'Your valuation has been submitted.',
        'ref_id'  => 'CS-VAL-' . strtoupper(substr(md5(uniqid()), 0, 6))
    ]);
    exit;
}

// --- Feedback / Pickup Scheduling Action ---
if ($is_feedback_action) {
    $ref_id      = isset($_POST['ref_id'])           ? htmlspecialchars(strip_tags(trim($_POST['ref_id'])),           ENT_QUOTES, 'UTF-8') : (!empty($_POST['lead_id']) ? htmlspecialchars(strip_tags(trim($_POST['lead_id'])), ENT_QUOTES, 'UTF-8') : '');
    $rating      = isset($_POST['feedback_rating'])  ? htmlspecialchars(strip_tags(trim($_POST['feedback_rating'])),  ENT_QUOTES, 'UTF-8') : 'Good Price';
    $comment     = isset($_POST['feedback_comment']) ? htmlspecialchars(strip_tags(trim($_POST['feedback_comment'])), ENT_QUOTES, 'UTF-8') : '';
    $pickup_date = isset($_POST['pickup_date'])       ? htmlspecialchars(strip_tags(trim($_POST['pickup_date'])),      ENT_QUOTES, 'UTF-8') : 'Today';
    $pickup_slot = isset($_POST['pickup_slot'])       ? htmlspecialchars(strip_tags(trim($_POST['pickup_slot'])),      ENT_QUOTES, 'UTF-8') : 'Express (Within 6 Hours)';
    $pickup_addr = isset($_POST['pickup_address'])    ? htmlspecialchars(strip_tags(trim($_POST['pickup_address'])),   ENT_QUOTES, 'UTF-8') : '';
    $pincode     = isset($_POST['pincode'])           ? preg_replace('/[^0-9]/', '', trim($_POST['pincode']))          : '';

    $extra_data = [
        'customer_name'   => isset($_POST['customer_name']) ? htmlspecialchars(strip_tags(trim($_POST['customer_name'])), ENT_QUOTES, 'UTF-8') : (isset($_POST['name']) ? htmlspecialchars(strip_tags(trim($_POST['name'])), ENT_QUOTES, 'UTF-8') : ''),
        'customer_phone'  => isset($_POST['customer_phone']) ? preg_replace('/[^0-9+]/', '', trim($_POST['customer_phone'])) : (isset($_POST['phone']) ? preg_replace('/[^0-9+]/', '', trim($_POST['phone'])) : ''),
        'customer_email'  => isset($_POST['customer_email']) ? filter_var(trim($_POST['customer_email']), FILTER_SANITIZE_EMAIL) : '',
        'device_model'    => isset($_POST['device_model']) ? htmlspecialchars(strip_tags(trim($_POST['device_model'])), ENT_QUOTES, 'UTF-8') : (isset($_POST['model']) ? htmlspecialchars(strip_tags(trim($_POST['model'])), ENT_QUOTES, 'UTF-8') : ''),
        'estimated_value' => isset($_POST['estimated_value']) ? htmlspecialchars(strip_tags(trim($_POST['estimated_value'])), ENT_QUOTES, 'UTF-8') : (isset($_POST['price']) ? htmlspecialchars(strip_tags(trim($_POST['price'])), ENT_QUOTES, 'UTF-8') : '')
    ];

    // Resolve project root (2 levels up from components/iphone-valuator/)
    $project_root = dirname(__DIR__, 2);
    $logs_dir     = $project_root . '/logs';
    if (!is_dir($logs_dir)) {
        @mkdir($logs_dir, 0755, true);
    }

    // Fallback: search recent logs if name/phone/model/price missing
    if (empty($extra_data['customer_phone']) || empty($extra_data['customer_name']) || empty($extra_data['estimated_value'])) {
        foreach (['valuator_leads.jsonl', 'questionnaire_leads.jsonl'] as $logFile) {
            $filePath = $logs_dir . '/' . $logFile;
            if (file_exists($filePath)) {
                $lines = @file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                if ($lines) {
                    for ($i = count($lines) - 1; $i >= 0; $i--) {
                        $entry = json_decode($lines[$i], true);
                        if ($entry && (
                            (isset($entry['ref_id']) && $entry['ref_id'] === $ref_id) ||
                            (isset($entry['lead_id']) && $entry['lead_id'] === $ref_id)
                        )) {
                            if (empty($extra_data['customer_name']))  $extra_data['customer_name'] = $entry['name'] ?? $entry['customer_name'] ?? $entry['full_name'] ?? '';
                            if (empty($extra_data['customer_phone'])) $extra_data['customer_phone'] = $entry['phone'] ?? $entry['customer_phone'] ?? $entry['phone_number'] ?? '';
                            if (empty($extra_data['device_model']))   $extra_data['device_model'] = $entry['model'] ?? $entry['device_model'] ?? '';
                            if (empty($extra_data['estimated_value']))$extra_data['estimated_value'] = $entry['estimated_value'] ?? $entry['val'] ?? $entry['final_price'] ?? '';
                            break 2;
                        }
                    }
                }
            }
        }
    }

    $feedback_entry = array_merge([
        'type'           => 'valuation_feedback',
        'ref_id'         => $ref_id,
        'timestamp'      => date('Y-m-d H:i:s'),
        'rating'         => $rating,
        'comment'        => $comment,
        'pickup_date'    => $pickup_date,
        'pickup_slot'    => $pickup_slot,
        'pickup_address' => $pickup_addr,
        'pincode'        => $pincode,
        'ip'             => $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN'
    ], $extra_data);

    @file_put_contents($logs_dir . '/valuator_feedback.jsonl',
        json_encode($feedback_entry, JSON_UNESCAPED_UNICODE) . "\n",
        FILE_APPEND | LOCK_EX
    );

    // Sync to Google Sheets and dispatch *100% Confirmed* email via shared service
    $sheets_result = false;
    $sheets_service = $project_root . '/includes/GoogleSheetsService.php';
    if (file_exists($sheets_service)) {
        require_once $sheets_service;
        $sheets_result = GoogleSheetsService::updateFeedbackRow(
            $ref_id, $rating, $comment, $pickup_date, $pickup_slot, $pickup_addr, $pincode, $extra_data
        );
    }

    echo json_encode([
        'status'  => 'success',
        'message' => 'Pickup scheduled! We will contact you shortly.',
        'ref_id'  => $ref_id,
        'sheets'  => $sheets_result
    ]);
    exit;
}

// --- Main Lead Submission ---
date_default_timezone_set('Asia/Kolkata');

$name        = isset($_POST['full_name'])     ? htmlspecialchars(strip_tags(trim($_POST['full_name'])),  ENT_QUOTES, 'UTF-8') : '';
$phone       = isset($_POST['phone_number'])  ? preg_replace('/[^0-9+]/', '', trim($_POST['phone_number'])) : '';
$email       = isset($_POST['email'])         ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
$address     = isset($_POST['address'])       ? htmlspecialchars(strip_tags(trim($_POST['address'])),    ENT_QUOTES, 'UTF-8') : '';
$pincode     = isset($_POST['pincode'])       ? preg_replace('/[^0-9]/', '', trim($_POST['pincode']))    : '';
$pickup_date = isset($_POST['pickup_date'])   ? htmlspecialchars(strip_tags(trim($_POST['pickup_date'])),ENT_QUOTES, 'UTF-8') : '';
$pickup_slot = isset($_POST['pickup_slot'])   ? htmlspecialchars(strip_tags(trim($_POST['pickup_slot'])),ENT_QUOTES, 'UTF-8') : '';

$model       = isset($_POST['device_model'])    ? htmlspecialchars(strip_tags(trim($_POST['device_model'])),   ENT_QUOTES, 'UTF-8') : '';
$variant     = isset($_POST['device_variant'])  ? htmlspecialchars(strip_tags(trim($_POST['device_variant'])), ENT_QUOTES, 'UTF-8') : '';
$base_value  = isset($_POST['base_value'])      ? htmlspecialchars(strip_tags(trim($_POST['base_value'])),     ENT_QUOTES, 'UTF-8') : '';
$est_value   = isset($_POST['estimated_value']) ? htmlspecialchars(strip_tags(trim($_POST['estimated_value'])),ENT_QUOTES, 'UTF-8') : '';

$answers_json     = isset($_POST['questionnaire_answers'])  ? trim($_POST['questionnaire_answers'])  : '';
$adjustments_json = isset($_POST['valuation_adjustments'])  ? trim($_POST['valuation_adjustments'])  : '';

// --- Validation ---
$errors = [];
if (empty($name) || mb_strlen($name) < 2 || mb_strlen($name) > 80) {
    $errors['full_name'] = 'Please enter your full name (at least 2 characters).';
}
$clean_digits = preg_replace('/[^0-9]/', '', $phone);
if (strlen($clean_digits) < 10 || strlen($clean_digits) > 13) {
    $errors['phone_number'] = 'Please enter a valid 10-digit mobile number.';
}
if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Please enter a valid email address.';
}
if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(['status' => 'error', 'message' => 'Please correct the highlighted fields.', 'errors' => $errors]);
    exit;
}

// --- Parse JSON Payloads ---
$parsed_answers     = [];
$parsed_adjustments = [];
if (!empty($answers_json)) {
    $dec = json_decode($answers_json, true);
    if (json_last_error() === JSON_ERROR_NONE) $parsed_answers = $dec;
}
if (!empty($adjustments_json)) {
    $dec = json_decode($adjustments_json, true);
    if (json_last_error() === JSON_ERROR_NONE) $parsed_adjustments = $dec;
}

// --- Generate Lead ID ---
$lead_id = !empty($_POST['lead_id'])
    ? htmlspecialchars(strip_tags(trim($_POST['lead_id'])), ENT_QUOTES, 'UTF-8')
    : ('CS-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 5)));

// --- Build Lead Payload ---
$lead_entry = [
    'lead_id'    => $lead_id,
    'ref_id'     => $lead_id,
    'timestamp'  => date('Y-m-d H:i:s'),
    'ip'         => $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN',
    'source'     => 'iphone-valuator-component',
    'customer'   => [
        'name'        => $name,
        'phone'       => $phone,
        'email'       => $email,
        'address'     => $address,
        'pincode'     => $pincode,
        'pickup_date' => $pickup_date,
        'pickup_slot' => $pickup_slot,
    ],
    'device'     => [
        'model'         => $model,
        'variant'       => $variant,
        'base_val'      => $base_value,
        'estimated_val' => $est_value,
    ],
    'answers'      => $parsed_answers,
    'adjustments'  => $parsed_adjustments,
];

// --- Save to Google Sheets & Send Email ---
$project_root  = dirname(__DIR__, 2);
$sheets_result = false;
$sheets_service= $project_root . '/includes/GoogleSheetsService.php';

if (file_exists($sheets_service)) {
    require_once $sheets_service;
    $sheets_result = GoogleSheetsService::appendValuationRow($lead_entry);
}

// --- Save to Local JSONL Log ---
$logs_dir = $project_root . '/logs';
if (!is_dir($logs_dir)) {
    @mkdir($logs_dir, 0755, true);
}
@file_put_contents(
    $logs_dir . '/valuator_leads.jsonl',
    json_encode($lead_entry, JSON_UNESCAPED_UNICODE) . "\n",
    FILE_APPEND | LOCK_EX
);

$_SESSION['last_valuator_submit'] = time();

echo json_encode([
    'status'   => 'success',
    'message'  => 'Your valuation has been submitted successfully.',
    'ref_id'   => $lead_id,
    'lead_id'  => $lead_id,
    'sheets'   => $sheets_result,
    'details'  => [
        'device'     => "$model ($variant)",
        'base_value' => $base_value,
        'est_value'  => $est_value,
    ]
]);
exit;
