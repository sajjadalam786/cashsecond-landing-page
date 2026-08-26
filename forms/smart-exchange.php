<?php
/**
 * CashSecond - Smart Exchange & Device Check Lead Processor
 * Validates lead submissions, CSRF token, rate limiting, and saves diagnostic payload.
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
if (isset($_SESSION['last_smart_exchange_time']) && ($now - $_SESSION['last_smart_exchange_time']) < 3) {
    http_response_code(429);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Please wait a few seconds before submitting again.'
    ]);
    exit;
}

// 2. CSRF Token Verification
$csrf_token = isset($_POST['csrf_token']) ? trim($_POST['csrf_token']) : '';
if (empty($csrf_token) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrf_token)) {
    http_response_code(403);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Security token expired. Please refresh the page and try again.'
    ]);
    exit;
}

// 3. Honeypot Spam Protection
if (!empty($_POST['website_hp'])) {
    echo json_encode([
        'status'  => 'success',
        'message' => 'Thank you! Your doorstep pickup request has been scheduled.',
        'ref_id'  => 'CS-EX-' . strtoupper(substr(md5(uniqid()), 0, 6))
    ]);
    exit;
}

date_default_timezone_set('Asia/Kolkata');

// Extract & Sanitize Contact Fields
$name     = isset($_POST['full_name']) ? htmlspecialchars(strip_tags(trim($_POST['full_name'])), ENT_QUOTES, 'UTF-8') : '';
$phone    = isset($_POST['phone_number']) ? preg_replace('/[^0-9+]/', '', trim($_POST['phone_number'])) : '';
$email    = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
$location = isset($_POST['location']) ? htmlspecialchars(strip_tags(trim($_POST['location'])), ENT_QUOTES, 'UTF-8') : '';
$pickup_slot = isset($_POST['pickup_slot']) ? htmlspecialchars(strip_tags(trim($_POST['pickup_slot'])), ENT_QUOTES, 'UTF-8') : 'Earliest Available';

// Extract Device & Diagnostic Report Fields
$brand        = isset($_POST['device_brand']) ? htmlspecialchars(strip_tags(trim($_POST['device_brand'])), ENT_QUOTES, 'UTF-8') : 'Apple';
$model        = isset($_POST['device_model']) ? htmlspecialchars(strip_tags(trim($_POST['device_model'])), ENT_QUOTES, 'UTF-8') : '';
$storage      = isset($_POST['device_storage']) ? htmlspecialchars(strip_tags(trim($_POST['device_storage'])), ENT_QUOTES, 'UTF-8') : '';
$battery      = isset($_POST['device_battery']) ? htmlspecialchars(strip_tags(trim($_POST['device_battery'])), ENT_QUOTES, 'UTF-8') : '';
$est_value    = isset($_POST['estimated_value']) ? htmlspecialchars(strip_tags(trim($_POST['estimated_value'])), ENT_QUOTES, 'UTF-8') : '';
$total_passed = isset($_POST['total_passed']) ? intval($_POST['total_passed']) : 0;
$total_failed = isset($_POST['total_failed']) ? intval($_POST['total_failed']) : 0;
$diag_json    = isset($_POST['diagnostics_json']) ? trim($_POST['diagnostics_json']) : '';

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

if (empty($location) || mb_strlen($location) < 3) {
    $errors['location'] = 'Please enter your Mumbai pickup location/address.';
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

// Generate Reference ID
$ref_id = 'CS-EX-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 7));

// Parse Diagnostics Payload
$diagnostics_data = [];
if (!empty($diag_json)) {
    $decoded = json_decode($diag_json, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        $diagnostics_data = $decoded;
    }
}

// Lead Payload
$lead_entry = [
    'ref_id'           => $ref_id,
    'timestamp'        => date('Y-m-d H:i:s'),
    'ip'               => $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN',
    'customer'         => [
        'name'         => $name,
        'phone'        => $phone,
        'email'        => $email,
        'location'     => $location,
        'pickup_slot'  => $pickup_slot
    ],
    'device'           => [
        'brand'        => $brand,
        'model'        => $model,
        'storage'      => $storage,
        'battery'      => $battery,
        'estimated_val'=> $est_value
    ],
    'diagnostic_stats' => [
        'total_passed' => $total_passed,
        'total_failed' => $total_failed,
        'test_details' => $diagnostics_data
    ]
];

// Append to Logs Directory
$logs_dir = __DIR__ . '/../logs';
if (!is_dir($logs_dir)) {
    @mkdir($logs_dir, 0755, true);
}

$log_file = $logs_dir . '/smart_exchange_leads.jsonl';
$log_line = json_encode($lead_entry, JSON_UNESCAPED_UNICODE) . "\n";
@file_put_contents($log_file, $log_line, FILE_APPEND | LOCK_EX);

$_SESSION['last_smart_exchange_time'] = time();

echo json_encode([
    'status'   => 'success',
    'message'  => 'Free doorstep pickup scheduled successfully! Our executive will contact you shortly.',
    'ref_id'   => $ref_id,
    'details'  => [
        'device'    => "$brand $model ($storage)",
        'est_value' => $est_value,
        'slot'      => $pickup_slot
    ]
]);
exit;
