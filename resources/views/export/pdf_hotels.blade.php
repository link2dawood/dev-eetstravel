<!DOCTYPE html>
<html>
<head>
	<title></title>
	<style>
	body{
		margin: 0;
		padding: 0;
		font-size: 12px;
	}
	td {
		padding: 5px;
	}
	.page-break{
        page-break-after: always;
		margin-top: 30px;
	}
	.preview-table {
		width: 700px;
	}
	.preview-table td {
		width: 150px;
	}
	.package-table {
		width: 700px;
	}
	.package-table td {
		width: 150px;
		border-width: 1px;
		border-style: solid;
	}
	table {
  		border-collapse: collapse;
	}
	</style>
</head>
<body>
<img src="{{ public_path() . '/img/eets_logo.jpg'}}" width="160px">
<p style="float: right; margin: 0; padding: 0; top: 0;">
	<b>EETS EUROPE EXPRESS & EAST EUROPE TRAVEL SERVICE INT'L CO., LTD</b><br>
	( Associates ) / Budapest operation office:<br>
	RADAY utca 15. 1/14, Budapest 1092 , Hungary <br>
	TEL: +36 1 2019416 / 2019422 , FAX: +36 1 2019418 <br>
	Office Email : eets@eets.hu wwwwwwww <br><br>
	<span>Name : {{$tour->name}}</span>	<span>External name : {{$tour->external_name}}</span>
</p>
<?php
    $index=0;
?>

@foreach($tourDays as $tourDay)
	@foreach($tourDay->packages as $package)
    {{ $package->type }}
    @if($package->type == 0)
    <?php
        $index++;
    ?>
  {{--  <p style="float: right;  text-align: right;">{{$issued}}</p> --}}
	<span style="clear: both;"></span>
	<hr>
    {{-- <p style="width: 700px; border-top-style: solid; border-top-width: 1px;"></p> --}}
	<table class="preview-table" style="margin-top: 15px;">
		<tbody>
			<tr>
				<td><b>To:</b></td>
				<td>{{$package->name}}</td>
				<td><b>Issued Date</b></td>
				<td>{{$issued}}</td>
			</tr>
			<tr>
				<td><b>Address:</b></td>
				<td>@if(isset($package->service()->address_first)){{$package->service()->address_first}}@endif</td>
				<td><b>Issued By</b></td>
				<td>{{$package->issued_by}}</td>
			</tr>

			<tr>
				<td><b>@if (!in_array($package->id, $checkedExcludeVch)) <img src="{{ public_path() . '/img/checked.png'}}" width="32px"> @endif</b></td>
				<td></td>
				<td><b>Responsible User</b></td>
				<td>{{ $tour->getResponsibleUser() ? $tour->getResponsibleUser()->name : 'Without Responsible User' }}</td>
			</tr>
		</tbody>
	</table>
	<table class="package-table" style="margin-top: 20px;">
		<tbody>
			<tr>
				<td><b>Booking:</b></td>
				<td colspan="3">{{$tour->showAllAssignedName()}}</td>
			</tr>
			<tr>
				<td><b>Tour Code:</b></td>
				<td>{{$tour->tourCode}}</td>
				<td><b>Reference</b></td>
				<td></td>
			</tr>
			<tr>
				<td><b>Service Type</b></td>
				<td>@if(isset($package->service()->service_type)){{$package->service()->service_type}}@endif</td>
				<td><b>Service Date</b></td>
				<td>From: {{$package->time_from}}<br>
					To: {{$package->time_to}}
				</td>
			</tr>
			<tr>
				<td><b>Pax:</b></td>
				<td colspan="3">{{$package->pax}}</td>
			</tr>
			<tr>
				<td><b>Rooms:</b></td>
				<td colspan="3">{{$tour->rooms}}</td>
			</tr>
			<tr>
				<td><b>Porterage:</b></td>
				<td colspan="3"></td>
			</tr>
			<tr>
				<td><b>Detail:</b></td>
				<td colspan="3">{{$package->note}}</td>
			</tr>
			<tr>
				<td><b>Payment By:</b></td>
				<td colspan="3"></td>
			</tr>
		</tbody>
	</table>
	@if($index % 2 == 0) 
        <div class="page-break"></div>
    @endif
    @endif
	@endforeach
@endforeach

</body>
</html>