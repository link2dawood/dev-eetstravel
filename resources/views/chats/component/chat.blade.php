{{-- AJAX-loaded chat panel — injected into .chat-container by main.blade.php
     after a sidebar chat is clicked.

     Critical selectors (read by main.blade.php JS + comment.js):
     - .direct-chat-messages[data-chat-id]
     - .direct-chat-contacts, .contacts-list, .remove-user-from-chat[data-chat-id][data-user-id]
     - .send-message[data-chat-id], .input-message[data-chat-id]
     - .add-direct-chat[data-link][data-target="#myModal"] with #add_user_to_chat
     - .delete with data-link to chat.deleteMsg --}}
<div class="rounded border border-slate-200 bg-white box box-warning direct-chat direct-chat-warning">
    <div class="box-header with-border border-b border-slate-200 px-4 py-3 flex items-start justify-between gap-3">
        <div class="flex items-center gap-2 min-w-0 flex-1">
            @if($chat->type == \App\Chat::CHAT_TYPE_DIRECT && $dashboard)
                <a href="#" id="return-contact" class="inline-flex h-8 w-8 items-center justify-center rounded border border-slate-200 bg-white text-slate-500 hover:bg-slate-50">
                    <x-ui.icon name="arrow-left" size="sm" />
                </a>
            @endif
            <h3 class="box-title text-sm font-semibold text-slate-900 truncate" style="vertical-align: middle;">{{ $chat->title }}</h3>
        </div>

        <div class="box-tools shrink-0 flex items-center gap-1">
            @if($chat->type == \App\Chat::CHAT_TYPE_CUSTOM)
                <a class="add-direct-chat" id="add_user_to_chat" data-toggle="modal" data-target="#myModal" data-link="/chat/{{ $chat->id }}/renderUsersForCustomChat">
                    <button type="button" class="btn btn-box-tool inline-flex h-7 w-7 items-center justify-center rounded text-slate-400 hover:bg-slate-100 hover:text-slate-700">
                        <i class="fa fa-plus"></i>
                    </button>
                </a>
            @endif
            <button type="button" class="btn btn-box-tool inline-flex h-7 w-7 items-center justify-center rounded text-slate-400 hover:bg-slate-100 hover:text-slate-700" data-widget="collapse"><i class="fa fa-minus"></i></button>
            @if($chat->type == \App\Chat::CHAT_TYPE_CUSTOM)
                <a data-toggle="modal" data-target="#myModal" class="btn btn-box-tool delete inline-flex h-7 w-7 items-center justify-center rounded text-danger-500 hover:bg-danger-50 hover:text-danger-600" data-link="{{ route('chat.deleteMsg', ['id' => $chat->id], false) }}">
                    <i class="fa fa-trash-o" aria-hidden="true"></i>
                </a>
            @endif
            <button type="button" class="btn btn-box-tool inline-flex h-7 w-7 items-center justify-center rounded text-slate-400 hover:bg-slate-100 hover:text-slate-700" data-widget="remove"><i class="fa fa-times"></i></button>
        </div>
    </div>

    <div class="box-body p-4" style="min-height: 300px">
        <div class="direct-chat-messages max-h-[55vh] overflow-y-auto" data-chat-id="{{ $chat->id }}">
            @foreach($chat->messages as $message)
                @include('chats.component.message')
            @endforeach
        </div>

        <div class="direct-chat-contacts">
            <ul class="contacts-list list-none m-0 p-0 divide-y divide-slate-100">
                @foreach($chat->users as $user)
                    <li>
                        <a href="#" class="flex items-center gap-3 px-3 py-2 hover:bg-slate-50">
                            <img class="contacts-list-img h-9 w-9 rounded-full object-cover"
                                 src="{{ $user->avatar_file_name == null ? asset('img/avatar.png') : $user->avatar->url('logo') }}">
                            <div class="contacts-list-info flex-1 min-w-0">
                                <span class="contacts-list-name text-sm font-medium text-slate-800 truncate">{{ $user->name }}</span>
                            </div>
                            @if($chat->type == \App\Chat::CHAT_TYPE_CUSTOM)
                                <div class="shrink-0">
                                    <a href="#" class="remove-user-from-chat inline-flex h-7 w-7 items-center justify-center rounded text-slate-400 hover:bg-danger-50 hover:text-danger-600" data-chat-id="{{ $chat->id }}" data-user-id="{{ $user->id }}">
                                        <i class="fa fa-times" aria-hidden="true"></i>
                                    </a>
                                </div>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="box-footer border-t border-slate-200 bg-slate-50 px-4 py-3">
        <form action="#" method="post">
            {{ csrf_field() }}
            <div class="input-group flex items-center gap-2">
                <input type="text" name="message" placeholder="Type Message …"
                       class="form-control input-message block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600"
                       data-chat-id="{{ $chat->id }}">
                <span class="input-group-btn shrink-0">
                    <button type="submit" class="btn btn-success btn-flat send-message inline-flex items-center gap-1.5 rounded bg-success-600 px-4 h-9 text-sm text-white hover:bg-success-700" data-chat-id="{{ $chat->id }}">
                        <x-ui.icon name="send" size="sm" />{!! trans('main.Send') !!}
                    </button>
                </span>
            </div>
        </form>
    </div>
</div>
