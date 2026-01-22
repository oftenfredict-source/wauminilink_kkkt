@extends('layouts.index')

@section('content')
<div class="container-fluid px-2 px-md-5 py-4">
    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
        <div class="card-header bg-primary d-flex flex-column flex-md-row justify-content-between align-items-center py-4 px-4 border-0">
            <span class="fs-5 fw-bold text-white d-flex align-items-center">
                <i class="fas fa-user me-2"></i> 
                <span>Member Details - {{ $member->full_name ?? 'N/A' }}</span>
            </span>
            <div class="mt-3 mt-md-0">
                <a href="{{ route('members.index') }}" class="btn btn-outline-light btn-sm shadow-sm me-2">
                    <i class="fas fa-list me-1"></i> All Members
                </a>
                @php
                    $memberId = is_object($member) && property_exists($member, 'id') ? $member->id : (is_array($member) && isset($member['id']) ? $member['id'] : (isset($member->id) ? $member->id : null));
                @endphp
                @if($memberId)
                    <a href="{{ route('members.edit', $memberId) }}" class="btn btn-light btn-sm shadow-sm me-2">
                        <i class="fas fa-edit me-1"></i> Edit
                    </a>
                    <button onclick="confirmDelete({{ $memberId }})" class="btn btn-danger btn-sm shadow-sm">
                        <i class="fas fa-trash me-1"></i> Delete
                    </button>
                @endif
            </div>
        </div>
        <div class="card-body bg-light px-4 py-4">

            <div class="row g-4">
                <!-- Personal Information -->
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-primary text-white py-3">
                            <h5 class="mb-0"><i class="fas fa-user me-2"></i>Personal Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-3 text-center mb-3">
                                    @if(isset($member->profile_picture) && $member->profile_picture)
                                        <img src="{{ Storage::url($member->profile_picture) }}" alt="Profile" class="img-fluid rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                                    @else
                                        <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 150px; height: 150px;">
                                            <i class="fas fa-user fa-4x text-white"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-9">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="text-muted small">Member ID</label>
                                            <p class="fw-bold">{{ $member->member_id ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="text-muted small">Full Name</label>
                                            <p class="fw-bold">{{ $member->full_name ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="text-muted small">Gender</label>
                                            <p class="fw-bold text-capitalize">{{ $member->gender ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="text-muted small">Date of Birth</label>
                                            <p class="fw-bold">{{ $member->date_of_birth ? \Carbon\Carbon::parse($member->date_of_birth)->format('M d, Y') : 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="text-muted small">Membership Type</label>
                                            <p class="fw-bold text-capitalize">{{ $member->membership_type ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="text-muted small">Member Type</label>
                                            <p class="fw-bold text-capitalize">{{ $member->member_type ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="text-muted small">Branch/Campus</label>
                                            <p class="fw-bold">{{ $member->campus->name ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="text-muted small">Community</label>
                                            <p class="fw-bold">{{ $member->community->name ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="text-muted small">Education Level</label>
                                            <p class="fw-bold text-capitalize">{{ str_replace('_', ' ', $member->education_level ?? 'N/A') }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="text-muted small">Profession</label>
                                            <p class="fw-bold">{{ $member->profession ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="text-muted small">NIDA Number</label>
                                            <p class="fw-bold">{{ $member->nida_number ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="text-muted small">Baptism Status</label>
                                            <p class="fw-bold">{{ isset($member->baptized) && $member->baptized ? 'Baptized' : 'Not Baptized' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact & Location Information -->
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-primary text-white py-3">
                            <h5 class="mb-0"><i class="fas fa-phone me-2"></i>Contact & Location Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="text-muted small">Phone Number</label>
                                    <p class="fw-bold">{{ $member->phone_number ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small">Email</label>
                                    <p class="fw-bold">{{ $member->email ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-muted small">Region</label>
                                    <p class="fw-bold">{{ $member->region ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-muted small">District</label>
                                    <p class="fw-bold">{{ $member->district ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-muted small">Ward</label>
                                    <p class="fw-bold">{{ $member->ward ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small">Street</label>
                                    <p class="fw-bold">{{ $member->street ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small">P.O. Box</label>
                                    <p class="fw-bold">{{ $member->address ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small">Tribe</label>
                                    <p class="fw-bold">{{ $member->tribe ?? 'N/A' }}{{ $member->other_tribe ? ' (' . $member->other_tribe . ')' : '' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Current Residence -->
                @if(isset($member->residence_region) || isset($member->residence_district))
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-primary text-white py-3">
                            <h5 class="mb-0"><i class="fas fa-home me-2"></i>Current Residence</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="text-muted small">Region</label>
                                    <p class="fw-bold">{{ $member->residence_region ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-muted small">District</label>
                                    <p class="fw-bold">{{ $member->residence_district ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-muted small">Ward</label>
                                    <p class="fw-bold">{{ $member->residence_ward ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-muted small">Street</label>
                                    <p class="fw-bold">{{ $member->residence_street ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-muted small">Road</label>
                                    <p class="fw-bold">{{ $member->residence_road ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-muted small">House Number</label>
                                    <p class="fw-bold">{{ $member->residence_house_number ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Family Information -->
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-primary text-white py-3">
                            <h5 class="mb-0"><i class="fas fa-users me-2"></i>Family Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="text-muted small">Marital Status</label>
                                    <p class="fw-bold text-capitalize">{{ $member->marital_status ?? 'N/A' }}</p>
                                </div>
                                @if(isset($member->marital_status) && $member->marital_status === 'married')
                                    <div class="col-md-6">
                                        <label class="text-muted small">Wedding Date</label>
                                        <p class="fw-bold">
                                            @if(isset($member->wedding_date) && $member->wedding_date)
                                                {{ \Carbon\Carbon::parse($member->wedding_date)->format('M d, Y') }}
                                            @else
                                                N/A
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-12">
                                        <h6 class="mt-3 mb-2">
                                            <i class="fas fa-heart me-2"></i>Spouse Information
                                            @if(isset($member->spouse_member_id) && $member->spouse_member_id && $member->spouseMember)
                                                <span class="badge bg-success ms-2">Church Member</span>
                                            @elseif(empty($member->spouse_full_name) && empty($member->spouse_phone_number))
                                                <span class="badge bg-warning ms-2 text-dark">No Spouse Information</span>
                                            @else
                                                <span class="badge bg-info ms-2">Not a Church Member</span>
                                            @endif
                                        </h6>
                                        @if(empty($member->spouse_full_name) && empty($member->spouse_phone_number) && !isset($member->spouse_member_id))
                                            <div class="alert alert-info mb-3">
                                                <i class="fas fa-info-circle me-2"></i>
                                                Spouse information has not been added for this member. 
                                                <a href="{{ route('members.edit', $member->id) }}" class="alert-link">Click here to add spouse information</a>.
                                            </div>
                                        @endif
                                        <div class="row g-3">
                                            @php
                                                // Use spouse member details if spouse is a church member, otherwise use basic spouse fields
                                                $spouse = null;
                                                if (isset($member->spouse_member_id) && $member->spouse_member_id && $member->spouseMember) {
                                                    $spouse = $member->spouseMember;
                                                }
                                                
                                                // Helper function to get spouse value
                                                $getSpouseValue = function($spouseField, $memberField) use ($spouse, $member) {
                                                    if ($spouse) {
                                                        return $spouse->$spouseField ?? ($member->$memberField ?? 'N/A');
                                                    }
                                                    return $member->$memberField ?? 'N/A';
                                                };
                                            @endphp
                                            
                                            <div class="col-md-6">
                                                <label class="text-muted small">Full Name</label>
                                                <p class="fw-bold">
                                                    @if($spouse)
                                                        {{ $spouse->full_name ?? 'N/A' }}
                                                    @else
                                                        {{ !empty($member->spouse_full_name) ? $member->spouse_full_name : 'N/A' }}
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="text-muted small">Member ID</label>
                                                <p class="fw-bold">
                                                    @if($spouse)
                                                        {{ $spouse->member_id ?? 'N/A' }}
                                                        @if($spouse)
                                                            <a href="{{ route('members.show', $spouse->id) }}" class="btn btn-sm btn-outline-primary ms-2" title="View Spouse Details">
                                                                <i class="fas fa-eye"></i> View
                                                            </a>
                                                        @endif
                                                    @else
                                                        N/A (Not a church member)
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="text-muted small">Gender</label>
                                                <p class="fw-bold text-capitalize">
                                                    @if($spouse)
                                                        {{ $spouse->gender ?? 'N/A' }}
                                                    @else
                                                        {{ $member->spouse_gender ?? 'N/A' }}
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="text-muted small">Date of Birth</label>
                                                <p class="fw-bold">
                                                    @if($spouse && $spouse->date_of_birth)
                                                        {{ \Carbon\Carbon::parse($spouse->date_of_birth)->format('M d, Y') }}
                                                    @elseif($member->spouse_date_of_birth)
                                                        {{ \Carbon\Carbon::parse($member->spouse_date_of_birth)->format('M d, Y') }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="text-muted small">Phone Number</label>
                                                <p class="fw-bold">
                                                    @if($spouse)
                                                        {{ !empty($spouse->phone_number) ? $spouse->phone_number : 'N/A' }}
                                                    @else
                                                        {{ !empty($member->spouse_phone_number) ? $member->spouse_phone_number : 'N/A' }}
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="text-muted small">Email</label>
                                                <p class="fw-bold">
                                                    @if($spouse)
                                                        {{ !empty($spouse->email) ? $spouse->email : 'N/A' }}
                                                    @else
                                                        {{ !empty($member->spouse_email) ? $member->spouse_email : 'N/A' }}
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="text-muted small">Education Level</label>
                                                <p class="fw-bold text-capitalize">
                                                    @if($spouse)
                                                        {{ !empty($spouse->education_level) ? str_replace('_', ' ', $spouse->education_level) : 'N/A' }}
                                                    @else
                                                        {{ !empty($member->spouse_education_level) ? str_replace('_', ' ', $member->spouse_education_level) : 'N/A' }}
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="text-muted small">Profession</label>
                                                <p class="fw-bold">
                                                    @if($spouse)
                                                        {{ !empty($spouse->profession) ? $spouse->profession : 'N/A' }}
                                                    @else
                                                        {{ !empty($member->spouse_profession) ? $member->spouse_profession : 'N/A' }}
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="text-muted small">NIDA Number</label>
                                                <p class="fw-bold">
                                                    @if($spouse)
                                                        {{ !empty($spouse->nida_number) ? $spouse->nida_number : 'N/A' }}
                                                    @else
                                                        {{ !empty($member->spouse_nida_number) ? $member->spouse_nida_number : 'N/A' }}
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="text-muted small">Tribe</label>
                                                <p class="fw-bold">
                                                    @if($spouse)
                                                        @if(!empty($spouse->tribe))
                                                            {{ $spouse->tribe }}{{ !empty($spouse->other_tribe) ? ' (' . $spouse->other_tribe . ')' : '' }}
                                                        @else
                                                            N/A
                                                        @endif
                                                    @else
                                                        @if(!empty($member->spouse_tribe))
                                                            {{ $member->spouse_tribe }}{{ !empty($member->spouse_other_tribe) ? ' (' . $member->spouse_other_tribe . ')' : '' }}
                                                        @else
                                                            N/A
                                                        @endif
                                                    @endif
                                                </p>
                                            </div>
                                            @if($spouse)
                                                <div class="col-md-6">
                                                    <label class="text-muted small">Branch/Campus</label>
                                                    <p class="fw-bold">{{ $spouse->campus->name ?? 'N/A' }}</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="text-muted small">Membership Type</label>
                                                    <p class="fw-bold text-capitalize">{{ $spouse->membership_type ?? 'N/A' }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                                @if(isset($member->member_type) && $member->member_type === 'independent' && (!isset($member->marital_status) || $member->marital_status !== 'married'))
                                    <div class="col-12">
                                        <h6 class="mt-3 mb-2"><i class="fas fa-user-shield me-2"></i>Guardian / Responsible Person Information</h6>
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label class="text-muted small">Name</label>
                                                <p class="fw-bold">{{ !empty($member->guardian_name) ? $member->guardian_name : 'N/A' }}</p>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="text-muted small">Phone Number</label>
                                                <p class="fw-bold">{{ !empty($member->guardian_phone) ? $member->guardian_phone : 'N/A' }}</p>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="text-muted small">Relationship</label>
                                                <p class="fw-bold">{{ !empty($member->guardian_relationship) ? $member->guardian_relationship : 'N/A' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Children Information -->
                @php
                    // Merge children from multiple sources to show all children on both parents' pages
                    $displayChildren = collect();
                    
                    // Start with children passed from controller (which already includes merged children)
                    if (isset($children) && $children->isNotEmpty()) {
                        $displayChildren = $displayChildren->merge($children);
                    }
                    
                    // 1. Get children directly linked to the current member
                    $member->load('children');
                    if ($member->children->isNotEmpty()) {
                        $displayChildren = $displayChildren->merge($member->children);
                    }
                    
                    // 2. If current member is a spouse, get children from the main member
                    if ($member->mainMember) {
                        $member->mainMember->load('children');
                        if ($member->mainMember->children->isNotEmpty()) {
                            $displayChildren = $displayChildren->merge($member->mainMember->children);
                        }
                    }
                    
                    // 3. If current member has a spouse who is a church member, get children from spouse
                    if ($member->spouseMember) {
                        $member->spouseMember->load('children');
                        if ($member->spouseMember->children->isNotEmpty()) {
                            $displayChildren = $displayChildren->merge($member->spouseMember->children);
                        }
                    }
                    
                    // 4. If spouse has a mainMember, also check spouse's mainMember's children
                    if ($member->spouseMember && $member->spouseMember->mainMember) {
                        $spouseMainMember = $member->spouseMember->mainMember;
                        $spouseMainMember->load('children');
                        if ($spouseMainMember->children->isNotEmpty()) {
                            $displayChildren = $displayChildren->merge($spouseMainMember->children);
                        }
                    }
                    
                    // Remove duplicates by ID (in case same child appears in multiple collections)
                    $displayChildren = $displayChildren->unique('id')->values();
                @endphp
                
                @if($displayChildren && $displayChildren->count() > 0)
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light py-3">
                            <h5 class="mb-0 text-dark">
                                <i class="fas fa-child me-2"></i>Children Information
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Age</th>
                                            <th>Gender</th>
                                            <th>Date of Birth</th>
                                            <th>Parent/Guardian</th>
                                            <th>Age Group</th>
                                            <th>Baptism Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($displayChildren as $child)
                                        @php
                                            $age = $child->getAge();
                                            $ageGroup = $child->getAgeGroup();
                                            $hasMemberParent = $child->hasMemberParent();
                                            $parentName = $child->getParentName();
                                            $parentRelationship = $child->getParentRelationship();
                                            
                                            // Age group labels and colors
                                            $ageGroupLabels = [
                                                'infant' => 'Infant (< 3)',
                                                'sunday_school' => 'Sunday School (3-12)',
                                                'teenager' => 'Teenager (13-17)'
                                            ];
                                            
                                            $ageGroupColors = [
                                                'infant' => 'secondary',
                                                'sunday_school' => 'success',
                                                'teenager' => 'warning'
                                            ];
                                            
                                            $ageGroupLabel = $ageGroup ? ($ageGroupLabels[$ageGroup] ?? 'N/A') : 'Adult (18+)';
                                            $ageGroupColor = $ageGroup ? ($ageGroupColors[$ageGroup] ?? 'secondary') : 'dark';
                                        @endphp
                                        <tr>
                                            <td class="text-muted">{{ $loop->iteration }}</td>
                                            <td><strong>{{ $child->full_name }}</strong></td>
                                            <td><strong>{{ $age }} years</strong></td>
                                            <td>
                                                <span class="badge bg-{{ $child->gender === 'male' ? 'primary' : 'info' }}">
                                                    {{ ucfirst($child->gender) }}
                                                </span>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($child->date_of_birth)->format('M d, Y') }}</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    @if($hasMemberParent)
                                                        <i class="fas fa-user text-primary"></i>
                                                    @else
                                                        <i class="fas fa-users text-warning"></i>
                                                    @endif
                                                    <span class="fw-bold">{{ $parentName ?? 'N/A' }}</span>
                                                </div>
                                                @if($hasMemberParent)
                                                    <span class="badge bg-success mt-1">Member</span>
                                                @else
                                                    <span class="badge bg-warning text-dark mt-1">Non-Member</span>
                                                    @if($parentRelationship)
                                                        <div class="mt-1">
                                                            <i class="fas fa-link text-muted me-1"></i>
                                                            <small class="text-muted">{{ $parentRelationship }}</small>
                                                        </div>
                                                    @endif
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $ageGroupColor }}">
                                                    {{ $ageGroupLabel }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($child->baptism_status)
                                                    @if($child->baptism_status === 'baptized')
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-tint me-1"></i>Baptized
                                                        </span>
                                                        @if($child->baptism_date)
                                                            <br><small class="text-muted">{{ \Carbon\Carbon::parse($child->baptism_date)->format('M d, Y') }}</small>
                                                        @endif
                                                    @else
                                                        <span class="badge bg-secondary">Not Baptized</span>
                                                    @endif
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                @elseif(isset($member->marital_status) && $member->marital_status === 'married')
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-primary text-white py-3">
                            <h5 class="mb-0"><i class="fas fa-child me-2"></i>Children Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                No children have been registered for this family yet.
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    @media (min-width: 992px) {
        body.sb-nav-fixed:not(.sb-sidenav-toggled) #layoutSidenav_content {
            padding-left: 225px !important;
        }
        body.sb-nav-fixed.sb-sidenav-toggled #layoutSidenav_content {
            padding-left: 0 !important;
        }
    }
    
    @media (max-width: 991px) {
        #layoutSidenav_content {
            padding-left: 0 !important;
            margin-left: 0 !important;
        }
    }
    
    .card-header.bg-primary {
        background-color: #0d6efd !important;
    }
</style>
@endpush

@section('scripts')
<script>
function confirmDelete(memberId) {
    Swal.fire({
        title: 'Delete Member',
        html: `
            <p>Are you sure you want to delete this member?</p>
            <p class="text-danger small mt-2"><strong>Warning:</strong> This action cannot be undone!</p>
            <textarea id="delete-reason" class="form-control mt-3" placeholder="Reason for deletion (optional)" rows="3"></textarea>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'Cancel',
        preConfirm: () => {
            return {
                reason: document.getElementById('delete-reason').value || 'Member deleted by user'
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Get fresh CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                             document.querySelector('input[name="_token"]')?.value || 
                             '{{ csrf_token() }}';
            
            const formData = new FormData();
            formData.append('reason', result.value.reason);
            formData.append('_token', csrfToken);
            
            fetch(`{{ url('/members') }}/${memberId}`, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: formData
            })
            .then(async response => {
                // Check for 419 CSRF token mismatch - handle it immediately before global handler
                if (response.status === 419) {
                    // Immediately reload page to get fresh CSRF token
                    // This prevents the global handler from redirecting to login
                    Swal.fire({
                        icon: 'warning',
                        title: 'Session Expired',
                        html: '<p>Your session has expired.</p><p class="small mt-2">Refreshing page...</p>',
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        timer: 1500,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Reload immediately after showing message
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                    
                    // Return to stop further processing
                    return Promise.reject(new Error('Session expired - page reloading'));
                }
                
                // Check if response is ok
                if (!response.ok) {
                    const text = await response.text();
                    let errorData;
                    try {
                        errorData = JSON.parse(text);
                    } catch (e) {
                        errorData = { message: text || 'Server error' };
                    }
                    const error = new Error(errorData.message || 'Server error');
                    error.response = response;
                    error.data = errorData;
                    throw error;
                }
                // Try to parse as JSON
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                } else {
                    const text = await response.text();
                    return { success: true, message: text || 'Member deleted successfully' };
                }
            })
            .then(data => {
                // Check if session expired
                if (data.error === 'Page Expired' || data.message?.includes('session has expired') || data.message?.includes('Page Expired') || data.redirect) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Session Expired',
                        text: 'Your session has expired. Please refresh the page and try again.',
                        confirmButtonText: 'Refresh Page'
                    }).then(() => {
                        window.location.reload();
                    });
                    return;
                }
                
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted',
                        text: data.message || 'Member has been deleted successfully',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = '{{ route("members.index") }}';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Failed to delete member'
                    });
                }
            })
            .catch(error => {
                console.error('Delete error:', error);
                console.error('Error details:', {
                    message: error.message,
                    stack: error.stack,
                    memberId: memberId,
                    response: error.response,
                    data: error.data
                });
                
                // Check if session expired
                if (error.data && (error.data.error === 'Page Expired' || error.data.message?.includes('session has expired') || error.data.message?.includes('Page Expired') || error.data.redirect)) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Session Expired',
                        text: 'Your session has expired. Please refresh the page and try again.',
                        confirmButtonText: 'Refresh Page'
                    }).then(() => {
                        window.location.reload();
                    });
                    return;
                }
                
                // Try to get more details from the error
                let errorMessage = 'An error occurred while deleting the member';
                if (error.data && error.data.message) {
                    errorMessage = error.data.message;
                } else if (error.message) {
                    errorMessage = error.message;
                    // If it's a JSON error response, try to parse it
                    try {
                        const jsonError = JSON.parse(error.message);
                        if (jsonError.message) {
                            errorMessage = jsonError.message;
                        }
                    } catch (e) {
                        // Not JSON, use the message as is
                    }
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Delete Error',
                    html: `<p><strong>${errorMessage}</strong></p><p class="small text-muted mt-2">Member ID: ${memberId}</p><p class="small text-muted">Check the browser console (F12) for more details.</p>`
                });
            });
        }
    });
}

// Show success message if member was just added
document.addEventListener('DOMContentLoaded', function() {
    @if(session('success') && session('member_id'))
        Swal.fire({
            icon: 'success',
            title: 'Member Registered Successfully!',
            html: `
                <div class="text-start">
                    <p><strong>{{ session('name') }}</strong> has been registered successfully!</p>
                    <p class="mb-2"><strong>Member ID:</strong> {{ session('user_id') }}</p>
                    <p class="mb-2"><strong>Membership Type:</strong> {{ ucfirst(session('membership_type')) }}</p>
                    <hr>
                    <p class="small text-muted mb-0">You are now viewing the member's details page.</p>
                </div>
            `,
            confirmButtonText: 'OK',
            confirmButtonColor: '#5b2a86',
            timer: 5000,
            timerProgressBar: true
        });
    @elseif(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '{{ session('success') }}',
            timer: 3000,
            showConfirmButton: false
        });
    @endif
});

</script>
@endsection
@endsection

