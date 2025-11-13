@extends('scaffold-interface.layouts.tabler-app')
@section('title','Edit Tour')

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

    {{-- Modal for service table --}}
    <div class="modal modal-blur fade" role='dialog' id="service-modal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered" role='document'>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{!!trans('main.Addservice')!!}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss='modal' aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{route('supplier_show')}}">
                        <div class="mb-3">
                            <select id="service-select" class="form-select">
                                <option selected>{!!trans('main.All')!!}</option>
                                @foreach($options as $option)
                                    <option>{{$option}}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table id="search-table" class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>{!!trans('main.Name')!!}</th>
                                <th>{!!trans('main.Address')!!}</th>
                                <th>{!!trans('main.Phone')!!}</th>
                                <th>{!!trans('main.ContactName')!!}</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <form method='POST' action='{{ url("tour/{$tour->id}/update") }}' enctype="multipart/form-data" id="tour_form">
                @csrf
                <input type="hidden" id="tab" name="tab" value="{{ $tab }}" >
                <input type="hidden" name="reference_id" value="{{ $tour->id }}">
                <input type="hidden" name="calendar_edit" value="{{ $calendar_edit }}">

                {{-- Action Buttons --}}
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="btn-list">
                            <a href="javascript:history.back()" class='btn btn-secondary'>
                                <i class="ti ti-arrow-left me-1"></i>
                                {!!trans('main.Back')!!}
                            </a>
                            <button class='btn btn-primary' type='submit' id="submitBtn">
                                <i class="ti ti-device-floppy me-1"></i>
                                {!!trans('main.Save')!!}
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Messages --}}
                @if(session('message_buses'))
                    <div class="alert alert-info alert-dismissible" role="alert">
                        <div class="d-flex">
                            <div>
                                <i class="ti ti-info-circle icon alert-icon"></i>
                            </div>
                            <div>
                                {{session('message_buses')}}
                            </div>
                        </div>
                        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                    </div>
                @endif

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

                <div class="alert alert-info block-error-driver" style="display: none;"></div>

                {{-- Main Form Card --}}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Tour Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            {{-- Left Column --}}
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required" for="name">{!!trans('main.Name')!!}</label>
                                    <input id="name" name="name" type="text" class="form-control"
                                           value="{!!old('name', $tour->name)!!}" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="external_name">{!!trans('main.ExternalName')!!}</label>
                                    <input id="external_name" name="external_name" type="text" disabled
                                           class="form-control" value="{!!$tour->external_name!!}">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label required" for="departure_date">{!!trans('main.DepDate')!!}</label>
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <i class="ti ti-calendar"></i>
                                        </span>
                                        {!! Form::text('departure_date', old('departure_date', $tour->departure_date), ['class' => 'form-control datepicker',
                                         'id' => 'departure_date', 'placeholder' => 'Select date', 'required' => true]) !!}
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label required" for="retirement_date">{!!trans('main.RetDate')!!}</label>
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <i class="ti ti-calendar"></i>
                                        </span>
                                        {!! Form::text('retirement_date', old('retirement_date', $tour->retirement_date), ['class' => 'form-control datepicker',
                                         'id' => 'retirement_date', 'placeholder' => 'Select date', 'required' => true]) !!}
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label required">{!! trans('main.AssignedUser') !!}</label>
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
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="responsible_user">{!!trans('main.ResponsibleUser')!!}</label>
                                    <select name="responsible_user" class="form-select" id="responsible_user">
                                        <option value="0">{!!trans('main.Withoutresponsibleuser')!!}</option>
                                        @foreach($users as $user)
                                            <option value="{{$user->id}}" {{$tour->getResponsibleUser() ?
                                             $tour->getResponsibleUser()->id == $user->id ? "selected='selected'" : '' :
                                             ''}}>{{$user->name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label required" for="status">{!!trans('main.Status')!!}</label>
                                    <select name="status" id="status" class="form-select">
                                        @foreach($statuses as $status)
                                            <option value="{{ $status->id }}"
                                                {{ ($errors != null && count($errors) > 0) ? (old('status') == $status->id ? 'selected' : '') : ($tour->status == $status->id ? 'selected' : '') }}>
                                                {{ $status->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Right Column --}}
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="pax">Pax</label>
                                    <input id="pax" name="pax" type="number" class="form-control"
                                           value="{!!old('pax', $tour->pax)!!}">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="child_count">Number of Children</label>
                                    @if(empty($tour->childrens))
                                    <input type="number" id="child_count" name="child_count" class="form-control" value="{{ old('child_count', 0) }}" min="0">
                                    @else
                                    <input type="number" id="child_count" name="child_count" class="form-control" value="{{ old('child_count', count($tour->childrens)) }}" min="0">
                                    @endif
                                </div>

                                @php $i = 0; @endphp
                                <div id="child_details" class="mb-3">
                                @if(!empty($tour->childrens))
                                @foreach($tour->childrens as $chd)
                                @php $i++ @endphp
                                    <div class="card card-sm mb-2 child-field">
                                        <div class="card-body">
                                            <div class="row g-2">
                                                <div class="col-md-6">
                                                    <label class="form-label" for="age_{{$i}}">Age of Child {{$i}}</label>
                                                    <input type="number" id="age_{{$i}}" name="ages[]" class="form-control" min="0" value="{{ old('ages.'.$loop->index, $chd->age) }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label" for="price_{{$i}}">Price</label>
                                                    <input type="number" id="price_{{$i}}" name="prices[]" class="form-control" step="0.01" value="{{ old('prices.'.$loop->index, $chd->price) }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                @endif
                                </div>

                                <button type="button" onclick="addChildFields()" class="btn btn-primary mb-3">
                                    <i class="ti ti-refresh me-1"></i>
                                    Update Child Fields
                                </button>

                                <div class="mb-3">
                                    <label class="form-label" for="pax_free">{!!trans('main.PaxFree')!!}</label>
                                    <input id="pax_free" name="pax_free" type="number" class="form-control"
                                           value="{!!old('pax_free', $tour->getAttributes()['pax_free'])!!}">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">{!!trans('main.RoomTypes')!!}</label>

                                    <div id="list_selected_room_types" class="mb-2">
                                        @if(!empty($selected_room_types))
                                            @foreach($selected_room_types as $item)
                                                @include('component.item_hotel_room_type', ['room_type' => $item])
                                            @endforeach
                                        @endif
                                    </div>

                                    <button class="btn btn-success btn_for_select_room_type" type="button">
                                        <i class="ti ti-bed me-1"></i>
                                        {!!trans('main.SelectRooms')!!}
                                    </button>

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

                                <div class="mb-3">
                                    <label class="form-label" for="itinerary">{!! trans('main.tourleader') !!}</label>
                                    {!! Form::text('itinerary_tl', old('itinerary_tl', $tour->itinerary_tl), ['class' => 'form-control', 'id'=>'itinerary']) !!}
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="phone">{!!trans('main.Phone')!!}</label>
                                    <input id="phone" name="phone" type="text" class="form-control"
                                           value="{!!old('phone', $tour->phone)!!}">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="attach">{!!trans('main.Files')!!}</label>
                                    @component('component.file_upload_field')@endcomponent
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="files">{!!trans('main.imageforlanding')!!}</label>

                                    <div class="card card-sm mb-2">
                                        <div class="card-body p-2">
                                            @if($tour->attachments()->first() != null)
                                                <img class="img-fluid rounded" src="{{ $tour->attachments()->first()->url }}" style="width:100%; max-height: 300px; object-fit: cover;">
                                            @else
                                                <div class="text-center py-5 text-muted">
                                                    <i class="ti ti-photo ti-lg mb-2"></i>
                                                    <p>No image uploaded</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="input-group">
                                        <input type="text" class="form-control" id="file-name-display" readonly placeholder="No file chosen">
                                        <label class="btn btn-primary" for="files">
                                            <i class="ti ti-upload me-1"></i>
                                            Browse
                                        </label>
                                        <input name="files[]" id="files" class="fileToUpload d-none" type="file" accept="image/*" />
                                    </div>
                                </div>
                                <span id="url" hidden data-url="{{ route('images.savefile') }}"></span>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <div class="btn-list">
                            <a href="{{\App\Helper\AdminHelper::getBackButton(route('tour.index'))}}" class='btn btn-secondary'>
                                <i class="ti ti-x me-1"></i>
                                {!!trans('main.Cancel')!!}
                            </a>
                            <button class='btn btn-primary' type='submit'>
                                <i class="ti ti-device-floppy me-1"></i>
                                {!!trans('main.Save')!!}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <span id="tour_dates" data-departure_date='{{$tour->departure_date}}'
          data-retirement_date='{{$tour->retirement_date}}'></span>
    <span id="tour_date_id" data-tour-id="{{ $tour->id }}"></span>
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
        div.classList.add('card', 'card-sm', 'mb-2', 'child-field');
        div.innerHTML = `
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-md-6">
                        <label class="form-label" for="age_${i}">Age of Child ${i}</label>
                        <input type="number" id="age_${i}" name="ages[]" class="form-control" min="0" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="price_${i}">Price</label>
                        <input type="number" id="price_${i}" name="prices[]" class="form-control" step="0.01" required>
                    </div>
                </div>
            </div>
        `;
        container.appendChild(div);
    }
}

// Display selected file name
document.addEventListener('DOMContentLoaded', function() {
    var fileInput = document.getElementById('files');
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            var fileName = e.target.files[0] ? e.target.files[0].name : 'No file chosen';
            document.getElementById('file-name-display').value = fileName;
        });
    }

    // Prevent multiple form submissions
    var form = document.getElementById('tour_form');
    var submitBtn = document.getElementById('submitBtn');

    if (form && submitBtn) {
        form.addEventListener('submit', function(e) {
            // Disable submit button to prevent double submission
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Saving...';
        });
    }
});
</script>
@endpush
