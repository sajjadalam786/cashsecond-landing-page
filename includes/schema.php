<?php
/**
 * CashSecond - JSON-LD Structured Data Generator
 * Outputs verified Schema.org markup for LocalBusiness, WebSite, WebPage, FAQPage, and Supported iPhone ItemList.
 */

if (!isset($config)) {
    $config = require __DIR__ . '/../config/config.php';
}

$site_url = rtrim($config['seo']['site_url'], '/');
$business = $config['business'] ?? [];
$faqs = $config['faqs'] ?? [];
$sellBrands = $config['sell_brands'] ?? [];
$iphoneModels = $sellBrands['Apple'] ?? [];

// 1. LocalBusiness / ElectronicsStore Schema
$localBusinessSchema = [
    '@context'   => 'https://schema.org',
    '@type'      => 'ElectronicsStore',
    '@id'        => $site_url . '/#business',
    'name'       => $business['name'],
    'image'      => $site_url . '/assets/images/cashsecond-logo.png',
    'url'        => $site_url,
    'telephone'  => $business['phone_raw'],
    'priceRange' => $business['price_range'] ?? '₹₹',
    'address'    => [
        '@type'           => 'PostalAddress',
        'streetAddress'   => $business['address'] ?? 'Office Number 1307, 13th Floor, Arcadia Building, NCPA Marg, Nariman Point',
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

// 2. FAQPage Schema (Strictly matches visible on-page FAQs for AEO & SGE)
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
    'description'     => 'Apple iPhone models eligible for instant online valuation, doorstep inspection, and buyback in India.',
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
                    'name'  => $business['name'],
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
        'name'  => $business['name'],
        'url'   => $site_url,
    ],
    'areaServed'  => [
        '@type' => 'City',
        'name'  => $business['city'] ?? 'Mumbai',
    ],
    'description' => 'Sell your iPhone online for the best price with instant iPhone valuation, free doorstep pickup, secure data wipe, and fast payment. Trusted iPhone buyers for used, second hand, and old Apple iPhones in Mumbai.',
];
?>

<!-- JSON-LD Structured Data for LocalBusiness -->
<script type="application/ld+json">
<?= json_encode($localBusinessSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>
</script>

<!-- JSON-LD Structured Data for FAQPage (AEO / Answer Engine Optimization) -->
<script type="application/ld+json">
<?= json_encode($faqSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>
</script>

<!-- JSON-LD Structured Data for Supported iPhone Models ItemList -->
<script type="application/ld+json">
<?= json_encode($productCatalogSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>
</script>

<!-- JSON-LD Structured Data for WebSite -->
<script type="application/ld+json">
<?= json_encode($websiteSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>
</script>

<!-- JSON-LD Structured Data for Service (SEO / AEO) -->
<script type="application/ld+json">
<?= json_encode($serviceSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>
</script>

