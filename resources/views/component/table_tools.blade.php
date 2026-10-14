<div class="d-flex flex-wrap gap-2 justify-content-between mb-3">
    <input class="form-control" style="max-width:320px" type="search" aria-label="Search {{ $label }}" placeholder="Search {{ $label }}…" oninput="filterTable('{{ $tableId }}', this.value)">
    <button type="button" class="btn btn-outline-secondary" onclick="exportTableToCSV('{{ $tableId }}', '{{ $tableId }}.csv')">Export CSV</button>
</div>
