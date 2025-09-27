<div class='tour-package-item ui-sortable-handle' id="agreement_from_{{$package->id}}" >
    <span>{{$tour->name}}</span>
    <span>{{ \Carbon\Carbon::parse($package->time_from)->format('H:m')}}</span>
    <span><font>{{$package->name }}</font></span>
    <span>{{ $status }}</span>
    <span>
        <button class='btn btn-xs btn-danger' type='button'>{!!trans('main.No')!!}</button>
    </span>

    <span>@foreach ($hotel_package_rooms as $pack_room)
             {{ $pack_room->room_name }} - {{ $pack_room->count_room }} <br>
          @endforeach
    </span>

    <span>{{ $package->pax }} {{$package->pax_free}}</span>

    <span> {{ !empty($package->service()->address_first) ? $package->service()->address_first : '' }}</span>

    <span>{{ !empty($package->service()->work_email) ? $package->service()->work_email : '' }}</span>

    <span>{{ !empty($package->service()->work_phone) ? $package->service()->work_phone : '' }}</span>

    <span>{!! $package->description !!} {!! $hotel_package_id !!}</span>

    <span style='text-align: center;'>
        @if($deleted)
            <button class="btn btn-xs btn-danger" onclick="deleteHotelFromPackages({{ $package->id }},{{ $hotel->id }},'{{ \Carbon\Carbon::parse($package->time_from)->format('Y-m-d') }}','{{ $hotel->name }}','{{ $tour->name }}');" >{!!trans('main.Delete')!!}</button>
        @else
       <button class="btn btn-xs btn-success add-service-button" onclick="addFromAgreement({{ $package->id }});" >{!!trans('main.Add')!!}</button>
        @endif
       <input type="hidden" id="tour_id" value="{{ $tour->id }}" >
    </span>

</div>

