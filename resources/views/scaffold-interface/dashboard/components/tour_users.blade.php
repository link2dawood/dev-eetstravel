<div class="col-md-6">
    <div class="box box-primary ">
        <div class="box-header">
            <h4>{{ trans('main.Userswithtours') }}</h4>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
            </div>
        </div>
        <div class="box-body">
            <table class="table table-striped table-hover" style='background:#fff'>
                <thead>
                <th>User</th>
                <th>Tours Count</th>
                </thead>
                <tbody>
                @foreach($tourUsers as $tourUser)
                    <tr>
                        <td>
                            {!! $tourUser['name'] !!}
                        </td>
                        <td>
                            <h4><span class="label label-success pull-left">{!! $tourUser['count'] !!}</span></h4>

                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>