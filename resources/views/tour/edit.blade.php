{{--
    /tour/{id}/edit — Tour edit form.
    Migrated chrome to Tailwind. JS hooks preserved:
      - Form action url("tour/{$tour->id}/update") unchanged
      - Field names: name, external_name, departure_date, retirement_date,
        assigned_user[], responsible_user, status, pax, child_count,
        ages[], prices[], pax_free, itinerary_tl, phone, files[]
      - Hidden inputs #tab, reference_id, calendar_edit preserved
      - #tour_form, #submitBtn, #tour_dates, #tour_date_id, #url
      - #passenger_count not used here; #pax is the input
      - #responsible_user, #status, #name, #external_name, #phone, etc.
      - .datepicker (bootstrap-datepicker binding)
      - .btn_for_select_room_type + #list_selected_room_types +
        .list_room_types + .select_room_type
      - #child_count + #child_details + addChildFields()
      - #files + #file-name-display (existing change listener)
      - Service-search modal #service-modal preserved
      - Post-scripts: hide_elements.js, rooms.js, tour.js,
        supplier-search.js, attachments.js
--}}
@extends('scaffold-interface.layouts.tabler-app')
@section('title','Edit Tour')

@section('content')
<x-ui.page-header
    :title="$tour->name ?: 'Edit tour'"
    :description="$tour->external_name ?: null"
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Tours', 'href' => route('tour.index')],
        ['label' => $tour->name ?: 'Tour'],
        ['label' => 'Edit'],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="{{ route('tour.show', $tour->id) }}" variant="ghost" icon="arrow-left">
            {{ trans('main.Back') }}
        </x-ui.button>
    </x-slot>
</x-ui.page-header>

@php
    $tab = '';
    $uri_parts = explode('?', \Request::fullUrl());
    if(count($uri_parts) > 1){
        $tab_parts = explode('=', $uri_parts[1]);
        if($tab_parts[0] == 'tab') $tab = $uri_parts[1];
    }
@endphp

{{-- Service-search modal — kept as Bootstrap modal since supplier-search.js
     opens it via bootstrap.Modal API. --}}
<div class="modal modal-blur fade" role="dialog" id="service-modal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('main.Addservice') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('supplier_show') }}">
                    <div class="mb-3">
                        <select id="service-select" class="form-select">
                            <option selected>{{ trans('main.All') }}</option>
                            @foreach($options as $option)
                                <option>{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
                <div class="table-responsive">
                    <table id="search-table" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>{{ trans('main.Name') }}</th>
                                <th>{{ trans('main.Address') }}</th>
                                <th>{{ trans('main.Phone') }}</th>
                                <th>{{ trans('main.ContactName') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@if(session('message_buses'))
    <div class="mb-4 flex items-start gap-3 rounded border border-info-600/20 bg-info-50 px-4 py-3 text-sm text-info-700">
        <x-ui.icon name="info" class="mt-0.5 text-info-600" />
        <div class="flex-1">{{ session('message_buses') }}</div>
    </div>
@endif

@if($errors->any())
    <div class="mb-4 rounded border border-danger-600/20 bg-danger-50 px-4 py-3 text-sm text-danger-700">
        <div class="flex items-center gap-2 font-medium">
            <x-ui.icon name="alert-octagon" class="text-danger-600" />
            Please correct the following:
        </div>
        <ul class="mt-2 list-disc pl-5 space-y-0.5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Driver/bus block-error (filled by tour.js when relevant). --}}
<div class="block-error-driver mb-4 hidden rounded border border-info-600/20 bg-info-50 px-4 py-3 text-sm text-info-700"></div>

<form method="POST" action="{{ url("tour/{$tour->id}/update") }}" enctype="multipart/form-data" id="tour_form" class="space-y-4">
    @csrf
    <input type="hidden" id="tab" name="tab" value="{{ $tab }}" />
    <input type="hidden" name="reference_id" value="{{ $tour->id }}" />
    <input type="hidden" name="calendar_edit" value="{{ $calendar_edit }}" />

    {{-- ============================================================ --}}
    {{-- Section 1: Identity                                            --}}
    {{-- ============================================================ --}}
    <div class="rounded border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-3 flex items-start gap-3">
            <div class="flex h-8 w-8 items-center justify-center rounded bg-primary-50 text-primary-600 shrink-0"><x-ui.icon name="plane" size="sm" /></div>
            <div class="flex-1 min-w-0">
                <h2 class="text-sm font-medium text-slate-700">Identity</h2>
            </div>
        </div>
        <div class="px-5 py-5 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="name" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.Name') }}</label>
                <input id="name" name="name" type="text" value="{{ old('name', $tour->name) }}"
                       class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            </div>
            <div>
                <label for="external_name" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.ExternalName') }}</label>
                <input id="external_name" name="external_name" type="text" value="{{ $tour->external_name }}"
                       class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            </div>
            <div>
                <label for="itinerary" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.tourleader') }}</label>
                {!! Form::text('itinerary_tl', old('itinerary_tl', $tour->itinerary_tl), ['id' => 'itinerary', 'class' => 'form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600']) !!}
            </div>
            <div>
                <label for="phone" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.Phone') }}</label>
                <input id="phone" name="phone" type="text" value="{{ old('phone', $tour->phone) }}"
                       class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- Section 2: Schedule                                            --}}
    {{-- ============================================================ --}}
    <div class="rounded border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-3 flex items-start gap-3">
            <div class="flex h-8 w-8 items-center justify-center rounded bg-primary-50 text-primary-600 shrink-0"><x-ui.icon name="calendar" size="sm" /></div>
            <div class="flex-1 min-w-0">
                <h2 class="text-sm font-medium text-slate-700">Schedule</h2>
            </div>
        </div>
        <div class="px-5 py-5 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="departure_date" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.DepDate') }}</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <x-ui.icon name="calendar" size="sm" />
                    </span>
                    {!! Form::text('departure_date', old('departure_date', $tour->departure_date), ['id' => 'departure_date', 'placeholder' => 'Select date', 'class' => 'form-control datepicker block w-full h-9 rounded border border-slate-300 bg-white pl-9 pr-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600']) !!}
                </div>
            </div>
            <div>
                <label for="retirement_date" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.RetDate') }}</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <x-ui.icon name="calendar" size="sm" />
                    </span>
                    {!! Form::text('retirement_date', old('retirement_date', $tour->retirement_date), ['id' => 'retirement_date', 'placeholder' => 'Select date', 'class' => 'form-control datepicker block w-full h-9 rounded border border-slate-300 bg-white pl-9 pr-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600']) !!}
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- Section 3: Staff (Status + Responsible + Assigned)             --}}
    {{-- ============================================================ --}}
    <div class="rounded border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-3 flex items-start gap-3">
            <div class="flex h-8 w-8 items-center justify-center rounded bg-primary-50 text-primary-600 shrink-0"><x-ui.icon name="users" size="sm" /></div>
            <div class="flex-1 min-w-0">
                <h2 class="text-sm font-medium text-slate-700">Status &amp; staff</h2>
            </div>
        </div>
        <div class="px-5 py-5 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="status" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.Status') }}</label>
                    <select name="status" id="status"
                            class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                        @foreach($statuses as $status)
                            <option value="{{ $status->id }}"
                                {{ ($errors != null && count($errors) > 0) ? (old('status') == $status->id ? 'selected' : '') : ($tour->status == $status->id ? 'selected' : '') }}>
                                {{ $status->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="responsible_user" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.ResponsibleUser') }}</label>
                    <select name="responsible_user" id="responsible_user"
                            class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                        <option value="0">{{ trans('main.Withoutresponsibleuser') }}</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ $tour->getResponsibleUser() && $tour->getResponsibleUser()->id == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-2">{{ trans('main.AssignedUser') }}</label>
                <div class="rounded border border-slate-200 bg-white max-h-[280px] overflow-y-auto p-3">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                        @foreach($users as $user)
                            <label class="flex items-center gap-2 rounded border border-slate-300 bg-white px-3 py-2 cursor-pointer hover:bg-slate-50 has-[:checked]:border-primary-600 has-[:checked]:bg-primary-50">
                                <input type="checkbox" name="assigned_user[]" value="{{ $user->id }}" {{ $user->selected ? 'checked' : '' }}
                                       class="user_checkboxes h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-600/30" />
                                <span class="text-sm text-slate-700">{{ $user->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- Section 4: Capacity + children                                 --}}
    {{-- ============================================================ --}}
    <div class="rounded border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-3 flex items-start gap-3">
            <div class="flex h-8 w-8 items-center justify-center rounded bg-primary-50 text-primary-600 shrink-0"><x-ui.icon name="user-check" size="sm" /></div>
            <div class="flex-1 min-w-0">
                <h2 class="text-sm font-medium text-slate-700">Capacity</h2>
            </div>
        </div>
        <div class="px-5 py-5 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="pax" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Pax</label>
                    <input id="pax" name="pax" type="number" value="{{ old('pax', $tour->pax) }}"
                           class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                </div>
                <div>
                    <label for="pax_free" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.PaxFree') }}</label>
                    <input id="pax_free" name="pax_free" type="number" value="{{ old('pax_free', $tour->getAttributes()['pax_free']) }}"
                           class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                </div>
                <div>
                    <label for="child_count" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Number of children</label>
                    @if(empty($tour->childrens))
                        <input type="number" id="child_count" name="child_count" min="0" value="{{ old('child_count', 0) }}"
                               class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                    @else
                        <input type="number" id="child_count" name="child_count" min="0" value="{{ old('child_count', count($tour->childrens)) }}"
                               class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                    @endif
                </div>
            </div>

            @php $i = 0; @endphp
            <div id="child_details" class="space-y-2">
                @if(!empty($tour->childrens))
                    @foreach($tour->childrens as $chd)
                        @php $i++ @endphp
                        <div class="child-field grid grid-cols-1 md:grid-cols-2 gap-3 rounded border border-slate-200 bg-slate-50 p-3">
                            <div>
                                <label for="age_{{ $i }}" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Age of child {{ $i }}</label>
                                <input type="number" id="age_{{ $i }}" name="ages[]" min="0" value="{{ old('ages.'.$loop->index, $chd->age) }}"
                                       class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                            </div>
                            <div>
                                <label for="price_{{ $i }}" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Price</label>
                                <input type="number" id="price_{{ $i }}" name="prices[]" step="0.01" value="{{ old('prices.'.$loop->index, $chd->price) }}"
                                       class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <div>
                <button type="button" onclick="addChildFields()"
                        class="inline-flex h-9 items-center gap-2 rounded border border-slate-300 bg-white px-3 text-sm font-medium text-slate-700 hover:bg-slate-50">
                    <x-ui.icon name="refresh-cw" size="sm" />
                    Update child fields
                </button>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- Section 5: Rooms                                               --}}
    {{-- ============================================================ --}}
    <div class="rounded border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-3 flex items-start gap-3">
            <div class="flex h-8 w-8 items-center justify-center rounded bg-primary-50 text-primary-600 shrink-0"><x-ui.icon name="bed" size="sm" /></div>
            <div class="flex-1 min-w-0">
                <h2 class="text-sm font-medium text-slate-700">{{ trans('main.RoomTypes') }}</h2>
            </div>
        </div>
        <div class="px-5 py-5 space-y-3">
            <div id="list_selected_room_types" class="space-y-2">
                @if(!empty($selected_room_types))
                    @foreach($selected_room_types as $item)
                        @include('component.item_hotel_room_type', ['room_type' => $item])
                    @endforeach
                @endif
            </div>

            <button type="button"
                    class="btn_for_select_room_type inline-flex h-9 items-center gap-2 rounded border border-slate-300 bg-white px-3 text-sm font-medium text-slate-700 hover:bg-slate-50">
                <x-ui.icon name="plus" size="sm" />
                {{ trans('main.SelectRooms') }}
            </button>

            <ul class="list_room_types hidden">
                <ul class="list_room_types" style="display: block; z-index:999;">
                    @if(!empty($room_types))
                        @foreach($room_types as $room_type)
                            <li class="select_room_type">
                                <label>{{ $room_type->name }}</label>
                                <input type="text" data-info="{{ $room_type->id }}" hidden value="{{ $room_type }}">
                            </li>
                        @endforeach
                    @endif
                </ul>
            </ul>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- Section 6: Files                                               --}}
    {{-- ============================================================ --}}
    <div class="rounded border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-3 flex items-start gap-3">
            <div class="flex h-8 w-8 items-center justify-center rounded bg-primary-50 text-primary-600 shrink-0"><x-ui.icon name="paperclip" size="sm" /></div>
            <div class="flex-1 min-w-0">
                <h2 class="text-sm font-medium text-slate-700">{{ trans('main.Files') }}</h2>
            </div>
        </div>
        <div class="px-5 py-5 space-y-5">
            @component('component.file_upload_field', ['enableAjaxUploads' => false])@endcomponent

            <div>
                <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-2">{{ trans('main.imageforlanding') }}</label>

                <div class="rounded border border-slate-200 bg-slate-50 mb-3">
                    @if($tour->attachments()->first() != null)
                        <img src="{{ $tour->attachments()->first()->url }}" class="block w-full rounded" style="max-height: 300px; object-fit: cover;" />
                    @else
                        <div class="py-10 text-center text-slate-400">
                            <x-ui.icon name="image" size="lg" class="mx-auto mb-2" />
                            <p class="text-sm">No image uploaded</p>
                        </div>
                    @endif
                </div>

                <div class="flex items-stretch gap-0">
                    <input type="text" id="file-name-display" readonly placeholder="No file chosen"
                           class="form-control flex-1 h-9 rounded-l border border-r-0 border-slate-300 bg-white px-3 text-sm text-slate-500" />
                    <label for="files" class="inline-flex h-9 items-center gap-2 rounded-r border border-slate-300 bg-primary-600 px-3 text-sm font-medium text-white hover:bg-primary-700 cursor-pointer">
                        <x-ui.icon name="upload" size="sm" />
                        Browse
                    </label>
                    <input type="file" name="files[]" id="files" class="fileToUpload" accept="image/*" style="display:none;" />
                </div>
            </div>

            <span id="url" hidden data-url="{{ route('images.savefile') }}"></span>
        </div>
    </div>

    {{-- Form footer --}}
    <div class="sticky bottom-0 -mx-4 sm:mx-0 sm:static sm:rounded sm:border sm:border-slate-200 bg-white sm:bg-slate-50 px-4 sm:px-5 py-3 border-t border-slate-200 sm:border-t-0 sm:border flex items-center justify-end gap-2 shadow-[0_-4px_8px_-4px_rgba(15,23,42,0.05)] sm:shadow-none">
        <x-ui.button as="a" href="{{ \App\Helper\AdminHelper::getBackButton(route('tour.index')) }}" variant="secondary">{{ trans('main.Cancel') }}</x-ui.button>
        <x-ui.button type="submit" id="submitBtn" variant="primary" icon="save">{{ trans('main.Save') }}</x-ui.button>
    </div>
</form>

<span id="tour_dates" data-departure_date="{{ $tour->departure_date }}" data-retirement_date="{{ $tour->retirement_date }}"></span>
<span id="tour_date_id" data-tour-id="{{ $tour->id }}"></span>
@endsection

@push('scripts')
<script type="text/javascript" src='{{ asset('js/hide_elements.js') }}'></script>
<script type="text/javascript" src='{{ asset('js/rooms.js') }}'></script>
<script type="text/javascript" src='{{ asset('js/tour.js') }}'></script>
<script type="text/javascript" src='{{ asset('js/supplier-search.js') }}'></script>
<script type="text/javascript" src='{{ asset('js/attachments.js') }}'></script>
<script>
    function addChildFields() {
        var count = document.getElementById('child_count').value;
        var container = document.getElementById('child_details');
        container.innerHTML = '';
        for (var i = 1; i <= count; i++) {
            var div = document.createElement('div');
            div.className = 'child-field grid grid-cols-1 md:grid-cols-2 gap-3 rounded border border-slate-200 bg-slate-50 p-3';
            div.innerHTML = `
                <div>
                    <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1" for="age_${i}">Age of child ${i}</label>
                    <input type="number" id="age_${i}" name="ages[]" min="0" class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                </div>
                <div>
                    <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1" for="price_${i}">Price</label>
                    <input type="number" id="price_${i}" name="prices[]" step="0.01" class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                </div>
            `;
            container.appendChild(div);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        var fileInput = document.getElementById('files');
        if (fileInput) {
            fileInput.addEventListener('change', function (e) {
                var fileName = e.target.files[0] ? e.target.files[0].name : 'No file chosen';
                document.getElementById('file-name-display').value = fileName;
            });
        }

        var form = document.getElementById('tour_form');
        var submitBtn = document.getElementById('submitBtn');
        if (form && submitBtn) {
            form.addEventListener('submit', function () {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="inline-block h-4 w-4 mr-2 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>Saving...';
            });
        }
    });
</script>
@endpush
