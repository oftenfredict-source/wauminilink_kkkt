@if($records->count() > 0)
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Community</th>
                    <th>Campus</th>
                    <th>Service Type</th>
                    <th>Offering Type</th>
                    <th>Amount (TZS)</th>
                    <th>Collection Method</th>
                    <th>Date</th>
                    <th>From Leader</th>
                    <th>Received</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($records as $record)
                    <tr>
                        <td>
                            <strong>{{ $record->community->name }}</strong>
                        </td>
                        <td>
                            <span class="badge bg-primary">{{ $record->community->campus->name ?? 'N/A' }}</span>
                        </td>
                        <td>
                            @if($record->service_type)
                                <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $record->service_type)) }}</span>
                            @else
                                <span class="badge bg-secondary">General</span>
                            @endif
                            @if($record->service)
                                <br><small class="text-muted">{{ $record->service->service_date->format('M d, Y') }}</small>
                            @endif
                        </td>
                        <td>
                            @if($record->offering_type)
                                <span class="badge bg-success">{{ $record->offering_type }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <strong class="text-success">TZS {{ number_format($record->amount, 2) }}</strong>
                        </td>
                        <td>
                            <span
                                class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $record->collection_method)) }}</span>
                            @if($record->reference_number)
                                <br><small class="text-muted">Ref: {{ $record->reference_number }}</small>
                            @endif
                        </td>
                        <td>
                            {{ $record->offering_date->format('M d, Y') }}
                        </td>
                        <td>
                            {{ $record->evangelismLeader ? $record->evangelismLeader->name : 'N/A' }}
                        </td>
                        <td>
                            <small class="text-muted">
                                @if($record->handover_to_evangelism_at)
                                    {{ $record->handover_to_evangelism_at->format('M d, Y H:i') }}
                                @else
                                    {{ $record->created_at->format('M d, Y H:i') }}
                                @endif
                            </small>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('secretary.offerings.show', $record->id) }}" class="btn btn-info"
                                    title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if(auth()->user()->isSecretary() || auth()->user()->isAdmin())
                                    <button type="button" class="btn btn-success"
                                        onclick="finalizeCommunityOffering({{ $record->id }})" title="Finalize">
                                        <i class="fas fa-check"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="table-primary">
                    <th colspan="4" class="text-end">Total:</th>
                    <th class="text-success">TZS {{ number_format($records->sum('amount'), 2) }}</th>
                    <th colspan="5"></th>
                </tr>
            </tfoot>
        </table>
    </div>
@else
    <div class="alert alert-info mb-0">
        <i class="fas fa-info-circle me-2"></i>No pending community offerings from Evangelism Leader at this time.
    </div>
@endif

<!-- Finalize Modal for Community Offerings -->
<div class="modal fade" id="finalizeCommunityOfferingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="finalizeCommunityOfferingForm" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Finalize Community Offering</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="secretary_notes" class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" id="secretary_notes" name="secretary_notes" rows="3"
                            placeholder="Add any notes about finalizing this offering..."></textarea>
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
    function finalizeCommunityOffering(id) {
        document.getElementById('finalizeCommunityOfferingForm').action = '{{ route("secretary.offerings.confirm", ":id") }}'.replace(':id', id);
        new bootstrap.Modal(document.getElementById('finalizeCommunityOfferingModal')).show();
    }
</script>