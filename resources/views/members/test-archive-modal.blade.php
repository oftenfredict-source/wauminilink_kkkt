<!-- Minimal Bootstrap 5 Archive Modal Example -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Test Archive Modal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container py-5">
        <button class="btn btn-warning" onclick="openArchiveModal(123)">Archive Member #123</button>
    </div>
    <div class="modal fade" id="archiveMemberModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Archive Member</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="archiveMemberForm">
                        <input type="hidden" id="archive_member_id">
                        <div class="mb-3">
                            <label for="archive_reason" class="form-label">Reason for archiving</label>
                            <textarea class="form-control" id="archive_reason" name="reason" rows="3" required></textarea>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" id="archiveSubmitBtn" class="btn btn-warning">Archive</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
    function openArchiveModal(id) {
        document.getElementById('archive_member_id').value = id;
        document.getElementById('archive_reason').value = '';
        var modalEl = document.getElementById('archiveMemberModal');
        var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    }
    document.getElementById('archiveSubmitBtn').addEventListener('click', function() {
        const id = document.getElementById('archive_member_id').value;
        const reason = document.getElementById('archive_reason').value.trim();
        if (!reason) {
            Swal.fire({ icon: 'warning', title: 'Please provide a reason.' });
            return;
        }
        Swal.fire({ icon: 'success', title: 'Would archive member #' + id, text: 'Reason: ' + reason });
        bootstrap.Modal.getInstance(document.getElementById('archiveMemberModal')).hide();
    });
    </script>
</body>
</html>
