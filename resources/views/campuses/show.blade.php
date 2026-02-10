@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mt-4"><i class="fas fa-building me-2"></i>{{ $campus->name }}</h1>
            <p class="text-muted mb-0">
                @if($campus->is_main_campus)
                    <span class="badge bg-primary">Main Campus</span>
                @else
                    <span class="badge bg-info">Sub Campus</span>
                    @if($campus->parent)
                        <span class="text-muted">— Parent: {{ $campus->parent->name }}</span>
                    @endif
                @endif
            </p>
        </div>
        <div class="d-flex gap-2">
            @if(auth()->user()->isAdmin() || auth()->user()->isUsharikaAdmin())
            <a href="{{ route('campuses.edit', $campus) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            @endif
            <a href="{{ route('campuses.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Campuses
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">{{ autoTranslate('Total Members') }}</h6>
                            <h2 class="mb-0">{{ number_format($totalMembers) }}</h2>
                            @if($campus->is_main_campus && ($subCampusMemberCount > 0 || $subCampusChildMemberCount > 0))
                                <small class="text-muted">
                                    {{ number_format($memberCount + $childMemberCount) }} direct ({{ $memberCount }} adults, {{ $childMemberCount }} children) + {{ number_format($subCampusMemberCount + $subCampusChildMemberCount) }} from sub campuses
                                </small>
                            @elseif($childMemberCount > 0)
                                <small class="text-muted">
                                    {{ number_format($memberCount) }} adults, {{ number_format($childMemberCount) }} children
                                </small>
                            @endif
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">{{ autoTranslate('Communities') }}</h6>
                            <h2 class="mb-0">{{ number_format($campus->communities->count()) }}</h2>
                            <small class="text-muted">
                                {{ $campus->communities->where('is_active', true)->count() }} {{ autoTranslate('active') }}
                            </small>
                        </div>
                        <div class="text-info">
                            <i class="fas fa-home fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">{{ autoTranslate('Leaders') }}</h6>
                            <h2 class="mb-0">{{ number_format(\App\Models\Leader::where('campus_id', $campus->id)->where('is_active', true)->count()) }}</h2>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-user-tie fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">{{ autoTranslate('Status') }}</h6>
                            <h4 class="mb-0">
                                @if($campus->is_active)
                                    <span class="badge bg-success">{{ autoTranslate('Active') }}</span>
                                @else
                                    <span class="badge bg-danger">{{ autoTranslate('Inactive') }}</span>
                                @endif
                            </h4>
                            <small class="text-muted">{{ autoTranslate('Code') }}: {{ $campus->code }}</small>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Campus Details -->
        <div class="col-md-8">
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>{{ autoTranslate('Campus Details') }}</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        @if($campus->description)
                        <tr>
                            <th width="30%">{{ autoTranslate('Description') }}:</th>
                            <td>{{ $campus->description }}</td>
                        </tr>
                        @endif
                        @if($campus->address)
                        <tr>
                            <th>{{ autoTranslate('Address') }}:</th>
                            <td>{{ $campus->address }}</td>
                        </tr>
                        @endif
                        @if($campus->region || $campus->district || $campus->ward)
                        <tr>
                            <th>{{ autoTranslate('Location') }}:</th>
                            <td>
                                {{ implode(', ', array_filter([$campus->ward, $campus->district, $campus->region])) }}
                            </td>
                        </tr>
                        @endif
                        @if($campus->phone_number)
                        <tr>
                            <th>{{ autoTranslate('Phone') }}:</th>
                            <td>
                                <a href="tel:{{ $campus->phone_number }}">{{ $campus->phone_number }}</a>
                            </td>
                        </tr>
                        @endif
                        @if($campus->email)
                        <tr>
                            <th>{{ autoTranslate('Email') }}:</th>
                            <td>
                                <a href="mailto:{{ $campus->email }}">{{ $campus->email }}</a>
                            </td>
                        </tr>
                        @endif
                        @if($campus->parent)
                        <tr>
                            <th>{{ autoTranslate('Parent Campus') }}:</th>
                            <td>
                                <a href="{{ route('campuses.show', $campus->parent) }}">{{ $campus->parent->name }}</a>
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Communities Section -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-home me-2"></i>{{ autoTranslate('Communities') }}</h5>
                    <a href="{{ route('campuses.communities.create', $campus) }}" class="btn btn-sm btn-light">
                        <i class="fas fa-plus me-1"></i>{{ autoTranslate('Add Community') }}
                    </a>
                </div>
                <div class="card-body">
                    @if($campus->communities->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ autoTranslate('Community Name') }}</th>
                                        <th>{{ autoTranslate('Church Elder') }}</th>
                                        <th>{{ autoTranslate('Members') }}</th>
                                        <th>{{ autoTranslate('Status') }}</th>
                                        <th>{{ autoTranslate('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($campus->communities as $community)
                                    <tr>
                                        <td>
                                            <strong>{{ $community->name }}</strong>
                                            @if($community->description)
                                                <br><small class="text-muted">{{ Str::limit($community->description, 50) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($community->churchElder && $community->churchElder->member)
                                                <span class="badge bg-info">{{ $community->churchElder->member->full_name }}</span>
                                            @else
                                                <span class="text-muted">{{ autoTranslate('Not Assigned') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $communityMemberCount = $community->members()->count();
                                                $communityChildCount = $community->memberChildren()->count();
                                                $communityTotal = $communityMemberCount + $communityChildCount;
                                            @endphp
                                            <span class="badge bg-primary">{{ $communityTotal }}</span>
                                            @if($communityChildCount > 0)
                                                <small class="text-muted d-block">({{ $communityMemberCount }} adults, {{ $communityChildCount }} children)</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($community->is_active)
                                                <span class="badge bg-success">{{ autoTranslate('Active') }}</span>
                                            @else
                                                <span class="badge bg-danger">{{ autoTranslate('Inactive') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('campuses.communities.show', [$campus, $community]) }}" class="btn btn-info" title="{{ autoTranslate('View') }}">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if(auth()->user()->isAdmin() || auth()->user()->isUsharikaAdmin())
                                                <a href="{{ route('campuses.communities.edit', [$campus, $community]) }}" class="btn btn-warning" title="{{ autoTranslate('Edit') }}">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-home fa-3x mb-3 d-block"></i>
                            <p class="mb-0">{{ autoTranslate('No communities created for this campus yet.') }}</p>
                            <a href="{{ route('campuses.communities.create', $campus) }}" class="btn btn-primary mt-3">
                                <i class="fas fa-plus me-2"></i>{{ autoTranslate('Create First Community') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sub Campuses Section (if main campus) -->
            @if($campus->is_main_campus && $campus->subCampuses->count() > 0)
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-sitemap me-2"></i>{{ autoTranslate('Sub Campuses (Branches)') }}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ autoTranslate('Branch Name') }}</th>
                                    <th>{{ autoTranslate('Code') }}</th>
                                    <th>{{ autoTranslate('Members') }}</th>
                                    <th>{{ autoTranslate('Status') }}</th>
                                    <th>{{ autoTranslate('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($campus->subCampuses as $subCampus)
                                <tr>
                                    <td>
                                        <strong>{{ $subCampus->name }}</strong>
                                        @if($subCampus->description)
                                            <br><small class="text-muted">{{ Str::limit($subCampus->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td><span class="badge bg-secondary">{{ $subCampus->code }}</span></td>
                                    <td>
                                        @php
                                            $subMemberCount = $subCampus->members()->count();
                                            $subChildCount = $subCampus->memberChildren()->count();
                                            $subTotal = $subMemberCount + $subChildCount;
                                        @endphp
                                        <span class="badge bg-primary">{{ $subTotal }}</span>
                                        @if($subChildCount > 0)
                                            <small class="text-muted d-block">({{ $subMemberCount }} adults, {{ $subChildCount }} children)</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($subCampus->is_active)
                                            <span class="badge bg-success">{{ autoTranslate('Active') }}</span>
                                        @else
                                            <span class="badge bg-danger">{{ autoTranslate('Inactive') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('campuses.show', $subCampus) }}" class="btn btn-sm btn-info" title="{{ autoTranslate('View') }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Campus Members Section -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-users me-2"></i>{{ autoTranslate('Campus Members') }}
                        <span class="badge bg-white text-dark ms-2 fw-bold">{{ $memberCount + $childMemberCount }}</span>
                        @if($childMemberCount > 0)
                            <small class="ms-2">({{ $memberCount }} adults, {{ $childMemberCount }} children)</small>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if(($campusMembers && $campusMembers->count() > 0) || ($campusChildMembers && $campusChildMembers->count() > 0))
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ autoTranslate('Name') }}</th>
                                        <th>{{ autoTranslate('Member ID') }}</th>
                                        <th>{{ autoTranslate('Type') }}</th>
                                        <th>{{ autoTranslate('Phone') }}</th>
                                        <th>{{ autoTranslate('Email') }}</th>
                                        <th>{{ autoTranslate('Community') }}</th>
                                        <th>{{ autoTranslate('Parent/Guardian') }}</th>
                                        <th>{{ autoTranslate('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Regular Adult Members --}}
                                    @foreach($campusMembers as $member)
                                        <tr>
                                            <td><strong>{{ $member->full_name }}</strong></td>
                                            <td><span class="badge bg-secondary">{{ $member->member_id }}</span></td>
                                            <td>
                                                @if($member->membership_type === 'temporary')
                                                    <span class="badge bg-warning">{{ autoTranslate('Temporary') }}</span>
                                                @else
                                                    <span class="badge bg-primary">{{ autoTranslate('Adult') }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $member->phone_number ?? '—' }}</td>
                                            <td>{{ $member->email ?? '—' }}</td>
                                            <td>
                                                @if($member->community)
                                                    <span class="badge bg-info">{{ $member->community->name }}</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>—</td>
                                            <td>
                                                <a href="{{ route('members.view') }}?search={{ $member->member_id }}" class="btn btn-sm btn-info" title="{{ autoTranslate('View Member') }}">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    {{-- Children who are church members --}}
                                    @foreach($campusChildMembers as $child)
                                        <tr>
                                            <td><strong>{{ $child->full_name }}</strong></td>
                                            <td>
                                                @if($child->member && $child->member->member_id)
                                                    <span class="badge bg-secondary">{{ $child->member->member_id }}-CH</span>
                                                @else
                                                    <span class="badge bg-info">{{ autoTranslate('Child') }}</span>
                                                @endif
                                            </td>
                                            <td><span class="badge bg-success">{{ autoTranslate('Child Member') }}</span></td>
                                            <td>{{ $child->phone_number ?? ($child->member->phone_number ?? '—') }}</td>
                                            <td>{{ $child->member->email ?? '—' }}</td>
                                            <td>
                                                @if($child->community)
                                                    <span class="badge bg-info">{{ $child->community->name }}</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($child->member)
                                                    <a href="{{ route('members.view') }}?search={{ $child->member->member_id }}" class="text-decoration-none">
                                                        {{ $child->member->full_name }}
                                                    </a>
                                                @else
                                                    {{ $child->parent_name ?? '—' }}
                                                @endif
                                            </td>
                                            <td>
                                                @if($child->member)
                                                    <a href="{{ route('members.view') }}?search={{ $child->member->member_id }}" class="btn btn-sm btn-info" title="{{ autoTranslate('View Parent') }}">
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
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-users fa-2x mb-2 d-block"></i>
                            <p class="mb-0">{{ autoTranslate('No members assigned to this campus yet.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Quick Actions -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>{{ autoTranslate('Quick Actions') }}</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('campuses.communities.create', $campus) }}" class="btn btn-outline-primary">
                            <i class="fas fa-plus me-2"></i>{{ autoTranslate('Add Community') }}
                        </a>
                        <a href="{{ route('members.view', ['campus_id' => $campus->id]) }}" class="btn btn-outline-info">
                            <i class="fas fa-users me-2"></i>{{ autoTranslate('View Members') }}
                        </a>
                        <a href="{{ route('campuses.communities.index', $campus) }}" class="btn btn-outline-success">
                            <i class="fas fa-home me-2"></i>{{ autoTranslate('View All Communities') }}
                        </a>
                        @if(auth()->user()->isAdmin() || auth()->user()->isUsharikaAdmin())
                        <a href="{{ route('campuses.edit', $campus) }}" class="btn btn-outline-warning">
                            <i class="fas fa-edit me-2"></i>{{ autoTranslate('Edit Campus') }}
                        </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Evangelism Leader Assignment -->
            @if(auth()->user()->isAdmin() || auth()->user()->isUsharikaAdmin())
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-cross me-2"></i>{{ autoTranslate('Evangelism Leader') }}</h5>
                </div>
                <div class="card-body">
                    @if($campus->evangelismLeader && $campus->evangelismLeader->member)
                        <div class="mb-3">
                            <strong>{{ autoTranslate('Current Evangelism Leader') }}:</strong>
                            <div class="mt-2 p-2 bg-light rounded">
                                <i class="fas fa-user-tie text-warning me-2"></i>
                                <strong>{{ $campus->evangelismLeader->member->full_name }}</strong>
                                <br>
                                <small class="text-muted">
                                    {{ autoTranslate('Member ID') }}: {{ $campus->evangelismLeader->member->member_id }}
                                    @if($campus->evangelismLeader->member->phone_number)
                                        | {{ $campus->evangelismLeader->member->phone_number }}
                                    @endif
                                </small>
                            </div>
                        </div>
                    @else
                        <p class="text-muted mb-3">{{ autoTranslate('No evangelism leader assigned to this campus yet.') }}</p>
                    @endif
                    
                    <form action="{{ route('campuses.assign-evangelism-leader', $campus) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="evangelism_leader_id" class="form-label">{{ autoTranslate('Select Evangelism Leader') }}</label>
                            <select name="evangelism_leader_id" id="evangelism_leader_id" class="form-select">
                                <option value="">{{ autoTranslate('-- Remove Assignment --') }}</option>
                                @foreach($availableEvangelismLeaders as $leader)
                                    <option value="{{ $leader->id }}" {{ $campus->evangelism_leader_id == $leader->id ? 'selected' : '' }}>
                                        {{ $leader->member->full_name ?? 'N/A' }} ({{ $leader->member->member_id ?? 'N/A' }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">{{ autoTranslate('Select an evangelism leader from this campus.') }}</small>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-warning btn-sm">
                                <i class="fas fa-save me-2"></i>{{ autoTranslate('Save Assignment') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            <!-- Campus Information -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>{{ autoTranslate('Information') }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>{{ autoTranslate('Campus Code') }}:</strong><br>
                        <code>{{ $campus->code }}</code>
                    </div>
                    <div class="mb-3">
                        <strong>{{ autoTranslate('Type') }}:</strong><br>
                        @if($campus->is_main_campus)
                            <span class="badge bg-primary">{{ autoTranslate('Main Campus') }}</span>
                        @else
                            <span class="badge bg-info">{{ autoTranslate('Sub Campus') }}</span>
                        @endif
                    </div>
                    <div class="mb-3">
                        <strong>{{ autoTranslate('Created') }}:</strong><br>
                        <small class="text-muted">{{ $campus->created_at->format('M d, Y') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
