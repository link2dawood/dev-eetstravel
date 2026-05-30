@extends('scaffold-interface.layouts.tabler-app')
@section('title','Criteria')

@section('content')
<x-ui.page-header
    :title="$criteria->name"
    description="Criteria details"
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Criteria', 'href' => route('criteria.index')],
        ['label' => $criteria->name],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="{{ route('criteria.index') }}" variant="ghost" icon="arrow-left">{{ trans('main.Back') }}</x-ui.button>
        @if(Auth::user()->can('criteria.edit'))
            <x-ui.button as="a" href="{!! route('criteria.edit', $criteria->id) !!}" variant="secondary" icon="edit">{{ trans('main.Edit') }}</x-ui.button>
        @endif
    </x-slot>
</x-ui.page-header>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="lg:col-span-2">
        <div class="rounded border border-slate-200 bg-white">
            <div class="border-b border-slate-200 px-5 py-3 flex items-start gap-3">
                <div class="flex h-8 w-8 items-center justify-center rounded bg-primary-50 text-primary-600 shrink-0"><x-ui.icon name="tag" size="sm" /></div>
                <div class="flex-1 min-w-0">
                    <h2 class="text-sm font-medium text-slate-700">Criteria details</h2>
                </div>
            </div>
            <dl class="px-5 py-5 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('main.Name') !!}</dt>
                    <dd class="mt-0.5 text-slate-800 font-medium">{!! $criteria->name !!}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('main.ShortName') !!}</dt>
                    <dd class="mt-0.5 text-slate-800">{!! $criteria->short_name !!}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('main.Icon') !!}</dt>
                    <dd class="mt-0.5 text-slate-800 font-mono">{!! $criteria->icon !!}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{!! trans('main.CriteriaType') !!}</dt>
                    <dd class="mt-0.5 text-slate-800">{!! $criteria_type == null ? '—' : $criteria_type->name !!}</dd>
                </div>
            </dl>
        </div>
    </div>

    <div>
        <span id="showPreviewBlock" data-info="{{ true }}"></span>
        <div class="rounded border border-slate-200 bg-white">
            <div class="border-b border-slate-200 px-5 py-3 flex items-center gap-2">
                <x-ui.icon name="messages-square" size="sm" class="text-slate-400" />
                <h3 class="text-sm font-medium text-slate-700">{!! trans('main.Comments') !!}</h3>
            </div>
            <div class="px-5 py-4">
                <div id="chat-box" class="chat box max-h-96 overflow-y-auto pr-1">
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
                    <input type="text" id="parent_comment" hidden name="parent" value="{{ null }}">
                    <input type="text" id="default_reference_id" hidden name="reference_id" value="{{ $criteria->id }}">
                    <input type="text" id="default_reference_type" hidden name="reference_type" value="{{ \App\Comment::$services['criteria'] }}">

                    <div class="flex justify-end">
                        <x-ui.button type="submit" variant="primary" icon="send" id="btn_send_comment">{!! trans('main.Send') !!}</x-ui.button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('post_scripts')
    <script src="{{ asset('js/comment.js') }}"></script>
@endsection
