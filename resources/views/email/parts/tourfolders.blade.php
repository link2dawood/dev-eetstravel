{{-- Tour-folder list rendered inside #emailsfolders Vue root. The
     @click.prevent="changeTourFolder(...)" handler toggles #email_box
     and #tour_box visibility. --}}
<div class="rounded border border-slate-200 bg-white shadow-subtle mt-3">
    <div class="border-b border-slate-200 px-4 py-3 flex items-center gap-2">
        <x-ui.icon name="map-pin" size="sm" class="text-primary-600" />
        <h3 class="text-sm font-medium text-slate-700 flex-1">Tour folders</h3>
    </div>

    <nav class="list-group list-group-flush divide-y divide-slate-100">
        <a href="#" @click.prevent="changeTourFolder('active')"
           class="list-group-item list-group-item-action flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">
            <x-ui.icon name="map-pin" size="sm" class="text-success-600" />
            <span class="flex-1">Active tours</span>
        </a>
        <a href="#" @click.prevent="changeTourFolder('archive')"
           class="list-group-item list-group-item-action flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">
            <x-ui.icon name="archive" size="sm" class="text-slate-400" />
            <span class="flex-1">Archived tours</span>
        </a>
    </nav>
</div>
