@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mt-4"><i class="fas fa-chart-line me-2"></i>{{ autoTranslate('Financial Dashboard') }}</h1>
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#quickAddModal">
                <i class="fas fa-plus me-1"></i>{{ autoTranslate('Quick Add') }}
            </button>
            <a href="{{ route('reports.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-chart-bar me-1"></i>{{ autoTranslate('Reports') }}
            </a>
        </div>
    </div>

    <!-- Financial Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">{{ autoTranslate('Total Income (This Month)') }}</div>
                            <div class="h4">TZS {{ number_format($totalIncome, 0) }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-arrow-up fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white text-decoration-none" href="{{ route('finance.tithes') }}">
                        {{ autoTranslate('View Details') }}
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
                            <div class="small text-white-50">{{ autoTranslate('Net Income (This Month)') }}</div>
                            <div class="h4">TZS {{ number_format($netIncome, 0) }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white text-decoration-none" href="{{ route('reports.income-vs-expenditure') }}">
                        {{ autoTranslate('View Report') }}
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
                            <div class="small text-white-50">{{ autoTranslate('Expenses (This Year)') }}</div>
                            <div class="h4">TZS {{ number_format($monthlyExpenses, 0) }}</div>
                            @if(isset($currentMonthExpenses))
                                <div class="small text-white-50 mt-1">{{ autoTranslate('This Month') }}: TZS {{ number_format($currentMonthExpenses, 0) }}</div>
                            @endif
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-arrow-down fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white text-decoration-none" href="{{ route('finance.expenses') }}">
                        {{ autoTranslate('View Details') }}
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
                            <div class="small text-white-50">{{ autoTranslate('Active Pledges') }}</div>
                            <div class="h4">{{ $activePledges->count() }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-handshake fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white text-decoration-none" href="{{ route('finance.pledges') }}">
                        {{ autoTranslate('View Details') }}
                    </a>
                    <div class="small text-white-50">
                        <i class="fas fa-angle-right"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Income Breakdown -->
    <div class="row mb-4">
        <div class="col-xl-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-chart-pie me-1"></i>
                    <strong>{{ autoTranslate('Income Breakdown (This Month)') }}</strong>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="h5 text-primary">TZS {{ number_format($monthlyTithes, 0) }}</div>
                                <div class="small text-muted">{{ autoTranslate('Tithes') }}</div>
                                <div class="progress mt-2" style="height: 8px;">
                                    <div class="progress-bar bg-primary" style="width: {{ $totalIncome > 0 ? ($monthlyTithes / $totalIncome) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="h5 text-success">TZS {{ number_format($monthlyOfferings, 0) }}</div>
                                <div class="small text-muted">{{ autoTranslate('Offerings') }}</div>
                                <div class="progress mt-2" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: {{ $totalIncome > 0 ? ($monthlyOfferings / $totalIncome) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="h5 text-info">TZS {{ number_format($monthlyDonations, 0) }}</div>
                                <div class="small text-muted">{{ autoTranslate('Donations') }}</div>
                                <div class="progress mt-2" style="height: 8px;">
                                    <div class="progress-bar bg-info" style="width: {{ $totalIncome > 0 ? ($monthlyDonations / $totalIncome) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="h5 text-warning">TZS {{ number_format($monthlyPledgePayments, 0) }}</div>
                                <div class="small text-muted">{{ autoTranslate('Pledge Payments') }}</div>
                                <div class="progress mt-2" style="height: 8px;">
                                    <div class="progress-bar bg-warning" style="width: {{ $totalIncome > 0 ? ($monthlyPledgePayments / $totalIncome) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-chart-line me-1"></i>
                    <strong>{{ autoTranslate('Income Trend (Last 6 Months)') }}</strong>
                </div>
                <div class="card-body">
                    <canvas id="incomeTrendChart" width="100%" height="50"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="row mb-4">
        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-coins me-1"></i>
                    <strong>{{ autoTranslate('Recent Tithes') }}</strong>
                </div>
                <div class="card-body">
                    @if($recentTithes->count() > 0)
                        @foreach($recentTithes as $tithe)
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="fas fa-coins text-white"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="fw-bold">
                                        @if($tithe->campus)
                                            {{ $tithe->campus->name }}
                                        @elseif($tithe->evangelismLeader && $tithe->evangelismLeader->getCampus())
                                            {{ $tithe->evangelismLeader->getCampus()->name }}
                                        @else
                                            {{ $tithe->member->full_name ?? 'Unknown' }}
                                        @endif
                                    </div>
                                    <div class="small text-muted">{{ $tithe->tithe_date->format('M d, Y') }}</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold text-primary">TZS {{ number_format($tithe->amount, 0) }}</div>
                                    <div class="small text-muted">{{ ucfirst($tithe->payment_method) }}</div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-coins fa-2x mb-2"></i>
                            <div>{{ autoTranslate('No recent tithes') }}</div>
                        </div>
                    @endif
                </div>
                <div class="card-footer">
                    <a href="{{ route('finance.tithes') }}" class="btn btn-primary btn-sm">{{ autoTranslate('View All Tithes') }}</a>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-gift me-1"></i>
                    <strong>{{ autoTranslate('Recent Offerings') }}</strong>
                </div>
                <div class="card-body">
                    @if($recentOfferings->count() > 0)
                        @foreach($recentOfferings as $offering)
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0">
                                    <div class="bg-success rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="fas fa-gift text-white"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="fw-bold">{{ $offering->member->full_name ?? 'General Member' }}</div>
                                    <div class="small text-muted">{{ $offering->offering_date->format('M d, Y') }}</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold text-success">TZS {{ number_format($offering->amount, 0) }}</div>
                                    <div class="small text-muted">{{ ucfirst($offering->offering_type) }}</div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-gift fa-2x mb-2"></i>
                            <div>{{ autoTranslate('No recent offerings') }}</div>
                        </div>
                    @endif
                </div>
                <div class="card-footer">
                    <a href="{{ route('finance.offerings') }}" class="btn btn-success btn-sm">{{ autoTranslate('View All Offerings') }}</a>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-heart me-1"></i>
                    <strong>{{ autoTranslate('Recent Donations') }}</strong>
                </div>
                <div class="card-body">
                    @if($recentDonations->count() > 0)
                        @foreach($recentDonations as $donation)
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0">
                                    <div class="bg-info rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="fas fa-heart text-white"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="fw-bold">{{ $donation->member->full_name ?? $donation->donor_name ?? 'Anonymous' }}</div>
                                    <div class="small text-muted">{{ $donation->donation_date->format('M d, Y') }}</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold text-info">TZS {{ number_format($donation->amount, 0) }}</div>
                                    <div class="small text-muted">{{ ucfirst($donation->donation_type) }}</div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-heart fa-2x mb-2"></i>
                            <div>{{ autoTranslate('No recent donations') }}</div>
                        </div>
                    @endif
                </div>
                <div class="card-footer">
                    <a href="{{ route('finance.donations') }}" class="btn btn-info btn-sm">{{ autoTranslate('View All Donations') }}</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Budget Status and Pledges -->
    <div class="row mb-4">
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-wallet me-1"></i>
                    <strong>{{ autoTranslate('Current Budgets') }}</strong>
                </div>
                <div class="card-body">
                    @if($currentBudgets->count() > 0)
                        @foreach($currentBudgets as $budget)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <div class="fw-bold">{{ $budget->budget_name }}</div>
                                    <div class="text-muted">{{ $budget->fiscal_year }}</div>
                                </div>
                                <div class="progress mb-2" style="height: 8px;">
                                    <div class="progress-bar {{ $budget->is_over_budget ? 'bg-danger' : ($budget->is_near_limit ? 'bg-warning' : 'bg-success') }}" 
                                         style="width: {{ $budget->utilization_percentage }}%"></div>
                                </div>
                                <div class="d-flex justify-content-between small text-muted">
                                    <span>TZS {{ number_format($budget->spent_amount, 0) }} / {{ number_format($budget->total_budget, 0) }}</span>
                                    <span>{{ $budget->utilization_percentage }}% used</span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-wallet fa-2x mb-2"></i>
                            <div>{{ autoTranslate('No active budgets') }}</div>
                        </div>
                    @endif
                </div>
                <div class="card-footer">
                    <a href="{{ route('finance.budgets') }}" class="btn btn-primary btn-sm">{{ autoTranslate('Manage Budgets') }}</a>
                </div>
            </div>
        </div>
        
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-handshake me-1"></i>
                    <strong>{{ autoTranslate('Pledge Status') }}</strong>
                </div>
                <div class="card-body">
                    @if($activePledges->count() > 0)
                        @foreach($activePledges->take(3) as $pledge)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <div class="fw-bold">{{ $pledge->member->full_name ?? 'Unknown' }}</div>
                                    <div class="text-muted">{{ $pledge->pledge_type }}</div>
                                </div>
                                <div class="progress mb-2" style="height: 8px;">
                                    <div class="progress-bar bg-primary" style="width: {{ $pledge->progress_percentage }}%"></div>
                                </div>
                                <div class="d-flex justify-content-between small text-muted">
                                    <span>TZS {{ number_format($pledge->amount_paid, 0) }} / {{ number_format($pledge->pledge_amount, 0) }}</span>
                                    <span>{{ $pledge->progress_percentage }}% complete</span>
                                </div>
                            </div>
                        @endforeach
                        @if($activePledges->count() > 3)
                            <div class="text-center">
                                <small class="text-muted">And {{ $activePledges->count() - 3 }} more pledges...</small>
                            </div>
                        @endif
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-handshake fa-2x mb-2"></i>
                            <div>{{ autoTranslate('No active pledges') }}</div>
                        </div>
                    @endif
                </div>
                <div class="card-footer">
                    <a href="{{ route('finance.pledges') }}" class="btn btn-primary btn-sm">{{ autoTranslate('Manage Pledges') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Add Modal -->
<div class="modal fade" id="quickAddModal" tabindex="-1" aria-labelledby="quickAddModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quickAddModalLabel">{{ autoTranslate('Quick Add Transaction') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-coins fa-3x text-primary mb-3"></i>
                                <h5 class="card-title">{{ autoTranslate('Record Tithe') }}</h5>
                                <p class="card-text">{{ autoTranslate("Record a member's tithe payment") }}</p>
                                <a href="{{ route('finance.tithes') }}" class="btn btn-primary">{{ autoTranslate('Add Tithe') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-gift fa-3x text-success mb-3"></i>
                                <h5 class="card-title">{{ autoTranslate('Record Offering') }}</h5>
                                <p class="card-text">{{ autoTranslate('Record an offering or special gift') }}</p>
                                <a href="{{ route('finance.offerings') }}" class="btn btn-success">{{ autoTranslate('Add Offering') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-heart fa-3x text-info mb-3"></i>
                                <h5 class="card-title">{{ autoTranslate('Record Donation') }}</h5>
                                <p class="card-text">{{ autoTranslate('Record a donation or contribution') }}</p>
                                <a href="{{ route('finance.donations') }}" class="btn btn-info">{{ autoTranslate('Add Donation') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-handshake fa-3x text-warning mb-3"></i>
                                <h5 class="card-title">{{ autoTranslate('Record Pledge') }}</h5>
                                <p class="card-text">{{ autoTranslate("Record a member's pledge commitment") }}</p>
                                <a href="{{ route('finance.pledges') }}" class="btn btn-warning">{{ autoTranslate('Add Pledge') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js for Income Trend -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Income Trend Chart
    const ctx = document.getElementById('incomeTrendChart').getContext('2d');
    const incomeTrendData = @json($incomeTrend);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: incomeTrendData.map(item => item.month),
            datasets: [{
                label: '{{ autoTranslate('Monthly Income') }}',
                data: incomeTrendData.map(item => item.income),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'TZS ' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
});
</script>
@endsection



