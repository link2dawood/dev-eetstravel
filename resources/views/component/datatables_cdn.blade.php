{{-- Centralized DataTables CDN. Include this once near the top of any view
     that calls $().DataTable(). Pushes CSS/JS so DataTables is available by
     the time any @push('scripts') block in the same view runs. --}}
@php
    $datatablesVersion = '1.13.7';
    $responsiveVersion = '2.5.0';
    $cdnBase = 'https://cdn.datatables.net';
@endphp

@once
    <link rel="stylesheet" href="{{ $cdnBase }}/{{ $datatablesVersion }}/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ $cdnBase }}/responsive/{{ $responsiveVersion }}/css/responsive.bootstrap5.min.css">

    @push('scripts')
        <script src="{{ $cdnBase }}/{{ $datatablesVersion }}/js/jquery.dataTables.min.js"></script>
        <script src="{{ $cdnBase }}/{{ $datatablesVersion }}/js/dataTables.bootstrap5.min.js"></script>
        <script src="{{ $cdnBase }}/responsive/{{ $responsiveVersion }}/js/dataTables.responsive.min.js"></script>
        <script src="{{ $cdnBase }}/responsive/{{ $responsiveVersion }}/js/responsive.bootstrap5.min.js"></script>
        <script>
            // Back-compat shim: previously this partial only defined a lazy
            // loadDataTables() promise. Some views were written to await it
            // even though they never actually did. Provide a resolved promise
            // so any leftover `loadDataTables().then(...)` calls still work.
            window.loadDataTables = function () { return Promise.resolve(); };
            window.DATATABLES_CDN = {
                version: '{{ $datatablesVersion }}',
                responsiveVersion: '{{ $responsiveVersion }}',
                baseUrl: '{{ $cdnBase }}'
            };
        </script>
    @endpush
@endonce
