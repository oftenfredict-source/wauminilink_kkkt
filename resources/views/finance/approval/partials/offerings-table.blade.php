@if($records->count() > 0)
<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                @if(isset($canApprove) && $canApprove)
                    <th>
                        <input type="checkbox" id="selectAllOfferings" onchange="toggleAllOfferings()">
                    </th>
                @endif
                <th>Member</th>
                <th>Amount</th>
                <th>Type</th>
                <th>Service</th>
                <th>Date</th>
                <th>Payment Method</th>
                <th>Reference</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $record)
            <tr data-record-type="offering" data-record-id="{{ $record->id }}">
                @if(isset($canApprove) && $canApprove)
                    <td>
                        <input type="checkbox" class="offering-checkbox" value="{{ $record->id }}">
                    </td>
                @endif
                <td>
                    @if($record->member)
                        <strong>{{ $record->member->full_name }}</strong>
                        <br><small class="text-muted">{{ $record->member->member_id }}</small>
                    @else
                        <span class="text-muted">General Member</span>
                    @endif
                </td>
                <td>
                    <strong class="text-success">TZS {{ number_format($record->amount, 0) }}</strong>
                </td>
                <td>
                    <span class="badge bg-dark text-white px-2 py-1">
                        @if($record->offering_type == 'general')
                            General Offering
                        @elseif(in_array($record->offering_type, ['special', 'thanksgiving', 'building_fund']))
                            {{ ucfirst(str_replace('_', ' ', $record->offering_type)) }}
                        @else
                            {{ ucfirst($record->offering_type) }}
                        @endif
                    </span>
                </td>
                <td>
                    @if($record->service_type)
                        <span class="badge bg-info">{{ ucfirst($record->service_type) }}</span>
                    @else
                        <span class="text-muted">N/A</span>
                    @endif
                </td>
                <td>{{ $record->offering_date ? \Carbon\Carbon::parse($record->offering_date)->format('M d, Y') : '-' }}</td>
                <td>
                    <span class="badge bg-info">{{ ucfirst($record->payment_method) }}</span>
                </td>
                <td>{{ $record->reference_number ?? 'N/A' }}</td>
                <td>
                    <div class="btn-group btn-group-sm">
                        @if(isset($canApprove) && $canApprove)
                            <button class="btn btn-success btn-sm" onclick="approveRecord('offering', {{ $record->id }})" title="Approve">
                                <i class="fas fa-check"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="rejectRecord('offering', {{ $record->id }})" title="Reject">
                                <i class="fas fa-times"></i>
                            </button>
                        @endif
                        <button class="btn btn-info btn-sm" onclick="viewDetails('offering', {{ $record->id }})" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
<div class="text-center py-4">
    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
    <h5>No Pending Offerings</h5>
    <p class="text-muted">All offerings for today have been processed.</p>
</div>
@endif

<script>
function toggleAllOfferings() {
    const selectAll = document.getElementById('selectAllOfferings');
    const checkboxes = document.querySelectorAll('.offering-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
}
</script>


