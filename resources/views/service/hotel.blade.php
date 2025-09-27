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
                <div class="col-sm-4">
                    <div class="bootstrap-search">
                        <label>Search:
                            {!! Form::text('search', $search, ['class' => 'form-control input-sm', 'id' => 'tour-package-search']) !!}
                        </label>
                    </div>

                </div>
            </div>
            <div class="row">
            </div>
            <br>
            <br>
            <table class = "table table-striped table-bordered table-hover" style = 'background:#fff'>
                <thead>
                <th>ID</th>
                <th>{!!trans('main.Name')!!}</th>
                <th>{!!trans('main.Country')!!}</th>
                <th>{!!trans('main.City')!!}</th>
                <th>{!!trans('main.Address')!!}</th>
                <th>{!!trans('main.Select')!!}</th>
                </thead>
                <tbody>
                @foreach($services as $service)
                    <tr>
                        <td>{!! $service->id !!}</td>
                        <td>{!! $service->name !!}</td>
                        <td>
                            {!! \App\Helper\CitiesHelper::getCountryById($service->country)['name']!!}
                        </td>
                        <td>
                            {!! \App\Helper\CitiesHelper::getCityById($service->city)['name']!!}
                        </td>
                        <td>{!! $service->address_first !!}</td>
                        <td>{!! Form::button('select', [
                                    'type' => 'button',
                                    'class' => 'tour_package_select_button',
                                    'data-type' => $filterType,
                                    'data-id' => $service->id,
                                    'data-name' => $service->name]) !!}</td>
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