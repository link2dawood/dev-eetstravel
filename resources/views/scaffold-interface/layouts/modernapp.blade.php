<!DOCTYPE html>
<html lang="en">

<head>
    <title>@yield('title')</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-id" content="{{ Auth::getUser()->id }}">


    <!-- Bootstrap 3.3.7 -->
   
    <link rel="stylesheet" href="{{asset('css/fileinput.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/font-awesome-4.7.0/css/font-awesome.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/bootstrap-tables.css')}}" />

    <!-- Ionicons -->
    <link rel="stylesheet" href="{{asset('css/ionicons.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/magnific.css')}}">
    <link rel="stylesheet" href="{{asset('css/jquery.toast.css')}}">
    <link rel="stylesheet" href="{{asset('css/fullcalendar.css')}}" />

    <!-- Theme style -->
    <link rel="stylesheet" href="{{asset('css/adminlte-app.css')}}">
    <link rel="stylesheet" href="{{asset('css/style.css')}}">

    @yield('colorpicker-css')

    <link rel="stylesheet" href="{{asset('css/skins/_all-skins.min.css')}}">
    <link rel="stylesheet" href="/css/util.css">
    <link rel="stylesheet" href="/plugins/iCheck/all.css">
    <link rel="stylesheet" href="{{asset('css/plugins/export.css')}}" type="text/css" media="all" />

    <link rel="stylesheet" href="{{asset('assets/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/jquery-ui.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/slick.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/slick-theme.css')}}">
    <link rel="stylesheet" href="{{asset('assets/fonts/fontawesome-free-5.15.4-web/css/all.css')}}">
    <link rel="stylesheet" href="{{asset('assets/plugin/richtexteditor/rte_theme_default.css')}}">
    <link rel="stylesheet" href="{{asset('assets/scss/multiselect.css')}}">
    <link rel="stylesheet" href="{{asset('assets/scss/style.css')}}">

    {{--<script type="text/javascript" src="{{ asset('js/app.js') }}"></script>--}}

    <script type="text/javascript" src="{{asset('js/lib/jquery.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/lib/moment.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/lib/fullcalendar.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/lib/gcal.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/jquery.toast.js')}}"></script>
    <script src="{{asset('js/vue.js')}}"></script>
    <script src="{{asset('js/piexif.min.js')}}"></script>
    <script src="{{asset('js/purify.min.js')}}"></script>
    <script src="{{asset('js/fileinput.min.js')}}"></script>

    <!--[if lt IE 9]>
    <script src="{{asset('js/html5shiv.min.js')}}"></script>
    <script src="{{asset('js/respond.min.js')}}"></script>
    <![endif]-->


</head>

<body class="hold-transition skin-blue sidebar-mini sidebar-collapse">

    <audio src="/new_message.mp3" id="chat_message"></audio>
    <!--
<div class="loadingoverlay">
    <div class="loadingoverlay_fontawesome fa fa-spinner fa-spin"></div>
</div>-->

    <div class="wrapper">
        <header class="main-header">
            <a href="{{url('home')}}" class="logo">
                <span class="logo-mini"><b>TMS</b></span>
                <span class="logo-lg"><b>TMS</b></span>
            </a>


            <nav class="navbar navbar-static-top" style="height: 50px" disabled>
                <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button" disabled>
                    <span class="sr-only">{{ trans('main.Togglenavigation') }}</span>
                </a>
                <div class="navbar-custom-menu" disabled>
                    <ul class="nav navbar-nav d-none" disabled>
                        <li class="dropdown notifications-menu notifications-content">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-bell-o"></i>
                            </a>
                        </li>

                        @php
                        $messages = \App\Helper\DashboardHelper::getCountUnreadMailMessage();

                        // $tasks = \App\Helper\DashboardHelper::getTasks();
                        @endphp

                        <li class="dropdown messages-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-envelope-o"></i>
                                @if($messages)
                                <span class="label label-danger">{{ $messages }}</span>
                                @endif
                            </a>

                            <ul class="dropdown-menu">
                                <ul class="list_notification_email">
                                    <li class="footer">
                                        <a href="snappymail.eu">{{ trans('main.Viewall') }}</a>
                                    </li>
                                    <li class="footer">
                                        <a class="{{ !$messages ? 'disabled-link' : '' }}" href="{{route('email.readAll')}}">{{ trans('main.Readall') }}</a>
                                    </li>
                                </ul>
                            </ul>
                        </li>
                        <li class="dropdown tasks-menu">

                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-flag-o"></i>
                                <span class="label label-danger">{{(@$tasks) ? @$tasks->count : "" }}</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="header">{{ trans('main.Youhave') }} {{ (@$tasks) ? @$tasks->count : "" }} {{ trans('main.tasks') }}</li>
                                <li>
                                    <ul class="menu menuTasks">
                                        {{-- @foreach($tasks as $task)--}}
                                        <li>
                                            <a href="{{--{!! route('task.show', ['task' => $task->id]) !!}--}}" class="my_notif_tasks">
                                                <div class="image_tasks_notif">
                                                    <i class="fa fa-users text-aqua"></i>
                                                </div>
                                                <div class="desc_tasks_notif">
                                                    <span>{{--{!! $task->tourNameNotification() !!}--}}</span>
                                                    <small>{{--{!! $task->dead_line !!}--}</small>
                                                </div>
                                            </a>
                                        </li>
                                   {{--@endforeach--}}
                                    </ul>
                                </li>
                                <li class="footer">
                                    <a href="/profile/?tab=history-tasks-tab">{{ trans('main.Viewtasks') }}</a>
                                </li>
                            </ul>
                        </li>
                        <li class="user user-menu">
                            <a href="{{ url('profile') }}">
                                <img src="{{ Auth::user()->avatar ? asset(Auth::user()->avatar) : asset('img/avatar.png') }}" class="user-image" alt="User Image" style="text-align: center;">
                                <span class="hidden-xs">{{Auth::user()->name}}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

        </header>

        <aside class="main-sidebar fix-sidebar">
            @include('scaffold-interface.layouts.sidebar')
        </aside>

        <div class="content-wrapper">
            @include('component.session-messages')
            @yield('content')
        </div>
        <script type="text/javascript" src="{{asset('js/appi.js')}}"></script>
    </div>

    <div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel">
        <div class='AjaxisModal'>
        </div>
    </div>
    <!--
<div class="protect_loader hidden">
    <div class="loadingoverlay_fontawesome fa fa-spinner fa-spin" ></div>
</div>-->

    <!-- Bootstrap Tables -->

    <script type="text/javascript" src="{{asset('js/bootstrap-tables.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/app.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/lib/moment-with-locales.js')}}"></script>
    <link href="{{asset('css/select2.min.css')}}" rel="stylesheet" />
    <link href="{{asset('css/bootstrap-datetimepicker.min.css')}}" rel="stylesheet" />

    <script type="text/javascript" src="{{URL::asset('js/select2.min.js') }}"></script>
    <script>
        var baseURL = "{{ URL::to('/') }}"
    </script>
    <script type="text/javascript" src="{{URL::asset('js/AjaxisBootstrap.js') }}"></script>
    <script type="text/javascript" src="{{URL::asset('js/scaffold-interface-js/customA.js') }}"></script>
    <script type="text/javascript" src="{{asset('js/bootstrap-datetimepicker.min.js')}}"></script>
    @yield('colorpicker-js')

    <link href="{{asset('css/bootstrap-datepicker.min.css')}}" rel="stylesheet" type="text/css" />

    <script src="{{asset('js/bootstrap-datepicker.min.js')}}"></script>

    <script src="{{asset('js/script.js')}}"></script>

    <script src="{{asset('js/magnific.js')}}"></script>

    <script type="text/javascript" src="{{asset('js/lib/bootstrap.min.js')}}"></script>


    @if( strpos(Auth::user()->timezone, 'Asia') !== false )
    <script type="text/javascript" src="http://maps.google.cn/maps/api/js?region=cn&libraries=places&sensor=false&language=en&key=AIzaSyBV706sie0bSi4QCwu06KbvH3QBiTSNJzY"></script>
    @else
    <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?libraries=places&sensor=false&key=AIzaSyBV706sie0bSi4QCwu06KbvH3QBiTSNJzY&language=en"></script>
    @endif


    <script src="{{URL::asset('js/google_places.js')}}"></script>
    <script src="{{URL::asset('js/jquery.scrollTo.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/pusher.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/jquery.repeater.min.js')}}"></script>
    @stack('scripts')
    @yield('post_styles')
    @yield('tour_package_script')
    @yield('post_scripts')
    @yield('post_scripts_calendar')

    <script type="text/javascript" src="{{asset('js/tables.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/helper.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/onclick-events.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/notifications.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/ckeditor/ckeditor.js')}}"></script>
    {{--<script type="text/javascript" src="{{asset('plugins/iCheck/icheck.js')}}"></script>--}}

    <script src="{{asset('js/ckeditor.js')}}"></script>
    <script src="{{asset('js/icheck.min.js')}}"></script>
    <script>
        var user_email = "{{Auth::user()->email_login}}";
    </script>



    <script src="{{asset('assets/js/jquery-3.6.0.min.js')}}"></script>
    <script src="{{asset('assets/js/jquery-ui.min.js')}}"></script>
    <script src="{{asset('assets/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('assets/js/slick.min.js')}}"></script>
    <script src="{{asset('assets/plugin/richtexteditor/rte.js')}}"></script>
    <script src="{{asset('assets/plugin/richtexteditor/plugins/all_plugins.js')}}"></script>
    <script src="{{asset('assets/js/multiselect.js')}}"></script>
    <script src="{{asset('assets/js/custom.js')}}"></script>

    <script>
        var editor1 = new RichTextEditor("#div_editor1");
        var editor2 = new RichTextEditor("#div_editor2");
        var editor3 = new RichTextEditor("#div_editor3");
    </script>

</body>

</html>