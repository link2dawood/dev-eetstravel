      <div id="legend_help" style=" z-index:9999; position: absolute;top:-20px;width:350px; right: -100%; background-color: rgb(255, 255, 255);opacity: 0;">

                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-sm-12"><h2><?php echo trans('main.EmailTemplates'); ?></h2><hr></div>
                            </div>

                            <div class="row top" >
                                <div class="col-sm-3" >
                                    <button class="btn btn-success btn-xs" style="margin-left: 16px;" ><?php echo trans('main.New'); ?></button>
                                </div>
                                <div class="col-sm-9"><p><?php echo trans('main.AddTemplatenameand'); ?></p>
                                        <small></small>
                                </div>
                            </div>

                            <div class="row" >
                                <div class="col-sm-3">
                                    <i class='fa fa-pencil-square-o legend-media' style="background-color: #3c8cbb;"></i>
                                </div>
                                <div class="col-sm-9"><p><?php echo trans('main.EditTemplatenameandcontent'); ?></p>
                                    <small></small>
                                </div>
                            </div>

                            <div class="row" >
                                <div class="col-sm-3">
                                    <i class='fa fa-trash-o legend-media' style="background-color: #dc4a39;"></i>
                                </div>
                                <div class="col-sm-9"><p><?php echo trans('main.ConfirmremovalofTemplateforever'); ?></p>
                                    <small></small>
                                </div>
                            </div>

                            <div class="row align-self-center" style="margin-bottom: 16px;" >
                                <div class="col-sm-12 text-center">
                                    <span > <?php echo trans('main.Availabletags'); ?></span>
                                </div>

                            </div>

                            <div class="row line_br" >
                                <div class="col-sm-6">
                                    <button class="btn btn-xs centered" type="button" style="margin-left: 16px;margin-bottom: 8px;">##name##</button><br>
                                    <button class="btn btn-xs centered" type="button" style="margin-left: 16px;margin-bottom: 8px;">##date##</button><br>
                                    <button class="btn btn-xs centered" type="button" style="margin-left: 16px;margin-bottom: 8px;">##pax##</button><br>
                                    <button class="btn btn-xs centered" type="button" style="margin-left: 16px;margin-bottom: 8px;">##address##</button><br>
                                    <button class="btn btn-xs centered" type="button" style="margin-left: 16px;margin-bottom: 8px;">##email##</button><br>
                                    <button class="btn btn-xs centered" type="button" style="margin-left: 16px;margin-bottom: 8px;">##phone##</button>
                                </div>
                                <div class="col-sm-6">
                                    <button class="btn btn-xs centered" type="button" style="margin-left: 16px;margin-bottom: 8px;">##description##</button><br>
                                    <button class="btn btn-xs centered" type="button" style="margin-left: 16px;margin-bottom: 8px;">##status##</button><br>
                                    <button class="btn btn-xs centered" type="button" style="margin-left: 16px;margin-bottom: 8px;">##time_from##</button><br>
                                    <button class="btn btn-xs centered" type="button" style="margin-left: 16px;margin-bottom: 8px;">##price_for_one##</button><br>
                                    <button class="btn btn-xs centered" type="button" style="margin-left: 16px;margin-bottom: 8px;">##menu##</button><br>
                                    <button class="btn btn-xs centered" type="button" style="margin-left: 16px;margin-bottom: 8px;">##roominglist##</button>
                                </div>
                            </div>

                            <div class="row bottom"  >
                                <div class="col-sm-12">
                                    <small style="margin-left: 16px;"><?php echo trans('main.Thetagswillbereplaced'); ?></small>
                                </div>

                            </div>

                        </div>
        </div><?php /**PATH /var/www/html/resources/views/legend/templates_legend.blade.php ENDPATH**/ ?>