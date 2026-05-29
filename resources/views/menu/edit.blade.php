@extends('scaffold-interface.layouts.tabler-app')
@section('title','Edit menu item')

@section('content')
<x-ui.page-header
    title="Edit menu item"
    :description="$menu->name"
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Menu'],
        ['label' => 'Edit'],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="javascript:history.back()" variant="ghost" icon="arrow-left">{{ trans('main.Back') }}</x-ui.button>
    </x-slot>
</x-ui.page-header>

<form method="POST" action="{{ route('menu.update', ['menu' => $menu->id]) }}" class="space-y-4">
    {{ csrf_field() }}
    {{ method_field('PUT') }}
    @include('component.js-validate')

    <div class="rounded border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-3 flex items-start gap-3">
            <div class="flex h-8 w-8 items-center justify-center rounded bg-primary-50 text-primary-600 shrink-0"><x-ui.icon name="list-details" size="sm" /></div>
            <div class="flex-1 min-w-0">
                <h2 class="text-sm font-medium text-slate-700">Menu details</h2>
            </div>
        </div>
        <div class="px-5 py-5 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="name" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.Name') }}</label>
                {{ Form::input('text', 'name', $menu->name, ['id' => 'name', 'class' => 'form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600']) }}
            </div>
            <div>
                <label for="price" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.Price') }}</label>
                {{ Form::input('text', 'price', $menu->price, ['id' => 'price', 'class' => 'form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600']) }}
            </div>
            <div class="md:col-span-2">
                <label for="description" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.Description') }}</label>
                {{ Form::textarea('description', $menu->description, ['class' => 'form-control textarea_editor block w-full rounded border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700', 'id' => 'description']) }}
            </div>
        </div>
    </div>

    <span id="page" data-page="create"></span>

    <div class="sticky bottom-0 -mx-4 sm:mx-0 sm:static sm:rounded sm:border sm:border-slate-200 bg-white sm:bg-slate-50 px-4 sm:px-5 py-3 border-t border-slate-200 sm:border-t-0 sm:border flex items-center justify-end gap-2 shadow-[0_-4px_8px_-4px_rgba(15,23,42,0.05)] sm:shadow-none">
        <x-ui.button as="a" href="{{ $menu->getParentRoute() }}" variant="secondary">{{ trans('main.Cancel') }}</x-ui.button>
        <x-ui.button type="submit" variant="primary" icon="save" class="pre-loader-func">{{ trans('main.Save') }}</x-ui.button>
    </div>
</form>

<script>
    $(document).ready(function () {
        if (typeof CKEDITOR !== 'undefined') {
            CKEDITOR.replace('description', { title: false, height: '400px' });
        }
    });
</script>
@endsection
