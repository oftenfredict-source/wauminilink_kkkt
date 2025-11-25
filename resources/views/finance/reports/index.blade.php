@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mt-4"><i class="fas fa-chart-pie me-2"></i>Financial Reports</h1>
    </div>

    <!-- Date Range Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-calendar-alt me-1"></i>
                    <strong>Financial Summary Period</strong>
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" 
                                   value="{{ $startDate }}" required>
                        </div>
                        <div class="col-md-4">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" 
                                   value="{{ $endDate }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i>Update Summary
                                </button>
                                <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-refresh me-1"></i>Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Summary Overview -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <i class="fas fa-chart-pie me-1"></i>
                    <strong>Financial Summary - {{ $financialSummary['period']['start'] }} to {{ $financialSummary['period']['end'] }}</strong>
                </div>
                <div class="card-body">
                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-primary text-white mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <div class="small text-white-50">Total Income</div>
                                            <div class="h4">TZS {{ number_format($financialSummary['summary']['total_income'], 0) }}</div>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-arrow-up fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-danger text-white mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <div class="small text-white-50">Total Expenses</div>
                                            <div class="h4">TZS {{ number_format($financialSummary['summary']['total_expenses'], 0) }}</div>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-arrow-down fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-{{ $financialSummary['summary']['net_income'] >= 0 ? 'success' : 'warning' }} text-white mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <div class="small text-white-50">Net Income</div>
                                            <div class="h4">TZS {{ number_format($financialSummary['summary']['net_income'], 0) }}</div>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-{{ $financialSummary['summary']['net_income'] >= 0 ? 'check' : 'exclamation' }}-circle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-info text-white mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <div class="small text-white-50">Pending Approval</div>
                                            <div class="h4">TZS {{ number_format($financialSummary['summary']['total_pending'], 0) }}</div>
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
                    <div class="row">
                        <!-- Tithes -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-primary text-white">
                                    <i class="fas fa-coins me-1"></i>
                                    <strong>Tithes</strong>
                                </div>
                                <div class="card-body">
                                    <div class="text-center mb-3">
                                        <h3 class="text-primary">TZS {{ number_format($financialSummary['tithes']['total'], 0) }}</h3>
                                        <small class="text-muted">{{ $financialSummary['tithes']['count'] }} transactions</small>
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
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-success text-white">
                                    <i class="fas fa-gift me-1"></i>
                                    <strong>Offerings</strong>
                                </div>
                                <div class="card-body">
                                    <div class="text-center mb-3">
                                        <h3 class="text-success">TZS {{ number_format($financialSummary['offerings']['total'], 0) }}</h3>
                                        <small class="text-muted">{{ $financialSummary['offerings']['count'] }} transactions</small>
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
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-info text-white">
                                    <i class="fas fa-heart me-1"></i>
                                    <strong>Donations</strong>
                                </div>
                                <div class="card-body">
                                    <div class="text-center mb-3">
                                        <h3 class="text-info">TZS {{ number_format($financialSummary['donations']['total'], 0) }}</h3>
                                        <small class="text-muted">{{ $financialSummary['donations']['count'] }} transactions</small>
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
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-warning text-white">
                                    <i class="fas fa-handshake me-1"></i>
                                    <strong>Pledges</strong>
                                </div>
                                <div class="card-body">
                                    <div class="text-center mb-3">
                                        <h3 class="text-warning">TZS {{ number_format($financialSummary['pledges']['total_paid'], 0) }}</h3>
                                        <small class="text-muted">{{ $financialSummary['pledges']['count'] }} pledges</small>
                                    </div>
                                    <div class="small text-muted">
                                        <div>Pledged: TZS {{ number_format($financialSummary['pledges']['total_pledged'], 0) }}</div>
                                        <div>Outstanding: TZS {{ number_format($financialSummary['pledges']['outstanding'], 0) }}</div>
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
                                        <span class="badge bg-success">{{ ucfirst(str_replace('_', ' ', $type->offering_type)) }}</span>
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
                                        <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $type->donation_type)) }}</span>
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
                                        <span class="badge bg-warning">{{ ucfirst(str_replace('_', ' ', $type->pledge_type)) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <strong>TZS {{ number_format($type->total_pledged, 0) }}</strong>
                                    </td>
                                    <td class="text-end">
                                        <span class="text-success">TZS {{ number_format($type->total_paid, 0) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <span class="text-danger">TZS {{ number_format($type->total_pledged - $type->total_paid, 0) }}</span>
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
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
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
        
        <div class="col-xl-3 col-md-6">
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
                    <a class="small text-white text-decoration-none" href="{{ route('reports.income-vs-expenditure') }}">
                        View Report
                    </a>
                    <div class="small text-white-50">
                        <i class="fas fa-angle-right"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
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
                    <a class="small text-white text-decoration-none" href="{{ route('reports.offering-fund-breakdown') }}">
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
            <div class="row">
                <div class="col-md-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-user-chart fa-3x text-primary mb-3"></i>
                            <h5 class="card-title">Member Giving</h5>
                            <p class="card-text">Analyze individual member contributions and giving patterns.</p>
                            <a href="{{ route('reports.member-giving') }}" class="btn btn-primary">View Report</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-building fa-3x text-success mb-3"></i>
                            <h5 class="card-title">Department Giving</h5>
                            <p class="card-text">View giving breakdown by departments and categories.</p>
                            <a href="{{ route('reports.department-giving') }}" class="btn btn-success">View Report</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-chart-line fa-3x text-warning mb-3"></i>
                            <h5 class="card-title">Income vs Expenditure</h5>
                            <p class="card-text">Compare income against expenses for financial health.</p>
                            <a href="{{ route('reports.income-vs-expenditure') }}" class="btn btn-warning">View Report</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-wallet fa-3x text-info mb-3"></i>
                            <h5 class="card-title">Budget Performance</h5>
                            <p class="card-text">Track budget utilization and performance metrics.</p>
                            <a href="{{ route('reports.budget-performance') }}" class="btn btn-info">View Report</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-chart-pie fa-3x text-success mb-3"></i>
                            <h5 class="card-title">Fund Breakdown</h5>
                            <p class="card-text">View offering types with income, used, and available amounts.</p>
                            <a href="{{ route('reports.offering-fund-breakdown') }}" class="btn btn-success">View Report</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-calendar-alt fa-3x text-primary mb-3"></i>
                            <h5 class="card-title">Monthly Financial Report</h5>
                            <p class="card-text">Comprehensive monthly financial report with all income and expenses.</p>
                            <a href="{{ route('reports.monthly-financial') }}" class="btn btn-primary">View Report</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-calendar-week fa-3x text-info mb-3"></i>
                            <h5 class="card-title">Weekly Financial Report</h5>
                            <p class="card-text">Comprehensive weekly financial report with all income and expenses.</p>
                            <a href="{{ route('reports.weekly-financial') }}" class="btn btn-info">View Report</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection


