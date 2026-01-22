@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0"><i class="fas fa-file-alt me-2 text-primary"></i>Community Reports</h1>
                            <p class="text-muted mb-0">View and manage your community reports</p>
                        </div>
                        <a href="{{ route('evangelism-leader.reports.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Create Report
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
                    @if($reports->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Community</th>
                                        <th>Report Date</th>
                                        <th>Status</th>
                                        <th>Submitted</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reports as $report)
                                    <tr>
                                        <td>{{ Str::limit($report->title, 50) }}</td>
                                        <td>
                                            @if($report->community)
                                                <span class="badge bg-info">{{ $report->community->name }}</span>
                                            @else
                                                <span class="text-muted">General</span>
                                            @endif
                                        </td>
                                        <td>{{ $report->report_date->format('M d, Y') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $report->status === 'submitted' ? 'primary' : ($report->status === 'reviewed' ? 'success' : 'secondary') }}">
                                                {{ ucfirst($report->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $report->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <a href="{{ route('evangelism-leader.reports.show', $report) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $reports->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No reports submitted yet.</p>
                            <a href="{{ route('evangelism-leader.reports.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Create First Report
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection




