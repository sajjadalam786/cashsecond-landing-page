<?php
/**
 * CashSecond iPhone Valuation Component — Embed Template (valuator.php)
 *
 * Drop-in include for any PHP page:
 *   require_once '/path/to/components/iphone-valuator/valuator.php';
 *
 * Optional configuration before including:
 *   $iv_config = [
 *       'submit_url'         => '/my-site/components/iphone-valuator/submit-handler.php',
 *       'thankyou_url'       => '/my-site/thankyou.php',
 *       'wa_number'          => '918976332211',
 *       'redirect_on_success'=> false,
 *       'base_path'          => '',   // path prefix for assets
 *       'privacy_url'        => 'policies/privacy-policy.php',
 *   ];
 */

// -------------------------------------------------------
// 1. Resolve configuration
// -------------------------------------------------------
if (!isset($iv_config) || !is_array($iv_config)) {
    $iv_config = [];
}

// Auto-detect component base path relative to the embedding page
$_iv_component_dir = __DIR__;
$_iv_base_path     = $iv_config['base_path'] ?? (isset($base_path) ? $base_path : '');

$_iv_submit_url  = $iv_config['submit_url']          ?? ($_iv_base_path . 'components/iphone-valuator/submit-handler.php');
$_iv_thankyou    = $iv_config['thankyou_url']         ?? ($_iv_base_path . 'thankyou.php');
$_iv_wa_number   = $iv_config['wa_number']            ?? '918976332211';
$_iv_redirect    = $iv_config['redirect_on_success']  ?? false;
$_iv_privacy_url = $iv_config['privacy_url']          ?? ($_iv_base_path . 'policies/privacy-policy.php');

// -------------------------------------------------------
// 2. Load and parse pricing CSV → build JSON matrices
// -------------------------------------------------------
$_iv_pricing_data = require $_iv_component_dir . '/data/pricing.php';
$_iv_matrix       = $_iv_pricing_data['matrix']  ?? [];
$_iv_models_map   = $_iv_pricing_data['models']  ?? [];

$_iv_matrix_json    = json_encode($_iv_matrix,    JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);
$_iv_models_json    = json_encode($_iv_models_map,JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);

// CSRF token (reuse session token if available)
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$_iv_csrf = $_SESSION['csrf_token'];

// -------------------------------------------------------
// 3. Output: CSS link, Modal HTML, JS embed
// -------------------------------------------------------
?>
<!-- =====================================================
     IPHONE VALUATOR COMPONENT
     ===================================================== -->
<link rel="stylesheet" href="<?= $_iv_base_path ?>components/iphone-valuator/valuator.css?v=<?= file_exists($_iv_component_dir . '/valuator.css') ? filemtime($_iv_component_dir . '/valuator.css') : time() ?>">

<!-- Modal Overlay -->
<div class="iv-overlay" id="ivOverlay" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="ivStepLabel">
    <div class="iv-modal" id="ivModal">

        <!-- HEADER -->
        <div class="iv-header">
            <div class="iv-header-top">
                <div class="iv-brand-row">
                    <div class="iv-device-badge">
                        <span>📱</span>
                        <span id="ivDeviceBadge">Select iPhone</span>
                    </div>
                </div>
                <button type="button" class="iv-close-btn" id="ivCloseBtn" aria-label="Close valuation">&times;</button>
            </div>

            <!-- Progress Bar -->
            <div class="iv-progress-wrap">
                <div class="iv-progress-bar">
                    <div class="iv-progress-fill" id="ivProgressFill"></div>
                </div>
                <span class="iv-step-label" id="ivStepLabel">Step 1 of 11</span>
            </div>
        </div>

        <!-- BODY (questions rendered here by JS) -->
        <div class="iv-body" id="ivBody">
            <!-- Populated by valuator.js -->
        </div>

        <!-- FOOTER NAVIGATION -->
        <div class="iv-footer" id="ivFooter">
            <button type="button" class="iv-btn-back" id="ivBtnBack" aria-label="Previous step">← Back</button>
            <button type="button" class="iv-btn-next" id="ivBtnNext">Next →</button>
        </div>

    </div>
</div>

<!-- Pricing Matrix & Config (embedded server-side from CSV) -->
<script>
    window.IV_PRICING_MATRIX = <?= $_iv_matrix_json ?>;
    window.IV_MODELS_MAP     = <?= $_iv_models_json ?>;
    window.csrfToken         = <?= json_encode($_iv_csrf) ?>;
    window.IV_CONFIG         = {
        submitUrl:        <?= json_encode($_iv_submit_url) ?>,
        thankyouUrl:      <?= json_encode($_iv_thankyou) ?>,
        waNumber:         <?= json_encode($_iv_wa_number) ?>,
        redirectOnSuccess:<?= $_iv_redirect ? 'true' : 'false' ?>,
        privacyUrl:       <?= json_encode($_iv_privacy_url) ?>
    };
</script>
<script src="<?= $_iv_base_path ?>components/iphone-valuator/valuator.js?v=<?= file_exists($_iv_component_dir . '/valuator.js') ? filemtime($_iv_component_dir . '/valuator.js') : time() ?>" defer></script>
<!-- END IPHONE VALUATOR COMPONENT -->
