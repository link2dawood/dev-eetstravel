<div class="chat">
                    <div class="item">
                        <div class="chat-details">
                            <span class="chat-author">
                                by <b><?php echo e(\App\User::find($announcement->author)->name); ?></b>
                            </span>
                            <span class="chat-date">
                               <i><?php echo e($announcement->created_at); ?></i>
                            </span>
                        </div>
                        <div class="chat-content">
                            <?php echo $announcement->content; ?>


                        </div>
                        <div class="chat-attachments">
                            <table class="table">
                                <tbody>


                            <?php $__currentLoopData = $announcement->files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attach): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="del-container">
                                    <td class="td_link_attach">
                                        <div class="td_link_attach__name">
                                            <a class="name_attach" href="<?php echo e($attach->attach->url()); ?>" target="_blank">
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
                                

                            </div>

        <?php if(count($announcement->childs)): ?>
            <?php echo $__env->make('announcements.childs',['childs' => $announcement->childs, 'nesting' => 1], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php endif; ?>
    </div>
</div><?php /**PATH /var/www/html/resources/views/announcements/show_main.blade.php ENDPATH**/ ?>