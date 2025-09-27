<a href="#" class="dropdown-toggle" data-toggle="dropdown">
    <i class="fa fa-bell-o"></i>
    @if($user->checkNotification())
        <span class="label notification-label label-danger">{{$user->countNotification()}}</span>
    @endif
</a>
<ul class="dropdown-menu">
    <li class="header">{!!trans('main.Yournotifications')!!}</li>
    <li class="content-notification">
        <ul class="menu notifications-list-tasks">
            @foreach($notifications as $notification)
                
                    <li>
                     {{--   <a href="{{$notification->click ? '#' : $notification->link ? $notification->link : '#'}}"
                           class="notification" data-notif-id="{{$notification->id}}">--}}
						<a href="{{url('/notification/show')}}"
                           >
                            <i class="fa fa-users text-aqua"></i> {{$notification->content}}
                        </a>
                        <span class="delete-notification-task" data-notif-id="{{$notification->id}}">
													<i class="fa fa-times" aria-hidden="true"></i>
												</span>
                    </li>
               
            @endforeach
        </ul>
    </li>
    @if($user->countNotification())
    <ul class="list_notification">
        <li class="footer"><a href="/profile?tab=notifications-tab">{!!trans('main.Viewall')!!}</a></li>
        <li class="footer"><a href="#" id="read_all_notification">{!!trans('main.Readall')!!}</a></li>
        <li class="footer"><a href="#" id="delete_all_notification">{!!trans('main.Deleteall')!!}</a></li>
    </ul>
    @else
        <li class="footer"><a href="#">{!!trans('main.Youdonthavenotifications')!!}</a></li>
    @endif

</ul>