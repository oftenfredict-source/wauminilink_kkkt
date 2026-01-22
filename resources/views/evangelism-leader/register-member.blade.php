@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h1 class="h3 mb-0"><i class="fas fa-user-plus me-2 text-primary"></i>Register Member</h1>
                    <p class="text-muted mb-0">Redirecting to member registration...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Redirect to members.add with campus_id
    window.location.href = "{{ route('members.add', ['campus_id' => request('campus_id')]) }}";
</script>
@endsection




