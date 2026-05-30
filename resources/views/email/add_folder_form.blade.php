{{-- Add-folder form fragment (legacy non-Vue version). Posts to email.addFolder. --}}
<div class="modal-dialog" role="document">
    <form class="modal-content rounded border border-slate-200 bg-white shadow-lg" action="{{ route('email.addFolder') }}" method="POST">
        {!! Form::token() !!}
        <div class="border-b border-slate-200 px-5 py-3">
            <h3 class="text-sm font-medium text-slate-700 m-0">Add folder</h3>
        </div>
        <div class="px-5 py-4">
            <div class="form-group">
                <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Folder name</label>
                <input type="text" name="folderName" placeholder="Folder name"
                       class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
            </div>
        </div>
        <div class="border-t border-slate-200 bg-slate-50 px-5 py-3 flex items-center justify-between gap-2">
            <button type="reset" data-dismiss="modal"
                    class="inline-flex items-center gap-1 rounded border border-slate-300 bg-white px-3 h-9 text-sm text-slate-700 hover:bg-slate-50">
                <x-ui.icon name="x" size="sm" /> Discard
            </button>
            <button type="submit"
                    class="inline-flex items-center gap-1.5 rounded bg-primary-600 px-4 h-9 text-sm text-white hover:bg-primary-700">
                <x-ui.icon name="folder-plus" size="sm" /> Add folder
            </button>
        </div>
    </form>
</div>
