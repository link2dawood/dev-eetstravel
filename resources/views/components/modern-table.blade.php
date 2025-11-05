{{-- Modern Table Component --}}
@props([
    'title' => '',
    'searchable' => true,
    'exportable' => true,
    'id' => 'data-table',
    'emptyText' => 'No data found',
    'emptyIcon' => 'ti ti-database',
    'compact' => false,
    'striped' => false,
    'bordered' => false,
    'mobileCards' => true
])

<div class="modern-table-container">
    {{-- Header --}}
    @if($title || $searchable || $exportable || isset($headerActions))
    <div class="table-header">
        @if($title)
            <h3>{{ $title }}</h3>
        @endif
        
        <div class="table-header-actions">
            @if($searchable)
            <div class="table-search">
                <i class="ti ti-search table-search-icon"></i>
                <input 
                    type="text" 
                    id="{{ $id }}-search" 
                    placeholder="Search..." 
                    onkeyup="filterTable('{{ $id }}', this.value)"
                >
            </div>
            @endif

            @if(isset($headerActions))
                {{ $headerActions }}
            @endif

            @if($exportable)
            <button class="table-export-btn" onclick="exportTableToCSV('{{ $id }}', '{{ $id }}_export.csv')">
                <i class="ti ti-download"></i>
                Export CSV
            </button>
            @endif
        </div>
    </div>
    @endif

    {{-- Filters --}}
    @if(isset($filters))
    <div class="table-filters">
        {{ $filters }}
    </div>
    @endif

    {{-- Table --}}
    <div class="table-responsive">
        <table 
            id="{{ $id }}" 
            class="modern-table {{ $compact ? 'table-compact' : '' }} {{ $striped ? 'table-striped' : '' }} {{ $bordered ? 'table-bordered' : '' }} {{ $mobileCards ? 'mobile-cards' : '' }}"
        >
            {{ $slot }}
        </table>
    </div>

    {{-- Pagination --}}
    @if(isset($pagination))
    <div class="table-pagination">
        {{ $pagination }}
    </div>
    @endif
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/modern-tables.css') }}">
@endpush

@push('scripts')
<script>
// Table Search Function
function filterTable(tableId, searchTerm) {
    const table = document.getElementById(tableId);
    const tbody = table.querySelector('tbody');
    const rows = tbody.querySelectorAll('tr');
    searchTerm = searchTerm.toLowerCase();

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
}

// Table Sort Function
function sortTable(columnIndex, tableId) {
    const table = document.getElementById(tableId);
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    const header = table.querySelectorAll('thead th')[columnIndex];
    
    // Determine sort direction
    const isAsc = header.classList.contains('asc');
    
    // Remove all sort classes
    table.querySelectorAll('thead th').forEach(th => {
        th.classList.remove('asc', 'desc');
    });
    
    // Add current sort class
    header.classList.add(isAsc ? 'desc' : 'asc');
    
    // Sort rows
    rows.sort((a, b) => {
        const aValue = a.cells[columnIndex].textContent.trim();
        const bValue = b.cells[columnIndex].textContent.trim();
        
        // Try numeric comparison first
        const aNum = parseFloat(aValue);
        const bNum = parseFloat(bValue);
        
        if (!isNaN(aNum) && !isNaN(bNum)) {
            return isAsc ? bNum - aNum : aNum - bNum;
        }
        
        // String comparison
        return isAsc 
            ? bValue.localeCompare(aValue)
            : aValue.localeCompare(bValue);
    });
    
    // Re-append sorted rows
    rows.forEach(row => tbody.appendChild(row));
}

// Export to CSV Function
function exportTableToCSV(tableId, filename) {
    const table = document.getElementById(tableId);
    const rows = table.querySelectorAll('tr:not([style*="display: none"])');
    let csv = [];
    
    rows.forEach(row => {
        const cols = row.querySelectorAll('td, th');
        const rowData = [];
        
        cols.forEach((col, index) => {
            // Skip action columns
            if (!col.classList.contains('col-actions') && !col.querySelector('.table-actions')) {
                let data = col.textContent.trim();
                data = data.replace(/"/g, '""'); // Escape quotes
                rowData.push('"' + data + '"');
            }
        });
        
        if (rowData.length > 0) {
            csv.push(rowData.join(','));
        }
    });
    
    // Download CSV
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    link.setAttribute('href', url);
    link.setAttribute('download', filename);
    link.style.visibility = 'hidden';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Initialize sortable headers
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.modern-table thead th.sortable').forEach((th, index) => {
        th.style.cursor = 'pointer';
        th.addEventListener('click', function() {
            const table = this.closest('table');
            sortTable(index, table.id);
        });
    });
});
</script>
@endpush

