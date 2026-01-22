@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0"><i class="fas fa-file-alt me-2 text-primary"></i>{{ $report->title }}</h1>
                            <p class="text-muted mb-0">{{ $report->campus->name }}</p>
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
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="mb-4">
                        <h5>Report Details</h5>
                        <hr>
                        <div class="mb-3">
                            <strong>Report Date:</strong> {{ $report->report_date->format('F d, Y') }}
                        </div>
                        @if($report->community)
                        <div class="mb-3">
                            <strong>Community:</strong> 
                            <span class="badge bg-info">{{ $report->community->name }}</span>
                        </div>
                        @endif
                        <div class="mb-3">
                            <strong>Status:</strong> 
                            <span class="badge bg-{{ $report->status === 'submitted' ? 'primary' : ($report->status === 'reviewed' ? 'success' : 'secondary') }}">
                                {{ ucfirst($report->status) }}
                            </span>
                        </div>
                        <div class="mb-3">
                            <strong>Submitted:</strong> {{ $report->created_at->format('F d, Y g:i A') }}
                        </div>
                        @if($report->reviewed_at && $report->reviewer)
                        <div class="mb-3">
                            <strong>Reviewed by:</strong> {{ $report->reviewer->name }} on {{ $report->reviewed_at->format('F d, Y g:i A') }}
                        </div>
                        @endif
                    </div>

                    <div>
                        <h5>Content</h5>
                        <hr>
                        <div class="report-content">
                            {!! nl2br(e($report->content)) !!}
                        </div>
                    </div>

                    @if($report->review_notes)
                    <div class="mt-4">
                        <h5>Review Notes</h5>
                        <hr>
                        <div class="alert alert-info">
                            {!! nl2br(e($report->review_notes)) !!}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Report Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Campus:</strong><br>
                        {{ $report->campus->name }}
                    </div>
                    @if($report->community)
                    <div class="mb-3">
                        <strong>Community:</strong><br>
                        {{ $report->community->name }}
                    </div>
                    @endif
                    <div class="mb-3">
                        <strong>Report ID:</strong><br>
                        <code>#{{ $report->id }}</code>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection




