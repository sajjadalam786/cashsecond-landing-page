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
                        fetchpriority="high"
                        decoding="async"
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
                <a href="#valuation" class="btn promo-banner-cta btn-promo-dark" id="heroCheckValueBtn" aria-haspopup="dialog" aria-controls="buybackQuestionnaireModal">
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
                <a href="#valuation" class="btn promo-banner-cta btn-promo-light" id="transparentValuationBtn" aria-haspopup="dialog" aria-controls="buybackQuestionnaireModal">
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
