<?php
header('Content-Type: application/json');
header('Cache-Control: public, max-age=3600');

$catalog = require __DIR__ . '/../data/catalog.php';
$buyProducts = $catalog['buy_products'] ?? [];
$sellBrands = $catalog['sell_brands'] ?? [];

$action = $_GET['action'] ?? 'search';
$query = trim($_GET['query'] ?? '');
$brand = trim($_GET['brand'] ?? '');

// 1. Action: Get all models for a specific brand
if ($action === 'brand_models') {
    if (!$brand || !isset($sellBrands[$brand])) {
        echo json_encode(['status' => 'error', 'message' => 'Brand not found', 'models' => []]);
        exit;
    }
    
    echo json_encode([
        'status' => 'success',
        'brand' => $brand,
        'count' => count($sellBrands[$brand]),
        'models' => $sellBrands[$brand]
    ]);
    exit;
}

// 2. Action: Global autocomplete search (searches both buy & sell catalog)
if ($action === 'search' || $action === 'autocomplete') {
    if (strlen($query) < 1) {
        echo json_encode(['status' => 'success', 'results' => [], 'buy_results' => [], 'sell_results' => []]);
        exit;
    }

    $tokens = array_filter(preg_split('/\s+/', trim(preg_replace('/[^a-z0-9\s]/', ' ', strtolower($query)))));
    $sellMatches = [];
    $buyMatches = [];

    // Search Buy products
    foreach ($buyProducts as $p) {
        $pSearch = strtolower($p['model'] . ' ' . ($p['seo_name'] ?? '') . ' ' . $p['brand'] . ' ' . $p['variant'] . ' ' . $p['storage'] . ' ' . ($p['keywords'] ?? '') . ' sell buy resale price');
        $pMatch = true;
        foreach ($tokens as $t) {
            if (strpos($pSearch, $t) === false) {
                $pMatch = false;
                break;
            }
        }
        if ($pMatch) {
            $buyMatches[] = $p;
        }
    }

    // Search Sell models across all brands
    foreach ($sellBrands as $bName => $models) {
        foreach ($models as $m) {
            $mKeywords = $m['keywords'] ?? 'sell resale buyback exchange price value used old valuation trade-in';
            $mSeoName = $m['seo_name'] ?? $m['product_name'];
            $mSearch = strtolower($m['product_name'] . ' ' . $mSeoName . ' ' . $mKeywords . ' ' . $bName . ' sell resale buyback exchange price value used old valuation');
            
            $mMatch = true;
            foreach ($tokens as $t) {
                if (strpos($mSearch, $t) === false) {
                    $mMatch = false;
                    break;
                }
            }
            if ($mMatch) {
                $sellMatches[] = [
                    'product_id'   => $m['product_id'],
                    'product_name' => $m['product_name'],
                    'seo_name'     => $mSeoName,
                    'brand'        => $bName,
                    'image'        => $m['image'] ?? 'assets/images/phones/iphone-15.svg',
                    'alt_text'     => $m['alt_text'] ?? $mSeoName,
                    'series'       => $m['series'] ?? ''
                ];
                if (count($sellMatches) >= 40) {
                    break 2;
                }
            }
        }
    }

    echo json_encode([
        'status' => 'success',
        'query' => $query,
        'total_sell_matches' => count($sellMatches),
        'total_buy_matches' => count($buyMatches),
        'buy_results' => $buyMatches,
        'sell_results' => $sellMatches
    ]);
    exit;
}

// 3. Action: Get brand directory list with counts
if ($action === 'brands_list') {
    $brandsSummary = [];
    foreach ($sellBrands as $bName => $models) {
        $brandsSummary[] = [
            'brand' => $bName,
            'count' => count($models)
        ];
    }
    echo json_encode(['status' => 'success', 'brands' => $brandsSummary]);
    exit;
}

// Default response
echo json_encode([
    'status' => 'success',
    'total_sell_models' => count($sellBrands['Apple'] ?? []),
    'total_buy_products' => count($buyProducts),
    'brands_count' => count($sellBrands)
]);
