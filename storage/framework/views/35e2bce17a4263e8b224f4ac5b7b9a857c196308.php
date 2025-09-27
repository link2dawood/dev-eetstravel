<?php $__env->startSection('title','Index'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('layouts.title',
           ['title' => 'Email', 'sub_title' => 'Email Templates',
           'breadcrumbs' => [
           ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
           ['title' => 'Email', 'icon' => 'envelope', 'route' => null]]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <table id="templates" class="table table-striped table-bordered table-hover" style='background:#fff;width: 100%;'>
                    <thead>
                    <tr>
                        <th style="width:100%"><?php echo trans('main.ServiceName'); ?></th>
                        <th><?php echo trans('main.Actions'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $__currentLoopData = $templatesData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($template->name); ?></td>
                            <td><?php echo $template->action_buttons; ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <th style="width:100%"><?php echo trans('main.ServiceName'); ?></th>
                        <th><?php echo trans('main.Actions'); ?></th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>

<script>


    $(document).ready(function() {
        $('#templates').DataTable( {
            columnDefs: [
                { targets: [1], orderable: false } // Actions column not sortable
            ]
        } );

        $('#templates').find("tfoot").remove();

        $('#templates tbody').on( 'click', 'tr', function () {
            let url = $(this).find("a").attr('href');
            window.location.href = url;
        } );

    })

</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('scaffold-interface.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/templates/index.blade.php ENDPATH**/ ?>