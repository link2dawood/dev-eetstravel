<?php $__env->startSection('title','Edit'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('layouts.title',
   ['title' => 'Comment', 'sub_title' => 'Comment Edit',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Comments', 'icon' => 'comment', 'route' => route('comment.index')],
   ['title' => 'Edit', 'route' => null]]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <section class="content">
        <div class="box box-primary">
            <div class="box box-body border_top_none">
                <form method='POST' action='<?php echo e(route('comment.update', ['comment' => $comment->id])); ?>' enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="margin_button">
                                <a href="javascript:history.back()">
                                    <button class='btn btn-primary back_btn' type="button"><?php echo trans('main.Back'); ?></button>
                                </a>
                                <button class='btn btn-success' type='submit'><?php echo trans('main.Save'); ?></button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">

                                <?php echo e(csrf_field()); ?>

                                <?php echo e(method_field('PUT')); ?>

                                <div class="form-group">
                                    <label for="content"><?php echo trans('main.Content'); ?></label>
                                    <?php echo Form::textarea('content', $comment->content, ['id' => 'content', 'class' => 'form-control']); ?>

                                </div>
                                <div class="form-group">
                                    <label for="attach"><?php echo trans('main.Files'); ?></label>
                                    <?php $__env->startComponent('component.file_upload_field'); ?><?php echo $__env->renderComponent(); ?>
                                </div>
                                <?php $__env->startComponent('component.files', ['files' => $files]); ?><?php echo $__env->renderComponent(); ?>
                                <button class='btn btn-success' type='submit'><?php echo trans('main.Save'); ?></button>
                            <a href="<?php echo e(\App\Helper\AdminHelper::getBackButton(route('comment.index'))); ?>">
                                <button class='btn btn-warning' type='button'><?php echo trans('main.Cancel'); ?></button>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('scaffold-interface.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/comments/edit.blade.php ENDPATH**/ ?>