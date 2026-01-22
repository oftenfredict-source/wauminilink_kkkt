@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0"><i class="fas fa-tasks me-2 text-primary"></i>Task Details</h1>
                            <p class="text-muted mb-0">{{ $community->name }}</p>
                        </div>
                        <a href="{{ route('church-elder.tasks.index', $community->id) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Tasks
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">{{ $task->task_title }}</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Task Type:</strong>
                            <span class="badge bg-info ms-2">{{ $task->task_type_display }}</span>
                        </div>
                        <div class="col-md-6">
                            <strong>Status:</strong>
                            <span class="badge bg-{{ $task->status === 'completed' ? 'success' : ($task->status === 'pending' ? 'warning' : ($task->status === 'in_progress' ? 'info' : 'secondary')) }} ms-2">
                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                            </span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Task Date:</strong> {{ $task->task_date->format('M d, Y') }}
                        </div>
                        @if($task->task_time)
                        <div class="col-md-6">
                            <strong>Task Time:</strong> {{ \Carbon\Carbon::parse($task->task_time)->format('h:i A') }}
                        </div>
                        @endif
                    </div>
                    @if($task->member)
                    <div class="mb-3">
                        <strong>Member:</strong> {{ $task->member->full_name }} ({{ $task->member->member_id ?? 'N/A' }})
                    </div>
                    @endif
                    @if($task->location)
                    <div class="mb-3">
                        <strong>Location:</strong> {{ $task->location }}
                    </div>
                    @endif
                    <div class="mb-3">
                        <strong>Description:</strong>
                        <p class="mt-2">{{ $task->description }}</p>
                    </div>
                    @if($task->outcome)
                    <div class="mb-3">
                        <strong>Outcome:</strong>
                        <p class="mt-2">{{ $task->outcome }}</p>
                    </div>
                    @endif
                    @if($task->notes)
                    <div class="mb-3">
                        <strong>Notes:</strong>
                        <p class="mt-2">{{ $task->notes }}</p>
                    </div>
                    @endif

                    @if($task->pastor_comments)
                    <div class="mb-3 mt-4">
                        <h6><i class="fas fa-user-tie me-2 text-primary"></i>Pastor's Comments & Suggestions</h6>
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
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Update Status</h6>
                </div>
                <div class="card-body">
                    <form id="statusForm">
                        @csrf
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="pending" {{ $task->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="in_progress" {{ $task->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ $task->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ $task->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="outcome" class="form-label">Outcome</label>
                            <textarea name="outcome" id="outcome" class="form-control" rows="3">{{ $task->outcome }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3">{{ $task->notes }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-2"></i>Update Status
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('statusForm');
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';
        
        try {
            const response = await fetch('{{ route("church-elder.tasks.update-status", [$community->id, $task->id]) }}', {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(Object.fromEntries(formData))
            });
            
            const data = await response.json();
            
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: data.message,
                    confirmButtonText: 'OK'
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: data.message || 'Failed to update task status.'
                });
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'An error occurred while updating the task.'
            });
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });
});
</script>
@endsection






