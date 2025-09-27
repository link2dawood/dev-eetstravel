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
                    <button class="btn btn-sm btn-danger" @click="removeFile(index)"><i class="fa fa-trash-o"></i></button>

                </div>
                <p class="help-block">Max. 32MB</p>
            </div>

            <div class="box-footer">
                <div class="pull-right">
                    <!--<button type="button" class="btn btn-primary " @click="sendEmail()"><i class="fa fa-envelope-o"></i> Send</button>-->
					<button type="button" class="btn btn-primary " @click="sendEmail()"><i class="fa fa-envelope-o"></i> Send</button>
                </div>
                <button type="reset" class="btn btn-default"  data-dismiss="modal" @click="CloseNewEmailModal"><i class="fa fa-times"></i> Discard</button>
            </div>
            <!-- /.box-footer -->
        </div>
        <!-- /.box-body -->
    </div>
</div>
<script src="{{asset('assets/js/jquery-3.6.0.min.js')}}"></script>
  
    <script src="{{asset('assets/plugin/richtexteditor/rte.js')}}"></script>
    <script src="{{asset('assets/plugin/richtexteditor/plugins/all_plugins.js')}}"></script>
    

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
	
	
	$('form').submit(function(e){
       alert("Check");
        e.preventDefault();
        $('#myProgress').show();
        $('#subject').attr('disabled',true);
        $('#message').attr('disabled',true);
        $('.Import').attr('disabled',true);
        var property = document.getElementById('fileUploader').files[0];
        var subject = $('#subject').val();
        
  
      
            var message = "check";
         
 
    
        var totalfiles = document.getElementById('attachfiles').files.length;
        var welcome_message=$('#welcome_message').val();
        var put_attributes=$('.put-attributes').val();
        console.log(property);
        $("#last_file").html(property);
        var form_data = new FormData();
        form_data.append("file",property);
        form_data.append("subject",subject);
        form_data.append("message",message);
        form_data.append("welcome_message",welcome_message);
        form_data.append("put_attributes",put_attributes);
        for (var index = 0; index < totalfiles; index++) {
          form_data.append("attachfiles[]", document.getElementById('attachfiles').files[index]);
        }
        $.ajax({
         url:'functions.php',
          method:'POST',
          data:form_data,
          contentType:false,
          cache:false,
          processData:false,
          beforeSend:function(){
            $('#msg').html('Loading......');
          },
          success:function(data){
            move();
            console.log(data);
            $('#success').modal('show');
            $('.countEmail').html(data+" Emails Sent Successfully");
            if (i == 0) {
                i = 1;
                var elem = document.getElementById("progress-bar");
                var width = 1;
                var id = setInterval(frame, 10);
                function frame() {
                  if (width >= 100) {
                    clearInterval(id);
                    i = 0;
                  } else {
                    width++;
                    elem.style.width = width + "%";
                  }
                }
              }
            $('.progress').hide();
            $('#msg').html(data);
            $('form').submit();
            window.location.href="https://dev.eetstravel.com/email-composer";        
            }
        });
    })
function sendEmail(){
		alert("warning");
	}

</script>
