

<?php if(session()->has('flashMessages')): ?>
    
    <?php if(count(session('flashMessages')['success']) > 0): ?>
        <?php $__currentLoopData = session('flashMessages')['success']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <script>
                $.toast({
                    heading: 'Success',
                    text: "<?php echo $message; ?>",
                    icon: 'success',
                    loader: true,        // Change it to false to disable loader
                    hideAfter : 15000,
                    position: 'top-right',
                });
                </script>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>
    
    
    <?php if(count(session('flashMessages')['error']) > 0): ?>
        <?php $__currentLoopData = session('flashMessages')['error']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <script>
                $.toast({
                    heading: 'Error',
                    text: "<?php echo $message; ?>",
                    icon: 'error',
//                    loaderBg: '#9EC600',  // To change the background,
                    hideAfter : 15000,
                    position: 'top-right',
                });
            </script>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>
    
    
    <?php if(count(session('flashMessages')['warning']) > 0): ?>
        <?php $__currentLoopData = session('flashMessages')['warning']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <script>
                $.toast({
                    heading: 'Information',
                    text: "<?php echo $message; ?>",
                    icon: 'info',
                    loader: true,        // Change it to false to disable loader
                    loaderBg: '#9EC600',  // To change the background,
                    hideAfter : 15000,
                    position: 'top-right',
                });
            </script>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>
    
    
    <?php if(count(session('flashMessages')['info']) > 0): ?>
        <?php $__currentLoopData = session('flashMessages')['info']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <script>
                $.toast({
                    heading: 'Information',
                    text: "<?php echo $message; ?>",
                    icon: 'info',
                    loader: true,        // Change it to false to disable loader
                    loaderBg: '#9EC600',  // To change the background,
                    hideAfter : 15000,
                    position: 'top-right',
                });
            </script>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>
    
<?php endif; ?>

<?php /**PATH /var/www/html/resources/views/component/session-messages.blade.php ENDPATH**/ ?>