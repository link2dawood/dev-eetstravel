<?php $__env->startSection('title', 'Method Not Allowed'); ?>
<?php $__env->startSection('content'); ?>
<section class="content">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="fa fa-ban"></i>
                        405 - Method Not Allowed
                    </h3>
                </div>
                <div class="box-body text-center">
                    <div style="font-size: 120px; color: #dd4b39; margin: 20px 0;">
                        <i class="fa fa-ban"></i>
                    </div>

                    <h2>Method Not Allowed</h2>
                    <p class="lead">The request method is not supported for this resource.</p>

                    <div class="row" style="margin-top: 30px;">
                        <div class="col-sm-6">
                            <a href="<?php echo e(url('/home')); ?>" class="btn btn-primary btn-lg">
                                <i class="fa fa-dashboard"></i>
                                Go to Dashboard
                            </a>
                        </div>
                        <div class="col-sm-6">
                            <a href="javascript:history.back()" class="btn btn-default btn-lg">
                                <i class="fa fa-arrow-left"></i>
                                Go Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('scaffold-interface.layouts.tabler-app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/errors/405.blade.php ENDPATH**/ ?>