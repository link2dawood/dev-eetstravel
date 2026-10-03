<?php
    $unreadCount = $notifications->where('click', false)->count();
?>
<a href="#" class="nav-link px-0" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" aria-label="Show notifications">
    <i class="ti ti-bell icon"></i>
    <?php if($unreadCount): ?>
        <span class="badge bg-red notification-label" aria-label="<?php echo e($unreadCount); ?> unread notifications"><?php echo e($unreadCount); ?></span>
    <?php endif; ?>
</a>
<div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card notification-dropdown">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><?php echo e(trans('main.Yournotifications')); ?></h3>
            <span class="ms-auto text-muted small"><?php echo e($unreadCount); ?> unread</span>
        </div>
        <div class="list-group list-group-flush list-group-hoverable" data-notifications-list style="max-height: 22rem; overflow-y: auto;">
            <?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="list-group-item d-flex align-items-start gap-2">
                    <a href="<?php echo e(url('/notification/show')); ?>" class="notification-content-link flex-fill" style="overflow-wrap: anywhere;">
                        <?php echo e($notification->content); ?>

                    </a>
                    <button type="button" class="btn btn-sm delete-notification-task" data-notif-id="<?php echo e($notification->id); ?>" aria-label="Delete notification">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="list-group-item text-muted"><?php echo e(trans('main.Youdonthavenotifications')); ?></div>
            <?php endif; ?>
        </div>
        <?php if($notifications->isNotEmpty()): ?>
            <div class="card-footer d-flex flex-wrap gap-2">
                <a href="<?php echo e(url('/profile')); ?>?tab=notifications-tab" class="btn btn-sm btn-link"><?php echo e(trans('main.Viewall')); ?></a>
                <a href="#" id="read_all_notification" class="btn btn-sm btn-link"><?php echo e(trans('main.Readall')); ?></a>
                <a href="#" id="delete_all_notification" class="btn btn-sm btn-link text-danger"><?php echo e(trans('main.Deleteall')); ?></a>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH F:\dev-eetstravel\resources\views/component/list-notification-tabler.blade.php ENDPATH**/ ?>