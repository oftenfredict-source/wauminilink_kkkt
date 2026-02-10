@extends('layouts.index')

@section('content')
    <div class="container-fluid px-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-fire me-2"></i>Candle Transaction (Gidhinisho la Kandili)</h5>
                        <a href="{{ route('parish-worker.candles.index') }}" class="btn btn-light btn-sm">Back to List</a>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('parish-worker.candles.store') }}" method="POST">
                            @csrf

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="action_type" class="form-label fw-bold">Action Type (Aina ya
                                        Kitendo)</label>
                                    <select class="form-select @error('action_type') is-invalid @enderror" id="action_type"
                                        name="action_type" required>
                                        <option value="purchase" {{ old('action_type') == 'purchase' ? 'selected' : '' }}>
                                            Purchase (Ununuzi)</option>
                                        <option value="distribution" {{ old('action_type') == 'distribution' ? 'selected' : '' }}>Distribution (Ugawaji)</option>
                                    </select>
                                    @error('action_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="action_date" class="form-label fw-bold">Date (Tarehe)</label>
                                    <input type="date" class="form-control @error('action_date') is-invalid @enderror"
                                        id="action_date" name="action_date" value="{{ old('action_date', date('Y-m-d')) }}"
                                        required>
                                    @error('action_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="quantity" class="form-label fw-bold">Quantity (Idadi ya Kandili)</label>
                                <input type="number" class="form-control @error('quantity') is-invalid @enderror"
                                    id="quantity" name="quantity" value="{{ old('quantity') }}"
                                    placeholder="Enter amount of candles" required min="1">
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div id="purchase_fields"
                                class="{{ old('action_type', 'purchase') == 'purchase' ? '' : 'd-none' }}">
                                <div class="mb-3">
                                    <label for="cost" class="form-label fw-bold">Total Cost (Gharama ya Jumla)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">TSh</span>
                                        <input type="number" class="form-control @error('cost') is-invalid @enderror"
                                            id="cost" name="cost" value="{{ old('cost') }}" step="0.01" placeholder="0.00">
                                    </div>
                                    @error('cost')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div id="distribution_fields"
                                class="{{ old('action_type') == 'distribution' ? '' : 'd-none' }}">
                                <div class="mb-3">
                                    <label for="campus_id" class="form-label fw-bold">Target Campus (Tawi
                                        Linalopelekewa)</label>
                                    <select class="form-select @error('campus_id') is-invalid @enderror" id="campus_id"
                                        name="campus_id">
                                        <option value="" selected disabled>Select campus...</option>
                                        @foreach($campuses as $campus)
                                            <option value="{{ $campus->id }}" {{ old('campus_id') == $campus->id ? 'selected' : '' }}>{{ $campus->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('campus_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="received_by" class="form-label fw-bold">Received By (Amepokelewa Na)</label>
                                    <input type="text" class="form-control @error('received_by') is-invalid @enderror"
                                        id="received_by" name="received_by" value="{{ old('received_by') }}"
                                        placeholder="Name of the person receiving">
                                    @error('received_by')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="notes" class="form-label fw-bold">Notes (Maelezo ya Ziada)</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes"
                                    rows="3" placeholder="Any additional information...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-danger py-2 fw-bold">
                                    <i class="fas fa-save me-2"></i>Save Transaction
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('action_type').addEventListener('change', function () {
            if (this.value === 'purchase') {
                document.getElementById('purchase_fields').classList.remove('d-none');
                document.getElementById('distribution_fields').classList.add('d-none');
                document.getElementById('cost').setAttribute('required', 'required');
                document.getElementById('campus_id').removeAttribute('required');
            } else {
                document.getElementById('purchase_fields').classList.add('d-none');
                document.getElementById('distribution_fields').classList.remove('d-none');
                document.getElementById('cost').removeAttribute('required');
                document.getElementById('campus_id').setAttribute('required', 'required');
            }
        });

        // Initial state check
        window.addEventListener('load', function () {
            const actionType = document.getElementById('action_type').value;
            if (actionType === 'purchase') {
                document.getElementById('cost').setAttribute('required', 'required');
            } else {
                document.getElementById('campus_id').setAttribute('required', 'required');
            }
        });
    </script>
@endsection