@extends('scaffold-interface.layouts.app')
@section('title','Show')
@section('content')
    @include('layouts.title',
   ['title' => 'Profile', 'sub_title' => $user->name,
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Show', 'route' => null]]])
    {{-- modal for service table --}}
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs" role='tablist'>
                        <li role='presentation' class="active">
                            <a href="#info_profile" aria-controls='info_profile' role='tab' data-toggle='tab'>
                                {{trans('main.Info')}}
                            </a>
                        </li>
                        <li role='presentation'>
                            <a href="#timeline" aria-controls='timeline' role='tab' data-toggle='tab'>History</a>
                        </li>
                        @if(Auth::user()->can('task.index'))
                            <li role='presentation'>
                                <a href="#history_tasks_tab" aria-controls='history_tasks_tab' role='tab'
                                   data-toggle='tab' id="history-tasks-tab">
                                    {{trans('main.Tasks')}}
                                </a>
                            </li>
                        @endif
                        @if(Auth::user()->can('tour.index'))
                            <li role='presentation'>
                                <a href="#history_tours_tab" aria-controls='history_tours_tab' role='tab'
                                   data-toggle='tab' id="history-tours-tab">
                                    {{trans('main.Tours')}}
                                </a>
                            </li>
                        @endif
                        <li role='presentation'>
                            <a href="#notifications" aria-controls='notifications_tab' role='tab' data-toggle='tab' id="notifications-tab" >
                                {{trans('main.Notifications')}}
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade in active" id="info_profile">
                            <div class="block_info_profile" style="overflow: hidden; display: block">
                                <div class="col-lg-4 col-md-4">
                                    <!-- Profile Image -->
                                    <div class="box box-primary without-border">
                                        <div class="box-body box-profile">
                                            <img class="profile-user-img img-responsive img-circle"
                                                 src="{{ $user->avatar ? asset($user->avatar) : asset('img/avatar.png') }}" alt="User profile picture"
                                                 style="text-align: center;">
                                            <h3 class="profile-username text-center">{{ $user->name }}</h3>

                                            <p class="text-muted text-center">{{ $user->email }}</p>
                                        </div>
                                        <!-- /.box-body -->
                                    </div>
                                    <!-- /.box -->

                                    <div class="box box-primary without-border">
                                        <div class="box-body">
                                            <strong>
                                                <i class="fa fa-book margin-r-5"></i>{{trans('main.Education')}}
                                            </strong>
                                            <p class="text-muted">{{$user->education}}</p>
                                            <hr style="margin-top: 20px; margin-bottom: 20px;">
                                            <strong>
                                                <i class="fa fa-map-marker margin-r-5"></i>{{trans('main.Location')}}
                                            </strong>
                                            <p class="text-muted">{{$user->location}}</p>
                                            <hr style="margin-top: 20px; margin-bottom: 20px;">
                                            <strong>
                                                <i class="fa fa-file-text-o margin-r-5"></i>{{trans('main.Notes')}}
                                            </strong>
                                            <p class="text-muted">{{$user->note}}</p>
                                            <strong>
                                                {{trans('main.Updateemails')}} :
                                            </strong>
                                            <div class="row">
                                                <div class="col-md-3 col-xs-8">
                                                    <input type="text" class="form-control" id="time_period">
                                                </div>
                                                <div class="col-md-4">
                                                    <select name="update" id="period_type">
                                                        <option value="D">{{trans('main.Days')}}</option>
                                                        <option value="H">{{trans('main.Hours')}}</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4 col-xs-4">
                                                    <button type="button" class="btn btn-success" id="period_submit">{{trans('main.Go')}}</button>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="alert alert-info alert-dismissible" id="alert-message" style="display: none; margin-top: 20px">
                                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                                        <i class="icon fa fa-info"></i>
                                                        {{trans('main.Theprocessisrunning')}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="box-footer">
                                            <a href="{{url('logout')}}" id="logout" class="btn btn-default btn-flat"
                                               onclick="event.preventDefault();
											logoutForm()">{{trans('main.Signout')}}</a>
                                            <form id="logout-form" action="{{ url('/logout') }}" method="POST"
                                                  style="display: none;">
                                                {{ csrf_field() }}
                                            </form>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-lg-8 col-md-8">
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

                                    @if (session('incorrect_data'))
                                        <div class="alert alert-danger" style="text-align: center">
                                            {{ session('incorrect_data') }}
                                        </div>
                                    @endif

                                    <form class="form-horizontal" action="{{url('/users/'.$user->id)}}" method="post"
                                          enctype="multipart/form-data">
                                        {!! csrf_field() !!}

                                        <input type="hidden" name="user_id" value="{{$user->id}}">

                                        <div class="form-group">
                                            <label for="email" class="col-sm-2 control-label">{{trans('main.Email')}}</label>

                                            <div class="col-sm-10">
                                                <input type="email" name="email"
                                                       value="{{ $errors != null && count($errors) > 0 ? old('email') : $user->email }}"
                                                       class="form-control" id="email" placeholder="Email" required>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="name" class="col-sm-2 control-label">{{trans('main.Name')}}</label>

                                            <div class="col-sm-10">
                                                <input type="text" name="name"
                                                       value="{{ $errors != null && count($errors) > 0 ? old('name') : $user->name }}"
                                                       class="form-control" id="name" placeholder="Name" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="password" class="col-sm-2 control-label">{{trans('main.Password')}}</label>
                                            <div class="col-sm-10">
                                                <input type="password" name="password" class="form-control"
                                                       placeholder="password">
                                            </div>
                                        </div>
										{{--
                                        <div class="form-group form-inline">

                                            <label for="email_server" class="col-sm-2 control-label">{{trans('main.Server')}}</label>

                                            <div class="col-sm-10">
                                                {!! Form::select(
                                                'email_server',
                                                 [0 => 'Select', 1 => 'Emailsrvr', 2=> 'Outlook.cn'],
                                                  $user->email_server,
                                                   ['id' => 'email_server', 'class' => 'form-control'] ) !!}
                                            </div>
                                        </div>
										--}}
										{{--
                                        <div class="form-group">
                                            <label for="email_login" class="col-sm-2 control-label">{{trans('main.EmailLogin')}}</label>
                                            <div class="col-sm-10">
                                                <input type="text" name="email_login"
                                                       value="{{ $errors != null && count($errors) > 0 ? old('email_login') : $user->email_login }}"
                                                       class="form-control" id="email_login" placeholder="Email Login">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="email_password" class="col-sm-2 control-label">{{trans('main.Email')}}
                                                Password</label>
                                            <div class="col-sm-10">
                                                <input type="password" name="email_password"
                                                       class="form-control" id="email_password"
                                                       placeholder="Email Password">
                                            </div>
                                        </div>--}}
                                        {{-- <div class="form-group">
                                            <label for="password" class="col-sm-2 control-label">{{trans('main.Password')}}</label>

                                            <div class="col-sm-10">
                                                <input type="password" name="password" class="form-control" id="password" placeholder="Password" required>
                                            </div>
                                        </div> --}}
                                        <div class="form-group">
                                            <label for="education" class="col-sm-2 control-label">{{trans('main.Education')}}</label>
                                            <div class="col-sm-10">
                                                <input type="text" name="education"
                                                       value="{{ $errors != null && count($errors) > 0 ? old('education') : $user->education }}"
                                                       class="form-control" placeholder="Education">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="location" class="col-sm-2 control-label">{{trans('main.Location')}}</label>
                                            <div class="col-sm-10">
                                                <input type="text" name="location"
                                                       value="{{ $errors != null && count($errors) > 0 ? old('location') : $user->location }}"
                                                       class="form-control" placeholder="Location">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="note" class="col-sm-2 control-label">{{trans('main.Note')}}</label>
                                            <div class="col-sm-10">
                                                <input type="text" name="note"
                                                       value="{{ $errors != null && count($errors) > 0 ? old('note') : $user->note }}"
                                                       class="form-control" placeholder="Note">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="password" class="col-sm-2 control-label">{{trans('main.Image')}}</label>

                                            <div class="col-sm-10">
                                                <input id="avatar" name="avatar" type="file" class="file"
                                                       data-show-upload="false">
                                            </div>
                                        </div>
                                        <input type="text" hidden name="edit_profile" value="1">

                                        <div class="form-group">
                                            <div class="col-sm-offset-2 col-sm-10">
                                                <button type="submit" class="btn btn-danger">{{trans('main.Submit')}}</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div role='tabpanel' class="tab-pane fade" id="history_tasks_tab">
                            @include('component.list_tasks_for_profile', ['userName' => $user->name, 'userId' => $user->id, 'tasks' => $tasks])
                        </div>

                        <div role='tabpanel' class="tab-pane fade" id="history_tours_tab">
                            @include('component.list_tours_for_profile', ['userName' => $user->name, 'userId' => $user->id, 'tours' => $tours])
                        </div>

                        <div class="tab-pane fade in" id="timeline">
                            <ul class="timeline timeline-inverse">
                                @foreach($activities as $activity)
                                    <li class="time-label">
                                        <span class="bg-green">{{$activity->updated_at->format('d-m-Y')}}</span>
                                    </li>
                                    <li>
                                        <i class="fa fa-user bg-aqua"></i>
                                        <div class="timeline-item">
                                                <span class="time">
                                                    <i class="fa fa-clock-o"></i>
                                                    {{$activity->updated_at->format('H:i')}}
                                                </span>
                                            <h3 class="timeline-header">{{$activity->log_name}}</h3>
                                            <div class="timeline-body">{{$activity->description}}</div>
                                            @if($activity->getExtraProperty('link'))
                                                <div class="timeline-footer">
                                                    <a href="{{$activity->getExtraProperty('link')}}"
                                                       class="btn btn-primary btn-xs">{{trans('main.Seemore')}}</a>
                                                </div>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                            {{$activities->links()}}
                        </div>

                        <div role='tabpanel' class="tab-pane fade" id="notifications">
                            @include('component.list_notifications_profile', ['userName' => $user->name, 'userId' => $user->id, 'notifications' => $notifications])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('post_scripts')
    <script src="{{asset('js/profile.js')}}"></script>
@endsection