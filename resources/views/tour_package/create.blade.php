@extends('scaffold-interface.layouts.tabler-app')
@section('title','Create tour package')

@section('content')
<x-ui.page-header
    title="Tour package"
    description="Create a new service for this tour day."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Tours', 'href' => route('tour.index')],
        ['label' => 'Tour edit', 'href' => url('/tour/' . $tour_package->tour . '/edit')],
        ['label' => 'Create'],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="{{ url('/tour/' . $tour_package->tour . '/edit') }}" variant="ghost" icon="arrow-left">
            {{ trans('main.Cancel') }}
        </x-ui.button>
    </x-slot>
</x-ui.page-header>

<div class="space-y-4">

    {{-- Validation banner --}}
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

    {{-- Service-type chooser (component preserved verbatim). The legacy hook
         .tour-package-services is what the JS reads / rewrites at runtime. --}}
    <div class="tour-package-services">
        @component('component.packages_service', [
            'tour_package' => $tour_package,
            'serviceTypes' => $serviceTypes,
            'servicesData' => $servicesData,
        ])
        @endcomponent
    </div>

    <form method='POST' action='{!! url("tour_package") !!}' id="tour_package_create_form" class="space-y-4">
        <input type='hidden' name='_token' value='{{ Session::token() }}'>

        {{-- Main info card --}}
        <div class="rounded border border-slate-200 bg-white shadow-subtle">

            <div class="border-b border-slate-200 px-5 py-3 flex items-center gap-2">
                <x-ui.icon name="package" class="text-primary-600" />
                <h3 class="text-sm font-semibold text-slate-900">
                    {{ trans('main.Service') }}:
                    <span id="service_name" class="capitalize text-slate-700 font-normal">
                        {!! isset($serviceName) ? $serviceName : '—' !!}
                    </span>
                </h3>
            </div>

            <div class="px-5 py-5 space-y-4">

                {{-- Name + description --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Name</label>
                        {!! Form::text('name', '', [
                            'class' => 'form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600',
                            'id' => 'name',
                        ]) !!}
                    </div>
                    <div>
                        <label for="description" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Description</label>
                        {!! Form::text('description', '', [
                            'class' => 'form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600',
                            'id' => 'description',
                        ]) !!}
                    </div>
                </div>

                {{-- Status + paid + currency --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="status" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.Status') }}</label>
                        <select name="status" id="status" class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                            @foreach($statuses as $status)
                                <option {{ old('status') == $status->id ? 'selected' : '' }} value="{{ $status->id }}">{{ $status->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="currency" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.Currency') }}</label>
                        <select name="currency" id="currency" class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                            @foreach($currencies as $currency)
                                <option {{ old('currency') == $currency->id ? 'selected' : '' }} value="{{ $currency->id }}">{{ $currency->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <label class="inline-flex items-center gap-2 cursor-pointer pb-1">
                            {!! Form::checkbox('paid', 1, '', ['class' => 'form-checkbox h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-600/30']) !!}
                            <span class="text-sm text-slate-700">Paid</span>
                        </label>
                    </div>
                </div>

                {{-- Pax + amount + rate --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="pax" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Pax</label>
                        {!! Form::text('pax', $tour->pax, [
                            'class' => 'form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600',
                            'id' => 'pax',
                        ]) !!}
                    </div>
                    <div>
                        <label for="pax_free" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Pax free</label>
                        {!! Form::text('pax_free', $tour->pax_free, [
                            'class' => 'form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600',
                            'id' => 'pax_free',
                        ]) !!}
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="total_amount" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Total amount</label>
                        {!! Form::text('total_amount', 0, [
                            'class' => 'form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600',
                            'id' => 'total_amount',
                        ]) !!}
                    </div>
                    <div>
                        <label for="rate" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Rate</label>
                        {!! Form::text('rate', '', [
                            'class' => 'form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600',
                            'id' => 'rate',
                        ]) !!}
                    </div>
                </div>

                {{-- Date / time grid. .input-group + .datepicker + .timepicker class
                     hooks are kept because the legacy picker JS keys on them. --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="from_date" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.DateFrom') }}</label>
                        <div class="input-group date relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <x-ui.icon name="calendar" size="sm" />
                            </span>
                            {!! Form::text('from_date', '', [
                                'class' => 'form-control datepicker block w-full h-9 rounded border border-slate-300 bg-white pl-9 pr-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600',
                                'id' => 'from_date',
                                'autocomplete' => 'off',
                            ]) !!}
                        </div>
                    </div>
                    <div>
                        <label for="from_time" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.TimeFrom') }}</label>
                        <div class="input-group date relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <x-ui.icon name="clock" size="sm" />
                            </span>
                            {!! Form::text('from_time', '12:00', [
                                'class' => 'form-control timepicker block w-full h-9 rounded border border-slate-300 bg-white pl-9 pr-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600',
                                'id' => 'from_time',
                                'autocomplete' => 'off',
                            ]) !!}
                        </div>
                    </div>
                    <div>
                        <label for="to_date" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.DateTo') }}</label>
                        <div class="input-group date relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <x-ui.icon name="calendar" size="sm" />
                            </span>
                            {!! Form::text('to_date', '', [
                                'class' => 'form-control datepicker block w-full h-9 rounded border border-slate-300 bg-white pl-9 pr-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600',
                                'id' => 'to_date',
                                'autocomplete' => 'off',
                            ]) !!}
                        </div>
                    </div>
                    <div>
                        <label for="to_time" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.TimeTo') }}</label>
                        <div class="input-group date relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <x-ui.icon name="clock" size="sm" />
                            </span>
                            {!! Form::text('to_time', '13:00', [
                                'class' => 'form-control timepicker block w-full h-9 rounded border border-slate-300 bg-white pl-9 pr-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600',
                                'id' => 'to_time',
                                'autocomplete' => 'off',
                            ]) !!}
                        </div>
                    </div>
                </div>

                {{-- Note --}}
                <div>
                    <label for="note" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Note</label>
                    {!! Form::textarea('note', '', [
                        'class' => 'form-control block w-full rounded border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600',
                        'id' => 'note',
                        'rows' => 3,
                    ]) !!}
                </div>

                {{-- Hidden fields for the JS submit pipeline --}}
                {!! Form::hidden('serviceType', $selectedServiceType, ['id' => 'tour_package_service_type_value']) !!}
                {!! Form::hidden('serviceId',   $selectedServiceId,   ['id' => 'tour_package_service_type_id'])   !!}
            </div>

            {{-- Card footer --}}
            <div class="border-t border-slate-200 bg-slate-50 px-5 py-3 flex items-center justify-end gap-2 rounded-b">
                <x-ui.button as="a" href="{{ url('/tour/' . $tour_package->tour . '/edit') }}" variant="secondary">
                    {{ trans('main.Cancel') }}
                </x-ui.button>
                <button class="btn btn-primary inline-flex items-center gap-1.5 rounded bg-primary-600 px-4 h-9 text-sm font-medium text-white hover:bg-primary-700" type="submit">
                    <x-ui.icon name="plus" size="sm" />
                    {{ trans('main.Create') }}
                </button>
            </div>
        </div>

        {{-- Tour-day FK pointer used by the submit handler --}}
        {!! Form::hidden('tourDayId', $id, ['id' => 'tour_package_tour_day_id']) !!}
    </form>
</div>
@endsection
