<?php
/**
 * CashSecond - Footer Template & Mobile Sticky Conversion Bar
 * Apple-Inspired Minimalist Design with Verified Contact Information
 */
if (!isset($config)) {
    $config = require __DIR__ . '/../config/config.php';
}

if (!isset($base_path)) {
    $base_path = '';
}

$business = $config['business'] ?? [];
$current_year = date('Y');
?>
    <!-- Futuristic Apple-Grade Footer -->
    <footer class="site-footer" id="site-footer">
        <div class="container footer-container">
            

            <!-- 2. Main 4-Column Architectural Grid -->
            <div class="footer-main-grid">
                <!-- Col 1: Brand & Verified Location -->
                <div class="footer-grid-col footer-col-brand">
                    <a href="<?= $base_path ?>index.php#top" class="footer-brand-logo" aria-label="CashSecond Home">
                        <img src="<?= $base_path ?>assets/images/cashsecond-logo.png" alt="CashSecond - Best Value For Your iPhone" class="footer-logo-img" width="160" height="52" loading="lazy">
                    </a>
                    <p class="footer-brand-tagline">Mumbai's trusted doorstep iPhone buyback &amp; valuation service. 100% fair pricing, spot payment, and certified data destruction.</p>
                    
                    <div class="footer-contact-block">
                        <div class="footer-contact-row">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            <span>Office Address: Arcadia Bldg, NCPA Marg, Nariman Point, Mumbai 400021</span>
                        </div>
                       
                        <div class="footer-contact-row">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            <a href="mailto:cashsecondofficial@gmail.com">cashsecondofficial@gmail.com</a>
                        </div>
                    </div>
                </div>

                <!-- Mobile-Only Footer Category Filter Tabs -->
                <div class="footer-mobile-tabs-wrap" aria-label="Footer Categories">
                    <div class="footer-mobile-tabs" role="tablist">
                        <button type="button" class="footer-tab-btn active" data-target="footer-tab-models" role="tab" aria-selected="true">
                            <span>Sell by Model</span>
                        </button>
                        <button type="button" class="footer-tab-btn" data-target="footer-tab-why" role="tab" aria-selected="false">
                            <span>Why CashSecond</span>
                        </button>
                        <button type="button" class="footer-tab-btn" data-target="footer-tab-legal" role="tab" aria-selected="false">
                            <span>Trust &amp; Legal</span>
                        </button>
                    </div>
                </div>

                <!-- Col 2: Sell by iPhone Model -->
                <div class="footer-grid-col footer-tab-panel active" id="footer-tab-models" role="tabpanel">
                    <h4 class="footer-col-title">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="5" y="2" width="14" height="20" rx="3" ry="3"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
                        <span>Sell by Model</span>
                    </h4>
                    <ul class="footer-links-list">
                        <li><a href="<?= $base_path ?>index.php#iphone-models" class="start-exact-valuation-btn" data-model="Apple iPhone 16 Pro Max">Sell iPhone 16 Pro Max</a></li>
                        <li><a href="<?= $base_path ?>index.php#iphone-models" class="start-exact-valuation-btn" data-model="Apple iPhone 16 Pro">Sell iPhone 16 Pro / 16</a></li>
                        <li><a href="<?= $base_path ?>index.php#iphone-models" class="start-exact-valuation-btn" data-model="Apple iPhone 15 Pro Max">Sell iPhone 15 Pro Max</a></li>
                        <li><a href="<?= $base_path ?>index.php#iphone-models" class="start-exact-valuation-btn" data-model="Apple iPhone 15 Pro">Sell iPhone 15 Pro / 15</a></li>
                        <li><a href="<?= $base_path ?>index.php#iphone-models" class="start-exact-valuation-btn" data-model="Apple iPhone 14 Pro Max">Sell iPhone 14 Pro Max / 14</a></li>
                        <li><a href="<?= $base_path ?>index.php#iphone-models" class="start-exact-valuation-btn" data-model="Apple iPhone 13">Sell iPhone 13 Pro / 13</a></li>
                        <li><a href="<?= $base_path ?>index.php#iphone-models" class="start-exact-valuation-btn" data-model="Apple iPhone 12">Sell iPhone 12 &amp; Older</a></li>
                    </ul>
                </div>

                <!-- Col 3: Process & Guarantees -->
                <div class="footer-grid-col footer-tab-panel" id="footer-tab-why" role="tabpanel">
                    <h4 class="footer-col-title">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                        <span>Process &amp; Service</span>
                    </h4>
                    <ul class="footer-links-list">
                        <li><a href="<?= $base_path ?>index.php#iphone-models">Sell Old iPhone Models</a></li>
                        <li><a href="<?= $base_path ?>index.php#how-it-works">4-Step Selling Process</a></li>
                        <li><a href="<?= $base_path ?>index.php#buyback-advantages">Best Price Guarantee</a></li>
                        <li><a href="<?= $base_path ?>index.php#coverage">Mumbai Service Areas</a></li>
                        <li><a href="<?= $base_path ?>index.php#faq">Selling FAQs &amp; Help</a></li>
                        <li><a href="<?= $base_path ?>index.php#contact">Support &amp; Store Location</a></li>
                    </ul>
                </div>

                <!-- Col 4: Legal Policies & Compliance -->
                <div class="footer-grid-col footer-tab-panel" id="footer-tab-legal" role="tabpanel">
                    <h4 class="footer-col-title">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        <span>Trust &amp; Legal</span>
                    </h4>
                    <ul class="footer-links-list">
                        <li><a href="<?= $base_path ?>policies/privacy-policy.php">Privacy Policy</a></li>
                        <li><a href="<?= $base_path ?>policies/terms.php">Terms &amp; Conditions</a></li>
                        <li><a href="<?= $base_path ?>policies/buyback-policy.php">Buyback Policy</a></li>
                        <li><a href="<?= $base_path ?>policies/cookie-policy.php">Cookie Policy</a></li>
                        <li><a href="<?= $base_path ?>policies/disclaimer.php">Trademark Disclaimer</a></li>
                    </ul>
                </div>
            </div>

            <!-- 3. Bottom Legal & Trademark Disclaimer Bar -->
            <div class="footer-bottom-deck">
                <div class="footer-disclaimer-card">
                    <p class="trademark-disclaimer">
                        <strong>Disclaimer:</strong> Apple, iPhone, iOS, Retina, Face ID, Dynamic Island, and Apple Intelligence are registered trademarks of Apple Inc. Their mention on this website is strictly for device identification, technical compatibility, and trade reference. CashSecond is an independent pre-owned electronics valuation and buyback platform and is not affiliated with Apple Inc.
                    </p>
                </div>

                <div class="footer-copyright-row">
                    <p>&copy; <?= $current_year; ?> CashSecond. All rights reserved. Registered Business in Mumbai, Maharashtra.</p>
                    <a href="#top" class="footer-back-to-top" aria-label="Back to top">
                        <span>Back to top</span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="18 15 12 9 6 15"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Mobile Sticky Conversion Bar (Triggers Unified Valuation Modal) -->
    <div class="mobile-sticky-bar" aria-label="Quick Actions">
        <div class="mobile-sticky-inner">
            <a href="<?= $base_path ?>index.php#valuation-entry" class="btn btn-primary btn-full start-exact-valuation-btn" id="mobile-sticky-valuation-btn" aria-haspopup="dialog" aria-controls="ivOverlay">
                <svg class="btn-click-icon" width="18" height="21" viewBox="0 0 24 28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
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
                <img src="<?= $base_path ?>assets/images/iphone-value-check-button.png" alt="iPhone" class="btn-iphone-thumb" width="20" height="34" loading="eager">
            </a>
        </div>
    </div>

    <!-- Structured Data Schema.org (JSON-LD) -->
    <?php require __DIR__ . '/schema.php'; ?>

    <!-- Main JavaScript Engine (Deferred for high speed & non-blocking execution) -->
    <script src="<?= $base_path ?>assets/js/script.js?v=16.0" defer></script>
    <!-- valuator.js is loaded directly by components/iphone-valuator/valuator.php -->
    <!-- assets/js/questionnaire.js retained as backup (deprecated) -->
</body>
</html>

