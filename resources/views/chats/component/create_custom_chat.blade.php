<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            {!!trans('main.CreateChat')!!}
        </div>
        <div class="modal-body">
            <div class="alert alert-danger" id="errors_message" style="text-align: center; display: none">
            </div>
            <div class="form-group">
                <label for="custom_chat_name">{!!trans('main.Name')!!}:</label>
                <input type="text" class="form-control" id="custom_chat_name">
            </div>
            {{-- <div class="form-group">
                <label for="custom_chat_description">{!!trans('main.Description')!!}:</label>
                <textarea class="form-control" id="custom_chat_description"></textarea>
            </div> --}}
            <div class="row">
                <button type="button" class="btn bg-olive btn-flat margin pull-right" id="add_custom_chat">{!!trans('main.AddChat')!!}</button>
            </div>
        </div>
    </div>
</div>
