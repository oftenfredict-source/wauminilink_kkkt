@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0"><i class="fas fa-plus-circle me-2 text-success"></i>Record Branch Offering</h1>
                            <p class="text-muted mb-0">{{ $campus->name }} - Sent to General Secretary</p>
                        </div>
                        <a href="{{ route('evangelism-leader.branch-offerings.index') }}" class="btn btn-outline-primary">
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
                    <form action="{{ route('evangelism-leader.branch-offerings.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="service_id" class="form-label">Link to Service (Optional)</label>
                                <select class="form-select @error('service_id') is-invalid @enderror" id="service_id" name="service_id">
                                    <option value="">Select Service...</option>
                                    @foreach($recentServices as $svc)
                                        <option value="{{ $svc->id }}" {{ (old('service_id') == $svc->id || (isset($service) && $service->id == $svc->id)) ? 'selected' : '' }}>
                                            {{ $svc->service_date->format('M d, Y') }} - {{ $svc->theme ?? 'No theme' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('service_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">You can link this offering to a branch Sunday service</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="offering_date" class="form-label">Offering Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('offering_date') is-invalid @enderror" 
                                       id="offering_date" name="offering_date" value="{{ old('offering_date', isset($service) ? $service->service_date->format('Y-m-d') : date('Y-m-d')) }}" required>
                                @error('offering_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="amount" class="form-label">Amount (TZS) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0.01" class="form-control @error('amount') is-invalid @enderror" 
                                       id="amount" name="amount" value="{{ old('amount') }}" required>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="collection_method" class="form-label">Collection Method <span class="text-danger">*</span></label>
                                <select class="form-select @error('collection_method') is-invalid @enderror" id="collection_method" name="collection_method" required>
                                    <option value="cash" {{ old('collection_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="mobile_money" {{ old('collection_method') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                                    <option value="bank_transfer" {{ old('collection_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                </select>
                                @error('collection_method')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="reference_number" class="form-label">Reference Number</label>
                            <input type="text" class="form-control @error('reference_number') is-invalid @enderror" 
                                   id="reference_number" name="reference_number" value="{{ old('reference_number') }}" 
                                   placeholder="For mobile money or bank transfer">
                            @error('reference_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Required if collection method is Mobile Money or Bank Transfer</small>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="leader_notes" class="form-label">Leader Notes (Internal)</label>
                            <textarea class="form-control @error('leader_notes') is-invalid @enderror" 
                                      id="leader_notes" name="leader_notes" rows="2">{{ old('leader_notes') }}</textarea>
                            @error('leader_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">These notes are only visible to you</small>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Note:</strong> This offering will be sent directly to the General Secretary for approval. 
                            It will bypass the community elder workflow since this is a branch-level Sunday service offering.
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('evangelism-leader.branch-offerings.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Record Offering
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



