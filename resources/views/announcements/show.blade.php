@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'View Announcement')

@section('content')
<x-ui.page-header
    :title="$announcement->title"
    description="Announcement details"
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Announcements', 'href' => route('announcements.index')],
        ['label' => $announcement->title],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="{{ route('announcements.index') }}" variant="ghost" icon="arrow-left">{{ trans('main.Back') ?? 'Back' }}</x-ui.button>
    </x-slot>
</x-ui.page-header>

{{-- Meta panel --}}
<div class="rounded border border-slate-200 bg-white mb-4">
    <div class="border-l-4 border-primary-600 px-5 py-4 grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-2 text-sm">
        <div class="flex items-center gap-2 text-slate-700">
            <x-ui.icon name="user" size="sm" class="text-slate-400" />
            <span><span class="font-medium">Author:</span> {{ optional(\App\User::find($announcement->author))->name ?? 'Unknown' }}</span>
        </div>
        <div class="flex items-center gap-2 text-slate-700">
            <x-ui.icon name="calendar" size="sm" class="text-slate-400" />
            <span><span class="font-medium">Created:</span> {{ $announcement->created_at ? $announcement->created_at->format('Y-m-d H:i:s') : 'N/A' }}</span>
        </div>
        <div class="flex items-center gap-2 text-slate-700">
            <x-ui.icon name="mail" size="sm" class="text-slate-400" />
            <span><span class="font-medium">Sender:</span> {{ $announcement->sender ?? 'N/A' }}</span>
        </div>
        <div class="flex items-center gap-2 text-slate-700">
            <x-ui.icon name="clock" size="sm" class="text-slate-400" />
            <span><span class="font-medium">Updated:</span> {{ $announcement->updated_at ? $announcement->updated_at->format('Y-m-d H:i:s') : 'N/A' }}</span>
        </div>
    </div>
</div>

{{-- Content --}}
<div class="rounded border border-slate-200 bg-white mb-4">
    <div class="border-b border-slate-200 px-4 py-3 flex items-center gap-2">
        <x-ui.icon name="file-text" size="sm" class="text-slate-400" />
        <h2 class="text-sm font-medium text-slate-700">Content</h2>
    </div>
    <div class="px-5 py-5 text-sm text-slate-800 whitespace-pre-wrap break-words min-h-[200px]">
        {!! nl2br(e($announcement->content)) !!}
    </div>
</div>

@php
    $attachments = collect();
    try {
        if (Schema::hasTable('media')) {
            $attachments = $announcement->getMedia('announcement_files');
        }
    } catch (\Exception $e) {
        // Media table doesn't exist, skip
    }
@endphp

@if($attachments->isNotEmpty())
    <div class="rounded border border-slate-200 bg-white mb-4">
        <div class="border-b border-slate-200 px-4 py-3 flex items-center gap-2">
            <x-ui.icon name="paperclip" size="sm" class="text-slate-400" />
            <h2 class="text-sm font-medium text-slate-700">Attachments <span class="text-slate-400 font-normal">({{ $attachments->count() }})</span></h2>
        </div>
        <ul class="divide-y divide-slate-100 list-none pl-0 m-0">
            @foreach($attachments as $index => $attachment)
                <li class="px-4 py-3 flex items-center gap-3 hover:bg-slate-50">
                    <span class="flex h-9 w-9 items-center justify-center rounded bg-slate-100 text-slate-500 shrink-0"><x-ui.icon name="paperclip" size="sm" /></span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-slate-700 truncate">{{ $attachment->file_name }}</p>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $attachment->human_readable_size }}</p>
                    </div>
                    <a href="{{ $attachment->getUrl() }}" target="_blank" class="inline-flex h-8 items-center gap-1 rounded bg-primary-600 px-3 text-xs font-medium text-white hover:bg-primary-700">
                        <x-ui.icon name="download" size="xs" />Download
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
@endif

@php $childs = $announcement->childs()->with('author')->get(); @endphp
@if($childs && $childs->isNotEmpty())
    <div class="rounded border border-slate-200 bg-white mb-4">
        <div class="border-b border-slate-200 px-4 py-3 flex items-center gap-2">
            <x-ui.icon name="message-circle" size="sm" class="text-slate-400" />
            <h2 class="text-sm font-medium text-slate-700">Replies <span class="text-slate-400 font-normal">({{ $childs->count() }})</span></h2>
        </div>
        <div class="px-4 py-4">
            @include('announcements.childs', ['childs' => $childs, 'nesting' => 1])
        </div>
    </div>
@endif
@endsection
