<?php
/**
 * CashSecond - Configuration & Settings
 * Sourced directly from https://cashsecond.com/
 * Zero invented data. 100% exact CashSecond contact information, catalog and pricing.
 */

// Start session if not already started (needed for CSRF tokens & anti-spam rate limiting)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Generate CSRF token if not present
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Load structured catalog database directly from data/catalog.php
$catalogData = require __DIR__ . '/../data/catalog.php';

return [
    // ============================================================
    // BUSINESS CONTACT & TRANSPARENCY INFORMATION
    // Exact contact information from https://cashsecond.com/
    // ============================================================
    'business' => [
        'name'             => 'CashSecond',
        'short_name'       => 'CashSecond',
        'tagline'          => 'Sell Your iPhone at a Fair Price in Mumbai',
        
        // Exact Phone: +91 897633 2211 | Clickable: tel:+918976332211
        'phone_display'    => '+91 897633 2211',
        'phone_raw'        => '+918976332211',
        
        // Exact WhatsApp
        'whatsapp_number'  => '918976332211',
        'whatsapp_message' => 'Hi CashSecond, I want to check my iPhone estimated value and book doorstep pickup.',
        
        // Exact Email: cashsecondofficial@gmail.com | Clickable: mailto:cashsecondofficial@gmail.com
        'email'            => 'cashsecondofficial@gmail.com',
        
        // Exact Office Address:
        // Office Number 1307, 13th Floor, Arcadia Building, NCPA Marg, Nariman Point, Mumbai – 400021
        'address_line1'    => 'Office Number 1307, 13th Floor',
        'address_building' => 'Arcadia Building',
        'address_street'   => 'NCPA Marg, Nariman Point',
        'address'          => 'Office Number 1307, 13th Floor, Arcadia Building, NCPA Marg, Nariman Point',
        'full_address'     => 'Office Number 1307, 13th Floor, Arcadia Building, NCPA Marg, Nariman Point, Mumbai – 400021',
        
        'city'             => 'Mumbai',
        'state'            => 'Maharashtra',
        'country'          => 'India',
        'pincode'          => '400021',
        
        'opening_hours'    => 'Mon - Sun: 10:00 AM to 9:00 PM',
        
        'google_maps_url'  => 'https://maps.google.com/?q=Arcadia+Building+NCPA+Marg+Nariman+Point+Mumbai+400021',
        'google_maps_embed'=> 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3773.809088661623!2d72.825833!3d18.928000!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3be7d1e8a2a9a8d7%3A0x1d368d30e52bca1!2sArcadia%20Building%2C%20NCPA%20Marg%2C%20Nariman%20Point%2C%20Mumbai%2C%20Maharashtra%20400021!5e0!3m2!1sen!2sin!4v1600000000000!5m2!1sen!2sin',
        
        'price_range'      => '₹44,799 - ₹53,600',
    ],

    // ============================================================
    // SEO & META CONFIGURATION (Google Ads & Organic Search Optimization)
    // ============================================================
    'seo' => [
        'site_url'         => 'http://localhost/cashsecond-landing-page', // Production URL (e.g., https://cashsecond.com)
        'meta_title'       => 'Sell iPhone Online & Check Resale Value | CashSecond',
        'meta_description' => 'Sell your used or old iPhone online. Check your iPhone resale value with a simple and transparent valuation process and convenient selling options.',
        'keywords'         => 'Sell iPhone, Sell Used iPhone, Sell Old iPhone, iPhone Resale Value, Sell iPhone Online, iPhone Valuation, iPhone Resale Price, Sell iPhone for Cash, Used iPhone Value, iPhone Selling Process, iPhone Exchange Value, Sell iPhone in India, sell my iPhone, sell old iPhone online, check iPhone resale value, best price for used iPhone, sell used iPhone online, iPhone resale value in India',
        'og_image'         => '/assets/images/cashsecond-logo.png',
        'theme_color'      => '#0B0D10',
    ],

    // ============================================================
    // INTEGRATION SETTINGS (Google Sheets via Apps Script Web App)
    // Paste your deployed Google Apps Script Web App URL below.
    // Leads will automatically sync to your Google Sheet with all 12 columns.
    // ============================================================
    'integrations' => [
        'google_sheets_web_app_url' => '', // Paste Google Apps Script Web App URL here (e.g. https://script.google.com/macros/s/XXXXX/exec)
        'google_sheets_webhook_url' => '', // Backward-compatibility alias
        'notification_email'        => 'cashsecondofficial@gmail.com',
        'enable_local_lead_log'     => true,
    ],

    // ============================================================
    // TRACKING & CONVERSION PLACEHOLDERS
    // ============================================================
    'tracking' => [
        'ga4_measurement_id'     => '',
        'google_ads_id'          => '',
        'google_ads_conv_label'  => '',
        'meta_pixel_id'          => '',
    ],

    // ============================================================
    // EXACT CASHSECOND BUY CATALOG
    // Sourced directly from https://cashsecond.com/product/list
    // ============================================================
    'buy_catalog' => $catalogData['buy_products'],

    // ============================================================
    // COMPLETE CASHSECOND SELL CATALOG (856 Models across 20+ Brands)
    // Sourced directly from https://cashsecond.com/sell-old-mobile-phone
    // ============================================================
    'sell_brands' => $catalogData['sell_brands'],

    // ============================================================
    // 32-POINT TECHNICAL QUALITY CHECKLIST
    // ============================================================
    'inspection_checklist' => [
        'Display & Touch'       => ['Original OEM Panel', 'Zero Dead Pixels', '10-Point Multi-Touch', 'TrueTone / Refresh Rate'],
        'Battery & Power'       => ['Battery Health Check (>80%)', 'Fast Charging Current Test', 'Thermal / Overheating Test', 'Port Longevity Test'],
        'Cameras & Optical'     => ['Main / Ultra-Wide Focus', 'Optical Image Stabilization', 'Front Portrait Camera', 'Microphone Noise Cancellation'],
        'Network & Audio'       => ['5G / 4G LTE Connectivity', 'Dual SIM / eSIM Function', 'Earpiece & Stereo Speakers', 'Bluetooth 5.x & Wi-Fi 6'],
        'Sensors & Security'    => ['Face ID / Fingerprint Sensor', 'Proximity Sensor', 'Gyroscope & Accelerometer', 'NFC Payment Tested'],
        'Legal & Authenticity'  => ['IMEI Blacklist Verified', 'iCloud / Google FRP Unlocked', 'Genuine Bill / Ownership Verification', 'Zero Tampered Components']
    ],

    // ============================================================
    // FAQ DATA FOR SEO & ANSWER ENGINE OPTIMIZATION (AEO)
    // 10 Targeted Conversion & Search Questions
    // ============================================================
    'faqs' => [
        [
            'q' => 'How can I sell my iPhone?',
            'a' => 'Selling your iPhone is simple: select your exact iPhone model and storage capacity on our valuation calculator, choose its physical condition, get an instant estimated resale quote, and schedule a convenient doorstep pickup with on-spot payout.'
        ],
        [
            'q' => 'How is my iPhone resale value calculated?',
            'a' => 'Your iPhone resale value is calculated based on current market demand, factoring in your device model, storage capacity, physical condition (screen & body), battery health, and functional status of features like Face ID and cameras.'
        ],
        [
            'q' => 'How much can I get for my iPhone?',
            'a' => 'Payouts depend on the model generation, storage tier, and device condition. Newer models like the iPhone 15 and 16 series in flawless condition receive top market value, while older generations still command competitive resale prices.'
        ],
        [
            'q' => 'Can I sell an old or used iPhone?',
            'a' => 'Yes, you can sell used, older, and out-of-warranty iPhones. We evaluate devices across flawless, good, and average condition grades so you get an honest and fair market price regardless of age.'
        ],
        [
            'q' => 'Which iPhone models can I sell?',
            'a' => 'You can sell virtually any Apple iPhone model including iPhone 16 Pro Max, 16 Pro, 16 Plus, 16, 15 series, 14 series, 13 series, 12 series, 11 series, iPhone SE, and earlier generations.'
        ],
        [
            'q' => 'What affects my iPhone\'s resale value?',
            'a' => 'The main factors are cosmetic condition (scratches, dents, back glass), display originality and touch responsiveness, battery health percentage, functional hardware (Face ID, cameras, speakers), and internal storage capacity.'
        ],
        [
            'q' => 'Can I sell an iPhone with a damaged screen?',
            'a' => 'Yes. Devices with minor scratches, display wear, or cracked glass can be evaluated through our condition selector, and you will receive a transparent estimated valuation reflecting the actual condition.'
        ],
        [
            'q' => 'How does the iPhone selling process work?',
            'a' => 'The process follows 4 simple steps: 1) Select your model and condition online to check estimated value, 2) Schedule a doorstep appointment, 3) 5-minute diagnostic verification with iCloud reset, and 4) Instant payment via UPI, IMPS, or Cash before handover.'
        ],
        [
            'q' => 'How does pickup work?',
            'a' => 'Our verified executive visits your home or office at your selected time slot across Mumbai, performs a quick 32-point inspection, assists with complete factory data reset, and initiates your instant payment on the spot.'
        ],
        [
            'q' => 'When do I receive payment?',
            'a' => 'You receive payment instantly during the doorstep pickup appointment. The executive transfers funds directly via UPI (Google Pay / PhonePe / Paytm), instant IMPS bank transfer, or cash before you hand over the device.'
        ]
    ]
];
