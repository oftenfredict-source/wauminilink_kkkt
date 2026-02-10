@extends('layouts.index')

@section('title', 'Fund Breakdown Report')

@section('content')
    <style>
        /* Mobile Responsive Styles */
        @media (max-width: 768px) {
            .container-fluid {
                padding-left: 0.5rem !important;
                padding-right: 0.5rem !important;
            }

            /* Actions Card */
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

            #actionsBody {
                transition: all 0.3s ease;
                display: none;
            }

            .actions-header {
                cursor: pointer !important;
            }

            #actionsToggleIcon {
                display: block !important;
            }

            /* Filter Section */
            .filters-form .card-header {
                transition: all 0.2s ease;
            }

            .filter-header:hover {
                opacity: 0.9;
            }

            #filterBody {
                transition: all 0.3s ease;
                display: none;
                background: #fafbfc;
            }

            .filter-header {
                cursor: pointer !important;
            }

            #filterToggleIcon {
                display: block !important;
                transition: transform 0.3s ease;
            }

            .filter-header.active #filterToggleIcon {
                transform: rotate(180deg);
            }

            .filters-form .card-body {
                padding: 0.75rem 0.5rem !important;
            }

            .filters-form .form-label {
                font-size: 0.7rem !important;
                margin-bottom: 0.2rem !important;
                font-weight: 600 !important;
            }

            .filters-form .form-control {
                font-size: 0.8125rem !important;
                padding: 0.4rem 0.5rem !important;
                border-radius: 6px !important;
            }

            .filters-form .btn-sm {
                padding: 0.4rem 0.75rem !important;
                font-size: 0.8125rem !important;
                border-radius: 6px !important;
                font-weight: 600 !important;
            }

            /* Cards - Stack on Mobile */
            .col-xl-3 {
                margin-bottom: 1rem;
            }

            /* Summary Cards - Smaller on Mobile */
            .h5 {
                font-size: 1.25rem !important;
            }

            /* Table Responsive */
            .table {
                font-size: 0.75rem;
            }

            .table th,
            .table td {
                padding: 0.75rem 0.5rem;
            }

            /* Header adjustments */
            h1,
            .h3 {
                font-size: 1.25rem !important;
            }

            /* Fund Breakdown Card - Collapsible on Mobile */
            .fund-breakdown-card {
                transition: all 0.3s ease;
            }

            .fund-breakdown-card .card-header {
                user-select: none;
                transition: background-color 0.2s ease;
                cursor: pointer !important;
            }

            .fund-breakdown-card .card-header:hover {
                background-color: #7a0000 !important;
            }

            #fundBreakdownBody {
                transition: all 0.3s ease;
                display: none;
            }

            .fund-breakdown-header {
                cursor: pointer !important;
            }

            #fundBreakdownToggleIcon {
                display: block !important;
                transition: transform 0.3s ease;
            }

            .fund-breakdown-header.active #fundBreakdownToggleIcon {
                transform: rotate(180deg);
            }
        }

        /* Desktop: Always show fund breakdown */
        @media (min-width: 769px) {
            #fundBreakdownBody {
                display: block !important;
            }

            #fundBreakdownToggleIcon {
                display: none !important;
            }

            .fund-breakdown-header {
                cursor: default !important;
            }
        }

        @media (max-width: 768px) {

            /* Fund row styling on mobile */
            .fund-row td:first-child {
                padding: 1rem !important;
                border-bottom: 2px solid #e9ecef;
            }

            /* Better spacing for mobile cards */
            .d-md-none .row.g-2>div {
                margin-bottom: 0.5rem;
            }

            /* Mobile card-style boxes - cleaner design */
            .d-md-none .bg-opacity-10 {
                border-radius: 8px;
            }

            /* Progress bar on mobile */
            .d-md-none .progress {
                height: 20px !important;
                border-radius: 10px !important;
                box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
            }

            .d-md-none .progress-bar {
                font-size: 0.7rem;
                font-weight: 700;
            }

            /* Badge adjustments for mobile */
            .d-md-none .badge {
                font-size: 0.75rem;
                padding: 0.4rem 0.6rem;
            }

            /* Better spacing on mobile */
            .d-md-none .d-flex.flex-column.gap-2>div {
                margin-bottom: 0.5rem;
            }

            /* Better table header on mobile */
            .table-dark th {
                font-size: 0.7rem;
                padding: 0.75rem 0.5rem;
            }

            /* Table footer on mobile */
            .table-info td {
                font-size: 0.8rem;
                padding: 0.75rem 0.5rem;
            }
        }

        /* Desktop: Always show actions and filters */
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
    </style>
    <div class="container-fluid px-4">
        <!-- Page Title and Quick Actions - Compact Collapsible -->
        <div class="card border-0 shadow-sm mb-3 actions-card">
            <div class="card-header bg-white border-bottom p-2 px-3 d-flex align-items-center justify-content-between actions-header"
                onclick="toggleActions()">
                <div class="d-flex align-items-center gap-2">
                    <h1 class="mb-0 mt-2" style="font-size: 1.5rem;"><i class="fas fa-chart-pie text-primary me-2"></i>Fund
                        Breakdown</h1>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-chevron-down text-muted d-md-none" id="actionsToggleIcon"></i>
                </div>
            </div>
            <div class="card-body p-3" id="actionsBody">
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="window.print()">
                        <i class="fas fa-print me-1"></i>
                        <span class="d-none d-sm-inline">Print</span>
                        <span class="d-sm-none">Print</span>
                    </button>
                    <a href="{{ route('reports.export.pdf', ['report_type' => 'offering-fund-breakdown', 'start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}"
                        class="btn btn-danger btn-sm">
                        <i class="fas fa-file-pdf me-1"></i>
                        <span class="d-none d-sm-inline">PDF</span>
                        <span class="d-sm-none">PDF</span>
                    </a>
                    <a href="{{ route('reports.export.excel', ['report_type' => 'offering-fund-breakdown', 'start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}"
                        class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel me-1"></i>
                        <span class="d-none d-sm-inline">Excel</span>
                        <span class="d-sm-none">Excel</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Date Range Filter Card - Collapsible on Mobile -->
        <form method="GET" action="{{ route('reports.offering-fund-breakdown') }}"
            class="card shadow-sm border-0 mb-4 filters-form">
            <!-- Filter Header -->
            <div class="card-header report-header-primary text-white p-2 px-3 filter-header" onclick="toggleFilters()">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-calendar-alt me-2"></i>
                        <h6 class="mb-0 fw-semibold">Report Period Filter</h6>
                        @if(request('start_date') || request('end_date'))
                            <span class="badge bg-white text-dark rounded-pill ms-2"
                                id="activeFiltersCount">{{ (request('start_date') ? 1 : 0) + (request('end_date') ? 1 : 0) }}</span>
                        @endif
                    </div>
                    <i class="fas fa-chevron-down text-white d-md-none" id="filterToggleIcon"></i>
                </div>
            </div>

            <!-- Filter Body - Collapsible on Mobile -->
            <div class="card-body p-3" id="filterBody">
                <div class="row g-2 mb-2">
                    <div class="col-6 col-md-4">
                        <label for="start_date" class="form-label small text-muted mb-1 fw-bold">
                            <i class="fas fa-calendar me-1 text-primary"></i>Start Date
                        </label>
                        <input type="date" class="form-control form-control-sm" id="start_date" name="start_date"
                            value="{{ $startDate->format('Y-m-d') }}">
                    </div>
                    <div class="col-6 col-md-4">
                        <label for="end_date" class="form-label small text-muted mb-1 fw-bold">
                            <i class="fas fa-calendar me-1 text-info"></i>End Date
                        </label>
                        <input type="date" class="form-control form-control-sm" id="end_date" name="end_date"
                            value="{{ $endDate->format('Y-m-d') }}">
                    </div>
                    <div class="col-12 col-md-4 d-flex gap-2 align-items-end">
                        <button type="submit" class="btn btn-primary btn-sm flex-fill">
                            <i class="fas fa-search me-1"></i>
                            <span class="d-none d-sm-inline">Update</span>
                            <span class="d-sm-none">Go</span>
                        </button>
                        <a href="{{ route('reports.offering-fund-breakdown') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-times me-1"></i>
                            <span class="d-none d-sm-inline">Reset</span>
                        </a>
                    </div>
                </div>
            </div>
        </form>

        <!-- Summary Cards -->
        <div class="row mb-4 g-3">
            <div class="col-xl-3 col-md-6 col-12">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Income</div>
                                <div class="h5 mb-0 font-weight-bold text-success">TZS {{ number_format($totalIncome, 0) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-arrow-up fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 col-12">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Used</div>
                                <div class="h5 mb-0 font-weight-bold text-warning">TZS {{ number_format($totalUsed, 0) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-arrow-down fa-2x text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 col-12">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Available</div>
                                <div class="h5 mb-0 font-weight-bold text-primary">TZS
                                    {{ number_format($totalAvailable, 0) }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-wallet fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 col-12">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Utilization Rate</div>
                                <div class="h5 mb-0 font-weight-bold text-info">
                                    {{ $totalIncome > 0 ? round(($totalUsed / $totalIncome) * 100, 1) : 0 }}%
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-percentage fa-2x text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fund Breakdown Table -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow border-0 fund-breakdown-card">
                    <div class="card-header bg-primary text-white py-3 fund-breakdown-header"
                        onclick="toggleFundBreakdown()">
                        <div
                            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-table me-2"></i>
                                <h6 class="m-0 font-weight-bold mb-0">Fund Breakdown</h6>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-white text-primary">
                                    <i class="fas fa-layer-group me-1"></i>{{ count($fundBreakdown) }}
                                    {{ count($fundBreakdown) == 1 ? 'Fund Type' : 'Fund Types' }}
                                </span>
                                <i class="fas fa-chevron-down text-white d-md-none ms-2" id="fundBreakdownToggleIcon"
                                    style="transition: transform 0.3s ease;"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0" id="fundBreakdownBody">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead style="background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);">
                                    <tr>
                                        <th class="ps-3 text-white" style="font-weight: 600;">
                                            <i class="fas fa-tag me-2"></i>Fund Type
                                        </th>
                                        <th class="text-end text-white" style="font-weight: 600;">
                                            <i class="fas fa-arrow-up me-2"></i>Total Income
                                        </th>
                                        <th class="text-end d-none d-lg-table-cell text-white" style="font-weight: 600;">
                                            <i class="fas fa-gift me-2"></i>From Offerings
                                        </th>
                                        <th class="text-end d-none d-lg-table-cell text-white" style="font-weight: 600;">
                                            <i class="fas fa-heart me-2"></i>From Donations
                                        </th>
                                        <th class="text-end text-white" style="font-weight: 600;">
                                            <i class="fas fa-arrow-down me-2"></i>Used Amount
                                        </th>
                                        <th class="text-end text-white" style="font-weight: 600;">
                                            <i class="fas fa-wallet me-2"></i>Available
                                        </th>
                                        <th class="text-center d-none d-md-table-cell text-white" style="font-weight: 600;">
                                            <i class="fas fa-percentage me-2"></i>Utilization
                                        </th>
                                        <th class="text-center d-none d-md-table-cell text-white" style="font-weight: 600;">
                                            <i class="fas fa-info-circle me-2"></i>Status
                                        </th>
                                        <th class="text-center pe-3 d-none d-xl-table-cell text-white"
                                            style="font-weight: 600;">
                                            <i class="fas fa-chart-bar me-2"></i>Progress
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($fundBreakdown as $index => $fund)
                                        <tr class="fund-row" data-fund="{{ $fund['fund_type'] ?? $fund['offering_type'] }}">
                                            <td class="align-middle ps-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3 d-none d-md-block">
                                                        <div class="bg-{{ $index % 2 == 0 ? 'primary' : 'success' }} rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                                                            style="width: 45px; height: 45px;">
                                                            <i
                                                                class="fas fa-{{ $index % 2 == 0 ? 'church' : 'coins' }} text-white fa-lg"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1 font-weight-bold" style="color: #2c3e50;">
                                                            {{ $fund['display_name'] }}</h6>
                                                        <small class="d-none d-md-inline"
                                                            style="color: #6c757d; font-weight: 500;">{{ ucfirst($fund['fund_type'] ?? $fund['offering_type']) }}</small>
                                                        <div class="d-md-none mt-3">
                                                            <div class="d-flex flex-column gap-2">
                                                                <div class="d-flex justify-content-between align-items-center p-2 rounded border-start border-3"
                                                                    style="background-color: #e3f2fd !important; border-left-color: #2196f3 !important;">
                                                                    <span class="small fw-semibold"
                                                                        style="color: #1565c0 !important;"><i
                                                                            class="fas fa-gift me-1"
                                                                            style="color: #1976d2 !important;"></i>From
                                                                        Offerings</span>
                                                                    <span class="fw-bold"
                                                                        style="color: #0d47a1 !important; font-size: 0.95rem; min-width: 80px; text-align: right; display: block !important; visibility: visible !important; opacity: 1 !important;">TZS
                                                                        {{ number_format(isset($fund['offering_amount']) ? $fund['offering_amount'] : 0, 0) }}</span>
                                                                </div>
                                                                <div class="d-flex justify-content-between align-items-center p-2 rounded border-start border-3"
                                                                    style="background-color: #e0f7fa !important; border-left-color: #00bcd4 !important;">
                                                                    <span class="small fw-semibold"
                                                                        style="color: #00838f !important;"><i
                                                                            class="fas fa-heart me-1"
                                                                            style="color: #00acc1 !important;"></i>From
                                                                        Donations</span>
                                                                    <span class="fw-bold"
                                                                        style="color: #006064 !important; font-size: 0.95rem; min-width: 80px; text-align: right; display: block !important; visibility: visible !important; opacity: 1 !important;">TZS
                                                                        {{ number_format(isset($fund['donation_amount']) ? $fund['donation_amount'] : 0, 0) }}</span>
                                                                </div>
                                                                <div
                                                                    class="d-flex justify-content-between align-items-center p-2 bg-{{ $fund['utilization_percentage'] > 80 ? 'danger' : ($fund['utilization_percentage'] > 50 ? 'warning' : 'success') }} bg-opacity-10 rounded border-start border-{{ $fund['utilization_percentage'] > 80 ? 'danger' : ($fund['utilization_percentage'] > 50 ? 'warning' : 'success') }} border-3">
                                                                    <span class="small fw-semibold" style="color: #495057;"><i
                                                                            class="fas fa-percentage me-1"
                                                                            style="color: {{ $fund['utilization_percentage'] > 80 ? '#dc3545' : ($fund['utilization_percentage'] > 50 ? '#ffc107' : '#28a745') }};"></i>Utilization</span>
                                                                    <span
                                                                        class="badge bg-{{ $fund['utilization_percentage'] > 80 ? 'danger' : ($fund['utilization_percentage'] > 50 ? 'warning' : 'success') }} text-white">
                                                                        {{ $fund['utilization_percentage'] }}%
                                                                    </span>
                                                                </div>
                                                                <div class="d-flex gap-2 mt-1">
                                                                    @if($fund['status'] == 'available')
                                                                        <span
                                                                            class="badge bg-success text-white flex-fill text-center py-2">
                                                                            <i class="fas fa-check-circle me-1"></i>Available
                                                                        </span>
                                                                    @else
                                                                        <span
                                                                            class="badge bg-danger text-white flex-fill text-center py-2">
                                                                            <i class="fas fa-exclamation-circle me-1"></i>Depleted
                                                                        </span>
                                                                    @endif
                                                                    @if(!empty($fund['expense_details']) && count($fund['expense_details']) > 0)
                                                                        <button type="button"
                                                                            class="btn btn-sm btn-outline-info flex-fill"
                                                                            onclick="toggleExpenseDetails('{{ $fund['offering_type'] }}')"
                                                                            title="View expense breakdown">
                                                                            <i
                                                                                class="fas fa-list me-1"></i>{{ count($fund['expense_details']) }}
                                                                        </button>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle text-end">
                                                <div class="d-flex flex-column align-items-end">
                                                    <span class="h5 mb-1 font-weight-bold" style="color: #28a745;">
                                                        TZS {{ number_format($fund['total_income'], 0) }}
                                                    </span>
                                                    <small class="fw-semibold d-none d-lg-inline" style="color: #6c757d;">Total
                                                        Income</small>
                                                </div>
                                            </td>
                                            <td class="align-middle text-end d-none d-lg-table-cell">
                                                @if(isset($fund['offering_amount']) && $fund['offering_amount'] > 0)
                                                    <div class="d-flex flex-column align-items-end">
                                                        <span class="font-weight-bold" style="color: #940000;">
                                                            TZS {{ number_format($fund['offering_amount'], 0) }}
                                                        </span>
                                                        <small style="color: #6c757d; font-weight: 500;">From Offerings</small>
                                                    </div>
                                                @else
                                                    <span style="color: #adb5bd;">-</span>
                                                @endif
                                            </td>
                                            <td class="align-middle text-end d-none d-lg-table-cell">
                                                @if(isset($fund['donation_amount']) && $fund['donation_amount'] > 0)
                                                    <div class="d-flex flex-column align-items-end">
                                                        <span class="font-weight-bold" style="color: #36b9cc;">
                                                            TZS {{ number_format($fund['donation_amount'], 0) }}
                                                        </span>
                                                        <small style="color: #6c757d; font-weight: 500;">From Donations</small>
                                                    </div>
                                                @else
                                                    <span style="color: #adb5bd;">-</span>
                                                @endif
                                            </td>
                                            <td class="align-middle text-end">
                                                <div class="d-flex flex-column align-items-end">
                                                    <span class="h5 mb-1 font-weight-bold" style="color: #e0a800;">
                                                        TZS {{ number_format($fund['used_amount'], 0) }}
                                                    </span>
                                                    <small class="fw-semibold d-none d-lg-inline" style="color: #6c757d;">Amount
                                                        Used</small>
                                                    @if(!empty($fund['expense_details']) && count($fund['expense_details']) > 0)
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-info mt-2 d-none d-lg-inline-block"
                                                            onclick="toggleExpenseDetails('{{ $fund['offering_type'] }}')"
                                                            title="View expense breakdown">
                                                            <i class="fas fa-list me-1"></i>{{ count($fund['expense_details']) }}
                                                            {{ count($fund['expense_details']) == 1 ? 'Expense' : 'Expenses' }}
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="align-middle text-end">
                                                <div class="d-flex flex-column align-items-end">
                                                    <span class="h6 mb-0 font-weight-bold"
                                                        style="color: {{ $fund['available_amount'] > 0 ? '#940000' : '#dc3545' }};">
                                                        TZS {{ number_format($fund['available_amount'], 0) }}
                                                    </span>
                                                    <small class="d-none d-lg-inline"
                                                        style="color: #6c757d; font-weight: 500;">Remaining</small>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center d-none d-md-table-cell">
                                                <div class="d-flex flex-column align-items-center">
                                                    <span
                                                        class="badge bg-{{ $fund['utilization_percentage'] > 80 ? 'danger' : ($fund['utilization_percentage'] > 50 ? 'warning' : 'success') }} text-white badge-lg mb-1"
                                                        style="font-size: 0.9rem; padding: 0.5rem 0.75rem; font-weight: 600;">
                                                        {{ $fund['utilization_percentage'] }}%
                                                    </span>
                                                    <small style="color: #6c757d; font-weight: 500;">Utilized</small>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center d-none d-md-table-cell">
                                                <div class="d-flex flex-column align-items-center">
                                                    @if($fund['status'] == 'available')
                                                        <span class="badge bg-success text-white badge-lg mb-1"
                                                            style="font-size: 0.9rem; padding: 0.5rem 0.75rem; font-weight: 600;">
                                                            <i class="fas fa-check-circle me-1"></i>Available
                                                        </span>
                                                    @else
                                                        <span class="badge bg-danger text-white badge-lg mb-1"
                                                            style="font-size: 0.9rem; padding: 0.5rem 0.75rem; font-weight: 600;">
                                                            <i class="fas fa-exclamation-circle me-1"></i>Depleted
                                                        </span>
                                                    @endif
                                                    <small style="color: #6c757d; font-weight: 500;">Status</small>
                                                </div>
                                            </td>
                                            <td class="align-middle pe-3 d-none d-xl-table-cell">
                                                <div class="progress-container position-relative">
                                                    <div class="progress"
                                                        style="height: 32px; border-radius: 16px; background-color: #e9ecef;">
                                                        <div class="progress-bar 
                                                            {{ $fund['utilization_percentage'] > 80 ? 'bg-danger' : ($fund['utilization_percentage'] > 50 ? 'bg-warning' : 'bg-success') }}"
                                                            role="progressbar"
                                                            style="width: {{ $fund['utilization_percentage'] }}%; border-radius: 16px; position: relative;"
                                                            aria-valuenow="{{ $fund['utilization_percentage'] }}"
                                                            aria-valuemin="0" aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="progress-text-overlay position-absolute top-50 start-50 translate-middle text-center">
                                                        <span class="badge bg-dark text-white fw-bold"
                                                            style="font-size: 0.75rem; padding: 0.25rem 0.5rem; border: 2px solid rgba(255,255,255,0.9); background-color: #212529 !important;">
                                                            {{ $fund['utilization_percentage'] }}%
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <!-- Expense Details Row (Collapsible) -->
                                        @if(!empty($fund['expense_details']) && count($fund['expense_details']) > 0)
                                            <tr id="expense-details-{{ $fund['offering_type'] }}" class="expense-details-row"
                                                style="display: none;">
                                                <td colspan="9" class="bg-light">
                                                    <div class="p-3">
                                                        <h6 class="mb-3 fw-bold" style="color: #940000;">
                                                            <i class="fas fa-receipt me-2"></i>Expense Breakdown for
                                                            {{ $fund['display_name'] }}
                                                        </h6>
                                                        <div class="table-responsive">
                                                            <table class="table table-sm table-bordered table-hover mb-0">
                                                                <thead class="table-secondary">
                                                                    <tr>
                                                                        <th>Expense Name</th>
                                                                        <th>Category</th>
                                                                        <th>Budget</th>
                                                                        <th>Date</th>
                                                                        <th class="text-end">Total Expense</th>
                                                                        <th class="text-end">From {{ $fund['display_name'] }}</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($fund['expense_details'] as $expense)
                                                                        <tr>
                                                                            <td>
                                                                                <strong
                                                                                    style="color: #2c3e50;">{{ $expense['expense_name'] }}</strong>
                                                                            </td>
                                                                            <td>
                                                                                <span
                                                                                    class="badge bg-info">{{ $expense['category'] }}</span>
                                                                            </td>
                                                                            <td style="color: #495057;">{{ $expense['budget_name'] }}
                                                                            </td>
                                                                            <td style="color: #6c757d;">
                                                                                {{ \Carbon\Carbon::parse($expense['expense_date'])->format('M d, Y') }}
                                                                            </td>
                                                                            <td class="text-end">
                                                                                <strong style="color: #2c3e50;">TZS
                                                                                    {{ number_format($expense['total_amount'], 0) }}</strong>
                                                                            </td>
                                                                            <td class="text-end">
                                                                                <strong style="color: #e0a800;">TZS
                                                                                    {{ number_format($expense['offering_amount'], 0) }}</strong>
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                    <tr class="table-warning">
                                                                        <td colspan="5" class="text-end fw-bold">Total Used from
                                                                            {{ $fund['display_name'] }}:</td>
                                                                        <td class="text-end fw-bold">TZS
                                                                            {{ number_format($fund['used_from_expenses'], 0) }}</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-5">
                                                <div class="d-flex flex-column align-items-center">
                                                    <i class="fas fa-info-circle fa-3x mb-3" style="color: #adb5bd;"></i>
                                                    <h5 style="color: #495057;">No Offering Data Found</h5>
                                                    <p style="color: #6c757d;">No offering data found for the selected period.
                                                    </p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if(count($fundBreakdown) > 0)
                                                        <tfoot class="table-light">
                                                            <tr class="fw-bold">
                                                                <td class="ps-3">
                                                                    <i class="fas fa-calculator me-2" style="color: #940000;"></i><strong
                                                                        style="color: #2c3e50;">Grand Total</strong>
                                                                </td>
                                                                <td class="text-end">
                                                                    <span class="h6 mb-0 fw-bold" style="color: #28a745;">
                                                                        TZS {{ number_format(collect($fundBreakdown)->sum('total_income'), 0) }}
                                                                    </span>
                                                                </td>
                                                                <td class="text-end d-none d-lg-table-cell">
                                                                    <span class="fw-bold" style="color: #940000;">
                                                                        TZS
                                                                        {{ number_format(collect($fundBreakdown)->sum(function ($f) {
                                    return $f['offering_amount'] ?? 0; }), 0) }}
                                                                    </span>
                                                                </td>
                                                                <td class="text-end d-none d-lg-table-cell">
                                                                    <span class="fw-bold" style="color: #36b9cc;">
                                                                        TZS
                                                                        {{ number_format(collect($fundBreakdown)->sum(function ($f) {
                                    return $f['donation_amount'] ?? 0; }), 0) }}
                                                                    </span>
                                                                </td>
                                                                <td class="text-end">
                                                                    <span class="h6 mb-0 fw-bold" style="color: #e0a800;">
                                                                        TZS {{ number_format(collect($fundBreakdown)->sum('used_amount'), 0) }}
                                                                    </span>
                                                                </td>
                                                                <td class="text-end">
                                                                    <span class="h6 mb-0 fw-bold" style="color: #940000;">
                                                                        TZS {{ number_format(collect($fundBreakdown)->sum('available_amount'), 0) }}
                                                                    </span>
                                                                </td>
                                                                <td class="text-center d-none d-md-table-cell">
                                                                    <span class="badge bg-dark text-white">
                                                                        {{ $totalIncome > 0 ? round((collect($fundBreakdown)->sum('used_amount') / $totalIncome) * 100, 1) : 0 }}%
                                                                    </span>
                                                                </td>
                                                                <td class="text-center d-none d-md-table-cell">
                                                                    <span class="badge bg-secondary text-white">
                                                                        <i class="fas fa-info-circle me-1"></i>Summary
                                                                    </span>
                                                                </td>
                                                                <td class="pe-3 d-none d-xl-table-cell">
                                                                    <div class="progress"
                                                                        style="height: 28px; border-radius: 14px; background-color: #e9ecef;">
                                                                        @php
                                                                            $totalUtilization = $totalIncome > 0 ? round((collect($fundBreakdown)->sum('used_amount') / $totalIncome) * 100, 1) : 0;
                                                                        @endphp
                                                                        <div class="progress-bar 
                                                                            {{ $totalUtilization > 80 ? 'bg-danger' : ($totalUtilization > 50 ? 'bg-warning' : 'bg-success') }}"
                                                                            role="progressbar"
                                                                            style="width: {{ $totalUtilization }}%; border-radius: 14px;"
                                                                            aria-valuenow="{{ $totalUtilization }}" aria-valuemin="0"
                                                                            aria-valuemax="100">
                                                                            <span class="fw-bold text-white small">{{ $totalUtilization }}%</span>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <style>
        /* Custom styles for the fund breakdown report */
        .border-left-success {
            border-left: 0.25rem solid #1cc88a !important;
        }

        .border-left-warning {
            border-left: 0.25rem solid #f6c23e !important;
        }

        .border-left-primary {
            border-left: 0.25rem solid #940000 !important;
        }

        .border-left-info {
            border-left: 0.25rem solid #36b9cc !important;
        }

        .text-xs {
            font-size: 0.7rem;
        }

        .badge-lg {
            font-size: 0.9rem;
            padding: 0.5rem 0.75rem;
            font-weight: 600;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Enhanced badge visibility */
        .badge-success,
        .bg-success {
            background-color: #28a745 !important;
            color: white !important;
        }

        .badge-warning,
        .bg-warning {
            background-color: #ffc107 !important;
            color: #212529 !important;
        }

        .badge-danger,
        .bg-danger {
            background-color: #dc3545 !important;
            color: white !important;
        }

        .badge-primary,
        .bg-primary {
            background-color: #940000 !important;
            color: white !important;
        }

        .badge-dark,
        .bg-dark {
            background-color: #212529 !important;
            color: white !important;
        }

        /* Enhanced summary card colors */
        .text-success {
            color: #1e7e34 !important;
            font-weight: 700;
        }

        .text-warning {
            color: #e0a800 !important;
            font-weight: 700;
        }

        .text-primary {
            color: #0056b3 !important;
            font-weight: 700;
        }

        .text-info {
            color: #0c5460 !important;
            font-weight: 700;
        }

        /* Add subtle glow effect to icons */
        .fa-arrow-up.text-success,
        .fa-arrow-down.text-warning,
        .fa-wallet.text-primary,
        .fa-percentage.text-info {
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
        }

        .progress-text-overlay {
            z-index: 10;
            pointer-events: none;
        }

        .progress-text-overlay .badge {
            background-color: rgba(33, 37, 41, 0.95) !important;
            border: 2px solid rgba(255, 255, 255, 0.8) !important;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.2);
            color: white !important;
            font-weight: 700;
        }

        .progress-container {
            min-height: 30px;
            display: flex;
            align-items: center;
        }

        .fund-row:hover {
            background-color: #f8f9fc;
            transform: translateY(-1px);
            box-shadow: 0 0.15rem 0.35rem rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fc;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%) !important;
        }

        /* Enhanced table styling */
        .table-hover tbody tr {
            transition: all 0.2s ease;
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fc;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        /* Table footer styling */
        .table-light {
            background-color: #f8f9fa !important;
        }

        .table-light td {
            border-top: 2px solid #dee2e6 !important;
            font-weight: 700;
        }

        .table-light .h6 {
            font-weight: 700;
        }

        /* Improved text colors for better readability */
        .table td {
            color: #495057;
        }

        .table th {
            color: #ffffff !important;
            font-weight: 600;
            border-bottom: 2px solid rgba(255, 255, 255, 0.1) !important;
        }

        /* Table header gradient background */
        table thead {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%) !important;
        }

        table thead th {
            background: transparent !important;
            color: #ffffff !important;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            padding: 1rem 0.75rem !important;
        }

        /* Better contrast for labels */
        .table small {
            color: #6c757d !important;
            font-weight: 500;
        }

        /* Better table cell alignment */
        .table td {
            vertical-align: middle;
        }

        /* Enhanced progress bar styling */
        .progress-container {
            min-width: 150px;
        }

        .progress {
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .progress-bar {
            transition: width 0.6s ease;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Card header improvements */
        .card-header.bg-gradient-primary {
            border-bottom: 2px solid rgba(255, 255, 255, 0.2);
        }

        .card {
            border: none;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        .shadow {
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
        }

        .shadow-sm {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
        }

        /* Print styles */
        @media print {

            .btn,
            .card-header,
            .d-flex.justify-content-between {
                display: none !important;
            }

            .card {
                border: 1px solid #dee2e6 !important;
                box-shadow: none !important;
            }

            .table {
                font-size: 0.8rem;
            }
        }

        /* Animation for progress bars */
        .progress-bar {
            transition: width 1s ease-in-out;
        }

        /* Enhanced progress bar styling */
        .progress {
            position: relative;
            overflow: visible;
        }

        .progress-bar {
            position: relative;
            overflow: visible;
        }

        /* Ensure text is always readable */
        .progress-text-overlay {
            white-space: nowrap;
            font-weight: 600;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        /* Mobile Responsive Styles */
        @media (max-width: 768px) {
            .container-fluid {
                padding-left: 0.75rem !important;
                padding-right: 0.75rem !important;
            }

            /* Hide button text on mobile, show only icons */
            .btn-mobile-icon-only {
                padding: 0.375rem 0.5rem !important;
            }

            .btn-mobile-icon-only .btn-text {
                display: none;
            }

            .btn-mobile-icon-only i {
                margin: 0 !important;
            }

            /* Header adjustments */
            h1 {
                font-size: 1.5rem;
            }

            /* Summary cards full width on mobile */
            .col-xl-3.col-md-6 {
                margin-bottom: 1rem;
            }

            /* Table responsive improvements */
            .table-responsive {
                font-size: 0.875rem;
            }

            .table {
                font-size: 0.875rem;
            }

            .table th,
            .table td {
                padding: 0.5rem 0.25rem;
                white-space: nowrap;
            }

            /* Hide some table columns on mobile or make them stack */
            .table th:nth-child(8),
            .table td:nth-child(8) {
                display: none;
                /* Hide Status column on mobile */
            }

            /* Progress bar adjustments */
            .progress-text-overlay .badge {
                font-size: 0.65rem !important;
                padding: 0.2rem 0.4rem !important;
            }

            .progress {
                height: 25px !important;
            }

            /* Card adjustments */
            .card-body {
                padding: 1rem !important;
            }

            /* Icon adjustments */
            .fa-2x {
                font-size: 1.5em !important;
            }

            /* Badge adjustments */
            .badge-lg {
                font-size: 0.8rem;
                padding: 0.375rem 0.5rem;
            }

            /* Form adjustments */
            .form-control-lg {
                font-size: 1rem;
                padding: 0.5rem 0.75rem;
            }
        }

        @media (max-width: 576px) {
            .container-fluid {
                padding-left: 0.5rem !important;
                padding-right: 0.5rem !important;
            }

            h1 {
                font-size: 1.25rem;
            }

            .h3 {
                font-size: 1.1rem;
            }

            .h5,
            .h6 {
                font-size: 1rem;
            }

            /* Table improvements */
            .table {
                font-size: 0.75rem;
            }

            .table th,
            .table td {
                padding: 0.375rem 0.25rem;
            }

            /* Hide more columns on very small screens */
            .table th:nth-child(7),
            .table td:nth-child(7),
            .table th:nth-child(9),
            .table td:nth-child(9) {
                display: none;
                /* Hide Utilization and Progress columns on very small screens */
            }

            /* Progress bar smaller */
            .progress {
                height: 20px !important;
            }

            .progress-text-overlay .badge {
                font-size: 0.6rem !important;
                padding: 0.15rem 0.3rem !important;
            }

            /* Smaller icons */
            .fa-2x {
                font-size: 1.25em !important;
            }

            /* Summary card adjustments */
            .text-xs {
                font-size: 0.65rem;
            }

            /* Button group stack vertically */
            .btn-group {
                flex-direction: column;
                width: 100%;
            }

            .btn-group .btn {
                width: 100%;
                margin-bottom: 0.5rem;
                border-radius: 0.375rem !important;
            }
        }

        /* Custom hover effects */
        .fund-row {
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .fund-row:hover .progress-bar {
            transform: scale(1.02);
        }
    </style>

    <script>
        // Toggle Actions Function
        function toggleActions() {
            // Only toggle on mobile devices
            if (window.innerWidth > 768) {
                return; // Don't toggle on desktop
            }

            const actionsBody = document.getElementById('actionsBody');
            const actionsIcon = document.getElementById('actionsToggleIcon');

            if (!actionsBody || !actionsIcon) return;

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

        // Toggle Filters Function
        function toggleFilters() {
            // Only toggle on mobile devices
            if (window.innerWidth > 768) {
                return; // Don't toggle on desktop
            }

            const filterBody = document.getElementById('filterBody');
            const filterIcon = document.getElementById('filterToggleIcon');
            const filterHeader = document.querySelector('.filter-header');

            if (!filterBody || !filterIcon) return;

            // Check computed style to see if it's visible
            const computedStyle = window.getComputedStyle(filterBody);
            const isVisible = computedStyle.display !== 'none';

            if (isVisible) {
                filterBody.style.display = 'none';
                filterIcon.classList.remove('fa-chevron-up');
                filterIcon.classList.add('fa-chevron-down');
                if (filterHeader) filterHeader.classList.remove('active');
            } else {
                filterBody.style.display = 'block';
                filterIcon.classList.remove('fa-chevron-down');
                filterIcon.classList.add('fa-chevron-up');
                if (filterHeader) filterHeader.classList.add('active');
            }
        }

        // Toggle Fund Breakdown Function
        function toggleFundBreakdown() {
            // Only toggle on mobile devices
            if (window.innerWidth > 768) {
                return; // Don't toggle on desktop
            }

            const fundBreakdownBody = document.getElementById('fundBreakdownBody');
            const fundBreakdownIcon = document.getElementById('fundBreakdownToggleIcon');
            const fundBreakdownHeader = document.querySelector('.fund-breakdown-header');

            if (!fundBreakdownBody || !fundBreakdownIcon) return;

            // Check computed style to see if it's visible
            const computedStyle = window.getComputedStyle(fundBreakdownBody);
            const isVisible = computedStyle.display !== 'none';

            if (isVisible) {
                fundBreakdownBody.style.display = 'none';
                fundBreakdownIcon.style.transform = 'rotate(0deg)';
                if (fundBreakdownHeader) fundBreakdownHeader.classList.remove('active');
            } else {
                fundBreakdownBody.style.display = 'block';
                fundBreakdownIcon.style.transform = 'rotate(180deg)';
                if (fundBreakdownHeader) fundBreakdownHeader.classList.add('active');
            }
        }

        // Handle window resize
        window.addEventListener('resize', function () {
            const actionsBody = document.getElementById('actionsBody');
            const actionsIcon = document.getElementById('actionsToggleIcon');
            const filterBody = document.getElementById('filterBody');
            const filterIcon = document.getElementById('filterToggleIcon');
            const fundBreakdownBody = document.getElementById('fundBreakdownBody');
            const fundBreakdownIcon = document.getElementById('fundBreakdownToggleIcon');

            if (window.innerWidth > 768) {
                // Always show on desktop
                if (actionsBody && actionsIcon) {
                    actionsBody.style.display = 'block';
                    actionsIcon.style.display = 'none';
                }
                if (filterBody && filterIcon) {
                    filterBody.style.display = 'block';
                    filterIcon.style.display = 'none';
                }
                if (fundBreakdownBody && fundBreakdownIcon) {
                    fundBreakdownBody.style.display = 'block';
                    fundBreakdownIcon.style.display = 'none';
                }
            } else {
                // On mobile, show chevrons
                if (actionsIcon) actionsIcon.style.display = 'block';
                if (filterIcon) filterIcon.style.display = 'block';
                if (fundBreakdownIcon) fundBreakdownIcon.style.display = 'block';
            }
        });

        $(document).ready(function () {
            // Initialize actions, filters, and fund breakdown
            const actionsBody = document.getElementById('actionsBody');
            const actionsIcon = document.getElementById('actionsToggleIcon');
            const filterBody = document.getElementById('filterBody');
            const filterIcon = document.getElementById('filterToggleIcon');
            const fundBreakdownBody = document.getElementById('fundBreakdownBody');
            const fundBreakdownIcon = document.getElementById('fundBreakdownToggleIcon');

            if (window.innerWidth <= 768) {
                // Mobile: start collapsed
                if (actionsBody && actionsIcon) {
                    actionsBody.style.display = 'none';
                    actionsIcon.classList.remove('fa-chevron-up');
                    actionsIcon.classList.add('fa-chevron-down');
                }
                if (filterBody && filterIcon) {
                    filterBody.style.display = 'none';
                    filterIcon.classList.remove('fa-chevron-up');
                    filterIcon.classList.add('fa-chevron-down');
                }
                if (fundBreakdownBody && fundBreakdownIcon) {
                    fundBreakdownBody.style.display = 'none';
                    fundBreakdownIcon.style.transform = 'rotate(0deg)';
                }
            } else {
                // Desktop: always show
                if (actionsBody && actionsIcon) {
                    actionsBody.style.display = 'block';
                    actionsIcon.style.display = 'none';
                }
                if (filterBody && filterIcon) {
                    filterBody.style.display = 'block';
                    filterIcon.style.display = 'none';
                }
                if (fundBreakdownBody && fundBreakdownIcon) {
                    fundBreakdownBody.style.display = 'block';
                    fundBreakdownIcon.style.display = 'none';
                }
            }

            // Show filters if any are active
            @if(request('start_date') || request('end_date'))
                if (window.innerWidth <= 768 && filterBody && filterIcon) {
                    toggleFilters(); // Expand if filters are active
                    const filterHeader = document.querySelector('.filter-header');
                    if (filterHeader) filterHeader.classList.add('active');
                }
            @endif

            console.log('Fund Breakdown Report loaded');

            // Add hover effects to fund rows
            $('.fund-row').hover(
                function () {
                    $(this).addClass('shadow-sm');
                },
                function () {
                    $(this).removeClass('shadow-sm');
                }
            );

            // Toggle expense details
            window.toggleExpenseDetails = function (offeringType) {
                const detailsRow = $('#expense-details-' + offeringType);
                if (detailsRow.is(':visible')) {
                    detailsRow.slideUp(300);
                } else {
                    // Close all other expense details
                    $('.expense-details-row').slideUp(300);
                    // Show this one
                    detailsRow.slideDown(300);
                }
            };

            // Add click functionality to fund rows for future expansion
            $('.fund-row').click(function () {
                const fundType = $(this).data('fund');
                console.log('Clicked on fund:', fundType);
                // Future: Could show detailed breakdown modal
            });

            // Animate progress bars on load
            $('.progress-bar').each(function () {
                const width = $(this).attr('style').match(/width: ([\d.]+)%/);
                if (width) {
                    $(this).css('width', '0%');
                    $(this).animate({
                        width: width[1] + '%'
                    }, 1500);
                }
            });

            // Ensure progress text is always visible
            $('.progress-text-overlay').each(function () {
                $(this).css('opacity', '1');
            });

            // Add tooltips to status badges
            $('[data-toggle="tooltip"]').tooltip();

            // Print functionality
            window.print = function () {
                window.print();
            };
        });
    </script>
@endsection