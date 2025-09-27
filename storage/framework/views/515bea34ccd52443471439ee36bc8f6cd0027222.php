<?php if(!empty($mainParents)): ?>
    <div class='chat'>
        <?php $__currentLoopData = $mainParents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class='item'>
                <div class='chat-details'>
                    <span class='chat-author'>
                        by <b><?php echo e(\App\User::find($comment->author)->first()->name); ?></b>
                    </span>
                    <span class='chat-date pull-right'>
                        <i class='fa fa-clock-o' style='margin-right: 5px'></i>
                        <i><?php echo e($comment->created_at); ?></i>
                    </span>
                </div>
                <div class='chat-content'>
                    <?php echo $comment->content; ?>

                </div>
                <div class='chat-attachments'>
                    <table class='table'>
                        <tbody>
                        <?php $__currentLoopData = $comment->files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attach): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class='del-container'>
                                <td class='td_link_attach'>
                                    <div class='td_link_attach__name'>
                                        <a class='name_attach' href='<?php echo e("public".$attach->attach->url()); ?>' target='_blank'>
                                            <span class='glyphicon glyphicon-paperclip'></span>
                                            <?php echo e($attach->attach_file_name); ?>

                                        </a>
                                    </div>

                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <div class='announcement-actions'>
                    <a href='#content' data-comment-id='<?php echo e($comment->id); ?>' data-parent-name='<?php echo e(\App\User::find($comment->author)->first()->name); ?>' class='link-black text-sm reply_comment' style='margin-top: 10px'><i class='fa fa-reply margin-r-5'></i> <?php echo trans('main.Reply'); ?></a>
                </div>
                <?php if(count($comment->childs)): ?>
                    <?php echo $__env->make('comments.childs',['childs' => $comment->childs, 'nesting' => 1], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <?php endif; ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
<?php endif; ?><?php /**PATH /var/www/html/resources/views/comments/show_tree_view_all.blade.php ENDPATH**/ ?>