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
      <p class="text-muted">{{ auth()->user()->hasRole('admin') ? 'All users’ activities' : 'Your activities' }}</p>
      <form method="GET" action="{{ route('activities_index') }}" class="d-flex flex-wrap align-items-end gap-2 mb-3">
        <div><label for="activity-search" class="form-label">Description or user</label><input id="activity-search" name="q" value="{{ request('q') }}" class="form-control" type="search"></div>
        <div><label for="activity-user" class="form-label">User ID</label><input id="activity-user" name="user" value="{{ request('user') }}" class="form-control" type="number" min="1"></div>
        <div><label for="activity-module" class="form-label">Module</label><input id="activity-module" name="module" value="{{ request('module') }}" class="form-control" placeholder="e.g. Tour"></div>
        <div><label for="activity-from" class="form-label">From</label><input id="activity-from" name="from" value="{{ request('from') }}" class="form-control" type="date"></div>
        <div><label for="activity-to" class="form-label">To</label><input id="activity-to" name="to" value="{{ request('to') }}" class="form-control" type="date"></div>
        <div><label for="activity-page-size" class="form-label">Rows per page</label><select id="activity-page-size" name="per_page" class="form-select">@foreach([10,25,50,100] as $size)<option value="{{ $size }}" {{ (int)request('per_page', 50) === $size ? 'selected' : '' }}>{{ $size }}</option>@endforeach</select></div>
        <button class="btn btn-primary" type="submit">Filter</button><a class="btn btn-secondary" href="{{ route('activities_index') }}">Clear</a>
      </form>
      @if($errors->any())<div class="alert alert-danger" role="alert">@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>@endif
      <div class="d-flex gap-2 mb-3">
        <button type="button" class="btn btn-outline-secondary" data-activity-export="csv">Export CSV</button>
        <button type="button" class="btn btn-outline-secondary" data-activity-export="xlsx">Export Excel</button>
        <button type="button" class="btn btn-outline-secondary" data-activity-export="pdf">Export PDF</button>
      </div>
      <p class="text-muted">{{ $activitiesData->total() }} matching activities. Exports include this page only.</p>
      <div id="activity-export-error" class="alert alert-danger" role="alert" hidden></div>
      <table id="activity-table" data-server-paginated="true" class="table table-striped table-bordered table-hover" style='background:#fff'>
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
        @forelse($activitiesData as $activity)
          <tr>
            <td>{{ $activity->created_at }}</td>
            <td>{{ $activity->causer ?? 'System / deleted user' }}</td>
            <td data-delete-label>{{ $activity->formatted_description }}</td>
            <td>
              <div class="btn-list flex-nowrap">
                @if($activity->safe_link)
                  <a class="btn btn-sm btn-warning" href="{{ $activity->safe_link }}" title="View activity record">View</a>
                @else
                  <span class="text-muted">-</span>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr class="table-empty"><td colspan="4" class="text-muted">No activities found</td></tr>
        @endforelse
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
      {{ $activitiesData->links('pagination::bootstrap-4') }}
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
