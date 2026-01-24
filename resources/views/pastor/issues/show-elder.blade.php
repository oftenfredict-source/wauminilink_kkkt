@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0"><i class="fas fa-exclamation-triangle me-2 text-danger"></i>{{ $issue->title }}</h1>
                            <p class="text-muted mb-0">Church Elder Issue</p>
                        </div>
                        <a href="{{ route('pastor.issues.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Issues
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8 mb-4">
            <!-- Issue Details -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Issue Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Priority:</strong><br>
                            <span class="badge {{ $issue->priority_badge }}">{{ ucfirst($issue->priority) }}</span>
                        </div>
                        <div class="col-md-6">
                            <strong>Status:</strong><br>
                            <span class="badge {{ $issue->status_badge }}">{{ ucfirst(str_replace('_', ' ', $issue->status)) }}</span>
                        </div>
                    </div>
                    @if($issue->issue_type)
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Issue Type:</strong><br>
                            <span class="text-muted">{{ ucfirst(str_replace('_', ' ', $issue->issue_type)) }}</span>
                        </div>
                    </div>
                    @endif
                    <div class="mb-3">
                        <strong>Description:</strong><br>
                        <p class="text-muted">{{ $issue->description }}</p>
                    </div>
                    @if($issue->resolution_notes)
                    <div class="mb-3">
                        <strong>Resolution Notes:</strong><br>
                        <div class="alert alert-success">
                            {!! nl2br(e($issue->resolution_notes)) !!}
                        </div>
                    </div>
                    @endif
                    @if($issue->resolved_at && $issue->resolver)
                    <div class="mb-3">
                        <strong>Resolved By:</strong><br>
                        <span class="text-muted">{{ $issue->resolver->name }} on {{ $issue->resolved_at->format('F d, Y h:i A') }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Reporter Information -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Reporter Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <strong>Church Elder:</strong><br>
                            <span class="text-muted">{{ $issue->churchElder->name ?? 'N/A' }}</span>
                        </div>
                        @if($issue->community)
                        <div class="col-md-6 mb-2">
                            <strong>Community:</strong><br>
                            <span class="text-muted">{{ $issue->community->name }}</span>
                        </div>
                        @endif
                        @if($issue->resolved_at)
                        <div class="col-md-6 mb-2">
                            <strong>Resolved At:</strong><br>
                            <span class="text-muted">{{ $issue->resolved_at->format('F d, Y h:i A') }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar - Comments & Actions -->
        <div class="col-md-4 mb-4">
            @if($issue->pastor_comments)
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-comment me-2"></i>Your Comments</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2">{{ $issue->pastor_comments }}</p>
                    @if($issue->pastor_commented_at)
                        <small class="text-muted">
                            <i class="fas fa-clock me-1"></i>{{ $issue->pastor_commented_at->format('M d, Y h:i A') }}
                        </small>
                    @endif
                </div>
            </div>
            @endif

            <!-- Update Status Form -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-tasks me-2"></i>Update Issue Status</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('pastor.issues.update-status-elder', $issue->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="open" {{ $issue->status === 'open' ? 'selected' : '' }}>Open</option>
                                <option value="in_progress" {{ $issue->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="resolved" {{ $issue->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="closed" {{ $issue->status === 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="resolution_notes" class="form-label">Resolution Notes</label>
                            <textarea class="form-control @error('resolution_notes') is-invalid @enderror" 
                                      id="resolution_notes" name="resolution_notes" rows="3" 
                                      placeholder="Optional: Add notes about how the issue was resolved...">{{ old('resolution_notes', $issue->resolution_notes) }}</textarea>
                            @error('resolution_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Optional: Add notes when resolving or closing the issue</small>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-1"></i> Update Status
                        </button>
                    </form>
                </div>
            </div>

            <!-- Add/Update Comments Form -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-edit me-2"></i>{{ $issue->pastor_comments ? 'Update Comments' : 'Add Comments/Suggestions' }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('pastor.issues.comment-elder', $issue->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="pastor_comments" class="form-label">Your Comments or Suggestions <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('pastor_comments') is-invalid @enderror" 
                                      id="pastor_comments" name="pastor_comments" rows="6" 
                                      placeholder="Provide your feedback, suggestions, or comments on this issue..." required>{{ old('pastor_comments', $issue->pastor_comments) }}</textarea>
                            @error('pastor_comments')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Minimum 10 characters required</small>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-paper-plane me-1"></i> {{ $issue->pastor_comments ? 'Update Comments' : 'Submit Comments' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



