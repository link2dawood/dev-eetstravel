{{-- Loaded into #myModal via .add-direct-chat data-link wiring in main.blade.php
     (specifically /chat/{chatId}/renderUsersForCustomChat). Selectors preserved:
     .custom-chat-user-select[data-chat-id][data-user-id] handled in main.blade.php. --}}
<div class="modal-dialog" role="document">
    <div class="modal-content rounded border border-slate-200 bg-white shadow-lg">
        <div class="modal-header border-b border-slate-200 px-5 py-3 text-sm font-medium text-slate-700">
            {!! trans('main.Addusertochat') !!}
        </div>
        <div class="modal-body px-5 py-4">
            <ul class="nav nav-stacked list-none m-0 p-0 max-h-[50vh] overflow-y-auto divide-y divide-slate-100">
                <input type="hidden" id="chat_id">
                @if(count($users))
                    @foreach($users as $user)
                        <li>
                            <a href="#" class="custom-chat-user-select flex items-center gap-2 px-3 py-2.5 text-sm text-slate-700 hover:bg-slate-50"
                               data-chat-id="{{ $chatId }}" data-user-id="{{ $user->id }}">
                                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-primary-100 text-primary-700 text-xs font-semibold">
                                    {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                                </span>
                                {{ $user->name }}
                            </a>
                        </li>
                    @endforeach
                @else
                    <li class="px-3 py-4 text-center text-xs text-slate-500">{!! trans('main.NOUSERS') !!}</li>
                @endif
            </ul>
        </div>
    </div>
</div>
