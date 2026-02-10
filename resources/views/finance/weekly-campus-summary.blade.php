@extends('layouts.index')

@section('content')
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="mt-4">Weekly Campus Offering Summary</h1>
            <div>
                <span class="badge bg-info fs-6">
                    {{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}
                </span>
            </div>
        </div>

        <!-- Date Range Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('finance.weekly-campus-summary') }}" class="row g-3">
                    <div class="col-md-4">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date"
                            value="{{ $startDate->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date"
                            value="{{ $endDate->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        <a href="{{ route('finance.weekly-campus-summary') }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Grand Total Summary -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h6 class="card-title">Total Community Offerings</h6>
                        <h3 class="mb-0">TZS {{ number_format($grandTotal['community_offerings'], 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h6 class="card-title">Total Sunday Offerings</h6>
                        <h3 class="mb-0">TZS {{ number_format($grandTotal['sunday_offerings'], 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <h6 class="card-title">Grand Total</h6>
                        <h3 class="mb-0">TZS {{ number_format($grandTotal['total'], 2) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Campus Breakdown -->
        <div class="row">
            @foreach($summaryData as $data)
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                <i class="fas fa-building"></i> {{ $data['campus']->name }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <!-- Community Offerings -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">Community Offerings:</span>
                                    <strong class="text-primary">TZS
                                        {{ number_format($data['community_offerings'], 2) }}</strong>
                                </div>
                            </div>

                            <!-- Sunday Offerings Breakdown -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">Sunday Offerings:</span>
                                    <strong class="text-success">TZS {{ number_format($data['sunday_offerings'], 2) }}</strong>
                                </div>
                                <div class="ms-3">
                                    <small class="d-flex justify-content-between">
                                        <span class="text-muted">• Unity (Umoja):</span>
                                        <span>TZS {{ number_format($data['sunday_breakdown']['unity'], 2) }}</span>
                                    </small>
                                    <small class="d-flex justify-content-between">
                                        <span class="text-muted">• Building (Jengo):</span>
                                        <span>TZS {{ number_format($data['sunday_breakdown']['building'], 2) }}</span>
                                    </small>
                                    <small class="d-flex justify-content-between">
                                        <span class="text-muted">• Pledges (Ahadi):</span>
                                        <span>TZS {{ number_format($data['sunday_breakdown']['pledge'], 2) }}</span>
                                    </small>
                                    <small class="d-flex justify-content-between">
                                        <span class="text-muted">• Other (Nyingine):</span>
                                        <span>TZS {{ number_format($data['sunday_breakdown']['other'], 2) }}</span>
                                    </small>
                                </div>
                            </div>

                            <hr>

                            <!-- Campus Total -->
                            <div class="d-flex justify-content-between align-items-center">
                                <strong>Campus Total:</strong>
                                <h5 class="mb-0 text-danger">TZS {{ number_format($data['total'], 2) }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if(count($summaryData) === 0)
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No offering data found for the selected date range.
            </div>
        @endif
    </div>
@endsection