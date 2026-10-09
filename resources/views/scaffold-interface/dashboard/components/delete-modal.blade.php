<div class="modal modal-blur fade" id="myTourModal" tabindex="-1" role="dialog" aria-hidden="true"> {{-- <-- ID CHANGED --}}
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-status bg-danger"></div>
            <div class="modal-body text-center py-4">
                <i class="ti ti-alert-triangle icon mb-2 text-danger icon-lg"></i>
                <h3>Are you sure?</h3>
                <div class="text-muted" id="delete-message">Do you really want to delete this tour?</div>
                <small class="text-muted d-block mt-2" id="tour-name-display"></small>
            </div>
            <div class="modal-footer">
                <div class="w-100">
                    <div class="row">
                        <div class="col">
                            <button type="button" class="btn w-100" data-bs-dismiss="modal">
                                Cancel
                            </button>
                        </div>
                        <div class="col">
                            <button type="button" id="confirmTourDeleteBtn" class="btn btn-danger w-100"> {{-- <-- ID CHANGED --}}
                                Delete
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
$(document).ready(function() {
    let deleteUrl = null;
    let tourModal = new bootstrap.Modal(document.getElementById('myTourModal'), { {{-- <-- ID CHANGED --}}
        backdrop: 'static',
        keyboard: false
    });

    // Handle delete button click for tours
    $(document).on('click', '.delete', function(e) {
        e.preventDefault();
        
        deleteUrl = $(this).data('link');
        
        if (!deleteUrl) {
            if (typeof window.appToast === 'function') {
                window.appToast('Delete URL not found', 'error', 'Error');
            }
            return;
        }

        var $row = $(this).closest('tr');
        var tourName = $row.find('td:eq(1)').text() || 'Unknown Tour';
        var tourId = $row.find('td:eq(0)').text() || '';
        
        $('#tour-name-display').text('Tour: ' + tourName + (tourId ? ' (ID: ' + tourId + ')' : ''));
        
        tourModal.show();
    });

    // Handle confirm delete button
    $('#confirmTourDeleteBtn').on('click', function() { {{-- <-- ID CHANGED --}}
        if (!deleteUrl) {
            if (typeof window.appToast === 'function') {
                window.appToast('No delete URL specified', 'error', 'Error');
            }
            return;
        }

        var $btn = $(this);
        var originalText = $btn.html();
        
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Deleting...');

        $.ajax({
            url: deleteUrl,
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                tourModal.hide();
                var successMsg = response.message || 'Tour deleted successfully!';
                if (typeof window.appToast === 'function') {
                    window.appToast(successMsg, 'success', 'Success');
                }
                setTimeout(function() {
                    location.reload();
                }, 700);
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html(originalText);
                tourModal.hide();
                var errorMsg = 'Error deleting tour!';
                
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    } else if (xhr.responseJSON.error) {
                        errorMsg = xhr.responseJSON.error;
                    }
                } else if (xhr.status === 0) {
                    errorMsg = 'Network error. Please check your connection.';
                } else if (xhr.status === 404) {
                    errorMsg = 'Tour not found!';
                } else if (xhr.status === 403) {
                    errorMsg = 'You do not have permission to delete this tour!';
                } else if (xhr.status === 500) {
                    errorMsg = 'Server error. Please try again later.';
                }
                
                if (typeof window.appToast === 'function') {
                    window.appToast(errorMsg, 'error', 'Error');
                }
            }
        });
    });

    // Reset form when modal is hidden
    document.getElementById('myTourModal').addEventListener('hidden.bs.modal', function() { {{-- <-- ID CHANGED --}}
        deleteUrl = null;
        $('#confirmTourDeleteBtn').prop('disabled', false).html('Delete'); {{-- <-- ID CHANGED --}}
    });
});
</script>
@endpush
