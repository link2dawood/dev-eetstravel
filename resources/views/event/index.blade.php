@extends('scaffold-interface.layouts.app')
@section('title','Index')
@section('content')
    @include('layouts.title',
   ['title' => 'Events', 'sub_title' => 'Events List',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Events', 'icon' => 'map-signs', 'route' => null]]])
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                @if (Session::has('message'))
                    <div class="alert alert-danger"><center>{{ Session::get('message') }}</center></div>
                @endif
                    <div>
                        {!! \App\Helper\PermissionHelper::getCreateButton(route('event.create'), \App\Event::class) !!}
                    </div>
                @if(session('export_all'))
                <div class="alert alert-info col-md-12" style="text-align: center;">
                    {{session('export_all')}}
                </div>
                @endif
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" id="events-search" class="form-control" placeholder="Search events..." onkeyup="filterTable('events-table', this.value)">
                        </div>
                        <div class="col-md-6 text-right">
                            <button class="btn btn-success btn-sm" onclick="exportTableToCSV('events-table', 'events_export.csv')">
                                <i class="fa fa-download"></i> Export CSV
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="events-table" class="table table-striped table-bordered table-hover bootstrap-table" style='background:#fff'>
                        <thead>
                        <tr>
                            <th onclick="sortTable(0, 'events-table')">ID <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(1, 'events-table')">{!!trans('main.Name')!!} <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(2, 'events-table')">{!!trans('main.Address')!!} <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(3, 'events-table')">{!!trans('main.Country')!!} <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(4, 'events-table')">{!!trans('main.City')!!} <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(5, 'events-table')">{!!trans('main.WorkPhone')!!} <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(6, 'events-table')">{!!trans('main.ContactEmail')!!} <i class="fa fa-sort"></i></th>
                            <th class="actions-button" style="width: 140px">{!!trans('main.actions')!!}</th>
                        </tr>
                        </thead>
                        <tbody>
                            @forelse($events as $event)
                            <tr>
                                <td>{{ $event->id }}</td>
                                <td>{{ $event->name }}</td>
                                <td>{{ $event->address }}</td>
                                <td>{{ $event->country_name ?? '' }}</td>
                                <td>{{ $event->city_name ?? '' }}</td>
                                <td>{{ $event->work_phone }}</td>
                                <td>{{ $event->contact_email }}</td>
                                <td>
                                    @include('component.action_buttons', [
                                        'show_route' => route('event.show', $event->id),
                                        'edit_route' => route('event.edit', $event->id),
                                        'delete_route' => route('event.destroy', $event->id),
                                        'model' => $event
                                    ])
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center">No events found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        {{ $events->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script src="{{ asset('js/bootstrap-tables.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        initializeBootstrapTable('events-table');
    });
</script>
@endpush
