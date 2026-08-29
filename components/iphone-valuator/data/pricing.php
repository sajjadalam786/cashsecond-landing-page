<?php
/**
 * CashSecond iPhone Valuation Component — CSV Pricing Parser
 *
 * Parses Iphone-base-price-&-deduction-logic.csv and returns a structured
 * array of all device variants with their base price and per-defect
 * percentage deductions.
 *
 * Usage:
 *   $pricing = require __DIR__ . '/pricing.php';
 *   // $pricing['matrix']  — array of all device rows
 *   // $pricing['models']  — deduplicated model names array
 */

$csvPath = __DIR__ . '/../../../Iphone-base-price-&-deduction-logic.csv';

if (!file_exists($csvPath)) {
    return ['matrix' => [], 'models' => []];
}

$handle = fopen($csvPath, 'r');
if (!$handle) {
    return ['matrix' => [], 'models' => []];
}

// --- Read header row ---
$header = fgetcsv($handle);
if (!$header) {
    fclose($handle);
    return ['matrix' => [], 'models' => []];
}

// Clean BOM if present
$header[0] = ltrim($header[0], "\xEF\xBB\xBF");

// --- Define all deduction column names from the CSV ---
// These map directly to question triggers in valuator.js
$deductionColumns = [
    // Age / Time since purchase (mutually exclusive radio)
    'months_0_3', 'months_3_6', 'months_6_11', 'months_11_more',

    // Screen Scratches (mutually exclusive radio — severity)
    'scratch_screen_1_2', 'scratch_screen_3_4', 'multiple_scratches_screen',

    // Screen Defects (multi-select — any can be triggered)
    'back_glass_broken', 'loose_screen', 'touch_not_working',
    'dots_on_display', 'no_display', 'glass_cracked',
    'lines_on_display', 'color_fade', 'flickering',

    // Body Scratches (mutually exclusive radio — severity)
    'scratch_body_1_2', 'scratch_body_3_4', 'multiple_scratches_body',

    // Body Damage (multi-select)
    'body_curved', 'dents_1_or_2', 'multiple_dents',

    // Functional Issues (multi-select)
    'sensor_issues', 'front_camera_not_working', 'back_camera_not_working',
    'volume', 'finger_print_not_working', 'face_id_not_working',
    'wifi_issues', 'vibrator', 'battery_faulty', 'charging_port_issue',
    'speaker_not_working', 'audio_ic_problem', 'power_button_issue',
    'bluetooth_issue', 'camera_glass_broken', 'headphone_jackissue',

    // Battery health (mutually exclusive radio)
    'battery_less_80', 'battery_greater_80',

    // Accessories / Bonuses (multi-select — ADD value)
    'box', 'charger', 'invoice',
];

$matrix     = [];
$modelsMap  = []; // product_name => [storage => product_id]

while (($row = fgetcsv($handle)) !== false) {
    if (count($row) < count($header)) {
        continue; // skip malformed rows
    }

    $data = array_combine($header, $row);

    $productId   = (int)   ($data['product_id']       ?? 0);
    $productName = trim(    $data['product_name']      ?? '');
    $storage     = trim(    $data['product_storage']   ?? '');
    $basePrice   = (float) ($data['product_base_price']?? 0);

    if ($productId === 0 || empty($productName) || $basePrice <= 0) {
        continue;
    }

    // Build deductions map — only include columns that have numeric values
    $deductions = [];
    foreach ($deductionColumns as $col) {
        $val = isset($data[$col]) ? trim($data[$col]) : '';
        $numVal = ($val !== '' && is_numeric($val)) ? (float)$val : 0.0;
        $deductions[$col] = $numVal;
    }

    $entry = [
        'id'         => $productId,
        'name'       => $productName,
        'storage'    => $storage,
        'base_price' => $basePrice,
        'deductions' => $deductions,
    ];

    $matrix[$productId] = $entry;

    // Build models lookup: name → storage options
    if (!isset($modelsMap[$productName])) {
        $modelsMap[$productName] = [];
    }
    $modelsMap[$productName][$storage] = $productId;
}

fclose($handle);

// Sort modelsMap by name for consistent display
ksort($modelsMap);

return [
    'matrix' => $matrix,       // All device rows keyed by product_id
    'models' => $modelsMap,    // product_name => [storage => product_id]
];
