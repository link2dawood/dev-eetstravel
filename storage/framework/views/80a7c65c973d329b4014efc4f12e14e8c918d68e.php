<?php $__env->startSection('title','Show'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('layouts.title',
   ['title' => 'Hotel', 'sub_title' => 'Hotel Show',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Hotels', 'icon' => 'hotel', 'route' => route('hotel.index')],
   ['title' => 'Show', 'route' => null]]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div style="margin-bottom: 10px;">
                    <a href="javascript:history.back()">
                        <button class='btn btn-primary'><?php echo trans('main.Back'); ?></button>
                    </a>
                    <a href="<?php echo route('hotel.edit', $hotel->id); ?>">
                        <button class='btn btn-warning'><?php echo trans('main.Edit'); ?></button>
                        <?php if(isset($tab)): ?> <?php echo e($tab); ?> <?php endif; ?>
                    </a>
                </div>
                <div id="fixed-scroll" class="nav-tabs-custom">
                    <ul class="nav nav-tabs" id="fixed-scroll" role='tablist'>
                        <li role='presentation' class="active"><a href="#info-tab" aria-controls='info-tab' role='tab' data-toggle='tab'><?php echo trans('main.Info'); ?></a></li>
                        <li role='presentation'><a href="#contacts-tab" aria-controls='contacts-tab' role='tab' data-toggle='tab'><?php echo trans('main.Contacts'); ?></a></li>
                        <li role='presentation'><a href="#history-tab" aria-controls='history-tab' role='tab' data-toggle='tab'><?php echo trans('main.History'); ?></a></li>
                        <li role='presentation'><a href="#agreement-tab" aria-controls='agreement-tab' role='tab' data-toggle='tab' id="agreement_tab" ><?php echo trans('main.Agreements'); ?></a></li>

                        <li role='presentation'><a href="#kontingent-tab" aria-controls='kontingent-tab' role='tab' data-toggle='tab' id="kontingent_tab" ><?php echo trans('main.Allotment'); ?></a></li>
                        <li role='presentation'><a href="#menu-tab" aria-controls='menu-tab' role='tab' data-toggle='tab'><?php echo trans('main.Menu'); ?></a></li>
                        <li role='presentation'><a href="#season-tab" aria-controls='season-tab' role='tab' data-toggle='tab' id="season_tab"><?php echo trans('main.Seasonprice'); ?></a></li>
						<li role='presentation' class="tab" data-tab="invoices-tab"><a href="#invoices-tab" aria-controls='invoices-tab' role='tab'
                                data-toggle='tab' id="invoices_tab" ><?php echo trans('Invoices'); ?></a></li>
                    </ul>
                </div>
                <div class="tab-content">
                    <div class="tab-pane fade in active" role='tabpanel' id='info-tab'>

                        <div>
                            <table class='table table-bordered' style="width:50%; float:left">
                                <tbody>
                                <tr>
                                    <td>
                                        <b><i><?php echo trans('main.Name'); ?> : </i></b>
                                    </td>
                                    <td class="info_td_show"><?php echo $hotel->name; ?></td>
                                </tr>
                                <tr>
                                    <td>
                                        <b><i><?php echo trans('main.AddressFirst'); ?> : </i></b>
                                    </td>
                                    <td class="info_td_show"><?php echo $hotel->address_first; ?></td>
                                </tr>
                                <tr>
                                    <td>
                                        <b><i><?php echo trans('main.AddressSecond'); ?> : </i></b>
                                    </td>
                                    <td class="info_td_show"><?php echo $hotel->address_second; ?></td>
                                </tr>
                                <tr>
                                    <td>
                                        <b><i><?php echo trans('main.Code'); ?> : </i></b>
                                    </td>
                                    <td class="info_td_show"><?php echo $hotel->code; ?></td>
                                </tr>
                                <tr>
                                    <td>
                                        <b><i><?php echo trans('main.Country'); ?> : </i></b>
                                    </td>
                                    <td class="info_td_show"><?php echo \App\Helper\CitiesHelper::getCountryById($hotel->country)['name']??""; ?></td>
                                </tr>
                                <tr>
                                    <td>
                                        <b><i><?php echo trans('main.City'); ?> : </i></b>
                                    </td>
									<?php if(!empty($hotel->city)): ?>
                                    <td class="info_td_show"><?php echo \App\Helper\CitiesHelper::getCityById($hotel->city)['name']??""; ?></td>
									<?php else: ?>
									<td class="info_td_show"></td>
									<?php endif; ?>
                                </tr>
                                <tr>
                                    <td>
                                        <b><i><?php echo trans('main.WorkPhone'); ?> : </i></b>
                                    </td>
                                    <td class="info_td_show"><?php echo $hotel->work_phone??""; ?></td>
                                </tr>

                                <tr>
                                    <td>
                                        <b><i><?php echo trans('main.WorkFax'); ?> : </i></b>
                                    </td>
                                    <td class="info_td_show"><?php echo $hotel->work_fax??""; ?></td>
                                </tr>
                                <tr>
                                    <td>
                                        <b><i><?php echo trans('main.WorkEmail'); ?> : </i></b>
                                    </td>
                                    <td class="info_td_show"><?php echo $hotel->work_email; ?></td>
                                </tr>
                                </tbody>
                            </table>
                            <table class='table table-bordered' style="width:50%; float:left">
                                <tbody>
                                <tr>
                                    <td>
                                        <b><i><?php echo trans('main.ContactName'); ?> : </i></b>
                                    </td>
                                    <td class="info_td_show"><?php echo $hotel->contact_name; ?></td>
                                </tr>
                                <tr>
                                    <td>
                                        <b><i><?php echo trans('main.ContactPhone'); ?> : </i></b>
                                    </td>
                                    <td class="info_td_show"><?php echo $hotel->contact_phone; ?></td>
                                </tr>
                                <tr>
                                    <td>
                                        <b><i><?php echo trans('main.ContactEmail'); ?> : </i></b>
                                    </td>
                                    <td class="info_td_show"><?php echo $hotel->contact_email; ?></td>
                                </tr>
                                <tr>
                                    <td>
                                        <b><i><?php echo trans('main.Comments'); ?> : </i></b>
                                    </td>
                                    <td class="info_td_show"><?php echo $hotel->comments; ?></td>
                                </tr>
                                <tr>
                                    <td>
                                        <b><i><?php echo trans('main.IntComments'); ?> : </i></b>
                                    </td>
                                    <td class="info_td_show"><?php echo $hotel->int_comments; ?></td>
                                </tr>
                                <tr>
                                    <td>
                                        <b><i><?php echo trans('main.Criterias'); ?> : </i></b>
                                    </td>
<?php                                    
    $empty = 0;
?>
                                    <?php $__empty_1 = true; $__currentLoopData = $criterias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $criteria): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <?php $__empty_2 = true; $__currentLoopData = $hotel->criterias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                            <?php if($criteria->id == $item->criteria_id): ?>
                                                <td class="info_td_show criteria_block" style="width:100%"><?php echo $criteria->name; ?></td>
<?php                                    
    $empty = 1;
?>
                                                

                                            <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>

                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

                                    <?php endif; ?>
                                    <?php if($empty == 0): ?>
                                        <td class="info_td_show"></td>
                                    <?php endif; ?>
                                </tr>
                                <tr>
                                    <td>
                                        <b><i><?php echo trans('main.Rate'); ?> : </i></b>
                                    </td>
                                    <td class="info_td_show"><?php echo $hotel->rate_name; ?></td>
                                </tr>
                                <tr>
                                    <td>
                                        <b><i><?php echo trans('main.Website'); ?> : </i></b>
                                    </td>
                                    <td class="info_td_show"><?php echo $hotel->website; ?></td>
                                </tr>
                                <tr>
                                    <td>
                                        <b><i><?php echo trans('main.CityTax'); ?> : </i></b>
                                    </td>
                                    <td class="info_td_show"><?php echo $hotel->city_tax; ?></td>
                                </tr>

                                <tr>
                                    <td>
                                        <b><i><?php echo trans('main.Note'); ?> : </i></b>
                                    </td>
                                    <td class="info_td_show"><?php echo $hotel->note; ?></td>
                                </tr>

                                </tbody>
                            </table><!--
                            <table class="table table-bordered">
                            <tbody>
                            <tr>
                                <td>
                                    <b><i>Prices Room Type : </i></b>
                                </td>
                                <?php $__currentLoopData = $hotel->prices_room_type; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                            <td style="display: block"><?php echo $item->room_types->code . ' - ' . $item->price; ?></td>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tr>
                                </tbody>
                            </table>-->
                        </div>
                        <div class="clearfix"></div>
                        <?php $__env->startComponent('component.files', ['files' => $files]); ?><?php echo $__env->renderComponent(); ?>
                        <span id="showPreviewBlock" data-info="<?php echo e(true); ?>"></span>
                        <div class="box box-success" style="position: relative; left: 0px; top: 0px;">
                            <div class="box-header ui-sortable-handle" style="cursor: move;">
                                <i class="fa fa-comments-o"></i>

                                <h3 class="box-title"><?php echo trans('main.Comments'); ?></h3>
                            </div>
                            <div class="box-body">
                                <div class="slimScrollDiv" style="position: relative; overflow-y: scroll;  width: auto;">
                                    <div class="box-body box chat" id="chat-box" style="width: auto; height: auto;">
                                        <div id="show_comments"></div>
                                    </div>
                                    <div class="slimScrollRail" style="width: 7px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; background: rgb(51, 51, 51) none repeat scroll 0% 0%; opacity: 0.2; z-index: 90; right: 1px;"></div>
                                </div>
                            </div>
                            <!-- /.chat -->
                            <div class="box-footer">
                                <form method='POST' action='<?php echo e(route('comment.store')); ?>' enctype="multipart/form-data" id="form_comment">
                                    <div class="input-group" style="width: 100%">
                                        <span id="author_name" class="input-group-addon">
                                            <span id="name"></span>
                                            <a href="#" id="reply_close"><i class="fa fa-close"></i></a>
                                        </span>
                                        <textarea class="form-control" id="content" name="content" placeholder="Ctrl + Enter to post comment"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label><?php echo trans('main.Files'); ?></label>
                                        <?php $__env->startComponent('component.file_upload_field'); ?><?php echo $__env->renderComponent(); ?>
                                    </div>
                                    <input type="text" id="parent_comment" hidden name="parent" value="<?php echo e(null); ?>">
                                    <input type="text" id="default_reference_id" hidden name="reference_id" value="<?php echo e($hotel->id); ?>">
                                    <input type="text" id="default_reference_type" hidden name="reference_type" value="<?php echo e(\App\Comment::$services['hotel']); ?>">

                                    <button type="submit" class="btn btn-success pull-right" id="btn_send_comment" style="margin-top: 5px;"><?php echo trans('main.Send'); ?></button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" role='tabpanel' id='contacts-tab'>
                        <div>
                            <?php if($contacts->count()): ?>
                            <table class='table table-bordered' style="width:50%; float:left">
                                <tbody>
                                    <tr>
                                        <td><b><?php echo trans('main.FullName'); ?></b></td>
                                        <td><b><?php echo trans('main.MobilePhone'); ?></b></td>
                                        <td><b><?php echo trans('main.WorkPhone'); ?></b></td>
                                        <td><b><?php echo trans('main.Email'); ?></b></td>
                                    </tr>
                                    <?php $__currentLoopData = $contacts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($contact->full_name); ?></td>
                                            <td><?php echo $contact->mobile_phone; ?></td>
                                            <td><?php echo $contact->work_phone; ?></td>
                                            <td><?php echo $contact->email; ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                            <?php else: ?>
                                <h2><?php echo trans('main.Hoteldonthavecontacts'); ?>!</h2>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="tab-pane fade" role='tabpanel' id='history-tab'>
                        <div id='history-container'></div>
                    </div>

                    <div class="tab-pane fade" role='tabpanel' id='agreement-tab'>
                        <br>
                        <div class="box box-success">
                            <div class="box-header ui-sortable-handle" style="cursor: move;">
                                <i class="fa fa-handshake-o"></i>
                                <h3 class="box-title"><?php echo trans('main.Agreements'); ?></h3>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <?php if(Auth::user()->can('create_agreements')): ?>
                                        <a href="<?php echo e(route('create_agreements', ['id' => $hotel->id])); ?>">
                                            <button type="button" class="btn btn-block btn-success btn-flat"><?php echo trans('main.AddAgreement'); ?></button>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <br>
                                <table class="table table-bordered finder-disable ">
                                    <thead>
                                    <tr role="row">
                                        <th><?php echo trans('main.StartDate'); ?></th>
                                        <th><?php echo trans('main.EndDate'); ?></th>
                                        <th><?php echo trans('main.Hotel'); ?></th>
                                        <th><?php echo trans('main.Name'); ?></th>
                                        <th><?php echo trans('main.Rooms'); ?></th>
                                        <th><?php echo trans('main.Description'); ?></th>
                                        <th><?php echo trans('main.Actions'); ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $__currentLoopData = $hotel->agreements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agreement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                        <tr>
                                            <td>
                                                <?php echo e(Carbon\Carbon::parse($agreement->start_date)->format('d-m-Y')); ?>

                                            </td>
                                            <td>
                                                <?php echo e(Carbon\Carbon::parse($agreement->end_date)->format('d-m-Y')); ?>

                                            </td>
                                            <td>
                                                <?php echo e($hotel->name); ?>

                                            </td>
                                            <td>
                                                <?php echo e($agreement->name); ?>


                                            </td>
                                            <td>
                                                <?php $__currentLoopData = $agreement->agreements_room_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <p> <?php echo e($item->count); ?> - <?php echo e($agreement->getRoom($item->room_type_id)->name); ?> </p>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </td>
                                            <td style="width:50em;">
                                                <div id="desc_agreement" > <?php echo e($agreement->description); ?> </div>
                                            </td>
                                            <td align="center" style="min-width: 100px;" >
                                                <?php if(Auth::user()->can('edit_agreements')): ?>
                                                <a href="<?php echo e(route('edit_agreements', ['id' => $agreement->id,'hotel_id' => $hotel->id])); ?>" type="button" class="btn btn-primary btn-sm" ><i class="fa fa-pencil-square-o"></i></a>
                                                <?php endif; ?>
                                                <?php if(Auth::user()->can('delete_agreements')): ?>
                                                    <a href="#" onclick="checkAgreement( <?php echo e($agreement->id); ?>,<?php echo e($hotel->id); ?> );" type="button" class="btn btn-danger btn-sm" ><i class="fa fa-trash-o"></i></a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                    
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" role='tabpanel' id='kontingent-tab'>
                        <br>
                        <div class="container">

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">

                                        <label for="">&nbsp;&nbsp;<?php echo trans('main.StartMonth'); ?></label>
                                        <div class="input-group date margin">

                                            <div class="input-group-addon date_calendar" >
                                                <i class="fa fa-calendar" ></i>
                                            </div>
                                            <input type="text" value="" name="start_date" id="start_date" class="form-control pull-right datepicker"  >
                                            <span class="input-group-btn">
                                                 <button type="button" class="btn btn-primary btn-flat" id="showMonth"><?php echo trans('main.Show'); ?></button>
                                            </span>
                                        </div>

                                    </div>
                                </div>

                                <div class="col-sm-6" style="margin-top: 34px;">

                                      <span id="help" class="btn btn-box-tool" style="margin-left: 105%;"><i class="fa fa-question-circle" aria-hidden="true" style="font-size: 25px;"></i>
                                          <?php echo $__env->make('legend.kontingent_legend', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                    </span>

                                </div>


                            </div>


                            <div class="row">
                                <div class="col-sm-12">
                                    <table class="calendar" id="calendar1" align="left"
                                           style="table-layout: fixed;height: 100%;border-collapse:collapse;">
                                        <thead id="calendar_head1">
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <table class="calendar" id="calendar2" align="left"
                                           style="table-layout: fixed;height: 100%;border-collapse:collapse;">
                                        <thead id="calendar_head2">
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <table class="calendar" id="calendar3" align="left"
                                           style="table-layout: fixed;height: 100%;border-collapse:collapse;">
                                        <thead id="calendar_head3">
                                        </thead>
                                    </table>
                                </div>
                            </div>


                        </div>
                        <br>
                    </div>

                    <div class="tab-pane fade" role='tabpanel' id='menu-tab'>
                        <br>
                        <div class="box box-success">
                            <div class="box-header ui-sortable-handle" style="cursor: move;">
                                <h3 class="box-title"><?php echo trans('main.Menu'); ?></h3>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <?php if(Auth::user()->can('menu.create')): ?>
                                        <a href="<?php echo e(route('menu.create', ['type' => 'hotel', 'id' => $hotel->id])); ?>">
                                            <button type="button" class="btn btn-block btn-success btn-flat"><?php echo trans('main.AddMenu'); ?></button>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <br>
                                <table class="table table-bordered finder-disable" >
                                    <thead>
                                    <tr role="row">
                                        <th ><?php echo trans('main.Name'); ?></th>
                                        <th><?php echo trans('main.Price'); ?></th>
                                        <th><?php echo trans('main.Description'); ?></th>
                                        <th><?php echo trans('main.Actions'); ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $__currentLoopData = $hotel->menus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><a href="<?php echo e(route('menu.show', ['menu' => $menu->id])); ?>"><?php echo e($menu->name); ?></a></td>
                                            <td><?php echo e($menu->price); ?></td>
                                            <td><?php echo $menu->description; ?></td>
                                            <td style="width: 100px;">
                                                <?php if(Auth::user()->can('menu.edit')): ?>
                                                <a href="<?php echo e(route('menu.edit', ['id' => $menu->id])); ?>" class="btn btn-primary btn-sm edit-button" data-link="<?php echo e(route('menu.edit', ['id' => $menu->id])); ?>"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                                <?php endif; ?>
                                                <?php if(Auth::user()->can('menu.destroy_menu')): ?>
                                                    <a  data-toggle="modal" data-target="#myModal" class="btn btn-danger btn-sm delete" data-link="<?php echo e(route('menu.delete', ['id' => $menu->id], false)); ?>"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                                <?php endif; ?>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" role='tabpanel' id='season-tab'>
                        <br>
                        <div class="box box-success">
                            <div class="box-header ui-sortable-handle" style="cursor: move;">
                                <i class="fa fa-snowflake-o"></i>
                                <h3 class="box-title"><?php echo trans('main.Seasonprice'); ?></h3>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <?php if(Auth::user()->can('create_season')): ?>
                                        <a href="<?php echo e(route('create_season', ['id' => $hotel->id])); ?>">
                                            <button type="button" class="btn btn-block btn-success btn-flat"><?php echo trans('main.AddSeason'); ?></button>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <br>
                                <table class="table table-bordered finder-disable"  >
                                    <thead>
                                    <tr role="row">
                                        <th ><?php echo trans('main.StartDate'); ?></th>
                                        <th><?php echo trans('main.EndDate'); ?></th>
                                        <th><?php echo trans('main.Hotel'); ?></th>
                                        <th><?php echo trans('main.Name'); ?></th>
                                        <th><?php echo trans('main.Type'); ?></th>
                                        <th><?php echo trans('main.Prices'); ?></th>
                                        <th><?php echo trans('main.Description'); ?></th>
                                        <th><?php echo trans('main.Actions'); ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $__currentLoopData = $hotel->seasons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agreement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                        <tr>
                                            <td>
                                                <?php echo e(Carbon\Carbon::parse($agreement->start_date)->format('d.m')); ?>

                                            </td>
                                            <td>
                                                <?php echo e(Carbon\Carbon::parse($agreement->end_date)->format('d.m')); ?>

                                            </td>
                                            <td>
                                                <?php echo e($hotel->name); ?>

                                            </td>
                                            <td>
                                                <?php echo e($agreement->name); ?>

                                            </td>
                                            <td>
                                                <?php echo e(!empty($agreement->getType($agreement->type)->name) ? $agreement->getType($agreement->type)->name : ''); ?>

                                            </td>
                                            <td>
                                                <?php $__currentLoopData = $agreement->seasons_room_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php echo e($agreement->getRoom($item->room_type_id)->name); ?> - <?php echo e($item->price); ?> <br>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </td>
                                            <td style="width:50em;">
                                                <div id="desc_season" > <?php echo e($agreement->description); ?> </div>
                                            </td>
                                            <td align="center" style="min-width: 100px;" >
                                                <?php if(Auth::user()->can('edit_season')): ?>
                                                <a href="<?php echo e(route('edit_season', ['id' => $agreement->id,'hotel_id' => $hotel->id])); ?>" type="button" class="btn btn-primary btn-sm" ><i class="fa fa-pencil-square-o"></i></a>
                                                <?php endif; ?>
                                                <?php if(Auth::user()->can('delete_season')): ?>
                                                    <a href="#" onclick="checkAgreement( <?php echo e($agreement->id); ?>,<?php echo e($hotel->id); ?>,1);" type="button" class="btn btn-danger btn-sm" ><i class="fa fa-trash-o"></i></a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                    
                                </table>
                            </div>
                        </div>

                    </div>
						<div role="tabpanel" class="tab-pane fade in" id="invoices-tab">
					<div id="tour_create" style="margin-bottom : 20px;">
                    <?php echo \App\Helper\PermissionHelper::getCreateButton(route('invoices.create'), \App\Invoices::class); ?>

                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" id="hotelInvoiceSearchInput" class="form-control" placeholder="Search invoices..." onkeyup="filterHotelInvoiceTable()">
                        </div>
                    </div>
                    <div class="col-md-6 text-right">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-primary" onclick="exportHotelInvoicesToCSV()">Export CSV</button>
                            <button type="button" class="btn btn-success" onclick="exportHotelInvoicesToExcel()">Export Excel</button>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="inovices-table" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Invoice No</th>
                                <th>Due Date</th>
                                <th>Received Date</th>
                                <th>Tour</th>
                                <th>Service</th>
                                <th>Office Name</th>
                                <th>Total Price</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(isset($invoices) && $invoices->count() > 0): ?>
                                <?php $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($invoice->id); ?></td>
                                        <td><?php echo e($invoice->invoice_no ?? 'N/A'); ?></td>
                                        <td><?php echo e($invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('Y-m-d') : 'N/A'); ?></td>
                                        <td><?php echo e($invoice->received_date ? \Carbon\Carbon::parse($invoice->received_date)->format('Y-m-d') : 'N/A'); ?></td>
                                        <td><?php echo e($invoice->tour_name ?? 'N/A'); ?></td>
                                        <td><?php echo e($invoice->service_name ?? 'N/A'); ?></td>
                                        <td><?php echo e($invoice->office_name ?? 'N/A'); ?></td>
                                        <td><?php echo e(number_format($invoice->total_amount ?? 0, 2)); ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo e($invoice->status == 'paid' ? 'success' : ($invoice->status == 'pending' ? 'warning' : 'danger')); ?>">
                                                <?php echo e(ucfirst($invoice->status ?? 'pending')); ?>

                                            </span>
                                        </td>
                                        <td>
                                            <?php echo $__env->make('component.action_buttons', [
                                                'routePrefix' => 'invoices',
                                                'item' => $invoice,
                                                'showEdit' => true,
                                                'showDelete' => true,
                                                'showView' => true
                                            ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="10" class="text-center">No invoices found for this hotel</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if(isset($invoices) && method_exists($invoices, 'links')): ?>
                    <div class="row">
                        <div class="col-md-12">
                            <?php echo e($invoices->links()); ?>

                        </div>
                    </div>
                <?php endif; ?>



                </div>
                </div>
            </div>
        </div>
    </section>
    <div id="modalCreateKontingent" class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="modalCreateTourLabel" style="padding-left: 17px;padding-right: 17px;z-index:9999;">
        <div class="modal-dialog modal-lg" style="width: 90%;">
            <div class="modal-content" style="overflow: hidden;">
                <div class="modal-header">
                    <a class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">x</span>
                    </a>
                    <h4 id="modalCreateKontigentLabel" class="modal-title"></h4>
                    <input type="hidden" id="modalCreateKontigentQuota" value="">
                    <input type="hidden" id="modalCreateKontigentValue" value="">
                    <div class="container-fluid; hide" style="margin: 3px;" >
                        <div class="row">
                            <div class="col-xs-1" style="background-color: #f4f4f4;width: 8em;text-align: center;"><?php echo trans('main.Rooms'); ?></div>
                            <div class="col-xs-5" id="hotel_rooms">2 US 3 AS 4 SIN </div>
                            <div class="col-xs-1" style="background-color: #f4f4f4;width: 8em;text-align: center;">Pax</div>
                            <div class="col-xs-5" id="hotel_pax">23</div>
                        </div>
                    </div>
                </div>

                <div class="modal-body" style="overflow: hidden" >

                    <input type='hidden' name='_token' value='<?php echo e(Session::token()); ?>'>
                    <input type='hidden' name='modal_create_tour' value="1">



                    <div class="box box-solid" >
                        <div class="box-header with-border">

                            <div class="box-body" style="overflow-y: scroll; height:250px;">
                                <table class="table table-striped table-bordered table-hover package-service-table" style="background:#fff">
                                    <colgroup>
                                        <col style="width: auto">
                                        <col style="width: auto">
                                        <col style="width: auto;">
                                        <col style="width: auto;">
                                        <col style="width: auto;">
                                        <col style="width: auto;">
                                        <col style="width: auto;">
                                        <col style="width: auto;">
                                        <col style="width: auto;">
                                        <col style="width: auto">
                                        <col style="width: auto;">
                                        <col style="width: 100px;">
                                    </colgroup>
                                    <thead>
                                    <tr>
                                        <th><?php echo trans('main.Tour'); ?></th>
                                        <th><?php echo trans('main.FromTime'); ?></th>
                                        <th style="width: 15%; min-width: 100px;"><?php echo trans('main.Name'); ?></th>
                                        <th style="min-width: 150px"><?php echo trans('main.Status'); ?></th>
                                        <th><?php echo trans('main.Paid'); ?></th>
                                        <th><?php echo trans('main.Rooms'); ?></th>
                                        <th>Pax</th>
                                        <th><?php echo trans('main.Address'); ?></th>
                                        <th><?php echo trans('main.Email'); ?></th>
                                        <th><?php echo trans('main.Phone'); ?></th>
                                        <th><?php echo trans('main.Description'); ?></th>

                                        <!-- <th style="width: 120px">Rooms Hotel</th>-->
                                        <th style="width: 100px; min-width: 100px"><?php echo trans('main.Actions'); ?></th>
                                    </tr>
                                    </thead>
                                    <tbody class="ui-sortable" id="from_div">
                                    </tbody>
                                </table>

                            </div>
                        </div>

                    </div>





                    <div class="box box-solid" >
                        <div class="box-header with-border">

                            <div class="box-body" style="overflow-y: scroll; height:250px;" >
                                <table class="table table-striped table-bordered table-hover package-service-table" style="background:#fff" >
                                    <colgroup>
                                        <col style="width: auto">
                                        <col style="width: auto">
                                        <col style="width: auto;">
                                        <col style="width: auto;">
                                        <col style="width: auto;">
                                        <col style="width: auto;">
                                        <col style="width: auto;">
                                        <col style="width: auto;">
                                        <col style="width: auto;">
                                        <col style="width: auto;">
                                        <col style="width: auto;">
                                        <col style="width: 130px;">
                                    </colgroup>
                                    <thead>
                                    <tr>
                                        <th><?php echo trans('main.Tour'); ?></th>
                                        <th><?php echo trans('main.FromTime'); ?></th>
                                        <th style="width: 15%; min-width: 100px;"><?php echo trans('main.Name'); ?></th>
                                        <th style="min-width: 150px"><?php echo trans('main.Status'); ?></th>
                                        <th><?php echo trans('main.Paid'); ?></th>
                                        <th><?php echo trans('main.Rooms'); ?></th>
                                        <th>Pax</th>
                                        <th><?php echo trans('main.Address'); ?></th>
                                        <th><?php echo trans('main.Email'); ?></th>
                                        <th><?php echo trans('main.Phone'); ?></th>
                                        <th><?php echo trans('main.Description'); ?></th>

                                        <!-- <th style="width: 120px">Rooms Hotel</th>-->
                                        <th style="width: 130px; min-width: 130px"><?php echo trans('main.Actions'); ?></th>
                                    </tr>
                                    </thead>
                                    <tbody class="ui-sortable" id="to_div">
                                    </tbody>
                                </table>

                            </div>
                        </div>

                    </div>



                </div>





                <div class="modal-footer">
                    <a href="close" class='btn btn-default' data-dismiss="modal"><?php echo trans('main.Close'); ?></a>
                    <!--<button class='btn btn-primary' type="button" onclick="saveRemoved();" >Replace</button>-->
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="warnReplace" tabindex="-1" role="dialog" aria-labelledby="myModalLabelDelete" >
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo trans('main.Warning'); ?>!!</h4></div>
                <div class="modal-body">
                    <div class="modal-body"><?php echo trans('main.Themaximumnumberofroomsavailable'); ?></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Ok</button>
                </div></div></div>
    </div>

    <div class="modal fade" id="deleteAgreement" tabindex="-1" role="dialog" aria-labelledby="myModalLabelDelete" >
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo trans('main.Warning'); ?>!!</h4></div>
                <div class="modal-body">
                    <div class="modal-body"><?php echo trans('main.WouldyouliketoremoveThis'); ?>?</div></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo trans('main.Close'); ?></button>
                    <button type="button" class="destroy btn btn-primary" onclick="deleteAgreement();" ><?php echo trans('main.Agree'); ?></button>
                    <input type="hidden" id="id_field" value="" >
                    <input type="hidden" id="hotel_id_field" value="" >
                </div></div></div>
    </div>

    <div class="modal fade" id="replacePackage" tabindex="-1" role="dialog" aria-labelledby="myModalLabelDelete" >
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo trans('main.Warning'); ?>!!</h4></div>
                <div class="modal-body">
                    <div class="modal-body"><?php echo trans('main.WouldyouliketoreplaceThis'); ?>?</div></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo trans('main.Close'); ?></button>
                    <button type="button" class="destroy btn btn-primary" onclick="addFromAgreement();" ><?php echo trans('main.Agree'); ?></button>
                    <input type="hidden" id="id_package" value="0" >
                </div></div></div>
    </div>

    <span id="services_name" data-service-name='Hotel' data-history-route="<?php echo e(route('services_history', ['id' => $hotel->id])); ?>"></span>
    <style>
        .popover .close {
            position: absolute;
            top: 8px;
            right: 10px;
        }
        .popover-title {
            padding-right: 30px;
        }

        .datepicker{z-index:1151 !important;}

    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('post_scripts'); ?>
    <script src="<?php echo e(asset('js/comment.js')); ?>"></script>
    <script src="<?php echo e(asset('js/bootstrap-tables.js')); ?>"></script>
    <script>

        var TodayDate = new Date();
        var removed = [];
        var show = true;
        var current_date = '';

        Date.prototype.getDayOfYear = function () {
            var fd = new Date(this.getFullYear(), 0, 0);
            var sd = new Date(this.getFullYear(), this.getMonth(), this.getDate());
            return Math.ceil((sd - fd) / 86400000);
        };

        Date.prototype.getLastDayOfMonth = function () {
            var y = this.getFullYear();
            var m = this.getMonth();
            return new Date(y, m + 1, 0);
        };

        Date.locale = {
            en: {
                month_names: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                month_names_short: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
            }
        };

        Date.prototype.getMonthName = function (lang) {
            lang = lang && (lang in Date.locale) ? lang : 'en';
            return Date.locale[lang].month_names[this.getMonth()];
        };

        Date.prototype.getMonthNameShort = function (lang) {
            lang = lang && (lang in Date.locale) ? lang : 'en';
            return Date.locale[lang].month_names_short[this.getMonth()];
        };

        Date.isLeapYear = function (year) {
            return (((year % 4 === 0) && (year % 100 !== 0)) || (year % 400 === 0));
        };

        Date.getDaysInMonth = function (year, month) {
            return [31, (Date.isLeapYear(year) ? 29 : 28), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31][month];
        };

        Date.prototype.isLeapYear = function () {
            return Date.isLeapYear(this.getFullYear());
        };

        Date.prototype.getDaysInMonth = function () {
            return Date.getDaysInMonth(this.getFullYear(), this.getMonth());
        };

        Date.prototype.addMonths = function (value) {
            var n = this.getDate();
            this.setDate(1);
            this.setMonth(this.getMonth() + value);
            this.setDate(Math.min(n, this.getDaysInMonth()));
            return this;
        };


        function dateDifference(d1, d2) {
            var fd = new Date(d1.getFullYear(), d1.getMonth(), d1.getDate());
            var sd = new Date(d2.getFullYear(), d2.getMonth(), d2.getDate());
            return Math.ceil((sd - fd) / 86400000);
        }

        function setModalMaxHeight(element) {
            this.$element     = $(element);
            this.$content     = this.$element.find('.modal-content');
            var borderWidth   = this.$content.outerHeight() - this.$content.innerHeight();
            var dialogMargin  = $(window).width() < 768 ? 20 : 60;
            var contentHeight = $(window).height() - (dialogMargin + borderWidth);
            var headerHeight  = this.$element.find('.modal-header').outerHeight() || 0;
            var footerHeight  = this.$element.find('.modal-footer').outerHeight() || 0;
            var maxHeight     = contentHeight - (headerHeight + footerHeight);

            this.$content.css({
                'overflow': 'hidden'
            });

            this.$element
                .find('.modal-body').css({
                'max-height': maxHeight,
                'overflow-y': 'auto'
            });
        }

        $('#modalCreateKontingent').on('show.bs.modal', function() {
            $(this).show();
            setModalMaxHeight(this);
        });

        $('#warnReplace').on('hidden.bs.modal', function () {
            $('#modalCreateKontingent').modal('show');
        });

        $('#modalCreateKontingent').on('hidden.bs.modal', function () {
            restore_reload();
        });


        $(window).resize(function() {
            if ($('.modal.in').length != 0) {
                setModalMaxHeight($('.modal.in'));
            }
        });

        function setMonths() {
            if(!show) return;
            var date = $("#start_date").datepicker('getDate');
            var startDate = new Date(date.getFullYear(), (date.getMonth()), 1);
            show = false;
            $('#calendar_head1').html('');
            $('#calendar_head2').html('');
            $('#calendar_head3').html('');
            $('#line1').html('');
            $('#line2').html('');
            $('#line3').html('');
            genMonths(startDate, 1);
        }

        function genRow(text, startDate, endDate, arr, mode , line, month) {

            var days = dateDifference(startDate, endDate) + 1;
            if (days > 31) days--;
            var backcolor = '';
            var backtext = '';
            var num = '';
            var popover = '';
            var id = '';
            var data = '';

            if (mode) {
                text = '<span style="font-size: 15px;"><b>' + text + '</b></span>';
                backtext = '#dbdee1';
                backcolor = '#dbdee1';
            } else {
                backtext = '#dbdee1';
                backcolor = '#ffffff';
                text = '<span style="font-size: 14px;">' + text + '</span>';
            }

            var numbers2 = '<td style="background-color: ' + backtext + ';border: 1px solid #9ea5a8;text-align: center;min-width: 200px;min-height: 20px;">' + text + '</td>';
            for (var ii = 0; ii < days; ii++) {

                num = '';
                id = '';
                popover = '';
                data = '';




                if (mode) {
                    num = (ii + 1);
                    //id = "day_" + num;
                } else {
                    if (arr[ii]) {
                        num = arr[ii];

                        if (line == '1') {

                            startDate.getMonthName() + " " + startDate.getFullYear();
                            popover = ' data-title="Tours at '+(ii+1)+' '+ startDate.getMonthName()+' '+ startDate.getFullYear()+'" class="my-popover" ';

                            data = ' data-pop="' +(ii + 1)+'"';


                        }

                        if (line == '4') {
                            id = "quota_"+month+"_" +(ii + 1);
                        }


                    }

                }
                numbers2 += '<td id="' + id + '"'+ data +'  style="background-color: ' + backcolor + '; border: 1px solid #9ea5a8;text-align: center;min-width: 30px;min-height: 20px;"' + popover + ' >' + num + '</td>';
            }

            return numbers2;

        }



        function genMonths(startDate, num) {

            var endDate = startDate;
            endDate.setDate(1);
            endDate.setMonth(endDate.getMonth() + 1);
            endDate.setDate(endDate.getDate() - 1);
            var startDate = new Date(endDate.getFullYear(), endDate.getMonth(), 1);
            var days = dateDifference(startDate, endDate) + 1;
            if(days>31) days--;
            ///ajax get by date ;

            $.ajax({
                type: "GET",
                url: '<?php echo e(route('kontingent_agreements')); ?>',
                data: {hotel_id: $('#default_reference_id').val(), days : days, startDate: moment(startDate).format('Y-M-D'), endDate: moment(endDate).format('Y-M-D'), num : num},

                success: function (data) {

                    //set data array
//                    console.log(data);

                    $('#calendar_head' + num).append("<tr><td><br></td></tr>");
                    $('#calendar_head' + num).append("<tr>" + genRow(startDate.getMonthName() + " " + startDate.getFullYear(), startDate, endDate,data,true,0) + "</tr>");
                    $('#calendar_head' + num).append("<tr>" + genRow('Contractual number of rooms', startDate, endDate,data[0],null,1) + "</tr>");
                    $('#calendar_head' + num).append("<tr>" + genRow('Number of rooms already used', startDate, endDate,data[1],null,2) + "</tr>");
                    $('#calendar_head' + num).append("<tr>" + genRow('Allotment Reserved', startDate, endDate,data[2],null,3) + "</tr>");
                    $('#calendar_head' + num).append("<tr>" + genRow('Available quota', startDate, endDate,data[3],null,4,moment(endDate).format('MM')) + "</tr>");
                    $('#calendar_head' + num).append("<tr>" + genRow('Current booking status %', startDate, endDate,data[4],null,5) + "</tr>");
                    /*
                   if(data[4][1] != '0') {
                        $('#calendar' + num).after(
                            "<div id='line" + num + "'><table class='calendar' align='left' style='table-layout: fixed;height: 100%;border-collapse:collapse'>" +
                            "    <thead>" +
                            "    <tr>" +
                            "        <td style='background-color: #dbdee1;border-left: 1px solid #9ea5a8;border-right: 1px solid #9ea5a8;border-bottom: 1px solid #9ea5a8;text-align: center;min-width: 200px;min-height: 20px;'>" +
                            "            <span style='font-size: 15px;'>" + data[4][0] + "</span></td>" +
                            "        <td width='120' " +
                            "            style='border-bottom: 1px solid #9ea5a8;border-right: 1px solid #9ea5a8;text-align: center;min-height: 135px;'>" +
                            +data[4][1] + "/" + data[4][2] + " " + data[4][3] + "%" + "</td>" +
                            "<tr>" +
                            "</tr>" +
                            "</thead>" +
                            "</table></div>");
                   }*/

                    /*
                    if(data[4]) {
                        for (var ii = 0; ii < data[4].length; ii++) {
                            $('#calendar_head' + num).find('#day_' + data[4][ii]).css({
                                'background-color': '#3c8cbb',
                                'color': '#ffffff'
                            });
                        }
                    }*/
                    //next month
                    num++;
                    if (num < 5) {
                        genMonths(startDate.addMonths(1), num);
                    }else{
                        show = true;
                    }

                },
                error: function (data) {
                }
            });
        }


        function kontingentModal(date, id, pop, hotel_name) {

            //  var idpack= $(document).find("#pop_"+pop+"_"+id).find('#package_id').val();
            //$(document).find('#modalCreateKontingent').find('#agreement_from_'+idpack).addClass('hidden');





            var content = $(document).find("#pop_"+pop+"_"+id).html();


            current_date = $(document).find("#pop_"+pop+"_"+id).data('dates');

            //    content = content.replace(/<url/g,"<a");
            content = content.replace(/div/g,"tr");
            content = content.replace(/span/g,"td");
            //    content = content.replace(/<font>[\s\S]*?<\/font>/g, hotel_name );

            $('#modalCreateKontingent').find('#modalCreateKontigentLabel').html(date);
            $('#modalCreateKontingent').find('#from_div').html(content);

            $('#modalCreateKontingent').find('#modalCreateKontigentQuota').val($(document).find("#quota_"+pop+"_"+id).text());
            $('#modalCreateKontingent').find('#modalCreateKontigentValue').val($(document).find("#pop_"+pop+"_"+id).closest('a').text().split("\n")[0]);

            $('#modalCreateKontingent').find('#from_div .ui-sortable-handle .add-service-button').parent().parent().removeClass('hidden');
            $('#modalCreateKontingent').find('#from_div .ui-sortable-handle .delete-service-button').parent().parent().removeClass('hidden');
            $('#modalCreateKontingent').find('#from_div .ui-sortable-handle .add-service-button').parent().parent().addClass('hidden');
            // content = content.replace(/<tr/g,"tr class='hidden' ");

            //   content = $(document).find('#from_div').html();
            //  content = content.replace(/span/g,"<td class='hidden' ");
            content = content.replace(/agreement_from_/g,"agreement_to_");
            //   content = content.replace(/addFromAgreement/g,"removeFromAgreement");
            //  content = content.replace(/Add/g,"Remove");

            //   content = content.replace(/<link>[\s\S]*?<\/link>/g, '<a href="" target="_blank">'+33333333333+'</a>' );

            $('#modalCreateKontingent').find('#to_div').html(content);

            $('#modalCreateKontingent').find('#to_div .ui-sortable-handle .add-service-button').parent().parent().removeClass('hidden');
            $('#modalCreateKontingent').find('#to_div .ui-sortable-handle .delete-service-button').parent().parent().removeClass('hidden');
            $('#modalCreateKontingent').find('#to_div .ui-sortable-handle .delete-service-button').parent().parent().addClass('hidden');

            //   $('#modalCreateKontingent').find('#to_div').closest('table').removeClass('hidden');
            //  $('#modalCreateKontingent').find('#from_div').closest('table').removeClass('hidden');
            //  $('#modalCreateKontingent').find('#empty_message').removeClass('hidden');
            setModalMaxHeight('#modalCreateKontingent');

            $('#modalCreateKontingent').modal();

        }

        function checkAgreement(id,hotel_id,season) {
            $('#deleteAgreement').find('#id_field').val(id);
            $('#deleteAgreement').find('#hotel_id_field').val(hotel_id);

            $('#deleteAgreement').off('hidden');

            $('#deleteAgreement').modal();
            if(season){
                $('#deleteAgreement').find('.btn-primary').on('click', function (e) {
                    deleteAgreement(1);
                });
            }
        }

        function deleteAgreement(season) {

            $('#deleteAgreement').off('hidden');

            var id = $('#deleteAgreement').find('#id_field').val(),
                hotel_id = $('#deleteAgreement').find('#hotel_id_field').val();
            if(season){
                url = "/season/" + hotel_id + "/delete/" + id;
            }else {
                url = "/agreements/" + hotel_id + "/delete/" + id;
            }
            $('#deleteAgreement').modal('hide');
            location.href = url;
        }

        function replaceFromPackages(id) {

            $('#modalCreateKontingent').modal('hide');

            $('#replacePackage').on('hidden.bs.modal', function () {
                $('#modalCreateKontingent').modal('show');
            });

            $('#replacePackage').find('#id_package').val(id);

            $('#replacePackage').modal();

        }

        function deleteHotelFromPackages(package_id, hotel_id, date, hotel_name, tour_name) {

            $('#modalCreateKontingent').modal('hide');

            $('#deleteAgreement').on('hidden.bs.modal', function () {
                $('#modalCreateKontingent').modal('show');
            });

            $('#deleteAgreement').find('.btn-primary').prop('onclick',null).off('click');

            $('#deleteAgreement').modal();

            $('#deleteAgreement').find('.btn-primary').on('click', function (e) {

                $('#deleteAgreement').off('hidden');
                $('#deleteAgreement').modal('hide');
                $('#modalCreateKontingent').modal('hide');


                $.ajax({
                    type: "GET",
                    url: '<?php echo e(route('kontingent_delete')); ?>',
                    data: { package_id:  package_id },

                    success: function (data) {

                        $('#deleteAgreement').find('.btn-primary').prop('onclick',null).off('click');
                        $('#deleteAgreement').find('.btn-primary').on('click', function (e) {
                            deleteAgreement();
                        });

                        $('#kontingent_tab').click();

                        restore_reload();

                        setMonths();

                    },

                    error: function (data) {
                        console.log("kontingent delete error2")
                    }
                });
            });

        }

        function restore_reload() {
            removed = [];
        }

        function addFromAgreement() {

            var id = $('#replacePackage').find('#id_package').val(),
                tour = $('#modalCreateKontingent').find('#agreement_from_'+id+ ' #tour_id').val(),
                quota = $('#modalCreateKontingent').find('#modalCreateKontigentQuota').val();
            value = $('#modalCreateKontingent').find('#modalCreateKontigentValue').val();

            if(quota.length == 0) quota = value;

            removed.push([id, parseInt(tour),parseInt(quota)]);

            $('#replacePackage').off('hidden');
            $('#replacePackage').modal('hide');

            $.ajax({
                type: "GET",
                url: '<?php echo e(route('kontingent_save')); ?>',
                data: { replace_id: JSON.stringify(removed), hotel_id : <?php echo e($hotel->id); ?> ,dates : current_date},
                success: function (data) {
                    if(data['error']) {
                        $('#warnReplace').modal();
                    }else {

                        $('#modalCreateKontingent').modal('hide');
                        setMonths();
                    }
                },

                error: function (data) {
                    console.log("kontingent data error")
                }
            });
        }

        function breakString(text) {
            return text.replace(/(.{100})/g, '$1\n');
        }


        $(document).ready(function () {

            var text = $('#desc_season').html();
            if(text) $('#desc_season').html(breakString(text));

            text = $('#desc_agreement').html();
            if(text) $('#desc_agreement').html(breakString(text));

            $('.date').css("z-index",0);

            $('#start_date').datepicker("remove");
            $('#start_date').datepicker({
                format: "mm-yyyy",
                startView: "months",
                minViewMode: "months"
            });



            $("#start_date").datepicker('setDate', (TodayDate.getMonth() + 1) + '-' + TodayDate.getFullYear());

            $("#start_date").datepicker({
                onSelect: function (dateText) {
                    $(this).change();
                }
            }).on("change", function () {
                $(this).datepicker("hide");
            });

            $('#showMonth').on('click', function (e) {
                setMonths();
            });

            $('.date_calendar').on('click', function (e) {
                $("#start_date").datepicker('show');
            });

            setMonths();

        });
		
		
        // Initialize Bootstrap table functionality for hotel invoices
        initializeBootstrapTable('inovices-table');

        // Hotel invoice table search functionality
        function filterHotelInvoiceTable() {
            const input = document.getElementById('hotelInvoiceSearchInput');
            const filter = input.value.toUpperCase();
            const table = document.getElementById('inovices-table');
            const tr = table.getElementsByTagName('tr');

            for (let i = 1; i < tr.length; i++) {
                let display = false;
                const td = tr[i].getElementsByTagName('td');

                for (let j = 0; j < td.length - 1; j++) { // Exclude action column
                    if (td[j]) {
                        const txtValue = td[j].textContent || td[j].innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            display = true;
                            break;
                        }
                    }
                }

                tr[i].style.display = display ? '' : 'none';
            }
        }

        // Export functions for hotel invoices
        function exportHotelInvoicesToCSV() {
            exportTableToCSV('inovices-table', 'hotel-invoices.csv');
        }

        function exportHotelInvoicesToExcel() {
            exportTableToExcel('inovices-table', 'hotel-invoices');
        }

    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('scaffold-interface.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/hotel/show.blade.php ENDPATH**/ ?>