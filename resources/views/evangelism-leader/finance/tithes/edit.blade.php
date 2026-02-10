@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="mt-4"><i class="fas fa-edit me-2"></i>Edit Tithe</h1>
                <a href="{{ route('evangelism-leader.finance.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Finance
                </a>
            </div>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('evangelism-leader.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('evangelism-leader.finance.index') }}">Finance Management</a></li>
                <li class="breadcrumb-item active">Edit Tithe</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Aggregate Tithe</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Note:</strong> This records the total tithe amount collected from all members. Individual member contributions are not tracked.
                    </div>
                    <form action="{{ route('evangelism-leader.finance.tithes.update', $tithe) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label">Tithe Date <span class="text-danger">*</span></label>
                            <input type="date" name="tithe_date" class="form-control" value="{{ old('tithe_date', $tithe->tithe_date->format('Y-m-d')) }}" required>
                            @error('tithe_date')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Total Amount (TZS) <span class="text-danger">*</span></label>
                            <input type="number" name="total_amount" id="totalAmount" class="form-control" step="0.01" min="0" value="{{ old('total_amount', $tithe->amount) }}" required>
                            <small class="text-muted">Enter the total tithe amount collected from all members</small>
                            @error('total_amount')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                            <select name="payment_method" class="form-select" required>
                                <option value="cash" {{ $tithe->payment_method == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="check" {{ $tithe->payment_method == 'check' ? 'selected' : '' }}>Check</option>
                                <option value="bank_transfer" {{ $tithe->payment_method == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="mobile_money" {{ $tithe->payment_method == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                            </select>
                            @error('payment_method')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Reference Number</label>
                            <input type="text" name="reference_number" class="form-control" value="{{ old('reference_number', $tithe->reference_number) }}" placeholder="Optional reference number">
                            @error('reference_number')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Optional notes about this tithe collection">{{ old('notes', $tithe->notes) }}</textarea>
                            @error('notes')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('evangelism-leader.finance.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Tithe
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection






