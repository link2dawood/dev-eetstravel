<div class="box box-primary">
    @if(Auth::user()->can('dashboard.announcements'))
    <div class="box-header">
        <h4>Announcements</h4>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
        </div>
    </div>
    <div class="box-body">
        <br>
        <table class="table table-striped table-hover" style='background:#fff'>
            <thead>
            <th>ID</th>
            <th>{{ trans('main.Title') }}</th>
            <th>{{ trans('main.Content') }}</th>
            <th>{{ trans('main.Date') }}</th>
            <th>{{ trans('main.Sender') }}</th>
            <th style="width: 140px">{{ trans('main.Actions') }}</th>
            </thead>
            <tbody>
            @foreach($announcements as $announcement)
                <tr>
                    <td>{{ $announcement->id }}</td>
                    <td><span style="font-size: 14px;font-weight: bold;">{{ $announcement->title }}</span></td>
                    <td>{{ $announcement->content }}</td>
                    <td>{{ $announcement->created_at }}</td>
                    <td>{{ $announcement->sender }}</td>
                    <td>{!! $announcement->action_buttons !!}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="box-footer clearfix">
            @if(Auth::user()->can('announcements.create'))
            <a href="{{route('announcements.create')}}">
                <button class="btn btn-primary pull-left" type="submit"><i class="fa fa-plus fa-md" aria-hidden="true"></i> New Announcement
                </button>
            </a>
            @endif
            @if(Auth::user()->can('announcements.index'))
            <a href="{{route('announcements.index')}}">
                <button href="javascript:void(0)" class="btn btn-default pull-right">View All Announcements
                </button>
            </a>
            @endif
        </div>
    </div>
    @else
        <div class="box-header">
            <h4>{{ trans('main.Announcements') }}</h4>
        </div>
        <div class="box-body">
            {{ trans('main.Youdonthavepermissions') }}
        </div>
    @endif
</div>
{{--    END Activities Table--}}
<!--  END Commentaries  -->

