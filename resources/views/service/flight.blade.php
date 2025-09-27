<div class="box box-primary">
    <div class="box-body">
        <section class="content">
            <div class="row">
                <div class="col-sm-4">
                    {!! Form::label('type', 'Service Type') !!}
                    {!! Form::select('type', $serviceTypes, $serviceType, ['class' => 'form-control', 'id' => 'tour-package-service-type']) !!}
                </div>
                <div class="col-sm-4">
                </div>
            </div>
            <div class="row">
            </div>
            <br>
            <br>
            <table class = "table table-striped table-bordered table-hover" style = 'background:#fff'>
                <thead>
                <th>ID</th>
                <th>{!!trans('main.From')!!}</th>
                <th>{!!trans('main.To')!!}</th>
                <th>{!!trans('main.DateTo')!!}</th>
                <th>{!!trans('main.DateFrom')!!}</th>
                <th>{!!trans('main.Select')!!}</th>
                </thead>
                <tbody>
                @foreach($services as $service)
                   @if($from =  \App\Helper\CitiesHelper::getCountryById($service->country_from)['name'] . ' - '. \App\Helper\CitiesHelper::getCityById($service->city_from)['name'])
                   @endif
                   @if($to =  \App\Helper\CitiesHelper::getCountryById($service->country_to)['name'] . ' - '. \App\Helper\CitiesHelper::getCityById($service->city_to)['name'])
                   @endif
                    <tr>
                        <td>{!! $service->id!!}</td>
                        <td>{!! $from!!}</td>
                        <td>{!! $to!!}</td>
                        <td>{!! $service->date_from!!}</td>
                        <td>{!! $service->date_to!!}</td>
                        <td>{!! Form::button('select', [
                                    'type' => 'button',
                                    'class' => 'tour_package_select_button',
                                    'data-type' => $filterType,
                                    'data-id' => $service->id,
                                    'data-name' => $from . ' - '. $to]) !!}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div id="tour-package-form-service-pagination">
                @include('layouts.pagination', ['paginator' => $services])
            </div>
        </section>

    </div>
</div>