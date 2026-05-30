@extends('scaffold-interface.layouts.tabler-app')
@section('title','Comment')

@section('content')
<x-ui.page-header
    :title="$comment->serviceName()"
    description="Comment thread"
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Comments', 'href' => route('comment.index')],
        ['label' => $comment->serviceName()],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="javascript:history.back()" variant="ghost" icon="arrow-left">{!! trans('main.Back') !!}</x-ui.button>
        <x-ui.button as="a" href="{!! route('comment.edit', $comment->id) !!}" variant="secondary" icon="edit">{!! trans('main.Edit') !!}</x-ui.button>
    </x-slot>
</x-ui.page-header>

<span id="showPreviewBlock" data-info="{{ true }}"></span>

<div class="rounded border border-slate-200 bg-white">
    <div class="border-b border-slate-200 px-5 py-3 flex items-center gap-2">
        <x-ui.icon name="messages-square" size="sm" class="text-slate-400" />
        <h3 class="text-sm font-medium text-slate-700">{!! trans('main.Comments') !!}</h3>
    </div>
    <div class="px-5 py-4">
        {{-- The id #chat-box, .chat class, and #show_comments are read by
             comment.js to inject the rendered thread. Keep names intact. --}}
        <div id="chat-box" class="chat box max-h-[28rem] overflow-y-auto pr-1">
            <div id="show_comments"></div>
        </div>
    </div>

    <div class="border-t border-slate-200 bg-slate-50 px-5 py-4">
        <form method="POST" action="{{ route('comment.store') }}" enctype="multipart/form-data" id="form_comment" class="space-y-3">
            @csrf
            <div class="input-group flex flex-col gap-2">
                <div id="author_name" class="input-group-addon hidden items-center gap-2 rounded border border-slate-200 bg-white px-3 py-1.5 text-xs text-slate-600">
                    <span id="name"></span>
                    <a href="#" id="reply_close" class="text-slate-400 hover:text-slate-600"><x-ui.icon name="x" size="xs" /></a>
                </div>
                <textarea class="form-control block w-full rounded border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600"
                          id="content" name="content" rows="3" placeholder="Ctrl + Enter to post comment"></textarea>
            </div>
            <div>
                <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.Files') !!}</label>
                @component('component.file_upload_field')@endcomponent
            </div>
            <input type="text" id="parent_comment" hidden name="parent" value="{{ $comment->id }}">
            <input type="text" id="default_reference_id" hidden name="reference_id" value="{{ $comment->reference_id }}">
            <input type="text" id="default_reference_type" hidden name="reference_type" value="{{ $comment->reference_type }}">
            <input type="text" id="id_comment" hidden name="id_comment" value="{{ $comment->id }}">

            <div class="flex justify-end">
                <x-ui.button type="submit" variant="primary" icon="send" id="btn_send_comment">Send</x-ui.button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('post_scripts')
    <script src="{{ asset('js/comment.js') }}"></script>
@endsection
