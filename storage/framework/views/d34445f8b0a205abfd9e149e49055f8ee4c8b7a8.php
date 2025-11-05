
<?php $__env->startSection('title','Dashboard'); ?>
<?php $__env->startSection('content'); ?>
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <?php echo $__env->make('layouts.title',
    ['title' => 'Dashboard', 'sub_title' => 'Control Panel',
    'breadcrumbs' => [
    ['title' => 'Home', 'icon' => 'dashboard', 'route' => null]]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <section>
            
        <div class="container-fluid">
            
            <div class="row">
                <div class="block-stretch">
                <?php echo $__env->make('scaffold-interface.dashboard.components.tasks_calendar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>

            <div class="row">
                <div class="block-stretch">
                <?php echo $__env->make('scaffold-interface.dashboard.components.tours_table', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
        
            <div class="row">
                <div class="block-stretch">
                <?php echo $__env->make('scaffold-interface.dashboard.components.inbox_emails', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <?php echo $__env->make('scaffold-interface.dashboard.components.announcements_list', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                
                
                
                
                <?php echo $__env->make('scaffold-interface.dashboard.components.tasks_list', [
                    'todoTasks' => $todoTasks,
                    'completedTasks' => $completedTasks,
                    'abortedTasks' => $abortedTasks,
                    'statuses' => $statuses
                ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                
                
                

                </div>
            </div>
        
        
        
        
        
        <?php echo $__env->make('component.modal_add_tour', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php echo $__env->make('scaffold-interface.dashboard.components.create_task_popup', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

</div>
    </section>
<?php $__env->startSection('post_styles'); ?>
    <link href="<?php echo e(URL::asset('css/jquery-jvectormap-2.0.3.css')); ?>" rel="stylesheet"/>
    <link href="<?php echo e(URL::asset('css/calendar-enhancements.css')); ?>" rel="stylesheet"/>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('post_scripts_calendar'); ?>
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

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        height: 'auto',
            expandRows: true,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: ''
        },
        events: function(info, successCallback, failureCallback) {
            var startStr = info.startStr;
            var endStr = info.endStr;
            var url = '/home/getToursTasksForCalendar?start=' + startStr + '&end=' + endStr;
            fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                credentials: 'same-origin'
            })
                .then(function(response) {
                    if (!response.ok) {
                        if (response.status === 401 || response.status === 403) {
                            successCallback([]);
                            return null;
                        }
                        throw new Error('Network response was not ok: ' + response.status);
                    }
                    return response.json();
                })
                .then(function(events) {
                    if (!events) return; // handled above
                    var transformed = events.map(function(event) {
                        var backgroundColor = event.backgroundColor || '#6b7280';
                        var borderColor = backgroundColor;
                        var classNames = [];
                        if (!event.backgroundColor) {
                            if (event.id === 'Holiday') {
                                backgroundColor = '#ef4444';
                                borderColor = '#ef4444';
                                classNames.push('event-holiday');
                            } else if (event.c_type === 'month') {
                                backgroundColor = '#3b82f6';
                                borderColor = '#3b82f6';
                                classNames.push('event-task');
                            } else {
                                backgroundColor = '#10b981';
                                borderColor = '#10b981';
                                classNames.push('event-tour');
                            }
                        } else {
                            // If server provided backgroundColor, infer class by id/c_type for CSS consistency
                            if (event.id === 'Holiday') classNames.push('event-holiday');
                            else if (event.c_type === 'month') classNames.push('event-task');
                            else classNames.push('event-tour');
                        }
                        return {
                            id: event.id,
                            title: event.title,
                            start: event.date,
                            allDay: event.allDay !== undefined ? event.allDay : false,
                            backgroundColor: backgroundColor,
                            borderColor: borderColor,
                            textColor: '#ffffff',
                            classNames: classNames,
                            extendedProps: {
                                original: event,
                                c_type: event.c_type
                            }
                        };
                    });
                    successCallback(transformed);
                })
                .catch(function(error) {
                    console.error('Error loading calendar data:', error);
                    failureCallback(error);
                });
        },
        eventClick: function(info) {
            console.log('Event clicked:', info.event);
            if (info.event.url) {
                window.open(info.event.url);
                info.jsEvent.preventDefault();
            } else if (
                info.event &&
                info.event.id !== 'Holiday' &&
                info.event.id !== 'error-1' &&
                !String(info.event.id || '').startsWith('sample-')
            ) {
                window.location = '<?php echo e(url("task")); ?>/' + info.event.id + '/edit?calendar_edit=1';
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
        var toolsArea = headerElement && headerElement.querySelector ? headerElement.querySelector('.box-tools') : null;

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


<?php $__env->stopSection(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('scaffold-interface.layouts.tabler-app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\dev-eetstravel\resources\views/scaffold-interface/dashboard/dashboard.blade.php ENDPATH**/ ?>