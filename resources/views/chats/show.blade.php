@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Chat')
@section('content')
<div class="card">
    <div class="card-header"><h1 class="card-title">{{ $chat->title }}</h1></div>
    <div class="card-body">
        <p>{{ $chat->description }}</p>
        <p class="text-muted">Conversation preview. Open Chats to send messages or manage chat members.</p>
        <a class="btn btn-primary mb-3" href="{{ route('chat.main') }}">Open Chats</a>
        <div class="direct-chat-messages" aria-label="Conversation messages">
            @forelse($chat->messages as $message)
                @include('chats.component.message', ['message' => $message])
            @empty
                <p class="text-muted">No messages yet.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
