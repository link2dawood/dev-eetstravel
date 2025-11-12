
<?php $__env->startSection('content'); ?>
	<?php echo $__env->make('layouts.title', [
		'title' => 'Roles',
		'sub_title' => 'Roles List',
		'breadcrumbs' => [
			['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
			['title' => 'Roles', 'icon' => 'user', 'route' => null]
		]
	], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
	<section class="content">
		<div class="card">
			<div class="card-body">
				<div class="mb-3">
					<a href="<?php echo e(url('roles/create')); ?>" class="btn btn-primary">
						<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
							<path stroke="none" d="M0 0h24v24H0z" fill="none"/>
							<path d="M12 5l0 14" />
							<path d="M5 12l14 0" />
						</svg>
						<?php echo e(trans('main.New')); ?>

					</a>
				</div>

				<div class="table-responsive">
					<table class="table table-vcenter card-table">
						<thead>
						<tr>
							<th><?php echo e(trans('main.Role')); ?></th>
							<th><?php echo e(trans('main.Permissions')); ?></th>
							<th class="w-1"><?php echo e(trans('main.Actions')); ?></th>
						</tr>
						</thead>
						<tbody>
							<?php $__empty_1 = true; $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
							<tr>
								<td>
									<span class="badge bg-blue-lt"><?php echo e($role->name); ?></span>
								</td>
								<td>
									<?php if(!empty($role->permissions)): ?>
										<div class="badges-list">
											<?php $__currentLoopData = $role->permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<span class="badge bg-orange text-orange-fg"><?php echo e($permission->alias); ?></span>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
										</div>
									<?php else: ?>
										<span class="badge bg-secondary"><?php echo e(trans('main.NoPermissions')); ?></span>
									<?php endif; ?>
								</td>
								<td>
									<div class="btn-list flex-nowrap">
										<!-- EDIT BUTTON -->
										<a href="<?php echo e(url('/roles')); ?>/<?php echo e($role->id); ?>/edit" class="btn btn-icon btn-ghost-warning" title="Edit">
											<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
												<path stroke="none" d="M0 0h24v24H0z" fill="none"/>
												<path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
												<path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
												<path d="M16 5l3 3" />
											</svg>
										</a>

										<!-- DELETE BUTTON -->
										<form action="<?php echo e(route('roles.destroy', $role->id)); ?>" method="POST" style="display: inline-block;">
											<?php echo csrf_field(); ?>
											<?php echo method_field('DELETE'); ?>
											<button type="submit" class="btn btn-icon btn-ghost-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this role?')">
												<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
													<path stroke="none" d="M0 0h24v24H0z" fill="none"/>
													<path d="M4 7l16 0" />
													<path d="M10 11l0 6" />
													<path d="M14 11l0 6" />
													<path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
													<path d="M9 7v-1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v1" />
												</svg>
											</button>
										</form>
									</div>
								</td>
							</tr>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
							<tr>
								<td colspan="3" class="text-center text-secondary py-4">
									No roles found
								</td>
							</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</section>
<?php $__env->stopSection(); ?>

<style>
	.badges-list {
		display: flex;
		flex-wrap: wrap;
		gap: 6px;
	}

	.badges-list .badge {
		margin: 0;
	}
</style>
<?php echo $__env->make('scaffold-interface.layouts.tabler-app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xamppp\htdocs\dev-eetstravel\resources\views/scaffold-interface/roles/index.blade.php ENDPATH**/ ?>