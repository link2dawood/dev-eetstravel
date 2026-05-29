{{-- Help popover for task calendar (data-mode="1"). --}}
<div id="legend_help" data-mode="1"
     style="position:absolute; z-index:500; top:30px; width:500px; left:-20px; opacity:0; background:#fff; border:1px solid #e2e8f0; border-radius:8px; box-shadow:0 12px 28px -8px rgba(15,23,42,0.18); padding:16px;">
    <h2 class="text-base font-semibold text-slate-800 mb-2">{!! trans('main.TaskCalendar') !!}</h2>
    <div class="border-t border-slate-200 -mx-4 mb-3"></div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-x-4 gap-y-2 text-sm">
        {{-- Left column --}}
        <div class="space-y-2">
            <div class="flex items-start gap-3">
                <div class="w-[80px] shrink-0"></div>
                <div class="flex-1"><button class="inline-flex items-center rounded border border-slate-300 bg-slate-50 px-2 py-0.5 text-xs text-slate-700" type="button">{!! trans('main.Holidays') !!}</button></div>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-[80px] shrink-0"><i class="fa fa-calendar text-lg text-slate-400"></i></div>
                <p class="flex-1 text-slate-700">{!! trans('main.Checklistforselecting') !!}</p>
            </div>
        </div>

        {{-- Right column (spans 2) --}}
        <div class="md:col-span-2 space-y-2">
            <div class="flex items-start gap-3">
                <div class="w-[160px] shrink-0 space-x-1 space-y-1">
                    <button class="inline-flex items-center rounded border border-slate-300 bg-slate-50 px-2 py-0.5 text-xs text-slate-700">{!! trans('main.today') !!}</button>
                    <button class="inline-flex items-center rounded border border-slate-300 bg-slate-50 px-2 py-0.5 text-xs text-slate-700">{!! trans('main.month') !!}</button>
                    <button class="inline-flex items-center rounded border border-slate-300 bg-slate-50 px-2 py-0.5 text-xs text-slate-700">&lt;</button>
                    <button class="inline-flex items-center rounded border border-slate-300 bg-slate-50 px-2 py-0.5 text-xs text-slate-700">&gt;</button>
                    <button class="inline-flex items-center rounded border border-slate-300 bg-slate-50 px-2 py-0.5 text-xs text-slate-700">{!! trans('main.day') !!}</button>
                </div>
                <p class="flex-1 text-slate-700">{!! trans('main.Dateperiodnavigation') !!}</p>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-[160px] shrink-0"><a class="fc-day-grid-event fc-h-event fc-event fc-start fc-end fc-draggable inline-block rounded px-2 py-0.5 text-xs text-white" style="background-color:#3a87ad; width:120px;"><div class="fc-content"><span class="fc-title">Pending status</span></div></a></div>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-[160px] shrink-0"><a class="fc-day-grid-event fc-h-event fc-event fc-start fc-end fc-draggable inline-block rounded px-2 py-0.5 text-xs text-white" style="background-color:#d73925; width:120px;"><div class="fc-content"><span class="fc-title">Abort status</span></div></a></div>
            </div>
        </div>
    </div>

    <p class="mt-3 pt-2 border-t border-slate-100 text-xs text-slate-500"><i class="fa fa-info-circle"></i> {!! trans('main.YoucanclickontheTasktoeditit') !!}</p>
</div>

<style>
    .color-box {
        float: left;
        width: 20px;
        height: 20px;
        margin: 5px;
        border: 1px solid rgba(0, 0, 0, .2);
    }
</style>
