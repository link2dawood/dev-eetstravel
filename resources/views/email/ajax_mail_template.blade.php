{{-- AJAX-loaded single mail view. .reply / .delete data-link selectors
     are wired by surrounding code; keep them intact. --}}
@if($imapConnected)
    <div class="mailbox-controls border-b border-slate-200 px-5 py-3 flex items-center justify-end gap-2">
        <button type="button"
                class="reply inline-flex items-center gap-1 rounded border border-slate-300 bg-white px-3 h-9 text-sm text-slate-700 hover:bg-slate-50"
                data-reply-message="{{ $mail->message_id }}"
                data-reply-folder="{{ $currentFolder }}"
                data-to="@if($currentFolder == 'INBOX.Sent'){{ $mail->to }}@else{{ $mail->from }}@endif"
                data-link="{{ route('email.getComposeForm', ['id' => $mail->message_id, 'folder' => $currentFolder], false) }}">
            <x-ui.icon name="corner-up-left" size="sm" /> Reply
        </button>
        <a data-toggle="modal" data-target="#myModal"
           class="delete inline-flex items-center gap-1 rounded border border-danger-300 bg-white px-3 h-9 text-sm text-danger-600 hover:bg-danger-50 cursor-pointer"
           data-link="{{ route('email.deleteMsg', ['id' => $mail->message_id, 'folder' => $currentFolder], false) }}">
            <x-ui.icon name="trash" size="sm" /> Delete
        </a>
    </div>

    <div class="px-5 py-4">
        <div class="mailbox-read-info border-b border-slate-200 pb-3">
            <h3 class="text-base font-semibold text-slate-900 m-0">
                @if(\App\Helper\AdminHelper::emailCheck($mail))
                    {{ $mail->subject }}
                @endif
            </h3>
            <h5 class="mt-2 text-xs text-slate-500 flex items-center justify-between gap-2 m-0">
                <span>
                    From:
                    @if(\App\Helper\AdminHelper::emailCheck($mail))
                        <span class="font-medium text-slate-700">{{ $mail->from }}</span>
                    @endif
                </span>
                <span class="mailbox-read-time">
                    @if(\App\Helper\AdminHelper::emailCheck($mail))
                        {{ $mail->date }}
                    @endif
                </span>
            </h5>
        </div>

        <div class="mailbox-read-message prose prose-sm max-w-none mt-4 text-slate-800">
            @if($mail->body_html)
                {!! $mail->body_html !!}
            @else
                {!! $mail->body_text !!}
            @endif
        </div>
    </div>

    <div class="attach-block px-5 pb-4 mt-4">
        <div class="rounded border border-slate-200 bg-white relative">
            <div class="border-b border-slate-200 px-4 py-2 flex items-center gap-2">
                <x-ui.icon name="paperclip" size="sm" class="text-slate-400" />
                <h3 class="text-sm font-medium text-slate-700 m-0">Attachments</h3>
            </div>
            <ul class="mailbox-attachments list-none m-0 p-0"></ul>
            <div class="overlay absolute inset-0 flex items-center justify-center bg-white/70" id="overlay_attach">
                <x-ui.icon name="loader" size="md" class="text-primary-600 animate-spin" />
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $.ajax({
                method: "GET",
                url: "/email/attachmentList/{{ $currentFolder }}/{{ $mail->message_id }}",
                data: {}
            }).done(function (data) {
                $('.mailbox-attachments').html(data);
                $('#overlay_attach').remove();
            });
            $('.mailbox-read-message').find('style').remove();
        });
    </script>
@endif
