@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0"><i class="fas fa-cross me-2 text-primary"></i>Evangelism Leader Dashboard</h1>
                            <p class="text-muted mb-0">{{ $campus->name }}</p>
                        </div>
                        <div>
                            <span class="badge bg-primary fs-6">{{ $campus->code }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Total Members</h6>
                            <h2 class="mb-0">{{ number_format($totalMembers) }}</h2>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Communities</h6>
                            <h2 class="mb-0">{{ number_format($totalCommunities) }}</h2>
                        </div>
                        <div class="text-info">
                            <i class="fas fa-home fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Pending Tasks</h6>
                            <h2 class="mb-0">{{ number_format($pendingTasks) }}</h2>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-tasks fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Open Issues</h6>
                            <h2 class="mb-0">{{ number_format($openIssues) }}</h2>
                        </div>
                        <div class="text-danger">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Submitted Offerings</h6>
                            <h2 class="mb-0">{{ number_format($pendingOfferings ?? 0) }}</h2>
                            <small class="text-muted">TZS {{ number_format($pendingOfferingsAmount ?? 0, 2) }}</small>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-money-bill-wave fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if(isset($activeBereavements))
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Active Bereavements</h6>
                            <h2 class="mb-0">{{ number_format($activeBereavements ?? 0) }}</h2>
                            @if(isset($totalBereavementContributions))
                            <small class="text-muted">TZS {{ number_format($totalBereavementContributions, 2) }}</small>
                            @endif
                        </div>
                        <div class="text-danger">
                            <i class="fas fa-heart-broken fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="{{ route('evangelism-leader.register-member') }}" class="btn btn-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                                <i class="fas fa-user-plus fa-2x mb-2"></i>
                                <span>Register Member</span>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('evangelism-leader.reports.create') }}" class="btn btn-info w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                                <i class="fas fa-file-alt fa-2x mb-2"></i>
                                <span>Create Report</span>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('evangelism-leader.tasks.create') }}" class="btn btn-success w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                                <i class="fas fa-tasks fa-2x mb-2"></i>
                                <span>Report Task</span>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('evangelism-leader.finance.index') }}" class="btn btn-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                                <i class="fas fa-coins fa-2x mb-2"></i>
                                <span>Finance Management</span>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('departments.index') }}" class="btn btn-dark w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                                <i class="fas fa-layer-group fa-2x mb-2"></i>
                                <span>Church Departments</span>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('evangelism-leader.issues.create') }}" class="btn btn-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                                <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                                <span>Report Issue</span>
                            </a>
                        </div>
                    </div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-4">
                            <a href="{{ route('evangelism-leader.branch-services.index') }}" class="btn btn-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                                <i class="fas fa-church fa-2x mb-2"></i>
                                <span>Branch Sunday Services</span>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('evangelism-leader.branch-offerings.index') }}" class="btn btn-success w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                                <i class="fas fa-money-bill-wave fa-2x mb-2"></i>
                                <span>Branch Offerings</span>
                                @if(isset($branchOfferingsPendingCount) && $branchOfferingsPendingCount > 0)
                                    <span class="badge bg-danger mt-1">{{ $branchOfferingsPendingCount }} pending</span>
                                @endif
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('evangelism-leader.offerings.index') }}" class="btn btn-info w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                                <i class="fas fa-calendar-week fa-2x mb-2"></i>
                                <span>Community Offerings</span>
                            </a>
                        </div>
                    </div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-4">
                            <a href="{{ route('evangelism-leader.bereavement.index') }}" class="btn btn-danger w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                                <i class="fas fa-heart-broken fa-2x mb-2"></i>
                                <span>Bereavement Management</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Branch Sunday Services Section -->
    @if(isset($branchServices) && $branchServices->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-church me-2"></i>Recent Branch Sunday Services</h5>
                    <a href="{{ route('evangelism-leader.branch-services.create') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-plus me-1"></i>Create Service
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Theme</th>
                                    <th>Preacher</th>
                                    <th>Attendance</th>
                                    <th>Offerings</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($branchServices as $service)
                                <tr>
                                    <td>{{ $service->service_date->format('M d, Y') }}</td>
                                    <td>{{ $service->theme ?? '-' }}</td>
                                    <td>{{ $service->preacher ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-success">{{ $service->attendance_count ?? 0 }}</span>
                                        @if($service->guests_count > 0)
                                            <span class="badge bg-secondary">{{ $service->guests_count }} guests</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($service->branchOfferings && $service->branchOfferings->count() > 0)
                                            <strong>TZS {{ number_format($service->branchOfferings->sum('amount'), 2) }}</strong>
                                            <br><small class="text-muted">{{ $service->branchOfferings->count() }} offering(s)</small>
                                        @else
                                            <span class="text-muted">No offering recorded</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $service->status === 'completed' ? 'success' : 'warning' }}">
                                            {{ ucfirst($service->status ?? 'scheduled') }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('evangelism-leader.branch-services.show', $service->id) }}" class="btn btn-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if(!$service->branchOfferings || $service->branchOfferings->count() == 0)
                                                <a href="{{ route('evangelism-leader.branch-offerings.create', ['service_id' => $service->id]) }}" class="btn btn-success" title="Record Offering">
                                                    <i class="fas fa-money-bill-wave"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('evangelism-leader.branch-services.index') }}" class="btn btn-outline-primary">View All Branch Services</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <!-- Recent Reports -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Recent Reports</h5>
                </div>
                <div class="card-body">
                    @if($recentReports->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentReports as $report)
                            <a href="{{ route('evangelism-leader.reports.show', $report) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ Str::limit($report->title, 40) }}</h6>
                                    <small class="text-muted">{{ $report->created_at->format('M d') }}</small>
                                </div>
                                <p class="mb-1 text-muted small">{{ Str::limit($report->content, 60) }}</p>
                                @if($report->community)
                                <small class="text-muted"><i class="fas fa-home"></i> {{ $report->community->name }}</small>
                                @endif
                            </a>
                            @endforeach
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('evangelism-leader.reports.index') }}" class="btn btn-sm btn-outline-info">View All Reports</a>
                        </div>
                    @else
                        <p class="text-muted text-center py-4">No reports submitted yet.</p>
                        <div class="text-center">
                            <a href="{{ route('evangelism-leader.reports.create') }}" class="btn btn-sm btn-info">Create First Report</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Tasks -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-tasks me-2"></i>Recent Tasks</h5>
                </div>
                <div class="card-body">
                    @if($recentTasks->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentTasks as $task)
                            <a href="{{ route('evangelism-leader.tasks.show', $task) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ Str::limit($task->task_title, 40) }}</h6>
                                    <span class="badge bg-{{ $task->status === 'completed' ? 'success' : ($task->status === 'pending' ? 'warning' : 'secondary') }}">
                                        {{ ucfirst($task->status) }}
                                    </span>
                                </div>
                                <p class="mb-1 text-muted small">{{ $task->task_type_display }}</p>
                                <small class="text-muted">{{ $task->task_date->format('M d, Y') }}</small>
                            </a>
                            @endforeach
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('evangelism-leader.tasks.index') }}" class="btn btn-sm btn-outline-success">View All Tasks</a>
                        </div>
                    @else
                        <p class="text-muted text-center py-4">No tasks reported yet.</p>
                        <div class="text-center">
                            <a href="{{ route('evangelism-leader.tasks.create') }}" class="btn btn-sm btn-success">Report First Task</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Open Issues -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Open Issues</h5>
                </div>
                <div class="card-body">
                    @if($recentIssues->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentIssues as $issue)
                            <a href="{{ route('evangelism-leader.issues.show', $issue) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ Str::limit($issue->title, 40) }}</h6>
                                    <span class="badge {{ $issue->priority_badge }}">
                                        {{ $issue->priority_display }}
                                    </span>
                                </div>
                                <p class="mb-1 text-muted small">{{ Str::limit($issue->description, 60) }}</p>
                                <small class="text-muted">{{ $issue->created_at->format('M d, Y') }}</small>
                            </a>
                            @endforeach
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('evangelism-leader.issues.index') }}" class="btn btn-sm btn-outline-danger">View All Issues</a>
                        </div>
                    @else
                        <p class="text-muted text-center py-4">No issues reported yet.</p>
                        <div class="text-center">
                            <a href="{{ route('evangelism-leader.issues.create') }}" class="btn btn-sm btn-danger">Report First Issue</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection




