
<?php $__env->startSection('title', 'Create Guest List'); ?>

<?php $__env->startSection('post_styles'); ?>
<style>
    :root {
        --primary: #667eea;
        --primary-dark: #764ba2;
        --success: #48bb78;
        --danger: #e53e3e;
        --light-gray: #f7fafc;
        --border: #e2e8f0;
    }

    body {
        background-color: #f5f7fa;
    }

    .box {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        margin-bottom: 20px;
    }

    .box-body {
        padding: 24px;
    }

    .section-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 2px solid var(--border);
    }

    .section-header i {
        font-size: 20px;
        color: var(--primary);
    }

    .section-header h3 {
        font-size: 16px;
        font-weight: 600;
        color: #2d3748;
        margin: 0;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 8px;
    }

    .required {
        color: var(--danger);
        margin-left: 4px;
    }

    .form-control {
        width: 100%;
        padding: 11px 14px;
        font-size: 14px;
        border: 2px solid var(--border);
        border-radius: 6px;
        background: #fff;
        transition: all 0.3s;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .form-control::placeholder {
        color: #a0aec0;
    }

    textarea.form-control {
        resize: vertical;
        min-height: 280px;
        font-family: inherit;
    }

    .select-all-wrapper {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 16px;
        padding: 12px 14px;
        background: var(--light-gray);
        border-radius: 6px;
    }

    .select-all-wrapper input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    .select-all-wrapper label {
        margin: 0;
        font-weight: 500;
        color: #2d3748;
        cursor: pointer;
        font-size: 14px;
    }

    .help-text {
        font-size: 12px;
        color: #718096;
        margin-top: 6px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
    }

    /* CKEditor Wrapper */
    .ckeditor-wrapper {
        border: 2px solid var(--border);
        border-radius: 6px;
        overflow: hidden;
        transition: border-color 0.3s;
    }

    .ckeditor-wrapper:focus-within {
        border-color: var(--primary);
    }

    /* Buttons */
    .btn-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        padding: 20px 24px;
        border-top: 1px solid var(--border);
        background: var(--light-gray);
        margin-top: 24px;
    }

    .btn {
        padding: 11px 24px;
        font-size: 14px;
        font-weight: 600;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-success {
        background: linear-gradient(135deg, var(--success) 0%, #38a169 100%);
        color: white;
    }

    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(72, 187, 120, 0.4);
    }

    .btn-warning {
        background: #e2e8f0;
        color: #4a5568;
    }

    .btn-warning:hover {
        background: #cbd5e0;
    }

    .btn-loading {
        position: relative;
        color: transparent;
    }

    .btn-loading::after {
        content: '';
        position: absolute;
        width: 14px;
        height: 14px;
        top: 50%;
        left: 50%;
        margin-left: -7px;
        margin-top: -7px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-top-color: white;
        border-radius: 50%;
        animation: spin 0.6s linear infinite;
    }

    @keyframes  spin {
        to { transform: rotate(360deg); }
    }

    .alert {
        padding: 14px 16px;
        border-radius: 6px;
        margin-bottom: 20px;
        display: flex;
        align-items: start;
        gap: 12px;
    }

    .alert-danger {
        background: #fff5f5;
        border-left: 4px solid var(--danger);
        color: #c53030;
    }

    .alert-danger ul {
        margin: 8px 0 0 0;
        padding-left: 20px;
    }

    .alert i {
        font-size: 18px;
        margin-top: 2px;
    }

    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }

        .btn-actions {
            flex-direction: column-reverse;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }

        .box-body {
            padding: 16px;
        }
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-xl">
    <?php echo $__env->make('layouts.title', [
        'title' => 'Guest List',
        'sub_title' => 'Create New Guest List',
        'breadcrumbs' => [
            ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
            ['title' => 'Guest Lists', 'icon' => 'users', 'route' => route('guestlist.index')],
            ['title' => 'Create', 'icon' => 'plus', 'route' => null]
        ]
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <section class="content">
        <div class="box">
            <div class="box-body">
                <form id="guestlist_form" method="POST" action="<?php echo e(route('guestlist.store')); ?>">
                    <?php echo csrf_field(); ?>

                    
                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger">
                            <i class="ti ti-alert-circle"></i>
                            <div>
                                <strong>Please fix the following errors:</strong>
                                <ul>
                                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><?php echo e($error); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                        </div>
                    <?php endif; ?>

                    
                    <div class="section-header">
                        <i class="ti ti-info-circle"></i>
                        <h3>Basic Information</h3>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="guest_list_name">
                                Guest List Name
                                <span class="required">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="guest_list_name" 
                                name="name" 
                                class="form-control" 
                                placeholder="Enter guest list name"
                                value="<?php echo e(old('name')); ?>"
                                required
                            >
                            <p class="help-text">A descriptive name for this guest list</p>
                        </div>

                        <div class="form-group">
                            <label>Tour</label>
                            <input 
                                type="text" 
                                class="form-control" 
                                value="<?php echo e($tour->name); ?>" 
                                disabled
                            >
                            <input type="hidden" name="tourId" value="<?php echo e($tour->id); ?>">
                        </div>
                    </div>

                    
                    <div class="section-header">
                        <i class="ti ti-template"></i>
                        <h3>Template</h3>
                    </div>

                    <div class="form-group">
                        <label for="template_selector_guest">Select Template</label>
                        <select id="template_selector_guest" class="form-control">
                            <option value="" disabled hidden>Choose template...</option>
                        </select>
                        <p class="help-text">Select a pre-designed template to format your guest list</p>
                    </div>

                    
                    <div class="section-header">
                        <i class="ti ti-building"></i>
                        <h3>Hotels</h3>
                    </div>

                    <div class="select-all-wrapper">
                        <input 
                            type="checkbox" 
                            id="checkboxallhotels"
                        >
                        <label for="checkboxallhotels">Select All Hotels</label>
                    </div>

                    <div class="form-group">
                        <select id="hotelselect" name="hotelIds[]" class="form-control" multiple required>
                            <?php $__currentLoopData = $hotels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hotel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($hotel->id); ?>"><?php echo e($hotel->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <p class="help-text">Select one or more hotels (Required)</p>
                    </div>

                    
                    <div class="section-header">
                        <i class="ti ti-document"></i>
                        <h3>Guest List Content</h3>
                    </div>

                    <div class="form-group">
                        <label for="roomlist_textarea">Content</label>
                        <div class="ckeditor-wrapper">
                            <textarea 
                                id="roomlist_textarea" 
                                name="roomlist_textarea" 
                                class="form-control"
                            ><?php echo e(old('roomlist_textarea')); ?></textarea>
                        </div>
                    </div>

                    
                    <div class="btn-actions">
                        <a href="<?php echo e(route('guestlist.index')); ?>" class="btn btn-warning">
                            <i class="ti ti-x"></i>
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-success" id="submitBtn">
                            <i class="ti ti-device-floppy"></i>
                            Save Guest List
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    
    <div class="modal fade" id="error_send" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 bg-light">
                    <h5 class="modal-title">
                        <i class="ti ti-alert-circle me-2" style="color: var(--danger);"></i>Validation Error
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="ti ti-alert-triangle"></i>
                        <p class="error_send_message" style="margin: 0;"></p>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal">
                        <i class="ti ti-check me-1"></i>OK
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('post_scripts'); ?>
<script type="text/javascript" src="<?php echo e(asset('js/ckeditor/ckeditor.js')); ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tour_id = '<?php echo e($tour->id); ?>';
    
    // Load templates
    $.ajax({
        url: '/templates/api/loadServiceTemplates',
        method: 'GET',
        data: { id: 0 },
        success: function(res) {
            let html = '<option value="" disabled hidden>Choose template...</option>';
            if(res.templates) {
                res.templates.forEach(template => {
                    if(template.name !== 'Footer' && template.name !== 'Header') {
                        html += `<option value="${template.id}">${template.name}</option>`;
                    }
                });
            }
            $('#template_selector_guest').html(html);
        }
    });

    // Template change handler
    $('#template_selector_guest').on('change', function() {
        const templateId = $(this).val();
        if(templateId) {
            $.ajax({
                url: '/templates/api/load',
                method: 'GET',
                data: {
                    id: templateId,
                    tour_id: tour_id
                },
                success: function(res) {
                    if (CKEDITOR.instances['roomlist_textarea']) {
                        CKEDITOR.instances['roomlist_textarea'].setData(res.content);
                    }
                }
            });
        }
    });

    // Select all hotels
    $("#checkboxallhotels").on('change', function() {
        if($(this).is(':checked')) {
            $("#hotelselect > option").prop("selected", true);
            $("#hotelselect").trigger("change");
        } else {
            $("#hotelselect > option").prop("selected", false);
            $("#hotelselect").trigger("change");
        }
    });

    // Form submission
    $('#guestlist_form').on('submit', function(e) {
        const name = $('#guest_list_name').val();
        const hotels = $("#hotelselect").val();

        if(!name || !hotels || hotels.length === 0) {
            e.preventDefault();
            const errorModal = new bootstrap.Modal(document.getElementById('error_send'));
            $('#error_send').find('.error_send_message').text('Please fill all required fields (Guest List Name and Hotels)');
            errorModal.show();
            return false;
        }

        $('#submitBtn').addClass('btn-loading').prop('disabled', true);
        return true; // Allow form to submit
    });
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('scaffold-interface.layouts.tabler-app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\dev-eetstravel\resources\views/guest_list/create.blade.php ENDPATH**/ ?>