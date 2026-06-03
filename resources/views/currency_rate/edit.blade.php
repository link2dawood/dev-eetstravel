@extends('scaffold-interface.layouts.tabler-app')
@section('title','Edit currency rate')

@section('content')
<x-ui.page-header
    title="Currency rate"
    description="Edit rate"
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Currency rates', 'href' => route('currency_rate.index')],
        ['label' => 'Edit'],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="javascript:history.back()" variant="ghost" icon="arrow-left" class="back_btn">
            {{ trans('main.Back') }}
        </x-ui.button>
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

    <form method="POST" action="{!! url('currency_rate') . '/' . $currency_rate->id . '/update' !!}" class="space-y-4">
        <input type="hidden" name="_token" value="{{ Session::token() }}">

        <div class="rounded border border-slate-200 bg-white shadow-subtle">
            <div class="border-b border-slate-200 px-5 py-3 flex items-center gap-2">
                <x-ui.icon name="trending-up" class="text-primary-600" />
                <h2 class="text-sm font-semibold text-slate-900">Rate details</h2>
            </div>

            <div class="px-5 py-5">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                    {{-- Left: rate fields --}}
                    <div class="lg:col-span-5 space-y-4">
                        <div>
                            <label for="currency" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">
                                {{ trans('main.Currency') }}
                            </label>
                            <input type="text"
                                   id="currency"
                                   name="currency"
                                   value="{{ (isset($errors) && $errors->any()) ? '' : ($currency_rate->currency ?? '') }}{{ old('currency') }}"
                                   class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                        </div>

                        <div>
                            <label for="rate" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">
                                {{ trans('main.Rate') }}
                            </label>
                            <input type="text"
                                   id="rate"
                                   name="rate"
                                   value="{{ (isset($errors) && $errors->any()) ? '' : ($currency_rate->rate ?? '') }}{{ old('rate') }}"
                                   class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-900 font-mono shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                        </div>

                        <div>
                            <label for="date" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">
                                {{ trans('main.Date') }}
                            </label>
                            <div class="input-group date relative">
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                    <x-ui.icon name="calendar" size="sm" />
                                </span>
                                {!! Form::text('date', $currency_rate->date, [
                                    'class' => 'form-control datepicker block w-full h-9 rounded border border-slate-300 bg-white pl-9 pr-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600',
                                    'id' => 'date',
                                    'autocomplete' => 'off',
                                ]) !!}
                            </div>
                        </div>
                    </div>

                    {{-- Right: tour packages injection point + itinerary tab pane.
                         The .tour-packages container and #itinerary tab pane are
                         legacy DOM hooks the page's script populates at runtime;
                         preserved verbatim. --}}
                    <div class="lg:col-span-7">
                        <div class="tour-packages"></div>
                        <div id="itinerary" class="tab-pane fade"></div>
                    </div>
                </div>
            </div>

            {{-- Card footer with Save / Cancel --}}
            <div class="border-t border-slate-200 bg-slate-50 px-5 py-3 flex items-center justify-end gap-2 rounded-b">
                <x-ui.button
                    as="a"
                    :href="\App\Helper\AdminHelper::getBackButton(route('currency_rate.index'))"
                    variant="secondary"
                >
                    {{ trans('main.Cancel') }}
                </x-ui.button>
                <button type="submit"
                        class="btn btn-success inline-flex items-center gap-1.5 rounded bg-primary-600 px-4 h-9 text-sm font-medium text-white hover:bg-primary-700">
                    <x-ui.icon name="save" size="sm" />
                    {{ trans('main.Save') }}
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
