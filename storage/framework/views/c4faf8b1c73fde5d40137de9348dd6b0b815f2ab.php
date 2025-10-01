<?php $__env->startSection('title', 'Show'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('layouts.title', [
        'title' => 'Tour',
        'sub_title' => $tour->name,
        'breadcrumbs' => [
            ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
            ['title' => 'Tours', 'icon' => 'suitcase', 'route' => route('tour.index')],
            ['title' => 'Show', 'route' => null],
        ],
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    

    <div class="modal fade" role='dialog' id="service-modal" style="padding-left: 17px;padding-right: 17px;">
        <div class="modal-dialog modal-lg" role='document' style="width: 90%;">
            <div class="modal-content" style="overflow: hidden;">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss='modal' aria-label="Close"><span
                            aria-hidden='true'>&times;</span></button>
                    <h4 class="modal-title"><?php echo trans('main.Addservice'); ?></h4>
					
					
                    
                    <form action="<?php echo e(route('supplier_show')); ?>">
                        <div class="form-group" style="margin: 15px;">
                            <div class="form-check" style="display:inline;">
                                <input id="service-selec" type="radio" class="form-check-input option-radio"
                                    name="selected_options[]" value="All">
                                <label class="form-check-label options-label" for="service-select">
                                    All
                                </label>
                            </div>
                            <?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="form-check" style="display:inline; margin: 12px;">
                                    <input type="radio" class="form-check-input option-radio"
                                        id="<?php echo e(strtolower($option)); ?>-checkbox" name="selected_options[]"
                                        value="<?php echo e($option); ?>">
                                    <label class="form-check-label options-label" for="<?php echo e(strtolower($option)); ?>-checkbox">
                                        <?php if($option === 'Transfer'): ?>
                                            Bus Company
                                        <?php else: ?>
                                            <?php echo e($option); ?>

                                        <?php endif; ?>
                                    </label>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </form>
					<div id="hotel_service_create" style="display: none;">
						<a class="btn btn-success" href="<?php echo e(route('hotel.create')); ?>" target="_blank">
                <i class="fa fa-plus fa-md" aria-hidden="true"></i> New</a>
					</div>
					<div id="guide_service_create" style="display: none;">
						<a class="btn btn-success" href="<?php echo e(route('guide.create')); ?>" target="_blank">
                <i class="fa fa-plus fa-md" aria-hidden="true"></i> New</a>
					</div>
					<div id="event_service_create" style="display: none;">
						<a class="btn btn-success" href="<?php echo e(route('event.create')); ?>" target="_blank">
                <i class="fa fa-plus fa-md" aria-hidden="true"></i> New</a>
					</div>
					<div id="res_service_create" style="display: none;">
						<a class="btn btn-success" href="<?php echo e(route('restaurant.create')); ?>" target="_blank">
                <i class="fa fa-plus fa-md" aria-hidden="true"></i> New</a>
					</div>
					<div id="bus_service_create" style="display: none;">
						<a class="btn btn-success" href="<?php echo e(route('bus.create')); ?>" target="_blank">
                <i class="fa fa-plus fa-md" aria-hidden="true"></i> New</a>
					</div>
                    
                </div>
                <div class="box box-body table-responsive" style="border-top: none">
                    <table id="search-table" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th><?php echo trans('main.Name'); ?></th>
                                <th><?php echo trans('main.Address'); ?></th>
                                <th><?php echo trans('main.Country'); ?></th>
                                <th><?php echo trans('main.City'); ?></th>
                                <th><?php echo trans('main.Phone'); ?></th>
                                <th><?php echo trans('main.ContactName'); ?></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th><?php echo trans('main.Name'); ?></th>
                                <th><?php echo trans('main.Address'); ?></th>
                                <th><?php echo trans('main.Country'); ?></th>
                                <th><?php echo trans('main.City'); ?></th>
                                <th><?php echo trans('main.Phone'); ?></th>
                                <th><?php echo trans('main.ContactName'); ?></th>
                                <th></th>
                            </tr>
                        </tfoot>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade in" id="question_modal" tabindex="-1" role="dialog" aria-labelledby="myQuestionLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">×</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo trans('main.Warning'); ?>!!</h4>
                </div>
                <div class="modal-body">
                    <div class="modal-body"><?php echo trans('main.WouldyouliketosendGuestList'); ?>?</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo trans('main.Close'); ?></button>
                    <button type="button" class="btn btn-primary" id="send_agree"><?php echo trans('main.Agree'); ?></button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="error_send">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss='modal' aria-label="Close"><span
                            aria-hidden='true'>&times;</span></button>
                    <h4 class="modal-title" id="title_modal_error"><?php echo trans('main.Warning'); ?>!</h4>
                </div>
                <div class="modal-body">
                    <h3 class="error_send_message"></h3>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" role='dialog' id="service-description">
        <div class="modal-dialog" role='document'  style="
    width: 90%;
">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss='modal' aria-label="Close"><span
                            aria-hidden='true'>&times;</span></button>
                    <h4 class="modal-title"><?php echo trans('main.Adddescriptionpackage'); ?></h4>
                </div>
                <div class="box box-body" style="border-top: none">
                    <form action="<?php echo e(route('description_package')); ?>" method="Post" id="description-service">
						  <?php echo e(csrf_field()); ?>

                        <div class="form-group">
                            <label for="description"><?php echo trans('main.Text'); ?></label>
							<h2>Select Time</h2>
							<label for="appt">Select a time:</label>
<input type="time" id="time" name="time">
							<div class="form-group">
                        	<div class="input-group">
							 <span class="input-group-addon"> <?php echo trans('main.Template'); ?></span>
							<select id="desc_template_selector" name="desc_template_selector" class="form-control">
                            </select>
								</div>
							</div>
                            
                            <textarea name="description" id="description"  class="form-control" style="width: 100%; resize: vertical;"></textarea>
                        </div>
                        <input type="text" hidden="hidden" id="tour_day_id" name="tourDayId">
                        <button type="submit" class="btn btn-primary pre-loader-func"><?php echo trans('main.Create'); ?></button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" role='dialog' id="service-description-edit">
        <div class="modal-dialog" role='document'>
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss='modal' aria-label="Close"><span
                            aria-hidden='true'>&times;</span></button>
                    <h4 class="modal-title"><?php echo trans('main.Editdescriptionpackage'); ?></h4>
                </div>
                <div class="box box-body" style="border-top: none">
                    <form action="<?php echo e(route('description_package')); ?>" method="Post" id="description-service-edit">
						<?php echo e(csrf_field()); ?>

                        <div class="form-group">
                            <label for="description-edit">Text</label>
                            
                            <textarea name="description-edit" id="description-edit" class="form-control" style="width: 100%; resize: vertical;"></textarea>
                        </div>
                        <input type="text" hidden="hidden" id="tour_day_id" name="tourDayId">
                        <button type="button" class="btn btn-primary save-description"><?php echo trans('main.Save'); ?></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="box box-primary">
            <div class="box box-body border_top_none">
                <div class="row">
                    <div class="col-md-3" style="margin-bottom: 10px;">
                        <div class="margin_button">
                            <a href="javascript:history.back()">
                                <button class='btn btn-primary'><?php echo trans('main.Back'); ?></button>
                            </a>
                            <?php if(Auth::user()->can('tour.edit')): ?>
                                <a href="<?php echo route('tour.edit', ['tour' => $tour->id]); ?>">
                                    <button class='btn btn-warning'><?php echo trans('main.Edit'); ?></button>
                                </a>
                            <?php endif; ?>
                            <?php if(Auth::user()->can('task.create')): ?>
                                <a href="<?php echo url('task'); ?>/create?tour=<?php echo $tour->id; ?>"
                                    class='btn btn-success'><?php echo trans('main.AddTask'); ?></a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <ul>
                            <li style="display: inline-block;">
                                <div class="dropdown">
                                    <button class="btn btn-default" id="csvLabel" type="button"
                                        data-toggle="dropdown">
                                        image
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="csvLabel">
                                        <li><a class="btn btn-default" href="#"
                                                onclick='export_to("<?php echo e(route('tour_export', ['id' => $tour->id, 'export' => 'csv', 'type' => 'tour'])); ?>");'
                                                href="">Tour</a></li>
                                        <li><a class="btn btn-default"
                                                onclick='export_to("<?php echo e(route('tour_export', ['id' => $tour->id, 'export' => 'csv', 'type' => 'service'])); ?>");'
                                                href="#">Service</a></li>
                                    </ul>
                                </div>
                            </li>
                            <li style="display: inline-block;"><a class="btn btn-default"
                                    onclick='export_to("<?php echo e(route('tour_export', ['id' => $tour->id, 'export' => 'xlsx'])); ?>");'
                                    href="#">Excel</a></li>
                            
							<li style="display: inline-block;">
                                <div class="dropdown">
                                    <button class="btn btn-default" id="voucherLabel" type="button"
                                        data-toggle="dropdown">
                                        <?php echo trans('main.Voucher'); ?>

                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="csvLabel">
                                        <li><a class="btn btn-default"
                                                onclick='export_to("<?php echo e(route('tour_pdf_export', ['id' => $tour->id, 'pdf_type' => 'voucher'])); ?>");'
                                                href="#">PDF</a></li>
                                        <li><a class="btn btn-default"
                                                onclick='export_to("<?php echo e(route('tour_doc_export', ['id' => $tour->id, 'doc_type' => 'voucher'])); ?>");'
                                                href="#">DOC</a></li>
                                    </ul>
                                </div>
                            </li>
                            <li style="display: inline-block;">
                            </li>
							

                            <li style="display: inline-block;">
                                <div class="dropdown">
                                    <button class="btn btn-default" id="itenaryLabel" type="button"
                                        data-toggle="dropdown">
                                        <?php echo trans('main.Itinerary'); ?>

                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="csvLabel">
                                        <li><a class="btn btn-default"
                                                onclick='export_to("<?php echo e(route('tour_pdf_export', ['id' => $tour->id, 'pdf_type' => 'short'])); ?>");'
                                                href="#">PDF</a></li>
                                        <li><a class="btn btn-default"
                                                onclick='export_to("<?php echo e(route('tour_html_export', ['id' => $tour->id, 'type' => 'html'])); ?>");'
                                                href="#">HTML</a></li>
                                        <li><a class="btn btn-default"
                                                onclick='export_to("<?php echo e(route('tour_doc_export', ['id' => $tour->id, 'doc_type' => 'short'])); ?>");'
                                                href="#">DOC</a></li>
                                    </ul>
                                </div>
                            </li>
							
							<li style="display: inline-block;">
                                <div class="dropdown">
                                    <button class="btn btn-default" id="hotellistLabel" type="button"
                                        data-toggle="dropdown">
                                        <?php echo trans('main.Hotellist'); ?>

                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="csvLabel">
                                        <li><a class="btn btn-default"
                                                onclick='export_to("<?php echo e(route('tour_pdf_export', ['id' => $tour->id, 'pdf_type' => 'hotel'])); ?>");'
                                                href="#">PDF</a></li>
                                        <li><a class="btn btn-default"
                                                onclick='export_to("<?php echo e(route('tour_doc_export', ['id' => $tour->id, 'doc_type' => 'hotel'])); ?>");'
                                                href="#">DOC</a></li>
                                    </ul>
                                </div>
                            </li>
							
                            
                            <li style="display: inline-block;"><button onclick="showmodal()"
                                    class="btn btn-default">Landing page</button></li>
                            <a id = "quotation_to_tour_href"
                                href="<?php echo e(route('tour.convert_to_tour', ['id' => $tour->id])); ?>"></a>
                            <a id = "tour_to_quotation"
                                href="<?php echo e(route('tour.convertToQuotation', ['id' => $tour->id])); ?>"></a>
                        </ul>
                    </div>
                    <div class="col-md-4">
						
                        <span id="help" class="legend_tour btn btn-box-tool pull-right">
                            <i class="fa fa-question-circle " aria-hidden="true"></i>
							
                            <?php echo $__env->make('legend.tour_service_legend', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        </span>
						<h2>Select Office</h2>
						<div>
							
						<select class="selectedOfice">
							<?php $__currentLoopData = $offices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $office): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<?php if(isset( $select_office->id)): ?>
							<option value="<?php echo e($office->id); ?>" <?php echo e(( $office->id == $select_office->id) ? 'selected' : ''); ?>><?php echo e($office->office_name); ?></option>
							<?php else: ?>
							<option value="<?php echo e($office->id); ?>"><?php echo e($office->office_name); ?></option>
							<?php endif; ?>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						</select>
							
							<button class="btn btn-primary btn-sm select" style="margin-bottom:5px;margin-top:5px;">Select</button>
						</div>
                    </div>
                </div>
                <?php if($tour->is_quotation): ?>
                    <div class="callout callout-info" style="
    background: rgb(255, 249, 176) !important;color:black !important;
">
                        <div class="row">
                            <div class="col-md-8">
                                <h5>Convert Quotation to Tour</h5>
                            </div>
                            <div class="col-md-4">
                                <div class="toggle" style = "float:right; margin-right:30px">
                                    <input type="checkbox" id= "check1" onclick = "quotation_to_tour()" checked />
                                    <label></label>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="callout callout-info" style="
    background:rgb(202, 255, 189) !important; color:black !important;
">
                        <div class="row">
                            <div class="col-md-8">
                                <h5>Convert Tour to Quotation</h5>
                            </div>
                            <div class="col-md-4">
                                <div class="toggle" style = "float:right; margin-right:30px">
                                    <input type="checkbox" id= "check2" onclick = "tour_to_quotation()" />
                                    <label></label>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="row">

                    <div class="col-md-12 info-row"></div>
                    <div class="col-md-12">
                        <div class="alert alert-warning alert-dismissable msg" hidden>
                            <button type="button" class="close" data-dismiss='alert' aria-hidden='true'>x</button>
                        </div>
                    </div>
                </div>

                <div id="fixed-scroll" class="nav-tabs-custom">
                    <ul class="nav nav-tabs" role='tablist'>
                        <li role='presentation' class="tab" data-tab="frontsheet-tab"><a href="#frontsheet-tab" aria-controls='frontsheet-tab'
                                role='tab' data-toggle='tab' id="frontsheet_tab">Front Sheet</a></li>
                        <li role='presentation' class="  tab" data-tab="service-tab"><a href="#service-tab" aria-controls='service-tab' role='tab'
                                data-toggle='tab' id="service_tab" ><?php echo trans('main.Services'); ?></a></li>
                        <li role='presentation' class="tab" data-tab="tour-tab"><a href="#tour-tab" aria-controls='tour-tab' role='tab'
                                data-toggle='tab'><?php echo trans('main.Tour'); ?></a></li>
                        <li role='presentation' class="tab" data-tab="quotation-tab"><a href="#quotation-tab" aria-controls='quotation-tab' role='tab'
                                data-toggle='tab' id="quotation_tab"><?php echo trans('main.Quotation'); ?></a></li>
                        <li role='presentation' class="tab" data-tab="roomlist-tab"><a href="#roomlist-tab" aria-controls='roomlist-tab' role='tab'
                                data-toggle='tab' id="roomlist_tab" ><?php echo trans('main.GuestList'); ?></a></li>
                        <li role='presentation' class="tab" data-tab="invoices-tab"><a href="#invoices-tab" aria-controls='invoices-tab' role='tab'
                                data-toggle='tab' id="invoices_tab" ><?php echo trans('Invoices'); ?></a></li>
                        <li role='presentation' class="tab" data-tab="billing-tab"><a href="#billing-tab" aria-controls='invoices-tab' role='tab'
                                data-toggle='tab' id="billing_tab"><?php echo trans('Billing'); ?></a></li>

                    </ul>
                </div>
                <div class="tab-content">

                    <div role='tabpanel' class="tab-pane fade in" id="service-tab">
                        


                        <div class="tour-packages"></div>

                        
                        <?php echo $__env->make('component.list_tasks_for_tour', [
                            'listIdTasks' => $listIdTasks,
                            'tour' => $tour,
                            'tasksData' => $tasksData,
                        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        <span id="showPreviewBlock" data-info="<?php echo e(true); ?>"></span>
                        <div class="box box-success" style="position: relative; left: 0px; top: 0px; border-top: none">
                            <div class="box-header ui-sortable-handle" style="cursor: move;">
                                <i class="fa fa-comments-o"></i>

                                <h3 class="box-title"><?php echo trans('main.Comments'); ?></h3>
                            </div>
                            <div class="box-body">
                                <div class="slimScrollDiv" style="position: relative; overflow-y: scroll;  width: auto;">
                                    <div class="box-body box chat" id="chat-box" style="width: auto; height: auto;">
                                        <div id="show_comments"></div>
                                    </div>
                                    <div class="slimScrollRail"
                                        style="width: 7px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; background: rgb(51, 51, 51) none repeat scroll 0% 0%; opacity: 0.2; z-index: 90; right: 1px;">
                                    </div>
                                </div>
                            </div>
                            <!-- /.chat -->
                            <div class="box-footer">
                                <form method='POST' action='<?php echo e(route('comment.store')); ?>' enctype="multipart/form-data"
                                    id="form_comment">
                                    <div class="input-group" style="width: 100%">
                                        <span id="author_name" class="input-group-addon">
                                            <span id="name"></span>
                                            <a href="#" id="reply_close"><i class="fa fa-close"></i></a>
                                        </span>
                                        <textarea class="form-control" id="content" name="content" placeholder="Ctrl + Enter to post comment"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label><?php echo trans('main.Files'); ?></label>
                                        <?php $__env->startComponent('component.file_upload_field'); ?>
                                        <?php echo $__env->renderComponent(); ?>
                                    </div>
                                    <input type="text" id="parent_comment" hidden name="parent"
                                        value="<?php echo e(null); ?>">
                                    <input type="text" id="default_reference_id" hidden name="reference_id"
                                        value="<?php echo e($tour->id); ?>">
                                    <input type="text" id="default_reference_type" hidden name="reference_type"
                                        value="<?php echo e(\App\Comment::$services['tour']); ?>">

                                    <button type="submit" class="btn btn-success pull-right" id="btn_send_comment"
                                        style="margin-top: 5px;"><?php echo trans('main.Send'); ?>

                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div role='tabpanel' class="tab-pane fade" id="tour-tab">
                        <div class="row">
                            <div class="col-md-6">
                                <table class='table table-bordered' id="pdf-table">
                                    <tbody>
                                        <tr>
                                            <td><b><i><?php echo trans('main.Name'); ?></i></b></td>
                                            <td><?php echo $tour->name ?? ''; ?></td>
                                        </tr>
                                        <tr>
                                            <td><b><i><?php echo trans('main.ExternalName'); ?></i></b></td>
                                            <td><?php echo $tour->external_name ?? ''; ?></td>
                                        </tr>

                                        <tr>
                                            <td><b><i><?php echo trans('main.Pax'); ?></i></b></td>
                                            <td><?php echo $tour->pax ?? ''; ?></td>
                                        </tr>
                                        <tr>
                                            <td><b><i><?php echo trans('main.PaxFree'); ?></i></b></td>
                                            <td><?php echo $tour->pax_free ?? ''; ?></td>
                                        </tr>

                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class='table table-bordered' id="pdf-table">
                                    <tbody>
                                        <tr>
                                            <td><b><i><?php echo trans('main.DepDate'); ?></i></b></td>
                                            <td><?php echo $tour->departure_date ?? ''; ?></td>
                                        </tr>
                                        <tr>
                                            <td><b><i><?php echo trans('main.RetDate'); ?></i></b></td>
                                            <td><?php echo $tour->retirement_date ?? ''; ?></td>
                                        </tr>
                                        <tr>
                                            <td><b><i><?php echo trans('main.Status'); ?></i></b></td>
                                            <td><?php echo e($status->name); ?></td>
                                        </tr>

                                        <tr>
                                            <td><b><i><?php echo trans('main.RoomsHotel'); ?></i></b></td>
                                            <td>
                                                <?php
                                                    $peopleCount = 0;
                                                ?>
                                                <?php $__currentLoopData = $listRoomsHotel; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php
                                                        $peopleCount += isset(App\TourPackage::$roomsPeopleCount[$item->room_types->code]) ? App\TourPackage::$roomsPeopleCount[$item->room_types->code] * $item->count : 0;
                                                    ?>
                                                    <span>
                                                        <?php echo e($item->room_types->code ?? ''); ?>

                                                        <?php echo e($item->count ?? ''); ?>

                                                    </span>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <br>
                                                <?php if($peopleCount != $tour->pax + $tour->pax_free): ?>
                                                    <div class="alert alert-warning alert-dismissible">
                                                        <button type="button" class="close" data-dismiss="alert"
                                                            aria-hidden="true">×
                                                        </button>
                                                        <i class="icon fa fa-warning"></i>
                                                        Pax Count (<?php echo e($tour->pax + $tour->pax_free); ?>) is not equal to the
                                                        number of people in the rooms (<?php echo e($peopleCount); ?>)
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php if(Auth::user()->hasRole('admin')): ?>
                                            <tr>
                                                <td><b><i><?php echo trans('main.AssignedUser'); ?></i></b></td>
                                                <td>
                                                    <?php $__currentLoopData = $tour->users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php echo e($user->name); ?>

                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <tr>
                                                <td><b><i><?php echo trans('main.AssignedUser'); ?></i></b></td>
                                                <td>
                                                    <?php $__currentLoopData = $tour->users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php echo e($user->name ?? ''); ?>

                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </td>
                                            </tr>
                                        <?php endif; ?>

                                        <tr>
                                            <td><b><i><?php echo trans('main.Phone'); ?></i></b></td>
                                            <td>
                                                <?php echo $tour->phone ?? ''; ?>

                                            </td>
                                        </tr>

                                        <tr>
                                            <td><b><i><?php echo trans('main.ResponsibleUser'); ?></i></b></td>
                                            <td>
                                                <?php echo e($tour->getResponsibleUser() ? $tour->getResponsibleUser()->name : ''); ?>

                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <?php $__env->startComponent('component.files', ['files' => $files, 'tour' => $tour]); ?>
                        <?php echo $__env->renderComponent(); ?>
                    </div>

                    <div role='tabpanel' class="tab-pane fade" id="comments-tab">

                    </div>
                    <div role="tabpanel" class="tab-pane fade" id="quotation-tab">
                        <div class="box box-success">
                            <div class="box-header ui-sortable-handle" style="cursor: move;">
                                <i class="fa fa-comments-o"></i>

                                <h3 class="box-title"><?php echo trans('main.Quotations'); ?></h3>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <?php if(Auth::user()->can('quotation.add')): ?>
                                            <a href="<?php echo e(route('quotation.add', ['id' => $tour->id])); ?>">
                                                <button type="button"
                                                    class="btn btn-block btn-success btn-flat"><?php echo trans('main.AddQuotation'); ?>

                                                </button>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-9">
                                        <span class="quotation_legend btn btn-box-tool pull-right"><i
                                                class="fa fa-question-circle" aria-hidden="true"></i>
                                            <?php echo $__env->make('legend.quotation_legend', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                        </span>
                                    </div>
                                </div>
                                <br>
                                <table class="table table-bordered finder-disable">
                                    <tr>
                                        <td><?php echo trans('main.Name'); ?></td>
                                        <td><?php echo trans('main.Assigned'); ?></td>
                                        <td><?php echo trans('main.Frontsheet'); ?></td>
                                        <td><?php echo trans('main.Print'); ?></td>
                                        <td><?php echo trans('Excel'); ?></td>
                                        <td><?php echo trans('main.CreatedAt'); ?></td>
                                    </tr>
                                    <?php $__currentLoopData = $tour->quotations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $quotation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            if ($quotation->is_confirm == 0) {
                                                $style = 'background-color:#ff00008f';
                                            } else {
                                                $style = 'background-color:#caffbd';
                                            }

                                        ?>
                                        <tr style=<?php echo e($style); ?>>
                                            <td>
                                                <?php if(Auth::user()->can('quotation.edit')): ?>
                                                    <a href="<?php echo e(route('quotation.edit', ['quotation' => $quotation->id])); ?>">
                                                        <?php echo e($quotation->name ?? ''); ?>

                                                    </a>
                                                <?php else: ?>
                                                    <span><?php echo e($quotation->name ?? ''); ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo e($quotation->userName() ?? ''); ?></td>
                                            <td>
                                                <?php if(Auth::user()->can('comparison.show')): ?>
                                                    <a href="<?php echo e(route('comparison.show', ['comparison' => $quotation->id])); ?>"><?php echo trans('main.Front'); ?>

                                                        sheet</a>
                                                <?php else: ?>
                                                    <span><?php echo trans('main.Nopermission'); ?>.</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><a target="_blank"
                                                    href="<?php echo e(route('quotation.pdf', ['id' => $quotation->id])); ?>"
                                                    class="btn btn-primary btn-xs show-button"><i class="fa fa-print"
                                                        aria-hidden="true"></i></a>
                                            </td>
                                            <td><a target="_blank"
                                                    href="<?php echo e(route('quotation.excel', ['id' => $quotation->id])); ?>"
                                                    class="btn btn-primary btn-xs show-button"><i
                                                        class="fa fa-file-excel-o" aria-hidden="true"></i></a>
                                            </td>
                                            <td><?php echo e(Carbon\Carbon::parse($quotation->created_at)->format('d-m-Y')); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane fade" id="roomlist-tab">
                        <div class="box box-success">
                            <div class="box-header ui-sortable-handle" style="cursor: move;">
                                <i class="fa fa-address-card"></i>

                                <h3 class="box-title"><?php echo trans('main.Guestlists'); ?></h3>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <?php if(Auth::user()->can('guestList.add')): ?>
                                            <a href="<?php echo e(route('guestList.add', ['id' => $tour->id])); ?>">
                                                <button type="button"
                                                    class="btn btn-block btn-success btn-flat"><?php echo trans('main.Add'); ?>

                                                    <?php echo trans('main.Guestlist'); ?>

                                                </button>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-9">
                                        <span class="guest_list_legend btn btn-box-tool pull-right"><i
                                                class="fa fa-question-circle" aria-hidden="true"></i>
                                            <?php echo $__env->make('legend.guest_list_legend', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                        </span>
                                    </div>
                                </div>
                                <br>

                                <table class="table table-bordered finder-disable">
                                    <?php if(Auth::user()->can('guestlist.index')): ?>
                                        <tr>
                                            <td>Id</td>
                                            <td><?php echo trans('main.Name'); ?></td>
                                            <td><?php echo trans('main.Author'); ?></td>
                                            <td><?php echo trans('main.CreatedAt'); ?></td>
                                            <td><?php echo trans('main.SentAt'); ?></td>
                                            <td><?php echo trans('main.Hotels'); ?></td>
                                            <td><?php echo trans('main.Send'); ?></td>
                                        </tr>
                                        <?php $__currentLoopData = $tour->guestLists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $guestList): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td>
                                                    <?php echo e($guestList->version); ?>

                                                </td>
                                                <td>
                                                    <?php if(Auth::user()->can('guestList.showbyid')): ?>
                                                        <a href="<?php echo e(route('guestList.showbyid', ['id' => $guestList->id])); ?>"
                                                            class="pre-loader-func guest_list_show"><?php echo e($guestList->name); ?>

                                                        </a>
                                                    <?php else: ?>
                                                        <span><?php echo e($guestList->name ?? ''); ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php echo e($guestList->getAuthor()->name); ?> -
                                                    <?php echo e($guestList->getAuthor()->email); ?>

                                                </td>
                                                <td>
                                                    <?php echo e(Carbon\Carbon::parse($guestList->created_at)->format('d-m-Y')); ?>

                                                </td>
                                                <td class="sent_at">
                                                    <?php if($guestList->sent_at): ?>
                                                        <?php echo e(Carbon\Carbon::parse($guestList->sent_at)->format('d-m-Y')); ?>

                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php $__currentLoopData = $guestList->getSelectedHotelNames(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hotelName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php echo e($hotelName); ?>,
                                                        <?php if($loop->index > 0): ?>
                                                        <?php break; ?>
                                                    <?php endif; ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php if(Auth::user()->can('guestList.showbyid')): ?>
                                                    <a href="<?php echo e(route('guestList.showhotelemailsbyid', ['id' => $guestList->id])); ?>"
                                                        class="pre-loader-func guest_list_show_email_hotels">
                                                        more...</a>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if(!$guestList->sent_at): ?>
                                                    <button
                                                        data-url="<?php echo e(route('guestlist.send', ['id' => $tour->id, 'guestlistid' => $guestList->id])); ?>"
                                                        class="btn btn-primary btn-xs aftersend">
                                                        <i class="fa fa-paper-plane-o" aria-hidden="true"></i>
                                                    </button>
                                                    <button class="btn btn-danger btn-xs delete_guest_list"
                                                        data-url="<?php echo e(route('guestlist.delete', ['id' => $tour->id, 'guestlistid' => $guestList->id])); ?>">
                                                        <i class="fa fa-trash-o" aria-hidden="true"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    No permission.
                                <?php endif; ?>
                            </table>

                        </div>
                    </div>
                </div>

                <div role="tabpanel" class="tab-pane fade in" id="invoices-tab">
					<div id="tour_create" style="margin-bottom : 20px;">
                    <?php echo \App\Helper\PermissionHelper::getCreateButton(route('invoices.create'), \App\Invoices::class); ?>

                </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover bootstrap-table" data-search="true" data-pagination="true" data-page-size="10">
                            <thead>
                                <tr>
                                    <th data-sortable="true">ID</th>
                                    <th data-sortable="true">Invoice No</th>
                                    <th data-sortable="true">Due Date</th>
                                    <th data-sortable="true">Received Date</th>
                                    <th data-sortable="true">Tour</th>
                                    <th data-sortable="true">Service</th>
                                    <th data-sortable="true">Office Name</th>
                                    <th data-sortable="true">Total Price</th>
                                    <th data-sortable="true">Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $invoicesData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($invoice['id']); ?></td>
                                    <td><?php echo e($invoice['invoice_no']); ?></td>
                                    <td><?php echo e($invoice['due_date']); ?></td>
                                    <td><?php echo e($invoice['received_date']); ?></td>
                                    <td><?php echo e($invoice['tour_name']); ?></td>
                                    <td><?php echo e($invoice['package_name']); ?></td>
                                    <td><?php echo e($invoice['office_name']); ?></td>
                                    <td><?php echo e($invoice['total_amount']); ?></td>
                                    <td><?php echo e($invoice['status']); ?></td>
                                    <td>
                                        <a href="<?php echo e(route('invoices.show', ['invoice' => $invoice['id']])); ?>" class="btn btn-info btn-sm">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="<?php echo e(route('invoices.edit', ['invoice' => $invoice['id']])); ?>" class="btn btn-warning btn-sm">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <a class="btn btn-danger btn-sm delete" data-toggle="modal" data-target="#myModal" data-link="/invoices/<?php echo e($invoice['id']); ?>/deleteMsg">
                                            <i class="fa fa-trash-o"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>

                </div>

                <div role="tabpanel" class="tab-pane fade" id="billing-tab">
					<div id="tour_create" style="margin-bottom : 20px;">
                    <?php echo \App\Helper\PermissionHelper::getCreateButton(route('accounting.create'), \App\Tour::class); ?>

                </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover bootstrap-table" data-search="true" data-pagination="true" data-page-size="10">
                            <thead>
                                <tr>
                                    <th data-sortable="true">ID</th>
                                    <th data-sortable="true">Date</th>
                                    <th data-sortable="true">Tour Name</th>
                                    <th data-sortable="true">Office Name</th>
                                    <th data-sortable="true">Total Amount</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $billingData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $billing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($billing['id']); ?></td>
                                    <td><?php echo e(\Carbon\Carbon::parse($billing['date'] ?? now())->format('Y-m-d')); ?></td>
                                    <td><?php echo e($billing['tour_name']); ?></td>
                                    <td><?php echo e($billing['office_name']); ?></td>
                                    <td><?php echo e($billing['total_amount']); ?></td>
                                    <td>
                                        <a href="<?php echo e(route('accounting.show', ['accounting' => $billing['id']])); ?>" class="btn btn-info btn-sm">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="<?php echo e(route('accounting.edit', ['accounting' => $billing['id']])); ?>" class="btn btn-warning btn-sm">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <a class="btn btn-danger btn-sm delete" data-toggle="modal" data-target="#myModal" data-link="/accounting/<?php echo e($billing['id']); ?>/deleteMsg">
                                            <i class="fa fa-trash-o"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>



                </div>
                <div role="tabpanel" class="tab-pane fade" id="frontsheet-tab">
                    <div class="box box-success">
                        <div class="box-header ui-sortable-handle" style="cursor: move;">


                            <div class="box-body">
                                <h2 class="page-header">
                                    <i class="fa fa-list" aria-hidden="true"></i> Front Sheet
                                    [<?php echo e($quotation->name ?? ''); ?> - <?php echo e($tour->name); ?>]
                                </h2>
                                <span id="help" class="btn btn-box-tool pull-right"><i
                                        class="fa fa-question-circle" aria-hidden="true"></i>
                                    <?php echo $__env->make('legend.frontsheet_legend', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                </span>
                                <?php if(!empty($quotation) && $quotation->id): ?>
                                <form action="<?php echo e(route('comparison.update', ['comparison' => $quotation->id])); ?>"
                                    method="POST">
                                    <div style="margin-bottom: 10px;">
                                        <div class="row">
                                            <div class="col-md-12">
                                                
                                            </div>
                                        </div>

                                    </div>

                                    <?php echo e(csrf_field()); ?>

                                    <?php echo e(method_field('PUT')); ?>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="lead">
                                                Rooms:
                                                <?php
                                                    $peopleCount = 0;
                                                ?>

                                                <?php $__currentLoopData = $listRoomsHotel; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php
                                                        $peopleCount += isset(App\TourPackage::$roomsPeopleCount[$room->room_types->code]) ? App\TourPackage::$roomsPeopleCount[$room->room_types->code] * $room->count : 0;
                                                    ?>
                                                    <?php echo e($room->room_types->code); ?> : <?php echo e($room->count); ?>

                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php if($peopleCount != $tour->pax + $tour->pax_free): ?>
                                                    <div class="alert alert-warning alert-dismissible">
                                                        <button type="button" class="close" data-dismiss="alert"
                                                            aria-hidden="true">×
                                                        </button>
                                                        <i class="icon fa fa-warning"></i>
                                                        <?php echo trans('main.PaxCount'); ?>

                                                        (<?php echo e($tour->pax + $tour->pax_free); ?>) is not equal to the
                                                        number of people in the rooms (<?php echo e($peopleCount); ?>)
                                                    </div>
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="lead">
                                                Pax:
                                                <?php echo e($tour->pax); ?> <?php echo e($tour->pax_free); ?>

                                            </p>
                                        </div>
                                    </div>

                                    <div class="col-md-12" style="overflow: auto">
                                        <table class="table table-bordered finder-disable">
                                            <thead>
                                                <td><?php echo trans('main.Date'); ?></td>
                                                <td><?php echo trans('main.City'); ?></td>
												<td><?php echo trans('Quote Single'); ?></td>
												<td><?php echo trans('Quote SS'); ?></td>
												<td><?php echo trans('Quote HPP'); ?></td>
                                                
                                                <td>
                                                    CMFD HOTEL
                                                </td>

                                                <td><?php echo trans('main.Option'); ?></td>
												
                                                <?php
                                                    $selected_room_count = 'selected_room_count';
                                                    $roomValues = [];
                                                ?>
                                                <?php $__currentLoopData = $listRoomsHotel; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<?php if($room->room_types->code == 'SIN'): ?>
                                                    <td
                                                        <?php if($room->room_types->code == 'SIN'): ?> data-container="body" data-toggle="tooltip" data-placement="bottom"
                                                            data-original-title="Single suppl." <?php endif; ?>>
                                                        Offer Single</td>
												<?php endif; ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                                
												<td><?php echo trans('Offer SS'); ?></td>
												<td><?php echo trans('Offer HPP'); ?></td>
												<td>&reg;</td>
                                                <td>
                                                    VC sent <br>to SHA
                                                </td>
                                                <td></td>
                                                <td><?php echo trans('Budjet HPP +/-'); ?></td>
                                            </thead>
                                            <tbody>
                                                <?php
                                                    $overallSum = 0;
                                                    $count = 0;
                                                ?>
                                                <?php $__currentLoopData = $tour->getTourDaysSortedByDate(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tourDay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php
                                                        $tourday_hotels = count($tourDay->hotels()) > 0 ? count($tourDay->hotels()) : 1;

                                                        $offer_hotel_count = 0;
                                                        if (count($tourDay->hotels()) != 0) {
                                                            foreach ($tourDay->hotels() as $hotel) {
                                                                $offer_hotel_count = $offer_hotel_count + count($hotel->hotel_offers);
                                                            }
                                                            $total = count($tourDay->hotels());
                                                        } else {
                                                            $offer_hotel_count = 1;
                                                            $total = 1;
                                                        }
                                                    ?>
                                                    <tr>
                                                        <td rowspan="<?php echo e($total); ?>"><?php echo e($tourDay->date ?? ''); ?>

                                                        </td>

                                                        <?php if(count($tourDay->hotels()) != 0): ?>
                                                            <?php $__currentLoopData = $tourDay->hotels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hotel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <?php
																	if (!empty($quotation)){
														$quotehtlpp = (int)$quotation->getValueByDate($tourDay->date?? '', "htlpp");
                                                                    $quotationBudget = (int)$quotation->getValueByDate($tourDay->date?? '', "SIN") + $quotehtlpp;
														}else{
														$quotehtlpp = 0;
														 $quotationBudget =0;
														}
														
														            
                                                                    $realBudget = 0;
                                                                    $first_hotel = $tourDay->firstHotel();
                                                                    $count += 1;
                                                                    $offer_hotel_count = count($hotel->hotel_offers) > 0 ? count($hotel->hotel_offers) : 1;
                                                                    $offer_hotel_count = $offer_hotel_count + 1;

                                                                ?>


                                                                <td>
                                                                    <?php if(!is_null($hotel) && method_exists($hotel, 'service') && isset($hotel->service()->cityObject)): ?>
                                                                        <?php echo e($hotel->service()->cityObject->name ?? ''); ?>

                                                                    <?php endif; ?>
                                                                </td>
														<?php if(!empty($quotation)): ?>
														<td><?php echo e((int)$quotation->getValueByDate($tourDay->date?? '', "SIN")+ (int)$quotation->getValueByDate($tourDay->date?? '', "htlpp")); ?></td>
														<td><?php echo e($quotation->getValueByDate($tourDay->date?? '', "SIN")?? ''); ?></td>
														<td><?php echo e($quotation->getValueByDate($tourDay->date?? '', "htlpp")?? ''); ?></td>
														<?php else: ?>
														<td></td>
														<td></td>
														<?php endif; ?>
                                                                


                                                                <td>

                                                                    <?php echo e($hotel->name ?? ''); ?>



                                                                </td>
                                                                
																<?php $total_count = count($listRoomsHotel); $budjet = true; $count_room = 0; $hotelpp = 0;
														$ssp =0; $single =0;?>
                                                                <?php if(count($hotel->hotel_offers) != 0): ?>
                                                                    <td rowspan="1">
                                                                        <?php echo e($hotel->latestHotelOffer->status); ?></td>
														
                                                                    
                                                                    <?php $__currentLoopData = $listRoomsHotel; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $selected_room_type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																	
																		<?php 
														
														
														
														if($selected_room_type->room_types->code == "SIN"){
														$single += $hotel->latestHotelOffer->offersWithRoomPrice($selected_room_type->room_types); 
														}else if( $selected_room_type->room_types->code == "TWN" || $selected_room_type->room_types->code == "DOU"){
														$hotelpp += $hotel->latestHotelOffer->offersWithRoomPrice($selected_room_type->room_types)/2; 
														
														
														$count_room += 1;
														}
														
														?>
														<?php if($selected_room_type->room_types->code == 'SIN'): ?>
                                                                        <td><?php echo e($hotel->latestHotelOffer->offersWithRoomPrice($selected_room_type->room_types)); ?>

                                                                        </td>
																	<?php endif; ?>
                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                <?php else: ?>
                                                                    <td></td>
                                                                    <?php $__currentLoopData = $listRoomsHotel; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $selected_room_type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																		<?php if($selected_room_type->room_types->code == 'SIN'): ?>
                                                                        <td></td>
																		<?php endif; ?>
                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                <?php endif; ?>
														<?php
														if( $count_room == 0){
														$count_room = 1;
														}
														$hotelpp = $hotelpp/$count_room ;
														$ssp = abs($single - $hotelpp);
														$realBudget = $ssp + $hotelpp ;
														?>
																	<td rowspan="1"><?php echo e($ssp??""); ?></td>
														<td rowspan="1"><?php echo e($hotelpp??""); ?></td>
                                                                <td><?php echo e(Form::checkbox('rooming_list_reserved[]', $comparison->comparisonRowByDate($tourDay->date)->id ?? '', $comparison->comparisonRowByDate($tourDay->date)->rooming_list_reserved ?? '', ['class' => 'rooming_list_reserved'])); ?>

                                                                </td>
                                                                <td><?php echo e(Form::checkbox('visa_confirmation[]', $comparison->comparisonRowByDate($tourDay->date)->id ?? '', $comparison->comparisonRowByDate($tourDay->date)->visa_confirmation ?? '', ['class' => 'visa_confirmation'])); ?>

                                                                </td>
                                                                <td>
                                                                    <a class="btn btn-block comments-button"
                                                                        data-row-id="<?php echo e($comparison->comparisonRowByDate($tourDay->date)->id ?? ''); ?>"
                                                                        data-link="<?php echo e(route('comparison.comments', ['id' => $comparison->comparisonRowByDate($tourDay->date)->id ?? ''])); ?>/">
                                                                        <span
                                                                            class="badge bg-yellow"><?php echo e(\App\Helper\AdminHelper::getComparisonRowCommentsCount($comparison->comparisonRowByDate($tourDay->date)->id ?? '')); ?></span>
                                                                        <i class="fa fa-comment-o"
                                                                            aria-hidden="true"></i>
                                                                    </a>
                                                                </td>
														<td data-toggle="tooltip" data-placement="top"
                                                                    title="(<?php echo e($quotehtlpp); ?> - (<?php echo e($hotelpp); ?>)) ">
                                                                    <?php
                                                             
                                                                            $sum = $quotehtlpp - $hotelpp ;
                                                                      
                                                                    ?>
                                                                    <?php echo e(round($sum, 2)); ?>

                                                                </td>
														


                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php $count = 0; ?>
                                            <?php else: ?>
                                                <?php for($i = 1; $i < 10; $i++): ?>
                                                    <td rowspan="<?php echo e($total); ?>"></td>
                                                <?php endfor; ?>
                                                <?php $__currentLoopData = $listRoomsHotel; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $selected_room_type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <td></td>
                                                    <td></td>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php endif; ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <tr rowspan="<?php echo e($offer_hotel_count); ?>">
                                                    <td colspan="<?php echo e(8 + count($listRoomsHotel) * 2); ?>">
                                                        <?php echo trans('main.ENDOFSERVICE'); ?></td>
                                                    <td>&#931; =</td>
                                                    <td><?php echo e(round($overallSum, 2)); ?></td>
                                                </tr>

                                                <!--  Bottom  -->
                                            </tbody>
                                        </table>
                                    </div>
                                </form>
                                <?php else: ?>
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle"></i> No quotation data available for front sheet.
                                </div>
                                <?php endif; ?>
                            </div>

                            <div class="col-md-8">
                                
                                <div id="commentModal" class="modal fade in" tabindex="-1" role="dialog"
                                    aria-labelledby="commentModal">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <a class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">x</span>
                                                </a>
                                                <h4 id="modalCreateBusLabel" class="modal-title"></h4>
                                            </div>

                                        </div>

                                        <div class="modal-body">
                                            <div class="modal-body">
                                                <form id="comments">


                                                </form>
                                            </div>
                                        </div>


                                        
                                    </div>
                                </div>
                            </div>
                        </div>



                    </div>

                </div>
            </div>
        </div>

        
    </div>
    </div>
    <span id="tour_date_id" data-tour-id="<?php echo e($tour->id); ?>"></span>
    <span id="tour_dates" data-departure_date='<?php echo e($tour->departure_date); ?>'
        data-retirement_date='<?php echo e($tour->retirement_date); ?>'></span>

    <div class="modal fade" tabindex="-1" id="date_modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="task_submit_form">
                    <div class="modal-header">
                        <button type="button" class="close btn-task-tour-hotel_cancel" data-dismiss='modal'
                            aria-label="Close"><span aria-hidden='true'>&times;</span></button>
                        <h4 class="modal-title">Add Task for the Tour "<?php echo e($tour->name); ?>" users</h4>
                    </div>

                    <div class="row" style="padding: 2em;">
                        <div class="form-group col-md-6 col-lg-6" style="padding-left: 0">

                            <label for="departure_date">Date</label>

                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input class="form-control pull-right datepicker" id="start_date" name="start_date"
                                    type="text" value="<?php echo e(date('Y-m-d')); ?>">
                            </div>
                        </div>
                        <div class="form-group col-md-6 col-lg-6" style="padding-right: 0">

                            <label for="departure_date">Time</label>

                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-clock-o"></i>
                                </div>
                                <input class="form-control pull-right timepicker" id="end_time" name="end_time"
                                    type="text" value="18:00">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" id="tour_id" value="<?php echo e($tour->id); ?>">
                        <button type="button" class="btn btn-default btn-task-tour-hotel_cancel"
                            data-dismiss="modal">Close</button>
                        <button type="button" id="submit_on_option_task" class="btn btn-primary"
                            id="">Create Task</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" id="error_send">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss='modal' aria-label="Close"><span
                            aria-hidden='true'>&times;</span></button>
                    <h4 class="modal-title" id="title_modal_error">Warning!</h4>
                </div>
                <div class="modal-body">
                    <h3 class="error_send_message"></h3>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" role='dialog' id="guest-list-modal">
        <div class="modal-dialog modal-lg" role='document' style="width: 90% !important; ">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss='modal' aria-label="Close"><span
                            aria-hidden='true'>&times;</span></button>
                    <h4 class="modal-title">Info</h4>
                </div>
                <div class="modal-body">

                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade in" id="question_modal" tabindex="-1" role="dialog" aria-labelledby="myQuestionLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myModalLabel">Warning!!</h4>
            </div>
            <div class="modal-body">
                <div class="modal-body">Would you like to send Guest List to selected tour hotels?</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary pre-loader-func" id="send_agree">Agree</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade in" id="landingpage_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myModalLabel">Warning!!</h4>
            </div>
            <div class="modal-body">
                <div class="modal-body">There is no image for landing page. Are you sure you want to generate the page?
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary pre-loader-func" id="open-landing"
                    onclick='export_to("<?php echo e(route('landing_page', ['id' => $tour->id])); ?>");'>Agree</button>
            </div>
        </div>
    </div>
</div>

<span hidden id="tourimage"
    data-image="<?php if($tour->attachments()->first() != null): ?> <?php echo e($tour->attachments()->first()->url); ?> <?php endif; ?>">
    <?php if($tour->attachments()->first() != null): ?>
        <?php echo e($tour->attachments()->first()->url); ?>

    <?php endif; ?>
</span>
<span id="url" hidden data-url="<?php echo e(route('images.savefile')); ?>"></span>
<script type="text/javascript">
    var selectedGuestList;

    $(document).on('dblclick', '.price_tour_text', function() {
        $(this).find('span').css({
            'display': 'none'
        });
        $(this).find('input').css({
            'display': 'block'
        });
        $(this).find('input').select();
    });

    $("#checkboxallhotels").click(function() {
        if ($("#checkboxallhotels").is(':checked')) {
            $("#hotelselect > option").prop("selected", "selected");
            $("#hotelselect").trigger("change");
        } else {
            $("#hotelselect > option").removeAttr("selected");
            $("#hotelselect").trigger("change");
        }
    });
    const tour_id = $('#tour_date_id').attr('data-tour-id');

    $(document).on('keydown blur', '#new_price_total_amount', function(e) {
        if (e.type === 'keydown') {
            if (e.keyCode === 13) {
                changeTotalAmount($(this).val(), $('#new_price_for_one').val(), $(this));
            }
        } else {
            changeTotalAmount($(this).val(), $('#new_price_for_one').val(), $(this));
        }
    });

    $(document).on('keydown blur', '#new_price_for_one', function(e) {
        if (e.type === 'keydown') {
            if (e.keyCode === 13) {
                changePriceForOne($(this).val(), $('#new_price_total_amount').val(), $(this));
            }
        } else {
            changePriceForOne($(this).val(), $('#new_price_total_amount').val(), $(this));
        }
    });

    function changeTotalAmount(new_price, def_price, _this) {
        $.ajax({
            method: 'POST',
            url: '/change_tour_price',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                price_total_amount: new_price,
                price_for_one: def_price,
                tour_id: tour_id
            }
        }).done((res) => {
            $(_this).css({
                'display': 'none'
            });
            $(_this).attr('value', res.total_amount);
            $(_this).prev('span').css({
                'display': 'block'
            });
            $(_this).prev('span').text(res.total_amount);
        });
    }

    function changePriceForOne(new_price, def_price, _this) {
        $.ajax({
            method: 'POST',
            url: '/change_tour_price',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                price_total_amount: def_price,
                price_for_one: new_price,
                tour_id: tour_id
            }
        }).done((res) => {
            $(_this).css({
                'display': 'none'
            });
            $(_this).attr('value', res.price_for_one);
            $(_this).prev('span').css({
                'display': 'block'
            });
            $(_this).prev('span').text(res.price_for_one);
        });
    }

    function showmodal() {
        var img = $("#tourimage").data("image");
        if (!img) {
            $('#landingpage_modal').modal();
        } else {
            window.open("<?php echo e(route('landing_page', ['id' => $tour->id])); ?>", '_blank');
            //                window.location.href = "<?php echo e(route('landing_page', ['id' => $tour->id])); ?>";
        }
    };

    $(document).keyup(function(e) {
        if (e.keyCode === 27) {
            $('#new_price_for_one').css({
                'display': 'none'
            });
            $('#new_price_total_amount').css({
                'display': 'none'
            });

            $('#new_price_for_one').prev('span').css({
                'display': 'block'
            });
            $('#new_price_total_amount').prev('span').css({
                'display': 'block'
            });
        }
    });

    $('.aftersend').on('click', function() {
        selectedGuestList = $(this);
        $('#question_modal').modal();
    });

    $('.delete_guest_list').on('click', function() {
        if (confirm('Are you sure to delete guest list?')) {
            $.ajax({
                method: 'GET',
                url: jQuery(this).data("url"),
                data: {},
            }).done((res) => {
                document.location.reload(true);

            });

        }
    });

    $('#send_agree').on('click', function() {
        var self = selectedGuestList;
        let block_overlay = '<div class="overlay" id="overlay_delete">\n' +
            '\t\t<i class="fa fa-refresh fa-spin"></i>\n' +
            '\t</div>';
        let overlay_component = self.closest('.box-body');
        overlay_component.append(block_overlay);
        self.hide();
        $.ajax({
            method: 'GET',
            url: self.data("url"),
            data: {},
            beforeSend: function() {
                $('#question_modal').modal('hide');
            },
        }).done((res) => {
            $('#error_send').find('#title_modal_error').html('');

            if (res.error === 'error') {
                $('#error_send').find('.error_send_message').html(res.message);
                $('#error_send').find('#title_modal_error').html('Warning!');
            } else {
                $('#error_send').find('.error_send_message').html(res.message);
                if (res.broke) {
                    $('#error_send').find('.error_send_message').append('<br><br>' + res.broke);
                }
                $('#error_send').find('#title_modal_error').html('Success!');
            }
            $('#overlay_delete').remove();
            $('#error_send').modal();

            setTimeout(function() {
                $('#error_send').modal('hide');
                if (res.error != 'error') {
                    self.closest("tr").find(".sent_at").html(res.sent_at);
                } else {
                    self.show();
                }
            }, 3000);
        });
    });

    $('.guest_list_show').click(function(e) {
        e.preventDefault();
        let block_overlay = '<div class="overlay" id="overlay_delete">\n' +
            '\t\t<i class="fa fa-refresh fa-spin"></i>\n' +
            '\t</div>';
        let overlay_component = $(this).closest('.box-body');
        overlay_component.append(block_overlay);

        var self = $(this);
        $.ajax({
            method: 'GET',
            url: self.attr('href'),
            data: {},
            beforeSend: function() {
                $('#question_modal').modal('hide');
            },
        }).done((res) => {
            $('#guest-list-modal').find('.modal-body').html(res);
            $('#overlay_delete').remove();
            $('#guest-list-modal').modal();

        });
        return false;
    });

    $('.guest_list_show_email_hotels').click(function(e) {
        e.preventDefault();
        let block_overlay = '<div class="overlay" id="overlay_delete">\n' +
            '\t\t<i class="fa fa-refresh fa-spin"></i>\n' +
            '\t</div>';
        let overlay_component = $(this).closest('.box-body');
        overlay_component.append(block_overlay);

        var self = $(this);
        $.ajax({
            method: 'GET',
            url: self.attr('href'),
            data: {},
            beforeSend: function() {
                $('#question_modal').modal('hide');
            },
        }).done((res) => {
            $('#error_send').find('#title_modal_error').html('INFO');
            $('#error_send').find('.error_send_message').html(res);
            $('#overlay_delete').remove();
            $('#error_send').modal();

        });
        return false;
    });
</script>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript" src="<?php echo e(asset('js/jspdf.min.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(asset('js/autotable.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(asset('js/ckeditor/ckeditor.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/2.4.1/lodash.min.js"></script>

<script type="text/javascript" src='<?php echo e(asset('js/supplier-search.js')); ?>'></script>
<script type="text/javascript" src='<?php echo e(asset('js/tour.js')); ?>'></script>
<script type="text/javascript" src='<?php echo e(asset('js/hide_elements.js')); ?>'></script>
<script type="text/javascript" src='<?php echo e(asset('js/roomlist.js')); ?>'></script>
<script type="text/javascript" src='<?php echo e(asset('js/attachments.js')); ?>'></script>
<?php $__env->stopPush(); ?>
<?php $__env->startSection('post_scripts'); ?>
<script type="text/javascript">
    jQuery(document).ready(function() {
        if ($(document).find('#description').length > 0) {
            if (CKEDITOR.instances['description']) {
                CKEDITOR.instances['description'].destroy(true);
            }
            CKEDITOR.replace('description', {
                height: '200px',
                title: false
            });
            CKEDITOR.config.toolbar = [
                ['Bold', 'Italic', 'Underline', 'SpellChecker', 'TextColor', 'BGColor', 'Undo', 'Redo',
                    'Link', 'Unlink', '-', 'Format'
                ],

            ];
        }
        if ($(document).find('#description-edit').length > 0) {
            //            if (CKEDITOR.instances['description-edit']) {
            //                CKEDITOR.instances['description-edit'].destroy(true);
            //            }
            CKEDITOR.replace('description-edit', {
                height: '200px',
                //                title: false
            });
            CKEDITOR.config.toolbar = [
                ['Bold', 'Italic', 'Underline', 'SpellChecker', 'TextColor', 'BGColor', 'Undo', 'Redo',
                    'Link', 'Unlink', '-', 'Format'
                ],

            ];
        }

    });
</script>
<script>
    function quotation_to_tour() {
        //confirm("Are you Sure");
        var quotation_url = $("#quotation_to_tour_href").attr("href");
        if ($("#check1").prop('checked')) {
            var quotation_url = $("#tour_to_quotation").attr("href");
        }
        console.log(quotation_url);
        $.ajax({
            type: "GET",
            url: quotation_url,



            success: function(result) {
                location.reload();
                console.log("working");
            },
            error: function(result) {
                console.log(result);
            }
        });
    }

    function tour_to_quotation() {
        var quotation_url = $("#tour_to_quotation").attr("href");
        if (!$("#check2").prop('checked')) {
            var quotation_url = $("#quotation_to_tour_href").attr("href");
        }

        console.log(quotation_url);
        $.ajax({
            type: "GET",
            url: quotation_url,



            success: function(result) {
                location.reload();
                console.log("working");
            },
            error: function(result) {
                console.log(result);
            }
        });
    }
</script>


<script src="<?php echo e(asset('js/comment.js')); ?>"></script>


<script>
    $(document).ready(function() {
        let permission = $('#permission').attr('data-permission');
        let classNameStatus = permission ? 'touredit-status' : '';
        const tour_id = $('#tour_date_id').attr('data-tour-id');

        // Invoices table now uses Bootstrap table with direct controller data

    })
</script>
<script>
	
	$(document).ready(function() {
   
    var activeTab = localStorage.getItem('activeTab');
		console.log(localStorage);
    // If there's no active tab in localStorage, set the default tab
    if (activeTab) {
      //  activeTab = 'frontsheet-tab'; // Set the default active tab
		
		//$( '#frontsheet-tab' ).removeClass( 'active' );
		$( 'li[ data-tab=' + activeTab + ']' ).addClass( 'active' );
		  $('#' + activeTab).addClass('active in');

    }else{
		$( 'li[ data-tab=frontsheet-tab]' ).addClass( 'active' );

		$("#frontsheet-tab").addClass("active in")

	}

    // Show the initially active tab
    // Click event to switch tabs
    $('.tab').on('click', function() {
	  var tabId = $(this).data('tab');

        // Save the active tab to localStorage
        localStorage.setItem('activeTab', tabId);
		console.log(localStorage);
    // $('.tab').removeClass('active');
       // $('.tab-pane').hide();
		
    });
});
	$(document).on('click','.select',function(){
		let selectId = $(".selectedOfice option:selected").val();
		
		 $.ajax({
            url: '/update-status/'+selectId,
            type: 'get',
            success: function () {   
				location.reload(true);
            }
            
        });
	});
	
    $(document).ready(function() {
        let permission = $('#permission').attr('data-permission');
        let classNameStatus = permission ? 'touredit-status' : '';
        // Transactions table now uses Bootstrap table with direct controller data

    })
	
</script>
<script>
    $(document).ready(function () {
        // Check if there is a stored scroll position
        var storedScrollPosition = localStorage.getItem('scrollPosition');

			if (storedScrollPosition) {
				// Scroll to the stored position
				$("div").scrollTop(parseInt(storedScrollPosition));
			}

        // Add a scroll event handler to store the scroll position
        $("div").scroll(function () {
		
            var scrollPosition = $(this).scrollTop();

            // Store the scroll position in localStorage
            localStorage.setItem('scrollPosition', scrollPosition);
        });
    });
</script>
<style>
    .toggle {
        position: relative;
        height: 42px;
        display: flex;
        align-items: center;
        box-sizing: border-box;
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
        box-sizing: border-box;
    }

    .toggle label:before,
    .toggle label:after {
        font-size: 18px;
        font-weight: bold;
        font-family: arial;
        transition: 0.2s ease-in;
        box-sizing: border-box;
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
        font-size: 10px
    }

    .toggle label:after {
        content: "GoAhead";
        position: absolute;
        left: 80px;
        line-height: 42px;
        top: 0;
        color: #FFF;
        font-size: 10px
    }

    .toggle input[type="checkbox"]:checked+label:before {
        color: #000;
        box-shadow: inset 0px 0px 0 0px #000;
    }

    .toggle input[type="checkbox"]:checked+label:after {
        color: #FFF;
    }

    .option-radio {
        box-shadow: 0px 0px 0px 1px #6d6d6d;
        font-size: 3em;
        width: 20px;
        height: 20px;
        margin-right: 7px;

        border: 4px solid #fff;
        background-clip: border-box;
        border-radius: 50%;
        appearance: none;
        transition: background-color 0.3s, box-shadow 0.3s;

    }

    .option-radio:checked {
        box-shadow: 0px 0px 0px 4px #eb0000;
        background-color: #ff5151;
    }

    .options-label {
        cursor: pointer;
        font-size: 20px;
        margin-left: 10px;
    }
	
</style>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('scaffold-interface.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/tour/show.blade.php ENDPATH**/ ?>