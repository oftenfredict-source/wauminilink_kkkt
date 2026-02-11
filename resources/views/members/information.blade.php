@extends('layouts.index')

@section('content')
    <div class="container-fluid px-4">
        <div class="row mb-3">
            <div class="col-12">
                <div class="card border-0 shadow-sm dashboard-header" style="background:white;">
                    <div class="card-body py-2 px-3">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center border border-danger border-2"
                                    style="width:48px; height:48px; background:rgba(148,0,0,.1);">
                                    <i class="fas fa-user-circle text-danger"></i>
                                </div>
                                <div class="lh-sm">
                                    <h5 class="mb-0 fw-semibold text-dark">My Information</h5>
                                    <small class="text-muted">Personal details and profile</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="fas fa-id-card me-2"></i>Personal Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Member ID:</strong>
                                <p class="text-muted">{{ $member->member_id }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Full Name:</strong>
                                <p class="text-muted">{{ $member->full_name }}</p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Email:</strong>
                                <p class="text-muted">{{ $member->email ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Phone Number:</strong>
                                <p class="text-muted">{{ $member->phone_number }}</p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Date of Birth:</strong>
                                <p class="text-muted">
                                    {{ $member->date_of_birth ? \Carbon\Carbon::parse($member->date_of_birth)->format('M d, Y') : 'N/A' }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <strong>Gender:</strong>
                                <p class="text-muted">{{ ucfirst($member->gender ?? 'N/A') }}</p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Membership Type:</strong>
                                <p class="text-muted">{{ ucfirst($member->membership_type ?? 'N/A') }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Member Type:</strong>
                                <p class="text-muted">{{ ucfirst($member->member_type ?? 'N/A') }}</p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Profession:</strong>
                                <p class="text-muted">{{ $member->profession ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Education Level:</strong>
                                <p class="text-muted">
                                    {{ ucfirst(str_replace('_', ' ', $member->education_level ?? 'N/A')) }}
                                </p>
                            </div>
                        </div>
                        <hr>
                        <h6 class="mb-3">Address Information</h6>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <strong>Address:</strong>
                                <p class="text-muted">{{ $member->address ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong>Region:</strong>
                                <p class="text-muted">{{ $member->region ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-4">
                                <strong>District:</strong>
                                <p class="text-muted">{{ $member->district ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-4">
                                <strong>Ward:</strong>
                                <p class="text-muted">{{ $member->ward ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('member.change-password') }}" class="btn btn-outline-danger">
                                <i class="fas fa-key me-2"></i>Change Password
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection