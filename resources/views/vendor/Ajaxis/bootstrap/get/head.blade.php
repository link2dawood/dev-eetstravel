{{-- Overrides Ajaxis' delete dialog (Bootstrap 3): the layout's #myModal already provides
     .modal-dialog and .modal-content, so only header and body are rendered here. --}}
<div class="modal-header">
    <h5 class="modal-title" id="myModalLabel"><i class="ti ti-alert-triangle text-danger me-1"></i>{{ rtrim($title, '!') }}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
