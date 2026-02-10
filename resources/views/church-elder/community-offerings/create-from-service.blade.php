@extends('layouts.index')

@section('content')
    <div class="container-fluid px-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h1 class="h3 mb-0"><i class="fas fa-money-bill-wave me-2 text-success"></i>Record Mid-Week
                                    Service Offering</h1>
                                <p class="text-muted mb-0">{{ $community->name }}</p>
                            </div>
                            <a href="{{ route('church-elder.services', $community->id) }}" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left me-1"></i> Back to Services
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($existingOffering)
            <div class="row mb-4">
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        An offering has already been recorded for this service.
                        <a href="{{ route('church-elder.community-offerings.show', $existingOffering->id) }}"
                            class="alert-link">View existing offering</a>
                    </div>
                </div>
            </div>
        @endif

        @if(!$canRecordOffering)
            <div class="row mb-4">
                <div class="col-12">
                    <div class="alert alert-warning d-flex align-items-center" role="alert">
                        <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                        <div>
                            <strong>Offering Recording Restricted</strong>
                            <p class="mb-0">{{ $timeRestrictionMessage }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

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
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Service Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Service Type:</strong><br>
                                <span
                                    class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $service->service_type)) }}</span>
                            </div>
                            <div class="col-md-6">
                                <strong>Service Date:</strong><br>
                                {{ $service->service_date->format('F d, Y') }}
                                @if($service->start_time)
                                    <br><small class="text-muted">Start Time:
                                        {{ \Carbon\Carbon::parse($service->start_time)->format('h:i A') }}</small>
                                @endif
                            </div>
                        </div>
                        @if($service->theme)
                            <div class="row mb-3">
                                <div class="col-12">
                                    <strong>Theme:</strong><br>
                                    {{ $service->theme }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Offering Details</h5>
                    </div>
                    <div class="card-body">
                        <form id="communityOfferingForm" action="{{ route('church-elder.community-offerings.store') }}"
                            method="POST">
                            @csrf
                            <input type="hidden" name="community_id" value="{{ $community->id }}">
                            <input type="hidden" name="service_id" value="{{ $service->id }}">
                            <input type="hidden" name="service_type" value="{{ $service->service_type }}">
                            <input type="hidden" name="offering_date" value="{{ $service->service_date->format('Y-m-d') }}">

                            <div class="mb-3">
                                <label for="offering_type" class="form-label">Offering Type <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('offering_type') is-invalid @enderror" id="offering_type"
                                    name="offering_type" required>
                                    <option value="">Select offering type...</option>
                                    <option value="general" {{ old('offering_type') == 'general' ? 'selected' : '' }}>General
                                        Offering</option>
                                    <option value="sadaka_umoja" {{ old('offering_type') == 'sadaka_umoja' ? 'selected' : '' }}>Sadaka ya Umoja</option>
                                    <option value="sadaka_jengo" {{ old('offering_type') == 'sadaka_jengo' ? 'selected' : '' }}>Sadaka ya Jengo</option>
                                    <option value="other" {{ old('offering_type') == 'other' ? 'selected' : '' }}>Other
                                        (Mengineyo)</option>
                                </select>
                                @error('offering_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3" id="other_description_container" style="display: none;">
                                <div class="row g-2">
                                    <div class="col-md-6 mb-2">
                                        <label for="other_category" class="form-label">Category <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="other_category" name="other_category">
                                            <option value="">Select category...</option>
                                            @foreach($offeringCategories as $category => $subtypes)
                                                <option value="{{ $category }}">{{ $category }}</option>
                                            @endforeach
                                            <option value="Special">Special / Other</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label for="other_subtype" class="form-label">Offering Analyzed <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="other_subtype" name="other_subtype">
                                            <option value="">First select category...</option>
                                        </select>
                                    </div>
                                    <div class="col-12" id="manual_description_group" style="display: none;">
                                        <label for="other_description" class="form-label">Specify Offering Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="other_description" name="notes"
                                            placeholder="Enter offering name (e.g. Shukrani Maalumu)"
                                            value="{{ old('notes') }}">
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
                                        <thead>
                                            <tr>
                                                <th style="width: 200px;">Envelope Number</th>
                                                <th style="width: 250px;">Member Name</th>
                                                <th>Amount</th>
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
                                    value="{{ old('amount', $service->offerings_amount ?? '') }}" required>
                                @error('amount')
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
                                <a href="{{ route('church-elder.services', $community->id) }}"
                                    class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-success" @if(!$canRecordOffering) disabled @endif>
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
            const otherCategorySelect = document.getElementById('other_category');
            const otherSubtypeSelect = document.getElementById('other_subtype');
            const manualDescriptionGroup = document.getElementById('manual_description_group');
            const otherDescriptionInput = document.getElementById('other_description');

            const offeringTypeSelect = document.getElementById('offering_type');
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
                    manualDescriptionGroup.style.display = 'none'; // Ensure manual description is hidden
                }

                if (type === 'sadaka_umoja' || type === 'sadaka_jengo') {
                    envelopeSection.style.display = 'block';
                    amountInput.setAttribute('readonly', 'readonly');
                    // If table is empty, add first row
                    if (itemsTableBody.children.length === 0) {
                        addEnvelopeRow();
                    }
                } else {
                    envelopeSection.style.display = 'none';
                    // Only make amount editable if it's not pre-filled from service report (though here it's likely editable)
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
                const rowId = 'row_' + Date.now();
                const tr = document.createElement('tr');
                tr.id = rowId;
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
                itemsTableBody.appendChild(tr);

                // Add event listeners for the new row
                const envelopeInput = tr.querySelector('.envelope-input');
                const amountInputRow = tr.querySelector('.amount-input');
                const removeBtn = tr.querySelector('.remove-row-btn');

                envelopeInput.addEventListener('blur', function () {
                    lookupMember(this);
                });

                amountInputRow.addEventListener('input', calculateTotal);

                removeBtn.addEventListener('click', function () {
                    tr.remove();
                    calculateTotal();
                });
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
                const amountInputs = document.querySelectorAll('.amount-input');

                amountInputs.forEach(input => {
                    const val = parseFloat(input.value);
                    if (!isNaN(val)) {
                        total += val;
                    }
                });

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
                        const amountInput = row.querySelector('.amount-input');

                        if (!envelopeInput || !amountInput) return;

                        const envelope = envelopeInput.value.trim();
                        const amount = amountInput.value.trim();

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

            const collectionMethodSelect = document.getElementById('collection_method');
            if (collectionMethodSelect) {
                collectionMethodSelect.addEventListener('change', function () {
                    const referenceGroup = document.getElementById('reference_number_group');
                    if (this.value === 'mobile_money' || this.value === 'bank_transfer') {
                        referenceGroup.style.display = 'block';
                        document.getElementById('reference_number').setAttribute('required', 'required');
                    } else {
                        referenceGroup.style.display = 'none';
                        document.getElementById('reference_number').removeAttribute('required');
                    }
                });
            }
        });
    </script>
@endsection