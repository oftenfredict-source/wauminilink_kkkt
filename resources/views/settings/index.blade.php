<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <title>System Settings</title>
        <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" />
        <link href="{{ asset('css/styles.css') }}" rel="stylesheet" />
    </head>
    <body class="sb-nav-fixed">
        @include('partials.nav-sidebar')
        <div id="layoutSidenav_content">
            <main class="container-fluid px-4 py-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">Membership Settings</div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        <form method="POST" action="{{ route('settings.update') }}">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Child Max Age</label>
                                    <input type="number" min="1" max="30" class="form-control" name="child_max_age" value="{{ old('child_max_age', $settings['child_max_age']) }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Age Reference</label>
                                    <select class="form-select" name="age_reference" required>
                                        <option value="today" {{ old('age_reference', $settings['age_reference'])=='today'?'selected':'' }}>Today</option>
                                        <option value="end_of_year" {{ old('age_reference', $settings['age_reference'])=='end_of_year'?'selected':'' }}>End of Year</option>
                                    </select>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end mt-3">
                                <button type="submit" class="btn btn-success"><i class="fas fa-save me-2"></i>Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
        <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    </body>
</html>



