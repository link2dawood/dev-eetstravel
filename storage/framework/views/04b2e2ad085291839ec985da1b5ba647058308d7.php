<?php $__env->startSection('title','Edit'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('layouts.title',
   ['title' => 'Tour Task', 'sub_title' => 'Tour Task Edit',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Tour Tasks', 'icon' => 'tasks', 'route' => route('task.index')],
   ['title' => 'Edit', 'route' => null]]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php
        $tab = '' ;
        $uri_parts = explode('?', \Request::fullUrl() );
        if(count($uri_parts)>1){
           $tab_parts = explode('=', $uri_parts[1]);
           if($tab_parts[0] == 'tab') $tab = $uri_parts[1];
        }
    ?>
    <section class="content">
        <div class="box box-primary">
            <div class="box box-body border_top_none">
                <form method='POST' action='<?php echo url("task"); ?>/<?php echo $task->id; ?>/update'>
                    <input type="hidden" id="tab" name="tab" value="<?php echo e($tab); ?>" >
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

                    <input type='hidden' name='_token' value='<?php echo e(Session::token()); ?>'>
                    <div class="tab-content">
                        <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="content"><?php echo trans('main.Content'); ?></label>
                                        <input id="content" name="content" type="text" class="form-control" value="<?php echo e($errors != null && count($errors) > 0 ? '' : $task->content); ?><?php echo e(old('content')); ?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="tours"><?php echo trans('main.Tour'); ?></label>
                                        <select name="tour" id="tours" class="form-control">
                                            <option value="">Without Tour</option>
                                            <?php $__currentLoopData = $tours; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tour): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option <?php echo e($task->tour_id == $tour->id ? 'selected' : ''); ?> value="<?php echo e($tour->id); ?>"><?php echo e($tour->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-6 col-lg-6" style="padding-left: 0;">

                                        <label for="departure_date"><?php echo trans('main.Deadline'); ?></label>

                                        <div class="input-group date">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            <?php echo Form::text('end_date', $task->end_date, ['class' => 'form-control pull-right datepicker', 'id' => 'start_date']); ?>

                                        </div>
                                    </div>

                                    <div class="form-group col-md-6 col-lg-6" style="padding-right: 0">

                                        <label for="departure_date"><?php echo trans('main.Time'); ?></label>

                                        <div class="input-group date">
                                            <div class="input-group-addon">
                                                <i class="fa fa-clock-o"></i>
                                            </div>
                                            <?php echo Form::text('end_time', $task->end_time, ['class' => 'form-control pull-right timepicker', 'id' => 'end_time']); ?>

                                        </div>

                                    </div>

                                    
                                    <div class="form-group" >
										<label for="assigned_user"><?php echo trans('main.AssignedUser'); ?> *</label>
										<div class ="form-control" style="max-height:100px !important;overflow-x:auto;height: auto; ">
										<?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<p><?php echo e($user->name); ?>

										<input type="checkbox" name="assigned_user" id="assigned_user" value="<?php echo e($user->id); ?>" <?php echo e($user->selected ? 'checked' : ''); ?>></p>

										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
										</div>
									</div>
                                    <div class="form-group">
                                        <label for="task_type"><?php echo trans('main.Tasktype'); ?></label>
                                        <?php echo Form::select('task_type', \App\Task::$taskTypes, $task->task_type, ['class' => 'form-control']); ?>

                                    </div>
                                    <div class="form-group">
                                        <label for="status"><?php echo trans('main.Status'); ?></label>
                                        <select name="status" id="status" class="form-control">
                                            <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($status->id); ?>" <?php echo e($errors != null && count($errors) > 0 ? (old('status') == $status->id ? 'selected' : '') : ($task->status == $status->id ? 'selected' : '')); ?>><?php echo e($status->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                    <div class="checkbox">
                                        <label>
                                            <?php echo Form::checkbox('priority', 1, $task->priority); ?>

                                            Priority
                                        </label>
                                    </div>

                                    <div class="form-group">
                                        <label for="attach"><?php echo trans('main.Files'); ?></label>
                                        <?php $__env->startComponent('component.file_upload_field'); ?><?php echo $__env->renderComponent(); ?>
                                    </div>
                                    <input type="text" hidden name="calendar_edit" value="<?php echo e($calendar_edit); ?>">
                                </div>
                                <div class="col-md-8">
                                    <div class="tour-packages"></div>

                                    <div class="row">
                                        <?php $__env->startComponent('component.files', ['files' => $files]); ?><?php echo $__env->renderComponent(); ?>
                                    </div>
                                </div>
                            </div>
                        <div id="itinerary" class="tab-pane fade">

                        </div>
                        <button class='btn btn-success' type='submit'><?php echo trans('main.Save'); ?></button>
                        <a href="<?php echo e(\App\Helper\AdminHelper::getBackButton(route('task.index'))); ?>">
                            <button class='btn btn-warning' type='button'><?php echo trans('main.Cancel'); ?></button>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('scaffold-interface.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/task/edit.blade.php ENDPATH**/ ?>