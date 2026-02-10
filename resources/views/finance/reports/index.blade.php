@extends('layouts.index')

@section('content')
    <style>
        /* Mobile Responsive Styles */
        @media (max-width: 768px) {
            .container-fluid {
                padding-left: 0.5rem !important;
                padding-right: 0.5rem !important;
            }

            /* Filter Section */
            #dateFilterForm .card-header {
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

            #dateFilterForm .card-body {
                padding: 0.75rem 0.5rem !important;
            }

            #dateFilterForm .form-label {
                font-size: 0.7rem !important;
                margin-bottom: 0.2rem !important;
                font-weight: 600 !important;
            }

            #dateFilterForm .form-control {
                font-size: 0.8125rem !important;
                padding: 0.4rem 0.5rem !important;
                border-radius: 6px !important;
            }

            #dateFilterForm .btn-sm {
                padding: 0.4rem 0.75rem !important;
                font-size: 0.8125rem !important;
                border-radius: 6px !important;
                font-weight: 600 !important;
            }

            /* Cards - Stack on Mobile */
            .col-xl-3,
            .col-md-6,
            .col-md-3 {
                margin-bottom: 1rem;
            }

            /* Summary Cards - Smaller on Mobile */
            .card-body .h4 {
                font-size: 1.25rem !important;
            }

            /* Table Responsive */
            .table {
                font-size: 0.75rem;
            }

            .table th,
            .table td {
                padding: 0.5rem 0.25rem;
            }

            /* Header adjustments */
            h1 {
                font-size: 1.25rem !important;
            }

            /* Quick Access Cards - Full Width on Mobile */
            .col-md-3 {
                margin-bottom: 1rem;
            }
        }

        /* Desktop: Always show filters */
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
    </style>
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="mt-4"><i class="fas fa-chart-pie me-2"></i>Financial Reports</h1>
        </div>

        <!-- Date Range Filter - Collapsible on Mobile -->
        <form method="GET" class="card mb-4 border-0 shadow-sm" id="dateFilterForm">
            <!-- Filter Header -->
            <div class="card-header bg-primary text-white p-2 px-3 filter-header" onclick="toggleFilters()">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-calendar-alt me-1"></i>
                        <span class="fw-semibold">Financial Summary Period</span>
                    </div>
                    <i class="fas fa-chevron-down text-white d-md-none" id="filterToggleIcon"></i>
                </div>
            </div>

            <!-- Filter Body - Collapsible on Mobile -->
            <div class="card-body p-3" id="filterBody">
                <div class="row g-2 mb-2">
                    <div class="col-12 col-md-4">
                        <label for="start_date" class="form-label small text-muted mb-1">
                            <i class="fas fa-calendar me-1 text-primary"></i>Start Date
                        </label>
                        <input type="date" class="form-control form-control-sm" id="start_date" name="start_date"
                            value="{{ $startDate }}" required>
                    </div>
                    <div class="col-12 col-md-4">
                        <label for="end_date" class="form-label small text-muted mb-1">
                            <i class="fas fa-calendar me-1 text-info"></i>End Date
                        </label>
                        <input type="date" class="form-control form-control-sm" id="end_date" name="end_date"
                            value="{{ $endDate }}" required>
                    </div>
                    <div class="col-12 col-md-4 d-flex gap-2 align-items-end">
                        <button type="submit" class="btn btn-primary btn-sm flex-fill">
                            <i class="fas fa-search me-1"></i>
                            <span class="d-none d-sm-inline">Update</span>
                            <span class="d-sm-none">Apply</span>
                        </button>
                        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-times me-1"></i>
                            <span class="d-none d-sm-inline">Reset</span>
                        </a>
                    </div>
                </div>
            </div>
        </form>

        <!-- Financial Summary Overview -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <i class="fas fa-chart-pie me-1"></i>
                        <strong>Financial Summary - {{ $financialSummary['period']['start'] }} to
                            {{ $financialSummary['period']['end'] }}</strong>
                    </div>
                    <div class="card-body">
                        <!-- Summary Cards -->
                        <div class="row mb-4 g-3">
                            <div class="col-xl-3 col-md-6 col-12">
                                <div class="card bg-primary text-white mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <div class="small text-white-50">Total Income</div>
                                                <div class="h4">TZS
                                                    {{ number_format($financialSummary['summary']['total_income'], 0) }}
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-arrow-up fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6 col-12">
                                <div class="card bg-danger text-white mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <div class="small text-white-50">Total Expenses</div>
                                                <div class="h4">TZS
                                                    {{ number_format($financialSummary['summary']['total_expenses'], 0) }}
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-arrow-down fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6 col-12">
                                <div
                                    class="card bg-{{ $financialSummary['summary']['net_income'] >= 0 ? 'success' : 'warning' }} text-white mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <div class="small text-white-50">Net Income</div>
                                                <div class="h4">TZS
                                                    {{ number_format($financialSummary['summary']['net_income'], 0) }}
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <i
                                                    class="fas fa-{{ $financialSummary['summary']['net_income'] >= 0 ? 'check' : 'exclamation' }}-circle fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6 col-12">
                                <div class="card bg-info text-white mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <div class="small text-white-50">Pending Approval</div>
                                                <div class="h4">TZS
                                                    {{ number_format($financialSummary['summary']['total_pending'], 0) }}
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-clock fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Detailed Breakdown -->
                        <div class="row g-3">
                            <!-- Tithes -->
                            <div class="col-xl-3 col-md-6 col-12">
                                <div class="card h-100">
                                    <div class="card-header bg-primary text-white">
                                        <i class="fas fa-coins me-1"></i>
                                        <strong>Tithes</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="text-center mb-3">
                                            <h3 class="text-primary">TZS
                                                {{ number_format($financialSummary['tithes']['total'], 0) }}
                                            </h3>
                                            <small class="text-muted">{{ $financialSummary['tithes']['count'] }}
                                                transactions</small>
                                        </div>
                                        @if($financialSummary['tithes']['pending'] > 0)
                                            <div class="alert alert-warning alert-sm">
                                                <i class="fas fa-clock me-1"></i>
                                                Pending: TZS {{ number_format($financialSummary['tithes']['pending'], 0) }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Offerings -->
                            <div class="col-xl-3 col-md-6 col-12">
                                <div class="card h-100">
                                    <div class="card-header bg-success text-white">
                                        <i class="fas fa-gift me-1"></i>
                                        <strong>Offerings</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="text-center mb-3">
                                            <h3 class="text-success">TZS
                                                {{ number_format($financialSummary['offerings']['total'], 0) }}
                                            </h3>
                                            <small class="text-muted">{{ $financialSummary['offerings']['count'] }}
                                                transactions</small>
                                        </div>
                                        @if($financialSummary['offerings']['pending'] > 0)
                                            <div class="alert alert-warning alert-sm">
                                                <i class="fas fa-clock me-1"></i>
                                                Pending: TZS {{ number_format($financialSummary['offerings']['pending'], 0) }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Donations -->
                            <div class="col-xl-3 col-md-6 col-12">
                                <div class="card h-100">
                                    <div class="card-header bg-info text-white">
                                        <i class="fas fa-heart me-1"></i>
                                        <strong>Donations</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="text-center mb-3">
                                            <h3 class="text-info">TZS
                                                {{ number_format($financialSummary['donations']['total'], 0) }}
                                            </h3>
                                            <small class="text-muted">{{ $financialSummary['donations']['count'] }}
                                                transactions</small>
                                        </div>
                                        @if($financialSummary['donations']['pending'] > 0)
                                            <div class="alert alert-warning alert-sm">
                                                <i class="fas fa-clock me-1"></i>
                                                Pending: TZS {{ number_format($financialSummary['donations']['pending'], 0) }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Pledges -->
                            <div class="col-xl-3 col-md-6 col-12">
                                <div class="card h-100">
                                    <div class="card-header bg-warning text-white">
                                        <i class="fas fa-handshake me-1"></i>
                                        <strong>Pledges</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="text-center mb-3">
                                            <h3 class="text-warning">TZS
                                                {{ number_format($financialSummary['pledges']['total_paid'], 0) }}
                                            </h3>
                                            <small class="text-muted">{{ $financialSummary['pledges']['count'] }}
                                                pledges</small>
                                        </div>
                                        <div class="small text-muted">
                                            <div>Pledged: TZS
                                                {{ number_format($financialSummary['pledges']['total_pledged'], 0) }}
                                            </div>
                                            <div>Outstanding: TZS
                                                {{ number_format($financialSummary['pledges']['outstanding'], 0) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Type Breakdowns -->
        <div class="row mb-4">
            <!-- Offering Types Breakdown -->
            @if($financialSummary['offerings']['types']->count() > 0)
                <div class="col-xl-6 mb-4">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <i class="fas fa-gift me-1"></i>
                            <strong>Offering Types Breakdown</strong>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th class="text-end">Amount</th>
                                            <th class="text-center">Count</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($financialSummary['offerings']['types'] as $type)
                                            <tr>
                                                <td>
                                                    <span
                                                        class="badge bg-success">{{ ucfirst(str_replace('_', ' ', $type->offering_type)) }}</span>
                                                </td>
                                                <td class="text-end">
                                                    <strong>TZS {{ number_format($type->total_amount, 0) }}</strong>
                                                </td>
                                                <td class="text-center">{{ $type->count }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Donation Types Breakdown -->
            @if($financialSummary['donations']['types']->count() > 0)
                <div class="col-xl-6 mb-4">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <i class="fas fa-heart me-1"></i>
                            <strong>Donation Types Breakdown</strong>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th class="text-end">Amount</th>
                                            <th class="text-center">Count</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($financialSummary['donations']['types'] as $type)
                                            <tr>
                                                <td>
                                                    <span
                                                        class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $type->donation_type)) }}</span>
                                                </td>
                                                <td class="text-end">
                                                    <strong>TZS {{ number_format($type->total_amount, 0) }}</strong>
                                                </td>
                                                <td class="text-center">{{ $type->count }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Pledge Types Breakdown -->
        @if($financialSummary['pledges']['types']->count() > 0)
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-warning text-white">
                            <i class="fas fa-handshake me-1"></i>
                            <strong>Pledge Types Breakdown</strong>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th class="text-end">Total Pledged</th>
                                            <th class="text-end">Amount Paid</th>
                                            <th class="text-end">Outstanding</th>
                                            <th class="text-center">Count</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($financialSummary['pledges']['types'] as $type)
                                            <tr>
                                                <td>
                                                    <span
                                                        class="badge bg-warning">{{ ucfirst(str_replace('_', ' ', $type->pledge_type)) }}</span>
                                                </td>
                                                <td class="text-end">
                                                    <strong>TZS {{ number_format($type->total_pledged, 0) }}</strong>
                                                </td>
                                                <td class="text-end">
                                                    <span class="text-success">TZS {{ number_format($type->total_paid, 0) }}</span>
                                                </td>
                                                <td class="text-end">
                                                    <span class="text-danger">TZS
                                                        {{ number_format($type->total_pledged - $type->total_paid, 0) }}</span>
                                                </td>
                                                <td class="text-center">{{ $type->count }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Report Cards -->
        <div class="row mb-4 g-3">
            <div class="col-xl-3 col-md-6 col-12">
                <div class="card bg-primary text-white mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="small text-white-50">Member Giving Report</div>
                                <div class="h6">Individual Member Analysis</div>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-user-chart fa-2x"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white text-decoration-none" href="{{ route('reports.member-giving') }}">
                            View Report
                        </a>
                        <div class="small text-white-50">
                            <i class="fas fa-angle-right"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card bg-success text-white mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="small text-white-50">Department Giving</div>
                                <div class="h6">Giving by Category</div>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-building fa-2x"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white text-decoration-none" href="{{ route('reports.department-giving') }}">
                            View Report
                        </a>
                        <div class="small text-white-50">
                            <i class="fas fa-angle-right"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 col-12">
                <div class="card bg-warning text-white mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="small text-white-50">Income vs Expenditure</div>
                                <div class="h6">Financial Performance</div>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-chart-line fa-2x"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white text-decoration-none"
                            href="{{ route('reports.income-vs-expenditure') }}">
                            View Report
                        </a>
                        <div class="small text-white-50">
                            <i class="fas fa-angle-right"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 col-12">
                <div class="card bg-info text-white mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="small text-white-50">Budget Performance</div>
                                <div class="h6">Budget vs Actual</div>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-wallet fa-2x"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white text-decoration-none" href="{{ route('reports.budget-performance') }}">
                            View Report
                        </a>
                        <div class="small text-white-50">
                            <i class="fas fa-angle-right"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card bg-success text-white mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="small text-white-50">Fund Breakdown</div>
                                <div class="h6">Fund Analysis</div>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-chart-pie fa-2x"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white text-decoration-none"
                            href="{{ route('reports.offering-fund-breakdown') }}">
                            View Report
                        </a>
                        <div class="small text-white-50">
                            <i class="fas fa-angle-right"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="row mb-4">
            <div class="col-xl-8">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-chart-bar me-1"></i>
                        <strong>Report Categories</strong>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="text-primary">Giving Reports</h5>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success me-2"></i>Member Giving Analysis</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Department/Category Giving</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Pledge Tracking</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Donation Summary</li>
                                    <li><i class="fas fa-check text-primary me-2"></i>Special Offering Member Breakdown</li>
                                    @if(auth()->user()->isSecretary() || auth()->user()->isPastor() || auth()->user()->isAdmin())
                                        <li><i class="fas fa-star text-warning me-2"></i>General Secretary's Annual Report</li>
                                    @endif
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h5 class="text-primary">Financial Reports</h5>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success me-2"></i>Income vs Expenditure</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Budget Performance</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Fund Breakdown</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Expense Analysis</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Financial Trends</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-info-circle me-1"></i>
                        <strong>Report Features</strong>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <h6 class="text-primary">Export Options</h6>
                            <p class="small text-muted">Export reports to PDF or Excel format for sharing and archiving.</p>
                        </div>
                        <div class="mb-3">
                            <h6 class="text-primary">Date Filtering</h6>
                            <p class="small text-muted">Filter reports by custom date ranges for specific periods.</p>
                        </div>
                        <div class="mb-3">
                            <h6 class="text-primary">Real-time Data</h6>
                            <p class="small text-muted">All reports are generated with the latest financial data.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Reports -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-history me-1"></i>
                <strong>Quick Access</strong>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @if(auth()->user()->isSecretary() || auth()->user()->isPastor() || auth()->user()->isAdmin())
                        <div class="col-md-3 col-12">
                            <div class="card h-100 border-primary shadow-sm">
                                <div class="card-body text-center">
                                    <i class="fas fa-file-invoice-dollar fa-3x text-primary mb-3"></i>
                                    <h5 class="card-title fw-bold">General Secretary's Report</h5>
                                    <p class="card-text">Annual Income and Expenditure summary report.</p>
                                    <a href="{{ route('reports.general-secretary') }}"
                                        class="btn btn-primary rounded-pill px-4">View Report</a>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="col-md-3 col-12">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-user-chart fa-3x text-primary mb-3"></i>
                                <h5 class="card-title">Member Giving</h5>
                                <p class="card-text">Analyze individual member contributions and giving patterns.</p>
                                <a href="{{ route('reports.member-giving') }}" class="btn btn-primary">View Report</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-12">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-building fa-3x text-success mb-3"></i>
                                <h5 class="card-title">Department Giving</h5>
                                <p class="card-text">View giving breakdown by departments and categories.</p>
                                <a href="{{ route('reports.department-giving') }}" class="btn btn-success">View Report</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-12">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-chart-line fa-3x text-warning mb-3"></i>
                                <h5 class="card-title">Income vs Expenditure</h5>
                                <p class="card-text">Compare income against expenses for financial health.</p>
                                <a href="{{ route('reports.income-vs-expenditure') }}" class="btn btn-warning">View
                                    Report</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-12">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-wallet fa-3x text-info mb-3"></i>
                                <h5 class="card-title">Budget Performance</h5>
                                <p class="card-text">Track budget utilization and performance metrics.</p>
                                <a href="{{ route('reports.budget-performance') }}" class="btn btn-info">View Report</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-12">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-chart-pie fa-3x text-success mb-3"></i>
                                <h5 class="card-title">Fund Breakdown</h5>
                                <p class="card-text">View offering types with income, used, and available amounts.</p>
                                <a href="{{ route('reports.offering-fund-breakdown') }}" class="btn btn-success">View
                                    Report</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-12">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-calendar-alt fa-3x text-primary mb-3"></i>
                                <h5 class="card-title">Monthly Financial Report</h5>
                                <p class="card-text">Comprehensive monthly financial report with all income and expenses.
                                </p>
                                <a href="{{ route('reports.monthly-financial') }}" class="btn btn-primary">View Report</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-12">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-calendar-week fa-3x text-info mb-3"></i>
                                <h5 class="card-title">Weekly Financial Report</h5>
                                <p class="card-text">Comprehensive weekly financial report with all income and expenses.</p>
                                <a href="{{ route('reports.weekly-financial') }}" class="btn btn-info">View Report</a>
                            </div>
                        </div>
                        <div class="col-md-3 col-12">
                            <div class="card h-100 border-primary shadow-sm">
                                <div class="card-body text-center">
                                    <i class="fas fa-file-invoice-dollar fa-3x text-primary mb-3"></i>
                                    <h5 class="card-title fw-bold">Special Offering Report</h5>
                                    <p class="card-text">Detailed per-member grid for Sadaka ya Umoja and Jengo.</p>
                                    <a href="{{ route('reports.special-offerings') }}"
                                        class="btn btn-primary rounded-pill px-4">View Report</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
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

            // Handle window resize
            window.addEventListener('resize', function () {
                const filterBody = document.getElementById('filterBody');
                const filterIcon = document.getElementById('filterToggleIcon');

                if (window.innerWidth > 768) {
                    // Always show on desktop
                    if (filterBody && filterIcon) {
                        filterBody.style.display = 'block';
                        filterIcon.style.display = 'none';
                    }
                } else {
                    // On mobile, show chevron
                    if (filterIcon) filterIcon.style.display = 'block';
                }
            });

            // Initialize on page load
            document.addEventListener('DOMContentLoaded', function () {
                const filterBody = document.getElementById('filterBody');
                const filterIcon = document.getElementById('filterToggleIcon');

                if (window.innerWidth <= 768) {
                    // Mobile: start collapsed
                    if (filterBody && filterIcon) {
                        filterBody.style.display = 'none';
                        filterIcon.classList.remove('fa-chevron-up');
                        filterIcon.classList.add('fa-chevron-down');
                    }
                } else {
                    // Desktop: always show
                    if (filterBody && filterIcon) {
                        filterBody.style.display = 'block';
                        filterIcon.style.display = 'none';
                    }
                }
            });
        </script>

@endsection