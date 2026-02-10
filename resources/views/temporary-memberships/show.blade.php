@extends('layouts.index')

@section('title', 'Temporary Membership Details')

@section('content')
<div class="container-fluid px-2 px-md-5 py-4">
    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
        <div class="card-header bg-primary d-flex flex-column flex-md-row justify-content-between align-items-center py-4 px-4 border-0">
            <span class="fs-5 fw-bold text-white d-flex align-items-center">
                <i class="fas fa-user-clock me-2"></i> Temporary Membership Details
            </span>
            <a href="{{ route('pastor.temporary-memberships.index') }}" class="btn btn-outline-light btn-sm mt-2 mt-md-0">
                <i class="fas fa-arrow-left me-1"></i> Back to List
            </a>
        </div>
        <div class="card-body bg-light px-4 py-4">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6 class="text-muted">Member Information</h6>
                    <p><strong>Name:</strong> {{ $member->full_name }}</p>
                    <p><strong>Member ID:</strong> {{ $member->member_id }}</p>
                    <p><strong>Phone:</strong> {{ $member->phone_number }}</p>
                    <p><strong>Email:</strong> {{ $member->email ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted">Membership Information</h6>
                    <p><strong>Type:</strong> <span class="badge bg-warning">Temporary</span></p>
                    <p><strong>Start Date:</strong> {{ $member->membership_start_date ? $member->membership_start_date->format('F d, Y') : 'N/A' }}</p>
                    <p><strong>End Date:</strong> {{ $member->membership_end_date ? $member->membership_end_date->format('F d, Y') : 'N/A' }}</p>
                    <p><strong>Duration:</strong> {{ $member->membership_duration_months ?? 'N/A' }} months</p>
                    <p><strong>Status:</strong> <span class="badge bg-{{ $member->isExpired() ? 'danger' : ($member->isExpiringSoon() ? 'warning' : 'success') }}">
                        {{ ucfirst($member->membership_status) }}
                    </span></p>
                </div>
            </div>

            <hr>

            <h5 class="mb-3">Actions</h5>
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="card border-primary">
                        <div class="card-body">
                            <h6 class="card-title">Extend Membership</h6>
                            <form id="extendForm" class="mt-3">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Duration</label>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <input type="number" class="form-control" name="duration_value" min="1" max="120" value="3" required>
                                        </div>
                                        <div class="col-6">
                                            <select class="form-select" name="duration_unit" required>
                                                <option value="months">Months</option>
                                                <option value="years">Years</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm w-100">Extend</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-success">
                        <div class="card-body">
                            <h6 class="card-title">Convert to Permanent</h6>
                            <p class="text-muted small">Convert this temporary membership to permanent.</p>
                            <form id="convertForm" class="mt-3">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm w-100">Convert</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-secondary">
                        <div class="card-body">
                            <h6 class="card-title">Mark as Completed</h6>
                            <p class="text-muted small">Mark this membership as completed/left.</p>
                            <form id="completeForm" class="mt-3">
                                @csrf
                                <button type="submit" class="btn btn-secondary btn-sm w-100">Mark Completed</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('extendForm').addEventListener('submit', function(e) {
    e.preventDefault();
    if (confirm('Are you sure you want to extend this membership?')) {
        fetch('{{ route("pastor.temporary-memberships.extend", $member) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                duration_value: this.duration_value.value,
                duration_unit: this.duration_unit.value
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Membership extended successfully!');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        });
    }
});

document.getElementById('convertForm').addEventListener('submit', function(e) {
    e.preventDefault();
    if (confirm('Are you sure you want to convert this membership to permanent?')) {
        fetch('{{ route("pastor.temporary-memberships.convert", $member) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Membership converted to permanent successfully!');
                window.location.href = '{{ route("pastor.temporary-memberships.index") }}';
            } else {
                alert('Error: ' + data.message);
            }
        });
    }
});

document.getElementById('completeForm').addEventListener('submit', function(e) {
    e.preventDefault();
    if (confirm('Are you sure you want to mark this membership as completed?')) {
        fetch('{{ route("pastor.temporary-memberships.complete", $member) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Membership marked as completed successfully!');
                window.location.href = '{{ route("pastor.temporary-memberships.index") }}';
            } else {
                alert('Error: ' + data.message);
            }
        });
    }
});
</script>
@endsection





