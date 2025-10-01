<?php $__env->startSection('title','Edit'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('layouts.title',
           ['title' => 'Tour Package', 'sub_title' => 'Tour Package Edit',
           'breadcrumbs' => [
           ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
           ['title' => 'Tours', 'icon' => 'suitcase', 'route' => route('tour.index')],
           ['title' => 'Edit', 'route' => null]]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <section class="content">
            <div class="box-body">
                <?php if(count($errors) > 0): ?>
                    <div class="alert alert-danger">
                        <ul>
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php endif; ?>
                    <div class="alert alert-info block-error-driver" style="text-align: center; display: none;">

                    </div>

                <?php if(session('message_buses')): ?>
                    <div class="alert alert-info" style="text-align: center;">
                        <?php echo e(session('message_buses')); ?>

                    </div>
                <?php endif; ?>
                <?php if(session('retirement_package')): ?>
                    <div class="alert alert-danger">
                        <?php echo e(session('retirement_package')); ?>

                    </div>
                <?php endif; ?>
                <?php if(session('transfer_add_date')): ?>
                    <div class="alert alert-danger" style="text-align: center">
                        <?php echo e(session('transfer_add_date')); ?>

                    </div>
                <?php endif; ?>

                
                    
                
                    <!--Items box -->
                    <div class="tour-package-services">

                    </div>
                <form method='POST' action='<?php echo url("tour_package"); ?>/<?php echo $tourPackage->
        id; ?>/update' id="tour_package_add_form">
                    <input type="hidden" name="tour_package_id" value="<?php echo e($tourPackage->id); ?>">
                    <input type='hidden' name='_token' value='<?php echo e(Session::token()); ?>'>
                    <!--Main info box -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                       
                            <h3 class=""> <?php echo trans('main.Service'); ?> :
                                <a href="<?php echo e($tourPackage->service_link); ?>" target="_blank">
                                    <span id="service_name" style="text-transform: capitalize;"><?php echo isset($serviceName) ?  str_replace('transfer', 'Bus Company', $serviceName) : $tourPackage->name; ?></span>
                                </a>
                            </h3>
                            
                            <a href="#" class="btn btn-success btn open-modal-change-service"
                               id="change_service_with_edit"
                               style="display: <?php echo e($tourPackage->getStatusName() !== 'Confirmed' ? 'inline-block' : 'none'); ?>"
                               data-toggle="modal"
                               data-target="#list-tour-packages"
                               data-serviceTypeId="<?php echo e($tourPackage->type); ?>"
                               data-serviceId="<?php echo e($tourPackage->service()->id ??""); ?>"
                               data-packageId="<?php echo e($tourPackage->id); ?>"
                               <?php if($tourPackage->tourDays->isNotEmpty()): ?>
                                    data-tour-day-id="<?php echo e($tourPackage->tourDays[0]->id); ?>"
                               <?php endif; ?>

                               data-time-old-service="<?php echo e(!$tourPackage->hasParrent() ? $tourPackage->time_from : null); ?>"
                               data-link="<?php echo e(route('tour_package.store')); ?>"
                               data-departure_date='<?php echo e($tourPackage->getTour()->departure_date); ?>'
                               data-retirement_date="<?php echo e($tourPackage->getTour()->retirement_date); ?>"
                               data-tour_id='<?php echo e($tourPackage->getTour()->id); ?>'
                               data-info="change_edit_service"
                            >
                                <?php echo trans('main.ChangeService'); ?>

                            </a>
                            
                        </div>
                        <?php if(session('driver_busy')): ?>
                            <div class="alert alert-info col-md-12" style="text-align: center;">
                                <?php echo e(session('driver_busy')); ?>

                            </div>
                        <?php endif; ?>
                        <div class="box-body">
                            <div class="form-group">
                                <?php echo Form::label('name', 'Name'); ?>

                                <?php echo Form::text('name', $tourPackage->name, ['class' => 'form-control']); ?>

                            </div>
                            <div class="form-group">
                                <?php echo Form::label('description', 'Description'); ?>

                                <?php echo Form::text('description', $tourPackage->description, ['class' => 'form-control']); ?>

                            </div>
                            
                            <div class="form-group">
								<label for="status"><?php echo trans('main.Status'); ?></label>
								<select name="status" id="status" class="form-control">
									<?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<option value="<?php echo e($status->id); ?>" 
											<?php echo e(($errors !== null && count($errors) > 0) 
													? (old('status') == $status->id ? 'selected' : '') 
													: ($tourPackage->status == $status->id ? 'selected' : '')); ?>

										>
											<?php echo e($status->name); ?>

										</option>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								</select>
							</div>



                            <?php if($drivers != null && $buses != null): ?>
                            <div style="overflow: hidden; display: block">
                                <div class="col-lg-6" style="padding-left: 0">
                                    <div class="form-group">
                                        <label for="driver_transfer_edit">Drivers</label>
                                        <?php if(count($drivers) > 0): ?>
                                            <select name="driver_transfer[]" id="driver_transfer_edit" class="select2 js-state form-control" multiple="multiple">
                                                <?php $__currentLoopData = $drivers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $driver): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php $check = false; ?>
                                                    <?php $__currentLoopData = $selected_drivers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <?php if($item->driver_id == $driver->id): ?>
                                                                <?php $check = true; ?>
                                                            <?php endif; ?>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        <option <?php echo e($check ? 'selected' : ''); ?>

                                                                class="transfer_driver"
                                                                value="<?php echo e($driver->id); ?>"
                                                        ><?php echo e($driver->name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                            <script>
                                             if($('#driver_transfer_edit').length >0) {
                                                 $('#driver_transfer_edit').select2();
                                             }
                                            </script>
                                        <?php else: ?>
                                            <h5><?php echo trans('main.Transferwithoutdriver'); ?></h5>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-lg-6" style="padding-right: 0">
                                    <div class="form-group">
                                        <label for="bus_transfer_edit">Bus</label>
                                        <?php if(count($buses) > 0): ?>
                                            <select name="bus_transfer" id="bus_transfer_edit" class="form-control">
                                                <?php $__currentLoopData = $buses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bus): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option <?php echo e($selected_bus ? $selected_bus->bus_id == $bus->id ? 'selected' : '' : ''); ?>

                                                            class="transfer_bus"
                                                            value="<?php echo e($bus->id); ?>"
                                                    ><?php echo e($bus->name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        <?php else: ?>
                                            <h5><?php echo trans('main.Transferwithoutbus'); ?></h5>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
							
                            <div class="form-group">
                                <?php echo Form::label('pax', 'Pax'); ?>

                                <?php echo Form::text('pax', $tourPackage->pax, ['class' => 'form-control']); ?>

                            </div>
                            <div class="form-group">
                                <?php echo Form::label('pax_free', 'Pax Free'); ?>

                                <?php echo Form::text('pax_free', $tourPackage->pax_free, ['class' => 'form-control']); ?>

                            </div>
                            <div class="form-group">
                                <?php echo Form::label('total_amount', 'Price per Person'); ?>

                                <?php echo Form::text('total_amount', round($tourPackage->total_amount, 2), [
                                'class' => 'form-control',
                                strtolower(\App\Helper\TourPackage\TourService::$serviceTypes[$tourPackage->type]) == 'hotel' ?
                                  :
                                  '' ]
                                 ); ?>

                            </div>
                            <div class="form-group">
                                <?php echo Form::label('total_amount_manually', 'Total Price'); ?>

                                <?php echo Form::text('total_amount_manually', $tourPackage->total_amount_manually, ['class' => 'form-control']); ?>

                            </div>
                            <div class="form-group">
                                <?php echo Form::label('total_amount_auto', 'Total Price Auto'); ?>

                                <?php echo Form::text('total_amount_auto', $tourPackage->getTotalAmountAuto(), ['class' => 'form-control', 'disabled']); ?>

                            </div>
                          
                            <div class="form-group">
                                <label for="currency">Currency</label>
                                <select name="currency" id="currency" class="form-control">
                                    <?php $__currentLoopData = $currencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $currency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($currency->id); ?>" ><?php echo e($currency->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-lg-12 col-md-12">
                            <?php if(session('date-error')): ?>
                                    <span class="alert alert-danger" style="display: block"><?php echo e(session('date-error')); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="form-group col-md-6 col-lg-6" style="padding-left: 0">
                                <label for="departure_date"><?php echo trans('main.DateFrom'); ?></label>
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <?php echo Form::text('from_date', $tourPackage->from_date , ['class' => 'form-control pull-right datepickerTourPackageDate', 'id' => 'from_date']); ?>

                                </div>
                            </div>

                            <div class="form-group col-md-6 col-lg-6" style="padding-right: 0">
                                <label for="departure_date"><?php echo trans('main.TimeFrom'); ?></label>
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-clock-o"></i>
                                    </div>
                                    <?php echo Form::text('from_time', $tourPackage->from_time, ['class' => 'form-control pull-right timepicker', 'id' => 'from_time']); ?>

                                </div>
                            </div>

                            <div class="form-group col-md-6 col-lg-6" style="padding-left: 0">
                                <label for="departure_date"><?php echo trans('main.DateTo'); ?></label>
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <?php echo Form::text('to_date', $tourPackage->to_date, ['class' => 'form-control pull-right datepickerTourPackageDate', 'id' => 'to_date']); ?>

                                </div>
                            </div>

                            <div class="form-group col-md-6 col-lg-6" style="padding-right: 0">
                                <label for="departure_date"><?php echo trans('main.TimeTo'); ?></label>
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-clock-o"></i>
                                    </div>
                                    <?php echo Form::text('to_time', $tourPackage->to_time, ['class' => 'form-control pull-right timepicker', 'id' => 'to_time']); ?>

                                </div>
                            </div>

							<?php if(\App\Helper\TourPackage\TourService::$serviceTypes[$tourPackage->type] == 'transfer' || \App\Helper\TourPackage\TourService::$serviceTypes[$tourPackage->type] == 'guide'): ?>
							<div class="form-group col-md-6 col-lg-6" style="margin-left: -15px;">
                                <?php echo Form::label('description', 'Pickup Description'); ?>

                                <?php echo Form::text('pickup_des', $tourPackage->pickup_des, ['class' => 'form-control']); ?>

                            </div>
							<div class="form-group col-md-6 col-lg-6" style="padding-right: 0">
                                <?php echo Form::label('drop_des', 'Drop Destination'); ?>

                                <?php echo Form::text('drop_des', $tourPackage->drop_des, ['class' => 'form-control']); ?>

                            </div>
							<?php endif; ?>
                            <?php if($tourPackage->type == 0): ?>
                            <div class="form-group">
                                <?php echo Form::label('city_tax', 'City Tax'); ?>

                                <?php echo Form::text('city_tax', $tourPackage->city_tax, ['class' => 'form-control']); ?>

                            </div>
                            <div class="form-group">
                                <label ><?php echo trans('main.RoomTypes'); ?>

                                    (
                                <a href="<?php echo e(route('hotel.show', ['hotel' => $tourPackage->reference, 'tab' => 'season_tab'])); ?>" ><?php echo trans('main.HotelSeasons'); ?></a>
                                    )</label>

                                <div id="list_selected_room_types">
                                    <?php
                                        $peopleCount = 0;
                                    ?>
                                    <?php $__currentLoopData = $selected_room_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $peopleCount += isset( App\TourPackage::$roomsPeopleCount[$item->code])
                                            ? App\TourPackage::$roomsPeopleCount[$item->code] * $item->count_room : 0;
                                        ?>
                                        <?php echo $__env->make('component.item_hotel_room_type', ['room_type' => $item], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($peopleCount != $tourPackage->pax + $tourPackage->pax_free): ?>
                                        <div class="alert alert-warning alert-dismissible">
                                            <button type="button" class="close" data-dismiss="alert"
                                                    aria-hidden="true">×
                                            </button>
                                            <i class="icon fa fa-warning"></i>
                                            <?php echo trans('main.PaxCount'); ?> (<?php echo e($tourPackage->pax + $tourPackage->pax_free); ?>) <?php echo trans('main.isnotequaltothenumberof'); ?> (<?php echo e($peopleCount); ?>)
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <button class="btn btn-success btn_for_select_room_type" type="button"><?php echo trans('main.SelectRooms'); ?></button>

                                <div class="alert alert-danger error_room_types" style="display: none; margin-top: 10px;">

                                </div>
                                <ul class="list_room_types">

                                    <?php $__currentLoopData = $room_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room_type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>


                                        <li class="select_room_type">
                                            <label><?php echo e($room_type->name); ?></label>
                                            <input type="text" data-info="<?php echo e($room_type->id); ?>" hidden value="<?php echo e($room_type); ?>">
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                </ul>
                            </div>
                            <?php endif; ?>

                            <?php if($tourPackage->type == 0 || $tourPackage->type == 4): ?>
                                <div class="repeater col-md-12 col-lg-12">
                                    <h5>Menus</h5>
                                    <div data-repeater-list="package_menu">
                                        
                                        <div class="row" data-repeater-item>
                                            <div class="col-md-3">
                                                <div class="form-group  package-menu-item">
                                                    <?php echo Form::label('count', 'Quantity'); ?>

                                                    <?php echo Form::input('string', 'count', 0 ,['class' => 'form-control']); ?>

                                                </div>
                                            </div>
                                            <?php if( isset($tourPackage->service()->menus)): ?>
                                            <div class="col-md-3">
                                                <div class="form-group package-menu-item">
                                                    <?php echo Form::label('menu', 'Menu'); ?>

                                                    <?php echo Form::select('menu',['' => '-- Choose Menu --'] + $tourPackage->service()->menus->pluck('name', 'id')->toArray(),  0, ['class' => 'form-control']); ?>


                                                </div>
                                            </div>
                                            <?php endif; ?>
                                            <div class="col-md-1">
                                                <div class="form-group">
                                                    <?php echo e(Form::label('')); ?>

                                                    <a href="#" data-repeater-delete  class="form-control btn btn-danger" name="remove" style="margin-top: 5px">
                                                        <i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                <?php $__currentLoopData = $tourPackage->menus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $packageMenu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="row" data-repeater-item>
                                        <div class="col-md-3">
                                            <div class="form-group  package-menu-item">
                                                <?php echo Form::label('count', 'Quantity'); ?>

                                                <?php echo Form::input('text', 'count', $packageMenu->count ,['class' => 'form-control new_qty']); ?>

                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group package-menu-item">
                                                <?php echo Form::label('menu', 'Menu'); ?>

												<select class="form-control new_menu select2-hidden-accessible" id="menu" name="package_menu[1][menu]" tabindex="-1" aria-hidden="true">
													<option value="">-- Choose Menu --</option>
													<?php $__currentLoopData = $tourPackage->service()->menus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
														<?php if(@$packageMenu->menu->id == $menu->id): ?>
															<option value="<?php echo e($menu->id); ?>" selected="selected" title="<?php echo 'Price : '.$menu->price; ?><?php echo '         Desc : '.strip_tags($menu->description); ?>"><?php echo e($menu->name); ?></option>
														<?php else: ?>
															<option value="<?php echo e($menu->id); ?>" title="<?php echo 'Price : '.$menu->price; ?>"><?php echo e($menu->name); ?></option>
														<?php endif; ?>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												</select>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group">
                                                <?php echo e(Form::label('')); ?>

                                                <a href="#" data-repeater-delete  class="form-control btn btn-danger" name="remove" style="margin-top: 5px">
                                                <i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                            </div>
                                        </div>
                                    </div>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                    </div>
                                    <button data-repeater-create class="btn btn-success" type="button"><i class="fa fa-plus fa-md" aria-hidden="true"></i> Add</button>

                                    <div class="alert alert-danger error_menu" style="display: none; margin-top: 10px;">

                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="form-group">
                                <?php echo Form::label('note', 'Note'); ?>

                                <?php echo Form::textarea('note', $tourPackage->note, ['class' => 'form-control']); ?>

                            </div>
                            <div class="form-group">
                                <?php echo Form::hidden('serviceType', \App\Helper\TourPackage\TourService::$serviceTypes[$tourPackage->type], ['id' => 'tour_package_service_type_value']); ?>

                                <?php echo Form::hidden('serviceId', $tourPackage->reference, ['id' => 'tour_package_service_type_id']); ?>

                                <?php if($tourPackage->tourDays()->first()    !== null): ?>
                                    <?php echo Form::hidden('tourDayId', $tourPackage->tourDays()->first()->id, ['id' => 'tour_package_tour_day_id']); ?>

                                <?php endif; ?>
                            </div>
                            <button class='btn btn-success' type='submit' id="send-tour-package-form"><?php echo trans('main.Save'); ?></button>
                            <a href="<?php echo e(\App\Helper\AdminHelper::getBackButton(route('tour.show', ['tour' => $tourPackage->getTour()->id]))); ?>">
                                <button class='btn btn-warning' type='button'><?php echo trans('main.Cancel'); ?></button>
                            </a>
                        </div>
                    </div>
                    <span id="tour_dep" data-departure_date='<?php echo e($tourPackage->getTour()->departure_date); ?>'></span>
                    <span id="tour_package_id" data-id='<?php echo e($tourPackage->id); ?>'></span>
                    <span id="tour_ret" data-retirement_date="<?php echo e($tourPackage->getTour()->retirement_date); ?>"></span>


                </form>
            </div>
    </section>


    <div class="modal fade" tabindex="-1" id="list-tour-packages" style="padding-left: 17px;padding-right: 17px;">
        <div class="modal-dialog modal-lg" style="width: 90%;">
            <div class="modal-content" style="overflow: hidden;">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss='modal' aria-label="Close"><span aria-hidden='true'>&times;</span></button>
                    <h4 class="modal-title"><?php echo trans('main.Changeservice'); ?></h4>
                </div>
                <div class="modal-body">
                    <table id="search-table-service-list" class="table table-striped table-bordered table-hover" style="width: 100%!important;">
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
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" id="confirmed_hotel">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="form_confirmed_hotel">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss='modal' aria-label="Close"><span aria-hidden='true'>&times;</span></button>
                        <h4 class="modal-title"><?php echo trans('main.Confirmedhotel'); ?></h4>
                    </div>
                    <div class="modal-body">
                        <div class="confirmed_hotel_block">

                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="btn-send-confirmed_hotel">
                            <button type="button" class="btn btn-success btn-send-confirmed_hotel_add"><?php echo trans('main.Save'); ?></button>
                            <button type="button" class="btn btn-default btn-send-hotel_cancel"><?php echo trans('main.Cancel'); ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="select-driver-and-bus_transfer_package">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="form_transfer_buses_drivers_transfer_package">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss='modal' aria-label="Close"><span aria-hidden='true'>&times;</span></button>
                        <h4 class="modal-title"><?php echo trans('main.Selectdriversandbuses'); ?></h4>
                    </div>
                    <div class="box box-body" style="border-top: none">
                        <div class="list-driver-and-buses_transfer_package"></div>

                        <div class="overlay" style="display: none">
                            <i class="fa fa-refresh fa-spin"></i>
                        </div>

                        <div class="modal-footer">
                            <div class="btn-send-driver">
                                <button type="button" class="btn btn-success btn-send-transfer_add_transfer_package"><?php echo trans('main.Add'); ?></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script type="text/javascript" src="<?php echo e(asset('js/tour.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('tour_package_script'); ?>
    <script type="text/javascript">
        let tour_dep_date = $('#tour_dep').attr('data-departure_date');
        let tour_ret_date = $('#tour_ret').attr('data-retirement_date');

        $('.datepickerTourPackageDate').datepicker({
            format: 'yyyy-mm-dd',
            autoclose : true,
            startDate: tour_dep_date,
            endDate: tour_ret_date
        });

        $(document).on('keydown', '.count_room_type', function(e){
            if (e.keyCode === 13) {
                e.preventDefault();
                $('.count_room_type').blur();
            }
        });

        $(document).on('focus', '.count_room_type', function (e) {
            $(this).select();
        });

        $('.select_room_type').click(function(){
            var data = $(this).find('input');
            var list_selected_rooms = $('.count_room_type');

            for(var i = 0; i < list_selected_rooms.length; i++){
                var item = list_selected_rooms[i];

                if($(item).attr('data-info') === data.attr('data-info')){
                    return false;
                }
            }

            $.ajax({
                method: 'POST',
                url: '/hotel_room_types',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    room_type: data.val()
                }
            }).done((res) => {
                $('#list_selected_room_types').append(res);
                $('.list_room_types').slideUp(200);
            })
        });

        $('.btn_for_select_room_type').click(function(){
            if($('.list_room_types').css('display') === 'none'){
                $('.list_room_types').slideDown(200);
            }else{
                $('.list_room_types').slideUp(200);
            }
        });

        $(document).on('click', '.icon_delete_room_type', function(){
            $(this).closest('.item_selected_room_type').remove();
        });
    </script>

    <script>
        var list_drivers = $('#driver-list-edit').find('option');
        var tour_package_id = $('#tour_package_id').attr('data-id');
        var driver_id = null;

        if(list_drivers.length !== 0){
            for (var i = 0; i < list_drivers.length; i++){
                if($(list_drivers[i]).is(':selected')){
                    driver_id = $(list_drivers[i]).val();
                }
            }
            getDriverBuses();
        }


        $('body').on('change', '#driver-list-edit', function () {
            driver_id = $(this).val();
            getDriverBuses();
        });



        function getDriverBuses() {
            $.ajax({
                method: 'GET',
                url: `/bus_driver_edit/api/${driver_id}`,
                data: {
                    tourPackageId : tour_package_id
                }
            }).done((res) => {
                $('.list-buses-edit').html(res);
            });
        }
    </script>

    <script type="text/javascript">

        function validateRoomTypes() {
            let list_count_rooms = $('.block-qty-room input');
            let list_price_rooms = $('.block-price-room input');
            $('.error_room_types').css({'display': 'none'});

            for(let i = 0; i < list_count_rooms.length; i++){
                // count validate
                if($(list_count_rooms[i]).val() === ''){
                    $('.error_room_types').css({'display': 'block'});
                    $('.error_room_types').html('Fill in the quantity fields please');
                    return false;
                }

                // price validate
                if($(list_price_rooms[i]).val() === ''){
                    $('.error_room_types').css({'display': 'block'});
                    $('.error_room_types').html('Fill in the price fields please');
                    return false;
                }
            }

            return true;
        }

        function validateMenu() {
            let list_menu = $('.new_qty');
            let select = $('.new_menu');

            $('.error_menu').css({'display': 'none'});

            if(list_menu.length === 0){
                return true;
            }


            for(let i = 0; i < list_menu.length; i++){
                if($(list_menu[i]).val() === ''){
                    $('.error_menu').css({'display': 'block'});
                    $('.error_menu').html('Fill in the quantity fields please');
                    return false;
                }
            }

            for (let j = 0; j < select.length; j++){
                if($(select[j]).select2('data')[0].element.value === ''){
                    $('.error_menu').css({'display': 'block'});
                    $('.error_menu').html('Select one of the list of items');
                    return false;
                }
            }

            return true;
        }


        $('#send-tour-package-form').click(function (e) {
            e.preventDefault();
            let oldForm = document.forms.tour_package_add_form;
            let form = new FormData(oldForm);
            var selected_status_id = form.get('status');
            var tour_package_id = form.get('tour_package_id');
            var serviceType = form.get('serviceType');


            if(serviceType === 'hotel'){
                if(!validateRoomTypes()){
                    return false;
                }
            }

            if(serviceType === 'hotel' || serviceType === 'restaurant'){
                if(!validateMenu()){
                    return false;
                }
            }

            $.ajax({
                method: 'GET',
                url: `/api/get_status/${selected_status_id}`,
                data: {}
            }).done((res) => {
                if(res){
                    $('#confirmed_hotel').modal();

                    $.ajax({
                        url: `/get_packages_for_delete/${tour_package_id}`,
                    }).done( (res) => {
                        $('.confirmed_hotel_block').html(res);

                        $('.btn-send-confirmed_hotel_add').click(function (e) {
                            tourServiceChanger.deleteTourPackagesHotel(tour_package_id);
                            // tourServiceChanger.updateStatusTourPackage($(that), _this, statusName, packageType, package_id, packageId);
                            $('#confirmed_hotel').modal('hide');
                            $('#tour_package_add_form').submit();
                        });

                        $('.btn-send-hotel_cancel').click(function (e) {
                            $('#confirmed_hotel').modal('hide');
                            return false;
                        });

                    });
                }else{
                    $('#tour_package_add_form').submit();
                }

            });
        });
    </script>

    <script>
        var $repeater = $('.repeater').repeater( {
            // (Required)
            // Specify the jQuery selector for this nested repeater
            selector: '.package-menu-item',
            show: function () {
                $(this).slideDown();
                $(this).find('.select2').remove();
                $(this).find('select').addClass('new_menu');
                $(this).find('input').addClass('new_qty');
//                $(this).find('select').data('select2').destroy();
                $(this).find('select').select2();
            },
            hide: function (deleteElement) {
                if(confirm('Are you sure you want to delete this element?')) {
                    $(this).slideUp(deleteElement);
                }
            },
        });
    </script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('scaffold-interface.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/tour_package/edit.blade.php ENDPATH**/ ?>