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
            <a href="{{ route('evangelism-leader.bereavement.edit', $bereavement->id) }}" class="btn btn-outline-warning">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <form id="closeBereavementForm" action="{{ route('evangelism-leader.bereavement.close', $bereavement->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="button" class="btn btn-outline-danger" onclick="confirmCloseBereavement()">
                    <i class="fas fa-lock me-2"></i>Close Event
                </button>
            </form>
            @endif
            <a href="{{ route('evangelism-leader.bereavement.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(isset($campus))
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        Showing contributions from <strong>{{ $campus->name }}</strong> branch members only.
    </div>
    @endif

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
                    @if($bereavement->community)
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <strong>Community:</strong>
                            <div>
                                <span class="badge bg-primary">{{ $bereavement->community->name }}</span>
                            </div>
                        </div>
                    </div>
                    @endif
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
                                @php
                                    $endDate = \Carbon\Carbon::parse($bereavement->contribution_end_date)->endOfDay();
                                    $now = \Carbon\Carbon::now();
                                    $daysRemaining = (int) round($now->diffInDays($endDate, false));
                                @endphp
                                @if($daysRemaining >= 0)
                                <span class="badge bg-info">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ $daysRemaining }} {{ $daysRemaining == 1 ? 'day' : 'days' }} remaining
                                </span>
                                @else
                                <span class="badge bg-warning">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    Contribution period ended {{ abs($daysRemaining) }} {{ abs($daysRemaining) == 1 ? 'day' : 'days' }} ago
                                </span>
                                @endif
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
                            <div class="h4 mb-0 text-primary">{{ $totalContributors }}</div>
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
                            <div class="h4 mb-0 text-secondary">{{ $totalMembers - $totalContributors }}</div>
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
                        Contributors ({{ $totalContributors }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#nonContributors">
                        Not Yet Contributed ({{ $totalMembers - $totalContributors }})
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
                                    <th>Payment Method</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($contributions->where('has_contributed', true) as $contribution)
                                <tr>
                                    <td>
                                        <span class="contribution-status status-contributed"></span>
                                        {{ $contribution->member->full_name ?? 'N/A' }}
                                    </td>
                                    <td class="fw-bold text-success">TZS {{ number_format($contribution->contribution_amount ?? 0, 2) }}</td>
                                    <td>{{ $contribution->contribution_date ? \Carbon\Carbon::parse($contribution->contribution_date)->format('M j, Y') : '—' }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $contribution->payment_method ?? '—')) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
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
                                @forelse($contributions->where('has_contributed', false) as $contribution)
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
@if($bereavement->isOpen())
<div class="modal fade" id="recordContributionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Record Contribution</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('evangelism-leader.bereavement.record-contribution', $bereavement->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Member <span class="text-danger">*</span></label>
                        <select name="member_id" class="form-select @error('member_id') is-invalid @enderror" required>
                            <option value="">Select Member</option>
                            @foreach($contributions->where('has_contributed', false) as $contribution)
                            <option value="{{ $contribution->member_id }}">{{ $contribution->member->full_name ?? 'N/A' }} ({{ $contribution->member->member_id ?? 'N/A' }})</option>
                            @endforeach
                        </select>
                        @error('member_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if($contributions->where('has_contributed', false)->isEmpty())
                        <small class="text-muted">All members have already contributed to this event.</small>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror" step="0.01" min="0" required>
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contribution Date <span class="text-danger">*</span></label>
                        <input type="date" name="contribution_date" class="form-control @error('contribution_date') is-invalid @enderror" value="{{ date('Y-m-d') }}" required>
                        @error('contribution_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
                            <option value="cash">Cash</option>
                            <option value="mobile_money">Mobile Money</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="other">Other</option>
                        </select>
                        @error('payment_method')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="2"></textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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
}

function confirmCloseBereavement() {
    Swal.fire({
        title: 'Close Bereavement Event?',
        html: '<p>Are you sure you want to close this bereavement event?</p><p class="text-danger"><strong>This action cannot be undone.</strong></p>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-lock me-2"></i>Yes, Close Event',
        cancelButtonText: '<i class="fas fa-times me-2"></i>Cancel',
        reverseButtons: true,
        focusCancel: true,
        customClass: {
            popup: 'swal2-popup-custom',
            confirmButton: 'swal2-confirm-custom',
            cancelButton: 'swal2-cancel-custom'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state
            Swal.fire({
                title: 'Closing Event...',
                text: 'Please wait while we close the bereavement event.',
                icon: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Submit the form
            document.getElementById('closeBereavementForm').submit();
        }
    });
}
</script>
@endif
@endsection

