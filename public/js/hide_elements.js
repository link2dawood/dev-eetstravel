$(function () {
    $(document).bind('mousewheel', function (evt) {
        hideElements(); //catch mousewheel
    });

    $('.wrapper').scroll(function () {
        hideElements(); //catch scrollbar , oh shit !! AdminLTE uses fake browser scrolling !!
    });

    function hideElements() {
        $(".datepicker-dropdown").css("display", "none");
        /// $(".datepicker-days").css("display", "none"); //for all datapickers
        $(".pac-container").css("display", "none"); //for all google maps places autocompletes
        //if ($('select').data('select2') && $('select').data('select2').isOpen()) {
        $('#service-select').select2("close"); //for service select
        //}
    }

    $(".select2-selection__rendered").removeAttr("title");

    $('select').on('change', function (evt) {
        $('.select2-selection__rendered').removeAttr('title');
    });

    $('.datepickernoyear').datepicker( {
        format: "mm-dd",
        viewMode: "months", 
        maxViewMode: "months"
    }); 
    /*
    $('.datepicker').on('show', function(e){
    $('.datapicker').css("z-index", "2000");
    $('.main-sidebar').css("z-index", "520");
    });*/

});
	
	