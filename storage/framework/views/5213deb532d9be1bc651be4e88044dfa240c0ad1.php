<?php $__env->startSection('title','Index'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('layouts.title',
   ['title' => 'Tours', 'sub_title' => 'Tours List',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Tours', 'icon' => 'suitcase', 'route' => null]]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div>
					<div id = "tour_create">
                    <?php echo \App\Helper\PermissionHelper::getCreateButton(route('tour.create'), \App\Tour::class); ?>

					</div>
                    <span id="help" class="btn btn-box-tool pull-right"><i class="fa fa-question-circle" aria-hidden="true"></i>
                        <?php echo $__env->make('legend.tour_legend', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </span>
                </div>
				<br>
				<div id="fixed-scroll" class="nav-tabs-custom">
                    <ul class="nav nav-tabs" role='tablist'>

                        <li role='presentation' class="active"><a href="#tour_tab" aria-controls='tour_tab' role='tab'
                                                   data-toggle='tab' ><?php echo e(trans('main.Tours')); ?></a></li>
						<li role='presentation' ><a href="#client_tour_tab" aria-controls='client_tour_tab'
                                                                  role='tab' data-toggle='tab' ><?php echo e(trans('Requested Tours')); ?></a></li>
						<li role='presentation' ><a href="#monthly_chart_tab" aria-controls='monthly_chart_tab'
                                                                  role='tab' data-toggle='tab' ><?php echo e(trans('Monthly Chart')); ?></a></li>
						<li role='presentation' ><a href="#archieve_tours_tab" aria-controls='monthly_chart_tab'
                                                                  role='tab' data-toggle='tab' ><?php echo e(trans('Archived Tours')); ?></a></li>
                    </ul>
                </div>
				<div class="tab-content">
					<div role='tabpanel' class="tab-pane fade in active" id="tour_tab">
				<div class="toggle" style="float:right">
					<select id="filterDropdown" class="form-control">
						<option value="">All</option>
						<option value="quotations">Quotations</option>
						<option value="go_ahead">Go Ahead</option>
					</select>

            	</div>
                <?php if(session('message_buses')): ?>
                    <div class="alert alert-info col-md-12" style="text-align: center;">
                        <?php echo e(session('message_buses')); ?>

                    </div>
                <?php endif; ?>
                <br>
                <br>

                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" id="tour-search" class="form-control" placeholder="Search tours..." onkeyup="filterTable('tour-table', this.value)">
                        </div>
                        <div class="col-md-6 text-right">
                            <button class="btn btn-success btn-sm" onclick="exportTableToCSV('tour-table', 'tours_export.csv')">
                                <i class="fa fa-download"></i> Export CSV
                            </button>
                        </div>
                    </div>
                </div>
				<div class="table-responsive">
                <table id="tour-table" class="table table-striped table-bordered table-hover" style='background:#fff; width: 100%;'>
                    <thead>
                      <tr>
                        <th style='width: 30px!important;'>ID</th>
                        <th><?php echo trans('main.Name'); ?></th>
                        <th><?php echo trans('main.DepDate'); ?></th>
						<th><?php echo trans('Responsible Users'); ?></th>
						 <th><?php echo trans('Assigned Users'); ?></th>
                        <th><?php echo trans('main.Status'); ?></th>
                        <th><?php echo trans('main.ExternalName'); ?></th>
                        <th class="actions-button" style="width:140px; text-align: center;"><?php echo trans('main.Actions'); ?></th>
                      </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $tours; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tour): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr style="background: <?php echo e($tour->getRowBackgroundColor()); ?>; cursor: pointer;"
                            onclick="window.location='<?php echo e(route('tour.show', ['tour' => $tour->id])); ?>'"
                            title="Click to view tour details">
                            <td><?php echo e($tour->id); ?></td>
                            <td><?php echo e($tour->name); ?></td>
                            <td><?php echo e($tour->departure_date ? \Carbon\Carbon::parse($tour->departure_date)->format('Y-m-d') : ''); ?></td>
                            <td><?php echo e($tour->responsible_user_names ?? ''); ?></td>
                            <td><?php echo e($tour->assigned_user_names ?? ''); ?></td>
                            <td>
                                <span class="label" style="background-color: <?php echo e($tour->getStatusColor()); ?>">
                                    <?php echo e($tour->getStatusName()); ?>

                                </span>
                            </td>
                            <td><?php echo e($tour->external_name); ?></td>
                            <td class="text-center" onclick="event.stopPropagation();">
                                <?php echo $__env->make('component.action_buttons', ['item' => $tour, 'routePrefix' => 'tour'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            </div>
			<div role='tabpanel' class="tab-pane fade" id="client_tour_tab">
				<div class="table-responsive">
				<table id="client-tour-table" class="table table-striped table-bordered table-hover" style='background:#fff; width: 100%;'>
					<thead>
                      <tr>
                        <th style='width: 30px!important;'>ID</th>
                        <th><?php echo trans('main.Name'); ?></th>
						<th><?php echo trans('Client Name'); ?></th>
                        <th><?php echo trans('main.DepDate'); ?></th>
                        <th><?php echo trans('main.Status'); ?></th>
                        <th><?php echo trans('main.ExternalName'); ?></th>
                        <th class="actions-button" style="width:140px; text-align: center;"><?php echo trans('main.Actions'); ?></th>
                      </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $clientTours; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tour): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr style="background: <?php echo e($tour->getRowBackgroundColor()); ?>; cursor: pointer;"
                            onclick="window.location='<?php echo e(route('tour.show', ['tour' => $tour->id])); ?>'"
                            title="Click to view tour details">
                            <td><?php echo e($tour->id); ?></td>
                            <td><?php echo e($tour->name); ?></td>
                            <td><?php echo e($tour->client_name ?? ''); ?></td>
                            <td><?php echo e($tour->departure_date ? \Carbon\Carbon::parse($tour->departure_date)->format('Y-m-d') : ''); ?></td>
                            <td>
                                <span class="label" style="background-color: <?php echo e($tour->getStatusColor()); ?>">
                                    <?php echo e($tour->getStatusName()); ?>

                                </span>
                            </td>
                            <td><?php echo e($tour->external_name); ?></td>
                            <td class="text-center" onclick="event.stopPropagation();">
                                <?php echo $__env->make('component.action_buttons', ['item' => $tour, 'routePrefix' => 'tour'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
				</table>
			</div>
			</div>
			<div role='tabpanel' class="tab-pane fade" id="monthly_chart_tab">
				<div class="toggle" style="float:right">
					<select id="year-filter" class="form-control">
						<option value="">All Years</option>
						<?php $__currentLoopData = $years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<option value="<?php echo e($year); ?>"><?php echo e($year); ?></option>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					</select>
				</div>
				<div class="toggle" style="float:right; margin:0 20px 0 0">
					<select id="month-filter" class="form-control">
						<option value="">All Months</option>
						<?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<option value="<?php echo e($key); ?>"><?php echo e($month); ?></option>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					</select>
				</div>
				<h1>On Going Projects</h1>
				<div class="table-responsive">
				<table id="monthly-chart-table" class="table table-striped table-bordered table-hover" style='background:#fff; width: 100%;'>
					<thead>
                      <tr>
                        <th style='width: 30px!important;'>ID</th>
                        <th><?php echo trans('main.Name'); ?></th>
						  <th><?php echo trans('Responsible Users'); ?></th>
                        <th><?php echo trans('main.Status'); ?></th>
                        <th><?php echo trans('main.ExternalName'); ?></th>
                        <th class="actions-button" style="width:30px; text-align: center;"><?php echo trans('main.Actions'); ?></th>
                      </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $monthlyChartTours; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tour): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr style="background: <?php echo e($tour->getRowBackgroundColor()); ?>; cursor: pointer;"
                            onclick="window.location='<?php echo e(route('tour.show', ['tour' => $tour->id])); ?>'"
                            title="Click to view tour details">
                            <td><?php echo e($tour->id); ?></td>
                            <td><?php echo e($tour->name); ?></td>
                            <td><?php echo e($tour->responsible_user_names ?? ''); ?></td>
                            <td>
                                <span class="label" style="background-color: <?php echo e($tour->getStatusColor()); ?>">
                                    <?php echo e($tour->getStatusName()); ?>

                                </span>
                            </td>
                            <td><?php echo e($tour->external_name); ?></td>
                            <td class="text-center" onclick="event.stopPropagation();">
                                <?php echo $__env->make('component.action_buttons', ['item' => $tour, 'routePrefix' => 'tour'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
				</table>
					</div>
				<h1>Cancelled Projects</h1>
					<div class="table-responsive">
				<table id="cancelled-chart-table" class="table table-striped table-bordered table-hover" style='background:#fff; width: 100%;'>
					<thead>
                      <tr>
                        <th style='width: 30px!important;'>ID</th>
                        <th><?php echo trans('main.Name'); ?></th>
						 <th><?php echo trans('Responsible Users'); ?></th>
                        <th><?php echo trans('main.Status'); ?></th>
                        <th><?php echo trans('main.ExternalName'); ?></th>
                        <th class="actions-button" style="width:30px; text-align: center;"><?php echo trans('main.Actions'); ?></th>
                      </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $cancelledChartTours; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tour): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr style="background: <?php echo e($tour->getRowBackgroundColor()); ?>; cursor: pointer;"
                            onclick="window.location='<?php echo e(route('tour.show', ['tour' => $tour->id])); ?>'"
                            title="Click to view tour details">
                            <td><?php echo e($tour->id); ?></td>
                            <td><?php echo e($tour->name); ?></td>
                            <td><?php echo e($tour->responsible_user_names ?? ''); ?></td>
                            <td>
                                <span class="label" style="background-color: <?php echo e($tour->getStatusColor()); ?>">
                                    <?php echo e($tour->getStatusName()); ?>

                                </span>
                            </td>
                            <td><?php echo e($tour->external_name); ?></td>
                            <td class="text-center" onclick="event.stopPropagation();">
                                <?php echo $__env->make('component.action_buttons', ['item' => $tour, 'routePrefix' => 'tour'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
				</table>
						</div>

			</div>
			<div role='tabpanel' class="tab-pane fade" id="archieve_tours_tab">
				<div class="table-responsive">
				<table id="archieve-tour-table" class="table table-striped table-bordered table-hover" style='background:#fff; width: 100%;'>
					<thead>
                      <tr>
                        <th style='width: 30px!important;'>ID</th>
                        <th><?php echo trans('main.Name'); ?></th>
						 <th><?php echo trans('Responsible Users'); ?></th>
                        <th><?php echo trans('main.DepDate'); ?></th>
                        <th><?php echo trans('main.Status'); ?></th>
                        <th><?php echo trans('main.ExternalName'); ?></th>
                        <th class="actions-button" style=" text-align: center;"><?php echo trans('main.Actions'); ?></th>
                      </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $archivedTours; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tour): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr style="background: <?php echo e($tour->getRowBackgroundColor()); ?>; cursor: pointer;"
                            onclick="window.location='<?php echo e(route('tour.show', ['tour' => $tour->id])); ?>'"
                            title="Click to view tour details">
                            <td><?php echo e($tour->id); ?></td>
                            <td><?php echo e($tour->name); ?></td>
                            <td><?php echo e($tour->responsible_user_names ?? ''); ?></td>
                            <td><?php echo e($tour->departure_date ? \Carbon\Carbon::parse($tour->departure_date)->format('Y-m-d') : ''); ?></td>
                            <td>
                                <span class="label" style="background-color: <?php echo e($tour->getStatusColor()); ?>">
                                    <?php echo e($tour->getStatusName()); ?>

                                </span>
                            </td>
                            <td><?php echo e($tour->external_name); ?></td>
                            <td class="text-center" onclick="event.stopPropagation();">
                                <?php echo $__env->make('component.action_buttons', ['item' => $tour, 'routePrefix' => 'tour'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
				</table>
				</div>
			</div>
        </div>

	</div>

</div>

    </section>
    
<div class="modal fade" id="tour-clone-modal" tabindex="-1" role='dialog' aria-labelledby='tour-clone-label'>
    <div class="modal-dialog" role='document'>
        <div class="modal-content">
            <div class="box box-body" style="border-top: none">
                <div class="alert alert-info block-error" style="text-align: center; display: none;">

                </div>

                <form id="tour-clone-modal-form">
                    <div class="form-group">
                            <label for="departure_date"><?php echo trans('main.DepartureDate'); ?></label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                        <?php echo Form::text('departure_date', '', ['class' => 'form-control pull-right datepicker', 'id' => 'departure_date', 'autocomplete' => 'off']); ?>

                        </div>
                    </div>
                    <button type="submit" class="btn btn-success pre-loader-func" id="clone_tour_send"><?php echo trans('main.Submit'); ?></button>
                </form>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" tabindex="-1" id="error_tour">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="form_confirmed_hotel">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss='modal' aria-label="Close"><span aria-hidden='true'>&times;</span></button>
                        <h4 class="modal-title"><?php echo trans('main.Warning'); ?>!</h4>
                    </div>
                    <div class="modal-body">
                        <h3 class="error_tour_message"></h3>
                    </div>
                    <div class="modal-footer">
                        <div class="btn-send-confirmed_hotel">
                            <button type="reset" class="btn btn-success modal-close" data-dismiss="modal">Ok</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
</div>

    <span id="permission" data-permission="<?php echo e(\App\Helper\PermissionHelper::checkPermission('tour.edit')); ?>"></span>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('scaffold-interface.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/tour/index.blade.php ENDPATH**/ ?>