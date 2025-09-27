<?php $__env->startSection('content'); ?>
	<?php echo $__env->make('layouts.title',
   ['title' => 'Roles', 'sub_title' => 'Roles List',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Roles', 'icon' => 'user', 'route' => null]]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<section class="content">
	<div class="box box-primary">
		<div class="box-body">
			<a href="<?php echo e(url('roles/create')); ?>" class = "btn btn-success"><i class="fa fa-plus fa-md" aria-hidden="true"></i> <?php echo e(trans('main.New')); ?></a>
			<span id="help" class="btn btn-box-tool pull-right"><i class="fa fa-question-circle " aria-hidden="true"></i>
				<?php echo $__env->make('legend.roles_legend', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </span>
			<br>
			<br>
			<table class="table table-striped">
				<thead>
				<tr>
					<th><?php echo e(trans('main.Role')); ?></th>
					<th><?php echo e(trans('main.Permissions')); ?></th>
					<th style="width: 100px"><?php echo e(trans('main.Actions')); ?></th>
				</tr>
				</thead>
				<tbody>
					<?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<tr>
						<td><?php echo e($role->name); ?></td>
						<td>
							<?php if(!empty($role->permissions)): ?>
								<?php $__currentLoopData = $role->permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<p style="display: inline-block"><small class = 'label bg-orange'><?php echo e($permission->alias); ?></small></p>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							<?php else: ?>
								<small class = 'label bg-red'><?php echo e(trans('main.NoPermissions')); ?></small>
							<?php endif; ?>
						</td>
						<td style="width: 100px">
							<a href="<?php echo e(url('/roles')); ?>/<?php echo e($role->id); ?>/edit" class = "btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
<form action="<?php echo e(route('roles.destroy', $role->id)); ?>" method="POST" style="display: inline-block;">
    <?php echo csrf_field(); ?>
    <?php echo method_field('DELETE'); ?>
    <button type="submit" class="btn btn-danger btn-sm">
        <i class="fa fa-trash-o"></i>
    </button>
</form>
						</td>
					</tr>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				</tbody>
			</table>
		</div>
	</div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('scaffold-interface.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/scaffold-interface/roles/index.blade.php ENDPATH**/ ?>