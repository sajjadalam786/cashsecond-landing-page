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
$faqCategories = $config['faq_categories'] ?? [
    'valuation' => 'Valuation & Pricing',
    'pickup'    => 'Doorstep Pickup',
    'payment'   => 'Instant Payments',
    'security'  => 'Data Security & Prep'
];

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
     1. HERO SECTION — PREMIUM APPLE-INSPIRED PRODUCT PRESENTATION
     ============================================================ -->
<section class="hero-section" id="promo-hero">
    <div class="container hero-container">
        <!-- 1A. Full-Width Panoramic Visual Hero Banner with Responsive Mobile/Desktop Images -->
        <div class="hero-panoramic-banner">
            <picture class="hero-banner-picture">
                <source media="(max-width: 767px)" srcset="assets/images/banners/mobile/sell-your-iphone-with-cashsecond-for-mobile-view.webp">
                <img 
                    src="assets/images/banners/desktop/sell-your-iphone-with-cashsecond.webp" 
                    alt="Sell Your Old iPhone From Your Doorstep with CashSecond" 
                    class="hero-panoramic-bg" 
                    width="1280" 
                    height="560" 
                    loading="eager"
                    fetchpriority="high"
                    decoding="async"
                >
            </picture>
            <div class="hero-banner-overlay">
                <h1 class="hero-main-title">Sell Your Old iPhone From Your Doorstep</h1>
                <p class="hero-main-subtitle">Get an instant valuation, free doorstep pickup in Mumbai, secure data wipe, and spot payment.</p>
                <div class="hero-banner-cta-wrap" style="margin-top: 18px;">
                    <a href="#valuation" class="btn btn-primary btn-lg start-exact-valuation-btn" id="startExactValuationBtn" aria-haspopup="dialog" aria-controls="buybackQuestionnaireModal">
                        <svg class="btn-click-icon" width="20" height="23" viewBox="0 0 24 28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
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
                        <img src="assets/images/iphone-value-check-button.png" alt="iPhone" class="btn-iphone-thumb" width="22" height="38" loading="eager">
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     SECTION 1B: CERTIFIED iPHONE BUYBACK ADVANTAGES (8 BENTO PILLARS)
     ============================================================ -->
<section class="trust-pillars-section" id="buyback-advantages" aria-label="Why Sell Your iPhone to CashSecond">
    <div class="container trust-pillars-container">
        <div class="trust-pillars-header text-center">
            
            <h2 class="pillars-section-title">The Smarter, Safer Way to Sell Your iPhone</h2>
            <p class="pillars-section-subtitle">Mumbai's top-rated doorstep buyback network engineered for speed, transparency, and top value.</p>
        </div>

        <div class="trust-bento-grid">
            <!-- 1. Certified iPhone Buyer -->
            <div class="bento-pillar-card">
                <div class="bento-pillar-icon-box icon-blue">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                </div>
                <div class="bento-pillar-content">
                    <h3 class="bento-pillar-title">Certified iPhone Buyer</h3>
                    <p class="bento-pillar-desc">GST registered &amp; officially verified pre-owned device platform.</p>
                </div>
            </div>

            <!-- 2. Instant iPhone Valuation -->
            <div class="bento-pillar-card">
                <div class="bento-pillar-icon-box icon-cyan">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                    </svg>
                </div>
                <div class="bento-pillar-content">
                    <h3 class="bento-pillar-title">Instant iPhone Valuation</h3>
                    <p class="bento-pillar-desc">Transparent diagnostic engine calculating exact live market quotes.</p>
                </div>
            </div>

            <!-- 3. Best Market Price -->
            <div class="bento-pillar-card">
                <div class="bento-pillar-icon-box icon-green">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <line x1="12" y1="1" x2="12" y2="23"/>
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                    </svg>
                </div>
                <div class="bento-pillar-content">
                    <h3 class="bento-pillar-title">Best Market Price</h3>
                    <p class="bento-pillar-desc">Up to 15% higher resale valuation with zero hidden deductions.</p>
                </div>
            </div>

            <!-- 4. Free Doorstep Pickup -->
            <div class="bento-pillar-card">
                <div class="bento-pillar-icon-box icon-indigo">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="1" y="3" width="15" height="13"/>
                        <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/>
                        <circle cx="5.5" cy="18.5" r="2.5"/>
                        <circle cx="18.5" cy="18.5" r="2.5"/>
                    </svg>
                </div>
                <div class="bento-pillar-content">
                    <h3 class="bento-pillar-title">Free Doorstep Pickup</h3>
                    <p class="bento-pillar-desc">Express doorstep inspection across all Mumbai pin codes.</p>
                </div>
            </div>

            <!-- 5. Secure Data Protection -->
            <div class="bento-pillar-card">
                <div class="bento-pillar-icon-box icon-purple">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                </div>
                <div class="bento-pillar-content">
                    <h3 class="bento-pillar-title">Secure Data Protection</h3>
                    <p class="bento-pillar-desc">DoD-grade certified on-spot data wipe protecting complete privacy.</p>
                </div>
            </div>

            <!-- 6. Fast & Secure Payment -->
            <div class="bento-pillar-card">
                <div class="bento-pillar-icon-box icon-amber">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                        <line x1="1" y1="10" x2="23" y2="10"/>
                    </svg>
                </div>
                <div class="bento-pillar-content">
                    <h3 class="bento-pillar-title">Fast &amp; Secure Payment</h3>
                    <p class="bento-pillar-desc">Direct bank UPI, IMPS transfer, or spot cash upon device handover.</p>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ============================================================
     2. iPHONE MODELS HORIZONTAL SCROLLING STRIP (AUTO-SCROLL MARQUEE)
     ============================================================ -->
<div class="iphone-strip-section" id="iphone-models" aria-label="Featured iPhone Models for Resale">
    <div class="iphone-strip-wrapper">
        <div class="iphone-strip-track">
            <!-- Sell iPhone 16 Pro Max -->
            <div class="iphone-pill-card">
                <div class="iphone-pill-img-wrap">
                    <img src="assets/images/phones/iphone-16-pro.svg" alt="Apple iPhone 16 Pro Max" class="iphone-pill-img" width="32" height="42" loading="lazy">
                </div>
                <span class="iphone-pill-title">Sell iPhone 16 Pro Max</span>
            </div>

            <!-- iPhone 16 Pro Resale -->
            <div class="iphone-pill-card">
                <div class="iphone-pill-img-wrap">
                    <img src="assets/images/phones/iphone-16-pro.svg" alt="iPhone 16 Pro resale" class="iphone-pill-img" width="32" height="42" loading="lazy">
                </div>
                <span class="iphone-pill-title">iPhone 16 Pro Resale</span>
            </div>

            <!-- Sell iPhone 16 -->
            <div class="iphone-pill-card">
                <div class="iphone-pill-img-wrap">
                    <img src="assets/images/phones/iphone-16.svg" alt="Sell Apple iPhone 16" class="iphone-pill-img" width="32" height="42" loading="lazy">
                </div>
                <span class="iphone-pill-title">Sell iPhone 16</span>
            </div>

            <!-- iPhone 15 Pro Max -->
            <div class="iphone-pill-card">
                <div class="iphone-pill-img-wrap">
                    <img src="assets/images/phones/iphone-15-pro.svg" alt="iPhone 15 Pro Max valuation" class="iphone-pill-img" width="32" height="42" loading="lazy">
                </div>
                <span class="iphone-pill-title">iPhone 15 Pro Max Value</span>
            </div>

            <!-- iPhone 15 Pro -->
            <div class="iphone-pill-card">
                <div class="iphone-pill-img-wrap">
                    <img src="assets/images/phones/iphone-15-pro.svg" alt="iPhone 15 Pro resale" class="iphone-pill-img" width="32" height="42" loading="lazy">
                </div>
                <span class="iphone-pill-title">iPhone 15 Pro Resale</span>
            </div>

            <!-- Sell iPhone 15 -->
            <div class="iphone-pill-card">
                <div class="iphone-pill-img-wrap">
                    <img src="assets/images/phones/iphone-15.svg" alt="Sell Apple iPhone 15" class="iphone-pill-img" width="32" height="42" loading="lazy">
                </div>
                <span class="iphone-pill-title">Sell iPhone 15</span>
            </div>

            <!-- iPhone 14 Pro -->
            <div class="iphone-pill-card">
                <div class="iphone-pill-img-wrap">
                    <img src="assets/images/phones/iphone-14-pro.svg" alt="iPhone 14 Pro buyback" class="iphone-pill-img" width="32" height="42" loading="lazy">
                </div>
                <span class="iphone-pill-title">iPhone 14 Pro Buyback</span>
            </div>

            <!-- Sell iPhone 14 -->
            <div class="iphone-pill-card">
                <div class="iphone-pill-img-wrap">
                    <img src="assets/images/phones/iphone-14.svg" alt="Sell Apple iPhone 14" class="iphone-pill-img" width="32" height="42" loading="lazy">
                </div>
                <span class="iphone-pill-title">Sell iPhone 14</span>
            </div>

            <!-- Sell iPhone 13 -->
            <div class="iphone-pill-card">
                <div class="iphone-pill-img-wrap">
                    <img src="assets/images/phones/iphone-13.svg" alt="Sell Apple iPhone 13" class="iphone-pill-img" width="32" height="42" loading="lazy">
                </div>
                <span class="iphone-pill-title">Sell iPhone 13</span>
            </div>
        </div>

        <!-- Exact Duplicate Track for 100% Seamless Infinite Loop -->
        <div class="iphone-strip-track" aria-hidden="true">
            <div class="iphone-pill-card">
                <div class="iphone-pill-img-wrap">
                    <img src="assets/images/phones/iphone-16-pro.svg" alt="Apple iPhone 16 Pro Max" class="iphone-pill-img" width="32" height="42" loading="lazy">
                </div>
                <span class="iphone-pill-title">Sell iPhone 16 Pro Max</span>
            </div>
            <div class="iphone-pill-card">
                <div class="iphone-pill-img-wrap">
                    <img src="assets/images/phones/iphone-16-pro.svg" alt="iPhone 16 Pro resale" class="iphone-pill-img" width="32" height="42" loading="lazy">
                </div>
                <span class="iphone-pill-title">iPhone 16 Pro Resale</span>
            </div>
            <div class="iphone-pill-card">
                <div class="iphone-pill-img-wrap">
                    <img src="assets/images/phones/iphone-16.svg" alt="Sell Apple iPhone 16" class="iphone-pill-img" width="32" height="42" loading="lazy">
                </div>
                <span class="iphone-pill-title">Sell iPhone 16</span>
            </div>
            <div class="iphone-pill-card">
                <div class="iphone-pill-img-wrap">
                    <img src="assets/images/phones/iphone-15-pro.svg" alt="iPhone 15 Pro Max valuation" class="iphone-pill-img" width="32" height="42" loading="lazy">
                </div>
                <span class="iphone-pill-title">iPhone 15 Pro Max Value</span>
            </div>
            <div class="iphone-pill-card">
                <div class="iphone-pill-img-wrap">
                    <img src="assets/images/phones/iphone-15-pro.svg" alt="iPhone 15 Pro resale" class="iphone-pill-img" width="32" height="42" loading="lazy">
                </div>
                <span class="iphone-pill-title">iPhone 15 Pro Resale</span>
            </div>
            <div class="iphone-pill-card">
                <div class="iphone-pill-img-wrap">
                    <img src="assets/images/phones/iphone-15.svg" alt="Sell Apple iPhone 15" class="iphone-pill-img" width="32" height="42" loading="lazy">
                </div>
                <span class="iphone-pill-title">Sell iPhone 15</span>
            </div>
            <div class="iphone-pill-card">
                <div class="iphone-pill-img-wrap">
                    <img src="assets/images/phones/iphone-14-pro.svg" alt="iPhone 14 Pro buyback" class="iphone-pill-img" width="32" height="42" loading="lazy">
                </div>
                <span class="iphone-pill-title">iPhone 14 Pro Buyback</span>
            </div>
            <div class="iphone-pill-card">
                <div class="iphone-pill-img-wrap">
                    <img src="assets/images/phones/iphone-14.svg" alt="Sell Apple iPhone 14" class="iphone-pill-img" width="32" height="42" loading="lazy">
                </div>
                <span class="iphone-pill-title">Sell iPhone 14</span>
            </div>
            <div class="iphone-pill-card">
                <div class="iphone-pill-img-wrap">
                    <img src="assets/images/phones/iphone-13.svg" alt="Sell Apple iPhone 13" class="iphone-pill-img" width="32" height="42" loading="lazy">
                </div>
                <span class="iphone-pill-title">Sell iPhone 13</span>
            </div>
        </div>
    </div>
</div>





<!-- ============================================================
     4. HOW IT WORKS (4 SIMPLE STEPS)
     ============================================================ -->
<section class="section-container how-it-works-section" id="how-it-works">
    <div class="container">
        <div class="section-header text-center">
            <span class="section-eyebrow">HOW IT WORKS</span>
            <h2 class="section-title">Sell Your iPhone in 4 Simple Steps</h2>
            <p class="section-subtitle">Experience a fast, transparent, and secure doorstep selling journey built exclusively for Apple users.</p>
        </div>

        <div class="how-it-works-4grid">
            <!-- Step 01: Model Selection -->
            <div class="how-step-card-4">
                <div class="how-step-badge">01</div>    
                <div class="how-step-icon-wrap" aria-hidden="true">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect>
                        <line x1="12" y1="18" x2="12.01" y2="18"></line>
                    </svg>
                </div>
                <h3 class="how-step-heading">Select iPhone</h3>
                <p class="how-step-text">Choose your exact model and storage capacity on our valuation calculator.</p>
            </div>

            <!-- Step 02: Instant Quote -->
            <div class="how-step-card-4">
                <div class="how-step-badge">02</div>
                <div class="how-step-icon-wrap" aria-hidden="true">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="4" y="3" width="16" height="18" rx="2"></rect>
                        <line x1="8" y1="7" x2="16" y2="7"></line>
                        <line x1="8" y1="11" x2="10" y2="11"></line>
                        <line x1="14" y1="11" x2="16" y2="11"></line>
                        <line x1="8" y1="15" x2="10" y2="15"></line>
                        <line x1="14" y1="15" x2="16" y2="15"></line>
                    </svg>
                </div>
                <h3 class="how-step-heading">Get Valuation</h3>
                <p class="how-step-text">Answer simple condition questions to get a fair, market-accurate online quote.</p>
            </div>

            <!-- Step 03: Doorstep Pickup -->
            <div class="how-step-card-4">
                <div class="how-step-badge">03</div>
                <div class="how-step-icon-wrap" aria-hidden="true">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                        <polyline points="9 16 11 18 15 14"></polyline>
                    </svg>
                </div>
                <h3 class="how-step-heading">Schedule Pickup</h3>
                <p class="how-step-text">Select your preferred date, time slot, and doorstep address anywhere in Mumbai.</p>
            </div>

            <!-- Step 04: Spot Payment & Wipe -->
            <div class="how-step-card-4">
                <div class="how-step-badge">04</div>
                <div class="how-step-icon-wrap" aria-hidden="true">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                        <polyline points="9 12 11 14 15 10"></polyline>
                    </svg>
                </div>
                <h3 class="how-step-heading">Paid Instantly Online</h3>
                <p class="how-step-text">5-min doorstep diagnostic, instant UPI / cash payout, and certified data wipe.</p>
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
                <span class="promo-banner-eyebrow">READY TO SELL YOUR USED OR OLD IPHONE ?</span>
                <h3 class="promo-banner-title">Your iPhone Deserves a Better Value.</h3>
                <p class="promo-banner-desc">Check your iPhone's value in seconds and get a hassle-free pickup.</p>
                <a href="#valuation" class="btn promo-banner-cta btn-promo-dark" id="heroCheckValueBtn" aria-haspopup="dialog" aria-controls="buybackQuestionnaireModal">
                    <svg class="btn-click-icon" width="20" height="23" viewBox="0 0 24 28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
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
                    <img src="assets/images/iphone-value-check-button.png" alt="iPhone" class="btn-iphone-thumb" width="22" height="38" loading="lazy">
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
     8. FAQ ACCORDION SECTION (20 Categorized AEO & SEO Questions)
     ============================================================ -->
<section class="section-container faq-section-wrapper" id="faq">
    <div class="container">
        <div class="section-header text-center faq-header">
            <span class="section-eyebrow">Got Questions?</span>
            <h2 class="section-title">Frequently Asked Questions</h2>
            <p class="section-subtitle">Short, transparent answers to help you sell your old iPhone with total confidence.</p>
        </div>

        <!-- Category Switching Buttons (No 'All' Category) -->
        <div class="faq-category-nav-wrapper">
            <div class="faq-category-nav" role="tablist" aria-label="FAQ Categories">
                <?php $catIdx = 0; foreach ($faqCategories as $catKey => $catName): ?>
                <button type="button" 
                        class="faq-category-btn <?= $catIdx === 0 ? 'active' : '' ?>" 
                        data-category="<?= htmlspecialchars($catKey) ?>" 
                        role="tab" 
                        aria-selected="<?= $catIdx === 0 ? 'true' : 'false' ?>">
                    <span><?= htmlspecialchars($catName) ?></span>
                </button>
                <?php $catIdx++; endforeach; ?>
            </div>
        </div>

        <!-- Categorized FAQ Accordion List -->
        <div class="faq-accordion-wrap">
            <div class="faq-accordion" id="faqAccordion">
                <?php foreach ($faqs as $idx => $faq): ?>
                <div class="faq-item" 
                     data-category="<?= htmlspecialchars($faq['category'] ?? 'valuation') ?>" 
                     style="<?= ($faq['category'] ?? 'valuation') === 'valuation' ? '' : 'display: none;' ?>">
                    <button type="button" class="faq-btn" aria-expanded="false">
                        <span class="faq-question-text"><?= htmlspecialchars($faq['q']) ?></span>
                        <span class="faq-icon" aria-hidden="true">+</span>
                    </button>
                    <div class="faq-content">
                        <p><?= htmlspecialchars($faq['a']) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     11. FINAL DARK CTA & BOOKING FORM
     ============================================================ -->
                        
<!-- ============================================================
     12. STORE CONTACT & CORPORATE LOCATION STRIP (Compact & Simple)
     ============================================================ -->
<section class="section-container contact-strip-section" id="contact">
    <div class="container">
        <div class="contact-single-strip">
            <!-- Left: Office Details & GST Badge -->
            <div class="contact-strip-info">
                <div class="contact-strip-header-row">
                    <span class="contact-gst-badge">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>
                        <span>GST Registered &amp; Verified</span>
                    </span>
                    
                </div>

                <h3 class="contact-strip-title">Corporate Office &amp; Verification Hub</h3>
                <p class="contact-strip-address">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>
                    </svg>
                    <span><strong>CashSecond</strong> &bull; Arcadia Bldg, NCPA Marg, Nariman Point, Mumbai, Maharashtra 400021</span>
                </p>

                <div class="contact-strip-footer-links">
                    <a href="mailto:cashsecondofficial@gmail.com" class="contact-strip-action-link">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        <span>cashsecondofficial@gmail.com</span>
                    </a>
                    
                </div>
            </div>

            <!-- Right: Cute Compact Map Visual -->
            <div class="contact-strip-map-wrap">
                <img 
                    src="assets/images/location.jpg" 
                    alt="CashSecond Location - Nariman Point Mumbai" 
                    class="contact-strip-map-img" 
                    width="260" 
                    height="160" 
                    loading="lazy"
                >
                <span class="contact-map-pin-pill">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    <span>Nariman Point</span>
                </span>
            </div>
        </div>
    </div>
</section>




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
