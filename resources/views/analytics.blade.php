@extends('layouts.index')

@section('content')
    <div class="container-fluid px-4 py-4">
        <!-- Premium Header with Glassmorphism -->
        <div class="analytics-header mb-4 p-4 rounded-4 shadow-sm position-relative overflow-hidden">
            <div
                class="header-content position-relative z-index-2 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="header-icon-glow rounded-3 d-flex align-items-center justify-content-center">
                        <i class="fas fa-chart-line fa-lg"></i>
                    </div>
                    <div>
                        <h1 class="h3 mb-0 fw-bold text-white">Analytics Dashboard</h1>
                        <p class="mb-0 text-white-50 opacity-75">Visualizing church growth and ministry impact</p>
                    </div>
                </div>

                <!-- Global Filter Bar -->
                <div class="filter-glass p-2 rounded-3 d-flex align-items-center gap-2 flex-wrap">
                    <form id="analyticsFilterForm" method="GET" class="d-flex align-items-center gap-2 mb-0">
                        <select name="filter" id="filterType"
                            class="form-select form-select-sm bg-transparent text-white border-white-25">
                            <option value="year" {{ $filter === 'year' ? 'selected' : '' }} class="bg-dark">Yearly View
                            </option>
                            <option value="custom" {{ $filter === 'custom' ? 'selected' : '' }} class="bg-dark">Custom Range
                            </option>
                        </select>

                        <div id="yearPicker" class="{{ $filter === 'year' ? '' : 'd-none' }}">
                            <select name="year"
                                class="form-select form-select-sm bg-transparent text-white border-white-25">
                                @foreach($availableYears as $y)
                                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }} class="bg-dark">{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="customPicker" class="d-flex gap-2 {{ $filter === 'custom' ? '' : 'd-none' }}">
                            <input type="date" name="start_date" value="{{ $startDate }}"
                                class="form-control form-control-sm bg-transparent text-white border-white-25 px-2">
                            <span class="text-white-50 align-self-center">-</span>
                            <input type="date" name="end_date" value="{{ $endDate }}"
                                class="form-control form-control-sm bg-transparent text-white border-white-25 px-2">
                        </div>

                        <button type="submit" class="btn btn-sm btn-light-glass px-3">
                            <i class="fas fa-sync-alt me-1"></i> Update
                        </button>
                    </form>
                </div>
            </div>
            <div class="header-bg-glow"></div>
        </div>

        <!-- Navigation Tabs -->
        <ul class="nav nav-pills custom-tabs mb-4 gap-2" id="analyticsTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active rounded-pill px-4" id="overview-tab" data-bs-toggle="tab"
                    data-bs-target="#overview" type="button" role="tab">
                    <i class="fas fa-th-large me-2"></i>Overview
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-4" id="financial-tab" data-bs-toggle="tab"
                    data-bs-target="#financial" type="button" role="tab">
                    <i class="fas fa-dollar-sign me-2"></i>Financial
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-4" id="members-tab" data-bs-toggle="tab" data-bs-target="#members"
                    type="button" role="tab">
                    <i class="fas fa-users me-2"></i>Demographics
                </button>
            </li>

        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="analyticsTabsContent">
            <!-- Overview Tab -->
            <div class="tab-pane fade show active" id="overview" role="tabpanel">
                <div class="row g-4 mb-4">
                    <!-- KPI Summary Cards -->
                    <div class="col-md-3">
                        <div class="glass-card p-4 h-100 border-start border-primary border-4 rounded-3">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-muted small mb-1 fw-semibold text-uppercase">Net Income</p>
                                    <h3
                                        class="mb-0 fw-bold {{ $financialData['totals']['net_income'] >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($financialData['totals']['net_income']) }}
                                    </h3>
                                </div>
                                <div class="badge-icon bg-soft-primary"><i class="fas fa-wallet"></i></div>
                            </div>
                            <div class="mt-3 small text-muted">For selected period</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="glass-card p-4 h-100 border-start border-success border-4 rounded-3">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-muted small mb-1 fw-semibold text-uppercase">New Registrations</p>
                                    <h3 class="mb-0 fw-bold">{{ number_format($memberData['totals']['total']) }}</h3>
                                </div>
                                <div class="badge-icon bg-soft-success"><i class="fas fa-user-plus"></i></div>
                            </div>
                            <div class="mt-3 small text-muted">Adults & Children combined</div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="glass-card p-4 h-100 border-start border-warning border-4 rounded-3">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-muted small mb-1 fw-semibold text-uppercase">Total Events</p>
                                    <h3 class="mb-0 fw-bold">
                                        {{ $eventData['events']['total'] + $eventData['celebrations']['total'] }}
                                    </h3>
                                </div>
                                <div class="badge-icon bg-soft-warning"><i class="fas fa-star"></i></div>
                            </div>
                            <div class="mt-3 small text-muted">Special events & celebrations</div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="glass-card p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="mb-0 fw-bold">Combined Growth Trend</h5>
                                <span class="badge bg-soft-primary text-primary px-3 rounded-pill">{{ $year ?? 'Period' }}
                                    Trend</span>
                            </div>
                            <div style="height: 350px;">
                                <canvas id="overviewGrowthChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="glass-card p-4 h-100">
                            <h5 class="mb-4 fw-bold">Income Distribution</h5>
                            <div style="height: 300px;">
                                <canvas id="overviewIncomeDoughnut"></canvas>
                            </div>
                            <div class="mt-4">
                                <div class="d-flex justify-content-between mb-2 small">
                                    <span class="text-muted"><i class="fas fa-circle text-primary me-2"></i>Tithes</span>
                                    <span class="fw-bold">{{ number_format($financialData['totals']['tithes']) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2 small">
                                    <span class="text-muted"><i class="fas fa-circle text-success me-2"></i>Offerings</span>
                                    <span class="fw-bold">{{ number_format($financialData['totals']['offerings']) }}</span>
                                </div>
                                <!-- Donations removed -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Tab -->
            <div class="tab-pane fade" id="financial" role="tabpanel">
                <div class="glass-card p-4 mb-4">
                    <h5 class="mb-4 fw-bold">Detailed Financial Performance</h5>
                    <div style="height: 400px;">
                        <canvas id="detailedFinancialChart"></canvas>
                    </div>
                </div>
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="glass-card p-4">
                            <h5 class="mb-4 fw-bold">5-Year Revenue Trend</h5>
                            <div style="height: 300px;">
                                <canvas id="yearlyRevenueChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="glass-card p-4">
                            <h5 class="mb-4 fw-bold">Expense VS Income</h5>
                            <div style="height: 300px;">
                                <canvas id="incomeExpenseComparison"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Demographics Tab -->
            <div class="tab-pane fade" id="members" role="tabpanel">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="glass-card p-4">
                            <h5 class="mb-4 fw-bold">Age Group Distribution</h5>
                            <div style="height: 400px;">
                                <canvas id="ageDistributionChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="glass-card p-4">
                            <h5 class="mb-4 fw-bold">Gender & Membership</h5>
                            <div class="row g-4">
                                <div class="col-12" style="height: 250px;">
                                    <canvas id="genderPieChart"></canvas>
                                </div>
                                <div class="col-12" style="height: 250px;">
                                    <canvas id="memberTypePieChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance Tab -->

        </div>
    </div>

    <style>
        /* Modern Theme Variables */
        :root {
            --primary: #940000;
            --success: #10b981;
            --info: #0ea5e9;
            --warning: #f59e0b;
            --danger: #ef4444;
            --dark: #1e293b;
            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(255, 255, 255, 0.3);
        }

        .text-primary {
            color: #940000 !important;
        }

        .bg-primary {
            background-color: #940000 !important;
        }

        .border-primary {
            border-color: #940000 !important;
        }

        body {
            background-color: #f8fafc;
        }

        /* Glassmorphism Cards */
        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .glass-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        /* Premium Header */
        .analytics-header {
            background: linear-gradient(135deg, #940000 0%, #b30000 100%);
            color: white;
        }

        .header-icon-glow {
            width: 48px;
            height: 48px;
            background: rgba(255, 255, 255, 0.2);
            box-shadow: 0 0 20px rgba(148, 0, 0, 0.4);
        }

        .header-bg-glow {
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 70%);
            pointer-events: none;
        }

        .filter-glass {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .btn-light-glass {
            background: rgba(255, 255, 255, 0.9);
            border: none;
            color: var(--primary);
            font-weight: 600;
        }

        .btn-light-glass:hover {
            background: white;
            color: #940000;
        }

        /* Custom Tabs */
        .custom-tabs .nav-link {
            background: white;
            color: var(--dark);
            font-weight: 600;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .custom-tabs .nav-link.active {
            background: var(--primary) !important;
            color: white !important;
            border-color: var(--primary);
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.3);
        }

        .custom-tabs .nav-link:hover:not(.active) {
            background: #f1f5f9;
        }

        /* Icons & Badges */
        .badge-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            font-size: 1.2rem;
        }

        .bg-soft-primary {
            background: #fee2e2;
            color: #940000;
        }

        .bg-soft-success {
            background: #ecfdf5;
            color: #10b981;
        }

        .bg-soft-info {
            background: #f0f9ff;
            color: #0ea5e9;
        }

        .bg-soft-warning {
            background: #fffbeb;
            color: #f59e0b;
        }

        /* Table Styling */
        .custom-table thead th {
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            color: #64748b;
            padding-bottom: 1.5rem;
        }

        .rank-circle {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.8rem;
        }

        .rank-1 {
            background: #fef3c7;
            color: #92400e;
        }

        .rank-2 {
            background: #f1f5f9;
            color: #475569;
        }

        .rank-3 {
            background: #ffedd5;
            color: #9a3412;
        }

        /* Scrollbar theme */
        .table-responsive::-webkit-scrollbar {
            height: 6px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
    </style>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const filterSelect = document.getElementById('filterType');
            const yearPicker = document.getElementById('yearPicker');
            const customPicker = document.getElementById('customPicker');

            filterSelect.addEventListener('change', function () {
                if (this.value === 'year') {
                    yearPicker.classList.remove('d-none');
                    customPicker.classList.add('d-none');
                } else {
                    yearPicker.classList.add('d-none');
                    customPicker.classList.remove('d-none');
                }
            });

            // Chart Defaults
            Chart.defaults.font.family = "'Inter', system-ui, -apple-system, sans-serif";
            Chart.defaults.color = "#64748b";

            // Overview Growth Chart
            const growthCtx = document.getElementById('overviewGrowthChart').getContext('2d');
            new Chart(growthCtx, {
                type: 'line',
                data: {
                    labels: @json(collect($financialData['monthly_trends'])->pluck('month')),
                    datasets: [{
                        label: 'Income',
                        data: @json(collect($financialData['monthly_trends'])->pluck('income')),
                        borderColor: '#940000',
                        backgroundColor: 'rgba(148, 0, 0, 0.1)',
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Members',
                        data: @json(collect($memberData['monthly_registrations'])->pluck('count')),
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'top' } },
                    scales: {
                        y: { grid: { borderDash: [2, 2] } },
                        x: { grid: { display: false } }
                    }
                }
            });

            // Income Doughnut
            const incomeCtx = document.getElementById('overviewIncomeDoughnut').getContext('2d');
            new Chart(incomeCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Tithes', 'Offerings'],
                    datasets: [{
                        data: [{{ $financialData['totals']['tithes'] }}, {{ $financialData['totals']['offerings'] }}],
                        backgroundColor: ['#940000', '#10b981', '#0ea5e9'],
                        borderWidth: 0,
                        cutout: '70%'
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } }
                }
            });

            // Detailed Financial Chart
            const financialCtx = document.getElementById('detailedFinancialChart').getContext('2d');
            new Chart(financialCtx, {
                type: 'bar',
                data: {
                    labels: @json(collect($financialData['monthly_trends'])->pluck('month')),
                    datasets: [
                        {
                            label: 'Tithes',
                            data: @json(collect($financialData['monthly_trends'])->pluck('tithes')),
                            backgroundColor: '#940000'
                        },
                        {
                            label: 'Offerings',
                            data: @json(collect($financialData['monthly_trends'])->pluck('offerings')),
                            backgroundColor: '#10b981'
                        },
                        {
                            label: 'Expenses',
                            data: @json(collect($financialData['monthly_trends'])->pluck('expenses')),
                            backgroundColor: '#ef4444'
                        }
                    ]
                },
                options: {
                    maintainAspectRatio: false,
                    scales: {
                        y: { stacked: false },
                        x: { stacked: false }
                    }
                }
            });

            // Age Distribution
            const ageCtx = document.getElementById('ageDistributionChart').getContext('2d');
            new Chart(ageCtx, {
                type: 'bar',
                data: {
                    labels: @json(collect($memberData['age_groups'])->keys()),
                    datasets: [{
                        label: 'Members',
                        data: @json(collect($memberData['age_groups'])->values()),
                        backgroundColor: ['#940000', '#b30000', '#d40000', '#f00000', '#ff4d4d', '#ff8080'],
                        borderRadius: 8
                    }]
                },
                options: {
                    indexAxis: 'y',
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } }
                }
            });

            // Gender Chart
            const genderCtx = document.getElementById('genderPieChart').getContext('2d');
            new Chart(genderCtx, {
                type: 'pie',
                data: {
                    labels: ['Male', 'Female'],
                    datasets: [{
                        data: [{{ $memberData['totals']['male'] }}, {{ $memberData['totals']['female'] }}],
                        backgroundColor: ['#940000', '#f472b6'],
                        borderWidth: 0
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        title: { display: true, text: 'Gender Distribution', position: 'top' },
                        legend: { position: 'bottom' }
                    }
                }
            });

            // Member types
            const memberTypeCtx = document.getElementById('memberTypePieChart').getContext('2d');
            new Chart(memberTypeCtx, {
                type: 'pie',
                data: {
                    labels: @json(collect($memberData['member_types'])->keys()),
                    datasets: [{
                        data: @json(collect($memberData['member_types'])->values()),
                        backgroundColor: ['#940000', '#10b981', '#0ea5e9', '#f59e0b'],
                        borderWidth: 0
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        title: { display: true, text: 'Membership Type', position: 'top' },
                        legend: { position: 'bottom' }
                    }
                }
            });

            // Attendance Trend


            // Yearly Revenue Chart
            const yearlyRevCtx = document.getElementById('yearlyRevenueChart').getContext('2d');
            new Chart(yearlyRevCtx, {
                type: 'bar',
                data: {
                    labels: @json(collect($financialData['yearly_trends'])->pluck('year')),
                    datasets: [{
                        label: 'Income',
                        data: @json(collect($financialData['yearly_trends'])->pluck('income')),
                        backgroundColor: '#10b981'
                    }]
                },
                options: {
                    maintainAspectRatio: false
                }
            });

            // Income vs Expense Comparison
            const comparisonCtx = document.getElementById('incomeExpenseComparison').getContext('2d');
            new Chart(comparisonCtx, {
                type: 'pie',
                data: {
                    labels: ['Total Income', 'Total Expenses'],
                    datasets: [{
                        data: [
                            {{ $financialData['totals']['tithes'] + $financialData['totals']['offerings'] }},
                            {{ $financialData['totals']['expenses'] }}
                        ],
                        backgroundColor: ['#10b981', '#ef4444'],
                        borderWidth: 0
                    }]
                },
                options: {
                    maintainAspectRatio: false
                }
            });
        });
    </script>
@endsection