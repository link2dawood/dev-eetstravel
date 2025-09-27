@extends('scaffold-interface.layouts.app')
@section('title','Edit')
@section('content')
    @include('layouts.title',
   ['title' => 'Restaurant', 'sub_title' => 'Edit Restaurant',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Restaurants', 'icon' => 'coffee', 'route' => route('restaurant.index')],
   ['title' => 'Edit', 'route' => null]]])
    <section class="content">
        <div class="box box-primary">
            <div class="box box-body border_top_none">
                <form method='POST' action='{{route('restaurant.update', ['restaurant' => $restaurant->id])}}' enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-12">
                        <div class="margin_button">
                            <a href="javascript:history.back()">
                                <button class='btn btn-primary back_btn' type="button">{{trans('main.Back')}}</button>
                            </a>
                            <button class='btn btn-success' type='submit'>{{trans('main.Save')}}</button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">

                            {{csrf_field()}}
                            {{method_field('PUT')}}
                            <div class="form-group">
                                <label for="name">{{trans('main.Name')}}</label>
                                <input id="name" name="name" type="text" class="form-control" value="{!!$restaurant->
            name!!}">
                            </div>
                            <div class="form-group">
                                <label for="address_first">{{trans('main.AddressFirst')}}</label>
                                <input id="address_first" name="address_first" type="text" class="form-control" value="{!!$restaurant->
            address_first!!}">
                            </div>
                            <div class="form-group">
                                <label for="address_second">{{trans('main.AddressSecond')}}</label>
                                <input id="address_second" name="address_second" type="text" class="form-control"
                                       value="{!!$restaurant->
            address_second!!}">
                            </div>
							@php
						$test = "";
						if(!empty($restaurant->city) && is_numeric($restaurant->city)){
						$test = \App\Helper\CitiesHelper::getCityById($restaurant->city)['name'];
						}
							@endphp
		
                            @component('component.city_form', ['country_label' => 'country', 'country_translation' => 'main.Country', 'country_default' => $restaurant->country??"",
                                                                'city_label' => 'city','city_translation' =>'main.City', 'city_default' => $test])
                            @endcomponent
				
                            <div class="form-group">
                                <label for="work_phone">{{trans('main.WorkPhone')}}</label>
                                <input id="work_phone" name="work_phone" type="text" class="form-control" value="{!!$restaurant->
            work_phone!!}">
                            </div>
                            <div class="form-group">
                                <label for="work_fax">{{trans('main.WorkFax')}}</label>
                                <input id="work_fax" name="work_fax" type="text" class="form-control" value="{!!$restaurant->
            work_fax!!}">
                            </div>
                            <div class="form-group">
                                <label for="work_email">{{trans('main.WorkEmail')}}</label>
                                <input id="work_email" name="work_email" type="text" class="form-control" value="{!!$restaurant->
            work_email!!}">
                            </div>
                            <div class="form-group">
                                <label for="contact_name">{{trans('main.ContactName')}}</label>
                                <input id="contact_name" name="contact_name" type="text" class="form-control" value="{!!$restaurant->
            contact_name!!}">
                            </div>
                            <div class="form-group">
                                <label for="contact_phone">{{trans('main.ContactPhone')}}</label>
                                <input id="contact_phone" name="contact_phone" type="text" class="form-control" value="{!!$restaurant->
            contact_phone!!}">
                            </div>
                            <div class="form-group">
                                <label for="contact_email">{{trans('main.ContactEmail')}}</label>
                                <input id="contact_email" name="contact_email" type="text" class="form-control" value="{!!$restaurant->contact_email!!}">
                            </div>
                            <div class="form-group">
                                <label for="comments">{{trans('main.Comments')}}</label>
                                <input id="comments" name="comments" type="text" class="form-control" value="{!!$restaurant->comments!!}">
                            </div>
                            <div class="form-group">
                                <label for="int_comments">{{trans('main.IntComments')}}</label>
                                <input id="int_comments" name="int_comments" type="text" class="form-control" value="{!!$restaurant->int_comments!!}">
                            </div>
                            <div class="form-group">
                                <label for="website">{{trans('main.Website')}}</label>
                                <input id="website" name="website" type="text" class="form-control" value="{!!$restaurant->website!!}">
                            </div>
                            <div class="form-group">
                                <label for="code">{{trans('main.Code')}}</label>
                                <input id="code" name="code" type="text" class="form-control" value="{!!$restaurant->code!!}">
                            </div>

                            <div class="form-group">
                                <label for="criteria">{{trans('main.Criteria')}}</label>
                            </div>
                            @foreach($criterias as $criteria)
                                <div class="form-group criteria_block">
                                    <input type="checkbox"
                                           @foreach($restaurant->criterias as $item)
                                           {{ $criteria->id == $item->criteria_id ? 'checked' : '' }}
                                           @endforeach
                                           value="{{ $criteria->id }}" name="criterias">
                                    <label for="">{{ $criteria->name }}</label>
                                </div>
                            @endforeach
			
                            <div class="form-group">
    				<label for="rate">Rate</label>
    				<select name="rate" id="rate" class="form-control">
        			<?php $__currentLoopData = $rates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            			<?php $selected = ($errors != null && count($errors) > 0) ? (old('rate') == $rate->id ? 'selected' : '') : ($restaurant->rate == $rate->id ? 'selected' : ''); ?>
            				<option value="<?php echo e($rate->id); ?>" <?php echo e($selected); ?>><?php echo e($rate->name); ?></option>
       				 <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    					</select>		 	    
			    </div>

                            <input type="text" hidden name="place_id" id="place_id">
                            <div class="form-group">
                                <label for="attach">Files</label>
                                @component('component.file_upload_field')@endcomponent
                            </div>
                            @component('component.files', ['files' => $files])@endcomponent
                            <button class='btn btn-success' type='submit'>{{trans('main.Save')}}</button>
                        <a href="{{\App\Helper\AdminHelper::getBackButton(route('restaurant.index'))}}">
                            <button class='btn btn-warning' type='button'>{{trans('main.Cancel')}}</button>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <span id="page" data-page="edit"></span>
                        <button class="btn btn-primary btn_google_maps" id="btn_generate_map">
                        </button>
                        <button class="btn btn-primary btn_google_maps" id="btn_select_location">{{trans('main.SelectLocation')}}</button>
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