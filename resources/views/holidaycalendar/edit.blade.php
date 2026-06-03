@extends('scaffold-interface.layouts.tabler-app')
@section('title','Edit holiday')

@section('content')
<x-ui.page-header
    title="Holiday"
    description="Edit holiday entry"
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Holidays', 'href' => route('holiday.index')],
        ['label' => 'Edit'],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="javascript:history.back()" variant="ghost" icon="arrow-left">
            {{ trans('main.Back') }}
        </x-ui.button>
        <span id="help" class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-slate-700 cursor-pointer relative">
            <x-ui.icon name="help-circle" size="sm" />
            <span class="hidden sm:inline">Legend</span>
            @include('legend.quotation_legend_edit')
        </span>
    </x-slot>
</x-ui.page-header>

<div class="space-y-4">

    @if (isset($errors) && $errors->any())
        <div class="rounded border border-danger-600/20 bg-danger-50 px-4 py-3 text-sm text-danger-700">
            <div class="flex items-center gap-2 font-medium mb-1">
                <x-ui.icon name="alert-octagon" class="text-danger-600" />
                Please correct the following:
            </div>
            <ul class="list-disc pl-5 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{!! url('holiday') . '/' . $holidaycalendarday->id . '/update' !!}" id="update_holiday_form" class="space-y-4">
        <input type="hidden" name="_token" value="{{ Session::token() }}">

        <div class="rounded border border-slate-200 bg-white shadow-subtle">
            <div class="border-b border-slate-200 px-5 py-3 flex items-center gap-2">
                <x-ui.icon name="calendar-event" class="text-primary-600" />
                <h2 class="text-sm font-semibold text-slate-900">Holiday details</h2>
            </div>

            <div class="px-5 py-5">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                    {{-- Left: holiday fields --}}
                    <div class="lg:col-span-5 space-y-4">
                        <div>
                            <label for="name" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">
                                {{ trans('main.Name') }}
                            </label>
                            <input type="text"
                                   id="name"
                                   name="name"
                                   value="{{ (isset($errors) && $errors->any()) ? '' : ($holidaycalendarday->name ?? '') }}{{ old('name') }}"
                                   class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                        </div>

                        {{-- Color picker — hidden by default; .color_view shown by JS colorView() --}}
                        <div class="form-group color_view" style="display: none;">
                            <label for="backgroundcolor" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">
                                {{ trans('main.Color') }}
                            </label>
                            <div id="cp2" class="input-group colorpicker-component flex items-stretch rounded border border-slate-300 bg-white shadow-subtle overflow-hidden">
                                <input type="text"
                                       id="backgroundcolor"
                                       name="backgroundcolor"
                                       value="@if (old('backgroundcolor')){{ old('backgroundcolor') }}@else{{ $holidaycalendarday->backgroundcolor ?? '' }}@endif"
                                       class="form-control block w-full px-3 h-9 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-primary-600/30 border-0">
                                <span class="input-group-addon flex items-center justify-center w-10 bg-slate-50 border-l border-slate-200">
                                    <i></i>
                                </span>
                            </div>
                        </div>

                        {{-- #color_status carries the colour value into the JS hook --}}
                        <span id="color_status" data-attr="{{ $holidaycalendarday->color ?? '' }}" class="hidden"></span>

                        <div>
                            <label for="start_time" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">
                                {{ trans('main.Date') }}
                            </label>
                            <div class="input-group date relative">
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                    <x-ui.icon name="calendar" size="sm" />
                                </span>
                                <input class="form-control datepicker block w-full h-9 rounded border border-slate-300 bg-white pl-9 pr-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600"
                                       id="start_time"
                                       name="start_time"
                                       type="text"
                                       autocomplete="off"
                                       value="@if (old('start_time')){{ old('start_time') }}@else{{ $holidaycalendarday->start_time ?? '' }}@endif">
                            </div>
                        </div>
                    </div>

                    {{-- Right: tour-packages + itinerary tab pane injection points --}}
                    <div class="lg:col-span-7">
                        <div class="tour-packages"></div>
                        <div id="itinerary" class="tab-pane fade"></div>
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-200 bg-slate-50 px-5 py-3 flex items-center justify-end gap-2 rounded-b">
                <x-ui.button
                    as="a"
                    :href="\App\Helper\AdminHelper::getBackButton(route('restaurant.index'))"
                    variant="secondary"
                >
                    {{ trans('main.Cancel') }}
                </x-ui.button>
                <button type="button"
                        class="btn btn-success update-holiday inline-flex items-center gap-1.5 rounded bg-primary-600 px-4 h-9 text-sm font-medium text-white hover:bg-primary-700">
                    <x-ui.icon name="save" size="sm" />
                    {{ trans('main.Save') }}
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    function colorView(_this) {
        var color = $('#color_status').attr('data-attr');
        $('#color_field').val('');
        $('#block_color_field_bg').css({'background-color' : 'transparent'});
        $('.color_view').css({'display': 'block'});
        $('.update-holiday').click(function(){
            $('#update_holiday_form').submit();
        });
    }

    $(document).ready(function (e) {
        colorView($('#type_status'));
        var color = $('#color_status').attr('data-attr');
        $('#color_field').val(color);
    });
</script>
@endsection

@section('colorpicker-js')
    <script src="{{ asset('js/colorpicker.js') }}"></script>
    <div class="colorpicker dropdown-menu colorpicker-hidden colorpicker-with-alpha colorpicker-right"><div class="colorpicker-saturation"><i><b></b></i></div><div class="colorpicker-hue"><i></i></div><div class="colorpicker-alpha"><i></i></div><div class="colorpicker-color"><div></div></div><div class="colorpicker-selectors"></div></div>
    <script>
        $(function() {
            $('#cp2').colorpicker();
        });
    </script>
@endsection

@section('colorpicker-css')
    <link rel="stylesheet" href="{{ asset('css/colorpicker.css') }}">
@endsection
