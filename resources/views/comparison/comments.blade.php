{{--
    /comparison/{id}/comments — AJAX-loaded into the global #myModal on the
    Front Sheet page when a .comments-button is clicked. Chrome migrated
    to Tailwind; every JS hook preserved:
      - #form_comment, #content, #author_name, #name, #reply_close,
        #parent_comment, #default_reference_id, #default_reference_type,
        #btn_send_comment, #show_comments, #chat-box, #attach
      - file_upload_field component reused
      - Inline <script> that wires #attach.fileinput(...) and handles
        the AJAX upload response preserved verbatim.
--}}
<div class="rounded-md bg-white">
    <div class="border-b border-slate-200 px-5 py-3 flex items-center gap-2">
        <x-ui.icon name="message-circle" size="sm" class="text-slate-400" />
        <h3 class="text-sm font-semibold text-slate-900">{{ trans('main.Comments') }}</h3>
    </div>

    <div class="px-5 py-4">
        <div id="chat-box" class="max-h-[400px] overflow-y-auto">
            <div id="show_comments"></div>
        </div>
    </div>

    <div class="border-t border-slate-200 bg-slate-50 px-5 py-4 rounded-b-md">
        <form method="POST" action="{{ route('comment.store') }}" enctype="multipart/form-data" id="form_comment" class="space-y-3">
            @csrf
            <div>
                <span id="author_name" class="hidden mb-2 inline-flex items-center gap-2 rounded bg-primary-50 px-2 py-1 text-xs text-primary-700">
                    Replying to <span id="name" class="font-medium"></span>
                    <a href="#" id="reply_close" class="text-primary-700/70 hover:text-primary-900"><x-ui.icon name="x" size="xs" /></a>
                </span>
                <textarea class="form-control block w-full rounded border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600"
                          id="content" name="content" rows="3" placeholder="Ctrl + Enter to post comment"></textarea>
            </div>
            <div>
                <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">{{ trans('main.Files') }}</label>
                @component('component.file_upload_field')@endcomponent
            </div>
            <input type="hidden" id="parent_comment" name="parent" value="">
            <input type="hidden" id="default_reference_id" name="reference_id" value="{{ $id }}">
            <input type="hidden" id="default_reference_type" name="reference_type" value="{{ \App\Comment::$services['comparison'] }}">

            <div class="flex justify-end">
                <button type="submit" id="btn_send_comment" class="inline-flex h-9 items-center gap-2 rounded bg-primary-600 px-4 text-sm font-medium text-white hover:bg-primary-700">
                    <x-ui.icon name="send" size="sm" />
                    {{ trans('main.Send') }}
                </button>
            </div>
        </form>
    </div>
</div>

<script src="{{ asset('js/comment.js') }}"></script>
<script>
    $(document).ready(function () {
        let targetForm = $('#attach').closest('form');
        let url = $(targetForm).attr('action');
        let modal_id = '#chat-box';

        $('#form_comment').on('submit', function (event) {
            event.preventDefault();
            $('#attach').fileinput('upload');
        });

        $('#attach').fileinput({
            uploadUrl: url,
            uploadExtraData: () => {
                let obj = {};
                let sr  = [];
                obj._token = "{{ csrf_token() }}";
                $(targetForm).find('input:text').each(function () { if ($(this).attr('name')) obj[$(this).attr('name')] = $(this).val(); });
                $(targetForm).find('input[type=number]').each(function () { if ($(this).attr('name')) obj[$(this).attr('name')] = $(this).val(); });
                $(targetForm).find('input:hidden').each(function () { if ($(this).attr('name')) obj[$(this).attr('name')] = $(this).val(); });
                $(targetForm).find('select').each(function () { if ($(this).attr('name')) obj[$(this).attr('name')] = $(this).val(); });
                $(targetForm).find('textarea').each(function () { if ($(this).attr('name')) obj[$(this).attr('name')] = $(this).val(); });
                $(targetForm).find('input:checkbox').each(function () {
                    if (!$(this).attr('name')) return;
                    if ($(this).is(":checked")) { sr.push($(this).val()); obj[$(this).attr('name')] = sr; }
                });
                return obj;
            },
            showUpload: false,
            uploadAsync: false,
            elErrorContainer: false,
            fileActionSettings: { showUpload: false }
        });

        $('#attach').on('filebatchuploadsuccess', function (event, data) {
            let res = data.response;
            if (res.comments) {
                $('#chat-box').find('#show_comments').html(res.content);
                if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances['content']) {
                    CKEDITOR.instances['content'].setData('');
                }
                $(targetForm).find('#content').val('');
                $(targetForm).find('#attach').fileinput('clear');
                $(targetForm).find('#author_name').addClass('hidden');
                $(targetForm).find('#name').html('');
                $(targetForm).find('#parent_comment').val('');
                $(targetForm).find('#parent_comment').val($('#id_comment').val());
            } else {
                window.location.replace(res.route);
            }
        });
    });
</script>
