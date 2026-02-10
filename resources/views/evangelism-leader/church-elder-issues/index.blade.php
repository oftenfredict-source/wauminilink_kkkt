@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0"><i class="fas fa-exclamation-triangle me-2 text-danger"></i>Church Elder Issues</h1>
                            <p class="text-muted mb-0">Issues reported by Church Elders in {{ $campus->name }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-church me-2"></i>All Church Elder Issues</h5>
                </div>
                <div class="card-body">
                    @if($issues->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Church Elder</th>
                                        <th>Community</th>
                                        <th>Type</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($issues as $issue)
                                        <tr>
                                            <td>
                                                <strong>{{ $issue->title }}</strong>
                                                @if($issue->pastor_comments)
                                                    <i class="fas fa-comment text-primary ms-2" title="Pastor has commented"></i>
                                                @endif
                                            </td>
                                            <td>{{ $issue->churchElder->name ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-info">{{ $issue->community->name ?? 'N/A' }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $issue->issue_type ?? 'N/A')) }}</span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $issue->priority_badge }}">{{ ucfirst($issue->priority) }}</span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $issue->status_badge }}">{{ ucfirst(str_replace('_', ' ', $issue->status)) }}</span>
                                            </td>
                                            <td>
                                                <a href="{{ route('evangelism-leader.church-elder-issues.show', $issue->id) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye me-1"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $issues->links() }}
                        </div>
                    @else
                        <p class="text-muted text-center py-5">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            No issues reported by Church Elders yet
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection








