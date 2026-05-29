{{-- Help popover for quotation edit (data-mode="3"). Wider — two columns at 750px. --}}
<div id="legend_help" data-mode="3"
     style="position:absolute; z-index:9999; top:-5px; width:750px; left:-200%; opacity:0; background:#fff; border:1px solid #e2e8f0; border-radius:8px; box-shadow:0 12px 28px -8px rgba(15,23,42,0.18); padding:16px;">
    <h2 class="text-base font-semibold text-slate-800 mb-2">{!! trans('main.Quotations') !!}</h2>
    <div class="border-t border-slate-200 -mx-4 mb-3"></div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-2 text-sm">
        <div class="space-y-2">
            <div class="flex items-start gap-3">
                <div class="w-[140px] shrink-0"><input class="form-control block w-full h-7 rounded border border-slate-300 bg-white px-2 text-xs" type="text" value="Usa Quotation"></div>
                <p class="flex-1 text-slate-700">{!! trans('main.Quotationname') !!}</p>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-[140px] shrink-0"><a href="#" class="text-primary-600 underline text-xs">{!! trans('main.Showtitles') !!}</a></div>
                <p class="flex-1 text-slate-700">{!! trans('main.ShowHidetitles') !!}</p>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-[140px] shrink-0"><input type="text" value="1.00" class="form-control block w-full h-7 rounded border border-slate-300 bg-white px-2 text-xs"></div>
                <p class="flex-1 text-slate-700">{!! trans('main.Ratevalue') !!}</p>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-[140px] shrink-0"><input type="text" value="0" class="form-control block w-full h-7 rounded border border-slate-300 bg-white px-2 text-xs"></div>
                <p class="flex-1 text-slate-700">{!! trans('main.MarkUpvalue') !!}</p>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-[140px] shrink-0"><button type="button" class="inline-flex items-center rounded bg-success-600 px-2 py-0.5 text-xs text-white" style="width:100px">{!! trans('main.Addcolumn') !!}</button></div>
                <p class="flex-1 text-slate-700">{!! trans('main.Addcolumntoquotationwith') !!}</p>
            </div>
        </div>
        <div class="space-y-2">
            <div class="flex items-start gap-3">
                <div class="w-[140px] shrink-0"><textarea class="block w-full h-12 rounded border border-slate-300 bg-white px-2 text-xs">{!! trans('main.Notes') !!}</textarea></div>
                <p class="flex-1 text-slate-700">{!! trans('main.Addyournotes') !!}</p>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-[140px] shrink-0"><input type="checkbox" checked></div>
                <p class="flex-1 text-slate-700">{!! trans('main.Checkedelementswillbe') !!}</p>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-[140px] shrink-0"><button class="inline-flex items-center gap-1 rounded bg-success-600 px-2 py-0.5 text-xs text-white" type="button"><x-ui.icon name="plus" size="xs" /> {!! trans('main.Add') !!}</button></div>
                <p class="flex-1 text-slate-700">{!! trans('main.NewAdditionalConfigure') !!}</p>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-[140px] shrink-0"><a href="#" class="inline-flex items-center justify-center rounded bg-danger-600 px-2 py-0.5 text-white"><i class="fa fa-trash-o"></i></a></div>
                <p class="flex-1 text-slate-700">{!! trans('main.DeleteAdditionalConfigure') !!}</p>
            </div>
        </div>
    </div>
</div>
