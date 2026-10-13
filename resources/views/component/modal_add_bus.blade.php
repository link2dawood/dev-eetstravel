

{{--Popup update trip or tour--}}
<div id="modalUpdateTrip" class="modal fade bus-modal" tabindex="-1" role="dialog" aria-labelledby="modalUpdateTripLabel">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content position-relative">
            <div class="modal-header">
                <h5 id="modalUpdateTripLabel" class="modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="alert alert-warning block-error-driver" style="display: none;"></div>

                <div class="trip_update"></div>

                <span id="showPreviewBlock" data-info="{{ true }}"></span>
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-comments-o me-1"></i>{!!trans('main.Comments')!!}</h3>
                    </div>
                    <div class="card-body">
                        <div id="chat-box" style="max-height: 300px; overflow-y: auto;">
                            <div id="show_comments"></div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <form method='POST' action='{{route('comment.store')}}' enctype="multipart/form-data" id="form_comment">
                            <div class="input-group mb-2">
                                <span id="author_name" class="input-group-text" style="display: none;">
                                    <span id="name"></span>
                                    <a href="#" id="reply_close" class="ms-2" aria-label="Cancel reply"><i class="fa fa-close"></i></a>
                                </span>
                                <textarea class="form-control" id="content" name="content" rows="2" placeholder="Ctrl + Enter to post comment"></textarea>
                            </div>

                            <div class="mb-2">
                                <label class="form-label">{!!trans('main.Files')!!}</label>
                                @component('component.file_upload_field_modal')@endcomponent
                            </div>

                            <input type="text" id="parent_comment" hidden name="parent" value="{{ null }}">
                            <input type="text" id="default_reference_id" hidden name="reference_id" value="">
                            <input type="text" id="default_reference_type" hidden name="reference_type" value="{{ \App\Comment::$services['BusDay']}}">

                            <div class="text-end">
                                <button type="submit" class="btn btn-success" id="btn_send_comment">{!!trans('main.Send')!!}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class='btn btn-link link-secondary me-auto' data-bs-dismiss="modal">{!!trans('main.Close')!!}</button>
                <button class='btn btn-primary' id="update_btn_table_bus" type='button'>{!!trans('main.Save')!!}</button>
            </div>

            <div class="overlay bus-modal-overlay" style="display: none">
                <div class="spinner-border text-primary" role="status"></div>
            </div>
        </div>
    </div>
</div>


{{--Popup create--}}
<div id="modalCreateBusAdd" class="modal fade bus-modal" tabindex="-1" role="dialog" aria-labelledby="modalCreateBusLabel">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content position-relative">
            <form method='POST' action='' id="add_day" enctype="multipart/form-data" class="d-flex flex-column" style="min-height: 0;">
                <div class="modal-header">
                    <h5 id="modalCreateBusLabel" class="modal-title">{!!trans('main.AddTrip')!!}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="alert alert-warning block-error-driver" style="display: none;"></div>
                    <div class="alert alert-warning error_date" style="display: none;"></div>

                    <input type='hidden' name='_token' value='{{Session::token()}}'>

                    <div class="d-flex flex-wrap gap-4 mb-3">
                        <label class="form-check trip_select mb-0">
                            <input class="form-check-input" type="radio" name="form_mode" value="0" checked>
                            <span class="form-check-label">{!!trans('main.Withouttour')!!}</span>
                        </label>
                        <label class="form-check tour_select mb-0">
                            <input class="form-check-input" type="radio" name="form_mode" value="1">
                            <span class="form-check-label">{!!trans('main.Withtour')!!}</span>
                        </label>
                    </div>

                    <div class="row g-3">
                        <div class="col-12 trip_field">
                            <label class="form-label required" for="name_trip">{!!trans('main.NameTrip')!!}</label>
                            {!! Form::text('name_trip', '', ['class' => 'form-control clear_input','id' => 'name_trip', 'required',
                              'oninvalid' => "this.setCustomValidity('Field required for filling')",
                              'onchange' => "this.setCustomValidity('')"]) !!}
                        </div>

                        <div class="col-md-6 tour_field">
                            <label class="form-label required" for="tour">{!!trans('main.Tour')!!}</label>
                            <select id="tour" name="tour" onchange="setTimeout(tour_check, 200)" class="form-select clear_select">
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

                        <div class="col-md-6 tour_field">
                            <label class="form-label required" for="transfer_tour">{!!trans('main.Transfer')!!}</label>
                            <select id="transfer_tour" name="transfer_tour" oninvalid="this.setCustomValidity('Field required for filling')" onchange="this.setCustomValidity(''); setTimeout(select_transfer, 200)" class="form-select clear_select">
                                <option selected disabled>{!!trans('main.SelectTransfer')!!}</option>
                                @foreach($transfers as $transfer)
                                    <option value="{{$transfer->id}}" data-service_name="{{$transfer->name}}">
                                        {{$transfer->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 trip_field">
                            <label class="form-label required" for="country_from">{!!trans('main.Country')!!}</label>
                            {!! Form::select('country_begin',  \App\Helper\Choices::getCountriesArray(), 0,
                            ['class' => 'form-select clear_select', 'id' => 'country_from',
                             'required',
                              'oninvalid' => "this.setCustomValidity('Select one of the list of items')",
                              'onchange' => "this.setCustomValidity('')"
                              ]) !!}
                        </div>
                        <div class="col-md-6 trip_field">
                            <label class="form-label required" for="city_from">{!!trans('main.City')!!}</label>
                            <input id="city_from" required oninvalid="this.setCustomValidity('Field required for filling')" onchange="this.setCustomValidity('')" name="city_begin" type="text" class="form-control clear_input">
                            <input type="hidden" name="city_begin_code" id="city_code_from">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label required" for="start_date">{!!trans('main.StartDate')!!}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                {!! Form::text('start_date', '', ['class' => 'form-control clear_input datepicker datepicker_bus_day_dep',
                                 'id' => 'start_date' , 'required', 'autocomplete' => 'off', 'placeholder' => 'YYYY-MM-DD',
                                  'oninvalid' => "this.setCustomValidity('Field required for filling')",
                                  'onchange' => "this.setCustomValidity('')"
                                  ]) !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required" for="end_date">{!!trans('main.EndDate')!!}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                {!! Form::text('end_date', '', ['class' => 'form-control clear_input datepicker datepicker_bus_day_ret',
                                 'id' => 'end_date', 'required', 'autocomplete' => 'off', 'placeholder' => 'YYYY-MM-DD',
                                  'oninvalid' => "this.setCustomValidity('Field required for filling')",
                                  'onchange' => "this.setCustomValidity('')"
                                 ]) !!}
                            </div>
                        </div>

                        <div class="col-md-6 trip_field">
                            <label class="form-label" for="status">{!!trans('main.Status')!!}</label>
                            <select id="status" name="status" class="form-select clear_select" required oninvalid="this.setCustomValidity('Select one of the list of items')" onchange="this.setCustomValidity('')">
                                @foreach( $bus_statuses as $status)
                                    <option {{ $status->name == 'Planned, need to check with coach co' ? 'selected' : ''  }}
                                    value="{{$status->id}}">{{$status->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Trip Fields-->
                        <div class="col-md-6 trip_field">
                            <label class="form-label" for="bus_trip">{!!trans('main.Bus')!!}</label>
                            <select id="bus_trip" name="bus_trip" class="form-select clear_select" required oninvalid="this.setCustomValidity('Select one of the list of items')" onchange="this.setCustomValidity('')">
                                @foreach($buses as $bus)
                                <option value="{{$bus->id}}">{{$bus->name}} ({{$bus->bus_number}})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 trip_field">
                            <label class="form-label" for="drivers_trip">{!!trans('main.Drivers')!!}</label>
                            <select id="drivers_trip" name="drivers_trip[]" class="js-state select2 form-select clear_select2" multiple="multiple">
                                @foreach( $drivers as $driver)
                                    <option value="{{$driver->id}}">{{$driver->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Trip Fields-->

                        <!-- Tour Fields-->
                        <div class="col-md-6 tour_field">
                            <label class="form-label" for="buses_tour">{!!trans('main.Buses')!!}</label>
                            <select name="bus_tour" id="buses_tour" class="form-select clear_select"></select>
                        </div>

                        <div class="col-md-6 tour_field">
                            <label class="form-label" for="drivers_tour">{!!trans('main.Drivers')!!}</label>
                            <select id="drivers_tour" name="drivers_tour[]" class="js-state select2 form-select clear_select2" multiple="multiple"></select>
                        </div>
                        <!-- Tour Fields -->
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class='btn btn-link link-secondary me-auto' data-bs-dismiss="modal">{!!trans('main.Close')!!}</button>
                    <button class='btn btn-primary trip_btn' type='submit'>{!!trans('main.Add')!!}</button>
                    <button class='btn btn-primary tour_btn' id="tour_add_calendar" onclick="createTransferInTour();" type='button'>{!!trans('main.Add')!!}</button>
                </div>
            </form>

            <div class="overlay bus-modal-overlay" style="display: none">
                <div class="spinner-border text-primary" role="status"></div>
            </div>
        </div>
    </div>
</div>

{{--Popup no permission--}}
<div id="modalNoPermission" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalNoPermissionLabel">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="modalNoPermissionLabel" class="modal-title">{!!trans('main.Nopermission')!!}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-footer">
                <button type="button" class='btn btn-primary' data-bs-dismiss="modal">{!!trans('main.Close')!!}</button>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script type="text/javascript">
        // Select2 inside the modals: full width, dropdown attached to the modal (BS5 keeps focus inside it)
        function initBusModalSelects() {
            var modal = $('#modalCreateBusAdd');
            modal.find('select').each(function () {
                var select = $(this);
                if (select.hasClass('select2-hidden-accessible')) select.select2('destroy');
                var options = { width: '100%', dropdownParent: modal };
                if (this.id === 'drivers_trip') options.placeholder = 'Select drivers';
                if (this.id === 'drivers_tour') { options.placeholder = 'Select a transfer first'; options.disabled = !$('#transfer_tour').val(); }
                select.select2(options);
            });
        }

        function showBusMode(tourMode) {
            $('#modalCreateBusAdd .tour_field, #modalCreateBusAdd .tour_btn').toggle(tourMode);
            $('#modalCreateBusAdd .trip_field, #modalCreateBusAdd .trip_btn').toggle(!tourMode);
            $('#modalCreateBusAdd .block-error-driver, #modalCreateBusAdd .error_date').hide().text('');
            mode = tourMode ? 1 : 0;
        }

        $(function () {
            initBusModalSelects();
            showBusMode(false);
        });

        // switch between the fields of the tour and the trip
        $(document).on('change', '#modalCreateBusAdd input[name="form_mode"]', function () {
            var tourMode = this.value === '1';
            resetForm();
            if (tourMode) {
                tour_check();
            } else {
                tour_reset();
            }
            clearFields();
            showBusMode(tourMode);
        });

        // change transfer and get buses and drivers
        function select_transfer() {
            let id_transfer = $('#transfer_tour').val();
            if (!id_transfer) return;

            $('#drivers_tour').prop('disabled', false);

            $.get(`/api/get_buses_transfer/${id_transfer}`).done((buses) => {
                $('#buses_tour').html(buses || '<option value="">No buses for this transfer</option>').trigger('change');
            });

            $.get(`/api/get_drivers_transfer/${id_transfer}`).done((drivers) => {
                $('#drivers_tour').html(drivers || '').trigger('change');
                $('#drivers_tour').select2({ width: '100%', dropdownParent: $('#modalCreateBusAdd'),
                    placeholder: drivers ? 'Select drivers' : 'No drivers for this transfer' });
            });
        }
    </script>
@endpush
{{--end Popup create--}}