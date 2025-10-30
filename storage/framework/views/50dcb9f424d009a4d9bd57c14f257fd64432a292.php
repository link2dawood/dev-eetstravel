<div id="legend_help_guest_list" style=" z-index:9999; position: absolute;top:-20px;width:350px; left: 65%; background-color: rgb(255, 255, 255);opacity: 0;">

    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12"><h2><?php echo e(trans('main.Guestlist')); ?></h2><hr></div>
        </div>

        <div class="row top" >
            <div class="col-sm-3" >
                <button class="btn btn-success btn-xs" type="button" style="width: 50px;"><?php echo e(trans('main.Add')); ?></button>
            </div>
            <div class="col-sm-9"><p><?php echo e(trans('main.AddGuestlist')); ?><br> <?php echo e(trans('main.parameters')); ?></p>
                <small></small>
            </div>
        </div>
        <div class="row bottom">
            <div class="col-sm-3" >
                <i class='fa fa-paper-plane-o legend-media' style="background-color: #3c8cbb;"></i>
            </div>
            <div class="col-sm-9"><p><?php echo e(trans('main.Sendemailto')); ?></p>
                <small></small>
            </div>
        </div>
    </div>
</div><?php /**PATH /var/www/html/resources/views/legend/guest_list_legend.blade.php ENDPATH**/ ?>