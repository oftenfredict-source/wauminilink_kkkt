@extends('layouts.index')

@section('content')
    <div class="container-fluid px-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h1 class="h3 mb-0"><i class="fas fa-walking me-2 text-secondary"></i>Parish Worker
                                    Activities</h1>
                                <p class="text-muted mb-0">Full history of activities recorded by Parish Workers</p>
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
                        <h5 class="mb-0"><i class="fas fa-filter me-2 text-primary"></i>Filter Activities (Chuja Shughuli)</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('parish-worker.activities.index') }}" method="GET" class="row g-3">
                            <div class="col-md-3">
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
                            <div class="col-md-3">
                                <label for="activity_type" class="form-label small fw-bold text-muted">Activity Type (Aina)</label>
                                <select name="activity_type" id="activity_type" class="form-select form-select-sm">
                                    <option value="">All Types</option>
                                    <option value="altar_cleanliness" {{ request('activity_type') == 'altar_cleanliness' ? 'selected' : '' }}>Altar Cleanliness</option>
                                    <option value="womens_department" {{ request('activity_type') == 'womens_department' ? 'selected' : '' }}>Women's Department</option>
                                    <option value="sunday_school" {{ request('activity_type') == 'sunday_school' ? 'selected' : '' }}>Sunday School</option>
                                    <option value="holy_communion" {{ request('activity_type') == 'holy_communion' ? 'selected' : '' }}>Holy Communion</option>
                                    <option value="church_candles" {{ request('activity_type') == 'church_candles' ? 'selected' : '' }}>Church Candles</option>
                                    <option value="other" {{ request('activity_type') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="date_from" class="form-label small fw-bold text-muted">From (Kuanzia)</label>
                                <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="date_to" class="form-label small fw-bold text-muted">To (Mpaka)</label>
                                <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <div class="btn-group w-100 btn-group-sm">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search me-1"></i>Apply
                                    </button>
                                    <a href="{{ route('parish-worker.activities.index') }}" class="btn btn-outline-secondary">
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
                                        <th class="px-4 py-3">Date</th>
                                        <th class="px-4 py-3">Parish Worker</th>
                                        <th class="px-4 py-3">Campus</th>
                                        <th class="px-4 py-3">Type</th>
                                        <th class="px-4 py-3">Title</th>
                                        <th class="px-4 py-3">Status</th>
                                        <th class="px-4 py-3">Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($activities as $activity)
                                        <tr>
                                            <td class="px-4 py-3 text-nowrap">{{ $activity->activity_date->format('M d, Y') }}
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="fw-bold">{{ $activity->user->name }}</div>
                                                <small class="text-muted">{{ $activity->user->email }}</small>
                                            </td>
                                            <td class="px-4 py-3">{{ $activity->campus->name ?? 'N/A' }}</td>
                                            <td class="px-4 py-3">
                                                <span
                                                    class="badge bg-light text-dark border">{{ $activity->activity_type_display }}</span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="fw-bold">{{ $activity->title }}</div>
                                                <small class="text-muted d-block"
                                                    style="max-width: 300px;">{{ Str::limit($activity->description, 100) }}</small>
                                            </td>
                                            <td class="px-4 py-3">
                                                <span
                                                    class="badge bg-{{ $activity->status === 'completed' ? 'success' : 'info' }}">
                                                    {{ ucfirst($activity->status) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <small class="text-muted">{{ $activity->notes ?: '-' }}</small>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-5">
                                                <i class="fas fa-calendar-times fa-3x mb-3 d-block text-muted"></i>
                                                No activities recorded yet.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if($activities->hasPages())
                        <div class="card-footer bg-white border-0 py-3">
                            {{ $activities->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection