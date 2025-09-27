@extends('scaffold-interface.layouts.app')
@section('title','Create')
@section('content')
    @include('layouts.title',
   ['title' => 'Task', 'sub_title' => 'Task Create',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Tasks', 'icon' => 'tasks', 'route' => route('task.index')],
   ['title' => 'Create', 'route' => null]]])
    <section class="content">
        <div class="box box-primary">
            <div class="box box-body border_top_none">
                <form method='POST' action='{!!route("task.store")!!}' enctype="multipart/form-data">
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
                    <div class="row">
                        <div class="col-md-12">
                            <div class="margin_button">
                                <a href="javascript:history.back()">
                                    <button type="button" class='btn btn-primary back_btn'>{!!trans('main.Back')!!}</button>
                                </a>
                                <button class='btn btn-success' type='submit'>{!!trans('main.Save')!!}</button>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <input type='hidden' name='_token' value='{{Session::token()}}'>
                            <input type='hidden' name='modal_create' value="0">

                            <div class="form-group">
                                <label for="content">{!!trans('main.Content')!!}</label>
                                <textarea name="content" id="content" class="form-control" style="resize: none">{{ old('content') }}</textarea>
                            </div>
                    
                            <div class="form-group">
                                <label for="tours">{!!trans('main.Tour')!!}</label>
                                <select name="tour" id="tours" class="form-control">
                                    <option value="">{!!trans('main.WithoutTour')!!}</option>
                                    @foreach($tours as $tour)
                                        <option {{ $tour_default && $tour_default == $tour->id ? 'selected' : '' }} value="{{ $tour->id }}">{{ $tour->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="form-group col-md-6 col-lg-6" style="padding-left: 0">

                                <label for="departure_date">{!!trans('main.Deadline')!!}</label>

                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    {!! Form::text('end_date', Carbon\Carbon::now()->format('Y-m-d'), ['class' => 'form-control pull-right datepicker', 'id' => 'start_date']) !!}
                                </div>
                            </div>

                            <div class="form-group col-md-6 col-lg-6" style="padding-right: 0">

                                <label for="departure_date">{!!trans('main.Time')!!}</label>

                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-clock-o"></i>
                                    </div>
                                    {!! Form::text('end_time', '18:00', ['class' => 'form-control pull-right timepicker', 'id' => 'end_time']) !!}
                                </div>

                            </div>


                            <div class="form-group" >
                                    
								<label for="assigned_user">{!! trans('main.AssignedUser') !!} *</label>
								<div class ="form-control" style="max-height:200px !important;overflow-x:auto;height: auto; ">
									@foreach ($users as $user)
									<p>{{ $user->name }}
										<input type="checkbox" name="assigned_user" id="assigned_user" value="{{ $user->id }}" ></p>

									@endforeach
								</div>
                           </div>

                            <div class="form-group">
                                <label for="task_type">{!!trans('main.Tasktype')!!}</label>
                                {!! Form::select('task_type', \App\Task::$taskTypes, 0, ['class' => 'form-control']) !!}
                            </div>
                            <div class="form-group">
                                <label for="status">{!!trans('main.Status')!!}</label>
                                <select name="status" id="status" class="form-control">
                                    @foreach($statuses as $status)
                                        <option {{ old('status') == $status->id ? 'selected' : '' }} value="{{ $status->id }}">{{ $status->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="checkbox">
                                <label>
                                {!! Form::checkbox('priority') !!}
                                    {!!trans('main.Priority')!!}
                                </label>
                            </div>
                            <div class="form-group">
                                <label>{!!trans('main.Files')!!}</label>
                                @component('component.file_upload_field')@endcomponent
                            </div>
                            <input type="text" hidden name="redirect_tour" value="{{ $tour_default ? $tour_default : null }}">
                        </div>
                    </div>
                    <button class='btn btn-success' type='submit'>{!!trans('main.Save')!!}</button>
                </form>
            </div>
        </div>
    </section>
@endsection