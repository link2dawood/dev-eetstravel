{{-- Popup create — legacy fallback variant of changeFolder modal. --}}
<div id="modalCreate" class="modal fade in" role="dialog" aria-labelledby="modalCreateLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content rounded border border-slate-200 bg-white shadow-lg">
            <div class="border-b border-slate-200 px-5 py-3">
                <h3 class="text-sm font-medium text-slate-700 m-0">Choose folder</h3>
            </div>
            <div class="px-5 py-4">
                <div class="form-group">
                    <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Folder</label>
                    <select name="folder"
                            class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                        <option v-for="(folder, index) in folders.INBOX">11</option>
                    </select>
                </div>
            </div>
            <div class="border-t border-slate-200 bg-slate-50 px-5 py-3 flex items-center justify-between gap-2">
                <button type="reset" data-dismiss="modal"
                        class="inline-flex items-center gap-1 rounded border border-slate-300 bg-white px-3 h-9 text-sm text-slate-700 hover:bg-slate-50">
                    <x-ui.icon name="x" size="sm" /> Discard
                </button>
                <button type="submit"
                        class="pre-loader-func inline-flex items-center gap-1.5 rounded bg-primary-600 px-4 h-9 text-sm text-white hover:bg-primary-700">
                    <x-ui.icon name="send" size="sm" /> Send
                </button>
            </div>
        </div>
    </div>
</div>
