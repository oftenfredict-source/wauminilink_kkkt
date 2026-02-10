@extends('layouts.index')

@section('content')
    <div class="container-fluid px-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h1 class="h3 mb-0"><i class="fas fa-file-invoice me-2 text-primary"></i>Parish Worker
                                    Report Details</h1>
                                <p class="text-muted mb-0">Reviewing report from {{ $report->user->name }}</p>
                            </div>
                            <div class="d-flex gap-2">
                                <button onclick="window.print()" class="btn btn-outline-secondary">
                                    <i class="fas fa-print me-1"></i>Print View
                                </button>
                                <a href="{{ route('parish-worker.reports.index') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-arrow-left me-1"></i>Back to Reports
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Report Content -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">{{ $report->title }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="text-muted small d-block">Parish Worker</label>
                                <div class="fw-bold">{{ $report->user->name }}</div>
                                <div class="small">{{ $report->user->email }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small d-block">Report Period</label>
                                <div class="fw-bold">{{ $report->report_period_start->format('M d, Y') }} -
                                    {{ $report->report_period_end->format('M d, Y') }}
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="report-content mt-4" style="white-space: pre-wrap; line-height: 1.6;">
                            {{ $report->content }}
                        </div>
                    </div>
                    <div class="card-footer bg-light py-2 text-end">
                        <small class="text-muted">Submitted on: {{ $report->submitted_at->format('M d, Y H:i') }}</small>
                    </div>
                </div>
            </div>

            <!-- Pastor Review -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4 sticky-top" style="top: 20px;">
                    <div
                        class="card-header bg-{{ $report->status === 'reviewed' ? 'success' : 'warning' }} text-white py-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-comment-medical me-2"></i>Review & Comments
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4 text-center">
                            <div class="small text-muted mb-1">Current Status</div>
                            <span
                                class="badge rounded-pill bg-{{ $report->status === 'reviewed' ? 'success' : 'warning' }} px-4 py-2 fs-6">
                                {{ strtoupper($report->status) }}
                            </span>
                        </div>

                        <form action="{{ route('pastor.parish-worker.reports.comment', $report->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="pastor_comments" class="form-label fw-bold">Pastor's Feedback</label>
                                <textarea name="pastor_comments" id="pastor_comments" rows="8" class="form-control"
                                    placeholder="Provide your feedback, guidance or appreciation here..."
                                    required>{{ $report->pastor_comments }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-save me-2"></i>Submit Review
                            </button>
                        </form>

                        @if($report->reviewed_at)
                            <div class="mt-4 pt-4 border-top">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-info-circle text-primary me-2"></i>
                                    <span class="small fw-bold">Review History</span>
                                </div>
                                <small class="text-muted d-block">
                                    <strong>Reviewed by:</strong> {{ $report->reviewer->name ?? 'Unknown' }}
                                </small>
                                <small class="text-muted d-block">
                                    <strong>Reviewed on:</strong> {{ $report->reviewed_at->format('M d, Y H:i') }}
                                </small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            .container-fluid {
                padding: 0 !important;
            }

            .btn,
            .sidebar,
            .sticky-top,
            form {
                display: none !important;
            }

            .card {
                border: 1px solid #ddd !important;
                box-shadow: none !important;
            }

            .col-lg-8 {
                width: 100% !important;
            }

            .col-lg-4 {
                width: 100% !important;
                margin-top: 20px;
            }
        }
    </style>
@endsection