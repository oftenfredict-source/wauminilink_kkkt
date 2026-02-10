@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mt-4">
                <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                Members Living Outside Main Church Area
            </h1>
            <p class="text-muted mb-0">
                View and filter members who live outside the main parish/church area
                @if(isset($mainCampusRegion) && $mainCampusRegion)
                    <br><small>Main Campus Region: <strong>{{ $mainCampusRegion }}</strong></small>
                @endif
            </p>
        </div>
        <div>
            <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('dashboard.pastor') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filter Section -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filter by Region</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ auth()->user()->isAdmin() ? route('admin.members.outside-main-area') : route('pastor.members.outside-main-area') }}" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="region" class="form-label">Region</label>
                        <select name="region" id="region" class="form-select">
                            <option value="">All Regions</option>
                            @if(isset($tanzaniaLocations) && is_array($tanzaniaLocations) && count($tanzaniaLocations) > 0)
                                @foreach($tanzaniaLocations as $region => $districts)
                                    <option value="{{ $region }}" {{ request('region') === $region ? 'selected' : '' }}>
                                        {{ $region }}
                                    </option>
                                @endforeach
                            @endif
                            @if(isset($allRegions) && $allRegions->count() > 0)
                                @foreach($allRegions as $region)
                                    <option value="{{ $region }}" {{ request('region') === $region ? 'selected' : '' }}>
                                        {{ $region }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-6 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-2"></i>Apply Filter
                        </button>
                        <a href="{{ auth()->user()->isAdmin() ? route('admin.members.outside-main-area') : route('pastor.members.outside-main-area') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-redo me-2"></i>Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Adult Members</h6>
                            <h2 class="mb-0">{{ number_format(($members ? $members->total() : 0) + ($adultChildren ? $adultChildren->count() : 0)) }}</h2>
                            @if($adultChildren && $adultChildren->count() > 0)
                                <small class="text-muted">({{ $members ? $members->total() : 0 }} members, {{ $adultChildren->count() }} adult dependents)</small>
                            @endif
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-user fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Children (Under 18)</h6>
                            <h2 class="mb-0">{{ number_format($children ? $children->count() : 0) }}</h2>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-child fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Total</h6>
                            <h2 class="mb-0">{{ number_format(($members ? $members->total() : 0) + ($adultChildren ? $adultChildren->count() : 0) + ($children ? $children->count() : 0)) }}</h2>
                        </div>
                        <div class="text-info">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <!-- Bulk SMS Section -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-sms me-2"></i>Send Bulk SMS</h5>
            <span class="badge bg-light text-dark">
                Target: {{ request('region') ?: 'All Regions' }} 
                ({{ number_format(($members ? $members->total() : 0) + ($adultChildren ? $adultChildren->count() : 0)) }} adult recipients)
            </span>
        </div>
        <div class="card-body">
            <form id="bulkSmsForm">
                @csrf
                <input type="hidden" name="region" value="{{ request('region') }}">
                <div class="mb-3">
                    <label for="smsMessage" class="form-label fw-bold">Message Content</label>
                    <textarea class="form-control" id="smsMessage" name="message" rows="3" 
                        placeholder="Type your message here..." maxlength="160"></textarea>
                    <div id="charCount" class="form-text text-end">0 / 160 characters</div>
                </div>
                <div class="d-grid d-md-flex justify-content-md-end">
                    <button type="button" class="btn btn-dark px-4" id="btnPreviewSms" 
                        @if((($members ? $members->total() : 0) + ($adultChildren ? $adultChildren->count() : 0)) == 0) disabled @endif>
                        <i class="fas fa-paper-plane me-2"></i>Send SMS to {{ number_format(($members ? $members->total() : 0) + ($adultChildren ? $adultChildren->count() : 0)) }} Members
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- SMS Confirmation Modal -->
    <div class="modal fade" id="smsConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title">Confirm Bulk SMS</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info py-2">
                        <strong>Recipients:</strong> {{ number_format(($members ? $members->total() : 0) + ($adultChildren ? $adultChildren->count() : 0)) }} members
                        <br><strong>Location:</strong> {{ request('region') ?: 'All Regions Outside Main Area' }}
                    </div>
                    <p class="mb-2 fw-bold">Message Preview:</p>
                    <div class="p-3 bg-light rounded italic border mb-3" id="previewText" style="font-style: italic;"></div>
                    <p class="text-danger small"><i class="fas fa-exclamation-triangle me-1"></i> This action will send SMS messages to all selected recipients via the configured provider.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="btnConfirmSend">
                        <span id="sendSpinner" class="spinner-border spinner-border-sm d-none me-2"></span>
                        Confirm and Send
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Adult Members Section -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-user me-2"></i>Adult Members
                <span class="badge bg-light text-dark ms-2">{{ ($members ? $members->total() : 0) + ($adultChildren ? $adultChildren->count() : 0) }}</span>
                @if($adultChildren && $adultChildren->count() > 0)
                    <small class="ms-2">({{ $members ? $members->total() : 0 }} members, {{ $adultChildren->count() }} adult dependents)</small>
                @endif
            </h5>
        </div>
        <div class="card-body">
            @if(($members && $members->count() > 0) || ($adultChildren && $adultChildren->count() > 0))
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Member ID</th>
                                <th>Type</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Residence Region</th>
                                <th>Residence District</th>
                                <th>Campus</th>
                                <th>Parent/Guardian</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $adultCounter = 1; @endphp
                            {{-- Regular Adult Members --}}
                            @foreach($members as $member)
                                <tr>
                                    <td>{{ $adultCounter++ + ($members->currentPage() - 1) * $members->perPage() }}</td>
                                    <td><strong>{{ $member->full_name }}</strong></td>
                                    <td><span class="badge bg-secondary">{{ $member->member_id }}</span></td>
                                    <td><span class="badge bg-primary">Adult Member</span></td>
                                    <td>{{ $member->phone_number ?? '—' }}</td>
                                    <td>{{ $member->email ?? '—' }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $member->residence_region ?? '—' }}</span>
                                    </td>
                                    <td>{{ $member->residence_district ?? '—' }}</td>
                                    <td>
                                        @if($member->campus)
                                            <span class="badge bg-primary">{{ $member->campus->name }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>—</td>
                                    <td>
                                        <a href="{{ route('members.view') }}?search={{ $member->member_id }}" class="btn btn-sm btn-info" title="View Member">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            {{-- Adult Children (18+) --}}
                            @if($adultChildren && $adultChildren->count() > 0)
                                @foreach($adultChildren as $adultChild)
                                    <tr>
                                        <td>{{ $adultCounter++ }}</td>
                                        <td><strong>{{ $adultChild->full_name }}</strong></td>
                                        <td>
                                            @if($adultChild->member && $adultChild->member->member_id)
                                                <span class="badge bg-secondary">{{ $adultChild->member->member_id }}-CH</span>
                                            @else
                                                <span class="badge bg-info">Dependent</span>
                                            @endif
                                        </td>
                                        <td><span class="badge bg-warning">Adult Dependent (18+)</span></td>
                                        <td>{{ $adultChild->phone_number ?? ($adultChild->member->phone_number ?? '—') }}</td>
                                        <td>{{ $adultChild->member->email ?? '—' }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $adultChild->region ?? '—' }}</span>
                                        </td>
                                        <td>{{ $adultChild->district ?? '—' }}</td>
                                        <td>
                                            @if($adultChild->member && $adultChild->member->campus)
                                                <span class="badge bg-primary">{{ $adultChild->member->campus->name }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($adultChild->member)
                                                <a href="{{ route('members.view') }}?search={{ $adultChild->member->member_id }}" class="text-decoration-none">
                                                    {{ $adultChild->member->full_name }}
                                                </a>
                                            @else
                                                {{ $adultChild->parent_name ?? '—' }}
                                            @endif
                                        </td>
                                        <td>
                                            @if($adultChild->member)
                                                <a href="{{ route('members.view') }}?search={{ $adultChild->member->member_id }}" class="btn btn-sm btn-info" title="View Parent">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination (only for regular members) -->
                @if($members && $members->count() > 0)
                    <div class="d-flex justify-content-center mt-4">
                        {{ $members->links() }}
                    </div>
                @endif
            @else
                <div class="text-center text-muted py-5">
                    <i class="fas fa-user-slash fa-3x mb-3 d-block"></i>
                    <p class="mb-0">No adult members found living outside the main church area.</p>
                    @if(request('region'))
                        <a href="{{ auth()->user()->isAdmin() ? route('admin.members.outside-main-area') : route('pastor.members.outside-main-area') }}" class="btn btn-link mt-2">Clear filter</a>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Children Section (Under 18) -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">
                <i class="fas fa-child me-2"></i>Children Living Outside Main Area (Under 18)
                <span class="badge bg-light text-dark ms-2">{{ $children ? $children->count() : 0 }}</span>
            </h5>
        </div>
        <div class="card-body">
            @if($children && $children->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Gender</th>
                                <th>Date of Birth</th>
                                <th>Phone Number</th>
                                <th>Region</th>
                                <th>District</th>
                                <th>City/Town</th>
                                <th>Parent/Guardian</th>
                                <th>Current Church</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($children as $child)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><strong>{{ $child->full_name }}</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $child->gender === 'male' ? 'primary' : 'danger' }}">
                                            {{ ucfirst($child->gender) }}
                                        </span>
                                    </td>
                                    <td>{{ $child->date_of_birth ? $child->date_of_birth->format('M d, Y') : '—' }}</td>
                                    <td>{{ $child->phone_number ?? '—' }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $child->region ?? '—' }}</span>
                                    </td>
                                    <td>{{ $child->district ?? '—' }}</td>
                                    <td>{{ $child->city_town ?? '—' }}</td>
                                    <td>
                                        @if($child->member)
                                            <a href="{{ route('members.view') }}?search={{ $child->member->member_id }}" class="text-decoration-none">
                                                {{ $child->member->full_name }}
                                            </a>
                                        @else
                                            {{ $child->parent_name ?? '—' }}
                                        @endif
                                    </td>
                                    <td>{{ $child->current_church_attended ?? '—' }}</td>
                                    <td>
                                        @if($child->member)
                                            <a href="{{ route('members.view') }}?search={{ $child->member->member_id }}" class="btn btn-sm btn-info" title="View Parent">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center text-muted py-5">
                    <i class="fas fa-child fa-3x mb-3 d-block"></i>
                    <p class="mb-0">No children found living outside the main church area.</p>
                    @if(request('region'))
                        <a href="{{ auth()->user()->isAdmin() ? route('admin.members.outside-main-area') : route('pastor.members.outside-main-area') }}" class="btn btn-link mt-2">Clear filter</a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form when region changes
    const regionSelect = document.getElementById('region');
    if (regionSelect) {
        regionSelect.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    }

    // SMS Character Counter
    const smsMessage = document.getElementById('smsMessage');
    const charCount = document.getElementById('charCount');
    
    if (smsMessage && charCount) {
        smsMessage.addEventListener('input', function() {
            const count = this.value.length;
            charCount.textContent = `${count} / 160 characters`;
            if (count > 160) {
                charCount.classList.add('text-danger');
            } else {
                charCount.classList.remove('text-danger');
            }
        });
    }

    // Modal Preview
    const btnPreviewSms = document.getElementById('btnPreviewSms');
    const smsConfirmModal = new bootstrap.Modal(document.getElementById('smsConfirmModal'));
    const previewText = document.getElementById('previewText');

    if (btnPreviewSms) {
        btnPreviewSms.addEventListener('click', function() {
            const message = smsMessage.value.trim();
            if (!message) {
                alert('Please type a message before sending.');
                smsMessage.focus();
                return;
            }
            previewText.textContent = message;
            smsConfirmModal.show();
        });
    }

    // Confirm and Send SMS
    const btnConfirmSend = document.getElementById('btnConfirmSend');
    const sendSpinner = document.getElementById('sendSpinner');
    const bulkSmsForm = document.getElementById('bulkSmsForm');

    if (btnConfirmSend) {
        btnConfirmSend.addEventListener('click', function() {
            const message = smsMessage.value.trim();
            const region = regionSelect ? regionSelect.value : '';
            
            // Disable buttons and show spinner
            btnConfirmSend.disabled = true;
            sendSpinner.classList.remove('d-none');
            
            const smsRoute = "{{ auth()->user()->isAdmin() ? route('admin.members.outside-main-area.send-sms') : route('pastor.members.outside-main-area.send-sms') }}";
            
            fetch(smsRoute, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    message: message,
                    region: region
                })
            })
            .then(response => response.json())
            .then(data => {
                smsConfirmModal.hide();
                if (data.success) {
                    alert(data.message);
                    smsMessage.value = '';
                    if (charCount) charCount.textContent = '0 / 160 characters';
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An unexpected error occurred while sending SMS.');
            })
            .finally(() => {
                btnConfirmSend.disabled = false;
                sendSpinner.classList.add('d-none');
            });
        });
    }
});
</script>
@endsection

