@extends('scaffold-interface.layouts.app')
@section('title','Create')
@section('content')
    @include('layouts.title',
   ['title' => 'Tour Expense', 'sub_title' => 'Expense Create',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Clients', 'icon' => 'handshake-o', 'route' => route('clients.index')],
   ['title' => 'Create', 'route' => null]]])
    <section class="content">
        <div class="box box-primary">
            <div class="box box-body border_top_none">
                @if (count($errors) > 0)
                    <br>
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form method='POST' action='{{route('tour_expenses.store')}}' enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="margin_button">
                                <a href="javascript:history.back()">
                                    <button type="button" class='btn btn-primary back_btn'>{!!trans('main.Back')!!}</button>
                                </a>
                                <button class='btn btn-success' type='submit'>{!!trans('main.Edit')!!}</button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">

                                {{csrf_field()}}
								<input type = "hidden" name = "office_id" value = "{{$office->id}}">
								<div class="form-group">
									<label for="tours">{{ trans('main.Tour') }}</label>
									<select name="tour_name" id="tour_name" class="form-control">

										@foreach ($tours as $tour)
											<option value="{{ $tour->name }}">{{ $tour->name }}</option>
										@endforeach
									</select>
                            	</div>
                                <div class="form-group">
                                    <label for="Tour_Expense">{!!trans('Tour Expense')!!}</label>
                                    <input id="tour_expenses" name="tour_expenses" type="text" class="form-control" value="{{old('tour_expenses')}}">
                                </div>
                                
                                <div class="form-group">
                                    <label for="Departure_Date">{!!trans('Departure Date')!!}</label>
                                    <input id="tour_departure_date" name="tour_departure_date" type="date" class="form-control" value="{{old('tour_departure_date')}}">
                                </div>
                                <div class="form-group">
                                    <label for="Return_Date">{!!trans('Return Date')!!}</label>
                                    <input id="tour_return_date" name="tour_return_date" type="date" class="form-control" value="{{old('tour_return_date')}}">
                                </div>
                                
                                <button class='btn btn-success' type='submit'>{!!trans('main.Save')!!}</button>

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection