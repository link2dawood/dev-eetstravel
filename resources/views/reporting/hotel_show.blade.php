@extends('scaffold-interface.layouts.tabler-app')
@section('title','Hotel reporting')

@section('content')
<x-ui.page-header
    :title="$hotel->name"
    description="Hotel supplier reporting"
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Accounting', 'href' => route('accounting.index')],
        ['label' => $hotel->name],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="javascript:history.back()" variant="ghost" icon="arrow-left">{!! trans('main.Back') !!}</x-ui.button>
    </x-slot>
</x-ui.page-header>

@php
    $tabBase   = 'group inline-flex items-center gap-2 whitespace-nowrap border-b-2 px-1 pb-3 pt-3 text-sm transition-colors border-transparent text-slate-600 hover:text-slate-900 hover:border-slate-300';
    $tabActive = '[&.active]:border-primary-600 [&.active]:text-primary-700 [&.active]:font-medium';
    $tabClass  = $tabBase . ' ' . $tabActive;
@endphp

<div id="fixed-scroll" class="rounded border border-slate-200 bg-white nav-tabs-custom">
    <div class="border-b border-slate-200 px-1">
        <ul class="nav nav-tabs nav-tabs-underline -mb-px flex flex-nowrap gap-6 overflow-x-auto border-0 px-3 list-none pl-0 m-0 [&_.nav-link]:cursor-pointer" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active {{ $tabClass }}" href="#info-tab" data-toggle="tab" data-bs-toggle="tab" aria-controls="info-tab" role="tab" aria-selected="true">
                    <x-ui.icon name="info" />{!! trans('main.Info') !!}
                </a>
            </li>
        </ul>
    </div>

    <div class="p-5 tab-content">
        <div class="tab-pane fade in active show" role="tabpanel" id="info-tab">
            <input id="tourName" type="hidden" name="tourName" value="">

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
                <div class="rounded border border-slate-200 bg-white">
                    <div class="border-b border-slate-200 px-4 py-3 flex items-center gap-2">
                        <x-ui.icon name="building" size="sm" class="text-slate-400" />
                        <h2 class="text-sm font-medium text-slate-700">Identity</h2>
                    </div>
                    <dl class="px-4 py-4 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                        <div><dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('main.Name') !!}</dt><dd class="mt-0.5 text-slate-800 font-medium">{!! $hotel->name !!}</dd></div>
                        <div><dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('main.Code') !!}</dt><dd class="mt-0.5 text-slate-800">{!! $hotel->code !!}</dd></div>
                        <div><dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('main.AddressFirst') !!}</dt><dd class="mt-0.5 text-slate-800">{!! $hotel->address_first !!}</dd></div>
                        <div><dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('main.AddressSecond') !!}</dt><dd class="mt-0.5 text-slate-800">{!! $hotel->address_second !!}</dd></div>
                        <div><dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('main.Country') !!}</dt><dd class="mt-0.5 text-slate-800">{!! \App\Helper\CitiesHelper::getCountryById($hotel->country)['name'] ?? '' !!}</dd></div>
                        <div><dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('main.City') !!}</dt><dd class="mt-0.5 text-slate-800">{!! !empty($hotel->city) ? \App\Helper\CitiesHelper::getCityById($hotel->city)['name'] : '' !!}</dd></div>
                        <div><dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('main.WorkPhone') !!}</dt><dd class="mt-0.5 text-slate-800">{!! $hotel->work_phone !!}</dd></div>
                        <div><dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('main.WorkFax') !!}</dt><dd class="mt-0.5 text-slate-800">{!! $hotel->work_fax !!}</dd></div>
                        <div class="sm:col-span-2"><dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('main.WorkEmail') !!}</dt><dd class="mt-0.5 text-slate-800">{!! $hotel->work_email !!}</dd></div>
                    </dl>
                </div>

                <div class="rounded border border-slate-200 bg-white">
                    <div class="border-b border-slate-200 px-4 py-3 flex items-center gap-2">
                        <x-ui.icon name="user" size="sm" class="text-slate-400" />
                        <h2 class="text-sm font-medium text-slate-700">Contact & profile</h2>
                    </div>
                    <dl class="px-4 py-4 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                        <div><dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('main.ContactName') !!}</dt><dd class="mt-0.5 text-slate-800">{!! $hotel->contact_name !!}</dd></div>
                        <div><dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('main.ContactPhone') !!}</dt><dd class="mt-0.5 text-slate-800">{!! $hotel->contact_phone !!}</dd></div>
                        <div class="sm:col-span-2"><dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('main.ContactEmail') !!}</dt><dd class="mt-0.5 text-slate-800">{!! $hotel->contact_email !!}</dd></div>
                        <div class="sm:col-span-2"><dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('main.Comments') !!}</dt><dd class="mt-0.5 text-slate-800 prose prose-sm max-w-none">{!! $hotel->comments !!}</dd></div>
                        <div class="sm:col-span-2"><dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('main.IntComments') !!}</dt><dd class="mt-0.5 text-slate-800 prose prose-sm max-w-none">{!! $hotel->int_comments !!}</dd></div>
                        <div class="sm:col-span-2">
                            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('main.Criterias') !!}</dt>
                            <dd class="mt-0.5 flex flex-wrap gap-1">
                                @php $empty = 0; @endphp
                                @forelse($criterias as $criteria)
                                    @forelse($hotel->criterias as $item)
                                        @if($criteria->id == $item->criteria_id)
                                            <span class="criteria_block inline-flex items-center rounded bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-700">{!! $criteria->name !!}</span>
                                            @php $empty = 1; @endphp
                                        @endif
                                    @empty
                                    @endforelse
                                @empty
                                @endforelse
                                @if($empty == 0)<span class="text-xs text-slate-400">—</span>@endif
                            </dd>
                        </div>
                        <div><dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('main.Rate') !!}</dt><dd class="mt-0.5 text-slate-800">{!! $hotel->rate_name !!}</dd></div>
                        <div><dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('main.Website') !!}</dt><dd class="mt-0.5 text-slate-800 truncate">{!! $hotel->website !!}</dd></div>
                        <div><dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('main.CityTax') !!}</dt><dd class="mt-0.5 text-slate-800">{!! $hotel->city_tax !!}</dd></div>
                        <div class="sm:col-span-2"><dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('main.Note') !!}</dt><dd class="mt-0.5 text-slate-800 prose prose-sm max-w-none">{!! $hotel->note !!}</dd></div>
                    </dl>
                </div>
            </div>

            {{-- Total invoice chart --}}
            <div class="rounded border border-slate-200 bg-white">
                <div class="border-b border-slate-200 px-5 py-3 flex items-center gap-2">
                    <x-ui.icon name="trending-up" size="sm" class="text-slate-400" />
                    <h3 class="text-sm font-medium text-slate-700">Total invoice amount</h3>
                </div>
                <div class="px-5 py-5 space-y-4">
                    <p class="text-4xl sm:text-5xl font-bold tracking-tight text-slate-900">€ {{ number_format($invoice_total, 0, '.', ',') }}</p>
                    <input type="hidden" value="{{ $totalAmounts['Febuary'] }}" id="value10">
                    <input type="hidden" value="{{ $totalAmounts['March'] }}"   id="value20">
                    <input type="hidden" value="{{ $totalAmounts['April'] }}"   id="value30">
                    <input type="hidden" value="{{ $totalAmounts['May'] }}"     id="value40">
                    <input type="hidden" value="{{ $totalAmounts['June'] }}"    id="value50">
                    <canvas id="chart" class="chart" style="max-height: 500px;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('post_scripts')
    <script src="{{ asset('js/comment.js') }}"></script>
@endsection

@push('scripts')
<script type="text/javascript" src="{{ asset('js/jspdf.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/2.4.1/lodash.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    var currentDate = new Date();
    var monthNames = ["January","February","March","April","May","June","July","August","September","October","November","December"];
    var currentMonth = monthNames[currentDate.getMonth()];
    var previousMonths = [];
    for (let i = 4; i >= 0; i--) {
        var previousMonthIndex = currentDate.getMonth() - i;
        previousMonths.push(monthNames[previousMonthIndex < 0 ? 11 : previousMonthIndex]);
    }
    var day = currentDate.getDate();

    const ctx = document.querySelectorAll('.chart');
    for (var i = 0; i < ctx.length; i++) {
        var value1 = document.getElementById("value1" + i).value;
        var value2 = document.getElementById("value2" + i).value;
        var value3 = document.getElementById("value3" + i).value;
        var value4 = document.getElementById("value4" + i).value;
        var value5 = document.getElementById("value5" + i).value;
        new Chart(ctx[i], {
            type: "line",
            data: {
                labels: previousMonths,
                datasets: [{
                    label: "Amount",
                    data: [value1, value2, value3, value4, value5],
                    borderWidth: 1,
                    borderColor: "#159a9c",
                    backgroundColor: '#159a9c',
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: { x: { display: true }, y: { beginAtZero: true, display: true } }
            }
        });
    }
</script>
@endpush
