@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0"><i class="fas fa-money-bill-wave me-2 text-success"></i>Offering Details</h1>
                            <p class="text-muted mb-0">View complete offering information</p>
                        </div>
                        <a href="{{ url()->previous() }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- Offering Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Offering Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Amount:</strong><br>
                            <h4 class="text-success">TZS {{ number_format($offering->amount, 2) }}</h4>
                        </div>
                        <div class="col-md-6">
                            <strong>Status:</strong><br>
                            @if($offering->status === 'pending_evangelism')
                                <span class="badge bg-warning fs-6">Pending Leader</span>
                            @elseif($offering->status === 'pending_secretary')
                                <span class="badge bg-info fs-6">Pending Secretary</span>
                            @elseif($offering->status === 'completed')
                                <span class="badge bg-success fs-6">Completed</span>
                            @elseif($offering->status === 'rejected')
                                <span class="badge bg-danger fs-6">Rejected</span>
                            @endif
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Offering Date:</strong><br>
                            {{ $offering->offering_date->format('F d, Y') }}
                        </div>
                        <div class="col-md-6">
                            <strong>Community:</strong><br>
                            {{ $offering->community->name }}
                        </div>
                    </div>
                    @if($offering->service)
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Service Type:</strong><br>
                            <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $offering->service_type)) }}</span>
                        </div>
                        <div class="col-md-6">
                            <strong>Service Date:</strong><br>
                            {{ $offering->service->service_date->format('F d, Y') }}
                        </div>
                    </div>
                    @endif
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Collection Method:</strong><br>
                            {{ ucfirst(str_replace('_', ' ', $offering->collection_method)) }}
                        </div>
                        @if($offering->reference_number)
                        <div class="col-md-6">
                            <strong>Reference Number:</strong><br>
                            {{ $offering->reference_number }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Workflow Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-users me-2"></i>Workflow Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Recorded By (Church Elder):</strong><br>
                            {{ $offering->churchElder->name ?? 'N/A' }}<br>
                            <small class="text-muted">{{ $offering->created_at->format('M d, Y H:i') }}</small>
                        </div>
                        @if($offering->evangelismLeader)
                        <div class="col-md-6">
                            <strong>Confirmed By (Evangelism Leader):</strong><br>
                            {{ $offering->evangelismLeader->name }}<br>
                            <small class="text-muted">{{ $offering->handover_to_evangelism_at->format('M d, Y H:i') }}</small>
                        </div>
                        @endif
                    </div>
                    @if($offering->secretary)
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Finalized By (General Secretary):</strong><br>
                            {{ $offering->secretary->name }}<br>
                            <small class="text-muted">{{ $offering->handover_to_secretary_at->format('M d, Y H:i') }}</small>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Notes -->
            @if($offering->elder_notes || $offering->leader_notes || $offering->secretary_notes)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Notes</h5>
                </div>
                <div class="card-body">
                    @if($offering->elder_notes)
                    <div class="mb-3">
                        <strong>Church Elder Notes:</strong><br>
                        <p class="mb-0">{{ $offering->elder_notes }}</p>
                    </div>
                    @endif
                    @if($offering->leader_notes)
                    <div class="mb-3">
                        <strong>Evangelism Leader Notes:</strong><br>
                        <p class="mb-0">{{ $offering->leader_notes }}</p>
                    </div>
                    @endif
                    @if($offering->secretary_notes)
                    <div class="mb-3">
                        <strong>General Secretary Notes:</strong><br>
                        <p class="mb-0">{{ $offering->secretary_notes }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Rejection Information -->
            @if($offering->status === 'rejected' && $offering->rejection_reason)
            <div class="card border-0 shadow-sm mb-4 border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Rejection Information</h5>
                </div>
                <div class="card-body">
                    <strong>Rejection Reason:</strong><br>
                    <p class="mb-0">{{ $offering->rejection_reason }}</p>
                    @if($offering->rejectedBy)
                    <small class="text-muted">Rejected by: {{ $offering->rejectedBy->name }} on {{ $offering->rejected_at->format('M d, Y H:i') }}</small>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection



