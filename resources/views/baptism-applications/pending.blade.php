@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0"><i class="fas fa-water me-2 text-warning"></i>Pending Baptism Applications</h1>
                            <p class="text-muted mb-0">Review and approve baptism applications</p>
                        </div>
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

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Applications Pending Review ({{ $applications->total() }})</h5>
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
                                        <th>Submitted By</th>
                                        <th>Submitted Date</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($applications as $application)
                                    <tr>
                                        <td><strong>{{ $application->full_name }}</strong></td>
                                        <td>{{ ucfirst($application->gender) }}</td>
                                        <td>{{ $application->age }}</td>
                                        <td>{{ $application->phone_number }}</td>
                                        <td>
                                            @if($application->community)
                                                <span class="badge bg-info">{{ $application->community->name }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $application->evangelismLeader->name ?? 'N/A' }}</td>
                                        <td>{{ $application->submitted_at->format('M d, Y') }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('pastor.baptism-applications.show', $application->id) }}" 
                                               class="btn btn-sm btn-primary" title="Review">
                                                <i class="fas fa-eye me-1"></i> Review
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
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <p class="text-muted">No pending applications. All applications have been reviewed.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

