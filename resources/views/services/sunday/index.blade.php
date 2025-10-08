@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between mt-4 mb-3 gap-2">
        <h2 class="mb-0">Sunday Services</h2>
        <div class="d-flex gap-2">
            <a href="{{ route('services.sunday.export.csv', request()->query()) }}" class="btn btn-outline-success"><i class="fas fa-file-excel me-2"></i>Export CSV</a>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addServiceModal"><i class="fas fa-plus me-2"></i>Add Service</button>
        </div>
    </div>

    <form method="GET" action="{{ route('services.sunday.index') }}" class="card mb-3" id="filtersForm">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search theme, preacher, venue">
                </div>
                <div class="col-md-3">
                    <label class="form-label">From</label>
                    <input type="date" name="from" value="{{ request('from') }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">To</label>
                    <input type="date" name="to" value="{{ request('to') }}" class="form-control">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter me-2"></i>Apply</button>
                </div>
            </div>
        </div>
    </form>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-nowrap">#</th>
                            <th>Date</th>
                            <th>Theme</th>
                            <th>Preacher</th>
                            <th>Time</th>
                            <th>Venue</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($services as $service)
                            <tr id="row-{{ $service->id }}">
                                <td class="text-muted">{{ $services->firstItem() + $loop->index }}</td>
                                <td><span class="badge bg-secondary">{{ optional($service->service_date)->format('d/m/Y') }}</span></td>
                                <td>{{ $service->theme ?? '—' }}</td>
                                <td>{{ $service->preacher ?? '—' }}</td>
                                @php
                                    $fmtTime = function($t){
                                        if (!$t) return '--:--';
                                        try {
                                            // Handle 'HH:MM:SS', 'HH:MM', or full datetime 'YYYY-MM-DD HH:MM:SS'
                                            if (preg_match('/^\d{2}:\d{2}/', $t)) {
                                                return substr($t, 0, 5);
                                            }
                                            if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}/', $t)) {
                                                return substr(substr($t, 11), 0, 5);
                                            }
                                            // Fallback via Carbon
                                            return \Carbon\Carbon::parse($t)->format('H:i');
                                        } catch (\Throwable $e) {
                                            return '--:--';
                                        }
                                    };
                                @endphp
                                <td>{{ $fmtTime($service->start_time) }} - {{ $fmtTime($service->end_time) }}</td>
                                <td>{{ $service->venue ?? '—' }}</td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button class="btn btn-outline-info" onclick="viewService({{ $service->id }})"><i class="fas fa-eye"></i></button>
                                        <button class="btn btn-outline-primary" onclick="openEditService({{ $service->id }})"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-outline-danger" onclick="confirmDeleteService({{ $service->id }})"><i class="fas fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center py-4">No services found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between align-items-center">
            <div class="text-muted small">Showing {{ $services->firstItem() }} to {{ $services->lastItem() }} of {{ $services->total() }} entries</div>
            <div>{{ $services->withQueryString()->links() }}</div>
        </div>
    </div>
</div>

<!-- Add Service Modal -->
<div class="modal fade" id="addServiceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 25px; overflow: hidden; animation: modalSlideIn 0.3s ease-out;">
            <!-- Enhanced Header with Gradient and Icons -->
            <div class="modal-header text-white position-relative" style="background: linear-gradient(135deg, #940000 0%, #667eea 50%, #764ba2 100%); border: none; padding: 2rem;">
                <div class="d-flex align-items-center">
                    <div class="bg-white bg-opacity-20 rounded-circle p-3 me-3">
                        <i class="fas fa-church fa-2x"></i>
                    </div>
                    <div>
                        <h4 class="modal-title mb-1 fw-bold">Create Sunday Service</h4>
                        <p class="mb-0 opacity-75">Plan and organize your weekly church services</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <!-- Enhanced Body with Better Layout -->
            <div class="modal-body" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); padding: 2rem;">
                <form id="addServiceForm">
                    
                    <!-- Service Basic Information Section -->
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-header bg-white border-0" style="border-radius: 15px 15px 0 0;">
                            <h6 class="mb-0 text-primary fw-bold">
                                <i class="fas fa-info-circle me-2"></i>Service Information
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="svc_theme" placeholder="Service Theme" style="border-radius: 10px; border: 2px solid #e9ecef; transition: all 0.3s ease;">
                                        <label for="svc_theme" class="text-muted">
                                            <i class="fas fa-star me-1"></i>Service Theme
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="svc_preacher" placeholder="Preacher Name" style="border-radius: 10px; border: 2px solid #e9ecef; transition: all 0.3s ease;">
                                        <label for="svc_preacher" class="text-muted">
                                            <i class="fas fa-user-tie me-1"></i>Preacher
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="svc_venue" placeholder="Venue Location" style="border-radius: 10px; border: 2px solid #e9ecef; transition: all 0.3s ease;">
                                        <label for="svc_venue" class="text-muted">
                                            <i class="fas fa-map-marker-alt me-1"></i>Venue
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="svc_choir" placeholder="Choir Name" style="border-radius: 10px; border: 2px solid #e9ecef; transition: all 0.3s ease;">
                                        <label for="svc_choir" class="text-muted">
                                            <i class="fas fa-music me-1"></i>Choir
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Date & Time Section -->
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-header bg-white border-0" style="border-radius: 15px 15px 0 0;">
                            <h6 class="mb-0 text-primary fw-bold">
                                <i class="fas fa-clock me-2"></i>Date & Time
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input type="date" class="form-control" id="svc_date" required style="border-radius: 10px; border: 2px solid #e9ecef; transition: all 0.3s ease;">
                                        <label for="svc_date" class="text-muted">
                                            <i class="fas fa-calendar-alt me-1"></i>Service Date
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input type="time" class="form-control" id="svc_start" style="border-radius: 10px; border: 2px solid #e9ecef; transition: all 0.3s ease;">
                                        <label for="svc_start" class="text-muted">
                                            <i class="fas fa-play me-1"></i>Start Time
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input type="time" class="form-control" id="svc_end" style="border-radius: 10px; border: 2px solid #e9ecef; transition: all 0.3s ease;">
                                        <label for="svc_end" class="text-muted">
                                            <i class="fas fa-stop me-1"></i>End Time
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Attendance & Offerings Section -->
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-header bg-white border-0" style="border-radius: 15px 15px 0 0;">
                            <h6 class="mb-0 text-primary fw-bold">
                                <i class="fas fa-chart-line me-2"></i>Attendance & Offerings
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="number" min="0" class="form-control" id="svc_attendance" placeholder="Expected Attendance" style="border-radius: 10px; border: 2px solid #e9ecef; transition: all 0.3s ease;">
                                        <label for="svc_attendance" class="text-muted">
                                            <i class="fas fa-users me-1"></i>Attendance
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="number" min="0" step="0.01" class="form-control" id="svc_offerings" placeholder="Offerings Amount" style="border-radius: 10px; border: 2px solid #e9ecef; transition: all 0.3s ease;">
                                        <label for="svc_offerings" class="text-muted">
                                            <i class="fas fa-money-bill-wave me-1"></i>Offerings (TZS)
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Scripture & Content Section -->
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-header bg-white border-0" style="border-radius: 15px 15px 0 0;">
                            <h6 class="mb-0 text-primary fw-bold">
                                <i class="fas fa-bible me-2"></i>Scripture & Content
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-12">
                                    <div class="form-floating">
                                        <textarea class="form-control" id="svc_readings" placeholder="Scripture Readings" style="height: 100px; border-radius: 10px; border: 2px solid #e9ecef; transition: all 0.3s ease; resize: none;"></textarea>
                                        <label for="svc_readings" class="text-muted">
                                            <i class="fas fa-book-open me-1"></i>Scripture Readings
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Announcements & Notes Section -->
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-header bg-white border-0" style="border-radius: 15px 15px 0 0;">
                            <h6 class="mb-0 text-primary fw-bold">
                                <i class="fas fa-bullhorn me-2"></i>Announcements & Notes
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-12">
                                    <div class="form-floating">
                                        <textarea class="form-control" id="svc_announcements" placeholder="Service Announcements" style="height: 100px; border-radius: 10px; border: 2px solid #e9ecef; transition: all 0.3s ease; resize: none;"></textarea>
                                        <label for="svc_announcements" class="text-muted">
                                            <i class="fas fa-bullhorn me-1"></i>Announcements
                                        </label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <textarea class="form-control" id="svc_notes" placeholder="Additional Notes" style="height: 100px; border-radius: 10px; border: 2px solid #e9ecef; transition: all 0.3s ease; resize: none;"></textarea>
                                        <label for="svc_notes" class="text-muted">
                                            <i class="fas fa-sticky-note me-1"></i>Notes
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Enhanced Action Buttons -->
                    <div class="d-flex justify-content-end gap-3 mt-4">
                        <button type="button" class="btn btn-outline-secondary px-4 py-2" data-bs-dismiss="modal" style="border-radius: 25px; font-weight: 600; transition: all 0.3s ease;">
                            <i class="fas fa-times me-2"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-primary px-4 py-2" style="border-radius: 25px; font-weight: 600; background: linear-gradient(135deg, #940000 0%, #667eea 100%); border: none; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(148, 0, 0, 0.3);">
                            <i class="fas fa-save me-2"></i>Save Service
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    /* Enhanced Modal Animations */
    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: translateY(-50px) scale(0.9);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    /* Form Control Focus Effects */
    .form-control:focus {
        border-color: #940000 !important;
        box-shadow: 0 0 0 0.2rem rgba(148, 0, 0, 0.25) !important;
        transform: translateY(-2px);
    }

    .form-control:hover {
        border-color: #667eea !important;
        transform: translateY(-1px);
    }

    /* Button Hover Effects */
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    }

    /* Card Hover Effects */
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
    }

    /* Floating Label Animation */
    .form-floating > .form-control:focus ~ label,
    .form-floating > .form-control:not(:placeholder-shown) ~ label {
        color: #940000;
        font-weight: 600;
    }

    /* Modal Backdrop */
    .modal-backdrop {
        background: linear-gradient(135deg, rgba(148, 0, 0, 0.1) 0%, rgba(102, 126, 234, 0.1) 100%);
    }
</style>

<!-- View Modal -->
<div class="modal fade" id="serviceDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 18px; overflow: hidden;">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #1f2b6c 0%, #5b2a86 100%); border: none;">
                <h5 class="modal-title d-flex align-items-center gap-2"><i class="fas fa-info-circle"></i><span>Service Details</span></h5>
                <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light" id="serviceDetailsBody">
                <div class="text-center text-muted py-4">Loading...</div>
            </div>
            <div class="modal-footer d-flex justify-content-between align-items-center">
                <div class="small">
                    <span class="me-1">Powered by</span>
                    <a href="https://emca.tech/#" target="_blank" rel="noopener" class="emca-link fw-semibold">EmCa Technologies</a>
                </div>
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editServiceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-white border-0">
                <h6 class="modal-title d-flex align-items-center gap-2"><i class="fas fa-edit text-primary"></i><span>Edit Sunday Service</span></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editServiceForm">
                    <input type="hidden" id="edit_id">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Service Date</label>
                            <input type="date" class="form-control" id="edit_date" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Start Time</label>
                            <input type="time" class="form-control" id="edit_start">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">End Time</label>
                            <input type="time" class="form-control" id="edit_end">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Theme</label>
                            <input type="text" class="form-control" id="edit_theme">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Preacher</label>
                            <input type="text" class="form-control" id="edit_preacher">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Venue</label>
                            <input type="text" class="form-control" id="edit_venue">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Attendance</label>
                            <input type="number" min="0" class="form-control" id="edit_attendance">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Offerings (TZS)</label>
                            <input type="number" min="0" step="0.01" class="form-control" id="edit_offerings">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Scripture Readings</label>
                            <textarea class="form-control" id="edit_readings" rows="2"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Choir</label>
                            <input type="text" class="form-control" id="edit_choir">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Announcements</label>
                            <textarea class="form-control" id="edit_announcements" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" id="edit_notes" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function viewService(id){
        fetch(`{{ url('/services/sunday') }}/${id}`, { headers: { 'Accept': 'application/json' } })
            .then(r => { if (!r.ok) throw new Error('HTTP '+r.status); return r.json(); })
            .then(s => {
                const row = (label, value) => `<tr><td class="text-muted text-nowrap">${label}</td><td class="fw-semibold">${value || '—'}</td></tr>`;
                const html = `
                    <div class="small text-uppercase text-muted mt-2 mb-1">Overview</div>
                    <table class="table table-bordered table-striped align-middle interactive-table"><tbody>
                        ${row('Service Date', (s.service_date || '').replaceAll('-', '/'))}
                        ${row('Theme', s.theme)}
                        ${row('Preacher', s.preacher)}
                        ${row('Time', (s.start_time||'--:--') + ' - ' + (s.end_time||'--:--'))}
                        ${row('Venue', s.venue)}
                        ${row('Attendance', s.attendance_count)}
                        ${row('Offerings (TZS)', s.offerings_amount)}
                    </tbody></table>
                    <div class="small text-uppercase text-muted mt-3 mb-1">Details</div>
                    <table class="table table-bordered table-striped align-middle interactive-table"><tbody>
                        ${row('Scripture Readings', s.scripture_readings)}
                        ${row('Choir', s.choir)}
                        ${row('Announcements', s.announcements)}
                        ${row('Notes', s.notes)}
                    </tbody></table>
                `;
                document.getElementById('serviceDetailsBody').innerHTML = html;
                new bootstrap.Modal(document.getElementById('serviceDetailsModal')).show();
            })
            .catch(() => {
                document.getElementById('serviceDetailsBody').innerHTML = '<div class="text-danger">Failed to load details.</div>';
                new bootstrap.Modal(document.getElementById('serviceDetailsModal')).show();
            });
    }

    function openEditService(id){
        fetch(`{{ url('/services/sunday') }}/${id}`, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(s => {
                document.getElementById('edit_id').value = s.id;
                document.getElementById('edit_date').value = (s.service_date || '');
                document.getElementById('edit_start').value = (s.start_time || '');
                document.getElementById('edit_end').value = (s.end_time || '');
                document.getElementById('edit_theme').value = s.theme || '';
                document.getElementById('edit_preacher').value = s.preacher || '';
                document.getElementById('edit_venue').value = s.venue || '';
                document.getElementById('edit_attendance').value = s.attendance_count || '';
                document.getElementById('edit_offerings').value = s.offerings_amount || '';
                document.getElementById('edit_readings').value = s.scripture_readings || '';
                document.getElementById('edit_choir').value = s.choir || '';
                document.getElementById('edit_announcements').value = s.announcements || '';
                document.getElementById('edit_notes').value = s.notes || '';
                new bootstrap.Modal(document.getElementById('editServiceModal')).show();
            });
    }

    document.getElementById('addServiceForm').addEventListener('submit', function(e){
        e.preventDefault();
        const fd = new FormData();
        fd.append('service_date', document.getElementById('svc_date').value);
        fd.append('start_time', document.getElementById('svc_start').value);
        fd.append('end_time', document.getElementById('svc_end').value);
        fd.append('theme', document.getElementById('svc_theme').value);
        fd.append('preacher', document.getElementById('svc_preacher').value);
        fd.append('venue', document.getElementById('svc_venue').value);
        fd.append('attendance_count', document.getElementById('svc_attendance').value);
        fd.append('offerings_amount', document.getElementById('svc_offerings').value);
        fd.append('scripture_readings', document.getElementById('svc_readings').value);
        fd.append('choir', document.getElementById('svc_choir').value);
        fd.append('announcements', document.getElementById('svc_announcements').value);
        fd.append('notes', document.getElementById('svc_notes').value);
        fetch(`{{ route('services.sunday.store') }}`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }, body: fd })
            .then(r => r.json())
            .then(res => { if(res.success){ Swal.fire({ icon:'success', title:'Saved', timer:1200, showConfirmButton:false }).then(()=>location.reload()); } else { Swal.fire({ icon:'error', title:'Failed', text: res.message || 'Try again' }); } })
            .catch(() => Swal.fire({ icon:'error', title:'Network error' }));
    });

    document.getElementById('editServiceForm').addEventListener('submit', function(e){
        e.preventDefault();
        const id = document.getElementById('edit_id').value;
        const fd = new FormData();
        fd.append('service_date', document.getElementById('edit_date').value);
        fd.append('start_time', document.getElementById('edit_start').value);
        fd.append('end_time', document.getElementById('edit_end').value);
        fd.append('theme', document.getElementById('edit_theme').value);
        fd.append('preacher', document.getElementById('edit_preacher').value);
        fd.append('venue', document.getElementById('edit_venue').value);
        fd.append('attendance_count', document.getElementById('edit_attendance').value);
        fd.append('offerings_amount', document.getElementById('edit_offerings').value);
        fd.append('scripture_readings', document.getElementById('edit_readings').value);
        fd.append('choir', document.getElementById('edit_choir').value);
        fd.append('announcements', document.getElementById('edit_announcements').value);
        fd.append('notes', document.getElementById('edit_notes').value);
        fd.append('_method', 'PUT');
        fetch(`{{ url('/services/sunday') }}/${id}`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }, body: fd })
            .then(r => r.json())
            .then(res => { if(res.success){ Swal.fire({ icon:'success', title:'Saved', timer:1200, showConfirmButton:false }).then(()=>location.reload()); } else { Swal.fire({ icon:'error', title:'Failed', text: res.message || 'Try again' }); } })
            .catch(() => Swal.fire({ icon:'error', title:'Network error' }));
    });

    function confirmDeleteService(id){
        Swal.fire({ title:'Delete service?', text:'This action cannot be undone.', icon:'warning', showCancelButton:true, confirmButtonText:'Yes, delete', cancelButtonText:'Cancel', confirmButtonColor:'#dc3545' })
        .then((result)=>{ if(result.isConfirmed){ fetch(`{{ url('/services/sunday') }}/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') } })
            .then(r => r.json())
            .then(res => { if(res.success){ document.getElementById(`row-${id}`)?.remove(); Swal.fire({ icon:'success', title:'Deleted', timer:1200, showConfirmButton:false }); } else { Swal.fire({ icon:'error', title:'Delete failed', text: res.message || 'Try again' }); } })
            .catch(()=> Swal.fire({ icon:'error', title:'Error', text:'Request failed.' })); } });
    }
</script>
@endpush
@endsection


