{{-- Help popover for tasks index. --}}
<div id="legend_help"
     style="position:absolute; z-index:9999; top:-20px; width:350px; right:-100%; opacity:0; background:#fff; border:1px solid #e2e8f0; border-radius:8px; box-shadow:0 12px 28px -8px rgba(15,23,42,0.18); padding:16px;">
    <h2 class="text-base font-semibold text-slate-800 mb-2">{!! trans('main.Tasks') !!}</h2>
    <div class="border-t border-slate-200 -mx-4 mb-3"></div>

    <div class="space-y-2 text-sm">
        <div class="flex items-start gap-3">
            <div class="w-[120px] shrink-0"><button class="inline-flex items-center gap-1 rounded bg-success-600 px-2 py-0.5 text-xs text-white" type="button"><x-ui.icon name="plus" size="xs" /> {!! trans('main.New') !!}</button></div>
            <p class="flex-1 text-slate-700">{!! trans('main.AddTaskandconfigure') !!}</p>
        </div>
        <div class="flex items-start gap-3">
            <div class="w-[120px] shrink-0"><i class="fa fa-info-circle legend-media" style="background-color:#f29b1a;"></i></div>
            <p class="flex-1 text-slate-700">{!! trans('main.ShowTaskinfo') !!}</p>
        </div>
        <div class="flex items-start gap-3">
            <div class="w-[120px] shrink-0"><i class="fa fa-pencil-square-o legend-media" style="background-color:#3c8cbb;"></i></div>
            <p class="flex-1 text-slate-700">{!! trans('main.EditTaskparameters') !!}</p>
        </div>
        <div class="flex items-start gap-3">
            <div class="w-[120px] shrink-0"><i class="fa fa-trash-o legend-media" style="background-color:#dc4a39;"></i></div>
            <p class="flex-1 text-slate-700">{!! trans('main.Confirmremovalof') !!}</p>
        </div>
        <div class="flex items-start gap-3">
            <div class="w-[120px] shrink-0">
                <select id="test_select2" class="block w-full h-7 rounded border border-slate-300 bg-white px-2 text-xs text-slate-700">
                    <option selected value="2">{!! trans('main.Pending') !!}</option>
                </select>
            </div>
            <p class="flex-1 text-slate-700">{!! trans('main.ChangecurrentTaskstatus') !!}</p>
        </div>
        <div class="flex items-start gap-3">
            <div class="w-[120px] shrink-0"><input type="text" disabled placeholder="Search…" class="w-[90px] h-7 rounded border border-slate-300 bg-slate-50 px-2 text-xs"></div>
            <p class="flex-1 text-slate-700">{!! trans('main.Searchamongall') !!}</p>
        </div>
        <div class="flex items-start gap-3">
            <div class="w-[120px] shrink-0"><i class="legend-media" style="background: rgb(255, 187, 178);"></i></div>
            <p class="flex-1 text-slate-700">{!! trans('main.Taskinhighpriorityorder') !!}</p>
        </div>
        <div class="flex items-start gap-3 pt-2 border-t border-slate-100">
            <div class="w-[120px] shrink-0"><button class="inline-flex items-center rounded border border-slate-300 bg-slate-50 px-2 py-0.5 text-xs text-slate-700" type="button">CSV</button></div>
            <p class="flex-1 text-slate-700">{!! trans('main.ExportTaskslistCSV') !!}</p>
        </div>
        <div class="flex items-start gap-3">
            <div class="w-[120px] shrink-0"><button class="inline-flex items-center rounded border border-slate-300 bg-slate-50 px-2 py-0.5 text-xs text-slate-700" type="button">Excel</button></div>
            <p class="flex-1 text-slate-700">{!! trans('main.ExportTaskslistexcel') !!}</p>
        </div>
        <div class="flex items-start gap-3">
            <div class="w-[120px] shrink-0"><button class="inline-flex items-center rounded border border-slate-300 bg-slate-50 px-2 py-0.5 text-xs text-slate-700" type="button">PDF</button></div>
            <p class="flex-1 text-slate-700">{!! trans('main.ExportTaskslistPDF') !!}</p>
        </div>
    </div>
</div>
