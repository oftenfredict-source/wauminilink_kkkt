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
                                <h1 class="h3 mb-0"><i class="fas fa-church me-2 text-primary"></i>Parish Worker Dashboard
                                    (Mtendaji wa Usharika)</h1>
                                <p class="text-muted mb-0">{{ $campus->name ?? 'Main Campus' }}</p>
                            </div>
                            <div>
                                <span class="badge bg-primary fs-6">{{ $campus->code ?? 'HQ' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Total Activities</h6>
                                <h2 class="mb-0">{{ number_format($stats['total_activities']) }}</h2>
                            </div>
                            <div class="text-primary">
                                <i class="fas fa-clipboard-check fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Pending Activities</h6>
                                <h2 class="mb-0">{{ number_format($stats['pending_activities']) }}</h2>
                            </div>
                            <div class="text-warning">
                                <i class="fas fa-clock fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Reports Submitted</h6>
                                <h2 class="mb-0">{{ number_format($stats['total_reports']) }}</h2>
                            </div>
                            <div class="text-info">
                                <i class="fas fa-file-alt fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Candles on Hand</h6>
                                <h2 class="mb-0">{{ number_format($stats['candles_on_hand']) }}</h2>
                            </div>
                            <div class="text-danger">
                                <i class="fas fa-fire fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions (Hatua za Haraka)</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <a href="{{ route('parish-worker.activities.create') }}"
                                    class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center justify-content-center">
                                    <i class="fas fa-plus-circle fa-2x mb-2"></i>
                                    <span>Record Activity (Rekodi Shughuli)</span>
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="{{ route('parish-worker.candles.index') }}"
                                    class="btn btn-outline-danger w-100 py-3 d-flex flex-column align-items-center justify-content-center">
                                    <i class="fas fa-fire fa-2x mb-2"></i>
                                    <span>Candle Inventory (Kandili)</span>
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="{{ route('parish-worker.reports.create') }}"
                                    class="btn btn-outline-info w-100 py-3 d-flex flex-column align-items-center justify-content-center">
                                    <i class="fas fa-paper-plane fa-2x mb-2"></i>
                                    <span>Submit Report (Tuma Ripoti)</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Recent Activities -->
            <div class="col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-history me-2 text-primary"></i>Recent Activities</h5>
                        <a href="{{ route('parish-worker.activities.index') }}" class="btn btn-sm btn-link">View All</a>
                    </div>
                    <div class="card-body px-0">
                        @if($recentActivities->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th class="ps-4">Activity</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentActivities as $activity)
                                            <tr>
                                                <td class="ps-4">
                                                    <div class="fw-bold">{{ $activity->title }}</div>
                                                    <small class="text-muted">{{ $activity->activity_type_display }}</small>
                                                </td>
                                                <td>{{ $activity->activity_date->format('M d, Y') }}</td>
                                                <td>
                                                    <span
                                                        class="badge bg-{{ $activity->status === 'completed' ? 'success' : 'warning' }}">
                                                        {{ ucfirst($activity->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-clipboard fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No activities recorded in this campus yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Reports -->
            <div class="col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-file-invoice me-2 text-info"></i>Recent Reports</h5>
                        <a href="{{ route('parish-worker.reports.index') }}" class="btn btn-sm btn-link">View All</a>
                    </div>
                    <div class="card-body px-0">
                        @if($recentReports->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th class="ps-4">Title</th>
                                            <th>Period</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentReports as $report)
                                            <tr>
                                                <td class="ps-4">
                                                    <div class="fw-bold">{{ $report->title }}</div>
                                                    <small class="text-muted">Submitted
                                                        {{ $report->submitted_at->diffForHumans() }}</small>
                                                </td>
                                                <td>{{ $report->report_period_start->format('M d') }} -
                                                    {{ $report->report_period_end->format('M d, Y') }}
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge bg-{{ $report->status === 'reviewed' ? 'success' : 'primary' }}">
                                                        {{ ucfirst($report->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No reports submitted yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Responsibilities Guide -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm bg-light">
                    <div class="card-body">
                        <h6><i class="fas fa-info-circle me-2 text-primary"></i>Your Key Responsibilities:</h6>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <ul class="list-unstyled mb-0">
                                    <li><i class="fas fa-check-circle text-success me-2"></i> Altar Cleanliness (Usafi wa
                                        Madhabahu)</li>
                                    <li><i class="fas fa-check-circle text-success me-2"></i> Women's Department Activities
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <ul class="list-unstyled mb-0">
                                    <li><i class="fas fa-check-circle text-success me-2"></i> Sunday School Management</li>
                                    <li><i class="fas fa-check-circle text-success me-2"></i> Holy Communion Materials</li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <ul class="list-unstyled mb-0">
                                    <li><i class="fas fa-check-circle text-success me-2"></i> Church Candles (Purchase &
                                        Distribution)</li>
                                    <li><i class="fas fa-check-circle text-success me-2"></i> Periodic Reporting to Senior
                                        Pastor</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection