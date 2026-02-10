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
            <div class="col-12 mb-4">
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
                                                    <span><i
                                                            class="fas fa-user me-1"></i>{{ $report->evangelismLeader->name ?? 'N/A' }}</span>
                                                    @if($report->campus)
                                                        <span class="ms-3"><i
                                                                class="fas fa-building me-1"></i>{{ $report->campus->name }}</span>
                                                    @endif
                                                    @if($report->community)
                                                        <span class="ms-3"><i
                                                                class="fas fa-map-marker-alt me-1"></i>{{ $report->community->name }}</span>
                                                    @endif
                                                    @if($report->report_date)
                                                        <span class="ms-3"><i
                                                                class="fas fa-calendar me-1"></i>{{ $report->report_date->format('M d, Y') }}</span>
                                                    @endif
                                                    @if($report->submitted_at)
                                                        <span class="ms-3"><i class="fas fa-clock me-1"></i>Submitted:
                                                            {{ $report->submitted_at->format('M d, Y') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="text-end ms-3">
                                                @if($report->status)
                                                    <span
                                                        class="badge bg-{{ $report->status === 'reviewed' ? 'success' : 'warning' }} mb-2 d-block">
                                                        {{ ucfirst($report->status) }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-3">
                                {{ $evangelismReports->appends(['evangelism_page' => $evangelismReports->currentPage()])->links() }}
                            </div>
                        @else
                            <p class="text-muted text-center py-4">No reports from Evangelism Leaders</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-hard-hat me-2"></i>Parish Worker Reports</h5>
                    </div>
                    <div class="card-body">
                        @if($parishWorkerReports->count() > 0)
                            <div class="list-group">
                                @foreach($parishWorkerReports as $report)
                                    <a href="{{ route('pastor.parish-worker.reports.show', $report->id) }}"
                                        class="list-group-item list-group-item-action">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">{{ $report->title }}</h6>
                                                <p class="mb-2 text-muted">{{ Str::limit($report->content, 200) }}</p>
                                                <div class="small text-muted">
                                                    <span><i class="fas fa-user me-1"></i>{{ $report->user->name ?? 'N/A' }}</span>
                                                    @if($report->campus)
                                                        <span class="ms-3"><i
                                                                class="fas fa-building me-1"></i>{{ $report->campus->name }}</span>
                                                    @endif
                                                    <span class="ms-3"><i class="fas fa-calendar me-1"></i>Period:
                                                        {{ $report->report_period_start->format('M d') }} -
                                                        {{ $report->report_period_end->format('M d, Y') }}</span>
                                                    @if($report->submitted_at)
                                                        <span class="ms-3"><i class="fas fa-clock me-1"></i>Submitted:
                                                            {{ $report->submitted_at->format('M d, Y H:i') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="text-end ms-3">
                                                <span
                                                    class="badge bg-{{ $report->status === 'reviewed' ? 'success' : 'warning' }} mb-2 d-block">
                                                    {{ ucfirst($report->status) }}
                                                </span>
                                                @if($report->reviewer)
                                                    <small class="text-muted d-block">Reviewed
                                                        by:<br>{{ $report->reviewer->name }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                            <div class="mt-3">
                                {{ $parishWorkerReports->appends(['parish_worker_page' => $parishWorkerReports->currentPage()])->links() }}
                            </div>
                        @else
                            <p class="text-muted text-center py-4">No reports from Parish Workers</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection