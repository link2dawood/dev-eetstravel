      <div id="legend_help" style="z-index:9999; position: absolute;top:-20px;width:350px; right: -100%; background-color: rgb(255, 255, 255);opacity: 0;">

                        <div class="container-fluid">


                            <div class="row">
                                <div class="col-sm-12"><h2><?php echo trans('main.SupplierSearch'); ?></h2><hr></div>
                            </div>


                            <div class="row top" >
                                <div class="col-sm-5">
                                    <input type="button" class="btn" value="Search" style="background-color:#3c8dbc; color:#fff">
                                </div>
                                <div class="col-sm-7"><p><?php echo trans('main.Startsearching'); ?></p>
                                    <small></small>
                                </div>
                            </div>

                            <div class="row line_br" >
                                <div class="col-sm-5">
                                    <div class="select_filter">
                                        <label style="display: block"></label>
                                        <div class="">
                                            <input type="checkbox" name="">English<br>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-7"><p><?php echo trans('main.SearchCriteria'); ?></p>

                                </div>
                            </div>

                            <div class="row line_br" >
                                <div class="col-sm-5">
                                    <div class="select_filter">
                                        <label style="display: block"></label>
                                        <div class="">

                                            <input type="radio" name="" value="">1*
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-7">
                                    <p><?php echo trans('main.Filterrates'); ?></p>
                                </div>
                            </div>

                            <div class="row line_br" >
                                <div class="col-sm-5" >
                                    <button class="btn btn-warning btn-xs" ><?php echo trans('main.ClearFilter'); ?></button>
                                </div>
                                <div class="col-sm-7"><p><?php echo trans('main.Clearcurrent'); ?></p>

                                </div>
                            </div>


                            <div class="row line_br" >
                                <div class="col-sm-5" >
                                    <button class="btn btn-success btn-xs" type="button" style="margin-left: 16px;"><?php echo trans('main.Tour'); ?></button>
                                </div>
                                <div class="col-sm-7"><p><?php echo trans('main.Addcurrentservice'); ?></p>

                                </div>
                            </div>

                            <div class="row bottom" >
                                <div class="col-sm-5">
                                    <i class='fa fa-plus legend-media' style="background-color: #00a55b;"></i>
                                </div>
                                <div class="col-sm-7"><p><?php echo trans('main.Selectdatestoadd'); ?></p>

                                </div>
                            </div>
                            
                        </div>
        </div><?php /**PATH /var/www/html/resources/views/legend/supplier_search.blade.php ENDPATH**/ ?>