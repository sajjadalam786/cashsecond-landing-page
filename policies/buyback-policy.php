<?php
/**
 * CashSecond - Buyback Policy
 * Explicit guidelines on iPhone grading, testing, doorstep pickup, and payout.
 */
$config = require __DIR__ . '/../config/config.php';
$base_path = '../';
$seo = $config['seo'] ?? [];

$page_title       = "iPhone Buyback Policy | CashSecond";
$page_description = "Understand CashSecond's transparent iPhone buyback policy: 32-point inspection, device grading, doorstep pickup, and instant on-spot payment.";
$canonical_url    = rtrim($seo['site_url'] ?? 'http://localhost/cashsecond-landing-page', '/') . "/policies/buyback-policy.php";

require __DIR__ . '/../includes/header.php';
?>

<main class="policy-page">
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

            <h2>3. The 32-Point Doorstep Diagnostic Check</h2>
            <p>Upon arrival, our technician runs a rapid 10-minute diagnostic verifying:</p>
            <ul>
                <li>Touch response, OLED dead pixels, and True Tone operation.</li>
                <li>Camera autofocus, ultra-wide lens, LiDAR sensor, and flash.</li>
                <li>Face ID / Touch ID biometric sensors and ambient light detectors.</li>
                <li>Microphones, stereo speakers, earpiece, and charging port.</li>
                <li>Wi-Fi, Bluetooth, cellular connectivity, and iCloud unlock status.</li>
            </ul>

            <h2>4. Spot Payment Guarantee</h2>
            <p>Immediately upon signing the buyback invoice, the agreed amount is transferred to your bank account via UPI / IMPS or handed over in cash. We charge zero convenience fees for doorstep pickup.</p>
        </div>
    </div>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>
