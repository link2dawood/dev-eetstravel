<!--  TOURS CALENDAR  -->
<div class="col-md-12">
    <div class="box box-primary">
        @if(Auth::user()->can('dashboard.tours_calendar'))
        <div class="box-header">
            <h4>{{ trans('main.ToursCalendar') }}</h4>
            <div class="box-tools pull-right">

                <div class="form-inline">
                        @if(!empty($user->roles))
                            @foreach($user->roles as $role)
                                @if($role->name === 'admin')
                                <div class="form-group" >
                                    <label>{{ trans('main.Toursby') }}</label>
                                </div>
                                <div class="form-group gant_tours_users_group">
                                <select name="gant_tours_users" id="gant_tours_users" class="form-control" onchange="javascript:selectDataset();" >
                                        @foreach(\App\User::all() as $user)
                                            <option value="{{$user->id}}" {{ (Auth::user()->id === $user->id) ? 'selected="selected"' : ''}} >{{$user->name}} </option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif
                            @endforeach
                        @endif

                        @if(Auth::user()->can('tour.create'))
                        <div class="form-group">
                            <button type="button"
                                    class="btn btn-box-tool c-addTourButton-button fc-button fc-state-default fc-corner-left fc-corner-right"
                                    onClick="openTourModal();" style="height: 2.3em;" >
                                {{ trans('main.Addtour') }}
                            </button>
                       </div>
                        @endif

                    <div class="form-group">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>

                </div>

            </div>
        </div>
        <div class="box-body">
            <div id="chartdiv" class="chartdiv" style="width: 104%; height: 700px; position: relative; top:-45px;"></div>
        </div>
        @else
            <div class="box-header">
                <h4>{{ trans('main.ToursCalendar') }}</h4>
            </div>
            <div class="box-body">
                {{ trans('main.Youdonthavepermissions') }}
            </div>
        @endif
    </div>
</div>

<style>
    .gant_tours_users_group .select2-container {
        width: 12em !important;
        /*
        position: relative;
        z-index: 2;
        float: left;
        width: 12em;
        margin-bottom: 0;
        display: table;
        table-layout: fixed;*/
    }
</style>