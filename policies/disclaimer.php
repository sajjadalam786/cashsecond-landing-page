<?php
/**
 * CashSecond - Trademark & General Legal Disclaimer
 */
$config = require __DIR__ . '/../config/config.php';
$business = $config['business'] ?? [];
$seo = $config['seo'] ?? [];

$page_title       = "Legal Disclaimer | CashSecond iPhone Buyback";
$page_description = "Legal disclaimer regarding trademark ownership, non-affiliation with Apple Inc., and independent pre-owned valuation operations at CashSecond.";
$canonical_url    = rtrim($seo['site_url'], '/') . "/policies/disclaimer.php";
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

                <h1>Legal Disclaimer</h1>
                <p class="updated-date">Last Updated: August 2026</p>

                <h2>1. Trademark Non-Affiliation</h2>
                <p>Apple, iPhone, iOS, Retina, Face ID, TrueDepth, Dynamic Island, and Apple Intelligence are registered trademarks of Apple Inc., registered in the U.S. and other countries.</p>
                <p><strong>CashSecond</strong> is an independent pre-owned electronics valuation, resale, and buyback platform operating from Mumbai, India. CashSecond is not endorsed by, directly affiliated with, maintained, authorized, or sponsored by Apple Inc. The use of Apple trademarks on this website is strictly for identification, technical specification reference, and trade-in compatibility.</p>

                <h2>2. Valuation Estimates & Market Fluctuations</h2>
                <p>Values provided by our online tool reflect estimated secondary market rates based on standard condition assumptions. Pre-owned electronics markets fluctuate based on supply, device age, battery degradation, cosmetic damage, and regional availability. Final pricing is confirmed only upon physical inspection.</p>

                <h2>3. Verification & Anti-Theft Policy</h2>
                <p>CashSecond maintains a strict zero-tolerance policy against handling stolen, blacklisted, or illegally obtained devices. All sellers are required to provide photo identification and undergo IMEI blacklist checks before any payment is disbursed.</p>
            </div>
        </div>
    </div>
</body>
</html>
