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
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" id="quotation-search" class="form-control" placeholder="Search quotations..." onkeyup="filterTable('quotation_table', this.value)">
                            </div>
                            <div class="col-md-6 text-right">
                                <button class="btn btn-success btn-sm" onclick="exportTableToCSV('quotation_table', 'quotations_export.csv')">
                                    <i class="fa fa-download"></i> Export CSV
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="quotation_table" class="table table-striped table-bordered table-hover bootstrap-table" style='background:#fff;width: 100%;'>
                            <thead>
                                <tr>
                                    <th onclick="sortTable(0, 'quotation_table')">ID <i class="fa fa-sort"></i></th>
                                    <th onclick="sortTable(1, 'quotation_table')">{{trans('main.Name')}} <i class="fa fa-sort"></i></th>
                                    <th onclick="sortTable(2, 'quotation_table')">{{trans('main.Tour')}} <i class="fa fa-sort"></i></th>
                                    <th onclick="sortTable(3, 'quotation_table')">{{trans('main.Assigned')}} <i class="fa fa-sort"></i></th>
                                    <th onclick="sortTable(4, 'quotation_table')">{{trans('main.CreatedAt')}} <i class="fa fa-sort"></i></th>
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
                    </div>

                    <div class="mb-3" style="margin-top: 20px;">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" id="goahead-search" class="form-control" placeholder="Search go-ahead tours..." onkeyup="filterTable('go-ahead-table', this.value)">
                            </div>
                            <div class="col-md-6 text-right">
                                <button class="btn btn-success btn-sm" onclick="exportTableToCSV('go-ahead-table', 'goahead_tours_export.csv')">
                                    <i class="fa fa-download"></i> Export CSV
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
						       <table id="go-ahead-table" class="table table-striped table-bordered table-hover bootstrap-table" style='background:#fff;width: 100%; '>
                            <thead>
                            <tr>
                                <th onclick="sortTable(0, 'go-ahead-table')">ID <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(1, 'go-ahead-table')">{{trans('main.Name')}} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(2, 'go-ahead-table')">{{trans('main.DepDate')}} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(3, 'go-ahead-table')">{{trans('main.CountryBegin')}} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(4, 'go-ahead-table')">{{trans('main.CityBegin')}} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(5, 'go-ahead-table')">{{trans('main.Status')}} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(6, 'go-ahead-table')">{{trans('main.Externalname')}} <i class="fa fa-sort"></i></th>
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
                    </div>
                

                {{--     TAB QUOTATION    --}}

                   

                </div>

            </div>
        </div>
    </section>
@endsection


@push('scripts')
<script src="{{ asset('js/bootstrap-tables.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        initializeBootstrapTable('quotation_table');
        initializeBootstrapTable('go-ahead-table');
    });
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


