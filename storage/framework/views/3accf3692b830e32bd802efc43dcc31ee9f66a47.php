
<?php $__env->startSection('title','Users'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('layouts.title', [
        'title' => 'Users',
        'sub_title' => 'Users List',
        'breadcrumbs' => [
            ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
            ['title' => 'Users', 'icon' => 'user', 'route' => null]
        ]
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <section class="content">
        <div class="card">
            <div class="card-body">
                <?php if(Session::has('message')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                            <path d="M12 9v2m0 4v2" />
                        </svg>
                        <?php echo e(Session::get('message')); ?>

                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="mb-3">
                    <a href="<?php echo e(url('/users/create')); ?>" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M12 5l0 14" />
                            <path d="M5 12l14 0" />
                        </svg>
                        <?php echo e(trans('main.New')); ?>

                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-vcenter card-table clickable-rows">
                        <thead>
                            <tr>
                                <th><?php echo e(trans('main.Name')); ?></th>
                                <th><?php echo e(trans('main.Email')); ?></th>
                                <th><?php echo e(trans('main.Roles')); ?></th>
                                <th><?php echo e(trans('main.Permissions')); ?></th>
                                <th class="w-1"><?php echo e(trans('main.Actions')); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="clickable-row" onclick="window.location.href='<?php echo e(url('/users/'.$user->id.'/edit')); ?>'">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <span class="badge bg-blue-lt"><?php echo e($user->name); ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-secondary"><?php echo e($user->email); ?></span>
                                </td>
                                <td>
                                    <?php if(!empty($user->roles)): ?>
                                        <div class="badges-list">
                                            <?php $__currentLoopData = $user->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <span class="badge bg-blue text-blue-fg"><?php echo e($role->name); ?></span>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="badge bg-secondary"><?php echo e(trans('main.NoRoles')); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if(!empty($user->permissions)): ?>
                                        <div class="badges-list">
                                            <?php $__currentLoopData = $user->permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <span class="badge bg-orange text-orange-fg"><?php echo e($permission->alias); ?></span>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="badge bg-secondary"><?php echo e(trans('main.NoPermissions')); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td onclick="event.stopPropagation();">
                                    <?php echo $__env->make('component.action_buttons', [
                                        'item' => $user,
                                        'routePrefix' => 'users'
                                    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="text-center text-secondary py-4">
                                    No users found
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if(method_exists($users, 'links')): ?>
                    <div class="card-footer">
                        <?php echo e($users->links()); ?>

                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<style>
    .clickable-rows .clickable-row {
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .clickable-rows .clickable-row:hover {
        background-color: #f8f9fa !important;
    }

    .clickable-rows .clickable-row td:last-child {
        cursor: default;
    }

    .badges-list {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .badges-list .badge {
        margin: 0;
    }
</style>
<?php echo $__env->make('scaffold-interface.layouts.tabler-app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xamppp\htdocs\dev-eetstravel\resources\views/scaffold-interface/users/index.blade.php ENDPATH**/ ?>