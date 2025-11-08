
<?php $__env->startSection('title','Edit'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('layouts.title',
           ['title' => 'Bus', 'sub_title' => 'Bus Edit',
           'breadcrumbs' => [
           ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
           ['title' => 'Buses', 'icon' => 'bus', 'route' => route('bus.index')],
           ['title' => 'Edit', 'route' => null]]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <?php if(count($errors) > 0): ?>
                    <br>
                    <div class="alert alert-danger">
                        <ul>
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php endif; ?>
                <form method='POST' action='<?php echo url("bus"); ?>/<?php echo $bus->id; ?>/update'>
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
                    <input type='hidden' name='_token' value='<?php echo e(Session::token()); ?>'>
                    <div class="tab-content">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label ><?php echo trans('main.Name'); ?></label>
                                    <input type="text" value="<?php echo e($errors != null && count($errors) > 0 ? '' : $bus->name); ?><?php echo e(old('name')); ?>" name="name" class="form-control">
                                </div>

                                <div class="form-group">
                                    <label><?php echo trans('main.Busnumber'); ?></label>
                                    <input type="text" value="<?php echo e($errors != null && count($errors) > 0 ? '' : $bus->bus_number); ?><?php echo e(old('bus_number')); ?>" name="bus_number" class="form-control">
                                </div>

                                <div class="form-group">
                                    <label><?php echo trans('main.BusCompany'); ?></label>
                                    <?php echo Form::select('transfer_id', $transfers, $errors != null && count($errors) > 0 ? '' : $bus->transfer_id, ['class' => 'form-control']); ?>

                                </div>

                                <div class="form-group">
                                    <label for="attach"><?php echo trans('main.Files'); ?></label>
                                    <?php $__env->startComponent('component.file_upload_field'); ?><?php echo $__env->renderComponent(); ?>
                                </div>
                                <?php $__env->startComponent('component.files', ['files' => $files]); ?><?php echo $__env->renderComponent(); ?>
                            </div>
                        </div>

                        <button class='btn btn-success' type='submit'><?php echo trans('main.Save'); ?></button>
                        <a href="<?php echo e(\App\Helper\AdminHelper::getBackButton(route('bus.index'))); ?>">
                            <button class='btn btn-warning' type='button'><?php echo trans('main.Cancel'); ?></button>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('scaffold-interface.layouts.tabler-app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xamppp\htdocs\dev-eetstravel\resources\views/bus/edit.blade.php ENDPATH**/ ?>