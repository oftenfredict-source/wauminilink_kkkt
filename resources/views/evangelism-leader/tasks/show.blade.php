@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0"><i class="fas fa-tasks me-2 text-primary"></i>{{ $task->task_title }}</h1>
                            <p class="text-muted mb-0">{{ $task->campus->name }}</p>
                        </div>
                        <a href="{{ route('evangelism-leader.tasks.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Tasks
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="mb-4">
                        <h5>Task Details</h5>
                        <hr>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong>Task Type:</strong> 
                                <span class="badge bg-info">{{ $task->task_type_display }}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Status:</strong> 
                                <span class="badge bg-{{ $task->status === 'completed' ? 'success' : ($task->status === 'pending' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($task->status) }}
                                </span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Task Date:</strong> {{ $task->task_date->format('F d, Y') }}
                            </div>
                            @if($task->task_time)
                            <div class="col-md-6 mb-3">
                                <strong>Task Time:</strong> {{ date('g:i A', strtotime($task->task_time)) }}
                            </div>
                            @endif
                            @if($task->location)
                            <div class="col-md-6 mb-3">
                                <strong>Location:</strong> {{ $task->location }}
                            </div>
                            @endif
                            @if($task->member)
                            <div class="col-md-6 mb-3">
                                <strong>Member:</strong> {{ $task->member->full_name }} ({{ $task->member->member_id }})
                            </div>
                            @endif
                            @if($task->community)
                            <div class="col-md-6 mb-3">
                                <strong>Community:</strong> 
                                <span class="badge bg-info">{{ $task->community->name }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5>Description</h5>
                        <hr>
                        <div class="task-description">
                            {!! nl2br(e($task->description)) !!}
                        </div>
                    </div>

                    @if($task->outcome)
                    <div class="mb-4">
                        <h5>Outcome</h5>
                        <hr>
                        <div class="task-outcome">
                            {!! nl2br(e($task->outcome)) !!}
                        </div>
                    </div>
                    @endif

                    @if($task->notes)
                    <div class="mb-4">
                        <h5>Notes</h5>
                        <hr>
                        <div class="task-notes">
                            {!! nl2br(e($task->notes)) !!}
                        </div>
                    </div>
                    @endif

                    @if($task->pastor_comments)
                    <div class="mb-4">
                        <h5><i class="fas fa-user-tie me-2 text-primary"></i>Pastor's Comments & Suggestions</h5>
                        <hr>
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
                    @endif

                    @if($task->status === 'pending')
                    <div class="mt-4">
                        <h5>Update Task Status</h5>
                        <hr>
                        <form action="{{ route('evangelism-leader.tasks.update-status', $task) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="status" class="form-label">Status</label>
                                    <select name="status" id="status" class="form-select" required>
                                        <option value="pending" {{ $task->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="completed" {{ $task->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ $task->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="outcome" class="form-label">Outcome (Optional)</label>
                                <textarea name="outcome" id="outcome" class="form-control" rows="3">{{ old('outcome', $task->outcome) }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label for="notes" class="form-label">Notes (Optional)</label>
                                <textarea name="notes" id="notes" class="form-control" rows="3">{{ old('notes', $task->notes) }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Status
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Task Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Campus:</strong><br>
                        {{ $task->campus->name }}
                    </div>
                    <div class="mb-3">
                        <strong>Task ID:</strong><br>
                        <code>#{{ $task->id }}</code>
                    </div>
                    <div class="mb-3">
                        <strong>Created:</strong><br>
                        {{ $task->created_at->format('F d, Y g:i A') }}
                    </div>
                    @if($task->sent_to_pastor)
                    <div class="mb-3">
                        <strong>Sent to Pastor:</strong><br>
                        {{ $task->sent_to_pastor_at ? $task->sent_to_pastor_at->format('F d, Y g:i A') : 'Yes' }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection




