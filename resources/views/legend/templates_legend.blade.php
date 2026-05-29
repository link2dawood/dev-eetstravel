{{-- Help popover for email templates index/show. --}}
<div id="legend_help"
     style="position:absolute; z-index:9999; top:-20px; width:350px; right:-100%; opacity:0; background:#fff; border:1px solid #e2e8f0; border-radius:8px; box-shadow:0 12px 28px -8px rgba(15,23,42,0.18); padding:16px;">
    <h2 class="text-base font-semibold text-slate-800 mb-2">{!! trans('main.EmailTemplates') !!}</h2>
    <div class="border-t border-slate-200 -mx-4 mb-3"></div>

    <div class="space-y-2 text-sm">
        <div class="flex items-start gap-3">
            <div class="w-[110px] shrink-0"><button class="inline-flex items-center gap-1 rounded bg-success-600 px-2 py-0.5 text-xs text-white" type="button">{!! trans('main.New') !!}</button></div>
            <p class="flex-1 text-slate-700">{!! trans('main.AddTemplatenameand') !!}</p>
        </div>
        <div class="flex items-start gap-3">
            <div class="w-[110px] shrink-0"><i class="fa fa-pencil-square-o legend-media" style="background-color:#3c8cbb;"></i></div>
            <p class="flex-1 text-slate-700">{!! trans('main.EditTemplatenameandcontent') !!}</p>
        </div>
        <div class="flex items-start gap-3">
            <div class="w-[110px] shrink-0"><i class="fa fa-trash-o legend-media" style="background-color:#dc4a39;"></i></div>
            <p class="flex-1 text-slate-700">{!! trans('main.ConfirmremovalofTemplateforever') !!}</p>
        </div>

        <div class="pt-3 mt-2 border-t border-slate-100">
            <p class="text-center text-xs font-medium text-slate-500 mb-2">{!! trans('main.Availabletags') !!}</p>
            <div class="grid grid-cols-2 gap-1.5">
                @foreach(['##name##','##date##','##pax##','##address##','##email##','##phone##','##description##','##status##','##time_from##','##price_for_one##','##menu##','##roominglist##'] as $tag)
                    <button type="button" class="inline-flex justify-center rounded border border-slate-300 bg-slate-50 px-2 py-0.5 text-xs font-mono text-slate-700">{{ $tag }}</button>
                @endforeach
            </div>
            <p class="mt-3 text-xs text-slate-500">{!! trans('main.Thetagswillbereplaced') !!}</p>
        </div>
    </div>
</div>
