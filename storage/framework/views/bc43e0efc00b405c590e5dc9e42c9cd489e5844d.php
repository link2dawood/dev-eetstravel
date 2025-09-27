      <div id="legend_help" style=" z-index:9999; position: absolute;top:-250px;width:1000px; left: 200%; background-color: rgb(255, 255, 255);opacity: 0;" data-mode="2" >

                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-sm-12"><h2><?php echo e(trans('main.Allotment')); ?></h2><hr></div>
                            </div>

                            <div class="row">
                                <div class="col-sm-3">
                                    <!-- --->
                                    <div class="container-fluid">

                                        <div class="row top" >
                                            <div class="col-sm-6">
                                                <div class="input-group date margin" style="margin-left: -2px;">
                                                    <div class="input-group-addon date_calendar"  >
                                                        <i class="fa fa-calendar" ></i>
                                                    </div>
                                                    <input type="text" style="width: 80px;" value="02-2018" class="form-control" disabled >
                                                </div>

                                            </div>
                                            <div class="col-sm-6" ><p style="margin-left: 16px;margin-top: 10px;"><?php echo e(trans('main.Firstmonthand')); ?> <br><?php echo e(trans('main.yeardatechooser')); ?></p>
                                                <small></small>
                                            </div>
                                        </div>

                                        <div class="row line_br" >
                                            <div class="col-sm-6" style="margin-left: -16px;">
                                                <button class="btn btn-xs centered" type="button" style="margin-left: 16px;background-color:#dbdee1;">1|2|3...</button>
                                            </div>
                                            <div class="col-sm-6"><p style="margin-left: 8px;margin-top: 0px;"><?php echo e(trans('main.Currentcalendardates')); ?></p>
                                                <small></small>
                                            </div>
                                        </div>

                                        <div class="row line_br" >
                                            <div class="col-sm-4" >
                                                <button class="btn btn-xs btn-danger" style="" ><?php echo e(trans('main.Delete')); ?></button>
                                            </div>
                                            <div class="col-sm-8"><p><?php echo e(trans('main.Confirmremovalofthe')); ?><br> <?php echo e(trans('main.HotelfromtheTour')); ?></p>
                                            </div>
                                        </div>



                                            <div class="row bottom" >
                                                <div class="col-sm-4">
                                                    <button class="btn btn-xs btn-success" type="button" ><?php echo e(trans('main.Replace')); ?></button>
                                                </div>
                                                <div class="col-sm-8"><p><?php echo e(trans('main.ReplacecurrentHotelwith')); ?> <br> <?php echo e(trans('main.AllotmentHotel')); ?></p>
                                                    <small></small>
                                                </div>
                                            </div>






                                        <!-- --->
                                    </div>
                                    <!-- --->
                                </div>
                                <div class="col-sm-4">
                                    <!-- --->
                                    <div class="container-fluid">




                                        <div class="row top" >
                                            <div class="col-sm-6">
                                                <button class="btn btn-xs centered" type="button" style="margin-left: 16px;background-color:#dbdee1;"><?php echo e(trans('main.Contractual')); ?><br> <?php echo e(trans('main.numberofrooms')); ?></button>
                                            </div>
                                            <div class="col-sm-6"><p><?php echo e(trans('main.Numbersofrooms')); ?><br><?php echo e(trans('main.specifiedinthe')); ?> <br><?php echo e(trans('main.agreementforthe')); ?><br> <?php echo e(trans('main.relevantperiod')); ?></p>
                                                <small></small>
                                            </div>
                                        </div>

                                        <div class="row line_br" >
                                            <div class="col-sm-6">
                                                <button class="btn btn-xs centered" type="button" style="margin-left: 16px;background-color:#dbdee1;"><?php echo e(trans('main.Numberof')); ?> <br><?php echo e(trans('main.roomsalreadyused')); ?></button>
                                            </div>
                                            <div class="col-sm-6"><p><?php echo e(trans('main.Numbersofroomsin')); ?><br> <?php echo e(trans('main.tourswithstatus')); ?><br> <?php echo e(trans('main.Allotmentused')); ?></p>
                                                <small></small>
                                            </div>
                                        </div>

                                        <div class="row bottom" >
                                            <div class="col-sm-6">
                                                <button class="btn btn-xs" type="button" style="background-color:#dbdee1;margin-left:16px;"><?php echo e(trans('main.AllotmentReserved')); ?></button>
                                            </div>
                                            <div class="col-sm-6"><p><?php echo e(trans('main.Numbersofroomsin')); ?><br> <?php echo e(trans('main.tourswithstatus')); ?><br> <?php echo e(trans('main.Allotmentreserved')); ?></p>
                                                <small></small>
                                            </div>
                                        </div>



                                    </div>


                                </div>

                                <div class="col-sm-4">
                                    <div class="container-fluid">



                                        <div class="row top" >
                                            <div class="col-sm-5">
                                                <button class="btn btn-xs" type="button" style="background-color:#dbdee1;"><?php echo e(trans('main.Availablequota')); ?></button>
                                            </div>
                                            <div class="col-sm-7"><p><?php echo e(trans('main.Differencebetweenthedatain')); ?><br> <?php echo e(trans('main.thecellsofthefirstand')); ?><br> <?php echo e(trans('main.secondrows')); ?></p>
                                                <small></small>
                                            </div>
                                        </div>

                                        <div class="row bottom" >
                                            <div class="col-sm-5">
                                                <button class="btn btn-xs" type="button" style="background-color:#dbdee1;"><?php echo e(trans('main.Currentbooking')); ?><br> <?php echo e(trans('main.status')); ?> %</button>
                                            </div>
                                            <div class="col-sm-7"><p><?php echo trans('main.Thepercentagethatrepresents'); ?></p>
                                                <small></small>
                                            </div>
                                        </div>


                                    </div>


                                </div>


                            </div>










                        </div>
      </div><?php /**PATH /var/www/html/resources/views/legend/kontingent_legend.blade.php ENDPATH**/ ?>