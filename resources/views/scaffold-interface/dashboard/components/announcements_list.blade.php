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
        
        <table id="announcement-table" class="table table-striped table-hover" style='background:#fff'>
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
                @php
                    $showRoute = route('announcements.show', ['announcement' => $announcement->id]);
                @endphp
                <tr>
                    <td>
                        <a href="{{ $showRoute }}" class="text-reset text-decoration-none d-block">
                            {{ $announcement->id }}
                        </a>
                    </td>
                    <td data-delete-label>
                        <a href="{{ $showRoute }}" class="text-reset text-decoration-none d-block fw-semibold">
                            {{ $announcement->title }}
                        </a>
                    </td>
                    <td>
                        <a href="{{ $showRoute }}" class="text-reset text-decoration-none d-block">
                            {{ \Illuminate\Support\Str::limit($announcement->content, 50) }}
                        </a>
                    </td>
                    <td>
                        <a href="{{ $showRoute }}" class="text-reset text-decoration-none d-block">
                            {{ $announcement->created_at->format('Y-m-d') }}
                        </a>
                    </td>
                    <td>
                        <a href="{{ $showRoute }}" class="text-reset text-decoration-none d-block">
                            {{ $announcement->sender }}
                        </a>
                    </td>
                    <td>
                        @include('component.action_buttons', ['item' => $announcement, 'routePrefix' => 'announcements'])
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="box-footer clearfix">
            @if(Auth::user()->can('announcements.create'))
            <a href="{{route('announcements.create')}}" class="btn btn-primary">
                <i class="fa fa-plus fa-md" aria-hidden="true"></i> New Announcement
            </a>
            @endif
            @if(Auth::user()->can('announcements.index'))
            <a href="{{route('announcements.index')}}" class="btn btn-outline-secondary float-end">
                View All Announcements
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

@include('component.delete_modal_simple')