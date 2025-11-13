<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('layouts.title',
   ['title' => 'Announcement', 'sub_title' => $announcement->title,
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Announcements', 'icon' => 'coffee', 'route' => route('announcements.index')],
   ['title' => 'Show', 'route' => null]]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <section class="content">
        
        <span id="showPreviewBlock" data-info="<?php echo e(true); ?>"></span>
        <div class="box box-success" style="position: relative; left: 0px; top: 0px;">
            <div class="box-header ui-sortable-handle" style="cursor: move;">
                <i class="fa fa-comments-o"></i>

                <h3 class="box-title"><?php echo trans('main.Announcement'); ?></h3>
            </div>
            <div class="box-body">
                <div style="margin-bottom: 10px;">
                    <a href="javascript:history.back()">
                        <button class='btn btn-primary'><?php echo trans('main.Back'); ?></button>
                    </a>
                    <?php if(\Auth::id() == $announcement->author): ?>
                    <a href="<?php echo route('announcements.edit', $announcement->id); ?>">
                        <button class='btn btn-warning'><?php echo trans('main.Edit'); ?></button>
                    </a>
                    <?php endif; ?>
                </div>
                <div class="slimScrollDiv" style="position: relative; overflow-y: scroll;  width: auto;">
                    <div class="box-body box chat" id="chat-box" style="width: auto; height: auto;">
                        <div id="show_comments"></div>
                    </div>
                    <div class="slimScrollRail" style="width: 7px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; background: rgb(51, 51, 51) none repeat scroll 0% 0%; opacity: 0.2; z-index: 90; right: 1px;"></div>
                </div>
            </div>
            <!-- /.chat -->
            <div class="box-footer">
                <form method='post' action='<?php echo e(route('announcement_reply', ['id' => $announcement->id])); ?>' enctype="multipart/form-data" id="form_comment">
                <?php echo e(csrf_field()); ?>

                    <div class="input-group" style="width: 100%">
                                        <span id="author_name" class="input-group-addon">
                                            <span id="name"></span>
                                            <a href="#" id="reply_close"><i class="fa fa-close"></i></a>
                                        </span>
                        
                        <textarea class="form-control" id="content" name="content" placeholder="Ctrl + Enter to post comment"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Files</label>
                        <?php $__env->startComponent('component.file_upload_field'); ?><?php echo $__env->renderComponent(); ?>
                    </div>
                    <input type="text" id="parent_comment" hidden name="parent_id" value="<?php echo e($announcement->id); ?>">
                    
                    
                    <input type="text" id="id_comment" hidden name="id_comment" value="<?php echo e($announcement->id); ?>">

                    <button type="submit" class="btn btn-success pull-right"  style="margin-top: 5px;"><?php echo trans('main.Send'); ?></button>
                </form>
            </div>
        </div>
        <span id="announcements" data-announ-id="<?php echo e($announcement->id); ?>"></span>
    </section>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('js/comment.js')); ?>"></script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('scaffold-interface.layouts.tabler-app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/announcements/show_tree_view.blade.php ENDPATH**/ ?>