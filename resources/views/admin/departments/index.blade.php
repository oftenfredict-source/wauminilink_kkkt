@extends('layouts.index')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold"><i class="fas fa-layer-group me-2"></i>Church Departments</h2>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createDepartmentModal">
            <i class="fas fa-plus me-1"></i> New Department
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        @forelse($departments as $dept)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm hover-shadow transition-all border-0 rounded-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h5 class="card-title fw-bold text-dark mb-0">{{ $dept->name }}</h5>
                        <div class="dropdown">
                            <button class="btn btn-link text-muted p-0" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('departments.show', $dept->id) }}"><i class="fas fa-eye me-2 text-primary"></i>View Details</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('departments.destroy', $dept->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger"><i class="fas fa-trash me-2"></i>Delete</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <p class="card-text text-muted small mb-3">{{ Str::limit($dept->description, 100) }}</p>
                    
                    <div class="mb-3">
                        <h6 class="text-uppercase text-muted small fw-bold mb-2">Eligibility Rules:</h6>
                        <div class="d-flex flex-wrap gap-1">
                            @if(isset($dept->criteria['min_age']) || isset($dept->criteria['max_age']))
                                <span class="badge bg-light text-dark border">
                                    Age: {{ $dept->criteria['min_age'] ?? '0' }} - {{ $dept->criteria['max_age'] ?? '∞' }}
                                </span>
                            @endif
                            @if(isset($dept->criteria['gender']))
                                <span class="badge bg-{{ $dept->criteria['gender'] === 'male' ? 'info' : 'danger' }} text-white">
                                    {{ ucfirst($dept->criteria['gender']) }}
                                </span>
                            @endif
                            @if(isset($dept->criteria['marital_status']))
                                <span class="badge bg-warning text-dark">
                                    {{ ucfirst($dept->criteria['marital_status']) }}
                                </span>
                            @endif
                            @if(isset($dept->criteria['has_children']) && $dept->criteria['has_children'])
                                <span class="badge bg-success">Parents</span>
                            @endif
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top">
                        <span class="text-muted small"><i class="fas fa-users me-1"></i> {{ $dept->members_count }} Members</span>
                        <a href="{{ route('departments.show', $dept->id) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">Manage</a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <div class="text-muted mb-3"><i class="fas fa-layer-group fa-3x"></i></div>
            <h4>No departments found</h4>
            <p class="text-muted">Get started by creating a new department.</p>
        </div>
        @endforelse
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createDepartmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('departments.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Create New Department</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Department Name</label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g. Youth Ministry">
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Describe the department's purpose..."></textarea>
                    </div>
                    
                    <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">Eligibility Criteria</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Age Range</label>
                            <div class="input-group">
                                <input type="number" name="criteria[min_age]" class="form-control" placeholder="Min" min="0">
                                <span class="input-group-text">-</span>
                                <input type="number" name="criteria[max_age]" class="form-control" placeholder="Max" min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Gender</label>
                            <select name="criteria[gender]" class="form-select">
                                <option value="">Any</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Marital Status</label>
                            <select name="criteria[marital_status]" class="form-select">
                                <option value="">Any</option>
                                <option value="single">Single</option>
                                <option value="married">Married</option>
                                <option value="divorced">Divorced</option>
                                <option value="widowed">Widowed</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Parental Status</label>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="criteria[has_children]" value="1" id="hasChildrenCheck">
                                <label class="form-check-label" for="hasChildrenCheck">
                                    Must have children
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Create Department</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
