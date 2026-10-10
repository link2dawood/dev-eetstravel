@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Offer Emails')
@section('content')
    @include('layouts.title', [
        'title' => 'Offer Emails',
        'sub_title' => $tour_package->name,
        'breadcrumbs' => [
            ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
            ['title' => 'Offers', 'icon' => 'suitcase', 'route' => route('offers', $tour_package->id)],
            ['title' => 'Emails', 'route' => null],
        ],
    ])
    <style>
        .offer-mails .mail-item + .mail-item { border-top: 1px solid var(--tblr-border-color, #e6e7e9); }
        .offer-mails .mail-body { overflow-x: auto; max-width: 100%; }
        .offer-mails .mail-body img { max-width: 100%; height: auto; }
    </style>

    <div class="offer-mails">
        <div class="mb-3">
            <a href="{{ route('offers', $tour_package->id) }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-1"></i>{!! trans('main.Back') !!}
            </a>
        </div>

        @foreach ([
            ['title' => 'Emails from supplier', 'items' => $emails, 'reply' => true],
            ['title' => 'Emails from TMS', 'items' => $tms_emails, 'reply' => false],
        ] as $section)
            <div class="card mb-3">
                <div class="card-header"><h3 class="card-title">{{ $section['title'] }}</h3></div>
                @if (!empty($section['items']))
                    <div class="list-group list-group-flush">
                        @foreach ($section['items'] as $email)
                            @php
                                $sentAt = !empty($email->header->date) ? new DateTime($email->header->date) : null;
                                $sender = $email->header->details->sender[0] ?? null;
                                $replyTo = $sender ? $sender->mailbox . '@' . $sender->host : '';
                            @endphp
                            <div class="list-group-item mail-item">
                                <div class="d-flex flex-wrap justify-content-between gap-2 mb-2">
                                    <div>
                                        <strong>{{ $email->header->from ?? '' }}</strong>
                                        <div class="text-muted small">{{ $email->header->subject ?? '' }}</div>
                                    </div>
                                    <div class="text-muted small text-nowrap">
                                        <i class="ti ti-clock me-1"></i>{{ $sentAt ? $sentAt->format('D d.m.Y H:i') : '' }}
                                    </div>
                                </div>
                                <div class="mail-body">{!! $email->message->html ?? '' !!}</div>
                                @if ($section['reply'] && $replyTo)
                                    <button type="button" class="btn btn-primary btn-sm mt-2 reply-btn" data-bs-toggle="modal"
                                        data-bs-target="#replyModal" data-mail="{{ $replyTo }}" data-subject="{{ $email->header->subject ?? '' }}">
                                        <i class="ti ti-arrow-back-up me-1"></i>Reply to supplier
                                    </button>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="card-body text-muted">
                        No emails yet. Either the supplier has not replied, or the supplier has no work email in TMS.
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <div class="modal fade" id="replyModal" tabindex="-1" aria-labelledby="replyModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="replyForm" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="replyModalLabel">Reply to supplier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="email_sent" id="email_sent">
                    <input type="hidden" name="email_subject" id="email_subject">
                    <input type="hidden" name="package_id" value="{{ $tour_package->id }}">
                    <div class="text-muted small mb-2" id="reply_to_label"></div>
                    <label for="reply_body" class="form-label">Message</label>
                    <textarea name="body" id="reply_body" rows="8" class="form-control" required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="replySendBtn">Send message</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
(function () {
    var toast = function (message, type) {
        if (typeof window.appToast === 'function') window.appToast(message, type);
    };

    document.querySelectorAll('.reply-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            document.getElementById('email_sent').value = button.dataset.mail;
            document.getElementById('email_subject').value = button.dataset.subject;
            document.getElementById('reply_to_label').textContent = 'To: ' + button.dataset.mail;
        });
    });

    $('#replyForm').on('submit', function (e) {
        e.preventDefault();
        var sendBtn = document.getElementById('replySendBtn');
        if (sendBtn.disabled) return;
        sendBtn.disabled = true;
        sendBtn.textContent = 'Sending...';
        $.ajax({
            type: 'POST',
            url: @json(url('templates/' . $user->id . '/emails/reply')),
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            success: function () {
                try {
                    sessionStorage.setItem('appToastAfterReload', JSON.stringify({ message: 'Reply sent to the supplier.', type: 'success' }));
                } catch (err) {}
                location.reload();
            },
            error: function () {
                toast('The reply could not be sent. Please try again.', 'error');
                sendBtn.disabled = false;
                sendBtn.textContent = 'Send message';
            }
        });
    });
})();
</script>
@endpush
