@extends('layouts.index')

@section('content')
    <div class="container-fluid px-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h1 class="h3 mb-0"><i class="fas fa-file-invoice me-2 text-info"></i>Parish Worker Performance Reports</h1>
                                <p class="text-muted mb-0">Historical performance reports submitted by Parish Workers</p>
                            </div>
                            <a href="{{ route('dashboard.pastor') }}" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0"><i class="fas fa-filter me-2 text-primary"></i>Filter Reports (Chuja Ripoti)</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('parish-worker.reports.index') }}" method="GET" class="row g-3">
                            <div class="col-md-4">
                                <label for="campus_id" class="form-label small fw-bold text-muted">Campus (Tawi)</label>
                                <select name="campus_id" id="campus_id" class="form-select form-select-sm">
                                    <option value="">All Campuses</option>
                                    @foreach($campuses as $campus)
                                        <option value="{{ $campus->id }}" {{ request('campus_id') == $campus->id ? 'selected' : '' }}>
                                            {{ $campus->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="user_id" class="form-label small fw-bold text-muted">Parish Worker (Mtendaji)</label>
                                <select name="user_id" id="user_id" class="form-select form-select-sm">
                                    <option value="">All Workers</option>
                                    @foreach($parishWorkers as $worker)
                                        <option value="{{ $worker->id }}" {{ request('user_id') == $worker->id ? 'selected' : '' }}>
                                            {{ $worker->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <div class="btn-group w-100 btn-group-sm">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search me-1"></i>Apply
                                    </button>
                                    <a href="{{ route('parish-worker.reports.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-undo"></i>
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="px-4 py-3">Submitted</th>
                                        <th class="px-4 py-3">Parish Worker</th>
                                        <th class="px-4 py-3">Campus</th>
                                        <th class="px-4 py-3">Title</th>
                                        <th class="px-4 py-3">Period</th>
                                        <th class="px-4 py-3">Status</th>
                                        <th class="px-4 py-3 text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($reports as $report)
                                        <tr>
                                            <td class="px-4 py-3 text-nowrap">
                                                {{ $report->submitted_at->format('M d, Y') }}<br>
                                                <small class="text-muted">{{ $report->submitted_at->format('H:i') }}</small>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="fw-bold">{{ $report->user->name }}</div>
                                                <small class="text-muted">{{ $report->user->email }}</small>
                                            </td>
                                            <td class="px-4 py-3">{{ $report->campus->name ?? 'N/A' }}</td>
                                            <td class="px-4 py-3">
                                                <div class="fw-bold">{{ $report->title }}</div>
                                            </td>
                                            <td class="px-4 py-3">
                                                {{ $report->report_period_start->format('M d') }} - {{ $report->report_period_end->format('M d, Y') }}
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="badge bg-{{ $report->status === 'reviewed' ? 'success' : 'primary' }}">
                                                    {{ ucfirst($report->status) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-end">
                                                <a href="{{ route('parish-worker.reports.show', $report->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye me-1"></i>View & Comment
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-5">
                                                <i class="fas fa-file-alt fa-3x mb-3 d-block text-muted"></i>
                                                No performance reports found.
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
        </div>
    </div>
@endsection
