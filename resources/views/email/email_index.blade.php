@extends('email.layout')
@section('title', 'Email')
@section('main-content')


    <style>
        /* Header: Back + folder name on the left, search fills the rest */
        .email-box-header {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: .75rem 1rem;
        }
        .email-box-header .email-box-title {
            display: flex;
            align-items: center;
            gap: .75rem;
        }
        .email-box-header .email-box-title .btn { display: inline-flex; align-items: center; }
        .email-box-header .email-search { flex: 1 1 320px; width: auto; }

        /* Folder lists */
        .email-folders .nav-link {
            display: flex;
            align-items: center;
            gap: .6rem;
            padding: .5rem .9rem;
            margin: 1px .5rem;
        }
        .email-folders .nav-link .ti {
            flex: 0 0 18px;
            width: 18px;
            font-size: 18px;
            text-align: center;
        }

        /* Mail rows */
        #emaillists table { table-layout: fixed; width: 100%; min-width: 760px; margin-bottom: 0; }
        #emaillists td { vertical-align: middle; padding: .75rem; border-color: #eef0f3; }
        #emaillists .email-row { cursor: pointer; }
        #emaillists .mailbox-name { width: 36%; }
        #emaillists .mailbox-attachment { width: 44px; text-align: center; color: #64748b; }
        #emaillists .mailbox-date { width: 150px; }
        #emaillists .mailbox-actions { width: 112px; }

        #emaillists .mailbox-sender { display: flex; align-items: center; gap: .625rem; min-width: 0; }
        #emaillists .mailbox-avatar {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 32px;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            color: #ffffff;
            font-size: .75rem;
            font-weight: 600;
            line-height: 1;
        }
        #emaillists .mailbox-sender-text { display: flex; flex-direction: column; min-width: 0; }
        #emaillists .mailbox-sender-name,
        #emaillists .mailbox-sender-email,
        #emaillists .mailbox-subject-text {
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        #emaillists .mailbox-sender-name { color: #1f2937; font-weight: 600; }
        #emaillists .mailbox-sender-email { color: #6b7280; font-size: .8125rem; }
        #emaillists .mailbox-subject-text { color: #6b7280; }
        #emaillists .email-unread .mailbox-subject-text { color: #1f2937; font-weight: 600; }
        #emaillists .mailbox-date-text {
            display: inline-flex;
            align-items: center;
            gap: .375rem;
            color: #6b7280;
            font-size: .8125rem;
            white-space: nowrap;
        }
        #emaillists .mailbox-actions-group { display: flex; justify-content: flex-end; gap: .375rem; }
    </style>
    <div class="box-body no-padding" id="emailList">

        <div class="mailbox-controls">
            <div class="pull-right">
            </div>
        </div>
        <div class="table-responsive mailbox-messages"   id="emaillists">
            <div  v-if="view">
                @include('email.parts.viewEmail')
            </div>
            <table class="table table-hover finder-disable" v-if="emailsArray && !view && !loading">
                <tbody>
                <tr v-if="emailsArray" v-for="(email, index) in emailsArray" class="email-row" :class="{ 'email-unread': email.header.seen == 0 }">
                    <td class="mailbox-name" @click="infoEmail(email)">
                        <div class="mailbox-sender">
                            <span class="mailbox-avatar" :style="'background-color: ' + getColorFromEmail(mailParty(email))">@{{ getInitials(mailParty(email)) }}</span>
                            <span class="mailbox-sender-text">
                                <span class="mailbox-sender-name" :title="extractName(mailParty(email))">@{{ extractName(mailParty(email)) }}</span>
                                <span class="mailbox-sender-email" :title="extractEmail(mailParty(email))">@{{ extractEmail(mailParty(email)) }}</span>
                            </span>
                        </div>
                    </td>
                    <td class="mailbox-subject onclick_redirect" @click="infoEmail(email)">
                        <span class="mailbox-subject-text" :title="email.header.subject">@{{ email.header.subject }}</span>
                    </td>

                    <td class="mailbox-attachment onclick_redirect" @click="infoEmail(email)">
                        <i v-if="email.attachments" class="ti ti-paperclip"></i>
                    </td>
                    <td class="mailbox-date onclick_redirect" @click="infoEmail(email)">
                        <span class="mailbox-date-text">
                            <i class="ti ti-clock"></i>
                            @{{ moment(email.header.date).format('YYYY-MM-DD HH:mm') }}
                        </span>
                    </td>
                    <td class="mailbox-actions">
                        <div class="mailbox-actions-group">
                            <a href="#" class="btn btn-sm dash-act dash-act-edit" @click.prevent="openModal(email)" title="Move to folder"><i class="ti ti-folder-share" aria-hidden="true"></i></a>
                            {{--<a class="btn btn-default btn-sm" @click="replyEmail(email)"><i class="fa fa-reply" aria-hidden="true"></i></a>--}}
                            <a href="#" class="btn btn-sm dash-act dash-act-delete" @click.prevent="deleteMail(email)" title="Delete"><i class="ti ti-trash" aria-hidden="true"></i></a>
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
