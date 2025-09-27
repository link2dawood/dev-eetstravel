<?php $__env->startSection('title','Index'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('layouts.title',
           ['title' => 'Cancellation Polices', 'sub_title' => 'Policies Offer List',
           'breadcrumbs' => [
           ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
           ['title' => 'Currencies', 'icon' => null, 'route' => null]]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" id="cancellation-search" class="form-control" placeholder="Search cancellation policies..." onkeyup="filterTable('cancellation-policies-table', this.value)">
                        </div>
                        <div class="col-md-6 text-right">
                            <button class="btn btn-success btn-sm" onclick="exportTableToCSV('cancellation-policies-table', 'cancellation_policies_export.csv')">
                                <i class="fa fa-download"></i> Export CSV
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="cancellation-policies-table" class="table table-striped table-bordered table-hover bootstrap-table" style='background:#fff; width: 100%;'>
                        <thead>
                            <tr>
                                <th onclick="sortTable(0, 'cancellation-policies-table')">ID <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(1, 'cancellation-policies-table')"><?php echo trans('Policy'); ?> <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(2, 'cancellation-policies-table')"><?php echo trans('Hotel Name'); ?> <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(3, 'cancellation-policies-table')"><?php echo trans('City'); ?> <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(4, 'cancellation-policies-table')"><?php echo trans('Status'); ?> <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(5, 'cancellation-policies-table')"><?php echo trans('Date of stay'); ?> <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(6, 'cancellation-policies-table')">SIN <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(7, 'cancellation-policies-table')">DOU <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(8, 'cancellation-policies-table')">TRI <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(9, 'cancellation-policies-table')"><?php echo trans('Offer Date'); ?> <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(10, 'cancellation-policies-table')"><?php echo trans('Option Date'); ?> <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(11, 'cancellation-policies-table')"><?php echo trans('Tour Name'); ?> <i class="fa fa-sort"></i></th>
                                <th class="actions-button" style="width: 140px!important"><?php echo trans('main.Actions'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $processedOffers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $offer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($offer->id); ?></td>
                                <td><?php echo e($offer->cancel_policy); ?></td>
                                <td><?php echo e($offer->hotel_name); ?></td>
                                <td><?php echo e($offer->city_name); ?></td>
                                <td><?php echo e($offer->status); ?></td>
                                <td><?php echo e($offer->stay_date); ?></td>
                                <td><?php echo e($offer->SIN); ?></td>
                                <td><?php echo e($offer->DOU); ?></td>
                                <td><?php echo e($offer->TRI); ?></td>
                                <td><?php echo e($offer->offer_date ? \Carbon\Carbon::parse($offer->offer_date)->format('Y-m-d') : ''); ?></td>
                                <td><?php echo e($offer->option_date ? \Carbon\Carbon::parse($offer->option_date)->format('Y-m-d') : ''); ?></td>
                                <td><?php echo e($offer->tour_name); ?></td>
                                <td>
                                    <!-- Action buttons would go here -->
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-info btn-sm" title="View">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="13" class="text-center">No cancellation policies found</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Clone Modal -->
        <div class="modal fade" id="tour-clone-modal" tabindex="-1" role='dialog' aria-labelledby='tour-clone-label'>
            <div class="modal-dialog" role='document'>
                <div class="modal-content">
                    <div class="box box-body" style="border-top: none">
                        <div class="alert alert-info block-error" style="text-align: center; display: none;"></div>
                        <form id="tour-clone-modal-form">
                            <div class="form-group">
                                <label for="tour_id"><?php echo e(trans('main.Tour')); ?></label>
                                <input name="offer_date" id="offer_date" type="hidden" value="">
                                <input name="option_date" id="option_date" type="hidden" value="">
                                <select name="tour_id" id="tour_id" class="form-control tour_dropdown" required>
                                    <?php $__currentLoopData = $tours; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tour): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($tour->id); ?>"><?php echo e($tour->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="form-group" id="services" style="display:none"></div>
                            <div class="form-group" id="service_div"></div>
                            <button type="submit" class="btn btn-success pre-loader-func" id="clone_tour_send"><?php echo trans('main.Submit'); ?></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Templates Modal -->
    <div class="modal fade" id="TemplatesModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false" style="padding-left: 17px;padding-right: 17px;">
        <div class="modal-dialog modal-lg" style="width: 90%;">
            <form class="modal-content" id="templateSendForm" enctype="multipart/form-data" action="/templates/api/send" method="POST">
                <input name="_token" type="hidden" value="<?php echo e(csrf_token()); ?>">
                <input name="id" id="id" type="hidden" value="">
                <input name="package_id" id="package_id" type="hidden" value="">
                <input name="tour_id" id="tour_id" type="hidden" value="">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo trans('main.SendTemplate'); ?></h3>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <div class="input-group">
                                <input name="email" id="email" class="form-control" placeholder="E-mail:" required="" value="">
                                <span class="input-group-addon"> <?php echo trans('main.Template'); ?></span>
                                <span class="input-group-addon" style="width:0px; padding-left:0px; padding-right:0px; border:none;"></span>
                                <select id="template_selector" name="template_selector" class="form-control"></select>
                            </div>
                        </div>
                        <div class="form-group">
                            <input name="subject" id="subject" class="form-control" placeholder="Subject:" value="" style="pointer-events: none;">
                        </div>
                        <div class="form-group">
                            <textarea name="templatesContent" id="templatesContent" placeholder="Non required Field" class="form-control" style="height: 400px; visibility: hidden; display: none;"></textarea>
                        </div>
                        <div class="form-group">
                            <div class="btn btn-default btn-file">
                                <i class="fa fa-paperclip"></i> <?php echo trans('main.Attachment'); ?>

                                <input type="file" name="attachment[]" multiple="" name="file" id="file">
                            </div>
                            <div id="file_name"></div>
                            <script>
                                document.getElementById('file').onchange = function() {
                                    $('#file_name').html('Selected files: <br/>');
                                    $.each(this.files, function(i, file) {
                                        $('#file_name').append(file.name + ' <br/>');
                                    });
                                };
                            </script>
                            <p class="help-block">Max. 32MB</p>
                        </div>
                    </div>
                    <div class="box-footer">
                        <div class="pull-right">
                            <button id="send" onclick="sendTemplate();" class="btn btn-primary"><i class="fa fa-file-code-o"></i> <?php echo trans('main.Send'); ?></button>
                        </div>
                        <button type="reset" class="btn btn-default modal-close" data-dismiss="modal"><i class="fa fa-times"></i> <?php echo trans('main.Discard'); ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('js/loadtemplate.js')); ?>"></script>
<script src="<?php echo e(asset('js/bootstrap-tables.js')); ?>"></script>
<script>
    $(document).ready(function () {
        // Initialize Bootstrap table
        initializeBootstrapTable('cancellation-policies-table');

        $('#tour-clone-modal-form').submit(function (e) {
            var userConfirmed = confirm('Are you sure? Do you really want to submit the form?');
            if (!userConfirmed) {
                e.preventDefault();
                location.reload();
            }
        });
    });

    function dropdown_ajax(tour_id, offer_date, option_date) {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content') }
        });
        $.ajax({
            type: "POST",
            url: `/offer/${tour_id}/days_dropdown`,
            data: {
                offer_date: offer_date,
                option_date: option_date,
            },
            success: function(result) {
                if (result[0] === "") {
                    $("#service_div").show();
                    $("#services").hide();
                    $("#service_div").html(`<h3> Please Add Service in the tour </h3>`);
                } else {
                    $("#service_div").hide();
                    $("#services").show();
                    $("#services").html(result);
                }
            },
            error: function(result) {
                console.log(result);
            }
        });
    }

    setTimeout(function () {
        $('.tour_dropdown').on('change', function(){
            let offer_date = $('#offer_date').val();
            let option_date = $('#option_date').val();
            dropdown_ajax($(this).val(), offer_date, option_date);
        });

        $('.change-tour-button').show();
        $('.change-tour-button').on('click', function(){
            let id = $(this).data('id');
            let tour_id = $(this).data('tour');
            let offer_date = $(this).data('offer_date');
            let option_date = $(this).data('option_date');

            $('#offer_date').val(offer_date);
            $('#option_date').val(option_date);

            dropdown_ajax(tour_id, offer_date, option_date);
            $('#tour_id').val(tour_id).trigger('change');

            $('#tour-clone-modal-form').attr('action', '/offer/' + id + '/assign_to_tour');
        });
    }, 3000);
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('scaffold-interface.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/offers/cancellation_policies.blade.php ENDPATH**/ ?>