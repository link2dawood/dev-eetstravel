
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

            </table>
        </div>
        </div>
    </div>
</section>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('scaffold-interface.layouts.tabler-app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xamppp\htdocs\dev-eetstravel\resources\views/office/index.blade.php ENDPATH**/ ?>