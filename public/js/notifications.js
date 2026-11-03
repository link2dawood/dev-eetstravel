let notifications = {
    init : () => {
        notifications.config = {};
        notifications.bindEvents();
        notifications.generateNotificationsTasks();
    },
    feedback: (message, type) => {
        if (typeof window.appToast === 'function') {
            window.appToast(message, type);
        } else if ($.toast) {
            $.toast({
                heading: type === 'success' ? 'Success' : 'Error',
                text: message,
                icon: type,
                hideAfter: 5000,
                position: 'top-right'
            });
        }
    },
    requestError: (xhr, fallback) => {
        if (xhr && xhr.status === 419) {
            return 'Your session expired. Refresh the page and try again.';
        }
        if (xhr && xhr.status === 429) {
            return 'Too many requests. Please wait a moment and try again.';
        }
        if (xhr && (xhr.status === 401 || xhr.status === 403)) {
            return 'You are not authorized to perform this action. Refresh the page to continue.';
        }
        return fallback;
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
            notifications.readAllNotification($(this));
        });

        $(document).on('click', '#delete_all_notification', function (e) {
            e.preventDefault();
            notifications.deleteAllNotification($(this));
        })
    },
    readAllNotification : (button) => {
        let request = $.tmsLoading.ajax(button, {
            method: 'GET',
            url: '/read_all_notifications',
            data: {}
        }, 'Marking read...');
        if (!request) return;

        request.done((res) => {
            notifications.feedback('Notifications marked as read.', 'success');
            notifications.refreshNotifications();
        }).fail((xhr) => {
            notifications.feedback(notifications.requestError(xhr, 'Unable to mark notifications as read. Please try again.'), 'error');
        });
    },

    deleteAllNotification : (button) => {
        let request = $.tmsLoading.ajax(button, {
            method: 'POST',
            url: '/delete_all_notifications',
            data: {}
        }, 'Deleting...');
        if (!request) return;

        request.done((res) => {
            notifications.feedback('Notifications deleted.', 'success');
            notifications.refreshNotifications();
        }).fail((xhr) => {
            notifications.feedback(notifications.requestError(xhr, 'Unable to delete notifications. Please try again.'), 'error');
        });
    },

    deleteNotificationTask: (_this) => {
        if (_this.prop('disabled')) {
            return;
        }
        let id_notification = $(_this).attr('data-notif-id');
        let request = $.tmsLoading.ajax(_this, {
            method: 'POST',
            url: '/delete_notifications',
            timeout: 8000,
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                id: id_notification
            }
        }, 'Deleting...');
        if (!request) return;

        request.done((res) => {
            if (res === true) {
                notifications.generateNotificationsTasks();
            } else {
                notifications.feedback('Unable to delete this notification. Please try again.', 'error');
            }
        }).fail((xhr) => {
            notifications.feedback(notifications.requestError(xhr, 'Unable to delete this notification. Please try again.'), 'error');
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
            let previousRequest = container.data('notifications-request');
            if (previousRequest && previousRequest.readyState !== 4) {
                previousRequest.abort();
            }

            let request = $.ajax({
                method: 'GET',
                url: '/getNotifications',
                cache: false,
                data: { layout: container.attr('data-notifications-layout') || 'legacy' }
            });
            container.data('notifications-request', request);

            let notificationList = container.find('[data-notifications-list]');
            if (notificationList.length && container.attr('data-notifications-layout') === 'tabler') {
                notificationList.attr('aria-busy', 'true').html(
                    '<div class="tms-skeleton-row" aria-hidden="true">' +
                        '<span class="tms-skeleton" style="width: 2rem; height: 2rem;"></span>' +
                        '<span class="tms-skeleton" style="width: 85%;"></span>' +
                        '<span class="tms-skeleton" style="width: 3rem;"></span>' +
                    '</div>'.repeat(3)
                );
            }

            request.done((res) => {
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
            }).fail((xhr, status) => {
                if (container.data('notifications-request-version') !== requestVersion) {
                    return;
                }
                if (status === 'abort') {
                    return;
                }
                container.find('[data-notifications-list]').text('Unable to load notifications. Please refresh to try again.');
            }).always(() => {
                if (container.data('notifications-request-version') === requestVersion) {
                    container.find('[data-notifications-list]').removeAttr('aria-busy');
                }
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
        window.clearTimeout(chatnotifi.timer);
        chatnotifi.timer = null;
        if (!document.hidden) {
            chatnotifi.getChatNotifi();
        }
    },

    getChatNotifi:  () => {
        if (chatnotifi.request) {
            return;
        }

        let request = $.ajax({
            method: 'GET',
            url: '/chat/getChatNotifications',
            timeout: 8000
        });
        chatnotifi.request = request;

        request.done((res) => {
            chatnotifi.setBells(res);
        }).fail((xhr, status) => {
            if (status !== 'abort') {
                console.warn('Chat notification polling failed.', status);
            }
        }).always(() => {
            if (chatnotifi.request !== request) {
                return;
            }

            chatnotifi.request = null;
            if (!document.hidden) {
                chatnotifi.timer = window.setTimeout(chatnotifi.getChatNotifi, 10000);
            }
        });
    },

    stop: () => {
        window.clearTimeout(chatnotifi.timer);
        if (chatnotifi.request) {
            let request = chatnotifi.request;
            chatnotifi.request = null;
            request.abort();
        }
    },

    setBells: (res) => {
        var userId = $('meta[name="user-id"]').attr('content');
        let chats = $('.custom-chat-users-list li');
        // jQuery may already have parsed it; an expired session answers with the HTML login page
        let json = res;
        if (typeof res === 'string') {
            try { json = JSON.parse(res); } catch (e) { return; }
        }
        if (!json || typeof json !== 'object') return;
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

$(document).ready(chatnotifi.init);
window.addEventListener('pagehide', chatnotifi.stop);
window.addEventListener('pageshow', chatnotifi.init);
document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
        chatnotifi.stop();
    } else {
        chatnotifi.init();
    }
});

var notificationEmail = {
     init : function () {
         var config = window.appPusherConfig;
         if (!config || !config.enabled || !config.key || typeof Pusher !== 'function' || notificationEmail.pusher) {
             return;
         }
         var pusher = notificationEmail.pusher = new Pusher(config.key, {
             cluster: config.cluster,
             encrypted: true
         });
         var channel = pusher.subscribe('notification');
         channel.bind('new-emails', notificationEmail.newNotification);

     },
     newNotification : function (data) {
         // console.log(data['user'] ,user_email, data['server'] , window.location.hostname)
         if((data['user'] === user_email) && (data['server'] === window.location.hostname || data['server'] === window.location.host)) {
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
