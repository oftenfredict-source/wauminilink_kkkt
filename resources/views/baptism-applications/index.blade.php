@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0"><i class="fas fa-water me-2 text-primary"></i>Baptism Applications</h1>
                            <p class="text-muted mb-0">{{ $campus->name }}</p>
                        </div>
                        <a href="{{ route('evangelism-leader.baptism-applications.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> New Application
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Applications</h5>
                </div>
                <div class="card-body">
                    @if($applications->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Applicant Name</th>
                                        <th>Gender</th>
                                        <th>Age</th>
                                        <th>Phone</th>
                                        <th>Community</th>
                                        <th>Submitted Date</th>
                                        <th>Status</th>
                                        <th>Scheduled Date</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($applications as $application)
                                    <tr>
                                        <td><strong>{{ $application->full_name }}</strong></td>
                                        <td>{{ ucfirst($application->gender) }}</td>
                                        <td>
                                            {{ $application->age }}
                                            @if($application->age < 18)
                                                <span class="badge bg-info ms-1" title="Child Applicant">👶</span>
                                            @endif
                                        </td>
                                        <td>{{ $application->phone_number }}</td>
                                        <td>
                                            @if($application->community)
                                                <span class="badge bg-info">{{ $application->community->name }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $application->submitted_at->format('M d, Y') }}</td>
                                        <td>
                                            @if($application->status === 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @elseif($application->status === 'approved')
                                                <span class="badge bg-info">Approved</span>
                                            @elseif($application->status === 'scheduled')
                                                <span class="badge bg-success">Scheduled</span>
                                            @elseif($application->status === 'rejected')
                                                <span class="badge bg-danger">Rejected</span>
                                            @elseif($application->status === 'completed')
                                                <span class="badge bg-dark">Completed</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($application->scheduled_baptism_date)
                                                {{ $application->scheduled_baptism_date->format('M d, Y') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('evangelism-leader.baptism-applications.show', $application->id) }}" 
                                               class="btn btn-sm btn-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $applications->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-water fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No baptism applications submitted yet.</p>
                            <a href="{{ route('evangelism-leader.baptism-applications.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Create First Application
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

