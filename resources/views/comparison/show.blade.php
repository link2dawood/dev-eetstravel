@extends('scaffold-interface.layouts.app')
@section('title','Show')
@section('content')
    @php
        $tour = $quotation->tour;
    @endphp

    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <h2 class="page-header">
                    <i class="fa fa-list" aria-hidden="true"></i> Front Sheet [{{$quotation->name}} - {{$tour->name}}]
                </h2>
                <span id="help" class="btn btn-box-tool pull-right"><i class="fa fa-question-circle" aria-hidden="true"></i>
                    @include('legend.frontsheet_legend')
                    </span>
                <form action="{{route('comparison.update', ['comparison' => $quotation->id])}}" method="POST">
                <div style="margin-bottom: 10px;">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box-header with-border">
                                <a  class='btn btn-primary'
                                    href="javascript:history.back()">
                                   {!!trans('main.Back')!!}
                                </a>
                                <button class='btn btn-success' type='submit'>{!!trans('main.Save')!!}</button>
                            </div>
                        </div>
                    </div>

                </div>

                    {{csrf_field()}}
                    {{ method_field('PUT') }}
                <div class="row">
                    <div class="col-md-6">
                        <p class="lead">
                            Rooms:
                            @php
                                $peopleCount = 0;
                            @endphp

                            @foreach ($listRoomsHotel as $room)
                                @php
                                    $peopleCount += isset( App\TourPackage::$roomsPeopleCount[$room->room_types->code])
                                    ? App\TourPackage::$roomsPeopleCount[$room->room_types->code] * $room->count : 0;
                                @endphp
                                {{$room->room_types->code}} : {{$room->count}}
                            @endforeach
                        @if($peopleCount != $tour->pax + $tour->pax_free)
                            <div class="alert alert-warning alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert"
                                        aria-hidden="true">×
                                </button>
                                <i class="icon fa fa-warning"></i>
                                {!!trans('main.PaxCount')!!} ({{$tour->pax + $tour->pax_free}}) is not equal to the number of people in the rooms ({{$peopleCount}})
                            </div>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="lead">
                            Pax:
                            {{$tour->pax}} {{$tour->pax_free}}
                        </p>
                    </div>
                </div>

                    <div class="col-md-12" style="overflow: auto">
                        <table class="table table-bordered finder-disable">
                            <thead>
                            <td>{!!trans('main.Date')!!}</td>
                            <td>{!!trans('main.City')!!}</td>
                            <td>{!!trans('main.QuoteHotel')!!}</td>
                            @foreach ($listRoomsHotel as $room)
                                <td
                                        @if ($room->room_types->code == 'SIN')
                                        data-container="body" data-toggle="tooltip" data-placement="bottom"
                                        data-original-title="Single suppl."
                                        @endif
                                >Quote {{$room->room_types->code}}</td>
                            @endforeach
                            <td>
                                CMFD HOTEL
                            </td>
                            @foreach ($listRoomsHotel as $room)
                                <td
                                        @if ($room->room_types->code == 'SIN')
                                        data-container="body" data-toggle="tooltip" data-placement="bottom"
                                        data-original-title="Single suppl."
                                        @endif
                                >CMFD {{$room->room_types->code}}</td>
                            @endforeach
                            <td>{!!trans('main.CityTax')!!}</td>
                            <td>{!!trans('main.Option')!!}</td>
                            <td>&reg;</td>
                            <td>
                                VC sent <br>to SHA
                            </td>
                            <td></td>
                            <td>{!!trans('main.Budget')!!}</td>
                            </thead>
                            <tbody>
                            @php
                                $overallSum =  0;
                            @endphp
                            @foreach($tour->getTourDaysSortedByDate() as $tourDay)

                                @php
                                    $quotationBudget = 0;
                                    $realBudget = 0;
									$first_hotel = $tourDay->firstHotel();
                                @endphp
                                <tr>

                                    <td>{{$tourDay->date}}</td>
                                    <td>
									@if(!is_null($first_hotel ) && method_exists($first_hotel , 'service') && isset($first_hotel->service()->cityObject))
                                        {{ $first_hotel->service()->cityObject->name }}
                                        @endif
                                    </td>
                                    <td>
                                        {{($quotation->getValueByDate($tourDay->date, 'hotelName'))}}
                                    </td>
                                    @foreach ($listRoomsHotel as $room)
                                        @if($tourDay->firstHotel())
                                        @php

  $roomPrice = 0;


                                          if ($room->room_types->code == 'SIN') {
                                                $roomPrice = (int)$quotation->getValueByDate($tourDay->date, $room->room_types->code) + (int)$quotation->getValueByDate($tourDay->date, 'htlpp') ;
								
                                            } else {
									
                                                $roomPrice = (int)$quotation->getValueByDate($tourDay->date, "htlpp") ?? 0;
                                            }
                                            $roomCount = (int)$tourDay->firstHotel()->getRoomTypeCount($room->room_types->id) ?? 0;
                                            $peopleCount = 1;
                                            if ($room->room_types->code == 'DOU') {
                                                $peopleCount = 2;
                                            }
                                            if ($room->room_types->code == 'TRI') {
                                                $peopleCount = 3;
                                            }
                                            if ($room->room_types->code == 'TWN') {
                                                $peopleCount = 2;
                                            }
                                            $quotationBudget += $roomPrice * $roomCount * $peopleCount;
                                        @endphp
                                        <td data-container="body" data-toggle="tooltip" data-placement="bottom"
                                            data-original-title="({{$roomPrice}} * {{$roomCount}} * {{$peopleCount}}) = {{ $roomPrice * $roomCount * $peopleCount}}">

                                            {{$roomPrice}}
                                        </td>
                                       @else
                                            <td></td>

                                            {{--<td>{{$tourDay->firstHotel()->room_types_hotel->find($room->room_type_id)->price}}</td>--}}
                                            {{--<td>{{$tourDay->firstHotel()->getRoomTypePrice($room->room_type_id)}}</td>--}}
                                        @endif
                                    @endforeach

                                    <td>
                                        @if($tourDay->firstHotel())
                                            {{$tourDay->firstHotel()->name}}
                                        @endif

                                    </td>
                                    @foreach ($listRoomsHotel as $room)
                                        @if($tourDay->firstHotel())
                                            @php
                                             $roomPrice = $tourDay->firstHotel()->getRoomTypePrice($room->room_type_id);
                                             $roomCount = $tourDay->firstHotel()->getRoomTypeCount($room->room_types->id);
                                             $peopleCount = 1;
                                            if ($room->room_types->code == 'DOU') {
                                                $peopleCount = 2;
                                            }
                                            if ($room->room_types->code == 'TRI') {
                                                $peopleCount = 3;
                                            }
                                            if ($room->room_types->code == 'TWN') {
                                                $peopleCount = 2;
                                            }
                                            $realBudget += $roomPrice * $roomCount * $peopleCount;
                                            @endphp
                                            <td data-container="body" data-toggle="tooltip" data-placement="bottom"
                                                data-original-title="({{$roomPrice}} * {{$roomCount}} * {{$peopleCount}}) = {{ $roomPrice * $roomCount * $peopleCount}}">


                                                {{--<td>{{$tourDay->firstHotel()->room_types_hotel->find($room->room_type_id)->price}}</td>--}}
                                                {{$tourDay->firstHotel()->getRoomTypePrice($room->room_type_id)}}
                                            </td>
                                        @else
                                            <td></td>
                                        @endif
                                    @endforeach
                                    <td>
                                        @php
                                            if ($comparison->comparisonRowByDate($tourDay->date)->city_tax != 0) {

                                                $cityTax = $comparison->comparisonRowByDate($tourDay->date)->city_tax;
                                            } else {
                                                $cityTax = $tourDay->firstHotel() ? $tourDay->firstHotel()->city_tax : 0;
                                            }

                                        @endphp

                                        {{Form::input('text', 'city_tax['.$comparison->comparisonRowByDate($tourDay->date)->id.']', $cityTax, ['class' => 'form-control cityTax city_tax_select'])}}
                                    </td>
                                    <td>
                                        @if($tourDay->firstHotel())
                                            {{$tourDay->firstHotel()->getStatusName()}}
                                        @endif
                                    </td>
                                    <td>{{Form::checkbox('rooming_list_reserved[]', $comparison->comparisonRowByDate($tourDay->date)->id, $comparison->comparisonRowByDate($tourDay->date)->rooming_list_reserved, ['class' => 'rooming_list_reserved'])}}</td>
                                    <td>{{Form::checkbox('visa_confirmation[]', $comparison->comparisonRowByDate($tourDay->date)->id, $comparison->comparisonRowByDate($tourDay->date)->visa_confirmation, ['class' => 'visa_confirmation'])}}</td>
                                    <td>
                                        <a class="btn btn-block comments-button" data-row-id="{{$comparison->comparisonRowByDate($tourDay->date)->id}}" data-link="{{route('comparison.comments', ['id' => $comparison->comparisonRowByDate($tourDay->date)->id])}}/">
                                            <span class="badge bg-yellow">{{\App\Helper\AdminHelper::getComparisonRowCommentsCount($comparison->comparisonRowByDate($tourDay->date)->id)}}</span>
                                            <i class="fa fa-comment-o" aria-hidden="true"></i>
                                        </a>
                                    </td>
                                    <td data-toggle="tooltip" data-placement="top"
                                        title="({{$quotationBudget}} - ({{$realBudget}} + {{$cityTax}})) / {{$tour->pax}}">
                                        @php
                                            if ($tour->pax != 0) {
                                                $sum =($quotationBudget - ($realBudget + $cityTax)) / $tour->pax;
                                            }
                                            else {
                                                $sum = 0;
                                            }
                                            $overallSum += $sum;
                                        @endphp
                                        {{round($sum, 2)}}
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="{{8+count($listRoomsHotel)*2}}">{!!trans('main.ENDOFSERVICE')!!}</td>
                                <td >&#931; =</td>
                                <td>{{round($overallSum, 2)}}</td>
                            </tr>

                            <!--  Bottom  -->
                            </tbody>
                        </table>
                    </div>

                        <table class="table table-bordered">
                            <tr>
                                <td width="200px">{!!trans('main.GoAheadDate')!!}</td>
                                <td>{{$tour->ga}}</td>
                            </tr>
                            <tr>
                                <td>{!!trans('main.HotelListSent')!!}</td>
                                <td>
                                    <div class="form-group">
                                        <div class='input-group date'  style="width:150px">
                                            <input type='text'name="hotel_list_sent" class="form-control datepicker" value="{{$comparison->hotel_list_sent}}"/>
                                            <span class="input-group-addon">
                                              <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>{!!trans('main.Roominglistreceived')!!}</td>
                                <td>
                                    <div class="form-group">
                                        <div class='input-group date' style="width:150px">
                                            <input type='text' class="form-control datepicker" name="rooming_list_received" value="{{$comparison->rooming_list_received}}"/>
                                            <span class="input-group-addon">
                                              <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>{!!trans('main.Visaconfirmationsent')!!}</td>
                                <td>
                                    <div class="form-group">
                                        <div class='input-group date'  style="width:150px">
                                            <input type='text' class="form-control datepicker" name="visa_confirmation_sent" value="{{$comparison->visa_confirmation_sent}}" }}"/>
                                            <span class="input-group-addon">
                                              <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td><h5>{!!trans('main.Finaldocumentssent')!!}</h5></td>
                                <td>
                                    <div class="form-group">
                                        <div class='input-group date' style="width:150px">
                                            <input type='text' class="form-control datepicker" name="final_documents_sent" value="{{$comparison->final_documents_sent}}"/>
                                            <span class="input-group-addon">
                                              <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>{!!trans('main.IMPORTANTCOMMENTSEMAILS')!!}:</td>
                                <td>
                                    <div class="form-group">
                                        <textarea name="comments" rows="10" class="col-xs-12 form-control" style="min-width: 100%">{{$comparison->comments}}</textarea>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <button type="submit" class="btn btn-success">{!!trans('main.Save')!!}</button>
                        <a href="{{route('tour.show', ['tour' => $tour->id])}}">
                            <button class='btn btn-warning' type='button'>{!!trans('main.Cancel')!!}</button>
                        </a>
                        </form>
                    </div>
            </div>
        <div class="col-md-8">
        {{--Popup create--}}
        <div id="commentModal" class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="commentModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <a class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">x</span>
                        </a>
                        <h4 id="modalCreateBusLabel" class="modal-title"></h4>
                    </div>

                    </div>

                    <div class="modal-body">
                        <div class="modal-body">
                            <form id="comments">


                                    </form>
                                </div>
                            </div>


                    <div class="modal-footer">
                        <a href="close" class='btn btn-default' data-dismiss="modal">{!!trans('main.Close')!!}</a>
                        <button class='btn btn-primary' id="update_btn_table_bus" type='button'>{!!trans('main.Save')!!}</button>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>
    @stop
@section('post_scripts')
    <script>
        let comments = {
            init : function() {
                comments.bind();
            },
            bind: function() {
                $('.comments-button').click(function(){

                });
            }
        };
        let comparison = {
            init : function() {
                comparison.bind();
            },
            bind : function() {
                $('.visa_confirmation').on('click', function(){
                    if (comparison.isAllVisasConfirmed()) {

                        if ($('input[name=visa_confirmation_sent]').val() == ''){
                            today = comparison.today();
                            $('input[name=visa_confirmation_sent]').val(today)
                        }

                    }
                });
                $('.rooming_list_reserved').on('click', function(){
                    if (comparison.isAllRoomingListReserved()) {
                        if ($('input[name=rooming_list_received]').val() == ''){
                            today = comparison.today();
                            $('input[name=rooming_list_received]').val(today)
                        }
                    }
                });

                $('.city_tax_select').on('focus', function (e) {
                    $(this).select();
                });

                $('input[name=rooming_list_received]').change(function(){
                    if ($(this).val() != '') {
                        comparison.checkAllRoomingListReserved();
                    }
                });
                $('input[name=visa_confirmation_sent]').change(function(){
                    if ($(this).val() != '') {
                        comparison.checkAllVisasConfirmed();
                    }
                });

                $(document).on('click', '.comments-button', function() {
                    $.ajax({
                        async: true,
                        type: 'get',
                        url: $(this).data('link'),
                        data : {
                            reply_message: $(this).data('reply-message'),
                            reply_folder: $(this).data('reply-folder'),
                            reply_to: $(this).data('to')
                        },
                        success: function(response) {
                            $('#myModal').html(response);
                            $('#myModal').modal();
                        }
                    })

                });
            },
            today : function () {
                var today = new Date();
                var dd = today.getDate();
                var mm = today.getMonth()+1; //January is 0!
                var yyyy = today.getFullYear();
                if(dd<10) {
                    dd = '0'+dd
                }

                if(mm<10) {
                    mm = '0'+mm
                }

                return  yyyy + '-' + mm + '-' + dd;
            },
            isAllRoomingListReserved : function (){
                return $('.rooming_list_reserved:not(:checked)').length == 0;
            },
            isAllVisasConfirmed : function (){
                return $('.visa_confirmation:not(:checked)').length == 0;
            },
            checkAllRoomingListReserved : function (){
                return $('.rooming_list_reserved:not(:checked)').prop('checked', true);
            },
            checkAllVisasConfirmed : function (){
                return $('.visa_confirmation:not(:checked)').prop('checked', true);
            },

        };
        $(document).ready(function(){
//            $('input[type=checkbox]').iCheck({
//                checkboxClass: 'icheckbox_minimal-blue',
//                radioClass   : 'iradio_minimal-blue'
//            });
            comparison.init();



        });
    </script>
    <script src="/js/utils.js"></script>
@stop