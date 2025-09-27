<!DOCTYPE html>
<html>
<head>
	<title></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<style type="text/css">
		thead {
			background-color: lightgrey;
		}
		.page-break {
    		page-break-after: always;
		}
	</style>
</head>
<body>
@foreach ($data as $d)
<h4>Service table for {{$d['name']}}</h4>
<table style="width: 100%">
	<thead>
		<tr>
			<th style="width: 30%">{!!trans('main.Key')!!}</th>			
			<th style="width: 70%">{!!trans('main.Value')!!}</th>
		</tr>
	</thead>
	<tbody>
		@foreach($d as $key => $value)
		<tr>
			<td>{{$key}}</td>
			<td>{{$value}}</td>
		</tr>
		@endforeach
	</tbody>
</table>
<div class="page-break"></div>
@endforeach	
</body>
</html>