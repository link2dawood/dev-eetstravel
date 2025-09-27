@extends('scaffold-interface.layouts.app')
@section('title','Index')
@section('content')
    @include('layouts.title',
   ['title' => 'Quotation', 'sub_title' => 'Quotation List',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Quotation', 'icon' => 'list-ul', 'route' => null]]])
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <br>
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" id="quotation-search" class="form-control" placeholder="Search quotations..." onkeyup="filterTable('quotation-table', this.value)">
                        </div>
                        <div class="col-md-6 text-right">
                            <button class="btn btn-success btn-sm" onclick="exportTableToCSV('quotation-table', 'quotations_export.csv')">
                                <i class="fa fa-download"></i> Export CSV
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="quotation-table" class="table table-striped table-bordered table-hover bootstrap-table">
                        <thead>
                            <tr>
                                <th onclick="sortTable(0, 'quotation-table')">ID <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(1, 'quotation-table')">{!!trans('main.Name')!!} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(2, 'quotation-table')">{!!trans('main.Tour')!!} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(3, 'quotation-table')">{!!trans('main.Assigned')!!} <i class="fa fa-sort"></i></th>
                                <th onclick="sortTable(4, 'quotation-table')">{!!trans('main.CreatedAt')!!} <i class="fa fa-sort"></i></th>
                                <th>{!!trans('main.Frontsheet')!!}</th>
                                <th>{!!trans('main.Actions')!!}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="7" class="text-center">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection


@push('scripts')
<script src="{{ asset('js/bootstrap-tables.js') }}"></script>
<script>
$(document).ready(function() {
    initializeBootstrapTable('quotation-table');

    // Load data via AJAX
    $.get("{{route('quotation.data')}}", function(data) {
        const tbody = $('#quotation-table tbody');
        tbody.empty();

        if(data.data && data.data.length > 0) {
            data.data.forEach(function(row) {
                tbody.append(`
                    <tr>
                        <td>${row.id}</td>
                        <td>${row.name}</td>
                        <td>${row.tour_name}</td>
                        <td>${row.user_name}</td>
                        <td>${row.created_at}</td>
                        <td>${row.comparison}</td>
                        <td>${row.action}</td>
                    </tr>
                `);
            });
        } else {
            tbody.append('<tr><td colspan="7" class="text-center">No quotations found</td></tr>');
        }
    }).fail(function() {
        $('#quotation-table tbody').html('<tr><td colspan="7" class="text-center">Error loading data</td></tr>');
    });
});
</script>
@endpush


