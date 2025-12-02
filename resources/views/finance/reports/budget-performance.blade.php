@extends('layouts.index')

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
        .filters-form .form-control,
        .filters-form .form-select {
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
        .col-xl-3, .col-md-6, .col-md-4 {
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
        
        /* Chart Container */
        #monthlyChart {
            max-height: 300px !important;
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
        <div class="card-header bg-white border-bottom p-2 px-3 d-flex align-items-center justify-content-between actions-header" onclick="toggleActions()">
            <div class="d-flex align-items-center gap-2">
                <h1 class="mb-0 mt-2" style="font-size: 1.5rem;"><i class="fas fa-wallet me-2"></i>Budget Performance</h1>
            </div>
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-chevron-down text-muted d-md-none" id="actionsToggleIcon"></i>
            </div>
        </div>
        <div class="card-body p-3" id="actionsBody">
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-success btn-sm" onclick="exportReport('pdf')">
                    <i class="fas fa-file-pdf me-1"></i>
                    <span class="d-none d-sm-inline">Export PDF</span>
                    <span class="d-sm-none">PDF</span>
                </button>
                <button type="button" class="btn btn-primary btn-sm" onclick="exportReport('excel')">
                    <i class="fas fa-file-excel me-1"></i>
                    <span class="d-none d-sm-inline">Export Excel</span>
                    <span class="d-sm-none">Excel</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Filters & Search - Collapsible on Mobile -->
    <form method="GET" action="{{ route('reports.budget-performance') }}" class="card mb-4 border-0 shadow-sm filters-form">
        <!-- Filter Header -->
        <div class="card-header report-header-neutral py-2 px-3 filter-header" onclick="toggleFilters()">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-filter me-1"></i>
                    <span class="fw-semibold text-white">Report Filters</span>
                    @if(request('budget_id') || request('start_date') || request('end_date'))
                        <span class="badge bg-white text-dark rounded-pill ms-2" id="activeFiltersCount">{{ (request('budget_id') ? 1 : 0) + (request('start_date') ? 1 : 0) + (request('end_date') ? 1 : 0) }}</span>
                    @endif
                </div>
                <i class="fas fa-chevron-down text-white d-md-none" id="filterToggleIcon"></i>
            </div>
        </div>
        
        <!-- Filter Body - Collapsible on Mobile -->
        <div class="card-body p-3" id="filterBody">
            <div class="row g-2 mb-2">
                <!-- Budget - Full Width on Mobile -->
                <div class="col-12 col-md-4">
                    <label for="budget_id" class="form-label small text-muted mb-1">
                        <i class="fas fa-wallet me-1 text-primary"></i>Select Budget
                    </label>
                    <select class="form-select form-select-sm" id="budget_id" name="budget_id">
                        <option value="">All Budgets</option>
                        @foreach($budgets as $b)
                            <option value="{{ $b->id }}" {{ request('budget_id') == $b->id ? 'selected' : '' }}>
                                {{ $b->budget_name }} ({{ $b->fiscal_year }})
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Start Date - Full Width on Mobile -->
                <div class="col-6 col-md-3">
                    <label for="start_date" class="form-label small text-muted mb-1">
                        <i class="fas fa-calendar me-1 text-info"></i>Start Date
                    </label>
                    <input type="date" class="form-control form-control-sm" id="start_date" name="start_date" value="{{ request('start_date', date('Y-01-01')) }}">
                </div>
                
                <!-- End Date - Full Width on Mobile -->
                <div class="col-6 col-md-3">
                    <label for="end_date" class="form-label small text-muted mb-1">
                        <i class="fas fa-calendar me-1 text-success"></i>End Date
                    </label>
                    <input type="date" class="form-control form-control-sm" id="end_date" name="end_date" value="{{ request('end_date', date('Y-12-31')) }}">
                </div>
                
                <!-- Action Buttons - Full Width on Mobile -->
                <div class="col-12 col-md-2 d-flex gap-2 align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm flex-fill">
                        <i class="fas fa-search me-1"></i>
                        <span class="d-none d-sm-inline">Generate</span>
                        <span class="d-sm-none">Go</span>
                    </button>
                </div>
            </div>
        </div>
    </form>

    @if($budget)
    <!-- Budget Summary -->
    <div class="row mb-4 g-3">
        <div class="col-xl-3 col-md-6 col-12">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Total Budget</div>
                            <div class="h4">TZS {{ number_format($budget->total_budget, 0) }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-wallet fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 col-12">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Amount Spent</div>
                            <div class="h4">TZS {{ number_format($budget->spent_amount, 0) }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-receipt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 col-12">
            <div class="card bg-info text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Remaining</div>
                            <div class="h4">TZS {{ number_format($budget->remaining_amount, 0) }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-piggy-bank fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 col-12">
            <div class="card {{ $budget->is_over_budget ? 'bg-danger' : ($budget->is_near_limit ? 'bg-warning' : 'bg-success') }} text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Utilization</div>
                            <div class="h4">{{ $budget->utilization_percentage }}%</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-chart-pie fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Budget Information -->
    <div class="card mb-4">
        <div class="card-header report-header-primary py-2">
            <h6 class="mb-0 text-white"><i class="fas fa-info-circle me-1"></i>Budget Information</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>{{ $budget->budget_name }}</h5>
                    <p class="text-muted">Type: {{ ucfirst($budget->budget_type) }}</p>
                    <p class="text-muted">Fiscal Year: {{ $budget->fiscal_year }}</p>
                    <p class="text-muted">Period: {{ $budget->start_date->format('M d, Y') }} - {{ $budget->end_date->format('M d, Y') }}</p>
                </div>
                <div class="col-md-6">
                    <h6>Budget Status</h6>
                    <div class="progress mb-2" style="height: 25px;">
                        <div class="progress-bar {{ $budget->is_over_budget ? 'bg-danger' : ($budget->is_near_limit ? 'bg-warning' : 'bg-success') }}" 
                             style="width: {{ min($budget->utilization_percentage, 100) }}%">
                            {{ $budget->utilization_percentage }}%
                        </div>
                    </div>
                    <p class="text-muted">
                        @if($budget->is_over_budget)
                            <i class="fas fa-exclamation-triangle text-danger me-1"></i>Over Budget
                        @elseif($budget->is_near_limit)
                            <i class="fas fa-exclamation-circle text-warning me-1"></i>Near Limit
                        @else
                            <i class="fas fa-check-circle text-success me-1"></i>On Track
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Performance Chart -->
    <div class="card mb-4">
        <div class="card-header report-header-info py-2">
            <h6 class="mb-0 text-white"><i class="fas fa-chart-line me-1"></i>Monthly Budget Performance</h6>
        </div>
        <div class="card-body">
            <div style="position: relative; height: 400px; width: 100%;">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Expenses by Category -->
    <div class="card mb-4">
        <div class="card-header report-header-warning py-2">
            <h6 class="mb-0 text-white"><i class="fas fa-chart-bar me-1"></i>Expenses by Category</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="categoryTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Total Amount</th>
                            <th>Transaction Count</th>
                            <th>Average Amount</th>
                            <th>Percentage of Budget</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expensesByCategory as $category => $data)
                        <tr>
                            <td>
                                <span class="badge bg-secondary">{{ ucfirst($category) }}</span>
                            </td>
                            <td class="text-end">TZS {{ number_format($data['total'], 0) }}</td>
                            <td class="text-center">{{ $data['count'] }}</td>
                            <td class="text-end">TZS {{ number_format($data['avg'], 0) }}</td>
                            <td class="text-end">{{ $budget->total_budget > 0 ? number_format(($data['total'] / $budget->total_budget) * 100, 1) : 0 }}%</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">No expense data found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Expenses -->
    <div class="card mb-4">
        <div class="card-header report-header-neutral py-2">
            <h6 class="mb-0 text-white"><i class="fas fa-receipt me-1"></i>Recent Expenses</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="expensesTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="d-none d-md-table-cell">Date</th>
                            <th class="d-table-cell d-md-none">Expense</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Amount</th>
                            <th class="d-none d-lg-table-cell">Vendor</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $expense)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $expense->expense_name }}</div>
                                <div class="d-md-none">
                                    <small class="text-muted d-block">
                                        <i class="fas fa-calendar me-1"></i>{{ $expense->expense_date->format('M d, Y') }}
                                    </small>
                                    @if($expense->vendor)
                                        <small class="text-muted d-block">
                                            <i class="fas fa-store me-1"></i>{{ $expense->vendor }}
                                        </small>
                                    @endif
                                </div>
                            </td>
                            <td class="d-none d-md-table-cell">{{ $expense->expense_date->format('M d, Y') }}</td>
                            <td class="d-md-none">{{ $expense->expense_name }}</td>
                            <td>
                                <span class="badge bg-info">{{ ucfirst($expense->expense_category) }}</span>
                            </td>
                            <td class="text-end">
                                <span class="fw-bold text-primary">TZS {{ number_format($expense->amount, 0) }}</span>
                            </td>
                            <td class="d-none d-lg-table-cell">{{ $expense->vendor ?? '-' }}</td>
                            <td>
                                @if($expense->status == 'paid')
                                    <span class="badge bg-success">Paid</span>
                                @elseif($expense->status == 'approved')
                                    <span class="badge bg-primary">Approved</span>
                                @else
                                    <span class="badge bg-warning">Pending</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No expenses found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @else
    <!-- Budget Selection -->
    <div class="card mb-4">
        <div class="card-header report-header-primary py-2">
            <h6 class="mb-0 text-white"><i class="fas fa-wallet me-1"></i>Select a Budget to View Performance</h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                @foreach($budgets as $b)
                <div class="col-md-4 col-12">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">{{ $b->budget_name }}</h5>
                            <p class="card-text text-muted">{{ $b->budget_type }} - {{ $b->fiscal_year }}</p>
                            <div class="progress mb-2" style="height: 20px;">
                                <div class="progress-bar {{ $b->is_over_budget ? 'bg-danger' : ($b->is_near_limit ? 'bg-warning' : 'bg-success') }}" 
                                     style="width: {{ min($b->utilization_percentage, 100) }}%">
                                    {{ $b->utilization_percentage }}%
                                </div>
                            </div>
                            <p class="card-text">
                                <small class="text-muted">
                                    TZS {{ number_format($b->spent_amount, 0) }} / {{ number_format($b->total_budget, 0) }}
                                </small>
                            </p>
                            <a href="{{ route('reports.budget-performance', ['budget_id' => $b->id]) }}" class="btn btn-primary">View Performance</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>

@if($budget && isset($monthlyData))
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('monthlyChart').getContext('2d');
    const monthlyData = @json($monthlyData);
    const budgetTotal = {{ $budget->total_budget }};
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: monthlyData.map(item => item.month),
            datasets: [{
                label: 'Amount Spent',
                data: monthlyData.map(item => item.spent),
                backgroundColor: 'rgba(255, 99, 132, 0.8)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }, {
                label: 'Budget Limit',
                data: monthlyData.map(item => item.budget),
                backgroundColor: 'rgba(54, 162, 235, 0.8)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'TZS ' + value.toLocaleString();
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': TZS ' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            }
        }
    });
});
</script>
@endif

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

// Handle window resize
window.addEventListener('resize', function() {
    const actionsBody = document.getElementById('actionsBody');
    const actionsIcon = document.getElementById('actionsToggleIcon');
    const filterBody = document.getElementById('filterBody');
    const filterIcon = document.getElementById('filterToggleIcon');
    
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
    } else {
        // On mobile, show chevrons
        if (actionsIcon) actionsIcon.style.display = 'block';
        if (filterIcon) filterIcon.style.display = 'block';
    }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Initialize actions and filters
    const actionsBody = document.getElementById('actionsBody');
    const actionsIcon = document.getElementById('actionsToggleIcon');
    const filterBody = document.getElementById('filterBody');
    const filterIcon = document.getElementById('filterToggleIcon');
    
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
    }
    
    // Show filters if any are active
    @if(request('budget_id') || request('start_date') || request('end_date'))
        if (window.innerWidth <= 768 && filterBody && filterIcon) {
            toggleFilters(); // Expand if filters are active
            const filterHeader = document.querySelector('.filter-header');
            if (filterHeader) filterHeader.classList.add('active');
        }
    @endif
});

function exportReport(format) {
    const budgetId = '{{ $budget->id ?? "" }}';
    const startDate = '{{ $startDate }}';
    const endDate = '{{ $endDate }}';
    const baseUrl = '{{ url("/") }}';
    
    const url = `${baseUrl}/reports/export/${format}?report_type=budget-performance&budget_id=${budgetId}&start_date=${startDate}&end_date=${endDate}`;
    
    // Force download - server will send Content-Disposition header
    window.location.href = url;
}
</script>
@endsection

<style>
.report-header-primary{
    background: linear-gradient(135deg, #4e73df 0%, #6f42c1 100%) !important;
}
.report-header-info{
    background: linear-gradient(135deg, #36b9cc 0%, #2aa2b3 100%) !important;
}
.report-header-warning{
    background: linear-gradient(135deg, #f6c23e 0%, #d6a62f 100%) !important;
}
.report-header-neutral{
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%) !important;
}
.report-header-primary, .report-header-info, .report-header-warning, .report-header-neutral{
    color: #fff !important;
}
.report-header-primary h6, .report-header-info h6, .report-header-warning h6, .report-header-neutral h6{
    color: #fff !important;
}
</style>
