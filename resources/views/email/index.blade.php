@php
    use App\Email;
    use App\User;
    $user = Auth::user();
    $userName = "Unknown User";
@endphp
@extends('scaffold-interface.layouts.tabler-app')

@section('content')
<x-ui.page-header
    title="Email"
    description="Inbox + sent mail (legacy in-app webmail)."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Emails'],
    ]"
/>

@if(Auth::user()->can('dashboard.inbox'))
<section class="mail dashboard">
    <div class="rounded border border-slate-200 bg-white shadow-subtle">
        <div class="p-4 sm:p-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">

                {{-- Left sidebar: compose button + tabs --}}
                <div class="lg:col-span-4">
                    <div class="rounded border border-slate-200 bg-primary-50/30">
                        <div class="p-4 space-y-3">
                            <input type="text" placeholder="Search"
                                   class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">

                            <a href="#compose" data-bs-toggle="modal"
                               class="inline-flex w-full items-center justify-center gap-1.5 rounded bg-primary-600 px-4 h-10 text-sm font-medium text-white hover:bg-primary-700">
                                <x-ui.icon name="square-pen" size="sm" /> Compose
                            </a>

                            <ul class="nav nav-tabs nav-tabs-vertical list-none m-0 p-0 space-y-1" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active inline-flex w-full items-center gap-2 rounded px-3 h-9 text-sm text-slate-700 hover:bg-white [&.active]:bg-primary-600 [&.active]:text-white"
                                            id="inbox-tab" data-bs-toggle="tab" data-bs-target="#inbox-tab-pane"
                                            type="button" role="tab" aria-controls="inbox-tab-pane" aria-selected="true">
                                        <x-ui.icon name="inbox" size="sm" /> Inbox
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link inline-flex w-full items-center gap-2 rounded px-3 h-9 text-sm text-slate-700 hover:bg-white [&.active]:bg-primary-600 [&.active]:text-white"
                                            id="sent-tab" data-bs-toggle="tab" data-bs-target="#sent-tab-pane"
                                            type="button" role="tab" aria-controls="sent-tab-pane" aria-selected="false">
                                        <x-ui.icon name="send" size="sm" /> Sent emails
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content" id="myTabContent">
                                {{-- Inbox tab --}}
                                <div class="tab-pane fade show active" id="inbox-tab-pane" role="tabpanel" aria-labelledby="inbox-tab" tabindex="0">
                                    <p class="text-xs font-medium uppercase tracking-wide text-slate-500 mt-3 mb-2">Inbox</p>
                                    <ul class="email-user-list list-none m-0 p-0 space-y-2 max-h-[60vh] overflow-y-auto">
                                        @if(!empty($emails))
                                            @foreach($emails as $email)
                                                @php $id = $email->id; @endphp
                                                <li class="user-item active">
                                                    <a href="#" class="text-decoration-none block rounded border border-slate-200 bg-white hover:bg-slate-50"
                                                       onclick="myfunction('{{ $id }}','inbox')">
                                                        <div class="p-3">
                                                            <div class="flex items-center justify-between gap-2">
                                                                @php
                                                                    $users = User::where('email', $email->from)->get();
                                                                    foreach($users as $u) { $userName = $u->name; }
                                                                @endphp
                                                                <div class="flex items-center gap-2 min-w-0">
                                                                    <img src="https://t4.ftcdn.net/jpg/00/97/00/09/240_F_97000908_wwH2goIihwrMoeV9QF3BW6HtpsVFaNVM.jpg"
                                                                         alt="" class="user-img h-9 w-9 rounded-full object-cover shrink-0">
                                                                    <div class="min-w-0">
                                                                        <h3 class="card-title text-sm font-medium text-slate-900 truncate m-0">{{ $userName }}</h3>
                                                                        <p class="card-text mail-text text-xs text-slate-500 truncate m-0">{{ $email->from }}</p>
                                                                    </div>
                                                                </div>
                                                                <div class="text-right shrink-0">
                                                                    <p class="card-text text-xs text-slate-500 m-0">{{ $email->date }}</p>
                                                                    <div class="mail-status"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </li>
                                            @endforeach
                                        @endif
                                    </ul>
                                </div>

                                {{-- Sent tab --}}
                                <div class="tab-pane fade" id="sent-tab-pane" role="tabpanel" aria-labelledby="sent-tab" tabindex="0">
                                    <p class="text-xs font-medium uppercase tracking-wide text-slate-500 mt-3 mb-2">Sent</p>
                                    <ul class="email-user-list list-none m-0 p-0 space-y-2 max-h-[60vh] overflow-y-auto">
                                        @if(!empty($sentemails))
                                            @foreach($sentemails as $sentemail)
                                                @php $id = $sentemail->id; @endphp
                                                <li class="user-item active">
                                                    <a href="#" class="text-decoration-none block rounded border border-slate-200 bg-white hover:bg-slate-50"
                                                       onclick="myfunction('{{ $id }}','sent')">
                                                        <div class="p-3">
                                                            <div class="flex items-center justify-between gap-2">
                                                                @php
                                                                    $users = User::where('email', $sentemail->to)->get();
                                                                    $userName = "Outside User";
                                                                    foreach($users as $u) { $userName = $u->name; }
                                                                @endphp
                                                                <div class="flex items-center gap-2 min-w-0">
                                                                    <img src="https://t4.ftcdn.net/jpg/00/97/00/09/240_F_97000908_wwH2goIihwrMoeV9QF3BW6HtpsVFaNVM.jpg"
                                                                         alt="" class="user-img h-9 w-9 rounded-full object-cover shrink-0">
                                                                    <div class="min-w-0">
                                                                        <h3 class="card-title text-sm font-medium text-slate-900 truncate m-0">To: {{ $userName }}</h3>
                                                                        <p class="card-text mail-text text-xs text-slate-500 truncate m-0">{{ $sentemail->to }}</p>
                                                                    </div>
                                                                </div>
                                                                <div class="text-right shrink-0">
                                                                    <p class="card-text text-xs text-slate-500 m-0">{{ $sentemail->date }}</p>
                                                                    <div class="mail-status"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </li>
                                            @endforeach
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right: email viewer --}}
                @if(count($emails) != "0")
                    <div class="lg:col-span-8">
                        <div class="rounded border border-slate-200 bg-white">
                            <div class="p-4 sm:p-6 flex flex-col gap-4">
                                <div class="flex items-center justify-between border-b border-slate-200 pb-4">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <img src="https://t4.ftcdn.net/jpg/00/97/00/09/240_F_97000908_wwH2goIihwrMoeV9QF3BW6HtpsVFaNVM.jpg"
                                             alt="" class="user-img h-10 w-10 rounded-full object-cover shrink-0">
                                        <div class="min-w-0">
                                            <h1 class="card-title username text-sm font-semibold text-slate-900 truncate m-0">{{ $userName }}</h1>
                                            <p class="card-text text-xs text-slate-500 m-0">1 day ago</p>
                                        </div>
                                    </div>
                                    <div class="btn-group relative">
                                        <button type="button" class="btn px-2 inline-flex h-8 w-8 items-center justify-center rounded text-slate-400 hover:bg-slate-100 hover:text-slate-700" data-bs-toggle="dropdown" aria-expanded="false">
                                            <x-ui.icon name="more-vertical" size="sm" />
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end hidden absolute right-0 mt-1 min-w-[160px] rounded border border-slate-200 bg-white shadow-lg p-1">
                                            <li><button class="dropdown-item block w-full text-left rounded px-3 py-1.5 text-sm hover:bg-slate-50" type="button">Action</button></li>
                                            <li><button class="dropdown-item block w-full text-left rounded px-3 py-1.5 text-sm hover:bg-slate-50" type="button">Another action</button></li>
                                            <li><button class="dropdown-item block w-full text-left rounded px-3 py-1.5 text-sm hover:bg-slate-50" type="button">Something else here</button></li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="email-wrapper space-y-4">
                                    <div class="emails-list accordion space-y-2" id="accordionExample">
                                        <div class="accordion-item rounded border border-slate-200">
                                            <h2 class="accordion-header" id="headingOne">
                                                <div class="flex items-center justify-between gap-2 px-3 py-2">
                                                    <button class="accordion-button flex-1 text-left flex items-center min-w-0 bg-transparent" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                        <span class="subject font-medium text-slate-900 truncate">{{ $email->subject }}</span>
                                                        <p class="card-text body text-xs text-slate-500 truncate ms-2 m-0">{{ $email->body_text }}</p>
                                                    </button>
                                                    <a id="delete_id" href="users/{{ $email->id }}/emails/delete"
                                                       class="card-title inline-flex h-7 w-7 items-center justify-center rounded text-danger-500 hover:bg-danger-50">
                                                        <x-ui.icon name="trash" size="sm" />
                                                    </a>
                                                </div>
                                            </h2>
                                            <div id="collapseOne" class="accordion-collapse collapse show border-t border-slate-200" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                                <div class="accordion-body p-3">
                                                    <p class="card-text body text-sm text-slate-700 m-0 whitespace-pre-line">{{ $email->body_text }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Reply form. Selectors preserved: #replyForm, #email_sent,
                                         #email_subject, #div_editor3, action /users/{id}/emails/reply. --}}
                                    <form id="replyForm" class="space-y-3 border-t border-slate-200 pt-4">
                                        <input type="hidden" name="email_sent"    value="{{ $email->to }}"      id="email_sent">
                                        <input type="hidden" name="subject"       value="{{ $email->subject }}" id="email_subject">
                                        <textarea name="body" id="div_editor3" rows="10"
                                                  class="form-control block w-full rounded border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600"></textarea>
                                        <button class="inline-flex w-full items-center justify-center gap-1.5 rounded bg-primary-600 px-4 h-10 text-sm font-medium text-white hover:bg-primary-700">Reply</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="lg:col-span-8 flex items-center justify-center rounded border border-dashed border-slate-300 bg-white p-12 text-sm text-slate-500">
                        Your inbox is empty right now.
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Compose modal — Bootstrap-5 data-bs-* preserved. #to, #subject, #div_editor2
         and the sendEmail2 onclick handler retained. --}}
    <div class="modal fade" id="compose" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded border border-slate-200 bg-white shadow-lg">
                <div class="modal-header border-b border-slate-200 px-5 py-3 flex items-center justify-between">
                    <h1 class="modal-title text-sm font-medium text-slate-700" id="staticBackdropLabel">New message</h1>
                    <button type="button" class="btn-close text-slate-400 hover:text-slate-600" data-bs-dismiss="modal" aria-label="Close">&times;</button>
                </div>
                <div class="modal-body px-5 py-4 space-y-3">
                    <input type="text" id="to" placeholder="To:" v-model="newEmail.to"
                           class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                    <input type="text" id="subject" placeholder="Subject" v-model="newEmail.subject"
                           class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                    <textarea id="div_editor2" rows="10"
                              class="form-control block w-full rounded border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600"></textarea>
                </div>
                <div class="modal-footer border-t border-slate-200 bg-slate-50 px-5 py-3 flex justify-end">
                    <button type="submit" @click="sendEmail2" onclick="sendEmail2('{{ $user->id }}')"
                            class="inline-flex items-center gap-1.5 rounded bg-primary-600 px-4 h-9 text-sm text-white hover:bg-primary-700">Send</button>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    function myfunction(email, mailtype) {
        $.ajax({
            type: "GET",
            url: `/getemailbyId/${email}/${mailtype}`,
            success: function (result) {
                $(".username").html(result.users);
                $(".body").html(result.email.body_text);
                $(".subject").html(result.email.subject);
                $("#email_subject").val(result.email.subject);
                $("#delete_id").attr("href", `/users/${result.email.id}/emails/delete`);
                if (mailtype === "inbox") {
                    $("#email_sent").val(result.email.from);
                } else {
                    $("#email_sent").val(result.email.to);
                }
            },
            error: function (result) { console.log(result); }
        });
    }

    function sendEmail2(userId) {
        var form_data = new FormData();
        var to = $('#to').val();
        var subject = $('#subject').val();
        var message = editor2.getHTMLCode();
        form_data.append("to", to);
        form_data.append("subject", subject);
        form_data.append("message", message);
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        $.ajax({
            type: "POST",
            url: `users/${userId}/emails/sending`,
            data: form_data,
            contentType: false,
            cache: false,
            processData: false,
            success: function (result) { location.reload(); },
            error: function (result) { console.log(result); }
        });
    }

    $('#replyForm').submit(function (e) {
        e.preventDefault();
        var form_data = new FormData(this);
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        $.ajax({
            type: "POST",
            url: 'users/{{ $user->id }}/emails/reply',
            data: form_data,
            contentType: false,
            cache: false,
            processData: false,
            success: function (result) { console.log("SUCCESS"); },
            error: function (result) { console.log(result); }
        });
    });
</script>
@endif
@endsection
