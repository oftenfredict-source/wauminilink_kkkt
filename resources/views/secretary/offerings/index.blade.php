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
                            <p class="text-muted mb-0">Finalize offerings from Evangelism Leader</p>
                        </div>
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

    <!-- Pending Finalization -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Pending Finalization ({{ $offerings->total() }})</h5>
                </div>
                <div class="card-body">
                    @if($offerings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Community</th>
                                        <th>Service Type</th>
                                        <th>Amount (TZS)</th>
                                        <th>From Leader</th>
                                        <th>Received</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($offerings as $offering)
                                    <tr>
                                        <td>{{ $offering->offering_date->format('M d, Y') }}</td>
                                        <td>{{ $offering->community->name }}</td>
                                        <td>
                                            @if($offering->service_type)
                                                <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $offering->service_type)) }}</span>
                                            @else
                                                <span class="badge bg-secondary">General</span>
                                            @endif
                                        </td>
                                        <td><strong>{{ number_format($offering->amount, 2) }}</strong></td>
                                        <td>{{ $offering->evangelismLeader->name ?? 'N/A' }}</td>
                                        <td>{{ $offering->handover_to_evangelism_at->format('M d, Y H:i') }}</td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('secretary.offerings.show', $offering->id) }}" class="btn btn-info" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button type="button" class="btn btn-success" onclick="finalizeOffering({{ $offering->id }})" title="Finalize">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $offerings->links() }}
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i>No pending offerings at this time.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Completed Offerings -->
    @if($completedOfferings->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-check-circle me-2"></i>Completed Offerings ({{ $completedOfferings->total() }})</h5>
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
                                    <th>Finalized</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($completedOfferings as $offering)
                                <tr>
                                    <td>{{ $offering->offering_date->format('M d, Y') }}</td>
                                    <td>{{ $offering->community->name }}</td>
                                    <td>
                                        @if($offering->service_type)
                                            <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $offering->service_type)) }}</span>
                                        @else
                                            <span class="badge bg-secondary">General</span>
                                        @endif
                                    </td>
                                    <td><strong>{{ number_format($offering->amount, 2) }}</strong></td>
                                    <td>{{ $offering->handover_to_secretary_at->format('M d, Y H:i') }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('secretary.offerings.show', $offering->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $completedOfferings->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Finalize Modal -->
<div class="modal fade" id="finalizeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="finalizeForm" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Finalize Offering</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="secretary_notes" class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" id="secretary_notes" name="secretary_notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Finalize</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function finalizeOffering(id) {
    document.getElementById('finalizeForm').action = '{{ url("secretary/offerings") }}/' + id + '/confirm';
    new bootstrap.Modal(document.getElementById('finalizeModal')).show();
}
</script>
@endsection



