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
        <tr>
            <td><b><i>{!!trans('main.Overview')!!} : </i></b></td>
            <td>{{$tour->overview}}</td>
        </tr>
        <tr>
            <td><b><i>{!!trans('main.Remark')!!} : </i></b></td>
            <td>{{$tour->remark}}</td>
        </tr>
        <tr>
            <td><b><i>Pax : </i></b></td>
            <td>{{$tour->pax}}</td>
        </tr>
        <tr>
            <td><b><i>{!!trans('main.PaxFree')!!}: </i></b></td>
            <td>{{$tour->pax_free}}</td>
        </tr>
        <tr>
            <td><b><i>{!!trans('main.CountryBegin')!!}: </i></b></td>
            <td>{{$tour->country_begin}}</td>
        </tr>
        <tr>
            <td><b><i>{!!trans('main.CityBegin')!!}: </i></b></td>
            <td>{{$tour->city_begin}}</td>
        </tr>
        <tr>
            <td><b><i>{!!trans('main.CountryEnd')!!}: </i></b></td>
            <td>{{$tour->country_end}}</td>
        </tr>
        <tr>
            <td><b><i>{!!trans('main.CityEnd')!!}: </i></b></td>
            <td>{{$tour->city_end}}</td>
        </tr>
        <tr>
            <td><b><i>{!!trans('main.DepDate')!!} : </i></b></td>
            <td>{{$tour->departure_date}}</td>
        </tr>
        <tr>
            <td><b><i>{!!trans('main.RetDate')!!} : </i></b></td>
            <td>{{$tour->retirement_date}}</td>
        </tr>
        <tr>
            <td><b><i>{!!trans('main.Invoice')!!} : </i></b></td>
            <td>{{$tour->invoice}}</td>
        </tr>
        <tr>
            <td><b><i>G\A : </i></b></td>
            <td>{{$tour->ga}}</td>
        </tr>
        <tr>
            <td><b><i>{!!trans('main.Status')!!} : </i></b></td>
            <td>{{$tour->getStatusName()}}</td>
        </tr>
        <tr>
            <td><b><i>{!!trans('main.Totalamount')!!} : </i></b></td>
            <td>{{$tour->total_amount}}</td>
        </tr>
        <tr>
            <td><b><i>{!!trans('main.PriceperPerson')!!} : </i></b></td>
            <td>{{$tour->price_for_one}}</td>
        </tr>
        <tr>
            <td><b><i>{!!trans('main.RoomsHotel')!!} : </i></b></td>
            <td>
                @foreach( \App\TourRoomTypeHotel::where('tour_id', $tour->id )->get() as $item)
                    {{ $item->room_types->code }} - {{ $item->count }} &nbsp;&nbsp;&nbsp;
                @endforeach
            </td>
        </tr>
        <tr>
            <td><b><i>{!!trans('main.Assigneduser')!!} : </i></b></td>
            <td>
                @foreach($tour->users as $user)
                    {{$user->name}}
                @endforeach
            </td>
        </tr>
        <tr>
            <td><b><i>{!!trans('main.ResponsibleUser')!!}: </i></b></td>
            <td>{{ $tour->getResponsibleUser() ? $tour->getResponsibleUser()->name : 'Without Responsible User' }}</td>
        </tr>
        {{--
        <tr>
            <td><b><i>{!!trans('main.Rooms')!!} : </i></b></td>
            <td>{{$tour->rooms}}</td>
        </tr>--}}
    </tbody>
</table>
