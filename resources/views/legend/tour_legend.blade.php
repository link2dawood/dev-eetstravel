{{-- Help popover for tours index. Wider — two columns at 600px. --}}
<div id="legend_help"
     style="position:absolute; z-index:9999; top:-20px; width:600px; right:-100%; opacity:0; background:#fff; border:1px solid #e2e8f0; border-radius:8px; box-shadow:0 12px 28px -8px rgba(15,23,42,0.18); padding:16px;">
    <h2 class="text-base font-semibold text-slate-800 mb-2">{!! trans('main.Tours') !!}</h2>
    <div class="border-t border-slate-200 -mx-4 mb-3"></div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-2 text-sm">
        <div class="space-y-2">
            <div class="flex items-start gap-3">
                <div class="w-[110px] shrink-0"><button class="inline-flex items-center gap-1 rounded bg-success-600 px-2 py-0.5 text-xs text-white" type="button"><x-ui.icon name="plus" size="xs" /> {!! trans('main.New') !!}</button></div>
                <p class="flex-1 text-slate-700">{!! trans('main.AddTourandconfigurerooms') !!}</p>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-[110px] shrink-0"><i class="fa fa-info-circle legend-media" style="background-color:#f29b1a;"></i></div>
                <p class="flex-1 text-slate-700">{!! trans('main.ShowTourservices') !!}</p>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-[110px] shrink-0"><i class="fa fa-pencil-square-o legend-media" style="background-color:#3c8cbb;"></i></div>
                <p class="flex-1 text-slate-700">{!! trans('main.EditTourparameters') !!}</p>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-[110px] shrink-0"><i class="fa fa-trash-o legend-media" style="background-color:#dc4a39;"></i></div>
                <p class="flex-1 text-slate-700">{!! trans('main.ConfirmremovalofTourforever') !!}</p>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-[110px] shrink-0 text-right pr-2"><i class="fa fa-info-circle fa-2x text-slate-400"></i></div>
                <p class="flex-1 text-slate-700">{!! trans('main.Statusoftheservicecanbechanged') !!}</p>
            </div>
        </div>
        <div class="space-y-2">
            <div class="flex items-start gap-3">
                <div class="w-[110px] shrink-0"><i class="fa fa-plus legend-media" style="background-color:#00a55b;"></i></div>
                <p class="flex-1 text-slate-700">{!! trans('main.AddnewTourwithsimilar') !!}</p>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-[110px] shrink-0"><button class="inline-flex items-center rounded border border-slate-300 bg-slate-50 px-2 py-0.5 text-xs text-slate-700" type="button">CSV</button></div>
                <p class="flex-1 text-slate-700">{!! trans('main.ExportTourslisttoCSVSheet') !!}</p>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-[110px] shrink-0"><button class="inline-flex items-center rounded border border-slate-300 bg-slate-50 px-2 py-0.5 text-xs text-slate-700" type="button">Excel</button></div>
                <p class="flex-1 text-slate-700">{!! trans('main.ExportTourslisttoExcelSheet') !!}</p>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-[110px] shrink-0"><button class="inline-flex items-center rounded border border-slate-300 bg-slate-50 px-2 py-0.5 text-xs text-slate-700" type="button">PDF</button></div>
                <p class="flex-1 text-slate-700">{!! trans('main.ExportTourslisttoPDFDocument') !!}</p>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-[110px] shrink-0"><input type="text" disabled placeholder="Search…" class="w-[90px] h-7 rounded border border-slate-300 bg-slate-50 px-2 text-xs"></div>
                <p class="flex-1 text-slate-700">{!! trans('main.SearchamongallavailableTours') !!}</p>
            </div>
        </div>
    </div>
</div>
