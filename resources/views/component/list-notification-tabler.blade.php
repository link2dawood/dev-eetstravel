@php
    $unreadCount = $notifications->where('click', false)->count();
@endphp
<a href="#" class="nav-link px-0" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" aria-label="Show notifications">
    <i class="ti ti-bell icon"></i>
    @if($unreadCount)
        <span class="badge bg-red notification-label" aria-label="{{ $unreadCount }} unread notifications">{{ $unreadCount }}</span>
    @endif
</a>
<div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card notification-dropdown">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ trans('main.Yournotifications') }}</h3>
            <span class="ms-auto text-muted small">{{ $unreadCount }} unread</span>
        </div>
        <div class="list-group list-group-flush list-group-hoverable" data-notifications-list style="max-height: 22rem; overflow-y: auto;">
            @forelse($notifications as $notification)
                <div class="list-group-item d-flex align-items-start gap-2">
                    <a href="{{ url('/notification/show') }}" class="notification-content-link flex-fill" style="overflow-wrap: anywhere;">
                        {{ $notification->content }}
                    </a>
                    <button type="button" class="btn btn-sm delete-notification-task" data-notif-id="{{ $notification->id }}" aria-label="Delete notification">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
            @empty
                <div class="list-group-item text-muted">{{ trans('main.Youdonthavenotifications') }}</div>
            @endforelse
        </div>
        @if($notifications->isNotEmpty())
            <div class="card-footer d-flex flex-wrap gap-2">
                <a href="{{ url('/profile') }}?tab=notifications-tab" class="btn btn-sm btn-link">{{ trans('main.Viewall') }}</a>
                <a href="#" id="read_all_notification" class="btn btn-sm btn-link">{{ trans('main.Readall') }}</a>
                <a href="#" id="delete_all_notification" class="btn btn-sm btn-link text-danger">{{ trans('main.Deleteall') }}</a>
            </div>
        @endif
    </div>
</div>
