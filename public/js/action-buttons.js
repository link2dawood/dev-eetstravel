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

        showToast: function(message, type, title) {
            if (typeof window.appToast === 'function') {
                window.appToast(message, type, title);
                return;
            }

            if (typeof $.toast === 'function') {
                $.toast({
                    heading: title || (type === 'error' ? 'Error' : 'Success'),
                    text: message,
                    icon: type || 'info',
                    position: 'top-right'
                });
            }
        },

        askConfirm: function(message) {
            if (typeof window.appConfirm === 'function') {
                return window.appConfirm(message, {
                    title: 'Confirm delete',
                    confirmText: 'Delete',
                    cancelText: 'Cancel'
                });
            }

            return Promise.resolve(true);
        },

        confirmDelete: function(url) {
            ActionButtonsHandler.askConfirm('Are you sure you want to delete this item?').then(function(confirmed) {
            if (!confirmed) {
                return;
            }

            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                ActionButtonsHandler.showToast('CSRF token not found. Please refresh the page.', 'error', 'Error');
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
                    ActionButtonsHandler.showToast('Item deleted successfully', 'success', 'Success');

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

                ActionButtonsHandler.showToast(error.message || 'Error deleting item. Please try again.', 'error', 'Error');
            });
            });
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        ActionButtonsHandler.init();
    });

})();
