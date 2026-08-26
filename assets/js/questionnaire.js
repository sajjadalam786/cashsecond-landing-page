/**
 * CashSecond - Complete Phone Buyback Questionnaire Application
 * Real multi-step valuation engine, question flow state machine, and lead submission
 */

(function () {
    'use strict';

    // 1. BUYBACK PRICING & MODEL MATRIX
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
            'Apple iPhone SE (2022)':  { maxVal: 13500, mrp: 39900,  biometrics: 'Touch ID', variants: ['64 GB', '128 GB', '256 GB'], img: 'assets/images/phones/iphone-13.svg' }
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

    // 2. COMPLETE QUESTIONNAIRE STEP DEFINITIONS (Steps 1 to 17)
    const QUESTION_STEPS = [
        // STEP 1 — PHONE
        {
            stepId: 'phone_intro',
            cat: 'Phone Check',
            trail: 'Phone → Model',
            type: 'phone_intro',
            title: 'Verify Your Phone Model',
            desc: 'Check your selected iPhone model and initial maximum value estimate.'
        },
        // STEP 2 — VARIANT
        {
            stepId: 'variant',
            cat: 'Variant Check',
            trail: 'Phone → Storage Variant',
            type: 'variant_select',
            title: 'Which variant is your phone?',
            desc: 'Select the internal storage capacity of your device.'
        },
        // STEP 3 — POWER / FUNCTIONALITY
        {
            stepId: 'power_on',
            cat: 'Device Check',
            trail: 'Functionality → Power On',
            type: 'binary',
            title: 'Does your phone switch on?',
            desc: 'Check if the device powers on and boots normally to the lock/home screen.',
            yesText: 'Yes, it works normally',
            noText: 'No, it does not switch on',
            penaltyNo: 9000,
            reportCat: 'device'
        },
        // STEP 4 — DISPLAY (5 sequential questions)
        {
            stepId: 'display_working',
            cat: 'Display Check',
            trail: 'Display → Working',
            type: 'binary',
            title: "Is your phone's display working properly?",
            desc: 'Check if the screen illuminates clearly without complete blackout.',
            yesText: 'Yes',
            noText: 'No',
            penaltyNo: 5000,
            reportCat: 'device'
        },
        {
            stepId: 'touch_screen',
            cat: 'Display Check',
            trail: 'Display → Touchscreen',
            type: 'binary',
            title: 'Does the touchscreen work properly across the entire screen?',
            desc: 'Verify touch response across all corners without ghost touches or dead spots.',
            yesText: 'Yes',
            noText: 'No',
            penaltyNo: 3200,
            reportCat: 'device'
        },
        {
            stepId: 'display_flaws',
            cat: 'Display Check',
            trail: 'Display → Screen Flaws',
            type: 'binary_reverse',
            title: 'Does the display have lines, black spots, dead pixels or flickering?',
            desc: 'Inspect screen background for colored lines, ink bleeding, or flickering.',
            noText: 'No',
            yesText: 'Yes',
            penaltyYes: 3800,
            reportCat: 'physical'
        },
        {
            stepId: 'screen_cracked',
            cat: 'Display Check',
            trail: 'Display → Cracks',
            type: 'binary_reverse',
            title: 'Is the screen cracked or damaged?',
            desc: 'Check for cracks, chipped glass, or shattered front panel.',
            noText: 'No',
            yesText: 'Yes',
            penaltyYes: 3500,
            reportCat: 'physical'
        },
        {
            stepId: 'screen_scratches',
            cat: 'Display Check',
            trail: 'Display → Scratches',
            type: 'binary_reverse',
            title: 'Does the screen have major scratches or chips?',
            desc: 'Deep visible scratches or glass chips around the edges.',
            noText: 'No',
            yesText: 'Yes',
            penaltyYes: 1200,
            reportCat: 'physical'
        },
        // STEP 5 — BODY (5 questions)
        {
            stepId: 'body_dents',
            cat: 'Body Check',
            trail: 'Body → Dents',
            type: 'binary',
            title: 'Is the phone body free from major dents or damage?',
            desc: 'Check side bezel, metal chassis, and rear glass for severe impact dents.',
            yesText: 'Yes',
            noText: 'No',
            penaltyNo: 1500,
            reportCat: 'physical'
        },
        {
            stepId: 'body_bent',
            cat: 'Body Check',
            trail: 'Body → Frame Bent',
            type: 'binary_reverse',
            title: 'Is the phone bent?',
            desc: 'Place the phone on a flat surface to check for curvature.',
            noText: 'No',
            yesText: 'Yes',
            penaltyYes: 2800,
            reportCat: 'physical'
        },
        {
            stepId: 'body_visible_damage',
            cat: 'Body Check',
            trail: 'Body → Visible Damage',
            type: 'binary_reverse',
            title: 'Does the phone have visible body damage?',
            desc: 'Chipped edges, back glass cracks, or panel separation.',
            noText: 'No',
            yesText: 'Yes',
            penaltyYes: 1600,
            reportCat: 'physical'
        },
        {
            stepId: 'camera_glass_crack',
            cat: 'Body Check',
            trail: 'Body → Camera Glass',
            type: 'binary_reverse',
            title: 'Is the camera glass damaged or cracked?',
            desc: 'Inspect protective glass covering the rear camera lenses.',
            noText: 'No',
            yesText: 'Yes',
            penaltyYes: 1800,
            reportCat: 'physical'
        },
        {
            stepId: 'missing_parts',
            cat: 'Body Check',
            trail: 'Body → Missing Parts',
            type: 'binary_reverse',
            title: 'Are any physical parts missing?',
            desc: 'SIM tray, volume buttons, side screws, or speaker mesh.',
            noText: 'No',
            yesText: 'Yes',
            penaltyYes: 1400,
            reportCat: 'physical'
        },
        // STEP 6 — BATTERY
        {
            stepId: 'battery_health',
            cat: 'Battery Check',
            trail: 'Battery → Health %',
            type: 'battery_select',
            title: 'What is your Battery Health?',
            desc: 'Check in Settings → Battery → Battery Health & Charging.',
            reportCat: 'device'
        },
        // STEP 7 — CAMERA (4 questions)
        {
            stepId: 'rear_camera',
            cat: 'Multimedia Check',
            trail: 'Camera → Rear Camera',
            type: 'binary',
            title: 'Is the rear camera working properly?',
            desc: 'Check photo clarity, autofocus, optical zoom, and 4K video capture.',
            yesText: 'Yes',
            noText: 'No',
            penaltyNo: 3000,
            reportCat: 'multimedia'
        },
        {
            stepId: 'front_camera',
            cat: 'Multimedia Check',
            trail: 'Camera → Front Camera',
            type: 'binary',
            title: 'Is the front camera working properly?',
            desc: 'Verify selfie camera, portrait mode, and video call clarity.',
            yesText: 'Yes',
            noText: 'No',
            penaltyNo: 2000,
            reportCat: 'multimedia'
        },
        {
            stepId: 'camera_flash',
            cat: 'Multimedia Check',
            trail: 'Camera → Flash',
            type: 'binary',
            title: 'Is the camera flash working properly?',
            desc: 'Verify True Tone LED flash and torch flashlight functionality.',
            yesText: 'Yes',
            noText: 'No',
            penaltyNo: 600,
            reportCat: 'multimedia'
        },
        {
            stepId: 'camera_lens_damage',
            cat: 'Multimedia Check',
            trail: 'Camera → Lens Damage',
            type: 'binary_reverse',
            title: 'Does the camera lens/glass have any damage?',
            desc: 'Scratches on lens surface creating blurry spots in photos.',
            noText: 'No',
            yesText: 'Yes',
            penaltyYes: 1500,
            reportCat: 'multimedia'
        },
        // STEP 8 — AUDIO (3 questions)
        {
            stepId: 'loudspeaker',
            cat: 'Multimedia Check',
            trail: 'Audio → Loudspeaker',
            type: 'binary',
            title: 'Is the loudspeaker working properly?',
            desc: 'Check bottom stereo speaker volume and sound clarity without crackling.',
            yesText: 'Yes',
            noText: 'No',
            penaltyNo: 1500,
            reportCat: 'multimedia'
        },
        {
            stepId: 'earpiece_receiver',
            cat: 'Multimedia Check',
            trail: 'Audio → Earpiece',
            type: 'binary',
            title: 'Is the earpiece/receiver working properly?',
            desc: 'Verify top receiver speaker call clarity during phone calls.',
            yesText: 'Yes',
            noText: 'No',
            penaltyNo: 1200,
            reportCat: 'multimedia'
        },
        {
            stepId: 'microphone',
            cat: 'Multimedia Check',
            trail: 'Audio → Microphone',
            type: 'binary',
            title: 'Is the microphone working properly?',
            desc: 'Check voice recording and caller audio reception.',
            yesText: 'Yes',
            noText: 'No',
            penaltyNo: 1400,
            reportCat: 'multimedia'
        },
        // STEP 9 — BUTTONS (3 questions)
        {
            stepId: 'power_button',
            cat: 'Device Check',
            trail: 'Buttons → Power/Side',
            type: 'binary',
            title: 'Is the Power/Side button working?',
            desc: 'Check click tactile feedback and screen lock response.',
            yesText: 'Yes',
            noText: 'No',
            penaltyNo: 800,
            reportCat: 'device'
        },
        {
            stepId: 'volume_buttons',
            cat: 'Device Check',
            trail: 'Buttons → Volume Up/Down',
            type: 'binary',
            title: 'Are the Volume Up/Down buttons working?',
            desc: 'Verify tactile response on Volume Up and Volume Down keys.',
            yesText: 'Yes',
            noText: 'No',
            penaltyNo: 800,
            reportCat: 'device'
        },
        {
            stepId: 'silent_switch',
            cat: 'Device Check',
            trail: 'Buttons → Silent Switch / Action',
            type: 'binary',
            title: 'Is the Silent/Ring switch working?',
            desc: 'Verify silent ring toggle switch or Action button.',
            yesText: 'Yes',
            noText: 'No',
            penaltyNo: 700,
            reportCat: 'device'
        },
        // STEP 10 — CHARGING (2 questions)
        {
            stepId: 'charging_port',
            cat: 'Device Check',
            trail: 'Charging → Port',
            type: 'binary',
            title: 'Is the charging port working properly?',
            desc: 'Lightning or USB-C port fits firmly without loose pin connectivity.',
            yesText: 'Yes',
            noText: 'No',
            penaltyNo: 1800,
            reportCat: 'device'
        },
        {
            stepId: 'charges_normally',
            cat: 'Device Check',
            trail: 'Charging → Power Flow',
            type: 'binary',
            title: 'Does the phone charge normally?',
            desc: 'Phone draws power and charges battery percentage consistently.',
            yesText: 'Yes',
            noText: 'No',
            penaltyNo: 2000,
            reportCat: 'device'
        },
        // STEP 11 — BIOMETRIC (Face ID / Touch ID)
        {
            stepId: 'biometrics',
            cat: 'Device Check',
            trail: 'Security → Biometrics',
            type: 'biometric_dynamic',
            title: 'Is Biometric Authentication working properly?',
            desc: 'Verify Face ID or Touch ID recognition.',
            reportCat: 'device'
        },
        // STEP 12 — CONNECTIVITY (4 questions)
        {
            stepId: 'wifi_working',
            cat: 'Connectivity Check',
            trail: 'Connectivity → Wi-Fi',
            type: 'binary',
            title: 'Is Wi-Fi working properly?',
            desc: 'Connects to wireless networks and maintains internet connection.',
            yesText: 'Yes',
            noText: 'No',
            penaltyNo: 1500,
            reportCat: 'connectivity'
        },
        {
            stepId: 'bluetooth_working',
            cat: 'Connectivity Check',
            trail: 'Connectivity → Bluetooth',
            type: 'binary',
            title: 'Is Bluetooth working properly?',
            desc: 'Pairs with AirPods, headphones, and Apple Watch.',
            yesText: 'Yes',
            noText: 'No',
            penaltyNo: 1200,
            reportCat: 'connectivity'
        },
        {
            stepId: 'cellular_sim',
            cat: 'Connectivity Check',
            trail: 'Connectivity → SIM / Cellular',
            type: 'binary',
            title: 'Is the mobile network/SIM working properly?',
            desc: 'Supports calling, 5G/4G cellular data, and eSIM activation.',
            yesText: 'Yes',
            noText: 'No',
            penaltyNo: 2400,
            reportCat: 'connectivity'
        },
        {
            stepId: 'gps_location',
            cat: 'Connectivity Check',
            trail: 'Connectivity → GPS / Maps',
            type: 'binary',
            title: 'Is GPS/location working properly?',
            desc: 'Location tracking on Apple Maps and Google Maps.',
            yesText: 'Yes',
            noText: 'No',
            penaltyNo: 1000,
            reportCat: 'connectivity'
        },
        // STEP 13 — LIQUID DAMAGE
        {
            stepId: 'liquid_damage',
            cat: 'Physical Check',
            trail: 'Physical → Liquid Damage',
            type: 'binary_reverse',
            title: 'Has the phone ever suffered liquid or water damage?',
            desc: 'Moisture exposure, water drop, or liquid contact indicator trigger.',
            noText: 'No',
            yesText: 'Yes',
            penaltyYes: 4500,
            reportCat: 'physical'
        },
        // STEP 14 — ORIGINAL PARTS
        {
            stepId: 'display_original',
            cat: 'Physical Check',
            trail: 'Parts → Original Screen',
            type: 'binary',
            title: 'Is the display original?',
            desc: 'Authentic Apple factory screen with True Tone.',
            yesText: 'Yes',
            noText: 'No',
            penaltyNo: 3500,
            reportCat: 'physical'
        },
        {
            stepId: 'parts_replaced',
            cat: 'Physical Check',
            trail: 'Parts → Replaced Components',
            type: 'binary_reverse',
            title: 'Has the phone ever had a major component replaced?',
            desc: 'Third-party or service replacement history.',
            noText: 'No',
            yesText: 'Yes',
            penaltyYes: 1600,
            reportCat: 'physical'
        },
        // STEP 15 — WARRANTY
        {
            stepId: 'warranty_status',
            cat: 'Physical Check',
            trail: 'Warranty → Coverage',
            type: 'multi_choice',
            title: 'Is your phone currently under warranty?',
            desc: 'Check under Settings → General → About.',
            options: [
                { val: 'YES (Under 11 Months)', bonus: 1500 },
                { val: 'NO (Above 1 Year)', bonus: 0 },
                { val: 'NOT SURE', bonus: 0 }
            ],
            reportCat: 'physical'
        },
        // STEP 16 — BILL
        {
            stepId: 'bill_invoice',
            cat: 'Physical Check',
            trail: 'Documents → Bill',
            type: 'binary',
            title: 'Do you have the original purchase bill/invoice?',
            desc: 'Original retail invoice or purchase document.',
            yesText: 'Yes',
            noText: 'No',
            bonusYes: 600,
            reportCat: 'physical'
        },
        // STEP 17 — ACCESSORIES
        {
            stepId: 'has_box',
            cat: 'Physical Check',
            trail: 'Accessories → Box',
            type: 'binary',
            title: 'Do you have the original box?',
            desc: 'Original matching IMEI box (optional).',
            yesText: 'Yes',
            noText: 'No',
            bonusYes: 600,
            reportCat: 'physical'
        },
        {
            stepId: 'has_cable',
            cat: 'Physical Check',
            trail: 'Accessories → Cable / Adapter',
            type: 'binary',
            title: 'Do you have the original charging cable/adapter?',
            desc: 'Original charging cable (optional).',
            yesText: 'Yes',
            noText: 'No',
            bonusYes: 300,
            reportCat: 'physical'
        }
    ];

    // 3. APPLICATION STATE
    const state = {
        model: 'Apple iPhone 13',
        variant: '128 GB',
        currentStepIndex: 0,
        answers: {},
        calculatedValue: 23220
    };

    // Initialize default answers
    QUESTION_STEPS.forEach(step => {
        if (step.type === 'binary') state.answers[step.stepId] = 'Yes';
        else if (step.type === 'binary_reverse') state.answers[step.stepId] = 'No';
        else if (step.type === 'battery_select') state.answers[step.stepId] = '85% – 89%';
        else if (step.type === 'biometric_dynamic') state.answers[step.stepId] = 'Yes';
        else if (step.type === 'multi_choice') state.answers[step.stepId] = 'NO (Above 1 Year)';
    });

    // 4. DYNAMIC VALUATION CALCULATOR
    function computeValuation() {
        const modelData = BUYBACK_CONFIG.models[state.model] || BUYBACK_CONFIG.models['Apple iPhone 13'];
        const mult = BUYBACK_CONFIG.variantMultipliers[state.variant] || 1.0;

        let val = Math.round(modelData.maxVal * mult);

        QUESTION_STEPS.forEach(step => {
            const ans = state.answers[step.stepId];
            if (!ans) return;

            if (step.type === 'binary' && ans === 'No') {
                val -= (step.penaltyNo || 0);
            } else if (step.type === 'binary_reverse' && ans.startsWith('Yes')) {
                val -= (step.penaltyYes || 0);
            } else if (step.type === 'battery_select') {
                if (ans === '85% – 89%') val -= 800;
                else if (ans === '80% – 84%') val -= 1800;
                else if (ans === 'Below 80%') val -= 3600;
                else if (ans === "I don't know") val -= 1200;
            } else if (step.type === 'biometric_dynamic' && ans === 'No') {
                val -= 3000;
            } else if (step.type === 'multi_choice') {
                if (ans.includes('YES')) val += 1500;
            }
            if (step.bonusYes && ans === 'Yes') {
                val += step.bonusYes;
            }
        });

        // Floor price: minimum 30% of base
        const floorPrice = Math.round(modelData.maxVal * mult * 0.30);
        val = Math.max(floorPrice, val);

        state.calculatedValue = val;
        return val;
    }

    function computeValuationAdjustments() {
        const modelData = BUYBACK_CONFIG.models[state.model] || BUYBACK_CONFIG.models['Apple iPhone 13'];
        const mult = BUYBACK_CONFIG.variantMultipliers[state.variant] || 1.0;
        const base = modelData.maxVal;
        const storageDiff = Math.round(base * (mult - 1.0));

        let battDiff = 0;
        const batt = state.answers['battery_health'] || '85% – 89%';
        if (batt === '85% – 89%') battDiff = -800;
        else if (batt === '80% – 84%') battDiff = -1800;
        else if (batt === 'Below 80%') battDiff = -3600;
        else if (batt === "I don't know") battDiff = -1200;

        let dispDiff = 0;
        if (state.answers['display_working'] === 'No') dispDiff -= 5000;
        if (state.answers['touch_screen'] === 'No') dispDiff -= 3200;
        if (state.answers['display_flaws'] && state.answers['display_flaws'].startsWith('Yes')) dispDiff -= 3800;
        if (state.answers['screen_cracked'] && state.answers['screen_cracked'].startsWith('Yes')) dispDiff -= 3500;
        if (state.answers['screen_scratches'] && state.answers['screen_scratches'].startsWith('Yes')) dispDiff -= 1200;

        let bodyDiff = 0;
        if (state.answers['body_dents'] === 'No') bodyDiff -= 1500;
        if (state.answers['body_bent'] && state.answers['body_bent'].startsWith('Yes')) bodyDiff -= 2800;
        if (state.answers['body_visible_damage'] && state.answers['body_visible_damage'].startsWith('Yes')) bodyDiff -= 1600;
        if (state.answers['camera_glass_crack'] && state.answers['camera_glass_crack'].startsWith('Yes')) bodyDiff -= 1800;
        if (state.answers['missing_parts'] && state.answers['missing_parts'].startsWith('Yes')) bodyDiff -= 1400;

        let funcDiff = 0;
        if (state.answers['power_on'] === 'No') funcDiff -= 9000;
        if (state.answers['rear_camera'] === 'No') funcDiff -= 3000;
        if (state.answers['front_camera'] === 'No') funcDiff -= 2000;
        if (state.answers['camera_flash'] === 'No') funcDiff -= 600;
        if (state.answers['loudspeaker'] === 'No') funcDiff -= 1500;
        if (state.answers['earpiece_receiver'] === 'No') funcDiff -= 1200;
        if (state.answers['microphone'] === 'No') funcDiff -= 1400;
        if (state.answers['power_button'] === 'No') funcDiff -= 800;
        if (state.answers['volume_buttons'] === 'No') funcDiff -= 800;
        if (state.answers['silent_switch'] === 'No') funcDiff -= 700;
        if (state.answers['charging_port'] === 'No') funcDiff -= 1800;
        if (state.answers['charges_normally'] === 'No') funcDiff -= 2000;
        if (state.answers['biometrics'] === 'No') funcDiff -= 3000;
        if (state.answers['wifi_working'] === 'No') funcDiff -= 1500;
        if (state.answers['bluetooth_working'] === 'No') funcDiff -= 1200;
        if (state.answers['cellular_sim'] === 'No') funcDiff -= 2400;
        if (state.answers['gps_location'] === 'No') funcDiff -= 1000;

        let liquidDiff = (state.answers['liquid_damage'] && state.answers['liquid_damage'].startsWith('Yes')) ? -4500 : 0;
        let partsDiff = (state.answers['display_original'] === 'No' ? -3500 : 0) + ((state.answers['parts_replaced'] && state.answers['parts_replaced'].startsWith('Yes')) ? -1600 : 0);
        let warrantyDiff = (state.answers['warranty_status'] && state.answers['warranty_status'].includes('YES')) ? 1500 : 0;
        let accDiff = (state.answers['bill_invoice'] === 'Yes' ? 600 : 0) + (state.answers['has_box'] === 'Yes' ? 600 : 0) + (state.answers['has_cable'] === 'Yes' ? 300 : 0);

        const fmt = (n) => (n >= 0 ? `+₹${n.toLocaleString('en-IN')}` : `-₹${Math.abs(n).toLocaleString('en-IN')}`);
        const total = storageDiff + battDiff + dispDiff + bodyDiff + funcDiff + liquidDiff + partsDiff + warrantyDiff + accDiff;

        return {
            base_price: `₹${base.toLocaleString('en-IN')}`,
            storage: fmt(storageDiff),
            battery: fmt(battDiff),
            display: fmt(dispDiff),
            body: fmt(bodyDiff),
            functional: fmt(funcDiff),
            liquid: fmt(liquidDiff),
            parts: fmt(partsDiff),
            warranty: fmt(warrantyDiff),
            accessories: fmt(accDiff),
            total_adjustment: fmt(total)
        };
    }

    // 5. POPUP OPEN / CLOSE & STEP NAVIGATION
    function openQuestionnaire(selectedModel, selectedVariant) {
        state.answers = {};
        state.calculatedValue = 0;
        state.deductions = [];

        const normalized = normalizeModelName(selectedModel);
        if (normalized && BUYBACK_CONFIG.models[normalized]) {
            state.model = normalized;
        }

        const modelData = BUYBACK_CONFIG.models[state.model] || BUYBACK_CONFIG.models['Apple iPhone 13'];
        if (selectedVariant && modelData.variants && modelData.variants.includes(selectedVariant)) {
            state.variant = selectedVariant;
        } else if (modelData.variants && modelData.variants.length > 0) {
            state.variant = modelData.variants.includes('128 GB') ? '128 GB' : modelData.variants[0];
        }

        const overlay = document.getElementById('buybackQuestionnaireModal');
        if (!overlay) return;

        overlay.classList.add('active');
        overlay.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';

        state.currentStepIndex = 0;
        renderCurrentStep();
    }

    function closeQuestionnaire() {
        const overlay = document.getElementById('buybackQuestionnaireModal');
        if (!overlay) return;

        overlay.classList.remove('active');
        overlay.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    function renderCurrentStep() {
        const totalSteps = QUESTION_STEPS.length;
        const currentStep = state.currentStepIndex;

        // Header Updates
        const deviceBadge = document.getElementById('qnHeaderDeviceBadge');
        if (deviceBadge) deviceBadge.textContent = `${state.model} (${state.variant})`;

        const progressFill = document.getElementById('qnProgressFill');
        if (progressFill) {
            const pct = Math.round(((currentStep + 1) / (totalSteps + 1)) * 100);
            progressFill.style.width = `${pct}%`;
        }

        const stepTrackerText = document.getElementById('qnStepTrackerText');
        const trailText = document.getElementById('qnStepTrailText');

        if (currentStep < totalSteps) {
            const stepDef = QUESTION_STEPS[currentStep];
            if (stepTrackerText) stepTrackerText.textContent = `Step ${currentStep + 1} of ${totalSteps}`;
            if (trailText) trailText.textContent = stepDef.cat;

            renderQuestionCard(stepDef);

            const footer = document.getElementById('qnAppFooter');
            if (footer) footer.style.display = (stepDef.type === 'phone_intro') ? 'none' : 'flex';

            const backBtn = document.getElementById('qnBackBtn');
            if (backBtn) backBtn.style.visibility = (currentStep === 0) ? 'hidden' : 'visible';

            const nextBtn = document.getElementById('qnNextBtn');
            if (nextBtn) {
                nextBtn.style.display = 'none'; // Auto-advance on answer selection
            }
        } else {
            // FINAL DEVICE REPORT & VALUE SCREEN INSIDE THE SAME POPUP
            renderFinalResultScreen();
        }

        // Scroll body viewport to top
        const bodyViewport = document.getElementById('qnAppBody');
        if (bodyViewport) bodyViewport.scrollTop = 0;
    }

    function renderQuestionCard(stepDef) {
        const container = document.getElementById('qnAppBody');
        if (!container) return;

        const currentAnswer = state.answers[stepDef.stepId];
        const modelData = BUYBACK_CONFIG.models[state.model] || BUYBACK_CONFIG.models['Apple iPhone 13'];
        let contentHtml = '';

        if (stepDef.type === 'phone_intro') {
            // STEP 1 — PHONE INTRO CARD INSIDE POPUP WITH MODEL SELECTOR
            let modelOptionsHtml = '';
            Object.keys(BUYBACK_CONFIG.models).forEach(m => {
                const isSelected = (m === state.model);
                modelOptionsHtml += `<option value="${m}" ${isSelected ? 'selected' : ''}>${m}</option>`;
            });

            contentHtml = `
                <div class="qn-intro-phone-card" style="text-align:center; padding:18px 14px;">
                    <div style="margin:0 auto 14px auto; display:inline-flex; align-items:center; gap:8px; background:#F2F2F7; border:1px solid #E5E5EA; border-radius:12px; padding:6px 12px; max-width:100%; box-sizing:border-box;">
                        <span style="font-size:0.75rem; color:#636366; font-weight:700; text-transform:uppercase;">Device:</span>
                        <select id="qnModelSelectDropdown" style="font-size:0.875rem; font-weight:700; color:#1C1C1E; border:none; background:transparent; outline:none; cursor:pointer; max-width:210px;">
                            ${modelOptionsHtml}
                        </select>
                    </div>
                    <div style="width:96px; height:120px; margin:0 auto 12px auto; display:flex; align-items:center; justify-content:center; background:#F2F2F7; border-radius:18px; padding:10px;">
                        <img src="${modelData.img || 'assets/images/phones/iphone-13.svg'}" alt="${state.model}" style="max-width:100%; max-height:100%; object-fit:contain;">
                    </div>
                    <h3 style="font-size:1.35rem; font-weight:800; color:#1C1C1E; margin:0 0 4px 0;">${state.model} (${state.variant})</h3>
                    <div style="display:inline-flex; align-items:center; gap:8px; margin:6px 0 14px 0;">
                        <span style="font-size:0.875rem; color:#636366; font-weight:600;">Get Upto</span>
                        <span style="font-size:1.75rem; font-weight:800; color:#0071E3;">₹${(modelData.maxVal).toLocaleString('en-IN')}</span>
                    </div>
                    <p style="font-size:0.8125rem; color:#8E8E93; margin:0 0 20px 0;">Free doorstep pickup in Mumbai • Instant spot payment</p>
                    <button type="button" class="btn-get-exact-value" id="qnStartValuationBtn" style="width:100%; justify-content:center; padding:14px; font-size:1rem; border-radius:14px;">
                        <span>Check Your iPhone Value</span>
                        <img src="assets/images/iphone-value-check-button.png" alt="iPhone" class="btn-iphone-thumb" width="22" height="38" loading="eager">
                    </button>
                </div>
            `;
        } else if (stepDef.type === 'variant_select') {
            const variants = modelData.variants || ['128 GB', '256 GB', '512 GB'];
            let optsHtml = '';
            variants.forEach(v => {
                const isSel = (state.variant === v);
                optsHtml += `
                    <div class="qn-option-pill-card ${isSel ? 'selected' : ''}" data-variant="${v}">
                        <div class="qn-option-title">${v}</div>
                        <div class="qn-option-sub">Storage Variant</div>
                    </div>
                `;
            });
            contentHtml = `<div class="qn-options-grid">${optsHtml}</div>`;
        } else if (stepDef.type === 'binary' || stepDef.type === 'binary_reverse') {
            const yesLabel = stepDef.yesText || 'YES';
            const noLabel = stepDef.noText || 'NO';

            const isYesSelected = (currentAnswer === 'Yes' || currentAnswer === yesLabel);
            const isNoSelected = (currentAnswer === 'No' || currentAnswer === noLabel);

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
        } else if (stepDef.type === 'battery_select') {
            const presets = ['90% – 100%', '85% – 89%', '80% – 84%', 'Below 80%', "I don't know"];
            let phtml = '';
            presets.forEach(p => {
                const isSel = (currentAnswer === p);
                phtml += `
                    <div class="qn-option-pill-card ${isSel ? 'selected' : ''}" data-val="${p}">
                        <div class="qn-option-title">${p}</div>
                    </div>
                `;
            });
            contentHtml = `<div class="qn-options-grid">${phtml}</div>`;
        } else if (stepDef.type === 'biometric_dynamic') {
            const bioName = modelData.biometrics || 'Face ID';
            const isYes = (currentAnswer === 'Yes');

            contentHtml = `
                <div class="qn-yes-no-grid">
                    <div class="qn-choice-card yes-variant ${isYes ? 'selected' : ''}" data-val="Yes">
                        <span class="qn-choice-icon">✓</span>
                        <h4 class="qn-choice-title">YES, ${bioName} Works</h4>
                    </div>
                    <div class="qn-choice-card no-variant ${!isYes ? 'selected' : ''}" data-val="No">
                        <span class="qn-choice-icon">✕</span>
                        <h4 class="qn-choice-title">NO, ${bioName} Issue</h4>
                    </div>
                </div>
            `;
        } else if (stepDef.type === 'multi_choice') {
            let mhtml = '';
            stepDef.options.forEach(opt => {
                const isSel = (currentAnswer === opt.val);
                mhtml += `
                    <div class="qn-option-pill-card ${isSel ? 'selected' : ''}" data-val="${opt.val}" style="padding: 16px 14px;">
                        <div class="qn-option-title">${opt.val}</div>
                    </div>
                `;
            });
            contentHtml = `<div class="qn-options-grid" style="grid-template-columns: 1fr;">${mhtml}</div>`;
        }

        container.innerHTML = `
            <div class="qn-question-view">
                <span class="qn-question-category-tag">${stepDef.cat}</span>
                <h3 class="qn-question-heading">${stepDef.title}</h3>
                <p class="qn-question-desc">${stepDef.desc}</p>
                ${contentHtml}
            </div>
        `;

        // Click Bindings for Auto-Advancing
        if (stepDef.type === 'phone_intro') {
            const startBtn = container.querySelector('#qnStartValuationBtn');
            if (startBtn) {
                startBtn.addEventListener('click', () => {
                    advanceNextStep();
                });
            }
            const modelDropdown = container.querySelector('#qnModelSelectDropdown');
            if (modelDropdown) {
                modelDropdown.addEventListener('change', (e) => {
                    const newModel = normalizeModelName(e.target.value);
                    if (BUYBACK_CONFIG.models[newModel]) {
                        state.model = newModel;
                        const newModelData = BUYBACK_CONFIG.models[newModel];
                        state.variant = (newModelData.variants && newModelData.variants.includes(state.variant))
                            ? state.variant
                            : (newModelData.variants ? newModelData.variants[0] : '128 GB');
                        renderCurrentStep();
                    }
                });
            }
        } else if (stepDef.type === 'variant_select') {
            container.querySelectorAll('.qn-option-pill-card').forEach(card => {
                card.addEventListener('click', () => {
                    const v = card.getAttribute('data-variant');
                    state.variant = v;
                    computeValuation();
                    setTimeout(() => advanceNextStep(), 150);
                });
            });
        } else {
            container.querySelectorAll('.qn-choice-card, .qn-option-pill-card').forEach(card => {
                card.addEventListener('click', () => {
                    const val = card.getAttribute('data-val');
                    state.answers[stepDef.stepId] = val;
                    computeValuation();
                    setTimeout(() => advanceNextStep(), 180);
                });
            });
        }
    }

    function advanceNextStep() {
        if (state.currentStepIndex < QUESTION_STEPS.length) {
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

    // 6. FINAL REPORT & VALUE RENDERING INSIDE THE SAME POPUP
    function renderFinalResultScreen() {
        const container = document.getElementById('qnAppBody');
        if (!container) return;

        const val = computeValuation();
        const formattedVal = '₹' + val.toLocaleString('en-IN');

        const stepTrackerText = document.getElementById('qnStepTrackerText');
        const trailText = document.getElementById('qnStepTrailText');
        if (stepTrackerText) stepTrackerText.textContent = 'Completed';
        if (trailText) trailText.textContent = 'Device Report & Valuation';

        const footer = document.getElementById('qnAppFooter');
        if (footer) footer.style.display = 'none';

        // Calculate Category Breakdown & Passed / Failed stats
        let physPass = 0, physFail = 0;
        let devPass = 0, devFail = 0;
        let multiPass = 0, multiFail = 0;
        let connPass = 0, connFail = 0;

        QUESTION_STEPS.forEach(s => {
            if (s.type === 'phone_intro' || s.type === 'variant_select') return;

            const ans = state.answers[s.stepId] || 'Yes';
            const isPassed = (!ans.startsWith('No') && !ans.startsWith('Yes (Has') && !ans.startsWith('Yes (Cracked') && !ans.startsWith('Yes (Bent') && !ans.startsWith('Yes (Missing') && !ans.startsWith('Yes (Liquid') && !ans.includes('NO (Above') && ans !== 'No');

            if (s.reportCat === 'physical') {
                if (isPassed) physPass++; else physFail++;
            } else if (s.reportCat === 'device') {
                if (isPassed) devPass++; else devFail++;
            } else if (s.reportCat === 'multimedia') {
                if (isPassed) multiPass++; else multiFail++;
            } else if (s.reportCat === 'connectivity') {
                if (isPassed) connPass++; else connFail++;
            }
        });

        const totalPassed = physPass + devPass + multiPass + connPass;
        const totalFailed = physFail + devFail + multiFail + connFail;
        const batteryAns = state.answers['battery_health'] || '89%';

        container.innerHTML = `
            <div class="qn-result-view">
                <!-- 1. YOUR DEVICE REPORT -->
                <div class="qn-device-report-card" style="background:#FAFAFC; border:1.5px solid #E5E5EA; border-radius:20px; padding:20px 18px; box-sizing:border-box;">
                    <div style="font-size:0.71875rem; font-weight:700; color:#0071E3; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:2px;">DIAGNOSTIC SUMMARY</div>
                    <h2 style="font-size:1.35rem; font-weight:800; color:#1C1C1E; margin:0 0 2px 0;">Your Device Report</h2>
                    <p style="font-size:0.84375rem; color:#636366; margin:0 0 12px 0;"><strong>${state.model}</strong> • ${state.variant} • Battery Health: ${batteryAns}</p>

                    <div style="background:#FFFFFF; border:1px solid #E5E5EA; border-radius:14px; padding:12px 14px; display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;">
                        <span style="font-size:0.875rem; font-weight:700; color:#1C1C1E;">Test Summary</span>
                        <div style="display:flex; gap:6px;">
                            <span style="background:#E8F5E9; color:#1E8E3E; font-size:0.75rem; font-weight:700; padding:3px 8px; border-radius:8px;">${totalPassed} Passed</span>
                            ${totalFailed > 0 ? `<span style="background:#FFEBEE; color:#D32F2F; font-size:0.75rem; font-weight:700; padding:3px 8px; border-radius:8px;">${totalFailed} Failed</span>` : ''}
                        </div>
                    </div>

                    <!-- Expandable Category Summary Cards -->
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
                        <div style="background:#FFFFFF; border:1px solid #E5E5EA; border-radius:12px; padding:10px 12px;">
                            <div style="font-size:0.75rem; font-weight:700; color:#1C1C1E;">Physical</div>
                            <div style="font-size:0.6875rem; color:#636366; margin-top:2px;">${physPass} Passed | ${physFail} Failed</div>
                        </div>
                        <div style="background:#FFFFFF; border:1px solid #E5E5EA; border-radius:12px; padding:10px 12px;">
                            <div style="font-size:0.75rem; font-weight:700; color:#1C1C1E;">Device Check</div>
                            <div style="font-size:0.6875rem; color:#636366; margin-top:2px;">${devPass} Passed | ${devFail} Failed</div>
                        </div>
                        <div style="background:#FFFFFF; border:1px solid #E5E5EA; border-radius:12px; padding:10px 12px;">
                            <div style="font-size:0.75rem; font-weight:700; color:#1C1C1E;">Multimedia</div>
                            <div style="font-size:0.6875rem; color:#636366; margin-top:2px;">${multiPass} Passed | ${multiFail} Failed</div>
                        </div>
                        <div style="background:#FFFFFF; border:1px solid #E5E5EA; border-radius:12px; padding:10px 12px;">
                            <div style="font-size:0.75rem; font-weight:700; color:#1C1C1E;">Connectivity</div>
                            <div style="font-size:0.6875rem; color:#636366; margin-top:2px;">${connPass} Passed | ${connFail} Failed</div>
                        </div>
                    </div>
                </div>

                <!-- 2. YOUR ESTIMATED EXCHANGE VALUE -->
                <div class="qn-result-hero-card" style="background:linear-gradient(135deg, #0071E3, #004DB3); border-radius:20px; padding:22px 18px; color:#FFFFFF; text-align:center;">
                    <span style="font-size:0.71875rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; opacity:0.9;">YOUR ESTIMATED EXCHANGE VALUE</span>
                    <h1 class="qn-result-price-amount" style="font-size:2.6rem; font-weight:800; margin:6px 0; color:#FFFFFF; line-height:1;">${formattedVal}</h1>
                    <p style="font-size:0.78125rem; opacity:0.85; margin:0;">Based on the complete condition answers provided</p>
                </div>

                <!-- 3. FINAL CUSTOMER FORM — SELL YOUR PHONE -->
                <div class="qn-pickup-form-card" style="background:#FFFFFF; border:1.5px solid #E5E5EA; border-radius:20px; padding:20px 18px;">
                    <h3 style="font-size:1.15rem; font-weight:800; color:#1C1C1E; margin:0 0 4px 0;">Sell Your Phone</h3>
                    <p style="font-size:0.8125rem; color:#636366; margin:0 0 14px 0;">Provide your pickup details to lock in your calculated exchange value.</p>

                    <form id="buybackLeadForm" novalidate>
                        <div class="qn-form-grid" style="display:flex; flex-direction:column; gap:10px;">
                            <div class="qn-form-group">
                                <label class="qn-form-label" for="qn_lead_name">Full Name <span style="color:red;">*</span></label>
                                <input type="text" id="qn_lead_name" name="full_name" class="qn-form-input" placeholder="e.g. Rahul Sharma" required>
                            </div>

                            <div class="qn-form-row-2" style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                                <div class="qn-form-group">
                                    <label class="qn-form-label" for="qn_lead_phone">WhatsApp Number <span style="color:red;">*</span></label>
                                    <input type="tel" id="qn_lead_phone" name="phone_number" class="qn-form-input" placeholder="98200 12345" pattern="[0-9]{10}" required>
                                </div>
                                <div class="qn-form-group">
                                    <label class="qn-form-label" for="qn_lead_email">Email Address</label>
                                    <input type="email" id="qn_lead_email" name="email" class="qn-form-input" placeholder="rahul@example.com">
                                </div>
                            </div>

                            <div class="qn-form-row-2" style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                                <div class="qn-form-group">
                                    <label class="qn-form-label" for="qn_lead_address">Pickup Address <span style="color:red;">*</span></label>
                                    <input type="text" id="qn_lead_address" name="address" class="qn-form-input" placeholder="Flat No, Building, Street..." required>
                                </div>
                                <div class="qn-form-group">
                                    <label class="qn-form-label" for="qn_lead_pincode">Pincode <span style="color:red;">*</span></label>
                                    <input type="text" id="qn_lead_pincode" name="pincode" class="qn-form-input" placeholder="400021" maxlength="6" pattern="[0-9]{6}" required>
                                </div>
                            </div>
                        </div>

                        <div id="qnFormStatusAlert" style="display:none; margin-top:12px;"></div>

                        <div style="margin-top:14px;">
                            <button type="submit" class="btn-qn-next" id="qnSubmitLeadBtn" style="width:100%; padding:13px; font-size:0.9375rem; border-radius:12px;">
                                <span>Submit Details &rarr;</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- 4. NOTE / DISCLAIMER -->
                <p class="qn-disclaimer-note" style="margin:10px 0 0 0; text-align:center; font-size:0.75rem; color:#8E8E93; line-height:1.4;">
                    ⚠️ <strong>Note:</strong> Estimated value is based on the information provided and may be revised after physical verification of the device.
                </p>

                <!-- Confirmation Success Message -->
                <div id="qnSuccessBox" style="display:none; flex-direction:column; align-items:center; text-align:center; padding:20px 14px; background:#F2F2F7; border-radius:16px; margin-top:14px;">
                    <div style="width:48px; height:48px; border-radius:50%; background:#34C759; color:#FFFFFF; display:flex; align-items:center; justify-content:center; font-size:1.5rem; margin-bottom:10px;">✓</div>
                    <h4 style="font-size:1.15rem; font-weight:800; color:#1C1C1E; margin:0 0 4px 0;">Details Submitted Successfully!</h4>
                    <p style="font-size:0.8125rem; color:#636366; margin:0 0 8px 0;">Reference ID: <strong id="qnBookingRefId" style="color:#0071E3;">CS-VAL-88910</strong></p>
                    <p style="font-size:0.75rem; color:#8E8E93; line-height:1.4;">Our executive will connect with you shortly for device inspection.</p>
                </div>
            </div>
        `;

        // Attach form submission for lead capture
        const form = document.getElementById('buybackLeadForm');
        if (form) {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const submitBtn = document.getElementById('qnSubmitLeadBtn');
                const statusAlert = document.getElementById('qnFormStatusAlert');

                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span>Submitting...</span>';
                }

                const formData = new FormData(form);
                formData.append('device_model', state.model);
                formData.append('device_variant', state.variant);
                formData.append('estimated_value', '₹' + state.calculatedValue.toLocaleString('en-IN'));
                formData.append('questionnaire_answers', JSON.stringify(state.answers));
                formData.append('valuation_adjustments', JSON.stringify(computeValuationAdjustments()));
                formData.append('csrf_token', window.csrfToken || '');

                try {
                    const response = await fetch('forms/buyback-questionnaire.php', {
                        method: 'POST',
                        body: formData
                    });

                    const result = await response.json();

                    if (response.ok && result.status === 'success') {
                        form.style.display = 'none';
                        const successBox = document.getElementById('qnSuccessBox');
                        if (successBox) {
                            successBox.style.display = 'flex';
                            const refElem = document.getElementById('qnBookingRefId');
                            if (refElem) refElem.textContent = result.lead_id || result.ref_id || 'EXG-20260825-001';
                        }
                    } else {
                        if (statusAlert) {
                            statusAlert.style.display = 'block';
                            statusAlert.style.backgroundColor = '#FFEBEE';
                            statusAlert.style.color = '#E53935';
                            statusAlert.style.border = '1px solid #FFCDD2';
                            statusAlert.style.padding = '10px 14px';
                            statusAlert.style.borderRadius = '10px';
                            statusAlert.innerHTML = `<strong>Notice:</strong> ${result.message || "We couldn't save your request right now. Please try again."}`;
                        }
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = '<span>Submit Details &rarr;</span>';
                        }
                    }
                } catch (err) {
                    if (statusAlert) {
                        statusAlert.style.display = 'block';
                        statusAlert.style.backgroundColor = '#FFEBEE';
                        statusAlert.style.color = '#E53935';
                        statusAlert.style.border = '1px solid #FFCDD2';
                        statusAlert.style.padding = '10px 14px';
                        statusAlert.style.borderRadius = '10px';
                        statusAlert.innerHTML = "<strong>Notice:</strong> We couldn't save your request right now. Please try again.";
                    }
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<span>Retry Submission &rarr;</span>';
                    }
                }
            });
        }
    }

    // 7. INITIALIZE EVENT LISTENERS
    document.addEventListener('DOMContentLoaded', () => {
        // Close Button
        const closeBtn = document.getElementById('qnAppCloseBtn');
        if (closeBtn) closeBtn.addEventListener('click', closeQuestionnaire);

        // Back Button
        const backBtn = document.getElementById('qnBackBtn');
        if (backBtn) backBtn.addEventListener('click', goPreviousStep);

        // Next Button
        const nextBtn = document.getElementById('qnNextBtn');
        if (nextBtn) nextBtn.addEventListener('click', advanceNextStep);

        // Triggers for all valuation buttons on landing page
        const triggerSelector = [
            '#startExactValuationBtn',
            '.start-exact-valuation-btn',
            '#heroCheckValueBtn',
            '#transparentValuationBtn',
            '.btn-promo-light',
            '.btn-promo-dark',
            '.promo-banner-cta',
            '#openSmartExchangeBtn',
            '.smart-exchange-open-btn',
            '#mobile-sticky-valuation-btn',
            '.iphone-pill-card',
            'a[href="#valuation"]'
        ].join(', ');

        const startBtns = document.querySelectorAll(triggerSelector);
        startBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const rawModel = btn.getAttribute('data-name') || btn.getAttribute('data-model') || state.model || 'Apple iPhone 13';
                const model = normalizeModelName(rawModel);
                const variant = btn.getAttribute('data-variant') || '128 GB';
                openQuestionnaire(model, variant);
            });
        });

        // Backdrop click to close (clicking outside modal dialog)
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
            const overlay = document.getElementById('buybackQuestionnaireModal');
            if (e.key === 'Escape' && overlay && overlay.classList.contains('active')) {
                closeQuestionnaire();
            }
        });
    });

    // Expose unified global API
    window.openValuationFlow = openQuestionnaire;
    window.openBuybackQuestionnaire = openQuestionnaire;
    window.openSmartExchange = openQuestionnaire;
})();
