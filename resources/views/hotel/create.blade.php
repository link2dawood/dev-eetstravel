@extends('scaffold-interface.layouts.app')
@section('title','Create')
@section('content')
    @include('layouts.title',
   ['title' => 'Hotel', 'sub_title' => 'Hotel Create',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Hotels', 'icon' => 'hotel', 'route' => route('hotel.index')],
   ['title' => 'Create', 'route' => null]]])
    <section class="content">
        <div class="box box-primary">
            <div class="box box-body border_top_none">
                <form method='POST' action='{{route('hotel.store')}}' enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="margin_button">
                                <a href="javascript:history.back()">
                                    <button type="button" class='btn btn-primary back_btn'>{!!trans('main.Back')!!}</button>
                                </a>
                                <button class='btn btn-success' type='submit'>{!!trans('main.Save')!!}</button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">

                                {{csrf_field()}}

                                <div class="form-group {{$errors->has('name') ? 'has-error' : ''}}">
                                    <label for="name">Name</label>
                                    <input id="name" name="name" type="text" class="form-control">
                                    @if($errors->has('name'))
                                        <strong>{{$errors->first('name')}}</strong>
                                        @endif
                                </div>
                                <div class="form-group">
                                    <label for="address_first">{!!trans('main.AddressFirst')!!}</label>
                                    <input id="address_first" name="address_first" type="text" class="form-control" value="{{old('address_first')}}">
                                </div>
                                <div class="form-group">
                                    <label for="address_second">{!!trans('main.AddressSecond')!!}</label>
                                    <input id="address_second" name="address_second" type="text" class="form-control" value="{{old('address_second')}}">
                                </div>
                                @component('component.city_form', ['country_label' => 'country', 'country_translation' => 'main.Country', 'country_default' =>0,
                                        'city_label' => 'city','city_translation' =>'main.City', 'city_default' => 0])
                                @endcomponent
                                <div class="form-group">
                                    <label for="code">{!!trans('main.Code')!!}</label>
                                    <input id="code" name="code" type="text" class="form-control" value="{{old('code')}}">
                                </div>
                                <div class="form-group">
                                    <label for="work_phone">{!!trans('main.WorkPhone')!!}</label>
                                    <input id="work_phone" name="work_phone" type="text" class="form-control" value="{{old('work_phone')}}">
                                </div>
                                <div class="form-group">
                                    <label for="work_fax">{!!trans('main.WorkFax')!!}</label>
                                    <input id="work_fax" name="work_fax" type="text" class="form-control" value="{{old('work_fax')}}">
                                </div>
                                <div class="form-group">
                                    <label for="work_email">{!!trans('main.WorkEmail')!!}</label>
                                    <input id="work_email" name="work_email" type="text" class="form-control" value="{{old('work_email')}}">
                                </div>
                                <div class="form-group">
                                    <label for="contact_name">{!!trans('main.ContactName')!!}</label>
                                    <input id="contact_name" name="contact_name" type="text" class="form-control" value="{{old('contact_name')}}">
                                </div>
                                <div class="form-group">
                                    <label for="contact_phone">{!!trans('main.ContactPhone')!!}</label>
                                    <input id="contact_phone" name="contact_phone" type="text" class="form-control" value="{{old('contact_phone')}}">
                                </div>
                                <div class="form-group">
                                    <label for="contact_email">{!!trans('main.ContactEmail')!!}</label>
                                    <input id="contact_email" name="contact_email" type="text" class="form-control" value="{{old('contact_email')}}">
                                </div>
                                <div class="form-group">
                                    <label for="comments">{!!trans('main.Comments')!!}</label>
                                    <input id="comments" name="comments" type="text" class="form-control" value="{{old('comments')}}">
                                </div>
                                <div class="form-group">
                                    <label for="note">{!!trans('main.Note')!!}</label>
                                    <input id="note" name="note" type="text" class="form-control" value="{{old('note')}}">
                                </div>
                                <div class="form-group">
                                    <label for="int_comments">{!!trans('main.IntComments')!!}</label>
                                    <input id="int_comments" name="int_comments" type="text" class="form-control" value="{{old('int_comments')}}">
                                </div>
                                <div class="form-group">
                                    <label for="website">{!!trans('main.Website')!!}</label>
                                    <input type="text" name="website" id="website" class="form-control" value="{{old('website')}}">
                                </div>
                                <div class="form-group">
                                    <label for="city_tax">{!!trans('main.CityTax')!!}</label>
                                    <input type="text" name="city_tax" id="city_tax" class="form-control" value="{{old('city_tax')}}">
                                </div>


                                <!--
                                <div class="form-group">
                                    <label>Prices Room Type</label>
                                    <div class="block-list-prices-room-type">
                                        @foreach($room_types as $room_type)
                                            <div class="item-price-room-type">
                                                <span>{{ $room_type->name }}</span>
                                                <input type="number" min="0" name="prices_room_type[{{$room_type->id}}]" value="0" class="form-control price_room_type_in_hotel">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                -->


                                <div class="form-group">
                                    <label for="criteria">{!!trans('main.Criteria')!!}</label>
                                </div>
                                @foreach($criterias as $criteria)
                                    <div class="form-group criteria_block">
                                        <input type="checkbox" value="{{ $criteria->id }}" name="criterias">
                                        <label for="">{{ $criteria->name }}</label>
                                    </div>
                                @endforeach

                                <div class="form-group">
                                    <label for="rate">{!!trans('main.Rate')!!}</label>
                                    <select name="rate" id="rate" class="form-control">
                                        @foreach($rates as $rate)
                                            <option {{ old('rate') == $rate->id ? 'selected' : '' }} value="{{ $rate->id }}">{{ $rate->name }}</option>
                                        @endforeach
                                    </select>
                                </div>


                                <input type="text" hidden name="place_id" id="place_id">
                                <div class="form-group">
                                    <label>{!!trans('main.Files')!!}</label>
                                    @component('component.file_upload_field')@endcomponent
                                </div>
                                <button class='btn btn-success' type='submit'>{!!trans('main.Save')!!}</button>

                        </div>
                        <div class="col-md-6">
                            <span id="page" data-page="create"></span>
                            <button class="btn btn-primary" id="btn_generate_map">{!!trans('main.GenerateLocation')!!}</button>
                            <button class="btn btn-primary btn_google_maps" id="btn_select_location">{!!trans('main.SelectLocation')!!}</button>
                            <br>
                            <span id="error_map"></span>
                            <div class="block_map">
                                <div id="map"></div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script type="text/javascript">
        $(document).on('keydown', '.price_room_type_in_hotel', function(e){
            if (e.keyCode === 13) {
                e.preventDefault();
                $('.price_room_type_in_hotel').blur();
            }
        });
    </script>
 <script>
	

    </script>
@endpush
