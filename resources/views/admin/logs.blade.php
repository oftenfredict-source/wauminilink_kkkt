@extends('layouts.index')

@section('content')
<style>
    /* Mobile Responsive Styles */
    @media (max-width: 768px) {
        .container-fluid {
            padding-left: 0.75rem !important;
            padding-right: 0.75rem !important;
            padding-top: 0.25rem !important;
        }

        .d-flex.justify-content-between {
            flex-direction: column;
            align-items: flex-start !important;
        }

        h1.h3 {
            font-size: 1.25rem !important;
            margin-bottom: 1rem !important;
            line-height: 1.3 !important;
        }

        h1.h3 i {
            font-size: 1.1rem !important;
        }

        .card {
            margin-bottom: 1rem !important;
        }

        .card-body {
            padding: 1rem !important;
        }

        .card-header {
            padding: 0.75rem 1rem !important;
        }

        /* Filter forms - stack columns on mobile */
        .row.g-3 > [class*="col-"] {
            margin-bottom: 0.75rem !important;
        }

        .form-label {
            font-size: 0.875rem !important;
            margin-bottom: 0.375rem !important;
            font-weight: 500 !important;
        }

        .form-select,
        .form-control {
            width: 100% !important;
            padding: 0.5rem 0.75rem !important;
            font-size: 0.9rem !important;
        }

        /* Filter section improvements */
        .card.shadow-sm:has(form) {
            margin-bottom: 1rem !important;
        }

        .card.shadow-sm:has(form) .card-body {
            padding: 1rem !important;
        }

        /* Filter header on mobile */
        .card-header.d-md-none {
            background-color: #f8f9fa !important;
            border-bottom: 1px solid #dee2e6 !important;
            padding: 0.75rem 1rem !important;
            user-select: none !important;
        }

        .card-header.d-md-none:hover {
            background-color: #e9ecef !important;
        }

        .card-header.d-md-none h6 {
            font-size: 0.9rem !important;
            font-weight: 600 !important;
            margin: 0 !important;
        }

        .card-header.d-md-none i {
            font-size: 0.875rem !important;
            transition: transform 0.3s ease !important;
        }

        /* Ensure filter button is full width on mobile */
        .row.g-3 button[type="submit"] {
            width: 100% !important;
            margin-top: 0.5rem !important;
        }

        /* Form check styling on mobile */
        .form-check {
            margin-top: 0.5rem !important;
        }

        .form-check-label {
            font-size: 0.875rem !important;
        }

        /* Desktop: Always show filters */
        @media (min-width: 769px) {
            #activityFilters,
            #systemFilters,
            #failedLoginFilters {
                display: block !important;
            }
        }

        /* Table responsive */
        .table-responsive {
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch !important;
        }

        .table {
            font-size: 0.85rem !important;
            min-width: 800px !important;
        }

        .table th,
        .table td {
            padding: 0.5rem 0.5rem !important;
            white-space: nowrap !important;
        }

        .table th {
            font-size: 0.8rem !important;
            font-weight: 600 !important;
        }

        /* Buttons */
        .btn {
            font-size: 0.875rem !important;
            padding: 0.5rem 0.75rem !important;
        }

        .btn-sm {
            font-size: 0.75rem !important;
            padding: 0.375rem 0.5rem !important;
        }

        /* Pagination */
        .pagination {
            flex-wrap: wrap !important;
            justify-content: center !important;
        }

        .pagination .page-link {
            padding: 0.375rem 0.5rem !important;
            font-size: 0.875rem !important;
        }

        .card-footer {
            padding: 0.75rem 1rem !important;
        }

        .card-footer .text-muted {
            font-size: 0.8rem !important;
            text-align: center !important;
            width: 100% !important;
            margin-bottom: 0.5rem !important;
        }
    }

    @media (max-width: 576px) {
        .container-fluid {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
            padding-top: 0.15rem !important;
        }

        h1.h3 {
            font-size: 1.1rem !important;
            line-height: 1.2 !important;
            margin-bottom: 0.75rem !important;
        }

        h1.h3 i {
            font-size: 1rem !important;
        }

        .card {
            margin-bottom: 0.75rem !important;
        }

        .card-body {
            padding: 0.75rem !important;
        }

        .card-header {
            padding: 0.625rem 0.75rem !important;
        }

        .form-label {
            font-size: 0.8125rem !important;
            font-weight: 500 !important;
        }

        .form-select,
        .form-control {
            font-size: 0.875rem !important;
            padding: 0.45rem 0.625rem !important;
        }

        /* Filter section improvements on extra small */
        .card.shadow-sm:has(form) .card-body {
            padding: 0.75rem !important;
        }

        /* Filter header on extra small mobile */
        .card-header.d-md-none {
            padding: 0.625rem 0.75rem !important;
        }

        .card-header.d-md-none h6 {
            font-size: 0.85rem !important;
        }

        .card-header.d-md-none i {
            font-size: 0.8125rem !important;
        }

        /* Ensure filter button is full width on extra small mobile */
        .row.g-3 button[type="submit"] {
            width: 100% !important;
            margin-top: 0.5rem !important;
        }

        .form-check-label {
            font-size: 0.8125rem !important;
        }

        .table {
            font-size: 0.8rem !important;
        }

        .table th,
        .table td {
            padding: 0.4rem 0.4rem !important;
        }

        .table th {
            font-size: 0.75rem !important;
        }

        .btn {
            font-size: 0.8125rem !important;
            padding: 0.45rem 0.625rem !important;
        }

        .btn-sm {
            font-size: 0.7rem !important;
            padding: 0.3rem 0.45rem !important;
        }

        .pagination .page-link {
            padding: 0.3rem 0.4rem !important;
            font-size: 0.8125rem !important;
        }

        .card-footer {
            padding: 0.625rem 0.75rem !important;
        }

        .card-footer .text-muted {
            font-size: 0.75rem !important;
        }
    }
</style>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-list-alt me-2"></i>System Logs
        </h1>
    </div>

    <!-- Log Type Dropdown -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center g-3">
                <div class="col-12 col-md-4">
                    <label for="logType" class="form-label fw-bold">Log Type:</label>
                    <select id="logType" class="form-select" onchange="changeLogType(this.value)">
                        <option value="activity" {{ $logType === 'activity' ? 'selected' : '' }}>Activity Logs</option>
                        <option value="system" {{ $logType === 'system' ? 'selected' : '' }}>System Logs</option>
                        <option value="failed-login" {{ $logType === 'failed-login' ? 'selected' : '' }}>Failed Login</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    @if($logType === 'activity')
        @include('admin.logs.partials.activity-logs')
    @elseif($logType === 'system')
        @include('admin.logs.partials.system-logs')
    @elseif($logType === 'failed-login')
        @include('admin.logs.partials.failed-login-logs')
    @endif
</div>

<script>
function changeLogType(type) {
    window.location.href = '{{ route("admin.logs") }}?type=' + type;
}

// Toggle filter section on mobile
function toggleFilterSection(filterId) {
    const filterBody = document.getElementById(filterId);
    const filterIcon = document.getElementById(filterId + 'Icon');
    
    if (filterBody && filterIcon) {
        if (filterBody.style.display === 'none') {
            filterBody.style.display = 'block';
            filterIcon.classList.remove('fa-chevron-down');
            filterIcon.classList.add('fa-chevron-up');
        } else {
            filterBody.style.display = 'none';
            filterIcon.classList.remove('fa-chevron-up');
            filterIcon.classList.add('fa-chevron-down');
        }
    }
}

// Initialize filter sections on mobile
document.addEventListener('DOMContentLoaded', function() {
    function initializeFilters() {
        const filterSections = ['activityFilters', 'systemFilters', 'failedLoginFilters'];
        if (window.innerWidth <= 768) {
            filterSections.forEach(function(filterId) {
                const filterBody = document.getElementById(filterId);
                if (filterBody && filterBody.style.display === '') {
                    filterBody.style.display = 'none';
                }
            });
        } else {
            filterSections.forEach(function(filterId) {
                const filterBody = document.getElementById(filterId);
                if (filterBody) {
                    filterBody.style.display = 'block';
                }
            });
        }
    }
    
    initializeFilters();
    
    // Handle window resize
    window.addEventListener('resize', function() {
        initializeFilters();
    });
});
</script>
@endsection


