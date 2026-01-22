@extends('layouts.index')

@section('content')
<style>
    /* Mobile Responsive Styles */
    @media (max-width: 767.98px) {
        .container-fluid {
            padding-left: 0.75rem !important;
            padding-right: 0.75rem !important;
        }
        
        /* Page Header - Stack on mobile */
        .page-header-mobile {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 1rem;
        }
        
        .page-header-mobile h2 {
            font-size: 1.5rem !important;
            margin-bottom: 0 !important;
        }
        
        .page-header-mobile .btn-group-mobile {
            display: flex;
            flex-direction: column;
            width: 100%;
            gap: 0.5rem;
        }
        
        .page-header-mobile .btn-group-mobile .btn {
            width: 100%;
            justify-content: center;
        }
        
        /* Filter Form - Stack on mobile */
        .filter-form .row > div {
            margin-bottom: 0.75rem;
        }
        
        .filter-form .btn {
            width: 100%;
        }
        
        /* Compact Filter Section Styles */
        #filtersForm {
            transition: all 0.3s ease;
        }
        #filtersForm .card-header {
            transition: background-color 0.2s ease;
        }
        #filterBody {
            transition: all 0.3s ease;
        }
        
        /* Desktop: Always show filters, make header non-clickable */
        @media (min-width: 769px) {
            .filter-header {
                cursor: default !important;
                pointer-events: none !important;
            }
            .filter-header .fa-chevron-down {
                display: none !important;
            }
            #filterBody {
                display: block !important;
            }
        }
        
        /* Mobile: Collapsible */
        @media (max-width: 768px) {
            .filter-header {
                cursor: pointer !important;
                pointer-events: auto !important;
            }
            #filterBody {
                display: none;
            }
            #filterToggleIcon {
                font-size: 1.1rem !important;
                width: 24px !important;
                height: 24px !important;
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
                cursor: pointer !important;
                transition: transform 0.3s ease !important;
                flex-shrink: 0 !important;
            }
        }
        
        /* Table - Horizontal scroll on mobile */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .table {
            font-size: 0.875rem !important;
            min-width: 700px;
        }
        
        .table th,
        .table td {
            padding: 0.5rem !important;
            white-space: nowrap;
        }
        
        .table th:first-child,
        .table td:first-child {
            position: sticky;
            left: 0;
            background-color: inherit;
            z-index: 1;
        }
        
        /* Button groups - Stack on mobile */
        .btn-group-sm {
            flex-direction: column;
            width: 100%;
        }
        
        .btn-group-sm .btn {
            width: 100%;
            margin-bottom: 0.25rem;
        }
        
        /* Cards - Better spacing on mobile */
        .card {
            margin-bottom: 1rem !important;
        }
        
        .card-body {
            padding: 0.75rem !important;
        }
        
        .card-footer {
            flex-direction: column;
            gap: 0.5rem;
            align-items: flex-start !important;
        }
        
        /* Modals - Full width on mobile */
        .modal-dialog {
            margin: 0.5rem;
            max-width: calc(100% - 1rem);
        }
        
        .modal-content {
            border-radius: 0.5rem !important;
        }
        
        .modal-body {
            padding: 1rem !important;
        }
        
        .modal-body .row > div {
            margin-bottom: 0.75rem;
        }
        
        /* Badge adjustments */
        .badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
    }
    
    @media (max-width: 575.98px) {
        .container-fluid {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
        }
        
        .page-header-mobile h2 {
            font-size: 1.25rem !important;
        }
        
        .table {
            font-size: 0.75rem !important;
        }
        
        .btn {
            font-size: 0.875rem !important;
            padding: 0.375rem 0.75rem !important;
        }
        
        .btn i {
            margin-right: 0.25rem !important;
        }
        
        .modal-dialog {
            margin: 0.25rem;
            max-width: calc(100% - 0.5rem);
        }
    }
</style>

<div class="container-fluid px-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between mt-4 mb-3 gap-2 page-header-mobile">
        <h2 class="mb-0">{{ autoTranslate('Sunday Services') }}</h2>
        <div class="d-flex flex-wrap gap-2 btn-group-mobile">
            <a href="{{ route('services.sunday.export.csv', request()->query()) }}" class="btn btn-outline-success btn-sm">
                <i class="fas fa-file-excel me-2"></i><span class="d-none d-sm-inline">{{ autoTranslate('Export') }} </span>{{ autoTranslate('CSV') }}
            </a>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addServiceModal">
                <i class="fas fa-plus me-2"></i>{{ autoTranslate('Add Service') }}
            </button>
        </div>
    </div>

    <!-- Filters & Search - Collapsible on Mobile -->
    <form method="GET" action="{{ route('services.sunday.index') }}" class="card mb-3 border-0 shadow-sm" id="filtersForm">
        <!-- Filter Header -->
        <div class="card-header bg-white border-bottom p-2 px-3 filter-header" onclick="toggleFilters()">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-filter text-primary"></i>
                    <span class="fw-semibold">{{ autoTranslate('Filters') }}</span>
                    @if(request('search') || request('from') || request('to'))
                        <span class="badge bg-primary rounded-pill" id="activeFiltersCount">{{ (request('search') ? 1 : 0) + (request('from') ? 1 : 0) + (request('to') ? 1 : 0) }}</span>
                    @endif
                </div>
                <i class="fas fa-chevron-down text-muted d-md-none" id="filterToggleIcon"></i>
            </div>
        </div>
        
        <!-- Filter Body - Collapsible on Mobile -->
        <div class="card-body p-3" id="filterBody">
            <div class="row g-2 mb-2">
                <!-- Search Field - Full Width on Mobile -->
                <div class="col-12 col-md-4">
                    <label class="form-label small text-muted mb-1">
                        <i class="fas fa-search me-1 text-primary"></i>{{ autoTranslate('Search') }}
                    </label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="{{ autoTranslate('Search theme, preacher, venue') }}">
                </div>
                
                <!-- Date Range - Side by Side on Mobile -->
                <div class="col-6 col-md-3">
                    <label class="form-label small text-muted mb-1">
                        <i class="fas fa-calendar-alt me-1 text-info"></i>{{ autoTranslate('From Date') }}
                    </label>
                    <input type="date" name="from" value="{{ request('from') }}" class="form-control form-control-sm">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small text-muted mb-1">
                        <i class="fas fa-calendar-check me-1 text-info"></i>{{ autoTranslate('To Date') }}
                    </label>
                    <input type="date" name="to" value="{{ request('to') }}" class="form-control form-control-sm">
                </div>
                
                <!-- Apply Button - Full Width on Mobile -->
                <div class="col-12 col-md-2">
                    <label class="form-label small text-muted mb-1 d-block">&nbsp;</label>
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="fas fa-filter me-1"></i>
                        <span class="d-none d-sm-inline">{{ autoTranslate('Apply') }}</span>
                        <span class="d-sm-none">{{ autoTranslate('Filter') }}</span>
                    </button>
                </div>
            </div>
            
            <!-- Action Buttons - Compact, Full Width on Mobile -->
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('services.sunday.index') }}" class="btn btn-outline-secondary btn-sm flex-fill flex-md-grow-0">
                    <i class="fas fa-redo me-1"></i>
                    <span class="d-none d-sm-inline">{{ autoTranslate('Reset') }}</span>
                    <span class="d-sm-none">{{ autoTranslate('Clear') }}</span>
                </a>
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
                            <th>{{ autoTranslate('Date') }}</th>
                            <th>{{ autoTranslate('Theme') }}</th>
                            <th>{{ autoTranslate('Preacher') }}</th>
                            <th>{{ autoTranslate('Time') }}</th>
                            <th>{{ autoTranslate('Venue') }}</th>
                            <th class="text-end">{{ autoTranslate('Actions') }}</th>
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
                                        <button class="btn btn-outline-info" onclick="viewService({{ $service->id }})" title="{{ autoTranslate('View') }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-primary" onclick="openEditService({{ $service->id }})" title="{{ autoTranslate('Edit') }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" onclick="confirmDeleteService({{ $service->id }})" title="{{ autoTranslate('Delete') }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center py-4">{{ autoTranslate('No services found.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between align-items-center flex-wrap">
            <div class="text-muted small mb-2 mb-md-0">{{ autoTranslate('Showing') }} {{ $services->firstItem() }} {{ autoTranslate('to') }} {{ $services->lastItem() }} {{ autoTranslate('of') }} {{ $services->total() }} {{ autoTranslate('entries') }}</div>
            <div>{{ $services->withQueryString()->links() }}</div>
        </div>
    </div>
</div>

<!-- Add Service Modal -->
<div class="modal fade" id="addServiceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                <h5 class="modal-title d-flex align-items-center gap-2"><i class="fas fa-church"></i><span>{{ autoTranslate('Add Sunday Service') }}</span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light">
                <form id="addServiceForm">
                    <div class="row g-3">
                        <div class="col-md-6 col-12">
                            <label class="form-label">{{ autoTranslate('Service Theme') }}</label>
                            <input type="text" class="form-control" id="svc_theme" placeholder="{{ autoTranslate('Enter service theme') }}">
                        </div>
                        <div class="col-md-6 col-12">
                            <label class="form-label">{{ autoTranslate('Preacher') }}</label>
                            <input type="text" class="form-control" id="svc_preacher" placeholder="{{ autoTranslate('Enter preacher name') }}">
                        </div>
                        <div class="col-md-6 col-12">
                            <label class="form-label">{{ autoTranslate('Venue') }}</label>
                            <input type="text" class="form-control" id="svc_venue" placeholder="{{ autoTranslate('Enter venue location') }}">
                        </div>
                        <div class="col-md-6 col-12">
                            <label class="form-label">{{ autoTranslate('Choir') }}</label>
                            <input type="text" class="form-control" id="svc_choir" placeholder="{{ autoTranslate('Enter choir name') }}">
                        </div>
                        <div class="col-md-4 col-12">
                            <label class="form-label">{{ autoTranslate('Service Date') }} <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="svc_date" required>
                        </div>
                        <div class="col-md-4 col-6">
                            <label class="form-label">{{ autoTranslate('Start Time') }}</label>
                            <input type="time" class="form-control" id="svc_start">
                        </div>
                        <div class="col-md-4 col-6">
                            <label class="form-label">{{ autoTranslate('End Time') }}</label>
                            <input type="time" class="form-control" id="svc_end">
                        </div>
                        <div class="col-md-6 col-12">
                            <label class="form-label">{{ autoTranslate('Attendance') }}</label>
                            <input type="number" min="0" class="form-control" id="svc_attendance" placeholder="{{ autoTranslate('Enter attendance count') }}">
                        </div>
                        <div class="col-md-6 col-12">
                            <label class="form-label">{{ autoTranslate('Offerings (TZS)') }}</label>
                            <input type="number" min="0" step="0.01" class="form-control" id="svc_offerings" placeholder="{{ autoTranslate('Enter offerings amount') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">{{ autoTranslate('Scripture Readings') }}</label>
                            <textarea class="form-control" id="svc_readings" placeholder="{{ autoTranslate('Enter scripture readings...') }}" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">{{ autoTranslate('Notes') }}</label>
                            <textarea class="form-control" id="svc_notes" placeholder="{{ autoTranslate('Enter additional notes...') }}" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="d-flex flex-column flex-sm-row justify-content-end gap-2 mt-3">
                        <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">{{ autoTranslate('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ autoTranslate('Save Service') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- View Modal -->
<div class="modal fade" id="serviceDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 18px; overflow: hidden;">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #1f2b6c 0%, #5b2a86 100%); border: none;">
                <h5 class="modal-title d-flex align-items-center gap-2"><i class="fas fa-info-circle"></i><span>{{ autoTranslate('Service Details') }}</span></h5>
                <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light" id="serviceDetailsBody">
                <div class="text-center text-muted py-4">{{ autoTranslate('Loading...') }}</div>
            </div>
            <div class="modal-footer d-flex justify-content-between align-items-center">
                <div class="small">
                    <span class="me-1">Powered by</span>
                    <a href="https://emca.tech/#" target="_blank" rel="noopener" class="emca-link fw-semibold" style="color: #940000 !important;">EmCa Technologies</a>
                </div>
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{ autoTranslate('Close') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editServiceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-white border-0">
                <h6 class="modal-title d-flex align-items-center gap-2"><i class="fas fa-edit text-primary"></i><span>{{ autoTranslate('Edit Sunday Service') }}</span></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editServiceForm">
                    <input type="hidden" id="edit_id">
                    <div class="row g-3">
                        <div class="col-md-4 col-12">
                            <label class="form-label">{{ autoTranslate('Service Date') }}</label>
                            <input type="date" class="form-control" id="edit_date" required>
                        </div>
                        <div class="col-md-4 col-6">
                            <label class="form-label">{{ autoTranslate('Start Time') }}</label>
                            <input type="time" class="form-control" id="edit_start">
                        </div>
                        <div class="col-md-4 col-6">
                            <label class="form-label">{{ autoTranslate('End Time') }}</label>
                            <input type="time" class="form-control" id="edit_end">
                        </div>
                        <div class="col-md-6 col-12">
                            <label class="form-label">{{ autoTranslate('Theme') }}</label>
                            <input type="text" class="form-control" id="edit_theme">
                        </div>
                        <div class="col-md-6 col-12">
                            <label class="form-label">{{ autoTranslate('Preacher') }}</label>
                            <input type="text" class="form-control" id="edit_preacher">
                        </div>
                        <div class="col-md-6 col-12">
                            <label class="form-label">{{ autoTranslate('Venue') }}</label>
                            <input type="text" class="form-control" id="edit_venue">
                        </div>
                        <div class="col-md-3 col-6">
                            <label class="form-label">{{ autoTranslate('Attendance') }}</label>
                            <input type="number" min="0" class="form-control" id="edit_attendance">
                        </div>
                        <div class="col-md-3 col-6">
                            <label class="form-label">{{ autoTranslate('Offerings (TZS)') }}</label>
                            <input type="number" min="0" step="0.01" class="form-control" id="edit_offerings">
                        </div>
                        <div class="col-12">
                            <label class="form-label">{{ autoTranslate('Scripture Readings') }}</label>
                            <textarea class="form-control" id="edit_readings" rows="2"></textarea>
                        </div>
                        <div class="col-md-6 col-12">
                            <label class="form-label">{{ autoTranslate('Choir') }}</label>
                            <input type="text" class="form-control" id="edit_choir">
                        </div>
                        <div class="col-12">
                            <label class="form-label">{{ autoTranslate('Notes') }}</label>
                            <textarea class="form-control" id="edit_notes" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="d-flex flex-column flex-sm-row justify-content-end gap-2 mt-3">
                        <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">{{ autoTranslate('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ autoTranslate('Save') }}</button>
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
                    <div class="small text-uppercase text-muted mt-2 mb-1">{{ autoTranslate('Overview') }}</div>
                    <table class="table table-bordered table-striped align-middle interactive-table"><tbody>
                        ${row('{{ autoTranslate('Service Date') }}', (s.service_date || '').replaceAll('-', '/'))}
                        ${row('{{ autoTranslate('Theme') }}', s.theme)}
                        ${row('{{ autoTranslate('Preacher') }}', s.preacher)}
                        ${row('{{ autoTranslate('Time') }}', (s.start_time||'--:--') + ' - ' + (s.end_time||'--:--'))}
                        ${row('{{ autoTranslate('Venue') }}', s.venue)}
                        ${row('{{ autoTranslate('Attendance') }}', s.attendance_count)}
                        ${row('{{ autoTranslate('Offerings (TZS)') }}', s.offerings_amount)}
                    </tbody></table>
                    <div class="small text-uppercase text-muted mt-3 mb-1">{{ autoTranslate('Details') }}</div>
                    <table class="table table-bordered table-striped align-middle interactive-table"><tbody>
                        ${row('{{ autoTranslate('Scripture Readings') }}', s.scripture_readings)}
                        ${row('{{ autoTranslate('Choir') }}', s.choir)}
                        ${row('{{ autoTranslate('Notes') }}', s.notes)}
                    </tbody></table>
                `;
                document.getElementById('serviceDetailsBody').innerHTML = html;
                new bootstrap.Modal(document.getElementById('serviceDetailsModal')).show();
            })
            .catch(() => {
                document.getElementById('serviceDetailsBody').innerHTML = '<div class="text-danger">{{ autoTranslate('Failed to load details.') }}</div>';
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
        fd.append('notes', document.getElementById('svc_notes').value);
        fetch(`{{ route('services.sunday.store') }}`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }, body: fd })
            .then(r => r.json())
            .then(res => { if(res.success){ Swal.fire({ icon:'success', title:'{{ autoTranslate('Saved') }}', timer:1200, showConfirmButton:false }).then(()=>location.reload()); } else { Swal.fire({ icon:'error', title:'{{ autoTranslate('Failed') }}', text: res.message || '{{ autoTranslate('Try again') }}' }); } })
            .catch(() => Swal.fire({ icon:'error', title:'{{ autoTranslate('Network error') }}' }));
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
        fd.append('notes', document.getElementById('edit_notes').value);
        fd.append('_method', 'PUT');
        fetch(`{{ url('/services/sunday') }}/${id}`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }, body: fd })
            .then(r => r.json())
            .then(res => { if(res.success){ Swal.fire({ icon:'success', title:'{{ autoTranslate('Saved') }}', timer:1200, showConfirmButton:false }).then(()=>location.reload()); } else { Swal.fire({ icon:'error', title:'{{ autoTranslate('Failed') }}', text: res.message || '{{ autoTranslate('Try again') }}' }); } })
            .catch(() => Swal.fire({ icon:'error', title:'{{ autoTranslate('Network error') }}' }));
    });

    function confirmDeleteService(id){
        Swal.fire({ title:'{{ autoTranslate('Delete service?') }}', text:'{{ autoTranslate('This action cannot be undone.') }}', icon:'warning', showCancelButton:true, confirmButtonText:'{{ autoTranslate('Yes, delete') }}', cancelButtonText:'{{ autoTranslate('Cancel') }}', confirmButtonColor:'#dc3545' })
        .then((result)=>{ if(result.isConfirmed){ fetch(`{{ url('/services/sunday') }}/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') } })
            .then(r => r.json())
            .then(res => { if(res.success){ document.getElementById(`row-${id}`)?.remove(); Swal.fire({ icon:'success', title:'{{ autoTranslate('Deleted') }}', timer:1200, showConfirmButton:false }); } else { Swal.fire({ icon:'error', title:'{{ autoTranslate('Delete failed') }}', text: res.message || '{{ autoTranslate('Try again') }}' }); } })
            .catch(()=> Swal.fire({ icon:'error', title:'{{ autoTranslate('Error') }}', text:'{{ autoTranslate('Request failed.') }}' })); } });
    }
    
    // Toggle Filters Function
    function toggleFilters() {
        // Only toggle on mobile devices
        if (window.innerWidth > 768) {
            return; // Don't toggle on desktop
        }
        
        const filterBody = document.getElementById('filterBody');
        const filterIcon = document.getElementById('filterToggleIcon');
        const filterHeader = document.querySelector('.filter-header');
        
        if (!filterBody || !filterIcon) return;
        
        // Check computed style to see if it's visible
        const computedStyle = window.getComputedStyle(filterBody);
        const isVisible = computedStyle.display !== 'none';
        
        if (isVisible) {
            filterBody.style.display = 'none';
            filterIcon.classList.remove('fa-chevron-up');
            filterIcon.classList.add('fa-chevron-down');
            if (filterHeader) filterHeader.classList.remove('active');
        } else {
            filterBody.style.display = 'block';
            filterIcon.classList.remove('fa-chevron-down');
            filterIcon.classList.add('fa-chevron-up');
            if (filterHeader) filterHeader.classList.add('active');
        }
    }
    
    // Auto-expand filters on mobile if filters are active
    document.addEventListener('DOMContentLoaded', function() {
        if (window.innerWidth <= 768) {
            const hasActiveFilters = {{ (request('search') || request('from') || request('to')) ? 'true' : 'false' }};
            if (hasActiveFilters) {
                toggleFilters(); // Expand if filters are active
            }
        }
        
        // Handle window resize
        window.addEventListener('resize', function() {
            const filterBody = document.getElementById('filterBody');
            const filterIcon = document.getElementById('filterToggleIcon');
            
            if (window.innerWidth > 768) {
                // Always show on desktop
                if (filterBody && filterIcon) {
                    filterBody.style.display = 'block';
                    filterIcon.style.display = 'none';
                }
            } else {
                // On mobile, show chevrons
                if (filterIcon) filterIcon.style.display = 'block';
            }
        });
    });
</script>
@endpush
@endsection


