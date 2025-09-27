      <div id="legend_help" style="z-index:500; position: absolute;top:30px;width:500px; left: -20px; right: -100%; background-color: rgb(255, 255, 255);opacity: 0;"  data-mode="1" >

                        <div class="container-fluid">
                            <div class="row line_br">
                                <div class="col-sm-12"><h2><?php echo trans('main.TaskCalendar'); ?></h2><hr></div>
                            </div>

							<div class="row">								
								<div class="col-sm-4">
                                    <!-- --->
                                    <div class="container-fluid">
                                        <div class="row line_br">
                                            <div class="col-sm-4"></div>
                                            <div class="col-sm-8"><button class="btn btn-xs centered" type="button"><?php echo trans('main.Holidays'); ?></button></div>
                                        </div>


                                        <div class="row bottom">
                                            <div class="col-sm-4">
                                                <i class="fa fa-calendar" style="font-size:18px;color:#97a0b3" aria-hidden="true"></i>
                                            </div>
                                            <div class="col-sm-8"><p><?php echo trans('main.Checklistforselecting'); ?></p>
                                                <small></small>
                                            </div>
                                        </div>

                                        <!--
                                        <div class="row line_br">
                                            <div class="col-sm-4">
                                                <div class="row" style="margin-bottom: 2px;">
                                                    <div class="col-sm-1"><div class="icheckbox_minimal-blue checked" aria-checked="false" aria-disabled="false" style="position: relative;">
                                                            <input type="checkbox" class="calendar_filter" value="0" checked="" style="position: absolute; opacity: 1;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div></div></div>
                                            </div>
                                            <div class="col-sm-8"><p style="color:purple">Countrys holidays</p>
                                                <small></small>
                                            </div>
                                        </div>-->





                                    </div>




                                    <!-- --->
								</div>



								<div class="col-sm-8 ">
                                    <!-- --->
                                    <div class="container-fluid">



                                    <div class="row line_br" >
                                        <div class="col-sm-5" >
                                            <button class="btn btn-xs" style=""><?php echo trans('main.today'); ?></button>
                                            <button class="btn btn-xs" style=""><?php echo trans('main.month'); ?></button><br>
                                            <button class="btn btn-xs" style="margin-top: 8px;"><</button>
                                            <button class="btn btn-xs" style="margin-top: 8px;">></button>

                                            <button class="btn btn-xs" style="margin-top: 8px;" ><?php echo trans('main.day'); ?></button>
                                        </div>
                                        <div class="col-sm-7 ">
                                            <p><?php echo trans('main.Dateperiodnavigation'); ?></p>
                                            <small></small>
                                            </span>
                                        </div>
                                    </div>


                                        <div class="row line_br" >
                                            <div class="col-sm-5">
                                                <a class="fc-day-grid-event fc-h-event fc-event fc-start fc-end fc-draggable" style="background-color:#3a87ad;width: 120px;"><div class="fc-content"><span class="fc-time"><!--10a--></span> <span class="fc-title">	Pending status </span></div></a>
                                            </div>



                                        </div>

                                        <div class="row line_br" >
                                            <div class="col-sm-5">
                                                <a class="fc-day-grid-event fc-h-event fc-event fc-start fc-end fc-draggable" style="background-color:#d73925;width: 120px;"><div class="fc-content"><span class="fc-time"><!--6p--></span> <span class="fc-title">	Abort status </span></div></a>
                                            </div>



                                        </div>


                                        <div class="row bottom">


                                            <div class="col-sm-12" >
                                                <small >
                                                    <i class="fa fa-info-circle"></i>
                                                    <?php echo trans('main.YoucanclickontheTasktoeditit'); ?></small>
                                            </div>
                                        </div>

                                    </div>

                                    <!-- --->
								</div>

								

							</div>



                        </div>
        </div>
<style>
      .color-box {
      float: left;
      width: 20px;
      height: 20px;
      margin: 5px;
      border: 1px solid rgba(0, 0, 0, .2);
      }
      
</style><?php /**PATH /var/www/html/resources/views/legend/task_calendar_legend.blade.php ENDPATH**/ ?>