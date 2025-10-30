      <div id="legend_help" style=" z-index:9999; position: absolute;top:-20px;width:350px; right: -100%; background-color: rgb(255, 255, 255);opacity: 0;">

                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-sm-12"><h2><?php echo e(trans('main.FrontSheet')); ?></h2><hr></div>
                            </div>



                            <div class="row top">
                                <div class="col-sm-3" >
                                    <a class="comments-button" style="margin-left: 16px;">
                                        <span class="badge bg-yellow">0</span>
                                        <i class="fa fa-comment-o" aria-hidden="true"></i>
                                    </a>
                                </div>
                                <div class="col-sm-9"><p>Add/Show comments</p>

                                </div>
                            </div>




                            <div class="row line_br">
                                <div class="col-sm-3" >
                                    <div class="input-group date" style="width:70px">
                                        <input type="text" name="hotel_list_sent" class="form-control datepicker" value="">
                                        <span class="input-group-addon">
                                              <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                    </div>
                                </div>
                                <div class="col-sm-9"><p>* Rooming list received</p>

                                </div>
                            </div>

                            <div class="row line_br">
                                <div class="col-sm-3" >
                                    <div class="input-group date" style="width:70px">
                                        <input type="text" name="hotel_list_sent" class="form-control datepicker" value="">
                                        <span class="input-group-addon">
                                              <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                    </div>
                                </div>
                                <div class="col-sm-9"><p>* Visa confirmation sent</p>

                                </div>
                            </div>






                            <div class="row line_br">
                                <div class="col-sm-3" >
                                   <textarea style="width: 70px;"></textarea>
                                </div>
                                <div class="col-sm-9"><p>Text field for important<br> comments or e-mails</p>

                                </div>
                            </div>


                            <div class="row line_br">
                                <div class="col-sm-3" >
                                    <input type="checkbox" checked class="pull-right" style="margin-right: -10px;" >
                                </div>
                                <div class="col-sm-9"><p>When all the checkboxes of the<br> column are checked, the current date<br> appears in the corresponding field</p>

                                </div>
                            </div>

                            <div class="row bottom">
                                <div class="col-sm-12">
                                    <small>* When the date is selected, the checkboxes of the<br> corresponding column become checked</small>
                                </div>
                            </div>


                        </div>
        </div><?php /**PATH /var/www/html/resources/views/legend/frontsheet_legend.blade.php ENDPATH**/ ?>