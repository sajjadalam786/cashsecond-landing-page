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
     1. HERO SECTION & 3D INTERACTION
     ============================================================ -->
<section class="hero-section" id="hero">
    <div class="container">
        <div class="hero-grid">
            <!-- Left Hero Content -->
            <div class="hero-content-col">
                <div class="hero-badge">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    <span>Apple-Specialist Buyback</span>
                </div>

                <h1 class="hero-title">
                    Sell Your iPhone.<br>Get Its Best Value.
                </h1>

                <p class="hero-subtitle">
                    Get an instant estimate for your iPhone in seconds. Simple, secure and hassle-free with free doorstep pickup in Mumbai.
                </p>

                <!-- Hero Search Experience -->
                <div class="hero-search-wrapper" id="hero-search-box">
                    <div class="search-input-group">
                        <span class="search-icon-left">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        </span>
                        <input type="text" id="hero-search-input" class="hero-search-input" placeholder="Search your iPhone model (e.g. 16 Pro, 15, 14)..." autocomplete="off">
                        <span class="search-clear-btn" id="hero-search-clear" title="Clear search">&times;</span>
                    </div>

                    <!-- Autocomplete Dropdown -->
                    <div class="search-autocomplete-dropdown" id="search-autocomplete-results"></div>
                </div>

                <!-- CTA Group -->
                <div class="hero-cta-group">
                    <a href="#valuation" class="btn btn-primary btn-lg" id="hero-get-price-cta">
                        <span>Get Instant Price</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                    </a>
                    <a href="#models" class="btn btn-secondary btn-lg" id="hero-explore-models-cta">
                        <span>Explore iPhone Models</span>
                    </a>
                </div>
            </div>

            <!-- Right 3D Interactive Hero iPhone Visual -->
            <div class="hero-3d-scene" id="hero-3d-container">
                <div class="hero-3d-phone-wrap" id="hero-3d-phone-wrap">
                    <!-- Floating Specification Pills -->
                    <div class="spec-pill spec-pill-1">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="var(--color-cta)" stroke-width="2.5"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                        <span>Pro Camera</span>
                    </div>
                    <div class="spec-pill spec-pill-2">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="var(--color-cta)" stroke-width="2.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                        <span>Apple Silicon</span>
                    </div>
                    <div class="spec-pill spec-pill-3">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="var(--color-success)" stroke-width="2.5"><rect x="1" y="6" width="18" height="12" rx="2" ry="2"/><line x1="23" y1="13" x2="23" y2="11"/></svg>
                        <span>Battery Health</span>
                    </div>
                    <div class="spec-pill spec-pill-4">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="var(--color-cta)" stroke-width="2.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        <span>Original Display</span>
                    </div>

                    <img src="assets/images/phones/iphone-16-pro.svg" alt="Apple iPhone Buyback, Valuation and Resale in Mumbai - CashSecond" class="hero-3d-phone-img" id="hero-main-phone-img" loading="eager" width="280" height="310">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     2. MOVING TRUST STRIP #1 (Infinite Marquee)
     ============================================================ -->
<div class="marquee-wrapper trust-marquee-section" aria-label="Trust Badges">
    <div class="marquee-track">
        <span class="trust-badge-item"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Instant Valuation</span>
        <span class="trust-badge-item"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Free Doorstep Pickup</span>
        <span class="trust-badge-item"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Secure Data Erasure</span>
        <span class="trust-badge-item"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Fast Payment</span>
        <span class="trust-badge-item"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Trusted Process</span>
    </div>
    <div class="marquee-track" aria-hidden="true">
        <span class="trust-badge-item"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Instant Valuation</span>
        <span class="trust-badge-item"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Free Doorstep Pickup</span>
        <span class="trust-badge-item"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Secure Data Erasure</span>
        <span class="trust-badge-item"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Fast Payment</span>
        <span class="trust-badge-item"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Trusted Process</span>
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
                <span class="section-eyebrow">Instant Valuation</span>
                <h2>What's Your iPhone?</h2>
                <p>Select your iPhone model and condition to get an estimated valuation.</p>
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
                    <div class="model-search-box" style="margin-bottom: 14px;">
                        <input type="text" id="model-filter-input" class="form-control" placeholder="Filter models (e.g. 16 Pro, 15 Plus, 13 Mini, SE)...">
                    </div>

                    <!-- Model Product Cards Grid -->
                    <div class="models-grid-list" id="models-list-container">
                        <?php foreach ($iphoneModels as $model): 
                            $imgSrc = htmlspecialchars($model['image'] ?? 'assets/images/phones/iphone-15.svg');
                            $prodName = htmlspecialchars($model['product_name']);
                            $prodId = htmlspecialchars($model['product_id']);
                            $series = htmlspecialchars($model['series'] ?? 'older');
                        ?>
                        <div class="model-product-card" data-name="<?= $prodName ?>" data-id="<?= $prodId ?>" data-image="<?= $imgSrc ?>" data-series="<?= $series ?>">
                            <div class="model-product-img-wrap">
                                <img src="<?= $imgSrc ?>" alt="<?= $prodName ?> valuation and buyback" class="model-product-img" loading="lazy" width="80" height="80">
                            </div>
                            <h4 class="model-product-name"><?= $prodName ?></h4>
                            <span class="model-product-cta">Check Value &rarr;</span>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Model Filter Empty State -->
                    <div id="model-filter-empty" class="search-empty-state" style="display: none;">
                        <h4 class="search-empty-title">No iPhone model found</h4>
                        <p class="search-empty-sub">Try searching by model name, such as iPhone 16 or iPhone 15 Pro.</p>
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
                        <p class="condition-desc">Are there any heavy dents, bent frames, or cracked back glass?</p>
                        <div class="radio-options-row">
                            <label class="radio-option-label">
                                <input type="radio" name="body_condition" value="flawless" checked> No Dents / Clean
                            </label>
                            <label class="radio-option-label">
                                <input type="radio" name="body_condition" value="minor_wear"> Normal Minor Wear
                            </label>
                            <label class="radio-option-label">
                                <input type="radio" name="body_condition" value="heavy_dents"> Heavy Dents / Scuffs
                            </label>
                        </div>
                    </div>

                    <div class="condition-group">
                        <h4 class="condition-title">Apple Warranty Status</h4>
                        <div class="radio-options-row">
                            <label class="radio-option-label">
                                <input type="radio" name="warranty_status" value="under_warranty"> Under Apple Warranty
                            </label>
                            <label class="radio-option-label">
                                <input type="radio" name="warranty_status" value="out_of_warranty" checked> Out of Warranty
                            </label>
                        </div>
                    </div>

                    <div class="wizard-nav-btns">
                        <button type="button" class="btn-back-step" id="btn-back-to-step-2">&larr; Back to Storage</button>
                        <button type="button" class="btn btn-primary" id="btn-calculate-value">Calculate Value &rarr;</button>
                    </div>
                </div>

                <!-- STEP 4: ANIMATED PRICE REVEAL -->
                <div class="wizard-panel" id="wizard-step-4">
                    <!-- Shimmer / Calculating State -->
                    <div class="calculating-loader" id="calculating-state-box" style="display: none;">
                        <div class="pulse-spinner"></div>
                        <h3 style="font-size: 1.125rem; font-weight: 700; color: var(--color-dark); margin-bottom: 4px;">Calculating your iPhone value...</h3>
                        <p style="font-size: 0.8125rem; color: var(--color-text-secondary);">Analyzing live secondary market buyback rates in Mumbai</p>
                    </div>

                    <!-- Revealed Estimate Box -->
                    <div class="quote-result-box" id="quote-result-container">
                        <h3 class="quote-device-name" id="quote-device-name-display">Apple iPhone 16 Pro</h3>
                        <p class="quote-device-specs" id="quote-device-specs-display">128GB Storage • Flawless Condition</p>

                        <div style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--color-text-muted); margin-bottom: 4px;">
                            Your Estimated Buyback Value
                        </div>
                        <div class="quote-price-tag" id="quote-animated-price-val">₹48,500</div>
                        <p class="quote-disclaimer">Final value confirmed upon physical 32-point inspection at your doorstep.</p>
                    </div>

                    <div class="quote-actions-row">
                        <a href="#enquire" class="btn btn-primary btn-full" id="btn-schedule-pickup-trigger">
                            <span>Schedule Pickup &rarr;</span>
                        </a>
                        <a href="#" target="_blank" rel="noopener noreferrer" class="btn btn-whatsapp btn-full" id="btn-quote-wa-link">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
                            <span>Lock Price on WhatsApp</span>
                        </a>
                    </div>

                    <div class="text-center" style="margin-top: 18px;">
                        <button type="button" class="btn-back-step" id="btn-recalculate-quote">↻ Recalculate or Change Details</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     4. INTERACTIVE iPHONE SHOWCASE ("Your iPhone. Your Value.")
     ============================================================ -->
<section class="section-container interactive-showcase-section" id="showcase">
    <div class="container">
        <div class="section-header text-center">
            <span class="section-eyebrow">Interactive Showcase</span>
            <h2 class="section-title">Your iPhone. Your Value.</h2>
            <p class="section-subtitle">Select a featured model to preview its key specifications and estimated starting buyback price.</p>
        </div>

        <!-- Model Selector Pills -->
        <div class="showcase-model-pills">
            <button type="button" class="showcase-pill-btn active" data-model="16pro" data-name="Apple iPhone 16 Pro" data-img="assets/images/phones/iphone-16-pro.svg" data-price="₹58,000" data-desc="A18 Pro chip, 48MP Fusion camera system with Camera Control, and Grade 5 titanium design.">iPhone 16 Pro</button>
            <button type="button" class="showcase-pill-btn" data-model="16" data-name="Apple iPhone 16" data-img="assets/images/phones/iphone-16.svg" data-price="₹44,500" data-desc="A18 chip with Apple Intelligence, 48MP 2-in-1 camera, Action button, and super-high-resolution photos.">iPhone 16</button>
            <button type="button" class="showcase-pill-btn" data-model="15pro" data-name="Apple iPhone 15 Pro" data-img="assets/images/phones/iphone-15-pro.svg" data-price="₹46,000" data-desc="A17 Pro chip, aerospace-grade titanium frame, Action button, and 3x telephoto optical zoom.">iPhone 15 Pro</button>
            <button type="button" class="showcase-pill-btn" data-model="15" data-name="Apple iPhone 15" data-img="assets/images/phones/iphone-15.svg" data-price="₹35,500" data-desc="Dynamic Island, 48MP main camera, durable color-infused glass back, and USB-C connectivity.">iPhone 15</button>
            <button type="button" class="showcase-pill-btn" data-model="14pro" data-name="Apple iPhone 14 Pro" data-img="assets/images/phones/iphone-14-pro.svg" data-price="₹38,000" data-desc="A16 Bionic chip, Dynamic Island introduction, Always-On display, and 48MP camera sensor.">iPhone 14 Pro</button>
        </div>

        <!-- Showcase Stage Card -->
        <div class="showcase-stage-card">
            <div class="showcase-img-col">
                <img src="assets/images/phones/iphone-16-pro.svg" alt="Apple iPhone 16 Pro Showcase" class="showcase-phone-img" id="showcase-phone-img" loading="lazy" width="220" height="250">
            </div>
            <div class="showcase-info-col">
                <h3 class="showcase-title" id="showcase-title-display">Apple iPhone 16 Pro</h3>
                <p class="showcase-desc" id="showcase-desc-display">A18 Pro chip, 48MP Fusion camera system with Camera Control, and Grade 5 titanium design.</p>
                <div class="showcase-price-row">
                    <div class="showcase-price-label">Estimated Starting Value</div>
                    <div class="showcase-price-val" id="showcase-price-display">₹58,000</div>
                </div>
                <a href="#valuation" class="btn btn-primary" id="showcase-check-val-btn">
                    <span>Check Your Model's Exact Value &rarr;</span>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     5. WHY CHOOSE US (6 Premium Cards)
     ============================================================ -->
<section class="section-container" id="why-us">
    <div class="container">
        <div class="section-header text-center">
            <span class="section-eyebrow">Why CashSecond</span>
            <h2 class="section-title">Why Sell Your iPhone With Us?</h2>
            <p class="section-subtitle">Experience a trusted, secure, and transparent selling process built exclusively for Apple users.</p>
        </div>

        <div class="why-grid-6">
            <!-- Card 1 -->
            <div class="why-card">
                <div class="why-icon-box">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
                <h3 class="why-card-title">Best iPhone Value</h3>
                <p class="why-card-desc">Our valuation algorithm tracks real secondary market rates to provide a competitive and honest estimate for your device.</p>
            </div>

            <!-- Card 2 -->
            <div class="why-card">
                <div class="why-icon-box">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <h3 class="why-card-title">Instant Price Estimate</h3>
                <p class="why-card-desc">Get a valuation for any iPhone model in under 60 seconds with our streamlined 4-step calculator.</p>
            </div>

            <!-- Card 3 -->
            <div class="why-card">
                <div class="why-icon-box">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                </div>
                <h3 class="why-card-title">Free Doorstep Pickup</h3>
                <p class="why-card-desc">We dispatch a certified executive to your home or office across Mumbai, Navi Mumbai, and Thane at your convenience.</p>
            </div>

            <!-- Card 4 -->
            <div class="why-card">
                <div class="why-icon-box">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <h3 class="why-card-title">Secure Data Erasure</h3>
                <p class="why-card-desc">We ensure your iCloud account is signed out and complete a 100% factory reset in front of you before handover.</p>
            </div>

            <!-- Card 5 -->
            <div class="why-card">
                <div class="why-icon-box">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                </div>
                <h3 class="why-card-title">Fast On-Spot Payment</h3>
                <p class="why-card-desc">Receive instant payout via UPI (GPay, PhonePe, Paytm), IMPS Bank Transfer, or Cash immediately after inspection.</p>
            </div>

            <!-- Card 6 -->
            <div class="why-card">
                <div class="why-icon-box">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                </div>
                <h3 class="why-card-title">Transparent Process</h3>
                <p class="why-card-desc">Zero hidden charges, zero surprise deductions, and no obligation to sell if you decline the final offer.</p>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     6. 32-POINT IPHONE INSPECTION SECTION
     ============================================================ -->
<section class="section-container inspection-section" id="inspection">
    <div class="container">
        <div class="section-header text-center">
            <span class="section-eyebrow">Quality Diagnostics</span>
            <h2 class="section-title">32-Point iPhone Inspection</h2>
            <p class="section-subtitle">Every iPhone undergoes a thorough 5-minute diagnostic verification by our technician.</p>
        </div>

        <div class="inspection-grid">
            <div class="inspection-item"><span class="inspection-check-icon">✓</span> Display & Touch Response</div>
            <div class="inspection-item"><span class="inspection-check-icon">✓</span> TrueTone & Brightness</div>
            <div class="inspection-item"><span class="inspection-check-icon">✓</span> Face ID & TrueDepth</div>
            <div class="inspection-item"><span class="inspection-check-icon">✓</span> Battery Health %</div>
            <div class="inspection-item"><span class="inspection-check-icon">✓</span> Rear Camera & Zoom</div>
            <div class="inspection-item"><span class="inspection-check-icon">✓</span> Front Selfie Camera</div>
            <div class="inspection-item"><span class="inspection-check-icon">✓</span> Earpiece & Mic Clarity</div>
            <div class="inspection-item"><span class="inspection-check-icon">✓</span> Stereo Speaker Quality</div>
            <div class="inspection-item"><span class="inspection-check-icon">✓</span> Wi-Fi, Bluetooth & GPS</div>
            <div class="inspection-item"><span class="inspection-check-icon">✓</span> Wireless & MagSafe Charge</div>
            <div class="inspection-item"><span class="inspection-check-icon">✓</span> Action / Mute Switch</div>
            <div class="inspection-item"><span class="inspection-check-icon">✓</span> IMEI Blacklist Check</div>
        </div>
    </div>
</section>

<!-- ============================================================
     7. HOW IT WORKS SECTION
     ============================================================ -->
<section class="section-container" id="how-it-works">
    <div class="container">
        <div class="section-header text-center">
            <span class="section-eyebrow">3 Simple Steps</span>
            <h2 class="section-title">Selling Your iPhone Is Simple</h2>
            <p class="section-subtitle">From instant online price check to doorstep pickup and instant payment.</p>
        </div>

        <div class="how-it-works-grid">
            <div class="how-step-card">
                <div class="how-step-num">STEP 01</div>
                <h3 class="how-step-title">Check Your Price</h3>
                <p class="how-step-desc">Select your iPhone model, storage capacity, and condition to get an instant valuation estimate in seconds.</p>
            </div>
            <div class="how-step-card">
                <div class="how-step-num">STEP 02</div>
                <h3 class="how-step-title">Schedule Pickup</h3>
                <p class="how-step-desc">Pick a date and convenient time slot. Our certified executive visits your home or office across Mumbai.</p>
            </div>
            <div class="how-step-card">
                <div class="how-step-num">STEP 03</div>
                <h3 class="how-step-title">Get Paid Instantly</h3>
                <p class="how-step-desc">Following a quick 5-minute diagnostic check, payment is transferred immediately via UPI, IMPS, or Cash.</p>
            </div>
        </div>

        <!-- Visual Flow Banner -->
        <div class="process-flow-banner">
            <span class="flow-node">You</span>
            <span class="flow-arrow">&rarr;</span>
            <span class="flow-node">iPhone Details</span>
            <span class="flow-arrow">&rarr;</span>
            <span class="flow-node">Valuation</span>
            <span class="flow-arrow">&rarr;</span>
            <span class="flow-node">Inspection</span>
            <span class="flow-arrow">&rarr;</span>
            <span class="flow-node">Pickup</span>
            <span class="flow-arrow">&rarr;</span>
            <span class="flow-node" style="color:var(--color-cta);">Instant Payment</span>
        </div>

        <!-- After Submit Card -->
        <div class="after-submit-card">
            <h4 class="after-submit-title">What Happens After You Submit Your Details?</h4>
            <ol class="after-submit-list">
                <li><span class="bullet-num">1.</span> We review your iPhone information and lock your estimated quote.</li>
                <li><span class="bullet-num">2.</span> Our coordinator confirms your preferred pickup time slot via WhatsApp/Phone.</li>
                <li><span class="bullet-num">3.</span> The executive visits your doorstep and performs the 32-point check.</li>
                <li><span class="bullet-num">4.</span> We assist you with iCloud sign-out and full device factory reset.</li>
                <li><span class="bullet-num">5.</span> Payment is disbursed on the spot before handover.</li>
            </ol>
        </div>
    </div>
</section>

<!-- ============================================================
     8. MOVING TRUST STRIP #2 (Infinite Marquee)
     ============================================================ -->
<div class="marquee-wrapper mid-marquee-section" aria-label="Benefits">
    <div class="marquee-track">
        <span class="marquee-item">BEST VALUE <span class="marquee-dot">•</span> FREE DOORSTEP PICKUP <span class="marquee-dot">•</span> SECURE DATA WIPE <span class="marquee-dot">•</span> FAST PAYMENT <span class="marquee-dot">•</span> SIMPLE PROCESS <span class="marquee-dot">•</span> iPHONE SPECIALISTS <span class="marquee-dot">•</span></span>
        <span class="marquee-item">BEST VALUE <span class="marquee-dot">•</span> FREE DOORSTEP PICKUP <span class="marquee-dot">•</span> SECURE DATA WIPE <span class="marquee-dot">•</span> FAST PAYMENT <span class="marquee-dot">•</span> SIMPLE PROCESS <span class="marquee-dot">•</span> iPHONE SPECIALISTS <span class="marquee-dot">•</span></span>
    </div>
    <div class="marquee-track" aria-hidden="true">
        <span class="marquee-item">BEST VALUE <span class="marquee-dot">•</span> FREE DOORSTEP PICKUP <span class="marquee-dot">•</span> SECURE DATA WIPE <span class="marquee-dot">•</span> FAST PAYMENT <span class="marquee-dot">•</span> SIMPLE PROCESS <span class="marquee-dot">•</span> iPHONE SPECIALISTS <span class="marquee-dot">•</span></span>
        <span class="marquee-item">BEST VALUE <span class="marquee-dot">•</span> FREE DOORSTEP PICKUP <span class="marquee-dot">•</span> SECURE DATA WIPE <span class="marquee-dot">•</span> FAST PAYMENT <span class="marquee-dot">•</span> SIMPLE PROCESS <span class="marquee-dot">•</span> iPHONE SPECIALISTS <span class="marquee-dot">•</span></span>
    </div>
</div>

<!-- ============================================================
     9. CUSTOMER REVIEWS
     ============================================================ -->
<section class="section-container" id="reviews" style="background-color: #FFFFFF; border-top: 1px solid var(--color-border); border-bottom: 1px solid var(--color-border);">
    <div class="container">
        <div class="section-header text-center">
            <span class="section-eyebrow">Customer Experiences</span>
            <h2 class="section-title">What Our Customers Say</h2>
            <p class="section-subtitle">Real feedback from iPhone sellers across Mumbai who traded through CashSecond.</p>
        </div>

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
</section>

<!-- ============================================================
     10. FAQ ACCORDION SECTION
     ============================================================ -->
<section class="section-container" id="faq">
    <div class="container">
        <div class="section-header text-center">
            <span class="section-eyebrow">Got Questions?</span>
            <h2 class="section-title">Frequently Asked Questions</h2>
            <p class="section-subtitle">Everything you need to know about our iPhone valuation, doorstep pickup, and instant payouts.</p>
        </div>

        <div class="faq-container">
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

                <!-- Additional AEO FAQ items -->
                <div class="faq-item">
                    <button type="button" class="faq-btn" aria-expanded="false">
                        <span>How is my personal data handled during the sale?</span>
                        <span class="faq-icon">+</span>
                    </button>
                    <div class="faq-content">
                        <p>Your data privacy is guaranteed. Our technician guides you to sign out of your Apple ID / iCloud account and performs a complete factory reset (Erase All Content and Settings) in front of you before device handover.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <button type="button" class="faq-btn" aria-expanded="false">
                        <span>What happens if the physical inspection value differs?</span>
                        <span class="faq-icon">+</span>
                    </button>
                    <div class="faq-content">
                        <p>If undisclosed flaws (like display lines or Face ID issues) are identified, our technician provides a revised fair offer. You are under zero obligation to accept and may decline the sale with no cancellation fee.</p>
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
        <div class="section-header text-center">
            <span class="section-eyebrow" style="color: #64D2FF;">Get Started</span>
            <h2 class="section-title">Ready to Sell Your iPhone?</h2>
            <p class="section-subtitle">Find out what your iPhone is worth in just a few steps with free doorstep pickup across Mumbai.</p>
        </div>

        <div class="lead-form-card">
            <form id="landing-lead-form" class="lead-form" action="forms/submit.php" method="POST" novalidate>
                <!-- CSRF Protection -->
                <input type="hidden" name="csrf_token" id="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                <!-- Anti-Spam Honeypot -->
                <div style="display:none !important;" aria-hidden="true">
                    <input type="text" name="website_hp" tabindex="-1" autocomplete="off">
                </div>

                <div class="form-group">
                    <label for="form_full_name" class="form-label">Full Name <span style="color:red;">*</span></label>
                    <input type="text" id="form_full_name" name="full_name" class="form-control" placeholder="e.g. Rahul Sharma" required autocomplete="name">
                </div>

                <div class="form-group">
                    <label for="form_phone_number" class="form-label">Mobile Number (WhatsApp) <span style="color:red;">*</span></label>
                    <input type="tel" id="form_phone_number" name="phone_number" class="form-control" placeholder="e.g. 98200 12345" required autocomplete="tel" inputmode="tel">
                </div>

                <div class="form-group">
                    <label for="form_phone_model" class="form-label">iPhone Model & Storage <span style="color:red;">*</span></label>
                    <input type="text" id="form_phone_model" name="phone_model" class="form-control" placeholder="e.g. Apple iPhone 16 Pro 128GB" required>
                </div>

                <div class="form-group">
                    <label for="form_message" class="form-label">Pickup Locality / Condition Notes (Optional)</label>
                    <textarea id="form_message" name="message" class="form-control" rows="2" placeholder="Mention battery health, area in Mumbai, or preferred time for executive pickup..."></textarea>
                </div>

                <div class="form-consent-row">
                    <input type="checkbox" name="consent" id="form_consent" required checked>
                    <label for="form_consent">By submitting your details, you agree to our <a href="policies/terms.php" target="_blank">Terms &amp; Conditions</a> and acknowledge our <a href="policies/privacy-policy.php" target="_blank">Privacy Policy</a>.</label>
                </div>

                <button type="submit" class="btn btn-primary btn-lg btn-full" id="form-submit-btn">
                    <span>Schedule Free Doorstep Pickup</span>
                </button>
                <p class="text-center text-muted" style="font-size: 0.75rem; margin-top: 10px;">Zero obligation. Instant payout on inspection.</p>

                <!-- Status Feedback Alert -->
                <div id="form-status-alert" class="form-status-alert"></div>
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

<?php require __DIR__ . '/includes/footer.php'; ?>
