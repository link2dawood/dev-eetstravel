{{-- AJAX fragment: hotel-agreement package row. Loaded into the agreement
     panel by hotel/agreement JS. Preserve the .tour-package-item /
     .ui-sortable-handle classes and #tour_id / #package_id hooks — JS
     wires them up by id and selector. --}}
<div class="tour-package-item ui-sortable-handle grid grid-cols-12 items-center gap-2 px-3 py-2 border-b border-slate-100 text-sm hover:bg-slate-50"
     id="agreement_from_{{ $package->id }}">
    <span class="col-span-2">
        <a class="text-primary-600 underline cursor-pointer hover:text-primary-700"
           onClick="window.open('/tour/{{ $tour->id }}','_blank');">{{ $tour->name }}</a>
    </span>
    <span class="col-span-1 font-mono text-slate-700">{{ \Carbon\Carbon::parse($package->time_from)->format("H:i") }}</span>
    <span class="col-span-1 text-slate-700">{{ $package->name }}</span>
    <span class="col-span-1 text-slate-700">{{ $status }}</span>
    <span class="col-span-1">
        @if($package->paid == 0)
            <button type="button" class="inline-flex items-center rounded bg-danger-100 px-2 py-0.5 text-xs font-medium text-danger-700">{!! trans('main.No') !!}</button>
        @else
            <button type="button" class="inline-flex items-center rounded bg-success-100 px-2 py-0.5 text-xs font-medium text-success-700">{!! trans('main.Yes') !!}</button>
        @endif
    </span>
    <span class="col-span-1 text-slate-700">
        @foreach($hotel_package_rooms as $pack_room)
            {{ $pack_room->name }} - {{ $pack_room->count_room }}<br>
        @endforeach
    </span>
    <span class="col-span-1 text-slate-700">{{ $package->pax }} {{ $package->pax_free }}</span>
    <span class="col-span-1 text-slate-700">{{ !empty($package->service()->address_first) ? $package->service()->address_first : '' }}</span>
    <span class="col-span-1 text-slate-700">{{ !empty($package->service()->work_email) ? $package->service()->work_email : '' }}</span>
    <span class="col-span-1 text-slate-700">{{ !empty($package->service()->work_phone) ? $package->service()->work_phone : '' }}</span>
    <span class="col-span-1 text-slate-700">{!! $package->description !!}</span>

    <span class="col-span-12 text-right">
        @if($deleted)
            <button type="button" class="delete-service-button inline-flex items-center gap-1 rounded bg-danger-600 px-2 py-0.5 text-xs text-white hover:bg-danger-700"
                    onclick="deleteHotelFromPackages({{ $package->id }},{{ $hotel->id }},'{{ \Carbon\Carbon::parse($package->time_from)->format('Y-m-d') }}','{{ $hotel->name }}','{{ $tour->name }}');">{!! trans('main.Delete') !!}</button>
        @else
            <button type="button" class="add-service-button inline-flex items-center gap-1 rounded bg-success-600 px-2 py-0.5 text-xs text-white hover:bg-success-700"
                    onclick="replaceFromPackages({{ $package->id }});">{!! trans('main.Replace') !!}</button>
        @endif
    </span>

    <input type="hidden" id="tour_id" value="{{ $tour->id }}">
    <input type="hidden" id="package_id" value="{{ $package->id }}">
</div>
