<?php
/**
 * CashSecond - Terms & Conditions
 * Transparent trade-in, valuation and transaction terms.
 */
$config = require __DIR__ . '/../config/config.php';
$base_path = '../';
$seo = $config['seo'] ?? [];

$page_title       = "Terms & Conditions | CashSecond iPhone Buyback";
$page_description = "Read the terms of service and trade-in guidelines for iPhone buyback, valuation estimates, and doorstep pickup at CashSecond.";
$canonical_url    = rtrim($seo['site_url'] ?? 'http://localhost/cashsecond-landing-page', '/') . "/policies/terms.php";

require __DIR__ . '/../includes/header.php';
?>

<main class="policy-page">
    <div class="container">
        <div class="policy-card">
            <a href="../index.php" class="back-nav">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                <span>Back to CashSecond</span>
            </a>

            <h1>Terms of Service</h1>
            <p class="updated-date">Last Updated: August 2026</p>

            <p>Welcome to <strong>CashSecond</strong>. By using our website, submitting an iPhone valuation enquiry, or selling your Apple device to us, you agree to comply with the following Terms and Conditions.</p>

            <h2>1. Ownership & Legal Eligibility</h2>
            <p>By offering an iPhone for sale, you declare and warrant that:</p>
            <ul>
                <li>You are the sole legal owner of the device or are authorized to sell it on the owner's behalf.</li>
                <li>The device is not reported lost, stolen, or subject to any third-party financial encumbrance or legal lien.</li>
                <li>You will present a valid government-issued photo ID (Aadhaar / PAN / Driving License) and original purchase invoice if requested during verification.</li>
            </ul>

            <h2>2. Nature of Online Valuations</h2>
            <p>The valuation generated through our online calculator is an <strong>estimate</strong> based on the model, storage, and condition parameters you provide. The final buyback price is confirmed following a physical 32-point diagnostic check by our executive.</p>

            <h2>3. Doorstep Pickup & On-Spot Inspection</h2>
            <ul>
                <li>Doorstep pickup is free within our operational service coverage across Mumbai, Navi Mumbai, and Thane.</li>
                <li>If the physical device differs from the answers submitted online (e.g. undisclosed screen replacement, damaged cameras, faulty Face ID), the executive will provide an adjusted quote. You retain 100% right to accept or decline the updated quote without any cancellation penalty.</li>
            </ul>

            <h2>4. Payment Disbursement & Finality</h2>
            <p>Once you accept the physical valuation quote and hand over the device, payment is transferred immediately via Instant UPI, IMPS Bank Transfer, or Cash. Once payment is disbursed and the receipt is signed, the device transfer is final and cannot be cancelled or refunded.</p>

            <h2>5. Limitation of Liability</h2>
            <p>CashSecond is not liable for data loss once a factory reset is performed upon your authorization. Customers are strictly advised to complete iCloud backups before device handover.</p>
        </div>
    </div>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>
