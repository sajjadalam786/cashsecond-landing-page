<?php
/**
 * CashSecond - Valuation & Doorstep Confirmation Page (Thank You)
 * Official Price Reveal, Mandatory Valuation Feedback & Doorstep Pickup Scheduling.
 * Optimized for Google Ads Conversion Tracking & Apple-grade aesthetic.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$config = require_once __DIR__ . '/config/config.php';
$base_path = '';
$business = $config['business'] ?? [];

$model        = isset($_GET['model']) ? htmlspecialchars(strip_tags(trim($_GET['model'])), ENT_QUOTES, 'UTF-8') : 'Apple iPhone';
$variant      = isset($_GET['variant']) ? htmlspecialchars(strip_tags(trim($_GET['variant'])), ENT_QUOTES, 'UTF-8') : '';
$val          = isset($_GET['val']) ? (int)preg_replace('/[^0-9]/', '', $_GET['val']) : 0;
$name         = isset($_GET['name']) ? htmlspecialchars(strip_tags(trim($_GET['name'])), ENT_QUOTES, 'UTF-8') : 'Valued Customer';
$phone        = isset($_GET['phone']) ? htmlspecialchars(strip_tags(trim($_GET['phone'])), ENT_QUOTES, 'UTF-8') : '';
$ref_id       = isset($_GET['ref']) ? htmlspecialchars(strip_tags(trim($_GET['ref'])), ENT_QUOTES, 'UTF-8') : 'EXG-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 4));

$device_display = trim($model . ($variant ? " ($variant)" : ''));

$noindex          = true;
$page_title       = "Valuation & Pickup Confirmed | " . ($business['name'] ?? 'CashSecond');
$page_description = "Your official iPhone valuation quote and Mumbai doorstep pickup details.";

require __DIR__ . '/includes/header.php';
?>

<div class="thankyou-page-wrapper">
    <!-- Main Confirmation Container -->
    <main class="thankyou-card-container">
        <div class="thankyou-card">
            <!-- Animated Verified Check Icon -->
            <div class="thankyou-icon-wrap" aria-hidden="true">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>

            <span class="thankyou-badge-confirmed">✓ Valuation Calculated Successfully</span>
            <h1 class="thankyou-title">Your Valuation Quote</h1>
            <p class="thankyou-subtitle">Thank you, <strong><?= htmlspecialchars($name) ?></strong>. Here is your official estimated resale quote for your device.</p>

            <!-- Revealed Valuation Hero Card -->
            <div class="thankyou-valuation-hero">
                <div class="ty-device-name"><?= htmlspecialchars($device_display) ?></div>
                <div class="ty-amount-caption">Estimated Resale Value</div>
                <div class="ty-amount-value">₹ <?= $val > 0 ? number_format($val) : '48,500' ?></div>
                <div class="ty-ref-pill">Booking Ref: <?= htmlspecialchars($ref_id) ?></div>
                <p class="ty-sub-note">Free Mumbai doorstep pickup • Spot UPI / Cash payment upon physical verification</p>
            </div>

            <!-- Feedback & Doorstep Pickup Scheduling Box -->
            <div class="ty-feedback-schedule-card" id="tyScheduleCard">
                <!-- Feedback Section -->
                <div class="ty-section-header">
                    <div class="ty-section-title">
                        <span>How was your valuation estimate?</span>
                        <span class="ty-req-badge">Required</span>
                    </div>
                    <p class="ty-section-subtitle">Please rate our price quote to confirm your doorstep pickup:</p>
                </div>

                <div class="ty-feedback-pills-grid" id="tyFeedbackPills">
                    <button type="button" class="ty-feedback-pill" data-val="Too Less Price">Too Less Price</button>
                    <button type="button" class="ty-feedback-pill" data-val="Less Price">Less Price</button>
                    <button type="button" class="ty-feedback-pill" data-val="Average Price">Average Price</button>
                    <button type="button" class="ty-feedback-pill" data-val="Good Price">Good Price</button>
                    <button type="button" class="ty-feedback-pill" data-val="Awesome">Awesome! 🔥</button>
                </div>

                <div class="ty-comment-wrap">
                    <textarea id="tyFeedbackComment" class="ty-comment-textarea" placeholder="Share your experience or suggestion (optional)..." rows="2"></textarea>
                </div>

                <div id="tyFeedbackError" class="ty-feedback-error" style="display:none;">Please select one price rating option above to schedule your pickup.</div>

                <hr style="border: none; border-top: 1px solid #E5E5EA; margin: 16px 0;">

                <!-- Pickup Slot Selector -->
                <div class="ty-section-header">
                    <div class="ty-section-title">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <span>Select Doorstep Pickup Slot:</span>
                    </div>
                </div>

                <div class="ty-slots-grid" id="tyDateSlotsGrid" style="margin-bottom: 8px;">
                    <button type="button" class="ty-slot-pill selected" data-date="Today">Today</button>
                    <button type="button" class="ty-slot-pill" data-date="Tomorrow">Tomorrow</button>
                    <button type="button" class="ty-slot-pill" data-date="Day After">Day After</button>
                </div>

                <div class="ty-slots-grid" id="tyTimeSlotsGrid" style="margin-bottom: 16px;">
                    <button type="button" class="ty-slot-pill selected" data-slot="Express (Within 6 Hours)">⚡ Express (Within 6 Hours)</button>
                    <button type="button" class="ty-slot-pill" data-slot="10:00 AM - 1:00 PM">10:00 AM - 1:00 PM</button>
                    <button type="button" class="ty-slot-pill" data-slot="1:00 PM - 5:00 PM">1:00 PM - 5:00 PM</button>
                    <button type="button" class="ty-slot-pill" data-slot="5:00 PM - 9:00 PM">5:00 PM - 9:00 PM</button>
                </div>

                <!-- Schedule CTA Button -->
                <button type="button" id="tyConfirmPickupBtn" class="btn ty-btn-primary" style="font-size: 1rem; padding: 14px 20px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><polyline points="9 15 12 18 15 15"/></svg>
                    <span>Schedule Doorstep Pickup (Within 6 Hours) →</span>
                </button>
            </div>

            <!-- Pickup Success Banner (Shown after scheduling) -->
            <div id="tyPickupSuccessBox" style="display:none; background:#E8F5E9; border:1.5px solid #A5D6A7; border-radius:18px; padding:18px 20px; margin-bottom:20px; text-align:left;">
                <div style="display:flex; align-items:center; gap:8px; font-weight:800; font-size:1rem; color:#1B5E20; margin-bottom:4px;">
                    <span>✓</span>
                    <span>Doorstep Pickup Scheduled!</span>
                </div>
                <div style="font-size:0.84375rem; color:#2E7D32; line-height:1.45;">
                    Thank you! Your pickup window (<strong id="tyConfirmedSlotText">Today • Express Within 6 Hours</strong>) has been registered. Our Mumbai verification specialist will call you shortly before arrival.
                </div>
            </div>

            <!-- Booking Specifications Deck -->
            <div class="thankyou-valuation-deck">
                <div class="valuation-row">
                    <span class="label">Reference ID</span>
                    <span class="value" style="font-family: monospace; font-weight: 700; color: #0071E3;"><?= htmlspecialchars($ref_id) ?></span>
                </div>
                <div class="valuation-row">
                    <span class="label">Device Model</span>
                    <span class="value"><?= htmlspecialchars($device_display) ?></span>
                </div>
                <div class="valuation-row">
                    <span class="label">Customer Name</span>
                    <span class="value"><?= htmlspecialchars($name) ?></span>
                </div>
                <?php if (!empty($phone)): ?>
                <div class="valuation-row">
                    <span class="label">Contact Phone</span>
                    <span class="value">+91 <?= htmlspecialchars($phone) ?></span>
                </div>
                <?php endif; ?>
                <div class="valuation-row">
                    <span class="label">Doorstep Service Area</span>
                    <span class="value">Mumbai &amp; MMR (Free Doorstep Inspection)</span>
                </div>
                <div class="valuation-row">
                    <span class="label">Payment Mode</span>
                    <span class="value">Instant Spot UPI / Cash</span>
                </div>
            </div>

            <!-- What Happens Next 3-Step Timeline -->
            <div class="ty-next-steps-card">
                <h3 class="ty-steps-title">What Happens Next?</h3>
                <div class="ty-step-item">
                    <span class="ty-step-num">1</span>
                    <div>
                        <strong>Confirmation Call:</strong>
                        <p>Our executive will call you to confirm your exact doorstep address and arrival time.</p>
                    </div>
                </div>
                <div class="ty-step-item">
                    <span class="ty-step-num">2</span>
                    <div>
                        <strong>5-Minute Doorstep Diagnostic:</strong>
                        <p>Our trained technician inspects screen, cameras, and battery health at your doorstep.</p>
                    </div>
                </div>
                <div class="ty-step-item">
                    <span class="ty-step-num">3</span>
                    <div>
                        <strong>Spot Payment &amp; Certified Wipe:</strong>
                        <p>Immediate bank transfer / UPI payment before handover with a government-compliant data destruction receipt.</p>
                    </div>
                </div>
            </div>

            <!-- Quick Action Buttons -->
            <div class="ty-action-btns">
                <a href="<?= $base_path ?>index.php" class="btn ty-btn-secondary">
                    Return to Homepage
                </a>
                <a href="tel:<?= preg_replace('/[^0-9+]/', '', $business['phone'] ?? '+918976332211') ?>" class="btn ty-btn-secondary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    <span>Call Support: <?= htmlspecialchars($business['phone'] ?? '+91 897633 2211') ?></span>
                </a>
            </div>

            <!-- Trust Strip -->
            <div class="thankyou-trust-strip">
                <span>⚡ Free Mumbai Doorstep Pickup</span>
                <span>•</span>
                <span>🛡️ Certified Data Erasure</span>
                <span>•</span>
                <span>💰 Instant Spot UPI / Cash</span>
            </div>
        </div>
    </main>
</div>

<!-- Interactive Scheduling & Feedback Client Script -->
<script>
    (function () {
        var selectedFeedbackRating = '';
        var selectedPickupDate = 'Today';
        var selectedPickupSlot = 'Express (Within 6 Hours)';
        var refId = <?= json_encode($ref_id) ?>;

        // Feedback Pills
        var fbPills = document.querySelectorAll('#tyFeedbackPills .ty-feedback-pill');
        var fbError = document.getElementById('tyFeedbackError');

        fbPills.forEach(function (pill) {
            pill.addEventListener('click', function () {
                fbPills.forEach(function (p) { p.classList.remove('selected'); });
                pill.classList.add('selected');
                selectedFeedbackRating = pill.getAttribute('data-val') || '';
                if (fbError) fbError.style.display = 'none';
            });
        });

        // Date Pills
        var datePills = document.querySelectorAll('#tyDateSlotsGrid .ty-slot-pill');
        datePills.forEach(function (pill) {
            pill.addEventListener('click', function () {
                datePills.forEach(function (p) { p.classList.remove('selected'); });
                pill.classList.add('selected');
                selectedPickupDate = pill.getAttribute('data-date') || 'Today';
            });
        });

        // Time Pills
        var timePills = document.querySelectorAll('#tyTimeSlotsGrid .ty-slot-pill');
        timePills.forEach(function (pill) {
            pill.addEventListener('click', function () {
                timePills.forEach(function (p) { p.classList.remove('selected'); });
                pill.classList.add('selected');
                selectedPickupSlot = pill.getAttribute('data-slot') || 'Express (Within 6 Hours)';
            });
        });

        // Confirm Pickup Button
        var confirmBtn = document.getElementById('tyConfirmPickupBtn');
        var scheduleCard = document.getElementById('tyScheduleCard');
        var successBox = document.getElementById('tyPickupSuccessBox');
        var confirmedSlotText = document.getElementById('tyConfirmedSlotText');

        if (confirmBtn) {
            confirmBtn.addEventListener('click', function () {
                if (!selectedFeedbackRating) {
                    if (fbError) {
                        fbError.style.display = 'block';
                        fbError.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }
                    return;
                }

                confirmBtn.disabled = true;
                confirmBtn.innerHTML = '<span>Saving Pickup Schedule...</span>';

                var commentEl = document.getElementById('tyFeedbackComment');
                var commentText = commentEl ? commentEl.value.trim() : '';

                var fbData = new FormData();
                fbData.append('action', 'feedback');
                fbData.append('ref_id', refId);
                fbData.append('feedback_rating', selectedFeedbackRating);
                fbData.append('feedback_comment', commentText);
                fbData.append('pickup_date', selectedPickupDate);
                fbData.append('pickup_slot', selectedPickupSlot);

                fetch('forms/buyback-questionnaire.php', { method: 'POST', body: fbData })
                    .catch(function () {});

                setTimeout(function () {
                    if (scheduleCard) scheduleCard.style.display = 'none';
                    if (confirmedSlotText) confirmedSlotText.textContent = selectedPickupDate + ' • ' + selectedPickupSlot;
                    if (successBox) {
                        successBox.style.display = 'block';
                        successBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }, 400);
            });
        }
    })();
</script>

<!-- Google Ads Conversion Tracking Event Hook -->
<script>
    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push({
        'event': 'valuation_lead_completed',
        'transaction_id': <?= json_encode($ref_id) ?>,
        'value': <?= (int)$val ?>,
        'currency': 'INR',
        'device_model': <?= json_encode($device_display) ?>
    });
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
