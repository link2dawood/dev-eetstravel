var dashboard = {
    init: function () {
        dashboard.getTasksBlock();
        dashboard.config = {
            calendar: $('#calendar'),
            countLiError: $('#modalCreate1 .alert li'),
            calendarTasks: $('#calendarTasks'),
            urlGetToursForCalendar: "./home/getToursForCalendar",
            urlToursTasksForCalendar: "./home/getToursTasksForCalendar",
            urlEventsForCalendar: "/calendar/list",
            API_KEY_GOOGLE_CALENDAR: $('#API_KEY_google_calendar').attr('data-info'),
            AllHollydayCalendars:[],
            calendarHollydays:[
//                {
//                    googleCalendarId: 'ru.austrian#holiday@group.v.calendar.google.com',
//                    color: 'purple',
//                    allDay: true,
//                },
//                {
//                    googleCalendarId: 'ru.hungarian#holiday@group.v.calendar.google.com',
//                    color: 'orange',
//                    allDay: true,
//                },
//                {
//                    googleCalendarId: 'ru.german#holiday@group.v.calendar.google.com',
//                    color: 'blue',
//                    allDay: true,
//                },
//                {
//                    googleCalendarId: 'ru.china#holiday@group.v.calendar.google.com',
//                    color: 'green',
//                    allDay: true,
//                }
            ]
        };
        dashboard.getAllHollydayCalendars();
        dashboard.config.calendar.fullCalendar({
            events: dashboard.config.urlGetToursForCalendar,
            customButtons: {
                addTourButton: {
                    text: 'Add tour',
                    click: function() {
                        console.log('modalCreateTour')
                        console.log($('#modalCreateTour').modal())
                        $('#modalCreateTour').modal();
                    }
                }
            },
            header: {
                left:   'title',
                center: '',
                right:  'addTourButton today prev,next month listDay'
            },
            buttonText: {
                list: 'day'
            },
            eventLimitClick: "day",
            views: {
                month: {
                }
            },
            viewRender : function(view, element) {
                console.log(view, element);
            },
            editable: true,
            selectable: true,
            eventClick: (event, jsEvent, view) => {
                window.location = '/tour/'+event.id+'/edit?calendar_edit=1';
            },
            dayClick(date, jsEvent, view){
                $('#calendar').fullCalendar('changeView', 'listDay', date.format());
            },
 /*           eventDrop: function(event, delta){
                var type = $('#calendar').fullCalendar('getView').name;
                var start_date = $.fullCalendar.moment(event.start);
                var end_date = $.fullCalendar.moment(event.end);
                if (type === 'month'){
                    end_date = end_date.add(-1, 'days');
                }
                $.ajax({
                    url: 'tour/'+event.id+'/updateCalendar',
                    data: {
                        'start_date' : start_date.format('YYYY-MM-DD'),
                        'end_date': end_date.format('YYYY-MM-DD'),
                    },
                    type: "POST",
                    success: function(json) {
                        console.log(json);
                    }
                });
            },
            eventRender: function (event, element, view) {

                $(element).each(function () {
                    $(this).attr('date-num', event.start.format('YYYY-MM-DD'));
                    for (day = event.start.clone(); day.isBefore(event.end); day.add(1, 'day'))
                    {
                        let dayStr = day.format('YYYY-MM-DD');
                        $(this).addClass('event-date-'+dayStr);
                    }

                });
                if(view.type == 'month') {
                    $(element).css("display", "none");
                }
            },
            eventAfterAllRender: function(view){
                for( cDay = view.start.clone(); cDay.isBefore(view.end) ; cDay.add(1, 'day') ){
                    var dateNum = cDay.format('YYYY-MM-DD');
                    var dayEl = $('#calendar .fc-day[data-date="' + dateNum + '"]');
                    var eventCount = $('.fc-event.fc-start.event-date-' + dateNum).length;
                    if(eventCount){
                        var html = '<small class="label event-count">' +
                            '<span class="badge bg-green">' +
                            eventCount +
                            '</span>' +
                            '</small>';

                        dayEl.append(html);
                    }
                }
            }
        ,
            eventResize: function(event,dayDelta,minuteDelta,revertFunc) {
                var type = $('#calendar').fullCalendar('getView').name;
                var start_date = $.fullCalendar.moment(event.start);
                var end_date = $.fullCalendar.moment(event.end);
                if (type === 'month'){
                    end_date = end_date.add(-1, 'days');
                }
                $.ajax({
                    url: 'tour/'+event.id+'/updateCalendar',
                    data: {
                        'start_date' : start_date.format('YYYY-MM-DD'),
                        'end_date': end_date.format('YYYY-MM-DD'),
                        'c_type' : type
                    },
                    type: "POST",
                    success: function(json) {
                        console.log(json);
                    }
                });
            },*/
        });
//        dashboard.generateCalendar();
    },

    generateCalendar: () => {
//console.log(dashboard.config.urlToursTasksForCalendar);
//console.log(dashboard.config.calendarHollydays);
        let task_permission = $('#task_create_permission').attr('data-info');
        let holiday_permission = $('#holiday_list_permission').attr('data-info');
        dashboard.config.calendarTasks.fullCalendar({
            googleCalendarApiKey: dashboard.config.API_KEY_GOOGLE_CALENDAR,
            events: dashboard.config.urlToursTasksForCalendar,
            eventSources: dashboard.config.calendarHollydays,
            customButtons: {
                addHolildayButton: {
                    text: 'Holidays',
                    click: function() {
                        window.location.assign("/holiday")
                    }
                },
                addTaskButton: {
                    text: 'Add task',
                    click: function() {
                        $('#modalCreate1').modal();
                    }
                }
            },
            header: {
                left:   'title',
                center: '',
                right:  `${holiday_permission ? 'addHolildayButton' : '' } ${task_permission ? 'addTaskButton' : ''} today prev,next month agendaDay`,
            },
            allDaySlot: true,
            eventLimit : true,
            eventLimitClick: "day",
            defaultTimedEventDuration: "00:30:00",
            eventDurationEditable : false,
            agenda: {
                eventLimit: 12   // 4
            },
            editable: true,
            selectable: true,
            eventClick: (event, jsEvent, view) => {
                if (event.url) {
                    return false;
                }
                if (event.id != 'Holiday'){
                    window.location = '/task/'+event.id+'/edit?calendar_edit=1';
                }    
            },
            dayClick(date, jsEvent, view){
                $('#calendarTasks').fullCalendar('changeView', 'agendaDay', date.format());
            },
            eventDrop: function(event, delta){
                console.log(event.source.googleCalendarId);
                if(event.source.googleCalendarId){
                    return false;
                }
                var type = $('#calendarTasks').fullCalendar('getView').name;
                const date = event.start.format('YYYY-MM-DD');
                const date_time = event.start.format('HH:mm');

                $.ajax({
                    url: 'task/'+event.id+'/updateCalendar',
                    data: {
                        'date' : date,
                        'date_time' : date_time,
                    },
                    type: "POST",
                    success: function(json) {
                        console.log(json);
                    }
                });
            }
        });
    },

    showModalError: function () {
        if(dashboard.config.countLiError.length > 0){
            $('#modalCreate1').modal('show');
        }
    },


    getTasksBlock: function(){
        $.ajax({
            url: '/task/getTasksBlock',
            type: "GET",
            success: function(data) {
				document.getElementById("tasksBlock").innerHTML ;
                $('#tasksBlock').html("<h1>Data</h1>"+data);
				console.log(data);
            }
        });
    },

    getAllHollydayCalendars: function(){
        $.ajax({
            url: '/getallhollydaycalendars',
            type: "GET",
            success: function(data) {
                dashboard.config.AllHollydayCalendars = $.parseJSON(data);

                dashboard.config.calendarHollydays = [];
                for(var i = 0; i<dashboard.config.AllHollydayCalendars.length; i++){

                    if (dashboard.config.AllHollydayCalendars[i].checked == true) {
                        dashboard.config.calendarHollydays.push({
                            googleCalendarId: dashboard.config.AllHollydayCalendars[i].googlecalendarid,
                            color: dashboard.config.AllHollydayCalendars[i].color,
                            allDay: dashboard.config.AllHollydayCalendars[i].allday,
                        });
                    }
                }
                dashboard.getHollydayCalendarsBlock();
                dashboard.config.calendarTasks.fullCalendar('destroy');
                dashboard.generateCalendar();
            }
        });    
    },


    getHollydayCalendarsBlock: function(){

        var allChecked = dashboard.config.AllHollydayCalendars.length == dashboard.config.calendarHollydays.length ? 'checked' : '';
                
        var html ="<div class='container-fluid' style='margin: 10px;'>";
        
            html += "<div class='row' style='margin-bottom: 2px;'><div class='col-sm-1'><input type='checkbox' class='calendar_filter_all' value='set_all' "+allChecked+"></div><div class='col-sm-9'>All</div></div>";

            for(var i = 0; i<dashboard.config.AllHollydayCalendars.length; i++){
                var isChecked = dashboard.config.AllHollydayCalendars[i].checked == true ? 'checked' : '';
                html += "<div class='row' style='margin-bottom: 2px;'><div class='col-sm-1'><input type='checkbox' class='calendar_filter' value='"+i+"' "+isChecked+"></div><div class='col-sm-9' style='color:"+dashboard.config.AllHollydayCalendars[i].color+"'>"+dashboard.config.AllHollydayCalendars[i].name+"</div></div>";
            }

            html += "</div>";

            $(document).find('#calendar_filter_block').html(html);
            
            $('.calendar_filter_all').iCheck({
                checkboxClass: 'icheckbox_minimal-blue',
                radioClass   : 'iradio_minimal-blue'
            }).on('ifClicked', function(e) {

                filter_all = true ;

                $(this).on('ifUnchecked', function(event) {
                    if (!filter_all)  return;
                    $.ajax({
                        url: '/checkHollydayCalendarById/all',
                        data: {
                            'checked' : 0
                        },
                        type: "POST",
                        success: function(json) {
                            dashboard.getAllHollydayCalendars();
                        }
                    });
                });

                $(this).on('ifChecked', function(event){
                    if(!filter_all) return;
                        $.ajax({
                            url: '/checkHollydayCalendarById/all',
                            data: {
                                'checked' : 1
                            },
                            type: "POST",
                            success: function(json) {
                                dashboard.getAllHollydayCalendars();
                            }
                        });
                });

            });


            $('.calendar_filter').iCheck({
                checkboxClass: 'icheckbox_minimal-blue',
                radioClass   : 'iradio_minimal-blue'
            }).on('ifClicked', function(e) {
                filter_all = false;

                $('.calendar_filter_all').iCheck('uncheck');

                $(this).on('ifUnchecked', function(event){
                    var hollydayCalendarId = dashboard.config.AllHollydayCalendars[event.target.value].id; 
                    
                    $.ajax({
                        url: '/checkHollydayCalendarById/'+hollydayCalendarId,
                        data: {
                            'checked' : 0
                        },
                        type: "POST",
                        success: function(json) {
                            dashboard.getAllHollydayCalendars();
                        }
                    });

                });


                $(this).on('ifChecked', function(event){
                    var hollydayCalendarId = dashboard.config.AllHollydayCalendars[event.target.value].id; 
                    
                    $.ajax({
                        url: '/checkHollydayCalendarById/'+hollydayCalendarId,
                        data: {
                            'checked' : 1
                        },
                        type: "POST",
                        success: function(json) {
                            dashboard.getAllHollydayCalendars();
                        }
                    });                  
                });
            });  
    }

};

//$(document).ready(dashboard.init);
//$(document).ready(dashboard.showModalError);


