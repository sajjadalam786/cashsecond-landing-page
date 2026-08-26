<?php
/**
 * CashSecond - Valuation & Doorstep Confirmation Page (Thank You)
 * Keeps valuation blurred and initiates WhatsApp handover with 5s countdown.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$config = require_once __DIR__ . '/config/config.php';
$base_path = '';
$business = $config['business'] ?? [];

$model    = isset($_GET['model']) ? htmlspecialchars(strip_tags(trim($_GET['model'])), ENT_QUOTES, 'UTF-8') : 'Apple iPhone';
$variant  = isset($_GET['variant']) ? htmlspecialchars(strip_tags(trim($_GET['variant'])), ENT_QUOTES, 'UTF-8') : '';
$val      = isset($_GET['val']) ? (int)preg_replace('/[^0-9]/', '', $_GET['val']) : 0;
$name     = isset($_GET['name']) ? htmlspecialchars(strip_tags(trim($_GET['name'])), ENT_QUOTES, 'UTF-8') : 'Valued Customer';
$ref_id   = isset($_GET['ref']) ? htmlspecialchars(strip_tags(trim($_GET['ref'])), ENT_QUOTES, 'UTF-8') : 'CS-' . strtoupper(substr(md5(uniqid()), 0, 6));

$device_display = trim($model . ($variant ? " ($variant)" : ''));

// WhatsApp Message Format as requested by User
$wa_phone   = preg_replace('/[^0-9]/', '', $business['whatsapp'] ?? '918976332211');
if (strlen($wa_phone) === 10) {
    $wa_phone = '91' . $wa_phone;
}

$wa_message = "i have completed the form Please share my iphone Value - " . $device_display;
$wa_url     = "https://wa.me/" . $wa_phone . "?text=" . rawurlencode($wa_message);

$noindex          = true;
$page_title       = "Valuation Request Sent | " . ($business['name'] ?? 'CashSecond');
$page_description = "Thank you for choosing CashSecond. Your iPhone valuation has been sent directly to your WhatsApp.";

require __DIR__ . '/includes/header.php';
?>

<div class="thankyou-page-wrapper">
    <!-- Main Confirmation Card -->
    <main class="thankyou-card-container">
        <div class="thankyou-card">
            <!-- WhatsApp / Checkmark Icon -->
            <div class="thankyou-icon-wrap" aria-hidden="true">
                <svg width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>

            <h1 class="thankyou-title">Valuation Request Sent!</h1>
            <p class="thankyou-subtitle">Thank you <?= htmlspecialchars($name) ?>. We've sent your official iPhone valuation and doorstep pickup slot directly to your WhatsApp.</p>

            <!-- Valuation Breakdown Deck (With Blurred Locked Value) -->
            <div class="thankyou-valuation-deck">
                <div class="valuation-row">
                    <span class="label">Reference ID</span>
                    <span class="value" style="font-family: monospace; letter-spacing: 0.5px; color: #0071E3;"><?= htmlspecialchars($ref_id) ?></span>
                </div>
                <div class="valuation-row">
                    <span class="label">Device Model</span>
                    <span class="value"><?= htmlspecialchars($device_display) ?></span>
                </div>
                <div class="valuation-row">
                    <span class="label">Customer Name</span>
                    <span class="value"><?= htmlspecialchars($name) ?></span>
                </div>
                <div class="valuation-row">
                    <span class="label">Doorstep Service Area</span>
                    <span class="value">Mumbai &amp; MMR</span>
                </div>
                <div class="valuation-highlight-row">
                    <div>
                        <span class="label" style="display: block; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700; color: #0071E3;">Estimated Resale Value</span>
                        <span style="font-size: 0.75rem; color: #8E8E93;">Sent directly to your WhatsApp chat</span>
                    </div>
                    <div class="valuation-blurred-wrap">
                        <div class="valuation-blurred-amount">₹ <?= $val > 0 ? number_format($val) : '48,500' ?></div>
                        <div class="valuation-lock-pill">🔒 Sent to WhatsApp</div>
                    </div>
                </div>
            </div>

            <!-- WhatsApp Auto-Redirect Box with 5-Second Countdown -->
            <div class="whatsapp-redirect-box">
                <div class="whatsapp-spinner-row">
                    <span class="pulse-dot"></span>
                    <span id="waStatusText">Connecting with CashSecond Executive (<span id="waCountdownSec">5</span>s)...</span>
                </div>
                <p style="font-size: 0.8125rem; color: #4A4A4E; margin: 0 0 14px 0;">
                    Opening WhatsApp with your pre-written quote request message:
                </p>
                <a href="<?= htmlspecialchars($wa_url) ?>" class="btn-whatsapp-direct" id="manualWhatsAppBtn" target="_blank" rel="noopener noreferrer">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                    </svg>
                    <span>Open WhatsApp &amp; View Value Now</span>
                </a>
            </div>

            <div class="thankyou-trust-strip">
                <span>⚡ Instant Spot UPI / Cash</span>
                <span>•</span>
                <span>🛡️ DoD-Grade Data Wipe</span>
                <span>•</span>
                <span>🏠 Free Doorstep Pickup</span>
            </div>
        </div>
    </main>
</div>

<!-- Automatic 5-Second WhatsApp Transition Script -->
<script>
    (function () {
        var waTarget = <?= json_encode($wa_url) ?>;
        var countdownSec = 5;
        var countdownEl = document.getElementById('waCountdownSec');
        var statusEl = document.getElementById('waStatusText');

        if (waTarget) {
            var timer = setInterval(function () {
                countdownSec--;
                if (countdownEl) {
                    countdownEl.textContent = countdownSec;
                }
                if (countdownSec <= 0) {
                    clearInterval(timer);
                    if (statusEl) {
                        statusEl.textContent = 'Redirecting to WhatsApp...';
                    }
                    window.location.href = waTarget;
                }
            }, 1000);
        }
    })();
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
