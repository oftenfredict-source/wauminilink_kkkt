@extends('layouts.index')

@section('content')
@if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        });
    </script>
@endif

@if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '{{ session('error') }}',
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        });
    </script>
@endif

<div class="container-fluid px-4">
    <!-- Page Title and Quick Actions - Compact Collapsible -->
    <div class="card border-0 shadow-sm mb-3 actions-card">
        <div class="card-header bg-white border-bottom p-2 px-3 d-flex align-items-center justify-content-between actions-header" onclick="toggleActions()">
            <div class="d-flex align-items-center gap-2">
                <h1 class="mb-0 mt-2" style="font-size: 1.5rem;"><i class="fas fa-gift me-2"></i>Offerings Management</h1>
            </div>
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-chevron-down text-muted d-md-none" id="actionsToggleIcon"></i>
            </div>
        </div>
        <div class="card-body p-3" id="actionsBody">
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addOfferingModal">
                    <i class="fas fa-plus me-1"></i>
                    <span class="d-none d-sm-inline">Add Offering</span>
                    <span class="d-sm-none">Add</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Filters & Search - Collapsible on Mobile -->
    <form method="GET" action="{{ route('finance.offerings') }}" class="card mb-4 border-0 shadow-sm" id="filtersForm">
        <!-- Filter Header -->
        <div class="card-header bg-primary text-white p-2 px-3 filter-header" onclick="toggleFilters()">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-filter me-1"></i>
                    <span class="fw-semibold">Filters</span>
                    @if(request('offering_type') || request('date_from') || request('date_to'))
                        <span class="badge bg-white text-primary rounded-pill ms-2" id="activeFiltersCount">{{ (request('offering_type') ? 1 : 0) + (request('date_from') ? 1 : 0) + (request('date_to') ? 1 : 0) }}</span>
                    @endif
                </div>
                <i class="fas fa-chevron-down text-white d-md-none" id="filterToggleIcon"></i>
            </div>
        </div>
        
        <!-- Filter Body - Collapsible on Mobile -->
        <div class="card-body p-3" id="filterBody">
            <div class="row g-2 mb-2">
                <!-- Offering Type - Full Width on Mobile -->
                <div class="col-12 col-md-3">
                    <label for="offering_type" class="form-label small text-muted mb-1">
                        <i class="fas fa-tags me-1 text-primary"></i>Offering Type
                    </label>
                    <select class="form-select form-select-sm" id="offering_type" name="offering_type">
                        <option value="">All Types</option>
                        <option value="general" {{ request('offering_type') == 'general' ? 'selected' : '' }}>General Offering</option>
                        <option value="special" {{ request('offering_type') == 'special' ? 'selected' : '' }}>Special</option>
                        <option value="thanksgiving" {{ request('offering_type') == 'thanksgiving' ? 'selected' : '' }}>Thanksgiving</option>
                        <option value="building_fund" {{ request('offering_type') == 'building_fund' ? 'selected' : '' }}>Building Fund</option>
                        <option value="other" {{ request('offering_type') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                
                <!-- Date Range - Side by Side on Mobile -->
                <div class="col-6 col-md-2">
                    <label for="date_from" class="form-label small text-muted mb-1">
                        <i class="fas fa-calendar me-1 text-info"></i>From Date
                    </label>
                    <input type="date" class="form-control form-control-sm" id="date_from" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="col-6 col-md-2">
                    <label for="date_to" class="form-label small text-muted mb-1">
                        <i class="fas fa-calendar-check me-1 text-info"></i>To Date
                    </label>
                    <input type="date" class="form-control form-control-sm" id="date_to" name="date_to" value="{{ request('date_to') }}">
                </div>
                
                <!-- Action Buttons - Full Width on Mobile -->
                <div class="col-12 col-md-3 d-flex gap-2 align-items-end">
                    <button type="submit" class="btn btn-success btn-sm flex-fill">
                        <i class="fas fa-search me-1"></i>
                        <span class="d-none d-sm-inline">Filter</span>
                        <span class="d-sm-none">Apply</span>
                    </button>
                    <a href="{{ route('finance.offerings') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-times me-1"></i>
                        <span class="d-none d-sm-inline">Clear</span>
                    </a>
                </div>
            </div>
        </div>
    </form>

    <!-- Offerings Table -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-table me-1"></i>
            <strong>Offerings Records</strong>
            <span class="badge bg-white text-primary ms-2 fw-bold">{{ $offerings->total() }} {{ $offerings->total() == 1 ? 'record' : 'records' }}</span>
        </div>
        <div class="card-body">
            @if($offerings->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="d-none d-md-table-cell">Member/Donor</th>
                                <th class="d-table-cell d-md-none">Offering</th>
                                <th>Amount</th>
                                <th>Type</th>
                                <th>Date</th>
                                <th class="d-none d-lg-table-cell">Payment Method</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($offerings as $offering)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-success rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                                <i class="fas fa-gift text-white small"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $offering->member->full_name ?? 'General Member' }}</div>
                                                <small class="text-muted d-none d-md-inline">
                                                    @if($offering->member)
                                                        {{ $offering->member->member_id }}
                                                    @endif
                                                </small>
                                                <div class="d-md-none">
                                                    <small class="text-muted d-block">
                                                        <i class="fas fa-credit-card me-1"></i>
                                                        <span class="badge bg-{{ $offering->payment_method == 'cash' ? 'success' : ($offering->payment_method == 'bank_transfer' ? 'info' : 'warning') }} badge-sm">
                                                            {{ ucfirst(str_replace('_', ' ', $offering->payment_method)) }}
                                                        </span>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-success">TZS {{ number_format($offering->amount, 0) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $offering->offering_type == 'general' ? 'primary' : ($offering->offering_type == 'special' ? 'warning' : 'info') }}">
                                            @if($offering->offering_type == 'general')
                                                General
                                            @elseif(in_array($offering->offering_type, ['special', 'thanksgiving', 'building_fund']))
                                                {{ ucfirst(str_replace('_', ' ', $offering->offering_type)) }}
                                            @else
                                                {{ ucfirst($offering->offering_type) }}
                                            @endif
                                        </span>
                                    </td>
                                    <td>{{ $offering->offering_date->format('M d, Y') }}</td>
                                    <td class="d-none d-lg-table-cell">
                                        <span class="badge bg-{{ $offering->payment_method == 'cash' ? 'success' : ($offering->payment_method == 'bank_transfer' ? 'info' : 'warning') }}">
                                            {{ ucfirst(str_replace('_', ' ', $offering->payment_method)) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($offering->approval_status == 'approved')
                                            <span class="badge bg-success">
                                                <i class="fas fa-check me-1"></i>Approved
                                            </span>
                                        @elseif($offering->approval_status == 'rejected')
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times me-1"></i>Rejected
                                            </span>
                                        @else
                                            <span class="badge bg-warning">
                                                <i class="fas fa-clock me-1"></i>Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-success text-white" data-bs-toggle="modal" data-bs-target="#viewOfferingModal{{ $offering->id }}" title="View Details">
                                                <i class="fas fa-eye"></i>
                                                <span class="d-none d-sm-inline ms-1">View</span>
                                            </button>
                                        </div>
                                        @if($offering->approval_status == 'pending')
                                            <div class="d-md-none mt-1">
                                                <span class="badge bg-info badge-sm">
                                                    <i class="fas fa-clock me-1"></i>Awaiting Approval
                                                </span>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $offerings->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-gift fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No offerings found</h5>
                    <p class="text-muted">Start by adding a new offering record.</p>
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addOfferingModal">
                        <i class="fas fa-plus me-1"></i>Add First Offering
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Add Offering Modal -->
<div class="modal fade" id="addOfferingModal" tabindex="-1" aria-labelledby="addOfferingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content stylish-modal">
            <form action="{{ route('finance.offerings.store') }}" method="POST">
                @csrf
                <div class="modal-header stylish-modal-header" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                    <div class="d-flex align-items-center">
                        <div class="modal-icon-wrapper me-3">
                            <i class="fas fa-gift"></i>
                        </div>
                        <div>
                            <h5 class="modal-title text-white mb-0" id="addOfferingModalLabel">
                                <strong>Add New Offering</strong>
                            </h5>
                            <small class="text-white-50">Record an offering or special gift</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="member_id" class="form-label">Member (Optional)</label>
                            <select class="form-select select2-member-modal @error('member_id') is-invalid @enderror" id="member_id" name="member_id">
                                <option value="">General Member Offering</option>
                                @foreach($members as $member)
                                    <option value="{{ $member->id }}" {{ old('member_id') == $member->id ? 'selected' : '' }}>
                                        {{ $member->full_name }} ({{ $member->member_id }})
                                    </option>
                                @endforeach
                            </select>
                            @error('member_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="amount" class="form-label">Amount (TZS) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" 
                                   value="{{ old('amount') }}" min="0" step="0.01" required>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="offering_type" class="form-label">Offering Type <span class="text-danger">*</span></label>
                            <select class="form-select @error('offering_type') is-invalid @enderror" id="offering_type" name="offering_type" required>
                                <option value="">Select Type</option>
                                <option value="general" {{ old('offering_type') == 'general' ? 'selected' : '' }}>General Offering</option>
                                <option value="special" {{ old('offering_type') == 'special' ? 'selected' : '' }}>Special</option>
                                <option value="thanksgiving" {{ old('offering_type') == 'thanksgiving' ? 'selected' : '' }}>Thanksgiving</option>
                                <option value="building_fund" {{ old('offering_type') == 'building_fund' ? 'selected' : '' }}>Building Fund</option>
                                <option value="other" {{ old('offering_type') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('offering_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6" id="custom_offering_type_group" style="display: none !important;">
                            <label for="custom_offering_type" class="form-label">Custom Offering Type <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('custom_offering_type') is-invalid @enderror" id="custom_offering_type" name="custom_offering_type" 
                                   value="{{ old('custom_offering_type') }}" placeholder="Enter custom offering type">
                            @error('custom_offering_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="offering_date" class="form-label">Offering Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('offering_date') is-invalid @enderror" id="offering_date" name="offering_date" 
                                   value="{{ old('offering_date', date('Y-m-d')) }}" required>
                            @error('offering_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                            <select class="form-select @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method" required>
                                <option value="">Select Method</option>
                                <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="check" {{ old('payment_method') == 'check' ? 'selected' : '' }}>Check</option>
                                <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="mobile_money" {{ old('payment_method') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                            </select>
                            @error('payment_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6" id="offering_reference_group">
                            <label for="reference_number" class="form-label">Reference Number</label>
                            <input type="text" class="form-control @error('reference_number') is-invalid @enderror" id="reference_number" name="reference_number" 
                                   value="{{ old('reference_number') }}" placeholder="e.g., Check #123, Transaction ID">
                            @error('reference_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="service_type" class="form-label">Service Type</label>
                            <select class="form-select @error('service_type') is-invalid @enderror" id="service_type" name="service_type">
                                <option value="">Select Service</option>
                                <option value="sunday_service" {{ old('service_type') == 'sunday_service' ? 'selected' : '' }}>Sunday Service</option>
                                <option value="special_event" {{ old('service_type') == 'special_event' ? 'selected' : '' }}>Special Event</option>
                                <option value="prayer_meeting" {{ old('service_type') == 'prayer_meeting' ? 'selected' : '' }}>Prayer Meeting</option>
                                <option value="other" {{ old('service_type') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('service_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-info mt-4">
                                <i class="fas fa-info-circle me-1"></i>
                                <strong>Note:</strong> All offerings require pastor approval before being added to totals.
                            </div>
                        </div>
                        <div class="col-12">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3" 
                                      placeholder="Additional notes about this offering...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer stylish-modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-success stylish-submit-btn" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); border: none; box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);">
                        <i class="fas fa-save me-1"></i>Save Offering
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Offering Modals -->
@foreach($offerings as $offering)
<div class="modal fade" id="viewOfferingModal{{ $offering->id }}" tabindex="-1" aria-labelledby="viewOfferingModalLabel{{ $offering->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewOfferingModalLabel{{ $offering->id }}">Offering Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Member/Donor</label>
                        <p>{{ $offering->member->full_name ?? 'General Member' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Amount</label>
                        <p class="h5 text-success">TZS {{ number_format($offering->amount, 0) }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Offering Type</label>
                        <p>
                            <span class="badge bg-{{ $offering->offering_type == 'general' ? 'primary' : ($offering->offering_type == 'special' ? 'warning' : 'info') }}">
                                @if($offering->offering_type == 'general')
                                    General Offering
                                @elseif(in_array($offering->offering_type, ['special', 'thanksgiving', 'building_fund']))
                                    {{ ucfirst(str_replace('_', ' ', $offering->offering_type)) }}
                                @else
                                    {{ ucfirst($offering->offering_type) }}
                                @endif
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Offering Date</label>
                        <p>{{ $offering->offering_date->format('F d, Y') }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Payment Method</label>
                        <p>
                            <span class="badge bg-{{ $offering->payment_method == 'cash' ? 'success' : ($offering->payment_method == 'bank_transfer' ? 'info' : 'warning') }}">
                                {{ ucfirst(str_replace('_', ' ', $offering->payment_method)) }}
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Status</label>
                        <p>
                            @if($offering->approval_status == 'approved')
                                <span class="badge bg-success">
                                    <i class="fas fa-check me-1"></i>Approved
                                </span>
                            @elseif($offering->approval_status == 'rejected')
                                <span class="badge bg-danger">
                                    <i class="fas fa-times me-1"></i>Rejected
                                </span>
                            @else
                                <span class="badge bg-warning">
                                    <i class="fas fa-clock me-1"></i>Pending Pastor Approval
                                </span>
                            @endif
                        </p>
                    </div>
                    @if($offering->reference_number)
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Reference Number</label>
                            <p>{{ $offering->reference_number }}</p>
                        </div>
                    @endif
                    @if($offering->service_type)
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Service Type</label>
                            <p>{{ ucfirst(str_replace('_', ' ', $offering->service_type)) }}</p>
                        </div>
                    @endif
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Recorded By</label>
                        <p>{{ $offering->recorded_by ?? 'System' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Recorded On</label>
                        <p>{{ $offering->created_at->format('F d, Y g:i A') }}</p>
                    </div>
                    @if($offering->notes)
                        <div class="col-12">
                            <label class="form-label fw-bold">Notes</label>
                            <p>{{ $offering->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                @if($offering->approval_status == 'pending')
                    <span class="badge bg-info">
                        <i class="fas fa-clock me-1"></i>Awaiting Pastor Approval
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>
@endforeach

<script>
// Toggle Actions Function
function toggleActions() {
    // Only toggle on mobile devices
    if (window.innerWidth > 768) {
        return; // Don't toggle on desktop
    }
    
    const actionsBody = document.getElementById('actionsBody');
    const actionsIcon = document.getElementById('actionsToggleIcon');
    
    if (!actionsBody || !actionsIcon) return;
    
    // Check computed style to see if it's visible
    const computedStyle = window.getComputedStyle(actionsBody);
    const isVisible = computedStyle.display !== 'none';
    
    if (isVisible) {
        actionsBody.style.display = 'none';
        actionsIcon.classList.remove('fa-chevron-up');
        actionsIcon.classList.add('fa-chevron-down');
    } else {
        actionsBody.style.display = 'block';
        actionsIcon.classList.remove('fa-chevron-down');
        actionsIcon.classList.add('fa-chevron-up');
    }
}

// Toggle Filters Function
function toggleFilters() {
    // Only toggle on mobile devices
    if (window.innerWidth > 768) {
        return; // Don't toggle on desktop
    }
    
    const filterBody = document.getElementById('filterBody');
    const filterIcon = document.getElementById('filterToggleIcon');
    const filterHeader = document.querySelector('.filter-header');
    
    if (!filterBody || !filterIcon) return;
    
    // Check computed style to see if it's visible
    const computedStyle = window.getComputedStyle(filterBody);
    const isVisible = computedStyle.display !== 'none';
    
    if (isVisible) {
        filterBody.style.display = 'none';
        filterIcon.classList.remove('fa-chevron-up');
        filterIcon.classList.add('fa-chevron-down');
        if (filterHeader) filterHeader.classList.remove('active');
    } else {
        filterBody.style.display = 'block';
        filterIcon.classList.remove('fa-chevron-down');
        filterIcon.classList.add('fa-chevron-up');
        if (filterHeader) filterHeader.classList.add('active');
    }
}

// Handle window resize
window.addEventListener('resize', function() {
    const actionsBody = document.getElementById('actionsBody');
    const actionsIcon = document.getElementById('actionsToggleIcon');
    const filterBody = document.getElementById('filterBody');
    const filterIcon = document.getElementById('filterToggleIcon');
    
    if (window.innerWidth > 768) {
        // Always show on desktop
        if (actionsBody && actionsIcon) {
            actionsBody.style.display = 'block';
            actionsIcon.style.display = 'none';
        }
        if (filterBody && filterIcon) {
            filterBody.style.display = 'block';
            filterIcon.style.display = 'none';
        }
    } else {
        // On mobile, show chevrons
        if (actionsIcon) actionsIcon.style.display = 'block';
        if (filterIcon) filterIcon.style.display = 'block';
    }
});

// Initialize Select2 for member dropdowns
document.addEventListener('DOMContentLoaded', function() {
    // Initialize actions and filters
    const actionsBody = document.getElementById('actionsBody');
    const actionsIcon = document.getElementById('actionsToggleIcon');
    const filterBody = document.getElementById('filterBody');
    const filterIcon = document.getElementById('filterToggleIcon');
    
    if (window.innerWidth <= 768) {
        // Mobile: start collapsed
        if (actionsBody && actionsIcon) {
            actionsBody.style.display = 'none';
            actionsIcon.classList.remove('fa-chevron-up');
            actionsIcon.classList.add('fa-chevron-down');
        }
        if (filterBody && filterIcon) {
            filterBody.style.display = 'none';
            filterIcon.classList.remove('fa-chevron-up');
            filterIcon.classList.add('fa-chevron-down');
        }
    } else {
        // Desktop: always show
        if (actionsBody && actionsIcon) {
            actionsBody.style.display = 'block';
            actionsIcon.style.display = 'none';
        }
        if (filterBody && filterIcon) {
            filterBody.style.display = 'block';
            filterIcon.style.display = 'none';
        }
    }
    
    // Show filters if any are active
    @if(request('offering_type') || request('date_from') || request('date_to'))
        if (window.innerWidth <= 768 && filterBody && filterIcon) {
            toggleFilters(); // Expand if filters are active
            const filterHeader = document.querySelector('.filter-header');
            if (filterHeader) filterHeader.classList.add('active');
        }
    @endif
    // Initialize Select2 for modal dropdown
    $('.select2-member-modal').select2({
        placeholder: 'Search for a member...',
        allowClear: true,
        width: '100%',
        dropdownParent: $('#addOfferingModal')
    });

    // Toggle reference number visibility based on payment method
    var offeringModal = document.getElementById('addOfferingModal');
    if (offeringModal) {
        var methodEl = offeringModal.querySelector('#payment_method');
        var refGroup = offeringModal.querySelector('#offering_reference_group');
        var refInput = offeringModal.querySelector('#reference_number');

        function updateOfferingRefVisibility() {
            var method = methodEl ? methodEl.value : '';
            var hide = method === 'cash' || method === '';
            if (refGroup) {
                refGroup.style.display = hide ? 'none' : '';
            }
            if (refInput) {
                refInput.required = !hide;
                if (hide) refInput.value = '';
            }
        }

        if (methodEl) {
            methodEl.addEventListener('change', updateOfferingRefVisibility);
        }
        offeringModal.addEventListener('shown.bs.modal', updateOfferingRefVisibility);
        // Initialize state on load
        updateOfferingRefVisibility();
    }

    // Handle custom offering type field visibility
    function updateCustomOfferingTypeVisibility() {
        var offeringTypeEl = document.querySelector('#addOfferingModal #offering_type');
        var customOfferingTypeGroup = document.querySelector('#addOfferingModal #custom_offering_type_group');
        var customOfferingTypeInput = document.querySelector('#addOfferingModal #custom_offering_type');
        
        if (!offeringTypeEl || !customOfferingTypeGroup || !customOfferingTypeInput) {
            console.log('Elements not found:', {
                offeringTypeEl: !!offeringTypeEl,
                customOfferingTypeGroup: !!customOfferingTypeGroup,
                customOfferingTypeInput: !!customOfferingTypeInput
            });
            return;
        }
        
        var offeringType = offeringTypeEl.value;
        var showCustom = offeringType === 'other';
        
        console.log('Offering type:', offeringType, 'Show custom:', showCustom);
        
        if (showCustom) {
            customOfferingTypeGroup.style.setProperty('display', 'block', 'important');
            customOfferingTypeInput.required = true;
        } else {
            customOfferingTypeGroup.style.setProperty('display', 'none', 'important');
            customOfferingTypeInput.required = false;
            customOfferingTypeInput.value = '';
        }
    }

    // Add event listener to the offering type dropdown
    document.addEventListener('change', function(e) {
        if (e.target && e.target.id === 'offering_type' && e.target.closest('#addOfferingModal')) {
            updateCustomOfferingTypeVisibility();
        }
    });

    // Initialize when modal is shown
    document.addEventListener('shown.bs.modal', function(e) {
        if (e.target && e.target.id === 'addOfferingModal') {
            updateCustomOfferingTypeVisibility();
        }
    });
});

// Offering verification is now handled by pastor approval system
</script>
@endsection

@section('styles')
<style>
    /* Stylish Modal Styles for Offerings */
    .stylish-modal {
        border: none;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        overflow: hidden;
    }
    
    .stylish-modal-header {
        padding: 1.5rem;
        border-bottom: none;
    }
    
    .modal-icon-wrapper {
        width: 50px;
        height: 50px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
        backdrop-filter: blur(10px);
    }
    
    .stylish-modal .modal-body {
        padding: 2rem;
        background: #f8f9fa;
    }
    
    .stylish-modal .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }
    
    .stylish-modal .form-control,
    .stylish-modal .form-select {
        border-radius: 8px;
        border: 1.5px solid #e0e0e0;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
        font-size: 0.95rem;
    }
    
    .stylish-modal .form-control:focus,
    .stylish-modal .form-select:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.15);
        transform: translateY(-1px);
    }
    
    .stylish-modal .form-control:hover,
    .stylish-modal .form-select:hover {
        border-color: #28a745;
    }
    
    .stylish-modal-footer {
        padding: 1.25rem 2rem;
        border-top: 1px solid #e9ecef;
        background: white;
    }
    
    .stylish-submit-btn {
        padding: 0.75rem 2rem;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .stylish-submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4) !important;
        background: linear-gradient(135deg, #218838 0%, #1aa179 100%) !important;
    }
    
    .stylish-modal .btn-light {
        border-radius: 8px;
        padding: 0.75rem 2rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .stylish-modal .btn-light:hover {
        background: #e9ecef;
        transform: translateY(-1px);
    }
    
    .stylish-modal .form-check-input {
        width: 1.25rem;
        height: 1.25rem;
        border-radius: 6px;
        border: 2px solid #28a745;
        cursor: pointer;
    }
    
    .stylish-modal .form-check-input:checked {
        background-color: #28a745;
        border-color: #28a745;
    }
    
    .stylish-modal textarea.form-control {
        resize: vertical;
        min-height: 100px;
    }
    
    /* Animation for modal */
    .modal.fade .modal-dialog {
        transition: transform 0.3s ease-out;
        transform: translate(0, -50px);
    }
    
    .modal.show .modal-dialog {
        transform: none;
    }
    
    /* Select2 styling in modal */
    .stylish-modal .select2-container--default .select2-selection--single {
        border: 1.5px solid #e0e0e0;
        border-radius: 8px;
        height: auto;
        padding: 0.5rem;
    }
    
    .stylish-modal .select2-container--default .select2-selection--single:focus {
        border-color: #28a745;
    }
    
    /* Prevent scrolling */
    .stylish-modal .modal-body {
        max-height: calc(100vh - 250px);
        overflow-y: auto;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .stylish-modal .modal-body {
            padding: 1.5rem;
            max-height: calc(100vh - 200px);
        }
        
        .stylish-modal-header {
            padding: 1.25rem;
        }
        
        .modal-icon-wrapper {
            width: 40px;
            height: 40px;
            font-size: 20px;
        }
        
        .container-fluid {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
        }
        
        /* Actions Card */
        .actions-card {
            transition: all 0.3s ease;
        }
        .actions-card .card-header {
            user-select: none;
            transition: background-color 0.2s ease;
        }
        .actions-card .card-header:hover {
            background-color: #f8f9fa !important;
        }
        #actionsBody {
            transition: all 0.3s ease;
            display: none;
        }
        .actions-header {
            cursor: pointer !important;
        }
        #actionsToggleIcon {
            display: block !important;
        }
        
        /* Filter Section */
        #filtersForm .card-header {
            transition: all 0.2s ease;
        }
        .filter-header:hover {
            opacity: 0.9;
        }
        #filterBody {
            transition: all 0.3s ease;
            display: none;
            background: #fafbfc;
        }
        .filter-header {
            cursor: pointer !important;
        }
        #filterToggleIcon {
            display: block !important;
            transition: transform 0.3s ease;
        }
        .filter-header.active #filterToggleIcon {
            transform: rotate(180deg);
        }
        #filtersForm .card-body {
            padding: 0.75rem 0.5rem !important;
        }
        #filtersForm .form-label {
            font-size: 0.7rem !important;
            margin-bottom: 0.2rem !important;
            font-weight: 600 !important;
        }
        #filtersForm .form-control,
        #filtersForm .form-select {
            font-size: 0.8125rem !important;
            padding: 0.4rem 0.5rem !important;
            border-radius: 6px !important;
        }
        #filtersForm .btn-sm {
            padding: 0.4rem 0.75rem !important;
            font-size: 0.8125rem !important;
            border-radius: 6px !important;
            font-weight: 600 !important;
        }
        #filtersForm .row.g-2 > [class*="col-"] {
            padding-left: 0.375rem !important;
            padding-right: 0.375rem !important;
            margin-bottom: 0.5rem !important;
        }
        #filtersForm .row.g-2 {
            margin-left: -0.375rem !important;
            margin-right: -0.375rem !important;
        }
        
        /* Table Responsive */
        .table {
            font-size: 0.75rem;
        }
        .table th,
        .table td {
            padding: 0.5rem 0.25rem;
        }
        
        /* Buttons - Icon Only on Mobile */
        .btn-group .btn {
            padding: 0.375rem 0.5rem !important;
        }
        .btn-group .btn i {
            margin: 0 !important;
        }
        
        /* Modal Full Screen on Mobile */
        @media (max-width: 576px) {
            .modal-dialog {
                margin: 0;
                max-width: 100%;
                height: 100vh;
            }
            .modal-content {
                height: 100vh;
                border-radius: 0 !important;
            }
            #filtersForm .card-body {
                padding: 0.5rem 0.375rem !important;
            }
            #filtersForm .form-label {
                font-size: 0.65rem !important;
            }
            #filtersForm .form-control,
            #filtersForm .form-select {
                font-size: 0.75rem !important;
                padding: 0.35rem 0.45rem !important;
            }
        }
    }
    
    /* Desktop: Always show actions and filters */
    @media (min-width: 769px) {
        .actions-header {
            cursor: default !important;
            pointer-events: none !important;
        }
        .actions-header .fa-chevron-down {
            display: none !important;
        }
        #actionsBody {
            display: block !important;
        }
        
        .filter-header {
            cursor: default !important;
            pointer-events: none !important;
        }
        .filter-header .fa-chevron-down {
            display: none !important;
        }
        #filterBody {
            display: block !important;
        }
    }
</style>
@endsection

