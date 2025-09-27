@extends('scaffold-interface.layouts.app')
@section('title','Index')
@section('content')
    @include('layouts.title',
        ['title' => 'Activities', 'sub_title' => 'Activities List',
        'breadcrumbs' => [
        ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
        ['title' => 'Activities', 'icon' => null, 'route' => null]]])
<section class="content">
  <div class="box box-primary">
    <div class="box-body">
      <table id="activity-table" class="table table-striped table-bordered table-hover" style='background:#fff'>
        <thead>
        <tr>
          <th>{!!trans('main.CreatedTime')!!}</th>
          <th>{!!trans('main.UserCreated')!!}</th>
          {{-- <th>Action</th> --}}
          <th>{!!trans('main.Description')!!}</th>
          <th class="actions-button"></th>
        </tr>
        </thead>
        <tbody>
        @foreach($activitiesData as $activity)
          <tr>
            <td>{{ $activity->created_at }}</td>
            <td>{{ $activity->causer }}</td>
            <td>{{ $activity->formatted_description }}</td>
            <td>{!! $activity->link_button !!}</td>
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
  <script>
      $(document).ready(function() {
          let table = $('#activity-table').DataTable({
              dom: "<'row'<'col-sm-5'l><'col-sm-2'B><'col-sm-5'f>>" +
              "<'row'<'col-sm-12'tr>>" +
              "<'row'<'col-sm-5'i><'col-sm-7'p>>",
              buttons: [
                  {
                      extend: 'csv',
                      title: 'Activities List',
                      exportOptions: {
                          columns: ':not(.actions-button)'
                      }
                  },
                  {
                      extend: 'excel',
                      title: 'Activities List',
                      exportOptions: {
                          columns: ':not(.actions-button)'
                      }
                  },
                  {
                      extend: 'pdfHtml5',
                      title: 'Activities List',
                      exportOptions: {
                          columns: ':not(.actions-button)'
                      }
                  }
              ],
              language : {
                  search: "Global Search :"
              },
              pageLength: 50,
              columnDefs: [
                  { targets: [3], orderable: false } // Actions/Link column not sortable
              ]
          });
          $('#activity-table tfoot th').each(function() {
              let column = this;
              if(column.className !== 'not') {
                  let title = $(this).text();
                  $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');
              }
          });
          table.columns().every(function() {
              let that = this;

              $('input', this.footer()).on('keyup change', function() {
                  if(that.search() !== this.value) {
                      that.search(this.value).draw();
                  }
              });
          });
          $('#activity-table tfoot th').appendTo('#activity-table thead');
      });
  </script>
@endpush
