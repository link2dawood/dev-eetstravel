{{-- Help popover for tour services (data-mode="4"). Wide — three columns at 950px.
     Note opacity:1 (it's shown by default on first visit and JS later toggles). --}}
<div id="legend_help" data-mode="4"
     style="position:absolute; z-index:9999; top:-20px; width:950px; right:-500%; opacity:1; background:#fff; border:1px solid #e2e8f0; border-radius:8px; box-shadow:0 12px 28px -8px rgba(15,23,42,0.18); padding:16px;">
    <h2 class="text-base font-semibold text-slate-800 mb-2">{!! trans('main.Services') !!}</h2>
    <div class="border-t border-slate-200 -mx-4 mb-3"></div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-x-4 gap-y-2 text-sm">
        {{-- Column 1 --}}
        <div class="space-y-2">
            <div class="flex items-start gap-3">
                <div class="w-[120px] shrink-0"><i class="fa fa-pencil-square-o legend-media" style="background-color:#3c8cbb;"></i></div>
                <p class="flex-1 text-slate-700">{!! trans('main.Editserviceparameters') !!}</p>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-[120px] shrink-0"><span class="legend-media" style="background-color:#dc4a39;">M</span></div>
                <p class="flex-1 text-slate-700">{!! trans('main.SetcurrentHotelasMain') !!}</p>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-[120px] shrink-0"><i class="fa fa-trash-o legend-media" style="background-color:#dc4a39;"></i></div>
                <p class="flex-1 text-slate-700">{!! trans('main.ConfirmremovalofservicefromTourforever') !!}</p>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-[120px] shrink-0"><i class="fa fa-exchange legend-media" style="background-color:#f29b1a;"></i></div>
                <p class="flex-1 text-slate-700">{!! trans('main.Replacecurrentservice') !!}</p>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-[120px] shrink-0"><i class="fa fa-envelope legend-media" style="background-color:#00a65a;"></i></div>
                <p class="flex-1 text-slate-700">{!! trans('main.Composeemailbasedontemplate') !!}</p>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-[120px] shrink-0"><button class="show_all inline-flex items-center rounded border border-slate-300 bg-slate-50 px-2 py-0.5 text-xs text-slate-700">{!! trans('main.Checkall') !!}</button></div>
                <p class="flex-1 text-slate-700">{!! trans('main.Showalldatesonthemap') !!}</p>
            </div>
        </div>

        {{-- Column 2 --}}
        <div class="space-y-2">
            <div class="flex items-start gap-3">
                <div class="w-[120px] shrink-0"><button class="inline-flex items-center rounded border border-slate-300 bg-slate-50 px-2 py-0.5 text-xs text-slate-700" type="button">CSV</button></div>
                <p class="flex-1 text-slate-700">{!! trans('main.ExportTourinformationtoCSVSheet') !!}</p>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-[120px] shrink-0"><button class="inline-flex items-center rounded border border-slate-300 bg-slate-50 px-2 py-0.5 text-xs text-slate-700" type="button">Excel</button></div>
                <p class="flex-1 text-slate-700">{!! trans('main.ExportTourinformationtoExcelSheet') !!}</p>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-[120px] shrink-0"><button class="inline-flex items-center rounded border border-slate-300 bg-slate-50 px-2 py-0.5 text-xs text-slate-700" type="button">{!! trans('main.Voucher') !!}</button></div>
                <p class="flex-1 text-slate-700">{!! trans('main.ExportvouchertoPDFdocument') !!}</p>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-[120px] shrink-0"><button class="inline-flex items-center rounded border border-slate-300 bg-slate-50 px-2 py-0.5 text-xs text-slate-700" type="button">{!! trans('main.Itinerary') !!}</button></div>
                <p class="flex-1 text-slate-700">{!! trans('main.ExportitinerarytoPDFdocument') !!}</p>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-[120px] shrink-0"><div class="icheckbox_minimal-blue checked" aria-checked="false" aria-disabled="false" style="position:relative;"><input type="checkbox" value="" checked style="position:absolute; opacity:0;"></div></div>
                <p class="flex-1 text-slate-700">{!! trans('main.CheckedelementswillbeexportedtoIt') !!}</p>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-[120px] shrink-0"><div class="icheckbox_minimal-blue checked" aria-checked="false" aria-disabled="false" style="position:relative;"><input type="checkbox" value="" checked style="position:absolute; opacity:0;"></div></div>
                <p class="flex-1 text-slate-700">{!! trans('main.Checkedelementswillhaveanicon') !!}</p>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-[120px] shrink-0"><button class="inline-flex items-center rounded bg-warning-500 px-2 py-0.5 text-xs text-white">{!! trans('main.Edit') !!}</button></div>
                <p class="flex-1 text-slate-700">{!! trans('main.EditTourparameters') !!}</p>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-[120px] shrink-0"><a href="#" class="inline-flex items-center rounded bg-success-600 px-2 py-0.5 text-xs text-white">{!! trans('main.AddTask') !!}</a></div>
                <p class="flex-1 text-slate-700">{!! trans('main.AddTaskforassignedusers') !!}</p>
            </div>
        </div>

        {{-- Column 3 --}}
        <div class="space-y-2">
            <div class="flex items-start gap-3">
                <div class="w-[60px] shrink-0 text-right pr-2"><i class="fa fa-info-circle fa-2x text-slate-400"></i></div>
                <p class="flex-1 text-slate-700">{!! trans('main.Timestatusanddescription') !!}</p>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-[60px] shrink-0 text-right pr-2"><i class="fa fa-info-circle fa-2x text-slate-400"></i></div>
                <p class="flex-1 text-slate-700">{!! trans('main.Draganddropactivity') !!}</p>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-[60px] shrink-0"><i class="fa fa-star text-warning-500"></i></div>
                <p class="flex-1 text-slate-700">{!! trans('main.Hotelmarkedasparent') !!}</p>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-[60px] shrink-0"><i class="fa fa-star-o text-warning-500"></i></div>
                <p class="flex-1 text-slate-700">{!! trans('main.Hotelmarkedaschild') !!}</p>
            </div>
        </div>
    </div>
</div>
