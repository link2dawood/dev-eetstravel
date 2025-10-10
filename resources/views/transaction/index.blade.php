@extends('scaffold-interface.layouts.app')
@section('content')
@include('layouts.title',
['title' => 'Customer Transaction', 'sub_title' => 'Transaction List',
'breadcrumbs' => [
['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
['title' => 'Tours', 'icon' => 'suitcase', 'route' => null]]])

<section class="content">
    <div class="box box-primary">
        <div class="box-body">
            <div>
                <div id="tour_create">
                    {!! \App\Helper\PermissionHelper::getCreateButton(route('transaction.create'), \App\Invoices::class) !!}
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
                        <input type="text" id="transaction-search" class="form-control" placeholder="Search transactions..." onkeyup="filterTable('inovices-table', this.value)">
                    </div>
                    <div class="col-md-6 text-right">
                        <button class="btn btn-success btn-sm" onclick="exportTableToCSV('inovices-table', 'transactions_export.csv')">
                            <i class="fa fa-download"></i> Export CSV
                        </button>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table id="inovices-table" class="table table-striped table-bordered table-hover bootstrap-table" style='background:#fff; width: 100%;'>
                    <thead>
                        <tr>
                            <th onclick="sortTable(0, 'inovices-table')">Id <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(1, 'inovices-table')">Date <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(2, 'inovices-table')">Payment To <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(3, 'inovices-table')">Transaction No <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(4, 'inovices-table')">Invoice No <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(5, 'inovices-table')">Amount <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(6, 'inovices-table')">Unallocated <i class="fa fa-sort"></i></th>
                            <th class="actions-button">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($transactionsData as $transaction)
                        <tr>
                            <td>{{ $transaction->id }}</td>
                            <td>{{ $transaction->date }}</td>
                            <td>{{ $transaction->pay_to }}</td>
                            <td>{{ $transaction->transaction_no }}</td>
                            <td>{{ $transaction->invoice_no }}</td>
                            <td>{{ $transaction->amount }}</td>
                            <td>{{ $transaction->unallocated }}</td>
                            <td>{!! $transaction->action_buttons !!}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No transactions found</td>
                        </tr>
                    @endforelse
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
    document.addEventListener('DOMContentLoaded', function() {
        initializeBootstrapTable('inovices-table');
    });
</script>
@endpush
