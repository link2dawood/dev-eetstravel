@extends('scaffold-interface.layouts.app')
@section('title','Index')
@section('content')
    @include('layouts.title',
    ['title' => 'Tasks', 'sub_title' => 'Tasks List',
    'breadcrumbs' => [
    ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
    ['title' => 'Tour Tasks', 'icon' => 'tasks', 'route' => null]]])
    <select hidden id="task-status-search" class="select2">
        @foreach($statuses as $status)
        <option value="{{is_object($status) ? $status->id : $status}}">{{is_object($status) ? $status->name : $status}}</option>
        @endforeach
    </select>
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div>
                    {!! \App\Helper\PermissionHelper::getCreateButton(route('task.create'), \App\Task::class) !!}
                </div>
                <span id="help" class="btn btn-box-tool pull-right"><i class="fa fa-question-circle " aria-hidden="true"></i>
                    @include('legend.task_legend')
                    </span>
                <br>
                <br>
				<div class="table-responsive">
                <table id="tasks-table" class="table table-striped table-bordered table-hover bootstrap-table" style='background:#fff;width: 100%;'>
                    <thead>
                        <tr>
                            <th onclick="sortTable(0, 'tasks-table')">ID <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(1, 'tasks-table')" style="max-width: 250px;">{!!trans('main.Content')!!} <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(2, 'tasks-table')">{!!trans('main.Deadline')!!} <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(3, 'tasks-table')">{!!trans('main.Tour')!!} <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(4, 'tasks-table')">{!!trans('main.Assignto')!!} <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(5, 'tasks-table')">{!!trans('main.TaskType')!!} <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(6, 'tasks-table')">{!!trans('main.Status')!!} <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(7, 'tasks-table')">{!!trans('main.Priority')!!} <i class="fa fa-sort"></i></th>
                            <th class="actions-button" style="width:150px; text-align: center;">{!!trans('main.Actions')!!}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tasks as $task)
                        <tr>
                            <td>{{ $task->id }}</td>
                            <td style="max-width: 250px;">{{ $task->content }}</td>
                            <td>{{ $task->deadline ? (\Carbon\Carbon::parse($task->deadline)->format('Y-m-d H:i')) : '' }}</td>
                            <td>{{ $task->tour && is_object($task->tour) ? $task->tour->name : '' }}</td>
                            <td>
                                @if($task->assignedTo && is_object($task->assignedTo))
                                    {{ $task->assignedTo->name }}
                                @endif
                            </td>
                            <td>{{ $task->task_type ? \App\Task::$taskTypes[$task->task_type] ?? $task->task_type : '' }}</td>
                            <td>
                                <span class="badge badge-primary" style="background-color: {{ $task->getStatusColor() }}">
                                    {{ $task->getStatusName() }}
                                </span>
                            </td>
                            <td>
                                @if($task->priority)
                                    <span class="badge badge-{{ $task->priority == 'high' ? 'danger' : ($task->priority == 'medium' ? 'warning' : 'info') }}">
                                        {{ ucfirst($task->priority) }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                @include('component.action_buttons', [
                                    'show_route' => route('task.show', ['task' => $task->id]),
                                    'edit_route' => route('task.edit', ['task' => $task->id]),
                                    'delete_route' => route('task.destroy', $task->id),
                                    'model' => $task
                                ])
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">No tasks found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="row">
                    <div class="col-md-12">
                        {{ $tasks->links() }}
                    </div>
                </div>
				</div>
                <span id="task_types" data-info="{{ $task_types }}"></span>
                <span id="task_statuses" data-info="{{ json_encode($statuses) }}"></span>
                <span id="status_permission" data-info="{{ \App\Helper\PermissionHelper::checkPermission('task.edit') ? 'status' : '' }}"></span>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script src="{{ asset('js/bootstrap-tables.js') }}"></script>
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
                url: "{{route('task_data')}}",
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
@endpush