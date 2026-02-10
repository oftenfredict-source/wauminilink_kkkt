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
                                <h1 class="h3 mb-0"><i class="fas fa-church me-2 text-primary"></i>Church Elder Dashboard
                                </h1>
                                <p class="text-muted mb-0">{{ $community->name }} - {{ $community->campus->name ?? 'N/A' }}
                                </p>
                            </div>
                            <div>
                                <span class="badge bg-primary fs-6">Church Elder</span>
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
                                <h2 class="mb-0">{{ number_format($stats['total_members']) }}</h2>
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
                                <h6 class="text-muted mb-2">Active Members</h6>
                                <h2 class="mb-0">{{ number_format($stats['active_members']) }}</h2>
                            </div>
                            <div class="text-success">
                                <i class="fas fa-user-check fa-2x"></i>
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
                                <h2 class="mb-0">{{ number_format($pendingTasks ?? 0) }}</h2>
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
                                <h2 class="mb-0">{{ number_format($openIssues ?? 0) }}</h2>
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
                                <h6 class="text-muted mb-2">Total Offerings</h6>
                                <h2 class="mb-0">{{ number_format($stats['total_offerings'], 2) }} TZS</h2>
                            </div>
                            <div class="text-info">
                                <i class="fas fa-money-bill-wave fa-2x"></i>
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
                                <h6 class="text-muted mb-2">Total Attendance</h6>
                                <h2 class="mb-0">{{ number_format($stats['total_attendance']) }}</h2>
                            </div>
                            <div class="text-warning">
                                <i class="fas fa-calendar-check fa-2x"></i>
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
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <a href="{{ route('church-elder.community.show', $community->id) }}"
                                    class="btn btn-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                                    <i class="fas fa-info-circle fa-2x mb-2"></i>
                                    <span>Community Info</span>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('church-elder.services', $community->id) }}"
                                    class="btn btn-info w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                                    <i class="fas fa-calendar-alt fa-2x mb-2"></i>
                                    <span>Service Reports</span>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('church-elder.tasks.create', $community->id) }}"
                                    class="btn btn-success w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                                    <i class="fas fa-tasks fa-2x mb-2"></i>
                                    <span>Report Task</span>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('church-elder.issues.create', $community->id) }}"
                                    class="btn btn-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                                    <span>Report Issue</span>
                                </a>
                            </div>
                        </div>
                        <div class="row g-3 mt-2">
                            <div class="col-md-4">
                                <a href="{{ route('church-elder.community-offerings.index', $community->id) }}"
                                    class="btn btn-info w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                                    <i class="fas fa-calendar-week fa-2x mb-2"></i>
                                    <span>Mid-Week Offerings</span>
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="{{ route('church-elder.reports', $community->id) }}"
                                    class="btn btn-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                                    <i class="fas fa-chart-bar fa-2x mb-2"></i>
                                    <span>Reports</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Personal Information & Community Information -->
        <div class="row">
            <!-- Personal Information -->
            <div class="col-md-6 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0 text-white"><i class="fas fa-user me-2"></i>Personal Information</h5>
                    </div>
                    <div class="card-body">
                        @if($user->member)
                            <table class="table table-borderless">
                                <tr>
                                    <td class="text-muted" style="width: 40%;">Name:</td>
                                    <td><strong>{{ $user->member->full_name }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Member ID:</td>
                                    <td><strong>{{ $user->member->member_id }}</strong></td>
                                </tr>
                                @if($user->member->phone_number)
                                    <tr>
                                        <td class="text-muted">Phone:</td>
                                        <td>
                                            <a href="tel:{{ $user->member->phone_number }}" class="text-decoration-none">
                                                <i class="fas fa-phone me-1"></i>{{ $user->member->phone_number }}
                                            </a>
                                        </td>
                                    </tr>
                                @endif
                                @if($user->member->email)
                                    <tr>
                                        <td class="text-muted">Email:</td>
                                        <td>
                                            <a href="mailto:{{ $user->member->email }}" class="text-decoration-none">
                                                <i class="fas fa-envelope me-1"></i>{{ $user->member->email }}
                                            </a>
                                        </td>
                                    </tr>
                                @endif
                                @if($user->member->address)
                                    <tr>
                                        <td class="text-muted">Address:</td>
                                        <td>{{ $user->member->address }}</td>
                                    </tr>
                                @endif
                                @if($user->member->region || $user->member->district)
                                    <tr>
                                        <td class="text-muted">Location:</td>
                                        <td>
                                            @if($user->member->region)
                                                {{ $user->member->region }}
                                            @endif
                                            @if($user->member->district)
                                                @if($user->member->region), @endif{{ $user->member->district }}
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if($user->member->date_of_birth)
                                    <tr>
                                        <td class="text-muted">Date of Birth:</td>
                                        <td>{{ \Carbon\Carbon::parse($user->member->date_of_birth)->format('M d, Y') }}</td>
                                    </tr>
                                @endif
                                @if($user->member->gender)
                                    <tr>
                                        <td class="text-muted">Gender:</td>
                                        <td>{{ ucfirst($user->member->gender) }}</td>
                                    </tr>
                                @endif
                                @if($user->member->marital_status)
                                    <tr>
                                        <td class="text-muted">Marital Status:</td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $user->member->marital_status)) }}</td>
                                    </tr>
                                @endif
                            </table>
                            <a href="{{ route('members.show', $user->member->id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye me-1"></i> View Full Profile
                            </a>
                        @else
                            <p class="text-muted">No member information available.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Community Information -->
            <div class="col-md-6 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-home me-2"></i>Community Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td class="text-muted" style="width: 40%;">Name:</td>
                                <td><strong>{{ $community->name }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Campus:</td>
                                <td><strong>{{ $community->campus->name ?? 'N/A' }}</strong></td>
                            </tr>
                            @if($community->description)
                                <tr>
                                    <td class="text-muted">Description:</td>
                                    <td>{{ $community->description }}</td>
                                </tr>
                            @endif
                            @if($community->address)
                                <tr>
                                    <td class="text-muted">Address:</td>
                                    <td>{{ $community->address }}</td>
                                </tr>
                            @endif
                            @if($community->phone_number)
                                <tr>
                                    <td class="text-muted">Phone:</td>
                                    <td>{{ $community->phone_number }}</td>
                                </tr>
                            @endif
                            <tr>
                                <td class="text-muted">Status:</td>
                                <td>
                                    @if($community->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                        <a href="{{ route('church-elder.community.show', $community->id) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-eye me-1"></i> View Full Details
                        </a>
                    </div>
                </div>
            </div>

        </div>

        <div class="row">
            <!-- Recent Tasks -->
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-tasks me-2"></i>Recent Tasks</h5>
                    </div>
                    <div class="card-body">
                        @if(isset($recentTasks) && $recentTasks->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($recentTasks as $task)
                                    <a href="{{ route('church-elder.tasks.show', [$community->id, $task->id]) }}"
                                        class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">{{ Str::limit($task->task_title, 40) }}</h6>
                                            <span
                                                class="badge bg-{{ $task->status === 'completed' ? 'success' : ($task->status === 'pending' ? 'warning' : 'secondary') }}">
                                                {{ ucfirst($task->status) }}
                                            </span>
                                        </div>
                                        <p class="mb-1 text-muted small">
                                            @if($task->task_type)
                                                {{ ucfirst(str_replace('_', ' ', $task->task_type)) }}
                                            @endif
                                        </p>
                                        <small
                                            class="text-muted">{{ $task->task_date ? \Carbon\Carbon::parse($task->task_date)->format('M d, Y') : $task->created_at->format('M d, Y') }}</small>
                                    </a>
                                @endforeach
                            </div>
                            <div class="text-center mt-3">
                                <a href="{{ route('church-elder.tasks.index', $community->id) }}"
                                    class="btn btn-sm btn-outline-success">View All Tasks</a>
                            </div>
                        @else
                            <p class="text-muted text-center py-4">No tasks reported yet.</p>
                            <div class="text-center">
                                <a href="{{ route('church-elder.tasks.create', $community->id) }}"
                                    class="btn btn-sm btn-success">Create First Task</a>
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
                        @if(isset($recentIssues) && $recentIssues->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($recentIssues as $issue)
                                    <a href="{{ route('church-elder.issues.show', [$community->id, $issue->id]) }}"
                                        class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">{{ Str::limit($issue->title, 40) }}</h6>
                                            <span
                                                class="badge bg-{{ $issue->priority === 'urgent' ? 'danger' : ($issue->priority === 'high' ? 'warning' : 'info') }}">
                                                {{ ucfirst($issue->priority ?? 'medium') }}
                                            </span>
                                        </div>
                                        <p class="mb-1 text-muted small">{{ Str::limit($issue->description, 60) }}</p>
                                        <small class="text-muted">{{ $issue->created_at->format('M d, Y') }}</small>
                                    </a>
                                @endforeach
                            </div>
                            <div class="text-center mt-3">
                                <a href="{{ route('church-elder.issues.index', $community->id) }}"
                                    class="btn btn-sm btn-outline-danger">View All Issues</a>
                            </div>
                        @else
                            <p class="text-muted text-center py-4">No issues reported yet.</p>
                            <div class="text-center">
                                <a href="{{ route('church-elder.issues.create', $community->id) }}"
                                    class="btn btn-sm btn-danger">Report First Issue</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- This Month's Summary -->
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>This Month's Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Total Offerings:</span>
                                <strong class="text-success">{{ number_format($stats['total_offerings'], 2) }} TZS</strong>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 100%"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Pending Approval:</span>
                                <strong class="text-warning">{{ number_format($stats['pending_offerings'], 2) }}
                                    TZS</strong>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-warning" role="progressbar"
                                    style="width: {{ $stats['total_offerings'] > 0 ? ($stats['pending_offerings'] / $stats['total_offerings'] * 100) : 0 }}%">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Service Attendance:</span>
                                <strong>{{ number_format($stats['total_attendance']) }} sessions</strong>
                            </div>
                        </div>
                        <a href="{{ route('church-elder.reports', $community->id) }}" class="btn btn-sm btn-warning w-100">
                            <i class="fas fa-chart-bar me-1"></i> View Detailed Reports
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection