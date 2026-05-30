@extends('email.layout')
@section('title','Inbox (legacy)')

@section('main-content')
@if(!$imapConnected)
@else
{{-- Legacy non-Vue email list. .moveToFolder / .reply / .delete with
     data-link / data-message-id / data-reply-* selectors are wired by
     surrounding code; preserve. --}}
<div class="px-0">

    <div class="mailbox-controls border-b border-slate-200 px-5 py-3 flex items-center justify-end">
        @if($mails)
            <div class="text-xs text-slate-500 mr-3">
                {{ ($page-1) * $per_page + 1 }} - {{ ($page-1) * $per_page + count($mails) }} / {{ $mailsCount }}
            </div>

            <div class="btn-group flex items-center gap-1">
                @if($page != 1)
                    @if(Route::getCurrentRoute()->getName() == 'email.index')
                        <a href="{{ route('email.index', ['page' => $page-1]) }}">
                    @endif
                    @if(Route::getCurrentRoute()->getName() == 'email.search_result' || Route::getCurrentRoute()->getName() == 'email.search')
                        <a href="{{ route('email.search', ['page' => $page-1, 'searched' => $search]) }}">
                    @endif
                    @if(Route::getCurrentRoute()->getName() == 'email.folder')
                        <a href="{{ route('email.folder', ['name' => $page-1, 'page' => $currentFolder]) }}">
                    @endif
                @endif
                    <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded border border-slate-300 bg-white text-slate-600 hover:bg-slate-50">
                        <x-ui.icon name="chevron-left" size="sm" />
                    </button>
                </a>

                @if(Route::getCurrentRoute()->getName() == 'email.index')
                    <a href="{{ route('email.index', ['page' => $page+1]) }}">
                @endif
                @if(Route::getCurrentRoute()->getName() == 'email.search_result' || Route::getCurrentRoute()->getName() == 'email.search')
                    <a href="{{ route('email.search', ['page' => $page+1, 'searched' => $search]) }}">
                @endif
                @if(Route::getCurrentRoute()->getName() == 'email.folder')
                    <a href="{{ route('email.folder', ['name' => $page+1, 'page' => $currentFolder]) }}">
                @endif
                    <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded border border-slate-300 bg-white text-slate-600 hover:bg-slate-50">
                        <x-ui.icon name="chevron-right" size="sm" />
                    </button>
                </a>
            </div>
        @endif
    </div>

    <div class="overflow-x-auto">
        <table class="finder-disable min-w-full divide-y divide-slate-200 text-sm">
            <tbody class="divide-y divide-slate-100">
                @if(!$mails)
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center">
                            <h2 class="text-base font-medium text-slate-700">
                                @if(!isset($search))The folder is empty@else No search result @endif
                            </h2>
                        </td>
                    </tr>
                @endif
                @foreach($mails as $mail)
                    <tr class="cursor-pointer hover:bg-slate-50" data-link="{{ route('email.mail', ['id' => $mail->message_id, 'currentFolder' => $currentFolder]) }}">
                        <td class="mailbox-star onclick_redirect px-3 py-2 text-slate-300 w-6"></td>
                        <td class="mailbox-name onclick_redirect px-3 py-2 max-w-[14rem] truncate">
                            <a href="{{ route('email.mail', ['id' => $mail->getNumber(), 'currentFolder' => $currentFolder]) }}" class="text-slate-700 hover:text-primary-600">
                                @if(\App\Helper\AdminHelper::emailCheck($mail))
                                    @if($currentFolder == 'INBOX.Sent')
                                        @php
                                            $addresses = [];
                                            $toArray = $mail->getTo();
                                            array_walk($toArray, function ($to) use (&$addresses) { $addresses[] = $to->getAddress(); });
                                        @endphp
                                        {{ implode(', ', $addresses) }}
                                    @else
                                        {{ $mail->getFrom() }}
                                    @endif
                                @endif
                            </a>
                        </td>
                        <td class="mailbox-subject onclick_redirect px-3 py-2 text-slate-800 max-w-md truncate">{{ $mail->getSubject() }}</td>
                        <td class="mailbox-attachment onclick_redirect px-3 py-2 w-8"></td>
                        <td class="mailbox-date onclick_redirect px-3 py-2 text-xs text-slate-500 whitespace-nowrap">
                            @if($mail->getDate())
                                {{ \Carbon\Carbon::createFromTimestamp($mail->getDate()->getTimestamp())->diffForHumans() }}
                            @endif
                        </td>
                        <td class="px-3 py-2 w-[150px]">
                            <div class="btn-group flex items-center justify-end gap-1">
                                <a href="#" class="moveToFolder inline-flex h-8 w-8 items-center justify-center rounded border border-slate-300 bg-white text-slate-600 hover:bg-slate-50"
                                   data-message-id="{{ $mail->getNumber() }}"
                                   data-message-folder="{{ $currentFolder }}"
                                   data-link="{{ route('email.getMoveToForm', ['id' => $mail->getNumber(), 'folder' => $currentFolder], false) }}">
                                    <x-ui.icon name="folder-open" size="sm" />
                                </a>
                                <a class="reply inline-flex h-8 w-8 items-center justify-center rounded border border-slate-300 bg-white text-slate-600 hover:bg-slate-50 cursor-pointer"
                                   data-reply-message="{{ $mail->getNumber() }}"
                                   data-reply-folder="{{ $currentFolder }}"
                                   data-to="@if($currentFolder == 'INBOX.Sent'){{ implode(',', $addresses) }}@else{{ $mail->getFrom() }}@endif"
                                   data-link="{{ route('email.getComposeForm', ['id' => $mail->getNumber(), 'folder' => $currentFolder], false) }}">
                                    <x-ui.icon name="corner-up-left" size="sm" />
                                </a>
                                <a data-toggle="modal" data-target="#myModal"
                                   class="delete inline-flex h-8 w-8 items-center justify-center rounded border border-danger-300 bg-white text-danger-600 hover:bg-danger-50 cursor-pointer"
                                   data-link="{{ route('email.deleteMsg', ['id' => $mail->getNumber(), 'folder' => $currentFolder], false) }}">
                                    <x-ui.icon name="trash" size="sm" />
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@stop
