<section class="content-header" style="margin-bottom: 20px;">
    <?php if(!empty($sub_title)): ?>
        <h1>
            <?php echo e($title); ?>

            <small><?php echo e($sub_title); ?></small>
        </h1>
    <?php endif; ?>
        <?php if(!empty($breadcrumbs)): ?>
            <ol class="breadcrumb">
                <?php $__currentLoopData = $breadcrumbs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $breadcrumb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(empty($breadcrumb['route'])): ?>
                        <li class="active">
                            <?php if(!empty($breadcrumb['icon'])): ?>
                                <i class="fa fa-<?php echo e($breadcrumb['icon']); ?>"></i>
                            <?php endif; ?>
                                <?php echo e($breadcrumb['title']); ?>

                        </li>
                    <?php else: ?>
                        <li><a href="<?php echo e($breadcrumb['route']); ?>">
                                <?php if(!empty($breadcrumb['icon'])): ?>
                                    <i class="fa fa-<?php echo e($breadcrumb['icon']); ?>"></i>
                                <?php endif; ?>
                                <?php echo e($breadcrumb['title']); ?>

                            </a>
                        </li>
                <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ol>
        <?php endif; ?>
</section>
<script type="text/javascript" src="<?php echo e(asset('js/utils.js')); ?>"></script><?php /**PATH /var/www/html/resources/views/layouts/title.blade.php ENDPATH**/ ?>