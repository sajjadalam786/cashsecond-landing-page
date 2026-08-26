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
    <!-- Futuristic Apple-Grade Footer -->
    <footer class="site-footer" id="site-footer">
        <div class="container footer-container">
            <!-- 1. Top Phone Series Quick-Access Strip with Phone Icons -->
            <div class="footer-models-strip" aria-label="iPhone Model Series Quick Links">
                <span class="footer-models-label">Popular Series:</span>
                <div class="footer-models-pills">
                    <a href="#valuation-entry" class="footer-model-pill" aria-label="Sell iPhone 16 Series">
                        <svg class="footer-phone-icon" width="14" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <rect x="5" y="2" width="14" height="20" rx="3" ry="3"/>
                            <line x1="12" y1="18" x2="12.01" y2="18"/>
                        </svg>
                        <span>iPhone 16 Series</span>
                    </a>
                    <a href="#valuation-entry" class="footer-model-pill" aria-label="Sell iPhone 15 Series">
                        <svg class="footer-phone-icon" width="14" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <rect x="5" y="2" width="14" height="20" rx="3" ry="3"/>
                            <line x1="12" y1="18" x2="12.01" y2="18"/>
                        </svg>
                        <span>iPhone 15 Series</span>
                    </a>
                    <a href="#valuation-entry" class="footer-model-pill" aria-label="Sell iPhone 14 Series">
                        <svg class="footer-phone-icon" width="14" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <rect x="5" y="2" width="14" height="20" rx="3" ry="3"/>
                            <line x1="12" y1="18" x2="12.01" y2="18"/>
                        </svg>
                        <span>iPhone 14 Series</span>
                    </a>
                    <a href="#valuation-entry" class="footer-model-pill" aria-label="Sell iPhone 13 Series">
                        <svg class="footer-phone-icon" width="14" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <rect x="5" y="2" width="14" height="20" rx="3" ry="3"/>
                            <line x1="12" y1="18" x2="12.01" y2="18"/>
                        </svg>
                        <span>iPhone 13 Series</span>
                    </a>
                    <a href="#valuation-entry" class="footer-model-pill" aria-label="Sell iPhone 12 & Older">
                        <svg class="footer-phone-icon" width="14" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <rect x="5" y="2" width="14" height="20" rx="3" ry="3"/>
                            <line x1="12" y1="18" x2="12.01" y2="18"/>
                        </svg>
                        <span>iPhone 12 &amp; Older</span>
                    </a>
                </div>
            </div>

            <!-- 2. Main 4-Column Architectural Grid -->
            <div class="footer-main-grid">
                <!-- Col 1: Brand & Verified Location -->
                <div class="footer-grid-col footer-col-brand">
                    <a href="#top" class="footer-brand-logo" aria-label="CashSecond Home">
                        <img src="assets/images/cashsecond-logo.png" alt="CashSecond - Best Value For Your iPhone" class="footer-logo-img" width="160" height="52" loading="lazy">
                    </a>
                    <p class="footer-brand-tagline">Mumbai's trusted doorstep iPhone buyback &amp; valuation service. 100% fair pricing, spot payment, and certified data destruction.</p>
                    
                    <div class="footer-contact-block">
                        <div class="footer-contact-row">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            <span>Office Address: Arcadia Bldg, NCPA Marg, Nariman Point, Mumbai 400021</span>
                        </div>
                        <div class="footer-contact-row">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            <span>Mon - Sun: 10:00 AM – 9:00 PM</span>
                        </div>
                        <div class="footer-contact-row">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            <a href="mailto:cashsecondofficial@gmail.com">cashsecondofficial@gmail.com</a>
                        </div>
                    </div>
                </div>

                <!-- Col 2: Sell by iPhone Model -->
                <div class="footer-grid-col">
                    <h4 class="footer-col-title">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="5" y="2" width="14" height="20" rx="3" ry="3"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
                        <span>Sell by Model</span>
                    </h4>
                    <ul class="footer-links-list">
                        <li><a href="#valuation-entry">Sell iPhone 16 Pro Max</a></li>
                        <li><a href="#valuation-entry">Sell iPhone 16 Pro / 16</a></li>
                        <li><a href="#valuation-entry">Sell iPhone 15 Pro Max</a></li>
                        <li><a href="#valuation-entry">Sell iPhone 15 Pro / 15</a></li>
                        <li><a href="#valuation-entry">Sell iPhone 14 Pro Max / 14</a></li>
                        <li><a href="#valuation-entry">Sell iPhone 13 Pro / 13</a></li>
                        <li><a href="#valuation-entry">Sell iPhone 12 &amp; 11 Series</a></li>
                    </ul>
                </div>

                <!-- Col 3: Process & Guarantees -->
                <div class="footer-grid-col">
                    <h4 class="footer-col-title">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                        <span>Why CashSecond</span>
                    </h4>
                    <ul class="footer-links-list">
                        <li><a href="#how-it-works">How It Works</a></li>
                        <li><a href="#valuation-entry">Instant Valuation Engine</a></li>
                        <li><a href="#how-it-works">Free Doorstep Pickup</a></li>
                        <li><a href="#smart-exchange">Smart iPhone Exchange</a></li>
                        <li><a href="#reviews">Verified Reviews</a></li>
                        <li><a href="#faq">Frequently Asked Questions</a></li>
                        <li><a href="#contact">Contact Store Team</a></li>
                    </ul>
                </div>

                <!-- Col 4: Legal Policies & Compliance -->
                <div class="footer-grid-col">
                    <h4 class="footer-col-title">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        <span>Trust &amp; Legal</span>
                    </h4>
                    <ul class="footer-links-list">
                        <li><a href="policies/privacy-policy.php">Privacy Policy</a></li>
                        <li><a href="policies/terms.php">Terms &amp; Conditions</a></li>
                        <li><a href="policies/buyback-policy.php">Buyback Policy</a></li>
                        <li><a href="policies/cookie-policy.php">Cookie Policy</a></li>
                        <li><a href="policies/disclaimer.php">Trademark Disclaimer</a></li>
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
            <a href="#valuation-entry" class="btn btn-primary btn-full" id="mobile-sticky-valuation-btn" aria-haspopup="dialog" aria-controls="buybackQuestionnaireModal">
                <span>Check Your iPhone Value</span>
                <img src="assets/images/iphone-value-check-button.png" alt="iPhone" class="btn-iphone-thumb" width="20" height="34" loading="eager">
            </a>
        </div>
    </div>

    <!-- Structured Data Schema.org (JSON-LD) -->
    <?php require __DIR__ . '/schema.php'; ?>

    <!-- Main JavaScript Engine (Deferred for high speed & non-blocking execution) -->
    <script src="assets/js/script.js?v=14.0" defer></script>
    <script src="assets/js/questionnaire.js?v=2.0" defer></script>
</body>
</html>
