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
                    <button type="button" class="btn btn-primary" @click="createFolder" :disabled="creatingFolder">
                        <span v-if="creatingFolder" class="tms-spinner" aria-hidden="true"></span>
                        <i v-else class="fa fa-folder-plus" aria-hidden="true"></i> @{{ creatingFolder ? 'Adding...' : 'Add folder' }}
                    </button>
                </div>
                <button type="reset" class="btn btn-default"  data-dismiss="modal"><i class="fa fa-times"></i> Discard</button>
            </div>
            <!-- /.box-footer -->
        </div>
    </div>
</div>
