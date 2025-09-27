<?php $__env->startSection('title', 'Settings'); ?>
<?php $__env->startSection('content'); ?>
	<?php echo $__env->make('layouts.title',
        ['title' => 'Settings', 'sub_title' => 'Settings List', 'breadcrumbs' => [
           ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
           ['title' => 'Settings', 'icon' => null, 'route' => null]]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
	<section class="content">
		<div class="box box-primary">
			<div class="box-body">
				 <a href="<?php echo e(route('settings.create')); ?>" class="btn btn-success">New</a> 
				<br><br>
				<div class="mb-3">
					<div class="row">
						<div class="col-md-6">
							<input type="text" id="settings-search" class="form-control" placeholder="Search settings..." onkeyup="filterTable('settings-table', this.value)">
						</div>
						<div class="col-md-6 text-right">
							<button class="btn btn-success btn-sm" onclick="exportTableToCSV('settings-table', 'settings_export.csv')">
								<i class="fa fa-download"></i> Export CSV
							</button>
						</div>
					</div>
				</div>
				<div class="table-responsive">
					<table id="settings-table" class="table table-striped table-bordered table-hover bootstrap-table">
					<thead>
						<tr>
							<th><?php echo trans('main.Description'); ?></th>
							<th><?php echo trans('main.Value'); ?></th>
							<th><?php echo trans('main.Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php $__currentLoopData = $settings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $setting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<tr>
						<td><?php echo e(@$setting->description); ?></td>
						<td><?php echo e(@$setting->value); ?></td>
						<td style="width: 100px">
        <a href="<?php echo e(route('settings.edit', ['setting' => $setting->id])); ?>" class="btn btn-primary btn-sm">
            <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
        </a>
<a href="<?php echo e(route('settings.destroy', $setting->id)); ?>"
   class="btn btn-danger btn-sm"
   onclick="event.preventDefault(); document.getElementById('delete-form-<?php echo e($setting->id); ?>').submit();">
    <i class="fa fa-trash-o" aria-hidden="true"></i>
</a>

<form id="delete-form-<?php echo e($setting->id); ?>" action="<?php echo e(route('settings.destroy', $setting->id)); ?>" method="POST" style="display: none;">
    <?php echo csrf_field(); ?>
    <?php echo method_field('DELETE'); ?>
</form>


    </td>
						<tr>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

					</tbody>
					
				</table>
				</div>
			</div>
		</div>
	</section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('js/bootstrap-tables.js')); ?>"></script>
<script>
$(document).ready(function() {
    initializeBootstrapTable('settings-table');
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('scaffold-interface.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/settings/index.blade.php ENDPATH**/ ?>