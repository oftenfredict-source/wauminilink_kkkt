@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0"><i class="fas fa-tasks me-2 text-primary"></i>Report Task</h1>
                            <p class="text-muted mb-0">{{ $campus->name }}</p>
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
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('evangelism-leader.tasks.store') }}" method="POST">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="task_type" class="form-label">Task Type <span class="text-danger">*</span></label>
                                <select name="task_type" id="task_type" class="form-select" required>
                                    <option value="">Select Task Type...</option>
                                    <option value="member_visit" {{ old('task_type') == 'member_visit' ? 'selected' : '' }}>Member Visit</option>
                                    <option value="prayer_request" {{ old('task_type') == 'prayer_request' ? 'selected' : '' }}>Prayer Request</option>
                                    <option value="follow_up" {{ old('task_type') == 'follow_up' ? 'selected' : '' }}>Follow Up</option>
                                    <option value="outreach" {{ old('task_type') == 'outreach' ? 'selected' : '' }}>Outreach</option>
                                    <option value="other" {{ old('task_type') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="task_date" class="form-label">Task Date <span class="text-danger">*</span></label>
                                <input type="date" name="task_date" id="task_date" class="form-control" value="{{ old('task_date', date('Y-m-d')) }}" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="community_id" class="form-label">Community (Optional)</label>
                                <select name="community_id" id="community_id" class="form-select">
                                    <option value="">Select Community...</option>
                                    @foreach($communities as $community)
                                        <option value="{{ $community->id }}" {{ old('community_id') == $community->id ? 'selected' : '' }}>
                                            {{ $community->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="member_id" class="form-label">
                                    Member 
                                    <span id="member_required" class="text-danger" style="display: none;">*</span>
                                    <span id="member_optional" class="text-muted">(Optional)</span>
                                </label>
                                <select name="member_id" id="member_id" class="form-select">
                                    <option value="">Select Member...</option>
                                    @foreach($members as $member)
                                        <option value="{{ $member->id }}" data-community-id="{{ $member->community_id }}" {{ old('member_id') == $member->id ? 'selected' : '' }}>
                                            {{ $member->full_name }} ({{ $member->member_id }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback" id="member_error" style="display: none;">
                                    Please select a member for member visit tasks.
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="task_title" class="form-label">Task Title <span class="text-danger">*</span></label>
                            <input type="text" name="task_title" id="task_title" class="form-control" value="{{ old('task_title') }}" required maxlength="255">
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="task_time" class="form-label">Task Time (Optional)</label>
                                <input type="time" name="task_time" id="task_time" class="form-control" value="{{ old('task_time') }}">
                            </div>
                            <div class="col-md-6">
                                <label for="location" class="form-label">Location (Optional)</label>
                                <input type="text" name="location" id="location" class="form-control" value="{{ old('location') }}" maxlength="255">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea name="description" id="description" class="form-control" rows="5" required>{{ old('description') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="outcome" class="form-label">Outcome (Optional)</label>
                            <textarea name="outcome" id="outcome" class="form-control" rows="3">{{ old('outcome') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes (Optional)</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('evangelism-leader.tasks.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Submit Task Report
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
    const communitySelect = document.getElementById('community_id');
    const memberRequired = document.getElementById('member_required');
    const memberOptional = document.getElementById('member_optional');
    const memberError = document.getElementById('member_error');
    
    // Store original member options
    const originalMemberOptions = Array.from(memberSelect.options);

    function filterMembersByCommunity() {
        const selectedCommunityId = communitySelect.value;
        const currentMemberValue = memberSelect.value;
        
        // Clear current options
        memberSelect.innerHTML = '';
        
        // Add default option
        memberSelect.appendChild(originalMemberOptions[0].cloneNode(true));
        
        let hasValidSelection = false;

        originalMemberOptions.slice(1).forEach(option => {
            const memberCommunityId = option.getAttribute('data-community-id');
            
            // Show member if no community selected OR member belongs to selected community
            if (!selectedCommunityId || memberCommunityId == selectedCommunityId) {
                const newOption = option.cloneNode(true);
                memberSelect.appendChild(newOption);
                
                if (option.value == currentMemberValue) {
                    newOption.selected = true;
                    hasValidSelection = true;
                }
            }
        });

        // Reset selection if the previously selected member is no longer visible
        if (currentMemberValue && !hasValidSelection) {
            memberSelect.value = '';
        }
    }

    // Filter members when community changes
    communitySelect.addEventListener('change', filterMembersByCommunity);

    function updateMemberFieldRequirement() {
        const taskType = taskTypeSelect.value;
        
        if (taskType === 'member_visit') {
            // Make member field required
            memberSelect.setAttribute('required', 'required');
            memberRequired.style.display = 'inline';
            memberOptional.style.display = 'none';
            memberSelect.classList.add('is-invalid');
        } else {
            // Make member field optional
            memberSelect.removeAttribute('required');
            memberRequired.style.display = 'none';
            memberOptional.style.display = 'inline';
            memberSelect.classList.remove('is-invalid');
            memberError.style.display = 'none';
        }
    }
    
    // Update on page load
    updateMemberFieldRequirement();
    filterMembersByCommunity(); // Run initial filter
    
    // Update when task type changes
    taskTypeSelect.addEventListener('change', function() {
        updateMemberFieldRequirement();
        
        // Clear member selection if task type is not member_visit
        if (this.value !== 'member_visit') {
            memberSelect.value = '';
        }
    });
    
    // Validate on form submit
    document.querySelector('form').addEventListener('submit', function(e) {
        const taskType = taskTypeSelect.value;
        
        if (taskType === 'member_visit' && !memberSelect.value) {
            e.preventDefault();
            memberSelect.classList.add('is-invalid');
            memberError.style.display = 'block';
            memberSelect.focus();
            return false;
        }
    });
    
    // Remove error when member is selected
    memberSelect.addEventListener('change', function() {
        if (this.value) {
            this.classList.remove('is-invalid');
            memberError.style.display = 'none';
        }
    });
});
</script>
@endsection

