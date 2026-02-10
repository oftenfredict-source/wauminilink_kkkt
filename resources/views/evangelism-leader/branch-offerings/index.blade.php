@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0"><i class="fas fa-money-bill-wave me-2 text-success"></i>Branch Sunday Service Offerings</h1>
                            <p class="text-muted mb-0">{{ $campus->name }} - Sent to General Secretary</p>
                        </div>
                        <a href="{{ route('evangelism-leader.branch-offerings.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> Record Offering
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

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Pending Secretary Approval</h6>
                            <h3 class="mb-0">TZS {{ number_format($totalPending, 2) }}</h3>
                        </div>
                        <i class="fas fa-clock fa-2x text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Completed</h6>
                            <h3 class="mb-0">TZS {{ number_format($totalCompleted, 2) }}</h3>
                        </div>
                        <i class="fas fa-check-circle fa-2x text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Branch Offerings</h5>
                </div>
                <div class="card-body">
                    @if($offerings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Service</th>
                                        <th>Amount</th>
                                        <th>Collection Method</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($offerings as $offering)
                                    <tr class="{{ $offering->status === 'rejected' ? 'table-danger' : '' }}">
                                        <td>{{ $offering->offering_date->format('M d, Y') }}</td>
                                        <td>
                                            @if($offering->service)
                                                <span class="badge bg-info">{{ $offering->service->service_date->format('M d, Y') }}</span>
                                                @if($offering->service->theme)
                                                    <br><small class="text-muted">{{ Str::limit($offering->service->theme, 30) }}</small>
                                                @endif
                                            @else
                                                <span class="text-muted">No service linked</span>
                                            @endif
                                        </td>
                                        <td><strong>TZS {{ number_format($offering->amount, 2) }}</strong></td>
                                        <td>
                                            <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $offering->collection_method)) }}</span>
                                            @if($offering->reference_number)
                                                <br><small class="text-muted">Ref: {{ $offering->reference_number }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($offering->status === 'pending_secretary')
                                                <span class="badge bg-warning">Pending Secretary</span>
                                            @elseif($offering->status === 'completed')
                                                <span class="badge bg-success">Completed</span>
                                            @elseif($offering->status === 'rejected')
                                                <span class="badge bg-danger">Rejected</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('evangelism-leader.branch-offerings.show', $offering->id) }}" class="btn btn-sm btn-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $offerings->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-money-bill-wave fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No branch offerings recorded yet.</p>
                            <a href="{{ route('evangelism-leader.branch-offerings.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Record First Offering
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection








