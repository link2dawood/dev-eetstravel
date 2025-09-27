@extends('email.layout')
@section('title','Index')
@section('main-content')


    <div class="box-body no-padding" id="emailList">

        <div class="mailbox-controls">
            <div class="pull-right">
            </div>
        </div>
        <div class="table-responsive mailbox-messages"   id="emaillists">
            <div  v-if="view">
                @include('email.parts.viewEmail')
            </div>
            <table class="table table-hover table-striped finder-disable" v-if="emailsArray && !view && !loading">
                <tbody>
                <tr v-if="emailsArray" style="cursor: pointer" v-for="(email, index) in emailsArray" >
                    <td class="mailbox-star onclick_redirect">
                    </td>

                    <td class="mailbox-name" @click="infoEmail(email)">
                        <a >
                            <div v-if="currentFolder == 'INBOX.Sent'">@{{email.header.to}}</div>
                            <div v-else>@{{email.header.from}}</div>
                        </a></td>
                    <td class="mailbox-subject onclick_redirect" @click="infoEmail(email)">
                        <b v-if="email.header.seen == 0">@{{email.header.subject}}</b>
                        <span v-else>@{{email.header.subject}}</span>
                    </td>

                    <td class="mailbox-attachment onclick_redirect">
                            <i v-if="email.attachments" class="fa fa-paperclip"></i>

                    </td>
                    <td class="mailbox-date onclick_redirect">
                        @{{moment(email.header.date).format('YYYY-MM-DD H:m:s')}}
                    </td>
                    <td style="width: 150px;">
                        <div class="btn-group pull-right">
                            <a href="#" class="btn btn-sm btn-default " @click="openModal(email)"><i class="fa fa-folder-open" aria-hidden="true"></i></a>
                            {{--<a class="btn btn-default btn-sm" @click="replyEmail(email)"><i class="fa fa-reply" aria-hidden="true"></i></a>--}}
                            <a class="btn btn-danger btn-sm " @click="deleteMail(email)"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                        </div>
                    </td>

                </tr>
                </tbody>
            </table>
        </div>
        <div class="box-footer no-padding">
            <div class="mailbox-controls">
                <div class="pull-right">
                    <ul class="pagination" v-if="!loading">

                        <li :class="{active: (page+1) === pageNumber, last: (pageNumber == totalPages && Math.abs(pageNumber - page) > 3), first:(pageNumber == 1 && Math.abs(pageNumber - page) > 3)}" v-for="pageNumber in totalPages" v-if="Math.abs(pageNumber - page) < 5 || pageNumber == totalPages || pageNumber == 1">
                            <a :key="pageNumber" href="#" @click="changePage(pageNumber)" >@{{ pageNumber }}</a>
                        </li>

                    </ul>
                </div>
            </div>
        </div>
    </div>
@stop