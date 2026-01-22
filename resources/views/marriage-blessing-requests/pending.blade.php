@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0"><i class="fas fa-heart me-2 text-warning"></i>Pending Marriage Blessing Requests</h1>
                            <p class="text-muted mb-0">Review and approve marriage blessing requests</p>
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

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Requests Pending Review ({{ $requests->total() }})</h5>
        </div>
        <div class="card-body">
            @if($requests->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Husband</th>
                                <th>Wife</th>
                                <th>Marriage Date</th>
                                <th>Both Members</th>
                                <th>Submitted By</th>
                                <th>Submitted Date</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $request)
                            <tr>
                                <td><strong>{{ $request->husband_full_name }}</strong></td>
                                <td><strong>{{ $request->wife_full_name }}</strong></td>
                                <td>{{ $request->marriage_date->format('M d, Y') }}</td>
                                <td>
                                    <span class="badge bg-{{ $request->both_spouses_members ? 'success' : 'warning' }}">
                                        {{ $request->both_spouses_members ? 'Yes' : 'No' }}
                                    </span>
                                </td>
                                <td>{{ $request->evangelismLeader->name ?? 'N/A' }}</td>
                                <td>{{ $request->submitted_at->format('M d, Y') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('pastor.marriage-blessing-requests.show', $request->id) }}" 
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
                    {{ $requests->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <p class="text-muted">No pending requests. All requests have been reviewed.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection



