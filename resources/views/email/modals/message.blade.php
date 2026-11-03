{{--<script src="{{ asset('js/vue/ckeditor.js') }}"></script>--}}

<div id="NewEmail" class="modal fade in" role="dialog"  aria-labelledby="modalCreateLabel" style="width: 100%; margin: 0 auto;">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Compose New Message </h3>
        </div>
        <!-- /.box-header -->
        <div class="box box-body" style="border-top: none">
            <div class="form-group" :class="emailClassErrors.to">
                <input type="text" v-model="newEmail.to" class="form-control "  placeholder="To:">
            </div>
            <div class="form-group" :class="emailClassErrors.subject">
                <input type="text" v-model="newEmail.subject" class="form-control" placeholder="Subject:">
            </div>
            <div class="form-group">
                <ckeditor2   :id="'editor1'" style="height: 400px; "></ckeditor2>
				 {{-- <textarea name="" id="div_editor1" cols="30" rows="10"  style="height: 400px; "></textarea>--}}
            </div>
			
            <div class="form-group">
                <div class="btn btn-default btn-file">
                    <i class="fa fa-paperclip"></i> Attachments
                    <input type="file"  ref="file" multiple @change="onFileChange">
                </div>

                <div v-for="(file, index) in newEmail.files" style="margin: 5px">
                    <span>@{{ file.name }}</span>
                    <button type="button" class="btn btn-sm btn-danger dash-act dash-act-delete" @click="removeFile(index)" title="Remove"><i class="ti ti-trash"></i></button>

                </div>
                <p class="help-block">Max. 32MB</p>
            </div>

            <div class="box-footer">
                <div class="pull-right">
                    <!--<button type="button" class="btn btn-primary " @click="sendEmail()"><i class="fa fa-envelope-o"></i> Send</button>-->
					<button type="button" class="btn btn-primary" @click="sendEmail()" :disabled="sendingEmail">
                        <span v-if="sendingEmail" class="tms-spinner" aria-hidden="true"></span>
                        <i v-else class="fa fa-envelope-o" aria-hidden="true"></i> @{{ sendingEmail ? 'Sending...' : 'Send' }}
                    </button>
                </div>
                <button type="reset" class="btn btn-default" data-dismiss="modal" @click="CloseNewEmailModal" :disabled="sendingEmail"><i class="fa fa-times"></i> Discard</button>
            </div>
            <div v-if="sendingEmail" class="mt-2" role="status" aria-live="polite">
                <div v-if="uploadProgress !== null" class="progress mb-1" role="progressbar" :aria-valuenow="uploadProgress" aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar" :style="{ width: uploadProgress + '%' }"></div>
                </div>
                <small class="text-muted">
                    @{{ uploadProgress === null ? 'Preparing message...' : uploadProgress < 100 ? 'Uploading message... ' + uploadProgress + '%' : 'Upload complete; waiting for mail server...' }}
                </small>
            </div>
            <!-- /.box-footer -->
        </div>
        <!-- /.box-body -->
    </div>
</div>
{{-- The layout already loads jQuery: a second copy here replaced it and dropped its plugins (select2, datepicker...) --}}
    <script src="{{asset('assets/plugin/richtexteditor/rte.js')}}"></script>
    <script src="{{asset('assets/plugin/richtexteditor/plugins/all_plugins.js')}}"></script>

    <script>
        ['#div_editor1', '#div_editor2', '#div_editor3'].forEach(function (selector) {
            if (document.querySelector(selector)) {
                new RichTextEditor(selector);
            }
        });
    </script>
<script>
    $(function () {
        Vue.component('ckeditor2', {
            template: `<div class="ckeditor"><textarea :id="id" ></textarea></div>`,
            props: {
                value: {
                    type: String,
                    default:''
                },
                id: {
                    type: String,
                    default: 'editor'
                },
                height: {
                    type: String,
                    default: '325px',
                },
                toolbar: {
                    type: Array,
                    default: () => [
                        ['Undo', 'Redo'],
                        ['Bold', 'Italic', 'Strike'],
                        ['NumberedList', 'BulletedList'],
                        ['Cut', 'Copy', 'Paste'],
                    ]
                },
                language: {
                    type: String,
                    default: 'en'
                },
                extraplugins: {
                    type: String,
                    default: ''
                }
            },
            beforeUpdate() {
                const ckeditorId = this.id
                if (this.value !== CKEDITOR.instances[ckeditorId].getData()) {
                    CKEDITOR.instances[ckeditorId].setData(this.value)
                    console.log('bu->',this.value)
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
                CKEDITOR.on('instanceReady', function(){
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
</script>
