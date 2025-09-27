<?php $__env->startSection('title','Create'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('layouts.title',
   ['title' => $title, 'sub_title' => $subTitle,
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Tours', 'icon' => 'suitcase', 'route' => route('tour.index')],
   ['title' => 'Create', 'route' => null]]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
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
                <form method='POST' action='<?php echo url("tour"); ?>' enctype="multipart/form-data">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="margin_button">
                                <a href="javascript:history.back()">
                                    <button type="button" class='btn btn-primary back_btn'><?php echo trans('main.Back'); ?></button>
                                </a>
                                <button class='btn btn-success' type='submit'><?php echo trans('main.Save'); ?></button>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <?php echo e(csrf_field()); ?>

                        <?php if($isQuotation): ?>
                            <?php echo $__env->make('component.js-validate', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        <?php endif; ?>
                        <div class="col-md-6">
                            <input type='hidden' name='_token' value='<?php echo e(Session::token()); ?>'>
                            <div class="form-group">
                                <label for="name"><?php echo trans('main.Name'); ?> *</label>
                                <?php echo Form::text('name', '', ['class' => 'form-control']); ?>

                            </div>
                            
                            <div class="form-group">

                                <label for="departure_date"><?php echo trans('main.DepDate'); ?> *</label>

                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <?php echo Form::text('departure_date', '',
                                    ['class' => 'form-control pull-right datepicker', 'id' => 'departure_date', 'autocomplete' => 'off']); ?>

                                </div>
                            </div>
                            <div class="form-group">
                                <label for="retirement_date"><?php echo trans('main.RetDate'); ?> *</label>

                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <?php echo Form::text('retirement_date', '', ['class' => 'form-control pull-right datepicker', 'id' => 'retirement_date']); ?>

                                </div>
                            </div>

                            
                            <?php if(!$isQuotation): ?>
                              
                                <div class="form-group">
                                    <label for="status"><?php echo trans('main.Status'); ?></label>
                                    <select name="status" id="status" class="form-control">
                                        <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option <?php echo e(old('status') == $status->id ? 'selected' : ''); ?> value="<?php echo e($status->id); ?>"><?php echo e($status->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
								<div class="form-group">
									<label for="assigned_user"><?php echo trans('main.AssignedUser'); ?> *</label>
									<div class="form-control" style="max-height:200px !important;overflow-x:auto;height: auto; ">
										<table>
											<tr>
												<?php $i = 1; ?>
												<?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<td style="width: 30rem;" id="user_data_<?php echo e($user->id); ?>">
													<label for="user_<?php echo e($user->id); ?>" style="font-size: 18px;">
														<?php echo e($user->name); ?>

														
													</label>
														<input class = "user_checkboxes" type="checkbox" name="assigned_user" id="user_<?php echo e($user->id); ?>" value="<?php echo e($user->id); ?>">
													
													
												</td>
												
												<?php if($i % 4 == 0): ?>
											</tr>
											<tr>
												<?php endif; ?>
												<?php $i += 1; ?>
												<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											</tr>
										</table>
									</div>
								</div>

                                <div class="form-group">
                                    <label for="responsible_user"><?php echo trans('main.ResponsibleUser'); ?></label>
                                    <select name="responsible_user" class="form-control" id="responsible_user">
                                        <option value="0"><?php echo trans('main.Withoutresponsibleuser'); ?></option>
                                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($user->id); ?>"><?php echo e($user->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            <?php else: ?>
                                
                                <?php echo Form::hidden('status', 1); ?>

                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="pax">Pax</label>
                                <?php echo Form::text('pax', '', ['class' => 'form-control','id' => 'passenger_count']); ?>

                            </div>
							<div class="form-group">
								<label for="child_count">Number of Children:</label>
								<input type="number" id="child_count" name="child_count" class="form-control">
							</div>

							<div id="child_details">
								<!-- Child details will be added dynamically using JavaScript -->
							</div>

							 <button type="button" onclick="addChildFields()" class="btn btn-primary">Add Child</button>
      
       
                            <div class="form-group">
                                <label for="pax_free"><?php echo trans('main.PaxFree'); ?></label>
                                <?php echo Form::text('pax_free', '', ['class' => 'form-control']); ?>

                            </div>
                            <!-- ////////////////// -->
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
                                        <?php $__currentLoopData = $room_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room_type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li class="select_room_type">
                                                <label><?php echo e($room_type->name); ?></label>
                                                <input type="text" data-info="<?php echo e($room_type->id); ?>" hidden value="<?php echo e($room_type); ?>">
                                            </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                </ul>

                            </div>
                            <!-- ////////////////// -->
                            <?php if(!$isQuotation): ?>
                            
 
                               
                                <div class="form-group">
                                    <label><?php echo trans('main.Files'); ?></label>
                                    <?php $__env->startComponent('component.file_upload_field'); ?><?php echo $__env->renderComponent(); ?> 
                                </div>
                                <div class="form-group">
                                        <label for="attach"><?php echo trans('main.imageforlanding'); ?></label>
                                        <div>
                                            <div class="file-preview thumbnail">
                                                <div class="file-drop-zone-title" style="padding:15px 10px;"><center>Image for landing page</center>
                                                    <img id="pic" src="" style="width:100%">
                                                </div>                                   
                                            </div>
                                        </div>

                                        <div class="input-group file-caption-main">
                                            <div tabindex="500" class="form-control">
                                            <div class="file-caption-name" id="file-caption-name"></div>
                                            </div>

                                                <div class="input-group-btn">
                                                    <div tabindex="500" class="btn btn-primary btn-file"><i class="glyphicon glyphicon-folder-open"></i>&nbsp;  <span class="hidden-xs">Browse …</span>
                                                        <input type="file" name="files[]" id="imgInp" class="fileToUpload" multiple>

                                                    </div>
                                            </div>
                                         </div>
                                    </div>
                            <?php endif; ?>
                            <?php echo Form::hidden('is_quotation', 1); ?>

                        </div>
                    </div>
                    <button class='btn btn-success' type='submit'><?php echo trans('main.Save'); ?></button>
                </form>
            </div>
        </div>
    </section>
   <script type="text/javascript" src='<?php echo e(asset('js/rooms.js')); ?>'></script>
   <script type="text/javascript" src='<?php echo e(asset('js/hide_elements.js')); ?>'></script>
    
    <script type="text/javascript">
        function readURL(input) {

            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                  $('#pic').attr('src', e.target.result);
                  $('#file-caption-name').html(input.files[0].name); 
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#imgInp").change(function() {
            readURL(this);
        });
		
    </script>
<script>
 function handleCheckboxes() {
  const checkboxes = document.querySelectorAll('.user_checkboxes');

  checkboxes.forEach(function (checkbox) {
    checkbox.addEventListener("click", function () {
      if (this.checked) {
        // Add the selected row to your list
        var input = $('<input class="user_checkboxes" type="checkbox" name="assigned_user" id="user_' + this.value + '" value="' + this.value + '" checked>');
        $('#user_' + this.value).remove();
        input.appendTo($('#user_data_' + this.value));
        console.log("Selected User ID: " + this.value);
      } else {
        // Remove the deselected row from your list
        var input = $('<input class="user_checkboxes" type="checkbox" name="assigned_user" id="user_' + this.value + '" value="' + this.value + '">');
        $('#user_' + this.value).remove();
        input.appendTo($('#user_data_' + this.value));
        $('#user_check' + this.value).remove();
        console.log("Deselected User ID: " + this.value);
      }
    });
  });
}

// Call the function initially
handleCheckboxes();

// Set an interval to refresh the event handling
setInterval(function () {
  handleCheckboxes();
}, 500); // Adjust the interval time as needed

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


</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('scaffold-interface.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/tour/create.blade.php ENDPATH**/ ?>