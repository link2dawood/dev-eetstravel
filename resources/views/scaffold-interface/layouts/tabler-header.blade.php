@auth
    @php
        $user = Auth::user();
        $messages = \App\Helper\DashboardHelper::getCountUnreadMailMessage();
        $tasks = \App\Helper\DashboardHelper::getTasks();
        $notifications = $user->notifications()->latest()->get();
        $unreadNotifications = $notifications->where('click', false);
        $unreadNotificationsCount = $unreadNotifications->count();
    @endphp
@endauth

<!-- Page header -->
<header class="navbar navbar-expand-md navbar-light d-print-none tms-header">
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
            <div class="nav-item dropdown d-none d-md-flex me-3 notifications-content" data-notifications-layout="tabler">
                <a href="#" class="nav-link px-0" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" aria-label="Show notifications">
                    <i class="ti ti-bell icon"></i>
                    @auth
                        @if($unreadNotificationsCount)
                            <span class="badge bg-red tabler-notifications-badge">{{ $unreadNotificationsCount }}</span>
                        @endif
                    @endauth
                </a>
                <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card notification-dropdown">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                Notifications
                                @auth
                                    @if($unreadNotificationsCount)
                                        <span class="badge bg-red ms-2 tabler-notifications-badge">{{ $unreadNotificationsCount }}</span>
                                    @endif
                                @endauth
                            </h3>
                        </div>
                        <div class="list-group list-group-flush list-group-hoverable" data-notifications-list>
                            <div class="list-group-item text-muted">Loading notifications...</div>
                        </div>
                        @auth
                            <div class="card-footer d-flex justify-content-between">
                                <a href="/profile?tab=notifications-tab" class="btn btn-link p-0">{{ trans('main.Viewall') }}</a>
                                <a href="#" id="read_all_notification" class="btn btn-link p-0 {{ !$unreadNotificationsCount ? 'disabled-link' : '' }}">{{ trans('main.Readall') }}</a>
                                <a href="#" id="delete_all_notification" class="btn btn-link p-0 tabler-delete-all-notifications {{ !$notifications->count() ? 'disabled-link' : '' }}">{{ trans('main.Deleteall') }}</a>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>

            <!-- Messages -->
            @auth
            <div class="nav-item dropdown d-none d-md-flex me-3">
                <a href="#" class="nav-link px-0" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Show messages">
                    <i class="ti ti-mail icon"></i>
                    @if($messages)
                        <span class="badge bg-red">{{ $messages }}</span>
                    @endif
                </a>
                <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card header-dropdown">
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
                <a href="#" class="nav-link px-0" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Show tasks">
                    <i class="ti ti-checkbox icon"></i>
                    @if($tasks)
                        <span class="badge bg-red">{{ count($tasks) }}</span>
                    @endif
                </a>
                <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card header-dropdown">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ trans('main.Youhave') }} {{ $tasks ? count($tasks) : 0 }} {{ trans('main.tasks') }}</h3>
                        </div>
                        <div class="list-group list-group-flush list-group-hoverable">
                            @foreach($tasks as $task)
                            <a href="{!! route('task.show', ['id' => $task->id]) !!}" class="list-group-item">
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
            <div class="nav-item dropdown account-dropdown">
                <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Open user menu">
                    <span class="avatar avatar-sm account-avatar" aria-hidden="true">
                        <i class="ti ti-user icon"></i>
                        @if(Auth::user()->avatar_path || Auth::user()->avatar)
                            <img src="{{ Auth::user()->avatar_path ? Auth::user()->avatar_url : asset(Auth::user()->avatar) }}" alt="" onerror="this.remove()">
                        @endif
                    </span>
                    <div class="d-none d-xl-block ps-2">
                        <div>{{ Auth::user()->name }}</div>
                        <div class="mt-1 small text-muted">{{ Auth::user()->email }}</div>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow header-dropdown account-menu">
                    <a href="{{ url('profile') }}" class="dropdown-item">
                        <i class="ti ti-user icon dropdown-item-icon"></i>
                        Profile
                    </a>
                    <a href="{{ url('profile/edit') }}" class="dropdown-item">
                        <i class="ti ti-settings icon dropdown-item-icon"></i>
                        Settings
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="{{ route('logout') }}" class="dropdown-item account-logout"
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
.tms-header {
    background: #ffffff;
    border-bottom: 1px solid #e5e7eb;
    padding-top: 12px;
    padding-bottom: 12px;
}
.tms-header .navbar-nav {
    align-items: center;
    gap: .5rem;
}
.tms-header .navbar-nav > .nav-item {
    margin-right: 0 !important;
}
.tms-header .navbar-nav > .nav-item > .nav-link {
    position: relative;
    min-width: 44px;
    min-height: 44px;
    justify-content: center;
    border-radius: 12px;
    background: #f3f4f6;
    border: 1px solid #e5e7eb;
    color: #334155 !important;
    transition: background .15s, border-color .15s;
}
.tms-header .navbar-nav > .nav-item > .nav-link:hover,
.tms-header .navbar-nav > .nav-item > .nav-link[aria-expanded="true"] {
    background: #eff6ff;
    border-color: #93c5fd;
    color: #1d4ed8 !important;
}
.tms-header .nav-link .icon {
    color: inherit;
}
.tms-header .nav-link > .badge:not(:empty) {
    position: absolute;
    top: -4px;
    right: -4px;
    padding: 3px 5px;
    min-width: 20px;
    border: 2px solid #ffffff;
    border-radius: 10px;
    background: #b91c1c !important;
    color: #ffffff;
    font-size: 10px;
    line-height: 1.2;
}
.tms-header .nav-link > .badge:empty {
    display: none;
}
.tms-header .account-dropdown > .nav-link {
    padding: 5px 10px !important;
    margin-left: .5rem;
}
.tms-header .account-dropdown .avatar {
    background-color: #dbeafe;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
}
.tms-header .account-avatar {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    color: #1d4ed8;
}
.tms-header .account-avatar .icon {
    color: #1d4ed8;
    font-size: 20px;
}
.tms-header .account-avatar img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.tms-header .header-dropdown {
    width: min(24rem, calc(100vw - 2rem));
    padding: 0;
    background: #ffffff;
    color: #1f2937;
    border: 1px solid #d1d5db;
    border-radius: 12px;
    box-shadow: 0 12px 32px rgba(15, 23, 42, .14);
    overflow: hidden;
}
.tms-header .header-dropdown .card {
    margin: 0;
    border: 0;
    box-shadow: none;
}
.tms-header .header-dropdown .card,
.tms-header .header-dropdown .card-header,
.tms-header .header-dropdown .list-group-item {
    background: #ffffff;
    color: #1f2937;
    border-color: #e5e7eb;
}
.tms-header .header-dropdown .card-title,
.tms-header .header-dropdown .text-body {
    color: #1f2937 !important;
}
.tms-header .text-muted {
    color: #4b5563 !important;
}
.tms-header .header-dropdown .list-group {
    max-height: 22rem;
    overflow-y: auto;
}
.tms-header .header-dropdown .list-group-item:hover,
.tms-header .account-menu .dropdown-item:hover {
    background: #eff6ff;
    color: #1d4ed8;
}
.tms-header .header-dropdown .avatar {
    background: #eff6ff;
    color: #1d4ed8;
}
.tms-header .header-dropdown .card-footer {
    background: #f9fafb;
    border-color: #e5e7eb;
}
.tms-header .header-dropdown .btn-link {
    color: #1d4ed8;
}
.tms-header .account-menu {
    width: 15rem;
    padding: .5rem;
}
.tms-header .account-menu .dropdown-item {
    padding: .65rem .75rem;
    border-radius: 8px;
    color: #334155;
}
.tms-header .account-menu .account-logout {
    color: #b91c1c;
}
.tms-header a:focus-visible {
    outline: 2px solid #1d4ed8;
    outline-offset: 2px;
}
.disabled-link {
    pointer-events: none;
    opacity: 0.5;
}
.notifications-content .notification-dropdown {
    width: min(24rem, calc(100vw - 2rem));
    background: #ffffff;
    color: #1f2937;
    border: 1px solid #d1d5db;
}
.notification-dropdown .card,
.notification-dropdown .card-header,
.notification-dropdown .list-group-item {
    background: #ffffff;
    color: #1f2937;
    border-color: #e5e7eb;
}
.notification-dropdown .card-title,
.notification-dropdown .notification-content-link {
    color: #1f2937;
}
.notification-dropdown .list-group-item:hover {
    background: #f3f4f6;
}
.notification-dropdown .text-muted {
    color: #4b5563 !important;
}
.notification-dropdown .card-footer {
    background: #f9fafb;
    border-color: #e5e7eb;
}
.notification-dropdown .btn-link {
    color: #1d4ed8;
}
.notification-dropdown .delete-notification-task,
.notification-dropdown #delete_all_notification {
    color: #b91c1c !important;
}
.notification-dropdown .delete-notification-task {
    flex-shrink: 0;
    background: #ffffff;
    border: 1px solid #e5e7eb;
}
.notification-dropdown .delete-notification-task:hover {
    background: #fee2e2;
    border-color: #b91c1c;
}
.notification-dropdown a:focus-visible,
.notification-dropdown button:focus-visible {
    outline: 2px solid #1d4ed8;
    outline-offset: 2px;
}
.notifications-content .notification-label {
    color: #ffffff;
    background: #b91c1c !important;
}
</style>
@endpush
