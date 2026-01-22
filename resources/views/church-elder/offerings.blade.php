@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0"><i class="fas fa-money-bill-wave me-2 text-success"></i>Record Offerings</h1>
                            <p class="text-muted mb-0">{{ $community->name }}</p>
                        </div>
                        <div>
                            <a href="{{ route('church-elder.dashboard') }}" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted">Total Offerings</h6>
                    <h3 class="text-success mb-0">{{ number_format($offeringStats['total'], 2) }} TZS</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted">Pending Approval</h6>
                    <h3 class="text-warning mb-0">{{ number_format($offeringStats['pending'], 2) }} TZS</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted">Total Records</h6>
                    <h3 class="text-info mb-0">{{ number_format($offeringStats['count']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted">This Month</h6>
                    <h3 class="text-primary mb-0">{{ \Carbon\Carbon::now()->format('M Y') }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Record Offering Form -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Record New Offering</h5>
                </div>
                <div class="card-body">
                    <form id="offeringForm">
                        @csrf
                        <div class="mb-3">
                            <label for="member_id" class="form-label">Member <span class="text-danger">*</span></label>
                            <select class="form-select" id="member_id" name="member_id">
                                <option value="">Anonymous / Non-Member</option>
                                @foreach($members as $member)
                                <option value="{{ $member->id }}">{{ $member->full_name }} ({{ $member->member_id ?? 'N/A' }})</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Leave blank for anonymous offering</small>
                        </div>
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount (TZS) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="amount" name="amount" step="0.01" min="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="offering_date" class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="offering_date" name="offering_date" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="offering_type" class="form-label">Offering Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="offering_type" name="offering_type" required>
                                <option value="general">General Offering</option>
                                <option value="special">Special Offering</option>
                                <option value="thanksgiving">Thanksgiving</option>
                                <option value="building_fund">Building Fund</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                            <select class="form-select" id="payment_method" name="payment_method" required>
                                <option value="cash">Cash</option>
                                <option value="mobile_money">Mobile Money</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes (Optional)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Additional notes..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-save me-1"></i> Record Offering
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Recent Offerings -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Recent Offerings</h5>
                    <a href="{{ route('church-elder.reports', $community->id) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-chart-bar me-1"></i> View Reports
                    </a>
                </div>
                <div class="card-body">
                    @if($recentOfferings->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Member</th>
                                    <th>Amount</th>
                                    <th>Type</th>
                                    <th>Payment Method</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOfferings as $offering)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($offering->offering_date)->format('M d, Y') }}</td>
                                    <td>
                                        @if($offering->member)
                                            {{ $offering->member->full_name }}
                                        @else
                                            <span class="text-muted">Anonymous</span>
                                        @endif
                                    </td>
                                    <td><strong class="text-success">{{ number_format($offering->amount, 2) }} TZS</strong></td>
                                    <td>
                                        <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $offering->offering_type)) }}</span>
                                    </td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $offering->payment_method)) }}</td>
                                    <td>
                                        @if($offering->approval_status === 'approved')
                                            <span class="badge bg-success">Approved</span>
                                        @elseif($offering->approval_status === 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @else
                                            <span class="badge bg-danger">Rejected</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i>No offerings recorded yet. Start by recording an offering using the form on the left.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const offeringForm = document.getElementById('offeringForm');
    
    if (offeringForm) {
        offeringForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            // Show loading
            Swal.fire({
                title: 'Recording Offering...',
                text: 'Please wait while we record the offering.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch('{{ route("church-elder.offerings.store", $community->id) }}', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message || 'Offering recorded successfully.',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    let errorMessage = data.message || 'Failed to record offering.';
                    if (data.errors) {
                        const errorList = Object.values(data.errors).flat();
                        errorMessage = errorList.join('<br>');
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        html: errorMessage
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An error occurred while recording the offering.'
                });
            });
        });
    }
});
</script>
@endsection








