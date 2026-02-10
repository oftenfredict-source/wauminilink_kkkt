@extends('layouts.index')

@section('content')
<div class="container-fluid px-2 px-md-5 py-4">
    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
        <div class="card-header bg-info d-flex flex-column flex-md-row justify-content-between align-items-center py-4 px-4 border-0">
            <span class="fs-5 fw-bold text-dark d-flex align-items-center">
                <i class="fas fa-child me-2"></i> 
                <span>Child Details - {{ $child->full_name ?? 'N/A' }}</span>
            </span>
            @php
                $previousUrl = url()->previous();
                $backLink = route('reports.welfare.unified');
                $backText = 'Back to Report';
                
                if (str_contains($previousUrl, 'members')) {
                    $backLink = $previousUrl;
                    $backText = 'All Member';
                }
            @endphp
            <div class="mt-3 mt-md-0">
                <a href="{{ $backLink }}" class="btn btn-outline-dark btn-sm shadow-sm me-2">
                    <i class="fas fa-arrow-left me-1"></i> {{ $backText }}
                </a>
                <a href="{{ route('children.edit', $child->id) }}" class="btn btn-dark btn-sm shadow-sm me-2 text-white">
                    <i class="fas fa-edit me-1"></i> Edit
                </a>
                <button onclick="confirmDelete({{ $child->id }})" class="btn btn-danger btn-sm shadow-sm">
                    <i class="fas fa-trash me-1"></i> Delete
                </button>
            </div>
        </div>
        <div class="card-body bg-light px-4 py-4">
            
            <div class="row g-4">
                <!-- Personal Information -->
                <div class="col-md-7">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-primary text-white py-3">
                            <h5 class="mb-0"><i class="fas fa-user-graduate me-2"></i>Personal Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4 text-center mb-3">
                                    <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 120px; height: 120px;">
                                        <i class="fas fa-child fa-4x text-white"></i>
                                    </div>
                                    <div class="mt-3">
                                        <span class="badge bg-{{ $child->gender === 'male' ? 'primary' : 'info' }} px-3 py-2 rounded-pill">
                                            {{ ucfirst($child->gender) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <label class="text-muted small">Full Name</label>
                                            <p class="fw-bold fs-5">{{ $child->full_name ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="text-muted small">Date of Birth</label>
                                            <p class="fw-bold">{{ $child->date_of_birth ? \Carbon\Carbon::parse($child->date_of_birth)->format('M d, Y') : 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="text-muted small">Age</label>
                                            <p class="fw-bold">{{ $child->getAge() }} years</p>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="text-muted small">Age Group</label>
                                            <p class="fw-bold">
                                                @php
                                                    $ageGroup = $child->getAgeGroup();
                                                    $ageGroupLabels = [
                                                        'infant' => 'Infant (< 3)',
                                                        'sunday_school' => 'Sunday School (3-12)',
                                                        'teenager' => 'Teenager (13-17)'
                                                    ];
                                                @endphp
                                                {{ $ageGroupLabels[$ageGroup] ?? 'Adult (18+)' }}
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="text-muted small">Church Member?</label>
                                            <p class="fw-bold">
                                                @if($child->is_church_member)
                                                    <span class="text-success"><i class="fas fa-check-circle me-1"></i> Yes</span>
                                                @else
                                                    <span class="text-muted"><i class="fas fa-times-circle me-1"></i> No</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Family & Guardianship -->
                <div class="col-md-5">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-success text-white py-3">
                            <h5 class="mb-0"><i class="fas fa-users me-2"></i>Family & Guardianship</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                @php
                                    $memberParents = $child->getMemberParents();
                                @endphp

                                @if($memberParents->isNotEmpty())
                                    <div class="col-12 mb-2">
                                        <h6 class="text-muted small mb-3 uppercase">Church Member Parents</h6>
                                        <div class="list-group list-group-flush border-top border-bottom">
                                            @foreach($memberParents as $parent)
                                                <div class="list-group-item px-0 py-3 bg-transparent">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <div class="fw-bold fs-6">
                                                                <i class="fas fa-user-circle text-success me-2"></i>
                                                                {{ $parent->full_name }}
                                                            </div>
                                                            <div class="small mt-1">
                                                                @php
                                                                    $role = 'Parent';
                                                                    $badgeClass = 'bg-secondary';
                                                                    
                                                                    if (strtolower($parent->member_type) === 'father' || $parent->gender === 'male') {
                                                                        $role = 'Father';
                                                                        $badgeClass = 'bg-primary';
                                                                    } elseif (strtolower($parent->member_type) === 'mother' || $parent->gender === 'female') {
                                                                        $role = 'Mother';
                                                                        $badgeClass = 'bg-danger';
                                                                    }
                                                                @endphp
                                                                <span class="badge {{ $badgeClass }} px-2 shadow-sm">{{ $role }}</span>
                                                                <span class="ms-2 text-muted"><i class="fas fa-id-badge me-1"></i>{{ $parent->member_id }}</span>
                                                            </div>
                                                            @if($parent->phone_number)
                                                                <div class="small mt-2">
                                                                    <a href="tel:{{ $parent->phone_number }}" class="text-decoration-none text-muted">
                                                                        <i class="fas fa-phone-alt me-1"></i> {{ $parent->phone_number }}
                                                                    </a>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <a href="{{ route('members.show', $parent->id) }}" class="btn btn-sm btn-outline-success rounded-pill">
                                                            View Profile <i class="fas fa-chevron-right ms-1"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <div class="col-12">
                                        <label class="text-muted small">Parent/Guardian Name</label>
                                        <p class="fw-bold">{{ $child->parent_name ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-6">
                                        <label class="text-muted small">Relationship</label>
                                        <p class="fw-bold">{{ $child->parent_relationship ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-6">
                                        <label class="text-muted small">Contact Phone</label>
                                        <p class="fw-bold">
                                            @if($child->parent_phone)
                                                <a href="tel:{{ $child->parent_phone }}" class="text-decoration-none">
                                                    <i class="fas fa-phone-alt me-1"></i> {{ $child->parent_phone }}
                                                </a>
                                            @else
                                                N/A
                                            @endif
                                        </p>
                                    </div>
                                @endif

                                <div class="col-12 mt-2">
                                    <label class="text-muted small">Branch/Campus</label>
                                    <p class="fw-bold text-primary">{{ $child->campus->name ?? 'None' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Social Welfare Status -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-danger text-white py-3">
                            <h5 class="mb-0"><i class="fas fa-hand-holding-heart me-2"></i>Social Welfare Status</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="text-muted small">Orphan Status</label>
                                    <p class="fw-bold">
                                        @php
                                            $orphanLabels = [
                                                'not_orphan' => 'Not Orphan',
                                                'father_deceased' => 'Father Deceased',
                                                'mother_deceased' => 'Mother Deceased',
                                                'both_deceased' => 'Both Deceased'
                                            ];
                                        @endphp
                                        @if($child->orphan_status != 'not_orphan')
                                            <span class="text-danger"><i class="fas fa-exclamation-triangle me-1"></i> {{ $orphanLabels[$child->orphan_status] ?? $child->orphan_status }}</span>
                                        @else
                                            <span class="text-success">Not Orphan</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small">Disability</label>
                                    <p class="fw-bold">
                                        @if($child->disability_status)
                                            <span class="text-warning text-dark"><i class="fas fa-accessible-icon me-1"></i> Yes ({{ $child->disability_type ?? 'Unspecified' }})</span>
                                        @else
                                            <span class="text-muted">None</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small">Vulnerability</label>
                                    <p class="fw-bold">
                                        @if($child->vulnerable_status)
                                            <span class="text-danger"><i class="fas fa-heart me-1"></i> Yes ({{ $child->vulnerable_type ?? 'Unspecified' }})</span>
                                        @else
                                            <span class="text-muted">None</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Religious Milestones -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-primary text-white py-3">
                            <h5 class="mb-0"><i class="fas fa-cross me-2"></i>Religious Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="text-muted small">Baptism Status</label>
                                    <p class="fw-bold">
                                        @if($child->baptism_status === 'baptized')
                                            <span class="text-success"><i class="fas fa-tint me-1"></i> Baptized</span>
                                        @else
                                            <span class="text-muted">Not Baptized</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small">Baptism Date</label>
                                    <p class="fw-bold">{{ $child->baptism_date ? \Carbon\Carbon::parse($child->baptism_date)->format('M d, Y') : 'N/A' }}</p>
                                </div>
                                <div class="col-12">
                                    <label class="text-muted small">Baptized By</label>
                                    <p class="fw-bold">{{ $child->baptized_by ?? 'N/A' }}</p>
                                </div>
                                <div class="col-12">
                                    <label class="text-muted small">Baptism Location</label>
                                    <p class="fw-bold">{{ $child->baptism_location ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Registration Details -->
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-secondary text-white py-3">
                            <h5 class="mb-0"><i class="fas fa-id-card me-2"></i>Registration Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="text-muted small">Biometric Enroll ID</label>
                                    <p class="fw-bold"><code>{{ $child->biometric_enroll_id ?? 'Not Enrolled' }}</code></p>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-muted small">Registered On</label>
                                    <p class="fw-bold">{{ $child->created_at->format('M d, Y H:i') }}</p>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-muted small">Last Updated</label>
                                    <p class="fw-bold">{{ $child->updated_at->format('M d, Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id) {
    if (confirm('Are you sure you want to delete this child\'s record? This action cannot be undone.')) {
        let form = document.createElement('form');
        form.method = 'POST';
        form.action = '/children/' + id;
        
        let csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        
        let method = document.createElement('input');
        method.type = 'hidden';
        method.name = '_method';
        method.value = 'DELETE';
        
        form.appendChild(csrf);
        form.appendChild(method);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
