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
            <div>
                <p style="font-weight: 700; color: var(--color-dark); margin-bottom: 4px;">CashSecond</p>
                <p>Office 1307, 13th Floor, Arcadia Building, NCPA Marg, Nariman Point, Mumbai – 400021</p>
                <p style="margin-top: 4px;">Phone: <a href="tel:+918976332211" style="color: var(--color-cta);">+91 897633 2211</a> • Email: <a href="mailto:cashsecondofficial@gmail.com" style="color: var(--color-cta);">cashsecondofficial@gmail.com</a></p>
            </div>

            <!-- Quick Navigation & Legal Policy Links -->
            <div class="footer-links">
                <a href="#top">Home</a>
                <a href="#valuation">Check Value</a>
                <a href="#why-us">Why Us</a>
                <a href="#inspection">32-Point Check</a>
                <a href="#how-it-works">How It Works</a>
                <a href="#faq">FAQ</a>
                <a href="#contact">Contact</a>
                <a href="policies/privacy-policy.php">Privacy Policy</a>
                <a href="policies/terms.php">Terms & Conditions</a>
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
        <a href="#valuation" class="btn btn-primary" id="mobile-sticky-valuation-btn">
            <span>Check iPhone Value</span>
        </a>
        <a href="https://wa.me/<?= htmlspecialchars($business['whatsapp_number'] ?? '918976332211'); ?>?text=<?= urlencode('Hi CashSecond, I want to check my iPhone value and book pickup in Mumbai.'); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-whatsapp" id="mobile-sticky-wa-btn">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
            <span>WhatsApp Us</span>
        </a>
    </div>

    <!-- Structured Data Schema.org (JSON-LD) -->
    <?php require __DIR__ . '/schema.php'; ?>

    <!-- Main JavaScript Engine -->
    <script src="assets/js/script.js?v=4.0"></script>
</body>
</html>
