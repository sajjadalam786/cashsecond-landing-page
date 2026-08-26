<?php
/**
 * CashSecond - Terms & Conditions
 * Transparent trade-in, valuation and transaction terms.
 */
$config = require __DIR__ . '/../config/config.php';
$business = $config['business'] ?? [];
$seo = $config['seo'] ?? [];

$page_title       = "Terms & Conditions | CashSecond iPhone Buyback";
$page_description = "Read the terms of service and trade-in guidelines for iPhone buyback, valuation estimates, and doorstep pickup at CashSecond.";
$canonical_url    = rtrim($seo['site_url'], '/') . "/policies/terms.php";
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
    <link rel="icon" type="image/webp" href="../assets/images/CashSecond-Fevicon-icon.webp">
    <link rel="apple-touch-icon" href="../assets/images/CashSecond-Fevicon-icon.webp">
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

                <h2>3. Inspection & Final Offer Confirmation</h2>
                <p>During doorstep verification, our technician will test the display, TrueTone, battery health, cameras, Face ID, earpiece, and IMEI status. If the physical or functional condition differs from the initial details submitted, a revised fair offer will be presented. You are under no obligation to sell and may decline the revised offer with zero cancellation fee.</p>

                <h2>4. Payment Terms</h2>
                <p>Once you accept the final confirmed offer, payment is disbursed instantly via UPI (Google Pay, PhonePe, Paytm), instant Bank Transfer (IMPS), or Cash before the executive departs with the device.</p>

                <h2>5. iCloud Sign-Out & Handover</h2>
                <p>The seller must sign out of Apple ID / iCloud and complete a full factory reset before payment is completed. CashSecond cannot purchase devices with active Activation Lock or remote management locks (MDM).</p>

                <h2>6. Jurisdiction</h2>
                <p>These terms are governed by the laws of India. Any disputes arising shall be subject to the exclusive jurisdiction of the courts in Mumbai, Maharashtra.</p>
            </div>
        </div>
    </div>
</body>
</html>
