@extends('scaffold-interface.layouts.tabler-app')
@section('title','Create Driver')

@section('content')
<x-ui.page-header
    title="New driver"
    description="Add a driver to the fleet catalog."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Drivers', 'href' => route('driver.index')],
        ['label' => 'New'],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="{{ route('driver.index') }}" variant="ghost" icon="arrow-left">
            {!! trans('main.Back') !!}
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

<form method="POST" action="{!! url('driver') !!}" enctype="multipart/form-data" id="driver-form" class="space-y-4">
    @csrf

    {{-- ============================================================ --}}
    {{-- Section 1: Identity --}}
    {{-- ============================================================ --}}
    <div class="rounded border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-3 flex items-start gap-3">
            <div class="flex h-8 w-8 items-center justify-center rounded bg-primary-50 text-primary-600 shrink-0"><x-ui.icon name="user" size="sm" /></div>
            <div class="flex-1 min-w-0">
                <h2 class="text-sm font-medium text-slate-700">Identity</h2>
                <p class="text-xs text-slate-500">Who is this driver?</p>
            </div>
        </div>
        <div class="px-5 py-5 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label for="name" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">
                    {!! trans('main.Name') !!} <span class="text-danger-600">*</span>
                </label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required
                       class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            </div>
            <div>
                <label for="phone" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">
                    {!! trans('main.Phone') !!}
                </label>
                <input id="phone" name="phone" type="text" value="{{ old('phone') }}"
                       class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            </div>
            <div>
                <label for="email" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">
                    {!! trans('main.Email') !!}
                </label>
                <input id="email" name="email" type="text" value="{{ old('email') }}"
                       class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            </div>
            <div class="md:col-span-2">
                <label for="transfer_id" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">
                    {!! trans('main.BusCompany') !!}
                </label>
                <select id="transfer_id" name="transfer_id"
                        class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                    <option value="0">— select —</option>
                    @foreach($transfers as $id => $name)
                        <option value="{{ $id }}" {{ old('transfer_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- Section 2: Files --}}
    {{-- ============================================================ --}}
    <div class="rounded border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-3 flex items-start gap-3">
            <div class="flex h-8 w-8 items-center justify-center rounded bg-primary-50 text-primary-600 shrink-0"><x-ui.icon name="paperclip" size="sm" /></div>
            <div class="flex-1 min-w-0">
                <h2 class="text-sm font-medium text-slate-700">{!! trans('main.Files') !!}</h2>
                <p class="text-xs text-slate-500">Attach contracts, licences, anything related.</p>
            </div>
        </div>
        <div class="px-5 py-5">
            @component('component.file_upload_field')@endcomponent
        </div>
    </div>

    {{-- Form footer --}}
    <div class="sticky bottom-0 -mx-4 sm:mx-0 sm:static sm:rounded sm:border sm:border-slate-200 bg-white sm:bg-slate-50 px-4 sm:px-5 py-3 border-t border-slate-200 sm:border-t-0 sm:border flex items-center justify-end gap-2 shadow-[0_-4px_8px_-4px_rgba(15,23,42,0.05)] sm:shadow-none">
        <x-ui.button as="a" href="{{ route('driver.index') }}" variant="secondary">{!! trans('main.Cancel') !!}</x-ui.button>
        <x-ui.button type="submit" variant="primary" icon="save">{!! trans('main.Save') !!}</x-ui.button>
    </div>
</form>
@endsection
