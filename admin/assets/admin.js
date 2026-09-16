/**
 * CashSecond Admin Pricing Engine & Dashboard - Client Application
 * Real-time CSV pricing matrix editor with search, filters, inline price save, and deduction drawer
 */

(function () {
    'use strict';

    var allRows = [];
    var filteredRows = [];
    var currentEditingRow = null;
    var currentModelFilter = 'all';
    var currentStorageFilter = 'all';
    var searchQuery = '';

    // DOM Elements - Navigation & Mobile Drawer
    var sidebar = document.getElementById('csSidebar');
    var sidebarBackdrop = document.getElementById('sidebarBackdrop');
    var mobileMenuBtn = document.getElementById('mobileMenuBtn');
    var navBadgeTotal = document.getElementById('navBadgeTotal');

    // DOM Elements - Pricing Matrix Toolbar
    var tableBody = document.getElementById('productsTableBody');
    var searchInput = document.getElementById('productSearchInput');
    var searchClearBtn = document.getElementById('searchClearBtn');
    var modelFilterSelect = document.getElementById('modelFilterSelect');
    var storageFilterSelect = document.getElementById('storageFilterSelect');
    var btnResetFilters = document.getElementById('btnResetFilters');
    var tableFilterCount = document.getElementById('tableFilterCount');
    var btnRefresh = document.getElementById('btnRefresh');

    // Stats Elements
    var totalProductsCount = document.getElementById('totalProductsCount');
    var totalModelsCount = document.getElementById('totalModelsCount');
    var priceRangeCount = document.getElementById('priceRangeCount');
    var lastUpdatedText = document.getElementById('lastUpdatedText');
    var syncTimeBadge = document.getElementById('syncTimeBadge');

    // Modal Drawer Elements
    var editModal = document.getElementById('editProductModal');
    var modalProductName = document.getElementById('modalProductName');
    var modalProductIdTag = document.getElementById('modalProductIdTag');
    var btnCloseSheet = document.getElementById('btnCloseSheet');
    var btnCancelSheet = document.getElementById('btnCancelSheet');
    var btnSaveSheet = document.getElementById('btnSaveSheet');
    var sheetTabBtns = document.querySelectorAll('.sheet-tab-item');
    var sheetTabPanes = document.querySelectorAll('.sheet-tab-panel');

    /**
     * Show floating toast notification
     */
    function showToast(message, type) {
        type = type || 'success';
        var container = document.getElementById('toastStack');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toastStack';
            container.className = 'toast-stack';
            document.body.appendChild(container);
        }

        var toast = document.createElement('div');
        toast.className = 'toast-msg ' + type;
        toast.innerHTML = (type === 'success' ? '✅ ' : '⚠️ ') + message;
        container.appendChild(toast);

        setTimeout(function () {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(-10px)';
            toast.style.transition = 'all 0.3s ease';
            setTimeout(function () {
                if (toast.parentNode) toast.parentNode.removeChild(toast);
            }, 300);
        }, 3500);
    }

    /**
     * Mobile Sidebar Drawer Controls
     */
    function openMobileSidebar() {
        if (sidebar) sidebar.classList.add('open');
        if (sidebarBackdrop) sidebarBackdrop.classList.add('show');
    }

    function closeMobileSidebar() {
        if (sidebar) sidebar.classList.remove('open');
        if (sidebarBackdrop) sidebarBackdrop.classList.remove('show');
    }

    if (mobileMenuBtn) mobileMenuBtn.addEventListener('click', openMobileSidebar);
    if (sidebarBackdrop) sidebarBackdrop.addEventListener('click', closeMobileSidebar);

    /**
     * Fetch all CSV data from api.php
     */
    function fetchCsvData() {
        if (btnRefresh) {
            btnRefresh.innerHTML = '<span>⏳</span> Loading...';
            btnRefresh.disabled = true;
        }

        fetch('api.php?action=get_data')
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (btnRefresh) {
                    btnRefresh.innerHTML = '<span>🔄</span> Reload Data';
                    btnRefresh.disabled = false;
                }

                if (data.status === 'success') {
                    allRows = data.rows || [];

                    // Update stats
                    var totalCount = data.total_rows || allRows.length;
                    if (totalProductsCount) totalProductsCount.textContent = totalCount;
                    if (navBadgeTotal) navBadgeTotal.textContent = totalCount;
                    if (totalModelsCount) totalModelsCount.textContent = (data.models || []).length;
                    
                    if (data.price_range) {
                        var minFmt = '₹' + Number(data.price_range.min).toLocaleString('en-IN');
                        var maxFmt = '₹' + Number(data.price_range.max).toLocaleString('en-IN');
                        if (priceRangeCount) priceRangeCount.textContent = minFmt + ' - ' + maxFmt;
                    }

                    if (lastUpdatedText && data.last_modified) {
                        lastUpdatedText.textContent = data.last_modified;
                    }
                    if (syncTimeBadge) {
                        syncTimeBadge.textContent = 'Active: ' + (data.last_modified || 'Live');
                    }

                    // Populate Model Filter dropdown
                    if (modelFilterSelect && modelFilterSelect.options.length <= 1) {
                        (data.models || []).forEach(function (m) {
                            var opt = document.createElement('option');
                            opt.value = m;
                            opt.textContent = m;
                            modelFilterSelect.appendChild(opt);
                        });
                    }

                    // Populate Storage Filter dropdown
                    if (storageFilterSelect && storageFilterSelect.options.length <= 1) {
                        (data.storages || []).forEach(function (s) {
                            var opt = document.createElement('option');
                            opt.value = s;
                            opt.textContent = s;
                            storageFilterSelect.appendChild(opt);
                        });
                    }

                    applyFilters();
                } else {
                    showToast(data.message || 'Failed to load CSV data', 'error');
                }
            })
            .catch(function (err) {
                if (btnRefresh) {
                    btnRefresh.innerHTML = '<span>🔄</span> Reload Data';
                    btnRefresh.disabled = false;
                }
                showToast('Error connecting to admin API: ' + err.message, 'error');
            });
    }

    /**
     * Filter and render rows
     */
    function applyFilters() {
        filteredRows = allRows.filter(function (r) {
            var matchSearch = true;
            if (searchQuery) {
                var q = searchQuery.toLowerCase();
                var nameMatch = (r.product_name || '').toLowerCase().indexOf(q) !== -1;
                var idMatch = (r.product_id || '').toLowerCase().indexOf(q) !== -1;
                var storageMatch = (r.product_storage || '').toLowerCase().indexOf(q) !== -1;
                matchSearch = nameMatch || idMatch || storageMatch;
            }

            var matchModel = (currentModelFilter === 'all') || (r.product_name === currentModelFilter);
            var matchStorage = (currentStorageFilter === 'all') || (r.product_storage === currentStorageFilter);

            return matchSearch && matchModel && matchStorage;
        });

        if (tableFilterCount) {
            tableFilterCount.textContent = 'Showing ' + filteredRows.length + ' of ' + allRows.length + ' Devices';
        }

        renderTable();
    }

    /**
     * Render data table rows
     */
    function renderTable() {
        if (!tableBody) return;

        if (filteredRows.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="7" style="text-align:center; padding: 50px 20px; color: #94A3B8;">'
                                + '<div style="font-size: 32px; margin-bottom: 8px;">🔍</div>'
                                + '<strong style="font-size: 15px; color: #334155;">No iPhone models found</strong>'
                                + '<p style="font-size: 13px; color: #64748B; margin-top: 4px;">Try clearing your search query or selecting "All Models".</p>'
                                + '</td></tr>';
            return;
        }

        var html = '';
        filteredRows.forEach(function (r) {
            var pId = r.product_id || '';
            var pName = r.product_name || 'iPhone';
            var pStorage = r.product_storage || '';
            var pRam = r.ram || '-';
            var pPrice = r.product_base_price || '0';
            var cleanPrice = Number(pPrice.toString().replace(/,/g, '')).toLocaleString('en-IN');

            html += '<tr id="row-' + pId + '">'
                  + '<td><span class="badge-tag">#' + pId + '</span></td>'
                  + '<td>'
                  + '  <div style="font-weight: 800; color: #0F172A; font-size: 14px;">' + pName + '</div>'
                  + '  <div style="font-size: 11px; color: #94A3B8; margin-top: 2px;">Variant: ' + (r.product_variant || pName) + '</div>'
                  + '</td>'
                  + '<td><span class="badge-storage">' + pStorage + '</span></td>'
                  + '<td><span style="color: #64748B; font-weight: 700; font-size: 12.5px;">' + pRam + '</span></td>'
                  + '<td>'
                  + '  <div class="price-control-cell">'
                  + '    <span style="font-weight: 800; color: #065F46; font-size: 14px;">₹</span>'
                  + '    <input type="text" class="input-price-inline" id="price-input-' + pId + '" value="' + cleanPrice + '" data-original="' + cleanPrice + '" inputmode="numeric" />'
                  + '    <button type="button" class="btn-save-price" data-id="' + pId + '" title="Instant Save Base Price to CSV">💾</button>'
                  + '  </div>'
                  + '</td>'
                  + '<td>'
                  + '  <div style="display: flex; flex-wrap: wrap; gap: 4px; font-size: 11.5px;">'
                  + '    <span class="badge-tag" title="Screen Scratches Deduction">Scr: <strong>' + (r.multiple_scratches_screen || '0') + '%</strong></span>'
                  + '    <span class="badge-tag" title="Age Over 11 Months Deduction">Age: <strong>' + (r.months_11_more || '0') + '%</strong></span>'
                  + '    <span class="badge-tag" title="Battery Under 80% Deduction">Bat: <strong>' + (r.battery_less_80 || '0') + '%</strong></span>'
                  + '  </div>'
                  + '</td>'
                  + '<td style="text-align: center;">'
                  + '  <button type="button" class="btn-open-drawer" data-id="' + pId + '">'
                  + '    ✏️ Edit All Deductions'
                  + '  </button>'
                  + '</td>'
                  + '</tr>';
        });

        tableBody.innerHTML = html;

        // Quick inline price save handlers
        var inlineBtns = tableBody.querySelectorAll('.btn-save-price');
        inlineBtns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var id = btn.getAttribute('data-id');
                var input = document.getElementById('price-input-' + id);
                if (!input) return;

                var newPrice = input.value.replace(/[^0-9]/g, '');
                if (!newPrice || Number(newPrice) <= 0) {
                    showToast('Please enter a valid base price in rupees.', 'error');
                    return;
                }

                btn.disabled = true;
                btn.textContent = '⏳';

                var formData = new FormData();
                formData.append('action', 'quick_update_price');
                formData.append('product_id', id);
                formData.append('price', newPrice);

                fetch('api.php?action=quick_update_price', {
                    method: 'POST',
                    body: formData
                })
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    btn.disabled = false;
                    btn.textContent = '💾';
                    if (data.status === 'success') {
                        showToast(data.message, 'success');
                        input.value = Number(newPrice).toLocaleString('en-IN');
                        input.setAttribute('data-original', input.value);
                        var matched = allRows.find(function (row) { return String(row.product_id) === String(id); });
                        if (matched) matched.product_base_price = newPrice;
                        if (lastUpdatedText && data.last_modified) lastUpdatedText.textContent = data.last_modified;
                        if (syncTimeBadge && data.last_modified) syncTimeBadge.textContent = 'Active: ' + data.last_modified;
                    } else {
                        showToast(data.message || 'Failed to update price.', 'error');
                    }
                })
                .catch(function (err) {
                    btn.disabled = false;
                    btn.textContent = '💾';
                    showToast('Connection error: ' + err.message, 'error');
                });
            });
        });

        // Full modal edit handlers
        var editBtns = tableBody.querySelectorAll('.btn-open-drawer');
        editBtns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var id = btn.getAttribute('data-id');
                openEditModal(id);
            });
        });
    }

    /**
     * Reset Filters
     */
    if (btnResetFilters) {
        btnResetFilters.addEventListener('click', function () {
            searchQuery = '';
            if (searchInput) searchInput.value = '';
            if (searchClearBtn) searchClearBtn.style.display = 'none';

            currentModelFilter = 'all';
            if (modelFilterSelect) modelFilterSelect.value = 'all';

            currentStorageFilter = 'all';
            if (storageFilterSelect) storageFilterSelect.value = 'all';

            applyFilters();
        });
    }

    /**
     * Open Full Deduction Edit Modal
     */
    function openEditModal(productId) {
        var row = allRows.find(function (r) { return String(r.product_id) === String(productId); });
        if (!row) {
            showToast('Row not found for ID ' + productId, 'error');
            return;
        }

        currentEditingRow = row;

        if (modalProductName) modalProductName.textContent = (row.product_name || 'iPhone') + ' (' + (row.product_storage || '') + ')';
        if (modalProductIdTag) modalProductIdTag.textContent = '#' + (row.product_id || '');

        var form = document.getElementById('editProductForm');
        if (form) {
            var inputs = form.querySelectorAll('input[name]');
            inputs.forEach(function (input) {
                var fieldName = input.getAttribute('name');
                if (row[fieldName] !== undefined) {
                    var val = row[fieldName];
                    if (fieldName === 'product_base_price') {
                        input.value = val.toString().replace(/,/g, '');
                    } else {
                        input.value = val;
                    }
                } else {
                    input.value = '';
                }
            });
        }

        switchModalTab('tab-general');

        if (editModal) {
            editModal.style.display = 'flex';
        }
    }

    function closeEditModal() {
        if (editModal) editModal.style.display = 'none';
        currentEditingRow = null;
    }

    function switchModalTab(targetTabId) {
        sheetTabBtns.forEach(function (btn) {
            btn.classList.toggle('active', btn.getAttribute('data-tab') === targetTabId);
        });
        sheetTabPanes.forEach(function (pane) {
            pane.classList.toggle('active', pane.id === targetTabId);
        });
    }

    function saveModalChanges() {
        if (!currentEditingRow) return;

        var form = document.getElementById('editProductForm');
        if (!form) return;

        var payload = {
            action: 'update_row',
            product_id: currentEditingRow.product_id
        };

        var inputs = form.querySelectorAll('input[name]');
        inputs.forEach(function (input) {
            var name = input.getAttribute('name');
            var val = input.value.trim();
            payload[name] = val;
        });

        if (btnSaveSheet) {
            btnSaveSheet.disabled = true;
            btnSaveSheet.innerHTML = '<span>⏳</span> Saving to CSV...';
        }

        fetch('api.php?action=update_row', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            if (btnSaveSheet) {
                btnSaveSheet.disabled = false;
                btnSaveSheet.innerHTML = '<span>💾</span> Save Changes Real-Time';
            }

            if (data.status === 'success') {
                showToast(data.message, 'success');
                var idx = allRows.findIndex(function (r) { return String(r.product_id) === String(currentEditingRow.product_id); });
                if (idx !== -1 && data.updated_row) {
                    allRows[idx] = data.updated_row;
                }
                if (lastUpdatedText && data.last_modified) {
                    lastUpdatedText.textContent = data.last_modified;
                }
                if (syncTimeBadge && data.last_modified) {
                    syncTimeBadge.textContent = 'Active: ' + data.last_modified;
                }
                closeEditModal();
                applyFilters();
            } else {
                showToast(data.message || 'Error updating CSV.', 'error');
            }
        })
        .catch(function (err) {
            if (btnSaveSheet) {
                btnSaveSheet.disabled = false;
                btnSaveSheet.innerHTML = '<span>💾</span> Save Changes Real-Time';
            }
            showToast('Connection error: ' + err.message, 'error');
        });
    }

    /**
     * Search & Filter Event Handlers
     */
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            searchQuery = searchInput.value.trim();
            if (searchClearBtn) {
                searchClearBtn.style.display = searchQuery ? 'block' : 'none';
            }
            applyFilters();
        });
    }

    if (searchClearBtn) {
        searchClearBtn.addEventListener('click', function () {
            searchInput.value = '';
            searchQuery = '';
            searchClearBtn.style.display = 'none';
            searchInput.focus();
            applyFilters();
        });
    }

    if (modelFilterSelect) {
        modelFilterSelect.addEventListener('change', function () {
            currentModelFilter = modelFilterSelect.value;
            applyFilters();
        });
    }

    if (storageFilterSelect) {
        storageFilterSelect.addEventListener('change', function () {
            currentStorageFilter = storageFilterSelect.value;
            applyFilters();
        });
    }

    if (btnRefresh) {
        btnRefresh.addEventListener('click', function () {
            fetchCsvData();
        });
    }

    if (btnCloseSheet) btnCloseSheet.addEventListener('click', closeEditModal);
    if (btnCancelSheet) btnCancelSheet.addEventListener('click', closeEditModal);
    if (btnSaveSheet) btnSaveSheet.addEventListener('click', saveModalChanges);

    // ==========================================================================
    // BULK CSV IMPORT WITH SMART COLUMN / FIELD MAPPING
    // ==========================================================================
    var importModal = document.getElementById('importCsvModal');
    var btnTopImport = document.getElementById('btnTopImport');
    var btnPanelImport = document.getElementById('btnPanelImport');
    var navOpenImportModal = document.getElementById('navOpenImportModal');
    var btnCloseImportModal = document.getElementById('btnCloseImportModal');
    var btnCancelImportModal = document.getElementById('btnCancelImportModal');

    // Wizard Navigation Elements
    var wizardStep1Indicator = document.getElementById('wizardStep1Indicator');
    var wizardStep2Indicator = document.getElementById('wizardStep2Indicator');
    var wizardStep3Indicator = document.getElementById('wizardStep3Indicator');
    var wizardStepLine1 = document.getElementById('wizardStepLine1');
    var wizardStepLine2 = document.getElementById('wizardStepLine2');
    var importStepUpload = document.getElementById('importStepUpload');
    var importStepMapping = document.getElementById('importStepMapping');
    var importStepPreview = document.getElementById('importStepPreview');
    var btnNextImportStep = document.getElementById('btnNextImportStep');
    var btnPrevImportStep = document.getElementById('btnPrevImportStep');
    var btnConfirmImport = document.getElementById('btnConfirmImport');

    // File Upload Elements
    var uploadDropzone = document.getElementById('uploadDropzone');
    var csvFileInput = document.getElementById('csvFileInput');
    var btnBrowseFile = document.getElementById('btnBrowseFile');
    var uploadFileInfo = document.getElementById('uploadFileInfo');
    var fileNameDisplay = document.getElementById('fileNameDisplay');
    var fileDetailsDisplay = document.getElementById('fileDetailsDisplay');
    var btnChangeFile = document.getElementById('btnChangeFile');

    // Mapping Elements
    var mapSourceColCount = document.getElementById('mapSourceColCount');
    var mapMatchedCount = document.getElementById('mapMatchedCount');
    var mapRowCount = document.getElementById('mapRowCount');
    var mappingCatsNav = document.getElementById('mappingCatsNav');
    var mappingFieldsContainer = document.getElementById('mappingFieldsContainer');

    // Preview Elements
    var previewTotalRows = document.getElementById('previewTotalRows');
    var previewMappedCols = document.getElementById('previewMappedCols');
    var previewSelectedMode = document.getElementById('previewSelectedMode');
    var previewTableHead = document.getElementById('previewTableHead');
    var previewTableBody = document.getElementById('previewTableBody');

    // State Variables for Import
    var currentImportStep = 1;
    var parsedCsvData = null; // { headers: [], rows: [] }
    var columnMappings = {}; // { targetKey: sourceColumnName | '__skip__' }

    // Target Schema Specification with Categories and Auto-Match Synonyms
    var TARGET_SCHEMA = [
        // Category 1: Core Details & Base Price
        { key: 'product_name', label: 'Model Name', category: 'cat-core', required: true, synonyms: ['product_name', 'model', 'name', 'phone', 'device', 'iphone', 'model_name', 'title'] },
        { key: 'product_storage', label: 'Storage Capacity', category: 'cat-core', required: true, synonyms: ['product_storage', 'storage', 'rom', 'capacity', 'size', 'gb', 'internal_storage'] },
        { key: 'product_base_price', label: 'Base Price (₹)', category: 'cat-core', required: true, synonyms: ['product_base_price', 'base_price', 'price', 'cost', 'amount', 'resale_price', 'value', 'mrp', 'inr'] },
        { key: 'ram', label: 'RAM', category: 'cat-core', synonyms: ['ram', 'memory', 'ram_size'] },
        { key: 'product_id', label: 'Product ID (Optional)', category: 'cat-core', synonyms: ['product_id', 'id', 'pid', 'item_id'] },
        { key: 'product_variant', label: 'Variant Key', category: 'cat-core', synonyms: ['product_variant', 'variant', 'specs'] },
        { key: 'product_description', label: 'Description', category: 'cat-core', synonyms: ['product_description', 'description', 'desc', 'details'] },
        { key: 'product_image', label: 'Image Filename', category: 'cat-core', synonyms: ['product_image', 'image', 'img', 'photo', 'pic'] },

        // Category 2: Age Deductions
        { key: 'months_0_3', label: '0 - 3 Months Old (%)', category: 'cat-age', synonyms: ['months_0_3', '0_3', '0-3', 'under_3_months', '0_to_3_months'] },
        { key: 'months_3_6', label: '3 - 6 Months Old (%)', category: 'cat-age', synonyms: ['months_3_6', '3_6', '3-6', '3_to_6_months'] },
        { key: 'months_6_11', label: '6 - 11 Months Old (%)', category: 'cat-age', synonyms: ['months_6_11', '6_11', '6-11', '6_to_11_months'] },
        { key: 'months_11_more', label: '11+ Months Old (%)', category: 'cat-age', synonyms: ['months_11_more', '11_more', '11+', '11_plus', 'out_of_warranty', 'above_11_months'] },

        // Category 3: Screen & Display
        { key: 'scratch_screen_1_2', label: '1 - 2 Screen Scratches (%)', category: 'cat-screen', synonyms: ['scratch_screen_1_2', 'screen_scratch_minor', 'screen_1_2'] },
        { key: 'scratch_screen_3_4', label: '3 - 4 Screen Scratches (%)', category: 'cat-screen', synonyms: ['scratch_screen_3_4', 'screen_3_4'] },
        { key: 'multiple_scratches_screen', label: 'Multiple Screen Scratches (%)', category: 'cat-screen', synonyms: ['multiple_scratches_screen', 'screen_scratches_heavy'] },
        { key: 'glass_cracked', label: 'Front Glass Cracked (%)', category: 'cat-screen', synonyms: ['glass_cracked', 'front_glass_cracked', 'broken_screen_glass'] },
        { key: 'dots_on_display', label: 'Dots on Display (%)', category: 'cat-screen', synonyms: ['dots_on_display', 'black_spots', 'dead_pixels'] },
        { key: 'lines_on_display', label: 'Lines on Display (%)', category: 'cat-screen', synonyms: ['lines_on_display', 'display_lines', 'green_line'] },
        { key: 'touch_not_working', label: 'Touch Not Working (%)', category: 'cat-screen', synonyms: ['touch_not_working', 'touch_issue', 'touch_fault'] },
        { key: 'no_display', label: 'No Display / Blackout (%)', category: 'cat-screen', synonyms: ['no_display', 'blackout', 'display_dead'] },
        { key: 'loose_screen', label: 'Loose Screen / Lifted (%)', category: 'cat-screen', synonyms: ['loose_screen', 'screen_lifted', 'loose_display'] },
        { key: 'color_fade', label: 'Color Fade / Burn-in (%)', category: 'cat-screen', synonyms: ['color_fade', 'burn_in', 'color_issue'] },
        { key: 'flickering', label: 'Screen Flickering (%)', category: 'cat-screen', synonyms: ['flickering', 'screen_flickering'] },

        // Category 4: Body & Frame
        { key: 'scratch_body_1_2', label: '1 - 2 Body Scratches (%)', category: 'cat-body', synonyms: ['scratch_body_1_2', 'body_1_2'] },
        { key: 'scratch_body_3_4', label: '3 - 4 Body Scratches (%)', category: 'cat-body', synonyms: ['scratch_body_3_4', 'body_3_4'] },
        { key: 'multiple_scratches_body', label: 'Multiple Body Scratches (%)', category: 'cat-body', synonyms: ['multiple_scratches_body', 'body_heavy_scratches'] },
        { key: 'dents_1_or_2', label: '1 or 2 Dents (%)', category: 'cat-body', synonyms: ['dents_1_or_2', 'dents_1_2'] },
        { key: 'multiple_dents', label: 'Multiple Dents (%)', category: 'cat-body', synonyms: ['multiple_dents', 'heavy_dents'] },
        { key: 'body_curved', label: 'Body Curved / Bent (%)', category: 'cat-body', synonyms: ['body_curved', 'bent_frame', 'curved_body'] },
        { key: 'back_glass_broken', label: 'Back Glass Broken (%)', category: 'cat-body', synonyms: ['back_glass_broken', 'broken_back_glass', 'back_glass_crack'] },
        { key: 'camera_glass_broken', label: 'Camera Glass Broken (%)', category: 'cat-body', synonyms: ['camera_glass_broken', 'broken_camera_glass'] },

        // Category 5: Hardware Tests
        { key: 'front_camera_not_working', label: 'Front Camera Fault (%)', category: 'cat-hardware', synonyms: ['front_camera_not_working', 'front_camera_issue', 'selfie_camera'] },
        { key: 'back_camera_not_working', label: 'Back Camera Fault (%)', category: 'cat-hardware', synonyms: ['back_camera_not_working', 'rear_camera', 'main_camera'] },
        { key: 'face_id_not_working', label: 'Face ID Fault (%)', category: 'cat-hardware', synonyms: ['face_id_not_working', 'face_id_issue', 'faceid'] },
        { key: 'finger_print_not_working', label: 'Fingerprint Fault (%)', category: 'cat-hardware', synonyms: ['finger_print_not_working', 'touch_id', 'fingerprint'] },
        { key: 'battery_faulty', label: 'Battery Faulty (%)', category: 'cat-hardware', synonyms: ['battery_faulty', 'battery_health_issue', 'battery_bad'] },
        { key: 'charging_port_issue', label: 'Charging Port Issue (%)', category: 'cat-hardware', synonyms: ['charging_port_issue', 'charging_fault', 'port_issue'] },
        { key: 'speaker_not_working', label: 'Speaker Issue (%)', category: 'cat-hardware', synonyms: ['speaker_not_working', 'loudspeaker_fault', 'earpiece_speaker'] },
        { key: 'audio_ic_problem', label: 'Audio IC Issue (%)', category: 'cat-hardware', synonyms: ['audio_ic_problem', 'audio_ic', 'mic_issue'] },
        { key: 'power_button_issue', label: 'Power Button Issue (%)', category: 'cat-hardware', synonyms: ['power_button_issue', 'power_button', 'lock_button'] },
        { key: 'volume', label: 'Volume Buttons Issue (%)', category: 'cat-hardware', synonyms: ['volume', 'volume_buttons', 'volume_rocker'] },
        { key: 'wifi_issues', label: 'Wi-Fi Issue (%)', category: 'cat-hardware', synonyms: ['wifi_issues', 'wifi_problem', 'wifi_fault'] },
        { key: 'bluetooth_issue', label: 'Bluetooth Issue (%)', category: 'cat-hardware', synonyms: ['bluetooth_issue', 'bluetooth_problem'] },
        { key: 'vibrator', label: 'Vibrator Issue (%)', category: 'cat-hardware', synonyms: ['vibrator', 'taptic_engine', 'vibration'] },
        { key: 'sensor_issues', label: 'Sensors Issue (%)', category: 'cat-hardware', synonyms: ['sensor_issues', 'proximity_sensor', 'sensor_fault'] },
        { key: 'headphone_jackissue', label: 'Headphone Jack Issue (%)', category: 'cat-hardware', synonyms: ['headphone_jackissue', 'headphone_jack'] },

        // Category 6: Battery & Kit
        { key: 'battery_less_80', label: 'Battery < 80% (Service) (%)', category: 'cat-battery', synonyms: ['battery_less_80', 'battery_under_80', 'battery_sub_80'] },
        { key: 'battery_greater_80', label: 'Battery >= 80% (Healthy) (%)', category: 'cat-battery', synonyms: ['battery_greater_80', 'battery_above_80', 'battery_healthy'] },
        { key: 'box', label: 'Box Missing (%)', category: 'cat-battery', synonyms: ['box', 'missing_box', 'no_box'] },
        { key: 'charger', label: 'Charger Missing (%)', category: 'cat-battery', synonyms: ['charger', 'missing_charger', 'no_charger'] },
        { key: 'invoice', label: 'Invoice Missing (%)', category: 'cat-battery', synonyms: ['invoice', 'missing_invoice', 'no_bill', 'no_invoice'] }
    ];

    /**
     * RFC 4180 Compliant Client-side CSV Parser
     */
    function parseCsvString(text) {
        if (!text) return { headers: [], rows: [] };
        if (text.charCodeAt(0) === 0xFEFF) {
            text = text.slice(1);
        }
        var rows = [];
        var currentRow = [];
        var currentField = '';
        var insideQuotes = false;
        var i = 0;
        var len = text.length;

        while (i < len) {
            var char = text[i];
            var nextChar = text[i + 1];

            if (insideQuotes) {
                if (char === '"' && nextChar === '"') {
                    currentField += '"';
                    i += 2;
                    continue;
                } else if (char === '"') {
                    insideQuotes = false;
                    i++;
                    continue;
                } else {
                    currentField += char;
                    i++;
                    continue;
                }
            } else {
                if (char === '"') {
                    insideQuotes = true;
                    i++;
                    continue;
                } else if (char === ',') {
                    currentRow.push(currentField.trim());
                    currentField = '';
                    i++;
                    continue;
                } else if (char === '\r' || char === '\n') {
                    if (char === '\r' && nextChar === '\n') {
                        i += 2;
                    } else {
                        i++;
                    }
                    currentRow.push(currentField.trim());
                    currentField = '';
                    if (currentRow.length > 1 || (currentRow.length === 1 && currentRow[0] !== '')) {
                        rows.push(currentRow);
                    }
                    currentRow = [];
                    continue;
                } else {
                    currentField += char;
                    i++;
                    continue;
                }
            }
        }
        if (currentField !== '' || currentRow.length > 0) {
            currentRow.push(currentField.trim());
            if (currentRow.length > 1 || (currentRow.length === 1 && currentRow[0] !== '')) {
                rows.push(currentRow);
            }
        }

        if (rows.length === 0) {
            return { headers: [], rows: [] };
        }

        var headers = rows[0].map(function (h) { return h.replace(/^["']|["']$/g, '').trim(); });
        var dataRows = [];
        for (var r = 1; r < rows.length; r++) {
            var rArr = rows[r];
            var rowObj = {};
            for (var c = 0; c < headers.length; c++) {
                rowObj[headers[c]] = rArr[c] !== undefined ? rArr[c].replace(/^["']|["']$/g, '').trim() : '';
            }
            dataRows.push(rowObj);
        }

        return { headers: headers, rows: dataRows };
    }

    /**
     * Normalize key string for heuristic comparisons
     */
    function normalizeKey(str) {
        return (str || '').toLowerCase().replace(/[^a-z0-9]/g, '');
    }

    /**
     * Open Bulk Import Modal
     */
    function openImportModal() {
        if (importModal) {
            importModal.style.display = 'flex';
            setImportStep(1);
        }
    }

    /**
     * Close Bulk Import Modal and reset state
     */
    function closeImportModal() {
        if (importModal) {
            importModal.style.display = 'none';
        }
        resetImportState();
    }

    /**
     * Reset Import Wizard state
     */
    function resetImportState() {
        currentImportStep = 1;
        parsedCsvData = null;
        columnMappings = {};
        if (csvFileInput) csvFileInput.value = '';
        if (uploadDropzone) uploadDropzone.style.display = 'block';
        if (uploadFileInfo) uploadFileInfo.style.display = 'none';
        if (btnNextImportStep) {
            btnNextImportStep.disabled = true;
            btnNextImportStep.textContent = 'Proceed to Field Mapping →';
        }
        updateWizardStepUI(1);
    }

    /**
     * Switch Wizard Steps (1, 2, 3)
     */
    function setImportStep(step) {
        currentImportStep = step;
        updateWizardStepUI(step);

        if (importStepUpload) importStepUpload.classList.toggle('active', step === 1);
        if (importStepMapping) importStepMapping.classList.toggle('active', step === 2);
        if (importStepPreview) importStepPreview.classList.toggle('active', step === 3);

        if (btnPrevImportStep) {
            btnPrevImportStep.style.display = step > 1 ? 'inline-block' : 'none';
        }

        if (step === 1) {
            if (btnNextImportStep) {
                btnNextImportStep.style.display = 'inline-block';
                btnNextImportStep.disabled = !parsedCsvData || !parsedCsvData.rows.length;
                btnNextImportStep.textContent = 'Proceed to Field Mapping →';
            }
            if (btnConfirmImport) btnConfirmImport.style.display = 'none';
        } else if (step === 2) {
            if (btnNextImportStep) {
                btnNextImportStep.style.display = 'inline-block';
                btnNextImportStep.disabled = false;
                btnNextImportStep.textContent = 'Review & Preview →';
            }
            if (btnConfirmImport) btnConfirmImport.style.display = 'none';
            renderMappingFields();
        } else if (step === 3) {
            if (btnNextImportStep) btnNextImportStep.style.display = 'none';
            if (btnConfirmImport) btnConfirmImport.style.display = 'inline-flex';
            renderImportPreview();
        }

        var scrollBody = document.getElementById('importScrollBody');
        if (scrollBody) scrollBody.scrollTop = 0;
    }

    /**
     * Update Wizard Stepper Header UI
     */
    function updateWizardStepUI(step) {
        if (wizardStep1Indicator) {
            wizardStep1Indicator.classList.toggle('active', step >= 1);
            wizardStep1Indicator.classList.toggle('completed', step > 1);
        }
        if (wizardStepLine1) {
            wizardStepLine1.classList.toggle('active', step >= 2);
        }
        if (wizardStep2Indicator) {
            wizardStep2Indicator.classList.toggle('active', step >= 2);
            wizardStep2Indicator.classList.toggle('completed', step > 2);
        }
        if (wizardStepLine2) {
            wizardStepLine2.classList.toggle('active', step >= 3);
        }
        if (wizardStep3Indicator) {
            wizardStep3Indicator.classList.toggle('active', step >= 3);
        }
    }

    /**
     * Handle CSV or Excel File Selected
     */
    function handleFileSelected(file) {
        if (!file) return;

        var fileName = file.name || '';
        var ext = fileName.split('.').pop().toLowerCase();

        if (ext !== 'csv' && ext !== 'txt' && ext !== 'xlsx' && ext !== 'xls') {
            showToast('Please select a valid .csv or .xlsx / .xls file.', 'error');
            return;
        }

        // 1. Process Excel (.xlsx, .xls)
        if (ext === 'xlsx' || ext === 'xls') {
            if (typeof XLSX !== 'undefined') {
                // Client-side SheetJS parsing
                var reader = new FileReader();
                reader.onload = function (e) {
                    try {
                        var data = new Uint8Array(e.target.result);
                        var workbook = XLSX.read(data, { type: 'array' });
                        if (!workbook.SheetNames || !workbook.SheetNames.length) {
                            showToast('No sheets found in Excel file.', 'error');
                            return;
                        }
                        var firstSheetName = workbook.SheetNames[0];
                        var worksheet = workbook.Sheets[firstSheetName];
                        var rawRows = XLSX.utils.sheet_to_json(worksheet, { header: 1, defval: '' });

                        if (!rawRows || rawRows.length < 2) {
                            showToast('Excel sheet "' + firstSheetName + '" appears to be empty or has no data rows.', 'error');
                            return;
                        }

                        // Extract headers from first non-empty row
                        var headers = rawRows[0].map(function (h) { return String(h || '').trim(); }).filter(function (h) { return h !== ''; });
                        if (!headers.length) {
                            showToast('No column headers detected in Excel sheet.', 'error');
                            return;
                        }

                        var dataRows = [];
                        for (var r = 1; r < rawRows.length; r++) {
                            var rowArr = rawRows[r];
                            var rowObj = {};
                            var hasVal = false;
                            for (var c = 0; c < headers.length; c++) {
                                var cellVal = rowArr[c] !== undefined ? String(rowArr[c]).trim() : '';
                                if (cellVal !== '') hasVal = true;
                                rowObj[headers[c]] = cellVal;
                            }
                            if (hasVal) {
                                dataRows.push(rowObj);
                            }
                        }

                        if (!dataRows.length) {
                            showToast('No data rows found in Excel sheet.', 'error');
                            return;
                        }

                        parsedCsvData = { headers: headers, rows: dataRows };
                        onFileParsedSuccess(file, 'Excel .' + ext.toUpperCase());
                    } catch (err) {
                        showToast('Error reading Excel file: ' + err.message, 'error');
                    }
                };
                reader.readAsArrayBuffer(file);
            } else {
                // Fallback to server-side parser
                var formData = new FormData();
                formData.append('excel_file', file);
                showToast('Parsing Excel workbook on server...', 'success');

                fetch('api.php?action=parse_excel', {
                    method: 'POST',
                    body: formData
                })
                .then(function (res) { return res.json(); })
                .then(function (res) {
                    if (res.status === 'success') {
                        parsedCsvData = { headers: res.headers, rows: res.rows };
                        onFileParsedSuccess(file, 'Excel .' + ext.toUpperCase());
                    } else {
                        showToast(res.message || 'Failed to parse Excel file on server.', 'error');
                    }
                })
                .catch(function (err) {
                    showToast('Connection error parsing Excel: ' + err.message, 'error');
                });
            }
            return;
        }

        // 2. Process CSV (.csv, .txt)
        var reader = new FileReader();
        reader.onload = function (e) {
            var text = e.target.result;
            try {
                parsedCsvData = parseCsvString(text);
                if (!parsedCsvData || !parsedCsvData.rows.length) {
                    showToast('The selected CSV file appears to be empty or has no data rows.', 'error');
                    return;
                }
                onFileParsedSuccess(file, 'CSV');
            } catch (err) {
                showToast('Error reading CSV: ' + err.message, 'error');
            }
        };
        reader.readAsText(file);
    }

    /**
     * Common Success Handler after parsing CSV or Excel
     */
    function onFileParsedSuccess(file, formatLabel) {
        if (fileNameDisplay) fileNameDisplay.textContent = file.name;
        var sizeKb = Math.round(file.size / 1024);
        if (fileDetailsDisplay) {
            fileDetailsDisplay.textContent = sizeKb + ' KB • ' + parsedCsvData.rows.length + ' rows detected • ' + parsedCsvData.headers.length + ' columns (' + formatLabel + ')';
        }

        if (uploadDropzone) uploadDropzone.style.display = 'none';
        if (uploadFileInfo) uploadFileInfo.style.display = 'block';

        if (btnNextImportStep) {
            btnNextImportStep.disabled = false;
        }

        autoMatchColumns();
        showToast(formatLabel + ' parsed: ' + parsedCsvData.rows.length + ' rows and ' + parsedCsvData.headers.length + ' columns detected.', 'success');
    }

    /**
     * Intelligent Heuristic Auto-Matching Engine
     */
    function autoMatchColumns() {
        if (!parsedCsvData || !parsedCsvData.headers) return;
        var headers = parsedCsvData.headers;
        columnMappings = {};

        var normalizedHeaders = headers.map(function (h) {
            return { raw: h, norm: normalizeKey(h) };
        });

        var usedSourceCols = {};

        TARGET_SCHEMA.forEach(function (field) {
            var targetNorm = normalizeKey(field.key);
            var bestMatch = null;

            // 1. Exact match with field key
            for (var i = 0; i < normalizedHeaders.length; i++) {
                if (normalizedHeaders[i].norm === targetNorm && !usedSourceCols[normalizedHeaders[i].raw]) {
                    bestMatch = normalizedHeaders[i].raw;
                    break;
                }
            }

            // 2. Match with synonyms dictionary
            if (!bestMatch && field.synonyms) {
                for (var s = 0; s < field.synonyms.length; s++) {
                    var synNorm = normalizeKey(field.synonyms[s]);
                    for (var j = 0; j < normalizedHeaders.length; j++) {
                        if (normalizedHeaders[j].norm === synNorm && !usedSourceCols[normalizedHeaders[j].raw]) {
                            bestMatch = normalizedHeaders[j].raw;
                            break;
                        }
                    }
                    if (bestMatch) break;
                }
            }

            // 3. Substring / contains match
            if (!bestMatch && field.synonyms) {
                for (var k = 0; k < normalizedHeaders.length; k++) {
                    if (usedSourceCols[normalizedHeaders[k].raw]) continue;
                    var srcNorm = normalizedHeaders[k].norm;
                    if (srcNorm.indexOf(targetNorm) !== -1 || targetNorm.indexOf(srcNorm) !== -1) {
                        bestMatch = normalizedHeaders[k].raw;
                        break;
                    }
                }
            }

            if (bestMatch) {
                columnMappings[field.key] = bestMatch;
                usedSourceCols[bestMatch] = true;
            } else {
                columnMappings[field.key] = '__skip__';
            }
        });
    }

    /**
     * Render Mapping Fields in Step 2
     */
    function renderMappingFields() {
        if (!parsedCsvData || !mappingFieldsContainer) return;

        var headers = parsedCsvData.headers;
        var firstRow = parsedCsvData.rows[0] || {};

        if (mapSourceColCount) mapSourceColCount.textContent = headers.length;
        if (mapRowCount) mapRowCount.textContent = parsedCsvData.rows.length;

        var matchedCount = 0;
        for (var k in columnMappings) {
            if (columnMappings[k] && columnMappings[k] !== '__skip__') matchedCount++;
        }
        if (mapMatchedCount) mapMatchedCount.textContent = matchedCount + ' / ' + TARGET_SCHEMA.length;

        var categories = ['cat-core', 'cat-age', 'cat-screen', 'cat-body', 'cat-hardware', 'cat-battery'];
        var html = '';

        categories.forEach(function (cat) {
            var fieldsInCat = TARGET_SCHEMA.filter(function (f) { return f.category === cat; });
            html += '<div class="map-cat-section' + (cat === 'cat-core' ? ' active' : '') + '" id="section-' + cat + '">';

            fieldsInCat.forEach(function (field) {
                var currentMapped = columnMappings[field.key] || '__skip__';
                var isMatched = currentMapped !== '__skip__';
                var sampleVal = (isMatched && firstRow[currentMapped] !== undefined) ? firstRow[currentMapped] : '—';

                html += '<div class="mapping-field-card' + (isMatched ? ' is-matched' : ' is-skipped') + '" id="field-card-' + field.key + '">'
                      + '  <div class="map-target-info">'
                      + '    <div class="map-target-title">' + field.label + (field.required ? ' <span style="color:#EF4444;">*</span>' : '') + '</div>'
                      + '    <div class="map-target-key">' + field.key + '</div>'
                      + '  </div>'
                      + '  <div class="map-select-wrap">'
                      + '    <select class="map-col-select" data-target-key="' + field.key + '">'
                      + '      <option value="__skip__">-- Skip (Do Not Import) --</option>';

                headers.forEach(function (srcCol) {
                    var selected = (srcCol === currentMapped) ? ' selected' : '';
                    html += '<option value="' + escapeHtml(srcCol) + '"' + selected + '>' + escapeHtml(srcCol) + '</option>';
                });

                html += '    </select>'
                      + (isMatched ? '    <div class="map-match-badge">✓ Auto-Matched</div>' : '')
                      + '  </div>'
                      + '  <div class="map-sample-pill">'
                      + '    <div class="map-sample-lbl">Row 1 Sample Value:</div>'
                      + '    <div class="map-sample-val" id="sample-' + field.key + '">' + escapeHtml(String(sampleVal)) + '</div>'
                      + '  </div>'
                      + '</div>';
            });

            html += '</div>';
        });

        mappingFieldsContainer.innerHTML = html;

        // Attach change listeners to each select dropdown
        var selects = mappingFieldsContainer.querySelectorAll('.map-col-select');
        selects.forEach(function (sel) {
            sel.addEventListener('change', function () {
                var targetKey = sel.getAttribute('data-target-key');
                var val = sel.value;
                columnMappings[targetKey] = val;

                var card = document.getElementById('field-card-' + targetKey);
                var sampleEl = document.getElementById('sample-' + targetKey);

                if (val !== '__skip__') {
                    if (card) {
                        card.classList.add('is-matched');
                        card.classList.remove('is-skipped');
                    }
                    if (sampleEl) {
                        sampleEl.textContent = firstRow[val] !== undefined ? firstRow[val] : '—';
                    }
                } else {
                    if (card) {
                        card.classList.remove('is-matched');
                        card.classList.add('is-skipped');
                    }
                    if (sampleEl) {
                        sampleEl.textContent = '—';
                    }
                }

                // Update matched count
                var count = 0;
                for (var key in columnMappings) {
                    if (columnMappings[key] && columnMappings[key] !== '__skip__') count++;
                }
                if (mapMatchedCount) mapMatchedCount.textContent = count + ' / ' + TARGET_SCHEMA.length;
            });
        });
    }

    /**
     * Render Live Preview in Step 3
     */
    function renderImportPreview() {
        if (!parsedCsvData || !previewTableHead || !previewTableBody) return;

        var mappedTargetKeys = [];
        for (var k in columnMappings) {
            if (columnMappings[k] && columnMappings[k] !== '__skip__') {
                mappedTargetKeys.push(k);
            }
        }

        var modeInput = document.querySelector('input[name="importMergeMode"]:checked');
        var modeVal = modeInput ? modeInput.value : 'merge_append';
        var modeLabels = {
            'merge_append': 'Smart Update & Append',
            'update_only': 'Update Existing Only',
            'replace': 'Full Overwrite / Replace'
        };

        if (previewTotalRows) previewTotalRows.textContent = parsedCsvData.rows.length;
        if (previewMappedCols) previewMappedCols.textContent = mappedTargetKeys.length;
        if (previewSelectedMode) previewSelectedMode.textContent = modeLabels[modeVal] || 'Smart Update';

        // Render table headers
        var theadHtml = '<tr><th style="width: 45px;">#</th>';
        mappedTargetKeys.forEach(function (tk) {
            var fieldMeta = TARGET_SCHEMA.find(function (f) { return f.key === tk; });
            var label = fieldMeta ? fieldMeta.label : tk;
            theadHtml += '<th>' + escapeHtml(label) + '<br><span style="font-size: 10px; font-weight: normal; color: #94A3B8;">' + tk + '</span></th>';
        });
        theadHtml += '</tr>';
        previewTableHead.innerHTML = theadHtml;

        // Render preview of up to first 5 rows
        var sampleRows = parsedCsvData.rows.slice(0, 5);
        var tbodyHtml = '';
        sampleRows.forEach(function (row, idx) {
            tbodyHtml += '<tr><td><strong>' + (idx + 1) + '</strong></td>';
            mappedTargetKeys.forEach(function (tk) {
                var srcCol = columnMappings[tk];
                var cellVal = row[srcCol] !== undefined ? row[srcCol] : '';
                tbodyHtml += '<td>' + escapeHtml(String(cellVal)) + '</td>';
            });
            tbodyHtml += '</tr>';
        });

        previewTableBody.innerHTML = tbodyHtml;
    }

    /**
     * Submit Mapped Bulk Import Payload to Backend
     */
    function executeBulkImport() {
        if (!parsedCsvData || !parsedCsvData.rows.length) {
            showToast('No data rows available to import.', 'error');
            return;
        }

        var modeInput = document.querySelector('input[name="importMergeMode"]:checked');
        var modeVal = modeInput ? modeInput.value : 'merge_append';

        // Transform CSV rows to mapped target keys
        var mappedPayloadRows = parsedCsvData.rows.map(function (row) {
            var obj = {};
            for (var targetKey in columnMappings) {
                var srcCol = columnMappings[targetKey];
                if (srcCol && srcCol !== '__skip__') {
                    obj[targetKey] = row[srcCol] !== undefined ? row[srcCol] : '';
                }
            }
            return obj;
        });

        if (btnConfirmImport) {
            btnConfirmImport.disabled = true;
            btnConfirmImport.innerHTML = '<span>⏳</span> Processing Bulk Import...';
        }

        fetch('api.php?action=bulk_import_csv', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'bulk_import_csv',
                mode: modeVal,
                rows: mappedPayloadRows
            })
        })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            if (btnConfirmImport) {
                btnConfirmImport.disabled = false;
                btnConfirmImport.innerHTML = '<span>🚀</span> Confirm &amp; Apply Import';
            }

            if (data.status === 'success') {
                showToast(data.message, 'success');
                closeImportModal();
                fetchCsvData(); // Live reload table and KPIs
            } else {
                showToast(data.message || 'Error executing import.', 'error');
            }
        })
        .catch(function (err) {
            if (btnConfirmImport) {
                btnConfirmImport.disabled = false;
                btnConfirmImport.innerHTML = '<span>🚀</span> Confirm &amp; Apply Import';
            }
            showToast('Connection error during import: ' + err.message, 'error');
        });
    }

    /**
     * Helper: Escape HTML special characters
     */
    function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // Modal Trigger Event Handlers
    if (btnTopImport) btnTopImport.addEventListener('click', openImportModal);
    if (btnPanelImport) btnPanelImport.addEventListener('click', openImportModal);
    if (navOpenImportModal) navOpenImportModal.addEventListener('click', function () {
        closeMobileSidebar();
        openImportModal();
    });

    if (btnCloseImportModal) btnCloseImportModal.addEventListener('click', closeImportModal);
    if (btnCancelImportModal) btnCancelImportModal.addEventListener('click', closeImportModal);

    // Dropzone & File Pickers
    if (btnBrowseFile && csvFileInput) {
        btnBrowseFile.addEventListener('click', function (e) {
            e.stopPropagation();
            csvFileInput.click();
        });
    }

    if (uploadDropzone && csvFileInput) {
        uploadDropzone.addEventListener('click', function () {
            csvFileInput.click();
        });

        uploadDropzone.addEventListener('dragover', function (e) {
            e.preventDefault();
            uploadDropzone.classList.add('dragover');
        });

        uploadDropzone.addEventListener('dragleave', function () {
            uploadDropzone.classList.remove('dragover');
        });

        uploadDropzone.addEventListener('drop', function (e) {
            e.preventDefault();
            uploadDropzone.classList.remove('dragover');
            if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length) {
                handleFileSelected(e.dataTransfer.files[0]);
            }
        });
    }

    if (csvFileInput) {
        csvFileInput.addEventListener('change', function () {
            if (csvFileInput.files && csvFileInput.files.length) {
                handleFileSelected(csvFileInput.files[0]);
            }
        });
    }

    if (btnChangeFile && csvFileInput) {
        btnChangeFile.addEventListener('click', function () {
            csvFileInput.click();
        });
    }

    // Wizard Step Navigation Buttons
    if (btnNextImportStep) {
        btnNextImportStep.addEventListener('click', function () {
            if (currentImportStep === 1) {
                setImportStep(2);
            } else if (currentImportStep === 2) {
                setImportStep(3);
            }
        });
    }

    if (btnPrevImportStep) {
        btnPrevImportStep.addEventListener('click', function () {
            if (currentImportStep === 3) {
                setImportStep(2);
            } else if (currentImportStep === 2) {
                setImportStep(1);
            }
        });
    }

    if (btnConfirmImport) {
        btnConfirmImport.addEventListener('click', executeBulkImport);
    }

    // Category Tabs Switching in Mapping Step
    if (mappingCatsNav) {
        var catBtns = mappingCatsNav.querySelectorAll('.map-nav-btn');
        catBtns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var targetCat = btn.getAttribute('data-cat');
                catBtns.forEach(function (b) { b.classList.toggle('active', b === btn); });

                if (mappingFieldsContainer) {
                    var sections = mappingFieldsContainer.querySelectorAll('.map-cat-section');
                    sections.forEach(function (sec) {
                        sec.classList.toggle('active', sec.id === 'section-' + targetCat);
                    });
                }
            });
        });
    }

    // Escape Key Handler for Modals
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            if (importModal && importModal.style.display === 'flex') {
                closeImportModal();
            } else if (editModal && editModal.style.display === 'flex') {
                closeEditModal();
            }
        }
    });

    // Initial Load
    fetchCsvData();

})();

