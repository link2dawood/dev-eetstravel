<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            {!!trans('main.Addusertochat')!!}
        </div>
        <div class="modal-body">
            <div class="box">
                <div class="box-boy">
                    <ul class="nav nav-stacked">
                        <input type="hidden" id="chat_id">
                        @if (count($users))
                            @foreach ($users as $user)
                                <li><a href="#" class="custom-chat-user-select" data-chat-id = "{{$chatId}}" data-user-id="{{$user->id}}">{{$user->name}}</a></li>
                            @endforeach
                        @else
                            {!!trans('main.NOUSERS')!!}
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>