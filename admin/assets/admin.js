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

    sheetTabBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var target = btn.getAttribute('data-tab');
            switchModalTab(target);
        });
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && editModal && editModal.style.display === 'flex') {
            closeEditModal();
        }
    });

    // Initial Load
    fetchCsvData();

})();
