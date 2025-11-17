<div class="modal modal-blur fade" id="generic-delete-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-status bg-danger"></div>
            <div class="modal-body text-center py-4">
                <i class="ti ti-alert-triangle icon mb-2 text-danger icon-lg"></i>
                <h3>{{ __('main.Warning') ?? 'Are you sure?' }}</h3>
                <div class="text-muted" data-delete-modal-message>
                    {{ __('main.WouldyouliketoremoveThis') ?? 'Do you really want to delete this record?' }}
                </div>
            </div>
            <div class="modal-footer">
                <div class="w-100">
                    <div class="row">
                        <div class="col">
                            <button type="button" class="btn w-100" data-bs-dismiss="modal">
                                {{ __('main.Cancel') ?? 'Cancel' }}
                            </button>
                        </div>
                        <div class="col">
                            <button type="button" class="btn btn-danger w-100" data-delete-modal-confirm>
                                {{ __('main.Delete') ?? 'Delete' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalEl = document.getElementById('generic-delete-modal');
    if (!modalEl || typeof bootstrap === 'undefined') {
        return;
    }

    // Get CSRF token from meta tag (used for non-GET delete methods)
    const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : null;

    const modal = new bootstrap.Modal(modalEl);
    const messageEl = modalEl.querySelector('[data-delete-modal-message]');
    const confirmBtn = modalEl.querySelector('[data-delete-modal-confirm]');
    const defaultMessage = messageEl ? messageEl.textContent.trim() : 'Do you really want to delete this record?';
    let deleteUrl = null;
    let deleteMethod = 'GET';

    document.body.addEventListener('click', function (event) {
        const trigger = event.target.closest('.delete');
        if (!trigger) {
            return;
        }

        event.preventDefault();
        event.stopImmediatePropagation();
        const link = trigger.getAttribute('data-link');
        if (!link) {
            return;
        }
        deleteUrl = link;
        deleteMethod = (trigger.getAttribute('data-delete-method') || 'GET').toUpperCase();

        if (messageEl) {
            const row = trigger.closest('tr');
            const labelCell = row ? row.querySelector('[data-delete-label]') : null;
            const labelText = labelCell ? labelCell.textContent.trim() : '';
            messageEl.textContent = labelText
                ? `Do you really want to delete "${labelText}"?`
                : defaultMessage;
        }

        modal.show();
    });

    if (confirmBtn) {
        confirmBtn.addEventListener('click', function () {
            if (!deleteUrl) {
                return;
            }
            if (deleteMethod === 'GET') {
                window.location.href = deleteUrl;
                return;
            }

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = deleteUrl;
            form.style.display = 'none';

            if (csrfToken) {
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken;
                form.appendChild(csrfInput);
            }

            if (deleteMethod !== 'POST') {
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = deleteMethod;
                form.appendChild(methodInput);
            }

            document.body.appendChild(form);
            form.submit();
        });
    }

    modalEl.addEventListener('hidden.bs.modal', function () {
        deleteUrl = null;
        deleteMethod = 'GET';
        if (messageEl) {
            messageEl.textContent = defaultMessage;
        }
    });
});
</script>
@endpush

