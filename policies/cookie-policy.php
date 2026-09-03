<?php
/**
 * CashSecond - Cookie Policy
 * Transparent cookie, session, and analytics disclosure.
 */
$config = require __DIR__ . '/../config/config.php';
$base_path = '../';
$seo = $config['seo'] ?? [];

$page_title       = "Cookie Policy | CashSecond iPhone Buyback";
$page_description = "Learn how CashSecond uses cookies and session storage for security, CSRF protection, and anonymous website analytics.";
$canonical_url    = rtrim($seo['site_url'] ?? 'https://selliphone.cashsecond.com', '/') . "/policies/cookie-policy.php";

require __DIR__ . '/../includes/header.php';
?>

<main class="policy-page">
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
            <p>You can adjust your browser settings at any time to block or delete cookies. Please note that disabling essential cookies may impact CSRF security verification when submitting our valuation form.</p>
        </div>
    </div>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>
