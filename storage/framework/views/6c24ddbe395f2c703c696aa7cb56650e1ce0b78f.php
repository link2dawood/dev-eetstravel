<?php $__env->startSection('title','Create'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('layouts.title',
   ['title' => 'Announcement', 'sub_title' => 'Announcement Create',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Announcements', 'icon' => 'coffee', 'route' => route('announcements.index')],
   ['title' => 'Create', 'route' => null]]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <section class="content">
        <div class="box box-primary">
            <div class="box box-body border_top_none">
                <form method='POST' action='<?php echo e(route('announcements.store')); ?>' enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="margin_button">
                                <a href="javascript:history.back()">
                                    <button type="button" class='btn btn-primary back_btn'><?php echo trans('main.Back'); ?></button>
                                </a>
                                <button class='btn btn-success' type='submit'><?php echo trans('main.Save'); ?></button>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">

                            <div class="form-group <?php echo e($errors->has('title') ? 'has-error' : ''); ?>">
                                <label for="title"><?php echo trans('main.Title'); ?></label>
                                <?php echo Form::input('text', 'title', $title, ['class' => 'form-control', 'id' => 'title']); ?>

                                <?php if($errors->has('title')): ?>
                                    <strong><?php echo e($errors->first('title')); ?></strong>
                                <?php endif; ?>
                            </div>

                            <div class="form-group">
                                <label for="content"><?php echo trans('main.Content'); ?></label>
                                <?php echo Form::textarea('content', '', ['id' => 'content', 'class' => 'form-control']); ?>

                            </div>

                            <?php echo e(Form::hidden('parent_id', $parent_id)); ?>


                                <div class="form-group">
                         <label><?php echo trans('main.Files'); ?></label>
                   <input type="file" name="files[]" class="form-control" multiple>
                 </div>


                            <button class='btn btn-success' type='submit'><?php echo trans('main.Save'); ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('scaffold-interface.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/announcements/create.blade.php ENDPATH**/ ?>