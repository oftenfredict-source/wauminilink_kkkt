<div class="table-responsive">
    <table class="table table-hover align-middle mb-0" id="dataTable{{ str_replace(' ', '', $category) }}" width="100%"
        cellspacing="0">
        <thead class="bg-light text-muted small">
            <tr>
                <th class="d-none d-md-table-cell">Budget Name</th>
                <th class="d-table-cell d-md-none">Budget</th>
                <th>Type</th>
                <th class="d-none d-lg-table-cell">Fiscal Year</th>
                <th>Total Budget</th>
                <th class="d-none d-xl-table-cell text-end">Spent</th>
                <th class="d-none d-xl-table-cell text-end">Remaining</th>
                <th style="width: 150px;">Utilization</th>
                <th>Status</th>
                <th class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($budgets as $budget)
                <tr>
                    <td>
                        <div class="fw-bold">{{ $budget->budget_name }}</div>
                        <div class="d-md-none">
                            <small class="text-muted d-block">
                                <i class="fas fa-calendar me-1"></i>FY: {{ $budget->fiscal_year }}
                            </small>
                            <small class="text-muted d-block">
                                <i class="fas fa-money-bill-wave me-1"></i>Spent: TZS
                                {{ number_format($budget->spent_amount, 0) }}
                            </small>
                            <small class="text-muted d-block">
                                <i class="fas fa-piggy-bank me-1"></i>Remaining: TZS
                                {{ number_format($budget->remaining_amount, 0) }}
                            </small>
                        </div>
                    </td>
                    <td>
                        <span class="badge 
                                        @if($budget->budget_type == 'injili' || $budget->budget_type == 'operational') bg-primary 
                                        @elseif($budget->budget_type == 'umoja' || $budget->budget_type == 'program') bg-success 
                                        @elseif($budget->budget_type == 'majengo' || $budget->budget_type == 'capital') bg-warning 
                                        @elseif($budget->budget_type == 'other' || $budget->budget_type == 'special' || $budget->budget_type == 'zinginezo') bg-info 
                                        @else bg-secondary @endif">
                            {{ $budget->budget_type == 'umoja' ? 'Umoja' : ucfirst($budget->budget_type) }}
                        </span>
                    </td>
                    <td class="d-none d-lg-table-cell">{{ $budget->fiscal_year }}</td>
                    <td class="text-end fw-bold">
                        TZS {{ number_format($budget->total_budget, 0) }}
                    </td>
                    <td class="d-none d-xl-table-cell text-end">
                        <div>TZS {{ number_format($budget->spent_amount, 0) }}</div>
                        @if($budget->pending_spent_amount > 0)
                            <small class="text-muted" title="Approved/Pending Expenses">
                                +{{ number_format($budget->pending_spent_amount, 0) }} committed
                            </small>
                        @endif
                    </td>
                    <td
                        class="d-none d-xl-table-cell text-end font-monospace {{ $budget->remaining_amount < 0 ? 'text-danger' : '' }}">
                        TZS {{ number_format($budget->remaining_amount - $budget->pending_spent_amount, 0) }}
                        @if($budget->pending_spent_amount > 0)
                            <div class="small text-muted" title="Real remaining after commitments">
                                ({{ number_format($budget->remaining_amount, 0) }} cash)
                            </div>
                        @endif
                    </td>
                    <td>
                        <div class="progress" style="height: 12px; border-radius: 10px;">
                            <div class="progress-bar {{ $budget->is_over_budget ? 'bg-danger' : ($budget->is_near_limit ? 'bg-warning' : 'bg-success') }}"
                                role="progressbar" style="width: {{ min(100, $budget->utilization_percentage) }}%"
                                aria-valuenow="{{ $budget->utilization_percentage }}" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                        <small class="text-muted mt-1 d-block"
                            style="font-size: 0.7rem;">{{ $budget->utilization_percentage }}% used</small>
                    </td>
                    <td>
                        @if($budget->status == 'active')
                            <span class="badge rounded-pill bg-success-soft text-success px-3">Active</span>
                        @elseif($budget->status == 'completed')
                            <span class="badge rounded-pill bg-primary-soft text-primary px-3">Completed</span>
                        @else
                            <span class="badge rounded-pill bg-secondary-soft text-secondary px-3">Inactive</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-primary" onclick="viewBudget(this)"
                                data-id="{{ $budget->id }}" data-name="{{ $budget->budget_name }}"
                                data-type="{{ ucfirst($budget->budget_type) }}" data-fy="{{ $budget->fiscal_year }}"
                                data-total="{{ number_format($budget->total_budget, 2) }}"
                                data-spent="{{ number_format($budget->spent_amount, 2) }}"
                                data-remaining="{{ number_format($budget->remaining_amount, 2) }}"
                                data-utilization="{{ $budget->utilization_percentage }}"
                                data-status="{{ ucfirst($budget->status) }}"
                                data-start="{{ $budget->start_date->format('Y-m-d') }}"
                                data-end="{{ $budget->end_date->format('Y-m-d') }}"
                                data-description="{{ $budget->description ?? '-' }}" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button type="button" class="btn btn-outline-warning" onclick="editBudget(this)"
                                data-id="{{ $budget->id }}" data-name="{{ $budget->budget_name }}"
                                data-type="{{ $budget->budget_type }}" data-fy="{{ $budget->fiscal_year }}"
                                data-total="{{ $budget->total_budget }}"
                                data-start="{{ $budget->start_date->format('Y-m-d') }}"
                                data-end="{{ $budget->end_date->format('Y-m-d') }}" data-status="{{ $budget->status }}"
                                data-description="{{ $budget->description ?? '' }}" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form class="d-inline" onsubmit="return confirmDeleteBudget(this, {{ $budget->id }})"
                                method="POST" action="{{ route('finance.budgets.destroy', $budget) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center py-5">
                        <div class="text-muted">
                            <i class="fas fa-folder-open fa-3x mb-3 d-block opacity-25"></i>
                            No budgets found in this category
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<style>
    .bg-success-soft {
        background-color: rgba(25, 135, 84, 0.1);
    }

    .bg-primary-soft {
        background-color: rgba(13, 110, 253, 0.1);
    }

    .bg-secondary-soft {
        background-color: rgba(108, 117, 125, 0.1);
    }
</style>