@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0"><i class="fas fa-file-alt me-2 text-primary"></i>Create Community Report</h1>
                            <p class="text-muted mb-0">{{ $campus->name }}</p>
                        </div>
                        <a href="{{ route('evangelism-leader.reports.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Reports
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
                    <form action="{{ route('evangelism-leader.reports.store') }}" method="POST">
                        @csrf

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
                                <small class="text-muted">Select a community if this report is specific to a community</small>
                            </div>
                            <div class="col-md-6">
                                <label for="report_date" class="form-label">Report Date <span class="text-danger">*</span></label>
                                <input type="date" name="report_date" id="report_date" class="form-control" value="{{ old('report_date', date('Y-m-d')) }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="title" class="form-label">Report Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" required maxlength="255">
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">Report Content <span class="text-danger">*</span></label>
                            <textarea name="content" id="content" class="form-control" rows="10" required>{{ old('content') }}</textarea>
                            <small class="text-muted">Describe the activities, progress, and updates for your community</small>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('evangelism-leader.reports.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Submit Report
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection




