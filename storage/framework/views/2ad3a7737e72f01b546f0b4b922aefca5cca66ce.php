<?php $__env->startSection('title','Edit'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('layouts.title',
   ['title' => 'Tour', 'sub_title' => 'Edit Tour',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Tours', 'icon' => 'suitcase', 'route' => route('tour.index')],
   ['title' => 'Edit', 'route' => null]]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php
        $tab = '' ;
        $uri_parts = explode('?', \Request::fullUrl() );
        if(count($uri_parts)>1){
           $tab_parts = explode('=', $uri_parts[1]);
           if($tab_parts[0] == 'tab') $tab = $uri_parts[1];
        }
    ?>
    
    <div class="modal fade" role='dialog' id="service-modal">
        <div class="modal-dialog modal-lg" role='document'>
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss='modal' aria-label="Close"><span
                                aria-hidden='true'>&times;</span></button>
                    <h4 class="modal-title"><?php echo trans('main.Addservice'); ?></h4>
                    
                    <form action="<?php echo e(route('supplier_show')); ?>">
                        <div class="form-group">
                            <select id="service-select" class="form-control">
                                <option selected><?php echo trans('main.All'); ?></option>
                                <?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option><?php echo e($option); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </form>
                    
                </div>
                <div class="modal-body">
                    <table id="search-table" class="table table-striped table-bordered table-hover">
                        <thead>
                        <tr>
                            <th><?php echo trans('main.Name'); ?></th>
                            <th><?php echo trans('main.Address'); ?></th>
                            <th><?php echo trans('main.Phone'); ?></th>
                            <th><?php echo trans('main.ContactName'); ?></th>
                            
                            <th></th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <form method='POST' action='<?php echo url("tour"); ?>/<?php echo $tour->id; ?>/update'
                      enctype="multipart/form-data" id="tour_form">
                    <input type="hidden" id="tab" name="tab" value="<?php echo e($tab); ?>" >
                    <div class="row">
                        <div class="col-md-12">
                            <div class="margin_button">
                                <a href="javascript:history.back()">
                                    <button class='btn btn-primary back_btn' type="button"><?php echo trans('main.Back'); ?></button>
                                </a>
                                <button class='btn btn-success' type='submit'><?php echo trans('main.Save'); ?></button>
                            </div>
                        </div>
                    </div>


                <?php if(session('message_buses')): ?>
                    <div class="alert alert-info block-error-driver" style="text-align: center;">
                        <?php echo e(session('message_buses')); ?>

                    </div>
                <?php endif; ?>

                <div class="alert alert-info block-error-driver" style="text-align: center; display: none;">

                </div>

                <div class="row">
                    <div class="col-md-12">

                            <div class="row">
                                <div class="col-md-6">
                                    <?php echo e(csrf_field()); ?>

                                    <div class="form-group">
                                        <label for="name"><?php echo trans('main.Name'); ?></label>
                                        <input id="name" name="name" type="text" class="form-control"
                                               value="<?php echo $tour->name; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="external_name"><?php echo trans('main.ExternalName'); ?></label>
                                        <input id="external_name" name="external_name" type="text" disabled
                                               class="form-control" value="<?php echo $tour->external_name; ?>">
                                    </div>
                                   

                                    <div class="form-group">
                                        <label for="departure_date"><?php echo trans('main.DepDate'); ?></label>
                                        <div class="input-group date">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            <?php echo Form::text('departure_date', $tour->departure_date, ['class' => 'form-control pull-right datepicker',
                                             'id' => 'departure_date']); ?>

                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="retirement_date"><?php echo trans('main.RetDate'); ?></label>
                                        <div class="input-group date">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            <?php echo Form::text('retirement_date', $tour->retirement_date, ['class' => 'form-control pull-right datepicker',
                                             'id' => 'retirement_date']); ?>

                                        </div>
                                    </div>
                                    
                                   
									<div class="form-group" >
										<label for="assigned_user"><?php echo trans('main.AssignedUser'); ?> *</label>
										<div class ="form-control" style="max-height:200px !important;overflow-x:auto;height: auto; ">
										<?php $i = 1; ?>
											<?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
											<span style="font-size: 18px; margin-right:15px; margin-left:15px; "><?php echo e($user->name); ?>

												<input type="checkbox" name="assigned_user" id="assigned_user" value="<?php echo e($user->id); ?>" <?php echo e($user->selected ? 'checked' : ''); ?>>
											</span>
										<span>|</span>

											<?php if($i % 6 == 0): ?>
												<br>
											<?php endif; ?>

											<?php $i += 1; ?>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
										</div>
									</div>
                                    <div class="form-group">
                                        <label for="responsible_user"><?php echo trans('main.ResponsibleUser'); ?></label>
                                        <select name="responsible_user" class="form-control" id="responsible_user">
                                            <option value="0"><?php echo trans('main.Withoutresponsibleuser'); ?></option>
                                            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($user->id); ?>" <?php echo e($tour->getResponsibleUser() ?
                                                 $tour->getResponsibleUser()->id == $user->id ? "selected='selected'" : '' :
                                                 ''); ?>><?php echo e($user->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>

                                    <div class="form-group">
										<label for="status"><?php echo trans('main.Status'); ?></label>
										<select name="status" id="status" class="form-control">
											<?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<option value="<?php echo e($status->id); ?>"
													<?php echo e(($errors != null && count($errors) > 0) ? (old('status') == $status->id ? 'selected' : '') : ($tour->status == $status->id ? 'selected' : '')); ?>>
													<?php echo e($status->name); ?>

												</option>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
										</select>
									</div>


                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pax">Pax</label>
                                        <input id="pax" name="pax" type="text" class="form-control"
                                               value="<?php echo $tour->pax; ?>">
                                    </div>
									<div class="form-group">
										<label for="child_count">Number of Children:</label>
										<?php if(empty($tour->childrens)): ?>
										<input type="number" id="child_count" name="child_count" class="form-control" >
										<?php else: ?>
										<input type="number" id="child_count" name="child_count" class="form-control" value= "<?php echo count($tour->childrens); ?>">
										<?php endif; ?>
									</div>
									<?php $i = 0; ?>
									<div id="child_details">
									<?php if(!empty($tour->childrens)): ?>
									<?php $__currentLoopData = $tour->childrens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chd): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<?php $i++ ?>
									
										<div class="form-group">
												<label for="age_1">Age of Child <?php echo e($i); ?>:</label>
												<input type="number" id="age_1" name="ages[]" class="form-control" min="0" value="<?php echo e($chd->age); ?>">
												<label for="price_1">Price:</label>
												<input type="number" id="price_1" name="prices[]" class="form-control" value="<?php echo e($chd->price); ?>">
										</div>
									
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									<?php endif; ?>
										</div>

									 <button type="button" onclick="addChildFields()" class="btn btn-primary">Add Child</button>
      						
                                    <div class="form-group">
                                        <label for="pax_free"><?php echo trans('main.PaxFree'); ?></label>
                                        <input id="pax_free" name="pax_free" type="text" class="form-control"
                                               value="<?php echo $tour->getAttributes()['pax_free']; ?>">
                                    </div>

                                    

                                    <div class="form-group">
                                        <label ><?php echo trans('main.RoomTypes'); ?></label>

                                        <div id="list_selected_room_types">
                                            <?php if(!empty($selected_room_types)): ?>
                                                <?php $__currentLoopData = $selected_room_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php echo $__env->make('component.item_hotel_room_type', ['room_type' => $item], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php endif; ?>

                                        </div>

                                        <button class="btn btn-success btn_for_select_room_type" type="button"><?php echo trans('main.SelectRooms'); ?></button>

                                        <ul class="list_room_types">
                                            <ul class="list_room_types" style="display: block; z-index:999;">
                                                <?php if(!empty($room_types)): ?>
                                                    <?php $__currentLoopData = $room_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room_type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <li class="select_room_type">
                                                            <label><?php echo e($room_type->name); ?></label>
                                                            <input type="text" data-info="<?php echo e($room_type->id); ?>" hidden value="<?php echo e($room_type); ?>">
                                                        </li>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php endif; ?>
                                            </ul>
                                        </ul>

                                    </div>


                                    <div class="form-group">
                                        <label for="itinerary"><?php echo trans('main.tourleader'); ?></label>
                                            <?php echo Form::text('itinerary_tl',$tour->itinerary_tl, ['class' => 'form-control', 'id'=>'itinerary']); ?>

                                    </div>
                                    <div class="form-group">
                                        <label for="phone"><?php echo trans('main.Phone'); ?></label>
                                        <input id="phone" name="phone" type="text" class="form-control"
                                               value="<?php echo $tour->phone; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="attach"><?php echo trans('main.Files'); ?></label>
                                        <?php $__env->startComponent('component.file_upload_field'); ?><?php echo $__env->renderComponent(); ?>
                                    </div>
                                    <input type="text" hidden name="calendar_edit" value="<?php echo e($calendar_edit); ?>">

                                    <div class="form-group">
                                        
                                        <label for="attach"><?php echo trans('main.imageforlanding'); ?></label>
                                        <div>
                                            <div class="file-preview thumbnail">
                                                <div class="file-drop-zone-title" style="padding:15px 10px;"><center>Image for landing page</center>
                                                    <img class="pic" src="<?php if($tour->attachments()->first() != null): ?> <?php echo e($tour->attachments()->first()->url); ?> <?php endif; ?>" style="width:100%">
                                                </div>
                                                                                           
                                            </div>
                                        </div>

                                        <div class="input-group file-caption-main">
                                            <div tabindex="500" class="form-control">
                                            <div class="file-caption-name"></div>
                                            </div>

                                                <div class="input-group-btn">
                                                    <div tabindex="500" class="btn btn-primary btn-file"><i class="glyphicon glyphicon-folder-open"></i>&nbsp;  <span class="hidden-xs">Browse …</span>
                                                        <input name="fileToUpload[]" data-model="Tour" data-id="<?php echo e($tour->id); ?>"class="fileToUpload"type="file" name="myfile" />
                                                    </div>
                                            </div>
                                         </div>
                                    </div>    
                                    <span id="url" hidden data-url="<?php echo e(route('images.savefile')); ?>"></span>
                                </div>

                            </div>
                            <button class='btn btn-success' type='submit'><?php echo trans('main.Save'); ?></button>
                            <a href="<?php echo e(\App\Helper\AdminHelper::getBackButton(route('tour.index'))); ?>">
                                <button class='btn btn-warning' type='button'><?php echo trans('main.Cancel'); ?></button>
                            </a>

                    </div>
                </div>
                </form>
            </div>
        </div>
    </section>
    <span id="tour_dates" data-departure_date='<?php echo e($tour->departure_date); ?>'
          data-retirement_date='<?php echo e($tour->retirement_date); ?>'></span>
    <span id="tour_date_id" data-tour-id="<?php echo e($tour->id); ?>"></span>
    
    <input type="text" id="default_reference_id" hidden name="reference_id" value="<?php echo e($tour->id); ?>">
<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
    <script type="text/javascript" src='<?php echo e(asset('js/supplier-search.js')); ?>'></script>
    <script type="text/javascript" src='<?php echo e(asset('js/rooms.js')); ?>'></script>
    <script type="text/javascript" src='<?php echo e(asset('js/tour.js')); ?>'></script>
    <script type="text/javascript" src='<?php echo e(asset('js/hide_elements.js')); ?>'></script>
    <script type="text/javascript" src='<?php echo e(asset('js/attachments.js')); ?>'></script>
<script>
	function addChildFields() {
    var count = document.getElementById('child_count').value;
    var container = document.getElementById('child_details');
    
    // Clear previous fields
    container.innerHTML = '';
    
    for (var i = 1; i <= count; i++) {
        var div = document.createElement('div');
        div.classList.add('form-group');
        div.innerHTML = `
            <label for="age_${i}">Age of Child ${i}:</label>
            <input type="number" id="age_${i}" name="ages[]" class="form-control" min="0">
            <label for="price_${i}">Price:</label>
            <input type="number" id="price_${i}" name="prices[]" class="form-control">
        `;
        container.appendChild(div);
    }
}
$(document).ready(function() {
    $('#tour_form').on('submit', function(e) {
        // Prevent the default form submission
        e.preventDefault();

        // Check if there's an active AJAX request
        if ($.active > 0) {
            // Abort the AJAX request
            $.ajax.abort();
        }

        // Submit the form
        $(this).unbind('submit').submit();
    });
});

</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('scaffold-interface.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/tour/edit.blade.php ENDPATH**/ ?>