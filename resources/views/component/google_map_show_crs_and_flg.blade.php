<div class="panel panel-default">
    <div class="panel-heading">
        {!!trans('main.GooglePlace')!!}
    </div>
    <div class="panel-body">
        <span id="page" data-page="show"></span>
        <span id="place" data-info="{{ $place }}"></span>
        <div class="block_map" style="height: 500px">
            <div id="map"></div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('js/google_map_flights_and_cruise.js') }}"></script>
@endpush