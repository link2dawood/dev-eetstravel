{{-- 
    Quotation Create Page - Tabler Design
    Modern UI with Tabler components
    Features: Page header, card layout, responsive table, form inputs
--}}
@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Create Quotation')
@section('content')

<div class="container-xl">
    {{-- Page Header --}}
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col">
                {{-- Page pre-title --}}
                <div class="page-pretitle">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('/home') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('tour.show', ['tour' => $tour->id]) }}">{{ $tour->name }}</a></li>
                            <li class="breadcrumb-item active">Create Quotation</li>
                        </ol>
                    </nav>
                </div>
                <h2 class="page-title">
                    <i class="ti ti-calculator me-2"></i>Create Quotation
                </h2>
            </div>
            {{-- Page title actions --}}
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="javascript:history.back()" class="btn btn-ghost-secondary">
                        <i class="ti ti-arrow-left me-1"></i>{{trans('main.Back')}}
                    </a>
                    <button type="button" class="btn btn-success saved">
                        <i class="ti ti-device-floppy me-1"></i>{{trans('main.Save')}}
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content Card --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Quotation Details</h3>
            <div class="card-actions">
                <a href="#" class="namesToggle hideTitle btn btn-sm btn-outline-secondary">
                    <i class="ti ti-eye me-1"></i>{{trans('main.Showtitles')}}
                </a>
            </div>
        </div>
        <div class="card-body" id="quotation_body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Quotation Name <span class="text-danger">*</span></label>
                    <input class="form-control" type="text" placeholder="Enter quotation name" id="quotation_name">
                </div>
                <div class="col-md-8">
                    <div class="hide validate-name">
                        <span class="text-danger"><i class="ti ti-alert-circle me-1"></i>{{trans('main.Nameisrequiredfield')}}</span>
                    </div>
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
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive" id="quotation_table">
                            <table class="table card-table table-vcenter table-nowrap">
                                <thead>
                                    <tr>
                                        <th>{{trans('main.Date')}}</th>
                                        <th>{{trans('main.City')}}</th>
                                        <th>{{trans('main.Hotel')}}</th>
									<th
                                                        data-container="body" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                        data-bs-title="Single Suppl."
                                                >
                                                    SS
                                                </th>
									<th data-column="Hotel P.P" data-container="body" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                        data-bs-title="Hotel P.P"
                                                >
                                                    HPP
                                                </th>
                                    <th data-column="lunchName"
                                        data-container="body" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                        data-bs-title="Lunch Name"
                                    >
                                        L.Name</th>
                                    <th
                                            data-container="body" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                            data-bs-title="Lunch"
                                    >Lun</th>
                                    <th data-column="dinnerName"
                                        data-container="body" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                        data-bs-title="Dinner Name"
                                    >D.Name</th>
                                    <th
                                            data-container="body" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                            data-bs-title="Dinner"
                                    >Din</th>
                                    <th>Entr</th>
                                    <th
                                            data-container="body" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                            data-bs-title="Comments"
                                    >Com</th>
                                    <th
                                            data-container="body" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                            data-bs-title="Local G\D"
                                    >LGD</th>
                                    <th>BUS</th>
                                    <th
                                            data-container="body" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                            data-bs-title="Group Cost"
                                    >GC</th>
                                    <th
                                            data-container="body" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                            data-bs-title="Driver">Dri</th>
                                    <th
                                            data-container="body" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                            data-bs-title="Porterage"
                                    >Por</th>
                                    </tr>
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
        </div>
        <div class="card-footer text-end">
            <button type="button" class="btn btn-success saved">
                <i class="ti ti-device-floppy me-1"></i>{{trans('main.Save')}}
            </button>
        </div>
    </div>
</div>

@stop

@section('post_scripts')
    <script>var calculationArray = {};</script>
    <script type="text/javascript" src='{{asset('js/quotation.js')}}'></script>
@stop