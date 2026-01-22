@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="mt-4"><i class="fas fa-eye me-2"></i>Offering Details</h1>
                <a href="{{ route('evangelism-leader.finance.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Finance
                </a>
            </div>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('evangelism-leader.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('evangelism-leader.finance.index') }}">Finance Management</a></li>
                <li class="breadcrumb-item active">Offering Details</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Offering Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="200">Member</th>
                            <td>{{ $offering->member->full_name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Amount</th>
                            <td><strong class="text-success">TZS {{ number_format($offering->amount, 2) }}</strong></td>
                        </tr>
                        <tr>
                            <th>Offering Type</th>
                            <td><span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $offering->offering_type)) }}</span></td>
                        </tr>
                        <tr>
                            <th>Date</th>
                            <td>{{ $offering->offering_date->format('F d, Y') }}</td>
                        </tr>
                        <tr>
                            <th>Payment Method</th>
                            <td>{{ ucfirst(str_replace('_', ' ', $offering->payment_method)) }}</td>
                        </tr>
                        @if($offering->reference_number)
                        <tr>
                            <th>Reference Number</th>
                            <td>{{ $offering->reference_number }}</td>
                        </tr>
                        @endif
                        @if($offering->notes)
                        <tr>
                            <th>Notes</th>
                            <td>{{ $offering->notes }}</td>
                        </tr>
                        @endif
                        <tr>
                            <th>Recorded By</th>
                            <td>{{ $offering->recorded_by ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($offering->submitted_to_secretary)
                                    <span class="badge bg-warning">Submitted to Secretary</span>
                                @else
                                    <span class="badge bg-secondary">Pending Submission</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Approval Status</th>
                            <td>
                                @if($offering->approval_status === 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($offering->approval_status === 'rejected')
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
                    @if(!$offering->submitted_to_secretary)
                    <a href="{{ route('evangelism-leader.finance.offerings.edit', $offering) }}" class="btn btn-warning w-100 mb-2">
                        <i class="fas fa-edit me-2"></i>Edit Offering
                    </a>
                    <form action="{{ route('evangelism-leader.finance.offerings.submit') }}" method="POST" onsubmit="return confirm('Are you sure you want to send this offering to the secretary?')">
                        @csrf
                        <input type="hidden" name="offering_ids[]" value="{{ $offering->id }}">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-paper-plane me-2"></i>Send to Secretary
                        </button>
                    </form>
                    @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>This offering has already been submitted to the secretary.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

