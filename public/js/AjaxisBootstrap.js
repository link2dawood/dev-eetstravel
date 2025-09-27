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
}), $(document).on("click", ".add-direct-chat", function() {
    GET($(this).data("link"))
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
        type: "get",
        url: baseURL + $(this).data("link"),
        success: function(a) {
            if (a.error) {
                var t = $("#errors_message");
                t.css({
                    display: "block"
                }), t.html(a.message), $("#myModal").modal("hide")
            } else if (a.email_search) {
                var e = $("#search_form");
                $(e).submit()
            } else window.location = a
        }
    })
}), $(document).on("click", ".save", function() {
    POST($("#AjaxisForm").serializeArray(), $(this).data("link"))
});