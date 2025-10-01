@extends('scaffold-interface.layouts.app')
@section('title','Dashboard')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
    @include('layouts.title',
    ['title' => 'Dashboard', 'sub_title' => 'Control Panel',
    'breadcrumbs' => [
    ['title' => 'Home', 'icon' => 'dashboard', 'route' => null]]])
    <section>
			{{--@include('scaffold-interface.dashboard.components.new_emails')--}}
		<div class="container-fluid">
			
            <div class="row">
				<div class="block-stretch">
                @include('scaffold-interface.dashboard.components.tasks_calendar')
            
                {{--@include('scaffold-interface.dashboard.components.inbox_emails_vue')--}}
				</div>
				</div>
        
		
		 @include('scaffold-interface.dashboard.components.inbox_emails')
        @include('scaffold-interface.dashboard.components.announcements_list')
		@include('scaffold-interface.dashboard.components.tours_table')
        @include('scaffold-interface.dashboard.components.tasks_list')
		
        
{{--
        <div class="row">
            @include('scaffold-interface.dashboard.components.chat_groups_list')

            @if(Auth::user()->can('dashboard.main_chat'))
                @include('scaffold-interface.dashboard.components.direct_chat')
            @else
                <div class="col-md-6 dashboard-widget-chat">
                    <div class="box box-warning direct-chat direct-chat-warning">
                        <div class="box-header">
                            <h3>{{ trans('main.MainChat') }}</h3>
                        </div>
                        <div class="box-body" style="padding: 0 10px 10px 10px">
                            {{ trans('main.Youdonthavepermissions') }}
                        </div>
                    </div>
                </div>
            @endif


        </div>
--}}		
        {{--<div class="row">
            @include('scaffold-interface.dashboard.components.activities_list')
        </div>--}}
		{{--@include('scaffold-interface.dashboard.components.weChat')--}}
        @include('component.modal_add_tour')
        @include('scaffold-interface.dashboard.components.create_task_popup')

</div>
    </section>
@section('post_styles')
    <link href="{{URL::asset('css/jquery-jvectormap-2.0.3.css')}}" rel="stylesheet"/>
    <link href="{{URL::asset('css/calendar-enhancements.css')}}" rel="stylesheet"/>
@endsection
@section('post_scripts_calendar')
<!-- FullCalendar CDN -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Initializing FullCalendar...');

    var calendarEl = document.getElementById('bootsnipp-calendar');
    if (!calendarEl) {
        console.error('Calendar element not found');
        return;
    }

    console.log('Calendar element found, initializing FullCalendar...');

    var task_permission = document.getElementById('task_create_permission')?.getAttribute('data-info') === 'true';
    var holiday_permission = document.getElementById('holiday_list_permission')?.getAttribute('data-info') === 'true';

    // Sample events for immediate display
    var sampleEvents = [
        {
            id: '1',
            title: 'Sample Task',
            start: new Date().toISOString().split('T')[0],
            backgroundColor: '#3b82f6',
            borderColor: '#3b82f6',
            textColor: '#ffffff'
        },
        {
            id: '2',
            title: 'Sample Tour',
            start: new Date(Date.now() + 86400000).toISOString().split('T')[0],
            backgroundColor: '#10b981',
            borderColor: '#10b981',
            textColor: '#ffffff'
        },
        {
            id: '3',
            title: 'Holiday',
            start: new Date(Date.now() + 172800000).toISOString().split('T')[0],
            backgroundColor: '#ef4444',
            borderColor: '#ef4444',
            textColor: '#ffffff'
        }
    ];

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        height: 'auto',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: ''
        },
        events: sampleEvents,
        eventClick: function(info) {
            console.log('Event clicked:', info.event);
            if (info.event.url) {
                window.open(info.event.url);
                info.jsEvent.preventDefault();
            } else if (info.event.id !== 'Holiday' && info.event.id !== 'error-1' && !info.event.id.startsWith('sample-')) {
                window.location = '{{ url("task") }}/' + info.event.id + '/edit?calendar_edit=1';
            }
        },
        dateClick: function(info) {
            if (task_permission) {
                console.log('Date clicked:', info.dateStr);
                // You can add task creation logic here
            }
        },
        eventDidMount: function(info) {
            // Add custom styling based on event type
            if (info.event.title.includes('Task')) {
                info.el.classList.add('event-task');
            } else if (info.event.title.includes('Tour')) {
                info.el.classList.add('event-tour');
            } else if (info.event.title.includes('Holiday')) {
                info.el.classList.add('event-holiday');
            }
        }
    });

    calendar.render();
    console.log('FullCalendar rendered successfully');

    // Load real data from API
    function loadCalendarData() {
        var now = new Date();
        var startOfMonth = new Date(now.getFullYear(), now.getMonth(), 1);
        var endOfMonth = new Date(now.getFullYear(), now.getMonth() + 1, 0);

        var startStr = startOfMonth.toISOString().split('T')[0];
        var endStr = endOfMonth.toISOString().split('T')[0];

        console.log('Fetching calendar data from:', '/home/getToursTasksForCalendar?start=' + startStr + '&end=' + endStr);

        fetch('/home/getToursTasksForCalendar?start=' + startStr + '&end=' + endStr, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            credentials: 'same-origin'
        })
            .then(function(response) {
                console.log('API Response status:', response.status);
                if (!response.ok) {
                    if (response.status === 401) {
                        console.error('Authentication required for calendar data');
                        return [];
                    } else if (response.status === 403) {
                        console.error('Permission denied for calendar data');
                        return [];
                    }
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json();
            })
            .then(function(events) {
                console.log('Loaded calendar events:', events);
                console.log('Number of events:', events.length);

                // Transform events for FullCalendar
                var transformedEvents = events.map(function(event) {
                    var backgroundColor = '#6b7280'; // default gray
                    var borderColor = '#6b7280';

                    // Use server-provided backgroundColor if available, otherwise use defaults
                    if (event.backgroundColor) {
                        backgroundColor = event.backgroundColor;
                        borderColor = event.backgroundColor;
                    } else {
                        // Fallback colors based on event type
                        if (event.id === 'Holiday') {
                            backgroundColor = '#ef4444'; // red
                            borderColor = '#ef4444';
                        } else if (event.c_type === 'month') {
                            backgroundColor = '#3b82f6'; // blue for tasks
                            borderColor = '#3b82f6';
                        } else {
                            backgroundColor = '#10b981'; // green for tours
                            borderColor = '#10b981';
                        }
                    }

                    return {
                        id: event.id,
                        title: event.title,
                        start: event.date, // API returns 'date' not 'start'
                        allDay: event.allDay !== undefined ? event.allDay : false,
                        backgroundColor: backgroundColor,
                        borderColor: borderColor,
                        textColor: '#ffffff',
                        extendedProps: {
                            original: event,
                            c_type: event.c_type
                        }
                    };
                });

                // Replace sample events with real data
                calendar.removeAllEvents();

                if (transformedEvents.length > 0) {
                    calendar.addEventSource(transformedEvents);
                    console.log('Calendar data updated with', transformedEvents.length, 'real events');
                } else {
                    // Show sample events if no real data available
                    console.log('No real events found, showing sample data');
                    var currentDate = new Date();
                    var sampleData = [
                        {
                            id: 'sample-1',
                            title: 'No real tasks found - Sample Task',
                            start: currentDate.toISOString().split('T')[0],
                            backgroundColor: '#3b82f6',
                            borderColor: '#3b82f6',
                            textColor: '#ffffff'
                        },
                        {
                            id: 'sample-2',
                            title: 'Sample Tour Event',
                            start: new Date(currentDate.getTime() + 86400000).toISOString().split('T')[0],
                            backgroundColor: '#10b981',
                            borderColor: '#10b981',
                            textColor: '#ffffff'
                        }
                    ];
                    calendar.addEventSource(sampleData);
                }
            })
            .catch(function(error) {
                console.error('Error loading calendar data:', error);

                // Show error indicator on calendar
                var currentDate = new Date();
                var errorEvent = [{
                    id: 'error-1',
                    title: 'Error loading tasks - Check console',
                    start: currentDate.toISOString().split('T')[0],
                    backgroundColor: '#ef4444',
                    borderColor: '#ef4444',
                    textColor: '#ffffff'
                }];

                calendar.removeAllEvents();
                calendar.addEventSource(errorEvent);
                console.log('Error event added to calendar');
            });
    }

    // Load real data after a short delay
    setTimeout(loadCalendarData, 1000);

    // Add navigation buttons if permissions allow
    if (holiday_permission || task_permission) {
        var headerElement = document.querySelector('.calendar-compact .box-header');
        var toolsArea = headerElement?.querySelector('.box-tools');

        if (toolsArea) {
            if (holiday_permission) {
                var holidayBtn = document.createElement('button');
                holidayBtn.className = 'btn btn-box-tool';
                holidayBtn.title = 'Manage Holidays';
                holidayBtn.innerHTML = '<i class="fa fa-calendar-o"></i>';
                holidayBtn.onclick = function() { window.location.assign('/holiday'); };
                toolsArea.insertBefore(holidayBtn, toolsArea.firstChild);
            }

            if (task_permission) {
                var taskBtn = document.createElement('button');
                taskBtn.className = 'btn btn-box-tool';
                taskBtn.title = 'Add Task';
                taskBtn.innerHTML = '<i class="fa fa-plus"></i>';
                taskBtn.onclick = function() {
                    var modal = document.getElementById('modalCreate1');
                    if (modal && typeof $(modal).modal === 'function') {
                        $(modal).modal('show');
                    }
                };
                toolsArea.insertBefore(taskBtn, toolsArea.firstChild);
            }
        }
    }
});
</script>


@endsection
@endsection
