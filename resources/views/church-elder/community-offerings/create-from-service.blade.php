@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0"><i class="fas fa-money-bill-wave me-2 text-success"></i>Record Mid-Week Service Offering</h1>
                            <p class="text-muted mb-0">{{ $community->name }}</p>
                        </div>
                        <a href="{{ route('church-elder.services', $community->id) }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Services
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($existingOffering)
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                An offering has already been recorded for this service. 
                                <a href="{{ route('church-elder.community-offerings.show', $existingOffering->id) }}" class="alert-link">View existing offering</a>
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Service Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Service Type:</strong><br>
                            <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $service->service_type)) }}</span>
                        </div>
                        <div class="col-md-6">
                            <strong>Service Date:</strong><br>
                            {{ $service->service_date->format('F d, Y') }}
                        </div>
                    </div>
                    @if($service->theme)
                    <div class="row mb-3">
                        <div class="col-12">
                            <strong>Theme:</strong><br>
                            {{ $service->theme }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Offering Details</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('church-elder.community-offerings.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="community_id" value="{{ $community->id }}">
                        <input type="hidden" name="service_id" value="{{ $service->id }}">
                        <input type="hidden" name="service_type" value="{{ $service->service_type }}">
                        <input type="hidden" name="offering_date" value="{{ $service->service_date->format('Y-m-d') }}">

                        <div class="mb-3">
                            <label for="amount" class="form-label">Offering Amount (TZS) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" class="form-control @error('amount') is-invalid @enderror" 
                                   id="amount" name="amount" value="{{ old('amount', $service->offerings_amount ?? '') }}" required>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="collection_method" class="form-label">Collection Method <span class="text-danger">*</span></label>
                            <select class="form-select @error('collection_method') is-invalid @enderror" id="collection_method" name="collection_method" required>
                                <option value="">Select method...</option>
                                <option value="cash" {{ old('collection_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="mobile_money" {{ old('collection_method') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                                <option value="bank_transfer" {{ old('collection_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            </select>
                            @error('collection_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3" id="reference_number_group" style="display: none;">
                            <label for="reference_number" class="form-label">Reference Number</label>
                            <input type="text" class="form-control @error('reference_number') is-invalid @enderror" 
                                   id="reference_number" name="reference_number" value="{{ old('reference_number') }}" 
                                   placeholder="Enter transaction reference">
                            @error('reference_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="elder_notes" class="form-label">Notes (Optional)</label>
                            <textarea class="form-control @error('elder_notes') is-invalid @enderror" 
                                      id="elder_notes" name="elder_notes" rows="3" 
                                      placeholder="Any additional notes about this offering...">{{ old('elder_notes') }}</textarea>
                            @error('elder_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('church-elder.services', $community->id) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-paper-plane me-1"></i> Submit to Evangelism Leader
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('collection_method').addEventListener('change', function() {
    const referenceGroup = document.getElementById('reference_number_group');
    if (this.value === 'mobile_money' || this.value === 'bank_transfer') {
        referenceGroup.style.display = 'block';
        document.getElementById('reference_number').setAttribute('required', 'required');
    } else {
        referenceGroup.style.display = 'none';
        document.getElementById('reference_number').removeAttribute('required');
    }
});
</script>
@endsection

