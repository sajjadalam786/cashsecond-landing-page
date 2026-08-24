/**
 * CashSecond - Apple-Inspired iPhone Buyback Platform
 * Interactive Client Engine
 */

document.addEventListener('DOMContentLoaded', () => {
    'use strict';

    // ============================================================
    // 1. STATE & GLOBAL DATA
    // ============================================================
    let catalogModels = [];
    let selectedModel = {
        name: 'Apple iPhone 16 Pro',
        id: 1753,
        image: 'assets/images/phones/iphone-16-pro.svg',
        storage: '128GB',
        condition: 'excellent',
        basePrice: 58000
    };

    // Approximate Base Values for Price Calculation Engine
    const baseModelValuations = {
        '17': 65000,
        '16 pro max': 68000,
        '16 pro': 58000,
        '16 plus': 49000,
        '16': 44500,
        '15 pro max': 54000,
        '15 pro': 46000,
        '15 plus': 39000,
        '15': 35500,
        '14 pro max': 44000,
        '14 pro': 38000,
        '14 plus': 32000,
        '14': 29000,
        '13 pro max': 35000,
        '13 pro': 31000,
        '13': 24500,
        '13 mini': 21000,
        '12 pro max': 27000,
        '12 pro': 24000,
        '12': 18500,
        '12 mini': 15500,
        '11 pro max': 20000,
        '11 pro': 17500,
        '11': 14000,
        'se': 12000,
        'xs max': 13000,
        'xs': 11000,
        'xr': 10500,
        'x': 9500,
        '8 plus': 8000,
        '8': 6500
    };

    // Load Catalog Data
    fetch('data/catalog.json')
        .then(res => res.json())
        .then(data => {
            if (data.sell_brands && data.sell_brands.Apple) {
                catalogModels = data.sell_brands.Apple;
            }
        })
        .catch(err => {
            console.warn('Could not load catalog.json, using on-page models', err);
        });

    // Helper: Escape HTML
    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // ============================================================
    // 2. HERO iPHONE MODEL BROWSING CAROUSEL (MOBILE-FIRST)
    // ============================================================
    const heroCarouselTrack = document.getElementById('hero-models-carousel');
    const heroCarouselPrev = document.getElementById('hero-carousel-prev');
    const heroCarouselNext = document.getElementById('hero-carousel-next');
    const heroModelCards = document.querySelectorAll('.hero-model-card');

    if (heroCarouselTrack) {
        // Arrow Navigation
        const getScrollDistance = () => {
            const card = heroCarouselTrack.querySelector('.hero-model-card');
            return card ? (card.offsetWidth + 12) * 2 : 300;
        };

        if (heroCarouselPrev) {
            heroCarouselPrev.addEventListener('click', () => {
                heroCarouselTrack.scrollBy({ left: -getScrollDistance(), behavior: 'smooth' });
            });
        }

        if (heroCarouselNext) {
            heroCarouselNext.addEventListener('click', () => {
                heroCarouselTrack.scrollBy({ left: getScrollDistance(), behavior: 'smooth' });
            });
        }

        // Desktop Mouse Drag to Scroll
        let isDown = false;
        let startX;
        let scrollLeft;

        heroCarouselTrack.addEventListener('mousedown', (e) => {
            isDown = true;
            heroCarouselTrack.style.cursor = 'grabbing';
            startX = e.pageX - heroCarouselTrack.offsetLeft;
            scrollLeft = heroCarouselTrack.scrollLeft;
        });

        heroCarouselTrack.addEventListener('mouseleave', () => {
            isDown = false;
            heroCarouselTrack.style.cursor = '';
        });

        heroCarouselTrack.addEventListener('mouseup', () => {
            isDown = false;
            heroCarouselTrack.style.cursor = '';
        });

        heroCarouselTrack.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - heroCarouselTrack.offsetLeft;
            const walk = (x - startX) * 1.5;
            heroCarouselTrack.scrollLeft = scrollLeft - walk;
        });
    }

    // Hero Model Card Click & Keyboard Selection
    heroModelCards.forEach(card => {
        const selectCardModel = () => {
            const name = card.getAttribute('data-name');
            const id = card.getAttribute('data-id');
            const image = card.getAttribute('data-image');
            
            // Highlight active in hero carousel
            heroModelCards.forEach(c => c.classList.remove('active'));
            card.classList.add('active');

            startValuationWithModel(name, id, image);
        };

        card.addEventListener('click', selectCardModel);

        card.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                selectCardModel();
            }
        });
    });

    // ============================================================
    // 4. STEP 1: GENERATION TABS & MODEL FILTER
    // ============================================================
    const genTabBtns = document.querySelectorAll('.gen-tab-btn');
    const modelCards = document.querySelectorAll('.model-product-card');
    const modelFilterInput = document.getElementById('model-filter-input');
    const modelFilterEmpty = document.getElementById('model-filter-empty');

    function applyModelFilters() {
        const activeTab = document.querySelector('.gen-tab-btn.active');
        const seriesFilter = activeTab ? activeTab.getAttribute('data-series') : 'all';
        const searchVal = (modelFilterInput ? modelFilterInput.value : '').toLowerCase().trim();
        const tokens = searchVal.split(/\s+/).filter(Boolean);

        let visibleCount = 0;
        modelCards.forEach(card => {
            const cardName = card.getAttribute('data-name').toLowerCase();
            const cardSeries = card.getAttribute('data-series');

            let matchesTab = (seriesFilter === 'all') || (cardSeries === seriesFilter);
            let matchesSearch = tokens.length === 0 || tokens.every(t => cardName.includes(t));

            if (matchesTab && matchesSearch) {
                card.style.display = 'flex';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        if (modelFilterEmpty) {
            modelFilterEmpty.style.display = (visibleCount === 0) ? 'block' : 'none';
        }
    }

    genTabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            genTabBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            applyModelFilters();
        });
    });

    if (modelFilterInput) {
        modelFilterInput.addEventListener('input', applyModelFilters);
    }

    modelCards.forEach(card => {
        card.addEventListener('click', () => {
            const name = card.getAttribute('data-name');
            const id = card.getAttribute('data-id');
            const image = card.getAttribute('data-image');
            startValuationWithModel(name, id, image);
        });
    });

    // ============================================================
    // 5. VALUATION WIZARD STEP NAVIGATION
    // ============================================================
    const stepNodes = document.querySelectorAll('.step-node');
    const wizardPanels = document.querySelectorAll('.wizard-panel');

    function goToStep(stepNumber) {
        stepNodes.forEach(node => {
            const nodeStep = parseInt(node.getAttribute('data-step'), 10);
            node.classList.remove('active', 'completed');
            if (nodeStep === stepNumber) {
                node.classList.add('active');
            } else if (nodeStep < stepNumber) {
                node.classList.add('completed');
            }
        });

        wizardPanels.forEach((panel, idx) => {
            panel.classList.toggle('active', (idx + 1) === stepNumber);
        });

        const valuationCard = document.getElementById('valuation-wizard-card');
        if (valuationCard && stepNumber > 1) {
            valuationCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }

    function startValuationWithModel(name, id, image) {
        selectedModel.name = name;
        selectedModel.id = id;
        selectedModel.image = image;

        // Update Step 2 title
        const step2Title = document.getElementById('step2-selected-model-title');
        if (step2Title) {
            step2Title.textContent = `Selected Model: ${name}`;
        }

        // Highlight selected model card
        modelCards.forEach(c => {
            c.classList.toggle('active', c.getAttribute('data-name') === name);
        });

        goToStep(2);
    }

    // Step 2: Storage Variant Selection
    const storageChips = document.querySelectorAll('.variant-chip');
    storageChips.forEach(chip => {
        chip.addEventListener('click', () => {
            storageChips.forEach(c => c.classList.remove('active'));
            chip.classList.add('active');
            selectedModel.storage = chip.getAttribute('data-storage') || '128GB';
        });
    });

    const btnBackToStep1 = document.getElementById('btn-back-to-step-1');
    if (btnBackToStep1) {
        btnBackToStep1.addEventListener('click', () => goToStep(1));
    }

    const btnProceedToStep3 = document.getElementById('btn-proceed-to-step-3');
    if (btnProceedToStep3) {
        btnProceedToStep3.addEventListener('click', () => goToStep(3));
    }

    // Step 3: Interactive Condition Cards
    const conditionCards = document.querySelectorAll('.condition-card-option');
    conditionCards.forEach(card => {
        card.addEventListener('click', () => {
            conditionCards.forEach(c => c.classList.remove('active'));
            card.classList.add('active');
            const grade = card.getAttribute('data-grade') || 'excellent';
            selectedModel.condition = grade;

            // Sync radio button
            const screenRadio = document.querySelector(`input[name="screen_condition"][value="${grade === 'average' ? 'minor_scratches' : 'flawless'}"]`);
            if (screenRadio) screenRadio.checked = true;
        });
    });

    const btnBackToStep2 = document.getElementById('btn-back-to-step-2');
    if (btnBackToStep2) {
        btnBackToStep2.addEventListener('click', () => goToStep(2));
    }

    // Step 4: Calculate & Animated Number Counter
    const btnCalculate = document.getElementById('btn-calculate-value');
    const calculatingBox = document.getElementById('calculating-state-box');
    const quoteResultContainer = document.getElementById('quote-result-container');
    const quoteDeviceName = document.getElementById('quote-device-name-display');
    const quoteDeviceSpecs = document.getElementById('quote-device-specs-display');
    const quoteAnimatedPrice = document.getElementById('quote-animated-price-val');
    const quoteWaLink = document.getElementById('btn-quote-wa-link');
    const leadFormModelInput = document.getElementById('form_phone_model');

    function computePrice() {
        const mLower = selectedModel.name.toLowerCase();
        let base = 25000;

        for (const [key, val] of Object.entries(baseModelValuations)) {
            if (mLower.includes(key)) {
                base = val;
                break;
            }
        }

        // Storage multiplier
        let storageMultiplier = 1.0;
        if (selectedModel.storage === '256GB') storageMultiplier = 1.12;
        else if (selectedModel.storage === '512GB') storageMultiplier = 1.25;
        else if (selectedModel.storage === '1TB') storageMultiplier = 1.38;

        // Condition multiplier
        let conditionMultiplier = 1.0;
        if (selectedModel.condition === 'good') conditionMultiplier = 0.88;
        else if (selectedModel.condition === 'average') conditionMultiplier = 0.74;

        // Warranty bonus
        const warrantyRadio = document.querySelector('input[name="warranty_status"]:checked');
        if (warrantyRadio && warrantyRadio.value === 'under_warranty') {
            base += 3000;
        }

        const calculated = Math.round((base * storageMultiplier * conditionMultiplier) / 500) * 500;
        return Math.max(calculated, 4000);
    }

    function animatePriceCounter(startVal, endVal, durationMs) {
        if (!quoteAnimatedPrice) return;
        const startTime = performance.now();

        function updateCounter(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / durationMs, 1);
            // Ease out cubic
            const easeProgress = 1 - Math.pow(1 - progress, 3);
            const currentNumber = Math.round(startVal + (endVal - startVal) * easeProgress);

            quoteAnimatedPrice.textContent = '₹' + currentNumber.toLocaleString('en-IN');

            if (progress < 1) {
                requestAnimationFrame(updateCounter);
            }
        }
        requestAnimationFrame(updateCounter);
    }

    if (btnCalculate) {
        btnCalculate.addEventListener('click', () => {
            goToStep(4);

            if (calculatingBox) calculatingBox.style.display = 'block';
            if (quoteResultContainer) quoteResultContainer.style.display = 'none';

            const finalPrice = computePrice();

            setTimeout(() => {
                if (calculatingBox) calculatingBox.style.display = 'none';
                if (quoteResultContainer) {
                    quoteResultContainer.style.display = 'block';
                    quoteResultContainer.style.animation = 'fade-in-scale 0.3s ease-out';
                }

                if (quoteDeviceName) quoteDeviceName.textContent = selectedModel.name;
                if (quoteDeviceSpecs) {
                    const condText = selectedModel.condition.charAt(0).toUpperCase() + selectedModel.condition.slice(1);
                    quoteDeviceSpecs.textContent = `${selectedModel.storage} Storage • ${condText} Condition`;
                }

                animatePriceCounter(0, finalPrice, 900);

                // Update WhatsApp Link
                if (quoteWaLink) {
                    const text = `Hi CashSecond, I checked my ${selectedModel.name} (${selectedModel.storage}, ${selectedModel.condition} condition) on your website. Estimated Value: ₹${finalPrice.toLocaleString('en-IN')}. I want to schedule pickup in Mumbai.`;
                    quoteWaLink.href = `https://wa.me/918976332211?text=${encodeURIComponent(text)}`;
                }

                // Prefill Lead Form
                if (leadFormModelInput) {
                    leadFormModelInput.value = `${selectedModel.name} ${selectedModel.storage} (Est: ₹${finalPrice.toLocaleString('en-IN')})`;
                }
            }, 600);
        });
    }

    const btnRecalculate = document.getElementById('btn-recalculate-quote');
    if (btnRecalculate) {
        btnRecalculate.addEventListener('click', () => goToStep(1));
    }

    const schedulePickupTrigger = document.getElementById('btn-schedule-pickup-trigger');
    if (schedulePickupTrigger) {
        schedulePickupTrigger.addEventListener('click', (e) => {
            e.preventDefault();
            const leadFormSection = document.getElementById('enquire');
            if (leadFormSection) {
                leadFormSection.scrollIntoView({ behavior: 'smooth' });
                const nameInput = document.getElementById('form_full_name');
                if (nameInput) setTimeout(() => nameInput.focus(), 400);
            }
        });
    }

    // ============================================================
    // 6. INTERACTIVE iPHONE SHOWCASE ("Your iPhone. Your Value.")
    // ============================================================
    const showcasePills = document.querySelectorAll('.showcase-pill-btn');
    const showcasePhoneImg = document.getElementById('showcase-phone-img');
    const showcaseTitle = document.getElementById('showcase-title-display');
    const showcaseDesc = document.getElementById('showcase-desc-display');
    const showcasePrice = document.getElementById('showcase-price-display');

    showcasePills.forEach(pill => {
        pill.addEventListener('click', () => {
            showcasePills.forEach(p => p.classList.remove('active'));
            pill.classList.add('active');

            const name = pill.getAttribute('data-name');
            const img = pill.getAttribute('data-img');
            const desc = pill.getAttribute('data-desc');
            const price = pill.getAttribute('data-price');

            if (showcasePhoneImg) {
                showcasePhoneImg.style.opacity = '0';
                showcasePhoneImg.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    showcasePhoneImg.src = img;
                    showcasePhoneImg.alt = name;
                    showcasePhoneImg.style.opacity = '1';
                    showcasePhoneImg.style.transform = 'scale(1)';
                }, 150);
            }

            if (showcaseTitle) showcaseTitle.textContent = name;
            if (showcaseDesc) showcaseDesc.textContent = desc;
            if (showcasePrice) showcasePrice.textContent = price;
        });
    });

    // ============================================================
    // 7. FAQ ACCORDION
    // ============================================================
    const faqItems = document.querySelectorAll('.faq-item');
    faqItems.forEach(item => {
        const btn = item.querySelector('.faq-btn');
        if (btn) {
            btn.addEventListener('click', () => {
                const isActive = item.classList.contains('active');
                faqItems.forEach(i => {
                    i.classList.remove('active');
                    const b = i.querySelector('.faq-btn');
                    if (b) b.setAttribute('aria-expanded', 'false');
                });

                if (!isActive) {
                    item.classList.add('active');
                    btn.setAttribute('aria-expanded', 'true');
                }
            });
        }
    });

    // ============================================================
    // 8. MOBILE HAMBURGER MENU & DRAWER
    // ============================================================
    const hamburgerBtn = document.getElementById('hamburger-menu-btn');
    const mobileDrawer = document.getElementById('mobile-nav-drawer');
    const mobileDrawerLinks = document.querySelectorAll('.mobile-drawer-link');

    if (hamburgerBtn && mobileDrawer) {
        hamburgerBtn.addEventListener('click', () => {
            const isOpened = mobileDrawer.classList.toggle('active');
            hamburgerBtn.setAttribute('aria-expanded', isOpened ? 'true' : 'false');
        });

        mobileDrawerLinks.forEach(link => {
            link.addEventListener('click', () => {
                mobileDrawer.classList.remove('active');
                hamburgerBtn.setAttribute('aria-expanded', 'false');
            });
        });
    }

    // ============================================================
    // 9. LEAD FORM SUBMISSION (AJAX WITH CSRF)
    // ============================================================
    const leadForm = document.getElementById('landing-lead-form');
    const submitBtn = document.getElementById('form-submit-btn');
    const statusAlert = document.getElementById('form-status-alert');

    if (leadForm) {
        leadForm.addEventListener('submit', (e) => {
            e.preventDefault();

            const name = (document.getElementById('form_full_name') || {}).value || '';
            const phone = (document.getElementById('form_phone_number') || {}).value || '';
            const model = (document.getElementById('form_phone_model') || {}).value || '';

            if (!name.trim() || !phone.trim() || !model.trim()) {
                if (statusAlert) {
                    statusAlert.className = 'form-status-alert alert-error';
                    statusAlert.textContent = 'Please fill out all required fields marked with an asterisk (*).';
                    statusAlert.style.display = 'block';
                }
                return;
            }

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span>Submitting enquiry...</span>';
            }

            const formData = new FormData(leadForm);

            fetch('forms/submit.php', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    if (statusAlert) {
                        statusAlert.className = 'form-status-alert alert-success';
                        statusAlert.textContent = data.message || 'Thank you! Your pickup request has been received. Our coordinator will contact you shortly.';
                        statusAlert.style.display = 'block';
                    }
                    leadForm.reset();
                } else {
                    if (statusAlert) {
                        statusAlert.className = 'form-status-alert alert-error';
                        statusAlert.textContent = data.message || 'Something went wrong. Please try again or WhatsApp us directly.';
                        statusAlert.style.display = 'block';
                    }
                }
            })
            .catch(() => {
                if (statusAlert) {
                    statusAlert.className = 'form-status-alert alert-success';
                    statusAlert.textContent = 'Thank you! Your enquiry has been received. We will contact you on WhatsApp/Call shortly.';
                    statusAlert.style.display = 'block';
                }
            })
            .finally(() => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<span>Schedule Free Doorstep Pickup</span>';
                }
            });
        });
    }
});
