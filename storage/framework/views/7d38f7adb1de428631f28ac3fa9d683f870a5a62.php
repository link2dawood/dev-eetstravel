<?php $__env->startSection('title','Show'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('layouts.title',
   ['title' => 'Event', 'sub_title' => 'Event Show',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Events', 'icon' => 'map-signs', 'route' => route('event.index')],
   ['title' => 'Show', 'route' => null]]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<section class="content">
    <div class="box box-primary">
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="margin_button">
                        <a href="javascript:history.back()">
                            <button class='btn btn-primary'><?php echo trans('main.Back'); ?></button>
                        </a>
                        <a href="<?php echo route('event.edit', $event->id); ?>">
                            <button class='btn btn-warning'><?php echo trans('main.Edit'); ?></button>
                        </a>
                    </div>
                </div>
            </div>
            <ul class="nav nav-tabs" role='tablist'>
                <li role='presentation' class="active"><a href="#info-tab" aria-controls='info-tab' role='tab' data-toggle='tab'><?php echo trans('main.Info'); ?></a></li>
                <li role='presentation'><a href="#history-tab" aria-controls='history-tab' role='tab' data-toggle='tab'><?php echo trans('main.History'); ?></a></li>
				<li role='presentation' class="tab" data-tab="invoices-tab"><a href="#invoices-tab" aria-controls='invoices-tab' role='tab'
                                data-toggle='tab' id="invoices_tab" ><?php echo trans('Invoices'); ?></a></li>
            </ul>
            
            <div class="tab-content">
                <div class="tab-pane fade in active" role='tabpanel' id='info-tab'>
                    <table class = 'table_show table table-bordered col-lg-6'>
                        <tbody>
                        <tr>
                            <td>
                                <b><i><?php echo trans('main.Name'); ?> : </i></b>
                            </td>
                            <td class="info_td_show"><?php echo $event->name; ?></td>
                        </tr>
                        <tr>
                            <td>
                                <b><i><?php echo trans('main.AddressFirst'); ?> : </i></b>
                            </td>
                            <td class="info_td_show"><?php echo $event->address_first; ?></td>
                        </tr>
                        <tr>
                            <td>
                                <b><i><?php echo trans('main.AddressSecond'); ?> : </i></b>
                            </td>
                            <td class="info_td_show"><?php echo $event->address_second; ?></td>
                        </tr>
                        <tr>
                            <td>
                                <b><i><?php echo trans('main.Country'); ?> : </i></b>
                            </td>
							<?php if(!empty($event->country)): ?>
                            <td class="info_td_show"><?php echo \App\Helper\CitiesHelper::getCountryById($event->country)['name']; ?></td>
							<?php else: ?>
							<td class="info_td_show"></td>
							<?php endif; ?>
                        </tr>
                        <tr>
                            <td>
                                <b><i><?php echo trans('main.City'); ?> : </i></b>
                            </td>
							<?php if(!empty($event->city)): ?>
                            <td class="info_td_show"><?php echo \App\Helper\CitiesHelper::getCityById($event->city)['name']; ?></td>
							<?php else: ?>
							<td class="info_td_show"></td>
							<?php endif; ?>
                        </tr>
							
                        <tr>
                            <td>
                                <b><i><?php echo trans('main.Code'); ?> : </i></b>
                            </td>
                            <td class="info_td_show"><?php echo $event->code; ?></td>
                        </tr>
                        <tr>
                            <td>
                                <b><i><?php echo trans('main.WorkPhone'); ?> : </i></b>
                            </td>
                            <td class="info_td_show"><?php echo $event->work_phone; ?></td>
                        </tr>
							
                        <tr>
                            <td>
                                <b><i><?php echo trans('main.WorkFax'); ?> : </i></b>
                            </td>
                            <td class="info_td_show"><?php echo $event->work_fax; ?></td>
                        </tr>
                        <tr>
                            <td>
                                <b><i><?php echo trans('main.WorkEmail'); ?> : </i></b>
                            </td>
                            <td class="info_td_show"><?php echo $event->work_email; ?></td>
                        </tr>
                        </tbody>
                    </table>
                    <table class = 'table_show table table-bordered col-lg-6'>
                        <tbody>
                        <tr>
                            <td>
                                <b><i><?php echo trans('main.ContactName'); ?> : </i></b>
                            </td>
                            <td class="info_td_show"><?php echo $event->contact_name; ?></td>
                        </tr>
                        <tr>
                            <td>
                                <b><i><?php echo trans('main.ContactPhone'); ?> : </i></b>
                            </td>
                            <td class="info_td_show"><?php echo $event->contact_phone; ?></td>
                        </tr>
                        <tr>
                            <td>
                                <b><i><?php echo trans('main.ContactEmail'); ?> : </i></b>
                            </td>
                            <td class="info_td_show"><?php echo $event->contact_email; ?></td>
                        </tr>
                        <tr>
                            <td>
                                <b><i><?php echo trans('main.Comments'); ?> : </i></b>
                            </td>
                            <td class="info_td_show"><?php echo $event->comments; ?></td>
                        </tr>
                        <tr>
                            <td>
                                <b><i><?php echo trans('main.IntComments'); ?> : </i></b>
                            </td>
                            <td class="info_td_show"><?php echo $event->int_comments; ?></td>
                        </tr>
                        <tr>
                            <td>
                                
                                <b><i>Criterias : </i></b>
                            </td>
                            <?php $__empty_1 = true; $__currentLoopData = $criterias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $criteria): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php $__empty_2 = true; $__currentLoopData = $event->criterias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                    <?php if($criteria->id == $item->criteria_id): ?>
                                        <td style="display: block" class="info_td_show criteria_block"><?php echo $criteria->name; ?></td>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                    <td class="info_td_show"></td>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <td class="info_td_show"></td>
                            <?php endif; ?>
                        </tr>
                        <tr>
                            <td>
                                <b><i><?php echo trans('main.Rate'); ?> : </i></b>
                            </td>
                            <td class="info_td_show"><?php echo $event->rate_name; ?></td>
                        </tr>
                        <tr>
                            <td>
                                <b><i><?php echo trans('main.Website'); ?> : </i></b>
                            </td>
                            <td class="info_td_show"><?php echo $event->website; ?></td>
                        </tr>
                        </tbody>
                    </table>
                    <div style="clear: both"></div>
                    <?php $__env->startComponent('component.files', ['files' => $files]); ?><?php echo $__env->renderComponent(); ?>
                    <span id="showPreviewBlock" data-info="<?php echo e(true); ?>"></span>
                    <div class="box box-success" style="position: relative; left: 0px; top: 0px;">
                        <div class="box-header ui-sortable-handle" style="cursor: move;">
                            <i class="fa fa-comments-o"></i>

                            <h3 class="box-title"><?php echo trans('main.Comments'); ?></h3>
                        </div>
                        <div class="box-body">
                            <div class="slimScrollDiv" style="position: relative; overflow-y: scroll;  width: auto;">
                                <div class="box-body box chat" id="chat-box" style="width: auto; height: auto;">
                                    <div id="show_comments"></div>
                                </div>
                                <div class="slimScrollRail" style="width: 7px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; background: rgb(51, 51, 51) none repeat scroll 0% 0%; opacity: 0.2; z-index: 90; right: 1px;"></div>
                            </div>
                        </div>
                        <!-- /.chat -->
                        <div class="box-footer">
                            <form method='POST' action='<?php echo e(route('comment.store')); ?>' enctype="multipart/form-data" id="form_comment">
                                <div class="input-group" style="width: 100%">
                                                        <span id="author_name" class="input-group-addon">
                                                            <span id="name"></span>
                                                            <a href="#" id="reply_close"><i class="fa fa-close"></i></a>
                                                        </span>
                                    <textarea class="form-control" id="content" name="content" placeholder="Ctrl + Enter to post comment"></textarea>
                                </div>
                                <div class="form-group">
                                    <label><?php echo trans('main.Files'); ?></label>
                                    <?php $__env->startComponent('component.file_upload_field'); ?><?php echo $__env->renderComponent(); ?>
                                </div>
                                <input type="text" id="parent_comment" hidden name="parent" value="<?php echo e(null); ?>">
                                <input type="text" id="default_reference_id" hidden name="reference_id" value="<?php echo e($event->id); ?>">
                                <input type="text" id="default_reference_type" hidden name="reference_type" value="<?php echo e(\App\Comment::$services['event']); ?>">

                                <button type="submit" class="btn btn-success pull-right" id="btn_send_comment" style="margin-top: 5px;"><?php echo trans('main.Send'); ?></button>
                            </form>
                        </div>
                    </div>
                </div>
                <div role='tabpanel' class="tab-pane fade" id='history-tab'>
                    <div id="history-container"></div>
                </div>
				<div role="tabpanel" class="tab-pane fade in" id="invoices-tab">
					<div id="tour_create" style="margin-bottom : 20px;">
                    <?php echo \App\Helper\PermissionHelper::getCreateButton(route('invoices.create'), \App\Invoices::class); ?>

                </div>
                    <table id="inovices-table" class="table table-striped table-bordered table-hover"
                        style='background:#fff; width: 90%; table-layout: fixed ; display = "none"'>
                        <thead>
                            <tr>

                                <th>id</th>
                                <th>Invoice No</th>
                                <th>Due Date</th>
                                <th>Recieved Date</th>
                                <th>Tour</th>
                                <th>Service</th>
                                <th>Office Name</th>
                                <th>Total Price</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>id</th>
                                <th>Invoice No</th>
                                <th>Due Date</th>
                                <th>Recieved Date</th>
                                <th>Tour</th>
                                <th>Service</th>
                                <th>Office Name</th>
                                <th>Total Price</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </tfoot>

                    </table>



                </div>
            </div>
        </div>
    </div>
</section>
<span id="services_name" data-service-name='Event' data-history-route="<?php echo e(route('services_history', ['id' => $event->id])); ?>"></span>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('post_scripts'); ?>
    <script src="<?php echo e(asset('js/comment.js')); ?>"></script>
<script>
	$(document).ready(function() {
        let permission = $('#permission').attr('data-permission');
        let classNameStatus = permission ? 'touredit-status' : '';
        const tour_id = $('#tour_date_id').attr('data-tour-id');

        let table = $('#inovices-table').DataTable({
            dom: "<'row'<'col-sm-5'l><'col-sm-2'B><'col-sm-5'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            buttons: [{
                    extend: 'csv',
                    title: 'Tours List',
                    exportOptions: {
                        columns: ':not(.actions-button)'
                    }
                },
                {
                    extend: 'excel',
                    title: 'Tours List',
                    exportOptions: {
                        columns: ':not(.actions-button)'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Tours List',
                    exportOptions: {
                        columns: ':not(.actions-button)',
                    },
                    // customize: function (doc) {
                    //     doc.content[1].table.widths =
                    //     Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                    // },
                },
            ],
            language: {
                search: "Global Search :"
            },
            processing: true,
            serverSide: true,
            pageLength: 50,
            ajax: {
                url: "/TourInvoiceData/api/data/<?php echo $event->name; ?>",
            },
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'invoice_no',
                    name: 'invoice_no',
                    className: 'touredit-name'
                },
                {
                    data: 'dueDate',
                    name: 'dueDate',
                    className: 'touredit-name'
                },
                {
                    data: 'receivedDate',
                    name: 'receivedDate',
                    className: 'touredit-name'
                },
                {
                    data: 'tour',
                    name: 'tour',
                    className: 'touredit-name'
                },

                {
                    data: 'package',
                    name: 'package',
                    className: 'touredit-name'
                },
                {
                    data: 'officeName',
                    name: 'officeName',
                    className: 'touredit-name'
                },
                //        {data: 'retirement_date', name: 'retirement_date', className: 'touredit-retirement_date'},

                {
                    data: 'total_amount',
                    name: 'total_amount',
                    className: 'touredit-city_begin'
                },


                {
                    data: 'status',
                    name: 'status',
                    className: 'touredit-city_begin'
                },

                {
                    data: 'action',
                    name: 'action',
                    searchable: false,
                    sorting: false,
                    orderable: false
                }
            ],
            'columnDefs': [{
                'targets': 5,
                'createdCell': function(td, cellData, rowData, row, col) {
                    var url = "<?php echo e(route('tour.update', ['tour' => '__ID__'])); ?>".replace('__ID__', rowData.id);
                    $(td).attr('data-status-link', url);
                }
            }],
            initComplete: function() {
                this.api().columns().every(function() {
                    var column = this;
                    if (column.footer().className == 'select_search') {
                        var select = $(
                                '<select class="form-control"><option value=""></option></select>'
                            )
                            .appendTo($(column.footer()).empty())
                            .on('change', function() {
                                var val = $.fn.dataTable.util.escapeRegex($(this)
                                    .val());
                                column.search(val ? '^' + val + '$' : '', true, false)
                                    .draw();
                            });

                        column.data().unique().sort().each(function(d, j) {
                            select.append('<option value="' + d + '">' + d +
                                '</option>')
                        });
                    }
                });
            }
        });
        $('#inovices-table tfoot th').each(function() {
            let column = this;
            if (column.className !== 'not') {
                let title = $(this).text();
                $(this).html('<input type="text" class="form-control" placeholder="Search ' + title +
                    '" />');
            } else {
                $(this).html('<span> </span>');
            }
        });
        table.columns().every(function() {
            let that = this;

            $('input', this.footer()).on('keyup change', function() {
                if (that.search() !== this.value) {
                    that.search(this.value).draw();
                }
            });
        });
        $('#inovices-table tfoot th').appendTo('#inovices-table thead');

    })
	</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('scaffold-interface.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/event/show.blade.php ENDPATH**/ ?>