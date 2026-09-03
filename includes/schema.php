<?php
/**
 * CashSecond - JSON-LD Structured Data Generator
 * Outputs verified Schema.org markup for LocalBusiness, WebSite, WebPage, FAQPage, HowTo, Service, ItemList, and Breadcrumbs.
 * Fully optimized for SEO, AEO (Voice & Answer Engines), and GEO (Generative AI Search).
 */

if (!isset($config)) {
    $config = require __DIR__ . '/../config/config.php';
}

$site_url = rtrim($config['seo']['site_url'] ?? 'https://selliphone.cashsecond.com', '/');
$business = $config['business'] ?? [];
$faqs = $config['faqs'] ?? [];
$sellBrands = $config['sell_brands'] ?? [];
$iphoneModels = $sellBrands['Apple'] ?? [];

$current_page_url = $canonical_url ?? ($site_url . '/');
$is_home_page = ($current_page_url === $site_url || $current_page_url === $site_url . '/' || strpos($current_page_url, 'index.php') !== false);

// 1. LocalBusiness / ElectronicsStore / Organization Schema (GEO Entity Graph)
$localBusinessSchema = [
    '@context'   => 'https://schema.org',
    '@type'      => ['ElectronicsStore', 'LocalBusiness', 'Organization'],
    '@id'        => $site_url . '/#business',
    'name'       => $business['name'] ?? 'CashSecond',
    'alternateName' => 'CashSecond iPhone Buyback & Valuation Mumbai',
    'legalName'  => 'CashSecond',
    'url'        => $site_url,
    'logo'       => $site_url . '/assets/images/cashsecond-logo.png',
    'image'      => $site_url . '/assets/images/banners/desktop/sell-your-iphone-with-cashsecond.webp',
    'telephone'  => $business['phone_raw'] ?? '+918976332211',
    'email'      => $business['email'] ?? 'cashsecondofficial@gmail.com',
    'priceRange' => '₹4,000 - ₹1,40,000',
    'disambiguatingDescription' => 'CashSecond is an independent pre-owned Apple iPhone valuation, doorstep inspection, and buyback platform in Mumbai, Maharashtra. CashSecond is not affiliated with Apple Inc.',
    'sameAs'     => [
        'https://cashsecond.com/',
        'https://maps.google.com/?q=Arcadia+Building+NCPA+Marg+Nariman+Point+Mumbai+400021'
    ],
    'address'    => [
        '@type'           => 'PostalAddress',
        'streetAddress'   => $business['address'] ?? 'Arcadia Bldg, NCPA Marg, Nariman Point',
        'addressLocality' => $business['city'] ?? 'Mumbai',
        'addressRegion'   => $business['state'] ?? 'Maharashtra',
        'postalCode'      => $business['pincode'] ?? '400021',
        'addressCountry'  => 'IN',
    ],
    'geo' => [
        '@type'     => 'GeoCoordinates',
        'latitude'  => 18.928000,
        'longitude' => 72.825833,
    ],
    'areaServed' => [
        [
            '@type' => 'City',
            'name'  => 'Mumbai',
        ],
        [
            '@type' => 'City',
            'name'  => 'Navi Mumbai',
        ],
        [
            '@type' => 'City',
            'name'  => 'Thane',
        ],
        [
            '@type' => 'AdministrativeArea',
            'name'  => 'Mumbai Metropolitan Region',
        ],
        [
            '@type' => 'AdministrativeArea',
            'name'  => 'Maharashtra',
        ],
    ],
    'openingHoursSpecification' => [
        [
            '@type'     => 'OpeningHoursSpecification',
            'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
            'opens'     => '10:00',
            'closes'    => '21:00',
        ],
    ],
    'paymentAccepted'    => 'Cash, UPI, Google Pay, PhonePe, Paytm, IMPS Bank Transfer',
    'currenciesAccepted' => 'INR',
];

// 2. FAQPage Schema (Verbatim match to on-page FAQs for AEO & AI Overviews)
$faqSchema = [
    '@context'   => 'https://schema.org',
    '@type'      => 'FAQPage',
    'mainEntity' => [],
];

foreach ($faqs as $faq) {
    $faqSchema['mainEntity'][] = [
        '@type'          => 'Question',
        'name'           => $faq['q'],
        'acceptedAnswer' => [
            '@type' => 'Answer',
            'text'  => $faq['a'],
        ],
    ];
}

// 3. Supported iPhone Models ItemList Schema
$productCatalogSchema = [
    '@context'        => 'https://schema.org',
    '@type'           => 'ItemList',
    'name'            => 'Apple iPhone Models for Valuation and Resale',
    'description'     => 'Apple iPhone models eligible for instant online valuation, doorstep inspection, and buyback in Mumbai, India.',
    'itemListElement' => [],
];

$itemPosition = 1;
foreach ($iphoneModels as $item) {
    $productCatalogSchema['itemListElement'][] = [
        '@type'    => 'ListItem',
        'position' => $itemPosition++,
        'item'     => [
            '@type'       => 'Product',
            'name'        => $item['seo_name'] ?? $item['product_name'],
            'image'       => $site_url . '/' . ltrim($item['image'] ?? 'assets/images/phones/iphone-15.svg', '/'),
            'brand'       => [
                '@type' => 'Brand',
                'name'  => 'Apple',
            ],
            'offers'      => [
                '@type'         => 'Offer',
                'priceCurrency' => 'INR',
                'availability'  => 'https://schema.org/InStock',
                'seller'        => [
                    '@type' => 'Organization',
                    'name'  => $business['name'] ?? 'CashSecond',
                ],
            ],
        ],
    ];
}

// 4. WebSite Schema with SearchAction
$websiteSchema = [
    '@context' => 'https://schema.org',
    '@type'    => 'WebSite',
    'name'     => $business['short_name'] ?? 'CashSecond',
    'url'      => $site_url,
    'potentialAction' => [
        '@type'       => 'SearchAction',
        'target'      => $site_url . '/#valuation',
        'query-input' => 'required name=search_term_string',
    ],
];

// 5. Service Schema for iPhone Valuation, Buyback & Doorstep Resale (SEO / AEO)
$serviceSchema = [
    '@context'    => 'https://schema.org',
    '@type'       => 'Service',
    'name'        => 'Apple iPhone Buyback, Valuation & Doorstep Payout',
    'serviceType' => 'iPhone Valuation, Resale, Doorstep Inspection, and Buyback',
    'provider'    => [
        '@type' => 'Organization',
        'name'  => $business['name'] ?? 'CashSecond',
        'url'   => $site_url,
    ],
    'areaServed'  => [
        [
            '@type' => 'City',
            'name'  => 'Mumbai',
        ],
        [
            '@type' => 'City',
            'name'  => 'Navi Mumbai',
        ],
        [
            '@type' => 'City',
            'name'  => 'Thane',
        ],
    ],
    'description' => 'Sell your used or old iPhone online for the best price with instant valuation, free doorstep pickup across Mumbai, secure data wipe, and spot payment.',
];

// 6. BreadcrumbList Schema for Rich Search Snippets & GEO
$breadcrumbItems = [
    [
        '@type'    => 'ListItem',
        'position' => 1,
        'name'     => 'Home',
        'item'     => $site_url . '/',
    ]
];

if (!$is_home_page && isset($page_title)) {
    $breadcrumbItems[] = [
        '@type'    => 'ListItem',
        'position' => 2,
        'name'     => trim(explode('|', $page_title)[0]),
        'item'     => $current_page_url,
    ];
} else {
    $breadcrumbItems[] = [
        '@type'    => 'ListItem',
        'position' => 2,
        'name'     => 'Sell iPhone',
        'item'     => $site_url . '/#valuation',
    ];
    $breadcrumbItems[] = [
        '@type'    => 'ListItem',
        'position' => 3,
        'name'     => 'iPhone Resale Models',
        'item'     => $site_url . '/#iphone-models',
    ];
}

$breadcrumbSchema = [
    '@context'        => 'https://schema.org',
    '@type'           => 'BreadcrumbList',
    'itemListElement' => $breadcrumbItems,
];

// 7. HowTo Schema for AEO & AI Engine Step-by-Step Direct Answers
$howToSchema = [
    '@context'      => 'https://schema.org',
    '@type'         => 'HowTo',
    'name'          => 'How to Sell Your Old iPhone Online with Free Doorstep Pickup',
    'description'   => 'Follow these 4 simple steps to calculate your iPhone resale value, schedule a free Mumbai doorstep pickup, and receive spot payment.',
    'totalTime'     => 'PT5M',
    'estimatedCost' => [
        '@type'    => 'MonetaryAmount',
        'currency' => 'INR',
        'value'    => '0',
    ],
    'supply' => [
        [
            '@type' => 'HowToSupply',
            'name'  => 'Old or Used Apple iPhone',
        ],
    ],
    'tool' => [
        [
            '@type' => 'HowToTool',
            'name'  => 'CashSecond Online Valuation Calculator',
        ],
    ],
    'step' => [
        [
            '@type'    => 'HowToStep',
            'position' => 1,
            'name'     => 'Select iPhone Model & Storage',
            'text'     => 'Choose your exact Apple iPhone model and storage capacity on the CashSecond valuation tool.',
            'url'      => $site_url . '/#valuation',
        ],
        [
            '@type'    => 'HowToStep',
            'position' => 2,
            'name'     => 'Answer Condition Questions for Live Valuation',
            'text'     => 'Complete the quick condition check to receive an instant, market-accurate resale estimate.',
            'url'      => $site_url . '/#valuation',
        ],
        [
            '@type'    => 'HowToStep',
            'position' => 3,
            'name'     => 'Schedule Free Mumbai Doorstep Pickup',
            'text'     => 'Select your preferred pickup date, time slot, and doorstep address anywhere in Mumbai.',
            'url'      => $site_url . '/#valuation',
        ],
        [
            '@type'    => 'HowToStep',
            'position' => 4,
            'name'     => '5-Minute Doorstep Diagnostic & Instant Spot Payment',
            'text'     => 'Our technician verifies your device at your doorstep and provides instant UPI / Cash payment with certified data wipe.',
            'url'      => $site_url . '/#how-it-works',
        ],
    ],
];

// 8. WebPage Schema with Speakable Specification for Voice Search & Answer Engines (AEO)
$webPageSchema = [
    '@context'    => 'https://schema.org',
    '@type'       => 'WebPage',
    '@id'         => $current_page_url . '#webpage',
    'url'         => $current_page_url,
    'name'        => $page_title ?? ($config['seo']['meta_title'] ?? 'Sell iPhone Online & Check Resale Value | CashSecond Mumbai'),
    'description' => $page_description ?? ($config['seo']['meta_description'] ?? 'Sell your used or old iPhone online for the best price in Mumbai. Get instant iPhone valuation, free doorstep pickup, certified data wipe, and spot payment.'),
    'speakable'   => [
        '@type'       => 'SpeakableSpecification',
        'cssSelector' => ['.hero-main-title', '.hero-main-subtitle', '.faq-question-text', '.faq-content p'],
    ],
    'isPartOf' => [
        '@type' => 'WebSite',
        '@id'   => $site_url . '/#website',
    ],
];
?>

<!-- JSON-LD Structured Data: LocalBusiness / Organization (GEO) -->
<script type="application/ld+json">
<?= json_encode($localBusinessSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>
</script>

<!-- JSON-LD Structured Data: WebSite -->
<script type="application/ld+json">
<?= json_encode($websiteSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>
</script>

<!-- JSON-LD Structured Data: WebPage with Speakable (Voice & AEO) -->
<script type="application/ld+json">
<?= json_encode($webPageSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>
</script>

<!-- JSON-LD Structured Data: Breadcrumbs -->
<script type="application/ld+json">
<?= json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>
</script>

<!-- JSON-LD Structured Data: Service -->
<script type="application/ld+json">
<?= json_encode($serviceSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>
</script>

<?php if ($is_home_page): ?>
<!-- JSON-LD Structured Data: FAQPage (AEO / Answer Engine Optimization) -->
<script type="application/ld+json">
<?= json_encode($faqSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>
</script>

<!-- JSON-LD Structured Data: HowTo (AEO / AI Step-by-Step Guidance) -->
<script type="application/ld+json">
<?= json_encode($howToSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>
</script>

<!-- JSON-LD Structured Data: Supported iPhone Models ItemList -->
<script type="application/ld+json">
<?= json_encode($productCatalogSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>
</script>
<?php endif; ?>
