<?php $__env->startSection('content'); ?>
<?php echo $__env->make('layouts.title',
['title' => 'Office Fees', 'sub_title' => 'Office List',
'breadcrumbs' => [
['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
['title' => 'Tours', 'icon' => 'suitcase', 'route' => null]]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<section class="content">
    <div class="box box-primary">
        <div class="box-body">
			
            <div>
                <div id="tour_create">
                    <?php echo \App\Helper\PermissionHelper::getCreateButton(route('office.create'), \App\Tour::class); ?>

                </div>

            </div>
            <?php if(session('message_buses')): ?>
            <div class="alert alert-info col-md-12" style="text-align: center;">
                <?php echo e(session('message_buses')); ?>

            </div>
            <?php endif; ?>
         
            <br>
            <br>
      
			<div class="table-responsive">
            <table id="offices-table" class="table table-striped table-bordered table-hover" style='background:#fff; width: 100%;'>

                <thead>
                    <tr>
                        <th>id</th>
                        <th>Office Name</th>
                        <th>Office Address</th>
						<th>Bank Name</th>
                        <th>Account No</th>
                        <th>Swift Code</th>
                        <th>Tel</th>
                        <th>Fax</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php $__currentLoopData = $officesData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $office): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($office->id); ?></td>
                        <td><?php echo e($office->office_name); ?></td>
                        <td><?php echo e($office->office_address); ?></td>
                        <td><?php echo e($office->bank_name); ?></td>
                        <td><?php echo e($office->account_no); ?></td>
                        <td><?php echo e($office->swift_code); ?></td>
                        <td><?php echo e($office->tel); ?></td>
                        <td><?php echo e($office->fax); ?></td>
                        <td><?php echo $office->action_buttons; ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th >id</th>
                        <th>Office Name</th>
                        <th>Office Address</th>
						<th>Bank Name</th>
                        <th>Account No</th>
                        <th>Swift Code</th>
                        <th>Tel</th>
                        <th>Fax</th>
                        <th>Actions</th>
                    </tr>
                </tfoot>

            </table>
        </div>
        </div>
    </div>
</section>

<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>

<script>
	
	
//	$(function(){
  //$('.selectedOfice').select2().on('change', function(e) {
  //  var data = $(".selectedOfice option:selected").text();
	//  alert(data);
   // $("#test").val(data);
	  
  //});

//});
	
	
	
	
	
	
	
	
	

	
    $(document).ready(function() {
        let permission = $('#permission').attr('data-permission');
        let classNameStatus = permission ? 'touredit-status' : '';
        let table = $('#offices-table').DataTable({
            dom: "<'row'<'col-md-4'l><'col-md-4'B><'col-md-4'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            buttons: [{
                    extend: 'csv',
                    title: 'Offices List',
                    exportOptions: {
                        columns: ':not(.actions-button)'
                    }
                },
                {
                    extend: 'excel',
                    title: 'Offices List',
                    exportOptions: {
                        columns: ':not(.actions-button)'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Offices List',
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
            pageLength: 50,
            columnDefs: [
                { targets: [8], orderable: false } // Actions column not sortable
            ],
            'columnDefs': [{
                'targets': 5,
                'createdCell': function(td, cellData, rowData, row, col) {
                   
					var url = "<?php echo e(route('tour.update', ':id')); ?>".replace(':id', rowData.id);

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
        $('#offices-table tfoot th').each(function() {
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
        $('#offices-table tfoot th').appendTo('#offices-table thead');

    })
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('scaffold-interface.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/office/index.blade.php ENDPATH**/ ?>