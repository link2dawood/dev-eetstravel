@extends('scaffold-interface.layouts.tabler-app')
@section('title','Menu item')

@section('content')
<x-ui.page-header
    :title="$menu->name"
    description="Menu item details"
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Menu'],
        ['label' => $menu->name],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="javascript:history.back()" variant="ghost" icon="arrow-left">{{ trans('main.Back') }}</x-ui.button>
        <x-ui.button as="a" href="{{ route('menu.edit', ['menu' => $menu->id]) }}" variant="secondary" icon="edit">{{ trans('main.Edit') }}</x-ui.button>
    </x-slot>
</x-ui.page-header>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div class="rounded border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-3 flex items-start gap-3">
            <div class="flex h-8 w-8 items-center justify-center rounded bg-primary-50 text-primary-600 shrink-0"><x-ui.icon name="list-details" size="sm" /></div>
            <div class="flex-1 min-w-0">
                <h2 class="text-sm font-medium text-slate-700">Menu details</h2>
            </div>
        </div>
        <dl class="px-5 py-5 grid grid-cols-1 gap-x-6 gap-y-4 text-sm">
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ trans('main.Name') }}</dt>
                <dd class="mt-0.5 text-slate-800 font-medium">{!! $menu->name !!}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ trans('main.Price') }}</dt>
                <dd class="mt-0.5 text-slate-800">{!! $menu->price !!}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ trans('main.Description') }}</dt>
                <dd class="mt-0.5 text-slate-800 prose prose-sm max-w-none">{!! $menu->description !!}</dd>
            </div>
        </dl>
    </div>
    <div>
        <span id="page" data-page="create"></span>
    </div>
</div>
@endsection
