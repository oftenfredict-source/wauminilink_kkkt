@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mt-4"><i class="fas fa-plus-circle me-2"></i>Create New Campus</h1>
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

            <form action="{{ route('campuses.store') }}" method="POST">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="campus_type" class="form-label">Campus Type <span class="text-danger">*</span></label>
                        <select class="form-select" id="campus_type" name="campus_type" required>
                            <option value="">Select Type</option>
                            <option value="main" {{ old('campus_type') == 'main' ? 'selected' : '' }}>Main Campus</option>
                            <option value="sub" {{ old('campus_type') == 'sub' ? 'selected' : '' }}>Sub Campus</option>
                        </select>
                        <small class="form-text text-muted">Main campus is the primary campus. Sub campuses belong to a main campus.</small>
                    </div>
                    <div class="col-md-6" id="parent_campus_field" style="display: none;">
                        <label for="parent_id" class="form-label">Parent Campus <span class="text-danger">*</span></label>
                        <select class="form-select" id="parent_id" name="parent_id">
                            <option value="">Select Main Campus</option>
                            @foreach($mainCampuses as $mainCampus)
                                <option value="{{ $mainCampus->id }}" {{ old('parent_id') == $mainCampus->id ? 'selected' : '' }}>
                                    {{ $mainCampus->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="name" class="form-label">Campus Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" class="form-control" id="address" name="address" value="{{ old('address') }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="region" class="form-label">Region</label>
                        <input type="text" class="form-control" id="region" name="region" value="{{ old('region') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="district" class="form-label">District</label>
                        <input type="text" class="form-control" id="district" name="district" value="{{ old('district') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="ward" class="form-label">Ward</label>
                        <input type="text" class="form-control" id="ward" name="ward" value="{{ old('ward') }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="phone_number" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="phone_number" name="phone_number" value="{{ old('phone_number') }}">
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}">
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('campuses.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Create Campus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const campusType = document.getElementById('campus_type');
        const parentField = document.getElementById('parent_campus_field');
        const parentSelect = document.getElementById('parent_id');

        campusType.addEventListener('change', function() {
            if (this.value === 'sub') {
                parentField.style.display = 'block';
                parentSelect.required = true;
            } else {
                parentField.style.display = 'none';
                parentSelect.required = false;
                parentSelect.value = '';
            }
        });

        // Trigger on page load if there's an old value
        if (campusType.value === 'sub') {
            parentField.style.display = 'block';
            parentSelect.required = true;
        }
    });
</script>
@endsection














