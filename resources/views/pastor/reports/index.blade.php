@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0"><i class="fas fa-file-alt me-2 text-info"></i>All Reports</h1>
                            <p class="text-muted mb-0">View all reports from Evangelism Leaders</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-user-tie me-2"></i>Evangelism Leader Reports</h5>
                </div>
                <div class="card-body">
                    @if($evangelismReports->count() > 0)
                        <div class="list-group">
                            @foreach($evangelismReports as $report)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $report->title }}</h6>
                                            <p class="mb-2 text-muted">{{ Str::limit($report->content, 200) }}</p>
                                            <div class="small text-muted">
                                                <span><i class="fas fa-user me-1"></i>{{ $report->evangelismLeader->name ?? 'N/A' }}</span>
                                                @if($report->campus)
                                                    <span class="ms-3"><i class="fas fa-building me-1"></i>{{ $report->campus->name }}</span>
                                                @endif
                                                @if($report->community)
                                                    <span class="ms-3"><i class="fas fa-map-marker-alt me-1"></i>{{ $report->community->name }}</span>
                                                @endif
                                                @if($report->report_date)
                                                    <span class="ms-3"><i class="fas fa-calendar me-1"></i>{{ $report->report_date->format('M d, Y') }}</span>
                                                @endif
                                                @if($report->submitted_at)
                                                    <span class="ms-3"><i class="fas fa-clock me-1"></i>Submitted: {{ $report->submitted_at->format('M d, Y') }}</span>
                                                @endif
                                            </div>
                                            @if($report->review_notes)
                                                <div class="mt-2 p-2 bg-light rounded">
                                                    <small><strong>Review Notes:</strong> {{ $report->review_notes }}</small>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="text-end ms-3">
                                            @if($report->status)
                                                <span class="badge bg-{{ $report->status === 'reviewed' ? 'success' : 'warning' }} mb-2 d-block">
                                                    {{ ucfirst($report->status) }}
                                                </span>
                                            @endif
                                            @if($report->reviewer)
                                                <small class="text-muted d-block">
                                                    Reviewed by:<br>{{ $report->reviewer->name }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-3">
                            {{ $evangelismReports->links() }}
                        </div>
                    @else
                        <p class="text-muted text-center py-5">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            No reports from Evangelism Leaders
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



