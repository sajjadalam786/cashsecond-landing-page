<?php
/**
 * CashSecond - Admin AJAX API
 * Handles real-time reading, filtering, and safe atomic updates of Iphone-base-price-&-deduction-logic.csv
 */

require_once __DIR__ . '/auth.php';

header('Content-Type: application/json; charset=UTF-8');

if (!isAdminLoggedIn()) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized. Please login again.']);
    exit;
}

$csvPath = dirname(__DIR__) . '/Iphone-base-price-&-deduction-logic.csv';
$backupsDir = dirname(__DIR__) . '/data/backups';

if (!is_dir($backupsDir)) {
    @mkdir($backupsDir, 0775, true);
}

$rawInput = file_get_contents('php://input');
$jsonInput = json_decode($rawInput, true);
if (!is_array($jsonInput)) {
    $jsonInput = [];
}

$action = $_GET['action'] ?? ($_POST['action'] ?? ($jsonInput['action'] ?? ''));

/**
 * Helper: Parse CSV with header preservation
 */
function readFullCsv(string $path): array
{
    if (!file_exists($path)) {
        return ['header' => [], 'rows' => []];
    }

    $handle = fopen($path, 'r');
    if (!$handle) {
        return ['header' => [], 'rows' => []];
    }

    $header = fgetcsv($handle);
    if (!$header) {
        fclose($handle);
        return ['header' => [], 'rows' => []];
    }

    // Strip UTF-8 BOM if present
    $header[0] = ltrim($header[0], "\xEF\xBB\xBF");
    $trimmedHeader = array_map('trim', $header);

    $rows = [];
    $rowIndex = 0;
    while (($data = fgetcsv($handle)) !== false) {
        // Pad row to match header length if needed
        while (count($data) < count($trimmedHeader)) {
            $data[] = '';
        }
        $rowObj = [];
        foreach ($trimmedHeader as $idx => $colName) {
            $rowObj[$colName] = $data[$idx] ?? '';
        }
        $rowObj['_row_index'] = $rowIndex;
        $rows[] = $rowObj;
        $rowIndex++;
    }
    fclose($handle);

    return ['header' => $trimmedHeader, 'rows' => $rows];
}

/**
 * Helper: Write rows back to CSV with atomic write
 */
function writeFullCsv(string $path, array $header, array $rows, string $backupsDir): bool
{
    // 1. Create automated timestamped backup copy before modifying
    if (file_exists($path)) {
        date_default_timezone_set('Asia/Kolkata');
        $backupFile = $backupsDir . '/pricing_backup_' . date('Ymd_His') . '.csv';
        @copy($path, $backupFile);

        // Keep last 30 backups to avoid disk bloat
        $allBackups = glob($backupsDir . '/pricing_backup_*.csv');
        if ($allBackups && count($allBackups) > 30) {
            usort($allBackups, function ($a, $b) {
                return filemtime($a) - filemtime($b);
            });
            while (count($allBackups) > 30) {
                $oldest = array_shift($allBackups);
                @unlink($oldest);
            }
        }
    }

    // 2. Try atomic temp file write first
    $tempFile = $path . '.tmp.' . uniqid();
    $handle = @fopen($tempFile, 'w');
    if ($handle) {
        fputcsv($handle, $header);
        foreach ($rows as $row) {
            $line = [];
            foreach ($header as $colName) {
                $line[] = $row[$colName] ?? '';
            }
            fputcsv($handle, $line);
        }
        fclose($handle);
        if (@rename($tempFile, $path)) {
            @chmod($path, 0666);
            return true;
        }
        @unlink($tempFile);
    }

    // 3. Fallback to direct locked write if directory write is restricted
    $directHandle = @fopen($path, 'w');
    if (!$directHandle) {
        return false;
    }
    flock($directHandle, LOCK_EX);
    fputcsv($directHandle, $header);
    foreach ($rows as $row) {
        $line = [];
        foreach ($header as $colName) {
            $line[] = $row[$colName] ?? '';
        }
        fputcsv($directHandle, $line);
    }
    fflush($directHandle);
    flock($directHandle, LOCK_UN);
    fclose($directHandle);
    @chmod($path, 0666);
    return true;
}

switch ($action) {
    case 'get_data':
        $parsed = readFullCsv($csvPath);
        $lastModified = file_exists($csvPath) ? filemtime($csvPath) : 0;
        date_default_timezone_set('Asia/Kolkata');

        // Extract deduplicated model names and storage variants
        $models = [];
        $storages = [];
        $minPrice = PHP_INT_MAX;
        $maxPrice = 0;

        foreach ($parsed['rows'] as $r) {
            $m = $r['product_name'] ?? '';
            $s = $r['product_storage'] ?? '';
            $p = (int)str_replace(',', '', $r['product_base_price'] ?? '0');

            if ($m && !in_array($m, $models, true)) $models[] = $m;
            if ($s && !in_array($s, $storages, true)) $storages[] = $s;

            if ($p > 0) {
                if ($p < $minPrice) $minPrice = $p;
                if ($p > $maxPrice) $maxPrice = $p;
            }
        }

        echo json_encode([
            'status'        => 'success',
            'total_rows'    => count($parsed['rows']),
            'models'        => $models,
            'storages'      => $storages,
            'price_range'   => [
                'min' => ($minPrice === PHP_INT_MAX) ? 0 : $minPrice,
                'max' => $maxPrice
            ],
            'last_modified' => date('d M Y, h:i A', $lastModified),
            'header'        => $parsed['header'],
            'rows'          => $parsed['rows']
        ], JSON_UNESCAPED_UNICODE);
        break;

    case 'get_row':
        $productId = $_GET['product_id'] ?? '';
        if ($productId === '') {
            echo json_encode(['status' => 'error', 'message' => 'Product ID is required']);
            exit;
        }

        $parsed = readFullCsv($csvPath);
        $found = null;
        foreach ($parsed['rows'] as $r) {
            if ((string)$r['product_id'] === (string)$productId) {
                $found = $r;
                break;
            }
        }

        if ($found) {
            echo json_encode(['status' => 'success', 'data' => $found]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Product not found']);
        }
        break;

    case 'update_row':
        $input = !empty($jsonInput) ? $jsonInput : $_POST;

        $productId = $input['product_id'] ?? '';
        if ($productId === '') {
            echo json_encode(['status' => 'error', 'message' => 'Product ID is required.']);
            exit;
        }

        $parsed = readFullCsv($csvPath);
        $targetIndex = -1;

        foreach ($parsed['rows'] as $idx => $r) {
            if ((string)$r['product_id'] === (string)$productId) {
                $targetIndex = $idx;
                break;
            }
        }

        if ($targetIndex === -1) {
            echo json_encode(['status' => 'error', 'message' => 'Product ID ' . htmlspecialchars($productId) . ' not found in CSV.']);
            exit;
        }

        // Validate base price
        if (isset($input['product_base_price'])) {
            $cleanPrice = str_replace(',', '', trim($input['product_base_price']));
            if (!is_numeric($cleanPrice) || (float)$cleanPrice < 0) {
                echo json_encode(['status' => 'error', 'message' => 'Base price must be a valid positive number.']);
                exit;
            }
            $input['product_base_price'] = (string)(int)$cleanPrice;
        }

        // Apply updates to the matched row
        $updatedColumns = [];
        foreach ($parsed['header'] as $colName) {
            if (isset($input[$colName])) {
                $val = trim((string)$input[$colName]);
                // Ensure percentage deduction values are numeric or empty
                if ($colName !== 'product_name' && $colName !== 'product_storage' && $colName !== 'ram' && $colName !== 'product_variant' && $colName !== 'product_description' && $colName !== 'product_image' && $colName !== 'product_color' && $colName !== 'product_country' && $colName !== 'product_status' && $colName !== 'product_id' && $colName !== 'product_category' && $colName !== 'product_brand' && $colName !== 'product_series') {
                    if ($val !== '' && !is_numeric($val)) {
                        echo json_encode(['status' => 'error', 'message' => "Field '{$colName}' must be a numeric percentage."]);
                        exit;
                    }
                }
                $parsed['rows'][$targetIndex][$colName] = $val;
                $updatedColumns[] = $colName;
            }
        }

        // Write safely to CSV
        $success = writeFullCsv($csvPath, $parsed['header'], $parsed['rows'], $backupsDir);

        if ($success) {
            date_default_timezone_set('Asia/Kolkata');
            echo json_encode([
                'status'          => 'success',
                'message'         => 'Product ' . htmlspecialchars($parsed['rows'][$targetIndex]['product_name']) . ' (' . htmlspecialchars($parsed['rows'][$targetIndex]['product_storage']) . ') updated successfully in real time!',
                'product_id'      => $productId,
                'updated_columns' => $updatedColumns,
                'updated_row'     => $parsed['rows'][$targetIndex],
                'last_modified'   => date('d M Y, h:i A')
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to write changes to CSV. Please check folder permissions.']);
        }
        break;

    case 'quick_update_price':
        $input = !empty($jsonInput) ? $jsonInput : $_POST;
        $productId = $input['product_id'] ?? '';
        $newPrice = trim((string)($input['price'] ?? ''));

        if ($productId === '' || !is_numeric(str_replace(',', '', $newPrice))) {
            echo json_encode(['status' => 'error', 'message' => 'Valid Product ID and numeric price are required.']);
            exit;
        }

        $cleanPrice = (string)(int)str_replace(',', '', $newPrice);

        $parsed = readFullCsv($csvPath);
        $targetIndex = -1;
        foreach ($parsed['rows'] as $idx => $r) {
            if ((string)$r['product_id'] === (string)$productId) {
                $targetIndex = $idx;
                break;
            }
        }

        if ($targetIndex === -1) {
            echo json_encode(['status' => 'error', 'message' => 'Product not found.']);
            exit;
        }

        $parsed['rows'][$targetIndex]['product_base_price'] = $cleanPrice;
        $success = writeFullCsv($csvPath, $parsed['header'], $parsed['rows'], $backupsDir);

        if ($success) {
            date_default_timezone_set('Asia/Kolkata');
            echo json_encode([
                'status'        => 'success',
                'message'       => 'Base price updated to ₹' . number_format((int)$cleanPrice) . ' successfully!',
                'product_id'    => $productId,
                'new_price'     => $cleanPrice,
                'last_modified' => date('d M Y, h:i A')
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update price.']);
        }
        break;

    case 'get_leads':
        $logsDir = dirname(__DIR__) . '/logs';
        $leads = [];
        $leadFiles = ['questionnaire_leads.jsonl', 'valuator_leads.jsonl'];
        
        foreach ($leadFiles as $file) {
            $path = $logsDir . '/' . $file;
            if (file_exists($path)) {
                $lines = @file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                if ($lines) {
                    foreach (array_reverse($lines) as $line) {
                        $item = json_decode($line, true);
                        if ($item) {
                            $leads[] = $item;
                            if (count($leads) >= 15) break 2;
                        }
                    }
                }
            }
        }

        echo json_encode(['status' => 'success', 'leads' => $leads]);
        break;

    case 'get_backups':
        $backupFiles = glob($backupsDir . '/pricing_backup_*.csv');
        $backups = [];
        if ($backupFiles) {
            usort($backupFiles, function ($a, $b) {
                return filemtime($b) - filemtime($a);
            });
            foreach (array_slice($backupFiles, 0, 10) as $f) {
                $backups[] = [
                    'name'     => basename($f),
                    'size'     => round(filesize($f) / 1024, 1) . ' KB',
                    'modified' => date('d M Y, h:i:s A', filemtime($f))
                ];
            }
        }
        echo json_encode(['status' => 'success', 'backups' => $backups]);
        break;

    case 'create_backup':
        if (!file_exists($csvPath)) {
            echo json_encode(['status' => 'error', 'message' => 'Source CSV file not found.']);
            exit;
        }
        if (!is_dir($backupsDir)) {
            @mkdir($backupsDir, 0777, true);
        }
        $snapshotName = 'pricing_backup_' . date('Ymd_His') . '_manual.csv';
        $dest = $backupsDir . '/' . $snapshotName;
        if (@copy($csvPath, $dest)) {
            @chmod($dest, 0666);
            echo json_encode([
                'status' => 'success',
                'message' => 'Backup created: ' . $snapshotName,
                'backup' => [
                    'name'     => $snapshotName,
                    'size'     => round(filesize($dest) / 1024, 1) . ' KB',
                    'modified' => date('d M Y, h:i:s A', filemtime($dest))
                ]
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to create backup snapshot.']);
        }
        break;

    case 'bulk_import_csv':
        $input = !empty($jsonInput) ? $jsonInput : $_POST;
        $importRows = $input['rows'] ?? [];
        $mode = $input['mode'] ?? 'merge_append'; // 'merge_append', 'update_only', 'replace'

        if (empty($importRows) || !is_array($importRows)) {
            echo json_encode(['status' => 'error', 'message' => 'No valid data rows provided for import.']);
            exit;
        }

        $parsed = readFullCsv($csvPath);
        $header = $parsed['header'];
        $existingRows = $parsed['rows'];

        $defaultHeader = [
            'product_id','product_category','product_brand','product_series',
            'product_name','product_storage','ram','product_variant','product_color',
            'product_country','product_base_price','product_description','product_image',
            'months_0_3','months_3_6','months_6_11','months_11_more',
            'scratch_screen_1_2','scratch_screen_3_4','multiple_scratches_screen',
            'no_problem','back_glass_broken','loose_screen','touch_not_working',
            'dots_on_display','no_display','glass_cracked','lines_on_display',
            'color_fade','flickering','scratch_body_1_2','scratch_body_3_4',
            'multiple_scratches_body','body_curved','dents_1_or_2','multiple_dents',
            'sensor_issues','front_camera_not_working','back_camera_not_working',
            'volume','finger_print_not_working','face_id_not_working','wifi_issues',
            'vibrator','battery_faulty','charging_port_issue','speaker_not_working',
            'audio_ic_problem','power_button_issue','bluetooth_issue',
            'camera_glass_broken','headphone_jackissue','battery_less_80',
            'battery_greater_80','box','charger','invoice','product_status'
        ];

        if (empty($header)) {
            $header = $defaultHeader;
        }

        // Helper: normalize key for comparison
        $normKey = function ($val) {
            $val = strtolower(trim((string)$val));
            return preg_replace('/[^a-z0-9]/', '', $val);
        };

        $maxId = 0;
        $idIndexMap = [];
        $compositeIndexMap = [];

        foreach ($existingRows as $idx => $r) {
            $currId = (int)($r['product_id'] ?? 0);
            if ($currId > $maxId) {
                $maxId = $currId;
            }
            if (!empty($r['product_id'])) {
                $idIndexMap[(string)$r['product_id']] = $idx;
            }
            $pNameNorm = $normKey($r['product_name'] ?? '');
            $pStorageNorm = $normKey($r['product_storage'] ?? '');
            if ($pNameNorm !== '' && $pStorageNorm !== '') {
                $compositeIndexMap[$pNameNorm . '___' . $pStorageNorm] = $idx;
            }
        }

        $updatedCount = 0;
        $addedCount = 0;
        $skippedCount = 0;
        $finalRows = [];

        if ($mode === 'replace') {
            // Full replace mode
            $rowId = 1;
            foreach ($importRows as $inRow) {
                $newRow = [];
                foreach ($header as $col) {
                    $newRow[$col] = isset($inRow[$col]) ? trim((string)$inRow[$col]) : '';
                }
                if (empty($newRow['product_id'])) {
                    $newRow['product_id'] = (string)$rowId++;
                }
                if (empty($newRow['product_category'])) $newRow['product_category'] = '1';
                if (empty($newRow['product_brand']))    $newRow['product_brand'] = '1';
                if (empty($newRow['product_country']))  $newRow['product_country'] = 'India';
                if (empty($newRow['product_status']))   $newRow['product_status'] = '1';
                
                // Clean price
                if (isset($newRow['product_base_price'])) {
                    $newRow['product_base_price'] = (string)(int)str_replace(',', '', $newRow['product_base_price']);
                }
                $finalRows[] = $newRow;
                $addedCount++;
            }
        } else {
            // 'merge_append' or 'update_only'
            $workingRows = $existingRows;

            foreach ($importRows as $inRow) {
                $matchedIndex = -1;

                // 1. Try match by product_id
                $inId = isset($inRow['product_id']) ? trim((string)$inRow['product_id']) : '';
                if ($inId !== '' && isset($idIndexMap[$inId])) {
                    $matchedIndex = $idIndexMap[$inId];
                }

                // 2. Try match by Model Name + Storage
                if ($matchedIndex === -1) {
                    $inNameNorm = $normKey($inRow['product_name'] ?? '');
                    $inStorageNorm = $normKey($inRow['product_storage'] ?? '');
                    $compKey = $inNameNorm . '___' . $inStorageNorm;
                    if ($inNameNorm !== '' && $inStorageNorm !== '' && isset($compositeIndexMap[$compKey])) {
                        $matchedIndex = $compositeIndexMap[$compKey];
                    }
                }

                if ($matchedIndex !== -1) {
                    // Update existing row with supplied fields
                    foreach ($inRow as $k => $v) {
                        if (in_array($k, $header, true) && $v !== '') {
                            $cleanVal = trim((string)$v);
                            if ($k === 'product_base_price') {
                                $cleanVal = (string)(int)str_replace(',', '', $cleanVal);
                            }
                            $workingRows[$matchedIndex][$k] = $cleanVal;
                        }
                    }
                    $updatedCount++;
                } else {
                    // Not matched
                    if ($mode === 'merge_append') {
                        $newRow = [];
                        foreach ($header as $col) {
                            $newRow[$col] = isset($inRow[$col]) ? trim((string)$inRow[$col]) : '';
                        }
                        if (empty($newRow['product_id'])) {
                            $maxId++;
                            $newRow['product_id'] = (string)$maxId;
                        } else {
                            $maxId = max($maxId, (int)$newRow['product_id']);
                        }
                        if (empty($newRow['product_category'])) $newRow['product_category'] = '1';
                        if (empty($newRow['product_brand']))    $newRow['product_brand'] = '1';
                        if (empty($newRow['product_country']))  $newRow['product_country'] = 'India';
                        if (empty($newRow['product_status']))   $newRow['product_status'] = '1';
                        if (isset($newRow['product_base_price'])) {
                            $newRow['product_base_price'] = (string)(int)str_replace(',', '', $newRow['product_base_price']);
                        }
                        $workingRows[] = $newRow;
                        $addedCount++;

                        // Add to map for subsequent rows in this same batch
                        $newIdx = count($workingRows) - 1;
                        $idIndexMap[(string)$newRow['product_id']] = $newIdx;
                        $pNameNorm = $normKey($newRow['product_name'] ?? '');
                        $pStorageNorm = $normKey($newRow['product_storage'] ?? '');
                        if ($pNameNorm !== '' && $pStorageNorm !== '') {
                            $compositeIndexMap[$pNameNorm . '___' . $pStorageNorm] = $newIdx;
                        }
                    } else {
                        $skippedCount++;
                    }
                }
            }

            $finalRows = $workingRows;
        }

        // Write safely to CSV with auto-backup
        $success = writeFullCsv($csvPath, $header, $finalRows, $backupsDir);

        if ($success) {
            date_default_timezone_set('Asia/Kolkata');
            echo json_encode([
                'status'         => 'success',
                'message'        => "Import completed! {$updatedCount} updated, {$addedCount} added" . ($skippedCount > 0 ? ", {$skippedCount} skipped." : "."),
                'updated_count'  => $updatedCount,
                'added_count'    => $addedCount,
                'skipped_count'  => $skippedCount,
                'total_rows'     => count($finalRows),
                'last_modified'  => date('d M Y, h:i A')
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to save imported data to CSV. Check write permissions.']);
        }
        break;

    case 'parse_excel':
        if (!isset($_FILES['excel_file']) || $_FILES['excel_file']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['status' => 'error', 'message' => 'No valid Excel file was uploaded.']);
            exit;
        }

        $tmpPath = $_FILES['excel_file']['tmp_name'];
        $origName = $_FILES['excel_file']['name'] ?? 'file.xlsx';
        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

        if ($ext !== 'xlsx' && $ext !== 'xls' && $ext !== 'csv') {
            echo json_encode(['status' => 'error', 'message' => 'Unsupported format. Please upload .xlsx or .csv.']);
            exit;
        }

        if ($ext === 'csv') {
            $parsed = readFullCsv($tmpPath);
            echo json_encode([
                'status'  => 'success',
                'headers' => $parsed['header'],
                'rows'    => $parsed['rows']
            ]);
            exit;
        }

        if (!class_exists('ZipArchive')) {
            echo json_encode(['status' => 'error', 'message' => 'PHP ZipArchive is not enabled on server. Please use client-side XLSX parser.']);
            exit;
        }

        $zip = new ZipArchive();
        if ($zip->open($tmpPath) !== true) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to extract Excel workbook. The file may be corrupt or encrypted.']);
            exit;
        }

        // 1. Read shared strings
        $sharedStrings = [];
        $sharedXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedXml !== false) {
            $xml = @simplexml_load_string($sharedXml);
            if ($xml) {
                foreach ($xml->si as $si) {
                    if (isset($si->t)) {
                        $sharedStrings[] = (string)$si->t;
                    } elseif (isset($si->r)) {
                        $text = '';
                        foreach ($si->r as $r) {
                            $text .= (string)$r->t;
                        }
                        $sharedStrings[] = $text;
                    } else {
                        $sharedStrings[] = '';
                    }
                }
            }
        }

        // 2. Read first sheet XML
        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        if ($sheetXml === false) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $entry = $zip->getNameIndex($i);
                if (preg_match('#xl/worksheets/sheet\d+\.xml#', $entry)) {
                    $sheetXml = $zip->getFromName($entry);
                    break;
                }
            }
        }
        $zip->close();

        if ($sheetXml === false) {
            echo json_encode(['status' => 'error', 'message' => 'No readable sheets found inside the Excel file.']);
            exit;
        }

        $xml = @simplexml_load_string($sheetXml);
        if (!$xml || !isset($xml->sheetData)) {
            echo json_encode(['status' => 'error', 'message' => 'Could not parse Excel worksheet XML.']);
            exit;
        }

        $grid = [];
        foreach ($xml->sheetData->row as $row) {
            $rowCells = [];
            foreach ($row->c as $c) {
                $cellRef = (string)$c['r'];
                $colLetter = preg_replace('/[0-9]/', '', $cellRef);
                $type = (string)$c['t'];
                $val = '';

                if ($type === 's') {
                    $idx = (int)$c->v;
                    $val = $sharedStrings[$idx] ?? '';
                } elseif ($type === 'inlineStr') {
                    $val = (string)($c->is->t ?? '');
                } else {
                    $val = (string)($c->v ?? '');
                }
                $rowCells[$colLetter] = trim($val);
            }
            if (!empty($rowCells)) {
                $grid[] = $rowCells;
            }
        }

        if (empty($grid)) {
            echo json_encode(['status' => 'error', 'message' => 'The uploaded Excel file contains no data rows.']);
            exit;
        }

        $colToIndex = function ($col) {
            $len = strlen($col);
            $idx = 0;
            for ($i = 0; $i < $len; $i++) {
                $idx = $idx * 26 + (ord($col[$i]) - ord('A') + 1);
            }
            return $idx - 1;
        };

        $allCols = [];
        foreach ($grid as $r) {
            foreach (array_keys($r) as $k) {
                if (!in_array($k, $allCols, true)) $allCols[] = $k;
            }
        }
        usort($allCols, function ($a, $b) use ($colToIndex) {
            return $colToIndex($a) - $colToIndex($b);
        });

        $headerRow = array_shift($grid);
        $headers = [];
        foreach ($allCols as $col) {
            $h = trim((string)($headerRow[$col] ?? ''));
            $headers[] = $h !== '' ? $h : "Column $col";
        }

        $rows = [];
        foreach ($grid as $r) {
            $rowObj = [];
            $hasData = false;
            foreach ($allCols as $idx => $col) {
                $v = $r[$col] ?? '';
                if ($v !== '') $hasData = true;
                $rowObj[$headers[$idx]] = $v;
            }
            if ($hasData) {
                $rows[] = $rowObj;
            }
        }

        echo json_encode([
            'status'  => 'success',
            'headers' => $headers,
            'rows'    => $rows
        ]);
        break;

    case 'download_csv':
        if (file_exists($csvPath)) {
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="Iphone-base-price-&-deduction-logic-' . date('Y-m-d') . '.csv"');
            readfile($csvPath);
            exit;
        } else {
            echo json_encode(['status' => 'error', 'message' => 'CSV file not found.']);
        }
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Unknown action.']);
        break;
}
