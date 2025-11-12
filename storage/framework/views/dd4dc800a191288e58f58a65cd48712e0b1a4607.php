<?php $__env->startSection('title', 'Create Guest List'); ?>

<?php $__env->startSection('content'); ?>
    
    <div class="page-header d-print-none mb-3">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(url('/home')); ?>">Home</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('tour.show', ['tour' => $tour->id])); ?>"><?php echo e($tour->name); ?></a></li>
                            <li class="breadcrumb-item active">Create Guest List</li>
                        </ol>
                    </nav>
                </div>
                <h2 class="page-title">
                    <i class="ti ti-users me-2"></i>Create Guest List
                </h2>
            </div>
            
            <div class="col-auto ms-auto d-print-none">
                <?php echo $__env->make('components.guest-list-actions', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
        </div>
    </div>

    
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="ti ti-file-text me-2"></i><?php echo trans('main.GuestList'); ?>

            </h3>
        </div>
        <div class="card-body">
            <script>
                let tourId = <?php echo e($tour->id); ?>;
                const URLtoGuestList = "<?php echo e(route('tour.show', ['tour' => $tour->id, 'tab' => 'room_list'])); ?>";
            </script>

            
            <?php echo $__env->make('components.guest-list-form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
        <div class="card-footer text-end">
            <button class="btn btn-success roomlist_submit">
                <i class="ti ti-device-floppy me-1"></i><?php echo trans('main.Save'); ?>

            </button>
            <a href="javascript:history.back()" class="btn btn-warning">
                <i class="ti ti-x me-1"></i><?php echo trans('main.Cancel'); ?>

            </a>
        </div>
    </div>
    
    <div class="modal modal-blur fade" id="question_modal" tabindex="-1" aria-labelledby="myQuestionLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">
                        <i class="ti ti-alert-triangle me-2"></i><?php echo trans('main.Warning'); ?>

                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><?php echo trans('main.WouldyouliketosendGuestList'); ?>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-bs-dismiss="modal">
                        <i class="ti ti-x me-1"></i><?php echo trans('main.Close'); ?>

                    </button>
                    <button type="button" class="btn btn-primary" id="send_agree">
                        <i class="ti ti-check me-1"></i><?php echo trans('main.Agree'); ?>

                    </button>
                </div>
            </div>
        </div>
    </div>

    
    <div class="modal modal-blur fade" tabindex="-1" id="error_send" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="title_modal_error">
                        <i class="ti ti-alert-triangle me-2"></i><?php echo trans('main.Warning'); ?>!
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <h3 class="error_send_message"></h3>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                        <i class="ti ti-check me-1"></i>OK
                    </button>
                </div>
            </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('post_scripts'); ?>
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        const tour_id = $('#tour_date_id').attr('data-tour-id');
        loadGuestTemplate('0', 'email', '<?php echo e($tour->name); ?>', '<?php echo e($tour->pax); ?>', '<?php echo e($tour->city_end); ?>', 'emailto', 'phone', 'description', '<?php echo e($tour->getStatusName()); ?>','<?php echo e($tour->getTourDaysSortedByDate()->first()->date); ?>','<?php echo e($tour->price_for_one); ?>', 'menu', '<?php echo e($tour->id); ?>' );

        function loadGuestTemplate(service_id, email, name, pax, address, emailto, phone, description, status, time_from, price_for_one, menu, tour_id) {
            var selected = '';
            var html = '';
            $.ajax({
                url: '/templates/api/loadServiceTemplates',
                method: 'GET',
                data: {
                    id: 0,
                },
                success: function (res) {
                    html += "<option value='' selected disabled hidden>Choose template...</option>";
                    for(var i = 0; i < res.templates.length; i++) {
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
                                address: address,
                                emailto: emailto,
                                phone: phone,
                                description: description,
                                status: status,
                                time_from: time_from,
                                price_for_one: price_for_one,
                                menu: menu,
                                tour_id: tour_id
                            }
                        }).done((res) => {
                            if (CKEDITOR.instances['roomlist_textarea']) {
                                CKEDITOR.instances['roomlist_textarea'].setData(res.content);
                            }
                        });
                    });
                }
            });
        }
        
        // Show/hide send button based on name input
        $('#guest_list_name').on('input', function() {
            if($(this).val() != '') {
                $('#roomlist_send').removeClass('hidden');
            } else {
                $('#roomlist_send').addClass('hidden');
            }
        });
        
        // Select all hotels checkbox
        $("#checkboxallhotels").on('change', function() {
            if($(this).is(':checked')) {
                $("#hotelselect > option").prop("selected", "selected");
                $("#hotelselect").trigger("change");
            } else {
                $("#hotelselect > option").removeAttr("selected");
                $("#hotelselect").trigger("change");
            }
        });
        
        // Form submission
        $('.roomlist_submit').on('click', function(e) {
            e.preventDefault();
            if($("#hotelselect").val() && $('#guest_list_name').val()) {
                $('#roomlist_form').submit();
            } else {
                $('#error_send').find('.error_send_message').html('Please fill all required fields!');
                $('#error_send').find('#title_modal_error').html('Alert!');

                // Show modal using jQuery and Bootstrap
                var errorModalEl = document.getElementById('error_send');
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    const errorModal = new bootstrap.Modal(errorModalEl);
                    errorModal.show();
                } else {
                    // Fallback to jQuery modal
                    $('#error_send').modal('show');
                }
            }
        });

        // Initialize Bootstrap modals
        var questionModal = document.getElementById('question_modal');
        if (questionModal) {
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                new bootstrap.Modal(questionModal);
            }
        }
    });
</script>
<script type="text/javascript" src="<?php echo e(asset('js/ckeditor/ckeditor.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(asset('js/roomlist.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('scaffold-interface.layouts.tabler-app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/guest_list/create.blade.php ENDPATH**/ ?>