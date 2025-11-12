
<form id="roomlist_form" action="<?php echo e(route('guestlist.store')); ?>" method="POST">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="tourId" id="tourId" value="<?php echo e($tour->id); ?>">
    <input type="hidden" id="show_url" value="<?php echo e(route('roomlist.show', ['id' => $tour->id])); ?>">
    <input type="hidden" id="send_url" value="<?php echo e(route('guestlist.send', ['id' => $tour->id])); ?>">
    
    
    <div class="row g-3">
        
        <div class="col-md-12">
            <label class="form-label required">Guest List Name</label>
            <input name="name" 
                   class="form-control" 
                   type="text" 
                   placeholder="Enter guest list name" 
                   id="guest_list_name" 
                   required>
            <div class="hide validate-name text-danger mt-1">
                <i class="ti ti-alert-circle me-1"></i>
                <span><?php echo trans('main.Nameisrequiredfield'); ?></span>
            </div>
        </div>

        
        <div class="col-md-6">
            <label class="form-label"><?php echo trans('main.Template'); ?></label>
            <select id="template_selector_guest" 
                    name="template_selector_guest" 
                    class="form-select">
                <option value="" disabled selected>Choose template...</option>
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label"><?php echo trans('main.Hotels'); ?></label>
            <select multiple 
                    id="hotelselect" 
                    name="hotelIds[]" 
                    class="form-select"
                    size="5">
                <?php $__currentLoopData = $tour->getHotels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hotel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($hotel->name): ?>
                        <option value="<?php echo e($hotel->id); ?>"><?php echo $hotel->name; ?></option>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <div class="form-check mt-2">
                <input type="checkbox" id="checkboxallhotels" class="form-check-input">
                <label class="form-check-label" for="checkboxallhotels">
                    <?php echo trans('main.SelectAll'); ?>

                </label>
            </div>
        </div>

        
        <div class="col-md-12">
            <label class="form-label">Guest List Content</label>
            <textarea name="roomlist_textarea" 
                      id="roomlist_textarea"
                      class="form-control" 
                      style="height: 600px; visibility: hidden; display: none;">
            </textarea>
        </div>
    </div>
</form>

<?php /**PATH /var/www/html/resources/views/components/guest-list-form.blade.php ENDPATH**/ ?>