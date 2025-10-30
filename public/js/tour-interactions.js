/**
 * Tour Interactions JavaScript
 * Handles all interactive features for the Shopify-inspired Tours page
 * - Tab switching with smooth transitions
 * - Live search with debouncing
 * - Filter combinations
 * - Clickable rows with action button exceptions
 * - CSV export functionality
 */

(function() {
    'use strict';

    // Tours Page Manager - Encapsulated module
    const ToursPageManager = {
        searchDebounceTimer: null,
        currentTab: 'tour_tab',

        /**
         * Initialize the page
         */
        init: function() {
            this.initTabs();
            this.initClickableRows();
            this.initSearchFields();
            this.initExportButtons();
            this.initFilterDropdowns();
            this.addLoadingStates();
            console.log('Shopify Tours Page Manager initialized');
        },

        /**
         * Initialize tab switching with smooth transitions
         */
        initTabs: function() {
            const self = this;
            const tabButtons = document.querySelectorAll('.shopify-tab');
            const tabPanes = document.querySelectorAll('.shopify-tab-pane');

            tabButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('data-target');

                    // Remove active class from all tabs and panes
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    tabPanes.forEach(pane => pane.classList.remove('active'));

                    // Add active class to clicked tab and corresponding pane
                    this.classList.add('active');
                    const targetPane = document.querySelector(targetId);
                    if (targetPane) {
                        targetPane.classList.add('active');
                        self.currentTab = targetId.replace('#', '');
                    }

                    // Smooth scroll to top of content
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                });
            });
        },

        /**
         * Initialize clickable table rows
         */
        initClickableRows: function() {
            document.addEventListener('click', function(e) {
                const row = e.target.closest('.clickable-row');

                if (row) {
                    // Don't navigate if clicking on action buttons or inside action cell
                    if (e.target.closest('.action-cell') ||
                        e.target.closest('.shopify-action-buttons') ||
                        e.target.closest('button') ||
                        e.target.closest('a')) {
                        return;
                    }

                    const href = row.getAttribute('data-href');
                    if (href) {
                        window.location.href = href;
                    }
                }
            });

            // Add hover effect via CSS class
            const rows = document.querySelectorAll('.clickable-row');
            rows.forEach(row => {
                row.style.cursor = 'pointer';
                row.style.transition = 'all 0.2s ease';

                row.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = '#f9fafb';
                });

                row.addEventListener('mouseleave', function() {
                    const originalBg = this.getAttribute('data-original-bg');
                    this.style.backgroundColor = originalBg || '';
                });
            });
        },

        /**
         * Initialize search fields with debouncing for performance
         */
        initSearchFields: function() {
            const self = this;
            const searchInput = document.getElementById('tour-search');

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchValue = this.value;
                    const tableId = this.getAttribute('data-table');

                    // Clear previous timer
                    if (self.searchDebounceTimer) {
                        clearTimeout(self.searchDebounceTimer);
                    }

                    // Set new timer for debounce (300ms)
                    self.searchDebounceTimer = setTimeout(function() {
                        self.filterTable(tableId, searchValue);
                    }, 300);
                });

                // Add keyboard shortcuts
                searchInput.addEventListener('keydown', function(e) {
                    // ESC to clear search
                    if (e.key === 'Escape') {
                        this.value = '';
                        self.filterTable(this.getAttribute('data-table'), '');
                    }
                });
            }
        },

        /**
         * Filter table rows based on search value
         */
        filterTable: function(tableId, searchValue) {
            if (!tableId) {
                console.error('Table ID is required for filtering');
                return;
            }

            const table = document.getElementById(tableId);
            if (!table) {
                console.warn('Table not found:', tableId);
                return;
            }

            const tbody = table.querySelector('tbody');
            if (!tbody) return;

            const rows = tbody.getElementsByTagName('tr');
            const searchLower = searchValue.toLowerCase().trim();
            let visibleCount = 0;

            for (let i = 0; i < rows.length; i++) {
                // Skip empty state rows
                if (rows[i].querySelector('.shopify-empty-state')) {
                    continue;
                }

                const cells = rows[i].getElementsByTagName('td');
                let found = false;

                // Search only in data cells, excluding action cell (last column)
                for (let j = 0; j < cells.length - 1; j++) {
                    const cellText = cells[j].textContent || cells[j].innerText;
                    if (cellText.toLowerCase().indexOf(searchLower) > -1) {
                        found = true;
                        break;
                    }
                }

                if (found || searchLower === '') {
                    rows[i].style.display = '';
                    visibleCount++;
                } else {
                    rows[i].style.display = 'none';
                }
            }

            // Show "no results" message if needed
            this.updateEmptyState(table, visibleCount, searchValue);
        },

        /**
         * Update empty state based on search results
         */
        updateEmptyState: function(table, visibleCount, searchValue) {
            const tbody = table.querySelector('tbody');
            let emptyRow = tbody.querySelector('.search-empty-state');

            if (visibleCount === 0 && searchValue !== '') {
                if (!emptyRow) {
                    emptyRow = document.createElement('tr');
                    emptyRow.className = 'search-empty-state';
                    emptyRow.innerHTML = `
                        <td colspan="8" class="shopify-empty-state">
                            <div class="shopify-empty-state-content">
                                <i class="fa fa-search shopify-empty-state-icon"></i>
                                <h3 class="shopify-empty-state-title">No results found</h3>
                                <p class="shopify-empty-state-description">Try adjusting your search terms</p>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(emptyRow);
                }
                emptyRow.style.display = '';
            } else if (emptyRow) {
                emptyRow.style.display = 'none';
            }
        },

        /**
         * Initialize export buttons
         */
        initExportButtons: function() {
            const self = this;
            const exportButtons = document.querySelectorAll('.export-csv');

            exportButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const tableId = this.getAttribute('data-table');
                    const filename = this.getAttribute('data-filename') || 'export.csv';
                    self.exportTableToCSV(tableId, filename);
                });
            });
        },

        /**
         * Export table to CSV with error handling
         */
        exportTableToCSV: function(tableId, filename) {
            try {
                // Validate inputs
                if (!tableId) {
                    throw new Error('Table ID is required');
                }

                // Sanitize filename
                const sanitizedFilename = filename.replace(/[^a-z0-9._-]/gi, '_');

                const table = document.getElementById(tableId);
                if (!table) {
                    throw new Error('Table not found: ' + tableId);
                }

                const csv = [];
                const rows = table.querySelectorAll('tr:not([style*="display: none"]):not(.search-empty-state)');

                if (rows.length === 0) {
                    throw new Error('No data to export');
                }

                // Process each row
                for (let i = 0; i < rows.length; i++) {
                    // Skip empty state rows
                    if (rows[i].querySelector('.shopify-empty-state')) {
                        continue;
                    }

                    const row = [];
                    const cols = rows[i].querySelectorAll('td, th');

                    // Exclude last column (actions)
                    const colCount = cols.length > 0 ? cols.length - 1 : cols.length;

                    for (let j = 0; j < colCount; j++) {
                        const text = (cols[j].innerText || '').replace(/"/g, '""').trim();
                        row.push('"' + text + '"');
                    }

                    csv.push(row.join(','));
                }

                // Create blob and download
                const csvContent = csv.join('\n');
                const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });

                // Create download link
                const link = document.createElement('a');
                if (link.download !== undefined) {
                    const url = URL.createObjectURL(blob);
                    link.setAttribute('href', url);
                    link.setAttribute('download', sanitizedFilename);
                    link.style.visibility = 'hidden';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    // Clean up
                    setTimeout(function() {
                        URL.revokeObjectURL(url);
                    }, 100);

                    // Show success message
                    this.showNotification('CSV exported successfully!', 'success');
                } else {
                    throw new Error('Browser does not support CSV download');
                }
            } catch (error) {
                console.error('CSV Export Error:', error);
                this.showNotification('Error exporting CSV: ' + error.message, 'error');
            }
        },

        /**
         * Initialize filter dropdowns
         */
        initFilterDropdowns: function() {
            const self = this;

            // Status filter dropdown
            const filterDropdown = document.getElementById('filterDropdown');
            if (filterDropdown) {
                filterDropdown.addEventListener('change', function() {
                    const filterValue = this.value.toLowerCase();
                    const tableBody = document.querySelector('#tour-table tbody');

                    if (!tableBody) return;

                    const rows = tableBody.querySelectorAll('tr');

                    if (!filterValue) {
                        rows.forEach(row => row.style.display = '');
                        return;
                    }

                    rows.forEach(row => {
                        if (row.querySelector('.shopify-empty-state')) return;

                        const statusCell = row.querySelector('td:nth-child(6)'); // Status column
                        if (statusCell) {
                            const statusText = statusCell.textContent.toLowerCase();
                            row.style.display = statusText.indexOf(filterValue) > -1 ? '' : 'none';
                        }
                    });
                });
            }

            // Year and month filters
            const yearFilter = document.getElementById('year-filter');
            const monthFilter = document.getElementById('month-filter');

            const applyMonthlyFilters = function() {
                const yearValue = yearFilter ? yearFilter.value : '';
                const monthValue = monthFilter ? monthFilter.value : '';

                const tables = ['#monthly-chart-table', '#cancelled-chart-table'];
                tables.forEach(tableSelector => {
                    const table = document.querySelector(tableSelector);
                    if (!table) return;

                    const rows = table.querySelectorAll('tbody tr');
                    rows.forEach(row => {
                        if (row.querySelector('.shopify-empty-state')) return;

                        let showRow = true;
                        const rowText = row.textContent;

                        if (yearValue && rowText.indexOf(yearValue) === -1) {
                            showRow = false;
                        }

                        if (monthValue && rowText.indexOf(monthValue) === -1) {
                            showRow = false;
                        }

                        row.style.display = showRow ? '' : 'none';
                    });
                });
            };

            if (yearFilter) {
                yearFilter.addEventListener('change', applyMonthlyFilters);
            }

            if (monthFilter) {
                monthFilter.addEventListener('change', applyMonthlyFilters);
            }
        },

        /**
         * Add loading states for better UX
         */
        addLoadingStates: function() {
            // Add loading indicator for CSV export
            const exportButtons = document.querySelectorAll('.export-csv');
            exportButtons.forEach(button => {
                const originalHTML = button.innerHTML;
                button.setAttribute('data-original-html', originalHTML);
            });
        },

        /**
         * Show notification messages
         */
        showNotification: function(message, type) {
            type = type || 'info';

            const notification = document.createElement('div');
            notification.className = 'shopify-notification shopify-notification-' + type;
            notification.innerHTML = `
                <div class="shopify-notification-content">
                    <i class="fa fa-${type === 'success' ? 'check' : 'exclamation'}-circle"></i>
                    <span>${message}</span>
                </div>
            `;
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${type === 'success' ? '#00c875' : '#d72c0d'};
                color: white;
                padding: 16px 24px;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0,0,0,0.15);
                z-index: 9999;
                animation: slideIn 0.3s ease;
            `;

            document.body.appendChild(notification);

            setTimeout(function() {
                notification.style.animation = 'slideOut 0.3s ease';
                setTimeout(function() {
                    document.body.removeChild(notification);
                }, 300);
            }, 3000);
        },

        /**
         * Cleanup event listeners (called on page unload)
         */
        destroy: function() {
            if (this.searchDebounceTimer) {
                clearTimeout(this.searchDebounceTimer);
            }
            console.log('Tours Page Manager destroyed');
        }
    };

    // Initialize on document ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            ToursPageManager.init();
        });
    } else {
        ToursPageManager.init();
    }

    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
        ToursPageManager.destroy();
    });

    // Add CSS animations
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);

})();
