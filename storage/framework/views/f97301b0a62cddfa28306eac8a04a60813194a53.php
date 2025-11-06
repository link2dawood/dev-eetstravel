

<?php $__env->startSection('title','Tours'); ?>

<?php $__env->startSection('post_styles'); ?>
<style>
    /* Responsive table enhancements */
    @media (max-width: 768px) {
        .page-title {
            font-size: 1.5rem;
        }
        
        .btn-list {
            flex-wrap: wrap;
        }
        
        .table-responsive {
            font-size: 0.875rem;
        }
    }
    
    /* Status badge styling */
    .status-dot {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-right: 6px;
    }
    
    /* Clickable row hover effect */
    .clickable-row {
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .clickable-row:hover {
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        transform: translateY(-1px);
    }
    
    /* Empty state styling */
    .empty {
        padding: 3rem 1rem;
    }
    
    .empty-icon {
        font-size: 3rem;
        color: var(--tblr-muted);
        margin-bottom: 1rem;
    }
    
    /* Action buttons in tables */
    .action-cell .btn-list {
        gap: 0.25rem;
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-xl">
    
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    Tour Management
                </div>
                <h2 class="page-title">
                    <i class="ti ti-plane me-2"></i>Tours
                </h2>
            </div>
            
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <?php echo $__env->make('legend.tour_legend', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    <?php echo \App\Helper\PermissionHelper::getCreateButton(route('tour.create'), \App\Tour::class, 'btn btn-primary'); ?>

                </div>
            </div>
        </div>
    </div>

    
    <?php if(session('message_buses')): ?>
        <div class="alert alert-info alert-dismissible" role="alert">
            <div class="d-flex">
                <div>
                    <i class="ti ti-info-circle me-2"></i>
                </div>
                <div class="flex-fill">
                    <?php echo e(session('message_buses')); ?>

                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    <?php endif; ?>

    
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a href="#tours-tab" class="nav-link active" data-bs-toggle="tab" aria-selected="true" role="tab">
                        <i class="ti ti-plane me-1"></i>Tours
                        <span class="badge bg-blue-lt ms-1"><?php echo e($tours->total()); ?></span>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="#client-tours-tab" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        <i class="ti ti-user me-1"></i>Requested
                        <span class="badge bg-yellow-lt ms-1"><?php echo e($clientTours->total()); ?></span>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="#monthly-chart-tab" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        <i class="ti ti-chart-line me-1"></i>Monthly
                        <span class="badge bg-green-lt ms-1"><?php echo e($monthlyChartTours->total() + $cancelledChartTours->total()); ?></span>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="#archived-tours-tab" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        <i class="ti ti-archive me-1"></i>Archived
                        <span class="badge bg-secondary-lt ms-1"><?php echo e($archivedTours->total()); ?></span>
                    </a>
                </li>
            </ul>
        </div>
        
        <div class="card-body">
            
            <div class="row mb-3">
                <div class="col-md-6 col-lg-5 mb-2 mb-md-0">
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="ti ti-search"></i>
                        </span>
                        <input type="text" 
                               id="tour-search" 
                               class="form-control" 
                               placeholder="Search tours by name, date, status..."
                               data-table="tour-table">
                    </div>
                </div>
                <div class="col-md-6 col-lg-7">
                    <div class="d-flex gap-2 justify-content-md-end">
                        <select id="filterDropdown" class="form-select" style="max-width: 200px;">
                            <option value="">All Statuses</option>
                            <option value="quotations">Quotations</option>
                            <option value="go_ahead">Go Ahead</option>
                        </select>
                        <button class="btn btn-secondary export-csv"
                                data-table="tour-table"
                                data-filename="tours_export.csv">
                            <i class="ti ti-download me-1"></i>
                            <span class="d-none d-sm-inline">Export CSV</span>
                        </button>
                    </div>
                </div>
            </div>

            
            <div class="tab-content">
                
                <div class="tab-pane fade show active" id="tours-tab" role="tabpanel">
                    <div class="table-responsive">
                        <table id="tour-table" class="table card-table table-vcenter">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">ID</th>
                                    <th><?php echo e(trans('main.Name')); ?></th>
                                    <th><?php echo e(trans('main.DepDate')); ?></th>
                                    <th class="d-none d-lg-table-cell"><?php echo e(trans('Responsible Users')); ?></th>
                                    <th class="d-none d-xl-table-cell"><?php echo e(trans('Assigned Users')); ?></th>
                                    <th><?php echo e(trans('main.Status')); ?></th>
                                    <th class="d-none d-md-table-cell"><?php echo e(trans('main.ExternalName')); ?></th>
                                    <th class="text-end"><?php echo e(trans('main.Actions')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $tours; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tour): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="clickable-row"
                                    style="background: <?php echo e($tour->getRowBackgroundColor()); ?>;"
                                    data-href="<?php echo e(route('tour.show', ['tour' => $tour->id])); ?>">
                                    <td>
                                        <span class="text-muted">#<?php echo e($tour->id); ?></span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold"><?php echo e($tour->name); ?></span>
                                            <small class="text-muted d-lg-none">
                                                <?php echo e($tour->responsible_user_names ?? ''); ?>

                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted">
                                            <?php echo e($tour->departure_date ? \Carbon\Carbon::parse($tour->departure_date)->format('Y-m-d') : '—'); ?>

                                        </span>
                                    </td>
                                    <td class="d-none d-lg-table-cell">
                                        <?php echo e($tour->responsible_user_names ?? '—'); ?>

                                    </td>
                                    <td class="d-none d-xl-table-cell">
                                        <?php echo e($tour->assigned_user_names ?? '—'); ?>

                                    </td>
                                    <td>
                                        <span class="badge" style="background-color: <?php echo e($tour->getStatusColor()); ?>20; color: <?php echo e($tour->getStatusColor()); ?>; border: 1px solid <?php echo e($tour->getStatusColor()); ?>40;">
                                            <span class="status-dot" style="background-color: <?php echo e($tour->getStatusColor()); ?>;"></span>
                                            <?php echo e($tour->getStatusName()); ?>

                                        </span>
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        <span class="text-muted"><?php echo e($tour->external_name ?? '—'); ?></span>
                                    </td>
                                    <td class="text-end action-cell">
                                        <div class="btn-list justify-content-end">
                                            <?php echo $__env->make('component.action_buttons', ['item' => $tour, 'routePrefix' => 'tour'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="empty">
                                            <div class="empty-icon">
                                                <i class="ti ti-plane icon" style="font-size: 3rem;"></i>
                                            </div>
                                            <p class="empty-title">No tours found</p>
                                            <p class="empty-subtitle text-muted">Get started by creating your first tour</p>
                                            <div class="empty-action">
                                                <?php echo \App\Helper\PermissionHelper::getCreateButton(route('tour.create'), \App\Tour::class, 'btn btn-primary'); ?>

                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    
                    <?php if($tours->hasPages()): ?>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            Showing <?php echo e($tours->firstItem()); ?> to <?php echo e($tours->lastItem()); ?> of <?php echo e($tours->total()); ?> entries
                        </div>
                        <div>
                            <?php echo e($tours->links()); ?>

                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                
                <div class="tab-pane fade" id="client-tours-tab" role="tabpanel">
                <div class="card">
                    <div class="table-responsive">
                        <table id="client-tour-table" class="table card-table table-vcenter">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">ID</th>
                                    <th><?php echo e(trans('main.Name')); ?></th>
                                    <th class="d-none d-md-table-cell"><?php echo e(trans('Client Name')); ?></th>
                                    <th><?php echo e(trans('main.DepDate')); ?></th>
                                    <th><?php echo e(trans('main.Status')); ?></th>
                                    <th class="d-none d-lg-table-cell"><?php echo e(trans('main.ExternalName')); ?></th>
                                    <th class="text-end"><?php echo e(trans('main.Actions')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $clientTours; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tour): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="clickable-row"
                                    style="background: <?php echo e($tour->getRowBackgroundColor()); ?>;"
                                    data-href="<?php echo e(route('tour.show', ['tour' => $tour->id])); ?>">
                                    <td >
                                        <span class="text-muted">#<?php echo e($tour->id); ?></span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold"><?php echo e($tour->name); ?></span>
                                            <small class="text-muted d-md-none"><?php echo e($tour->client_name ?? ''); ?></small>
                                        </div>
                                    </td>
                                    <td class="d-none d-md-table-cell"><?php echo e($tour->client_name ?? '—'); ?></td>
                                    <td>
                                        <span class="text-muted">
                                            <?php echo e($tour->departure_date ? \Carbon\Carbon::parse($tour->departure_date)->format('Y-m-d') : '—'); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge" style="background-color: <?php echo e($tour->getStatusColor()); ?>20; color: <?php echo e($tour->getStatusColor()); ?>; border: 1px solid <?php echo e($tour->getStatusColor()); ?>40;">
                                            <span class="status-dot" style="background-color: <?php echo e($tour->getStatusColor()); ?>;"></span>
                                            <?php echo e($tour->getStatusName()); ?>

                                        </span>
                                    </td>
                                    <td class="d-none d-lg-table-cell">
                                        <span class="text-muted"><?php echo e($tour->external_name ?? '—'); ?></span>
                                    </td>
                                    <td class="text-end action-cell">
                                        <div class="btn-list justify-content-end">
                                            <?php echo $__env->make('component.action_buttons', ['item' => $tour, 'routePrefix' => 'tour'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="empty">
                                            <div class="empty-icon">
                                                <i class="ti ti-users icon" style="font-size: 3rem;"></i>
                                            </div>
                                            <p class="empty-title">No requested tours</p>
                                            <p class="empty-subtitle text-muted">Client tour requests will appear here</p>
                                        </div>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    
                    <?php if($clientTours->hasPages()): ?>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            Showing <?php echo e($clientTours->firstItem()); ?> to <?php echo e($clientTours->lastItem()); ?> of <?php echo e($clientTours->total()); ?> entries
                        </div>
                        <div>
                            <?php echo e($clientTours->appends(['client_page' => $clientTours->currentPage()])->links()); ?>

                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

                
                <div class="tab-pane fade" id="monthly-chart-tab" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">On Going Projects</h3>
                        <div class="card-actions">
                            <select id="year-filter" class="form-select">
                                <option value="">All Years</option>
                                <?php $__currentLoopData = $years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($year); ?>"><?php echo e($year); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <select id="month-filter" class="form-select">
                                <option value="">All Months</option>
                                <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>"><?php echo e($month); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="monthly-chart-table" class="table card-table table-vcenter">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">ID</th>
                                    <th><?php echo e(trans('main.Name')); ?></th>
                                    <th class="d-none d-lg-table-cell"><?php echo e(trans('Responsible Users')); ?></th>
                                    <th><?php echo e(trans('main.Status')); ?></th>
                                    <th class="d-none d-md-table-cell"><?php echo e(trans('main.ExternalName')); ?></th>
                                    <th class="text-end"><?php echo e(trans('main.Actions')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $monthlyChartTours; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tour): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="clickable-row"
                                    style="background: <?php echo e($tour->getRowBackgroundColor()); ?>;"
                                    data-href="<?php echo e(route('tour.show', ['tour' => $tour->id])); ?>">
                                    <td>
                                        <span class="text-muted">#<?php echo e($tour->id); ?></span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold"><?php echo e($tour->name); ?></span>
                                            <small class="text-muted d-lg-none"><?php echo e($tour->responsible_user_names ?? ''); ?></small>
                                        </div>
                                    </td>
                                    <td class="d-none d-lg-table-cell"><?php echo e($tour->responsible_user_names ?? '—'); ?></td>
                                    <td>
                                        <span class="badge" style="background-color: <?php echo e($tour->getStatusColor()); ?>20; color: <?php echo e($tour->getStatusColor()); ?>; border: 1px solid <?php echo e($tour->getStatusColor()); ?>40;">
                                            <span class="status-dot" style="background-color: <?php echo e($tour->getStatusColor()); ?>;"></span>
                                            <?php echo e($tour->getStatusName()); ?>

                                        </span>
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        <span class="text-muted"><?php echo e($tour->external_name ?? '—'); ?></span>
                                    </td>
                                    <td class="text-end action-cell">
                                        <div class="btn-list justify-content-end">
                                            <?php echo $__env->make('component.action_buttons', ['item' => $tour, 'routePrefix' => 'tour'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="empty">
                                            <div class="empty-icon">
                                                <i class="ti ti-calendar icon" style="font-size: 3rem;"></i>
                                            </div>
                                            <p class="empty-title">No ongoing projects</p>
                                            <p class="empty-subtitle text-muted">Tours for the selected month will appear here</p>
                                        </div>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    
                    <?php if($monthlyChartTours->hasPages()): ?>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            Showing <?php echo e($monthlyChartTours->firstItem()); ?> to <?php echo e($monthlyChartTours->lastItem()); ?> of <?php echo e($monthlyChartTours->total()); ?> entries
                        </div>
                        <?php echo e($monthlyChartTours->appends(['monthly_page' => $monthlyChartTours->currentPage()])->links()); ?>

                    </div>
                    <?php endif; ?>
                </div>

                <div class="card" style="margin-top: 24px;">
                    <div class="card-header">
                        <h3 class="card-title">Cancelled Projects</h3>
                    </div>
                    <div class="table-responsive">
                        <table id="cancelled-chart-table" class="table card-table table-vcenter">
                            <thead>
                                <tr>
                                    <th  style="width: 60px;">ID</th>
                                    <th ><?php echo e(trans('main.Name')); ?></th>
                                    <th ><?php echo e(trans('Responsible Users')); ?></th>
                                    <th ><?php echo e(trans('main.Status')); ?></th>
                                    <th ><?php echo e(trans('main.ExternalName')); ?></th>
                                    <th class="text-end"><?php echo e(trans('main.Actions')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $cancelledChartTours; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tour): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="clickable-row"
                                    style="background: <?php echo e($tour->getRowBackgroundColor()); ?>;"
                                    data-href="<?php echo e(route('tour.show', ['tour' => $tour->id])); ?>">
                                    <td >
                                        <span class="text-muted">#<?php echo e($tour->id); ?></span>
                                    </td>
                                    <td >
                                        <span class="fw-bold"><?php echo e($tour->name); ?></span>
                                    </td>
                                    <td ><?php echo e($tour->responsible_user_names ?? '—'); ?></td>
                                    <td >
                                        <span class="badge" style="background-color: <?php echo e($tour->getStatusColor()); ?>20; color: <?php echo e($tour->getStatusColor()); ?>; border: 1px solid <?php echo e($tour->getStatusColor()); ?>40;">
                                            <span class="status-dot" style="background-color: <?php echo e($tour->getStatusColor()); ?>;"></span>
                                            <?php echo e($tour->getStatusName()); ?>

                                        </span>
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        <span class="text-muted"><?php echo e($tour->external_name ?? '—'); ?></span>
                                    </td>
                                    <td class="text-end action-cell">
                                        <div class="btn-list justify-content-end">
                                            <?php echo $__env->make('component.action_buttons', ['item' => $tour, 'routePrefix' => 'tour'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="empty">
                                            <div class="empty-icon">
                                                <i class="ti ti-ban icon" style="font-size: 3rem;"></i>
                                            </div>
                                            <p class="empty-title">No cancelled projects</p>
                                            <p class="empty-subtitle text-muted">Cancelled tours will appear here</p>
                                        </div>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    
                    <?php if($cancelledChartTours->hasPages()): ?>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            Showing <?php echo e($cancelledChartTours->firstItem()); ?> to <?php echo e($cancelledChartTours->lastItem()); ?> of <?php echo e($cancelledChartTours->total()); ?> entries
                        </div>
                        <?php echo e($cancelledChartTours->appends(['cancelled_page' => $cancelledChartTours->currentPage()])->links()); ?>

                    </div>
                    <?php endif; ?>
                </div>
            </div>

            
                <div class="tab-pane fade" id="archived-tours-tab" role="tabpanel">
                <div class="card">
                    <div class="table-responsive">
                        <table id="archive-tour-table" class="table card-table table-vcenter">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">ID</th>
                                    <th><?php echo e(trans('main.Name')); ?></th>
                                    <th class="d-none d-lg-table-cell"><?php echo e(trans('Responsible Users')); ?></th>
                                    <th><?php echo e(trans('main.DepDate')); ?></th>
                                    <th><?php echo e(trans('main.Status')); ?></th>
                                    <th class="d-none d-md-table-cell"><?php echo e(trans('main.ExternalName')); ?></th>
                                    <th class="text-end"><?php echo e(trans('main.Actions')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $archivedTours; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tour): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="clickable-row"
                                    style="background: <?php echo e($tour->getRowBackgroundColor()); ?>;"
                                    data-href="<?php echo e(route('tour.show', ['tour' => $tour->id])); ?>">
                                    <td>
                                        <span class="text-muted">#<?php echo e($tour->id); ?></span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold"><?php echo e($tour->name); ?></span>
                                            <small class="text-muted d-lg-none"><?php echo e($tour->responsible_user_names ?? ''); ?></small>
                                        </div>
                                    </td>
                                    <td class="d-none d-lg-table-cell"><?php echo e($tour->responsible_user_names ?? '—'); ?></td>
                                    <td>
                                        <span class="text-muted">
                                            <?php echo e($tour->departure_date ? \Carbon\Carbon::parse($tour->departure_date)->format('Y-m-d') : '—'); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge" style="background-color: <?php echo e($tour->getStatusColor()); ?>20; color: <?php echo e($tour->getStatusColor()); ?>; border: 1px solid <?php echo e($tour->getStatusColor()); ?>40;">
                                            <span class="status-dot" style="background-color: <?php echo e($tour->getStatusColor()); ?>;"></span>
                                            <?php echo e($tour->getStatusName()); ?>

                                        </span>
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        <span class="text-muted"><?php echo e($tour->external_name ?? '—'); ?></span>
                                    </td>
                                    <td class="text-end action-cell">
                                        <div class="btn-list justify-content-end">
                                            <?php echo $__env->make('component.action_buttons', ['item' => $tour, 'routePrefix' => 'tour'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="empty">
                                            <div class="empty-icon">
                                                <i class="ti ti-archive icon" style="font-size: 3rem;"></i>
                                            </div>
                                            <p class="empty-title">No archived tours</p>
                                            <p class="empty-subtitle text-muted">Archived tours will appear here</p>
                                        </div>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    
                    <?php if($archivedTours->hasPages()): ?>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            Showing <?php echo e($archivedTours->firstItem()); ?> to <?php echo e($archivedTours->lastItem()); ?> of <?php echo e($archivedTours->total()); ?> entries
                        </div>
                        <?php echo e($archivedTours->appends(['archived_page' => $archivedTours->currentPage()])->links()); ?>

                    </div>
                    <?php endif; ?>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>


<div class="modal modal-blur fade" id="tour-clone-modal" tabindex="-1" aria-labelledby="tour-clone-label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-copy me-2"></i>Clone Tour
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info block-error text-center" style="display: none;"></div>
                <form id="tour-clone-modal-form">
                    <div class="mb-3">
                        <label for="departure_date" class="form-label"><?php echo e(trans('main.DepartureDate')); ?></label>
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <i class="ti ti-calendar"></i>
                            </span>
                            <?php echo Form::text('departure_date', '', ['class' => 'form-control datepicker', 'id' => 'departure_date', 'autocomplete' => 'off']); ?>

                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" data-bs-dismiss="modal">
                    <i class="ti ti-x me-1"></i>Cancel
                </button>
                <button type="submit" class="btn btn-success pre-loader-func" id="clone_tour_send">
                    <i class="ti ti-check me-1"></i><?php echo e(trans('main.Submit')); ?>

                </button>
            </div>
        </div>
    </div>
</div>


<div class="modal modal-blur fade" tabindex="-1" id="error_tour" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="form_confirmed_hotel">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="ti ti-alert-triangle me-2"></i><?php echo e(trans('main.Warning')); ?>!
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h3 class="error_tour_message"></h3>
                </div>
                <div class="modal-footer">
                    <div class="btn-send-confirmed_hotel">
                        <button type="reset" class="btn btn-success modal-close" data-bs-dismiss="modal">
                            <i class="ti ti-check me-1"></i>Ok
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<span id="permission" data-permission="<?php echo e(\App\Helper\PermissionHelper::checkPermission('tour.edit')); ?>"></span>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('post_scripts'); ?>
<script src="<?php echo e(asset('js/tour-interactions.js')); ?>"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Bootstrap 5 handles tab switching automatically with data-bs-toggle="tab"
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Handle tour clone button click
        $('.clone-tour-button').show(); // Show clone buttons
        $(document).on('click', '.clone-tour-button', function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            $('.block-error').text('');
            $('.block-error').hide();
            $('#tour-clone-modal-form').attr('action', '/tour/' + id + '/clone');
            
            // Open modal with Bootstrap 5
            var cloneModal = new bootstrap.Modal(document.getElementById('tour-clone-modal'));
            cloneModal.show();
        });

        // Handle clone form submission
        $('#clone_tour_send').on('click', function (e) {
            e.preventDefault();
            $('.block-error').text('');
            $('.block-error').hide();

            if($('#departure_date').val() === '') {
                $('.block-error').text('Enter Date');
                $('.block-error').show();
            } else {
                $('#tour-clone-modal-form').submit();
            }
        });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('scaffold-interface.layouts.tabler-app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xamppp\htdocs\dev-eetstravel\resources\views/tour/index.blade.php ENDPATH**/ ?>