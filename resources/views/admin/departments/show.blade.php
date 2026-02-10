@extends('layouts.index')

@section('content')
    <div class="container-fluid px-4 py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="{{ route('departments.index') }}">Departments</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $department->name }}</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-primary">{{ $department->name }}</h2>
            </div>
            <div>
                <div>
                    @unless(auth()->user()->isSecretary())
                        <button class="btn btn-outline-primary me-2" data-bs-toggle="modal"
                            data-bs-target="#editDepartmentModal">
                            <i class="fas fa-edit me-1"></i> Edit Rules
                        </button>
                        <button class="btn btn-info me-2 text-white" onclick="loadSuggestions({{ $department->id }})"
                            data-bs-toggle="modal" data-bs-target="#suggestMemberModal">
                            <i class="fas fa-magic me-1"></i> Suggest Members
                        </button>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                            <i class="fas fa-user-plus me-1"></i> Add Member
                        </button>
                    @endunless
                </div>
            </div>
        </div>



        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-4">
            <!-- Criteria Card -->
            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-light fw-bold text-uppercase small">
                        Eligibility Rules
                    </div>
                    <div class="card-body">
                        <p class="text-muted small">{{ $department->description }}</p>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Age Range</span>
                                <span class="badge bg-secondary rounded-pill">
                                    {{ $department->criteria['min_age'] ?? '0' }} -
                                    {{ $department->criteria['max_age'] ?? '∞' }}
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Gender</span>
                                <span
                                    class="badge bg-{{ ($department->criteria['gender'] ?? '') === 'male' ? 'info' : (($department->criteria['gender'] ?? '') === 'female' ? 'danger' : 'secondary') }}">
                                    {{ ucfirst($department->criteria['gender'] ?? 'Any') }}
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Marital Status</span>
                                <span class="badge bg-warning text-dark">
                                    {{ ucfirst($department->criteria['marital_status'] ?? 'Any') }}
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Children</span>
                                <span
                                    class="badge bg-{{ ($department->criteria['has_children'] ?? false) ? 'success' : 'secondary' }}">
                                    {{ ($department->criteria['has_children'] ?? false) ? 'Required' : 'Optional' }}
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Members List -->
            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold"><i class="fas fa-users me-2 text-primary"></i>Members
                            ({{ $members->count() + $children->count() }})</h5>
                        <div class="input-group w-auto">
                            <input type="text" class="form-control form-control-sm" id="memberSearch"
                                placeholder="Search...">
                            <button class="btn btn-outline-secondary btn-sm"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="membersTable">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">Name</th>
                                        <th>Status</th>
                                        <th>Eligibility</th>
                                        <th>Assigned</th>
                                        @unless(auth()->user()->isSecretary())
                                            <th>Unassign</th>
                                        @endunless
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($members as $member)
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-primary rounded-circle text-white d-flex align-items-center justify-content-center me-3"
                                                        style="width: 32px; height: 32px;">
                                                        {{ substr($member->full_name, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 fw-bold">{{ $member->full_name }}</h6>
                                                        <small class="text-muted">Adult - ID: {{ $member->member_id }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-success bg-opacity-10 text-success">Active</span>
                                            </td>
                                            <td>
                                                @if($member->eligibility_status['eligible'])
                                                    <span class="badge bg-success bg-opacity-10 text-success">Eligible</span>
                                                @else
                                                    <span class="badge bg-danger bg-opacity-10 text-danger" data-bs-toggle="tooltip"
                                                        title="{{ $member->eligibility_status['reason'] }}">
                                                        Ineligible <i class="fas fa-exclamation-circle ps-1"></i>
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <small
                                                    class="text-muted">{{ $member->pivot->created_at->format('M d, Y') }}</small>
                                            </td>
                                            <td>
                                                @unless(auth()->user()->isSecretary())
                                                    <form
                                                        action="{{ route('departments.remove-member', [$department->id, $member->id]) }}"
                                                        method="POST" onsubmit="return confirm('Remove this member?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-link text-danger"
                                                            title="Remove">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </form>
                                                @endunless
                                            </td>
                                        </tr>
                                    @empty
                                        @if($children->isEmpty())
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">
                                                    <i class="fas fa-user-slash fa-2x mb-3 opacity-50"></i>
                                                    <p>No members assigned yet.</p>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforelse

                                    @foreach($children as $child)
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-info rounded-circle text-white d-flex align-items-center justify-content-center me-3"
                                                        style="width: 32px; height: 32px;">
                                                        {{ substr($child->full_name, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 fw-bold">{{ $child->full_name }}</h6>
                                                        <small class="text-muted">Child - ID: {{ $child->id }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-success bg-opacity-10 text-success">Active</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">N/A</span>
                                            </td>
                                            <td>
                                                <small
                                                    class="text-muted">{{ $child->pivot->created_at->format('M d, Y') }}</small>
                                            </td>
                                            <td>
                                                @unless(auth()->user()->isSecretary())
                                                    <form
                                                        action="{{ route('departments.remove-child', [$department->id, $child->id]) }}"
                                                        method="POST" onsubmit="return confirm('Remove this child?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-link text-danger"
                                                            title="Remove">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </form>
                                                @endunless
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editDepartmentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('departments.update', $department->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Edit Department Rules</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" value="{{ $department->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control"
                                rows="3">{{ $department->description }}</textarea>
                        </div>
                        <hr>
                        <h6 class="fw-bold mb-3">Eligibility Criteria</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Age Range</label>
                                <div class="input-group">
                                    <input type="number" name="criteria[min_age]" class="form-control"
                                        value="{{ $department->criteria['min_age'] ?? '' }}" placeholder="Min">
                                    <span class="input-group-text">-</span>
                                    <input type="number" name="criteria[max_age]" class="form-control"
                                        value="{{ $department->criteria['max_age'] ?? '' }}" placeholder="Max">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Gender</label>
                                <select name="criteria[gender]" class="form-select">
                                    <option value="">Any</option>
                                    <option value="male" {{ ($department->criteria['gender'] ?? '') == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ ($department->criteria['gender'] ?? '') == 'female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Marital Status</label>
                                <select name="criteria[marital_status]" class="form-select">
                                    <option value="">Any</option>
                                    <option value="single" {{ ($department->criteria['marital_status'] ?? '') == 'single' ? 'selected' : '' }}>Single</option>
                                    <option value="married" {{ ($department->criteria['marital_status'] ?? '') == 'married' ? 'selected' : '' }}>Married</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Parental Status</label>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" name="criteria[has_children]" value="1"
                                        id="editHasChildren" {{ ($department->criteria['has_children'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="editHasChildren">
                                        Must have children
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Member Modal -->
    <div class="modal fade" id="addMemberModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('departments.assign', $department->id) }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Member to Department</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <ul class="nav nav-pills nav-justified mb-3" id="addTypeTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="adult-add-tab" data-bs-toggle="pill"
                                    data-bs-target="#adultAdd" type="button" role="tab">Adult</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="child-add-tab" data-bs-toggle="pill" data-bs-target="#childAdd"
                                    type="button" role="tab">Child</button>
                            </li>
                        </ul>

                        <div class="tab-content" id="addTypeContent">
                            <div class="tab-pane fade show active" id="adultAdd" role="tabpanel">
                                <div class="mb-3">
                                    <label class="form-label">Member ID</label>
                                    <select name="member_id" class="form-select mb-2" id="memberSelect"
                                        onchange="document.getElementById('childSelect').value = '';">
                                        <option value="">Select Member...</option>
                                        @php
                                            $memberQuery = \App\Models\Member::select('id', 'full_name', 'member_id');
                                            if (auth()->user()->isEvangelismLeader()) {
                                                $campus = auth()->user()->getCampus();
                                                if ($campus) {
                                                    $memberQuery->where('campus_id', $campus->id);
                                                }
                                            }
                                            $membersList = $memberQuery->limit(100)->get();
                                        @endphp
                                        @foreach($membersList as $m)
                                            <option value="{{ $m->id }}">{{ $m->full_name }} ({{ $m->member_id }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="childAdd" role="tabpanel">
                                <div class="mb-3">
                                    <label class="form-label">Child Name</label>
                                    <select name="child_id" class="form-select mb-2" id="childSelect"
                                        onchange="document.getElementById('memberSelect').value = '';">
                                        <option value="">Select Child...</option>
                                        @php
                                            $childQuery = \App\Models\Child::select('id', 'full_name');
                                            if (auth()->user()->isEvangelismLeader()) {
                                                $campus = auth()->user()->getCampus();
                                                if ($campus) {
                                                    $childQuery->where('campus_id', $campus->id);
                                                }
                                            }
                                            $childrenList = $childQuery->limit(100)->get();
                                        @endphp
                                        @foreach($childrenList as $c)
                                            <option value="{{ $c->id }}">{{ $c->full_name }} (ID: {{ $c->id }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="force" value="1" id="forceAssign">
                            <label class="form-check-label text-danger" for="forceAssign">
                                Force Assignment (Ignore Eligibility Rules)
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Add Member</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('memberSearch').addEventListener('keyup', function () {
            let searchText = this.value.toLowerCase();
            let tableRows = document.querySelectorAll('#membersTable tbody tr');

            tableRows.forEach(row => {
                let text = row.innerText.toLowerCase();
                row.style.display = text.includes(searchText) ? '' : 'none';
            });
        });
    </script>

    <!-- Suggest Member Modal -->
    <div class="modal fade" id="suggestMemberModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="fas fa-magic me-2"></i>Suggested Members</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">Based on age, gender, and marital status criteria.</p>
                    <div class="table-responsive">
                        <table class="table table-hover" id="suggestionsTable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Details</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="suggestionsBody">
                                <tr>
                                    <td colspan="3" class="text-center">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function loadSuggestions(deptId) {
            const tbody = document.getElementById('suggestionsBody');
            tbody.innerHTML = '<tr><td colspan="3" class="text-center"><div class="spinner-border text-primary" role="status"></div><div class="mt-2">Finding eligible candidates...</div></td></tr>';

            fetch(`{{ url('departments') }}/${deptId}/suggest`)
                .then(response => response.json())
                .then(data => {
                    tbody.innerHTML = '';
                    if (data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted">No eligible candidates found not already in this department.</td></tr>';
                        return;
                    }

                    data.forEach(person => {
                        const isChild = person.person_type === 'child';
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>
                                <div class="fw-bold">${person.full_name}</div>
                                <div class="small text-muted">${isChild ? 'Child ID: ' + person.id : person.member_id}</div>
                            </td>
                            <td>
                                <div class="small">
                                    ${person.gender ? '<span class="text-capitalize">' + person.gender + '</span>, ' : ''}
                                    ${person.age ? person.age + ' yrs' : ''}
                                </div>
                                <div class="small text-muted text-capitalize">${isChild ? 'Child' : (person.marital_status || 'Unknown status')}</div>
                            </td>
                            <td>
                                <form action="{{ url('departments') }}/${deptId}/assign" method="POST">
                                    @csrf
                                    <input type="hidden" name="${isChild ? 'child_id' : 'member_id'}" value="${person.id}">
                                    <button type="submit" class="btn btn-sm btn-primary">Add</button>
                                </form>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    tbody.innerHTML = '<tr><td colspan="3" class="text-center text-danger">Failed to load suggestions.</td></tr>';
                });
        }
    </script>
@endsection