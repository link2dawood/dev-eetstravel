<?php $__env->startSection('title','Index'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('layouts.title',
           ['title' => 'Criteria', 'sub_title' => 'Criteria List',
           'breadcrumbs' => [
           ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
           ['title' => 'Criteria', 'icon' => null, 'route' => null]]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <?php if(Session::has('message')): ?>
                    <div class="alert alert-danger"><center><?php echo e(Session::get('message')); ?></center></div>
                <?php endif; ?>
                    <div>
                        <?php echo \App\Helper\PermissionHelper::getCreateButton(route('criteria.create'), \App\Criteria::class); ?>

                    </div>
                <br>
                <br>
                <table id="criteria-table" class="table table-striped table-bordered table-hover" style='background:#fff; width: 98%; table-layout: fixed'>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th><?php echo trans('main.Name'); ?></th>
                        <th><?php echo trans('main.ShortName'); ?></th>
                        <th><?php echo trans('main.Icon'); ?></th>
                        <th><?php echo trans('main.CriteriaType'); ?></th>
                        <th class="actions-button" style="width: 140px!important"><?php echo trans('main.Actions'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $__currentLoopData = $criteriaData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $criteria): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($criteria->id); ?></td>
                            <td><?php echo e($criteria->name); ?></td>
                            <td><?php echo e($criteria->short_name); ?></td>
                            <td><?php echo $criteria->formatted_icon; ?></td>
                            <td><?php echo e($criteria->criteria_type); ?></td>
                            <td><?php echo $criteria->action_buttons; ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <th class="not"></th>
                        <th><?php echo trans('main.Name'); ?></th>
                        <th><?php echo trans('main.ShortName'); ?></th>
                        <th><?php echo trans('main.Icon'); ?></th>
                        <th><?php echo trans('main.CriteriaType'); ?></th>
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
        let table = $('#criteria-table').DataTable({
            dom: 	"<'row'<'col-sm-5'l><'col-sm-2'B><'col-sm-5'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            buttons: [
                {
                    extend: 'csv',
                    title: 'Criteria List',
                    exportOptions: {
                        columns: ':not(.actions-button)'
                    }
                },
                {
                    extend: 'excel',
                    title: 'Criteria List',
                    exportOptions: {
                        columns: ':not(.actions-button)'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Criteria List',
                    exportOptions: {
                        columns: ':not(.actions-button)'
                    }
                }
            ],
            pageLength: 50,
            columnDefs: [
                { targets: [5], orderable: false } // Actions column not sortable
            ]
        });

        $('#criteria-table tfoot th').each( function () {
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

        $('#criteria-table tfoot th').appendTo('#criteria-table thead');
    })
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('scaffold-interface.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/criteria/index.blade.php ENDPATH**/ ?>