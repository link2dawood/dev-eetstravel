@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Bus Company Details')

@section('content')
<div class="container-xl">
    {{-- Page Header --}}
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('/home') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('transfer.index') }}">Bus Companies</a></li>
                            <li class="breadcrumb-item active">{{ $transfer->name }}</li>
                        </ol>
                    </nav>
                </div>
                <h2 class="page-title">
                    <i class="ti ti-bus me-2"></i>{{ $transfer->name }}
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route('transfer.index') }}" class="btn btn-ghost-secondary">
                        <i class="ti ti-arrow-left me-1"></i>{!! trans('main.Back') !!}
                    </a>
                    @if (Auth::user()->can('transfer.edit'))
                        <a href="{{ route('transfer.edit', $transfer->id) }}" class="btn btn-warning">
                            <i class="ti ti-edit me-1"></i>{!! trans('main.Edit') !!}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a href="#info-tab" class="nav-link active" data-bs-toggle="tab" aria-selected="true" role="tab">
                        <i class="ti ti-info-circle me-1"></i>Information
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="#history-tab" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        <i class="ti ti-history me-1"></i>History
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="#invoices-tab" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1" id="invoices_tab">
                        <i class="ti ti-file-invoice me-1"></i>{!! trans('Invoices') !!}
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                {{-- Info Tab --}}
                <div class="tab-pane fade show active" id="info-tab" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6">
                            <h3 class="mb-3"><i class="ti ti-building me-2"></i>Company Information</h3>
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <td class="w-50"><strong>{!!trans('main.Name')!!}:</strong></td>
                                        <td>{{ $transfer->name ?? '—' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{!!trans('main.AddressFirst')!!}:</strong></td>
                                        <td>{{ $transfer->address_first ?? '—' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{!!trans('main.AddressSecond')!!}:</strong></td>
                                        <td>{{ $transfer->address_second ?? '—' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{!!trans('main.Country')!!}:</strong></td>
                                        <td>
                                            @if(!empty($transfer->country))
                                                {{ \App\Helper\CitiesHelper::getCountryById($transfer->country)['name'] ?? '—' }}
                                            @else
                                                —
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>{!!trans('main.City')!!}:</strong></td>
                                        <td>
                                            @if(!empty($transfer->city))
                                                {{ \App\Helper\CitiesHelper::getCityById($transfer->city)['name'] ?? '—' }}
                                            @else
                                                —
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>{!!trans('main.Code')!!}:</strong></td>
                                        <td>{{ $transfer->code ?? '—' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{!!trans('main.WorkPhone')!!}:</strong></td>
                                        <td>{{ $transfer->work_phone ?? '—' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{!!trans('main.WorkFax')!!}:</strong></td>
                                        <td>{{ $transfer->work_fax ?? '—' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{!!trans('main.WorkEmail')!!}:</strong></td>
                                        <td>{{ $transfer->work_email ?? '—' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h3 class="mb-3"><i class="ti ti-user me-2"></i>Contact & Details</h3>
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <td class="w-50"><strong>{!!trans('main.ContactName')!!}:</strong></td>
                                        <td>{{ $transfer->contact_name ?? '—' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{!!trans('main.ContactPhone')!!}:</strong></td>
                                        <td>{{ $transfer->contact_phone ?? '—' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{!!trans('main.ContactEmail')!!}:</strong></td>
                                        <td>{{ $transfer->contact_email ?? '—' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{!!trans('main.Website')!!}:</strong></td>
                                        <td>
                                            @if($transfer->website)
                                                <a href="{{ $transfer->website }}" target="_blank" class="text-primary">
                                                    {{ $transfer->website }}
                                                </a>
                                            @else
                                                —
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>{!!trans('main.Rate')!!}:</strong></td>
                                        <td>{{ $transfer->rate_name ?? '—' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{!!trans('main.Criterias')!!}:</strong></td>
                                        <td>
                                            @forelse($criterias as $criteria)
                                                @forelse($transfer->criterias as $item)
                                                    @if($criteria->id == $item->criteria_id)
                                                        <span class="badge bg-blue-lt me-1">{{ $criteria->name }}</span>
                                                    @endif
                                                @empty
                                                @endforelse
                                            @empty
                                                <span class="text-muted">—</span>
                                            @endforelse
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>{!!trans('main.Comments')!!}:</strong></td>
                                        <td>{{ $transfer->comments ?? '—' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{!!trans('main.IntComments')!!}:</strong></td>
                                        <td>{{ $transfer->int_comments ?? '—' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Files Component --}}
                    <div class="mt-4">
                        @component('component.files', ['files' => $files])@endcomponent
                    </div>

                    {{-- Comments Section --}}
                    <div class="card mt-4">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="ti ti-message me-2"></i>{!!trans('main.Comments')!!}
                            </h3>
                        </div>
                        <div class="card-body">
                            <div id="show_comments" style="max-height: 400px; overflow-y: auto;"></div>
                        </div>
                        <div class="card-footer">
                            <form method="POST" action="{{ route('comment.store') }}" enctype="multipart/form-data" id="form_comment">
                                @csrf
                                <div class="mb-3">
                                    <span id="author_name" class="text-muted" style="display:none;">
                                        <span id="name"></span>
                                        <a href="#" id="reply_close" class="ms-2"><i class="ti ti-x"></i></a>
                                    </span>
                                    <textarea class="form-control" 
                                              id="content" 
                                              name="content" 
                                              rows="3" 
                                              placeholder="Ctrl + Enter to post comment"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{!!trans('main.Files')!!}</label>
                                    @component('component.file_upload_field')@endcomponent
                                </div>
                                <input type="hidden" id="parent_comment" name="parent" value="">
                                <input type="hidden" id="default_reference_id" name="reference_id" value="{{ $transfer->id }}">
                                <input type="hidden" id="default_reference_type" name="reference_type" value="{{ \App\Comment::$services['transfer'] ?? 'transfer' }}">
                                <button type="submit" class="btn btn-primary" id="btn_send_comment">
                                    <i class="ti ti-send me-1"></i>{!!trans('main.Send')!!}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- History Tab --}}
                <div class="tab-pane fade" id="history-tab" role="tabpanel">
                    <div id="history-container"></div>
                </div>

                {{-- Invoices Tab --}}
                <div class="tab-pane fade" id="invoices-tab" role="tabpanel">
                    <div class="mb-3">
                        {!! \App\Helper\PermissionHelper::getCreateButton(route('invoices.create'), \App\Invoices::class, 'btn btn-primary') !!}
                    </div>

                    {{-- Search & Export --}}
                    <div class="row mb-3">
                        <div class="col-md-6 mb-2 mb-md-0">
                            <div class="input-icon">
                                <span class="input-icon-addon">
                                    <i class="ti ti-search"></i>
                                </span>
                                <input type="text" 
                                       id="invoiceSearchInput" 
                                       class="form-control" 
                                       placeholder="Search invoices..." 
                                       onkeyup="filterInvoiceTable()">
                            </div>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-success" onclick="exportInvoicesToCSV()">
                                    <i class="ti ti-file-type-csv me-1"></i>CSV
                                </button>
                                <button type="button" class="btn btn-success" onclick="exportInvoicesToExcel()">
                                    <i class="ti ti-file-type-xls me-1"></i>Excel
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Invoices Table --}}
                    <div class="table-responsive">
                        <table id="inovices-table" class="table card-table table-vcenter table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">ID</th>
                                    <th>Invoice No</th>
                                    <th class="d-none d-md-table-cell">Due Date</th>
                                    <th class="d-none d-md-table-cell">Received Date</th>
                                    <th class="d-none d-lg-table-cell">Tour</th>
                                    <th class="d-none d-xl-table-cell">Service</th>
                                    <th class="d-none d-xl-table-cell">Office</th>
                                    <th>Total Price</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($invoices) && $invoices->count() > 0)
                                    @foreach($invoices as $invoice)
                                        <tr>
                                            <td>
                                                <span class="text-muted">#{{ $invoice->id }}</span>
                                            </td>
                                            <td>
                                                <strong>{{ $invoice->invoice_no ?? 'N/A' }}</strong>
                                            </td>
                                            <td class="d-none d-md-table-cell">
                                                {{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('Y-m-d') : 'N/A' }}
                                            </td>
                                            <td class="d-none d-md-table-cell">
                                                {{ $invoice->received_date ? \Carbon\Carbon::parse($invoice->received_date)->format('Y-m-d') : 'N/A' }}
                                            </td>
                                            <td class="d-none d-lg-table-cell">
                                                {{ $invoice->tour_name ?? 'N/A' }}
                                            </td>
                                            <td class="d-none d-xl-table-cell">
                                                {{ $invoice->service_name ?? 'N/A' }}
                                            </td>
                                            <td class="d-none d-xl-table-cell">
                                                {{ $invoice->office_name ?? 'N/A' }}
                                            </td>
                                            <td>
                                                <strong>{{ number_format($invoice->total_amount ?? 0, 2) }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $invoice->status == 'paid' ? 'success' : ($invoice->status == 'pending' ? 'warning' : 'danger') }}">
                                                    {{ ucfirst($invoice->status ?? 'pending') }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-list justify-content-end">
                                                    @include('component.action_buttons', [
                                                        'routePrefix' => 'invoices',
                                                        'item' => $invoice,
                                                        'showEdit' => true,
                                                        'showDelete' => true,
                                                        'showView' => true
                                                    ])
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="10" class="text-center py-5">
                                            <div class="empty">
                                                <div class="empty-icon">
                                                    <i class="ti ti-file-invoice icon" style="font-size: 3rem;"></i>
                                                </div>
                                                <p class="empty-title">No invoices found</p>
                                                <p class="empty-subtitle text-muted">Invoices for this bus company will appear here</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if(isset($invoices) && method_exists($invoices, 'links') && $invoices->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            Showing {{ $invoices->firstItem() }} to {{ $invoices->lastItem() }} of {{ $invoices->total() }} entries
                        </div>
                        <div>
                            {{ $invoices->links() }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<span id="showPreviewBlock" data-info="{{ true }}" hidden></span>
<span id="services_name" data-service-name='Transfer' data-history-route="{{route('services_history', ['id' => $transfer->id])}}" hidden></span>
@endsection

@section('post_scripts')
<script src="{{ asset('js/comment.js') }}"></script>
<script src="{{ asset('js/bootstrap-tables.js') }}"></script>
<script>
    $(document).ready(function() {
        // Initialize Bootstrap table functionality
        initializeBootstrapTable('inovices-table');
    });

    // Invoice table search functionality
    function filterInvoiceTable() {
        const input = document.getElementById('invoiceSearchInput');
        const filter = input.value.toUpperCase();
        const table = document.getElementById('inovices-table');
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

    // Export functions for invoices
    function exportInvoicesToCSV() {
        exportTableToCSV('inovices-table', 'transfer-invoices.csv');
    }

    function exportInvoicesToExcel() {
        exportTableToExcel('inovices-table', 'transfer-invoices');
    }
</script>
@endsection
