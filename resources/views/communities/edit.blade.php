@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mt-4"><i class="fas fa-edit me-2"></i>Edit Community</h1>
        <a href="{{ route('campuses.show', $campus) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Campus
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-users me-2"></i>Community Information
            <span class="badge bg-white text-dark ms-2 fw-bold">Campus: {{ $campus->name }}</span>
        </div>
        <div class="card-body">
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

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('campuses.communities.update', [$campus, $community]) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="name" class="form-label">Community Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $community->name) }}" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $community->description) }}</textarea>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" class="form-control" id="address" name="address" value="{{ old('address', $community->address) }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="region" class="form-label">Region</label>
                        <input type="text" class="form-control" id="region" name="region" value="{{ old('region', $community->region) }}">
                    </div>
                    <div class="col-md-4">
                        <label for="district" class="form-label">District</label>
                        <input type="text" class="form-control" id="district" name="district" value="{{ old('district', $community->district) }}">
                    </div>
                    <div class="col-md-4">
                        <label for="ward" class="form-label">Ward</label>
                        <input type="text" class="form-control" id="ward" name="ward" value="{{ old('ward', $community->ward) }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="phone_number" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="phone_number" name="phone_number" value="{{ old('phone_number', $community->phone_number) }}">
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $community->email) }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $community->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active Community
                            </label>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('campuses.show', $campus) }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Community
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Church Elder Assignment --}}
    <div class="card mb-4">
        <div class="card-header bg-warning text-dark">
            <i class="fas fa-user-tie me-2"></i>{{ autoTranslate('Church Elder Assignment') }}
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
                        </small>
                    </div>
                </div>
            @else
                <p class="text-muted mb-3">{{ autoTranslate('No church elder assigned to this community yet.') }}</p>
            @endif
            
            <form action="{{ route('campuses.communities.assign-church-elder', [$campus, $community]) }}" method="POST">
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
                    <small class="form-text text-muted">{{ autoTranslate('Select a church elder from this campus.') }}</small>
                </div>
                <div class="d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save me-2"></i>{{ autoTranslate('Save Assignment') }}
                    </button>
                    @if($availableChurchElders->count() == 0)
                    <a href="{{ route('leaders.create') }}?campus_id={{ $campus->id }}&position=elder" class="btn btn-info">
                        <i class="fas fa-plus me-2"></i>{{ autoTranslate('Create Church Elder') }}
                    </a>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
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

