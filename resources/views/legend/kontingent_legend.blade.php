{{-- Help popover for kontingent (allotment) calendar (data-mode="2"). Wide — three columns at 1000px. --}}
<div id="legend_help" data-mode="2"
     style="position:absolute; z-index:9999; top:-250px; width:1000px; left:200%; opacity:0; background:#fff; border:1px solid #e2e8f0; border-radius:8px; box-shadow:0 12px 28px -8px rgba(15,23,42,0.18); padding:16px;">
    <h2 class="text-base font-semibold text-slate-800 mb-2">{{ trans('main.Allotment') }}</h2>
    <div class="border-t border-slate-200 -mx-4 mb-3"></div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-x-4 gap-y-2 text-sm">
        {{-- Column 1 --}}
        <div class="space-y-2">
            <div class="flex items-start gap-3">
                <div class="w-[140px] shrink-0">
                    <div class="input-group date relative">
                        <span class="input-group-addon date_calendar absolute left-2 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"><i class="fa fa-calendar"></i></span>
                        <input type="text" value="02-2018" disabled class="form-control block w-[100px] h-7 rounded border border-slate-300 bg-white pl-7 pr-2 text-xs">
                    </div>
                </div>
                <p class="flex-1 text-slate-700">{{ trans('main.Firstmonthand') }}<br>{{ trans('main.yeardatechooser') }}</p>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-[140px] shrink-0"><button class="inline-flex items-center rounded border border-slate-300 bg-slate-200 px-2 py-0.5 text-xs text-slate-700" type="button">1|2|3...</button></div>
                <p class="flex-1 text-slate-700">{{ trans('main.Currentcalendardates') }}</p>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-[140px] shrink-0"><button class="inline-flex items-center rounded bg-danger-600 px-2 py-0.5 text-xs text-white" type="button">{{ trans('main.Delete') }}</button></div>
                <p class="flex-1 text-slate-700">{{ trans('main.Confirmremovalofthe') }}<br>{{ trans('main.HotelfromtheTour') }}</p>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-[140px] shrink-0"><button class="inline-flex items-center rounded bg-success-600 px-2 py-0.5 text-xs text-white" type="button">{{ trans('main.Replace') }}</button></div>
                <p class="flex-1 text-slate-700">{{ trans('main.ReplacecurrentHotelwith') }}<br>{{ trans('main.AllotmentHotel') }}</p>
            </div>
        </div>

        {{-- Column 2 --}}
        <div class="space-y-2">
            <div class="flex items-start gap-3">
                <div class="w-[180px] shrink-0"><button class="inline-flex items-center text-center rounded border border-slate-300 bg-slate-200 px-2 py-0.5 text-xs text-slate-700" type="button">{{ trans('main.Contractual') }}<br>{{ trans('main.numberofrooms') }}</button></div>
                <p class="flex-1 text-slate-700">{{ trans('main.Numbersofrooms') }}<br>{{ trans('main.specifiedinthe') }}<br>{{ trans('main.agreementforthe') }}<br>{{ trans('main.relevantperiod') }}</p>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-[180px] shrink-0"><button class="inline-flex items-center text-center rounded border border-slate-300 bg-slate-200 px-2 py-0.5 text-xs text-slate-700" type="button">{{ trans('main.Numberof') }}<br>{{ trans('main.roomsalreadyused') }}</button></div>
                <p class="flex-1 text-slate-700">{{ trans('main.Numbersofroomsin') }}<br>{{ trans('main.tourswithstatus') }}<br>{{ trans('main.Allotmentused') }}</p>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-[180px] shrink-0"><button class="inline-flex items-center rounded border border-slate-300 bg-slate-200 px-2 py-0.5 text-xs text-slate-700" type="button">{{ trans('main.AllotmentReserved') }}</button></div>
                <p class="flex-1 text-slate-700">{{ trans('main.Numbersofroomsin') }}<br>{{ trans('main.tourswithstatus') }}<br>{{ trans('main.Allotmentreserved') }}</p>
            </div>
        </div>

        {{-- Column 3 --}}
        <div class="space-y-2">
            <div class="flex items-start gap-3">
                <div class="w-[150px] shrink-0"><button class="inline-flex items-center rounded border border-slate-300 bg-slate-200 px-2 py-0.5 text-xs text-slate-700" type="button">{{ trans('main.Availablequota') }}</button></div>
                <p class="flex-1 text-slate-700">{{ trans('main.Differencebetweenthedatain') }}<br>{{ trans('main.thecellsofthefirstand') }}<br>{{ trans('main.secondrows') }}</p>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-[150px] shrink-0"><button class="inline-flex items-center text-center rounded border border-slate-300 bg-slate-200 px-2 py-0.5 text-xs text-slate-700" type="button">{{ trans('main.Currentbooking') }}<br>{{ trans('main.status') }} %</button></div>
                <p class="flex-1 text-slate-700">{!! trans('main.Thepercentagethatrepresents') !!}</p>
            </div>
        </div>
    </div>
</div>
