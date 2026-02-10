@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0"><i class="fas fa-exclamation-triangle me-2 text-danger"></i>Reported Issues</h1>
                            <p class="text-muted mb-0">{{ $community->name }}</p>
                        </div>
                        <a href="{{ route('church-elder.issues.create', $community->id) }}" class="btn btn-danger">
                            <i class="fas fa-plus me-2"></i>Report Issue
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
                    @if($issues->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Type</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Reported</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($issues as $issue)
                                    <tr>
                                        <td>{{ Str::limit($issue->title, 50) }}</td>
                                        <td>
                                            <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $issue->issue_type)) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $issue->priority_badge }}">
                                                {{ ucfirst($issue->priority) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $issue->status_badge }}">
                                                {{ ucfirst(str_replace('_', ' ', $issue->status)) }}
                                            </span>
                                        </td>
                                        <td>{{ $issue->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <a href="{{ route('church-elder.issues.show', [$community->id, $issue->id]) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> View
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
                        <div class="text-center py-5">
                            <i class="fas fa-exclamation-triangle fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No issues reported yet.</p>
                            <a href="{{ route('church-elder.issues.create', $community->id) }}" class="btn btn-danger">
                                <i class="fas fa-plus me-2"></i>Report First Issue
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection













