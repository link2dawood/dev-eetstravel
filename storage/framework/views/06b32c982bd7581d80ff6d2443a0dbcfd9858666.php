<?php $__env->startSection('title','Show'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('layouts.title',
   ['title' => 'Client Invoices ', 'sub_title' => 'accounting Show',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'accountings', 'icon' => 'handshake-o', 'route' => route('accounting.index')],
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
						<a class="btn btn-default"

                                               href="<?php echo e(route('accounting_pdf_export', ['id' => $transactions->id, 'pdf_type' => 'short'])); ?>">PDF</a>
						<a class="btn btn-default"
                                               
                                               href="<?php echo e(route('accounting_excel_export', ['id' => $transactions->id])); ?>">Excel</a>
                        
                    </div>
                </div>
            </div>
            <div id="fixed-scroll" class="nav-tabs-custom">
                <ul class="nav nav-tabs" id="fixed-scroll" role='tablist'>
                    <li role='presentation' class="active"><a href="#info-tab" aria-controls='info-tab' role='tab' data-toggle='tab'><?php echo trans('main.Info'); ?></a></li>
             
                 
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane fade in active" role='tabpanel' id='info-tab'>
					
					<input id="invoice_id" type="hidden" name="invoice_id" value ="<?php echo e($transactions->id); ?>">
					<div class="row">
						<div class="col-lg-6">
					<table class = 'table_show table table-bordered col-lg-6'>
                        <tbody>
                       
                        <tr>
                            <td>
                                <b><i><?php echo trans('Date'); ?> : </i></b>
                            </td>
                            <td class="info_td_show"><?php echo $transactions->date; ?></td>
                        </tr>
                        <tr>
                            <td>
                                <b><i><?php echo trans('Invoice No'); ?> : </i></b>
                            </td>
                            <td class="info_td_show"><?php echo $transactions->invoice_no; ?></td>
                        </tr>
                        </tbody>
                    </table>
						</div>
						<div class="col-lg-6">
							
                    <table class = 'table_show table table-bordered col-lg-6'>
                        <tbody>
                       
                        <tr>
                            <td>
                                <b><i><?php echo trans('Tour Name'); ?> : </i></b>
                            </td>
                            <td class="info_td_show"><?php echo $tour->name; ?></td>
                        </tr>
                        <tr>
                            <td>
                                <b><i><?php echo trans('Status'); ?> : </i></b>
                            </td>
                            <td class="info_td_show"><?php echo $transactions->status($transactions); ?></td>
                        </tr>
                        </tbody>
                    </table>
						</div>
						<div class="col-lg-6">
                    <table class = 'table_show table table-bordered col-lg-6'>
                        <tbody>
                        
                        
                        <tr>
                            <td>
                                <b><i><?php echo trans('Client Name'); ?> : </i></b>
                            </td>
                            <td class="info_td_show"><?php echo $transactions->client->name??""; ?></td>
                        </tr>
                        <tr>
                            <td>
                                <b><i><?php echo trans('Amount Receiveable'); ?> : </i></b>
                            </td>
                            <td class="info_td_show"><?php echo $transactions->amount_receiveable; ?></td>
                        </tr>
                        </tbody>
                    </table>
									</div>
									</div>
							
                    <div style="clear: both"></div>
                   
                </div>
				<div class="">
					<h3 class="box-title">Paid To Payment Amount</h3>
					<div class="table-responsive">
						<div id="payment_create" style="margin-bottom : 20px;">
                    	<?php echo \App\Helper\PermissionHelper::getCreateButton(route('add__invoice_payment',$transactions->id), \App\ClientInvoices::class); ?>

               		 </div>
					<table id="service-transactions-table" class="table table-striped table-bordered table-hover" style='background:#fff; width: 100%; display = "none"'>
                <thead>
                    <tr>
				<th>Id</th>
                <th>Date</th>
				<th>Transaction No</th>
				<th>Invoice No</th>
				<th>Amount</th>
                <th>Unallocated</th>
            </tr>
                </thead>
                <tfoot>
                    <tr>
				<th>Id</th>
                <th>Date</th>
				<th>Transaction No</th>
				<th>Invoice No</th>
				<th>Amount</th>
                <th>Unallocated</th>
            </tr>
                </tfoot>
              
            </table>
						
						 <div class="box box-success" style="position: relative; left: 0px; top: 0px; border-top: none">
                            <div class="box-header ui-sortable-handle" style="cursor: move;">
                                <i class="fa fa-comments-o"></i>

                                <h3 class="box-title"><?php echo trans('main.Comments'); ?></h3>
                            </div>
                            <div class="box-body">
                                <div class="slimScrollDiv" style="position: relative; overflow-y: scroll;  width: auto;">
                                    <div class="box-body box chat" id="chat-box" style="width: auto; height: auto;">
                                        <div id="show_comments"></div>
                                    </div>
                                    <div class="slimScrollRail"
                                        style="width: 7px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; background: rgb(51, 51, 51) none repeat scroll 0% 0%; opacity: 0.2; z-index: 90; right: 1px;">
                                    </div>
                                </div>
                            </div>
                            <!-- /.chat -->
                            <div class="box-footer">
                                <form method='POST' action='<?php echo e(route('comment.store')); ?>' enctype="multipart/form-data"
                                    id="form_comment">
                                    <div class="input-group" style="width: 100%">
                                        <span id="author_name" class="input-group-addon">
                                            <span id="name"></span>
                                            <a href="#" id="reply_close"><i class="fa fa-close"></i></a>
                                        </span>
                                        <textarea class="form-control" id="content" name="content" placeholder="Ctrl + Enter to post comment"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label><?php echo trans('main.Files'); ?></label>
                                        <?php $__env->startComponent('component.file_upload_field'); ?>
                                        <?php echo $__env->renderComponent(); ?>
                                    </div>
                                    <input type="text" id="parent_comment" hidden name="parent"
                                        value="<?php echo e(null); ?>">
                                    <input type="text" id="default_reference_id" hidden name="reference_id"
                                        value="<?php echo e($transactions->id); ?>">
                                    <input type="text" id="default_reference_type" hidden name="reference_type"
                                        value="<?php echo e(\App\Comment::$services['client_invoices']); ?>">

                                    <button type="submit" class="btn btn-success pull-right" id="btn_send_comment"
                                        style="margin-top: 5px;"><?php echo trans('main.Send'); ?>

                                    </button>
                                </form>
                            </div>
                        </div>
				</div>
				</div>
           
               
					
            </div>
            </div>
		
		
</section>
	
    <span id="services_name" data-service-name='accounting' data-history-route="<?php echo e(route('services_history', ['id' => $transactions->id])); ?>"></span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('post_scripts'); ?>
    <script src="<?php echo e(asset('js/comment.js')); ?>"></script>
	
<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
	<script type="text/javascript" src="<?php echo e(asset('js/jspdf.min.js')); ?>"></script>
	 <script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/2.4.1/lodash.min.js"></script>
<script>
	
  $(document).ready(function() {
	  let invoice_id = $("#invoice_id").val();
        let permission = $('#permission').attr('data-permission');
        let classNameStatus = permission ? 'touredit-status' : '';
        let table = $('#service-transactions-table').DataTable({
            dom: "<'row'<'col-sm-4'l><'col-sm-4'B><'col-sm-4'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            buttons: [{
                    extend: 'csv',
                    title: 'Payments List',
                    exportOptions: {
                        columns: ':not(.actions-button)'
                    }
                },
                {
                    extend: 'excel',
                    title: 'Payments List',
                    exportOptions: {
                        columns: ':not(.actions-button)'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Payments List',
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
                url: "/accountingServiceTransaction/api/data/1/"+ invoice_id,
            },
            columns: [{
                    data: 'id',
                    name: 'id'
                },
				{
                    data: 'date',
                    name: 'date',
                    className: 'touredit-name'
                },
				 
					  {
                    data: 'trans_no',
                    name: 'trans_no',
                    className: 'touredit-name'
                },
					  {
                    data: 'invoice_no',
                    name: 'invoice_no',
                    className: 'touredit-name'
                },
				 {
                    data: 'amount',
                    name: 'amount',
                    className: 'touredit-name'
                },
                {
                    data: 'unallocated',
                    name: 'unallocated',
                    className: 'touredit-name'
                },
         
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
                        var select = $('<select class="form-control"><option value=""></option></select>')
                            .appendTo($(column.footer()).empty())
                            .on('change', function() {
                                var val = $.fn.dataTable.util.escapeRegex($(this).val());
                                column.search(val ? '^' + val + '$' : '', true, false).draw();
                            });

                        column.data().unique().sort().each(function(d, j) {
                            select.append('<option value="' + d + '">' + d + '</option>')
                        });
                    }
                });
            }
        });
        $('#service-transactions-table tfoot th').each(function() {
            let column = this;
            if (column.className !== 'not') {
                let title = $(this).text();
                $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');
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
        $('#service-transactions-table tfoot th').appendTo('#service-transactions-table thead');

    })
		</script>

<?php $__env->stopPush(); ?>
<?php echo $__env->make('scaffold-interface.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/accounting/show.blade.php ENDPATH**/ ?>