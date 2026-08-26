<?php
/**
 * CashSecond - Privacy Policy
 * Legitimate, transparent privacy and data protection terms.
 */
$config = require __DIR__ . '/../config/config.php';
$base_path = '../';
$seo = $config['seo'] ?? [];

$page_title       = "Privacy Policy | CashSecond iPhone Buyback";
$page_description = "Learn how CashSecond handles your contact details, device information, and data privacy during iPhone valuation and buyback.";
$canonical_url    = rtrim($seo['site_url'] ?? 'http://localhost/cashsecond-landing-page', '/') . "/policies/privacy-policy.php";

require __DIR__ . '/../includes/header.php';
?>

<main class="policy-page">
    <div class="container">
        <div class="policy-card">
            <a href="../index.php" class="back-nav">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                <span>Back to CashSecond</span>
            </a>

            <h1>Privacy Policy</h1>
            <p class="updated-date">Last Updated: August 2026</p>

            <p>Welcome to <strong>CashSecond</strong> ("we", "our", or "us"). We respect your privacy and are committed to protecting the personal information and device details you share with us when checking your iPhone valuation, communicating with our support team, or selling your device.</p>

            <h2>1. Information We Collect</h2>
            <p>When you use our valuation calculator, submit an enquiry form, or contact us via WhatsApp/Phone, we may collect:</p>
            <ul>
                <li><strong>Contact Details:</strong> Full Name, Mobile Number, WhatsApp Number, and Email Address.</li>
                <li><strong>Device Information:</strong> iPhone model, storage capacity, physical condition notes, and battery health details.</li>
                <li><strong>Pickup Address:</strong> Address / locality details in Mumbai, Navi Mumbai, or Thane provided voluntarily for executive doorstep verification.</li>
            </ul>

            <h2>2. How We Use Your Information</h2>
            <p>Your information is used strictly to fulfill your requested iPhone buyback services:</p>
            <ul>
                <li>Generating your estimated iPhone valuation quote.</li>
                <li>Contacting you via WhatsApp or Call to answer questions or schedule doorstep pickup.</li>
                <li>Dispatching a device executive for physical inspection and instant on-spot payment.</li>
                <li>Maintaining required transaction records and invoices in compliance with Indian consumer laws.</li>
            </ul>

            <h2>3. User Device Data & Factory Reset Protection</h2>
            <p>We place paramount importance on customer device data security:</p>
            <ul>
                <li>Before handing over any iPhone, our executive guides you to sign out of your Apple ID / iCloud account.</li>
                <li>We ensure a full factory reset (<em>Erase All Content and Settings</em>) is completed in front of you.</li>
                <li>CashSecond never stores, accesses, or attempts to recover personal data from purchased devices.</li>
            </ul>

            <h2>4. Data Sharing & Third Parties</h2>
            <p>We do not sell, rent, or trade your personal information to third-party marketing companies. Data is only shared with trusted service providers necessary to operate our business (e.g., communication channels like WhatsApp Business, secure server hosting, and logistics coordination).</p>

            <h2>5. Contact Our Privacy Team</h2>
            <p>If you have questions regarding this Privacy Policy or wish to request deletion of your enquiry data, please contact us:</p>
            <p>
                <strong>CashSecond</strong><br>
                Arcadia Bldg, NCPA Marg, Nariman Point, Mumbai 400021<br>
                Email: <a href="mailto:cashsecondofficial@gmail.com">cashsecondofficial@gmail.com</a>
            </p>
        </div>
    </div>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>
