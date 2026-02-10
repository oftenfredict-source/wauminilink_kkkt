@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mt-4"><i class="fas fa-exchange-alt me-2"></i>Review Transition</h1>
        @php
            $indexRoute = auth()->user()->isAdmin() ? 'admin.transitions.index' : 'pastor.transitions.index';
        @endphp
        <a href="{{ route($indexRoute) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Transitions
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Child Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="30%">Full Name:</th>
                            <td><strong>{{ $transition->child->full_name }}</strong></td>
                        </tr>
                        <tr>
                            <th>Age:</th>
                            <td><span class="badge bg-info">{{ $transition->child->getAge() }} years</span></td>
                        </tr>
                        <tr>
                            <th>Gender:</th>
                            <td>
                                <span class="badge bg-{{ $transition->child->gender === 'male' ? 'primary' : 'danger' }}">
                                    {{ ucfirst($transition->child->gender) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Date of Birth:</th>
                            <td>{{ $transition->child->date_of_birth ? $transition->child->date_of_birth->format('M d, Y') : '—' }}</td>
                        </tr>
                        <tr>
                            <th>Phone Number:</th>
                            <td>{{ $transition->child->phone_number ?? ($transition->child->member->phone_number ?? '—') }}</td>
                        </tr>
                        <tr>
                            <th>Parent/Guardian:</th>
                            <td>
                                @if($transition->child->member)
                                    <a href="{{ route('members.view') }}?search={{ $transition->child->member->member_id }}" class="text-decoration-none">
                                        {{ $transition->child->member->full_name }}
                                    </a>
                                    <br><small class="text-muted">Member ID: {{ $transition->child->member->member_id }}</small>
                                @else
                                    {{ $transition->child->parent_name ?? '—' }}
                                    @if($transition->child->parent_phone)
                                        <br><small class="text-muted">Phone: {{ $transition->child->parent_phone }}</small>
                                    @endif
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Current Campus:</th>
                            <td>
                                @if($transition->child->campus)
                                    <span class="badge bg-primary">{{ $transition->child->campus->name }}</span>
                                @else
                                    <span class="text-muted">Not assigned</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Current Community:</th>
                            <td>
                                @if($transition->child->community)
                                    <span class="badge bg-info">{{ $transition->child->community->name }}</span>
                                @else
                                    <span class="text-muted">Not assigned</span>
                                @endif
                            </td>
                        </tr>
                        @if($transition->child->region || $transition->child->district)
                        <tr>
                            <th>Location:</th>
                            <td>
                                {{ implode(', ', array_filter([$transition->child->city_town, $transition->child->district, $transition->child->region])) }}
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Approval Form -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-check-circle me-2"></i>Approve Transition</h5>
                </div>
                <div class="card-body">
                    @php
                        $approveRoute = auth()->user()->isAdmin() ? 'admin.transitions.approve' : 'pastor.transitions.approve';
                    @endphp
                    <form action="{{ route($approveRoute, $transition) }}" method="POST" id="approveForm">
                        @csrf
                        <div class="mb-3">
                            <label for="campus_id" class="form-label">Assign to Campus <span class="text-danger">*</span></label>
                            <select name="campus_id" id="campus_id" class="form-select" required>
                                <option value="">Select Campus...</option>
                                @foreach($campuses as $campus)
                                    <option value="{{ $campus->id }}" {{ $transition->child->campus_id == $campus->id ? 'selected' : '' }}>
                                        {{ $campus->name }} {{ $campus->is_main_campus ? '(Main)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Select the campus where this new member will be assigned.</small>
                        </div>

                        <div class="mb-3">
                            <label for="community_id" class="form-label">Assign to Community (Optional)</label>
                            <select name="community_id" id="community_id" class="form-select">
                                <option value="">Select Community...</option>
                                @foreach($communities as $community)
                                    <option value="{{ $community->id }}" {{ $transition->child->community_id == $community->id ? 'selected' : '' }}>
                                        {{ $community->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Communities will be loaded based on selected campus.</small>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes (Optional)</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Add any notes about this transition..."></textarea>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Note:</strong> Upon approval, a new member record will be created with:
                            <ul class="mb-0 mt-2">
                                <li>A unique Member ID</li>
                                <li>Member type: Independent</li>
                                <li>Membership type: Permanent</li>
                                <li>Parent/guardian information preserved as reference</li>
                            </ul>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to approve this transition? A new member record will be created.');">
                                <i class="fas fa-check me-2"></i>Approve & Convert to Member
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Rejection Form -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-times-circle me-2"></i>Reject Transition</h5>
                </div>
                <div class="card-body">
                    @php
                        $rejectRoute = auth()->user()->isAdmin() ? 'admin.transitions.reject' : 'pastor.transitions.reject';
                    @endphp
                    <form action="{{ route($rejectRoute, $transition) }}" method="POST" id="rejectForm">
                        @csrf
                        <div class="mb-3">
                            <label for="rejection_reason" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                            <textarea name="rejection_reason" id="rejection_reason" class="form-control" rows="3" required placeholder="Please provide a reason for rejecting this transition..."></textarea>
                            <small class="text-muted">This reason will be recorded for audit purposes.</small>
                        </div>

                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to reject this transition?');">
                            <i class="fas fa-times me-2"></i>Reject Transition
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Transition Details</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Status:</strong><br>
                        <span class="badge bg-warning">Pending Review</span>
                    </div>
                    <div class="mb-3">
                        <strong>Request Date:</strong><br>
                        <small class="text-muted">{{ $transition->created_at->format('M d, Y h:i A') }}</small>
                    </div>
                    <div class="mb-3">
                        <strong>Eligibility:</strong><br>
                        <ul class="mb-0">
                            <li>✓ Age: {{ $transition->child->getAge() }} years (≥ 18)</li>
                            <li>✓ Church Member: Yes</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const campusSelect = document.getElementById('campus_id');
    const communitySelect = document.getElementById('community_id');

    if (campusSelect) {
        campusSelect.addEventListener('change', function() {
            const campusId = this.value;
            
            // Clear community options
            communitySelect.innerHTML = '<option value="">Select Community...</option>';
            
            if (campusId) {
                // Load communities for selected campus
                fetch(`/campuses/${campusId}/communities/ajax`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.communities && data.communities.length > 0) {
                            data.communities.forEach(community => {
                                const option = document.createElement('option');
                                option.value = community.id;
                                option.textContent = community.name;
                                communitySelect.appendChild(option);
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error loading communities:', error);
                    });
            }
        });
    }
});
</script>
@endsection

