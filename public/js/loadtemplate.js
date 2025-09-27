

sendTemplate = function () {

    $('form#templateSendForm').submit(function (event) {

        event.preventDefault();

        $('#TemplatesModal').find('#send').prop('disabled', true);

        var data = new FormData($(this)[0]);

        $.ajax({
            url: '/templates/api/send',
            method: 'POST',
            enctype: 'multipart/form-data',
            processData: false,
            contentType: false,
            cache: false,
            timeout: 600000,
            data: data
        }).done((res) => {
            console.log(res);
            $('#TemplatesModal').find('#email').val('');
            $('#TemplatesModal').find('#subject').val('');
//                CKEDITOR.instances['templatesContent'].setData('This field is optional');
            $('#TemplatesModal').modal('hide');
            $.toast({
                        heading: 'Success',
                        text: "Email sended successfully",
                        icon: 'success',
                        loader: true,        // Change it to false to disable loader
                        hideAfter : 3000,
                        position: 'top-right',
                    });
        });

        return false;
    });

};


loadTemplateById = function (service_id, email, name, pax, address, emailto, phone, description, status, time_from,time_to,supplier_url,price_for_one, menu, tour_id, reference,tour_name,package_id,offer_id) {

    var id = $('#TemplatesModal').find('#template_selector').val();

    $.ajax({
        url: '/templates/api/load',
        method: 'GET',
        data: {
            service_id: service_id,
            id: id,
            email: email,
            name: name,
            pax: pax,
            address: address,
            emailto: emailto,
            phone: phone,
            description: description,
            status: status,
            time_from: time_from,
            time_to: time_to,
            supplier_url: supplier_url,
            price_for_one: price_for_one,
            menu: menu,
            tour_id: tour_id,
            reference: reference,
            tour_name:tour_name,
            package_id:package_id,
			offer_id: offer_id,
        }
    }).done((res) => {
      //  $('#TemplatesModal').find('#email').val(res.email);
        var emailsSelect = $('#emails_select');
        emailsSelect.empty();

        // Check if there are values in the array
        if (res.emails.length > 1) {
            $.each(res.emails, function(index, value) {
                emailsSelect.append('<option value="' + value + '">' + value + '</option>');
            });
        } else if (res.emails.length === 1) {
            // If there is only one value, you might want to set it as the selected option
            $('#TemplatesModal').find('#email').val(res.emails[0]);
            emailsSelect.hide();
        } else {
            // If there are no values, you might want to add a default option or handle it in some way
            emailsSelect.hide();
        }

        $('#TemplatesModal').find('#id').val(id);
        $('#TemplatesModal').find('#subject').val(tour_name+" Request #"+package_id);
        $('#TemplatesModal').find('#package_id').val(package_id);
        $('#TemplatesModal').find('#tour_id').val(tour_id);
		$('#TemplatesModal').find('#offer_id').val(offer_id);
//            if(res.content === '' ) res.content = 'This field is optional'; 
        CKEDITOR.instances['templatesContent'].setData(res.content);
        $('#TemplatesModal').modal('show');
    });

};


loadTemplate = function (service_id, email, name, pax, address, emailto, phone, description, status, time_from,time_to,supplier_url,price_for_one, menu, tour_id, reference,tour_name,package_id,offer_id) {

    var selected = '';
    var html = '';
	if ($(document).find('#templatesContent').length > 0) {
            if (CKEDITOR.instances['templatesContent']) {
                CKEDITOR.instances['templatesContent'].destroy(true);
            }
            CKEDITOR.replace('templatesContent', {
                extraPlugins: 'confighelper',
                height: '200px',
                title: false
            });
        }
    $('#TemplatesModal').find('#send').prop('disabled', false);
    $('#TemplatesModal').find('#email').val('');
    $('#TemplatesModal').find('#subject').val('');
    $('#TemplatesModal').find('#file').val('');
    $('#TemplatesModal').find('#file_name').text('');

    CKEDITOR.instances['templatesContent'].setData('');

    $.ajax({
        url: '/templates/api/loadServiceTemplates',
        method: 'GET',

        data: {
            id: service_id,
        },
        success: function (res) {

            for (var i = 0; i < res.templates.length; i++) {

                (i == 0) ? selected = "selected" : "";

                if (res.templates[i]['name'] != 'Footer' && res.templates[i]['name'] != 'Header') {
					if(res.templates[i]['name'] == 'Offer_confirm_template'){
                    html += "<option value='" + res.templates[i]['id'] + "' " + selected + ">" + res.templates[i]['name'] + "</option>";
					}
                }
            }

            $('#TemplatesModal').find('#template_selector').html(html);

            $('#TemplatesModal').find('#template_selector').on('change', function () {
                loadTemplateById(service_id, email, name, pax, address, emailto, phone, description, status, time_from,time_to,supplier_url, price_for_one, menu, tour_id, reference,tour_name,package_id,offer_id);
            });

            loadTemplateById(service_id, email, name, pax, address, emailto, phone, description, status, time_from,time_to,supplier_url, price_for_one, menu, tour_id, reference,tour_name,package_id,offer_id);
        }

    })

};


function loadGuestTemplate(service_id, email, name, pax, address, emailto, phone, description, status, time_from, price_for_one, menu, tour_id,offer_id) {
    var selected = '';
    var html = '';

//        $('#TemplatesModal').find('#send').prop('disabled', false);
//        $('#TemplatesModal').find('#email').val('');
//        $('#TemplatesModal').find('#subject').val('');
//        $('#TemplatesModal').find('#file').val('');
//        $('#TemplatesModal').find('#file_name').text('');
//        CKEDITOR.instances['templatesContent'].setData('');

    $.ajax({
        url: '/templates/api/loadServiceTemplates',
        method: 'GET',

        data: {
            id: 0,
        },
        success: function (res) {

            html += "<option value='' selected disabled hidden>Choose here</option>";
            for (var i = 0; i < res.templates.length; i++) {

                //(i == 0) ? selected = "selected" : "";

                if (res.templates[i]['name'] != 'Footer' && res.templates[i]['name'] != 'Header') {
                    html += "<option value='" + res.templates[i]['id'] + "' " + selected + ">" + res.templates[i]['name'] + "</option>";
                }
            }

            $('#template_selector_guest').html(html);

            $('#template_selector_guest').on('change', function () {
                var id = $('#template_selector_guest').val();

                $.ajax({
                    url: '/templates/api/load',
                    method: 'GET',
                    data: {
                        service_id: service_id,
                        id: id,
                        email: email,
                        name: name,
                        pax: pax,
                        address: address,
                        emailto: emailto,
                        phone: phone,
                        description: description,
                        status: status,
                        time_from: time_from,
                        price_for_one: price_for_one,
                        menu: menu,
                        tour_id: tour_id,
						offer_id: offer_id,
                    }
                }).done((res) => {
                    CKEDITOR.instances['roomlist_textarea'].setData(res.content);
                });
            });
        }
    })
}