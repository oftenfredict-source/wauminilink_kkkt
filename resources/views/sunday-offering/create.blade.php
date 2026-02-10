@extends('layouts.index')

@section('title', 'Start Sunday Collection')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Start New Collection Session</h6>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('sunday-offering.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label fw-bold">Date of Collection</label>
                            <input type="date" name="collection_date" class="form-control" value="{{ date('Y-m-d') }}"
                                required>
                            <div class="form-text">The Sunday date for this offering.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Select Mtaa / Sub-Campus</label>
                            @if($campuses->isEmpty())
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    You don't have any assigned Mtaa to record offerings for. Please contact the administrator.
                                </div>
                                <select name="campus_id" class="form-select" disabled>
                                    <option value="">No Mtaa available</option>
                                </select>
                            @else
                                <select name="campus_id" class="form-select select2" required>
                                    <option value="">-- Select Mtaa --</option>
                                    @foreach($campuses as $campus)
                                        <option value="{{ $campus->id }}">{{ $campus->name }}</option>
                                    @endforeach
                                </select>
                            @endif
                            <div class="form-text">Select the Mtaa you are collecting for.</div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-1"></i>
                            You will be recorded as the <strong>Lead Elder</strong> (Collector) for this session.
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                Proceed to Data Entry <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection