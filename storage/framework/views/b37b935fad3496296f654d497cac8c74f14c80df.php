<div style="margin-left: 25px; ">
    <?php $__currentLoopData = $childs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="item nesting-<?php echo e($nesting); ?>">
            <div class="chat-title
                <?php if($child->id == $comment->id): ?>
                    active-announcement
                <?php endif; ?>
                    ">
                <h4>
                    <?php echo e($child->title); ?>

                </h4>
            </div>
            <div class="chat-details">
                <span class="chat-author">by
                    <b><?php echo e(\App\User::find($child->author)->first()->name); ?></b>
                </span>
                <span class="chat-date pull-right">
                    <i class="fa fa-clock-o" style="margin-right: 5px"></i>
                    <i><?php echo e($child->created_at); ?></i>
                </span>
            </div>
            <div class="chat-content">
                <?php echo $child->content; ?>

            </div>
            <div class="chat-attachments">
                <table class="table">
                    <tbody>


                    <?php $__currentLoopData = $child->files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attach): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="del-container">
                            <td class="td_link_attach">
                                <div class="td_link_attach__name">
                                    <a class="name_attach" href="<?php echo e('public/'.$attach->attach->url()); ?>" target="_blank">
                                        <span class="glyphicon glyphicon-paperclip"></span>
                                        <?php echo e($attach->attach_file_name); ?>

                                    </a>
                                </div>
                            </td>

                            
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <div class="announcement-actions">
                <a href="#content" data-comment-id="<?php echo e($child->id); ?>" data-parent-name="<?php echo e(\App\User::find($child->author)->first()->name); ?>" class="link-black text-sm reply_comment" style="margin-top: 10px"><i class="fa fa-reply margin-r-5"></i> Reply</a>
            </div>
            <?php if(count($child->childs)): ?>
                <?php echo $__env->make('comments.childs',['childs' => $child->childs, 'nesting' => $nesting+1], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php endif; ?>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div><?php /**PATH /var/www/html/resources/views/comments/childs.blade.php ENDPATH**/ ?>