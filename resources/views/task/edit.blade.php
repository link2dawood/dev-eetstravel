@extends('scaffold-interface.layouts.app')
@section('title','Edit')
@section('content')
    @include('layouts.title',
   ['title' => 'Tour Task', 'sub_title' => 'Tour Task Edit',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Tour Tasks', 'icon' => 'tasks', 'route' => route('task.index')],
   ['title' => 'Edit', 'route' => null]]])
    @php
        $tab = '' ;
        $uri_parts = explode('?', \Request::fullUrl() );
        if(count($uri_parts)>1){
           $tab_parts = explode('=', $uri_parts[1]);
           if($tab_parts[0] == 'tab') $tab = $uri_parts[1];
        }
    @endphp
    <section class="content">
        <div class="box box-primary">
            <div class="box box-body border_top_none">
                <form method='POST' action='{!! url("task")!!}/{!!$task->id!!}/update'>
                    <input type="hidden" id="tab" name="tab" value="{{ $tab }}" >
                    <div class="row">
                        <div class="col-md-12">
                            <div class="margin_button">
                                <a href="javascript:history.back()">
                                    <button class='btn btn-primary back_btn' type="button">{!!trans('main.Back')!!}</button>
                                </a>
                                <button class='btn btn-success' type='submit'>{!!trans('main.Save')!!}</button>
                            </div>
                        </div>
                    </div>

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

                    <input type='hidden' name='_token' value='{{Session::token()}}'>
                    <div class="tab-content">
                        <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="content">{!!trans('main.Content')!!}</label>
                                        <input id="content" name="content" type="text" class="form-control" value="{{ $errors != null && count($errors) > 0 ? '' : $task->content }}{{ old('content') }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="tours">{!!trans('main.Tour')!!}</label>
                                        <select name="tour" id="tours" class="form-control">
                                            <option value="">Without Tour</option>
                                            @foreach($tours as $tour)
                                                <option {{ $task->tour_id == $tour->id ? 'selected' : '' }} value="{{ $tour->id }}">{{ $tour->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group col-md-6 col-lg-6" style="padding-left: 0;">

                                        <label for="departure_date">{!!trans('main.Deadline')!!}</label>

                                        <div class="input-group date">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            {!! Form::text('end_date', $task->end_date, ['class' => 'form-control pull-right datepicker', 'id' => 'start_date']) !!}
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6 col-lg-6" style="padding-right: 0">

                                        <label for="departure_date">{!!trans('main.Time')!!}</label>

                                        <div class="input-group date">
                                            <div class="input-group-addon">
                                                <i class="fa fa-clock-o"></i>
                                            </div>
                                            {!! Form::text('end_time', $task->end_time, ['class' => 'form-control pull-right timepicker', 'id' => 'end_time']) !!}
                                        </div>

                                    </div>

                                    {{-- <div class="form-group">
                                        <label for="assign">{!!trans('main.Assignto')!!}</label>
                                        <select name="assign" id="assign">
                                            @foreach($users as $user)
                                                <option {{ $task->assign == $user->name ? 'selected' : '' }} value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div> --}}
                                    <div class="form-group" >
										<label for="assigned_user">{!! trans('main.AssignedUser') !!} *</label>
										<div class ="form-control" style="max-height:100px !important;overflow-x:auto;height: auto; ">
										@foreach ($users as $user)
										<p>{{$user->name }}
										<input type="checkbox" name="assigned_user" id="assigned_user" value="{{ $user->id }}" {{$user->selected ? 'checked' : ''}}></p>

										@endforeach
										</div>
									</div>
                                    <div class="form-group">
                                        <label for="task_type">{!!trans('main.Tasktype')!!}</label>
                                        {!! Form::select('task_type', \App\Task::$taskTypes, $task->task_type, ['class' => 'form-control']) !!}
                                    </div>
                                    <div class="form-group">
                                        <label for="status">{!!trans('main.Status')!!}</label>
                                        <select name="status" id="status" class="form-control">
                                            @foreach($statuses as $status)
                                                <option value="{{ $status->id }}" {{ $errors != null && count($errors) > 0 ? (old('status') == $status->id ? 'selected' : '') : ($task->status == $status->id ? 'selected' : '') }}>{{ $status->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('priority', 1, $task->priority) !!}
                                            Priority
                                        </label>
                                    </div>

                                    <div class="form-group">
                                        <label for="attach">{!!trans('main.Files')!!}</label>
                                        @component('component.file_upload_field')@endcomponent
                                    </div>
                                    <input type="text" hidden name="calendar_edit" value="{{ $calendar_edit }}">
                                </div>
                                <div class="col-md-8">
                                    <div class="tour-packages"></div>

                                    <div class="row">
                                        @component('component.files', ['files' => $files])@endcomponent
                                    </div>
                                </div>
                            </div>
                        <div id="itinerary" class="tab-pane fade">

                        </div>
                        <button class='btn btn-success' type='submit'>{!!trans('main.Save')!!}</button>
                        <a href="{{\App\Helper\AdminHelper::getBackButton(route('task.index'))}}">
                            <button class='btn btn-warning' type='button'>{!!trans('main.Cancel')!!}</button>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection