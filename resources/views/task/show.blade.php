@extends('scaffold-interface.layouts.tabler-app')
@section('title','Task')

@section('content')
<x-ui.page-header
    title="Task"
    :description="$task->tourName() ?: 'Without tour'"
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Tasks', 'href' => route('task.index')],
        ['label' => 'Show'],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="javascript:history.back()" variant="ghost" icon="arrow-left">
            {{ trans('main.Back') }}
        </x-ui.button>
        @if(Auth::user()->can('task.edit'))
            <x-ui.button as="a" :href="route('task.edit', ['task' => $task->id])" variant="primary" icon="pencil">
                {{ trans('main.Edit') }}
            </x-ui.button>
        @endif
    </x-slot>
</x-ui.page-header>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

    {{-- ─── Detail card ───────────────────────────────────────────────── --}}
    <div class="lg:col-span-2 space-y-4">
        <div class="rounded border border-slate-200 bg-white shadow-subtle overflow-hidden">
            <div class="border-b border-slate-200 px-5 py-3 flex items-center gap-2">
                <x-ui.icon name="clipboard-list" class="text-primary-600" />
                <h2 class="text-sm font-semibold text-slate-900">Task details</h2>
            </div>
            <table class="table table-bordered w-full text-sm">
                <tbody class="divide-y divide-slate-100">
                    <tr>
                        <td class="px-5 py-3 text-xs font-medium uppercase tracking-wide text-slate-500 bg-slate-50 align-top w-48">
                            {{ trans('main.Content') }}
                        </td>
                        <td class="px-5 py-3 text-sm text-slate-900">
                            {!! $task->content ?? '<span class="text-slate-400">—</span>' !!}
                        </td>
                    </tr>
                    <tr>
                        <td class="px-5 py-3 text-xs font-medium uppercase tracking-wide text-slate-500 bg-slate-50 align-top">
                            {{ trans('main.Deadline') }}
                        </td>
                        <td class="px-5 py-3 text-sm text-slate-700 font-mono">
                            {!! $task->dead_line ?? '<span class="text-slate-400 font-sans not-italic">—</span>' !!}
                        </td>
                    </tr>
                    <tr>
                        <td class="px-5 py-3 text-xs font-medium uppercase tracking-wide text-slate-500 bg-slate-50 align-top">
                            {{ trans('main.Tour') }}
                        </td>
                        <td class="px-5 py-3 text-sm text-slate-900">
                            @if($task->tourModel)
                                <span class="font-medium">{{ $task->tourModel->name }}</span>
                            @else
                                <span class="text-slate-400 italic">Without tour</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="px-5 py-3 text-xs font-medium uppercase tracking-wide text-slate-500 bg-slate-50 align-top">
                            {{ trans('main.Assignto') }}
                        </td>
                        <td class="px-5 py-3 text-sm text-slate-700">
                            @forelse($task->assigned_users as $user)
                                <span class="inline-flex items-center gap-1 rounded bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-700 mr-1">
                                    <x-ui.icon name="user" size="xs" class="text-slate-500" />
                                    {{ $user->name }}
                                </span>
                            @empty
                                <span class="text-slate-400 italic">Nobody assigned</span>
                            @endforelse
                        </td>
                    </tr>
                    <tr>
                        <td class="px-5 py-3 text-xs font-medium uppercase tracking-wide text-slate-500 bg-slate-50 align-top">
                            {{ trans('main.TaskType') }}
                        </td>
                        <td class="px-5 py-3 text-sm text-slate-700">
                            {!! \App\Task::$taskTypes[$task->task_type] ?? '<span class="text-slate-400">—</span>' !!}
                        </td>
                    </tr>
                    <tr>
                        <td class="px-5 py-3 text-xs font-medium uppercase tracking-wide text-slate-500 bg-slate-50 align-top">
                            {{ trans('main.Status') }}
                        </td>
                        <td class="px-5 py-3 text-sm">
                            @if(isset($status) && $status)
                                <span class="inline-flex items-center rounded bg-primary-50 px-2 py-0.5 text-xs font-medium text-primary-700">
                                    {{ $status->name }}
                                </span>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="px-5 py-3 text-xs font-medium uppercase tracking-wide text-slate-500 bg-slate-50 align-top">
                            {{ trans('main.Priority') }}
                        </td>
                        <td class="px-5 py-3 text-sm">
                            @if($task->priority)
                                <span class="inline-flex items-center gap-1 rounded bg-danger-50 px-2 py-0.5 text-xs font-medium text-danger-700">
                                    <x-ui.icon name="alert-triangle" size="xs" />
                                    Yes
                                </span>
                            @else
                                <span class="inline-flex items-center rounded bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600">No</span>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        @if(!empty($files))
            <div class="rounded border border-slate-200 bg-white shadow-subtle p-5">
                @component('component.files', ['files' => $files])@endcomponent
            </div>
        @endif
    </div>

    {{-- ─── Comments card ─────────────────────────────────────────────── --}}
    <span id="showPreviewBlock" data-info="{{ true }}" class="hidden"></span>
    <div class="rounded border border-slate-200 bg-white shadow-subtle flex flex-col max-h-[80vh]">
        <div class="border-b border-slate-200 px-5 py-3 flex items-center gap-2">
            <x-ui.icon name="messages-square" class="text-success-600" />
            <h3 class="text-sm font-semibold text-slate-900">{{ trans('main.Comments') }}</h3>
        </div>

        <div class="slimScrollDiv flex-1 overflow-y-auto px-5 py-4">
            <div class="chat" id="chat-box">
                <div id="show_comments"></div>
            </div>
        </div>

        <div class="border-t border-slate-200 bg-slate-50 px-5 py-3">
            <form method="POST" action="{{ route('comment.store') }}" enctype="multipart/form-data" id="form_comment" class="space-y-3">
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
                    <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.Files') }}</label>
                    @component('component.file_upload_field')@endcomponent
                </div>

                <input type="text" id="parent_comment" hidden name="parent" value="{{ null }}">
                <input type="text" id="default_reference_id" hidden name="reference_id" value="{{ $task->id }}">
                <input type="text" id="default_reference_type" hidden name="reference_type" value="{{ \App\Comment::$services['task'] }}">

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
@endsection

@section('post_scripts')
    <script src="{{ asset('js/comment.js') }}"></script>
@endsection
