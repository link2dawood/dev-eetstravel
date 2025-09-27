<div class="modal-dialog modal-lg" role="document" style="width: 90%">
    <form class="modal-content" action="{{route('email.send')}}" enctype="multipart/form-data" method="POST">
        {!! Form::token() !!}

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Compose New Message</h3>
            </div>
            <!-- /.box-header -->
            <div class="box box-body" style="border-top: none">
                <div class="form-group">
                    <input type="text" name="to" class="form-control emails_validate" placeholder="To:" value="@if($replyTo) {{$replyTo}} @endif">
                </div>
                <div class="form-group">
                    <input type="text" name="subject" class="form-control" value="{{ @$mail ? $mail->subject : '' }}" placeholder="Subject:">
                </div>
                <div class="form-group">
                    <textarea name="content" id="compose-textarea" class="form-control" style="height: 400px; " required>
                        @if($replyMessage)
                            <blockquote>
                            {!! $replyMessage !!}
                            </blockquote>
                        @endif
                    </textarea>

                </div>
                <div class="form-group">
                    <div class="btn btn-default btn-file">
                        <i class="fa fa-paperclip"></i> Attachment
                        <input type="file" name="attachment[]" multiple id="file">
                    </div>
                    <div id="file_name"></div>
                    <script>
                        document.getElementById('file').onchange = function () {
                            $('#file_name').html('Selected files: <br/>');
                            $.each(this.files, function(i, file){
                                $('#file_name').append(file.name + ' <br/>');
                            });
                        };
                    </script>
                    <p class="help-block">Max. 32MB</p>
                </div>

                <div class="box-footer">
                    <div class="pull-right">
                        <button type="button" class="btn btn-primary protect_submit"><i class="fa fa-envelope-o"></i> Send</button>
                    </div>
                    <button type="reset" class="btn btn-default modal-close" data-dismiss="modal"><i class="fa fa-times"></i> Discard</button>
                </div>
                <!-- /.box-footer -->
            </div>
            <!-- /.box-body -->
        </div>
    </form>
</div>
<script>
    if ($('#compose-textarea').length > 0) {
        CKEDITOR.replace('compose-textarea', {
            height: '400px'
        });
    }
</script>
<script type="text/javascript" src="{{asset('js/utils.js')}}"></script>