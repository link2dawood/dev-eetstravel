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
                    {!! \App\Helper\PermissionHelper::getCreateButton(route('accounting.create'), \App\ClientInvoices::class) !!}
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
                            <th>id <i class="fa fa-sort"></i></th>
                            <th>Date <i class="fa fa-sort"></i></th>
                            <th>Invoice No <i class="fa fa-sort"></i></th>
                            <th>Tour Name <i class="fa fa-sort"></i></th>
                            <th>Client Name <i class="fa fa-sort"></i></th>
                            <th>Amount Receivable <i class="fa fa-sort"></i></th>
                            <th>Status <i class="fa fa-sort"></i></th>
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
