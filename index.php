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
                    alt="Sell Old iPhone From Your Doorstep with CashSecond" 
                    class="hero-panoramic-bg" 
                    width="1280" 
                    height="560" 
                    loading="eager"
                    fetchpriority="high"
                    decoding="async"
                >
            </picture>
            <div class="hero-banner-overlay">
                <h1 class="hero-main-title">Sell Old iPhone From Your Doorstep</h1>
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

            <!-- 7. Used iPhone Trade-In -->
            <div class="bento-pillar-card">
                <div class="bento-pillar-icon-box icon-teal">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <polyline points="23 4 23 10 17 10"/>
                        <polyline points="1 20 1 14 7 14"/>
                        <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>
                    </svg>
                </div>
                <div class="bento-pillar-content">
                    <h3 class="bento-pillar-title">Used iPhone Trade-In</h3>
                    <p class="bento-pillar-desc">Maximize your resale value toward your next iPhone upgrade.</p>
                </div>
            </div>

            <!-- 8. Hassle-Free iPhone Buyback -->
            <div class="bento-pillar-card">
                <div class="bento-pillar-icon-box icon-emerald">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                </div>
                <div class="bento-pillar-content">
                    <h3 class="bento-pillar-title">Hassle-Free Buyback</h3>
                    <p class="bento-pillar-desc">Zero haggling, zero paperwork delays, and certified peace of mind.</p>
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
                        Arcadia Bldg, NCPA Marg, Nariman Point, Mumbai 400021
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
