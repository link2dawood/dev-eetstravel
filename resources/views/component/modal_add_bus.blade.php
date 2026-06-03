

{{--Popup update trip or tour--}}
<div id="modalUpdateTrip" class="modal fade in" role="dialog" aria-labelledby="modalUpdateTripLabel" style="padding-left: 17px;padding-right: 17px;">
    <div class="modal-dialog modal-lg" role="document" style="width: 90%;">
        <div class="modal-content">
            <div class="modal-header">
                <a class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">x</span>
                </a>
                <h4 id="modalUpdateTripLabel" class="modal-title"></h4>
            </div>

            <div class="alert alert-info block-error-driver" style="text-align: center; display: none;">

            </div>

            <div class="px-5 py-4 space-y-4">
                <div class="trip_update"></div>

                <div class="overlay hidden flex items-center justify-center py-4">
                    <div class="spinner-border spinner-border-sm text-primary" role="status" aria-label="Loading"></div>
                </div>

                <span id="showPreviewBlock" data-info="{{ true }}" class="hidden"></span>

                {{-- Comments panel --}}
                <div class="rounded border border-slate-200 bg-white shadow-subtle flex flex-col">
                    <div class="border-b border-slate-200 px-4 py-3 flex items-center gap-2 cursor-move ui-sortable-handle">
                        <x-ui.icon name="messages-square" class="text-success-600" size="sm" />
                        <h3 class="text-sm font-semibold text-slate-900">{!! trans('main.Comments') !!}</h3>
                    </div>
                    <div class="p-4">
                        <div class="slimScrollDiv max-h-[300px] overflow-y-auto pr-1">
                            <div class="chat" id="chat-box">
                                <div id="show_comments"></div>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-slate-200 bg-slate-50 px-4 py-3">
                        <form method="POST" action="{{ route('comment.store') }}" enctype="multipart/form-data" id="form_comment" class="space-y-3">
                            <div class="input-group flex items-stretch rounded border border-slate-300 bg-white shadow-subtle focus-within:ring-2 focus-within:ring-primary-600/30 focus-within:border-primary-600 overflow-hidden">
                                <span id="author_name" class="input-group-addon hidden items-center gap-1 bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700">
                                    <span id="name"></span>
                                    <a href="#" id="reply_close" class="text-slate-500 hover:text-slate-900 ml-1">
                                        <x-ui.icon name="x" size="xs" />
                                    </a>
                                </span>
                                <textarea class="form-control block w-full px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none border-0"
                                          id="content"
                                          name="content"
                                          rows="3"
                                          placeholder="Ctrl + Enter to post comment"></textarea>
                            </div>

                            <div class="form-group">
                                <label>{!!trans('main.Files')!!}</label>
                                @component('component.file_upload_field_modal')@endcomponent
                            </div>

                            <input type="text" id="parent_comment" hidden name="parent" value="{{ null }}">
                            <input type="text" id="default_reference_id" hidden name="reference_id" value="">
                            <input type="text" id="default_reference_type" hidden name="reference_type" value="{{ \App\Comment::$services['BusDay']}}">

                            <button type="submit" class="btn btn-success pull-right" id="btn_send_comment" style="margin-top: 5px;">{!!trans('main.Send')!!}</button>
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


{{--Popup create--}}
<div id="modalCreateBusAdd" class="modal fade in" role="dialog" aria-labelledby="modalCreateBusLabel"  style="padding-left: 17px;padding-right: 17px;">
    <div class="modal-dialog modal-lg" style="width: 90%;">
        <div class="modal-content">
            <form method='POST' action='' id="add_day" enctype="multipart/form-data">
                <div class="modal-header">
                    <a class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">x</span>
                    </a>
                    <h4 id="modalCreateBusLabel" class="modal-title">{!!trans('main.AddTrip')!!}</h4>
                </div>

                <div class="alert alert-info block-error-driver" style="text-align: center; display: none;">

                </div>

                <div class="alert alert-info error_date" style="text-align: center; display: none;">

                </div>

                    <div class="modal-body">

                        <input type='hidden' name='_token' value='{{Session::token()}}'>

                        <div class="form-group">
                            <div class="col-lg-12">
                                <div class="col-lg-6">
                                    <label class="trip_select">
                                        <input type="radio" name="form_mode"  value="0" checked>
                                        <span style="font-size: 1.1em;">{!!trans('main.Withouttour')!!}</span>
                                    </label>
                                </div>
                                <div class="col-lg-6">
                                    <label class="tour_select">
                                        <input type="radio" name="form_mode" value="1">
                                        <span style="font-size: 1.1em;"> {!!trans('main.Withtour')!!} </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group trip_field">
                            <label for="name">{!!trans('main.NameTrip')!!}</label>
                            {!! Form::text('name_trip', '', ['class' => 'form-control clear_input','id' => 'name_trip', 'required',
                              'oninvalid' => "this.setCustomValidity('Field required for filling')",
                              'onchange' => "this.setCustomValidity('')"]) !!}
                        </div>

                        <div class="form-group tour_field">
                            <label for="tour">{!!trans('main.Tour')!!}</label>
                            <select id="tour" name="tour" onchange="setTimeout(tour_check, 200)" class="form-control clear_select">
                                <option selected disabled>Select Tour</option>
                                @foreach($tours as $tour)
                                    <option value="{{$tour->id}}"
                                            data-dep="{{$tour->departure_date}}"
                                            data-ret="{{$tour->retirement_date}}"
                                    >
                                        {{$tour->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group tour_field">
                            <label for="transfer_tour">{!!trans('main.Transfer')!!}</label>
                            <select id="transfer_tour" name="transfer_tour" required oninvalid="this.setCustomValidity('Field required for filling')" onchange="this.setCustomValidity(''); setTimeout(select_transfer, 200)" class="form-control clear_select">
                                <option selected disabled>{!!trans('main.SelectTransfer')!!}</option>
                                @foreach($transfers as $transfer)
                                    <option value="{{$transfer->id}}" data-service_name="{{$transfer->name}}">
                                        {{$transfer->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group trip_field">
                            <label for="country_begin">{!!trans('main.Country')!!}</label>
                            {!! Form::select('country_begin',  \App\Helper\Choices::getCountriesArray(), 0,
                            ['class' => 'form-control clear_select', 'id' => 'country_from',
                             'required',
                              'oninvalid' => "this.setCustomValidity('Select one of the list of items')",
                              'onchange' => "this.setCustomValidity('')"
                              ]) !!}
                        </div>
                        <div class="form-group trip_field">
                            <label for="city_from">{!!trans('main.City')!!}</label>
                            <input id="city_from" required oninvalid="this.setCustomValidity('Field required for filling')" onchange="this.setCustomValidity('')" name="city_begin" type="text" class="form-control clear_input">
                            <input type="hidden" name="city_begin_code" id="city_code_from">
                        </div>

                        <div class="form-group">
                            <label for="start_date">{!!trans('main.StartDate')!!}</label>
                            <div class="input-group date relative">
                                <div class="input-group-addon pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                    <x-ui.icon name="calendar" size="sm" />
                                </div>
                                {!! Form::text('start_date', '', ['class' => 'form-control clear_input pull-right datepicker datepicker_bus_day_dep',
                                 'id' => 'start_date' , 'required',
                                  'oninvalid' => "this.setCustomValidity('Field required for filling')",
                                  'onchange' => "this.setCustomValidity('')"
                                  ]) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="departure_date">{!!trans('main.EndDate')!!}</label>
                            <div class="input-group date relative">
                                <div class="input-group-addon pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                    <x-ui.icon name="calendar" size="sm" />
                                </div>
                                {!! Form::text('end_date', '', ['class' => 'form-control clear_input pull-right datepicker datepicker_bus_day_ret',
                                 'id' => 'end_date', 'required',
                                  'oninvalid' => "this.setCustomValidity('Field required for filling')",
                                  'onchange' => "this.setCustomValidity('')"
                                 ]) !!}
                            </div>
                        </div>

                        <div class="form-group trip_field">
                            <label for="status"><b>{!!trans('main.Status')!!}</b></label>
                            <select id="status" name="status" class="form-control clear_select" required oninvalid="this.setCustomValidity('Select one of the list of items')" onchange="this.setCustomValidity('')">
                                @foreach( $bus_statuses as $status)
                                    <option {{ $status->name == 'Planned, need to check with coach co' ? 'selected' : ''  }}
                                    value="{{$status->id}}">{{$status->name}}</option>
                                @endforeach
                            </select>

                        </div>

                        <!-- Trip Fields-->
                        <div class="form-group trip_field">
                            <label for="bus_trip"><b>{!!trans('main.Bus')!!}</b></label>
                            <select id="bus_trip" name="bus_trip" class="form-control clear_select" required oninvalid="this.setCustomValidity('Select one of the list of items')" onchange="this.setCustomValidity('')">
                                @foreach($buses as $bus)
                                <option value="{{$bus->id}}">{{$bus->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group trip_field">
                            <label for="drivers_trip"><b>{!!trans('main.Drivers')!!}</b></label>
                            <select id="drivers_trip" name="drivers_trip[]" class="js-state select2 form-control clear_select2" multiple="multiple">
                                @foreach( $drivers as $driver)
                                    <option value="{{$driver->id}}">{{$driver->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Trip Fields-->


                        <!-- Tour Fields-->
                        <div class="form-group tour_field">
                            <label for="buses_tour"><b>{!!trans('main.Buses')!!}</b></label>
                            <select name="bus_tour" id="buses_tour" class="form-control clear_select">

                            </select>
                        </div>

                        <div class="form-group tour_field">
                            <label for="drivers_tour"><b>{!!trans('main.Drivers')!!}</b></label>
                            <select id="drivers_tour" name="drivers_tour[]" class="js-state select2 form-control clear_select2" multiple="multiple">

                            </select>
                        </div>
                        <!-- Tour Fields -->
                    </div>

                    <div class="overlay hidden flex items-center justify-center py-4">
                        <div class="spinner-border spinner-border-sm text-primary" role="status" aria-label="Loading"></div>
                    </div>

                    <div class="modal-footer">
                        <a href="close" class='btn btn-default' data-dismiss="modal">{!!trans('main.Close')!!}</a>
                        <button class='btn btn-primary trip_btn' type='submit'>{!!trans('main.Add')!!}</button>
                        <button class='btn btn-primary tour_btn' id="tour_add_calendar" onclick="createTransferInTour();" type='button'>{!!trans('main.Add')!!}</button>
                    </div>


         </form>
        </div>
    </div>
</div>

{{--Popup no permission--}}
<div id="modalNoPermission" class="modal fade in" role="dialog" aria-labelledby="modalNoPermissionLabel"  style="padding-left: 17px;padding-right: 17px;">
    <div class="modal-dialog modal-lg" style="width: 90%;">
        <div class="modal-content">
            <div class="modal-header">
                <a class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">x</span>
                </a>
            </div>

            <div class="modal-body">
                <h3>{!!trans('main.Nopermission')!!}.</h3>
            </div>
            <div class="modal-footer">
                <a href="close" class='btn btn-default' data-dismiss="modal">{!!trans('main.Close')!!}</a>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        $(function () {
            $('.add_search').select2({
                dropdownParent: $("#modalCreateBus")
            });
			
			$('.add_search').select2({
                dropdownParent: $("#modalCreateBusAdd")
            });
        });
    </script>


    <script type="text/javascript">
        // switch between the fields of the tour and the trip
        $('.trip_select').click(function (e) {
            $('.tour_field').css({'display':'none'});
            $('.trip_field').css({'display':'block'});

            $('.tour_btn').css({'display':'none'});
            $('.trip_btn').css({'display':'inline-block'});

            resetForm();
            tour_reset();
            clearFields();
        });

        // switch between the fields of the tour and the trip
        $('.tour_select').click(function (e) {
            tour_check();
            setTimeout(function (e) {
                $('.trip_field').css({'display':'none'});
                $('.tour_field').css({'display':'block'});

                $('.trip_btn').css({'display':'none'});
                $('.tour_btn').css({'display':'inline-block'});


                clearFields();
            }, 200);
        });



        // placeholder for buses and drivers
        $('#drivers_tour').select2({
            placeholder: 'Select Transfer',
            disabled : true
        });


        // change transfer and get buses and drivers
        function select_transfer() {
            let id_transfer = $('#transfer_tour').val();

            $('#drivers_tour').select2({
                placeholder: false,
                disabled : false
            });


            // get buses
            $.ajax({
                method: 'GET',
                url: `/api/get_buses_transfer/${id_transfer}`,
                data: {}
            }).done( (buses) => {
                $('#buses_tour').html(buses);
            });


            // get drivers
            $.ajax({
                method: 'GET',
                url: `/api/get_drivers_transfer/${id_transfer}`,
                data: {}
            }).done( (drivers) => {
                $('#drivers_tour').html(drivers);
            })
        }
    </script>
@endpush
{{--end Popup create--}}