
<?php $__env->startSection('title','Edit Task'); ?>
<?php $__env->startSection('content'); ?>
    <?php
        $tab = '' ;
        $uri_parts = explode('?', \Request::fullUrl() );
        if(count($uri_parts)>1){
            $tab_parts = explode('=', $uri_parts[1]);
            if($tab_parts[0] == 'tab') $tab = $uri_parts[1];
        }
    ?>

    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?php echo e(url('/home')); ?>"><i class="ti ti-home"></i> Home</a></li>
                                <li class="breadcrumb-item"><a href="<?php echo e(route('task.index')); ?>">Tasks</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Edit</li>
                            </ol>
                        </nav>
                    </div>
                    <h2 class="page-title">Edit Task</h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="javascript:history.back()" class="btn btn-primary">
                            <i class="ti ti-arrow-left"></i> <?php echo trans('main.Back'); ?>

                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <?php if(count($errors) > 0): ?>
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <div class="d-flex">
                                <div>
                                    <i class="ti ti-alert-circle icon alert-icon"></i>
                                </div>
                                <div>
                                    <h4 class="alert-title">Validation Error</h4>
                                    <ul class="mb-0">
                                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li><?php echo e($error); ?></li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                </div>
                            </div>
                            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                        </div>
                    <?php endif; ?>

                    <form 
                        method="POST" 
                        action="<?php echo e(url('task')); ?>/<?php echo e($task->id); ?>/update" 
                        id="task-form"
                        enctype="multipart/form-data"
                    >
                        <?php echo csrf_field(); ?>
                        <input type="hidden" id="tab" name="tab" value="<?php echo e($tab); ?>">
                        <input type="hidden" name="calendar_edit" value="<?php echo e($calendar_edit); ?>">

                        <div class="row">
                            <div class="col-md-8">
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="ti ti-checklist me-2"></i> Task Details</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group mb-3">
                                                    <label class="form-label required"><?php echo e(trans('main.Content')); ?></label>
                                                    <textarea 
                                                        name="content" 
                                                        class="form-control" 
                                                        rows="5" 
                                                        required
                                                    ><?php echo e($errors != null && count($errors) > 0 ? old('content') : $task->content); ?></textarea>
                                                    <small class="form-hint">Describe the task in detail</small>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group mb-3">
                                                    <label class="form-label"><?php echo e(trans('main.Tour')); ?></label>
                                                    <select name="tour" class="form-select" placeholder="Without Tour">
                                                        <option value="">Without Tour</option>
                                                        <?php $__currentLoopData = $tours; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tour): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <option value="<?php echo e($tour->id); ?>" <?php echo e($task->tour_id == $tour->id ? 'selected' : ''); ?>>
                                                                <?php echo e($tour->name); ?>

                                                            </option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="ti ti-calendar me-2"></i> Schedule</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="end_date" class="form-label"><?php echo trans('main.Deadline'); ?></label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="ti ti-calendar"></i>
                                                        </span>
                                                        <input 
                                                            type="text" 
                                                            name="end_date" 
                                                            id="end_date" 
                                                            class="form-control datepicker" 
                                                            value="<?php echo e($task->end_date); ?>"
                                                            autocomplete="off"
                                                        >
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="end_time" class="form-label"><?php echo trans('main.Time'); ?></label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="ti ti-clock"></i>
                                                        </span>
                                                        <input 
                                                            type="text" 
                                                            name="end_time" 
                                                            id="end_time" 
                                                            class="form-control timepicker" 
                                                            value="<?php echo e($task->end_time); ?>"
                                                        >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="ti ti-notes me-2"></i> Additional Details</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label class="form-label required"><?php echo e(trans('main.Tasktype')); ?></label>
                                                    <select name="task_type" class="form-select" required>
                                                        <option value="" disabled>Select a type</option>
                                                        <?php $__currentLoopData = \App\Task::$taskTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <option value="<?php echo e($id); ?>" <?php echo e($task->task_type == $id ? 'selected' : ''); ?>>
                                                                <?php echo e($name); ?>

                                                            </option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">Story Points</label>
                                                    <input 
                                                        type="number" 
                                                        name="story_points" 
                                                        class="form-control" 
                                                        value="<?php echo e(old('story_points', $task->story_points)); ?>"
                                                        min="0"
                                                        max="100"
                                                    >
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="form-label"><?php echo trans('main.Files'); ?></label>
                                                    <?php $__env->startComponent('component.file_upload_field'); ?><?php echo $__env->renderComponent(); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php if(isset($files) && count($files) > 0): ?>
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="ti ti-paperclip me-2"></i> Attached Files</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="col-12">
                                            <?php $__env->startComponent('component.files', ['files' => $files]); ?><?php echo $__env->renderComponent(); ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>

                            <div class="col-md-4">
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="ti ti-users me-2"></i> Assignment</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label required"><?php echo trans('main.AssignedUser'); ?></label>
                                                <div class="form-selectgroup form-selectgroup-boxes d-flex flex-column gap-2">
                                                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <label class="form-selectgroup-item flex-fill">
                                                        <input 
                                                            type="checkbox" 
                                                            name="assigned_user" 
                                                            id="user_<?php echo e($user->id); ?>" 
                                                            value="<?php echo e($user->id); ?>"
                                                            class="form-selectgroup-input user_checkboxes"
                                                            <?php echo e($user->selected ? 'checked' : ''); ?>

                                                        >
                                                        <div class="form-selectgroup-label d-flex align-items-center p-3">
                                                            <div class="me-3">
                                                                <span class="form-selectgroup-check"></span>
                                                            </div>
                                                            <div class="form-selectgroup-label-content d-flex align-items-center">
                                                                <?php if($user->gravatar): ?>
                                                                <span class="avatar me-2" style="background-image: url(<?php echo e($user->gravatar); ?>)"></span>
                                                                <?php else: ?>
                                                                <span class="avatar me-2"><?php echo e(substr($user->name, 0, 2)); ?></span>
                                                                <?php endif; ?>
                                                                <div>
                                                                    <div><?php echo e($user->name); ?></div>
                                                                    <div class="text-muted small"><?php echo e($user->email); ?></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </label>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="ti ti-flag me-2"></i> Status & Priority</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="col-12">
                                            <div class="form-group mb-3">
                                                <label class="form-label required"><?php echo e(trans('main.Status')); ?></label>
                                                <?php
                                                    $statusOptions = $statuses->pluck('name', 'id')->toArray();
                                                    $currentStatus = ($errors != null && count($errors) > 0 ? old('status') : $task->status);
                                                ?>
                                                <select name="status" class="form-select" required>
                                                    <option value="" disabled <?php echo e(!$currentStatus ? 'selected' : ''); ?>>Select a status</option>
                                                    <?php $__currentLoopData = $statusOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($id); ?>" <?php echo e($currentStatus == $id ? 'selected' : ''); ?>>
                                                            <?php echo e($name); ?>

                                                        </option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label">Priority</label>
                                                <div class="form-check form-switch">
                                                    <input 
                                                        class="form-check-input" 
                                                        type="checkbox" 
                                                        name="priority" 
                                                        id="priority"
                                                        value="1"
                                                        <?php echo e($task->priority ? 'checked' : ''); ?>

                                                    >
                                                    <label class="form-check-label" for="priority">
                                                        <i class="ti ti-flag text-danger"></i> Mark as High Priority
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body text-end">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="ti ti-device-floppy me-2"></i>
                                            <?php echo trans('main.Save'); ?>

                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize date picker
    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true
    });

    // Initialize time picker
    $('.timepicker').timepicker({
        showMeridian: false,
        defaultTime: '18:00'
    });

    // Validate at least one user is selected
    const form = document.getElementById('task-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const checkedUsers = document.querySelectorAll('.user_checkboxes:checked');
            if (checkedUsers.length === 0) {
                e.preventDefault();
                alert('Please assign at least one user to this task.');
                return false;
            }
        });
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('scaffold-interface.layouts.tabler-app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/task/edit.blade.php ENDPATH**/ ?>