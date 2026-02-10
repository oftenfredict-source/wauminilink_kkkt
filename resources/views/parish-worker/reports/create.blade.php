@extends('layouts.index')

@section('content')
    <div class="container-fluid px-4">
        <div class="row justify-content-center">
            <div class="col-md-9">
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-file-export me-2"></i>New Activity Report (Ripoti Mpya ya
                            Utendaji)</h5>
                        <a href="{{ route('parish-worker.dashboard') }}" class="btn btn-light btn-sm">Back to Dashboard</a>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('parish-worker.reports.store') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="title" class="form-label fw-bold">Report Title (Kichwa cha Ripoti)</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                                    name="title" value="{{ old('title') }}"
                                    placeholder="e.g., Monthly Activity Report - January 2026" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="report_period_start" class="form-label fw-bold">From Date (Kuanzia
                                        Tarehe)</label>
                                    <input type="date"
                                        class="form-control @error('report_period_start') is-invalid @enderror"
                                        id="report_period_start" name="report_period_start"
                                        value="{{ old('report_period_start') }}" required>
                                    @error('report_period_start')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="report_period_end" class="form-label fw-bold">To Date (Hadi Tarehe)</label>
                                    <input type="date" class="form-control @error('report_period_end') is-invalid @enderror"
                                        id="report_period_end" name="report_period_end"
                                        value="{{ old('report_period_end', date('Y-m-d')) }}" required>
                                    @error('report_period_end')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="content" class="form-label fw-bold">Report Content (Maelezo ya Ripoti)</label>
                                <textarea class="form-control @error('content') is-invalid @enderror" id="content"
                                    name="content" rows="12"
                                    placeholder="Summarize your activities, successes, challenges, and any requirements..."
                                    required>{{ old('content') }}</textarea>
                                <div class="form-text text-muted mt-2">
                                    <i class="fas fa-info-circle me-1"></i> Tip: This report will be visible to the Senior
                                    Pastor for review.
                                </div>
                                @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="submit" class="btn btn-info px-5 py-2 fw-bold text-white">
                                    <i class="fas fa-paper-plane me-2"></i>Submit Report to Pastor
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection