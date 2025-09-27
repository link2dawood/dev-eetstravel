<div class='tour-package-item ui-sortable-handle' id="agreement_from_{{$package->id}}"    >
    <span><url style='text-decoration: underline;cursor: pointer;color: blue;' onClick="window.open('/tour/{{$tour->id}}','_blank');">{{$tour->name}}</url></span>
    <span>{{ \Carbon\Carbon::parse($package->time_from)->format("H:i")}}</span>
    <span><font>{{$package->name }}</font></span>
    <span>{{ $status }} </span>
    <span>
         @if($package->paid == 0)
        <button class='btn btn-xs btn-danger' type='button'>{!!trans('main.No')!!}</button>
             @else
            <button class='btn btn-xs btn-success' type='button'>{!!trans('main.Yes')!!}</button>
        @endif
    </span>

    <span>@foreach ($hotel_package_rooms as $pack_room)
             {{ $pack_room->name }} - {{ $pack_room->count_room }} <br>
          @endforeach
    </span>

    <span>{{ $package->pax }} {{$package->pax_free}}</span>

    <span> {{ !empty($package->service()->address_first) ? $package->service()->address_first : '' }}</span>

    <span>{{ !empty($package->service()->work_email) ? $package->service()->work_email : '' }}</span>

    <span>{{ !empty($package->service()->work_phone) ? $package->service()->work_phone : '' }}</span>

    <span>{!! $package->description !!}</span>

    <span style='text-align: center;'>
        @if($deleted)
            <button class="btn btn-xs btn-danger delete-service-button" onclick="deleteHotelFromPackages({{ $package->id }},{{ $hotel->id }},'{{ \Carbon\Carbon::parse($package->time_from)->format('Y-m-d') }}','{{ $hotel->name }}','{{ $tour->name }}');" >{!!trans('main.Delete')!!}</button>
        @else
       <button class="btn btn-xs btn-success add-service-button" onclick="replaceFromPackages({{ $package->id }});" >{!!trans('main.Replace')!!}</button>
        @endif
    </span>

    <input type="hidden" id="tour_id" value="{{ $tour->id }}" >
    <input type="hidden" id="package_id" value="{{$package->id}}" >

</div>

