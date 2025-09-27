<div class="panel">
    <div class="panel-body">
        <table class="table table-striped table-bordered table-hover" style='background:#fff; width: 99%; table-layout: fixed'>
            <thead>
                <tr>
                    <th style='width: 30px!important;'>ID</th>
                    <th>{!!trans('main.Name')!!}</th>
                    <th>{!!trans('main.DepDate')!!}</th>
                    <th>{!!trans('main.RetDate')!!}</th>
                    <th>{!!trans('main.CountryBegin')!!}</th>
                    <th>{!!trans('main.CityBegin')!!}</th>
                    <th>{!!trans('main.Status')!!}</th>
                    <th>{!!trans('main.ExternalName')!!}</th>
                    <th style="width:150px!important; text-align: center;">{!!trans('main.Actions')!!}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tours as $tour)
                    <tr>
                        <td>{{ $tour->id }}</td>
                        <td class="touredit-name">{{ $tour->name }}</td>
                        <td class="touredit-departure_date">{{ $tour->departure_date }}</td>
                        <td class="touredit-retirement_date">{{ $tour->retirement_date }}</td>
                        <td class="touredit-country_begin">{{ $tour->country_begin }}</td>
                        <td class="touredit-city_begin">{{ $tour->city_begin }}</td>
                        <td class="touredit-status">{!! $tour->status_display !!}</td>
                        <td class="touredit-status">{{ $tour->external_name }}</td>
                        <td style="text-align: center;">{!! $tour->action_buttons !!}</td>
                    </tr>
                @endforeach
                @if(empty($tours))
                    <tr>
                        <td colspan="9" class="text-center">No tours found</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
{{-- <button class='btn btn-default btn-sm' data-toggle='modal' data-target='#tour-clone-modal'><i class='fa fa-plus'></i></button> --}}
<div class="modal fade" id="tour-clone-modal" tabindex="-1" role='dialog' aria-labelledby='tour-clone-label'>
    <div class="modal-dialog" role='document'>
        <div class="modal-content">
            <div class="modal-body">
                <form id="tour-clone-modal-form">
                    <div class="form-group">
                        <label for="departure_date">{!!trans('main.DepartureDate')!!}</label>
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            {!! Form::text('departure_date', '', ['class' => 'form-control pull-right datepicker', 'id' => 'departure_date']) !!}
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success">{!!trans('main.Submit')!!}</button>
                </form>
            </div>
        </div>
    </div>
</div>
<span id="permission" data-permission="{{ \App\Helper\PermissionHelper::checkPermission('tour.edit') }}"></span>
<style>

</style>
