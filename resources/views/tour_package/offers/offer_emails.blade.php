@extends('scaffold-interface.layouts.tabler-app')
@section('title','Offer emails')

@section('content')
<x-ui.page-header
    title="Offer emails"
    :description="$tour_package->name ?? 'Email correspondence'"
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Tour package'],
        ['label' => 'Hotel offer', 'href' => route('offers', $tour_package->id)],
        ['label' => 'Emails'],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="javascript:history.back()" variant="ghost" icon="arrow-left">{!! trans('main.Back') !!}</x-ui.button>
    </x-slot>
</x-ui.page-header>

@php
    $renderEmail = function ($email, $supplierReply = true) {
        $dateString = $email->header->date ?? '';
        $dateTime   = new DateTime($dateString);
        return [
            'date'    => $dateTime->format('D m.Y'),
            'time'    => $dateTime->format('H:i:s'),
            'from'    => $email->header->from ?? '',
            'subject' => $email->header->subject ?? '',
            'html'    => $email->message->html ?? '',
            'mail'    => $supplierReply && !empty($email->header->details->sender[0])
                ? ($email->header->details->sender[0]->mailbox . '@' . $email->header->details->sender[0]->host)
                : null,
        ];
    };
@endphp

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

    {{-- Emails from supplier --}}
    <div class="rounded border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-3 flex items-center gap-2">
            <x-ui.icon name="inbox" size="sm" class="text-slate-400" />
            <h2 class="text-sm font-medium text-slate-700">Emails from supplier</h2>
        </div>
        <div class="p-5">
            @if(!empty($emails))
                <ul class="timeline relative space-y-4 list-none p-0 m-0">
                    @foreach($emails as $email)
                        @php $e = $renderEmail($email, true); @endphp
                        <li class="time-label">
                            <span class="inline-flex items-center rounded bg-danger-100 px-2 py-1 text-xs font-semibold text-danger-700">{{ $e['date'] }}</span>
                        </li>
                        <li class="rounded border border-slate-200 bg-slate-50">
                            <div class="flex items-center gap-2 px-4 py-2 border-b border-slate-200 bg-white">
                                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-info-100 text-info-700">
                                    <x-ui.icon name="mail" size="xs" />
                                </span>
                                <span class="time inline-flex items-center gap-1 text-xs text-slate-500">
                                    <x-ui.icon name="clock" size="xs" class="text-slate-400" /> {{ $e['time'] }}
                                </span>
                            </div>
                            <div class="timeline-item px-4 py-3">
                                <h3 class="timeline-header text-sm text-slate-700">
                                    <a href="#" class="text-primary-600 hover:text-primary-700">{{ $e['from'] }}</a>
                                    <span class="text-slate-500"> reply to your email</span>
                                    <b class="block sm:inline mt-0.5 text-slate-900">: {{ $e['subject'] }}</b>
                                </h3>
                                <div class="timeline-body mt-3 text-sm text-slate-700 prose prose-sm max-w-none">
                                    {!! $e['html'] !!}
                                </div>
                                <div class="timeline-footer mt-3">
                                    <button type="button" data-bs-toggle="modal" data-bs-target="#exampleModal"
                                            onclick="myfunction('{{ $e['mail'] }}','{{ addslashes($e['subject']) }}')"
                                            class="inline-flex items-center gap-1.5 rounded bg-primary-600 px-3 h-9 text-sm text-white hover:bg-primary-700">
                                        <x-ui.icon name="corner-up-left" size="sm" /> Reply to supplier
                                    </button>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="text-center text-sm text-slate-500 py-6">
                    <p>The supplier has not replied yet — contact them for further inquiry.</p>
                    <p class="mt-1">Or you may not have included a work email in the TMS dashboard yet.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Emails from TMS (outgoing) --}}
    <div class="rounded border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-3 flex items-center gap-2">
            <x-ui.icon name="send" size="sm" class="text-slate-400" />
            <h2 class="text-sm font-medium text-slate-700">Emails from TMS</h2>
        </div>
        <div class="p-5">
            @if(!empty($tms_emails))
                <ul class="timeline relative space-y-4 list-none p-0 m-0">
                    @foreach($tms_emails as $tmsemail)
                        @php $e = $renderEmail($tmsemail, false); @endphp
                        <li class="time-label">
                            <span class="inline-flex items-center rounded bg-danger-100 px-2 py-1 text-xs font-semibold text-danger-700">{{ $e['date'] }}</span>
                        </li>
                        <li class="rounded border border-slate-200 bg-slate-50">
                            <div class="flex items-center gap-2 px-4 py-2 border-b border-slate-200 bg-white">
                                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-info-100 text-info-700">
                                    <x-ui.icon name="mail" size="xs" />
                                </span>
                                <span class="time inline-flex items-center gap-1 text-xs text-slate-500">
                                    <x-ui.icon name="clock" size="xs" class="text-slate-400" /> {{ $e['time'] }}
                                </span>
                            </div>
                            <div class="timeline-item px-4 py-3">
                                <h3 class="timeline-header text-sm text-slate-700">
                                    <a href="#" class="text-primary-600 hover:text-primary-700">{{ $e['from'] }}</a>
                                    <span class="text-slate-500"> sent email to supplier</span>
                                    <b class="block sm:inline mt-0.5 text-slate-900">: {{ $e['subject'] }}</b>
                                </h3>
                                <div class="timeline-body mt-3 text-sm text-slate-700 prose prose-sm max-w-none">
                                    {!! $e['html'] !!}
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="text-center text-sm text-slate-500 py-6">
                    <p>No TMS messages on file yet.</p>
                    <p class="mt-1">Use the "Reply to supplier" button or the TMS email composer to start.</p>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Reply modal — Bootstrap data-bs-* selectors preserved. #email_sent,
     #email_subject, #package_id are populated by myfunction() (see below). --}}
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="replyForm">
            <div class="modal-content rounded border border-slate-200 bg-white shadow-lg">
                <div class="modal-header border-b border-slate-200 px-5 py-3 flex items-center justify-between">
                    <h5 class="modal-title text-sm font-medium text-slate-700" id="exampleModalLabel">New message</h5>
                    <button type="button" class="btn-close text-slate-400 hover:text-slate-600" data-bs-dismiss="modal" aria-label="Close">&times;</button>
                </div>
                <div class="modal-body px-5 py-4 space-y-3">
                    <input type="hidden" name="email_sent"    value="" id="email_sent">
                    <input type="hidden" name="email_subject" value="" id="email_subject">
                    <input type="hidden" name="package_id"    value="{{ $tour_package->id }}" id="package_id">
                    <div>
                        <label for="div_editor1" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Message</label>
                        <textarea name="body" id="div_editor1" rows="10"
                                  class="form-control block w-full rounded border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-t border-slate-200 bg-slate-50 px-5 py-3 flex items-center justify-end gap-2">
                    <button type="button" data-bs-dismiss="modal"
                            class="inline-flex items-center gap-1.5 rounded border border-slate-300 bg-white px-3 h-9 text-sm text-slate-700 hover:bg-slate-50">Close</button>
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 rounded bg-primary-600 px-4 h-9 text-sm text-white hover:bg-primary-700">Send message</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function myfunction(mail, subject) {
        $('#email_sent').val(mail);
        $('#email_subject').val(subject);
    }

    $('#replyForm').submit(function (e) {
        e.preventDefault();
        var form_data = new FormData(this);
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        $.ajax({
            type: "POST",
            url: '/templates/{{ $user->id }}/emails/reply',
            data: form_data,
            contentType: false,
            cache: false,
            processData: false,
            success: function (result) { location.reload(); },
            error: function (result) { console.log(result); }
        });
    });
</script>
@endsection
