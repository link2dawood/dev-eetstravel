{{-- Modernized tour folders list (Bootstrap 5 + Tabler). Vue bindings preserved. --}}
<div class="card mb-3 shadow-sm">
    <div class="card-header d-flex align-items-center">
        <i class="ti ti-map-pin me-2 text-primary"></i>
        <h3 class="card-title mb-0 flex-grow-1">Tour Folders</h3>
        <button type="button" class="btn btn-sm btn-icon btn-ghost-secondary" data-widget="collapse" aria-label="Collapse">
            <i class="ti ti-chevron-up"></i>
        </button>
    </div>

    <div class="card-body p-0">
        <nav class="list-group list-group-flush">
            <a href="#"
               class="list-group-item list-group-item-action d-flex align-items-center"
               @click.prevent="changeTourFolder('active')">
                <i class="ti ti-route me-2 text-success"></i>
                <span class="flex-grow-1">Active tours</span>
            </a>
            <a href="#"
               class="list-group-item list-group-item-action d-flex align-items-center"
               @click.prevent="changeTourFolder('archive')">
                <i class="ti ti-archive me-2 text-muted"></i>
                <span class="flex-grow-1">Archived tours</span>
            </a>
        </nav>
    </div>
</div>
