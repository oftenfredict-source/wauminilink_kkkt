@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0"><i class="fas fa-rings-wedding me-2 text-primary"></i>Church Wedding Requests</h1>
                            <p class="text-muted mb-0">Kufunga Ndoa Kanisani - Manage church wedding requests</p>
                        </div>
                        <a href="{{ route('evangelism-leader.church-wedding-requests.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> New Request
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="text-primary mb-0">{{ $requests->where('status', 'pending')->count() }}</h3>
                    <p class="text-muted mb-0">Pending</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="text-success mb-0">{{ $requests->where('status', 'approved')->count() }}</h3>
                    <p class="text-muted mb-0">Approved</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="text-warning mb-0">{{ $requests->where('status', 'documents_required')->count() }}</h3>
                    <p class="text-muted mb-0">Documents Required</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="text-dark mb-0">{{ $requests->count() }}</h3>
                    <p class="text-muted mb-0">Total</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @if($requests->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Groom</th>
                                <th>Bride</th>
                                <th>Preferred Date</th>
                                <th>Both Baptized</th>
                                <th>Submitted</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $request)
                                <tr>
                                    <td><strong>{{ $request->groom_full_name }}</strong></td>
                                    <td><strong>{{ $request->bride_full_name }}</strong></td>
                                    <td>{{ $request->preferred_wedding_date->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $request->both_baptized ? 'success' : 'warning' }}">
                                            {{ $request->both_baptized ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                    <td>{{ $request->submitted_at->format('M d, Y') }}</td>
                                    <td>
                                        @if($request->status === 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($request->status === 'approved')
                                            <span class="badge bg-success">Approved</span>
                                        @elseif($request->status === 'documents_required')
                                            <span class="badge bg-warning">Documents Required</span>
                                        @elseif($request->status === 'scheduled')
                                            <span class="badge bg-info">Scheduled</span>
                                        @elseif($request->status === 'rejected')
                                            <span class="badge bg-danger">Rejected</span>
                                        @elseif($request->status === 'completed')
                                            <span class="badge bg-dark">Completed</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('evangelism-leader.church-wedding-requests.show', $request->id) }}" 
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
                    {{ $requests->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No requests found. <a href="{{ route('evangelism-leader.church-wedding-requests.create') }}">Create a new request</a></p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection



