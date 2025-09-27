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
            <h1 class="title">Your Tours</h1>
			  <div class="d-flex justify-content-between align-items-end"> 
				  
				  <a href="#new_audience" data-bs-toggle="modal" class="btn btn-primary m-auto"><i class="fa fa-upload"></i> Upload Tour</a>
			  <a href="{{url('TMS-Client-simpletours/create')}}" class="btn btn-primary mx-2"><i class="fa fa-plus fa-md" aria-hidden="true"></i> Simple Tour</a>
              <a href="{{url('TMS-Client-tours/create')}}" class="btn btn-primary mx-2"><i class="fa fa-plus fa-md" aria-hidden="true"></i>    Tour</a>
			  </div>
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
              
          </div>
        </div>
			<div class="modal fade" id="new_audience" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Upload Tour</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="form">
					<a href="{{url('TMS-Client/downloadSampleExcel')}}"  class="btn btn-primary"><i class="fa fa-download"></i> Download Sample</a>
					
                    <div class="input-wrapper mt-5 d-none">
                        <select class="form-control form-select put-attributes" aria-label="Default select example" required="">
                            <option selected="">Choose email column from Csv</option>
                        </select>
                    </div>
                    <p>Please upload file as given excel format and according to sample</p>
                    <div class="input-wrapper" id="drag_and_drop">
                        <label for="fileUploader" class="form-label">
                            <div class="d-flex align-items-center flex-column">
                                <div class="uploader-icon">
                                    <img src="assets/img/svg/uload-ico.svg" alt="">
                                </div>
                                Drag and Drop CSV File Here
                            </div>
                        </label>
                        <input type="file" id="fileUploader" class="form-control d-none" accept=".xlsx">
                    </div>
                    <div class="card" data-aos="zoom-in" data-aos-duration="500" id="file_wrapper" style="width: 18rem; height: 5rem;">
                        <div class="card-body">

                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <img class="file-ico" src="assets/img/svg/file-csv-ico.svg" alt="" data-aos="flip-left" data-aos-delay="300">
                                    <div>
                                        <h4 class="file-title" data-aos="fade-right" data-aos-delay="400" style="font-size: 15px;"></h4>
                                        <p class="card-text file-size" data-aos="fade-right" data-aos-delay="600"></p>
                                    </div>
                                </div>
                                <a href="" class="remove-btn" data-aos="zoom-in" data-aos-delay="800"><img src="assets/img/svg/close-btn.svg" alt=""></a>
                            </div>

                        </div>
                    </div>

                </div>
                <button type="button" class="btn btn-primary w-100" onclick="upload_audience()">Upload</button>
            </div>
        </div>
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
                url: "{{route('client_tour_data')}}",
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
	
	function upload_audience() {
        var form_data = new FormData();
        var put_attributes = $('.put-attributes').val();
        var property = document.getElementById('fileUploader').files[0];



        form_data.append("file", property);
      
        form_data.append("put_attributes", put_attributes);

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url:'uploadTourFile',
            data: form_data,
            contentType: false,
            cache: false,
            processData: false,



            success: function(result) {
                Swal.fire({
                    icon: 'success',
                    title: 'Tour Created',
                    text: 'Tour has been created successfully.',
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Redirect to a new page
                        window.location.href = "https://dev.eetstravel.com/TMS-Client/quotation_requests";
                    }
                });
            },
            error: function(result) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',

                });
            }
        });

    }
	$('#fileUploader').change(function(e) {
        if (document.getElementById("fileUploader").value.toLowerCase().lastIndexOf(".xlsx") == -1) {
            alert("Please upload a file with .xlsx extension.");
            return false;
        } else {
            var property1 = document.getElementById('fileUploader').files[0];
            var form_data_ = new FormData();
            form_data_.append("fileget", property1);
			$.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        	});
            $.ajax({
                url: 'file_viewer',
                method: 'POST',
                data: form_data_,
                contentType: false,
                cache: false,
                processData: false,
                success: function(data) {
                    var obj = eval(data);
                    console.log(obj[0]);
                    $html = "<option selected>Select Option</option>";
                    $('.put-attributes').empty();
                    for (var i = 0; i < obj[0].length; i++) {
                        $html += "<option value='" + i + "'>" + obj[0][i] + "</option>";
                    }
                    $('.put-attributes').html($html);
                }
            });
            $('#file_wrapper').show();
            $('.file-title').html(e.target.files[0].name);
           // $('.file-size').html(formatBytes(e.target.files[0].size));

        }
    });
</script>
</body>

</html>