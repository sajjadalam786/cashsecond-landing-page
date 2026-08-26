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

    <!-- Preconnect Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="assets/css/style.css?v=15.0">
    <link rel="stylesheet" href="assets/css/questionnaire.css?v=1.1">
</head>
<body id="top">
    <!-- Main Brand Header (Large Centered Logo Area) -->
    <header class="site-header" id="site-header">
        <div class="container header-inner">
            <!-- Large Centered Brand Logo -->
            <a href="#top" class="brand-logo" aria-label="CashSecond Home">
                <img src="assets/images/cashsecond-logo.png" alt="CashSecond - Best Value For Your iPhone" class="brand-logo-img" width="420" height="140" loading="eager">
            </a>
        </div>
    </header>
