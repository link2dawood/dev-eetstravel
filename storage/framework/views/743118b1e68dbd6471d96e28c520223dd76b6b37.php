<?php $__env->startSection('title','Dashboard'); ?>
<?php $__env->startSection('content'); ?>
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
        
		
		 <?php echo $__env->make('scaffold-interface.dashboard.components.inbox_emails', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php echo $__env->make('scaffold-interface.dashboard.components.announcements_list', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
		<?php echo $__env->make('scaffold-interface.dashboard.components.tours_table', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php echo $__env->make('scaffold-interface.dashboard.components.tasks_list', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
		
        
		
        
		
        <?php echo $__env->make('component.modal_add_tour', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php echo $__env->make('scaffold-interface.dashboard.components.create_task_popup', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

</div>
    </section>
<?php $__env->startSection('post_styles'); ?>
    <link href="<?php echo e(URL::asset('css/jquery-jvectormap-2.0.3.css')); ?>" rel="stylesheet"/>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('post_scripts_calendar'); ?>
	<script type="text/javascript" src="<?php echo e(asset('js/lib/fullcalendar.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('js/dashboard.js')); ?>"></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.5/index.global.min.js'></script>
    <script src="<?php echo e(URL::asset('js/jquery-jvectormap-2.0.3.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('js/jquery-jvectormap-world-mill.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('js/lib/amcharts.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('js/lib/serial.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('js/lib/gantt.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('js/lib/themes/light.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('js/lib/themes/patterns.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('js/lib/plugins/dataloader/dataloader.min.js')); ?>"></script>
    <script src="https://www.amcharts.com/lib/3/plugins/export/export.min.js"></script>
    <script type="text/javascript" src="<?php echo e(asset('js/lib/chart_tours.js')); ?>"></script>

 <script src="<?php echo e(asset('assets/dist/frappe-gantt.js')); ?>"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
 /* initialize the external events
     -----------------------------------------------------------------*/

	//new calendar
	function init_events(ele) {
		
      ele.each(function () {

        // create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
        // it doesn't need to have a start or end
        var eventObject = {
          title: $.trim($(this).text()) // use the element's text as the event title
        }

        // store the Event Object in the DOM element so we can get to it later
        $(this).data('eventObject', eventObject)

        // make the event draggable using jQuery UI
        $(this).draggable({
          zIndex        : 1070,
          revert        : true, // will cause the event to go back to its
          revertDuration: 0  //  original position after the drag
        })

      })
    }

    init_events($('#external-events div.external-event'))

  document.addEventListener('DOMContentLoaded', function() {
	  
	  
    var calendarEl = document.getElementById('calendar');
	let task_permission = $('#task_create_permission').attr('data-info');
    let holiday_permission = $('#holiday_list_permission').attr('data-info');
	  
    var calendar = new FullCalendar.Calendar(calendarEl, {
	events: function (fetchInfo, successCallback, failureCallback) {
      // Make a fetch request to your server's endpoint
      fetch('/home/getToursTasksForCalendar')
        .then(function (response) {
          return response.json();
        })
        .then(function (events) {
          successCallback(events); // Pass the events to FullCalendar
        })
        .catch(function (error) {
          failureCallback(error);
        });
    },
      /*headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
      },*/
	headerToolbar: {
         left:   'title',
                center: '',
                right:  `${holiday_permission ? 'addHolildayButton' : '' } ${task_permission ? 'addTaskButton' : ''} today prevYear prev,next nextYear dayGridMonth timeGridDay`,
      },
      //initialDate: '2023-03-12',
      navLinks: true, // can click day/week names to navigate views
      businessHours: true, // display business hours
      editable: true,
      selectable: true,
	//events: dashboard.config.urlGetToursForCalendar,
			
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
		
	 
			allDaySlot: true,
            
            defaultTimedEventDuration: "00:00:00",
            eventDurationEditable : false,
            
            droppable: true,
		      drop      : function (date, allDay) { // this function is called when something is dropped

			// retrieve the dropped element's stored Event Object
			var originalEventObject = $(this).data('eventObject')

			// we need to copy it, so that multiple events don't have a reference to the same object
			var copiedEventObject = $.extend({}, originalEventObject)

			// assign it the date that was reported
			copiedEventObject.start           = date
			copiedEventObject.allDay          = allDay
			copiedEventObject.backgroundColor = $(this).css('background-color')
			copiedEventObject.borderColor     = $(this).css('border-color')

			// render the event on the calendar
			// the last `true` argument determines if the event "sticks" (http://arshaw.com/fullcalendar/docs/event_rendering/renderEvent/)
			$('#calendar').fullCalendar('renderEvent', copiedEventObject, true)

			// is the "remove after drop" checkbox checked?
			if ($('#drop-remove').is(':checked')) {
			  // if so, remove the element from the "Draggable Events" list
			  $(this).remove()
			}

		  },

            selectable: true,
			eventClick: (event, jsEvent, view) => {
			
                if (event.url) {
                    return false;
                }
                if (event.event.id != 'Holiday'){
					
                    window.location = '/task/'+event.event.id+'/edit?calendar_edit=1';
                } 
				else{
					console.log(event.event);
				}
				
            },
            dayClick(date, jsEvent, view){
                $('#calendarTasks').fullCalendar('changeView', 'agendaDay', date.format());
            },
            eventDrop: function(event, delta){
                console.log(event);
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

    calendar.render();
  });
	/* ADDING EVENTS */
    var currColor = '#3c8dbc' //Red by default
    //Color chooser button
    var colorChooser = $('#color-chooser-btn')
    $('#color-chooser > li > a').click(function (e) {
      e.preventDefault()
      //Save color
      currColor = $(this).css('color')
      //Add color effect to button
      $('#add-new-event').css({ 'background-color': currColor, 'border-color': currColor })
    })
    $('#add-new-event').click(function (e) {
      e.preventDefault()
      //Get value and make sure it is not null
      var val = $('#new-event').val()
      if (val.length == 0) {
        return
      }

      //Create events
      var event = $('<div />')
      event.css({
        'background-color': currColor,
        'border-color'    : currColor,
        'color'           : '#fff'
      }).addClass('external-event')
      event.html(val)
      $('#external-events').prepend(event)

      //Add draggable funtionality
      init_events(event)

      //Remove event from text input
      $('#new-event').val('')
	})
	//new calender end
	
	//dashboard.getTasksBlock();
	//dashboard.init();
//	dashboard.getHollydayCalendarsBlock();
	
	$(document).ready(function () {
  
  var eventElement = $('.fc-event-main-frame');

  // Remove the 'style' attribute from the selected event element
  eventElement.style.display="block";
});
</script>


<?php $__env->stopSection(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('scaffold-interface.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/scaffold-interface/dashboard/dashboard.blade.php ENDPATH**/ ?>