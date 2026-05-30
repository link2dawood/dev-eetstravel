{{-- Legacy compose form (non-Vue) — posted to email.send. The
     #compose-textarea, .emails_validate, .protect_submit, #file,
     #file_name selectors and the CKEditor init at the bottom are
     all read by surrounding code; preserve. --}}
<div class="modal-dialog modal-lg" role="document" style="width: 90%; max-width: 1000px;">
    <form class="modal-content rounded border border-slate-200 bg-white shadow-lg" action="{{ route('email.send') }}" enctype="multipart/form-data" method="POST">
        {!! Form::token() !!}

        <div class="border-b border-slate-200 px-5 py-3">
            <h3 class="text-sm font-medium text-slate-700 m-0">Compose new message</h3>
        </div>

        <div class="px-5 py-4 space-y-3">
            <div class="form-group">
                <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">To</label>
                <input type="text" name="to" placeholder="recipient@example.com"
                       value="@if($replyTo){{ $replyTo }}@endif"
                       class="emails_validate form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
            </div>

            <div class="form-group">
                <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Subject</label>
                <input type="text" name="subject" placeholder="Subject:"
                       value="{{ @$mail ? $mail->subject : '' }}"
                       class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
            </div>

            <div class="form-group">
                <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Message</label>
                <textarea name="content" id="compose-textarea" required style="height: 400px;"
                          class="form-control block w-full rounded border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">@if($replyMessage)
<blockquote>{!! $replyMessage !!}</blockquote>
@endif</textarea>
            </div>

            <div class="form-group">
                <label class="inline-flex cursor-pointer items-center gap-1.5 rounded border border-slate-300 bg-white px-3 h-9 text-sm text-slate-700 hover:bg-slate-50">
                    <x-ui.icon name="paperclip" size="sm" /> Attachment
                    <input type="file" name="attachment[]" multiple id="file" class="hidden">
                </label>
                <div id="file_name" class="mt-2 text-xs text-slate-500"></div>
                <script>
                    document.getElementById('file').onchange = function () {
                        $('#file_name').html('Selected files: <br/>');
                        $.each(this.files, function (i, file) { $('#file_name').append(file.name + ' <br/>'); });
                    };
                </script>
                <p class="mt-1 text-xs text-slate-500">Max. 32 MB</p>
            </div>
        </div>

        <div class="border-t border-slate-200 bg-slate-50 px-5 py-3 flex items-center justify-between gap-2">
            <button type="reset" class="modal-close inline-flex items-center gap-1 rounded border border-slate-300 bg-white px-3 h-9 text-sm text-slate-700 hover:bg-slate-50" data-dismiss="modal">
                <x-ui.icon name="x" size="sm" /> Discard
            </button>
            <button type="button" class="protect_submit inline-flex items-center gap-1.5 rounded bg-primary-600 px-4 h-9 text-sm text-white hover:bg-primary-700">
                <x-ui.icon name="send" size="sm" /> Send
            </button>
        </div>
    </form>
</div>

<script>
    if ($('#compose-textarea').length > 0) {
        CKEDITOR.replace('compose-textarea', { height: '400px' });
    }
</script>
<script type="text/javascript" src="{{ asset('js/utils.js') }}"></script>
