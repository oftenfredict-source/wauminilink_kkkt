@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0"><i class="fas fa-money-bill-wave me-2 text-success"></i>Community Offerings</h1>
                            <p class="text-muted mb-0">Review and confirm offerings from communities</p>
                        </div>
                        @if(Auth::user()->isAdmin() || Auth::user()->isPastor())
                        <!-- Consolidated view hidden for now as per user request -->
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Pending Offerings removed as per user request -->


    <!-- Confirmed Offerings -->
    @if($confirmedOfferings->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Submitted Community Offerings</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Community</th>
                                    <th>Service Type</th>
                                    <th>Amount (TZS)</th>
                                    <th>Confirmed</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($confirmedOfferings as $offering)
                                <tr>
                                    <td>{{ $offering->offering_date ? $offering->offering_date->format('M d, Y') : 'N/A' }}</td>
                                    <td>{{ $offering->community->name }}</td>
                                    <td>
                                        @if($offering->service_type)
                                            <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $offering->service_type)) }}</span>
                                        @else
                                            <span class="badge bg-secondary">General</span>
                                        @endif
                                    </td>
                                    <td><strong>{{ number_format($offering->amount, 2) }}</strong></td>
                                    <td>{{ $offering->handover_to_evangelism_at ? $offering->handover_to_evangelism_at->format('M d, Y H:i') : 'N/A' }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('evangelism-leader.offerings.show', $offering->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $confirmedOfferings->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Confirm Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="confirmForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Offering</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="leader_notes" class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" id="leader_notes" name="leader_notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Reject Offering</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function confirmOffering(id) {
    document.getElementById('confirmForm').action = '{{ route("evangelism-leader.offerings.confirm", ":id") }}'.replace(':id', id);
    new bootstrap.Modal(document.getElementById('confirmModal')).show();
}

function rejectOffering(id) {
    document.getElementById('rejectForm').action = '{{ route("evangelism-leader.offerings.reject", ":id") }}'.replace(':id', id);
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}

function selectAll() {
    document.querySelectorAll('.offering-checkbox').forEach(cb => cb.checked = true);
    document.getElementById('selectAllCheckbox').checked = true;
}

function deselectAll() {
    document.querySelectorAll('.offering-checkbox').forEach(cb => cb.checked = false);
    document.getElementById('selectAllCheckbox').checked = false;
}

function toggleAll() {
    const checked = document.getElementById('selectAllCheckbox').checked;
    document.querySelectorAll('.offering-checkbox').forEach(cb => cb.checked = checked);
}
</script>
@endsection

