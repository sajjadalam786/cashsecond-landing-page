<?php
/**
 * CashSecond - Google Sheets Direct Connectivity Diagnostic Tool
 * Open this in your browser (e.g. http://localhost/cashsecond-landing-page/test_sheets.php)
 * to test live submission directly to your Google Sheet!
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/GoogleSheetsService.php';

$testLead = [
    'lead_id'   => 'TEST-EXG-' . date('Ymd-His'),
    'customer'  => [
        'name'            => 'Rahul Sharma (Diagnostic Test)',
        'phone'           => '9876543210',
        'email'           => 'rahul.sharma@example.com',
        'address'         => '',
        'pincode'         => '',
        'pickup_date'     => '',
        'pickup_slot'     => '',
        'feedback_rating' => '',
        'feedback_comment'=> ''
    ],
    'device'    => [
        'model'         => 'Apple iPhone 16 Pro Max',
        'variant'       => '512 GB',
        'battery'       => '92%',
        'base_val'      => '₹76,800',
        'estimated_val' => '₹76,800'
    ],
    'answers'   => [
        'power_on'         => 'Yes',
        'display_working'  => 'Yes',
        'touch_screen'     => 'Yes',
        'display_flaws'    => 'No',
        'screen_cracked'   => 'No',
        'screen_scratches' => 'No',
        'body_dents'       => 'Yes',
        'body_bent'        => 'No',
        'body_visible_damage' => 'No',
        'camera_glass_crack'=> 'No',
        'missing_parts'    => 'No',
        'battery_health'   => '90% – 100%',
        'rear_camera'      => 'Yes',
        'front_camera'     => 'Yes',
        'camera_flash'     => 'Yes',
        'loudspeaker'      => 'Yes',
        'earpiece_receiver'=> 'Yes',
        'microphone'       => 'Yes',
        'power_button'     => 'Yes',
        'volume_buttons'   => 'Yes',
        'silent_switch'    => 'Yes',
        'charging_port'    => 'Yes',
        'charges_normally' => 'Yes',
        'biometrics'       => 'Yes',
        'wifi_working'     => 'Yes',
        'bluetooth_working'=> 'Yes',
        'cellular_sim'     => 'Yes',
        'gps_location'     => 'Yes',
        'liquid_damage'    => 'No',
        'display_original' => 'Yes',
        'parts_replaced'   => 'No',
        'warranty_status'  => 'YES (Under 11 Months)',
        'bill_invoice'     => 'Yes',
        'has_box'          => 'Yes',
        'has_cable'        => 'Yes'
    ]
];

$config = require __DIR__ . '/config/google_sheets.php';
$result = GoogleSheetsService::appendValuationRow($testLead);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Google Sheets Live Test | CashSecond</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #0B0D10; color: #F5F5F7; padding: 40px 20px; line-height: 1.5; }
        .container { max-width: 750px; margin: 0 auto; background: #15181E; border: 1px solid rgba(255,255,255,0.1); border-radius: 18px; padding: 30px; }
        h1 { color: #0071E3; font-size: 1.5rem; margin-top: 0; }
        .badge { display: inline-block; padding: 4px 12px; border-radius: 8px; font-weight: 700; font-size: 0.85rem; }
        .success { background: #1B5E20; color: #A5D6A7; }
        .error { background: #B71C1C; color: #FFCDD2; }
        pre { background: #000; padding: 15px; border-radius: 10px; overflow-x: auto; color: #34C759; font-size: 0.85rem; }
        a { color: #0071E3; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📊 CashSecond Google Sheets Live Test</h1>
        <p><strong>Configured Webhook URL:</strong><br><code><?= htmlspecialchars($config['webhook_url']) ?></code></p>
        
        <p>
            <strong>Test Status:</strong> 
            <span class="badge <?= ($result['success'] ?? false) ? 'success' : 'error' ?>">
                <?= ($result['success'] ?? false) ? 'COMPLETED' : 'FAILED' ?>
            </span>
        </p>

        <h3>Server Response:</h3>
        <pre><?= htmlspecialchars(print_r($result, true)) ?></pre>

        <p style="margin-top: 25px;">
            👉 <strong>Check your Google Sheet now:</strong><br>
            <a href="https://docs.google.com/spreadsheets/d/1LpQdgV5PtA2-nVpzjVZPGAmVdaVBzVM8g1hCWBaApCo/edit" target="_blank">
                https://docs.google.com/spreadsheets/d/1LpQdgV5PtA2-nVpzjVZPGAmVdaVBzVM8g1hCWBaApCo/edit &rarr;
            </a>
        </p>

        <p><a href="index.php">&larr; Return to Website</a></p>
    </div>
</body>
</html>
