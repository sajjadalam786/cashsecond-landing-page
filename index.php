<?php
/**
 * CashSecond - Premium iPhone Buyback & Resale Landing Page
 * Apple-Inspired Design • Mobile-First • Conversion-Focused
 */

// Initialize secure session for CSRF
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Generate CSRF token if not set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Load configurations and iPhone-only catalog
$config = require __DIR__ . '/config/config.php';
$business = $config['business'] ?? [];
$sellBrands = $config['sell_brands'] ?? [];
$buyCatalog = $config['buy_catalog'] ?? [];
$iphoneModels = $sellBrands['Apple'] ?? [];

// Include Header
require_once __DIR__ . '/includes/header.php';
?>

<!-- ============================================================
     1. HERO SECTION
     ============================================================ -->
<section class="hero-section" id="hero">
    <div class="container">
        <div class="hero-grid">
            <!-- Left Hero Content -->
            <div class="hero-content">
                <div class="hero-badge">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    <span>Instant iPhone Valuation • Free Doorstep Pickup</span>
                </div>

                <h1 class="hero-title">Sell Your iPhone at a Fair Price</h1>

                <p class="hero-subtitle">
                    Get a transparent valuation for your used iPhone with a simple, secure and hassle-free selling process.
                </p>

                <!-- Quick Model Search Autocomplete -->
                <div class="hero-search-wrapper">
                    <div class="search-input-group">
                        <span class="search-icon-left">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        </span>
                        <input type="text" id="global-phone-search" class="hero-search-input" placeholder="Search your iPhone model (e.g. iPhone 15 Pro, 14, 13)..." autocomplete="off">
                        <button type="button" id="search-clear-btn" class="search-clear-btn" aria-label="Clear Search">&times;</button>
                    </div>
                    <div id="search-autocomplete-results" class="search-autocomplete-dropdown"></div>
                </div>

                <!-- CTAs -->
                <div class="hero-cta-group">
                    <a href="#valuation" class="btn btn-primary btn-lg" id="hero-primary-cta">
                        <span>Check My iPhone Value</span>
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                    <a href="https://wa.me/<?= htmlspecialchars($business['whatsapp_number'] ?? '918976332211'); ?>?text=<?= urlencode('Hi CashSecond, I want to check my iPhone value.'); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-whatsapp btn-lg" id="hero-wa-cta">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
                        <span>WhatsApp Us</span>
                    </a>
                </div>

                <!-- Trust Strip -->
                <div class="hero-trust-bar">
                    <span class="hero-trust-item">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="var(--color-cta)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        Free Valuation
                    </span>
                    <span class="hero-trust-dot">•</span>
                    <span class="hero-trust-item">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="var(--color-cta)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        Doorstep Pickup
                    </span>
                    <span class="hero-trust-dot">•</span>
                    <span class="hero-trust-item">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="var(--color-cta)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        Secure Payment
                    </span>
                </div>
            </div>

            <!-- Right Hero Visual Showcase -->
            <div class="hero-visual-col">
                <div class="hero-visual-card">
                    <img src="assets/images/phones/iphone-16-pro.svg" alt="Apple iPhone Buyback, Valuation and Resale in Mumbai - CashSecond" class="hero-visual-img" loading="eager" width="280" height="280">
                    <p class="hero-visual-caption">Apple iPhone Resale & Buyback</p>
                    <p class="hero-visual-sub">Supporting iPhone 8 through iPhone 16 & 17 series</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     2. IPHONE VALUATION CALCULATOR
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

                    <div class="model-search-box">
                        <input type="text" id="model-filter-input" class="model-search-input" placeholder="Filter models (e.g. 15 Pro, 14, 13, SE)...">
                    </div>

                    <div class="models-grid-list" id="models-list-container">
                        <?php foreach ($iphoneModels as $model): 
                            $imgSrc = htmlspecialchars($model['image'] ?? 'assets/images/phones/iphone-15.svg');
                            $prodName = htmlspecialchars($model['product_name']);
                            $prodId = htmlspecialchars($model['product_id']);
                        ?>
                        <div class="model-product-card" data-name="<?= $prodName ?>" data-id="<?= $prodId ?>" data-image="<?= $imgSrc ?>">
                            <div class="model-product-img-wrap">
                                <img src="<?= $imgSrc ?>" alt="<?= $prodName ?> valuation and buyback" class="model-product-img" loading="lazy" width="80" height="80">
                            </div>
                            <h4 class="model-product-name"><?= $prodName ?></h4>
                            <span class="model-product-cta">Check Value &rarr;</span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div id="model-filter-empty" class="search-empty-state" style="display:none;">
                        <h4 class="search-empty-title">No iPhone model found</h4>
                        <p class="search-empty-sub">Try searching by model name, such as iPhone 16 or iPhone 15 Pro.</p>
                    </div>
                </div>

                <!-- STEP 2: STORAGE VARIANT -->
                <div class="wizard-panel" id="wizard-step-2">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                        <h3 class="wizard-panel-title" style="margin-bottom:0;">2. Select Storage Capacity</h3>
                        <span class="badge" id="badge-selected-model" style="font-size:0.8125rem; font-weight:700; color:var(--color-cta);">iPhone</span>
                    </div>
                    <p class="wizard-panel-sub">Choose the internal storage of your device:</p>

                    <div class="variant-chips-grid">
                        <div class="variant-chip active" data-storage="128 GB" data-ram="6 GB">
                            <div class="variant-storage">128 GB</div>
                            <div class="variant-ram">Standard Capacity</div>
                        </div>
                        <div class="variant-chip" data-storage="256 GB" data-ram="6 GB">
                            <div class="variant-storage">256 GB</div>
                            <div class="variant-ram">High Capacity</div>
                        </div>
                        <div class="variant-chip" data-storage="512 GB" data-ram="6 GB">
                            <div class="variant-storage">512 GB</div>
                            <div class="variant-ram">Pro Capacity</div>
                        </div>
                        <div class="variant-chip" data-storage="1 TB" data-ram="6 GB">
                            <div class="variant-storage">1 TB</div>
                            <div class="variant-ram">Max Capacity</div>
                        </div>
                    </div>

                    <div class="wizard-nav-btns">
                        <button type="button" class="btn-back-step" data-goto="1">&larr; Change Model</button>
                        <button type="button" class="btn btn-primary" id="btn-confirm-variant">Continue to Condition &rarr;</button>
                    </div>
                </div>

                <!-- STEP 3: CONDITION QUESTIONNAIRE -->
                <div class="wizard-panel" id="wizard-step-3">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                        <h3 class="wizard-panel-title" style="margin-bottom:0;">3. Tell Us About Your iPhone</h3>
                        <span class="badge" id="badge-selected-spec" style="font-size:0.8125rem; font-weight:700; color:var(--color-cta);">Specs</span>
                    </div>
                    <p class="wizard-panel-sub">Answer 4 quick questions about physical and functional condition:</p>

                    <!-- Question 1 -->
                    <div class="condition-group">
                        <div class="condition-title">1. Screen Condition</div>
                        <div class="condition-desc">Are there any cracks, deep scratches, dead pixels or lines on display?</div>
                        <div class="radio-options-row">
                            <label class="radio-option-label">
                                <input type="radio" name="q_screen" value="no" checked>
                                <span>No (Flawless / Minor Scratches)</span>
                            </label>
                            <label class="radio-option-label">
                                <input type="radio" name="q_screen" value="yes">
                                <span>Yes (Cracked / Display Defect)</span>
                            </label>
                        </div>
                    </div>

                    <!-- Question 2 -->
                    <div class="condition-group">
                        <div class="condition-title">2. Body / Glass Condition</div>
                        <div class="condition-desc">Are there dents, bent frame or broken back glass?</div>
                        <div class="radio-options-row">
                            <label class="radio-option-label">
                                <input type="radio" name="q_body" value="no" checked>
                                <span>No (Good / Minor Wear)</span>
                            </label>
                            <label class="radio-option-label">
                                <input type="radio" name="q_body" value="yes">
                                <span>Yes (Dents / Cracked Back Glass)</span>
                            </label>
                        </div>
                    </div>

                    <!-- Question 3 -->
                    <div class="condition-group">
                        <div class="condition-title">3. Apple Official Warranty</div>
                        <div class="condition-desc">Is your iPhone currently under active Apple official warranty?</div>
                        <div class="radio-options-row">
                            <label class="radio-option-label">
                                <input type="radio" name="q_warranty" value="yes" checked>
                                <span>Yes (Valid Warranty with Bill)</span>
                            </label>
                            <label class="radio-option-label">
                                <input type="radio" name="q_warranty" value="no">
                                <span>No (Out of Warranty)</span>
                            </label>
                        </div>
                    </div>

                    <!-- Question 4 -->
                    <div class="condition-group">
                        <div class="condition-title">4. Functional Status (Face ID, Calls, Cameras, Charging)</div>
                        <div class="condition-desc">Do all cameras, Face ID, earpiece, speakers and charging port work properly?</div>
                        <div class="radio-options-row">
                            <label class="radio-option-label">
                                <input type="radio" name="q_calls" value="yes" checked>
                                <span>Yes (100% Fully Functional)</span>
                            </label>
                            <label class="radio-option-label">
                                <input type="radio" name="q_calls" value="no">
                                <span>No (Has Functional Flaw)</span>
                            </label>
                        </div>
                    </div>

                    <div class="wizard-nav-btns">
                        <button type="button" class="btn-back-step" data-goto="2">&larr; Change Storage</button>
                        <button type="button" class="btn btn-primary" id="btn-calculate-quote">Get Estimated Value &rarr;</button>
                    </div>
                </div>

                <!-- STEP 4: QUOTE RESULT -->
                <div class="wizard-panel" id="wizard-step-4">
                    <div class="quote-result-box">
                        <span class="section-eyebrow">Your Estimated Valuation</span>
                        <h3 class="quote-device-name" id="quote-display-device">Apple iPhone 15</h3>
                        <p class="quote-device-specs" id="quote-display-variant">128 GB Storage • Flawless Condition</p>

                        <div class="quote-price-tag" id="quote-display-price">₹39,500*</div>
                        <p class="quote-disclaimer">*Estimated value. Final value confirmed upon physical inspection if you decide to sell. Check first, decide later.</p>
                    </div>

                    <div class="quote-actions-row">
                        <a href="#" class="btn btn-whatsapp btn-full" id="quote-whatsapp-btn" target="_blank" rel="noopener noreferrer">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
                            <span>WhatsApp Us This Quote</span>
                        </a>
                        <button type="button" class="btn btn-primary btn-full" id="quote-form-btn">
                            <span>Book Pickup via Form</span>
                        </button>
                    </div>

                    <div class="text-center" style="margin-top:16px;">
                        <button type="button" class="btn-back-step" data-goto="1">&larr; Value Another iPhone</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     3. TRUST CARDS ("Why Sell Your iPhone With Us?")
     ============================================================ -->
<section class="section-container" id="why-us">
    <div class="container">
        <div class="section-header text-center">
            <span class="section-eyebrow">Why CashSecond</span>
            <h2 class="section-title">Why Sell Your iPhone With Us?</h2>
            <p class="section-subtitle">We make iPhone resale straightforward, reliable and secure from start to finish.</p>
        </div>

        <div class="trust-cards-grid">
            <!-- Card 1 -->
            <div class="trust-card">
                <div class="trust-icon-box">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
                <h3 class="trust-card-title">Fair iPhone Valuation</h3>
                <p class="trust-card-desc">Transparent pricing based on your iPhone model, storage and condition.</p>
            </div>

            <!-- Card 2 -->
            <div class="trust-card">
                <div class="trust-icon-box">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <h3 class="trust-card-title">32-Point Inspection</h3>
                <p class="trust-card-desc">Every iPhone is carefully checked before the final offer is confirmed.</p>
            </div>

            <!-- Card 3 -->
            <div class="trust-card">
                <div class="trust-icon-box">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                </div>
                <h3 class="trust-card-title">Doorstep Pickup</h3>
                <p class="trust-card-desc">Sell your iPhone conveniently without unnecessary store visits.</p>
            </div>

            <!-- Card 4 -->
            <div class="trust-card">
                <div class="trust-icon-box">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                </div>
                <h3 class="trust-card-title">Secure & Fast Payment</h3>
                <p class="trust-card-desc">Get paid securely after successful verification via UPI, IMPS or Cash.</p>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     4. IPHONE MODEL SERIES SECTION
     ============================================================ -->
<section class="section-container" id="models" style="background-color: #FFFFFF; border-top: 1px solid var(--color-border); border-bottom: 1px solid var(--color-border);">
    <div class="container">
        <div class="section-header text-center">
            <span class="section-eyebrow">Supported Generations</span>
            <h2 class="section-title">Which iPhone Are You Selling?</h2>
            <p class="section-subtitle">Select your iPhone series to get an instant valuation.</p>
        </div>

        <div class="series-grid">
            <!-- iPhone 16 Series -->
            <div class="series-card" data-series="16">
                <div class="series-icon-circle">📱</div>
                <h3 class="series-name">iPhone 16 Series</h3>
                <p class="series-sub">16 Pro Max, 16 Pro, 16, 16e</p>
                <span class="series-cta-link">Check Value &rarr;</span>
            </div>

            <!-- iPhone 15 Series -->
            <div class="series-card" data-series="15">
                <div class="series-icon-circle">📱</div>
                <h3 class="series-name">iPhone 15 Series</h3>
                <p class="series-sub">15 Pro Max, 15 Pro, 15 Plus, 15</p>
                <span class="series-cta-link">Check Value &rarr;</span>
            </div>

            <!-- iPhone 14 Series -->
            <div class="series-card" data-series="14">
                <div class="series-icon-circle">📱</div>
                <h3 class="series-name">iPhone 14 Series</h3>
                <p class="series-sub">14 Pro Max, 14 Pro, 14 Plus, 14</p>
                <span class="series-cta-link">Check Value &rarr;</span>
            </div>

            <!-- iPhone 13 Series -->
            <div class="series-card" data-series="13">
                <div class="series-icon-circle">📱</div>
                <h3 class="series-name">iPhone 13 Series</h3>
                <p class="series-sub">13 Pro Max, 13 Pro, 13, 13 Mini</p>
                <span class="series-cta-link">Check Value &rarr;</span>
            </div>

            <!-- iPhone 12 Series -->
            <div class="series-card" data-series="12">
                <div class="series-icon-circle">📱</div>
                <h3 class="series-name">iPhone 12 Series</h3>
                <p class="series-sub">12 Pro Max, 12 Pro, 12, 12 Mini</p>
                <span class="series-cta-link">Check Value &rarr;</span>
            </div>

            <!-- iPhone 11 & Older Series -->
            <div class="series-card" data-series="11">
                <div class="series-icon-circle">📱</div>
                <h3 class="series-name">iPhone 11 & Older</h3>
                <p class="series-sub">11 Series, XR, XS, X, 8, SE</p>
                <span class="series-cta-link">Check Value &rarr;</span>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     5. TRUST PROOF STRIP
     ============================================================ -->
<div class="trust-proof-strip">
    <div class="container">
        <div class="proof-items-row">
            <div class="proof-item">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                <span>Transparent Process</span>
            </div>
            <div class="proof-item">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                <span>32-Point Inspection</span>
            </div>
            <div class="proof-item">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                <span>Doorstep Pickup</span>
            </div>
            <div class="proof-item">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                <span>Secure Payment</span>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================
     6. "YOUR IPHONE IS SAFE WITH US" SECTION
     ============================================================ -->
<section class="section-container" id="security">
    <div class="container">
        <div class="section-header text-center">
            <span class="section-eyebrow">Data & Security</span>
            <h2 class="section-title">Your iPhone. Your Data. Your Peace of Mind.</h2>
            <p class="section-subtitle">We believe selling your iPhone should be simple, transparent and secure.</p>
        </div>

        <div class="security-grid">
            <div class="security-card">
                <div class="security-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <h3 class="security-title">Secure Handling</h3>
                <p class="security-desc">Your device is handled responsibly throughout the evaluation and handover process.</p>
            </div>

            <div class="security-card">
                <div class="security-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                </div>
                <h3 class="security-title">Data Protection</h3>
                <p class="security-desc">Your personal information and device data are treated responsibly with full factory reset guidance.</p>
            </div>

            <div class="security-card">
                <div class="security-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                </div>
                <h3 class="security-title">Professional Verification</h3>
                <p class="security-desc">Your iPhone goes through proper diagnostic inspection before final offer confirmation.</p>
            </div>

            <div class="security-card">
                <div class="security-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                </div>
                <h3 class="security-title">Transparent Offer</h3>
                <p class="security-desc">You understand the valuation and inspection process clearly before completing the sale.</p>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     7. 32-POINT IPHONE INSPECTION SECTION
     ============================================================ -->
<section class="section-container inspection-section" id="inspection">
    <div class="container">
        <div class="section-header text-center">
            <span class="section-eyebrow">Diagnostic Standards</span>
            <h2 class="section-title">Your iPhone Goes Through a 32-Point Inspection</h2>
            <p class="section-subtitle">Every iPhone is carefully checked across important hardware, software and functionality before the final valuation is confirmed.</p>
        </div>

        <div class="inspection-grid">
            <div class="inspection-item">
                <span class="inspection-check-icon">✓</span>
                <span>Display & TrueTone</span>
            </div>
            <div class="inspection-item">
                <span class="inspection-check-icon">✓</span>
                <span>Battery Health & Cycles</span>
            </div>
            <div class="inspection-item">
                <span class="inspection-check-icon">✓</span>
                <span>Camera & Optical Zoom</span>
            </div>
            <div class="inspection-item">
                <span class="inspection-check-icon">✓</span>
                <span>Face ID / Touch ID</span>
            </div>
            <div class="inspection-item">
                <span class="inspection-check-icon">✓</span>
                <span>Stereo Speakers</span>
            </div>
            <div class="inspection-item">
                <span class="inspection-check-icon">✓</span>
                <span>Microphone & Noise Cancel</span>
            </div>
            <div class="inspection-item">
                <span class="inspection-check-icon">✓</span>
                <span>Charging Port & Current</span>
            </div>
            <div class="inspection-item">
                <span class="inspection-check-icon">✓</span>
                <span>Physical Buttons & Switch</span>
            </div>
            <div class="inspection-item">
                <span class="inspection-check-icon">✓</span>
                <span>Body & Glass Condition</span>
            </div>
            <div class="inspection-item">
                <span class="inspection-check-icon">✓</span>
                <span>IMEI / Device Verification</span>
            </div>
            <div class="inspection-item">
                <span class="inspection-check-icon">✓</span>
                <span>5G & Wi-Fi Connectivity</span>
            </div>
            <div class="inspection-item">
                <span class="inspection-check-icon">✓</span>
                <span>Overall Functionality</span>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     8. HOW IT WORKS & PROCESS FLOW
     ============================================================ -->
<section class="section-container" id="how-it-works">
    <div class="container">
        <div class="section-header text-center">
            <span class="section-eyebrow">Hassle-Free Flow</span>
            <h2 class="section-title">Selling Your iPhone Is Simple</h2>
            <p class="section-subtitle">Complete your resale journey in three effortless steps.</p>
        </div>

        <div class="how-it-works-grid">
            <div class="how-step-card">
                <div class="how-step-num">STEP 01</div>
                <h3 class="how-step-title">Tell Us About Your iPhone</h3>
                <p class="how-step-desc">Select your model, storage and condition.</p>
            </div>

            <div class="how-step-card">
                <div class="how-step-num">STEP 02</div>
                <h3 class="how-step-title">Get Your Offer</h3>
                <p class="how-step-desc">Receive an estimated value based on your iPhone details.</p>
            </div>

            <div class="how-step-card">
                <div class="how-step-num">STEP 03</div>
                <h3 class="how-step-title">Verify & Get Paid</h3>
                <p class="how-step-desc">Complete verification, pickup and payment.</p>
            </div>
        </div>

        <!-- Transparent Process Flow Bar -->
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

        <!-- What Happens After You Submit -->
        <div class="after-submit-card">
            <h4 class="after-submit-title">What Happens After You Submit Your Details?</h4>
            <ol class="after-submit-list">
                <li><span class="bullet-num">1.</span> We review your iPhone information.</li>
                <li><span class="bullet-num">2.</span> Your device is inspected.</li>
                <li><span class="bullet-num">3.</span> The final offer is confirmed.</li>
                <li><span class="bullet-num">4.</span> Pickup is completed.</li>
                <li><span class="bullet-num">5.</span> Payment is processed immediately.</li>
            </ol>
        </div>
    </div>
</section>

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
            <!-- Review 1 -->
            <div class="review-card">
                <div class="review-stars">★★★★★</div>
                <p class="review-text">"Sold my iPhone 14 Pro directly through CashSecond. The executive arrived in Bandra within 2 hours, verified the screen and battery health, and transferred the agreed amount on Google Pay immediately. Super transparent!"</p>
                <div class="review-author">
                    <span class="author-name">Rahul S. • Bandra West</span>
                    <span class="author-model">iPhone 14 Pro 128GB</span>
                </div>
            </div>

            <!-- Review 2 -->
            <div class="review-card">
                <div class="review-stars">★★★★★</div>
                <p class="review-text">"Great experience getting a value for my iPhone 13. Very honest valuation with no last-minute deductions since my condition answers were accurate. The pickup executive was very polite."</p>
                <div class="review-author">
                    <span class="author-name">Pooja M. • Andheri</span>
                    <span class="author-model">iPhone 13 128GB</span>
                </div>
            </div>

            <!-- Review 3 -->
            <div class="review-card">
                <div class="review-stars">★★★★★</div>
                <p class="review-text">"Upgraded to iPhone 16 and sold my iPhone 15 Pro Max here. Seamless doorstep pickup in Nariman Point and on-spot payment. Definitely recommended for anyone selling an iPhone."</p>
                <div class="review-author">
                    <span class="author-name">Anand K. • Nariman Point</span>
                    <span class="author-model">iPhone 15 Pro Max 256GB</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     10. ABOUT / COMPANY TRUST
     ============================================================ -->
<section class="section-container" id="about">
    <div class="container">
        <div class="about-trust-card">
            <span class="section-eyebrow" style="color: #60a5fa;">About CashSecond</span>
            <h2>A Better Way to Sell Your iPhone</h2>
            <p>
                We make selling your used iPhone simple, transparent and secure — from valuation and verification to doorstep pickup and payment. Operating from Nariman Point, Mumbai with dedicated device specialists.
            </p>
            <div style="display:flex; justify-content:center; gap:12px; flex-wrap:wrap;">
                <a href="#valuation" class="btn btn-primary">Check My iPhone Value</a>
                <a href="https://wa.me/<?= htmlspecialchars($business['whatsapp_number'] ?? '918976332211'); ?>?text=<?= urlencode('Hi CashSecond, I want to sell my iPhone.'); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-whatsapp">WhatsApp Us</a>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     11. FAQ ACCORDION SECTION
     ============================================================ -->
<section class="section-container" id="faq">
    <div class="container faq-container">
        <div class="section-header text-center">
            <span class="section-eyebrow">Clear Answers</span>
            <h2 class="section-title">Frequently Asked Questions</h2>
            <p class="section-subtitle">Everything you need to know about our iPhone valuation, pickup and payment.</p>
        </div>

        <div class="faq-accordion" id="faq-accordion">
            <!-- FAQ 1 -->
            <div class="faq-item active">
                <button type="button" class="faq-btn" aria-expanded="true">
                    <span>How is my iPhone's value calculated?</span>
                    <span class="faq-icon">+</span>
                </button>
                <div class="faq-content">
                    <p>Your iPhone's estimated value is calculated based on its exact model, storage capacity, physical screen/body condition, functional checks (Face ID, cameras, battery health) and current pre-owned market rates.</p>
                </div>
            </div>

            <!-- FAQ 2 -->
            <div class="faq-item">
                <button type="button" class="faq-btn" aria-expanded="false">
                    <span>Do you provide doorstep pickup?</span>
                    <span class="faq-icon">+</span>
                </button>
                <div class="faq-content">
                    <p>Yes. We offer convenient, free doorstep executive pickup across Mumbai, Navi Mumbai and Thane at your preferred date and time slot.</p>
                </div>
            </div>

            <!-- FAQ 3 -->
            <div class="faq-item">
                <button type="button" class="faq-btn" aria-expanded="false">
                    <span>When will I receive payment?</span>
                    <span class="faq-icon">+</span>
                </button>
                <div class="faq-content">
                    <p>You receive instant, on-spot payment immediately after the 5-minute diagnostic inspection at your doorstep via UPI (Google Pay / PhonePe / Paytm), instant Bank Transfer (IMPS), or Cash.</p>
                </div>
            </div>

            <!-- FAQ 4 -->
            <div class="faq-item">
                <button type="button" class="faq-btn" aria-expanded="false">
                    <span>What happens if the inspection changes the estimated value?</span>
                    <span class="faq-icon">+</span>
                </button>
                <div class="faq-content">
                    <p>If undisclosed flaws (such as screen burn-in, non-genuine parts, or functional issues) are detected during testing, the executive will explain the diagnostic findings and provide a revised fair offer. You are under no obligation to sell and can decline with zero penalty.</p>
                </div>
            </div>

            <!-- FAQ 5 -->
            <div class="faq-item">
                <button type="button" class="faq-btn" aria-expanded="false">
                    <span>How is my personal data handled?</span>
                    <span class="faq-icon">+</span>
                </button>
                <div class="faq-content">
                    <p>Your personal data is protected with utmost responsibility. Our executive ensures your iCloud account is signed out and guides you through a complete factory reset (Erase All Content and Settings) in front of you before handover.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     12. FINAL CTA & BOOKING FORM
     ============================================================ -->
<section class="final-cta-section" id="enquire">
    <div class="container">
        <div class="section-header text-center">
            <span class="section-eyebrow">Get Started</span>
            <h2 class="section-title">Ready to Sell Your iPhone?</h2>
            <p class="section-subtitle">Get your iPhone valued today with a simple and transparent process.</p>
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
                    <input type="text" id="form_phone_model" name="phone_model" class="form-control" placeholder="e.g. iPhone 15 Pro 128GB" required>
                </div>

                <div class="form-group">
                    <label for="form_message" class="form-label">Condition Notes / Preferred Pickup Time (Optional)</label>
                    <textarea id="form_message" name="message" class="form-control" rows="2" placeholder="Mention battery health, warranty or preferred time for executive pickup..."></textarea>
                </div>

                <div class="form-consent-row">
                    <input type="checkbox" name="consent" id="form_consent" required checked>
                    <label for="form_consent">By submitting your details, you agree to our <a href="policies/terms.php" target="_blank">Terms &amp; Conditions</a> and acknowledge our <a href="policies/privacy-policy.php" target="_blank">Privacy Policy</a>.</label>
                </div>

                <button type="submit" class="btn btn-primary btn-lg btn-full" id="form-submit-btn">
                    <span>Check My iPhone Value</span>
                </button>
                <p class="text-center text-muted" style="font-size: 0.8125rem; margin-top: 10px;">Check first. Decide later.</p>

                <!-- Status Feedback Alert -->
                <div id="form-status-alert" class="form-status-alert"></div>
            </form>
        </div>
    </div>
</section>

<!-- ============================================================
     13. CONTACT INFORMATION & STORE DETAILS
     ============================================================ -->
<section class="section-container" id="contact" style="background-color: var(--color-bg-page);">
    <div class="container">
        <div class="section-header text-center">
            <span class="section-eyebrow">Reach Us</span>
            <h2 class="section-title">Contact CashSecond</h2>
            <p class="section-subtitle">Visit our Nariman Point office or speak with our Mumbai support team directly.</p>
        </div>

        <div class="contact-grid">
            <!-- Contact Card -->
            <div class="contact-card">
                <h3 style="font-size: 1.1875rem; font-weight: 700; margin-bottom: 14px; color: var(--color-dark);">Official Office Details</h3>

                <div class="contact-item">
                    <span class="contact-icon">📍</span>
                    <div>
                        <strong style="color: var(--color-dark);">Address:</strong><br>
                        Office Number 1307, 13th Floor, Arcadia Building,<br>
                        NCPA Marg, Nariman Point, Mumbai – 400021
                    </div>
                </div>

                <div class="contact-item">
                    <span class="contact-icon">📞</span>
                    <div>
                        <strong style="color: var(--color-dark);">Phone Support:</strong><br>
                        <a href="tel:+918976332211" style="color: var(--color-cta); font-weight: 600;">+91 897633 2211</a>
                    </div>
                </div>

                <div class="contact-item">
                    <span class="contact-icon">💬</span>
                    <div>
                        <strong style="color: var(--color-dark);">WhatsApp:</strong><br>
                        <a href="https://wa.me/918976332211?text=Hi+CashSecond%2C+I+want+to+sell+my+iPhone." target="_blank" rel="noopener noreferrer" style="color: var(--color-whatsapp); font-weight: 600;">+91 897633 2211 (Instant Chat)</a>
                    </div>
                </div>

                <div class="contact-item">
                    <span class="contact-icon">✉️</span>
                    <div>
                        <strong style="color: var(--color-dark);">Email:</strong><br>
                        <a href="mailto:cashsecondofficial@gmail.com" style="color: var(--color-cta); font-weight: 600;">cashsecondofficial@gmail.com</a>
                    </div>
                </div>

                <div class="contact-item">
                    <span class="contact-icon">🕒</span>
                    <div>
                        <strong style="color: var(--color-dark);">Business Hours:</strong><br>
                        Monday to Sunday: 10:00 AM to 9:00 PM
                    </div>
                </div>
            </div>

            <!-- Google Maps Embed -->
            <div class="contact-card" style="padding: 0; overflow: hidden;">
                <iframe 
                    class="contact-map-embed"
                    title="CashSecond Nariman Point Mumbai Location"
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3773.809088661623!2d72.825833!3d18.928000!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3be7d1e8a2a9a8d7%3A0x1d368d30e52bca1!2sArcadia%20Building%2C%20NCPA%20Marg%2C%20Nariman%20Point%2C%20Mumbai%2C%20Maharashtra%20400021!5e0!3m2!1sen!2sin!4v1600000000000!5m2!1sen!2sin" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>
    </div>
</section>

<?php 
// Include Footer
require_once __DIR__ . '/includes/footer.php'; 
?>
