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
    <meta property="og:image" content="<?= htmlspecialchars(rtrim($canonical_url, '/') . '/assets/images/phones/iphone-16-pro.svg'); ?>">

    <!-- Preconnect Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="assets/css/style.css?v=6.0">
</head>
<body id="top">
    <!-- 1. Top Moving Announcement Strip (Infinite Marquee) -->
    <div class="marquee-wrapper marquee-top-bar" aria-label="Announcements">
        <div class="marquee-track">
            <span class="marquee-item">SELL YOUR iPHONE <span class="marquee-dot">•</span> GET INSTANT VALUE <span class="marquee-dot">•</span> FREE DOORSTEP PICKUP <span class="marquee-dot">•</span> SECURE DATA WIPE <span class="marquee-dot">•</span> FAST PAYMENT <span class="marquee-dot">•</span> BEST VALUE FOR YOUR iPHONE <span class="marquee-dot">•</span></span>
            <span class="marquee-item">SELL YOUR iPHONE <span class="marquee-dot">•</span> GET INSTANT VALUE <span class="marquee-dot">•</span> FREE DOORSTEP PICKUP <span class="marquee-dot">•</span> SECURE DATA WIPE <span class="marquee-dot">•</span> FAST PAYMENT <span class="marquee-dot">•</span> BEST VALUE FOR YOUR iPHONE <span class="marquee-dot">•</span></span>
        </div>
        <div class="marquee-track" aria-hidden="true">
            <span class="marquee-item">SELL YOUR iPHONE <span class="marquee-dot">•</span> GET INSTANT VALUE <span class="marquee-dot">•</span> FREE DOORSTEP PICKUP <span class="marquee-dot">•</span> SECURE DATA WIPE <span class="marquee-dot">•</span> FAST PAYMENT <span class="marquee-dot">•</span> BEST VALUE FOR YOUR iPHONE <span class="marquee-dot">•</span></span>
            <span class="marquee-item">SELL YOUR iPHONE <span class="marquee-dot">•</span> GET INSTANT VALUE <span class="marquee-dot">•</span> FREE DOORSTEP PICKUP <span class="marquee-dot">•</span> SECURE DATA WIPE <span class="marquee-dot">•</span> FAST PAYMENT <span class="marquee-dot">•</span> BEST VALUE FOR YOUR iPHONE <span class="marquee-dot">•</span></span>
        </div>
    </div>

    <!-- 2. Sticky Header -->
    <header class="site-header" id="site-header">
        <div class="container header-inner">
            <!-- Brand Logo -->
            <a href="#top" class="brand-logo" aria-label="CashSecond Home">
                <img src="assets/images/logo.svg" alt="CashSecond Logo" width="30" height="30">
                <span>CashSecond</span>
            </a>

            <!-- Desktop Nav Menu -->
            <nav class="nav-menu" aria-label="Main Navigation">
                <a href="#valuation" class="nav-link">Sell iPhone</a>
                <a href="#how-it-works" class="nav-link">How It Works</a>
                <a href="#models" class="nav-link">iPhone Models</a>
                <a href="#showcase" class="nav-link">Showcase</a>
                <a href="#why-us" class="nav-link">Why Us</a>
                <a href="#faq" class="nav-link">FAQs</a>
            </nav>

            <!-- Mobile Hamburger Button -->
            <button type="button" class="hamburger-btn" id="hamburger-menu-btn" aria-label="Toggle Menu" aria-expanded="false">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>

        <!-- Mobile Navigation Drawer -->
        <div class="mobile-nav-drawer" id="mobile-nav-drawer" aria-label="Mobile Navigation">
            <nav class="mobile-nav-links">
                <a href="#valuation" class="nav-link mobile-drawer-link">Sell iPhone</a>
                <a href="#how-it-works" class="nav-link mobile-drawer-link">How It Works</a>
                <a href="#models" class="nav-link mobile-drawer-link">iPhone Models</a>
                <a href="#showcase" class="nav-link mobile-drawer-link">Interactive Showcase</a>
                <a href="#why-us" class="nav-link mobile-drawer-link">Why Choose Us</a>
                <a href="#inspection" class="nav-link mobile-drawer-link">32-Point Check</a>
                <a href="#faq" class="nav-link mobile-drawer-link">FAQs</a>
                <a href="#contact" class="nav-link mobile-drawer-link">Contact Store</a>
            </nav>
            <a href="#valuation" class="btn btn-primary btn-full mobile-drawer-link">
                <span>Get Instant Price &rarr;</span>
            </a>
        </div>
    </header>
