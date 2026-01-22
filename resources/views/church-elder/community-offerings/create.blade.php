@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0"><i class="fas fa-money-bill-wave me-2 text-success"></i>Record Community Offering</h1>
                            <p class="text-muted mb-0">Create a new community offering</p>
                        </div>
                        @if(isset($community))
                            <a href="{{ route('church-elder.community-offerings.index', $community->id) }}" class="btn btn-outline-primary">
                        @elseif($communities->isNotEmpty())
                            <a href="{{ route('church-elder.community-offerings.index', $communities->first()->id) }}" class="btn btn-outline-primary">
                        @else
                            <a href="{{ route('church-elder.dashboard') }}" class="btn btn-outline-primary">
                        @endif
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Offering Details</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('church-elder.community-offerings.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="community_id" class="form-label">Community <span class="text-danger">*</span></label>
                            <select class="form-select @error('community_id') is-invalid @enderror" id="community_id" name="community_id" required>
                                <option value="">Select community...</option>
                                @foreach($communities as $community)
                                    <option value="{{ $community->id }}" {{ old('community_id') == $community->id ? 'selected' : '' }}>
                                        {{ $community->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('community_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="service_id" class="form-label">Service (Optional - for mid-week services)</label>
                            <select class="form-select @error('service_id') is-invalid @enderror" id="service_id" name="service_id">
                                <option value="">Select service (optional)...</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}" data-type="{{ $service->service_type }}" data-date="{{ $service->service_date->format('Y-m-d') }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $service->service_type)) }} - {{ $service->service_date->format('M d, Y') }}
                                    </option>
                                @endforeach
                            </select>
                            @error('service_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <input type="hidden" id="service_type" name="service_type" value="{{ old('service_type') }}">
                        </div>

                        <div class="mb-3">
                            <label for="amount" class="form-label">Offering Amount (TZS) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" class="form-control @error('amount') is-invalid @enderror" 
                                   id="amount" name="amount" value="{{ old('amount') }}" required>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="offering_date" class="form-label">Offering Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('offering_date') is-invalid @enderror" 
                                   id="offering_date" name="offering_date" value="{{ old('offering_date', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required>
                            @error('offering_date')
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
                            @if(isset($community))
                                <a href="{{ route('church-elder.community-offerings.index', $community->id) }}" class="btn btn-outline-secondary">
                            @elseif($communities->isNotEmpty())
                                <a href="{{ route('church-elder.community-offerings.index', $communities->first()->id) }}" class="btn btn-outline-secondary">
                            @else
                                <a href="{{ route('church-elder.dashboard') }}" class="btn btn-outline-secondary">
                            @endif
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
document.getElementById('service_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    if (selectedOption.value) {
        document.getElementById('service_type').value = selectedOption.dataset.type;
        document.getElementById('offering_date').value = selectedOption.dataset.date;
    } else {
        document.getElementById('service_type').value = '';
    }
});

document.getElementById('collection_method').addEventListener('change', function() {
    const referenceGroup = document.getElementById('reference_number_group');
    if (this.value === 'mobile_money' || this.value === 'bank_transfer') {
        referenceGroup.style.display = 'block';
    } else {
        referenceGroup.style.display = 'none';
    }
});
</script>
@endsection

