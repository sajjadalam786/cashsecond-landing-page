<?php
/**
 * CashSecond - Comprehensive Email Diagnostic Tool
 * Open: http://localhost/cashsecond-landing-page/email_diagnostics.php
 */
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/GoogleSheetsService.php';

$recipient = get_env_var('RECIPIENT_EMAIL', 'wholesalehouse2016@gmail.com');
$sender    = get_env_var('SENDER_EMAIL', 'no-reply@cashsecond.in');
$webhook   = get_env_var('GOOGLE_SHEETS_WEBHOOK_URL', '');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CashSecond — Email System Diagnostics</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f5f5f7; color: #1d1d1f; padding: 30px; }
        .card { background: #fff; border-radius: 12px; padding: 24px; max-width: 800px; margin: 0 auto 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
        h1 { font-size: 24px; margin-top: 0; }
        h2 { font-size: 18px; border-bottom: 1px solid #e5e5ea; padding-bottom: 8px; margin-top: 20px; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 6px; font-weight: bold; font-size: 12px; }
        .badge-success { background: #e6f4ea; color: #137333; }
        .badge-fail { background: #fce8e6; color: #c5221f; }
        .badge-warn { background: #fef7e0; color: #b06000; }
        pre { background: #f0f0f2; padding: 12px; border-radius: 8px; overflow-x: auto; font-size: 13px; }
        .btn { display: inline-block; background: #0071e3; color: #fff; padding: 10px 18px; border-radius: 8px; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>

<div class="card">
    <h1>🔍 CashSecond Email & Delivery Diagnostics</h1>
    <p>Detailed breakdown of your email delivery channels:</p>

    <h2>1. Environment Configuration</h2>
    <table>
        <tr><td><strong>Recipient Email:</strong></td><td><code><?= htmlspecialchars($recipient) ?></code></td></tr>
        <tr><td><strong>Sender Email:</strong></td><td><code><?= htmlspecialchars($sender) ?></code></td></tr>
        <tr><td><strong>Google Apps Script Webhook:</strong></td><td><code><?= htmlspecialchars(substr($webhook, 0, 60)) ?>...</code></td></tr>
    </table>

    <h2>2. Channel 1: Local PHP <code>mail()</code> Function</h2>
    <?php
    $phpMailAvailable = function_exists('mail');
    $sendmailPath = ini_get('sendmail_path');
    ?>
    <p>
        Function <code>mail()</code> available: 
        <span class="badge <?= $phpMailAvailable ? 'badge-success' : 'badge-fail' ?>">
            <?= $phpMailAvailable ? 'YES' : 'NO' ?>
        </span>
    </p>
    <p><code>sendmail_path</code> setting: <code><?= htmlspecialchars($sendmailPath ?: 'Not configured (Local XAMPP default)') ?></code></p>
    
    <div style="background:#fff3cd; color:#856404; padding:14px; border-radius:8px; font-size:13.5px; line-height:1.5;">
        <strong>⚠️ Why local PHP mail() doesn't land in Gmail:</strong><br>
        On local XAMPP / macOS, PHP calls the local system sendmail. Because your laptop is on a residential IP without SPF, DKIM, or reverse DNS records, <strong>Gmail, Yahoo, and Outlook reject or drop these messages instantly</strong> to protect against spam.
        <br><br>
        <strong>When it will work:</strong> Once you upload this project to your live hosting server (cPanel, Hostinger, VPS) with your actual domain email (<code>no-reply@cashsecond.in</code>), native PHP <code>mail()</code> will work.
    </div>

    <h2>3. Channel 2: Google Apps Script Cloud Mailer (Recommended for Local & Live)</h2>
    <div style="background:#e8f4fd; color:#0c5460; padding:14px; border-radius:8px; font-size:13.5px; line-height:1.5;">
        <strong>✅ Why Google Apps Script is the best solution:</strong><br>
        Google Apps Script runs directly inside <strong>Google Cloud Servers</strong> with your Gmail account (<code>wholesalehouse2016@gmail.com</code>). It has 100% inbox delivery rate and requires zero SMTP passwords.
    </div>

    <h2>4. Test Live Webhook + Cloud Email Right Now</h2>
    <form method="POST" action="">
        <input type="hidden" name="action" value="trigger_test">
        <button type="submit" class="btn">🚀 Send Live Test Lead to Google Sheet & Email</button>
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'trigger_test') {
        echo "<h3>Diagnostic Test Result:</h3>";
        
        $diagLead = [
            'lead_id'   => 'DIAG-' . date('Ymd-His'),
            'customer'  => [
                'name'    => 'Diagnostic Test User',
                'phone'   => '8976332211',
                'email'   => $recipient
            ],
            'device'    => [
                'model'         => 'Apple iPhone 16 Pro',
                'variant'       => '128 GB',
                'base_val'      => '81000',
                'estimated_val' => '76800'
            ],
            'answers'   => [
                'months_6_11'         => true,
                'battery_greater_80'  => true,
                'box'                 => true,
                'charger'             => true,
                'invoice'             => true
            ]
        ];

        $res = GoogleSheetsService::appendValuationRow($diagLead);
        echo "<pre>" . htmlspecialchars(json_encode($res, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) . "</pre>";
        
        if (!empty($res['success'])) {
            echo "<p><span class='badge badge-success'>SUCCESS</span> Lead sent to Google Apps Script. Check your Google Sheet and inbox!</p>";
        } else {
            echo "<p><span class='badge badge-fail'>FAILED</span> Error: " . htmlspecialchars($res['error'] ?? 'Unknown') . "</p>";
        }
    }
    ?>

    <h2>5. Recent Log Entries</h2>
    <p><strong>Email Notifications Log (<code>logs/email_notifications.jsonl</code>):</strong></p>
    <pre><?php
    $emailLog = __DIR__ . '/logs/email_notifications.jsonl';
    if (file_exists($emailLog)) {
        $lines = array_slice(file($emailLog), -3);
        echo htmlspecialchars(implode("", $lines));
    } else {
        echo "No log entries yet.";
    }
    ?></pre>
</div>

</body>
</html>
