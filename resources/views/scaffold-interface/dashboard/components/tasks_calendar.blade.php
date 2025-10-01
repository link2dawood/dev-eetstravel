

    <!--  TOUR TASKS CALENDAR  -->

    <div class="box col-4 calendar-compact">
        @if(Auth::user()->can('dashboard.calendar'))
        <div class="box-header">
            <h4>{!! trans('Calendar') !!}</h4>
            <div class="box-tools pull-right">
                {{--<span id="hollydaycalbutton" class="btn btn-box-tool"><i class="fa fa-calendar" aria-hidden="true"></i>
                    <div id="hollydaycaldiv" style=" z-index:500; position: absolute;top:30px;width:250px; left: -20px; right: -100%; background-color: rgb(255, 255, 255);opacity: 0;"  data-mode="1" >
                        <div id="calendar_filter_block"  style="height: 450px; overflow-y: scroll;"></div>
                    </div>
                </span>--}}

                <span id="help" class="btn btn-box-tool"><i class="fa fa-question-circle" aria-hidden="true"></i>
                    @include('legend.task_calendar_legend')
                </span>
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
            </div>
        </div>
        <div class="box-body no-padding">
           {{-- <div class="calendar" id="calendarTasks">

            </div>--}}
			   <div class="calendar calendar-widget bootsnipp-calendar-container" id="bootsnipp-calendar"></div>

        </div>
        @else
            <div class="box-header">
                <h4>{!! trans('main.TasksCalendar') !!}</h4>
            </div>
            <div class="box-body">
                {!! trans('main.Youdonthavepermissions') !!}
            </div>
        @endif
    </div>
	   
    <span id="task_create_permission" data-info="{{ \App\Helper\PermissionHelper::checkPermission('task.create') ? true : false }}"></span>
    <span id="holiday_list_permission" data-info="{{ \App\Helper\PermissionHelper::checkPermission('holiday.index') ? true : false }}"></span>
    <span id="API_KEY_google_calendar" data-info="{{ env('API_KEY_GOOGLE_CALENDAR') }}"></span>
    <!--  END TOUR TASKS CALENDAR  -->
