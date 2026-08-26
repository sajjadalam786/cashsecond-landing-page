<?php
/**
 * CashSecond - Valuation & Doorstep Confirmation Page (Thank You)
 * Keeps valuation blurred and initiates WhatsApp handover.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$config = require_once __DIR__ . '/config/config.php';
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Valuation Sent to WhatsApp | <?= htmlspecialchars($business['name'] ?? 'CashSecond') ?></title>
    <meta name="robots" content="noindex, nofollow">
    
    <!-- Apple Favicon -->
    <link rel="icon" type="image/webp" href="assets/images/CashSecond-Fevicon-icon.webp">
    <link rel="apple-touch-icon" href="assets/images/CashSecond-Fevicon-icon.webp">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="assets/css/style.css?v=28.0">
    
    <style>
        .thankyou-page-wrapper {
            min-height: 100vh;
            background: linear-gradient(180deg, #F5F5F7 0%, #FFFFFF 100%);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            font-family: var(--font-sans, -apple-system, sans-serif);
        }
        
        .thankyou-header {
            padding: 20px 24px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--color-border, #E5E5EA);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .thankyou-header .brand-logo img {
            height: 38px;
            width: auto;
        }
        
        .thankyou-card-container {
            max-width: 620px;
            margin: 40px auto;
            padding: 0 20px;
            width: 100%;
        }
        
        .thankyou-card {
            background: #FFFFFF;
            border: 1px solid var(--color-border, #E5E5EA);
            border-radius: 24px;
            padding: 36px 32px;
            box-shadow: 0 16px 48px rgba(0, 0, 0, 0.06);
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .thankyou-icon-wrap {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: rgba(37, 211, 102, 0.14);
            color: #25D366;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            animation: iconPop 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        @keyframes iconPop {
            0% { transform: scale(0.4); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }
        
        .thankyou-title {
            font-family: var(--font-heading, sans-serif);
            font-size: 1.65rem;
            font-weight: 800;
            color: var(--color-dark, #1C1C1E);
            margin: 0 0 8px 0;
            letter-spacing: -0.02em;
        }
        
        .thankyou-subtitle {
            font-size: 0.95rem;
            color: var(--color-text-secondary, #636366);
            line-height: 1.5;
            margin: 0 0 24px 0;
        }
        
        .thankyou-valuation-deck {
            background: #F5F5F7;
            border: 1px solid #E5E5EA;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 24px;
            text-align: left;
        }
        
        .valuation-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dashed rgba(0, 0, 0, 0.08);
            font-size: 0.875rem;
        }

        .valuation-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        
        .valuation-row .label {
            color: #636366;
            font-weight: 500;
        }

        .valuation-row .value {
            color: #1C1C1E;
            font-weight: 700;
        }

        .valuation-highlight-row {
            margin-top: 10px;
            padding-top: 12px;
            border-top: 1.5px solid #E5E5EA;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* Blurred Valuation Payout */
        .valuation-blurred-wrap {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .valuation-blurred-amount {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--color-cta, #0071E3);
            filter: blur(8px);
            user-select: none;
            -webkit-user-select: none;
            opacity: 0.6;
            letter-spacing: 2px;
        }

        .valuation-lock-pill {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #0071E3;
            color: #FFFFFF;
            font-size: 0.6875rem;
            font-weight: 800;
            padding: 4px 12px;
            border-radius: 20px;
            box-shadow: 0 4px 12px rgba(0, 113, 227, 0.3);
            white-space: nowrap;
        }

        .whatsapp-redirect-box {
            background: #E8F8EE;
            border: 1.5px solid #25D366;
            border-radius: 16px;
            padding: 18px 20px;
            margin-bottom: 24px;
            text-align: center;
        }

        .whatsapp-spinner-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-size: 0.9rem;
            font-weight: 700;
            color: #0E7C3A;
            margin-bottom: 8px;
        }

        .pulse-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: #25D366;
            animation: pulse 1.2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(0.9); opacity: 0.7; }
            50% { transform: scale(1.4); opacity: 1; }
            100% { transform: scale(0.9); opacity: 0.7; }
        }

        .btn-whatsapp-direct {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            padding: 15px 24px;
            background: #25D366;
            color: #FFFFFF;
            font-size: 1rem;
            font-weight: 700;
            border-radius: 14px;
            text-decoration: none;
            box-shadow: 0 4px 16px rgba(37, 211, 102, 0.35);
            transition: all 0.22s ease;
        }

        .btn-whatsapp-direct:hover {
            background: #1EBE5D;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(37, 211, 102, 0.45);
            color: #FFFFFF;
        }

        .thankyou-trust-strip {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            font-size: 0.75rem;
            color: #8E8E93;
            margin-top: 16px;
            flex-wrap: wrap;
        }

        .thankyou-footer {
            padding: 24px;
            text-align: center;
            font-size: 0.75rem;
            color: #8E8E93;
            border-top: 1px solid #E5E5EA;
            background: #FFFFFF;
        }

        @media (max-width: 600px) {
            .thankyou-header {
                justify-content: center;
                padding: 14px 16px;
            }
            .thankyou-header .brand-logo {
                margin: 0 auto;
                display: flex;
                justify-content: center;
            }
            .thankyou-header a[href="index.php"]:not(.brand-logo) {
                display: none;
            }
            .thankyou-card {
                padding: 28px 20px;
            }
            .thankyou-title {
                font-size: 1.4rem;
            }
        }
    </style>
</head>
<body>
    <div class="thankyou-page-wrapper">
        <!-- Top Navigation -->
        <header class="thankyou-header">
            <a href="index.php" class="brand-logo" aria-label="<?= htmlspecialchars($business['name'] ?? 'CashSecond') ?> Home">
                <img src="assets/images/CashSecond-Main-Logo.webp" alt="<?= htmlspecialchars($business['name'] ?? 'CashSecond') ?>" width="170" height="38">
            </a>
            <a href="index.php" style="font-size: 0.875rem; font-weight: 600; color: #0071E3; text-decoration: none;">&larr; Back to Home</a>
        </header>

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
                        <span class="value"><?= htmlspecialchars($ref_id) ?></span>
                    </div>
                    <div class="valuation-row">
                        <span class="label">Selected iPhone</span>
                        <span class="value"><?= htmlspecialchars($device_display) ?></span>
                    </div>
                    <div class="valuation-row">
                        <span class="label">Pickup Location</span>
                        <span class="value">Mumbai (Doorstep Free)</span>
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

                <!-- WhatsApp Auto-Redirect Box -->
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

        <!-- Footer -->
        <footer class="thankyou-footer">
            <p style="margin: 0 0 6px 0;">© <?= date('Y') ?> <?= htmlspecialchars($business['name'] ?? 'CashSecond') ?>. <?= htmlspecialchars($business['address'] ?? 'Arcadia Bldg, NCPA Marg, Nariman Point, Mumbai 400021') ?>.</p>
            <p style="margin: 0; color: #A1A1A6;">Apple, iPhone, iOS, Face ID, and Lightning are trademarks of Apple Inc., registered in the U.S. and other countries.</p>
        </footer>
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
</body>
</html>
