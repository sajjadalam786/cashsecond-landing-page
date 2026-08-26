<?php
/**
 * CashSecond - Apple-Inspired iPhone Valuation & Buyback Landing Page
 * Full Implementation adhering to all UX, Performance, and SEO standards
 */

session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$config = require __DIR__ . '/config/config.php';
$business = $config['business'] ?? [];
$sellBrands = $config['sell_brands'] ?? [];
$iphoneModels = $sellBrands['Apple'] ?? [];
$faqs = $config['faqs'] ?? [];

// Sorted 35 iPhone Models for the Showcase Strip (iPhone 17 down to iPhone 8)
$orderMap = [
    'Apple iPhone 17 Pro Max' => 1,
    'Apple iPhone 17 Pro' => 2,
    'Apple iPhone 17 Air' => 3,
    'Apple iPhone 17e' => 4,
    'Apple iPhone 17' => 5,
    'Apple iPhone 16 Pro Max' => 6,
    'Apple iPhone 16 Pro' => 7,
    'Apple iPhone 16 Plus' => 8,
    'Apple iPhone 16' => 9,
    'Apple iPhone 15 Pro Max' => 10,
    'Apple iPhone 15 Pro' => 11,
    'Apple iPhone 15 Plus' => 12,
    'Apple iPhone 15' => 13,
    'Apple iPhone 14 Pro Max' => 14,
    'Apple iPhone 14 Pro' => 15,
    'Apple iPhone 14 Plus' => 16,
    'Apple iPhone 14' => 17,
    'Apple iPhone 13 Pro Max' => 18,
    'Apple iPhone 13 Pro' => 19,
    'Apple iPhone 13' => 20,
    'Apple iPhone 13 Mini' => 21,
    'Apple iPhone 12 Pro Max' => 22,
    'Apple iPhone 12 Pro' => 23,
    'Apple iPhone 12' => 24,
    'Apple iPhone 12 Mini' => 25,
    'Apple iPhone 11 Pro Max' => 26,
    'Apple iPhone 11 Pro' => 27,
    'Apple iPhone 11' => 28,
    'Apple iPhone XS Max' => 29,
    'Apple iPhone XS' => 30,
    'Apple iPhone XR' => 31,
    'Apple iPhone X' => 32,
    'Apple iPhone SE (2022)' => 33,
    'Apple iPhone 8 Plus' => 34,
    'Apple iPhone 8' => 35,
];

$showcaseModels = $iphoneModels;
usort($showcaseModels, function($a, $b) use ($orderMap) {
    $posA = $orderMap[$a['product_name']] ?? 999;
    $posB = $orderMap[$b['product_name']] ?? 999;
    return $posA - $posB;
});

// Include Header
require __DIR__ . '/includes/header.php';
?>

<!-- ============================================================
     TOP GLOBAL SEARCH BAR (BETWEEN HEADER & HERO BANNER)
     ============================================================ -->
<section class="top-search-section" id="top-search-bar" aria-label="Search iPhone Models">
    <div class="container top-search-container">
        <div class="top-search-wrapper" id="global-search-wrapper">
            <div class="top-search-input-group">
                <!-- Search Icon -->
                <span class="top-search-icon" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </span>

                <!-- Search Input Field -->
                <input 
                    type="search" 
                    id="top-iphone-search-input" 
                    class="top-search-input" 
                    placeholder="Search iPhone Models" 
                    autocomplete="off" 
                    spellcheck="false"
                    aria-label="Search iPhone Models"
                    aria-expanded="false"
                    aria-controls="top-search-autocomplete"
                >

                <!-- Clear Button -->
                <button type="button" class="top-search-clear-btn" id="top-search-clear-btn" aria-label="Clear search" style="display: none;">
                    &times;
                </button>
            </div>

            <!-- Autocomplete Live Dropdown -->
            <div class="top-search-dropdown" id="top-search-autocomplete" role="listbox" aria-label="Matching iPhone Models" style="display: none;">
                <div class="top-search-results-list" id="top-search-results-list">
                    <!-- Populated dynamically via JS from iPhone catalog -->
                </div>
                <div class="top-search-empty" id="top-search-empty-state" style="display: none;">
                    <p>No iPhone model matching your search.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     1. HERO SECTION — PREMIUM APPLE-INSPIRED PRODUCT PRESENTATION
     ============================================================ -->
<section class="hero-section" id="promo-hero">
    <div class="container hero-container">
        <div class="hero-grid">
            <!-- Left Column: High-Converting Hero Content -->
            <div class="hero-content">
                <div class="hero-tag-badge">
                    <span class="hero-tag-dot"></span>
                    <span class="hero-tag-text">SELL YOUR iPHONE</span>
                </div>

                <h1 class="hero-main-title">Get the Best Value for Your iPhone</h1>
                
                <p class="hero-main-subtitle">Get an instant valuation, sell your iPhone online, enjoy free doorstep pickup, secure data wipe, and fast payment.</p>

                <!-- Premium Brand Value Statement -->
                <div class="hero-brand-statement">
                    <p class="hero-slogan-title">Your iPhone Deserves Its Best Value.</p>
                    <p class="hero-slogan-subtitle">Fair pricing • Trusted buyers • Fast &amp; secure doorstep service</p>
                    
                    <!-- Section 2: Trust / Proof Badges - Clean Single Horizontal Row -->
                    <div class="trust-badge-row" aria-label="Verified Trust Credentials">
                        <span class="trust-badge-pill">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                            <span>GST Registered &amp; Verified</span>
                        </span>
                        <span class="trust-badge-pill">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                            <span>Trusted by 500+ Customers</span>
                        </span>
                    </div>

                    <!-- STEP 1: PHONE VALUATION ENTRY CARD -->
                    <div class="questionnaire-entry-card" id="valuation-entry" style="margin-top: 14px;">
                        <div class="entry-card-phone-visual">
                            <div class="entry-phone-img-wrap">
                                <img src="assets/images/phones/iphone-13.svg" alt="Apple iPhone 13 Resale" class="entry-phone-img" width="56" height="70" loading="lazy">
                            </div>
                            <div class="entry-card-info">
                                <h3 class="entry-card-model" id="entrySelectedModel">Apple iPhone 13 (128 GB)</h3>
                                <div class="entry-card-upto-box">
                                    <span class="entry-card-upto-label">Get Upto</span>
                                    <strong class="entry-card-upto-price" id="entrySelectedPrice">₹23,220</strong>
                                </div>
                                <p class="entry-card-trust-note">Free doorstep pickup in Mumbai • Spot cash/UPI payment</p>
                            </div>
                        </div>
                        <div class="entry-card-action">
                            <button type="button" class="btn-get-exact-value start-exact-valuation-btn" id="startExactValuationBtn" data-model="Apple iPhone 13" data-variant="128 GB" aria-haspopup="dialog" aria-controls="buybackQuestionnaireModal">
                                <span>Get Exact Value &rarr;</span>
                            </button>
                        </div>
                    </div>

                    <p class="hero-slogan-note" style="margin-top: 8px;">Trusted iPhone buyers • Free doorstep pickup • No obligation</p>
                </div>
            </div>

            <!-- Right Column: Studio Commercial Product Visual -->
            <div class="hero-visual-col">
                <div class="hero-product-stage">
                    <img 
                        src="assets/images/hero-iphone-showcase.png" 
                        alt="Sell Apple iPhone at best resale value with CashSecond" 
                        class="hero-product-img" 
                        width="1024" 
                        height="682" 
                        loading="eager"
                    >
                </div>
            </div>
        </div>

        <!-- Section 1: All 8 iPhone Keywords - Clean Single Horizontal Row -->
        <div class="hero-keywords-wrapper">
            <div class="keyword-container" aria-label="Certified iPhone Resale Benefits and Services">
                <span class="keyword-pill">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                    <span>Certified iPhone Buyer</span>
                </span>
                <span class="keyword-pill">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                    <span>Instant iPhone Valuation</span>
                </span>
                <span class="keyword-pill">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                    <span>Best Market Price</span>
                </span>
                <span class="keyword-pill">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                    <span>Free Doorstep Pickup</span>
                </span>
                <span class="keyword-pill">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                    <span>Secure Data Protection</span>
                </span>
                <span class="keyword-pill">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                    <span>Fast &amp; Secure Payment</span>
                </span>
                <span class="keyword-pill">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                    <span>Used iPhone Trade-In</span>
                </span>
                <span class="keyword-pill">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                    <span>Hassle-Free iPhone Buyback</span>
                </span>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     2. iPHONE MODELS HORIZONTAL SCROLLING STRIP (AUTO-SCROLL MARQUEE)
     ============================================================ -->
<div class="iphone-strip-section" aria-label="Featured iPhone Models for Resale">
    <div class="iphone-strip-wrapper">
        <div class="iphone-strip-track">
            <!-- Sell iPhone 16 Pro Max -->
            <a href="#valuation" class="iphone-pill-card" data-name="Apple iPhone 16 Pro Max" data-id="1757" data-image="assets/images/phones/iphone-16-pro.svg" role="button" aria-label="Sell Apple iPhone 16 Pro Max">
                <div class="iphone-pill-img-wrap">
                    <img src="assets/images/phones/iphone-16-pro.svg" alt="Sell Apple iPhone 16 Pro Max" class="iphone-pill-img" width="28" height="34" loading="lazy">
                </div>
                <span class="iphone-pill-title">Sell iPhone 16 Pro Max</span>
            </a>

            <!-- iPhone 16 Pro Resale -->
            <a href="#valuation" class="iphone-pill-card" data-name="Apple iPhone 16 Pro" data-id="1753" data-image="assets/images/phones/iphone-16-pro.svg" role="button" aria-label="iPhone 16 Pro Resale">
                <div class="iphone-pill-img-wrap">
                    <img src="assets/images/phones/iphone-16-pro.svg" alt="iPhone 16 Pro resale" class="iphone-pill-img" width="28" height="34" loading="lazy">
                </div>
                <span class="iphone-pill-title">iPhone 16 Pro Resale</span>
            </a>

            <!-- Sell iPhone 16 -->
            <a href="#valuation" class="iphone-pill-card" data-name="Apple iPhone 16" data-id="1747" data-image="assets/images/phones/iphone-16.svg" role="button" aria-label="Sell Apple iPhone 16">
                <div class="iphone-pill-img-wrap">
                    <img src="assets/images/phones/iphone-16.svg" alt="Sell Apple iPhone 16" class="iphone-pill-img" width="28" height="34" loading="lazy">
                </div>
                <span class="iphone-pill-title">Sell iPhone 16</span>
            </a>

            <!-- iPhone 15 Pro Max -->
            <a href="#valuation" class="iphone-pill-card" data-name="Apple iPhone 15 Pro Max" data-id="1351" data-image="assets/images/phones/iphone-15-pro.svg" role="button" aria-label="iPhone 15 Pro Max Valuation">
                <div class="iphone-pill-img-wrap">
                    <img src="assets/images/phones/iphone-15-pro.svg" alt="iPhone 15 Pro Max valuation" class="iphone-pill-img" width="28" height="34" loading="lazy">
                </div>
                <span class="iphone-pill-title">iPhone 15 Pro Max Value</span>
            </a>

            <!-- iPhone 15 Pro -->
            <a href="#valuation" class="iphone-pill-card" data-name="Apple iPhone 15 Pro" data-id="1347" data-image="assets/images/phones/iphone-15-pro.svg" role="button" aria-label="iPhone 15 Pro Resale Value">
                <div class="iphone-pill-img-wrap">
                    <img src="assets/images/phones/iphone-15-pro.svg" alt="iPhone 15 Pro resale" class="iphone-pill-img" width="28" height="34" loading="lazy">
                </div>
                <span class="iphone-pill-title">iPhone 15 Pro Resale</span>
            </a>

            <!-- Sell iPhone 15 -->
            <a href="#valuation" class="iphone-pill-card" data-name="Apple iPhone 15" data-id="1341" data-image="assets/images/phones/iphone-15.svg" role="button" aria-label="Sell Apple iPhone 15">
                <div class="iphone-pill-img-wrap">
                    <img src="assets/images/phones/iphone-15.svg" alt="Sell Apple iPhone 15" class="iphone-pill-img" width="28" height="34" loading="lazy">
                </div>
                <span class="iphone-pill-title">Sell iPhone 15</span>
            </a>

            <!-- iPhone 14 Pro -->
            <a href="#valuation" class="iphone-pill-card" data-name="Apple iPhone 14 Pro" data-id="81" data-image="assets/images/phones/iphone-14-pro.svg" role="button" aria-label="iPhone 14 Pro Buyback">
                <div class="iphone-pill-img-wrap">
                    <img src="assets/images/phones/iphone-14-pro.svg" alt="iPhone 14 Pro buyback" class="iphone-pill-img" width="28" height="34" loading="lazy">
                </div>
                <span class="iphone-pill-title">iPhone 14 Pro Buyback</span>
            </a>

            <!-- Sell iPhone 14 -->
            <a href="#valuation" class="iphone-pill-card" data-name="Apple iPhone 14" data-id="76" data-image="assets/images/phones/iphone-14.svg" role="button" aria-label="Sell Apple iPhone 14">
                <div class="iphone-pill-img-wrap">
                    <img src="assets/images/phones/iphone-14.svg" alt="Sell Apple iPhone 14" class="iphone-pill-img" width="28" height="34" loading="lazy">
                </div>
                <span class="iphone-pill-title">Sell iPhone 14</span>
            </a>

            <!-- Sell iPhone 13 -->
            <a href="#valuation" class="iphone-pill-card" data-name="Apple iPhone 13" data-id="47" data-image="assets/images/phones/iphone-13.svg" role="button" aria-label="Sell Apple iPhone 13">
                <div class="iphone-pill-img-wrap">
                    <img src="assets/images/phones/iphone-13.svg" alt="Sell Apple iPhone 13" class="iphone-pill-img" width="28" height="34" loading="lazy">
                </div>
                <span class="iphone-pill-title">Sell iPhone 13</span>
            </a>
        </div>

        <!-- Exact Duplicate Track for 100% Seamless Infinite Loop -->
        <div class="iphone-strip-track" aria-hidden="true">
            <a href="#valuation" class="iphone-pill-card" data-name="Apple iPhone 16 Pro Max" data-id="1757" data-image="assets/images/phones/iphone-16-pro.svg" tabindex="-1">
                <div class="iphone-pill-img-wrap">
                    <img src="assets/images/phones/iphone-16-pro.svg" alt="Sell Apple iPhone 16 Pro Max" class="iphone-pill-img" width="28" height="34" loading="lazy">
                </div>
                <span class="iphone-pill-title">Sell iPhone 16 Pro Max</span>
            </a>
            <a href="#valuation" class="iphone-pill-card" data-name="Apple iPhone 16 Pro" data-id="1753" data-image="assets/images/phones/iphone-16-pro.svg" tabindex="-1">
                <div class="iphone-pill-img-wrap">
                    <img src="assets/images/phones/iphone-16-pro.svg" alt="iPhone 16 Pro resale" class="iphone-pill-img" width="28" height="34" loading="lazy">
                </div>
                <span class="iphone-pill-title">iPhone 16 Pro Resale</span>
            </a>
            <a href="#valuation" class="iphone-pill-card" data-name="Apple iPhone 16" data-id="1747" data-image="assets/images/phones/iphone-16.svg" tabindex="-1">
                <div class="iphone-pill-img-wrap">
                    <img src="assets/images/phones/iphone-16.svg" alt="Sell Apple iPhone 16" class="iphone-pill-img" width="28" height="34" loading="lazy">
                </div>
                <span class="iphone-pill-title">Sell iPhone 16</span>
            </a>
            <a href="#valuation" class="iphone-pill-card" data-name="Apple iPhone 15 Pro Max" data-id="1351" data-image="assets/images/phones/iphone-15-pro.svg" tabindex="-1">
                <div class="iphone-pill-img-wrap">
                    <img src="assets/images/phones/iphone-15-pro.svg" alt="iPhone 15 Pro Max valuation" class="iphone-pill-img" width="28" height="34" loading="lazy">
                </div>
                <span class="iphone-pill-title">iPhone 15 Pro Max Value</span>
            </a>
            <a href="#valuation" class="iphone-pill-card" data-name="Apple iPhone 15 Pro" data-id="1347" data-image="assets/images/phones/iphone-15-pro.svg" tabindex="-1">
                <div class="iphone-pill-img-wrap">
                    <img src="assets/images/phones/iphone-15-pro.svg" alt="iPhone 15 Pro resale" class="iphone-pill-img" width="28" height="34" loading="lazy">
                </div>
                <span class="iphone-pill-title">iPhone 15 Pro Resale</span>
            </a>
            <a href="#valuation" class="iphone-pill-card" data-name="Apple iPhone 15" data-id="1341" data-image="assets/images/phones/iphone-15.svg" tabindex="-1">
                <div class="iphone-pill-img-wrap">
                    <img src="assets/images/phones/iphone-15.svg" alt="Sell Apple iPhone 15" class="iphone-pill-img" width="28" height="34" loading="lazy">
                </div>
                <span class="iphone-pill-title">Sell iPhone 15</span>
            </a>
            <a href="#valuation" class="iphone-pill-card" data-name="Apple iPhone 14 Pro" data-id="81" data-image="assets/images/phones/iphone-14-pro.svg" tabindex="-1">
                <div class="iphone-pill-img-wrap">
                    <img src="assets/images/phones/iphone-14-pro.svg" alt="iPhone 14 Pro buyback" class="iphone-pill-img" width="28" height="34" loading="lazy">
                </div>
                <span class="iphone-pill-title">iPhone 14 Pro Buyback</span>
            </a>
            <a href="#valuation" class="iphone-pill-card" data-name="Apple iPhone 14" data-id="76" data-image="assets/images/phones/iphone-14.svg" tabindex="-1">
                <div class="iphone-pill-img-wrap">
                    <img src="assets/images/phones/iphone-14.svg" alt="Sell Apple iPhone 14" class="iphone-pill-img" width="28" height="34" loading="lazy">
                </div>
                <span class="iphone-pill-title">Sell iPhone 14</span>
            </a>
            <a href="#valuation" class="iphone-pill-card" data-name="Apple iPhone 13" data-id="47" data-image="assets/images/phones/iphone-13.svg" tabindex="-1">
                <div class="iphone-pill-img-wrap">
                    <img src="assets/images/phones/iphone-13.svg" alt="Sell Apple iPhone 13" class="iphone-pill-img" width="28" height="34" loading="lazy">
                </div>
                <span class="iphone-pill-title">Sell iPhone 13</span>
            </a>
        </div>
    </div>
</div>





<!-- ============================================================
     4. HOW IT WORKS (3 SIMPLE STEPS)
     ============================================================ -->
<section class="section-container how-it-works-section" id="how-it-works">
    <div class="container">
        <div class="section-header text-center">
            <span class="section-eyebrow">HOW IT WORKS</span>
            <h2 class="section-title">Sell Your iPhone in 3 Simple Steps</h2>
            <p class="section-subtitle">Experience a fast, transparent, and secure doorstep selling journey built exclusively for Apple users.</p>
        </div>

        <div class="how-it-works-3grid">
            <!-- Step 01 -->
            <div class="how-step-card-3">
                <div class="how-step-badge">01</div>    
                <div class="how-step-icon-wrap">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect>
                        <line x1="12" y1="18" x2="12.01" y2="18"></line>
                    </svg>
                </div>
                <h3 class="how-step-heading">Select Your iPhone</h3>
                <p class="how-step-text">Choose your exact model and storage capacity on our valuation calculator.</p>
            </div>

            <!-- Step 02 -->
            <div class="how-step-card-3">
                <div class="how-step-badge">02</div>
                <div class="how-step-icon-wrap">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="1" x2="12" y2="23"></line>
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                    </svg>
                </div>
                <h3 class="how-step-heading">Get Your Value</h3>
                <p class="how-step-text">Answer a few condition questions to receive an instant, market-accurate resale estimate.</p>
            </div>

            <!-- Step 03 -->
            <div class="how-step-card-3">
                <div class="how-step-badge">03</div>
                <div class="how-step-icon-wrap">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                </div>
                <h3 class="how-step-heading">Pickup &amp; Get Paid</h3>
                <p class="how-step-text">Our executive arrives at your doorstep, verifies the device, and transfers payment on the spot.</p>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     5. PROMOTIONAL BANNER 1 (Dark Theme: Value & Quick Valuation)
     ============================================================ -->
<div class="promo-banner-wrapper">
    <div class="container">
        <aside class="promo-banner promo-banner-dark" aria-label="Ready to sell your iPhone">
            <div class="promo-banner-content">
                <span class="promo-banner-eyebrow">READY TO SELL?</span>
                <h3 class="promo-banner-title">Your iPhone Deserves a Better Value.</h3>
                <p class="promo-banner-desc">Check your iPhone's value in seconds and get a hassle-free pickup.</p>
                <a href="#valuation" class="btn promo-banner-cta btn-promo-dark" id="heroCheckValueBtn">
                    <span>Check Your iPhone Value</span>
                    <span class="promo-cta-arrow" aria-hidden="true">&rarr;</span>
                </a>
            </div>
            <div class="promo-banner-visual">
                <img 
                    src="assets/images/phones/banner-iphone-desert.svg" 
                    alt="Sell iPhone for best value with doorstep pickup" 
                    class="promo-banner-img" 
                    width="420" 
                    height="280" 
                    loading="lazy"
                >
            </div>
        </aside>
    </div>
</div>

<!-- ============================================================
     6. CUSTOMER REVIEWS (Master Expandable Section)
     ============================================================ -->
<section class="section-container reviews-section-wrapper" id="reviews">
    <div class="container">
        <div class="reviews-card-panel" id="reviewsMasterPanel">
            <!-- Master Clickable Header / Card Trigger -->
            <button type="button" class="reviews-master-header" id="reviewsMasterToggle" aria-expanded="false" aria-controls="reviewsMasterBody" aria-label="Toggle Customer Experiences panel">
                <div class="reviews-master-header-text">
                    <span class="section-eyebrow reviews-eyebrow">Customer Experiences</span>
                    <h2 class="section-title reviews-master-title">What Our Customers Say</h2>
                </div>
                <div class="reviews-master-toggle-indicator" aria-hidden="true">
                    <span class="reviews-master-icon">
                        <svg class="reviews-chevron-svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </span>
                </div>
            </button>

            <!-- Collapsible Body Containing Subtitle and Full Testimonials Grid -->
            <div class="reviews-master-collapse" id="reviewsMasterBody">
                <div class="reviews-master-body-inner">
                    <p class="section-subtitle reviews-master-subtitle">Real feedback from iPhone sellers across Mumbai who traded through CashSecond.</p>

                    <div class="reviews-grid">
                        <div class="review-card">
                            <div class="review-stars">★★★★★</div>
                            <p class="review-text">"Sold my iPhone 14 Pro directly through CashSecond. The executive arrived in Bandra within 2 hours, verified the screen and battery health, and transferred the agreed amount on Google Pay immediately. Super transparent!"</p>
                            <div class="review-author">
                                <span class="author-name">Rahul S. • Bandra West</span>
                                <span class="author-model">iPhone 14 Pro 128GB</span>
                            </div>
                        </div>
                        <div class="review-card">
                            <div class="review-stars">★★★★★</div>
                            <p class="review-text">"Great experience getting a value for my iPhone 13. Very honest valuation with no last-minute deductions since my condition answers were accurate. The pickup executive was very polite."</p>
                            <div class="review-author">
                                <span class="author-name">Pooja M. • Andheri</span>
                                <span class="author-model">iPhone 13 128GB</span>
                            </div>
                        </div>
                        <div class="review-card">
                            <div class="review-stars">★★★★★</div>
                            <p class="review-text">"Checked the value online and scheduled doorstep pickup at Nariman Point office. The transaction took barely 10 minutes. Fast payment and complete data reset assistance."</p>
                            <div class="review-author">
                                <span class="author-name">Vikram K. • South Mumbai</span>
                                <span class="author-model">iPhone 15 256GB</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     7. PROMOTIONAL BANNER 2 (Light Apple Theme: Instant Payment)
     ============================================================ -->
<div class="promo-banner-wrapper">
    <div class="container">
        <aside class="promo-banner promo-banner-light" aria-label="Sell your iPhone with confidence">
            <div class="promo-banner-content">
                <span class="promo-banner-eyebrow">SELL WITH CONFIDENCE</span>
                <h3 class="promo-banner-title">From Your iPhone to Instant Payment.</h3>
                <p class="promo-banner-desc">Transparent valuation, doorstep pickup and secure data handling — all in one simple experience.</p>
                <a href="#valuation" class="btn promo-banner-cta btn-promo-light">
                    <span>Get Your iPhone Value</span>
                    <span class="promo-cta-arrow" aria-hidden="true">&rarr;</span>
                </a>
            </div>
            <div class="promo-banner-visual">
                <img 
                    src="assets/images/phones/banner-iphone-payment.svg" 
                    alt="Instant payment transfer for your sold iPhone" 
                    class="promo-banner-img" 
                    width="420" 
                    height="280" 
                    loading="lazy"
                >
            </div>
        </aside>
    </div>
</div>

<!-- ============================================================
     8. FAQ ACCORDION SECTION (10 SEO & AEO Questions)
     ============================================================ -->
<section class="section-container faq-section-wrapper" id="faq">
    <div class="container">
        <div class="faq-card-panel" id="faqMasterPanel">
            <!-- Master Clickable Header / Card Trigger -->
            <button type="button" class="faq-master-header" id="faqMasterToggle" aria-expanded="false" aria-controls="faqMasterBody" aria-label="Toggle Frequently Asked Questions panel">
                <div class="faq-master-header-text">
                    <span class="section-eyebrow faq-eyebrow">Got Questions?</span>
                    <h2 class="section-title faq-master-title">Frequently Asked Questions</h2>
                </div>
                <div class="faq-master-toggle-indicator" aria-hidden="true">
                    <span class="faq-master-icon">
                        <svg class="faq-chevron-svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </span>
                </div>
            </button>

            <!-- Collapsible Body Containing Subtitle and Full FAQ List -->
            <div class="faq-master-collapse" id="faqMasterBody">
                <div class="faq-master-body-inner">
                    <p class="section-subtitle faq-master-subtitle">Everything you need to know about our iPhone valuation, doorstep pickup, and instant payouts.</p>
                    
                    <div class="faq-accordion">
                        <?php foreach ($faqs as $idx => $faq): ?>
                        <div class="faq-item <?= $idx === 0 ? 'active' : '' ?>">
                            <button type="button" class="faq-btn" aria-expanded="<?= $idx === 0 ? 'true' : 'false' ?>">
                                <span><?= htmlspecialchars($faq['q']) ?></span>
                                <span class="faq-icon">+</span>
                            </button>
                            <div class="faq-content">
                                <p><?= htmlspecialchars($faq['a']) ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     11. FINAL DARK CTA & BOOKING FORM
     ============================================================ -->
                        
<!-- ============================================================
     12. STORE CONTACT & LOCATION SECTION
     ============================================================ -->
<section class="section-container" id="contact" style="background-color: var(--color-bg-page);">
    <div class="container">
        <div class="section-header text-center">
            <span class="section-eyebrow">Contact & Store</span>
            <h2 class="section-title">Contact CashSecond</h2>
            <p class="section-subtitle">Visit our corporate office or schedule executive doorstep pickup across Mumbai.</p>
        </div>

        <div class="contact-grid">
            <div class="contact-card">
                <h3 style="font-size: 1.125rem; font-weight: 700; color: var(--color-dark); margin-bottom: 16px;">Corporate Office</h3>
                <div class="contact-item">
                    <svg class="contact-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    <div>
                        <strong>CashSecond</strong><br>
                        Office Number 1307, 13th Floor, Arcadia Building, NCPA Marg, Nariman Point, Mumbai – 400021
                    </div>
                </div>
                <div class="contact-item">
                    <svg class="contact-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    <div>
                        Phone: <a href="tel:+918976332211" style="font-weight: 600; color: var(--color-cta);">+91 897633 2211</a>
                    </div>
                </div>
                <div class="contact-item">
                    <svg class="contact-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <div>
                        Hours: Monday – Sunday, 10:00 AM – 9:00 PM
                    </div>
                </div>
                <div class="contact-item">
                    <svg class="contact-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    <div>
                        Email: <a href="mailto:cashsecondofficial@gmail.com" style="color: var(--color-cta);">cashsecondofficial@gmail.com</a>
                    </div>
                </div>
            </div>

            <!-- Interactive Map Preview Card -->
            <div class="contact-card contact-map-card" id="contact-map-trigger" role="button" tabindex="0" aria-label="Open Free Consultation Form">
                <div class="map-preview-wrap">
                    <img 
                        src="assets/images/location.jpg" 
                        alt="CashSecond Office Location Map Preview - Nariman Point Mumbai" 
                        class="contact-map-preview-img" 
                        width="600" 
                        height="380" 
                        loading="lazy"
                    >
                    <div class="map-click-overlay">
                        <span class="map-click-badge">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            <span>Click to Request Free Consultation</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     13. COMPACT SMART EXCHANGE / DEVICE CHECK FOOTER WIDGET
     ============================================================ -->
<section class="smart-exchange-widget-section" id="smart-exchange" aria-label="Smart iPhone Exchange & Device Diagnostics">
    <div class="container">
        <div class="smart-exchange-mini-card">
            <div class="smart-exchange-mini-content">
                <div class="smart-exchange-mini-icon-wrap">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect>
                        <path d="M12 18h.01"></path>
                        <path d="M9 10l2 2 4-4"></path>
                    </svg>
                </div>
                <div class="smart-exchange-mini-text">
                    <h3 class="smart-exchange-mini-title">Smart iPhone Exchange</h3>
                    <p class="smart-exchange-mini-desc">Check your iPhone condition and get an estimated exchange value.</p>
                </div>
            </div>
            <div class="smart-exchange-mini-action">
                <button type="button" class="btn btn-primary smart-exchange-open-btn" id="openSmartExchangeBtn" aria-haspopup="dialog" aria-controls="smartExchangeModal">
                    <span>Start Device Check &rarr;</span>
                </button>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     SMART EXCHANGE / DEVICE CHECK MODAL & DIAGNOSTIC ENGINE
     ============================================================ -->
<div class="smart-exchange-modal-overlay" id="smartExchangeModal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="smartExchangeModalTitle">
    <div class="smart-exchange-modal-backdrop" id="smartExchangeBackdrop"></div>
    <div class="smart-exchange-modal-dialog">
        <!-- Close Button -->
        <button type="button" class="smart-exchange-close-btn" id="smartExchangeCloseBtn" aria-label="Close Smart Exchange">&times;</button>
        
        <!-- Modal Header -->
        <div class="smart-exchange-header">
            <div class="smart-exchange-badge-row">
                <span class="smart-exchange-pill-badge">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    Automated Diagnostic Engine
                </span>
            </div>
            <h2 class="smart-exchange-title" id="smartExchangeModalTitle">Smart Device Check &amp; Exchange</h2>
            <p class="smart-exchange-subtitle">Verify device functionality across 4 core hardware categories to calculate your guaranteed exchange value.</p>
            
            <!-- Quick Device Switcher -->
            <div class="smart-exchange-device-bar">
                <div class="smart-device-info">
                    <span class="smart-device-label">Selected Device:</span>
                    <strong class="smart-device-name" id="smartExchangeDeviceName">Apple iPhone 13 Pro (128 GB)</strong>
                </div>
                <div class="smart-device-selector-wrap">
                    <select id="smartExchangeModelSelect" class="smart-device-select" aria-label="Select iPhone Model for Diagnostic">
                        <option value="Apple iPhone 17 Pro Max|78000" data-storage="256GB">iPhone 17 Pro Max • 256 GB</option>
                        <option value="Apple iPhone 17 Pro|72000" data-storage="128GB">iPhone 17 Pro • 128 GB</option>
                        <option value="Apple iPhone 16 Pro Max|68000" data-storage="256GB">iPhone 16 Pro Max • 256 GB</option>
                        <option value="Apple iPhone 16 Pro|58500" data-storage="128GB">iPhone 16 Pro • 128 GB</option>
                        <option value="Apple iPhone 16|48000" data-storage="128GB">iPhone 16 • 128 GB</option>
                        <option value="Apple iPhone 15 Pro Max|54000" data-storage="256GB">iPhone 15 Pro Max • 256 GB</option>
                        <option value="Apple iPhone 15 Pro|49000" data-storage="128GB">iPhone 15 Pro • 128 GB</option>
                        <option value="Apple iPhone 15|39000" data-storage="128GB">iPhone 15 • 128 GB</option>
                        <option value="Apple iPhone 14 Pro|42000" data-storage="128GB">iPhone 14 Pro • 128 GB</option>
                        <option value="Apple iPhone 14|33000" data-storage="128GB">iPhone 14 • 128 GB</option>
                        <option value="Apple iPhone 13 Pro|42500" data-storage="128GB" selected>iPhone 13 Pro • 128 GB</option>
                        <option value="Apple iPhone 13|27500" data-storage="128GB">iPhone 13 • 128 GB</option>
                        <option value="Apple iPhone 12 Pro|26000" data-storage="128GB">iPhone 12 Pro • 128 GB</option>
                        <option value="Apple iPhone 12|20500" data-storage="64GB">iPhone 12 • 64 GB</option>
                        <option value="Apple iPhone 11|15500" data-storage="64GB">iPhone 11 • 64 GB</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Modal Body: Compact Accordions -->
        <div class="smart-exchange-body">
            <div class="diagnostic-accordion-group">
                <!-- CATEGORY 1: PHYSICAL -->
                <div class="diag-cat-card active" data-category="physical">
                    <button type="button" class="diag-cat-header" aria-expanded="true">
                        <div class="diag-cat-header-left">
                            <span class="diag-cat-icon">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/></svg>
                            </span>
                            <span class="diag-cat-name">Physical</span>
                        </div>
                        <div class="diag-cat-header-right">
                            <span class="diag-cat-status-pill" id="stat-pill-physical">
                                <span class="stat-pass">5 Passed</span> | <span class="stat-fail">1 Failed</span>
                            </span>
                            <span class="diag-cat-chevron">▼</span>
                        </div>
                    </button>
                    <div class="diag-cat-body">
                        <div class="diag-test-list">
                            <div class="diag-test-row" data-test="screen_glass" data-penalty="2000" data-status="pass">
                                <div class="diag-test-info">
                                    <span class="diag-test-icon">📱</span>
                                    <span class="diag-test-label">Front Screen Glass</span>
                                </div>
                                <button type="button" class="diag-toggle-btn pass" aria-label="Toggle Front Screen Glass result">
                                    <span class="diag-check-indicator">✓</span>
                                </button>
                            </div>
                            <div class="diag-test-row" data-test="touch_screen" data-penalty="3000" data-status="pass">
                                <div class="diag-test-info">
                                    <span class="diag-test-icon">👆</span>
                                    <span class="diag-test-label">Touch Screen Response</span>
                                </div>
                                <button type="button" class="diag-toggle-btn pass" aria-label="Toggle Touch Screen result">
                                    <span class="diag-check-indicator">✓</span>
                                </button>
                            </div>
                            <div class="diag-test-row" data-test="body_housing" data-penalty="1500" data-status="pass">
                                <div class="diag-test-info">
                                    <span class="diag-test-icon">🛡️</span>
                                    <span class="diag-test-label">Body &amp; Metal Housing</span>
                                </div>
                                <button type="button" class="diag-toggle-btn pass" aria-label="Toggle Body Housing result">
                                    <span class="diag-check-indicator">✓</span>
                                </button>
                            </div>
                            <div class="diag-test-row" data-test="back_glass" data-penalty="2500" data-status="fail">
                                <div class="diag-test-info">
                                    <span class="diag-test-icon">🪟</span>
                                    <span class="diag-test-label">Back Glass Panel</span>
                                </div>
                                <button type="button" class="diag-toggle-btn fail" aria-label="Toggle Back Glass result">
                                    <span class="diag-check-indicator">✕</span>
                                </button>
                            </div>
                            <div class="diag-test-row" data-test="side_buttons" data-penalty="800" data-status="pass">
                                <div class="diag-test-info">
                                    <span class="diag-test-icon">🔘</span>
                                    <span class="diag-test-label">Side / Volume Buttons</span>
                                </div>
                                <button type="button" class="diag-toggle-btn pass" aria-label="Toggle Side Buttons result">
                                    <span class="diag-check-indicator">✓</span>
                                </button>
                            </div>
                            <div class="diag-test-row" data-test="screen_lines" data-penalty="4000" data-status="pass">
                                <div class="diag-test-info">
                                    <span class="diag-test-icon">✨</span>
                                    <span class="diag-test-label">Display Colors &amp; No Lines</span>
                                </div>
                                <button type="button" class="diag-toggle-btn pass" aria-label="Toggle Display Lines result">
                                    <span class="diag-check-indicator">✓</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CATEGORY 2: DEVICE CHECK -->
                <div class="diag-cat-card" data-category="device">
                    <button type="button" class="diag-cat-header" aria-expanded="false">
                        <div class="diag-cat-header-left">
                            <span class="diag-cat-icon">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
                            </span>
                            <span class="diag-cat-name">Device Check</span>
                        </div>
                        <div class="diag-cat-header-right">
                            <span class="diag-cat-status-pill" id="stat-pill-device">
                                <span class="stat-pass">4 Passed</span> | <span class="stat-fail">1 Failed</span>
                            </span>
                            <span class="diag-cat-chevron">▼</span>
                        </div>
                    </button>
                    <div class="diag-cat-body">
                        <div class="diag-test-list">
                            <div class="diag-test-row" data-test="face_id" data-penalty="3500" data-status="pass">
                                <div class="diag-test-info">
                                    <span class="diag-test-icon">👤</span>
                                    <span class="diag-test-label">Face ID / Biometrics</span>
                                </div>
                                <button type="button" class="diag-toggle-btn pass" aria-label="Toggle Face ID result">
                                    <span class="diag-check-indicator">✓</span>
                                </button>
                            </div>
                            <div class="diag-test-row" data-test="battery_health" data-penalty="1800" data-status="fail">
                                <div class="diag-test-info">
                                    <span class="diag-test-icon">🔋</span>
                                    <span class="diag-test-label">Battery Health &gt; 80%</span>
                                </div>
                                <button type="button" class="diag-toggle-btn fail" aria-label="Toggle Battery Health result">
                                    <span class="diag-check-indicator">✕</span>
                                </button>
                            </div>
                            <div class="diag-test-row" data-test="proximity_sensor" data-penalty="1000" data-status="pass">
                                <div class="diag-test-info">
                                    <span class="diag-test-icon">💡</span>
                                    <span class="diag-test-label">Proximity &amp; Ambient Sensor</span>
                                </div>
                                <button type="button" class="diag-toggle-btn pass" aria-label="Toggle Proximity Sensor result">
                                    <span class="diag-check-indicator">✓</span>
                                </button>
                            </div>
                            <div class="diag-test-row" data-test="true_tone" data-penalty="1200" data-status="pass">
                                <div class="diag-test-info">
                                    <span class="diag-test-icon">🎨</span>
                                    <span class="diag-test-label">True Tone Display</span>
                                </div>
                                <button type="button" class="diag-toggle-btn pass" aria-label="Toggle True Tone result">
                                    <span class="diag-check-indicator">✓</span>
                                </button>
                            </div>
                            <div class="diag-test-row" data-test="vibration" data-penalty="800" data-status="pass">
                                <div class="diag-test-info">
                                    <span class="diag-test-icon">📳</span>
                                    <span class="diag-test-label">Taptic Engine / Vibration</span>
                                </div>
                                <button type="button" class="diag-toggle-btn pass" aria-label="Toggle Vibration result">
                                    <span class="diag-check-indicator">✓</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CATEGORY 3: MULTIMEDIA -->
                <div class="diag-cat-card" data-category="multimedia">
                    <button type="button" class="diag-cat-header" aria-expanded="false">
                        <div class="diag-cat-header-left">
                            <span class="diag-cat-icon">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                            </span>
                            <span class="diag-cat-name">Multimedia</span>
                        </div>
                        <div class="diag-cat-header-right">
                            <span class="diag-cat-status-pill" id="stat-pill-multimedia">
                                <span class="stat-pass">5 Passed</span> | <span class="stat-fail">0 Failed</span>
                            </span>
                            <span class="diag-cat-chevron">▼</span>
                        </div>
                    </button>
                    <div class="diag-cat-body">
                        <div class="diag-test-list">
                            <div class="diag-test-row" data-test="rear_camera" data-penalty="3000" data-status="pass">
                                <div class="diag-test-info">
                                    <span class="diag-test-icon">📷</span>
                                    <span class="diag-test-label">Back Camera &amp; Optical Zoom</span>
                                </div>
                                <button type="button" class="diag-toggle-btn pass" aria-label="Toggle Rear Camera result">
                                    <span class="diag-check-indicator">✓</span>
                                </button>
                            </div>
                            <div class="diag-test-row" data-test="front_camera" data-penalty="2000" data-status="pass">
                                <div class="diag-test-info">
                                    <span class="diag-test-icon">🤳</span>
                                    <span class="diag-test-label">Front TrueDepth Camera</span>
                                </div>
                                <button type="button" class="diag-toggle-btn pass" aria-label="Toggle Front Camera result">
                                    <span class="diag-check-indicator">✓</span>
                                </button>
                            </div>
                            <div class="diag-test-row" data-test="flashlight" data-penalty="500" data-status="pass">
                                <div class="diag-test-info">
                                    <span class="diag-test-icon">🔦</span>
                                    <span class="diag-test-label">Camera Flash / Torch</span>
                                </div>
                                <button type="button" class="diag-toggle-btn pass" aria-label="Toggle Flashlight result">
                                    <span class="diag-check-indicator">✓</span>
                                </button>
                            </div>
                            <div class="diag-test-row" data-test="ear_speaker" data-penalty="1000" data-status="pass">
                                <div class="diag-test-info">
                                    <span class="diag-test-icon">🔊</span>
                                    <span class="diag-test-label">Stereo Speakers</span>
                                </div>
                                <button type="button" class="diag-toggle-btn pass" aria-label="Toggle Speakers result">
                                    <span class="diag-check-indicator">✓</span>
                                </button>
                            </div>
                            <div class="diag-test-row" data-test="microphone" data-penalty="1200" data-status="pass">
                                <div class="diag-test-info">
                                    <span class="diag-test-icon">🎤</span>
                                    <span class="diag-test-label">Microphone &amp; Noise Cancellation</span>
                                </div>
                                <button type="button" class="diag-toggle-btn pass" aria-label="Toggle Microphone result">
                                    <span class="diag-check-indicator">✓</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CATEGORY 4: CONNECTIVITY -->
                <div class="diag-cat-card" data-category="connectivity">
                    <button type="button" class="diag-cat-header" aria-expanded="false">
                        <div class="diag-cat-header-left">
                            <span class="diag-cat-icon">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12.55a11 11 0 0 1 14.08 0"/><path d="M1.42 9a16 16 0 0 1 21.16 0"/><path d="M8.53 16.11a6 6 0 0 1 6.95 0"/><line x1="12" y1="20" x2="12.01" y2="20"/></svg>
                            </span>
                            <span class="diag-cat-name">Connectivity</span>
                        </div>
                        <div class="diag-cat-header-right">
                            <span class="diag-cat-status-pill" id="stat-pill-connectivity">
                                <span class="stat-pass">3 Passed</span> | <span class="stat-fail">0 Failed</span>
                            </span>
                            <span class="diag-cat-chevron">▼</span>
                        </div>
                    </button>
                    <div class="diag-cat-body">
                        <div class="diag-test-list">
                            <div class="diag-test-row" data-test="wifi_bluetooth" data-penalty="1500" data-status="pass">
                                <div class="diag-test-info">
                                    <span class="diag-test-icon">📶</span>
                                    <span class="diag-test-label">Wi-Fi &amp; Bluetooth</span>
                                </div>
                                <button type="button" class="diag-toggle-btn pass" aria-label="Toggle Wi-Fi result">
                                    <span class="diag-check-indicator">✓</span>
                                </button>
                            </div>
                            <div class="diag-test-row" data-test="cellular_sim" data-penalty="2000" data-status="pass">
                                <div class="diag-test-info">
                                    <span class="diag-test-icon">📞</span>
                                    <span class="diag-test-label">Cellular SIM &amp; 5G Network</span>
                                </div>
                                <button type="button" class="diag-toggle-btn pass" aria-label="Toggle Cellular result">
                                    <span class="diag-check-indicator">✓</span>
                                </button>
                            </div>
                            <div class="diag-test-row" data-test="charging_port" data-penalty="1500" data-status="pass">
                                <div class="diag-test-info">
                                    <span class="diag-test-icon">🔌</span>
                                    <span class="diag-test-label">Charging Port &amp; Lightning / USB-C</span>
                                </div>
                                <button type="button" class="diag-toggle-btn pass" aria-label="Toggle Charging Port result">
                                    <span class="diag-check-indicator">✓</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- COMPACT REPORT SUMMARY CARD -->
            <div class="diag-report-summary-card">
                <div class="diag-report-header">
                    <div class="diag-report-tag">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                        <span>Automated Device Report</span>
                    </div>
                    <h3 class="diag-report-title">Device Report</h3>
                    <p class="diag-report-model" id="diagReportModelName">iPhone 13 Pro • 128 GB</p>
                </div>

                <div class="diag-report-price-box">
                    <span class="diag-report-price-label">Estimated Exchange Value</span>
                    <span class="diag-report-price-value" id="diagReportEstimatedVal">₹42,500</span>
                    <div class="diag-report-counters" id="diagReportCounters">
                        <span class="counter-badge pass-badge"><strong id="totalPassCount">17</strong> Passed</span>
                        <span class="counter-badge fail-badge"><strong id="totalFailCount">2</strong> Failed</span>
                    </div>
                </div>

                <div class="diag-report-actions">
                    <button type="button" class="btn btn-primary btn-lg btn-full" id="diagRequestPickupBtn">
                        <span>Request Free Pickup &rarr;</span>
                    </button>
                    <a href="https://wa.me/918976332211?text=Hi%20CashSecond%2C%20I%20completed%20the%20Smart%20Exchange%20device%20check%20for%20my%20iPhone%2013%20Pro%20and%20got%20estimated%20value%20of%20%E2%82%B942%2C500.%20Please%20schedule%20doorstep%20pickup." target="_blank" rel="noopener noreferrer" class="btn btn-whatsapp btn-full" id="diagWhatsAppBtn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
                        <span>WhatsApp Us &rarr;</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================
     FREE CONSULTATION MODAL / POPUP
     ============================================================ -->
<div class="consultation-modal-overlay" id="consultationModal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="consultModalTitle">
    <div class="consultation-modal-backdrop" id="consultModalBackdrop"></div>
    <div class="consultation-modal-card">
        <button type="button" class="consultation-modal-close-btn" id="consultModalCloseBtn" aria-label="Close consultation modal">&times;</button>
        
        <div class="consultation-modal-header text-center">
            <span class="section-eyebrow">FREE EXPERT ASSISTANCE</span>
            <h3 class="consultation-modal-title" id="consultModalTitle">Request Free Consultation</h3>
            <p class="consultation-modal-subtitle">Fill in your details below and our team will get in touch with you shortly.</p>
        </div>

        <form id="consultation-lead-form" class="consultation-form" action="forms/consultation.php" method="POST" novalidate>
            <!-- CSRF Protection -->
            <input type="hidden" name="csrf_token" id="consult_csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
            
            <!-- Anti-Spam Honeypot -->
            <div style="display:none !important;" aria-hidden="true">
                <input type="text" name="website_hp" tabindex="-1" autocomplete="off">
            </div>

            <!-- Full Name -->
            <div class="form-group">
                <label for="consult_full_name" class="form-label">Full Name <span style="color:red;">*</span></label>
                <input type="text" id="consult_full_name" name="full_name" class="form-control" placeholder="e.g. Rahul Sharma" required autocomplete="name">
                <span class="field-error-msg" id="err_consult_name"></span>
            </div>

            <!-- WhatsApp / Phone Number -->
            <div class="form-group">
                <label for="consult_phone_number" class="form-label">WhatsApp / Phone Number <span style="color:red;">*</span></label>
                <div class="phone-input-wrapper">
                    <span class="phone-prefix">+91</span>
                    <input type="tel" id="consult_phone_number" name="phone_number" class="form-control phone-input-field" placeholder="98200 12345" required autocomplete="tel" inputmode="tel" maxlength="10">
                </div>
                <span class="field-error-msg" id="err_consult_phone"></span>
            </div>

            <!-- Email Address -->
            <div class="form-group">
                <label for="consult_email" class="form-label">Email Address <span style="color:red;">*</span></label>
                <input type="email" id="consult_email" name="email" class="form-control" placeholder="rahul@example.com" required autocomplete="email">
                <span class="field-error-msg" id="err_consult_email"></span>
            </div>

            <!-- Describe Your Problem -->
            <div class="form-group">
                <label for="consult_problem" class="form-label">Describe Your Problem <span style="color:red;">*</span></label>
                <textarea id="consult_problem" name="problem" class="form-control" rows="3" placeholder="Tell us about your iPhone model, condition, question, or valuation requirement..." required></textarea>
                <span class="field-error-msg" id="err_consult_problem"></span>
            </div>

            <button type="submit" class="btn btn-primary btn-lg btn-full" id="consult-submit-btn">
                <span>Get Free Consultation &rarr;</span>
            </button>
            
            <p class="consult-trust-note">🔒 Fast response • 100% Free &amp; confidential • No obligation</p>

            <!-- Status Alert Message -->
            <div id="consult-form-status" class="form-status-alert" style="margin-top: 12px; display: none;"></div>
        </form>
    </div>
</div>

<!-- ============================================================
     SMART PHONE EXCHANGE & REAL DEVICE CHECK APPLICATION
     ============================================================ -->
<div class="smart-app-overlay" id="smartExchangeApp" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="smartAppHeading">
    <div class="smart-app-container">
        <!-- App Header & Stepper -->
        <div class="smart-app-header">
            <div class="smart-header-top">
                <div class="smart-brand-tag">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    <span>Smart Exchange Diagnostic Engine</span>
                </div>
                <button type="button" class="smart-app-close-btn" aria-label="Close Smart Exchange">&times;</button>
            </div>
            
            <!-- Progress Bar & Stepper -->
            <div class="smart-progress-track">
                <div class="smart-progress-fill" id="smartProgressFill"></div>
            </div>
            <div class="smart-step-nav">
                <span class="step-node active">1. Device</span>
                <span class="step-node">2. Physical</span>
                <span class="step-node">3. Hardware</span>
                <span class="step-node">4. Multimedia</span>
                <span class="step-node">5. Valuation</span>
            </div>
        </div>

        <!-- Scrollable Viewport -->
        <div class="smart-app-viewport">
            <!-- ================= STEP 1: DEVICE SELECTION ================= -->
            <div class="smart-step-view active" id="smartStep1">
                <div class="step-title-box">
                    <h2 class="step-main-title" id="smartAppHeading">Check Your iPhone Value</h2>
                    <p class="step-subtitle">Select your phone model, storage capacity, and battery health to initialize device checks.</p>
                </div>

                <!-- Brand Tabs -->
                <div>
                    <div class="spec-title-label">Select Brand</div>
                    <div class="brand-tab-group">
                        <button type="button" class="brand-tab-pill active" data-brand="Apple">Apple</button>
                        <button type="button" class="brand-tab-pill" data-brand="Samsung">Samsung</button>
                        <button type="button" class="brand-tab-pill" data-brand="OnePlus">OnePlus</button>
                        <button type="button" class="brand-tab-pill" data-brand="Google">Google</button>
                        <button type="button" class="brand-tab-pill" data-brand="Xiaomi">Xiaomi</button>
                        <button type="button" class="brand-tab-pill" data-brand="Other">Other</button>
                    </div>
                </div>

                <!-- Model Selection Grid -->
                <div>
                    <div class="spec-title-label">Select iPhone Model</div>
                    <div class="device-select-grid">
                        <div class="device-card-opt" data-model="iPhone 16 Pro Max">
                            <span class="device-card-icon">📱</span>
                            <span class="device-card-name">iPhone 16 Pro Max</span>
                        </div>
                        <div class="device-card-opt" data-model="iPhone 16 Pro">
                            <span class="device-card-icon">📱</span>
                            <span class="device-card-name">iPhone 16 Pro</span>
                        </div>
                        <div class="device-card-opt" data-model="iPhone 16">
                            <span class="device-card-icon">📱</span>
                            <span class="device-card-name">iPhone 16</span>
                        </div>
                        <div class="device-card-opt" data-model="iPhone 15 Pro Max">
                            <span class="device-card-icon">📱</span>
                            <span class="device-card-name">iPhone 15 Pro Max</span>
                        </div>
                        <div class="device-card-opt" data-model="iPhone 15 Pro">
                            <span class="device-card-icon">📱</span>
                            <span class="device-card-name">iPhone 15 Pro</span>
                        </div>
                        <div class="device-card-opt" data-model="iPhone 15">
                            <span class="device-card-icon">📱</span>
                            <span class="device-card-name">iPhone 15</span>
                        </div>
                        <div class="device-card-opt" data-model="iPhone 14 Pro Max">
                            <span class="device-card-icon">📱</span>
                            <span class="device-card-name">iPhone 14 Pro Max</span>
                        </div>
                        <div class="device-card-opt" data-model="iPhone 14 Pro">
                            <span class="device-card-icon">📱</span>
                            <span class="device-card-name">iPhone 14 Pro</span>
                        </div>
                        <div class="device-card-opt" data-model="iPhone 14">
                            <span class="device-card-icon">📱</span>
                            <span class="device-card-name">iPhone 14</span>
                        </div>
                        <div class="device-card-opt selected" data-model="iPhone 13 Pro">
                            <span class="device-card-icon">📱</span>
                            <span class="device-card-name">iPhone 13 Pro</span>
                        </div>
                        <div class="device-card-opt" data-model="iPhone 13">
                            <span class="device-card-icon">📱</span>
                            <span class="device-card-name">iPhone 13</span>
                        </div>
                        <div class="device-card-opt" data-model="iPhone 12">
                            <span class="device-card-icon">📱</span>
                            <span class="device-card-name">iPhone 12</span>
                        </div>
                        <div class="device-card-opt" data-model="iPhone 11">
                            <span class="device-card-icon">📱</span>
                            <span class="device-card-name">iPhone 11</span>
                        </div>
                    </div>
                </div>

                <!-- Storage Capacity -->
                <div>
                    <div class="spec-title-label">Storage Capacity</div>
                    <div class="storage-pill-row">
                        <button type="button" class="spec-pill-btn storage-pill-btn" data-storage="64 GB">64 GB</button>
                        <button type="button" class="spec-pill-btn storage-pill-btn selected" data-storage="128 GB">128 GB</button>
                        <button type="button" class="spec-pill-btn storage-pill-btn" data-storage="256 GB">256 GB</button>
                        <button type="button" class="spec-pill-btn storage-pill-btn" data-storage="512 GB">512 GB</button>
                        <button type="button" class="spec-pill-btn storage-pill-btn" data-storage="1 TB">1 TB</button>
                    </div>
                </div>

                <!-- Battery Health -->
                <div>
                    <div class="spec-title-label">Battery Health (Settings &rarr; Battery)</div>
                    <div class="battery-presets-row" style="margin-bottom: 8px;">
                        <button type="button" class="spec-pill-btn battery-preset-btn" data-preset="90%+" data-health="92">90%+</button>
                        <button type="button" class="spec-pill-btn battery-preset-btn selected" data-preset="85–89%" data-health="89">85–89%</button>
                        <button type="button" class="spec-pill-btn battery-preset-btn" data-preset="80–84%" data-health="82">80–84%</button>
                        <button type="button" class="spec-pill-btn battery-preset-btn" data-preset="Below 80%" data-health="76">Below 80%</button>
                    </div>
                    <div class="battery-slider-wrap">
                        <div class="battery-slider-header">
                            <span style="font-size: 0.8125rem; font-weight: 600; color: #636366;">Exact Battery Health:</span>
                            <span class="battery-slider-val" id="smartBatterySliderVal">89%</span>
                        </div>
                        <input type="range" min="50" max="100" value="89" class="battery-slider" id="smartBatterySlider" aria-label="Battery health percentage slider">
                    </div>
                </div>
            </div>

            <!-- ================= STEP 2: PHYSICAL CONDITION ================= -->
            <div class="smart-step-view" id="smartStep2">
                <div class="live-valuation-header-bar">
                    <div>
                        <span style="font-size: 0.6875rem; color: #8E8E93; text-transform: uppercase; font-weight: 700;">Testing</span>
                        <div class="live-val-device">iPhone 13 Pro (128 GB)</div>
                    </div>
                    <div style="text-align: right;">
                        <span style="font-size: 0.6875rem; color: #8E8E93; text-transform: uppercase; font-weight: 700;">Est. Value</span>
                        <div class="live-val-price">₹42,500</div>
                    </div>
                </div>

                <div class="step-title-box">
                    <h2 class="step-main-title">Physical Condition Check</h2>
                    <p class="step-subtitle">Answer these quick questions regarding the external body and cosmetic condition.</p>
                </div>

                <div class="question-card-list" id="physicalQuestionsContainer">
                    <!-- Populated dynamically via smart-exchange.js -->
                </div>
            </div>

            <!-- ================= STEP 3: FUNCTIONAL HARDWARE ================= -->
            <div class="smart-step-view" id="smartStep3">
                <div class="live-valuation-header-bar">
                    <div>
                        <span style="font-size: 0.6875rem; color: #8E8E93; text-transform: uppercase; font-weight: 700;">Testing</span>
                        <div class="live-val-device">iPhone 13 Pro (128 GB)</div>
                    </div>
                    <div style="text-align: right;">
                        <span style="font-size: 0.6875rem; color: #8E8E93; text-transform: uppercase; font-weight: 700;">Est. Value</span>
                        <div class="live-val-price">₹42,500</div>
                    </div>
                </div>

                <div class="step-title-box">
                    <h2 class="step-main-title">Device Functionality Check</h2>
                    <p class="step-subtitle">Verify internal hardware sensors, buttons, charging, and biometric authentication.</p>
                </div>

                <div class="hardware-test-list" id="hardwareTestsContainer">
                    <!-- Populated dynamically via smart-exchange.js -->
                </div>
            </div>

            <!-- ================= STEP 4: MULTIMEDIA & CONNECTIVITY ================= -->
            <div class="smart-step-view" id="smartStep4">
                <div class="live-valuation-header-bar">
                    <div>
                        <span style="font-size: 0.6875rem; color: #8E8E93; text-transform: uppercase; font-weight: 700;">Testing</span>
                        <div class="live-val-device">iPhone 13 Pro (128 GB)</div>
                    </div>
                    <div style="text-align: right;">
                        <span style="font-size: 0.6875rem; color: #8E8E93; text-transform: uppercase; font-weight: 700;">Est. Value</span>
                        <div class="live-val-price">₹42,500</div>
                    </div>
                </div>

                <div class="step-title-box">
                    <h2 class="step-main-title">Multimedia &amp; Connectivity</h2>
                    <p class="step-subtitle">Run interactive camera, microphone, loudspeaker, and pixel screen tests.</p>
                </div>

                <div class="hardware-test-list" id="multimediaTestsContainer">
                    <!-- Populated dynamically via smart-exchange.js -->
                </div>
            </div>

            <!-- ================= STEP 5: FINAL REPORT & PICKUP FORM ================= -->
            <div class="smart-step-view" id="smartStep5">
                <div class="report-hero-card">
                    <span class="report-hero-tag">✓ Diagnostic Verified</span>
                    <h3 class="report-hero-device" id="reportFinalDeviceName">Apple iPhone 13 Pro</h3>
                    <p class="report-hero-specs" id="reportFinalSpecs">128 GB • Battery Health: 89%</p>
                    
                    <span class="report-hero-val-label">Guaranteed Estimated Exchange Value</span>
                    <div class="report-hero-price" id="reportFinalPrice">₹42,500</div>

                    <div class="report-stat-pills">
                        <span class="report-stat-pill passed" id="reportFinalPassedCount">26 Passed</span>
                        <span class="report-stat-pill failed" id="reportFinalFailedCount">0 Failed</span>
                    </div>
                </div>

                <!-- Category Breakdown Accordion -->
                <div class="report-breakdown-section">
                    <div class="report-breakdown-title">Comprehensive Hardware Summary (Click to expand)</div>
                    <div id="reportBreakdownList">
                        <!-- Populated dynamically via smart-exchange.js -->
                    </div>
                </div>

                <!-- Free Doorstep Pickup Lead Form -->
                <div class="lead-capture-card" id="smartExchangeFormView">
                    <h4 style="font-size: 1.0625rem; font-weight: 800; color: #1C1C1E; margin: 0 0 4px 0;">Request Free Doorstep Inspection &amp; Instant Cash Pickup</h4>
                    <p style="font-size: 0.8125rem; color: #636366; margin: 0 0 14px 0;">Lock in your estimated value. Our verified technician will visit your location in Mumbai with spot payment.</p>

                    <form id="smartExchangeLeadForm" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                        <input type="text" name="website_hp" style="display:none !important;" tabindex="-1" autocomplete="off">

                        <div class="lead-form-grid">
                            <div class="lead-input-wrap">
                                <label class="lead-label" for="smart_lead_name">Full Name <span style="color:red;">*</span></label>
                                <input type="text" id="smart_lead_name" name="full_name" class="lead-input" placeholder="e.g. Rahul Sharma" required>
                            </div>

                            <div class="lead-input-wrap">
                                <label class="lead-label" for="smart_lead_phone">WhatsApp / Mobile Number <span style="color:red;">*</span></label>
                                <input type="tel" id="smart_lead_phone" name="phone_number" class="lead-input" placeholder="e.g. 9876543210" pattern="[0-9]{10}" required>
                            </div>

                            <div class="lead-input-wrap">
                                <label class="lead-label" for="smart_lead_email">Email Address</label>
                                <input type="email" id="smart_lead_email" name="email" class="lead-input" placeholder="e.g. rahul@example.com">
                            </div>

                            <div class="lead-input-wrap">
                                <label class="lead-label" for="smart_lead_loc">Pickup Location / Area in Mumbai <span style="color:red;">*</span></label>
                                <input type="text" id="smart_lead_loc" name="location" class="lead-input" placeholder="e.g. Bandra West, Nariman Point, Andheri..." required>
                            </div>

                            <div class="lead-input-wrap">
                                <label class="lead-label" for="smart_pickup_slot">Preferred Pickup Slot</label>
                                <select id="smart_pickup_slot" name="pickup_slot" class="lead-input">
                                    <option value="Today - Express (Within 2 Hours)">⚡ Today - Express (Within 2 Hours)</option>
                                    <option value="Today - Afternoon (2:00 PM - 5:00 PM)">Today - Afternoon (2:00 PM - 5:00 PM)</option>
                                    <option value="Today - Evening (6:00 PM - 9:00 PM)">Today - Evening (6:00 PM - 9:00 PM)</option>
                                    <option value="Tomorrow - Morning (10:00 AM - 1:00 PM)">Tomorrow - Morning (10:00 AM - 1:00 PM)</option>
                                </select>
                            </div>
                        </div>

                        <div id="smartExchangeFormStatus" style="display: none;"></div>

                        <div style="margin-top: 14px; display: flex; flex-direction: column; gap: 8px;">
                            <button type="submit" class="btn-nav-next" id="smartExchangeSubmitBtn" style="width: 100%; padding: 14px;">
                                <span>Request Free Pickup &rarr;</span>
                            </button>

                            <a href="https://wa.me/918976332211" target="_blank" rel="noopener noreferrer" class="btn btn-whatsapp btn-full" id="reportWhatsAppBtn" style="padding: 12px; text-align: center; justify-content: center;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
                                <span>Chat on WhatsApp &rarr;</span>
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Booking Success View -->
                <div id="smartExchangeSuccessView" style="display:none; flex-direction:column; align-items:center; text-align:center; padding:24px 16px; background:#F2F2F7; border-radius:18px; margin-top:14px;">
                    <div style="width:54px; height:54px; border-radius:50%; background:#34C759; color:#FFFFFF; display:flex; align-items:center; justify-content:center; font-size:1.75rem; margin-bottom:12px;">✓</div>
                    <h4 style="font-size:1.25rem; font-weight:800; color:#1C1C1E; margin:0 0 4px 0;">Pickup Scheduled Successfully!</h4>
                    <p style="font-size:0.875rem; color:#636366; margin:0 0 10px 0;">Booking Reference ID: <strong id="smartBookingRefId" style="color:#0071E3;">CS-EX-88910</strong></p>
                    <p style="font-size:0.8125rem; color:#8E8E93; line-height:1.4;">Our executive will contact you shortly to confirm doorstep pickup and instant payment transfer.</p>
                </div>
            </div>
        </div>

        <!-- Real Interactive Hardware Submodals -->
        <!-- 1. Microphone Test Submodal -->
        <div class="interactive-submodal" id="micTestSubmodal">
            <div style="font-size: 2.5rem; margin-bottom: 8px;">🎙️</div>
            <div class="submodal-title">Microphone Live Audio Test</div>
            <p class="submodal-desc" id="micStatusText">Speak or tap the microphone to test live audio capture level.</p>
            <div class="audio-meter-bar"><div class="audio-meter-fill" id="micMeterFill"></div></div>
            <div class="submodal-actions">
                <button type="button" class="btn-nav-next" id="micPassBtn" style="background:#34C759;">✓ Mic Working</button>
                <button type="button" class="choice-btn no" id="micFailBtn" style="color:#FF3B30;">✕ Mic Issue</button>
            </div>
        </div>

        <!-- 2. Camera Test Submodal -->
        <div class="interactive-submodal" id="cameraTestSubmodal">
            <div class="submodal-title" id="cameraSubmodalTitle">Camera Verification Test</div>
            <p class="submodal-desc">Check the live camera preview for clear focus, blur, or spots.</p>
            <video class="camera-preview-video" id="cameraPreviewVideo" playsinline autoplay muted></video>
            <div class="submodal-actions">
                <button type="button" class="btn-nav-next" id="cameraPassBtn" style="background:#34C759;">✓ Camera OK</button>
                <button type="button" class="choice-btn no" id="cameraFailBtn" style="color:#FF3B30;">✕ Camera Issue</button>
            </div>
        </div>

        <!-- 3. Full-Screen Pixel Check Canvas -->
        <div class="pixel-test-canvas-view" id="pixelTestCanvasView">
            <div class="pixel-test-overlay-controls">
                <span>Tap screen to cycle colors (White, Red, Green, Blue, Black)</span>
                <button type="button" class="choice-btn yes" id="pixelPassBtn" style="background:#34C759; color:#FFF; border:none;">✓ Display OK</button>
                <button type="button" class="choice-btn no" id="pixelFailBtn" style="background:#FF3B30; color:#FFF; border:none;">✕ Pixel Issue</button>
            </div>
        </div>

        <!-- App Footer Nav Bar -->
        <div class="smart-app-footer">
            <button type="button" class="btn-nav-back" id="smartAppBackBtn">← Back</button>
            <button type="button" class="btn-nav-next" id="smartAppNextBtn">
                <span>Continue to Next Step &rarr;</span>
            </button>
        </div>
    </div>
</div>

<!-- ============================================================
     COMPLETE PHONE BUYBACK QUESTIONNAIRE APPLICATION
     ============================================================ -->
<div class="qn-app-overlay" id="buybackQuestionnaireModal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="qnStepTrackerText">
    <div class="qn-app-modal">
        <!-- Questionnaire Header & Stepper -->
        <div class="qn-app-header">
            <div class="qn-header-top">
                <div class="qn-device-badge">
                    <span>📱</span>
                    <span id="qnHeaderDeviceBadge">Apple iPhone 13 (128 GB)</span>
                </div>
                <button type="button" class="qn-app-close-btn" id="qnAppCloseBtn" aria-label="Close Questionnaire">&times;</button>
            </div>
            
            <!-- Progress Bar -->
            <div class="qn-progress-track">
                <div class="qn-progress-fill" id="qnProgressFill"></div>
            </div>
            <div class="qn-step-tracker-text">
                <span id="qnStepTrackerText">Step 1 of 17</span>
                <span class="qn-step-path-trail" id="qnStepTrailText">Phone &rarr; Display &rarr; Body &rarr; Battery &rarr; Accessories</span>
            </div>
        </div>

        <!-- Question Dynamic Content Viewport -->
        <div class="qn-app-body" id="qnAppBody">
            <!-- Populated one question at a time via questionnaire.js -->
        </div>

        <!-- Questionnaire Footer Navigation -->
        <div class="qn-app-footer" id="qnAppFooter">
            <button type="button" class="btn-qn-back" id="qnBackBtn">&larr; Back</button>
            <button type="button" class="btn-qn-next" id="qnNextBtn">
                <span>Next Question &rarr;</span>
            </button>
        </div>
    </div>
</div>

<script>
    window.allIphoneCatalog = <?= json_encode($iphoneModels, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    window.csrfToken = <?= json_encode($_SESSION['csrf_token'] ?? '') ?>;
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
