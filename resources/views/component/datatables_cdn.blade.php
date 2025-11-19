{{-- Centralized DataTables CDN Configuration --}}
{{-- Version: 1.13.7 (DataTables Core), 2.5.0 (Responsive) --}}
@php
    $datatablesVersion = '1.13.7';
    $responsiveVersion = '2.5.0';
    $cdnBase = 'https://cdn.datatables.net';
@endphp

{{-- DataTables CSS --}}
<link rel="stylesheet" href="{{ $cdnBase }}/{{ $datatablesVersion }}/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="{{ $cdnBase }}/responsive/{{ $responsiveVersion }}/css/responsive.bootstrap5.min.css">

{{-- DataTables JavaScript Configuration (available globally) - Pushed to scripts stack --}}
@once('datatables-cdn-script')
@push('scripts')
<script>
    // Centralized DataTables CDN URLs
    window.DATATABLES_CDN = {
        version: '{{ $datatablesVersion }}',
        responsiveVersion: '{{ $responsiveVersion }}',
        baseUrl: '{{ $cdnBase }}',
        scripts: [
            '{{ $cdnBase }}/{{ $datatablesVersion }}/js/jquery.dataTables.min.js',
            '{{ $cdnBase }}/{{ $datatablesVersion }}/js/dataTables.bootstrap5.min.js',
            '{{ $cdnBase }}/responsive/{{ $responsiveVersion }}/js/dataTables.responsive.min.js',
            '{{ $cdnBase }}/responsive/{{ $responsiveVersion }}/js/responsive.bootstrap5.min.js'
        ],
        css: [
            '{{ $cdnBase }}/{{ $datatablesVersion }}/css/dataTables.bootstrap5.min.css',
            '{{ $cdnBase }}/responsive/{{ $responsiveVersion }}/css/responsive.bootstrap5.min.css'
        ]
    };
    
    // Centralized DataTables loader function
    window.loadDataTables = function() {
        return new Promise(function(resolve, reject) {
            // Check if DataTables is already loaded
            if (typeof $.fn.DataTable !== 'undefined' && 
                typeof $.fn.dataTable !== 'undefined' && 
                typeof $.fn.dataTable.Api !== 'undefined') {
                resolve();
                return;
            }
            
            // Use centralized CDN configuration
            const scripts = window.DATATABLES_CDN.scripts;
            
            // Load scripts one by one to ensure proper initialization order
            function loadScript(index) {
                if (index >= scripts.length) {
                    // All scripts loaded, wait a bit for initialization
                    setTimeout(function() {
                        if (typeof $.fn.DataTable !== 'undefined') {
                            resolve();
                        } else {
                            reject(new Error('DataTables failed to initialize'));
                        }
                    }, 100);
                    return;
                }
                
                const script = document.createElement('script');
                script.src = scripts[index];
                script.onload = function() {
                    // Load next script only after current one is loaded
                    loadScript(index + 1);
                };
                script.onerror = function() {
                    reject(new Error('Failed to load DataTables from ' + scripts[index]));
                };
                document.head.appendChild(script);
            }
            
            // Start loading from first script
            loadScript(0);
        });
    };
</script>
@endpush
@endonce

