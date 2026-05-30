@extends('scaffold-interface.layouts.tabler-app')
@section('title','Tax')

@section('content')
<x-ui.page-header
    :title="$tax->name"
    description="Tax details"
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Taxes', 'href' => route('taxes.index')],
        ['label' => $tax->name],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="{{ route('taxes.index') }}" variant="ghost" icon="arrow-left">{{ trans('main.Back') }}</x-ui.button>
        @if(Auth::user()->can('taxes.edit'))
            <x-ui.button as="a" href="{!! route('taxes.edit', $tax->id) !!}" variant="secondary" icon="edit">{{ trans('main.Edit') }}</x-ui.button>
        @endif
    </x-slot>
</x-ui.page-header>

<div class="rounded border border-slate-200 bg-white">
    <div class="border-b border-slate-200 px-5 py-3 flex items-start gap-3">
        <div class="flex h-8 w-8 items-center justify-center rounded bg-primary-50 text-primary-600 shrink-0"><x-ui.icon name="percent" size="sm" /></div>
        <div class="flex-1 min-w-0">
            <h2 class="text-sm font-medium text-slate-700">Tax details</h2>
        </div>
    </div>
    <dl class="px-5 py-5 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('Tax Name') !!}</dt>
            <dd class="mt-0.5 text-slate-800 font-medium">{!! $tax->name !!}</dd>
        </div>
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('Value') !!}</dt>
            <dd class="mt-0.5 text-slate-800">{!! $tax->value !!}</dd>
        </div>
    </dl>
</div>
@endsection
