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
                            <h1 class="h3 mb-0"><i class="fas fa-home me-2 text-primary"></i>{{ $community->name }}</h1>
                            <p class="text-muted mb-0">{{ $community->campus->name ?? 'N/A' }}</p>
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

    <!-- Community Details -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Community Details</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Community Name</label>
                            <p class="fw-bold">{{ $community->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Campus/Branch</label>
                            <p class="fw-bold">{{ $community->campus->name ?? 'N/A' }}</p>
                        </div>
                        @if($community->description)
                        <div class="col-12">
                            <label class="text-muted small">Description</label>
                            <p>{{ $community->description }}</p>
                        </div>
                        @endif
                        @if($community->address)
                        <div class="col-md-6">
                            <label class="text-muted small">Address</label>
                            <p>{{ $community->address }}</p>
                        </div>
                        @endif
                        @if($community->region)
                        <div class="col-md-3">
                            <label class="text-muted small">Region</label>
                            <p>{{ $community->region }}</p>
                        </div>
                        @endif
                        @if($community->district)
                        <div class="col-md-3">
                            <label class="text-muted small">District</label>
                            <p>{{ $community->district }}</p>
                        </div>
                        @endif
                        @if($community->ward)
                        <div class="col-md-3">
                            <label class="text-muted small">Ward</label>
                            <p>{{ $community->ward }}</p>
                        </div>
                        @endif
                        @if($community->phone_number)
                        <div class="col-md-3">
                            <label class="text-muted small">Phone Number</label>
                            <p>{{ $community->phone_number }}</p>
                        </div>
                        @endif
                        @if($community->email)
                        <div class="col-md-3">
                            <label class="text-muted small">Email</label>
                            <p>{{ $community->email }}</p>
                        </div>
                        @endif
                        <div class="col-md-3">
                            <label class="text-muted small">Status</label>
                            <p>
                                @if($community->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Total Members</span>
                            <strong>{{ number_format($stats['total_members']) }}</strong>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Active Members</span>
                            <strong class="text-success">{{ number_format($stats['active_members']) }}</strong>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Total Offerings</span>
                            <strong class="text-info">{{ number_format($stats['total_offerings'], 2) }} TZS</strong>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Pending Offerings</span>
                            <strong class="text-warning">{{ number_format($stats['pending_offerings'], 2) }} TZS</strong>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Total Attendance</span>
                            <strong>{{ number_format($stats['total_attendance']) }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Community Members -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>Community Members 
                        ({{ $community->members->count() + $community->memberChildren->count() }})
                        @if($community->memberChildren->count() > 0)
                            <small class="ms-2">({{ $community->members->count() }} adults, {{ $community->memberChildren->count() }} children)</small>
                        @endif
                    </h5>
                </div>
                <div class="card-body">
                    @if($community->members->count() > 0 || $community->memberChildren->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Member ID</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Type</th>
                                    <th>Parent/Guardian</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $counter = 1; @endphp
                                {{-- Regular Members --}}
                                @foreach($community->members as $member)
                                <tr>
                                    <td>{{ $counter++ }}</td>
                                    <td><strong>{{ $member->full_name }}</strong></td>
                                    <td>{{ $member->member_id ?? 'N/A' }}</td>
                                    <td>{{ $member->phone_number ?? '-' }}</td>
                                    <td>{{ $member->email ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-primary">Adult</span>
                                        <span class="badge bg-{{ $member->membership_type === 'permanent' ? 'success' : 'warning' }}">
                                            {{ ucfirst($member->membership_type) }}
                                        </span>
                                    </td>
                                    <td>—</td>
                                </tr>
                                @endforeach
                                {{-- Children who are church members --}}
                                @foreach($community->memberChildren as $child)
                                <tr>
                                    <td>{{ $counter++ }}</td>
                                    <td><strong>{{ $child->full_name }}</strong></td>
                                    <td>
                                        @if($child->member && $child->member->member_id)
                                            {{ $child->member->member_id }}-CH
                                        @else
                                            <span class="badge bg-info">Child</span>
                                        @endif
                                    </td>
                                    <td>{{ $child->member->phone_number ?? '-' }}</td>
                                    <td>{{ $child->member->email ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-success">Child Member</span>
                                    </td>
                                    <td>
                                        @if($child->member)
                                            {{ $child->member->full_name }}
                                        @else
                                            {{ $child->parent_name ?? '—' }}
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i>No members have been assigned to this community yet.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection









