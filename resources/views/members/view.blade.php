

<!DOCTYPE html>
<html lang="en">
    <head>
        <style>
        /* Force modal and backdrop to appear on top */
        .modal-backdrop.show {
            z-index: 2050 !important;
        }
        .modal.show {
            z-index: 2100 !important;
            display: block !important;
        }
        #archiveMemberModal {
            z-index: 2100 !important;
        }
        </style>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Waumini Link - Dashboard</title>
        <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" />
        <link href="{{ asset('assets/css/datatables.min.css') }}" rel="stylesheet" />
        <link href="{{ asset('css/styles.css') }}" rel="stylesheet" />
        <script src="{{ asset('assets/js/fontawesome.min.js') }}" crossorigin="anonymous"></script>
        <!-- SweetAlert2 CDN -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <style>
            /* Compact Filter Section Styles */
            #filtersForm {
                transition: all 0.3s ease;
            }
            #filtersForm .card-header {
                transition: background-color 0.2s ease;
            }
            #filterBody {
                transition: all 0.3s ease;
            }
            
            /* Desktop: Always show filters, make header non-clickable */
            @media (min-width: 769px) {
                .filter-header {
                    cursor: default !important;
                    pointer-events: none !important;
                }
                .filter-header .fa-chevron-down {
                    display: none !important;
                }
                #filterBody {
                    display: block !important;
                }
            }
            
            /* Mobile: Collapsible */
            @media (max-width: 768px) {
                .filter-header {
                    cursor: pointer !important;
                    pointer-events: auto !important;
                }
                #filterBody {
                    display: none;
                }
                #filterToggleIcon {
                    font-size: 1.1rem !important;
                    width: 24px !important;
                    height: 24px !important;
                    display: inline-flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                    cursor: pointer !important;
                    transition: transform 0.3s ease !important;
                    flex-shrink: 0 !important;
                }
            }
            
            /* Compact Actions Section Styles */
            .actions-card {
                transition: all 0.3s ease;
            }
            .actions-card .card-header {
                user-select: none;
                transition: background-color 0.2s ease;
            }
            .actions-card .card-header:hover {
                background-color: #f8f9fa !important;
            }
            .actions-card .card-header i {
                transition: transform 0.3s ease;
            }
            .actions-card .card-header h1 {
                color: #212529 !important;
            }
            .actions-card .card-header h1 i {
                color: #212529 !important;
            }
            #actionsBody {
                transition: all 0.3s ease;
            }
            #actionsBody .btn-sm {
                font-size: 0.875rem;
                padding: 0.375rem 0.75rem;
            }
            
            /* Desktop: Always show actions, make header non-clickable */
            @media (min-width: 769px) {
                .actions-header {
                    cursor: default !important;
                    pointer-events: none !important;
                }
                .actions-header .fa-chevron-down {
                    display: none !important;
                }
                #actionsBody {
                    display: block !important;
                }
            }
            
            /* Desktop Sidebar Toggle Button - Ensure proper size */
            @media (min-width: 769px) {
                #sidebarToggle {
                    font-size: 1.5rem !important;
                    padding: 0.5rem !important;
                    min-width: 44px !important;
                    min-height: 44px !important;
                }
                
                #sidebarToggle i {
                    font-size: 1.5rem !important;
                }
            }
            
            /* Mobile: Collapsible */
            @media (max-width: 768px) {
                .actions-header {
                    cursor: pointer !important;
                    pointer-events: auto !important;
                }
                #actionsBody {
                    display: none;
                    transition: all 0.3s ease;
                }
                #actionsToggleIcon {
                    display: block !important;
                    font-size: 1.1rem !important;
                    width: 24px !important;
                    height: 24px !important;
                    display: inline-flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                    cursor: pointer !important;
                    transition: transform 0.3s ease !important;
                    flex-shrink: 0 !important;
                }
            }
            #filtersForm .input-group-text {
                border-right: none;
            }
            #filtersForm .form-control:focus,
            #filtersForm .form-select:focus {
                border-left: none;
                box-shadow: none;
            }
            #filtersForm .form-control:focus + .input-group-text,
            #filtersForm .input-group:focus-within .input-group-text {
                border-color: #86b7fe;
            }
            
            .logo-white-section {
                background-color: white !important;
                border-radius: 8px;
                margin: 8px 0;
                padding: 8px 16px !important;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                transition: all 0.3s ease;
            }
            .logo-white-section:hover {
                transform: translateY(-1px);
                box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            }
            .navbar-brand .logo {
                transition: all 0.3s ease;
            }
            .navbar-brand .logo:hover {
                transform: scale(1.05);
            }
            .navbar-brand {
                min-height: 60px;
                display: flex !important;
                align-items: center !important;
            }
            .sb-sidenav {
                background: linear-gradient(180deg, #17082d 0%, #17082ddd 100%) !important;
            }
            .sb-sidenav .nav-link {
                color: white !important;
                font-weight: 500 !important;
                transition: all 0.3s ease;
                padding: 0.75rem 1rem !important;
            }
            .sb-sidenav .nav-link:hover {
                background-color: rgba(255, 255, 255, 0.1) !important;
                color: white !important;
            }
            .sb-sidenav .nav-link.active {
                background-color: rgba(255, 255, 255, 0.15) !important;
                font-weight: 600 !important;
            }
            .sb-sidenav .sb-sidenav-menu-heading {
                color: #ffffff !important;
                font-weight: 700 !important;
                text-transform: uppercase !important;
                letter-spacing: 0.8px !important;
                font-size: 0.75rem !important;
                padding: 0.75rem 1rem 0.25rem 1rem !important;
                background-color: rgba(255, 255, 255, 0.1) !important;
                border-radius: 4px !important;
                margin: 0.5rem 0.5rem 0.25rem 0.5rem !important;
            }
            .sb-sidenav .sb-nav-link-icon {
                color: rgba(255, 255, 255, 0.8) !important;
                margin-right: 0.5rem !important;
            }
            .sb-sidenav .sb-sidenav-collapse-arrow {
                color: rgba(255, 255, 255, 0.8) !important;
            }
            .sb-sidenav .sb-sidenav-menu-nested .nav-link {
                padding-left: 2.5rem !important;
                font-size: 0.9rem !important;
            }
            .sb-sidenav .sb-sidenav-footer {
                background-color: rgba(255, 255, 255, 0.1) !important;
                color: white !important;
                border-top: 1px solid rgba(255, 255, 255, 0.1) !important;
            }
            .sb-sidenav .sb-sidenav-footer .small {
                font-weight: 600 !important;
            }
            /* Ensure all sidebar text is visible */
            .sb-sidenav * {
                color: inherit !important;
            }
            .card-header {
                color: white !important;
                font-weight: 600;
            }
            /* Override for actions card header - white background needs dark text */
            .actions-card .card-header {
                color: #212529 !important;
            }
            .actions-card .card-header h1 {
                color: #212529 !important;
            }
            .actions-card .card-header h1 i {
                color: #212529 !important;
            }
            
            /* Reduce gap between topbar and content */
            #layoutSidenav_content main {
                padding-top: 0 !important;
            }
            .container-fluid {
                padding-top: 0 !important;
            }
            .card .small.text-white-50 {
                color: white !important;
                font-weight: 500;
            }
            /* Interactive table styling for details tables */
            .table.interactive-table tbody tr {
                transition: background-color 0.2s ease, box-shadow 0.2s ease;
            }
            .table.interactive-table tbody tr:hover {
                background-color: #f8f9ff;
            }
            .table.interactive-table tbody tr td:first-child {
                border-left: 4px solid #5b2a86;
            }
            /* Slightly wider member details modal */
            #memberDetailsModal .modal-dialog { max-width: 700px; }
            #memberDetailsModal .modal-footer {
                background: linear-gradient(135deg, #1f2b6c 0%, #5b2a86 100%);
                border-top: 0;
                color: #ffffff;
            }
            #memberDetailsModal .modal-footer a.emca-link { color: #ffffff; text-decoration: none; }
            #memberDetailsModal .modal-footer a.emca-link:hover { text-decoration: underline; opacity: 0.95; }
            /* QR styling */
            #inlineQrImg { border: 3px solid #5b2a86; border-radius: 8px; padding: 4px; background: #ffffff; }
            #qrSpinner { width: 2.5rem; height: 2.5rem; }
            
            /* Mobile Responsive Styles */
            @media (max-width: 768px) {
                #layoutSidenav_content {
                    margin-top: -50px !important;
                }
                .container-fluid {
                    padding-left: 0.5rem !important;
                    padding-right: 0.5rem !important;
                }
                
                /* Actions card improvements */
                .actions-card {
                    margin-bottom: 1rem !important;
                    transition: all 0.3s ease;
                }
                .actions-card .card-header {
                    padding: 0.5rem 0.75rem !important;
                    user-select: none;
                    transition: background-color 0.2s ease;
                }
                .actions-card .card-header:hover {
                    background-color: #f8f9fa !important;
                }
                .actions-card .card-header h1 {
                    font-size: 1.25rem !important;
                    margin: 0 !important;
                }
                .actions-card .card-body {
                    padding: 0.75rem !important;
                }
                #actionsBody .btn-sm {
                    font-size: 0.8125rem !important;
                    padding: 0.375rem 0.625rem !important;
                }
                
                /* Header adjustments */
                h1 {
                    font-size: 1.25rem !important;
                }
                
                /* Hide button text on mobile, show only icons */
                .btn-mobile-icon-only {
                    padding: 0.5rem !important;
                    min-width: 44px !important;
                    height: 44px !important;
                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                }
                .btn-mobile-icon-only .btn-text {
                    display: none !important;
                }
                .btn-mobile-icon-only i {
                    margin: 0 !important;
                    font-size: 1rem !important;
                }
                
                /* View toggle buttons */
                .btn-group {
                    width: 100% !important;
                }
                .btn-group .btn {
                    flex: 1 !important;
                }
                
                /* Make tabs scrollable on mobile */
                .nav-tabs {
                    overflow-x: auto;
                    flex-wrap: nowrap;
                    -webkit-overflow-scrolling: touch;
                    display: flex !important;
                    border-bottom: 2px solid #dee2e6;
                    padding-bottom: 0;
                }
                .nav-tabs .nav-item {
                    white-space: nowrap;
                    flex-shrink: 0;
                    min-width: auto;
                }
                .nav-tabs .nav-link {
                    padding: 0.75rem 1rem !important;
                    font-size: 0.875rem !important;
                    border-radius: 0.5rem 0.5rem 0 0 !important;
                }
                
                /* Tab content */
                .tab-content {
                    padding: 1rem 0.5rem !important;
                    border: none !important;
                }
                
                /* Table responsive improvements */
                .table-responsive {
                    border: none;
                    overflow-x: auto;
                    -webkit-overflow-scrolling: touch;
                }
                .table {
                    font-size: 0.875rem;
                    min-width: 600px;
                }
                .table th,
                .table td {
                    padding: 0.5rem 0.5rem;
                    white-space: nowrap;
                }
                
                /* Card view improvements */
                .card-view-item {
                    margin-bottom: 1rem;
                }
                
                /* Modal improvements */
                .modal-dialog {
                    margin: 0.5rem !important;
                    max-width: calc(100% - 1rem) !important;
                }
                .modal-dialog.modal-lg {
                    max-width: calc(100% - 1rem) !important;
                }
                .modal-content {
                    border-radius: 0.5rem;
                }
                .modal-header,
                .modal-body,
                .modal-footer {
                    padding: 1rem !important;
                }
                
                /* Sidebar Toggle Button - Match size with other toggle buttons */
                #sidebarToggle {
                    font-size: 1.1rem !important;
                    padding: 0.5rem !important;
                    min-width: 40px !important;
                    min-height: 40px !important;
                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                    margin-right: 0.75rem !important;
                    margin-left: 0 !important;
                    order: -1 !important;
                }
                
                #sidebarToggle i {
                    font-size: 1.1rem !important;
                }
                
                /* Ensure navbar has proper padding on mobile to prevent cutoff */
                .sb-topnav {
                    padding-left: 0.75rem !important;
                    padding-right: 0.5rem !important;
                    overflow-x: hidden !important;
                    position: relative !important;
                    max-width: 100vw !important;
                    width: 100% !important;
                }
                
                /* Ensure navbar container doesn't cut off content */
                body.sb-nav-fixed .sb-topnav {
                    margin-left: 0 !important;
                    width: 100% !important;
                    max-width: 100vw !important;
                }
                
                /* Ensure navbar content doesn't overflow */
                .sb-topnav .navbar-nav,
                .sb-topnav .d-flex {
                    max-width: 100% !important;
                    overflow-x: hidden !important;
                }
                
                /* Welcome message on mobile */
                .sb-topnav .navbar-text {
                    font-size: 0.85rem !important;
                    margin-left: 0.5rem !important;
                    margin-right: auto !important;
                    flex: 1 !important;
                    min-width: 0 !important;
                    white-space: nowrap !important;
                }
                
                /* Ensure profile dropdown menu is hidden by default, visible when active */
                .sb-topnav .dropdown-menu {
                    position: absolute !important;
                    z-index: 1050 !important;
                    right: 0 !important;
                    left: auto !important;
                    margin-top: 0.5rem !important;
                    min-width: 180px !important;
                    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
                    background-color: #fff !important;
                    border: 1px solid rgba(0, 0, 0, 0.15) !important;
                    border-radius: 0.375rem !important;
                    display: none !important;
                    opacity: 0 !important;
                    visibility: hidden !important;
                }
                
                .sb-topnav .dropdown-menu.show {
                    display: block !important;
                    opacity: 1 !important;
                    visibility: visible !important;
                }
                
                /* Ensure dropdown items are visible */
                .sb-topnav .dropdown-menu .dropdown-item {
                    padding: 0.5rem 1rem !important;
                    font-size: 0.9rem !important;
                    white-space: nowrap !important;
                    color: #212529 !important;
                    display: block !important;
                }
                
                .sb-topnav .dropdown-menu .dropdown-item:hover {
                    background-color: #f8f9fa !important;
                }
                
                /* Hide logo on mobile */
                .sb-topnav .navbar-brand,
                .sb-topnav .logo-white-section {
                    display: none !important;
                }
                
                /* Ensure navbar nav items are visible and don't shrink */
                .sb-topnav .navbar-nav {
                    flex-shrink: 0 !important;
                    display: flex !important;
                    align-items: center !important;
                    margin-left: auto !important;
                }
                
                .sb-topnav .navbar-nav .nav-item {
                    flex-shrink: 0 !important;
                    display: flex !important;
                    align-items: center !important;
                }
                
                /* Profile dropdown icon - ensure it's always visible */
                #navbarDropdown {
                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                    padding: 0.5rem !important;
                    min-width: 40px !important;
                    min-height: 40px !important;
                }
                
                #navbarDropdown i {
                    font-size: 1.1rem !important;
                    display: block !important;
                }
                
                /* Notification icon spacing on mobile */
                #notificationDropdown {
                    margin-right: 0.5rem !important;
                }
                
                /* Filter section improvements - Compact */
                #filtersForm {
                    margin-bottom: 1rem !important;
                }
                #filtersForm .card-header {
                    padding: 0.75rem 1rem !important;
                }
                #filtersForm .card-body {
                    padding: 1rem !important;
                }
                #filtersForm .form-label {
                    font-size: 0.75rem !important;
                    margin-bottom: 0.25rem !important;
                }
                #filtersForm .form-control,
                #filtersForm .form-select {
                    font-size: 0.875rem !important;
                    padding: 0.375rem 0.5rem !important;
                }
                #filtersForm .input-group-sm {
                    height: auto;
                }
                #filtersForm .btn-sm {
                    padding: 0.375rem 0.75rem !important;
                    font-size: 0.875rem !important;
                }
                
                /* Action buttons in table */
                .table .btn {
                    padding: 0.375rem 0.5rem;
                    font-size: 0.75rem;
                }
                .table .btn i {
                    margin: 0 !important;
                }
                .table .btn .btn-text {
                    display: none;
                }
                
                /* Better spacing */
                .mb-3 {
                    margin-bottom: 1rem !important;
                }
                .mt-4 {
                    margin-top: 1rem !important;
                }
            }
            
            @media (max-width: 576px) {
                /* Extra small devices */
                #layoutSidenav_content {
                    margin-top: -50px !important;
                }
                .container-fluid {
                    padding-left: 0.25rem !important;
                    padding-right: 0.25rem !important;
                }
                
                /* Actions card on mobile */
                .actions-card .card-header {
                    padding: 0.5rem 0.625rem !important;
                }
                
                /* Toggle icons - Extra small mobile */
                #actionsToggleIcon,
                #filterToggleIcon {
                    font-size: 1rem !important;
                    width: 22px !important;
                    height: 22px !important;
                }
                .actions-card .card-header h1 {
                    font-size: 1.1rem !important;
                }
                .actions-card .card-body {
                    padding: 0.75rem 0.5rem !important;
                }
                #actionsBody .d-flex {
                    gap: 0.5rem !important;
                }
                #actionsBody .btn-sm {
                    font-size: 0.75rem !important;
                    padding: 0.375rem 0.5rem !important;
                }
                
                h2 {
                    font-size: 1.25rem !important;
                }
                
                .btn {
                    font-size: 0.8125rem !important;
                    padding: 0.5rem 0.625rem !important;
                }
                
                .btn-mobile-icon-only {
                    padding: 0.5rem !important;
                    min-width: 40px !important;
                    height: 40px !important;
                }
                
                .table {
                    font-size: 0.75rem;
                }
                
                .tab-badge {
                    font-size: 0.7rem !important;
                    padding: 2px 6px !important;
                    margin-left: 4px !important;
                }
                
                /* Sidebar Toggle Button - Extra Small Mobile */
                #sidebarToggle {
                    font-size: 1rem !important;
                    padding: 0.45rem !important;
                    min-width: 38px !important;
                    min-height: 38px !important;
                    margin-right: 0.5rem !important;
                    margin-left: 0 !important;
                    order: -1 !important;
                }
                
                #sidebarToggle i {
                    font-size: 1rem !important;
                }
                
                /* Ensure navbar has proper padding on extra small mobile */
                .sb-topnav {
                    padding-left: 0.5rem !important;
                    padding-right: 0.25rem !important;
                    overflow-x: hidden !important;
                    position: relative !important;
                    max-width: 100vw !important;
                    width: 100% !important;
                }
                
                /* Ensure navbar container doesn't cut off content on extra small */
                body.sb-nav-fixed .sb-topnav {
                    margin-left: 0 !important;
                    width: 100% !important;
                    max-width: 100vw !important;
                }
                
                /* Ensure navbar content doesn't overflow on extra small */
                .sb-topnav .navbar-nav,
                .sb-topnav .d-flex {
                    max-width: 100% !important;
                    overflow-x: hidden !important;
                }
                
                /* Welcome message on extra small mobile */
                .sb-topnav .navbar-text {
                    font-size: 0.8rem !important;
                    margin-left: 0.25rem !important;
                    white-space: nowrap !important;
                    overflow: hidden !important;
                    text-overflow: ellipsis !important;
                }
                
                /* Ensure profile dropdown menu is hidden by default on extra small mobile */
                .sb-topnav .dropdown-menu {
                    position: absolute !important;
                    z-index: 1050 !important;
                    right: 0 !important;
                    left: auto !important;
                    margin-top: 0.5rem !important;
                    min-width: 160px !important;
                    max-width: calc(100vw - 1rem) !important;
                    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
                    background-color: #fff !important;
                    border: 1px solid rgba(0, 0, 0, 0.15) !important;
                    border-radius: 0.375rem !important;
                    display: none !important;
                    opacity: 0 !important;
                    visibility: hidden !important;
                }
                
                .sb-topnav .dropdown-menu.show {
                    display: block !important;
                    opacity: 1 !important;
                    visibility: visible !important;
                }
                
                /* Ensure dropdown items are visible on extra small */
                .sb-topnav .dropdown-menu .dropdown-item {
                    padding: 0.5rem 1rem !important;
                    font-size: 0.85rem !important;
                    white-space: nowrap !important;
                    color: #212529 !important;
                    display: block !important;
                }
                
                .sb-topnav .dropdown-menu .dropdown-item:hover {
                    background-color: #f8f9fa !important;
                }
                
                /* Ensure navbar doesn't clip dropdown on extra small */
                .sb-topnav {
                    overflow: visible !important;
                }
                
                .sb-topnav .navbar-nav {
                    overflow: visible !important;
                }
                
                /* Hide logo on extra small mobile */
                .sb-topnav .navbar-brand,
                .sb-topnav .logo-white-section {
                    display: none !important;
                }
                
                /* Ensure navbar nav items are visible on extra small mobile */
                .sb-topnav .navbar-nav {
                    flex-shrink: 0 !important;
                    display: flex !important;
                    align-items: center !important;
                    margin-left: auto !important;
                }
                
                .sb-topnav .navbar-nav .nav-item {
                    flex-shrink: 0 !important;
                    display: flex !important;
                    align-items: center !important;
                }
                
                /* Profile dropdown icon - ensure it's visible on extra small */
                #navbarDropdown {
                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                    padding: 0.45rem !important;
                    min-width: 38px !important;
                    min-height: 38px !important;
                }
                
                #navbarDropdown i {
                    font-size: 1rem !important;
                    display: block !important;
                }
                
                /* Notification icon spacing on extra small mobile */
                #notificationDropdown {
                    margin-right: 0.5rem !important;
                }
                
                /* Make tabs more compact */
                .nav-tabs {
                    padding: 0 0.25rem;
                }
                .nav-tabs .nav-link {
                    padding: 0.625rem 0.75rem !important;
                    font-size: 0.8125rem !important;
                }
                
                /* Tab content */
                .tab-content {
                    padding: 0.75rem 0.25rem !important;
                }
                
                /* Mobile card improvements */
                .mobile-card-row {
                    padding: 0.75rem !important;
                }
                
                .mobile-card-row .card-body-row {
                    grid-template-columns: 1fr !important;
                    gap: 0.5rem !important;
                }
                
                /* Card view full width on mobile */
                .card-view-item {
                    margin-bottom: 0.75rem;
                }
                
                /* Better spacing for filters */
                .card-body {
                    padding: 0.75rem 0.5rem !important;
                }
                
                /* Filter form improvements - Extra compact on mobile */
                #filtersForm .card-body {
                    padding: 0.75rem 0.5rem !important;
                }
                #filtersForm .row.g-2 {
                    margin: 0 !important;
                }
                #filtersForm .row.g-2 > [class*="col-"] {
                    padding-left: 0.375rem !important;
                    padding-right: 0.375rem !important;
                    margin-bottom: 0.5rem !important;
                }
                #filtersForm .mb-3 {
                    margin-bottom: 0.75rem !important;
                }
                #filtersForm .form-label {
                    font-size: 0.7rem !important;
                }
                #filtersForm .form-control,
                #filtersForm .form-select {
                    font-size: 0.8125rem !important;
                    padding: 0.25rem 0.5rem !important;
                }
                #filtersForm .input-group-sm .input-group-text {
                    padding: 0.25rem 0.5rem !important;
                    font-size: 0.8125rem !important;
                }
                
                /* Button group full width */
                .d-flex.gap-2 {
                    width: 100% !important;
                }
                
                .d-flex.gap-2 > * {
                    flex: 1 1 auto !important;
                    min-width: 0 !important;
                }
                
                /* Modal full screen on very small devices */
                .modal-dialog {
                    margin: 0 !important;
                    max-width: 100% !important;
                }
                .modal-dialog.modal-lg {
                    max-width: 100% !important;
                    height: 100vh !important;
                }
                .modal-content {
                    border-radius: 0 !important;
                    height: 100% !important;
                    display: flex !important;
                    flex-direction: column !important;
                }
                .modal-body {
                    flex: 1 !important;
                    overflow-y: auto !important;
                }
                
                /* Table horizontal scroll indicator */
                .table-responsive {
                    position: relative;
                }
                .table-responsive::after {
                    content: '← Swipe to see more →';
                    display: block;
                    text-align: center;
                    padding: 0.5rem;
                    color: #6c757d;
                    font-size: 0.7rem;
                    background: #f8f9fa;
                    border-top: 1px solid #dee2e6;
                    font-weight: 500;
                }
                
                /* Modal full screen on very small devices */
                .modal-dialog {
                    margin: 0 !important;
                    max-width: 100% !important;
                    height: 100vh !important;
                }
                .modal-content {
                    border-radius: 0 !important;
                    height: 100% !important;
                    display: flex !important;
                    flex-direction: column !important;
                }
                .modal-body {
                    flex: 1 !important;
                    overflow-y: auto !important;
                }
            }
        </style>
    </head>
    <body class="sb-nav-fixed">
        <!-- Header -->
        @php
            $navClasses = 'sb-topnav navbar navbar-expand navbar-dark';
            $navStyle = 'background: #212529 !important;';
        @endphp
        <nav class="{{ $navClasses }}" @if($navStyle)style="{{ $navStyle }}"@endif>
            <!-- Navbar Brand - Hidden on Mobile -->
            <a class="navbar-brand ps-3 d-none d-lg-flex align-items-center logo-white-section" href="{{ route('dashboard') }}">
                <img src="{{ asset('assets/images/waumini_link_logo.png') }}" alt="Waumini Link Logo" class="logo" style="height: 45px; max-width: 200px; object-fit: contain;">
            </a>
            <!-- Sidebar Toggle - First on Mobile -->
            <button class="btn btn-link btn-sm order-first order-lg-0 me-3 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars" style="color: #ffffff !important;"></i></button>
            <!-- Welcome Message -->
            <div class="navbar-text me-auto ms-2 ms-md-3" style="font-size: 1.1rem; font-weight: 600; color: #ffffff !important; text-shadow: 2px 2px 4px rgba(0,0,0,0.8);">
                <strong>AIC Moshi Kilimanjaro</strong>
            </div>
            <!-- Date and Time Display -->
            <div class="d-none d-md-flex align-items-center ms-auto me-0 me-md-3">
                <div class="text-end" style="color: #ffffff !important;">
                    <div id="currentDate" style="font-size: 0.9rem; font-weight: 500; color: #ffffff !important;"></div>
                    <div id="currentTime" style="font-size: 1.1rem; font-weight: 600; color: #ffffff !important;"></div>
                </div>
            </div>

            <!-- Navbar-->
            <ul class="navbar-nav ms-auto me-2 me-md-3 me-lg-4">
                <!-- Notification Icon -->
                <li class="nav-item dropdown me-2 me-md-3" id="notificationDropdown">
                    <a class="nav-link position-relative" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Notifications" style="color: #ffffff !important;">
                        <!-- Inline SVG bell to avoid external icon dependency -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="#ffffff" viewBox="0 0 16 16" class="align-text-top" style="color: #ffffff !important;">
                            <path d="M8 16a2 2 0 0 0 1.985-1.75H6.015A2 2 0 0 0 8 16m.104-14.983a1 1 0 1 0-.208 0A5.002 5.002 0 0 0 3 6c0 1.098-.5 3.06-1.638 4.723-.2.29-.295.63-.295.977 0 .713.54 1.3 1.207 1.3h11.452c.667 0 1.207-.587 1.207-1.3 0-.347-.095-.687-.295-.977C13.5 9.06 13 7.098 13 6a5.002 5.002 0 0 0-4.896-4.983"/>
                        </svg>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notificationBadge">
                            0
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end notification-dropdown" style="width: 400px; max-height: 70vh; border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,0.15); border: none;">
                        <div class="dropdown-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 16px 16px 0 0; padding: 1rem 1.5rem;">
                            <h6 class="mb-0 fw-bold"><i class="fas fa-bell me-2"></i>Notifications</h6>
                            <small class="opacity-75" id="lastUpdated">Just now</small>
                        </div>
                        
                        <div class="notification-content" style="padding: 1rem 1.5rem; max-height: calc(70vh - 80px); overflow-y: auto;">
                            <!-- Upcoming Events -->
                            <div class="notification-section mb-3">
                                <div class="section-header d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0 fw-bold text-primary">
                                        <i class="fas fa-calendar-alt me-2"></i>Special Events
                                    </h6>
                                    <span class="notification-count-badge bg-primary" id="eventsCount">0</span>
                                </div>
                                <div id="eventsList" class="notification-list">
                                    <!-- Events will be loaded here -->
                                </div>
                            </div>
                            
                            <!-- Upcoming Celebrations -->
                            <div class="notification-section mb-3">
                                <div class="section-header d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0 fw-bold text-warning">
                                        <i class="fas fa-birthday-cake me-2"></i>Celebrations
                                    </h6>
                                    <span class="notification-count-badge bg-warning" id="celebrationsCount">0</span>
                                </div>
                                <div id="celebrationsList" class="notification-list">
                                    <!-- Celebrations will be loaded here -->
                                </div>
                            </div>
                            
                            <!-- Upcoming Services -->
                            <div class="notification-section mb-3">
                                <div class="section-header d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0 fw-bold text-success">
                                        <i class="fas fa-church me-2"></i>Services
                                    </h6>
                                    <span class="notification-count-badge bg-success" id="servicesCount">0</span>
                                </div>
                                <div id="servicesList" class="notification-list">
                                    <!-- Services will be loaded here -->
                                </div>
                            </div>
                            
                            <div class="text-center py-2 pb-3">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>Click on any item to view details
                                </small>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="color: #ffffff !important;"><i class="fas fa-user fa-fw" style="color: #ffffff !important;"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="{{ route('member.settings') }}"><i class="fas fa-cog me-2"></i>Settings</a></li>
                        <li><hr class="dropdown-divider" /></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('logout') }}" 
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
        <div id="layoutSidenav">
            <!-- Sidebar -->
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                            @if(auth()->user()->isAdmin())
                            {{-- Admin Menu --}}
                            <div class="sb-sidenav-menu-heading">Administration</div>
                            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-shield-alt"></i></div>
                                Admin Dashboard
                            </a>
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseAdmin" aria-expanded="false" aria-controls="collapseAdmin">
                                <div class="sb-nav-link-icon"><i class="fas fa-cogs"></i></div>
                                System Management
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseAdmin" aria-labelledby="headingAdmin" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="{{ route('admin.logs') }}">
                                        <i class="fas fa-list-alt me-2"></i>Logs
                                    </a>
                                    <a class="nav-link" href="{{ route('admin.sessions') }}">
                                        <i class="fas fa-user-check me-2"></i>User Sessions
                                    </a>
                                    <a class="nav-link" href="{{ route('admin.users') }}">
                                        <i class="fas fa-users me-2"></i>Manage Users
                                    </a>
                                    <a class="nav-link" href="{{ route('admin.roles-permissions') }}">
                                        <i class="fas fa-shield-alt me-2"></i>Roles & Permissions
                                    </a>
                                    <a class="nav-link" href="{{ route('admin.system-monitor') }}">
                                        <i class="fas fa-server me-2"></i>System Monitor
                                    </a>
                                </nav>
                            </div>
                            @endif
                            
                            @if(!auth()->user()->isTreasurer() && !auth()->user()->isAdmin())
                            <div class="sb-sidenav-menu-heading">Main</div>
                            <a class="nav-link" href="{{ route('dashboard') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                Dashboard
                            </a>
                            @elseif(auth()->user()->isAdmin())
                            <div class="sb-sidenav-menu-heading">Main</div>
                            <a class="nav-link" href="{{ route('dashboard') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                Dashboard
                            </a>
                            @endif
                            
                            @if(!auth()->user()->isTreasurer() || auth()->user()->isAdmin())
                            @if(!auth()->user()->isMember())
                            
                            <div class="sb-sidenav-menu-heading">Management</div>
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseMembers" aria-expanded="false" aria-controls="collapseMembers">
                                <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                                Members
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseMembers" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    @if(auth()->user()->hasPermission('members.create') || auth()->user()->isAdmin())
                                    <a class="nav-link" href="{{ route('members.add') }}">
                                        <i class="fas fa-user-plus me-2"></i>Add New Member
                                    </a>
                                    @endif
                                    @if(auth()->user()->hasPermission('members.view') || auth()->user()->isAdmin())
                                    <a class="nav-link" href="{{ route('members.view') }}">
                                        <i class="fas fa-list me-2"></i>All Members
                                    </a>
                                    @endif
                                </nav>
                            </div>
                            
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLeadership" aria-expanded="false" aria-controls="collapseLeadership">
                                <div class="sb-nav-link-icon"><i class="fas fa-user-tie"></i></div>
                                Leadership
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseLeadership" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="{{ route('leaders.index') }}">
                                        <i class="fas fa-list me-2"></i>All Leaders
                                    </a>
                                    <a class="nav-link" href="{{ route('leaders.reports') }}">
                                        <i class="fas fa-chart-bar me-2"></i>Reports
                                    </a>
                                    @if(auth()->user()->canManageLeadership())
                                        <a class="nav-link" href="{{ route('leaders.create') }}">
                                            <i class="fas fa-plus me-2"></i>Assign Position
                                        </a>
                                    @endif
                                </nav>
                            </div>
                            
                            <a class="nav-link" href="{{ route('announcements.index') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-bullhorn"></i></div>
                                Announcements
                            </a>
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseEvents" aria-expanded="false" aria-controls="collapseEvents">
                                <div class="sb-nav-link-icon"><i class="fas fa-calendar-alt"></i></div>
                                Events & Services
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseEvents" aria-labelledby="headingTwo" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="{{ route('services.sunday.index') }}"><i class="fas fa-church me-2"></i>Services</a>
                                    <a class="nav-link" href="{{ route('special.events.index') }}"><i class="fas fa-calendar-plus me-2"></i>Special Events</a>
                                    <a class="nav-link" href="{{ route('attendance.index') }}"><i class="fas fa-users me-2"></i>Record Attendance</a>
                                    <a class="nav-link" href="{{ route('attendance.statistics') }}"><i class="fas fa-chart-bar me-2"></i>Attendance Statistics</a>
                                    <a class="nav-link" href="{{ route('promise-guests.index') }}"><i class="fas fa-user-check me-2"></i>Promise Guests</a>
                                    <a class="nav-link" href="{{ route('celebrations.index') }}"><i class="fas fa-birthday-cake me-2"></i>Celebrations</a>
                                    <a class="nav-link" href="{{ route('bereavement.index') }}"><i class="fas fa-heart-broken me-2"></i>Bereavement</a>
                                </nav>
                            </div>
                            @endif
                            @endif
                            
                            @if(auth()->user()->isTreasurer())
                            {{-- For Treasurer: Show finance menu items directly without dropdown --}}
                            <a class="nav-link" href="{{ route('finance.dashboard') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                Dashboard
                            </a>
                            @if(auth()->user()->canApproveFinances())
                            <a class="nav-link" href="{{ route('finance.approval.dashboard') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-check-circle"></i></div>
                                Approval Dashboard
                            </a>
                            @endif
                            <a class="nav-link" href="{{ route('finance.tithes') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-coins"></i></div>
                                Tithes
                            </a>
                            <a class="nav-link" href="{{ route('finance.offerings') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-gift"></i></div>
                                Offerings
                            </a>
                            <a class="nav-link" href="{{ route('finance.donations') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-heart"></i></div>
                                Donations
                            </a>
                            <a class="nav-link" href="{{ route('finance.pledges') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-handshake"></i></div>
                                Pledges
                            </a>
                            <a class="nav-link" href="{{ route('finance.budgets') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-wallet"></i></div>
                                Budgets
                            </a>
                            <a class="nav-link" href="{{ route('finance.expenses') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-receipt"></i></div>
                                Expenses
                            </a>
                            <a class="nav-link" href="{{ route('reports.index') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-chart-pie"></i></div>
                                Reports
                            </a>
                            <div class="sb-sidenav-menu-heading">Account</div>
                            <a class="nav-link" href="{{ route('leader.change-password') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-key"></i></div>
                                Change Password
                            </a>
                            @elseif(!auth()->user()->isMember())
                            {{-- For other users (not treasurer, not member): Show finance menu as collapsed dropdown --}}
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseFinance" aria-expanded="false" aria-controls="collapseFinance">
                                <div class="sb-nav-link-icon"><i class="fas fa-chart-line"></i></div>
                                Finance
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseFinance" aria-labelledby="headingThree" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="{{ route('finance.dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
                                    @if(auth()->user()->canApproveFinances())
                                    <a class="nav-link" href="{{ route('finance.approval.dashboard') }}"><i class="fas fa-check-circle me-2"></i>Approval Dashboard</a>
                                    @endif
                                    <a class="nav-link" href="{{ route('finance.tithes') }}"><i class="fas fa-coins me-2"></i>Tithes</a>
                                    <a class="nav-link" href="{{ route('finance.offerings') }}"><i class="fas fa-gift me-2"></i>Offerings</a>
                                    <a class="nav-link" href="{{ route('finance.donations') }}"><i class="fas fa-heart me-2"></i>Donations</a>
                                    <a class="nav-link" href="{{ route('finance.pledges') }}"><i class="fas fa-handshake me-2"></i>Pledges</a>
                                    <a class="nav-link" href="{{ route('finance.budgets') }}"><i class="fas fa-wallet me-2"></i>Budgets</a>
                                    <a class="nav-link" href="{{ route('finance.expenses') }}"><i class="fas fa-receipt me-2"></i>Expenses</a>
                                    <a class="nav-link" href="{{ route('reports.index') }}"><i class="fas fa-chart-pie me-2"></i>Reports</a>
                                </nav>
                            </div>
                            @endif
                            
                            @if((!auth()->user()->isTreasurer() || auth()->user()->isAdmin()) && !auth()->user()->isMember())
                            <div class="sb-sidenav-menu-heading">Reports</div>
                            <a class="nav-link" href="{{ route('analytics.index') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-chart-line"></i></div>
                                Analytics
                            </a>
                            <a class="nav-link" href="{{ route('reports.overview') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-file-alt"></i></div>
                                All Reports
                            </a>
                            
                            @if(!auth()->user()->isMember())
                            <div class="sb-sidenav-menu-heading">Account</div>
                            <a class="nav-link" href="{{ route('leader.change-password') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-key"></i></div>
                                Change Password
                            </a>
                            @endif
                            @endif
                            
                            @if(auth()->user()->isAdmin())
                            <div class="sb-sidenav-menu-heading">Settings</div>
                            <a class="nav-link" href="{{ route('settings.index') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-cog"></i></div>
                                System Settings
                            </a>
                            @endif
                        </div>
                    </div>
                    <div class="sb-sidenav-footer">
                        <div class="small">Logged in as:</div>
                        {{ Auth::user()->name ?? 'User' }}
                    </div>
                </nav>
            </div>
            
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4 pt-0">
                        <!-- Page Title and Quick Actions - Compact Collapsible -->
                        <div class="card border-0 shadow-sm mb-2 mt-1 actions-card">
                            <div class="card-header bg-white border-bottom p-2 px-3 d-flex align-items-center justify-content-between actions-header" onclick="toggleActions()">
                                <div class="d-flex align-items-center gap-2">
                                    <h1 class="mb-0 mt-2" style="font-size: 1.5rem;"><i class="fas fa-users me-2"></i>Members</h1>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fas fa-chevron-down text-muted d-md-none" id="actionsToggleIcon"></i>
                                </div>
                            </div>
                            <div class="card-body p-3" id="actionsBody">
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="{{ route('attendance.index') }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-users me-1"></i>
                                        <span class="d-none d-sm-inline">Record Attendance</span>
                                        <span class="d-sm-none">Attendance</span>
                                    </a>
                                    <a href="{{ route('attendance.statistics') }}" class="btn btn-outline-info btn-sm">
                                        <i class="fas fa-chart-bar me-1"></i>
                                        <span class="d-none d-sm-inline">Statistics</span>
                                        <span class="d-sm-none">Stats</span>
                                    </a>
                                    <div class="btn-group btn-group-sm" role="group" aria-label="View toggle">
                                        <button type="button" class="btn btn-outline-secondary active" id="listViewBtn" onclick="switchView('list')">
                                            <i class="fas fa-list"></i>
                                            <span class="d-none d-md-inline ms-1">List</span>
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" id="cardViewBtn" onclick="switchView('card')">
                                            <i class="fas fa-th-large"></i>
                                            <span class="d-none d-md-inline ms-1">Card</span>
                                        </button>
                                    </div>
                                    @if(auth()->user()->hasPermission('members.create') || auth()->user()->isAdmin())
                                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addChildModal">
                                        <i class="fas fa-child me-1"></i>
                                        <span class="d-none d-sm-inline">Add Child</span>
                                        <span class="d-sm-none">Child</span>
                                    </button>
                                    <a href="{{ route('members.add') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-user-plus me-1"></i>
                                        <span class="d-none d-sm-inline">Add Member</span>
                                        <span class="d-sm-none">Add</span>
                                    </a>
                                    @endif
                                    <a href="{{ route('members.export.csv', request()->query()) }}" class="btn btn-outline-success btn-sm">
                                        <i class="fas fa-file-excel me-1"></i>
                                        <span class="d-none d-sm-inline">Export</span>
                                        <span class="d-sm-none">Export</span>
                                    </a>
                                    <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                                        <i class="fas fa-print me-1"></i>
                                        <span class="d-none d-sm-inline">Print</span>
                                        <span class="d-sm-none">Print</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- List View -->
                        <div id="listView">
                            <!-- Tabs and main table section -->
                        @php
                            $permanentCount = $members->where('membership_type','permanent')->count();
                            $temporaryCount = $members->where('membership_type','temporary')->count();
                            $childrenCount = ($children ?? collect())->count();
                            $archivedCount = ($archivedMembers ?? collect())->count();
                        @endphp
                        <style>
                            .tab-badge {
                                background: linear-gradient(90deg, #5b2a86 0%, #1f2b6c 100%);
                                color: #fff;
                                font-size: 0.95em;
                                font-weight: 600;
                                border-radius: 12px;
                                padding: 2px 10px;
                                margin-left: 6px;
                                box-shadow: 0 1px 4px rgba(91,42,134,0.10);
                                vertical-align: middle;
                                letter-spacing: 0.02em;
                                transition: background 0.2s;
                            }
                            .nav-tabs .nav-link.active .tab-badge {
                                background: linear-gradient(90deg, #1f2b6c 0%, #5b2a86 100%);
                                color: #fff;
                            }
                        </style>
                        <ul class="nav nav-tabs" id="memberTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="permanent-tab" data-bs-toggle="tab" data-bs-target="#permanent" type="button" role="tab">
                                    Permanent <span class="tab-badge">{{ $permanentCount }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="temporary-tab" data-bs-toggle="tab" data-bs-target="#temporary" type="button" role="tab">
                                    Temporary <span class="tab-badge">{{ $temporaryCount }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="children-tab" data-bs-toggle="tab" data-bs-target="#children" type="button" role="tab">
                                    Children <span class="tab-badge">{{ $childrenCount }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="archived-tab" data-bs-toggle="tab" data-bs-target="#archived" type="button" role="tab">
                                    Archived <span class="tab-badge">{{ $archivedCount }}</span>
                                </button>
                            </li>
                        </ul>
                        <div class="tab-content border-bottom border-start border-end p-3" id="memberTabsContent">
                            <div class="tab-pane fade show active" id="permanent" role="tabpanel">
                                @include('members.partials.main-table', ['members' => $members->where('membership_type','permanent'), 'showArchive' => true])
                            </div>
                            <div class="tab-pane fade" id="temporary" role="tabpanel">
                                @include('members.partials.main-table', ['members' => $members->where('membership_type','temporary'), 'showArchive' => true])
                            </div>
                            <div class="tab-pane fade" id="children" role="tabpanel">
                                <div class="card">
                                    <div class="card-body p-0">
                                        @if($childrenCount > 0)
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover align-middle mb-0">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Name</th>
                                                            <th>Age</th>
                                                            <th>Gender</th>
                                                            <th>Date of Birth</th>
                                                            <th>Parent/Guardian</th>
                                                            <th>Age Group</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($children as $child)
                                                            @php
                                                                $age = (int) $child->getAge();
                                                                $ageGroup = $child->getAgeGroup();
                                                            @endphp
                                                            <tr>
                                                                <td class="text-muted">{{ $loop->iteration }}</td>
                                                                <td>
                                                                    <strong>{{ $child->full_name }}</strong>
                                                                </td>
                                                                <td>{{ $age }} years</td>
                                                                <td>
                                                                    <span class="badge bg-{{ $child->gender === 'male' ? 'primary' : 'info' }}">
                                                                        {{ ucfirst($child->gender) }}
                                                                    </span>
                                                                </td>
                                                                <td>{{ $child->date_of_birth ? $child->date_of_birth->format('M d, Y') : '—' }}</td>
                                                                <td>
                                                                    @if($child->member)
                                                                        <a href="javascript:void(0);" 
                                                                           onclick="viewDetails({{ $child->member->id }})" 
                                                                           class="text-decoration-none text-primary" 
                                                                           style="cursor: pointer;">
                                                                            <i class="fas fa-user me-1"></i>{{ $child->member->full_name }}
                                                                            <span class="badge bg-success ms-1" style="font-size: 0.7em;">Member</span>
                                                                        </a>
                                                                    @elseif($child->parent_name)
                                                                        <div>
                                                                            <i class="fas fa-user-friends me-1 text-warning"></i>
                                                                            <strong>{{ $child->parent_name }}</strong>
                                                                            <span class="badge bg-warning text-dark ms-1" style="font-size: 0.7em;">Non-Member</span>
                                                                        </div>
                                                                        @if($child->parent_phone)
                                                                            <small class="text-muted">
                                                                                <i class="fas fa-phone me-1"></i>{{ $child->parent_phone }}
                                                                            </small>
                                                                        @endif
                                                                        @if($child->parent_relationship)
                                                                            <br><small class="text-muted">
                                                                                <i class="fas fa-link me-1"></i>{{ $child->parent_relationship }}
                                                                            </small>
                                                                        @endif
                                                                    @else
                                                                        <span class="text-muted">—</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if($ageGroup === 'infant')
                                                                        <span class="badge bg-secondary">Infant (&lt;3)</span>
                                                                    @elseif($ageGroup === 'sunday_school')
                                                                        <span class="badge bg-success">Sunday School (3-12)</span>
                                                                    @elseif($ageGroup === 'teenager')
                                                                        <span class="badge bg-warning text-dark">Teenager (13-17)</span>
                                                                    @else
                                                                        <span class="badge bg-dark">Adult (18+)</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="text-center py-5">
                                                <i class="fas fa-child fa-3x text-muted mb-3"></i>
                                                <p class="text-muted">No children registered yet.</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="archived" role="tabpanel">
                                @include('members.partials.main-table', ['members' => $archivedMembers ?? collect(), 'showArchive' => false, 'isArchived' => true])
                            </div>
                        </div>
                        </div>

                        <!-- Card View -->
                        <div id="cardView" style="display: none;">
                            @include('members.partials.card-view', ['members' => $members, 'archivedMembers' => $archivedMembers ?? collect()])
                        </div>
                </main>

                <!-- Details Modal -->
                <div class="modal fade" id="memberDetailsModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-scrollable">
                        <div class="modal-content border-0 shadow-lg" style="border-radius: 18px; overflow: hidden;">
                            <div class="modal-header text-white" style="background: linear-gradient(135deg, #1f2b6c 0%, #5b2a86 100%); border: none;">
                                <h5 class="modal-title d-flex align-items-center gap-2"><i class="fas fa-id-card" aria-label="Member details"></i><span>Member Details</span></h5>
                                <div class="ms-auto d-flex gap-2 align-items-center">
                                    <button class="btn btn-sm btn-outline-light" id="btnCopyAllDetails" title="Copy all details" aria-label="Copy all details"><i class="fas fa-copy"></i></button>
                                    <div class="vr opacity-50 mx-1"></div>
                                    <button class="btn btn-sm btn-light" id="btnDownloadExcel" title="Download Excel"><i class="fas fa-file-excel text-success"></i></button>
                                    <button class="btn btn-sm btn-light" id="btnDownloadPDF" title="Download PDF"><i class="fas fa-file-pdf text-danger"></i></button>
                                    <button class="btn btn-sm btn-light" id="btnPrintDetails" title="Print"><i class="fas fa-print text-secondary"></i></button>
                                </div>
                                <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body bg-light" id="memberDetailsBody">
                                <div class="text-center text-muted py-4">Loading...</div>
                            </div>
                            <div class="modal-footer d-flex justify-content-between align-items-center">
                                <div class="small">
                                    <span class="me-1">Powered by</span>
                                    <a href="https://emca.tech/#" target="_blank" rel="noopener" class="emca-link fw-semibold" style="color: #940000 !important;">EmCa Technologies</a>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-outline-light" id="btnAttendanceHistory" onclick="viewAttendanceHistory()" style="display: none;">
                                        <i class="fas fa-calendar-check me-1"></i>Attendance History
                                    </button>
                                    <button type="button" class="btn btn-outline-light" id="btnIdCard" onclick="viewIdCard()" style="display: none;">
                                        <i class="fas fa-id-card me-1"></i>ID Card
                                    </button>
                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Section Chooser Modal -->
                <div class="modal fade" id="editSectionChooserModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-sm">
                        <div class="modal-content border-0 shadow" style="border-radius: 14px; overflow: hidden;">
                            <div class="modal-header text-white" style="background: linear-gradient(135deg,#5b2a86 0%, #0ea5ea 100%); border: none;">
                                <h6 class="modal-title"><i class="fas fa-edit me-2"></i>Select Section to Edit</h6>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="d-grid gap-2">
                                    <button class="btn btn-outline-primary" id="btnEditPersonal"><i class="fas fa-user me-2"></i>Personal</button>
                                    <button class="btn btn-outline-primary" id="btnEditLocation"><i class="fas fa-map-marker-alt me-2"></i>Location</button>
                                    <button class="btn btn-outline-primary" id="btnEditFamily"><i class="fas fa-home me-2"></i>Family</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Personal Modal -->
                <div class="modal fade" id="memberEditPersonalModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-md">
                        <div class="modal-content border-0 shadow-lg" style="border-radius: 18px; overflow: hidden;">
                            <div class="modal-header bg-white border-0">
                                <h6 class="modal-title d-flex align-items-center gap-2"><i class="fas fa-user text-primary"></i><span>Edit Personal</span></h6>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="editPersonalForm">
                                    <input type="hidden" id="edit_personal_id">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label">Full Name</label>
                                            <input type="text" class="form-control" id="edit_personal_full_name" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Email</label>
                                            <input type="email" class="form-control" id="edit_personal_email">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Phone</label>
                                            <input type="text" class="form-control" id="edit_personal_phone_number">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Membership Type</label>
                                            <select id="edit_personal_membership_type" class="form-select">
                                                <option value="permanent">Permanent</option>
                                                <option value="temporary">Temporary</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Gender</label>
                                            <select id="edit_personal_gender" class="form-select">
                                                <option value="">Select</option>
                                                <option value="male">Male</option>
                                                <option value="female">Female</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Date of Birth</label>
                                            <input type="date" class="form-control" id="edit_personal_date_of_birth">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">NIDA Number</label>
                                            <input type="text" class="form-control" id="edit_personal_nida_number">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Tribe</label>
                                            <select id="edit_personal_tribe" class="form-select"></select>
                                        </div>
                                        <div class="col-md-6" id="edit_personal_other_tribe_group" style="display:none;">
                                            <label class="form-label">Other Tribe</label>
                                            <input type="text" class="form-control" id="edit_personal_other_tribe" placeholder="Specify tribe">
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end gap-2 mt-3">
                                        <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Location Modal -->
                <div class="modal fade" id="memberEditLocationModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-md">
                        <div class="modal-content border-0 shadow-lg" style="border-radius: 18px; overflow: hidden;">
                            <div class="modal-header bg-white border-0">
                                <h6 class="modal-title d-flex align-items-center gap-2"><i class="fas fa-map-marker-alt text-primary"></i><span>Edit Location</span></h6>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="editLocationForm">
                                    <input type="hidden" id="edit_location_id">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label">Region</label>
                                            <select id="edit_location_region" class="form-select"></select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">District</label>
                                            <select id="edit_location_district" class="form-select"></select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Ward</label>
                                            <select id="edit_location_ward" class="form-select"></select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Street</label>
                                            <input type="text" class="form-control" id="edit_location_street">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Address</label>
                                            <input type="text" class="form-control" id="edit_location_address">
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end gap-2 mt-3">
                                        <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Family Modal -->
                <div class="modal fade" id="memberEditFamilyModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-scrollable">
                        <div class="modal-content border-0 shadow-lg" style="border-radius: 18px; overflow: hidden;">
                            <div class="modal-header bg-white border-0">
                                <h6 class="modal-title d-flex align-items-center gap-2"><i class="fas fa-home text-primary"></i><span>Edit Family Information</span></h6>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="editFamilyForm">
                                    <input type="hidden" id="edit_family_id">
                                    <input type="hidden" id="edit_family_member_type">
                                    <input type="hidden" id="edit_family_membership_type">
                                    
                                    <!-- Marital Status Section (for permanent father/mother) -->
                                    <div id="edit_family_marital_section" style="display:none;">
                                        <h6 class="mb-3 text-primary fw-bold"><i class="fas fa-heart me-2"></i>Marital Status</h6>
                                        <div class="row g-3 mb-4">
                                            <div class="col-12">
                                                <label class="form-label">Marital Status</label>
                                                <select id="edit_family_marital_status" class="form-select">
                                                    <option value="">Select</option>
                                                    <option value="married">Married</option>
                                                    <option value="divorced">Divorced</option>
                                                    <option value="widowed">Widowed</option>
                                                    <option value="separated">Separated</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <!-- Spouse Information (shown when married) -->
                                        <div id="edit_family_spouse_section" style="display:none;">
                                            <h6 class="mb-3 text-primary fw-bold"><i class="fas fa-user me-2"></i>Spouse Information</h6>
                                            <div class="row g-3 mb-4">
                                                <div class="col-md-6">
                                                    <label class="form-label">Spouse Full Name</label>
                                                    <input type="text" class="form-control" id="edit_family_spouse_full_name">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Spouse Date of Birth</label>
                                                    <input type="date" class="form-control" id="edit_family_spouse_date_of_birth">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Spouse Education Level</label>
                                                    <select id="edit_family_spouse_education_level" class="form-select">
                                                        <option value="">Select</option>
                                                        <option value="primary">Primary</option>
                                                        <option value="secondary">Secondary</option>
                                                        <option value="high_level">High Level</option>
                                                        <option value="certificate">Certificate</option>
                                                        <option value="diploma">Diploma</option>
                                                        <option value="bachelor_degree">Bachelor Degree</option>
                                                        <option value="masters">Masters</option>
                                                        <option value="phd">PhD</option>
                                                        <option value="professor">Professor</option>
                                                        <option value="not_studied">Not Studied</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Spouse Profession</label>
                                                    <input type="text" class="form-control" id="edit_family_spouse_profession">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Spouse NIDA Number</label>
                                                    <input type="text" class="form-control" id="edit_family_spouse_nida_number">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Spouse Email</label>
                                                    <input type="email" class="form-control" id="edit_family_spouse_email">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Spouse Phone Number</label>
                                                    <input type="text" class="form-control" id="edit_family_spouse_phone_number" placeholder="+255744000000">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Spouse Tribe</label>
                                                    <select id="edit_family_spouse_tribe" class="form-select"></select>
                                                </div>
                                                <div class="col-md-6" id="edit_family_spouse_other_tribe_group" style="display:none;">
                                                    <label class="form-label">Spouse Other Tribe</label>
                                                    <input type="text" class="form-control" id="edit_family_spouse_other_tribe" placeholder="Specify tribe">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Is Spouse a Church Member?</label>
                                                    <select id="edit_family_spouse_church_member" class="form-select">
                                                        <option value="">Select</option>
                                                        <option value="yes">Yes</option>
                                                        <option value="no">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Guardian Section (for temporary members and independent permanent) -->
                                    <div id="edit_family_guardian_section" style="display:none;">
                                        <h6 class="mb-3 text-primary fw-bold"><i class="fas fa-user-shield me-2"></i>Guardian Information</h6>
                                        <div class="row g-3 mb-4">
                                            <div class="col-md-4">
                                                <label class="form-label">Guardian Name</label>
                                                <input type="text" class="form-control" id="edit_family_guardian_name">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Guardian Phone</label>
                                                <input type="text" class="form-control" id="edit_family_guardian_phone" placeholder="+255744000000">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Guardian Relationship</label>
                                                <select id="edit_family_guardian_relationship" class="form-select">
                                                    <option value="">Select</option>
                                                    <option value="parent">Parent</option>
                                                    <option value="guardian">Guardian</option>
                                                    <option value="relative">Relative</option>
                                                    <option value="other">Other</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-end gap-2 mt-3">
                                        <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Compact Add Member Modal (icon-triggered in table header) -->
                <div class="modal fade" id="addMemberModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-md">
                        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                            <div class="modal-header text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                                <h5 class="modal-title d-flex align-items-center gap-2"><i class="fas fa-user-plus"></i><span>Register New Member</span></h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body bg-light">
                                <form id="quickAddMemberForm">
                                    <div class="row g-3">
                                        <div class="col-md-8">
                                            <label class="form-label">Full Name</label>
                                            <input type="text" class="form-control" id="add_full_name" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Gender</label>
                                            <select class="form-select" id="add_gender">
                                                <option value="">Select</option>
                                                <option value="male">Male</option>
                                                <option value="female">Female</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Phone</label>
                                            <input type="text" class="form-control" id="add_phone_number">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Email</label>
                                            <input type="email" class="form-control" id="add_email">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Region</label>
                                            <select id="add_region" class="form-select"></select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">District</label>
                                            <select id="add_district" class="form-select"></select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Ward</label>
                                            <select id="add_ward" class="form-select"></select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Tribe</label>
                                            <select id="add_tribe" class="form-select"></select>
                                        </div>
                                        <div class="col-md-6" id="add_other_tribe_group" style="display:none;">
                                            <label class="form-label">Other Tribe</label>
                                            <input type="text" class="form-control" id="add_other_tribe">
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end gap-2 mt-3">
                                        <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Register</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <footer class="bg-dark text-light py-4 mt-auto">
  <div class="container px-4">
    <div class="row align-items-center">
      <!-- Left Side -->
      <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
        <small>&copy; <span id="year"></span> Waumini Link — Version 1.0</small>
      </div>

      <!-- Right Side -->
      <div class="col-md-6 text-center text-md-end">
        <small>
          Powered by 
          <a href="https://emca.tech/#" class="text-decoration-none fw-semibold" style="color: #940000 !important;">
            EmCa Technologies
          </a>
        </small>
      </div>
    </div>
  </div>
                </footer>
                
                <!-- Add Child Modal -->
                <div class="modal fade" id="addChildModal" tabindex="-1" aria-labelledby="addChildModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title" id="addChildModalLabel"><i class="fas fa-child me-2"></i>Add Child</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="addChildForm">
                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <label class="form-label">Child's Full Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="child_full_name" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Gender <span class="text-danger">*</span></label>
                                            <select class="form-select" id="child_gender" required>
                                                <option value="">Select</option>
                                                <option value="male">Male</option>
                                                <option value="female">Female</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" id="child_date_of_birth" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Age</label>
                                            <input type="text" class="form-control" id="child_age" readonly>
                                        </div>
                                        
                                        <div class="col-12">
                                            <hr>
                                            <h6 class="mb-3"><i class="fas fa-user-friends me-2"></i>Parent/Guardian Information</h6>
                                        </div>
                                        
                                        <div class="col-md-12">
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="radio" name="parent_type" id="parent_member" value="member" checked>
                                                <label class="form-check-label" for="parent_member">
                                                    Parent is a Church Member
                                                </label>
                                            </div>
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="radio" name="parent_type" id="parent_non_member" value="non_member">
                                                <label class="form-check-label" for="parent_non_member">
                                                    Parent is NOT a Church Member
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <!-- Member Parent Fields -->
                                        <div id="memberParentFields">
                                            <div class="col-md-12">
                                                <label class="form-label">Select Parent Member <span class="text-danger">*</span></label>
                                                <select class="form-select" id="child_member_id">
                                                    <option value="">Select Member</option>
                                                    @foreach($members->flatten() as $member)
                                                        <option value="{{ $member->id }}">{{ $member->full_name }} ({{ $member->member_id }})</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <!-- Non-Member Parent Fields -->
                                        <div id="nonMemberParentFields" style="display: none;">
                                            <div class="col-md-6">
                                                <label class="form-label">Parent/Guardian Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="child_parent_name">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Parent/Guardian Phone</label>
                                                <input type="text" class="form-control" id="child_parent_phone">
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label">Relationship to Child</label>
                                                <select class="form-select" id="child_parent_relationship">
                                                    <option value="">Select Relationship</option>
                                                    <option value="Father">Father</option>
                                                    <option value="Mother">Mother</option>
                                                    <option value="Guardian">Guardian</option>
                                                    <option value="Grandfather">Grandfather</option>
                                                    <option value="Grandmother">Grandmother</option>
                                                    <option value="Uncle">Uncle</option>
                                                    <option value="Aunt">Aunt</option>
                                                    <option value="Other">Other</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-success" onclick="saveChild()">
                                    <i class="fas fa-save me-2"></i>Save Child
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Archive Modal (should be included once per page, not per row) -->
                <div class="modal fade" id="archiveMemberModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><i class="fas fa-archive me-2"></i>Archive Member</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="archiveMemberForm">
                                    <input type="hidden" id="archive_member_id">
                                    <div class="mb-3">
                                        <label for="archive_reason" class="form-label">Reason for archiving</label>
                                        <textarea class="form-control" id="archive_reason" name="reason" rows="3" required></textarea>
                                    </div>
                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-warning">Archive</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}" crossorigin="anonymous"></script>
        <script src="{{ asset('js/scripts.js') }}"></script>
        <script>
        // Archive member logic (robust, attaches only once)
        let archiveMemberId = null;
        function openArchiveModal(id) {
            document.getElementById('archive_member_id').value = id;
            document.getElementById('archive_reason').value = '';
            var modalEl = document.getElementById('archiveMemberModal');
            var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        }
        
        // Attach submit handler only once
        const form = document.getElementById('archiveMemberForm');
        if (form && !form._archiveHandlerAttached) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const id = document.getElementById('archive_member_id').value;
                const reason = document.getElementById('archive_reason').value.trim();
                if (!reason) {
                    Swal.fire({ icon: 'warning', title: 'Please provide a reason.' });
                    return;
                }
                const formData = new FormData();
                formData.append('reason', reason);
                formData.append('_method', 'DELETE');
                fetch(`{{ url('/members') }}/${id}/archive`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                })
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        Swal.fire({ icon: 'success', title: 'Member archived', timer: 1200, showConfirmButton: false }).then(()=>location.reload());
                    } else {
                        Swal.fire({ icon: 'error', title: 'Archive failed', text: res.message || 'Please try again.' });
                    }
                })
                .catch(()=> Swal.fire({ icon: 'error', title: 'Network error' }));
            });
            form._archiveHandlerAttached = true;
        }
        // Activate correct tab based on ?tab=permanent|temporary|archived
        (function() {
            function getQueryParam(name) {
                const url = new URL(window.location.href);
                return url.searchParams.get(name);
            }
            const tab = getQueryParam('tab');
            if(tab && ['permanent','temporary','children','archived'].includes(tab)) {
                const trigger = document.getElementById(tab+'-tab');
                if(trigger) {
                    // Bootstrap 5 tab activation
                    if(window.bootstrap && bootstrap.Tab) {
                        const tabObj = new bootstrap.Tab(trigger);
                        tabObj.show();
                    } else {
                        // fallback: click
                        trigger.click();
                    }
                }
            }
        })();
        </script>
        <script>
            // Globals to share state between details/print
            let currentDetailsMember = null;
            function formatDateDisplay(value){
                if(!value) return '-';
                try{
                    const d = new Date(value);
                    if (!isNaN(d.getTime())) {
                        const dd = String(d.getDate()).padStart(2,'0');
                        const mm = String(d.getMonth()+1).padStart(2,'0');
                        const yyyy = d.getFullYear();
                        return `${dd}/${mm}/${yyyy}`;
                    }
                }catch(e){}
                const datePart = String(value).split('T')[0];
                if (datePart && datePart.includes('-')){
                    const [y,m,d] = datePart.split('-');
                    if (y && m && d) return `${d}/${m}/${y}`;
                }
                return datePart || '-';
            }
            function confirmThen(message, onConfirm){
                Swal.fire({ title: message, icon: 'question', showCancelButton: true, confirmButtonText: 'Yes', cancelButtonText: 'No', confirmButtonColor: '#5b2a86', cancelButtonColor: '#6c757d' }).then(r=>{ if(r.isConfirmed) { try{ onConfirm && onConfirm(); }catch(e){ console.error(e); } }});
            }

            function handleAction(fn){ confirmThen('Proceed with this action?', fn); return false; }

            // View switching functionality
            function toggleActions() {
                // Only toggle on mobile devices
                if (window.innerWidth > 768) {
                    return; // Don't toggle on desktop
                }
                
                const actionsBody = document.getElementById('actionsBody');
                const actionsIcon = document.getElementById('actionsToggleIcon');
                
                // Check computed style to see if it's visible
                const computedStyle = window.getComputedStyle(actionsBody);
                const isVisible = computedStyle.display !== 'none';
                
                if (isVisible) {
                    actionsBody.style.display = 'none';
                    actionsIcon.classList.remove('fa-chevron-up');
                    actionsIcon.classList.add('fa-chevron-down');
                } else {
                    actionsBody.style.display = 'block';
                    actionsIcon.classList.remove('fa-chevron-down');
                    actionsIcon.classList.add('fa-chevron-up');
                }
            }
            
            // Handle window resize
            window.addEventListener('resize', function() {
                const actionsBody = document.getElementById('actionsBody');
                const actionsIcon = document.getElementById('actionsToggleIcon');
                
                if (window.innerWidth > 768) {
                    // Always show on desktop
                    actionsBody.style.display = 'block';
                    actionsIcon.classList.remove('fa-chevron-up');
                    actionsIcon.classList.add('fa-chevron-down');
                } else {
                    // On mobile, ensure it starts collapsed
                    const computedStyle = window.getComputedStyle(actionsBody);
                    if (computedStyle.display !== 'none' && !actionsBody.hasAttribute('data-user-opened')) {
                        actionsBody.style.display = 'none';
                        actionsIcon.classList.remove('fa-chevron-up');
                        actionsIcon.classList.add('fa-chevron-down');
                    }
                }
            });
            
            // Initialize on page load
            document.addEventListener('DOMContentLoaded', function() {
                const actionsBody = document.getElementById('actionsBody');
                const actionsIcon = document.getElementById('actionsToggleIcon');
                
                if (window.innerWidth <= 768) {
                    // Mobile: start collapsed
                    actionsBody.style.display = 'none';
                    actionsIcon.classList.remove('fa-chevron-up');
                    actionsIcon.classList.add('fa-chevron-down');
                } else {
                    // Desktop: always show
                    actionsBody.style.display = 'block';
                    actionsIcon.style.display = 'none';
                }
            });
            
            function switchView(view) {
                console.log('Switching to view:', view);
                const listView = document.getElementById('listView');
                const cardView = document.getElementById('cardView');
                const listBtn = document.getElementById('listViewBtn');
                const cardBtn = document.getElementById('cardViewBtn');
                
                console.log('Elements found:', { listView, cardView, listBtn, cardBtn });
                
                if (view === 'list') {
                    listView.style.display = 'block';
                    cardView.style.display = 'none';
                    listBtn.classList.add('active');
                    cardBtn.classList.remove('active');
                    localStorage.setItem('memberViewPreference', 'list');
                    console.log('Switched to list view');
                } else {
                    listView.style.display = 'none';
                    cardView.style.display = 'block';
                    listBtn.classList.remove('active');
                    cardBtn.classList.add('active');
                    localStorage.setItem('memberViewPreference', 'card');
                    console.log('Switched to card view');
                }
            }

            // Load saved view preference
            document.addEventListener('DOMContentLoaded', function() {
                console.log('DOM Content Loaded');
                const savedView = localStorage.getItem('memberViewPreference');
                console.log('Saved view preference:', savedView);
                if (savedView === 'card') {
                    switchView('card');
                }
                
                // Test if buttons are working
                const listBtn = document.getElementById('listViewBtn');
                const cardBtn = document.getElementById('cardViewBtn');
                console.log('View buttons found:', { listBtn, cardBtn });
                
                if (listBtn) {
                    listBtn.addEventListener('click', function() {
                        console.log('List view button clicked');
                        switchView('list');
                    });
                }
                
                if (cardBtn) {
                    cardBtn.addEventListener('click', function() {
                        console.log('Card view button clicked');
                        switchView('card');
                    });
                }
            });

            function viewDetails(id) {
                console.log('viewDetails called with ID:', id);
                fetch(`{{ url('/members') }}/${id}`, { headers: { 'Accept': 'application/json' } })
                    .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
                    .then(async m => {
                        console.log('Member details loaded:', m);
                        currentDetailsMember = m;
                        
                        // Check if we have spouse details from the API response
                        if (m.spouse_details) {
                            console.log('Spouse details loaded from API:', m.spouse_details);
                            m.mainMemberSpouseInfo = m.spouse_details;
                        } else if (m.main_member_details) {
                            console.log('Main member details loaded from API:', m.main_member_details);
                            m.mainMemberSpouseInfo = m.main_member_details;
                        }
                        // Determine if archived (by checking if member_snapshot exists)
                        let isArchived = false;
                        let snap = null;
                        let archiveReason = null;
                        if (m.member_snapshot) {
                            isArchived = true;
                            snap = m.member_snapshot;
                            archiveReason = m.reason || null;
                        }
                        const data = isArchived ? snap : m;
                        // Helper functions
                        const actionCell = (content, actionsHtml = '') => `<div class="d-flex align-items-center justify-content-between">${content}<span class="ms-2 d-inline-flex gap-2">${actionsHtml}</span></div>`;
                        const badge = (text, tone = 'secondary') => `<span class="badge bg-${tone}">${text}</span>`;
                        const copyBtn = (text, title, icon) => `<button type="button" class="btn btn-sm btn-outline-secondary" onclick="navigator.clipboard.writeText('${(text||'').toString().replace(/'/g, "&#39;") }').then(()=>Swal.fire({ icon:'success', title:'Copied', timer:900, showConfirmButton:false })).catch(()=>Swal.fire({ icon:'error', title:'Copy failed' }))" title="${title}" aria-label="${title}"><i class="${icon}"></i></button>`;
                        const mailto = (email) => {
                            if (!email) return '';
                            const raw = String(email).trim();
                            const escaped = raw.replace(/[&"<>]/g, c => ({'&':'&amp;','"':'&quot;','<':'&lt;','>':'&gt;'}[c]));
                            return `<a href="mailto:${escaped}" onclick="window.location.href=this.href; return false;" class="btn btn-sm btn-outline-primary" title="Send email" aria-label="Send email"><i class="fas fa-paper-plane"></i></a>`;
                        };
                        const telto = (phone) => {
                            if (!phone) return '';
                            const sanitized = String(phone).replace(/[^+\d]/g, '');
                            return `<a href="tel:${sanitized}" onclick="window.location.href=this.href; return false;" class="btn btn-sm btn-outline-primary" title="Call" aria-label="Call"><i class="fas fa-phone"></i></a>`;
                        };
                        const mapsBtn = (q) => q ? `<a href="#" onclick="return handleAction(()=>window.open('https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(q)}','_blank'))" class="btn btn-sm btn-outline-success" title="Open in Maps" aria-label="Open in Maps"><i class="fas fa-map-marked-alt"></i></a>` : '';
                        const row = (icon, label, value, actions = '') => `
                            <tr>
                                <td class="text-muted text-nowrap"><i class="${icon} me-2" aria-hidden="true"></i>${label}</td>
                                <td class="fw-semibold">${actionCell(value || '—', actions)}</td>
                            </tr>`;
                        // Compose QR payload with all fields
                        const lines = [
                            `Full Name: ${data.full_name || '-'}`,
                            `Member ID: ${data.member_id || '-'}`,
                            `Membership Type: ${data.membership_type || '-'}`,
                            `Member Type: ${data.member_type || '-'}`,
                            `Phone: ${data.phone_number || '-'}`,
                            `Email: ${data.email || '-'}`,
                            `Gender: ${data.gender ? data.gender.charAt(0).toUpperCase()+data.gender.slice(1) : '-'}`,
                            `Date of Birth: ${formatDateDisplay(data.date_of_birth)}`,
                            `Education Level: ${data.education_level || '-'}`,
                            `Profession: ${data.profession || '-'}`,
                            `NIDA Number: ${data.nida_number || '-'}`,
                            `Region: ${data.region || '-'}`,
                            `District: ${data.district || '-'}`,
                            `Ward: ${data.ward || '-'}`,
                            `Street: ${data.street || '-'}`,
                            `Address: ${data.address || '-'}`,
                            `Living with family: ${data.living_with_family || '-'}`,
                            `Family relationship: ${data.family_relationship || '-'}`,
                            `Tribe: ${(data.tribe || '-') + (data.other_tribe ? ` (${data.other_tribe})` : '')}`,
                        ];
                        if ((data.membership_type === 'temporary' || (data.membership_type === 'permanent' && data.member_type === 'independent')) && (data.guardian_name || data.guardian_phone || data.guardian_relationship)) {
                            lines.push(`Guardian Name: ${data.guardian_name || '-'}`);
                            lines.push(`Guardian Phone: ${data.guardian_phone || '-'}`);
                            lines.push(`Guardian Relationship: ${data.guardian_relationship || '-'}`);
                        }
                        if (archiveReason) {
                            lines.push(`Archive Reason: ${archiveReason}`);
                        }
                        const qrPayload = lines.join('\n');
                        // Build HTML
                        let html = `<div id=\"memberDetailsPrint\" class=\"p-2\">
                            <div class=\"d-flex justify-content-center\">
                                <div class=\"text-center mb-3\">
                                    <img id=\"inlineQrImg\" alt=\"Member details QR\" width=\"120\" height=\"120\"/>
                                    <div class=\"text-muted small mt-1\">Scan for details</div>
                                </div>
                            </div>
                            <div class=\"small text-uppercase text-muted mt-2 mb-1\">Personal</div>
                            <table class=\"table table-bordered table-striped align-middle interactive-table\"><tbody>
                                ${row('fas fa-user', 'Full Name', data.full_name)}
                                ${row('fas fa-id-badge', 'Member ID', data.member_id, copyBtn(data.member_id, 'Copy ID', 'fas fa-copy'))}
                                ${row('fas fa-id-card', 'Membership Type', data.membership_type)}
                                ${row('fas fa-user-tag', 'Member Type', data.member_type)}
                                ${row('fas fa-phone', 'Phone', data.phone_number, telto(data.phone_number) + copyBtn(data.phone_number, 'Copy phone', 'fas fa-copy'))}
                                ${row('fas fa-envelope', 'Email', data.email, mailto(data.email) + copyBtn(data.email, 'Copy email', 'fas fa-copy'))}
                                ${row('fas fa-venus-mars', 'Gender', data.gender ? badge(data.gender.charAt(0).toUpperCase()+data.gender.slice(1), (data.gender||'').toLowerCase()==='male' ? 'primary' : 'danger') : '—')}
                                ${row('fas fa-birthday-cake', 'Date of Birth', formatDateDisplay(data.date_of_birth))}
                                ${row('fas fa-graduation-cap', 'Education Level', data.education_level)}
                                ${row('fas fa-briefcase', 'Profession', data.profession)}
                                ${row('fas fa-id-card', 'NIDA Number', data.nida_number)}
                            </tbody></table>
                            <div class=\"small text-uppercase text-muted mt-3 mb-1\">Location</div>
                            <table class=\"table table-bordered table-striped align-middle interactive-table\"><tbody>
                                ${row('fas fa-map', 'Region', data.region ? badge(data.region, 'secondary') : '—', mapsBtn([data.region,'Tanzania'].filter(Boolean).join(', ')))}
                                ${row('fas fa-city', 'District', data.district ? badge(data.district, 'secondary') : '—', mapsBtn([data.district,data.region,'Tanzania'].filter(Boolean).join(', ')))}
                                ${row('fas fa-location-arrow', 'Ward', data.ward ? badge(data.ward, 'secondary') : '—', mapsBtn([data.ward,data.district,data.region,'Tanzania'].filter(Boolean).join(', ')))}
                                ${row('fas fa-road', 'Street', data.street || '—', mapsBtn([data.street,data.ward,data.district,data.region,'Tanzania'].filter(Boolean).join(', ')))}
                                ${row('fas fa-address-card', 'Address', data.address || '—', mapsBtn([data.address,data.street,data.ward,data.district,data.region,'Tanzania'].filter(Boolean).join(', ')))}
                            </tbody></table>
                            <div class=\"small text-uppercase text-muted mt-3 mb-1\">Family</div>
                            <table class=\"table table-bordered table-striped align-middle interactive-table\"><tbody>
                                ${(() => { 
                                    // Check if spouse information is present
                                    const hasSpouseDetails = data.spouse_details || data.main_member_details || data.spouse_full_name || data.spouse_phone_number || data.spouse_email;
                                    const hasChildren = Array.isArray(data.children) && data.children.length > 0;
                                    const inferred = (hasSpouseDetails || hasChildren) ? 'yes' : 'no';
                                    const v = (data.living_with_family && typeof data.living_with_family === 'string') ? data.living_with_family.toLowerCase() : '';
                                    const value = v === 'yes' || v === 'no' ? v : inferred;
                                    const pretty = value === 'yes' ? 'Yes' : (value === 'no' ? 'No' : '—');
                                    return row('fas fa-users', 'Living with family', pretty);
                                })()}
                                ${row('fas fa-user-friends', 'Family relationship', data.family_relationship)}
                                ${row('fas fa-flag', 'Tribe', (data.tribe || '') + (data.other_tribe ? ` (${data.other_tribe})` : ''))}
                            </tbody></table>
                            ${(() => {
                                // Check if we have spouse information to display
                                const hasSpouseDetails = data.spouse_details || data.main_member_details || data.spouse_full_name || data.spouse_email || data.spouse_phone_number;
                                
                                if (hasSpouseDetails) {
                                    let spouseData, spouseTitle, spouseTribe, spouseId;
                                    
                                    if (data.spouse_details) {
                                        // This member has a spouse member record
                                        spouseData = data.spouse_details;
                                        spouseTitle = (data.gender === 'male' ? 'Wife' : 'Husband');
                                        spouseTribe = (spouseData.tribe || '') + (spouseData.tribe === 'Other' && spouseData.other_tribe ? ` (${spouseData.other_tribe})` : '');
                                        spouseId = spouseData.id;
                                    } else if (data.main_member_details) {
                                        // This is a spouse member - show main member info
                                        spouseData = data.main_member_details;
                                        spouseTitle = (data.gender === 'male' ? 'Husband' : 'Wife');
                                        spouseTribe = (spouseData.tribe || '') + (spouseData.tribe === 'Other' && spouseData.other_tribe ? ` (${spouseData.other_tribe})` : '');
                                        spouseId = spouseData.id;
                                    } else {
                                        // Fallback to old spouse fields
                                        spouseData = data;
                                        spouseTitle = (data.member_type === 'father' ? 'Wife' : (data.member_type === 'mother' ? 'Husband' : 'Spouse'));
                                        spouseTribe = (data.spouse_tribe || '') + (data.spouse_tribe === 'Other' && data.spouse_other_tribe ? ` (${data.spouse_other_tribe})` : '');
                                        spouseId = data.spouse_member_id;
                                    }
                                    
                                    return `
                                    <div class=\"small text-uppercase text-muted mt-3 mb-1\">${spouseTitle}</div>
                                    <table class=\"table table-bordered table-striped align-middle interactive-table\"><tbody>
                                        ${row('fas fa-heart', 'Marital Status', (data.marital_status ? data.marital_status.charAt(0).toUpperCase() + data.marital_status.slice(1) : '—'))}
                                        ${row('fas fa-user', spouseTitle+' Name', spouseData.full_name || data.spouse_full_name)}
                                        ${row('fas fa-church', spouseTitle+' Church Member', data.spouse_church_member ? (data.spouse_church_member === 'yes' ? 'Yes' : 'No') : '—')}
                                        ${row('fas fa-id-badge', spouseTitle+' Member Status', spouseId ? `<a href="/members/view?id=${spouseId}" class="text-primary">View as Member</a>` : 'Not a church member')}
                                        ${row('fas fa-birthday-cake', spouseTitle+' DOB', formatDateDisplay(spouseData.date_of_birth || data.spouse_date_of_birth))}
                                        ${row('fas fa-graduation-cap', spouseTitle+' Education', spouseData.education_level || data.spouse_education_level)}
                                        ${row('fas fa-briefcase', spouseTitle+' Profession', spouseData.profession || data.spouse_profession)}
                                        ${row('fas fa-id-card', spouseTitle+' NIDA', spouseData.nida_number || data.spouse_nida_number)}
                                        ${row('fas fa-envelope', spouseTitle+' Email', spouseData.email || data.spouse_email, (spouseData.email || data.spouse_email) ? (mailto(spouseData.email || data.spouse_email) + copyBtn(spouseData.email || data.spouse_email, 'Copy email', 'fas fa-copy')) : '')}
                                        ${row('fas fa-phone', spouseTitle+' Phone', spouseData.phone_number || data.spouse_phone_number, (spouseData.phone_number || data.spouse_phone_number) ? (telto(spouseData.phone_number || data.spouse_phone_number) + copyBtn(spouseData.phone_number || data.spouse_phone_number, 'Copy phone', 'fas fa-copy')) : '')}
                                        ${row('fas fa-flag', spouseTitle+' Tribe', spouseTribe)}
                                    </tbody></table>`;
                                }
                                return '';
                            })()}`;
                        // Guardian section (for temporary and independent members)
                        if ((data.membership_type === 'temporary' || (data.membership_type === 'permanent' && data.member_type === 'independent')) && (data.guardian_name || data.guardian_phone || data.guardian_relationship)) {
                            html += `<div class=\"small text-uppercase text-muted mt-3 mb-1\">Guardian</div>
                            <table class=\"table table-bordered table-striped align-middle interactive-table\"><tbody>
                                ${row('fas fa-user-shield', 'Guardian Name', data.guardian_name)}
                                ${row('fas fa-phone-square', 'Guardian Phone', data.guardian_phone)}
                                ${row('fas fa-users-cog', 'Relationship', data.guardian_relationship)}
                            </tbody></table>`;
                        }
                        // Children section (for permanent father/mother only)
                        if (data.membership_type === 'permanent' && (data.member_type === 'father' || data.member_type === 'mother') && Array.isArray(data.children) && data.children.length > 0) {
                            html += `<div class=\"small text-uppercase text-muted mt-3 mb-1\">Children</div>
                            <table class=\"table table-bordered table-striped align-middle interactive-table\"><thead><tr><th>Name</th><th>Gender</th><th>Date of Birth</th></tr></thead><tbody>`;
                            data.children.forEach(child => {
                                html += `<tr><td>${child.full_name || '-'}</td><td>${child.gender || '-'}</td><td>${formatDateDisplay(child.date_of_birth)}</td></tr>`;
                            });
                            html += `</tbody></table>`;
                        }
                        // Archive info (for archived)
                        if (isArchived) {
                            html += `<div class=\"small text-uppercase text-muted mt-3 mb-1\">Archive Info</div>
                            <table class=\"table table-bordered table-striped align-middle interactive-table\"><tbody>
                                ${row('fas fa-archive', 'Reason for Archiving', archiveReason || 'Not specified')}
                                ${row('fas fa-calendar-times', 'Archived Date', m.archived_at ? formatDateDisplay(m.archived_at) : '—')}
                            </tbody></table>`;
                        }
                        html += `</div>`;
                        document.getElementById('memberDetailsBody').innerHTML = html;
                        
                        // Show attendance history button and store member ID
                        const attendanceBtn = document.getElementById('btnAttendanceHistory');
                        attendanceBtn.style.display = 'inline-block';
                        attendanceBtn.setAttribute('data-member-id', m.id);
                        
                        const idCardBtn = document.getElementById('btnIdCard');
                        idCardBtn.style.display = 'inline-block';
                        idCardBtn.setAttribute('data-member-id', m.id);
                        
                        const detailsModalEl = document.getElementById('memberDetailsModal');
                        const modal = new bootstrap.Modal(detailsModalEl);
                        // Set QR image src to encoded details (no link shown when scanning)
                        const qrData = encodeURIComponent(qrPayload);
                        setTimeout(() => {
                            const img = document.getElementById('inlineQrImg');
                            if (img) {
                                try {
                                    const spinner = document.createElement('div');
                                    spinner.id = 'qrSpinner';
                                    spinner.className = 'spinner-border text-primary';
                                    spinner.setAttribute('role', 'status');
                                    if (img.parentElement) img.parentElement.insertBefore(spinner, img);
                                    img.style.display = 'none';
                                    img.onload = () => { spinner && (spinner.style.display = 'none'); img.style.display = 'inline-block'; };
                                    img.onerror = () => { spinner && (spinner.style.display = 'none'); };
                                } catch (e) {}
                                img.src = `https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=${qrData}`;
                            }
                        }, 0);
                        modal.show();
                        // Attach actions for export/print
                        const btnPrint = document.getElementById('btnPrintDetails');
                        const btnCsv = document.getElementById('btnDownloadExcel');
                        const btnPdf = document.getElementById('btnDownloadPDF');
                        btnPrint && (btnPrint.onclick = () => confirmThen('Proceed to print this member details?', () => printMemberDetails()));
                        btnCsv && (btnCsv.onclick = () => confirmThen('Download details as CSV?', () => downloadMemberCSV(m)));
                        btnPdf && (btnPdf.onclick = () => confirmThen('Generate a PDF of these details?', () => downloadMemberPDF()))
                        // Copy all details
                        const btnCopyAll = document.getElementById('btnCopyAllDetails');
                        btnCopyAll && (btnCopyAll.onclick = () => confirmThen('Copy all details to clipboard?', () => { navigator.clipboard.writeText(`${qrPayload}`).then(()=>Swal.fire({ icon:'success', title:'Copied', timer:900, showConfirmButton:false })).catch(()=>Swal.fire({ icon:'error', title:'Copy failed' })); }));
                        // Header edit buttons removed per requirement
                    })
                    .catch((err) => {
                        document.getElementById('memberDetailsBody').innerHTML = `
                            <div class="text-danger">Failed to load member details. ${err && err.message ? '('+err.message+')' : ''}</div>
                            <div class="mt-2">
                                <button class="btn btn-sm btn-outline-primary" onclick="viewDetails(${id})"><i class="fas fa-redo me-1"></i>Retry</button>
                            </div>`;
                        new bootstrap.Modal(document.getElementById('memberDetailsModal')).show();
                    });
            }

			// Ensure openEdit sets state for chooser and header buttons
 			let currentEditMember = null;
            function openEdit(id) {
				console.log('openEdit called with ID:', id);
				confirmThen('Open edit for this member?', () => {
					console.log('Edit confirmed for member ID:', id);
					fetch(`{{ url('/members') }}/${id}`, { headers: { 'Accept': 'application/json' } })
					.then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
                    .then(m => {
						console.log('Member data loaded:', m);
						currentEditMember = m;
						const chooser = new bootstrap.Modal(document.getElementById('editSectionChooserModal'));
						chooser.show();
					})
					.catch(err => {
						console.error('Error loading member:', err);
						Swal.fire({ icon: 'error', title: 'Failed to load member', text: err && err.message ? err.message : 'Please try again.' });
					});
				});
			}

            // Wire chooser buttons to open respective modals with prefill
            // Ensure these are attached after DOM is ready
            function setupEditButtonListeners() {
                const btnEditPersonal = document.getElementById('btnEditPersonal');
                const btnEditLocation = document.getElementById('btnEditLocation');
                const btnEditFamily = document.getElementById('btnEditFamily');
                
                if (btnEditPersonal) {
                    btnEditPersonal.addEventListener('click', () => {
                        if (!currentEditMember) return;
                        const chooser = bootstrap.Modal.getInstance(document.getElementById('editSectionChooserModal'));
                        chooser && chooser.hide();
                        document.getElementById('edit_personal_id').value = currentEditMember.id;
                        document.getElementById('edit_personal_full_name').value = currentEditMember.full_name || '';
                        document.getElementById('edit_personal_email').value = currentEditMember.email || '';
                        document.getElementById('edit_personal_phone_number').value = currentEditMember.phone_number || '';
                        document.getElementById('edit_personal_gender').value = currentEditMember.gender || '';
                        document.getElementById('edit_personal_date_of_birth').value = currentEditMember.date_of_birth || '';
                        document.getElementById('edit_personal_nida_number').value = currentEditMember.nida_number || '';
                        document.getElementById('edit_personal_membership_type').value = currentEditMember.membership_type || 'permanent';
                        // tribe
                        populateSelect(document.getElementById('edit_personal_tribe'), tribeList, 'Select tribe');
                        const tribeEl = document.getElementById('edit_personal_tribe');
                        const otherGroup = document.getElementById('edit_personal_other_tribe_group');
                        const otherInput = document.getElementById('edit_personal_other_tribe');
                        tribeEl.value = tribeList.includes(currentEditMember.tribe) ? currentEditMember.tribe : 'Other';
                        otherGroup.style.display = tribeEl.value === 'Other' ? '' : 'none';
                        otherInput.value = currentEditMember.other_tribe || '';
                        tribeEl.onchange = () => { otherGroup.style.display = tribeEl.value === 'Other' ? '' : 'none'; if (tribeEl.value !== 'Other') otherInput.value = ''; };
                        new bootstrap.Modal(document.getElementById('memberEditPersonalModal')).show();
                    });
                }

                if (btnEditLocation) {
                    btnEditLocation.addEventListener('click', () => {
                        if (!currentEditMember) return;
                        const chooser = bootstrap.Modal.getInstance(document.getElementById('editSectionChooserModal'));
                        chooser && chooser.hide();
                        document.getElementById('edit_location_id').value = currentEditMember.id;
                        ensureLocationsLoaded().then(() => {
                            populateSelect(document.getElementById('edit_location_region'), Object.keys(tzLocations), 'Select region');
                            const regionEl = document.getElementById('edit_location_region');
                            const districtEl = document.getElementById('edit_location_district');
                            const wardEl = document.getElementById('edit_location_ward');
                            function updateDistricts() {
                                populateSelect(districtEl, regionEl.value ? Object.keys(tzLocations[regionEl.value] || {}) : [], 'Select district');
                                updateWards();
                            }
                            function updateWards() {
                                const wards = regionEl.value && districtEl.value ? (tzLocations[regionEl.value]?.[districtEl.value] || []) : [];
                                populateSelect(wardEl, wards, 'Select ward');
                            }
                            regionEl.onchange = updateDistricts;
                            districtEl.onchange = updateWards;
                            regionEl.value = currentEditMember.region || '';
                            updateDistricts();
                            districtEl.value = currentEditMember.district || '';
                            updateWards();
                            wardEl.value = currentEditMember.ward || '';
                        });
                        document.getElementById('edit_location_street').value = currentEditMember.street || '';
                        document.getElementById('edit_location_address').value = currentEditMember.address || '';
                        new bootstrap.Modal(document.getElementById('memberEditLocationModal')).show();
                    });
                }

                if (btnEditFamily) {
                    btnEditFamily.addEventListener('click', () => {
                        if (!currentEditMember) return;
                        const chooser = bootstrap.Modal.getInstance(document.getElementById('editSectionChooserModal'));
                        chooser && chooser.hide();
                        
                        const member = currentEditMember;
                        document.getElementById('edit_family_id').value = member.id;
                        document.getElementById('edit_family_member_type').value = member.member_type || '';
                        document.getElementById('edit_family_membership_type').value = member.membership_type || '';
                        
                        // Show/hide sections based on member type
                        const maritalSection = document.getElementById('edit_family_marital_section');
                        const guardianSection = document.getElementById('edit_family_guardian_section');
                        const isPermanentFatherOrMother = (member.membership_type === 'permanent' && (member.member_type === 'father' || member.member_type === 'mother'));
                        const isTemporaryOrIndependent = (member.membership_type === 'temporary' || (member.membership_type === 'permanent' && member.member_type === 'independent'));
                        
                        if (isPermanentFatherOrMother) {
                            maritalSection.style.display = 'block';
                            // Set marital status
                            document.getElementById('edit_family_marital_status').value = member.marital_status || '';
                            
                            // Show/hide spouse section based on marital status
                            const spouseSection = document.getElementById('edit_family_spouse_section');
                            if (member.marital_status === 'married') {
                                spouseSection.style.display = 'block';
                                // Populate spouse fields
                                document.getElementById('edit_family_spouse_full_name').value = member.spouse_full_name || '';
                                document.getElementById('edit_family_spouse_date_of_birth').value = member.spouse_date_of_birth || '';
                                document.getElementById('edit_family_spouse_education_level').value = member.spouse_education_level || '';
                                document.getElementById('edit_family_spouse_profession').value = member.spouse_profession || '';
                                document.getElementById('edit_family_spouse_nida_number').value = member.spouse_nida_number || '';
                                document.getElementById('edit_family_spouse_email').value = member.spouse_email || '';
                                document.getElementById('edit_family_spouse_phone_number').value = member.spouse_phone_number || '';
                                document.getElementById('edit_family_spouse_church_member').value = member.spouse_church_member || '';
                                
                                // Spouse tribe
                                const spouseTribeEl = document.getElementById('edit_family_spouse_tribe');
                                populateSelect(spouseTribeEl, tribeList, 'Select tribe');
                                if (member.spouse_tribe) {
                                    spouseTribeEl.value = member.spouse_tribe;
                                    if (member.spouse_tribe === 'Other') {
                                        document.getElementById('edit_family_spouse_other_tribe_group').style.display = 'block';
                                        document.getElementById('edit_family_spouse_other_tribe').value = member.spouse_other_tribe || '';
                                    }
                                }
                            } else {
                                spouseSection.style.display = 'none';
                            }
                        } else {
                            maritalSection.style.display = 'none';
                        }
                        
                        if (isTemporaryOrIndependent) {
                            guardianSection.style.display = 'block';
                            document.getElementById('edit_family_guardian_name').value = member.guardian_name || '';
                            document.getElementById('edit_family_guardian_phone').value = member.guardian_phone || '';
                            document.getElementById('edit_family_guardian_relationship').value = member.guardian_relationship || '';
                        } else {
                            guardianSection.style.display = 'none';
                        }
                        
                        // Add event listener for marital status change
                        const maritalStatusEl = document.getElementById('edit_family_marital_status');
                        maritalStatusEl.onchange = function() {
                            const spouseSection = document.getElementById('edit_family_spouse_section');
                            spouseSection.style.display = this.value === 'married' ? 'block' : 'none';
                        };
                        
                        // Add event listener for spouse tribe change
                        const spouseTribeEl = document.getElementById('edit_family_spouse_tribe');
                        spouseTribeEl.onchange = function() {
                            const otherTribeGroup = document.getElementById('edit_family_spouse_other_tribe_group');
                            otherTribeGroup.style.display = this.value === 'Other' ? 'block' : 'none';
                        };
                        
                        new bootstrap.Modal(document.getElementById('memberEditFamilyModal')).show();
                    });
                }
            }

            // Setup edit button listeners when DOM is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', setupEditButtonListeners);
            } else {
                setupEditButtonListeners();
            }

            // Submit handlers for section forms
            function setupFormSubmitHandlers() {
                const editPersonalForm = document.getElementById('editPersonalForm');
                const editLocationForm = document.getElementById('editLocationForm');
                const editFamilyForm = document.getElementById('editFamilyForm');
                
                if (editPersonalForm) {
                    editPersonalForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        const id = document.getElementById('edit_personal_id').value;
                        if (!id) {
                            Swal.fire({ icon: 'error', title: 'Error', text: 'Member ID is missing.' });
                            return;
                        }
                        const fd = new FormData();
                        // Required fields
                        fd.append('full_name', document.getElementById('edit_personal_full_name').value);
                        fd.append('phone_number', document.getElementById('edit_personal_phone_number').value);
                        fd.append('membership_type', document.getElementById('edit_personal_membership_type').value);
                        fd.append('gender', document.getElementById('edit_personal_gender').value);
                        
                        // Optional fields - only send if they have values
                        const email = document.getElementById('edit_personal_email').value.trim();
                        if (email) fd.append('email', email);
                        
                        const dateOfBirth = document.getElementById('edit_personal_date_of_birth').value;
                        if (dateOfBirth) fd.append('date_of_birth', dateOfBirth);
                        
                        const nidaNumber = document.getElementById('edit_personal_nida_number').value;
                        if (nidaNumber) fd.append('nida_number', nidaNumber);
                        
                        const tribeVal = document.getElementById('edit_personal_tribe').value;
                        if (tribeVal && tribeVal !== 'Other') {
                            fd.append('tribe', tribeVal);
                        } else if (tribeVal === 'Other') {
                            const otherTribe = document.getElementById('edit_personal_other_tribe').value;
                            if (otherTribe) {
                                fd.append('tribe', 'Other');
                                fd.append('other_tribe', otherTribe);
                            }
                        }
                        
                        fd.append('_method', 'PUT');
                        
                        const csrfToken = document.querySelector('meta[name="csrf-token"]');
                        if (!csrfToken) {
                            Swal.fire({ icon: 'error', title: 'Error', text: 'CSRF token not found.' });
                            return;
                        }
                        
                        fetch(`{{ url('/members') }}/${id}`, { 
                            method: 'POST', 
                            headers: { 
                                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                                'Accept': 'application/json'
                            }, 
                            body: fd 
                        })
                        .then(async r => {
                            // Clone response to avoid "body already read" error
                            const clonedResponse = r.clone();
                            const contentType = r.headers.get('content-type') || '';
                            const isJson = contentType.includes('application/json');
                            
                            // Handle non-OK responses
                            if (!r.ok) {
                                let errorMessage = `Update failed (Status: ${r.status})`;
                                
                                try {
                                    if (isJson) {
                                        const res = await clonedResponse.json();
                                        // Handle validation errors (422)
                                        if (r.status === 422 && res.errors) {
                                            const errorMessages = Object.values(res.errors).flat().join('<br>');
                                            errorMessage = errorMessages || res.message || 'Validation failed';
                                        }
                                        // Handle 404 (member not found)
                                        else if (r.status === 404) {
                                            errorMessage = 'Member not found. Please refresh the page and try again.';
                                        }
                                        // Handle 500 (server error)
                                        else if (r.status === 500) {
                                            errorMessage = res.message || 'Server error. Please try again later.';
                                        }
                                        // Other errors with message
                                        else if (res.message) {
                                            errorMessage = res.message;
                                        }
                                    } else {
                                        // Not JSON, get text
                                        const text = await clonedResponse.text();
                                        errorMessage = text || errorMessage;
                                    }
                                } catch (parseError) {
                                    // If parsing fails, use default message
                                    console.error('Error parsing response:', parseError);
                                    errorMessage = `Server error (Status: ${r.status})`;
                                }
                                
                                throw new Error(errorMessage);
                            }
                            
                            // Parse successful response
                            if (isJson) {
                                return await r.json();
                            } else {
                                const text = await r.text();
                                throw new Error(text || 'Server returned non-JSON response');
                            }
                        })
                        .then(res => {
                            if (res.success) { 
                                Swal.fire({ icon: 'success', title: 'Saved', timer: 1200, showConfirmButton: false }).then(()=>location.reload()); 
                            } else { 
                                Swal.fire({ icon: 'error', title: 'Update failed', text: res.message || 'Please try again.' }); 
                            }
                        })
                        .catch(err => {
                            console.error('Update error:', err);
                            Swal.fire({ 
                                icon: 'error', 
                                title: 'Update failed', 
                                html: err.message || 'Network error. Please check your connection and try again.' 
                            });
                        });
                    });
                }

                if (editLocationForm) {
                    editLocationForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        const id = document.getElementById('edit_location_id').value;
                        if (!id) {
                            Swal.fire({ icon: 'error', title: 'Error', text: 'Member ID is missing.' });
                            return;
                        }
                        const fd = new FormData();
                        fd.append('region', document.getElementById('edit_location_region').value);
                        fd.append('district', document.getElementById('edit_location_district').value);
                        fd.append('ward', document.getElementById('edit_location_ward').value);
                        fd.append('street', document.getElementById('edit_location_street').value);
                        fd.append('address', document.getElementById('edit_location_address').value);
                        fd.append('_method', 'PUT');
                        
                        const csrfToken = document.querySelector('meta[name="csrf-token"]');
                        if (!csrfToken) {
                            Swal.fire({ icon: 'error', title: 'Error', text: 'CSRF token not found.' });
                            return;
                        }
                        
                        fetch(`{{ url('/members') }}/${id}`, { 
                            method: 'POST', 
                            headers: { 
                                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                                'Accept': 'application/json'
                            }, 
                            body: fd 
                        })
                        .then(async r => {
                            // Clone response to avoid "body already read" error
                            const clonedResponse = r.clone();
                            const contentType = r.headers.get('content-type') || '';
                            const isJson = contentType.includes('application/json');
                            
                            // Handle non-OK responses
                            if (!r.ok) {
                                let errorMessage = `Update failed (Status: ${r.status})`;
                                
                                try {
                                    if (isJson) {
                                        const res = await clonedResponse.json();
                                        // Handle validation errors (422)
                                        if (r.status === 422 && res.errors) {
                                            const errorMessages = Object.values(res.errors).flat().join('<br>');
                                            errorMessage = errorMessages || res.message || 'Validation failed';
                                        }
                                        // Handle 404 (member not found)
                                        else if (r.status === 404) {
                                            errorMessage = 'Member not found. Please refresh the page and try again.';
                                        }
                                        // Handle 500 (server error)
                                        else if (r.status === 500) {
                                            errorMessage = res.message || 'Server error. Please try again later.';
                                        }
                                        // Other errors with message
                                        else if (res.message) {
                                            errorMessage = res.message;
                                        }
                                    } else {
                                        // Not JSON, get text
                                        const text = await clonedResponse.text();
                                        errorMessage = text || errorMessage;
                                    }
                                } catch (parseError) {
                                    // If parsing fails, use default message
                                    console.error('Error parsing response:', parseError);
                                    errorMessage = `Server error (Status: ${r.status})`;
                                }
                                
                                throw new Error(errorMessage);
                            }
                            
                            // Parse successful response
                            if (isJson) {
                                return await r.json();
                            } else {
                                const text = await r.text();
                                throw new Error(text || 'Server returned non-JSON response');
                            }
                        })
                        .then(res => {
                            if (res.success) { 
                                Swal.fire({ icon: 'success', title: 'Saved', timer: 1200, showConfirmButton: false }).then(()=>location.reload()); 
                            } else { 
                                Swal.fire({ icon: 'error', title: 'Update failed', text: res.message || 'Please try again.' }); 
                            }
                        })
                        .catch(err => {
                            console.error('Update error:', err);
                            Swal.fire({ 
                                icon: 'error', 
                                title: 'Update failed', 
                                html: err.message || 'Network error. Please check your connection and try again.' 
                            });
                        });
                    });
                }

                if (editFamilyForm) {
                    editFamilyForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        const id = document.getElementById('edit_family_id').value;
                        if (!id) {
                            Swal.fire({ icon: 'error', title: 'Error', text: 'Member ID is missing.' });
                            return;
                        }
                        const fd = new FormData();
                        const memberType = document.getElementById('edit_family_member_type').value;
                        const membershipType = document.getElementById('edit_family_membership_type').value;
                        
                        // Marital status and spouse info (for permanent father/mother)
                        if (membershipType === 'permanent' && (memberType === 'father' || memberType === 'mother')) {
                            const maritalStatus = document.getElementById('edit_family_marital_status').value;
                            if (maritalStatus) {
                                fd.append('marital_status', maritalStatus);
                            }
                            
                            // Spouse information (if married)
                            if (maritalStatus === 'married') {
                                const spouseFullName = document.getElementById('edit_family_spouse_full_name').value;
                                if (spouseFullName) fd.append('spouse_full_name', spouseFullName);
                                
                                const spouseDob = document.getElementById('edit_family_spouse_date_of_birth').value;
                                if (spouseDob) fd.append('spouse_date_of_birth', spouseDob);
                                
                                const spouseEducation = document.getElementById('edit_family_spouse_education_level').value;
                                if (spouseEducation) fd.append('spouse_education_level', spouseEducation);
                                
                                const spouseProfession = document.getElementById('edit_family_spouse_profession').value;
                                if (spouseProfession) fd.append('spouse_profession', spouseProfession);
                                
                                const spouseNida = document.getElementById('edit_family_spouse_nida_number').value;
                                if (spouseNida) fd.append('spouse_nida_number', spouseNida);
                                
                                const spouseEmail = document.getElementById('edit_family_spouse_email').value;
                                if (spouseEmail) fd.append('spouse_email', spouseEmail);
                                
                                const spousePhone = document.getElementById('edit_family_spouse_phone_number').value;
                                if (spousePhone) fd.append('spouse_phone_number', spousePhone);
                                
                                const spouseChurchMember = document.getElementById('edit_family_spouse_church_member').value;
                                if (spouseChurchMember) fd.append('spouse_church_member', spouseChurchMember);
                                
                                // Spouse tribe
                                const spouseTribeVal = document.getElementById('edit_family_spouse_tribe').value;
                                if (spouseTribeVal && spouseTribeVal !== 'Other') {
                                    fd.append('spouse_tribe', spouseTribeVal);
                                } else if (spouseTribeVal === 'Other') {
                                    const spouseOtherTribe = document.getElementById('edit_family_spouse_other_tribe').value;
                                    if (spouseOtherTribe) {
                                        fd.append('spouse_tribe', 'Other');
                                        fd.append('spouse_other_tribe', spouseOtherTribe);
                                    }
                                }
                            }
                        }
                        
                        // Guardian information (for temporary and independent permanent)
                        if (membershipType === 'temporary' || (membershipType === 'permanent' && memberType === 'independent')) {
                            const guardianName = document.getElementById('edit_family_guardian_name').value;
                            if (guardianName) fd.append('guardian_name', guardianName);
                            
                            const guardianPhone = document.getElementById('edit_family_guardian_phone').value;
                            if (guardianPhone) fd.append('guardian_phone', guardianPhone);
                            
                            const guardianRelationship = document.getElementById('edit_family_guardian_relationship').value;
                            if (guardianRelationship) fd.append('guardian_relationship', guardianRelationship);
                        }
                        
                        fd.append('_method', 'PUT');
                        
                        const csrfToken = document.querySelector('meta[name="csrf-token"]');
                        if (!csrfToken) {
                            Swal.fire({ icon: 'error', title: 'Error', text: 'CSRF token not found.' });
                            return;
                        }
                        
                        fetch(`{{ url('/members') }}/${id}`, { 
                            method: 'POST', 
                            headers: { 
                                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                                'Accept': 'application/json'
                            }, 
                            body: fd 
                        })
                        .then(async r => {
                            // Clone response to avoid "body already read" error
                            const clonedResponse = r.clone();
                            const contentType = r.headers.get('content-type') || '';
                            const isJson = contentType.includes('application/json');
                            
                            // Handle non-OK responses
                            if (!r.ok) {
                                let errorMessage = `Update failed (Status: ${r.status})`;
                                
                                try {
                                    if (isJson) {
                                        const res = await clonedResponse.json();
                                        // Handle validation errors (422)
                                        if (r.status === 422 && res.errors) {
                                            const errorMessages = Object.values(res.errors).flat().join('<br>');
                                            errorMessage = errorMessages || res.message || 'Validation failed';
                                        }
                                        // Handle 404 (member not found)
                                        else if (r.status === 404) {
                                            errorMessage = 'Member not found. Please refresh the page and try again.';
                                        }
                                        // Handle 500 (server error)
                                        else if (r.status === 500) {
                                            errorMessage = res.message || 'Server error. Please try again later.';
                                        }
                                        // Other errors with message
                                        else if (res.message) {
                                            errorMessage = res.message;
                                        }
                                    } else {
                                        // Not JSON, get text
                                        const text = await clonedResponse.text();
                                        errorMessage = text || errorMessage;
                                    }
                                } catch (parseError) {
                                    // If parsing fails, use default message
                                    console.error('Error parsing response:', parseError);
                                    errorMessage = `Server error (Status: ${r.status})`;
                                }
                                
                                throw new Error(errorMessage);
                            }
                            
                            // Parse successful response
                            if (isJson) {
                                return await r.json();
                            } else {
                                const text = await r.text();
                                throw new Error(text || 'Server returned non-JSON response');
                            }
                        })
                        .then(res => {
                            if (res.success) { 
                                Swal.fire({ icon: 'success', title: 'Saved', timer: 1200, showConfirmButton: false }).then(()=>location.reload()); 
                            } else { 
                                Swal.fire({ icon: 'error', title: 'Update failed', text: res.message || 'Please try again.' }); 
                            }
                        })
                        .catch(err => {
                            console.error('Update error:', err);
                            Swal.fire({ 
                                icon: 'error', 
                                title: 'Update failed', 
                                html: err.message || 'Network error. Please check your connection and try again.' 
                            });
                        });
                    });
                }
            }

            // Setup form submit handlers when DOM is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', setupFormSubmitHandlers);
            } else {
                setupFormSubmitHandlers();
            }

            // Quick Add modal cascading + tribe
            function setupCascadingForAdd() {
                // Always reset to a clean state first
                resetAddMemberForm();
                ensureLocationsLoaded().then(() => {
                    populateSelect(document.getElementById('add_region'), Object.keys(tzLocations), 'Select region');
                    const regionEl = document.getElementById('add_region');
                    const districtEl = document.getElementById('add_district');
                    const wardEl = document.getElementById('add_ward');
                    function updateDistricts() {
                        populateSelect(districtEl, regionEl.value ? Object.keys(tzLocations[regionEl.value] || {}) : [], 'Select district');
                        updateWards();
                    }
                    function updateWards() {
                        const wards = regionEl.value && districtEl.value ? (tzLocations[regionEl.value]?.[districtEl.value] || []) : [];
                        populateSelect(wardEl, wards, 'Select ward');
                    }
                    regionEl.onchange = updateDistricts;
                    districtEl.onchange = updateWards;
                });
                populateSelect(document.getElementById('add_tribe'), tribeList, 'Select tribe');
                const tribeEl = document.getElementById('add_tribe');
                const otherGroup = document.getElementById('add_other_tribe_group');
                const otherInput = document.getElementById('add_other_tribe');
                tribeEl.onchange = () => { const show = tribeEl.value === 'Other'; otherGroup.style.display = show ? '' : 'none'; if (!show) otherInput.value = ''; };
            }

            function resetAddMemberForm(){
                // Reset form fields
                const form = document.getElementById('quickAddMemberForm');
                if (form && typeof form.reset === 'function') { form.reset(); }
                // Hide other tribe input
                const otherGroup = document.getElementById('add_other_tribe_group');
                if (otherGroup) otherGroup.style.display = 'none';
                // Clear any existing options in selects to avoid stale state
                ['add_region','add_district','add_ward','add_tribe'].forEach(id => { const s = document.getElementById(id); if (s) s.innerHTML = ''; });
                // Remove any validation classes/messages if present
                const modal = document.getElementById('addMemberModal');
                if (modal) {
                    modal.querySelectorAll('.is-invalid, .is-valid').forEach(el => el.classList.remove('is-invalid','is-valid'));
                    modal.querySelectorAll('.invalid-feedback, .valid-feedback').forEach(el => { el.textContent = ''; el.style.display = 'none'; });
                }
            }

            document.getElementById('addMemberModal').addEventListener('show.bs.modal', setupCascadingForAdd);
            // Ensure fresh state after cancel/close
            document.getElementById('addMemberModal').addEventListener('hidden.bs.modal', resetAddMemberForm);

            document.getElementById('quickAddMemberForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const fd = new FormData();
                fd.append('full_name', document.getElementById('add_full_name').value);
                fd.append('gender', document.getElementById('add_gender').value);
                fd.append('phone_number', document.getElementById('add_phone_number').value);
                fd.append('email', document.getElementById('add_email').value);
                fd.append('region', document.getElementById('add_region').value);
                fd.append('district', document.getElementById('add_district').value);
                fd.append('ward', document.getElementById('add_ward').value);
                const tribeVal = document.getElementById('add_tribe').value;
                fd.append('tribe', tribeVal === 'Other' ? '' : tribeVal);
                fd.append('other_tribe', document.getElementById('add_other_tribe').value);
                fetch(`{{ url('/members') }}`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }, body: fd })
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                            // Reset form state before reload just in case
                            resetAddMemberForm();
                            Swal.fire({ icon: 'success', title: 'Member registered', timer: 1400, showConfirmButton: false }).then(()=>location.reload());
                    } else {
                            Swal.fire({ icon: 'error', title: 'Registration failed', text: res.message || 'Please review and try again.' });
                    }
                })
                    .catch(()=> Swal.fire({ icon: 'error', title: 'Network error' }));
            });

            function downloadArchiveReport(member, reason) {
                try {
                    console.log('Starting download for member:', member);
                    
                    // Generate the report HTML
                    const reportHTML = generateArchiveReportHTML(member, reason);
                    console.log('Generated HTML length:', reportHTML.length);
                    
                    // Method 1: Try blob download first
                    if (window.Blob && window.URL) {
                        try {
                            const blob = new Blob([reportHTML], { 
                                type: 'text/html;charset=utf-8' 
                            });
                            console.log('Created blob:', blob);
                            
                            const url = window.URL.createObjectURL(blob);
                            console.log('Created URL:', url);
                            
                            const link = document.createElement('a');
                            link.href = url;
                            link.download = `Member_Archive_Report_${member.member_id || member.id || 'Unknown'}_${new Date().toISOString().split('T')[0]}.html`;
                            link.style.display = 'none';
                            
                            console.log('Download filename:', link.download);
                            
                            document.body.appendChild(link);
                            
                            // Trigger download
                            setTimeout(() => {
                                link.click();
                                console.log('Download triggered');
                                
                                setTimeout(() => {
                                    document.body.removeChild(link);
                                    window.URL.revokeObjectURL(url);
                                    console.log('Cleanup completed');
                                }, 100);
                            }, 100);
                            
                            // Show success message
                            Swal.fire({
                                icon: 'success',
                                title: 'Report Downloaded',
                                text: 'Archive report has been downloaded successfully!',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            
                            return;
                        } catch (blobError) {
                            console.log('Blob method failed, trying alternative:', blobError);
                        }
                    }
                    
                    // Method 2: Fallback - open in new window and let user save
                    const newWindow = window.open('', '_blank');
                    if (newWindow) {
                        newWindow.document.write(reportHTML);
                        newWindow.document.close();
                        newWindow.focus();
                        
                        // Show instructions
                        Swal.fire({
                            icon: 'info',
                            title: 'Report Opened',
                            html: `
                                <p>The report has been opened in a new window.</p>
                                <p><strong>To save the file:</strong></p>
                                <ol class="text-start">
                                    <li>Press <kbd>Ctrl+S</kbd> (Windows) or <kbd>Cmd+S</kbd> (Mac)</li>
                                    <li>Choose a location to save the file</li>
                                    <li>The file will be saved as an HTML file</li>
                                </ol>
                            `,
                            showConfirmButton: true,
                            confirmButtonText: 'Got it!'
                        });
                    } else {
                        throw new Error('Could not open new window');
                    }
                    
                } catch (error) {
                    console.error('Download error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Download Failed',
                        html: `
                            <p>There was an error downloading the report.</p>
                            <p><strong>Alternative options:</strong></p>
                            <ul class="text-start">
                                <li>Use the "Print Report" option and save as PDF</li>
                                <li>Copy the report content manually</li>
                                <li>Try using a different browser</li>
                            </ul>
                        `,
                        showConfirmButton: true
                    });
                }
            }

            function generateArchiveReportHTML(member, reason) {
                return `
                    <!DOCTYPE html>
                    <html lang="en">
                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <title>Member Archive Report - ${member.full_name}</title>
                        <style>
                            * {
                                margin: 0;
                                padding: 0;
                                box-sizing: border-box;
                            }
                            
                            body {
                                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                                background: #f8f9fa;
                                padding: 20px;
                                line-height: 1.6;
                            }
                            
                            .report-container {
                                max-width: 600px;
                                margin: 0 auto;
                                background: white;
                                border-radius: 15px;
                                box-shadow: 0 10px 30px rgba(0,0,0,0.1);
                                overflow: hidden;
                            }
                            
                            .report-header {
                                background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
                                color: white;
                                padding: 25px;
                                text-align: center;
                            }
                            
                            .report-header h1 {
                                font-size: 24px;
                                margin-bottom: 10px;
                                font-weight: 600;
                            }
                            
                            .report-header .subtitle {
                                font-size: 14px;
                                opacity: 0.9;
                            }
                            
                            .report-body {
                                padding: 30px;
                            }
                            
                            .member-info {
                                background: #f8f9fa;
                                border-radius: 10px;
                                padding: 20px;
                                margin-bottom: 25px;
                            }
                            
                            .member-info h3 {
                                color: #495057;
                                margin-bottom: 15px;
                                font-size: 18px;
                                border-bottom: 2px solid #dee2e6;
                                padding-bottom: 10px;
                            }
                            
                            .info-grid {
                                display: grid;
                                grid-template-columns: 1fr 1fr;
                                gap: 15px;
                            }
                            
                            .info-item {
                                display: flex;
                                flex-direction: column;
                            }
                            
                            .info-label {
                                font-weight: 600;
                                color: #6c757d;
                                font-size: 12px;
                                text-transform: uppercase;
                                letter-spacing: 0.5px;
                                margin-bottom: 5px;
                            }
                            
                            .info-value {
                                color: #212529;
                                font-size: 14px;
                                font-weight: 500;
                            }
                            
                            .archive-reason {
                                background: #fff3cd;
                                border: 1px solid #ffeaa7;
                                border-radius: 10px;
                                padding: 20px;
                                margin-bottom: 25px;
                            }
                            
                            .archive-reason h3 {
                                color: #856404;
                                margin-bottom: 15px;
                                font-size: 18px;
                                display: flex;
                                align-items: center;
                            }
                            
                            .archive-reason h3::before {
                                content: "📋";
                                margin-right: 10px;
                            }
                            
                            .reason-text {
                                color: #856404;
                                font-size: 14px;
                                line-height: 1.6;
                                background: white;
                                padding: 15px;
                                border-radius: 8px;
                                border-left: 4px solid #ffc107;
                            }
                            
                            .financial-note {
                                background: #d1ecf1;
                                border: 1px solid #bee5eb;
                                border-radius: 10px;
                                padding: 20px;
                                text-align: center;
                            }
                            
                            .financial-note h4 {
                                color: #0c5460;
                                margin-bottom: 10px;
                                font-size: 16px;
                            }
                            
                            .financial-note p {
                                color: #0c5460;
                                font-size: 14px;
                                margin: 0;
                            }
                            
                            .report-footer {
                                background: #f8f9fa;
                                padding: 20px;
                                text-align: center;
                                border-top: 1px solid #dee2e6;
                            }
                            
                            .report-footer p {
                                color: #6c757d;
                                font-size: 12px;
                                margin: 0;
                            }
                            
                            .date-time {
                                color: #6c757d;
                                font-size: 12px;
                                margin-top: 10px;
                            }
                            
                            @media print {
                                body {
                                    background: white;
                                    padding: 0;
                                }
                                
                                .report-container {
                                    box-shadow: none;
                                    border-radius: 0;
                                }
                            }
                        </style>
                    </head>
                    <body>
                        <div class="report-container">
                            <div class="report-header">
                                <h1>📦 Member Archive Report</h1>
                                <p class="subtitle">Member has been moved to archived status</p>
                            </div>
                            
                            <div class="report-body">
                                <div class="member-info">
                                    <h3>👤 Member Information</h3>
                                    <div class="info-grid">
                                        <div class="info-item">
                                            <span class="info-label">Full Name</span>
                                            <span class="info-value">${member.full_name || 'N/A'}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Member ID</span>
                                            <span class="info-value">${member.member_id || 'N/A'}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Phone Number</span>
                                            <span class="info-value">${member.phone_number || 'N/A'}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Email</span>
                                            <span class="info-value">${member.email || 'N/A'}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Gender</span>
                                            <span class="info-value">${member.gender ? member.gender.charAt(0).toUpperCase() + member.gender.slice(1) : 'N/A'}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Membership Type</span>
                                            <span class="info-value">${member.membership_type ? member.membership_type.charAt(0).toUpperCase() + member.membership_type.slice(1) : 'N/A'}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Date of Birth</span>
                                            <span class="info-value">${member.date_of_birth ? new Date(member.date_of_birth).toLocaleDateString() : 'N/A'}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Registration Date</span>
                                            <span class="info-value">${member.created_at ? new Date(member.created_at).toLocaleDateString() : 'N/A'}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="archive-reason">
                                    <h3>Archive Reason</h3>
                                    <div class="reason-text">${reason}</div>
                                </div>
                                
                                <div class="financial-note">
                                    <h4>💰 Financial Records Preserved</h4>
                                    <p>All financial records including tithes, offerings, donations, and pledges have been preserved and remain intact in the system.</p>
                                </div>
                            </div>
                            
                            <div class="report-footer">
                                <p><strong>Waumini Link Church Management System</strong></p>
                                <p class="date-time">Report generated on ${new Date().toLocaleString()}</p>
                            </div>
                        </div>
                    </body>
                    </html>
                `;
            }

            function printArchiveReport(member, reason) {
                // Create a new window for printing
                const printWindow = window.open('', '_blank', 'width=800,height=600');
                
                // Generate the report HTML using the shared function
                const reportHTML = generateArchiveReportHTML(member, reason);
                
                // Write the HTML to the new window
                printWindow.document.write(reportHTML);
                printWindow.document.close();
                
                // Focus the window and trigger print dialog
                printWindow.focus();
                setTimeout(() => {
                    printWindow.print();
                }, 500);
            }

            function confirmDelete(id) {
                console.log('confirmDelete called with ID:', id);
                console.log('Attempting to delete member with ID:', id);
                
                // Check if we're in the archived tab
                const isArchived = document.querySelector('.nav-link[href="#archived"]')?.classList.contains('active');
                console.log('Is archived tab:', isArchived);
                
                // First, test if the member exists
                fetch(`/test-member/${id}`)
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            // If not found in active members, check if it's archived
                            if (isArchived) {
                                console.log('Member not found in active members, checking archived...');
                                // For archived members, we'll proceed with deletion attempt
                                // The controller will handle the archived member deletion
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Member Not Found',
                                    text: 'The member you are trying to delete does not exist in active members.',
                                    confirmButtonText: 'OK'
                                });
                                return;
                            }
                        }
                        
                        console.log('Member found:', data.member);
                        
                        // Show reason input form first
                        Swal.fire({
                            title: 'Archive Member',
                            html: `
                                <div class="mb-3">
                                    <label for="archive-reason" class="form-label">Reason for archiving ${data.member.full_name}:</label>
                                    <textarea id="archive-reason" class="form-control" rows="3" placeholder="Please provide a reason for archiving this member..." required></textarea>
                                </div>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Note:</strong> The member will be moved to archived status and all their financial records will be preserved.
                                </div>
                            `,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Archive Member',
                            cancelButtonText: 'Cancel',
                            confirmButtonColor: '#dc3545',
                            preConfirm: () => {
                                const reason = document.getElementById('archive-reason').value.trim();
                                if (!reason) {
                                    Swal.showValidationMessage('Please provide a reason for archiving this member.');
                                    return false;
                                }
                                return reason;
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Show loading state
                                Swal.fire({
                                    title: 'Archiving...',
                                    text: 'Please wait while we archive the member.',
                                    allowOutsideClick: false,
                                    allowEscapeKey: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                });

                                // Use different endpoint for archived vs active members
                                const deleteUrl = isArchived ? `{{ url('/members/archived') }}/${id}` : `{{ url('/members') }}/${id}`;
                                console.log('Delete URL:', deleteUrl);
                                console.log('Archive reason:', result.value);
                                
                                // Prepare request body with reason
                                const requestBody = {
                                    reason: result.value
                                };
                                
                                // Use a simple fetch request with proper error handling
                                fetch(deleteUrl, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify(requestBody)
                                })
                                .then(response => {
                                    console.log('Delete response status:', response.status);
                                    if (response.ok) {
                                        return response.json();
                                    } else if (response.status === 403) {
                                        // Handle 403 Forbidden - permission denied
                                        return response.json().then(data => {
                                            throw new Error(data.message || 'You do not have permission to archive members. Please contact your administrator.');
                                        });
                                    } else if (response.status === 404) {
                                        throw new Error('Member not found');
                                    } else if (response.status === 419) {
                                        // CSRF token expired - reload page to get new token
                                        if (typeof Swal !== 'undefined') {
                                            Swal.fire({
                                                icon: 'info',
                                                title: 'Refreshing...',
                                                text: 'Please wait while we refresh your session.',
                                                allowOutsideClick: false,
                                                allowEscapeKey: false,
                                                showConfirmButton: false,
                                                didOpen: () => {
                                                    Swal.showLoading();
                                                }
                                            });
                                            setTimeout(() => {
                                                window.location.reload();
                                            }, 500);
                                        } else {
                                            window.location.reload();
                                        }
                                        return;
                                    } else if (response.status === 422) {
                                        // Parse the 422 response to get the actual error message
                                        return response.json().then(data => {
                                            throw new Error(data.message || 'Validation error occurred');
                                        });
                                    } else {
                                        // Try to parse error message from response
                                        return response.json().then(data => {
                                            throw new Error(data.message || `Server error: ${response.status}`);
                                        }).catch(() => {
                                            throw new Error(`Server error: ${response.status}`);
                                        });
                                    }
                                })
                                .then(data => {
                                    console.log('Delete response data:', data);
                                    if (data.success) {
                                        // Remove the row from the table
                                        const row = document.getElementById(`row-${id}`);
                                        if (row) {
                                            row.remove();
                                        }
                                        
                                        // Also remove from card view if it exists
                                        const card = document.querySelector(`[data-member-id="${id}"]`);
                                        if (card) {
                                            card.remove();
                                        }
                                        
                                        Swal.fire({ 
                                            icon: 'success', 
                                            title: 'Member Archived', 
                                            html: `
                                                <div class="text-start">
                                                    <p><strong>Reason:</strong> ${result.value}</p>
                                                    <p>The member has been moved to archived status. All financial records (tithes, offerings, donations, pledges) have been preserved and remain intact.</p>
                                                </div>
                                            `, 
                                            showConfirmButton: true,
                                            showCancelButton: true,
                                            showDenyButton: true,
                                            confirmButtonText: '📄 Download Report',
                                            denyButtonText: '🖨️ Print Report',
                                            cancelButtonText: 'Close',
                                            confirmButtonColor: '#28a745',
                                            denyButtonColor: '#007bff',
                                            allowOutsideClick: false,
                                            allowEscapeKey: false,
                                            timer: 0
                                        }).then((actionResult) => {
                                            if (actionResult.isConfirmed) {
                                                downloadArchiveReport(data.member, result.value);
                                            } else if (actionResult.isDenied) {
                                                printArchiveReport(data.member, result.value);
                                            }
                                            
                                            // Only reload after user has made a choice
                                            setTimeout(() => {
                                                location.reload();
                                            }, 1000);
                                        });
                                    } else {
                                        Swal.fire({ 
                                            icon: 'error', 
                                            title: 'Delete failed', 
                                            text: data.message || 'Please try again.',
                                            confirmButtonText: 'OK'
                                        });
                                    }
                                })
                                .catch(error => {
                                    console.error('Delete error:', error);
                                    Swal.fire({ 
                                        icon: 'error', 
                                        title: 'Error', 
                                        text: error.message,
                                        confirmButtonText: 'OK'
                                    });
                                });
                            }
                        });
                    })
                    .catch(error => {
                        console.error('Member check error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Unable to verify member. Please try again.',
                            confirmButtonText: 'OK'
                        });
                    });
            }

            // Simple client-side, real-time filtering
            function filterTable() {
                const q = (document.getElementById('searchInput').value || '').trim().toLowerCase();
                const gender = (document.getElementById('genderFilter').value || '').toLowerCase();
                const region = (document.getElementById('regionFilter').value || '').toLowerCase();
                const district = (document.getElementById('districtFilter').value || '').toLowerCase();
                const ward = (document.getElementById('wardFilter').value || '').toLowerCase();
                const rows = document.querySelectorAll('#membersTable tbody tr');
                let visibleIndex = 0;
                rows.forEach((row) => {
                    const textMatch = [
                        row.dataset.name,
                        row.dataset.memberid,
                        row.dataset.phone,
                        row.dataset.email
                    ].some(v => (v || '').includes(q));
                    const genderMatch = !gender || (row.dataset.gender === gender);
                    const regionMatch = !region || (row.dataset.region === region);
                    const districtMatch = !district || (row.dataset.district === district);
                    const wardMatch = !ward || (row.dataset.ward === ward);
                    const show = textMatch && genderMatch && regionMatch && districtMatch && wardMatch;
                    row.style.display = show ? '' : 'none';
                    if (show) {
                        // Re-number visible rows client-side
                        const numberCell = row.querySelector('td');
                        if (numberCell) numberCell.textContent = String(++visibleIndex);
                    }
                });
            }

            ['searchInput', 'genderFilter', 'regionFilter', 'districtFilter', 'wardFilter']
                .forEach(id => {
                    const el = document.getElementById(id);
                    if (!el) return;
                    const evt = id === 'searchInput' ? 'input' : 'change';
                    el.addEventListener(evt, filterTable);
                });

            function downloadMemberCSV(m) {
                const headers = ['Full Name','Member ID','Phone','Email','Gender','Date of Birth','NIDA Number','Region','District','Ward','Street','Address','Living with family','Family relationship','Tribe','Other tribe'];
                const values = [
                    m.full_name || '',
                    m.member_id || '',
                    m.phone_number || '',
                    m.email || '',
                    m.gender || '',
                    m.date_of_birth || '',
                    m.nida_number || '',
                    m.region || '',
                    m.district || '',
                    m.ward || '',
                    m.street || '',
                    m.address || '',
                    m.living_with_family || '',
                    m.family_relationship || '',
                    m.tribe || '',
                    m.other_tribe || ''
                ];
                const csv = [headers.join(','), values.map(v => '"' + String(v).replace(/"/g, '""') + '"').join(',')].join('\n');
                const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = (m.member_id || 'member') + '.csv';
                a.click();
                URL.revokeObjectURL(url);
            }

            function printMemberDetails() {
                const content = document.getElementById('memberDetailsPrint');
                const w = window.open('', '_blank');
                const logoUrl = `{{ asset('assets/images/waumini_link_logo.png') }}`;
                const printedAt = new Date().toLocaleString();
                const printedBy = `{{ Auth::user()->name ?? 'User' }}`;
                const yearNow = new Date().getFullYear();
                const m = currentDetailsMember || null;
                const payload = (function(mm){ if(!mm) return ''; return [
                    'Full Name: ' + (mm.full_name || '-'),
                    'Member ID: ' + (mm.member_id || '-'),
                    'Phone: ' + (mm.phone_number || '-'),
                    'Email: ' + (mm.email || '-'),
                    'Gender: ' + (mm.gender ? mm.gender.charAt(0).toUpperCase()+mm.gender.slice(1) : '-'),
                    'Date of Birth: ' + formatDateDisplay(mm.date_of_birth),
                    'NIDA Number: ' + (mm.nida_number || '-'),
                    'Region: ' + (mm.region || '-'),
                    'District: ' + (mm.district || '-'),
                    'Ward: ' + (mm.ward || '-'),
                    'Street: ' + (mm.street || '-'),
                    'Address: ' + (mm.address || '-'),
                    'Living with family: ' + (mm.living_with_family || '-'),
                    'Family relationship: ' + (mm.family_relationship || '-'),
                    'Tribe: ' + ((mm.tribe || '-') + (mm.other_tribe ? (' ('+mm.other_tribe+')') : ''))
                ].join('\n'); })(m);

                // Prebuild section HTML using current window's data
                function row(label, value){ return '<tr><td>' + label + '</td><td><strong>' + (value ? String(value) : '—') + '</strong></td></tr>'; }
                let sectionsHtml = '';
                if (m) {
                    sectionsHtml += '<div class="section-title">Personal</div>'+
                        '<table class="table"><tbody>'+
                        row('Full Name', m.full_name)+
                        row('Member ID', m.member_id)+
                        row('Phone', m.phone_number)+
                        row('Email', m.email)+
                        row('Gender', m.gender ? (m.gender.charAt(0).toUpperCase()+m.gender.slice(1)) : '')+
                        row('Date of Birth', formatDateDisplay(m.date_of_birth))+
                        row('NIDA Number', m.nida_number)+
                        '</tbody></table>';

                    sectionsHtml += '<div class="section-title">Location</div>'+
                        '<table class="table"><tbody>'+
                        row('Region', m.region)+
                        row('District', m.district)+
                        row('Ward', m.ward)+
                        row('Street', m.street)+
                        row('Address', m.address)+
                        '</tbody></table>';

                    sectionsHtml += '<div class="section-title">Family</div>'+
                        '<table class="table"><tbody>'+
                        row('Living with family', m.living_with_family)+
                        row('Family relationship', m.family_relationship)+
                        row('Tribe', (m.tribe || '') + (m.other_tribe ? (' ('+m.other_tribe+')') : ''))+
                        '</tbody></table>';
                    
                    // Add spouse details if present
                    if (m.spouse_full_name || m.spouse_email || m.spouse_phone_number || m.spouse_profession || m.spouse_education_level || m.spouse_nida_number || m.spouse_date_of_birth || m.spouse_tribe) {
                        const spouseTitle = (m.member_type === 'father' ? 'Wife' : (m.member_type === 'mother' ? 'Husband' : 'Spouse'));
                        sectionsHtml += '<div class="section-title">' + spouseTitle + '</div>'+
                            '<table class="table"><tbody>'+
                            row('Marital Status', m.marital_status ? (m.marital_status.charAt(0).toUpperCase() + m.marital_status.slice(1)) : '—')+
                            row(spouseTitle + ' Name', m.spouse_full_name)+
                            row(spouseTitle + ' Church Member', m.spouse_church_member ? (m.spouse_church_member === 'yes' ? 'Yes' : 'No') : '—')+
                            row(spouseTitle + ' DOB', formatDateDisplay(m.spouse_date_of_birth))+
                            row(spouseTitle + ' Education', m.spouse_education_level)+
                            row(spouseTitle + ' Profession', m.spouse_profession)+
                            row(spouseTitle + ' NIDA', m.spouse_nida_number)+
                            row(spouseTitle + ' Email', m.spouse_email)+
                            row(spouseTitle + ' Phone', m.spouse_phone_number)+
                            row(spouseTitle + ' Tribe', (m.spouse_tribe || '') + (m.spouse_other_tribe ? (' ('+m.spouse_other_tribe+')') : ''))+
                            '</tbody></table>';
                    }
                }

                w.document.write('<html><head><title>Member Details</title>');
                w.document.write('<link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">');
                w.document.write('<style>\n@page { margin: 15mm; }\nbody{ -webkit-print-color-adjust: exact; print-color-adjust: exact; }\n.print-shell{ max-width: 980px; margin: 0 auto; }\n.header{ position:relative; padding:16px 18px; border-radius:10px; margin-bottom:18px; background: linear-gradient(135deg, #f4f6ff 0%, #ffffff 100%); border:1px solid #e9ecef; }\n.header-top{ display:flex; align-items:center; justify-content:space-between; }\n.brand{ display:flex; align-items:center; gap:12px; }\n.brand h2{ margin:0; color:#1f2b6c; }\n.badges{ display:none; }\n.qr-wrap{ text-align:right; }\n.qr{ width:120px; height:120px; border:3px solid #5b2a86; border-radius:8px; padding:4px; background:#fff }\n.section-title{ font-size:12px; letter-spacing:1px; color:#6c757d; text-transform:uppercase; margin:18px 0 6px; }\n.table{ width:100%; border-collapse:separate; border-spacing:0; }\n.table th,.table td{ padding:10px 12px; vertical-align:top; }\n.table tbody tr:nth-child(odd){ background:#fbfbfe; }\n.table tbody tr td:first-child{ width:220px; color:#6c757d; border-left:4px solid #5b2a86; }\n.footer{ margin-top:24px; padding-top:12px; border-top:1px dashed #ced4da; font-size:12px; color:#6c757d; text-align:center; }\n.footer a{ color:#5b2a86; text-decoration:none; }\n.footer a:hover{ text-decoration:underline; }\n</style>');
                w.document.write('</head><body>');
                w.document.write('<div class="print-shell">');
                // Header
                w.document.write(`<div class="header">
                     <div class="header-top">
                         <div class="brand">
                             <img src="${logoUrl}" style="height:48px"/>
                             <div>
                                 <h2 class="mb-0">Member Details</h2>
                             </div>
                         </div>
                         <div class="qr-wrap"><img id="printQrImg" class="qr" src="" alt="QR"/></div>
                     </div>
                 </div>`);

                // Sections (prebuilt)
                w.document.write(sectionsHtml);

                // Footer
                w.document.write(`<div class="footer">
                    Printed on ${printedAt} by ${printedBy} • © ${yearNow} Waumini Link • Powered by <a href="https://emca.tech/#" target="_blank" rel="noopener" style="color: #940000 !important;">EmCa Technologies</a>
                </div>`);

                w.document.write('</div>');
                // Ensure QR loads before printing
                const qrUrlPromise = getQrDataUrl(payload, 120);
                w.document.write(`<script>\n(function(){\nvar done = false;\nfunction go(){ if(done) return; done = true; try{ window.print(); }catch(e){} setTimeout(function(){ window.close(); }, 200); }\nwindow.addEventListener('load', function(){ setTimeout(go, 400); });\n})();\n<\/script>`);
                w.document.write('</body></html>');
                w.document.close();
                w.focus();
                // After doc open, set QR and wait for load
                setTimeout(function(){
                    qrUrlPromise.then(function(url){
                        const fallback = 'https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=' + encodeURIComponent(payload || 'Member');
                        try {
                            const img = w.document.getElementById('printQrImg');
                            if (img) {
                                img.onload = function(){ setTimeout(function(){ try{ w.print(); }catch(e){} setTimeout(function(){ try{ w.close(); }catch(e){} }, 200); }, 150); };
                                img.onerror = function(){ setTimeout(function(){ try{ w.print(); }catch(e){} setTimeout(function(){ try{ w.close(); }catch(e){} }, 200); }, 200); };
                                img.src = url || fallback;
                            } else {
                                setTimeout(function(){ try{ w.print(); }catch(e){} setTimeout(function(){ try{ w.close(); }catch(e){} }, 200); }, 300);
                            }
                        } catch(e){ setTimeout(function(){ try{ w.print(); }catch(e){} setTimeout(function(){ try{ w.close(); }catch(e){} }, 200); }, 300); }
                    });
                }, 50);
            }

            function downloadMemberPDF(){
                const m = currentDetailsMember || null;
                if (!m) return Swal.fire({ icon:'error', title:'Open details first' });
                // Build a hidden container to render into PDF
                const container = document.createElement('div');
                container.style.position = 'fixed';
                container.style.left = '-9999px';
                container.style.top = '0';
                container.style.width = '800px';
                container.innerHTML = '';
                const logoUrl = `{{ asset('assets/images/waumini_link_logo.png') }}`;
                const payload = [
                    'Full Name: ' + (m.full_name || '-'),
                    'Member ID: ' + (m.member_id || '-'),
                    'Phone: ' + (m.phone_number || '-'),
                    'Email: ' + (m.email || '-'),
                    'Gender: ' + (m.gender ? m.gender.charAt(0).toUpperCase()+m.gender.slice(1) : '-'),
                    'Date of Birth: ' + formatDateDisplay(m.date_of_birth),
                    'NIDA Number: ' + (m.nida_number || '-'),
                    'Region: ' + (m.region || '-'),
                    'District: ' + (m.district || '-'),
                    'Ward: ' + (m.ward || '-'),
                    'Street: ' + (m.street || '-'),
                    'Address: ' + (m.address || '-'),
                    'Living with family: ' + (m.living_with_family || '-'),
                    'Family relationship: ' + (m.family_relationship || '-'),
                    'Tribe: ' + ((m.tribe || '-') + (m.other_tribe ? (' ('+m.other_tribe+')') : ''))
                ].join('\n');
                // Generate data URL for QR to avoid CORS issues in PDF
                getQrDataUrl(payload, 120).then(function(qrDataUrl){
                    const qrImgSrc = qrDataUrl || ('https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=' + encodeURIComponent(payload));
                function row(label, value){ return '<tr><td style="width:220px;color:#6c757d;border-left:4px solid #5b2a86; padding:8px 10px">' + label + '</td><td style="padding:8px 10px"><strong>' + (value ? String(value) : '—') + '</strong></td></tr>'; }
                let html = '';
                html += '<div style="border:1px solid #e9ecef;border-radius:10px;padding:14px 16px;margin-bottom:16px;background:linear-gradient(135deg,#f4f6ff 0%,#ffffff 100%)">'+
                        '<div style="display:flex;align-items:center;justify-content:space-between">'+
                        '<div style="display:flex;align-items:center;gap:12px">'+
                        '<img src="'+logoUrl+'" style="height:44px"/><div><h3 style="margin:0;color:#1f2b6c">Member Details</h3>'+ 
                        '</div></div></div>'+ 
                        '<div><img src="'+qrImgSrc+'" style="width:120px;height:120px;border:3px solid #5b2a86;border-radius:8px;padding:4px;background:#fff"/></div>'+ 
                        '</div></div>';
                html += '<div style="font-size:12px;letter-spacing:1px;color:#6c757d;text-transform:uppercase;margin:14px 0 6px">Personal</div>';
                html += '<table style="width:100%;border-collapse:separate;border-spacing:0"><tbody>'+
                        row('Full Name', m.full_name)+row('Member ID', m.member_id)+row('Phone', m.phone_number)+row('Email', m.email)+row('Gender', m.gender ? (m.gender.charAt(0).toUpperCase()+m.gender.slice(1)) : '')+row('Date of Birth', formatDateDisplay(m.date_of_birth))+row('NIDA Number', m.nida_number)+
                        '</tbody></table>';
                html += '<div style="font-size:12px;letter-spacing:1px;color:#6c757d;text-transform:uppercase;margin:14px 0 6px">Location</div>';
                html += '<table style="width:100%;border-collapse:separate;border-spacing:0"><tbody>'+
                        row('Region', m.region)+row('District', m.district)+row('Ward', m.ward)+row('Street', m.street)+row('Address', m.address)+
                        '</tbody></table>';
                html += '<div style="font-size:12px;letter-spacing:1px;color:#6c757d;text-transform:uppercase;margin:14px 0 6px">Family</div>';
                html += '<table style="width:100%;border-collapse:separate;border-spacing:0"><tbody>'+
                        row('Living with family', m.living_with_family)+row('Family relationship', m.family_relationship)+row('Tribe', (m.tribe || '') + (m.other_tribe ? (' ('+m.other_tribe+')') : ''))+
                        '</tbody></table>';
                html += '<div style="margin-top:18px;padding-top:10px;border-top:1px dashed #ced4da;font-size:12px;color:#6c757d;text-align:center">Powered by <a href="https://emca.tech/#" target="_blank" style="color:#940000;text-decoration:none">EmCa Technologies</a></div>';
                container.innerHTML = html;
                document.body.appendChild(container);
                // Preload images before generating PDF
                const imgs = Array.from(container.querySelectorAll('img'));
                Promise.all(imgs.map(img => new Promise(res => { if (img.complete) return res(); img.onload = () => res(); img.onerror = () => res(); }))).then(() => {
                // Load html2pdf and generate
                function generate(){
                    window.html2pdf().set({ margin:10, filename: (m.member_id || 'member') + '.pdf', image: { type:'jpeg', quality: 0.98 }, html2canvas: { scale: 2, useCORS:true, allowTaint:true }, jsPDF: { unit:'mm', format:'a4', orientation:'portrait' } }).from(container).save().then(()=>{ document.body.removeChild(container); }).catch(()=>{ document.body.removeChild(container); Swal.fire({ icon:'error', title:'PDF failed' }); });
                }
                if (!window.html2pdf) {
                    const s = document.createElement('script');
                    s.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js';
                    s.onload = generate;
                    s.onerror = () => { document.body.removeChild(container); Swal.fire({ icon:'error', title:'Failed to load PDF lib' }); };
                    document.head.appendChild(s);
                } else {
                    generate();
                }
                });
                });
            }

            // Cascading selects for Region -> District -> Ward and Tribe
            let tzLocations = null;
            let tribeList = ['Chaga','Sukuma','Haya','Nyakyusa','Makonde','Hehe','Other'];
            function ensureLocationsLoaded() {
                if (tzLocations) return Promise.resolve(tzLocations);
                return fetch(`{{ asset('data/tanzania-locations.json') }}`)
                    .then(r => r.json())
                    .then(json => { tzLocations = json; return tzLocations; });
            }
            function populateSelect(selectEl, items, placeholder = 'Select') {
                selectEl.innerHTML = '';
                const opt = document.createElement('option');
                opt.value = '';
                opt.textContent = placeholder;
                selectEl.appendChild(opt);
                (items || []).forEach(v => {
                    const o = document.createElement('option');
                    o.value = v;
                    o.textContent = v;
                    selectEl.appendChild(o);
                });
            }
            function setupCascadingForEdit(prefill = {}) {
                ensureLocationsLoaded().then(data => {
                    const regions = Object.keys(data || {});
                    const regionEl = document.getElementById('edit_region');
                    const districtEl = document.getElementById('edit_district');
                    const wardEl = document.getElementById('edit_ward');
                    populateSelect(regionEl, regions, 'Select region');
                    if (prefill.region) regionEl.value = prefill.region;
                    function updateDistricts() {
                        const r = regionEl.value;
                        const districts = r && data[r] ? Object.keys(data[r]) : [];
                        populateSelect(districtEl, districts, 'Select district');
                        updateWards();
                        if (prefill.district) districtEl.value = prefill.district;
                    }
                    function updateWards() {
                        const r = regionEl.value;
                        const d = districtEl.value;
                        const wards = r && d && data[r] && data[r][d] ? data[r][d] : [];
                        populateSelect(wardEl, wards, 'Select ward');
                        if (prefill.ward) wardEl.value = prefill.ward;
                    }
                    regionEl.onchange = updateDistricts;
                    districtEl.onchange = updateWards;
                    updateDistricts();
                });
                // Tribe
                const tribeEl = document.getElementById('edit_tribe');
                populateSelect(tribeEl, tribeList, 'Select tribe');
                if (prefill.tribe) tribeEl.value = tribeList.includes(prefill.tribe) ? prefill.tribe : 'Other';
                const otherGroup = document.getElementById('edit_other_tribe_group');
                const otherInput = document.getElementById('edit_other_tribe');
                function toggleOther() {
                    const show = tribeEl.value === 'Other';
                    otherGroup.style.display = show ? '' : 'none';
                    if (!show) otherInput.value = '';
                }
                tribeEl.onchange = toggleOther;
                toggleOther();
                if (prefill.other_tribe) { otherGroup.style.display = ''; otherInput.value = prefill.other_tribe; }
            }

            // QR helper: load once and render
            let qrLibLoaded = false;
            
            // Preload QR lib early and accessibility: focus first actionable element when modals open
            ensureQrLib();
            document.getElementById('memberDetailsModal').addEventListener('shown.bs.modal', function(){
                const first = document.getElementById('btnHeaderEditPersonal') || document.getElementById('btnPrintDetails');
                first && first.focus();
            });
            
            document.getElementById('memberDetailsModal').addEventListener('hidden.bs.modal', function(){
                // Hide attendance history button when modal is closed
                const attendanceBtn = document.getElementById('btnAttendanceHistory');
                attendanceBtn.style.display = 'none';
                attendanceBtn.removeAttribute('data-member-id');
                
                const idCardBtn = document.getElementById('btnIdCard');
                idCardBtn.style.display = 'none';
                idCardBtn.removeAttribute('data-member-id');
            });

            // Set footer year
            document.getElementById('year').textContent = new Date().getFullYear();
            
            // Attendance history function
            function viewAttendanceHistory() {
                const attendanceBtn = document.getElementById('btnAttendanceHistory');
                const memberId = attendanceBtn.getAttribute('data-member-id');
                if (memberId) {
                    window.open(`{{ url('/attendance/member') }}/${memberId}/history`, '_blank');
                }
            }
            
            function viewIdCard() {
                const idCardBtn = document.getElementById('btnIdCard');
                const memberId = idCardBtn.getAttribute('data-member-id');
                if (memberId) {
                    window.open(`{{ url('/members') }}/${memberId}/identity-card`, '_blank');
                }
            }
			function ensureQrLib() {
				return new Promise((resolve) => {
					if (qrLibLoaded || window.QRCode) { qrLibLoaded = true; return resolve(); }
					const s = document.createElement('script');
					s.src = 'https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js';
					s.onload = () => { qrLibLoaded = true; resolve(); };
					document.head.appendChild(s);
				});
			}
			function renderQrToCanvas(canvasId, text, size = 96) {
				ensureQrLib().then(() => {
					setTimeout(() => {
						const c = document.getElementById(canvasId);
						if (!c || !window.QRCode) return;
						QRCode.toCanvas(c, text, { width: size, margin: 1 }, function(err) {
							if (err) {
								console.error(err);
								const holder = c.parentElement;
								if (holder) holder.innerHTML = '<span class="badge bg-warning text-dark">QR unavailable</span>';
							}
						});
					}, 50);
				});
			}

            // Build a QR data URL for embedding (avoids CORS issues when printing/PDF)
            function getQrDataUrl(text, size = 120) {
                return new Promise((resolve) => {
                    ensureQrLib().then(() => {
                        if (window.QRCode && QRCode.toDataURL) {
                            QRCode.toDataURL(text, { width: size, margin: 1 }, function(err, url){
                                if (err) { console.error(err); resolve(''); }
                                else { resolve(url || ''); }
                            });
                        } else {
                            resolve('');
                        }
                    });
                });
            }

            // Ensure global access for action handlers
            function resetPassword(memberId) {
                Swal.fire({
                    title: 'Reset Password',
                    text: 'Are you sure you want to reset this member\'s password? A new password will be generated and sent via SMS if available.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Reset Password',
                    cancelButtonText: 'Cancel',
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return fetch(`/members/${memberId}/reset-password`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (!data.success) {
                                throw new Error(data.message || 'Failed to reset password');
                            }
                            return data;
                        })
                        .catch(error => {
                            Swal.showValidationMessage(`Request failed: ${error.message}`);
                        });
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.isConfirmed && result.value) {
                        const data = result.value;
                        let message = `Password reset successfully!\n\n`;
                        message += `New Password: <strong>${data.password}</strong>\n\n`;
                        
                        if (data.sms_sent) {
                            message += `✓ SMS sent to ${data.phone_number || 'member'}`;
                        } else {
                            message += `⚠ SMS could not be sent. Please share the password manually.`;
                        }
                        
                        Swal.fire({
                            title: 'Password Reset Successful',
                            html: message,
                            icon: 'success',
                            confirmButtonText: 'Copy Password',
                            showCancelButton: true,
                            cancelButtonText: 'Close'
                        }).then((copyResult) => {
                            if (copyResult.isConfirmed) {
                                // Copy password to clipboard
                                navigator.clipboard.writeText(data.password).then(() => {
                                    Swal.fire({
                                        title: 'Copied!',
                                        text: 'Password copied to clipboard',
                                        icon: 'success',
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                }).catch(() => {
                                    // Fallback: select text
                                    const tempInput = document.createElement('input');
                                    tempInput.value = data.password;
                                    document.body.appendChild(tempInput);
                                    tempInput.select();
                                    document.execCommand('copy');
                                    document.body.removeChild(tempInput);
                                    Swal.fire({
                                        title: 'Copied!',
                                        text: 'Password copied to clipboard',
                                        icon: 'success',
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                });
                            }
                        });
                    }
                });
            }

            function restoreMember(memberId) {
                Swal.fire({
                    title: 'Restore Member',
                    text: 'Are you sure you want to restore this member? They will be moved back to active members.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Restore',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#28a745',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading state
                        Swal.fire({
                            title: 'Restoring...',
                            text: 'Please wait while we restore the member.',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        fetch(`{{ url('/members/archived') }}/${memberId}/restore`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => {
                            if (response.ok) {
                                return response.json();
                            } else if (response.status === 403) {
                                return response.json().then(data => {
                                    throw new Error(data.message || 'You do not have permission to restore members.');
                                });
                            } else {
                                return response.json().then(data => {
                                    throw new Error(data.message || `Server error: ${response.status}`);
                                }).catch(() => {
                                    throw new Error(`Server error: ${response.status}`);
                                });
                            }
                        })
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Member Restored',
                                    text: data.message || 'Member has been restored successfully.',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Restore Failed',
                                    text: data.message || 'Please try again.',
                                    confirmButtonText: 'OK'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Restore error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Restore Failed',
                                text: error.message || 'An error occurred while restoring the member.',
                                confirmButtonText: 'OK'
                            });
                        });
                    }
                });
            }

            window.viewDetails = viewDetails;
            window.openEdit = openEdit;
            window.confirmDelete = confirmDelete;
            window.resetPassword = resetPassword;
            window.restoreMember = restoreMember;
            window.switchView = switchView;
            window.printMemberDetails = printMemberDetails;
            window.downloadMemberPDF = downloadMemberPDF;
        </script>
        
        <script>
            // Initialize child form handlers when DOM is ready
            document.addEventListener('DOMContentLoaded', function() {
                // Calculate age from date of birth
                const dobInput = document.getElementById('child_date_of_birth');
                if (dobInput) {
                    dobInput.addEventListener('change', function() {
                        const dob = new Date(this.value);
                        if (!isNaN(dob.getTime())) {
                            const today = new Date();
                            let age = today.getFullYear() - dob.getFullYear();
                            const monthDiff = today.getMonth() - dob.getMonth();
                            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
                                age--;
                            }
                            const ageInput = document.getElementById('child_age');
                            if (ageInput) {
                                ageInput.value = age + ' years';
                            }
                        }
                    });
                }

                // Toggle between member and non-member parent fields
                document.querySelectorAll('input[name="parent_type"]').forEach(radio => {
                    radio.addEventListener('change', function() {
                        const memberFields = document.getElementById('memberParentFields');
                        const nonMemberFields = document.getElementById('nonMemberParentFields');
                        const memberSelect = document.getElementById('child_member_id');
                        const parentNameInput = document.getElementById('child_parent_name');
                        
                        if (this.value === 'member') {
                            if (memberFields) memberFields.style.display = 'block';
                            if (nonMemberFields) nonMemberFields.style.display = 'none';
                            if (memberSelect) memberSelect.required = true;
                            if (parentNameInput) parentNameInput.required = false;
                        } else {
                            if (memberFields) memberFields.style.display = 'none';
                            if (nonMemberFields) nonMemberFields.style.display = 'block';
                            if (memberSelect) memberSelect.required = false;
                            if (parentNameInput) parentNameInput.required = true;
                        }
                    });
                });
            });

            // Save child function
            function saveChild() {
                const form = document.getElementById('addChildForm');
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                const parentType = document.querySelector('input[name="parent_type"]:checked').value;
                const childData = {
                    full_name: document.getElementById('child_full_name').value,
                    gender: document.getElementById('child_gender').value,
                    date_of_birth: document.getElementById('child_date_of_birth').value,
                };

                if (parentType === 'member') {
                    childData.member_id = document.getElementById('child_member_id').value;
                    if (!childData.member_id) {
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Please select a parent member' });
                        return;
                    }
                } else {
                    childData.parent_name = document.getElementById('child_parent_name').value;
                    childData.parent_phone = document.getElementById('child_parent_phone').value;
                    childData.parent_relationship = document.getElementById('child_parent_relationship').value;
                    if (!childData.parent_name) {
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Please enter parent/guardian name' });
                        return;
                    }
                }

                fetch('{{ route("children.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(childData)
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => Promise.reject(err));
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire({ icon: 'success', title: 'Success', text: data.message, timer: 1500 });
                        document.getElementById('addChildForm').reset();
                        document.getElementById('child_age').value = '';
                        const modal = bootstrap.Modal.getInstance(document.getElementById('addChildModal'));
                        if (modal) modal.hide();
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Failed to save child' });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    const errorMessage = error.message || (error.errors ? JSON.stringify(error.errors) : 'An error occurred while saving the child');
                    Swal.fire({ icon: 'error', title: 'Error', text: errorMessage });
                });
            }
        </script>
        
        <script>
            // Load notifications function
            function loadNotifications() {
                fetch('{{ route("notifications.data") }}', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data && data.success) {
                            // Store data globally for event details
                            window.currentNotificationData = data;
                            
                            // Update counts
                            const eventsCountEl = document.getElementById('eventsCount');
                            const celebrationsCountEl = document.getElementById('celebrationsCount');
                            const servicesCountEl = document.getElementById('servicesCount');
                            
                            if (eventsCountEl) eventsCountEl.textContent = data.counts.events || 0;
                            if (celebrationsCountEl) celebrationsCountEl.textContent = data.counts.celebrations || 0;
                            if (servicesCountEl) servicesCountEl.textContent = data.counts.services || 0;
                            
                            // Update total notification count
                            const totalCount = data.counts.total || 0;
                            const badge = document.getElementById('notificationBadge');
                            if (badge) {
                                badge.textContent = totalCount;
                                badge.style.display = totalCount > 0 ? 'inline' : 'none';
                            }
                            
                            // Update lists
                            const eventsList = document.getElementById('eventsList');
                            if (eventsList && data.events) {
                                eventsList.innerHTML = generateEventList(data.events);
                            }
                            
                            const celebrationsList = document.getElementById('celebrationsList');
                            if (celebrationsList && data.celebrations) {
                                celebrationsList.innerHTML = generateCelebrationList(data.celebrations);
                            }
                            
                            const servicesList = document.getElementById('servicesList');
                            if (servicesList && data.services) {
                                servicesList.innerHTML = generateServiceList(data.services);
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error loading notifications:', error);
                        const badge = document.getElementById('notificationBadge');
                        if (badge) {
                            badge.textContent = '0';
                            badge.style.display = 'none';
                        }
                    });
            }
            
            // Generate HTML for events list
            function generateEventList(events) {
                if (!events || events.length === 0) {
                    return '<div class="empty-notification-state"><i class="fas fa-calendar-times"></i><span>No upcoming events</span></div>';
                }
                return events.map((event, index) => {
                    const eventDate = new Date(event.date).toLocaleDateString('en-US', {
                        weekday: 'short',
                        month: 'short',
                        day: 'numeric'
                    });
                    const timeText = event.hours_remaining !== null ? 
                        `${event.hours_remaining}h left` : 
                        `${event.days_remaining}d left`;
                    const formatTime = (timeStr) => {
                        if (!timeStr || timeStr === 'TBD') return 'TBD';
                        try {
                            if (timeStr.includes('T')) {
                                const time = new Date(timeStr);
                                return time.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
                            }
                            if (timeStr.includes(':')) {
                                const [hours, minutes] = timeStr.split(':');
                                const time = new Date();
                                time.setHours(parseInt(hours), parseInt(minutes), 0);
                                return time.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
                            }
                            return timeStr;
                        } catch (e) {
                            return 'TBD';
                        }
                    };
                    return `
                        <div class="notification-item" style="animation-delay: ${index * 0.1}s;" onclick="showEventDetails(${event.id}, 'event')">
                            <div class="notification-item-content">
                                <div class="notification-icon bg-primary"><i class="fas fa-calendar-alt"></i></div>
                                <div class="notification-details">
                                    <div class="notification-title">${event.title}</div>
                                    <div class="notification-meta">
                                        <span class="meta-item"><i class="fas fa-calendar"></i>${eventDate}</span>
                                        <span class="meta-item"><i class="fas fa-clock"></i>${formatTime(event.time)}</span>
                                    </div>
                                    <div class="notification-info">
                                        <span class="info-item"><i class="fas fa-map-marker-alt"></i>${event.venue}</span>
                                        ${event.speaker ? `<span class="info-item"><i class="fas fa-user"></i>${event.speaker}</span>` : ''}
                                    </div>
                                    <div class="notification-badge"><span class="time-badge bg-primary">${timeText}</span></div>
                                </div>
                                <div class="notification-arrow"><i class="fas fa-chevron-right"></i></div>
                            </div>
                        </div>
                    `;
                }).join('');
            }
            
            // Generate HTML for celebrations list
            function generateCelebrationList(celebrations) {
                if (!celebrations || celebrations.length === 0) {
                    return '<div class="empty-notification-state"><i class="fas fa-birthday-cake"></i><span>No upcoming celebrations</span></div>';
                }
                return celebrations.map((celebration, index) => {
                    const celebrationDate = new Date(celebration.date).toLocaleDateString('en-US', {
                        weekday: 'short',
                        month: 'short',
                        day: 'numeric'
                    });
                    const timeText = celebration.hours_remaining !== null ? 
                        `${celebration.hours_remaining}h left` : 
                        `${celebration.days_remaining}d left`;
                    const formatTime = (timeStr) => {
                        if (!timeStr || timeStr === 'TBD') return 'TBD';
                        try {
                            if (timeStr.includes('T')) {
                                const time = new Date(timeStr);
                                return time.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
                            }
                            if (timeStr.includes(':')) {
                                const [hours, minutes] = timeStr.split(':');
                                const time = new Date();
                                time.setHours(parseInt(hours), parseInt(minutes), 0);
                                return time.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
                            }
                            return timeStr;
                        } catch (e) {
                            return 'TBD';
                        }
                    };
                    return `
                        <div class="notification-item" style="animation-delay: ${index * 0.1}s;" onclick="showEventDetails(${celebration.id}, 'celebration')">
                            <div class="notification-item-content">
                                <div class="notification-icon bg-warning"><i class="fas fa-birthday-cake"></i></div>
                                <div class="notification-details">
                                    <div class="notification-title">${celebration.title}</div>
                                    <div class="notification-meta">
                                        <span class="meta-item"><i class="fas fa-user"></i>${celebration.celebrant}</span>
                                        <span class="meta-item"><i class="fas fa-calendar"></i>${celebrationDate}</span>
                                    </div>
                                    <div class="notification-info">
                                        <span class="info-item"><i class="fas fa-clock"></i>${formatTime(celebration.time)}</span>
                                        <span class="info-item"><i class="fas fa-map-marker-alt"></i>${celebration.venue}</span>
                                    </div>
                                    <div class="notification-badge"><span class="time-badge bg-warning">${timeText}</span></div>
                                </div>
                                <div class="notification-arrow"><i class="fas fa-chevron-right"></i></div>
                            </div>
                        </div>
                    `;
                }).join('');
            }
            
            // Generate HTML for services list
            function generateServiceList(services) {
                if (!services || services.length === 0) {
                    return '<div class="empty-notification-state"><i class="fas fa-church"></i><span>No upcoming services</span></div>';
                }
                return services.map((service, index) => {
                    const serviceDate = new Date(service.date).toLocaleDateString('en-US', {
                        weekday: 'short',
                        month: 'short',
                        day: 'numeric'
                    });
                    const timeText = service.hours_remaining !== null ? 
                        `${service.hours_remaining}h left` : 
                        `${service.days_remaining}d left`;
                    const formatTime = (timeStr) => {
                        if (!timeStr || timeStr === 'TBD') return 'TBD';
                        try {
                            if (timeStr.includes('T')) {
                                const time = new Date(timeStr);
                                return time.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
                            }
                            if (timeStr.includes(':')) {
                                const [hours, minutes] = timeStr.split(':');
                                const time = new Date();
                                time.setHours(parseInt(hours), parseInt(minutes), 0);
                                return time.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
                            }
                            return timeStr;
                        } catch (e) {
                            return 'TBD';
                        }
                    };
                    return `
                        <div class="notification-item" style="animation-delay: ${index * 0.1}s;" onclick="showEventDetails(${service.id}, 'service')">
                            <div class="notification-item-content">
                                <div class="notification-icon bg-success"><i class="fas fa-church"></i></div>
                                <div class="notification-details">
                                    <div class="notification-title">${service.title}</div>
                                    <div class="notification-meta">
                                        <span class="meta-item"><i class="fas fa-calendar"></i>${serviceDate}</span>
                                        <span class="meta-item"><i class="fas fa-clock"></i>${formatTime(service.time)}</span>
                                    </div>
                                    <div class="notification-info">
                                        <span class="info-item"><i class="fas fa-map-marker-alt"></i>${service.venue}</span>
                                        ${service.speaker ? `<span class="info-item"><i class="fas fa-user"></i>${service.speaker}</span>` : ''}
                                    </div>
                                    ${service.theme ? `<div class="notification-theme"><i class="fas fa-quote-left"></i>${service.theme}</div>` : ''}
                                    <div class="notification-badge"><span class="time-badge bg-success">${timeText}</span></div>
                                </div>
                                <div class="notification-arrow"><i class="fas fa-chevron-right"></i></div>
                            </div>
                        </div>
                    `;
                }).join('');
            }
            
            // Function to show event details in a modal
            function showEventDetails(id, type) {
                let modal = document.getElementById('eventDetailsModal');
                if (!modal) {
                    modal = document.createElement('div');
                    modal.id = 'eventDetailsModal';
                    modal.className = 'modal fade';
                    modal.innerHTML = `
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header bg-light">
                                    <h5 class="modal-title" id="eventDetailsTitle">Event Details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-4" id="eventDetailsBody">
                                    <div class="text-center">
                                        <div class="spinner-border" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer bg-light">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        <i class="fas fa-times me-2"></i>Close
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                    document.body.appendChild(modal);
                }
                const bsModal = new bootstrap.Modal(modal);
                bsModal.show();
                loadEventDetails(id, type);
            }
            
            // Function to load event details
            function loadEventDetails(id, type) {
                const modalBody = document.getElementById('eventDetailsBody');
                const modalTitle = document.getElementById('eventDetailsTitle');
                const titles = {
                    'event': 'Event Details',
                    'celebration': 'Celebration Details', 
                    'service': 'Service Details'
                };
                modalTitle.textContent = titles[type] || 'Details';
                modalBody.innerHTML = `
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Loading details...</p>
                    </div>
                `;
                setTimeout(() => {
                    let eventData = null;
                    if (window.currentNotificationData) {
                        if (type === 'event') {
                            eventData = window.currentNotificationData.events.find(e => e.id === id);
                        } else if (type === 'celebration') {
                            eventData = window.currentNotificationData.celebrations.find(c => c.id === id);
                        } else if (type === 'service') {
                            eventData = window.currentNotificationData.services.find(s => s.id === id);
                        }
                    }
                    const formatTime = (timeStr) => {
                        if (!timeStr || timeStr === 'TBD') return 'TBD';
                        try {
                            if (timeStr.includes('T') || /\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}/.test(timeStr)) {
                                const time = new Date(timeStr);
                                return time.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
                            }
                            if (/^\d{2}:\d{2}/.test(timeStr)) {
                                const [hours, minutes] = timeStr.split(':');
                                const d = new Date();
                                d.setHours(parseInt(hours), parseInt(minutes), 0, 0);
                                return d.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
                            }
                            return timeStr;
                        } catch (e) {
                            return 'TBD';
                        }
                    };
                    if (eventData) {
                        const eventDate = new Date(eventData.date).toLocaleDateString('en-US', {
                            weekday: 'long',
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        });
                        let timeDisplay = 'TBD';
                        if (type === 'service') {
                            const start = eventData.start_time || eventData.time;
                            const end = eventData.end_time;
                            if (start && end) {
                                timeDisplay = `${formatTime(start)} - ${formatTime(end)}`;
                            } else if (start) {
                                timeDisplay = formatTime(start);
                            } else if (eventData.time) {
                                timeDisplay = formatTime(eventData.time);
                            }
                        } else {
                            timeDisplay = eventData.time ? formatTime(eventData.time) : 'TBD';
                        }
                        modalBody.innerHTML = `
                            <div class="text-center mb-4">
                                <div class="bg-${type === 'event' ? 'primary' : type === 'celebration' ? 'warning' : 'success'} text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 100px; height: 100px;">
                                    <i class="fas fa-${type === 'event' ? 'calendar-alt' : type === 'celebration' ? 'birthday-cake' : 'church'} fa-3x"></i>
                                </div>
                                <h3 class="text-dark mb-2">${eventData.title}</h3>
                                <p class="text-muted">${type.charAt(0).toUpperCase() + type.slice(1)} Information</p>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body text-center">
                                            <i class="fas fa-calendar text-primary fa-2x mb-3"></i>
                                            <h6 class="card-title">Date</h6>
                                            <p class="card-text text-muted">${eventDate}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body text-center">
                                            <i class="fas fa-clock text-success fa-2x mb-3"></i>
                                            <h6 class="card-title">Time</h6>
                                            <p class="card-text text-muted">${timeDisplay}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body text-center">
                                            <i class="fas fa-map-marker-alt text-danger fa-2x mb-3"></i>
                                            <h6 class="card-title">Venue</h6>
                                            <p class="card-text text-muted">${eventData.venue}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body text-center">
                                            <i class="fas fa-user text-info fa-2x mb-3"></i>
                                            <h6 class="card-title">${type === 'celebration' ? 'Celebrant' : (type === 'service' ? 'Preacher' : 'Speaker')}</h6>
                                            <p class="card-text text-muted">${eventData.speaker || eventData.celebrant || 'TBD'}</p>
                                        </div>
                                    </div>
                                </div>
                                ${eventData.theme ? `
                                <div class="col-12">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body text-center">
                                            <i class="fas fa-quote-left text-warning fa-2x mb-3"></i>
                                            <h6 class="card-title">Theme</h6>
                                            <p class="card-text text-muted">${eventData.theme}</p>
                                        </div>
                                    </div>
                                </div>
                                ` : ''}
                                <div class="col-12">
                                    <div class="alert alert-${type === 'event' ? 'primary' : type === 'celebration' ? 'warning' : 'success'} border-0">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-info-circle fa-2x me-3"></i>
                                            <div>
                                                <h6 class="mb-1">Time Remaining</h6>
                                                <p class="mb-0">
                                                    ${eventData.hours_remaining !== null ? 
                                                        `${eventData.hours_remaining} hours left` : 
                                                        `${eventData.days_remaining} days left`}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    } else {
                        modalBody.innerHTML = `
                            <div class="text-center py-4 text-muted">Details not found.</div>
                        `;
                    }
                }, 50);
            }
            
            // Load notifications on page load
            document.addEventListener('DOMContentLoaded', function() {
                loadNotifications();
                // Refresh notifications every 5 minutes
                setInterval(loadNotifications, 300000);
                
                // Handle mobile dropdown positioning
                const notificationDropdown = document.getElementById('notificationDropdown');
                if (notificationDropdown) {
                    const dropdownMenu = notificationDropdown.querySelector('.notification-dropdown');
                    if (dropdownMenu) {
                        // Function to apply mobile positioning
                        function applyMobilePositioning() {
                            if (window.innerWidth <= 576) {
                                dropdownMenu.style.setProperty('position', 'fixed', 'important');
                                dropdownMenu.style.setProperty('top', '60px', 'important');
                                dropdownMenu.style.setProperty('left', '0.25rem', 'important');
                                dropdownMenu.style.setProperty('right', '0.25rem', 'important');
                                dropdownMenu.style.setProperty('width', 'calc(100vw - 0.5rem)', 'important');
                                dropdownMenu.style.setProperty('max-width', 'calc(100vw - 0.5rem)', 'important');
                                dropdownMenu.style.setProperty('margin', '0', 'important');
                                dropdownMenu.style.setProperty('transform', 'none', 'important');
                                dropdownMenu.style.setProperty('z-index', '1055', 'important');
                                dropdownMenu.style.setProperty('inset', '60px 0.25rem auto 0.25rem', 'important');
                            } else if (window.innerWidth <= 768) {
                                dropdownMenu.style.setProperty('position', 'fixed', 'important');
                                dropdownMenu.style.setProperty('top', '60px', 'important');
                                dropdownMenu.style.setProperty('left', '0.5rem', 'important');
                                dropdownMenu.style.setProperty('right', '0.5rem', 'important');
                                dropdownMenu.style.setProperty('width', 'calc(100vw - 1rem)', 'important');
                                dropdownMenu.style.setProperty('max-width', 'calc(100vw - 1rem)', 'important');
                                dropdownMenu.style.setProperty('margin', '0', 'important');
                                dropdownMenu.style.setProperty('transform', 'none', 'important');
                                dropdownMenu.style.setProperty('z-index', '1055', 'important');
                                dropdownMenu.style.setProperty('inset', '60px 0.5rem auto 0.5rem', 'important');
                            } else {
                                // Desktop - reset styles
                                dropdownMenu.style.removeProperty('position');
                                dropdownMenu.style.removeProperty('top');
                                dropdownMenu.style.removeProperty('left');
                                dropdownMenu.style.removeProperty('right');
                                dropdownMenu.style.removeProperty('width');
                                dropdownMenu.style.removeProperty('max-width');
                                dropdownMenu.style.removeProperty('margin');
                                dropdownMenu.style.removeProperty('transform');
                                dropdownMenu.style.removeProperty('z-index');
                                dropdownMenu.style.removeProperty('inset');
                            }
                        }
                        
                        // Handle dropdown show event for mobile positioning
                        notificationDropdown.addEventListener('show.bs.dropdown', function() {
                            setTimeout(applyMobilePositioning, 10);
                        });
                        
                        // Also handle after shown to ensure positioning
                        notificationDropdown.addEventListener('shown.bs.dropdown', function() {
                            applyMobilePositioning();
                            // Use MutationObserver to watch for Bootstrap's style changes
                            const observer = new MutationObserver(function(mutations) {
                                mutations.forEach(function(mutation) {
                                    if (mutation.type === 'attributes' && mutation.attributeName === 'style') {
                                        applyMobilePositioning();
                                    }
                                });
                            });
                            observer.observe(dropdownMenu, { attributes: true, attributeFilter: ['style'] });
                            // Disconnect observer after dropdown is hidden
                            notificationDropdown.addEventListener('hide.bs.dropdown', function() {
                                observer.disconnect();
                            }, { once: true });
                        });
                        
                        // Handle window resize
                        window.addEventListener('resize', function() {
                            if (window.innerWidth > 768) {
                                // Reset to desktop styles
                                dropdownMenu.style.position = '';
                                dropdownMenu.style.top = '';
                                dropdownMenu.style.left = '';
                                dropdownMenu.style.right = '';
                                dropdownMenu.style.width = '';
                                dropdownMenu.style.maxWidth = '';
                                dropdownMenu.style.transform = '';
                                dropdownMenu.style.zIndex = '';
                            }
                        });
                        
                        // Handle dropdown hide to reset styles
                        notificationDropdown.addEventListener('hide.bs.dropdown', function() {
                            if (window.innerWidth > 768) {
                                dropdownMenu.style.position = '';
                                dropdownMenu.style.top = '';
                                dropdownMenu.style.left = '';
                                dropdownMenu.style.right = '';
                                dropdownMenu.style.width = '';
                                dropdownMenu.style.maxWidth = '';
                                dropdownMenu.style.transform = '';
                                dropdownMenu.style.zIndex = '';
                            }
                        });
                    }
                }
                
                // Ensure dropdowns close properly - close one when the other opens
                const profileDropdown = document.getElementById('navbarDropdown');
                const notificationDropdownEl = document.getElementById('notificationDropdown');
                
                if (notificationDropdownEl && profileDropdown) {
                    // Close profile dropdown when notification opens
                    notificationDropdownEl.addEventListener('show.bs.dropdown', function() {
                        // Use Bootstrap API to close profile dropdown
                        if (typeof bootstrap !== 'undefined') {
                            const profileDropdownInstance = bootstrap.Dropdown.getInstance(profileDropdown);
                            if (profileDropdownInstance) {
                                profileDropdownInstance.hide();
                            }
                        }
                    });
                    
                    // Close notification dropdown when profile opens
                    profileDropdown.addEventListener('show.bs.dropdown', function() {
                        // Use Bootstrap API to close notification dropdown
                        if (typeof bootstrap !== 'undefined') {
                            const notificationDropdownInstance = bootstrap.Dropdown.getInstance(notificationDropdownEl);
                            if (notificationDropdownInstance) {
                                notificationDropdownInstance.hide();
                            }
                        }
                    });
                }
            });
            
            // Update date and time display
            function updateDateTime() {
                const now = new Date();
                const dateElement = document.getElementById('currentDate');
                const timeElement = document.getElementById('currentTime');
                
                if (dateElement) {
                    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                    dateElement.textContent = now.toLocaleDateString('en-US', options);
                }
                
                if (timeElement) {
                    const hours = now.getHours().toString().padStart(2, '0');
                    const minutes = now.getMinutes().toString().padStart(2, '0');
                    const seconds = now.getSeconds().toString().padStart(2, '0');
                    timeElement.textContent = `${hours}:${minutes}:${seconds}`;
                }
            }
            
            // Update date and time immediately and then every second
            document.addEventListener('DOMContentLoaded', function() {
                updateDateTime();
                setInterval(updateDateTime, 1000);
            });
            
            // Toggle sidebar functionality
            document.addEventListener('DOMContentLoaded', function() {
                const sidebarToggle = document.getElementById('sidebarToggle');
                const layoutSidenav = document.getElementById('layoutSidenav');
                
                if (sidebarToggle) {
                    sidebarToggle.onclick = null;
                    sidebarToggle.removeAttribute('onclick');
                    
                    if (!sidebarToggle.hasAttribute('data-layout-toggle-handler')) {
                        sidebarToggle.setAttribute('data-layout-toggle-handler', 'true');
                        
                        sidebarToggle.addEventListener('click', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            
                            if (layoutSidenav) {
                                layoutSidenav.classList.toggle('sb-sidenav-toggled');
                            }
                            document.body.classList.toggle('sb-sidenav-toggled');
                            
                            const isToggled = layoutSidenav ? layoutSidenav.classList.contains('sb-sidenav-toggled') : document.body.classList.contains('sb-sidenav-toggled');
                            localStorage.setItem('sb-sidebar-toggle', isToggled ? 'true' : 'false');
                            
                            return false;
                        }, true);
                        
                        const savedState = localStorage.getItem('sb-sidebar-toggle');
                        if (savedState === 'true') {
                            if (layoutSidenav) {
                                layoutSidenav.classList.add('sb-sidenav-toggled');
                            }
                            document.body.classList.add('sb-sidenav-toggled');
                        }
                    }
                }
            });
            
            // Make functions globally available
            window.showEventDetails = showEventDetails;
            window.loadNotifications = loadNotifications;
        </script>
    </body>
</html>