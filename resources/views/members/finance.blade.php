@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm dashboard-header" style="background:white;">
                <div class="card-body py-2 px-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center border border-danger border-2" style="width:48px; height:48px; background:rgba(148,0,0,.1);">
                                <i class="fas fa-wallet text-danger"></i>
                            </div>
                            <div class="lh-sm">
                                <h5 class="mb-0 fw-semibold text-dark">My Finance</h5>
                                <small class="text-muted">Financial contributions and records</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Summary -->
    <!-- Financial Summary -->
    <div class="row mb-4">
        <!-- Ahadi ya Bwana (Pledges) -->
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm bg-warning text-white h-100">
                <div class="card-body text-center p-3">
                    <i class="fas fa-handshake fa-3x mb-2"></i>
                    <h6 class="mb-2">Ahadi ya Bwana</h6>
                    <h4 class="mb-0">TZS {{ number_format($financialSummary['total_pledges'], 0) }}</h4>
                    <small>Paid: {{ number_format($financialSummary['total_pledge_payments'], 0) }}</small>
                </div>
            </div>
        </div>

        <!-- Sadaka ya Jengo -->
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm text-white h-100" style="background: linear-gradient(135deg, #fd9644 0%, #feb47b 100%);">
                <div class="card-body text-center p-3">
                    <i class="fas fa-church fa-3x mb-2"></i>
                    <h6 class="mb-2">Sadaka ya Jengo</h6>
                    <h4 class="mb-0">TZS {{ number_format($financialSummary['total_jengo'], 0) }}</h4>
                    <small>Month: {{ number_format($financialSummary['monthly_jengo'], 0) }}</small>
                </div>
            </div>
        </div>

        <!-- Sadaka ya Umoja -->
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm text-white h-100" style="background: linear-gradient(135deg, #c0392b 0%, #e74c3c 100%);">
                <div class="card-body text-center p-3">
                    <i class="fas fa-users fa-3x mb-2"></i>
                    <h6 class="mb-2">Sadaka ya Umoja</h6>
                    <h4 class="mb-0">TZS {{ number_format($financialSummary['total_umoja'], 0) }}</h4>
                    <small>Month: {{ number_format($financialSummary['monthly_umoja'], 0) }}</small>
                </div>
            </div>
        </div>
    </div>


    <!-- Pledges Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-white">
                    <h6 class="mb-0"><i class="fas fa-file-contract me-2"></i>Ahadi ya Bwana</h6>
                </div>
                <div class="card-body">
                    @if(isset($pledges) && $pledges->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Pledge Amount</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Amount Paid</th>
                                        <th>Remaining Amount</th>
                                        <th>Status</th>
                                        <th>Payment History</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pledges as $pledge)
                                        <tr>
                                            <td><strong>TZS {{ number_format($pledge->pledge_amount, 2) }}</strong></td>
                                            <td>{{ $pledge->pledge_date ? $pledge->pledge_date->format('M d, Y') : 'N/A' }}</td>
                                            <td>{{ $pledge->due_date ? $pledge->due_date->format('M d, Y') : 'No due date' }}</td>
                                            <td>TZS {{ number_format($pledge->amount_paid, 2) }}</td>
                                            <td><strong class="text-danger">TZS {{ number_format($pledge->remaining_amount, 2) }}</strong></td>
                                            <td>
                                                @if($pledge->status == 'completed')
                                                    <span class="badge bg-success">Completed</span>
                                                @elseif($pledge->status == 'active')
                                                    <span class="badge bg-danger">Active</span>
                                                @elseif($pledge->status == 'overdue')
                                                    <span class="badge bg-dark">Overdue</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($pledge->status) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($pledge->payments && $pledge->payments->count() > 0)
                                                    <button class="btn btn-sm btn-outline-danger" type="button" data-bs-toggle="collapse" data-bs-target="#payments{{ $pledge->id }}" aria-expanded="false">
                                                        <i class="fas fa-list me-1"></i>View Payments ({{ $pledge->payments->count() }})
                                                    </button>
                                                @else
                                                    <span class="text-muted">No payments yet</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @if($pledge->payments && $pledge->payments->count() > 0)
                                            <tr>
                                                <td colspan="7" class="p-0">
                                                    <div class="collapse" id="payments{{ $pledge->id }}">
                                                        <div class="card card-body border-0 bg-light">
                                                            <h6 class="mb-3">Payment History:</h6>
                                                            <div class="table-responsive">
                                                                <table class="table table-sm table-bordered">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Payment Date</th>
                                                                            <th>Amount</th>
                                                                            <th>Payment Method</th>
                                                                            <th>Reference Number</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach($pledge->payments as $payment)
                                                                            <tr>
                                                                                <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                                                                <td><strong>TZS {{ number_format($payment->amount, 2) }}</strong></td>
                                                                                <td>{{ ucfirst($payment->payment_method ?? 'N/A') }}</td>
                                                                                <td>{{ $payment->reference_number ?? 'N/A' }}</td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center py-4">No pledges recorded</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Offerings Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="fas fa-donate me-2"></i>My Offerings</h6>
                </div>
                <div class="card-body">
                    @if(isset($allOfferings) && $allOfferings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Amount</th>
                                        <th>Date</th>
                                        <th>Day</th>
                                        <th>Purpose/Type</th>
                                        <th>Service Type</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($allOfferings as $offering)
                                        <tr>
                                            <td><strong>TZS {{ number_format($offering->amount, 2) }}</strong></td>
                                            <td>{{ $offering->offering_date->format('M d, Y') }}</td>
                                            <td>{{ $offering->offering_date->format('l') }}</td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    {{ ucfirst(str_replace('_', ' ', $offering->offering_type ?? 'General')) }}
                                                </span>
                                            </td>
                                            <td>{{ $offering->service_type ? ucfirst(str_replace('_', ' ', $offering->service_type)) : 'N/A' }}</td>
                                            <td>
                                                @if($offering->approval_status == 'approved')
                                                    <span class="badge bg-success">Approved</span>
                                                @elseif($offering->approval_status == 'pending')
                                                    <span class="badge bg-warning">Pending</span>
                                                @else
                                                    <span class="badge bg-danger">Rejected</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center py-4">No offerings recorded</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="row">
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h6 class="mb-0"><i class="fas fa-list me-2"></i>Recent Tithes</h6>
                </div>
                <div class="card-body">
                    @if($financialSummary['recent_tithes']->count() > 0)
                        <div class="list-group">
                            @foreach($financialSummary['recent_tithes'] as $tithe)
                                <div class="list-group-item border-0 mb-2 shadow-sm">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <strong>TZS {{ number_format($tithe->amount, 2) }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $tithe->tithe_date->format('M d, Y') }}</small>
                                        </div>
                                        <span class="badge bg-success">Approved</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center">No tithes recorded</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0"><i class="fas fa-list me-2"></i>Recent Donations</h6>
                </div>
                <div class="card-body">
                    @if($financialSummary['recent_donations']->count() > 0)
                        <div class="list-group">
                            @foreach($financialSummary['recent_donations'] as $donation)
                                <div class="list-group-item border-0 mb-2 shadow-sm">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <strong>TZS {{ number_format($donation->amount, 2) }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $donation->donation_date->format('M d, Y') }}</small>
                                        </div>
                                        <span class="badge bg-success">Approved</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center">No donations recorded</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

