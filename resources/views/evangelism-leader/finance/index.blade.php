@extends('layouts.index')

@section('title', 'Finance Management')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0"><i class="fas fa-coins me-2 text-primary"></i>Finance Management</h1>
                            <p class="text-muted mb-0">{{ $campus->name }}</p>
                        </div>
                        <div>
                            <span class="badge bg-primary fs-6">{{ $campus->code }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Pending Offerings</h6>
                            <h3 class="mb-0">TZS {{ number_format($pendingOfferingsTotal, 2) }}</h3>
                            <small class="text-muted">{{ $pendingOfferings->count() }} record(s)</small>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-money-bill-wave fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Pending Tithes</h6>
                            <h3 class="mb-0">TZS {{ number_format($pendingTithesTotal, 2) }}</h3>
                            <small class="text-muted">{{ $pendingTithes->count() }} record(s)</small>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-hand-holding-usd fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Total Pending</h6>
                            <h3 class="mb-0">TZS {{ number_format($pendingOfferingsTotal + $pendingTithesTotal, 2) }}</h3>
                            <small class="text-muted">{{ $pendingOfferings->count() + $pendingTithes->count() }} record(s)</small>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <ul class="nav nav-tabs" id="financeTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="offerings-tab" data-bs-toggle="tab" data-bs-target="#offerings" type="button" role="tab" aria-controls="offerings" aria-selected="true">
                        <i class="fas fa-money-bill-wave me-2"></i>Offerings (Individual)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tithes-tab" data-bs-toggle="tab" data-bs-target="#tithes" type="button" role="tab" aria-controls="tithes" aria-selected="false">
                        <i class="fas fa-hand-holding-usd me-2"></i>Tithes (Aggregate)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="submitted-tab" data-bs-toggle="tab" data-bs-target="#submitted" type="button" role="tab" aria-controls="submitted" aria-selected="false">
                        <i class="fas fa-paper-plane me-2"></i>Submitted to Secretary
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <div class="tab-content" id="financeTabsContent">
        <!-- Offerings Tab -->
        <div class="tab-pane fade show active" id="offerings" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Record Individual Offerings</h5>
                    <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#addOfferingModal">
                        <i class="fas fa-plus me-1"></i>Add Offering
                    </button>
                </div>
                <div class="card-body">
                    @if($pendingOfferings->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Member</th>
                                    <th>Amount</th>
                                    <th>Type</th>
                                    <th>Date</th>
                                    <th>Payment Method</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingOfferings as $offering)
                                <tr>
                                    <td>{{ $offering->member->full_name ?? 'N/A' }}</td>
                                    <td>TZS {{ number_format($offering->amount, 2) }}</td>
                                    <td><span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $offering->offering_type)) }}</span></td>
                                    <td>{{ $offering->offering_date->format('M d, Y') }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $offering->payment_method)) }}</td>
                                    <td>
                                        @if($offering->submitted_to_secretary)
                                            @if($offering->approval_status === 'approved')
                                                <span class="badge bg-success">Approved</span>
                                            @elseif($offering->approval_status === 'rejected')
                                                <span class="badge bg-danger">Rejected</span>
                                            @else
                                                <span class="badge bg-warning">Pending Approval</span>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">Pending Submission</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('evangelism-leader.finance.offerings.show', $offering->id) }}" class="btn btn-sm btn-info" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if(!$offering->submitted_to_secretary)
                                            <a href="{{ route('evangelism-leader.finance.offerings.edit', $offering->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('evangelism-leader.finance.offerings.submit') }}" method="POST" class="d-inline" onsubmit="return confirmSubmit('offering')">
                                                @csrf
                                                <input type="hidden" name="offering_ids[]" value="{{ $offering->id }}">
                                                <button type="submit" class="btn btn-sm btn-success" title="Send to Secretary">
                                                    <i class="fas fa-paper-plane"></i>
                                                </button>
                                            </form>
                                            @else
                                            <span class="text-muted small">Submitted</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>No pending offerings. Click "Add Offering" to record a new offering.
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tithes Tab -->
        <div class="tab-pane fade" id="tithes" role="tabpanel" aria-labelledby="tithes-tab">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-hand-holding-usd me-2"></i>Record Aggregate Tithes</h5>
                    <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#addTitheModal">
                        <i class="fas fa-plus me-1"></i>Add Tithes
                    </button>
                </div>
                <div class="card-body">
                    @if($pendingTithes->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Total Amount</th>
                                    <th>Payment Method</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingTithes as $tithe)
                                <tr>
                                    <td>{{ $tithe->tithe_date->format('M d, Y') }}</td>
                                    <td><strong>TZS {{ number_format($tithe->amount, 2) }}</strong></td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $tithe->payment_method)) }}</td>
                                    <td>
                                        @if($tithe->submitted_to_secretary)
                                            @if($tithe->approval_status === 'approved')
                                                <span class="badge bg-success">Approved</span>
                                            @elseif($tithe->approval_status === 'rejected')
                                                <span class="badge bg-danger">Rejected</span>
                                            @else
                                                <span class="badge bg-warning">Pending Approval</span>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">Pending Submission</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('evangelism-leader.finance.tithes.show', $tithe->id) }}" class="btn btn-sm btn-info" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if(!$tithe->submitted_to_secretary)
                                            <a href="{{ route('evangelism-leader.finance.tithes.edit', $tithe->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('evangelism-leader.finance.tithes.submit') }}" method="POST" class="d-inline" onsubmit="return confirmSubmit('tithe')">
                                                @csrf
                                                <input type="hidden" name="tithe_ids[]" value="{{ $tithe->id }}">
                                                <button type="submit" class="btn btn-sm btn-success" title="Send to Secretary">
                                                    <i class="fas fa-paper-plane"></i>
                                                </button>
                                            </form>
                                            @else
                                            <span class="text-muted small">Submitted</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>No pending tithes. Click "Add Tithes" to record aggregate tithes.
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Submitted Tab -->
        <div class="tab-pane fade" id="submitted" role="tabpanel" aria-labelledby="submitted-tab">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-paper-plane me-2"></i>Submitted to Secretary (Awaiting Approval)</h5>
                </div>
                <div class="card-body">
                    <h6 class="mb-3">Submitted Offerings</h6>
                    @if($submittedOfferings->count() > 0)
                    <div class="table-responsive mb-4">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Member</th>
                                    <th>Amount</th>
                                    <th>Type</th>
                                    <th>Date</th>
                                    <th>Payment Method</th>
                                    <th>Status</th>
                                    <th>Submitted At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($submittedOfferings as $offering)
                                <tr>
                                    <td>{{ $offering->member->full_name ?? 'N/A' }}</td>
                                    <td>TZS {{ number_format($offering->amount, 2) }}</td>
                                    <td><span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $offering->offering_type)) }}</span></td>
                                    <td>{{ $offering->offering_date->format('M d, Y') }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $offering->payment_method)) }}</td>
                                    <td>
                                        @if($offering->approval_status === 'approved')
                                            <span class="badge bg-success">Approved</span>
                                        @elseif($offering->approval_status === 'rejected')
                                            <span class="badge bg-danger">Rejected</span>
                                        @else
                                            <span class="badge bg-warning">Pending Approval</span>
                                        @endif
                                    </td>
                                    <td>{{ $offering->submitted_at ? $offering->submitted_at->format('M d, Y H:i') : 'N/A' }}</td>
                                    <td>
                                        <a href="{{ route('evangelism-leader.finance.offerings.show', $offering->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info mb-4">No submitted offerings.</div>
                    @endif

                    <h6 class="mb-3">Submitted Tithes</h6>
                    @if($submittedTithes->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Total Amount</th>
                                    <th>Payment Method</th>
                                    <th>Status</th>
                                    <th>Submitted At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($submittedTithes as $tithe)
                                <tr>
                                    <td>{{ $tithe->tithe_date->format('M d, Y') }}</td>
                                    <td><strong>TZS {{ number_format($tithe->amount, 2) }}</strong></td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $tithe->payment_method)) }}</td>
                                    <td>
                                        @if($tithe->approval_status === 'approved')
                                            <span class="badge bg-success">Approved</span>
                                        @elseif($tithe->approval_status === 'rejected')
                                            <span class="badge bg-danger">Rejected</span>
                                        @else
                                            <span class="badge bg-warning">Pending Approval</span>
                                        @endif
                                    </td>
                                    <td>{{ $tithe->submitted_at ? $tithe->submitted_at->format('M d, Y H:i') : 'N/A' }}</td>
                                    <td>
                                        <a href="{{ route('evangelism-leader.finance.tithes.show', $tithe->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info">No submitted tithes.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Offering Modal -->
<div class="modal fade" id="addOfferingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('evangelism-leader.finance.offerings.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-money-bill-wave me-2"></i>Record Individual Offering</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Member <span class="text-danger">*</span></label>
                        <select name="member_id" class="form-select" required>
                            <option value="">Select Member</option>
                            @foreach($members as $member)
                            <option value="{{ $member->id }}">{{ $member->full_name }} ({{ $member->member_id }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount (TZS) <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Offering Date <span class="text-danger">*</span></label>
                        <input type="date" name="offering_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Offering Type <span class="text-danger">*</span></label>
                        <select name="offering_type" class="form-select" required>
                            <option value="general">General</option>
                            <option value="special">Special</option>
                            <option value="thanksgiving">Thanksgiving</option>
                            <option value="building_fund">Building Fund</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-select" required>
                            <option value="cash">Cash</option>
                            <option value="check">Check</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="mobile_money">Mobile Money</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reference Number</label>
                        <input type="text" name="reference_number" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Record Offering</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Tithe Modal -->
<div class="modal fade" id="addTitheModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('evangelism-leader.finance.tithes.store') }}" method="POST" id="titheForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-hand-holding-usd me-2"></i>Record Aggregate Tithes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Note:</strong> This records the total tithe amount collected from all members. Individual member contributions are not tracked.
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tithe Date <span class="text-danger">*</span></label>
                        <input type="date" name="tithe_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Total Amount (TZS) <span class="text-danger">*</span></label>
                        <input type="number" name="total_amount" id="totalAmount" class="form-control" step="0.01" min="0" required>
                        <small class="text-muted">Enter the total tithe amount collected from all members</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-select" required>
                            <option value="cash">Cash</option>
                            <option value="check">Check</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="mobile_money">Mobile Money</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reference Number</label>
                        <input type="text" name="reference_number" class="form-control" placeholder="Optional reference number">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Optional notes about this tithe collection"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="recordTitheBtn">Record Tithe</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Offering checkbox handlers
function toggleAllOfferings(checkbox) {
    const checkboxes = document.querySelectorAll('.offering-checkbox');
    checkboxes.forEach(cb => cb.checked = checkbox.checked);
    updateOfferingsSubmitButton();
    updateOfferingsTotal();
}

function updateOfferingsSubmitButton() {
    const checked = document.querySelectorAll('.offering-checkbox:checked').length;
    const btn = document.getElementById('submitOfferingsBtn');
    if (btn) {
        btn.disabled = checked === 0;
    }
}

function updateOfferingsTotal() {
    let total = 0;
    document.querySelectorAll('.offering-checkbox:checked').forEach(checkbox => {
        const row = checkbox.closest('tr');
        const amountText = row.cells[2].textContent;
        const amount = parseFloat(amountText.replace(/[^0-9.]/g, ''));
        total += amount;
    });
    const totalEl = document.getElementById('selectedOfferingsTotal');
    if (totalEl) {
        totalEl.textContent = 'TZS ' + total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }
}

// Tithe checkbox handlers
function toggleAllTithes(checkbox) {
    const checkboxes = document.querySelectorAll('.tithe-checkbox');
    checkboxes.forEach(cb => cb.checked = checkbox.checked);
    updateTithesSubmitButton();
    updateTithesTotal();
}

function updateTithesSubmitButton() {
    const checked = document.querySelectorAll('.tithe-checkbox:checked').length;
    const btn = document.getElementById('submitTithesBtn');
    if (btn) {
        btn.disabled = checked === 0;
    }
}

function updateTithesTotal() {
    let total = 0;
    document.querySelectorAll('.tithe-checkbox:checked').forEach(checkbox => {
        total += parseFloat(checkbox.dataset.amount);
    });
    const totalEl = document.getElementById('selectedTithesTotal');
    if (totalEl) {
        totalEl.textContent = 'TZS ' + total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }
}

// View offering details
function viewOffering(offeringId) {
    window.location.href = '/evangelism-leader/finance/offerings/' + offeringId;
}

// Edit offering
function editOffering(offeringId) {
    window.location.href = '/evangelism-leader/finance/offerings/' + offeringId + '/edit';
}

// View tithe details
function viewTithe(titheId) {
    window.location.href = '/evangelism-leader/finance/tithes/' + titheId;
}

// Edit tithe
function editTithe(titheId) {
    window.location.href = '/evangelism-leader/finance/tithes/' + titheId + '/edit';
}

// Confirm submission
function confirmSubmit(type) {
    const typeName = type === 'offering' ? 'offering' : 'tithe';
    return confirm(`Are you sure you want to send this ${typeName} to the secretary?`);
}

// Initialize everything on page load
document.addEventListener('DOMContentLoaded', function() {
    // Offering checkboxes
    document.querySelectorAll('.offering-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateOfferingsSubmitButton();
            updateOfferingsTotal();
        });
    });
    
    // Tithe checkboxes
    document.querySelectorAll('.tithe-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateTithesSubmitButton();
            updateTithesTotal();
        });
    });
    
    // Form submission handler for offerings
    const submitOfferingsForm = document.getElementById('submitOfferingsForm');
    if (submitOfferingsForm) {
        submitOfferingsForm.addEventListener('submit', function(e) {
            const checked = document.querySelectorAll('.offering-checkbox:checked').length;
            if (checked === 0) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'No Selection',
                    text: 'Please select at least one offering to submit.'
                });
                return false;
            }
            
            // Show loading state
            const btn = document.getElementById('submitOfferingsBtn');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';
            }
        });
    }
    
    // Form submission handler for tithes
    const submitTithesForm = document.getElementById('submitTithesForm');
    if (submitTithesForm) {
        submitTithesForm.addEventListener('submit', function(e) {
            const checked = document.querySelectorAll('.tithe-checkbox:checked').length;
            if (checked === 0) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'No Selection',
                    text: 'Please select at least one tithe record to submit.'
                });
                return false;
            }
            
            // Show loading state
            const btn = document.getElementById('submitTithesBtn');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';
            }
        });
    }

// Simple tithe form validation (for modal form)
document.addEventListener('DOMContentLoaded', function() {
    const titheForm = document.getElementById('titheForm');
    const totalAmountInput = document.getElementById('totalAmount');
    const recordTitheBtn = document.getElementById('recordTitheBtn');
    
    if (titheForm && totalAmountInput && recordTitheBtn) {
        // Enable/disable button based on amount
        totalAmountInput.addEventListener('input', function() {
            const totalAmount = parseFloat(this.value) || 0;
            recordTitheBtn.disabled = totalAmount <= 0;
        });
        
        // Form submission validation
        titheForm.addEventListener('submit', function(e) {
            const totalAmount = parseFloat(totalAmountInput.value) || 0;
            if (totalAmount <= 0) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Amount',
                    text: 'Please enter a valid total amount greater than 0.'
                });
                return false;
            }
            
            // Show loading state
            recordTitheBtn.disabled = true;
            recordTitheBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Recording...';
        });
    }
});

function deleteOffering(id) {
    if (confirm('Are you sure you want to delete this offering?')) {
        // Implement delete functionality if needed
        alert('Delete functionality to be implemented');
    }
}

function viewTitheNotes(titheId, notes) {
    Swal.fire({
        title: 'Tithe Notes',
        html: '<div class="text-start"><p>' + (notes ? notes.replace(/\n/g, '<br>') : 'No notes available') + '</p></div>',
        icon: 'info',
        confirmButtonText: 'Close'
    });
}

// Initialize Bootstrap tabs on page load
document.addEventListener('DOMContentLoaded', function() {
    // Bootstrap tabs should work automatically with data-bs-toggle
    // But ensure they're visible
    const tabs = document.querySelectorAll('#financeTabs button[data-bs-toggle="tab"]');
    tabs.forEach(function(tab) {
        tab.addEventListener('shown.bs.tab', function (event) {
            console.log('Tab shown:', event.target.id);
        });
    });
    
    // Make sure tabs are visible
    const tabContainer = document.getElementById('financeTabs');
    if (tabContainer) {
        tabContainer.style.display = 'flex';
    }
});
</script>
@endpush
@endsection

