@if ($message->author->id !== Auth::id())
    @include('chats.component.chat_message')
    <!-- /.direct-chat-msg -->
@else
    @include('chats.component.chat_self_message')
@endif