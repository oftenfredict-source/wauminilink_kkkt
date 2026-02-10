@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0"><i class="fas fa-tasks me-2 text-primary"></i>Create Task</h1>
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
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form id="taskForm">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="task_type" class="form-label">Task Type <span class="text-danger">*</span></label>
                                <select name="task_type" id="task_type" class="form-select" required>
                                    <option value="">Select Task Type...</option>
                                    <option value="member_visit">Member Visit</option>
                                    <option value="prayer_request">Prayer Request</option>
                                    <option value="follow_up">Follow Up</option>
                                    <option value="outreach">Outreach</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="task_date" class="form-label">Task Date <span class="text-danger">*</span></label>
                                <input type="date" name="task_date" id="task_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="member_id" class="form-label">
                                    Member 
                                    <span id="member_required" class="text-danger" style="display: none;">*</span>
                                    <span id="member_optional" class="text-muted">(Optional)</span>
                                </label>
                                <select name="member_id" id="member_id" class="form-select">
                                    <option value="">Select Member...</option>
                                    @foreach($members as $member)
                                        <option value="{{ $member->id }}">{{ $member->full_name }} ({{ $member->member_id ?? 'N/A' }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="task_time" class="form-label">Task Time</label>
                                <input type="time" name="task_time" id="task_time" class="form-control">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="task_title" class="form-label">Task Title <span class="text-danger">*</span></label>
                            <input type="text" name="task_title" id="task_title" class="form-control" required maxlength="255">
                        </div>

                        <div class="mb-3">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" name="location" id="location" class="form-control" maxlength="255">
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea name="description" id="description" class="form-control" rows="5" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('church-elder.tasks.index', $community->id) }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Create Task
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const taskTypeSelect = document.getElementById('task_type');
    const memberSelect = document.getElementById('member_id');
    const memberRequired = document.getElementById('member_required');
    const memberOptional = document.getElementById('member_optional');
    
    function updateMemberFieldRequirement() {
        const taskType = taskTypeSelect.value;
        
        if (taskType === 'member_visit') {
            memberSelect.setAttribute('required', 'required');
            memberRequired.style.display = 'inline';
            memberOptional.style.display = 'none';
        } else {
            memberSelect.removeAttribute('required');
            memberRequired.style.display = 'none';
            memberOptional.style.display = 'inline';
        }
    }
    
    taskTypeSelect.addEventListener('change', updateMemberFieldRequirement);
    
    const form = document.getElementById('taskForm');
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating...';
        
        try {
            const response = await fetch('{{ route("church-elder.tasks.store", $community->id) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: data.message,
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = '{{ route("church-elder.tasks.index", $community->id) }}';
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: data.message || 'Failed to create task.'
                });
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'An error occurred while creating the task.'
            });
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });
});
</script>
@endsection













