@extends('layouts.index')

@section('content')
    <div class="container-fluid px-4">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('parish-worker.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('parish-worker.reports.index') }}">Reports</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">View Report</li>
                        </ol>
                    </nav>
                    <button onclick="window.print()" class="btn btn-outline-secondary btn-sm d-print-none">
                        <i class="fas fa-print me-2"></i>Print Report
                    </button>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0 text-primary">{{ $report->title }}</h4>
                            <span class="badge bg-{{ $report->status === 'reviewed' ? 'success' : 'primary' }} fs-6">
                                {{ ucfirst($report->status) }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-5">
                        <div class="row mb-5 pb-3 border-bottom">
                            <div class="col-md-6">
                                <h6 class="text-muted text-uppercase small fw-bold">Submitted By</h6>
                                <p class="mb-0 fw-bold">{{ $report->user->name }}</p>
                                <p class="text-muted small">Campus: {{ $report->campus->name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <h6 class="text-muted text-uppercase small fw-bold">Report Period</h6>
                                <p class="mb-0">{{ $report->report_period_start->format('M d, Y') }} -
                                    {{ $report->report_period_end->format('M d, Y') }}</p>
                                <p class="text-muted small">Submitted on
                                    {{ $report->submitted_at->format('F d, Y \a\t H:i') }}</p>
                            </div>
                        </div>

                        <div class="report-content mb-5">
                            <h6 class="text-muted text-uppercase small fw-bold mb-3">Report Details</h6>
                            <div style="white-space: pre-wrap; line-height: 1.8; color: #333;">{{ $report->content }}</div>
                        </div>

                        @if($report->status === 'reviewed')
                            <div class="bg-light p-4 rounded-3 border-start border-4 border-success mt-5">
                                <h6 class="text-success text-uppercase small fw-bold mb-2">Pastor's Feedback</h6>
                                <p class="mb-2 italic">"{{ $report->pastor_comments }}"</p>
                                <hr>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">Reviewed by:
                                        <strong>{{ $report->reviewer->name ?? 'Senior Pastor' }}</strong></small>
                                    <small class="text-muted">Date: {{ $report->reviewed_at->format('M d, Y') }}</small>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {

            .navbar,
            .sidebar,
            .d-print-none {
                display: none !important;
            }

            .main-content {
                margin-left: 0 !important;
                padding: 0 !important;
            }

            .card {
                box-shadow: none !important;
                border: 1px solid #eee !important;
            }
        }
    </style>
@endsection