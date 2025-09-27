{{--Popup create--}}
<div id="modalCreate" class="modal fade in" role="dialog" aria-labelledby="modalCreateLabel">
    <div class="modal-dialog" role="document">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Choose folder</h3>
            </div>
            <!-- /.box-header -->
            <div class="box box-body" style="border-top: none">
                <div class="form-group" >
                    <select  name="folder" class="form-control">
                        <option v-for="(folder, index) in folders.INBOX">11</option>

                    </select>

                </div>

                <div class="box-footer">
                    <div class="pull-right">
                        <button type="submit" class="btn btn-primary pre-loader-func"><i class="fa fa-envelope-o"></i> Send</button>
                    </div>
                    <button type="reset" class="btn btn-default"  data-dismiss="modal"><i class="fa fa-times"></i> Discard</button>
                </div>
            </div>
            <!-- /.box-body -->
        </div>
    </div>
</div>
{{--end Popup create--}}