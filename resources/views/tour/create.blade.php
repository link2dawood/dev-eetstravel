@extends('scaffold-interface.layouts.tabler-app')
@section('title','Create')
@section('post_styles')
<style>
    /* Enhanced checkbox/selectgroup styling */
    .form-selectgroup-item {
        flex: 1;
    }

    .form-selectgroup-label {
        border: 1px solid rgba(98, 105, 118, 0.16);
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.2s ease;
        background: #fff;
        min-height: 42px;
    }

    .form-selectgroup-label:hover {
        border-color: #206bc4;
        background: rgba(32, 107, 196, 0.02);
    }

    .form-selectgroup-input:checked ~ .form-selectgroup-label {
        border-color: #206bc4;
        background: rgba(32, 107, 196, 0.06);
        font-weight: 500;
    }

    .form-selectgroup-check {
        display: inline-block;
        width: 18px;
        height: 18px;
        border: 2px solid #d1d5db;
        border-radius: 3px;
        transition: all 0.2s ease;
        position: relative;
    }

    .form-selectgroup-input:checked ~ .form-selectgroup-label .form-selectgroup-check {
        background: #206bc4;
        border-color: #206bc4;
    }

    .form-selectgroup-input:checked ~ .form-selectgroup-label .form-selectgroup-check::after {
        content: '';
        position: absolute;
        left: 5px;
        top: 2px;
        width: 4px;
        height: 8px;
        border: solid white;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
    }
</style>
@endsection
@section('content')

    @include('layouts.title',
   ['title' => $title, 'sub_title' => $subTitle,
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Tours', 'icon' => 'suitcase', 'route' => route('tour.index')],
   ['title' => 'Create', 'route' => null]]])
    <section class="content">
        
        <div class="box box-primary">
            <div class="box box-body border_top_none">
                @if (count($errors) > 0)
                    <br>
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form method='POST' action="{{url('tour/save')}}"  enctype="multipart/form-data" >
<!-- action='{!!url("tour")!!}' -->
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
                    
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <div class="d-flex">
                            <div>
                                <i class="ti ti-alert-circle icon alert-icon"></i>
                            </div>
                            <div>
                                <h4 class="alert-title">Validation Errors</h4>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                    </div>
                @endif

                    <div class="row">
                        {{csrf_field()}}
                        @if($isQuotation)
                            @include('component.js-validate')
                        @endif
                        <div class="col-md-6">
                            <input type='hidden' name='_token' value='{{Session::token()}}'>
                            <div class="form-group">
                                <label for="name">{!!trans('main.Name')!!} *</label>
                                {!! Form::text('name', '', ['class' => 'form-control']) !!}
                            </div>
                            
                            <div class="form-group">

                                <label for="departure_date">{!!trans('main.DepDate')!!} *</label>

                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    {!! Form::text('departure_date', '',
                                    ['class' => 'form-control pull-right datepicker', 'id' => 'departure_date', 'autocomplete' => 'off']) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="retirement_date">{!!trans('main.RetDate')!!} *</label>

                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    {!! Form::text('retirement_date', '', ['class' => 'form-control pull-right datepicker', 'id' => 'retirement_date']) !!}
                                </div>
                            </div>

                            {{--
                            <div class="form-group">
                                <label for="rooms">{!!trans('main.Rooms')!!}</label>
                                {!! Form::text('rooms', '', ['class' => 'form-control']) !!}
                            </div>
                            --}}
                            @if(!$isQuotation)
                              
                                <div class="form-group">
                                    <label for="status">{!!trans('main.Status')!!}</label>
                                    <select name="status" id="status" class="form-control">
                                        @foreach($statuses as $status)
                                            <option {{ old('status') == $status->id ? 'selected' : '' }} value="{{ $status->id }}">{{ $status->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
								<!-- <div class="form-group">
									<label for="assigned_user">{!! trans('main.AssignedUser') !!} *</label>
									<div class="form-control" style="max-height:200px !important;overflow-x:auto;height: auto; ">
										<table>
											<tr>
												@php $i = 1; @endphp
												@foreach ($users as $user)
												<td style="width: 30rem;" id="user_data_{{ $user->id }}">
													<label for="user_{{ $user->id }}" style="font-size: 18px;">
														{{ $user->name }}
														
													</label>
														<input class = "user_checkboxes" type="checkbox" name="assigned_user[]" id="user_{{ $user->id }}" value="{{ $user->id }}">
													
													
												</td>
												
												@if($i % 4 == 0)
											</tr>
											<tr>
												@endif
												@php $i += 1; @endphp
												@endforeach
											</tr>
										</table>
									</div>
								</div> -->
                                                                <!-- <div class="mb-3">
                                    <label class="form-label">{!! trans('main.AssignedUser') !!}</label>
                                    <div class="card card-sm">
                                        <div class="card-body" style="max-height:250px; overflow-y:auto;">
                                            <div class="row g-2">
                                                @foreach ($users as $user)
                                                    <div class="col-md-6 col-lg-4">
                                                        <label class="form-selectgroup-item flex-fill">
                                                            <input type="checkbox" name="assigned_user[]" value="{{ $user->id }}"
                                                                   class="form-selectgroup-input" {{$user->selected ? 'checked' : ''}}>
                                                            <div class="form-selectgroup-label d-flex align-items-center p-2">
                                                                <div class="me-2">
                                                                    <span class="form-selectgroup-check"></span>
                                                                </div>
                                                                <div class="form-selectgroup-label-content">
                                                                    <div class="font-weight-medium">{{ $user->name }}</div>
                                                                </div>
                                                            </div>
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div> -->

                                <div class="form-group">
                                    <label for="responsible_user">{!!trans('main.ResponsibleUser')!!}</label>
                                    <select name="responsible_user" class="form-control" id="responsible_user">
                                        <option value="0">{!!trans('main.Withoutresponsibleuser')!!}</option>
                                        @foreach($users as $user)
                                            <option value="{{$user->id}}">{{$user->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @else
                                {{--Status pending--}}
                                {!! Form::hidden('status', 1) !!}
                            @endif
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="pax">Pax</label>
                                {!! Form::text('pax', '', ['class' => 'form-control','id' => 'passenger_count']) !!}
                            </div>
							<div class="form-group">
								<label for="child_count">Number of Children:</label>
								<input type="number" id="child_count" name="child_count" class="form-control">
							</div>

							<div id="child_details">
								<!-- Child details will be added dynamically using JavaScript -->
							</div>

							 <button type="button" onclick="addChildFields()" class="btn btn-primary">Add Child</button>
      
       
                            <div class="form-group">
                                <label for="pax_free">{!!trans('main.PaxFree')!!}</label>
                                {!! Form::text('pax_free', '', ['class' => 'form-control']) !!}
                            </div>
                            <!-- ////////////////// -->
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
                                        @foreach( $room_types as $room_type)
                                            <li class="select_room_type">
                                                <label>{{ $room_type->name }}</label>
                                                <input type="text" data-info="{{ $room_type->id }}" hidden value="{{ $room_type }}">
                                            </li>
                                        @endforeach
                                    </ul>
                                </ul>

                            </div>
                            <!-- ////////////////// -->
                            @if(!$isQuotation)
                            
 {{--                           <div class="form-group">
                                <label for="retirement_date">{!!trans('main.Invoice')!!}</label>

                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    {!! Form::text('invoice','', ['class' => 'form-control pull-right datepicker', 'id' => 'invoice', 'autocomplete' => 'off']) !!}
                                </div>

                            </div>
                            <div class="form-group">
                                <label for="retirement_date">G\A</label>

                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    {!! Form::text('ga','', ['class' => 'form-control pull-right datepicker', 'id' => 'ga', 'autocomplete' => 'off']) !!}
                                </div>

                            </div>--}}
                               
                                <div class="form-group">
                                    <label>{!!trans('main.Files')!!}</label>
                                    @component('component.file_upload_field')@endcomponent 
                                </div>
                                <div class="form-group">
                                        <label for="attach">{!!trans('main.imageforlanding')!!}</label>
                                        <div>
                                            <div class="file-preview thumbnail">
                                                <div class="file-drop-zone-title" style="padding:15px 10px;"><center>Image for landing page</center>
                                                    <img id="pic" src="" style="width:100%">
                                                </div>                                   
                                            </div>
                                        </div>

                                        <div class="input-group file-caption-main">
                                            <div tabindex="500" class="form-control">
                                            <div class="file-caption-name" id="file-caption-name"></div>
                                            </div>

                                                <div class="input-group-btn">
                                                    <div tabindex="500" class="btn btn-primary btn-file"><i class="glyphicon glyphicon-folder-open"></i>&nbsp;  <span class="hidden-xs">Browse …</span>
                                                        <input type="file" name="files[]" id="imgInp" class="fileToUpload" multiple>

                                                    </div>
                                            </div>
                                         </div>
                                    </div>
                            @endif
                            {!! Form::hidden('is_quotation', 1) !!}
                        </div>
                    </div>
                    <button class='btn btn-success' type='submit'>{!!trans('main.Save')!!}</button>
                </form>
            </div>
        </div>
    </section>
    @push('scripts')
   <script type="text/javascript" src='{{asset('js/rooms.js')}}'></script>
   <script type="text/javascript" src='{{asset('js/hide_elements.js')}}'></script>
   <script type="text/javascript" src='{{asset('js/tour.js')}}'></script>
<script type="text/javascript" src='{{asset('js/supplier-search.js')}}'></script>
    <script type="text/javascript" src='{{asset('js/attachments.js')}}'></script>

    
    <script type="text/javascript">
        function readURL(input) {

            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                  $('#pic').attr('src', e.target.result);
                  $('#file-caption-name').html(input.files[0].name); 
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#imgInp").change(function() {
            readURL(this);
        });
		
    </script>
<script>
 function handleCheckboxes() {
  const checkboxes = document.querySelectorAll('.user_checkboxes');

  checkboxes.forEach(function (checkbox) {
    checkbox.addEventListener("click", function () {
      // No need to recreate checkboxes, just update checked state
      console.log("User ID " + this.value + " is now " + (this.checked ? "selected" : "deselected"));
    });
  });
}

// Call the function initially
handleCheckboxes();

// Set an interval to refresh the event handling
setInterval(function () {
  handleCheckboxes();
}, 500); // Adjust the interval time as needed

// // Handle form submission
// $(document).ready(function() {
//     $('#tour-create-form').on('submit', function(e) {
//         var form = $(this);
//         var submitBtn = form.find('button[type="submit"]');
        
//         // Disable submit button to prevent double submission
//         submitBtn.prop('disabled', true);
//         submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Saving...');
        
//         // If form is submitted normally (not AJAX), let it proceed
//         // The server will handle the response appropriately
//     });
// });

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


</script>
@endpush

@endsection