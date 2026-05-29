@extends('scaffold-interface.layouts.tabler-app')
@section('title','Show')

@section('content')
<x-ui.page-header
    :title="$currencies->name"
    :description="$currencies->code"
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Currencies', 'href' => route('currencies.index')],
        ['label' => $currencies->name],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="{{ route('currencies.index') }}" variant="ghost" icon="arrow-left">{{ trans('main.Back') }}</x-ui.button>
        @if(Auth::user()->can('currencies.edit'))
            <x-ui.button as="a" href="{{ route('currencies.edit', $currencies->id) }}" variant="secondary" icon="edit">{{ trans('main.Edit') }}</x-ui.button>
        @endif
    </x-slot>
</x-ui.page-header>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="lg:col-span-2 rounded border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-4 py-3 flex items-center gap-2">
            <x-ui.icon name="circle-dollar-sign" size="sm" class="text-slate-400" />
            <h2 class="text-sm font-medium text-slate-700">Currency details</h2>
        </div>
        <dl class="px-4 py-4 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('main.Name') !!}</dt>
                <dd class="mt-0.5 text-slate-800">{!! $currencies->name !!}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('main.Code') !!}</dt>
                <dd class="mt-0.5 font-mono text-slate-800">{!! $currencies->code !!}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('main.Symbol') !!}</dt>
                <dd class="mt-0.5 text-slate-800">{!! $currencies->symbol !!}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('main.Cent') !!}</dt>
                <dd class="mt-0.5 text-slate-800">{!! $currencies->cent !!}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('main.SymbolCent') !!}</dt>
                <dd class="mt-0.5 text-slate-800">{!! $currencies->symbol_cent !!}</dd>
            </div>
        </dl>
    </div>
</div>

<span id="showPreviewBlock" data-info="true" hidden></span>

{{-- Comments --}}
<div class="mt-6 rounded border border-slate-200 bg-white">
    <div class="border-b border-slate-200 px-4 py-3 flex items-center gap-2">
        <x-ui.icon name="message-circle" size="sm" class="text-slate-400" />
        <h2 class="text-sm font-medium text-slate-700">{!! trans('main.Comments') !!}</h2>
    </div>
    <div class="px-4 py-4">
        <div id="chat-box" class="max-h-80 overflow-y-auto">
            <div id="show_comments"></div>
        </div>
    </div>
    <div class="border-t border-slate-200 bg-slate-50 px-4 py-4 rounded-b">
        <form method="POST" action="{{ route('comment.store') }}" enctype="multipart/form-data" id="form_comment" class="space-y-3">
            @csrf
            <div>
                <span id="author_name" class="hidden mb-2 inline-flex items-center gap-2 rounded bg-primary-50 px-2 py-1 text-xs text-primary-700">
                    Replying to <span id="name" class="font-medium"></span>
                    <a href="#" id="reply_close" class="text-primary-700/70 hover:text-primary-900"><x-ui.icon name="x" size="xs" /></a>
                </span>
                <textarea class="form-control block w-full rounded border border-slate-300 bg-white px-3 py-2 text-sm shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600"
                          id="content" name="content" rows="3" placeholder="Add a comment — Ctrl + Enter to post"></textarea>
            </div>
            <div>
                <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{!! trans('main.Files') !!}</label>
                @component('component.file_upload_field')@endcomponent
            </div>
            <input type="hidden" id="parent_comment" name="parent" value="">
            <input type="hidden" id="default_reference_id" name="reference_id" value="{{ $currencies->id }}">
            <input type="hidden" id="default_reference_type" name="reference_type" value="{{ \App\Comment::$services['currencies'] }}">
            <div class="flex">
                <button type="submit" id="btn_send_comment" class="inline-flex h-9 items-center gap-2 rounded bg-primary-600 px-4 text-sm font-medium text-white hover:bg-primary-700">
                    <x-ui.icon name="send" size="sm" />{!! trans('main.Send') !!}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('post_scripts')
    <script src="{{ asset('js/comment.js') }}"></script>
@endsection
