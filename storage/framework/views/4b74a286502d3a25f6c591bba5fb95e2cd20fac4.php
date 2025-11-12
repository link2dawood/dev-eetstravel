
<?php $__env->startSection('title', 'Show Tour'); ?>

<?php $__env->startSection('post_styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/tour-shopify.css')); ?>">
<style>
    /* Toggle Switch */
    .toggle {
        position: relative;
        height: 42px;
        display: flex;
        align-items: center;
    }
    
    .toggle input[type="checkbox"] {
        position: absolute;
        left: 0;
        top: 0;
        z-index: 10;
        width: 100%;
        height: 100%;
        cursor: pointer;
        opacity: 0;
    }
    
    .toggle label {
        position: relative;
        display: flex;
        height: 100%;
        align-items: center;
    }
    
    .toggle label:before {
        content: "Quotations";
        background: #fff;
        color: #000;
        height: 42px;
        width: 140px;
        display: inline-flex;
        align-items: center;
        padding-left: 15px;
        border-radius: 30px;
        border: 1px solid #eee;
        box-shadow: inset 140px 0px 0 0px #000;
        font-size: 10px;
        transition: 0.2s ease-in;
    }
    
    .toggle label:after {
        content: "GoAhead";
        position: absolute;
        left: 80px;
        line-height: 42px;
        top: 0;
        color: #FFF;
        font-size: 10px;
        transition: 0.2s ease-in;
    }
    
    .toggle input[type="checkbox"]:checked + label:before {
        color: #000;
        box-shadow: inset 0px 0px 0 0px #000;
    }
    
    .toggle input[type="checkbox"]:checked + label:after {
        color: #FFF;
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-xl">
    
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col">
                
                <div class="page-pretitle">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(url('/home')); ?>">Home</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('tour.index')); ?>">Tours</a></li>
                            <li class="breadcrumb-item active"><?php echo e($tour->name); ?></li>
                        </ol>
                    </nav>
                </div>
                <h2 class="page-title">
                    <i class="ti ti-plane me-2"></i> <?php echo e($tour->name); ?>

                </h2>
            </div>
            
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="<?php echo e(route('tour.index')); ?>" class="btn btn-ghost-secondary">
                        <i class="ti ti-arrow-left me-1"></i><?php echo trans('main.Back'); ?>

                    </a>
                    <?php if(Auth::user()->can('tour.edit')): ?>
                        <a href="<?php echo e(route('tour.edit', ['tour' => $tour->id])); ?>" class="btn btn-warning">
                            <i class="ti ti-edit me-1"></i><?php echo trans('main.Edit'); ?>

                        </a>
                    <?php endif; ?>
                    <?php if(Auth::user()->can('task.create')): ?>
                        <a href="<?php echo e(url('task')); ?>/create?tour=<?php echo e($tour->id); ?>" class="btn btn-success">
                            <i class="ti ti-plus me-1"></i><?php echo trans('main.AddTask'); ?>

                        </a>
                    <?php endif; ?>
                    
                    
                    <div class="btn-group">
                        <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ti ti-file-export me-1"></i>Export
                        </button>
                        <div class="dropdown-menu">
                            <h6 class="dropdown-header">Export Tour</h6>
                            <a class="dropdown-item" href="#" onclick='export_to("<?php echo e(route('tour_export', ['id' => $tour->id, 'export' => 'csv', 'type' => 'tour'])); ?>");'>
                                <i class="ti ti-file-spreadsheet me-2"></i>CSV - Tour
                            </a>
                            <a class="dropdown-item" href="#" onclick='export_to("<?php echo e(route('tour_export', ['id' => $tour->id, 'export' => 'csv', 'type' => 'service'])); ?>");'>
                                <i class="ti ti-file-spreadsheet me-2"></i>CSV - Service
                            </a>
                            <a class="dropdown-item" href="#" onclick='export_to("<?php echo e(route('tour_export', ['id' => $tour->id, 'export' => 'xlsx'])); ?>");'>
                                <i class="ti ti-file-excel me-2"></i>Excel
                            </a>
                        </div>
                    </div>
                    
                    
                    <div class="btn-group">
                        <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ti ti-file-invoice me-1"></i><?php echo trans('main.Voucher'); ?>

                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="#" onclick='export_to("<?php echo e(route('tour_pdf_export', ['id' => $tour->id, 'pdf_type' => 'voucher'])); ?>");'>
                                <i class="ti ti-file-type-pdf me-2"></i>PDF
                            </a>
                            <a class="dropdown-item" href="#" onclick='export_to("<?php echo e(route('tour_doc_export', ['id' => $tour->id, 'doc_type' => 'voucher'])); ?>");'>
                                <i class="ti ti-file-type-doc me-2"></i>DOC
                            </a>
                        </div>
                    </div>
                    
                    
                    <div class="btn-group">
                        <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ti ti-route me-1"></i><?php echo trans('main.Itinerary'); ?>

                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="#" onclick='export_to("<?php echo e(route('tour_pdf_export', ['id' => $tour->id, 'pdf_type' => 'short'])); ?>");'>
                                <i class="ti ti-file-type-pdf me-2"></i>PDF
                            </a>
                            <a class="dropdown-item" href="#" onclick='export_to("<?php echo e(route('tour_html_export', ['id' => $tour->id, 'type' => 'html'])); ?>");'>
                                <i class="ti ti-file-code me-2"></i>HTML
                            </a>
                            <a class="dropdown-item" href="#" onclick='export_to("<?php echo e(route('tour_doc_export', ['id' => $tour->id, 'doc_type' => 'short'])); ?>");'>
                                <i class="ti ti-file-type-doc me-2"></i>DOC
                            </a>
                        </div>
                    </div>
                    
                    <button class="btn btn-info" onclick="showLandingPageModal()">
                        <i class="ti ti-world me-1"></i>Landing Page
                    </button>
                </div>
            </div>
        </div>
    </div>

    
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Select Office:</label>
                            <div class="input-group">
                                <select class="form-select selectedOffice">
                                    <?php $__currentLoopData = $offices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $office): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($office->id); ?>" <?php echo e((isset($select_office->id) && $office->id == $select_office->id) ? 'selected' : ''); ?>>
                                            <?php echo e($office->office_name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <button class="btn btn-primary select-office-btn" type="button">
                                    <i class="ti ti-check me-1"></i>Select
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <button class="btn btn-info mt-4" data-bs-toggle="modal" data-bs-target="#legendModal">
                                <i class="ti ti-help me-1"></i>Help
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <?php if($tour->is_quotation): ?>
        <div class="alert alert-warning" role="alert">
            <div class="d-flex">
                <div class="flex-fill">
                    <h4 class="alert-title">
                        <i class="ti ti-exchange me-2"></i>Convert Quotation to Tour
                    </h4>
                </div>
                <div>
                    <div class="toggle">
                        <input type="checkbox" id="check1" onclick="handleToggleConversion(this, true)" checked />
                        <label></label>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-success" role="alert">
            <div class="d-flex">
                <div class="flex-fill">
                    <h4 class="alert-title">
                        <i class="ti ti-exchange me-2"></i>Convert Tour to Quotation
                    </h4>
                </div>
                <div>
                    <div class="toggle">
                        <input type="checkbox" id="check2" onclick="handleToggleConversion(this, false)" />
                        <label></label>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    
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
                    <a href="#frontsheet-tab" class="nav-link active" data-bs-toggle="tab" aria-selected="true" role="tab">
                        <i class="ti ti-file-text me-1"></i>Front Sheet
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="#service-tab" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        <i class="ti ti-list me-1"></i><?php echo trans('main.Services'); ?>

                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="#tour-tab" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        <i class="ti ti-plane me-1"></i><?php echo trans('main.Tour'); ?>

                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="#quotation-tab" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        <i class="ti ti-calculator me-1"></i><?php echo trans('main.Quotation'); ?>

                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="#roomlist-tab" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        <i class="ti ti-bed me-1"></i><?php echo trans('main.GuestList'); ?>

                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="#invoices-tab" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        <i class="ti ti-file-invoice me-1"></i>Invoices
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="#billing-tab" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        <i class="ti ti-cash me-1"></i>Billing
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
            
            <div role="tabpanel" class="tab-pane active show" id="frontsheet-tab">
                <h3 class="mb-4">
                    <i class="ti ti-file-text me-2"></i>Front Sheet [- <?php echo e($tour->external_name ?? $tour->name); ?> #<?php echo e($tour->id); ?>]
                </h3>
                
                <?php if(!empty($quotation) && isset($quotation->id)): ?>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5>
                                <strong>Rooms:</strong>
                                <?php $peopleCount = 0; ?>
                                <?php $__currentLoopData = $listRoomsHotel; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $peopleCount += isset(App\TourPackage::$roomsPeopleCount[$room->room_types->code]) 
                                            ? App\TourPackage::$roomsPeopleCount[$room->room_types->code] * $room->count 
                                            : 0;
                                    ?>
                                    <?php echo e($room->room_types->code); ?> : <?php echo e($room->count); ?><?php echo e(!$loop->last ? ', ' : ''); ?>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </h5>
                        </div>
                        <div class="col-md-6">
                            <h5>
                                <strong>Pax:</strong> <?php echo e($tour->pax); ?> +<?php echo e($tour->pax_free); ?>

                            </h5>
                        </div>
                    </div>

                    <?php if($peopleCount != $tour->pax + $tour->pax_free): ?>
                        <div class="alert alert-warning alert-dismissible" role="alert">
                            <div class="d-flex">
                                <div>
                                    <i class="ti ti-alert-triangle me-2"></i>
                                </div>
                                <div class="flex-fill">
                                    <strong>Pax Count (<?php echo e($tour->pax + $tour->pax_free); ?>) is not equal to the number of people in the rooms (<?php echo e($peopleCount); ?>)</strong>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        </div>
                    <?php endif; ?>

                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle" style="font-size: 0.9rem;">
                            <thead class="table-light">
                                <tr>
                                    <th style="min-width: 100px;">Date</th>
                                    <th style="min-width: 80px;">City</th>
                                    <th style="min-width: 100px;">Quote Single</th>
                                    <th style="min-width: 100px;">Quote SS</th>
                                    <th style="min-width: 100px;">Quote HPP</th>
                                    <th style="min-width: 200px;">CMFD HOTEL</th>
                                    <th style="min-width: 80px;">Option</th>
                                    <th style="min-width: 100px;">Offer SS</th>
                                    <th style="min-width: 100px;">Offer HPP</th>
                                    <th style="min-width: 80px;">®</th>
                                    <th style="min-width: 120px;">VC sent to SHA</th>
                                    <th style="min-width: 120px;">Budget HPP +/-</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    // Generate date range from tour departure to retirement
                                    $startDate = \Carbon\Carbon::parse($tour->departure_date);
                                    $endDate = \Carbon\Carbon::parse($tour->retirement_date);
                                    $currentDate = $startDate->copy();
                                    $dateArray = [];
                                    
                                    while ($currentDate->lte($endDate)) {
                                        $dateArray[] = $currentDate->format('Y-m-d');
                                        $currentDate->addDay();
                                    }
                                ?>

                                <?php $__currentLoopData = $dateArray; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($date); ?></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="text-center"></td>
                                        <td></td>
                                        <td></td>
                                        <td class="text-center"></td>
                                        <td class="text-center"></td>
                                        <td></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>

                    
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Column Descriptions</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <ul class="list-unstyled">
                                                <li><strong>Quote Single:</strong> Single room quoted price</li>
                                                <li><strong>Quote SS:</strong> Single supplement quoted price</li>
                                                <li><strong>Quote HPP:</strong> Half-board per person quoted price</li>
                                                <li><strong>CMFD HOTEL:</strong> Confirmed hotel name</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <ul class="list-unstyled">
                                                <li><strong>Option:</strong> Option status</li>
                                                <li><strong>Offer SS/HPP:</strong> Offered prices</li>
                                                <li><strong>®:</strong> Registered/Confirmed</li>
                                                <li><strong>VC sent to SHA:</strong> Voucher/Confirmation sent</li>
                                                <li><strong>Budget HPP +/-:</strong> Budget variance</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="ti ti-info-circle"></i> No quotation data available for front sheet. Please create a quotation first.
                    </div>
                <?php endif; ?>
            </div>

            
            <div role="tabpanel" class="tab-pane" id="service-tab">
                <h3 class="mb-4"><i class="ti ti-list me-2"></i>Services</h3>
                
                
                <div class="btn-toolbar mb-3" role="toolbar">
                    <div class="btn-group me-2" role="group">
                        <button type="button" class="btn btn-success btn-sm" onclick="addDay()">
                            <i class="ti ti-plus me-1"></i>Day
                        </button>
                        <button type="button" class="btn btn-info btn-sm" onclick="addAllDays()">Add All</button>
                    </div>
                    <div class="btn-group me-2" role="group">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="exportCity()">City</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="exportExcel()">Excel</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="exportNumber()">Number</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="exportItinerary()">Itinerary</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="printAll()">Print All</button>
                    </div>
                </div>

                
                <?php $peopleCount = 0; ?>
                <?php $__currentLoopData = $listRoomsHotel; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $peopleCount += isset(App\TourPackage::$roomsPeopleCount[$room->room_types->code]) 
                            ? App\TourPackage::$roomsPeopleCount[$room->room_types->code] * $room->count 
                            : 0;
                    ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                
                <?php if($peopleCount != $tour->pax + $tour->pax_free): ?>
                    <div class="alert alert-warning alert-dismissible" role="alert">
                        <div class="d-flex">
                            <div>
                                <i class="ti ti-alert-triangle me-2"></i>
                            </div>
                            <div class="flex-fill">
                                <strong>Pax Count (<?php echo e($tour->pax + $tour->pax_free); ?>) is not equal to the number of people in the rooms (<?php echo e($peopleCount); ?>)</strong>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    </div>
                <?php endif; ?>

                
                <div id="service-days-container">
                    <?php
                        $startDate = \Carbon\Carbon::parse($tour->departure_date);
                        $endDate = \Carbon\Carbon::parse($tour->retirement_date);
                        $dayNumber = 1;
                        $currentDate = $startDate->copy();
                        
                        // Get all tour packages for this tour
                        $packages = collect();
                        if (method_exists($tour, 'tourPackages')) {
                            $packages = $tour->tourPackages;
                        } elseif (method_exists($tour, 'packages')) {
                            $packages = $tour->packages;
                        } elseif (isset($tour->packages)) {
                            $packages = collect($tour->packages);
                        }
                    ?>

                    <?php while($currentDate->lte($endDate)): ?>
                        <?php
                            $dayName = $currentDate->format('l');
                            $dateString = $currentDate->format('Y-m-d');
                            
                            // Get packages for this specific date
                            $dayPackages = $packages->filter(function($package) use ($dateString) {
                                $packageDate = isset($package->start_date) 
                                    ? \Carbon\Carbon::parse($package->start_date)->format('Y-m-d')
                                    : null;
                                return $packageDate === $dateString;
                            });
                        ?>

                        
                        <div class="card mb-4" data-day="<?php echo e($dayNumber); ?>" data-date="<?php echo e($dateString); ?>">
                            <div class="card-header bg-light">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h4 class="card-title mb-0">
                                            Day <?php echo e($dayNumber); ?> - <?php echo e($currentDate->format('F d, Y')); ?> (<?php echo e($dayName); ?>)
                                        </h4>
                                    </div>
                                    <div class="col-auto">
                                        <button class="btn btn-success btn-sm me-1" onclick="addDescription(<?php echo e($dayNumber); ?>, '<?php echo e($dateString); ?>')">
                                            <i class="ti ti-plus"></i> Add description
                                        </button>
                                        <button class="btn btn-primary btn-sm" onclick="addService(<?php echo e($dayNumber); ?>, '<?php echo e($dateString); ?>')">
                                            <i class="ti ti-download"></i> Add Service
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">
                                <?php if($dayPackages->count() > 0): ?>
                                    <?php $__currentLoopData = $dayPackages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $package): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="service-item mb-4 pb-4 border-bottom">
                                            
                                            <div class="mb-3">
                                                <?php if(isset($package->is_confirmed) && $package->is_confirmed): ?>
                                                    <i class="ti ti-check text-success me-2" title="Confirmed"></i>
                                                <?php endif; ?>
                                                <?php if(isset($package->is_voucher_sent) && $package->is_voucher_sent): ?>
                                                    <i class="ti ti-check text-success" title="Voucher Sent"></i>
                                                <?php endif; ?>
                                            </div>

                                            
                                            <?php if(isset($package->time) && $package->time): ?>
                                                <div class="mb-2">
                                                    <strong><?php echo e(\Carbon\Carbon::parse($package->time)->format('H:i')); ?></strong>
                                                </div>
                                            <?php endif; ?>

                                            
                                            <div class="service-details">
                                                <h5 class="mb-2">
                                                    <?php echo e($package->supplier->name ?? 'Service'); ?>

                                                    <?php if(isset($package->supplier->city)): ?>
                                                        - <?php echo e($package->supplier->city); ?>

                                                    <?php endif; ?>
                                                </h5>
                                                
                                                <?php if(isset($package->pax) && $package->pax): ?>
                                                    <div class="mb-2">
                                                        <strong>PAX:</strong> <?php echo e($package->pax); ?>

                                                    </div>
                                                <?php endif; ?>
                                                
                                                <?php if(isset($package->room_configuration) && $package->room_configuration): ?>
                                                    <div class="mb-2">
                                                        <strong>ROOM NO:</strong> <?php echo e($package->room_configuration); ?>

                                                    </div>
                                                <?php endif; ?>
                                                
                                                <?php if(isset($package->total_price) || isset($package->price_per_person)): ?>
                                                    <div class="mb-2">
                                                        <?php if(isset($package->total_price)): ?>
                                                            <strong>TTL:</strong> <?php echo e($package->total_price); ?><br>
                                                        <?php endif; ?>
                                                        <?php if(isset($package->price_per_person)): ?>
                                                            <strong>PSN/TTL:</strong> <?php echo e($package->price_per_person); ?>

                                                        <?php endif; ?>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <?php if(isset($package->driver_included)): ?>
                                                    <div class="mb-2">
                                                        <strong>DRIVER:</strong> <?php echo e($package->driver_included ? 'Yes' : 'No'); ?>

                                                    </div>
                                                <?php endif; ?>
                                                
                                                <?php if(isset($package->rooms_info) && $package->rooms_info): ?>
                                                    <div class="mb-2">
                                                        <strong>ROOMS:</strong><br>
                                                        <?php echo e($package->rooms_info); ?>

                                                    </div>
                                                <?php endif; ?>
                                                
                                                <?php if(isset($package->breakfast_info) && $package->breakfast_info): ?>
                                                    <div class="mb-2">
                                                        <strong>BREAKFAST:</strong> <?php echo e($package->breakfast_info); ?>

                                                    </div>
                                                <?php endif; ?>
                                                
                                                <?php if(isset($package->meals) && $package->meals): ?>
                                                    <div class="mb-2">
                                                        <strong>MEALS:</strong> <?php echo e($package->meals); ?>

                                                    </div>
                                                <?php endif; ?>
                                                
                                                <?php if(isset($package->special_meal) && $package->special_meal): ?>
                                                    <div class="mb-2">
                                                        <strong>SPECIAL MEAL Req:</strong> <?php echo e($package->special_meal); ?>

                                                    </div>
                                                <?php endif; ?>
                                                
                                                <?php if(isset($package->notes) && $package->notes): ?>
                                                    <div class="alert alert-info mb-2">
                                                        <?php echo nl2br(e($package->notes)); ?>

                                                    </div>
                                                <?php endif; ?>
                                                
                                                <?php if(isset($package->description) && $package->description): ?>
                                                    <div class="mt-3">
                                                        <div class="description-content" id="desc-<?php echo e($package->id); ?>">
                                                            <?php echo nl2br(e(Str::limit($package->description, 500))); ?>

                                                        </div>
                                                        <?php if(strlen($package->description) > 500): ?>
                                                            <a href="javascript:void(0)" class="text-primary" onclick="toggleDescription(<?php echo e($package->id); ?>)">
                                                                <small>readmore</small>
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>

                                            
                                            <div class="mt-3">
                                                <button class="btn btn-warning btn-sm me-1" onclick="editService(<?php echo e($package->id); ?>)">
                                                    <i class="ti ti-edit"></i>
                                                </button>
                                                <button class="btn btn-danger btn-sm" onclick="deleteService(<?php echo e($package->id); ?>)">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <div class="text-center text-muted py-4">
                                        <i class="ti ti-inbox fs-1"></i>
                                        <p class="mt-2">No services added for this day</p>
                                        <button class="btn btn-sm btn-primary" onclick="addService(<?php echo e($dayNumber); ?>, '<?php echo e($dateString); ?>')">
                                            <i class="ti ti-plus me-1"></i>Add First Service
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php
                            $currentDate->addDay();
                            $dayNumber++;
                        ?>
                    <?php endwhile; ?>
                </div>

                
                <div class="card mt-4">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ti ti-message me-2"></i><?php echo trans('main.Comments'); ?>

                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="show_comments"></div>
                    </div>
                    <div class="card-footer">
                        <form method="POST" action="<?php echo e(route('comment.store')); ?>" id="form_comment">
                            <?php echo csrf_field(); ?>
                            <div class="mb-3">
                                <textarea class="form-control" id="content" name="content" rows="3" placeholder="Ctrl + Enter to post comment"></textarea>
                            </div>
                            <input type="hidden" name="reference_id" value="<?php echo e($tour->id); ?>">
                            <input type="hidden" name="reference_type" value="<?php echo e(\App\Comment::$services['tour'] ?? 'tour'); ?>">
                            <button type="submit" class="btn btn-success">
                                <i class="ti ti-send me-1"></i><?php echo trans('main.Send'); ?>

                            </button>
                        </form>
                    </div>
                </div>
            </div>

            
            <div role="tabpanel" class="tab-pane" id="tour-tab">
                <h3 class="mb-4"><i class="ti ti-plane me-2"></i>Tour Information</h3>
                <div class="row">
                    <div class="col-md-6">
                        <table class="table card-table table-vcenter">
                            <tbody>
                                <tr>
                                    <td><strong><?php echo trans('main.Name'); ?></strong></td>
                                    <td><?php echo e($tour->name ?? '—'); ?></td>
                                </tr>
                                <tr>
                                    <td><strong><?php echo trans('main.ExternalName'); ?></strong></td>
                                    <td><?php echo e($tour->external_name ?? '—'); ?></td>
                                </tr>
                                <tr>
                                    <td><strong><?php echo trans('main.Pax'); ?></strong></td>
                                    <td><?php echo e($tour->pax ?? '—'); ?></td>
                                </tr>
                                <tr>
                                    <td><strong><?php echo trans('main.PaxFree'); ?></strong></td>
                                    <td><?php echo e($tour->pax_free ?? '—'); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table card-table table-vcenter">
                            <tbody>
                                <tr>
                                    <td><strong><?php echo trans('main.DepDate'); ?></strong></td>
                                    <td><?php echo e($tour->departure_date ?? '—'); ?></td>
                                </tr>
                                <tr>
                                    <td><strong><?php echo trans('main.RetDate'); ?></strong></td>
                                    <td><?php echo e($tour->retirement_date ?? '—'); ?></td>
                                </tr>
                                <tr>
                                    <td><strong><?php echo trans('main.Status'); ?></strong></td>
                                    <td><?php echo e($status->name ?? '—'); ?></td>
                                </tr>
                                <tr>
                                    <td><strong><?php echo trans('main.Phone'); ?></strong></td>
                                    <td><?php echo e($tour->phone ?? '—'); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            
            <div role="tabpanel" class="tab-pane" id="quotation-tab">
                <h3 class="mb-4"><i class="ti ti-calculator me-2"></i>Quotations</h3>
                <?php if(Auth::user()->can('quotation.add')): ?>
                    <a href="<?php echo e(route('quotation.add', ['id' => $tour->id])); ?>" class="btn btn-success mb-3">
                        <i class="ti ti-plus"></i> <?php echo trans('main.AddQuotation'); ?>

                    </a>
                <?php endif; ?>
                
                <table class="table card-table table-vcenter table-striped">
                    <thead>
                        <tr>
                            <th><?php echo trans('main.Name'); ?></th>
                            <th><?php echo trans('main.Assigned'); ?></th>
                            <th><?php echo trans('main.Frontsheet'); ?></th>
                            <th><?php echo trans('main.Print'); ?></th>
                            <th>Excel</th>
                            <th><?php echo trans('main.CreatedAt'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $tour->quotations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $quotation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr style="background-color: <?php echo e($quotation->is_confirm == 0 ? '#ff00008f' : '#caffbd'); ?>">
                                <td>
                                    <?php if(Auth::user()->can('quotation.edit')): ?>
                                        <a href="<?php echo e(route('quotation.edit', ['quotation' => $quotation->id])); ?>">
                                            <?php echo e($quotation->name ?? '—'); ?>

                                        </a>
                                    <?php else: ?>
                                        <?php echo e($quotation->name ?? '—'); ?>

                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($quotation->userName() ?? '—'); ?></td>
                                <td>
                                    <?php if(Auth::user()->can('comparison.show')): ?>
                                        <a href="<?php echo e(route('comparison.show', ['comparison' => $quotation->id])); ?>">
                                            View Front Sheet
                                        </a>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?php echo e(route('quotation.pdf', ['id' => $quotation->id])); ?>" target="_blank" class="btn btn-sm btn-primary">
                                        <i class="ti ti-printer"></i>
                                    </a>
                                </td>
                                <td>
                                    <a href="<?php echo e(route('quotation.excel', ['id' => $quotation->id])); ?>" target="_blank" class="btn btn-sm btn-success">
                                        <i class="ti ti-file-excel"></i>
                                    </a>
                                </td>
                                <td><?php echo e(Carbon\Carbon::parse($quotation->created_at)->format('d-m-Y')); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="text-center">
                                    <i class="ti ti-inbox"></i> No quotations found
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            
            <div role="tabpanel" class="tab-pane" id="roomlist-tab">
                <h3 class="mb-4"><i class="ti ti-bed me-2"></i>Guest Lists</h3>
                <?php if(Auth::user()->can('guestList.add')): ?>
                    <a href="<?php echo e(route('guestList.add', ['id' => $tour->id])); ?>" class="btn btn-success mb-3">
                        <i class="ti ti-plus"></i> Add Guest List
                    </a>
                <?php endif; ?>
                
                <table class="table card-table table-vcenter table-striped">
                    <thead>
                        <tr>
                            <th>Version</th>
                            <th><?php echo trans('main.Name'); ?></th>
                            <th><?php echo trans('main.Author'); ?></th>
                            <th><?php echo trans('main.CreatedAt'); ?></th>
                            <th><?php echo trans('main.SentAt'); ?></th>
                            <th><?php echo trans('main.Hotels'); ?></th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $tour->guestLists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $guestList): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($guestList->version); ?></td>
                                <td>
                                    <?php if(Auth::user()->can('guestList.showbyid')): ?>
                                        <a href="<?php echo e(route('guestList.showbyid', ['id' => $guestList->id])); ?>">
                                            <?php echo e($guestList->name); ?>

                                        </a>
                                    <?php else: ?>
                                        <?php echo e($guestList->name); ?>

                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($guestList->getAuthor()->name ?? '—'); ?></td>
                                <td><?php echo e(Carbon\Carbon::parse($guestList->created_at)->format('d-m-Y')); ?></td>
                                <td>
                                    <?php if($guestList->sent_at): ?>
                                        <?php echo e(Carbon\Carbon::parse($guestList->sent_at)->format('d-m-Y')); ?>

                                    <?php else: ?>
                                        <span class="text-muted">Not sent</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php $__currentLoopData = $guestList->getSelectedHotelNames(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $hotelName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php echo e($hotelName); ?><?php echo e($index < count($guestList->getSelectedHotelNames()) - 1 ? ', ' : ''); ?>

                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </td>
                                <td>
                                    <?php if(!$guestList->sent_at): ?>
                                        <button class="btn btn-sm btn-primary send-guest-list" 
                                                data-url="<?php echo e(route('guestlist.send', ['id' => $tour->id, 'guestlistid' => $guestList->id])); ?>">
                                            <i class="ti ti-send"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger delete-guest-list"
                                                data-url="<?php echo e(route('guestlist.delete', ['id' => $tour->id, 'guestlistid' => $guestList->id])); ?>">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="text-center">
                                    <i class="ti ti-inbox"></i> No guest lists found
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            
            <div role="tabpanel" class="tab-pane" id="invoices-tab">
                <h3 class="mb-4"><i class="ti ti-file-invoice me-2"></i>Invoices</h3>
                <?php echo \App\Helper\PermissionHelper::getCreateButton(route('invoices.create'), \App\Invoices::class, 'btn btn-success mb-3'); ?>

                
                <table class="table card-table table-vcenter table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Invoice No</th>
                            <th>Due Date</th>
                            <th>Received Date</th>
                            <th>Service</th>
                            <th>Office</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $invoicesData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($invoice['id']); ?></td>
                                <td><?php echo e($invoice['invoice_no']); ?></td>
                                <td><?php echo e($invoice['due_date']); ?></td>
                                <td><?php echo e($invoice['received_date']); ?></td>
                                <td><?php echo e($invoice['package_name']); ?></td>
                                <td><?php echo e($invoice['office_name']); ?></td>
                                <td><?php echo e($invoice['total_amount']); ?></td>
                                <td><?php echo e($invoice['status']); ?></td>
                                <td>
                                    <a href="<?php echo e(route('invoices.show', ['invoice' => $invoice['id']])); ?>" class="btn btn-sm btn-info">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                    <a href="<?php echo e(route('invoices.edit', ['invoice' => $invoice['id']])); ?>" class="btn btn-sm btn-warning">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="9" class="text-center">
                                    <i class="ti ti-inbox"></i> No invoices found
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            
            <div role="tabpanel" class="tab-pane" id="billing-tab">
                <h3 class="mb-4"><i class="ti ti-cash me-2"></i>Billing</h3>
                <?php echo \App\Helper\PermissionHelper::getCreateButton(route('accounting.create'), \App\Tour::class, 'btn btn-success mb-3'); ?>

                
                <table class="table card-table table-vcenter table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Office</th>
                            <th>Total Amount</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $billingData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $billing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($billing['id']); ?></td>
                                <td><?php echo e(\Carbon\Carbon::parse($billing['date'] ?? now())->format('Y-m-d')); ?></td>
                                <td><?php echo e($billing['office_name']); ?></td>
                                <td><?php echo e($billing['total_amount']); ?></td>
                                <td>
                                    <a href="<?php echo e(route('accounting.show', ['accounting' => $billing['id']])); ?>" class="btn btn-sm btn-info">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                    <a href="<?php echo e(route('accounting.edit', ['accounting' => $billing['id']])); ?>" class="btn btn-sm btn-warning">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="text-center">
                                    <i class="ti ti-inbox"></i> No billing records found
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<span id="tour_date_id" data-tour-id="<?php echo e($tour->id); ?>" hidden></span>
<span id="tour_dates" data-departure_date="<?php echo e($tour->departure_date); ?>" data-retirement_date="<?php echo e($tour->retirement_date); ?>" hidden></span>


<div class="modal modal-blur fade" id="service-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo trans('main.Addservice'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table id="search-table" class="table card-table table-vcenter table-striped table-bordered">
                    <thead>
                        <tr>
                            <th><?php echo trans('main.Name'); ?></th>
                            <th><?php echo trans('main.Address'); ?></th>
                            <th><?php echo trans('main.Country'); ?></th>
                            <th><?php echo trans('main.City'); ?></th>
                            <th><?php echo trans('main.Phone'); ?></th>
                            <th><?php echo trans('main.ContactName'); ?></th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="landingpage_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Warning</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>There is no image for landing page. Are you sure you want to generate the page?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="open-landing" onclick='export_to("<?php echo e(route('landing_page', ['id' => $tour->id])); ?>");'>
                    Agree
                </button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="question_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Warning</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Would you like to send Guest List to selected tour hotels?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="send_agree">Agree</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="error_send" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title_modal_error">Warning!</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h3 class="error_send_message"></h3>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('post_scripts'); ?>
<script src="<?php echo e(asset('js/tour-interactions.js')); ?>"></script>
<script src="<?php echo e(asset('js/supplier-search.js')); ?>"></script>
<script src="<?php echo e(asset('js/tour.js')); ?>"></script>
<script src="<?php echo e(asset('js/comment.js')); ?>"></script>
<script src="<?php echo e(asset('js/attachments.js')); ?>"></script>

<script>
$(document).ready(function() {
    // Tab persistence
    var activeTab = localStorage.getItem('tourActiveTab') || 'frontsheet-tab';
    $('.nav-tabs a[href="#' + activeTab + '"]').tab('show');
    
    $('.nav-tabs a').on('shown.bs.tab', function(e) {
        var tabId = $(e.target).attr('href').substring(1);
        localStorage.setItem('tourActiveTab', tabId);
    });

    // Office Selection
    $('.select-office-btn').on('click', function() {
        let officeId = $('.selectedOffice').val();
        if (officeId) {
            $.ajax({
                url: '/update-status/' + officeId,
                type: 'GET',
                success: function() {
                    location.reload(true);
                },
                error: function(xhr, status, error) {
                    console.error('Error updating office:', error);
                    alert('Error updating office. Please try again.');
                }
            });
        }
    });

    // Send Guest List
    var selectedGuestList;
    $('.send-guest-list').on('click', function() {
        selectedGuestList = $(this);
        var bootstrapModal = new bootstrap.Modal(document.getElementById('question_modal'));
        bootstrapModal.show();
    });

    $('#send_agree').on('click', function() {
        if (!selectedGuestList) return;
        
        let overlay = '<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>';
        let container = selectedGuestList.closest('.card-body');
        container.append(overlay);
        selectedGuestList.hide();
        
        var questionModal = bootstrap.Modal.getInstance(document.getElementById('question_modal'));
        questionModal.hide();
        
        $.ajax({
            method: 'GET',
            url: selectedGuestList.data('url'),
            success: function(res) {
                $('#error_send').find('#title_modal_error').html(res.error === 'error' ? 'Warning!' : 'Success!');
                $('#error_send').find('.error_send_message').html(res.message);
                if (res.broke) {
                    $('#error_send').find('.error_send_message').append('<br><br>' + res.broke);
                }
                $('.overlay').remove();
                
                var errorModal = new bootstrap.Modal(document.getElementById('error_send'));
                errorModal.show();
                
                setTimeout(function() {
                    errorModal.hide();
                    if (res.error !== 'error') {
                        location.reload();
                    } else {
                        selectedGuestList.show();
                    }
                }, 3000);
            },
            error: function(xhr, status, error) {
                $('.overlay').remove();
                console.error('Error sending guest list:', error);
                alert('Error sending guest list. Please try again.');
                selectedGuestList.show();
            }
        });
    });

    // Delete Guest List
    $('.delete-guest-list').on('click', function() {
        if (confirm('Are you sure you want to delete this guest list?')) {
            let url = $(this).data('url');
            $.ajax({
                method: 'GET',
                url: url,
                success: function() {
                    location.reload(true);
                },
                error: function(xhr, status, error) {
                    console.error('Error deleting guest list:', error);
                    alert('Error deleting guest list. Please try again.');
                }
            });
        }
    });

    // Comment form submission with Ctrl+Enter
    $('#content').on('keydown', function(e) {
        if (e.ctrlKey && e.keyCode === 13) {
            $('#form_comment').submit();
        }
    });
});

// Handle Toggle Conversion - FIXED VERSION
function handleToggleConversion(checkbox, isCurrentlyQuotation) {
    var url;
    var confirmMessage;
    
    if (isCurrentlyQuotation) {
        // Currently a quotation
        if (checkbox.checked) {
            // Convert to Tour (Go Ahead)
            url = "<?php echo e(route('tour.convert_to_tour', ['id' => $tour->id])); ?>";
            confirmMessage = "Are you sure you want to convert this Quotation to Tour (Go Ahead)?";
        } else {
            // Stay as Quotation
            checkbox.checked = true;
            return;
        }
    } else {
        // Currently a tour
        if (checkbox.checked) {
            // Convert to Quotation
            url = "<?php echo e(route('tour.convertToQuotation', ['id' => $tour->id])); ?>";
            confirmMessage = "Are you sure you want to convert this Tour to Quotation?";
        } else {
            // Stay as Tour
            checkbox.checked = false;
            return;
        }
    }
    
    if (confirm(confirmMessage)) {
        // Show loading indicator
        var loadingHtml = '<div class="position-fixed top-50 start-50 translate-middle" style="z-index: 9999;">' +
                         '<div class="spinner-border text-primary" role="status">' +
                         '<span class="visually-hidden">Loading...</span>' +
                         '</div></div>';
        $('body').append(loadingHtml);
        
        $.ajax({
            type: 'GET',
            url: url,
            success: function(response) {
                console.log('Conversion successful:', response);
                location.reload();
            },
            error: function(xhr, status, error) {
                console.error('Conversion error:', {
                    status: status,
                    error: error,
                    response: xhr.responseText
                });
                
                // Remove loading indicator
                $('.spinner-border').parent().remove();
                
                // Revert checkbox state
                checkbox.checked = !checkbox.checked;
                
                // Show error message
                var errorMessage = 'Error converting tour status.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    errorMessage += ' ' + xhr.responseText;
                }
                
                alert(errorMessage + ' Please check the console for more details.');
            }
        });
    } else {
        // User cancelled, revert checkbox
        checkbox.checked = !checkbox.checked;
    }
}

// Show Landing Page Modal
function showLandingPageModal() {
    var img = "<?php echo e($tour->attachments()->first() ? $tour->attachments()->first()->url : ''); ?>";
    if (!img) {
        var modal = new bootstrap.Modal(document.getElementById('landingpage_modal'));
        modal.show();
    } else {
        window.open("<?php echo e(route('landing_page', ['id' => $tour->id])); ?>", '_blank');
    }
}

// Export function
function export_to(url) {
    window.open(url, '_blank');
}

// Scroll position persistence
$(window).on('scroll', function() {
    localStorage.setItem('tourScrollPosition', $(window).scrollTop());
});

$(document).ready(function() {
    var scrollPos = localStorage.getItem('tourScrollPosition');
    if (scrollPos) {
        $(window).scrollTop(parseInt(scrollPos));
    }
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('scaffold-interface.layouts.tabler-app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xamppp\htdocs\dev-eetstravel\resources\views/tour/show.blade.php ENDPATH**/ ?>