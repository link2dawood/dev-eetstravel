<?php $__env->startSection('title','Index'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('layouts.title',
    ['title' => 'Tasks', 'sub_title' => 'Tasks List',
    'breadcrumbs' => [
    ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
    ['title' => 'Tour Tasks', 'icon' => 'tasks', 'route' => null]]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <select hidden id="task-status-search" class="select2">
        <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e(is_object($status) ? $status->id : $status); ?>"><?php echo e(is_object($status) ? $status->name : $status); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div>
                    <?php echo \App\Helper\PermissionHelper::getCreateButton(route('task.create'), \App\Task::class); ?>

                </div>
                <span id="help" class="btn btn-box-tool pull-right"><i class="fa fa-question-circle " aria-hidden="true"></i>
                    <?php echo $__env->make('legend.task_legend', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </span>
                <br>
                <br>
				<div class="table-responsive">
                <table id="tasks-table" class="table table-striped table-bordered table-hover bootstrap-table" style='background:#fff;width: 100%;'>
                    <thead>
                        <tr>
                            <th onclick="sortTable(0, 'tasks-table')">ID <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(1, 'tasks-table')" style="max-width: 250px;"><?php echo trans('main.Content'); ?> <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(2, 'tasks-table')"><?php echo trans('main.Deadline'); ?> <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(3, 'tasks-table')"><?php echo trans('main.Tour'); ?> <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(4, 'tasks-table')"><?php echo trans('main.Assignto'); ?> <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(5, 'tasks-table')"><?php echo trans('main.TaskType'); ?> <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(6, 'tasks-table')"><?php echo trans('main.Status'); ?> <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(7, 'tasks-table')"><?php echo trans('main.Priority'); ?> <i class="fa fa-sort"></i></th>
                            <th class="actions-button" style="width:150px; text-align: center;"><?php echo trans('main.Actions'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $tasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($task->id); ?></td>
                            <td style="max-width: 250px;"><?php echo e($task->content); ?></td>
                            <td><?php echo e($task->deadline ? (\Carbon\Carbon::parse($task->deadline)->format('Y-m-d H:i')) : ''); ?></td>
                            <td><?php echo e($task->tour && is_object($task->tour) ? $task->tour->name : ''); ?></td>
                            <td>
                                <?php if($task->assignedTo && is_object($task->assignedTo)): ?>
                                    <?php echo e($task->assignedTo->name); ?>

                                <?php endif; ?>
                            </td>
                            <td><?php echo e($task->task_type ? \App\Task::$taskTypes[$task->task_type] ?? $task->task_type : ''); ?></td>
                            <td>
                                <span class="badge badge-primary" style="background-color: <?php echo e($task->getStatusColor()); ?>">
                                    <?php echo e($task->getStatusName()); ?>

                                </span>
                            </td>
                            <td>
                                <?php if($task->priority): ?>
                                    <span class="badge badge-<?php echo e($task->priority == 'high' ? 'danger' : ($task->priority == 'medium' ? 'warning' : 'info')); ?>">
                                        <?php echo e(ucfirst($task->priority)); ?>

                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo $__env->make('component.action_buttons', [
                                    'show_route' => route('task.show', ['task' => $task->id]),
                                    'edit_route' => route('task.edit', ['task' => $task->id]),
                                    'delete_route' => route('task.destroy', $task->id),
                                    'model' => $task
                                ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="9" class="text-center">No tasks found</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <div class="row">
                    <div class="col-md-12">
                        <?php echo e($tasks->links()); ?>

                    </div>
                </div>
				</div>
                <span id="task_types" data-info="<?php echo e($task_types); ?>"></span>
                <span id="task_statuses" data-info="<?php echo e(json_encode($statuses)); ?>"></span>
                <span id="status_permission" data-info="<?php echo e(\App\Helper\PermissionHelper::checkPermission('task.edit') ? 'status' : ''); ?>"></span>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('js/bootstrap-tables.js')); ?>"></script>
<script>
    $(document).ready(function() {
        if($('#task-status-search').length >0){

            // $('#task-status-search').select2('destroy');
        }
        let classNameStatus = $('#status_permission').attr('data-info');
        // Initialize Bootstrap Table
        initializeBootstrapTable('tasks-table');
        /*let table = $('#tasks-table').DataTable({
            dom: "<'row'<'col-sm-5'l><'col-sm-2'B><'col-sm-5'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            buttons: [
                {
                    extend: 'csv',
                    title: 'Tasks List',
                    exportOptions: {
                        columns: ':not(.actions-button)',
                        format: {
                            body:  function(data, row, col,node)  {
                                var elementType = node.firstChild;
                                if (elementType != null) {
                                    if (elementType.nodeName === "SELECT") {
                                        data = $(elementType).find(':selected').text();
                                    }else{
                                        data= $(elementType).text();
                                    }

                                }

                                return data;
                            }
                        }
                    }
                },
                {
                    extend: 'excel',
                    title: 'Tasks List',
                    exportOptions: {
                        columns: ':not(.actions-button)',
                        format: {
                            body:  function(data, row, col,node)  {
                                var elementType = node.firstChild;
                                if (elementType != null) {
                                    if (elementType.nodeName === "SELECT") {
                                        data = $(elementType).find(':selected').text();
                                    }else{
                                        data= $(elementType).text();
                                    }

                                }

                                return data;
                            }
                        }
                    }
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Tasks List',
                    exportOptions: {
                        columns: ':not(.actions-button)',
                        format: {
                            body:  function(data, row, col,node)  {
                                var elementType = node.firstChild;
                                if (elementType != null) {
                                    if (elementType.nodeName === "SELECT") {
                                        data = $(elementType).find(':selected').text();
                                    }else{
                                        data= $(elementType).text();
                                    }

                                }

                                return data;
                            }
                        }
                    }
                }
            ],
            language : {
                search: "Global Search :"
            },
            processing: true,
            serverSide: false,
            pageLength: 50,
            //order : [ [2 , 'desc'],[7, 'desc'] ],
            order : [ [7, 'desc'] ],
            ajax: {
                url: "<?php echo e(route('task_data')); ?>",
            },
            columns: [
                {data: 'id', name: 'id'},
                {data: 'content', name: 'content'},
                {data: 'dead_line', name: 'dead_line'},
                {data: 'tour', name: 'tour', className: 'click_tour_in_task'},
                {data: 'assign', name: 'assign'},
                {data: 'task_type', name: 'task_type'},
                {data: 'status_name', name: 'status_name', className: classNameStatus},
                {data: 'priority', name: 'priority', className: 'priority', visible: false},
                {data: 'action', name: 'action', searchable: false, sorting: false, orderable: false},
            ],

            initComplete: function () {

                var task_types = $('#task_types').attr('data-info');
                var task_statuses = $('#task_statuses').attr('data-info');
                var res = JSON.parse(task_types);
                var res_task_statuses = JSON.parse(task_statuses);
                this.api().columns().every( function () {
                    var column = this;

                    if(column.footer().className == 'select_search'){
                        var select = $('<select class="form-control"><option value=""></option></select>')
                            .appendTo( $(column.footer()).empty() )
                            .on( 'change', function () {
                                var val = $.fn.dataTable.util.escapeRegex( $(this).find(":selected").text() );

                                column.search( val ? '^'+val+'$' : '', true, false ).draw();
                            });

                        column.data().unique().sort().each( function ( d, j ) {
                            if(column.footer().getAttribute("data-info")  == 'task_types'){
                                select.append( '<option value="'+res[d]+'">'+d+'</option>' )
                            }else{
                                select.append( '<option value="'+d+'">'+d+'</option>' );
                                console.log("d: " + d + " j: " + j)
                            }
                        });
                    }
//
//                    if(column.footer().className == 'click_in_task2'){
//                        var select_status = $('<select class="form-control"><option value=""></option></select>')
//                            .appendTo( $(column.footer()).empty() )
//                            .on( 'change', function () {
//                                let val = $(this).val();
//
//                                // todo search for status task
//                                column.search('', true, false ).draw();
//
//
//                            });
//
//                        $(res_task_statuses).each(function (d, j) {
//                            select_status.append( '<option value="'+j.id+'">'+j.name+'</option>');
//                        });
//                    }
                } );


            },
            "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {

                switch(aData.priority){
                    case 'Yes':
                        $(nRow).css('background', '#ffbbb2');
                        break;
                }
            }
        });


        $('#tasks-table tfoot th').each( function () {
            let column = this;

            if (column.className !== 'not' && column.className !=='click_in_task') {
                let title = $(this).text();
               // console.log(title);
                $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');
            } else { 
                    $(this).html('<span> </span>'); 
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
        $('#tasks-table tfoot th').appendTo('#tasks-table thead');

        $.fn.dataTable.ext.order['dom-select'] = function  ( settings, col )
        {
            return this.api().column( col, {order:'index'} ).nodes().map( function ( td, i ) {
                return $('select', td).val();
            } );
        }

    })*/
    });


</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('scaffold-interface.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/task/index.blade.php ENDPATH**/ ?>