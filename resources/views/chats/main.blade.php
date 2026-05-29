@extends('scaffold-interface.layouts.tabler-app')
@section('title','Chats')

@section('content')
<x-ui.page-header
    title="Chats"
    description="Direct messages and group conversations."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Chats'],
    ]"
/>

<script type="text/javascript" src="https://cdn.rawgit.com/samsonjs/strftime/master/strftime-min.js"></script>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
    {{-- Sidebar: chat list + actions --}}
    <aside class="lg:col-span-4">
        <div class="rounded border border-slate-200 bg-white overflow-hidden">
            <div class="bg-gradient-to-r from-warning-500 to-warning-600 text-white px-4 py-3 chat-group-title">
                <h3 class="text-sm font-semibold tracking-tight">{!! trans('main.Chats') !!}</h3>
            </div>

            <div class="p-3 space-y-2 border-b border-slate-200">
                {{-- Modal triggers — keep .add-direct-chat + data-link + data-target intact. --}}
                <a class="add-direct-chat block" data-toggle="modal" data-target="#myModal" data-link="/chat/renderUsersForChat">
                    <button type="button" class="inline-flex w-full items-center justify-center gap-1.5 rounded border border-slate-300 bg-white px-3 h-9 text-sm text-slate-700 hover:bg-slate-50">
                        <x-ui.icon name="user-plus" size="sm" />{!! trans('main.AddUserChat') !!}
                    </button>
                </a>
                <a class="add-direct-chat block" data-toggle="modal" data-target="#myModal" data-link="/chat/renderCustomChatCreateFrom">
                    <button type="button" class="inline-flex w-full items-center justify-center gap-1.5 rounded border border-slate-300 bg-white px-3 h-9 text-sm text-slate-700 hover:bg-slate-50">
                        <x-ui.icon name="users" size="sm" />{!! trans('main.AddChat') !!}
                    </button>
                </a>
                <a class="add-direct-chat block" id="deleteChatButton" data-toggle="modal" data-target="#myModal" data-link="/chat/renderCustomChatDeleteFrom">
                    <button type="button" class="inline-flex w-full items-center justify-center gap-1.5 rounded border border-danger-200 bg-white px-3 h-9 text-sm text-danger-600 hover:bg-danger-50">
                        <x-ui.icon name="trash" size="sm" />{!! trans('main.DeleteChat') !!}
                    </button>
                </a>
            </div>

            {{-- Chat list. .chats-list + .chat-title[data-id] are read by the
                 JS in this file's @section('post_scripts') below. --}}
            <ul class="nav nav-stacked chats-list list-none m-0 p-0 max-h-[60vh] overflow-y-auto">
                <li></li>
                @foreach($chats as $chat)
                    <li class="border-b border-slate-100 last:border-0">
                        <a href="#" data-id="{{ $chat->id }}"
                           class="chat-title block px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">
                            {{ $chat->title }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </aside>

    {{-- Chat container is populated via AJAX by .chat-title click handler. --}}
    <section class="lg:col-span-8 chat-container">
        <div class="rounded border border-slate-200 bg-white px-6 py-12 text-center text-sm text-slate-500">
            <x-ui.icon name="message-circle" size="md" class="mx-auto mb-2 text-slate-300" />
            Select a chat from the list to start messaging.
        </div>
    </section>
</div>

<style>
    /* Make the active chat in the sidebar stand out. The JS toggles .active
       on the <li> parent of .chat-title — matches the existing behaviour. */
    .chats-list li.active > .chat-title {
        background-color: rgb(240 253 250);
        color: rgb(15 118 110);
        font-weight: 600;
    }
</style>
@endsection

@section('post_scripts')
    <script type="text/javascript">
        $('#deleteChatButton').hide();

        var chat = {
            config: {
                chatContainer: $('body').find('.chat-container'),
                chatTitleContainer: $('body').find('.chat-title')
            },
            init: function () { chat.bind(); },
            bind: function () {
                $('body').on('click', '.chat-title', function () {
                    $('body').find('.chat-title').parent().removeClass('active');
                    $(this).parent().addClass('active');
                    var chatId = $(this).attr('data-id');
                    $.ajax({
                        type: "GET",
                        url: "/chat/" + chatId + "/renderChat",
                        data: {},
                        success: function (result) {
                            chat.config.chatContainer.html(result);
                            $('.direct-chat-messages').scrollTo('max', { 'duration': 500 });
                        },
                        error: function (result) { console.log(result); }
                    });
                    $('#deleteChatButton').show();
                });
            }
        };

        $(document).ready(function () { chat.init(); });
    </script>
    <script type="text/javascript">
        var chatUser = {
            config: {
                chatUserSelect: $('body').find('.chat-user-select'),
                chatsList: $('body').find('.chats-list'),
                customChatNameButton: $('body').find('#add_custom_chat')
            },
            init: function () { chatUser.bind(); },
            bind: function () {
                $('body').on('click', '.chat-user-select', function () { chatUser.selectUserChat($(this)); });
                $('body').on('click', '.custom-chat-user-select', function () { chatUser.addUserToChat($(this)); });
                $('body').on('click', '#add_custom_chat', function () {
                    let name_chat = $('#custom_chat_name').val();
                    $('#errors_message').css({ 'display': 'none' }).html('');
                    if (name_chat === '') {
                        $('#errors_message').css({ 'display': 'block' }).html('Field required for filling');
                        return false;
                    }
                    chatUser.createCustomChat();
                });
                $('body').on('click', '#delete_custom_chat', function () {
                    var chatContainer = $('body').find('.active .chat-title');
                    var chatId = chatContainer.data('id');
                    $.ajax({
                        type: "GET",
                        url: "/chat/deleteChat",
                        data: { 'id': chatId },
                        success: function (result) {
                            $('#deleteChatButton').hide();
                            chatContainer.remove();
                            $('#myModal').modal('hide');
                        },
                        error: function (result) { console.log(result); }
                    });
                });
                $('body').on('keypress', '#custom_chat_name', function (e) {
                    if (e.which == 13) chatUser.createCustomChat();
                });
                $('body').on('click', '.remove-user-from-chat', function () { chatUser.removeUserFromChat($(this)); });
            },
            addUserToChat: function (userContainer) {
                var chatId = userContainer.attr('data-chat-id');
                var userId = userContainer.attr('data-user-id');
                $.ajax({
                    type: "GET",
                    url: "/chat/addUserToCustomChat",
                    data: { 'userId': userId, 'chatId': chatId },
                    success: function (result) {
                        var chatContainer = $('body').find('.active .chat-title');
                        chatUser.selectUserChat(chatContainer);
                        $('#myModal').modal('hide');
                    },
                    error: function (result) { console.log('error', result); }
                });
            },
            removeUserFromChat: function (userContainer) {
                var userId = userContainer.attr('data-user-id');
                var chatId = userContainer.attr('data-chat-id');
                $.ajax({
                    type: "GET",
                    url: "/chat/removeUserFromChat",
                    data: { 'userId': userId, 'chatId': chatId },
                    success: function (result) {
                        userContainer.parent().parent().hide();
                        var chatContainer = $('body').find('.active .chat-title');
                        chatUser.selectUserChat(chatContainer);
                        $('#myModal').modal('hide');
                    },
                    error: function (result) { console.log('error', result); }
                });
            },
            selectUserChat: function (userContainer) {
                var userId = userContainer.attr('data-user-id');
                $.ajax({
                    type: "GET",
                    url: "/chat/getOrCreateChat",
                    data: { 'userId': userId },
                    success: function (result) {
                        if ($('.chat-title[data-id=' + result.id + ']').length == 0) {
                            chatUser.config.chatsList.append(
                                '<li class="border-b border-slate-100 last:border-0"><a href="#" data-id="' + result.id + '" class="chat-title block px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">' + result.name + '</a></li>'
                            );
                        } else {
                            $('.chat-title[data-id=' + result.id + ']').click();
                        }
                        $('#myModal').modal('hide');
                    },
                    error: function (result) { console.log('error', result); }
                });
            },
            createCustomChat: function () {
                var chatName = $('body').find('#custom_chat_name').val();
                var chatDescription = $('body').find('#custom_chat_description').val();
                $.ajax({
                    type: "GET",
                    url: "/chat/createCustomChat",
                    data: { 'chatName': chatName, 'chatDesription': chatDescription },
                    success: function (result) {
                        chatUser.config.chatsList.append(
                            '<li class="border-b border-slate-100 last:border-0"><a href="#" data-id="' + result.id + '" class="chat-title block px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">' + result.name + '</a></li>'
                        );
                        $('#myModal').modal('hide');
                    },
                    error: function (result) { console.log('error', result); }
                });
            }
        };
        chatUser.init();
    </script>

    <script>
        // Ensure CSRF token is sent with AJAX requests
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('input[name=_token]').val() } });
    </script>

    <script type="text/javascript">
        function init() {
            $('body').on('click', '.send-message', function () {
                var event = jQuery.Event("keypress");
                event.keyCode = 13;
                $(this).closest('div.input-group').find('input[name=message]').trigger(event);
                return false;
            });
            $('body').on('keypress', '.input-message', checkSend);
            scrollChatToBottom();
        }

        function checkSend(e) {
            if (e.keyCode === 13) sendMessage($(this).attr('data-chat-id'));
        }

        function sendMessage(chatId) {
            var messageText = $('.input-message[data-chat-id=' + chatId + ']').val();
            var userId = $('meta[name="user-id"]').attr('content');
            $('.input-message[data-chat-id=' + chatId + ']').val('');
            var data = { message: messageText, chat: chatId, user: userId, '_token': $('input[name=_token]').val() };
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
                data: {},
                success: function (result) {
                    $('.direct-chat-messages[data-chat-id=' + result.chat_id + ']').append(result.data);
                    $('.direct-chat-messages').scrollTo('max', { 'duration': 500 });
                    return false;
                },
                error: function (result) { console.log(result); }
            });
        }

        function addChat(data) {
            $.ajax({
                type: "GET",
                url: "/chat/" + data.chat + "/getNewChat",
                data: { 'userId': data.user },
                success: function (result) { $('.chats-list').append(result); },
                error: function (result) { console.log(result); }
            });
        }

        init();

        var pusher = new Pusher('dec55d4997ee67fe0e91', { cluster: 'eu', encrypted: true });
        var channel = pusher.subscribe('chat');
        channel.bind('new-message', addMessage);
        channel.bind('new-chat', addChat);
    </script>
@endsection
