<?php $__env->startSection('title','Tours'); ?>

<?php $__env->startSection('post_styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/tour-shopify.css')); ?>">
<style>
    .pagination-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 0;
        margin-top: 20px;
    }
    .pagination-info {
        color: #6c757d;
        font-size: 14px;
    }
    .pagination {
        display: flex;
        list-style: none;
        padding: 0;
        margin: 0;
        gap: 5px;
    }
    .pagination li {
        display: inline-block;
    }
    .pagination a, .pagination span {
        display: block;
        padding: 8px 12px;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        color: #007bff;
        text-decoration: none;
        transition: all 0.2s;
    }
    .pagination .active span {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }
    .pagination a:hover {
        background-color: #e9ecef;
        border-color: #dee2e6;
    }
    .pagination .disabled span {
        color: #6c757d;
        pointer-events: none;
        background-color: #fff;
        border-color: #dee2e6;
    }
    
    /* Tab Navigation Styles */
    .shopify-tabs-nav {
        display: flex;
        gap: 0;
        border-bottom: 2px solid #dee2e6;
        margin-bottom: 24px;
    }
    .shopify-tabs-nav .tab-btn {
        padding: 12px 24px;
        background: none;
        border: none;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        color: #6c757d;
        border-bottom: 3px solid transparent;
        margin-bottom: -2px;
        transition: all 0.3s ease;
    }
    .shopify-tabs-nav .tab-btn:hover {
        color: #495057;
    }
    .shopify-tabs-nav .tab-btn.active {
        color: #007bff;
        border-bottom-color: #007bff;
    }
    
    .shopify-tab-content {
        display: none;
    }
    .shopify-tab-content.active {
        display: block;
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="shopify-tours-page">
    
    <div class="shopify-page-header">
        <div class="shopify-header-content">
            <div class="shopify-header-top">
                <div>
                    <h1 class="shopify-page-title">Tours</h1>
                    <p class="shopify-page-subtitle">Manage and organize all your travel tours</p>
                </div>
                <div class="shopify-header-actions">
                    <?php echo $__env->make('legend.tour_legend', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    <?php echo \App\Helper\PermissionHelper::getCreateButton(route('tour.create'), \App\Tour::class, 'shopify-btn shopify-btn-primary'); ?>

                </div>
            </div>
        </div>
    </div>

    
    <div class="shopify-page-content">
        <div class="shopify-container">
            
            <?php if(session('message_buses')): ?>
                <div class="shopify-alert shopify-alert-info">
                    <i class="fa fa-info-circle"></i>
                    <?php echo e(session('message_buses')); ?>

                </div>
            <?php endif; ?>

            
            <div class="shopify-tabs-nav">
                <button class="tab-btn active" data-tab="tours">
                    Tours (<?php echo e($tours->total()); ?>)
                </button>
                <button class="tab-btn" data-tab="client-tours">
                    Requested Tours (<?php echo e($clientTours->total()); ?>)
                </button>
                <button class="tab-btn" data-tab="monthly-chart">
                    Monthly Chart (<?php echo e($monthlyChartTours->total() + $cancelledChartTours->total()); ?>)
                </button>
                <button class="tab-btn" data-tab="archived-tours">
                    Archived Tours (<?php echo e($archivedTours->total()); ?>)
                </button>
            </div>

            
            <div class="shopify-toolbar">
                <div class="shopify-search-box">
                    <i class="fa fa-search shopify-search-icon"></i>
                    <input type="text"
                               id="tour-search"
                               class="shopify-search-input"
                               placeholder="Search tours by name, date, status..."
                               data-table="tour-table">
                </div>
                <div class="shopify-toolbar-actions">
                    <select id="filterDropdown" class="shopify-filter-select">
                        <option value="">All Statuses</option>
                        <option value="quotations">Quotations</option>
                        <option value="go_ahead">Go Ahead</option>
                    </select>
                    <button class="shopify-btn shopify-btn-secondary export-csv"
                                 data-table="tour-table"
                                 data-filename="tours_export.csv">
                        <i class="fa fa-download"></i>
                        Export CSV
                    </button>
                </div>
            </div>

            
            <div class="shopify-tab-content active" id="tours-tab">
                <div class="shopify-card">
                    <div class="shopify-table-wrapper">
                        <table id="tour-table" class="shopify-table">
                            <thead>
                                <tr>
                                    <th class="shopify-table-header" style="width: 60px;">ID</th>
                                    <th class="shopify-table-header"><?php echo e(trans('main.Name')); ?></th>
                                    <th class="shopify-table-header"><?php echo e(trans('main.DepDate')); ?></th>
                                    <th class="shopify-table-header"><?php echo e(trans('Responsible Users')); ?></th>
                                    <th class="shopify-table-header"><?php echo e(trans('Assigned Users')); ?></th>
                                    <th class="shopify-table-header"><?php echo e(trans('main.Status')); ?></th>
                                    <th class="shopify-table-header"><?php echo e(trans('main.ExternalName')); ?></th>
                                    <th class="shopify-table-header shopify-table-header-actions"><?php echo e(trans('main.Actions')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $tours; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tour): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="shopify-table-row clickable-row"
                                    style="background: <?php echo e($tour->getRowBackgroundColor()); ?>;"
                                    data-href="<?php echo e(route('tour.show', ['tour' => $tour->id])); ?>">
                                    <td class="shopify-table-cell">
                                        <span class="shopify-text-muted">#<?php echo e($tour->id); ?></span>
                                    </td>
                                    <td class="shopify-table-cell">
                                        <span class="shopify-text-strong"><?php echo e($tour->name); ?></span>
                                    </td>
                                    <td class="shopify-table-cell">
                                        <span class="shopify-text-muted">
                                            <?php echo e($tour->departure_date ? \Carbon\Carbon::parse($tour->departure_date)->format('Y-m-d') : '—'); ?>

                                        </span>
                                    </td>
                                    <td class="shopify-table-cell">
                                        <?php echo e($tour->responsible_user_names ?? '—'); ?>

                                    </td>
                                    <td class="shopify-table-cell">
                                        <?php echo e($tour->assigned_user_names ?? '—'); ?>

                                    </td>
                                    <td class="shopify-table-cell">
                                        <span class="shopify-status-badge" style="background-color: <?php echo e($tour->getStatusColor()); ?>20; color: <?php echo e($tour->getStatusColor()); ?>; border: 1px solid <?php echo e($tour->getStatusColor()); ?>40;">
                                            <span class="shopify-status-dot" style="background-color: <?php echo e($tour->getStatusColor()); ?>;"></span>
                                            <?php echo e($tour->getStatusName()); ?>

                                        </span>
                                    </td>
                                    <td class="shopify-table-cell">
                                        <span class="shopify-text-muted"><?php echo e($tour->external_name ?? '—'); ?></span>
                                    </td>
                                    <td class="shopify-table-cell shopify-table-cell-actions action-cell">
                                        <div class="shopify-action-buttons">
                                            <?php echo $__env->make('component.action_buttons', ['item' => $tour, 'routePrefix' => 'tour'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="8" class="shopify-empty-state">
                                        <div class="shopify-empty-state-content">
                                            <i class="fa fa-suitcase shopify-empty-state-icon"></i>
                                            <h3 class="shopify-empty-state-title">No tours found</h3>
                                            <p class="shopify-empty-state-description">Get started by creating your first tour</p>
                                            <?php echo \App\Helper\PermissionHelper::getCreateButton(route('tour.create'), \App\Tour::class, 'shopify-btn shopify-btn-primary'); ?>

                                        </div>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    
                    <?php if($tours->hasPages()): ?>
                    <div class="pagination-wrapper">
                        <div class="pagination-info">
                            Showing <?php echo e($tours->firstItem()); ?> to <?php echo e($tours->lastItem()); ?> of <?php echo e($tours->total()); ?> entries
                        </div>
                        <?php echo e($tours->links()); ?>

                    </div>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="shopify-tab-content" id="client-tours-tab">
                <div class="shopify-card">
                    <div class="shopify-table-wrapper">
                        <table id="client-tour-table" class="shopify-table">
                            <thead>
                                <tr>
                                    <th class="shopify-table-header" style="width: 60px;">ID</th>
                                    <th class="shopify-table-header"><?php echo e(trans('main.Name')); ?></th>
                                    <th class="shopify-table-header"><?php echo e(trans('Client Name')); ?></th>
                                    <th class="shopify-table-header"><?php echo e(trans('main.DepDate')); ?></th>
                                    <th class="shopify-table-header"><?php echo e(trans('main.Status')); ?></th>
                                    <th class="shopify-table-header"><?php echo e(trans('main.ExternalName')); ?></th>
                                    <th class="shopify-table-header shopify-table-header-actions"><?php echo e(trans('main.Actions')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $clientTours; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tour): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="shopify-table-row clickable-row"
                                    style="background: <?php echo e($tour->getRowBackgroundColor()); ?>;"
                                    data-href="<?php echo e(route('tour.show', ['tour' => $tour->id])); ?>">
                                    <td class="shopify-table-cell">
                                        <span class="shopify-text-muted">#<?php echo e($tour->id); ?></span>
                                    </td>
                                    <td class="shopify-table-cell">
                                        <span class="shopify-text-strong"><?php echo e($tour->name); ?></span>
                                    </td>
                                    <td class="shopify-table-cell"><?php echo e($tour->client_name ?? '—'); ?></td>
                                    <td class="shopify-table-cell">
                                        <span class="shopify-text-muted">
                                            <?php echo e($tour->departure_date ? \Carbon\Carbon::parse($tour->departure_date)->format('Y-m-d') : '—'); ?>

                                        </span>
                                    </td>
                                    <td class="shopify-table-cell">
                                        <span class="shopify-status-badge" style="background-color: <?php echo e($tour->getStatusColor()); ?>20; color: <?php echo e($tour->getStatusColor()); ?>; border: 1px solid <?php echo e($tour->getStatusColor()); ?>40;">
                                            <span class="shopify-status-dot" style="background-color: <?php echo e($tour->getStatusColor()); ?>;"></span>
                                            <?php echo e($tour->getStatusName()); ?>

                                        </span>
                                    </td>
                                    <td class="shopify-table-cell">
                                        <span class="shopify-text-muted"><?php echo e($tour->external_name ?? '—'); ?></span>
                                    </td>
                                    <td class="shopify-table-cell shopify-table-cell-actions action-cell">
                                        <div class="shopify-action-buttons">
                                            <?php echo $__env->make('component.action_buttons', ['item' => $tour, 'routePrefix' => 'tour'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="7" class="shopify-empty-state">
                                        <div class="shopify-empty-state-content">
                                            <i class="fa fa-users shopify-empty-state-icon"></i>
                                            <h3 class="shopify-empty-state-title">No requested tours</h3>
                                            <p class="shopify-empty-state-description">Client tour requests will appear here</p>
                                        </div>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    
                    <?php if($clientTours->hasPages()): ?>
                    <div class="pagination-wrapper">
                        <div class="pagination-info">
                            Showing <?php echo e($clientTours->firstItem()); ?> to <?php echo e($clientTours->lastItem()); ?> of <?php echo e($clientTours->total()); ?> entries
                        </div>
                        <?php echo e($clientTours->appends(['client_page' => $clientTours->currentPage()])->links()); ?>

                    </div>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="shopify-tab-content" id="monthly-chart-tab">
                <div class="shopify-card">
                    <div class="shopify-card-header">
                        <h3 class="shopify-card-title">On Going Projects</h3>
                        <div class="shopify-toolbar-actions">
                            <select id="year-filter" class="shopify-filter-select">
                                <option value="">All Years</option>
                                <?php $__currentLoopData = $years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($year); ?>"><?php echo e($year); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <select id="month-filter" class="shopify-filter-select">
                                <option value="">All Months</option>
                                <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>"><?php echo e($month); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <div class="shopify-table-wrapper">
                        <table id="monthly-chart-table" class="shopify-table">
                            <thead>
                                <tr>
                                    <th class="shopify-table-header" style="width: 60px;">ID</th>
                                    <th class="shopify-table-header"><?php echo e(trans('main.Name')); ?></th>
                                    <th class="shopify-table-header"><?php echo e(trans('Responsible Users')); ?></th>
                                    <th class="shopify-table-header"><?php echo e(trans('main.Status')); ?></th>
                                    <th class="shopify-table-header"><?php echo e(trans('main.ExternalName')); ?></th>
                                    <th class="shopify-table-header shopify-table-header-actions"><?php echo e(trans('main.Actions')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $monthlyChartTours; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tour): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="shopify-table-row clickable-row"
                                    style="background: <?php echo e($tour->getRowBackgroundColor()); ?>;"
                                    data-href="<?php echo e(route('tour.show', ['tour' => $tour->id])); ?>">
                                    <td class="shopify-table-cell">
                                        <span class="shopify-text-muted">#<?php echo e($tour->id); ?></span>
                                    </td>
                                    <td class="shopify-table-cell">
                                        <span class="shopify-text-strong"><?php echo e($tour->name); ?></span>
                                    </td>
                                    <td class="shopify-table-cell"><?php echo e($tour->responsible_user_names ?? '—'); ?></td>
                                    <td class="shopify-table-cell">
                                        <span class="shopify-status-badge" style="background-color: <?php echo e($tour->getStatusColor()); ?>20; color: <?php echo e($tour->getStatusColor()); ?>; border: 1px solid <?php echo e($tour->getStatusColor()); ?>40;">
                                            <span class="shopify-status-dot" style="background-color: <?php echo e($tour->getStatusColor()); ?>;"></span>
                                            <?php echo e($tour->getStatusName()); ?>

                                        </span>
                                    </td>
                                    <td class="shopify-table-cell">
                                        <span class="shopify-text-muted"><?php echo e($tour->external_name ?? '—'); ?></span>
                                    </td>
                                    <td class="shopify-table-cell shopify-table-cell-actions action-cell">
                                        <div class="shopify-action-buttons">
                                            <?php echo $__env->make('component.action_buttons', ['item' => $tour, 'routePrefix' => 'tour'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="shopify-empty-state">
                                        <div class="shopify-empty-state-content">
                                            <i class="fa fa-calendar shopify-empty-state-icon"></i>
                                            <h3 class="shopify-empty-state-title">No ongoing projects</h3>
                                        </div>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    
                    <?php if($monthlyChartTours->hasPages()): ?>
                    <div class="pagination-wrapper">
                        <div class="pagination-info">
                            Showing <?php echo e($monthlyChartTours->firstItem()); ?> to <?php echo e($monthlyChartTours->lastItem()); ?> of <?php echo e($monthlyChartTours->total()); ?> entries
                        </div>
                        <?php echo e($monthlyChartTours->appends(['monthly_page' => $monthlyChartTours->currentPage()])->links()); ?>

                    </div>
                    <?php endif; ?>
                </div>

                <div class="shopify-card" style="margin-top: 24px;">
                    <div class="shopify-card-header">
                        <h3 class="shopify-card-title">Cancelled Projects</h3>
                    </div>
                    <div class="shopify-table-wrapper">
                        <table id="cancelled-chart-table" class="shopify-table">
                            <thead>
                                <tr>
                                    <th class="shopify-table-header" style="width: 60px;">ID</th>
                                    <th class="shopify-table-header"><?php echo e(trans('main.Name')); ?></th>
                                    <th class="shopify-table-header"><?php echo e(trans('Responsible Users')); ?></th>
                                    <th class="shopify-table-header"><?php echo e(trans('main.Status')); ?></th>
                                    <th class="shopify-table-header"><?php echo e(trans('main.ExternalName')); ?></th>
                                    <th class="shopify-table-header shopify-table-header-actions"><?php echo e(trans('main.Actions')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $cancelledChartTours; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tour): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="shopify-table-row clickable-row"
                                    style="background: <?php echo e($tour->getRowBackgroundColor()); ?>;"
                                    data-href="<?php echo e(route('tour.show', ['tour' => $tour->id])); ?>">
                                    <td class="shopify-table-cell">
                                        <span class="shopify-text-muted">#<?php echo e($tour->id); ?></span>
                                    </td>
                                    <td class="shopify-table-cell">
                                        <span class="shopify-text-strong"><?php echo e($tour->name); ?></span>
                                    </td>
                                    <td class="shopify-table-cell"><?php echo e($tour->responsible_user_names ?? '—'); ?></td>
                                    <td class="shopify-table-cell">
                                        <span class="shopify-status-badge" style="background-color: <?php echo e($tour->getStatusColor()); ?>20; color: <?php echo e($tour->getStatusColor()); ?>; border: 1px solid <?php echo e($tour->getStatusColor()); ?>40;">
                                            <span class="shopify-status-dot" style="background-color: <?php echo e($tour->getStatusColor()); ?>;"></span>
                                            <?php echo e($tour->getStatusName()); ?>

                                        </span>
                                    </td>
                                    <td class="shopify-table-cell">
                                        <span class="shopify-text-muted"><?php echo e($tour->external_name ?? '—'); ?></span>
                                    </td>
                                    <td class="shopify-table-cell shopify-table-cell-actions action-cell">
                                        <div class="shopify-action-buttons">
                                            <?php echo $__env->make('component.action_buttons', ['item' => $tour, 'routePrefix' => 'tour'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="shopify-empty-state">
                                        <div class="shopify-empty-state-content">
                                            <i class="fa fa-ban shopify-empty-state-icon"></i>
                                            <h3 class="shopify-empty-state-title">No cancelled projects</h3>
                                        </div>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    
                    <?php if($cancelledChartTours->hasPages()): ?>
                    <div class="pagination-wrapper">
                        <div class="pagination-info">
                            Showing <?php echo e($cancelledChartTours->firstItem()); ?> to <?php echo e($cancelledChartTours->lastItem()); ?> of <?php echo e($cancelledChartTours->total()); ?> entries
                        </div>
                        <?php echo e($cancelledChartTours->appends(['cancelled_page' => $cancelledChartTours->currentPage()])->links()); ?>

                    </div>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="shopify-tab-content" id="archived-tours-tab">
                <div class="shopify-card">
                    <div class="shopify-table-wrapper">
                        <table id="archive-tour-table" class="shopify-table">
                            <thead>
                                <tr>
                                    <th class="shopify-table-header" style="width: 60px;">ID</th>
                                    <th class="shopify-table-header"><?php echo e(trans('main.Name')); ?></th>
                                    <th class="shopify-table-header"><?php echo e(trans('Responsible Users')); ?></th>
                                    <th class="shopify-table-header"><?php echo e(trans('main.DepDate')); ?></th>
                                    <th class="shopify-table-header"><?php echo e(trans('main.Status')); ?></th>
                                    <th class="shopify-table-header"><?php echo e(trans('main.ExternalName')); ?></th>
                                    <th class="shopify-table-header shopify-table-header-actions"><?php echo e(trans('main.Actions')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $archivedTours; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tour): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="shopify-table-row clickable-row"
                                    style="background: <?php echo e($tour->getRowBackgroundColor()); ?>;"
                                    data-href="<?php echo e(route('tour.show', ['tour' => $tour->id])); ?>">
                                    <td class="shopify-table-cell">
                                        <span class="shopify-text-muted">#<?php echo e($tour->id); ?></span>
                                    </td>
                                    <td class="shopify-table-cell">
                                        <span class="shopify-text-strong"><?php echo e($tour->name); ?></span>
                                    </td>
                                    <td class="shopify-table-cell"><?php echo e($tour->responsible_user_names ?? '—'); ?></td>
                                    <td class="shopify-table-cell">
                                        <span class="shopify-text-muted">
                                            <?php echo e($tour->departure_date ? \Carbon\Carbon::parse($tour->departure_date)->format('Y-m-d') : '—'); ?>

                                        </span>
                                    </td>
                                    <td class="shopify-table-cell">
                                        <span class="shopify-status-badge" style="background-color: <?php echo e($tour->getStatusColor()); ?>20; color: <?php echo e($tour->getStatusColor()); ?>; border: 1px solid <?php echo e($tour->getStatusColor()); ?>40;">
                                            <span class="shopify-status-dot" style="background-color: <?php echo e($tour->getStatusColor()); ?>;"></span>
                                            <?php echo e($tour->getStatusName()); ?>

                                        </span>
                                    </td>
                                    <td class="shopify-table-cell">
                                        <span class="shopify-text-muted"><?php echo e($tour->external_name ?? '—'); ?></span>
                                    </td>
                                    <td class="shopify-table-cell shopify-table-cell-actions action-cell">
                                        <div class="shopify-action-buttons">
                                            <?php echo $__env->make('component.action_buttons', ['item' => $tour, 'routePrefix' => 'tour'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="7" class="shopify-empty-state">
                                        <div class="shopify-empty-state-content">
                                            <i class="fa fa-archive shopify-empty-state-icon"></i>
                                            <h3 class="shopify-empty-state-title">No archived tours</h3>
                                            <p class="shopify-empty-state-description">Archived tours will appear here</p>
                                        </div>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    
                    <?php if($archivedTours->hasPages()): ?>
                    <div class="pagination-wrapper">
                        <div class="pagination-info">
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


<div class="modal fade" id="tour-clone-modal" tabindex="-1" role="dialog" aria-labelledby="tour-clone-label">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="box box-body" style="border-top: none">
                <div class="alert alert-info block-error" style="text-align: center; display: none;"></div>
                <form id="tour-clone-modal-form">
                    <div class="form-group">
                        <label for="departure_date"><?php echo e(trans('main.DepartureDate')); ?></label>
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <?php echo Form::text('departure_date', '', ['class' => 'form-control pull-right datepicker', 'id' => 'departure_date', 'autocomplete' => 'off']); ?>

                        </div>
                    </div>
                    <button type="submit" class="btn btn-success pre-loader-func" id="clone_tour_send"><?php echo e(trans('main.Submit')); ?></button>
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
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><?php echo e(trans('main.Warning')); ?>!</h4>
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

<?php $__env->startSection('post_scripts'); ?>
<script src="<?php echo e(asset('js/tour-interactions.js')); ?>"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabButtons = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.shopify-tab-content');

        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const tabName = this.getAttribute('data-tab');
                
                // Remove active class from all buttons and contents
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabContents.forEach(content => content.classList.remove('active'));
                
                // Add active class to clicked button
                this.classList.add('active');
                
                // Show corresponding tab content
                const tabId = tabName + '-tab';
                const tabElement = document.getElementById(tabId);
                if (tabElement) {
                    tabElement.classList.add('active');
                }
            });
        });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('scaffold-interface.layouts.tabler-app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/tour/index.blade.php ENDPATH**/ ?>