<!DOCTYPE html>
<html lang="en">
 @include('TMSClient.layout.head')
<body>
  <div class="main">
    @include('TMSClient.layout.nav')
    <div class="main-content" style="margin-top: 64px;">
      <section class="tours-archive">
        <div class="container">
          <div class="d-flex justify-content-between align-items-start">
            <h1 class="title">Your Requests</h1>
            <a href="{{url('TMS-Client-tours/create')}}" class="btn btn-primary">Add New</a>
          </div>
          <div class="card">
	      	<table id="tour-table" class="table table-responsive">
              <thead>
                <tr>
                  <th>Id</th>
                  <th>{!!trans('main.Name')!!}</th>
                  <th>{!!trans('main.Depdate')!!}</th>
                  <th>{!!trans('main.Status')!!}</th>
				  <th>{!!trans('main.ExternalName')!!}</th>
                 <th class="actions-button" style="width:140px; text-align: center;">{!!trans('main.Actions')!!}</th>
                </tr>
              </thead>
			<tfoot>
                    <tr>
                        <th class="not"></th>
                        <th>{!!trans('main.Name')!!}</th>
                        <th>{!!trans('main.Depdate')!!}</th>
                   
                        <th class="select_search">{!!trans('main.Status')!!}</th>
                        <th>{!!trans('main.ExternalName')!!}</th>
                        <th class="not"></th>
                    </tr>
                </tfoot>
              <tbody>
                <tr>
                  <td>
                    1.
                  </td>
                  <td>
                    <h6 class="fw-bold mb-0">Tour Name</h6>
                    <p class="text mb-0">30 Oct 2023 - 05 Nov 2023</p>
                  </td>
                  <td>24</td>
                  <td>
                    <div class="status active">
                      <!-- Note: These are some of the classes to change the color of status dot "active", "pending", "rejected". -->
                      Active
                    </div>
                  </td>
                  <td>
                    <div class="d-flex align-items-center gap-2">
                      <a href="" class="action-link btn-primary">
                        <i class="fas fa-eye"></i>
                      </a>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td>
                    1.
                  </td>
                  <td>
                    <h6 class="fw-bold mb-0">Tour Name</h6>
                    <p class="text mb-0">30 Oct 2023 - 05 Nov 2023</p>
                  </td>
                  <td>24</td>
                  <td>
                    <div class="status pending">
                      <!-- Note: These are some of the classes to change the color of status dot "active", "pending", "rejected". -->
                      Pending
                    </div>
                  </td>
                  <td>
                    <div class="d-flex align-items-center gap-2">
                      <a href="" class="action-link btn-primary">
                        <i class="fas fa-eye"></i>
                      </a>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td>
                    1.
                  </td>
                  <td>
                    <h6 class="fw-bold mb-0">Tour Name</h6>
                    <p class="text mb-0">30 Oct 2023 - 05 Nov 2023</p>
                  </td>
                  <td>24</td>
                  <td>
                    <div class="status rejected">
                      <!-- Note: These are some of the classes to change the color of status dot "active", "pending", "rejected". -->
                      Rejected
                    </div>
                  </td>
                  <td>
                    <div class="d-flex align-items-center gap-2">
                      <a href="" class="action-link btn-primary">
                        <i class="fas fa-eye"></i>
                      </a>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>
    </div>
  </div>



  <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
 @include('TMSClient.layout.footer')
	
<script>
    $(document).ready(function() {
        let permission = $('#permission').attr('data-permission');
        let classNameStatus = permission ? 'touredit-status' : '';
        let table = $('#tour-table').DataTable({
            dom: "<'row'<'col-sm-5'l><'col-sm-2'B><'col-sm-5'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            buttons: [{
                    extend: 'csv',
                    title: 'Tours List',
                    exportOptions: {
                        columns: ':not(.actions-button)'
                    }
                },
                {
                    extend: 'excel',
                    title: 'Tours List',
                    exportOptions: {
                        columns: ':not(.actions-button)'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Tours List',
                    exportOptions: {
                        columns: ':not(.actions-button)',
                    },
                    // customize: function (doc) {
                    //     doc.content[1].table.widths = 
                    //     Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                    // },
                },
            ],
            language: {
                search: "Global Search :"
            },
            processing: true,
            serverSide: true,
            pageLength: 50,
            ajax: {
                url: "{{route('quotation_requests_data')}}",
            },
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'name',
                    name: 'name',
                    className: 'touredit-name'
                },
                {
                    data: 'departure_date',
                    name: 'departure_date',
                    className: 'touredit-departure_date'
                },
                //        {data: 'retirement_date', name: 'retirement_date', className: 'touredit-retirement_date'},
               
                {
                    data: 'status_name',
                    className: classNameStatus 
                },
                {
                    data: 'external_name',
                    name: 'external_name',
                    className: 'touredit-external_name'
                },
                {
                    data: 'action',
                    name: 'action',
                    searchable: false,
                    sorting: false,
                    orderable: false
                }
            ],
            'columnDefs': [{
                'targets': 5,
                'createdCell': function(td, cellData, rowData, row, col) {
                    var url = "{{ route('tour.update', ['tour' => '__ID__']) }}".replace('__ID__', rowData.id);
                    $(td).attr('data-status-link', url);
                }
            }],
			'columnDefs': [ {
			  'targets': 3,
			  'createdCell': function (td, cellData, rowData, row, col) {
			
				if ( cellData == 'Requested' ) {
				  $(td).addClass('status pending');
				}
				 else if ( cellData == 'Cancelled' ) {
				  $(td).addClass('status rejected');
				}
				
			  }
			} ],
            initComplete: function() {
                this.api().columns().every(function() {
                    var column = this;
                    if (column.footer().className == 'select_search') {
                        var select = $('<select class="form-control"><option value=""></option></select>')
                            .appendTo($(column.footer()).empty())
                            .on('change', function() {
                                var val = $.fn.dataTable.util.escapeRegex($(this).val());
                                column.search(val ? '^' + val + '$' : '', true, false).draw();
                            });

                        column.data().unique().sort().each(function(d, j) {
                            select.append('<option value="' + d + '">' + d + '</option>')
                        });
                    }
                });
            }
        });
        $('#tour-table tfoot th').each(function() {
            let column = this;
            if (column.className !== 'not') {
                let title = $(this).text();
                $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');
            } else {
                $(this).html('<span> </span>');
            }
        });
        table.columns().every(function() {
            let that = this;

            $('input', this.footer()).on('keyup change', function() {
                if (that.search() !== this.value) {
                    that.search(this.value).draw();
                }
            });
        });
        $('#tour-table tfoot th').appendTo('#tour-table thead');

    })
</script>
</body>

</html>