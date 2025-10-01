@extends('scaffold-interface.layouts.app')
@section('title','Index')
@section('content')
    @include('layouts.title',
   ['title' => 'Tour Quotations', 'sub_title' => 'Quotation List',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Quotation', 'icon' => 'list-ul', 'route' => null]]])

    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="box-header with-border">
                    <div>
                        {!! \App\Helper\PermissionHelper::getCreateButton(route('tour.create', ['is_quotation' => 1]), \App\Tour::class) !!}
                    </div>
					
					<div class="toggle" style = "float:right">
					<input type="checkbox" id= "check" onclick = "myfunction()" checked/>
					<label></label>
				</div>
                </div>
				

                <br>
               
                {{--     TAB QUOTATION    --}}
                <div class="tab-content">
                   
                        <table id="quotation_table" class="table table-striped table-bordered table-hover" style='background:#fff;width: 100%;'>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>{{trans('main.Name')}}</th>
                                    <th>{{trans('main.Tour')}}</th>
                                    <th>{{trans('main.Assigned')}}</th>
                                    <th>{{trans('main.CreatedAt')}}</th>
                                    <th class="actions-button">{{trans('main.Frontsheet')}}</th>
                                    <th class="actions-button" style="width: 140px!important">{{trans('main.Actions')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($quotations as $quotation)
                                <tr>
                                    <td>{{ $quotation->id }}</td>
                                    <td>{{ $quotation->name }}</td>
                                    <td>{!! $quotation->tour_link !!}</td>
                                    <td>{{ $quotation->user_name }}</td>
                                    <td>{{ $quotation->formatted_created_at }}</td>
                                    <td>{!! $quotation->comparison !!}</td>
                                    <td>{!! $quotation->action !!}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
						
						
						       <table id="go-ahead-table" class="table table-striped table-bordered table-hover" style='background:#fff;width: 100%; '>
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>{{trans('main.Name')}}</th>
                                <th>{{trans('main.DepDate')}}</th>
                                <th>{{trans('main.CountryBegin')}}</th>
                                <th>{{trans('main.CityBegin')}}</th>
                                <th>{{trans('main.Status')}}</th>
                                <th>{{trans('main.Externalname')}}</th>
                                <th class="actions-button" style="width: 140px">{{trans('main.Actions')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($goAheadTours as $tour)
                                <tr>
                                    <td>{{ $tour->id }}</td>
                                    <td>{{ $tour->name }}</td>
                                    <td>{{ $tour->departure_date }}</td>
                                    <td>{{ $tour->country_begin }}</td>
                                    <td>{{ $tour->city_begin }}</td>
                                    <td>{{ $tour->status_name }}</td>
                                    <td>{{ $tour->external_name }}</td>
                                    <td>{!! $tour->action !!}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                

                {{--     TAB QUOTATION    --}}

                   

                </div>

            </div>
        </div>
    </section>
@endsection


@push('scripts')

    <script>
		
        $(document).ready(function() {
            // Initialize simple client-side DataTable
            let table = $('#quotation_table').DataTable({
                dom: 	"<'row'<'col-sm-5'l><'col-sm-2'B><'col-sm-5'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                buttons: [
                    {
                        extend: 'csv',
                        title: 'List Quotation of The Agency',
                        exportOptions: {
                            columns: ':not(.actions-button)'
                        }
                    },
                    {
                        extend: 'excel',
                        title: 'List Quotation of The Agency',
                        exportOptions: {
                            columns: ':not(.actions-button)'
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        title: 'List Quotation of The Agency',
                        exportOptions: {
                            columns: ':not(.actions-button)'
                        }
                    }
                ],
                pageLength: 50,
                order: [[0, 'desc']], // Sort by ID descending by default
                columnDefs: [
                    {
                        targets: [5, 6], // Frontsheet and Actions columns
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        })
    </script>

<script>
        $(document).ready(function() {
            // Initialize simple client-side DataTable for go-ahead tours
            let goAheadTable = $('#go-ahead-table').DataTable({
                dom: 	"<'row'<'col-sm-5'l><'col-sm-2'B><'col-sm-5'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                buttons: [
                    {
                        extend: 'csv',
                        title: 'Go-Ahead Tours List',
                        exportOptions: {
                            columns: ':not(.actions-button)'
                        }
                    },
                    {
                        extend: 'excel',
                        title: 'Go-Ahead Tours List',
                        exportOptions: {
                            columns: ':not(.actions-button)'
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        title: 'Go-Ahead Tours List',
                        exportOptions: {
                            columns: ':not(.actions-button)'
                        }
                    }
                ],
                pageLength: 50,
                order: [[0, 'desc']], // Sort by ID descending by default
                columnDefs: [
                    {
                        targets: [7], // Actions column
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        })
    </script>



	function myfunction(){
		
		  var checkBox = document.getElementById("check");
  // Get the output text
		
  var text = document.getElementById("text");
		if (checkBox.checked == true){
   
			 $('#go-ahead-table').hide();
			 $('#go-ahead-table_wrapper').hide();
			 $('#quotation_table').show();
			 $('#quotation_table_wrapper').show();
		
  } else {
   
	  $('#go-ahead-table').show();
	  $('#go-ahead-table_wrapper').show();
	  $('#quotation_table').hide();
	   $('#quotation_table_wrapper').hide();
	  
	  	
		
	
	}
   
  }
		
	 </script>
<style>
.toggle {
  position: relative;
  height: 42px;
  display: flex;
  align-items: center;
  box-sizing:border-box;
}
.toggle input[type="checkbox"] {
  position: absolute;
  left: 0;
  top: 0;
  z-index: 10;
  width: 100%;
  height: 100%;
  cursor: pointer;
  opacity: 0;
}
.toggle label {
  position: relative;
  display: flex;
  height: 100%;
  align-items: center;
  box-sizing:border-box;
}
.toggle label:before  , .toggle label:after {
  font-size: 18px;
  font-weight: bold;
  font-family:arial;
  transition: 0.2s ease-in;
  box-sizing:border-box;
}
.toggle label:before {
  content: "Quotations";
  background: #fff;
  color: #000;
  height: 42px;
  width: 140px;
  display: inline-flex;
  align-items: center;
  padding-left: 15px;
  border-radius: 30px;
  border: 1px solid #eee;
  box-shadow: inset 140px 0px 0 0px #000;
font-size:10px
}
.toggle label:after {
  content: "GoAhead";
  position: absolute;
  left: 80px;
  line-height: 42px;
  top: 0;
  color: #FFF;
font-size:10px
}
.toggle input[type="checkbox"]:checked + label:before {
    color: #000;
    box-shadow: inset 0px 0px 0 0px #000;
}
.toggle input[type="checkbox"]:checked + label:after {
  color: #FFF;
}
@endpush


