{{-- #NewEmail compose modal — opened by openNewEmailModal() Vue method.
     v-model bindings + emailClassErrors :class + onFileChange / removeFile
     refs + sendEmail() / CloseNewEmailModal click handlers preserved. --}}
<div id="NewEmail" class="modal fade in" role="dialog" aria-labelledby="modalCreateLabel">
    <div class="modal-dialog modal-lg" style="width: 90%; max-width: 900px;">
        <div class="modal-content rounded border border-slate-200 bg-white shadow-lg">
            <div class="border-b border-slate-200 px-5 py-3">
                <h3 class="text-sm font-medium text-slate-700 m-0">Compose new message</h3>
            </div>

            <div class="px-5 py-4 space-y-3">
                <div class="form-group" :class="emailClassErrors.to">
                    <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">To</label>
                    <input type="text" v-model="newEmail.to" placeholder="recipient@example.com"
                           class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                </div>

                <div class="form-group" :class="emailClassErrors.subject">
                    <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Subject</label>
                    <input type="text" v-model="newEmail.subject" placeholder="Subject:"
                           class="form-control block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600">
                </div>

                <div class="form-group">
                    <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Message</label>
                    <ckeditor2 :id="'editor1'" style="height: 400px;"></ckeditor2>
                </div>

                <div class="form-group">
                    <label class="inline-flex cursor-pointer items-center gap-1.5 rounded border border-slate-300 bg-white px-3 h-9 text-sm text-slate-700 hover:bg-slate-50">
                        <x-ui.icon name="paperclip" size="sm" /> Attachments
                        <input type="file" ref="file" multiple @change="onFileChange" class="hidden">
                    </label>

                    <div v-for="(file, index) in newEmail.files" class="mt-2 flex items-center gap-2 rounded border border-slate-200 bg-slate-50 px-3 py-1.5 text-sm">
                        <span class="flex-1 truncate text-slate-700">@{{ file.name }}</span>
                        <button class="inline-flex h-7 w-7 items-center justify-center rounded text-danger-500 hover:bg-danger-50" @click="removeFile(index)">
                            <x-ui.icon name="trash" size="xs" />
                        </button>
                    </div>
                    <p class="mt-2 text-xs text-slate-500">Max. 32 MB</p>
                </div>
            </div>

            <div class="border-t border-slate-200 bg-slate-50 px-5 py-3 flex items-center justify-between gap-2">
                <button type="reset" data-dismiss="modal" @click="CloseNewEmailModal"
                        class="inline-flex items-center gap-1 rounded border border-slate-300 bg-white px-3 h-9 text-sm text-slate-700 hover:bg-slate-50">
                    <x-ui.icon name="x" size="sm" /> Discard
                </button>
                <button type="button" @click="sendEmail()"
                        class="inline-flex items-center gap-1.5 rounded bg-primary-600 px-4 h-9 text-sm text-white hover:bg-primary-700">
                    <x-ui.icon name="send" size="sm" /> Send
                </button>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>
<script src="{{ asset('assets/plugin/richtexteditor/rte.js') }}"></script>
<script src="{{ asset('assets/plugin/richtexteditor/plugins/all_plugins.js') }}"></script>

<script>
    var editor1 = new RichTextEditor("#div_editor1");
    var editor1 = new RichTextEditor("#div_editor2");
    var editor1 = new RichTextEditor("#div_editor3");
</script>

<script>
    $(function () {
        Vue.component('ckeditor2', {
            template: `<div class="ckeditor"><textarea :id="id" ></textarea></div>`,
            props: {
                value:        { type: String, default: '' },
                id:           { type: String, default: 'editor' },
                height:       { type: String, default: '325px' },
                toolbar: {
                    type: Array,
                    default: () => [
                        ['Undo', 'Redo'],
                        ['Bold', 'Italic', 'Strike'],
                        ['NumberedList', 'BulletedList'],
                        ['Cut', 'Copy', 'Paste'],
                    ]
                },
                language:     { type: String, default: 'en' },
                extraplugins: { type: String, default: '' }
            },
            beforeUpdate() {
                const ckeditorId = this.id
                if (this.value !== CKEDITOR.instances[ckeditorId].getData()) {
                    CKEDITOR.instances[ckeditorId].setData(this.value)
                    console.log('bu->', this.value)
                }
            },
            mounted() {
                const ckeditorId = this.id
                const ckeditorConfig = {
                    toolbar: this.toolbar,
                    language: this.language,
                    height: this.height,
                    extraPlugins: this.extraplugins
                }
                CKEDITOR.replace(ckeditorId, ckeditorConfig)
                CKEDITOR.instances[ckeditorId].setData(this.value)
                CKEDITOR.on('instanceReady', function () {
                    CKEDITOR.instances[ckeditorId].on('change', () => {
                        console.log('upd', this.value);
                        let ckeditorData = CKEDITOR.instances[ckeditorId].getData()
                        if (ckeditorData !== this.value) {
                            this.$emit('input', ckeditorData)
                        }
                    })
                });
            },
            destroyed() {
                const ckeditorId = this.id
                if (CKEDITOR.instances[ckeditorId]) {
                    CKEDITOR.instances[ckeditorId].destroy()
                }
            }
        });
    });

    // Legacy form submit handler (kept for parity with the old AdminLTE composer)
    $('form').submit(function (e) {
        alert("Check");
        e.preventDefault();
        $('#myProgress').show();
        $('#subject').attr('disabled', true);
        $('#message').attr('disabled', true);
        $('.Import').attr('disabled', true);
        var property = document.getElementById('fileUploader').files[0];
        var subject = $('#subject').val();
        var message = "check";
        var totalfiles = document.getElementById('attachfiles').files.length;
        var welcome_message = $('#welcome_message').val();
        var put_attributes = $('.put-attributes').val();
        $("#last_file").html(property);
        var form_data = new FormData();
        form_data.append("file", property);
        form_data.append("subject", subject);
        form_data.append("message", message);
        form_data.append("welcome_message", welcome_message);
        form_data.append("put_attributes", put_attributes);
        for (var index = 0; index < totalfiles; index++) {
            form_data.append("attachfiles[]", document.getElementById('attachfiles').files[index]);
        }
        $.ajax({
            url: 'functions.php',
            method: 'POST',
            data: form_data,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () { $('#msg').html('Loading......'); },
            success: function (data) {
                move();
                $('#success').modal('show');
                $('.countEmail').html(data + " Emails Sent Successfully");
                if (i == 0) {
                    i = 1;
                    var elem = document.getElementById("progress-bar");
                    var width = 1;
                    var id = setInterval(frame, 10);
                    function frame() {
                        if (width >= 100) { clearInterval(id); i = 0; }
                        else { width++; elem.style.width = width + "%"; }
                    }
                }
                $('.progress').hide();
                $('#msg').html(data);
                $('form').submit();
                window.location.href = "https://dev.eetstravel.com/email-composer";
            }
        });
    });

    function sendEmail() { alert("warning"); }
</script>
