$(document).on('click', '.pre-loader-func', function (e) {
    let block_overlay = '<div class="overlay" id="overlay_delete">\n' +
        '\t\t<i class="fa fa-refresh fa-spin"></i>\n' +
        '\t</div>';
    let overlay_component = $(this).closest('.box-body');
    overlay_component.append(block_overlay);
});
var page = $('.wrapper');
var header = document.getElementById("fixed-scroll");

if (header) {
    var sticky = header.offsetTop + 150;
    $(page, window, 'body').on('scroll', function () {
        checkScroll();
    });

}

function checkScroll() {
    if ($(page).scrollTop() + 100 >= sticky) {
        header.classList.add("sticky");
    } else {
        header.classList.remove("sticky");
    }
}
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
    }
});
$('.datepicker').datepicker({format: 'yyyy-mm-dd', autoclose: true});
$('.timepicker').datetimepicker({format: 'HH:mm', 'sideBySide': true});
$('select:not(.select2)').select2();

$("#departure_date").datepicker({
    onSelect: function (dateText) {
    }
}).on("change", function () {
    let val_date_dep = $(this).val();
    let form = $(this).closest('form');
    let ret_date = $(form).find('#retirement_date');

    $(ret_date).datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true
    }).datepicker('setDate', val_date_dep);
    $(ret_date).datepicker('show');
});

jQuery(document).ready(function () {
    jQuery('.loadingoverlay').fadeOut();
});