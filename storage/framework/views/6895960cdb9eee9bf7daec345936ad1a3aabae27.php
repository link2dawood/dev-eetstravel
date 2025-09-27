<a href="#" class="dropdown-toggle" data-toggle="dropdown">
    <i class="fa fa-bell-o"></i>
    <?php if($user->checkNotification()): ?>
        <span class="label notification-label label-danger"><?php echo e($user->countNotification()); ?></span>
    <?php endif; ?>
</a>
<ul class="dropdown-menu">
    <li class="header"><?php echo trans('main.Yournotifications'); ?></li>
    <li class="content-notification">
        <ul class="menu notifications-list-tasks">
            <?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                
                    <li>
                     
						<a href="<?php echo e(url('/notification/show')); ?>"
                           >
                            <i class="fa fa-users text-aqua"></i> <?php echo e($notification->content); ?>

                        </a>
                        <span class="delete-notification-task" data-notif-id="<?php echo e($notification->id); ?>">
													<i class="fa fa-times" aria-hidden="true"></i>
												</span>
                    </li>
               
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </li>
    <?php if($user->countNotification()): ?>
    <ul class="list_notification">
        <li class="footer"><a href="/profile?tab=notifications-tab"><?php echo trans('main.Viewall'); ?></a></li>
        <li class="footer"><a href="#" id="read_all_notification"><?php echo trans('main.Readall'); ?></a></li>
        <li class="footer"><a href="#" id="delete_all_notification"><?php echo trans('main.Deleteall'); ?></a></li>
    </ul>
    <?php else: ?>
        <li class="footer"><a href="#"><?php echo trans('main.Youdonthavenotifications'); ?></a></li>
    <?php endif; ?>

</ul><?php /**PATH /var/www/html/resources/views/component/list-notification-task.blade.php ENDPATH**/ ?>