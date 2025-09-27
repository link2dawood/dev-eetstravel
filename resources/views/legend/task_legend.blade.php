      <div id="legend_help" style=" z-index:9999; position: absolute;top:-20px;width:350px; right: -100%; background-color: rgb(255, 255, 255);opacity: 0;">

                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-sm-12"><h2>{!!trans('main.Tasks')!!}</h2><hr></div>
                            </div>

                            <div class="row top" >
                                <div class="col-sm-5" >
                                    <button class="btn btn-success btn-xs" type="button"><i class="fa fa-plus fa-md" aria-hidden="true"></i> {!!trans('main.New')!!}</button>
                                </div>
                                <div class="col-sm-7"><p>{!!trans('main.AddTaskandconfigure')!!}</p>
                                        <small></small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-5" >
                                    <i class='fa fa-info-circle legend-media' style="background-color: #f29b1a;"></i>
                                </div>
                                <div class="col-sm-7"><p>{!!trans('main.ShowTaskinfo')!!}</p>
                                    <small></small>
                                </div>
                            </div>


                            <div class="row" >
                                <div class="col-sm-5">
                                    <i class='fa fa-pencil-square-o legend-media' style="background-color: #3c8cbb;"></i>
                                </div>
                                <div class="col-sm-7"><p>{!!trans('main.EditTaskparameters')!!}</p>
                                    <small></small>
                                </div>
                            </div>



                            <div class="row" >
                                <div class="col-sm-5">
                                    <i class='fa fa-trash-o legend-media' style="background-color: #dc4a39;"></i>
                                </div>
                                <div class="col-sm-7"><p>{!!trans('main.Confirmremovalof')!!}</p>
                                    <small></small>
                                </div>
                            </div>

                            <div class="row line_br" >
                                <div class="col-sm-5" >
                                    <select id="test_select2"  >
                                        <option selected="selected" value="2">{!!trans('main.Pending')!!}</option>
                                    </select>
                                </div>
                                <div class="col-sm-7"><p>{!!trans('main.ChangecurrentTaskstatus')!!}</p>
                                    <small></small>
                                </div>
                            </div>

                            <div class="row " >
                                <div class="col-sm-5">
                                    <input type="text" style="width:70px;" disabled placeholder="Search text">
                                </div>
                                <div class="col-sm-7"><p>{!!trans('main.Searchamongall')!!}</p>
                                    <small> </small>
                                </div>
                            </div>

                            <div class="row " >
                                <div class="col-sm-5">
                                    <i class='legend-media' style="background: rgb(255, 187, 178);"></i>
                                </div>
                                <div class="col-sm-7"><p>{!!trans('main.Taskinhighpriorityorder')!!}</p>
                                    <small></small>
                                </div>
                            </div>

                            <div class="row " >
                                <div class="col-sm-5">
                                    <button class="btn btn-xs centered" type="button" style="margin-left: 16px;">CSV</button>
                                </div>
                                <div class="col-sm-7"><p>{!!trans('main.ExportTaskslistCSV')!!}</p>
                                    <small></small>
                                </div>
                            </div>

                            <div class="row " >
                                <div class="col-sm-5">
                                    <button class="btn btn-xs centered" type="button" style="margin-left: 12px;">Excel</button>
                                </div>
                                <div class="col-sm-7"><p>{!!trans('main.ExportTaskslistexcel')!!}</p>
                                    <small></small>
                                </div>
                            </div>

                            <div class="row bottom" >
                                <div class="col-sm-5">
                                    <button class="btn btn-xs centered" type="button" style="margin-left: 16px;">PDF</button>
                                </div>
                                <div class="col-sm-7"><p>{!!trans('main.ExportTaskslistPDF')!!}</p>
                                    <small></small>
                                </div>
                            </div>
                        </div>
        </div>