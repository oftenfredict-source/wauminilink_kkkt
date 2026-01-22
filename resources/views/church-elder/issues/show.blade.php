@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0"><i class="fas fa-exclamation-triangle me-2 text-danger"></i>Issue Details</h1>
                            <p class="text-muted mb-0">{{ $community->name }}</p>
                        </div>
                        <a href="{{ route('church-elder.issues.index', $community->id) }}" class="btn btn-secondary">
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
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">{{ $issue->title }}</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Issue Type:</strong>
                            <span class="badge bg-secondary ms-2">{{ ucfirst(str_replace('_', ' ', $issue->issue_type)) }}</span>
                        </div>
                        <div class="col-md-4">
                            <strong>Priority:</strong>
                            <span class="badge {{ $issue->priority_badge }} ms-2">{{ ucfirst($issue->priority) }}</span>
                        </div>
                        <div class="col-md-4">
                            <strong>Status:</strong>
                            <span class="badge {{ $issue->status_badge }} ms-2">{{ ucfirst(str_replace('_', ' ', $issue->status)) }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <strong>Reported:</strong> {{ $issue->created_at->format('M d, Y h:i A') }}
                    </div>
                    <div class="mb-3">
                        <strong>Description:</strong>
                        <p class="mt-2">{{ $issue->description }}</p>
                    </div>
                    @if($issue->resolved_at)
                    <div class="mb-3">
                        <strong>Resolved:</strong> {{ $issue->resolved_at->format('M d, Y h:i A') }}
                        @if($issue->resolver)
                            by {{ $issue->resolver->name }}
                        @endif
                    </div>
                    @endif
                    @if($issue->resolution_notes)
                    <div class="mb-3">
                        <strong>Resolution Notes:</strong>
                        <p class="mt-2">{{ $issue->resolution_notes }}</p>
                    </div>
                    @endif

                    @if($issue->pastor_comments)
                    <div class="mb-3 mt-4">
                        <h6><i class="fas fa-user-tie me-2 text-primary"></i>Pastor's Comments & Suggestions</h6>
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
    </div>
</div>
@endsection






