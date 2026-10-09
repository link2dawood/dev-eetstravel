@extends('scaffold-interface.layouts.tabler-app')
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
        <div class="mb-3">
            <div class="row">
                <div class="col-md-6">
                    <input type="text" id="reporting-search" class="form-control" placeholder="Search services..." onkeyup="filterTable('search-table', this.value)">
                </div>
                <div class="col-md-6 text-right">
                    <button class="btn btn-success btn-sm" onclick="exportTableToCSV('search-table', 'reporting_services_export.csv')">
                        <i class="fa fa-download"></i> Export CSV
                    </button>
                </div>
            </div>
        </div>

		<div class="box box-body table-responsive" style="border-top: none">
                    <table id="search-table" class="table table-striped table-bordered table-hover bootstrap-table" style="width: 100%;">
                        <thead>
                        <tr>
                            <th onclick="sortTable(0, 'search-table')">{!!trans('main.Name')!!} <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(1, 'search-table')">{!!trans('main.Address')!!} <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(2, 'search-table')">{!!trans('main.Country')!!} <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(3, 'search-table')">{!!trans('main.City')!!} <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(4, 'search-table')">{!!trans('main.Phone')!!} <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(5, 'search-table')">{!!trans('main.ContactName')!!} <i class="fa fa-sort"></i></th>
                            <th class="actions-button">{!!trans('Actions')!!}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($servicesData as $service)
                            <tr>
                                <td data-delete-label>{{ $service->nameService ?? $service->name }}</td>
                                <td>{{ $service->address_first ?? '' }}</td>
                                <td>{{ $service->country ?? '' }}</td>
                                <td>{{ $service->city ?? '' }}</td>
                                <td>{{ $service->work_phone ?? '' }}</td>
                                <td>{{ $service->contact_name ?? '' }}</td>
                                <td>
                                    @if(isset($service->can_show) && $service->can_show)
                                        <a class='btn btn-primary btn-sm' hidden data-id="{{ $service->id }}" data-type="{{ class_basename(get_class($service)) }}" data-service_name="{{ $service->nameService ?? $service->name }}" id='service-property' href='{{ $service->show_link ?? '#' }}' data-link="{{ $service->show_link ?? '#' }}"><i class='fa fa-info-circle'></i></a>
                                    @endif
                                    @if(isset($service->service_type))
                                        @php
                                            $routePrefix = $service->service_type;
                                            if ($routePrefix === 'transfer') $routePrefix = 'bus';
                                        @endphp
                                        <div class="btn-list flex-nowrap">
                                            @include('component.action_buttons', [
                                                'item' => $service,
                                                'routePrefix' => $routePrefix
                                            ])
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No services found</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
</section>


@endsection

@include('component.delete_modal_simple')

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('js/bootstrap-tables.js') }}"></script>

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
		
		
 // Initialize bootstrap table
 initializeBootstrapTable('search-table');

 let service = "All";
 $('#service-select').on('change', function(){
            var tmp = this.value;
            if(tmp === 'Bus Company') { tmp = 'Transfer';}
			service = tmp;

			// Filter table rows based on service type
			var table = document.getElementById('search-table');
			var rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

			for(var i = 0; i < rows.length; i++) {
				if(tmp === 'All') {
					rows[i].style.display = '';
				} else {
					// You can add service type filtering logic here if needed
					rows[i].style.display = '';
				}
			}
		});

    </script>
@endpush