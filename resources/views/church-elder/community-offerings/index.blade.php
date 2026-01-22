@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0"><i class="fas fa-money-bill-wave me-2 text-success"></i>Mid-Week Service Offerings</h1>
                            <p class="text-muted mb-0">Manage offerings from mid-week services</p>
                        </div>
                        @if(isset($community))
                            <a href="{{ route('church-elder.community-offerings.create', $community->id) }}" class="btn btn-primary">
                        @else
                            <a href="{{ route('church-elder.dashboard') }}" class="btn btn-primary">
                        @endif
                            <i class="fas fa-plus me-1"></i> Record New Offering
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

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(isset($community))
    <div class="row mb-3">
        <div class="col-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Showing offerings for <strong>{{ $community->name }}</strong>. 
                <a href="{{ route('church-elder.services', $community->id) }}" class="alert-link">View Services</a> to record offerings from mid-week services.
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>My Submitted Offerings</h5>
                    @if($offerings->count() > 0)
                        <span class="badge bg-light text-dark">
                            {{ $offerings->total() }} total | 
                            {{ $offerings->where('service_id', '!=', null)->count() }} from services |
                            {{ $offerings->where('service_id', null)->count() }} general
                        </span>
                    @endif
                </div>
                <div class="card-body">
                    @if($offerings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Service Type</th>
                                        <th>Community</th>
                                        <th>Amount (TZS)</th>
                                        <th>Collection Method</th>
                                        <th>Status</th>
                                        <th>Submitted</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($offerings as $offering)
                                    <tr>
                                        <td>{{ $offering->offering_date->format('M d, Y') }}</td>
                                        <td>
                                            @if($offering->service_type)
                                                <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $offering->service_type)) }}</span>
                                                @if($offering->service)
                                                    <br><small class="text-muted">{{ $offering->service->service_date->format('M d, Y') }}</small>
                                                @endif
                                            @else
                                                <span class="badge bg-secondary">General</span>
                                            @endif
                                        </td>
                                        <td>{{ $offering->community->name }}</td>
                                        <td><strong>{{ number_format($offering->amount, 2) }}</strong></td>
                                        <td>
                                            <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $offering->collection_method)) }}</span>
                                        </td>
                                        <td>
                                            @if($offering->status === 'pending_evangelism')
                                                <span class="badge bg-warning">Pending Leader</span>
                                            @elseif($offering->status === 'pending_secretary')
                                                <span class="badge bg-info">Pending Secretary</span>
                                            @elseif($offering->status === 'completed')
                                                <span class="badge bg-success">Completed</span>
                                            @elseif($offering->status === 'rejected')
                                                <span class="badge bg-danger">Rejected</span>
                                                @if($offering->rejection_reason)
                                                    <br><small class="text-danger">{{ Str::limit($offering->rejection_reason, 50) }}</small>
                                                @endif
                                            @endif
                                        </td>
                                        <td>{{ $offering->created_at->format('M d, Y H:i') }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('church-elder.community-offerings.show', $offering->id) }}" class="btn btn-sm btn-info" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($offering->status === 'rejected')
                                                <button type="button" class="btn btn-sm btn-warning" onclick="resubmitOffering({{ $offering->id }})" title="Correct and Resubmit">
                                                    <i class="fas fa-redo"></i>
                                                </button>
                                            @endif
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
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i>No offerings recorded yet.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

