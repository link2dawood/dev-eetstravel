var config = {
    tour_id: null,
    transfer_id : null,
    bus_id: null,
    date: null,
    id_bus_day: null
},
 chart2 = [],
 modal_id = '#modalUpdateTrip',
 mode = 1,
 loaded_data = null,
 form_array = [],
 spliced = [];
 filter_all = true;

var d = new Date();
var monthd =(d.getMonth() + 1);
var yeard = d.getFullYear();
var mind = new Date(yeard, d.getMonth(), 1);
var lastd = new Date(yeard, d.getMonth() + 1, 0);
var datem = new Date(yeard,monthd,1);
datem.setMonth(datem.getMonth() + 6);
var maxd = datem;



function addMonths(date, months) {
    date.setMonth(date.getMonth() + months);
    return date;
}

var commentbus = {
    init : function () {
        commentbus.config = {
            reference_id_comment : $(modal_id).find('#default_reference_id').val(),
            reference_type_comment : $(modal_id).find('#default_reference_type').val(),
            id_comment : $(modal_id).find('#id_comment').val()
        };
        commentbus.bindEvents();
        commentbus.getComments();
    },

    bindEvents: function () {
        if (commentbus.bound) return;
        commentbus.bound = true;
        $(document).on("click", ".reply_comment", function () {
            commentbus.reply($(this));
            return false;
        });

        $(document).on("click", "#reply_close", function () {
            commentbus.replyClose($(this));
            return false;
        });
        $(document).find('#content').keydown(function (e) {
            if (e.ctrlKey && e.keyCode == 13) {
                $(modal_id).find('#form_comment').submit();

            }
        });


    },
    getAnnouncements(){
        $.ajax({
            type:'GET',
            url:  '/announcement/' + $(modal_id).find('#announcements').data('announ-id') + '/generate-announcements?' +
            'reference_id='+commentbus.config.reference_id_comment+'&' +
            'reference_type='+commentbus.config.reference_type_comment+'&' +
            'id_comment='+commentbus.config.id_comment+'',
            data: {
            },
            cache:false,
            contentType: false,
            processData: false,
            success:function(data){
                $(modal_id).find('#show_comments').html(data);
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
        if (check) return commentbus.getAnnouncements();
        $.ajax({
            type:'POST',
            url:  '/comment/generate-comments?' +
            'reference_id='+commentbus.config.reference_id_comment+'&' +
            'reference_type='+commentbus.config.reference_type_comment+'&' +
            'id_comment='+commentbus.config.id_comment+'',
            data: {
            },
            cache:false,
            contentType: false,
            processData: false,
            success:function(data){
                $(modal_id).find('#show_comments').html(data);
            },
            error: function(data){
                console.log(data)
            }
        });
    },
    reply(link){
        $(modal_id).find('#name').html('');
        $(modal_id).find('#parent_comment').attr('value',  $(modal_id).find('#id_comment').val());

        const id  = link.attr('href'),
            top = $(id).offset().top;
        $('body,html').animate({scrollTop: top}, 500);

        const id_comment = link.attr('data-comment-id');
        const parent_name = link.attr('data-parent-name');

        $(modal_id).find('#author_name').css({'display': 'flex'});
        $(modal_id).find('#name').append('@'+parent_name);
        $(modal_id).find('#parent_comment').attr('value', id_comment);
        $(modal_id).find('#content').focus();
    },
    replyClose(){
        $(modal_id).find('#author_name').css({'display': 'none'});
        $(modal_id).find('#name').html('');
        $(modal_id).find('#parent_comment').attr('value', '');
        $(modal_id).find('#parent_comment').attr('value', $(modal_id).find('#id_comment').val());
    }

};


function createGraph() {

    var leggend_array = [];

    $('#leggend_array span').each(function(){
        var span = $(this);
        leggend_array.push({ "title" : span.text(), "color" : span.attr('id') });
    });
    // $('div.bus_info').remove();

    $('input[type="radio"].minimal').iCheck({
        checkboxClass: 'icheckbox_minimal-blue',
        radioClass   : 'iradio_minimal-blue'
    });



    $('input[type="radio"].minimal').on('ifClicked', function(event){

        mode = $("input[type='radio'].minimal:checked").val();

        if( mode == 1 ){
            resetForm();
            tour_reset();
            $(document).find('.tours_bus').addClass('hidden');
            $(document).find('.name_bus').removeClass('hidden');
        }else{
            resetForm();
            tour_check();
            $(document).find('.tours_bus').removeClass('hidden');
            $(document).find('.name_bus').addClass('hidden');
        }

    });

    chart2 = AmCharts.makeChart("busdiv", {
        "type": "gantt",
        "theme": "light",
        "marginRight": 70,
        "marginTop": 30,
        "startDuration" : 1,
        "period": "DD",
        "dataDateFormat": "YYYY-MM-DD",
        "balloonDateFormat": "JJ:NN, DD MMMM",
        "mouseWheelZoomEnabled": false,
        "columnWidth": 0.5,
        "valueAxis": {
            "type": "date",
            "position": "top",
            "dateFormats" : ['DD'],
            "labelFunction": formatLabel,


    //        "stackType" : "regular",
            "autoGridCount" :false,
            //"includeHidden" : false,
            // "baseValue": 1,
            //  "strictMinMax" : true,
            "minPeriod": "DD",
            //"autoGridCount": false,
            //  "minorGridEnabled": true,
          ///  "minorTickLength" : 1,
        //    "labelFrequency" : 1,
        //    "tickLength" : 1,
            //   "ignoreAxisWidth" : true,
            //   "autoGridCount": false,
            //"includeAllValues" : true,
            //"autoGridCount" : false,
            //"dateFormats" : [{"period":"DD","format":"MMM DD"}],

           "minimumDate" : mind,
           "maximumDate" : maxd,
            "gridCount" : 50,

            dateFormats:[
                {period:'ss',format:'JJ:NN:SS'},
                {period:'mm',format:'JJ:NN'},
                {period:'hh',format:'JJ:NN'},
                {period:'DD',format:'DD'},
                {period:'WW',format:'DD'},
                {period:'MM',format:'MMM'},
                {period:'YYYY',format:'YYYY'}
                ]
          // "minPeriod": "MM DD"
           // 'minimumDate': new Date(2017, 8, 0),
           // 'maximumDate': new Date(2017, 9, 30)
            //  "gridColor": "#FFFFFF",
            //"gridAlpha": 0.2,
            //  "dashLength": 0
        },
        "chartScrollbar": {

            // "autoGridCount": true
        },
        "brightnessStep": 10,

        "rotate": true,
        "categoryField": "category",
        "segmentsField": "segments",
        "colorField": "color",
        "startDateField": "start",
        "endDateField": "end",
        "dataProvider": [],
        "dataLoader": {
            "url": "/api/bus_days",
            "load": function(loader, chart) { renderBusFilters(chart.dataProvider); }
        },
        "valueScrollbar": {
            // overview of the whole period, below the chart; the day axis stays on top
            "oppositeAxis": false,
            "offset": 30,

            "color": '#000000',

            "type": "date",

            "dateFormats" : ['DD'],

            //        "stackType" : "regular",
            "autoGridCount" :true,
            //"includeHidden" : false,
            // "baseValue": 1,
            //  "strictMinMax" : true,
            "minPeriod": "DD",
            "labelFunction": formatLabel,
            //"autoGridCount": false,
            //  "minorGridEnabled": true,
            ///  "minorTickLength" : 1,
            //    "labelFrequency" : 1,
            //    "tickLength" : 1,
            //   "ignoreAxisWidth" : true,
            //   "autoGridCount": false,
            //"includeAllValues" : true,
            //"autoGridCount" : false,
            //"dateFormats" : [{"period":"DD","format":"MMM DD"}],
            "gridAlpha" : 1,
            "gridColor" : "#000000",
            "minimumDate" : mind,
            "maximumDate" : maxd,
            // "gridCount" : 5,

            dateFormats:[

                {period:'ss',format:'JJ:NN:SS'},
                {period:'mm',format:'JJ:NN'},
                {period:'hh',format:'JJ:NN'},
                {period:'DD',format:'DD'},
                {period:'MY',format:'D'},
                {period:'WW',format:'DD'},
                {period:'MM',format:'MMM'},
                {period:'YYYY',format:'YYYY'}
                ],

            // "minPeriod": "MM DD"
            // 'minimumDate': new Date(2017, 8, 0),
            // 'maximumDate': new Date(2017, 9, 30)
            //  "gridColor": "#FFFFFF",
            //"gridAlpha": 0.2,
            //  "dashLength": 0
        },
        "graph": {
            "fillAlphas": 1,
            //"fillColors": '#ff0000',
            "fillColorsField": "color",
            "bulletSize": 5,
            "bulletAlpha": 0,
            "valueField": "start",
            "lineAlpha": 0.5,
            "showBalloon" : true,
        //    "bullet": "circle",
            "dataDateFormat": "YYYY-MM-DD",
       //     "labelText": "[[tour]] ([[comments]])",
            "labelPosition": "middle",
            //"color": "#ffffff",
            "lineColor" : "#000000",
            "showHandOnHover": true,
            "lineThickness" : 1,
         //   "balloonText": ",
            "balloonFunction": function(item,graph) {
                var city = 'no';

                if(isArray(graph.customData.cities) && graph.customData.cities[graph.customData.city_num]) {
                     city = graph.customData.cities[graph.customData.city_num];
                }else if(!isArray(graph.customData.cities)){
                     city = graph.customData.cities;
                }

                var result = "<i class='fa fa-bus' aria-hidden='true'></i>&nbsp;<span style='font-size:12px; color:#000;'>"+graph.customData.isTour+":"+graph.customData.tour+"</b><br>Bus: "+item.category+"<br>Cities:"+city+"<br><br>Driver:"+graph.customData.driver_name+"<br> Date: "+graph.customData.start+"<br>Comments:"+graph.customData.comments+"</span>";

                return result;
            }
        },
        "categoryAxis": {
            "parseDates": false,
            "labelFunction": function (value) {
                value = String(value);
                return value.length > 28 ? value.slice(0, 27) + '\u2026' : value;
            },
       //     "gridPosition": "start",
        //    "gridAlpha": 0,
            "ignoreAxisWidth": true,
            "autoWrap": false,
          //  "equalSpacing": true,
          //  "gridPosition": "end",
//            "dashLength": 1,
  //          "minorGridEnabled": true
            //"gridPosition": "start",
            //  "autoGridCount": true
            //"gridAlpha": 0,
            // "ignoreAxisWidth": true,
            //  "autoWrap": true
        },
        "marginLeft": 200,

        "chartCursor": {
      //      "cursorPosition": "mouse",
            //"categoryBalloonDateFormat" : "MM DD",
            "cursorColor": "#55bb76",
            "categoryBalloonEnabled" : true,
            "pan": true,
          //  "oneBalloonOnly" : true,
            "valueLineEnabled": true,
            "valueLineBalloonEnabled": true,
            "valueBalloonsEnabled": false,
            "cursorAlpha": 1,
            "valueLineAlpha": 0.5,
            "zoomable": false,
            "bulletsEnabled" :true,
            "valueZoomable": false
        },
        "legend": {
            "divId": "leggend",
            "data":  leggend_array
        },
        "listeners": [{
            "event": "changed",
            "method": function(event) {
                chart2.cursorDataContext = event.chart.dataProvider[event.index];
                chart2.flag = false;
                //e.chart.lastCursorPosition = e.index;
            }
        },
        {
            "event": "rendered",
            "method": function(event) {
                event.chart.chartCursor.addListener('moved', function (e) {
                    var axis = event.chart.valueAxis;
                    var value = axis && axis.coordinateToValue ? axis.coordinateToValue(e.x) : null;
                    chart2.cursorDate = value ? new Date(value) : null;
                });
            }
        },
        {
                "event": "rendered",
                "method": function(event) {

                    $('#help').off('.busCalendar').on('click.busCalendar', function() {
                        $('#filter_block').hide();
                        $('#leggend').css({left: 'auto', right: '0', opacity: 1}).toggle();
                    });
                    $('#filter').off('.busCalendar').on('click.busCalendar', function() {
                        $('#leggend').hide();
                        $('#filter_block').css({left: 'auto', right: '0', opacity: 1}).toggle();
                    });

                    event.chart.valueAxis.zoomToValues(new Date(mind), new Date(lastd));

                    event.chart.chartDiv.addEventListener('click', myFF);
                }
            },
            {
                "event": "dataUpdated",
                "method": function(event) {

                }
            }


            ],


        "export": {
            "enabled": false
        }
    });

    chart2.addListener("clickGraphItem", handleClick_bus);




    /*
    $('#modalCreateBus').on('hidden.bs.modal', function () {

    })*/
}

function clearFields() {
    $('#drivers_tour, #drivers_trip').val(null).trigger('change');
    $('#buses_tour').val(null).trigger('change');
}

function resetForm() {
    $('#modalCreateBusAdd').find('#start_date').datepicker('update', '');
    $('#modalCreateBusAdd').find('#end_date').datepicker('update', '');
    $('#modalCreateBusAdd').find('#name_trip').val('');
    $('#modalCreateBusAdd').find('.block-error-driver, .error_date').hide().text('');

    $('#leggend, #filter_block').hide();
}

function isArray(array) {
    if ( toString.call(array) === "[object Array]") {
        return true;
    }
    return false;
}

function handleClick_bus(event) {
    let trip_edit_permission = $('#trip_edit_permission').attr('data-info');
    $('#modalUpdateTrip .trip_update').empty();
    $('#update_btn_table_bus').prop('disabled', true);
    $('.block-error-driver').text('');
    $('.block-error-driver').css({'display': 'none'});
    console.log(event.graph.customData);
    chart2.flag = true;
    config.tour_id = event.graph.customData.tour_id;
    config.name = event.graph.customData.tour;
    config.transfer_id = event.graph.customData.transfer_id;
    config.tour_package_id = event.graph.customData.tour_package_id;
    config.date = event.graph.customData.start;
    config.id_bus_day = event.graph.customData.id;

    setTimeout(function () {
        if(config.tour_package_id === null){
            $.ajax({
                method: "GET",
                url: `/api/generate_form_trip`,
                data: {
                    'bus_day_id' : config.id_bus_day
                }
            }).done((res) => {
                $('.trip_update').html(res);
                $('#update_btn_table_bus').prop('disabled', false);
            });
        }

        else{
            $.ajax({
                method: "GET",
                url: `/api/generate_form_tour`,
                data: {
                    'tour_package_id' : config.tour_package_id,
                    'transfer_id' : config.transfer_id,
                    'bus_day_id' : config.id_bus_day
                }
            }).done((res) => {
                $('.trip_update').html(res);
                $('#update_btn_table_bus').prop('disabled', false);
            });
        }
    }, 300);

    $('#modalUpdateTrip').find('#default_reference_id').val(event.graph.customData.id);
    $('#modalUpdateTrip').find('#modalUpdateTripLabel').text("Edit day " + event.graph.customData.start);

    setTimeout(function (e) {
        if(!trip_edit_permission){
            $('#modalNoPermission').modal('show');
        }else{
            $('#modalUpdateTrip').modal('show');
        }
    }, 200);

    commentbus.init();

    resetForm();

    tour_reset();
}

function myFF(event) {
    let create_permission_trip = $('#trip_create_permission').attr('data-info');
    var target = event.target || event.toElement;
    if(!chart2.flag && chart2.cursorDataContext && target && target.nodeName === 'path') {

        if(!create_permission_trip){
            $('#modalNoPermission').modal('show');
            return;
        }

        openBusTripForm();

        // pre-fill the clicked day and bus
        if (chart2.cursorDate) {
            var clicked = moment(chart2.cursorDate).format('YYYY-MM-DD');
            $('#modalCreateBusAdd').find('#start_date').datepicker('update', clicked);
            $('#modalCreateBusAdd').find('#end_date').datepicker('update', clicked);
        }
        var busId = chart2.cursorDataContext.bus_id;
        if (busId && $('#bus_trip option[value="' + busId + '"]').length) $('#bus_trip').val(String(busId)).trigger('change');

        //value Y
        // console.log(chart.cursorDataContext.category);
    }


}

function formatLabel(value, valueString, axis){
    chart2.val = value;
    if(value === '2018'){
        axis.x = 10;
    }
    return value;
}

function serializeDeleteItem(strSerialize, strParamName)
{
    var arrSerialize = strSerialize.split("&");
    var i = arrSerialize.length;

    while (i--) {
        if (arrSerialize[i].indexOf(strParamName+"=") == 0) {
            arrSerialize.splice(i,1);
            break;
        }
    }

    return arrSerialize.join("&");
}

function tour_reset() {

    $('#modalCreateBusAdd').find('#start_date').val('').datepicker("remove");
    $('#modalCreateBusAdd').find('#start_date').datepicker({
        format: 'yyyy-mm-dd',
        autoclose : true,
        startDate: null,
        endDate: null
    });

    $('#modalCreateBusAdd').find('#end_date').val('').datepicker("remove");
    $('#modalCreateBusAdd').find('#end_date').datepicker({
        format: 'yyyy-mm-dd',
        autoclose : true,
        startDate: null,
        endDate: null
    });

}

function tour_check() {
    var tourOption = $('#tour option:selected');
    var departure_date = tourOption.data('dep') || null;
    var retirement_date = tourOption.data('ret') || null;
    $('#modalCreateBusAdd').find('#start_date').val('').datepicker("remove");
    $('#modalCreateBusAdd').find('#start_date').datepicker({
        format: 'yyyy-mm-dd',
        autoclose : true,
        startDate: departure_date,
        endDate: retirement_date
    });

    $('#modalCreateBusAdd').find('#end_date').val('').datepicker("remove");
    $('#modalCreateBusAdd').find('#end_date').datepicker({
        format: 'yyyy-mm-dd',
        autoclose : true,
        startDate: departure_date,
        endDate: retirement_date
    });

}


function createTransferInTour() {
    $('.block-error-driver').text('');
    $('.block-error-driver').css({'display': 'none'});
    $('#modalCreateBusAdd').find('.error_date').html(' ');
    $('#modalCreateBusAdd').find('.error_date').css({'display':'none'});


    if($('#tour').val() === null){
        $('#modalCreateBusAdd').find('.error_date').css({'display':'block'});
        $('#modalCreateBusAdd').find('.error_date').append('Select tour');
        return false;
    }else if($('#transfer_tour').val() === null){
        $('#modalCreateBusAdd').find('.error_date').css({'display':'block'});
        $('#modalCreateBusAdd').find('.error_date').append('Select transfer');
        return false;
    }

    if(($('.datepicker_bus_day_dep').val() === '') || ($('.datepicker_bus_day_ret').val() === '')){
        $('#modalCreateBusAdd').find('.error_date').css({'display':'block'});
        $('#modalCreateBusAdd').find('.error_date').append('Enter the date');
        return false;
    }

    if($('.datepicker_bus_day_dep').val() > $('.datepicker_bus_day_ret').val()){
        $('#modalCreateBusAdd').find('.error_date').css({'display':'block'});
        $('#modalCreateBusAdd').find('.error_date').append('Start date must be on or before end date.');
        return false;
    }

    $(document).find('#modalCreateBusAdd').find('.overlay').css({'display' : 'block'});


    let oldForm = document.forms.add_day;
    let form = new FormData(oldForm);
    let bus_id = form.get('bus_tour');
    let drivers_id = form.getAll('drivers_tour[]');
    let tour_id = form.get('tour');
    let service_id = form.get('transfer_tour');
    let service_type = 'transfer';
    let service_name = $('#transfer_tour option:selected').data('service_name');
    let dep_date = form.get('start_date');
    let ret_date = form.get('end_date');

    $.ajax({
        method: 'POST',
        url: '/tour_package',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            tourId: tour_id,
            serviceType: service_type,
            serviceId: service_id,
            serviceName: service_name,
            drivers_id: drivers_id,
            bus_id: bus_id,
            dep_date_transfer: dep_date,
            ret_date_transfer: ret_date
        }
    }).fail((xhr) => {
        $('#modalCreateBusAdd .overlay').hide();
        var errors = xhr.responseJSON && xhr.responseJSON.errors;
        var message = errors ? Object.values(errors).flat().join(' ') : (xhr.responseJSON && xhr.responseJSON.message) || 'Unable to add the transfer. Please try again.';
        $('#modalCreateBusAdd .block-error-driver').text(message).show();
    }).done((res) => {
        $(document).find('#modalCreateBusAdd').find('.overlay').css({'display' : 'none'});

        if(res.bus_busy){
            $('.block-error-driver').text('');
            $('.block-error-driver').append('<span>'+res.bus_busy_message+'</span>');
            $('.block-error-driver').css({'display': 'block'});
        }
        else if(res.transfer_add_date){
            $('.block-error-driver').text('');
            $('.block-error-driver').append('<span>'+res.transfer_message+'</span>');
            $('.block-error-driver').css({'display': 'block'});
        }
        else{
            $('#modalCreateBusAdd').modal('hide');
            $('.block-error-driver').text('');
            $('.block-error-driver').css({'display': 'none'});
            $('.clear_input').val('');
            notifyBus('Transfer added to the tour', 'success');
            reloadBusChart();
        }
    })
}

function notifyBus(message, type) {
    var toast = window.appToast || window.showToast;
    if (typeof toast === 'function') toast(message, type);
}

// Rebuild the chart and keep the period the user was looking at
function reloadBusChart() {
    var axis = chart2 && chart2.valueAxis;
    if (axis && axis.minZoom && axis.maxZoom) {
        mind = new Date(axis.minZoom);
        lastd = new Date(axis.maxZoom);
    }
    chart2.clear();
    chart2 = null;
    createGraph();
}

function initDateModal() {
    $('.block-error-driver').text('');
    $('.block-error-driver').css({'display': 'none'});

    $(document).find('#add_day').on('submit',function(e){
        $('#modalCreateBusAdd').find('.error_date').html(' ');
        $('#modalCreateBusAdd').find('.error_date').css({'display':'none'});

        if(($('.datepicker_bus_day_dep').val() === '') || ($('.datepicker_bus_day_ret').val() === '')){
            $('#modalCreateBusAdd').find('.error_date').css({'display':'block'});
            $('#modalCreateBusAdd').find('.error_date').append('Enter the date');
            return false;
        }

        if($('.datepicker_bus_day_dep').val() > $('.datepicker_bus_day_ret').val()){
            $('#modalCreateBusAdd').find('.error_date').css({'display':'block'});
            $('#modalCreateBusAdd').find('.error_date').append('Start date must be on or before end date.');
            return false;
        }


        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content')
            }
        });

        e.preventDefault(e);

        form_array = $(this).serialize();

        if( mode == 1 ) {
            form_array = serializeDeleteItem(form_array,'tour_bus');
        }else{
            form_array = serializeDeleteItem(form_array,'name');
        }

        $(document).find('#modalCreateBusAdd').find('.overlay').css({'display' : 'block'});
        $.ajax({

            type: "POST",
            url: '../api/add_day',
            data: form_array,
            //dataType: 'json',
            success: function(data){
                // field clearing , after save trip or tour
                $(document).find('#modalCreateBusAdd').find('.overlay').css({'display' : 'none'});

                if(data.bus_busy){
                    $('.block-error-driver').text('');
                    $('.block-error-driver').append('<span>'+data.bus_busy_message+'</span>');
                    $('.block-error-driver').css({'display': 'block'});

                }else{
                    $('.clear_input').val('');

                    $('#modalCreateBusAdd').modal('hide');

                    notifyBus('Trip added', 'success');
                    reloadBusChart();

                    $('.block-error-driver').text('');
                    $('.block-error-driver').css({'display': 'none'});
                }

            },
            error: function(xhr){
                $('#modalCreateBusAdd .overlay').hide();
                var errors = xhr.responseJSON && xhr.responseJSON.errors;
                var message = errors ? Object.values(errors).flat().join(' ') : 'Unable to save the trip. Please try again.';
                $('#modalCreateBusAdd .block-error-driver').text(message).show();
            }
        })

    });


    $(document).find('#update_btn_table_bus').on('click', function (e) {
        $('.block-error-driver').text('');
        $('.block-error-driver').css({'display': 'none'});
        $(document).find('#modalUpdateTrip').find('.overlay').css({'display' : 'block'});

        if (!document.forms.form_update_bus_table_trip && !document.forms.form_update_bus_table_tour) {
            $(document).find('#modalUpdateTrip').find('.overlay').css({'display' : 'none'});
            return;
        }
        let check = typeof document.forms.form_update_bus_table_trip === 'undefined' ? 'TOUR' : 'TRIP';

        // update Trip
        if(check === 'TRIP'){
            updateTrip(document.forms.form_update_bus_table_trip);
        }

        // update Tour
        else if(check === 'TOUR'){
            updateTour(document.forms.form_update_bus_table_tour);
        }
    });
}

function deleteTrip() {

    let oldForm = document.forms.form_update_bus_table_trip;
    let form = new FormData(oldForm);
    var bus_day_id = form.get('bus_day_id');
    var trip_mode = form.get('trip_mode');
    var trip_id = form.get('trip_id');

    $.ajax({
        url: '/bus_day/delete',
        method: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            bus_day_id: bus_day_id,
            trip_mode: trip_mode,
            trip_id: trip_id
        }
    }).done((res) => {
        notifyBus('Trip deleted', 'success');
        reloadBusChart();
        $('#modalUpdateTrip').modal('hide');
    }).fail(() => {
        notifyBus('Unable to delete the trip', 'error');
    });
}

function updateTrip(oldForm) {
    let form = new FormData(oldForm);
    var name_trip = form.get('name_update_trip');
    var bus_day_id = form.get('bus_day_id');
    var trip_id = form.get('trip_id');
    var trip_mode = form.get('trip_mode');
    var status = form.get('bus_status_update_trip');
    var country = form.get('country_begin');
    var city_begin_code = form.get('city_begin_code');
    var city_begin = form.get('city_begin');
    var bus_id_trip = form.get('bus_trip_update');
    var drivers_id_trip = form.getAll('driver_trip_update[]');
    var elm = document.getElementById("driver_trip_update");
    var arr = [];
    for (var i = 0; i < elm.length; i++) {
        arr.push(elm.options[i].value);
    }
    var date = $('#modalUpdateTrip').find('#modalUpdateTripLabel').text().replace(/Edit day/g,'');

    $.ajax({
        method: 'POST',
        url: '/bus_day/update',
        data: {
            tour_id : null,
            transfer_id : null,
            status : status,
            date : date,
            bus_id_trip : bus_id_trip,
            drivers_id_trip : drivers_id_trip,
            city_begin : city_begin,
            city_begin_code : city_begin_code,
            country_begin: country,
            name_trip: name_trip,
            id_bus_day : bus_day_id,
            trip_mode : trip_mode,
            trip_id : trip_id,
            alldrv : arr
        }
    }).done((data) => {
        $(document).find('#modalUpdateTrip').find('.overlay').css({'display' : 'none'});
        if(data.bus_busy){
            $('.block-error-driver').text('');
            $('.block-error-driver').append('<span>'+data.bus_busy_message+'</span>');
            $('.block-error-driver').css({'display': 'block'});
        }
        else{
            notifyBus('Changes saved', 'success');
            reloadBusChart();
            $('#modalUpdateTrip').modal('hide');
            $('.block-error-driver').text('');
            $('.block-error-driver').css({'display': 'none'});
        }
    }).fail((xhr) => {
        $('#modalUpdateTrip .overlay').hide();
        var errors = xhr.responseJSON && xhr.responseJSON.errors;
        var message = errors ? Object.values(errors).flat().join(' ') : 'Unable to save the changes. Please try again.';
        $('#modalUpdateTrip .block-error-driver').text(message).show();
    });
}

function updateTour(oldForm) {
    let form = new FormData(oldForm);
    var bus_day_id = form.get('bus_day_id');
    var tour_mode = form.get('tour_mode');
    var tour_package_id = form.get('tour_package_id_bus_day');
    var status = form.get('bus_status_update_tour');
    var bus_id_tour = form.get('bus_tour_update');
    var drivers_id_tour = form.getAll('drivers_tour_update[]');

    $.ajax({
        method: 'POST',
        url: '/bus_day/update/tour',
        data: {
            status : status,
            bus_id : bus_id_tour,
            drivers_id : drivers_id_tour,
            id_bus_day : bus_day_id,
            tour_package_id : tour_package_id,
            tour_mode : tour_mode
        }
    }).done((data) => {
        $(document).find('#modalUpdateTrip').find('.overlay').css({'display' : 'none'});
        if(data.bus_busy){
            $('.block-error-driver').text('');
            $('.block-error-driver').append('<span>'+data.bus_busy_message+'</span>');
            $('.block-error-driver').css({'display': 'block'});
        }
        else{
            notifyBus('Changes saved', 'success');
            reloadBusChart();
            $('#modalUpdateTrip').modal('hide');
            $('.block-error-driver').text('');
            $('.block-error-driver').css({'display': 'none'});
        }
    }).fail((xhr) => {
        $('#modalUpdateTrip .overlay').hide();
        var errors = xhr.responseJSON && xhr.responseJSON.errors;
        var message = errors ? Object.values(errors).flat().join(' ') : 'Unable to save the changes. Please try again.';
        $('#modalUpdateTrip .block-error-driver').text(message).show();
    });
}


function generateObject(){

    var out_obj = [];

    for(var i=0 ; i < spliced.length; i++){
        delete obj[ spliced[i] ];
    }

    for(var i=0 ; i < obj.length; i++){
        if(obj[i]) { out_obj.push(obj[i]);}
    }

    var out = JSON.stringify(out_obj);

    chart2.dataProvider = AmCharts.parseJSON(out);
    chart2.validateData();
}

function renderBusFilters(data) {
    loaded_data = JSON.stringify(data);
    obj = data;
    spliced = [];
    var panel = $('#filter_block').empty();
    var all = $('<input type="checkbox" class="bus_filter_all" checked>').attr('aria-label', 'All buses');
    $('<label class="d-flex gap-2 align-items-center mb-2"></label>').append(all, document.createTextNode('All buses')).appendTo(panel);
    data.forEach(function(bus, index) {
        var input = $('<input type="checkbox" class="bus_filter" checked>').val(index);
        $('<label class="d-flex gap-2 align-items-center mb-2"></label>').append(input, document.createTextNode(String(bus.category))).appendTo(panel);
    });
    panel.find('input').on('change', function() {
        if ($(this).hasClass('bus_filter_all')) panel.find('.bus_filter').prop('checked', this.checked);
        var selected = panel.find('.bus_filter:checked').map(function() { return Number(this.value); }).get();
        all.prop('checked', selected.length === data.length);
        chart2.dataProvider = data.filter(function(bus, index) { return selected.includes(index); });
        chart2.validateData();
    });
}

function openBusTripForm() {
    resetForm();
    tour_reset();
    clearFields();
    $('#modalCreateBusAdd .trip_select input').prop('checked', true);
    if (typeof showBusMode === 'function') {
        showBusMode(false);
    } else {
        $('#modalCreateBusAdd .trip_field, #modalCreateBusAdd .trip_btn').show();
        $('#modalCreateBusAdd .tour_field, #modalCreateBusAdd .tour_btn').hide();
    }
    $('#modalCreateBusAdd').modal('show');
}

$(function () {
    createGraph();
    initDateModal();
});
