<?php
/**
 * CashSecond - Security & URL Cryptographic Signing Module
 * Provides HMAC SHA-256 signature generation and validation for lead verification and Thank You page gatekeeping.
 */

if (!function_exists('get_lead_signature_secret')) {
    function get_lead_signature_secret(): string {
        static $secret = null;
        if ($secret !== null) {
            return $secret;
        }

        // 1. Try reading from config file
        $configFile = __DIR__ . '/../config/config.php';
        if (file_exists($configFile)) {
            $config = require $configFile;
            if (!empty($config['security']['lead_signature_secret'])) {
                $secret = (string)$config['security']['lead_signature_secret'];
                return $secret;
            }
        }

        // 2. Check environment variable
        $envVal = getenv('LEAD_SIGNATURE_SECRET');
        if (!empty($envVal)) {
            $secret = (string)$envVal;
            return $secret;
        }

        if (isset($_ENV['LEAD_SIGNATURE_SECRET']) && !empty($_ENV['LEAD_SIGNATURE_SECRET'])) {
            $secret = (string)$_ENV['LEAD_SIGNATURE_SECRET'];
            return $secret;
        }

        // 3. Fallback secret
        $secret = 'CS_SEC_SIG_786_e8d4a92c0b7e41fa89b2';
        return $secret;
    }
}

if (!function_exists('generate_lead_signature')) {
    function generate_lead_signature(string $ref_id, int $timestamp): string {
        $secret = get_lead_signature_secret();
        return hash_hmac('sha256', $ref_id . '|' . $timestamp, $secret);
    }
}

if (!function_exists('verify_lead_signature')) {
    function verify_lead_signature(string $ref_id, int $timestamp, string $sig, int $max_age = 600): bool {
        if (empty($ref_id) || empty($timestamp) || empty($sig)) {
            return false;
        }

        // Strict age check (10 minutes default)
        if (abs(time() - $timestamp) > $max_age) {
            return false;
        }

        $expected = generate_lead_signature($ref_id, $timestamp);
        return hash_equals($expected, $sig);
    }
}

if (!function_exists('authorize_lead_session')) {
    function authorize_lead_session(string $ref_id, int $timestamp, string $sig): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['authorized_leads']) || !is_array($_SESSION['authorized_leads'])) {
            $_SESSION['authorized_leads'] = [];
        }

        $_SESSION['authorized_leads'][$ref_id] = [
            'ts'  => $timestamp,
            'sig' => $sig,
            'ip'  => $_SERVER['REMOTE_ADDR'] ?? ''
        ];
    }
}

if (!function_exists('is_lead_session_authorized')) {
    function is_lead_session_authorized(string $ref_id, int $max_age = 1800): bool {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($ref_id) || empty($_SESSION['authorized_leads'][$ref_id])) {
            return false;
        }

        $sessionLead = $_SESSION['authorized_leads'][$ref_id];
        $ts = isset($sessionLead['ts']) ? (int)$sessionLead['ts'] : 0;

        if ($ts <= 0 || abs(time() - $ts) > $max_age) {
            return false;
        }

        return true;
    }
}

if (!function_exists('build_signed_thankyou_url')) {
    function build_signed_thankyou_url(string $base_url, array $params): string {
        $ref_id    = $params['ref'] ?? ($params['lead_id'] ?? '');
        $timestamp = time();
        $sig       = generate_lead_signature($ref_id, $timestamp);

        authorize_lead_session($ref_id, $timestamp, $sig);

        $params['ts']  = $timestamp;
        $params['sig'] = $sig;

        $query = http_build_query($params);
        return rtrim($base_url, '?') . (strpos($base_url, '?') === false ? '?' : '&') . $query;
    }
}
