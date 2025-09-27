@extends('scaffold-interface.layouts.app')
@section('content')
@include('layouts.title',
['title' => 'Reporting', 'sub_title' => 'Summary',
'breadcrumbs' => [
['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
['title' => 'Reporting', 'icon' => 'suitcase', 'route' => null]]])

<section class="content">
    {{--    <div class="row">
			@php
			$i = -1;
			$assets  = 0;
			$liabilities = 0;
			$equity = 0;
			@endphp
			@foreach($accounts as $account)
			@php
			$i++;
			if($account->category == 'Asset'){
				$assets  += $account->totalAmount();
			}
			if($account->category == 'Liability'){
				$liabilities  += $account->totalAmount();
			}
			if($account->category == 'Equity'){
				$equity  += $account->totalAmount();
			}
			
		
			
			
			
			
			@endphp
			<div class="col-lg-4">
                <div class="box">
                    <div class="box-body">
                        <h3 style="font-size: 16px; margin-bottom: 6px;">{{ $account->name }}</h3>
						<h3 style="font-size: 25px; margin-bottom: 6px;">{{ $account->category }}</h3>
						
                        <h3 style="font-size: 50px; font-weight: 700; color: black;" >€ {{ number_format($account->totalAmount(), 0, '.', ',')}}</h3>
					
						<input type = "hidden" value = "{{ $account->getTotalAmountForDateRange('05','2023') }}"  id = "value1{{$i}}">
						<input type = "hidden" value = "{{ $account->getTotalAmountForDateRange('06','2023') }}"  id = "value2{{$i}}">
						<input type = "hidden" value = "{{ $account->getTotalAmountForDateRange('07','2023') }}"  id = "value3{{$i}}">
						<input type = "hidden" value = "{{ $account->getTotalAmountForDateRange('08','2023') }}"  id = "value4{{$i}}">
						<input type = "hidden" value ="{{ $account->getTotalAmountForDateRange('09','2023') }}"  id = "value5{{$i}}">
                        <canvas id="chart" class = "chart" style="max-height: 120px;"></canvas>
                    </div>
                </div>
            </div>
			        @endforeach
			
			<div class="col-lg-6">
                <div class="box">
                    <div class="box-body">
                        <h3 style="font-size: 16px; margin-bottom: 6px;">Overall Assets</h3>
						
                        <h3 style="font-size: 50px; font-weight: 700; color: black;" >€ {{ number_format($assets, 0, '.', ',')}}</h3>
					
					
                    </div>
                </div>
            </div>
			
			<div class="col-lg-6">
                <div class="box">
                    <div class="box-body">
                        <h3 style="font-size: 16px; margin-bottom: 6px;">Overall Liability</h3>
						
                        <h3 style="font-size: 50px; font-weight: 700; color: black;" >€ {{ number_format($liabilities, 0, '.', ',')}}</h3>
					
					
                    </div>
                </div>
            </div>
			<div class="col-lg-6">
                <div class="box">
                    <div class="box-body">
                        <h3 style="font-size: 16px; margin-bottom: 6px;">Equity</h3>
						
                        <h3 style="font-size: 50px; font-weight: 700; color: black;" >€ {{ number_format($equity, 0, '.', ',')}}</h3>
					
					
                    </div>
                </div>
            </div>
     
			
        </div>--}}
	
	
		<button type="button" class="close" data-dismiss='modal' aria-label="Close"><span
                                aria-hidden='true'>&times;</span></button>
                    <h4 class="modal-title">{!!trans('main.Addservice')!!}</h4>
                    {{-- <div class="col-md-6"> --}}
                    <form action="{{route('supplier_show')}}">
                        <div class="form-group">
                            <select id="service-select" class="form-control">
                                <option selected>{!!trans('main.All')!!}</option>
                                @foreach($options as $option)
                                    <option>@if($option ==='Transfer') Bus Company @else {{$option}} @endif</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
		<div class="box box-body table-responsive" style="border-top: none">
                    <table id="search-table" class="table table-striped table-bordered table-hover" style="width: 100%;">
                        <thead>
                        <tr>
                            <th>{!!trans('main.Name')!!}</th>
                            <th>{!!trans('main.Address')!!}</th>
                            <th>{!!trans('main.Country')!!}</th>
                            <th>{!!trans('main.City')!!}</th>
                            <th>{!!trans('main.Phone')!!}</th>
                            <th>{!!trans('main.ContactName')!!}</th>
                            <th>{!!trans('Actions')!!}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($servicesData as $service)
                            <tr>
                                <td>{{ $service->nameService ?? $service->name }}</td>
                                <td>{{ $service->address_first ?? '' }}</td>
                                <td>{{ $service->country ?? '' }}</td>
                                <td>{{ $service->city ?? '' }}</td>
                                <td>{{ $service->work_phone ?? '' }}</td>
                                <td>{{ $service->contact_name ?? '' }}</td>
                                <td>{!! $service->action_buttons !!}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
</section>


@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
       // const ctx = document.getElementById('chart');

		var currentDate = new Date();
 var monthNames = [
            "January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
        ];
        var currentMonth = monthNames[currentDate.getMonth()];
        var previousMonths = [];
        for (let i = 4; i >= 0; i--) {

            var previousMonthIndex = currentDate.getMonth() - i;
            previousMonths.push(monthNames[previousMonthIndex < 0 ? 11 : previousMonthIndex]);


        }
		var day = currentDate.getDate();
	
		const ctx = document.querySelectorAll('.chart');
		
 for (var i = 0; i < ctx.length; i++) {
	 var value1 = document.getElementById("value1"+ i).value;
	 var value2 = document.getElementById("value2"+ i).value;
	 var value3 = document.getElementById("value3"+ i).value;
	 var value4 = document.getElementById("value4"+ i).value;
	 var value5 = document.getElementById("value5"+ i).value;

        new Chart(ctx[i], {
            type: "line",
            data: {
                labels: previousMonths,
                datasets: [
                    {
                        label: "Amount",
                        data: [value1, value2, value3, value4, value5],
                        borderWidth: 1,
                        borderColor: "#159a9c",
                        pointRadius: 0,
						backgroundColor: '#159a9c',
                    },
                ],
            },
            options: {
                plugins: {
                    legend: {
                        display: false,
                    },
                },
                scales: {
                    x: {
                        display: true
                    },
                    y: {
                        beginAtZero: true,
                        display: false
                    },
                },
            },
        });
	 
 }
		
		
 let service = "All";
 $('#service-select').on('change', function(){
   
            //$(this).attr('disabled', true);
            var service_select = $(this);
            
            var tmp = this.value;
            if(tmp === 'Bus Company') { tmp = 'Transfer';}
			service = tmp;
			rate = '';
			criterias = [];
            countryAlias = $('#country').val();
            searchName = $('#searchTextField').val();
            city_code = $('#city_code').val();
			$('#search-table').DataTable().destroy();
            generateTable(service_select);
                 
		});
        function generateTable(service_select = null){
		let table = $('#search-table').DataTable({
			dom: 	"<'row'<'col-sm-7'f><'col-sm-5 toRight'l>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            pageLength: 50,
            sort: false,
            "initComplete": function(settings, json) {
                if(service_select){
                    $(service_select).attr('disabled', false);
                }
            }
		});
    }
generateTable(null);

    </script>
@endpush