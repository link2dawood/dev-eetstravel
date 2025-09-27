<?php $__env->startSection('title','Show'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('layouts.title',
       ['title' => 'Tax', 'sub_title' => 'Tax Show',
       'breadcrumbs' => [
       ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
       ['title' => 'Taxes', 'icon' => null, 'route' => route('taxes.index')],
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
                            <a href="<?php echo route('taxes.edit', $tax->id); ?>">
                                <button class='btn btn-warning'><?php echo trans('main.Edit'); ?></button>
                            </a>
                        </div>
                    </div>
                </div>
                <table class = 'table table-bordered'>
                    <tbody>
                    <tr>
                        <td class="show_width_td">
                            <b><i><?php echo trans('Tax Name'); ?> : </i></b>
                        </td>
                        <td><?php echo $tax->name; ?></td>
                    </tr>
                    <tr>
                        <td class="show_width_td">
                            <b><i><?php echo trans('Value'); ?> : </i></b>
                        </td>
                        <td><?php echo $tax->value; ?></td>
                    </tr>
                   
                    </tbody>
                </table>
            </div>
        </div>
     
    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('post_scripts'); ?>
    <script src="<?php echo e(asset('js/comment.js')); ?>"></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('scaffold-interface.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/taxes/show.blade.php ENDPATH**/ ?>