@auth
    @php
        $messages = \App\Helper\DashboardHelper::getCountUnreadMailMessage();
        $tasks = \App\Helper\DashboardHelper::getTasks();
    @endphp
@endauth

<!-- Page header -->
<header class="navbar navbar-expand-md navbar-light d-print-none">
    <div class="container-xl">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu" aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
            <a href="{{url('home')}}">
                TMS
            </a>
        </h1>
        <div class="navbar-nav flex-row order-md-last">
            <!-- Notifications -->
            <div class="nav-item dropdown d-none d-md-flex me-3">
                <a href="#" class="nav-link px-0 notifications-content" data-bs-toggle="dropdown" tabindex="-1" aria-label="Show notifications">
                    <i class="ti ti-bell icon"></i>
                    <span class="badge bg-red"></span>
                </a>
                <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Notifications</h3>
                        </div>
                        <div class="list-group list-group-flush list-group-hoverable">
                            <!-- Notifications will be loaded here dynamically -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Messages -->
            @auth
            <div class="nav-item dropdown d-none d-md-flex me-3">
                <a href="#" class="nav-link px-0" data-bs-toggle="dropdown" tabindex="-1" aria-label="Show messages">
                    <i class="ti ti-mail icon"></i>
                    @if($messages)
                        <span class="badge bg-red">{{ $messages }}</span>
                    @endif
                </a>
                <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Messages</h3>
                        </div>
                        <div class="list-group list-group-flush list-group-hoverable list_notification_email">
                            <a href="{{route('email.index')}}" class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col text-truncate">
                                        <div class="d-block text-body">{{ trans('main.Viewall') }}</div>
                                    </div>
                                </div>
                            </a>
                            <a href="{{route('email.readAll')}}" class="list-group-item {{ !$messages ? 'disabled-link' : '' }}">
                                <div class="row align-items-center">
                                    <div class="col text-truncate">
                                        <div class="d-block text-body">{{ trans('main.Readall') }}</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tasks -->
            <div class="nav-item dropdown d-none d-md-flex me-3">
                <a href="#" class="nav-link px-0" data-bs-toggle="dropdown" tabindex="-1" aria-label="Show tasks">
                    <i class="ti ti-checkbox icon"></i>
                    @if($tasks)
                        <span class="badge bg-red">{{ count($tasks) }}</span>
                    @endif
                </a>
                <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ trans('main.Youhave') }} {{ $tasks ? count($tasks) : 0 }} {{ trans('main.tasks') }}</h3>
                        </div>
                        <div class="list-group list-group-flush list-group-hoverable">
                            @foreach($tasks as $task)
                            <a href="{!! route('task.show', ['task' => $task->id]) !!}" class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <span class="avatar">
                                            <i class="ti ti-users"></i>
                                        </span>
                                    </div>
                                    <div class="col text-truncate">
                                        <div class="d-block text-body">{!! $task->tourNameNotification() !!}</div>
                                        <div class="text-muted mt-1">
                                            <small>{!! $task->dead_line !!}</small>
                                        </div>
                                    </div>
                                </div>
                            </a>
                            @endforeach
                        </div>
                        <div class="card-footer text-center">
                            <a href="/profile/?tab=history-tasks-tab" class="btn btn-link">{{ trans('main.Viewtasks') }}</a>
                        </div>
                    </div>
                </div>
            </div>
            @endauth

            <!-- User Profile -->
            @auth
            <div class="nav-item dropdown">
                <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
                    <span class="avatar avatar-sm" style="background-image: url({{ Auth::user()->avatar ? asset(Auth::user()->avatar) : asset('img/avatar.png') }})"></span>
                    <div class="d-none d-xl-block ps-2">
                        <div>{{ Auth::user()->name }}</div>
                        <div class="mt-1 small text-muted">{{ Auth::user()->email }}</div>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <a href="{{ url('profile') }}" class="dropdown-item">
                        <i class="ti ti-user icon dropdown-item-icon"></i>
                        Profile
                    </a>
                    <a href="{{ url('profile/edit') }}" class="dropdown-item">
                        <i class="ti ti-settings icon dropdown-item-icon"></i>
                        Settings
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="{{ route('logout') }}" class="dropdown-item"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="ti ti-logout icon dropdown-item-icon"></i>
                        Logout
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </div>
            @endauth
        </div>
    </div>
</header>

@push('scripts')
<style>
.disabled-link {
    pointer-events: none;
    opacity: 0.5;
}
</style>
@endpush