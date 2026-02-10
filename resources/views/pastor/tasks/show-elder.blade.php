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
                            <p class="text-muted mb-0">Church Elder Task</p>
                        </div>
                        <a href="{{ route('pastor.tasks.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Tasks
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

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
        </div>

        <!-- Sidebar - Comments & Actions -->
        <div class="col-md-4 mb-4">
            @if($task->pastor_comments)
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-comment me-2"></i>Your Comments</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2">{{ $task->pastor_comments }}</p>
                    @if($task->pastor_commented_at)
                        <small class="text-muted">
                            <i class="fas fa-clock me-1"></i>{{ $task->pastor_commented_at->format('M d, Y h:i A') }}
                        </small>
                    @endif
                </div>
            </div>
            @endif

            <!-- Add/Update Comments Form -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-edit me-2"></i>{{ $task->pastor_comments ? 'Update Comments' : 'Add Comments/Suggestions' }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('pastor.tasks.comment-elder', $task->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="pastor_comments" class="form-label">Your Comments or Suggestions <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('pastor_comments') is-invalid @enderror" 
                                      id="pastor_comments" name="pastor_comments" rows="6" 
                                      placeholder="Provide your feedback, suggestions, or comments on this task..." required>{{ old('pastor_comments', $task->pastor_comments) }}</textarea>
                            @error('pastor_comments')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Minimum 10 characters required</small>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-paper-plane me-1"></i> {{ $task->pastor_comments ? 'Update Comments' : 'Submit Comments' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection








