$(document).ready(function () {
    $('.fileToUpload').on('change', function(){
        var self = $(this);
        var that = this;
        var url = $('#url').data('url');

        if (!this.files || !this.files[0]) {
            return;
        }

        var formData = new FormData;
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        formData.append('files[]', this.files[0]);
        formData.append('model', self.data('model'));
        formData.append('id', self.data('id'));

        $.ajax({
            method: "POST",
            url: url,
            data: formData,
            processData: false,
            contentType: false,
        }).done(function(response) {
            var uploadedFile = response && response.files && response.files.length ? response.files[0] : null;
            if (!uploadedFile || !uploadedFile.url) {
                return;
            }

            if (window.location.href.indexOf("edit") > 0){
                self.closest('.file-caption-main').find('.file-caption-name').html(that.files[0].name);
                $('.pic').attr('src', uploadedFile.url);
            } else{
                self.closest('.thumbnail').find('img.pic').attr('src', uploadedFile.url);
            }

            if (window.showToast) {
                window.showToast('Image uploaded successfully', 'success');
            }
        }).fail(function(xhr) {
            var message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Image upload failed';
            if (window.showToast) {
                window.showToast(message, 'error');
            } else {
                alert(message);
            }
        });
    });
});