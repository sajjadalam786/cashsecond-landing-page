<?php
/**
 * CashSecond - Cookie Policy
 * Transparent cookie, session, and analytics disclosure.
 */
$config = require __DIR__ . '/../config/config.php';
$business = $config['business'] ?? [];
$seo = $config['seo'] ?? [];

$page_title       = "Cookie Policy | CashSecond iPhone Buyback";
$page_description = "Learn how CashSecond uses cookies and session storage for security, CSRF protection, and anonymous website analytics.";
$canonical_url    = rtrim($seo['site_url'], '/') . "/policies/cookie-policy.php";
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

                <h1>Cookie Policy</h1>
                <p class="updated-date">Last Updated: August 2026</p>

                <p>This Cookie Policy explains how <strong>CashSecond</strong> uses cookies and similar tracking technologies when you visit our website.</p>

                <h2>1. What Are Cookies?</h2>
                <p>Cookies are small text files placed on your device by websites you visit. They are widely used to ensure websites function properly, secure form submissions against spam, and provide anonymized performance metrics.</p>

                <h2>2. Cookies We Use</h2>
                <ul>
                    <li><strong>Essential Session Cookies:</strong> We use PHP session cookies and CSRF security tokens to verify that form submissions originate from genuine users and prevent automated spam submissions.</li>
                    <li><strong>Analytics & Performance Cookies:</strong> If enabled, anonymous analytics tools (such as Google Analytics 4) help us understand website traffic patterns, popular iPhone models searched, and page load performance without identifying individual visitors.</li>
                </ul>

                <h2>3. Managing Cookie Preferences</h2>
                <p>You can choose to accept or decline cookies through your browser settings. Disabling essential session cookies may affect your ability to submit valuation enquiries securely through our online forms.</p>
            </div>
        </div>
    </div>
</body>
</html>
