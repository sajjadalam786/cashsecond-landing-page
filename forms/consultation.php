<?php
/**
 * CashSecond - Free Consultation Form Processor
 * Handles validation, CSRF verification, rate limiting, and logging.
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

// 1. Session Rate Limiting
$now = time();
if (isset($_SESSION['last_consultation_time']) && ($now - $_SESSION['last_consultation_time']) < 3) {
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
        'message' => 'Security token expired. Please reload the page and try again.'
    ]);
    exit;
}

// 3. Honeypot Spam Protection
if (!empty($_POST['website_hp'])) {
    echo json_encode([
        'status'  => 'success',
        'message' => 'Thank you! Your consultation request has been submitted.'
    ]);
    exit;
}

date_default_timezone_set('Asia/Kolkata');

$name    = isset($_POST['full_name']) ? htmlspecialchars(strip_tags(trim($_POST['full_name'])), ENT_QUOTES, 'UTF-8') : '';
$phone   = isset($_POST['phone_number']) ? preg_replace('/[^0-9+]/', '', trim($_POST['phone_number'])) : '';
$email   = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
$problem = isset($_POST['problem']) ? htmlspecialchars(strip_tags(trim($_POST['problem'])), ENT_QUOTES, 'UTF-8') : '';

$errors = [];

if (empty($name) || mb_strlen($name) < 2) {
    $errors['full_name'] = 'Please enter your full name (at least 2 characters).';
}

$cleanPhoneDigits = preg_replace('/[^0-9]/', '', $phone);
if (empty($phone) || strlen($cleanPhoneDigits) < 10 || strlen($cleanPhoneDigits) > 14) {
    $errors['phone_number'] = 'Please enter a valid 10-digit Indian phone / WhatsApp number.';
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Please enter a valid email address.';
}

if (empty($problem) || mb_strlen($problem) < 3) {
    $errors['problem'] = 'Please describe your problem or requirements.';
}

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode([
        'status'  => 'error',
        'message' => reset($errors),
        'errors'  => $errors
    ]);
    exit;
}

$_SESSION['last_consultation_time'] = $now;

// Save to consultation leads log
$logDir = __DIR__ . '/../logs';
if (!is_dir($logDir)) {
    @mkdir($logDir, 0755, true);
}

$leadData = [
    'timestamp'    => date('Y-m-d H:i:s'),
    'type'         => 'Free Consultation',
    'full_name'    => $name,
    'phone_number' => $phone,
    'email'        => $email,
    'problem'      => $problem,
    'ip'           => $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN'
];

@file_put_contents($logDir . '/consultations.jsonl', json_encode($leadData, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND | LOCK_EX);

echo json_encode([
    'status'  => 'success',
    'message' => 'Thank you, ' . $name . '! Your consultation request has been received. Our team will contact you on WhatsApp / Phone shortly.'
]);
exit;
