@if($records->count() > 0)
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Expense Name</th>
                    <th>Budget</th>
                    <th>Req. Amount</th>
                    <th>Available</th>
                    <th>Shortfall</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($records as $record)
                    <tr data-record-type="funding_request" data-record-id="{{ $record->id }}">
                        <td>
                            <strong>{{ $record->expense->expense_name ?? 'N/A' }}</strong>
                            @if($record->reason)
                                <br><small class="text-muted">{{ Str::limit($record->reason, 50) }}</small>
                            @endif
                        </td>
                        <td>
                            @if($record->budget)
                                <span class="badge bg-info">{{ $record->budget->budget_name }}</span>
                            @else
                                <span class="text-muted">No Budget</span>
                            @endif
                        </td>
                        <td>
                            <strong class="text-dark">TZS {{ number_format($record->requested_amount, 0) }}</strong>
                        </td>
                        <td>
                            <span class="text-success">TZS {{ number_format($record->available_amount, 0) }}</span>
                        </td>
                        <td>
                            <strong class="text-danger">TZS {{ number_format($record->shortfall_amount, 0) }}</strong>
                            @if($record->shortfall_percentage > 0)
                                <br><small class="text-muted">({{ $record->shortfall_percentage }}% short)</small>
                            @endif
                        </td>
                        <td>{{ $record->created_at->format('M d, Y') }}</td>
                        <td>
                            <span class="badge badge-warning">{{ ucfirst($record->status) }}</span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                @if(Route::has('finance.approval.funding-requests.details'))
                                    <a href="{{ route('finance.approval.funding-requests.details', $record->id) }}"
                                        class="btn btn-info btn-sm" title="Review & Allocate">
                                        <i class="fas fa-tasks"></i> Review
                                    </a>
                                @else
                                    <button class="btn btn-info btn-sm" onclick="viewDetails('funding_request', {{ $record->id }})"
                                        title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                @endif
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
        <h5>No Pending Funding Requests</h5>
        <p class="text-muted">All funding requests have been processed.</p>
    </div>
@endif