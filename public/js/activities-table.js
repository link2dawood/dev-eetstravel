$(document).ready(function() {
    const table = document.getElementById('activity-table');
    if (!table) return;
    // Reuse the layout's sorting, filtering and pagination instead of DataTables.
    table.querySelectorAll('.table-empty').forEach(row => row.remove());
    if (!table._tablePagination) paginateTable($(table.tBodies[0]), $(table.tBodies[0].rows), 50);
    const state = table._tablePagination;
    state.columnQueries = ['', '', ''];
    table.querySelectorAll('tfoot th').forEach((cell, index) => {
        if (index > 2) return;
        const input = document.createElement('input');
        input.type = 'search'; input.className = 'form-control';
        input.setAttribute('aria-label', 'Search ' + cell.textContent.trim());
        input.placeholder = input.getAttribute('aria-label');
        cell.replaceChildren(input);
        input.addEventListener('input', () => {
            state.columnQueries[index] = input.value.toLowerCase();
            state.page = 1; renderTablePage(table);
        });
    });
    document.getElementById('activity-search').addEventListener('input', event => filterTable(table.id, event.target.value));
    document.getElementById('activity-page-size').addEventListener('change', event => {
        state.size = Number(event.target.value); state.page = 1; renderTablePage(table);
    });
    document.querySelectorAll('[data-activity-export]').forEach(button => button.addEventListener('click', () => {
        const error = document.getElementById('activity-export-error');
        error.hidden = true;
        const headers = [...table.tHead.rows[0].cells].slice(0, 3).map(cell => cell.textContent.trim());
        const rows = [...table.tBodies[0].rows].filter(row => !row.classList.contains('table-search-empty') &&
            row.textContent.toLowerCase().includes(state.query) &&
            state.columnQueries.every((query, index) => row.cells[index].textContent.toLowerCase().includes(query))
        ).map(row => [...row.cells].slice(0, 3).map(cell => cell.textContent.trim()));
        try {
            const type = button.dataset.activityExport;
            if (type === 'csv') {
                const csv = [headers, ...rows].map(row => row.map(value => {
                    // Prevent spreadsheet formulas in user-generated log text.
                    const safe = /^[\s]*[=+@-]/.test(value) ? "'" + value : value;
                    return '"' + safe.replace(/"/g, '""') + '"';
                }).join(',')).join('\r\n');
                const url = URL.createObjectURL(new Blob(['\uFEFF', csv], {type: 'text/csv;charset=utf-8'}));
                const link = document.createElement('a'); link.href = url; link.download = 'Activities List.csv';
                document.body.append(link); link.click(); link.remove();
                setTimeout(() => URL.revokeObjectURL(url), 1000);
            } else if (type === 'xlsx') {
                const workbook = {SheetNames: ['Activities'], Sheets: {Activities: XLSX.utils.aoa_to_sheet([headers, ...rows])}};
                const binary = XLSX.write(workbook, {bookType: 'xlsx', type: 'binary'});
                const bytes = Uint8Array.from(binary, character => character.charCodeAt(0) & 255);
                saveAs(new Blob([bytes], {type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'}), 'Activities List.xlsx');
            } else {
                pdfMake.createPdf({pageOrientation: 'landscape', content: [
                    {text: 'Activities List', fontSize: 16, margin: [0, 0, 0, 12]},
                    {table: {headerRows: 1, widths: ['auto', 'auto', '*'], body: [headers, ...rows]}, fontSize: 9}
                ]}).download('Activities List.pdf');
            }
        } catch (exportError) {
            console.error('Activities export failed:', exportError);
            error.textContent = 'Unable to export activities. Please reload and try again.';
            error.hidden = false;
        }
    }));
    renderTablePage(table);
});
