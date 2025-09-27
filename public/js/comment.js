var comment = {
    init : function () {
        comment.config = {
            reference_id_comment : $('#default_reference_id').val(),
            reference_type_comment : $('#default_reference_type').val(),
            id_comment : $('#id_comment').val()
        };
        comment.bindEvents();
        comment.getComments();

        if ($('#content').length > 0) {
            CKEDITOR.replace('content', {
                height: '100px'
            });

            CKEDITOR.config.toolbar = [
                ['Bold','Italic','Underline','SpellChecker','TextColor','BGColor','Undo','Redo','Link','Unlink','-','Format'],
             
            ] ;


        }
    },

    bindEvents: function () {
        $(document).on("click", ".reply_comment", function () {
            comment.reply($(this));
            return false;
        });

        $(document).on("click", "#reply_close", function () {
            comment.replyClose($(this));
            return false;
        });

        $('#form_comment').on("submit", function(){
            for ( instance in CKEDITOR.instances )
                CKEDITOR.instances[instance].updateElement();
        })

        $('#content').keydown(function (e) {
            if (e.ctrlKey && e.keyCode == 13) {
                $('#form_comment').submit();
            }
        });

    },
    getAnnouncements(){
        $.ajax({
            type:'GET',
            url:  '/announcement/' + $('#announcements').data('announ-id') + '/generate-announcements?' +
                'reference_id='+comment.config.reference_id_comment+'&' +
                'reference_type='+comment.config.reference_type_comment+'&' +
                'id_comment='+comment.config.id_comment+'',
            data: {
            },
            cache:false,
            contentType: false,
            processData: false,
            success:function(data){
                $(document).find('#show_comments').html(data);
            },
            error: function(data){
                console.log(data)
            }
        });
    },
    getComments(){

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content')
            }
        });
        let check = document.getElementById('announcements');
        let route = '';
        if (check) return comment.getAnnouncements();
        $.ajax({
            type:'POST',
            url:  '/comment/generate-comments?' +
                'reference_id='+comment.config.reference_id_comment+'&' +
                'reference_type='+comment.config.reference_type_comment+'&' +
                'id_comment='+comment.config.id_comment+'',
            data: {
            },
            cache:false,
            contentType: false,
            processData: false,
            success:function(data){
                $(document).find('#show_comments').html(data);
            },
            error: function(data){
                console.log(data)
            }
        });
    },
    reply(link){
        $(document).find('#name').html('');
        $(document).find('#parent_comment').attr('value',  $('#id_comment').val());

        const id  = link.attr('href'),
            top = $(id).offset().top;
        $('body,html').animate({scrollTop: top}, 500);

        const id_comment = link.attr('data-comment-id');
        const parent_name = link.attr('data-parent-name');

        $(document).find('#author_name').css({'display': 'table-cell'});
        $(document).find('#name').append('@'+parent_name);
        $(document).find('#parent_comment').attr('value', id_comment);
        $(document).find('#content').focus();
    },
    replyClose(){
        $(document).find('#author_name').css({'display': 'none'});
        $(document).find('#name').html('');
        $(document).find('#parent_comment').attr('value', '');
        $(document).find('#parent_comment').attr('value', $('#id_comment').val());
    }

};

$(document).ready(comment.init);


