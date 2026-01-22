@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0"><i class="fas fa-exclamation-triangle me-2 text-danger"></i>All Issues</h1>
                            <p class="text-muted mb-0">View all issues from Evangelism Leaders and Church Elders</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Evangelism Leader Issues -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user-tie me-2"></i>Evangelism Leader Issues</h5>
                </div>
                <div class="card-body">
                    @if($evangelismIssues->count() > 0)
                        <div class="list-group">
                            @foreach($evangelismIssues as $issue)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $issue->title }}</h6>
                                            <p class="mb-1 small text-muted">{{ Str::limit($issue->description, 150) }}</p>
                                            <small class="text-muted">
                                                <i class="fas fa-user me-1"></i>{{ $issue->evangelismLeader->name ?? 'N/A' }}
                                                @if($issue->community)
                                                    | <i class="fas fa-map-marker-alt me-1"></i>{{ $issue->community->name }}
                                                @endif
                                                @if($issue->issue_type)
                                                    | <i class="fas fa-tag me-1"></i>{{ ucfirst(str_replace('_', ' ', $issue->issue_type)) }}
                                                @endif
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge {{ $issue->priority_badge }} mb-1 d-block">{{ ucfirst($issue->priority) }}</span>
                                            <span class="badge {{ $issue->status_badge }} mb-2 d-block">{{ ucfirst(str_replace('_', ' ', $issue->status)) }}</span>
                                            <a href="{{ route('pastor.issues.show-evangelism', $issue->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye me-1"></i> View
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-3">
                            {{ $evangelismIssues->links() }}
                        </div>
                    @else
                        <p class="text-muted text-center py-3">No issues from Evangelism Leaders</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Church Elder Issues -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-church me-2"></i>Church Elder Issues</h5>
                </div>
                <div class="card-body">
                    @if($churchElderIssues->count() > 0)
                        <div class="list-group">
                            @foreach($churchElderIssues as $issue)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $issue->title }}</h6>
                                            <p class="mb-1 small text-muted">{{ Str::limit($issue->description, 150) }}</p>
                                            <small class="text-muted">
                                                <i class="fas fa-user me-1"></i>{{ $issue->churchElder->name ?? 'N/A' }}
                                                @if($issue->community)
                                                    | <i class="fas fa-map-marker-alt me-1"></i>{{ $issue->community->name }}
                                                @endif
                                                @if($issue->issue_type)
                                                    | <i class="fas fa-tag me-1"></i>{{ ucfirst(str_replace('_', ' ', $issue->issue_type)) }}
                                                @endif
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge {{ $issue->priority_badge }} mb-1 d-block">{{ ucfirst($issue->priority) }}</span>
                                            <span class="badge {{ $issue->status_badge }} mb-2 d-block">{{ ucfirst(str_replace('_', ' ', $issue->status)) }}</span>
                                            <a href="{{ route('pastor.issues.show-elder', $issue->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye me-1"></i> View
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-3">
                            {{ $churchElderIssues->links() }}
                        </div>
                    @else
                        <p class="text-muted text-center py-3">No issues from Church Elders</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

