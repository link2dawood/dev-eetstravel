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
var mind = new Date(yeard + "-" + monthd + "-" +'01');
var lastd = new Date(yeard + "-" + monthd + "-" +'31');
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

        $(modal_id).find('#author_name').css({'display': 'table-cell'});
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
        "startDuration" : 1,
        "period": "DD",
        "dataDateFormat": "YYYY-MM-DD",
        "balloonDateFormat": "JJ:NN, DD MMMM",
        "mouseWheelZoomEnabled": false,
        "columnWidth": 0.5,
        "valueAxis": {
            "type": "date",
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
            "url": "../api/bus_days"
        },
        "valueScrollbar": {

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
       //     "gridPosition": "start",
        //    "gridAlpha": 0,
            "ignoreAxisWidth": true,
            "autoWrap": true,
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
        "marginLeft": 150,

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

                    $('#help').hover(
                        function() {
                            $('#leggend').css({"left":"80%"});
                                $('#leggend').fadeTo( "fast", 1 ,function() {
                            });
                            $('#filter_block').fadeTo( "fast", 0,function() {
                                $('#filter_block').css({"left":"-80%"});
                            });
                        }, function() {
                            $('#leggend').fadeTo( "fast", 0,function() {
                                $('#leggend').css({"left":"-80%"});
                            });
                        });

                    $('#filter').hover(
                        function() {
                            $('#filter_block').css({"left":"85%"});
                            $('#filter_block').fadeTo( "fast", 1 ,function() {
                            });
                            $('#leggend').fadeTo( "fast", 0,function() {
                                $('#leggend').css({"left":"-85%"});
                            });
                        }, function() {
                            //$('#filter_block').fadeTo( "fast", 0,function() {
                              //  $('#filter_block').css({"left":"-80%"});
                            //});
                        });

                    $('#filter_block').hover(
                        function() {
                            $(this).css({"left":"85%"});
                            $(this).fadeTo( "fast", 1 ,function() {
                            });
                        }, function() {
                            $(this).fadeTo( "fast", 0,function() {
                                $(this).css({"left":"-85%"});
                            });
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


    $('#drivers_tour').select2("val", " ");
    $('#drivers_trip').select2("val", " ");
    $('#buses_tour').select2("val", " ");
}

function resetForm() {
    $('#modalCreateBusAdd').find('#start_date').datepicker('update', '');
    $('#modalCreateBusAdd').find('#end_date').datepicker('update', '');
    $('#modalCreateBusAdd').find('#name').val('');

    $('#leggend').fadeTo( "fast", 0,function() {
        $('#leggend').css({"left":"-80%"});
    });


    $('#filter_block').fadeTo( "fast", 0,function() {
        $('#filter_block').css({"left":"-80%"});
    });
}

function isArray(array) {
    if ( toString.call(array) === "[object Array]") {
        return true;
    }
    return false;
}

function handleClick_bus(event) {
    let trip_edit_permission = $('#trip_edit_permission').attr('data-info');
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
            });
        }
    }, 300);

    $('#modalUpdateTrip').find('#default_reference_id').val(event.graph.customData.id);
    $('#modalUpdateTrip').find('#modalUpdateTripLabel').text("Edit day " + event.graph.customData.start);

    setTimeout(function (e) {
        if(!trip_edit_permission){
            $('#modalNoPermission').modal();
        }else{
            $('#modalUpdateTrip').modal();
        }
    }, 200);

    commentbus.init();

    resetForm();

    tour_reset();
}

function myFF(event) {
    let create_permission_trip = $('#trip_create_permission').attr('data-info');
    if(!chart2.flag && chart2.cursorDataContext && event.toElement.nodeName === 'path') {

        resetForm();

        var momentString = moment(chart2.val,"MMM DD").format("MM-DD");
        var year =(new Date()).getFullYear();
        var full_date = year + "-" + momentString;

        $('#modalCreateBusAdd').find('#start_date').datepicker('update', full_date);

        if( mode !== 1 ) {
            $("input[type='radio'].minimal").iCheck('uncheck'); //To uncheck the radio button
            $("input[type='radio'].minimal").filter('[value="1"]').iCheck('uncheck');
            $("input[type='radio'].minimal").filter('[value="0"]').iCheck('check');
            $("input[type='radio'].minimal").iCheck('update');
        }

        tour_reset();

        $(document).find('.tours_bus').addClass('hidden');
        $(document).find('.name_bus').removeClass('hidden');

        if(!create_permission_trip){
            $('#modalNoPermission').modal();
        }else{
            $('#modalCreateBusAdd').modal();
        }

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
    var departure_date =  $("#tour").select2('data')[0].element.dataset.dep;
    var retirement_date =  $("#tour").select2('data')[0].element.dataset.ret;
    console.log(departure_date);
    console.log(retirement_date);
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
        $('#modalCreateBusAdd').find('.error_date').append('Date from can not be less than the date to');
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
    let service_name = $("#transfer_tour").select2('data')[0].element.dataset.service_name;
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
            chart2.clear();
            chart2 = null;

            createGraph();
        }
    })
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
            $('#modalCreateBusAdd').find('.error_date').append('Date from can not be less than the date to');
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

                    chart2.clear();
                    chart2 = null;

                    createGraph();

                    $('.block-error-driver').text('');
                    $('.block-error-driver').css({'display': 'none'});
                }

            },
            error: function(data){

            }
        })

    });


    $(document).find('#update_btn_table_bus').on('click', function (e) {
        $('.block-error-driver').text('');
        $('.block-error-driver').css({'display': 'none'});
        $(document).find('#modalUpdateTrip').find('.overlay').css({'display' : 'block'});

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
        chart2.clear();
        chart2 = null;
        createGraph();
        $('#modalUpdateTrip').modal('hide');
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
            chart2.clear();
            chart2 = null;
            createGraph();
            $('#modalUpdateTrip').modal('hide');
            $('.block-error-driver').text('');
            $('.block-error-driver').css({'display': 'none'});
        }
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
            chart2.clear();
            chart2 = null;
            createGraph();
            $('#modalUpdateTrip').modal('hide');
            $('.block-error-driver').text('');
            $('.block-error-driver').css({'display': 'none'});
        }
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

$(function () {
    createGraph();
    initDateModal();

    if(!loaded_data) {

        AmCharts.loadFile('../api/bus_days', {}, function (data) {
            loaded_data = data;
            obj = $.parseJSON(data);
            var html ="<div class='container-fluid' style='margin: 10px;'>";


            html += "<div class='row' style='margin-bottom: 2px;'><div class='col-sm-1'><input type='checkbox' class='bus_filter_all' value='set_all'></div><div class='col-sm-9'>All</div></div>";

            for(var i = 0; i<obj.length; i++){
                html += "<div class='row' style='margin-bottom: 2px;'><div class='col-sm-1'><input type='checkbox' class='bus_filter' value='"+i+"' checked></div><div class='col-sm-9'>"+obj[i].category+"</div></div>";
            }

            html += "</div>";

            $(document).find('#filter_block').html(html);


            $('.bus_filter_all').iCheck({
                checkboxClass: 'icheckbox_minimal-blue',
                radioClass   : 'iradio_minimal-blue'
            }).on('ifClicked', function(e) {

                filter_all = true ;

                $(this).on('ifUnchecked', function(event) {

                    if (!filter_all)  return;

                        spliced = [];
                        obj = $.parseJSON(loaded_data);

                        $('.bus_filter').each(function (index, element) {
                            $(element).iCheck('uncheck');
                            spliced[$(element).val()] = $(element).val();
                        });

                        generateObject();



                });

                $(this).on('ifChecked', function(event){

                    if(!filter_all) return;

                        spliced = [];
                        obj = $.parseJSON(loaded_data);

                        $('.bus_filter').each(function (index, element) {
                            $(element).iCheck('check');
                        });

                        generateObject();


                });

            });


            $('.bus_filter').iCheck({
                checkboxClass: 'icheckbox_minimal-blue',
                radioClass   : 'iradio_minimal-blue'
            }).on('ifClicked', function(e) {

                filter_all = false;

                $('.bus_filter_all').iCheck('uncheck');

                obj = $.parseJSON(loaded_data);


                $(this).on('ifUnchecked', function(event){

                    if(!spliced[event.target.value]) {
                        spliced[event.target.value] = event.target.value;
                    }

                    generateObject();

                });

                $(this).on('ifChecked', function(event){

                    if(spliced[event.target.value]) {
                        delete spliced[event.target.value];
                    }

                    generateObject();

                });

            });


        });



    }
});
