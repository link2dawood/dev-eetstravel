<?php $__env->startSection('content'); ?>
	<?php echo $__env->make('layouts.title',
   ['title' => 'User', 'sub_title' => 'User Edit',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Users', 'icon' => 'user', 'route' => url('users')],
   ['title' => 'Edit', 'route' => null]]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
   <style>
	.select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    background: #f5f5f5!important;
    border: none;
    border-right: 1px solid #aaa;
    border-top-left-radius: 4px;
    border-bottom-left-radius: 4px;
    color: #999;
    cursor: pointer;
    font-size: 1em;
    font-weight: bold;
    padding: 0 4px;
    position: relative!important;
    left: -5px!important;
    top: 0!important;
}
.select2-container--default .select2-search--inline .select2-search__field {
	position: absolute;
}
   </style>
<section class="content">
	<div class="box box-primary">
		<div class="box box-body border_top_none">
			<?php if(count($errors) > 0): ?>
				<br>
				<div class="alert alert-danger">
					<ul>
						<?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<li><?php echo e($error); ?></li>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					</ul>
				</div>
			<?php endif; ?>
			<form action="<?php echo e(url('/users/'.$user->id)); ?>" method="post" enctype="multipart/form-data">
				<div class="row">
					<div class="col-md-12">
						<div class="margin_button">
							<a href="javascript:history.back()">
								<button class='btn btn-primary back_btn' type="button"><?php echo e(trans('main.Back')); ?></button>
							</a>
							<button class='btn btn-success' type='submit'><?php echo e(trans('main.Save')); ?></button>
						</div>
					</div>
				</div>
				<?php echo csrf_field(); ?>

				<input type="hidden" name = "user_id" value = "<?php echo e($user->id); ?>">
				<div class="form-group">
					<label for=""><?php echo e(trans('main.Email')); ?></label>
					<input type="text" name = "email" value = "<?php echo e($errors != null && count($errors) > 0 ? old('email') : $user->email); ?>" class = "form-control">
				</div>
				<div class="form-group">
					<label for=""><?php echo e(trans('main.Name')); ?></label>
					<input type="text" name = "name" value = "<?php echo e($errors != null && count($errors) > 0 ? old('name') : $user->name); ?>" class = "form-control">
				</div>
				<div class="form-group">
					<label for=""><?php echo e(trans('main.Password')); ?></label>
					<input type="password" name = "password" class = "form-control" placeholder = "password">
				</div>
                <div class="form-group">
                    <label for="">Avatar</label>
                    <div style="margin-bottom: 10px;">
                        <?php if($user->avatar): ?>
                            <img src="<?php echo e(asset('storage/' . $user->avatar)); ?>" alt="Current Avatar" style="max-width: 100px; height: auto;">
                        <?php elseif($user->avatar): ?>
                            <img src="<?php echo e(asset('storage/' . $user->avatar)); ?>" alt="Current Avatar" style="max-width: 100px; height: auto;">
                        <?php else: ?>
                            <img src="<?php echo e(asset('images/default-avatar.png')); ?>" alt="Default Avatar" style="max-width: 100px; height: auto;">
                        <?php endif; ?>
                    </div>
                    <input id="avatar" name="avatar" type="file" class="file" data-show-upload="false" accept="image/*">
                    <small class="form-text text-muted">Upload a new avatar image (optional)</small>
                </div>
				<button class = "btn btn-success" type="submit"><?php echo e(trans('main.Save')); ?></button>
				<a href="<?php echo e(\App\Helper\AdminHelper::getBackButton(route('users.index'))); ?>">
					<button class='btn btn-warning' type='button'><?php echo e(trans('main.Cancel')); ?></button>
				</a>
			</form>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6">
			<div class="box box-primary">
				<div class="box-header">
					<h3><?php echo e($user->name); ?> <?php echo e(trans('main.Roles')); ?></h3>
				</div>
				<div class="box-body">
					<form action="<?php echo e(url('users/addRole')); ?>" method = "post">
						<?php echo csrf_field(); ?>

						<input type="hidden" name = "user_id" value = "<?php echo e($user->id); ?>">
						<div class="form-group">
							<select name="role_name" id="" class = "form-control">
								<?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<option value="<?php echo e($role); ?>"><?php echo e($role); ?></option>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</select>
						</div>
						<div class="form-group">
							<button class = 'btn btn-primary'><?php echo e(trans('main.Addrole')); ?></button>
						</div>
					</form>
					<table class = 'table'>
						<thead>
							<th><?php echo e(trans('main.Role')); ?></th>
							<th><?php echo e(trans('main.Action')); ?></th>
						</thead>
						<tbody>
							<?php $__currentLoopData = $userRoles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<tr>
								<td><?php echo e($role); ?></td>
								<td>
									<form action="<?php echo e(route('user.remove_role')); ?>" method="POST">
										<?php echo e(csrf_field()); ?>

										<input type="text" hidden name="user_id" value="<?php echo e($user->id); ?>">
										<input type="text" hidden name="role" value="<?php echo e($role); ?>">
										<button type="submit" class = "btn btn-danger btn-sm">
											<i class="fa fa-trash-o" aria-hidden="true"></i>
										</button>
									</form>
								</td>
							</tr>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="box box-primary">
				<div class="box-header">
					<h3><?php echo e($user->name); ?> <?php echo e(trans('main.Permissions')); ?></h3>
				</div>
				<div class="box-body">
					<form action="<?php echo e(url('users/addPermission')); ?>" method = "post">
						<?php echo csrf_field(); ?>

						<input type="hidden" name = "user_id" value = "<?php echo e($user->id); ?>">
						<div class="form-group">
							<select name="permission_name[]" id="" class = "js-state form-control select22"
									multiple="multiple">
								<?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<option value="<?php echo e($key); ?>"><?php echo e($permission); ?></option>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</select>
						</div>
						<div class="form-group">
							<button class = 'btn btn-primary'><?php echo e(trans('main.Addpermission')); ?></button>
						</div>
					</form>
					<table class = 'table'>
						<thead>
							<th><?php echo e(trans('main.Permission')); ?></th>
							<th><?php echo e(trans('main.Action')); ?></th>
						</thead>
						<tbody>
							<?php $__currentLoopData = $userPermissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<tr>
								<td><?php echo e($permission); ?></td>
								<td><a href="<?php echo e(url('users/removePermission')); ?>/<?php echo e($user->id); ?>/<?php echo e($key); ?>" class = "btn btn-danger btn-sm"><i class="fa fa-trash-o" aria-hidden="true"></i></a></td>
							</tr>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</section>
<?php $__env->stopSection(); ?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
	$(document).ready(function() {
		$('.select22').select2({
			placeholder: "Select permissions",
			allowClear: true
		});
	});
</script>
<?php echo $__env->make('scaffold-interface.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/scaffold-interface/users/edit.blade.php ENDPATH**/ ?>