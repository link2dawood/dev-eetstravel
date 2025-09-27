<div id="modalAddFolder" class="modal fade in" role="dialog" aria-labelledby="modalCreateLabel">
    <div class="modal-dialog" role="document">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Add folder</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">

                <div class="form-group">
                    <input type="text" v-model="newFolder" class="form-control" placeholder="Folder Name">
                </div>
            </div>
            <!-- /.box-body -->
            <div class="box-footer">
                <div class="pull-right">
                    <button type="submit" class="btn btn-primary" @click="createFolder"><i class="fa fa-envelope-o"></i> Add folder</button>
                </div>
                <button type="reset" class="btn btn-default"  data-dismiss="modal"><i class="fa fa-times"></i> Discard</button>
            </div>
            <!-- /.box-footer -->
        </div>
    </div>
</div>
