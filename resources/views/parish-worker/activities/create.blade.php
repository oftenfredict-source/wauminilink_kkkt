@extends('layouts.index')

@section('content')
    <div class="container-fluid px-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Record New Activity (Rekodi Shughuli Mpya)
                        </h5>
                        <a href="{{ route('parish-worker.dashboard') }}" class="btn btn-light btn-sm">Back to Dashboard</a>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('parish-worker.activities.store') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="activity_type" class="form-label fw-bold">Activity Type (Aina ya
                                    Shughuli)</label>
                                <select class="form-select @error('activity_type') is-invalid @enderror" id="activity_type"
                                    name="activity_type" required>
                                    <option value="" selected disabled>Select activity type...</option>
                                    <option value="altar_cleanliness" {{ old('activity_type') == 'altar_cleanliness' ? 'selected' : '' }}>Altar Cleanliness (Usafi wa Madhabahu)</option>
                                    <option value="womens_department" {{ old('activity_type') == 'womens_department' ? 'selected' : '' }}>Women's Department Activities (Umoja wa Wanawake)</option>
                                    <option value="sunday_school" {{ old('activity_type') == 'sunday_school' ? 'selected' : '' }}>Sunday School (Sande School)</option>
                                    <option value="holy_communion" {{ old('activity_type') == 'holy_communion' ? 'selected' : '' }}>Holy Communion Materials (Maandalizi ya Meza ya Bwana)</option>
                                    <option value="church_candles" {{ old('activity_type') == 'church_candles' ? 'selected' : '' }}>Church Candles (Kandili za Kanisani)</option>
                                    <option value="other" {{ old('activity_type') == 'other' ? 'selected' : '' }}>Other
                                        Responsibilities</option>
                                </select>
                                @error('activity_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="title" class="form-label fw-bold">Title (Kichwa cha Shughuli)</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                                    name="title" value="{{ old('title') }}"
                                    placeholder="e.g., Altar cleaning for Sunday service" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label fw-bold">Description (Maelezo)</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description"
                                    name="description" rows="4" placeholder="Briefly describe what was done..."
                                    required>{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="activity_date" class="form-label fw-bold">Date (Tarehe)</label>
                                    <input type="date" class="form-control @error('activity_date') is-invalid @enderror"
                                        id="activity_date" name="activity_date"
                                        value="{{ old('activity_date', date('Y-m-d')) }}" required>
                                    @error('activity_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="status" class="form-label fw-bold">Status (Hali)</label>
                                    <select class="form-select @error('status') is-invalid @enderror" id="status"
                                        name="status" required>
                                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>
                                            Completed (Imekamilika)</option>
                                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending
                                            (Inasubiri)</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="notes" class="form-label fw-bold">Additional Notes (Ziada - Optional)</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes"
                                    rows="2"
                                    placeholder="Any special observations or requirements...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary py-2 fw-bold">
                                    <i class="fas fa-save me-2"></i>Save Activity Record
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection