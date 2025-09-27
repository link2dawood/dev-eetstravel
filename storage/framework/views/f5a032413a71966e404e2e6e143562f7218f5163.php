<div class="list-group">
    
    <?php $countDay = 0; ?>
	<div class="alert alert-info block-error-driver" style="text-align: center; display: none;">

        		</div>
    <?php $__currentLoopData = $tourDates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tourDate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $countDay++ ?>
		
        <div class="box box-solid" >
			 
            <div class="box-header with-border">
                <?php if(Auth::user()->can('tour_package.create')): ?>
                    <button class="btn btn-flat btn-success pull-right add-service-quick"
                            data-tourDayId="<?php echo e($tourDate->id); ?>"
                            data-link="<?php echo e(route('tour_package.store')); ?>" data-date="<?php echo $tourDate->date; ?>"
                            data-tour_id='<?php echo e($tour->id); ?>'
                            data-departure_date='<?php echo e($tour->departure_date); ?>'
                            data-retirement_date="<?php echo e($tour->retirement_date); ?>"><?php echo trans('main.AddService'); ?>

                    </button>
                    <button class="btn btn-flat btn-success pull-right add-description-package" onclick=loadDescriptionemplate()><?php echo trans('main.Adddescription'); ?></button>
                <?php endif; ?>
                <h3 class="box-title"><?php echo trans('main.Day'); ?> <?php echo e($countDay); ?>

                    - <?php echo e((new \Carbon\Carbon($tourDate->date))->formatLocalized('%B %d, %Y (%A)')); ?></h3>
                <br/><br/>
                <div class="box-body">
					
                    <table class="table table-striped table-bordered table-hover <?php echo e(\App\Helper\PermissionHelper::checkPermission('tour_package.edit') ? 'package-service-table' : ''); ?>"
                           style='background:#fff'>
                        <colgroup>
                            <col style="width: auto">
                            <col style="width: auto">
                            <col style="width: auto">
                            <col style="width: auto;">
                            <col style="width: auto;">
                            <col style="width: auto;">
                            <col style="width: auto;">
                            <col style="width: auto;">
                            <col style="width: auto;">
                            <col style="width: auto;">
                            <!--<col style="width: 30%;">
                            <col style="width: auto;">-->
                            <col style="width: auto;">
                        </colgroup>
                        <thead>
                        <tr>
                            <th>Itn</th>
                            <th>Vch</th>
                            <th><?php echo trans('main.FromTime'); ?></th>
                            <th></th>
                            <th style="width: 15%; min-width: 100px;"><?php echo trans('main.Name'); ?></th>
                            <th style="min-width: 150px"><?php echo trans('main.Status'); ?></th>
                            <th>Pax</th>
                            <th>Rooms</th>
                            <th><?php echo trans('main.Description'); ?></th>
							<th>Offers</th>
                        
                        <!-- <th style="width: 120px">Rooms Hotel</th>-->
                            <th style="width: 150px; min-width: 150px"><?php echo trans('main.Actions'); ?></th>
                        </tr>
                        </thead>
                        <tbody data-default_tour_day_id='<?php echo e($tourDate->id); ?>'>
                        <?php $__currentLoopData = $tourDate->packages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $package): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							
							<tr >
								<td colspan="14"><?php if(Auth::user()->can('tour_package.create')): ?>
                    <button class="btn btn-flat btn-success pull-right add-service-quick"
                            data-tourDayId="<?php echo e($tourDate->id); ?>"
                            data-link="<?php echo e(route('tour_package.store')); ?>" data-date="<?php echo $tourDate->date; ?>"
                            data-tour_id='<?php echo e($tour->id); ?>'
                            data-departure_date='<?php echo e($tour->departure_date); ?>'
                            data-retirement_date="<?php echo e($tour->retirement_date); ?>"><?php echo trans('main.AddService'); ?>

                    </button>
                    <button class="btn btn-flat btn-success pull-right add-description-package" onclick=loadDescriptionemplate()><?php echo trans('main.Adddescription'); ?></button>
                <?php endif; ?></td>
							</tr>
                            <?php if($package->description_package): ?>
								
                                <tr data-package_id='<?php echo e($package->id); ?>' data-type='<?php echo e($package->type); ?>'
                                    data-is_main='<?php if(!$package->hasParrent()): ?> <?php echo e($package->main_hotel); ?> <?php else: ?> <?php echo e(false); ?> <?php endif; ?>' >
                                    <td valign="center" align="center" class="not-click">
                                        <?php if($package->itn == 1): ?>
                                        <input type="checkbox" value="<?php echo e($package->id); ?>" class="export_selected" checked>
                                        <?php else: ?>
                                        <input type="checkbox" value="<?php echo e($package->id); ?>" class="export_selected" >
                                        <?php endif; ?>
                                    </td>
                                    <td valign="center" align="center" class="not-click">
                                        
                                        <?php if($package->vch == 1): ?>
                                        <input type="checkbox" value="<?php echo e($package->id); ?>" class="export_selected_vch"
                                               checked>
                                        <?php else: ?>
                                               <input type="checkbox" value="<?php echo e($package->id); ?>" class="export_selected_vch"
                                               >
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <input style="width: 80px; background-color: inherit; border-style: none;"
                                               type="text"
                                               data-package_id="<?php echo e($package->id); ?>"
                                               class="form-control timepicker <?php echo e(\App\Helper\PermissionHelper::checkPermission('tour_package.edit') ? 'service-time' : ''); ?>"
                                               name="time_from"
                                               value="<?php echo $package->time_from; ?>">
                                        <span class="package-data" data-type='<?php echo e($package->type); ?>'
                                              data-tour_day_id='<?php echo e($tourDate->id); ?>' data-package_id='<?php echo e($package->id); ?>'
                                              data-package-type="service_description"
                                              data-is_main='<?php if(!$package->hasParrent()): ?> <?php echo e($package->main_hotel); ?> <?php else: ?> <?php echo e(false); ?> <?php endif; ?>'
                                        ></span>
                                    </td>
									<td colspan="9">
                                    <div 
                                        class="<?php echo e(\App\Helper\PermissionHelper::checkPermission('tour_package.edit') ? 'service-description' : ''); ?>" id="service-description<?php echo e($package->id); ?>"><?php echo $package->description; ?></div>
										<?php if(strlen($package->description )>1000): ?>
										<p style="color:blue;width: 100%;float: right;" id="readmore<?php echo e($package->id); ?>" onclick="toggleReadMore(this,<?php echo e($package->id); ?>)">readmore</p>
										<?php endif; ?>
									</td>
									<td></td>
                                    <td style="text-align: center">
                                        <?php if(Auth::user()->can('tour_package.destroy')): ?>
                                            <a data-toggle="modal" data-target="#myModal"
                                               class="delete btn btn-danger btn-xs"
                                               data-link="/tour_package/<?php echo e($package->id); ?>/deleteMsg"
                                               style="text-align: center"><i class="fa fa-trash-o"
                                                                             aria-hidden="true"></i></a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <?php if(!$package->description_package): ?>
								<?php
								$background = "";
								if($package->service()->service_type??"" == "Hotel"){
						
									if($package->status == 23){
										$background = "background: rgb(202, 255, 189);";
										}
								
								}else{
									if($package->status == 9){
										$background = "background: rgb(202, 255, 189);";
										}
									}
								?>
                                <tr data-package_id='<?php echo e($package->id); ?>'
                                    data-type='<?php echo e(@$package->service()->service_type??""); ?>'
                                    data-is_main='<?php if(!$package->hasParrent()): ?> <?php echo e($package->main_hotel); ?> <?php else: ?> <?php echo e(false); ?> <?php endif; ?>'
                                    class="tour-package-item" style="<?php echo e($background); ?>">
                                    <td valign="center" align="center" class="not-click">
										<?php if(@$package->fellow_hotel_confirm == 0): ?>
                                        <?php if($package->itn == 1): ?>
                                        <input type="checkbox" value="<?php echo e($package->id); ?>" class="export_selected" checked>
                                        <?php else: ?>
                                        <input type="checkbox" value="<?php echo e($package->id); ?>" class="export_selected" >
                                        <?php endif; ?>
										<?php endif; ?>
                                    </td>
                                    <td valign="center" align="center" class="not-click">

                                        <?php
                                            $menu = '';
                                            $tourid = null ;
                                        ?>
                                        <?php if($package->type == 0 || $package->type == 4): ?>
                                            <?php
										if(!empty(@$package->service()->menus)){
                                                if(count(@$package->service()->menus) > 0){
													
                                                    foreach(@$package->service()->menus as $men){
									
                                                     //if ($men['id'] == $package->menu_id){
                                                            $menu = $men['name'];
                                                       // }
                                                    }
                                                }
										}
						
                                            ?>
                                        <?php endif; ?>
										<?php if(@$package->fellow_hotel_confirm == 0): ?>
                                        <?php if($package->vch == 1): ?>
                                        <input type="checkbox" value="<?php echo e($package->id); ?>" class="export_selected_vch"
                                               checked>
                                        <?php else: ?>
                                               <input type="checkbox" value="<?php echo e($package->id); ?>" class="export_selected_vch"
                                               >
                                        <?php endif; ?>
										<?php endif; ?>
                                    </td>
                                    <td class="not-click">
                                        <?php if(!$package->hasParrent()): ?>
                                            <?php if(\App\Helper\PermissionHelper::checkPermission('tour_package.edit')): ?>
                                                <input style="width: 80px; background-color: inherit; border-style: none;"
                                                       type="text"
                                                       data-package_id="<?php echo e($package->id); ?>"
                                                       class="form-control timepicker service-time"
                                                       name="time_from"
                                                       value="<?php echo $package->time_from; ?>"
                                                       data-type='<?php echo e(@$package->service()->service_type??""); ?>'
                                                       data-is_main='<?php if(!$package->hasParrent()): ?> <?php echo e($package->main_hotel); ?> <?php else: ?> <?php echo e(false); ?> <?php endif; ?>'
                                                >
                                            <?php else: ?>
                                                <span><?php echo e($package->time_from); ?></span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <td>
                                        <?php if(@$package->service()->service_type??"" === 'Hotel'): ?>
                                            <?php if($package->parent_id): ?>
                                                <i class="fa fa-star-o text-yellow"></i>
                                            <?php else: ?>
                                                <i class="fa fa-star text-yellow"></i>
                                            <?php endif; ?>

                                        <?php endif; ?>
                                    </td>
                                    <td><span class="package-data" data-tour_day_id='<?php echo e($tourDate->id); ?>'
                                              data-package_id='<?php echo e($package->id); ?>'
                                              data-package-type="<?php echo e(@$package->service()->service_type??""); ?>"
                                              data-is_main='<?php if(!$package->hasParrent()): ?> <?php echo e($package->main_hotel); ?> <?php else: ?> <?php echo e(false); ?> <?php endif; ?>'
                                        ><?php echo e($package->name  . ' (' .@$package->service()->service_type??"". ')'); ?><?php echo e(@$package->service()->address_first); ?> <?php echo @$package->service()->work_email; ?> <?php echo @$package->service()->work_phone; ?></span><br>
										<?php if(@$package->service()->service_type??"" == 'Transfer' || @$package->service()->service_type??"" == 'Guide'): ?>
										<span>Pickup:<?php echo e($tourDate->date. " " .$package->time_from); ?>/<?php echo e($package->pickup_des); ?> at <?php echo e($package->time_to); ?> Dropoff: <?php echo e($tourDate->date. " " .$package->time_to); ?>/<?php echo e($package->drop_des); ?> at <?php echo e($package->time_to); ?></span>
										<?php endif; ?>
									
									</td>
                                    <?php if(!$statusPackage->isEmpty()): ?>
                                        <?php
                                            $status = false;
                                            $status_name = '';
                                        ?>
                                        <?php $__currentLoopData = $statusPackage; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php if($item->id == $package->status): ?>

                                                <td class="<?php echo e(\App\Helper\PermissionHelper::checkPermission('tour_package.edit') ? 'tour_package_status' : ''); ?>"
                                                    data-info-package-id="<?php echo e($package->parent_id != null ? $package->parent_id  : $package->id); ?>"
                                                    data-info-status="<?php echo e($item->name); ?>"
                                                    data-info-status_id="<?php echo e($item->id); ?>"
                                                    data-info-package-type="<?php echo e((@$package->service()->service_type??"" === 'Hotel') ? 'hotel':  ''); ?><?php echo e((@$package->service()->service_type??"" === 'Transfer') ? 'transfer':  ''); ?>">
                                                    <?php echo e($item->name); ?> </td>
                                                <?php $status_name = $item->name; ?>
                                                <?php
                                                    $status = true;
                                                ?>
                                            <?php else: ?>

                                            <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php if(!$status): ?>
                                            <td></td>
                                        <?php endif; ?>

                                    <?php else: ?>
                                        <td class="<?php echo e(\App\Helper\PermissionHelper::checkPermission('tour_package.edit') ? 'tour_package_status' : ''); ?>"
                                            data-info-package-id="<?php echo e($package->parent_id != null ? $package->parent_id  : $package->id); ?>"
                                            data-info-status="<?php echo e($package->status); ?>"
                                            data-info-status_id="<?php echo e($package->status->id); ?>"
                                            data-info-package-type="<?php echo e((@$package->service()->service_type??"" === 'Hotel') ? 'hotel':  ''); ?><?php echo e((@$package->service()->service_type??"" === 'Transfer') ? 'transfer':  ''); ?>">
                                            <?php echo e($package->status); ?>

                                        </td>
                                    <?php endif; ?>
                                   
                                    <td><?php echo e($package->pax); ?> <?php echo e($package->pax_free); ?></td>
                                    <td>
                                        <?php $__currentLoopData = $package->room_types_hotel; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <span>
                                                <?php echo e($item->room_types->code); ?>

                                                <?php echo e($item->count); ?>

                                            </span>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </td>
                                    <td class="<?php echo e(\App\Helper\PermissionHelper::checkPermission('tour_package.edit') ? 'service-description' : ''); ?>"><?php echo $package->description; ?></td>
									<?php $offer_hotel_count = count($package->hotel_offers) > 0 ? count($package->hotel_offers) : 0; ?>
									<?php if($package->service()->service_type??"" == "Hotel"): ?>
									<td><table class="table table-striped table-bordered table-hover" >
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Status</th>
                                        <th>HPP</th>
										
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $package->hotel_offers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $offer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<?php 
									$hotelpp= 0;
									if(!empty( $offer)){
										foreach ($offer->offer_room_prices as $offer_room_price) {
											if ($offer_room_price->room_type_id == 2 || $offer_room_price->room_type_id == 6) {
												$hotelpp = $offer_room_price->price/2;
											}
										}
														
									}
														
														?>
                                        <tr>
                                            <td><?php echo e($offer->id ?? ''); ?></td>
                                            <td><?php echo e($offer->status ?? ''); ?></td>
                                            <td><?php echo e($hotelpp ?? ''); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
										<a href='/tour_package/hotel_offers/<?php echo e($package->parent_id != null ? $package->parent_id : $package->id); ?>' class="btn btn-primary">Show Offers</a>
									</td>
									<?php else: ?>
									<td></td>
									<?php endif; ?>
                                    
                                    
                                    <?php if(!$package->hasParrent()): ?>
                                        <td style="text-align: center;width:150px;">
                                            
											<?php if(@$package->fellow_hotel_confirm == 0): ?>
                                            <?php if($package->type == 0): ?>
                                                <?php $tourid = $tour->id; ?>
                                                <button class="<?php echo e(\App\Helper\PermissionHelper::checkPermission('comparison.show') ? 'main-hotel' : 'disabled'); ?> btn btn-xs <?php echo e($package->main_hotel ? 'btn-success' : 'btn-danger'); ?>"
                                                        type="button" style="margin-bottom: 5px">M
                                                </button>
                                            <?php endif; ?>
											<?php endif; ?>
											<?php if(@$package->fellow_hotel_confirm == 0): ?>
                                            <?php if(Auth::user()->can('tour_package.edit')): ?>
                                                <a href="/tour_package/<?php echo e($package->parent_id != null ? $package->parent_id  : $package->id); ?>/edit"
                                                   class="btn btn-primary btn-xs show-button"
                                                   data-link="/tour_package/<?php echo e($package->parent_id != null ? $package->parent_id  : $package->id); ?>/edit"
                                                   style="margin-bottom: 5px"><i
                                                            class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                            <?php endif; ?>
											<?php endif; ?>
                                            <?php if(Auth::user()->can('tour_package.destroy')): ?>
                                                <a data-toggle="modal" data-target="#myModal"
                                                   class="delete btn btn-danger btn-xs" style="margin-bottom: 5px"
                                                   data-link="/tour_package/<?php echo e($package->parent_id != null ? $package->parent_id  : $package->id); ?>/deleteMsg"><i
                                                            class="fa fa-trash-o" aria-hidden="true"></i></a>
                                            <?php endif; ?>
											<?php if(@$package->fellow_hotel_confirm == 0): ?>
                                            <?php if($package->parent_id == null): ?>
                                                <?php if(Auth::user()->can('tour_package.create') && Auth::user()->can('tour_package.destroy')): ?>
                                                    <a href="#" class="btn btn-warning btn-xs open-modal-change-service"
                                                       style="display: <?php echo e(@$package->getStatusName() !== 'Confirmed' ? 'inline-block' : 'none'); ?>; margin-bottom: 5px"
                                                       data-toggle="modal"
                                                       data-target="#list-tour-packages"
                                                       data-serviceTypeId="<?php echo e($package->type); ?>"
                                                       data-serviceId="<?php echo e(@$package->service()->id); ?>"
                                                       data-packageId="<?php echo e($package->parent_id != null ? $package->parent_id  : $package->id); ?>"
                                                       data-tour-day-id="<?php echo e($tourDate->id); ?>"
                                                       data-tour_id="<?php echo e($package->getTour()->id); ?>"
                                                       data-time-old-service="<?php echo e(!$package->hasParrent() ? $package->time_from : null); ?>"
                                                       data-link="<?php echo e(route('tour_package.store')); ?>"
                                                    >
                                                        <i class="fa fa-exchange" aria-hidden="true"></i>
                                                    </a>
                                                <?php endif; ?>
												<?php endif; ?>
                                                <?php if(Auth::user()->can('tour_package.edit')): ?>
                                                    <?php if(@$package->fellow_hotel_confirm == 0): ?>
											<?php $packageName = addslashes($package->name);$tourName = addslashes($tour->name);
											$desc = addslashes($package->description);
											?>
                                                        <button style="margin-bottom: 5px" href="javascript:void(0);"
                                                           data-info="<?php echo e(@$package ?: \GuzzleHttp\json_encode(' ')); ?>"
                                                           onclick="loadTemplate(JSON.parse($(this).attr('data-info')) ? JSON.parse($(this).attr('data-info')).type : '','<?php echo @$package->service()->work_email; ?>','<?php echo $packageName; ?>','<?php echo $package->pax; ?> <?php echo $package->pax_free; ?>','', '<?php echo @$package->service()->work_email; ?>','<?php echo @$package->service()->work_phone; ?>','<?php echo $desc; ?>','<?php echo $status_name; ?>','<?php echo $package->time_from; ?>','<?php echo $package->time_to; ?>','<?php echo $package->supplier_url; ?>','<?php echo $package->total_amount; ?>','<?php echo e($menu); ?>','<?php echo e($tour->id); ?>','<?php echo e($package->reference); ?>','<?php echo e($tourName); ?>','<?php echo e($package->id); ?>');"
                                                           class="btn btn-success btn-xs"
                                                        ><i class="fa fa-envelope" aria-hidden="true"></i></button>
													
                                                  <?php endif; ?>
													
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>

<div class="modal fade" tabindex="-1" id="list-tour-packages" style="padding-left: 17px;padding-right: 17px;">
    <div class="modal-dialog modal-lg" style="width: 90%;">
        <div class="modal-content" style="overflow: hidden;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss='modal' aria-label="Close"><span aria-hidden='true'>&times;</span>
                </button>
                <h4 class="modal-title"><?php echo trans('main.Changeservice'); ?></h4>
            </div>
            <div class="box box-body" style="border-top: none">
                <table id="search-table-service-list" class="table table-striped table-bordered table-hover"
                       style="width: 100%!important;">
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


<div class="modal fade" tabindex="-1" id="select-driver-and-bus">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="form_transfer_buses_drivers">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss='modal' aria-label="Close"><span
                                aria-hidden='true'>&times;</span></button>
                    <h4 class="modal-title"><?php echo trans('main.Selectdriversandbuses'); ?></h4>
                </div>
                <div class="box box-body" style="border-top: none">
				<div class="form-group">

                    <label for="departure_date"><?php echo trans('Pickup Destination'); ?></label>

                    <div class="input-group">
                        <?php echo Form::text('pickup_des', '', ['class' => 'form-control',
                         'id' => 'pickup_des']); ?>

                    </div>

                </div>
				<div class="form-group">

                    <label for="departure_date"><?php echo trans('Drop Destination'); ?></label>

                    <div class="input-group">
                        <?php echo Form::text('drop_des', '', ['class' => 'form-control',
                         'id' => 'drop_des']); ?>

                    </div>

                </div>
                    <div class="list-driver-and-buses"></div>

                    <div class="modal-footer">
                        <div class="btn-send-driver">
                            <button type="button"
                                    class="btn btn-success btn-send-transfer_add pre-loader-func"><?php echo trans('main.Add'); ?></button>
                        </div>
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
                    <button type="button" class="close" data-dismiss='modal' aria-label="Close"><span
                                aria-hidden='true'>&times;</span></button>
                    <h4 class="modal-title">Select drivers and buses</h4>
                </div>
                <div class="box box-body" style="border-top: none">
                    <div class="list-driver-and-buses_transfer_package"></div>

                    <div class="modal-footer">
                        <div class="btn-send-driver">
                            <button type="button"
                                    class="btn btn-success btn-send-transfer_add_transfer_package pre-loader-func"><?php echo trans('main.Add'); ?></button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>



<div class="modal fade" tabindex="-1" id="confirmed_hotel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="form_confirmed_hotel">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss='modal' aria-label="Close"><span
                                aria-hidden='true'>&times;</span></button>
                    <h4 class="modal-title"><?php echo trans('main.Confirmedhotel'); ?></h4>
                </div>
                <div class="modal-body">
                  
                </div>
                <div class="modal-footer">
                    <div class="btn-send-confirmed_hotel">
                        <button type="button"
                                class="btn btn-success btn-send-confirmed_hotel_add"><?php echo trans('main.Save'); ?></button>
                        <button type="button"
                                class="btn btn-default btn-send-hotel_cancel"><?php echo trans('main.Cancel'); ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" tabindex="-1" id="error_hotel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="form_eror_hotel">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss='modal' aria-label="Close"><span
                                aria-hidden='true'>&times;</span></button>
                    <h4 class="modal-title"><?php echo trans('main.Warning'); ?>!</h4>
                </div>
                <div class="modal-body">
                    <div class="confirmed_hotel_block">
                        <h3 id="message"></h3>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="btn-error-hotel">
                        <button type="button" class="btn btn-success " id="ok">Ok</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="selectDateForHotelPackage" tabindex="-1" aria-labelledby='selectDateForHotelPackageLabel'>
    <div class="modal-dialog modal-lg" role='document'>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span>
                </button>
                <h4 class="modal-title"><?php echo trans('main.SelectDate'); ?></h4>
            </div>
            <div class="box box-body" style="border-top: none">

                <div class="alert alert-info error_date" style="text-align: center; display: none;">

                </div>

                <div class="form-group">

                    <label for="departure_date"><?php echo trans('main.DateFrom'); ?></label>

                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <?php echo Form::text('date_service_package', '', ['class' => 'form-control pull-right datepickerDisabledHotelPackage',
                         'id' => 'date_service_package']); ?>

                    </div>

                </div>

                <div class="form-group">

                    <label for="departure_date"><?php echo trans('main.Dateto'); ?></label>

                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <?php echo Form::text('date_service_retirement_package', '', [
                        'class' => 'form-control pull-right datepickerDisabledHotelPackage',
                        'id' => 'date_service_retirement_package'
                        ]); ?>

                    </div>
					
                </div>
				<div class="form-group">
					<label for="appt">Select a time:</label>
					 <div class="input-group date">
<input type="time" id="date_service_time" name="time">
					</div>
					</div>
                <button class="addHotelPackageWithDate pre-loader-func btn btn-success"
                        type="button"><?php echo trans('main.Add'); ?></button>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="selectDateForTransferPackage" aria-labelledby='selectDateForTransferPackageLabel'>
    <div class="modal-dialog modal-lg" role='document'>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span>
                </button>
                <h4 class="modal-title"><?php echo trans('main.SelectDate'); ?></h4>
            </div>
            <div class="modal-body">

                <div class="alert alert-info error_date" style="text-align: center; display: none;">

                </div>


                <div class="form-group">

                    <label for="departure_date"><?php echo trans('main.DateFrom'); ?></label>

                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <?php echo Form::text('date_service_package', '', ['class' => 'form-control pull-right datepickerDisabledTransferPackage',
                         'id' => 'date_service_transfer_package']); ?>

                    </div>

                </div>

                <div class="form-group">

                    <label for="departure_date"><?php echo trans('main.DateTo'); ?></label>

                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <?php echo Form::text('date_service_retirement_package', '', [
                        'class' => 'form-control pull-right datepickerDisabledTransferPackage',
                        'id' => 'date_service_transfer_retirement_package'
                        ]); ?>

                    </div>

                </div>
                <div style="overflow: hidden; display: block">
			 
                  <button class="addTransferPackageWithDate btn btn-success pull-right"
                            type="button"><?php echo trans('main.Next'); ?></button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="TemplatesModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="false" style="padding-left: 17px;padding-right: 17px;">
    <div class="modal-dialog modal-lg" style="width: 90%;">
        <form class="modal-content" id="templateSendForm" enctype="multipart/form-data" action="/templates/api/send"
              method="POST">
            <input name="_token" type="hidden" value="<?php echo e(csrf_token()); ?>">
            <input name="id" id="id" type="hidden" value="">
			<input name="package_id" id="package_id" type="hidden" value="">
			<input name="tour_id" id="tour_id" type="hidden" value="">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><?php echo trans('main.SendTemplate'); ?></h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">

                    <div class="form-group">
                        <div class="input-group">
                            <input name="email" id="email" class="form-control" placeholder="E-mail:" required=""
                                   value="">
							 
                            <span class="input-group-addon"> <?php echo trans('main.Template'); ?></span>
                            <!-- insert this line -->
                            <span class="input-group-addon"
                                  style="width:0px; padding-left:0px; padding-right:0px; border:none;"></span>

                            <select id="template_selector" name="template_selector" class="form-control">
                            </select>
							
                        </div>
						<select id="emails_select" class="form-control">
                                </select>
                    </div>
                    <div class="form-group">
                        <input name="subject" id="subject" class="form-control" placeholder="Subject:" value="" style="
    pointer-events: none;
">
                    </div>
                    <div class="form-group">
                            <textarea name="templatesContent" id="templatesContent" placeholder="Non required Field"
                                      class="form-control" style="height: 400px; visibility: hidden; display: none;">
                            </textarea>
                    </div>
                    <div class="form-group">
                        <div class="btn btn-default btn-file">
                            <i class="fa fa-paperclip"></i> <?php echo trans('main.Attachment'); ?>

                            <input type="file" name="attachment[]" multiple="" name="file" id="file">
                        </div>
                        <div id="file_name"></div>
                        <script>
                            document.getElementById('file').onchange = function () {
                                $('#file_name').html('Selected files: <br/>');
                                $.each(this.files, function (i, file) {
                                    $('#file_name').append(file.name + ' <br/>');
                                });
                            };
                        </script>
                        <p class="help-block">Max. 32MB</p>
                    </div>
                </div>

                <!-- /.box-body -->
                <div class="box-footer">
                    <div class="pull-right">
                        <button id="send" onclick="sendTemplate();" class="btn btn-primary"><i
                                    class="fa fa-file-code-o"></i> <?php echo trans('main.Send'); ?></button>
                    </div>
                    <button type="reset" class="btn btn-default modal-close" data-dismiss="modal"><i
                                class="fa fa-times"></i> <?php echo trans('main.Discard'); ?></button>
                </div>
                <!-- /.box-footer -->
            </div>

        </form>
    </div>
</div>

<span id="last_package_object" data-info="<?php echo e(@$package ?
 @$package->description_package == null ? @$package->type : \GuzzleHttp\json_encode('') :
    \GuzzleHttp\json_encode('')); ?>"></span>
<script type="text/javascript">


    var unchecked_array = [];
    var unchecked_array_vch = [];

    $(document).ready(function () {
        // $('input.timepicker').timepicker();
      //  finder.init();
      //  addService.checkTransferIsExist();
        let package_type = JSON.parse($('#last_package_object').attr('data-info'));
        if (package_type) {
            loadGuestTemplate(package_type, "<?php echo @$package ? @$package->service()->work_email : ''; ?>", "<?php echo @$package->name; ?>", "<?php echo @$package->pax; ?> <?php echo @$package->pax_free; ?>", "<?php echo @$package ? @$package->service()->address_first : ''; ?>", "<?php echo @$package ? @$package->service()->work_email : ''; ?>", "<?php echo @$package ? @$package->service()->work_phone  : ''; ?>", `<?php echo @$package->description; ?>`, "<?php echo @$status_name; ?>", "<?php echo @$package->time_from; ?>", "<?php echo @$package->total_amount; ?>", "<?php echo e(@$menu); ?>", "<?php echo e(@$tourid); ?>");
        }
        // $('.timepicker').datetimepicker({
        //     format: 'HH:mm', 'sideBySide' : true,
        //     tooltips: {
        //         incrementHour: '',
        //         pickHour: '',
        //         decrementHour:'',
        //         incrementMinute: '',
        //         pickMinute: '',
        //         decrementMinute:'',
        //         incrementSecond: '',
        //         pickSecond: '',
        //         decrementSecond:'',

        //     }


        // }).on("dp.hide", function (e) {
        //      let timeKey = $(this).attr('name');
        //      console.log($(this).attr('name'));
        //      // $(this).datetimepicker('hide');
        //      $.ajax({
        //         url: '/tour_package/' + $(this).data('package_id') + '/change_time',
        //         method: 'GET',
        //         data: {
        //             timeKey: timeKey,
        //             timeValue: $(this).val()
        //         }
        //      }).done( (res) => {
        //         addService.addPackages();
        //      })
        //      // return false;
        // });

        if ($(document).find('#templatesContent').length > 0) {
            if (CKEDITOR.instances['templatesContent']) {
                CKEDITOR.instances['templatesContent'].destroy(true);
            }
            CKEDITOR.replace('templatesContent', {
                extraPlugins: 'confighelper',
                height: '200px',
                title: false
            });
        }

        $('.export_selected').iCheck({
            checkboxClass: 'icheckbox_minimal-blue',
            radioClass: 'iradio_minimal-blue'
        }).on('ifClicked', function (e) {

            $(this).on('ifUnchecked', function (event) {

                unchecked_array[event.target.value] = event.target.value;
                $.ajax({
                    type: 'POST', // Use POST or GET as needed
                    url: '/update_itnid', // Replace with the actual URL that handles the update
                    data: {
                    id:event.target.value ,
                    value:0,
                    
                    },
                });

            });

            $(this).on('ifChecked', function (event) {

                delete unchecked_array[event.target.value];
                
                $.ajax({
                    type: 'POST', // Use POST or GET as needed
                    url: '/update_itnid', // Replace with the actual URL that handles the update
                    data: {
                    id:event.target.value ,
                    value:1,
                    
                    },
                });


            });

        });

        $('.export_selected_vch').iCheck({
            checkboxClass: 'icheckbox_minimal-blue',
            radioClass: 'iradio_minimal-blue'
        }).on('ifClicked', function (e) {

            $(this).on('ifUnchecked', function (event) {

                unchecked_array_vch[event.target.value] = event.target.value;
                $.ajax({
                    type: 'POST', // Use POST or GET as needed
                    url: '/update_voucherid', // Replace with the actual URL that handles the update
                    data: {
                    id:event.target.value ,
                    value:0,
                    
                    },
                });

            });

            $(this).on('ifChecked', function (event) {

                delete unchecked_array_vch[event.target.value];
                $.ajax({
                    type: 'POST', // Use POST or GET as needed
                    url: '/update_voucherid', // Replace with the actual URL that handles the update
                    data: {
                    id:event.target.value ,
                    value:1,
                    
                    },
                });

            });

        });
    });
    export_to = function (url) {

        console.log(url);

        var out = '?';

        for (var i = 0; i < unchecked_array.length; i++) {
            if (unchecked_array[i]) out += "exclude[]=" + unchecked_array[i] + "&";

        }

        for (var i = 0; i < unchecked_array_vch.length; i++) {
            if (unchecked_array_vch[i]) out += "exclude_vch[]=" + unchecked_array_vch[i] + "&";

        }


        out = out.slice(0, -1);

        //console.log(url+"/"+out);
        if (url.indexOf("landingpage") > 0) {
            $('#landingpage_modal').modal('hide');
            window.open(
                url + "/" + out,
                '_blank'
            )
        } else
            window.location.href = url + "/" + out;

    };

    sendTemplate = function () {

        $('form#templateSendForm').submit(function (event) {

            event.preventDefault();

            $('#TemplatesModal').find('#send').prop('disabled', true);

            var data = new FormData($(this)[0]);

            $.ajax({
                url: '/templates/api/send',
                method: 'POST',
                enctype: 'multipart/form-data',
                processData: false,
                contentType: false,
                cache: false,
                timeout: 600000,
                data: data
            }).done((res) => {
                console.log(res);
                $('#TemplatesModal').find('#email').val('');
                $('#TemplatesModal').find('#subject').val('');
//                CKEDITOR.instances['templatesContent'].setData('This field is optional');
                $('#TemplatesModal').modal('hide');
				$.toast({
                            heading: 'Success',
                            text: "Email sended successfully",
                            icon: 'success',
                            loader: true,        // Change it to false to disable loader
                            hideAfter : 3000,
                            position: 'top-right',
                        });
            });

            return false;
        });

    };


    loadTemplateById = function (service_id, email, name, pax, address, emailto, phone, description, status, time_from,time_to,supplier_url,price_for_one, menu, tour_id, reference,tour_name,package_id) {

        var id = $('#TemplatesModal').find('#template_selector').val();

        $.ajax({
            url: '/templates/api/load',
            method: 'GET',
            data: {
                service_id: service_id,
                id: id,
                email: email,
                name: name,
                pax: pax,
                address: address,
                emailto: emailto,
                phone: phone,
                description: description,
                status: status,
                time_from: time_from,
				time_to: time_to,
                supplier_url: supplier_url,
                price_for_one: price_for_one,
                menu: menu,
                tour_id: tour_id,
				reference: reference,
				tour_name:tour_name,
				package_id:package_id,
            }
        }).done((res) => {
          //  $('#TemplatesModal').find('#email').val(res.email);
			var emailsSelect = $('#emails_select');
			emailsSelect.empty();

			// Check if there are values in the array
			if (res.emails.length > 1) {
				$.each(res.emails, function(index, value) {
					emailsSelect.append('<option value="' + value + '">' + value + '</option>');
				});
			} else if (res.emails.length === 1) {
				// If there is only one value, you might want to set it as the selected option
				$('#TemplatesModal').find('#email').val(res.emails[0]);
				emailsSelect.hide();
			} else {
				// If there are no values, you might want to add a default option or handle it in some way
				emailsSelect.hide();
			}

            $('#TemplatesModal').find('#id').val(id);
			$('#TemplatesModal').find('#subject').val(tour_name+" Request #"+package_id);
			$('#TemplatesModal').find('#package_id').val(package_id);
			$('#TemplatesModal').find('#tour_id').val(tour_id);
//            if(res.content === '' ) res.content = 'This field is optional'; 
            CKEDITOR.instances['templatesContent'].setData(res.content);
			CKEDITOR.instances['description'].setData(res.content);
            $('#TemplatesModal').modal('show');
        });

    };


    loadTemplate = function (service_id, email, name, pax, address, emailto, phone, description, status, time_from,time_to,supplier_url,price_for_one, menu, tour_id, reference,tour_name,package_id) {

        var selected = '';
        var html = '';

        $('#TemplatesModal').find('#send').prop('disabled', false);
        $('#TemplatesModal').find('#email').val('');
        $('#TemplatesModal').find('#subject').val('');
        $('#TemplatesModal').find('#file').val('');
        $('#TemplatesModal').find('#file_name').text('');
        CKEDITOR.instances['templatesContent'].setData('');

        $.ajax({
            url: '/templates/api/loadServiceTemplates',
            method: 'GET',

            data: {
                id: service_id,
            },
            success: function (res) {

                for (var i = 0; i < res.templates.length; i++) {

                    (i == 0) ? selected = "selected" : "";

                    if (res.templates[i]['name'] != 'Footer' && res.templates[i]['name'] != 'Header') {
						if(res.templates[i]['name'] != 'Offer_confirm_template'){
                        html += "<option value='" + res.templates[i]['id'] + "' " + selected + ">" + res.templates[i]['name'] + "</option>";
						}
                    }
                }

                $('#TemplatesModal').find('#template_selector').html(html);

                $('#TemplatesModal').find('#template_selector').on('change', function () {
                    loadTemplateById(service_id, email, name, pax, address, emailto, phone, description, status, time_from,time_to,supplier_url, price_for_one, menu, tour_id, reference,tour_name,package_id);
                });

                loadTemplateById(service_id, email, name, pax, address, emailto, phone, description, status, time_from,time_to,supplier_url, price_for_one, menu, tour_id, reference,tour_name,package_id);
            }

        })

    };


    function loadGuestTemplate(service_id, email, name, pax, address, emailto, phone, description, status, time_from, price_for_one, menu, tour_id) {
        var selected = '';
        var html = '';

//        $('#TemplatesModal').find('#send').prop('disabled', false);
//        $('#TemplatesModal').find('#email').val('');
//        $('#TemplatesModal').find('#subject').val('');
//        $('#TemplatesModal').find('#file').val('');
//        $('#TemplatesModal').find('#file_name').text('');
//        CKEDITOR.instances['templatesContent'].setData('');

        $.ajax({
            url: '/templates/api/loadServiceTemplates',
            method: 'GET',

            data: {
                id: 0,
            },
            success: function (res) {

                html += "<option value='' selected disabled hidden>Choose here</option>";
                for (var i = 0; i < res.templates.length; i++) {

                    //(i == 0) ? selected = "selected" : "";

                    if (res.templates[i]['name'] != 'Footer' && res.templates[i]['name'] != 'Header') {
                        html += "<option value='" + res.templates[i]['id'] + "' " + selected + ">" + res.templates[i]['name'] + "</option>";
                    }
                }

                $('#template_selector_guest').html(html);

                $('#template_selector_guest').on('change', function () {
                    var id = $('#template_selector_guest').val();

                    $.ajax({
                        url: '/templates/api/load',
                        method: 'GET',
                        data: {
                            service_id: service_id,
                            id: id,
                            email: email,
                            name: name,
                            pax: pax,
                            address: address,
                            emailto: emailto,
                            phone: phone,
                            description: description,
                            status: status,
                            time_from: time_from,
                            price_for_one: price_for_one,
                            menu: menu,
                            tour_id: tour_id
                        }
                    }).done((res) => {
                        CKEDITOR.instances['roomlist_textarea'].setData(res.content);
                    });
                });
            }
        })
    }
	$('#emails_select').on('change',function(){
		$('#email').val($(this).val());
	})

	function toggleReadMore(element,id) {
   $('#service-description'+id).css('max-height','none');
	$('#readmore'+id).css('display','none');
    
}
	function loadDescriptionemplate(){
		var selected = '';
		 html = "<option value='0' selected>Default</option>";
		$.ajax({
            url: '/templates/api/loadServiceTemplates',
            method: 'GET',

            data: {
                id: 6,
            },
            success: function(res) {

                for (var i = 0; i < res.templates.length; i++) {

                    (i == 0) ? selected = "selected": "";

                    if (res.templates[i]['name'] != 'Footer' && res.templates[i]['name'] != 'Header') {
						
                        html += "<option value='" + res.templates[i]['id'] + "' >" + res
                            .templates[i]['name'] + "</option>";
                    }
                }

                $('#service-description').find('#desc_template_selector').html(html);

                $('#service-description').find('#desc_template_selector').on('change', function() {
					if($(this).val() === '0'){
						CKEDITOR.instances['description'].setData("");
					}else{
                    guestTemplate($(this).val());
					}
                });

            }

        });
		
		 
	}
	
	function guestTemplate(service_id){
		
		$.ajax({
            url: '/desctemplates/api/load',
            method: 'GET',
            data: {
                id: service_id,
            }
        }).done((res) => {
          CKEDITOR.instances['description'].setData(res.content);
        });
	}
</script>
<?php /**PATH /var/www/html/resources/views/tour/packages.blade.php ENDPATH**/ ?>