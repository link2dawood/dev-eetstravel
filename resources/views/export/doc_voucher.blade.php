<!DOCTYPE html>
<html>
<head>
    <title></title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-size: 11px;
        }

        td {
            padding: 5px;
        }

        .page-break {
            page-break-after: always;
            margin-top: 30px;
        }

        .preview-table {
            width: 700px;
			border: 1px solid black;
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
		.page-break{
            page-break-after: always;
            margin-top: 30px;
        }
    </style>
</head>
<body>
{{--
<img src="{{ public_path() . '/img/eets_logo_small.jpg'}}">
<p style="float: right; margin: 0; padding: 0; top: 0;">
	<b>EETS EUROPE EXPRESS & EAST EUROPE TRAVEL SERVICE INT'L CO., LTD</b><br>
	( Associates ) / Budapest operation office:<br>
	RADAY utca 15. 1/14, Budapest 1092 , Hungary <br>
	TEL: +36 1 2019416 / 2019422 , FAX: +36 1 2019418 <br>
	Office Email : eets@eets.hu<br><br>
	<span>Name : {{$tour->name}}</span>	<span>External name : {{$tour->external_name}}</span>
</p>
--}}
<?php
$index = 0;
$hotels = [];
?>
@foreach($tourDays as $tourDay)
    @foreach($tourDay->packages as $package)
        @if ( $package->description_package )
            @continue;
        @endif
        @if( $package->reference && $package->type == 0)
            @if ( array_search($package->reference, $hotels) >-1 )
                @continue;
            @endif
            <?php
            array_push($hotels, $package->reference);
            ?>
        @endif
       <img src="{{ public_path() . '/img/eets_small.jpg' }}" height="2%" width="2%" style="height:20%;width:20%;float: left; "><p style=" padding: 0; top: 0;">
          <b>  {{ $office->office_name }}</b><br>
            ( Associates ) / Budapest operation office:<br>
            {{ $office->office_address }} <br>
            TEL: +{{ $office->tel }} , FAX: +{{ $office->fax }} <br>
            Office Email : eets@eets.hu<br><br>			    Name : {{ $tour->name }}>External name : {{ $tour->external_name }}
        </p>
        <?php
        $index++;
        ?>
       
        <hr>
        {{-- <p style="width: 700px; border-top-style: solid; border-top-width: 1px;"></p> --}}
        <table class="preview-table" style="margin-top: 5px;"  >
            <tbody>
            <tr>
                <td><b>{!!trans('main.To')!!}:</b></td>
                <td>{{str_replace("&", "and", $package->name)}}</td>
                <td  style="margin-top: 0px;"><b>{!!trans('main.IssuedDate')!!}</b></td>
                <td>{{$issued}}</td>
            </tr>
            <tr>
                <td><b>{!!trans('main.Address')!!}:</b></td>
                <td>@if(isset($package->service()->address_first)){{$package->service()->address_first}}@endif</td>
                <td><b>{!!trans('main.IssuedBy')!!}</b></td>
                <td>{{$package->issued_by}}</td>
            </tr>

            <tr>
                <td>
                    <b>@if (!in_array($package->id, $checkedExcludeVch)) {{--<img src="{{ public_path() . '/img/checked.png'}}" width="32px">--}} @endif</b>
                </td>
                <td></td>
                <td><b>{!!trans('main.ResponsibleUser')!!}</b></td>
                <td>{{ $tour->getResponsibleUser() ? $tour->getResponsibleUser()->name : trans('main.WithoutResponsibleUser') }}</td>
            </tr>
            </tbody>
        </table>
        <table class="package-table" style="margin-top: 10px;"  border="1">
            <tbody>
            <tr>
                <td><b>{!!trans('main.Booking')!!}:</b></td>
                <td colspan="3">{{$tour->showAllAssignedName()}}</td>
            </tr>
            <tr>
                <td><b>{!!trans('main.TourCode')!!}:</b></td>
                <td>{{$tour->tourCode}}</td>
                <td><b>{!!trans('main.Reference')!!}</b></td>
                <td></td>
            </tr>
            <tr>
                <td><b>{!!trans('main.ServiceType')!!}</b></td>
                <td>@if(isset($package->service()->service_type)){{$package->service()->service_type}}@endif</td>
                <td><b>{!!trans('main.ServiceDate')!!}</b></td>
                <td>{!!trans('main.From')!!}: {{$package->time_from}}<br>
                    {!!trans('main.To')!!}: {{$package->time_to}}
                </td>
            </tr>
             <tr>
                <td><b>Pax:</b></td>
               
				
				@if(@$package->service()->service_type == 'Transfer' || @$package->service()->service_type == 'Guide')
				 <td >{{$package->pax}}</td>
					<td ><b>{!!trans('Pickup')!!}</b></td>
										<td style="font-size:9px">{{ $tourDay->date. " " .$package->time_from }}/{{ $package->pickup_des }} at {{ $package->time_to }} </td>
				@else
				<td colspan="3">{{$package->pax}}</td>
				@endif
            </tr>
				
			 <tr>
                <td><b>{!!trans('main.Porterage')!!}:</b></td>
                
				@if(@$package->service()->service_type == 'Transfer' || @$package->service()->service_type == 'Guide')
				 <td ></td>
					<td ><b>{!!trans('Dropoff')!!}</b></td>
										<td style="font-size:9px">  {{ $tourDay->date. " " .$package->time_to }}/{{ $package->drop_des }} at {{ $package->time_to }} </td>
				@else
				<td colspan="3"></td>
				@endif
            </tr>

            <tr>
                <td><b>{!!trans('main.Rooms')!!}:</b></td>
                <td colspan="3">
                    @if(count($package->room_types_hotel) > 0)
                        @foreach($package->room_types_hotel as $item)
                            <span>
                                {{$item->room_types->code}}
                                {{$item->count}}
                            </span>
                        @endforeach
                    @endif
                </td>
            </tr>
            <tr>
                <td><b>{!!trans('main.Detail')!!}:</b></td>
                <td colspan="3">{{$package->note}}</td>
            </tr>
            <tr>
                <td><b>{!!trans('main.PaymentBy')!!}:</b></td>
                <td colspan="3"></td>
            </tr>
            @if ( count($package->menus) > 0 )
                <tr>
                    <td><b>{!!trans('main.Meals')!!}:</b></td>
                    <td colspan="3">
                        @foreach($package->menus as $menu)
                            <span>
                                {{$menu->count}}
                                {{$menu->menu->name??""}}
                            </span>
                        @endforeach
                    </td>
                </tr>
            @endif
            </tbody>
        </table>
	<br>
	<br>
	<br>
	<br>
	 @if($index > 7)
	@else
	<br>
	@endif
         <div class="page-break"></div>
                @if($index % 2 == 0)
                    <div class="page-break"></div>
                @endif

    @endforeach
@endforeach

</body>
</html>