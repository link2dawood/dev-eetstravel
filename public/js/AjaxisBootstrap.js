function GET(a) {
    console.log(a), $.ajax({
        async: !0,
        type: "get",
        url: baseURL + a,
        success: function(a) {
            console.log(a), $(".AjaxisModal").html(a)
        },
        error: function(a) {
            console.log("error", a)
        }
    })
}

function POST(a, t) {
    $.ajax({
        async: !0,
        type: "post",
        url: baseURL + t,
        data: a,
        success: function(a) {
            window.location = a
        }
    })
}
$(document).on("click", ".delete", function() {
    console.log("delete: " + $(this).data("link")), GET($(this).data("link"))
}), $(document).on("click", ".add-direct-chat", function(event) {
    event.preventDefault();
    var modal = document.getElementById('myModal');
    var content = $(modal).find('.AjaxisModal');
    var version = (content.data('chat-request-version') || 0) + 1;
    content.data('chat-request-version', version);
    content.html('<div class="modal-header"><h2 class="modal-title">Chat</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body" role="status">Loading...</div>');
    var trigger = this;
    $(modal).off('hidden.bs.modal.chatDialog').one('hidden.bs.modal.chatDialog', function() {
        if (document.contains(trigger)) trigger.focus();
    });
    $(modal).modal('show');
    $.ajax({
        type: 'GET',
        url: $(this).data('link'),
        success: function(html) {
            if (content.data('chat-request-version') !== version) return;
            // Chat fragments include legacy dialog wrappers; reuse the layout's dialog.
            var fragment = $('<div>').append($.parseHTML(html, document, true));
            var body = fragment.find('.modal-content').first();
            content.html(body.length ? body.html() : html);
            content.find('.modal-header').first().append('<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>');
            $(modal).modal('handleUpdate');
        },
        error: function() {
            if (content.data('chat-request-version') !== version) return;
            content.find('.modal-body').attr('role', 'alert').text('Unable to load this chat dialog. Close it and try again.');
        }
    });
}), $(document).on("click", ".create-email", function() {
    $.ajax({
        async: !0,
        type: "get",
        url: $(this).data("link"),
        success: function(a) {
            $("#modalCreate").html(a), $("#modalCreate").modal()
        }
    })
}), $(document).on("click", ".quotation-add-column", function() {
    return $.ajax({
        async: !0,
        type: "get",
        url: $(this).data("link"),
        success: function(a) {
            $("#myModal").html(a), $("#myModal").modal()
        }
    }), !1
}), $(document).on("click", ".reply", function() {
    console.log($(this).data("to")), $.ajax({
        async: !0,
        type: "get",
        url: $(this).data("link"),
        data: {
            reply_message: $(this).data("reply-message"),
            reply_folder: $(this).data("reply-folder"),
            reply_to: $(this).data("to")
        },
        success: function(a) {
            $("#modalCreate").html(a), $("#modalCreate").modal()
        }
    })
}), $(document).on("click", ".email-add-folder", function() {
    $.ajax({
        async: !0,
        type: "get",
        url: $(this).data("link"),
        data: {},
        success: function(a) {
            $("#modalCreate").html(a), $("#modalCreate").modal()
        }
    })
}), $(document).on("click", ".moveToFolder", function() {
    console.log("reply", $(this).data("link"), $(this).data("reply-message"), $(this).data("reply-folder")), $.ajax({
        async: !0,
        type: "get",
        url: $(this).data("link"),
        data: {
            message_id: $(this).data("message-id"),
            message_folder: $(this).data("message-folder")
        },
        success: function(a) {
            $("#modalCreate").html(a), $("#modalCreate").modal()
        }
    })
}), $(document).on("click", ".edit", function() {
    GET($(this).data("link"))
}), $(document).on("click", ".display", function() {
    console.log($(this).data("link")), GET($(this).data("link"))
}), $(document).on("click", ".create", function() {
    GET($(this).data("link"))
}), $(document).on("click", ".destroy", function() {
    $.ajax({
        async: !0,
        type: "post",
        headers: {"X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content},
        url: baseURL + $(this).data("link"),
        success: function(a) {
            if (a.error) {
                // the record is still in use: say why (most pages have no #errors_message box)
                var t = $("#errors_message");
                if (t.length) {
                    t.css({
                        display: "block"
                    }), t.html(a.message)
                } else if (typeof window.appToast === "function") {
                    window.appToast($("<div>").html(a.message).text(), "error")
                }
                $("#myModal").modal("hide")
            } else if (a.email_search) {
                var e = $("#search_form");
                $(e).submit()
            } else window.location = a
        }
    })
}), $(document).on("click", ".save", function() {
    POST($("#AjaxisForm").serializeArray(), $(this).data("link"))
});
