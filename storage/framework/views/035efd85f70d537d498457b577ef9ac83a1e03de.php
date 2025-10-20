<!--  Buses CALENDAR  -->
<div class="col-md-12">
    <div class="box box-primary">
        <div class="box-header">
            <div class="box-tools pull-right">

                <div class="form-inline">
                    <div class="form-group">
                        <span id="filter" class="btn btn-box-tool"><i class="fa fa-car" aria-hidden="true"></i></span>
                        <span id="help" class="btn btn-box-tool"><i class="fa fa-question-circle" aria-hidden="true"></i></span>
                        <span class="btn btn-box-tool"></span>
                        <span class="btn btn-box-tool"></span>
                   <!-- <button type="button" class="btn btn-box-tool" >
                   <i class="fa fa-minus"></i></button>
                        <button type="button" class="btn btn-box-tool" >
                        <i class="fa fa-times"></i></button> -->
                    </div>
                </div>

            </div>

            <div id="leggend" style="width: 275px; height: 94px;z-index:9999; position: absolute;top:5%; left: -80%; background-color: rgb(255, 255, 255);opacity: 0;"></div>
            <div id="filter_block" style="z-index:99999; position: absolute;top:5%; left: -85%; background-color: rgb(255, 255, 255);opacity: 0;">
            </div>

            <div id="busdiv" style="width: 104%; height: 700px; position: relative; top: 10px;"></div>

            <div id="leggend_array" class="hidden">
                <?php $__currentLoopData = $bus_statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <span id="<?php echo e($status->color); ?>"><?php echo e($status->name); ?></span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

        </div>
    </div>
    <span id="trip_edit_permission" data-info="<?php echo e(\App\Helper\PermissionHelper::checkPermission('tour_package.edit')); ?>"></span>
    <span id="trip_create_permission" data-info="<?php echo e(\App\Helper\PermissionHelper::checkPermission('tour_package.create')); ?>"></span>
</div>
<style>

    #leggend,#filter_block {
        -webkit-box-shadow: 3px 0px 19px 2px rgba(0, 0, 0, 0.31);
        -moz-box-shadow: 3px 0px 19px 2px rgba(0, 0, 0, 0.31);
        box-shadow: 3px 0px 19px 2px rgba(0, 0, 0, 0.31);

        border-radius: 5px 5px 5px 5px;
        -moz-border-radius: 5px 5px 5px 5px;
        -webkit-border-radius: 5px 5px 5px 5px;
        border: 0px solid #ffffff;
    }

</style>
<?php /**PATH /var/www/html/resources/views/scaffold-interface/dashboard/components/bus_calendar.blade.php ENDPATH**/ ?>