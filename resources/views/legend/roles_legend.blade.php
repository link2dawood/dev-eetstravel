{{-- Help popover for roles index. --}}
<div id="legend_help"
     style="position:absolute; z-index:9999; top:-20px; width:350px; right:-100%; opacity:0; background:#fff; border:1px solid #e2e8f0; border-radius:8px; box-shadow:0 12px 28px -8px rgba(15,23,42,0.18); padding:16px;">
    <h2 class="text-base font-semibold text-slate-800 mb-2">{!! trans('main.Roles') !!}</h2>
    <div class="border-t border-slate-200 -mx-4 mb-3"></div>

    <div class="space-y-2 text-sm">
        <div class="flex items-start gap-3">
            <div class="w-[140px] shrink-0"><button class="inline-flex items-center gap-1 rounded bg-success-600 px-2 py-0.5 text-xs text-white" type="button"><x-ui.icon name="plus" size="xs" /> {!! trans('main.New') !!}</button></div>
            <p class="flex-1 text-slate-700">{!! trans('main.AddRoleand') !!}</p>
        </div>
        <div class="flex items-start gap-3">
            <div class="w-[140px] shrink-0"><i class="fa fa-pencil-square-o legend-media" style="background-color:#3c8cbb;"></i></div>
            <p class="flex-1 text-slate-700">{!! trans('main.EditRoleparameters') !!}</p>
        </div>
        <div class="flex items-start gap-3">
            <div class="w-[140px] shrink-0"><i class="fa fa-trash-o legend-media" style="background-color:#dc4a39;"></i></div>
            <p class="flex-1 text-slate-700">{!! trans('main.Confirmremovalofrole') !!}</p>
        </div>
        <div class="flex items-start gap-3">
            <div class="w-[140px] shrink-0 flex flex-wrap gap-1">
                <small class="inline-flex rounded bg-warning-500 text-white px-1.5 py-0.5 text-[10px]">{!! trans('main.HotelList') !!}</small>
                <small class="inline-flex rounded bg-warning-500 text-white px-1.5 py-0.5 text-[10px]">{!! trans('main.HotelCreate') !!}</small>
                <small class="inline-flex rounded bg-warning-500 text-white px-1.5 py-0.5 text-[10px]">{!! trans('main.HotelEdit') !!}</small>
                <small class="inline-flex rounded bg-warning-500 text-white px-1.5 py-0.5 text-[10px]">{!! trans('main.HotelShow') !!}</small>
            </div>
            <p class="flex-1 text-slate-700">{!! trans('main.Permissionslist') !!}</p>
        </div>
    </div>
</div>
