<?php $__env->startSection('title','Index'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('layouts.title',
           ['title' => 'Taxes', 'sub_title' => 'Taxes List',
           'breadcrumbs' => [
           ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
           ['title' => 'Currencies', 'icon' => null, 'route' => null]]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <section class="content">
        <div class="box box-primary">
            <div class="box-body table-responsive">
                <div>
                    <?php echo \App\Helper\PermissionHelper::getCreateButton(route('taxes.create'), \App\Currencies::class); ?>

                </div>
                <br>
                <br>
                <table id="currencies-table" class="table table-striped table-bordered table-hover" style='background:#fff; width: 100%;'>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th><?php echo trans('Name'); ?></th>
                        <th><?php echo trans('Value'); ?></th>
                        <th class="actions-button" style="width: 140px!important"><?php echo trans('main.Actions'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $__currentLoopData = $taxesData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tax): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($tax->id); ?></td>
                            <td><?php echo e($tax->name); ?></td>
                            <td><?php echo e($tax->value); ?></td>
                            <td><?php echo $tax->action_buttons; ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <th class="not"></th>
                        <th><?php echo trans('Name'); ?></th>
						<th><?php echo trans('Value'); ?></th>
                        <th class="not"></th>
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
        let table = $('#currencies-table').DataTable({
            dom: 	"<'row'<'col-sm-5'l><'col-sm-2'B><'col-sm-5'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            buttons: [
                {
                    extend: 'csv',
                    title: 'Taxes List',
                    exportOptions: {
                        columns: ':not(.actions-button)'
                    }
                },
                {
                    extend: 'excel',
                    title: 'Taxes List',
                    exportOptions: {
                        columns: ':not(.actions-button)'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Taxes List',
                    exportOptions: {
                        columns: ':not(.actions-button)'
                    }
                }
            ],
            pageLength: 50,
            columnDefs: [
                { targets: [3], orderable: false } // Actions column not sortable
            ]
        });
        $('#currencies-table tfoot th').each( function () {
            let column = this;
            if (column.className !== 'not') {
                let title = $(this).text();
                $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');
            }
        });
        table.columns().every( function () {
            let that = this;

            $('input', this.footer()).on('keyup change', function() {
                if(that.search() !== this.value) {
                    that.search(this.value).draw();
                }
            });
        });
        $('#currencies-table tfoot th').appendTo('#currencies-table thead');
    })
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('scaffold-interface.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/taxes/index.blade.php ENDPATH**/ ?>