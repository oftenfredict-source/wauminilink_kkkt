@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0"><i class="fas fa-money-bill-wave me-2 text-success"></i>All Offerings</h1>
                            <p class="text-muted mb-0">{{ $community->name }}</p>
                        </div>
                        <a href="{{ route('church-elder.offerings', $community->id) }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Record Offerings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="GET" action="{{ route('church-elder.offerings.all', $community->id) }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="offering_type" class="form-label">Type</label>
                            <select name="offering_type" id="offering_type" class="form-select">
                                <option value="">All Types</option>
                                <option value="general" {{ request('offering_type') === 'general' ? 'selected' : '' }}>General</option>
                                <option value="special" {{ request('offering_type') === 'special' ? 'selected' : '' }}>Special</option>
                                <option value="thanksgiving" {{ request('offering_type') === 'thanksgiving' ? 'selected' : '' }}>Thanksgiving</option>
                                <option value="building_fund" {{ request('offering_type') === 'building_fund' ? 'selected' : '' }}>Building Fund</option>
                                <option value="other" {{ request('offering_type') === 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="approval_status" class="form-label">Status</label>
                            <select name="approval_status" id="approval_status" class="form-select">
                                <option value="">All Status</option>
                                <option value="approved" {{ request('approval_status') === 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="pending" {{ request('approval_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="rejected" {{ request('approval_status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="member_id" class="form-label">Member</label>
                            <select name="member_id" id="member_id" class="form-select">
                                <option value="">All Members</option>
                                @foreach($members as $member)
                                    <option value="{{ $member->id }}" {{ request('member_id') == $member->id ? 'selected' : '' }}>
                                        {{ $member->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-2"></i>Apply Filters
                            </button>
                            <a href="{{ route('church-elder.offerings.all', $community->id) }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Clear Filters
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Offerings Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    @if($offerings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Member</th>
                                        <th>Amount (TZS)</th>
                                        <th>Type</th>
                                        <th>Payment Method</th>
                                        <th>Status</th>
                                        <th>Recorded By</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($offerings as $offering)
                                    <tr>
                                        <td>{{ $offering->offering_date->format('M d, Y') }}</td>
                                        <td>
                                            @if($offering->member)
                                                {{ $offering->member->full_name }}
                                            @else
                                                <span class="text-muted">Anonymous</span>
                                            @endif
                                        </td>
                                        <td><strong>{{ number_format($offering->amount, 2) }}</strong></td>
                                        <td>
                                            <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $offering->offering_type)) }}</span>
                                        </td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $offering->payment_method ?? 'N/A')) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $offering->approval_status === 'approved' ? 'success' : ($offering->approval_status === 'pending' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($offering->approval_status) }}
                                            </span>
                                        </td>
                                        <td>{{ $offering->recorded_by ?? 'N/A' }}</td>
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
                            <p class="text-muted">No offerings found.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection













