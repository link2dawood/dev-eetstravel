{{-- Modernized folder list (Bootstrap 5 + Tabler). Vue bindings preserved. --}}
<div class="card mb-3 shadow-sm">
    <div class="card-header d-flex align-items-center">
        <i class="ti ti-folders me-2 text-primary"></i>
        <h3 class="card-title mb-0 flex-grow-1">Folders</h3>
        <button type="button" class="btn btn-sm btn-icon btn-ghost-secondary" data-widget="collapse" aria-label="Collapse">
            <i class="ti ti-chevron-up"></i>
        </button>
    </div>

    <div class="card-body p-0">
        <div v-if="loadingFolders" class="d-flex align-items-center justify-content-center" style="min-height:200px">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading…</span>
            </div>
        </div>

        <nav class="list-group list-group-flush" v-if="folders">
            <a href="#"
               class="list-group-item list-group-item-action d-flex align-items-center"
               :class="{ 'active': currentFolder === 'INBOX' }"
               @click.prevent="changeFolder('INBOX')">
                <i class="ti ti-inbox me-2"></i>
                <span class="flex-grow-1">Inbox</span>
            </a>

            <template v-for="(folder, index) in folders.INBOX">
                <a v-if="index !== 'Drafts'"
                   href="#"
                   :key="index"
                   class="list-group-item list-group-item-action d-flex align-items-center"
                   :class="{ 'active': currentFolder === index }"
                   @click.prevent="changeFolder(index)">
                    <i class="ti ti-folder me-2"></i>
                    <span class="flex-grow-1">@{{ index }}</span>
                </a>
            </template>

            <a href="#"
               class="list-group-item list-group-item-action d-flex align-items-center text-primary fw-medium"
               @click.prevent="openCreateFolderModal()">
                <i class="ti ti-plus me-2"></i>
                <span>Add folder</span>
            </a>
        </nav>
    </div>
</div>
