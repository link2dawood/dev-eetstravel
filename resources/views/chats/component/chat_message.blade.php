{{-- Other-user message (incoming, left-aligned). Class names preserved
     because the legacy CSS in app.css styles .direct-chat-msg /
     .direct-chat-text / .direct-chat-img directly. --}}
<div class="direct-chat-msg flex items-start gap-3 mb-4">
    <img class="direct-chat-img h-9 w-9 rounded-full object-cover shrink-0"
         src="{{ $message->author->avatar_file_name == null ? asset('img/avatar.png') : $message->author->avatar->url('logo') }}">
    <div class="flex-1 min-w-0">
        <div class="direct-chat-info clearfix flex items-baseline justify-between gap-2 mb-1">
            <span class="direct-chat-name text-sm font-medium text-slate-800">{{ $message->author->name }}</span>
            <span class="direct-chat-timestamp text-xs text-slate-500">{{ $message->created_at }}</span>
        </div>
        <div class="direct-chat-text inline-block rounded-lg rounded-tl-none bg-slate-100 text-slate-800 px-3 py-2 text-sm max-w-[36rem]">
            {!! nl2br(e($message->message)) !!}
        </div>
    </div>
</div>
