@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0"><i class="fas fa-chart-line me-2 text-primary"></i>Community Finance</h1>
                            <p class="text-muted mb-0">{{ $community->name }}</p>
                        </div>
                        <div>
                            <a href="{{ route('church-elder.community.show', $community->id) }}" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left me-1"></i> Back to Community
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Total Offerings</h6>
                            <h3 class="mb-0">TZS {{ number_format($totalGeneralOfferings, 0) }}</h3>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-circle p-3">
                            <i class="fas fa-gift fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Mid-Week Offerings</h6>
                            <h3 class="mb-0">TZS {{ number_format($totalCommunityOfferings, 0) }}</h3>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-circle p-3">
                            <i class="fas fa-calendar-week fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Approved</h6>
                            <h3 class="mb-0">TZS {{ number_format($offeringStats['total'] ?? 0, 0) }}</h3>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-circle p-3">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Pending</h6>
                            <h3 class="mb-0">TZS {{ number_format($offeringStats['pending'] ?? 0, 0) }}</h3>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-circle p-3">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="{{ route('church-elder.offerings', $community->id) }}" class="btn btn-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                                <i class="fas fa-money-bill-wave fa-2x mb-2"></i>
                                <span>Record Offerings</span>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('church-elder.community-offerings.index', $community->id) }}" class="btn btn-success w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                                <i class="fas fa-calendar-week fa-2x mb-2"></i>
                                <span>Mid-Week Offerings</span>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('church-elder.offerings.all', $community->id) }}" class="btn btn-info w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                                <i class="fas fa-list fa-2x mb-2"></i>
                                <span>All Offerings</span>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('church-elder.reports', $community->id) }}" class="btn btn-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                                <i class="fas fa-chart-bar fa-2x mb-2"></i>
                                <span>Reports</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Offerings -->
    <div class="row">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-gift me-2"></i>Recent General Offerings</h5>
                </div>
                <div class="card-body">
                    @if($recentOfferings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Member</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentOfferings->take(10) as $offering)
                                    <tr>
                                        <td>{{ $offering->offering_date->format('M d, Y') }}</td>
                                        <td>{{ $offering->member->full_name ?? 'General' }}</td>
                                        <td><strong>TZS {{ number_format($offering->amount, 0) }}</strong></td>
                                        <td>
                                            <span class="badge bg-{{ $offering->approval_status === 'approved' ? 'success' : ($offering->approval_status === 'pending' ? 'warning' : 'secondary') }}">
                                                {{ ucfirst($offering->approval_status ?? 'pending') }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('church-elder.offerings.all', $community->id) }}" class="btn btn-sm btn-outline-primary">View All</a>
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i>No general offerings recorded yet.
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-calendar-week me-2"></i>Recent Mid-Week Offerings</h5>
                </div>
                <div class="card-body">
                    @if($communityOfferings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Service Type</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($communityOfferings->take(10) as $offering)
                                    <tr>
                                        <td>{{ $offering->offering_date->format('M d, Y') }}</td>
                                        <td>
                                            @if($offering->service_type)
                                                <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $offering->service_type)) }}</span>
                                            @else
                                                <span class="badge bg-secondary">General</span>
                                            @endif
                                        </td>
                                        <td><strong>TZS {{ number_format($offering->amount, 0) }}</strong></td>
                                        <td>
                                            @if($offering->status === 'pending_evangelism')
                                                <span class="badge bg-warning">Pending Leader</span>
                                            @elseif($offering->status === 'pending_secretary')
                                                <span class="badge bg-info">Pending Secretary</span>
                                            @elseif($offering->status === 'completed')
                                                <span class="badge bg-success">Completed</span>
                                            @elseif($offering->status === 'rejected')
                                                <span class="badge bg-danger">Rejected</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('church-elder.community-offerings.index', $community->id) }}" class="btn btn-sm btn-outline-success">View All</a>
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i>No mid-week offerings recorded yet.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection








