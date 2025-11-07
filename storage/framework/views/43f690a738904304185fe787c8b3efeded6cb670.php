
<div class="form-group">
    <label for="<?php echo e($country_label); ?>"><?php echo trans($country_translation); ?> *</label>
    <?php echo Form::select($country_label,  \App\Helper\Choices::getCountriesArray(), $country_default, ['class' => 'form-control', 'id' => $country_label]); ?>

</div>
<div class="form-group">
    <label for="<?php echo e($city_label); ?>"><?php echo trans($city_translation); ?> *</label>
    <?php echo Form::select($city_label, \App\Helper\Choices::getCitiesArray(), $city_default, ['class' => 'form-control', 'id' => $city_label]); ?>

</div><?php /**PATH D:\xampp\htdocs\dev-eetstravel\resources\views/component/city_form.blade.php ENDPATH**/ ?>