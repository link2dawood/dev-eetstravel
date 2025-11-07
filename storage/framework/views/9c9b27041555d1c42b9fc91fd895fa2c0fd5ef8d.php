
<?php $__env->startSection('title', 'Create Bus Company'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-xl">
    
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(url('/home')); ?>">Home</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('transfer.index')); ?>">Bus Companies</a></li>
                            <li class="breadcrumb-item active">Create</li>
                        </ol>
                    </nav>
                </div>
                <h2 class="page-title">
                    <i class="ti ti-plus me-2"></i>Create Bus Company
                </h2>
            </div>
        </div>
    </div>

    
    <form method="POST" action="<?php echo e(route('transfer.store')); ?>" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Company Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label required"><?php echo trans('main.Name'); ?></label>
                                <input id="name" 
                                       name="name" 
                                       type="text" 
                                       class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       value="<?php echo e(old('name')); ?>" 
                                       required>
                                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label"><?php echo trans('main.Code'); ?></label>
                                <input id="code" 
                                       name="code" 
                                       type="text" 
                                       class="form-control" 
                                       value="<?php echo e(old('code')); ?>">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label required"><?php echo trans('main.AddressFirst'); ?></label>
                                <input id="address_first" 
                                       name="address_first" 
                                       type="text" 
                                       class="form-control" 
                                       value="<?php echo e(old('address_first')); ?>" 
                                       required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label"><?php echo trans('main.AddressSecond'); ?></label>
                                <input id="address_second" 
                                       name="address_second" 
                                       type="text" 
                                       class="form-control" 
                                       value="<?php echo e(old('address_second')); ?>">
                            </div>

                            
                            <div class="col-12 mb-3">
                                <?php $__env->startComponent('component.city_form', [
                                    'country_label' => 'country', 
                                    'country_translation' => 'main.Country', 
                                    'country_default' => 0,
                                    'city_label' => 'city',
                                    'city_translation' => 'main.City', 
                                    'city_default' => 0
                                ]); ?>
                                <?php echo $__env->renderComponent(); ?>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label"><?php echo trans('main.WorkPhone'); ?></label>
                                <div class="input-icon">
                                    <span class="input-icon-addon">
                                        <i class="ti ti-phone"></i>
                                    </span>
                                    <input id="work_phone" 
                                           name="work_phone" 
                                           type="text" 
                                           class="form-control" 
                                           value="<?php echo e(old('work_phone')); ?>">
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label"><?php echo trans('main.WorkFax'); ?></label>
                                <input id="work_fax" 
                                       name="work_fax" 
                                       type="text" 
                                       class="form-control" 
                                       value="<?php echo e(old('work_fax')); ?>">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label"><?php echo trans('main.WorkEmail'); ?></label>
                                <div class="input-icon">
                                    <span class="input-icon-addon">
                                        <i class="ti ti-mail"></i>
                                    </span>
                                    <input id="work_email" 
                                           name="work_email" 
                                           type="email" 
                                           class="form-control" 
                                           value="<?php echo e(old('work_email')); ?>">
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label"><?php echo trans('main.Website'); ?></label>
                                <div class="input-icon">
                                    <span class="input-icon-addon">
                                        <i class="ti ti-world"></i>
                                    </span>
                                    <input id="website" 
                                           name="website" 
                                           type="url" 
                                           class="form-control" 
                                           value="<?php echo e(old('website')); ?>">
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label"><?php echo trans('main.ContactName'); ?></label>
                                <input id="contact_name" 
                                       name="contact_name" 
                                       type="text" 
                                       class="form-control" 
                                       value="<?php echo e(old('contact_name')); ?>">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label"><?php echo trans('main.ContactPhone'); ?></label>
                                <div class="input-icon">
                                    <span class="input-icon-addon">
                                        <i class="ti ti-phone"></i>
                                    </span>
                                    <input id="contact_phone" 
                                           name="contact_phone" 
                                           type="text" 
                                           class="form-control" 
                                           value="<?php echo e(old('contact_phone')); ?>">
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label"><?php echo trans('main.ContactEmail'); ?></label>
                                <div class="input-icon">
                                    <span class="input-icon-addon">
                                        <i class="ti ti-mail"></i>
                                    </span>
                                    <input id="contact_email" 
                                           name="contact_email" 
                                           type="email" 
                                           class="form-control" 
                                           value="<?php echo e(old('contact_email')); ?>">
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label"><?php echo trans('main.Comments'); ?></label>
                                <input id="comments" 
                                       name="comments" 
                                       type="text" 
                                       class="form-control" 
                                       value="<?php echo e(old('comments')); ?>">
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label"><?php echo trans('main.IntComments'); ?></label>
                                <textarea id="int_comments" 
                                          name="int_comments" 
                                          rows="3" 
                                          class="form-control"><?php echo e(old('int_comments')); ?></textarea>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label"><?php echo trans('main.Rate'); ?></label>
                                <select name="rate" id="rate" class="form-select">
                                    <?php $__currentLoopData = $rates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($rate->id); ?>" <?php echo e(old('rate') == $rate->id ? 'selected' : ''); ?>>
                                            <?php echo e($rate->name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label"><?php echo trans('main.Criteria'); ?></label>
                                <div class="row">
                                    <?php $__currentLoopData = $criterias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $criteria): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="col-md-6 mb-2">
                                            <label class="form-check">
                                                <input type="checkbox" 
                                                       class="form-check-input" 
                                                       value="<?php echo e($criteria->id); ?>" 
                                                       name="criterias">
                                                <span class="form-check-label"><?php echo e($criteria->name); ?></span>
                                            </label>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label"><?php echo trans('main.Files'); ?></label>
                                <?php $__env->startComponent('component.file_upload_field'); ?><?php echo $__env->renderComponent(); ?>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="btn-list">
                            <button class="btn btn-primary" type="submit">
                                <i class="ti ti-check me-1"></i><?php echo trans('main.Save'); ?>

                            </button>
                            <a href="<?php echo e(route('transfer.index')); ?>" class="btn">
                                <i class="ti ti-x me-1"></i>Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="col-lg-4">
                <div class="card sticky-top" style="top: 1rem;">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ti ti-map me-2"></i>Location
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <button class="btn btn-primary w-100 mb-2" 
                                    type="button" 
                                    id="btn_generate_map">
                                <i class="ti ti-map-pin me-1"></i><?php echo trans('main.GenerateLocation'); ?>

                            </button>
                            <button class="btn btn-outline-primary w-100" 
                                    type="button" 
                                    id="btn_select_location">
                                <i class="ti ti-click me-1"></i><?php echo trans('main.SelectLocation'); ?>

                            </button>
                        </div>
                        <span id="error_map" class="text-danger"></span>
                        <div class="block_map">
                            <div id="map" style="height: 400px; border-radius: 6px;"></div>
                        </div>
                        <input type="hidden" name="place_id" id="place_id">
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<span id="page" data-page="create" hidden></span>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('js/google_map.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('scaffold-interface.layouts.tabler-app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\dev-eetstravel\resources\views/transfer/create.blade.php ENDPATH**/ ?>