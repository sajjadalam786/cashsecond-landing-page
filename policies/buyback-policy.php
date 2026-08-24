<?php
/**
 * CashSecond - Buyback Policy
 * Explicit guidelines on iPhone grading, testing, doorstep pickup, and payout.
 */
$config = require __DIR__ . '/../config/config.php';
$business = $config['business'] ?? [];
$seo = $config['seo'] ?? [];

$page_title       = "iPhone Buyback Policy | CashSecond";
$page_description = "Understand CashSecond's transparent iPhone buyback policy: 32-point inspection, device grading, doorstep pickup, and instant on-spot payment.";
$canonical_url    = rtrim($seo['site_url'], '/') . "/policies/buyback-policy.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title><?= htmlspecialchars($page_title); ?></title>
    <meta name="description" content="<?= htmlspecialchars($page_description); ?>">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?= htmlspecialchars($canonical_url); ?>">
    <link rel="stylesheet" href="../assets/css/style.css?v=5.0">
    <style>
      .policy-page { padding: 48px 0 80px; }
      .policy-card { background: #ffffff; border: 1px solid var(--color-border); border-radius: var(--radius-lg); padding: 36px 28px; max-width: 820px; margin: 0 auto; box-shadow: var(--shadow-card); }
      .policy-card h1 { font-family: var(--font-heading); font-size: 1.875rem; color: var(--color-dark); margin-bottom: 6px; }
      .policy-card .updated-date { color: var(--color-text-muted); font-size: 0.8125rem; margin-bottom: 24px; }
      .policy-card h2 { font-size: 1.1875rem; font-weight: 700; margin: 26px 0 10px; color: var(--color-dark); }
      .policy-card p, .policy-card ul { color: var(--color-text-secondary); font-size: 0.875rem; line-height: 1.65; margin-bottom: 14px; }
      .policy-card ul { padding-left: 20px; list-style: disc; }
      .back-nav { margin-bottom: 20px; display: inline-flex; align-items: center; gap: 6px; font-weight: 600; font-size: 0.875rem; color: var(--color-cta); }
    </style>
</head>
<body>
    <div class="policy-page">
        <div class="container">
            <div class="policy-card">
                <a href="../index.php" class="back-nav">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                    <span>Back to CashSecond</span>
                </a>

                <h1>iPhone Buyback Policy</h1>
                <p class="updated-date">Last Updated: August 2026</p>

                <p>At <strong>CashSecond</strong>, our goal is to deliver an honest, transparent and friction-free experience when selling your used Apple iPhone. This Buyback Policy explains how devices are evaluated, inspected, and purchased.</p>

                <h2>1. Supported Devices</h2>
                <p>CashSecond currently purchases Apple iPhone models starting from iPhone 8 up to the latest iPhone 16 and 17 series. We evaluate standard, Plus, Pro, Pro Max, Mini, and SE variants.</p>

                <h2>2. Device Grading Criteria</h2>
                <p>We classify iPhones based on cosmetic and functional health:</p>
                <ul>
                    <li><strong>Flawless (Grade A+):</strong> Screen and body are pristine with zero scratches, dents, or defects. All components 100% original.</li>
                    <li><strong>Good (Grade A):</strong> Normal minor cosmetic wear. Screen is intact without cracks or deep gouges. Fully functional.</li>
                    <li><strong>Average (Grade B):</strong> Visible scratches, minor housing scuffs, or battery health under 80%. Device operates normally.</li>
                    <li><strong>Below Average / Flawed:</strong> Cracked front glass, broken back panel, Face ID fault, or camera optical issues. Valued according to salvage / repair deductions.</li>
                </ul>

                <h2>3. The 32-Point Diagnostic Inspection</h2>
                <p>Upon doorstep pickup, our certified technician conducts a 5-minute diagnostic test verifying screen touch sensitivity, TrueTone presence, battery maximum capacity, optical image stabilization, wireless charging, earpiece clarity, Face ID, and IMEI blacklist status.</p>

                <h2>4. Doorstep Pickup & Coverage</h2>
                <p>We provide free executive doorstep pickup across Mumbai (South Mumbai, Western Suburbs, Central Suburbs, Harbour Line), Navi Mumbai, and Thane. There are no hidden pickup charges or convenience fees.</p>

                <h2>5. Instant On-Spot Payment</h2>
                <p>Payment is disbursed immediately upon mutual agreement on the inspection findings via UPI (Google Pay, PhonePe, Paytm), IMPS Bank Transfer, or Cash.</p>

                <h2>6. Zero Pressure / Free Cancellation</h2>
                <p>If you decide not to proceed with the sale after the on-spot inspection, you are free to decline the offer. There is no cancellation fee or obligation.</p>
            </div>
        </div>
    </div>
</body>
</html>
