@extends('email.layout')
@section('title','Inbox')

@section('main-content')
{{-- Default email list rendered inside the layout's @yield('main-content').
     All Vue directives (v-if, v-for, @click, :class, :key) preserved. --}}
<div id="emailList" class="px-0">
    <div class="mailbox-controls"></div>

    <div class="table-responsive overflow-x-auto mailbox-messages" id="emaillists">
        <div v-if="view">
            @include('email.parts.viewEmail')
        </div>

        <table class="finder-disable min-w-full divide-y divide-slate-200 text-sm" v-if="emailsArray && !view && !loading">
            <tbody class="divide-y divide-slate-100">
                <tr v-if="emailsArray" v-for="(email, index) in emailsArray"
                    class="cursor-pointer hover:bg-slate-50">
                    <td class="mailbox-star onclick_redirect px-3 py-2 text-slate-300 w-6"></td>

                    <td class="mailbox-name px-3 py-2 max-w-[14rem] truncate" @click="infoEmail(email)">
                        <a class="text-slate-700 hover:text-primary-600">
                            <div v-if="currentFolder == 'INBOX.Sent'">@{{ email.header.to }}</div>
                            <div v-else>@{{ email.header.from }}</div>
                        </a>
                    </td>

                    <td class="mailbox-subject onclick_redirect px-3 py-2 text-slate-800 max-w-md truncate" @click="infoEmail(email)">
                        <b v-if="email.header.seen == 0">@{{ email.header.subject }}</b>
                        <span v-else>@{{ email.header.subject }}</span>
                    </td>

                    <td class="mailbox-attachment onclick_redirect px-3 py-2 text-slate-400 w-8">
                        <x-ui.icon v-if="email.attachments" name="paperclip" size="sm" />
                    </td>

                    <td class="mailbox-date onclick_redirect px-3 py-2 text-xs text-slate-500 whitespace-nowrap">
                        @{{ moment(email.header.date).format('YYYY-MM-DD H:m:s') }}
                    </td>

                    <td class="px-3 py-2 w-[150px]">
                        <div class="btn-group flex items-center justify-end gap-1">
                            <a href="#" class="inline-flex h-8 w-8 items-center justify-center rounded border border-slate-300 bg-white text-slate-600 hover:bg-slate-50" @click="openModal(email)">
                                <x-ui.icon name="folder-open" size="sm" />
                            </a>
                            <a class="inline-flex h-8 w-8 items-center justify-center rounded border border-danger-300 bg-white text-danger-600 hover:bg-danger-50 cursor-pointer" @click="deleteMail(email)">
                                <x-ui.icon name="trash" size="sm" />
                            </a>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="border-t border-slate-200 px-4 py-3 flex items-center justify-end">
        <ul class="pagination list-none m-0 p-0 flex items-center gap-1" v-if="!loading">
            <li v-for="pageNumber in totalPages"
                v-if="Math.abs(pageNumber - page) < 5 || pageNumber == totalPages || pageNumber == 1"
                :class="{ active: (page+1) === pageNumber, last: (pageNumber == totalPages && Math.abs(pageNumber - page) > 3), first: (pageNumber == 1 && Math.abs(pageNumber - page) > 3) }">
                <a :key="pageNumber" href="#" @click="changePage(pageNumber)"
                   class="inline-flex h-8 w-8 items-center justify-center rounded border border-slate-300 bg-white text-sm text-slate-700 hover:bg-slate-50 [&.active]:bg-primary-600 [&.active]:text-white [&.active]:border-primary-600">
                    @{{ pageNumber }}
                </a>
            </li>
        </ul>
    </div>
</div>
@stop
