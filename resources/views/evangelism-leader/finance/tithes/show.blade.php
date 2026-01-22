@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="mt-4"><i class="fas fa-eye me-2"></i>Tithe Details</h1>
                <a href="{{ route('evangelism-leader.finance.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Finance
                </a>
            </div>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('evangelism-leader.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('evangelism-leader.finance.index') }}">Finance Management</a></li>
                <li class="breadcrumb-item active">Tithe Details</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Tithe Information</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Note:</strong> This is an aggregate tithe record representing the total tithe collected from all members.
                    </div>
                    <table class="table table-bordered">
                        <tr>
                            <th width="200">Total Amount</th>
                            <td><strong class="text-success">TZS {{ number_format($tithe->amount, 2) }}</strong></td>
                        </tr>
                        <tr>
                            <th>Date</th>
                            <td>{{ $tithe->tithe_date->format('F d, Y') }}</td>
                        </tr>
                        <tr>
                            <th>Payment Method</th>
                            <td>{{ ucfirst(str_replace('_', ' ', $tithe->payment_method)) }}</td>
                        </tr>
                        @if($tithe->reference_number)
                        <tr>
                            <th>Reference Number</th>
                            <td>{{ $tithe->reference_number }}</td>
                        </tr>
                        @endif
                        @if($tithe->notes)
                        <tr>
                            <th>Notes</th>
                            <td>{{ $tithe->notes }}</td>
                        </tr>
                        @endif
                        <tr>
                            <th>Recorded By</th>
                            <td>{{ $tithe->recorded_by ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($tithe->submitted_to_secretary)
                                    <span class="badge bg-warning">Submitted to Secretary</span>
                                @else
                                    <span class="badge bg-secondary">Pending Submission</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Approval Status</th>
                            <td>
                                @if($tithe->approval_status === 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($tithe->approval_status === 'rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                @else
                                    <span class="badge bg-warning">Pending</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-cog me-2"></i>Actions</h5>
                </div>
                <div class="card-body">
                    @if(!$tithe->submitted_to_secretary)
                    <a href="{{ route('evangelism-leader.finance.tithes.edit', $tithe) }}" class="btn btn-warning w-100 mb-2">
                        <i class="fas fa-edit me-2"></i>Edit Tithe
                    </a>
                    <form action="{{ route('evangelism-leader.finance.tithes.submit') }}" method="POST" onsubmit="return confirm('Are you sure you want to send this tithe to the secretary?')">
                        @csrf
                        <input type="hidden" name="tithe_ids[]" value="{{ $tithe->id }}">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-paper-plane me-2"></i>Send to Secretary
                        </button>
                    </form>
                    @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>This tithe has already been submitted to the secretary.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

