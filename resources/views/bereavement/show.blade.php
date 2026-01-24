@extends('layouts.index')

@section('content')
<style>
    .contribution-status {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 8px;
    }
    .status-contributed {
        background-color: #28a745;
    }
    .status-not-contributed {
        background-color: #6c757d;
    }
    .stat-card {
        border-left: 4px solid;
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
</style>

<div class="container-fluid px-4">
    <div class="d-flex align-items-center justify-content-between mt-4 mb-3">
        <h2 class="mb-0">🕊️ Bereavement Event Details</h2>
        <div class="d-flex gap-2">
            @if($bereavement->isOpen())
            <button class="btn btn-outline-primary" onclick="openRecordContributionModal()">
                <i class="fas fa-plus me-2"></i>Record Contribution
            </button>
            <button class="btn btn-outline-warning" onclick="closeEvent()">
                <i class="fas fa-lock me-2"></i>Close Event
            </button>
            @endif
            <a href="{{ route('bereavement.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
        </div>
    </div>

    <!-- Event Information -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Event Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Deceased / Affected Family:</strong>
                            <div class="fs-5 fw-bold">{{ $bereavement->deceased_name }}</div>
                        </div>
                        <div class="col-md-6">
                            <strong>Status:</strong>
                            <div>
                                <span class="badge bg-{{ $bereavement->isOpen() ? 'success' : 'secondary' }}">
                                    {{ ucfirst($bereavement->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Incident Date:</strong>
                            <div>{{ $bereavement->incident_date->format('F j, Y') }}</div>
                        </div>
                        <div class="col-md-6">
                            <strong>Contribution Period:</strong>
                            <div>
                                {{ $bereavement->contribution_start_date->format('M j, Y') }} - 
                                {{ $bereavement->contribution_end_date->format('M j, Y') }}
                            </div>
                            @if($bereavement->isOpen())
                            <div class="mt-2">
                                <span class="badge bg-info">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ $daysRemaining }} days remaining
                                </span>
                            </div>
                            @endif
                        </div>
                    </div>
                    @if($bereavement->family_details)
                    <div class="mb-3">
                        <strong>Family Details:</strong>
                        <div>{{ $bereavement->family_details }}</div>
                    </div>
                    @endif
                    @if($bereavement->related_departments)
                    <div class="mb-3">
                        <strong>Related Departments:</strong>
                        <div>{{ $bereavement->related_departments }}</div>
                    </div>
                    @endif
                    @if($bereavement->notes)
                    <div class="mb-3">
                        <strong>Notes:</strong>
                        <div>{{ $bereavement->notes }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card border-left-success mb-3">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-muted small">Total Contributions</div>
                            <div class="h4 mb-0 text-success">TZS {{ number_format($totalContributions, 2) }}</div>
                        </div>
                        <i class="fas fa-money-bill-wave fa-2x text-success"></i>
                    </div>
                </div>
            </div>
            <div class="card stat-card border-left-primary mb-3">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-muted small">Contributors</div>
                            <div class="h4 mb-0 text-primary">{{ $contributorsCount }}</div>
                        </div>
                        <i class="fas fa-users fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
            <div class="card stat-card border-left-secondary">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-muted small">Not Yet Contributed</div>
                            <div class="h4 mb-0 text-secondary">{{ $nonContributorsCount }}</div>
                        </div>
                        <i class="fas fa-user-times fa-2x text-secondary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contributions Tabs -->
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#contributors">
                        Contributors ({{ $contributorsCount }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#nonContributors">
                        Not Yet Contributed ({{ $nonContributorsCount }})
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <!-- Contributors Tab -->
                <div class="tab-pane fade show active" id="contributors">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Member</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Payment Method</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($contributors as $contribution)
                                <tr>
                                    <td>
                                        <span class="contribution-status status-contributed"></span>
                                        {{ $contribution->member->full_name ?? 'N/A' }}
                                    </td>
                                    <td class="fw-bold text-success">TZS {{ number_format($contribution->contribution_amount, 2) }}</td>
                                    <td>{{ $contribution->contribution_date ? $contribution->contribution_date->format('M j, Y') : '—' }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $contribution->contribution_type)) }}</span>
                                    </td>
                                    <td>{{ $contribution->payment_method ?? '—' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        No contributions recorded yet
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Non-Contributors Tab -->
                <div class="tab-pane fade" id="nonContributors">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Member</th>
                                    <th>Member ID</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($nonContributors as $contribution)
                                <tr>
                                    <td>
                                        <span class="contribution-status status-not-contributed"></span>
                                        {{ $contribution->member->full_name ?? 'N/A' }}
                                    </td>
                                    <td>{{ $contribution->member->member_id ?? '—' }}</td>
                                    <td>
                                        <span class="badge bg-secondary">Not Contributed</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">
                                        All members have contributed
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Record Contribution Modal -->
<div class="modal fade" id="recordContributionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Record Contribution</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="contributionForm">
                <div class="modal-body">
                    <input type="hidden" name="bereavement_id" value="{{ $bereavement->id }}">
                    <div class="mb-3">
                        <label class="form-label">Member <span class="text-danger">*</span></label>
                        <select name="member_id" id="member_id_select" class="form-select" required>
                            <option value="">Select Member</option>
                            @foreach($availableMembers as $member)
                            <option value="{{ $member->id }}" data-member-name="{{ $member->full_name }}" data-member-id="{{ $member->member_id }}">
                                {{ $member->full_name }} ({{ $member->member_id }})
                            </option>
                            @endforeach
                        </select>
                        @if($availableMembers->isEmpty())
                        <small class="text-muted">All members have already contributed to this event.</small>
                        @endif
                        <div id="member_info" class="mt-2" style="display: none;">
                            <div class="alert alert-info mb-0">
                                <small><strong>Selected Member:</strong> <span id="selected_member_name"></span></small>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount <span class="text-danger">*</span></label>
                        <input type="number" name="contribution_amount" class="form-control" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contribution Date <span class="text-danger">*</span></label>
                        <input type="date" name="contribution_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contribution Type <span class="text-danger">*</span></label>
                        <select name="contribution_type" class="form-select" required>
                            <option value="individual">Individual</option>
                            <option value="family_wide">Family-wide</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                        <select name="payment_method" id="payment_method" class="form-select" required>
                            <option value="cash">Cash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="mobile_money">Mobile Money</option>
                        </select>
                    </div>
                    <div class="mb-3" id="reference_number_group" style="display: none;">
                        <label class="form-label">Reference Number <span class="text-danger">*</span></label>
                        <input type="text" name="reference_number" id="reference_number" class="form-control" placeholder="Enter transaction reference number">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Record Contribution</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openRecordContributionModal() {
    const modal = new bootstrap.Modal(document.getElementById('recordContributionModal'));
    modal.show();
    
    // Reset form and hide reference number and member info by default
    document.getElementById('contributionForm').reset();
    document.getElementById('reference_number_group').style.display = 'none';
    document.getElementById('reference_number').removeAttribute('required');
    document.getElementById('member_info').style.display = 'none';
}

// Handle payment method change and member selection
document.addEventListener('DOMContentLoaded', function() {
    const paymentMethodSelect = document.getElementById('payment_method');
    const referenceNumberGroup = document.getElementById('reference_number_group');
    const referenceNumberInput = document.getElementById('reference_number');
    const memberSelect = document.getElementById('member_id_select');
    const memberInfo = document.getElementById('member_info');
    const selectedMemberName = document.getElementById('selected_member_name');
    
    // Handle member selection
    if (memberSelect) {
        memberSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (this.value && selectedOption) {
                const memberName = selectedOption.getAttribute('data-member-name') || selectedOption.text;
                selectedMemberName.textContent = memberName;
                memberInfo.style.display = 'block';
            } else {
                memberInfo.style.display = 'none';
            }
        });
    }
    
    // Handle payment method change
    if (paymentMethodSelect) {
        paymentMethodSelect.addEventListener('change', function() {
            if (this.value === 'cash') {
                referenceNumberGroup.style.display = 'none';
                referenceNumberInput.removeAttribute('required');
                referenceNumberInput.value = '';
            } else {
                referenceNumberGroup.style.display = 'block';
                referenceNumberInput.setAttribute('required', 'required');
            }
        });
    }
});

function closeEvent() {
    if (confirm('Are you sure you want to close this bereavement event? This action cannot be undone.')) {
        fetch('{{ route("bereavement.close", $bereavement->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        });
    }
}

document.getElementById('contributionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('{{ route("bereavement.record-contribution", $bereavement->id) }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to record contribution'));
        }
    });
});
</script>
@endsection

