<?php $__env->startSection('title','Index'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('layouts.title',
           ['title' => 'Buses', 'sub_title' => 'Buses Calendar',
           'breadcrumbs' => [
           ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
           ['title' => 'Bus', 'icon' => 'bus', 'route' => null]]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
		   
	<script type="text/javascript" src="<?php echo e(asset('js/lib/amcharts.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('js/lib/serial.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('js/lib/gantt.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('js/lib/themes/light.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('js/lib/themes/dark.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('js/lib/themes/black.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('js/lib/themes/patterns.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('js/lib/plugins/dataloader/dataloader.min.js')); ?>"></script>
    <script src="https://www.amcharts.com/lib/3/plugins/export/export.min.js"></script>
    <script type="text/javascript" src="<?php echo e(asset('js/lib/chart_bus.js')); ?>"></script>

    <section class="content">
            <div class="box-body">
                <?php echo $__env->make('component.modal_add_bus', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <?php echo $__env->make('scaffold-interface.dashboard.components.bus_calendar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
    </section>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('scaffold-interface.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/bus/calendar.blade.php ENDPATH**/ ?>