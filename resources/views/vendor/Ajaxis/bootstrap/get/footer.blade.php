<div class="modal-footer">
    <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Cancel</button>
    <button type="button" class="destroy btn btn-danger" data-link="{{ $link }}"><i class="ti ti-trash me-1"></i>{{ $action === 'Agree' ? 'Delete' : $action }}</button>
</div>
