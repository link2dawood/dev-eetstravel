@extends('scaffold-interface.layouts.app')
@section('title','Show')
@section('content')
    @include('layouts.title',
    ['title' => 'Chats', 'sub_title' => 'Chat',
    'breadcrumbs' => [
    ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
    ['title' => 'Chats', 'icon' => 'comments', 'route' => null]]])
    <script type="text/javascript" src="https://cdn.rawgit.com/samsonjs/strftime/master/strftime-min.js"></script>

    <section class="content">
        <div class="row">
            <div class="col-md-4">
                <div class="box box-widget">
                    <div class="bg-yellow chat-group-title">
                        <!-- /.widget-user-image -->
                        <h3 class="">{!!trans('main.Chats')!!}</h3>
                    </div>
                    <div class="box-footer no-padding">
                        <a class='add-direct-chat' data-toggle="modal"
                           data-target="#myModal"
                           data-link="/chat/renderUsersForChat"><button type="button"
                                                                        class="btn bg-olive btn-flat margin">{!!trans('main.AddUserChat')!!}</button></a>
                        <a class='add-direct-chat' data-toggle="modal"
                           data-target="#myModal"
                           data-link="/chat/renderCustomChatCreateFrom"><button type="button"
                                                                                class="btn bg-olive btn-flat margin">{!!trans('main.AddChat')!!}</button></a>
                                                                                
                                                                                
                        <a class='add-direct-chat' data-toggle="modal" id="deleteChatButton"
                           data-target="#myModal"
                           data-link="/chat/renderCustomChatDeleteFrom"><button type="button"
                                                                                class="btn bg-olive btn-flat margin">{!!trans('main.DeleteChat')!!}</button></a>
                                                                                
                                                                                
                        <ul class="nav nav-stacked chats-list">
                            <li>

                            </li>
                            @foreach($chats as $chat)
                                <li><a href="#" data-id="{{$chat->id}}" class="chat-title">
                                        {{$chat->title}}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-8 chat-container">
            </div>
        </div>
    </section>
@endsection

@section('post_scripts')
    <script type="text/javascript">
        $('#deleteChatButton').hide();
        var chat = {
            config: {
                chatContainer: $('body').find('.chat-container'),
                chatTitleContainer: $('body').find('.chat-title')
            },
            init: function () {
                chat.bind();
            },

            bind: function () {
                $('body').on('click', '.chat-title', function () {
                    $('body').find('.chat-title').parent().removeClass('active');
                    $(this).parent().addClass('active');
                    var chatId = $(this).attr('data-id');
                    $.ajax({
                        type: "GET",
                        url: "/chat/" + chatId + "/renderChat",
                        data: {},
                        success: function (result) {
                            chat.config.chatContainer.html(result);
                            $('.direct-chat-messages').scrollTo('max', {'duration': 500});
                        },
                        error: function (result) {
                            console.log(result);
                        }

                    });
                $('#deleteChatButton').show();    
                });
            }
        };

        $(document).ready(function(){
            chat.init()
        });

    </script>
    <script type="text/javascript">
        var chatUser = {
            config : {
                chatUserSelect : $('body').find('.chat-user-select'),
                chatsList : $('body').find('.chats-list'),
                customChatNameButton : $('body').find('#add_custom_chat')
            },
            init : function(){
                chatUser.bind();
            },
            bind : function (){
                $('body').on('click', '.chat-user-select', function(){
                    chatUser.selectUserChat($(this));
                });
                $('body').on('click', '.custom-chat-user-select', function(){
                    chatUser.addUserToChat($(this));
                });
                $('body').on('click', '#add_custom_chat', function(){
                    let name_chat = $('#custom_chat_name').val();
                    $('#errors_message').css({'display': 'none'});
                    $('#errors_message').html('');
                    if(name_chat === ''){
                        $('#errors_message').css({'display': 'block'});
                        $('#errors_message').html('Field required for filling');
                        return false;
                    }
                    chatUser.createCustomChat();
                });
                
                $('body').on('click', '#delete_custom_chat', function(){
                    var chatContainer = $('body').find('.active .chat-title');
                    chatId = chatContainer.data('id');
                    
                    $.ajax({
                        type: "GET",
                        url: "/chat/deleteChat",
                        data: {
                            'id': chatId,
                        },
                        success: function (result) {
                            $('#deleteChatButton').hide();
                            chatContainer.remove();
                            $('#myModal').modal('hide');
                        },
                        error : function (result) {
                            console.log(result);
                        }

                    });
                });
                
                $('body').on('keypress', '#custom_chat_name', function(e){
                    if(e.which == 13) {
                        chatUser.createCustomChat();
                    }
                });
                $('body').on('click', '.remove-user-from-chat', function(){
                        chatUser.removeUserFromChat($(this));
                });
            },
            addUserToChat : function (userContainer) {
                var chatId = userContainer.attr('data-chat-id');
                var userId = userContainer.attr('data-user-id');
                $.ajax({
                    type: "GET",
                    url: "/chat/addUserToCustomChat",
                    data: {
                        'userId'  : userId,
                        'chatId'  : chatId
                    },
                    success: function (result) {
                            var chatContainer = $('body').find('.active .chat-title');
                            chatUser.selectUserChat(chatContainer);
                        $('#myModal').modal('hide');

                    },
                    error : function (result) {
                        console.log('error', result);
                    }

                });
            },
            removeUserFromChat : function (userContainer) {
              var userId =    userContainer.attr('data-user-id');
              var chatId =    userContainer.attr('data-chat-id');
                $.ajax({
                    type: "GET",
                    url: "/chat/removeUserFromChat",
                    data: {
                        'userId'  : userId,
                        'chatId'  : chatId
                    },
                    success: function (result) {
                        userContainer.parent().parent().hide();
                        var chatContainer = $('body').find('.active .chat-title');
                        chatUser.selectUserChat(chatContainer);
                        $('#myModal').modal('hide');

                    },
                    error : function (result) {
                        console.log('error', result);
                    }

                });
            },
            selectUserChat : function (userContainer) {
                var userId = userContainer.attr('data-user-id');               
                $.ajax({
                    type: "GET",
                    url: "/chat/getOrCreateChat",
                    data: {
                        'userId'  : userId
                    },
                    success: function (result) {
       
                        if ($('.chat-title[data-id='+ result.id +']').length == 0) {
                            chatUser.config.chatsList.append(
                                "<li><a href=\"#\" data-id=\"" + result.id + "\" class=\"chat-title\">" + result.name + "</a></li>"
                            );
                        } else {
                            $('.chat-title[data-id='+ result.id +']').click();
                        }
                        $('#myModal').modal('hide');

                    },
                    error : function (result) {
                        console.log('error', result);
                    }

                });
            },
            createCustomChat : function () {
                var chatName = $('body').find('#custom_chat_name').val();
                var chatDescription = $('body').find('#custom_chat_description').val();
                $.ajax({
                    type: "GET",
                    url: "/chat/createCustomChat",
                    data: {
                        'chatName'  : chatName,
                        'chatDesription'  : chatDescription
                    },
                    success: function (result) {
                        chatUser.config.chatsList.append(
                            "<li><a href=\"#\" data-id=\""+result.id+"\" class=\"chat-title\">"+result.name+"</a></li>"
                        );
                        $('#myModal').modal('hide');

                    },
                    error : function (result) {
                        console.log('error', result);
                    }

                });
            }
        };
        chatUser.init();
    </script>


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
            // $('body').on('click', '.send-message', sendMessage($(this).attr('data-chat-id')));
            $('body').on('click', '.send-message', function(){
                var event = jQuery.Event("keypress");
                event.keyCode = 13;
                $(this).closest('div.input-group').find('input[name=message]').trigger(event);
                return false;
            });

            $('body').on('keypress', '.input-message', checkSend);
            scrollChatToBottom();
        }

        // Send on enter/return key
        function checkSend(e) {
            if (e.keyCode === 13) {
                sendMessage($(this).attr('data-chat-id'));
                // return false
            }
            // return false
        }

        // Handle the send button being clicked
        function sendMessage(chatId) {
            var messageText = $('.input-message[data-chat-id='+chatId+']').val();
            var userId = $('meta[name="user-id"]').attr('content');

            $('.input-message[data-chat-id='+chatId+']').val('');
//            if(messageText.length < 3) {
//                return false;
//            }
            // Build POST data and make AJAX request
            var data = {
                message: messageText,
                chat  : chatId,
                user : userId,
                '_token': $('input[name=_token]').val()
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
                },
                success: function (result) {
                    $('.direct-chat-messages[data-chat-id='+result.chat_id+']').append(result.data);
                    $('.direct-chat-messages').scrollTo('max', {'duration' : 500});
                    return false;
                },
                error : function (result) {
                    console.log(result);
                }

            });
        }

        // Build the UI for a new message and add to the DOM
        function addChat(data) {
            $.ajax({
                type: "GET",
                url: "/chat/" + data.chat + "/getNewChat",
                data: {
                    'userId' : data.user
                },
                success: function (result) {
                    $('.chats-list').append(result);
                },
                error : function (result) {
                    console.log(result);
                }

            });
        }

        init();

        /***********************************************/

        var pusher = new Pusher('dec55d4997ee67fe0e91', {
            cluster: 'eu',
            encrypted: true
        });

        var channel = pusher.subscribe('chat');
        channel.bind('new-message', addMessage);
        channel.bind('new-chat', addChat);
    </script>
@endsection
