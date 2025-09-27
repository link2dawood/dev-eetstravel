@extends('scaffold-interface.layouts.app')
@section('title','Create')
@section('content')
    @include('layouts.title',
       ['title' => 'Tour Package', 'sub_title' => 'Tour Package Create',
       'breadcrumbs' => [
       ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
       ['title' => 'Tours', 'icon' => 'suitcase', 'route' => route('tour.index')],
       ['title' => 'Tour Edit', 'icon' => 'suitcase', 'route' => url('/tour/'.$tour_package->tour.'/edit')],
       ['title' => 'Create', 'route' => null]]])
    <section class="content">
        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="tour-package-services">
            @component('component.packages_service', ['tour_package' => $tour_package, 'serviceTypes' => $serviceTypes, 'servicesData' => $servicesData])@endcomponent
        </div>
        <form method='POST' action='{!!url("tour_package")!!}' id="tour_package_create_form">
            <input type='hidden' name='_token' value='{{Session::token()}}'>

            <!--Main info box -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class=""> {!!trans('main.Service')!!}:
                        <span id="service_name" style="text-transform: capitalize;">{!! isset($serviceName) ? $serviceName : false!!}</span>
                    </h3>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        {!! Form::label('name', 'Name') !!}
                        {!! Form::text('name', '', ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('description', 'Description') !!}
                        {!! Form::text('description', '', ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        <label for="status">{!!trans('main.Status')!!}</label>
                        <select name="status" id="status" class="form-control">
                            @foreach($statuses as $status)
                                <option {{ old('status') == $status->id ? 'selected' : '' }} value="{{ $status->id }}">{{ $status->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        {!! Form::label('paid', 'Paid') !!}
                        {!! Form::checkbox('paid', 1, '') !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('pax', 'Pax') !!}
                        {!! Form::text('pax', $tour->pax, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('pax_free', 'Pax Free') !!}
                        {!! Form::text('pax_free', $tour->pax_free, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('total_amount', 'Total amount') !!}
                        {!! Form::text('total_amount', 0, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        <label for="currency">{!!trans('main.Currency')!!}</label>
                        <select name="currency" id="currency" class="form-control">
                            @foreach($currencies as $currency)
                                <option {{ old('currency') == $currency->id ? 'selected' : '' }} value="{{ $currency->id }}">{{ $currency->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-6 col-lg-6" style="padding-left: 0">
                        <label for="departure_date">{!!trans('main.DateFrom')!!}</label>
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            {!! Form::text('from_date', '' , ['class' => 'form-control pull-right datepicker', 'id' => 'from_date']) !!}
                        </div>
                    </div>

                    <div class="form-group col-md-6 col-lg-6" style="padding-right: 0">
                        <label for="departure_date">{!!trans('main.TimeFrom')!!}</label>
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-clock-o"></i>
                            </div>
                            {!! Form::text('from_time', '12:00', ['class' => 'form-control pull-right timepicker', 'id' => 'from_time']) !!}
                        </div>
                    </div>

                    <div class="form-group col-md-6 col-lg-6" style="padding-left: 0">
                        <label for="departure_date">{!!trans('main.DateTo')!!}</label>
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            {!! Form::text('to_date', '', ['class' => 'form-control pull-right datepicker', 'id' => 'to_date']) !!}
                        </div>
                    </div>

                    <div class="form-group col-md-6 col-lg-6" style="padding-right: 0">
                        <label for="departure_date">{!!trans('main.TimeTo')!!}</label>
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-clock-o"></i>
                            </div>
                            {!! Form::text('to_time', '13:00', ['class' => 'form-control pull-right timepicker', 'id' => 'to_time']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('rate', 'Rate') !!}
                        {!! Form::text('rate', '', ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('note', 'Note') !!}
                        {!! Form::textarea('note', '', ['class' => 'form-control']) !!}
                    </div>
                        {!! Form::hidden('serviceType', $selectedServiceType, ['id' => 'tour_package_service_type_value']) !!} {{-- назва типу з селекта --}}
                        {!! Form::hidden('serviceId', $selectedServiceId, ['id' => 'tour_package_service_type_id']) !!} {{-- id сервіса який обраний --}}
                </div>
            </div>

            {!! Form::hidden('tourDayId', $id, ['id' => 'tour_package_tour_day_id']) !!}

            {{-- {!! Form::hidden('servicePage', '', ['id' => 'tour_package_service_page']) !!} --}}
            <button class='btn btn-primary' type='submit'>{!!trans('main.Create')!!}</button>
        </form>
    </section>
@endsection