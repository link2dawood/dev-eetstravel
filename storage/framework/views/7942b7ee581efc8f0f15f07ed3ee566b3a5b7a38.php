

    <!--  TOUR TASKS CALENDAR  -->

    <div class="box col-6">
        <?php if(Auth::user()->can('dashboard.calendar')): ?>
        <div class="box-header">
            <h4><?php echo trans('Calendar'); ?></h4>
            <div class="box-tools pull-right">
                

                <span id="help" class="btn btn-box-tool"><i class="fa fa-question-circle" aria-hidden="true"></i>
                    <?php echo $__env->make('legend.task_calendar_legend', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </span>
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
            </div>
        </div>
        <div class="box-body no-padding">
           
			   <div class="calendar " id="calendar"></div>
			
        </div>
        <?php else: ?>
            <div class="box-header">
                <h4><?php echo trans('main.TasksCalendar'); ?></h4>
            </div>
            <div class="box-body">
                <?php echo trans('main.Youdonthavepermissions'); ?>

            </div>
        <?php endif; ?>
    </div>
	   
    <span id="task_create_permission" data-info="<?php echo e(\App\Helper\PermissionHelper::checkPermission('task.create') ? true : false); ?>"></span>
    <span id="holiday_list_permission" data-info="<?php echo e(\App\Helper\PermissionHelper::checkPermission('holiday.index') ? true : false); ?>"></span>
    <span id="API_KEY_google_calendar" data-info="<?php echo e(env('API_KEY_GOOGLE_CALENDAR')); ?>"></span>
    <!--  END TOUR TASKS CALENDAR  -->
<?php /**PATH /var/www/html/resources/views/scaffold-interface/dashboard/components/tasks_calendar.blade.php ENDPATH**/ ?>