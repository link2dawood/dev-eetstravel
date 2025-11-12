

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><?php echo e($announcement->title); ?></h3>
                    <div class="card-tools pull-right">
                        <a href="<?php echo e(route('announcements.index')); ?>" class="btn btn-sm btn-default">
                            <i class="fa fa-arrow-left"></i> Back
                        </a>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $announcement)): ?>
                            <a href="<?php echo e(route('announcements.edit', $announcement)); ?>" class="btn btn-sm btn-warning">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chat">
                        <div class="item">
                            <div class="chat-details">
                                <span class="chat-author">
                                    by <b><?php echo e(optional(\App\User::find($announcement->author))->name ?? 'Unknown'); ?></b>
                                </span>
                                <span class="chat-date">
                                    <i><?php echo e($announcement->created_at); ?></i>
                                </span>
                            </div>

                            <div class="chat-content">
                                <?php echo $announcement->content; ?>

                            </div>

                            
                            

                            <div class="announcement-actions">
                                
                            </div>

                            <?php
                                $childs = $announcement->childs()->get();
                            ?>
                            <?php if($childs && $childs->isNotEmpty()): ?>
                                <?php echo $__env->make('announcements.childs', ['childs' => $childs, 'nesting' => 1], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xamppp\htdocs\dev-eetstravel\resources\views/announcements/show.blade.php ENDPATH**/ ?>