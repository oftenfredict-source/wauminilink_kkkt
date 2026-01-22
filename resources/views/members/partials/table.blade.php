<div class="table-responsive">
  <table class="table table-striped align-middle">
    <thead>
      <tr>
        <th>Member ID</th>
        <th>Full Name</th>
        <th>Phone</th>
        <th>Email</th>
        <th>Gender</th>
        <th>Region</th>
        <th>Actions</th>
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
          <button class="btn btn-sm btn-outline-danger" onclick="archiveMember({{ $m->id }})">Archive</button>
        </td>
      </tr>
      @empty
      <tr><td colspan="7" class="text-center text-muted">No members found.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<script>
function archiveMember(id){
  const reason = prompt('Please enter a reason for archiving this member:');
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
    if(res.success){ location.reload(); } else { alert(res.message || 'Failed.'); }
  }).catch(()=>alert('Request failed'));
}
</script>



