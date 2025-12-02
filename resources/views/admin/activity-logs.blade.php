@extends('layouts.index')

@section('content')
<style>
    /* Ensure badge text is always visible with proper colors - works with Bootstrap 4 and 5 */
    .badge.badge-danger,
    .badge[class*="badge-danger"] {
        background-color: #dc3545 !important;
        color: white !important;
        font-weight: 600 !important;
        padding: 0.35em 0.65em !important;
        display: inline-block !important;
    }
    
    .badge.badge-success,
    .badge[class*="badge-success"] {
        background-color: #198754 !important;
        color: white !important;
        font-weight: 600 !important;
        padding: 0.35em 0.65em !important;
        display: inline-block !important;
    }
    
    .badge.badge-info,
    .badge[class*="badge-info"] {
        background-color: #0dcaf0 !important;
        color: white !important;
        font-weight: 600 !important;
        padding: 0.35em 0.65em !important;
        display: inline-block !important;
    }
    
    .badge.badge-secondary,
    .badge[class*="badge-secondary"] {
        background-color: #6c757d !important;
        color: white !important;
        font-weight: 600 !important;
        padding: 0.35em 0.65em !important;
        display: inline-block !important;
    }
    
    .badge.badge-warning,
    .badge[class*="badge-warning"] {
        background-color: #ffc107 !important;
        color: #212529 !important;
        font-weight: 600 !important;
        padding: 0.35em 0.65em !important;
        display: inline-block !important;
    }
    
    /* Fallback for any badge */
    .badge {
        display: inline-block !important;
        padding: 0.35em 0.65em !important;
        font-weight: 600 !important;
        border-radius: 0.25rem !important;
    }
    
    /* Reduce pagination spacing - very tight */
    .pagination {
        margin-top: 0 !important;
        margin-bottom: 0 !important;
        padding: 0 !important;
        line-height: 1.2 !important;
    }
    
    /* Reduce spacing between pagination items */
    .pagination .page-item {
        margin: 0 0.125rem !important;
    }
    
    /* Reduce padding on pagination links */
    .pagination .page-link {
        padding: 0.25rem 0.5rem !important;
    }
    
    /* Reduce card body padding at bottom for activity logs */
    .activity-logs-body {
        padding-bottom: 0 !important;
        padding-top: 1rem !important; /* Keep top padding normal */
        margin-bottom: 0 !important;
    }
    
    /* Remove extra spacing from table-responsive */
    .activity-logs-body .table-responsive {
        margin-bottom: 0 !important;
        padding-bottom: 0 !important;
    }
    
    /* Remove margin from table */
    .activity-logs-body table {
        margin-bottom: 0 !important;
    }
    
    /* Remove all spacing between table and footer */
    .activity-logs-body + .card-footer {
        margin-top: 0 !important;
        padding-top: 0.5rem !important;
    }
    
    /* Ensure no gap between card-body and card-footer */
    .card.shadow.mb-0 .card-body,
    .card.shadow.mb-4:last-child .card-body {
        border-bottom: none !important;
        padding-bottom: 0 !important;
        margin-bottom: 0 !important;
    }
    
    .card.shadow.mb-0 .card-footer,
    .card.shadow.mb-4:last-child .card-footer {
        border-top: 1px solid #dee2e6 !important;
        margin-top: 0 !important;
        margin-bottom: 0 !important;
        padding-top: 0.5rem !important;
    }
    
    /* Remove any gap between card-body and card-footer */
    .card.shadow.mb-0 .card-body + .card-footer,
    .card.shadow.mb-4:last-child .card-body + .card-footer {
        margin-top: 0 !important;
        padding-top: 0.5rem !important;
    }
    
    /* Remove any spacing after the last card */
    .card.shadow.mb-0:last-child,
    .card.shadow.mb-4:last-child {
        margin-bottom: 0 !important;
    }
    
    /* Remove container bottom padding */
    .container-fluid:has(.card.shadow.mb-0:last-child) {
        padding-bottom: 0 !important;
    }
    
    /* Improve overall page spacing */
    .container-fluid.px-4 {
        padding-bottom: 1.5rem !important;
    }
    
    /* Ensure cards have proper spacing between them */
    .card.shadow.mb-4:not(:last-child) {
        margin-bottom: 1.5rem !important;
    }
    
    /* Improve card footer appearance */
    .activity-logs-pagination {
        background-color: #f8f9fa !important;
    }
    
    /* Ultra-compact pagination footer - remove ALL spacing */
    .activity-logs-pagination {
        padding: 0.25rem 1rem !important;
        margin: 0 !important;
        border-top: 1px solid #dee2e6 !important;
        min-height: auto !important;
    }
    
    /* Compact pagination - remove all margins and padding */
    .activity-logs-pagination .pagination {
        margin: 0 !important;
        padding: 0 !important;
        display: flex !important;
        align-items: center !important;
        flex-wrap: wrap !important;
        gap: 0.125rem !important;
        line-height: 1 !important;
    }
    
    /* Compact pagination items - no margins */
    .activity-logs-pagination .pagination .page-item {
        margin: 0 !important;
    }
    
    /* Smaller pagination links */
    .activity-logs-pagination .pagination .page-link {
        padding: 0.2rem 0.4rem !important;
        line-height: 1 !important;
        font-size: 0.875rem !important;
        min-height: auto !important;
    }
    
    /* Hide Previous and Next arrows - hide first and last items which typically contain arrows */
    .activity-logs-pagination .pagination .page-item:first-child,
    .activity-logs-pagination .pagination .page-item:last-child {
        display: none !important;
    }
    
    /* Hide disabled Previous/Next items */
    .activity-logs-pagination .pagination .page-item.disabled {
        display: none !important;
    }
    
    /* Hide any link with rel="prev" or rel="next" */
    .activity-logs-pagination .pagination .page-link[rel="prev"],
    .activity-logs-pagination .pagination .page-link[rel="next"] {
        display: none !important;
    }
    
    /* Hide parent items of prev/next links */
    .activity-logs-pagination .pagination .page-item:has(.page-link[rel="prev"]),
    .activity-logs-pagination .pagination .page-item:has(.page-link[rel="next"]) {
        display: none !important;
    }
    
    /* Hide any large arrow icons that might appear on the page (but not in buttons) */
    .fa-chevron-left:not(.btn .fa-arrow-left),
    .fa-chevron-right:not(.btn .fa-arrow-right),
    .fa-angle-left:not(.btn .fa-angle-left),
    .fa-angle-right:not(.btn .fa-angle-right),
    .fa-arrow-left:not(.btn .fa-arrow-left),
    .fa-arrow-right:not(.btn .fa-arrow-right),
    i.fa-chevron-left:not(.btn i),
    i.fa-chevron-right:not(.btn i),
    i.fa-angle-left:not(.btn i),
    i.fa-angle-right:not(.btn i),
    i.fa-arrow-left:not(.btn i),
    i.fa-arrow-right:not(.btn i) {
        display: none !important;
    }
    
    /* Ensure button arrows are visible */
    .btn .fa-arrow-left,
    .btn .fa-arrow-right,
    .btn i.fa-arrow-left,
    .btn i.fa-arrow-right {
        display: inline-block !important;
    }
    
    /* Hide any elements containing arrow symbols */
    [class*="arrow"]:not(.activity-logs-pagination):not(.pagination),
    [class*="chevron"]:not(.activity-logs-pagination):not(.pagination) {
        display: none !important;
    }
    
    /* Hide any large navigation arrows outside pagination */
    .container-fluid > [class*="arrow"],
    .container-fluid > [class*="chevron"],
    .container-fluid > i[class*="arrow"],
    .container-fluid > i[class*="chevron"] {
        display: none !important;
    }
    
    /* Hide large standalone arrow elements */
    div[style*="arrow"],
    span[style*="arrow"],
    div[style*="chevron"],
    span[style*="chevron"],
    svg[style*="arrow"],
    svg[style*="chevron"] {
        display: none !important;
    }
    
    /* Hide any element with large arrow-like appearance */
    [class*="arrow"]:not(.btn):not(.pagination):not(.activity-logs-pagination),
    [class*="chevron"]:not(.btn):not(.pagination):not(.activity-logs-pagination),
    [id*="arrow"]:not(.btn):not(.pagination),
    [id*="chevron"]:not(.btn):not(.pagination) {
        display: none !important;
    }
    
    /* Hide large blue arrows specifically */
    [style*="blue"][style*="arrow"],
    [style*="blue"][style*="chevron"],
    .fa-arrow-left[style*="blue"],
    .fa-arrow-right[style*="blue"],
    .fa-chevron-left[style*="blue"],
    .fa-chevron-right[style*="blue"] {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
    }
    
    /* Enhanced result text */
    .activity-logs-pagination .text-muted.small {
        font-size: 0.875rem !important;
        line-height: 1.5 !important;
        margin: 0 !important;
        padding: 0 !important;
        color: #6c757d !important;
        font-weight: 500 !important;
    }
    
    /* Remove any bottom margin from the last card - no gap below */
    .card.shadow.mb-4:last-child {
        margin-bottom: 0 !important;
    }
    
    /* Remove bottom margin from container if it's the last element */
    .container-fluid > .row:last-child .card:last-child {
        margin-bottom: 0 !important;
    }
    
    /* Hide any duplicate pagination that might be rendered outside footer */
    .card-body > .pagination,
    .table-responsive + .pagination,
    .table-responsive ~ .pagination,
    .activity-logs-body > .pagination,
    .card-body .pagination:not(.activity-logs-pagination .pagination) {
        display: none !important;
    }
    
    /* Hide any duplicate "Showing" text outside footer */
    .card-body .text-muted:not(.activity-logs-pagination .text-muted) {
        display: none !important;
    }
    
    /* Ensure only footer pagination is visible */
    .activity-logs-pagination .pagination {
        display: flex !important;
    }
    
    /* Remove any extra spacing in the flex container */
    .activity-logs-pagination > div {
        margin: 0 !important;
        padding: 0 !important;
    }
    
    /* ========== ENHANCED PAGE STYLING ========== */
    
    /* Modern card styling */
    .card {
        border-radius: 12px !important;
        border: none !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08) !important;
        transition: box-shadow 0.3s ease !important;
    }
    
    .card:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.12) !important;
    }
    
    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        color: white !important;
        border-radius: 12px 12px 0 0 !important;
        border: none !important;
        padding: 1rem 1.5rem !important;
        font-weight: 600 !important;
    }
    
    .card-header h6 {
        color: white !important;
        margin: 0 !important;
        font-size: 1rem !important;
    }
    
    /* Enhanced table styling */
    .table {
        margin-bottom: 0 !important;
    }
    
    .table thead th {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
        color: #495057 !important;
        font-weight: 600 !important;
        text-transform: uppercase !important;
        font-size: 0.75rem !important;
        letter-spacing: 0.5px !important;
        padding: 1rem 0.75rem !important;
        border-bottom: 2px solid #dee2e6 !important;
        vertical-align: middle !important;
    }
    
    .table tbody tr {
        transition: all 0.2s ease !important;
        border-bottom: 1px solid #f0f0f0 !important;
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fa !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05) !important;
    }
    
    .table tbody td {
        padding: 1rem 0.75rem !important;
        vertical-align: middle !important;
        color: #495057 !important;
    }
    
    /* Enhanced badge styling */
    .badge {
        font-size: 0.75rem !important;
        padding: 0.4em 0.8em !important;
        border-radius: 6px !important;
        font-weight: 600 !important;
        letter-spacing: 0.3px !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
    }
    
    /* Form enhancements */
    .form-control, .form-select {
        border-radius: 8px !important;
        border: 1px solid #dee2e6 !important;
        padding: 0.6rem 0.75rem !important;
        transition: all 0.3s ease !important;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #667eea !important;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15) !important;
    }
    
    .form-label {
        font-weight: 600 !important;
        color: #495057 !important;
        margin-bottom: 0.5rem !important;
        font-size: 0.875rem !important;
    }
    
    /* Enhanced button styling */
    .btn {
        border-radius: 8px !important;
        padding: 0.6rem 1.2rem !important;
        font-weight: 600 !important;
        transition: all 0.3s ease !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
    }
    
    .btn:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 4px 8px rgba(0,0,0,0.15) !important;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        border: none !important;
    }
    
    .btn-secondary {
        background: #6c757d !important;
        border: none !important;
    }
    
    /* Enhanced pagination */
    .activity-logs-pagination {
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%) !important;
        border-top: 2px solid #e9ecef !important;
    }
    
    .pagination .page-link {
        border-radius: 6px !important;
        margin: 0 0.2rem !important;
        border: 1px solid #dee2e6 !important;
        color: #495057 !important;
        transition: all 0.2s ease !important;
    }
    
    .pagination .page-link:hover {
        background-color: #667eea !important;
        border-color: #667eea !important;
        color: white !important;
        transform: translateY(-2px) !important;
    }
    
    .pagination .page-item.active .page-link {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        border-color: #667eea !important;
        color: white !important;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3) !important;
    }
    
    /* User info styling */
    .table tbody td strong {
        color: #212529 !important;
        font-weight: 600 !important;
    }
    
    .table tbody td small {
        color: #6c757d !important;
        font-size: 0.8125rem !important;
    }
    
    /* Empty state */
    .table tbody tr td.text-center {
        padding: 3rem 1rem !important;
        color: #6c757d !important;
        font-style: italic !important;
    }
    
    /* Responsive improvements */
    @media (max-width: 768px) {
        .container-fluid {
            padding-left: 10px !important;
            padding-right: 10px !important;
        }

        .dashboard-header {
            margin-bottom: 12px !important;
            border-radius: 12px !important;
            overflow: hidden !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1) !important;
        }

        .dashboard-header .card-body {
            padding: 12px 14px !important;
        }

        .dashboard-header .rounded-circle {
            width: 38px !important;
            height: 38px !important;
            min-width: 38px !important;
            flex-shrink: 0 !important;
            background: rgba(255,255,255,0.2) !important;
            border: 2px solid rgba(255,255,255,0.3) !important;
        }

        .dashboard-header .rounded-circle i {
            font-size: 0.95rem !important;
        }

        .dashboard-header .d-flex.align-items-center.gap-3 {
            gap: 12px !important;
            flex: 1 !important;
            min-width: 0 !important;
        }

        .dashboard-header .lh-sm {
            flex: 1 !important;
            min-width: 0 !important;
            overflow: hidden !important;
        }

        .dashboard-header h5 {
            font-size: 1rem !important;
            line-height: 1.3 !important;
            margin-bottom: 2px !important;
            font-weight: 600 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        .dashboard-header small {
            font-size: 0.75rem !important;
            line-height: 1.2 !important;
            display: block !important;
            opacity: 0.9 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        .dashboard-header .btn {
            margin-top: 0 !important;
            padding: 8px 14px !important;
            font-size: 0.85rem !important;
            border-radius: 8px !important;
            white-space: nowrap !important;
            flex-shrink: 0 !important;
            font-weight: 500 !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
            transition: all 0.2s ease !important;
        }

        .dashboard-header .btn:hover {
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 6px rgba(0,0,0,0.15) !important;
        }

        .dashboard-header .d-flex.justify-content-between {
            align-items: center !important;
            flex-wrap: nowrap !important;
        }

        .dashboard-header .d-flex.justify-content-between > div:first-child {
            flex: 1 !important;
            min-width: 0 !important;
            overflow: hidden !important;
        }

        .card-header {
            padding: 0.75rem 1rem !important;
        }
        
        /* Filter form - stack on mobile */
        .card-body .row.g-3 > div {
            margin-bottom: 15px;
        }

        .card-body .row.g-3 .col-md-3,
        .card-body .row.g-3 .col-md-2 {
            width: 100%;
            margin-bottom: 15px;
        }

        .card-body .btn {
            width: 100%;
            margin-bottom: 10px;
        }
        
        /* Table responsive */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            display: block;
            width: 100%;
        }

        .table {
            min-width: 800px;
        }
        
        .table thead th,
        .table tbody td {
            padding: 0.75rem 0.5rem !important;
            font-size: 0.875rem !important;
        }
        
        .btn {
            padding: 0.5rem 1rem !important;
            font-size: 0.875rem !important;
        }

        .card-body {
            padding: 15px !important;
        }
    }

    @media (max-width: 576px) {
        .container-fluid {
            padding-left: 5px !important;
            padding-right: 5px !important;
        }

        .dashboard-header {
            margin-bottom: 10px !important;
            border-radius: 10px !important;
        }

        .dashboard-header .card-body {
            padding: 10px 12px !important;
        }

        .dashboard-header .rounded-circle {
            width: 36px !important;
            height: 36px !important;
            min-width: 36px !important;
        }

        .dashboard-header .rounded-circle i {
            font-size: 0.9rem !important;
        }

        .dashboard-header .d-flex.align-items-center.gap-3 {
            gap: 10px !important;
        }

        .dashboard-header h5 {
            font-size: 0.95rem !important;
            line-height: 1.25 !important;
            margin-bottom: 1px !important;
        }

        .dashboard-header small {
            font-size: 0.72rem !important;
            line-height: 1.15 !important;
        }

        .dashboard-header .d-flex.justify-content-between {
            flex-wrap: wrap !important;
            gap: 8px !important;
        }

        .dashboard-header .btn {
            margin-top: 0 !important;
            width: auto !important;
            min-width: fit-content !important;
            padding: 7px 12px !important;
            font-size: 0.8rem !important;
            flex: 0 0 auto !important;
        }

        /* Stack on very small screens */
        @media (max-width: 400px) {
            .dashboard-header .d-flex.justify-content-between {
                flex-direction: column !important;
                align-items: stretch !important;
            }

            .dashboard-header .btn {
                width: 100% !important;
                margin-top: 8px !important;
            }
        }

        .table {
            min-width: 700px;
            font-size: 0.75rem;
        }

        .table thead th,
        .table tbody td {
            padding: 0.5rem 0.25rem !important;
            font-size: 0.75rem !important;
        }

        .btn {
            font-size: 0.8rem !important;
            padding: 0.4rem 0.8rem !important;
        }
    }
    
    /* Filter card enhancement */
    .card.shadow.mb-4 {
        border-left: 4px solid #667eea !important;
    }
    
    /* Activity logs card enhancement */
    .card.shadow.mb-0 {
        border-left: 4px solid #764ba2 !important;
    }
</style>

<div class="container-fluid px-4" style="padding-bottom: 0;">
    <div class="row mb-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm dashboard-header" style="background:#17082d;">
                <div class="card-body text-white py-2 px-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center border border-white border-2" style="width:48px; height:48px; background:rgba(255,255,255,.15);">
                                <i class="fas fa-list text-white"></i>
                            </div>
                            <div class="lh-sm">
                                <h5 class="mb-0 fw-semibold" style="color: white !important;">Activity Logs</h5>
                                <small style="color: white !important;">Track all system activities</small>
                            </div>
                        </div>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-light btn-sm" style="border-radius: 8px; font-weight: 600;">
                            <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-filter me-2"></i>Filters
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.activity-logs') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">
                        <i class="fas fa-user me-1"></i>User
                    </label>
                    <select name="user_id" class="form-control form-select">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">
                        <i class="fas fa-bolt me-1"></i>Action
                    </label>
                    <select name="action" class="form-control form-select">
                        <option value="">All Actions</option>
                        @foreach($actions as $action)
                        <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                            {{ ucfirst($action) }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">
                        <i class="fas fa-calendar-alt me-1"></i>Date From
                    </label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">
                        <i class="fas fa-calendar-check me-1"></i>Date To
                    </label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">
                        <i class="fas fa-search me-1"></i>Search
                    </label>
                    <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
                </div>
                <div class="col-md-12 mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-2"></i>Apply Filters
                    </button>
                    <a href="{{ route('admin.activity-logs') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Activity Logs Table -->
    <div class="card shadow mb-0" style="margin-bottom: 0 !important;">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-list-alt me-2"></i>Activity Logs 
                <span class="badge bg-light text-dark ms-2">{{ $logs->total() }} total</span>
            </h6>
        </div>
        <div class="card-body activity-logs-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th><i class="fas fa-hashtag me-1"></i>ID</th>
                            <th><i class="fas fa-user me-1"></i>User</th>
                            <th><i class="fas fa-bolt me-1"></i>Action</th>
                            <th><i class="fas fa-align-left me-1"></i>Description</th>
                            <th><i class="fas fa-route me-1"></i>Route</th>
                            <th><i class="fas fa-network-wired me-1"></i>IP Address</th>
                            <th><i class="fas fa-clock me-1"></i>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td>{{ $log->id }}</td>
                            <td>
                                @if($log->user)
                                    <strong>{{ $log->user->name }}</strong><br>
                                    <small class="text-muted">{{ $log->user->email }}</small><br>
                                    <span class="badge badge-secondary" style="font-size: 0.75em;">{{ ucfirst($log->user->role) }}</span>
                                @else
                                    <span class="text-muted">System</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $actionBadgeClass = match($log->action) {
                                        'create' => 'badge-success',
                                        'delete' => 'badge-danger',
                                        'approve' => 'badge-warning',
                                        'update' => 'badge-info',
                                        'login' => 'badge-info',
                                        'logout' => 'badge-secondary',
                                        default => 'badge-info'
                                    };
                                @endphp
                                <span class="badge {{ $actionBadgeClass }}">
                                    {{ ucfirst($log->action) }}
                                </span>
                            </td>
                            <td>{{ $log->description }}</td>
                            <td><small>{{ $log->route ?? 'N/A' }}</small></td>
                            <td><small>{{ $log->ip_address }}</small></td>
                            <td>
                                <small>{{ $log->created_at->format('Y-m-d H:i:s') }}</small><br>
                                <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">No activity logs found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Pagination Footer -->
        <div class="card-footer activity-logs-pagination" style="padding: 0.5rem 1rem !important; margin-top: 0 !important;">
            <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 0.5rem; margin: 0; line-height: 1;">
                <div class="text-muted small" style="margin: 0; padding: 0; font-size: 0.8125rem;">
                    Showing {{ $logs->firstItem() }} to {{ $logs->lastItem() }} of {{ $logs->total() }} results
                </div>
                <div style="margin: 0; padding: 0;">
                    {{ $logs->links('pagination.simple-numbers') }}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Remove any duplicate pagination elements that might appear outside the footer
    const card = document.querySelector('.card.shadow.mb-0:last-child, .card.shadow.mb-4:last-child');
    if (card) {
        const cardBody = card.querySelector('.card-body');
        
        // Remove all pagination outside footer
        if (cardBody) {
            const paginations = cardBody.querySelectorAll('.pagination');
            paginations.forEach(function(pagination) {
                if (!pagination.closest('.card-footer')) {
                    pagination.remove();
                }
            });
            
            // Remove any duplicate "Showing X to Y" text outside footer
            const textElements = cardBody.querySelectorAll('.text-muted, .small');
            textElements.forEach(function(element) {
                if (element.textContent.includes('Showing') && !element.closest('.card-footer')) {
                    element.remove();
                }
            });
        }
        
        // Remove any pagination that appears after table-responsive
        const tableResponsive = card.querySelector('.table-responsive');
        if (tableResponsive && tableResponsive.nextSibling) {
            let next = tableResponsive.nextElementSibling;
            while (next && !next.classList.contains('card-footer')) {
                if (next.classList.contains('pagination') || next.querySelector('.pagination')) {
                    next.remove();
                }
                next = next.nextElementSibling;
            }
        }
    }
    
    // Force compact spacing
    const paginationFooter = document.querySelector('.activity-logs-pagination');
    if (paginationFooter) {
        paginationFooter.style.padding = '0.25rem 1rem';
        paginationFooter.style.margin = '0';
    }
    
    // Remove Previous and Next arrows from pagination
    const pagination = paginationFooter ? paginationFooter.querySelector('.pagination') : null;
    if (pagination) {
        const pageItems = pagination.querySelectorAll('.page-item');
        pageItems.forEach(function(item) {
            const link = item.querySelector('.page-link');
            if (link) {
                const text = link.textContent.trim();
                const rel = link.getAttribute('rel');
                // Hide Previous/Next links
                if (text.includes('Previous') || text.includes('Next') || 
                    text.includes('«') || text.includes('»') ||
                    rel === 'prev' || rel === 'next') {
                    item.style.display = 'none';
                }
            }
        });
    }
    
    // Remove any large arrow icons from the page (but keep button arrows)
    const arrows = document.querySelectorAll('.fa-chevron-left, .fa-chevron-right, .fa-angle-left, .fa-angle-right, .fa-arrow-left, .fa-arrow-right, i[class*="arrow"], i[class*="chevron"]');
    arrows.forEach(function(arrow) {
        // Only hide if not inside pagination and not inside buttons
        if (!arrow.closest('.pagination') && 
            !arrow.closest('.activity-logs-pagination') && 
            !arrow.closest('.btn') && 
            !arrow.closest('a.btn')) {
            arrow.style.display = 'none';
        }
    });
    
    // Remove any large arrow elements by checking for arrow-like content
    const allElements = document.querySelectorAll('*');
    allElements.forEach(function(el) {
        const text = el.textContent || '';
        const classes = el.className || '';
        const style = el.getAttribute('style') || '';
        const computedStyle = window.getComputedStyle(el);
        const bgColor = computedStyle.backgroundColor || '';
        const width = el.offsetWidth || 0;
        const height = el.offsetHeight || 0;
        
        // Hide large standalone arrow elements
        if ((text.trim() === '«' || text.trim() === '»' || text.trim() === '←' || text.trim() === '→') && 
            !el.closest('.pagination') && 
            !el.closest('.btn') &&
            width > 30 && height > 30) {
            el.style.display = 'none';
            el.style.visibility = 'hidden';
        }
        
        // Hide large blue arrow-like elements
        if ((classes.includes('arrow') || classes.includes('chevron') || 
             style.includes('arrow') || style.includes('chevron') ||
             bgColor.includes('rgb(0, 123, 255)') || bgColor.includes('rgb(13, 110, 253)') || 
             bgColor.includes('#007bff') || bgColor.includes('#0d6efd')) &&
            !el.closest('.pagination') && 
            !el.closest('.btn') &&
            !el.closest('.activity-logs-pagination') &&
            (width > 50 || height > 50)) {
            el.style.display = 'none';
            el.style.visibility = 'hidden';
            el.style.opacity = '0';
        }
    });
    
    // Specifically target and remove large blue arrows
    setTimeout(function() {
        const blueArrows = document.querySelectorAll('div, span, i, svg, a');
        blueArrows.forEach(function(el) {
            const style = window.getComputedStyle(el);
            const bgColor = style.backgroundColor;
            const width = el.offsetWidth;
            const height = el.offsetHeight;
            
            // Check if it's a large blue element that looks like an arrow
            if ((bgColor.includes('rgb(0, 123, 255)') || bgColor.includes('rgb(13, 110, 253)') || 
                 bgColor.includes('#007bff') || bgColor.includes('#0d6efd') ||
                 bgColor.includes('rgb(0, 86, 179)')) &&
                !el.closest('.pagination') && 
                !el.closest('.btn') &&
                !el.closest('.activity-logs-pagination') &&
                (width > 40 || height > 40)) {
                el.style.display = 'none';
                el.style.visibility = 'hidden';
                el.remove();
            }
        });
    }, 100);
    
    // Remove any bottom margin from the activity logs card
    const activityCard = document.querySelector('.card.shadow.mb-0');
    if (activityCard) {
        activityCard.style.marginBottom = '0';
        const cardFooter = activityCard.querySelector('.card-footer');
        if (cardFooter) {
            cardFooter.style.marginBottom = '0';
        }
    }
    
    // Aggressively remove large blue arrows
    function removeLargeBlueArrows() {
        const allElements = document.querySelectorAll('*');
        allElements.forEach(function(el) {
            if (el.closest('.pagination') || el.closest('.btn') || el.closest('.activity-logs-pagination')) {
                return; // Skip pagination and buttons
            }
            
            const style = window.getComputedStyle(el);
            const bgColor = style.backgroundColor;
            const width = el.offsetWidth || 0;
            const height = el.offsetHeight || 0;
            const classes = el.className || '';
            const id = el.id || '';
            
            // Check for large blue elements (arrows)
            if ((bgColor.includes('rgb(0, 123, 255)') || 
                 bgColor.includes('rgb(13, 110, 253)') || 
                 bgColor.includes('#007bff') || 
                 bgColor.includes('#0d6efd') ||
                 bgColor.includes('rgb(0, 86, 179)') ||
                 classes.includes('arrow') ||
                 classes.includes('chevron') ||
                 id.includes('arrow') ||
                 id.includes('chevron')) &&
                (width > 30 || height > 30)) {
                el.style.display = 'none';
                el.style.visibility = 'hidden';
                el.style.opacity = '0';
                el.style.position = 'absolute';
                el.style.left = '-9999px';
            }
        });
    }
    
    // Run multiple times to catch dynamically loaded elements
    removeLargeBlueArrows();
    setTimeout(removeLargeBlueArrows, 100);
    setTimeout(removeLargeBlueArrows, 500);
    setTimeout(removeLargeBlueArrows, 1000);
    
    // Use MutationObserver to catch any new elements
    const observer = new MutationObserver(function(mutations) {
        removeLargeBlueArrows();
    });
    
    observer.observe(document.body, {
        childList: true,
        subtree: true,
        attributes: true,
        attributeFilter: ['style', 'class']
    });
});
</script>

@endsection

