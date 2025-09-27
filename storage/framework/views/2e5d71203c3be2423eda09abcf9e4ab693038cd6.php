<?php $__env->startSection('title','Index'); ?>
<?php $__env->startSection('content'); ?>
<?php echo $__env->make('layouts.title',
['title' => 'Images', 'sub_title' => 'Images List',
'breadcrumbs' => [
['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
['title' => 'Images', 'icon' => 'image', 'route' => null]]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<section class="content">
    <div class="box box-primary">
        <div class="box-body">
            <div class="row">
                <?php if(Session::has('message')): ?>
                <div class="alert alert-danger"><center><?php echo e(Session::get('message')); ?></center></div>
                <?php endif; ?>
                <form action="upload.php" method="post" enctype="multipart/form-data">
                    
                    <?php $__currentLoopData = $attachmenttypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attachmenttype): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-3">
                        <div class="thumbnail text-center">
                           
                            <h3><?php echo e($attachmenttype->name); ?> </h3>
                            
                            <img class="pic" src="<?php if($attachmenttype->attachments()->first() != null): ?> <?php echo e($attachmenttype->attachments()->first()->url); ?> <?php endif; ?>" alt="<?php echo e($attachmenttype->name); ?>" style="width:100%">
                            <div class="caption">
                                
                                <div class="upload-btn-wrapper">
                                    
                                    <input name="fileToUpload[]" data-name="<?php echo e($attachmenttype->model); ?>" data-id="<?php echo e($attachmenttype->id); ?>" data-model="Attachmenttype" class="fileToUpload"type="file" name="myfile" />
                                </div>                                
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                </form>
                <span id="url" hidden data-url="<?php echo e(route('images.savefile')); ?>"></span>
            </div>    
        </div>
    </div>
    <?php $__env->startPush('scripts'); ?>
    <script type="text/javascript" src='<?php echo e(asset('js/attachments.js')); ?>'></script>
    <?php $__env->stopPush(); ?>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('scaffold-interface.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/attachment/index.blade.php ENDPATH**/ ?>