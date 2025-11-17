@extends('scaffold-interface.layouts.tabler-app')
@section('content')
@include('layouts.title',
['title' => 'Client Invoices', 'sub_title' => 'Invoices according to tours',
'breadcrumbs' => [
['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
['title' => 'Tours', 'icon' => 'suitcase', 'route' => null]]])
<section class="content">
    <div class="box box-primary">
        <div class="box-body">
            <div>
                <div id="tour_create">
                    {!! \App\Helper\PermissionHelper::getCreateButton(route('accounting.create'), \App\Tour::class) !!}
                </div>

            </div>
            @if(session('message_buses'))
            <div class="alert alert-info col-md-12" style="text-align: center;">
                {{session('message_buses')}}
            </div>
            @endif
         
            <br>
            <br>

            <div class="mb-3">
                <div class="row">
                    <div class="col-md-6">
                        <input type="text" id="accounting-search" class="form-control" placeholder="Search client invoices..." onkeyup="filterTable('transactions-table', this.value)">
                    </div>
                    <div class="col-md-6 text-right">
                        <button class="btn btn-success btn-sm" onclick="exportTableToCSV('transactions-table', 'client_invoices_export.csv')">
                            <i class="fa fa-download"></i> Export CSV
                        </button>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table id="transactions-table" class="table table-striped table-bordered table-hover bootstrap-table" style='background:#fff; width: 100%;'>
                    <thead>
                        <tr>
                            <th onclick="sortTable(0, 'transactions-table')">id <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(1, 'transactions-table')">Date <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(2, 'transactions-table')">Invoice No <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(3, 'transactions-table')">Tour Name <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(4, 'transactions-table')">Client Name <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(5, 'transactions-table')">Amount Receivable <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(6, 'transactions-table')">Status <i class="fa fa-sort"></i></th>
                            <th class="actions-button">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($accountingData as $transaction)
                        <tr>
                            <td>{{ $transaction->id }}</td>
                            <td>{{ $transaction->date }}</td>
                            <td data-delete-label>{{ $transaction->invoice_no }}</td>
                            <td>{{ $transaction->tourName }}</td>
                            <td>{{ $transaction->clientName }}</td>
                            <td>{{ $transaction->amount_receiveable }}</td>
                            <td>{{ $transaction->Status }}</td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    @include('component.action_buttons', [
                                        'item' => $transaction,
                                        'routePrefix' => 'accounting'
                                    ])
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No client invoices found</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection

@include('component.delete_modal_simple')
@push('scripts')
<script src="{{ asset('js/bootstrap-tables.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        initializeBootstrapTable('transactions-table');
    });
</script>
@endpush
