@extends('layouts.index')

@section('content')
    <div class="container-fluid px-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h1 class="h3 mb-0"><i class="fas fa-heart me-2 text-warning"></i>Marriage Blessing Worklist
                                </h1>
                                <p class="text-muted mb-0">Review pending and manage scheduled marriage blessing requests
                                </p>
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
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="fas fa-tasks me-2"></i>Active Requests ({{ $requests->total() }})</h5>
            </div>
            <div class="card-body">
                @if($requests->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Husband</th>
                                    <th>Wife</th>
                                    <th>Status</th>
                                    <th>Marriage Date</th>
                                    <th>Both Members</th>
                                    <th>Submitted By</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($requests as $request)
                                    <tr>
                                        <td><strong>{{ $request->husband_full_name }}</strong></td>
                                        <td><strong>{{ $request->wife_full_name }}</strong></td>
                                        <td>
                                            @php
                                                $statusClass = match ($request->status) {
                                                    'pending' => 'warning',
                                                    'approved' => 'success',
                                                    'scheduled' => 'info',
                                                    'counseling_required' => 'primary',
                                                    'rejected' => 'danger',
                                                    default => 'secondary'
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $statusClass }}">
                                                {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($request->status === 'scheduled' && $request->scheduled_blessing_date)
                                                <span class="text-primary font-weight-bold">
                                                    <i class="fas fa-calendar-check me-1"></i>
                                                    {{ $request->scheduled_blessing_date->format('M d, Y') }}
                                                </span>
                                            @else
                                                {{ $request->marriage_date ? $request->marriage_date->format('M d, Y') : 'Not Set' }}
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $request->both_spouses_members ? 'success' : 'warning' }}">
                                                {{ $request->both_spouses_members ? 'Yes' : 'No' }}
                                            </span>
                                        </td>
                                        <td>{{ $request->evangelismLeader->name ?? 'N/A' }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('pastor.marriage-blessing-requests.show', $request->id) }}"
                                                class="btn btn-sm btn-primary" title="Review">
                                                <i class="fas fa-eye me-1"></i> View
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
                        <p class="text-muted">No active requests in your worklist.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection