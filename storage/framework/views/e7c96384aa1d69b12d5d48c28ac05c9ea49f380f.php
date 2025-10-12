<?php $__env->startSection('title','Show'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('layouts.title',
   ['title' => 'Notifications', 'sub_title' => $user->name,
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Show', 'route' => null]]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                
                        <?php echo $__env->make('component.list_notifications_profile', ['userName' => $user->name, 'userId' => $user->id], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        
                    
                
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('post_scripts'); ?>
    <script src="<?php echo e(asset('js/profile.js')); ?>"></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('scaffold-interface.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/profile/notification.blade.php ENDPATH**/ ?>