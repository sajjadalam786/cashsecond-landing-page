/**
 * CashSecond - Apple-Inspired iPhone Buyback Client Engine
 * Features:
 * 1. Mobile Menu Drawer
 * 2. Multi-Token Live iPhone Search with Full Product Cards & Images
 * 3. Step 1 Model Filter with Instant Product Cards & Empty State
 * 4. 4-Step Valuation Calculator
 * 5. iPhone Series Triggers
 * 6. FAQ Accordion
 * 7. AJAX Lead Form Submission & Conversion Tracking
 */

document.addEventListener('DOMContentLoaded', () => {
    // --- State Variables ---
    let catalogData = null;
    let selectedModel = { 
        name: 'Apple iPhone 15', 
        id: 1341, 
        image: 'assets/images/phones/iphone-15.svg' 
    };
    let selectedVariant = { storage: '128 GB', ram: '6 GB', label: '128 GB' };
    let valuationAnswers = { screen: 'no', body: 'no', warranty: 'yes', calls: 'yes' };
    let currentCalculatedQuote = 39500;

    // Load iPhone catalog JSON asynchronously
    fetch('data/catalog.json')
        .then(res => res.json())
        .then(data => {
            catalogData = data;
        })
        .catch(err => console.error('Catalog load error:', err));

    // ============================================================
    // 1. MOBILE MENU DRAWER
    // ============================================================
    const menuToggleBtn = document.getElementById('mobile-menu-toggle');
    const mobileDrawer = document.getElementById('mobile-nav-drawer');

    if (menuToggleBtn && mobileDrawer) {
        menuToggleBtn.addEventListener('click', () => {
            mobileDrawer.classList.toggle('active');
        });

        // Close drawer when clicking any link
        document.querySelectorAll('.mobile-nav-item').forEach(item => {
            item.addEventListener('click', () => {
                mobileDrawer.classList.remove('active');
            });
        });
    }

    // ============================================================
    // 2. LIVE AUTOCOMPLETE IPHONE SEARCH WITH PRODUCT CARDS
    // ============================================================
    const searchInput = document.getElementById('global-phone-search');
    const searchClearBtn = document.getElementById('search-clear-btn');
    const searchDropdown = document.getElementById('search-autocomplete-results');
    let debounceTimer = null;

    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.trim();
            if (query.length > 0) {
                if (searchClearBtn) searchClearBtn.style.display = 'block';
            } else {
                if (searchClearBtn) searchClearBtn.style.display = 'none';
                if (searchDropdown) searchDropdown.style.display = 'none';
                return;
            }

            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                performLiveSearch(query);
            }, 100);
        });

        if (searchClearBtn) {
            searchClearBtn.addEventListener('click', () => {
                searchInput.value = '';
                searchClearBtn.style.display = 'none';
                if (searchDropdown) searchDropdown.style.display = 'none';
                searchInput.focus();
            });
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.hero-search-wrapper') && searchDropdown) {
                searchDropdown.style.display = 'none';
            }
        });
    }

    /**
     * Matches a search query against iPhone model names using token-based matching.
     * e.g. "16 pro" requires both "16" and "pro" in the model name.
     */
    function filterModelsByQuery(models, query) {
        if (!query || !models) return models || [];
        const cleanQuery = query.toLowerCase().replace(/[^a-z0-9\s]/g, ' ').trim();
        const tokens = cleanQuery.split(/\s+/).filter(t => t.length > 0);
        
        if (tokens.length === 0) return models;

        return models.filter(m => {
            const modelNameClean = m.product_name.toLowerCase().replace(/[^a-z0-9\s]/g, ' ');
            // If user searches "iphone 16", tokens are ['iphone', '16'].
            // Every token must match in the model name
            return tokens.every(token => modelNameClean.includes(token));
        });
    }

    function performLiveSearch(query) {
        if (!catalogData || !catalogData.sell_brands || !catalogData.sell_brands.Apple) {
            // Fallback from DOM if catalogData hasn't loaded
            const domCards = Array.from(document.querySelectorAll('.model-product-card'));
            const localModels = domCards.map(c => ({
                product_name: c.getAttribute('data-name'),
                product_id: c.getAttribute('data-id'),
                image: c.getAttribute('data-image')
            }));
            const matches = filterModelsByQuery(localModels, query);
            renderSearchResults(matches, query);
            return;
        }

        const models = catalogData.sell_brands.Apple;
        const matches = filterModelsByQuery(models, query);
        renderSearchResults(matches, query);
    }

    function renderSearchResults(matches, query) {
        if (!searchDropdown) return;

        if (matches.length === 0) {
            searchDropdown.innerHTML = `
                <div class="search-empty-state">
                    <h4 class="search-empty-title">No iPhone model found</h4>
                    <p class="search-empty-sub">Try searching by model name, such as <strong>iPhone 16</strong> or <strong>iPhone 15 Pro</strong>.</p>
                </div>
            `;
            searchDropdown.style.display = 'block';
            return;
        }

        let html = `
            <div class="search-section-header">
                <span class="search-section-title">Matching Apple iPhones (${matches.length})</span>
                <span style="font-size:0.75rem; color:var(--color-text-muted);">Select to check value</span>
            </div>
            <div class="search-products-grid">
        `;

        matches.forEach(m => {
            const imgSrc = m.image || 'assets/images/phones/iphone-15.svg';
            html += `
                <div class="search-product-card" data-name="${escapeHtml(m.product_name)}" data-id="${m.product_id}" data-image="${escapeHtml(imgSrc)}">
                    <div class="search-product-img-wrap">
                        <img src="${escapeHtml(imgSrc)}" alt="${escapeHtml(m.product_name)} valuation and buyback" class="search-product-img" loading="lazy" width="75" height="75">
                    </div>
                    <h4 class="search-product-name">${escapeHtml(m.product_name)}</h4>
                    <span class="search-product-cta">Check Value &rarr;</span>
                </div>
            `;
        });

        html += `</div>`;
        searchDropdown.innerHTML = html;
        searchDropdown.style.display = 'block';

        // Attach click events to each product card result
        searchDropdown.querySelectorAll('.search-product-card').forEach(card => {
            card.addEventListener('click', () => {
                const name = card.getAttribute('data-name');
                const id = card.getAttribute('data-id');
                const image = card.getAttribute('data-image');
                searchDropdown.style.display = 'none';
                if (searchClearBtn) searchClearBtn.style.display = 'none';
                if (searchInput) searchInput.value = name;
                startValuationWithModel(name, id, image);
            });
        });
    }

    // ============================================================
    // 3. IPHONE VALUATION WIZARD
    // ============================================================
    const wizardPanels = {
        1: document.getElementById('wizard-step-1'),
        2: document.getElementById('wizard-step-2'),
        3: document.getElementById('wizard-step-3'),
        4: document.getElementById('wizard-step-4'),
    };

    const stepNodes = document.querySelectorAll('.step-node');

    function goToWizardStep(stepNum) {
        for (let i = 1; i <= 4; i++) {
            if (wizardPanels[i]) {
                wizardPanels[i].classList.toggle('active', i === stepNum);
            }
        }
        stepNodes.forEach(node => {
            const nStep = parseInt(node.getAttribute('data-step'), 10);
            node.classList.toggle('active', nStep === stepNum);
            node.classList.toggle('completed', nStep < stepNum);
        });

        const wizardCard = document.getElementById('valuation-wizard-card');
        if (wizardCard && stepNum > 1) {
            wizardCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    // Back Buttons
    document.querySelectorAll('.btn-back-step').forEach(btn => {
        btn.addEventListener('click', () => {
            const targetStep = parseInt(btn.getAttribute('data-goto'), 10) || 1;
            goToWizardStep(targetStep);
        });
    });

    // Step 1: Model Selection & Instant Search Filter
    const modelFilterInput = document.getElementById('model-filter-input');
    const modelsListContainer = document.getElementById('models-list-container');
    const modelFilterEmpty = document.getElementById('model-filter-empty');

    if (modelFilterInput && modelsListContainer) {
        modelFilterInput.addEventListener('input', (e) => {
            const q = e.target.value.trim();
            filterStep1ModelCards(q);
        });
    }

    function filterStep1ModelCards(query) {
        if (!modelsListContainer) return;
        const cards = modelsListContainer.querySelectorAll('.model-product-card');
        
        if (!query) {
            cards.forEach(c => c.style.display = 'flex');
            if (modelFilterEmpty) modelFilterEmpty.style.display = 'none';
            return;
        }

        const cleanQuery = query.toLowerCase().replace(/[^a-z0-9\s]/g, ' ').trim();
        const tokens = cleanQuery.split(/\s+/).filter(t => t.length > 0);

        let visibleCount = 0;
        cards.forEach(card => {
            const name = (card.getAttribute('data-name') || '').toLowerCase().replace(/[^a-z0-9\s]/g, ' ');
            const isMatch = tokens.every(token => name.includes(token));
            if (isMatch) {
                card.style.display = 'flex';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        if (modelFilterEmpty) {
            modelFilterEmpty.style.display = visibleCount === 0 ? 'block' : 'none';
        }
    }

    // Attach click events on Step 1 model cards
    document.querySelectorAll('.model-product-card').forEach(card => {
        card.addEventListener('click', () => {
            const name = card.getAttribute('data-name');
            const id = card.getAttribute('data-id');
            const image = card.getAttribute('data-image');
            selectModelAndProceed(name, id, image);
        });
    });

    function selectModelAndProceed(name, id, image) {
        selectedModel = { 
            name: name, 
            id: id, 
            image: image || 'assets/images/phones/iphone-15.svg' 
        };
        const badge = document.getElementById('badge-selected-model');
        if (badge) badge.textContent = name;
        goToWizardStep(2);
    }

    function startValuationWithModel(name, id, image) {
        selectedModel = { 
            name: name, 
            id: id || 0, 
            image: image || 'assets/images/phones/iphone-15.svg' 
        };
        const badge = document.getElementById('badge-selected-model');
        if (badge) badge.textContent = name;

        const wizardCard = document.getElementById('valuation');
        if (wizardCard) {
            wizardCard.scrollIntoView({ behavior: 'smooth' });
        }
        goToWizardStep(2);
    }

    // Series Cards Direct Trigger
    document.querySelectorAll('.series-card').forEach(card => {
        card.addEventListener('click', () => {
            const series = card.getAttribute('data-series');
            if (modelFilterInput) {
                modelFilterInput.value = series;
                filterStep1ModelCards(series);
            }
            const wizardCard = document.getElementById('valuation');
            if (wizardCard) {
                wizardCard.scrollIntoView({ behavior: 'smooth' });
            }
            goToWizardStep(1);
        });
    });

    // Step 2: Storage Variant Selection
    const variantChips = document.querySelectorAll('.variant-chip');
    variantChips.forEach(chip => {
        chip.addEventListener('click', () => {
            variantChips.forEach(c => c.classList.remove('active'));
            chip.classList.add('active');
            selectedVariant = {
                storage: chip.getAttribute('data-storage') || '128 GB',
                ram: chip.getAttribute('data-ram') || '6 GB',
                label: chip.getAttribute('data-storage') || '128 GB'
            };
        });
    });

    const btnConfirmVariant = document.getElementById('btn-confirm-variant');
    if (btnConfirmVariant) {
        btnConfirmVariant.addEventListener('click', () => {
            const specBadge = document.getElementById('badge-selected-spec');
            if (specBadge) specBadge.textContent = `${selectedModel.name} (${selectedVariant.storage})`;
            goToWizardStep(3);
        });
    }

    // Step 3: Calculate Quote from 4 Questions
    const btnCalculateQuote = document.getElementById('btn-calculate-quote');
    if (btnCalculateQuote) {
        btnCalculateQuote.addEventListener('click', () => {
            const screenAns = document.querySelector('input[name="q_screen"]:checked')?.value || 'no';
            const bodyAns = document.querySelector('input[name="q_body"]:checked')?.value || 'no';
            const warrantyAns = document.querySelector('input[name="q_warranty"]:checked')?.value || 'yes';
            const callsAns = document.querySelector('input[name="q_calls"]:checked')?.value || 'yes';

            valuationAnswers = {
                screen: screenAns,
                body: bodyAns,
                warranty: warrantyAns,
                calls: callsAns
            };

            calculateAndDisplayFinalQuote();
            goToWizardStep(4);
        });
    }

    function calculateAndDisplayFinalQuote() {
        if (!selectedModel) {
            selectedModel = { name: 'Apple iPhone 15', id: 1341, image: 'assets/images/phones/iphone-15.svg' };
        }

        let baseQuote = 39500;
        const nameLower = selectedModel.name.toLowerCase();

        if (nameLower.includes('16 pro max') || nameLower.includes('17 pro max')) {
            baseQuote = 78000;
        } else if (nameLower.includes('15 pro max') || nameLower.includes('16 pro') || nameLower.includes('17 pro')) {
            baseQuote = 62000;
        } else if (nameLower.includes('15 pro') || nameLower.includes('14 pro max') || nameLower.includes('17 air') || nameLower.includes('17')) {
            baseQuote = 48000;
        } else if (nameLower.includes('15') || nameLower.includes('14 pro') || nameLower.includes('16e') || nameLower.includes('17e')) {
            baseQuote = 39500;
        } else if (nameLower.includes('14') || nameLower.includes('13 pro')) {
            baseQuote = 32000;
        } else if (nameLower.includes('13') || nameLower.includes('12 pro')) {
            baseQuote = 24500;
        } else if (nameLower.includes('12') || nameLower.includes('11 pro')) {
            baseQuote = 18500;
        } else if (nameLower.includes('11')) {
            baseQuote = 14500;
        } else if (nameLower.includes('xr') || nameLower.includes('xs')) {
            baseQuote = 10500;
        } else if (nameLower.includes('se')) {
            baseQuote = 12000;
        } else if (nameLower.includes('8')) {
            baseQuote = 7500;
        } else {
            baseQuote = 9500;
        }

        // Storage additions
        if (selectedVariant.storage.includes('256')) baseQuote += 2500;
        if (selectedVariant.storage.includes('512')) baseQuote += 5000;
        if (selectedVariant.storage.includes('1 TB') || selectedVariant.storage.includes('1TB')) baseQuote += 8500;

        // Diagnostic condition deductions
        let quoteMultiplier = 1.0;
        if (valuationAnswers.screen === 'yes') quoteMultiplier -= 0.20;
        if (valuationAnswers.body === 'yes') quoteMultiplier -= 0.12;
        if (valuationAnswers.warranty === 'no') quoteMultiplier -= 0.08;
        if (valuationAnswers.calls === 'no') quoteMultiplier -= 0.25;

        currentCalculatedQuote = Math.round(baseQuote * quoteMultiplier);
        const formattedPrice = '₹' + currentCalculatedQuote.toLocaleString('en-IN');

        // Update UI
        const deviceTitle = document.getElementById('quote-display-device');
        const deviceVariant = document.getElementById('quote-display-variant');
        const priceAmount = document.getElementById('quote-display-price');

        if (deviceTitle) deviceTitle.textContent = selectedModel.name;
        if (deviceVariant) {
            const condSummary = (valuationAnswers.screen === 'no' && valuationAnswers.body === 'no') ? 'Flawless Condition' : 'Good Used Condition';
            deviceVariant.textContent = `${selectedVariant.storage} Storage • ${condSummary}`;
        }
        if (priceAmount) priceAmount.textContent = `${formattedPrice}*`;

        // Update WhatsApp Deep Link
        const quoteWaBtn = document.getElementById('quote-whatsapp-btn');
        if (quoteWaBtn) {
            const msg = `Hi CashSecond, I checked the valuation for my ${selectedModel.name} (${selectedVariant.storage}) on your website. Estimated Value: ${formattedPrice}. Condition: Screen ${valuationAnswers.screen === 'no' ? 'Flawless' : 'Flaw'}, Body ${valuationAnswers.body === 'no' ? 'Flawless' : 'Dents'}, Warranty ${valuationAnswers.warranty === 'yes' ? 'Valid' : 'Expired'}, Calls/Face ID ${valuationAnswers.calls === 'yes' ? 'Working' : 'Issue'}. Please book my free doorstep pickup in Mumbai.`;
            quoteWaBtn.href = `https://wa.me/918976332211?text=${encodeURIComponent(msg)}`;
        }

        // Form Prefill Button
        const quoteFormBtn = document.getElementById('quote-form-btn');
        if (quoteFormBtn) {
            quoteFormBtn.onclick = () => {
                const leadSection = document.getElementById('enquire');
                const modelInput = document.getElementById('form_phone_model');
                const msgInput = document.getElementById('form_message');

                if (modelInput) modelInput.value = `${selectedModel.name} ${selectedVariant.storage}`;
                if (msgInput) msgInput.value = `Estimated Quote: ${formattedPrice} | Screen: ${valuationAnswers.screen === 'no' ? 'OK' : 'Flaw'}, Body: ${valuationAnswers.body === 'no' ? 'OK' : 'Defect'}, Warranty: ${valuationAnswers.warranty === 'yes' ? 'Valid' : 'Expired'}`;

                if (leadSection) {
                    leadSection.scrollIntoView({ behavior: 'smooth' });
                }
            };
        }
    }

    // ============================================================
    // 5. FAQ ACCORDION
    // ============================================================
    const faqItems = document.querySelectorAll('.faq-item');
    faqItems.forEach(item => {
        const btn = item.querySelector('.faq-btn');
        if (btn) {
            btn.addEventListener('click', () => {
                const isOpen = item.classList.contains('active');
                faqItems.forEach(i => {
                    i.classList.remove('active');
                    const b = i.querySelector('.faq-btn');
                    if (b) b.setAttribute('aria-expanded', 'false');
                });

                if (!isOpen) {
                    item.classList.add('active');
                    btn.setAttribute('aria-expanded', 'true');
                }
            });
        }
    });

    // ============================================================
    // 6. AJAX LEAD FORM SUBMISSION
    // ============================================================
    const leadForm = document.getElementById('landing-lead-form');
    const submitBtn = document.getElementById('form-submit-btn');
    const formAlert = document.getElementById('form-status-alert');

    if (leadForm) {
        leadForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const nameInput = document.getElementById('form_full_name');
            const phoneInput = document.getElementById('form_phone_number');
            const modelInput = document.getElementById('form_phone_model');
            const consentInput = document.getElementById('form_consent');

            if (!nameInput.value.trim() || nameInput.value.trim().length < 2) {
                showAlert('Please enter your full name.', 'alert-error');
                nameInput.focus();
                return;
            }

            const phoneDigits = phoneInput.value.replace(/[^0-9]/g, '');
            if (phoneDigits.length < 10 || phoneDigits.length > 14) {
                showAlert('Please enter a valid 10-digit mobile number.', 'alert-error');
                phoneInput.focus();
                return;
            }

            if (!modelInput.value.trim()) {
                showAlert('Please specify your iPhone model & storage.', 'alert-error');
                modelInput.focus();
                return;
            }

            if (!consentInput.checked) {
                showAlert('Please accept consent to receive callback / WhatsApp.', 'alert-error');
                return;
            }

            // Send via AJAX
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span>Processing Valuation...</span>';
            }

            const formData = new FormData(leadForm);

            try {
                const res = await fetch(leadForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await res.json();

                if (res.ok && data.status === 'success') {
                    showAlert('✓ Thank you! Your iPhone valuation enquiry has been received. Our Mumbai specialist will contact you on WhatsApp/Call shortly.', 'alert-success');
                    leadForm.reset();
                } else {
                    showAlert(data.message || 'Submission error. Please contact us directly on WhatsApp.', 'alert-error');
                }
            } catch (err) {
                showAlert('Network error. Please WhatsApp us directly at +91 897633 2211.', 'alert-error');
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<span>Check My iPhone Value</span>';
                }
            }
        });
    }

    function showAlert(msg, className) {
        if (!formAlert) return;
        formAlert.textContent = msg;
        formAlert.className = `form-status-alert ${className}`;
        formAlert.style.display = 'block';
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
});
