
<div id="modalCreateTour" class="modal hide" tabindex="-1" role="dialog" aria-labelledby="modalCreateTourLabel" style="padding-left: 17px;padding-right: 17px;">
    <div class="modal-dialog modal-lg" style="width: 90%;">
        <div class="modal-content" style="overflow: hidden;">
            <div class="modal-header">
                <a class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">x</span>
                </a>
                <h4 id="modalCreateTourLabel" class="modal-title"><?php echo trans('main.Createtour'); ?></h4>
            </div>
            <?php if(count($errors) > 0): ?>
                <br>
                <div class="alert alert-danger">
                    <ul>
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
                <br>
            <?php endif; ?>
            <form method='POST' action='<?php echo url("tour"); ?>'>

                <div class="modal-body" style="max-height: 320px; overflow-y: auto;">
                        <input type='hidden' name='_token' value='<?php echo e(Session::token()); ?>'>
                        <input type='hidden' name='modal_create_tour' value="2">

                        <div class="form-group">
                            <label for="name"><?php echo trans('main.Name'); ?> *</label>
                            <?php echo Form::text('name', '', ['class' => 'form-control']); ?>

                        </div>
                        <div class="form-group">
                            <label for="overview"><?php echo trans('main.Overview'); ?></label>
                            <?php echo Form::text('overview', '', ['class' => 'form-control']); ?>

                        </div>
                        <div class="form-group">
                            <label for="remark"><?php echo trans('main.Remark'); ?></label>
                            <?php echo Form::text('remark', '', ['class' => 'form-control']); ?>

                        </div>
                        <div class="form-group">

                            <label for="departure_date"><?php echo trans('main.DepDate'); ?> *</label>

                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <?php echo Form::text('departure_date', '', ['class' => 'form-control pull-right datepicker', 'id' => 'departure_date', 'autocomplete' => 'off']); ?>

                            </div>

                        </div>
                        <div class="form-group">
                            <label for="retirement_date"><?php echo trans('main.RetDate'); ?> *</label>

                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <?php echo Form::text('retirement_date', '', ['class' => 'form-control pull-right datepicker', 'id' => 'retirement_date', 'autocomplete' => 'off']); ?>

                            </div>

                        </div>
                        <div class="form-group">
                            <label for="pax">Pax</label>
                            <?php echo Form::text('pax', '', ['class' => 'form-control']); ?>

                        </div>
                        <div class="form-group">
                            <label for="pax_free"><?php echo trans('main.PaxFree'); ?></label>
                            <?php echo Form::text('pax_free', '', ['class' => 'form-control']); ?>

                        </div>
                        
                        <div class="form-group">
                            <label ><?php echo trans('main.RoomTypes'); ?></label>


                            <div id="list_selected_room_types">

                                <?php if(!empty($selected_room_types)): ?>
                                    <?php $__currentLoopData = $selected_room_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php echo $__env->make('component.item_hotel_room_type', ['room_type' => $item], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>

                            </div>

                            <button class="btn btn-success btn_for_select_room_type" type="button"><?php echo trans('main.SelectRooms'); ?></button>

                            <ul class="list_room_types">
                                <ul class="list_room_types" style="display: block; z-index:999;">
                                    <?php if(!empty($room_types)): ?>
                                    <?php $__currentLoopData = $room_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room_type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li class="select_room_type">
                                            <label><?php echo e($room_type->name); ?></label>
                                            <input type="text" data-info="<?php echo e($room_type->id); ?>" hidden value="<?php echo e($room_type); ?>">
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </ul>
                            </ul>

                        </div>
                        <div class="form-group">
                            <label for="country_begin"><?php echo trans('main.CountryFrom'); ?> *</label>
                            <?php echo Form::select('country_begin',  \App\Helper\Choices::getCountriesArray(), 0, ['class' => 'form-control', 'id' => 'country_from']); ?>

                        </div>
                        <div class="form-group">
                            <label for="city_from"><?php echo trans('main.Cityfrom'); ?> *</label>
                            <input id="city_from" name="city_begin" type="text" class="form-control">
                            <input type="hidden" name="city_begin_code" id="city_code_from">
                        </div>
                        <div class="form-group">
                            <label for="country_end"><?php echo trans('main.CountryTo'); ?> *</label>
                            <?php echo Form::select('country_end',  \App\Helper\Choices::getCountriesArray(), 0, ['class' => 'form-control', 'id' => 'country_to']); ?>

                        </div>
                        <div class="form-group">
                            <label for="city_to"><?php echo trans('main.CityTo'); ?> *</label>
                            <input id="city_to" name="city_end" type="text" class="form-control">
                            <input type="hidden" name="city_end_code" id="city_code_to">
                        </div>
						<div class="form-group">
                                <label for="pax"><?php echo trans('main.Totalamount'); ?></label>
                                <input class="form-control" name="total_amount" type="number" value="">
                        </div>
						<div class="form-group">
                                <label for="pax"><?php echo trans('main.PriceperPerson'); ?></label>
                                <input class="form-control" name="price_for_one" type="number" value="">
                        </div>
                        <div class="form-group">
                            <label for="retirement_date"><?php echo trans('main.Invoice'); ?></label>

                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <?php echo Form::text('invoice','', ['class' => 'form-control pull-right datepicker', 'id' => 'invoice', 'autocomplete' => 'off']); ?>

                            </div>

                        </div>
                        <div class="form-group">
                            <label for="retirement_date">G\A</label>

                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <?php echo Form::text('ga','', ['class' => 'form-control pull-right datepicker', 'id' => 'ga', 'autocomplete' => 'off']); ?>

                            </div>

                        </div>
                    <div id="modal_add_tour">
                        <div class="form-group">
                            <label for="status"><?php echo trans('main.Status'); ?></label>
                            <select2
                                    name="status"
                                    id="status"
                                    :value.sync="status_value_default"
                                    :options="statuses_tour"
                                    :allow-clear="true">
                            </select2>
                        </div>
                        <div class="form-group">
                            <label for="assigned_user"><?php echo trans('main.AssignedUser'); ?> *</label>
                            <select2
                                    name="assigned_user"
                                    id="assigned_user"
                                    :options="users"
                                    :allow-clear="true">
                            </select2>
                        </div>

                        <div class="form-group">
                            <label for="phone"><?php echo trans('main.Phone'); ?></label>
                            <input id="phone" name="phone" type="text" class="form-control"
                                   value="">
                        </div>
                        
                        <div class="form-group">
                            <label for="responsible_user"><?php echo trans('main.ResponsibleUser'); ?></label>
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
                            <label><?php echo trans('main.Files'); ?></label>
                            <?php $__env->startComponent('component.file_upload_field'); ?><?php echo $__env->renderComponent(); ?>
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
                    <a href="close" class='btn btn-warning' data-dismiss="modal"><?php echo trans('main.Close'); ?></a>
                    <button class='btn btn-success' type='submit'><?php echo trans('main.Save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript" src='<?php echo e(asset('js/rooms.js')); ?>'></script>
<script type="text/javascript" src='<?php echo e(asset('js/tour.js')); ?>'></script>
<script type="text/javascript" src='<?php echo e(asset('js/hide_elements.js')); ?>'></script>

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


<?php /**PATH /var/www/html/resources/views/component/modal_add_tour.blade.php ENDPATH**/ ?>