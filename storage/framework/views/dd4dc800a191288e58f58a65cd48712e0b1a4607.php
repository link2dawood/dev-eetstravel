<?php $__env->startSection('title', 'Guest List'); ?>
<?php $__env->startSection('content'); ?>

    <section class="content">
        <div class="box box-primary">
            <div class="box box-body" id="quotation_body" style="border-top: none">
                <div style="margin-bottom: 10px;">
                    <a href="javascript:history.back()">
                        <button class='btn btn-primary'><?php echo trans('main.Back'); ?></button>
                    </a>
                    <button class="btn btn-success roomlist_submit"><?php echo trans('main.Save'); ?></button>
                </div>
                <script>
                    let tourId = <?php echo e($tour->id); ?>;
                    const URLtoGuestList = "<?php echo e(route('tour.show', ['tour' => $tour->id, 'tab' => 'room_list' ])); ?>";
                </script>
                <?php echo e(csrf_field()); ?>

                <br>
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-success">
                            <div class="box-header ui-sortable-handle" style="cursor: move;">
                                <i class="fa fa-list-ol" aria-hidden="true"></i>
                                <h3 class="box-title"><?php echo trans('main.GuestList'); ?></h3>
                            </div>
                            <div class="box box-body">
                                <form id="roomlist_form" action="<?php echo e(route('guestlist.store')); ?>" method="POST">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-4">
                                                <input name="name" class="form-control" type="text" placeholder="Name" id="guest_list_name" required>
                                            </div>
                                            <div class="col-md-8 text-red hide validate-name">
                                                <span style="line-height: 30px;"><?php echo trans('main.Nameisrequiredfield'); ?></span>
                                            </div>
                                        </div>    
                                        <br/>
                                        <br/>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">                                            
                                            <div class="col-md-5">
                                                <div class="input-group">
                                                    <span class="input-group-addon" > <?php echo trans('main.Template'); ?></span>
                                                    <!-- insert this line -->
                                                    <span class="input-group-addon" style="width:0px; padding-left:0px; padding-right:0px; border:none;"></span>

                                                    <select id="template_selector_guest" name="template_selector_guest" class="form-control" >
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-group">
                                                    <span class="input-group-addon" > <?php echo trans('main.Hotels'); ?></span>
                                                    <select multiple id="hotelselect" name="hotelIds[]" >
                                                        <?php $__currentLoopData = $tour->getHotels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hotel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <?php if($hotel->name ): ?><option value="<?php echo e($hotel->id); ?>"><?php echo $hotel->name; ?></option> <?php endif; ?>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                                    </select>
                                                    <span class="input-group-addon" >
                                                        <input type="checkbox" id="checkboxallhotels"><?php echo trans('main.SelectAll'); ?>

                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" class="btn btn-primary btn-flat pull-right hidden"
                                                        id="roomlist_send"><?php echo trans('main.SendtoHotels'); ?>

                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <table class="table table-bordered finder-disable">
                                        <textarea name="roomlist_textarea" id="roomlist_textarea"
                                                  class="form-control" style=" height: 800px; visibility: hidden;
                                                  display: none;" >
                                        </textarea>
                                    </table>
                                    <div class="row">
                                        <div class="col-md-3">
                                        </div>
                                    </div>
                                    <input type="hidden" id="show_url"
                                           value="<?php echo e(route('roomlist.show', ['id' => $tour->id])); ?>">
                                    <input type="hidden" id="send_url"
                                           value="<?php echo e(route('guestlist.send', ['id' => $tour->id])); ?>">
                                    <input type="hidden" name="tourId" id="tourId" value="<?php echo e($tour->id); ?>">
                                    <?php echo e(csrf_field()); ?>

                            </div>
                        </div>                        
                    </div>
                </div>
                <div class="box-footer">
                        <button class="btn btn-success roomlist_submit"><?php echo trans('main.Save'); ?></button>
                        <a href="javascript:history.back()">
                            <button class="btn btn-warning" type="button"><?php echo trans('main.Cancel'); ?></button>
                        </a>
                </div>
                </form>
            </div>
        </div>
        <div class="modal fade in" id="question_modal" tabindex="-1" role="dialog" aria-labelledby="myQuestionLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">×</span></button>
                        <h4 class="modal-title" id="myModalLabel"><?php echo trans('main.Warning'); ?>!!</h4></div>
                    <div class="modal-body">
                        <div class="modal-body"><?php echo trans('main.WouldyouliketosendGuestList'); ?>?</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo trans('main.Close'); ?></button>
                        <button type="button" class="btn btn-primary" id="send_agree"><?php echo trans('main.Agree'); ?></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" tabindex="-1" id="error_send">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss='modal' aria-label="Close"><span
                                    aria-hidden='true'>&times;</span></button>
                        <h4 class="modal-title" id="title_modal_error"><?php echo trans('main.Warning'); ?>!</h4>
                    </div>
                    <div class="modal-body">
                        <h3 class="error_send_message"></h3>
                    </div>
                    <div class="modal-footer">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script type="text/javascript">
        const tour_id = $('#tour_date_id').attr('data-tour-id');
        loadGuestTemplate('0', 'email', '<?php echo e($tour->name); ?>', '<?php echo e($tour->pax); ?>', '<?php echo e($tour->city_end); ?>', 'emailto', 'phone', 'description', '<?php echo e($tour->getStatusName()); ?>','<?php echo e($tour->getTourDaysSortedByDate()->first()->date); ?>','<?php echo e($tour->price_for_one); ?>', 'menu', '<?php echo e($tour->id); ?>' );
        
        function loadGuestTemplate(service_id, email, name, pax, address, emailto, phone, description, status, time_from, price_for_one,menu,tour_id) {

                var selected = '';
                var html = '';
                $.ajax({
                    url: '/templates/api/loadServiceTemplates',
                    method: 'GET',

                    data: {
                        id: 0,
                    },
                    success : function (res) {

                        html += "<option value='' selected disabled hidden>Choose here</option>";
                        for(var i= 0 ; i < res.templates.length; i++) {

                            if(res.templates[i]['name'] != 'Footer' && res.templates[i]['name'] != 'Header') {
                                html += "<option value='" + res.templates[i]['id'] + "' " + selected + ">" + res.templates[i]['name'] + "</option>";
                            }
                        }

                        $('#template_selector_guest').html(html);

                        $('#template_selector_guest').on('change', function() {
                                var id = $('#template_selector_guest').val();

                                $.ajax({
                                    url: '/templates/api/load',
                                    method: 'GET',
                                    data: {
                                        service_id: service_id,
                                        id: id,
                                        email: email,
                                        name: name,
                                        pax: pax,
                                        address : address,
                                        emailto : emailto,
                                        phone : phone,
                                        description: description,
                                        status: status,
                                        time_from: time_from,
                                        price_for_one : price_for_one,
                                        menu: menu,
                                        tour_id : tour_id
                                    }
                                }).done((res) => {
                                    CKEDITOR.instances['roomlist_textarea'].setData(res.content);
                                });
                        });
                    }
                })
            }        
        
        $('#guest_list_name').keyup(function() {
            if($(this).val() != ''){
                $('#roomlist_form').find('#roomlist_send').removeClass('hidden');
            } else{
                $('#roomlist_form').find('#roomlist_send').addClass('hidden');
            }
        });
        
        $("#checkboxallhotels").click(function(){
            if($("#checkboxallhotels").is(':checked') ){
                $("#hotelselect > option").prop("selected","selected");
                $("#hotelselect").trigger("change");
            }else{
                $("#hotelselect > option").removeAttr("selected");
                 $("#hotelselect").trigger("change");
             }
        });
        
        $('.roomlist_submit').click(function(e){
            e.preventDefault();
            if($("#hotelselect").val() && $('#guest_list_name').val()){
                $('#roomlist_form').submit();
            } else{
                $('#error_send').find('.error_send_message').html('Please fill input fields!');
                $('#error_send').find('#title_modal_error').html('Alert!');
                $('#error_send').modal();
            }
        });
    </script>
    <script type="text/javascript" src="<?php echo e(asset('js/ckeditor/ckeditor.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('js/roomlist.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('scaffold-interface.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/guest_list/create.blade.php ENDPATH**/ ?>