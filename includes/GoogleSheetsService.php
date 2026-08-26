<?php
/**
 * CashSecond - Google Sheets Integration Service
 * Formats full valuation payloads with all 72 columns and securely sends them to Google Sheets.
 */

class GoogleSheetsService
{
    private static $ramMap = [
        'Apple iPhone 16 Pro Max' => '8 GB',
        'Apple iPhone 16 Pro'     => '8 GB',
        'Apple iPhone 16 Plus'    => '8 GB',
        'Apple iPhone 16'         => '8 GB',
        'Apple iPhone 15 Pro Max' => '8 GB',
        'Apple iPhone 15 Pro'     => '8 GB',
        'Apple iPhone 15 Plus'    => '6 GB',
        'Apple iPhone 15'         => '6 GB',
        'Apple iPhone 14 Pro Max' => '6 GB',
        'Apple iPhone 14 Pro'     => '6 GB',
        'Apple iPhone 14 Plus'    => '6 GB',
        'Apple iPhone 14'         => '6 GB',
        'Apple iPhone 13 Pro Max' => '6 GB',
        'Apple iPhone 13 Pro'     => '6 GB',
        'Apple iPhone 13'         => '4 GB',
        'Apple iPhone 13 mini'    => '4 GB',
        'Apple iPhone 12 Pro Max' => '6 GB',
        'Apple iPhone 12 Pro'     => '6 GB',
        'Apple iPhone 12'         => '4 GB',
        'Apple iPhone 12 mini'    => '4 GB',
        'Apple iPhone 11 Pro Max' => '4 GB',
        'Apple iPhone 11 Pro'     => '4 GB',
        'Apple iPhone 11'         => '4 GB',
        'Apple iPhone SE (2022)'  => '4 GB',
        'Apple iPhone SE (2020)'  => '3 GB'
    ];

    /**
     * Build the structured 72-column Google Sheet row payload
     */
    public static function buildSheetRowData(array $leadData): array
    {
        date_default_timezone_set('Asia/Kolkata');

        $now = time();
        $date = date('Y-m-d', $now);
        $time = date('H:i:s', $now);

        // Generate Structured Lead ID: EXG-YYYYMMDD-XXX
        $leadId = $leadData['lead_id'] ?? ('EXG-' . date('Ymd', $now) . '-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 4)));

        $customer = $leadData['customer'] ?? [];
        $device   = $leadData['device'] ?? [];
        $answers  = $leadData['answers'] ?? [];
        $adjustments = $leadData['adjustments'] ?? [];

        $modelName = $device['model'] ?? 'Apple iPhone 13';
        $variant   = $device['variant'] ?? '128 GB';
        $battery   = $device['battery'] ?? ($answers['battery_health'] ?? '89%');
        $baseVal   = $device['base_val'] ?? '₹23,220';
        $finalVal  = $device['estimated_val'] ?? '₹23,220';

        $ram = self::$ramMap[$modelName] ?? '4 GB';

        // Helper to normalize Yes / No
        $getAns = function ($key, $default = 'YES') use ($answers) {
            if (!isset($answers[$key])) return $default;
            $val = $answers[$key];
            if (is_bool($val)) return $val ? 'YES' : 'NO';
            $val = trim((string)$val);
            if (strncasecmp($val, 'Yes', 3) === 0) return 'YES';
            if (strncasecmp($val, 'No', 2) === 0) return 'NO';
            return strtoupper($val);
        };

        // 1. Functional & Physical Answers
        $ansPower       = $getAns('power_on', 'YES');
        $ansDispWork    = $getAns('display_working', 'YES');
        $ansTouch       = $getAns('touch_screen', 'YES');
        $ansDispFlaws   = $getAns('display_flaws', 'NO');
        $ansScreenCrack = $getAns('screen_cracked', 'NO');
        $ansScreenScr   = $getAns('screen_scratches', 'NO');
        $ansBodyDents   = $getAns('body_dents', 'YES');
        $ansBodyBent    = $getAns('body_bent', 'NO');
        $ansBodyDmg     = $getAns('body_visible_damage', 'NO');
        $ansCamGlass    = $getAns('camera_glass_crack', 'NO');
        $ansMissing     = $getAns('missing_parts', 'NO');
        $ansRearCam     = $getAns('rear_camera', 'YES');
        $ansFrontCam    = $getAns('front_camera', 'YES');
        $ansFlash       = $getAns('camera_flash', 'YES');
        $ansSpeaker     = $getAns('loudspeaker', 'YES');
        $ansEarRec      = $getAns('earpiece_receiver', 'YES');
        $ansMic         = $getAns('microphone', 'YES');
        $ansPowerBtn    = $getAns('power_button', 'YES');
        $ansVolBtn      = $getAns('volume_buttons', 'YES');
        $ansSilentSw    = $getAns('silent_switch', 'YES');
        $ansChargePort  = $getAns('charging_port', 'YES');
        $ansChargeWork  = $getAns('charges_normally', 'YES');
        $ansBio         = $getAns('biometrics', 'YES');
        $ansWifi        = $getAns('wifi_working', 'YES');
        $ansBT          = $getAns('bluetooth_working', 'YES');
        $ansCellular    = $getAns('cellular_sim', 'YES');
        $ansGPS         = $getAns('gps_location', 'YES');
        $ansLiquid      = $getAns('liquid_damage', 'NO');

        // 2. Parts & History
        $ansDispOrig    = $getAns('display_original', 'YES');
        $ansMajorRep    = $getAns('parts_replaced', 'NO');
        $ansReplacedComp= ($ansMajorRep === 'YES') ? ($answers['replaced_component_name'] ?? 'Display / Battery') : 'None';
        $ansWarranty    = $answers['warranty_status'] ?? 'YES (Under 11 Months)';
        $ansBill        = $getAns('bill_invoice', 'YES');
        $ansBox         = $getAns('has_box', 'YES');
        $ansCable       = $getAns('has_cable', 'YES');

        // 3. Failed Tests and Stats Calculation
        $testChecks = [
            'Phone Powers On'                     => ($ansPower === 'YES'),
            'Display Working'                     => ($ansDispWork === 'YES'),
            'Touchscreen'                         => ($ansTouch === 'YES'),
            'Display Lines / Spots / Flickering'  => ($ansDispFlaws === 'NO'),
            'Screen Cracked'                      => ($ansScreenCrack === 'NO'),
            'Screen Major Scratches'              => ($ansScreenScr === 'NO'),
            'Body Dents'                          => ($ansBodyDents === 'YES'),
            'Phone Bent'                          => ($ansBodyBent === 'NO'),
            'Body Visible Damage'                 => ($ansBodyDmg === 'NO'),
            'Camera Glass Condition'              => ($ansCamGlass === 'NO'),
            'Missing Parts'                       => ($ansMissing === 'NO'),
            'Rear Camera'                         => ($ansRearCam === 'YES'),
            'Front Camera'                        => ($ansFrontCam === 'YES'),
            'Camera Flash'                        => ($ansFlash === 'YES'),
            'Loudspeaker'                         => ($ansSpeaker === 'YES'),
            'Ear Receiver'                        => ($ansEarRec === 'YES'),
            'Microphone'                          => ($ansMic === 'YES'),
            'Power Button'                        => ($ansPowerBtn === 'YES'),
            'Volume Buttons'                      => ($ansVolBtn === 'YES'),
            'Silent Switch'                       => ($ansSilentSw === 'YES'),
            'Charging Port'                       => ($ansChargePort === 'YES'),
            'Charging Function'                   => ($ansChargeWork === 'YES'),
            'Biometrics (Face ID / Touch ID)'     => ($ansBio === 'YES'),
            'Wi-Fi'                               => ($ansWifi === 'YES'),
            'Bluetooth'                           => ($ansBT === 'YES'),
            'Mobile Network / SIM'                => ($ansCellular === 'YES'),
            'GPS Location'                        => ($ansGPS === 'YES'),
            'Liquid Damage'                       => ($ansLiquid === 'NO'),
            'Original Display'                    => ($ansDispOrig === 'YES'),
            'Component Replacement'               => ($ansMajorRep === 'NO'),
            'Original Bill'                       => ($ansBill === 'YES'),
            'Original Box'                        => ($ansBox === 'YES')
        ];

        $totalTests = count($testChecks);
        $passedTests = 0;
        $failedTests = 0;
        $failedNames = [];

        foreach ($testChecks as $tName => $isPass) {
            if ($isPass) {
                $passedTests++;
            } else {
                $failedTests++;
                $failedNames[] = $tName;
            }
        }

        $passPercentage = round(($passedTests / $totalTests) * 100, 2) . '%';
        $failedTestString = empty($failedNames) ? 'None (All Passed)' : implode(', ', $failedNames);

        // 4. Valuation Adjustments Breakdown
        $adjStorage    = $adjustments['storage'] ?? '+₹0';
        $adjBattery    = $adjustments['battery'] ?? ($battery === 'Below 80%' ? '-₹3,600' : ($battery === '80% – 84%' ? '-₹1,800' : ($battery === '85% – 89%' ? '-₹800' : '+₹0')));
        $adjDisplay    = $adjustments['display'] ?? ($ansDispWork === 'NO' ? '-₹5,000' : ($ansScreenCrack === 'YES' ? '-₹3,500' : ($ansDispFlaws === 'YES' ? '-₹3,800' : '+₹0')));
        $adjBody       = $adjustments['body'] ?? ($ansBodyBent === 'YES' ? '-₹2,800' : ($ansBodyDents === 'NO' ? '-₹1,500' : '+₹0'));
        $adjFunctional = $adjustments['functional'] ?? ($ansBio === 'NO' ? '-₹3,000' : ($ansChargePort === 'NO' ? '-₹1,800' : ($ansPower === 'NO' ? '-₹9,000' : '+₹0')));
        $adjLiquid     = $adjustments['liquid'] ?? ($ansLiquid === 'YES' ? '-₹4,500' : '+₹0');
        $adjParts      = $adjustments['parts'] ?? ($ansDispOrig === 'NO' ? '-₹3,500' : '+₹0');
        $adjWarranty   = $adjustments['warranty'] ?? (stripos($ansWarranty, 'YES') !== false ? '+₹1,500' : '+₹0');
        $adjAccessory  = $adjustments['accessories'] ?? (($ansBox === 'YES' && $ansCable === 'YES') ? '+₹900' : ($ansBox === 'YES' ? '+₹600' : '+₹0'));

        $totalAdj = $adjustments['total_adjustment'] ?? '₹0';

        // Full 72-column row mapping matching the Google Sheet definition
        return [
            // Lead Information (1-8)
            'submission_date'         => $date,
            'submission_time'         => $time,
            'lead_id'                 => $leadId,
            'full_name'               => $customer['name'] ?? '',
            'whatsapp_number'         => $customer['phone'] ?? '',
            'email'                   => $customer['email'] ?? '',
            'pickup_address'          => $customer['address'] ?? '',
            'pincode'                 => $customer['pincode'] ?? '',

            // Device Information (9-15)
            'brand'                   => 'Apple',
            'model'                   => $modelName,
            'ram'                     => $ram,
            'storage'                 => $variant,
            'battery_health'          => $battery,
            'base_max_value'          => $baseVal,
            'final_estimated_value'   => $finalVal,

            // Functional Tests (16-43)
            'phone_powers_on'         => $ansPower,
            'display_working'         => $ansDispWork,
            'touchscreen_working'     => $ansTouch,
            'display_lines_spots'     => $ansDispFlaws,
            'screen_cracked'          => $ansScreenCrack,
            'screen_major_scratches'  => $ansScreenScr,
            'body_condition'          => $ansBodyDents,
            'phone_bent'              => $ansBodyBent,
            'body_damage'             => $ansBodyDmg,
            'camera_glass_condition'  => $ansCamGlass,
            'missing_parts'           => $ansMissing,
            'rear_camera'             => $ansRearCam,
            'front_camera'            => $ansFrontCam,
            'camera_flash'            => $ansFlash,
            'speaker'                 => $ansSpeaker,
            'ear_receiver'            => $ansEarRec,
            'microphone'              => $ansMic,
            'power_button'            => $ansPowerBtn,
            'volume_buttons'          => $ansVolBtn,
            'silent_switch'           => $ansSilentSw,
            'charging_port'           => $ansChargePort,
            'charging_working'        => $ansChargeWork,
            'face_id_touch_id'        => $ansBio,
            'wifi'                    => $ansWifi,
            'bluetooth'               => $ansBT,
            'mobile_network_sim'      => $ansCellular,
            'gps'                     => $ansGPS,
            'liquid_damage'           => $ansLiquid,

            // Parts / History (44-50)
            'original_display'        => $ansDispOrig,
            'major_component_replaced'=> $ansMajorRep,
            'replaced_component'      => $ansReplacedComp,
            'warranty_status'         => $ansWarranty,
            'original_bill'           => $ansBill,
            'original_box'            => $ansBox,
            'original_cable_adapter'  => $ansCable,

            // Test Summary (51-55)
            'total_tests'             => $totalTests,
            'passed_tests'            => $passedTests,
            'failed_tests'            => $failedTests,
            'pass_percentage'         => $passPercentage,
            'failed_test_names'       => $failedTestString,

            // Valuation Breakdown (56-67)
            'model_base_price'        => $baseVal,
            'storage_adjustment'      => $adjStorage,
            'battery_adjustment'      => $adjBattery,
            'display_adjustment'      => $adjDisplay,
            'body_adjustment'         => $adjBody,
            'functional_adjustment'   => $adjFunctional,
            'liquid_damage_adjustment'=> $adjLiquid,
            'parts_adjustment'        => $adjParts,
            'warranty_adjustment'     => $adjWarranty,
            'accessories_adjustment'  => $adjAccessory,
            'total_adjustment'        => $totalAdj,
            'final_exchange_value'    => $finalVal,

            // System Information (68-72)
            'valuation_status'        => 'Verified Online Quote',
            'submission_source'       => 'In-Popup Buyback Questionnaire',
            'page_url'                => $_SERVER['HTTP_REFERER'] ?? 'https://cashsecond.com/',
            'user_agent'              => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
            'lead_timestamp'          => date('Y-m-d H:i:s', $now)
        ];
    }

    /**
     * Send Row Data to Google Sheets via Webhook
     */
    public static function appendValuationRow(array $leadData): array
    {
        $config = require __DIR__ . '/../config/google_sheets.php';
        $rowData = self::buildSheetRowData($leadData);

        // Always save structured row to local audit log first
        $logFile = $config['log_path'];
        $logsDir = dirname($logFile);
        if (!is_dir($logsDir)) {
            @mkdir($logsDir, 0755, true);
        }

        $logEntry = json_encode($rowData, JSON_UNESCAPED_UNICODE) . "\n";
        @file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);

        $webhookUrl = $config['webhook_url'] ?? '';

        // If webhook URL is mock/placeholder or network fails, queue safely
        $isMockUrl = (empty($webhookUrl) || strpos($webhookUrl, 'AKfycbz_CashSecond_Valuations_Webhook') !== false);

        if ($isMockUrl) {
            // Log to pending queue for batch syncing once production URL is supplied
            $queueFile = $config['queue_path'];
            @file_put_contents($queueFile, $logEntry, FILE_APPEND | LOCK_EX);

            return [
                'success' => true,
                'lead_id' => $rowData['lead_id'],
                'mode'    => 'queued_local',
                'message' => 'Valuation saved and queued for Google Sheets.'
            ];
        }

        // Post to active Google Apps Script Webhook
        $postPayload = json_encode([
            'action'    => 'add_valuation_row',
            'token'     => $config['secret_token'] ?? '',
            'sheetName' => $config['sheets']['main'] ?? 'Phone Valuations',
            'row'       => $rowData
        ]);

        $ch = curl_init($webhookUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $postPayload,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => $config['timeout'] ?? 8,
            CURLOPT_SSL_VERIFYPEER => true
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError= curl_error($ch);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            return [
                'success' => true,
                'lead_id' => $rowData['lead_id'],
                'mode'    => 'sheets_live',
                'response'=> $response
            ];
        }

        // On network error, backup to queue file so no data is ever lost
        $queueFile = $config['queue_path'];
        @file_put_contents($queueFile, $logEntry, FILE_APPEND | LOCK_EX);

        return [
            'success' => true, // Still true for customer since backup is securely preserved
            'lead_id' => $rowData['lead_id'],
            'mode'    => 'queued_fallback',
            'error'   => $curlError ?: "HTTP $httpCode"
        ];
    }
}
