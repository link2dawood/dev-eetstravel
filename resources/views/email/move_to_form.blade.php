<div class="modal-dialog" role="document">
    <form class="modal-content" action="{{route('email.moveEmail')}}" method="POST">
        {!! Form::token() !!}
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Choose folder</h3>
            </div>
            <!-- /.box-header -->
            <div class="box box-body" style="border-top: none">

                <div class="form-group">
                    <select name="folder" id="folder" class="form-control">
                        @foreach($folders as $folder)
                            <option value="{{$folder->name}}">{{$folder->name}}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="messageFolder" value="{{$messageFolder}}">
                    <input type="hidden" name="messageId" value="{{$messageId}}">
                </div>
                <script>
                    $(document).ready(function() {
                        $('#folder').select2();
                    });
                </script>

                <div class="box-footer">
                    <div class="pull-right">
                        <button type="submit" class="btn btn-primary pre-loader-func"><i class="fa fa-envelope-o"></i> Send</button>
                    </div>
                    <button type="reset" class="btn btn-default"  data-dismiss="modal"><i class="fa fa-times"></i> Discard</button>
                </div>
            </div>
            <!-- /.box-body -->
        </div>
    </form>
</div>