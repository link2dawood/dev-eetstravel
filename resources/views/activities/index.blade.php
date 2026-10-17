@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Activities')
@section('content')
    @include('layouts.title',
        ['title' => 'Activities', 'sub_title' => 'Activities List',
        'breadcrumbs' => [
        ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
        ['title' => 'Activities', 'icon' => null, 'route' => null]]])
<section class="content">
  <div class="box box-primary">
    <div class="box-body">
      <div class="d-flex flex-wrap align-items-end gap-2 mb-3">
        <div><label for="activity-search" class="form-label">Global search</label><input id="activity-search" class="form-control" type="search"></div>
        <div><label for="activity-page-size" class="form-label">Rows per page</label><select id="activity-page-size" class="form-select"><option>10</option><option>25</option><option selected>50</option><option>100</option></select></div>
        <button type="button" class="btn btn-outline-secondary" data-activity-export="csv">Export CSV</button>
        <button type="button" class="btn btn-outline-secondary" data-activity-export="xlsx">Export Excel</button>
        <button type="button" class="btn btn-outline-secondary" data-activity-export="pdf">Export PDF</button>
      </div>
      <p class="text-muted">Exports include all matching records across pages.</p>
      <div id="activity-export-error" class="alert alert-danger" role="alert" hidden></div>
      <table id="activity-table" class="table table-striped table-bordered table-hover" style='background:#fff'>
        <thead>
        <tr>
          <th>{!!trans('main.CreatedTime')!!}</th>
          <th>{!!trans('main.UserCreated')!!}</th>
          {{-- <th>Action</th> --}}
          <th>{!!trans('main.Description')!!}</th>
          <th class="actions-button" style="width: 140px">{!!trans('main.Actions')!!}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($activitiesData as $activity)
          <tr>
            <td>{{ $activity->created_at }}</td>
            <td>{{ $activity->causer }}</td>
            <td data-delete-label>{{ $activity->formatted_description }}</td>
            <td>
              <div class="btn-list flex-nowrap">
                @if(!empty($activity->link_button))
                  {!! $activity->link_button !!}
                @else
                  <span class="text-muted">-</span>
                @endif
              </div>
            </td>
          </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr>
          <th>{!!trans('main.CreatedTime')!!}</th>
          <th>{!!trans('main.UserCreated')!!}</th>
          {{-- <th>Action</th> --}}
          <th>{!!trans('main.Description')!!}</th>
          <th class="not"></th>
        </tr>
        </tfoot>
      </table>
    </div>
  </div>
</section>
@endsection
@push('scripts')
<script src="{{ asset('js/lib/plugins/export/libs/jszip/jszip.min.js') }}"></script>
<script src="{{ asset('js/lib/plugins/export/libs/FileSaver.js/FileSaver.min.js') }}"></script>
<script src="{{ asset('js/lib/plugins/export/libs/xlsx/xlsx.min.js') }}"></script>
<script src="{{ asset('js/lib/plugins/export/libs/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('js/lib/plugins/export/libs/pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ asset('js/activities-table.js') }}?v={{ filemtime(public_path('js/activities-table.js')) }}"></script>
@endpush
