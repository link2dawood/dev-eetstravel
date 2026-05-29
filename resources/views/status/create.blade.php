@extends('scaffold-interface.layouts.tabler-app')
@section('title','New status')

@section('colorpicker-css')
    <link rel="stylesheet" href="{{ asset('css/colorpicker.css') }}">
@endsection

@section('content')
<x-ui.page-header
    title="New status"
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Statuses', 'href' => route('status.index')],
        ['label' => 'New'],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="{{ route('status.index') }}" variant="ghost" icon="arrow-left">{{ trans('main.Back') }}</x-ui.button>
    </x-slot>
</x-ui.page-header>

@if(count($errors) > 0)
    <div class="mb-4 rounded border border-danger-600/20 bg-danger-50 px-4 py-3 text-sm text-danger-700">
        <div class="flex items-center gap-2 font-medium"><x-ui.icon name="alert-octagon" class="text-danger-600" />Please correct the following:</div>
        <ul class="mt-2 list-disc pl-5 space-y-0.5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

<form method="POST" action="{!! url('status') !!}" class="space-y-4">
    <input type="hidden" name="_token" value="{{ Session::token() }}">
    <input type="hidden" name="modal_create" value="0">

    <div class="rounded border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-3 flex items-start gap-3">
            <div class="flex h-8 w-8 items-center justify-center rounded bg-primary-50 text-primary-600 shrink-0"><x-ui.icon name="circle-check" size="sm" /></div>
            <div class="flex-1 min-w-0">
                <h2 class="text-sm font-medium text-slate-700">Status details</h2>
            </div>
        </div>
        <div class="px-5 py-5 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.Name') !!} <span class="text-danger-600">*</span></label>
                <input type="text" value="{{ old('name') }}" name="name" required
                       oninvalid="this.setCustomValidity('Field required for filling')" onchange="this.setCustomValidity('')"
                       class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
            </div>

            <div>
                <label for="type_status" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.Type') !!}</label>
                <select name="type" id="type_status" onchange="colorView($(this));"
                        class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                    @foreach($status_types as $status_type)
                        <option {{ old('type') == $status_type->type ? 'selected' : '' }}
                                value="{{ $status_type->type }}">{{ $status_type->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.Sortorder') !!} <span class="text-danger-600">*</span></label>
                <input type="text" value="{{ old('sort_order') }}" name="sort_order" required
                       oninvalid="this.setCustomValidity('Field required for filling')" onchange="this.setCustomValidity('')"
                       class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
            </div>

            <div class="color_view" style="display: none">
                <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.Color') !!}</label>
                <div id="cp2" class="input-group colorpicker-component">
                    <input type="text" value="{{ old('color') }}" name="color"
                           class="form-control block w-full h-9 rounded-l border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                    <span class="input-group-addon inline-flex h-9 w-9 items-center justify-center rounded-r border border-l-0 border-slate-300 bg-slate-50"><i></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="sticky bottom-0 -mx-4 sm:mx-0 sm:static sm:rounded sm:border sm:border-slate-200 bg-white sm:bg-slate-50 px-4 sm:px-5 py-3 border-t border-slate-200 sm:border-t-0 sm:border flex items-center justify-end gap-2 shadow-[0_-4px_8px_-4px_rgba(15,23,42,0.05)] sm:shadow-none">
        <x-ui.button as="a" href="{{ route('status.index') }}" variant="secondary">{{ trans('main.Cancel') }}</x-ui.button>
        <x-ui.button type="submit" variant="primary" icon="save">{{ trans('main.Save') }}</x-ui.button>
    </div>
</form>

<script>
    function colorView(_this) {
        if ($(_this).val() === 'tour' || $(_this).val() === 'bus') {
            $('.color_view').css({'display': 'block'});
        } else {
            $('.color_view').css({'display': 'none'});
        }
    }
    $(document).ready(function () {
        colorView($('#type_status'));
    });
</script>
@endsection

@section('colorpicker-js')
    <script src="{{ asset('js/colorpicker.js') }}"></script>
    <div class="colorpicker dropdown-menu colorpicker-hidden colorpicker-with-alpha colorpicker-right"><div class="colorpicker-saturation"><i><b></b></i></div><div class="colorpicker-hue"><i></i></div><div class="colorpicker-alpha"><i></i></div><div class="colorpicker-color"><div></div></div><div class="colorpicker-selectors"></div></div>
    <script>$(function () { $('#cp2').colorpicker(); });</script>
@endsection
