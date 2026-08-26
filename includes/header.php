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

    <!-- Favicon & Touch Icons -->
    <link rel="icon" type="image/webp" href="assets/images/CashSecond-Fevicon-icon.webp">
    <link rel="apple-touch-icon" href="assets/images/CashSecond-Fevicon-icon.webp">

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
    <link rel="stylesheet" href="assets/css/style.css?v=23.0">
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
                <a href="#valuation-entry" class="btn-header-quote" aria-haspopup="dialog" aria-controls="buybackQuestionnaireModal">
                    <span>Check Value &rarr;</span>
                </a>
            </div>
        </div>
    </header>
