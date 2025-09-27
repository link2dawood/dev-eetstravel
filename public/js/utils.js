$(document).ready(function() {

    $('.protect_submit').on('click', function (event) {
        event.preventDefault();
        let form = $(this).closest('form');
        let check = true;

        $(this).attr("disabled", true);
        $('.protect_loader').removeClass('hidden');

        $(form).find("input[type=text]:not('.emails_validate')").each(function(){
            let val = $(this).val();
            let block_append = $(this).closest('.form-group');

            deleteMessageBlock(block_append);

            if(val === ''){
                setMessage('Field does not must empty', block_append);
                check = false;
            }
        });

        $(form).find('input[type=text].emails_validate').each(function(){
            let val = $(this).val();
            let block_append = $(this).closest('.form-group');
            let emailsArray = val.split(',');

            deleteMessageBlock(block_append);

            if(val === ''){
                setMessage('Field does not must empty', block_append);
                check = false;
            }

            if(isValidMultiEmails(emailsArray)){
                setMessage(`Email is not a valid email`, block_append);
                check = false;
            }
        });

        if(check){

            for ( instance in CKEDITOR.instances )
                CKEDITOR.instances[instance].updateElement();

            let formData = new FormData(form[0]);

            $.ajax({
                url: $(form).attr('action'),
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(data)
                {
                    $('.protect_submit').attr("disabled", false);
                    $('.protect_loader').addClass('hidden');
                    $('#modalCreate').modal('hide');
                    $.toast({
                        heading: 'Success',
                        text: data.result,
                        icon: 'success',
                        loader: true,
                        hideAfter : 15000,
                        position: 'top-right',
                    });
                }
            });

        }else{
            $(this).attr("disabled", false);
            $('.protect_loader').addClass('hidden');
        }
    });

    activateTab = function() {
        var tab_id = location.search.split('tab=')[1];		
        $('#' + tab_id).click();
    };

    if(location.search.split('tab=')[1]) activateTab();

    var mode = $('#legend_help').data('mode');

    function validateEmail(email) {
        var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    }

    function setMessage(message, block_append) {
        $(block_append).append("<p class='alert alert-danger validation-error'>"+message+"</p>");
        $(block_append).addClass('has-error');
    }

    function isValidMultiEmails(val) {
        for (let i = 0; i < val.length; i++){
            if(!validateEmail(val[i].trim())){
                return true;
            }
        }

        return false;
    }

    function deleteMessageBlock(block_append) {
        $(block_append).removeClass('has-error');
        $(block_append).find('p.validation-error').remove();
    }
	
	$('#quotation_tab').on('click', function (event) {
		$('.legend_tour').hide();
	});
	
	$('#service_tab').on('click', function (event) {
		$('.legend_tour').show();
	});
    
	$('#roomlist_tab').on('click', function (event) {
		$('.legend_tour').hide();
	});

    $('#help').hover(
        function() {
            $('#legend_help').stop().fadeTo( "fast", 1 ,function() {

                if(mode == 2) {
                    $(this).css({"left": "-50%"});
                }else if (mode == 1) {
                    $(this).css({"left": "-20px"});
                }else if (mode == 3){
                    $(this).css({"left": "47%" });
                }else if (mode == 4){
                    $(this).css({"right": "10%" });
                }else {
                    $(this).css({"right": "20px"});
                }
                $(this).css({"opacity":1});

            });
        }, function() {
            $('#legend_help').stop().fadeTo( "fast", 0,function() {

                if(mode == 2){
                    $(this).css({"left": "200%"});
                }else if (mode == 1 ) {
                    $(this).css({"left": -$(document).width()});
                }else if (mode == 3) {
                    $(this).css({"left": "-200%"});
                }else if (mode == 4){
                    $(this).css({"right": "-200%" });
                }else {
                    $(this).css({"right": "-100%"});
                }

                $(this).css({"opacity":0});
            });
        }
    );


    $('#hollydaycalbutton').hover(
        function() {
            $('#hollydaycaldiv').stop().fadeTo( "fast", 1 ,function() {
                if(mode == 2) {
                    $(this).css({"left": "-50%"});
                }else if (mode == 1) {
                    $(this).css({"left": "-20px"});
                }else if (mode == 3){
                    $(this).css({"left": "47%" });
                }else if (mode == 4){
                    $(this).css({"right": "10%" });
                }else {
                    $(this).css({"right": "20px"});
                }
                $(this).css({"opacity":1});

            });
        }, function() {
            $('#hollydaycaldiv').stop().fadeTo( "fast", 0,function() {
                if(mode == 2){
                    $(this).css({"left": "200%"});
                }else if (mode == 1 ) {
                    $(this).css({"left": -$(document).width()});
                }else if (mode == 3) {
                    $(this).css({"left": "-200%"});
                }else if (mode == 4){
                    $(this).css({"right": "-200%" });
                }else {
                    $(this).css({"right": "-100%"});
                }

                $(this).css({"opacity":0});
            });
        }
    );

    ///mode = $('#legend_help').data('legend_help_quotation');
	
	 $('.quotation_legend').hover(
        function() {
            $('#legend_help_quotation').stop().fadeTo( "fast", 1 ,function() {
                $(this).css({"left": "65%"});
                $(this).css({"opacity":1});
            });
        }, function() {
            $('#legend_help_quotation').stop().fadeTo( "fast", 0,function() {
                $(this).css({"left": "-200%"});
                $(this).css({"opacity":0});
            });
        }
    );
    
    
	 $('.guest_list_legend').hover(
        function() {
            $('#legend_help_guest_list').stop().fadeTo( "fast", 1 ,function() {
                $(this).css({"left": "65%"});
                $(this).css({"opacity":1});
            });
        }, function() {
            $('#legend_help_guest_list').stop().fadeTo( "fast", 0,function() {
                $(this).css({"left": "-200%"});
                $(this).css({"opacity":0});
            });
        }
    );
});