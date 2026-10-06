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
            <div class="nav-item dropdown d-none d-md-flex me-3 tabler-notifications-dropdown">
                <a href="#" class="nav-link px-0" data-bs-toggle="dropdown" data-bs-auto-close="outside" tabindex="-1" aria-label="Show notifications">
                    <i class="ti ti-bell icon"></i>
                    @auth
                        @if($unreadNotificationsCount)
                            <span class="badge bg-red tabler-notifications-badge">{{ $unreadNotificationsCount }}</span>
                        @endif
                    @endauth
                </a>
                <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card">
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
                        <div class="list-group list-group-flush list-group-hoverable tabler-notifications-list">
                            @auth
                                @forelse($notifications->take(8) as $notification)
                                    <div class="list-group-item tabler-notification-item {{ !$notification->click ? 'bg-blue-lt' : '' }}"
                                         data-unread="{{ !$notification->click ? '1' : '0' }}">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <span class="status-dot {{ !$notification->click ? 'status-dot-animated bg-red' : 'bg-muted' }} d-block"></span>
                                            </div>
                                            <div class="col text-truncate">
                                                <a href="{{ $notification->link ? url($notification->link) . '?notification_click=' . $notification->id : '#' }}"
                                                   class="d-block text-reset text-decoration-none">
                                                    <div class="d-block text-body text-truncate">{{ $notification->content }}</div>
                                                    @if($notification->created_at)
                                                        <div class="text-muted mt-1">
                                                            <small>{{ $notification->created_at->diffForHumans() }}</small>
                                                        </div>
                                                    @endif
                                                </a>
                                            </div>
                                            <div class="col-auto">
                                                <button type="button" class="delete-notification-task text-muted" data-notif-id="{{ $notification->id }}" title="Delete notification" onclick="return window.deleteTablerNotification ? window.deleteTablerNotification(event, this) : false;">
                                                    <i class="ti ti-x"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="list-group-item tabler-notifications-empty">
                                        <div class="text-muted">You don't have notifications</div>
                                    </div>
                                @endforelse
                            @endauth
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

.tabler-notifications-dropdown .dropdown-menu-card {
    width: 320px;
    max-width: calc(100vw - 24px);
}

.tabler-notifications-dropdown .card {
    width: 100%;
}

.tabler-notifications-dropdown .card-header {
    padding: 0.75rem 1rem;
}

.tabler-notifications-dropdown .card-title {
    font-size: 0.95rem;
}

.tabler-notifications-list {
    max-height: 260px;
    overflow-y: auto;
}

.tabler-notifications-list .list-group-item {
    padding: 0.65rem 0.85rem;
}

.tabler-notifications-dropdown .card-footer {
    padding: 0.65rem 0.85rem;
    gap: 0.75rem;
}

.delete-notification-task {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    padding: 0;
    border: 0;
    border-radius: 6px;
    background: transparent;
    cursor: pointer;
}

.delete-notification-task:hover,
.delete-notification-task:focus {
    background: #fee2e2;
    color: #dc2626 !important;
    outline: none;
}
</style>
<script>
document.addEventListener('click', function (event) {
    const button = event.target.closest('.tabler-notifications-dropdown .delete-notification-task');
    if (!button) {
        return;
    }

    event.preventDefault();
    event.stopPropagation();
    event.stopImmediatePropagation();

    if (button.dataset.deleting === '1') {
        return false;
    }
    button.dataset.deleting = '1';

    const item = button.closest('.tabler-notification-item');
    const list = document.querySelector('.tabler-notifications-list');

    if (item) {
        item.remove();
    }

    if (item) {
        const badges = document.querySelectorAll('.tabler-notifications-badge');
        const current = parseInt((badges[0] && badges[0].textContent) || '0', 10) || 0;
        const next = Math.max(0, current - 1);

        badges.forEach(function (badge) {
            if (next > 0) {
                badge.textContent = next;
            } else {
                badge.remove();
            }
        });

        if (next === 0) {
            const readAll = document.getElementById('read_all_notification');
            if (readAll) {
                readAll.classList.add('disabled-link');
            }
        }
    }

    if (list && !list.querySelector('.tabler-notification-item')) {
        list.innerHTML = "<div class='list-group-item tabler-notifications-empty'><div class='text-muted'>You don't have notifications</div></div>";
        const deleteAll = document.querySelector('.tabler-delete-all-notifications');
        const readAll = document.getElementById('read_all_notification');
        if (deleteAll) {
            deleteAll.classList.add('disabled-link');
        }
        if (readAll) {
            readAll.classList.add('disabled-link');
        }
    }

    const token = document.querySelector('meta[name="csrf-token"]');
    const body = new FormData();
    body.append('_token', token ? token.content : '');
    body.append('id', button.dataset.notifId || '');

    fetch('/delete_notifications', {
        method: 'POST',
        body: body,
        credentials: 'same-origin',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    }).catch(function () {});

    return false;
}, true);
</script>
@endpush



