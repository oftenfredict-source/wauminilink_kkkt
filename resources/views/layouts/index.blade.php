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
<html lang="en">
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
        <style>
            /* Dynamic theme color application */
            @php
                $themeColors = [
                    'waumini' => '#17082d',
                    'primary' => '#0d6efd',
                    'secondary' => '#6c757d',
                    'success' => '#198754',
                    'danger' => '#dc3545',
                    'warning' => '#ffc107',
                    'info' => '#0dcaf0'
                ];
                $selectedColor = $themeColors[$themeColor] ?? $themeColors['waumini'];
                // Use a different color for cards/buttons, but keep sidebar as #17082d
                $cardColor = '#4a5568'; // Nice gray-blue color for cards
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
                background: linear-gradient(180deg, #17082d 0%, #17082ddd 100%) !important;
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
            @elseif($sidebarStyle === 'primary')
                .sb-topnav {
                    background: linear-gradient(180deg, {{ $selectedColor }} 0%, {{ $selectedColor }}dd 100%) !important;
                }
            @else
                .sb-topnav {
                    background-color: #212529 !important;
                }
            @endif
            
            /* Ensure card headers are visible */
            .card-header.bg-primary {
                padding: 0.75rem 1.25rem !important;
                border-bottom: 1px solid rgba(255,255,255,0.2) !important;
            }
            .card-header.bg-primary .badge {
                background-color: #f8f9fa !important;
                color: #0d6efd !important;
            }
            .card-header.bg-primary i {
                color: white !important;
            }
            .card-header.bg-primary strong {
                color: white !important;
                font-weight: 600 !important;
            }
            
            /* Ensure all card headers have proper visibility */
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
                background-color: #0d6efd !important;
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
                color: white !important;
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
                color: #0d6efd !important;
                font-weight: 700 !important;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
            }
            
            /* Custom btn-white class for better visibility on colored backgrounds */
            .btn-white {
                background-color: white !important;
                color: #0d6efd !important;
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
                color: #0d6efd !important;
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
                background-color: #17082d !important;
            }
            
            .sb-sidenav .nav-link {
                color: white !important;
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
                letter-spacing: 0.8px !important;
                font-size: 0.75rem !important;
                padding: 0.75rem 1rem 0.25rem 1rem !important;
                background-color: rgba(255, 255, 255, 0.1) !important;
                border-radius: 4px !important;
                margin: 0.5rem 0.5rem 0.25rem 0.5rem !important;
            }
            
            .sb-sidenav .sb-nav-link-icon {
                color: white !important;
            }
            
            .sb-sidenav .sb-sidenav-collapse-arrow {
                color: white !important;
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
                color: #ffffff !important;
                font-weight: 500 !important;
            }
            
            .sb-sidenav .nav-link:hover {
                color: #ffffff !important;
                background-color: rgba(255, 255, 255, 0.1) !important;
            }
            
            .sb-sidenav .sb-sidenav-footer {
                background-color: rgba(255, 255, 255, 0.1) !important;
                color: white !important;
            }
            
            /* Card header titles styling */
            .card-header {
                color: white !important;
                font-weight: 600;
            }
            
            /* Statistics card labels styling */
            .card .small.text-white-50 {
                color: white !important;
                font-weight: 500;
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
            
            /* Mobile Responsive Styles for Notifications */
            @media (max-width: 768px) {
                /* Fix dropdown positioning on mobile - only apply on mobile */
                .notification-dropdown {
                    width: calc(100vw - 1rem) !important;
                    max-width: calc(100vw - 1rem) !important;
                    margin: 0.5rem !important;
                    left: 0.5rem !important;
                    right: auto !important;
                    transform: none !important;
                    position: fixed !important;
                    top: 60px !important;
                    max-height: calc(100vh - 120px) !important;
                    border-radius: 12px !important;
                    z-index: 1055 !important;
                }
                
                /* Override Bootstrap dropdown positioning on mobile */
                #notificationDropdown.show .notification-dropdown {
                    position: fixed !important;
                    left: 0.5rem !important;
                    right: auto !important;
                    top: 60px !important;
                    transform: none !important;
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
                
                /* Ensure dropdown doesn't overflow */
                .notification-dropdown.show {
                    display: block !important;
                }
            }
            
            @media (max-width: 576px) {
                .notification-dropdown {
                    width: calc(100vw - 0.5rem) !important;
                    max-width: calc(100vw - 0.5rem) !important;
                    margin: 0.25rem !important;
                    left: 0.25rem !important;
                    right: auto !important;
                    transform: none !important;
                    position: fixed !important;
                    top: 60px !important;
                    max-height: calc(100vh - 100px) !important;
                    border-radius: 10px !important;
                    z-index: 1055 !important;
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
        </style>
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
            <!-- Navbar Brand-->
            <a class="navbar-brand ps-3 d-flex align-items-center logo-white-section" href="{{ route('dashboard') }}">
                <img src="{{ asset('assets/images/waumini_link_logo.png') }}" alt="Waumini Link Logo" class="logo" style="height: 45px; max-width: 200px; object-fit: contain;">
            </a>
            <!-- Sidebar Toggle-->
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!" style="font-size: 1.5rem;"><i class="fas fa-bars" style="color: #ffffff !important;"></i></button>
            <!-- Welcome Message -->
            <div class="navbar-text me-auto ms-3" style="font-size: 1.1rem; font-weight: 600; color: #ffffff !important; text-shadow: 2px 2px 4px rgba(0,0,0,0.8);">
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
            <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
                <!-- Notification Icon -->
                <li class="nav-item dropdown me-3" id="notificationDropdown">
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
                            
                            <!-- Pending Financial Approvals (for Secretary, Pastor, Admin) -->
                            @if(auth()->user() && (auth()->user()->isSecretary() || auth()->user()->isPastor() || auth()->user()->isAdmin() || auth()->user()->canApproveFinances()))
                            <div class="notification-section mb-3">
                                <div class="section-header d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0 fw-bold text-warning">
                                        <i class="fas fa-clock me-2"></i>Pending Approvals
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
                                        <i class="fas fa-dollar-sign me-2"></i>Payments Needing Verification
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
                                    <i class="fas fa-info-circle me-1"></i>Click on any item to view details
                                </small>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="color: #ffffff !important;"><i class="fas fa-user fa-fw" style="color: #ffffff !important;"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="#!">Settings</a></li>
                        <li><a class="dropdown-item" href="#!">Activity Log</a></li>
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
                                    <a class="nav-link" href="{{ route('admin.activity-logs') }}">
                                        <i class="fas fa-list me-2"></i>Activity Logs
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
                                </nav>
                            </div>
                            @endif
                            
                            @if(auth()->user()->isMember())
                            {{-- Member Menu --}}
                            <div class="sb-sidenav-menu-heading">Member Portal</div>
                            <a class="nav-link" href="{{ route('member.dashboard') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                Dashboard
                            </a>
                            <a class="nav-link" href="{{ route('member.information') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-user-circle"></i></div>
                                My Information
                            </a>
                            <a class="nav-link" href="{{ route('member.finance') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-wallet"></i></div>
                                My Finance
                            </a>
                            <a class="nav-link" href="{{ route('member.announcements') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-bullhorn"></i></div>
                                Announcements
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
                                Leaders
                            </a>
                            <a class="nav-link" href="{{ route('member.change-password') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-key"></i></div>
                                Change Password
                            </a>
                            @elseif(!auth()->user()->isTreasurer() && !auth()->user()->isAdmin())
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
               <footer class="bg-dark text-light py-4 mt-auto">
  <div class="container px-4">
    <div class="row align-items-center">
      <!-- Left Side -->
      <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
        <small>&copy; <span id="year"></span> Waumini Link — Version 1.0</small>
      </div>

      <!-- Right Side -->
      <div class="col-md-6 text-center text-md-end">
        <small>Powered by EmCa Technologies</small>
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
                        // Handle dropdown show event for mobile positioning
                        notificationDropdown.addEventListener('show.bs.dropdown', function() {
                            if (window.innerWidth <= 768) {
                                // On mobile, position dropdown fixed
                                setTimeout(() => {
                                    const rect = notificationDropdown.getBoundingClientRect();
                                    dropdownMenu.style.position = 'fixed';
                                    dropdownMenu.style.top = (rect.bottom + 5) + 'px';
                                    dropdownMenu.style.left = '0.5rem';
                                    dropdownMenu.style.right = 'auto';
                                    dropdownMenu.style.width = 'calc(100vw - 1rem)';
                                    dropdownMenu.style.maxWidth = 'calc(100vw - 1rem)';
                                    dropdownMenu.style.transform = 'none';
                                    dropdownMenu.style.zIndex = '1055';
                                }, 10);
                            } else {
                                // On desktop, ensure normal Bootstrap positioning
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
                const layoutSidenavNav = document.getElementById('layoutSidenav_nav');
                
                if (sidebarToggle) {
                    // Remove any existing onclick handlers
                    sidebarToggle.onclick = null;
                    sidebarToggle.removeAttribute('onclick');
                    
                    // Check if handler is already attached (to avoid duplicate handlers from scripts.js)
                    if (!sidebarToggle.hasAttribute('data-layout-toggle-handler')) {
                        sidebarToggle.setAttribute('data-layout-toggle-handler', 'true');
                        
                        sidebarToggle.addEventListener('click', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            
                            // Toggle on the main container
                            if (layoutSidenav) {
                                layoutSidenav.classList.toggle('sb-sidenav-toggled');
                            }
                            
                            // Also toggle on body as fallback
                            document.body.classList.toggle('sb-sidenav-toggled');
                            
                            // Save state to localStorage (as string to match scripts.js)
                            const isToggled = layoutSidenav ? layoutSidenav.classList.contains('sb-sidenav-toggled') : document.body.classList.contains('sb-sidenav-toggled');
                            localStorage.setItem('sb|sidebar-toggle', isToggled ? 'true' : 'false');
                            
                            console.log('Sidebar toggled (layout handler):', isToggled);
                            
                            return false;
                        }, true); // Use capture phase to ensure it runs first
                        
                        // Restore sidebar state from localStorage
                        const savedState = localStorage.getItem('sb|sidebar-toggle');
                        if (savedState === 'true') {
                            if (layoutSidenav) {
                                layoutSidenav.classList.add('sb-sidenav-toggled');
                            }
                            document.body.classList.add('sb-sidenav-toggled');
                        }
                    }
                } else {
                    console.warn('Sidebar toggle button not found');
                }
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
        
        @yield('modals')
        
        @yield('scripts')
    </body>
</html>