@extends('layouts.index')

@section('title', 'Reports Overview')

@section('content')
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #1e3799 0%, #0c2461 100%);
        --success-gradient: linear-gradient(135deg, #10ac84 0%, #0b8e6b 100%);
        --info-gradient: linear-gradient(135deg, #0fbcf9 0%, #0984e3 100%);
        --warning-gradient: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
        --glass-bg: rgba(255, 255, 255, 0.9);
        --glass-border: rgba(255, 255, 255, 0.4);
    }

    .glass-card {
        background: var(--glass-bg);
        backdrop-filter: blur(10px);
        border: 1px solid var(--glass-border);
        border-radius: 16px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
    }

    .glass-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.12);
    }

    .kpi-card {
        color: white;
        overflow: hidden;
        position: relative;
    }

    .kpi-card::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 150px;
        height: 150px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        z-index: 0;
    }

    .bg-umoja { background: linear-gradient(135deg, #6c5ce7 0%, #a29bfe 100%); }
    .bg-jengo { background: linear-gradient(135deg, #fd9644 0%, #feb47b 100%); }

    .action-grid .btn {
        height: 100px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 10px;
        border-radius: 12px;
        font-weight: 600;
        border: none;
        transition: transform 0.2s;
    }

    .action-grid .btn:hover {
        transform: scale(1.05);
    }

    .avatar-sm {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-weight: bold;
        font-size: 0.8rem;
    }

    .dotted-spacer {
        border-bottom: 1px dotted #dee2e6;
        flex-grow: 1;
        margin: 0 10px;
        margin-bottom: 5px;
    }

    .table-footer-total {
        background-color: #f8f9fa;
        font-weight: 700;
    }
</style>

<div class="container-fluid px-4 py-4">
    <!-- Header Section -->
    <div class="glass-card p-4 mb-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle bg-primary bg-opacity-10 p-3 text-primary">
                <i class="fas fa-chart-line fa-2x"></i>
            </div>
            <div>
                <h2 class="h4 fw-bold mb-1">
                    @if(isset($isChurchElder) && $isChurchElder && isset($selectedCommunity))
                        {{ $selectedCommunity->name }} Reports
                    @else
                        Executive Reports Overview
                    @endif
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item active">Overview</li>
                    </ol>
                </nav>
            </div>
        </div>
        
        <div class="d-flex gap-2 align-items-center">
            @if(isset($isChurchElder) && $isChurchElder && isset($communities) && $communities->count() > 1)
                <select onchange="window.location.href=this.value" class="form-select border-0 shadow-sm rounded-pill px-4">
                    @foreach($communities as $community)
                        <option value="{{ route('reports.overview', ['community_id' => $community->id]) }}" 
                            {{ isset($selectedCommunity) && $selectedCommunity->id == $community->id ? 'selected' : '' }}>
                            {{ $community->name }}
                        </option>
                    @endforeach
                </select>
            @endif
            <button class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="fas fa-filter me-2"></i>Filter Date
            </button>
        </div>
    </div>

    <!-- KPI Section -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-md-6 col-xl-4">
            <div class="glass-card kpi-card bg-success p-4 h-100">
                <div class="position-relative z-1">
                    <div class="text-white text-opacity-75 small text-uppercase fw-bold mb-1">Total Focused Giving</div>
                    <div class="h2 fw-bold mb-0 text-white">TZS {{ number_format($totalGiving, 0) }}</div>
                    <div class="mt-2 small text-white text-opacity-75 text-truncate"> Umoja + Jengo + Pledges </div>
                </div>
                <i class="fas fa-hand-holding-usd fa-3x position-absolute end-0 bottom-0 mb-3 me-3 text-white opacity-25"></i>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-4">
            <div class="glass-card kpi-card bg-info p-4 h-100">
                <div class="position-relative z-1">
                    <div class="text-white text-opacity-75 small text-uppercase fw-bold mb-1">Target Transactions</div>
                    <div class="h2 fw-bold mb-0 text-white">{{ number_format($transactionsCount) }}</div>
                    <div class="mt-2 badge bg-white bg-opacity-20 rounded-pill">
                        <i class="fas fa-check-circle me-1"></i> All Approved & Completed
                    </div>
                </div>
                <i class="fas fa-receipt fa-3x position-absolute end-0 bottom-0 mb-3 me-3 text-white opacity-25"></i>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-4">
            <div class="glass-card kpi-card bg-primary p-4 h-100">
                <div class="position-relative z-1">
                    <div class="text-white text-opacity-75 small text-uppercase fw-bold mb-1">Active Members</div>
                    <div class="h2 fw-bold mb-0 text-white">{{ number_format($totalMembers) }}</div>
                    <div class="mt-2 badge bg-white bg-opacity-20 rounded-pill">
                        <i class="fas fa-user-plus me-1"></i> +{{ $newMembers30d }} new (30d)
                    </div>
                </div>
                <i class="fas fa-users fa-3x position-absolute end-0 bottom-0 mb-3 me-3 text-white opacity-25"></i>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- New Special Offerings Section -->
        <div class="col-12">
            <div class="glass-card p-4">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h5 class="fw-bold mb-0"><i class="fas fa-star text-warning me-2"></i>Special Offering Summary</h5>
                    <a href="{{ route('reports.special-offerings') }}" class="btn btn-sm btn-outline-primary rounded-pill">View Breakdowns</a>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 rounded-4 bg-umoja text-white shadow-sm d-flex align-items-center gap-3">
                            <div class="bg-white bg-opacity-25 rounded-circle p-3">
                                <i class="fas fa-users fa-lg"></i>
                            </div>
                            <div>
                                <div class="small text-uppercase fw-bold opacity-75">Sadaka ya Umoja</div>
                                <div class="h4 fw-bold mb-0">TZS {{ number_format($specialOfferingsSummary['umoja'], 0) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded-4 bg-jengo text-white shadow-sm d-flex align-items-center gap-3">
                            <div class="bg-white bg-opacity-25 rounded-circle p-3">
                                <i class="fas fa-building fa-lg"></i>
                            </div>
                            <div>
                                <div class="small text-uppercase fw-bold opacity-75">Sadaka ya Jengo</div>
                                <div class="h4 fw-bold mb-0">TZS {{ number_format($specialOfferingsSummary['jengo'], 0) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts/Tables Row -->
        <div class="col-12 col-lg-7">
            <div class="glass-card h-100 overflow-hidden">
                <div class="bg-primary p-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="mb-0 fw-bold text-white"><i class="fas fa-trophy me-2"></i>Top Contributors (YTD)</h6>
                    <span class="badge bg-white text-primary rounded-pill">{{ $topContributors->count() }} Members</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Rank</th>
                                    <th>Member Name</th>
                                    <th class="text-end pe-4">Total Giving (TZS)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topContributors as $index => $contributor)
                                <tr>
                                    <td class="ps-4">
                                        @if($index == 0) <span class="badge bg-warning text-dark"><i class="fas fa-medal"></i></span>
                                        @elseif($index == 1) <span class="badge bg-secondary text-white">2</span>
                                        @elseif($index == 2) <span class="badge bg-bronze text-white" style="background:#cd7f32">3</span>
                                        @else <span class="text-muted">{{ $index + 1 }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-sm bg-primary bg-opacity-10 text-primary">
                                                {{ strtoupper(substr($contributor->full_name, 0, 1)) }}
                                            </div>
                                            <span class="fw-semibold">{{ $contributor->full_name }}</span>
                                        </div>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="fw-bold text-success">{{ number_format($contributor->total_giving, 0) }}</div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            @if($topContributors->count() > 0)
                            <tfoot class="table-footer-total">
                                <tr>
                                    <td colspan="2" class="ps-4">TOTAL (Top Contributors)</td>
                                    <td class="text-end pe-4 text-primary">
                                        {{ number_format($topContributors->sum('total_giving'), 0) }}
                                    </td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-5">
            <div class="glass-card h-100 overflow-hidden">
                <div class="bg-info bg-opacity-10 p-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-info"><i class="fas fa-th-list me-2"></i>Focused Giving Breakdown</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex align-items-center">
                            <span class="fw-bold text-muted small" style="width: 120px;">Sadaka ya Umoja</span>
                            <div class="dotted-spacer"></div>
                            <span class="fw-bold text-dark">TZS {{ number_format($specialOfferingsSummary['umoja'], 0) }}</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="fw-bold text-muted small" style="width: 120px;">Sadaka ya Jengo</span>
                            <div class="dotted-spacer"></div>
                            <span class="fw-bold text-dark">TZS {{ number_format($specialOfferingsSummary['jengo'], 0) }}</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="fw-bold text-muted small" style="width: 120px;">Ahadi ya Bwana</span>
                            <div class="dotted-spacer"></div>
                            <span class="fw-bold text-primary">TZS {{ number_format($totalPledgesPaid, 0) }}</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="fw-bold text-muted small" style="width: 120px;">Zaka (Tithes)</span>
                            <div class="dotted-spacer"></div>
                            <span class="fw-bold text-dark">TZS {{ number_format($totalTithes, 0) }}</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="fw-bold text-muted small" style="width: 120px;">Mengineyo</span>
                            <div class="dotted-spacer"></div>
                            <span class="fw-bold text-dark">TZS {{ number_format($specialOfferingsSummary['other'], 0) }}</span>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-top">
                        <div class="d-flex align-items-center mb-3">
                            <span class="fw-bold text-dark" style="width: 120px;">GRAND TOTAL</span>
                            <div class="dotted-spacer"></div>
                            <span class="h5 fw-bold text-success mb-0">TZS {{ number_format($totalGiving, 0) }}</span>
                        </div>
                        <div class="alert alert-info border-0 bg-info bg-opacity-10 py-2 small mb-0">
                            <i class="fas fa-info-circle me-1"></i> These figures represent all approved payments tracked in the selected period.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Grid -->
    <div class="glass-card p-4">
        <h5 class="fw-bold mb-4"><i class="fas fa-bolt text-warning me-2"></i>Quick Access Reports</h5>
        <div class="row g-3 action-grid">
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ route('reports.member-giving') }}" class="btn w-100" style="background-color: #ebf5ff !important; color: #1e3799 !important;">
                    <i class="fas fa-users fa-lg"></i>
                    <span>Member Giving</span>
                </a>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ route('reports.department-giving') }}" class="btn bg-success bg-opacity-10 text-success w-100">
                    <i class="fas fa-building fa-lg"></i>
                    <span>Dept Giving</span>
                </a>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ route('reports.income-vs-expenditure') }}" class="btn bg-warning bg-opacity-10 text-warning w-100">
                    <i class="fas fa-balance-scale fa-lg"></i>
                    <span>In vs Out</span>
                </a>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ route('reports.budget-performance') }}" class="btn bg-info bg-opacity-10 text-info w-100">
                    <i class="fas fa-wallet fa-lg"></i>
                    <span>Budget Perf.</span>
                </a>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ route('reports.special-offerings') }}" class="btn bg-indigo bg-opacity-10 text-indigo w-100" style="background-color: rgba(108, 92, 231, 0.1); color: #6c5ce7;">
                    <i class="fas fa-file-invoice-dollar fa-lg"></i>
                    <span>Special Card</span>
                </a>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ route('reports.offering-fund-breakdown') }}" class="btn bg-dark bg-opacity-10 text-dark w-100">
                    <i class="fas fa-layer-group fa-lg"></i>
                    <span>Fund Analysis</span>
                </a>
            </div>
            @if(auth()->user()->isSecretary() || auth()->user()->isPastor() || auth()->user()->isAdmin())
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ route('reports.general-secretary') }}" class="btn bg-danger bg-opacity-10 text-danger w-100">
                    <i class="fas fa-file-invoice-dollar fa-lg"></i>
                    <span>Gen Sec Report</span>
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Filter Overview Period</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('reports.overview') }}" method="GET">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Start Date</label>
                            <input type="date" name="start_date" class="form-control" value="{{ $start->format('Y-m-d') }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">End Date</label>
                            <input type="date" name="end_date" class="form-control" value="{{ $end->format('Y-m-d') }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Apply Filter</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
