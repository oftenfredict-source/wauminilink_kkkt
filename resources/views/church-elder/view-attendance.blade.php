@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0"><i class="fas fa-list-check me-2 text-info"></i>View Attendance</h1>
                            <p class="text-muted mb-0">{{ $community->name }}</p>
                        </div>
                        <div>
                            <a href="{{ route('church-elder.attendance', $community->id) }}" class="btn btn-primary me-2">
                                <i class="fas fa-plus me-1"></i> Record Attendance
                            </a>
                            <a href="{{ route('church-elder.dashboard') }}" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="GET" action="{{ route('church-elder.attendance.view', $community->id) }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="service_type" class="form-label">Service Type</label>
                            <select name="service_type" id="service_type" class="form-select">
                                <option value="">All Types</option>
                                <option value="sunday_service" {{ request('service_type') === 'sunday_service' ? 'selected' : '' }}>Sunday Service</option>
                                <option value="children_service" {{ request('service_type') === 'children_service' ? 'selected' : '' }}>Children Service</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="member_id" class="form-label">Member</label>
                            <select name="member_id" id="member_id" class="form-select">
                                <option value="">All Members</option>
                                @foreach($members as $member)
                                    <option value="{{ $member->id }}" {{ request('member_id') == $member->id ? 'selected' : '' }}>
                                        {{ $member->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-filter me-2"></i>Filter
                                </button>
                            </div>
                        </div>
                        <div class="col-12">
                            <a href="{{ route('church-elder.attendance.view', $community->id) }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Clear Filters
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Records Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    @if($attendances->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Member</th>
                                        <th>Member ID</th>
                                        <th>Service Type</th>
                                        <th>Service Date</th>
                                        <th>Recorded By</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($attendances as $attendance)
                                    <tr>
                                        <td>{{ $attendance->attended_at->format('M d, Y h:i A') }}</td>
                                        <td><strong>{{ $attendance->member->full_name ?? 'N/A' }}</strong></td>
                                        <td>{{ $attendance->member->member_id ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $attendance->service_type)) }}</span>
                                        </td>
                                        <td>
                                            @if($attendance->sundayService)
                                                {{ $attendance->sundayService->service_date->format('M d, Y') }}
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>{{ $attendance->recorded_by ?? 'System' }}</td>
                                        <td>{{ $attendance->notes ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $attendances->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-list-check fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No attendance records found.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection








