@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="mt-4"><i class="fas fa-edit me-2"></i>Edit Offering</h1>
                <a href="{{ route('evangelism-leader.finance.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Finance
                </a>
            </div>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('evangelism-leader.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('evangelism-leader.finance.index') }}">Finance Management</a></li>
                <li class="breadcrumb-item active">Edit Offering</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Offering Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('evangelism-leader.finance.offerings.update', $offering) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label">Member <span class="text-danger">*</span></label>
                            <select name="member_id" class="form-select" required>
                                <option value="">Select Member</option>
                                @foreach($members as $member)
                                <option value="{{ $member->id }}" {{ $offering->member_id == $member->id ? 'selected' : '' }}>
                                    {{ $member->full_name }}
                                </option>
                                @endforeach
                            </select>
                            @error('member_id')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Amount (TZS) <span class="text-danger">*</span></label>
                            <input type="number" name="amount" class="form-control" step="0.01" min="0" value="{{ old('amount', $offering->amount) }}" required>
                            @error('amount')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Offering Date <span class="text-danger">*</span></label>
                            <input type="date" name="offering_date" class="form-control" value="{{ old('offering_date', $offering->offering_date->format('Y-m-d')) }}" required>
                            @error('offering_date')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Offering Type <span class="text-danger">*</span></label>
                            <select name="offering_type" class="form-select" required>
                                <option value="general" {{ $offering->offering_type == 'general' ? 'selected' : '' }}>General</option>
                                <option value="special" {{ $offering->offering_type == 'special' ? 'selected' : '' }}>Special</option>
                                <option value="thanksgiving" {{ $offering->offering_type == 'thanksgiving' ? 'selected' : '' }}>Thanksgiving</option>
                                <option value="building_fund" {{ $offering->offering_type == 'building_fund' ? 'selected' : '' }}>Building Fund</option>
                                <option value="other" {{ $offering->offering_type == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('offering_type')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                            <select name="payment_method" class="form-select" required>
                                <option value="cash" {{ $offering->payment_method == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="check" {{ $offering->payment_method == 'check' ? 'selected' : '' }}>Check</option>
                                <option value="bank_transfer" {{ $offering->payment_method == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="mobile_money" {{ $offering->payment_method == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                            </select>
                            @error('payment_method')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Reference Number</label>
                            <input type="text" name="reference_number" class="form-control" value="{{ old('reference_number', $offering->reference_number) }}" placeholder="Optional reference number">
                            @error('reference_number')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Optional notes">{{ old('notes', $offering->notes) }}</textarea>
                            @error('notes')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('evangelism-leader.finance.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Offering
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

