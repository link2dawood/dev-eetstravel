{{-- Loaded into #myModal via .add-direct-chat data-link wiring in
     main.blade.php. Selectors preserved: #errors_message, #custom_chat_name,
     #add_custom_chat are all read by chatUser.bind() in main.blade.php. --}}
<div class="modal-dialog" role="document">
    <div class="modal-content rounded border border-slate-200 bg-white shadow-lg">
        <div class="modal-header border-b border-slate-200 px-5 py-3 text-sm font-medium text-slate-700">
            {!! trans('main.CreateChat') !!}
        </div>
        <div class="modal-body px-5 py-4 space-y-3">
            <div id="errors_message"
                 class="hidden rounded border border-danger-600/20 bg-danger-50 px-3 py-2 text-sm text-danger-700 text-center"></div>

            <div class="form-group">
                <label for="custom_chat_name" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.Name') !!}</label>
                <input type="text" class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" id="custom_chat_name">
            </div>

            <div class="flex justify-end">
                <button type="button" class="inline-flex items-center gap-1.5 rounded bg-success-600 px-3 h-9 text-sm text-white hover:bg-success-700" id="add_custom_chat">
                    <x-ui.icon name="plus" size="sm" />{!! trans('main.AddChat') !!}
                </button>
            </div>
        </div>
    </div>
</div>
