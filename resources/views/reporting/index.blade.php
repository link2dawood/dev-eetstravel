@extends('scaffold-interface.layouts.tabler-app')
@section('title','Reporting')

@section('content')
<x-ui.page-header
    title="Reporting"
    description="Cross-service spend summary and per-supplier detail."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Reporting'],
    ]"
/>

{{-- Service-type filter --}}
<div class="rounded border border-slate-200 bg-white mb-4">
    <div class="border-b border-slate-200 px-5 py-3 flex items-center gap-2">
        <x-ui.icon name="filter" size="sm" class="text-slate-400" />
        <h2 class="text-sm font-medium text-slate-700">{!! trans('main.Addservice') !!}</h2>
    </div>
    <form action="{{ route('supplier_show') }}" class="px-5 py-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="md:col-span-2">
                <label for="service-select" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Service type</label>
                <select id="service-select"
                        class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                    <option selected>{!! trans('main.All') !!}</option>
                    @foreach($options as $option)
                        <option>@if($option === 'Transfer') Bus Company @else {{ $option }} @endif</option>
                    @endforeach
                </select>
            </div>
        </div>
    </form>
</div>

{{-- Search results table --}}
<div class="rounded border border-slate-200 bg-white">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-4 py-3 border-b border-slate-200">
        <div class="w-full sm:max-w-xs">
            <input type="text" id="reporting-search" placeholder="Search services…"
                   onkeyup="filterTable('search-table', this.value)"
                   class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
        </div>
        <div>
            <button type="button" onclick="exportTableToCSV('search-table', 'reporting_services_export.csv')"
                    class="inline-flex items-center gap-1.5 rounded border border-slate-300 bg-white px-3 h-9 text-sm text-slate-700 hover:bg-slate-50 shadow-subtle">
                <x-ui.icon name="download" size="sm" /> Export CSV
            </button>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table id="search-table" class="min-w-full divide-y divide-slate-200 text-sm bootstrap-table" style="width: 100%;">
            <thead class="bg-slate-50">
                <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                    <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(0, 'search-table')">{!! trans('main.Name') !!} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-400" /></th>
                    <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(1, 'search-table')">{!! trans('main.Address') !!} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-400" /></th>
                    <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(2, 'search-table')">{!! trans('main.Country') !!} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-400" /></th>
                    <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(3, 'search-table')">{!! trans('main.City') !!} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-400" /></th>
                    <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(4, 'search-table')">{!! trans('main.Phone') !!} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-400" /></th>
                    <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(5, 'search-table')">{!! trans('main.ContactName') !!} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-400" /></th>
                    <th class="px-4 py-3 text-right actions-button">{!! trans('Actions') !!}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($servicesData as $service)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium text-slate-900" data-delete-label>{{ $service->nameService ?? $service->name }}</td>
                        <td class="px-4 py-3 text-slate-700">{{ $service->address_first ?? '' }}</td>
                        <td class="px-4 py-3 text-slate-700">{{ $service->country ?? '' }}</td>
                        <td class="px-4 py-3 text-slate-700">{{ $service->city ?? '' }}</td>
                        <td class="px-4 py-3 text-slate-700">{{ $service->work_phone ?? '' }}</td>
                        <td class="px-4 py-3 text-slate-700">{{ $service->contact_name ?? '' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1 btn-list flex-nowrap">
                                @if(isset($service->can_show) && $service->can_show)
                                    <a class="inline-flex h-8 w-8 items-center justify-center rounded border border-slate-300 bg-white text-info-600 hover:bg-info-50"
                                       hidden data-id="{{ $service->id }}" data-type="{{ class_basename(get_class($service)) }}"
                                       data-service_name="{{ $service->nameService ?? $service->name }}" id="service-property"
                                       href="{{ $service->show_link ?? '#' }}" data-link="{{ $service->show_link ?? '#' }}">
                                        <x-ui.icon name="info" size="sm" />
                                    </a>
                                @endif
                                @if(isset($service->service_type))
                                    @php
                                        $routePrefix = $service->service_type;
                                        if ($routePrefix === 'transfer') $routePrefix = 'bus';
                                    @endphp
                                    @include('component.action_buttons', ['item' => $service, 'routePrefix' => $routePrefix])
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-sm text-slate-500">No services found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@include('component.delete_modal_simple')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('js/bootstrap-tables.js') }}"></script>
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

    // Render per-account mini charts if the (currently commented out) account cards
    // are re-enabled. Loops over .chart canvases and reads #valueN{idx} hidden inputs.
    const ctx = document.querySelectorAll('.chart');
    for (var i = 0; i < ctx.length; i++) {
        var value1 = document.getElementById("value1" + i)?.value;
        var value2 = document.getElementById("value2" + i)?.value;
        var value3 = document.getElementById("value3" + i)?.value;
        var value4 = document.getElementById("value4" + i)?.value;
        var value5 = document.getElementById("value5" + i)?.value;
        new Chart(ctx[i], {
            type: "line",
            data: {
                labels: previousMonths,
                datasets: [{
                    label: "Amount",
                    data: [value1, value2, value3, value4, value5],
                    borderWidth: 1,
                    borderColor: "#159a9c",
                    pointRadius: 0,
                    backgroundColor: '#159a9c',
                }],
            },
            options: {
                plugins: { legend: { display: false } },
                scales: { x: { display: true }, y: { beginAtZero: true, display: false } }
            }
        });
    }

    // Filter by service type — the legacy on-change handler is preserved for
    // future filter logic; today it just resets row visibility.
    if (typeof initializeBootstrapTable === 'function') {
        initializeBootstrapTable('search-table');
    }
    let service = "All";
    $('#service-select').on('change', function () {
        var tmp = this.value;
        if (tmp === 'Bus Company') tmp = 'Transfer';
        service = tmp;
        var rows = document.getElementById('search-table').getElementsByTagName('tbody')[0].getElementsByTagName('tr');
        for (var i = 0; i < rows.length; i++) { rows[i].style.display = ''; }
    });
</script>
@endpush
