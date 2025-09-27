@extends('scaffold-interface.layouts.app')
@section('title','Edit')
@section('content')
    @include('layouts.title',
   ['title' => 'Tour', 'sub_title' => 'Edit Tour',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Tours', 'icon' => 'suitcase', 'route' => route('tour.index')],
   ['title' => 'Edit', 'route' => null]]])
    @php
        $tab = '' ;
        $uri_parts = explode('?', \Request::fullUrl() );
        if(count($uri_parts)>1){
           $tab_parts = explode('=', $uri_parts[1]);
           if($tab_parts[0] == 'tab') $tab = $uri_parts[1];
        }
    @endphp
    {{-- modal for service table --}}
    <div class="modal fade" role='dialog' id="service-modal">
        <div class="modal-dialog modal-lg" role='document'>
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss='modal' aria-label="Close"><span
                                aria-hidden='true'>&times;</span></button>
                    <h4 class="modal-title">{!!trans('main.Addservice')!!}</h4>
                    {{-- <div class="col-md-6"> --}}
                    <form action="{{route('supplier_show')}}">
                        <div class="form-group">
                            <select id="service-select" class="form-control">
                                <option selected>{!!trans('main.All')!!}</option>
                                @foreach($options as $option)
                                    <option>{{$option}}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                    {{-- </div> --}}
                </div>
                <div class="modal-body">
                    <table id="search-table" class="table table-striped table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>{!!trans('main.Name')!!}</th>
                            <th>{!!trans('main.Address')!!}</th>
                            <th>{!!trans('main.Phone')!!}</th>
                            <th>{!!trans('main.ContactName')!!}</th>
                            {{-- <th>Contact Phone</th> --}}
                            <th></th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <form method='POST' action='{!! url("tour")!!}/{!!$tour->id!!}/update'
                      enctype="multipart/form-data" id="tour_form">
                    <input type="hidden" id="tab" name="tab" value="{{ $tab }}" >
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


                @if(session('message_buses'))
                    <div class="alert alert-info block-error-driver" style="text-align: center;">
                        {{session('message_buses')}}
                    </div>
                @endif

                <div class="alert alert-info block-error-driver" style="text-align: center; display: none;">

                </div>

                <div class="row">
                    <div class="col-md-12">

                            <div class="row">
                                <div class="col-md-6">
                                    {{csrf_field()}}
                                    <div class="form-group">
                                        <label for="name">{!!trans('main.Name')!!}</label>
                                        <input id="name" name="name" type="text" class="form-control"
                                               value="{!!$tour->name!!}">
                                    </div>
                                    <div class="form-group">
                                        <label for="external_name">{!!trans('main.ExternalName')!!}</label>
                                        <input id="external_name" name="external_name" type="text" disabled
                                               class="form-control" value="{!!$tour->external_name!!}">
                                    </div>
                                   

                                    <div class="form-group">
                                        <label for="departure_date">{!!trans('main.DepDate')!!}</label>
                                        <div class="input-group date">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            {!! Form::text('departure_date', $tour->departure_date, ['class' => 'form-control pull-right datepicker',
                                             'id' => 'departure_date']) !!}
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="retirement_date">{!!trans('main.RetDate')!!}</label>
                                        <div class="input-group date">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            {!! Form::text('retirement_date', $tour->retirement_date, ['class' => 'form-control pull-right datepicker',
                                             'id' => 'retirement_date']) !!}
                                        </div>
                                    </div>
                                    
                                   
									<div class="form-group" >
										<label for="assigned_user">{!! trans('main.AssignedUser') !!} *</label>
										<div class ="form-control" style="max-height:200px !important;overflow-x:auto;height: auto; ">
										@php $i = 1; @endphp
											@foreach ($users as $user)
											<span style="font-size: 18px; margin-right:15px; margin-left:15px; ">{{ $user->name }}
												<input type="checkbox" name="assigned_user" id="assigned_user" value="{{ $user->id }}" {{$user->selected ? 'checked' : ''}}>
											</span>
										<span>|</span>

											@if($i % 6 == 0)
												<br>
											@endif

											@php $i += 1; @endphp
										@endforeach
										</div>
									</div>
                                    <div class="form-group">
                                        <label for="responsible_user">{!!trans('main.ResponsibleUser')!!}</label>
                                        <select name="responsible_user" class="form-control" id="responsible_user">
                                            <option value="0">{!!trans('main.Withoutresponsibleuser')!!}</option>
                                            @foreach($users as $user)
                                                <option value="{{$user->id}}" {{$tour->getResponsibleUser() ?
                                                 $tour->getResponsibleUser()->id == $user->id ? "selected='selected'" : '' :
                                                 ''}}>{{$user->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
										<label for="status">{!!trans('main.Status')!!}</label>
										<select name="status" id="status" class="form-control">
											@foreach($statuses as $status)
												<option value="{{ $status->id }}"
													{{ ($errors != null && count($errors) > 0) ? (old('status') == $status->id ? 'selected' : '') : ($tour->status == $status->id ? 'selected' : '') }}>
													{{ $status->name }}
												</option>
											@endforeach
										</select>
									</div>


                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pax">Pax</label>
                                        <input id="pax" name="pax" type="text" class="form-control"
                                               value="{!!$tour->pax!!}">
                                    </div>
									<div class="form-group">
										<label for="child_count">Number of Children:</label>
										@if(empty($tour->childrens))
										<input type="number" id="child_count" name="child_count" class="form-control" >
										@else
										<input type="number" id="child_count" name="child_count" class="form-control" value= "{!!count($tour->childrens)!!}">
										@endif
									</div>
									@php $i = 0; @endphp
									<div id="child_details">
									@if(!empty($tour->childrens))
									@foreach($tour->childrens as $chd)
									@php $i++ @endphp
									
										<div class="form-group">
												<label for="age_1">Age of Child {{$i}}:</label>
												<input type="number" id="age_1" name="ages[]" class="form-control" min="0" value="{{$chd->age}}">
												<label for="price_1">Price:</label>
												<input type="number" id="price_1" name="prices[]" class="form-control" value="{{$chd->price}}">
										</div>
									
									@endforeach
									@endif
										</div>

									 <button type="button" onclick="addChildFields()" class="btn btn-primary">Add Child</button>
      						
                                    <div class="form-group">
                                        <label for="pax_free">{!!trans('main.PaxFree')!!}</label>
                                        <input id="pax_free" name="pax_free" type="text" class="form-control"
                                               value="{!!$tour->getAttributes()['pax_free']!!}">
                                    </div>

                                    {{--
                                    <div class="form-group">
                                        <label for="rooms">{!!trans('main.Rooms')!!}</label>
                                        <input id="rooms" name="rooms" type="text" class="form-control"
                                               value="{!!$tour->rooms!!}">
                                    </div>
                                    --}}

                                    <div class="form-group">
                                        <label >{!!trans('main.RoomTypes')!!}</label>

                                        <div id="list_selected_room_types">
                                            @if(!empty($selected_room_types))
                                                @foreach($selected_room_types as $item)
                                                    @include('component.item_hotel_room_type', ['room_type' => $item])
                                                @endforeach
                                            @endif

                                        </div>

                                        <button class="btn btn-success btn_for_select_room_type" type="button">{!!trans('main.SelectRooms')!!}</button>

                                        <ul class="list_room_types">
                                            <ul class="list_room_types" style="display: block; z-index:999;">
                                                @if(!empty($room_types))
                                                    @foreach( $room_types as $room_type)
                                                        <li class="select_room_type">
                                                            <label>{{ $room_type->name }}</label>
                                                            <input type="text" data-info="{{ $room_type->id }}" hidden value="{{ $room_type }}">
                                                        </li>
                                                    @endforeach
                                                @endif
                                            </ul>
                                        </ul>

                                    </div>


                                    <div class="form-group">
                                        <label for="itinerary">{!! trans('main.tourleader') !!}</label>
                                            {!! Form::text('itinerary_tl',$tour->itinerary_tl, ['class' => 'form-control', 'id'=>'itinerary']) !!}
                                    </div>
                                    <div class="form-group">
                                        <label for="phone">{!!trans('main.Phone')!!}</label>
                                        <input id="phone" name="phone" type="text" class="form-control"
                                               value="{!!$tour->phone!!}">
                                    </div>
                                    <div class="form-group">
                                        <label for="attach">{!!trans('main.Files')!!}</label>
                                        @component('component.file_upload_field')@endcomponent
                                    </div>
                                    <input type="text" hidden name="calendar_edit" value="{{ $calendar_edit }}">

                                    <div class="form-group">
{{--                                        
                                        <div class="thumbnail text-center">
                                            <img class="pic" src="@if($tour->attachments()->first() != null) {{ $tour->attachments()->first()->url }} @endif" alt="Image for landing page" style="width:100%">
                                            <div class="caption">
                                                <div class="upload-btn-wrapper">
                                                    <button class="btn btn-primary">Change</button>
                                                    <input name="fileToUpload[]" data-model="Tour" data-id="{{ $tour->id }}"class="fileToUpload"type="file" name="myfile" />
                                                </div>
                                            </div>
                                        </div>
--}}                                        
                                        <label for="attach">{!!trans('main.imageforlanding')!!}</label>
                                        <div>
                                            <div class="file-preview thumbnail">
                                                <div class="file-drop-zone-title" style="padding:15px 10px;"><center>Image for landing page</center>
                                                    <img class="pic" src="@if($tour->attachments()->first() != null) {{ $tour->attachments()->first()->url }} @endif" style="width:100%">
                                                </div>
                                                                                           
                                            </div>
                                        </div>

                                        <div class="input-group file-caption-main">
                                            <div tabindex="500" class="form-control">
                                            <div class="file-caption-name"></div>
                                            </div>

                                                <div class="input-group-btn">
                                                    <div tabindex="500" class="btn btn-primary btn-file"><i class="glyphicon glyphicon-folder-open"></i>&nbsp;  <span class="hidden-xs">Browse …</span>
                                                        <input name="fileToUpload[]" data-model="Tour" data-id="{{ $tour->id }}"class="fileToUpload"type="file" name="myfile" />
                                                    </div>
                                            </div>
                                         </div>
                                    </div>    
                                    <span id="url" hidden data-url="{{ route('images.savefile') }}"></span>
                                </div>

                            </div>
                            <button class='btn btn-success' type='submit'>{!!trans('main.Save')!!}</button>
                            <a href="{{\App\Helper\AdminHelper::getBackButton(route('tour.index'))}}">
                                <button class='btn btn-warning' type='button'>{!!trans('main.Cancel')!!}</button>
                            </a>

                    </div>
                </div>
                </form>
            </div>
        </div>
    </section>
    <span id="tour_dates" data-departure_date='{{$tour->departure_date}}'
          data-retirement_date='{{$tour->retirement_date}}'></span>
    <span id="tour_date_id" data-tour-id="{{ $tour->id }}"></span>
    {{-- <span id="tour_dates" data-departure_date='{{$tour->departure_date}}' data-retirement_date='{{$tour->retirement_date}}'></span> --}}
    <input type="text" id="default_reference_id" hidden name="reference_id" value="{{ $tour->id }}">
@endsection
@push('scripts')
    <script type="text/javascript" src='{{asset('js/supplier-search.js')}}'></script>
    <script type="text/javascript" src='{{asset('js/rooms.js')}}'></script>
    <script type="text/javascript" src='{{asset('js/tour.js')}}'></script>
    <script type="text/javascript" src='{{asset('js/hide_elements.js')}}'></script>
    <script type="text/javascript" src='{{asset('js/attachments.js')}}'></script>
<script>
	function addChildFields() {
    var count = document.getElementById('child_count').value;
    var container = document.getElementById('child_details');
    
    // Clear previous fields
    container.innerHTML = '';
    
    for (var i = 1; i <= count; i++) {
        var div = document.createElement('div');
        div.classList.add('form-group');
        div.innerHTML = `
            <label for="age_${i}">Age of Child ${i}:</label>
            <input type="number" id="age_${i}" name="ages[]" class="form-control" min="0">
            <label for="price_${i}">Price:</label>
            <input type="number" id="price_${i}" name="prices[]" class="form-control">
        `;
        container.appendChild(div);
    }
}
$(document).ready(function() {
    $('#tour_form').on('submit', function(e) {
        // Prevent the default form submission
        e.preventDefault();

        // Check if there's an active AJAX request
        if ($.active > 0) {
            // Abort the AJAX request
            $.ajax.abort();
        }

        // Submit the form
        $(this).unbind('submit').submit();
    });
});

</script>
@endpush