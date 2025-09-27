<?php $__env->startSection('title','Show'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('layouts.title',
       ['title' => 'Criteria', 'sub_title' => 'Criteria Show',
       'breadcrumbs' => [
       ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
       ['title' => 'Criteria', 'icon' => null, 'route' => route('criteria.index')],
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
                            <a href="<?php echo route('criteria.edit', $criteria->id); ?>">
                                <button class='btn btn-warning'><?php echo trans('main.Edit'); ?></button>
                            </a>
                        </div>
                    </div>
                </div>
                <table class = 'table table-bordered'>
                    <tbody>
                    <tr>
                        <td class="show_width_td">
                            <b><i><?php echo trans('main.Name'); ?> : </i></b>
                        </td>
                        <td><?php echo $criteria->name; ?></td>
                    </tr>
                    <tr>
                        <td class="show_width_td">
                            <b><i><?php echo trans('main.ShortName'); ?> : </i></b>
                        </td>
                        <td><?php echo $criteria->short_name; ?></td>
                    </tr>
                    <tr>
                        <td class="show_width_td">
                            <b><i><?php echo trans('main.Icon'); ?> : </i></b>
                        </td>
                        <td><?php echo $criteria->icon; ?></td>
                    </tr>
                    <tr>
                        <td class="show_width_td">
                            <b><i><?php echo trans('main.CriteriaType'); ?> : </i></b>
                        </td>
                        <td><?php echo $criteria_type == null ? '' : $criteria_type->name; ?></td>
                    </tr>
                    </tbody>
                </table>
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
                    <input type="text" id="default_reference_id" hidden name="reference_id" value="<?php echo e($criteria->id); ?>">
                    <input type="text" id="default_reference_type" hidden name="reference_type" value="<?php echo e(\App\Comment::$services['criteria']); ?>">

                    <button type="submit" class="btn btn-success pull-right" id="btn_send_comment" style="margin-top: 5px;"><?php echo trans('main.Send'); ?></button>
                </form>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('post_scripts'); ?>
    <script src="<?php echo e(asset('js/comment.js')); ?>"></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('scaffold-interface.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/criteria/show.blade.php ENDPATH**/ ?>