<!DOCTYPE html>
<html>
<head>
	<title></title>
	<style type="text/css">
		body {
			font-size: 14px;
			margin: 0;
			padding: 0;

		}
		.preview-table,
		.service-table {
			table-layout: fixed;
			width: 700px;
		}
		.preview-table td {
			max-width: 150px;
			width: 150px;
			word-wrap: break-word;
		}
		.services-table .package-td {
			max-width: 150px;
			width: 150px;
			word-wrap: break-word;
			text-align: center;
		}
		.services-table .package-tr td {
			border-width: 1px;
			border-style: solid;
		}
		.services-table .day-td {
			background-color: lightgrey;
			max-width: 750px;
			width: 700px;
			border-width: 1px;
			border-style: solid;
		}
		table {
  			border-collapse: collapse;
		}
	</style>
</head>
<body>
	<img src="{{ public_path() . '/img/eets_logo.jpg'}}" width="180px">
	<h4 style="float: right; margin: 0; padding: 0; top: 0;">EETS EUROPE EXPRESS & EAST EUROPE TRAVEL SERVICE INT'L CO., LTD<br> 
		( Associates ) / Budapest operation office:<br>
        RADAY utca 15. 1/14, Budapest 1092 , Hungary <br>
        TEL: +36 1 2019416 / 2019422 , FAX: +36 1 2019418 <br>
        Office Email : eets@eets.hu yyyyyy</h4>
	<span style="clear: both;"></span>

	<p><b>Itinerary</b></p>
    <table class="services-table" style='background:#fff;width: 100%'>
        <colgroup>
            <col style="width: auto;">
            <col style="width: auto;">
            <col style="width: auto;">
            <col style="width: auto;">
            <col style="width: auto;">
            <col style="width: auto;">
            <col style="width: auto;">
        </colgroup>
        <thead>
        <tr>
            <th>Name</th>
			<th>External name</th>
            <th>Overview</th>
            <th>DepDate</th>
			<th>RetDate</th>
			<th>Pax</th>
            <th>Itinerary</th>
            <th>Status</th>
            <!--<th>Price for one</th>-->
        </tr>
        </thead>
        <tr>
            <td>{{$tour->name}}</td>
			<td>{{$tour->external_name}}</td>
            <td>{{$tour->overview}}</td>
            <td>{{$tour->departure_date}}</td>
			<td>{{$tour->retirement_date}}</td>
			<td>{{$tour->pax}} {{$tour->pax_free}}</td>
            <td>{{$tour->city_begin . " - " . $tour->city_end}}</td>
            <td>{{$tour->getStatusName()}}</td>
        </tr>
    </table>

	@if(count($tourTransfers) > 0)
	<p><b>Transfer</b></p>
		<table class="services-table" style='background:#fff;width: 100%'>
			<colgroup>
				<col style="width: auto;">
				<col style="width: auto;">
				<col style="width: auto;">
				<col style="width: auto;">
				<col style="width: auto;">
				<col style="width: auto;">
				<col style="width: auto;">
			</colgroup>
			<thead>
			<tr>
				<th>Name</th>
				<th>Status</th>
				<th>Paid</th>
				<th>Address</th>
				<th>Drivers Phones</th>
				<th>Date From</th>
				<th>Date To</th>
				<!--<th>Price for one</th>-->
			</tr>
			</thead>
			@foreach ($tourTransfers as $package)
				<tr data-package_id='{{$package->id}}' class="cancel" data-package-type="Transfer">
					<td>
						{{$package->name}}
					</td>
					<td>
						@foreach( \App\Status::where('type', 'bus')->get() as $item)
							@if($item->id == $package->status ) {{$item->name}} @endif
						@endforeach
					</td>
					<td>
						{{ ($package->paid) ? 'Yes' : 'No' }}
					</td>
					<td>@if(isset($package->service()->address_first)){{$package->service()->address_first}}@endif</td>
					<td>
						@forelse($package->getTransferDrivers() as $driver)
							<span style="display: block">{{ $driver->phone }}</span>
							@empty
						@endforelse
					</td>
					<td>
						{{ \Carbon\Carbon::parse($package->time_from)->format('Y-m-d')}}
					</td>
					<td>{{ \Carbon\Carbon::parse($package->time_to)->format('Y-m-d')}}</td>
				</tr>
			@endforeach
		</table>
	@endif

	<table class="services-table">
		<tbody>
			@foreach($tourDays as $tourDay)
				<tr>
					<td class="day-td" colspan="3">{{"Day " . $tourDay->queue . "#" . $tourDay->date}}</td>
				</tr>
				@foreach($tourDay->packages as $package)
				@if(!$package->description_package)
				<tr class="package-tr">
					<td class="package-td">{{$package->time_from}}</td>
					<td class="package-td">
						<b>@if(isset($package->service()->service_type)){{$package->service()->service_type}}@endif</b><br>
						Name: {{$package->name}}<br>
						@if( $package->getStatusName() === 'Requested') Status: {{ $package->getStatusName() }} <br>@endif
						Address:@if(isset($package->service()->address_first)){{$package->service()->address_first}}@endif<br>
						Phone: @if(isset($package->service()->work_phone)){{$package->service()->work_phone}}@endif<br>
						@if(isset($package->service()->service_type))
						@if(in_array ($package->service()->service_type, ['Hotel', 'Restaurant']) )
							@if($package->menus->count() > 0)
								<b>Menu:</b><br>
							@endif
							@foreach($package->menus as $menu)
								{{@$menu->menu->name}} : {{@$menu->count}}<br/>
							@endforeach
						@endif
						@endif
					</td>
					<td class="package-td">
						Description: {{$package->description}}<br>
					</td>
				</tr>
				@endif
				@if($package->description_package)
				<tr class="package-tr">
					<td class="package-td">{{$package->time_from}}</td>
					<td class="package-td" colspan="2">{{$package->description}}</td>
				</tr>
				@endif
				@endforeach
			@endforeach
		</tbody>
	</table>
</body>
</html>