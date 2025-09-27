<div class="modal-dialog">
    <div class="modal-content">
<div class="box box-primary">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span></button>
        <h4 class="modal-title">{{trans('main.Addcolumntoquotation')}}</h4>
    </div>
    <div class="box-body">

            <div class="form-group">
                <div class="form-group">
                    <label>{{trans('main.Columnname')}}</label>
                    <input type="text" class="form-control" name="name" id="quotation_column_name">
                </div>
                <div class="form-group">
                <label>{{trans('main.Selecttypeofcolumn')}}</label>
                <select class="form-control" id="quotation_column_type">
                    <option value="person">{{trans('main.Perperson')}}</option>
                    <option value="all">{{trans('main.All')}}</option>
                </select>
                </div>
            </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">{{trans('main.Close')}}</button>
            <button type="button" class="btn btn-primary" id="add_quotation_column">{{trans('main.Savechanges')}}</button>
        </div>

    </div>
</div>
</div>
</div>