<?php $__env->startSection('title', 'Quotation'); ?>
<?php $__env->startSection('content'); ?>

    <section class="content">
        <div class="box box-primary">
            <div class="box box-body" id="quotation_body" style="border-top: none">
                <div style="margin-bottom: 10px;">
                    <a href="javascript:history.back()">
                        <button class='btn btn-primary'><?php echo e(trans('main.Back')); ?></button>
                    </a>
                    <button type="button" class="btn btn-success saved"><?php echo e(trans('main.Save')); ?></button>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <input class="form-control" type="text" placeholder="Name" id="quotation_name">
                    </div>
                    <div class="col-md-2 text-red hide validate-name">
                        <span style="line-height: 30px;"><?php echo e(trans('main.Nameisrequiredfield')); ?></span>
                    </div>
                    <div class="col-md-1 pull-right">
                        <a href="#" class="namesToggle hideTitle"><?php echo e(trans('main.Showtitles')); ?></a>
                    </div>
                </div>

                <script>
                    let tourId = <?php echo e($tour->id); ?>;
                    $(document).on('blur', 'input', function () {
//                        $(this).hide();
                        let data_row = $(this).attr('data_row');
                        let data_column = $(this).attr('data_column');

                    });
                </script>
                <?php echo e(csrf_field()); ?>

                <br>
                <div class="row">
                    <div class="col-md-12">
                        <div id="quotation_table" style="overflow-x: scroll;">
                            <table class="table table-bordered">
                                <thead>
                                    <th><?php echo e(trans('main.Date')); ?></th>
                                    <th><?php echo e(trans('main.City')); ?></th>
                                    <th><?php echo e(trans('main.Hotel')); ?></th>
									<th
                                                        data-container="body" data-toggle="tooltip" data-placement="bottom"
                                                        data-original-title="Single Suppl."
                                                >
                                                    SS
                                                </th>
									<th data-column="Hotel P.P" data-container="body" data-toggle="tooltip" data-placement="bottom"
                                                        data-original-title="Hotel P.P"
                                                >
                                                    HPP
                                                </th>
                                    <th data-column="lunchName"
                                        data-container="body" data-toggle="tooltip" data-placement="bottom"
                                        data-original-title="Lunch Name"
                                    >
                                        L.Name</th>
                                    <th
                                            data-container="body" data-toggle="tooltip" data-placement="bottom"
                                            data-original-title="Lunch"
                                    >Lun</th>
                                    <th data-column="dinnerName"
                                        data-container="body" data-toggle="tooltip" data-placement="bottom"
                                        data-original-title="Dinner Name"
                                    >D.Name</th>
                                    <th
                                            data-container="body" data-toggle="tooltip" data-placement="bottom"
                                            data-original-title="Dinner"
                                    >Din</th>
                                    <th>Entr</th>
                                    <th
                                            data-container="body" data-toggle="tooltip" data-placement="bottom"
                                            data-original-title="Comments"
                                    >Com</th>
                                    <th
                                            data-container="body" data-toggle="tooltip" data-placement="bottom"
                                            data-original-title="Local G\D"
                                    >LGD</th>
                                    <th>BUS</th>
                                    <th
                                            data-container="body" data-toggle="tooltip" data-placement="bottom"
                                            data-original-title="Group Cost"
                                    >GC</th>
                                    <th
                                            data-container="body" data-toggle="tooltip" data-placement="bottom"
                                            data-original-title="Driver">Dri</th>
                                    <th
                                            data-container="body" data-toggle="tooltip" data-placement="bottom"
                                            data-original-title="Porterage"
                                    >Por</th>

                                </thead>
								<?php if(empty($quotation)): ?>
                                <tbody id="quotation_table">
                                <?php
                                    $sortedTourDays = $tour->tour_days->sortBy(function($tourDay){
                                      return $tourDay->date;
                                    });
                                ?>
                                <?php $__currentLoopData = $sortedTourDays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row => $tour_day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="quotation-row" data-row="<?php echo e($row); ?>">
                                        <td data-column="date">
                                            <?php echo e($tour_day->date); ?>

                                        </td>
                                        <td data-column="cityName">
                                            <?php if($tour_day->firstHotel() && $tour_day->firstHotel()->service()->cityObject): ?>
                                                <?php echo e($tour_day->firstHotel()->service()->cityObject->name); ?>

                                            <?php endif; ?>
                                        </td>
                                        <td
                                                data-column="hotelName"
                                                data-value="<?php if($tour_day->firstHotel()): ?>
                                                <?php echo e($tour_day->firstHotel()->name); ?>

                                                <?php endif; ?>"
                                        >
                                            <?php if($tour_day->firstHotel()): ?>
                                                <?php echo e($tour_day->firstHotel()->name); ?>

                                            <?php endif; ?>
											<input type="text">
                                        </td>
                                        <?php $__currentLoopData = $listRoomsHotel; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<?php if($room->room_types->code == 'SIN'): ?>
                                            <td
                                                    class="hotelRooms"
                                                    data-column="<?php echo e($room->room_types->code); ?>"
                                                    data-value="<?php if($tour_day->firstHotel()): ?>
                                                    <?php echo e($tour_day->firstHotel()->getRoomTypePrice($room->room_type_id, true)); ?>

                                                    <?php endif; ?>"
                                            >
												
                                                <?php if($tour_day->firstHotel()): ?>
                                                    <?php if($room->room_types->code == 'SIN'): ?>
                                                        <?php echo e($tour_day->firstHotel()->getSinglePrice()); ?>

                                                    <?php else: ?>
                                                    <?php echo e($tour_day->firstHotel()->getRoomTypePrice($room->room_type_id, true)); ?>

                                                    <?php endif; ?>
                                                <?php endif; ?>
												<input type="text">
                                            </td>
										<?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
										<td data-column="htlpp">

											<input type="text">
                                        </td>
                                        <td data-column="lunchName">
                                            <?php if($tour_day->firstRestaurant()): ?>
                                                <?php echo e($tour_day->firstRestaurant()->name); ?>

                                            <?php endif; ?>
											<input type="text">
                                        </td>
                                        <td data-column="lunch">
                                            <?php if($tour_day->firstRestaurant()): ?>
                                                <?php echo e($tour_day->firstRestaurant()->total_amount); ?>

                                            <?php endif; ?>
											<input type="text">
                                        </td>
                                        <td data-column="dinnerName">
                                            <?php if($tour_day->secondRestaurant()): ?>
                                                <?php echo e($tour_day->secondRestaurant()->name); ?>

                                            <?php endif; ?>
											<input type="text">
                                        </td>
                                        <td data-column="dinner">
                                            <?php if($tour_day->secondRestaurant()): ?>
                                                <?php echo e($tour_day->secondRestaurant()->total_amount); ?>

                                            <?php endif; ?>
											<input type="text">
                                        </td>
                                        <td data-column="entrance">
                                            <!-- Entrance -->
											<input type="text">
                                        </td>
                                        <td data-column="comments">
                                            <!-- Comments -->
											<input type="text">
                                        </td>
                                        <td data-column="local_g_d">
                                            <!-- LocalG/d -->
											<input type="text">
                                        </td>
                                        <td data-column="bus">
                                            <!-- BUS -->
											<input type="text">
                                        </td>
                                        <td data-column="group_cost">
                                            <!-- Group Cost -->
											<input type="text">
                                        </td>
                                        <td data-column="driver">
                                            <!-- Driver -->
											<input type="text">
                                        </td>
                                        <td data-column="porterage">
                                            <!-- Porterage -->
											<input type="text">
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
								<?php else: ?>
								<tbody id="quotation_table">
                                    <?php
										$sortedTourDays = $tour->tour_days->sortBy(function($tourDay){
										  return $tourDay->date;
										});
                                        $sortedQuotationRows = $quotation->rows->sortBy(function($quotationRow){
                                          return $quotationRow->getValueByKey('date')->value??"";
                                        });
                                        $data_row = -1;
										$total_days = count($sortedTourDays);
										$rows_count = count($sortedQuotationRows);
										$counter = 0;
                                    ?>
									<?php $columns = array("date","cityName","hotelName","SIN","htlpp","lunchName","lunch","dinnerName","dinner","entrance","comments","local_g_d","bus","group_cost","driver","porterage"); 
												
												
												$insertIndex = array_search("hotelName", $columns);
												// Reverse the $listRoomsHotel collection
												/*
												$listRoomsHotel = $listRoomsHotel->reverse();

												foreach ($listRoomsHotel as $room) {
													array_splice($columns, $insertIndex + 1, 0, $room->room_types->code);
												}
												$listRoomsHotel = $listRoomsHotel->reverse();
												*/
												?>
                                    <?php $__currentLoopData = $sortedQuotationRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                        $data_row = $data_row + 1;
										 if( $total_days == $data_row){
                                            break;
                                        }
                                        ?>
                                        <tr class="quotation-row" data-row= <?php echo e($data_row); ?> >
                                            
                                            <?php $__currentLoopData = $columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
														<?php
															$found = false; // Initialize a flag to check if the column is found
														?>

														<?php $__currentLoopData = $row->values; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
															<?php if($value->key === $column): ?>
																<?php
																	$found = true; // Set the flag to true when a matching column is found
																?>

																<?php if(!empty($value->value) && in_array($value->key, $columns)): ?>
																	<td data-column="<?php echo e($value->key); ?>">
																		<?php echo e($value->value); ?>

																	</td>
																	<?php elseif(in_array($value->key, $columns)): ?>
																	<td data-column="<?php echo e($value->key); ?>">
																		<input type="text" value="<?php echo e($value->value); ?>">
																	</td>
																	<?php endif; ?>
																<?php break; ?> // Exit the inner loop as we've found the column
															<?php endif; ?>
														<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

														
														<?php if(!$found): ?>
															<td data-column="<?php echo e($column); ?>">
																
															</td>
														<?php endif; ?>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

												   <?php $__currentLoopData = $quotation->additional_columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <td
                                                                        data-column="<?php echo e($column->name); ?>"
                                                                        class="additional-cell
                                                                        <?php if($column->type == 'all'): ?>
                                                                                quotation-cell-general
                                                                        <?php endif; ?>
                                                                        <?php if($column->type == 'person'): ?>
                                                                                quotation-cell-per-person
                                                                        <?php endif; ?>
                                                                        ">
                                                                    
                                                                    <?php echo e(@$quotation->getAdditionalColumnValueCell($row->id, $column->name)->value); ?>

                                                                </td>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									<?php $__currentLoopData = $sortedTourDays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row => $tour_day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                        $counter++;
                                        ?>
                                    <?php if($counter > $rows_count): ?>
                                            <tr class="quotation-row" data-row="<?php echo e($row); ?>">
                                                <td data-column="date">
                                                    <?php echo e($tour_day->date); ?>

                                                </td>
                                                <td data-column="cityName">
                                                    <?php if($tour_day->firstHotel() && $tour_day->firstHotel()->service()->cityObject): ?>
                                                        <?php echo e($tour_day->firstHotel()->service()->cityObject->name); ?>

                                                    <?php endif; ?>
                                                </td>
                                                <td
                                                        data-column="hotelName"
                                                        data-value="<?php if($tour_day->firstHotel()): ?>
                                                        <?php echo e($tour_day->firstHotel()->name); ?>

                                                        <?php endif; ?>"
                                                >
                                                    <?php if($tour_day->firstHotel()): ?>
                                                        <?php echo e($tour_day->firstHotel()->name); ?>

                                                    <?php endif; ?>
                                                </td>
                                                <?php $__currentLoopData = $listRoomsHotel; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                                    <td
                                                            class="hotelRooms"
                                                            data-column="<?php echo e($room->room_types->code); ?>"
                                                            data-value="<?php if($tour_day->firstHotel()): ?>
                                                            <?php echo e($tour_day->firstHotel()->getRoomTypePrice($room->room_type_id, true)); ?>

                                                            <?php endif; ?>"
                                                    >
                                                        <?php if($tour_day->firstHotel()): ?>
                                                            <?php if($room->room_types->code == 'SIN'): ?>
                                                                <?php echo e($tour_day->firstHotel()->getSinglePrice()); ?>

                                                            <?php else: ?>
                                                            <?php echo e($tour_day->firstHotel()->getRoomTypePrice($room->room_type_id, true)); ?>

                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                    <input type="text">
                                                    </td>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <td data-column="lunchName">
                                                    <?php if($tour_day->firstRestaurant()): ?>
                                                        <?php echo e($tour_day->firstRestaurant()->name); ?>

                                                    <?php endif; ?>
                                                    <input type="text">
                                                </td>
                                                <td data-column="lunch">
                                                    <?php if($tour_day->firstRestaurant()): ?>
                                                        <?php echo e($tour_day->firstRestaurant()->total_amount); ?>

                                                    <?php endif; ?>
                                                    <input type="text">
                                                </td>
                                                <td data-column="dinnerName">
                                                    <?php if($tour_day->secondRestaurant()): ?>
                                                        <?php echo e($tour_day->secondRestaurant()->name); ?>

                                                    <?php endif; ?>
                                                    <input type="text">
                                                </td>
                                                <td data-column="dinner">
                                                    <?php if($tour_day->secondRestaurant()): ?>
                                                        <?php echo e($tour_day->secondRestaurant()->total_amount); ?>

                                                    <?php endif; ?>
                                                    <input type="text">
                                                </td>
                                                <td data-column="entrance">
                                                    <!-- Entrance -->
                                                </td>
                                                <td data-column="comments">
                                                    <!-- Comments -->
                                                </td>
                                                <td data-column="local_g_d">
                                                    <!-- LocalG/d -->
                                                </td>
                                                <td data-column="bus">
                                                    <!-- BUS -->
                                                </td>
                                                <td data-column="group_cost">
                                                    <!-- Group Cost -->
                                                </td>
                                                <td data-column="driver">
                                                    <!-- Driver -->
                                                </td>
                                                <td data-column="porterage">
                                                    <!-- Porterage -->
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="button" class="btn btn-success saved" ><?php echo e(trans('main.Save')); ?></button>
                </div>
            </div>
        </div>
    </section>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('post_scripts'); ?>
    <script>var calculationArray = {};</script>
    <script type="text/javascript" src='<?php echo e(asset('js/quotation.js')); ?>'></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('scaffold-interface.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/quotation/create.blade.php ENDPATH**/ ?>