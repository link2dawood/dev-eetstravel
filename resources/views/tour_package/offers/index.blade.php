@extends('scaffold-interface.layouts.tabler-app')
@section('title','Hotel offers')

@section('content')
<style>
    .select2-container { margin-top: -25px !important; }
</style>

<x-ui.page-header
    title="Hotel offers"
    :description="$tour_package->name"
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Tour package'],
        ['label' => $tour_package->name],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="javascript:history.back()" variant="ghost" icon="arrow-left">{!! trans('main.Back') !!}</x-ui.button>
        @if($tour_package->service()->service_type == 'Hotel')
            {!! \App\Helper\PermissionHelper::getCreateButton(route('offers.create', $tour_package->id), \App\Tour::class) !!}
            <x-ui.button as="a" href="{{ route('offers.emails', $tour_package->id) }}" variant="secondary" icon="mail">{!! trans('Offer Emails') !!}</x-ui.button>
        @endif
    </x-slot>
</x-ui.page-header>

<div class="space-y-4">

    {{-- Package detail --}}
    <div class="rounded border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-3 flex items-center gap-2">
            <x-ui.icon name="package" size="sm" class="text-slate-400" />
            <h2 class="text-sm font-medium text-slate-700">Package details</h2>
        </div>
        <dl class="px-5 py-4 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('Name') !!}</dt>
                <dd class="mt-0.5 text-slate-800 font-medium">{!! $tour_package->name !!}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('Description') !!}</dt>
                <dd class="mt-0.5 text-slate-800 prose prose-sm max-w-none">{!! $tour_package->description !!}</dd>
            </div>
            @if($tour_package->service()->service_type == 'Hotel')
                <div class="sm:col-span-2">
                    <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('Supplier Url') !!}</dt>
                    <dd class="mt-0.5">
                        <a href="{!! $tour_package->supplier_url . '/' . $tour_package->id !!}"
                           class="inline-flex items-center gap-1.5 text-sm text-primary-600 hover:text-primary-700">
                            <x-ui.icon name="external-link" size="sm" />Go to Supplier Page
                        </a>
                    </dd>
                </div>
            @endif
        </dl>

        {{-- Comment widget hooks (read by comment.js) --}}
        <div class="px-5 pb-4">
            <span id="default_reference_id"   data-info="{{ $tour_package->id }}"></span>
            <span id="default_reference_type" data-info="{{ \App\Comment::$services['tour_package'] }}"></span>
            <span id="default_token_val"      data-info="{{ csrf_token() }}"></span>
            <div id="commentContainer"></div>
        </div>
    </div>

    {{-- Hotel offers table (only for Hotel-type packages) --}}
    @if($tour_package->service()->service_type == 'Hotel')
        @php $offer = $tour_package->hotel_offers->last(); @endphp

        <div class="rounded border border-slate-200 bg-white">
            <div class="border-b border-slate-200 px-5 py-3 flex items-center gap-2">
                <x-ui.icon name="building" size="sm" class="text-slate-400" />
                <h3 class="text-sm font-medium text-slate-700">Hotel offers</h3>
            </div>

            @if(empty($offer))
                <div class="px-5 py-6 text-center text-sm text-slate-500">
                    We don't have any offers yet — please send an email to get an offer from the supplier.
                </div>
            @else
                <div class="overflow-x-auto" style="max-height: 400px;">
                    <table id="offers-table" class="min-w-full divide-y divide-slate-200 text-sm" style="background:#fff; width: 100%; table-layout: auto;">
                        <thead class="bg-slate-50">
                            <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                                <th class="px-3 py-2">ID</th>
                                <th class="px-3 py-2" style="width: 104px;">{!! trans('Offer Status') !!}</th>
                                <th class="px-3 py-2" style="width: 104px;">{!! trans('Supplier Status') !!}</th>
                                @php $printedRoomNames = []; @endphp
                                @foreach($selected_room_types as $selected_room_type)
                                    @if(!in_array($selected_room_type->name, $printedRoomNames))
                                        <th class="px-3 py-2 rooms-title">{{ $selected_room_type->name }}</th>
                                        @php $printedRoomNames[] = $selected_room_type->name; @endphp
                                    @endif
                                @endforeach
                                <th class="px-3 py-2">{!! trans('Currency') !!}</th>
                                <th class="px-3 py-2">{!! trans('City Tax') !!}</th>
                                <th class="px-3 py-2">{!! trans('Halfboard Supp p.p') !!}</th>
                                <th class="px-3 py-2">{!! trans('foc') !!}</th>
                                <th class="px-3 py-2">{!! trans('Max per group') !!}</th>
                                <th class="px-3 py-2">{!! trans('Portrage pp') !!}</th>
                                <th class="px-3 py-2">{!! trans('Hotel File') !!}</th>
                                <th class="px-3 py-2">{!! trans('Hotel Note') !!}</th>
                                <th class="px-3 py-2 text-right actions-button" style="width: 140px!important">{!! trans('main.Actions') !!}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($offersData as $offer)
                                <tr class="hover:bg-slate-50" style="background: {{ $offer->supplier_delete == 1 ? '#ffbbb2' : '' }};">
                                    <td class="px-3 py-2 font-mono text-xs text-slate-500">#{{ $offer->id }}</td>
                                    <td class="px-3 py-2 offer-status text-slate-700"
                                        data-status-link="{{ url('offer/updatestatus') }}/{{ $offer->id }}"
                                        data-name-status="{{ $offer->status_tms_name }}">{{ $offer->status_tms_name }}</td>
                                    <td class="px-3 py-2 text-slate-700">{{ $offer->status }}</td>
                                    @foreach($selected_room_types as $selected_room_type)
                                        <td class="px-3 py-2 text-slate-700">{{ $offer->room_prices[$selected_room_type->code] ?? 'N/A' }}</td>
                                    @endforeach
                                    <td class="px-3 py-2 text-slate-700">{{ $offer->currency }}</td>
                                    <td class="px-3 py-2 text-slate-700">{{ $offer->city_tax }}</td>
                                    <td class="px-3 py-2 text-slate-700">{{ $offer->halfboard }}</td>
                                    <td class="px-3 py-2 text-slate-700">{{ $offer->foc_after_every_pax }}</td>
                                    <td class="px-3 py-2 text-slate-700">{{ $offer->halfboardMax }}</td>
                                    <td class="px-3 py-2 text-slate-700">{{ $offer->portrage_perperson }}</td>
                                    <td class="px-3 py-2 text-slate-700">{{ $offer->hotel_file }}</td>
                                    <td class="px-3 py-2 text-slate-700 max-w-[20rem] truncate">{{ $offer->hotel_note }}</td>
                                    <td class="px-3 py-2"><div class="flex items-center justify-end gap-1">{!! $offer->action_buttons !!}</div></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif
</div>

{{-- Templates / send-email modal — Bootstrap-driven (#TemplatesModal,
     #templateSendForm, #template_selector, #templatesContent, #file,
     #file_name, #subject, #email, #id, #package_id, #tour_id, #offer,
     #offer_id) referenced by loadtemplate.js / sendTemplate(). --}}
<div class="modal fade" id="TemplatesModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
    <div class="modal-dialog modal-lg" style="width: 90%;">
        <form class="modal-content rounded border border-slate-200 bg-white shadow-lg" id="templateSendForm" enctype="multipart/form-data" action="/templates/api/send" method="POST">
            <input name="_token" type="hidden" value="{{ csrf_token() }}">
            <input name="id"         id="id"         type="hidden" value="">
            <input name="package_id" id="package_id" type="hidden" value="">
            <input name="tour_id"    id="tour_id"    type="hidden" value="">
            <input name="offer"      id="offer"      type="hidden" value="1">
            <input name="offer_id"   id="offer_id"   type="hidden" value="1">

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
                    <textarea name="templatesContent" id="templatesContent" placeholder="Non required Field" class="form-control" style="height: 400px; visibility: hidden; display: none;"></textarea>
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
@endsection

@section('post_scripts')
<script src="{{ asset('js/loadtemplate.js') }}"></script>
<script>
    $(document).ready(function () {
        // Click-through navigation on table-row helper (kept from legacy)
        $('.table-row').click(function () {
            var url = $(this).data('href');
            if (url) window.open(url, '_blank');
        });
    });

    let offerChanger = {
        init: () => { offerChanger.getStatuses(); },
        settings: () => {
            offerChanger.fieldName = '';
            offerChanger.fieldValue = '';
            offerChanger.updateLink = '';
            offerChanger.cityName = '';
            offerChanger.oldValue = '';
            offerChanger.element;
            offerChanger.countryList;
            offerChanger.statuses;
        },
        updateTour: () => {
            $.ajax({
                url: offerChanger.updateLink,
                method: 'PUT',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    fieldName: offerChanger.fieldName,
                    fieldValue: offerChanger.fieldValue,
                }
            }).done((res) => {
                if (res == 'wrong date') {
                    $(tourChanger.element).text(tourChanger.oldValue);
                }
                if (res.status_error) {
                    $('#error_tour').find('.error_tour_message').html(res.status_error);
                    $('#error_tour').modal();
                    $('#error_tour').on('hidden.bs.modal', function () {});
                }
                tourChanger.fieldName = '';
                tourChanger.fieldValue = '';
                tourChanger.updateLink = '';
                tourChanger.cityName = '';
                tourChanger.oldValue = '';
            });
        },
        handleChange: (that) => {
            $('table').off('click', 'tr td:not(:last-child):not(.fc-event-container)');
            offerChanger.fieldName = that.className.split('-')[1];
            offerChanger.updateLink = $(that).closest('tr').find('a.show-button').data('link');
            var value = $(that).text();
            var valueStatus = $(that).attr('data-name-status');
            if (offerChanger.fieldName == 'status') {
                $(that).attr('class', '');
                $(that).attr('class', 'touredit-process');
                $(that).text('');
                $(that).append('<form><select name="status_offer" class="offer-status form-control"></select></form>');
                $(offerChanger.statuses).each(function (key, status) {
                    let selected = (status.name == valueStatus) ? 'selected="selected"' : '';
                    $(that).find('.offer-status').append('<option value="' + status.id + '"' + ' ' + selected + '>' + status.name + '</option>');
                });
                $(that).find('.offer-status').on('change', function () {
                    $(that).attr('data-name-status', '');
                    offerChanger.fieldValue = $(this).val();
                    $(this).remove();
                    let statusName = $.grep(offerChanger.statuses, function (e) { return e.id == offerChanger.fieldValue; });
                    $(that).text(statusName[0].name);
                    $(that).attr('data-name-status', statusName[0].name);
                    offerChanger.updateLink = $(that).attr('data-status-link');
                    offerChanger.updateTour();
                    $('table').on('click', 'tr td:not(:last-child):not(.fc-event-container)', eventHandlerForoffer);
                    $(that).attr('class', '');
                    $(that).attr('class', 'offer-status');
                });
                $(document).keyup(function (e) {
                    if (e.keyCode === ESCAPE_CODE) {
                        $(that).find('.offer-status').change();
                    }
                });
                $(that).find('.offer-status').on('blur', function () {
                    $(that).find('.offer-status').change();
                });
            }
        },
        getStatuses: () => {
            $.ajax({ url: '/offer/api/status_list' }).done((res) => {
                offerChanger.statuses = JSON.parse(res);
            });
        }
    };

    function eventHandlerForoffer() {
        let elem = this;
        if (this.className.split('-')[1].trim() === 'process') return false;
        if (this.className.split('-')[1].trim() === 'status') {
            return offerChanger.handleChange(elem);
        } else {
            finder.class = elem.className.trim();
            finder.finder(elem);
        }
    }

    offerChanger.init();
    $(document).on('click', 'table:not(.finder-disable) tr td:not(:last-child):not(.fc-event-container)', eventHandlerForoffer);
</script>
<script src="{{ asset('js/comment.js') }}"></script>
@endsection
