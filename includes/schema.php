<?php
/**
 * CashSecond - Advanced Schema.org JSON-LD Knowledge Graph Generator
 * Sourced for https://selliphone.cashsecond.com/
 * 
 * Generates a single, fully interconnected '@graph' linking:
 * - ElectronicsStore / LocalBusiness / Organization (#business)
 * - WebSite (#website)
 * - WebPage with Speakable (#webpage)
 * - Primary Image Objects (#logo, #primaryimage)
 * - Service for Doorstep Buyback (#service)
 * - BreadcrumbList (#breadcrumb)
 * - FAQPage (AEO Direct Answers)
 * - HowTo (AEO Step-by-Step AI Guidance)
 * - ItemList of Supported iPhone Models (Product Resale Catalog)
 */

if (!isset($config)) {
    $config = require __DIR__ . '/../config/config.php';
}

$site_url = rtrim($config['seo']['site_url'] ?? 'https://selliphone.cashsecond.com', '/');
$home_url = $site_url . '/';
$business = $config['business'] ?? [];
$faqs = $config['faqs'] ?? [];
$sellBrands = $config['sell_brands'] ?? [];
$iphoneModels = $sellBrands['Apple'] ?? [];

$current_page_url = $canonical_url ?? $home_url;
$is_home_page = ($current_page_url === $site_url || $current_page_url === $home_url || strpos($current_page_url, 'index.php') !== false);

// Build the unified Schema.org graph array
$graph = [];

// -------------------------------------------------------------
// 1. Logo & Primary Image Objects
// -------------------------------------------------------------
$logoImage = [
    '@type'      => 'ImageObject',
    '@id'        => $site_url . '/#logo',
    'url'        => $site_url . '/assets/images/cashsecond-logo.png',
    'contentUrl' => $site_url . '/assets/images/cashsecond-logo.png',
    'caption'    => $business['name'] ?? 'CashSecond Logo',
    'inLanguage' => 'en-IN',
];

$primaryBannerImage = [
    '@type'      => 'ImageObject',
    '@id'        => $site_url . '/#primaryimage',
    'url'        => $site_url . '/assets/images/banners/desktop/sell-your-iphone-with-cashsecond.webp',
    'contentUrl' => $site_url . '/assets/images/banners/desktop/sell-your-iphone-with-cashsecond.webp',
    'width'      => 1280,
    'height'     => 560,
    'caption'    => 'Sell Your Old iPhone From Your Doorstep with CashSecond Mumbai',
    'inLanguage' => 'en-IN',
];

$graph[] = $logoImage;
$graph[] = $primaryBannerImage;

// -------------------------------------------------------------
// 2. ElectronicsStore / LocalBusiness / Organization Entity (GEO Graph)
// -------------------------------------------------------------
$localBusinessSchema = [
    '@type'                     => ['ElectronicsStore', 'LocalBusiness', 'Organization'],
    '@id'                        => $site_url . '/#business',
    'name'                       => $business['name'] ?? 'CashSecond',
    'alternateName'              => 'CashSecond iPhone Buyback & Valuation Mumbai',
    'legalName'                  => 'CashSecond',
    'url'                        => $home_url,
    'logo'                       => ['@id' => $site_url . '/#logo'],
    'image'                      => ['@id' => $site_url . '/#primaryimage'],
    'telephone'                  => $business['phone_raw'] ?? '+918976332211',
    'email'                      => $business['email'] ?? 'cashsecondofficial@gmail.com',
    'priceRange'                 => '₹4,000 - ₹1,40,000',
    'currenciesAccepted'         => 'INR',
    'paymentAccepted'            => 'Cash, UPI, Google Pay, PhonePe, Paytm, IMPS Bank Transfer',
    'disambiguatingDescription'  => 'CashSecond is an independent pre-owned Apple iPhone valuation, doorstep inspection, and buyback platform in Mumbai, Maharashtra. Not affiliated with Apple Inc.',
    'hasMap'                     => 'https://maps.google.com/?q=Arcadia+Building+NCPA+Marg+Nariman+Point+Mumbai+400021',
    'parentOrganization'         => [
        '@type'  => 'Organization',
        '@id'    => 'https://cashsecond.com/#organization',
        'name'   => 'CashSecond',
        'url'    => 'https://cashsecond.com/',
        'logo'   => $site_url . '/assets/images/cashsecond-logo.png',
        'sameAs' => [
            'https://cashsecond.com/',
            'https://maps.google.com/?q=Arcadia+Building+NCPA+Marg+Nariman+Point+Mumbai+400021'
        ],
    ],
    'sameAs'                     => [
        'https://cashsecond.com/',
        'https://maps.google.com/?q=Arcadia+Building+NCPA+Marg+Nariman+Point+Mumbai+400021'
    ],
    'address'                    => [
        '@type'           => 'PostalAddress',
        'streetAddress'   => $business['address'] ?? 'Arcadia Bldg, NCPA Marg, Nariman Point',
        'addressLocality' => $business['city'] ?? 'Mumbai',
        'addressRegion'   => $business['state'] ?? 'Maharashtra',
        'postalCode'      => $business['pincode'] ?? '400021',
        'addressCountry'  => 'IN',
    ],
    'geo'                        => [
        '@type'     => 'GeoCoordinates',
        'latitude'  => 18.928000,
        'longitude' => 72.825833,
    ],
    'contactPoint'               => [
        '@type'             => 'ContactPoint',
        'telephone'         => $business['phone_raw'] ?? '+918976332211',
        'contactType'       => 'customer service',
        'areaServed'        => 'IN',
        'availableLanguage' => ['English', 'Hindi', 'Marathi'],
    ],
    'areaServed'                 => [
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
    'openingHoursSpecification'  => [
        [
            '@type'     => 'OpeningHoursSpecification',
            'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
            'opens'     => '10:00',
            'closes'    => '21:00',
        ],
    ],
    'knowsAbout'                 => [
        'Apple iPhone Resale Value',
        'Used iPhone Buyback',
        'Doorstep Mobile Evaluation',
        'Secure Device Factory Reset',
        'Used Mobile Phone Diagnostics'
    ],
];
$graph[] = $localBusinessSchema;

// -------------------------------------------------------------
// 3. WebSite Entity
// -------------------------------------------------------------
$websiteSchema = [
    '@type'           => 'WebSite',
    '@id'             => $site_url . '/#website',
    'url'             => $home_url,
    'name'            => $business['short_name'] ?? 'CashSecond',
    'description'     => 'Sell your used or old iPhone online for the best price in Mumbai with instant valuation and free doorstep pickup.',
    'publisher'       => ['@id' => $site_url . '/#business'],
    'isPartOf'        => [
        '@type' => 'WebSite',
        '@id'   => 'https://cashsecond.com/#website',
        'name'  => 'CashSecond',
        'url'   => 'https://cashsecond.com/',
    ],
    'inLanguage'      => 'en-IN',
    'potentialAction' => [
        '@type'       => 'SearchAction',
        'target'      => [
            '@type'       => 'EntryPoint',
            'urlTemplate' => $site_url . '/#valuation',
        ],
        'query-input' => 'required name=search_term_string',
    ],
];
$graph[] = $websiteSchema;

// -------------------------------------------------------------
// 4. BreadcrumbList Entity
// -------------------------------------------------------------
$breadcrumbItems = [
    [
        '@type'    => 'ListItem',
        'position' => 1,
        'name'     => 'Home',
        'item'     => $home_url,
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
    '@type'           => 'BreadcrumbList',
    '@id'             => $current_page_url . '#breadcrumb',
    'itemListElement' => $breadcrumbItems,
];
$graph[] = $breadcrumbSchema;

// -------------------------------------------------------------
// 5. WebPage Entity with Speakable Specification (Voice Search & AEO)
// -------------------------------------------------------------
$webPageSchema = [
    '@type'               => 'WebPage',
    '@id'                 => $current_page_url . '#webpage',
    'url'                 => $current_page_url,
    'name'                => $page_title ?? ($config['seo']['meta_title'] ?? 'Sell iPhone Online & Check Resale Value | CashSecond Mumbai'),
    'description'         => $page_description ?? ($config['seo']['meta_description'] ?? 'Sell your used or old iPhone online for the best price in Mumbai. Get instant iPhone valuation, free doorstep pickup, certified data wipe, and spot payment.'),
    'isPartOf'            => ['@id' => $site_url . '/#website'],
    'about'               => ['@id' => $site_url . '/#business'],
    'breadcrumb'          => ['@id' => $current_page_url . '#breadcrumb'],
    'primaryImageOfPage'  => ['@id' => $site_url . '/#primaryimage'],
    'inLanguage'          => 'en-IN',
    'speakable'           => [
        '@type'       => 'SpeakableSpecification',
        'cssSelector' => ['.hero-main-title', '.hero-main-subtitle', '.faq-question-text', '.faq-content p'],
    ],
];
$graph[] = $webPageSchema;

// -------------------------------------------------------------
// 6. Service Entity (iPhone Valuation & Doorstep Buyback)
// -------------------------------------------------------------
$serviceSchema = [
    '@type'        => 'Service',
    '@id'          => $site_url . '/#service',
    'name'         => 'Apple iPhone Buyback, Valuation & Doorstep Payout',
    'serviceType'  => 'iPhone Valuation, Resale, Doorstep Inspection, and Buyback',
    'category'     => 'Electronics Buyback & Recycling',
    'provider'     => ['@id' => $site_url . '/#business'],
    'areaServed'   => [
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
    'description'  => 'Sell your used or old iPhone online for the best price with instant valuation, free doorstep pickup across Mumbai, certified data wipe, and spot payment.',
    'termsOfService' => $site_url . '/policies/terms.php',
];
$graph[] = $serviceSchema;

// -------------------------------------------------------------
// 7. Homepage Rich Schemas: FAQPage, HowTo, and ItemList
// -------------------------------------------------------------
if ($is_home_page) {
    // FAQPage Schema (AEO / Answer Engine Direct Answers)
    $faqItems = [];
    foreach ($faqs as $faq) {
        $faqItems[] = [
            '@type'          => 'Question',
            'name'           => $faq['q'],
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text'  => $faq['a'],
            ],
        ];
    }
    $faqSchema = [
        '@type'      => 'FAQPage',
        '@id'        => $site_url . '/#faq',
        'mainEntity' => $faqItems,
    ];
    $graph[] = $faqSchema;

    // HowTo Schema (AEO / Step-by-Step AI Guidance)
    $howToSchema = [
        '@type'         => 'HowTo',
        '@id'           => $site_url . '/#howto',
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
    $graph[] = $howToSchema;

    // ItemList / Product Resale Catalog
    $productItems = [];
    $itemPosition = 1;
    foreach ($iphoneModels as $item) {
        $productItems[] = [
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
                        '@id' => $site_url . '/#business',
                    ],
                ],
            ],
        ];
    }
    $productCatalogSchema = [
        '@type'           => 'ItemList',
        '@id'             => $site_url . '/#catalog',
        'name'            => 'Apple iPhone Models for Valuation and Resale',
        'description'     => 'Apple iPhone models eligible for instant online valuation, doorstep inspection, and buyback in Mumbai, India.',
        'itemListElement' => $productItems,
    ];
    $graph[] = $productCatalogSchema;
}

// Final Schema.org structure wrapper
$finalSchema = [
    '@context' => 'https://schema.org',
    '@graph'   => $graph,
];
?>

<!-- Consolidated Schema.org JSON-LD Knowledge Graph (SEO, AEO & GEO Optimized) -->
<script type="application/ld+json">
<?= json_encode($finalSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>
</script>
