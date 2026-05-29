{{-- Help popover for quotations index. Note distinct ID: #legend_help_quotation. --}}
<div id="legend_help_quotation"
     style="position:absolute; z-index:9999; top:-20px; width:350px; left:65%; opacity:0; background:#fff; border:1px solid #e2e8f0; border-radius:8px; box-shadow:0 12px 28px -8px rgba(15,23,42,0.18); padding:16px;">
    <h2 class="text-base font-semibold text-slate-800 mb-2">{!! trans('main.Quotations') !!}</h2>
    <div class="border-t border-slate-200 -mx-4 mb-3"></div>

    <div class="space-y-2 text-sm">
        <div class="flex items-start gap-3">
            <div class="w-[110px] shrink-0"><button class="inline-flex w-[50px] justify-center rounded bg-success-600 px-2 py-0.5 text-xs text-white" type="button">{!! trans('main.Add') !!}</button></div>
            <p class="flex-1 text-slate-700">{!! trans('main.AddQuotationandconfigure') !!}</p>
        </div>
        <div class="flex items-start gap-3">
            <div class="w-[110px] shrink-0"><i class="fa fa-print legend-media" style="background-color:#3c8cbb;"></i></div>
            <p class="flex-1 text-slate-700">{!! trans('main.Exportcurrentquotation') !!}</p>
        </div>
    </div>
</div>
