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

// Include Header & Top Marquee
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
                
                <p class="hero-main-subtitle">Get an instant estimate, schedule doorstep pickup, and get paid quickly.</p>

                <!-- Compact Above-the-Fold Trust Strip -->
                <div class="hero-trust-strip" aria-label="Key Service Benefits">
                    <span class="hero-trust-pill">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        Instant Valuation
                    </span>
                    <span class="hero-trust-pill">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        Doorstep Pickup
                    </span>
                    <span class="hero-trust-pill">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        Secure Data Wipe
                    </span>
                    <span class="hero-trust-pill">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        Fast Payment
                    </span>
                </div>

                <!-- Primary Conversion CTA -->
                <div class="hero-cta-group">
                    <a href="#valuation" class="btn btn-primary btn-hero-cta" id="hero-main-cta-btn">
                        <span>CHECK YOUR iPHONE VALUE &rarr;</span>
                    </a>
                    <p class="hero-cta-subtext">Free • Takes less than 60 seconds • No obligation</p>
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
     3. IPHONE VALUATION CALCULATOR (Cashify-Style Selling Flow)
     ============================================================ -->
<section class="valuation-section" id="valuation">
    <div class="container">
        <div class="valuation-card" id="valuation-wizard-card">
            <!-- Header -->
            <div class="valuation-header">
                <span class="section-eyebrow">VALUATION CALCULATOR</span>
                <h2>Get Your iPhone's Estimated Value</h2>
                <p>Select your iPhone model to see how much your device could be worth.</p>
            </div>

            <!-- Stepper Indicators -->
            <div class="valuation-stepper">
                <div class="step-node active" data-step="1">
                    <span class="step-num">1</span>
                    <span>Model</span>
                </div>
                <div class="step-node" data-step="2">
                    <span class="step-num">2</span>
                    <span>Storage</span>
                </div>
                <div class="step-node" data-step="3">
                    <span class="step-num">3</span>
                    <span>Condition</span>
                </div>
                <div class="step-node" data-step="4">
                    <span class="step-num">4</span>
                    <span>Estimate</span>
                </div>
            </div>

            <div class="valuation-body">
                <!-- STEP 1: MODEL SELECTION -->
                <div class="wizard-panel active" id="wizard-step-1">
                    <h3 class="wizard-panel-title">1. Select Your iPhone Model</h3>
                    <p class="wizard-panel-sub">Choose from <?= count($iphoneModels) ?> verified Apple iPhone models:</p>

                    <!-- Generation Tabs -->
                    <div class="gen-tabs-wrapper" id="generation-tabs-row">
                        <button type="button" class="gen-tab-btn active" data-series="all">All Models</button>
                        <button type="button" class="gen-tab-btn" data-series="17">iPhone 17</button>
                        <button type="button" class="gen-tab-btn" data-series="16">iPhone 16</button>
                        <button type="button" class="gen-tab-btn" data-series="15">iPhone 15</button>
                        <button type="button" class="gen-tab-btn" data-series="14">iPhone 14</button>
                        <button type="button" class="gen-tab-btn" data-series="13">iPhone 13</button>
                        <button type="button" class="gen-tab-btn" data-series="12">iPhone 12</button>
                        <button type="button" class="gen-tab-btn" data-series="11">iPhone 11</button>
                        <button type="button" class="gen-tab-btn" data-series="older">X / Older</button>
                    </div>

                    <!-- Search Filter Input -->
                    <div class="model-search-box">
                        <svg class="search-icon-left" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        <input type="text" id="model-filter-input" class="form-control model-search-input" placeholder="Search iPhone Models">
                    </div>

                    <!-- Model Product Cards Grid -->
                    <div class="models-grid-list" id="models-list-container">
                        <?php foreach ($iphoneModels as $model): 
                            $imgSrc = htmlspecialchars($model['image'] ?? 'assets/images/phones/iphone-15.svg');
                            $prodName = htmlspecialchars($model['product_name']);
                            $seoName = htmlspecialchars($model['seo_name'] ?? $model['product_name']);
                            $prodId = htmlspecialchars($model['product_id']);
                            $series = htmlspecialchars($model['series'] ?? 'older');
                            $altText = htmlspecialchars($model['alt_text'] ?? ($seoName . ' valuation'));
                            $keywords = htmlspecialchars($model['keywords'] ?? 'sell resale buyback price value');
                        ?>
                        <div class="model-product-card" data-name="<?= $prodName ?>" data-seo-name="<?= $seoName ?>" data-id="<?= $prodId ?>" data-image="<?= $imgSrc ?>" data-series="<?= $series ?>" data-keywords="<?= $keywords ?>">
                            <div class="model-product-img-wrap">
                                <img src="<?= $imgSrc ?>" alt="<?= $altText ?>" class="model-product-img" loading="lazy" width="80" height="80">
                            </div>
                            <h4 class="model-product-name"><?= $seoName ?></h4>
                            <span class="model-product-cta">Check Value &rarr;</span>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Model Filter Empty State -->
                    <div id="model-filter-empty" class="search-empty-state" style="display: none;">
                        <h4 class="search-empty-title">No iPhone model found</h4>
                        <p class="search-empty-sub">Try searching by model name, such as iPhone 16, iPhone 15 Pro, or iPhone 14.</p>
                    </div>
                </div>

                <!-- STEP 2: STORAGE CAPACITY SELECTION -->
                <div class="wizard-panel" id="wizard-step-2">
                    <h3 class="wizard-panel-title">2. Select Storage Capacity</h3>
                    <p class="wizard-panel-sub" id="step2-selected-model-title">Selected Model: Apple iPhone 16 Pro</p>

                    <div class="variant-chips-grid" id="storage-chips-container">
                        <div class="variant-chip active" data-storage="128GB">
                            <div class="variant-storage">128 GB</div>
                            <div class="variant-ram">Standard Capacity</div>
                        </div>
                        <div class="variant-chip" data-storage="256GB">
                            <div class="variant-storage">256 GB</div>
                            <div class="variant-ram">High Capacity</div>
                        </div>
                        <div class="variant-chip" data-storage="512GB">
                            <div class="variant-storage">512 GB</div>
                            <div class="variant-ram">Pro Storage</div>
                        </div>
                        <div class="variant-chip" data-storage="1TB">
                            <div class="variant-storage">1 TB</div>
                            <div class="variant-ram">Maximum Storage</div>
                        </div>
                    </div>

                    <div class="wizard-nav-btns">
                        <button type="button" class="btn-back-step" id="btn-back-to-step-1">&larr; Change Model</button>
                        <button type="button" class="btn btn-primary" id="btn-proceed-to-step-3">Continue to Condition &rarr;</button>
                    </div>
                </div>

                <!-- STEP 3: INTERACTIVE CONDITION CARDS & FUNCTIONAL CHECK -->
                <div class="wizard-panel" id="wizard-step-3">
                    <h3 class="wizard-panel-title">3. Select Physical & Functional Condition</h3>
                    <p class="wizard-panel-sub">Accurate details give you the most realistic valuation estimate:</p>

                    <!-- 3 Interactive Condition Cards -->
                    <div class="condition-cards-grid">
                        <div class="condition-card-option active" data-grade="excellent">
                            <div class="condition-card-header">
                                <span class="condition-grade-title">Flawless / Excellent</span>
                                <span class="condition-check-badge">✓</span>
                            </div>
                            <p class="condition-card-desc">Screen and body are pristine with zero scratches or dents. 100% original parts.</p>
                        </div>
                        <div class="condition-card-option" data-grade="good">
                            <div class="condition-card-header">
                                <span class="condition-grade-title">Good Condition</span>
                                <span class="condition-check-badge">✓</span>
                            </div>
                            <p class="condition-card-desc">Minor normal signs of use. Screen is intact without cracks. Fully functional.</p>
                        </div>
                        <div class="condition-card-option" data-grade="average">
                            <div class="condition-card-header">
                                <span class="condition-grade-title">Fair / Average</span>
                                <span class="condition-check-badge">✓</span>
                            </div>
                            <p class="condition-card-desc">Visible scratches, minor housing scuffs, or battery below 80%. Everything works.</p>
                        </div>
                    </div>

                    <!-- Quick Functional Questions -->
                    <div class="condition-group">
                        <h4 class="condition-title">Screen Condition</h4>
                        <p class="condition-desc">Is the display original, touch responsive, and free of discoloration or lines?</p>
                        <div class="radio-options-row">
                            <label class="radio-option-label">
                                <input type="radio" name="screen_condition" value="flawless" checked> Perfect / Flawless
                            </label>
                            <label class="radio-option-label">
                                <input type="radio" name="screen_condition" value="minor_scratches"> Minor Scratches
                            </label>
                            <label class="radio-option-label">
                                <input type="radio" name="screen_condition" value="cracked"> Cracked / Replaced
                            </label>
                        </div>
                    </div>

                    <div class="condition-group">
                        <h4 class="condition-title">Body & Housing Condition</h4>
                        <p class="condition-desc">Are there any heavy dents, bent frames, or back glass cracks?</p>
                        <div class="radio-options-row">
                            <label class="radio-option-label">
                                <input type="radio" name="body_condition" value="flawless" checked> Clean / Like New
                            </label>
                            <label class="radio-option-label">
                                <input type="radio" name="body_condition" value="minor_dents"> Minor Scuffs
                            </label>
                            <label class="radio-option-label">
                                <input type="radio" name="body_condition" value="heavy_dents"> Dents / Cracked Back
                            </label>
                        </div>
                    </div>

                    <div class="condition-group">
                        <h4 class="condition-title">Functional Features</h4>
                        <p class="condition-desc">Check any functional issues (uncheck if working normally):</p>
                        <div class="checkbox-options-grid">
                            <label class="checkbox-option-label">
                                <input type="checkbox" name="func_face_id" value="working" checked> Face ID / Touch ID Works
                            </label>
                            <label class="checkbox-option-label">
                                <input type="checkbox" name="func_cameras" value="working" checked> Both Cameras Work
                            </label>
                            <label class="checkbox-option-label">
                                <input type="checkbox" name="func_battery" value="good" checked> Battery Health &gt; 80%
                            </label>
                            <label class="checkbox-option-label">
                                <input type="checkbox" name="func_speakers" value="working" checked> Speakers &amp; Mic Work
                            </label>
                        </div>
                    </div>

                    <div class="wizard-nav-btns">
                        <button type="button" class="btn-back-step" id="btn-back-to-step-2">&larr; Change Storage</button>
                        <button type="button" class="btn btn-primary" id="btn-calculate-estimate">Calculate Resale Value &rarr;</button>
                    </div>
                </div>

                <!-- STEP 4: INSTANT VALUATION ESTIMATE DISPLAY & LEAD FUNNEL -->
                <div class="wizard-panel" id="wizard-step-4">
                    <div class="estimate-result-badge">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                        <span>Verified Price Estimate</span>
                    </div>

                    <h3 class="estimate-model-title" id="estimate-model-name">Apple iPhone 16 Pro (128GB)</h3>
                    <p class="estimate-subtitle">Based on your condition selection and current secondary market demand:</p>

                    <!-- Price Card -->
                    <div class="estimate-price-card">
                        <div class="estimate-label">Estimated Resale Value</div>
                        <div class="estimate-amount-display" id="estimate-amount">₹56,500</div>
                        <div class="estimate-range-sub" id="estimate-range-text">Expected range: ₹54,000 – ₹58,000</div>
                    </div>

                    <!-- Breakdown Details -->
                    <div class="estimate-breakdown-list">
                        <div class="breakdown-item">
                            <span>Base Device Valuation</span>
                            <span id="breakdown-base-price">₹56,500</span>
                        </div>
                        <div class="breakdown-item">
                            <span>Doorstep Diagnostic &amp; Pickup</span>
                            <span style="color: var(--color-cta); font-weight: 700;">FREE</span>
                        </div>
                        <div class="breakdown-item">
                            <span>Instant Payment Transfer</span>
                            <span style="color: var(--color-cta); font-weight: 700;">Instant (UPI / Bank / Cash)</span>
                        </div>
                    </div>

                    <!-- STEP 5: Lead Capture Box inside Valuation Flow -->
                    <div class="valuation-lead-box" id="valuation-lead-box" style="margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--color-border);">
                        <div class="valuation-lead-header text-center" style="margin-bottom: 14px;">
                            <h4 style="font-size: 1.125rem; font-weight: 700; color: var(--color-dark); margin-bottom: 4px;">Get Your iPhone's Estimated Value</h4>
                            <p style="font-size: 0.8125rem; color: var(--color-text-secondary); margin: 0;">Enter your details and we'll help you with the next step.</p>
                        </div>

                        <!-- 5 Form Trust Badges directly around form -->
                        <div class="form-trust-pills" style="display: flex; flex-wrap: wrap; justify-content: center; gap: 6px 8px; margin-bottom: 16px;">
                            <span class="hero-trust-badge" style="color: var(--color-dark); background: rgba(0, 113, 227, 0.08); border: 1px solid rgba(0, 113, 227, 0.16);">✓ Transparent valuation</span>
                            <span class="hero-trust-badge" style="color: var(--color-dark); background: rgba(0, 113, 227, 0.08); border: 1px solid rgba(0, 113, 227, 0.16);">✓ Free doorstep pickup</span>
                            <span class="hero-trust-badge" style="color: var(--color-dark); background: rgba(0, 113, 227, 0.08); border: 1px solid rgba(0, 113, 227, 0.16);">✓ 32-point inspection</span>
                            <span class="hero-trust-badge" style="color: var(--color-dark); background: rgba(0, 113, 227, 0.08); border: 1px solid rgba(0, 113, 227, 0.16);">✓ Secure data handling</span>
                            <span class="hero-trust-badge" style="color: var(--color-dark); background: rgba(0, 113, 227, 0.08); border: 1px solid rgba(0, 113, 227, 0.16);">✓ Fast payment</span>
                        </div>

                        <form id="wizard-lead-form" class="lead-form" action="forms/submit.php" method="POST" novalidate>
                            <input type="hidden" name="csrf_token" id="wizard_csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                            <input type="hidden" name="phone_model" id="wizard_form_model" value="Apple iPhone 16 Pro">
                            <input type="hidden" name="storage" id="wizard_form_storage" value="128GB">
                            <input type="hidden" name="condition" id="wizard_form_condition" value="Flawless">
                            <input type="hidden" name="estimated_value" id="wizard_form_est_val" value="₹56,500">
                            
                            <!-- Anti-Spam Honeypot -->
                            <div style="display:none !important;" aria-hidden="true">
                                <input type="text" name="website_hp" tabindex="-1" autocomplete="off">
                            </div>

                            <div class="form-group" style="margin-bottom: 12px;">
                                <label for="wizard_full_name" class="form-label" style="font-size: 0.8125rem;">Full Name <span style="color:red;">*</span></label>
                                <input type="text" id="wizard_full_name" name="full_name" class="form-control" placeholder="e.g. Rahul Sharma" required autocomplete="name">
                            </div>

                            <div class="form-group" style="margin-bottom: 14px;">
                                <label for="wizard_phone_number" class="form-label" style="font-size: 0.8125rem;">Mobile / WhatsApp Number <span style="color:red;">*</span></label>
                                <input type="tel" id="wizard_phone_number" name="phone_number" class="form-control" placeholder="e.g. 98200 12345" required autocomplete="tel" inputmode="tel">
                            </div>

                            <div class="form-consent-row" style="margin-bottom: 14px;">
                                <input type="checkbox" name="consent" id="wizard_form_consent" required checked>
                                <label for="wizard_form_consent" style="font-size: 0.75rem;">By submitting, you agree to our <a href="policies/terms.php" target="_blank">Terms &amp; Conditions</a> and <a href="policies/privacy-policy.php" target="_blank">Privacy Policy</a>.</label>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg btn-full" id="wizard-form-submit-btn">
                                <span>GET MY IPHONE VALUE &rarr;</span>
                            </button>
                            <p class="text-center text-muted" style="font-size: 0.75rem; margin-top: 8px; margin-bottom: 0;">Free • No obligation • Your information stays private</p>

                            <!-- Success / Error Status Alert -->
                            <div id="wizard-form-status" class="form-status-alert" style="margin-top: 12px; display: none;"></div>
                        </form>
                    </div>

                    <!-- Direct WhatsApp Option -->
                    <div class="estimate-cta-group" style="margin-top: 16px;">
                        <a href="#" target="_blank" rel="noopener noreferrer" class="btn btn-whatsapp btn-full" id="btn-quote-wa-link">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
                            <span>Lock Price on WhatsApp &rarr;</span>
                        </a>
                    </div>

                    <div class="text-center" style="margin-top: 18px;">
                        <button type="button" class="btn-back-step" id="btn-recalculate-quote">&larr; Change Model or Details</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

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
                <a href="#valuation" class="btn promo-banner-cta btn-promo-dark">
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
<section class="final-cta-dark-section" id="enquire">
    <div class="container">
        <div class="section-header text-center" style="margin-bottom: 24px;">
            <span class="section-eyebrow" style="color: #64D2FF;">GET YOUR IPHONE VALUE</span>
            <h2 class="section-title">Ready to Find Out What Your iPhone Is Worth?</h2>
            <p class="section-subtitle">Check your iPhone's estimated resale value and start the selling process.</p>
        </div>

        <div class="lead-form-card">
            <div class="lead-form-header text-center" style="margin-bottom: 14px;">
                <h3 style="font-size: 1.1875rem; font-weight: 700; color: var(--color-dark); margin-bottom: 4px;">Get Your iPhone's Estimated Value</h3>
                <p style="font-size: 0.8125rem; color: var(--color-text-secondary); margin: 0;">Enter your details and we'll help you with the next step.</p>
            </div>

            <!-- Form Trust Badges around form -->
            <div class="form-trust-pills" style="display: flex; flex-wrap: wrap; justify-content: center; gap: 6px 8px; margin-bottom: 16px;">
                <span class="hero-trust-badge" style="color: var(--color-dark); background: rgba(0, 113, 227, 0.08); border: 1px solid rgba(0, 113, 227, 0.16);">✓ Transparent valuation</span>
                <span class="hero-trust-badge" style="color: var(--color-dark); background: rgba(0, 113, 227, 0.08); border: 1px solid rgba(0, 113, 227, 0.16);">✓ Free doorstep pickup</span>
                <span class="hero-trust-badge" style="color: var(--color-dark); background: rgba(0, 113, 227, 0.08); border: 1px solid rgba(0, 113, 227, 0.16);">✓ 32-point inspection</span>
                <span class="hero-trust-badge" style="color: var(--color-dark); background: rgba(0, 113, 227, 0.08); border: 1px solid rgba(0, 113, 227, 0.16);">✓ Fast payment</span>
            </div>

            <form id="landing-lead-form" class="lead-form" action="forms/submit.php" method="POST" novalidate>
                <!-- CSRF Protection -->
                <input type="hidden" name="csrf_token" id="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                <!-- Anti-Spam Honeypot -->
                <div style="display:none !important;" aria-hidden="true">
                    <input type="text" name="website_hp" tabindex="-1" autocomplete="off">
                </div>

                <div class="form-group" style="margin-bottom: 12px;">
                    <label for="form_full_name" class="form-label" style="font-size: 0.8125rem;">Full Name <span style="color:red;">*</span></label>
                    <input type="text" id="form_full_name" name="full_name" class="form-control" placeholder="e.g. Rahul Sharma" required autocomplete="name">
                </div>

                <div class="form-group" style="margin-bottom: 12px;">
                    <label for="form_phone_number" class="form-label" style="font-size: 0.8125rem;">Mobile Number (WhatsApp) <span style="color:red;">*</span></label>
                    <input type="tel" id="form_phone_number" name="phone_number" class="form-control" placeholder="e.g. 98200 12345" required autocomplete="tel" inputmode="tel">
                </div>

                <div class="form-group" style="margin-bottom: 12px;">
                    <label for="form_phone_model" class="form-label" style="font-size: 0.8125rem;">iPhone Model &amp; Storage <span style="color:red;">*</span></label>
                    <input type="text" id="form_phone_model" name="phone_model" class="form-control" placeholder="e.g. Apple iPhone 16 Pro 128GB" required>
                </div>

                <div class="form-group" style="margin-bottom: 14px;">
                    <label for="form_message" class="form-label" style="font-size: 0.8125rem;">Pickup Locality / Condition Notes (Optional)</label>
                    <textarea id="form_message" name="message" class="form-control" rows="2" placeholder="Mention battery health, area in Mumbai, or preferred time for executive pickup..."></textarea>
                </div>

                <div class="form-consent-row" style="margin-bottom: 14px;">
                    <input type="checkbox" name="consent" id="form_consent" required checked>
                    <label for="form_consent" style="font-size: 0.75rem;">By submitting your details, you agree to our <a href="policies/terms.php" target="_blank">Terms &amp; Conditions</a> and acknowledge our <a href="policies/privacy-policy.php" target="_blank">Privacy Policy</a>.</label>
                </div>

                <button type="submit" class="btn btn-primary btn-lg btn-full" id="form-submit-btn">
                    <span>GET MY IPHONE VALUE &rarr;</span>
                </button>
                <p class="text-center text-muted" style="font-size: 0.75rem; margin-top: 8px; margin-bottom: 0;">Free • No obligation • Your information stays private</p>

                <!-- Status Feedback Alert -->
                <div id="form-status-alert" class="form-status-alert" style="margin-top: 10px;"></div>
            </form>
        </div>
    </div>
</section>

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

            <div class="contact-card" style="padding: 0; overflow: hidden;">
                <iframe 
                    class="contact-map-embed"
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3774.240321287959!2d72.82364467596048!3d18.928000082245892!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3be7d1e704b2ebef%3A0x6a056a29f8f411fa!2sArcadia%20Building%2C%20NCPA%20Marg%2C%20Nariman%20Point%2C%20Mumbai%2C%20Maharashtra%20400021!5e0!3m2!1sen!2sin!4v1708700000000!5m2!1sen!2sin" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade"
                    title="CashSecond Office Location in Nariman Point, Mumbai">
                </iframe>
            </div>
        </div>
    </div>
</section>

<script>
    window.allIphoneCatalog = <?= json_encode($iphoneModels, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
