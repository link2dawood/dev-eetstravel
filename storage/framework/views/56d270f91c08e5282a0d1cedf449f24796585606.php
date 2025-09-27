<?php if(count($packages) > 0): ?>
<div class="col-md-12">
	<table id="history-table" class="table table-condensed table-bordered table-hover">
		<thead>
			<tr>
				<th><?php echo trans('main.TourName'); ?></th>
				<th><?php echo trans('main.PackageName'); ?></th>
				<th><?php echo trans('main.PriceperPerson'); ?></th>
				<th><?php echo trans('main.TotalAmount'); ?></th>
				<th>Pax</th>
				<th><?php echo trans('main.PaxFree'); ?></th>
				<th><?php echo trans('main.TimeFrom'); ?></th>
				<th><?php echo trans('main.TimeTo'); ?></th>
				<th><?php echo trans('main.AssignedUser'); ?></th>
				<th><?php echo trans('main.Created'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php $__currentLoopData = $packages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $package): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			<tr>
				<td >

                    <?php if($package->tour && !$package->tour->deleted): ?>
                        <?php if($package->tour->isMyTour()): ?>
                            <a href="<?php echo e(route('tour.show', ['tour' => $package->tour->id])); ?>">
                            <?php echo e($package->tour->name); ?> </a>
                        <?php else: ?>
                            <?php echo e($package->tour->name); ?> 
                        <?php endif; ?>
					<?php else: ?>
						<?php echo e($package->tour['name']); ?> (deleted)
					<?php endif; ?>
				</td>
				<td><?php echo e($package->name); ?></td>
				<td><?php echo e(round($package->total_amount, 2)); ?></td>
				<td><?php echo e(round($package->getTotalAmountTourPackage(), 2)); ?></td>
				<td><?php echo e($package->pax); ?></td>
				<td><?php echo e($package->pax_free); ?></td>
				<td><?php echo e($package->time_from); ?></td>
				<td><?php echo e($package->time_to); ?></td>
				
				<td>
					<?php if($package->tour): ?>
					<?php echo e(@$package->tour->showAllAssignedName()); ?>

					<?php endif; ?>
				</td>
				<td><?php echo e($package->created_at); ?></td>
			</tr>
			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
		</tbody>
	</table>
</div>
<?php else: ?>
	<div class="col-md-12">
		<h3><?php echo trans('main.Historyisempty'); ?></h3>
	</div>
<?php endif; ?>
<script>
    $(function() {
        $('#history-table tr').click(function() {
            var link = $(this).find('a').attr('href');
                if (link){
                    window.location.href = link;
                }
        });
    });
</script><?php /**PATH /var/www/html/resources/views/component/services_history.blade.php ENDPATH**/ ?>