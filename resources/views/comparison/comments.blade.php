
<div class="col-md-8 col-md-offset-2">
    <div class="box box-success" style="position: relative; left: 0px; top: 0px;">
        <div class="box-header ui-sortable-handle" style="cursor: move;">
            <i class="fa fa-comments-o"></i>
            <h3 class="box-title">{!!trans('main.Comments')!!}</h3>
        </div>
        <div class="box-body">
            <div class="slimScrollDiv" style="position: relative; overflow-y: scroll;  width: auto;">
                <div class="box-body box chat" id="chat-box" style="width: auto; height: auto;">
                    <div id="show_comments"></div>
                </div>
                <div class="slimScrollRail" style="width: 7px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; background: rgb(51, 51, 51) none repeat scroll 0% 0%; opacity: 0.2; z-index: 90; right: 1px;"></div>
            </div>
        </div>
        <!-- /.chat -->
        <div class="box-footer">
            <form method='POST' action='{{route('comment.store')}}' enctype="multipart/form-data" id="form_comment">
                {{csrf_field()}}
                <div class="input-group" style="width: 100%">
                                        <span id="author_name" class="input-group-addon">
                                            <span id="name"></span>
                                            <a href="#" id="reply_close"><i class="fa fa-close"></i></a>
                                        </span>
                    <textarea class="form-control" id="content" name="content" placeholder="Ctrl + Enter to post comment"></textarea>
                </div>

                <div class="form-group">
                    <label>{!!trans('main.Files')!!}</label>
                    @component('component.file_upload_field')@endcomponent
                </div>

                <input type="text" id="parent_comment" hidden name="parent" value="{{ null }}">
                <input type="text" id="default_reference_id" hidden name="reference_id" value="{{$id}}">
                <input type="text" id="default_reference_type" hidden name="reference_type" value="{{ \App\Comment::$services['comparison']}}">

                <button type="submit" class="btn btn-success pull-right" id="btn_send_comment" style="margin-top: 5px;">{!!trans('main.Send')!!}</button>
            </form>
        </div>
    </div>
</div>
<script src="{{ asset('js/comment.js') }}"></script>
<script>
    $(document).ready(function(){
        let targetForm = $('#attach').closest('form');
        let url = $(targetForm).attr('action');
        let modal_id = '#chat-box';

        $('#form_comment').on('submit', function(event){
            event.preventDefault();
            $('#attach').fileinput('upload');
        });

        $('#attach').fileinput({
            uploadUrl: url,
            uploadExtraData: () => {
                let obj = {};
                let sr  = [];
                obj._token = "{{csrf_token()}}";
                $(targetForm).find('input:text').each(function(){
                    if (!$(this).attr('name')) return;
                    obj[$(this).attr('name')] = $(this).val();
                });
                $(targetForm).find('input[type=number]').each(function(){
                    if (!$(this).attr('name')) return;
                    obj[$(this).attr('name')] = $(this).val();
                });
                $(targetForm).find('input:hidden').each(function(){
                    if (!$(this).attr('name')) return;
                    obj[$(this).attr('name')] = $(this).val();
                });
                $(targetForm).find('select').each(function(){
                    if (!$(this).attr('name')) return;
                    obj[$(this).attr('name')] = $(this).val();
                });
                $(targetForm).find('textarea').each(function(){
                    if (!$(this).attr('name')) return;
                    obj[$(this).attr('name')] = $(this).val();
                });
                $(targetForm).find('input:checkbox').each(function(){
                    if (!$(this).attr('name')) return;
                    if($(this).is(":checked")) {
                        sr.push($(this).val());
                        obj[$(this).attr('name')] = sr;
                    }
                });
                return obj;
            },
            showUpload: false,
            uploadAsync: false,
            elErrorContainer: false,
            fileActionSettings: {
                showUpload: false
            }
        })

        $('#attach').on('filebatchuploadsuccess', function(event, data, previewId, index){
            let res = data.response;
            if(res.comments){
                $('#chat-box').find('#show_comments').html(res.content);
                CKEDITOR.instances['content'].setData('');
                $(targetForm).find('#content').val('');
                $(targetForm).find('#attach').fileinput('clear');
                $(targetForm).find('#author_name').css({'display': 'none'});
                $(targetForm).find('#name').html('');
                $(targetForm).find('#parent_comment').attr('value', '');
                $(targetForm).find('#parent_comment').attr('value', $('#id_comment').val());
            }else{
                window.location.replace(res.route);
            }
        })
    });

</script>

