@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0"><i class="fas fa-tasks me-2 text-success"></i>{{ $task->task_title }}</h1>
                            <p class="text-muted mb-0">Church Elder Task from {{ $task->community->name ?? 'N/A' }}</p>
                        </div>
                        <a href="{{ route('evangelism-leader.church-elder-tasks.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Tasks
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 mb-4">
            <!-- Task Details -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Task Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Task Type:</strong><br>
                            <span class="badge bg-info">{{ $task->task_type_display }}</span>
                        </div>
                        <div class="col-md-6">
                            <strong>Status:</strong><br>
                            <span class="badge bg-{{ $task->status === 'completed' ? 'success' : ($task->status === 'in_progress' ? 'warning' : 'secondary') }}">
                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                            </span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Task Date:</strong><br>
                            <span class="text-muted">{{ $task->task_date->format('F d, Y') }}</span>
                        </div>
                        @if($task->task_time)
                        <div class="col-md-6">
                            <strong>Task Time:</strong><br>
                            <span class="text-muted">{{ \Carbon\Carbon::parse($task->task_time)->format('g:i A') }}</span>
                        </div>
                        @endif
                    </div>
                    @if($task->location)
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Location:</strong><br>
                            <span class="text-muted">{{ $task->location }}</span>
                        </div>
                    </div>
                    @endif
                    <div class="mb-3">
                        <strong>Description:</strong><br>
                        <p class="text-muted">{{ $task->description }}</p>
                    </div>
                    @if($task->outcome)
                    <div class="mb-3">
                        <strong>Outcome:</strong><br>
                        <p class="text-muted">{{ $task->outcome }}</p>
                    </div>
                    @endif
                    @if($task->notes)
                    <div class="mb-3">
                        <strong>Notes:</strong><br>
                        <p class="text-muted">{{ $task->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Reporter Information -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Reporter Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <strong>Church Elder:</strong><br>
                            <span class="text-muted">{{ $task->churchElder->name ?? 'N/A' }}</span>
                        </div>
                        @if($task->community)
                        <div class="col-md-6 mb-2">
                            <strong>Community:</strong><br>
                            <span class="text-muted">{{ $task->community->name }}</span>
                        </div>
                        @endif
                        @if($task->member)
                        <div class="col-md-6 mb-2">
                            <strong>Related Member:</strong><br>
                            <span class="text-muted">{{ $task->member->full_name }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Pastor Comments -->
            @if($task->pastor_comments)
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user-tie me-2"></i>Pastor's Comments & Suggestions</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <strong><i class="fas fa-user me-1"></i>{{ $task->pastorCommenter->name ?? 'Pastor' }}</strong>
                                @if($task->pastor_commented_at)
                                    <small class="text-muted ms-2">
                                        <i class="fas fa-clock me-1"></i>{{ $task->pastor_commented_at->format('M d, Y h:i A') }}
                                    </small>
                                @endif
                            </div>
                        </div>
                        <div class="pastor-comments">
                            {!! nl2br(e($task->pastor_comments)) !!}
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Task Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Campus:</strong><br>
                        {{ $campus->name }}
                    </div>
                    <div class="mb-3">
                        <strong>Task ID:</strong><br>
                        <code>#{{ $task->id }}</code>
                    </div>
                    <div class="mb-3">
                        <strong>Created:</strong><br>
                        {{ $task->created_at->format('F d, Y g:i A') }}
                    </div>
                    @if($task->updated_at)
                    <div class="mb-3">
                        <strong>Last Updated:</strong><br>
                        {{ $task->updated_at->format('F d, Y g:i A') }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



