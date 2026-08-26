/**
 * CashSecond - Real Smart Phone Exchange & Device Check System
 * Interactive Valuation Engine, WebRTC Camera/Microphone Diagnostics, Web Audio Tester
 */

(function () {
    'use strict';

    // 1. CONFIGURABLE VALUATION PRICING ENGINE
    const VALUATION_CONFIG = {
        models: {
            'iPhone 16 Pro Max': { base: 68000, mrp: 144900, icon: '📱' },
            'iPhone 16 Pro':     { base: 58500, mrp: 119900, icon: '📱' },
            'iPhone 16 Plus':    { base: 52000, mrp: 89900,  icon: '📱' },
            'iPhone 16':         { base: 48000, mrp: 79900,  icon: '📱' },
            'iPhone 15 Pro Max': { base: 54000, mrp: 134900, icon: '📱' },
            'iPhone 15 Pro':     { base: 49000, mrp: 109900, icon: '📱' },
            'iPhone 15 Plus':    { base: 43000, mrp: 79900,  icon: '📱' },
            'iPhone 15':         { base: 39000, mrp: 69900,  icon: '📱' },
            'iPhone 14 Pro Max': { base: 47000, mrp: 129900, icon: '📱' },
            'iPhone 14 Pro':     { base: 42000, mrp: 104900, icon: '📱' },
            'iPhone 14 Plus':    { base: 36000, mrp: 69900,  icon: '📱' },
            'iPhone 14':         { base: 33000, mrp: 59900,  icon: '📱' },
            'iPhone 13 Pro Max': { base: 46000, mrp: 119900, icon: '📱' },
            'iPhone 13 Pro':     { base: 42500, mrp: 99900,  icon: '📱' },
            'iPhone 13':         { base: 27500, mrp: 49900,  icon: '📱' },
            'iPhone 12 Pro Max': { base: 31000, mrp: 99900,  icon: '📱' },
            'iPhone 12 Pro':     { base: 26000, mrp: 84900,  icon: '📱' },
            'iPhone 12':         { base: 20500, mrp: 44900,  icon: '📱' },
            'iPhone 11 Pro':     { base: 19000, mrp: 79900,  icon: '📱' },
            'iPhone 11':         { base: 15500, mrp: 39900,  icon: '📱' }
        },
        storageMultipliers: {
            '64 GB':  0.92,
            '128 GB': 1.00,
            '256 GB': 1.10,
            '512 GB': 1.22,
            '1 TB':   1.35
        },
        batteryPenalties: {
            '90%+':      0,
            '85–89%':    1000,
            '80–84%':    2200,
            'Below 80%': 4500
        }
    };

    // 2. DIAGNOSTIC TESTS & QUESTIONS DEFINITION
    const DIAGNOSTIC_TESTS = {
        physical: [
            { id: 'screen_damage', label: 'Screen Condition', question: 'Is the screen free from major cracks or glass damage?', penalty: 4000, defaultPass: true },
            { id: 'display_orig',  label: 'Display Originality', question: 'Is the display authentic Apple original?', penalty: 3500, defaultPass: true },
            { id: 'body_dents',    label: 'Body Housing', question: 'Is the device free from deep dents or metal chips?', penalty: 1500, defaultPass: true },
            { id: 'body_bend',     label: 'Frame Straightness', question: 'Is the device body straight and unbent?', penalty: 3000, defaultPass: true },
            { id: 'screen_scratches', label: 'Screen Glass', question: 'Is the screen free from heavy scratches?', penalty: 1200, defaultPass: true },
            { id: 'camera_glass',  label: 'Camera Glass', question: 'Is the rear camera glass clean and scratch-free?', penalty: 1800, defaultPass: true },
            { id: 'missing_parts', label: 'Complete Parts', question: 'Are all body screws and buttons intact?', penalty: 1500, defaultPass: true },
            { id: 'warranty',      label: 'Brand Warranty', question: 'Is your phone still under active Apple warranty?', penalty: -1500, defaultPass: false }, // Bonus if yes
            { id: 'brand_box',     label: 'Original Box', question: 'Do you have the original Apple box & matching IMEI?', penalty: -800, defaultPass: true } // Bonus if yes
        ],
        hardware: [
            { id: 'proximity_sensor', label: 'Proximity Sensor', desc: 'Cover top speaker with hand during test.', penalty: 1500, defaultPass: true, type: 'guided' },
            { id: 'face_id',         label: 'Face ID / Biometrics', desc: 'Verify TrueDepth Face ID recognition.', penalty: 3800, defaultPass: true, type: 'guided' },
            { id: 'charging_port',   label: 'Charging Port', desc: 'Lightning / USB-C fast charging connection.', penalty: 2000, defaultPass: true, type: 'guided' },
            { id: 'volume_up',       label: 'Volume Up Button', desc: 'Click Volume Up button.', penalty: 800, defaultPass: true, type: 'guided' },
            { id: 'volume_down',     label: 'Volume Down Button', desc: 'Click Volume Down button.', penalty: 800, defaultPass: true, type: 'guided' },
            { id: 'power_button',    label: 'Side / Power Button', desc: 'Click Side sleep/wake button.', penalty: 900, defaultPass: true, type: 'guided' },
            { id: 'battery_perf',    label: 'Battery Performance', desc: 'Normal power delivery without shutdowns.', penalty: 2500, defaultPass: true, type: 'guided' }
        ],
        multimedia: [
            { id: 'speaker_test',   label: 'Loudspeaker', desc: 'Play stereo audio chime to test speaker.', penalty: 1800, defaultPass: true, type: 'audio_speaker' },
            { id: 'mic_test',       label: 'Microphone', desc: 'Record audio sample to check mic input.', penalty: 1600, defaultPass: true, type: 'audio_mic' },
            { id: 'receiver_test',  label: 'Ear Receiver', desc: 'Verify ear speaker call audio clarity.', penalty: 1200, defaultPass: true, type: 'guided' },
            { id: 'back_camera',    label: 'Rear Main Camera', desc: 'Open back camera preview & check focus.', penalty: 3500, defaultPass: true, type: 'camera_back' },
            { id: 'front_camera',   label: 'Front Selfie Camera', desc: 'Open front selfie camera preview.', penalty: 2200, defaultPass: true, type: 'camera_front' },
            { id: 'screen_pixels',  label: 'Display Pixel Check', desc: 'Inspect full-screen color canvas for dead pixels.', penalty: 3000, defaultPass: true, type: 'pixel_check' }
        ],
        connectivity: [
            { id: 'wifi_test',      label: 'Wi-Fi Connection', desc: 'Check Wi-Fi network connectivity.', penalty: 1500, defaultPass: true, type: 'wifi' },
            { id: 'bluetooth_test', label: 'Bluetooth Wireless', desc: 'Verify Bluetooth radio connection.', penalty: 1200, defaultPass: true, type: 'bluetooth' },
            { id: 'gps_test',       label: 'GPS & Location', desc: 'Verify location navigation sensor.', penalty: 1000, defaultPass: true, type: 'gps' },
            { id: 'sim_network',    label: 'Cellular SIM / 5G', desc: 'Verify cellular network and SIM reception.', penalty: 2500, defaultPass: true, type: 'guided' }
        ]
    };

    // 3. APPLICATION STATE
    const state = {
        brand: 'Apple',
        model: 'iPhone 13 Pro',
        storage: '128 GB',
        batteryPreset: '85–89%',
        batteryHealth: 89,
        currentStep: 1,
        testResults: {},
        activeCameraStream: null,
        activeAudioContext: null,
        activeMicStream: null
    };

    // Initialize all default test answers
    Object.keys(DIAGNOSTIC_TESTS).forEach(category => {
        DIAGNOSTIC_TESTS[category].forEach(test => {
            state.testResults[test.id] = test.defaultPass;
        });
    });

    // 4. DOM ELEMENTS
    let overlay, appContainer, closeBtn, navBackBtn, navNextBtn, progressFill;

    function initElements() {
        overlay = document.getElementById('smartExchangeApp');
        if (!overlay) return false;

        appContainer = overlay.querySelector('.smart-app-container');
        closeBtn = overlay.querySelector('.smart-app-close-btn');
        navBackBtn = document.getElementById('smartAppBackBtn');
        navNextBtn = document.getElementById('smartAppNextBtn');
        progressFill = document.getElementById('smartProgressFill');

        return true;
    }

    // 5. STEP ROUTER & NAVIGATION
    function goToStep(stepNumber) {
        if (stepNumber < 1 || stepNumber > 5) return;
        state.currentStep = stepNumber;

        // Hide all step views
        const allSteps = document.querySelectorAll('.smart-step-view');
        allSteps.forEach(el => el.classList.remove('active'));

        // Show current step view
        const currentView = document.getElementById(`smartStep${stepNumber}`);
        if (currentView) currentView.classList.add('active');

        // Update Progress Bar & Stepper Indicators
        if (progressFill) {
            progressFill.style.width = `${(stepNumber / 5) * 100}%`;
        }

        const stepNodes = document.querySelectorAll('.smart-step-nav .step-node');
        stepNodes.forEach((node, idx) => {
            node.classList.toggle('active', idx + 1 === stepNumber);
        });

        // Update Nav Buttons
        if (navBackBtn) {
            navBackBtn.style.visibility = (stepNumber === 1) ? 'hidden' : 'visible';
        }

        if (navNextBtn) {
            if (stepNumber === 4) {
                navNextBtn.innerHTML = '<span>Generate Valuation Report &rarr;</span>';
            } else if (stepNumber === 5) {
                navNextBtn.style.display = 'none'; // Replaced by Lead Form CTA
            } else {
                navNextBtn.style.display = 'inline-flex';
                navNextBtn.innerHTML = '<span>Continue to Next Step &rarr;</span>';
            }
        }

        // Trigger dynamic rendering based on step
        if (stepNumber === 2) renderPhysicalQuestions();
        if (stepNumber === 3) renderHardwareTests();
        if (stepNumber === 4) renderMultimediaTests();
        if (stepNumber === 5) renderFinalReport();

        updateLiveValuationBar();

        // Scroll viewport to top
        const viewport = overlay.querySelector('.smart-app-viewport');
        if (viewport) viewport.scrollTop = 0;
    }

    // 6. VALUATION CALCULATOR
    function calculateValuation() {
        const modelData = VALUATION_CONFIG.models[state.model] || { base: 35000, mrp: 79900 };
        const storageMult = VALUATION_CONFIG.storageMultipliers[state.storage] || 1.0;
        
        let val = Math.round(modelData.base * storageMult);

        // Deduct battery penalty
        const battPenalty = VALUATION_CONFIG.batteryPenalties[state.batteryPreset] || 0;
        val -= battPenalty;

        // Deduct/Bonus for each test
        Object.keys(DIAGNOSTIC_TESTS).forEach(category => {
            DIAGNOSTIC_TESTS[category].forEach(test => {
                const passed = state.testResults[test.id];
                if (!passed && test.penalty > 0) {
                    val -= test.penalty; // Failure penalty
                } else if (passed && test.penalty < 0) {
                    val += Math.abs(test.penalty); // Positive bonus
                }
            });
        });

        // Minimum floor protection (at least 32% of base)
        const floorPrice = Math.round(modelData.base * storageMult * 0.32);
        val = Math.max(floorPrice, val);

        state.estimatedPrice = val;
        return val;
    }

    function updateLiveValuationBar() {
        const val = calculateValuation();
        const formatted = '₹' + val.toLocaleString('en-IN');

        const liveValBars = document.querySelectorAll('.live-val-price');
        liveValBars.forEach(el => el.textContent = formatted);

        const liveDeviceLabels = document.querySelectorAll('.live-val-device');
        liveDeviceLabels.forEach(el => el.textContent = `${state.model} (${state.storage})`);

        updateCategoryCounters();
    }

    function updateCategoryCounters() {
        let totalPass = 0;
        let totalFail = 0;

        Object.keys(DIAGNOSTIC_TESTS).forEach(category => {
            let catPass = 0;
            let catFail = 0;

            DIAGNOSTIC_TESTS[category].forEach(test => {
                if (state.testResults[test.id]) {
                    catPass++;
                    totalPass++;
                } else {
                    catFail++;
                    totalFail++;
                }
            });

            const pill = document.getElementById(`liveCount_${category}`);
            if (pill) {
                pill.innerHTML = `<span class="badge-pass">${catPass} Pass</span> | <span class="badge-fail">${catFail} Fail</span>`;
            }
        });

        const totalPassedEls = document.querySelectorAll('.live-total-passed');
        totalPassedEls.forEach(el => el.textContent = totalPass);

        const totalFailedEls = document.querySelectorAll('.live-total-failed');
        totalFailedEls.forEach(el => el.textContent = totalFail);
    }

    // 7. RENDER STEP 2: PHYSICAL CONDITION QUESTIONS
    function renderPhysicalQuestions() {
        const container = document.getElementById('physicalQuestionsContainer');
        if (!container) return;

        container.innerHTML = '';
        DIAGNOSTIC_TESTS.physical.forEach(test => {
            const isYes = state.testResults[test.id] === true;
            const card = document.createElement('div');
            card.className = 'question-card-item';
            card.innerHTML = `
                <div class="question-card-info">
                    <span class="question-cat-tag">${test.label}</span>
                    <p class="question-text">${test.question}</p>
                </div>
                <div class="question-btn-group">
                    <button type="button" class="choice-btn yes ${isYes ? 'selected' : ''}" data-id="${test.id}" data-val="true">YES</button>
                    <button type="button" class="choice-btn no ${!isYes ? 'selected' : ''}" data-id="${test.id}" data-val="false">NO</button>
                </div>
            `;
            container.appendChild(card);
        });

        // Add event listeners for YES/NO buttons
        container.querySelectorAll('.choice-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const testId = btn.getAttribute('data-id');
                const isVal = btn.getAttribute('data-val') === 'true';
                state.testResults[testId] = isVal;

                // Update UI selection on card
                const parentGroup = btn.closest('.question-btn-group');
                parentGroup.querySelectorAll('.choice-btn').forEach(b => b.classList.remove('selected'));
                btn.classList.add('selected');

                updateLiveValuationBar();
            });
        });
    }

    // 8. RENDER STEP 3: HARDWARE DIAGNOSTICS
    function renderHardwareTests() {
        const container = document.getElementById('hardwareTestsContainer');
        if (!container) return;

        container.innerHTML = '';
        DIAGNOSTIC_TESTS.hardware.forEach(test => {
            const isPass = state.testResults[test.id] === true;
            const card = document.createElement('div');
            card.className = 'hardware-test-card';
            card.innerHTML = `
                <div class="test-card-top">
                    <div class="test-card-title-wrap">
                        <span class="test-card-icon">⚙️</span>
                        <span class="test-card-name">${test.label}</span>
                    </div>
                    <div class="question-btn-group">
                        <button type="button" class="choice-btn yes ${isPass ? 'selected' : ''}" data-id="${test.id}" data-val="true">✓ Working</button>
                        <button type="button" class="choice-btn no ${!isPass ? 'selected' : ''}" data-id="${test.id}" data-val="false">✕ Issue</button>
                    </div>
                </div>
                <div class="test-action-bar">
                    <span class="test-instr-note">${test.desc}</span>
                </div>
            `;
            container.appendChild(card);
        });

        container.querySelectorAll('.choice-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const testId = btn.getAttribute('data-id');
                const isVal = btn.getAttribute('data-val') === 'true';
                state.testResults[testId] = isVal;

                const parentGroup = btn.closest('.question-btn-group');
                parentGroup.querySelectorAll('.choice-btn').forEach(b => b.classList.remove('selected'));
                btn.classList.add('selected');

                updateLiveValuationBar();
            });
        });
    }

    // 9. RENDER STEP 4: MULTIMEDIA & CONNECTIVITY REAL HARDWARE TESTS
    function renderMultimediaTests() {
        const container = document.getElementById('multimediaTestsContainer');
        if (!container) return;

        container.innerHTML = '';

        // Combine multimedia and connectivity
        const allTests = [
            ...DIAGNOSTIC_TESTS.multimedia.map(t => ({ ...t, cat: 'multimedia' })),
            ...DIAGNOSTIC_TESTS.connectivity.map(t => ({ ...t, cat: 'connectivity' }))
        ];

        allTests.forEach(test => {
            const isPass = state.testResults[test.id] === true;
            let actionBtnHtml = '';

            if (test.type === 'audio_speaker') {
                actionBtnHtml = `<button type="button" class="run-test-trigger-btn" data-action="test_speaker"><span>🔊 Play Sound</span></button>`;
            } else if (test.type === 'audio_mic') {
                actionBtnHtml = `<button type="button" class="run-test-trigger-btn" data-action="test_mic"><span>🎤 Test Mic</span></button>`;
            } else if (test.type === 'camera_back') {
                actionBtnHtml = `<button type="button" class="run-test-trigger-btn" data-action="test_camera_back"><span>📷 Open Camera</span></button>`;
            } else if (test.type === 'camera_front') {
                actionBtnHtml = `<button type="button" class="run-test-trigger-btn" data-action="test_camera_front"><span>🤳 Open Selfie</span></button>`;
            } else if (test.type === 'pixel_check') {
                actionBtnHtml = `<button type="button" class="run-test-trigger-btn" data-action="test_pixels"><span>🎨 Pixel Check</span></button>`;
            } else if (test.type === 'wifi') {
                const online = navigator.onLine;
                actionBtnHtml = `<span style="font-size:0.75rem; font-weight:700; color:${online ? '#34C759' : '#FF3B30'};">${online ? '✓ Network Connected' : 'Offline'}</span>`;
            }

            const card = document.createElement('div');
            card.className = 'hardware-test-card';
            card.innerHTML = `
                <div class="test-card-top">
                    <div class="test-card-title-wrap">
                        <span class="test-card-icon">${getTestIcon(test.type)}</span>
                        <span class="test-card-name">${test.label}</span>
                    </div>
                    <div class="question-btn-group">
                        <button type="button" class="choice-btn yes ${isPass ? 'selected' : ''}" data-id="${test.id}" data-val="true">✓ Pass</button>
                        <button type="button" class="choice-btn no ${!isPass ? 'selected' : ''}" data-id="${test.id}" data-val="false">✕ Fail</button>
                    </div>
                </div>
                <div class="test-action-bar">
                    <span class="test-instr-note">${test.desc}</span>
                    <div>${actionBtnHtml}</div>
                </div>
            `;
            container.appendChild(card);
        });

        // Event listeners for pass/fail buttons
        container.querySelectorAll('.choice-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const testId = btn.getAttribute('data-id');
                const isVal = btn.getAttribute('data-val') === 'true';
                state.testResults[testId] = isVal;

                const parentGroup = btn.closest('.question-btn-group');
                parentGroup.querySelectorAll('.choice-btn').forEach(b => b.classList.remove('selected'));
                btn.classList.add('selected');

                updateLiveValuationBar();
            });
        });

        // Trigger buttons for real interactive hardware APIs
        container.querySelectorAll('.run-test-trigger-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const action = btn.getAttribute('data-action');
                if (action === 'test_speaker') playTestSound();
                if (action === 'test_mic') startMicTest();
                if (action === 'test_camera_back') openCameraTest('environment');
                if (action === 'test_camera_front') openCameraTest('user');
                if (action === 'test_pixels') startPixelScreenTest();
            });
        });
    }

    function getTestIcon(type) {
        if (type === 'audio_speaker') return '🔊';
        if (type === 'audio_mic') return '🎤';
        if (type === 'camera_back') return '📷';
        if (type === 'camera_front') return '🤳';
        if (type === 'pixel_check') return '🎨';
        if (type === 'wifi') return '📶';
        if (type === 'bluetooth') return '🔵';
        if (type === 'gps') return '📍';
        return '⚙️';
    }

    // 10. REAL HARDWARE INTERACTIVE ENGINES (Web Audio, WebRTC, Fullscreen Canvas)

    // A. Web Audio API Speaker Test (Synthesized 880Hz Double Chime)
    function playTestSound() {
        try {
            const AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (!AudioCtx) {
                alert('Audio test sound played! Please verify you can hear audio clearly.');
                return;
            }
            const ctx = new AudioCtx();
            const now = ctx.currentTime;

            // Beep 1
            const osc1 = ctx.createOscillator();
            const gain1 = ctx.createGain();
            osc1.type = 'sine';
            osc1.frequency.setValueAtTime(880, now);
            gain1.gain.setValueAtTime(0.3, now);
            gain1.gain.exponentialRampToValueAtTime(0.01, now + 0.35);
            osc1.connect(gain1);
            gain1.connect(ctx.destination);
            osc1.start(now);
            osc1.stop(now + 0.35);

            // Beep 2
            const osc2 = ctx.createOscillator();
            const gain2 = ctx.createGain();
            osc2.type = 'sine';
            osc2.frequency.setValueAtTime(1320, now + 0.25);
            gain2.gain.setValueAtTime(0.35, now + 0.25);
            gain2.gain.exponentialRampToValueAtTime(0.01, now + 0.65);
            osc2.connect(gain2);
            gain2.connect(ctx.destination);
            osc2.start(now + 0.25);
            osc2.stop(now + 0.65);
        } catch (e) {
            console.warn('Web Audio error:', e);
        }
    }

    // B. Real WebRTC Microphone Stream & Visualizer
    function startMicTest() {
        const modal = document.getElementById('micTestSubmodal');
        if (!modal) return;
        modal.classList.add('active');

        const meterFill = document.getElementById('micMeterFill');
        const statusText = document.getElementById('micStatusText');

        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ audio: true })
                .then(stream => {
                    state.activeMicStream = stream;
                    const AudioCtx = window.AudioContext || window.webkitAudioContext;
                    const ctx = new AudioCtx();
                    const analyser = ctx.createAnalyser();
                    const source = ctx.createMediaStreamSource(stream);
                    source.connect(analyser);
                    analyser.fftSize = 256;
                    const bufferLength = analyser.frequencyBinCount;
                    const dataArray = new Uint8Array(bufferLength);

                    if (statusText) statusText.textContent = '🎙️ Listening... Please speak into your microphone.';

                    function checkVolume() {
                        if (!state.activeMicStream) return;
                        analyser.getByteFrequencyData(dataArray);
                        let sum = 0;
                        for (let i = 0; i < bufferLength; i++) sum += dataArray[i];
                        const average = sum / bufferLength;
                        const percent = Math.min(100, Math.round((average / 128) * 100));
                        if (meterFill) meterFill.style.width = `${percent}%`;
                        requestAnimationFrame(checkVolume);
                    }
                    checkVolume();
                })
                .catch(err => {
                    if (statusText) statusText.textContent = 'Please confirm if your microphone records voice notes normally.';
                });
        }
    }

    function closeMicTest(isPass) {
        state.testResults['mic_test'] = isPass;
        if (state.activeMicStream) {
            state.activeMicStream.getTracks().forEach(t => t.stop());
            state.activeMicStream = null;
        }
        const modal = document.getElementById('micTestSubmodal');
        if (modal) modal.classList.remove('active');
        renderMultimediaTests();
        updateLiveValuationBar();
    }

    // C. Real WebRTC Camera Test (Environment & Front Selfie)
    function openCameraTest(facingMode) {
        const modal = document.getElementById('cameraTestSubmodal');
        const video = document.getElementById('cameraPreviewVideo');
        const title = document.getElementById('cameraSubmodalTitle');
        if (!modal || !video) return;

        modal.classList.add('active');
        if (title) {
            title.textContent = (facingMode === 'user') ? 'Front Selfie Camera Test' : 'Rear Main Camera Test';
        }

        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ video: { facingMode: facingMode } })
                .then(stream => {
                    state.activeCameraStream = stream;
                    video.srcObject = stream;
                    video.play();
                })
                .catch(err => {
                    video.poster = '';
                    if (title) title.textContent = 'Camera Verification (Guided Check)';
                });
        }
    }

    function closeCameraTest(isPass) {
        const title = document.getElementById('cameraSubmodalTitle');
        const isFront = title && title.textContent.includes('Front');
        const testId = isFront ? 'front_camera' : 'back_camera';
        state.testResults[testId] = isPass;

        if (state.activeCameraStream) {
            state.activeCameraStream.getTracks().forEach(t => t.stop());
            state.activeCameraStream = null;
        }

        const video = document.getElementById('cameraPreviewVideo');
        if (video) video.srcObject = null;

        const modal = document.getElementById('cameraTestSubmodal');
        if (modal) modal.classList.remove('active');

        renderMultimediaTests();
        updateLiveValuationBar();
    }

    // D. Interactive Full-Screen Pixel Check Canvas
    const PIXEL_COLORS = ['#FFFFFF', '#FF0000', '#00FF00', '#0000FF', '#000000'];
    let pixelColorIndex = 0;

    function startPixelScreenTest() {
        const view = document.getElementById('pixelTestCanvasView');
        if (!view) return;
        pixelColorIndex = 0;
        view.style.backgroundColor = PIXEL_COLORS[0];
        view.classList.add('active');
    }

    function nextPixelColor() {
        const view = document.getElementById('pixelTestCanvasView');
        if (!view) return;
        pixelColorIndex = (pixelColorIndex + 1) % PIXEL_COLORS.length;
        view.style.backgroundColor = PIXEL_COLORS[pixelColorIndex];
    }

    function closePixelScreenTest(isPass) {
        state.testResults['screen_pixels'] = isPass;
        const view = document.getElementById('pixelTestCanvasView');
        if (view) view.classList.remove('active');
        renderMultimediaTests();
        updateLiveValuationBar();
    }

    // 11. RENDER STEP 5: FINAL REPORT & LEAD FORM
    function renderFinalReport() {
        const val = calculateValuation();
        const formattedVal = '₹' + val.toLocaleString('en-IN');

        const reportDeviceName = document.getElementById('reportFinalDeviceName');
        const reportSpecs = document.getElementById('reportFinalSpecs');
        const reportPrice = document.getElementById('reportFinalPrice');
        const reportPassedCount = document.getElementById('reportFinalPassedCount');
        const reportFailedCount = document.getElementById('reportFinalFailedCount');

        if (reportDeviceName) reportDeviceName.textContent = `${state.brand} ${state.model}`;
        if (reportSpecs) reportSpecs.textContent = `${state.storage} • Battery Health: ${state.batteryHealth}%`;
        if (reportPrice) reportPrice.textContent = formattedVal;

        // Calculate counts
        let totalPass = 0;
        let totalFail = 0;
        Object.keys(DIAGNOSTIC_TESTS).forEach(category => {
            DIAGNOSTIC_TESTS[category].forEach(t => {
                if (state.testResults[t.id]) totalPass++;
                else totalFail++;
            });
        });

        if (reportPassedCount) reportPassedCount.textContent = `${totalPass} Passed`;
        if (reportFailedCount) reportFailedCount.textContent = `${totalFail} Failed`;

        // Render Expandable Category Breakdown Accordions
        const breakdownContainer = document.getElementById('reportBreakdownList');
        if (breakdownContainer) {
            breakdownContainer.innerHTML = '';
            const catNames = {
                physical: 'Physical Condition',
                hardware: 'Device Functionality',
                multimedia: 'Multimedia Hardware',
                connectivity: 'Wireless & Connectivity'
            };

            Object.keys(DIAGNOSTIC_TESTS).forEach(catKey => {
                const tests = DIAGNOSTIC_TESTS[catKey];
                let pCount = 0;
                let fCount = 0;
                tests.forEach(t => {
                    if (state.testResults[t.id]) pCount++;
                    else fCount++;
                });

                let rowsHtml = '';
                tests.forEach(t => {
                    const pass = state.testResults[t.id];
                    rowsHtml += `
                        <div class="report-test-row">
                            <span>${t.label}</span>
                            <span class="status-badge ${pass ? 'pass' : 'fail'}">${pass ? '✓ PASS' : '✕ FAIL'}</span>
                        </div>
                    `;
                });

                const acc = document.createElement('div');
                acc.className = 'report-cat-accordion';
                acc.innerHTML = `
                    <button type="button" class="report-cat-summary-btn">
                        <span>${catNames[catKey]}</span>
                        <span class="counters"><strong style="color:#34C759;">${pCount} Pass</strong> | <strong style="color:#FF3B30;">${fCount} Fail</strong> ▼</span>
                    </button>
                    <div class="report-cat-details">
                        ${rowsHtml}
                    </div>
                `;

                acc.querySelector('.report-cat-summary-btn').addEventListener('click', () => {
                    acc.classList.toggle('open');
                });

                breakdownContainer.appendChild(acc);
            });
        }

        // WhatsApp Button Link
        const waBtn = document.getElementById('reportWhatsAppBtn');
        if (waBtn) {
            const waMsg = encodeURIComponent(`Hi CashSecond! I completed the Smart Exchange device check on my ${state.brand} ${state.model} (${state.storage}, Battery: ${state.batteryHealth}%).\n\nDiagnostic Result: ${totalPass} Passed, ${totalFail} Failed.\nEstimated Exchange Value: ${formattedVal}.\n\nPlease schedule my free doorstep inspection & instant payment pickup.`);
            waBtn.href = `https://wa.me/918976332211?text=${waMsg}`;
        }
    }

    // 12. LEAD SUBMISSION HANDLER
    function setupLeadSubmission() {
        const form = document.getElementById('smartExchangeLeadForm');
        if (!form) return;

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const submitBtn = document.getElementById('smartExchangeSubmitBtn');
            const statusAlert = document.getElementById('smartExchangeFormStatus');

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span>Scheduling Free Doorstep Pickup...</span>';
            }

            if (statusAlert) {
                statusAlert.style.display = 'none';
                statusAlert.className = 'form-status-alert';
            }

            const formData = new FormData(form);
            formData.append('device_brand', state.brand);
            formData.append('device_model', state.model);
            formData.append('device_storage', state.storage);
            formData.append('device_battery', `${state.batteryHealth}% (${state.batteryPreset})`);
            formData.append('estimated_value', '₹' + state.estimatedPrice.toLocaleString('en-IN'));

            let totalPass = 0;
            let totalFail = 0;
            Object.keys(DIAGNOSTIC_TESTS).forEach(category => {
                DIAGNOSTIC_TESTS[category].forEach(t => {
                    if (state.testResults[t.id]) totalPass++;
                    else totalFail++;
                });
            });

            formData.append('total_passed', totalPass);
            formData.append('total_failed', totalFail);
            formData.append('diagnostics_json', JSON.stringify(state.testResults));

            try {
                const response = await fetch('forms/smart-exchange.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (response.ok && result.status === 'success') {
                    // Show Success Screen View
                    const successView = document.getElementById('smartExchangeSuccessView');
                    const formView = document.getElementById('smartExchangeFormView');
                    if (formView) formView.style.display = 'none';
                    if (successView) {
                        successView.style.display = 'flex';
                        const refElem = document.getElementById('smartBookingRefId');
                        if (refElem) refElem.textContent = result.ref_id || 'CS-EX-88910';
                    }
                } else {
                    if (statusAlert) {
                        statusAlert.className = 'form-status-alert alert-danger';
                        statusAlert.style.display = 'block';
                        statusAlert.style.backgroundColor = '#FFEBEE';
                        statusAlert.style.color = '#E53935';
                        statusAlert.style.border = '1px solid #FFCDD2';
                        statusAlert.style.padding = '10px 14px';
                        statusAlert.style.borderRadius = '10px';
                        statusAlert.style.marginTop = '10px';
                        statusAlert.innerHTML = `<strong>Error:</strong> ${result.message || 'Please check your information and try again.'}`;
                    }
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<span>Request Free Pickup &rarr;</span>';
                    }
                }
            } catch (err) {
                if (statusAlert) {
                    statusAlert.className = 'form-status-alert alert-danger';
                    statusAlert.style.display = 'block';
                    statusAlert.style.backgroundColor = '#FFEBEE';
                    statusAlert.style.color = '#E53935';
                    statusAlert.style.border = '1px solid #FFCDD2';
                    statusAlert.style.padding = '10px 14px';
                    statusAlert.style.borderRadius = '10px';
                    statusAlert.style.marginTop = '10px';
                    statusAlert.innerHTML = '<strong>Connection Error:</strong> Could not submit. Please check connection or contact us on WhatsApp.';
                }
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<span>Request Free Pickup &rarr;</span>';
                }
            }
        });
    }

    // 13. OPEN / CLOSE APP MODAL CONTROLLERS
    function openSmartExchangeApp() {
        if (!initElements()) return;
        overlay.classList.add('active');
        overlay.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        goToStep(1);
    }

    function closeSmartExchangeApp() {
        if (!overlay) return;
        overlay.classList.remove('active');
        overlay.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';

        // Stop any active streams
        if (state.activeCameraStream) {
            state.activeCameraStream.getTracks().forEach(t => t.stop());
            state.activeCameraStream = null;
        }
        if (state.activeMicStream) {
            state.activeMicStream.getTracks().forEach(t => t.stop());
            state.activeMicStream = null;
        }
    }

    // 14. EVENT LISTENERS INITIALIZER
    document.addEventListener('DOMContentLoaded', () => {
        if (!initElements()) return;

        // Triggers across the website
        const triggerBtns = document.querySelectorAll('#heroCheckValueBtn, #navCheckValueBtn, #openSmartExchangeBtn, .open-smart-exchange-trigger');
        triggerBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                openSmartExchangeApp();
            });
        });

        if (closeBtn) closeBtn.addEventListener('click', closeSmartExchangeApp);

        // Step Back / Next Navigation
        if (navBackBtn) {
            navBackBtn.addEventListener('click', () => {
                if (state.currentStep > 1) goToStep(state.currentStep - 1);
            });
        }

        if (navNextBtn) {
            navNextBtn.addEventListener('click', () => {
                if (state.currentStep < 5) goToStep(state.currentStep + 1);
            });
        }

        // Step 1: Model Selection Cards
        const modelCards = document.querySelectorAll('.device-card-opt');
        modelCards.forEach(card => {
            card.addEventListener('click', () => {
                modelCards.forEach(c => c.classList.remove('selected'));
                card.classList.add('selected');
                state.model = card.getAttribute('data-model');
                updateLiveValuationBar();
            });
        });

        // Step 1: Storage Pills
        const storagePills = document.querySelectorAll('.storage-pill-btn');
        storagePills.forEach(pill => {
            pill.addEventListener('click', () => {
                storagePills.forEach(p => p.classList.remove('selected'));
                pill.classList.add('selected');
                state.storage = pill.getAttribute('data-storage');
                updateLiveValuationBar();
            });
        });

        // Step 1: Battery Presets & Slider
        const batteryPills = document.querySelectorAll('.battery-preset-btn');
        const batterySlider = document.getElementById('smartBatterySlider');
        const batterySliderVal = document.getElementById('smartBatterySliderVal');

        batteryPills.forEach(pill => {
            pill.addEventListener('click', () => {
                batteryPills.forEach(p => p.classList.remove('selected'));
                pill.classList.add('selected');
                state.batteryPreset = pill.getAttribute('data-preset');
                const defaultHealth = parseInt(pill.getAttribute('data-health') || '89', 10);
                state.batteryHealth = defaultHealth;

                if (batterySlider) batterySlider.value = defaultHealth;
                if (batterySliderVal) batterySliderVal.textContent = `${defaultHealth}%`;

                updateLiveValuationBar();
            });
        });

        if (batterySlider) {
            batterySlider.addEventListener('input', () => {
                const val = parseInt(batterySlider.value, 10);
                state.batteryHealth = val;
                if (batterySliderVal) batterySliderVal.textContent = `${val}%`;

                if (val >= 90) state.batteryPreset = '90%+';
                else if (val >= 85) state.batteryPreset = '85–89%';
                else if (val >= 80) state.batteryPreset = '80–84%';
                else state.batteryPreset = 'Below 80%';

                batteryPills.forEach(p => {
                    p.classList.toggle('selected', p.getAttribute('data-preset') === state.batteryPreset);
                });

                updateLiveValuationBar();
            });
        }

        // Submodal Close Controls (Mic, Camera, Pixel Canvas)
        const micPassBtn = document.getElementById('micPassBtn');
        const micFailBtn = document.getElementById('micFailBtn');
        if (micPassBtn) micPassBtn.addEventListener('click', () => closeMicTest(true));
        if (micFailBtn) micFailBtn.addEventListener('click', () => closeMicTest(false));

        const camPassBtn = document.getElementById('cameraPassBtn');
        const camFailBtn = document.getElementById('cameraFailBtn');
        if (camPassBtn) camPassBtn.addEventListener('click', () => closeCameraTest(true));
        if (camFailBtn) camFailBtn.addEventListener('click', () => closeCameraTest(false));

        const pixelCanvas = document.getElementById('pixelTestCanvasView');
        if (pixelCanvas) {
            pixelCanvas.addEventListener('click', (e) => {
                if (!e.target.closest('.pixel-test-overlay-controls')) {
                    nextPixelColor();
                }
            });
        }
        const pixelPassBtn = document.getElementById('pixelPassBtn');
        const pixelFailBtn = document.getElementById('pixelFailBtn');
        if (pixelPassBtn) pixelPassBtn.addEventListener('click', (e) => { e.stopPropagation(); closePixelScreenTest(true); });
        if (pixelFailBtn) pixelFailBtn.addEventListener('click', (e) => { e.stopPropagation(); closePixelScreenTest(false); });

        // Close on ESC key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && overlay && overlay.classList.contains('active')) {
                closeSmartExchangeApp();
            }
        });

        // Initialize Lead Form
        setupLeadSubmission();
    });

    // Expose global open method
    window.openSmartExchange = openSmartExchangeApp;
})();
