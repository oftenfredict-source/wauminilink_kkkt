@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0"><i class="fas fa-exclamation-triangle me-2 text-danger"></i>{{ $issue->title }}</h1>
                            <p class="text-muted mb-0">Church Elder Issue from {{ $issue->community->name ?? 'N/A' }}</p>
                        </div>
                        <a href="{{ route('evangelism-leader.church-elder-issues.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Issues
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 mb-4">
            <!-- Issue Details -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Issue Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Priority:</strong><br>
                            <span class="badge {{ $issue->priority_badge }}">{{ ucfirst($issue->priority) }}</span>
                        </div>
                        <div class="col-md-6">
                            <strong>Status:</strong><br>
                            <span class="badge {{ $issue->status_badge }}">{{ ucfirst(str_replace('_', ' ', $issue->status)) }}</span>
                        </div>
                    </div>
                    @if($issue->issue_type)
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Issue Type:</strong><br>
                            <span class="text-muted">{{ ucfirst(str_replace('_', ' ', $issue->issue_type)) }}</span>
                        </div>
                    </div>
                    @endif
                    <div class="mb-3">
                        <strong>Description:</strong><br>
                        <p class="text-muted">{{ $issue->description }}</p>
                    </div>
                    @if($issue->resolution_notes)
                    <div class="mb-3">
                        <strong>Resolution Notes:</strong><br>
                        <p class="text-muted">{{ $issue->resolution_notes }}</p>
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
                            <span class="text-muted">{{ $issue->churchElder->name ?? 'N/A' }}</span>
                        </div>
                        @if($issue->community)
                        <div class="col-md-6 mb-2">
                            <strong>Community:</strong><br>
                            <span class="text-muted">{{ $issue->community->name }}</span>
                        </div>
                        @endif
                        @if($issue->resolved_at)
                        <div class="col-md-6 mb-2">
                            <strong>Resolved At:</strong><br>
                            <span class="text-muted">{{ $issue->resolved_at->format('F d, Y h:i A') }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Pastor Comments -->
            @if($issue->pastor_comments)
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user-tie me-2"></i>Pastor's Comments & Suggestions</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <strong><i class="fas fa-user me-1"></i>{{ $issue->pastorCommenter->name ?? 'Pastor' }}</strong>
                                @if($issue->pastor_commented_at)
                                    <small class="text-muted ms-2">
                                        <i class="fas fa-clock me-1"></i>{{ $issue->pastor_commented_at->format('M d, Y h:i A') }}
                                    </small>
                                @endif
                            </div>
                        </div>
                        <div class="pastor-comments">
                            {!! nl2br(e($issue->pastor_comments)) !!}
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
                    <h5 class="mb-0">Issue Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Campus:</strong><br>
                        {{ $campus->name }}
                    </div>
                    <div class="mb-3">
                        <strong>Issue ID:</strong><br>
                        <code>#{{ $issue->id }}</code>
                    </div>
                    <div class="mb-3">
                        <strong>Reported:</strong><br>
                        {{ $issue->created_at->format('F d, Y g:i A') }}
                    </div>
                    @if($issue->updated_at)
                    <div class="mb-3">
                        <strong>Last Updated:</strong><br>
                        {{ $issue->updated_at->format('F d, Y g:i A') }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



