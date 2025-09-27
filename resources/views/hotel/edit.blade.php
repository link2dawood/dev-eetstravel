@extends('scaffold-interface.layouts.app')
@section('title','Edit')
@section('content')
    @include('layouts.title',
   ['title' => 'Hotel', 'sub_title' => 'Hotel Edit',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Hotels', 'icon' => 'hotel', 'route' => route('hotel.index')],
   ['title' => 'Edit', 'route' => null]]])
    <section class="content">
        <div class="box box-primary">
            <div class="box box-body border_top_none">
                <span id="hotel_id_span" data-info="{{$hotel->id}}"></span>
                <form method='POST' action='{{route('hotel.update', ['hotel' => $hotel->id])}}' enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="margin_button">
                                <a href="javascript:history.back()">
                                    <button class='btn btn-primary back_btn' type="button">{!!trans('main.Back')!!}</button>
                                </a>
                                <button class='btn btn-success' type='submit'>{!!trans('main.Save')!!}</button>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="alert alert-danger block-error" style="text-align: center; display: none;">

                            </div>
                        </div>
                     </div>
{{--                    <div class="row">
                        <div class="col-md-12">
                            <div class="nav-tabs-custom">
                                <ul class="nav nav-tabs" role='tablist'>
                                    <li role='presentation' class="active"><a href="#info-tab" aria-controls='info-tab' role='tab' data-toggle='tab'>Info</a></li>
                                    <li role='presentation'><a href="#contact-tab" aria-controls='contact-tab' role='tab' data-toggle='tab'>Contacts</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
--}}
                    <div class="tab-content">
                        <div class="tab-pane fade in active" role='tabpanel' id='info-tab'>
                           <div class="row">
                               <div class="col-md-6">
                                   {{csrf_field()}}
                                   {{method_field('PUT')}}
                                   <div class="form-group">
                                       <label for="name">{!!trans('main.Name')!!}</label>
                                       <input id="name" name="name" type="text" class="form-control"
                                              value="{!!$hotel->name!!}">
                                   </div>
                                   <div class="form-group">
                                       <label for="address_first">{!!trans('main.AddressFirst')!!}</label>
                                       <input id="address_first" name="address_first" type="text" class="form-control"
                                              value="{!!$hotel->address_first!!}">
                                   </div>
                                   <div class="form-group">
                                       <label for="address_second">{!!trans('main.AddressSecond')!!}</label>
                                       <input id="address_second" name="address_second" type="text" class="form-control"
                                              value="{!!$hotel->
                address_second!!}">
                                   </div>
								   @if(!empty($hotel->city) && is_numeric($hotel->city))
                                   @component('component.city_form', ['country_label' => 'country', 'country_translation' => 'main.Country', 'country_default' => $hotel->country,
                                    'city_label' => 'city','city_translation' =>'main.City', 'city_default' => \App\Helper\CitiesHelper::getCityById($hotel->city)['name']])
                                   @endcomponent
								   @else
								   @component('component.city_form', ['country_label' => 'country', 'country_translation' => 'main.Country', 'country_default' =>  $hotel->country,
                                    'city_label' => 'city','city_translation' =>'main.City', 'city_default' => $hotel->city])
                                   @endcomponent
								   @endif
                                   <div class="form-group">
                                       <label for="code">{!!trans('main.Code')!!}</label>
                                       <input id="code" name="code" type="text" class="form-control" value="{!!$hotel->
                code!!}">
                                   </div>

                                   <div class="form-group">
                                       <label for="work_phone">{!!trans('main.WorkPhone')!!}</label>
                                       <input id="work_phone" name="work_phone" type="text" class="form-control" value="{!!$hotel->
                work_phone!!}">
                                   </div>
                                   <div class="form-group">
                                       <label for="work_fax">{!!trans('main.WorkFax')!!}</label>
                                       <input id="work_fax" name="work_fax" type="text" class="form-control" value="{!!$hotel->
                work_fax!!}">
                                   </div>
                                   <div class="form-group">
                                       <label for="work_email">{!!trans('main.WorkEmail')!!}</label>
                                       <input id="work_email" name="work_email" type="text" class="form-control" value="{!!$hotel->
                work_email!!}">
                                   </div>
                                   <div class="form-group">
                                       <label for="contact_name">{!!trans('main.ContactName')!!}</label>
                                       <input id="contact_name" name="contact_name" type="text" class="form-control"
                                              value="{!!$hotel->
                contact_name!!}">
                                   </div>
                                   <div class="form-group">
                                       <label for="contact_phone">{!!trans('main.ContactPhone')!!}</label>
                                       <input id="contact_phone" name="contact_phone" type="text" class="form-control"
                                              value="{!!$hotel->
                contact_phone!!}">
                                   </div>
                                   <div class="form-group">
                                       <label for="contact_email">{!!trans('main.ContactEmail')!!}</label>
                                       <input id="contact_email" name="contact_email" type="text" class="form-control"
                                              value="{!!$hotel->
                contact_email!!}">
                                   </div>
								   <div class="form-group">
									<label for="password">{!!trans('password')!!}</label>
									<input id="password" name="password" type="password" class="form-control" >
								 </div>
                                   <div class="form-group">
                                       <label for="comments">{!!trans('main.Comments')!!}</label>
                                       <input id="comments" name="comments" type="text" class="form-control" value="{!!$hotel->
                comments!!}">
                                   </div>
                                   <div class="form-group">
                                       <label for="int_comments">{!!trans('main.IntComments')!!}</label>
                                       <input id="int_comments" name="int_comments" type="text" class="form-control"
                                              value="{!!$hotel->
                int_comments!!}">
                                   </div>
                                   <div class="form-group">
                                       <label for="website">{!!trans('main.Website')!!}</label>
                                       <input id="website" name="website" type="text" class="form-control" value="{!!$hotel->website!!}">
                                   </div>
                                   <div class="form-group">
                                       <label for="city_tax">{!!trans('main.CityTax')!!}</label>
                                       <input id="city_tax" name="city_tax" type="text" class="form-control" value="{!!$hotel->city_tax!!}">
                                   </div>

                               <!--
                                <div class="form-group">
                                    <label>Prices Room Type</label>
                                    <div class="block-list-prices-room-type">
                                        @foreach($room_types as $room_type)
                                   <div class="item-price-room-type">
                                       <span>{{ $room_type->name }}</span>
                                                <input type="number" min="0" name="prices_room_type[{{$room_type->id}}]" value="{{ $room_type->price }}" class="form-control price_room_type_in_hotel">
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
                                           <input type="checkbox"
                                                  @foreach($hotel->criterias as $item)
                                                  {{ $criteria->id == $item->criteria_id ? 'checked' : '' }}
                                                  @endforeach
                                                  value="{{ $criteria->id }}" name="criterias">
                                           <label for="">{{ $criteria->name }}</label>
                                       </div>
                                   @endforeach

                                   <div class="form-group">
										<label for="rate">{!! trans('main.Rate') !!}</label>
										<select name="rate" id="rate" class="form-control">
											@foreach($rates as $rate)
												<option value="{{ $rate->id }}" {{ ($errors != null && count($errors) > 0) ? (old('rate') == $rate->id ? 'selected' : '') : ($hotel->rate == $rate->id ? 'selected' : '') }}>{{ $rate->name }}</option>
											@endforeach
										</select>
									</div>


                                   <div class="form-group">
                                       <label for="status">{!!trans('main.Status')!!}</label>
                                       <input type="checkbox" id="status" name="status" value="@if (!$hotel->status) true @else false @endif" @if (!$hotel->status) checked @endif> Closed<br>
                                   </div>

                                   <input type="text" hidden name="place_id" id="place_id">

                                   <div class="form-group">
                                       <label for="attach">{!!trans('main.Files')!!}</label>
                                       @component('component.file_upload_field')@endcomponent
                                   </div>
                                   @component('component.files', ['files' => $files])@endcomponent
                                   <button class='btn btn-success' type='submit'>{!!trans('main.Save')!!}</button>
                                   <a href="{{\App\Helper\AdminHelper::getBackButton(route('hotel.index'))}}">
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

                                   <br/>
                                   <br/>
                                   <br/>
                                    <div class="margin_button">
                                        <button class='btn btn-success' id="add_contact" type='button'>
                                            <i class="fa fa-plus"></i>
                                            {!!trans('main.AddContact')!!}
                                        </button>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div id="items-contacts">

                                            </div>
                                        </div>
                                    </div>
                               </div>
                           </div>
                        </div>
                        <div class="tab-pane fade" role='tabpanel' id='contact-tab'>
{{--
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="margin_button">
                                        <button class='btn btn-success' id="add_contact" type='button'>
                                            <i class="fa fa-plus"></i>
                                            {!!trans('main.AddContact')!!}
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div id="items-contacts">

                                    </div>
                                </div>
                            </div>
--}}
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade in" id="question_modal" tabindex="-1" role="dialog" aria-labelledby="myQuestionLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">×</span></button>
                        <h4 class="modal-title" id="myModalLabel">{!!trans('main.Warning')!!}!!</h4></div>
                    <div class="modal-body">
                        <div class="modal-body">{!!trans('main.Wouldyoulikesetclosedthishotel')!!}?</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">{!!trans('main.Close')!!}</button>
                        <button type="button" class="btn btn-primary pre-loader-func" id="send_agree">{!!trans('main.Agree')!!}</button>
                    </div>
                </div>
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
        
        $('#status').click( function(){
            if (this.checked){
                $('#status').prop('checked', false);
                $('#question_modal').modal();
                $('#status').val('true');
            } else{
                 $('#status').val('false');
            }
        });
        
        $('#send_agree').on('click',function () {
            $('#status').prop('checked', true);
            $('#question_modal').modal('hide');
        });
        
    </script>
    <script type="text/javascript">
        var contactItemCount = 0;
        let hotelId = $('#hotel_id_span').attr('data-info');

        // get all items contacts at document ready
        $.ajax({
            url: '/api/getItemsContacts',
            method: 'GET',
            data: {
                itemCount: contactItemCount,
                hotelId: hotelId
            }
        }).done((res) => {
            contactItemCount = res.count;
            $('#items-contacts').append(res.content);
        });

        // add new empty contact item
        $('#add_contact').on('click', function () {
            $.ajax({
                url: '/api/getItemContactView',
                method: 'GET',
                data: {
                    itemCount: contactItemCount + 1
                }
            }).done((res) => {
                contactItemCount++;
                $('#items-contacts').append(res);
            })
        });

        // delete item contact
        $(document).on('click', '#delete_contact_item', function () {
            $(this).closest('.item-contact').remove();
        });
    </script>
@endpush
