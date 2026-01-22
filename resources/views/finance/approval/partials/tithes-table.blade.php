@if($records->count() > 0)
<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                @if(isset($canApprove) && $canApprove)
                    <th>
                        <input type="checkbox" id="selectAllTithes" onchange="toggleAllTithes()">
                    </th>
                @endif
                <th>Branch</th>
                <th>Amount</th>
                <th>Date</th>
                <th>Payment Method</th>
                <th>Reference</th>
                <th>Notes</th>
                <th>Recorded By</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $record)
            <tr data-record-type="tithe" data-record-id="{{ $record->id }}">
                @if(isset($canApprove) && $canApprove)
                    <td>
                        <input type="checkbox" class="tithe-checkbox" value="{{ $record->id }}">
                    </td>
                @endif
                <td>
                    @if($record->campus)
                        <strong>{{ $record->campus->name }}</strong>
                        @if($record->campus->code)
                            <br><small class="text-muted">{{ $record->campus->code }}</small>
                        @endif
                    @elseif($record->evangelismLeader && $record->evangelismLeader->getCampus())
                        <strong>{{ $record->evangelismLeader->getCampus()->name }}</strong>
                        @if($record->evangelismLeader->getCampus()->code)
                            <br><small class="text-muted">{{ $record->evangelismLeader->getCampus()->code }}</small>
                        @endif
                    @else
                        <span class="text-muted">N/A</span>
                    @endif
                </td>
                <td>
                    <strong class="text-success">TZS {{ number_format($record->amount, 0) }}</strong>
                </td>
                <td>{{ $record->tithe_date ? \Carbon\Carbon::parse($record->tithe_date)->format('M d, Y') : '-' }}</td>
                <td>
                    <span class="badge bg-info">{{ ucfirst($record->payment_method) }}</span>
                </td>
                <td>{{ $record->reference_number ?? 'N/A' }}</td>
                <td>{{ Str::limit($record->notes, 30) }}</td>
                <td>{{ $record->recorded_by }}</td>
                <td>
                    <div class="btn-group btn-group-sm">
                        @if(isset($canApprove) && $canApprove)
                            <button class="btn btn-success btn-sm" onclick="console.log('Button clicked'); approveRecord('tithe', {{ $record->id }})" title="Approve">
                                <i class="fas fa-check"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="rejectRecord('tithe', {{ $record->id }})" title="Reject">
                                <i class="fas fa-times"></i>
                            </button>
                        @endif
                        <button class="btn btn-info btn-sm" onclick="viewDetails('tithe', {{ $record->id }})" title="View Details">
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
    <h5>No Pending Tithes</h5>
    <p class="text-muted">All tithes for today have been processed.</p>
</div>
@endif

<script>
function toggleAllTithes() {
    const selectAll = document.getElementById('selectAllTithes');
    const checkboxes = document.querySelectorAll('.tithe-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
}

// viewDetails function is now defined in the main approval dashboard
</script>
