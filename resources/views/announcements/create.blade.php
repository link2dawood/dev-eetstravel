@extends('scaffold-interface.layouts.tabler-app')
@section('title','Create')

@section('content')
<x-ui.page-header
    title="New announcement"
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Announcements', 'href' => route('announcements.index')],
        ['label' => 'New'],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="{{ route('announcements.index') }}" variant="ghost" icon="arrow-left">{{ trans('main.Back') }}</x-ui.button>
    </x-slot>
</x-ui.page-header>

<form method="POST" action="{{ route('announcements.store') }}" enctype="multipart/form-data" class="space-y-4">
    @csrf

    <div class="rounded border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-3 flex items-start gap-3">
            <div class="flex h-8 w-8 items-center justify-center rounded bg-primary-50 text-primary-600 shrink-0"><x-ui.icon name="megaphone" size="sm" /></div>
            <div class="flex-1 min-w-0">
                <h2 class="text-sm font-medium text-slate-700">Announcement</h2>
            </div>
        </div>
        <div class="px-5 py-5 space-y-4">
            <div>
                <label for="title" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">
                    {!! trans('main.Title') !!}
                    @if($errors->has('title'))<span class="text-danger-600 ml-2 normal-case">{{ $errors->first('title') }}</span>@endif
                </label>
                {!! Form::input('text', 'title', $title, ['id' => 'title', 'class' => ($errors->has('title') ? 'border-danger-600 ' : 'border-slate-300 ') . 'block w-full h-9 rounded border bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600']) !!}
            </div>
            <div>
                <label for="content" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.Content') !!}</label>
                {!! Form::textarea('content', '', ['id' => 'content', 'rows' => 6, 'class' => 'form-control block w-full rounded border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600']) !!}
            </div>
            {{ Form::hidden('parent_id', $parent_id) }}
            <div>
                <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.Files') !!}</label>
                <input type="file" name="files[]" multiple
                       class="block w-full text-sm text-slate-700 file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:bg-primary-600 file:text-white file:cursor-pointer hover:file:bg-primary-700" />
            </div>
        </div>
    </div>

    <div class="sticky bottom-0 -mx-4 sm:mx-0 sm:static sm:rounded sm:border sm:border-slate-200 bg-white sm:bg-slate-50 px-4 sm:px-5 py-3 border-t border-slate-200 sm:border-t-0 sm:border flex items-center justify-end gap-2 shadow-[0_-4px_8px_-4px_rgba(15,23,42,0.05)] sm:shadow-none">
        <x-ui.button as="a" href="{{ route('announcements.index') }}" variant="secondary">{{ trans('main.Cancel') }}</x-ui.button>
        <x-ui.button type="submit" variant="primary" icon="save">{{ trans('main.Save') }}</x-ui.button>
    </div>
</form>
@endsection
