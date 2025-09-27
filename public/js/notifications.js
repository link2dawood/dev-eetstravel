let notifications = {
    init : () => {
        notifications.config = {};
        notifications.bindEvents();
        notifications.generateNotificationsTasks();
    },
    bindEvents: () => {
        $(document).on('click', '.delete-notification-task', function () {
            notifications.deleteNotificationTask($(this));
        });

        $(document).on('click', '.notification', function(e){
            e.preventDefault();
            if($(this).attr('href') === '#'){
                notifications.deleteAllNotification($(this));
            }else{
                let id = $(this).data('notif-id');
                window.location.href = $(this).attr('href') + '?notification_click=' + id;
            }
        });

        $(document).on('click', '#read_all_notification', function (e) {
            e.preventDefault();
            notifications.readAllNotification();
        });

        $(document).on('click', '#delete_all_notification', function (e) {
            e.preventDefault();
            notifications.deleteAllNotification();
        })
    },
    readAllNotification : () => {
        $.ajax({
            method: 'GET',
            url: '/read_all_notifications',
            data: {}
        }).done((res) => {
            notifications.generateNotificationsTasks();
        })
    },

    deleteAllNotification : () => {
        $.ajax({
            method: 'GET',
            url: '/delete_all_notifications',
            data: {}
        }).done((res) => {
            notifications.generateNotificationsTasks();
        })
    },

    deleteNotificationTask: (_this) => {
        let id_notification = $(_this).attr('data-notif-id');

        $.ajax({
            method: 'POST',
            url: '/delete_notifications',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                id: id_notification
            }
        }).done((res) => {
            notifications.generateNotificationsTasks();
        })
    },

    generateNotificationsTasks : () => {
        $.ajax({
            method: 'GET',
            url: '/getNotifications',
            data: {}
        }).done((res) => {
            $(document).find('.notifications-content').html(res);
        })
    }
};

notifications.init();

let chatnotifi  = {

    init: () => {
        chatnotifi.startTimer();
        chatnotifi.getChatNotifi();
    },

    getChatNotifi:  () => {

        $.ajax({
            method: 'GET',
            url: '/chat/getChatNotifications',
        }).done((res) => {
            chatnotifi.setBells(res);
        })

    },

    startTimer: () => {
       setInterval(chatnotifi.getChatNotifi, 10000);
    },

    setBells: (res) => {
        var userId = $('meta[name="user-id"]').attr('content');
        let chats = $('.custom-chat-users-list li');
        let json = JSON.parse(res);
        let ids = json.direct_chats, ids_main = json.main_chat;

            for (let key in ids) {
                if (ids[key] > 0 && $(chats[key]).find('a').find('i').length === 0) {
                    $(chats[key]).find('a').append('<i class="fa fa-bell-o pull-right"></i> <span class="pull-right" style="background-color: #0d6aad; height: 8px; width: 8px; border-radius: 50%; position: relative; top: 5px"></span>');
                    console.log('1')
                    chatnotifi.playChatSound();
                }
            }

            if (ids_main > 0 ) {
                    if( $('.box-title').find('i').length === 0) {
                        $('.box-title').append('<i class="fa fa-bell-o pull-right" style="font-size: 14px;"></i> <span class="pull-right" style="background-color: #0d6aad; height: 8px; width: 8px; border-radius: 50%; position: relative; top: 5px"></span>');
                        console.log('2')
                        chatnotifi.playChatSound();
                    }
            }else{
                $('.box-title').find('i').remove();
            }

    },

    playChatSound: () => {
        let chat_sound = document.getElementById('chat_message');
        chat_sound.play();
    }

};

$(document).ready(chatnotifi.init());

var notificationEmail = {
     init : function () {
         var pusher = new Pusher('dec55d4997ee67fe0e91', {
             cluster: 'eu',
             encrypted: true
         });
         var channel = pusher.subscribe('notification');
         channel.bind('new-emails', notificationEmail.newNotification);

     },
     newNotification : function (data) {
         // console.log(data['user'] ,user_email, data['server'] , window.location.hostname)
         if((data['user'] === user_email) && data['server'] == window.location.hostname) {
             var userId = $('meta[name="user-id"]').attr('content');
             $.ajax({
                 type: "POST",
                 url: "/api/v1/users/"+userId+"/emails",
                 data: {},
                 success: function (result) {
                     // console.log('set')
                     localStorage.setItem(`emails[INBOX]`,JSON.stringify(result));

                     window.postMessage({
                         updateMailList: true
                     })

                 },
                 error: function (result) {
                     console.log(result);
                 }

             });
             $('.emails-count').text('*');

         $.toast({
              heading: 'New emails',
              text: "You have "+ data['newEmailCount'] + " new emails . Refresh to see them",
              icon: 'info',
              loader: true,        // Change it to false to disable loader
              hideAfter : 20000,
              position: 'top-right',
          });
         }

     }
 };

$(document).ready(notificationEmail.init);