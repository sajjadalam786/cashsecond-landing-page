<?php
/**
 * CashSecond - Buyback Questionnaire Lead Processor
 * Handles validation, CSRF verification, rate limiting, and saves full question-by-question audit log.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Invalid request method. Only POST is allowed.'
    ]);
    exit;
}

// 1. Session Rate Limiting (3 seconds)
$now = time();
if (isset($_SESSION['last_questionnaire_time']) && ($now - $_SESSION['last_questionnaire_time']) < 3) {
    http_response_code(429);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Please wait a few seconds before submitting again.'
    ]);
    exit;
}

// 2. CSRF Token Verification
$csrf_token = isset($_POST['csrf_token']) ? trim($_POST['csrf_token']) : '';
if (!empty($_SESSION['csrf_token']) && !empty($csrf_token)) {
    if (!hash_equals($_SESSION['csrf_token'], $csrf_token)) {
        http_response_code(403);
        echo json_encode([
            'status'  => 'error',
            'message' => 'Security token expired. Please refresh the page and try again.'
        ]);
        exit;
    }
}

// 3. Honeypot Spam Protection
if (!empty($_POST['website_hp'])) {
    echo json_encode([
        'status'  => 'success',
        'message' => 'Thank you! Your doorstep pickup request has been scheduled.',
        'ref_id'  => 'CS-VAL-' . strtoupper(substr(md5(uniqid()), 0, 6))
    ]);
    exit;
}

// 4. Handle Feedback Submission Action
if (isset($_POST['action']) && $_POST['action'] === 'feedback') {
    $ref_id       = isset($_POST['ref_id']) ? htmlspecialchars(strip_tags(trim($_POST['ref_id'])), ENT_QUOTES, 'UTF-8') : '';
    $rating       = isset($_POST['feedback_rating']) ? htmlspecialchars(strip_tags(trim($_POST['feedback_rating'])), ENT_QUOTES, 'UTF-8') : '';
    $comment      = isset($_POST['feedback_comment']) ? htmlspecialchars(strip_tags(trim($_POST['feedback_comment'])), ENT_QUOTES, 'UTF-8') : '';
    $pickup_date  = isset($_POST['pickup_date']) ? htmlspecialchars(strip_tags(trim($_POST['pickup_date'])), ENT_QUOTES, 'UTF-8') : 'Today';
    $pickup_slot  = isset($_POST['pickup_slot']) ? htmlspecialchars(strip_tags(trim($_POST['pickup_slot'])), ENT_QUOTES, 'UTF-8') : 'Express (Within 6 Hours)';

    $feedback_entry = [
        'type'           => 'valuation_feedback',
        'ref_id'         => $ref_id,
        'timestamp'      => date('Y-m-d H:i:s'),
        'rating'         => $rating,
        'comment'        => $comment,
        'pickup_date'    => $pickup_date,
        'pickup_slot'    => $pickup_slot,
        'ip'             => $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN'
    ];

    $logs_dir = __DIR__ . '/../logs';
    if (!is_dir($logs_dir)) {
        @mkdir($logs_dir, 0755, true);
    }
    @file_put_contents($logs_dir . '/questionnaire_feedback.jsonl', json_encode($feedback_entry, JSON_UNESCAPED_UNICODE) . "\n", FILE_APPEND | LOCK_EX);

    echo json_encode([
        'status'  => 'success',
        'message' => 'Thank you for your feedback! Doorstep pickup scheduled.',
        'ref_id'  => $ref_id
    ]);
    exit;
}

date_default_timezone_set('Asia/Kolkata');

// Extract & Sanitize Contact Fields
$name        = isset($_POST['full_name']) ? htmlspecialchars(strip_tags(trim($_POST['full_name'])), ENT_QUOTES, 'UTF-8') : '';
$phone       = isset($_POST['phone_number']) ? preg_replace('/[^0-9+]/', '', trim($_POST['phone_number'])) : '';
$email       = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
$address     = isset($_POST['address']) && !empty(trim($_POST['address'])) ? htmlspecialchars(strip_tags(trim($_POST['address'])), ENT_QUOTES, 'UTF-8') : 'Mumbai (Doorstep Pickup)';
$pincode     = isset($_POST['pincode']) && !empty(trim($_POST['pincode'])) ? preg_replace('/[^0-9]/', '', trim($_POST['pincode'])) : '400021';
$pickup_date = isset($_POST['pickup_date']) ? htmlspecialchars(strip_tags(trim($_POST['pickup_date'])), ENT_QUOTES, 'UTF-8') : 'Today';
$pickup_slot = isset($_POST['pickup_slot']) ? htmlspecialchars(strip_tags(trim($_POST['pickup_slot'])), ENT_QUOTES, 'UTF-8') : 'Express (Within 2 Hours)';

// Extract Device & Questionnaire Payload
$model        = isset($_POST['device_model']) ? htmlspecialchars(strip_tags(trim($_POST['device_model'])), ENT_QUOTES, 'UTF-8') : 'Apple iPhone 13';
$variant      = isset($_POST['device_variant']) ? htmlspecialchars(strip_tags(trim($_POST['device_variant'])), ENT_QUOTES, 'UTF-8') : '128 GB';
$est_value    = isset($_POST['estimated_value']) ? htmlspecialchars(strip_tags(trim($_POST['estimated_value'])), ENT_QUOTES, 'UTF-8') : '₹23,220';
$answers_json = isset($_POST['questionnaire_answers']) ? trim($_POST['questionnaire_answers']) : '';

$errors = [];

// Validation Rules
if (empty($name) || mb_strlen($name) < 2 || mb_strlen($name) > 80) {
    $errors['full_name'] = 'Please enter your full name (at least 2 characters).';
}

$clean_digits = preg_replace('/[^0-9]/', '', $phone);
if (strlen($clean_digits) < 10 || strlen($clean_digits) > 13) {
    $errors['phone_number'] = 'Please enter a valid 10-digit mobile/WhatsApp number.';
}

if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Please enter a valid email address.';
}

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Please correct the highlighted fields.',
        'errors'  => $errors
    ]);
    exit;
}

// Generate Structured Unique Lead ID: EXG-YYYYMMDD-XXX
$lead_id = 'EXG-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 4));

// Parse Questionnaire Answers & Valuation Adjustments
$parsed_answers = [];
if (!empty($answers_json)) {
    $decoded = json_decode($answers_json, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        $parsed_answers = $decoded;
    }
}

$adjustments_json = isset($_POST['valuation_adjustments']) ? trim($_POST['valuation_adjustments']) : '';
$parsed_adjustments = [];
if (!empty($adjustments_json)) {
    $decoded_adj = json_decode($adjustments_json, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        $parsed_adjustments = $decoded_adj;
    }
}

// Full Lead Payload
$lead_entry = [
    'lead_id'          => $lead_id,
    'ref_id'           => $lead_id,
    'timestamp'        => date('Y-m-d H:i:s'),
    'ip'               => $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN',
    'customer'         => [
        'name'         => $name,
        'phone'        => $phone,
        'email'        => $email,
        'address'      => $address,
        'pincode'      => $pincode,
        'pickup_date'  => $pickup_date,
        'pickup_slot'  => $pickup_slot
    ],
    'device'           => [
        'model'        => $model,
        'variant'      => $variant,
        'estimated_val'=> $est_value
    ],
    'answers'          => $parsed_answers,
    'adjustments'      => $parsed_adjustments,
    'condition_audit'  => $parsed_answers
];

// 1. Save Full Row to Google Sheets (Server-Side Integration)
require_once __DIR__ . '/../includes/GoogleSheetsService.php';
$sheetsResult = GoogleSheetsService::appendValuationRow($lead_entry);

// 2. Append to Local Audit Log
$logs_dir = __DIR__ . '/../logs';
if (!is_dir($logs_dir)) {
    @mkdir($logs_dir, 0755, true);
}

$log_file = $logs_dir . '/questionnaire_leads.jsonl';
$log_line = json_encode($lead_entry, JSON_UNESCAPED_UNICODE) . "\n";
@file_put_contents($log_file, $log_line, FILE_APPEND | LOCK_EX);

$_SESSION['last_questionnaire_time'] = time();

echo json_encode([
    'status'   => 'success',
    'message'  => 'Your valuation has been submitted successfully.',
    'ref_id'   => $lead_id,
    'lead_id'  => $lead_id,
    'sheets'   => $sheetsResult,
    'details'  => [
        'device'    => "$model ($variant)",
        'est_value' => $est_value
    ]
]);
exit;

