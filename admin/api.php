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

$action = $_GET['action'] ?? ($_POST['action'] ?? '');

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
        $rawInput = file_get_contents('php://input');
        $input = json_decode($rawInput, true);
        if (!$input) {
            $input = $_POST;
        }

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
        $productId = $_POST['product_id'] ?? '';
        $newPrice = trim($_POST['price'] ?? '');

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
