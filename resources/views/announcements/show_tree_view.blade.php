@extends('scaffold-interface.layouts.tabler-app')
@section('title', $announcement->title ?? 'Announcement')

@section('content')
<x-ui.page-header
    title="Announcement"
    :description="$announcement->title ?? ''"
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Announcements', 'href' => route('announcements.index')],
        ['label' => 'Show'],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="javascript:history.back()" variant="ghost" icon="arrow-left">
            {{ trans('main.Back') }}
        </x-ui.button>
        @if(\Auth::id() == $announcement->author)
            <x-ui.button as="a" :href="route('announcements.edit', $announcement->id)" variant="primary" icon="pencil">
                {{ trans('main.Edit') }}
            </x-ui.button>
        @endif
    </x-slot>
</x-ui.page-header>

{{-- Hidden flag the legacy comment.js looks for. --}}
<span id="showPreviewBlock" data-info="{{ true }}" class="hidden"></span>

<div class="rounded border border-slate-200 bg-white shadow-subtle flex flex-col max-h-[75vh]">

    <div class="border-b border-slate-200 px-5 py-3 flex items-center gap-2">
        <x-ui.icon name="messages-square" class="text-success-600" />
        <h3 class="text-sm font-semibold text-slate-900">{{ trans('main.Announcement') }}</h3>
    </div>

    {{-- Scrollable comments region. .slimScrollDiv + #chat-box + #show_comments
         + .chat class are read by public/js/comment.js. --}}
    <div class="slimScrollDiv flex-1 overflow-y-auto px-5 py-4">
        <div class="chat" id="chat-box">
            <div id="show_comments"></div>
        </div>
    </div>

    <div class="border-t border-slate-200 bg-slate-50 px-5 py-3">
        <form method="post"
              action="{{ route('announcement_reply', ['id' => $announcement->id]) }}"
              enctype="multipart/form-data"
              id="form_comment"
              class="space-y-3">
            {{ csrf_field() }}

            <div class="input-group flex items-stretch rounded border border-slate-300 bg-white shadow-subtle focus-within:ring-2 focus-within:ring-primary-600/30 focus-within:border-primary-600 overflow-hidden">
                <span id="author_name" class="input-group-addon hidden items-center gap-1 bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700">
                    <span id="name"></span>
                    <a href="#" id="reply_close" class="text-slate-500 hover:text-slate-900 ml-1">
                        <x-ui.icon name="x" size="xs" />
                    </a>
                </span>
                <textarea class="form-control block w-full px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none border-0"
                          id="content"
                          name="content"
                          rows="3"
                          placeholder="Ctrl + Enter to post comment"></textarea>
            </div>

            <div>
                <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Files</label>
                @component('component.file_upload_field')@endcomponent
            </div>

            <input type="text" id="parent_comment" hidden name="parent_id" value="{{ $announcement->id }}">
            <input type="text" id="id_comment" hidden name="id_comment" value="{{ $announcement->id }}">

            <div class="flex justify-end">
                <button type="submit"
                        class="btn btn-success pull-right inline-flex items-center gap-1.5 rounded bg-success-600 px-4 h-9 text-sm font-medium text-white hover:bg-success-700">
                    <x-ui.icon name="send" size="sm" />
                    {{ trans('main.Send') }}
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Announcement id pointer read by comment.js. --}}
<span id="announcements" data-announ-id="{{ $announcement->id }}" class="hidden"></span>
@endsection

@push('scripts')
<script src="{{ asset('js/comment.js') }}"></script>
@endpush
