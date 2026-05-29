@extends('scaffold-interface.layouts.tabler-app')
@section('title','Chats')

@section('content')
<x-ui.page-header
    title="Chats"
    description="All chat rooms and direct conversations in the system."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Chats'],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="{{ route('chat.main') }}" variant="primary" icon="message-circle">Open chat</x-ui.button>
    </x-slot>
</x-ui.page-header>

@if(count($chatsData) === 0)
    <div class="rounded border border-slate-200 bg-white">
        <x-ui.empty-state icon="message-circle" title="No chats yet" message="Open the chat panel to start a direct conversation or a group chat.">
            <x-ui.button as="a" href="{{ route('chat.main') }}" variant="primary" icon="message-circle">Open chat</x-ui.button>
        </x-ui.empty-state>
    </div>
@else
    <div class="rounded border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table id="chats-table" class="min-w-full divide-y divide-slate-200 text-sm" style="background:#fff">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs font-medium uppercase tracking-wide text-slate-500">
                        <th class="px-4 py-3">ID</th>
                        <th class="px-4 py-3">{!! trans('main.Title') !!}</th>
                        <th class="px-4 py-3">{!! trans('main.Description') !!}</th>
                        <th class="px-4 py-3">{!! trans('main.Type') !!}</th>
                        <th class="px-4 py-3">{!! trans('main.Author') !!}</th>
                        <th class="px-4 py-3 text-right actions-button" style="width: 140px">{!! trans('main.Actions') !!}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($chatsData as $chat)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-mono text-xs text-slate-500">#{{ $chat->id }}</td>
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $chat->title }}</td>
                            <td class="px-4 py-3 text-slate-700 max-w-xl truncate">{{ $chat->description }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $chat->type }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $chat->author }}</td>
                            <td class="px-4 py-3"><div class="flex items-center justify-end gap-1">{!! $chat->action_buttons !!}</div></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection
