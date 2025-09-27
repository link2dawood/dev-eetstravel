@extends('scaffold-interface.layouts.app')
@section('title', 'Quotation')
@section('content')

    <section class="content">
        <div class="box box-primary">

            <div class="box-body">
                <div style="margin-bottom: 10px;">
                    <a href="javascript:history.back()">
                        <button class='btn btn-primary'>{{trans('main.Back')}}</button>
                    </a>
					<button type="button" class="btn btn-success calculate-quotation" >Calculate</button>
                    <button type="button" class="btn btn-success update-quotation">{{trans('main.Save')}}</button>
					  @if($quotation->is_confirm == 0)
                    <button type="button" class="btn btn-success confirm-button" style="float:right" >Confirm</button>
                    @else
                    <button type="button" class="btn btn-danger confirm_cancel-button" style="float:right" >Cancel</button>
                    @endif
               
                    <input type="hidden" value = "{{$quotation->id}}" id = "quotation_id">
                    <span id="help" class="btn btn-box-tool pull-right"><i class="fa fa-question-circle" aria-hidden="true"></i>
                        @include('legend.quotation_legend_edit')
                    </span>
                </div>


                <div class="nav-tabs-custom" style="overflow: hidden">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#main" data-toggle="tab">{{trans('main.Main')}}</a></li>
                        @if(Auth::user()->can('quotation.calculation'))
                            <li><a href="#calculation" data-toggle="tab">{{trans('main.Calculation')}}</a></li>
                        @endif
                    </ul>
                    <div class="tab-content" style="overflow: hidden">
                        <div class="tab-pane active" id="main">
                            <div class="col-md-12">
                                <br>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <input class="form-control" type="text" placeholder="Name"
                                                   id="quotation_name" value="{{$quotation->name}}">
                                        </div>
                                    </div>
                                    <div class="col-md-2 text-red hide validate-name">
                                        <span style="line-height: 30px;">{{trans('main.Nameisrequiredfield')}}</span>
                                    </div>
                                    <div class="col-md-1 pull-right">
                                        <a href="#" class="namesToggle hideTitle">{{trans('main.Showtitles')}}</a>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div id="quotation_table" style="overflow-x: scroll;">
                                            <button type="button"
                                                    data-link="{{route('quotation.add_column_message')}}"
                                                    class="btn btn-block btn-success btn-xs pull-right quotation-add-column"
                                                    style="width:100px">
                                                {{trans('main.Addcolumn')}}
                                            </button>
                                            <table class="table table-bordered">
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
                                                <thead>
                                                <th>{{trans('main.Date')}}</th>
                                                <th>{{trans('main.City')}}</th>
                                                <th>{{trans('main.Hotel')}}</th>
												<th data-container="body" data-toggle="tooltip" data-placement="bottom"
                                                        data-original-title="Single Suppl."
                                                        >
                                                            SS
                                                        </th>
													<th data-column="htlpp" data-container="body" data-toggle="tooltip" data-placement="bottom"
														data-original-title="Hotel P.P" >
														HPP
													</th>
                                                <th data-column="lunchName"
                                                    data-container="body" data-toggle="tooltip" data-placement="bottom"
                                                    data-original-title="Lunch Name"
                                                >L.Name</th>
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
                                                @foreach($quotation->additional_columns as $column)
                                                    <th
                                                            data-column="{{$column->name}}"
                                                            data-type="{{$column->type}}"
                                                            class="additional-column"
                                                    >{{$column->name}}  <i class="fa fa-times pull-right remove-quotation-column"></i></th>
                                                @endforeach
                                                </thead>
                                                <tbody id="quotation_table">
                                                @php
                                                    $sortedQuotationRows = $quotation->rows->sortBy(function($quotationRow){
                                                      return $quotationRow->getValueByKey('date')->value??"";
                                                    });
													
                                                @endphp
                                               @foreach ($sortedQuotationRows as $key => $row)
												<tr class="quotation-row" data-row="{{$row->id}}">
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
                                                            @endforeach
															</tr>
														@endforeach


                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <br/>
                                <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="rate">{{trans('main.Rate')}} :</label>
                                        <input type="text" id="rate" name="rate" class="form-control"
                                               value="{{$quotation->rate != "" ? $quotation->rate :  '1.00'}}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="mark_up">{{trans('main.MarkUp')}} :</label>
                                        <input type="text" id="mark_up" name="mark_up" class="form-control"
                                               value="{{$quotation->mark_up != "" ? $quotation->mark_up :  '0'}}">
                                    </div>
                                </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="note">{{trans('main.Note')}} :</label>
                                            <textarea name="note" style="resize: vertical"  id="note" cols="30" rows="2"
                                                      class="form-control">{{$quotation->note}}</textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="note_show">{{trans('main.NoteShowinPDF')}} :</label>
                                            {{Form::checkbox('note_show', 1, $quotation->note_show, ['id' => 'note_show'] )}}
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                <div class="col-md-12" style="overflow-x: scroll;">
                                    <table id="summary" class="table table-bordered finder-disable">
                                        <tbody>
                                        <tr data-row="person">
                                        </tr>
                                        <tr data-row="netto_euro">
                                        </tr>
                                        <tr data-row="mark_up">
                                        </tr>
                                        <tr data-row="brutto">
                                        </tr>
                                        <tr data-row="active">
                                        </tr>

                                        </tbody>
                                    </table>
                                    <table id="summary_all" class="table table-bordered finder-disable">
                                        <tbody>
                                        <tr data-row="person">
                                        </tr>
                                        <tr data-row="netto_euro">
                                        </tr>
                                        <tr data-row="mark_up">
                                        </tr>
                                        <tr data-row="brutto">
                                        </tr>
                                        <tr data-row="active">
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                </div>
                                <br/>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="repeater col-md-12 col-lg-12">
                                            <h5>{{trans('main.AdditionalConfigures')}}:</h5>
                                            <div data-repeater-list="package_menu">
                                                {{--EMPTY ITEM FOR ADD--}}
                                                <div class="row" data-repeater-item>
                                                    <div class="col-md-3">
                                                        <div class="form-group  package-menu-item">
                                                            {!! Form::label('person', 'Person') !!}
                                                            {!! Form::input('string', 'person', 0 ,['class' => 'form-control']) !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group package-menu-item">
                                                            {!! Form::label('price', 'Price') !!}
                                                            {!! Form::input('string', 'price', 0 ,['class' => 'form-control']) !!}

                                                        </div>
                                                    </div>
                                                    <div class="col-md-1">
                                                        <div class="form-group">
                                                            {{Form::label('Active', 'Active', ['style' => 'text-align: center;display: block;'])}}
                                                            {!! Form::checkbox('active', 1, @$additional->active, ['class' => 'active-person']) !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1">
                                                        <div class="form-group">
                                                            {{Form::label('')}}
                                                            <a href="#" data-repeater-delete  class="form-control btn btn-danger" name="remove" style="margin-top: 5px">
                                                                <i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                                        </div>
                                                    </div>
                                                </div>
                                                @foreach ($quotation->additional_persons as $additional)
                                                    <div class="row" data-repeater-item>
                                                        <div class="col-md-3">
                                                            <div class="form-group  package-menu-item">
                                                                {!! Form::label('person', 'Person') !!}
                                                                {!! Form::input('string', 'person', @$additional->person ,['class' => 'form-control']) !!}
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group package-menu-item">
                                                                {!! Form::label('price', 'Price') !!}
                                                                {!! Form::input('string', 'price', @$additional->price ,['class' => 'form-control']) !!}

                                                            </div>
                                                        </div>
                                                        <div class="col-md-1">
                                                            <div class="form-group">
                                                                {{Form::label('Active', 'Active', ['style' => 'text-align: center;display: block;'])}}
                                                                {!! Form::checkbox('active', 1, @$additional->active, ['class' => 'active-person']) !!}
                                                            </div>
                                                        </div>
                                                        <div class="col-md-1">
                                                            <div class="form-group">
                                                                {{Form::label('')}}
                                                                <a href="#" data-repeater-delete  class="form-control btn btn-danger" name="remove" style="margin-top: 5px">
                                                                    <i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                                            </div>
                                                        </div>
                                                    </div>

                                                @endforeach

                                            </div>
                                            <button data-repeater-create class="btn btn-success" type="button"><i class="fa fa-plus fa-md" aria-hidden="true"></i> {{trans('main.Add')}}</button>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="calculation">



                            <script>
                                let quotationId = {{$quotation->id}};
                                $(document).on('blur', 'input', function () {
//                        $(this).hide();
                                    let data_row = $(this).attr('data_row');
                                    let data_column = $(this).attr('data_column');

                                });
                            </script>
                            {{csrf_field()}}

                            <div class="row">

                            </div>

                            <div class="col-md-12" style="overflow-x: scroll;">
                                <table id="calculation" class="table table-bordered">
                                    <thead>
                                    <td></td>
                                    <td>{{trans('main.Hotel')}}</td>
                                    <td>{{trans('main.Lunch')}}</td>
                                    <td>{{trans('main.Dinner')}}</td>
                                    <td>{{trans('main.Entrance')}}</td>
                                    <td>{{trans('main.LocalGD')}}</td>
                                    <td>{{trans('main.DriverRoom')}}</td>
                                    <td>{{trans('main.SingleSupple')}}.</td>
										<td>{{trans('SingleSuppleforDFs')}}.</td>
                                    <td>{{trans('main.Bus')}}</td>
                                    <td>{{trans('main.Bus')}}</td>
                                    <td>{{trans('main.Porterage')}}</td>
                                    <td>{{trans('main.TotalMeals')}}</td>
                                    </thead>
                                    <tbody>
                                    <tr data-row="quotation_sum">
                                        <td></td>
                                        <td data-column="htlpp"></td>
                                        <td data-column="lunch"></td>
                                        <td data-column="dinner"></td>
                                        <td data-column="entrance"></td>
                                        <td data-column="local_g_d"></td>
                                        <td data-column="driver_room"></td>
                                        <td data-column='sgl_suppl'></td>
										<td data-column='dfs_suppl'></td>
                                        <td data-column="bus"></td>
                                        <td data-column="group_cost"></td>
                                        <td data-column="porterage"></td>
                                        <td data-column="total_meals"></td>
                                    </tr>
                                    <tr data-row="combined_sum">
                                        <td data-column="entrance_porterage" data-container="body" data-toggle="tooltip"
                                            data-placement="bottom"
                                            data-original-title="entrance + porterage"></td>
                                        <td data-column="htlpp" data-container="body" data-toggle="tooltip"
                                            data-placement="bottom"
                                            data-original-title="entrance + porterage + hotel"></td>
                                        <td data-column="lunch" data-container="body" data-toggle="tooltip"
                                            data-placement="bottom"
                                            data-original-title="lunch + dinner"></td>
                                        <td data-column="dinner" data-container="body" data-toggle="tooltip"
                                            data-placement="bottom"
                                            data-original-title="hotel + lunch"></td>
                                        <td data-column="entrance" data-container="body" data-toggle="tooltip"
                                            data-placement="bottom"
                                            data-original-title="lunch + dinner + entrance"></td>
                                        <td data-column="local_g_d"
                                            data-container="body" data-toggle="tooltip" data-placement="bottom"
                                            data-original-title="Local G/D + Driver + Bus"
                                        ></td>
                                        <td data-column="driver_room"></td>
                                        <td data-column='sgl_suppl'></td>
										<td data-column='dfs_suppl'></td>
                                        <td data-column="bus"></td>
                                        <td data-column="group_cost"></td>
                                        <td data-column="porterage"></td>
                                        <td data-column="total_meals"></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="col-md-12" style="overflow-x: scroll;">
                                <table id="netto_first" class="table table-bordered">
                                    <tbody>
                                    <tr data-row="free">

                                    </tr>
                                    <tr data-row="person">
                                    </tr>
                                    <tr data-row="meals">
                                    </tr>
                                    <tr data-row="package">
                                    </tr>
                                    <tr data-row="foc">
                                    </tr>
                                    <tr data-row="bus_g_d">
                                    </tr>
                                    <tr data-row="netto">
                                    </tr>
                                    <tr data-row="netto_euro">
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <br>
                            <div class="col-md-12" style="overflow-x: scroll;">
                                <table id="netto_second" class="table table-bordered">
                                    <tbody>
                                    <tr data-row="free">

                                    </tr>
                                    <tr data-row="person">
                                    </tr>
                                    <tr data-row="meals">
                                    </tr>
                                    <tr data-row="package">
                                    </tr>
                                    <tr data-row="foc">
                                    </tr>
                                    <tr data-row="bus_g_d">
                                    </tr>
                                    <tr data-row="netto">
                                    </tr>
                                    <tr data-row="netto_euro">
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <br>
                            <div class="col-md-12" style="overflow-x: scroll;">
                                <table id="netto_third" class="table table-bordered">
                                    <tbody>
                                    <tr data-row="free">

                                    </tr>
                                    <tr data-row="person">
                                    </tr>
                                    <tr data-row="meals">
                                    </tr>
                                    <tr data-row="package">
                                    </tr>
                                    <tr data-row="foc">
                                    </tr>
                                    <tr data-row="bus_g_d">
                                    </tr>
                                    <tr data-row="netto">
                                    </tr>
                                    <tr data-row="netto_euro">
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                            <br>
                            <div class="col-md-12" style="overflow-x: scroll;">
                                <table id="netto_fourth" class="table table-bordered">
                                    <tbody>
                                    <tr data-row="free">

                                    </tr>
                                    <tr data-row="person">
                                    </tr>
                                    <tr data-row="meals">
                                    </tr>
                                    <tr data-row="package">
                                    </tr>
                                    <tr data-row="foc">
                                    </tr>
                                    <tr data-row="bus_g_d">
                                    </tr>
                                    <tr data-row="netto">
                                    </tr>
                                    <tr data-row="netto_euro">
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <br>

                        </div>
                    </div>
                    <!-- /.tab-pane -->

                </div>
                <!-- /.tab-content -->
            </div>

            <div class="box-footer">
                <button type="button" class="btn btn-success update-quotation">{{trans('main.Save')}}</button>
                <a href="javascript:history.back()">
                    <button class='btn btn-warning' type='button'>{{trans('main.Cancel')}}</button>
                </a>
            </div>
        </div>
    </section>
@stop

@section('post_scripts')
    <script>
        var $repeater = $('.repeater').repeater( {
            // (Required)
            // Specify the jQuery selector for this nested repeater
            selector: '.package-menu-item',
            show: function () {

                $(this).slideDown();
                $(this).find('.select2').remove();
//                $(this).find('select').data('select2').destroy();
                $(this).find('select').select2();
            },
            hide: function (deleteElement) {
                if(confirm('Are you sure you want to delete this element?')) {
                    $(this).slideUp(deleteElement);
                }
            },
        });

        var calculationArray = {!! $quotation->getCalculationJson() !!};
    </script>
    <script type="text/javascript" src='{{asset('js/utils.js')}}'></script>
    <script type="text/javascript" src='{{asset('js/quotation.js')}}'></script>
@stop