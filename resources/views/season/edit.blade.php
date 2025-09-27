@extends('scaffold-interface.layouts.app')
@section('title', 'Season')
@section('content')
    @include('layouts.title',
       ['title' => 'Season', 'sub_title' => 'Season Edit',
       'breadcrumbs' => [
       ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
       ['title' => 'Season Price', 'icon' => 'fa fa-snowflake-o', 'route' => route('hotel.show', ['hotel' => $hotel->id,'tab' => 'season_tab' ])],
       ['title' => 'Edit', 'route' => null]]])
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
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
                <form method='POST' action='{{route('update_season')}}' >
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
                    {{ csrf_field() }}
                    <input type='hidden' name='id' value='{{$agreement->id}}'>
                    <input type='hidden' name='hotel_id' value='{{$hotel->id}}'>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label >{!!trans('main.Name')!!}*</label>
                                <input type="text" value="{{ $agreement->name }}" name="name" class="form-control"  >
                            </div>
                            <br>
                            <div class="form-group">
                                <label for="departure_date">{!!trans('main.StartDate')!!} *</label>

                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" value="{{ Carbon\Carbon::parse($agreement->start_date)->format('m-d') }}" name="start_date" id="start_date" class="form-control pull-right datepickernoyear"   >
                                </div>

                            </div>

                            <div class="form-group">
                                <label for="departure_date">{!!trans('main.EndDate')!!} *</label>

                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" value="{{ Carbon\Carbon::parse($agreement->end_date )->format('m-d') }}" name="end_date" id="end_date" class="form-control pull-right datepickernoyear"  >

                                </div>

                            </div>




                            {{--
                        @foreach($agreement->agreements_room_types as $item)
                             <span style="display: block">{{ $item->room_types['name'] . ' - ' . $item->count }}</span>
                        @endforeach
                        --}}



                            <div class="form-group">
                                <label >{!!trans('main.RoomTypes')!!}</label>

                                <div id="list_selected_room_types">
                                    @if(!empty($agreement))

                                        @foreach($agreement->seasons_room_types as $item)
                                            {{-- $agreement->getRoom($item->room_type_id)->name }} - {{ $item->price --}} <br>
                                            @include('component.item_season_hotel_room_type', ['room_type' => $item])
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
                                                    <input type="text" data-agreement="{{ $agreement->id }}" data-info="{{ $room_type->id }}" hidden value="{{ $room_type }}">
                                                </li>
                                            @endforeach
                                        @endif
                                    </ul>
                                </ul>

                            </div>
                            {{--
                            <div class="form-group">
                                <label for="departure_date">{!!trans('main.Hotelrooms')!!}</label>

                                @foreach($hotel->prices_room_type as $item)
                                    <span style="display: block">{!!$item->room_types->code . ' - ' . $item->price!!}</span>
                                @endforeach
                            </div>
                            --}}

                            <div class="form-group">
                                <label for="departure_date">{!!trans('main.Type')!!}</label>
                                <select class="form-control" id="type" name="type">
                                    @foreach($season_types as $type)
                                        <option value="{{$type->id}}" @if($type->id == $agreement->type ) selected @endif> {{$type->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="departure_date">{!!trans('main.Description')!!}</label>

                                {!! Form::textarea('description', $agreement->description, ['class' => 'form-control', 'id' => 'description']) !!}
                            </div>
                        </div>
                    </div>
                    <button class='btn btn-success' type='submit'>{!!trans('main.Save')!!}</button>
                    <a href="{{route('hotel.show', ['hotel' => $hotel->id,'tab' => 'season_tab' ])}}">
                        <button class='btn btn-warning' type='button'>{!!trans('main.Cancel')!!}</button>
                    </a>
                </form>
            </div>
        </div>
    </section>
    <style>
        .datepicker{z-index:500 !important;}
    </style>
@endsection
@push('scripts')
    <script type="text/javascript" src='{{asset('js/seasons_rooms.js')}}'></script>
    <script type="text/javascript" src='{{asset('js/hide_elements.js')}}'></script>
@endpush