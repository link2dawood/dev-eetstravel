@extends('scaffold-interface.layouts.tabler-app')
@section('title','Chat')

@section('content')
<x-ui.page-header
    :title="$chat->title"
    :description="'Chat #' . $chat->id"
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Chats', 'href' => route('chat.main')],
        ['label' => 'Chat #' . $chat->id],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="{{ route('chat.main') }}" variant="ghost" icon="arrow-left">{!! trans('main.Back') !!}</x-ui.button>
    </x-slot>
</x-ui.page-header>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-4">

    {{-- Sidebar widgets (kept for parity with the legacy layout). --}}
    <aside class="lg:col-span-4 space-y-4">
        <div class="rounded border border-slate-200 bg-white overflow-hidden">
            <div class="bg-gradient-to-r from-warning-500 to-warning-600 text-white px-4 py-3 chat-group-title">
                <h3 class="text-sm font-semibold tracking-tight">{!! trans('main.Chats') !!}</h3>
            </div>
            <ul class="nav nav-stacked list-none m-0 p-0 divide-y divide-slate-100">
                <li><a href="#" class="flex items-center justify-between px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">{!! trans('main.Projects') !!} <span class="inline-flex items-center rounded bg-info-100 px-2 py-0.5 text-xs font-medium text-info-700">31</span></a></li>
                <li><a href="#" class="flex items-center justify-between px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">{!! trans('main.Tasks') !!} <span class="inline-flex items-center rounded bg-info-100 px-2 py-0.5 text-xs font-medium text-info-700">5</span></a></li>
                <li><a href="#" class="flex items-center justify-between px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">{!! trans('main.CompletedProjects') !!} <span class="inline-flex items-center rounded bg-success-100 px-2 py-0.5 text-xs font-medium text-success-700">12</span></a></li>
                <li><a href="#" class="flex items-center justify-between px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">{!! trans('main.Followers') !!} <span class="inline-flex items-center rounded bg-danger-100 px-2 py-0.5 text-xs font-medium text-danger-700">842</span></a></li>
            </ul>
        </div>

        <div class="rounded border border-slate-200 bg-white overflow-hidden">
            <div class="bg-gradient-to-r from-success-500 to-success-600 text-white px-4 py-3 chat-group-title">
                <h3 class="text-sm font-semibold tracking-tight">{!! trans('main.DirectChats') !!}</h3>
            </div>
            <ul class="nav nav-stacked list-none m-0 p-0 divide-y divide-slate-100">
                <li><a href="#" class="flex items-center justify-between px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">Nicola Smith <span class="inline-flex items-center rounded bg-info-100 px-2 py-0.5 text-xs font-medium text-info-700">31</span></a></li>
                <li><a href="#" class="flex items-center justify-between px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">Andre White <span class="inline-flex items-center rounded bg-info-100 px-2 py-0.5 text-xs font-medium text-info-700">31</span></a></li>
                <li class="p-3 text-right">
                    <button type="button" class="inline-flex items-center gap-1 rounded bg-success-600 px-3 h-8 text-xs text-white hover:bg-success-700">
                        <x-ui.icon name="user-plus" size="xs" />{!! trans('main.AddUserChat') !!}
                    </button>
                </li>
            </ul>
        </div>
    </aside>

    {{-- Chat panel. Note: `.direct-chat-messages` is referenced by the JS
         scroll helpers and Pusher addMessage handler; do not rename. --}}
    <section class="lg:col-span-8">
        <div class="rounded border border-slate-200 bg-white shadow-subtle overflow-hidden">
            <div class="border-b border-slate-200 px-4 py-3 flex items-start justify-between gap-3">
                <div class="min-w-0 flex-1">
                    <h3 class="text-sm font-semibold text-slate-900 truncate">{{ $chat->title }}</h3>
                    @if(!empty($chat->description))
                        <p class="mt-0.5 text-xs text-slate-500 truncate">{{ $chat->description }}</p>
                    @endif
                </div>
                <div class="shrink-0 flex items-center gap-1">
                    <span class="inline-flex items-center rounded-full bg-success-600 px-2 py-0.5 text-xs font-medium text-white" title="3 New Messages">3</span>
                </div>
            </div>

            <div class="p-4">
                <div class="direct-chat-messages min-h-[300px] max-h-[60vh] overflow-y-auto pr-1 space-y-3">
                    @foreach($chat->messages as $message)
                        @include('chats.component.message')
                    @endforeach
                </div>
            </div>

            <div class="border-t border-slate-200 bg-slate-50 px-4 py-3">
                {{-- onsubmit:return false prevents the legacy form action="#"
                     from anchoring on the page after the click handler fires.
                     `input-group` class is retained because the legacy JS
                     uses $(this).closest('div.input-group') to find the input. --}}
                <form action="#" method="post" onsubmit="return false;">
                    {{ csrf_field() }}
                    <div class="input-group flex items-center gap-2">
                        <input type="text" name="message" placeholder="Type Message …" autocomplete="off"
                               class="input-message block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                        <button type="submit"
                                class="send-message shrink-0 inline-flex items-center gap-1.5 rounded bg-success-600 px-4 h-9 text-sm text-white hover:bg-success-700">
                            <x-ui.icon name="send" size="sm" />{!! trans('main.Send') !!}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
@endsection

@section('post_scripts')
    <script type="text/javascript" src="https://cdn.rawgit.com/samsonjs/strftime/master/strftime-min.js"></script>
    <script type="text/javascript" src="{{ asset('js/pusher.min.js') }}"></script>
    <script>
        // CSRF for AJAX
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('input[name=_token]').val() } });
    </script>
    <script type="text/javascript">
        function init() {
            $('.send-message').click(function () {
                var event = jQuery.Event("keypress");
                event.keyCode = 13;
                $(this).closest('div.input-group').find('input[name=message]').trigger(event);
            });
            $('.input-message').keypress(checkSend);
            scrollChatToBottom();
        }

        function checkSend(e) { if (e.keyCode === 13) return sendMessage(); }

        function sendMessage() {
            var messageText = $('.input-message').val();
            var userId = $('meta[name="user-id"]').attr('content');
            $('.input-message').val('');
            var data = { message: messageText, chat: {{ $chat->id }}, user: userId };
            $.post('/chat/message', data).success(sendMessageSuccess);
            scrollChatToBottom();
            return false;
        }

        function sendMessageSuccess() { $('.input-message').val(''); }
        function scrollChatToBottom() { $('.direct-chat-messages').scrollTo('max', { 'duration': 500 }); }

        function addMessage(data) {
            $.ajax({
                type: "GET",
                url: "/chat/" + data.message + "/getMessage",
                data: { 'chat': {{ $chat->id }} },
                success: function (result) {
                    $('.direct-chat-messages').append(result);
                    $('.direct-chat-messages').scrollTo('max', { 'duration': 500 });
                },
                error: function (result) { console.log(result); }
            });
        }

        $(init);

        var pusher = new Pusher('dec55d4997ee67fe0e91', { cluster: 'eu', encrypted: true });
        var channel = pusher.subscribe('chat');
        channel.bind('new-message', addMessage);
    </script>
@endsection
