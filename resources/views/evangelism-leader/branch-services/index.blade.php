@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0"><i class="fas fa-church me-2 text-primary"></i>Branch Sunday Services</h1>
                            <p class="text-muted mb-0">{{ $campus->name }} - All Communities Together</p>
                        </div>
                        <a href="{{ route('evangelism-leader.branch-services.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> Create Service
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
                    <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Branch Sunday Services</h5>
                </div>
                <div class="card-body">
                    @if($services->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Theme</th>
                                        <th>Preacher</th>
                                        <th>Venue</th>
                                        <th>Attendance</th>
                                        <th>Offerings</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($services as $service)
                                    <tr>
                                        <td>{{ $service->service_date->format('M d, Y') }}</td>
                                        <td>{{ $service->theme ?? '-' }}</td>
                                        <td>{{ $service->preacher ?? '-' }}</td>
                                        <td>{{ $service->venue ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-success">{{ $service->attendance_count ?? 0 }}</span>
                                            @if($service->guests_count > 0)
                                                <span class="badge bg-secondary">{{ $service->guests_count }} guests</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($service->branchOfferings && $service->branchOfferings->count() > 0)
                                                <strong>TZS {{ number_format($service->branchOfferings->sum('amount'), 2) }}</strong>
                                                <br><small class="text-muted">{{ $service->branchOfferings->count() }} offering(s)</small>
                                            @else
                                                <span class="text-muted">No offering recorded</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $service->status === 'completed' ? 'success' : 'warning' }}">
                                                {{ ucfirst($service->status ?? 'scheduled') }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('evangelism-leader.branch-services.show', $service->id) }}" class="btn btn-info" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('evangelism-leader.branch-services.attendance', $service->id) }}" class="btn btn-primary" title="Record Attendance">
                                                    <i class="fas fa-user-check"></i>
                                                </a>
                                                @if(!$service->branchOfferings || $service->branchOfferings->count() == 0)
                                                    <a href="{{ route('evangelism-leader.branch-offerings.create', ['service_id' => $service->id]) }}" class="btn btn-success" title="Record Offering">
                                                        <i class="fas fa-money-bill-wave"></i>
                                                    </a>
                                                @endif
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
                        <div class="text-center py-5">
                            <i class="fas fa-church fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No branch Sunday services created yet.</p>
                            <a href="{{ route('evangelism-leader.branch-services.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Create First Service
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

