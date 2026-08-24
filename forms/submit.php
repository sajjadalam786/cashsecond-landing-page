<?php
/**
 * CashSecond - Lead Enquiry Form Processor
 * Handles validation, CSRF verification, honeypot protection, rate limiting, Google Sheets integration, and local backup logging.
 * Follows Google Ads Transparency & Safe Data Handling.
 */

// Set JSON response header
header('Content-Type: application/json; charset=UTF-8');

// Load configuration
$config = require_once __DIR__ . '/../config/config.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Invalid request method. Only POST requests are allowed.'
    ]);
    exit;
}

// 1. Basic Session Rate Limiting (Prevent flood abuse)
$now = time();
if (isset($_SESSION['last_submission_time']) && ($now - $_SESSION['last_submission_time']) < 4) {
    http_response_code(429);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Please wait a moment before submitting another enquiry.'
    ]);
    exit;
}

// 2. CSRF Token Verification
$csrf_token = isset($_POST['csrf_token']) ? trim($_POST['csrf_token']) : '';
if (empty($csrf_token) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrf_token)) {
    http_response_code(403);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Security validation failed (Session expired or invalid CSRF token). Please refresh the page and try again.'
    ]);
    exit;
}

// 3. Honeypot Spam Protection
// If the hidden 'website_hp' field is filled, it is an automated spam bot.
if (!empty($_POST['website_hp'])) {
    // Silently simulate success to bot without saving
    echo json_encode([
        'status'  => 'success',
        'message' => 'Thank you! Your enquiry has been received.'
    ]);
    exit;
}

// 4. Input Extraction & Sanitization
$name    = isset($_POST['full_name']) ? htmlspecialchars(strip_tags(trim($_POST['full_name'])), ENT_QUOTES, 'UTF-8') : '';
$phone   = isset($_POST['phone_number']) ? preg_replace('/[^0-9+]/', '', trim($_POST['phone_number'])) : '';
$model   = isset($_POST['phone_model']) ? htmlspecialchars(strip_tags(trim($_POST['phone_model'])), ENT_QUOTES, 'UTF-8') : '';
$budget  = isset($_POST['budget_range']) ? htmlspecialchars(strip_tags(trim($_POST['budget_range'])), ENT_QUOTES, 'UTF-8') : 'Flexible';
$message = isset($_POST['message']) ? htmlspecialchars(strip_tags(trim($_POST['message'])), ENT_QUOTES, 'UTF-8') : '';
$consent = isset($_POST['consent']) && ($_POST['consent'] === '1' || $_POST['consent'] === 'on');

// 5. Server-Side Validation
$errors = [];

if (empty($name) || mb_strlen($name) < 2) {
    $errors['full_name'] = 'Please enter your valid name (at least 2 characters).';
}

// Validate Phone Number (10 to 14 digits)
$cleanPhoneDigits = preg_replace('/[^0-9]/', '', $phone);
if (empty($phone) || strlen($cleanPhoneDigits) < 10 || strlen($cleanPhoneDigits) > 14) {
    $errors['phone_number'] = 'Please enter a valid 10-digit mobile / WhatsApp number.';
}

if (empty($model)) {
    $errors['phone_model'] = 'Please select or specify the phone model you are interested in.';
}

if (!$consent) {
    $errors['consent'] = 'Please accept consent to receive phone availability details on WhatsApp / Call.';
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

// Set last submission timestamp for rate limiting
$_SESSION['last_submission_time'] = $now;

// Prepare Lead Payload
$timestamp = date('Y-m-d H:i:s');
$client_ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
$city = $config['business']['city'] ?? 'Mumbai';

$lead_data = [
    'timestamp'    => $timestamp,
    'name'         => $name,
    'phone'        => $phone,
    'model'        => $model,
    'budget'       => $budget,
    'message'      => $message,
    'city'         => $city,
    'ip'           => $client_ip,
    'source'       => 'CashSecond Landing Page Lead Form',
];

// 6. Local Backup Logging (Always active for reliability)
if (!empty($config['integrations']['enable_local_lead_log'])) {
    $log_dir = __DIR__ . '/../logs';
    if (!is_dir($log_dir)) {
        @mkdir($log_dir, 0755, true);
        @file_put_contents($log_dir . '/.htaccess', "Deny from all\n");
    }
    
    $csv_file = $log_dir . '/leads.csv';
    $is_new = !file_exists($csv_file);
    $fp = @fopen($csv_file, 'a');
    if ($fp) {
        if ($is_new) {
            fputcsv($fp, ['Timestamp', 'Full Name', 'Phone Number', 'Phone Model', 'Budget', 'Message', 'City', 'IP Address']);
        }
        fputcsv($fp, [$timestamp, $name, $phone, $model, $budget, $message, $city, $client_ip]);
        fclose($fp);
    }
}

// 7. Google Sheets Integration via Google Apps Script Web App (FREE)
$google_sheets_synced = false;
$webhook_url = trim($config['integrations']['google_sheets_webhook_url'] ?? '');

if (!empty($webhook_url) && filter_var($webhook_url, FILTER_VALIDATE_URL)) {
    $post_json = json_encode($lead_data);

    if (function_exists('curl_init')) {
        $ch = curl_init($webhook_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_json);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($post_json)
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 6);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code >= 200 && $http_code < 400) {
            $google_sheets_synced = true;
        }
    } else {
        // Fallback using stream_context
        $options = [
            'http' => [
                'header'  => "Content-type: application/json\r\n",
                'method'  => 'POST',
                'content' => $post_json,
                'timeout' => 6
            ]
        ];
        $context  = stream_context_create($options);
        $result = @file_get_contents($webhook_url, false, $context);
        if ($result !== false) {
            $google_sheets_synced = true;
        }
    }
}

// Rotate CSRF token after submission
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// 8. Instant WhatsApp Quick Connect Link
$wa_phone = $config['business']['whatsapp_number'];
$wa_text  = urlencode("Hi CashSecond, I just submitted an enquiry for {$model}. My name is {$name}. Please share live photos, condition & price.");
$whatsapp_direct_url = "https://wa.me/{$wa_phone}?text={$wa_text}";

// Return Clean JSON Success
echo json_encode([
    'status'               => 'success',
    'message'              => 'Thank you, ' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '! Your enquiry has been received. Our team will contact you shortly with availability and pricing.',
    'new_csrf_token'       => $_SESSION['csrf_token'],
    'whatsapp_direct_url'  => $whatsapp_direct_url,
    'lead'                 => [
        'name'   => $name,
        'phone'  => $phone,
        'model'  => $model,
        'budget' => $budget
    ]
]);
exit;
