@php
    use App\Services\SettingsService;
    try {
        $themeColor = SettingsService::get('theme_color', 'waumini');
        $sidebarStyle = SettingsService::get('sidebar_style', 'dark');
    } catch (\Exception $e) {
        // Fallback to defaults if settings can't be loaded
        $themeColor = 'waumini';
        $sidebarStyle = 'dark';
    }
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
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
        <!-- SweetAlert Helpers -->
        <script src="{{ asset('js/sweetalert-helpers.js') }}"></script>
        <style>
            /* Global Century Gothic Font */
            * {
                font-family: "Century Gothic", "CenturyGothic", "AppleGothic", Arial, sans-serif !important;
            }
            
            html, body {
                font-family: "Century Gothic", "CenturyGothic", "AppleGothic", Arial, sans-serif !important;
            }
            
            /* Prevent horizontal scrolling - minimal approach */
            @media (max-width: 768px) {
                html {
                    overflow-x: hidden !important;
                }
                
                body {
                    overflow-x: hidden !important;
                    position: relative !important;
                }
                
                /* Only fix elements that actually cause overflow */
                #layoutSidenav_content {
                    overflow-x: hidden !important;
                }
                
                /* Navbar should not overflow */
                .sb-topnav {
                    overflow-x: hidden !important;
                }
                
                /* Tables should scroll internally, not cause page scroll */
                .table-responsive {
                    overflow-x: auto !important;
                    -webkit-overflow-scrolling: touch;
                }
                
                /* Media elements */
                img, video, iframe {
                    max-width: 100% !important;
                    height: auto !important;
                }
            }
            
            /* Dynamic theme color application */
            @php
                $themeColors = [
                    'waumini' => '#940000',
                    'primary' => '#940000',
                    'secondary' => '#b30000',
                    'success' => '#28a745',
                    'danger' => '#dc3545',
                    'warning' => '#ffc107',
                    'info' => '#36b9cc'
                ];
                $selectedColor = $themeColors['waumini'];
                // Use a different color for cards/buttons, but keep sidebar as #17082d
                $cardColor = '#940000'; // Red color for cards
            @endphp
            
            /* Apply card color to primary elements (cards, buttons) */
            .btn-primary,
            .bg-primary,
            .card-header.bg-primary {
                background-color: {{ $cardColor }} !important;
                border-color: {{ $cardColor }} !important;
                color: white !important;
            }
            
            .btn-outline-primary {
                border-color: {{ $cardColor }} !important;
                color: {{ $cardColor }} !important;
            }
            
            .btn-outline-primary:hover {
                background-color: {{ $cardColor }} !important;
                color: white !important;
            }
            
            .text-primary {
                color: {{ $cardColor }} !important;
            }
            
            .border-primary {
                border-color: {{ $cardColor }} !important;
            }
            
            /* Sidebar theme color - keep original #17082d */
            .sb-sidenav {
                background: linear-gradient(180deg, #fff5f5 0%, #ffe0e0 100%) !important;
            }

            .sb-sidenav .nav-link {
                color: #000000 !important;
            }

            .sb-sidenav .nav-link:hover {
                background-color: #ffe0e0 !important;
                color: #940000 !important; 
            }

            .sb-sidenav .sb-sidenav-menu-heading {
                color: #ffffff !important;
                background-color: #940000 !important;
                margin: 0.5rem 0.5rem 0.25rem 0.5rem !important;
                padding: 0.75rem 1rem !important;
                border-radius: 4px !important;
                font-weight: 700 !important;
                letter-spacing: 0.5px !important;
            }
            
            /* Top navigation bar style based on sidebar_style setting */
            @if($sidebarStyle === 'light')
                .sb-topnav {
                    background-color: #f8f9fa !important;
                    border-bottom: 1px solid #dee2e6 !important;
                }
                .sb-topnav .navbar-text,
                .sb-topnav .navbar-brand,
                .sb-topnav .nav-link,
                .sb-topnav .navbar-text strong {
                    color: #212529 !important;
                    text-shadow: none !important;
                }
                .sb-topnav .btn-link {
                    color: #212529 !important;
                }
            @else
                .sb-topnav {
                    background-color: #000000 !important;
                }
            @endif
            
            /* Ensure card headers are visible */
            .card-header.bg-primary {
                padding: 0.75rem 1.25rem !important;
                border-bottom: 1px solid rgba(255,255,255,0.2) !important;
            }
            .card-header.bg-primary .badge {
                background-color: #f8f9fa !important;
                color: #940000 !important;
            }
            .card-header.bg-primary i {
                color: white !important;
            }
            .card-header.bg-primary strong {
                color: white !important;
                font-weight: 600 !important;
            }
            
            /* Ensure all card headers have proper visibility */
            .card-header.bg-primary {
                background-color: #940000 !important;
                background-image: none !important;
            }
            
            .report-header-primary {
                background: linear-gradient(135deg, #940000 0%, #7a0000 100%) !important;
                color: #fff !important;
            }

            .report-header-neutral {
                background: linear-gradient(135deg, #6c757d 0%, #495057 100%) !important;
                color: #fff !important;
            }
            
            .text-primary {
                color: #940000 !important;
            }
            
            .badge-primary, .bg-primary {
                background-color: #940000 !important;
            }
            
            .btn-primary {
                background-color: #940000 !important;
                border-color: #940000 !important;
            }
            .card-header:not(.bg-primary):not(.bg-success):not(.bg-info):not(.bg-warning):not(.bg-danger):not(.bg-secondary):not(.report-header-primary):not(.report-header-success):not(.report-header-info):not(.report-header-warning):not(.report-header-neutral) {
                min-height: 3rem !important;
                display: flex !important;
                align-items: center !important;
                position: relative !important;
                z-index: 1 !important;
                background-color: #f8f9fa !important;
                color: #495057 !important;
                border-bottom: 1px solid #dee2e6 !important;
                padding: 0.75rem 1.25rem !important;
            }
            
            /* Ensure card header text and icons are visible for default headers */
            .card-header:not(.bg-primary):not(.bg-success):not(.bg-info):not(.bg-warning):not(.bg-danger):not(.bg-secondary):not(.report-header-primary):not(.report-header-success):not(.report-header-info):not(.report-header-warning):not(.report-header-neutral) h5,
            .card-header:not(.bg-primary):not(.bg-success):not(.bg-info):not(.bg-warning):not(.bg-danger):not(.bg-secondary):not(.report-header-primary):not(.report-header-success):not(.report-header-info):not(.report-header-warning):not(.report-header-neutral) h6,
            .card-header:not(.bg-primary):not(.bg-success):not(.bg-info):not(.bg-warning):not(.bg-danger):not(.bg-secondary):not(.report-header-primary):not(.report-header-success):not(.report-header-info):not(.report-header-warning):not(.report-header-neutral) .mb-0 {
                position: relative !important;
                z-index: 2 !important;
                color: #495057 !important;
                font-weight: 600 !important;
                margin-bottom: 0 !important;
            }
            
            .card-header:not(.bg-primary):not(.bg-success):not(.bg-info):not(.bg-warning):not(.bg-danger):not(.bg-secondary):not(.report-header-primary):not(.report-header-success):not(.report-header-info):not(.report-header-warning):not(.report-header-neutral) i {
                position: relative !important;
                z-index: 2 !important;
                color: #6c757d !important;
            }
            
            /* Card headers with colored backgrounds - ensure they maintain their colors */
            .card-header.bg-primary,
            .card-header.bg-success,
            .card-header.bg-info,
            .card-header.bg-warning,
            .card-header.bg-danger,
            .card-header.bg-secondary {
                min-height: 3rem !important;
                display: flex !important;
                align-items: center !important;
                position: relative !important;
                z-index: 1 !important;
                padding: 0.75rem 1.25rem !important;
            }
            
            /* Ensure colored card headers keep their background colors */
            .card-header.bg-primary {
                background-color: #940000 !important;
            }
            .card-header.bg-success {
                background-color: #198754 !important;
            }
            .card-header.bg-info {
                background-color: #0dcaf0 !important;
            }
            .card-header.bg-warning {
                background-color: #ffc107 !important;
            }
            .card-header.bg-danger {
                background-color: #dc3545 !important;
            }
            .card-header.bg-secondary {
                background-color: #6c757d !important;
            }
            
            .card-header.bg-primary h5,
            .card-header.bg-primary h6,
            .card-header.bg-primary strong,
            .card-header.bg-success h5,
            .card-header.bg-success h6,
            .card-header.bg-success strong,
            .card-header.bg-info h5,
            .card-header.bg-info h6,
            .card-header.bg-info strong,
            .card-header.bg-warning h5,
            .card-header.bg-warning h6,
            .card-header.bg-warning strong,
            .card-header.bg-danger h5,
            .card-header.bg-danger h6,
            .card-header.bg-danger strong,
            .card-header.bg-secondary h5,
            .card-header.bg-secondary h6,
            .card-header.bg-secondary strong {
                position: relative !important;
                z-index: 2 !important;
                color: white !important;
                font-weight: 600 !important;
            }
            
            .card-header.bg-primary i,
            .card-header.bg-success i,
            .card-header.bg-info i,
            .card-header.bg-warning i,
            .card-header.bg-danger i,
            .card-header.bg-secondary i {
                position: relative !important;
                z-index: 2 !important;
                color: white !important;
            }
            
            /* Text white class for colored headers - ensure all child elements are white */
            .card-header.bg-primary.text-white,
            .card-header.bg-success.text-white,
            .card-header.bg-info.text-white,
            .card-header.bg-warning.text-white,
            .card-header.bg-danger.text-white,
            .card-header.bg-secondary.text-white {
                background: linear-gradient(135deg, #940000 0%, #7a0000 100%) !important;
                color: white !important;
                border-bottom: none !important;
            }
            
            .card-header.bg-primary.text-white *,
            .card-header.bg-success.text-white *,
            .card-header.bg-info.text-white *,
            .card-header.bg-warning.text-white *,
            .card-header.bg-danger.text-white *,
            .card-header.bg-secondary.text-white *,
            .card-header.bg-primary.text-white strong,
            .card-header.bg-success.text-white strong,
            .card-header.bg-info.text-white strong,
            .card-header.bg-warning.text-white strong,
            .card-header.bg-danger.text-white strong,
            .card-header.bg-secondary.text-white strong {
                color: white !important;
            }
            
            /* Badges on colored headers - ensure visibility */
            .card-header.bg-primary .badge,
            .card-header.bg-success .badge,
            .card-header.bg-info .badge,
            .card-header.bg-warning .badge,
            .card-header.bg-danger .badge,
            .card-header.bg-secondary .badge {
                position: relative !important;
                z-index: 2 !important;
            }
            
            .card-header.bg-primary .badge.bg-white,
            .card-header.bg-success .badge.bg-white,
            .card-header.bg-info .badge.bg-white,
            .card-header.bg-warning .badge.bg-white,
            .card-header.bg-danger .badge.bg-white,
            .card-header.bg-secondary .badge.bg-white {
                background-color: white !important;
                color: #940000 !important;
                font-weight: 700 !important;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
            }
            
            /* Custom btn-white class for better visibility on colored backgrounds */
            .btn-white {
                background-color: white !important;
                color: #940000 !important;
                border: 1px solid rgba(0,0,0,0.1) !important;
            }
            
            .btn-white:hover {
                background-color: #f8f9fa !important;
                color: #0a58ca !important;
                border-color: rgba(0,0,0,0.2) !important;
            }
            
            /* Buttons on colored headers - ensure visibility */
            .card-header.bg-primary .btn,
            .card-header.bg-success .btn,
            .card-header.bg-info .btn,
            .card-header.bg-warning .btn,
            .card-header.bg-danger .btn,
            .card-header.bg-secondary .btn {
                position: relative !important;
                z-index: 2 !important;
            }
            
            .card-header.bg-primary .btn-white,
            .card-header.bg-success .btn-white,
            .card-header.bg-info .btn-white,
            .card-header.bg-warning .btn-white,
            .card-header.bg-danger .btn-white,
            .card-header.bg-secondary .btn-white {
                background-color: white !important;
                color: #940000 !important;
                border-color: rgba(255,255,255,0.3) !important;
                font-weight: 600 !important;
            }
            
            .card-header.bg-primary .btn-white:hover,
            .card-header.bg-success .btn-white:hover,
            .card-header.bg-info .btn-white:hover,
            .card-header.bg-warning .btn-white:hover,
            .card-header.bg-danger .btn-white:hover,
            .card-header.bg-secondary .btn-white:hover {
                background-color: rgba(255,255,255,0.9) !important;
                color: #0d6efd !important;
            }
            
            /* Dropdown menus in colored headers - ensure text is visible */
            .card-header.bg-primary .dropdown-menu,
            .card-header.bg-success .dropdown-menu,
            .card-header.bg-info .dropdown-menu,
            .card-header.bg-warning .dropdown-menu,
            .card-header.bg-danger .dropdown-menu,
            .card-header.bg-secondary .dropdown-menu {
                background-color: white !important;
                border: 1px solid rgba(0,0,0,0.15) !important;
                box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15) !important;
            }
            
            .card-header.bg-primary .dropdown-item,
            .card-header.bg-success .dropdown-item,
            .card-header.bg-info .dropdown-item,
            .card-header.bg-warning .dropdown-item,
            .card-header.bg-danger .dropdown-item,
            .card-header.bg-secondary .dropdown-item {
                color: #212529 !important;
            }
            
            .card-header.bg-primary .dropdown-item:hover,
            .card-header.bg-success .dropdown-item:hover,
            .card-header.bg-info .dropdown-item:hover,
            .card-header.bg-warning .dropdown-item:hover,
            .card-header.bg-danger .dropdown-item:hover,
            .card-header.bg-secondary .dropdown-item:hover {
                color: #1e2125 !important;
                background-color: #e9ecef !important;
            }
            
            .card-header.bg-primary .dropdown-item i,
            .card-header.bg-success .dropdown-item i,
            .card-header.bg-info .dropdown-item i,
            .card-header.bg-warning .dropdown-item i,
            .card-header.bg-danger .dropdown-item i,
            .card-header.bg-secondary .dropdown-item i {
                color: #0d6efd !important;
            }
            
            /* Fix report header overlays */
            .report-header-primary,
            .report-header-success,
            .report-header-info,
            .report-header-warning,
            .report-header-neutral {
                position: relative !important;
                z-index: 1 !important;
            }
            
            .report-header-primary::before,
            .report-header-success::before,
            .report-header-info::before,
            .report-header-warning::before,
            .report-header-neutral::before {
                position: absolute !important;
                inset: 0 !important;
                z-index: 0 !important;
                border-top-left-radius: inherit !important;
                border-top-right-radius: inherit !important;
            }
            
            .report-header-primary h5,
            .report-header-primary h6,
            .report-header-primary strong,
            .report-header-primary .mb-0,
            .report-header-success h5,
            .report-header-success h6,
            .report-header-success strong,
            .report-header-success .mb-0,
            .report-header-info h5,
            .report-header-info h6,
            .report-header-info strong,
            .report-header-info .mb-0,
            .report-header-warning h5,
            .report-header-warning h6,
            .report-header-warning strong,
            .report-header-warning .mb-0,
            .report-header-neutral h5,
            .report-header-neutral h6,
            .report-header-neutral strong,
            .report-header-neutral .mb-0 {
                position: relative !important;
                z-index: 2 !important;
                color: white !important;
            }
            
            .report-header-primary i,
            .report-header-success i,
            .report-header-info i,
            .report-header-warning i,
            .report-header-neutral i {
                position: relative !important;
                z-index: 2 !important;
                color: white !important;
            }
            
            /* Fix card headers inside modals */
            .modal .card-header.bg-light {
                background-color: #f8f9fa !important;
                color: #495057 !important;
                border-bottom: 1px solid #dee2e6 !important;
                padding: 0.75rem 1.25rem !important;
            }
            .modal .card-header.bg-light h6 {
                color: #495057 !important;
                font-weight: 600 !important;
                margin-bottom: 0 !important;
            }
            .modal .card-header.bg-light i {
                color: #6c757d !important;
            }
            
            /* Ensure modal card headers are visible */
            .modal .card-header {
                background-color: #f8f9fa !important;
                color: #495057 !important;
                border-bottom: 1px solid #dee2e6 !important;
                min-height: 3rem !important;
                display: flex !important;
                align-items: center !important;
                padding: 0.75rem 1.25rem !important;
            }
            
            /* Additional modal card header styling for better visibility */
            .modal .card {
                box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
                border: 1px solid #dee2e6 !important;
            }
            .modal .card-header h6 {
                font-size: 0.95rem !important;
                font-weight: 600 !important;
                color: #495057 !important;
            }
            .modal .card-header i {
                font-size: 0.9rem !important;
                color: #6c757d !important;
            }
            
            .logo-white-section {
                background-color: white !important;
                border-radius: 0;
                margin: 8px 0;
                padding: 8px 16px !important;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                transition: all 0.3s ease;
                display: flex !important;
                justify-content: center !important;
                align-items: center !important;
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
                justify-content: center !important;
            }
            
            /* Notification Dropdown Styles */
            .notification-dropdown {
                border: none;
                box-shadow: 0 10px 30px rgba(0,0,0,0.2);
                border-radius: 15px;
                padding: 0;
            }
            
            .notification-section {
                padding: 15px;
            }
            
            .notification-item {
                padding: 10px;
                margin: 5px 0;
                border-radius: 8px;
                border-left: 4px solid #007bff;
                background: #f8f9fa;
                cursor: pointer;
                transition: all 0.3s ease;
            }
            
            .notification-item:hover {
                background: #e9ecef;
                transform: translateX(5px);
            }
            
            .notification-item.events {
                border-left-color: #007bff;
            }
            
            .notification-item.celebrations {
                border-left-color: #ffc107;
            }
            
            .notification-item.services {
                border-left-color: #28a745;
            }
            
            .notification-item .days-remaining {
                font-size: 0.8rem;
                font-weight: bold;
                padding: 2px 8px;
                border-radius: 12px;
            }
            
            .days-remaining.urgent {
                background: #dc3545;
                color: white;
            }
            
            .days-remaining.warning {
                background: #ffc107;
                color: #212529;
            }
            
            .days-remaining.normal {
                background: #28a745;
                color: white;
            }
            
            .notification-item .item-title {
                font-weight: 600;
                margin-bottom: 5px;
                color: #333;
            }
            
            .notification-item .item-details {
                font-size: 0.85rem;
                color: #666;
                margin-bottom: 5px;
            }
            
            .notification-item .item-date {
                font-size: 0.8rem;
                color: #999;
            }
            
            #notificationBadge {
                animation: pulse 2s infinite;
            }
            
            @keyframes pulse {
                0% { transform: scale(1); }
                50% { transform: scale(1.1); }
                100% { transform: scale(1); }
            }
            
            /* Custom sidebar styling */
            .sb-sidenav {
                background: linear-gradient(180deg, #fff5f5 0%, #ffe0e0 100%) !important;
            }
            
            .sb-sidenav .nav-link {
                color: #000000 !important;
                transition: all 0.3s ease;
            }
            
            .sb-sidenav .nav-link:hover {
                background-color: #293846 !important;
                color: white !important;
            }
            
            .sb-sidenav .sb-sidenav-menu-heading {
                color: #ffffff !important;
                font-weight: 700 !important;
                text-transform: uppercase !important;
                letter-spacing: 0.5px !important;
                font-size: 0.75rem !important;
                padding: 0.75rem 1rem !important;
                background-color: #940000 !important;
                border-radius: 4px !important;
                margin: 0.5rem 0.5rem 0.25rem 0.5rem !important;
            }
            
            .sb-sidenav .sb-nav-link-icon {
                color: #000000 !important;
            }
            
            .sb-sidenav .sb-sidenav-collapse-arrow {
                color: #000000 !important;
            }
            
            /* Ensure all sidebar text is visible */
        .sb-sidenav * {
            color: inherit !important;
        }
        
        /* Notification dropdown styling */
        .notification-list {
            max-height: 300px;
            overflow-y: auto;
        }
        
        .notification-item {
            transition: background-color 0.2s ease;
        }
        
        .notification-item:hover {
            background-color: #f8f9fa;
        }
        
        .notification-item:last-child {
            border-bottom: none !important;
        }
        
        .notification-item h6 {
            font-size: 0.9rem;
            line-height: 1.2;
        }
        
        .notification-item p {
            font-size: 0.8rem;
            line-height: 1.3;
        }
        
        .notification-item .badge {
            font-size: 0.7rem;
        }
        
        .notification-item:hover {
            background-color: #f8f9fa !important;
            transform: translateX(2px);
            transition: all 0.2s ease;
        }
        
        .notification-item:hover .fas.fa-chevron-right {
            color: #007bff !important;
        }
            
            .sb-sidenav .nav-link {
                color: #000000 !important;
                font-weight: 700 !important;
            }
            
            .sb-sidenav .nav-link:hover {
                color: #940000 !important;
                background-color: #ffe0e0 !important;
            }
            
            .sb-sidenav .sb-sidenav-footer {
                background-color: #ffe0e0 !important;
                color: #000000 !important;
            }
            
            /* Card header titles styling */
            .card-header {
                color: #000000 !important;
                font-weight: 600;
            }
            
            /* Statistics card labels styling */
            .card .small.text-white-50 {
                color: #000000 !important;
                font-weight: 700;
            }
            
            /* Select2 styling */
            .select2-container--default .select2-selection--single {
                height: 38px;
                border: 1px solid #ced4da;
                border-radius: 0.375rem;
            }
            
            .select2-container--default .select2-selection--single .select2-selection__rendered {
                line-height: 36px;
                padding-left: 12px;
            }
            
            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 36px;
            }
            
            .select2-dropdown {
                border: 1px solid #ced4da;
                border-radius: 0.375rem;
            }
            
            .select2-container--default .select2-results__option--highlighted[aria-selected] {
                background-color: #0d6efd;
            }
            
            /* Custom member search dropdown styling */
            .member-option:hover {
                background-color: #f8f9fa;
            }
            
            .member-option:last-child {
                border-bottom: none !important;
            }
            
            #member_dropdown {
                box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            }
            
            /* Enhanced Notification Dropdown Styles */
            .notification-dropdown {
                background: white !important;
                border: none !important;
                box-shadow: 0 20px 60px rgba(0,0,0,0.15) !important;
                border-radius: 16px !important;
                overflow: hidden !important;
            }
            
            /* Desktop styles - ensure normal behavior */
            @media (min-width: 769px) {
                .notification-dropdown {
                    width: 400px !important;
                    max-width: 400px !important;
                    position: absolute !important;
                    left: auto !important;
                    right: 0 !important;
                    transform: none !important;
                    margin: 0 !important;
                }
            }
            
            /* Mobile - override Bootstrap's dropdown-menu-end positioning */
            @media (max-width: 768px) {
                #notificationDropdown .dropdown-menu-end.notification-dropdown,
                #notificationDropdown.show .dropdown-menu-end.notification-dropdown {
                    left: 0.5rem !important;
                    right: 0.5rem !important;
                    transform: none !important;
                    position: fixed !important;
                    inset: 60px 0.5rem auto 0.5rem !important;
                    width: calc(100vw - 1rem) !important;
                    max-width: calc(100vw - 1rem) !important;
                }
            }
            
            @media (max-width: 576px) {
                #notificationDropdown .dropdown-menu-end.notification-dropdown,
                #notificationDropdown.show .dropdown-menu-end.notification-dropdown {
                    left: 0.25rem !important;
                    right: 0.25rem !important;
                    transform: none !important;
                    position: fixed !important;
                    inset: 60px 0.25rem auto 0.25rem !important;
                    width: calc(100vw - 0.5rem) !important;
                    max-width: calc(100vw - 0.5rem) !important;
                }
            }
            
            .notification-content {
                scrollbar-width: thin;
                scrollbar-color: #667eea #f1f1f1;
            }
            
            .notification-content::-webkit-scrollbar {
                width: 6px;
            }
            
            .notification-content::-webkit-scrollbar-track {
                background: #f1f1f1;
                border-radius: 3px;
            }
            
            .notification-content::-webkit-scrollbar-thumb {
                background: linear-gradient(135deg, #667eea, #764ba2);
                border-radius: 3px;
            }
            
            .notification-content::-webkit-scrollbar-thumb:hover {
                background: linear-gradient(135deg, #5a6fd8, #6a4190);
            }
            
            .notification-count-badge {
                background: linear-gradient(135deg, #667eea, #764ba2) !important;
                color: white !important;
                padding: 0.25rem 0.75rem !important;
                border-radius: 20px !important;
                font-size: 0.75rem !important;
                font-weight: 600 !important;
                min-width: 24px !important;
                text-align: center !important;
            }
            
            .notification-item {
                margin-bottom: 0.5rem;
                border-radius: 12px;
                background: white;
                border: 1px solid #e9ecef;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                cursor: pointer;
                overflow: hidden;
                animation: slideInUp 0.6s ease-out;
                opacity: 0;
                animation-fill-mode: forwards;
            }
            
            .notification-item:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(102, 126, 234, 0.15);
                border-color: #667eea;
            }
            
            .notification-item-content {
                display: flex;
                align-items: center;
                padding: 1rem;
                gap: 1rem;
            }
            
            .notification-icon {
                width: 48px;
                height: 48px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 1.1rem;
                flex-shrink: 0;
                transition: all 0.3s ease;
            }
            
            .notification-item:hover .notification-icon {
                transform: scale(1.1) rotate(5deg);
            }
            
            .notification-details {
                flex: 1;
                min-width: 0;
            }
            
            .notification-title {
                font-size: 0.95rem;
                font-weight: 700;
                color: #333;
                margin-bottom: 0.5rem;
                line-height: 1.3;
            }
            
            .notification-meta {
                display: flex;
                gap: 1rem;
                margin-bottom: 0.5rem;
                flex-wrap: wrap;
            }
            
            .meta-item {
                display: flex;
                align-items: center;
                gap: 0.25rem;
                font-size: 0.8rem;
                color: #6c757d;
                font-weight: 500;
            }
            
            .meta-item i {
                font-size: 0.7rem;
                color: #667eea;
            }
            
            .notification-info {
                display: flex;
                gap: 1rem;
                margin-bottom: 0.5rem;
                flex-wrap: wrap;
            }
            
            .info-item {
                display: flex;
                align-items: center;
                gap: 0.25rem;
                font-size: 0.8rem;
                color: #6c757d;
                font-weight: 500;
            }
            
            .info-item i {
                font-size: 0.7rem;
                color: #667eea;
            }
            
            .notification-theme {
                font-size: 0.8rem;
                color: #667eea;
                font-style: italic;
                margin-bottom: 0.5rem;
                padding: 0.25rem 0.5rem;
                background: rgba(102, 126, 234, 0.1);
                border-radius: 6px;
                border-left: 3px solid #667eea;
            }
            
            .notification-theme i {
                margin-right: 0.25rem;
            }
            
            .notification-badge {
                margin-top: 0.5rem;
            }
            
            .time-badge {
                color: white !important;
                padding: 0.25rem 0.75rem !important;
                border-radius: 15px !important;
                font-size: 0.75rem !important;
                font-weight: 600 !important;
                text-transform: uppercase !important;
                letter-spacing: 0.5px !important;
            }
            
            .notification-arrow {
                color: #6c757d;
                font-size: 0.9rem;
                transition: all 0.3s ease;
                flex-shrink: 0;
            }
            
            .notification-item:hover .notification-arrow {
                color: #667eea;
                transform: translateX(3px);
            }
            
            .empty-notification-state {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 2rem 1rem;
                color: #6c757d;
                text-align: center;
            }
            
            .empty-notification-state i {
                font-size: 2rem;
                margin-bottom: 0.5rem;
                opacity: 0.5;
            }
            
            .empty-notification-state span {
                font-size: 0.9rem;
                font-weight: 500;
            }
            
            /* Hide date and time on mobile */
            @media (max-width: 768px) {
                #dateTimeDisplay,
                #currentDate,
                #currentTime {
                    display: none !important;
                }
            }
            
            @media (max-width: 576px) {
                #dateTimeDisplay,
                #currentDate,
                #currentTime {
                    display: none !important;
                }
            }
            
            /* Mobile Responsive Styles for Notifications */
            @media (max-width: 768px) {
                /* On mobile, allow sidebar to open temporarily but close it after link click */
                /* Ensure sidebar links are clickable when sidebar is open */
                #sidenavAccordion .nav-link {
                    pointer-events: auto !important;
                    z-index: 1040 !important;
                }
                /* Remove overlay that might block clicks */
                body.sb-sidenav-toggled #layoutSidenav #layoutSidenav_content:before,
                #layoutSidenav.sb-sidenav-toggled #layoutSidenav_content:before {
                    pointer-events: none !important;
                    z-index: 1034 !important;
                }
                /* Ensure main content is always clickable */
                #layoutSidenav_content {
                    pointer-events: auto !important;
                }
                /* Allow sidebar to scroll without closing */
                #layoutSidenav_nav,
                .sb-sidenav {
                    overflow-y: auto !important;
                    -webkit-overflow-scrolling: touch !important;
                    touch-action: pan-y !important;
                }
                /* Prevent scroll events in sidebar from propagating */
                #layoutSidenav_nav *,
                .sb-sidenav * {
                    touch-action: auto !important;
                }
                /* Fix dropdown positioning on mobile - only apply on mobile */
                #notificationDropdown .notification-dropdown,
                #notificationDropdown .dropdown-menu-end.notification-dropdown,
                #notificationDropdown.show .notification-dropdown,
                #notificationDropdown.show .dropdown-menu-end.notification-dropdown,
                .notification-dropdown.dropdown-menu-end,
                .dropdown-menu-end.notification-dropdown {
                    width: calc(100vw - 1rem) !important;
                    max-width: calc(100vw - 1rem) !important;
                    margin: 0 !important;
                    left: 0.5rem !important;
                    right: 0.5rem !important;
                    transform: none !important;
                    position: fixed !important;
                    top: 60px !important;
                    max-height: calc(100vh - 120px) !important;
                    border-radius: 12px !important;
                    z-index: 1055 !important;
                    inset: 60px 0.5rem auto 0.5rem !important;
                }
                
                .notification-dropdown .dropdown-header {
                    padding: 0.75rem 1rem !important;
                    border-radius: 12px 12px 0 0 !important;
                }
                
                .notification-dropdown .dropdown-header h6 {
                    font-size: 0.9rem !important;
                }
                
                .notification-dropdown .dropdown-header small {
                    font-size: 0.7rem !important;
                }
                
                .notification-content {
                    padding: 0.75rem 1rem !important;
                    max-height: calc(100vh - 180px) !important;
                }
                
                .notification-section {
                    margin-bottom: 1rem !important;
                }
                
                .notification-section .section-header {
                    margin-bottom: 0.75rem !important;
                }
                
                .notification-section .section-header h6 {
                    font-size: 0.85rem !important;
                }
                
                .notification-count-badge {
                    padding: 0.2rem 0.5rem !important;
                    font-size: 0.7rem !important;
                    min-width: 20px !important;
                }
                
                .notification-item {
                    margin-bottom: 0.75rem !important;
                    border-radius: 10px !important;
                }
                
                .notification-item-content {
                    padding: 0.75rem !important;
                    gap: 0.75rem !important;
                    flex-wrap: wrap;
                }
                
                .notification-icon {
                    width: 40px !important;
                    height: 40px !important;
                    font-size: 1rem !important;
                    border-radius: 10px !important;
                }
                
                .notification-details {
                    flex: 1;
                    min-width: 0;
                }
                
                .notification-title {
                    font-size: 0.85rem !important;
                    margin-bottom: 0.375rem !important;
                    line-height: 1.2 !important;
                }
                
                .notification-meta {
                    gap: 0.5rem !important;
                    margin-bottom: 0.375rem !important;
                    flex-wrap: wrap;
                }
                
                .meta-item {
                    font-size: 0.75rem !important;
                    gap: 0.2rem !important;
                }
                
                .meta-item i {
                    font-size: 0.65rem !important;
                }
                
                .notification-info {
                    gap: 0.5rem !important;
                    margin-bottom: 0.375rem !important;
                    flex-wrap: wrap;
                }
                
                .info-item {
                    font-size: 0.75rem !important;
                    gap: 0.2rem !important;
                }
                
                .info-item i {
                    font-size: 0.65rem !important;
                }
                
                .notification-theme {
                    font-size: 0.75rem !important;
                    padding: 0.2rem 0.4rem !important;
                    margin-bottom: 0.375rem !important;
                }
                
                .notification-badge {
                    font-size: 0.7rem !important;
                    padding: 0.15rem 0.4rem !important;
                }
                
                .notification-arrow {
                    font-size: 1rem !important;
                    display: none; /* Hide arrow on mobile to save space */
                }
                
                .empty-notification-state {
                    padding: 1.5rem 1rem !important;
                }
                
                .empty-notification-state i {
                    font-size: 2rem !important;
                }
                
                .empty-notification-state span {
                    font-size: 0.85rem !important;
                }
                
                /* Adjust notification bell icon and badge on mobile */
                #notificationDropdown .nav-link {
                    padding: 0.5rem !important;
                }
                
                #notificationDropdown .nav-link svg {
                    width: 20px !important;
                    height: 20px !important;
                }
                
                #notificationBadge {
                    font-size: 0.65rem !important;
                    padding: 0.15rem 0.4rem !important;
                    min-width: 18px !important;
                    height: 18px !important;
                    line-height: 1.2 !important;
                    top: 0 !important;
                    right: 0 !important;
                    transform: translate(25%, -25%) !important;
                }
                
                /* Let Bootstrap handle notification dropdown show/hide */
                /* Bootstrap will add/remove 'show' class automatically */
                
                /* Consistent Toggle Button Sizes on Mobile */
                /* Target all toggle icons by ID pattern */
                [id*="ToggleIcon"],
                [id*="toggleIcon"],
                /* Target chevron icons in mobile-only display */
                .fa-chevron-down.d-md-none,
                .fa-chevron-up.d-md-none,
                /* Target specific known toggle icons */
                #actionsToggleIcon,
                #filterToggleIcon,
                #fundBreakdownToggleIcon {
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
                
                /* Ensure toggle icons are visible and properly sized in headers */
                .actions-header [id*="ToggleIcon"],
                .filter-header [id*="ToggleIcon"],
                .fund-breakdown-header [id*="ToggleIcon"],
                .actions-header #actionsToggleIcon,
                .filter-header #filterToggleIcon,
                .fund-breakdown-header #fundBreakdownToggleIcon {
                    font-size: 1.1rem !important;
                    min-width: 24px !important;
                    min-height: 24px !important;
                }
                
                /* Ensure navbar has proper padding on mobile to prevent cutoff */
                .sb-topnav {
                    padding-left: 0.75rem !important;
                    padding-right: 0.5rem !important;
                    overflow-x: hidden !important;
                    position: fixed !important;
                    top: 0 !important;
                    left: 0 !important;
                    right: 0 !important;
                    z-index: 1040 !important;
                    max-width: 100vw !important;
                    width: 100% !important;
                }
                
                /* Add margin-top to content to account for fixed navbar */
                /* CRITICAL: Remove sidebar padding on mobile - fixes content alignment */
                #layoutSidenav_content {
                    margin-top: 10px !important;
                    padding-left: 0 !important;
                    margin-left: 0 !important;
                    width: 100% !important;
                    max-width: 100vw !important;
                }
                
                /* Ensure sidebar doesn't override navbar */
                #layoutSidenav_nav,
                .sb-sidenav {
                    z-index: 1035 !important;
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
                
                /* Notification dropdown - use Bootstrap's default behavior */
                /* Don't force hide, let Bootstrap handle it */
                
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
                
                /* Ensure navbar doesn't clip dropdown */
                .sb-topnav {
                    overflow: visible !important;
                }
                
                .sb-topnav .navbar-nav {
                    overflow: visible !important;
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
                
                /* Hide logo completely on mobile */
                .sb-topnav .navbar-brand {
                    display: none !important;
                }
                
                .sb-topnav .logo-white-section {
                    display: none !important;
                }
                
                /* Ensure toggle button is always visible and not cut off */
                .sb-topnav .navbar-nav,
                .sb-topnav .d-flex {
                    flex-wrap: nowrap !important;
                    overflow-x: hidden !important;
                    max-width: 100% !important;
                }
                
                #sidebarToggle {
                    flex-shrink: 0 !important;
                    position: relative !important;
                    z-index: 10 !important;
                }
            }
            
            @media (max-width: 576px) {
                #notificationDropdown .notification-dropdown,
                #notificationDropdown .dropdown-menu-end.notification-dropdown,
                #notificationDropdown.show .notification-dropdown,
                #notificationDropdown.show .dropdown-menu-end.notification-dropdown,
                .notification-dropdown.dropdown-menu-end,
                .dropdown-menu-end.notification-dropdown {
                    width: calc(100vw - 0.5rem) !important;
                    max-width: calc(100vw - 0.5rem) !important;
                    margin: 0 !important;
                    left: 0.25rem !important;
                    right: 0.25rem !important;
                    transform: none !important;
                    position: fixed !important;
                    top: 60px !important;
                    max-height: calc(100vh - 100px) !important;
                    border-radius: 10px !important;
                    z-index: 1055 !important;
                    inset: 60px 0.25rem auto 0.25rem !important;
                }
                
                .notification-dropdown .dropdown-header {
                    padding: 0.6rem 0.75rem !important;
                }
                
                .notification-content {
                    padding: 0.6rem 0.75rem !important;
                    max-height: calc(100vh - 160px) !important;
                }
                
                .notification-item-content {
                    padding: 0.6rem !important;
                    gap: 0.6rem !important;
                }
                
                .notification-icon {
                    width: 36px !important;
                    height: 36px !important;
                    font-size: 0.9rem !important;
                }
                
                .notification-title {
                    font-size: 0.8rem !important;
                }
                
                .meta-item,
                .info-item {
                    font-size: 0.7rem !important;
                }
                
                .notification-theme {
                    font-size: 0.7rem !important;
                }
                
                /* Consistent Toggle Button Sizes on Extra Small Mobile */
                [id*="ToggleIcon"],
                [id*="toggleIcon"],
                .fa-chevron-down.d-md-none,
                .fa-chevron-up.d-md-none,
                #actionsToggleIcon,
                #filterToggleIcon,
                #fundBreakdownToggleIcon {
                    font-size: 1rem !important;
                    width: 22px !important;
                    height: 22px !important;
                }
                
                .actions-header [id*="ToggleIcon"],
                .filter-header [id*="ToggleIcon"],
                .fund-breakdown-header [id*="ToggleIcon"],
                .actions-header #actionsToggleIcon,
                .filter-header #filterToggleIcon,
                .fund-breakdown-header #fundBreakdownToggleIcon {
                    font-size: 1rem !important;
                    min-width: 22px !important;
                    min-height: 22px !important;
                }
                
                /* Ensure navbar has proper padding on extra small mobile */
                .sb-topnav {
                    padding-left: 0.5rem !important;
                    padding-right: 0.25rem !important;
                    overflow-x: hidden !important;
                    position: fixed !important;
                    top: 0 !important;
                    left: 0 !important;
                    right: 0 !important;
                    z-index: 1040 !important;
                    max-width: 100vw !important;
                    width: 100% !important;
                }
                
                /* Add margin-top to content to account for fixed navbar */
                /* CRITICAL: Remove sidebar padding on extra small mobile - fixes content alignment */
                #layoutSidenav_content {
                    margin-top: 10px !important;
                    padding-left: 0 !important;
                    margin-left: 0 !important;
                    width: 100% !important;
                    max-width: 100vw !important;
                }
                
                /* Ensure sidebar doesn't override navbar on extra small mobile */
                #layoutSidenav_nav,
                .sb-sidenav {
                    z-index: 1035 !important;
                }
                
                /* Ensure sidebar overlay doesn't cover navbar */
                #layoutSidenav_content:before,
                body.sb-sidenav-toggled #layoutSidenav_content:before {
                    z-index: 1034 !important;
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
                
                /* Notification dropdown - use Bootstrap's default behavior on extra small */
                /* Don't force hide, let Bootstrap handle it */
                
                /* Ensure dropdown items are visible on extra small */
                .sb-topnav .dropdown-menu .dropdown-item {
                    padding: 0.5rem 0.75rem !important;
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
                    margin-right: 0.25rem !important;
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
                
                /* Hide logo completely on extra small mobile */
                .sb-topnav .navbar-brand {
                    display: none !important;
                }
                
                .sb-topnav .logo-white-section {
                    display: none !important;
                }
                
                /* Ensure toggle button is always visible on extra small mobile */
                #sidebarToggle {
                    flex-shrink: 0 !important;
                    position: relative !important;
                    z-index: 10 !important;
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
            
            /* Animations */
            @keyframes slideInUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            /* Notification Bell Animation */
            .nav-link:hover svg {
                animation: bellShake 0.5s ease-in-out;
            }
            
            @keyframes bellShake {
                0%, 100% { transform: rotate(0deg); }
                25% { transform: rotate(-10deg); }
                75% { transform: rotate(10deg); }
            }
            /* Footer Styling */
            #layoutSidenav {
                display: flex;
                flex-direction: column;
                min-height: 100vh;
            }
            
            #layoutSidenav_content {
                flex: 1;
                padding-bottom: 2rem;
            }
            
            #layoutSidenav_content main {
                padding-bottom: 3rem;
                min-height: calc(100vh - 200px);
            }
            
            footer.bg-dark.text-light {
                width: 100% !important;
                margin-left: 0 !important;
                margin-top: auto;
                position: relative;
                z-index: 1;
                flex-shrink: 0;
            }
            
            footer.bg-dark.text-light .text-md-end small a {
                color: #940000 !important;
                font-weight: bold !important;
            }
            
            footer.bg-dark.text-light .text-md-end small span {
                color: #ffffff !important;
            }
            
            /* Ensure footer spans full width regardless of sidebar state */
            body.sb-sidenav-toggled footer.bg-dark.text-light,
            #layoutSidenav.sb-sidenav-toggled footer.bg-dark.text-light {
                width: 100% !important;
                margin-left: 0 !important;
            }
            
            /* Footer container full width */
            footer.bg-dark.text-light .container-fluid {
                width: 100%;
                max-width: 100%;
                padding-left: 1.5rem;
                padding-right: 1.5rem;
            }
            
            /* Adjust footer position when sidebar is visible */
            @media (min-width: 992px) {
                footer.bg-dark.text-light {
                    margin-left: 0 !important;
                }
            }
            
            /* CRITICAL FIX: Ensure content has padding when sidebar is open on desktop */
            @media (min-width: 992px) {
                body.sb-nav-fixed:not(.sb-sidenav-toggled) #layoutSidenav_content {
                    padding-left: 225px !important;
                }
                body.sb-nav-fixed.sb-sidenav-toggled #layoutSidenav_content {
                    padding-left: 0 !important;
                }
            }
            
            /* Ensure content containers have bottom margin to prevent footer overlap */
            .container-fluid:last-child,
            .container-fluid > .row:last-child {
                margin-bottom: 2rem;
            }
            
            /* Additional padding for pages with tables or long content */
            .table-responsive {
                margin-bottom: 1rem;
            }
        </style>
        @stack('styles')
    </head>
    <body class="sb-nav-fixed">
        @php
            $navClasses = 'sb-topnav navbar navbar-expand ';
            $navStyle = '';
            
            if ($sidebarStyle === 'light') {
                $navClasses .= 'navbar-light bg-light';
            } elseif ($sidebarStyle === 'primary') {
                $navClasses .= 'navbar-dark';
                $navStyle = "background: linear-gradient(180deg, {$selectedColor} 0%, {$selectedColor}dd 100%) !important;";
            } elseif ($sidebarStyle === 'transparent') {
                $navClasses .= 'navbar-dark';
                $navStyle = "background: linear-gradient(180deg, {$selectedColor} 0%, {$selectedColor}dd 100%) !important;";
            } else {
                $navClasses .= 'navbar-dark bg-dark';
            }
        @endphp
        <nav class="{{ $navClasses }}" @if($navStyle)style="{{ $navStyle }}"@endif>
            <!-- Navbar Brand - Hidden on Mobile -->
            <a class="navbar-brand ps-3 d-none d-lg-flex align-items-center logo-white-section" href="{{ route('dashboard') }}">
                <img src="{{ asset('assets/images/waumini_link_logo.png') }}" alt="Waumini Link Logo" class="logo" style="height: 45px; max-width: 200px; object-fit: contain;">
            </a>
            <!-- Sidebar Toggle - First on Mobile -->
            <button class="btn btn-link btn-sm order-first order-lg-0 me-3 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars" style="color: #ffffff !important;"></i></button>
            <!-- Welcome Message -->
            <div class="navbar-text me-auto ms-2 ms-md-3" style="font-size: 1.1rem; font-weight: 800; color: #ffffff !important;">
                <strong>{{ SettingsService::get('church_name', 'KKKT Ushirika wa Longuo') }}</strong>
            </div>

            <!-- Navbar-->
            <ul class="navbar-nav ms-auto me-2 me-md-3 me-lg-4">
                <!-- Date and Time Display - Hidden on Mobile -->
                <li class="nav-item d-none d-md-flex align-items-center me-2 me-md-3" id="dateTimeDisplay">
                    <div class="text-end" style="color: #ffffff !important;">
                        <div id="currentDate" class="d-none d-md-block" style="font-size: 0.9rem; font-weight: 700; color: #ffffff !important;"></div>
                        <div id="currentTime" class="d-none d-md-block" style="font-size: 1.1rem; font-weight: 800; color: #ffffff !important;"></div>
                    </div>
                </li>
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
                            <h6 class="mb-0 fw-bold"><i class="fas fa-bell me-2"></i>{{ __('common.notifications') }}</h6>
                            <small class="opacity-75" id="lastUpdated">{{ __('common.just_now') }}</small>
                        </div>
                        
                        <div class="notification-content" style="padding: 1rem 1.5rem; max-height: calc(70vh - 80px); overflow-y: auto;">
                            <!-- Upcoming Events -->
                            <div class="notification-section mb-3">
                                <div class="section-header d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0 fw-bold text-primary">
                                        <i class="fas fa-calendar-alt me-2"></i>{{ __('common.special_events') }}
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
                                        <i class="fas fa-birthday-cake me-2"></i>{{ __('common.celebrations') }}
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
                                        <i class="fas fa-church me-2"></i>{{ __('common.sunday_services') }}
                                    </h6>
                                    <span class="notification-count-badge bg-success" id="servicesCount">0</span>
                                </div>
                                <div id="servicesList" class="notification-list">
                                    <!-- Services will be loaded here -->
                                </div>
                            </div>
                            
                            <!-- Pending Financial Approvals (for Secretary, Pastor, Admin) -->
                            @if(auth()->user() && (auth()->user()->isSecretary() || auth()->user()->isPastor() || auth()->user()->isAdmin() || auth()->user()->canApproveFinances()))
                            <div class="notification-section mb-3">
                                <div class="section-header d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0 fw-bold text-warning">
                                        <i class="fas fa-clock me-2"></i>{{ __('common.pending_approvals') }}
                                    </h6>
                                    <span class="notification-count-badge bg-warning" id="pendingApprovalsCount">0</span>
                                </div>
                                <div id="pendingApprovalsList" class="notification-list">
                                    <!-- Pending approvals will be loaded here -->
                                </div>
                            </div>
                            @endif
                            
                            <!-- Payments Needing Verification (for Treasurer) -->
                            @if(auth()->user() && (auth()->user()->isTreasurer() || auth()->user()->isAdmin()))
                            <div class="notification-section mb-3">
                                <div class="section-header d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0 fw-bold text-success">
                                        <i class="fas fa-dollar-sign me-2"></i>{{ __('common.payments_verification') }}
                                    </h6>
                                    <span class="notification-count-badge bg-success" id="paymentsNeedingVerificationCount">0</span>
                                </div>
                                <div id="paymentsNeedingVerificationList" class="notification-list">
                                    <!-- Payments needing verification will be loaded here -->
                                </div>
                            </div>
                            @endif
                            
                            <div class="text-center py-2 pb-3">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>{{ __('common.click_to_view_details') }}
                                </small>
                            </div>
                        </div>
                    </div>
                </li>
                <!-- Language Switcher -->
                @include('partials.language-switcher')
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="color: #ffffff !important;"><i class="fas fa-user fa-fw" style="color: #ffffff !important;"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        @if(auth()->user()->isMember())
                            <li><a class="dropdown-item" href="{{ route('member.settings') }}"><i class="fas fa-cog me-2"></i>{{ __('common.settings') }}</a></li>
                        @else
                            <li><a class="dropdown-item" href="#!">{{ __('common.settings') }}</a></li>
                            <li><a class="dropdown-item" href="#!">Activity Log</a></li>
                        @endif
                        <li><hr class="dropdown-divider" /></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('logout') }}" 
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-2"></i>{{ __('common.logout') }}
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
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                            @if(auth()->user()->isAdmin())
                            {{-- Admin Menu --}}
                            <div class="sb-sidenav-menu-heading">{{ __('common.administration') }}</div>
                            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-shield-alt"></i></div>
                                {{ __('common.admin_dashboard') }}
                            </a>
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseAdmin" aria-expanded="false" aria-controls="collapseAdmin">
                                <div class="sb-nav-link-icon"><i class="fas fa-cogs"></i></div>
                                {{ __('common.system_management') }}
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseAdmin" aria-labelledby="headingAdmin" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="{{ route('admin.logs') }}">
                                        <i class="fas fa-list-alt me-2"></i>{{ __('common.logs') }}
                                    </a>
                                    <a class="nav-link" href="{{ route('admin.sessions') }}">
                                        <i class="fas fa-user-check me-2"></i>{{ __('common.user_sessions') }}
                                    </a>
                                    <a class="nav-link" href="{{ route('admin.users') }}">
                                        <i class="fas fa-users me-2"></i>{{ __('common.manage_users') }}
                                    </a>
                                    <a class="nav-link" href="{{ route('admin.roles-permissions') }}">
                                        <i class="fas fa-shield-alt me-2"></i>{{ __('common.roles_permissions') }}
                                    </a>
                                    <a class="nav-link" href="{{ route('admin.system-monitor') }}">
                                        <i class="fas fa-server me-2"></i>{{ __('common.system_monitor') }}
                                    </a>
                                </nav>
                            </div>
                            @endif
                            
                            {{-- Member Portal for Church Elders (they are also members) - Show FIRST --}}
                            @if(auth()->user()->isChurchElder() && auth()->user()->member)
                            {{-- Member Portal for Church Elders --}}
                            <div class="sb-sidenav-menu-heading">{{ __('common.member_portal') }}</div>
                            <a class="nav-link" href="{{ route('member.dashboard') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                {{ __('common.dashboard') }}
                            </a>
                            <a class="nav-link" href="{{ route('member.information') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-user-circle"></i></div>
                                {{ __('common.my_information') }}
                            </a>
                            <a class="nav-link" href="{{ route('member.finance') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-wallet"></i></div>
                                {{ __('common.my_finance') }}
                            </a>
                            <a class="nav-link" href="{{ route('member.announcements') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-bullhorn"></i></div>
                                {{ __('common.announcements') }}
                                @php
                                    $member = auth()->user()->member ?? null;
                                    if ($member) {
                                        $activeAnnouncements = \App\Models\Announcement::active()->pluck('id');
                                        $viewedAnnouncementIds = \App\Models\AnnouncementView::where('member_id', $member->id)
                                            ->whereIn('announcement_id', $activeAnnouncements)
                                            ->pluck('announcement_id');
                                        $unreadCount = $activeAnnouncements->diff($viewedAnnouncementIds)->count();
                                    } else {
                                        $unreadCount = 0;
                                    }
                                @endphp
                                @if($unreadCount > 0)
                                    <span class="badge bg-danger ms-2">{{ $unreadCount }}</span>
                                @endif
                            </a>
                            <a class="nav-link" href="{{ route('member.leaders') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-users-cog"></i></div>
                                {{ __('common.leaders') }}
                            </a>
                            @endif
                            
                            @if(auth()->user()->isChurchElder())
                            {{-- Church Elder Menu --}}
                            <div class="sb-sidenav-menu-heading">{{ __('common.church_elder') }}</div>
                            <a class="nav-link" href="{{ route('church-elder.dashboard') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                {{ __('common.dashboard') }}
                            </a>
                            @php
                                $elderCommunities = auth()->user()->elderCommunities();
                            @endphp
                            

                            
                            @if($elderCommunities->count() > 0)
                                @foreach($elderCommunities as $elderCommunity)
                                <a class="nav-link" href="#" data-bs-toggle="collapse" data-bs-target="#collapseCommunity{{ $elderCommunity->id }}" aria-expanded="true" aria-controls="collapseCommunity{{ $elderCommunity->id }}">
                                    <div class="sb-nav-link-icon"><i class="fas fa-home"></i></div>
                                    {{ $elderCommunity->name }}
                                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                </a>
                                <div class="collapse show" id="collapseCommunity{{ $elderCommunity->id }}" aria-labelledby="headingCommunity{{ $elderCommunity->id }}" data-bs-parent="#sidenavAccordion">
                                    <nav class="sb-sidenav-menu-nested nav">
                                        <a class="nav-link" href="{{ route('church-elder.community.show', $elderCommunity->id) }}">
                                            <i class="fas fa-info-circle me-2"></i>{{ __('common.community_info') }}
                                        </a>
                                        
                                        {{-- Finance Menu (Community-specific) --}}
                                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseFinance{{ $elderCommunity->id }}" aria-expanded="false" aria-controls="collapseFinance{{ $elderCommunity->id }}">
                                            <i class="fas fa-chart-line me-2"></i>Finance
                                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                        </a>
                                        <div class="collapse" id="collapseFinance{{ $elderCommunity->id }}" aria-labelledby="headingFinance{{ $elderCommunity->id }}" data-bs-parent="#collapseCommunity{{ $elderCommunity->id }}">
                                            <nav class="sb-sidenav-menu-nested nav">
                                                <a class="nav-link" href="{{ route('church-elder.community-offerings.index', $elderCommunity->id) }}">
                                                    <i class="fas fa-calendar-week me-2"></i>{{ __('common.mid_week_offerings') }}
                                                </a>
                                            </nav>
                                        </div>
                                        
                                        <a class="nav-link" href="{{ route('church-elder.tasks.index', $elderCommunity->id) }}">
                                            <i class="fas fa-tasks me-2"></i>{{ __('common.tasks') }}
                                        </a>
                                        <a class="nav-link" href="{{ route('church-elder.issues.index', $elderCommunity->id) }}">
                                            <i class="fas fa-exclamation-triangle me-2"></i>{{ __('common.issues') }}
                                        </a>
                                        <a class="nav-link" href="{{ route('church-elder.reports', $elderCommunity->id) }}">
                                            <i class="fas fa-chart-bar me-2"></i>{{ __('common.community_reports') }}
                                        </a>
                                    </nav>
                                </div>
                                @endforeach
                            @endif
                            @endif
                            
                            {{-- Member Portal for Evangelism Leaders (they are also members) - Show FIRST, same structure as Church Elders --}}
                            @if(auth()->user()->isEvangelismLeader() && auth()->user()->member)
                            {{-- Member Portal for Evangelism Leaders --}}
                            <div class="sb-sidenav-menu-heading">{{ __('common.member_portal') }}</div>
                            <a class="nav-link" href="{{ route('member.dashboard') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                {{ __('common.dashboard') }}
                            </a>
                            <a class="nav-link" href="{{ route('member.information') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-user-circle"></i></div>
                                {{ __('common.my_information') }}
                            </a>
                            <a class="nav-link" href="{{ route('member.finance') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-wallet"></i></div>
                                {{ __('common.my_finance') }}
                            </a>
                            <a class="nav-link" href="{{ route('member.announcements') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-bullhorn"></i></div>
                                {{ __('common.announcements') }}
                                @php
                                    $member = auth()->user()->member ?? null;
                                    if ($member) {
                                        $activeAnnouncements = \App\Models\Announcement::active()->pluck('id');
                                        $viewedAnnouncementIds = \App\Models\AnnouncementView::where('member_id', $member->id)
                                            ->whereIn('announcement_id', $activeAnnouncements)
                                            ->pluck('announcement_id');
                                        $unreadCount = $activeAnnouncements->diff($viewedAnnouncementIds)->count();
                                    } else {
                                        $unreadCount = 0;
                                    }
                                @endphp
                                @if($unreadCount > 0)
                                    <span class="badge bg-danger ms-2">{{ $unreadCount }}</span>
                                @endif
                            </a>
                            <a class="nav-link" href="{{ route('member.leaders') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-users-cog"></i></div>
                                {{ __('common.leaders') }}
                            </a>
                            @endif
                            
                            {{-- Member Portal for Parish Workers --}}
                            @if(auth()->user()->isParishWorker() && auth()->user()->member)
                            <div class="sb-sidenav-menu-heading">{{ __('common.member_portal') }}</div>
                            <a class="nav-link" href="{{ route('member.dashboard') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                {{ __('common.dashboard') }}
                            </a>
                            <a class="nav-link" href="{{ route('member.information') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-user-circle"></i></div>
                                {{ __('common.my_information') }}
                            </a>
                            <a class="nav-link" href="{{ route('member.finance') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-wallet"></i></div>
                                {{ __('common.my_finance') }}
                            </a>
                            <a class="nav-link" href="{{ route('member.announcements') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-bullhorn"></i></div>
                                {{ __('common.announcements') }}
                                @php
                                    $member = auth()->user()->member ?? null;
                                    if ($member) {
                                        $activeAnnouncements = \App\Models\Announcement::active()->pluck('id');
                                        $viewedAnnouncementIds = \App\Models\AnnouncementView::where('member_id', $member->id)
                                            ->whereIn('announcement_id', $activeAnnouncements)
                                            ->pluck('announcement_id');
                                        $unreadCount = $activeAnnouncements->diff($viewedAnnouncementIds)->count();
                                    } else {
                                        $unreadCount = 0;
                                    }
                                @endphp
                                @if($unreadCount > 0)
                                    <span class="badge bg-danger ms-2">{{ $unreadCount }}</span>
                                @endif
                            </a>
                            <a class="nav-link" href="{{ route('member.leaders') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-users-cog"></i></div>
                                {{ __('common.leaders') }}
                            </a>
                            @endif
                            
                            {{-- Remove Finance menu for Church Elders (moved to community section) --}}
                            @if(auth()->user()->isMember() && !auth()->user()->isChurchElder() && !auth()->user()->isEvangelismLeader() && !auth()->user()->isParishWorker())
                            {{-- Member Menu --}}
                            <div class="sb-sidenav-menu-heading">Member Portal</div>
                            <a class="nav-link" href="{{ route('member.dashboard') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                {{ __('common.dashboard') }}
                            </a>
                            <a class="nav-link" href="{{ route('member.information') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-user-circle"></i></div>
                                {{ __('common.my_information') }}
                            </a>
                            <a class="nav-link" href="{{ route('member.finance') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-wallet"></i></div>
                                {{ __('common.my_finance') }}
                            </a>
                            <a class="nav-link" href="{{ route('member.announcements') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-bullhorn"></i></div>
                                {{ __('common.announcements') }}
                                @php
                                    $member = auth()->user()->member ?? null;
                                    if ($member) {
                                        $activeAnnouncements = \App\Models\Announcement::active()->pluck('id');
                                        $viewedAnnouncementIds = \App\Models\AnnouncementView::where('member_id', $member->id)
                                            ->whereIn('announcement_id', $activeAnnouncements)
                                            ->pluck('announcement_id');
                                        $unreadCount = $activeAnnouncements->diff($viewedAnnouncementIds)->count();
                                    } else {
                                        $unreadCount = 0;
                                    }
                                @endphp
                                @if($unreadCount > 0)
                                    <span class="badge bg-danger ms-2">{{ $unreadCount }}</span>
                                @endif
                            </a>
                            <a class="nav-link" href="{{ route('member.leaders') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-users-cog"></i></div>
                                {{ __('common.leaders') }}
                            </a>
                            @elseif(!auth()->user()->isTreasurer() && !auth()->user()->isAdmin() && !auth()->user()->isChurchElder() && !auth()->user()->isEvangelismLeader() && !auth()->user()->isParishWorker())
                            <div class="sb-sidenav-menu-heading">{{ __('common.main') }}</div>
                            <a class="nav-link" href="{{ route('dashboard') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                {{ __('common.dashboard') }}
                            </a>
                            @endif
                            
                            @if(!auth()->user()->isTreasurer() || auth()->user()->isAdmin())
                            @if(!auth()->user()->isMember() && !auth()->user()->isChurchElder() && !auth()->user()->isEvangelismLeader() && !auth()->user()->isParishWorker())
                            
                            <div class="sb-sidenav-menu-heading">{{ __('common.management') }}</div>
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseMembers" aria-expanded="false" aria-controls="collapseMembers">
                                <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                                {{ __('common.members') }}
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseMembers" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    @if(auth()->user()->hasPermission('members.create') || auth()->user()->isAdmin())
                                    <a class="nav-link" href="{{ route('members.add') }}">
                                        <i class="fas fa-user-plus me-2"></i>{{ __('common.add_new_member') }}
                                    </a>
                                    @endif
                                    @if(auth()->user()->hasPermission('members.view') || auth()->user()->isAdmin())
                                    <a class="nav-link" href="{{ route('members.view') }}">
                                        <i class="fas fa-list me-2"></i>{{ __('common.all_members') }}
                                    </a>
                                    @endif
                                </nav>
                            </div>

                            @if(auth()->user()->isAdmin() || auth()->user()->isPastor() || auth()->user()->isSecretary() || auth()->user()->isEvangelismLeader())
                            <a class="nav-link" href="{{ route('departments.index') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-layer-group"></i></div>
                                {{ __('common.departments') }}
                            </a>
                            @endif
                            
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLeadership" aria-expanded="false" aria-controls="collapseLeadership">
                                <div class="sb-nav-link-icon"><i class="fas fa-user-tie"></i></div>
                                {{ __('common.leadership') }}
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseLeadership" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="{{ route('leaders.index') }}">
                                        <i class="fas fa-list me-2"></i>{{ __('common.all_leaders') }}
                                    </a>
                                    <a class="nav-link" href="{{ route('leaders.reports') }}">
                                        <i class="fas fa-chart-bar me-2"></i>{{ __('common.reports') }}
                                    </a>
                                    @if(auth()->user()->canManageLeadership())
                                        <a class="nav-link" href="{{ route('leaders.create') }}">
                                            <i class="fas fa-plus me-2"></i>{{ __('common.assign_position') }}
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
                                {{ __('common.events_services') }}
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseEvents" aria-labelledby="headingTwo" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="{{ route('services.sunday.index') }}"><i class="fas fa-church me-2"></i>{{ __('common.sunday_services') }}</a>
                                    <a class="nav-link" href="{{ route('special.events.index') }}"><i class="fas fa-calendar-plus me-2"></i>{{ __('common.special_events') }}</a>
                                    <a class="nav-link" href="{{ route('promise-guests.index') }}"><i class="fas fa-user-check me-2"></i>{{ __('common.promise_guests') }}</a>
                                    <a class="nav-link" href="{{ route('celebrations.index') }}"><i class="fas fa-birthday-cake me-2"></i>{{ __('common.celebrations') }}</a>
                                    <a class="nav-link" href="{{ route('bereavement.index') }}"><i class="fas fa-heart-broken me-2"></i>{{ __('common.bereavement') }}</a>
                                </nav>
                            </div>
                            @if(auth()->user()->isPastor() || auth()->user()->isAdmin())
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePastorRequests" aria-expanded="false" aria-controls="collapsePastorRequests">
                                <div class="sb-nav-link-icon"><i class="fas fa-file-signature"></i></div>
                                {{ __('common.requests') }}
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapsePastorRequests" aria-labelledby="headingPastorRequests" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="{{ route('pastor.baptism-applications.pending') }}">
                                        <i class="fas fa-water me-2"></i>{{ __('common.baptism_applications') }}
                                        @php
                                            try {
                                                $pendingCount = \App\Models\BaptismApplication::where('status', 'pending')->count();
                                            } catch (\Exception $e) {
                                                $pendingCount = 0;
                                            }
                                        @endphp
                                        @if($pendingCount > 0)
                                            <span class="badge bg-warning ms-2">{{ $pendingCount }}</span>
                                        @endif
                                    </a>
                                    <a class="nav-link" href="{{ route('pastor.return-to-fellowship-requests.pending') }}">
                                        <i class="fas fa-door-open me-2"></i>{{ __('common.return_to_fellowship') }}
                                        @php
                                            try {
                                                $pendingFellowshipCount = \App\Models\ReturnToFellowshipRequest::where('status', 'pending')->count();
                                            } catch (\Exception $e) {
                                                $pendingFellowshipCount = 0;
                                            }
                                        @endphp
                                        @if($pendingFellowshipCount > 0)
                                            <span class="badge bg-warning ms-2">{{ $pendingFellowshipCount }}</span>
                                        @endif
                                    </a>
                                    <a class="nav-link" href="{{ route('pastor.marriage-blessing-requests.pending') }}">
                                        <i class="fas fa-heart me-2"></i>{{ __('common.marriage_blessing') }}
                                        @php
                                            try {
                                                $pendingBlessingCount = \App\Models\MarriageBlessingRequest::where('status', 'pending')->count();
                                            } catch (\Exception $e) {
                                                $pendingBlessingCount = 0;
                                            }
                                        @endphp
                                        @if($pendingBlessingCount > 0)
                                            <span class="badge bg-warning ms-2">{{ $pendingBlessingCount }}</span>
                                        @endif
                                    </a>
                                    <a class="nav-link" href="{{ route('pastor.church-wedding-requests.pending') }}">
                                        <i class="fas fa-rings-wedding me-2"></i>{{ __('common.church_wedding') }}
                                        @php
                                            try {
                                                $pendingWeddingCount = \App\Models\ChurchWeddingRequest::where('status', 'pending')->count();
                                            } catch (\Exception $e) {
                                                $pendingWeddingCount = 0;
                                            }
                                        @endphp
                                        @if($pendingWeddingCount > 0)
                                            <span class="badge bg-warning ms-2">{{ $pendingWeddingCount }}</span>
                                        @endif
                                    </a>
                                </nav>
                            </div>
                            
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePastorTasksIssues" aria-expanded="false" aria-controls="collapsePastorTasksIssues">
                                <div class="sb-nav-link-icon"><i class="fas fa-tasks"></i></div>
                                {{ __('common.tasks_issues') }}
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapsePastorTasksIssues" aria-labelledby="headingPastorTasksIssues" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="{{ route('pastor.tasks.index') }}">
                                        <i class="fas fa-tasks me-2"></i>{{ __('common.all_tasks') }}
                                        @php
                                            try {
                                                $totalTasks = \App\Models\EvangelismTask::count() + \App\Models\ChurchElderTask::count();
                                            } catch (\Exception $e) {
                                                $totalTasks = 0;
                                            }
                                        @endphp
                                        @if($totalTasks > 0)
                                            <span class="badge bg-primary ms-2">{{ $totalTasks }}</span>
                                        @endif
                                    </a>
                                    <a class="nav-link" href="{{ route('pastor.issues.index') }}">
                                        <i class="fas fa-exclamation-triangle me-2"></i>{{ __('common.all_issues') }}
                                        @php
                                            try {
                                                $totalIssues = \App\Models\EvangelismIssue::whereIn('status', ['open', 'in_progress'])->count() + \App\Models\ChurchElderIssue::whereIn('status', ['open', 'in_progress'])->count();
                                            } catch (\Exception $e) {
                                                $totalIssues = 0;
                                            }
                                        @endphp
                                        @if($totalIssues > 0)
                                            <span class="badge bg-danger ms-2">{{ $totalIssues }}</span>
                                        @endif
                                    </a>
                                    <a class="nav-link" href="{{ route('pastor.reports.index') }}">
                                        <i class="fas fa-file-alt me-2"></i>{{ __('common.all_reports') }}
                                        @php
                                            try {
                                                $totalReports = \App\Models\EvangelismReport::count();
                                            } catch (\Exception $e) {
                                                $totalReports = 0;
                                            }
                                        @endphp
                                        @if($totalReports > 0)
                                            <span class="badge bg-info ms-2">{{ $totalReports }}</span>
                                        @endif
                                    </a>
                                </nav>
                            </div>
                            @endif
                            @endif
                            @endif
                            
                            @if(auth()->user()->isTreasurer())
                            {{-- For Treasurer: Show finance menu items directly without dropdown --}}
                            <a class="nav-link" href="{{ route('finance.dashboard') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                {{ __('common.dashboard') }}
                            </a>
                            @if(auth()->user()->canApproveFinances() || auth()->user()->isSecretary())
                            <a class="nav-link" href="{{ route('finance.approval.dashboard') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-check-circle"></i></div>
                                {{ __('common.approval_dashboard') }}
                            </a>
                            @endif
                            <a class="nav-link" href="{{ route('finance.tithes') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-coins"></i></div>
                                {{ __('common.tithes') }}
                            </a>
                            <a class="nav-link" href="{{ route('finance.offerings') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-gift"></i></div>
                                {{ __('common.offerings') }}
                            </a>
                            <a class="nav-link" href="{{ route('sunday-offering.index') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-hand-holding-usd"></i></div>
                                Sunday Collections
                            </a>
                            <a class="nav-link" href="{{ route('finance.weekly-campus-summary') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-chart-bar"></i></div>
                                Weekly Campus Summary
                            </a>
                            <a class="nav-link" href="{{ route('finance.pledges') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-handshake"></i></div>
                                {{ __('common.pledges') }}
                            </a>
                            <a class="nav-link" href="{{ route('finance.ahadi-pledges.index') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-gift"></i></div>
                                {{ __('common.ahadi_kwa_bwana') }}
                            </a>
                            <a class="nav-link" href="{{ route('finance.budgets') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-wallet"></i></div>
                                {{ __('common.budgets') }}
                            </a>
                            <a class="nav-link" href="{{ route('finance.expenses') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-receipt"></i></div>
                                {{ __('common.expenses') }}
                            </a>
                            <div class="sb-sidenav-menu-heading">{{ __('common.account') }}</div>
                            <a class="nav-link" href="{{ route('leader.change-password') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-key"></i></div>
                                {{ __('common.change_password') }}
                            </a>
                            @elseif(!auth()->user()->isMember() && !auth()->user()->isChurchElder() && !auth()->user()->isEvangelismLeader() && !auth()->user()->isParishWorker())
                            {{-- For other users (not treasurer, not member, not church elder): Show finance menu as collapsed dropdown --}}
                            {{-- Church Elders have Finance inside their community section --}}
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseFinance" aria-expanded="false" aria-controls="collapseFinance">
                                <div class="sb-nav-link-icon"><i class="fas fa-chart-line"></i></div>
                                Finance
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseFinance" aria-labelledby="headingThree" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="{{ route('finance.dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i>{{ __('common.dashboard') }}</a>
                                    @if(auth()->user()->canApproveFinances() || auth()->user()->isSecretary())
                                    <a class="nav-link" href="{{ route('finance.approval.dashboard') }}"><i class="fas fa-check-circle me-2"></i>Approval {{ __('common.dashboard') }}</a>
                                    @endif
                                    <a class="nav-link" href="{{ route('finance.tithes') }}"><i class="fas fa-coins me-2"></i>Tithes</a>
                                    <a class="nav-link" href="{{ route('finance.offerings') }}"><i class="fas fa-gift me-2"></i>Offerings</a>
                                    <a class="nav-link" href="{{ route('sunday-offering.index') }}"><i class="fas fa-hand-holding-usd me-2"></i>Sunday Collections</a>
                                    <a class="nav-link" href="{{ route('finance.weekly-campus-summary') }}"><i class="fas fa-chart-bar me-2"></i>Weekly Campus Summary</a>
                                    <a class="nav-link" href="{{ route('finance.ahadi-pledges.index') }}"><i class="fas fa-gift me-2"></i>Ahadi kwa Bwana</a>
                                    <a class="nav-link" href="{{ route('finance.budgets') }}"><i class="fas fa-wallet me-2"></i>Budgets</a>
                                    <a class="nav-link" href="{{ route('finance.expenses') }}"><i class="fas fa-receipt me-2"></i>Expenses</a>
                                    @if(auth()->user()->isSecretary() || auth()->user()->isPastor() || auth()->user()->isAdmin())
                                    <a class="nav-link" href="{{ route('reports.general-secretary') }}"><i class="fas fa-star me-2"></i>Gen Sec Report</a>
                                    @endif
                                </nav>
                            </div>
                            @endif
                            
                            {{-- Evangelism Leader Menu (check FIRST before branch user) --}}
                            @if(auth()->user()->isEvangelismLeader())
                            <div class="sb-sidenav-menu-heading">{{ __('common.evangelism_leader') }}</div>
                            <a class="nav-link" href="{{ route('evangelism-leader.dashboard') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                {{ __('common.dashboard') }}
                            </a>
                            <a class="nav-link" href="{{ route('evangelism-leader.register-member') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-user-plus"></i></div>
                                {{ __('common.register_member') }}
                            </a>
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseEvangelismReports" aria-expanded="false" aria-controls="collapseEvangelismReports">
                                <div class="sb-nav-link-icon"><i class="fas fa-file-alt"></i></div>
                                {{ __('common.community_reports') }}
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseEvangelismReports" aria-labelledby="headingEvangelismReports" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="{{ route('evangelism-leader.reports.index') }}">
                                        <i class="fas fa-list me-2"></i>{{ __('common.all_reports') }}
                                    </a>
                                    <a class="nav-link" href="{{ route('evangelism-leader.reports.create') }}">
                                        <i class="fas fa-plus me-2"></i>{{ __('common.create_report') }}
                                    </a>
                                </nav>
                            </div>
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseEvangelismTasks" aria-expanded="false" aria-controls="collapseEvangelismTasks">
                                <div class="sb-nav-link-icon"><i class="fas fa-tasks"></i></div>
                                {{ __('common.task_reports') }}
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseEvangelismTasks" aria-labelledby="headingEvangelismTasks" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="{{ route('evangelism-leader.tasks.index') }}">
                                        <i class="fas fa-list me-2"></i>{{ __('common.all_tasks') }}
                                    </a>
                                    <a class="nav-link" href="{{ route('evangelism-leader.tasks.create') }}">
                                        <i class="fas fa-plus me-2"></i>{{ __('common.create_task') }}
                                    </a>
                                </nav>
                            </div>
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseEvangelismIssues" aria-expanded="false" aria-controls="collapseEvangelismIssues">
                                <div class="sb-nav-link-icon"><i class="fas fa-exclamation-triangle"></i></div>
                                {{ __('common.report_issue') }}
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseEvangelismIssues" aria-labelledby="headingEvangelismIssues" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="{{ route('evangelism-leader.issues.index') }}">
                                        <i class="fas fa-list me-2"></i>{{ __('common.all_issues') }}
                                    </a>
                                    <a class="nav-link" href="{{ route('evangelism-leader.issues.create') }}">
                                        <i class="fas fa-plus me-2"></i>{{ __('common.report_issue') }}
                                    </a>
                                </nav>
                            </div>
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseChurchElderReports" aria-expanded="false" aria-controls="collapseChurchElderReports">
                                <div class="sb-nav-link-icon"><i class="fas fa-church"></i></div>
                                {{ __('common.church_elder_reports') }}
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseChurchElderReports" aria-labelledby="headingChurchElderReports" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="{{ route('evangelism-leader.church-elder-tasks.index') }}">
                                        <i class="fas fa-tasks me-2"></i>{{ __('common.church_elder_tasks') }}
                                    </a>
                                    <a class="nav-link" href="{{ route('evangelism-leader.church-elder-issues.index') }}">
                                        <i class="fas fa-exclamation-triangle me-2"></i>{{ __('common.church_elder_issues') }}
                                    </a>
                                </nav>
                            </div>
                            <a class="nav-link" href="{{ route('evangelism-leader.offerings.index') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-money-bill-wave"></i></div>
                                {{ __('common.community_offerings') }}
                            </a>
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseBranchServices" aria-expanded="false" aria-controls="collapseBranchServices">
                                <div class="sb-nav-link-icon"><i class="fas fa-church"></i></div>
                                {{ __('common.branch_services') }}
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseBranchServices" aria-labelledby="headingBranchServices" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="{{ route('evangelism-leader.branch-services.index') }}">
                                        <i class="fas fa-calendar-alt me-2"></i>Sunday Services
                                    </a>
                                    <a class="nav-link" href="{{ route('evangelism-leader.branch-offerings.index') }}">
                                        <i class="fas fa-money-bill-wave me-2"></i>Branch Offerings
                                    </a>
                                </nav>
                            </div>
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseEvangelismRequests" aria-expanded="false" aria-controls="collapseEvangelismRequests">
                                <div class="sb-nav-link-icon"><i class="fas fa-file-signature"></i></div>
                                Requests
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseEvangelismRequests" aria-labelledby="headingEvangelismRequests" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="{{ route('evangelism-leader.baptism-applications.index') }}">
                                        <i class="fas fa-water me-2"></i>Baptism Applications
                                    </a>
                                    <a class="nav-link" href="{{ route('evangelism-leader.return-to-fellowship-requests.index') }}">
                                        <i class="fas fa-door-open me-2"></i>Return to Fellowship
                                    </a>
                                    <a class="nav-link" href="{{ route('evangelism-leader.marriage-blessing-requests.index') }}">
                                        <i class="fas fa-heart me-2"></i>Marriage Blessing
                                    </a>
                                    <a class="nav-link" href="{{ route('evangelism-leader.church-wedding-requests.index') }}">
                                        <i class="fas fa-rings-wedding me-2"></i>Church Wedding
                                    </a>
                                </nav>
                            </div>
                            <a class="nav-link" href="{{ route('evangelism-leader.bereavement.index') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-heart-broken"></i></div>
                                Bereavement Management
                            </a>
                            <a class="nav-link" href="{{ route('evangelism-leader.finance.index') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-coins"></i></div>
                                Finance Management
                            </a>
                            <div class="sb-sidenav-menu-heading">Account</div>
                            <a class="nav-link" href="{{ route('leader.change-password') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-key"></i></div>
                                Change Password
                            </a>
                            @elseif(auth()->user()->isParishWorker())
                            <div class="sb-sidenav-menu-heading">Parish Worker</div>
                            <a class="nav-link" href="{{ route('parish-worker.dashboard') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                {{ __('common.dashboard') }}
                            </a>
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePWActivities" aria-expanded="false" aria-controls="collapsePWActivities">
                                <div class="sb-nav-link-icon"><i class="fas fa-walking"></i></div>
                                Activities
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapsePWActivities" aria-labelledby="headingPWActivities" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="{{ route('parish-worker.activities.index') }}">
                                        <i class="fas fa-list me-2"></i>My Activities
                                    </a>
                                    <a class="nav-link" href="{{ route('parish-worker.activities.create') }}">
                                        <i class="fas fa-plus me-2"></i>Record Activity
                                    </a>
                                </nav>
                            </div>
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePWCandles" aria-expanded="false" aria-controls="collapsePWCandles">
                                <div class="sb-nav-link-icon"><i class="fas fa-fire"></i></div>
                                Candle Inventory
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapsePWCandles" aria-labelledby="headingPWCandles" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="{{ route('parish-worker.candles.index') }}">
                                        <i class="fas fa-list-alt me-2"></i>Stock History
                                    </a>
                                    <a class="nav-link" href="{{ route('parish-worker.candles.create') }}">
                                        <i class="fas fa-plus-circle me-2"></i>New Transaction
                                    </a>
                                </nav>
                            </div>
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePWReports" aria-expanded="false" aria-controls="collapsePWReports">
                                <div class="sb-nav-link-icon"><i class="fas fa-file-alt"></i></div>
                                My Reports
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapsePWReports" aria-labelledby="headingPWReports" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="{{ route('parish-worker.reports.index') }}">
                                        <i class="fas fa-list me-2"></i>All Reports
                                    </a>
                                    <a class="nav-link" href="{{ route('parish-worker.reports.create') }}">
                                        <i class="fas fa-plus me-2"></i>Submit Report
                                    </a>
                                </nav>
                            </div>
                            <div class="sb-sidenav-menu-heading">Account</div>
                            <a class="nav-link" href="{{ route('leader.change-password') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-key"></i></div>
                                Change Password
                            </a>
                            @else
                            {{-- Branch & Usharika Dashboards --}}
                            @php
                                $userCampus = auth()->user()->getCampus();
                                $isBranchUser = $userCampus && !$userCampus->is_main_campus;
                                $isUsharikaAdmin = auth()->user()->isUsharikaAdmin();
                                $isSuperSecretary = auth()->user()->isSecretary() && $userCampus && $userCampus->is_main_campus;
                            @endphp
                            
                            
                            @if($isBranchUser && !auth()->user()->isChurchElder())
                            <div class="sb-sidenav-menu-heading">Branch</div>
                            <a class="nav-link" href="{{ route('branch.dashboard') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-sitemap"></i></div>
                                Branch Dashboard
                            </a>
                            @endif
                            
                            @if($isUsharikaAdmin)
                            <div class="sb-sidenav-menu-heading">Usharika</div>
                            <a class="nav-link" href="{{ route('usharika.dashboard') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-building"></i></div>
                                Usharika Dashboard
                            </a>
                            @endif
                            
                            {{-- Super Secretary: Branches & Communities --}}
                            @if($isSuperSecretary && !auth()->user()->isMember())
                            <div class="sb-sidenav-menu-heading">Branches & Communities</div>
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseBranchesCommunities" aria-expanded="false" aria-controls="collapseBranchesCommunities">
                                <div class="sb-nav-link-icon"><i class="fas fa-sitemap"></i></div>
                                Branches
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseBranchesCommunities" aria-labelledby="headingBranchesCommunities" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="{{ route('campuses.index') }}">
                                        <i class="fas fa-list me-2"></i>All Branches
                                    </a>
                                    <a class="nav-link" href="{{ route('usharika.dashboard') }}">
                                        <i class="fas fa-building me-2"></i>Usharika Dashboard
                                    </a>
                                </nav>
                            </div>
                            @endif
                            
                            {{-- Campuses & Communities Management --}}
                            @if((auth()->user()->isAdmin() || $isUsharikaAdmin) && !auth()->user()->isMember())
                            <div class="sb-sidenav-menu-heading">Organization</div>
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseCampuses" aria-expanded="false" aria-controls="collapseCampuses">
                                <div class="sb-nav-link-icon"><i class="fas fa-building"></i></div>
                                Campuses & Branches
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseCampuses" aria-labelledby="headingCampuses" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="{{ route('campuses.index') }}">
                                        <i class="fas fa-list me-2"></i>All Campuses
                                    </a>
                                    <a class="nav-link" href="{{ route('campuses.create') }}">
                                        <i class="fas fa-plus me-2"></i>Add Campus/Branch
                                    </a>
                                </nav>
                            </div>
                            @endif
                            
                            @if((!auth()->user()->isTreasurer() || auth()->user()->isAdmin()) && !auth()->user()->isMember() && !auth()->user()->isParishWorker() && !auth()->user()->isChurchElder())
                            <div class="sb-sidenav-menu-heading">Reports</div>
                            <a class="nav-link" href="{{ route('analytics.index') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-chart-line"></i></div>
                                Analytics
                            </a>
                            <a class="nav-link" href="{{ route('reports.overview') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-file-alt"></i></div>
                                All Reports
                            </a>
                            @if(auth()->user()->isSecretary() || auth()->user()->isPastor() || auth()->user()->isAdmin())
                            <a class="nav-link" href="{{ route('reports.general-secretary') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-star"></i></div>
                                Gen Sec Report
                            </a>
                            @endif
                            @if(auth()->user()->isPastor())
                            <a class="nav-link" href="{{ route('pastor.parish-worker.activities.index') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-walking"></i></div>
                                PW Activities
                            </a>
                            <a class="nav-link" href="{{ route('pastor.parish-worker.reports.index') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-file-invoice"></i></div>
                                PW Reports
                            </a>
                            <a class="nav-link" href="{{ route('pastor.parish-worker.candles.index') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-fire"></i></div>
                                Candle Inventory
                            </a>
                            @endif
                            @endif
                            
                            @if(!auth()->user()->isMember() && !auth()->user()->isParishWorker())
                            <div class="sb-sidenav-menu-heading">Account</div>
                            <a class="nav-link" href="{{ route('leader.change-password') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-key"></i></div>
                                Change Password
                            </a>
                            @endif
                            
                            @if(auth()->user()->isAdmin())
                            <div class="sb-sidenav-menu-heading">Settings</div>
                            <a class="nav-link" href="{{ route('settings.index') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-cog"></i></div>
                                System Settings
                            </a>
                            @endif
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
                    @yield('content')
                </main>
            </div>
            <footer class="bg-dark text-light py-4 mt-auto" style="width: 100%; margin-left: 0;">
                <div class="container-fluid px-4">
                    <div class="row align-items-center">
                        <!-- Left Side -->
                        <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                            <small>&copy; <span id="year"></span> Waumini Link — Version 1.0</small>
                        </div>

                        <!-- Right Side -->
                        <div class="col-md-6 text-center text-md-end">
                            <small><span style="color: #ffffff !important;">Powered by</span> <a href="https://emca.tech/#" class="text-decoration-none fw-bold" style="color: #940000 !important;">EmCa Technologies</a></small>
                        </div>
                    </div>
                </div>
            </footer>

        <!-- jQuery (required for Select2) -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        
        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        
        <!-- Select2 CSS and JS -->
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        
        <!-- Custom Scripts -->
        <script>
            // Current date and time
            function updateDateTime() {
                const now = new Date();
                const dateOptions = { 
                    weekday: 'long', 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric' 
                };
                const timeOptions = { 
                    hour: '2-digit', 
                    minute: '2-digit', 
                    second: '2-digit' 
                };
                
                const dateElement = document.getElementById('current-date');
                const timeElement = document.getElementById('current-time');
                
                if (dateElement) {
                    dateElement.textContent = now.toLocaleDateString('en-US', dateOptions);
                }
                if (timeElement) {
                    timeElement.textContent = now.toLocaleTimeString('en-US', timeOptions);
                }
            }
            
            // Update date and time every second
            setInterval(updateDateTime, 1000);
            updateDateTime();
            
            // Update year in footer
            document.getElementById('year').textContent = new Date().getFullYear();
            
            // Notification functionality
            function loadNotifications() {
                console.log('Loading notifications...');
                fetch('/debug-notifications')
                    .then(response => {
                        console.log('Response status:', response.status);
                        return response.json();
                    })
                    .then(response => {
                        console.log('Notification response:', response);
                        if (response.success && response.data) {
                            const data = response.data;
                            // Store data globally for modal access
                            window.currentNotificationData = data;
                            console.log('Data received:', data);
                            console.log('Counts:', data.counts);
                            console.log('Events array:', data.events);
                            console.log('Celebrations array:', data.celebrations);
                            console.log('Services array:', data.services);
                            
                            // Update counts
                            const eventsCountEl = document.getElementById('eventsCount');
                            const celebrationsCountEl = document.getElementById('celebrationsCount');
                            const servicesCountEl = document.getElementById('servicesCount');
                            const pendingApprovalsCountEl = document.getElementById('pendingApprovalsCount');
                            const paymentsNeedingVerificationCountEl = document.getElementById('paymentsNeedingVerificationCount');
                            
                            console.log('Elements found:', {
                                eventsCount: !!eventsCountEl,
                                celebrationsCount: !!celebrationsCountEl,
                                servicesCount: !!servicesCountEl,
                                pendingApprovalsCount: !!pendingApprovalsCountEl,
                                paymentsNeedingVerificationCount: !!paymentsNeedingVerificationCountEl
                            });
                            
                            if (eventsCountEl) eventsCountEl.textContent = data.counts.events || 0;
                            if (celebrationsCountEl) celebrationsCountEl.textContent = data.counts.celebrations || 0;
                            if (servicesCountEl) servicesCountEl.textContent = data.counts.services || 0;
                            if (pendingApprovalsCountEl) pendingApprovalsCountEl.textContent = data.counts.pending_approvals || 0;
                            if (paymentsNeedingVerificationCountEl) paymentsNeedingVerificationCountEl.textContent = data.counts.payments_needing_verification || 0;
                            
                            // Update total notification count
                            const totalCount = data.counts.total || 0;
                            const badge = document.getElementById('notificationBadge');
                            console.log('Badge element found:', !!badge);
                            if (badge) {
                                badge.textContent = totalCount;
                                badge.style.display = totalCount > 0 ? 'inline' : 'none';
                                console.log('Badge updated with count:', totalCount);
                            } else {
                                console.log('Badge element not found!');
                            }
                            
                            console.log('Updated counts:', {
                                events: data.counts.events,
                                celebrations: data.counts.celebrations,
                                services: data.counts.services,
                                total: data.counts.total
                            });
                            
                            // Update events list
                            const eventsList = document.getElementById('eventsList');
                            console.log('Events list element found:', !!eventsList);
                            if (eventsList && data.events) {
                                console.log('Updating events list with', data.events.length, 'events');
                                eventsList.innerHTML = generateEventList(data.events);
                                console.log('Events list updated');
                            }
                            
                            // Update celebrations list
                            const celebrationsList = document.getElementById('celebrationsList');
                            console.log('Celebrations list element found:', !!celebrationsList);
                            if (celebrationsList && data.celebrations) {
                                console.log('Updating celebrations list with', data.celebrations.length, 'celebrations');
                                celebrationsList.innerHTML = generateCelebrationList(data.celebrations);
                                console.log('Celebrations list updated');
                            }
                            
                            // Update services list
                            const servicesList = document.getElementById('servicesList');
                            console.log('Services list element found:', !!servicesList);
                            if (servicesList && data.services) {
                                console.log('Updating services list with', data.services.length, 'services');
                                servicesList.innerHTML = generateServiceList(data.services);
                                console.log('Services list updated');
                            }
                            
                            // Update pending approvals list
                            const pendingApprovalsList = document.getElementById('pendingApprovalsList');
                            if (pendingApprovalsList && data.pending_approvals) {
                                pendingApprovalsList.innerHTML = generatePendingApprovalsList(data.pending_approvals);
                            }
                            
                            // Update payments needing verification list
                            const paymentsNeedingVerificationList = document.getElementById('paymentsNeedingVerificationList');
                            if (paymentsNeedingVerificationList && data.payments_needing_verification) {
                                paymentsNeedingVerificationList.innerHTML = generatePaymentsNeedingVerificationList(data.payments_needing_verification);
                            }
                        } else {
                            console.log('No data or success false:', response);
                        }
                    })
                    .catch(error => {
                        console.error('Error loading notifications:', error);
                        // Fallback: show test data
                        const eventsCountEl = document.getElementById('eventsCount');
                        const celebrationsCountEl = document.getElementById('celebrationsCount');
                        const servicesCountEl = document.getElementById('servicesCount');
                        const pendingApprovalsCountEl = document.getElementById('pendingApprovalsCount');
                        
                        if (eventsCountEl) eventsCountEl.textContent = '0';
                        if (celebrationsCountEl) celebrationsCountEl.textContent = '0';
                        if (servicesCountEl) servicesCountEl.textContent = '0';
                        if (pendingApprovalsCountEl) pendingApprovalsCountEl.textContent = '0';
                        
                        const paymentsNeedingVerificationCountEl = document.getElementById('paymentsNeedingVerificationCount');
                        if (paymentsNeedingVerificationCountEl) paymentsNeedingVerificationCountEl.textContent = '0';
                        
                        const badge = document.getElementById('notificationBadge');
                        if (badge) {
                            badge.textContent = '0';
                            badge.style.display = 'none';
                        }
                    });
            }
            
            // Generate HTML for events list
            function generateEventList(events) {
                console.log('Generating events list for:', events);
                if (!events || events.length === 0) {
                    console.log('No events found, showing empty message');
                    return '<div class="empty-notification-state"><i class="fas fa-calendar-times"></i><span>No upcoming events</span></div>';
                }
                
                console.log('Processing', events.length, 'events');
                return events.map((event, index) => {
                    const eventDate = new Date(event.date).toLocaleDateString('en-US', {
                        weekday: 'short',
                        month: 'short',
                        day: 'numeric'
                    });
                    const timeText = event.hours_remaining !== null ? 
                        `${event.hours_remaining}h left` : 
                        `${event.days_remaining}d left`;
                    
                    // Format time properly
                    const formatTime = (timeStr) => {
                        if (!timeStr || timeStr === 'TBD') return 'TBD';
                        try {
                            // Handle ISO format
                            if (timeStr.includes('T')) {
                                const time = new Date(timeStr);
                                return time.toLocaleTimeString('en-US', { 
                                    hour: '2-digit', 
                                    minute: '2-digit',
                                    hour12: true 
                                });
                            }
                            // Handle HH:MM:SS format
                            if (timeStr.includes(':')) {
                                const [hours, minutes] = timeStr.split(':');
                                const time = new Date();
                                time.setHours(parseInt(hours), parseInt(minutes), 0);
                                return time.toLocaleTimeString('en-US', { 
                                    hour: '2-digit', 
                                    minute: '2-digit',
                                    hour12: true 
                                });
                            }
                            return timeStr;
                        } catch (e) {
                            return 'TBD';
                        }
                    };
                    
                    return `
                        <div class="notification-item" style="animation-delay: ${index * 0.1}s;" onclick="showEventDetails(${event.id}, 'event')">
                            <div class="notification-item-content">
                                <div class="notification-icon bg-primary">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <div class="notification-details">
                                    <div class="notification-title">${event.title}</div>
                                    <div class="notification-meta">
                                        <span class="meta-item">
                                            <i class="fas fa-calendar"></i>
                                            ${eventDate}
                                        </span>
                                        <span class="meta-item">
                                            <i class="fas fa-clock"></i>
                                            ${formatTime(event.time)}
                                        </span>
                                    </div>
                                    <div class="notification-info">
                                        <span class="info-item">
                                            <i class="fas fa-map-marker-alt"></i>
                                            ${event.venue}
                                        </span>
                                        ${event.speaker ? `<span class="info-item"><i class="fas fa-user"></i>${event.speaker}</span>` : ''}
                                    </div>
                                    <div class="notification-badge">
                                        <span class="time-badge bg-primary">${timeText}</span>
                                    </div>
                                </div>
                                <div class="notification-arrow">
                                    <i class="fas fa-chevron-right"></i>
                                </div>
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
                    
                    // Format time properly
                    const formatTime = (timeStr) => {
                        if (!timeStr || timeStr === 'TBD') return 'TBD';
                        try {
                            // Handle ISO format
                            if (timeStr.includes('T')) {
                                const time = new Date(timeStr);
                                return time.toLocaleTimeString('en-US', { 
                                    hour: '2-digit', 
                                    minute: '2-digit',
                                    hour12: true 
                                });
                            }
                            // Handle HH:MM:SS format
                            if (timeStr.includes(':')) {
                                const [hours, minutes] = timeStr.split(':');
                                const time = new Date();
                                time.setHours(parseInt(hours), parseInt(minutes), 0);
                                return time.toLocaleTimeString('en-US', { 
                                    hour: '2-digit', 
                                    minute: '2-digit',
                                    hour12: true 
                                });
                            }
                            return timeStr;
                        } catch (e) {
                            return 'TBD';
                        }
                    };
                    
                    return `
                        <div class="notification-item" style="animation-delay: ${index * 0.1}s;" onclick="showEventDetails(${celebration.id}, 'celebration')">
                            <div class="notification-item-content">
                                <div class="notification-icon bg-warning">
                                    <i class="fas fa-birthday-cake"></i>
                                </div>
                                <div class="notification-details">
                                    <div class="notification-title">${celebration.title}</div>
                                    <div class="notification-meta">
                                        <span class="meta-item">
                                            <i class="fas fa-user"></i>
                                            ${celebration.celebrant}
                                        </span>
                                        <span class="meta-item">
                                            <i class="fas fa-calendar"></i>
                                            ${celebrationDate}
                                        </span>
                                    </div>
                                    <div class="notification-info">
                                        <span class="info-item">
                                            <i class="fas fa-clock"></i>
                                            ${formatTime(celebration.time)}
                                        </span>
                                        <span class="info-item">
                                            <i class="fas fa-map-marker-alt"></i>
                                            ${celebration.venue}
                                        </span>
                                    </div>
                                    <div class="notification-badge">
                                        <span class="time-badge bg-warning">${timeText}</span>
                                    </div>
                                </div>
                                <div class="notification-arrow">
                                    <i class="fas fa-chevron-right"></i>
                                </div>
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
                    
                    // Format time properly
                    const formatTime = (timeStr) => {
                        if (!timeStr || timeStr === 'TBD') return 'TBD';
                        try {
                            // Handle ISO format
                            if (timeStr.includes('T')) {
                                const time = new Date(timeStr);
                                return time.toLocaleTimeString('en-US', { 
                                    hour: '2-digit', 
                                    minute: '2-digit',
                                    hour12: true 
                                });
                            }
                            // Handle HH:MM:SS format
                            if (timeStr.includes(':')) {
                                const [hours, minutes] = timeStr.split(':');
                                const time = new Date();
                                time.setHours(parseInt(hours), parseInt(minutes), 0);
                                return time.toLocaleTimeString('en-US', { 
                                    hour: '2-digit', 
                                    minute: '2-digit',
                                    hour12: true 
                                });
                            }
                            return timeStr;
                        } catch (e) {
                            return 'TBD';
                        }
                    };
                    
                    return `
                        <div class="notification-item" style="animation-delay: ${index * 0.1}s;" onclick="showEventDetails(${service.id}, 'service')">
                            <div class="notification-item-content">
                                <div class="notification-icon bg-success">
                                    <i class="fas fa-church"></i>
                                </div>
                                <div class="notification-details">
                                    <div class="notification-title">${service.title}</div>
                                    <div class="notification-meta">
                                        <span class="meta-item">
                                            <i class="fas fa-calendar"></i>
                                            ${serviceDate}
                                        </span>
                                        <span class="meta-item">
                                            <i class="fas fa-clock"></i>
                                            ${formatTime(service.time)}
                                        </span>
                                    </div>
                                    <div class="notification-info">
                                        <span class="info-item">
                                            <i class="fas fa-map-marker-alt"></i>
                                            ${service.venue}
                                        </span>
                                        ${service.speaker ? `<span class="info-item"><i class="fas fa-user"></i>${service.speaker}</span>` : ''}
                                    </div>
                                    ${service.theme ? `<div class="notification-theme"><i class="fas fa-quote-left"></i>${service.theme}</div>` : ''}
                                    <div class="notification-badge">
                                        <span class="time-badge bg-success">${timeText}</span>
                                    </div>
                                </div>
                                <div class="notification-arrow">
                                    <i class="fas fa-chevron-right"></i>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');
            }
            
            // Generate HTML for pending approvals list
            function generatePendingApprovalsList(pendingApprovals) {
                if (!pendingApprovals || pendingApprovals.total === 0) {
                    return '<div class="empty-notification-state"><i class="fas fa-check-circle"></i><span>No pending approvals</span></div>';
                }
                
                const items = [];
                
                if (pendingApprovals.tithes > 0) {
                    items.push({
                        type: 'Tithes',
                        count: pendingApprovals.tithes,
                        icon: 'fa-coins',
                        color: 'success',
                        url: '/finance/approval/dashboard#tithes'
                    });
                }
                
                if (pendingApprovals.offerings > 0) {
                    items.push({
                        type: 'Offerings',
                        count: pendingApprovals.offerings,
                        icon: 'fa-gift',
                        color: 'primary',
                        url: '/finance/approval/dashboard#offerings'
                    });
                }
                
                if (pendingApprovals.donations > 0) {
                    items.push({
                        type: 'Donations',
                        count: pendingApprovals.donations,
                        icon: 'fa-heart',
                        color: 'info',
                        url: '/finance/approval/dashboard#donations'
                    });
                }
                
                if (pendingApprovals.expenses > 0) {
                    items.push({
                        type: 'Expenses',
                        count: pendingApprovals.expenses,
                        icon: 'fa-receipt',
                        color: 'danger',
                        url: '/finance/approval/dashboard#expenses'
                    });
                }
                
                if (pendingApprovals.budgets > 0) {
                    items.push({
                        type: 'Budgets',
                        count: pendingApprovals.budgets,
                        icon: 'fa-wallet',
                        color: 'warning',
                        url: '/finance/approval/dashboard#budgets'
                    });
                }
                
                if (pendingApprovals.pledge_payments > 0) {
                    items.push({
                        type: 'Pledge Payments',
                        count: pendingApprovals.pledge_payments,
                        icon: 'fa-handshake',
                        color: 'secondary',
                        url: '/finance/approval/dashboard#pledge-payments'
                    });
                }
                
                return items.map((item, index) => {
                    return `
                        <div class="notification-item" style="animation-delay: ${index * 0.1}s;" onclick="window.location.href='${item.url}'">
                            <div class="notification-item-content">
                                <div class="notification-icon bg-${item.color}">
                                    <i class="fas ${item.icon}"></i>
                                </div>
                                <div class="notification-details">
                                    <div class="notification-title">${item.type}</div>
                                    <div class="notification-meta">
                                        <span class="meta-item">
                                            <i class="fas fa-clock"></i>
                                            Pending Approval
                                        </span>
                                    </div>
                                    <div class="notification-badge">
                                        <span class="time-badge bg-${item.color}">${item.count} ${item.count === 1 ? 'record' : 'records'}</span>
                                    </div>
                                </div>
                                <div class="notification-arrow">
                                    <i class="fas fa-chevron-right"></i>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');
            }
            
            // Generate HTML for payments needing verification list
            function generatePaymentsNeedingVerificationList(payments) {
                if (!payments || payments.length === 0) {
                    return '<div class="empty-notification-state"><i class="fas fa-check-circle"></i><span>No payments needing verification</span></div>';
                }
                
                return payments.map((payment, index) => {
                    const date = new Date(payment.expense_date);
                    const formattedDate = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                    const formattedAmount = new Intl.NumberFormat('en-US', {
                        style: 'currency',
                        currency: 'TZS',
                        minimumFractionDigits: 0
                    }).format(payment.amount);
                    
                    return `
                        <div class="notification-item" style="animation-delay: ${index * 0.1}s;" onclick="window.location.href='/finance/expenses'">
                            <div class="notification-item-content">
                                <div class="notification-icon bg-success">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                                <div class="notification-details">
                                    <div class="notification-title">${payment.expense_name}</div>
                                    <div class="notification-info">
                                        <span class="info-item">
                                            <i class="fas fa-wallet"></i>
                                            ${payment.budget_name}
                                        </span>
                                        <span class="info-item">
                                            <i class="fas fa-tag"></i>
                                            ${payment.category}
                                        </span>
                                    </div>
                                    <div class="notification-info">
                                        <span class="info-item">
                                            <i class="fas fa-money-bill-wave"></i>
                                            ${formattedAmount}
                                        </span>
                                        <span class="info-item">
                                            <i class="fas fa-calendar"></i>
                                            ${formattedDate}
                                        </span>
                                    </div>
                                    <div class="notification-badge">
                                        <span class="time-badge bg-success">Needs Verification</span>
                                    </div>
                                </div>
                                <div class="notification-arrow">
                                    <i class="fas fa-chevron-right"></i>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');
            }
            
            // Function to show event details in a modal
            function showEventDetails(id, type) {
                console.log('Showing details for', type, 'with ID:', id);
                
                // Create modal if it doesn't exist
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
                
                // Show modal
                const bsModal = new bootstrap.Modal(modal);
                bsModal.show();
                
                // Load event details
                loadEventDetails(id, type);
            }
            
            // Function to load event details
            function loadEventDetails(id, type) {
                const modalBody = document.getElementById('eventDetailsBody');
                const modalTitle = document.getElementById('eventDetailsTitle');
                
                // Set title based on type
                const titles = {
                    'event': 'Event Details',
                    'celebration': 'Celebration Details', 
                    'service': 'Service Details'
                };
                modalTitle.textContent = titles[type] || 'Details';
                
                // Show loading
                modalBody.innerHTML = `
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Loading details...</p>
                    </div>
                `;
                
                // Load actual data from the notification data
                setTimeout(() => {
                    // Find the event data from the current notification data
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
                    
                    // Local time formatter (shared with lists)
                    const formatTime = (timeStr) => {
                        if (!timeStr || timeStr === 'TBD') return 'TBD';
                        try {
                            if (typeof timeStr !== 'string') return String(timeStr);
                            // ISO like or with date component
                            if (timeStr.includes('T') || /\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}/.test(timeStr)) {
                                const time = new Date(timeStr);
                                return time.toLocaleTimeString('en-US', { 
                                    hour: '2-digit', 
                                    minute: '2-digit',
                                    hour12: true 
                                });
                            }
                            // HH:MM(:SS)
                            if (/^\d{2}:\d{2}/.test(timeStr)) {
                                const [hours, minutes] = timeStr.split(':');
                                const d = new Date();
                                d.setHours(parseInt(hours), parseInt(minutes), 0, 0);
                                return d.toLocaleTimeString('en-US', { 
                                    hour: '2-digit', 
                                    minute: '2-digit',
                                    hour12: true 
                                });
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

                        // Build a correct time display, especially for Sunday service
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
                var sidebarToggle = document.getElementById('sidebarToggle');
                var layoutSidenav = document.getElementById('layoutSidenav');
                
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
                            
                            var isToggled = layoutSidenav ? layoutSidenav.classList.contains('sb-sidenav-toggled') : document.body.classList.contains('sb-sidenav-toggled');
                            localStorage.setItem('sb-sidebar-toggle', isToggled ? 'true' : 'false');
                            
                            return false;
                        }, true);
                        
                        // On mobile, always start with sidebar closed
                        // On mobile, sb-sidenav-toggled means OPEN, so we REMOVE it to close
                        function ensureSidebarClosed() {
                            if (window.innerWidth <= 768) {
                                if (layoutSidenav) {
                                    layoutSidenav.classList.remove('sb-sidenav-toggled');
                                }
                                document.body.classList.remove('sb-sidenav-toggled');
                                localStorage.setItem('sb-sidebar-toggle', 'false');
                            }
                        }
                        
                        // Ensure sidebar starts closed on initial load for mobile
                        ensureSidebarClosed();
                        
                        // On desktop, restore saved state
                        if (window.innerWidth > 768) {
                            var savedState = localStorage.getItem('sb-sidebar-toggle');
                            if (savedState === 'true') {
                                if (layoutSidenav) {
                                    layoutSidenav.classList.add('sb-sidenav-toggled');
                                }
                                document.body.classList.add('sb-sidenav-toggled');
                            }
                        }
                    }
                }
                
                // Mobile sidebar behavior: Close sidebar when navigation links are clicked
                function closeSidebarOnMobile() {
                    if (window.innerWidth <= 768) {
                        if (layoutSidenav) {
                            layoutSidenav.classList.remove('sb-sidenav-toggled');
                        }
                        document.body.classList.remove('sb-sidenav-toggled');
                        localStorage.setItem('sb-sidebar-toggle', 'false');
                    }
                }
                
                // Close sidebar when navigation links are clicked on mobile
                var sidebarLinks = document.querySelectorAll('#sidenavAccordion .nav-link[href]');
                for (var i = 0; i < sidebarLinks.length; i++) {
                    var link = sidebarLinks[i];
                    var href = link.getAttribute('href');
                    
                    // Skip collapse toggles and empty links
                    if (href === '#' || link.hasAttribute('data-bs-toggle')) {
                        continue;
                    }
                    
                    (function(currentLink) {
                        currentLink.addEventListener('click', function(e) {
                            if (window.innerWidth <= 768) {
                                // Close sidebar immediately
                                closeSidebarOnMobile();
                                
                                // Close multiple times to ensure it stays closed
                                setTimeout(closeSidebarOnMobile, 10);
                                setTimeout(closeSidebarOnMobile, 50);
                                setTimeout(closeSidebarOnMobile, 100);
                                setTimeout(closeSidebarOnMobile, 200);
                                
                                // Don't prevent default - allow navigation to happen
                            }
                        }, false); // Use bubble phase so link navigation works
                    })(link);
                }
                
                // Also close sidebar when page loads on mobile (in case it was open)
                if (window.innerWidth <= 768) {
                    closeSidebarOnMobile();
                    // Close again after a short delay to ensure it stays closed
                    setTimeout(closeSidebarOnMobile, 100);
                    setTimeout(closeSidebarOnMobile, 300);
                }
                
                // Close sidebar when page becomes visible (after navigation)
                document.addEventListener('visibilitychange', function() {
                    if (!document.hidden && window.innerWidth <= 768) {
                        closeSidebarOnMobile();
                    }
                });
                
                // Close sidebar on page show (handles back/forward navigation)
                window.addEventListener('pageshow', function(event) {
                    if (window.innerWidth <= 768) {
                        closeSidebarOnMobile();
                        setTimeout(closeSidebarOnMobile, 50);
                    }
                });
                
                // Prevent sidebar from reopening after navigation
                window.addEventListener('load', function() {
                    if (window.innerWidth <= 768) {
                        closeSidebarOnMobile();
                        setTimeout(closeSidebarOnMobile, 100);
                    }
                });
            });
        </script>
        
        {{-- Prevent back navigation to login page --}}
        <script>
            (function() {
                // Store that we're on a dashboard/authenticated page
                sessionStorage.setItem('isAuthenticated', 'true');
                
                // Prevent back navigation to login page
                if (window.history && window.history.pushState) {
                    // Replace the login page in history with current dashboard page
                    // This removes login from browser history
                    window.history.replaceState({ page: 'dashboard', preventBack: true }, '', window.location.href);
                    
                    // Add a new state to prevent going back
                    window.history.pushState({ page: 'dashboard', preventBack: true }, '', window.location.href);
                    
                    // Listen for back button
                    window.addEventListener('popstate', function(event) {
                        // If we're authenticated and trying to go back
                        if (sessionStorage.getItem('isAuthenticated') === 'true') {
                            // Check if the state we're going to is login-related
                            const state = event.state;
                            
                            // If no state or state indicates we should prevent back, push forward
                            if (!state || state.preventBack) {
                                // Push current page forward again to prevent going back
                                window.history.pushState({ page: 'dashboard', preventBack: true }, '', window.location.href);
                            }
                        }
                    });
                }
            })();
        </script>
        
        {{-- Auto-logout on inactivity (3 minutes) with 1-minute warning --}}
        <script>
            (function() {
                // Configuration
                const INACTIVITY_TIMEOUT = 3 * 60 * 1000; // 3 minutes in milliseconds
                const WARNING_TIME = 1 * 60 * 1000; // 1 minute before logout (warning time)
                const LOGOUT_URL = '{{ route("logout") }}';
                
                let inactivityTimer;
                let warningTimer;
                let warningShown = false;
                let countdownInterval;
                let remainingSeconds = 60;
                
                // Function to reset timers
                function resetTimers() {
                    // Clear existing timers
                    clearTimeout(inactivityTimer);
                    clearTimeout(warningTimer);
                    clearInterval(countdownInterval);
                    
                    // Reset warning flag
                    warningShown = false;
                    remainingSeconds = 60;
                    
                    // Set new inactivity timer
                    inactivityTimer = setTimeout(function() {
                        // Show warning when 1 minute remains
                        showWarning();
                        
                        // Set logout timer
                        warningTimer = setTimeout(function() {
                            logout();
                        }, WARNING_TIME);
                    }, INACTIVITY_TIMEOUT - WARNING_TIME);
                }
                
                // Function to show warning with countdown
                function showWarning() {
                    if (warningShown) return;
                    warningShown = true;
                    remainingSeconds = 60;
                    
                    // Show SweetAlert with countdown
                    Swal.fire({
                        title: 'Session Timeout Warning',
                        html: 'You have been inactive for 2 minutes.<br>You will be logged out in <strong id="inactivityCountdown" style="font-size: 1.5em; color: #d33;">60</strong> seconds.<br><br>Click "Stay Logged In" to continue your session.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Stay Logged In',
                        cancelButtonText: 'Logout Now',
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        timer: WARNING_TIME,
                        timerProgressBar: true,
                        didOpen: () => {
                            // Update countdown every second
                            const countdownElement = document.getElementById('inactivityCountdown');
                            if (countdownElement) {
                                countdownInterval = setInterval(function() {
                                    remainingSeconds--;
                                    if (remainingSeconds > 0) {
                                        countdownElement.textContent = remainingSeconds;
                                    } else {
                                        clearInterval(countdownInterval);
                                    }
                                }, 1000);
                            }
                        },
                        willClose: () => {
                            clearInterval(countdownInterval);
                        }
                    }).then((result) => {
                        clearInterval(countdownInterval);
                        
                        if (result.dismiss === Swal.DismissReason.timer) {
                            // Timeout - logout
                            logout();
                        } else if (result.isConfirmed) {
                            // User clicked "Stay Logged In" - reset timers
                            resetTimers();
                        } else if (result.isDismissed) {
                            // User clicked "Logout Now"
                            logout();
                        }
                    });
                }
                
                // Function to logout
                function logout() {
                    clearTimeout(inactivityTimer);
                    clearTimeout(warningTimer);
                    clearInterval(countdownInterval);
                    
                    // Create a form and submit to logout
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = LOGOUT_URL;
                    
                    // Add CSRF token
                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    if (csrfToken) {
                        const csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        csrfInput.value = csrfToken.getAttribute('content');
                        form.appendChild(csrfInput);
                    }
                    
                    document.body.appendChild(form);
                    form.submit();
                }
                
                // Events that indicate user activity
                const activityEvents = [
                    'mousedown',
                    'mousemove',
                    'keypress',
                    'scroll',
                    'touchstart',
                    'click',
                    'keydown'
                ];
                
                // Add event listeners for user activity
                activityEvents.forEach(function(eventName) {
                    document.addEventListener(eventName, function() {
                        resetTimers();
                    }, true);
                });
                
                // Initialize timer on page load
                resetTimers();
                
                // Also reset on visibility change (when user switches tabs back)
                document.addEventListener('visibilitychange', function() {
                    if (!document.hidden) {
                        resetTimers();
                    }
                });
            })();
        </script>
        
        {{-- Global AJAX error handler for session expiration and CSRF token --}}
        <script>
            (function() {
                // Function to refresh CSRF token
                async function refreshCsrfToken() {
                    try {
                        // Try to get new token from meta tag (if page was refreshed)
                        const metaToken = document.querySelector('meta[name="csrf-token"]');
                        if (metaToken) {
                            return metaToken.getAttribute('content');
                        }
                        // If meta tag doesn't exist, reload page to get new token
                        return null;
                    } catch (e) {
                        return null;
                    }
                }
                
                // Function to handle session expiration
                function handleSessionExpired(message) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Session Expired',
                            text: message || 'Your session has expired. Please log in again.',
                            confirmButtonText: 'Go to Login',
                            confirmButtonColor: '#3085d6',
                            allowOutsideClick: false,
                            allowEscapeKey: false
                        }).then(() => {
                            window.location.href = '{{ route("login") }}';
                        });
                    } else {
                        alert(message || 'Your session has expired. Please log in again.');
                        window.location.href = '{{ route("login") }}';
                    }
                }
                
                // Function to handle CSRF token expired (419)
                function handleCsrfExpired() {
                    // Silently refresh the page to get a new CSRF token
                    // This is better than showing an error since the token just needs to be refreshed
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
                        
                        // Reload page after a short delay to get new CSRF token
                        setTimeout(() => {
                            window.location.reload();
                        }, 500);
                    } else {
                        // Fallback: just reload
                        window.location.reload();
                    }
                }
                
                // Intercept fetch requests to handle 401 and 419 responses
                const originalFetch = window.fetch;
                window.fetch = function(...args) {
                    return originalFetch.apply(this, args)
                        .then(async response => {
                            const url = args[0] || '';
                            const isLoginPage = typeof url === 'string' && url.includes('/login');
                            
                            // Check for 419 CSRF Token Mismatch
                            if (response.status === 419) {
                                // Try to parse response to see if it has redirect info
                                try {
                                    const clonedResponse = response.clone();
                                    const data = await clonedResponse.json();
                                    
                                    // If response has redirect property, go to login
                                    if (data.redirect) {
                                        window.location.href = data.redirect;
                                        return response;
                                    }
                                } catch (e) {
                                    // If JSON parsing fails, continue with redirect
                                }
                                
                                // For 419 errors, always redirect to login page
                                window.location.href = '{{ route("login") }}';
                                return response;
                            }
                            
                            // Check for 401 Unauthorized (session expired)
                            if (response.status === 401 && !isLoginPage) {
                                // Clone response to read it without consuming
                                const clonedResponse = response.clone();
                                
                                // Try to parse JSON response for custom message
                                clonedResponse.json().then(data => {
                                    handleSessionExpired(data.message);
                                }).catch(() => {
                                    // If JSON parsing fails, use default message
                                    handleSessionExpired();
                                });
                            }
                            
                            return response;
                        });
                };
                
                // Also handle jQuery AJAX if it's being used
                if (typeof jQuery !== 'undefined') {
                    $(document).ajaxError(function(event, xhr, settings) {
                        const isLoginPage = settings.url && settings.url.includes('/login');
                        
                        // Handle 419 CSRF Token Mismatch - always redirect to login
                        if (xhr.status === 419) {
                            try {
                                const data = JSON.parse(xhr.responseText);
                                if (data.redirect) {
                                    window.location.href = data.redirect;
                                    return;
                                }
                            } catch (e) {
                                // If JSON parsing fails, continue with redirect
                            }
                            
                            // Always redirect to login for 419 errors
                            window.location.href = '{{ route("login") }}';
                            return;
                        }
                        
                        // Handle 401 Unauthorized (session expired)
                        if (xhr.status === 401 && !isLoginPage) {
                            let message = 'Your session has expired. Please log in again.';
                            
                            try {
                                const data = JSON.parse(xhr.responseText);
                                if (data.message) {
                                    message = data.message;
                                }
                            } catch (e) {
                                // Use default message
                            }
                            
                            handleSessionExpired(message);
                        }
                    });
                }
            })();
        </script>
        
        @yield('modals')
        
        @yield('scripts')
    </body>
</html>