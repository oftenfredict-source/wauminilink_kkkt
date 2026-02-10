@extends('layouts.index')

@section('content')
    <style>
        .item-row {
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .item-row:last-child {
            border-bottom: none;
        }

        .progress {
            height: 20px;
        }

        @media (max-width: 768px) {
            .btn-group .btn span {
                display: none;
            }
        }
    </style>

    <div class="container-fluid px-4">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-bottom p-3 d-flex align-items-center justify-content-between">
                <h5 class="mb-0"><i class="fas fa-handshake me-2 text-primary"></i>Ahadi kwa Bwana (In-Kind Pledges)</h5>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addAhadiModal">
                    <i class="fas fa-plus me-1"></i> Record New Ahadi
                </button>
            </div>
        </div>

        <!-- Filters -->
        <form method="GET" action="{{ route('finance.ahadi-pledges.index') }}" class="card mb-4 border-0 shadow-sm">
            <div class="card-body p-3">
                <div class="row g-2">
                    <div class="col-md-3">
                        <label class="small text-muted mb-1">Member / Envelope</label>
                        <select class="form-select form-select-sm select2-member" name="member_id">
                            <option value="">All Members</option>
                            @foreach($members as $member)
                                <option value="{{ $member->id }}" {{ request('member_id') == $member->id ? 'selected' : '' }}>
                                    {{ $member->full_name }} @if($member->envelope_number) [Env: {{ $member->envelope_number }}]
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="small text-muted mb-1">Item Type</label>
                        <select class="form-select form-select-sm" name="item_type">
                            <option value="">All Items</option>
                            @foreach($itemTypes as $type)
                                <option value="{{ $type }}" {{ request('item_type') == $type ? 'selected' : '' }}>{{ $type }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="small text-muted mb-1">Jumuiya</label>
                        <select class="form-select form-select-sm" name="community_id">
                            <option value="">All Jumuiya</option>
                            @foreach($communities as $community)
                                <option value="{{ $community->id }}" {{ request('community_id') == $community->id ? 'selected' : '' }}>{{ $community->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="small text-muted mb-1">Status</label>
                        <select class="form-select form-select-sm" name="status">
                            <option value="">All Statuses</option>
                            <option value="promised" {{ request('status') == 'promised' ? 'selected' : '' }}>Promised</option>
                            <option value="partially_fulfilled" {{ request('status') == 'partially_fulfilled' ? 'selected' : '' }}>Partially Fulfilled</option>
                            <option value="fully_fulfilled" {{ request('status') == 'fully_fulfilled' ? 'selected' : '' }}>
                                Fully Fulfilled</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary btn-sm flex-fill">Filter</button>
                        <a href="{{ route('finance.ahadi-pledges.index') }}"
                            class="btn btn-outline-secondary btn-sm">Clear</a>
                    </div>
                </div>
            </div>
        </form>

        <!-- Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-3" style="width: 40px;"></th>
                                <th>Member / Envelope</th>
                                <th>Jumuiya</th>
                                <th class="text-center">Items</th>
                                <th class="text-center">Status</th>
                                <th style="min-width: 180px;">Overall Progress</th>
                                <th class="text-end pe-3">Est. Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($paginator as $group)
                                <!-- Member Summary Row (Clickable) -->
                                <tr class="member-summary-row" style="cursor: pointer; background-color: #f8f9fa;"
                                    data-bs-toggle="collapse" data-bs-target="#member-{{ $group['member']->id }}"
                                    aria-expanded="false">
                                    <td class="ps-3 text-center">
                                        <i class="fas fa-chevron-right toggle-icon" id="icon-{{ $group['member']->id }}"></i>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-primary">{{ $group['member']->full_name }}</div>
                                        @if($group['member']->envelope_number)
                                            <small class="text-muted">Envelope: <span
                                                    class="badge bg-secondary">{{ $group['member']->envelope_number }}</span></small>
                                        @endif
                                    </td>
                                    <td><small class="text-muted">{{ $group['community']->name ?? 'N/A' }}</small></td>
                                    <td class="text-center">
                                        <span class="badge bg-primary">{{ $group['total_items'] }}
                                            {{ Str::plural('item', $group['total_items']) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-1 justify-content-center">
                                            @if($group['fully_fulfilled_count'] > 0)
                                                <span class="badge bg-success"
                                                    title="Fully Fulfilled">{{ $group['fully_fulfilled_count'] }}</span>
                                            @endif
                                            @if($group['partially_fulfilled_count'] > 0)
                                                <span class="badge bg-warning text-dark"
                                                    title="Partially Fulfilled">{{ $group['partially_fulfilled_count'] }}</span>
                                            @endif
                                            @if($group['promised_count'] > 0)
                                                <span class="badge bg-secondary"
                                                    title="Promised">{{ $group['promised_count'] }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            @php $overallPct = round($group['overall_progress']); @endphp
                                            <div class="progress-bar {{ $overallPct >= 100 ? 'bg-success' : ($overallPct > 0 ? 'bg-warning' : 'bg-light text-dark') }}"
                                                role="progressbar" style="width: {{ $overallPct }}%">
                                                {{ $overallPct }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end pe-3">
                                        <strong>TZS {{ number_format($group['total_value'], 0) }}</strong>
                                    </td>
                                </tr>

                                <!-- Expandable Detail Rows -->
                                <tr class="collapse" id="member-{{ $group['member']->id }}">
                                    <td colspan="7" class="p-0">
                                        <div class="bg-light p-3">
                                            <table class="table table-sm table-bordered mb-0 bg-white">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Year</th>
                                                        <th>Item Type</th>
                                                        <th>Promised</th>
                                                        <th>Fulfilled</th>
                                                        <th>Progress</th>
                                                        <th>Status</th>
                                                        <th class="text-end">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($group['pledges'] as $pledge)
                                                        <tr>
                                                            <td>{{ $pledge->year }}</td>
                                                            <td><span
                                                                    class="badge bg-info text-dark">{{ $pledge->item_type }}</span>
                                                            </td>
                                                            <td>{{ $pledge->quantity_promised }} {{ $pledge->unit }}</td>
                                                            <td>{{ $pledge->quantity_fulfilled }} {{ $pledge->unit }}</td>
                                                            <td style="min-width: 120px;">
                                                                <div class="progress" style="height: 15px;">
                                                                    @php $pct = $pledge->progress_percentage; @endphp
                                                                    <div class="progress-bar {{ $pct >= 100 ? 'bg-success' : ($pct > 0 ? 'bg-warning' : 'bg-light text-dark') }}"
                                                                        role="progressbar" style="width: {{ $pct }}%">
                                                                        {{ $pct }}%
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                @if($pledge->status == 'fully_fulfilled')
                                                                    <span class="badge bg-success">Fully Fulfilled</span>
                                                                @elseif($pledge->status == 'partially_fulfilled')
                                                                    <span class="badge bg-warning text-dark">Partially</span>
                                                                @else
                                                                    <span class="badge bg-secondary">Promised</span>
                                                                @endif
                                                            </td>
                                                            <td class="text-end">
                                                                <div class="btn-group btn-group-sm">
                                                                    <button class="btn btn-outline-primary btn-sm"
                                                                        onclick="openEditModal({{ json_encode($pledge) }})"
                                                                        title="Edit Pledge">
                                                                        <i class="fas fa-edit"></i>
                                                                    </button>
                                                                    <button class="btn btn-outline-success btn-sm"
                                                                        onclick="openFulfillmentModal({{ json_encode($pledge) }})"
                                                                        title="Update Fulfillment">
                                                                        <i class="fas fa-check-circle"></i>
                                                                    </button>
                                                                    <button class="btn btn-outline-danger btn-sm"
                                                                        onclick="confirmDeleteAhadi({{ $pledge->id }})"
                                                                        title="Delete">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            <!-- Add More Items Button -->
                                            <div class="text-end mt-2">
                                                <button type="button" class="btn btn-sm btn-outline-primary"
                                                    onclick="openAddItemsModal({{ json_encode($group['member']) }}, {{ $group['pledges']->first()->year ?? date('Y') }})">
                                                    <i class="fas fa-plus me-1"></i> Add More Items
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">No in-kind pledges found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-3">
                    {{ $paginator->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Add Ahadi Modal -->
    <div class="modal fade" id="addAhadiModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Record Ahadi kwa Bwana</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('finance.ahadi-pledges.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Select Member</label>
                                <select class="form-select select2-member-modal" name="member_id" required>
                                    <option value="">Search for member...</option>
                                    @foreach($members as $member)
                                        <option value="{{ $member->id }}" data-envelope="{{ $member->envelope_number }}">
                                            {{ $member->full_name }} @if($member->envelope_number) [Env:
                                            {{ $member->envelope_number }}] @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Year</label>
                                <input type="number" class="form-control" name="year" value="{{ date('Y') }}" required>
                            </div>
                        </div>

                        <div id="items-container">
                            <h6 class="border-bottom pb-2 mb-3 text-primary">Pledged Items</h6>
                            <div class="item-row">
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <label class="small">Item Type</label>
                                        <select class="form-select form-select-sm item-type-select"
                                            name="items[0][item_type]" onchange="handleItemTypeChange(this, 0)" required>
                                            @foreach($itemTypes as $type)
                                                <option value="{{ $type }}">{{ $type }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="small">Qty</label>
                                        <input type="number" step="0.01" class="form-control form-control-sm qty-input"
                                            name="items[0][quantity_promised]" data-index="0" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="small">Unit</label>
                                        <input type="text" class="form-control form-control-sm unit-input"
                                            name="items[0][unit]" placeholder="Bags, Head, etc">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="small">Est. Value (TZS)</label>
                                        <input type="number" class="form-control form-control-sm value-input"
                                            name="items[0][estimated_value]" data-index="0">
                                    </div>
                                    <div class="col-md-1 d-flex align-items-end">
                                        <button type="button" class="btn btn-outline-danger btn-sm w-100"
                                            onclick="removeItem(this)" disabled>
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addItem()">
                            <i class="fas fa-plus me-1"></i> Add Another Item
                        </button>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Ahadi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Pledge Modal -->
    <div class="modal fade" id="editPledgeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">Edit Pledge</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editPledgeForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Item Type</label>
                            <select class="form-select" name="item_type" id="edit_item_type"
                                onchange="handleEditItemTypeChange()" required>
                                @foreach($itemTypes as $type)
                                    <option value="{{ $type }}">{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Quantity Promised</label>
                                <input type="number" step="0.01" class="form-control" name="quantity_promised"
                                    id="edit_quantity" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Unit</label>
                                <input type="text" class="form-control" name="unit" id="edit_unit"
                                    placeholder="Bags, Head, TZS">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Estimated Value (TZS)</label>
                            <input type="number" class="form-control" name="estimated_value" id="edit_value">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Year</label>
                            <input type="number" class="form-control" name="year" id="edit_year" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" name="notes" id="edit_notes" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">Update Pledge</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Fulfillment Modal -->
    <div class="modal fade" id="fulfillmentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Update Fulfillment</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="fulfillmentForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="fw-bold d-block mb-1">Item:</label>
                            <span id="ful_item_name" class="badge bg-secondary"></span>
                            <div class="mt-2 small text-muted">
                                Promised: <span id="ful_promised"></span> | Currently fulfilled: <span
                                    id="ful_current_display"></span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-success">Amount to Add</label>
                            <div class="input-group">
                                <input type="number" step="0.01" class="form-control border-success" id="ful_add_amount"
                                    placeholder="e.g. 50000">
                                <span class="input-group-text bg-success text-white" id="ful_add_unit"></span>
                            </div>
                            <small class="text-muted">Enter the new payment amount here.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">New Total Fulfilled</label>
                            <div class="input-group">
                                <input type="number" step="0.01" class="form-control bg-light" name="quantity_fulfilled"
                                    id="ful_input" readonly required>
                                <span class="input-group-text" id="ful_unit"></span>
                            </div>
                            <small class="text-muted">This is the total cumulative amount (Previous + New).</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Fulfillment Date</label>
                            <input type="date" class="form-control" name="fulfillment_date" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" name="notes" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Update Progress</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        let itemCount = 1;
        const itemTypes = @json($itemTypes);

        function addItem() {
            const container = document.getElementById('items-container');
            const row = document.createElement('div');
            row.className = 'item-row';
            row.innerHTML = `
                <div class="row g-2">
                    <div class="col-md-4">
                        <select class="form-select form-select-sm item-type-select" name="items[${itemCount}][item_type]" onchange="handleItemTypeChange(this, ${itemCount})" required>
                            ${itemTypes.map(t => `<option value="${t}">${t}</option>`).join('')}
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" step="0.01" class="form-control form-control-sm qty-input" name="items[${itemCount}][quantity_promised]" data-index="${itemCount}" required>
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control form-control-sm unit-input" name="items[${itemCount}][unit]" placeholder="Bags, Head, etc">
                    </div>
                    <div class="col-md-3">
                        <input type="number" class="form-control form-control-sm value-input" name="items[${itemCount}][estimated_value]" data-index="${itemCount}">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="removeItem(this)">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            `;
            container.appendChild(row);
            itemCount++;
        }

        function removeItem(btn) {
            btn.closest('.item-row').remove();
        }

        // Handle item type change for cash pledges (Add modal)
        function handleItemTypeChange(selectElement, index) {
            const itemType = selectElement.value;
            const row = selectElement.closest('.row');
            const qtyInput = row.querySelector(`[data-index="${index}"].qty-input`);
            const unitInput = row.querySelector('.unit-input');
            const valueInput = row.querySelector(`[data-index="${index}"].value-input`);

            if (itemType === 'Fedha (Cash)') {
                // For cash, unit is TZS and value equals quantity
                unitInput.value = 'TZS';
                unitInput.readOnly = true;

                // Auto-sync quantity with estimated value
                qtyInput.addEventListener('input', function () {
                    valueInput.value = this.value;
                });

                if (qtyInput.value) {
                    valueInput.value = qtyInput.value;
                }
            } else {
                unitInput.readOnly = false;
                if (unitInput.value === 'TZS') {
                    unitInput.value = '';
                }
            }
        }

        // Handle item type change for cash pledges (Edit modal)
        function handleEditItemTypeChange() {
            const itemType = document.getElementById('edit_item_type').value;
            const qtyInput = document.getElementById('edit_quantity');
            const unitInput = document.getElementById('edit_unit');
            const valueInput = document.getElementById('edit_value');

            if (itemType === 'Fedha (Cash)') {
                unitInput.value = 'TZS';
                unitInput.readOnly = true;

                // Auto-sync quantity with estimated value
                qtyInput.addEventListener('input', function () {
                    valueInput.value = this.value;
                });

                if (qtyInput.value) {
                    valueInput.value = qtyInput.value;
                }
            } else {
                unitInput.readOnly = false;
                if (unitInput.value === 'TZS') {
                    unitInput.value = '';
                }
            }
        }

        // Open edit modal with pledge data
        function openEditModal(pledge) {
            const form = document.getElementById('editPledgeForm');
            form.action = `/finance/ahadi-pledges/${pledge.id}`;

            document.getElementById('edit_item_type').value = pledge.item_type;
            document.getElementById('edit_quantity').value = pledge.quantity_promised;
            document.getElementById('edit_unit').value = pledge.unit || '';
            document.getElementById('edit_value').value = pledge.estimated_value || '';
            document.getElementById('edit_year').value = pledge.year;
            document.getElementById('edit_notes').value = pledge.notes || '';

            // Trigger item type change to handle cash pledges
            handleEditItemTypeChange();

            const modal = new bootstrap.Modal(document.getElementById('editPledgeModal'));
            modal.show();
        }

        // Open Add modal with member pre-selected (for adding more items to existing pledges)
        function openAddItemsModal(member, year) {
            // Pre-select the member in the dropdown
            $('.select2-member-modal').val(member.id).trigger('change');

            // Set the year
            $('input[name="year"]').val(year);

            // Open the modal
            const modal = new bootstrap.Modal(document.getElementById('addAhadiModal'));
            modal.show();
        }

        function openFulfillmentModal(pledge) {
            const form = document.getElementById('fulfillmentForm');
            form.action = `/finance/ahadi-pledges/${pledge.id}`;

            document.getElementById('ful_item_name').textContent = pledge.item_type;
            const unit = pledge.unit || '';
            document.getElementById('ful_promised').textContent = parseFloat(pledge.quantity_promised).toLocaleString() + ' ' + unit;
            
            const currentFulfilled = parseFloat(pledge.quantity_fulfilled) || 0;
            document.getElementById('ful_current_display').textContent = currentFulfilled.toLocaleString() + ' ' + unit;
            
            // Setup inputs
            const addInput = document.getElementById('ful_add_amount');
            const totalInput = document.getElementById('ful_input');
            
            // Reset inputs
            addInput.value = '';
            totalInput.value = currentFulfilled;
            
            document.getElementById('ful_unit').textContent = unit;
            document.getElementById('ful_add_unit').textContent = unit;
            
            // Add listener for calculation
            addInput.oninput = function() {
                const added = parseFloat(this.value) || 0;
                totalInput.value = (currentFulfilled + added).toFixed(2);
            };

            const modal = new bootstrap.Modal(document.getElementById('fulfillmentModal'));
            modal.show();
            
            // Focus on add input
            setTimeout(() => addInput.focus(), 500);
        }

        function confirmDeleteAhadi(id) {
            Swal.fire({
                title: 'Delete Pledge?',
                text: "This will permanently remove this in-kind pledge record.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Yes, delete it'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/finance/ahadi-pledges/${id}`;
                    form.innerHTML = `@csrf @method('DELETE')`;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        $(document).ready(function () {
            $('.select2-member').select2({ width: '100%' });
            $('.select2-member-modal').select2({
                width: '100%',
                dropdownParent: $('#addAhadiModal')
            });

            // Toggle chevron icon on expand/collapse
            $('[data-bs-toggle="collapse"]').on('click', function () {
                const targetId = $(this).data('bs-target');
                const memberId = targetId.replace('#member-', '');
                const icon = $('#icon-' + memberId);

                // Toggle icon rotation
                if (icon.hasClass('fa-chevron-right')) {
                    icon.removeClass('fa-chevron-right').addClass('fa-chevron-down');
                } else {
                    icon.removeClass('fa-chevron-down').addClass('fa-chevron-right');
                }
            });
        });
    </script>
@endsection