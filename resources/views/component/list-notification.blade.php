<div class="panel">
    <span id="getNameUser" data-info="{{ $userName }}"></span>
    <span id="userId" data-info="{{ $userId }}"></span>
    <div class="panel-body">
        <table class="table table-striped table-bordered table-hover" style='background:#fff;width: 100%;'>
            <thead>
            <tr>
                <th>ID</th>
                <th>{!!trans('main.Content')!!}</th>
                <th class="actions-button" style="width: 140px!important">{!!trans('main.Actions')!!}</th>
            </tr>
            </thead>
            <tbody>
                @forelse($notificationsData ?? [] as $notification)
                <tr>
                    <td>{{ $notification['id'] }}</td>
                    <td>{{ $notification['content'] ?? '' }}</td>
                    <td>{!! $notification['action_buttons'] ?? '' !!}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center">No notifications found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>


@push('scripts')
<script>
$(document).ready(function() {
    // No DataTable initialization needed - data is displayed directly
    // Optional: Add any notification-specific JavaScript functionality here
    console.log('Notifications loaded directly from controller');
});
</script>
@endpush