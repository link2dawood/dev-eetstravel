<?php $__env->startSection('title','Show'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('layouts.title',
   ['title' => 'Task', 'sub_title' => $task->tourName() ? $task->tourName() : 'Without Tour',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Tour Tasks', 'icon' => 'tasks', 'route' => route('task.index')],
   ['title' => 'Show', 'route' => null]]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="margin_button">
                            <a href="javascript:history.back()">
                                <button class='btn btn-primary'><?php echo trans('main.Back'); ?></button>
                            </a>
                            <?php if(Auth::user()->can('task.edit')): ?>
                            <a href="<?php echo route('task.edit', ['task' => $task->id]); ?>">
                                <button class='btn btn-warning'><?php echo trans('main.Edit'); ?></button>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <table class = 'table table-bordered'>
                    <tbody>
                    <tr>
                        <td>
                            <b><i><?php echo trans('main.Content'); ?> : </i></b>
                        </td>
                        <td><?php echo $task->content; ?></td>
                    </tr>
                    <tr>
                        <td>
                            <b><i><?php echo trans('main.Deadline'); ?> : </i></b>
                        </td>
                        <td><?php echo $task->dead_line; ?></td>
                    </tr>
                    <tr>
                        <td>
                            <b><i><?php echo trans('main.Tour'); ?> : </i></b>
                        </td>
                        <td><?php echo $task->tourModel ? $task->tourModel->name : 'Without Tour'; ?></td>
                    </tr>
                    <tr>
                        <td><b><i><?php echo trans('main.Assignto'); ?>: </i></b></td>
                        
                        <td>
                            <?php $__currentLoopData = $task->assigned_users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php echo e($user->name . ' '); ?>

                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b><i><?php echo trans('main.TaskType'); ?> : </i></b>
                        </td>
                        <td><?php echo \App\Task::$taskTypes[$task->task_type]; ?></td>
                    </tr>
                    <tr>
                        <td><b><i><?php echo trans('main.Status'); ?> : </i></b></td>
                        <td><?php echo e($status->name); ?></td>
                    </tr>
                    <tr>
                        <td><b><i><?php echo trans('main.Priority'); ?> : </i></b></td>
                        <td><?php echo e($task->priority ? 'Yes' : 'No'); ?></td>
                    </tr>
                    </tbody>
                </table>
                <?php $__env->startComponent('component.files', ['files' => $files]); ?><?php echo $__env->renderComponent(); ?>
            </div>
        </div>
        <span id="showPreviewBlock" data-info="<?php echo e(true); ?>"></span>
        <div class="box box-success" style="position: relative; left: 0px; top: 0px;">
                <div class="box-header ui-sortable-handle" style="cursor: move;">
                    <i class="fa fa-comments-o"></i>

                    <h3 class="box-title"><?php echo trans('main.Comments'); ?></h3>
                </div>
                <div class="box-body">
                    <div class="slimScrollDiv" style="position: relative; overflow-y: scroll;  width: auto;">
                        <div class="box-body box chat" id="chat-box" style="width: auto; height: auto;">
                            <div id="show_comments"></div>
                        </div>
                        <div class="slimScrollRail" style="width: 7px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; background: rgb(51, 51, 51) none repeat scroll 0% 0%; opacity: 0.2; z-index: 90; right: 1px;"></div>
                    </div>
                </div>
                <!-- /.chat -->
                <div class="box-footer">
                    <form method='POST' action='<?php echo e(route('comment.store')); ?>' enctype="multipart/form-data" id="form_comment">
                        <div class="input-group" style="width: 100%">
                                        <span id="author_name" class="input-group-addon">
                                            <span id="name"></span>
                                            <a href="#" id="reply_close"><i class="fa fa-close"></i></a>
                                        </span>
                            <textarea class="form-control" id="content" name="content" placeholder="Ctrl + Enter to post comment"></textarea>
                        </div>
                        <div class="form-group">
                            <label><?php echo trans('main.Files'); ?></label>
                            <?php $__env->startComponent('component.file_upload_field'); ?><?php echo $__env->renderComponent(); ?>
                        </div>
                        <input type="text" id="parent_comment" hidden name="parent" value="<?php echo e(null); ?>">
                        <input type="text" id="default_reference_id" hidden name="reference_id" value="<?php echo e($task->id); ?>">
                        <input type="text" id="default_reference_type" hidden name="reference_type" value="<?php echo e(\App\Comment::$services['task']); ?>">

                        <button type="submit" class="btn btn-success pull-right" id="btn_send_comment" style="margin-top: 5px;"><?php echo trans('main.Send'); ?></button>
                    </form>
                </div>
            </div>
    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('post_scripts'); ?>
    <script src="<?php echo e(asset('js/comment.js')); ?>"></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('scaffold-interface.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/task/show.blade.php ENDPATH**/ ?>