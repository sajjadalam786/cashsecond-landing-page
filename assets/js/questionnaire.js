/**
 * CashSecond - Complete Unified iPhone Valuation Engine
 * 1. Open on any button
 * 2. Search Bar + iPhone Model List
 * 3. Variant & Qualification Questions (Silent background valuation)
 * 4. Contact Details (Name, Phone, Email) -> Submit
 * 5. Value Reveal Animation -> Redirects to thankyou.php -> Auto-opens WhatsApp!
 */

(function () {
    'use strict';

    // 1. COMPREHENSIVE iPHONE MODEL & VALUATION MATRIX
    const BUYBACK_CONFIG = {
        models: {
            'Apple iPhone 16 Pro Max': { maxVal: 72000, mrp: 144900, biometrics: 'Face ID', variants: ['256 GB', '512 GB', '1 TB'], img: 'assets/images/phones/iphone-16-pro.svg' },
            'Apple iPhone 16 Pro':     { maxVal: 62000, mrp: 119900, biometrics: 'Face ID', variants: ['128 GB', '256 GB', '512 GB', '1 TB'], img: 'assets/images/phones/iphone-16-pro.svg' },
            'Apple iPhone 16 Plus':    { maxVal: 52000, mrp: 89900,  biometrics: 'Face ID', variants: ['128 GB', '256 GB', '512 GB'], img: 'assets/images/phones/iphone-16.svg' },
            'Apple iPhone 16':         { maxVal: 48500, mrp: 79900,  biometrics: 'Face ID', variants: ['128 GB', '256 GB', '512 GB'], img: 'assets/images/phones/iphone-16.svg' },
            'Apple iPhone 15 Pro Max': { maxVal: 54000, mrp: 134900, biometrics: 'Face ID', variants: ['256 GB', '512 GB', '1 TB'], img: 'assets/images/phones/iphone-15-pro.svg' },
            'Apple iPhone 15 Pro':     { maxVal: 48000, mrp: 109900, biometrics: 'Face ID', variants: ['128 GB', '256 GB', '512 GB', '1 TB'], img: 'assets/images/phones/iphone-15-pro.svg' },
            'Apple iPhone 15 Plus':    { maxVal: 43000, mrp: 79900,  biometrics: 'Face ID', variants: ['128 GB', '256 GB', '512 GB'], img: 'assets/images/phones/iphone-15.svg' },
            'Apple iPhone 15':         { maxVal: 38500, mrp: 69900,  biometrics: 'Face ID', variants: ['128 GB', '256 GB', '512 GB'], img: 'assets/images/phones/iphone-15.svg' },
            'Apple iPhone 14 Pro Max': { maxVal: 46000, mrp: 129900, biometrics: 'Face ID', variants: ['128 GB', '256 GB', '512 GB', '1 TB'], img: 'assets/images/phones/iphone-14-pro.svg' },
            'Apple iPhone 14 Pro':     { maxVal: 41000, mrp: 104900, biometrics: 'Face ID', variants: ['128 GB', '256 GB', '512 GB'], img: 'assets/images/phones/iphone-14-pro.svg' },
            'Apple iPhone 14 Plus':    { maxVal: 35000, mrp: 69900,  biometrics: 'Face ID', variants: ['128 GB', '256 GB', '512 GB'], img: 'assets/images/phones/iphone-14.svg' },
            'Apple iPhone 14':         { maxVal: 32000, mrp: 59900,  biometrics: 'Face ID', variants: ['128 GB', '256 GB', '512 GB'], img: 'assets/images/phones/iphone-14.svg' },
            'Apple iPhone 13 Pro Max': { maxVal: 44000, mrp: 119900, biometrics: 'Face ID', variants: ['128 GB', '256 GB', '512 GB', '1 TB'], img: 'assets/images/phones/iphone-13.svg' },
            'Apple iPhone 13 Pro':     { maxVal: 39500, mrp: 99900,  biometrics: 'Face ID', variants: ['128 GB', '256 GB', '512 GB'], img: 'assets/images/phones/iphone-13.svg' },
            'Apple iPhone 13':         { maxVal: 23220, mrp: 49900,  biometrics: 'Face ID', variants: ['128 GB', '256 GB', '512 GB'], img: 'assets/images/phones/iphone-13.svg' },
            'Apple iPhone 13 Mini':    { maxVal: 21000, mrp: 44900,  biometrics: 'Face ID', variants: ['128 GB', '256 GB', '512 GB'], img: 'assets/images/phones/iphone-13.svg' },
            'Apple iPhone 12 Pro Max': { maxVal: 29000, mrp: 99900,  biometrics: 'Face ID', variants: ['128 GB', '256 GB', '512 GB'], img: 'assets/images/phones/iphone-12.svg' },
            'Apple iPhone 12 Pro':     { maxVal: 24500, mrp: 84900,  biometrics: 'Face ID', variants: ['128 GB', '256 GB', '512 GB'], img: 'assets/images/phones/iphone-12.svg' },
            'Apple iPhone 12':         { maxVal: 19500, mrp: 44900,  biometrics: 'Face ID', variants: ['64 GB', '128 GB', '256 GB'], img: 'assets/images/phones/iphone-12.svg' },
            'Apple iPhone 12 Mini':    { maxVal: 16500, mrp: 39900,  biometrics: 'Face ID', variants: ['64 GB', '128 GB', '256 GB'], img: 'assets/images/phones/iphone-12.svg' },
            'Apple iPhone 11 Pro Max': { maxVal: 21000, mrp: 89900,  biometrics: 'Face ID', variants: ['64 GB', '256 GB', '512 GB'], img: 'assets/images/phones/iphone-11.svg' },
            'Apple iPhone 11 Pro':     { maxVal: 18000, mrp: 79900,  biometrics: 'Face ID', variants: ['64 GB', '256 GB', '512 GB'], img: 'assets/images/phones/iphone-11.svg' },
            'Apple iPhone 11':         { maxVal: 14500, mrp: 39900,  biometrics: 'Face ID', variants: ['64 GB', '128 GB', '256 GB'], img: 'assets/images/phones/iphone-11.svg' },
            'Apple iPhone SE (2022)':  { maxVal: 13500, mrp: 39900,  biometrics: 'Touch ID', variants: ['64 GB', '128 GB', '256 GB'], img: 'assets/images/phones/iphone-13.svg' },
            'Apple iPhone SE (2020)':  { maxVal: 9500,  mrp: 34900,  biometrics: 'Touch ID', variants: ['64 GB', '128 GB', '256 GB'], img: 'assets/images/phones/iphone-12.svg' },
            'Apple iPhone XS Max':     { maxVal: 12500, mrp: 99900,  biometrics: 'Face ID', variants: ['64 GB', '256 GB', '512 GB'], img: 'assets/images/phones/iphone-11.svg' },
            'Apple iPhone XS':         { maxVal: 11000, mrp: 89900,  biometrics: 'Face ID', variants: ['64 GB', '256 GB', '512 GB'], img: 'assets/images/phones/iphone-11.svg' },
            'Apple iPhone XR':         { maxVal: 10500, mrp: 49900,  biometrics: 'Face ID', variants: ['64 GB', '128 GB'], img: 'assets/images/phones/iphone-11.svg' },
            'Apple iPhone X':          { maxVal: 9000,  mrp: 79900,  biometrics: 'Face ID', variants: ['64 GB', '256 GB'], img: 'assets/images/phones/iphone-11.svg' },
            'Apple iPhone 8 Plus':     { maxVal: 8000,  mrp: 59900,  biometrics: 'Touch ID', variants: ['64 GB', '128 GB'], img: 'assets/images/phones/iphone-11.svg' },
            'Apple iPhone 8':          { maxVal: 6500,  mrp: 49900,  biometrics: 'Touch ID', variants: ['64 GB', '128 GB'], img: 'assets/images/phones/iphone-11.svg' }
        },
        variantMultipliers: {
            '64 GB':  0.88,
            '128 GB': 1.00,
            '256 GB': 1.12,
            '512 GB': 1.25,
            '1 TB':   1.38
        }
    };

    function normalizeModelName(input) {
        if (!input) return 'Apple iPhone 13';
        const trimmed = String(input).trim();
        if (BUYBACK_CONFIG.models[trimmed]) return trimmed;
        if (!trimmed.toLowerCase().startsWith('apple')) {
            const withApple = 'Apple ' + trimmed;
            if (BUYBACK_CONFIG.models[withApple]) return withApple;
        }
        const lower = trimmed.toLowerCase();
        for (const key of Object.keys(BUYBACK_CONFIG.models)) {
            if (key.toLowerCase() === lower || key.toLowerCase().replace('apple ', '') === lower) {
                return key;
            }
        }
        return 'Apple iPhone 13';
    }

    // 2. STATE MACHINE
    let state = {
        currentStepIndex: 0,
        model: 'Apple iPhone 13',
        variant: '128 GB',
        answers: {},
        calculatedValue: 23220,
        isSubmitting: false,
        leadData: null
    };

    // 3. COMPLETE STEP DEFINITIONS
    const QUESTION_STEPS = [
        // STEP 0 — MODEL SEARCH & SELECT
        {
            stepId: 'model_search',
            cat: 'Model Selection',
            trail: 'Search & Select iPhone',
            type: 'model_search',
            title: 'Select Your iPhone Model',
            desc: 'Search or tap your iPhone model to start your accurate doorstep valuation.'
        },
        // STEP 1 — STORAGE VARIANT
        {
            stepId: 'variant',
            cat: 'Storage Capacity',
            trail: 'iPhone → Storage Variant',
            type: 'variant_select',
            title: 'Which variant is your iPhone?',
            desc: 'Select the internal storage capacity of your device.'
        },
        // STEP 2 — POWER & BOOT
        {
            stepId: 'power_on',
            cat: 'Device Condition',
            trail: 'Hardware → Power On',
            type: 'binary',
            title: 'Does your iPhone switch on properly?',
            desc: 'Check if the device powers on and boots normally to the lock/home screen.',
            yesText: 'Yes, works normally',
            noText: 'No, does not turn on',
            penaltyNo: 9000
        },
        // STEP 3 — DISPLAY LIGHTING
        {
            stepId: 'display_working',
            cat: 'Display Check',
            trail: 'Screen → Display Working',
            type: 'binary',
            title: "Is your iPhone's screen display working clearly?",
            desc: 'Check if the screen illuminates clearly without total blackout.',
            yesText: 'Yes, screen lights up',
            noText: 'No display / Blackout',
            penaltyNo: 5500
        },
        // STEP 4 — TOUCHSCREEN
        {
            stepId: 'touch_screen',
            cat: 'Display Check',
            trail: 'Screen → Touchscreen',
            type: 'binary',
            title: 'Does the touchscreen respond properly across all corners?',
            desc: 'Verify touch response without ghost touches, freezing, or dead spots.',
            yesText: 'Yes, smooth touch',
            noText: 'No, touch issues',
            penaltyNo: 3500
        },
        // STEP 5 — SCREEN FLAWS / LINES / SPOTS
        {
            stepId: 'display_flaws',
            cat: 'Display Check',
            trail: 'Screen → Lines & Spots',
            type: 'binary_reverse',
            title: 'Does the display have lines, black ink spots, or flickering?',
            desc: 'Inspect screen background on a white image for colored lines or bleeding.',
            noText: 'No, clean display',
            yesText: 'Yes, lines or spots',
            penaltyYes: 4000
        },
        // STEP 6 — SCREEN GLASS CRACKS
        {
            stepId: 'screen_cracked',
            cat: 'Physical Condition',
            trail: 'Glass → Screen Cracks',
            type: 'binary_reverse',
            title: 'Is the front screen glass cracked or chipped?',
            desc: 'Check for visible glass cracks, chips, or shattered edges.',
            noText: 'No, flawless glass',
            yesText: 'Yes, cracked screen',
            penaltyYes: 3800
        },
        // STEP 7 — SCREEN SCRATCHES
        {
            stepId: 'screen_scratches',
            cat: 'Physical Condition',
            trail: 'Glass → Scratches',
            type: 'binary_reverse',
            title: 'Does the screen have heavy, visible scratches?',
            desc: 'Visible deep scratches that can be felt with your fingernail.',
            noText: 'No, minor / no scratches',
            yesText: 'Yes, deep scratches',
            penaltyYes: 1200
        },
        // STEP 8 — BODY DENTS
        {
            stepId: 'body_dents',
            cat: 'Body Condition',
            trail: 'Body → Dents',
            type: 'binary',
            title: 'Is the phone body free from major dents or impact damage?',
            desc: 'Check metal frame and side corners for heavy drops or dents.',
            yesText: 'Yes, clean body',
            noText: 'No, has visible dents',
            penaltyNo: 1600
        },
        // STEP 9 — BODY BENT
        {
            stepId: 'body_bent',
            cat: 'Body Condition',
            trail: 'Body → Frame Bent',
            type: 'binary_reverse',
            title: 'Is the iPhone chassis or frame bent?',
            desc: 'Place the phone on a flat desk to verify there is no curvature.',
            noText: 'No, perfectly flat',
            yesText: 'Yes, frame bent',
            penaltyYes: 3000
        },
        // STEP 10 — CAMERA LENS GLASS
        {
            stepId: 'camera_glass_crack',
            cat: 'Camera Check',
            trail: 'Cameras → Lens Glass',
            type: 'binary_reverse',
            title: 'Is any rear camera lens glass cracked or damaged?',
            desc: 'Inspect protective glass covering the rear camera lenses.',
            noText: 'No, lenses intact',
            yesText: 'Yes, camera glass cracked',
            penaltyYes: 2200
        },
        // STEP 11 — BIOMETRICS & CAMERAS
        {
            stepId: 'biometrics',
            cat: 'Functional Check',
            trail: 'Hardware → Biometrics',
            type: 'biometric_dynamic',
            title: 'Do Face ID / Touch ID and front & rear cameras work?',
            desc: 'Verify that face unlocking and photo capture function properly.',
            yesText: 'Yes, works perfectly',
            noText: 'No, feature issue',
            penaltyNo: 3200
        },
        // STEP 12 — ORIGINAL BOX & INVOICE
        {
            stepId: 'accessories',
            cat: 'Accessories Check',
            trail: 'Box & Bill → Original Bill',
            type: 'binary',
            title: 'Do you have the original retail box and valid invoice?',
            desc: 'Original retail invoice and packaging adds bonus resale value.',
            yesText: 'Yes, bill & box available',
            noText: 'No, device only',
            bonusYes: 1000
        },
        // STEP 13 — USER CONTACT DETAILS & SUBMIT
        {
            stepId: 'lead_capture',
            cat: 'Lock In Valuation',
            trail: 'Contact Details → Reveal Value',
            type: 'lead_capture',
            title: 'Enter Details to Reveal Guaranteed Value',
            desc: 'Your custom resale estimate is ready! Enter your contact details to unlock your exact price and receive your official digital quote.'
        }
    ];

    // 4. VALUATION ENGINE CALCULATION (SILENT BACKGROUND)
    function computeValuation() {
        const modelData = BUYBACK_CONFIG.models[state.model] || BUYBACK_CONFIG.models['Apple iPhone 13'];
        let base = modelData.maxVal || 23220;

        const multiplier = BUYBACK_CONFIG.variantMultipliers[state.variant] || 1.0;
        base = Math.round(base * multiplier);

        let totalDeductions = 0;
        let totalBonuses = 0;

        QUESTION_STEPS.forEach(step => {
            const ans = state.answers[step.stepId];
            if (!ans) return;

            if (step.type === 'binary') {
                if (ans === step.noText || ans === 'No') {
                    totalDeductions += (step.penaltyNo || 0);
                } else if (ans === step.yesText || ans === 'Yes') {
                    totalBonuses += (step.bonusYes || 0);
                }
            } else if (step.type === 'binary_reverse') {
                if (ans === step.yesText || ans === 'Yes') {
                    totalDeductions += (step.penaltyYes || 0);
                }
            } else if (step.type === 'biometric_dynamic') {
                if (ans === 'No' || ans.startsWith('NO')) {
                    totalDeductions += (step.penaltyNo || 3200);
                }
            }
        });

        let finalVal = base - totalDeductions + totalBonuses;
        const minFloor = Math.round(modelData.mrp * 0.08) || 3500;
        if (finalVal < minFloor) finalVal = minFloor;

        state.calculatedValue = finalVal;
        return finalVal;
    }

    // 5. MODAL LIFECYCLE CONTROLLERS
    function openQuestionnaire(modelName, variantName) {
        if (modelName) {
            state.model = normalizeModelName(modelName);
        }
        if (variantName) {
            state.variant = variantName;
        }

        const overlay = document.getElementById('buybackQuestionnaireModal');
        if (!overlay) return;

        overlay.classList.add('active');
        overlay.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';

        // Start at Step 0 (Search & Model Selection)
        state.currentStepIndex = 0;
        state.answers = {};
        renderCurrentStep();
    }

    function closeQuestionnaire() {
        const overlay = document.getElementById('buybackQuestionnaireModal');
        if (!overlay) return;

        overlay.classList.remove('active');
        overlay.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    // 6. STEP RENDERER
    function renderCurrentStep() {
        const totalSteps = QUESTION_STEPS.length;
        const currentStep = state.currentStepIndex;

        if (currentStep >= totalSteps) return;

        const stepDef = QUESTION_STEPS[currentStep];

        // Header Updates
        const deviceBadge = document.getElementById('qnHeaderDeviceBadge');
        if (deviceBadge) {
            if (currentStep === 0) {
                deviceBadge.textContent = 'All Apple iPhone Models';
            } else {
                deviceBadge.textContent = `${state.model} (${state.variant})`;
            }
        }

        const progressFill = document.getElementById('qnProgressFill');
        if (progressFill) {
            const pct = Math.round(((currentStep + 1) / totalSteps) * 100);
            progressFill.style.width = `${pct}%`;
        }

        const stepTrackerText = document.getElementById('qnStepTrackerText');
        const trailText = document.getElementById('qnStepTrailText');
        if (stepTrackerText) stepTrackerText.textContent = `Step ${currentStep + 1} of ${totalSteps}`;
        if (trailText) trailText.textContent = stepDef.trail || stepDef.cat;

        // Footer Back / Next Controls
        const footer = document.getElementById('qnAppFooter');
        const backBtn = document.getElementById('qnBackBtn');
        const nextBtn = document.getElementById('qnNextBtn');

        if (footer) footer.style.display = (currentStep === 0 || currentStep === totalSteps - 1) ? 'none' : 'flex';
        if (backBtn) backBtn.style.display = (currentStep > 0) ? 'inline-flex' : 'none';
        if (nextBtn) nextBtn.style.display = 'none'; // Fast auto-advance on selection

        renderStepContent(stepDef);

        const bodyViewport = document.getElementById('qnAppBody');
        if (bodyViewport) bodyViewport.scrollTop = 0;
    }

    function renderStepContent(stepDef) {
        const container = document.getElementById('qnAppBody');
        if (!container) return;

        const currentAnswer = state.answers[stepDef.stepId];
        const modelData = BUYBACK_CONFIG.models[state.model] || BUYBACK_CONFIG.models['Apple iPhone 13'];
        let contentHtml = '';

        // STEP 0: SEARCH BAR WITH iPHONE LIST BELOW IT
        if (stepDef.type === 'model_search') {
            let modelCardsHtml = '';
            Object.keys(BUYBACK_CONFIG.models).forEach(mKey => {
                const mInfo = BUYBACK_CONFIG.models[mKey];
                const isCurrent = (mKey === state.model);
                modelCardsHtml += `
                    <div class="qn-model-pick-card ${isCurrent ? 'selected' : ''}" data-model="${mKey}">
                        <div class="qn-model-pick-img-wrap">
                            <img src="${mInfo.img || 'assets/images/phones/iphone-15.svg'}" alt="${mKey}" loading="lazy">
                        </div>
                        <div class="qn-model-pick-details">
                            <h4 class="qn-model-pick-name">${mKey}</h4>
                            <span class="qn-model-pick-price">Get Upto ₹${(mInfo.maxVal).toLocaleString('en-IN')}</span>
                        </div>
                        <span class="qn-model-pick-arrow">&rarr;</span>
                    </div>
                `;
            });

            container.innerHTML = `
                <div class="qn-model-select-step">
                    <div class="qn-search-header-box">
                        <span class="qn-question-category-tag">STEP 1: SELECT iPHONE</span>
                        <h3 class="qn-question-heading">${stepDef.title}</h3>
                        <p class="qn-question-desc">${stepDef.desc}</p>
                        
                        <!-- Search Bar -->
                        <div class="qn-search-input-wrap">
                            <svg class="qn-search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                            <input type="text" id="qnModelSearchInput" class="qn-model-search-field" placeholder="Search iPhone (e.g. 15 Pro, iPhone 14, 13...)" autocomplete="off">
                            <button type="button" id="qnModelSearchClear" class="qn-search-clear-btn" style="display:none;" aria-label="Clear search">&times;</button>
                        </div>
                    </div>

                    <!-- iPhone Model Grid / List -->
                    <div class="qn-models-grid-scroll" id="qnModelsGridContainer">
                        ${modelCardsHtml}
                    </div>
                </div>
            `;

            // Search Bar Live Filtering
            const searchInput = container.querySelector('#qnModelSearchInput');
            const searchClear = container.querySelector('#qnModelSearchClear');
            const gridContainer = container.querySelector('#qnModelsGridContainer');

            if (searchInput && gridContainer) {
                searchInput.addEventListener('input', (e) => {
                    const query = e.target.value.toLowerCase().trim();
                    if (searchClear) searchClear.style.display = query ? 'block' : 'none';

                    const cards = gridContainer.querySelectorAll('.qn-model-pick-card');
                    let visibleCount = 0;
                    cards.forEach(card => {
                        const mName = card.getAttribute('data-model').toLowerCase();
                        if (mName.includes(query) || mName.replace('apple ', '').includes(query)) {
                            card.style.display = 'flex';
                            visibleCount++;
                        } else {
                            card.style.display = 'none';
                        }
                    });

                    let emptyMsg = gridContainer.querySelector('.qn-search-empty-msg');
                    if (visibleCount === 0) {
                        if (!emptyMsg) {
                            emptyMsg = document.createElement('div');
                            emptyMsg.className = 'qn-search-empty-msg';
                            emptyMsg.innerHTML = '<p style="padding:24px; text-align:center; color:#8E8E93; font-size:0.875rem;">No matching iPhone models found. Please check spelling.</p>';
                            gridContainer.appendChild(emptyMsg);
                        }
                    } else if (emptyMsg) {
                        emptyMsg.remove();
                    }
                });

                if (searchClear) {
                    searchClear.addEventListener('click', () => {
                        searchInput.value = '';
                        searchClear.style.display = 'none';
                        searchInput.dispatchEvent(new Event('input'));
                        searchInput.focus();
                    });
                }
            }

            // Click on Model Card
            container.querySelectorAll('.qn-model-pick-card').forEach(card => {
                card.addEventListener('click', () => {
                    const mKey = card.getAttribute('data-model');
                    state.model = mKey;
                    const mData = BUYBACK_CONFIG.models[mKey];
                    state.variant = (mData && mData.variants && mData.variants.includes('128 GB')) ? '128 GB' : (mData.variants[0] || '128 GB');
                    computeValuation();
                    advanceNextStep();
                });
            });
            return;
        }

        // STEP 1: VARIANT SELECTION
        if (stepDef.type === 'variant_select') {
            const variants = modelData.variants || ['128 GB', '256 GB', '512 GB'];
            let optsHtml = '';
            variants.forEach(v => {
                const isSel = (state.variant === v);
                optsHtml += `
                    <div class="qn-option-pill-card ${isSel ? 'selected' : ''}" data-variant="${v}">
                        <div class="qn-option-title">${v}</div>
                        <div class="qn-option-sub">Internal Storage</div>
                    </div>
                `;
            });

            contentHtml = `<div class="qn-options-grid">${optsHtml}</div>`;

            container.innerHTML = `
                <div class="qn-question-view">
                    <span class="qn-question-category-tag">${stepDef.cat}</span>
                    <h3 class="qn-question-heading">${stepDef.title}</h3>
                    <p class="qn-question-desc">${stepDef.desc}</p>
                    ${contentHtml}
                </div>
            `;

            container.querySelectorAll('.qn-option-pill-card').forEach(card => {
                card.addEventListener('click', () => {
                    state.variant = card.getAttribute('data-variant');
                    computeValuation();
                    setTimeout(() => advanceNextStep(), 150);
                });
            });
            return;
        }

        // STEPS 2 to 12: QUALIFICATION QUESTIONS (YES / NO)
        if (stepDef.type === 'binary' || stepDef.type === 'binary_reverse' || stepDef.type === 'biometric_dynamic') {
            const yesLabel = stepDef.yesText || 'YES';
            const noLabel = stepDef.noText || 'NO';
            const isYesSelected = (currentAnswer === yesLabel || currentAnswer === 'Yes');
            const isNoSelected = (currentAnswer === noLabel || currentAnswer === 'No');

            contentHtml = `
                <div class="qn-yes-no-grid">
                    <div class="qn-choice-card yes-variant ${isYesSelected ? 'selected' : ''}" data-val="${yesLabel}">
                        <span class="qn-choice-icon">✓</span>
                        <h4 class="qn-choice-title">${yesLabel}</h4>
                    </div>
                    <div class="qn-choice-card no-variant ${isNoSelected ? 'selected' : ''}" data-val="${noLabel}">
                        <span class="qn-choice-icon">✕</span>
                        <h4 class="qn-choice-title">${noLabel}</h4>
                    </div>
                </div>
            `;

            container.innerHTML = `
                <div class="qn-question-view">
                    <span class="qn-question-category-tag">${stepDef.cat}</span>
                    <h3 class="qn-question-heading">${stepDef.title}</h3>
                    <p class="qn-question-desc">${stepDef.desc}</p>
                    ${contentHtml}
                </div>
            `;

            container.querySelectorAll('.qn-choice-card').forEach(card => {
                card.addEventListener('click', () => {
                    state.answers[stepDef.stepId] = card.getAttribute('data-val');
                    computeValuation();
                    setTimeout(() => advanceNextStep(), 160);
                });
            });
            return;
        }

        // STEP 13: CONTACT DETAILS FORM & HIDDEN VALUE REVEAL
        if (stepDef.type === 'lead_capture') {
            renderLeadCaptureStep(container, stepDef);
            return;
        }
    }

    // 7. STEP 13 CONTACT FORM & VALUE REVEAL SCREEN
    function renderLeadCaptureStep(container, stepDef) {
        computeValuation();

        container.innerHTML = `
            <div class="qn-contact-form-step">
                <!-- Locked Valuation Teaser Card -->
                <div class="qn-locked-valuation-box">
                    <div class="qn-lock-badge">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                        <span>Valuation Generated</span>
                    </div>
                    <h4 class="qn-locked-device-name">${state.model} (${state.variant})</h4>
                    <p class="qn-locked-desc">Enter your contact details below to send your official valuation report directly to your WhatsApp.</p>
                </div>

                <!-- Contact Form Card -->
                <div class="qn-contact-card">
                    <form id="qnBuybackLeadForm" novalidate>
                        <div class="qn-form-group">
                            <label class="qn-form-label" for="qn_cust_name">Your Full Name <span class="req">*</span></label>
                            <input type="text" id="qn_cust_name" name="full_name" class="qn-form-input" placeholder="e.g. Rahul Sharma" required autocomplete="name">
                        </div>

                        <div class="qn-form-group">
                            <label class="qn-form-label" for="qn_cust_phone">WhatsApp / Mobile Number <span class="req">*</span></label>
                            <div class="qn-phone-input-wrap">
                                <span class="qn-phone-prefix">+91</span>
                                <input type="tel" id="qn_cust_phone" name="phone_number" class="qn-form-input qn-input-with-prefix" placeholder="98200 12345" maxlength="10" pattern="[0-9]{10}" required autocomplete="tel">
                            </div>
                        </div>

                        <div class="qn-form-group">
                            <label class="qn-form-label" for="qn_cust_email">Email Address <span class="req">*</span></label>
                            <input type="email" id="qn_cust_email" name="email" class="qn-form-input" placeholder="rahul@example.com" required autocomplete="email">
                        </div>

                        <div id="qnFormErrorAlert" class="qn-alert-box" style="display:none;"></div>

                        <button type="submit" class="btn-qn-reveal-submit" id="qnSubmitRevealBtn">
                            <svg class="btn-click-icon" width="20" height="23" viewBox="0 0 24 28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <line x1="12" y1="1.5" x2="12" y2="4.5"/>
                                <line x1="6.5" y1="3.5" x2="8.6" y2="5.6"/>
                                <line x1="17.5" y1="3.5" x2="15.4" y2="5.6"/>
                                <line x1="4" y1="9" x2="7" y2="9"/>
                                <line x1="20" y1="9" x2="17" y2="9"/>
                                <path d="M10.5 13V8a1.5 1.5 0 0 1 3 0v5"/>
                                <path d="M13.5 12a1.4 1.4 0 0 1 2.8 0v2.5"/>
                                <path d="M16.3 13.5a1.4 1.4 0 0 1 2.8 0v2"/>
                                <path d="M19.1 15a1.4 1.4 0 0 1 2.8 0v3.5a6.5 6.5 0 0 1-6.5 6.5h-3a5.5 5.5 0 0 1-4.2-2L5.8 19.2a1.5 1.5 0 0 1 2.2-2.1l2.5 1.9V13"/>
                            </svg>
                            <span>Get My Valuation Quote</span>
                            <img src="assets/images/iphone-value-check-button.png" alt="iPhone" class="btn-iphone-thumb" width="22" height="38">
                        </button>
                    </form>

                    <div class="qn-form-trust-notes" id="qnFormTrustNotes">
                        <span>⚡ Free Mumbai Doorstep Pickup</span>
                        <span>•</span>
                        <span>🔒 100% Privacy Protected</span>
                    </div>
                </div>

                </div>
            </div>
        `;

        // Form Submit Handler
        const form = container.querySelector('#qnBuybackLeadForm');
        const submitBtn = container.querySelector('#qnSubmitRevealBtn');
        const errorAlert = container.querySelector('#qnFormErrorAlert');

        if (form) {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();

                const nameInput = form.querySelector('#qn_cust_name');
                const phoneInput = form.querySelector('#qn_cust_phone');
                const emailInput = form.querySelector('#qn_cust_email');

                const name = nameInput ? nameInput.value.trim() : '';
                const phone = phoneInput ? phoneInput.value.trim() : '';
                const email = emailInput ? emailInput.value.trim() : '';

                if (!name || name.length < 2) {
                    showError('Please enter your full name.');
                    if (nameInput) nameInput.focus();
                    return;
                }

                const cleanPhone = phone.replace(/[^0-9]/g, '');
                if (cleanPhone.length !== 10) {
                    showError('Please enter a valid 10-digit mobile number.');
                    if (phoneInput) phoneInput.focus();
                    return;
                }

                if (!email || !email.includes('@') || !email.includes('.')) {
                    showError('Please enter a valid email address.');
                    if (emailInput) emailInput.focus();
                    return;
                }

                hideError();

                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span>Opening Valuation...</span>';
                }

                const finalVal = computeValuation();
                const refId = 'EXG-' + (new Date().toISOString().slice(0,10).replace(/-/g,'')) + '-' + Math.floor(1000 + Math.random() * 9000);

                const formData = new FormData();
                formData.append('lead_id', refId);
                formData.append('ref_id', refId);
                formData.append('full_name', name);
                formData.append('phone_number', cleanPhone);
                formData.append('email', email);
                formData.append('address', '');
                formData.append('pincode', '');
                formData.append('device_model', state.model);
                formData.append('device_variant', state.variant);
                formData.append('estimated_value', '₹' + finalVal.toLocaleString('en-IN'));
                formData.append('questionnaire_answers', JSON.stringify(state.answers));
                formData.append('csrf_token', window.csrfToken || '');

                // Fire background request with keepalive (zero blocking, runs seamlessly in background)
                try {
                    fetch('forms/buyback-questionnaire.php', {
                        method: 'POST',
                        body: formData,
                        keepalive: true
                    }).catch(() => {});
                } catch (err) {}

                // Instantly close modal popup
                closeQuestionnaire();

                // Instantly redirect to Thank You page where price is revealed & feedback/scheduling is handled
                const thankYouUrl = `thankyou.php?model=${encodeURIComponent(state.model)}&variant=${encodeURIComponent(state.variant)}&val=${finalVal}&name=${encodeURIComponent(name)}&phone=${encodeURIComponent(cleanPhone)}&ref=${encodeURIComponent(refId)}`;

                window.location.href = thankYouUrl;
            });
        }

        function showError(msg) {
            if (errorAlert) {
                errorAlert.style.display = 'block';
                errorAlert.textContent = msg;
            }
        }

        function hideError() {
            if (errorAlert) errorAlert.style.display = 'none';
        }
    }

    function advanceNextStep() {
        if (state.currentStepIndex < QUESTION_STEPS.length - 1) {
            state.currentStepIndex++;
            renderCurrentStep();
        }
    }

    function goPreviousStep() {
        if (state.currentStepIndex > 0) {
            state.currentStepIndex--;
            renderCurrentStep();
        }
    }

    // 8. ATTACH UNIFIED TRIGGER TO ALL BUTTONS ACROSS THE SITE
    document.addEventListener('DOMContentLoaded', () => {
        // Modal Close Button
        const closeBtn = document.getElementById('qnAppCloseBtn');
        if (closeBtn) closeBtn.addEventListener('click', closeQuestionnaire);

        // Back Button
        const backBtn = document.getElementById('qnBackBtn');
        if (backBtn) backBtn.addEventListener('click', goPreviousStep);

        // Backdrop click to close
        const overlay = document.getElementById('buybackQuestionnaireModal');
        if (overlay) {
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) {
                    closeQuestionnaire();
                }
            });
        }

        // Close on Escape key
        document.addEventListener('keydown', (e) => {
            const modal = document.getElementById('buybackQuestionnaireModal');
            if (e.key === 'Escape' && modal && modal.classList.contains('active')) {
                closeQuestionnaire();
            }
        });

        // Unified Selectors for Every Button on the Site
        const allValuationTriggers = [
            '#startExactValuationBtn',
            '.start-exact-valuation-btn',
            '.btn-get-exact-value',
            '#heroCheckValueBtn',
            '#transparentValuationBtn',
            '.btn-header-quote',
            '.btn-promo-light',
            '.btn-promo-dark',
            '.promo-banner-cta',
            '#openSmartExchangeBtn',
            '.smart-exchange-open-btn',
            '#mobile-sticky-valuation-btn',
            '.iphone-pill-card',
            'a[href="#valuation"]',
            'a[href="#valuation-entry"]'
        ].join(', ');

        const buttons = document.querySelectorAll(allValuationTriggers);
        buttons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();

                const chosenModel = btn.getAttribute('data-model') || btn.getAttribute('data-name') || '';
                const chosenVariant = btn.getAttribute('data-variant') || '128 GB';

                openQuestionnaire(chosenModel, chosenVariant);
            });
        });
    });

    // Expose Global Unified API
    window.openValuationFlow = openQuestionnaire;
    window.openBuybackQuestionnaire = openQuestionnaire;
    window.openSmartExchange = openQuestionnaire;
})();
