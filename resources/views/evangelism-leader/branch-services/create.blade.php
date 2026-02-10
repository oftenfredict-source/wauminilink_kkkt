@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0"><i class="fas fa-plus-circle me-2 text-primary"></i>Create Branch Sunday Service</h1>
                            <p class="text-muted mb-0">{{ $campus->name }} - All Communities Together</p>
                        </div>
                        <a href="{{ route('evangelism-leader.branch-services.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('evangelism-leader.branch-services.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="service_date" class="form-label">Service Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('service_date') is-invalid @enderror" 
                                       id="service_date" name="service_date" value="{{ old('service_date') }}" required>
                                @error('service_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="theme" class="form-label">Theme</label>
                                <input type="text" class="form-control @error('theme') is-invalid @enderror" 
                                       id="theme" name="theme" value="{{ old('theme') }}">
                                @error('theme')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="preacher" class="form-label">Preacher</label>
                                <input type="text" class="form-control @error('preacher') is-invalid @enderror" 
                                       id="preacher" name="preacher" value="{{ old('preacher') }}">
                                @error('preacher')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="venue" class="form-label">Venue</label>
                                <input type="text" class="form-control @error('venue') is-invalid @enderror" 
                                       id="venue" name="venue" value="{{ old('venue') }}">
                                @error('venue')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="start_time" class="form-label">Start Time</label>
                                <input type="time" class="form-control @error('start_time') is-invalid @enderror" 
                                       id="start_time" name="start_time" value="{{ old('start_time') }}">
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="end_time" class="form-label">End Time</label>
                                <input type="time" class="form-control @error('end_time') is-invalid @enderror" 
                                       id="end_time" name="end_time" value="{{ old('end_time') }}">
                                @error('end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="attendance_count" class="form-label">Attendance Count</label>
                                <input type="number" class="form-control @error('attendance_count') is-invalid @enderror" 
                                       id="attendance_count" name="attendance_count" value="{{ old('attendance_count', 0) }}" min="0">
                                @error('attendance_count')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="guests_count" class="form-label">Guests Count</label>
                                <input type="number" class="form-control @error('guests_count') is-invalid @enderror" 
                                       id="guests_count" name="guests_count" value="{{ old('guests_count', 0) }}" min="0">
                                @error('guests_count')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="scripture_readings" class="form-label">Scripture Readings</label>
                            <textarea class="form-control @error('scripture_readings') is-invalid @enderror" 
                                      id="scripture_readings" name="scripture_readings" rows="2">{{ old('scripture_readings') }}</textarea>
                            @error('scripture_readings')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('evangelism-leader.branch-services.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Create Service
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection








