let notifications = {
    init : () => {
        notifications.config = {};
        notifications.bindEvents();
        notifications.generateNotificationsTasks();
    },
    bindEvents: () => {
        $(document).on('click', '.delete-notification-task', function (e) {
            e.preventDefault();
            e.stopPropagation();
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
            notifications.refreshNotifications();
        })
    },

    deleteAllNotification : () => {
        $.ajax({
            method: 'GET',
            url: '/delete_all_notifications',
            data: {}
        }).done((res) => {
            notifications.refreshNotifications();
        })
    },

    deleteNotificationTask: (_this) => {
        if (_this.prop('disabled')) {
            return;
        }
        let id_notification = $(_this).attr('data-notif-id');
        _this.prop('disabled', true);

        $.ajax({
            method: 'POST',
            url: '/delete_notifications',
            timeout: 8000,
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                id: id_notification
            }
        }).done((res) => {
            if (res === true) {
                notifications.generateNotificationsTasks();
            }
        }).always(() => {
            _this.prop('disabled', false);
        });
    },

    removeNotificationFromTabler: (_this) => {
        if ($(document).find('.notifications-content').length) {
            notifications.generateNotificationsTasks();
            return;
        }

        let item = $(_this).closest('.tabler-notification-item');

        item.slideUp(150, function () {
            $(this).remove();

            notifications.updateTablerBadge(-1);

            let list = $('.tabler-notifications-list');
            if (list.find('.tabler-notification-item').length === 0) {
                list.html("<div class='list-group-item tabler-notifications-empty'><div class='text-muted'>You don't have notifications</div></div>");
                $('.tabler-delete-all-notifications').addClass('disabled-link');
                $('#read_all_notification').addClass('disabled-link');
            }
        });
    },

    updateTablerBadge: (change) => {
        let badges = $('.tabler-notifications-badge');
        let current = parseInt(badges.first().text(), 10) || 0;
        let next = Math.max(0, current + change);

        if (next > 0) {
            badges.text(next);
        } else {
            badges.remove();
            $('#read_all_notification').addClass('disabled-link');
        }
    },

    refreshNotifications: () => {
        if ($(document).find('.notifications-content').length) {
            notifications.generateNotificationsTasks();
            return;
        }

        window.location.reload();
    },

    generateNotificationsTasks : () => {
        let containers = $('.notifications-content');
        if (!containers.length) {
            return;
        }

        containers.each(function () {
            let container = $(this);
            let requestVersion = (container.data('notifications-request-version') || 0) + 1;
            container.data('notifications-request-version', requestVersion);
            $.ajax({
                method: 'GET',
                url: '/getNotifications',
                cache: false,
                data: { layout: container.attr('data-notifications-layout') || 'legacy' }
            }).done((res) => {
                if (container.data('notifications-request-version') !== requestVersion) {
                    return;
                }
                if (container.attr('data-notifications-layout') === 'tabler') {
                    // Keep Bootstrap's toggle and menu nodes so an open dropdown stays open.
                    let content = $('<div>').html(res);
                    let menu = container.children('.dropdown-menu');
                    let scrollTop = menu.find('[data-notifications-list]').scrollTop();
                    container.children('[data-bs-toggle="dropdown"]').html(
                        content.children('[data-bs-toggle="dropdown"]').html()
                    );
                    menu.html(content.children('.dropdown-menu').html());
                    menu.find('[data-notifications-list]').scrollTop(scrollTop);
                } else {
                    container.html(res);
                }
            }).fail(() => {
                if (container.data('notifications-request-version') !== requestVersion) {
                    return;
                }
                container.find('[data-notifications-list]').text('Unable to load notifications. Please refresh to try again.');
            });
        });
    }
};

notifications.init();
window.notifications = notifications;
window.deleteTablerNotification = function (event, element) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
        if (typeof event.stopImmediatePropagation === 'function') {
            event.stopImmediatePropagation();
        }
    }

    notifications.deleteNotificationTask($(element));
    return false;
};

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
