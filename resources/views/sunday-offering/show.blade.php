@extends('layouts.index')

@section('title', 'Offering Session Details')

@section('content')
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0 text-gray-800">Session Details</h1>
            <p class="mb-0">
                <strong>Mtaa:</strong> {{ $session->campus->name }} <span class="mx-2">|</span>
                <strong>Date:</strong> {{ $session->collection_date->format('d M Y') }}
            </p>
        </div>
        <div class="col-md-4 text-end">
            @if($session->status === 'draft')
                <a href="{{ route('sunday-offering.entry', $session->id) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-1"></i> Continue Editing
                </a>
            @else
                <button class="btn btn-outline-secondary" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> Print
                </button>
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Summary Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div
                class="card border-left-{{ $session->status === 'received' ? 'success' : ($session->status === 'submitted' ? 'info' : 'warning') }} shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Status</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ ucfirst($session->status) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Amount -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Collected</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">TZS
                                {{ number_format($session->total_amount) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-coins fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Collector Info -->
        <div class="col-xl-6 col-md-12 mb-4">
            <div class="card shadow h-100 py-2">
                <div class="card-body">
                    <div class="mb-1">
                        <span class="text-xs font-weight-bold text-uppercase text-secondary">Lead Elder (Collector):</span>
                        <span class="fw-bold ms-2">{{ $session->leadElder->name }}</span>
                    </div>
                    <div>
                        <span class="text-xs font-weight-bold text-uppercase text-secondary">Verified By (Gen.
                            Secretary):</span>
                        @if($session->receivedBy)
                            <span class="fw-bold ms-2 text-success">{{ $session->receivedBy->name }}</span>
                            <small class="text-muted">({{ $session->received_at->format('d M Y H:i') }})</small>
                        @else
                            <span class="ms-2 badge bg-secondary">Pending Verification</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Breakdown by Fellowship</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th>Jumuiya</th>
                            <th class="text-end">Unity</th>
                            <th class="text-end">Building</th>
                            <th class="text-end">Pledges</th>
                            <th class="text-end">Other</th>
                            <th class="text-end fw-bold">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($session->items as $item)
                            <tr>
                                <td>{{ $item->community->name }}</td>
                                <td class="text-end">{{ number_format($item->amount_unity) }}</td>
                                <td class="text-end">{{ number_format($item->amount_building) }}</td>
                                <td class="text-end">{{ number_format($item->amount_pledge) }}</td>
                                <td class="text-end">{{ number_format($item->amount_other) }}</td>
                                <td class="text-end fw-bold">
                                    {{ number_format($item->amount_unity + $item->amount_building + $item->amount_pledge + $item->amount_other) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($session->status === 'submitted')
        @if(auth()->user()->isSecretary() || auth()->user()->isAdmin())
            <div class="card shadow border-left-warning">
                <div class="card-header bg-warning text-white py-3">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-user-shield me-2"></i>General Secretary Action</h6>
                </div>
                <div class="card-body">
                    <p class="mb-3">Please verify the physical cash matches the total of <strong>TZS
                            {{ number_format($session->total_amount) }}</strong> before confirming.</p>

                    <form action="{{ route('sunday-offering.receive', $session->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Notes (Optional)</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Any remarks..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-success btn-lg w-100"
                            onclick="return confirm('Confirm receipt of these funds?')">
                            <i class="fas fa-check-double me-2"></i> Confirm Verification & Receipt
                        </button>
                    </form>
                </div>
            </div>
        @endif
    @endif
@endsection