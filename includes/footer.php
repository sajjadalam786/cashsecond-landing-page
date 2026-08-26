<?php
/**
 * CashSecond - Footer Template & Mobile Sticky Conversion Bar
 * Apple-Inspired Minimalist Design with Verified Contact Information
 */
if (!isset($config)) {
    $config = require __DIR__ . '/../config/config.php';
}

$business = $config['business'] ?? [];
$current_year = date('Y');
?>
    <!-- Main Apple-Inspired Footer -->
    <footer class="site-footer">
        <div class="container footer-inner">
            <!-- Brand & Location -->
            <div class="footer-brand-col">
                <a href="#top" class="footer-brand-logo" aria-label="CashSecond Home">
                    <img src="assets/images/cashsecond-logo.png" alt="CashSecond - Best Value For Your iPhone" class="footer-logo-img" width="420" height="140" loading="lazy">
                </a>
                <p>Office 1307, 13th Floor, Arcadia Building, NCPA Marg, Nariman Point, Mumbai – 400021</p>
                <p style="margin-top: 4px;">Phone: <a href="tel:+918976332211" style="color: var(--color-cta);">+91 897633 2211</a> • Email: <a href="mailto:cashsecondofficial@gmail.com" style="color: var(--color-cta);">cashsecondofficial@gmail.com</a></p>
            </div>

            <!-- Quick Navigation & Legal Policy Links -->
            <div class="footer-links">
                <a href="#top">Sell iPhone</a>
                <a href="#valuation">iPhone Resale Value</a>
                <a href="#models">Sell iPhone Models</a>
                <a href="#showcase">Model Showcase</a>
                <a href="#why-us">Why Sell With Us</a>
                <a href="#inspection">32-Point Diagnostics</a>
                <a href="#how-it-works">How It Works</a>
                <a href="#faq">iPhone Resale FAQs</a>
                <a href="#contact">Contact Store</a>
                <a href="policies/privacy-policy.php">Privacy Policy</a>
                <a href="policies/terms.php">Terms &amp; Conditions</a>
                <a href="policies/buyback-policy.php">Buyback Policy</a>
                <a href="policies/cookie-policy.php">Cookie Policy</a>
                <a href="policies/disclaimer.php">Disclaimer</a>
            </div>
        </div>

        <div class="container">
            <p class="trademark-disclaimer">
                <strong>Disclaimer:</strong> Apple, iPhone, iOS, Retina, Face ID, Dynamic Island, and Apple Intelligence are registered trademarks of Apple Inc. Their mention on this website is strictly for product identification, technical compatibility, and trade reference. CashSecond is an independent pre-owned electronics valuation and buyback platform and is not affiliated with Apple Inc. &copy; <?= $current_year; ?> CashSecond. All rights reserved.
            </p>
        </div>
    </footer>

    <!-- Mobile Sticky Conversion Bar -->
    <div class="mobile-sticky-bar" aria-label="Quick Actions">
        <div class="mobile-sticky-inner">
            <span class="mobile-sticky-subtext">Free • 60 sec • No obligation</span>
            <a href="#valuation" class="btn btn-primary btn-full" id="mobile-sticky-valuation-btn" aria-haspopup="dialog" aria-controls="buybackQuestionnaireModal">
                <span>CHECK MY IPHONE VALUE &rarr;</span>
            </a>
        </div>
    </div>

    <!-- ============================================================
         HIGH-CONVERTING MULTI-STEP iPHONE VALUATION POPUP (4 STEPS)
         ============================================================ -->
    <div class="lead-modal-overlay" id="lead-popup-modal" aria-modal="true" role="dialog" aria-labelledby="popup-modal-heading">
        <div class="lead-modal-backdrop" id="lead-modal-backdrop"></div>
        <div class="lead-modal-card" id="lead-modal-card">
            <!-- Close Button -->
            <button type="button" class="lead-modal-close-btn" id="lead-modal-close-btn" aria-label="Close popup">&times;</button>

            <!-- Progress Header (Step 1 of 4) -->
            <div class="popup-progress-header" id="popup-progress-header">
                <div class="popup-step-counter" id="popup-step-counter">Step 1 of 4</div>
                <div class="popup-progress-dots" id="popup-progress-dots" aria-hidden="true">
                    <span class="prog-dot active" data-step="1" title="Model"></span>
                    <span class="prog-dot" data-step="2" title="Storage"></span>
                    <span class="prog-dot" data-step="3" title="Condition"></span>
                    <span class="prog-dot" data-step="4" title="Contact"></span>
                </div>
            </div>

            <!-- Header Content -->
            <div class="lead-modal-header text-center" id="popup-header-block">
                <span class="lead-modal-badge" id="popup-badge">GET YOUR IPHONE VALUE</span>
                <h2 class="lead-modal-title" id="popup-modal-heading">How Much Is Your iPhone Worth?</h2>
                <p class="lead-modal-desc" id="popup-modal-subheading">Get your estimated resale value in under 60 seconds.</p>
                <div class="popup-trust-badge-line" id="popup-trust-line">
                    <span>✓ Free valuation</span>
                    <span>•</span>
                    <span>✓ No obligation</span>
                    <span>•</span>
                    <span>✓ Instant payout</span>
                </div>
            </div>

            <!-- Multi-Step Form Body -->
            <form id="popup-lead-form" class="lead-modal-form" novalidate>
                <!-- CSRF, Honeypot & Hidden Metadata -->
                <input type="hidden" name="csrf_token" id="popup_csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                <input type="text" name="website_hp" style="display:none !important;" tabindex="-1" autocomplete="off">
                <input type="hidden" name="consent" value="1">
                <input type="hidden" name="page_source" value="CashSecond Landing Page">
                <input type="hidden" name="utm_source" id="popup_utm_source" value="Direct">
                <input type="hidden" name="utm_medium" id="popup_utm_medium" value="None">
                <input type="hidden" name="utm_campaign" id="popup_utm_campaign" value="None">

                <!-- Hidden Values Captured via Multi-Step Selection -->
                <input type="hidden" name="phone_model" id="popup_hidden_model" value="Apple iPhone 16 Pro">
                <input type="hidden" name="storage" id="popup_hidden_storage" value="128 GB">
                <input type="hidden" name="condition" id="popup_hidden_condition" value="Good">
                <input type="hidden" name="estimated_value" id="popup_hidden_est_val" value="">

                <!-- ==========================================
                     STEP 1: SELECT iPHONE MODEL (Live Filter & Grid)
                     ========================================== -->
                <div class="popup-step-panel active" id="popup-panel-1" data-step="1">
                    <div class="popup-step-intro">
                        <h3 class="popup-step-title">Which iPhone do you want to sell?</h3>
                        <p class="popup-step-subtitle">Select your Apple iPhone model below or search:</p>
                    </div>

                    <!-- Search Filter Input -->
                    <div class="popup-search-box">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <input type="text" id="popup-model-search-input" class="popup-search-input" placeholder="Search model (e.g. iPhone 15, 16 Pro)..." autocomplete="off">
                        <button type="button" id="popup-search-clear" class="popup-search-clear-btn" aria-label="Clear search" style="display:none;">&times;</button>
                    </div>

                    <!-- Scrollable Model Grid -->
                    <div class="popup-models-grid" id="popup-models-grid">
                        <!-- Flagship 16 Series -->
                        <button type="button" class="popup-model-card" data-name="Apple iPhone 16 Pro Max" data-image="assets/images/phones/iphone-16-pro.svg" data-series="16">
                            <img src="assets/images/phones/iphone-16-pro.svg" alt="Sell iPhone 16 Pro Max" width="32" height="40" loading="lazy">
                            <span class="popup-model-name">Sell iPhone 16 Pro Max</span>
                        </button>
                        <button type="button" class="popup-model-card" data-name="Apple iPhone 16 Pro" data-image="assets/images/phones/iphone-16-pro.svg" data-series="16">
                            <img src="assets/images/phones/iphone-16-pro.svg" alt="Sell iPhone 16 Pro" width="32" height="40" loading="lazy">
                            <span class="popup-model-name">Sell iPhone 16 Pro</span>
                        </button>
                        <button type="button" class="popup-model-card" data-name="Apple iPhone 16 Plus" data-image="assets/images/phones/iphone-16.svg" data-series="16">
                            <img src="assets/images/phones/iphone-16.svg" alt="Sell iPhone 16 Plus" width="32" height="40" loading="lazy">
                            <span class="popup-model-name">Sell iPhone 16 Plus</span>
                        </button>
                        <button type="button" class="popup-model-card" data-name="Apple iPhone 16" data-image="assets/images/phones/iphone-16.svg" data-series="16">
                            <img src="assets/images/phones/iphone-16.svg" alt="Sell iPhone 16" width="32" height="40" loading="lazy">
                            <span class="popup-model-name">Sell iPhone 16</span>
                        </button>

                        <!-- 15 Series -->
                        <button type="button" class="popup-model-card" data-name="Apple iPhone 15 Pro Max" data-image="assets/images/phones/iphone-15-pro.svg" data-series="15">
                            <img src="assets/images/phones/iphone-15-pro.svg" alt="Sell iPhone 15 Pro Max" width="32" height="40" loading="lazy">
                            <span class="popup-model-name">Sell iPhone 15 Pro Max</span>
                        </button>
                        <button type="button" class="popup-model-card" data-name="Apple iPhone 15 Pro" data-image="assets/images/phones/iphone-15-pro.svg" data-series="15">
                            <img src="assets/images/phones/iphone-15-pro.svg" alt="Sell iPhone 15 Pro" width="32" height="40" loading="lazy">
                            <span class="popup-model-name">Sell iPhone 15 Pro</span>
                        </button>
                        <button type="button" class="popup-model-card" data-name="Apple iPhone 15 Plus" data-image="assets/images/phones/iphone-15.svg" data-series="15">
                            <img src="assets/images/phones/iphone-15.svg" alt="Sell iPhone 15 Plus" width="32" height="40" loading="lazy">
                            <span class="popup-model-name">Sell iPhone 15 Plus</span>
                        </button>
                        <button type="button" class="popup-model-card" data-name="Apple iPhone 15" data-image="assets/images/phones/iphone-15.svg" data-series="15">
                            <img src="assets/images/phones/iphone-15.svg" alt="Sell iPhone 15" width="32" height="40" loading="lazy">
                            <span class="popup-model-name">Sell iPhone 15</span>
                        </button>

                        <!-- 14 Series -->
                        <button type="button" class="popup-model-card" data-name="Apple iPhone 14 Pro Max" data-image="assets/images/phones/iphone-14-pro.svg" data-series="14">
                            <img src="assets/images/phones/iphone-14-pro.svg" alt="Sell iPhone 14 Pro Max" width="32" height="40" loading="lazy">
                            <span class="popup-model-name">Sell iPhone 14 Pro Max</span>
                        </button>
                        <button type="button" class="popup-model-card" data-name="Apple iPhone 14 Pro" data-image="assets/images/phones/iphone-14-pro.svg" data-series="14">
                            <img src="assets/images/phones/iphone-14-pro.svg" alt="Sell iPhone 14 Pro" width="32" height="40" loading="lazy">
                            <span class="popup-model-name">Sell iPhone 14 Pro</span>
                        </button>
                        <button type="button" class="popup-model-card" data-name="Apple iPhone 14 Plus" data-image="assets/images/phones/iphone-14.svg" data-series="14">
                            <img src="assets/images/phones/iphone-14.svg" alt="Sell iPhone 14 Plus" width="32" height="40" loading="lazy">
                            <span class="popup-model-name">Sell iPhone 14 Plus</span>
                        </button>
                        <button type="button" class="popup-model-card" data-name="Apple iPhone 14" data-image="assets/images/phones/iphone-14.svg" data-series="14">
                            <img src="assets/images/phones/iphone-14.svg" alt="Sell iPhone 14" width="32" height="40" loading="lazy">
                            <span class="popup-model-name">Sell iPhone 14</span>
                        </button>

                        <!-- 13 Series -->
                        <button type="button" class="popup-model-card" data-name="Apple iPhone 13 Pro Max" data-image="assets/images/phones/iphone-13.svg" data-series="13">
                            <img src="assets/images/phones/iphone-13.svg" alt="Sell iPhone 13 Pro Max" width="32" height="40" loading="lazy">
                            <span class="popup-model-name">Sell iPhone 13 Pro Max</span>
                        </button>
                        <button type="button" class="popup-model-card" data-name="Apple iPhone 13 Pro" data-image="assets/images/phones/iphone-13.svg" data-series="13">
                            <img src="assets/images/phones/iphone-13.svg" alt="Sell iPhone 13 Pro" width="32" height="40" loading="lazy">
                            <span class="popup-model-name">Sell iPhone 13 Pro</span>
                        </button>
                        <button type="button" class="popup-model-card" data-name="Apple iPhone 13" data-image="assets/images/phones/iphone-13.svg" data-series="13">
                            <img src="assets/images/phones/iphone-13.svg" alt="Sell iPhone 13" width="32" height="40" loading="lazy">
                            <span class="popup-model-name">Sell iPhone 13</span>
                        </button>
                        <button type="button" class="popup-model-card" data-name="Apple iPhone 13 Mini" data-image="assets/images/phones/iphone-13.svg" data-series="13">
                            <img src="assets/images/phones/iphone-13.svg" alt="Sell iPhone 13 Mini" width="32" height="40" loading="lazy">
                            <span class="popup-model-name">Sell iPhone 13 Mini</span>
                        </button>

                        <!-- 12 Series & Earlier -->
                        <button type="button" class="popup-model-card" data-name="Apple iPhone 12 Pro Max" data-image="assets/images/phones/iphone-12.svg" data-series="12">
                            <img src="assets/images/phones/iphone-12.svg" alt="Sell iPhone 12 Pro Max" width="32" height="40" loading="lazy">
                            <span class="popup-model-name">Sell iPhone 12 Pro Max</span>
                        </button>
                        <button type="button" class="popup-model-card" data-name="Apple iPhone 12 Pro" data-image="assets/images/phones/iphone-12.svg" data-series="12">
                            <img src="assets/images/phones/iphone-12.svg" alt="Sell iPhone 12 Pro" width="32" height="40" loading="lazy">
                            <span class="popup-model-name">Sell iPhone 12 Pro</span>
                        </button>
                        <button type="button" class="popup-model-card" data-name="Apple iPhone 12" data-image="assets/images/phones/iphone-12.svg" data-series="12">
                            <img src="assets/images/phones/iphone-12.svg" alt="Sell iPhone 12" width="32" height="40" loading="lazy">
                            <span class="popup-model-name">Sell iPhone 12</span>
                        </button>
                        <button type="button" class="popup-model-card" data-name="Apple iPhone 11 Series" data-image="assets/images/phones/iphone-11.svg" data-series="11">
                            <img src="assets/images/phones/iphone-11.svg" alt="Sell iPhone 11 Series" width="32" height="40" loading="lazy">
                            <span class="popup-model-name">Sell iPhone 11 Series</span>
                        </button>
                        <button type="button" class="popup-model-card" data-name="Apple iPhone X / Older / SE" data-image="assets/images/phones/iphone-11.svg" data-series="older">
                            <img src="assets/images/phones/iphone-11.svg" alt="Sell iPhone X or Older" width="32" height="40" loading="lazy">
                            <span class="popup-model-name">Sell iPhone X / Older / SE</span>
                        </button>
                    </div>

                    <div class="popup-empty-filter" id="popup-model-empty" style="display:none;">
                        <p>No matching iPhone found. Please search again or choose from the list.</p>
                    </div>
                </div>

                <!-- ==========================================
                     STEP 2: SELECT STORAGE CAPACITY
                     ========================================== -->
                <div class="popup-step-panel" id="popup-panel-2" data-step="2">
                    <div class="popup-step-intro">
                        <div class="popup-selected-badge" id="popup-step2-badge">Selected: Apple iPhone 16 Pro</div>
                        <h3 class="popup-step-title">What's your iPhone's storage?</h3>
                        <p class="popup-step-subtitle">Select the internal storage capacity of your device:</p>
                    </div>

                    <div class="popup-storage-grid" id="popup-storage-grid">
                        <button type="button" class="popup-choice-card storage-card active" data-storage="128 GB">
                            <span class="choice-title">128 GB</span>
                            <span class="choice-sub">Standard</span>
                        </button>
                        <button type="button" class="popup-choice-card storage-card" data-storage="256 GB">
                            <span class="choice-title">256 GB</span>
                            <span class="choice-sub">Most Popular</span>
                        </button>
                        <button type="button" class="popup-choice-card storage-card" data-storage="512 GB">
                            <span class="choice-title">512 GB</span>
                            <span class="choice-sub">High Capacity</span>
                        </button>
                        <button type="button" class="popup-choice-card storage-card" data-storage="1 TB">
                            <span class="choice-title">1 TB</span>
                            <span class="choice-sub">Maximum</span>
                        </button>
                    </div>

                    <div class="popup-panel-nav">
                        <button type="button" class="btn-popup-back" id="popup-back-to-1">&larr; Change Model</button>
                        <button type="button" class="btn btn-primary btn-popup-next" id="popup-next-to-3">Next: Condition &rarr;</button>
                    </div>
                </div>

                <!-- ==========================================
                     STEP 3: SELECT DEVICE CONDITION
                     ========================================== -->
                <div class="popup-step-panel" id="popup-panel-3" data-step="3">
                    <div class="popup-step-intro">
                        <div class="popup-selected-badge" id="popup-step3-badge">Apple iPhone 16 Pro • 128 GB</div>
                        <h3 class="popup-step-title">What's the condition of your iPhone?</h3>
                        <p class="popup-step-subtitle">Choose the description that best matches your device:</p>
                    </div>

                    <div class="popup-condition-grid" id="popup-condition-grid">
                        <!-- LIKE NEW -->
                        <button type="button" class="popup-choice-card condition-card" data-condition="Like New" data-mult="1.0">
                            <div class="choice-badge">Flawless</div>
                            <span class="choice-title">LIKE NEW</span>
                            <span class="choice-sub">Minimal signs of use</span>
                        </button>

                        <!-- GOOD -->
                        <button type="button" class="popup-choice-card condition-card active" data-condition="Good" data-mult="0.88">
                            <div class="choice-badge">Popular</div>
                            <span class="choice-title">GOOD</span>
                            <span class="choice-sub">Normal signs of use</span>
                        </button>

                        <!-- FAIR -->
                        <button type="button" class="popup-choice-card condition-card" data-condition="Fair" data-mult="0.74">
                            <span class="choice-title">FAIR</span>
                            <span class="choice-sub">Visible wear</span>
                        </button>

                        <!-- DAMAGED -->
                        <button type="button" class="popup-choice-card condition-card" data-condition="Damaged" data-mult="0.55">
                            <span class="choice-title">DAMAGED</span>
                            <span class="choice-sub">Screen/body damage or functional issue</span>
                        </button>
                    </div>

                    <div class="popup-panel-nav">
                        <button type="button" class="btn-popup-back" id="popup-back-to-2">&larr; Change Storage</button>
                        <button type="button" class="btn btn-primary btn-popup-next" id="popup-next-to-4">Calculate My Value &rarr;</button>
                    </div>
                </div>

                <!-- ==========================================
                     STEP 4: LIVE VALUE & CONTACT LEAD CAPTURE
                     ========================================== -->
                <div class="popup-step-panel" id="popup-panel-4" data-step="4">
                    <div class="popup-step-intro">
                        <h3 class="popup-step-title">Where should we contact you?</h3>
                        <p class="popup-step-subtitle">Enter your details to continue with your iPhone valuation.</p>
                    </div>

                    <!-- Live Dynamic Estimate Card -->
                    <div class="popup-estimate-card" id="popup-estimate-card">
                        <div class="popup-estimate-label">Estimated Resale Value</div>
                        <div class="popup-estimate-price" id="popup-estimate-price-val">₹54,000 – ₹58,000</div>
                        <div class="popup-estimate-specs" id="popup-estimate-specs-summary">Apple iPhone 16 Pro (128 GB) • Good Condition</div>
                        <div class="popup-estimate-disclaimer">Final value may vary after device verification.</div>
                    </div>

                    <!-- Input Fields (Zero mandatory email!) -->
                    <div class="form-group" style="margin-bottom: 12px;">
                        <label for="popup_full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" id="popup_full_name" name="full_name" class="form-control" placeholder="e.g. Rahul Sharma" required autocomplete="name">
                        <div class="form-error-msg" id="popup-err-name"></div>
                    </div>

                    <div class="form-group" style="margin-bottom: 14px;">
                        <label for="popup_phone_number" class="form-label">Mobile / WhatsApp Number <span class="text-danger">*</span></label>
                        <div class="phone-input-wrapper">
                            <span class="phone-prefix">+91</span>
                            <input type="tel" id="popup_phone_number" name="phone_number" class="form-control phone-input-field" placeholder="98200 12345" required autocomplete="tel" inputmode="tel">
                        </div>
                        <div class="form-error-msg" id="popup-err-phone"></div>
                    </div>

                    <div class="popup-consent-note">
                        By continuing, you agree to our <a href="policies/privacy-policy.php" target="_blank">Privacy Policy</a> and <a href="policies/terms.php" target="_blank">Terms &amp; Conditions</a>.
                    </div>

                    <!-- Status Alert Box -->
                    <div class="form-status-alert" id="popup-form-status" style="display:none;"></div>

                    <!-- Primary High-Converting CTA Button -->
                    <button type="submit" class="btn btn-primary btn-full popup-submit-btn" id="btn-popup-submit">
                        <span>GET MY IPHONE VALUE &rarr;</span>
                    </button>

                    <p class="popup-trust-subtext">🔒 Your details are private • Free valuation • No obligation</p>

                    <div class="text-center" style="margin-top: 10px;">
                        <button type="button" class="btn-popup-back" id="popup-back-to-3">&larr; Change Selection</button>
                    </div>
                </div>

                <!-- ==========================================
                     SUCCESS SCREEN (Shows after submission)
                     ========================================== -->
                <div class="popup-step-panel" id="popup-panel-success" style="display:none;">
                    <div class="popup-success-content text-center">
                        <div class="popup-success-icon" aria-hidden="true">🎉</div>
                        <h3 class="popup-success-title">You're All Set! 🎉</h3>
                        <p class="popup-success-desc">We've received your iPhone details. Our team will contact you shortly regarding your valuation.</p>

                        <!-- Captured Summary Box -->
                        <div class="popup-summary-card">
                            <div class="summary-row">
                                <span class="lbl">iPhone:</span>
                                <span class="val" id="pop-succ-model">Apple iPhone 16 Pro</span>
                            </div>
                            <div class="summary-row">
                                <span class="lbl">Storage:</span>
                                <span class="val" id="pop-succ-storage">128 GB</span>
                            </div>
                            <div class="summary-row">
                                <span class="lbl">Condition:</span>
                                <span class="val" id="pop-succ-condition">Good</span>
                            </div>
                            <div class="summary-row highlight">
                                <span class="lbl">Estimated Value:</span>
                                <span class="val" id="pop-succ-estimate">₹56,500</span>
                            </div>
                        </div>

                        <!-- Direct WhatsApp Action Button -->
                        <a href="#" target="_blank" rel="noopener noreferrer" class="btn btn-whatsapp btn-full" id="popup-wa-continue-btn">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
                            <span>CHAT ON WHATSAPP &rarr;</span>
                        </a>

                        <p class="popup-private-note">Your information is kept private.</p>
                    </div>
                </div>

                <!-- ==========================================
                     FAILURE SCREEN (If submission fails)
                     ========================================== -->
                <div class="popup-step-panel" id="popup-panel-failure" style="display:none;">
                    <div class="popup-failure-content text-center">
                        <div class="popup-fail-icon">⚠️</div>
                        <h3 class="popup-fail-title">Something went wrong.</h3>
                        <p class="popup-fail-desc">Please try again or contact us directly on WhatsApp.</p>

                        <div class="popup-fail-btns">
                            <button type="button" class="btn btn-secondary btn-full" id="popup-try-again-btn">
                                <span>TRY AGAIN</span>
                            </button>
                            <a href="https://wa.me/918976332211" target="_blank" rel="noopener noreferrer" class="btn btn-whatsapp btn-full">
                                <span>CHAT ON WHATSAPP &rarr;</span>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Structured Data Schema.org (JSON-LD) -->
    <?php require __DIR__ . '/schema.php'; ?>

    <!-- Main JavaScript Engine (Deferred for high speed & non-blocking execution) -->
    <script src="assets/js/script.js?v=13.0" defer></script>
    <script src="assets/js/questionnaire.js?v=1.1" defer></script>
</body>
</html>
