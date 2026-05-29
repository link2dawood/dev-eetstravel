@extends('scaffold-interface.layouts.tabler-app')
@section('title','Edit')

@section('content')
<x-ui.page-header
    :title="'Edit ' . $currencies->name"
    :description="$currencies->code"
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Currencies', 'href' => route('currencies.index')],
        ['label' => 'Edit'],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="{{ route('currencies.index') }}" variant="ghost" icon="arrow-left">{{ trans('main.Back') }}</x-ui.button>
    </x-slot>
</x-ui.page-header>

@if(count($errors) > 0)
    <div class="mb-4 rounded border border-danger-600/20 bg-danger-50 px-4 py-3 text-sm text-danger-700">
        <div class="flex items-center gap-2 font-medium"><x-ui.icon name="alert-octagon" class="text-danger-600" />Please correct the following:</div>
        <ul class="mt-2 list-disc pl-5 space-y-0.5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

<form method="POST" action="{{ url('currencies') }}/{{ $currencies->id }}/update" class="space-y-4">
    <input type="hidden" name="_token" value="{{ Session::token() }}">

    <div class="rounded border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-3 flex items-start gap-3">
            <div class="flex h-8 w-8 items-center justify-center rounded bg-primary-50 text-primary-600 shrink-0"><x-ui.icon name="circle-dollar-sign" size="sm" /></div>
            <div class="flex-1 min-w-0">
                <h2 class="text-sm font-medium text-slate-700">Currency details</h2>
            </div>
        </div>
        <div class="px-5 py-5 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="name" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.Name') !!} <span class="text-danger-600">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name', $currencies->name) }}" required
                       class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            </div>
            <div>
                <label for="code" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.Code') !!} <span class="text-danger-600">*</span></label>
                <input type="text" id="code" name="code" value="{{ old('code', $currencies->code) }}" required
                       class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            </div>
            <div>
                <label for="symbol" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.Symbol') !!} <span class="text-danger-600">*</span></label>
                <input type="text" id="symbol" name="symbol" value="{{ old('symbol', $currencies->symbol) }}" required
                       class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            </div>
            <div>
                <label for="cent" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.Cent') !!}</label>
                <input type="text" id="cent" name="cent" value="{{ old('cent', $currencies->cent) }}"
                       class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            </div>
            <div>
                <label for="symbol_cent" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.SymbolCent') !!}</label>
                <input type="text" id="symbol_cent" name="symbol_cent" value="{{ old('symbol_cent', $currencies->symbol_cent) }}"
                       class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            </div>
        </div>
    </div>

    <div class="sticky bottom-0 -mx-4 sm:mx-0 sm:static sm:rounded sm:border sm:border-slate-200 bg-white sm:bg-slate-50 px-4 sm:px-5 py-3 border-t border-slate-200 sm:border-t-0 sm:border flex items-center justify-end gap-2 shadow-[0_-4px_8px_-4px_rgba(15,23,42,0.05)] sm:shadow-none">
        <x-ui.button as="a" href="{{ \App\Helper\AdminHelper::getBackButton(route('currencies.index')) }}" variant="secondary">{{ trans('main.Cancel') }}</x-ui.button>
        <x-ui.button type="submit" variant="primary" icon="save">{{ trans('main.Save') }}</x-ui.button>
    </div>
</form>
@endsection
