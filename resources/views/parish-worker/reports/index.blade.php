@extends('layouts.index')

@section('content')
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
            <h1 class="h3 mb-0">My Performance Reports (Ripoti Zangu za Utendaji)</h1>
            <a href="{{ route('parish-worker.reports.create') }}" class="btn btn-info text-white">
                <i class="fas fa-plus-circle me-2"></i>Submit New Report
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Submitted Date</th>
                                <th>Report Title</th>
                                <th>Period</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reports as $report)
                                <tr>
                                    <td class="ps-4 text-muted">{{ $report->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="fw-bold">{{ $report->title }}</div>
                                    </td>
                                    <td>
                                        <span class="small text-muted">
                                            {{ $report->report_period_start->format('M d') }} -
                                            {{ $report->report_period_end->format('M d, Y') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $report->status === 'reviewed' ? 'success' : 'primary' }}">
                                            {{ ucfirst($report->status) }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route('parish-worker.reports.show', $report) }}"
                                            class="btn btn-sm btn-outline-info" title="View Report">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="fas fa-file-alt fa-3x mb-3"></i>
                                        <p>No reports submitted yet.</p>
                                        <a href="{{ route('parish-worker.reports.create') }}"
                                            class="btn btn-sm btn-info text-white">Submit Your First Report</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($reports->hasPages())
                <div class="card-footer bg-white border-0 py-3">
                    {{ $reports->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection