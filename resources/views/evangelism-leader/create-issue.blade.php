@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0"><i class="fas fa-exclamation-triangle me-2 text-primary"></i>Report Issue</h1>
                            <p class="text-muted mb-0">{{ $campus->name }}</p>
                        </div>
                        <a href="{{ route('evangelism-leader.issues.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Issues
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
                    <form action="{{ route('evangelism-leader.issues.store') }}" method="POST">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="issue_type" class="form-label">Issue Type <span class="text-danger">*</span></label>
                                <select name="issue_type" id="issue_type" class="form-select" required>
                                    <option value="">Select Issue Type...</option>
                                    <option value="member_concern" {{ old('issue_type') == 'member_concern' ? 'selected' : '' }}>Member Concern</option>
                                    <option value="community_issue" {{ old('issue_type') == 'community_issue' ? 'selected' : '' }}>Community Issue</option>
                                    <option value="resource_need" {{ old('issue_type') == 'resource_need' ? 'selected' : '' }}>Resource Need</option>
                                    <option value="conflict" {{ old('issue_type') == 'conflict' ? 'selected' : '' }}>Conflict</option>
                                    <option value="other" {{ old('issue_type') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                                <select name="priority" id="priority" class="form-select" required>
                                    <option value="">Select Priority...</option>
                                    <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                                    <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="community_id" class="form-label">Community (Optional)</label>
                            <select name="community_id" id="community_id" class="form-select">
                                <option value="">Select Community...</option>
                                @foreach($communities as $community)
                                    <option value="{{ $community->id }}" {{ old('community_id') == $community->id ? 'selected' : '' }}>
                                        {{ $community->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Select a community if this issue is specific to a community</small>
                        </div>

                        <div class="mb-3">
                            <label for="title" class="form-label">Issue Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" required maxlength="255">
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea name="description" id="description" class="form-control" rows="8" required>{{ old('description') }}</textarea>
                            <small class="text-muted">Provide a detailed description of the issue</small>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('evangelism-leader.issues.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Submit Issue
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection




