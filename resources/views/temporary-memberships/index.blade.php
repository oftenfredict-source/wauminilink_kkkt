@extends('layouts.index')

@section('title', 'Temporary Memberships Management')

@section('content')
<div class="container-fluid px-2 px-md-5 py-4">
    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
        <div class="card-header bg-primary d-flex flex-column flex-md-row justify-content-between align-items-center py-4 px-4 border-0">
            <span class="fs-5 fw-bold text-white d-flex align-items-center">
                <i class="fas fa-clock me-2"></i> Temporary Memberships Management
            </span>
        </div>
        <div class="card-body bg-light px-4 py-4">
            <!-- Expired Memberships -->
            @if($expiredMembers->count() > 0)
            <div class="mb-4">
                <h5 class="text-danger mb-3"><i class="fas fa-exclamation-triangle me-2"></i>Expired Memberships ({{ $expiredMembers->count() }})</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Member Name</th>
                                <th>Member ID</th>
                                <th>Expiry Date</th>
                                <th>Days Expired</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($expiredMembers as $member)
                            <tr>
                                <td>{{ $member->full_name }}</td>
                                <td>{{ $member->member_id }}</td>
                                <td>{{ $member->membership_end_date->format('F d, Y') }}</td>
                                <td><span class="badge bg-danger">{{ now()->diffInDays($member->membership_end_date) }} days</span></td>
                                <td>
                                    <a href="{{ route('pastor.temporary-memberships.show', $member) }}" class="btn btn-sm btn-info">View</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- Expiring Soon Memberships -->
            @if($expiringMembers->count() > 0)
            <div class="mb-4">
                <h5 class="text-warning mb-3"><i class="fas fa-exclamation-circle me-2"></i>Expiring Soon ({{ $expiringMembers->count() }})</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Member Name</th>
                                <th>Member ID</th>
                                <th>Expiry Date</th>
                                <th>Days Until Expiry</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($expiringMembers as $member)
                            <tr>
                                <td>{{ $member->full_name }}</td>
                                <td>{{ $member->member_id }}</td>
                                <td>{{ $member->membership_end_date->format('F d, Y') }}</td>
                                <td><span class="badge bg-warning">{{ now()->diffInDays($member->membership_end_date) }} days</span></td>
                                <td>
                                    <a href="{{ route('pastor.temporary-memberships.show', $member) }}" class="btn btn-sm btn-info">View</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            @if($expiredMembers->count() == 0 && $expiringMembers->count() == 0)
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>No temporary memberships expiring soon or expired.
            </div>
            @endif
        </div>
    </div>
</div>
@endsection





