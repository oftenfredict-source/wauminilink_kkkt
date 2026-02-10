@extends('layouts.index')

@section('content')
    <div class="container-fluid px-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h1 class="h3 mb-0"><i class="fas fa-money-bill-wave me-2 text-success"></i>Record Community
                                    Offering</h1>
                                <p class="text-muted mb-0">Create a new community offering</p>
                            </div>
                            @if(isset($community))
                                <a href="{{ route('church-elder.community-offerings.index', $community->id) }}"
                                    class="btn btn-outline-primary">
                            @elseif($communities->isNotEmpty())
                                    <a href="{{ route('church-elder.community-offerings.index', $communities->first()->id) }}"
                                        class="btn btn-outline-primary">
                                @else
                                        <a href="{{ route('church-elder.dashboard') }}" class="btn btn-outline-primary">
                                    @endif
                                        <i class="fas fa-arrow-left me-1"></i> Back
                                    </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 mx-auto">
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Offering Details</h5>
                    </div>
                    <div class="card-body">
                        <form id="communityOfferingForm" action="{{ route('church-elder.community-offerings.store') }}"
                            method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="community_id" class="form-label">Community <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('community_id') is-invalid @enderror" id="community_id"
                                    name="community_id" required>
                                    <option value="">Select community...</option>
                                    @foreach($communities as $community)
                                        <option value="{{ $community->id }}" {{ old('community_id') == $community->id ? 'selected' : '' }}>
                                            {{ $community->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('community_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="service_id" class="form-label">Service (Optional - Sunday or Mid-week)</label>
                                <select class="form-select @error('service_id') is-invalid @enderror" id="service_id"
                                    name="service_id">
                                    <option value="">Select service (optional)...</option>
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}" data-type="{{ $service->service_type }}"
                                            data-date="{{ $service->service_date->format('Y-m-d') }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $service->service_type)) }} -
                                            {{ $service->service_date->format('M d, Y') }}
                                        </option>
                                    @endforeach
                                    <optgroup label="Mid-week Fellowships (Community)">
                                        <option value="" data-type="prayer_meeting">Prayer Meeting</option>
                                        <option value="" data-type="bible_study">Bible Study</option>
                                        <option value="" data-type="youth_service">Youth Service</option>
                                        <option value="" data-type="women_fellowship">Women Fellowship</option>
                                        <option value="" data-type="men_fellowship">Men Fellowship</option>
                                        <option value="" data-type="evangelism">Evangelism</option>
                                    </optgroup>
                                </select>
                                @error('service_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <input type="hidden" id="service_type" name="service_type"
                                    value="{{ old('service_type') }}">
                            </div>

                            <div class="mb-3" id="offering_type_container">
                                <label for="offering_type" class="form-label">Offering Type <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('offering_type') is-invalid @enderror" id="offering_type"
                                    name="offering_type" required>
                                    <option value="">Select offering type...</option>
                                    <option value="general" {{ old('offering_type') == 'general' ? 'selected' : '' }}>General
                                        Offering</option>

                                    <option value="sunday_offering" {{ old('offering_type') == 'sunday_offering' ? 'selected' : '' }}>Sunday Offering (Umoja, Jengo, Ahadi)</option>
                                    <option value="tithe" {{ old('offering_type') == 'tithe' ? 'selected' : '' }}>Tithes (Zaka)</option>
                                    <option value="other" {{ old('offering_type') == 'other' ? 'selected' : '' }}>Other (Mengineyo)</option>
                                </select>
                                @error('offering_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3" id="other_description_container" style="display: none;">
                                <div class="row g-2">
                                    <div class="col-md-6 mb-2">
                                        <label for="other_category" class="form-label">Category <span class="text-danger">*</span></label>
                                        <select class="form-select" id="other_category" name="other_category">
                                            <option value="">Select category...</option>
                                            @foreach($offeringCategories as $category => $subtypes)
                                                <option value="{{ $category }}">{{ $category }}</option>
                                            @endforeach
                                            <option value="Special">Special / Other</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label for="other_subtype" class="form-label">Offering Analyzed <span class="text-danger">*</span></label>
                                        <select class="form-select" id="other_subtype" name="other_subtype">
                                            <option value="">First select category...</option>
                                        </select>
                                    </div>
                                    <div class="col-12" id="manual_description_group" style="display: none;">
                                        <label for="other_description" class="form-label">Specify Offering Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="other_description" name="notes" placeholder="Enter offering name (e.g. Shukrani Maalumu)" value="{{ old('notes') }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Envelope Breakdown Section -->
                            <div id="envelope_breakdown_section" class="mb-4 p-3 bg-light border rounded"
                                style="display: none;">
                                <div class="alert alert-warning border-warning mb-3">
                                    <h6 class="alert-heading mb-2"><i
                                            class="fas fa-exclamation-triangle me-2"></i>IMPORTANT: Envelope Breakdown
                                        Required!</h6>
                                    <p class="mb-0"><strong>You MUST fill in the envelope breakdown below</strong> to track
                                        individual member contributions. Without this, members will NOT see their
                                        contributions in their individual reports.</p>
                                </div>
                                <h6 class="mb-3 border-bottom pb-2">Envelope Breakdown</h6>
                                <div class="alert alert-info py-2 mb-3">
                                    <small><i class="fas fa-info-circle me-1"></i> Enter envelope numbers to track
                                        individual contributions. Total amount will be calculated automatically.</small>
                                </div>

                                <div class="table-responsive mb-3">
                                    <table class="table table-sm table-bordered" id="envelope_items_table">
                                        <thead id="envelope_table_head">
                                            <tr>
                                                <th style="width: 150px;">Envelope Number</th>
                                                <th>Member Name</th>
                                                <th id="amount_header">Amount</th>
                                                <th style="width: 50px;"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="envelope_items_body">
                                            <!-- Rows will be added here via JS -->
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="4">
                                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                                        id="add_envelope_row_btn">
                                                        <i class="fas fa-plus me-1"></i> Add Envelope
                                                    </button>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <input type="hidden" name="items_json" id="items_json">
                            </div>

                            <div class="mb-3">
                                <label for="amount" class="form-label">Offering Amount (TZS) <span
                                        class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0"
                                    class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount"
                                    value="{{ old('amount') }}" required>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="offering_date" class="form-label">Offering Date <span
                                        class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('offering_date') is-invalid @enderror"
                                    id="offering_date" name="offering_date"
                                    value="{{ old('offering_date', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required>
                                @error('offering_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="collection_method" class="form-label">Collection Method <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('collection_method') is-invalid @enderror"
                                    id="collection_method" name="collection_method" required>
                                    <option value="">Select method...</option>
                                    <option value="cash" {{ old('collection_method') == 'cash' ? 'selected' : '' }}>Cash
                                    </option>
                                    <option value="mobile_money" {{ old('collection_method') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                                    <option value="bank_transfer" {{ old('collection_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                </select>
                                @error('collection_method')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3" id="reference_number_group" style="display: none;">
                                <label for="reference_number" class="form-label">Reference Number</label>
                                <input type="text" class="form-control @error('reference_number') is-invalid @enderror"
                                    id="reference_number" name="reference_number" value="{{ old('reference_number') }}"
                                    placeholder="Enter transaction reference">
                                @error('reference_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="elder_notes" class="form-label">Notes (Optional)</label>
                                <textarea class="form-control @error('elder_notes') is-invalid @enderror" id="elder_notes"
                                    name="elder_notes" rows="3"
                                    placeholder="Any additional notes about this offering...">{{ old('elder_notes') }}</textarea>
                                @error('elder_notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                @if(isset($community))
                                    <a href="{{ route('church-elder.community-offerings.index', $community->id) }}"
                                        class="btn btn-outline-secondary">
                                @elseif($communities->isNotEmpty())
                                        <a href="{{ route('church-elder.community-offerings.index', $communities->first()->id) }}"
                                            class="btn btn-outline-secondary">
                                    @else
                                            <a href="{{ route('church-elder.dashboard') }}" class="btn btn-outline-secondary">
                                        @endif
                                            <i class="fas fa-times me-1"></i> Cancel
                                        </a>
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-paper-plane me-1"></i> Submit to Secretary
                                        </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const offeringCategories = @json($offeringCategories);
            const offeringTypeSelect = document.getElementById('offering_type');
            const otherCategorySelect = document.getElementById('other_category');
            const otherSubtypeSelect = document.getElementById('other_subtype');
            const manualDescriptionGroup = document.getElementById('manual_description_group');
            const otherDescriptionInput = document.getElementById('other_description');
            
            const envelopeSection = document.getElementById('envelope_breakdown_section');
            const itemsTableBody = document.getElementById('envelope_items_body');
            const addRowBtn = document.getElementById('add_envelope_row_btn');
            const amountInput = document.getElementById('amount');
            const itemsJsonInput = document.getElementById('items_json');
            const form = document.getElementById('communityOfferingForm');

            // Handle Category Change
            otherCategorySelect.addEventListener('change', function() {
                const category = this.value;
                otherSubtypeSelect.innerHTML = '<option value="">Select subtype...</option>';
                
                if (category && offeringCategories[category]) {
                    manualDescriptionGroup.style.display = 'none';
                    otherDescriptionInput.removeAttribute('required');
                    
                    Object.entries(offeringCategories[category]).forEach(([key, value]) => {
                        // Skip 10.01 (Ahadi) for Mapato ya Injili as requested
                        if (category === 'Mapato ya Injili' && key === '10.01') return;
                        
                        const option = document.createElement('option');
                        option.value = key;
                        option.textContent = value;
                        otherSubtypeSelect.appendChild(option);
                    });
                    
                    const otherOption = document.createElement('option');
                    otherOption.value = 'other';
                    otherOption.textContent = 'Other (Manual Entry)';
                    otherSubtypeSelect.appendChild(otherOption);
                    
                    otherSubtypeSelect.setAttribute('required', 'required');
                } else if (category === 'Special') {
                    manualDescriptionGroup.style.display = 'block';
                    otherDescriptionInput.setAttribute('required', 'required');
                    otherSubtypeSelect.removeAttribute('required');
                } else {
                    otherSubtypeSelect.removeAttribute('required');
                }
            });

            // Handle Subtype Change
            otherSubtypeSelect.addEventListener('change', function() {
                if (this.value === 'other') {
                    manualDescriptionGroup.style.display = 'block';
                    otherDescriptionInput.setAttribute('required', 'required');
                } else {
                    manualDescriptionGroup.style.display = 'none';
                    otherDescriptionInput.removeAttribute('required');
                }
            });

            // Toggle breakdown section visibility
            function toggleBreakdownSection() {
                const type = offeringTypeSelect.value;
                const thead = document.getElementById('envelope_table_head');
                const otherDescContainer = document.getElementById('other_description_container');

                // Toggle Other Description
                if (type === 'other') {
                    otherDescContainer.style.display = 'block';
                    otherCategorySelect.setAttribute('required', 'required');
                } else {
                    otherDescContainer.style.display = 'none';
                    otherCategorySelect.removeAttribute('required');
                    otherSubtypeSelect.removeAttribute('required');
                    otherDescriptionInput.removeAttribute('required');
                }

                if (type === 'sunday_offering') {
                    envelopeSection.style.display = 'block';
                    amountInput.setAttribute('readonly', 'readonly');

                    // Update table headers
                    if (type === 'sunday_offering') {
                        thead.innerHTML = `
                        <tr>
                            <th style="width: 120px;">Env #</th>
                            <th>Member</th>
                            <th style="width: 100px;">Umoja</th>
                            <th style="width: 100px;">Jengo</th>
                            <th style="width: 100px;">Ahadi</th>
                            <th style="width: 100px;">Other</th>
                            <th style="width: 100px;">Total</th>
                            <th style="width: 40px;"></th>
                        </tr>
                    `;
                    } else {
                        let amountLabel = 'Amount';
                        if (type === 'sadaka_umoja') amountLabel = 'Umoja Amount';
                        if (type === 'sadaka_jengo') amountLabel = 'Jengo Amount';
                        if (type === 'sadaka_ahadi') amountLabel = 'Ahadi ya Bwana';

                        thead.innerHTML = `
                        <tr>
                            <th style="width: 150px;">Envelope Number</th>
                            <th>Member Name</th>
                            <th>${amountLabel}</th>
                            <th style="width: 50px;"></th>
                        </tr>
                    `;
                    }

                    // If table is empty, add first row
                    if (itemsTableBody.children.length === 0) {
                        addEnvelopeRow();
                    } else {
                        // If type changed, we might need to re-render rows to match columns
                        // For simplicity, let's clear and re-add if it was different
                        const isCombo = itemsTableBody.querySelector('.umoja-input') !== null;
                        if ((type === 'sunday_offering' && !isCombo) || (type !== 'sunday_offering' && isCombo)) {
                            itemsTableBody.innerHTML = '';
                            addEnvelopeRow();
                        }
                    }
                } else {
                    envelopeSection.style.display = 'none';
                    amountInput.removeAttribute('readonly');
                }
            }

            offeringTypeSelect.addEventListener('change', toggleBreakdownSection);

            // Initial check
            if (offeringTypeSelect.value) {
                toggleBreakdownSection();
            }

            // Add new row function
            function addEnvelopeRow() {
                const type = offeringTypeSelect.value;
                const rowId = 'row_' + Date.now() + '_' + Math.floor(Math.random() * 1000);
                const tr = document.createElement('tr');
                tr.id = rowId;

                if (type === 'sunday_offering') {
                    tr.innerHTML = `
                    <td>
                        <input type="text" class="form-control envelope-input px-2" placeholder="Env #" required>
                    </td>
                    <td>
                        <input type="text" class="form-control member-name-input px-2" placeholder="Member" readonly>
                        <div class="text-danger small member-error"></div>
                    </td>
                    <td><input type="number" step="0.01" min="0" class="form-control umoja-input px-1" placeholder="0"></td>
                    <td><input type="number" step="0.01" min="0" class="form-control jengo-input px-1" placeholder="0"></td>
                    <td><input type="number" step="0.01" min="0" class="form-control ahadi-input px-1" placeholder="0"></td>
                    <td><input type="number" step="0.01" min="0" class="form-control other-input px-1" placeholder="0"></td>
                    <td><input type="number" step="0.01" class="form-control row-total-input px-1" value="0.00" readonly></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-row-btn">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;
                } else {
                    tr.innerHTML = `
                    <td>
                        <input type="text" class="form-control envelope-input" placeholder="Env #" required>
                    </td>
                    <td>
                        <input type="text" class="form-control member-name-input" placeholder="Member Name" readonly>
                        <div class="text-danger small member-error"></div>
                    </td>
                    <td>
                        <input type="number" step="0.01" min="0" class="form-control amount-input" placeholder="0.00" required>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-row-btn">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;
                }
                itemsTableBody.appendChild(tr);

                // Add event listeners for the new row
                const envelopeInput = tr.querySelector('.envelope-input');
                const removeBtn = tr.querySelector('.remove-row-btn');

                envelopeInput.addEventListener('blur', function () {
                    lookupMember(this);
                });

                if (type === 'sunday_offering') {
                    const inputs = tr.querySelectorAll('.umoja-input, .jengo-input, .ahadi-input, .other-input');
                    inputs.forEach(input => {
                        input.addEventListener('input', function () {
                            calculateRowTotal(tr);
                            calculateTotal();
                        });
                    });
                } else {
                    const amountInputRow = tr.querySelector('.amount-input');
                    amountInputRow.addEventListener('input', calculateTotal);
                }

                removeBtn.addEventListener('click', function () {
                    tr.remove();
                    calculateTotal();
                });
            }

            function calculateRowTotal(tr) {
                let total = 0;
                const inputs = tr.querySelectorAll('.umoja-input, .jengo-input, .ahadi-input, .other-input');
                inputs.forEach(input => {
                    total += parseFloat(input.value) || 0;
                });
                tr.querySelector('.row-total-input').value = total.toFixed(2);
            }

            addRowBtn.addEventListener('click', addEnvelopeRow);

            // Member Lookup Function
            function lookupMember(inputElement) {
                const envelopeNumber = inputElement.value.trim();
                const row = inputElement.closest('tr');
                const nameInput = row.querySelector('.member-name-input');
                const errorDiv = row.querySelector('.member-error');

                if (!envelopeNumber) {
                    nameInput.value = '';
                    errorDiv.textContent = '';
                    return;
                }

                // Disable input while searching
                inputElement.disabled = true;
                nameInput.value = 'Searching...';

                fetch(`/member/lookup?envelope_number=${envelopeNumber}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.exists) {
                            nameInput.value = data.name;
                            nameInput.classList.remove('is-invalid');
                            nameInput.classList.add('is-valid');
                            errorDiv.textContent = '';
                        } else {
                            nameInput.value = '';
                            nameInput.classList.remove('is-valid');
                            nameInput.classList.add('is-invalid');
                            errorDiv.textContent = 'Member not found';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        nameInput.value = 'Error lookup';
                    })
                    .finally(() => {
                        inputElement.disabled = false;
                    });
            }

            // Calculate Total Function
            function calculateTotal() {
                if (envelopeSection.style.display === 'none') return;

                let total = 0;
                const type = offeringTypeSelect.value;

                if (type === 'sunday_offering') {
                    document.querySelectorAll('.row-total-input').forEach(input => {
                        total += parseFloat(input.value) || 0;
                    });
                } else {
                    document.querySelectorAll('.amount-input').forEach(input => {
                        total += parseFloat(input.value) || 0;
                    });
                }

                amountInput.value = total.toFixed(2);
            }

            // Form Submission Handling
            form.addEventListener('submit', function (e) {
                console.log('Form submission triggered. Offering Type:', offeringTypeSelect.value);

                if (envelopeSection.style.display !== 'none') {
                    const items = [];
                    const rows = itemsTableBody.querySelectorAll('tr');
                    let hasIncompleteRows = false;

                    console.log('Processing envelope rows. Total rows:', rows.length);

                    rows.forEach((row, index) => {
                        const envelopeInput = row.querySelector('.envelope-input');
                        const envelope = envelopeInput ? envelopeInput.value.trim() : '';

                        if (offeringTypeSelect.value === 'sunday_offering') {
                            const umoja = row.querySelector('.umoja-input').value.trim();
                            const jengo = row.querySelector('.jengo-input').value.trim();
                            const ahadi = row.querySelector('.ahadi-input').value.trim();
                            const other = row.querySelector('.other-input').value.trim();

                            if (envelope && (umoja || jengo || ahadi || other)) {
                                items.push({
                                    envelope_number: envelope,
                                    amount_umoja: umoja || 0,
                                    amount_jengo: jengo || 0,
                                    amount_ahadi: ahadi || 0,
                                    amount_other: other || 0
                                });
                            } else if (envelope || umoja || jengo || ahadi || other) {
                                hasIncompleteRows = true;
                            }
                        } else {
                            const amountInput = row.querySelector('.amount-input');
                            const amount = amountInput ? amountInput.value.trim() : '';

                            if (envelope && amount) {
                                items.push({
                                    envelope_number: envelope,
                                    amount: amount
                                });
                            } else if (envelope || amount) {
                                // One field filled but not both
                                hasIncompleteRows = true;
                                console.warn(`Row ${index + 1} is incomplete. Envelope: "${envelope}", Amount: "${amount}"`);
                            }
                        }
                    });

                    // Validate that at least one envelope item exists
                    if (items.length === 0 || hasIncompleteRows) {
                        e.preventDefault();
                        let msg = 'ERROR: You must add at least one complete envelope to the breakdown!\n\n';
                        if (hasIncompleteRows) {
                            msg = 'ERROR: You have incomplete envelope rows!\n\nPlease ensure both "Envelope Number" and "Amount" are filled for every row you added.';
                        } else {
                            msg += 'For Sadaka ya Umoja and Sadaka ya Jengo, individual member contributions MUST be tracked.\n\nClick "Add Envelope" and enter each member\'s envelope number and amount.';
                        }
                        alert(msg);
                        return false;
                    }

                    itemsJsonInput.value = JSON.stringify(items);
                    console.log('Success! Submitting with items:', items);
                } else {
                    console.log('Regular offering submission - no breakdown required.');
                }
            });

            // Existing event listeners
            const serviceSelect = document.getElementById('service_id');
            if (serviceSelect) {
                serviceSelect.addEventListener('change', function () {
                    const selectedOption = this.options[this.selectedIndex];
                    
                    // Set service type if available (works for both scheduled and static)
                    if (selectedOption.dataset.type) {
                        const type = selectedOption.dataset.type;
                        document.getElementById('service_type').value = type;

                        // Logic to hide/show Offering Type
                        const offeringTypeContainer = document.getElementById('offering_type_container');
                        const offeringTypeSelect = document.getElementById('offering_type');
                        
                        if (type === 'sunday_service') {
                            // Show Offering Type for Sunday Service
                            offeringTypeContainer.style.display = 'block';
                            // We don't necessarily reset the value here, let user choose
                        } else {
                            // Hide Offering Type for Mid-week and set to 'general'
                            offeringTypeContainer.style.display = 'none';
                            offeringTypeSelect.value = 'general';
                            // Trigger change event to ensure dependent logic (breakdown hiding) runs
                            offeringTypeSelect.dispatchEvent(new Event('change'));
                        }

                    } else {
                        document.getElementById('service_type').value = '';
                        // If no service selected, show offering type and maybe reset?
                        // Showing it is safer so they can choose offering type manually if needed
                        document.getElementById('offering_type_container').style.display = 'block';
                    }

                    // Set date only if it's a scheduled service
                    if (selectedOption.value && selectedOption.dataset.date) {
                        document.getElementById('offering_date').value = selectedOption.dataset.date;
                    }
                });
            }

            const collectionMethodSelect = document.getElementById('collection_method');
            if (collectionMethodSelect) {
                collectionMethodSelect.addEventListener('change', function () {
                    const referenceGroup = document.getElementById('reference_number_group');
                    if (this.value === 'mobile_money' || this.value === 'bank_transfer') {
                        referenceGroup.style.display = 'block';
                    } else {
                        referenceGroup.style.display = 'none';
                    }
                });
            }
        });
    </script>
@endsection