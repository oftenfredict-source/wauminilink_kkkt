@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between mt-4 mb-3 gap-2">
        <h2 class="mb-0">Special Events</h2>
        <div class="d-flex gap-2">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEventModal"><i class="fas fa-plus me-2"></i>Add Event</button>
        </div>
    </div>

    <form method="GET" action="{{ route('special.events.index') }}" class="card mb-3" id="filtersForm">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search title, speaker, venue">
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
                            <th>Title</th>
                            <th>Speaker</th>
                            <th>Time</th>
                            <th>Venue</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($events as $event)
                            @php
                                $fmtTime = function($t){
                                    if (!$t) return '--:--';
                                    try { if (preg_match('/^\d{2}:\d{2}/',$t)) return substr($t,0,5); return \Carbon\Carbon::parse($t)->format('H:i'); } catch (\Throwable $e) { return '--:--'; }
                                };
                            @endphp
                            <tr id="row-{{ $event->id }}">
                                <td class="text-muted">{{ $events->firstItem() + $loop->index }}</td>
                                <td><span class="badge bg-secondary">{{ optional($event->event_date)->format('d/m/Y') }}</span></td>
                                <td>{{ $event->title ?? '—' }}</td>
                                <td>{{ $event->speaker ?? '—' }}</td>
                                <td>{{ $fmtTime($event->start_time) }} - {{ $fmtTime($event->end_time) }}</td>
                                <td>{{ $event->venue ?? '—' }}</td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button class="btn btn-outline-info" onclick="viewEvent({{ $event->id }})"><i class="fas fa-eye"></i></button>
                                        <button class="btn btn-outline-primary" onclick="openEditEvent({{ $event->id }})"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-outline-danger" onclick="confirmDeleteEvent({{ $event->id }})"><i class="fas fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center py-4">No special events found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between align-items-center">
            <div class="text-muted small">Showing {{ $events->firstItem() }} to {{ $events->lastItem() }} of {{ $events->total() }} entries</div>
            <div>{{ $events->withQueryString()->links() }}</div>
        </div>
    </div>
</div>

<!-- Add Event Modal -->
<div class="modal fade" id="addEventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                <h5 class="modal-title d-flex align-items-center gap-2"><i class="fas fa-star"></i><span>Add Special Event</span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light">
                <form id="addEventForm">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Event Date</label>
                            <input type="date" class="form-control" id="ev_date" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Start Time</label>
                            <input type="time" class="form-control" id="ev_start">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">End Time</label>
                            <input type="time" class="form-control" id="ev_end">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" id="ev_title" placeholder="e.g., Youth Conference">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Speaker</label>
                            <input type="text" class="form-control" id="ev_speaker" placeholder="e.g., Guest Speaker">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Venue</label>
                            <input type="text" class="form-control" id="ev_venue" placeholder="Main Hall">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Attendance</label>
                            <input type="number" min="0" class="form-control" id="ev_attendance" placeholder="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Budget (TZS)</label>
                            <input type="number" min="0" step="0.01" class="form-control" id="ev_budget" placeholder="0.00">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Category</label>
                            <input type="text" class="form-control" id="ev_category" placeholder="e.g., Conference, Concert">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" id="ev_description" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" id="ev_notes" rows="2"></textarea>
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
    function viewEvent(id){
        fetch(`{{ url('/special-events') }}/${id}`, { headers: { 'Accept': 'application/json' } })
            .then(r => { if (!r.ok) throw new Error('HTTP '+r.status); return r.json(); })
            .then(s => {
                const row = (label, value) => `<tr><td class="text-muted text-nowrap">${label}</td><td class="fw-semibold">${value || '—'}</td></tr>`;
                const fmtTime = (t) => { try { if(!t) return '--:--'; if(/^\d{2}:\d{2}/.test(t)) return t.substring(0,5); return new Date(`1970-01-01T${t}`).toISOString().substring(11,16);} catch { return '--:--'; } };
                const html = `
                    <div class="small text-uppercase text-muted mt-2 mb-1">Overview</div>
                    <table class="table table-bordered table-striped align-middle interactive-table"><tbody>
                        ${row('Event Date', (s.event_date || '').replaceAll('-', '/'))}
                        ${row('Title', s.title)}
                        ${row('Speaker', s.speaker)}
                        ${row('Time', fmtTime(s.start_time)+' - '+fmtTime(s.end_time))}
                        ${row('Venue', s.venue)}
                        ${row('Attendance', s.attendance_count)}
                        ${row('Budget (TZS)', s.budget_amount)}
                        ${row('Category', s.category)}
                    </tbody></table>
                    <div class="small text-uppercase text-muted mt-3 mb-1">Details</div>
                    <table class="table table-bordered table-striped align-middle interactive-table"><tbody>
                        ${row('Description', s.description)}
                        ${row('Notes', s.notes)}
                    </tbody></table>
                `;
                Swal.fire({ title:'Event Details', html: html, width: 900, showConfirmButton: true });
            })
            .catch(() => Swal.fire({ icon:'error', title:'Failed to load details' }));
    }

    function openEditEvent(id){
        fetch(`{{ url('/special-events') }}/${id}`, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(s => {
                document.getElementById('ev_date').value = s.event_date || '';
                document.getElementById('ev_start').value = s.start_time || '';
                document.getElementById('ev_end').value = s.end_time || '';
                document.getElementById('ev_title').value = s.title || '';
                document.getElementById('ev_speaker').value = s.speaker || '';
                document.getElementById('ev_venue').value = s.venue || '';
                document.getElementById('ev_attendance').value = s.attendance_count || '';
                document.getElementById('ev_budget').value = s.budget_amount || '';
                document.getElementById('ev_category').value = s.category || '';
                document.getElementById('ev_description').value = s.description || '';
                document.getElementById('ev_notes').value = s.notes || '';
                new bootstrap.Modal(document.getElementById('addEventModal')).show();
            });
    }

    document.getElementById('addEventForm').addEventListener('submit', function(e){
        e.preventDefault();
        const fd = new FormData();
        fd.append('event_date', document.getElementById('ev_date').value);
        fd.append('start_time', document.getElementById('ev_start').value);
        fd.append('end_time', document.getElementById('ev_end').value);
        fd.append('title', document.getElementById('ev_title').value);
        fd.append('speaker', document.getElementById('ev_speaker').value);
        fd.append('venue', document.getElementById('ev_venue').value);
        fd.append('attendance_count', document.getElementById('ev_attendance').value);
        fd.append('budget_amount', document.getElementById('ev_budget').value);
        fd.append('category', document.getElementById('ev_category').value);
        fd.append('description', document.getElementById('ev_description').value);
        fd.append('notes', document.getElementById('ev_notes').value);
        fetch(`{{ route('special.events.store') }}`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }, body: fd })
            .then(r => r.json())
            .then(res => { if(res.success){ Swal.fire({ icon:'success', title:'Saved', timer:1200, showConfirmButton:false }).then(()=>location.reload()); } else { Swal.fire({ icon:'error', title:'Failed', text: res.message || 'Try again' }); } })
            .catch(() => Swal.fire({ icon:'error', title:'Network error' }));
    });

    function confirmDeleteEvent(id){
        Swal.fire({ title:'Delete event?', text:'This action cannot be undone.', icon:'warning', showCancelButton:true, confirmButtonText:'Yes, delete', cancelButtonText:'Cancel', confirmButtonColor:'#dc3545' })
        .then((result)=>{ if(result.isConfirmed){ fetch(`{{ url('/special-events') }}/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') } })
            .then(r => r.json())
            .then(res => { if(res.success){ document.getElementById(`row-${id}`)?.remove(); Swal.fire({ icon:'success', title:'Deleted', timer:1200, showConfirmButton:false }); } else { Swal.fire({ icon:'error', title:'Delete failed', text: res.message || 'Try again' }); } })
            .catch(()=> Swal.fire({ icon:'error', title:'Error', text:'Request failed.' })); } });
    }
</script>
@endpush
@endsection



