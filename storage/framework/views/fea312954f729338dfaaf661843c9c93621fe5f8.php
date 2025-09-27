      <div id="legend_help" style=" z-index:9999; position: absolute;top:-20px;width:350px; right: -100%; background-color: rgb(255, 255, 255);opacity: 0;">

                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-sm-12"><h2><?php echo e(trans('main.Permissions')); ?></h2><hr></div>
                            </div>

                            <div class="row top" >
                                <div class="col-sm-3" >
                                    <button class="btn btn-success btn-xs" type="button"><i class="fa fa-plus fa-md" aria-hidden="true"></i> <?php echo trans('main.New'); ?></button>
                                </div>
                                <div class="col-sm-9"><p><?php echo trans('main.AddPermissionand'); ?></p>
                                        <small></small>
                                </div>
                            </div>

                             <div class="row" >
                                <div class="col-sm-3">
                                    <i class='fa fa-pencil-square-o legend-media' style="background-color: #3c8cbb;"></i>
                                </div>
                                <div class="col-sm-9"><p><?php echo trans('main.EditPermissionparameters'); ?></p>
                                    <small></small>
                                </div>
                            </div>

                            <div class="row bottom" >
                                <div class="col-sm-3">
                                    <i class='fa fa-trash-o legend-media' style="background-color: #dc4a39;"></i>
                                </div>
                                <div class="col-sm-9"><p><?php echo trans('main.ConfirmremovalofPermissionforever'); ?></p>
                                    <small></small>
                                </div>
                            </div>



                        </div>
        </div><?php /**PATH /var/www/html/resources/views/legend/permissions_legend.blade.php ENDPATH**/ ?>