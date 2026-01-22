@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0"><i class="fas fa-chart-bar me-2 text-warning"></i>Community Reports</h1>
                            <p class="text-muted mb-0">{{ $community->name }}</p>
                        </div>
                        <div>
                            <a href="{{ route('church-elder.dashboard') }}" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="GET" action="{{ route('church-elder.reports', $community->id) }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}" required>
                        </div>
                        <div class="col-md-4">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-filter me-1"></i> Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Summary -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted">Total Members</h6>
                    <h3 class="text-primary mb-0">{{ number_format($stats['total_members']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted">Total Offerings</h6>
                    <h3 class="text-success mb-0">{{ number_format($offeringStats['total'], 2) }} TZS</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted">Pending Offerings</h6>
                    <h3 class="text-warning mb-0">{{ number_format($offeringStats['pending'], 2) }} TZS</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted">Total Attendance</h6>
                    <h3 class="text-info mb-0">{{ number_format($stats['total_attendance']) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Offerings by Type -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Offerings by Type</h5>
                </div>
                <div class="card-body">
                    @if($offeringStats['by_type']->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th class="text-end">Amount</th>
                                    <th class="text-end">Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($offeringStats['by_type'] as $type)
                                <tr>
                                    <td><strong>{{ ucfirst(str_replace('_', ' ', $type->offering_type)) }}</strong></td>
                                    <td class="text-end text-success"><strong>{{ number_format($type->total, 2) }} TZS</strong></td>
                                    <td class="text-end">{{ $type->count }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-primary">
                                    <td><strong>Total</strong></td>
                                    <td class="text-end"><strong>{{ number_format($offeringStats['by_type']->sum('total'), 2) }} TZS</strong></td>
                                    <td class="text-end"><strong>{{ $offeringStats['by_type']->sum('count') }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i>No offerings recorded for this period.
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Attendance Statistics -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-calendar-check me-2"></i>Attendance by Date</h5>
                </div>
                <div class="card-body">
                    @if($attendanceStats->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th class="text-end">Attendance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($attendanceStats as $stat)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($stat->attendance_date)->format('M d, Y') }}</td>
                                    <td class="text-end">
                                        <span class="badge bg-primary">{{ $stat->attendance_count }} members</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-primary">
                                    <td><strong>Total Sessions</strong></td>
                                    <td class="text-end"><strong>{{ $attendanceStats->count() }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i>No attendance records for this period.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

