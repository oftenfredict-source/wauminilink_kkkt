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
            .col-xl-3,
            .col-md-6,
            .col-xl-6,
            .col-md-4 {
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
            #monthlyTrendChart,
            #incomeChart,
            #expenseChart {
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
            <div class="card-header bg-white border-bottom p-2 px-3 d-flex align-items-center justify-content-between actions-header"
                onclick="toggleActions()">
                <div class="d-flex align-items-center gap-2">
                    <h1 class="mb-0 mt-2" style="font-size: 1.5rem;"><i class="fas fa-chart-line me-2"></i>Income vs
                        Expenditure</h1>
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
        <form method="GET" action="{{ route('reports.income-vs-expenditure') }}" id="reportForm"
            class="card mb-4 border-0 shadow-sm filters-form">
            <!-- Filter Header -->
            <div class="card-header bg-primary text-white p-2 px-3 filter-header" onclick="toggleFilters()">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-filter me-1"></i>
                        <span class="fw-semibold">Report Filters</span>
                        @if(request('filter_type') || request('start_date') || request('end_date') || request('month'))
                            <span class="badge bg-white text-primary rounded-pill ms-2"
                                id="activeFiltersCount">{{ (request('filter_type') ? 1 : 0) + (request('start_date') ? 1 : 0) + (request('end_date') ? 1 : 0) + (request('month') ? 1 : 0) }}</span>
                        @endif
                    </div>
                    <i class="fas fa-chevron-down text-white d-md-none" id="filterToggleIcon"></i>
                </div>
            </div>

            <!-- Filter Body - Collapsible on Mobile -->
            <div class="card-body p-3" id="filterBody">
                <div class="row g-2 mb-2">
                    <!-- Filter Type - Full Width on Mobile -->
                    <div class="col-12 col-md-3">
                        <label for="filter_type" class="form-label small text-muted mb-1">
                            <i class="fas fa-filter me-1 text-primary"></i>Filter Type
                        </label>
                        <select class="form-select form-select-sm" id="filter_type" name="filter_type"
                            onchange="toggleFilterType()">
                            <option value="date_range" {{ !request('filter_type') || request('filter_type') == 'date_range' ? 'selected' : '' }}>Date Range</option>
                            <option value="month" {{ request('filter_type') == 'month' ? 'selected' : '' }}>Specific Month
                            </option>
                        </select>
                    </div>

                    <!-- Month Filter - Full Width on Mobile -->
                    <div class="col-12 col-md-3" id="month_filter"
                        style="display: {{ request('filter_type') == 'month' ? 'block' : 'none' }};">
                        <label for="month" class="form-label small text-muted mb-1">
                            <i class="fas fa-calendar me-1 text-info"></i>Select Month
                        </label>
                        <input type="month" class="form-control form-control-sm" id="month" name="month"
                            value="{{ request('month', date('Y-m')) }}">
                    </div>

                    <!-- Start Date Filter - Full Width on Mobile -->
                    <div class="col-6 col-md-3" id="start_date_filter"
                        style="display: {{ !request('filter_type') || request('filter_type') == 'date_range' ? 'block' : 'none' }};">
                        <label for="start_date" class="form-label small text-muted mb-1">
                            <i class="fas fa-calendar me-1 text-success"></i>Start Date
                        </label>
                        <input type="date" class="form-control form-control-sm" id="start_date" name="start_date"
                            value="{{ request('start_date', date('Y-01-01')) }}">
                    </div>

                    <!-- End Date Filter - Full Width on Mobile -->
                    <div class="col-6 col-md-3" id="end_date_filter"
                        style="display: {{ !request('filter_type') || request('filter_type') == 'date_range' ? 'block' : 'none' }};">
                        <label for="end_date" class="form-label small text-muted mb-1">
                            <i class="fas fa-calendar me-1 text-warning"></i>End Date
                        </label>
                        <input type="date" class="form-control form-control-sm" id="end_date" name="end_date"
                            value="{{ request('end_date', date('Y-12-31')) }}">
                    </div>

                    <!-- Action Buttons - Full Width on Mobile -->
                    <div class="col-12 d-flex gap-2 align-items-end">
                        <button type="submit" class="btn btn-primary btn-sm flex-fill" id="generateBtn">
                            <i class="fas fa-search me-1"></i>
                            <span class="d-none d-sm-inline">Generate Report</span>
                            <span class="d-sm-none">Generate</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <!-- Summary Cards -->
        <div class="row mb-4 g-3">
            <div class="col-xl-3 col-md-6 col-12">
                <div class="card bg-success text-white mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="small text-white-50">Total Income</div>
                                <div class="h4">TZS {{ number_format($totalIncome, 0) }}</div>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-arrow-up fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 col-12">
                <div class="card bg-danger text-white mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="small text-white-50">Total Expenses</div>
                                <div class="h4">TZS {{ number_format($totalExpenses, 0) }}</div>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-arrow-down fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 col-12">
                <div class="card {{ $netIncome >= 0 ? 'bg-primary' : 'bg-warning' }} text-white mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="small text-white-50">Net Income</div>
                                <div class="h4">TZS {{ number_format($netIncome, 0) }}</div>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-chart-line fa-2x"></i>
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
                                <div class="small text-white-50">Profit Margin</div>
                                <div class="h4">
                                    {{ $totalIncome > 0 ? number_format(($netIncome / $totalIncome) * 100, 1) : 0 }}%
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-percentage fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Income Breakdown -->
        <div class="row mb-4 g-3">
            <div class="col-xl-6 col-12">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-chart-pie me-1"></i><strong>Income Sources</strong>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Source</th>
                                        <th>Amount</th>
                                        <th>Percentage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><i class="fas fa-coins text-primary me-2"></i>Tithes</td>
                                        <td class="text-end">TZS {{ number_format($tithes, 0) }}</td>
                                        <td class="text-end">
                                            {{ $totalIncome > 0 ? number_format(($tithes / $totalIncome) * 100, 1) : 0 }}%
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><i class="fas fa-gift text-success me-2"></i>Offerings</td>
                                        <td class="text-end">TZS {{ number_format($offerings, 0) }}</td>
                                        <td class="text-end">
                                            {{ $totalIncome > 0 ? number_format(($offerings / $totalIncome) * 100, 1) : 0 }}%
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><i class="fas fa-handshake text-warning me-2"></i>Ahadi ya Bwana</td>
                                        <td class="text-end">TZS {{ number_format($ahadiIncome, 0) }}</td>
                                        <td class="text-end">
                                            {{ $totalIncome > 0 ? number_format(($ahadiIncome / $totalIncome) * 100, 1) : 0 }}%
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 col-12">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-chart-bar me-1"></i><strong>Expenses by Category</strong>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Category</th>
                                        <th>Amount</th>
                                        <th>Count</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($expensesByCategory as $category => $data)
                                        <tr>
                                            <td><span class="badge bg-secondary">{{ ucfirst($category) }}</span></td>
                                            <td class="text-end">TZS {{ number_format($data['total'], 0) }}</td>
                                            <td class="text-center">{{ $data['count'] }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">No expense data found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Trend Chart -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-chart-line me-1"></i><strong>Monthly Income vs Expenditure Trend</strong>
            </div>
            <div class="card-body">
                <div style="position: relative; height: 400px; width: 100%;">
                    <canvas id="monthlyTrendChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Income vs Expenditure Pie Chart -->
        <div class="row mb-4 g-3">
            <div class="col-xl-6 col-12">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-chart-pie me-1"></i><strong>Income Distribution</strong>
                    </div>
                    <div class="card-body">
                        <div style="position: relative; height: 400px; width: 100%;">
                            <canvas id="incomeChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 col-12">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-chart-pie me-1"></i><strong>Expense Distribution</strong>
                    </div>
                    <div class="card-body">
                        <div style="position: relative; height: 400px; width: 100%;">
                            <canvas id="expenseChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Health Indicators -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-heartbeat me-1"></i><strong>Financial Health Indicators</strong>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4 col-12">
                        <div class="text-center">
                            <h5 class="text-primary">Expense Ratio</h5>
                            <div
                                class="h3 {{ ($totalExpenses / max($totalIncome, 1)) <= 0.8 ? 'text-success' : (($totalExpenses / max($totalIncome, 1)) <= 0.95 ? 'text-warning' : 'text-danger') }}">
                                {{ number_format(($totalExpenses / max($totalIncome, 1)) * 100, 1) }}%
                            </div>
                            <small class="text-muted">Expenses as % of Income</small>
                        </div>
                    </div>
                    <div class="col-md-4 col-12">
                        <div class="text-center">
                            <h5 class="text-primary">Savings Rate</h5>
                            <div class="h3 {{ $netIncome >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $netIncome >= 0 ? number_format(($netIncome / max($totalIncome, 1)) * 100, 1) : 0 }}%
                            </div>
                            <small class="text-muted">Net Income as % of Total Income</small>
                        </div>
                    </div>
                    <div class="col-md-4 col-12">
                        <div class="text-center">
                            <h5 class="text-primary">Financial Status</h5>
                            <div class="h3 {{ $netIncome >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $netIncome >= 0 ? 'Healthy' : 'Deficit' }}
                            </div>
                            <small class="text-muted">Overall Financial Position</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Monthly Trend Chart
            const trendCtx = document.getElementById('monthlyTrendChart').getContext('2d');
            const monthlyData = @json($monthlyData);

            new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: monthlyData.map(item => item.month),
                    datasets: [{
                        label: 'Income',
                        data: monthlyData.map(item => item.income),
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.1,
                        fill: false
                    }, {
                        label: 'Expenses',
                        data: monthlyData.map(item => item.expenses),
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        tension: 0.1,
                        fill: false
                    }, {
                        label: 'Net Income',
                        data: monthlyData.map(item => item.net),
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        tension: 0.1,
                        fill: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function (value) {
                                    return 'TZS ' + value.toLocaleString();
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return context.dataset.label + ': TZS ' + context.parsed.y.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });

            // Income Chart
            const incomeCtx = document.getElementById('incomeChart').getContext('2d');
            new Chart(incomeCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Tithes', 'Offerings', 'Ahadi ya Bwana'],
                    datasets: [{
                        data: [{{ $tithes ?? 0 }}, {{ $offerings ?? 0 }}, {{ $ahadiIncome ?? 0 }}],
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.8)',
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(255, 159, 64, 0.8)'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return context.label + ': TZS ' + context.parsed.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });

            // Expense Chart
            const expenseCtx = document.getElementById('expenseChart').getContext('2d');
            const expenseData = @json($expensesByCategory);

            new Chart(expenseCtx, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(expenseData),
                    datasets: [{
                        data: Object.values(expenseData).map(item => item.total),
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.8)',
                            'rgba(54, 162, 235, 0.8)',
                            'rgba(255, 206, 86, 0.8)',
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(153, 102, 255, 0.8)',
                            'rgba(255, 159, 64, 0.8)'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return context.label + ': TZS ' + context.parsed.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>

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
        window.addEventListener('resize', function () {
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

        document.addEventListener('DOMContentLoaded', function () {
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
            @if(request('filter_type') || request('start_date') || request('end_date') || request('month'))
                if (window.innerWidth <= 768 && filterBody && filterIcon) {
                    toggleFilters(); // Expand if filters are active
                    const filterHeader = document.querySelector('.filter-header');
                    if (filterHeader) filterHeader.classList.add('active');
                }
            @endif

                    // Handle form submission
                    const form = document.getElementById('reportForm');
            const generateBtn = document.getElementById('generateBtn');

            if (form && generateBtn) {
                form.addEventListener('submit', function (e) {
                    generateBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Generating...';
                    generateBtn.disabled = true;

                    // Re-enable button after 3 seconds in case of issues
                    setTimeout(function () {
                        generateBtn.innerHTML = 'Generate Report';
                        generateBtn.disabled = false;
                    }, 3000);
                });
            }
        });

        function toggleFilterType() {
            const filterType = document.getElementById('filter_type').value;
            const monthFilter = document.getElementById('month_filter');
            const startDateFilter = document.getElementById('start_date_filter');
            const endDateFilter = document.getElementById('end_date_filter');

            if (filterType === 'month') {
                monthFilter.style.display = 'block';
                startDateFilter.style.display = 'none';
                endDateFilter.style.display = 'none';
                document.getElementById('start_date').removeAttribute('required');
                document.getElementById('end_date').removeAttribute('required');
            } else {
                monthFilter.style.display = 'none';
                startDateFilter.style.display = 'block';
                endDateFilter.style.display = 'block';
                document.getElementById('start_date').setAttribute('required', 'required');
                document.getElementById('end_date').setAttribute('required', 'required');
            }
        }

        function exportReport(format) {
            const filterType = document.getElementById('filter_type')?.value || '{{ request("filter_type", "date_range") }}';
            // Get the current page URL and extract the base path
            const currentPath = window.location.pathname;
            const basePath = currentPath.substring(0, currentPath.indexOf('/reports/'));
            const baseUrl = window.location.origin + basePath;
            let url = `${baseUrl}/reports/export/${format}?report_type=income-vs-expenditure`;

            if (filterType === 'month') {
                const month = document.getElementById('month')?.value || '{{ request("month", date("Y-m")) }}';
                if (month) {
                    const [year, monthNum] = month.split('-');
                    const startDate = `${year}-${monthNum}-01`;
                    const endDate = new Date(year, monthNum, 0).toISOString().split('T')[0];
                    url += `&start_date=${startDate}&end_date=${endDate}&filter_type=month&month=${month}`;
                }
            } else {
                const startDate = document.getElementById('start_date')?.value || '{{ $startDate }}';
                const endDate = document.getElementById('end_date')?.value || '{{ $endDate }}';
                url += `&start_date=${startDate}&end_date=${endDate}`;
            }

            // Force download - server will send Content-Disposition header
            window.location.href = url;
        }
    </script>
@endsection