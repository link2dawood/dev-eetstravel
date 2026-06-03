@extends('scaffold-interface.layouts.tabler-app')
@section('title','Currency rate')

@section('content')
<x-ui.page-header
    title="Currency rate"
    :description="$currency_rate->currency ?? 'Rate detail'"
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Currency rates', 'href' => route('currency_rate.index')],
        ['label' => 'Show'],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="javascript:history.back()" variant="ghost" icon="arrow-left">
            {{ trans('main.Back') }}
        </x-ui.button>
        <x-ui.button as="a" :href="route('currency_rate.edit', $currency_rate->id)" variant="primary" icon="pencil">
            {{ trans('main.Edit') }}
        </x-ui.button>
    </x-slot>
</x-ui.page-header>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

    {{-- ─── Detail card ───────────────────────────────────────────────── --}}
    <div class="lg:col-span-2 rounded border border-slate-200 bg-white shadow-subtle overflow-hidden">
        <div class="border-b border-slate-200 px-5 py-3 flex items-center gap-2">
            <x-ui.icon name="trending-up" class="text-primary-600" />
            <h2 class="text-sm font-semibold text-slate-900">Rate details</h2>
        </div>
        <table class="table table-bordered w-full text-sm">
            <tbody class="divide-y divide-slate-100">
                <tr>
                    <td class="show_width_td px-5 py-3 text-xs font-medium uppercase tracking-wide text-slate-500 bg-slate-50 w-48">
                        {{ trans('main.Currency') }}
                    </td>
                    <td class="px-5 py-3 text-sm text-slate-900 font-medium">
                        {!! $currency_rate->currency ?? '<span class="text-slate-400">—</span>' !!}
                    </td>
                </tr>
                <tr>
                    <td class="show_width_td px-5 py-3 text-xs font-medium uppercase tracking-wide text-slate-500 bg-slate-50">
                        {{ trans('main.Rate') }}
                    </td>
                    <td class="px-5 py-3 text-sm text-slate-900 font-mono">
                        {!! $currency_rate->rate ?? '<span class="text-slate-400">—</span>' !!}
                    </td>
                </tr>
                <tr>
                    <td class="show_width_td px-5 py-3 text-xs font-medium uppercase tracking-wide text-slate-500 bg-slate-50">
                        {{ trans('main.Date') }}
                    </td>
                    <td class="px-5 py-3 text-sm text-slate-700">
                        {!! $currency_rate->date ?? '<span class="text-slate-400">—</span>' !!}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- ─── Comments card ─────────────────────────────────────────────── --}}
    {{-- showPreviewBlock + chat-box + show_comments + form_comment +
         #content + #author_name + #parent_comment + #default_reference_id +
         #default_reference_type + #btn_send_comment + #reply_close + #name
         are read by public/js/comment.js — keep their IDs/classes intact. --}}
    <div class="rounded border border-slate-200 bg-white shadow-subtle flex flex-col">
        <div class="border-b border-slate-200 px-5 py-3 flex items-center gap-2">
            <x-ui.icon name="messages-square" class="text-success-600" />
            <h3 class="text-sm font-semibold text-slate-900">{{ trans('main.Comments') }}</h3>
        </div>

        <div class="px-5 py-3 flex-1 min-h-[200px] max-h-[60vh] overflow-y-auto slimScrollDiv">
            <div class="chat" id="chat-box">
                <div id="show_comments"></div>
            </div>
        </div>

        <div class="border-t border-slate-200 bg-slate-50 px-5 py-3">
            <form method="POST" action="{{ route('comment.store') }}" enctype="multipart/form-data" id="form_comment" class="space-y-3">
                {{ csrf_field() }}

                <div class="input-group flex items-stretch rounded border border-slate-300 bg-white shadow-subtle focus-within:ring-2 focus-within:ring-primary-600/30 focus-within:border-primary-600 overflow-hidden">
                    <span id="author_name" class="input-group-addon hidden flex items-center gap-1 bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700">
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
                    <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.Files') }}</label>
                    @component('component.file_upload_field')@endcomponent
                </div>

                <input type="text" id="parent_comment" hidden name="parent" value="{{ null }}">
                <input type="text" id="default_reference_id" hidden name="reference_id" value="{{ $currency_rate->id }}">
                <input type="text" id="default_reference_type" hidden name="reference_type" value="{{ \App\Comment::$services['currency_rate'] }}">

                <div class="flex justify-end">
                    <button type="submit"
                            class="btn btn-success pull-right inline-flex items-center gap-1.5 rounded bg-success-600 px-4 h-9 text-sm font-medium text-white hover:bg-success-700"
                            id="btn_send_comment">
                        <x-ui.icon name="send" size="sm" />
                        {{ trans('main.Send') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<span id="showPreviewBlock" data-info="{{ true }}" class="hidden"></span>
@endsection

@section('post_scripts')
    <script src="{{ asset('js/comment.js') }}"></script>
@endsection
