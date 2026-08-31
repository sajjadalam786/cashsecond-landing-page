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
        $date = date('d/m/Y', $now);
        $time = date('h:i:s A', $now);

        // Generate Structured Lead ID: EXG-YYYYMMDD-XXXX
        $leadId = $leadData['lead_id'] ?? ('EXG-' . date('Ymd', $now) . '-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 4)));

        $customer = $leadData['customer'] ?? [];
        $device   = $leadData['device'] ?? [];
        $answers  = $leadData['answers'] ?? [];
        $adjustments = $leadData['adjustments'] ?? [];

        $modelName = $device['model'] ?? 'Apple iPhone 13';
        $variant   = $device['variant'] ?? '128 GB';
        $battery   = $device['battery'] ?? ($answers['battery_health'] ?? 'Above 80% (Healthy)');
        
        $formatInr = function($val) {
            $cleaned = preg_replace('/[^0-9]/', '', (string)$val);
            if (!empty($cleaned) && is_numeric($cleaned)) {
                return '₹' . number_format((float)$cleaned);
            }
            return (string)$val;
        };

        $baseVal   = $formatInr($device['base_val'] ?? '23220');
        $finalVal  = $formatInr($device['estimated_val'] ?? '23220');

        $ram = self::$ramMap[$modelName] ?? '6 GB';

        // Helper to normalize Yes / No from bool, string or CSV keys
        $getAns = function ($key, $default = 'YES') use ($answers) {
            if (!isset($answers[$key])) return $default;
            $val = $answers[$key];
            if (is_bool($val)) return $val ? 'YES' : 'NO';
            $val = trim((string)$val);
            if (strncasecmp($val, 'Yes', 3) === 0) return 'YES';
            if (strncasecmp($val, 'No', 2) === 0) return 'NO';
            return strtoupper($val);
        };

        // 1. Functional & Physical Answers (Supports both legacy and new CSV keys)
        $ansPower       = (!empty($answers['power_on']) && $answers['power_on'] === 'No') ? 'NO' : 'YES';
        $ansDispWork    = (!empty($answers['no_display']) || (isset($answers['display_working']) && $answers['display_working'] === 'No')) ? 'NO' : 'YES';
        $ansTouch       = (!empty($answers['touch_not_working']) || (isset($answers['touch_screen']) && $answers['touch_screen'] === 'No')) ? 'NO' : 'YES';
        $ansDispFlaws   = (!empty($answers['lines_on_display']) || !empty($answers['dots_on_display']) || !empty($answers['flickering']) || !empty($answers['color_fade']) || !empty($answers['loose_screen']) || (isset($answers['display_flaws']) && $answers['display_flaws'] === 'Yes')) ? 'YES' : 'NO';
        $ansScreenCrack = (!empty($answers['glass_cracked']) || (isset($answers['screen_cracked']) && $answers['screen_cracked'] === 'Yes')) ? 'YES' : 'NO';
        
        // Screen Scratches
        if (!empty($answers['multiple_scratches_screen']) || !empty($answers['scratch_screen_3_4'])) {
            $ansScreenScr = 'YES (Heavy Scratches)';
        } elseif (!empty($answers['scratch_screen_1_2'])) {
            $ansScreenScr = 'YES (1-2 Minor Scratches)';
        } else {
            $ansScreenScr = $getAns('screen_scratches', 'NO');
        }

        // Body Dents / Scratches / Bent
        $hasDents       = (!empty($answers['dents_1_or_2']) || !empty($answers['multiple_dents']) || !empty($answers['multiple_scratches_body']) || (isset($answers['body_dents']) && $answers['body_dents'] === 'No'));
        $ansBodyDents   = $hasDents ? 'NO (Has Dents/Scratches)' : 'YES (Clean Frame)';
        $ansBodyBent    = (!empty($answers['body_curved']) || (isset($answers['body_bent']) && $answers['body_bent'] === 'Yes')) ? 'YES' : 'NO';
        $ansBodyDmg     = (!empty($answers['back_glass_broken']) || (isset($answers['body_visible_damage']) && $answers['body_visible_damage'] === 'Yes')) ? 'YES' : 'NO';
        $ansCamGlass    = (!empty($answers['camera_glass_broken']) || (isset($answers['camera_glass_crack']) && $answers['camera_glass_crack'] === 'Yes')) ? 'YES' : 'NO';
        $ansMissing     = $getAns('missing_parts', 'NO');

        // Functional hardware
        $ansRearCam     = (!empty($answers['back_camera_not_working']) || (isset($answers['rear_camera']) && $answers['rear_camera'] === 'No')) ? 'NO' : 'YES';
        $ansFrontCam    = (!empty($answers['front_camera_not_working']) || (isset($answers['front_camera']) && $answers['front_camera'] === 'No')) ? 'NO' : 'YES';
        $ansFlash       = $getAns('camera_flash', 'YES');
        $ansSpeaker     = (!empty($answers['speaker_not_working']) || (isset($answers['loudspeaker']) && $answers['loudspeaker'] === 'No')) ? 'NO' : 'YES';
        $ansEarRec      = (!empty($answers['audio_ic_problem']) || (isset($answers['earpiece_receiver']) && $answers['earpiece_receiver'] === 'No')) ? 'NO' : 'YES';
        $ansMic         = $getAns('microphone', 'YES');
        $ansPowerBtn    = (!empty($answers['power_button_issue']) || (isset($answers['power_button']) && $answers['power_button'] === 'No')) ? 'NO' : 'YES';
        $ansVolBtn      = (!empty($answers['volume']) || (isset($answers['volume_buttons']) && $answers['volume_buttons'] === 'No')) ? 'NO' : 'YES';
        $ansSilentSw    = $getAns('silent_switch', 'YES');
        $ansChargePort  = (!empty($answers['charging_port_issue']) || (isset($answers['charging_port']) && $answers['charging_port'] === 'No')) ? 'NO' : 'YES';
        $ansChargeWork  = $getAns('charges_normally', 'YES');
        $ansBio         = (!empty($answers['face_id_not_working']) || !empty($answers['finger_print_not_working']) || (isset($answers['biometrics']) && $answers['biometrics'] === 'No')) ? 'NO' : 'YES';
        $ansWifi        = (!empty($answers['wifi_issues']) || (isset($answers['wifi_working']) && $answers['wifi_working'] === 'No')) ? 'NO' : 'YES';
        $ansBT          = (!empty($answers['bluetooth_issue']) || (isset($answers['bluetooth_working']) && $answers['bluetooth_working'] === 'No')) ? 'NO' : 'YES';
        $ansCellular    = (!empty($answers['headphone_jackissue']) || (isset($answers['cellular_sim']) && $answers['cellular_sim'] === 'No')) ? 'YES' : 'YES';
        $ansGPS         = $getAns('gps_location', 'YES');
        $ansLiquid      = $getAns('liquid_damage', 'NO');

        // Battery Health calculation
        if (!empty($answers['battery_less_80'])) {
            $battery = 'Below 80% (Degraded)';
        } elseif (!empty($answers['battery_faulty'])) {
            $battery = 'Faulty / Swollen';
        } elseif (!empty($answers['battery_greater_80'])) {
            $battery = 'Above 80% (Healthy)';
        }

        // 2. Parts, Accessories & Age History
        $ansDispOrig    = $getAns('display_original', 'YES');
        $ansMajorRep    = $getAns('parts_replaced', 'NO');
        $ansReplacedComp= ($ansMajorRep === 'YES') ? ($answers['replaced_component_name'] ?? 'Display / Battery') : 'None';
        
        // Age warranty estimation
        if (!empty($answers['months_0_3'])) {
            $ansWarranty = 'Under 3 Months (Brand New)';
        } elseif (!empty($answers['months_3_6'])) {
            $ansWarranty = '3 to 6 Months';
        } elseif (!empty($answers['months_6_11'])) {
            $ansWarranty = '6 to 11 Months (Under Warranty)';
        } elseif (!empty($answers['months_11_more'])) {
            $ansWarranty = 'Out of Warranty (Over 1 Year)';
        } else {
            $ansWarranty = $answers['warranty_status'] ?? 'Under 11 Months';
        }
        
        // Inclusions & Documents (Handles both valuator missing deductions and questionnaire booleans)
        if (isset($answers['invoice'])) {
            $ansBill = empty($answers['invoice']) ? 'YES' : 'NO';
        } else {
            $ansBill = (isset($answers['bill_invoice']) && strcasecmp((string)$answers['bill_invoice'], 'No') === 0) ? 'NO' : 'YES';
        }

        if (isset($answers['box'])) {
            $ansBox = empty($answers['box']) ? 'YES' : 'NO';
        } else {
            $ansBox = (isset($answers['has_box']) && strcasecmp((string)$answers['has_box'], 'No') === 0) ? 'NO' : 'YES';
        }

        if (isset($answers['charger'])) {
            $ansCable = empty($answers['charger']) ? 'YES' : 'NO';
        } else {
            $ansCable = (isset($answers['has_cable']) && strcasecmp((string)$answers['has_cable'], 'No') === 0) ? 'NO' : 'YES';
        }

        // 3. Failed Tests and Stats Calculation
        $testChecks = [
            'Phone Powers On'                     => ($ansPower === 'YES'),
            'Display Working'                     => ($ansDispWork === 'YES'),
            'Touchscreen'                         => ($ansTouch === 'YES'),
            'Display Lines / Spots / Flickering'  => ($ansDispFlaws === 'NO'),
            'Screen Cracked'                      => ($ansScreenCrack === 'NO'),
            'Screen Scratches'                    => (strpos($ansScreenScr, 'Heavy') === false),
            'Body Condition'                      => (strpos($ansBodyDents, 'Clean') !== false),
            'Phone Bent'                          => ($ansBodyBent === 'NO'),
            'Body Visible Damage'                 => ($ansBodyDmg === 'NO'),
            'Camera Glass Condition'              => ($ansCamGlass === 'NO'),
            'Rear Camera'                         => ($ansRearCam === 'YES'),
            'Front Camera'                        => ($ansFrontCam === 'YES'),
            'Speaker'                             => ($ansSpeaker === 'YES'),
            'Ear Receiver / Audio IC'             => ($ansEarRec === 'YES'),
            'Power Button'                        => ($ansPowerBtn === 'YES'),
            'Volume Buttons'                      => ($ansVolBtn === 'YES'),
            'Charging Port'                       => ($ansChargePort === 'YES'),
            'Biometrics (Face ID / Touch ID)'     => ($ansBio === 'YES'),
            'Wi-Fi'                               => ($ansWifi === 'YES'),
            'Bluetooth'                           => ($ansBT === 'YES'),
            'Battery Health'                      => (strpos($battery, 'Below') === false && strpos($battery, 'Faulty') === false)
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

        // 4. Valuation Adjustments Breakdown (Clean string format to prevent Excel/Sheets formula #ERROR!)
        $adjStorage    = $adjustments['storage'] ?? '₹0';
        $adjBattery    = $adjustments['battery'] ?? ($battery === 'Below 80%' ? '-₹3,600' : ($battery === '80% – 84%' ? '-₹1,800' : ($battery === '85% – 89%' ? '-₹800' : '₹0')));
        $adjDisplay    = $adjustments['display'] ?? ($ansDispWork === 'NO' ? '-₹5,000' : ($ansScreenCrack === 'YES' ? '-₹3,500' : ($ansDispFlaws === 'YES' ? '-₹3,800' : '₹0')));
        $adjBody       = $adjustments['body'] ?? ($ansBodyBent === 'YES' ? '-₹2,800' : ($ansBodyDents === 'NO' ? '-₹1,500' : '₹0'));
        $adjFunctional = $adjustments['functional'] ?? ($ansBio === 'NO' ? '-₹3,000' : ($ansChargePort === 'NO' ? '-₹1,800' : ($ansPower === 'NO' ? '-₹9,000' : '₹0')));
        $adjLiquid     = $adjustments['liquid'] ?? ($ansLiquid === 'YES' ? '-₹4,500' : '₹0');
        $adjParts      = $adjustments['parts'] ?? ($ansDispOrig === 'NO' ? '-₹3,500' : '₹0');
        $adjWarranty   = $adjustments['warranty'] ?? (stripos($ansWarranty, 'YES') !== false ? '₹1,500' : '₹0');
        $adjAccessory  = $adjustments['accessories'] ?? (($ansBox === 'YES' && $ansCable === 'YES') ? '₹900' : ($ansBox === 'YES' ? '₹600' : '₹0'));

        $totalAdj = $adjustments['total_adjustment'] ?? '₹0';

        // Clean out any leading '+' sign from all adjustment strings
        $cleanFormulaPrefix = function ($val) {
            $s = trim((string)$val);
            if (isset($s[0]) && $s[0] === '+') {
                return ltrim($s, '+ ');
            }
            return $s;
        };

        $adjStorage    = $cleanFormulaPrefix($adjStorage);
        $adjBattery    = $cleanFormulaPrefix($adjBattery);
        $adjDisplay    = $cleanFormulaPrefix($adjDisplay);
        $adjBody       = $cleanFormulaPrefix($adjBody);
        $adjFunctional = $cleanFormulaPrefix($adjFunctional);
        $adjLiquid     = $cleanFormulaPrefix($adjLiquid);
        $adjParts      = $cleanFormulaPrefix($adjParts);
        $adjWarranty   = $cleanFormulaPrefix($adjWarranty);
        $adjAccessory  = $cleanFormulaPrefix($adjAccessory);
        $totalAdj      = $cleanFormulaPrefix($totalAdj);

        // Full row mapping matching the Google Sheet definition
        return [
            // Lead Information (1-12)
            'submission_date'         => $date,
            'submission_time'         => $time,
            'lead_id'                 => $leadId,
            'full_name'               => $customer['name'] ?? '',
            'whatsapp_number'         => $customer['phone'] ?? '',
            'email'                   => $customer['email'] ?? '',
            'pickup_address'          => $customer['address'] ?? '',
            'pincode'                 => $customer['pincode'] ?? '',
            'pickup_date'             => $customer['pickup_date'] ?? ($leadData['pickup_date'] ?? ''),
            'pickup_slot'             => $customer['pickup_slot'] ?? ($leadData['pickup_slot'] ?? ''),
            'feedback_rating'         => $customer['feedback_rating'] ?? ($leadData['feedback_rating'] ?? ''),
            'feedback_comment'        => $customer['feedback_comment'] ?? ($leadData['feedback_comment'] ?? ''),

            // Device Information (13-19)
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
        // NOTE: Google Apps Script /exec URLs respond with a 302 redirect.
        // The POST body is consumed by the /exec handler. The 302 points to
        // script.googleusercontent.com which serves the JSON response via GET.
        // We do NOT follow the redirect in the POST call — a 302 means success.
        // Then we separately GET the redirect URL for the JSON response.
        $postPayload = json_encode([
            'action'    => 'add_valuation_row',
            'token'     => $config['secret_token'] ?? '',
            'sheetName' => $config['sheets']['main'] ?? 'Phone Valuations',
            'row'       => $rowData
        ], JSON_UNESCAPED_UNICODE);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $webhookUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $postPayload,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json'
            ],
            CURLOPT_FOLLOWLOCATION => false,   // Don't follow 302
            CURLOPT_TIMEOUT        => 25,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HEADER         => true      // Capture headers for Location
        ]);

        $rawResponse = curl_exec($ch);
        $httpCode    = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError   = curl_error($ch);
        $effectiveUrl= curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        $headerSize  = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);

        $responseBody = substr($rawResponse, $headerSize);
        $responseHeaders = substr($rawResponse, 0, $headerSize);

        // If 302, the POST was accepted by GAS. Follow the Location to get JSON response.
        $response = $responseBody;
        if ($httpCode === 302) {
            if (preg_match('/Location:\s*(.*)/i', $responseHeaders, $m)) {
                $redirectUrl = trim($m[1]);
                $ch2 = curl_init();
                curl_setopt_array($ch2, [
                    CURLOPT_URL            => $redirectUrl,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_TIMEOUT        => 15,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false
                ]);
                $response = curl_exec($ch2);
                $httpCode = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
                curl_close($ch2);
            } else {
                // 302 without Location — treat as success (POST was consumed)
                $httpCode = 200;
                $response = '{"status":"success","note":"302 accepted, no Location header"}';
            }
        }

        $debugEntry = [
            'timestamp'     => date('Y-m-d H:i:s'),
            'lead_id'       => $rowData['lead_id'],
            'webhook_url'   => $webhookUrl,
            'effective_url' => $effectiveUrl,
            'http_code'     => $httpCode,
            'response'      => $response,
            'curl_err'      => $curlError
        ];

        if (!is_dir($logsDir)) {
            @mkdir($logsDir, 0755, true);
        }
        @file_put_contents($logsDir . '/sheets_sync_debug.jsonl', json_encode($debugEntry, JSON_UNESCAPED_UNICODE) . "\n", FILE_APPEND | LOCK_EX);

        // Send readable notification email with text & emojis to store admin
        self::sendLeadNotificationEmail($leadData, $rowData);

        if ($httpCode >= 200 && $httpCode < 400) {
            return [
                'success'       => true,
                'lead_id'       => $rowData['lead_id'],
                'http_code'     => $httpCode,
                'mode'          => 'sheets_live',
                'google_reply'  => $response
            ];
        }

        // On network error, backup to queue file so no data is ever lost
        $queueFile = $config['queue_path'];
        @file_put_contents($queueFile, $logEntry, FILE_APPEND | LOCK_EX);

        return [
            'success'   => false,
            'lead_id'   => $rowData['lead_id'],
            'mode'      => 'queued_fallback',
            'http_code' => $httpCode,
            'error'     => $curlError ?: "HTTP $httpCode",
            'response'  => $response
        ];
    }

    /**
     * Send structured, high-priority email notification with emojis and quick-read condition matrix
     */
    public static function sendLeadNotificationEmail(array $leadData, array $rowData): bool
    {
        $to          = function_exists('get_env_var') ? get_env_var('RECIPIENT_EMAIL', 'wholesalehouse2016@gmail.com, Cashsecondoffice@gmail.com') : 'wholesalehouse2016@gmail.com, Cashsecondoffice@gmail.com';
        $senderEmail = function_exists('get_env_var') ? get_env_var('SENDER_EMAIL', 'no-reply@cashsecond.in') : 'no-reply@cashsecond.in';
        $senderName  = function_exists('get_env_var') ? get_env_var('SENDER_NAME', 'CashSecond Valuation Desk') : 'CashSecond Valuation Desk';

        if (empty($to)) {
            return false;
        }

        $customer = $leadData['customer'] ?? [];
        $device   = $leadData['device'] ?? [];
        $answers  = $leadData['answers'] ?? [];

        // Spam & Header Injection Protection
        $cleanStr = function ($val, $default = '') {
            if ($val === null || $val === '') return $default;
            return trim(strip_tags((string)$val));
        };

        $name     = $cleanStr($customer['name'] ?? ($rowData['full_name'] ?? 'Customer'), 'Customer');
        $phone    = $cleanStr($customer['phone'] ?? ($rowData['whatsapp_number'] ?? ''));
        $email    = $cleanStr($customer['email'] ?? ($rowData['email'] ?? 'Not provided'), 'Not provided');
        $model    = $cleanStr($device['model'] ?? ($rowData['model'] ?? 'Apple iPhone'), 'Apple iPhone');
        $variant  = $cleanStr($device['variant'] ?? ($rowData['storage'] ?? ''), '');
        $baseVal  = $cleanStr($device['base_val'] ?? ($rowData['base_max_value'] ?? ''), '');
        $finalVal = $cleanStr($device['estimated_val'] ?? ($rowData['final_estimated_value'] ?? ''), '');
        $leadId   = $cleanStr($rowData['lead_id'] ?? ('CS-' . date('Ymd-His')));
        $timeStr  = date('d M Y, h:i A');

        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        $waUrl      = "https://wa.me/91{$cleanPhone}?text=" . rawurlencode("Hi {$name}, this is CashSecond regarding your iPhone valuation of {$finalVal} for {$model} ({$variant}). Ref: {$leadId}");

        // Build Visual Emoji Condition Matrix
        $screenCond = ($rowData['display_working'] === 'YES' && $rowData['display_lines_spots'] === 'NO') ? '✅ Display Working (Clear)' : '❌ Display Issues / Lines / Dots';
        $glassCond  = ($rowData['screen_cracked'] === 'NO') ? '✅ Front Glass Intact' : '❌ Front Glass Cracked';
        $scrCond    = $rowData['screen_major_scratches'] ?? 'NO';
        $scratchTxt = (stripos($scrCond, 'Heavy') !== false) ? '❌ Heavy Scratches' : ((stripos($scrCond, '1-2') !== false) ? '⚠️ Minor Scratches' : '✅ Scratch-Free Screen');
        
        $bodyCond   = (strpos($rowData['body_condition'] ?? '', 'Clean') !== false) ? '✅ Clean Metal Frame' : '⚠️ Has Dents / Body Scratches';
        $bentCond   = ($rowData['phone_bent'] === 'NO') ? '✅ Frame Flat & Straight' : '❌ Body Curved / Bent';
        $backGlass  = ($rowData['body_damage'] === 'NO') ? '✅ Back Glass Intact' : '❌ Back Glass Broken';
        
        $batteryTxt = $rowData['battery_health'] ?? 'Above 80%';
        $batteryEmo = (strpos($batteryTxt, 'Above') !== false) ? "🟢 {$batteryTxt}" : ((strpos($batteryTxt, 'Below') !== false) ? "🔴 {$batteryTxt}" : "⚠️ {$batteryTxt}");
        
        $camTxt     = ($rowData['front_camera'] === 'YES' && $rowData['rear_camera'] === 'YES') ? '✅ Front & Rear Cameras Working' : '❌ Camera Faulty';
        $bioTxt     = ($rowData['face_id_touch_id'] === 'YES') ? '✅ Face ID / Biometrics OK' : '❌ Face ID / Sensor Broken';
        $chargeTxt  = ($rowData['charging_port'] === 'YES' && $rowData['charging_working'] === 'YES') ? '✅ Charging Port & Fast Charge OK' : '❌ Charging Port Issue';
        $soundTxt   = ($rowData['speaker'] === 'YES' && $rowData['ear_receiver'] === 'YES') ? '✅ Loudspeaker & Earpiece Clear' : '❌ Speaker / Audio Issue';
        $wifiTxt    = ($rowData['wifi'] === 'YES' && $rowData['bluetooth'] === 'YES') ? '✅ Wi-Fi & Bluetooth OK' : '❌ Wireless Issues';

        $boxTxt     = ($rowData['original_box'] === 'YES') ? '📦 Box: YES' : '📦 Box: NO';
        $chargerTxt = ($rowData['original_cable_adapter'] === 'YES') ? '⚡ Charger: YES' : '⚡ Charger: NO';
        $billTxt    = ($rowData['original_bill'] === 'YES') ? '🧾 Bill: YES' : '🧾 Bill: NO';

        $warrantyTxt= $rowData['warranty_status'] ?? 'Out of Warranty';

        $subject = "New CashSecond iPhone Valuation Lead from {$name} [{$model}]";

        // Plain Text Version with Clean Emoji Formatting
        $plainBody = "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n"
                   . "📱 ONSITE INSPECTION LEAD | CashSecond\n"
                   . "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n"
                   . "👤 CUSTOMER DETAILS:\n"
                   . "• Name:     {$name}\n"
                   . "• Phone:    {$phone} (Click to Call: tel:{$cleanPhone})\n"
                   . "• WhatsApp: {$waUrl}\n"
                   . "• Email:    {$email}\n"
                   . "• Lead ID:  {$leadId}\n"
                   . "• Time:     {$timeStr}\n\n"
                   . "💰 VALUATION SUMMARY:\n"
                   . "• Device:          {$model} ({$variant})\n"
                   . "• Final Valuation: {$finalVal}\n"
                   . "• Base Price:      {$baseVal}\n"
                   . "• Status:          Verified Online Quote\n\n"
                   . "🖥️ DISPLAY & SCREEN INSPECTION:\n"
                   . "• Screen Display:       {$screenCond}\n"
                   . "• Touchscreen:          " . ($rowData['touchscreen_working'] === 'YES' ? '✅ Responsive' : '❌ Touchscreen Faulty') . "\n"
                   . "• Front Screen Glass:   {$glassCond}\n"
                   . "• Screen Scratches:     {$scratchTxt}\n"
                   . "• Lines / Spots / Flaws:" . ($rowData['display_lines_spots'] === 'NO' ? '✅ Clean Display' : '❌ Lines/Spots Detected') . "\n"
                   . "• Original Display:     " . ($rowData['original_display'] === 'YES' ? '✅ Original Apple Screen' : '⚠️ Replaced Screen') . "\n\n"
                   . "📱 BODY & FRAME INSPECTION:\n"
                   . "• Frame / Body Marks:   {$bodyCond}\n"
                   . "• Chassis / Frame Bent: {$bentCond}\n"
                   . "• Back Glass:           {$backGlass}\n"
                   . "• Camera Glass:         " . ($rowData['camera_glass_condition'] === 'NO' ? '✅ Intact' : '❌ Camera Glass Cracked') . "\n"
                   . "• Missing Parts/Screws: " . ($rowData['missing_parts'] === 'NO' ? '✅ All Intact' : '❌ Missing Parts') . "\n\n"
                   . "⚙️ HARDWARE & FUNCTIONAL TESTS:\n"
                   . "• Front Camera:         " . ($rowData['front_camera'] === 'YES' ? '✅ Working' : '❌ Faulty') . "\n"
                   . "• Rear Main Camera:     " . ($rowData['rear_camera'] === 'YES' ? '✅ Working' : '❌ Faulty') . "\n"
                   . "• Camera Flash:         " . ($rowData['camera_flash'] === 'YES' ? '✅ Working' : '❌ Faulty') . "\n"
                   . "• Face ID / Biometrics: {$bioTxt}\n"
                   . "• Charging Port:        {$chargeTxt}\n"
                   . "• Loudspeaker:          " . ($rowData['speaker'] === 'YES' ? '✅ Working' : '❌ Speaker Issue') . "\n"
                   . "• Earpiece / Receiver:  " . ($rowData['ear_receiver'] === 'YES' ? '✅ Clear Call Audio' : '❌ Receiver Issue') . "\n"
                   . "• Microphone:           " . ($rowData['microphone'] === 'YES' ? '✅ Mic Working' : '❌ Mic Faulty') . "\n"
                   . "• Physical Buttons:     " . ($rowData['power_button'] === 'YES' && $rowData['volume_buttons'] === 'YES' ? '✅ Power & Volume OK' : '❌ Button Issue') . "\n"
                   . "• Silent Switch:        " . ($rowData['silent_switch'] === 'YES' ? '✅ Working' : '❌ Faulty') . "\n"
                   . "• Wi-Fi & Bluetooth:    {$wifiTxt}\n"
                   . "• Cellular / Network:   " . ($rowData['mobile_network_sim'] === 'YES' ? '✅ Network Signal OK' : '❌ Network Issue') . "\n"
                   . "• GPS Location:         " . ($rowData['gps'] === 'YES' ? '✅ GPS Working' : '❌ GPS Faulty') . "\n\n"
                   . "🔋 BATTERY, HISTORY & INCLUSIONS:\n"
                   . "• Battery Health:       {$batteryEmo}\n"
                   . "• Liquid / Water Damage:" . ($rowData['liquid_damage'] === 'NO' ? '✅ Safe (No Water Damage)' : '❌ Liquid Damaged') . "\n"
                   . "• Major Repairs:        " . ($rowData['major_component_replaced'] === 'NO' ? '✅ No Replacements' : '⚠️ Replaced: ' . $rowData['replaced_component']) . "\n"
                   . "• Warranty Status:      {$warrantyTxt}\n"
                   . "• 📦 Original Box:      {$boxTxt}\n"
                   . "• ⚡ Original Charger:  {$chargerTxt}\n"
                   . "• 🧾 Purchase Bill:     {$billTxt}\n\n"
                   . "📋 INSPECTOR SUMMARY:\n"
                   . "• Reported Issues:      " . ($rowData['failed_test_names'] !== 'None (All Passed)' ? '⚠️ ' . $rowData['failed_test_names'] : '✅ Clean Device (All Tests Passed)') . "\n"
                   . "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

        // Visual Styling Helpers for Email Matrix
        $tagPass = function($text) {
            return "<strong style='color:#1E8E3E;background:#E8F8EE;padding:2px 8px;border-radius:6px;font-size:12.5px;display:inline-block;'>{$text}</strong>";
        };
        $tagFail = function($text) {
            return "<strong style='color:#D70015;background:#FFF0F0;padding:2px 8px;border-radius:6px;font-size:12.5px;display:inline-block;'>{$text}</strong>";
        };
        $tagWarn = function($text) {
            return "<strong style='color:#E37400;background:#FFF6E6;padding:2px 8px;border-radius:6px;font-size:12.5px;display:inline-block;'>{$text}</strong>";
        };

        // Helper for Responsive Inspection Row
        $makeRow = function($label, $tag) {
            return "<tr>"
                 . "<td style='padding:7px 8px 7px 0;color:#555558;font-size:13px;line-height:1.4;border-bottom:1px solid #F2F2F5;vertical-align:middle;'>{$label}</td>"
                 . "<td align='right' style='padding:7px 0 7px 8px;border-bottom:1px solid #F2F2F5;vertical-align:middle;text-align:right;white-space:nowrap;'>{$tag}</td>"
                 . "</tr>";
        };

        // Fully Responsive HTML Email Template
        $htmlBody = "
<!DOCTYPE html>
<html lang='en'>
<head>
<meta charset='UTF-8'>
<meta name='viewport' content='width=device-width, initial-scale=1.0'>
<title>CashSecond Lead: {$model}</title>
<style type='text/css'>
  /* Email Client Resets */
  body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
  table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
  img { -ms-interpolation-mode: bicubic; border: 0; outline: none; text-decoration: none; }
  body { margin: 0 !important; padding: 0 !important; width: 100% !important; background-color: #F2F2F7; }

  /* Mobile Responsive Breakpoints */
  @media only screen and (max-width: 620px) {
    .main-table { width: 100% !important; max-width: 100% !important; }
    .content-box { padding: 16px 12px !important; border-radius: 8px !important; }
    .btn-wrap { display: block !important; width: 100% !important; margin-bottom: 8px !important; }
    .btn-spacer { display: none !important; }
    .device-title { font-size: 19px !important; }
    .price-title { font-size: 23px !important; }
    .section-card { padding: 12px 10px !important; margin-bottom: 12px !important; }
  }
</style>
</head>
<body style='margin:0;padding:0;background-color:#F2F2F7;font-family:-apple-system,BlinkMacSystemFont,\"Segoe UI\",Roboto,Helvetica,Arial,sans-serif;'>

<table border='0' cellpadding='0' cellspacing='0' width='100%' style='background-color:#F2F2F7;padding:16px 8px;'>
  <tr>
    <td align='center'>
      
      <!-- Main Container Table -->
      <table class='main-table' border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width:600px;width:100%;background-color:#FFFFFF;border-radius:14px;border:1px solid #E5E5EA;box-shadow:0 3px 12px rgba(0,0,0,0.05);overflow:hidden;'>
        <tr>
          <td class='content-box' style='padding:22px 20px;'>

            <!-- Header Badge & Model Header -->
            <div style='border-bottom:2px solid #0071E3;padding-bottom:14px;margin-bottom:16px;'>
              <table border='0' cellpadding='0' cellspacing='0' width='100%' style='margin-bottom:8px;'>
                <tr>
                  <td align='left'>
                    <span style='background:#0071E3;color:#FFFFFF;font-size:11px;font-weight:bold;padding:4px 9px;border-radius:20px;text-transform:uppercase;letter-spacing:0.04em;'>Onsite Valuation Lead</span>
                  </td>
                  <td align='right'>
                    <span style='font-size:11.5px;color:#86868B;font-weight:600;'>Ref: {$leadId}</span>
                  </td>
                </tr>
              </table>
              <h2 class='device-title' style='margin:0;font-size:21px;color:#111111;line-height:1.3;'>{$model} <span style='color:#6E6E73;font-size:15px;font-weight:normal;'>({$variant})</span></h2>
              <div class='price-title' style='font-size:25px;font-weight:800;color:#1E8E3E;margin-top:6px;'>
                {$finalVal} <span style='font-size:13.5px;font-weight:500;color:#86868B;margin-left:4px;'>Base: {$baseVal}</span>
              </div>
            </div>

            <!-- Customer Contact Information Card -->
            <div class='section-card' style='background:#F5F5F7;border-radius:10px;padding:12px 14px;margin-bottom:16px;'>
              <div style='font-size:12px;font-weight:700;color:#0071E3;text-transform:uppercase;letter-spacing:0.04em;margin-bottom:8px;'>👤 Customer Contact Details</div>
              <table border='0' cellpadding='0' cellspacing='0' width='100%' style='font-size:13px;line-height:1.7;'>
                <tr>
                  <td style='color:#6E6E73;width:34%;padding:2px 0;'>Full Name:</td>
                  <td style='color:#111111;font-weight:700;padding:2px 0;'>{$name}</td>
                </tr>
                <tr>
                  <td style='color:#6E6E73;padding:2px 0;'>Phone:</td>
                  <td style='padding:2px 0;'><a href='tel:{$cleanPhone}' style='color:#0071E3;font-weight:700;text-decoration:none;'>+91 {$cleanPhone}</a></td>
                </tr>
                <tr>
                  <td style='color:#6E6E73;padding:2px 0;'>Email:</td>
                  <td style='color:#111111;padding:2px 0;'>{$email}</td>
                </tr>
                <tr>
                  <td style='color:#6E6E73;padding:2px 0;'>Date / Time:</td>
                  <td style='color:#111111;padding:2px 0;'>{$timeStr}</td>
                </tr>
              </table>
            </div>

            <!-- Responsive Call & WhatsApp CTA Buttons -->
            <table border='0' cellpadding='0' cellspacing='0' width='100%' style='margin-bottom:18px;'>
              <tr>
                <td class='btn-wrap' width='49%' align='center' style='vertical-align:top;'>
                  <a href='{$waUrl}' target='_blank' style='display:block;width:100%;box-sizing:border-box;background:#25D366;color:#FFFFFF;text-align:center;padding:11px 8px;border-radius:8px;font-weight:bold;text-decoration:none;font-size:13.5px;'>💬 WhatsApp</a>
                </td>
                <td class='btn-spacer' width='2%'></td>
                <td class='btn-wrap' width='49%' align='center' style='vertical-align:top;'>
                  <a href='tel:{$cleanPhone}' style='display:block;width:100%;box-sizing:border-box;background:#0071E3;color:#FFFFFF;text-align:center;padding:11px 8px;border-radius:8px;font-weight:bold;text-decoration:none;font-size:13.5px;'>📞 Call Client</a>
                </td>
              </tr>
            </table>

            <!-- Section 1: Screen & Display Check -->
            <div class='section-card' style='border:1px solid #E5E5EA;border-radius:10px;padding:14px;margin-bottom:14px;'>
              <div style='font-size:12.5px;font-weight:700;color:#111111;text-transform:uppercase;letter-spacing:0.04em;margin-bottom:8px;'>🖥️ Screen &amp; Display Check</div>
              <table border='0' cellpadding='0' cellspacing='0' width='100%'>
                " . $makeRow('Display Power / Blackout', $dispWorkTag) . "
                " . $makeRow('Touchscreen Response', $touchTag) . "
                " . $makeRow('Front Screen Glass', $frontGlassTag) . "
                " . $makeRow('Screen Scratches', $scratchTag) . "
                " . $makeRow('Lines / Dots / Ink Spots', $linesTag) . "
                " . $makeRow('Screen Flickering', $flickerTag) . "
                " . $makeRow('Color Fade / Burn-in', $colorFadeTag) . "
                " . $makeRow('Screen Loose / Lifted', $looseScreenTag) . "
                " . $makeRow('Display Originality', $dispOrigTag) . "
              </table>
            </div>

            <!-- Section 2: Body & Frame Condition -->
            <div class='section-card' style='border:1px solid #E5E5EA;border-radius:10px;padding:14px;margin-bottom:14px;'>
              <div style='font-size:12.5px;font-weight:700;color:#111111;text-transform:uppercase;letter-spacing:0.04em;margin-bottom:8px;'>📱 Body &amp; Frame Condition</div>
              <table border='0' cellpadding='0' cellspacing='0' width='100%'>
                " . $makeRow('Frame Scratches / Marks', $bodyScratchTag) . "
                " . $makeRow('Dents on Body', $dentsTag) . "
                " . $makeRow('Chassis / Bent Frame', $bentTag) . "
                " . $makeRow('Back Glass Condition', $backGlassTag) . "
                " . $makeRow('Camera Lens Glass', $camGlassTag) . "
                " . $makeRow('Parts / Screws', $partsTag) . "
              </table>
            </div>

            <!-- Section 3: Hardware & Component Tests -->
            <div class='section-card' style='border:1px solid #E5E5EA;border-radius:10px;padding:14px;margin-bottom:14px;'>
              <div style='font-size:12.5px;font-weight:700;color:#111111;text-transform:uppercase;letter-spacing:0.04em;margin-bottom:8px;'>⚙️ Hardware &amp; Component Tests</div>
              <table border='0' cellpadding='0' cellspacing='0' width='100%'>
                " . $makeRow('Front Selfie Camera', $fCamTag) . "
                " . $makeRow('Rear Main Camera', $rCamTag) . "
                " . $makeRow('Camera Flash', $flashTag) . "
                " . $makeRow('Face ID / Fingerprint', $bioTag) . "
                " . $makeRow('Charging Port', $chargeTag) . "
                " . $makeRow('Loudspeaker', $speakerTag) . "
                " . $makeRow('Earpiece / Receiver', $receiverTag) . "
                " . $makeRow('Microphone &amp; Audio IC', $micTag) . "
                " . $makeRow('Power Button', $powerBtnTag) . "
                " . $makeRow('Volume Buttons', $volBtnTag) . "
                " . $makeRow('Silent / Mute Switch', $silentTag) . "
                " . $makeRow('Vibrator Engine', $vibratorTag) . "
                " . $makeRow('Proximity Sensors', $sensorTag) . "
                " . $makeRow('Wi-Fi &amp; Bluetooth', $wirelessTag) . "
                " . $makeRow('Cellular SIM / Network', $networkTag) . "
                " . $makeRow('GPS Location', $gpsTag) . "
                " . $makeRow('Headphone Jack', $headphoneTag) . "
              </table>
            </div>

            <!-- Section 4: Battery, History & Inclusions -->
            <div class='section-card' style='border:1px solid #E5E5EA;border-radius:10px;padding:14px;margin-bottom:14px;'>
              <div style='font-size:12.5px;font-weight:700;color:#111111;text-transform:uppercase;letter-spacing:0.04em;margin-bottom:8px;'>🔋 Battery, History &amp; Inclusions</div>
              <table border='0' cellpadding='0' cellspacing='0' width='100%'>
                " . $makeRow('Battery Health', $batteryTag) . "
                " . $makeRow('Liquid / Water Damage', $liquidTag) . "
                " . $makeRow('Component Repairs', $repairsTag) . "
                " . $makeRow('Purchase Timeline', '<strong>' . htmlspecialchars($warrantyTxt, ENT_QUOTES, 'UTF-8') . '</strong>') . "
                " . $makeRow('📦 Original Box', $boxTag) . "
                " . $makeRow('⚡ Original Charger / Cable', $chargerTag) . "
                " . $makeRow('🧾 Purchase Invoice / Bill', $billTag) . "
              </table>
            </div>

            <!-- Reported Defects Summary Alert Banner -->
            <div style='background:#FFF5F5;border:1px solid #FFD2D2;border-radius:10px;padding:12px 14px;'>
              <div style='font-size:12.5px;font-weight:700;color:#111111;margin-bottom:4px;'>📋 Reported Faults &amp; Deductions:</div>
              " . ($rowData['failed_test_names'] !== 'None (All Passed)' ? "<div style='color:#D70015;font-weight:700;font-size:13px;line-height:1.4;'>⚠️ {$rowData['failed_test_names']}</div>" : "<div style='color:#1E8E3E;font-weight:700;font-size:13px;line-height:1.4;'>✅ Clean Device (No Faults Reported)</div>") . "
            </div>

          </td>
        </tr>
      </table>
      <!-- End Container Table -->

    </td>
  </tr>
</table>

</body>
</html>";

        // Standard Email Headers (Spam & Header Injection Protected)
        $replyTo = (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) ? $email : $senderEmail;
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: {$senderName} <{$senderEmail}>\r\n";
        $headers .= "Reply-To: {$replyTo}\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

        // Dispatch email
        $mailSent = @mail($to, $subject, $htmlBody, $headers);

        // Also attempt SMTP if configured in config/smtp.php
        $smtpClassFile = __DIR__ . '/SmtpMailer.php';
        if (file_exists($smtpClassFile)) {
            require_once $smtpClassFile;
            $smtpConfig = file_exists(__DIR__ . '/../config/smtp.php') ? require __DIR__ . '/../config/smtp.php' : [];
            if (!empty($smtpConfig['enabled']) && !empty($smtpConfig['password'])) {
                $smtpRes = SmtpMailer::send($to, $subject, $htmlBody, $plainBody);
                if (!empty($smtpRes['success'])) {
                    $mailSent = true;
                }
            }
        }

        // Save to email log for audit & verification
        $logsDir = __DIR__ . '/../logs';
        if (!is_dir($logsDir)) {
            @mkdir($logsDir, 0755, true);
        }
        @file_put_contents($logsDir . '/email_notifications.jsonl', json_encode([
            'timestamp' => date('Y-m-d H:i:s'),
            'to'        => $to,
            'subject'   => $subject,
            'lead_id'   => $leadId,
            'sent'      => $mailSent
        ], JSON_UNESCAPED_UNICODE) . "\n", FILE_APPEND | LOCK_EX);

        return $mailSent;
    }

    /**
     * Update Feedback & Pickup Scheduling in Google Sheets
     */
    public static function updateFeedbackRow(string $refId, string $rating, string $comment, string $pickupDate, string $pickupSlot, string $pickupAddress = '', string $pincode = ''): array
    {
        date_default_timezone_set('Asia/Kolkata');
        $config = require __DIR__ . '/../config/google_sheets.php';
        $webhookUrl = $config['webhook_url'] ?? '';

        if (empty($webhookUrl) || strpos($webhookUrl, 'AKfycbz_CashSecond_Valuations_Webhook') !== false) {
            return ['success' => true, 'mode' => 'queued_local'];
        }

        $postPayload = json_encode([
            'action'          => 'update_feedback',
            'token'           => $config['secret_token'] ?? '',
            'sheetName'       => $config['sheets']['main'] ?? 'Phone Valuations',
            'lead_id'         => $refId,
            'feedback_rating' => $rating,
            'feedback_comment'=> $comment,
            'pickup_date'     => $pickupDate,
            'pickup_slot'     => $pickupSlot,
            'pickup_address'  => $pickupAddress,
            'pincode'         => $pincode,
            'update_timestamp'=> date('d/m/Y h:i:s A')
        ], JSON_UNESCAPED_UNICODE);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $webhookUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $postPayload,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json'
            ],
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 5,
            CURLOPT_POSTREDIR      => CURL_REDIR_POST_ALL,
            CURLOPT_TIMEOUT        => 20,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError= curl_error($ch);
        curl_close($ch);

        return [
            'success'   => ($httpCode >= 200 && $httpCode < 400),
            'lead_id'   => $refId,
            'http_code' => $httpCode,
            'response'  => $response
        ];
    }
}
