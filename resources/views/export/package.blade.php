@if($tour)
<table>
    <thead>
    <tr>
        <th>{!!trans('main.Key')!!}</th>
        <th>{!!trans('main.Value')!!}</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td><b><i>{!!trans('main.Name')!!} : </i></b></td>
        <td>{{$tour->name}}</td>
    </tr>
    <tr>
        <td><b><i>{!!trans('main.Externalname')!!} : </i></b></td>
        <td>{{$tour->external_name}}</td>
    </tr>
    </tbody>
</table>
@endif
@if($tour->transfer)
    <table>
    <thead>
    <tr>
        <th>{!!trans('main.Name')!!}</th>
        <th>{!!trans('main.Status')!!}</th>
        <th>{!!trans('main.Paid')!!}</th>
        <th>Pax</th>
        <th>{!!trans('main.Address')!!}</th>
        <th>{!!trans('main.Email')!!}</th>
        <th>{!!trans('main.Phone')!!}</th>
        <th>{!!trans('main.Description')!!}</th>
        <!--<th>Price for one</th>-->
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>
            {{$tour->transfer->name}}
        </td>
        <td>
            {{$tour->getTransferStatus()}}
            {{--@if(count($statusPackage) > 0)
                @foreach($statusPackage as $item)
                    @if($item->id == $tour->transfer->status)
                        {{$item->name}}
                    @endif
                @endforeach
            @else
                {{$tour->getTransferStatus()}}
            @endif --}}
        </td>
        <td>
            {{ ($tour->transfer->paid) ? trans('main.Yes') : trans('main.No') }}
        </td>
        <td>{{ $tour->transfer->pax }} {{$tour->transfer->pax_free}}</td>
        <td>{{  $tour->transfer->service()->address_first}}</td>
        <td>{!! $tour->transfer->service()->work_email!!}</td>
        <td>{!! $tour->transfer->service()->work_phone!!}</td>
        <td class="service-description">{!! $tour->transfer->description !!}</td>
        {{--<td>{!! $tour->transfer->total_amount !!}</td> --}}
    </tr>
    </tbody>
</table>
@endif

@foreach ($tourDates as $tourDate)
<table>
    <thead>
        <tr>
            <th>{!!trans('main.Name')!!}</th>
            <th>{!!trans('main.Status')!!}</th>
            <th>{!!trans('main.Paid')!!}</th>
            <th>{!!trans('main.FromTime')!!}</th>
            <th>{!!trans('main.ToTime')!!}</th>
            <th>Pax</th>
            <th>{!!trans('main.TotalAmount')!!}</th>
            <th>{!!trans('main.Address')!!}</th>
            <th>{!!trans('main.Email')!!}</th>
            <th>{!!trans('main.Phone')!!}</th>
            <th>{!!trans('main.ResponsibleUser')!!}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($tourDate->packages as $package)
            @if(!$package->description_package)
            <tr>
                <td>{{$package->name}}</td>
                <td>{{$package->getStatusName()}}</td>
                <td>
                    <button class="btn btn-xs {{$package->paid ? 'btn-success' : 'btn-danger'}}"
                        type="button">{{$package->paid ? trans('main.Yes') : trans('main.No')}}</button>
                </td>
                <td>{!! $package->time_from??"" !!}</td>
                <td>{!! $package->time_to??"" !!}</td>
                <td>{!! $package->pax??"" !!}</td>
                <td>{!! round($package->total_amount, 2) !!}</td>
                <td>{!! $package->service()['address_first']??"" !!}</td>
                <td>{!! $package->service()['work_email']??"" !!}</td>
                <td>{!! $package->service()['work_phone']??"" !!}</td>
                <td>{{ $tour->getResponsibleUser() ? $tour->getResponsibleUser()->name : trans('main.WithoutResponsibleUser') }}</td>
            </tr>
            @endif
        @endforeach
    </tbody>
</table>
@endforeach