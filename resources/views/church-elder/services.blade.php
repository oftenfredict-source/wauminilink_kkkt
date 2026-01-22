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
                            <h1 class="h3 mb-0"><i class="fas fa-calendar-alt me-2 text-info"></i>Service Reports</h1>
                            <p class="text-muted mb-0">{{ $community->name }}</p>
                        </div>
                        <div>
                            <a href="{{ route('church-elder.community-offerings.index', $community->id) }}" class="btn btn-success me-2">
                                <i class="fas fa-money-bill-wave me-1"></i> View Mid-Week Offerings
                            </a>
                            <a href="{{ route('church-elder.services.create', $community->id) }}" class="btn btn-primary me-2">
                                <i class="fas fa-plus me-1"></i> Create Service
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

    <!-- Created Services -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-church me-2"></i>Created Services</h5>
                    <span class="badge bg-light text-dark">{{ $services->total() }} services</span>
                </div>
                <div class="card-body">
                    @if($services->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Service Type</th>
                                        <th>Theme</th>
                                        <th>Preacher</th>
                                        <th>Venue</th>
                                        <th>Attendance</th>
                                        <th>Offerings (TZS)</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($services as $service)
                                    <tr>
                                        <td>{{ $service->service_date->format('M d, Y') }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $service->service_type ?? 'Sunday Service')) }}</span>
                                        </td>
                                        <td>{{ $service->theme ?? '-' }}</td>
                                        <td>{{ $service->preacher ?? '-' }}</td>
                                        <td>{{ $service->venue ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-success">{{ $service->attendance_count ?? 0 }}</span>
                                            @if($service->guests_count > 0)
                                                <span class="badge bg-secondary">{{ $service->guests_count }} guests</span>
                                            @endif
                                        </td>
                                        <td><strong>{{ number_format($service->offerings_amount ?? 0, 2) }}</strong></td>
                                        <td>
                                            <span class="badge bg-{{ $service->status === 'completed' ? 'success' : 'warning' }}">
                                                {{ ucfirst($service->status ?? 'scheduled') }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('church-elder.attendance', $community->id) }}?service_id={{ $service->id }}" class="btn btn-info" title="Add Attendance">
                                                    <i class="fas fa-user-check"></i>
                                                </a>
                                                @if(in_array($service->service_type, ['prayer_meeting', 'bible_study', 'youth_service', 'women_fellowship', 'men_fellowship', 'evangelism']) && !isset($serviceOfferings[$service->id]))
                                                    <a href="{{ route('church-elder.community-offerings.create-from-service', [$community->id, $service->id]) }}" class="btn btn-success" title="Record Offering">
                                                        <i class="fas fa-money-bill-wave"></i>
                                                    </a>
                                                @elseif(isset($serviceOfferings[$service->id]))
                                                    <span class="badge bg-success" title="Offering Recorded">
                                                        <i class="fas fa-check"></i> Offering Recorded
                                                    </span>
                                                @endif
                                                <button type="button" class="btn btn-warning" onclick="editService({{ $service->id }})" title="Edit Service">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger" onclick="deleteService({{ $service->id }})" title="Delete Service">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $services->links() }}
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i>No services created yet. <a href="{{ route('church-elder.services.create', $community->id) }}" class="alert-link">Create your first service</a>.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Service Attendance Records -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Service Attendance Records</h5>
                    <span class="badge bg-light text-dark">{{ $recentAttendances->count() }} service dates</span>
                </div>
                <div class="card-body">
                    @if($recentAttendances->count() > 0)
                        @foreach($recentAttendances as $date => $attendances)
                        <div class="card mb-3 border-light">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="fas fa-calendar me-2"></i>
                                    {{ \Carbon\Carbon::parse($date)->format('l, F d, Y') }}
                                    <span class="badge bg-primary ms-2">{{ $attendances->count() }} attendees</span>
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Member Name</th>
                                                <th>Member ID</th>
                                                <th>Service Type</th>
                                                <th>Time</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($attendances as $attendance)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td><strong>{{ $attendance->member->full_name ?? 'N/A' }}</strong></td>
                                                <td>{{ $attendance->member->member_id ?? 'N/A' }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $attendance->service_type === 'sunday_service' ? 'primary' : 'info' }}">
                                                        {{ ucfirst(str_replace('_', ' ', $attendance->service_type ?? 'N/A')) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($attendance->attended_at)
                                                        {{ \Carbon\Carbon::parse($attendance->attended_at)->format('h:i A') }}
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i>No service attendance records found for this community yet.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Service Modal -->
<div class="modal fade" id="editServiceModal" tabindex="-1" aria-labelledby="editServiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editServiceModalLabel">Edit Service</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="editServiceModalBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function editService(serviceId) {
    const modal = new bootstrap.Modal(document.getElementById('editServiceModal'));
    const modalBody = document.getElementById('editServiceModalBody');
    
    modalBody.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
    modal.show();
    
    fetch(`{{ url("church-elder/community/{$community->id}/services") }}/${serviceId}/edit`, {
        headers: {
            'Accept': 'text/html'
        }
    })
    .then(response => response.text())
    .then(html => {
        modalBody.innerHTML = html;
    })
    .catch(error => {
        modalBody.innerHTML = '<div class="alert alert-danger">Failed to load service details.</div>';
    });
}

function deleteService(serviceId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`{{ url("church-elder/community/{$community->id}/services") }}/${serviceId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: data.message,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: data.message || 'Failed to delete service.'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An error occurred while deleting the service.'
                });
            });
        }
    });
}
</script>
@endsection

