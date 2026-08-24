<?php
/**
 * CashSecond - Header Template (Apple-Inspired Minimalist Design)
 * SEO meta tags, OpenGraph, Favicon, Tracking & Responsive Navigation
 */

if (!isset($config) || !is_array($config)) {
    $config = require __DIR__ . '/../config/config.php';
}

$business = $config['business'] ?? [];
$seo = $config['seo'] ?? [];
$tracking = $config['tracking'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- Primary Meta Tags -->
    <title><?= htmlspecialchars($seo['meta_title'] ?? 'Sell Your iPhone at a Fair Price | CashSecond'); ?></title>
    <meta name="title" content="<?= htmlspecialchars($seo['meta_title'] ?? 'Sell Your iPhone at a Fair Price | CashSecond'); ?>">
    <meta name="description" content="<?= htmlspecialchars($seo['meta_description'] ?? 'Get a transparent valuation for your used Apple iPhone with a simple, secure and hassle-free selling process in Mumbai.'); ?>">
    <meta name="keywords" content="Sell Used iPhone, Sell Old iPhone, Sell iPhone Online, iPhone Buyback, Used iPhone Buyers, iPhone Resale, Sell iPhone for Cash, Mumbai iPhone buyback">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#0B0D10">
    <link rel="canonical" href="<?= htmlspecialchars($seo['site_url'] ?? 'http://localhost/'); ?>/">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= htmlspecialchars($seo['site_url'] ?? 'http://localhost/'); ?>/">
    <meta property="og:title" content="<?= htmlspecialchars($seo['meta_title'] ?? 'Sell Your iPhone at a Fair Price | CashSecond'); ?>">
    <meta property="og:description" content="<?= htmlspecialchars($seo['meta_description'] ?? 'Get a transparent valuation for your used Apple iPhone with a simple, secure and hassle-free selling process in Mumbai.'); ?>">
    <meta property="og:image" content="<?= htmlspecialchars(($seo['site_url'] ?? '') . ($seo['og_image'] ?? '/assets/images/logo.svg')); ?>">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?= htmlspecialchars($seo['site_url'] ?? 'http://localhost/'); ?>/">
    <meta property="twitter:title" content="<?= htmlspecialchars($seo['meta_title'] ?? 'Sell Your iPhone at a Fair Price | CashSecond'); ?>">
    <meta property="twitter:description" content="<?= htmlspecialchars($seo['meta_description'] ?? 'Get a transparent valuation for your used Apple iPhone with a simple, secure and hassle-free selling process in Mumbai.'); ?>">
    <meta property="twitter:image" content="<?= htmlspecialchars(($seo['site_url'] ?? '') . ($seo['og_image'] ?? '/assets/images/logo.svg')); ?>">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="assets/images/logo.svg">

    <!-- Stylesheet -->
    <link rel="stylesheet" href="assets/css/style.css?v=5.0">

    <?php if (!empty($tracking['ga4_measurement_id'])): ?>
    <!-- Google tag (gtag.js) GA4 -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= htmlspecialchars($tracking['ga4_measurement_id']); ?>"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', '<?= htmlspecialchars($tracking['ga4_measurement_id']); ?>');
    </script>
    <?php endif; ?>

    <?php if (!empty($tracking['google_ads_id'])): ?>
    <!-- Google Ads Tag -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= htmlspecialchars($tracking['google_ads_id']); ?>"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', '<?= htmlspecialchars($tracking['google_ads_id']); ?>');
    </script>
    <?php endif; ?>
</head>
<body id="top">
    <!-- Top Announcement Bar -->
    <div class="top-bar-notice">
        <span>📱 Sell Your iPhone Online • Free Doorstep Pickup in Mumbai • Secure Instant Payment</span>
    </div>

    <!-- Main Navigation Header -->
    <header class="site-header" id="site-header">
        <div class="container header-inner">
            <!-- Brand Logo -->
            <a href="index.php" class="brand-logo" title="CashSecond - iPhone Buyback">
                <img src="assets/images/logo.svg" alt="CashSecond" width="150" height="30">
            </a>

            <!-- Desktop Navigation Links -->
            <nav class="nav-menu" aria-label="Main Navigation">
                <a href="#top" class="nav-link">Home</a>
                <a href="#how-it-works" class="nav-link">How It Works</a>
                <a href="#why-us" class="nav-link">Why Us</a>
                <a href="#faq" class="nav-link">FAQ</a>
                <a href="#contact" class="nav-link">Contact</a>
            </nav>

            <!-- Header Action CTAs -->
            <div class="header-actions">
                <a href="#valuation" class="btn btn-primary btn-sm" id="header-cta-btn">Sell Your iPhone</a>

                <!-- Mobile Hamburger Menu Toggle -->
                <button type="button" class="hamburger-btn" id="mobile-menu-toggle" aria-label="Toggle Navigation Menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>

        <!-- Mobile Navigation Drawer -->
        <div class="mobile-nav-drawer" id="mobile-nav-drawer">
            <div class="mobile-nav-links">
                <a href="#top" class="nav-link mobile-nav-item">Home</a>
                <a href="#valuation" class="nav-link mobile-nav-item">Check iPhone Value</a>
                <a href="#models" class="nav-link mobile-nav-item">iPhone Models</a>
                <a href="#why-us" class="nav-link mobile-nav-item">Why Sell With Us</a>
                <a href="#inspection" class="nav-link mobile-nav-item">32-Point Inspection</a>
                <a href="#how-it-works" class="nav-link mobile-nav-item">How It Works</a>
                <a href="#faq" class="nav-link mobile-nav-item">FAQs</a>
                <a href="#contact" class="nav-link mobile-nav-item">Contact Us</a>
            </div>
            <a href="#valuation" class="btn btn-primary btn-full mobile-nav-item">Check My iPhone Value</a>
        </div>
    </header>
