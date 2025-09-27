$(document).ready(function () {
    $('.fileToUpload').on('change', function(){
        var self = $(this);
        var that = this;
        var url = $('#url').data('url');

        var formData = new FormData;
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        formData.append('file', this.files[0]);
        formData.append('model', self.data('model'));
        formData.append('id', self.data('id'));

        $.ajax({
            method: "POST",
            url: url,
            data: formData,
            processData: false,  // tell jQuery not to process the data
            contentType: false,  // tell jQuery not to set contentType
        }).done(function( msg ) {
            if (window.location.href.indexOf("edit") > 0){
                self.closest('.file-caption-main').find('.file-caption-name').html(that.files[0].name);      
                $('.pic').attr('src', msg);      
            } else{
                self.closest('.thumbnail').find('img.pic').attr('src', msg);      
            }
        });
    });
});