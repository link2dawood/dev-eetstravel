@extends('scaffold-interface.layouts.app')
@section('title', 'Quotation')
@section('content')

    <section class="content">
        <div class="box box-primary">
            <div class="box box-body" id="quotation_body" style="border-top: none">
                <div style="margin-bottom: 10px;">
                    <a href="javascript:history.back()">
                        <button class='btn btn-primary'>{{trans('main.Back')}}</button>
                    </a>
                    <button type="button" class="btn btn-success saved">{{trans('main.Save')}}</button>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <input class="form-control" type="text" placeholder="Name" id="quotation_name">
                    </div>
                    <div class="col-md-2 text-red hide validate-name">
                        <span style="line-height: 30px;">{{trans('main.Nameisrequiredfield')}}</span>
                    </div>
                    <div class="col-md-1 pull-right">
                        <a href="#" class="namesToggle hideTitle">{{trans('main.Showtitles')}}</a>
                    </div>
                </div>

                <script>
                    let tourId = {{$tour->id}};
                    $(document).on('blur', 'input', function () {
//                        $(this).hide();
                        let data_row = $(this).attr('data_row');
                        let data_column = $(this).attr('data_column');

                    });
                </script>
                {{csrf_field()}}
                <br>
                <div class="row">
                    <div class="col-md-12">
                        <div id="quotation_table" style="overflow-x: scroll;">
                            <table class="table table-bordered">
                                <thead>
                                    <th>{{trans('main.Date')}}</th>
                                    <th>{{trans('main.City')}}</th>
                                    <th>{{trans('main.Hotel')}}</th>
									<th
                                                        data-container="body" data-toggle="tooltip" data-placement="bottom"
                                                        data-original-title="Single Suppl."
                                                >
                                                    SS
                                                </th>
									<th data-column="Hotel P.P" data-container="body" data-toggle="tooltip" data-placement="bottom"
                                                        data-original-title="Hotel P.P"
                                                >
                                                    HPP
                                                </th>
                                    <th data-column="lunchName"
                                        data-container="body" data-toggle="tooltip" data-placement="bottom"
                                        data-original-title="Lunch Name"
                                    >
                                        L.Name</th>
                                    <th
                                            data-container="body" data-toggle="tooltip" data-placement="bottom"
                                            data-original-title="Lunch"
                                    >Lun</th>
                                    <th data-column="dinnerName"
                                        data-container="body" data-toggle="tooltip" data-placement="bottom"
                                        data-original-title="Dinner Name"
                                    >D.Name</th>
                                    <th
                                            data-container="body" data-toggle="tooltip" data-placement="bottom"
                                            data-original-title="Dinner"
                                    >Din</th>
                                    <th>Entr</th>
                                    <th
                                            data-container="body" data-toggle="tooltip" data-placement="bottom"
                                            data-original-title="Comments"
                                    >Com</th>
                                    <th
                                            data-container="body" data-toggle="tooltip" data-placement="bottom"
                                            data-original-title="Local G\D"
                                    >LGD</th>
                                    <th>BUS</th>
                                    <th
                                            data-container="body" data-toggle="tooltip" data-placement="bottom"
                                            data-original-title="Group Cost"
                                    >GC</th>
                                    <th
                                            data-container="body" data-toggle="tooltip" data-placement="bottom"
                                            data-original-title="Driver">Dri</th>
                                    <th
                                            data-container="body" data-toggle="tooltip" data-placement="bottom"
                                            data-original-title="Porterage"
                                    >Por</th>

                                </thead>
								@if(empty($quotation))
                                <tbody id="quotation_table">
                                @php
                                    $sortedTourDays = $tour->tour_days->sortBy(function($tourDay){
                                      return $tourDay->date;
                                    });
                                @endphp
                                @foreach ($sortedTourDays as $row => $tour_day)
                                    <tr class="quotation-row" data-row="{{$row}}">
                                        <td data-column="date">
                                            {{$tour_day->date}}
                                        </td>
                                        <td data-column="cityName">
                                            @if($tour_day->firstHotel() && $tour_day->firstHotel()->service()->cityObject)
                                                {{$tour_day->firstHotel()->service()->cityObject->name}}
                                            @endif
                                        </td>
                                        <td
                                                data-column="hotelName"
                                                data-value="@if($tour_day->firstHotel())
                                                {{$tour_day->firstHotel()->name}}
                                                @endif"
                                        >
                                            @if($tour_day->firstHotel())
                                                {{$tour_day->firstHotel()->name}}
                                            @endif
											<input type="text">
                                        </td>
                                        @foreach ($listRoomsHotel as $room)
												@if ($room->room_types->code == 'SIN')
                                            <td
                                                    class="hotelRooms"
                                                    data-column="{{$room->room_types->code}}"
                                                    data-value="@if($tour_day->firstHotel())
                                                    {{$tour_day->firstHotel()->getRoomTypePrice($room->room_type_id, true)}}
                                                    @endif"
                                            >
												
                                                @if($tour_day->firstHotel())
                                                    @if ($room->room_types->code == 'SIN')
                                                        {{$tour_day->firstHotel()->getSinglePrice()}}
                                                    @else
                                                    {{$tour_day->firstHotel()->getRoomTypePrice($room->room_type_id, true)}}
                                                    @endif
                                                @endif
												<input type="text">
                                            </td>
										@endif
                                        @endforeach
										<td data-column="htlpp">

											<input type="text">
                                        </td>
                                        <td data-column="lunchName">
                                            @if($tour_day->firstRestaurant())
                                                {{$tour_day->firstRestaurant()->name}}
                                            @endif
											<input type="text">
                                        </td>
                                        <td data-column="lunch">
                                            @if($tour_day->firstRestaurant())
                                                {{$tour_day->firstRestaurant()->total_amount}}
                                            @endif
											<input type="text">
                                        </td>
                                        <td data-column="dinnerName">
                                            @if($tour_day->secondRestaurant())
                                                {{$tour_day->secondRestaurant()->name}}
                                            @endif
											<input type="text">
                                        </td>
                                        <td data-column="dinner">
                                            @if($tour_day->secondRestaurant())
                                                {{$tour_day->secondRestaurant()->total_amount}}
                                            @endif
											<input type="text">
                                        </td>
                                        <td data-column="entrance">
                                            <!-- Entrance -->
											<input type="text">
                                        </td>
                                        <td data-column="comments">
                                            <!-- Comments -->
											<input type="text">
                                        </td>
                                        <td data-column="local_g_d">
                                            <!-- LocalG/d -->
											<input type="text">
                                        </td>
                                        <td data-column="bus">
                                            <!-- BUS -->
											<input type="text">
                                        </td>
                                        <td data-column="group_cost">
                                            <!-- Group Cost -->
											<input type="text">
                                        </td>
                                        <td data-column="driver">
                                            <!-- Driver -->
											<input type="text">
                                        </td>
                                        <td data-column="porterage">
                                            <!-- Porterage -->
											<input type="text">
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
								@else
								<tbody id="quotation_table">
                                    @php
										$sortedTourDays = $tour->tour_days->sortBy(function($tourDay){
										  return $tourDay->date;
										});
                                        $sortedQuotationRows = $quotation->rows->sortBy(function($quotationRow){
                                          return $quotationRow->getValueByKey('date')->value??"";
                                        });
                                        $data_row = -1;
										$total_days = count($sortedTourDays);
										$rows_count = count($sortedQuotationRows);
										$counter = 0;
                                    @endphp
									@php $columns = array("date","cityName","hotelName","SIN","htlpp","lunchName","lunch","dinnerName","dinner","entrance","comments","local_g_d","bus","group_cost","driver","porterage"); 
												
												
												$insertIndex = array_search("hotelName", $columns);
												// Reverse the $listRoomsHotel collection
												/*
												$listRoomsHotel = $listRoomsHotel->reverse();

												foreach ($listRoomsHotel as $room) {
													array_splice($columns, $insertIndex + 1, 0, $room->room_types->code);
												}
												$listRoomsHotel = $listRoomsHotel->reverse();
												*/
												@endphp
                                    @foreach ($sortedQuotationRows as $key => $row)
                                        @php
                                        $data_row = $data_row + 1;
										 if( $total_days == $data_row){
                                            break;
                                        }
                                        @endphp
                                        <tr class="quotation-row" data-row= {{$data_row}} >
                                            
                                            @foreach ($columns as $column)
														@php
															$found = false; // Initialize a flag to check if the column is found
														@endphp

														@foreach ($row->values as $value)
															@if ($value->key === $column)
																@php
																	$found = true; // Set the flag to true when a matching column is found
																@endphp

																@if (!empty($value->value) && in_array($value->key, $columns))
																	<td data-column="{{$value->key}}">
																		{{$value->value}}
																	</td>
																	@elseif (in_array($value->key, $columns))
																	<td data-column="{{$value->key}}">
																		<input type="text" value="{{$value->value}}">
																	</td>
																	@endif
																@break // Exit the inner loop as we've found the column
															@endif
														@endforeach

														{{-- If the column is not found, display an empty cell --}}
														@if (!$found)
															<td data-column="{{$column}}">
																{{-- Handle other cases here --}}
															</td>
														@endif
													@endforeach

												   @foreach($quotation->additional_columns as $column)
                                                                <td
                                                                        data-column="{{$column->name}}"
                                                                        class="additional-cell
                                                                        @if($column->type == 'all')
                                                                                quotation-cell-general
                                                                        @endif
                                                                        @if($column->type == 'person')
                                                                                quotation-cell-per-person
                                                                        @endif
                                                                        ">
                                                                    {{--{{dump($row->id, $column->name, @$quotation->getAdditionalColumnValueCell($row->id, $column->name)->value)}}--}}
                                                                    {{@$quotation->getAdditionalColumnValueCell($row->id, $column->name)->value}}
                                                                </td>
                                                            @endforeach                                        </tr>
                                    @endforeach
									@foreach ($sortedTourDays as $row => $tour_day)
                                        @php
                                        $counter++;
                                        @endphp
                                    @if($counter > $rows_count)
                                            <tr class="quotation-row" data-row="{{$row}}">
                                                <td data-column="date">
                                                    {{$tour_day->date}}
                                                </td>
                                                <td data-column="cityName">
                                                    @if($tour_day->firstHotel() && $tour_day->firstHotel()->service()->cityObject)
                                                        {{$tour_day->firstHotel()->service()->cityObject->name}}
                                                    @endif
                                                </td>
                                                <td
                                                        data-column="hotelName"
                                                        data-value="@if($tour_day->firstHotel())
                                                        {{$tour_day->firstHotel()->name}}
                                                        @endif"
                                                >
                                                    @if($tour_day->firstHotel())
                                                        {{$tour_day->firstHotel()->name}}
                                                    @endif
                                                </td>
                                                @foreach ($listRoomsHotel as $room)

                                                    <td
                                                            class="hotelRooms"
                                                            data-column="{{$room->room_types->code}}"
                                                            data-value="@if($tour_day->firstHotel())
                                                            {{$tour_day->firstHotel()->getRoomTypePrice($room->room_type_id, true)}}
                                                            @endif"
                                                    >
                                                        @if($tour_day->firstHotel())
                                                            @if ($room->room_types->code == 'SIN')
                                                                {{$tour_day->firstHotel()->getSinglePrice()}}
                                                            @else
                                                            {{$tour_day->firstHotel()->getRoomTypePrice($room->room_type_id, true)}}
                                                            @endif
                                                        @endif
                                                    <input type="text">
                                                    </td>
                                                @endforeach
                                                <td data-column="lunchName">
                                                    @if($tour_day->firstRestaurant())
                                                        {{$tour_day->firstRestaurant()->name}}
                                                    @endif
                                                    <input type="text">
                                                </td>
                                                <td data-column="lunch">
                                                    @if($tour_day->firstRestaurant())
                                                        {{$tour_day->firstRestaurant()->total_amount}}
                                                    @endif
                                                    <input type="text">
                                                </td>
                                                <td data-column="dinnerName">
                                                    @if($tour_day->secondRestaurant())
                                                        {{$tour_day->secondRestaurant()->name}}
                                                    @endif
                                                    <input type="text">
                                                </td>
                                                <td data-column="dinner">
                                                    @if($tour_day->secondRestaurant())
                                                        {{$tour_day->secondRestaurant()->total_amount}}
                                                    @endif
                                                    <input type="text">
                                                </td>
                                                <td data-column="entrance">
                                                    <!-- Entrance -->
                                                </td>
                                                <td data-column="comments">
                                                    <!-- Comments -->
                                                </td>
                                                <td data-column="local_g_d">
                                                    <!-- LocalG/d -->
                                                </td>
                                                <td data-column="bus">
                                                    <!-- BUS -->
                                                </td>
                                                <td data-column="group_cost">
                                                    <!-- Group Cost -->
                                                </td>
                                                <td data-column="driver">
                                                    <!-- Driver -->
                                                </td>
                                                <td data-column="porterage">
                                                    <!-- Porterage -->
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="button" class="btn btn-success saved" >{{trans('main.Save')}}</button>
                </div>
            </div>
        </div>
    </section>

@stop

@section('post_scripts')
    <script>var calculationArray = {};</script>
    <script type="text/javascript" src='{{asset('js/quotation.js')}}'></script>
@stop