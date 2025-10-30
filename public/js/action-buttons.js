/**
 * Action Buttons Handler
 * Centralized delete confirmation handler for all tables
 */
(function() {
    'use strict';

    const ActionButtonsHandler = {
        init: function() {
            this.initDeleteHandlers();
        },

        initDeleteHandlers: function() {
            // Use event delegation for better performance
            $(document).off('click', '.delete-btn').on('click', '.delete-btn', function(event) {
                event.preventDefault();
                event.stopPropagation();

                const button = $(this);
                const url = button.data('url');

                if (!url) {
                    console.error('Delete URL not specified');
                    return;
                }

                ActionButtonsHandler.confirmDelete(url);
            });
        },

        confirmDelete: function(url) {
            if (!confirm('Are you sure you want to delete this item?')) {
                return;
            }

            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                alert('CSRF token not found. Please refresh the page.');
                return;
            }

            // Show loading indicator
            const loadingOverlay = $('.loadingoverlay');
            if (loadingOverlay.length) {
                loadingOverlay.fadeIn();
            }

            // Send delete request
            fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('HTTP error! status: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                // Hide loading indicator
                if (loadingOverlay.length) {
                    loadingOverlay.fadeOut();
                }

                if (data.success) {
                    // Show success message
                    if (typeof $.toast === 'function') {
                        $.toast({
                            heading: 'Success',
                            text: 'Item deleted successfully',
                            icon: 'success',
                            position: 'top-right'
                        });
                    }

                    // Reload page after short delay
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    throw new Error(data.message || 'Failed to delete item');
                }
            })
            .catch(error => {
                // Hide loading indicator
                if (loadingOverlay.length) {
                    loadingOverlay.fadeOut();
                }

                console.error('Delete error:', error);

                // Show error message
                if (typeof $.toast === 'function') {
                    $.toast({
                        heading: 'Error',
                        text: error.message || 'Error deleting item. Please try again.',
                        icon: 'error',
                        position: 'top-right'
                    });
                } else {
                    alert('Error deleting item: ' + error.message);
                }
            });
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        ActionButtonsHandler.init();
    });

})();
