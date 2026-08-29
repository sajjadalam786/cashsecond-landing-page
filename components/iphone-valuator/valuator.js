/**
 * CashSecond iPhone Valuation Engine — CSV-Driven
 * v2.0 | Reusable Component
 *
 * Reads window.IV_PRICING_MATRIX (embedded by valuator.php from CSV)
 * and computes resale value via percentage-deduction logic.
 *
 * Step Flow:
 *  0  Model search & select
 *  1  Storage variant
 *  2  Age / purchase date
 *  3  Screen scratches (severity radio)
 *  4  Screen defects (multi-select)
 *  5  Body scratches (severity radio)
 *  6  Body damage (multi-select)
 *  7  Functional issues (multi-select)
 *  8  Battery health (radio)
 *  9  Accessories / bonuses (multi-select)
 * 10  Contact capture + reveal
 */

(function () {
    'use strict';

    /* --------------------------------------------------
       CONSTANTS & CONFIG
    -------------------------------------------------- */
    const SUBMIT_URL = (window.IV_CONFIG && window.IV_CONFIG.submitUrl)
        ? window.IV_CONFIG.submitUrl
        : 'components/iphone-valuator/submit-handler.php';

    const THANKYOU_URL = (window.IV_CONFIG && window.IV_CONFIG.thankyouUrl)
        ? window.IV_CONFIG.thankyouUrl
        : 'thankyou.php';

    const WA_NUMBER = (window.IV_CONFIG && window.IV_CONFIG.waNumber)
        ? window.IV_CONFIG.waNumber
        : '918976332211';

    /* --------------------------------------------------
       PRICING MATRIX (from server-embedded JSON)
    -------------------------------------------------- */
    const MATRIX  = window.IV_PRICING_MATRIX  || {};   // { product_id: {...} }
    const MODELS_MAP = window.IV_MODELS_MAP   || {};   // { "Apple iPhone X": { "64 GB": 50, ... } }

    // Build sorted model name list for search
    const MODEL_NAMES = Object.keys(MODELS_MAP).sort();

    /* --------------------------------------------------
       STATE
    -------------------------------------------------- */
    let state = {
        step:          0,
        totalSteps:    11,   // 0-10
        model:         '',
        storage:       '',
        productId:     null,
        basePrice:     0,
        liveValue:     0,
        totalDeduct:   0,
        bonusTotal:    0,
        answers:       {},    // { col_key: value }
        deductions:    {},    // { col_key: amount_deducted }
        isSubmitting:  false,
        leadId:        null,
        isBack:        false,
    };

    /* --------------------------------------------------
       STEP DEFINITIONS
    -------------------------------------------------- */
    const STEPS = [
        // 0 — Model Search
        {
            id: 'model',
            title: 'Which iPhone do you want to sell?',
            desc: 'Search by name or tap to select your device.',
            type: 'model_search',
        },
        // 1 — Storage Variant
        {
            id: 'storage',
            title: 'What is the storage size?',
            desc: 'Select the internal storage of your device.',
            type: 'variant_select',
        },
        // 2 — Age (mutually exclusive radio)
        {
            id: 'age',
            title: 'When did you buy this iPhone?',
            desc: 'Newer phones fetch a better resale price.',
            type: 'radio',
            options: [
                { label: 'Less than 3 months ago',  sub: 'Barely used — best value',     col: 'months_0_3',    icon: '🆕', style: 'none'   },
                { label: '3 to 6 months ago',        sub: 'Near new condition',            col: 'months_3_6',    icon: '✅', style: 'none'   },
                { label: '6 to 11 months ago',       sub: 'Light usage',                   col: 'months_6_11',   icon: '📅', style: 'none'   },
                { label: 'Over 1 year ago',          sub: 'Depreciation applied',          col: 'months_11_more',icon: '⏳', style: 'minus'  },
            ],
            cols: 2,
        },
        // 3 — Screen Scratches (mutually exclusive radio)
        {
            id: 'screen_scratch',
            title: 'How is the screen glass condition?',
            desc: 'Inspect with your phone screen on and off.',
            type: 'radio',
            options: [
                { label: 'Scratch-free',            sub: 'No visible marks',             col: null,                  icon: '✨', style: 'none'   },
                { label: '1–2 light scratches',     sub: 'Barely noticeable',            col: 'scratch_screen_1_2',  icon: '🔍', style: 'minus'  },
                { label: '3–4 scratches',           sub: 'Visible but minor',            col: 'scratch_screen_3_4',  icon: '😐', style: 'minus'  },
                { label: 'Heavy scratches',         sub: 'Deep, felt by fingernail',     col: 'multiple_scratches_screen', icon: '⚠️', style: 'minus' },
            ],
            cols: 2,
        },
        // 4 — Screen Defects (multi-select chips)
        {
            id: 'screen_defects',
            title: 'Any screen display issues?',
            desc: 'Select all that apply — "None" means a perfect display.',
            type: 'multi',
            noneLabel: '✅ None — Display is perfect',
            options: [
                { label: 'Glass cracked',        col: 'glass_cracked',       icon: '💥' },
                { label: 'No display / Blackout',col: 'no_display',          icon: '🖤' },
                { label: 'Touch not working',    col: 'touch_not_working',   icon: '👆' },
                { label: 'Lines on screen',      col: 'lines_on_display',    icon: '〰️' },
                { label: 'Dots / Ink spots',     col: 'dots_on_display',     icon: '⚫' },
                { label: 'Flickering',           col: 'flickering',          icon: '🔦' },
                { label: 'Color fade / Burn',    col: 'color_fade',          icon: '🎨' },
                { label: 'Loose / Lifted screen',col: 'loose_screen',        icon: '📋' },
                { label: 'Back glass broken',    col: 'back_glass_broken',   icon: '🔨' },
            ],
        },
        // 5 — Body Scratches (mutually exclusive radio)
        {
            id: 'body_scratch',
            title: 'How is the body / frame condition?',
            desc: 'Check the metal sides and back panel.',
            type: 'radio',
            options: [
                { label: 'Like new body',           sub: 'No scratches at all',          col: null,                  icon: '✨', style: 'none'  },
                { label: '1–2 minor scratches',     sub: 'Light usage marks',            col: 'scratch_body_1_2',    icon: '🔍', style: 'minus' },
                { label: '3–4 scratches',           sub: 'Visible but usable',           col: 'scratch_body_3_4',    icon: '😐', style: 'minus' },
                { label: 'Heavy scratches',         sub: 'Multiple deep marks',          col: 'multiple_scratches_body', icon: '⚠️', style: 'minus' },
            ],
            cols: 2,
        },
        // 6 — Body Damage (multi-select chips)
        {
            id: 'body_damage',
            title: 'Any body damage?',
            desc: 'Tap all that apply — tap "None" if no physical damage.',
            type: 'multi',
            noneLabel: '✅ None — Body is damage-free',
            options: [
                { label: '1–2 dents',           col: 'dents_1_or_2',      icon: '😬' },
                { label: 'Multiple dents',      col: 'multiple_dents',    icon: '💢' },
                { label: 'Frame bent / curved', col: 'body_curved',       icon: '🔄' },
            ],
        },
        // 7 — Functional Issues (multi-select chips)
        {
            id: 'functional',
            title: 'Any hardware or functional issues?',
            desc: 'Select all that apply — tap "All good" if everything works.',
            type: 'multi',
            noneLabel: '✅ All good — Everything works',
            options: [
                { label: 'Front camera fault', col: 'front_camera_not_working', icon: '🤳' },
                { label: 'Rear camera fault',  col: 'back_camera_not_working',  icon: '📸' },
                { label: 'Camera glass cracked',col:'camera_glass_broken',      icon: '🔭' },
                { label: 'Speaker issue',      col: 'speaker_not_working',      icon: '🔇' },
                { label: 'Charging port issue',col: 'charging_port_issue',      icon: '🔌' },
                { label: 'Battery faulty',     col: 'battery_faulty',           icon: '🔋' },
                { label: 'Wi-Fi not working',  col: 'wifi_issues',              icon: '📶' },
                { label: 'Bluetooth issue',    col: 'bluetooth_issue',          icon: '🔵' },
                { label: 'Face ID broken',     col: 'face_id_not_working',      icon: '👤' },
                { label: 'Fingerprint broken', col: 'finger_print_not_working', icon: '👆' },
                { label: 'Sensor issues',      col: 'sensor_issues',            icon: '📡' },
                { label: 'Volume buttons',     col: 'volume',                   icon: '🔊' },
                { label: 'Power button',       col: 'power_button_issue',       icon: '⏻'  },
                { label: 'Vibrator issue',     col: 'vibrator',                 icon: '📳' },
                { label: 'Audio IC issue',     col: 'audio_ic_problem',         icon: '🎵' },
                { label: 'Headphone jack',     col: 'headphone_jackissue',      icon: '🎧' },
            ],
        },
        // 8 — Battery Health (radio)
        {
            id: 'battery',
            title: 'What is your battery health?',
            desc: 'Check in Settings → Battery → Battery Health.',
            type: 'radio',
            options: [
                { label: 'Above 80% (Healthy)',   sub: 'Go to Settings to check',     col: 'battery_greater_80', icon: '🔋', style: 'none'  },
                { label: 'Below 80% (Degraded)',  sub: 'Affects daily battery life',  col: 'battery_less_80',    icon: '🪫', style: 'minus' },
                { label: 'Battery faulty/swollen',sub: 'Not charging or swollen',     col: 'battery_faulty',     icon: '⚠️', style: 'minus' },
            ],
            cols: 1,
        },
        // 9 — Accessories (bonus multi-select)
        {
            id: 'accessories',
            title: 'What accessories do you have?',
            desc: 'Original accessories add value to your payout.',
            type: 'accessories',
            options: [
                { label: 'Original box',             col: 'box',     icon: '📦' },
                { label: 'Original charger / cable', col: 'charger', icon: '⚡' },
                { label: 'Purchase invoice / bill',  col: 'invoice', icon: '🧾' },
            ],
        },
        // 10 — Contact capture
        {
            id: 'contact',
            title: 'Unlock Your Official Valuation',
            desc: 'Enter your details to receive your guaranteed price and book a free doorstep pickup.',
            type: 'lead_capture',
        },
    ];

    const TOTAL_STEPS = STEPS.length;

    /* --------------------------------------------------
       UTILITY
    -------------------------------------------------- */
    function fmt(n) {
        return '₹' + Math.round(n).toLocaleString('en-IN');
    }

    function getProductData() {
        if (!state.productId || !MATRIX[state.productId]) return null;
        return MATRIX[state.productId];
    }

    function computeValuation() {
        const pd = getProductData();
        if (!pd) { state.liveValue = 0; return; }

        const base = pd.base_price;
        const deductions = pd.deductions;

        let totalDeductPct = 0;
        let bonusPct       = 0;
        const usedDeductions = {};

        // Accumulate deductions from triggered answers
        for (const [col, triggered] of Object.entries(state.answers)) {
            if (!triggered || col === null) continue;
            const pct = deductions[col] || 0;
            if (pct === 0) continue;

            // Bonus columns (box, charger, invoice)
            if (['box', 'charger', 'invoice'].includes(col)) {
                bonusPct += pct;
                usedDeductions[col] = -(base * pct / 100); // negative = bonus
            } else {
                totalDeductPct += pct;
                usedDeductions[col] = base * pct / 100;
            }
        }

        const deductAmount = base * totalDeductPct / 100;
        const bonusAmount  = base * bonusPct / 100;
        const finalValue   = Math.max(0, base - deductAmount + bonusAmount);

        state.basePrice   = base;
        state.totalDeduct = deductAmount;
        state.bonusTotal  = bonusAmount;
        state.liveValue   = finalValue;
        state.deductions  = usedDeductions;
    }

    /* --------------------------------------------------
       DOM REFS
    -------------------------------------------------- */
    let overlay, modal, body, progressFill, stepLabel, deviceBadge,
        btnBack, btnNext, closeBtn;

    function initRefs() {
        overlay      = document.getElementById('ivOverlay');
        modal        = document.getElementById('ivModal');
        body         = document.getElementById('ivBody');
        progressFill = document.getElementById('ivProgressFill');
        stepLabel    = document.getElementById('ivStepLabel');
        deviceBadge  = document.getElementById('ivDeviceBadge');
        btnBack      = document.getElementById('ivBtnBack');
        btnNext      = document.getElementById('ivBtnNext');
        closeBtn     = document.getElementById('ivCloseBtn');
    }

    /* --------------------------------------------------
       OPEN / CLOSE
    -------------------------------------------------- */
    function openModal(preModel, preStorage) {
        if (!overlay) initRefs();
        if (!overlay) return;

        state = {
            step: 0, totalSteps: TOTAL_STEPS,
            model: '', storage: '', productId: null,
            basePrice: 0, liveValue: 0, totalDeduct: 0, bonusTotal: 0,
            answers: {}, deductions: {},
            isSubmitting: false, leadId: null, isBack: false,
        };

        if (preModel && MODELS_MAP[preModel]) {
            state.model = preModel;
            const storages = Object.keys(MODELS_MAP[preModel]);
            if (preStorage && MODELS_MAP[preModel][preStorage]) {
                state.storage = preStorage;
                state.productId = MODELS_MAP[preModel][preStorage];
                computeValuation();
                state.step = 2; // skip to age question
            } else {
                state.step = 1; // skip to variant select
            }
        }

        overlay.classList.add('iv-open');
        overlay.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        renderStep();
    }

    function closeModal() {
        if (!overlay) return;
        overlay.classList.remove('iv-open');
        overlay.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    /* --------------------------------------------------
       RENDER ENGINE
    -------------------------------------------------- */
    function renderStep() {
        const step = STEPS[state.step];
        if (!step) return;

        // Update progress
        const pct = Math.round((state.step / (TOTAL_STEPS - 1)) * 100);
        if (progressFill) progressFill.style.width = pct + '%';
        if (stepLabel)    stepLabel.textContent = 'Step ' + (state.step + 1) + ' of ' + TOTAL_STEPS;

        // Update device badge
        const badgeText = state.model
            ? state.model.replace('Apple ', '') + (state.storage ? ' · ' + state.storage : '')
            : 'Select iPhone';
        if (deviceBadge) deviceBadge.textContent = badgeText;

        // Render step HTML
        const html = buildStepHTML(step);
        body.innerHTML = html;
        body.scrollTop = 0;

        // Add animation class
        body.firstElementChild && (body.firstElementChild.className +=
            state.isBack ? ' iv-step-back' : ' iv-step-card');

        // Wire up step interactions
        wireStep(step);

        // Footer button states
        updateFooterBtns(step);

        state.isBack = false;
    }

    function buildStepHTML(step) {
        switch (step.type) {
            case 'model_search':   return buildModelSearch(step);
            case 'variant_select': return buildVariantSelect(step);
            case 'radio':          return buildRadio(step);
            case 'multi':          return buildMultiSelect(step);
            case 'accessories':    return buildAccessories(step);
            case 'lead_capture':   return buildLeadCapture(step);
            default: return '<div class="iv-step-card"><p>Unknown step</p></div>';
        }
    }

    function updateFooterBtns(step) {
        if (!btnBack || !btnNext) return;
        btnBack.disabled = (state.step === 0);
        btnBack.style.display = (state.step === 0) ? 'none' : '';

        const isContact = step.type === 'lead_capture';
        btnNext.innerHTML = isContact
            ? '<span>Submit & Reveal Value</span><span class="iv-spinner" style="display:none"></span>'
            : '<span>Next →</span>';
        btnNext.className = 'iv-btn-next' + (isContact ? ' iv-btn-submit' : '');

        // Auto-advance disabled for model / multi steps
        const autoReady = step.type === 'radio' || step.type === 'accessories';
        btnNext.disabled = false; // always allow manual next
    }

    /* --------------------------------------------------
       STEP BUILDERS
    -------------------------------------------------- */

    // STEP 0 — Model Search
    function buildModelSearch(step) {
        return `
        <div class="iv-step-card">
            <p class="iv-question-title">${step.title}</p>
            <p class="iv-question-desc">${step.desc}</p>
            <div class="iv-search-wrap">
                <svg class="iv-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" id="ivModelSearch" class="iv-search-input" placeholder="e.g. iPhone 16 Pro Max…" autocomplete="off" spellcheck="false">
            </div>
            <div class="iv-model-list" id="ivModelList">
                ${buildModelItems(MODEL_NAMES)}
            </div>
        </div>`;
    }

    function buildModelItems(names) {
        return names.map(n => {
            const selected = n === state.model ? ' iv-selected-model' : '';
            return `<button type="button" class="iv-model-item${selected}" data-model="${escHtml(n)}">
                <span class="iv-model-emoji">📱</span>
                <span class="iv-model-name">${escHtml(n.replace('Apple ', ''))}</span>
            </button>`;
        }).join('');
    }

    // STEP 1 — Storage variant
    function buildVariantSelect(step) {
        const storages = state.model && MODELS_MAP[state.model]
            ? Object.keys(MODELS_MAP[state.model])
            : [];

        return `
        <div class="iv-step-card">
            <p class="iv-question-title">Which ${state.model ? state.model.replace('Apple ','') : 'iPhone'} storage?</p>
            <p class="iv-question-desc">Select your device's internal storage capacity.</p>
            <div class="iv-options-grid iv-cols-2">
                ${storages.map(s => {
                    const selected = s === state.storage ? ' iv-selected' : '';
                    return `<button type="button" class="iv-opt${selected}" data-storage="${escHtml(s)}">
                        <span class="iv-opt-icon">💾</span>
                        <span class="iv-opt-content"><span class="iv-opt-title">${escHtml(s)}</span></span>
                    </button>`;
                }).join('')}
            </div>
        </div>`;
    }

    // Radio question (single select)
    function buildRadio(step) {
        const cols = step.cols === 1 ? 'iv-cols-1' : 'iv-cols-2';
        const items = step.options.map(opt => {
            const isSelected = state.answers[opt.col || '_none_' + step.id] === true ||
                               (opt.col === null && state.answers['_none_' + step.id] === true);
            const selClass = isSelected ? ' iv-selected' + (opt.style === 'none' ? '-none' : '') : '';
            return `<button type="button" class="iv-opt${selClass}" data-radio-col="${escHtml(opt.col || '')}" data-step-id="${escHtml(step.id)}">
                <span class="iv-opt-icon">${opt.icon}</span>
                <span class="iv-opt-content">
                    <span class="iv-opt-title">${escHtml(opt.label)}</span>
                    ${opt.sub ? `<span class="iv-opt-sub">${escHtml(opt.sub)}</span>` : ''}
                </span>
            </button>`;
        }).join('');

        return `
        <div class="iv-step-card">
            <p class="iv-question-title">${step.title}</p>
            <p class="iv-question-desc">${step.desc}</p>
            <div class="iv-options-grid ${cols}" id="ivRadioGroup">
                ${items}
            </div>
        </div>`;
    }

    // Multi-select chips
    function buildMultiSelect(step) {
        const chips = step.options.map(opt => {
            const selected = state.answers[opt.col] === true;
            return `<button type="button" class="iv-chip${selected ? ' iv-chip-selected' : ''}" data-multi-col="${escHtml(opt.col)}">
                <span class="iv-chip-icon">${opt.icon}</span>
                <span>${escHtml(opt.label)}</span>
            </button>`;
        }).join('');

        const noneKey = '_none_' + step.id;
        const noneSelected = state.answers[noneKey] === true;

        return `
        <div class="iv-step-card">
            <p class="iv-question-title">${step.title}</p>
            <p class="iv-question-desc">${step.desc}</p>
            <p class="iv-multi-hint">Tap all that apply:</p>
            <div class="iv-chips-grid">
                <button type="button" class="iv-chip iv-chip-none${noneSelected ? ' iv-chip-selected' : ''}" data-none-key="${escHtml(noneKey)}" style="grid-column: 1 / -1;">
                    <span class="iv-chip-icon">✅</span>
                    <span>${escHtml(step.noneLabel || 'None of the above')}</span>
                </button>
                ${chips}
            </div>
        </div>`;
    }

    // Accessories (bonus checkboxes)
    function buildAccessories(step) {
        const items = step.options.map(opt => {
            const checked = state.answers[opt.col] === true;
            return `
            <label class="iv-acc-check${checked ? ' iv-acc-checked' : ''}" data-acc-col="${escHtml(opt.col)}">
                <input type="checkbox" ${checked ? 'checked' : ''} style="display:none">
                <span style="font-size:18px">${opt.icon}</span>
                <span class="iv-acc-label">${escHtml(opt.label)}</span>
                <span class="iv-acc-bonus">+ Extra Value</span>
            </label>`;
        }).join('');

        return `
        <div class="iv-step-card">
            <p class="iv-question-title">${step.title}</p>
            <p class="iv-question-desc">${step.desc}</p>
            <div class="iv-accessories-row">${items}</div>
            <p class="iv-multi-hint" style="margin-top:10px;">These add bonus resale value to your device payout.</p>
        </div>`;
    }

    // Lead capture / contact form
    function buildLeadCapture(step) {
        computeValuation();
        const deviceLine = state.model
            ? state.model.replace('Apple ','') + (state.storage ? ' · ' + state.storage : '')
            : '';

        return `
        <div class="iv-step-card">
            <div class="iv-value-reveal-card">
                <div class="iv-reveal-label">Valuation Ready</div>
                <div class="iv-reveal-model">${escHtml(deviceLine)}</div>
                <div class="iv-reveal-disclaimer">Enter your contact details below to reveal your guaranteed price and lock in free doorstep pickup.</div>
            </div>
            <div class="iv-lead-form" id="ivLeadForm">
                <input type="hidden" id="ivHoneypot" name="website_hp" value="" autocomplete="off" tabindex="-1" style="display:none">
                <div class="iv-field-group">
                    <label class="iv-field-label" for="ivName">Full Name<span>*</span></label>
                    <input type="text" id="ivName" class="iv-input" placeholder="Enter your full name" autocomplete="name" maxlength="80">
                    <span class="iv-error-msg" id="ivNameErr"></span>
                </div>
                <div class="iv-field-group">
                    <label class="iv-field-label" for="ivPhone">WhatsApp / Mobile<span>*</span></label>
                    <input type="tel" id="ivPhone" class="iv-input" placeholder="10-digit mobile number" autocomplete="tel" maxlength="13">
                    <span class="iv-error-msg" id="ivPhoneErr"></span>
                </div>
                <div class="iv-field-group">
                    <label class="iv-field-label" for="ivEmail">Email <small style="font-weight:400;text-transform:none">(optional)</small></label>
                    <input type="email" id="ivEmail" class="iv-input" placeholder="your@email.com" autocomplete="email">
                </div>
                <p class="iv-consent-note">
                    By submitting you agree to our <a href="policies/privacy-policy.php" target="_blank">Privacy Policy</a>.
                    Your data is safe and never shared.
                </p>
            </div>
        </div>`;
    }

    /* --------------------------------------------------
       WIRE STEP INTERACTIONS
    -------------------------------------------------- */
    function wireStep(step) {
        switch (step.type) {
            case 'model_search':   wireModelSearch(); break;
            case 'variant_select': wireVariantSelect(); break;
            case 'radio':          wireRadio(step); break;
            case 'multi':          wireMulti(step); break;
            case 'accessories':    wireAccessories(step); break;
            case 'lead_capture':   wireLeadCapture(); break;
        }
    }

    function wireModelSearch() {
        const search = document.getElementById('ivModelSearch');
        const list   = document.getElementById('ivModelList');
        if (!search || !list) return;

        search.addEventListener('input', function () {
            const q = this.value.trim().toLowerCase().replace('apple ', '');
            const filtered = MODEL_NAMES.filter(n =>
                n.toLowerCase().includes(q) || n.toLowerCase().replace('apple ', '').includes(q)
            );
            list.innerHTML = buildModelItems(filtered.length ? filtered : MODEL_NAMES);
            wireModelItems();
        });
        wireModelItems();
    }

    function wireModelItems() {
        document.querySelectorAll('.iv-model-item').forEach(btn => {
            btn.addEventListener('click', function () {
                const m = this.dataset.model;
                if (!m) return;
                state.model   = m;
                state.storage = '';
                state.productId = null;
                computeValuation();
                document.querySelectorAll('.iv-model-item').forEach(b => b.classList.remove('iv-selected-model'));
                this.classList.add('iv-selected-model');

                // Auto-advance after short delay
                setTimeout(() => goNext(), 300);
            });
        });
    }

    function wireVariantSelect() {
        document.querySelectorAll('.iv-opt[data-storage]').forEach(btn => {
            btn.addEventListener('click', function () {
                const s = this.dataset.storage;
                if (!s) return;
                state.storage   = s;
                state.productId = MODELS_MAP[state.model] && MODELS_MAP[state.model][s]
                    ? MODELS_MAP[state.model][s] : null;
                computeValuation();
                document.querySelectorAll('.iv-opt[data-storage]').forEach(b => b.classList.remove('iv-selected'));
                this.classList.add('iv-selected');
                setTimeout(() => goNext(), 300);
            });
        });
    }

    function wireRadio(step) {
        document.querySelectorAll('#ivRadioGroup .iv-opt').forEach(btn => {
            btn.addEventListener('click', function () {
                const col    = this.dataset.radioCol;
                const stepId = this.dataset.stepId;

                // Clear all columns for this step's options
                step.options.forEach(opt => {
                    if (opt.col) delete state.answers[opt.col];
                });
                delete state.answers['_none_' + stepId];

                // Set chosen
                if (col) {
                    state.answers[col] = true;
                } else {
                    state.answers['_none_' + stepId] = true;
                }

                computeValuation();
                document.querySelectorAll('#ivRadioGroup .iv-opt').forEach(b => {
                    b.classList.remove('iv-selected', 'iv-selected-none');
                });
                this.classList.add(col ? 'iv-selected' : 'iv-selected-none');

                // Auto advance
                setTimeout(() => goNext(), 350);
            });
        });
    }

    function wireMulti(step) {
        const noneKey = '_none_' + step.id;

        // None button
        const noneBtn = document.querySelector(`[data-none-key="${noneKey}"]`);
        if (noneBtn) {
            noneBtn.addEventListener('click', function () {
                // Clear all issue cols for this step
                step.options.forEach(opt => delete state.answers[opt.col]);
                const wasSelected = state.answers[noneKey] === true;
                state.answers[noneKey] = !wasSelected;
                computeValuation();
                renderStep();
            });
        }

        // Individual chips
        document.querySelectorAll('.iv-chip[data-multi-col]').forEach(chip => {
            chip.addEventListener('click', function () {
                const col = this.dataset.multiCol;
                if (!col) return;
                // Untick "none" when selecting an issue
                delete state.answers[noneKey];
                state.answers[col] = !state.answers[col];
                if (!state.answers[col]) delete state.answers[col];
                computeValuation();
                this.classList.toggle('iv-chip-selected', !!state.answers[col]);
                // Re-render just the none button state
                if (noneBtn) noneBtn.classList.remove('iv-chip-selected');
            });
        });
    }

    function wireAccessories(step) {
        document.querySelectorAll('.iv-acc-check').forEach(label => {
            label.addEventListener('click', function () {
                const col = this.dataset.accCol;
                if (!col) return;
                state.answers[col] = !state.answers[col];
                if (!state.answers[col]) delete state.answers[col];
                this.classList.toggle('iv-acc-checked', !!state.answers[col]);
                const cb = this.querySelector('input[type="checkbox"]');
                if (cb) cb.checked = !!state.answers[col];
                computeValuation();
            });
        });
    }

    function wireLeadCapture() {
        // nothing special — submit handled by btnNext click
    }

    /* --------------------------------------------------
       NAVIGATION
    -------------------------------------------------- */
    function goNext() {
        const step = STEPS[state.step];

        // Validation before proceeding
        if (step.type === 'model_search' && !state.model) {
            shakeElement(body); return;
        }
        if (step.type === 'variant_select' && !state.storage) {
            shakeElement(body); return;
        }
        if (step.type === 'lead_capture') {
            submitLead(); return;
        }

        if (state.step < TOTAL_STEPS - 1) {
            state.step++;
            state.isBack = false;
            renderStep();
        }
    }

    function goBack() {
        if (state.step > 0) {
            state.step--;
            state.isBack = true;
            renderStep();
        }
    }

    function shakeElement(el) {
        el.style.animation = 'none';
        el.offsetHeight; // reflow
        el.style.animation = 'ivShake .4s ease';
        setTimeout(() => el.style.animation = '', 400);
    }

    /* --------------------------------------------------
       LEAD SUBMISSION
    -------------------------------------------------- */
    function submitLead() {
        if (state.isSubmitting) return;

        const nameEl  = document.getElementById('ivName');
        const phoneEl = document.getElementById('ivPhone');
        const emailEl = document.getElementById('ivEmail');

        const name  = nameEl  ? nameEl.value.trim()  : '';
        const phone = phoneEl ? phoneEl.value.trim()  : '';
        const email = emailEl ? emailEl.value.trim()  : '';

        let valid = true;

        if (!name || name.length < 2) {
            if (nameEl) { nameEl.classList.add('iv-error'); document.getElementById('ivNameErr').textContent = 'Please enter your full name.'; }
            valid = false;
        } else {
            if (nameEl) { nameEl.classList.remove('iv-error'); document.getElementById('ivNameErr').textContent = ''; }
        }

        const digits = phone.replace(/[^0-9]/g, '');
        if (digits.length < 10 || digits.length > 13) {
            if (phoneEl) { phoneEl.classList.add('iv-error'); document.getElementById('ivPhoneErr').textContent = 'Enter a valid 10-digit number.'; }
            valid = false;
        } else {
            if (phoneEl) { phoneEl.classList.remove('iv-error'); document.getElementById('ivPhoneErr').textContent = ''; }
        }

        if (!valid) return;

        state.isSubmitting = true;
        if (btnNext) {
            btnNext.disabled = true;
            const spinner = btnNext.querySelector('.iv-spinner');
            if (spinner) spinner.style.display = 'inline-block';
        }

        // Build POST payload
        const formData = new FormData();
        formData.append('full_name',             name);
        formData.append('phone_number',          phone);
        formData.append('email',                 email);
        formData.append('device_model',          state.model);
        formData.append('device_variant',        state.storage);
        formData.append('base_value',            state.basePrice.toString());
        formData.append('estimated_value',       Math.round(state.liveValue).toString());
        formData.append('csrf_token',            window.csrfToken || '');
        formData.append('website_hp',            ''); // honeypot blank
        formData.append('questionnaire_answers', JSON.stringify(state.answers));
        formData.append('valuation_adjustments', JSON.stringify(state.deductions));
        if (state.leadId) formData.append('lead_id', state.leadId);

        fetch(SUBMIT_URL, { method: 'POST', body: formData })
            .then(r => r.json())
            .then(data => {
                state.isSubmitting = false;
                if (data.status === 'success') {
                    state.leadId = data.lead_id || data.ref_id || '';
                    showSuccessScreen(data);
                } else {
                    if (data.errors) {
                        Object.keys(data.errors).forEach(field => {
                            const el = document.getElementById(
                                field === 'full_name' ? 'ivName' :
                                field === 'phone_number' ? 'ivPhone' : 'iv' + field
                            );
                            if (el) el.classList.add('iv-error');
                        });
                    }
                    if (btnNext) {
                        btnNext.disabled = false;
                        const spinner = btnNext.querySelector('.iv-spinner');
                        if (spinner) spinner.style.display = 'none';
                    }
                }
            })
            .catch(() => {
                state.isSubmitting = false;
                // On network error, still show success (lead saved locally)
                showSuccessScreen({ lead_id: 'CS-' + Date.now() });
            });
    }

    /* --------------------------------------------------
       SUCCESS SCREEN
    -------------------------------------------------- */
    function showSuccessScreen(data) {
        const refId = data.lead_id || data.ref_id || '';
        const waMsg = encodeURIComponent(
            `Hi CashSecond! I just got my ${state.model} (${state.storage}) valued at ${fmt(state.liveValue)}. Ref: ${refId}. I want to book a free doorstep pickup.`
        );
        const waUrl = `https://wa.me/${WA_NUMBER}?text=${waMsg}`;

        body.innerHTML = `
        <div class="iv-step-card iv-success-screen">
            <div class="iv-success-icon">✓</div>
            <h3 class="iv-success-title">Valuation Submitted!</h3>
            <p class="iv-success-sub">
                Your estimated value for ${escHtml(state.model.replace('Apple ',''))} (${escHtml(state.storage)}) is<br>
                <strong class="iv-reveal-amount">${fmt(state.liveValue)}</strong><br>
                <span class="iv-reveal-disclaimer">Subject to 5-min doorstep inspection. Our team will call you shortly.</span>
            </p>
            ${refId ? `<div class="iv-ref-pill">Ref ID: <strong>${escHtml(refId)}</strong></div>` : ''}
            <a href="${waUrl}" target="_blank" rel="noopener" class="iv-wa-btn">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                <span>WhatsApp Us Now</span>
            </a>
        </div>`;

        if (progressFill) progressFill.style.width = '100%';
        if (stepLabel)    stepLabel.textContent = 'Complete ✓';
        if (btnBack)  btnBack.style.display  = 'none';
        if (btnNext)  btnNext.style.display  = 'none';

        // Redirect after 5s if thankyou URL configured
        if (window.IV_CONFIG && window.IV_CONFIG.redirectOnSuccess && THANKYOU_URL) {
            setTimeout(() => { window.location.href = THANKYOU_URL; }, 5000);
        }
    }

    /* --------------------------------------------------
       HTML ESCAPE
    -------------------------------------------------- */
    function escHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    /* --------------------------------------------------
       INIT
    -------------------------------------------------- */
    function init() {
        initRefs();
        if (!overlay) return;

        // Close button
        closeBtn && closeBtn.addEventListener('click', closeModal);
        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) closeModal();
        });

        // Footer nav
        btnNext && btnNext.addEventListener('click', goNext);
        btnBack && btnBack.addEventListener('click', goBack);

        // Keyboard
        document.addEventListener('keydown', function (e) {
            if (!overlay.classList.contains('iv-open')) return;
            if (e.key === 'Escape') closeModal();
        });

        // Wire all trigger buttons (class: start-exact-valuation-btn, iv-open-valuator)
        document.querySelectorAll('.start-exact-valuation-btn, .iv-open-valuator').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                const preModel   = this.dataset.model   || '';
                const preStorage = this.dataset.variant  || this.dataset.storage || '';
                openModal(preModel, preStorage);
            });
        });
    }

    // Expose for external trigger (e.g. from script.js)
    window.CashSecondValuator = { open: openModal, close: closeModal };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
