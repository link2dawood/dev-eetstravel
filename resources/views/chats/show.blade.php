@extends('scaffold-interface.layouts.app')
@section('title','Show')
@section('content')
    @include('layouts.title', [
                    'title' => 'Chat',
                     'sub_title' => 'Chat (#'.$chat->id.')',
                      'breadcrumbs_chain' => 'Show',
                       'icon' => 'chat'])
    <section class="content">
        <div class="row">
            <div class="col-md-4">
                <div class="box box-widget">
                    <div class="bg-yellow chat-group-title">
                        <!-- /.widget-user-image -->
                        <h3 class="">{!!trans('main.Chats')!!}</h3>
                    </div>
                    <div class="box-footer no-padding">
                        <ul class="nav nav-stacked">
                            <li><a href="#">{!!trans('main.Projects')!!} <span class="pull-right badge bg-blue">31</span></a></li>
                            <li><a href="#">{!!trans('main.Tasks')!!} <span class="pull-right badge bg-aqua">5</span></a></li>
                            <li><a href="#">{!!trans('main.CompletedProjects')!!} <span class="pull-right badge bg-green">12</span></a></li>
                            <li><a href="#">{!!trans('main.Followers')!!} <span class="pull-right badge bg-red">842</span></a></li>
                        </ul>
                    </div>
                </div>
                <div class="box box-widget">
                    <div class="bg-green chat-group-title">
                        <!-- /.widget-user-image -->
                        <h3 class="">{!!trans('main.DirectChats')!!}</h3>
                    </div>
                    <div class="box-footer no-padding">
                        <ul class="nav nav-stacked">
                            <li><a href="#">Nicola Smith <span class="pull-right badge bg-blue">31</span></a></li>
                            <li><a href="#">Andre White <span class="pull-right badge bg-blue">31</span></a></li>
                            <li><span class="pull-right"> <button type="button" class="btn bg-olive btn-flat margin">{!!trans('main.AddUserChat')!!}</button></span></li>

                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="box box-warning direct-chat direct-chat-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{$chat->title}}</h3>
                            {{$chat->description}}
                        <div class="box-tools pull-right">
                            <span data-toggle="tooltip" title="" class="badge bg-green" data-original-title="3 New Messages">3</span>
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-box-tool" data-toggle="tooltip" title="" data-widget="chat-pane-toggle" data-original-title="Contacts">
                                <i class="fa fa-comments"></i></button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <!-- Conversations are loaded here -->
                        <div class="direct-chat-messages">

                            @foreach($chat->messages as $message)
                               @include('chats.component.message')
                            @endforeach
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <form action="#" method="post">
                            {{csrf_field()}}
                            <div class="input-group">
                                <input type="text" name="message" placeholder="Type Message ..." class="form-control input-message">
                                <span class="input-group-btn">
                        <button type="submit" class="btn btn-success btn-flat send-message">{!!trans('main.Send')!!}</button>
                      </span>
                            </div>
                        </form>
                    </div>
                    <!-- /.box-footer-->
                </div>
            </div>
        </div>
        </div>
    </section>
@endsection

@section('post_scripts')
    <script type="text/javascript" src="https://cdn.rawgit.com/samsonjs/strftime/master/strftime-min.js"></script>
    <script type="text/javascript" src="{{asset('js/pusher.min.js')}}"></script>
    <script>
        // Ensure CSRF token is sent with AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            }
        });

        // Added Pusher logging
//        Pusher.log = function(msg) {
//            console.log(msg);
//        };
    </script>
    <script type="text/javascript">
        function init() {
            // send button click handling
            // $('.send-message').click(sendMessage);
            $('.send-message').click(function(){
              var event = jQuery.Event("keypress");
                event.keyCode = 13;
                $(this).closest('div.input-group').find('input[name=message]').trigger(event);  
            });
             
            $('.input-message').keypress(checkSend);
            scrollChatToBottom();
        }

        // Send on enter/return key
        function checkSend(e) {
            if (e.keyCode === 13) {
                return sendMessage();
            }
        }

        // Handle the send button being clicked
        function sendMessage() {
            var messageText = $('.input-message').val();
            var userId = $('meta[name="user-id"]').attr('content');
            $('.input-message').val('');
//            if(messageText.length < 3) {
//                return false;
//            }

            // Build POST data and make AJAX request
            var data = {
                message: messageText,
                chat  : {{$chat->id}},
                user: userId
            };
            $.post('/chat/message', data).success(sendMessageSuccess);
            scrollChatToBottom();
            // Ensure the normal browser event doesn't take place
            return false;
        }

        // Handle the success callback
        function sendMessageSuccess() {
            $('.input-message').val('');
        }

        function scrollChatToBottom()
        {
            $('.direct-chat-messages').scrollTo('max', {'duration' : 500});
        }

        // Build the UI for a new message and add to the DOM
        function addMessage(data) {
            $.ajax({
                type: "GET",
                url: "/chat/" + data.message + "/getMessage",
                data: {
                    'chat'  : {{$chat->id}}
                },
                success: function (result) {
                    $('.direct-chat-messages').append(result);
                    $('.direct-chat-messages').scrollTo('max', {'duration' : 500});
                },
                error : function (result) {
                    console.log(result);
                }

            });
        }

        $(init);

        /***********************************************/

        var pusher = new Pusher('dec55d4997ee67fe0e91', {
            cluster: 'eu',
            encrypted: true
        });

        var channel = pusher.subscribe('chat');
        channel.bind('new-message', addMessage);
    </script>
@endsection