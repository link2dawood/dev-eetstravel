@extends('scaffold-interface.layouts.app')
@section('title','Index')
@section('content')
    @include('layouts.title', [
        'title' => 'Announcements',
        'sub_title' => 'Announcements List',
        'breadcrumbs' => [
            ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
            ['title' => 'Announcements', 'icon' => 'coffee', 'route' => null]
        ]
    ])
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                {!! \App\Helper\PermissionHelper::getCreateButton(route('announcements.create'), \App\Announcement::class) !!}
                <br/><br/>
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" id="announcements-search" class="form-control" placeholder="Search announcements..." onkeyup="filterTable('announcements-table', this.value)">
                        </div>
                        <div class="col-md-6 text-right">
                            <button class="btn btn-success btn-sm" onclick="exportTableToCSV('announcements-table', 'announcements_export.csv')">
                                <i class="fa fa-download"></i> Export CSV
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="announcements-table" class="table table-striped table-bordered table-hover" style="background:#fff">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>{!! trans('main.Title') !!}</th>
                                <th>{!! trans('main.Content') !!}</th>
                                <th>{!! trans('main.Time') !!}</th>
                                <th>{!! trans('main.Sender') !!}</th>
                                <th>{!! trans('main.Files') !!}</th>
                                <th class="actions-button" style="width: 140px">{!! trans('main.Actions') !!}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($announcements as $announcement)
                            <tr>
                                <td>{{ $announcement->id }}</td>
                                <td>{{ $announcement->title }}</td>
                                <td>{{ Str::limit($announcement->content, 100) }}</td>
                                <td>{{ $announcement->created_at ? $announcement->created_at->format('Y-m-d H:i') : '' }}</td>
                                <td>{{ $announcement->sender->name ?? '' }}</td>
                                <td>
                                    @if($announcement->files && $announcement->files->count() > 0)
                                        @foreach($announcement->files as $file)
                                            <a href="{{ $file->url }}" target="_blank">{{ $file->name }}</a><br>
                                        @endforeach
                                    @else
                                        <span class="text-muted">No files</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @include('component.action_buttons', ['item' => $announcement, 'routePrefix' => 'announcements'])
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if(method_exists($announcements, 'links'))
                    {{ $announcements->links() }}
                @endif
            </div>
        </div>
    </section>
@endsection