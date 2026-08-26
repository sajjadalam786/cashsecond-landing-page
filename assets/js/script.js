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
    // 1b. TOP GLOBAL SEARCH BAR CONTROLLER
    // ============================================================
    const topSearchInput = document.getElementById('top-iphone-search-input');
    const topSearchClearBtn = document.getElementById('top-search-clear-btn');
    const topSearchDropdown = document.getElementById('top-search-autocomplete');
    const topSearchResultsList = document.getElementById('top-search-results-list');
    const topSearchEmptyState = document.getElementById('top-search-empty-state');
    const globalSearchWrapper = document.getElementById('global-search-wrapper');

    if (topSearchInput && topSearchDropdown) {
        function getIphoneList() {
            if (Array.isArray(window.allIphoneCatalog) && window.allIphoneCatalog.length > 0) {
                return window.allIphoneCatalog;
            }
            if (catalogModels && catalogModels.length > 0) {
                return catalogModels;
            }
            const domCards = document.querySelectorAll('.model-product-card');
            return Array.from(domCards).map(c => ({
                product_name: c.getAttribute('data-name'),
                product_id: c.getAttribute('data-id'),
                image: c.getAttribute('data-image')
            }));
        }

        function renderSearchResults(query) {
            const trimmed = query.trim().toLowerCase();
            if (!trimmed) {
                topSearchDropdown.style.display = 'none';
                topSearchInput.setAttribute('aria-expanded', 'false');
                if (topSearchClearBtn) topSearchClearBtn.style.display = 'none';
                return;
            }

            if (topSearchClearBtn) topSearchClearBtn.style.display = 'flex';

            const iphoneList = getIphoneList();
            const tokens = trimmed.split(/\s+/).filter(Boolean);
            const matches = iphoneList.filter(item => {
                const name = (item.product_name || '').toLowerCase();
                const seoName = (item.seo_name || '').toLowerCase();
                const keywords = (item.keywords || 'sell resale buyback exchange price value used old valuation trade-in').toLowerCase();
                const searchHaystack = `${name} ${seoName} ${keywords} apple iphone buyback resale price value online india`;
                return tokens.every(token => searchHaystack.includes(token));
            });

            topSearchResultsList.innerHTML = '';

            if (matches.length > 0) {
                topSearchEmptyState.style.display = 'none';
                matches.slice(0, 8).forEach(item => {
                    const el = document.createElement('a');
                    el.href = '#valuation';
                    el.className = 'top-search-item';
                    el.setAttribute('role', 'option');
                    const displayName = item.seo_name || item.product_name;
                    el.setAttribute('data-name', item.product_name);
                    el.setAttribute('data-id', item.product_id);
                    el.setAttribute('data-image', item.image || 'assets/images/phones/iphone-15.svg');

                    el.innerHTML = `
                        <img src="${item.image || 'assets/images/phones/iphone-15.svg'}" alt="${escapeHtml(item.alt_text || displayName)}" class="top-search-item-img" loading="lazy" width="32" height="38">
                        <div class="top-search-item-info">
                            <span class="top-search-item-name">${escapeHtml(displayName)}</span>
                            <span class="top-search-item-series">Apple iPhone Buyback</span>
                        </div>
                        <span class="top-search-item-action">Check Resale Value &rarr;</span>
                    `;

                    el.addEventListener('click', (e) => {
                        e.preventDefault();
                        topSearchDropdown.style.display = 'none';
                        topSearchInput.setAttribute('aria-expanded', 'false');
                        topSearchInput.value = item.product_name;

                        if (typeof startValuationWithModel === 'function') {
                            startValuationWithModel(item.product_name, item.product_id, item.image || 'assets/images/phones/iphone-15.svg');
                        }
                        const valSection = document.getElementById('valuation');
                        if (valSection) {
                            valSection.scrollIntoView({ behavior: 'smooth' });
                        }
                    });

                    topSearchResultsList.appendChild(el);
                });
            } else {
                topSearchEmptyState.style.display = 'block';
            }

            topSearchDropdown.style.display = 'block';
            topSearchInput.setAttribute('aria-expanded', 'true');
        }

        topSearchInput.addEventListener('input', (e) => {
            renderSearchResults(e.target.value);
        });

        topSearchInput.addEventListener('focus', () => {
            if (topSearchInput.value.trim()) {
                renderSearchResults(topSearchInput.value);
            }
        });

        if (topSearchClearBtn) {
            topSearchClearBtn.addEventListener('click', () => {
                topSearchInput.value = '';
                topSearchInput.focus();
                topSearchDropdown.style.display = 'none';
                topSearchInput.setAttribute('aria-expanded', 'false');
                topSearchClearBtn.style.display = 'none';
            });
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (globalSearchWrapper && !globalSearchWrapper.contains(e.target)) {
                topSearchDropdown.style.display = 'none';
                topSearchInput.setAttribute('aria-expanded', 'false');
            }
        });

        // Keydown Enter on input
        topSearchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                const firstResult = topSearchResultsList.querySelector('.top-search-item');
                if (firstResult) {
                    e.preventDefault();
                    firstResult.click();
                } else {
                    const modelFilterInput = document.getElementById('model-filter-input');
                    if (modelFilterInput) {
                        modelFilterInput.value = topSearchInput.value;
                        if (typeof applyModelFilters === 'function') applyModelFilters();
                    }
                    const valSection = document.getElementById('valuation');
                    if (valSection) {
                        valSection.scrollIntoView({ behavior: 'smooth' });
                    }
                    topSearchDropdown.style.display = 'none';
                }
            } else if (e.key === 'Escape') {
                topSearchDropdown.style.display = 'none';
            }
        });
    }

    // ============================================================
    // 2. HERO PROMOTIONAL BANNER SLIDER (MOBILE-FIRST)
    // ============================================================
    const promoSliderContainer = document.getElementById('hero-promo-slider');
    const promoSliderTrack = document.getElementById('promo-slider-track');
    const promoSlides = document.querySelectorAll('.promo-slide');
    const promoDots = document.querySelectorAll('.promo-dot');
    const promoPrevBtn = document.getElementById('promo-slider-prev');
    const promoNextBtn = document.getElementById('promo-slider-next');

    let currentSlide = 0;
    const totalSlides = promoSlides.length;
    let autoplayInterval = null;

    function goToSlide(index) {
        if (totalSlides === 0) return;
        currentSlide = (index + totalSlides) % totalSlides;

        if (promoSliderTrack) {
            promoSliderTrack.style.transform = `translateX(-${currentSlide * 100}%)`;
        }

        promoDots.forEach((dot, idx) => {
            const isActive = idx === currentSlide;
            dot.classList.toggle('active', isActive);
            dot.setAttribute('aria-selected', isActive ? 'true' : 'false');
        });
    }

    function startAutoplay() {
        stopAutoplay();
        autoplayInterval = setInterval(() => {
            goToSlide(currentSlide + 1);
        }, 4500);
    }

    function stopAutoplay() {
        if (autoplayInterval) {
            clearInterval(autoplayInterval);
            autoplayInterval = null;
        }
    }

    if (promoSliderContainer && totalSlides > 1) {
        startAutoplay();

        // Pause on Hover & Focus
        promoSliderContainer.addEventListener('mouseenter', stopAutoplay);
        promoSliderContainer.addEventListener('mouseleave', startAutoplay);
        promoSliderContainer.addEventListener('focusin', stopAutoplay);
        promoSliderContainer.addEventListener('focusout', startAutoplay);

        // Arrow Buttons
        if (promoPrevBtn) {
            promoPrevBtn.addEventListener('click', () => {
                goToSlide(currentSlide - 1);
                startAutoplay();
            });
        }

        if (promoNextBtn) {
            promoNextBtn.addEventListener('click', () => {
                goToSlide(currentSlide + 1);
                startAutoplay();
            });
        }

        // Pagination Dots Click
        promoDots.forEach(dot => {
            dot.addEventListener('click', () => {
                const targetSlide = parseInt(dot.getAttribute('data-slide'), 10);
                goToSlide(targetSlide);
                startAutoplay();
            });
        });

        // Mobile Touch Swipe Handling
        let touchStartX = 0;
        let touchStartY = 0;
        let touchEndX = 0;
        let touchEndY = 0;
        let isSwiping = false;

        promoSliderContainer.addEventListener('touchstart', (e) => {
            stopAutoplay();
            touchStartX = e.touches[0].clientX;
            touchStartY = e.touches[0].clientY;
            isSwiping = true;
        }, { passive: true });

        promoSliderContainer.addEventListener('touchmove', (e) => {
            if (!isSwiping) return;
            touchEndX = e.touches[0].clientX;
            touchEndY = e.touches[0].clientY;
        }, { passive: true });

        promoSliderContainer.addEventListener('touchend', () => {
            if (!isSwiping) return;
            isSwiping = false;

            const deltaX = touchStartX - touchEndX;
            const deltaY = Math.abs(touchStartY - touchEndY);

            // Trigger only if horizontal swipe dominates vertical scroll
            if (Math.abs(deltaX) > 40 && Math.abs(deltaX) > deltaY) {
                if (deltaX > 0) {
                    goToSlide(currentSlide + 1); // Swiped Left -> Next
                } else {
                    goToSlide(currentSlide - 1); // Swiped Right -> Prev
                }
            }
            startAutoplay();
        });

        // Keyboard Navigation
        promoSliderContainer.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') {
                e.preventDefault();
                goToSlide(currentSlide - 1);
                startAutoplay();
            } else if (e.key === 'ArrowRight') {
                e.preventDefault();
                goToSlide(currentSlide + 1);
                startAutoplay();
            }
        });
    }

    // ============================================================
    // 3. "BETTER FOR POCKET. BUY REFURBISHED" HORIZONTAL CAROUSEL
    // ============================================================
    const refurbishedTrack = document.getElementById('refurbished-cards-track');
    const refurbishedPrevBtn = document.getElementById('refurbished-arrow-prev');
    const refurbishedNextBtn = document.getElementById('refurbished-arrow-next');
    const refurbishedCards = document.querySelectorAll('.refurbished-card');

    if (refurbishedTrack) {
        // Arrow Navigation
        const getRefurbishedScrollStep = () => {
            const card = refurbishedTrack.querySelector('.refurbished-card');
            return card ? (card.offsetWidth + 12) * 2 : 320;
        };

        if (refurbishedPrevBtn) {
            refurbishedPrevBtn.addEventListener('click', () => {
                pauseRefurbishedAutoscroll();
                refurbishedTrack.scrollBy({ left: -getRefurbishedScrollStep(), behavior: 'smooth' });
                resumeRefurbishedAutoscroll();
            });
        }

        if (refurbishedNextBtn) {
            refurbishedNextBtn.addEventListener('click', () => {
                pauseRefurbishedAutoscroll();
                refurbishedTrack.scrollBy({ left: getRefurbishedScrollStep(), behavior: 'smooth' });
                resumeRefurbishedAutoscroll();
            });
        }

        // Smooth Auto-scroll Controller
        let autoscrollInterval = null;
        let isUserInteracting = false;
        let scrollDirection = 1; // 1 = right, -1 = left

        function startRefurbishedAutoscroll() {
            if (autoscrollInterval || isUserInteracting) return;
            autoscrollInterval = setInterval(() => {
                if (isUserInteracting || !refurbishedTrack) return;
                
                const maxScrollLeft = refurbishedTrack.scrollWidth - refurbishedTrack.clientWidth;
                if (maxScrollLeft <= 5) return;

                if (refurbishedTrack.scrollLeft >= maxScrollLeft - 10) {
                    scrollDirection = -1;
                } else if (refurbishedTrack.scrollLeft <= 5) {
                    scrollDirection = 1;
                }

                refurbishedTrack.scrollLeft += (scrollDirection * 1);
            }, 35);
        }

        function pauseRefurbishedAutoscroll() {
            if (autoscrollInterval) {
                clearInterval(autoscrollInterval);
                autoscrollInterval = null;
            }
        }

        let resumeTimer = null;
        function resumeRefurbishedAutoscroll() {
            if (resumeTimer) clearTimeout(resumeTimer);
            resumeTimer = setTimeout(() => {
                if (!isUserInteracting) {
                    startRefurbishedAutoscroll();
                }
            }, 2500);
        }

        startRefurbishedAutoscroll();

        // Mouse hover / interaction
        refurbishedTrack.addEventListener('mouseenter', () => {
            isUserInteracting = true;
            pauseRefurbishedAutoscroll();
        });

        refurbishedTrack.addEventListener('mouseleave', () => {
            isUserInteracting = false;
            resumeRefurbishedAutoscroll();
        });

        // Desktop Mouse Drag to Scroll
        let isDownRefurb = false;
        let startXRefurb = 0;
        let scrollLeftRefurb = 0;

        refurbishedTrack.addEventListener('mousedown', (e) => {
            isDownRefurb = true;
            isUserInteracting = true;
            pauseRefurbishedAutoscroll();
            refurbishedTrack.style.cursor = 'grabbing';
            startXRefurb = e.pageX - refurbishedTrack.offsetLeft;
            scrollLeftRefurb = refurbishedTrack.scrollLeft;
        });

        window.addEventListener('mouseup', () => {
            if (isDownRefurb) {
                isDownRefurb = false;
                refurbishedTrack.style.cursor = '';
                isUserInteracting = false;
                resumeRefurbishedAutoscroll();
            }
        });

        refurbishedTrack.addEventListener('mousemove', (e) => {
            if (!isDownRefurb) return;
            e.preventDefault();
            const x = e.pageX - refurbishedTrack.offsetLeft;
            const walk = (x - startXRefurb) * 1.5;
            refurbishedTrack.scrollLeft = scrollLeftRefurb - walk;
        });

        // Touch handling (passive listeners for mobile swipe)
        refurbishedTrack.addEventListener('touchstart', () => {
            isUserInteracting = true;
            pauseRefurbishedAutoscroll();
        }, { passive: true });

        refurbishedTrack.addEventListener('touchend', () => {
            isUserInteracting = false;
            resumeRefurbishedAutoscroll();
        }, { passive: true });
    }

    // Refurbished Card Click & Keyboard Selection
    refurbishedCards.forEach(card => {
        const selectRefurbishedModel = (e) => {
            const name = card.getAttribute('data-name');
            const id = card.getAttribute('data-id');
            const image = card.getAttribute('data-image');

            refurbishedCards.forEach(c => c.classList.remove('active'));
            card.classList.add('active');

            if (name && (name.includes('iPhone') || name.includes('Apple'))) {
                startValuationWithModel(name, id, image);
            } else {
                // For non-iPhone devices, scroll smoothly to valuation
                const valSec = document.getElementById('valuation');
                if (valSec) {
                    valSec.scrollIntoView({ behavior: 'smooth' });
                }
            }
        };

        card.addEventListener('click', selectRefurbishedModel);

        card.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                selectRefurbishedModel(e);
            }
        });

        const ctaBtn = card.querySelector('.refurbished-card-cta');
        if (ctaBtn) {
            ctaBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                selectRefurbishedModel(e);
            });
        }
    });

    // iPhone Strip Pills Click Handler
    const iphonePillCards = document.querySelectorAll('.iphone-pill-card');
    iphonePillCards.forEach(pill => {
        pill.addEventListener('click', (e) => {
            e.preventDefault();
            const name = pill.getAttribute('data-name');
            const id = pill.getAttribute('data-id');
            const image = pill.getAttribute('data-image');

            if (name && typeof startValuationWithModel === 'function') {
                startValuationWithModel(name, id, image);
                const valSec = document.getElementById('valuation');
                if (valSec) {
                    valSec.scrollIntoView({ behavior: 'smooth' });
                }
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
            const cardName = (card.getAttribute('data-name') || '').toLowerCase();
            const cardSeoName = (card.getAttribute('data-seo-name') || '').toLowerCase();
            const cardKeywords = (card.getAttribute('data-keywords') || 'sell resale buyback exchange price value used old valuation').toLowerCase();
            const cardSeries = card.getAttribute('data-series');

            const cardHaystack = `${cardName} ${cardSeoName} ${cardKeywords} apple iphone buyback resale price value india`;

            let matchesTab = (seriesFilter === 'all') || (cardSeries === seriesFilter);
            let matchesSearch = tokens.length === 0 || tokens.every(t => cardHaystack.includes(t));

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
    // 4B. iPHONE MODELS SHOWCASE STRIP CONTROLLER
    // ============================================================
    const showcaseTrack = document.getElementById('models-showcase-track');
    const showcaseScrollPrev = document.getElementById('showcase-scroll-prev');
    const showcaseScrollNext = document.getElementById('showcase-scroll-next');
    const showcaseCards = document.querySelectorAll('.showcase-model-card');

    if (showcaseTrack) {
        if (showcaseScrollPrev) {
            showcaseScrollPrev.addEventListener('click', () => {
                showcaseTrack.scrollBy({ left: -320, behavior: 'smooth' });
            });
        }
        if (showcaseScrollNext) {
            showcaseScrollNext.addEventListener('click', () => {
                showcaseTrack.scrollBy({ left: 320, behavior: 'smooth' });
            });
        }
    }

    showcaseCards.forEach(card => {
        const selectShowcaseModel = (e) => {
            e.preventDefault();
            const name = card.getAttribute('data-name');
            const id = card.getAttribute('data-id');
            const image = card.getAttribute('data-image');

            if (name && typeof startValuationWithModel === 'function') {
                startValuationWithModel(name, id, image);
                const valSec = document.getElementById('valuation');
                if (valSec) {
                    valSec.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        };

        card.addEventListener('click', selectShowcaseModel);
        card.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                selectShowcaseModel(e);
            }
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

    // ============================================================
    // 5. UNIFIED PRICE CALCULATION ENGINE
    // ============================================================
    function getEstimatedPrice(modelName, storageStr, conditionStr) {
        const mLower = (modelName || '').toLowerCase();
        let base = 25000;

        for (const [key, val] of Object.entries(baseModelValuations)) {
            if (mLower.includes(key)) {
                base = val;
                break;
            }
        }

        // Storage multiplier
        let storageMultiplier = 1.0;
        const stor = (storageStr || '').toUpperCase();
        if (stor.includes('256')) storageMultiplier = 1.12;
        else if (stor.includes('512')) storageMultiplier = 1.25;
        else if (stor.includes('1 TB') || stor.includes('1TB')) storageMultiplier = 1.38;

        // Condition multiplier
        let conditionMultiplier = 0.88;
        const cond = (conditionStr || '').toLowerCase();
        if (cond.includes('like new') || cond.includes('flawless') || cond.includes('excellent')) conditionMultiplier = 1.0;
        else if (cond.includes('good')) conditionMultiplier = 0.88;
        else if (cond.includes('fair') || cond.includes('average')) conditionMultiplier = 0.74;
        else if (cond.includes('damaged') || cond.includes('poor')) conditionMultiplier = 0.55;

        // Warranty bonus if radio checked
        const warrantyRadio = document.querySelector('input[name="warranty_status"]:checked');
        if (warrantyRadio && warrantyRadio.value === 'under_warranty') {
            base += 3000;
        }

        const calculated = Math.round((base * storageMultiplier * conditionMultiplier) / 500) * 500;
        return Math.max(calculated, 4000);
    }

    function computePrice() {
        return getEstimatedPrice(selectedModel.name, selectedModel.storage, selectedModel.condition);
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

    // Step 4: Calculate & Animated Number Counter
    const btnCalculate = document.getElementById('btn-calculate-estimate') || document.getElementById('btn-calculate-value');
    const calculatingBox = document.getElementById('calculating-state-box');
    const quoteResultContainer = document.getElementById('quote-result-container');
    const quoteDeviceName = document.getElementById('quote-device-name-display') || document.getElementById('estimate-model-name');
    const quoteDeviceSpecs = document.getElementById('quote-device-specs-display');
    const quoteAnimatedPrice = document.getElementById('quote-animated-price-val') || document.getElementById('estimate-amount');
    const quoteWaLink = document.getElementById('btn-quote-wa-link');
    const leadFormModelInput = document.getElementById('form_phone_model');

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

                if (quoteDeviceName) quoteDeviceName.textContent = `${selectedModel.name} (${selectedModel.storage})`;
                if (quoteDeviceSpecs) {
                    const condText = selectedModel.condition.charAt(0).toUpperCase() + selectedModel.condition.slice(1);
                    quoteDeviceSpecs.textContent = `${selectedModel.storage} Storage • ${condText} Condition`;
                }

                const basePriceElem = document.getElementById('breakdown-base-price');
                if (basePriceElem) basePriceElem.textContent = '₹' + finalPrice.toLocaleString('en-IN');

                const rangeTextElem = document.getElementById('estimate-range-text');
                if (rangeTextElem) {
                    const low = Math.round((finalPrice * 0.95) / 500) * 500;
                    const high = Math.round((finalPrice * 1.05) / 500) * 500;
                    rangeTextElem.textContent = `Expected range: ₹${low.toLocaleString('en-IN')} – ₹${high.toLocaleString('en-IN')}`;
                }

                // Update hidden inputs for wizard lead form
                const wzModel = document.getElementById('wizard_form_model');
                const wzStorage = document.getElementById('wizard_form_storage');
                const wzCond = document.getElementById('wizard_form_condition');
                const wzEstVal = document.getElementById('wizard_form_est_val');

                if (wzModel) wzModel.value = selectedModel.name;
                if (wzStorage) wzStorage.value = selectedModel.storage;
                if (wzCond) wzCond.value = selectedModel.condition;
                if (wzEstVal) wzEstVal.value = '₹' + finalPrice.toLocaleString('en-IN');

                animatePriceCounter(0, finalPrice, 900);

                // Update WhatsApp Link
                if (quoteWaLink) {
                    const text = `Hi CashSecond, I checked my ${selectedModel.name} (${selectedModel.storage}, ${selectedModel.condition} condition) on your website. Estimated Value: ₹${finalPrice.toLocaleString('en-IN')}. I want to schedule pickup in Mumbai.`;
                    quoteWaLink.href = `https://wa.me/918976332211?text=${encodeURIComponent(text)}`;
                }

                // Prefill bottom Lead Form
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

    // Wizard Step 5 Lead Form AJAX Submission
    const wizardLeadForm = document.getElementById('wizard-lead-form');
    const wizardSubmitBtn = document.getElementById('wizard-form-submit-btn');
    const wizardStatusAlert = document.getElementById('wizard-form-status');

    if (wizardLeadForm) {
        wizardLeadForm.addEventListener('submit', (e) => {
            e.preventDefault();

            const name = (document.getElementById('wizard_full_name') || {}).value || '';
            const phone = (document.getElementById('wizard_phone_number') || {}).value || '';

            if (!name.trim() || !phone.trim()) {
                if (wizardStatusAlert) {
                    wizardStatusAlert.className = 'form-status-alert alert-error';
                    wizardStatusAlert.textContent = 'Please enter your Full Name and Mobile Number.';
                    wizardStatusAlert.style.display = 'block';
                }
                return;
            }

            if (wizardSubmitBtn) {
                wizardSubmitBtn.disabled = true;
                wizardSubmitBtn.innerHTML = '<span>Processing valuation...</span>';
            }

            const formData = new FormData(wizardLeadForm);

            fetch('forms/submit.php', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    if (wizardStatusAlert) {
                        wizardStatusAlert.className = 'form-status-alert alert-success';
                        wizardStatusAlert.innerHTML = `
                            <p style="font-weight: 700; color: #34C759; margin-bottom: 4px; font-size: 1rem;">✓ Thank You! Your iPhone details have been received.</p>
                            <p style="color: var(--color-text-secondary); font-size: 0.8125rem; margin-bottom: 12px;">Our team will contact you shortly regarding your iPhone valuation.</p>
                            <a href="${data.whatsapp_direct_url || '#'}" target="_blank" rel="noopener noreferrer" class="btn btn-whatsapp btn-full" style="text-decoration:none;">
                                <span>CONTINUE ON WHATSAPP &rarr;</span>
                            </a>
                        `;
                        wizardStatusAlert.style.display = 'block';
                    }
                    wizardLeadForm.reset();
                } else {
                    if (wizardStatusAlert) {
                        wizardStatusAlert.className = 'form-status-alert alert-error';
                        wizardStatusAlert.textContent = data.message || 'Something went wrong. Please try again.';
                        wizardStatusAlert.style.display = 'block';
                    }
                }
            })
            .catch(() => {
                if (wizardStatusAlert) {
                    wizardStatusAlert.className = 'form-status-alert alert-error';
                    wizardStatusAlert.textContent = 'Something went wrong. Please try again or WhatsApp us directly.';
                    wizardStatusAlert.style.display = 'block';
                }
            })
            .finally(() => {
                if (wizardSubmitBtn) {
                    wizardSubmitBtn.disabled = false;
                    wizardSubmitBtn.innerHTML = '<span>GET MY IPHONE VALUE &rarr;</span>';
                }
            });
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
    // 6.5 MASTER EXPANDABLE CUSTOMER REVIEWS PANEL
    // ============================================================
    const reviewsMasterPanel = document.getElementById('reviewsMasterPanel');
    const reviewsMasterToggle = document.getElementById('reviewsMasterToggle');

    function toggleMasterReviews(forceState) {
        if (!reviewsMasterPanel || !reviewsMasterToggle) return;
        const willOpen = typeof forceState === 'boolean' 
            ? forceState 
            : !reviewsMasterPanel.classList.contains('open');

        if (willOpen) {
            reviewsMasterPanel.classList.add('open');
            reviewsMasterToggle.setAttribute('aria-expanded', 'true');
        } else {
            reviewsMasterPanel.classList.remove('open');
            reviewsMasterToggle.setAttribute('aria-expanded', 'false');
        }
    }

    if (reviewsMasterToggle && reviewsMasterPanel) {
        reviewsMasterToggle.addEventListener('click', () => {
            toggleMasterReviews();
        });

        // Ensure Keyboard trigger with Enter/Space
        reviewsMasterToggle.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                toggleMasterReviews();
            }
        });
    }

    // Auto-open master Reviews panel if user clicks #reviews link or URL lands on #reviews
    const reviewLinks = document.querySelectorAll('a[href="#reviews"]');
    reviewLinks.forEach(link => {
        link.addEventListener('click', () => {
            toggleMasterReviews(true);
        });
    });

    if (window.location.hash === '#reviews') {
        toggleMasterReviews(true);
    }

    // ============================================================
    // 7. MASTER EXPANDABLE FAQ PANEL & INNER ACCORDION
    // ============================================================
    const faqMasterPanel = document.getElementById('faqMasterPanel');
    const faqMasterToggle = document.getElementById('faqMasterToggle');

    function toggleMasterFaq(forceState) {
        if (!faqMasterPanel || !faqMasterToggle) return;
        const willOpen = typeof forceState === 'boolean' 
            ? forceState 
            : !faqMasterPanel.classList.contains('open');

        if (willOpen) {
            faqMasterPanel.classList.add('open');
            faqMasterToggle.setAttribute('aria-expanded', 'true');
        } else {
            faqMasterPanel.classList.remove('open');
            faqMasterToggle.setAttribute('aria-expanded', 'false');
        }
    }

    if (faqMasterToggle && faqMasterPanel) {
        faqMasterToggle.addEventListener('click', () => {
            toggleMasterFaq();
        });

        // Ensure Keyboard trigger with Enter/Space
        faqMasterToggle.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                toggleMasterFaq();
            }
        });
    }

    // Auto-open master FAQ panel if user clicks #faq link or URL lands on #faq
    const faqLinks = document.querySelectorAll('a[href="#faq"]');
    faqLinks.forEach(link => {
        link.addEventListener('click', () => {
            toggleMasterFaq(true);
        });
    });

    if (window.location.hash === '#faq') {
        toggleMasterFaq(true);
    }

    // Inner Individual FAQ Questions Accordion
    const faqItems = document.querySelectorAll('.faq-item');
    faqItems.forEach(item => {
        const btn = item.querySelector('.faq-btn');
        if (btn) {
            btn.addEventListener('click', (e) => {
                e.stopPropagation(); // Prevent bubbling to master container
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
                submitBtn.innerHTML = '<span>Processing valuation...</span>';
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
                        statusAlert.innerHTML = `
                            <p style="font-weight: 700; color: #34C759; margin-bottom: 4px; font-size: 1rem;">✓ Thank You! Your iPhone details have been received.</p>
                            <p style="color: rgba(255, 255, 255, 0.85); font-size: 0.875rem; margin-bottom: 12px;">Our team will contact you shortly regarding your iPhone valuation.</p>
                            <a href="${data.whatsapp_direct_url || '#'}" target="_blank" rel="noopener noreferrer" class="btn btn-whatsapp btn-full" style="text-decoration:none;">
                                <span>CONTINUE ON WHATSAPP &rarr;</span>
                            </a>
                        `;
                        statusAlert.style.display = 'block';
                    }
                    leadForm.reset();
                } else {
                    if (statusAlert) {
                        statusAlert.className = 'form-status-alert alert-error';
                        statusAlert.textContent = data.message || 'Something went wrong. Please try again.';
                        statusAlert.style.display = 'block';
                    }
                }
            })
            .catch(() => {
                if (statusAlert) {
                    statusAlert.className = 'form-status-alert alert-error';
                    statusAlert.textContent = 'Something went wrong. Please try again or WhatsApp us directly.';
                    statusAlert.style.display = 'block';
                }
            })
            .finally(() => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<span>GET MY IPHONE VALUE &rarr;</span>';
                }
            });
        });
    }

    // ============================================================
    // 16. POPUP LEAD MODAL CONTROLLER (Auto-Opens on Load & 30s Loop)
    // ============================================================
    // ============================================================
    // 16. HIGH-CONVERTING MULTI-STEP iPHONE VALUATION POPUP ENGINE
    // ============================================================
    const leadPopupModal       = document.getElementById('lead-popup-modal');
    const leadModalBackdrop    = document.getElementById('lead-modal-backdrop');
    const leadModalCloseBtn    = document.getElementById('lead-modal-close-btn');
    const popupLeadForm        = document.getElementById('popup-lead-form');
    const popupStepCounter     = document.getElementById('popup-step-counter');
    const popupProgressDots    = document.querySelectorAll('.prog-dot');
    const popupModalHeading    = document.getElementById('popup-modal-heading');
    const popupModalSubheading = document.getElementById('popup-modal-subheading');
    const popupSubmitBtn       = document.getElementById('btn-popup-submit');
    const popupStatusAlert     = document.getElementById('popup-form-status');
    const popupProgressHeader  = document.getElementById('popup-progress-header');
    const popupHeaderBlock     = document.getElementById('popup-header-block');

    // Multi-Step Internal State
    let popupState = {
        currentStep: 1,
        model: 'Apple iPhone 16 Pro',
        storage: '128 GB',
        condition: 'Good',
        conditionMult: 0.88,
        estimatedPrice: 56500,
        isExitIntent: false
    };

    function setPopupStep(stepNum) {
        popupState.currentStep = stepNum;

        // 1. Update Step Counter Text
        if (popupStepCounter) {
            popupStepCounter.textContent = `Step ${stepNum} of 4`;
        }

        // 2. Update Progress Dots
        popupProgressDots.forEach((dot, idx) => {
            const dotStep = idx + 1;
            dot.classList.remove('active', 'completed');
            if (dotStep === stepNum) {
                dot.classList.add('active');
            } else if (dotStep < stepNum) {
                dot.classList.add('completed');
            }
        });

        // 3. Switch Step Panels
        for (let i = 1; i <= 4; i++) {
            const panel = document.getElementById(`popup-panel-${i}`);
            if (panel) {
                panel.classList.toggle('active', i === stepNum);
            }
        }

        // Hide success / failure panels when navigating steps
        const successPanel = document.getElementById('popup-panel-success');
        const failurePanel = document.getElementById('popup-panel-failure');
        if (successPanel) successPanel.style.display = 'none';
        if (failurePanel) failurePanel.style.display = 'none';
        if (popupProgressHeader) popupProgressHeader.style.display = 'flex';
        if (popupHeaderBlock) popupHeaderBlock.style.display = 'block';

        // 4. Update Step 2 Header Badge
        if (stepNum === 2) {
            const step2Badge = document.getElementById('popup-step2-badge');
            if (step2Badge) {
                step2Badge.textContent = `Selected: ${popupState.model}`;
            }
        }

        // 5. Update Step 3 Header Badge
        if (stepNum === 3) {
            const step3Badge = document.getElementById('popup-step3-badge');
            if (step3Badge) {
                step3Badge.textContent = `${popupState.model} • ${popupState.storage}`;
            }
        }

        // 6. Update Step 4 Live Price Calculation & Badges
        if (stepNum === 4) {
            const calculatedVal = getEstimatedPrice(popupState.model, popupState.storage, popupState.condition);
            popupState.estimatedPrice = calculatedVal;

            const low = Math.round((calculatedVal * 0.95) / 500) * 500;
            const high = Math.round((calculatedVal * 1.05) / 500) * 500;

            const estPriceElem = document.getElementById('popup-estimate-price-val');
            if (estPriceElem) {
                estPriceElem.textContent = `₹${low.toLocaleString('en-IN')} – ₹${high.toLocaleString('en-IN')}`;
            }

            const estSpecsElem = document.getElementById('popup-estimate-specs-summary');
            if (estSpecsElem) {
                estSpecsElem.textContent = `${popupState.model} (${popupState.storage}) • ${popupState.condition} Condition`;
            }

            // Sync Hidden Inputs
            const hidModel = document.getElementById('popup_hidden_model');
            const hidStorage = document.getElementById('popup_hidden_storage');
            const hidCond = document.getElementById('popup_hidden_condition');
            const hidEst = document.getElementById('popup_hidden_est_val');

            if (hidModel) hidModel.value = popupState.model;
            if (hidStorage) hidStorage.value = popupState.storage;
            if (hidCond) hidCond.value = popupState.condition;
            if (hidEst) hidEst.value = `₹${calculatedVal.toLocaleString('en-IN')}`;
        }
    }

    function openMultiStepPopup(isExitIntent = false, preselectedModel = null) {
        if (!leadPopupModal) return;
        if (sessionStorage.getItem('cs_lead_submitted') === '1') return;

        popupState.isExitIntent = isExitIntent;

        if (preselectedModel) {
            popupState.model = preselectedModel;
            const hidModel = document.getElementById('popup_hidden_model');
            if (hidModel) hidModel.value = preselectedModel;
        }

        // Customize Header for Exit Intent vs Normal
        if (isExitIntent) {
            if (popupModalHeading) popupModalHeading.textContent = "Before You Go — Check Your iPhone's Value";
            if (popupModalSubheading) popupModalSubheading.textContent = "Get a quick resale estimate before you leave.";
            if (popupSubmitBtn) {
                const btnSpan = popupSubmitBtn.querySelector('span');
                if (btnSpan) btnSpan.textContent = "CHECK MY VALUE →";
            }
        } else {
            if (popupModalHeading) popupModalHeading.textContent = "How Much Is Your iPhone Worth?";
            if (popupModalSubheading) popupModalSubheading.textContent = "Get your estimated resale value in under 60 seconds.";
            if (popupSubmitBtn) {
                const btnSpan = popupSubmitBtn.querySelector('span');
                if (btnSpan) btnSpan.textContent = "GET MY IPHONE VALUE →";
            }
        }

        // Start at Step 1 or Step 2 if model is already selected
        setPopupStep(preselectedModel ? 2 : 1);

        leadPopupModal.style.display = 'flex';
        void leadPopupModal.offsetHeight;
        leadPopupModal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeMultiStepPopup() {
        if (!leadPopupModal) return;
        leadPopupModal.classList.remove('active');
        document.body.style.overflow = '';
        setTimeout(() => {
            if (!leadPopupModal.classList.contains('active')) {
                leadPopupModal.style.display = 'none';
            }
        }, 280);
    }

    if (leadPopupModal) {
        // Close Button & Backdrop Listeners
        if (leadModalCloseBtn) {
            leadModalCloseBtn.addEventListener('click', closeMultiStepPopup);
        }
        if (leadModalBackdrop) {
            leadModalBackdrop.addEventListener('click', closeMultiStepPopup);
        }
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && leadPopupModal.classList.contains('active')) {
                closeMultiStepPopup();
            }
        });

        // ==========================================
        // STEP 1: Search & Model Card Selection
        // ==========================================
        const popupModelSearch = document.getElementById('popup-model-search-input');
        const popupSearchClear = document.getElementById('popup-search-clear');
        const popupModelCards  = document.querySelectorAll('.popup-model-card');
        const popupModelEmpty  = document.getElementById('popup-model-empty');

        if (popupModelSearch) {
            popupModelSearch.addEventListener('input', (e) => {
                const query = e.target.value.toLowerCase().trim();
                const tokens = query.split(/\s+/).filter(Boolean);
                let matchCount = 0;

                if (popupSearchClear) {
                    popupSearchClear.style.display = query ? 'block' : 'none';
                }

                popupModelCards.forEach(card => {
                    const name = (card.getAttribute('data-name') || '').toLowerCase();
                    const series = (card.getAttribute('data-series') || '').toLowerCase();
                    const haystack = `${name} ${series} sell apple iphone resale buyback price value`;

                    const matches = tokens.length === 0 || tokens.every(t => haystack.includes(t));
                    if (matches) {
                        card.style.display = 'flex';
                        matchCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                if (popupModelEmpty) {
                    popupModelEmpty.style.display = matchCount === 0 ? 'block' : 'none';
                }
            });

            if (popupSearchClear) {
                popupSearchClear.addEventListener('click', () => {
                    popupModelSearch.value = '';
                    popupModelSearch.dispatchEvent(new Event('input'));
                    popupModelSearch.focus();
                });
            }
        }

        popupModelCards.forEach(card => {
            card.addEventListener('click', () => {
                const name = card.getAttribute('data-name');
                if (name) {
                    popupState.model = name;
                    popupModelCards.forEach(c => c.classList.toggle('active', c === card));
                    setPopupStep(2);
                }
            });
        });

        // ==========================================
        // STEP 2: Storage Selection
        // ==========================================
        const storageCards = document.querySelectorAll('.storage-card');
        storageCards.forEach(card => {
            card.addEventListener('click', () => {
                const storage = card.getAttribute('data-storage') || '128 GB';
                popupState.storage = storage;
                storageCards.forEach(c => c.classList.toggle('active', c === card));
                setPopupStep(3);
            });
        });

        const btnBackTo1 = document.getElementById('popup-back-to-1');
        if (btnBackTo1) {
            btnBackTo1.addEventListener('click', () => setPopupStep(1));
        }

        const btnNextTo3 = document.getElementById('popup-next-to-3');
        if (btnNextTo3) {
            btnNextTo3.addEventListener('click', () => setPopupStep(3));
        }

        // ==========================================
        // STEP 3: Condition Selection
        // ==========================================
        const conditionCards = document.querySelectorAll('.condition-card');
        conditionCards.forEach(card => {
            card.addEventListener('click', () => {
                const cond = card.getAttribute('data-condition') || 'Good';
                const mult = parseFloat(card.getAttribute('data-mult') || 0.88);
                popupState.condition = cond;
                popupState.conditionMult = mult;
                conditionCards.forEach(c => c.classList.toggle('active', c === card));
                setPopupStep(4);
            });
        });

        const btnBackTo2 = document.getElementById('popup-back-to-2');
        if (btnBackTo2) {
            btnBackTo2.addEventListener('click', () => setPopupStep(2));
        }

        const btnNextTo4 = document.getElementById('popup-next-to-4');
        if (btnNextTo4) {
            btnNextTo4.addEventListener('click', () => setPopupStep(4));
        }

        // ==========================================
        // STEP 4: Lead Submission & Duplicate Protection
        // ==========================================
        const btnBackTo3 = document.getElementById('popup-back-to-3');
        if (btnBackTo3) {
            btnBackTo3.addEventListener('click', () => setPopupStep(3));
        }

        const btnTryAgain = document.getElementById('popup-try-again-btn');
        if (btnTryAgain) {
            btnTryAgain.addEventListener('click', () => {
                setPopupStep(4);
            });
        }

        if (popupLeadForm) {
            popupLeadForm.addEventListener('submit', (e) => {
                e.preventDefault();

                const nameInput  = document.getElementById('popup_full_name');
                const phoneInput = document.getElementById('popup_phone_number');
                const errName    = document.getElementById('popup-err-name');
                const errPhone   = document.getElementById('popup-err-phone');

                let isValid = true;

                // Validate Name (min 2 chars)
                const nameVal = nameInput ? nameInput.value.trim() : '';
                if (!nameVal || nameVal.length < 2) {
                    isValid = false;
                    if (nameInput) nameInput.classList.add('input-error');
                    if (errName) {
                        errName.textContent = 'Please enter your full name (at least 2 characters).';
                        errName.style.display = 'block';
                    }
                } else {
                    if (nameInput) nameInput.classList.remove('input-error');
                    if (errName) errName.style.display = 'none';
                }

                // Validate Phone (10 digits)
                const phoneVal = phoneInput ? phoneInput.value.trim().replace(/[^0-9]/g, '') : '';
                if (!phoneVal || phoneVal.length < 10) {
                    isValid = false;
                    if (phoneInput) phoneInput.classList.add('input-error');
                    if (errPhone) {
                        errPhone.textContent = 'Please enter a valid 10-digit mobile number.';
                        errPhone.style.display = 'block';
                    }
                } else {
                    if (phoneInput) phoneInput.classList.remove('input-error');
                    if (errPhone) errPhone.style.display = 'none';
                }

                if (!isValid) return;

                // Duplicate Protection: Disable Submit Button Immediately
                if (popupSubmitBtn) {
                    popupSubmitBtn.disabled = true;
                    popupSubmitBtn.innerHTML = '<span>Processing valuation...</span>';
                }

                // Capture URL UTM Parameters
                const urlParams = new URLSearchParams(window.location.search);
                const utmSrcElem = document.getElementById('popup_utm_source');
                const utmMedElem = document.getElementById('popup_utm_medium');
                const utmCmpElem = document.getElementById('popup_utm_campaign');

                if (utmSrcElem) utmSrcElem.value = urlParams.get('utm_source') || (document.referrer ? (document.referrer.includes('google') ? 'Google Organic' : 'Referral') : 'Direct');
                if (utmMedElem) utmMedElem.value = urlParams.get('utm_medium') || 'Organic';
                if (utmCmpElem) utmCmpElem.value = urlParams.get('utm_campaign') || 'None';

                const formData = new FormData(popupLeadForm);

                fetch('forms/submit.php', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Mark Lead Submitted in session
                        sessionStorage.setItem('cs_lead_submitted', '1');

                        // Hide progress & header block
                        if (popupProgressHeader) popupProgressHeader.style.display = 'none';
                        if (popupHeaderBlock) popupHeaderBlock.style.display = 'none';

                        // Hide Step Panels
                        for (let i = 1; i <= 4; i++) {
                            const p = document.getElementById(`popup-panel-${i}`);
                            if (p) p.classList.remove('active');
                        }

                        // Populate and Show Success Panel
                        const popSuccModel = document.getElementById('pop-succ-model');
                        const popSuccStorage = document.getElementById('pop-succ-storage');
                        const popSuccCondition = document.getElementById('pop-succ-condition');
                        const popSuccEstimate = document.getElementById('pop-succ-estimate');
                        const popupWaBtn = document.getElementById('popup-wa-continue-btn');

                        if (popSuccModel) popSuccModel.textContent = popupState.model;
                        if (popSuccStorage) popSuccStorage.textContent = popupState.storage;
                        if (popSuccCondition) popSuccCondition.textContent = popupState.condition;
                        if (popSuccEstimate) popSuccEstimate.textContent = `₹${popupState.estimatedPrice.toLocaleString('en-IN')}`;
                        if (popupWaBtn && data.whatsapp_direct_url) {
                            popupWaBtn.href = data.whatsapp_direct_url;
                        }

                        const successPanel = document.getElementById('popup-panel-success');
                        if (successPanel) {
                            successPanel.style.display = 'block';
                            successPanel.classList.add('active');
                        }
                    } else {
                        // Show Failure Panel
                        showFailureScreen();
                    }
                })
                .catch(() => {
                    showFailureScreen();
                })
                .finally(() => {
                    if (popupSubmitBtn) {
                        popupSubmitBtn.disabled = false;
                        popupSubmitBtn.innerHTML = '<span>GET MY IPHONE VALUE &rarr;</span>';
                    }
                });

                function showFailureScreen() {
                    if (popupProgressHeader) popupProgressHeader.style.display = 'none';
                    if (popupHeaderBlock) popupHeaderBlock.style.display = 'none';

                    for (let i = 1; i <= 4; i++) {
                        const p = document.getElementById(`popup-panel-${i}`);
                        if (p) p.classList.remove('active');
                    }

                    const failurePanel = document.getElementById('popup-panel-failure');
                    if (failurePanel) {
                        failurePanel.style.display = 'block';
                        failurePanel.classList.add('active');
                    }
                }
            });
        }

        // ==========================================
        // TRIGGER 1: Global CTA Button Interceptor
        // ==========================================
        const valuationCtaLinks = document.querySelectorAll('a[href="#valuation"], .hero-cta-wrapper a, #mobile-sticky-valuation-btn');
        valuationCtaLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                // If link is clicked, smoothly open multi-step popup
                e.preventDefault();
                openMultiStepPopup(false);
            });
        });

        // ==========================================
        // TRIGGER 2: Desktop Exit Intent (Once per session)
        // ==========================================
        document.addEventListener('mouseleave', (e) => {
            if (e.clientY <= 10) {
                if (!sessionStorage.getItem('cs_exit_shown') && !sessionStorage.getItem('cs_lead_submitted')) {
                    sessionStorage.setItem('cs_exit_shown', '1');
                    openMultiStepPopup(true);
                }
            }
        });

        // ==========================================
        // TRIGGER 3: Mobile Non-Aggressive Timer (Once per session after 25s)
        // ==========================================
        setTimeout(() => {
            if (!sessionStorage.getItem('cs_popup_shown') && !sessionStorage.getItem('cs_lead_submitted')) {
                sessionStorage.setItem('cs_popup_shown', '1');
                openMultiStepPopup(false);
            }
        }, 25000);
    }

    // ============================================================
    // FREE CONSULTATION MODAL & INTERACTIVE MAP PREVIEW
    // ============================================================
    const mapTrigger = document.getElementById('contact-map-trigger');
    const consultModal = document.getElementById('consultationModal');
    const consultBackdrop = document.getElementById('consultModalBackdrop');
    const consultCloseBtn = document.getElementById('consultModalCloseBtn');
    const consultForm = document.getElementById('consultation-lead-form');
    const consultSubmitBtn = document.getElementById('consult-submit-btn');
    const consultStatus = document.getElementById('consult-form-status');

    function openConsultationModal() {
        if (!consultModal) return;
        consultModal.classList.add('active');
        consultModal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        
        if (consultStatus) {
            consultStatus.style.display = 'none';
            consultStatus.innerHTML = '';
        }
        
        setTimeout(() => {
            const firstInput = document.getElementById('consult_full_name');
            if (firstInput) firstInput.focus();
        }, 100);
    }

    function closeConsultationModal() {
        if (!consultModal) return;
        consultModal.classList.remove('active');
        consultModal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    if (mapTrigger) {
        mapTrigger.addEventListener('click', (e) => {
            e.preventDefault();
            openConsultationModal();
        });

        mapTrigger.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                openConsultationModal();
            }
        });
    }

    if (consultCloseBtn) {
        consultCloseBtn.addEventListener('click', closeConsultationModal);
    }

    if (consultBackdrop) {
        consultBackdrop.addEventListener('click', closeConsultationModal);
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && consultModal && consultModal.classList.contains('active')) {
            closeConsultationModal();
        }
    });

    if (consultForm) {
        consultForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const errName = document.getElementById('err_consult_name');
            const errPhone = document.getElementById('err_consult_phone');
            const errEmail = document.getElementById('err_consult_email');
            const errProblem = document.getElementById('err_consult_problem');
            
            if (errName) errName.textContent = '';
            if (errPhone) errPhone.textContent = '';
            if (errEmail) errEmail.textContent = '';
            if (errProblem) errProblem.textContent = '';
            if (consultStatus) {
                consultStatus.style.display = 'none';
                consultStatus.innerHTML = '';
            }

            const nameInput = document.getElementById('consult_full_name');
            const phoneInput = document.getElementById('consult_phone_number');
            const emailInput = document.getElementById('consult_email');
            const problemInput = document.getElementById('consult_problem');

            const nameVal = nameInput ? nameInput.value.trim() : '';
            const phoneVal = phoneInput ? phoneInput.value.trim() : '';
            const emailVal = emailInput ? emailInput.value.trim() : '';
            const problemVal = problemInput ? problemInput.value.trim() : '';

            let hasError = false;

            if (nameVal.length < 2) {
                if (errName) errName.textContent = 'Please enter your full name (at least 2 characters).';
                if (!hasError && nameInput) nameInput.focus();
                hasError = true;
            }

            const cleanDigits = phoneVal.replace(/[^0-9]/g, '');
            const indianPhoneRegex = /^[6-9]\d{9}$/;
            if (!indianPhoneRegex.test(cleanDigits)) {
                if (errPhone) errPhone.textContent = 'Please enter a valid 10-digit mobile number.';
                if (!hasError && phoneInput) phoneInput.focus();
                hasError = true;
            }

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(emailVal)) {
                if (errEmail) errEmail.textContent = 'Please enter a valid email address.';
                if (!hasError && emailInput) emailInput.focus();
                hasError = true;
            }

            if (problemVal.length < 3) {
                if (errProblem) errProblem.textContent = 'Please describe your problem or enquiry.';
                if (!hasError && problemInput) problemInput.focus();
                hasError = true;
            }

            if (hasError) return;

            const formData = new FormData(consultForm);
            if (consultSubmitBtn) {
                consultSubmitBtn.disabled = true;
                consultSubmitBtn.innerHTML = '<span>Submitting Request...</span>';
            }

            try {
                const response = await fetch('forms/consultation.php', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                const result = await response.json();

                if (response.ok && result.status === 'success') {
                    if (consultStatus) {
                        consultStatus.className = 'form-status-alert alert-success';
                        consultStatus.style.display = 'block';
                        consultStatus.style.backgroundColor = '#E8F5E9';
                        consultStatus.style.color = '#1E8E3E';
                        consultStatus.style.border = '1px solid #A5D6A7';
                        consultStatus.innerHTML = `<strong>✓ Request Submitted!</strong><br>${result.message || 'Thank you! Our team will contact you shortly.'}`;
                    }
                    consultForm.reset();
                    if (consultSubmitBtn) {
                        consultSubmitBtn.innerHTML = '<span>✓ Request Sent</span>';
                    }
                    setTimeout(() => {
                        closeConsultationModal();
                        if (consultSubmitBtn) {
                            consultSubmitBtn.disabled = false;
                            consultSubmitBtn.innerHTML = '<span>Get Free Consultation &rarr;</span>';
                        }
                    }, 3500);
                } else {
                    if (consultStatus) {
                        consultStatus.className = 'form-status-alert alert-danger';
                        consultStatus.style.display = 'block';
                        consultStatus.style.backgroundColor = '#FFEBEE';
                        consultStatus.style.color = '#E53935';
                        consultStatus.style.border = '1px solid #FFCDD2';
                        consultStatus.innerHTML = `<strong>Submission Error:</strong> ${result.message || 'Please check your inputs and try again.'}`;
                    }
                    if (consultSubmitBtn) {
                        consultSubmitBtn.disabled = false;
                        consultSubmitBtn.innerHTML = '<span>Get Free Consultation &rarr;</span>';
                    }
                }
            } catch (err) {
                if (consultStatus) {
                    consultStatus.className = 'form-status-alert alert-danger';
                    consultStatus.style.display = 'block';
                    consultStatus.style.backgroundColor = '#FFEBEE';
                    consultStatus.style.color = '#E53935';
                    consultStatus.style.border = '1px solid #FFCDD2';
                    consultStatus.innerHTML = '<strong>Connection Error:</strong> Unable to submit form. Please try again.';
                }
                if (consultSubmitBtn) {
                    consultSubmitBtn.disabled = false;
                    consultSubmitBtn.innerHTML = '<span>Get Free Consultation &rarr;</span>';
                }
            }
        });
    }

    // ============================================================
    // 19. COMPACT SMART EXCHANGE / DIAGNOSTIC ENGINE CONTROLLER
    // ============================================================
    const openSmartExBtn = document.getElementById('openSmartExchangeBtn');
    const smartExModal = document.getElementById('smartExchangeModal');
    const smartExBackdrop = document.getElementById('smartExchangeBackdrop');
    const smartExCloseBtn = document.getElementById('smartExchangeCloseBtn');
    const smartExModelSelect = document.getElementById('smartExchangeModelSelect');
    const smartExDeviceName = document.getElementById('smartExchangeDeviceName');
    const diagReportModelName = document.getElementById('diagReportModelName');
    const diagReportEstimatedVal = document.getElementById('diagReportEstimatedVal');
    const totalPassCountElem = document.getElementById('totalPassCount');
    const totalFailCountElem = document.getElementById('totalFailCount');
    const diagRequestPickupBtn = document.getElementById('diagRequestPickupBtn');
    const diagWhatsAppBtn = document.getElementById('diagWhatsAppBtn');

    function openSmartExchangeModal() {
        if (!smartExModal) return;
        smartExModal.classList.add('active');
        smartExModal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        recalculateDiagnostics();
    }

    function closeSmartExchangeModal() {
        if (!smartExModal) return;
        smartExModal.classList.remove('active');
        smartExModal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    if (openSmartExBtn) {
        openSmartExBtn.addEventListener('click', (e) => {
            e.preventDefault();
            openSmartExchangeModal();
        });
    }

    if (smartExCloseBtn) {
        smartExCloseBtn.addEventListener('click', closeSmartExchangeModal);
    }

    if (smartExBackdrop) {
        smartExBackdrop.addEventListener('click', closeSmartExchangeModal);
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && smartExModal && smartExModal.classList.contains('active')) {
            closeSmartExchangeModal();
        }
    });

    // Accordion Category Header Toggles
    const diagCatHeaders = document.querySelectorAll('.diag-cat-header');
    diagCatHeaders.forEach(header => {
        header.addEventListener('click', (e) => {
            e.preventDefault();
            const card = header.closest('.diag-cat-card');
            if (card) {
                const isActive = card.classList.contains('active');
                card.classList.toggle('active', !isActive);
                header.setAttribute('aria-expanded', String(!isActive));
            }
        });
    });

    // Toggle individual test items
    const diagTestRows = document.querySelectorAll('.diag-test-row');
    diagTestRows.forEach(row => {
        const toggleBtn = row.querySelector('.diag-toggle-btn');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                toggleTestRow(row);
            });
        }
        row.addEventListener('click', (e) => {
            if (e.target.closest('.diag-toggle-btn')) return;
            toggleTestRow(row);
        });
    });

    function toggleTestRow(row) {
        const isPass = row.getAttribute('data-status') === 'pass';
        const newStatus = isPass ? 'fail' : 'pass';
        row.setAttribute('data-status', newStatus);

        const toggleBtn = row.querySelector('.diag-toggle-btn');
        if (toggleBtn) {
            toggleBtn.className = `diag-toggle-btn ${newStatus}`;
            const indicator = toggleBtn.querySelector('.diag-check-indicator');
            if (indicator) {
                indicator.textContent = (newStatus === 'pass') ? '✓' : '✕';
            }
        }

        recalculateDiagnostics();
    }

    function recalculateDiagnostics() {
        let totalPass = 0;
        let totalFail = 0;
        let totalPenalty = 0;

        const categories = document.querySelectorAll('.diag-cat-card');
        categories.forEach(cat => {
            const catType = cat.getAttribute('data-category');
            const rows = cat.querySelectorAll('.diag-test-row');
            let catPass = 0;
            let catFail = 0;

            rows.forEach(r => {
                const status = r.getAttribute('data-status');
                const penalty = parseInt(r.getAttribute('data-penalty') || '1000', 10);
                if (status === 'pass') {
                    catPass++;
                    totalPass++;
                } else {
                    catFail++;
                    totalFail++;
                    totalPenalty += penalty;
                }
            });

            const pill = document.getElementById(`stat-pill-${catType}`);
            if (pill) {
                pill.innerHTML = `<span class="stat-pass">${catPass} Passed</span> | <span class="stat-fail">${catFail} Failed</span>`;
            }
        });

        if (totalPassCountElem) totalPassCountElem.textContent = totalPass;
        if (totalFailCountElem) totalFailCountElem.textContent = totalFail;

        let basePrice = 42500;
        let modelLabel = 'iPhone 13 Pro • 128 GB';
        let fullModelName = 'Apple iPhone 13 Pro';
        let selectedStorage = '128GB';

        if (smartExModelSelect) {
            const selectedOpt = smartExModelSelect.options[smartExModelSelect.selectedIndex];
            if (selectedOpt) {
                const parts = selectedOpt.value.split('|');
                fullModelName = parts[0];
                basePrice = parseInt(parts[1] || '42500', 10);
                modelLabel = selectedOpt.textContent;
                selectedStorage = selectedOpt.getAttribute('data-storage') || '128GB';
            }
        }

        const minVal = Math.round(basePrice * 0.35);
        const finalEstVal = Math.max(minVal, basePrice - totalPenalty);
        const formattedVal = '₹' + finalEstVal.toLocaleString('en-IN');

        if (diagReportEstimatedVal) diagReportEstimatedVal.textContent = formattedVal;
        if (smartExDeviceName) smartExDeviceName.textContent = `${fullModelName} (${selectedStorage})`;
        if (diagReportModelName) diagReportModelName.textContent = modelLabel;

        if (diagWhatsAppBtn) {
            const waText = encodeURIComponent(`Hi CashSecond, I completed the Smart Exchange device check for my ${modelLabel} (${totalPass} Passed, ${totalFail} Failed). Estimated Exchange Value: ${formattedVal}. Please schedule free doorstep pickup.`);
            diagWhatsAppBtn.href = `https://wa.me/918976332211?text=${waText}`;
        }
    }

    if (smartExModelSelect) {
        smartExModelSelect.addEventListener('change', () => {
            recalculateDiagnostics();
        });
    }

    if (diagRequestPickupBtn) {
        diagRequestPickupBtn.addEventListener('click', (e) => {
            e.preventDefault();
            closeSmartExchangeModal();

            setTimeout(() => {
                if (typeof openConsultationModal === 'function') {
                    openConsultationModal();
                } else if (consultModal) {
                    consultModal.classList.add('active');
                    consultModal.setAttribute('aria-hidden', 'false');
                    document.body.style.overflow = 'hidden';
                }

                const problemField = document.getElementById('consult_problem');
                if (problemField) {
                    const currentModel = smartExModelSelect ? smartExModelSelect.options[smartExModelSelect.selectedIndex].textContent : 'iPhone 13 Pro 128GB';
                    const currentVal = diagReportEstimatedVal ? diagReportEstimatedVal.textContent : '₹42,500';
                    const passes = totalPassCountElem ? totalPassCountElem.textContent : '17';
                    const fails = totalFailCountElem ? totalFailCountElem.textContent : '2';
                    problemField.value = `Smart Exchange Diagnostic: ${currentModel} | ${passes} Passed, ${fails} Failed | Estimated Exchange Value: ${currentVal}. Please arrange doorstep pickup.`;
                }
            }, 300);
        });
    }
});
