<link rel="stylesheet" href="{{asset('assets/dist/frappe-gantt.css')}}" />
<div class="box box-primary">
    <div class="box-header" style="background-color:#329ba8 ;color:white">
        <h4>{!! trans('main.TasksCalendar') !!}</h4>
        <div class="box-tools pull-right">
            <span id="hollydaycalbutton" class="btn btn-box-tool"><i class="fa fa-calendar" aria-hidden="true"></i>
                <div id="hollydaycaldiv" style=" z-index:500; position: absolute;top:30px;width:250px; left: -20px; right: -100%; background-color: rgb(255, 255, 255);opacity: 0;" data-mode="1">
                    <div id="calendar_filter_block" style="height: 450px; overflow-y: scroll;"></div>
                </div>
            </span>

            <span id="help" class="btn btn-box-tool"><i class="fa fa-question-circle" aria-hidden="true"></i>
               {{-- @include('legend.task_calendar_legend')--}}
            </span>
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
        </div>
    </div>
    <div class="box-body">
        <div class="container">
      
            <div style="display: inline;" class="js-view">
                <button type="button" data-view="Quarter Day">Quarter Day</button>
                <button type="button" data-view="Half Day">Half Day</button>
                <button type="button" data-view="Day">Day</button>
                <button type="button" data-view="Week">Week</button>
                <button type="button" data-view="Month" class="selected">Month</button>
            </div>
            <div style="display: inline;float: right;">
            <button type="button"  onclick="holidays();">Holidays</button>
                <button type="button"  onclick="addTask();">Add Task</button>
                <button type="button" onclick="collapseAll();">Collapse all</button>
                <button type="button" onclick="expandBars();">Expand all</button>
                <button type="button"   onclick="generateCalendar();">Calender</button>
            </div>
            <div class="gantt-target"></div>
        </div>
    </div>

</div>
<style>
    body {
        font-family: sans-serif;
        background: #ccc;
    }

    .container {
        width: 90%;
        margin: 0 auto;
    }

    button:hover,
    button:focus {
        background: #a3a3ff;
    }

    button {
        border: solid thin grey;
        border-radius: 3px;
    }

    button.selected {
        background: #a3a3ff;
    }

    /* task custom class */
    .gantt-target .bar-milestone .bar {
        fill: tomato;
    }

    /* bar collapsed */
    .parent>.bar-group>.bar {
        outline: outset 1px #a3a3ff;
        border-radius: 2px;
        outline-offset: 2px;
    }

    .gantt-target .details-container {
        padding: 0px 12px;
    }
</style>