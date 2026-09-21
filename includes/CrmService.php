<?php
/**
 * CashSecond - 365 Lead Management CRM Integration Service
 * Documentation Reference:
 * - API URL: https://www.api.365crm.io/wpaddwebsiteleads
 * - API Method: POST
 * - Headers: Authorization: <API_KEY>, Content-Type: application/json
 * - Body: customerName, customerEmail, customerMobile, companyName, customerComment, label
 * - customerName and customerMobile are required.
 * - Country code included in customerMobile (e.g. 91XXXXXXXXXX).
 * - All other fields consolidated in customerComment.
 */

class CrmService
{
    /**
     * Format mobile number with country code (defaults to 91 for India)
     */
    public static function formatMobileNumber(?string $phone): string
    {
        if (empty($phone)) {
            return '';
        }

        $clean = preg_replace('/[^0-9]/', '', $phone);
        if (empty($clean)) {
            return '';
        }

        // If 11 digits starting with 0 (e.g. 09876543210), replace leading 0 with 91
        if (strlen($clean) === 11 && str_starts_with($clean, '0')) {
            $clean = '91' . substr($clean, 1);
        }
        // If standard 10 digit Indian number, prefix with 91
        elseif (strlen($clean) === 10) {
            $clean = '91' . $clean;
        }

        return $clean;
    }

    /**
     * Retrieve configuration array
     */
    public static function getConfig(): array
    {
        static $cfg = null;
        if ($cfg === null) {
            $configFile = dirname(__DIR__) . '/config/config.php';
            if (file_exists($configFile)) {
                $cfg = require $configFile;
            } else {
                $cfg = [];
            }
        }
        return $cfg;
    }

    /**
     * Dispatch raw payload to 365 CRM API
     */
    public static function sendRaw(array $payload): array
    {
        $config = self::getConfig();
        $apiUrl = trim($config['integrations']['crm_api_url'] ?? 'https://www.api.365crm.io/wpaddwebsiteleads');
        $apiKey = trim($config['integrations']['crm_api_key'] ?? 'c2hhaWtoc2FqamFkNzg2QGdtYWlsLmNvbS8yMTQ2Ny8yMTQ2Ny8yMTA5MjAyNjA4NTUxMw==');

        if (empty($apiUrl) || empty($apiKey)) {
            return [
                'success'   => false,
                'message'   => 'CRM API URL or API Key is missing.',
                'http_code' => 0
            ];
        }

        // Validate required fields per documentation
        if (empty($payload['customerName']) || empty($payload['customerMobile'])) {
            return [
                'success'   => false,
                'message'   => 'customerName and customerMobile are required by 365 CRM.',
                'http_code' => 0
            ];
        }

        $jsonPayload = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $responseBody = '';
        $httpCode = 0;
        $curlError = '';

        if (function_exists('curl_init')) {
            $ch = curl_init($apiUrl);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $jsonPayload,
                CURLOPT_HTTPHEADER     => [
                    'Authorization: ' . $apiKey,
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($jsonPayload)
                ],
                CURLOPT_TIMEOUT        => 6,
                CURLOPT_CONNECTTIMEOUT => 4,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_FOLLOWLOCATION => true
            ]);

            $responseBody = curl_exec($ch);
            $httpCode     = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (curl_errno($ch)) {
                $curlError = curl_error($ch);
            }
            curl_close($ch);
        } else {
            // Fallback via stream_context
            $opts = [
                'http' => [
                    'method'  => 'POST',
                    'header'  => "Authorization: {$apiKey}\r\nContent-Type: application/json\r\nContent-Length: " . strlen($jsonPayload) . "\r\n",
                    'content' => $jsonPayload,
                    'timeout' => 6
                ]
            ];
            $context = stream_context_create($opts);
            $responseBody = @file_get_contents($apiUrl, false, $context);
            $httpCode = ($responseBody !== false) ? 200 : 0;
        }

        $success = ($httpCode >= 200 && $httpCode < 300);

        // Local CRM Audit Logging
        $logDir = dirname(__DIR__) . '/logs';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }

        $logEntry = [
            'timestamp'     => date('Y-m-d H:i:s'),
            'endpoint'      => $apiUrl,
            'payload'       => $payload,
            'http_code'     => $httpCode,
            'success'       => $success,
            'response'      => $responseBody,
            'curl_error'    => $curlError
        ];

        @file_put_contents(
            $logDir . '/crm_leads.jsonl',
            json_encode($logEntry, JSON_UNESCAPED_UNICODE) . "\n",
            FILE_APPEND | LOCK_EX
        );

        return [
            'success'   => $success,
            'http_code' => $httpCode,
            'response'  => $responseBody,
            'error'     => $curlError
        ];
    }

    /**
     * Send Valuation Lead (from questionnaire modal or valuator component)
     */
    public static function sendValuationLead(array $leadEntry): array
    {
        $config = self::getConfig();
        $companyName = $config['integrations']['crm_company_name'] ?? 'CashSecond';

        $customer = $leadEntry['customer'] ?? [];
        $device   = $leadEntry['device'] ?? [];
        $answers  = $leadEntry['answers'] ?? [];

        $name   = trim($customer['name'] ?? ($leadEntry['name'] ?? 'Valued Customer'));
        $email  = trim($customer['email'] ?? ($leadEntry['email'] ?? ''));
        $mobile = self::formatMobileNumber($customer['phone'] ?? ($leadEntry['phone'] ?? ''));

        $model   = $device['model'] ?? ($leadEntry['model'] ?? 'Apple iPhone');
        $variant = $device['variant'] ?? ($leadEntry['variant'] ?? '');
        $val     = $device['estimated_val'] ?? ($leadEntry['estimated_value'] ?? '');
        $refId   = $leadEntry['ref_id'] ?? ($leadEntry['lead_id'] ?? 'EXG-' . date('Ymd'));

        // Build rich customerComment combining all questionnaire and device attributes
        $commentLines = [];
        $commentLines[] = "=== CASHSECOND VALUATION LEAD ===";
        $commentLines[] = "Lead Ref: " . $refId;
        $commentLines[] = "Device: " . trim($model . ($variant ? " ({$variant})" : ""));
        $commentLines[] = "Estimated Resale Value: " . $val;

        if (!empty($answers)) {
            $commentLines[] = "\n--- Device Condition Audit ---";
            foreach ($answers as $k => $v) {
                if (is_array($v)) {
                    $v = implode(', ', $v);
                }
                $label = ucwords(str_replace('_', ' ', (string)$k));
                $commentLines[] = "• {$label}: {$v}";
            }
        }

        if (!empty($customer['address']) || !empty($customer['pincode'])) {
            $commentLines[] = "\n--- Doorstep Location ---";
            if (!empty($customer['address'])) $commentLines[] = "Address: " . $customer['address'];
            if (!empty($customer['pincode'])) $commentLines[] = "Pincode: " . $customer['pincode'];
        }

        if (!empty($customer['pickup_date']) || !empty($customer['pickup_slot'])) {
            $commentLines[] = "Pickup Preferred: " . trim(($customer['pickup_date'] ?? '') . ' ' . ($customer['pickup_slot'] ?? ''));
        }

        $commentLines[] = "\nIP: " . ($leadEntry['ip'] ?? ($_SERVER['REMOTE_ADDR'] ?? 'Unknown'));
        $commentLines[] = "Submitted At: " . ($leadEntry['timestamp'] ?? date('d-m-Y H:i:s'));

        $customerComment = implode("\n", $commentLines);

        $payload = [
            'customerName'    => $name,
            'customerEmail'   => $email,
            'customerMobile'  => $mobile,
            'companyName'     => $companyName,
            'customerComment' => $customerComment,
            'label'           => $config['integrations']['crm_lead_label'] ?? 'selliphone'
        ];

        return self::sendRaw($payload);
    }

    /**
     * Send Doorstep Pickup Confirmation (when scheduled on thank you page)
     */
    public static function sendDoorstepPickup(string $refId, array $pickupData): array
    {
        $config = self::getConfig();
        $companyName = $config['integrations']['crm_company_name'] ?? 'CashSecond';

        $name   = trim($pickupData['customer_name'] ?? ($pickupData['name'] ?? 'Customer'));
        $email  = trim($pickupData['customer_email'] ?? ($pickupData['email'] ?? ''));
        $mobile = self::formatMobileNumber($pickupData['customer_phone'] ?? ($pickupData['phone'] ?? ''));

        $commentLines = [];
        $commentLines[] = "=== DOORSTEP PICKUP SCHEDULED (100% CONFIRMED) ===";
        $commentLines[] = "Booking Ref: " . $refId;
        if (!empty($pickupData['device_model']))   $commentLines[] = "Device: " . $pickupData['device_model'];
        if (!empty($pickupData['estimated_value']))$commentLines[] = "Locked Payout: " . $pickupData['estimated_value'];
        $commentLines[] = "Pickup Date: " . ($pickupData['pickup_date'] ?? 'Today');
        $commentLines[] = "Pickup Time Slot: " . ($pickupData['pickup_slot'] ?? 'Express (Within 4-6 Hours)');
        $commentLines[] = "Doorstep Address: " . ($pickupData['pickup_address'] ?? 'Not Specified');
        $commentLines[] = "Mumbai Pincode: " . ($pickupData['pincode'] ?? 'Not Specified');
        if (!empty($pickupData['feedback_rating'])) $commentLines[] = "Offer Rating: " . $pickupData['feedback_rating'];
        if (!empty($pickupData['feedback_comment']))$commentLines[] = "Customer Instructions: " . $pickupData['feedback_comment'];
        $commentLines[] = "Scheduled At: " . date('d-m-Y H:i:s');

        $customerComment = implode("\n", $commentLines);

        $payload = [
            'customerName'    => $name,
            'customerEmail'   => $email,
            'customerMobile'  => $mobile,
            'companyName'     => $companyName,
            'customerComment' => $customerComment,
            'label'           => $config['integrations']['crm_lead_label'] ?? 'selliphone'
        ];

        return self::sendRaw($payload);
    }

    /**
     * Send Lead Enquiry (from main homepage lead enquiry form)
     */
    public static function sendEnquiryLead(array $leadData): array
    {
        $config = self::getConfig();
        $companyName = $config['integrations']['crm_company_name'] ?? 'CashSecond';

        $name   = trim($leadData['full_name'] ?? 'Valued Customer');
        $email  = trim($leadData['email'] ?? '');
        $mobile = self::formatMobileNumber($leadData['mobile_number'] ?? '');

        $commentLines = [];
        $commentLines[] = "=== CASHSECOND WEBSITE LEAD ENQUIRY ===";
        $commentLines[] = "Model: " . ($leadData['phone_model'] ?? 'Apple iPhone');
        $commentLines[] = "Storage: " . ($leadData['storage'] ?? 'Not Specified');
        $commentLines[] = "Condition: " . ($leadData['condition'] ?? 'Not Specified');
        $commentLines[] = "Estimated Value: " . ($leadData['estimated_value'] ?? 'To be evaluated');

        if (!empty($leadData['notes'])) {
            $commentLines[] = "Customer Note: " . $leadData['notes'];
        }

        $commentLines[] = "Page Source: " . ($leadData['page_source'] ?? 'CashSecond Landing Page');
        $commentLines[] = "UTM Campaign: " . ($leadData['utm_campaign'] ?? 'None');
        $commentLines[] = "UTM Source: " . ($leadData['utm_source'] ?? 'Direct');
        $commentLines[] = "UTM Medium: " . ($leadData['utm_medium'] ?? 'None');
        $commentLines[] = "IP: " . ($leadData['ip_address'] ?? 'Unknown');
        $commentLines[] = "Submitted At: " . ($leadData['timestamp'] ?? date('d-m-Y H:i:s'));

        $customerComment = implode("\n", $commentLines);

        $payload = [
            'customerName'    => $name,
            'customerEmail'   => $email,
            'customerMobile'  => $mobile,
            'companyName'     => $companyName,
            'customerComment' => $customerComment,
            'label'           => $config['integrations']['crm_lead_label'] ?? 'selliphone'
        ];

        return self::sendRaw($payload);
    }
}
