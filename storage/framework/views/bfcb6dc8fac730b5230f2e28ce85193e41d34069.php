      <div id="legend_help" style=" z-index:9999; position: absolute;top:-20px;width:350px; right: -100%; background-color: rgb(255, 255, 255);opacity: 0;">

                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-sm-12"><h2><?php echo e(trans('main.Buses')); ?></h2><hr></div>
                            </div>

                            <div class="row top" >
                                <div class="col-sm-3" >
                                    <button class="btn btn-success btn-xs" type="button"><i class="fa fa-plus fa-md" aria-hidden="true"></i> <?php echo e(trans('main.New')); ?></button>
                                </div>
                                <div class="col-sm-9"><p><?php echo e(trans('main.AddBusand')); ?></p>

                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-3" >
                                    <i class='fa fa-info-circle legend-media' style="background-color: #f29b1a;"></i>
                                </div>
                                <div class="col-sm-9"><p><?php echo e(trans('main.ShowBusinfo')); ?></p>
                                </div>
                            </div>


                            <div class="row" >
                                <div class="col-sm-3">
                                    <i class='fa fa-pencil-square-o legend-media' style="background-color: #3c8cbb;"></i>
                                </div>
                                <div class="col-sm-9"><p><?php echo e(trans('main.EditBusparameters')); ?></p>
                                </div>
                            </div>

                            <div class="row" >
                                <div class="col-sm-3">
                                    <i class='fa fa-trash-o legend-media' style="background-color: #dc4a39;"></i>
                                </div>
                                <div class="col-sm-9"><p><?php echo e(trans('main.ConfirmremovalofBusforever')); ?></p>
                                </div>
                            </div>

                            <div class="row line_br" >
                                <div class="col-sm-3">
                                    <button class="btn btn-xs centered" type="button" style="margin-left: 16px;">CSV</button>
                                </div>
                                <div class="col-sm-9"><p><?php echo e(trans('main.ExportBuseslisttoCSVSheet')); ?></p>
                                </div>
                            </div>

                            <div class="row line_br" >
                                <div class="col-sm-3">
                                    <button class="btn btn-xs centered" type="button" style="margin-left: 12px;">Excel</button>
                                </div>
                                <div class="col-sm-9"><p><?php echo e(trans('main.ExportBuseslisttoExcelSheet')); ?></p>

                                </div>
                            </div>

                            <div class="row line_br" >
                                <div class="col-sm-3">
                                    <button class="btn btn-xs centered" type="button" style="margin-left: 16px;">PDF</button>
                                </div>
                                <div class="col-sm-9">
                                    <p><?php echo e(trans('main.ExportBuseslisttoPDFDocument')); ?></p>
                                </div>
                            </div>

                            <div class="row bottom" >
                                <div class="col-sm-3">
                                    <input type="text" style="width:70px;" disabled placeholder="Search text">
                                </div>
                                <div class="col-sm-9"><p><?php echo e(trans('main.SearchamongallavailableBuses')); ?></p>
                                    <small> </small>
                                </div>
                            </div>

                        </div>
        </div><?php /**PATH /var/www/html/resources/views/legend/buses_legend.blade.php ENDPATH**/ ?>