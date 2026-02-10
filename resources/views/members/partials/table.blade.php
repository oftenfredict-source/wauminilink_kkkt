<div class="table-responsive">
  <table class="table table-striped align-middle">
    <thead>
      <tr>
        <th>{{ __('common.member_id') }}</th>
        <th>{{ __('common.full_name') }}</th>
        <th>{{ __('common.phone') }}</th>
        <th>{{ __('common.email') }}</th>
        <th>{{ __('common.gender') }}</th>
        <th>{{ __('common.region') }}</th>
        <th>{{ __('common.actions') }}</th>
      </tr>
    </thead>
    <tbody>
      @forelse($members as $m)
      <tr>
        <td>{{ $m->member_id }}</td>
        <td>{{ $m->full_name }}</td>
        <td>{{ $m->phone_number }}</td>
        <td>{{ $m->email }}</td>
        <td>{{ ucfirst($m->gender) }}</td>
        <td>{{ $m->region }}</td>
        <td>
          <button class="btn btn-sm btn-outline-danger" onclick="archiveMember({{ $m->id }})">{{ __('common.archive') }}</button>
        </td>
      </tr>
      @empty
      <tr><td colspan="7" class="text-center text-muted">{{ __('common.no_members_found') }}</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<script>
function archiveMember(id){
  const reason = prompt('{{ __('common.enter_archive_reason') }}');
  if(!reason) return;
  
  // Get fresh CSRF token from meta tag
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                   document.querySelector('input[name="_token"]')?.value || 
                   '{{ csrf_token() }}';
  
  fetch(`{{ url('/members') }}/${id}/archive`, {
    method: 'DELETE',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': csrfToken,
      'Accept': 'application/json'
    },
    body: JSON.stringify({ reason })
  }).then(r=>r.json()).then(res=>{
    if(res.success){ location.reload(); } else { alert(res.message || '{{ __('common.failed') }}'); }
  }).catch(()=>alert('{{ __('common.request_failed') }}'));
}
</script>



