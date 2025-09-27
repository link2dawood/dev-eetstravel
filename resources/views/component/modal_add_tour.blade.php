{{--Popup create--}}
<div id="modalCreateTour" class="modal hide" tabindex="-1" role="dialog" aria-labelledby="modalCreateTourLabel" style="padding-left: 17px;padding-right: 17px;">
    <div class="modal-dialog modal-lg" style="width: 90%;">
        <div class="modal-content" style="overflow: hidden;">
            <div class="modal-header">
                <a class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">x</span>
                </a>
                <h4 id="modalCreateTourLabel" class="modal-title">{!!trans('main.Createtour')!!}</h4>
            </div>
            @if (count($errors) > 0)
                <br>
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <br>
            @endif
            <form method='POST' action='{!!url("tour")!!}'>

                <div class="modal-body" style="max-height: 320px; overflow-y: auto;">
                        <input type='hidden' name='_token' value='{{Session::token()}}'>
                        <input type='hidden' name='modal_create_tour' value="2">

                        <div class="form-group">
                            <label for="name">{!!trans('main.Name')!!} *</label>
                            {!! Form::text('name', '', ['class' => 'form-control']) !!}
                        </div>
                        <div class="form-group">
                            <label for="overview">{!!trans('main.Overview')!!}</label>
                            {!! Form::text('overview', '', ['class' => 'form-control']) !!}
                        </div>
                        <div class="form-group">
                            <label for="remark">{!!trans('main.Remark')!!}</label>
                            {!! Form::text('remark', '', ['class' => 'form-control']) !!}
                        </div>
                        <div class="form-group">

                            <label for="departure_date">{!!trans('main.DepDate')!!} *</label>

                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                {!! Form::text('departure_date', '', ['class' => 'form-control pull-right datepicker', 'id' => 'departure_date', 'autocomplete' => 'off']) !!}
                            </div>

                        </div>
                        <div class="form-group">
                            <label for="retirement_date">{!!trans('main.RetDate')!!} *</label>

                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                {!! Form::text('retirement_date', '', ['class' => 'form-control pull-right datepicker', 'id' => 'retirement_date', 'autocomplete' => 'off']) !!}
                            </div>

                        </div>
                        <div class="form-group">
                            <label for="pax">Pax</label>
                            {!! Form::text('pax', '', ['class' => 'form-control']) !!}
                        </div>
                        <div class="form-group">
                            <label for="pax_free">{!!trans('main.PaxFree')!!}</label>
                            {!! Form::text('pax_free', '', ['class' => 'form-control']) !!}
                        </div>
                        {{--
                        <div class="form-group">
                            <label for="rooms">{!!trans('main.Rooms')!!}</label>
                            {!! Form::text('rooms', '', ['class' => 'form-control']) !!}
                        </div>--}}
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
                            <label for="country_begin">{!!trans('main.CountryFrom')!!} *</label>
                            {!! Form::select('country_begin',  \App\Helper\Choices::getCountriesArray(), 0, ['class' => 'form-control', 'id' => 'country_from']) !!}
                        </div>
                        <div class="form-group">
                            <label for="city_from">{!!trans('main.Cityfrom')!!} *</label>
                            <input id="city_from" name="city_begin" type="text" class="form-control">
                            <input type="hidden" name="city_begin_code" id="city_code_from">
                        </div>
                        <div class="form-group">
                            <label for="country_end">{!!trans('main.CountryTo')!!} *</label>
                            {!! Form::select('country_end',  \App\Helper\Choices::getCountriesArray(), 0, ['class' => 'form-control', 'id' => 'country_to']) !!}
                        </div>
                        <div class="form-group">
                            <label for="city_to">{!!trans('main.CityTo')!!} *</label>
                            <input id="city_to" name="city_end" type="text" class="form-control">
                            <input type="hidden" name="city_end_code" id="city_code_to">
                        </div>
						<div class="form-group">
                                <label for="pax">{!!trans('main.Totalamount')!!}</label>
                                <input class="form-control" name="total_amount" type="number" value="">
                        </div>
						<div class="form-group">
                                <label for="pax">{!!trans('main.PriceperPerson')!!}</label>
                                <input class="form-control" name="price_for_one" type="number" value="">
                        </div>
                        <div class="form-group">
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

                        </div>
                    <div id="modal_add_tour">
                        <div class="form-group">
                            <label for="status">{!!trans('main.Status')!!}</label>
                            <select2
                                    name="status"
                                    id="status"
                                    :value.sync="status_value_default"
                                    :options="statuses_tour"
                                    :allow-clear="true">
                            </select2>
                        </div>
                        <div class="form-group">
                            <label for="assigned_user">{!!trans('main.AssignedUser')!!} *</label>
                            <select2
                                    name="assigned_user"
                                    id="assigned_user"
                                    :options="users"
                                    :allow-clear="true">
                            </select2>
                        </div>

                        <div class="form-group">
                            <label for="phone">{!!trans('main.Phone')!!}</label>
                            <input id="phone" name="phone" type="text" class="form-control"
                                   value="">
                        </div>
                        
                        <div class="form-group">
                            <label for="responsible_user">{!!trans('main.ResponsibleUser')!!}</label>
                            <select2
                                    custom-first="true"
                                    custom-text="Without responsible user"
                                    custom-value="0"
                                    :value.sync="users_value_default"
                                    name="responsible_user"
                                    id="responsible_user"
                                    :options="users"
                                    :allow-clear="true">
                            </select2>
                        </div>
                    </div>
                        <div class="form-group">
                            <label>{!!trans('main.Files')!!}</label>
                            @component('component.file_upload_field')@endcomponent
                        </div>
                        <div class="form-group">
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
                                            <input name="imgToUpload[]" data-model="Tour" data-id="" class="fileToUpload" type="file" id="imgInp" />
                                        </div>
                                </div>
                             </div>
                        </div>
                    </div>

                <div class="modal-footer">
                    <a href="close" class='btn btn-warning' data-dismiss="modal">{!!trans('main.Close')!!}</a>
                    <button class='btn btn-success' type='submit'>{!!trans('main.Save')!!}</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript" src='{{asset('js/rooms.js')}}'></script>
<script type="text/javascript" src='{{asset('js/tour.js')}}'></script>
<script type="text/javascript" src='{{asset('js/hide_elements.js')}}'></script>

<script>
    
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
    
    $(function () {

        // var select2 =  {
        new Vue({
            el: '#modal_add_tour ',
            data: {
                loading: true,
                users: [],
                statuses_tour: [],
                room_types: [],
                users_value_default:[0],
                status_value_default:[1],
            },
            mounted: function () {
                // this.fetchData();
                var self = this;

                $.ajax({
                    url: '/api/v1/dashboard/modal_add_tour',
                    method: 'GET',
                    dataType: "json",
                    success: function (data) {
                        self.users = data.users;
                        self.statuses_tour = data.statuses_tour;
                        self.room_types = data.room_types;
                    },
                    error: function (error) {
                        console.log(error);
                    }
                });
            },
            methods: {
                fetchData: function () {
                    var self = this;
                    $.ajax({
                        url: '/api/v1/dashboard/modal_add_tour',
                        method: 'GET',
                        dataType: "json",
                        success: function (data) {
                            self.users = data.users;
                            self.statuses_tour = data.statuses_tour;
                            self.room_types = data.room_types;
                        },
                        error: function (error) {
                            console.log(error);
                        }
                    });
                },
                onChange: function (v) {
                }
            }
        });

    });
</script>


<script>
    $(function () {
        $('#country_from').select2({
            dropdownParent: $("#modalCreateTour")
        });

        $('#country_to').select2({
            dropdownParent: $("#modalCreateTour")
        });
    });

	function openTourModal() {
        setModalMaxHeight('#modalCreateTour');
        $('#modalCreateTour').removeClass('hide');
        $('#modalCreateTour').addClass('fade');
        $('#modalCreateTour').modal();
    }

    function setModalMaxHeight(element) {
        this.$element     = $(element);
        this.$content     = this.$element.find('.modal-content');
        var borderWidth   = this.$content.outerHeight() - this.$content.innerHeight();
        var dialogMargin  = $(window).width() < 768 ? 20 : 60;
        var contentHeight = $(window).height() - (dialogMargin + borderWidth);
        var headerHeight  = this.$element.find('.modal-header').outerHeight() || 0;
        var footerHeight  = this.$element.find('.modal-footer').outerHeight() || 0;
        var maxHeight     = contentHeight - (headerHeight + footerHeight);

        this.$content.css({
            'overflow': 'hidden'
        });

        this.$element
            .find('.modal-body').css({
            'max-height': maxHeight,
            'overflow-y': 'auto'
        });
    }

    $('.modal').on('show.bs.modal', function() {
        $(this).show();
        setModalMaxHeight(this);
    });

    $(window).resize(function() {
        if ($('.modal.in').length != 0) {
            setModalMaxHeight($('.modal.in'));
        }
    });
</script>


{{--end Popup create--}}