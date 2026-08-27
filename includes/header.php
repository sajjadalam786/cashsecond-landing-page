<?php
/**
 * CashSecond - Premium Header Template & Top Moving Announcement Strip
 * Apple-Inspired Design with Marquee & Sticky Navigation
 */
if (!isset($config)) {
    $config = require __DIR__ . '/../config/config.php';
}

if (!isset($base_path)) {
    $base_path = '';
}

$business = $config['business'] ?? [];
$seo = $config['seo'] ?? [];
$page_title = $page_title ?? ($seo['meta_title'] ?? 'Sell Your iPhone at a Fair Price in Mumbai | CashSecond');
$page_description = $page_description ?? ($seo['meta_description'] ?? 'Get an instant estimate for your iPhone in seconds. Simple, secure and hassle-free buyback with free doorstep pickup in Mumbai.');
$canonical_url = $canonical_url ?? ($seo['site_url'] ?? 'http://localhost/cashsecond-landing-page/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title><?= htmlspecialchars($page_title); ?></title>
    <meta name="description" content="<?= htmlspecialchars($page_description); ?>">
    <meta name="robots" content="<?= isset($noindex) && $noindex ? 'noindex, nofollow' : 'index, follow' ?>">
    <link rel="canonical" href="<?= htmlspecialchars($canonical_url); ?>">

    <!-- Favicon & Touch Icons -->
    <link rel="icon" type="image/webp" href="<?= $base_path ?>assets/images/CashSecond-Fevicon-icon.webp">
    <link rel="apple-touch-icon" href="<?= $base_path ?>assets/images/CashSecond-Fevicon-icon.webp">

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
    <link rel="stylesheet" href="<?= $base_path ?>assets/css/style.css?v=46.0">
    <link rel="stylesheet" href="<?= $base_path ?>assets/css/questionnaire.css?v=6.0">
</head>
<body id="top">
    <!-- Main Brand Header -->
    <header class="site-header" id="site-header">
        <div class="container header-inner">
            <!-- Brand Logo -->
            <a href="<?= $base_path ?>index.php#top" class="brand-logo" aria-label="CashSecond Home">
                <img src="<?= $base_path ?>assets/images/cashsecond-logo.png" alt="CashSecond - Best Value For Your iPhone" class="brand-logo-img" width="165" height="52" loading="eager">
            </a>

            <!-- Desktop Navigation Menu -->
            <nav class="nav-menu" aria-label="Main Navigation">
                <a href="<?= $base_path ?>index.php#buyback-advantages" class="nav-link">Why Us</a>
                <a href="<?= $base_path ?>index.php#how-it-works" class="nav-link">How It Works</a>
                <a href="<?= $base_path ?>index.php#iphone-models" class="nav-link">iPhone Models</a>
                <a href="<?= $base_path ?>index.php#faq" class="nav-link">FAQs</a>
                <a href="<?= $base_path ?>index.php#contact" class="nav-link">Contact</a>
            </nav>

            <!-- Desktop Header Quick Actions -->
            <div class="header-cta-group">
                <a href="#valuation" class="btn-header-quote start-exact-valuation-btn" data-model="Apple iPhone 16 Pro" data-variant="128 GB" aria-haspopup="dialog" aria-controls="buybackQuestionnaireModal">
                    <svg class="btn-click-icon" width="16" height="19" viewBox="0 0 24 28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <line x1="12" y1="1.5" x2="12" y2="4.5"/>
                        <line x1="6.5" y1="3.5" x2="8.6" y2="5.6"/>
                        <line x1="17.5" y1="3.5" x2="15.4" y2="5.6"/>
                        <line x1="4" y1="9" x2="7" y2="9"/>
                        <line x1="20" y1="9" x2="17" y2="9"/>
                        <path d="M10.5 13V8a1.5 1.5 0 0 1 3 0v5"/>
                        <path d="M13.5 12a1.4 1.4 0 0 1 2.8 0v2.5"/>
                        <path d="M16.3 13.5a1.4 1.4 0 0 1 2.8 0v2"/>
                        <path d="M19.1 15a1.4 1.4 0 0 1 2.8 0v3.5a6.5 6.5 0 0 1-6.5 6.5h-3a5.5 5.5 0 0 1-4.2-2L5.8 19.2a1.5 1.5 0 0 1 2.2-2.1l2.5 1.9V13"/>
                    </svg>
                    <span>Check Your iPhone Value</span>
                    <img src="<?= $base_path ?>assets/images/iphone-value-check-button.png" alt="iPhone" class="btn-iphone-thumb" width="18" height="32" loading="eager">
                </a>
            </div>
        </div>
    </header>
