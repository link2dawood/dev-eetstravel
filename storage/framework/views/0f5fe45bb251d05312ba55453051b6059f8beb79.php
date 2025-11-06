
<?php $__env->startSection('content'); ?>
<?php echo $__env->make('layouts.title',
['title' => 'Reporting', 'sub_title' => 'Summary',
'breadcrumbs' => [
['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
['title' => 'Reporting', 'icon' => 'suitcase', 'route' => null]]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<section class="content">
    
	
	
		<button type="button" class="close" data-dismiss='modal' aria-label="Close"><span
                                aria-hidden='true'>&times;</span></button>
                    <h4 class="modal-title"><?php echo trans('main.Addservice'); ?></h4>
                    
                    <form action="<?php echo e(route('supplier_show')); ?>">
                        <div class="form-group">
                            <select id="service-select" class="form-control">
                                <option selected><?php echo trans('main.All'); ?></option>
                                <?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option><?php if($option ==='Transfer'): ?> Bus Company <?php else: ?> <?php echo e($option); ?> <?php endif; ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </form>
        <div class="mb-3">
            <div class="row">
                <div class="col-md-6">
                    <input type="text" id="reporting-search" class="form-control" placeholder="Search services..." onkeyup="filterTable('search-table', this.value)">
                </div>
                <div class="col-md-6 text-right">
                    <button class="btn btn-success btn-sm" onclick="exportTableToCSV('search-table', 'reporting_services_export.csv')">
                        <i class="fa fa-download"></i> Export CSV
                    </button>
                </div>
            </div>
        </div>

		<div class="box box-body table-responsive" style="border-top: none">
                    <table id="search-table" class="table table-striped table-bordered table-hover bootstrap-table" style="width: 100%;">
                        <thead>
                        <tr>
                            <th onclick="sortTable(0, 'search-table')"><?php echo trans('main.Name'); ?> <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(1, 'search-table')"><?php echo trans('main.Address'); ?> <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(2, 'search-table')"><?php echo trans('main.Country'); ?> <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(3, 'search-table')"><?php echo trans('main.City'); ?> <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(4, 'search-table')"><?php echo trans('main.Phone'); ?> <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(5, 'search-table')"><?php echo trans('main.ContactName'); ?> <i class="fa fa-sort"></i></th>
                            <th class="actions-button"><?php echo trans('Actions'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $servicesData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($service->nameService ?? $service->name); ?></td>
                                <td><?php echo e($service->address_first ?? ''); ?></td>
                                <td><?php echo e($service->country ?? ''); ?></td>
                                <td><?php echo e($service->city ?? ''); ?></td>
                                <td><?php echo e($service->work_phone ?? ''); ?></td>
                                <td><?php echo e($service->contact_name ?? ''); ?></td>
                                <td><?php echo $service->action_buttons; ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="text-center">No services found</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
</section>


<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="<?php echo e(asset('js/bootstrap-tables.js')); ?>"></script>

    <script>
       // const ctx = document.getElementById('chart');

		var currentDate = new Date();
 var monthNames = [
            "January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
        ];
        var currentMonth = monthNames[currentDate.getMonth()];
        var previousMonths = [];
        for (let i = 4; i >= 0; i--) {

            var previousMonthIndex = currentDate.getMonth() - i;
            previousMonths.push(monthNames[previousMonthIndex < 0 ? 11 : previousMonthIndex]);


        }
		var day = currentDate.getDate();
	
		const ctx = document.querySelectorAll('.chart');
		
 for (var i = 0; i < ctx.length; i++) {
	 var value1 = document.getElementById("value1"+ i).value;
	 var value2 = document.getElementById("value2"+ i).value;
	 var value3 = document.getElementById("value3"+ i).value;
	 var value4 = document.getElementById("value4"+ i).value;
	 var value5 = document.getElementById("value5"+ i).value;

        new Chart(ctx[i], {
            type: "line",
            data: {
                labels: previousMonths,
                datasets: [
                    {
                        label: "Amount",
                        data: [value1, value2, value3, value4, value5],
                        borderWidth: 1,
                        borderColor: "#159a9c",
                        pointRadius: 0,
						backgroundColor: '#159a9c',
                    },
                ],
            },
            options: {
                plugins: {
                    legend: {
                        display: false,
                    },
                },
                scales: {
                    x: {
                        display: true
                    },
                    y: {
                        beginAtZero: true,
                        display: false
                    },
                },
            },
        });
	 
 }
		
		
 // Initialize bootstrap table
 initializeBootstrapTable('search-table');

 let service = "All";
 $('#service-select').on('change', function(){
            var tmp = this.value;
            if(tmp === 'Bus Company') { tmp = 'Transfer';}
			service = tmp;

			// Filter table rows based on service type
			var table = document.getElementById('search-table');
			var rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

			for(var i = 0; i < rows.length; i++) {
				if(tmp === 'All') {
					rows[i].style.display = '';
				} else {
					// You can add service type filtering logic here if needed
					rows[i].style.display = '';
				}
			}
		});

    </script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('scaffold-interface.layouts.tabler-app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xamppp\htdocs\dev-eetstravel\resources\views/reporting/index.blade.php ENDPATH**/ ?>