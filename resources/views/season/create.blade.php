@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Season')

@section('content')
<x-ui.page-header
    title="New season price"
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Hotel', 'href' => route('hotel.show', ['hotel' => $id, 'tab' => 'season_tab'])],
        ['label' => 'Season Price', 'href' => route('hotel.show', ['hotel' => $id, 'tab' => 'season_tab'])],
        ['label' => 'New'],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="javascript:history.back()" variant="ghost" icon="arrow-left">{{ trans('main.Back') }}</x-ui.button>
    </x-slot>
</x-ui.page-header>

@if(count($errors) > 0)
    <div class="mb-4 rounded border border-danger-600/20 bg-danger-50 px-4 py-3 text-sm text-danger-700">
        <div class="flex items-center gap-2 font-medium"><x-ui.icon name="alert-octagon" class="text-danger-600" />Please correct the following:</div>
        <ul class="mt-2 list-disc pl-5 space-y-0.5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

<form method="POST" action="{{ route('store_season') }}" class="space-y-4">
    {{ csrf_field() }}
    <input type="hidden" id="hotel_id" name="hotel_id" value="{{ $id }}">
    <input type="hidden" id="agreement_id" name="agreement_id" value="">

    <div class="rounded border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-3 flex items-start gap-3">
            <div class="flex h-8 w-8 items-center justify-center rounded bg-primary-50 text-primary-600 shrink-0"><x-ui.icon name="sun" size="sm" /></div>
            <div class="flex-1 min-w-0">
                <h2 class="text-sm font-medium text-slate-700">Season details</h2>
            </div>
        </div>
        <div class="px-5 py-5 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.Name') !!} <span class="text-danger-600">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}"
                       class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            </div>
            <div>
                <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.StartDate') !!} <span class="text-danger-600">*</span></label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><x-ui.icon name="calendar" size="sm" /></span>
                    <input type="text" name="start_date" id="start_date" value="{{ old('start_date') }}"
                           class="form-control datepickernoyear block w-full h-9 rounded border border-slate-300 bg-white pl-9 pr-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.EndDate') !!} <span class="text-danger-600">*</span></label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><x-ui.icon name="calendar" size="sm" /></span>
                    <input type="text" name="end_date" id="end_date" value="{{ old('end_date') }}"
                           class="form-control datepickernoyear block w-full h-9 rounded border border-slate-300 bg-white pl-9 pr-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                </div>
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.RoomTypes') !!}</label>
                <div id="list_selected_room_types" class="space-y-2 mb-2"></div>
                <button class="btn_for_select_room_type inline-flex h-9 items-center gap-2 rounded border border-slate-300 bg-white px-3 text-sm font-medium text-slate-700 hover:bg-slate-50" type="button">
                    <x-ui.icon name="plus" size="sm" />{!! trans('main.SelectRooms') !!}
                </button>
                <ul class="list_room_types hidden">
                    <ul class="list_room_types" style="display: block; z-index:999;">
                        @if(!empty($room_types))
                            @foreach($room_types as $room_type)
                                <li class="select_room_type">
                                    <label>{{ $room_type->name }}</label>
                                    <input type="text" data-agreement="" data-info="{{ $room_type->id }}" hidden value="{{ $room_type }}">
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </ul>
            </div>
            <div>
                <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.Type') !!}</label>
                <select id="type" name="type"
                        class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                    @foreach($season_types as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.Description') !!}</label>
                {!! Form::textarea('description', old('description'), ['id' => 'description', 'rows' => 3, 'class' => 'form-control block w-full rounded border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600']) !!}
            </div>
        </div>
    </div>

    <div class="sticky bottom-0 -mx-4 sm:mx-0 sm:static sm:rounded sm:border sm:border-slate-200 bg-white sm:bg-slate-50 px-4 sm:px-5 py-3 border-t border-slate-200 sm:border-t-0 sm:border flex items-center justify-end gap-2 shadow-[0_-4px_8px_-4px_rgba(15,23,42,0.05)] sm:shadow-none">
        <x-ui.button as="a" href="javascript:history.back()" variant="secondary">{{ trans('main.Cancel') }}</x-ui.button>
        <x-ui.button type="submit" variant="primary" icon="save">{{ trans('main.Save') }}</x-ui.button>
    </div>
</form>

<style>.datepicker { z-index: 500 !important; }</style>
@endsection

@push('scripts')
<script type="text/javascript" src='{{ asset('js/seasons_rooms.js') }}'></script>
<script type="text/javascript" src='{{ asset('js/hide_elements.js') }}'></script>
@endpush
