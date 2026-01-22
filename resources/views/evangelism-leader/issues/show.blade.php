@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0"><i class="fas fa-exclamation-triangle me-2 text-primary"></i>{{ $issue->title }}</h1>
                            <p class="text-muted mb-0">{{ $issue->campus->name }}</p>
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
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="mb-4">
                        <h5>Issue Details</h5>
                        <hr>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong>Issue Type:</strong> 
                                <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $issue->issue_type)) }}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Priority:</strong> 
                                <span class="badge {{ $issue->priority_badge }}">
                                    {{ $issue->priority_display }}
                                </span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Status:</strong> 
                                <span class="badge {{ $issue->status_badge }}">
                                    {{ ucfirst(str_replace('_', ' ', $issue->status)) }}
                                </span>
                            </div>
                            @if($issue->community)
                            <div class="col-md-6 mb-3">
                                <strong>Community:</strong> 
                                <span class="badge bg-info">{{ $issue->community->name }}</span>
                            </div>
                            @endif
                            <div class="col-md-6 mb-3">
                                <strong>Reported:</strong> {{ $issue->created_at->format('F d, Y g:i A') }}
                            </div>
                            @if($issue->resolved_at && $issue->resolver)
                            <div class="col-md-6 mb-3">
                                <strong>Resolved by:</strong> {{ $issue->resolver->name }} on {{ $issue->resolved_at->format('F d, Y g:i A') }}
                            </div>
                            @endif
                        </div>
                    </div>

                    <div>
                        <h5>Description</h5>
                        <hr>
                        <div class="issue-description">
                            {!! nl2br(e($issue->description)) !!}
                        </div>
                    </div>

                    @if($issue->resolution_notes)
                    <div class="mt-4">
                        <h5>Resolution Notes</h5>
                        <hr>
                        <div class="alert alert-success">
                            {!! nl2br(e($issue->resolution_notes)) !!}
                        </div>
                    </div>
                    @endif

                    @if($issue->pastor_comments)
                    <div class="mt-4">
                        <h5><i class="fas fa-user-tie me-2 text-primary"></i>Pastor's Comments & Suggestions</h5>
                        <hr>
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
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Issue Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Campus:</strong><br>
                        {{ $issue->campus->name }}
                    </div>
                    @if($issue->community)
                    <div class="mb-3">
                        <strong>Community:</strong><br>
                        {{ $issue->community->name }}
                    </div>
                    @endif
                    <div class="mb-3">
                        <strong>Issue ID:</strong><br>
                        <code>#{{ $issue->id }}</code>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection




