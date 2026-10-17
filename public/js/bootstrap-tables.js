// Bootstrap Tables JavaScript Handler
// Replaces DataTables functionality with simple Bootstrap tables

$(document).ready(function() {
    // Initialize all tables with Bootstrap table functionality
    initBootstrapTables();

    // Handle filter dropdowns
    handleTableFilters();

    // Handle pagination if needed
    handlePagination();

    // Handle empty tables
    handleEmptyTables();
});

function initBootstrapTables() {
    // Add responsive wrapper to all tables
    $('.table').each(function() {
        if (!$(this).parent().hasClass('table-responsive')) {
            $(this).wrap('<div class="table-responsive"></div>');
        }

        // Add Bootstrap table classes if not present
        if (!$(this).hasClass('table-striped')) {
            $(this).addClass('table-striped');
        }
        if (!$(this).hasClass('table-bordered')) {
            $(this).addClass('table-bordered');
        }
        if (!$(this).hasClass('table-hover')) {
            $(this).addClass('table-hover');
        }

        // Add sortable functionality to table headers
        addSortingToTable($(this));
    });
}

function addSortingToTable(table) {
    const headers = table.find('thead th');

    headers.each(function(index) {
        const header = $(this);

        // Skip action columns
        if (header.hasClass('actions-button') || header.text().toLowerCase().includes('action')) {
            return;
        }

        header.addClass('sortable');
        header.css('cursor', 'pointer');

        header.off('click.bootstrapTables').on('click.bootstrapTables', function() {
            sortTableByColumn(table, index, header);
        });
    });
}

function sortTableByColumn(table, columnIndex, header) {
    const tbody = table.find('tbody');
    const rows = tbody.find('tr').toArray();

    const isAscending = !header.hasClass('sort-asc');

    // Remove sort classes from all headers
    table.find('thead th').removeClass('sort-asc sort-desc');

    // Add appropriate sort class
    header.addClass(isAscending ? 'sort-asc' : 'sort-desc');

    rows.sort(function(a, b) {
        const cellA = $(a).find('td').eq(columnIndex).text().trim();
        const cellB = $(b).find('td').eq(columnIndex).text().trim();

        // Try to parse as numbers
        const numA = /^#?-?\d+(?:\.\d+)?$/.test(cellA) ? Number(cellA.replace('#', '')) : NaN;
        const numB = /^#?-?\d+(?:\.\d+)?$/.test(cellB) ? Number(cellB.replace('#', '')) : NaN;

        let comparison;
        if (!isNaN(numA) && !isNaN(numB)) {
            comparison = numA - numB;
        } else {
            comparison = cellA.localeCompare(cellB);
        }

        return isAscending ? comparison : -comparison;
    });

    // Re-append sorted rows
    tbody.empty().append(rows);
    if (table[0]._tablePagination) { table[0]._tablePagination.page = 1; renderTablePage(table[0]); }
}

function handleTableFilters() {
    // Handle dropdown filters
    $('#filterDropdown').on('change', function() {
        const filterValue = $(this).val().toLowerCase();
        const targetTable = $('#tour-table tbody tr');

        targetTable.each(function() {
            const row = $(this);
            const statusText = row.find('td:nth-child(6)').text().toLowerCase();

            if (filterValue === '' || statusText.includes(filterValue)) {
                row.show();
            } else {
                row.hide();
            }
        });
    });

    // Handle year filter for monthly chart
    $('#year-filter').on('change', function() {
        const yearValue = $(this).val();
        filterTableByYear(yearValue);
    });

    // Handle month filter
    $('#month-filter').on('change', function() {
        const monthValue = $(this).val();
        filterTableByMonth(monthValue);
    });
}

function filterTableByYear(year) {
    // This would need to be implemented based on your specific data structure
    // For now, this is a placeholder
    console.log('Filtering by year:', year);
}

function filterTableByMonth(month) {
    // This would need to be implemented based on your specific data structure
    // For now, this is a placeholder
    console.log('Filtering by month:', month);
}

function handlePagination() {
    // Simple client-side pagination
    const itemsPerPage = 50; // Match the previous DataTable pageLength

    $('.table tbody').each(function() {
        const tbody = $(this);
        const rows = tbody.find('tr');

        if (rows.length > itemsPerPage) {
            paginateTable(tbody, rows, itemsPerPage);
        }
    });
}

function paginateTable(tbody, rows, itemsPerPage) {
    const table = tbody.closest('table')[0];
    if (table._tablePagination) return;
    const container = document.createElement('div');
    container.className = 'pagination-container d-flex flex-wrap gap-1 mt-3';
    container.setAttribute('aria-label', 'Table pages');
    tbody.closest('.table-responsive').after(container);
    table._tablePagination = {page: 1, size: itemsPerPage, query: '', container};
    renderTablePage(table);
}

function renderTablePage(table) {
    const state = table._tablePagination;
    const rows = [...table.tBodies[0].rows].filter(row => !row.classList.contains('table-search-empty'));
    table.querySelector('.table-search-empty')?.remove();
    const matches = rows.filter(row => row.textContent.toLowerCase().includes(state.query) &&
        (state.columnQueries || []).every((query, index) => !query || (row.cells[index]?.textContent || '').toLowerCase().includes(query)));
    const pages = Math.max(1, Math.ceil(matches.length / state.size));
    state.page = Math.min(state.page, pages);
    rows.forEach(row => row.style.display = 'none');
    matches.slice((state.page - 1) * state.size, state.page * state.size).forEach(row => row.style.display = '');
    if (!matches.length) {
        const row = table.tBodies[0].insertRow(); row.className = 'table-search-empty';
        const cell = row.insertCell(); cell.colSpan = table.tHead.rows[0].cells.length;
        cell.className = 'text-center text-muted'; cell.textContent = 'No matching records';
    }
    state.container.replaceChildren();
    const button = (label, page, disabled) => {
        const el = document.createElement('button'); el.type = 'button'; el.className = 'btn btn-sm btn-outline-secondary';
        el.textContent = label; el.disabled = disabled;
        el.onclick = () => { state.page = page; renderTablePage(table); };
        state.container.append(el);
    };
    button('Previous', state.page - 1, state.page === 1);
    const summary = document.createElement('span'); summary.className = 'align-self-center px-2';
    summary.textContent = `Page ${state.page} of ${pages} (${matches.length} records)`; state.container.append(summary);
    button('Next', state.page + 1, state.page === pages);
}

function showPage(rows, page, itemsPerPage) {
    const start = (page - 1) * itemsPerPage;
    const end = start + itemsPerPage;

    rows.hide();
    rows.slice(start, end).show();
}

function updateActivePage(pagination, page) {
    pagination.find('.page-item').removeClass('active');
    pagination.find(`[data-page="${page}"]`).parent().addClass('active');
}

// Basic search functionality
function addTableSearch(tableId, searchInputId) {
    $(`#${searchInputId}`).on('keyup', function() {
        const value = $(this).val().toLowerCase();
        $(`#${tableId} tbody tr`).filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
}

// Global filter function for tables
function filterTable(tableId, searchValue) {
    const table = document.getElementById(tableId);
    if (!table) return;
    if (!table._tablePagination) paginateTable($(table.tBodies[0]), $(table.tBodies[0].rows), 50);
    table._tablePagination.query = searchValue.toLowerCase();
    table._tablePagination.page = 1;
    renderTablePage(table);
}

// Export functionality (basic CSV export)
function exportTableToCSV(tableId, filename) {
    const csv = [];
    const rows = document.querySelectorAll(`#${tableId} tr`);

    for (let i = 0; i < rows.length; i++) {
        const row = [];
        const cols = rows[i].querySelectorAll('td, th');

        for (let j = 0; j < cols.length - 1; j++) { // Exclude actions column
            let data = cols[j].textContent.trim().replace(/"/g, '""');
            row.push('"' + data + '"');
        }

        csv.push(row.join(','));
    }

    downloadCSV(csv.join('\n'), filename);
}

function downloadCSV(csv, filename) {
    const csvFile = new Blob([csv], { type: 'text/csv' });
    const downloadLink = document.createElement('a');
    downloadLink.download = filename;
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = 'none';
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}

function handleEmptyTables() {
    $('.table tbody').each(function() {
        const tbody = $(this);
        const rows = tbody.find('tr');

        if (rows.length === 0) {
            const colCount = tbody.siblings('thead').find('th').length;
            tbody.append(`
                <tr class="table-empty">
                    <td colspan="${colCount}" class="text-center py-4">
                        <i class="fa fa-info-circle text-muted"></i>
                        <p class="text-muted mt-2 mb-0">No data available</p>
                    </td>
                </tr>
            `);
        }
    });
}

// Enhanced search with highlighting
function filterTableWithHighlight(tableId, searchValue) {
    const value = searchValue.toLowerCase();
    const table = $(`#${tableId}`);
    const rows = table.find('tbody tr');

    rows.each(function() {
        const row = $(this);
        const text = row.text().toLowerCase();
        const isVisible = text.indexOf(value) > -1;

        row.toggle(isVisible);

        if (isVisible && value) {
            // Remove previous highlights
            row.find('.search-highlight').removeClass('search-highlight');

            // Add highlight to matching text
            row.find('td').each(function() {
                const cell = $(this);
                const cellText = cell.text();
                const regex = new RegExp(`(${value})`, 'gi');
                const highlightedText = cellText.replace(regex, '<span class="search-highlight">$1</span>');
                if (cellText !== highlightedText) {
                    cell.html(highlightedText);
                }
            });
        }
    });
}

// Initialize single table - alias function for compatibility
function initializeBootstrapTable(tableId) {
    const table = $(`#${tableId}`);

    if (table.length === 0) {
        return;
    }

    // Add responsive wrapper if not present
    if (!table.parent().hasClass('table-responsive')) {
        table.wrap('<div class="table-responsive"></div>');
    }

    // Add Bootstrap table classes if not present
    if (!table.hasClass('table-striped')) {
        table.addClass('table-striped');
    }
    if (!table.hasClass('table-bordered')) {
        table.addClass('table-bordered');
    }
    if (!table.hasClass('table-hover')) {
        table.addClass('table-hover');
    }

    // Add sortable functionality to table headers
    addSortingToTable(table);

    // Handle empty state
    handleEmptyTables();
}

// Sorting function for individual tables
function sortTable(columnIndex, tableId) {
    const table = $(`#${tableId}`);
    const header = table.find('thead th').eq(columnIndex);
    sortTableByColumn(table, columnIndex, header);
}
