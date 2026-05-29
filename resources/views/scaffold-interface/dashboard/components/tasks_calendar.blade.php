{{-- Tour & Tasks calendar widget (dashboard) --}}
<div class="card shadow-sm calendar-compact h-100">
    @if(Auth::user()->can('dashboard.calendar'))
        <div class="card-header d-flex align-items-center">
            <h3 class="card-title mb-0 flex-grow-1">
                <i class="ti ti-calendar-event me-2 text-primary"></i>{!! trans('Calendar') !!}
            </h3>

            <div class="d-flex align-items-center gap-1">
                <span id="help" class="btn btn-sm btn-icon btn-ghost-secondary" title="Legend">
                    <i class="ti ti-help-circle"></i>
                    @include('legend.task_calendar_legend')
                </span>
                <button type="button"
                        class="btn btn-sm btn-icon btn-ghost-secondary"
                        data-widget="collapse"
                        aria-label="Collapse">
                    <i class="ti ti-minus"></i>
                </button>
                <button type="button"
                        class="btn btn-sm btn-icon btn-ghost-secondary"
                        data-widget="remove"
                        aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="calendar calendar-widget bootsnipp-calendar-container" id="bootsnipp-calendar"></div>
        </div>
    @else
        <div class="card-header">
            <h3 class="card-title mb-0">
                <i class="ti ti-calendar-event me-2 text-primary"></i>{!! trans('main.TasksCalendar') !!}
            </h3>
        </div>
        <div class="card-body">
            <div class="alert alert-warning d-flex align-items-center mb-0">
                <i class="ti ti-alert-circle me-2"></i>
                <div>{!! trans('main.Youdonthavepermissions') !!}</div>
            </div>
        </div>
    @endif
</div>

<span id="task_create_permission" data-info="{{ \App\Helper\PermissionHelper::checkPermission('task.create') ? true : false }}"></span>
<span id="holiday_list_permission" data-info="{{ \App\Helper\PermissionHelper::checkPermission('holiday.index') ? true : false }}"></span>
<span id="API_KEY_google_calendar" data-info="{{ env('API_KEY_GOOGLE_CALENDAR') }}"></span>
