@extends('scaffold-interface.layouts.tabler-app')
@section('title','Hotel offer')

@section('post_styles')
    @include('component.datatables_cdn')
@endsection

@section('content')
<x-ui.page-header
    :title="'Hotel offer #' . $offer->id"
    :description="$package->name ?? 'Offer details'"
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Hotel offer'],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="javascript:history.back()" variant="ghost" icon="arrow-left">{!! trans('main.Back') !!}</x-ui.button>
    </x-slot>
</x-ui.page-header>

<input id="invoice_id" type="hidden" name="invoice_id" value="{{ $offer->id }}">

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

            {{-- Two-column detail grid (was 6× col-lg-6 tables). --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">

                <div class="rounded border border-slate-200 bg-white">
                    <div class="border-b border-slate-200 px-4 py-3 flex items-center gap-2">
                        <x-ui.icon name="building" size="sm" class="text-slate-400" />
                        <h2 class="text-sm font-medium text-slate-700">Hotel & city</h2>
                    </div>
                    <dl class="px-4 py-4 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('Hotel name') !!}</dt>
                            <dd class="mt-0.5 text-slate-800">{!! $package->name ?? '' !!}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('City') !!}</dt>
                            <dd class="mt-0.5 text-slate-800">{!! $city->name ?? '' !!}</dd>
                        </div>
                    </dl>
                </div>

                <div class="rounded border border-slate-200 bg-white">
                    <div class="border-b border-slate-200 px-4 py-3 flex items-center gap-2">
                        <x-ui.icon name="map-pin" size="sm" class="text-slate-400" />
                        <h2 class="text-sm font-medium text-slate-700">Tour & status</h2>
                    </div>
                    <dl class="px-4 py-4 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('Tour Name') !!}</dt>
                            <dd class="mt-0.5 text-slate-800">{!! $tour->name !!}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('Status') !!}</dt>
                            <dd class="mt-0.5 text-slate-800">{!! $offer->status !!}</dd>
                        </div>
                    </dl>
                </div>

                <div class="rounded border border-slate-200 bg-white">
                    <div class="border-b border-slate-200 px-4 py-3 flex items-center gap-2">
                        <x-ui.icon name="user-check" size="sm" class="text-slate-400" />
                        <h2 class="text-sm font-medium text-slate-700">Supplier status</h2>
                    </div>
                    <dl class="px-4 py-4 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('Supplier Status') !!}</dt>
                            <dd class="mt-0.5 text-slate-800">{!! $offer->getStatusName($offer->tms_status) ?? '' !!}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('Option Date') !!}</dt>
                            <dd class="mt-0.5 text-slate-800 font-mono text-xs">{!! $offer->option_date !!}</dd>
                        </div>
                    </dl>
                </div>

                <div class="rounded border border-slate-200 bg-white">
                    <div class="border-b border-slate-200 px-4 py-3 flex items-center gap-2">
                        <x-ui.icon name="calendar" size="sm" class="text-slate-400" />
                        <h2 class="text-sm font-medium text-slate-700">Dates & tax</h2>
                    </div>
                    <dl class="px-4 py-4 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('Offer Date') !!}</dt>
                            <dd class="mt-0.5 text-slate-800 font-mono text-xs">{!! $stay_date ?? '' !!}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('City Tax') !!}</dt>
                            <dd class="mt-0.5 text-slate-800">{!! $offer->city_tax !!}</dd>
                        </div>
                    </dl>
                </div>

                <div class="rounded border border-slate-200 bg-white">
                    <div class="border-b border-slate-200 px-4 py-3 flex items-center gap-2">
                        <x-ui.icon name="utensils" size="sm" class="text-slate-400" />
                        <h2 class="text-sm font-medium text-slate-700">Halfboard & FOC</h2>
                    </div>
                    <dl class="px-4 py-4 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('Halfboard Supp p.p') !!}</dt>
                            <dd class="mt-0.5 text-slate-800">{!! $offer->halfboardMax !!}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('foc') !!}</dt>
                            <dd class="mt-0.5 text-slate-800">{!! $offer->foc_after_every_pax !!}</dd>
                        </div>
                    </dl>
                </div>

                <div class="rounded border border-slate-200 bg-white">
                    <div class="border-b border-slate-200 px-4 py-3 flex items-center gap-2">
                        <x-ui.icon name="briefcase" size="sm" class="text-slate-400" />
                        <h2 class="text-sm font-medium text-slate-700">Portrage & file</h2>
                    </div>
                    <dl class="px-4 py-4 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('Portrage pp') !!}</dt>
                            <dd class="mt-0.5 text-slate-800 font-mono text-xs">{!! $stay_date ?? '' !!}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('Hotel File') !!}</dt>
                            <dd class="mt-0.5 text-slate-800">{!! $offer->hotel_file !!}</dd>
                        </div>
                    </dl>
                </div>

                <div class="rounded border border-slate-200 bg-white lg:col-span-2">
                    <div class="border-b border-slate-200 px-4 py-3 flex items-center gap-2">
                        <x-ui.icon name="bed" size="sm" class="text-slate-400" />
                        <h2 class="text-sm font-medium text-slate-700">Notes & room prices</h2>
                    </div>
                    <dl class="px-4 py-4 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                        <div class="sm:col-span-2">
                            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('Hotel Note') !!}</dt>
                            <dd class="mt-0.5 text-slate-800 prose prose-sm max-w-none">{!! $offer->hotel_file ?? '' !!}</dd>
                        </div>
                        @php $printedRoomNames = []; @endphp
                        @foreach($selected_room_types as $selected_room_type)
                            @if(!in_array($selected_room_type->name, $printedRoomNames))
                                <div>
                                    <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ $selected_room_type->name }}</dt>
                                    <dd class="mt-0.5 text-slate-800">{!! $offer->offersWithRoomPrice($selected_room_type) ?? '' !!}</dd>
                                </div>
                                @php $printedRoomNames[] = $selected_room_type->name; @endphp
                            @endif
                        @endforeach
                    </dl>
                </div>
            </div>

            {{-- Cancellation policies datatable (server-side, route to
                 cancellation_policies_data?offer_id=...). Keep table + tfoot
                 markup intact for the DataTables init below. --}}
            <div class="rounded border border-slate-200 bg-white">
                <div class="border-b border-slate-200 px-4 py-3 flex items-center gap-2">
                    <x-ui.icon name="file-text" size="sm" class="text-slate-400" />
                    <h3 class="text-sm font-medium text-slate-700">Cancellation policies</h3>
                </div>
                <div class="p-4">
                    <div class="overflow-x-auto">
                        <table id="recent-offers-table" class="min-w-full divide-y divide-slate-200 text-sm" style="background:#fff; width: 100%; table-layout: fixed">
                            <thead class="bg-slate-50">
                                <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                                    <th class="px-3 py-2">ID</th>
                                    <th class="px-3 py-2">{!! trans('Policy') !!}</th>
                                    <th class="px-3 py-2">{!! trans('Hotel Name') !!}</th>
                                    <th class="px-3 py-2">{!! trans('City') !!}</th>
                                    <th class="px-3 py-2">{!! trans('Status') !!}</th>
                                    <th class="px-3 py-2">{!! trans('Date of stay') !!}</th>
                                    <th class="px-3 py-2">{!! trans('Offer Date') !!}</th>
                                    <th class="px-3 py-2">{!! trans('Option Date') !!}</th>
                                    <th class="px-3 py-2">{!! trans('Tour Name') !!}</th>
                                    <th class="px-3 py-2 text-right actions-button" style="width: 140px!important">{!! trans('main.Actions') !!}</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th class="not"></th>
                                    <th>{!! trans('Policy') !!}</th>
                                    <th>{!! trans('Hotel Name') !!}</th>
                                    <th>{!! trans('City') !!}</th>
                                    <th>{!! trans('Status') !!}</th>
                                    <th>{!! trans('Date of stay') !!}</th>
                                    <th>{!! trans('Offer Date') !!}</th>
                                    <th>{!! trans('Option Date') !!}</th>
                                    <th>{!! trans('Tour Name') !!}</th>
                                    <th class="not"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
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
<script>
    $(document).ready(function () {
        if (!$.fn.DataTable) return;
        let table = $('#recent-offers-table').DataTable({
            dom: "<'row'<'col-sm-5'l><'col-sm-2'B><'col-sm-5'f>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            buttons: [
                { extend: 'csv',      title: 'Current Offers List', exportOptions: { columns: ':not(.actions-button)' } },
                { extend: 'excel',    title: 'Current Offers List', exportOptions: { columns: ':not(.actions-button)' } },
                { extend: 'pdfHtml5', title: 'Current Offer List',  orientation: 'landscape', exportOptions: { columns: ':not(.actions-button)' } }
            ],
            processing: true,
            serverSide: true,
            pageLength: 50,
            ajax: { url: "{{ route('cancellation_policies_data', ['offer_id' => $offer->id]) }}" },
            columns: [
                { data: 'id',           name: 'id' },
                { data: 'cancel_policy', name: 'cancel_policy' },
                { data: 'hotel_name',   name: 'hotel_name' },
                { data: 'city',         name: 'city' },
                { data: 'status',       name: 'status' },
                { data: 'stay_date',    name: 'stay_date' },
                { data: 'stay_date',    name: 'stay_date' },
                { data: 'option_date',  name: 'option_date' },
                { data: 'tour_name',    name: 'tour_name' },
                { data: 'action',       name: 'action', searchable: false, sorting: false, orderable: false }
            ]
        });
        $('#recent-offers-table tfoot th').each(function () {
            let column = this;
            if (column.className !== 'not') {
                let title = $(this).text();
                $(this).html('<input type="text" class="form-control block w-full h-8 rounded border border-slate-300 bg-white px-2 text-sm" placeholder="Search ' + title + '" />');
            }
        });
        table.columns().every(function () {
            let that = this;
            $('input', this.footer()).on('keyup change', function () {
                if (that.search() !== this.value) {
                    that.search(this.value).draw();
                }
            });
        });
        $('#recent-offers-table tfoot th').appendTo('#recent-offers-table thead');
    });
</script>
@endpush
