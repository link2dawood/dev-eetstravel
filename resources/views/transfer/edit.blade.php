@extends('scaffold-interface.layouts.app')
@section('title','Edit')
@section('content')
    @include('layouts.title',
   ['title' => 'Bus Company', 'sub_title' => 'Bus Company Edit',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Bus Company', 'icon' => 'exchange', 'route' => route('transfer.index')],
   ['title' => 'Edit', 'route' => null]]])
    <section class="content">
        <div class="box box-primary">
            <div class="box box-body border_top_none">
                <form method='POST' action='{{route('transfer.update', ['transfer' => $transfer->id])}}' enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-12">
                        <div class="margin_button">
                            <a href="javascript:history.back()">
                                <button class='btn btn-primary back_btn' type="button">{!!trans('main.Back')!!}</button>
                            </a>
                            <button class='btn btn-success' type='submit'>{!!trans('main.Save')!!}</button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">

                            {{csrf_field()}}
                            {{method_field('PUT')}}
                            <div class="form-group">
                                <label for="name">{!!trans('main.Name')!!}</label>
                                <input id="name" name="name" type="text" class="form-control" value="{!!$transfer->
            name!!}">
                            </div>
                            <div class="form-group">
                                <label for="address_first">{!!trans('main.AddressFirst')!!}</label>
                                <input id="address_first" name="address_first" type="text" class="form-control" value="{!!$transfer->
            address_first!!}">
                            </div>
                            <div class="form-group">
                                <label for="address_second">{!!trans('main.AddressSecond')!!}</label>
                                <input id="address_second" name="address_second" type="text" class="form-control"
                                       value="{!!$transfer->
            address_second!!}">
                            </div>
						@if(!empty($transfer->city))
                            @component('component.city_form', ['country_label' => 'country', 'country_translation' => 'main.Country', 'country_default' => $transfer->country,
                                                                'city_label' => 'city','city_translation' =>'main.City', 'city_default' => \App\Helper\CitiesHelper::getCityById($transfer->city)['name']])
                            @endcomponent
						@endif
                            <div class="form-group">
                                <label for="code">{!!trans('main.Code')!!}</label>
                                <input id="code" name="code" type="text" class="form-control" value="{!!$transfer->
            code!!}">
                            </div>
                            <div class="form-group">
                                <label for="work_phone">{!!trans('main.WorkPhone')!!}</label>
                                <input id="work_phone" name="work_phone" type="text" class="form-control" value="{!!$transfer->
            work_phone!!}">
                            </div>
                            <div class="form-group">
                                <label for="work_fax">{!!trans('main.WorkFax')!!}</label>
                                <input id="work_fax" name="work_fax" type="text" class="form-control" value="{!!$transfer->
            work_fax!!}">
                            </div>
                            <div class="form-group">
                                <label for="work_email">{!!trans('main.WorkEmail')!!}</label>
                                <input id="work_email" name="work_email" type="text" class="form-control" value="{!!$transfer->
            work_email!!}">
                            </div>
                            <div class="form-group">
                                <label for="contact_name">{!!trans('main.ContactName')!!}</label>
                                <input id="contact_name" name="contact_name" type="text" class="form-control" value="{!!$transfer->
            contact_name!!}">
                            </div>
                            <div class="form-group">
                                <label for="contact_phone">{!!trans('main.ContactPhone')!!}</label>
                                <input id="contact_phone" name="contact_phone" type="text" class="form-control" value="{!!$transfer->
            contact_phone!!}">
                            </div>
                            <div class="form-group">
                                <label for="contact_email">{!!trans('main.ContactEmail')!!}</label>
                                <input id="contact_email" name="contact_email" type="text" class="form-control" value="{!!$transfer->
            contact_email!!}">
                            </div>
                            <div class="form-group">
                                <label for="comments">{!!trans('main.Comments')!!}</label>
                                <input id="comments" name="comments" type="text" class="form-control" value="{!!$transfer->
            comments!!}">
                            </div>
                            <div class="form-group">
                                <label for="int_comments">{!!trans('main.IntComments')!!}</label>
                                <input id="int_comments" name="int_comments" type="text" class="form-control" value="{!!$transfer->
            int_comments!!}">
                            </div>
                            <div class="form-group">
                                <label for="website">{!!trans('main.Website')!!}</label>
                                <input id="website" name="website" type="text" class="form-control" value="{!!$transfer->
            website!!}">
                            </div>
                            <div class="form-group">
                                <label for="criteria">{!!trans('main.Criteria')!!}</label>
                            </div>
                            @foreach($criterias as $criteria)
                                <div class="form-group criteria_block">
                                    <input type="checkbox"
                                           @foreach($transfer->criterias as $item)
                                           {{ $criteria->id == $item->criteria_id ? 'checked' : '' }}
                                           @endforeach
                                           value="{{ $criteria->id }}" name="criterias">
                                    <label for="">{{ $criteria->name }}</label>
                                </div>
                            @endforeach

                            <div class="form-group">
                                <label for="rate">{!!trans('main.Rate')!!}</label>
                                <select name="rate" id="rate" class="form-control">
                                    @foreach($rates as $rate)
                                        <option value="{{ $rate->id }}" {{ $errors != null && count($errors) > 0 ? old('rate') == $rate->id ? 'selected' : '' : $transfer->rate == $rate->id ? 'selected' : '' }}>{{ $rate->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            {{--<div class="form-group">--}}
                            {{--<label for="category">Category</label>--}}
                            {{--<select id="category" name="category" class="form-control" value="{!!$transfer->--}}
                            {{--category!!}">--}}
                            {{--<option value="1"> First cat</option>--}}
                            {{--<option value="2"> Second Cat</option>--}}
                            {{--</select>--}}
                            {{--</div>--}}
                            <input type="text" hidden name="place_id" id="place_id">
                            <div class="form-group">
                                <label for="attach">{!!trans('main.Files')!!}</label>
                                @component('component.file_upload_field')@endcomponent
                            </div>
                            @component('component.files', ['files' => $files])@endcomponent
                            <button class='btn btn-success' type='submit'>{!!trans('main.Save')!!}</button>
                        <a href="{{\App\Helper\AdminHelper::getBackButton(route('transfer.index'))}}">
                            <button class='btn btn-warning' type='button'>{!!trans('main.Cancel')!!}</button>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <span id="page" data-page="edit"></span>
                        <button class="btn btn-primary btn_google_maps" id="btn_generate_map">

                        </button>
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
<script src="{{ asset('js/google_map.js') }}"></script>
@endpush