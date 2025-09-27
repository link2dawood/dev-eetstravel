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
		<div class="box box-body table-responsive" style="border-top: none">
                    <table id="search-table" class="table table-striped table-bordered table-hover" style="width: 100%;">
                        <thead>
                        <tr>
                            <th><?php echo trans('main.Name'); ?></th>
                            <th><?php echo trans('main.Address'); ?></th>
                            <th><?php echo trans('main.Country'); ?></th>
                            <th><?php echo trans('main.City'); ?></th>
                            <th><?php echo trans('main.Phone'); ?></th>
                            <th><?php echo trans('main.ContactName'); ?></th>
                            <th><?php echo trans('Actions'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $__currentLoopData = $servicesData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($service->nameService ?? $service->name); ?></td>
                                <td><?php echo e($service->address_first ?? ''); ?></td>
                                <td><?php echo e($service->country ?? ''); ?></td>
                                <td><?php echo e($service->city ?? ''); ?></td>
                                <td><?php echo e($service->work_phone ?? ''); ?></td>
                                <td><?php echo e($service->contact_name ?? ''); ?></td>
                                <td><?php echo $service->action_buttons; ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
</section>


<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
		
		
 let service = "All";
 $('#service-select').on('change', function(){
   
            //$(this).attr('disabled', true);
            var service_select = $(this);
            
            var tmp = this.value;
            if(tmp === 'Bus Company') { tmp = 'Transfer';}
			service = tmp;
			rate = '';
			criterias = [];
            countryAlias = $('#country').val();
            searchName = $('#searchTextField').val();
            city_code = $('#city_code').val();
			$('#search-table').DataTable().destroy();
            generateTable(service_select);
                 
		});
        function generateTable(service_select = null){
		let table = $('#search-table').DataTable({
			dom: 	"<'row'<'col-sm-7'f><'col-sm-5 toRight'l>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            pageLength: 50,
            sort: false,
            "initComplete": function(settings, json) {
                if(service_select){
                    $(service_select).attr('disabled', false);
                }
            }
		});
    }
generateTable(null);

    </script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('scaffold-interface.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/reporting/index.blade.php ENDPATH**/ ?>