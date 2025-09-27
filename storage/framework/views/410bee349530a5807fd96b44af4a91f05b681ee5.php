<?php $__env->startSection('title','Index'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('layouts.title',
    ['title' => 'Comments', 'sub_title' => 'Comments List',
    'breadcrumbs' => [
    ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
    ['title' => 'Comments', 'icon' => 'comment', 'route' => null]]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <?php if(session('not_found')): ?>
                    <div class="alert alert-info">
                        <?php echo e(session('not_found')); ?>

                    </div>
                <?php endif; ?>
                <table id="comment-table" class="table table-striped table-bordered table-hover" style='background:#fff'>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th><?php echo trans('main.Content'); ?></th>
                        <th><?php echo trans('main.Time'); ?></th>
                        <th><?php echo trans('main.Sender'); ?></th>
                        <th class="actions-button" style="width: 140px"><?php echo trans('main.Actions'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $__currentLoopData = $commentsData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($comment->id); ?></td>
                            <td><?php echo e($comment->content); ?></td>
                            <td><?php echo e($comment->created_at); ?></td>
                            <td><?php echo e($comment->sender); ?></td>
                            <td><?php echo $comment->action_buttons; ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <th class="not"></th>
                        <th><?php echo trans('main.Content'); ?></th>
                        <th><?php echo trans('main.Time'); ?></th>
                        <th><?php echo trans('main.Sender'); ?></th>
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
        let table = $('#comment-table').DataTable({
            dom: 	"<'row'<'col-sm-5'l><'col-sm-2'B><'col-sm-5'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            "deferRender": true,
            buttons: [
                {
                    extend: 'csv',
                    title: 'Comments List',
                    exportOptions: {
                        columns: ':not(.actions-button)'
                    }
                },
                {
                    extend: 'excel',
                    title: 'Comments List',
                    exportOptions: {
                        columns: ':not(.actions-button)'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Comments List',
                    exportOptions: {
                        columns: ':not(.actions-button)'
                    }
                }
            ],
            pageLength: 50,
            processing: true,
            "bJQueryUI": true,
            serverSide: true,
            ajax: {
                url: "<?php echo e(route('comment_data')); ?>",
            },
            columns: [
                {data: 'id', name: 'comments.id'},
                {data: 'content', type:'html', },
                {data: 'created_at', name: 'comments.created_at'},
                {data: 'sender', name: 'users.name'},
                {data: 'action', name: 'action', searchable: false, sorting: false, orderable: false}
            ],
        });
        $('#comment-table tfoot th').each( function () {
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
        $('#comment-table tfoot th').appendTo('#comment-table thead');
    })
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('scaffold-interface.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/comments/index.blade.php ENDPATH**/ ?>