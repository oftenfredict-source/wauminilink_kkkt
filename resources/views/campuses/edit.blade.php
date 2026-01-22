@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mt-4"><i class="fas fa-edit me-2"></i>Edit Campus</h1>
        <a href="{{ route('campuses.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to List
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-building me-2"></i>Campus Information
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('campuses.update', $campus) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label">Campus Type</label>
                        <input type="text" class="form-control" value="{{ $campus->is_main_campus ? 'Main Campus' : 'Sub Campus' }}" disabled>
                        <small class="form-text text-muted">Campus type cannot be changed after creation.</small>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="name" class="form-label">Campus Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $campus->name) }}" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $campus->description) }}</textarea>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" class="form-control" id="address" name="address" value="{{ old('address', $campus->address) }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="region" class="form-label">Region</label>
                        <input type="text" class="form-control" id="region" name="region" value="{{ old('region', $campus->region) }}">
                    </div>
                    <div class="col-md-4">
                        <label for="district" class="form-label">District</label>
                        <input type="text" class="form-control" id="district" name="district" value="{{ old('district', $campus->district) }}">
                    </div>
                    <div class="col-md-4">
                        <label for="ward" class="form-label">Ward</label>
                        <input type="text" class="form-control" id="ward" name="ward" value="{{ old('ward', $campus->ward) }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="phone_number" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="phone_number" name="phone_number" value="{{ old('phone_number', $campus->phone_number) }}">
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $campus->email) }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $campus->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active Campus
                            </label>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('campuses.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Campus
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Evangelism Leader Assignment --}}
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-cross me-2"></i>{{ autoTranslate('Evangelism Leader Assignment') }}
        </div>
        <div class="card-body">
            @if($campus->evangelismLeader && $campus->evangelismLeader->member)
                <div class="mb-3">
                    <strong>{{ autoTranslate('Current Evangelism Leader') }}:</strong>
                    <div class="mt-2 p-2 bg-light rounded">
                        <i class="fas fa-user-tie text-primary me-2"></i>
                        <strong>{{ $campus->evangelismLeader->member->full_name }}</strong>
                        <br>
                        <small class="text-muted">
                            {{ autoTranslate('Member ID') }}: {{ $campus->evangelismLeader->member->member_id }}
                        </small>
                    </div>
                </div>
            @else
                <p class="text-muted mb-3">{{ autoTranslate('No evangelism leader assigned to this campus yet.') }}</p>
            @endif
            
            <form action="{{ route('campuses.assign-evangelism-leader', $campus) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="evangelism_leader_id" class="form-label">{{ autoTranslate('Select Evangelism Leader') }}</label>
                    <select name="evangelism_leader_id" id="evangelism_leader_id" class="form-select">
                        <option value="">{{ autoTranslate('-- Remove Assignment --') }}</option>
                        @foreach($availableEvangelismLeaders as $leader)
                            <option value="{{ $leader->id }}" {{ $campus->evangelism_leader_id == $leader->id ? 'selected' : '' }}>
                                {{ $leader->member->full_name ?? 'N/A' }} ({{ $leader->member->member_id ?? 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">{{ autoTranslate('Select an evangelism leader from this campus.') }}</small>
                </div>
                <div class="d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>{{ autoTranslate('Save Assignment') }}
                    </button>
                    @if($availableEvangelismLeaders->count() == 0)
                    <a href="{{ route('leaders.create') }}?campus_id={{ $campus->id }}&position=evangelism_leader" class="btn btn-warning">
                        <i class="fas fa-plus me-2"></i>{{ autoTranslate('Create Evangelism Leader') }}
                    </a>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
@endsection






