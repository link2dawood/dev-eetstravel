<?php $__env->startSection('title', 'Agreements'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('layouts.title',
       ['title' => 'Agreements', 'sub_title' => 'Agreements Create',
       'breadcrumbs' => [
       ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
       ['title' => 'Agreements', 'icon' => 'fa fa-handshake-o', 'route' => route('hotel.show', ['hotel' => $id])],
       ['title' => 'Create', 'route' => null]]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <section class="content">
        <div class="box box-primary">
            <div class="box box-body" style="border-top: none">
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
                <form method='POST' action='<?php echo e(route('store_agreements')); ?>' >
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
                        <div class="col-md-4">
                            <?php echo e(csrf_field()); ?>

                            <input type='hidden' id='hotel_id' name='hotel_id' value='<?php echo e($id); ?>'>
                            <input type='hidden' id='agreement_id' name='agreement_id' value=''>
                            <div class="form-group">
                                <label >Name*</label>
                                <input type="text" value="<?php echo e(old('name')); ?>" name="name" class="form-control"  >
                            </div>

                            <br>
                            <div class="form-group">
                                <label for="departure_date"><?php echo trans('main.StartDate'); ?> *</label>

                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" value="<?php echo e(old('start_date')); ?>" name="start_date" id="start_date" class="form-control pull-right datepicker"   >
                                </div>

                            </div>

                            <div class="form-group">
                                <label for="departure_date"><?php echo trans('main.EndDate'); ?> *</label>

                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" value="<?php echo e(old('end_date')); ?>" name="end_date" id="end_date" class="form-control pull-right datepicker"  >

                                </div>

                            </div>

                            <div class="form-group">
                                <label ><?php echo trans('main.RoomTypes'); ?></label>

                                <div id="list_selected_room_types">
                                    <?php if(!empty($agreement)): ?>
                                    <?php $__currentLoopData = $agreement->agreements_room_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php echo $__env->make('component.item_agreement_hotel_room_type', ['room_type' => $item, 'room'=> $agreement->getRoom($item->room_type_id)], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
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
                                                    <input type="text" data-agreement="<?php echo e(null); ?>" data-info="<?php echo e($room_type->id); ?>" hidden value="<?php echo e($room_type); ?>">
                                                </li>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </ul>
                                </ul>

                            </div>

                            <div class="form-group">
                                <label for="departure_date"><?php echo trans('main.Description'); ?></label>
                                    <?php echo Form::textarea('description',  old('description') , ['class' => 'form-control', 'id' => 'description']); ?>

                            </div>
                        </div>
                    </div>
                    <button class='btn btn-success' type='submit'><?php echo trans('main.Save'); ?></button>
                </form>
            </div>
        </div>
    </section>
    <style>
        .datepicker{z-index:500 !important;}
    </style>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
    <script type="text/javascript" src='<?php echo e(asset('js/agreement_rooms.js')); ?>'></script>
    <script type="text/javascript" src='<?php echo e(asset('js/hide_elements.js')); ?>'></script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('scaffold-interface.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/kontingent/create.blade.php ENDPATH**/ ?>