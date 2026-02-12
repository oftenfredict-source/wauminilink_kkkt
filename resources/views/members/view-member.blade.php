@extends('layouts.index')

@section('content')
    <div class="container-fluid px-2 px-md-5 py-4">
        <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
            <div
                class="card-header bg-primary d-flex flex-column flex-md-row justify-content-between align-items-center py-4 px-4 border-0">
                <span class="fs-5 fw-bold text-white d-flex align-items-center">
                    <i class="fas fa-user me-2"></i>
                    <span>{{ __('common.member_details') }} - {{ $member->full_name ?? __('common.no_data') }}</span>
                </span>

                <div class="mt-3 mt-md-0">
                    <a href="{{ route('members.index') }}" class="btn btn-outline-light btn-sm shadow-sm me-2">
                        <i class="fas fa-list me-1"></i> {{ __('common.all_members') }}
                    </a>
                    @php
                        $memberId = is_object($member) && property_exists($member, 'id') ? $member->id : (is_array($member) && isset($member['id']) ? $member['id'] : (isset($member->id) ? $member->id : null));
                    @endphp
                    @if($memberId)
                        @if(isset($member->membership_type) && $member->membership_type === 'temporary')
                            <button onclick="confirmExtension({{ $memberId }})"
                                class="btn btn-info btn-sm shadow-sm me-2 text-white">
                                <i class="fas fa-clock me-1"></i> {{ __('common.extend') }}
                            </button>
                        @endif
                        <a href="{{ route('members.edit', $memberId) }}"
                            class="btn btn-light btn-sm shadow-sm me-2 text-primary fw-bold">
                            <i class="fas fa-edit me-1"></i> {{ __('common.edit') }}
                        </a>
                        <button onclick="confirmDelete({{ $memberId }})" class="btn btn-danger btn-sm shadow-sm">
                            <i class="fas fa-trash me-1"></i> {{ __('common.delete') }}
                        </button>
                    @endif
                </div>
            </div>
            <div class="card-body bg-light px-4 py-4">

                @if(isset($member->membership_type) && $member->membership_type === 'temporary')
                    <div class="alert alert-info d-flex align-items-center mb-4">
                        <i class="fas fa-info-circle fa-2x me-3"></i>
                        <div>
                            <h5 class="alert-heading mb-1">{{ __('common.temporary_membership_status') }}</h5>
                            <p class="mb-0">
                                {{ __('common.member_on_temporary_membership') }}.
                                {{ __('common.duration') }}:
                                <strong>{{ $member->membership_duration_months ?? __('common.no_data') }}
                                    {{ __('common.months') }}</strong>.
                                {{ __('common.end_date') }}:
                                <strong>{{ $member->membership_end_date ? \Carbon\Carbon::parse($member->membership_end_date)->format('M d, Y') : __('common.no_data') }}</strong>.
                                {{ __('common.status') }}: <span
                                    class="badge bg-{{ ($member->membership_status ?? 'active') === 'active' ? 'success' : 'warning' }}">{{ $member->membership_status ? __('common.' . $member->membership_status) : __('common.active') }}</span>
                            </p>
                        </div>
                    </div>
                @endif

                <div class="row g-4">
                    <!-- Personal Information -->
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-primary text-white py-3">
                                <h5 class="mb-0"><i class="fas fa-user me-2"></i>{{ __('common.personal_information') }}
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-3 text-center mb-3">
                                        @if(isset($member->profile_picture) && $member->profile_picture)
                                            <img src="{{ Storage::url($member->profile_picture) }}" alt="Profile"
                                                class="img-fluid rounded-circle"
                                                style="width: 150px; height: 150px; object-fit: cover;">
                                        @else
                                            <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center mx-auto"
                                                style="width: 150px; height: 150px;">
                                                <i class="fas fa-user fa-4x text-white"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-9">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="text-muted small">{{ __('common.member_id') }}</label>
                                                <p class="fw-bold">{{ $member->member_id ?? __('common.no_data') }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="text-muted small">{{ __('common.envelope_number') }}</label>
                                                <p class="fw-bold">
                                                    @if($member->envelope_number)
                                                        <span
                                                            class="badge bg-success shadow-sm px-3">{{ $member->envelope_number }}</span>
                                                    @else
                                                        <span class="text-muted">{{ __('common.not_assigned') }}</span>
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="text-muted small">{{ __('common.full_name') }}</label>
                                                <p class="fw-bold">{{ $member->full_name ?? __('common.no_data') }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="text-muted small">{{ __('common.gender') }}</label>
                                                <p class="fw-bold text-capitalize">
                                                    {{ $member->gender ? __('common.' . $member->gender) : __('common.no_data') }}
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="text-muted small">{{ __('common.date_of_birth') }}</label>
                                                <p class="fw-bold">
                                                    {{ $member->date_of_birth ? \Carbon\Carbon::parse($member->date_of_birth)->format('M d, Y') : __('common.no_data') }}
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="text-muted small">{{ __('common.membership_type') }}</label>
                                                <p class="fw-bold text-capitalize">
                                                    {{ $member->membership_type ? __('common.' . $member->membership_type) : __('common.no_data') }}
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="text-muted small">{{ __('common.member_type') }}</label>
                                                <p class="fw-bold text-capitalize">
                                                    {{ $member->member_type ? __('common.' . $member->member_type) : __('common.no_data') }}
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="text-muted small">{{ __('common.branch_campus') }}</label>
                                                <p class="fw-bold">{{ $member->campus->name ?? __('common.no_data') }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="text-muted small">{{ __('common.community') }}</label>
                                                <p class="fw-bold">{{ $member->community->name ?? __('common.no_data') }}
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="text-muted small">{{ __('common.education_level') }}</label>
                                                <p class="fw-bold text-capitalize">
                                                    @if($member->education_level)
                                                        {{ __('common.' . $member->education_level) }}
                                                    @else
                                                        {{ __('common.no_data') }}
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="text-muted small">{{ __('common.profession') }}</label>
                                                <p class="fw-bold">{{ $member->profession ?? __('common.no_data') }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="text-muted small">{{ __('common.nida_number') }}</label>
                                                <p class="fw-bold">{{ $member->nida_number ?? __('common.no_data') }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="text-muted small">{{ __('common.baptism_status') }}</label>
                                                <p class="fw-bold">
                                                    @if(isset($member->baptism_status) && $member->baptism_status === 'baptized')
                                                        {{ __('common.baptized') }}
                                                    @elseif(isset($member->baptism_status) && $member->baptism_status === 'not_baptized')
                                                        {{ __('common.not_baptized') }}
                                                    @else
                                                        {{ __('common.no_data') }}
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Departments -->
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-primary text-white py-3">
                                <h5 class="mb-0"><i
                                        class="fas fa-layer-group me-2"></i>{{ __('common.church_departments') }}</h5>
                            </div>
                            <div class="card-body">
                                @if(isset($departmentStatus) && count($departmentStatus) > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th style="width: 30%">{{ __('common.department') }}</th>
                                                    <th style="width: 20%">{{ __('common.status') }}</th>
                                                    <th style="width: 20%">{{ __('common.eligibility') }}</th>
                                                    <th style="width: 30%">{{ __('common.action') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($departmentStatus as $status)
                                                    <tr>
                                                        <td>
                                                            <div class="fw-bold">{{ $status['department']->name }}</div>
                                                            <div class="small text-muted">
                                                                {{ \Illuminate\Support\Str::limit($status['department']->description, 50) }}
                                                            </div>
                                                        </td>
                                                        <td>
                                                            @if($status['assigned'])
                                                                <span class="badge bg-success">{{ __('common.assigned') }}</span>
                                                            @else
                                                                <span
                                                                    class="badge bg-light text-muted border">{{ __('common.not_member') }}</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($status['eligible'])
                                                                <span
                                                                    class="badge bg-success bg-opacity-10 text-success">{{ __('common.eligible') }}</span>
                                                            @else
                                                                <span class="badge bg-danger bg-opacity-10 text-danger"
                                                                    title="{{ $status['reason'] }}">
                                                                    {{ __('common.not_eligible') }}
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if(!$status['assigned'])
                                                                @if($status['eligible'])
                                                                    <form
                                                                        action="{{ route('departments.assign', $status['department']->id) }}"
                                                                        method="POST" class="d-inline">
                                                                        @csrf
                                                                        <input type="hidden" name="member_id" value="{{ $member->id }}">
                                                                        <button type="submit"
                                                                            class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                                                            <i class="fas fa-plus me-1"></i> {{ __('common.join') }}
                                                                        </button>
                                                                    </form>
                                                                @else
                                                                    <button class="btn btn-sm btn-outline-secondary rounded-pill px-3"
                                                                        disabled title="{{ $status['reason'] }}">
                                                                        <i class="fas fa-ban me-1"></i> {{ __('common.join') }}
                                                                    </button>
                                                                @endif
                                                            @else
                                                                <form
                                                                    action="{{ route('departments.remove-member', [$status['department']->id, $member->id]) }}"
                                                                    method="POST"
                                                                    onsubmit="return confirm('{{ __('common.are_you_sure') }}?');"
                                                                    class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                        class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                                                        <i class="fas fa-sign-out-alt me-1"></i>
                                                                        {{ __('common.leave') }}
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-3 text-muted">
                                        <i class="fas fa-layer-group fa-2x mb-2 opacity-50"></i>
                                        <p class="mb-0">{{ __('common.no_departments_found') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Contact & Location Information -->
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-primary text-white py-3">
                                <h5 class="mb-0"><i
                                        class="fas fa-phone me-2"></i>{{ __('common.contact_location_information') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="text-muted small">{{ __('common.phone_number') }}</label>
                                        <p class="fw-bold">{{ $member->phone_number ?? __('common.no_data') }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small">{{ __('common.email') }}</label>
                                        <p class="fw-bold">{{ $member->email ?? __('common.no_data') }}</p>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="text-muted small">{{ __('common.region') }}</label>
                                        <p class="fw-bold">{{ $member->region ?? __('common.no_data') }}</p>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="text-muted small">{{ __('common.district') }}</label>
                                        <p class="fw-bold">{{ $member->district ?? __('common.no_data') }}</p>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="text-muted small">{{ __('common.ward') }}</label>
                                        <p class="fw-bold">{{ $member->ward ?? __('common.no_data') }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small">{{ __('common.street') }}</label>
                                        <p class="fw-bold">{{ $member->street ?? __('common.no_data') }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small">{{ __('common.p_o_box') }}</label>
                                        <p class="fw-bold">{{ $member->address ?? __('common.no_data') }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small">{{ __('common.tribe') }}</label>
                                        <p class="fw-bold">
                                            {{ $member->tribe ?? __('common.no_data') }}{{ $member->other_tribe ? ' (' . $member->other_tribe . ')' : '' }}
                                        </p>
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
                                    <h5 class="mb-0"><i class="fas fa-home me-2"></i>{{ __('common.current_residence') }}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="text-muted small">{{ __('common.region') }}</label>
                                            <p class="fw-bold">{{ $member->residence_region ?? __('common.no_data') }}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="text-muted small">{{ __('common.district') }}</label>
                                            <p class="fw-bold">{{ $member->residence_district ?? __('common.no_data') }}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="text-muted small">{{ __('common.ward') }}</label>
                                            <p class="fw-bold">{{ $member->residence_ward ?? __('common.no_data') }}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="text-muted small">{{ __('common.street') }}</label>
                                            <p class="fw-bold">{{ $member->residence_street ?? __('common.no_data') }}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="text-muted small">{{ __('common.road') }}</label>
                                            <p class="fw-bold">{{ $member->residence_road ?? __('common.no_data') }}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="text-muted small">{{ __('common.house_number') }}</label>
                                            <p class="fw-bold">{{ $member->residence_house_number ?? __('common.no_data') }}</p>
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
                                <h5 class="mb-0"><i class="fas fa-users me-2"></i>{{ __('common.family_information') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="text-muted small">{{ __('common.marital_status') }}</label>
                                        <p class="fw-bold text-capitalize">
                                            {{ $member->marital_status ? __('common.' . $member->marital_status) : __('common.no_data') }}
                                        </p>
                                    </div>
                                    @if(isset($member->marital_status) && $member->marital_status === 'married')
                                        <div class="col-md-6">
                                            <label class="text-muted small">{{ __('common.wedding_date') }}</label>
                                            <p class="fw-bold">
                                                @if(isset($member->wedding_date) && $member->wedding_date)
                                                    {{ \Carbon\Carbon::parse($member->wedding_date)->format('M d, Y') }}
                                                @else
                                                    {{ __('common.no_data') }}
                                                @endif
                                            </p>
                                        </div>
                                        <div class="col-12">
                                            <h6 class="mt-3 mb-2">
                                                <i class="fas fa-heart me-2"></i>{{ __('common.spouse_information') }}
                                                @if(isset($member->spouse_member_id) && $member->spouse_member_id && $member->spouseMember)
                                                    <span class="badge bg-success ms-2">{{ __('common.church_member') }}</span>
                                                @elseif(empty($member->spouse_full_name) && empty($member->spouse_phone_number))
                                                    <span
                                                        class="badge bg-warning ms-2 text-dark">{{ __('common.no_spouse_information') }}</span>
                                                @else
                                                    <span class="badge bg-info ms-2">{{ __('common.not_a_church_member') }}</span>
                                                @endif
                                            </h6>
                                            @if(empty($member->spouse_full_name) && empty($member->spouse_phone_number) && !isset($member->spouse_member_id))
                                                <div class="alert alert-info mb-3">
                                                    <i class="fas fa-info-circle me-2"></i>
                                                    {{ __('common.no_spouse_added') }}
                                                    <a href="{{ route('members.edit', $member->id) }}"
                                                        class="alert-link">{{ __('common.click_to_add_spouse') }}</a>.
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
                                                    $getSpouseValue = function ($spouseField, $memberField) use ($spouse, $member) {
                                                        if ($spouse) {
                                                            return $spouse->$spouseField ?? ($member->$memberField ?? 'N/A');
                                                        }
                                                        return $member->$memberField ?? 'N/A';
                                                    };
                                                @endphp

                                                <div class="col-md-6">
                                                    <label class="text-muted small">{{ __('common.full_name') }}</label>
                                                    <p class="fw-bold">
                                                        @if($spouse)
                                                            {{ $spouse->full_name ?? __('common.no_data') }}
                                                        @else
                                                            {{ !empty($member->spouse_full_name) ? $member->spouse_full_name : __('common.no_data') }}
                                                        @endif
                                                    </p>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="text-muted small">{{ __('common.member_id') }}</label>
                                                    <p class="fw-bold">
                                                        @if($spouse)
                                                            {{ $spouse->member_id ?? __('common.no_data') }}
                                                            @if($spouse)
                                                                <a href="{{ route('members.show', $spouse->id) }}"
                                                                    class="btn btn-sm btn-outline-primary ms-2"
                                                                    title="{{ __('common.view_spouse_details') }}">
                                                                    <i class="fas fa-eye"></i> {{ __('common.view') }}
                                                                </a>
                                                            @endif
                                                        @else
                                                            {{ __('common.not_a_church_member') }}
                                                        @endif
                                                    </p>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="text-muted small">{{ __('common.gender') }}</label>
                                                    <p class="fw-bold text-capitalize">
                                                        @if($spouse)
                                                            {{ $spouse->gender ? __('common.' . $spouse->gender) : __('common.no_data') }}
                                                        @else
                                                            {{ $member->spouse_gender ? __('common.' . $member->spouse_gender) : __('common.no_data') }}
                                                        @endif
                                                    </p>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="text-muted small">{{ __('common.date_of_birth') }}</label>
                                                    <p class="fw-bold">
                                                        @if($spouse && $spouse->date_of_birth)
                                                            {{ \Carbon\Carbon::parse($spouse->date_of_birth)->format('M d, Y') }}
                                                        @elseif($member->spouse_date_of_birth)
                                                            {{ \Carbon\Carbon::parse($member->spouse_date_of_birth)->format('M d, Y') }}
                                                        @else
                                                            {{ __('common.no_data') }}
                                                        @endif
                                                    </p>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="text-muted small">{{ __('common.phone_number') }}</label>
                                                    <p class="fw-bold">
                                                        @if($spouse)
                                                            {{ !empty($spouse->phone_number) ? $spouse->phone_number : __('common.no_data') }}
                                                        @else
                                                            {{ !empty($member->spouse_phone_number) ? $member->spouse_phone_number : __('common.no_data') }}
                                                        @endif
                                                    </p>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="text-muted small">{{ __('common.email') }}</label>
                                                    <p class="fw-bold">
                                                        @if($spouse)
                                                            {{ !empty($spouse->email) ? $spouse->email : __('common.no_data') }}
                                                        @else
                                                            {{ !empty($member->spouse_email) ? $member->spouse_email : __('common.no_data') }}
                                                        @endif
                                                    </p>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="text-muted small">{{ __('common.education_level') }}</label>
                                                    <p class="fw-bold text-capitalize">
                                                        @if($spouse)
                                                            {{ !empty($spouse->education_level) ? __('common.' . $spouse->education_level) : __('common.no_data') }}
                                                        @else
                                                            {{ !empty($member->spouse_education_level) ? __('common.' . $member->spouse_education_level) : __('common.no_data') }}
                                                        @endif
                                                    </p>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="text-muted small">{{ __('common.profession') }}</label>
                                                    <p class="fw-bold">
                                                        @if($spouse)
                                                            {{ !empty($spouse->profession) ? $spouse->profession : __('common.no_data') }}
                                                        @else
                                                            {{ !empty($member->spouse_profession) ? $member->spouse_profession : __('common.no_data') }}
                                                        @endif
                                                    </p>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="text-muted small">{{ __('common.nida_number') }}</label>
                                                    <p class="fw-bold">
                                                        @if($spouse)
                                                            {{ !empty($spouse->nida_number) ? $spouse->nida_number : __('common.no_data') }}
                                                        @else
                                                            {{ !empty($member->spouse_nida_number) ? $member->spouse_nida_number : __('common.no_data') }}
                                                        @endif
                                                    </p>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="text-muted small">{{ __('common.tribe') }}</label>
                                                    <p class="fw-bold">
                                                        @if($spouse)
                                                            @if(!empty($spouse->tribe))
                                                                {{ $spouse->tribe }}{{ !empty($spouse->other_tribe) ? ' (' . $spouse->other_tribe . ')' : '' }}
                                                            @else
                                                                {{ __('common.no_data') }}
                                                            @endif
                                                        @else
                                                            @if(!empty($member->spouse_tribe))
                                                                {{ $member->spouse_tribe }}{{ !empty($member->spouse_other_tribe) ? ' (' . $member->spouse_other_tribe . ')' : '' }}
                                                            @else
                                                                {{ __('common.no_data') }}
                                                            @endif
                                                        @endif
                                                    </p>
                                                </div>
                                                @if($spouse)
                                                    <div class="col-md-6">
                                                        <label class="text-muted small">{{ __('common.branch_campus') }}</label>
                                                        <p class="fw-bold">{{ $spouse->campus->name ?? __('common.no_data') }}</p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="text-muted small">{{ __('common.membership_type') }}</label>
                                                        <p class="fw-bold text-capitalize">
                                                            {{ $spouse->membership_type ? __('common.' . $spouse->membership_type) : __('common.no_data') }}
                                                        </p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                    @if(isset($member->member_type) && $member->member_type === 'independent' && (!isset($member->marital_status) || $member->marital_status !== 'married'))
                                        <div class="col-12">
                                            <h6 class="mt-3 mb-2"><i
                                                    class="fas fa-user-shield me-2"></i>{{ __('common.guardian_responsible_info') }}
                                            </h6>
                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <label class="text-muted small">{{ __('common.name') }}</label>
                                                    <p class="fw-bold">
                                                        {{ !empty($member->guardian_name) ? $member->guardian_name : __('common.no_data') }}
                                                    </p>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="text-muted small">{{ __('common.phone_number') }}</label>
                                                    <p class="fw-bold">
                                                        {{ !empty($member->guardian_phone) ? $member->guardian_phone : __('common.no_data') }}
                                                    </p>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="text-muted small">{{ __('common.relationship') }}</label>
                                                    <p class="fw-bold">
                                                        {{ !empty($member->guardian_relationship) ? $member->guardian_relationship : __('common.no_data') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Social Welfare Status -->
                    @if(isset($member->orphan_status) || isset($member->disability_status) || isset($member->vulnerable_status) || isset($member->spouse_orphan_status) || isset($member->spouse_disability_status))
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-danger text-white py-3">
                                    <h5 class="mb-0"><i
                                            class="fas fa-hand-holding-heart me-2"></i>{{ __('common.social_welfare_status') }}
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <!-- Member Welfare Information -->
                                        <div class="col-12">
                                            <h6 class="text-primary mb-3"><i
                                                    class="fas fa-user me-2"></i>{{ __('common.member_welfare_information') }}
                                            </h6>
                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <label class="text-muted small">{{ __('common.orphan_status') }}</label>
                                                    <p class="fw-bold">
                                                        @php
                                                            $orphanLabels = [
                                                                'not_orphan' => __('common.not_orphan'),
                                                                'father_deceased' => __('common.father_deceased'),
                                                                'mother_deceased' => __('common.mother_deceased'),
                                                                'both_deceased' => __('common.both_deceased')
                                                            ];
                                                        @endphp
                                                        {{ $orphanLabels[$member->orphan_status ?? 'not_orphan'] ?? __('common.no_data') }}
                                                    </p>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="text-muted small">{{ __('common.disability_status') }}</label>
                                                    <p class="fw-bold">
                                                        @if(isset($member->disability_status) && $member->disability_status)
                                                            <span
                                                                class="badge bg-warning text-dark">{{ __('common.has_disability') }}</span>
                                                            @if(!empty($member->disability_type))
                                                                <br><small class="text-muted">{{ __('common.type') }}:
                                                                    {{ $member->disability_type }}</small>
                                                            @endif
                                                        @else
                                                            <span class="badge bg-success">{{ __('common.no_disability') }}</span>
                                                        @endif
                                                    </p>
                                                </div>
                                                <div class="col-md-4">
                                                    <label
                                                        class="text-muted small">{{ __('common.vulnerability_status') }}</label>
                                                    <p class="fw-bold">
                                                        @if(isset($member->vulnerable_status) && $member->vulnerable_status)
                                                            <span class="badge bg-danger">{{ __('common.vulnerable') }}</span>
                                                            @if(!empty($member->vulnerable_type))
                                                                <br><small class="text-muted">{{ __('common.type') }}:
                                                                    {{ $member->vulnerable_type }}</small>
                                                            @endif
                                                        @else
                                                            <span class="badge bg-success">{{ __('common.not_vulnerable') }}</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Spouse Welfare Information -->
                                        @if(isset($member->marital_status) && $member->marital_status === 'married' && (!empty($member->spouse_full_name) || isset($member->spouse_member_id)))
                                            <div class="col-12">
                                                <hr class="my-3">
                                                <h6 class="text-primary mb-3"><i
                                                        class="fas fa-heart me-2"></i>{{ __('common.spouse_welfare_information') }}
                                                </h6>
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label
                                                            class="text-muted small">{{ __('common.spouse_orphan_status') }}</label>
                                                        <p class="fw-bold">
                                                            @php
                                                                // Determine spouse orphan status
                                                                // Logic: Use linked spouse data if set to a specific status. 
                                                                // If linked spouse is 'not_orphan' (default) but manual field has a specific status, use manual field.
                                                                // Otherwise fallback to manual field or default.
                                                                $spouseOrphanStatus = 'not_orphan';
                                                                if ($spouse) {
                                                                    if (isset($spouse->orphan_status) && $spouse->orphan_status !== 'not_orphan') {
                                                                        $spouseOrphanStatus = $spouse->orphan_status;
                                                                    } elseif (isset($member->spouse_orphan_status) && $member->spouse_orphan_status !== 'not_orphan') {
                                                                        $spouseOrphanStatus = $member->spouse_orphan_status;
                                                                    }
                                                                } else {
                                                                    $spouseOrphanStatus = $member->spouse_orphan_status ?? 'not_orphan';
                                                                }
                                                            @endphp
                                                            {{ $orphanLabels[$spouseOrphanStatus] ?? __('common.no_data') }}
                                                        </p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label
                                                            class="text-muted small">{{ __('common.spouse_disability_status') }}</label>
                                                        <p class="fw-bold">
                                                            @php
                                                                // Determine spouse disability status and type
                                                                $spouseDisabilityStatus = false;
                                                                $spouseDisabilityType = null;

                                                                if ($spouse) {
                                                                    if ($spouse->disability_status) {
                                                                        $spouseDisabilityStatus = true;
                                                                        $spouseDisabilityType = $spouse->disability_type;
                                                                    } elseif ($member->spouse_disability_status) {
                                                                        $spouseDisabilityStatus = true;
                                                                        $spouseDisabilityType = $member->spouse_disability_type;
                                                                    }
                                                                } else {
                                                                    $spouseDisabilityStatus = $member->spouse_disability_status ?? false;
                                                                    $spouseDisabilityType = $member->spouse_disability_type ?? null;
                                                                }
                                                            @endphp
                                                            @if($spouseDisabilityStatus)
                                                                <span
                                                                    class="badge bg-warning text-dark">{{ __('common.has_disability') }}</span>
                                                                @if(!empty($spouseDisabilityType))
                                                                    <br><small class="text-muted">{{ __('common.type') }}:
                                                                        {{ $spouseDisabilityType }}</small>
                                                                @endif
                                                            @else
                                                                <span class="badge bg-success">{{ __('common.no_disability') }}</span>
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Ahadi kwa Bwana (In-Kind Pledges) -->
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header text-white py-3 d-flex justify-content-between align-items-center"
                                style="background-color: #940000 !important;">
                                <h5 class="mb-0"><i class="fas fa-handshake me-2"></i>{{ __('common.ahadi_kwa_bwana') }}
                                    ({{ date('Y') }})</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="ps-3">{{ __('common.item_type') }}</th>
                                                <th>{{ __('common.promised') }}</th>
                                                <th>{{ __('common.fulfilled') }}</th>
                                                <th>{{ __('common.progress') }}</th>
                                                <th>{{ __('common.status') }}</th>
                                                <th class="text-end pe-3">{{ __('common.actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($ahadiPledges as $pledge)
                                                <tr>
                                                    <td class="ps-3">
                                                        <span class="fw-bold">{{ $pledge->item_type }}</span>
                                                        @if($pledge->notes)
                                                            <br><small class="text-muted">{{ $pledge->notes }}</small>
                                                        @endif
                                                    </td>
                                                    <td>{{ $pledge->quantity_promised }} {{ $pledge->unit }}</td>
                                                    <td>{{ $pledge->quantity_fulfilled }} {{ $pledge->unit }}</td>
                                                    <td style="min-width: 150px;">
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
                                                            <span class="badge bg-success">{{ __('common.fully_fulfilled') }}</span>
                                                        @elseif($pledge->status == 'partially_fulfilled')
                                                            <span
                                                                class="badge bg-warning text-dark">{{ __('common.partially_fulfilled') }}</span>
                                                        @else
                                                            <span class="badge bg-secondary">{{ __('common.promised') }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end pe-3">
                                                        <button class="btn btn-sm btn-outline-success rounded-pill px-3"
                                                            onclick="openFulfillmentModal({{ json_encode($pledge) }})">
                                                            {{ __('common.update_progress') }}
                                                        </button>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center py-4 text-muted" style="height: 100px;">
                                                        <i
                                                            class="fas fa-info-circle me-2"></i>{{ __('common.no_kind_pledges_recorded') }}
                                                        {{ date('Y') }}.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
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
                                        <i class="fas fa-child me-2"></i>{{ __('common.children_information') }}
                                    </h5>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>#</th>
                                                    <th>{{ __('common.name') }}</th>
                                                    <th>{{ __('common.age') }}</th>
                                                    <th>{{ __('common.gender') }}</th>
                                                    <th>{{ __('common.date_of_birth') }}</th>
                                                    <th>{{ __('common.parent_guardian') }}</th>
                                                    <th>{{ __('common.age_group') }}</th>
                                                    <th>{{ __('common.baptism_status') }}</th>
                                                    <th>{{ __('common.orphan_status') }}</th>
                                                    <th>{{ __('common.disability') }}</th>
                                                    <th>{{ __('common.vulnerability') }}</th>
                                                    <th class="text-end">{{ __('common.actions') }}</th>
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
                                                            'infant' => __('common.infant') . ' (< 3)',
                                                            'sunday_school' => __('common.sunday_school') . ' (3-12)',
                                                            'teenager' => __('common.teenager') . ' (13-17)'
                                                        ];

                                                        $ageGroupColors = [
                                                            'infant' => 'secondary',
                                                            'sunday_school' => 'success',
                                                            'teenager' => 'warning'
                                                        ];

                                                        $ageGroupLabel = $ageGroup ? ($ageGroupLabels[$ageGroup] ?? __('common.no_data')) : __('common.adult') . ' (18+)';
                                                        $ageGroupColor = $ageGroup ? ($ageGroupColors[$ageGroup] ?? 'secondary') : 'dark';
                                                    @endphp
                                                    <tr>
                                                        <td class="text-muted">{{ $loop->iteration }}</td>
                                                        <td><strong>{{ $child->full_name }}</strong></td>
                                                        <td><strong>{{ $age }} {{ __('common.years') }}</strong></td>
                                                        <td>
                                                            <span
                                                                class="badge bg-{{ $child->gender === 'male' ? 'primary' : 'info' }}">
                                                                {{ __('common.' . $child->gender) }}
                                                            </span>
                                                        </td>
                                                        <td>{{ \Carbon\Carbon::parse($child->date_of_birth)->format('M d, Y') }}
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center gap-2">
                                                                @if($hasMemberParent)
                                                                    <i class="fas fa-user text-primary"></i>
                                                                @else
                                                                    <i class="fas fa-users text-warning"></i>
                                                                @endif
                                                                <span
                                                                    class="fw-bold">{{ $parentName ?? __('common.no_data') }}</span>
                                                            </div>
                                                            @if($hasMemberParent)
                                                                <span class="badge bg-success mt-1">{{ __('common.member') }}</span>
                                                            @else
                                                                <span
                                                                    class="badge bg-warning text-dark mt-1">{{ __('common.non_member') }}</span>
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
                                                                        <i class="fas fa-tint me-1"></i>{{ __('common.baptized') }}
                                                                    </span>
                                                                    @if($child->baptism_date)
                                                                        <br><small
                                                                            class="text-muted">{{ \Carbon\Carbon::parse($child->baptism_date)->format('M d, Y') }}</small>
                                                                    @endif
                                                                @else
                                                                    <span class="badge bg-secondary">{{ __('common.not_baptized') }}</span>
                                                                @endif
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @php
                                                                $childOrphanLabels = [
                                                                    'not_orphan' => __('common.not_orphan'),
                                                                    'father_deceased' => __('common.father_deceased'),
                                                                    'mother_deceased' => __('common.mother_deceased'),
                                                                    'both_deceased' => __('common.both_deceased'),
                                                                ];
                                                                $childOrphan = $child->orphan_status ?? 'not_orphan';
                                                            @endphp
                                                            <span class="badge bg-light text-dark border">
                                                                {{ $childOrphanLabels[$childOrphan] ?? __('common.no_data') }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            @if($child->disability_status)
                                                                <span
                                                                    class="badge bg-warning text-dark">{{ __('common.has_disability') }}</span>
                                                                @if(!empty($child->disability_type))
                                                                    <br><small class="text-muted">{{ __('common.type') }}:
                                                                        {{ $child->disability_type }}</small>
                                                                @endif
                                                            @else
                                                                <span class="badge bg-success">{{ __('common.no_disability') }}</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($child->vulnerable_status)
                                                                <span class="badge bg-danger">{{ __('common.vulnerable') }}</span>
                                                                @if(!empty($child->vulnerable_type))
                                                                    <br><small class="text-muted">{{ __('common.type') }}:
                                                                        {{ $child->vulnerable_type }}</small>
                                                                @endif
                                                            @else
                                                                <span class="badge bg-success">{{ __('common.not_vulnerable') }}</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-end">
                                                            <a href="{{ route('children.show', $child->id) }}"
                                                                class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                                                <i class="fas fa-eye me-1"></i> {{ __('common.view') }}
                                                            </a>
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
                                        {{ __('common.no_children_registered_family') }}
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


    <!-- Fulfillment Modal -->
    <div class="modal fade" id="fulfillmentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header text-white" style="background-color: #940000 !important;">
                    <h5 class="modal-title">{{ __('common.update_fulfillment') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="fulfillmentForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="fw-bold d-block mb-1">{{ __('common.item') }}:</label>
                            <span id="ful_item_name" class="badge bg-secondary"></span>
                            <div class="mt-2 small text-muted">
                                {{ __('common.promised') }}: <span id="ful_promised"></span> |
                                {{ __('common.currently_fulfilled') }}: <span id="ful_current"></span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('common.total_fulfilled_qty') }}</label>
                            <div class="input-group">
                                <input type="number" step="0.01" class="form-control" name="quantity_fulfilled"
                                    id="ful_input" required>
                                <span class="input-group-text" id="ful_unit"></span>
                            </div>
                            <small class="text-muted">{{ __('common.enter_total_delivered_qty') }}</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('common.fulfillment_date') }}</label>
                            <input type="date" class="form-control" name="fulfillment_date" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('common.notes') }}</label>
                            <textarea class="form-control" name="notes" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                        <button type="submit" class="btn text-white"
                            style="background-color: #940000 !important; border-color: #940000 !important;">{{ __('common.update_progress') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @section('scripts')
        <script>
            function confirmExtension(memberId) {
                Swal.fire({
                    title: '{{ __('common.extend_membership') }}',
                    html: `
                    <p>{{ __('common.select_duration_extend') }}:</p>
                    <select id="extension-duration" class="form-select mb-3">
                        <option value="1">1 {{ __('common.month') }}</option>
                        <option value="3" selected>3 {{ __('common.months') }}</option>
                        <option value="6">6 {{ __('common.months') }}</option>
                        <option value="12">1 {{ __('common.year') }}</option>
                        <option value="24">2 {{ __('common.years') }}</option>
                        <option value="custom">{{ __('common.custom_duration') }}</option>
                    </select>
                    <div id="custom-duration-container" style="display: none;" class="mb-3">
                        <label class="form-label small text-muted">{{ __('common.enter_duration') }}:</label>
                        <div class="input-group">
                            <input type="number" id="custom-duration" class="form-control" min="1" placeholder="e.g. 2">
                            <select id="custom-unit" class="form-select" style="max-width: 120px; flex: 0 0 auto;">
                                <option value="months">{{ __('common.months') }}</option>
                                <option value="years">{{ __('common.years') }}</option>
                            </select>
                        </div>
                    </div>
                    <textarea id="extension-notes" class="form-control" placeholder="{{ __('common.notes') }} ({{ __('common.optional') }})" rows="2"></textarea>
                `,
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#0dcaf0',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '{{ __('common.extend') }}',
                    cancelButtonText: '{{ __('common.cancel') }}',
                    didOpen: () => {
                        const select = Swal.getPopup().querySelector('#extension-duration');
                        const customContainer = Swal.getPopup().querySelector('#custom-duration-container');
                        const customInput = Swal.getPopup().querySelector('#custom-duration');

                        select.addEventListener('change', () => {
                            if (select.value === 'custom') {
                                customContainer.style.display = 'block';
                                customInput.focus();
                            } else {
                                customContainer.style.display = 'none';
                            }
                        });
                    },
                    preConfirm: () => {
                        let duration = document.getElementById('extension-duration').value;
                        let notes = document.getElementById('extension-notes').value;

                        if (duration === 'custom') {
                            const customValue = document.getElementById('custom-duration').value;
                            const unit = document.getElementById('custom-unit').value;

                            if (!customValue || customValue < 1) {
                                Swal.showValidationMessage('{{ __('common.enter_valid_duration') }}');
                                return false;
                            }

                            // Convert to months if years selected
                            duration = unit === 'years' ? customValue * 12 : customValue;
                        }

                        return {
                            duration_months: duration,
                            notes: notes
                        };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                            document.querySelector('input[name="_token"]')?.value ||
                            '{{ csrf_token() }}';

                        const formData = new FormData();
                        formData.append('duration_months', result.value.duration_months);
                        formData.append('notes', result.value.notes);
                        formData.append('_token', csrfToken);

                        fetch(`{{ url('/members') }}/${memberId}/extend`, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: formData
                        })
                            .then(async response => {
                                if (!response.ok) {
                                    const text = await response.text();
                                    try {
                                        const errorData = JSON.parse(text);
                                        throw new Error(errorData.message || 'Server error');
                                    } catch (e) {
                                        throw new Error(text || 'Server error');
                                    }
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: '{{ __('common.extended') }}!',
                                        html: `{{ __('common.membership_extended_success') }}.<br>{{ __('common.new_end_date') }}: <strong>${data.new_end_date}</strong>`,
                                        timer: 3000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: data.message || 'Failed to extend membership'
                                    });
                                }
                            })
                            .catch(error => {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: error.message || 'An error occurred'
                                });
                            });
                    }
                });
            }

            function confirmDelete(memberId) {
                Swal.fire({
                    title: '{{ __('common.delete_member') }}',
                    html: `
                    <p>{{ __('common.confirm_delete_member') }}</p>
                    <p class="text-danger small mt-2"><strong>{{ __('common.warning') }}:</strong> {{ __('common.action_cannot_undone') }}</p>
                    <textarea id="delete-reason" class="form-control mt-3" placeholder="{{ __('common.reason_for_deletion') }} ({{ __('common.optional') }})" rows="3"></textarea>
                `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '{{ __('common.yes_delete') }}',
                    cancelButtonText: '{{ __('common.cancel') }}',
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
                                        title: '{{ __('common.session_expired') }}',
                                        text: '{{ __('common.session_expired_refresh') }}',
                                        confirmButtonText: '{{ __('common.refresh_page') }}'
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                    return;
                                }

                                if (data.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: '{{ __('common.deleted') }}',
                                        text: data.message || '{{ __('common.member_deleted_success') }}',
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        window.location.href = '{{ route("members.index") }}';
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: '{{ __('common.error') }}',
                                        text: data.message || '{{ __('common.failed_delete_member') }}'
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
                                        title: '{{ __('common.session_expired') }}',
                                        text: '{{ __('common.session_expired_refresh') }}',
                                        confirmButtonText: '{{ __('common.refresh_page') }}'
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
                                    title: '{{ __('common.delete_error') }}',
                                    html: `<p><strong>${errorMessage}</strong></p><p class="small text-muted mt-2">Member ID: ${memberId}</p>`
                                });
                            });
                    }
                });
            }

            // Show success message if member was just added
            document.addEventListener('DOMContentLoaded', function () {
                @if(session('success') && session('member_id'))
                    Swal.fire({
                        icon: 'success',
                        title: '{{ __('common.member_registered_success_title') }}',
                        html: `
                            <div class="text-start">
                                <p><strong>{{ session('name') }}</strong> {{ __('common.has_been_registered_successfully') }}!</p>
                                <p class="mb-2"><strong>{{ __('common.member_id') }}:</strong> {{ session('user_id') }}</p>
                                <p class="mb-2"><strong>{{ __('common.membership_type') }}:</strong> {{ ucfirst(session('membership_type')) }}</p>
                                <hr>
                                <p class="small text-muted mb-0">{{ __('common.viewing_member_details_page') }}.</p>
                            </div>
                        `,
                        confirmButtonText: '{{ __('common.ok') }}',
                        confirmButtonColor: '#5b2a86',
                        timer: 5000,
                        timerProgressBar: true
                    });
                @elseif(session('success'))
                    Swal.fire({
                        icon: 'success',
                        title: '{{ __('common.success') }}!',
                        text: '{{ session('success') }}',
                        timer: 3000,
                        showConfirmButton: false
                    });
                @endif
        });

            let itemCount = 1;
            const itemTypes = @json($itemTypes);

            function addItem() {
                const container = document.getElementById('items-container');
                const row = document.createElement('div');
                row.className = 'item-row mb-3';
                row.innerHTML = `
                <div class="row g-2">
                    <div class="col-md-4">
                        <select class="form-select form-select-sm" name="items[${itemCount}][item_type]" required>
                            ${itemTypes.map(t => `<option value="${t}">${t}</option>`).join('')}
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" step="0.01" class="form-control form-control-sm" name="items[${itemCount}][quantity_promised]" required>
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control form-control-sm" name="items[${itemCount}][unit]" placeholder="{{ __('common.bags_head_etc') }}">
                    </div>
                    <div class="col-md-3">
                        <input type="number" class="form-control form-control-sm" name="items[${itemCount}][estimated_value]">
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

            function openFulfillmentModal(pledge) {
                const form = document.getElementById('fulfillmentForm');
                form.action = `/finance/ahadi-pledges/${pledge.id}`;

                document.getElementById('ful_item_name').textContent = pledge.item_type;
                document.getElementById('ful_promised').textContent = pledge.quantity_promised + ' ' + (pledge.unit || '');
                document.getElementById('ful_current').textContent = pledge.quantity_fulfilled + ' ' + (pledge.unit || '');
                document.getElementById('ful_input').value = pledge.quantity_fulfilled;
                document.getElementById('ful_unit').textContent = pledge.unit || '';

                const modal = new bootstrap.Modal(document.getElementById('fulfillmentModal'));
                modal.show();
            }

        </script>
    @endsection
@endsection