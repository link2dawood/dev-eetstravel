<script type="text/javascript" src="{{asset('js/strftime-min.js')}}"></script>
<script type="text/javascript" src="{{asset('js/pusher.min.js')}}"></script>
<div class="col-md-6 dashboard-widget-chat">
    @include('chats.component.chat', ['chat' => $main_chat])
</div>
@section('post_scripts')
<script type="text/javascript">
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
            });
        }
    };

    $(document).ready(chat.init());

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
                chatUser.createCustomChat();
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
                    chatUser.config.chatsList.append(
                        "<li><a href=\"#\" data-id=\""+result.id+"\" class=\"chat-title\">"+result.name+"</a></li>"
                    );
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
        $('body').on('click', '.send-message',function(){
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
            return false;
        }
    }

    // Handle the send button being clicked
    function sendMessage(chatId) {
        var userId = $('meta[name="user-id"]').attr('content');

        var messageText = $('.input-message[data-chat-id='+chatId+']').val();
        $('.input-message[data-chat-id='+chatId+']').val('');
//            if(messageText.length < 3) {
//                return false;
//            }
        // Build POST data and make AJAX request
        var data = {
            message: messageText,
            chat  : chatId,
            user : userId
        };
        $.post('/chat/message', data).success(function(){
            
            sendMessageSuccess();
            return false;
        });

        scrollChatToBottom();
        // Ensure the normal browser event doesn't take place
        return false;
    }

    // Handle the success callback
    function sendMessageSuccess() {
        $('.input-message').val('');
        return false;
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
</script>
    @endsection