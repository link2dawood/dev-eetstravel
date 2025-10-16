@extends('scaffold-interface.layouts.tabler-app')
@section('title','Show Client')
@section('content')
    <!-- Page header -->
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/home') }}"><i class="ti ti-home"></i> Home</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('clients.index') }}">Clients</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Show</li>
                            </ol>
                        </nav>
                    </div>
                    <h2 class="page-title">Client Details</h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="javascript:history.back()" class="btn btn-primary">
                            <i class="ti ti-arrow-left"></i> {!!trans('main.Back')!!}
                        </a>
                        <a href="{!! route('clients.edit', $client->id) !!}" class="btn btn-warning">
                            <i class="ti ti-edit"></i> {!!trans('main.Edit')!!}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Page body -->
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a href="#info-tab" class="nav-link active" data-bs-toggle="tab" aria-selected="true" role="tab">
                                        <i class="ti ti-info-circle"></i> {!!trans('main.Info')!!}
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a href="#contacts-tab" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                                        <i class="ti ti-users"></i> {!!trans('main.Contacts')!!}
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a href="#billing-tab" class="nav-link" id="billing_tab" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                                        <i class="ti ti-file-invoice"></i> {!! trans('Billing') !!}
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="tab-pane fade show active" role='tabpanel' id='info-tab'>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <table class='table table-bordered'>
                                                <tbody>
                                                <tr>
                                                    <td><strong>{!!trans('main.Name')!!}</strong></td>
                                                    <td>{!!$client->name!!}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>{!!trans('main.Country')!!}</strong></td>
                                                    <td>{!! \App\Helper\CitiesHelper::getCountryById($client->country)['name']!!}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>{!!trans('main.City')!!}</strong></td>
                                                    <td>{!! \App\Helper\CitiesHelper::getCityById($client->city)['name']!!}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>{!!trans('main.Address')!!}</strong></td>
                                                    <td>{!!$client->address!!}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>{!!trans('main.WorkPhone')!!}</strong></td>
                                                    <td>{!!$client->work_phone!!}</td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-lg-6">
                                            <table class='table table-bordered'>
                                                <tbody>
                                                <tr>
                                                    <td><strong>{!!trans('main.ContactPhone')!!}</strong></td>
                                                    <td>{!!$client->contact_phone!!}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>{!!trans('main.WorkEmail')!!}</strong></td>
                                                    <td>{!!$client->work_email!!}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>{!!trans('main.ContactEmail')!!}</strong></td>
                                                    <td>{!!$client->contact_email!!}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>{!!trans('main.WorkFax')!!}</strong></td>
                                                    <td>{!!$client->work_fax!!}</td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div style="clear: both"></div>
                                    @component('component.files', ['files' => $files])@endcomponent

                                    <span id="showPreviewBlock" data-info="{{ true }}"></span>
                                    <div class="card card-success mt-3">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                <i class="ti ti-messages"></i> {!!trans('main.Comments')!!}
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            <div style="position: relative; overflow-y: scroll;  width: auto;">
                                                <div style="width: auto; height: auto;">
                                                    <div id="show_comments"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <form method='POST' action='{{route('comment.store')}}' enctype="multipart/form-data" id="form_comment">
                                                <div class="mb-3">
                                                    <span id="author_name" class="d-none">
                                                        <span id="name"></span>
                                                        <a href="#" id="reply_close"><i class="ti ti-x"></i></a>
                                                    </span>
                                                    <textarea class="form-control" id="content" name="content" placeholder="Ctrl + Enter to post comment" rows="3"></textarea>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label class="form-label">{!!trans('main.Files')!!}</label>
                                                    @component('component.file_upload_field')@endcomponent
                                                </div>
                                                <input type="text" id="parent_comment" hidden name="parent" value="{{ null }}">
                                                <input type="text" id="default_reference_id" hidden name="reference_id" value="{{ $client->id }}">
                                                <input type="text" id="default_reference_type" hidden name="reference_type" value="{{ \App\Comment::$services['client']}}">

                                                <button type="submit" class="btn btn-success" id="btn_send_comment">
                                                    <i class="ti ti-send"></i> {!!trans('main.Send')!!}
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" role='tabpanel' id='contacts-tab'>
                                    @if($contacts->count())
                                    <div class="table-responsive">
                                        <table class='table table-bordered card-table table-vcenter'>
                                            <thead>
                                                <tr>
                                                    <th><strong>{!!trans('main.FullName')!!}</strong></th>
                                                    <th><strong>{!!trans('main.MobilePhone')!!}</strong></th>
                                                    <th><strong>{!!trans('main.WorkPhone')!!}</strong></th>
                                                    <th><strong>{!!trans('main.Email')!!}</strong></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($contacts as $contact)
                                                    <tr>
                                                        <td>{{ $contact->full_name }}</td>
                                                        <td>{!!$contact->mobile_phone!!}</td>
                                                        <td>{!!$contact->work_phone!!}</td>
                                                        <td>{!!$contact->email!!}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @else
                                        <div class="empty">
                                            <div class="empty-icon">
                                                <i class="ti ti-users"></i>
                                            </div>
                                            <p class="empty-title">{!!trans('Client dont have contacts')!!}</p>
                                        </div>
                                    @endif
                                </div>

                                <div role="tabpanel" class="tab-pane fade" id="billing-tab">
                                    <div class="mb-3">
                                        {!! \App\Helper\PermissionHelper::getCreateButton(route('accounting.create'), \App\Tour::class) !!}
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <input type="text" id="searchInput" class="form-control" placeholder="Search transactions..." onkeyup="filterTable()">
                                        </div>
                                        <div class="col-md-6 text-end">
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-primary btn-sm" onclick="exportToCSV()">
                                                    <i class="ti ti-file-type-csv"></i> Export CSV
                                                </button>
                                                <button type="button" class="btn btn-success btn-sm" onclick="exportToExcel()">
                                                    <i class="ti ti-file-spreadsheet"></i> Export Excel
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table id="transactions-table" class="table card-table table-vcenter text-nowrap datatable">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Date</th>
                                                    <th>Tour Name</th>
                                                    <th>Client Name</th>
                                                    <th>Amount Receivable</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(isset($transactions) && $transactions->count() > 0)
                                                    @foreach($transactions as $transaction)
                                                        <tr>
                                                            <td>{{ $transaction->id }}</td>
                                                            <td>{{ $transaction->date }}</td>
                                                            <td>{{ $transaction->tour_name ?? 'N/A' }}</td>
                                                            <td>{{ $transaction->client_name ?? 'N/A' }}</td>
                                                            <td>{{ number_format($transaction->amount_receivable ?? 0, 2) }}</td>
                                                            <td>
                                                                <span class="badge badge-{{ $transaction->status == 'paid' ? 'success' : 'warning' }}">
                                                                    {{ ucfirst($transaction->status ?? 'pending') }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                @include('component.action_buttons', [
                                                                    'routePrefix' => 'accounting',
                                                                    'item' => $transaction,
                                                                    'showEdit' => true,
                                                                    'showDelete' => true,
                                                                    'showView' => true
                                                                ])
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="7" class="text-center">No transactions found for this client</td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>

                                    @if(isset($transactions) && method_exists($transactions, 'links'))
                                        <div class="d-flex align-items-center mt-3">
                                            {{ $transactions->links() }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <span id="services_name" data-service-name='Client' data-history-route="{{route('services_history', ['id' => $client->id])}}"></span>
@endsection

@section('post_scripts')
    <script src="{{ asset('js/comment.js') }}"></script>
    <script src="{{ asset('js/bootstrap-tables.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Initialize Bootstrap table functionality
            initializeBootstrapTable('transactions-table');
        });

        // Table search functionality
        function filterTable() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toUpperCase();
            const table = document.getElementById('transactions-table');
            const tr = table.getElementsByTagName('tr');

            for (let i = 1; i < tr.length; i++) {
                let display = false;
                const td = tr[i].getElementsByTagName('td');

                for (let j = 0; j < td.length - 1; j++) { // Exclude action column
                    if (td[j]) {
                        const txtValue = td[j].textContent || td[j].innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            display = true;
                            break;
                        }
                    }
                }

                tr[i].style.display = display ? '' : 'none';
            }
        }

        // Export functions
        function exportToCSV() {
            exportTableToCSV('transactions-table', 'client-transactions.csv');
        }

        function exportToExcel() {
            exportTableToExcel('transactions-table', 'client-transactions');
        }
    </script>
@endsection
