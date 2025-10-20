<div class="panel">
    <div class="panel-body">
        <table class="table table-striped table-bordered table-hover" style='background:#fff; width: 99%; table-layout: fixed'>
            <thead>
                <tr>
                    <th style='width: 30px!important;'>ID</th>
                    <th><?php echo trans('main.Name'); ?></th>
                    <th><?php echo trans('main.DepDate'); ?></th>
                    <th><?php echo trans('main.RetDate'); ?></th>
                    <th><?php echo trans('main.CountryBegin'); ?></th>
                    <th><?php echo trans('main.CityBegin'); ?></th>
                    <th><?php echo trans('main.Status'); ?></th>
                    <th><?php echo trans('main.ExternalName'); ?></th>
                    <th style="width:150px!important; text-align: center;"><?php echo trans('main.Actions'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $tours; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tour): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($tour->id); ?></td>
                        <td class="touredit-name"><?php echo e($tour->name); ?></td>
                        <td class="touredit-departure_date"><?php echo e($tour->departure_date); ?></td>
                        <td class="touredit-retirement_date"><?php echo e($tour->retirement_date); ?></td>
                        <td class="touredit-country_begin"><?php echo e($tour->country_begin); ?></td>
                        <td class="touredit-city_begin"><?php echo e($tour->city_begin); ?></td>
                        <td class="touredit-status"><?php echo $tour->status_display; ?></td>
                        <td class="touredit-status"><?php echo e($tour->external_name); ?></td>
                        <td style="text-align: center;"><?php echo $tour->action_buttons; ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php if(empty($tours)): ?>
                    <tr>
                        <td colspan="9" class="text-center">No tours found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="tour-clone-modal" tabindex="-1" role='dialog' aria-labelledby='tour-clone-label'>
    <div class="modal-dialog" role='document'>
        <div class="modal-content">
            <div class="modal-body">
                <form id="tour-clone-modal-form">
                    <div class="form-group">
                        <label for="departure_date"><?php echo trans('main.DepartureDate'); ?></label>
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <?php echo Form::text('departure_date', '', ['class' => 'form-control pull-right datepicker', 'id' => 'departure_date']); ?>

                        </div>
                    </div>
                    <button type="submit" class="btn btn-success"><?php echo trans('main.Submit'); ?></button>
                </form>
            </div>
        </div>
    </div>
</div>
<span id="permission" data-permission="<?php echo e(\App\Helper\PermissionHelper::checkPermission('tour.edit')); ?>"></span>
<style>

</style>
<?php /**PATH /var/www/html/resources/views/component/list_tours_for_profile.blade.php ENDPATH**/ ?>