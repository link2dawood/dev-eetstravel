{{-- Folder list rendered inside #emailsfolders Vue root. Vue directives
     (v-if, v-for, :class, :key, @click.prevent) and the Blade @{{ }}
     mustache escape must stay intact. --}}
<div class="rounded border border-slate-200 bg-white shadow-subtle">
    <div class="border-b border-slate-200 px-4 py-3 flex items-center gap-2">
        <x-ui.icon name="folders" size="sm" class="text-primary-600" />
        <h3 class="text-sm font-medium text-slate-700 flex-1">Folders</h3>
    </div>

    <div class="p-0">
        <div v-if="loadingFolders" class="flex items-center justify-center" style="min-height: 200px">
            <div class="spinner-border text-primary-600" role="status">
                <span class="visually-hidden">Loading…</span>
            </div>
        </div>

        <nav class="list-group list-group-flush divide-y divide-slate-100" v-if="folders">
            <a href="#" @click.prevent="changeFolder('INBOX')"
               class="list-group-item list-group-item-action flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 [&.active]:bg-primary-50 [&.active]:text-primary-700 [&.active]:font-medium"
               :class="{ 'active': currentFolder === 'INBOX' }">
                <x-ui.icon name="inbox" size="sm" class="text-slate-400" />
                <span class="flex-1">Inbox</span>
            </a>

            <template v-for="(folder, index) in folders.INBOX">
                <a v-if="index !== 'Drafts'"
                   href="#" :key="index" @click.prevent="changeFolder(index)"
                   class="list-group-item list-group-item-action flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 [&.active]:bg-primary-50 [&.active]:text-primary-700 [&.active]:font-medium"
                   :class="{ 'active': currentFolder === index }">
                    <x-ui.icon name="folder" size="sm" class="text-slate-400" />
                    <span class="flex-1">@{{ index }}</span>
                </a>
            </template>

            <a href="#" @click.prevent="openCreateFolderModal()"
               class="list-group-item list-group-item-action flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-primary-700 hover:bg-primary-50">
                <x-ui.icon name="plus" size="sm" />
                <span>Add folder</span>
            </a>
        </nav>
    </div>
</div>
