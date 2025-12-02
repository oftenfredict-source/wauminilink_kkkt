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
                <h1 class="mb-0 mt-2" style="font-size: 1.5rem;"><i class="fas fa-heart me-2"></i>Donations Management</h1>
            </div>
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-chevron-down text-muted d-md-none" id="actionsToggleIcon"></i>
            </div>
        </div>
        <div class="card-body p-3" id="actionsBody">
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addDonationModal">
                    <i class="fas fa-plus me-1"></i>
                    <span class="d-none d-sm-inline">Add Donation</span>
                    <span class="d-sm-none">Add</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Filters & Search - Collapsible on Mobile -->
    <form method="GET" action="{{ route('finance.donations') }}" class="card mb-4 border-0 shadow-sm" id="filtersForm">
        <!-- Filter Header -->
        <div class="card-header bg-primary text-white p-2 px-3 filter-header" onclick="toggleFilters()">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-filter me-1"></i>
                    <span class="fw-semibold">Filters</span>
                    @if(request('donation_type') || request('date_from') || request('date_to'))
                        <span class="badge bg-white text-primary rounded-pill ms-2" id="activeFiltersCount">{{ (request('donation_type') ? 1 : 0) + (request('date_from') ? 1 : 0) + (request('date_to') ? 1 : 0) }}</span>
                    @endif
                </div>
                <i class="fas fa-chevron-down text-white d-md-none" id="filterToggleIcon"></i>
            </div>
        </div>
        
        <!-- Filter Body - Collapsible on Mobile -->
        <div class="card-body p-3" id="filterBody">
            <div class="row g-2 mb-2">
                <!-- Donation Type - Full Width on Mobile -->
                <div class="col-12 col-md-3">
                    <label for="donation_type" class="form-label small text-muted mb-1">
                        <i class="fas fa-tags me-1 text-primary"></i>Donation Type
                    </label>
                    <select class="form-select form-select-sm" id="donation_type" name="donation_type">
                        <option value="">All Types</option>
                        <option value="general" {{ request('donation_type') == 'general' ? 'selected' : '' }}>General</option>
                        <option value="building" {{ request('donation_type') == 'building' ? 'selected' : '' }}>Building Fund</option>
                        <option value="mission" {{ request('donation_type') == 'mission' ? 'selected' : '' }}>Mission</option>
                        <option value="special" {{ request('donation_type') == 'special' ? 'selected' : '' }}>Special Project</option>
                        <option value="other" {{ request('donation_type') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                
                <!-- Date Range - Side by Side on Mobile -->
                <div class="col-6 col-md-3">
                    <label for="date_from" class="form-label small text-muted mb-1">
                        <i class="fas fa-calendar me-1 text-info"></i>From Date
                    </label>
                    <input type="date" class="form-control form-control-sm" id="date_from" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="col-6 col-md-3">
                    <label for="date_to" class="form-label small text-muted mb-1">
                        <i class="fas fa-calendar-check me-1 text-info"></i>To Date
                    </label>
                    <input type="date" class="form-control form-control-sm" id="date_to" name="date_to" value="{{ request('date_to') }}">
                </div>
                
                <!-- Action Buttons - Full Width on Mobile -->
                <div class="col-12 col-md-3 d-flex gap-2 align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm flex-fill">
                        <i class="fas fa-search me-1"></i>
                        <span class="d-none d-sm-inline">Filter</span>
                        <span class="d-sm-none">Apply</span>
                    </button>
                    <a href="{{ route('finance.donations') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-times me-1"></i>
                        <span class="d-none d-sm-inline">Clear</span>
                    </a>
                </div>
            </div>
        </div>
    </form>

    <!-- Donations Table -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-table me-1"></i><strong>Donations List</strong>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="d-none d-md-table-cell">Date</th>
                            <th class="d-table-cell d-md-none">Donation</th>
                            <th class="d-none d-lg-table-cell">Donor</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th class="d-none d-xl-table-cell">Payment Method</th>
                            <th class="d-none d-md-table-cell">Purpose</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($donations as $donation)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $donation->donation_date->format('M d, Y') }}</div>
                                <div class="d-md-none">
                                    <small class="text-muted d-block">
                                        @if($donation->is_anonymous)
                                            <i class="fas fa-user-secret me-1"></i>Anonymous
                                        @elseif($donation->member)
                                            <i class="fas fa-user me-1"></i>{{ $donation->member->full_name }}
                                        @else
                                            <i class="fas fa-user me-1"></i>{{ $donation->donor_name ?? 'Unknown' }}
                                        @endif
                                    </small>
                                    @if($donation->purpose)
                                        <small class="text-muted d-block">
                                            <i class="fas fa-bullseye me-1"></i>{{ Str::limit($donation->purpose, 30) }}
                                        </small>
                                    @endif
                                </div>
                            </td>
                            <td class="d-none d-lg-table-cell">
                                @if($donation->is_anonymous)
                                    <span class="text-muted">Anonymous</span>
                                @elseif($donation->member)
                                    {{ $donation->member->full_name }}
                                @else
                                    {{ $donation->donor_name ?? 'Unknown' }}
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    @if(in_array($donation->donation_type, ['general', 'building', 'mission', 'special']))
                                        {{ ucfirst(str_replace('_', ' ', $donation->donation_type)) }}
                                    @else
                                        {{ ucfirst($donation->donation_type) }}
                                    @endif
                                </span>
                            </td>
                            <td class="text-end">
                                <span class="fw-bold text-primary">TZS {{ number_format($donation->amount, 0) }}</span>
                            </td>
                            <td class="d-none d-xl-table-cell">
                                <span class="badge bg-{{ $donation->payment_method == 'cash' ? 'success' : ($donation->payment_method == 'bank_transfer' ? 'info' : 'warning') }}">
                                    {{ ucfirst($donation->payment_method) }}
                                </span>
                            </td>
                            <td class="d-none d-md-table-cell">{{ $donation->purpose ?? '-' }}</td>
                            <td>
                                @if($donation->approval_status == 'approved')
                                    <span class="badge bg-success">
                                        <i class="fas fa-check me-1"></i>Approved
                                    </span>
                                @elseif($donation->approval_status == 'rejected')
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
                                    <button 
                                        type="button" 
                                        class="btn btn-sm btn-outline-primary text-white"
                                        onclick="viewDonation(this)"
                                        data-id="{{ $donation->id }}"
                                        data-date="{{ $donation->donation_date->format('M d, Y') }}"
                                        data-donor="{{ $donation->is_anonymous ? 'Anonymous' : ($donation->member->full_name ?? ($donation->donor_name ?? 'Unknown')) }}"
                                        data-donor-name="{{ $donation->donor_name ?? ($donation->member->full_name ?? 'N/A') }}"
                                        data-donor-email="{{ $donation->donor_email ?? ($donation->member->email ?? 'N/A') }}"
                                        data-donor-phone="{{ $donation->donor_phone ?? ($donation->member->phone_number ?? 'N/A') }}"
                                        data-member-id="{{ $donation->member_id ?? '' }}"
                                        data-is-member="{{ $donation->member_id ? 'true' : 'false' }}"
                                        data-is-anonymous="{{ $donation->is_anonymous ? 'true' : 'false' }}"
                                        data-type="{{ in_array($donation->donation_type, ['general', 'building', 'mission', 'special']) ? ucfirst(str_replace('_', ' ', $donation->donation_type)) : ucfirst($donation->donation_type) }}"
                                        data-amount="{{ number_format($donation->amount, 2) }}"
                                        data-method="{{ ucfirst($donation->payment_method) }}"
                                        data-purpose="{{ $donation->purpose ?? '-' }}"
                                        data-status="{{ $donation->approval_status == 'approved' ? 'Approved' : ($donation->approval_status == 'rejected' ? 'Rejected' : 'Pending Approval') }}"
                                        data-reference="{{ $donation->reference_number ?? '-' }}"
                                        data-notes="{{ $donation->notes ?? '-' }}"
                                        title="View Details"
                                    >
                                        <i class="fas fa-eye"></i>
                                        <span class="d-none d-sm-inline ms-1">View</span>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-success text-white" onclick="editDonation({{ $donation->id }})" title="Edit">
                                        <i class="fas fa-edit"></i>
                                        <span class="d-none d-sm-inline ms-1">Edit</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">No donations found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $donations->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Add Donation Modal -->
<div class="modal fade" id="addDonationModal" tabindex="-1" aria-labelledby="addDonationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content stylish-modal">
            <div class="modal-header stylish-modal-header" style="background: linear-gradient(135deg, #0dcaf0 0%, #17a2b8 100%);">
                <div class="d-flex align-items-center">
                    <div class="modal-icon-wrapper me-3">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white mb-0" id="addDonationModalLabel">
                            <strong>Add New Donation</strong>
                        </h5>
                        <small class="text-white-50">Record a donation or contribution</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('finance.donations.store') }}" method="POST">
                @csrf
                <div class="modal-body" style="background: #f8f9fa;">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="member_id" class="form-label">Member (Optional)</label>
                                <select class="form-select select2-member-modal" id="member_id" name="member_id">
                                    <option value="">Select Member (or leave blank for non-member)</option>
                                    @foreach($members as $member)
                                        <option value="{{ $member->id }}">{{ $member->full_name }} ({{ $member->member_id }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="donation_type" class="form-label">Donation Type *</label>
                                <select class="form-select" id="donation_type" name="donation_type" required>
                                    <option value="">Select Type</option>
                                    <option value="general">General</option>
                                    <option value="building">Building Fund</option>
                                    <option value="mission">Mission</option>
                                    <option value="special">Special Project</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6" id="custom_donation_type_group" style="display: none !important;">
                            <div class="mb-3">
                                <label for="custom_donation_type" class="form-label">Custom Donation Type <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="custom_donation_type" name="custom_donation_type" 
                                       placeholder="Enter custom donation type">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="donor_name" class="form-label">Donor Name (Optional - if not a member)</label>
                                <input type="text" class="form-control" id="donor_name" name="donor_name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="donor_email" class="form-label">Donor Email (Optional)</label>
                                <input type="email" class="form-control" id="donor_email" name="donor_email">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="donor_phone" class="form-label">Donor Phone (Optional)</label>
                                <input type="text" class="form-control" id="donor_phone" name="donor_phone">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="amount" class="form-label">Amount *</label>
                                <input type="number" class="form-control" id="amount" name="amount" step="0.01" min="0" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="donation_date" class="form-label">Donation Date *</label>
                                <input type="date" class="form-control" id="donation_date" name="donation_date" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="payment_method" class="form-label">Payment Method *</label>
                                <select class="form-select" id="payment_method" name="payment_method" required>
                                    <option value="">Select Method</option>
                                    <option value="cash">Cash</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="mobile_money">Mobile Money</option>
                                    <option value="check">Check</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6" id="donation_reference_group">
                            <div class="mb-3">
                                <label for="reference_number" class="form-label">Reference Number</label>
                                <input type="text" class="form-control" id="reference_number" name="reference_number">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="purpose" class="form-label">Purpose</label>
                                <input type="text" class="form-control" id="purpose" name="purpose">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_verified" name="is_verified" value="1">
                                <label class="form-check-label" for="is_verified">
                                    Verified
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_anonymous" name="is_anonymous" value="1">
                                <label class="form-check-label" for="is_anonymous">
                                    Anonymous Donation
                                </label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-1"></i>
                                <strong>Note:</strong> You can record donations without specifying a member or donor name. Leave optional fields empty to record as an anonymous donation.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer stylish-modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary stylish-submit-btn" style="background: linear-gradient(135deg, #0dcaf0 0%, #17a2b8 100%); border: none; box-shadow: 0 4px 15px rgba(13, 202, 240, 0.3);">
                        <i class="fas fa-save me-1"></i>Add Donation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Donation Modal -->
<div class="modal fade" id="viewDonationModal" tabindex="-1" aria-labelledby="viewDonationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="viewDonationModalLabel">
                    <i class="fas fa-heart me-2"></i>Donation Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="viewDonationBody">
                <!-- Content will be populated by JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

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
    @if(request('donation_type') || request('date_from') || request('date_to'))
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
        dropdownParent: $('#addDonationModal')
    });

    // Toggle reference number visibility based on payment method
    var donationModal = document.getElementById('addDonationModal');
    if (donationModal) {
        var methodEl = donationModal.querySelector('#payment_method');
        var refGroup = donationModal.querySelector('#donation_reference_group');
        var refInput = donationModal.querySelector('#reference_number');

        function updateDonationRefVisibility() {
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
            methodEl.addEventListener('change', updateDonationRefVisibility);
        }

        donationModal.addEventListener('shown.bs.modal', updateDonationRefVisibility);
        // Initialize state on load
        updateDonationRefVisibility();
    }

    // Handle custom donation type field visibility
    function updateCustomDonationTypeVisibility() {
        var donationTypeEl = document.querySelector('#addDonationModal #donation_type');
        var customDonationTypeGroup = document.querySelector('#addDonationModal #custom_donation_type_group');
        var customDonationTypeInput = document.querySelector('#addDonationModal #custom_donation_type');
        
        if (!donationTypeEl || !customDonationTypeGroup || !customDonationTypeInput) {
            console.log('Donation elements not found:', {
                donationTypeEl: !!donationTypeEl,
                customDonationTypeGroup: !!customDonationTypeGroup,
                customDonationTypeInput: !!customDonationTypeInput
            });
            return;
        }
        
        var donationType = donationTypeEl.value;
        var showCustom = donationType === 'other';
        
        console.log('Donation type:', donationType, 'Show custom:', showCustom);
        
        if (showCustom) {
            customDonationTypeGroup.style.setProperty('display', 'block', 'important');
            customDonationTypeInput.required = true;
        } else {
            customDonationTypeGroup.style.setProperty('display', 'none', 'important');
            customDonationTypeInput.required = false;
            customDonationTypeInput.value = '';
        }
    }

    // Add event listener to the donation type dropdown
    document.addEventListener('change', function(e) {
        if (e.target && e.target.id === 'donation_type' && e.target.closest('#addDonationModal')) {
            updateCustomDonationTypeVisibility();
        }
    });

    // Initialize when modal is shown
    document.addEventListener('shown.bs.modal', function(e) {
        if (e.target && e.target.id === 'addDonationModal') {
            updateCustomDonationTypeVisibility();
        }
    });
});

function viewDonation(button) {
    if (!button) return;
    const data = button.dataset;
    
    // Status styling
    const statusClass = data.status.toLowerCase() === 'verified' ? 'success' : 'warning';
    
    // Donation type styling
    const typeClass = data.type.toLowerCase() === 'general' ? 'primary' :
                     data.type.toLowerCase() === 'building' ? 'info' :
                     data.type.toLowerCase() === 'mission' ? 'success' :
                     data.type.toLowerCase() === 'special' ? 'warning' : 'secondary';
    
    // Payment method styling
    const methodClass = data.method.toLowerCase() === 'cash' ? 'success' :
                       data.method.toLowerCase() === 'bank_transfer' ? 'primary' :
                       data.method.toLowerCase() === 'mobile_money' ? 'info' :
                       data.method.toLowerCase() === 'check' ? 'warning' : 'secondary';

    const html = `
        <div class="row g-4">
            <!-- Donation Overview Cards -->
            <div class="col-12">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="card bg-primary text-white h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-heart fa-2x mb-2"></i>
                                <h6 class="card-title">Donation Amount</h6>
                                <h4 class="mb-0">TZS ${data.amount || '0.00'}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-${statusClass} text-white h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-check-circle fa-2x mb-2"></i>
                                <h6 class="card-title">Status</h6>
                                <h4 class="mb-0">${data.status || '-'}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-${typeClass} text-white h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-gift fa-2x mb-2"></i>
                                <h6 class="card-title">Type</h6>
                                <h4 class="mb-0">${data.type || '-'}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Donor Information -->
            ${data.isAnonymous !== 'true' ? `
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-user me-2"></i>
                            ${data.isMember === 'true' ? 'Member Information' : 'Donor Information'}
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-user text-primary me-3"></i>
                                    <div>
                                        <small class="text-muted">${data.isMember === 'true' ? 'Member Name' : 'Donor Name'}</small>
                                        <div class="fw-bold">${data.donorName && data.donorName !== 'N/A' ? data.donorName : '-'}</div>
                                    </div>
                                </div>
                            </div>
                            ${data.donorEmail && data.donorEmail !== 'N/A' ? `
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-envelope text-info me-3"></i>
                                    <div>
                                        <small class="text-muted">Email</small>
                                        <div class="fw-bold">
                                            <a href="mailto:${data.donorEmail}">${data.donorEmail}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            ` : ''}
                            ${data.donorPhone && data.donorPhone !== 'N/A' ? `
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-phone text-success me-3"></i>
                                    <div>
                                        <small class="text-muted">Phone</small>
                                        <div class="fw-bold">
                                            <a href="tel:${data.donorPhone}">${data.donorPhone}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            ` : ''}
                            ${data.isMember === 'true' && data.memberId ? `
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-id-card text-warning me-3"></i>
                                    <div>
                                        <small class="text-muted">Member ID</small>
                                        <div class="fw-bold">${data.memberId}</div>
                                    </div>
                                </div>
                            </div>
                            ` : ''}
                        </div>
                    </div>
                </div>
            </div>
            ` : `
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-user-secret me-2"></i>Donor Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            This is an anonymous donation. Donor information is not available.
                        </div>
                    </div>
                </div>
            </div>
            `}
            
            <!-- Donation Details -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Donation Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-calendar text-primary me-3"></i>
                                    <div>
                                        <small class="text-muted">Date</small>
                                        <div class="fw-bold">${data.date || '-'}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-tag text-${typeClass} me-3"></i>
                                    <div>
                                        <small class="text-muted">Donation Type</small>
                                        <div class="fw-bold">
                                            <span class="badge bg-${typeClass}">${data.type || '-'}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-credit-card text-${methodClass} me-3"></i>
                                    <div>
                                        <small class="text-muted">Payment Method</small>
                                        <div class="fw-bold">
                                            <span class="badge bg-${methodClass}">${data.method || '-'}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-hashtag text-secondary me-3"></i>
                                    <div>
                                        <small class="text-muted">Reference Number</small>
                                        <div class="fw-bold">${data.reference || '-'}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-flag text-${statusClass} me-3"></i>
                                    <div>
                                        <small class="text-muted">Status</small>
                                        <div class="fw-bold">
                                            <span class="badge bg-${statusClass}">${data.status || '-'}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Remaining Amount Card (Always Zero for Donations) -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-piggy-bank me-2"></i>Payment Status</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-check-circle text-success me-3"></i>
                                    <div>
                                        <small class="text-muted">Amount Paid</small>
                                        <div class="fw-bold text-success">TZS ${data.amount || '0.00'}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-times-circle text-muted me-3"></i>
                                    <div>
                                        <small class="text-muted">Remaining Amount</small>
                                        <div class="fw-bold text-muted">TZS 0.00</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Purpose and Notes -->
            ${(data.purpose && data.purpose !== '-') || (data.notes && data.notes !== '-') ? `
            <div class="col-12">
                <div class="row g-3">
                    ${data.purpose && data.purpose !== '-' ? `
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-bullseye me-2"></i>Purpose</h6>
                            </div>
                            <div class="card-body">
                                <p class="mb-0">${data.purpose}</p>
                            </div>
                        </div>
                    </div>
                    ` : ''}
                    ${data.notes && data.notes !== '-' ? `
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Notes</h6>
                            </div>
                            <div class="card-body">
                                <p class="mb-0">${data.notes}</p>
                            </div>
                        </div>
                    </div>
                    ` : ''}
                </div>
            </div>
            ` : ''}
        </div>
    `;
    
    document.getElementById('viewDonationBody').innerHTML = html;
    const modal = new bootstrap.Modal(document.getElementById('viewDonationModal'));
    modal.show();
}

function editDonation(id) {
    // Implementation for editing donation
    console.log('Edit donation:', id);
}

// Auto-hide alerts after 5 seconds
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    });
}, 5000);
</script>
@endsection

@section('styles')
<style>
    /* Stylish Modal Styles for Donations */
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
        border-color: #0dcaf0;
        box-shadow: 0 0 0 0.2rem rgba(13, 202, 240, 0.15);
        transform: translateY(-1px);
    }
    
    .stylish-modal .form-control:hover,
    .stylish-modal .form-select:hover {
        border-color: #0dcaf0;
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
        box-shadow: 0 6px 20px rgba(13, 202, 240, 0.4) !important;
        background: linear-gradient(135deg, #0bb8d4 0%, #138496 100%) !important;
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
        border: 2px solid #0dcaf0;
        cursor: pointer;
    }
    
    .stylish-modal .form-check-input:checked {
        background-color: #0dcaf0;
        border-color: #0dcaf0;
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
        border-color: #0dcaf0;
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
