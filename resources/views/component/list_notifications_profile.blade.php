<div class="panel">
    <div class="panel-body">
        <table class="table table-striped table-bordered table-hover" style='background:#fff;width: 100%;'>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>{!!trans('main.Content')!!}</th>
                    <th style="width: 140px!important">{!!trans('main.Actions')!!}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($notifications as $notification)
                    <tr>
                        <td>{{ $notification->id }}</td>
                        <td>{{ $notification->content }}</td>
                        <td>{!! $notification->action_buttons !!}</td>
                    </tr>
                @endforeach
                @if($notifications->isEmpty())
                    <tr>
                        <td colspan="3" class="text-center">No notifications found</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>


