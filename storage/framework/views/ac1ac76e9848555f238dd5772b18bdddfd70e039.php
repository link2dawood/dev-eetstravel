<div class="panel panel-info">
    <div class="panel-heading"><?php echo trans('main.Tasks'); ?></div>
    <a data-info="<?php echo e($listIdTasks); ?>" id="getListIdTasks"></a>
    <a data-info="<?php echo e($tour->name); ?>" id="getNameTour"></a>
    <div class="panel-body">
        <div class="row">
            <?php if(Auth::user()->can('create.task')): ?>
            <div class="col-md-12">
                <a href="<?php echo url("task"); ?>/create?tour=<?php echo $tour->id; ?>" class='btn btn-success'><?php echo trans('main.AddTask'); ?></a>
            </div>
            <?php endif; ?>
        </div>
        <br>
        <table id="tasks-table" class="table table-striped table-bordered table-hover" style='background:#fff;width: 100%;'>
            <thead>
            <th>ID</th>
            <th><?php echo trans('main.Content'); ?></th>
            <th><?php echo trans('main.Deadline'); ?></th>
            <th><?php echo trans('main.Assignto'); ?></th>
            <th><?php echo trans('main.Status'); ?></th>
            <th><?php echo trans('main.Priority'); ?></th>
            <th class="actions-button" style="width: 140px!important"><?php echo trans('main.Actions'); ?></th>
            </thead>
            <tfoot>
            <tr>
                <th class="not"></th>
                <th><?php echo trans('main.Content'); ?></th>
                <th><?php echo trans('main.Deadline'); ?></th>
                <th><?php echo trans('main.Assignto'); ?></th>
                <th class="select"><?php echo trans('main.Status'); ?></th>
                <th class="not"></th>
                <th class="not"></th>
            </tr>
            </tfoot>
        </table>
    </div>
</div>


<?php $__env->startPush('scripts'); ?>

<script>
    $(document).ready(function() {
        var idTasks = $('#getListIdTasks').attr('data-info');
        var tour = $('#getNameTour').attr('data-info');

        let table = $('#tasks-table').DataTable({
            dom: 	"<'row'<'col-sm-5'l><'col-sm-2'B><'col-sm-5'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
//            sDom: 'fi<"clear">tp',
            buttons: [
                {
                    extend: 'csv',
                    title: 'Tasks List For Tour ' + tour,
                    exportOptions: {
                        columns: ':not(.actions-button)',
                        format: {
                            body:  function(data, row, col,node)  {
                                var elementType = node.firstChild;
                                if (elementType != null) {
                                    if (elementType.nodeName === "SELECT") {
                                        data = $(elementType).find(':selected').text();
                                    }

                                }

                                return data;
                            }
                        }
                    }
                },
                {
                    extend: 'excel',
                    title: 'Tasks List For Tour ' + tour,
                    exportOptions: {
                        columns: ':not(.actions-button)',
                        format: {
                            body:  function(data, row, col,node)  {
                                var elementType = node.firstChild;
                                if (elementType != null) {
                                    if (elementType.nodeName === "SELECT") {
                                        data = $(elementType).find(':selected').text();
                                    }

                                }

                                return data;
                            }
                        }
                    }
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Tasks List For Tour ' + tour,
                    exportOptions: {
                        columns: ':not(.actions-button)',
                        format: {
                            body:  function(data, row, col,node)  {
                                var elementType = node.firstChild;
                                if (elementType != null) {
                                    if (elementType.nodeName === "SELECT") {
                                        data = $(elementType).find(':selected').text();
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
            serverSide: true,
            pageLength: 50,
            order : [ [5, 'desc'] ],
            ajax: {
                url: "<?php echo e(route('tasks_data_tour')); ?>",
                data: {
                    'idTasks': idTasks
                }
            },
            columns: [
                {data: 'id', name: 'id'},
                {data: 'content', name: 'content'},
                {data: 'dead_line', name: 'dead_line'},
                {data: 'assign', name: 'assign'},
                {data: 'status_name', name: 'status_name', className: 'status'},
                {data: 'priority', name: 'priority', className: 'priority', visible: false},
                {data: 'action', name: 'action', searchable: false, sorting: false, orderable: false}
            ],
            initComplete: function () {
                this.api().columns().every( function () {
                    var column = this;
                    if(column.footer().className == 'select_search'){
                        var select = $('<select class="form-control"><option value=""></option></select>')
                            .appendTo( $(column.footer()).empty() )
                            .on( 'change', function () {
                                var val = $.fn.dataTable.util.escapeRegex(
                                    $(this).val()
                                );

                                column
                                    .search( val ? '^'+val+'$' : '', true, false )
                                    .draw();
                            } );

                        column.data().unique().sort().each( function ( d, j ) {
                            select.append( '<option value="'+d+'">'+d+'</option>' )
                        } );
                    }
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
        $('#tasks-table tfoot th').appendTo('#tasks-table thead');
    })
</script>
<?php $__env->stopPush(); ?><?php /**PATH /var/www/html/resources/views/component/list_tasks_for_tour.blade.php ENDPATH**/ ?>