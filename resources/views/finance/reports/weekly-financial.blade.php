@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mt-4"><i class="fas fa-calendar-week me-2"></i>Weekly Financial Report</h1>
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-success" onclick="window.print()">
                <i class="fas fa-print me-1"></i>Print Report
            </button>
            <button type="button" class="btn btn-danger" onclick="exportReport('pdf')">
                <i class="fas fa-file-pdf me-1"></i>Export PDF
            </button>
        </div>
    </div>

    <!-- Week Selector -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-filter me-1"></i><strong>Select Week</strong>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reports.weekly-financial') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="week_start" class="form-label">Week Starting</label>
                    <input type="date" class="form-control" id="week_start" name="week_start" 
                           value="{{ $startDate->format('Y-m-d') }}" required>
                    <small class="text-muted">Select any date in the week (week starts on Monday)</small>
                </div>
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Generate Report</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Report Content -->
    <div class="report-content">
        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6">
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
            
            <div class="col-xl-3 col-md-6">
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
            
            <div class="col-xl-3 col-md-6">
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
            
            <div class="col-xl-3 col-md-6">
                <div class="card bg-info text-white mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="small text-white-50">Week Period</div>
                                <div class="h6">{{ $startDate->format('M d') }} - {{ $endDate->format('M d, Y') }}</div>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-calendar-week fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Income Breakdown -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <strong><i class="fas fa-arrow-up me-2"></i>Income Sources</strong>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Source</th>
                                    <th class="text-end">Amount (TZS)</th>
                                    <th class="text-end">Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><i class="fas fa-coins me-2 text-success"></i>Tithes</td>
                                    <td class="text-end"><strong>{{ number_format($totalTithes, 0) }}</strong></td>
                                    <td class="text-end">{{ $tithesCount }}</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-gift me-2 text-primary"></i>Offerings</td>
                                    <td class="text-end"><strong>{{ number_format($totalOfferings, 0) }}</strong></td>
                                    <td class="text-end">{{ $offeringsCount }}</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-heart me-2 text-info"></i>Donations</td>
                                    <td class="text-end"><strong>{{ number_format($totalDonations, 0) }}</strong></td>
                                    <td class="text-end">{{ $donationsCount }}</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-handshake me-2 text-warning"></i>Pledge Payments</td>
                                    <td class="text-end"><strong>{{ number_format($totalPledgePayments, 0) }}</strong></td>
                                    <td class="text-end">{{ $pledgePaymentsCount }}</td>
                                </tr>
                                <tr class="table-light">
                                    <td><strong>TOTAL INCOME</strong></td>
                                    <td class="text-end"><strong style="font-size: 1.1rem;">{{ number_format($totalIncome, 0) }}</strong></td>
                                    <td class="text-end"><strong>{{ $tithesCount + $offeringsCount + $donationsCount + $pledgePaymentsCount }}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <strong><i class="fas fa-arrow-down me-2"></i>Expenses by Category</strong>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th class="text-end">Amount (TZS)</th>
                                    <th class="text-end">Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($expensesByCategory as $category => $data)
                                <tr>
                                    <td>{{ ucfirst(str_replace('_', ' ', $category)) }}</td>
                                    <td class="text-end"><strong>{{ number_format($data['total'], 0) }}</strong></td>
                                    <td class="text-end">{{ $data['count'] }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No expenses in this week</td>
                                </tr>
                                @endforelse
                                <tr class="table-light">
                                    <td><strong>TOTAL EXPENSES</strong></td>
                                    <td class="text-end"><strong style="font-size: 1.1rem;">{{ number_format($totalExpenses, 0) }}</strong></td>
                                    <td class="text-end"><strong>{{ $expensesCount }}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Offerings by Type -->
        @if($offeringsByType->count() > 0)
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <strong><i class="fas fa-gift me-2"></i>Offerings by Type</strong>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Offering Type</th>
                                <th class="text-end">Amount (TZS)</th>
                                <th class="text-end">Count</th>
                                <th class="text-end">Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($offeringsByType as $type => $data)
                            <tr>
                                <td>{{ ucfirst(str_replace('_', ' ', $type)) }}</td>
                                <td class="text-end"><strong>{{ number_format($data['total'], 0) }}</strong></td>
                                <td class="text-end">{{ $data['count'] }}</td>
                                <td class="text-end">{{ $totalOfferings > 0 ? number_format(($data['total'] / $totalOfferings) * 100, 1) : 0 }}%</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Donations by Type -->
        @if($donationsByType->count() > 0)
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <strong><i class="fas fa-heart me-2"></i>Donations by Type</strong>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Donation Type</th>
                                <th class="text-end">Amount (TZS)</th>
                                <th class="text-end">Count</th>
                                <th class="text-end">Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($donationsByType as $type => $data)
                            <tr>
                                <td>{{ ucfirst(str_replace('_', ' ', $type)) }}</td>
                                <td class="text-end"><strong>{{ number_format($data['total'], 0) }}</strong></td>
                                <td class="text-end">{{ $data['count'] }}</td>
                                <td class="text-end">{{ $totalDonations > 0 ? number_format(($data['total'] / $totalDonations) * 100, 1) : 0 }}%</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Daily Breakdown -->
        @if(count($dailyData) > 0)
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">
                <strong><i class="fas fa-calendar-day me-2"></i>Daily Breakdown</strong>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th class="text-end">Income (TZS)</th>
                                <th class="text-end">Expenses (TZS)</th>
                                <th class="text-end">Net (TZS)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dailyData as $day)
                            <tr>
                                <td>{{ $day['date'] }} ({{ $day['day'] }})</td>
                                <td class="text-end text-success"><strong>{{ number_format($day['income'], 0) }}</strong></td>
                                <td class="text-end text-danger"><strong>{{ number_format($day['expenses'], 0) }}</strong></td>
                                <td class="text-end {{ $day['net'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    <strong>{{ number_format($day['net'], 0) }}</strong>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Top Contributors -->
        @if($topContributors->count() > 0)
        <div class="card mb-4">
            <div class="card-header bg-warning text-dark">
                <strong><i class="fas fa-users me-2"></i>Top Contributors (Top 20)</strong>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Member ID</th>
                                <th>Name</th>
                                <th class="text-end">Total Giving (TZS)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topContributors as $index => $contributor)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $contributor->member_id }}</td>
                                <td>{{ $contributor->full_name }}</td>
                                <td class="text-end"><strong>{{ number_format($contributor->total_giving, 0) }}</strong></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
function exportReport(format) {
    const weekStart = document.getElementById('week_start')?.value || '{{ $startDate->format('Y-m-d') }}';
    if (weekStart) {
        const startDate = weekStart;
        const endDate = '{{ $endDate->format('Y-m-d') }}';
        const baseUrl = '{{ url("/") }}';
        const url = `${baseUrl}/reports/export/${format}?report_type=weekly-financial&start_date=${startDate}&end_date=${endDate}`;
        
        // Force download - server will send Content-Disposition header
        window.location.href = url;
    }
}
</script>

<style>
@media print {
    .btn-group, .card-header.bg-primary:first-of-type {
        display: none !important;
    }
    
    .card {
        page-break-inside: avoid;
        margin-bottom: 15px;
    }
    
    .table {
        font-size: 0.85rem;
    }
    
    @page {
        size: A4;
        margin: 1.5cm;
    }
}
</style>
@endsection











