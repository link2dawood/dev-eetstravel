{{-- Help popover for supplier search. --}}
<div id="legend_help"
     style="position:absolute; z-index:9999; top:-20px; width:350px; right:-100%; opacity:0; background:#fff; border:1px solid #e2e8f0; border-radius:8px; box-shadow:0 12px 28px -8px rgba(15,23,42,0.18); padding:16px;">
    <h2 class="text-base font-semibold text-slate-800 mb-2">{!! trans('main.SupplierSearch') !!}</h2>
    <div class="border-t border-slate-200 -mx-4 mb-3"></div>

    <div class="space-y-2 text-sm">
        <div class="flex items-start gap-3">
            <div class="w-[140px] shrink-0"><input type="button" value="Search" class="inline-flex items-center rounded bg-primary-600 px-3 py-0.5 text-xs text-white"></div>
            <p class="flex-1 text-slate-700">{!! trans('main.Startsearching') !!}</p>
        </div>
        <div class="flex items-start gap-3">
            <div class="w-[140px] shrink-0 select_filter">
                <label class="block"></label>
                <div><input type="checkbox"> English</div>
            </div>
            <p class="flex-1 text-slate-700">{!! trans('main.SearchCriteria') !!}</p>
        </div>
        <div class="flex items-start gap-3">
            <div class="w-[140px] shrink-0 select_filter">
                <label class="block"></label>
                <div><input type="radio" value=""> 1*</div>
            </div>
            <p class="flex-1 text-slate-700">{!! trans('main.Filterrates') !!}</p>
        </div>
        <div class="flex items-start gap-3">
            <div class="w-[140px] shrink-0"><button class="inline-flex items-center rounded bg-warning-500 px-2 py-0.5 text-xs text-white" type="button">{!! trans('main.ClearFilter') !!}</button></div>
            <p class="flex-1 text-slate-700">{!! trans('main.Clearcurrent') !!}</p>
        </div>
        <div class="flex items-start gap-3">
            <div class="w-[140px] shrink-0"><button class="inline-flex items-center rounded bg-success-600 px-2 py-0.5 text-xs text-white" type="button">{!! trans('main.Tour') !!}</button></div>
            <p class="flex-1 text-slate-700">{!! trans('main.Addcurrentservice') !!}</p>
        </div>
        <div class="flex items-start gap-3">
            <div class="w-[140px] shrink-0"><i class="fa fa-plus legend-media" style="background-color:#00a55b;"></i></div>
            <p class="flex-1 text-slate-700">{!! trans('main.Selectdatestoadd') !!}</p>
        </div>
    </div>
</div>
