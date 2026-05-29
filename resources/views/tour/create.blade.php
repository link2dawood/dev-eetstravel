{{--
    /tour/create — Tour or Quotation create form.
    $isQuotation flips a few sections (status hidden, files+landing image
    suppressed). Migrated chrome to Tailwind; every JS hook is preserved
    verbatim:
      - Form action url('tour/save') unchanged
      - Field names: name, departure_date, retirement_date, status,
        responsible_user, pax, pax_free, child_count, ages[], prices[],
        is_quotation, files[]
      - #passenger_count, #child_count, #child_details, #responsible_user,
        #imgInp, #pic, #file-caption-name
      - .datepicker class (bootstrap-datepicker binding)
      - .btn_for_select_room_type + #list_selected_room_types +
        .list_room_types + .select_room_type
      - .user_checkboxes (used by setInterval handleCheckboxes loop)
      - readURL() + addChildFields() globals
      - js-validate partial for quotation mode
      - Post-scripts: rooms.js, hide_elements.js, tour.js,
        supplier-search.js, attachments.js
--}}
@extends('scaffold-interface.layouts.tabler-app')
@section('title','Create')

@section('content')
<x-ui.page-header
    :title="$title ?? ($isQuotation ? 'Create Quotation' : 'Create Tour')"
    :description="$subTitle ?? null"
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Tours', 'href' => route('tour.index')],
        ['label' => $isQuotation ? 'Create Quotation' : 'Create Tour'],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="javascript:history.back()" variant="ghost" icon="arrow-left">
            {{ trans('main.Back') }}
        </x-ui.button>
    </x-slot>
</x-ui.page-header>

@if(count($errors) > 0)
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

<form method="POST" action="{{ url('tour/save') }}" enctype="multipart/form-data" class="space-y-4">
    {{ csrf_field() }}
    <input type="hidden" name="_token" value="{{ Session::token() }}">

    @if($isQuotation)
        @include('component.js-validate')
    @endif

    {{-- ============================================================ --}}
    {{-- Section 1: Identity                                            --}}
    {{-- ============================================================ --}}
    <div class="rounded border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-3 flex items-start gap-3">
            <div class="flex h-8 w-8 items-center justify-center rounded bg-primary-50 text-primary-600 shrink-0"><x-ui.icon name="plane" size="sm" /></div>
            <div class="flex-1 min-w-0">
                <h2 class="text-sm font-medium text-slate-700">Identity</h2>
                <p class="text-xs text-slate-500">Name + capacity figures.</p>
            </div>
        </div>
        <div class="px-5 py-5 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label for="name" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">
                    {{ trans('main.Name') }} <span class="text-danger-600">*</span>
                </label>
                {!! Form::text('name', '', ['id' => 'name', 'class' => 'form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600']) !!}
            </div>
            <div>
                <label for="passenger_count" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Pax</label>
                {!! Form::text('pax', '', ['id' => 'passenger_count', 'class' => 'form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600']) !!}
            </div>
            <div>
                <label for="pax_free" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.PaxFree') }}</label>
                {!! Form::text('pax_free', '', ['id' => 'pax_free', 'class' => 'form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600']) !!}
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
                <p class="text-xs text-slate-500">Departure and return dates.</p>
            </div>
        </div>
        <div class="px-5 py-5 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="departure_date" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">
                    {{ trans('main.DepDate') }} <span class="text-danger-600">*</span>
                </label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <x-ui.icon name="calendar" size="sm" />
                    </span>
                    {!! Form::text('departure_date', '', ['id' => 'departure_date', 'autocomplete' => 'off', 'class' => 'form-control datepicker block w-full h-9 rounded border border-slate-300 bg-white pl-9 pr-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600']) !!}
                </div>
            </div>
            <div>
                <label for="retirement_date" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">
                    {{ trans('main.RetDate') }} <span class="text-danger-600">*</span>
                </label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <x-ui.icon name="calendar" size="sm" />
                    </span>
                    {!! Form::text('retirement_date', '', ['id' => 'retirement_date', 'class' => 'form-control datepicker block w-full h-9 rounded border border-slate-300 bg-white pl-9 pr-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600']) !!}
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- Section 3: Staff (Status + Responsible) — tour-only            --}}
    {{-- ============================================================ --}}
    @if(!$isQuotation)
        <div class="rounded border border-slate-200 bg-white">
            <div class="border-b border-slate-200 px-5 py-3 flex items-start gap-3">
                <div class="flex h-8 w-8 items-center justify-center rounded bg-primary-50 text-primary-600 shrink-0"><x-ui.icon name="users" size="sm" /></div>
                <div class="flex-1 min-w-0">
                    <h2 class="text-sm font-medium text-slate-700">Status &amp; staff</h2>
                </div>
            </div>
            <div class="px-5 py-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="status" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.Status') }}</label>
                    <select name="status" id="status"
                            class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                        @foreach($statuses as $status)
                            <option {{ old('status') == $status->id ? 'selected' : '' }} value="{{ $status->id }}">{{ $status->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="responsible_user" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.ResponsibleUser') }}</label>
                    <select name="responsible_user" id="responsible_user"
                            class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                        <option value="0">{{ trans('main.Withoutresponsibleuser') }}</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @else
        {{-- Quotation mode pins status to "pending" --}}
        {!! Form::hidden('status', 1) !!}
    @endif

    {{-- ============================================================ --}}
    {{-- Section 4: Children                                            --}}
    {{-- ============================================================ --}}
    <div class="rounded border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-3 flex items-center gap-3">
            <div class="flex h-8 w-8 items-center justify-center rounded bg-primary-50 text-primary-600 shrink-0"><x-ui.icon name="baby" size="sm" /></div>
            <div class="flex-1 min-w-0">
                <h2 class="text-sm font-medium text-slate-700">Children</h2>
            </div>
        </div>
        <div class="px-5 py-5 space-y-3">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-end">
                <div>
                    <label for="child_count" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Number of children</label>
                    <input type="number" id="child_count" name="child_count" min="0"
                           class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                </div>
                <div>
                    <button type="button" onclick="addChildFields()"
                            class="inline-flex h-9 items-center gap-2 rounded border border-slate-300 bg-white px-3 text-sm font-medium text-slate-700 hover:bg-slate-50">
                        <x-ui.icon name="plus" size="sm" />
                        Add child fields
                    </button>
                </div>
            </div>
            {{-- addChildFields() injects per-child <input> pairs here. --}}
            <div id="child_details" class="space-y-2"></div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- Section 5: Rooms                                               --}}
    {{-- ============================================================ --}}
    <div class="rounded border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-3 flex items-center gap-3">
            <div class="flex h-8 w-8 items-center justify-center rounded bg-primary-50 text-primary-600 shrink-0"><x-ui.icon name="bed" size="sm" /></div>
            <div class="flex-1 min-w-0">
                <h2 class="text-sm font-medium text-slate-700">{{ trans('main.RoomTypes') }}</h2>
            </div>
        </div>
        <div class="px-5 py-5 space-y-3">
            {{-- rooms.js fills this via the .btn_for_select_room_type click handler. --}}
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

            {{-- Hidden room-type picker used by rooms.js — list_room_types is read on btn click. --}}
            <ul class="list_room_types hidden">
                <ul class="list_room_types" style="display: block; z-index:999;">
                    @foreach($room_types as $room_type)
                        <li class="select_room_type">
                            <label>{{ $room_type->name }}</label>
                            <input type="text" data-info="{{ $room_type->id }}" hidden value="{{ $room_type }}">
                        </li>
                    @endforeach
                </ul>
            </ul>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- Section 6: Files + Landing image (tour-only)                   --}}
    {{-- ============================================================ --}}
    @if(!$isQuotation)
        <div class="rounded border border-slate-200 bg-white">
            <div class="border-b border-slate-200 px-5 py-3 flex items-start gap-3">
                <div class="flex h-8 w-8 items-center justify-center rounded bg-primary-50 text-primary-600 shrink-0"><x-ui.icon name="paperclip" size="sm" /></div>
                <div class="flex-1 min-w-0">
                    <h2 class="text-sm font-medium text-slate-700">{{ trans('main.Files') }}</h2>
                    <p class="text-xs text-slate-500">Vouchers, supplier sheets, etc.</p>
                </div>
            </div>
            <div class="px-5 py-5 space-y-5">
                @component('component.file_upload_field')@endcomponent

                <div>
                    <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-2">
                        {{ trans('main.imageforlanding') }}
                    </label>
                    <div class="rounded border border-slate-200 bg-slate-50 p-3 mb-3">
                        <p class="text-xs text-slate-500 mb-2 text-center">Image for landing page</p>
                        <img id="pic" src="" class="block w-full h-auto max-h-[300px] object-cover rounded" />
                    </div>
                    <div class="flex items-stretch gap-0">
                        <div class="flex-1 inline-flex items-center h-9 rounded-l border border-r-0 border-slate-300 bg-white px-3 text-sm text-slate-500">
                            <span class="file-caption-name" id="file-caption-name">No file chosen</span>
                        </div>
                        <label for="imgInp" class="inline-flex h-9 items-center gap-2 rounded-r border border-slate-300 bg-primary-600 px-3 text-sm font-medium text-white hover:bg-primary-700 cursor-pointer">
                            <x-ui.icon name="folder-open" size="sm" />
                            Browse
                            <input type="file" name="files[]" id="imgInp" class="fileToUpload" multiple style="display:none;" />
                        </label>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {!! Form::hidden('is_quotation', $isQuotation ? 1 : 0) !!}

    {{-- Form footer --}}
    <div class="sticky bottom-0 -mx-4 sm:mx-0 sm:static sm:rounded sm:border sm:border-slate-200 bg-white sm:bg-slate-50 px-4 sm:px-5 py-3 border-t border-slate-200 sm:border-t-0 sm:border flex items-center justify-end gap-2 shadow-[0_-4px_8px_-4px_rgba(15,23,42,0.05)] sm:shadow-none">
        <x-ui.button as="a" href="{{ route('tour.index') }}" variant="secondary">{{ trans('main.Cancel') }}</x-ui.button>
        <x-ui.button type="submit" variant="primary" icon="save">{{ trans('main.Save') }}</x-ui.button>
    </div>
</form>

@push('scripts')
<script type="text/javascript" src='{{ asset('js/rooms.js') }}'></script>
<script type="text/javascript" src='{{ asset('js/hide_elements.js') }}'></script>
<script type="text/javascript" src='{{ asset('js/tour.js') }}'></script>
<script type="text/javascript" src='{{ asset('js/supplier-search.js') }}'></script>
<script type="text/javascript" src='{{ asset('js/attachments.js') }}'></script>

<script type="text/javascript">
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#pic').attr('src', e.target.result);
                $('#file-caption-name').html(input.files[0].name);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    $("#imgInp").change(function () { readURL(this); });

    function handleCheckboxes() {
        const checkboxes = document.querySelectorAll('.user_checkboxes');
        checkboxes.forEach(function (checkbox) {
            checkbox.addEventListener("click", function () {
                console.log("User ID " + this.value + " is now " + (this.checked ? "selected" : "deselected"));
            });
        });
    }
    handleCheckboxes();
    setInterval(handleCheckboxes, 500);

    function addChildFields() {
        var count = document.getElementById('child_count').value;
        var container = document.getElementById('child_details');
        container.innerHTML = '';
        for (var i = 1; i <= count; i++) {
            var div = document.createElement('div');
            div.className = 'grid grid-cols-1 md:grid-cols-2 gap-3 rounded border border-slate-200 bg-slate-50 p-3';
            div.innerHTML = `
                <div>
                    <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1" for="age_${i}">Age of child ${i}</label>
                    <input type="number" id="age_${i}" name="ages[]" class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" min="0">
                </div>
                <div>
                    <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1" for="price_${i}">Price</label>
                    <input type="number" id="price_${i}" name="prices[]" step="0.01" class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                </div>
            `;
            container.appendChild(div);
        }
    }
</script>
@endpush
@endsection
