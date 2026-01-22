@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="card border-0 shadow-sm mb-4" style="background: white;">
        <div class="card-body p-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <div class="d-flex align-items-center justify-content-center rounded-2 border border-primary border-2" style="width:48px;height:48px;background:rgba(0,123,255,.1);color:#007bff;">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div>
                    <h1 class="h5 mb-0 text-dark fw-semibold">
                        @if(isset($isChurchElder) && $isChurchElder && isset($selectedCommunity))
                            {{ $selectedCommunity->name }} Reports Overview
                        @else
                            All Reports Overview
                        @endif
                    </h1>
                    <p class="mb-0 small text-muted">
                        @if(isset($isChurchElder) && $isChurchElder && isset($selectedCommunity))
                            Key activities and contributions for {{ $selectedCommunity->name }}
                        @else
                            Key activities and contributions across the system
                        @endif
                    </p>
                </div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                @if(isset($isChurchElder) && $isChurchElder && isset($communities) && $communities->count() > 1)
                    <div class="dropdown">
                        <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" id="communityDropdown" data-bs-toggle="dropdown">
                            <i class="fas fa-home me-1"></i>{{ $selectedCommunity->name ?? 'Select Community' }}
                        </button>
                        <ul class="dropdown-menu">
                            @foreach($communities as $community)
                                <li>
                                    <a class="dropdown-item {{ isset($selectedCommunity) && $selectedCommunity->id == $community->id ? 'active' : '' }}" 
                                       href="{{ route('reports.overview', ['community_id' => $community->id, 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}">
                                        {{ $community->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <a href="{{ route('reports.index') }}" class="btn btn-primary btn-sm"><i class="fas fa-chart-pie me-1"></i>Financial Reports</a>
                @if(isset($isChurchElder) && $isChurchElder && isset($selectedCommunity))
                    <a href="{{ route('church-elder.reports', $selectedCommunity->id) }}" class="btn btn-success btn-sm"><i class="fas fa-chart-bar me-1"></i>Community Reports</a>
                @endif
            </div>
        </div>
    </div>

    <!-- KPI Row -->
    <div class="row mb-4 g-3">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-xs text-uppercase fw-bold text-primary">Total Members</div>
                        <div class="h5 mb-0 fw-bold">{{ number_format($totalMembers) }}</div>
                        <small class="text-muted">As of {{ $end->format('M d, Y') }}</small>
                    </div>
                    <i class="fas fa-users fa-2x text-primary opacity-75"></i>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-xs text-uppercase fw-bold text-success">New (30 days)</div>
                        <div class="h5 mb-0 fw-bold">{{ number_format($newMembers30d) }}</div>
                        <small class="text-muted">Recent registrations</small>
                    </div>
                    <i class="fas fa-user-plus fa-2x text-success opacity-75"></i>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-xs text-uppercase fw-bold text-info">Total Giving (YTD)</div>
                        <div class="h5 mb-0 fw-bold">TZS {{ number_format($totalGiving + (isset($totalCommunityOfferings) ? $totalCommunityOfferings : 0), 2) }}</div>
                        <small class="text-muted">
                            @if(isset($isChurchElder) && $isChurchElder && isset($totalCommunityOfferings))
                                Tithes + Offerings + Donations + Mid-Week
                            @else
                                Tithes + Offerings + Donations
                            @endif
                        </small>
                    </div>
                    <i class="fas fa-hand-holding-usd fa-2x text-info opacity-75"></i>
                </div>
            </div>
        </div>
        @if(isset($isChurchElder) && $isChurchElder && isset($totalCommunityOfferings))
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-xs text-uppercase fw-bold text-success">Mid-Week Offerings</div>
                        <div class="h5 mb-0 fw-bold">TZS {{ number_format($totalCommunityOfferings, 0) }}</div>
                        <small class="text-muted">{{ $communityOfferings->count() }} completed</small>
                    </div>
                    <i class="fas fa-calendar-week fa-2x text-success opacity-75"></i>
                </div>
            </div>
        </div>
        @endif
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-xs text-uppercase fw-bold text-warning">Transactions (YTD)</div>
                        <div class="h5 mb-0 fw-bold">{{ number_format($transactionsCount + (isset($communityOfferings) ? $communityOfferings->count() : 0)) }}</div>
                        <small class="text-muted">Approved only</small>
                    </div>
                    <i class="fas fa-receipt fa-2x text-warning opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <!-- Member Contributions -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header report-header-primary py-2 rounded-top position-relative">
                    <h6 class="mb-0 fw-semibold text-white position-relative"><i class="fas fa-star me-2"></i>Top Contributors ({{ $start->format('M d') }} - {{ $end->format('M d, Y') }})</h6>
                </div>
                <div class="card-body">
                    @if($topContributors->count())
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-sm align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Member</th>
                                    <th class="text-end">Total Giving (TZS)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topContributors as $i => $m)
                                <tr>
                                    <td class="text-muted">{{ $i + 1 }}</td>
                                    <td>{{ $m->full_name }}</td>
                                    <td class="text-end fw-semibold">{{ number_format($m->total_giving, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                        <p class="text-muted mb-0">No contributor data available for the selected period.</p>
                    @endif
                    <div class="mt-3 d-flex flex-wrap gap-2">
                        <a href="{{ route('reports.member-giving') }}" class="btn btn-primary btn-sm"><i class="fas fa-user me-1"></i><span class="d-none d-sm-inline">Member Giving Report</span><span class="d-sm-none">Member Giving</span></a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Finance breakdown: Offerings and Donations by Type -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header report-header-info py-2 rounded-top position-relative">
                    <h6 class="mb-0 fw-semibold text-white position-relative"><i class="fas fa-layer-group me-2"></i>Offering & Donation Types (Approved)</h6>
                </div>
                <div class="card-body">
                    <!-- Combined View: Shows types that exist in both offerings and donations -->
                    @if(isset($combinedByType) && count($combinedByType) > 0)
                    <div class="mb-4">
                        <h6 class="text-success mb-3"><i class="fas fa-chart-pie me-2"></i>Combined by Type (Showing Total, Offering, and Donation amounts separately)</h6>
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-sm align-middle table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Type</th>
                                        <th class="text-end">Total Amount</th>
                                        <th class="text-end">Offering</th>
                                        <th class="text-end">Donation</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($combinedByType as $combined)
                                    <tr>
                                        <td><strong>{{ ucfirst(str_replace('_',' ', $combined['type'])) }}</strong></td>
                                        <td class="text-end fw-bold text-success">{{ number_format($combined['total_amount'], 2) }}</td>
                                        <td class="text-end">
                                            @if($combined['offering_amount'] > 0)
                                                <span class="text-primary">{{ number_format($combined['offering_amount'], 2) }}</span>
                                                <small class="text-muted">({{ $combined['offering_count'] }})</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if($combined['donation_amount'] > 0)
                                                <span class="text-info">{{ number_format($combined['donation_amount'], 2) }}</span>
                                                <small class="text-muted">({{ $combined['donation_count'] }})</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <hr>
                    @endif
                    
                    <div class="row g-3">
                        <div class="col-12 mb-3">
                            <h6 class="text-primary mb-2"><i class="fas fa-gift me-2"></i>Offerings by Type</h6>
                            @if($offeringTypes->count())
                            <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                                <table class="table table-sm align-middle">
                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th class="text-end">Total (TZS)</th>
                                            <th class="text-end">Transactions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($offeringTypes as $t)
                                        <tr>
                                            <td>{{ ucfirst(str_replace('_',' ', $t->offering_type ?? 'general')) }}</td>
                                            <td class="text-end fw-semibold">{{ number_format($t->total_amount, 2) }}</td>
                                            <td class="text-end">{{ number_format($t->count) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                                <p class="text-muted mb-0">No approved offerings in this period.</p>
                            @endif
                        </div>
                        <div class="col-md-12">
                            <h6 class="text-info mb-2"><i class="fas fa-heart me-2"></i>Donations by Type</h6>
                            @if($donationTypes->count())
                            <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                                <table class="table table-sm align-middle">
                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th class="text-end">Total (TZS)</th>
                                            <th class="text-end">Transactions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($donationTypes as $d)
                                        <tr>
                                            <td>{{ ucfirst(str_replace('_',' ', $d->donation_type ?? 'general')) }}</td>
                                            <td class="text-end fw-semibold">{{ number_format($d->total_amount, 2) }}</td>
                                            <td class="text-end">{{ number_format($d->count) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                                <p class="text-muted mb-0">No approved donations in this period.</p>
                            @endif
                        </div>
                    </div>
                    <div class="mt-3 d-flex flex-wrap gap-2">
                        <a href="{{ route('reports.department-giving') }}" class="btn btn-success btn-sm"><i class="fas fa-layer-group me-1"></i><span class="d-none d-sm-inline">Department Giving</span><span class="d-sm-none">Dept Giving</span></a>
                        <a href="{{ route('reports.offering-fund-breakdown') }}" class="btn btn-warning btn-sm"><i class="fas fa-coins me-1"></i><span class="d-none d-sm-inline">Fund Breakdown</span><span class="d-sm-none">Funds</span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Quick Links to Other Reports -->
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header report-header-neutral py-2 rounded-top position-relative">
                    <h6 class="mb-0 fw-semibold text-white position-relative"><i class="fas fa-link me-2"></i>More Reports</h6>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-12 col-sm-6 col-md-3">
                            <a href="{{ route('reports.income-vs-expenditure') }}" class="btn btn-outline-primary w-100"><i class="fas fa-balance-scale me-1"></i><span class="d-none d-md-inline">Income vs Expenditure</span><span class="d-md-none">Income vs Expense</span></a>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <a href="{{ route('reports.budget-performance') }}" class="btn btn-outline-secondary w-100"><i class="fas fa-chart-bar me-1"></i><span class="d-none d-md-inline">Budget Performance</span><span class="d-md-none">Budget</span></a>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <a href="{{ route('reports.member-giving') }}" class="btn btn-outline-success w-100"><i class="fas fa-user me-1"></i><span class="d-none d-md-inline">Member Giving</span><span class="d-md-none">Member</span></a>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <a href="{{ route('reports.department-giving') }}" class="btn btn-outline-info w-100"><i class="fas fa-sitemap me-1"></i><span class="d-none d-md-inline">Department Giving</span><span class="d-md-none">Department</span></a>
                        </div>
                    </div>
                    <p class="small text-muted mt-3 mb-0">Period: {{ $start->format('M d, Y') }} - {{ $end->format('M d, Y') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
.report-header-primary{
    background: linear-gradient(135deg, #4e73df 0%, #6f42c1 100%) !important;
    color: #fff !important;
}
.report-header-primary::before{
    content: '';
    position: absolute; inset: 0;
    background: rgba(0,0,0,.08);
    border-top-left-radius: .5rem; border-top-right-radius: .5rem;
}

.report-header-info{
    background: linear-gradient(135deg, #36b9cc 0%, #2aa2b3 100%) !important;
    color: #fff !important;
}
.report-header-info::before{
    content: '';
    position: absolute; inset: 0;
    background: rgba(0,0,0,.06);
    border-top-left-radius: .5rem; border-top-right-radius: .5rem;
}

.report-header-neutral{
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%) !important;
    color: #fff !important;
}
.report-header-neutral::before{
    content: '';
    position: absolute; inset: 0;
    background: rgba(0,0,0,.05);
    border-top-left-radius: .5rem; border-top-right-radius: .5rem;
}

/* Mobile Responsive Styles */
@media (max-width: 768px) {
    .container-fluid {
        padding-left: 0.75rem !important;
        padding-right: 0.75rem !important;
    }
    
    /* Header card mobile */
    .card-body.p-3 {
        padding: 1rem !important;
    }
    
    .card-body.p-3 h1.h5 {
        font-size: 1rem !important;
    }
    
    .card-body.p-3 .small {
        font-size: 0.8rem !important;
    }
    
    .card-body.p-3 .btn-sm {
        font-size: 0.8rem !important;
        padding: 0.375rem 0.75rem !important;
    }
    
    /* Icon in header */
    .card-body.p-3 > div:first-child > div:first-child {
        width: 40px !important;
        height: 40px !important;
        font-size: 18px !important;
    }
    
    /* KPI Cards */
    .card-body .h5 {
        font-size: 1.25rem !important;
    }
    
    .card-body .text-xs {
        font-size: 0.7rem !important;
    }
    
    .card-body small {
        font-size: 0.75rem !important;
    }
    
    .fa-2x {
        font-size: 1.5em !important;
    }
    
    /* Card headers */
    .card-header {
        padding: 0.75rem !important;
    }
    
    .card-header h6 {
        font-size: 0.9rem !important;
    }
    
    /* Tables */
    .table-responsive {
        font-size: 0.875rem;
        -webkit-overflow-scrolling: touch;
        overflow-x: auto;
        overflow-y: auto;
    }
    
    .table th,
    .table td {
        padding: 0.5rem 0.5rem;
        font-size: 0.875rem;
        min-width: 80px;
    }
    
    .table th {
        font-size: 0.8rem;
        white-space: nowrap;
        position: sticky;
        top: 0;
        background-color: #f8f9fa;
        z-index: 10;
    }
    
    /* Buttons */
    .btn-sm {
        font-size: 0.8rem !important;
        padding: 0.375rem 0.75rem !important;
    }
    
    /* Section headers in cards */
    h6.text-success,
    h6.text-primary,
    h6.text-info {
        font-size: 0.85rem !important;
    }
}

@media (max-width: 576px) {
    .container-fluid {
        padding-left: 0.5rem !important;
        padding-right: 0.5rem !important;
    }
    
    /* Header card extra small */
    .card-body.p-3 {
        padding: 0.75rem !important;
        flex-direction: column !important;
        align-items: flex-start !important;
    }
    
    .card-body.p-3 > div:last-child {
        width: 100%;
        margin-top: 0.75rem;
    }
    
    .card-body.p-3 > div:last-child .btn {
        width: 100%;
    }
    
    .card-body.p-3 h1.h5 {
        font-size: 0.95rem !important;
    }
    
    .card-body.p-3 .small {
        font-size: 0.75rem !important;
    }
    
    /* Icon in header */
    .card-body.p-3 > div:first-child > div:first-child {
        width: 36px !important;
        height: 36px !important;
        font-size: 16px !important;
    }
    
    /* KPI Cards */
    .card-body {
        padding: 0.875rem !important;
    }
    
    .card-body .h5 {
        font-size: 1.1rem !important;
    }
    
    .card-body .text-xs {
        font-size: 0.65rem !important;
    }
    
    .card-body small {
        font-size: 0.7rem !important;
    }
    
    .fa-2x {
        font-size: 1.25em !important;
    }
    
    /* Card headers */
    .card-header {
        padding: 0.625rem !important;
    }
    
    .card-header h6 {
        font-size: 0.85rem !important;
    }
    
    .card-header i {
        font-size: 0.9rem !important;
    }
    
    /* Tables */
    .table-responsive {
        font-size: 0.75rem;
        max-height: 300px;
        overflow-x: auto;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    .table {
        font-size: 0.75rem;
    }
    
    .table th,
    .table td {
        padding: 0.5rem 0.375rem;
        font-size: 0.75rem;
        min-width: 70px;
    }
    
    .table th {
        font-size: 0.7rem;
        white-space: nowrap;
        position: sticky;
        top: 0;
        background-color: #f8f9fa;
        z-index: 10;
    }
    
    .table td {
        word-break: break-word;
    }
    
    /* Buttons */
    .btn-sm {
        font-size: 0.75rem !important;
        padding: 0.35rem 0.65rem !important;
    }
    
    .btn {
        font-size: 0.8rem !important;
    }
    
    /* Section headers in cards */
    h6.text-success,
    h6.text-primary,
    h6.text-info {
        font-size: 0.8rem !important;
    }
    
    /* Button groups */
    .d-flex.gap-2 {
        flex-direction: column;
    }
    
    .d-flex.gap-2 .btn {
        width: 100%;
    }
    
    /* More Reports buttons */
    .row.g-2 .btn {
        font-size: 0.75rem !important;
        padding: 0.5rem 0.75rem !important;
    }
}
</style>
@endsection
