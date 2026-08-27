<?php
/**
 * CashSecond - Google Sheets Integration Configuration
 * Keeps all Google Sheets endpoints and credentials securely on the server.
 */

return [
    // Google Apps Script Web App Endpoint URL
    // Can be set via environment variable GOOGLE_SHEETS_WEBHOOK_URL or defined here
    'webhook_url' => getenv('GOOGLE_SHEETS_WEBHOOK_URL') ?: 'https://script.google.com/macros/s/AKfycbz92Odybh76PaC2kgCGS39SrebD-f4lALmvltCl6oygSHRmq5eMB0jml8vX10CGWUFT/exec',

    // Secret API Token for webhook authentication (optional extra layer of security)
    'secret_token' => getenv('GOOGLE_SHEETS_SECRET_TOKEN') ?: 'CS_GSHEETS_SECURE_TOKEN_2026',

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
