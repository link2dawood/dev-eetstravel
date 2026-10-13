$(document).ready(function () {
    function notify(message, type) {
        var toast = window.appToast || window.showToast;
        if (typeof toast === 'function') {
            toast(message, type);
        }
    }

    $('.fileToUpload').on('change', function(){
        var self = $(this);
        var that = this;
        var url = $('#url').data('url');

        // Inputs without a model (tour create/edit) are saved with their form instead
        if (!this.files || !this.files[0] || !self.data('model') || !self.data('id')) {
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
                // Images page: show the new picture in this category's card (create the <img> if it had none)
                var card = self.closest('.card, .thumbnail');
                var img = card.find('img.pic');
                if (!img.length) {
                    var frame = card.find('.ratio').first();
                    img = $('<img class="pic" alt="" style="width:100%; height:100%; object-fit:cover;">').appendTo(frame.length ? frame : card);
                }
                img.show().attr('src', uploadedFile.url);
                card.find('.image-placeholder').attr('style', 'display:none !important');
            }

            notify('Image uploaded successfully', 'success');
        }).fail(function(xhr) {
            var json = xhr.responseJSON || {};
            var fieldErrors = json.errors ? Object.values(json.errors)[0] : null;
            var message = (fieldErrors && fieldErrors[0]) || json.error || json.message || 'Image upload failed';
            notify(message, 'error');
        });
    });
});