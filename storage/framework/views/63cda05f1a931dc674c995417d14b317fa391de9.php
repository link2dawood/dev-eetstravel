
<?php $__env->startSection('title','Index'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('layouts.title',
    ['title' => 'Comments', 'sub_title' => 'Comments List',
    'breadcrumbs' => [
    ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
    ['title' => 'Comments', 'icon' => 'comment', 'route' => null]]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <?php if(session('not_found')): ?>
                    <div class="alert alert-info">
                        <?php echo e(session('not_found')); ?>

                    </div>
                <?php endif; ?>
                <table id="comment-table" class="table table-striped table-bordered table-hover" style='background:#fff'>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th><?php echo trans('main.Content'); ?></th>
                        <th><?php echo trans('main.Time'); ?></th>
                        <th><?php echo trans('main.Sender'); ?></th>
                        <th class="actions-button" style="width: 140px"><?php echo trans('main.Actions'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $__currentLoopData = $commentsData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($comment->id); ?></td>
                            <td><?php echo e($comment->content); ?></td>
                            <td><?php echo e($comment->created_at); ?></td>
                            <td><?php echo e($comment->sender); ?></td>
                            <td><?php echo $comment->action_buttons; ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>

    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('scaffold-interface.layouts.tabler-app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\dev-eetstravel\resources\views/comments/index.blade.php ENDPATH**/ ?>