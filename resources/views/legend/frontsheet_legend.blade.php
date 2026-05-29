{{-- Help popover for front sheet. --}}
<div id="legend_help"
     style="position:absolute; z-index:9999; top:-20px; width:350px; right:-100%; opacity:0; background:#fff; border:1px solid #e2e8f0; border-radius:8px; box-shadow:0 12px 28px -8px rgba(15,23,42,0.18); padding:16px;">
    <h2 class="text-base font-semibold text-slate-800 mb-2">{{ trans('main.FrontSheet') }}</h2>
    <div class="border-t border-slate-200 -mx-4 mb-3"></div>

    <div class="space-y-2 text-sm">
        <div class="flex items-start gap-3">
            <div class="w-[110px] shrink-0">
                <a class="comments-button inline-flex items-center gap-1">
                    <span class="badge inline-flex items-center rounded-full bg-warning-500 text-white px-1.5 py-0.5 text-[10px]">0</span>
                    <i class="fa fa-comment-o text-slate-500"></i>
                </a>
            </div>
            <p class="flex-1 text-slate-700">Add/Show comments</p>
        </div>
        <div class="flex items-start gap-3 pt-2 border-t border-slate-100">
            <div class="w-[110px] shrink-0">
                <div class="input-group date relative">
                    <input type="text" name="hotel_list_sent" value="" class="form-control datepicker block w-[80px] h-7 rounded border border-slate-300 bg-white pl-2 pr-7 text-xs">
                    <span class="input-group-addon absolute right-1 top-1/2 -translate-y-1/2 text-slate-400"><i class="fa fa-calendar"></i></span>
                </div>
            </div>
            <p class="flex-1 text-slate-700">* Rooming list received</p>
        </div>
        <div class="flex items-start gap-3">
            <div class="w-[110px] shrink-0">
                <div class="input-group date relative">
                    <input type="text" name="hotel_list_sent" value="" class="form-control datepicker block w-[80px] h-7 rounded border border-slate-300 bg-white pl-2 pr-7 text-xs">
                    <span class="input-group-addon absolute right-1 top-1/2 -translate-y-1/2 text-slate-400"><i class="fa fa-calendar"></i></span>
                </div>
            </div>
            <p class="flex-1 text-slate-700">* Visa confirmation sent</p>
        </div>
        <div class="flex items-start gap-3">
            <div class="w-[110px] shrink-0"><textarea class="block w-[80px] h-12 rounded border border-slate-300 bg-white px-2 text-xs"></textarea></div>
            <p class="flex-1 text-slate-700">Text field for important<br>comments or e-mails</p>
        </div>
        <div class="flex items-start gap-3">
            <div class="w-[110px] shrink-0 text-right pr-2"><input type="checkbox" checked></div>
            <p class="flex-1 text-slate-700">When all the checkboxes of the column are checked, the current date appears in the corresponding field</p>
        </div>
        <p class="pt-2 border-t border-slate-100 text-xs text-slate-500">* When the date is selected, the checkboxes of the corresponding column become checked</p>
    </div>
</div>
