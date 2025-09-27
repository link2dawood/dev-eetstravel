<div class="modal-dialog" role="document">
    <form class="modal-content" action="{{route('email.addFolder')}}" method="POST">
        {!! Form::token() !!}
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Add folder</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">

                <div class="form-group">
                    <input type="text" name="folderName" class="form-control" placeholder="Folder Name">
                </div>
            </div>
            <!-- /.box-body -->
            <div class="box-footer">
                <div class="pull-right">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-envelope-o"></i> Add folder</button>
                </div>
                <button type="reset" class="btn btn-default"  data-dismiss="modal"><i class="fa fa-times"></i> Discard</button>
            </div>
            <!-- /.box-footer -->
        </div>
    </form>
</div>