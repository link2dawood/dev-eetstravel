<div class="box box-warning direct-chat direct-chat-warning">
    <div class="box-header with-border">
        @if($chat->type == \App\Chat::CHAT_TYPE_DIRECT && $dashboard)
            <a href="#" id="return-contact"  class=" btn btn-success btn-sm">
                <i class="fa fa-fw fa-long-arrow-left"></i>
            </a>

        @endif
        <h3 class="box-title" style="    vertical-align: middle;">{{$chat->title}}</h3>
        <div class="box-tools pull-right">
            @if ($chat->type == \App\Chat::CHAT_TYPE_CUSTOM)
            <a class='add-direct-chat' id="add_user_to_chat"
               data-toggle="modal"
               data-target="#myModal"
               data-link="/chat/{{$chat->id}}/renderUsersForCustomChat">
            <button type="button" class="btn btn-box-tool">
                <i class="fa fa-plus"></i>
            </button>
            </a>
            @endif
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            {{--<button type="button" class="btn btn-box-tool" data-toggle="tooltip" title="" data-widget="chat-pane-toggle" data-original-title="Contacts">
                <i class="fa fa-comments"></i></button>--}}
                @if ($chat->type == \App\Chat::CHAT_TYPE_CUSTOM)
                <a  data-toggle="modal" data-target="#myModal" class=" btn btn-box-tool delete" data-link="{{route('chat.deleteMsg', ['id' => $chat->id], false)}}">
                    <i class="fa fa-trash-o" aria-hidden="true"></i>
                </a>
                @endif
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
        </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body" style="min-height: 300px">
        <!-- Conversations are loaded here -->
        <div class="direct-chat-messages" data-chat-id="{{$chat->id}}">

            @foreach($chat->messages as $message)
                @include('chats.component.message')
            @endforeach
        </div>
        <div class="direct-chat-contacts">
            <ul class="contacts-list">
                @foreach($chat->users as $user)
                <li>
                    <a href="#">
                        <img class="contacts-list-img" src="{{$user->avatar_file_name == null ? asset('img/avatar.png') : $user->avatar->url('logo')}}">

                        <div class="contacts-list-info">
                            <span class="contacts-list-name">
                              {{$user->name}}
                            </span>
                        </div>
                        @if ($chat->type == \App\Chat::CHAT_TYPE_CUSTOM)
                        <div class="pull-right">
                            <a href="#" class="remove-user-from-chat" data-chat-id="{{$chat->id}}" data-user-id="{{$user->id}}">
                                <i class="fa fa-times fa-inverse" aria-hidden="true"></i>
                            </a>
                        </div>
                        @endif
                        <!-- /.contacts-list-info -->
                    </a>
                </li>
                @endforeach
                <!-- End Contact Item -->
            </ul>
            <!-- /.contatcts-list -->
        </div>
        <!-- /.box-body -->

    </div>
    <div class="box-footer">
        <form action="#" method="post">
            {{csrf_field()}}
            <div class="input-group">
                <input type="text" name="message" placeholder="Type Message ..." class="form-control input-message" data-chat-id="{{$chat->id}}">
                <span class="input-group-btn">
                        <button type="submit" class="btn btn-success btn-flat send-message" data-chat-id="{{$chat->id}}">{!!trans('main.Send')!!}</button>

                      </span>
            </div>
        </form>
    </div>
    <!-- /.box-footer-->
</div>
