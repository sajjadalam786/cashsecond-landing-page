<?php
/**
 * CashSecond - Premium Header Template & Top Moving Announcement Strip
 * Apple-Inspired Design with Marquee & Sticky Navigation
 */
if (!isset($config)) {
    $config = require __DIR__ . '/../config/config.php';
}

$business = $config['business'] ?? [];
$seo = $config['seo'] ?? [];
$page_title = $seo['meta_title'] ?? 'Sell Your iPhone at a Fair Price in Mumbai | CashSecond';
$page_description = $seo['meta_description'] ?? 'Get an instant estimate for your iPhone in seconds. Simple, secure and hassle-free buyback with free doorstep pickup in Mumbai.';
$canonical_url = $seo['site_url'] ?? 'http://localhost/cashsecond-landing-page/';
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

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?= htmlspecialchars($page_title); ?>">
    <meta property="og:description" content="<?= htmlspecialchars($page_description); ?>">
    <meta property="og:url" content="<?= htmlspecialchars($canonical_url); ?>">
    <meta property="og:type" content="website">
    <meta property="og:image" content="<?= htmlspecialchars(rtrim($canonical_url, '/') . '/assets/images/cashsecond-logo.png'); ?>">

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($page_title); ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($page_description); ?>">
    <meta name="twitter:image" content="<?= htmlspecialchars(rtrim($canonical_url, '/') . '/assets/images/cashsecond-logo.png'); ?>">

    <!-- Preconnect & Luxury Typography Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="assets/css/style.css?v=17.0">
    <link rel="stylesheet" href="assets/css/questionnaire.css?v=1.1">
</head>
<body id="top">
    <!-- Main Brand Header -->
    <header class="site-header" id="site-header">
        <div class="container header-inner">
            <!-- Brand Logo -->
            <a href="#top" class="brand-logo" aria-label="CashSecond Home">
                <img src="assets/images/cashsecond-logo.png" alt="CashSecond - Best Value For Your iPhone" class="brand-logo-img" width="165" height="52" loading="eager">
            </a>

            <!-- Desktop Navigation Menu -->
            <nav class="nav-menu" aria-label="Main Navigation">
                <a href="#valuation-entry" class="nav-link">Sell iPhone</a>
                <a href="#how-it-works" class="nav-link">How It Works</a>
                <a href="#top-search-bar" class="nav-link">iPhone Models</a>
                <a href="#reviews" class="nav-link">Reviews</a>
                <a href="#faq" class="nav-link">FAQs</a>
                <a href="#contact" class="nav-link">Contact</a>
            </nav>

            <!-- Desktop Header Quick Actions -->
            <div class="header-cta-group">
                <a href="tel:+918976332211" class="header-phone-link" aria-label="Call CashSecond Support at +91 897633 2211">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                    </svg>
                    <span>+91 897633 2211</span>
                </a>
                <a href="#valuation-entry" class="btn-header-quote" aria-haspopup="dialog" aria-controls="buybackQuestionnaireModal">
                    <span>Check Value &rarr;</span>
                </a>
            </div>
        </div>
    </header>
