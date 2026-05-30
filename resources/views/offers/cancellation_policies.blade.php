@extends('scaffold-interface.layouts.tabler-app')
@section('title','Cancellation policies')

@section('content')
<x-ui.page-header
    title="Cancellation policies"
    description="Hotel cancellation terms and payment milestones for booked offers."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Cancellation policies'],
    ]"
/>

@if(empty($processedOffers) || count($processedOffers) === 0)
    <div class="rounded border border-slate-200 bg-white">
        <x-ui.empty-state icon="file-text" title="No cancellation policies" message="When an offer has a cancellation policy on file it will appear here." />
    </div>
@else
    <div class="rounded border border-slate-200 bg-white">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-4 py-3 border-b border-slate-200">
            <div class="w-full sm:max-w-xs">
                <input type="text" id="cancellation-search" placeholder="Search cancellation policies…"
                       onkeyup="filterTable('cancellation-policies-table', this.value)"
                       class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            </div>
            <div>
                <button type="button" onclick="exportTableToCSV('cancellation-policies-table', 'cancellation_policies_export.csv')"
                        class="inline-flex items-center gap-1.5 rounded border border-slate-300 bg-white px-3 h-9 text-sm text-slate-700 hover:bg-slate-50 shadow-subtle">
                    <x-ui.icon name="download" size="sm" /> Export CSV
                </button>
            </div>
        </div>
        <div class="overflow-x-auto" style="-webkit-overflow-scrolling: touch;">
            <table id="cancellation-policies-table" class="min-w-full divide-y divide-slate-200 text-sm bootstrap-table" style="background:#fff; min-width: 1200px;">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(0, 'cancellation-policies-table')">ID <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-400" /></th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(1, 'cancellation-policies-table')">{!! trans('Policy') !!} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-400" /></th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(2, 'cancellation-policies-table')">{!! trans('Hotel Name') !!} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-400" /></th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(3, 'cancellation-policies-table')">{!! trans('City') !!} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-400" /></th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(4, 'cancellation-policies-table')">{!! trans('Status') !!} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-400" /></th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(5, 'cancellation-policies-table')">{!! trans('Date of stay') !!} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-400" /></th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(6, 'cancellation-policies-table')">SIN <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-400" /></th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(7, 'cancellation-policies-table')">DOU <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-400" /></th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(8, 'cancellation-policies-table')">TRI <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-400" /></th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(9, 'cancellation-policies-table')">{!! trans('Offer Date') !!} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-400" /></th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(10, 'cancellation-policies-table')">{!! trans('Option Date') !!} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-400" /></th>
                        <th class="px-4 py-3 cursor-pointer select-none" onclick="sortTable(11, 'cancellation-policies-table')">{!! trans('Tour Name') !!} <x-ui.icon name="arrow-up-down" size="xs" class="text-slate-400" /></th>
                        <th class="px-4 py-3 text-right actions-button" style="width: 140px!important">{!! trans('main.Actions') !!}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($processedOffers as $offer)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-mono text-xs text-slate-500">#{{ $offer->id }}</td>
                            <td class="px-4 py-3 text-xs text-slate-700 max-w-[16rem] whitespace-normal break-words" data-delete-label>{{ $offer->cancel_policy }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $offer->hotel_name }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $offer->city_name }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $offer->status }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-slate-600 whitespace-nowrap">{{ $offer->stay_date }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $offer->SIN }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $offer->DOU }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $offer->TRI }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-slate-600">{{ $offer->offer_date ? \Carbon\Carbon::parse($offer->offer_date)->format('Y-m-d') : '' }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-slate-600">{{ $offer->option_date ? \Carbon\Carbon::parse($offer->option_date)->format('Y-m-d') : '' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $offer->tour_name }}</td>
                            <td class="px-4 py-3" onclick="event.stopPropagation();">
                                @if(!empty($offer->tour))
                                    <div class="flex items-center justify-end gap-1">
                                        @include('component.action_buttons', ['item' => $offer->tour, 'routePrefix' => 'tour'])
                                    </div>
                                @else
                                    <span class="block text-right text-xs text-slate-400">No tour linked</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

{{-- Clone tour modal — kept Bootstrap selectors (#tour-clone-modal,
     #tour-clone-modal-form, #tour_id, .tour_dropdown, #services,
     #service_div, #offer_date, #option_date) for the dropdown_ajax +
     .change-tour-button JS below. --}}
<div class="modal fade" id="tour-clone-modal" tabindex="-1" role="dialog" aria-labelledby="tour-clone-label">
    <div class="modal-dialog" role="document">
        <div class="modal-content rounded border border-slate-200 bg-white shadow-lg">
            <div class="modal-header border-b border-slate-200 px-5 py-3">
                <h4 class="text-sm font-medium text-slate-700" id="tour-clone-label">Assign offer to a tour</h4>
            </div>
            <div class="modal-body px-5 py-4 space-y-3">
                <div class="alert alert-info block-error hidden rounded border border-info-200 bg-info-50 px-3 py-2 text-center text-sm text-info-700"></div>
                <form id="tour-clone-modal-form" class="space-y-3">
                    <div class="form-group">
                        <label for="tour_id" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.Tour') }}</label>
                        <input name="offer_date" id="offer_date" type="hidden" value="">
                        <input name="option_date" id="option_date" type="hidden" value="">
                        <select name="tour_id" id="tour_id" required
                                class="form-control tour_dropdown block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                            @foreach($tours as $tour)
                                <option value="{{ $tour->id }}">{{ $tour->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" id="services" style="display:none"></div>
                    <div class="form-group" id="service_div"></div>
                    <div class="flex justify-end">
                        <button type="submit" class="pre-loader-func inline-flex items-center gap-1.5 rounded bg-success-600 px-4 h-9 text-sm text-white hover:bg-success-700" id="clone_tour_send">{!! trans('main.Submit') !!}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Templates / send email modal — kept selectors (#TemplatesModal,
     #templateSendForm, #template_selector, #templatesContent, #subject,
     #email, #file, #file_name, #id, #package_id, #tour_id) for
     loadtemplate.js / sendTemplate(). --}}
<div class="modal fade" id="TemplatesModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
    <div class="modal-dialog modal-lg" style="width: 90%;">
        <form class="modal-content rounded border border-slate-200 bg-white shadow-lg" id="templateSendForm" enctype="multipart/form-data" action="/templates/api/send" method="POST">
            <input name="_token" type="hidden" value="{{ csrf_token() }}">
            <input name="id" id="id" type="hidden" value="">
            <input name="package_id" id="package_id" type="hidden" value="">
            <input name="tour_id" id="tour_id" type="hidden" value="">

            <div class="border-b border-slate-200 px-5 py-3">
                <h3 class="text-sm font-medium text-slate-700">{!! trans('main.SendTemplate') !!}</h3>
            </div>

            <div class="px-5 py-4 space-y-3">
                <div class="form-group">
                    <div class="input-group flex items-stretch gap-2">
                        <input name="email" id="email" required placeholder="E-mail:" value=""
                               class="form-control flex-1 block h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                        <span class="input-group-addon inline-flex items-center px-3 h-9 rounded border border-slate-300 bg-slate-50 text-xs text-slate-500">{!! trans('main.Template') !!}</span>
                        <select id="template_selector" name="template_selector"
                                class="form-control h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle"></select>
                    </div>
                </div>
                <div class="form-group">
                    <input name="subject" id="subject" placeholder="Subject:" value="" style="pointer-events: none;"
                           class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                </div>
                <div class="form-group">
                    <textarea name="templatesContent" id="templatesContent" placeholder="Non required Field"
                              class="form-control" style="height: 400px; visibility: hidden; display: none;"></textarea>
                </div>
                <div class="form-group">
                    <label class="inline-flex cursor-pointer items-center gap-1.5 rounded border border-slate-300 bg-white px-3 h-9 text-sm text-slate-700 hover:bg-slate-50">
                        <x-ui.icon name="paperclip" size="sm" /> {!! trans('main.Attachment') !!}
                        <input type="file" name="attachment[]" multiple id="file" class="hidden">
                    </label>
                    <div id="file_name" class="mt-2 text-xs text-slate-500"></div>
                    <script>
                        document.getElementById('file').onchange = function () {
                            $('#file_name').html('Selected files: <br/>');
                            $.each(this.files, function (i, file) { $('#file_name').append(file.name + ' <br/>'); });
                        };
                    </script>
                    <p class="mt-1 text-xs text-slate-500">Max. 32MB</p>
                </div>
            </div>

            <div class="border-t border-slate-200 bg-slate-50 px-5 py-3 flex items-center justify-between">
                <button type="reset" class="modal-close inline-flex items-center gap-1.5 rounded border border-slate-300 bg-white px-3 h-9 text-sm text-slate-700 hover:bg-slate-50" data-dismiss="modal">
                    <x-ui.icon name="x" size="sm" /> {!! trans('main.Discard') !!}
                </button>
                <button id="send" type="button" onclick="sendTemplate();"
                        class="inline-flex items-center gap-1.5 rounded bg-primary-600 px-4 h-9 text-sm text-white hover:bg-primary-700">
                    <x-ui.icon name="send" size="sm" /> {!! trans('main.Send') !!}
                </button>
            </div>
        </form>
    </div>
</div>

@include('component.delete_modal_simple')
@endsection

@push('scripts')
<script src="{{ asset('js/loadtemplate.js') }}"></script>
<script src="{{ asset('js/bootstrap-tables.js') }}"></script>
<script>
    $(document).ready(function () {
        if (typeof initializeBootstrapTable === 'function') {
            initializeBootstrapTable('cancellation-policies-table');
        }
        $('#tour-clone-modal-form').submit(function (e) {
            if (!confirm('Are you sure? Do you really want to submit the form?')) {
                e.preventDefault();
                location.reload();
            }
        });
    });

    function dropdown_ajax(tour_id, offer_date, option_date) {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content') } });
        $.ajax({
            type: "POST",
            url: `/offer/${tour_id}/days_dropdown`,
            data: { offer_date: offer_date, option_date: option_date },
            success: function (result) {
                if (result[0] === "") {
                    $("#service_div").show();
                    $("#services").hide();
                    $("#service_div").html(`<h3> Please Add Service in the tour </h3>`);
                } else {
                    $("#service_div").hide();
                    $("#services").show();
                    $("#services").html(result);
                }
            },
            error: function (result) { console.log(result); }
        });
    }

    $('.tour_dropdown').on('change', function () {
        dropdown_ajax($(this).val(), $('#offer_date').val(), $('#option_date').val());
    });

    $('.change-tour-button').show().on('click', function () {
        let id = $(this).data('id');
        let tour_id = $(this).data('tour');
        let offer_date = $(this).data('offer_date');
        let option_date = $(this).data('option_date');

        $('#offer_date').val(offer_date);
        $('#option_date').val(option_date);
        dropdown_ajax(tour_id, offer_date, option_date);
        $('#tour_id').val(tour_id).trigger('change');
        $('#tour-clone-modal-form').attr('action', '/offer/' + id + '/assign_to_tour');
    });
</script>
@endpush
