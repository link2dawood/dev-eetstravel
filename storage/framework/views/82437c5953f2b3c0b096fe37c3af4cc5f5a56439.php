<?php $__env->startSection('title','Create'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('layouts.title',
   ['title' => 'Task', 'sub_title' => 'Task Create',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Tasks', 'icon' => 'tasks', 'route' => route('task.index')],
   ['title' => 'Create', 'route' => null]]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <section class="content">
        <div class="box box-primary">
            <div class="box box-body border_top_none">
                <form method='POST' action='<?php echo route("task.store"); ?>' enctype="multipart/form-data">
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
                        <div class="col-md-12">
                            <input type='hidden' name='_token' value='<?php echo e(Session::token()); ?>'>
                            <input type='hidden' name='modal_create' value="0">

                            <div class="form-group">
                                <label for="content"><?php echo trans('main.Content'); ?></label>
                                <textarea name="content" id="content" class="form-control" style="resize: none"><?php echo e(old('content')); ?></textarea>
                            </div>
                    
                            <div class="form-group">
                                <label for="tours"><?php echo trans('main.Tour'); ?></label>
                                <select name="tour" id="tours" class="form-control">
                                    <option value=""><?php echo trans('main.WithoutTour'); ?></option>
                                    <?php $__currentLoopData = $tours; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tour): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option <?php echo e($tour_default && $tour_default == $tour->id ? 'selected' : ''); ?> value="<?php echo e($tour->id); ?>"><?php echo e($tour->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            
                            <div class="form-group col-md-6 col-lg-6" style="padding-left: 0">

                                <label for="departure_date"><?php echo trans('main.Deadline'); ?></label>

                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <?php echo Form::text('end_date', Carbon\Carbon::now()->format('Y-m-d'), ['class' => 'form-control pull-right datepicker', 'id' => 'start_date']); ?>

                                </div>
                            </div>

                            <div class="form-group col-md-6 col-lg-6" style="padding-right: 0">

                                <label for="departure_date"><?php echo trans('main.Time'); ?></label>

                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-clock-o"></i>
                                    </div>
                                    <?php echo Form::text('end_time', '18:00', ['class' => 'form-control pull-right timepicker', 'id' => 'end_time']); ?>

                                </div>

                            </div>


                            <div class="form-group" >
                                    
								<label for="assigned_user"><?php echo trans('main.AssignedUser'); ?> *</label>
								<div class ="form-control" style="max-height:200px !important;overflow-x:auto;height: auto; ">
									<?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<p><?php echo e($user->name); ?>

										<input type="checkbox" name="assigned_user" id="assigned_user" value="<?php echo e($user->id); ?>" ></p>

									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								</div>
                           </div>

                            <div class="form-group">
                                <label for="task_type"><?php echo trans('main.Tasktype'); ?></label>
                                <?php echo Form::select('task_type', \App\Task::$taskTypes, 0, ['class' => 'form-control']); ?>

                            </div>
                            <div class="form-group">
                                <label for="status"><?php echo trans('main.Status'); ?></label>
                                <select name="status" id="status" class="form-control">
                                    <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option <?php echo e(old('status') == $status->id ? 'selected' : ''); ?> value="<?php echo e($status->id); ?>"><?php echo e($status->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="checkbox">
                                <label>
                                <?php echo Form::checkbox('priority'); ?>

                                    <?php echo trans('main.Priority'); ?>

                                </label>
                            </div>
                            <div class="form-group">
                                <label><?php echo trans('main.Files'); ?></label>
                                <?php $__env->startComponent('component.file_upload_field'); ?><?php echo $__env->renderComponent(); ?>
                            </div>
                            <input type="text" hidden name="redirect_tour" value="<?php echo e($tour_default ? $tour_default : null); ?>">
                        </div>
                    </div>
                    <button class='btn btn-success' type='submit'><?php echo trans('main.Save'); ?></button>
                </form>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('scaffold-interface.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/task/create.blade.php ENDPATH**/ ?>