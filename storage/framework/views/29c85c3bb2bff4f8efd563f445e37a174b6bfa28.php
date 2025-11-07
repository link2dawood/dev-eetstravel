
<?php $__env->startSection('title','Create'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('layouts.title',
    ['title' => 'Restaurant', 'sub_title' => 'Create Restaurant',
    'breadcrumbs' => [
    ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
    ['title' => 'Restaurants', 'icon' => 'coffee', 'route' => route('restaurant.index')],
    ['title' => 'Create', 'route' => null]]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <section class="content">
        <div class="box box-primary">
            <div class="box box-body border_top_none">
                <form method='POST' action='<?php echo e(route('restaurant.store')); ?>' enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="margin_button">
                                <a href="javascript:history.back()">
                                    <button type="button" class='btn btn-primary back_btn'><?php echo e(trans('main.Back')); ?></button>
                                </a>
                                <button class='btn btn-success' type='submit'><?php echo e(trans('main.Save')); ?></button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                                <?php echo e(csrf_field()); ?>

                                <div class="form-group <?php echo e($errors->has('name') ? 'has-error' : ''); ?>">
                                    <label for="name"><?php echo e(trans('main.Name')); ?></label>
                                    <input id="name" name="name" type="text" class="form-control" value="<?php echo e(old('name')); ?>">
                                    <?php if($errors->has('name')): ?>
                                        <strong><?php echo e($errors->first('name')); ?></strong>
                                    <?php endif; ?>
                                </div>
                                <div class="form-group">
                                    <label for="address_first"><?php echo e(trans('main.AddressFirst')); ?></label>
                                    <input id="address_first" name="address_first" type="text" class="form-control" value="<?php echo e(old('address_first')); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="address_second"><?php echo e(trans('main.AddressSecond')); ?></label>
                                    <input id="address_second" name="address_second" type="text" class="form-control" value="<?php echo e(old('address_second')); ?>">
                                </div>
						
                                <?php $__env->startComponent('component.city_form', ['country_label' => 'country', 'country_translation' => 'main.Country', 'country_default' =>"" ,
                                                                    'city_label' => 'city','city_translation' =>'main.City', 'city_default' =>""]); ?>
                                <?php echo $__env->renderComponent(); ?>
						
                                <div class="form-group">
                                    <label for="work_phone"><?php echo e(trans('main.WorkPhone')); ?></label>
                                    <input id="work_phone" name="work_phone" type="text" class="form-control" value="<?php echo e(old('work_phone')); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="work_fax"><?php echo e(trans('main.WorkFax')); ?></label>
                                    <input id="work_fax" name="work_fax" type="text" class="form-control" value="<?php echo e(old('work_fax')); ?>">

                                </div>
                                <div class="form-group">
                                    <label for="work_email"><?php echo e(trans('main.WorkEmail')); ?></label>
                                    <input id="work_email" name="work_email" type="text" class="form-control" value="<?php echo e(old('work_email')); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="contact_name"><?php echo e(trans('main.ContactName')); ?></label>
                                    <input id="contact_name" name="contact_name" type="text" class="form-control" value="<?php echo e(old('contact_name')); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="contact_phone"><?php echo e(trans('main.ContactPhone')); ?></label>
                                    <input id="contact_phone" name="contact_phone" type="text" class="form-control" value="<?php echo e(old('contact_phone')); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="contact_email"><?php echo e(trans('main.ContactEmail')); ?></label>
                                    <input id="contact_email" name="contact_email" type="text" class="form-control" value="<?php echo e(old('contact_email')); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="comments"><?php echo e(trans('main.Comments')); ?></label>
                                    <input id="comments" name="comments" type="text" class="form-control" value="<?php echo e(old('comments')); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="int_comments"><?php echo e(trans('main.IntComments')); ?></label>
                                    <input id="int_comments" name="int_comments" type="text" class="form-control" value="<?php echo e(old('int_comments')); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="website"><?php echo e(trans('main.Website')); ?></label>
                                    <input id="website" name="website" type="text" class="form-control" value="<?php echo e(old('website')); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="code"><?php echo e(trans('main.Code')); ?></label>
                                    <input id="code" name="code" type="text" class="form-control" value="<?php echo e(old('code')); ?>">
                                </div>

                                <div class="form-group">
                                    <label for="criteria"><?php echo e(trans('main.Criteria')); ?></label>
                                </div>
                                <?php $__currentLoopData = $criterias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $criteria): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="form-group criteria_block">
                                        <input type="checkbox" value="<?php echo e($criteria->id); ?>" name="criterias">
                                        <label for=""><?php echo e($criteria->name); ?></label>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                <div class="form-group">
                                    <label for="rate"><?php echo e(trans('main.Rate')); ?></label>
                                    <select name="rate" id="rate" class="form-control">
                                        <?php $__currentLoopData = $rates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option <?php echo e(old('rate') == $rate->id ? 'selected' : ''); ?> value="<?php echo e($rate->id); ?>"><?php echo e($rate->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>


                                <input type="text" hidden name="place_id" id="place_id">
                                <div class="form-group">
                                    <label>Files</label>
                                    <?php $__env->startComponent('component.file_upload_field'); ?><?php echo $__env->renderComponent(); ?>
                                </div>
                                <button class='btn btn-success' type='submit'><?php echo e(trans('main.Save')); ?></button>
                        </div>
                        <div class="col-md-6">
                            <span id="page" data-page="create"></span>
                            <button class="btn btn-primary" id="btn_generate_map"><?php echo e(trans('main.GenerateLocation')); ?></button>
                            <button class="btn btn-primary btn_google_maps" id="btn_select_location"><?php echo e(trans('main.SelectLocation')); ?></button>
                            <br>
                            <span id="error_map"></span>
                            <div class="block_map">
                                <div id="map"></div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('scaffold-interface.layouts.tabler-app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\dev-eetstravel\resources\views/restaurant/create.blade.php ENDPATH**/ ?>