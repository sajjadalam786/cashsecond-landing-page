<?php
require_once __DIR__ . '/auth.php';
requireAdminLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>CashSecond — iPhone Pricing &amp; Deductions Manager</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/admin.css">
</head>
<body>

    <!-- MOBILE SIDEBAR BACKDROP -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- ============================================================
         APPLE-GRADE CASHSECOND SIDEBAR NAVIGATION
         ============================================================ -->
    <aside class="cs-sidebar" id="csSidebar">
        <!-- BRAND HEADER -->
        <div class="sidebar-header">
            <a href="index.php" class="sidebar-brand-link">
                <div class="sidebar-brand-icon">📱</div>
                <div class="sidebar-brand-info">
                    <div class="sidebar-brand-text">Cash<span>Second</span></div>
                    <div class="sidebar-brand-sub">Pricing Engine &amp; Ops</div>
                </div>
            </a>
            <div class="sidebar-status-pill" title="Live sync with Iphone-base-price-&-deduction-logic.csv">
                <span class="status-pulse-dot"></span>
                <span>Live CSV</span>
            </div>
        </div>

        <!-- NAVIGATION LINKS -->
        <nav class="sidebar-nav">
            <div class="nav-section-title">Navigation</div>
            <a href="index.php" class="nav-link active">
                <span class="nav-icon">📱</span>
                <span class="nav-text">Pricing &amp; Deductions</span>
                <span class="nav-badge blue" id="navBadgeTotal">108</span>
            </a>
            <a href="api.php?action=download_csv" class="nav-link" download>
                <span class="nav-icon">📥</span>
                <span class="nav-text">Export CSV</span>
            </a>
        </nav>

        <!-- USER SNIPPET & LOGOUT -->
        <div class="sidebar-footer">
            <div class="user-snippet">
                <div class="user-avatar-badge">CS</div>
                <div class="user-details">
                    <div class="user-title">CashSecond Admin</div>
                    <div class="user-status">
                        <span class="user-pulse"></span>
                        <span>Super Admin</span>
                    </div>
                </div>
            </div>
            <a href="logout.php" class="sidebar-logout-btn" title="Logout from Admin Dashboard">
                <span>🚪</span>
            </a>
        </div>
    </aside>

    <!-- ============================================================
         MAIN WORKSPACE CONTAINER
         ============================================================ -->
    <div class="cs-main-wrap">
        <!-- TOPBAR -->
        <header class="cs-topbar">
            <div class="topbar-left">
                <button type="button" class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Toggle Navigation Menu">
                    <span class="hamburger-line"></span>
                    <span class="hamburger-line"></span>
                    <span class="hamburger-line"></span>
                </button>
                <div class="topbar-breadcrumb">
                    <span class="crumb-root">Dashboard</span>
                    <span class="crumb-sep">/</span>
                    <span class="crumb-active" id="topbarCurrentView">iPhone Pricing &amp; Deductions</span>
                </div>
            </div>

            <div class="topbar-right">
                <div class="live-sync-indicator" title="Connected to Iphone-base-price-&-deduction-logic.csv">
                    <span class="sync-dot"></span>
                    <span class="sync-label">Real-Time CSV</span>
                    <span class="sync-time" id="syncTimeBadge">Sync Active</span>
                </div>

                <a href="api.php?action=download_csv" class="btn-topbar-action btn-export" download title="Download live CSV file">
                    <span>📥</span>
                    <span class="btn-txt">Export CSV</span>
                </a>

                <a href="../index.php" target="_blank" class="btn-topbar-action btn-live-site" title="Open public buyback site">
                    <span class="btn-site-txt">Live Site</span>
                    <span class="btn-site-arrow">↗</span>
                </a>
            </div>
        </header>

        <!-- MAIN CONTENT AREA -->
        <main class="cs-content-area">

            <!-- ========================================================
                 VIEW 1: PRICING & DEDUCTION MATRIX (PRIMARY ENGINE)
                 ======================================================== -->
            <section class="dashboard-view active" id="view-pricing">
                <!-- QUICK KPI RIBBON -->
                <div class="kpi-mini-bar">
                    <div class="mini-kpi-item">
                        <div class="mini-kpi-icon icon-blue">📦</div>
                        <div>
                            <div class="mini-kpi-val" id="totalProductsCount">108</div>
                            <div class="mini-kpi-lbl">Total Devices</div>
                        </div>
                    </div>
                    <div class="mini-kpi-item">
                        <div class="mini-kpi-icon icon-purple">📱</div>
                        <div>
                            <div class="mini-kpi-val" id="totalModelsCount">25</div>
                            <div class="mini-kpi-lbl">iPhone Families</div>
                        </div>
                    </div>
                    <div class="mini-kpi-item">
                        <div class="mini-kpi-icon icon-green">💵</div>
                        <div>
                            <div class="mini-kpi-val" id="priceRangeCount">₹6,000 - ₹95,000</div>
                            <div class="mini-kpi-lbl">Price Spectrum</div>
                        </div>
                    </div>
                    <div class="mini-kpi-item">
                        <div class="mini-kpi-icon icon-amber">⚡</div>
                        <div>
                            <div class="mini-kpi-val" id="lastUpdatedText" style="color: #10B981; font-size: 13px;">Live CSV</div>
                            <div class="mini-kpi-lbl">Last Modified</div>
                        </div>
                    </div>
                </div>

                <!-- MAIN PRICING PANEL -->
                <div class="data-panel">
                    <div class="panel-header">
                        <div class="panel-title">
                            <div class="title-with-badge">
                                <h3>iPhone Pricing &amp; Deduction Matrix</h3>
                                <span class="badge-count" id="tableFilterCount">Showing 108 Devices</span>
                            </div>
                            <p>Real-time bidirectional synchronization with <code>Iphone-base-price-&amp;-deduction-logic.csv</code>. Inline price edits and deduction percentages are instantly written to the source CSV.</p>
                        </div>

                        <div class="panel-actions">
                            <button type="button" id="btnRefresh" class="btn-panel-primary">
                                <span>🔄</span> Reload Data
                            </button>
                        </div>
                    </div>

                    <!-- SEARCH & DROPDOWN TOOLBAR -->
                    <div class="panel-toolbar">
                        <div class="search-box-wrap">
                            <span class="search-ico">🔍</span>
                            <input type="text" id="productSearchInput" class="search-field" placeholder="Search iPhone model, storage, ID...">
                            <button type="button" class="search-clear-btn" id="searchClearBtn" title="Clear search">&times;</button>
                        </div>

                        <div class="filters-cluster">
                            <select id="modelFilterSelect" class="filter-box">
                                <option value="all">All iPhone Models</option>
                            </select>

                            <select id="storageFilterSelect" class="filter-box">
                                <option value="all">All Storage</option>
                            </select>

                            <button type="button" id="btnResetFilters" class="btn-reset-filters" title="Reset all filters">
                                <span>✕</span> Reset
                            </button>
                        </div>
                    </div>

                    <!-- DATA TABLE -->
                    <div class="table-scroll-container">
                        <table class="clean-table">
                            <thead>
                                <tr>
                                    <th style="width: 70px;">ID</th>
                                    <th>iPhone Model &amp; Family</th>
                                    <th style="width: 110px;">Storage</th>
                                    <th style="width: 80px;">RAM</th>
                                    <th style="width: 210px;">Base Resale Price (₹)</th>
                                    <th>Sample Deductions (% of Base)</th>
                                    <th style="width: 170px; text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="productsTableBody">
                                <tr>
                                    <td colspan="7" class="table-loading-cell">
                                        <div class="spinner-ring"></div>
                                        <div>Loading pricing matrix from CSV...</div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- ============================================================
         FULL DEDUCTION EDIT MODAL DRAWER
         ============================================================ -->
    <div class="modal-backdrop" id="editProductModal">
        <div class="modal-sheet">
            <!-- HEADER -->
            <div class="modal-sheet-header">
                <div>
                    <h2>Edit Pricing &amp; Percentage Deductions</h2>
                    <p style="font-size: 13px; color: #64748B; margin-top: 2px;">
                        <span id="modalProductName" style="font-weight: 700; color: #0F172A;">Apple iPhone</span>
                        <span id="modalProductIdTag" class="badge-tag">#0</span>
                    </p>
                </div>
                <button type="button" class="btn-close-sheet" id="btnCloseSheet" aria-label="Close">&times;</button>
            </div>

            <!-- TABS -->
            <div class="sheet-tabs-nav">
                <button type="button" class="sheet-tab-item active" data-tab="tab-general">🏷️ Base Price</button>
                <button type="button" class="sheet-tab-item" data-tab="tab-age">📅 Purchase Age (%)</button>
                <button type="button" class="sheet-tab-item" data-tab="tab-screen">🖥️ Screen &amp; Display (%)</button>
                <button type="button" class="sheet-tab-item" data-tab="tab-body">📱 Body &amp; Frame (%)</button>
                <button type="button" class="sheet-tab-item" data-tab="tab-hardware">⚙️ Hardware &amp; Tests (%)</button>
                <button type="button" class="sheet-tab-item" data-tab="tab-battery">🔋 Battery &amp; Inclusions (%)</button>
            </div>

            <!-- SCROLLABLE BODY -->
            <div class="sheet-scroll-body">
                <form id="editProductForm">
                    <!-- TAB 1: GENERAL & BASE PRICE -->
                    <div class="sheet-tab-panel active" id="tab-general">
                        <div class="form-grid-3">
                            <div class="input-block span-2">
                                <label>Model Name <span class="param-name">product_name</span></label>
                                <input type="text" name="product_name" class="sheet-field" required>
                            </div>

                            <div class="input-block">
                                <label>Base Price (₹) <span class="param-name">product_base_price</span></label>
                                <div class="affix-input-wrap">
                                    <span class="affix-sym left">₹</span>
                                    <input type="number" name="product_base_price" class="sheet-field has-left" min="0" required inputmode="numeric">
                                </div>
                            </div>

                            <div class="input-block">
                                <label>Storage <span class="param-name">product_storage</span></label>
                                <input type="text" name="product_storage" class="sheet-field">
                            </div>

                            <div class="input-block">
                                <label>RAM <span class="param-name">ram</span></label>
                                <input type="text" name="ram" class="sheet-field">
                            </div>

                            <div class="input-block">
                                <label>Variant Key <span class="param-name">product_variant</span></label>
                                <input type="text" name="product_variant" class="sheet-field">
                            </div>

                            <div class="input-block span-2">
                                <label>Description <span class="param-name">product_description</span></label>
                                <input type="text" name="product_description" class="sheet-field">
                            </div>

                            <div class="input-block">
                                <label>Image Filename <span class="param-name">product_image</span></label>
                                <input type="text" name="product_image" class="sheet-field">
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: PURCHASE AGE DEDUCTIONS -->
                    <div class="sheet-tab-panel" id="tab-age">
                        <div class="form-grid-3">
                            <div class="input-block">
                                <label>0 - 3 Months Old <span class="param-name">months_0_3</span></label>
                                <div class="affix-input-wrap">
                                    <input type="number" step="0.01" name="months_0_3" class="sheet-field has-right" inputmode="decimal">
                                    <span class="affix-sym right">%</span>
                                </div>
                            </div>

                            <div class="input-block">
                                <label>3 - 6 Months Old <span class="param-name">months_3_6</span></label>
                                <div class="affix-input-wrap">
                                    <input type="number" step="0.01" name="months_3_6" class="sheet-field has-right" inputmode="decimal">
                                    <span class="affix-sym right">%</span>
                                </div>
                            </div>

                            <div class="input-block">
                                <label>6 - 11 Months Old <span class="param-name">months_6_11</span></label>
                                <div class="affix-input-wrap">
                                    <input type="number" step="0.01" name="months_6_11" class="sheet-field has-right" inputmode="decimal">
                                    <span class="affix-sym right">%</span>
                                </div>
                            </div>

                            <div class="input-block">
                                <label>11+ Months Old <span class="param-name">months_11_more</span></label>
                                <div class="affix-input-wrap">
                                    <input type="number" step="0.01" name="months_11_more" class="sheet-field has-right" inputmode="decimal">
                                    <span class="affix-sym right">%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 3: SCREEN & DISPLAY DEDUCTIONS -->
                    <div class="sheet-tab-panel" id="tab-screen">
                        <div class="form-grid-3">
                            <div class="input-block">
                                <label>1 - 2 Screen Scratches <span class="param-name">scratch_screen_1_2</span></label>
                                <div class="affix-input-wrap">
                                    <input type="number" step="0.01" name="scratch_screen_1_2" class="sheet-field has-right" inputmode="decimal">
                                    <span class="affix-sym right">%</span>
                                </div>
                            </div>

                            <div class="input-block">
                                <label>3 - 4 Screen Scratches <span class="param-name">scratch_screen_3_4</span></label>
                                <div class="affix-input-wrap">
                                    <input type="number" step="0.01" name="scratch_screen_3_4" class="sheet-field has-right" inputmode="decimal">
                                    <span class="affix-sym right">%</span>
                                </div>
                            </div>

                            <div class="input-block">
                                <label>Multiple / Heavy Scratches <span class="param-name">multiple_scratches_screen</span></label>
                                <div class="affix-input-wrap">
                                    <input type="number" step="0.01" name="multiple_scratches_screen" class="sheet-field has-right" inputmode="decimal">
                                    <span class="affix-sym right">%</span>
                                </div>
                            </div>

                            <div class="input-block">
                                <label>Front Glass Cracked <span class="param-name">glass_cracked</span></label>
                                <div class="affix-input-wrap">
                                    <input type="number" step="0.01" name="glass_cracked" class="sheet-field has-right" inputmode="decimal">
                                    <span class="affix-sym right">%</span>
                                </div>
                            </div>

                            <div class="input-block">
                                <label>Dots on Display <span class="param-name">dots_on_display</span></label>
                                <div class="affix-input-wrap">
                                    <input type="number" step="0.01" name="dots_on_display" class="sheet-field has-right" inputmode="decimal">
                                    <span class="affix-sym right">%</span>
                                </div>
                            </div>

                            <div class="input-block">
                                <label>Lines on Display <span class="param-name">lines_on_display</span></label>
                                <div class="affix-input-wrap">
                                    <input type="number" step="0.01" name="lines_on_display" class="sheet-field has-right" inputmode="decimal">
                                    <span class="affix-sym right">%</span>
                                </div>
                            </div>

                            <div class="input-block">
                                <label>Touch Not Working <span class="param-name">touch_not_working</span></label>
                                <div class="affix-input-wrap">
                                    <input type="number" step="0.01" name="touch_not_working" class="sheet-field has-right" inputmode="decimal">
                                    <span class="affix-sym right">%</span>
                                </div>
                            </div>

                            <div class="input-block">
                                <label>No Display / Blackout <span class="param-name">no_display</span></label>
                                <div class="affix-input-wrap">
                                    <input type="number" step="0.01" name="no_display" class="sheet-field has-right" inputmode="decimal">
                                    <span class="affix-sym right">%</span>
                                </div>
                            </div>

                            <div class="input-block">
                                <label>Loose Screen / Lifted <span class="param-name">loose_screen</span></label>
                                <div class="affix-input-wrap">
                                    <input type="number" step="0.01" name="loose_screen" class="sheet-field has-right" inputmode="decimal">
                                    <span class="affix-sym right">%</span>
                                </div>
                            </div>

                            <div class="input-block">
                                <label>Color Fade / Burn-in <span class="param-name">color_fade</span></label>
                                <div class="affix-input-wrap">
                                    <input type="number" step="0.01" name="color_fade" class="sheet-field has-right" inputmode="decimal">
                                    <span class="affix-sym right">%</span>
                                </div>
                            </div>

                            <div class="input-block">
                                <label>Screen Flickering <span class="param-name">flickering</span></label>
                                <div class="affix-input-wrap">
                                    <input type="number" step="0.01" name="flickering" class="sheet-field has-right" inputmode="decimal">
                                    <span class="affix-sym right">%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 4: BODY & FRAME DEDUCTIONS -->
                    <div class="sheet-tab-panel" id="tab-body">
                        <div class="form-grid-3">
                            <div class="input-block">
                                <label>1 - 2 Body Scratches <span class="param-name">scratch_body_1_2</span></label>
                                <div class="affix-input-wrap">
                                    <input type="number" step="0.01" name="scratch_body_1_2" class="sheet-field has-right" inputmode="decimal">
                                    <span class="affix-sym right">%</span>
                                </div>
                            </div>

                            <div class="input-block">
                                <label>3 - 4 Body Scratches <span class="param-name">scratch_body_3_4</span></label>
                                <div class="affix-input-wrap">
                                    <input type="number" step="0.01" name="scratch_body_3_4" class="sheet-field has-right" inputmode="decimal">
                                    <span class="affix-sym right">%</span>
                                </div>
                            </div>

                            <div class="input-block">
                                <label>Multiple Body Scratches <span class="param-name">multiple_scratches_body</span></label>
                                <div class="affix-input-wrap">
                                    <input type="number" step="0.01" name="multiple_scratches_body" class="sheet-field has-right" inputmode="decimal">
                                    <span class="affix-sym right">%</span>
                                </div>
                            </div>

                            <div class="input-block">
                                <label>1 or 2 Dents <span class="param-name">dents_1_or_2</span></label>
                                <div class="affix-input-wrap">
                                    <input type="number" step="0.01" name="dents_1_or_2" class="sheet-field has-right" inputmode="decimal">
                                    <span class="affix-sym right">%</span>
                                </div>
                            </div>

                            <div class="input-block">
                                <label>Multiple Dents <span class="param-name">multiple_dents</span></label>
                                <div class="affix-input-wrap">
                                    <input type="number" step="0.01" name="multiple_dents" class="sheet-field has-right" inputmode="decimal">
                                    <span class="affix-sym right">%</span>
                                </div>
                            </div>

                            <div class="input-block">
                                <label>Body Curved / Bent <span class="param-name">body_curved</span></label>
                                <div class="affix-input-wrap">
                                    <input type="number" step="0.01" name="body_curved" class="sheet-field has-right" inputmode="decimal">
                                    <span class="affix-sym right">%</span>
                                </div>
                            </div>

                            <div class="input-block">
                                <label>Back Glass Broken <span class="param-name">back_glass_broken</span></label>
                                <div class="affix-input-wrap">
                                    <input type="number" step="0.01" name="back_glass_broken" class="sheet-field has-right" inputmode="decimal">
                                    <span class="affix-sym right">%</span>
                                </div>
                            </div>

                            <div class="input-block">
                                <label>Camera Glass Broken <span class="param-name">camera_glass_broken</span></label>
                                <div class="affix-input-wrap">
                                    <input type="number" step="0.01" name="camera_glass_broken" class="sheet-field has-right" inputmode="decimal">
                                    <span class="affix-sym right">%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 5: HARDWARE DEDUCTIONS -->
                    <div class="sheet-tab-panel" id="tab-hardware">
                        <div class="form-grid-3">
                            <div class="input-block">
                                <label>Front Camera Fault <span class="param-name">front_camera_not_working</span></label>
                                <div class="affix-input-wrap">
                                    <input type="number" step="0.01" name="front_camera_not_working" class="sheet-field has-right" inputmode="decimal">
                                    <span class="affix-sym right">%</span>
                                </div>
                            </div>

                            <div class="input-block">
                                <label>Back Camera Fault <span class="param-name">back_camera_not_working</span></label>
                                <div class="affix-input-wrap">
                                    <input type="number" step="0.01" name="back_camera_not_working" class="sheet-field has-right" inputmode="decimal">
                                    <span class="affix-sym right">%</span>
                                </div>
                            </div>

                            <div class="input-block">
                                <label>Face ID Broken <span class="param-name">face_id_not_working</span></label>
                                <div class="affix-input-wrap">
                                    <input type="number" step="0.01" name="face_id_not_working" class="sheet-field has-right" inputmode="decimal">
                                    <span class="affix-sym right">%</span>
                                </div>
                            </div>

                            <div class="input-block">
                                <label>Fingerprint Broken <span class="param-name">finger_print_not_working</span></label>
                                <div class="affix-input-wrap">
                                    <input type="number" step="0.01" name="finger_print_not_working" class="sheet-field has-right" inputmode="decimal">
                                    <span class="affix-sym right">%</span>
                                </div>
                            </div>

                            <div class="input-block">
                                <label>Battery Faulty <span class="param-name">battery_faulty</span></label>
                                <div class="affix-input-wrap">
                                    <input type="number" step="0.01" name="battery_faulty" class="sheet-field has-right" inputmode="decimal">
                                    <span class="affix-sym right">%</span>
                                </div>
                            </div>

                            <div class="input-block">
                                <label>Charging Port Fault <span class="param-name">charging_port_issue</span></label>
                                <div class="affix-input-wrap">
                                    <input type="number" step="0.01" name="charging_port_issue" class="sheet-field has-right" inputmode="decimal">
                                    <span class="affix-sym right">%</span>
                                </div>
                            </div>

                            <div class="input-block">
                                <label>Loudspeaker Issue <span class="param-name">speaker_not_working</span></label>
                                <div class="affix-input-wrap">
                                    <input type="number" step="0.01" name="speaker_not_working" class="sheet-field has-right" inputmode="decimal">
                                    <span class="affix-sym right">%</span>
                                </div>
                            </div>

                            <div class="input-block">
                                <label>Audio IC Issue <span class="param-name">audio_ic_problem</span></label>
                                <div class="affix-input-wrap">
                                    <input type="number" step="0.01" name="audio_ic_problem" class="sheet-field has-right" inputmode="decimal">
                                    <span class="affix-sym right">%</span>
                                </div>
                            </div>

                            <div class="input-block">
                                <label>Power Button Issue <span class="param-name">power_button_issue</span></label>
                                <div class="affix-input-wrap">
                                    <input type="number" step="0.01" name="power_button_issue" class="sheet-field has-right" inputmode="decimal">
                                    <span class="affix-sym right">%</span>
                                </div>
                            </div>

                            <div class="input-block">
                                <label>Volume Buttons Issue <span class="param-name">volume</span></label>
                                <div class="affix-input-wrap">
                                    <input type="number" step="0.01" name="volume" class="sheet-field has-right" inputmode="decimal">
                                    <span class="affix-sym right">%</span>
                                </div>
                            </div>

                            <div class="input-block">
                                <label>Wi-Fi Issue <span class="param-name">wifi_issues</span></label>
                                <div class="affix-input-wrap">
                                    <input type="number" step="0.01" name="wifi_issues" class="sheet-field has-right" inputmode="decimal">
                                    <span class="affix-sym right">%</span>
                                </div>
                            </div>

                            <div class="input-block">
                                <label>Bluetooth Issue <span class="param-name">bluetooth_issue</span></label>
                                <div class="affix-input-wrap">
                                    <input type="number" step="0.01" name="bluetooth_issue" class="sheet-field has-right" inputmode="decimal">
                                    <span class="affix-sym right">%</span>
                                </div>
                            </div>

                            <div class="input-block">
                                <label>Vibrator Issue <span class="param-name">vibrator</span></label>
                                <div class="affix-input-wrap">
                                    <input type="number" step="0.01" name="vibrator" class="sheet-field has-right" inputmode="decimal">
                                    <span class="affix-sym right">%</span>
                                </div>
                            </div>

                            <div class="input-block">
                                <label>Sensors Issue <span class="param-name">sensor_issues</span></label>
                                <div class="affix-input-wrap">
                                    <input type="number" step="0.01" name="sensor_issues" class="sheet-field has-right" inputmode="decimal">
                                    <span class="affix-sym right">%</span>
                                </div>
                            </div>

                            <div class="input-block">
                                <label>Headphone Jack <span class="param-name">headphone_jackissue</span></label>
                                <div class="affix-input-wrap">
                                    <input type="number" step="0.01" name="headphone_jackissue" class="sheet-field has-right" inputmode="decimal">
                                    <span class="affix-sym right">%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 6: BATTERY & INCLUSIONS -->
                    <div class="sheet-tab-panel" id="tab-battery">
                        <div class="form-grid-3">
                            <div class="input-block">
                                <label>Battery &lt; 80% (Service) <span class="param-name">battery_less_80</span></label>
                                <div class="affix-input-wrap">
                                    <input type="number" step="0.01" name="battery_less_80" class="sheet-field has-right" inputmode="decimal">
                                    <span class="affix-sym right">%</span>
                                </div>
                            </div>

                            <div class="input-block">
                                <label>Battery &ge; 80% (Healthy) <span class="param-name">battery_greater_80</span></label>
                                <div class="affix-input-wrap">
                                    <input type="number" step="0.01" name="battery_greater_80" class="sheet-field has-right" inputmode="decimal">
                                    <span class="affix-sym right">%</span>
                                </div>
                            </div>

                            <div class="input-block">
                                <label>Box Missing <span class="param-name">box</span></label>
                                <div class="affix-input-wrap">
                                    <input type="number" step="0.01" name="box" class="sheet-field has-right" inputmode="decimal">
                                    <span class="affix-sym right">%</span>
                                </div>
                            </div>

                            <div class="input-block">
                                <label>Charger Missing <span class="param-name">charger</span></label>
                                <div class="affix-input-wrap">
                                    <input type="number" step="0.01" name="charger" class="sheet-field has-right" inputmode="decimal">
                                    <span class="affix-sym right">%</span>
                                </div>
                            </div>

                            <div class="input-block">
                                <label>Invoice Missing <span class="param-name">invoice</span></label>
                                <div class="affix-input-wrap">
                                    <input type="number" step="0.01" name="invoice" class="sheet-field has-right" inputmode="decimal">
                                    <span class="affix-sym right">%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- FOOTER -->
            <div class="modal-sheet-footer">
                <button type="button" class="btn-sheet-cancel" id="btnCancelSheet">Cancel</button>
                <button type="button" class="btn-sheet-save" id="btnSaveSheet">
                    <span>💾</span> Save Changes Real-Time
                </button>
            </div>
        </div>
    </div>

    <!-- TOAST STACK -->
    <div class="toast-stack" id="toastStack"></div>

    <script src="assets/admin.js"></script>
</body>
</html>
