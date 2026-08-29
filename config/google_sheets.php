<?php
/**
 * CashSecond - Google Sheets Integration Configuration
 * Keeps all Google Sheets endpoints and credentials securely on the server.
 */

if (!function_exists('get_env_var')) {
    require_once __DIR__ . '/config.php';
}

return [
    // Google Apps Script Web App Endpoint URL
    'webhook_url' => get_env_var('GOOGLE_SHEETS_WEBHOOK_URL', 'https://script.google.com/macros/s/AKfycbzHqkm8BesKTtw3_0v-s95Kbf7ykMlwJqJ4CZWqiu8MC8lNi71wKRGnS6lA1OeSoMbG/exec'),

    // Secret API Token for webhook authentication (optional extra layer of security)
    'secret_token' => get_env_var('GOOGLE_SHEETS_SECRET_TOKEN', 'CS_GSHEETS_SECURE_TOKEN_2026'),

    // Store Owner Email for Lead Notifications
    'notification_email' => get_env_var('RECIPIENT_EMAIL', 'wholesalehouse2016@gmail.com'),

    // Sheet tab names
    'sheets' => [
        'main'         => 'Phone Valuations',
        'settings'     => 'Valuation Settings',
        'tests_config' => 'Test Configuration'
    ],

    // Network timeout in seconds for cURL requests to Google Sheets
    'timeout' => 8,

    // Enable local JSONL backup queue
    'backup_queue' => true,

    // Log files
    'log_path'     => __DIR__ . '/../logs/google_sheets_payloads.jsonl',
    'queue_path'   => __DIR__ . '/../logs/pending_sheets_queue.jsonl'
];
