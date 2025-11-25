@extends('layouts.index')

@section('title', 'Fund Breakdown Report')

@section('content')
<div class="container-fluid px-4">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-2">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-chart-pie text-primary me-2"></i>Fund Breakdown Report
            </h1>
            <p class="text-muted mb-0 d-none d-md-block">Comprehensive analysis of funds (offerings and donations) and their utilization</p>
        </div>
        <div class="btn-group w-100 w-md-auto" role="group">
            <button type="button" class="btn btn-outline-primary btn-mobile-icon-only" onclick="window.print()">
                <i class="fas fa-print"></i>
                <span class="btn-text ms-1 d-none d-md-inline">Print</span>
            </button>
            <a href="{{ route('reports.export.pdf', ['report_type' => 'offering-fund-breakdown', 'start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}" 
               class="btn btn-danger btn-mobile-icon-only">
                <i class="fas fa-file-pdf"></i>
                <span class="btn-text ms-1 d-none d-md-inline">PDF</span>
            </a>
            <a href="{{ route('reports.export.excel', ['report_type' => 'offering-fund-breakdown', 'start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}" 
               class="btn btn-success btn-mobile-icon-only">
                <i class="fas fa-file-excel"></i>
                <span class="btn-text ms-1 d-none d-md-inline">Excel</span>
            </a>
        </div>
    </div>

    <!-- Date Range Filter Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-gradient-primary text-white">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-calendar-alt me-2"></i>
                        <h6 class="mb-0">Report Period Filter</h6>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.offering-fund-breakdown') }}" class="row g-3">
                        <div class="col-12 col-md-4">
                            <label for="start_date" class="form-label fw-bold">Start Date</label>
                            <input type="date" class="form-control form-control-lg" id="start_date" name="start_date" 
                                   value="{{ $startDate->format('Y-m-d') }}">
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="end_date" class="form-label fw-bold">End Date</label>
                            <input type="date" class="form-control form-control-lg" id="end_date" name="end_date" 
                                   value="{{ $endDate->format('Y-m-d') }}">
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-search me-1"></i><span class="d-none d-sm-inline">Update Report</span><span class="d-sm-none">Update</span>
                                </button>
                                <a href="{{ route('reports.offering-fund-breakdown') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-refresh me-1"></i><span class="d-none d-sm-inline">Reset to Current Year</span><span class="d-sm-none">Reset</span>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Income</div>
                            <div class="h5 mb-0 font-weight-bold text-success">TZS {{ number_format($totalIncome, 0) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-up fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Used</div>
                            <div class="h5 mb-0 font-weight-bold text-warning">TZS {{ number_format($totalUsed, 0) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-down fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Available</div>
                            <div class="h5 mb-0 font-weight-bold text-primary">TZS {{ number_format($totalAvailable, 0) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wallet fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
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
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-table me-2"></i>Fund Breakdown
                        </h6>
                        <span class="badge bg-primary text-white" style="font-size: 0.9rem; padding: 0.5rem 0.75rem; font-weight: 600;">{{ count($fundBreakdown) }} {{ count($fundBreakdown) == 1 ? 'Fund Type' : 'Fund Types' }}</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th class="border-0">
                                        <i class="fas fa-tag me-2"></i>Fund Type
                                    </th>
                                    <th class="border-0 text-end">
                                        <i class="fas fa-arrow-up me-2"></i>Total Income
                                    </th>
                                    <th class="border-0 text-end">
                                        <i class="fas fa-gift me-2"></i>From Offerings
                                    </th>
                                    <th class="border-0 text-end">
                                        <i class="fas fa-heart me-2"></i>From Donations
                                    </th>
                                    <th class="border-0 text-end">
                                        <i class="fas fa-arrow-down me-2"></i>Used Amount
                                    </th>
                                    <th class="border-0 text-end">
                                        <i class="fas fa-wallet me-2"></i>Available
                                    </th>
                                    <th class="border-0 text-center">
                                        <i class="fas fa-percentage me-2"></i>Utilization
                                    </th>
                                    <th class="border-0 text-center">
                                        <i class="fas fa-info-circle me-2"></i>Status
                                    </th>
                                    <th class="border-0 text-center">
                                        <i class="fas fa-chart-bar me-2"></i>Progress
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($fundBreakdown as $index => $fund)
                                <tr class="fund-row" data-fund="{{ $fund['fund_type'] ?? $fund['offering_type'] }}">
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <div class="bg-{{ $index % 2 == 0 ? 'primary' : 'success' }} rounded-circle d-flex align-items-center justify-content-center" 
                                                     style="width: 40px; height: 40px;">
                                                    <i class="fas fa-{{ $index % 2 == 0 ? 'church' : 'coins' }} text-white"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 font-weight-bold text-gray-800">{{ $fund['display_name'] }}</h6>
                                                <small class="text-muted">{{ ucfirst($fund['fund_type'] ?? $fund['offering_type']) }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle text-end">
                                        <div class="d-flex flex-column align-items-end">
                                            <span class="h5 mb-1 text-success font-weight-bold">
                                                TZS {{ number_format($fund['total_income'], 0) }}
                                            </span>
                                            <small class="text-muted fw-bold">Total Income</small>
                                        </div>
                                    </td>
                                    <td class="align-middle text-end">
                                        @if(isset($fund['offering_amount']) && $fund['offering_amount'] > 0)
                                            <div class="d-flex flex-column align-items-end">
                                                <span class="text-primary font-weight-bold">
                                                    TZS {{ number_format($fund['offering_amount'], 0) }}
                                                </span>
                                                <small class="text-muted">From Offerings</small>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-end">
                                        @if(isset($fund['donation_amount']) && $fund['donation_amount'] > 0)
                                            <div class="d-flex flex-column align-items-end">
                                                <span class="text-info font-weight-bold">
                                                    TZS {{ number_format($fund['donation_amount'], 0) }}
                                                </span>
                                                <small class="text-muted">From Donations</small>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-end">
                                        <div class="d-flex flex-column align-items-end">
                                            <span class="h5 mb-1 text-warning font-weight-bold">
                                                TZS {{ number_format($fund['used_amount'], 0) }}
                                            </span>
                                            <small class="text-muted fw-bold">Amount Used</small>
                                            @if(!empty($fund['expense_details']) && count($fund['expense_details']) > 0)
                                                <button type="button" class="btn btn-sm btn-outline-info mt-2" 
                                                        onclick="toggleExpenseDetails('{{ $fund['offering_type'] }}')"
                                                        title="View expense breakdown">
                                                    <i class="fas fa-list me-1"></i>{{ count($fund['expense_details']) }} {{ count($fund['expense_details']) == 1 ? 'Expense' : 'Expenses' }}
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="align-middle text-end">
                                        <div class="d-flex flex-column align-items-end">
                                            <span class="h6 mb-0 text-{{ $fund['available_amount'] > 0 ? 'primary' : 'danger' }} font-weight-bold">
                                                TZS {{ number_format($fund['available_amount'], 0) }}
                                            </span>
                                            <small class="text-muted">Remaining</small>
                                        </div>
                                    </td>
                                    <td class="align-middle text-center">
                                        <div class="d-flex flex-column align-items-center">
                                            <span class="badge bg-{{ $fund['utilization_percentage'] > 80 ? 'danger' : ($fund['utilization_percentage'] > 50 ? 'warning' : 'success') }} text-white badge-lg mb-1" style="font-size: 0.9rem; padding: 0.5rem 0.75rem; font-weight: 600;">
                                                {{ $fund['utilization_percentage'] }}%
                                            </span>
                                            <small class="text-muted">Utilized</small>
                                        </div>
                                    </td>
                                    <td class="align-middle text-center">
                                        <div class="d-flex flex-column align-items-center">
                                            @if($fund['status'] == 'available')
                                                <span class="badge bg-success text-white badge-lg mb-1" style="font-size: 0.9rem; padding: 0.5rem 0.75rem; font-weight: 600;">
                                                    <i class="fas fa-check-circle me-1"></i>Available
                                                </span>
                                            @else
                                                <span class="badge bg-danger text-white badge-lg mb-1" style="font-size: 0.9rem; padding: 0.5rem 0.75rem; font-weight: 600;">
                                                    <i class="fas fa-exclamation-circle me-1"></i>Depleted
                                                </span>
                                            @endif
                                            <small class="text-muted">Status</small>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <div class="progress-container position-relative">
                                            <div class="progress" style="height: 30px; border-radius: 15px; background-color: #e9ecef;">
                                                <div class="progress-bar 
                                                    {{ $fund['utilization_percentage'] > 80 ? 'bg-danger' : ($fund['utilization_percentage'] > 50 ? 'bg-warning' : 'bg-success') }}" 
                                                    role="progressbar" 
                                                    style="width: {{ $fund['utilization_percentage'] }}%; border-radius: 15px; position: relative;"
                                                    aria-valuenow="{{ $fund['utilization_percentage'] }}" 
                                                    aria-valuemin="0" 
                                                    aria-valuemax="100">
                                                </div>
                                            </div>
                                            <div class="progress-text-overlay position-absolute top-50 start-50 translate-middle text-center">
                                                <span class="badge bg-dark text-white fw-bold" style="font-size: 0.8rem; padding: 0.3rem 0.6rem; border: 2px solid rgba(255,255,255,0.8); background-color: #212529 !important;">
                                                    {{ $fund['utilization_percentage'] }}%
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Expense Details Row (Collapsible) -->
                                @if(!empty($fund['expense_details']) && count($fund['expense_details']) > 0)
                                <tr id="expense-details-{{ $fund['offering_type'] }}" class="expense-details-row" style="display: none;">
                                    <td colspan="7" class="bg-light">
                                        <div class="p-3">
                                            <h6 class="mb-3 fw-bold text-primary">
                                                <i class="fas fa-receipt me-2"></i>Expense Breakdown for {{ $fund['display_name'] }}
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
                                                                <strong>{{ $expense['expense_name'] }}</strong>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-info">{{ $expense['category'] }}</span>
                                                            </td>
                                                            <td>{{ $expense['budget_name'] }}</td>
                                                            <td>{{ \Carbon\Carbon::parse($expense['expense_date'])->format('M d, Y') }}</td>
                                                            <td class="text-end">
                                                                <strong>TZS {{ number_format($expense['total_amount'], 0) }}</strong>
                                                            </td>
                                                            <td class="text-end">
                                                                <strong class="text-warning">TZS {{ number_format($expense['offering_amount'], 0) }}</strong>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                        <tr class="table-warning">
                                                            <td colspan="5" class="text-end fw-bold">Total Used from {{ $fund['display_name'] }}:</td>
                                                            <td class="text-end fw-bold">TZS {{ number_format($fund['used_from_expenses'], 0) }}</td>
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
                                    <td colspan="7" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">No Offering Data Found</h5>
                                            <p class="text-muted">No offering data found for the selected period.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
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
    border-left: 0.25rem solid #4e73df !important;
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
    background-color: #4e73df !important;
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
    background: linear-gradient(45deg, #4e73df, #224abe) !important;
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
    .btn, .card-header, .d-flex.justify-content-between {
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
    .table th:nth-child(6),
    .table td:nth-child(6) {
        display: none; /* Hide Status column on mobile */
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
    
    .h5, .h6 {
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
    .table th:nth-child(5),
    .table td:nth-child(5) {
        display: none; /* Hide Utilization column on very small screens */
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
$(document).ready(function() {
    console.log('Fund Breakdown Report loaded');
    
    // Add hover effects to fund rows
    $('.fund-row').hover(
        function() {
            $(this).addClass('shadow-sm');
        },
        function() {
            $(this).removeClass('shadow-sm');
        }
    );
    
    // Toggle expense details
    window.toggleExpenseDetails = function(offeringType) {
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
    $('.fund-row').click(function() {
        const fundType = $(this).data('fund');
        console.log('Clicked on fund:', fundType);
        // Future: Could show detailed breakdown modal
    });
    
    // Animate progress bars on load
    $('.progress-bar').each(function() {
        const width = $(this).attr('style').match(/width: ([\d.]+)%/);
        if (width) {
            $(this).css('width', '0%');
            $(this).animate({
                width: width[1] + '%'
            }, 1500);
        }
    });
    
    // Ensure progress text is always visible
    $('.progress-text-overlay').each(function() {
        $(this).css('opacity', '1');
    });
    
    // Add tooltips to status badges
    $('[data-toggle="tooltip"]').tooltip();
    
    // Print functionality
    window.print = function() {
        window.print();
    };
});
</script>
@endsection
