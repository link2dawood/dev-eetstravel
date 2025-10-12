<?php $__env->startSection('title','Show'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('layouts.title',
   ['title' => 'Profile', 'sub_title' => $user->name,
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Show', 'route' => null]]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs" role='tablist'>
                        <li role='presentation' class="active">
                            <a href="#info_profile" aria-controls='info_profile' role='tab' data-toggle='tab'>
                                <?php echo e(trans('main.Info')); ?>

                            </a>
                        </li>
                        <li role='presentation'>
                            <a href="#timeline" aria-controls='timeline' role='tab' data-toggle='tab'>History</a>
                        </li>
                        <?php if(Auth::user()->can('task.index')): ?>
                            <li role='presentation'>
                                <a href="#history_tasks_tab" aria-controls='history_tasks_tab' role='tab'
                                   data-toggle='tab' id="history-tasks-tab">
                                    <?php echo e(trans('main.Tasks')); ?>

                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if(Auth::user()->can('tour.index')): ?>
                            <li role='presentation'>
                                <a href="#history_tours_tab" aria-controls='history_tours_tab' role='tab'
                                   data-toggle='tab' id="history-tours-tab">
                                    <?php echo e(trans('main.Tours')); ?>

                                </a>
                            </li>
                        <?php endif; ?>
                        <li role='presentation'>
                            <a href="#notifications" aria-controls='notifications_tab' role='tab' data-toggle='tab' id="notifications-tab" >
                                <?php echo e(trans('main.Notifications')); ?>

                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade in active" id="info_profile">
                            <div class="block_info_profile" style="overflow: hidden; display: block">
                                <div class="col-lg-4 col-md-4">
                                    <!-- Profile Image -->
                                    <div class="box box-primary without-border">
                                        <div class="box-body box-profile">
                                            <img class="profile-user-img img-responsive img-circle"
                                                 src="<?php echo e($user->avatar ? asset($user->avatar) : asset('img/avatar.png')); ?>" alt="User profile picture"
                                                 style="text-align: center;">
                                            <h3 class="profile-username text-center"><?php echo e($user->name); ?></h3>

                                            <p class="text-muted text-center"><?php echo e($user->email); ?></p>
                                        </div>
                                        <!-- /.box-body -->
                                    </div>
                                    <!-- /.box -->

                                    <div class="box box-primary without-border">
                                        <div class="box-body">
                                            <strong>
                                                <i class="fa fa-book margin-r-5"></i><?php echo e(trans('main.Education')); ?>

                                            </strong>
                                            <p class="text-muted"><?php echo e($user->education); ?></p>
                                            <hr style="margin-top: 20px; margin-bottom: 20px;">
                                            <strong>
                                                <i class="fa fa-map-marker margin-r-5"></i><?php echo e(trans('main.Location')); ?>

                                            </strong>
                                            <p class="text-muted"><?php echo e($user->location); ?></p>
                                            <hr style="margin-top: 20px; margin-bottom: 20px;">
                                            <strong>
                                                <i class="fa fa-file-text-o margin-r-5"></i><?php echo e(trans('main.Notes')); ?>

                                            </strong>
                                            <p class="text-muted"><?php echo e($user->note); ?></p>
                                            <strong>
                                                <?php echo e(trans('main.Updateemails')); ?> :
                                            </strong>
                                            <div class="row">
                                                <div class="col-md-3 col-xs-8">
                                                    <input type="text" class="form-control" id="time_period">
                                                </div>
                                                <div class="col-md-4">
                                                    <select name="update" id="period_type">
                                                        <option value="D"><?php echo e(trans('main.Days')); ?></option>
                                                        <option value="H"><?php echo e(trans('main.Hours')); ?></option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4 col-xs-4">
                                                    <button type="button" class="btn btn-success" id="period_submit"><?php echo e(trans('main.Go')); ?></button>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="alert alert-info alert-dismissible" id="alert-message" style="display: none; margin-top: 20px">
                                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                                        <i class="icon fa fa-info"></i>
                                                        <?php echo e(trans('main.Theprocessisrunning')); ?>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="box-footer">
                                            <a href="<?php echo e(url('logout')); ?>" id="logout" class="btn btn-default btn-flat"
                                               onclick="event.preventDefault();
											logoutForm()"><?php echo e(trans('main.Signout')); ?></a>
                                            <form id="logout-form" action="<?php echo e(url('/logout')); ?>" method="POST"
                                                  style="display: none;">
                                                <?php echo e(csrf_field()); ?>

                                            </form>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-lg-8 col-md-8">
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

                                    <?php if(session('incorrect_data')): ?>
                                        <div class="alert alert-danger" style="text-align: center">
                                            <?php echo e(session('incorrect_data')); ?>

                                        </div>
                                    <?php endif; ?>

                                    <form class="form-horizontal" action="<?php echo e(url('/users/'.$user->id)); ?>" method="post"
                                          enctype="multipart/form-data">
                                        <?php echo csrf_field(); ?>


                                        <input type="hidden" name="user_id" value="<?php echo e($user->id); ?>">

                                        <div class="form-group">
                                            <label for="email" class="col-sm-2 control-label"><?php echo e(trans('main.Email')); ?></label>

                                            <div class="col-sm-10">
                                                <input type="email" name="email"
                                                       value="<?php echo e($errors != null && count($errors) > 0 ? old('email') : $user->email); ?>"
                                                       class="form-control" id="email" placeholder="Email" required>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="name" class="col-sm-2 control-label"><?php echo e(trans('main.Name')); ?></label>

                                            <div class="col-sm-10">
                                                <input type="text" name="name"
                                                       value="<?php echo e($errors != null && count($errors) > 0 ? old('name') : $user->name); ?>"
                                                       class="form-control" id="name" placeholder="Name" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="password" class="col-sm-2 control-label"><?php echo e(trans('main.Password')); ?></label>
                                            <div class="col-sm-10">
                                                <input type="password" name="password" class="form-control"
                                                       placeholder="password">
                                            </div>
                                        </div>
										
										
                                        
                                        <div class="form-group">
                                            <label for="education" class="col-sm-2 control-label"><?php echo e(trans('main.Education')); ?></label>
                                            <div class="col-sm-10">
                                                <input type="text" name="education"
                                                       value="<?php echo e($errors != null && count($errors) > 0 ? old('education') : $user->education); ?>"
                                                       class="form-control" placeholder="Education">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="location" class="col-sm-2 control-label"><?php echo e(trans('main.Location')); ?></label>
                                            <div class="col-sm-10">
                                                <input type="text" name="location"
                                                       value="<?php echo e($errors != null && count($errors) > 0 ? old('location') : $user->location); ?>"
                                                       class="form-control" placeholder="Location">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="note" class="col-sm-2 control-label"><?php echo e(trans('main.Note')); ?></label>
                                            <div class="col-sm-10">
                                                <input type="text" name="note"
                                                       value="<?php echo e($errors != null && count($errors) > 0 ? old('note') : $user->note); ?>"
                                                       class="form-control" placeholder="Note">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="password" class="col-sm-2 control-label"><?php echo e(trans('main.Image')); ?></label>

                                            <div class="col-sm-10">
                                                <input id="avatar" name="avatar" type="file" class="file"
                                                       data-show-upload="false">
                                            </div>
                                        </div>
                                        <input type="text" hidden name="edit_profile" value="1">

                                        <div class="form-group">
                                            <div class="col-sm-offset-2 col-sm-10">
                                                <button type="submit" class="btn btn-danger"><?php echo e(trans('main.Submit')); ?></button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div role='tabpanel' class="tab-pane fade" id="history_tasks_tab">
                            <?php echo $__env->make('component.list_tasks_for_profile', ['userName' => $user->name, 'userId' => $user->id, 'tasks' => $tasks], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        </div>

                        <div role='tabpanel' class="tab-pane fade" id="history_tours_tab">
                            <?php echo $__env->make('component.list_tours_for_profile', ['userName' => $user->name, 'userId' => $user->id, 'tours' => $tours], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        </div>

                        <div class="tab-pane fade in" id="timeline">
                            <ul class="timeline timeline-inverse">
                                <?php $__currentLoopData = $activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li class="time-label">
                                        <span class="bg-green"><?php echo e($activity->updated_at->format('d-m-Y')); ?></span>
                                    </li>
                                    <li>
                                        <i class="fa fa-user bg-aqua"></i>
                                        <div class="timeline-item">
                                                <span class="time">
                                                    <i class="fa fa-clock-o"></i>
                                                    <?php echo e($activity->updated_at->format('H:i')); ?>

                                                </span>
                                            <h3 class="timeline-header"><?php echo e($activity->log_name); ?></h3>
                                            <div class="timeline-body"><?php echo e($activity->description); ?></div>
                                            <?php if($activity->getExtraProperty('link')): ?>
                                                <div class="timeline-footer">
                                                    <a href="<?php echo e($activity->getExtraProperty('link')); ?>"
                                                       class="btn btn-primary btn-xs"><?php echo e(trans('main.Seemore')); ?></a>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                            <?php echo e($activities->links()); ?>

                        </div>

                        <div role='tabpanel' class="tab-pane fade" id="notifications">
                            <?php echo $__env->make('component.list_notifications_profile', ['userName' => $user->name, 'userId' => $user->id, 'notifications' => $notifications], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('post_scripts'); ?>
    <script src="<?php echo e(asset('js/profile.js')); ?>"></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('scaffold-interface.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/profile/show.blade.php ENDPATH**/ ?>