{{-- Self-message (outgoing, right-aligned). Class names preserved. --}}
<div class="direct-chat-msg right flex items-start gap-3 mb-4 flex-row-reverse">
    <img class="direct-chat-img h-9 w-9 rounded-full object-cover shrink-0"
         src="{{ $message->author->avatar_file_name == null ? asset('img/avatar.png') : $message->author->avatar->url('logo') }}"
         alt="Logo">
    <div class="flex-1 min-w-0 text-right">
        <div class="direct-chat-info clearfix flex items-baseline justify-between gap-2 mb-1 flex-row-reverse">
            <span class="direct-chat-name text-sm font-medium text-slate-800">{{ $message->author->name }}</span>
            <span class="direct-chat-timestamp text-xs text-slate-500">{{ $message->created_at }}</span>
        </div>
        <div class="direct-chat-text inline-block rounded-lg rounded-tr-none bg-primary-600 text-white px-3 py-2 text-sm max-w-[36rem] text-left">
            {{ nl2br($message->message) }}
        </div>
    </div>
</div>
