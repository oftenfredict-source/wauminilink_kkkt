@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mt-4"><i class="fas fa-users me-2"></i>{{ $community->name }}</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('campuses.communities.edit', [$campus, $community]) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="{{ route('campuses.show', $campus) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Campus
            </a>
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

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-info-circle me-2"></i>Community Details
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="30%">Campus:</th>
                            <td>
                                <a href="{{ route('campuses.show', $campus) }}">{{ $campus->name }}</a>
                            </td>
                        </tr>
                        @if($community->description)
                        <tr>
                            <th>Description:</th>
                            <td>{{ $community->description }}</td>
                        </tr>
                        @endif
                        @if($community->address)
                        <tr>
                            <th>Address:</th>
                            <td>{{ $community->address }}</td>
                        </tr>
                        @endif
                        @if($community->region || $community->district || $community->ward)
                        <tr>
                            <th>Location:</th>
                            <td>
                                {{ implode(', ', array_filter([$community->ward, $community->district, $community->region])) }}
                            </td>
                        </tr>
                        @endif
                        @if($community->phone_number)
                        <tr>
                            <th>Phone:</th>
                            <td>{{ $community->phone_number }}</td>
                        </tr>
                        @endif
                        @if($community->email)
                        <tr>
                            <th>Email:</th>
                            <td>{{ $community->email }}</td>
                        </tr>
                        @endif
                        <tr>
                            <th>Status:</th>
                            <td>
                                @if($community->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <i class="fas fa-users me-2"></i>Statistics
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Members:</strong>
                        <span class="badge bg-primary float-end">{{ $memberCount }}</span>
                    </div>
                </div>
            </div>

            {{-- Church Elder Assignment --}}
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <i class="fas fa-user-tie me-2"></i>{{ autoTranslate('Church Elder') }}
                </div>
                <div class="card-body">
                    @if($community->churchElder && $community->churchElder->member)
                        <div class="mb-3">
                            <strong>{{ autoTranslate('Current Church Elder') }}:</strong>
                            <div class="mt-2 p-2 bg-light rounded">
                                <i class="fas fa-user-tie text-warning me-2"></i>
                                <strong>{{ $community->churchElder->member->full_name }}</strong>
                                <br>
                                <small class="text-muted">
                                    {{ autoTranslate('Member ID') }}: {{ $community->churchElder->member->member_id }}
                                    @if($community->churchElder->member->phone_number)
                                        | {{ $community->churchElder->member->phone_number }}
                                    @endif
                                </small>
                            </div>
                        </div>
                    @else
                        <p class="text-muted mb-3">{{ autoTranslate('No church elder assigned to this community yet.') }}</p>
                    @endif
                    
                    <form action="{{ route('campuses.communities.assign-church-elder', [$campus, $community]) }}" method="POST" class="mt-3">
                        @csrf
                        <div class="mb-3">
                            <label for="church_elder_id" class="form-label">{{ autoTranslate('Select Church Elder') }}</label>
                            <select name="church_elder_id" id="church_elder_id" class="form-select">
                                <option value="">{{ autoTranslate('-- Remove Assignment --') }}</option>
                                @foreach($availableChurchElders as $elder)
                                    <option value="{{ $elder->id }}" {{ $community->church_elder_id == $elder->id ? 'selected' : '' }}>
                                        {{ $elder->member->full_name ?? 'N/A' }} ({{ $elder->member->member_id ?? 'N/A' }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">{{ autoTranslate('Select a church elder from this campus. If no elders are available, create one first.') }}</small>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-warning btn-sm">
                                <i class="fas fa-save me-2"></i>{{ autoTranslate('Save Assignment') }}
                            </button>
                            @if($availableChurchElders->count() == 0)
                            <a href="{{ route('leaders.create') }}?campus_id={{ $campus->id }}&position=elder" class="btn btn-sm btn-info">
                                <i class="fas fa-plus me-2"></i>{{ autoTranslate('Create Church Elder') }}
                            </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Members Section --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-users me-2"></i>Community Members
                        <span class="badge bg-white text-dark ms-2 fw-bold">{{ $memberCount }}</span>
                    </div>
                </div>
                <div class="card-body">
                    @if($memberCount > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Member ID</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($community->members as $member)
                                        <tr>
                                            <td><strong>{{ $member->full_name }}</strong></td>
                                            <td><span class="badge bg-secondary">{{ $member->member_id }}</span></td>
                                            <td>{{ $member->phone_number }}</td>
                                            <td>{{ $member->email ?? '—' }}</td>
                                            <td>
                                                <a href="{{ route('members.view') }}?search={{ $member->member_id }}" class="btn btn-sm btn-info" title="View Member">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-users fa-2x mb-2 d-block"></i>
                            No members assigned to this community yet.
                        </div>
                    @endif
                </div>
            </div>

            {{-- Assign Members Section --}}
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-user-plus me-2"></i>Assign Members to Community
                </div>
                <div class="card-body">
                    <form action="{{ route('campuses.communities.assign-members', [$campus, $community]) }}" method="POST" id="assignMembersForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Select Members from {{ $campus->name }}</label>
                            <select name="member_ids[]" id="member_ids" class="form-select select2" multiple style="width: 100%;" required>
                                @foreach($availableMembers as $member)
                                    <option value="{{ $member->id }}" {{ $member->community_id == $community->id ? 'selected' : '' }}>
                                        {{ $member->full_name }} ({{ $member->member_id }}) - {{ $member->phone_number }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Select members to assign to this community. You can select multiple members.</small>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Assign Members
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Select2 if available
        if ($.fn.select2) {
            $('#member_ids').select2({
                placeholder: 'Select members...',
                allowClear: true,
                width: '100%'
            });
        }
    });
    
    // Show success/error messages with SweetAlert
    @if(session('success'))
        showSuccess('Success!', '{{ session('success') }}');
    @endif
    
    @if(session('error'))
        showError('Error!', '{{ session('error') }}');
    @endif
});
</script>
@endsection

