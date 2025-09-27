<?php $__env->startSection('content'); ?>
	<?php echo $__env->make('layouts.title',
   ['title' => 'Users', 'sub_title' => 'Users List',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Users', 'icon' => 'user', 'route' => null]]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
	<section class="content">
	<div class="box box-primary">
	<div class="box-body">
        <?php if(Session::has('message')): ?>
            <div class="alert alert-danger"><center><?php echo e(Session::get('message')); ?></center></div>
        <?php endif; ?>
		<a href="<?php echo e(url('/users/create')); ?>" class="btn btn-success"><i class="fa fa-plus fa-md" aria-hidden="true"></i> <?php echo e(trans('main.New')); ?></a>
		<br><br>
		<table class = "table table-hover">
			<thead>
			<tr>
				<th><?php echo e(trans('main.Name')); ?></th>
				<th><?php echo e(trans('main.Email')); ?></th>
				<th><?php echo e(trans('main.Roles')); ?></th>
				<th><?php echo e(trans('main.Permissions')); ?></th>
				<th style="width: 100px"><?php echo e(trans('main.Actions')); ?></th>
			</tr>
			</thead>
		<tbody>
			<?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			<tr>
				<td><?php echo e($user->name); ?></td>
				<td><?php echo e($user->email); ?></td>
				<td>
				<?php if(!empty($user->roles)): ?>
					<?php $__currentLoopData = $user->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<small class = 'label bg-blue'><?php echo e($role->name); ?></small><hr style="margin: 2px">
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				<?php else: ?>
					<small class = 'label bg-red'><?php echo e(trans('main.NoRoles')); ?></small>
				<?php endif; ?>
				</td>
				<td>
				<?php if(!empty($user->permissions)): ?>
					<?php $__currentLoopData = $user->permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<p style="display: inline-block;"><small class = 'label bg-orange'><?php echo e($permission->alias); ?></small></p>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				<?php else: ?>
					<small class = 'label bg-red'><?php echo e(trans('main.NoPermissions')); ?></small>
				<?php endif; ?>
				</td>
				<td style="width: 100px">
					<a href="<?php echo e(url('/users/'.$user->id.'/edit')); ?>" class = 'btn btn-primary btn-sm'><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
					
			
					<form action="<?php echo e(route('users.destroy', $user->id)); ?>" method="POST" style="display: inline-block;">
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

<?php echo $__env->make('scaffold-interface.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/scaffold-interface/users/index.blade.php ENDPATH**/ ?>