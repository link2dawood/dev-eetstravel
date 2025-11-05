@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Show Tour')

@section('post_styles')
<link rel="stylesheet" href="{{ asset('css/tour-shopify.css') }}">
<style>
    
    /* Toggle Switch */
    .toggle {
        position: relative;
        height: 42px;
        display: flex;
        align-items: center;
    }
    
    .toggle input[type="checkbox"] {
        position: absolute;
        left: 0;
        top: 0;
        z-index: 10;
        width: 100%;
        height: 100%;
        cursor: pointer;
        opacity: 0;
    }
    
    .toggle label {
        position: relative;
        display: flex;
        height: 100%;
        align-items: center;
    }
    
    .toggle label:before {
        content: "Quotations";
        background: #fff;
        color: #000;
        height: 42px;
        width: 140px;
        display: inline-flex;
        align-items: center;
        padding-left: 15px;
        border-radius: 30px;
        border: 1px solid #eee;
        box-shadow: inset 140px 0px 0 0px #000;
        font-size: 10px;
        transition: 0.2s ease-in;
    }
    
    .toggle label:after {
        content: "GoAhead";
        position: absolute;
        left: 80px;
        line-height: 42px;
        top: 0;
        color: #FFF;
        font-size: 10px;
        transition: 0.2s ease-in;
    }
    
    .toggle input[type="checkbox"]:checked + label:before {
        color: #000;
        box-shadow: inset 0px 0px 0 0px #000;
    }
    
    .toggle input[type="checkbox"]:checked + label:after {
        color: #FFF;
    }
</style>
@endsection

@section('content')
<div class="container-xl">
    {{-- Page Header --}}
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col">
                {{-- Page pre-title --}}
                <div class="page-pretitle">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('/home') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('tour.index') }}">Tours</a></li>
                            <li class="breadcrumb-item active">{{ $tour->name }}</li>
                        </ol>
                    </nav>
                </div>
                <h2 class="page-title">
                    <i class="ti ti-plane me-2"></i> {{ $tour->name }}
                </h2>
            </div>
            {{-- Page title actions --}}
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route('tour.index') }}" class="btn btn-ghost-secondary">
                        <i class="ti ti-arrow-left me-1"></i>{!! trans('main.Back') !!}
                    </a>
                    @if (Auth::user()->can('tour.edit'))
                        <a href="{{ route('tour.edit', ['tour' => $tour->id]) }}" class="btn btn-warning">
                            <i class="ti ti-edit me-1"></i>{!! trans('main.Edit') !!}
                        </a>
                    @endif
                    @if (Auth::user()->can('task.create'))
                        <a href="{{ url('task') }}/create?tour={{ $tour->id }}" class="btn btn-success">
                            <i class="ti ti-plus me-1"></i>{!! trans('main.AddTask') !!}
                        </a>
                    @endif
                    
                    {{-- Export Dropdown --}}
                    <div class="btn-group">
                        <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ti ti-file-export me-1"></i>Export
                        </button>
                        <div class="dropdown-menu">
                            <h6 class="dropdown-header">Export Tour</h6>
                            <a class="dropdown-item" href="#" onclick='export_to("{{ route('tour_export', ['id' => $tour->id, 'export' => 'csv', 'type' => 'tour']) }}");'>
                                <i class="ti ti-file-spreadsheet me-2"></i>CSV - Tour
                            </a>
                            <a class="dropdown-item" href="#" onclick='export_to("{{ route('tour_export', ['id' => $tour->id, 'export' => 'csv', 'type' => 'service']) }}");'>
                                <i class="ti ti-file-spreadsheet me-2"></i>CSV - Service
                            </a>
                            <a class="dropdown-item" href="#" onclick='export_to("{{ route('tour_export', ['id' => $tour->id, 'export' => 'xlsx']) }}");'>
                                <i class="ti ti-file-excel me-2"></i>Excel
                            </a>
                        </div>
                    </div>
                    
                    {{-- Voucher Dropdown --}}
                    <div class="btn-group">
                        <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ti ti-file-invoice me-1"></i>{!! trans('main.Voucher') !!}
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="#" onclick='export_to("{{ route('tour_pdf_export', ['id' => $tour->id, 'pdf_type' => 'voucher']) }}");'>
                                <i class="ti ti-file-type-pdf me-2"></i>PDF
                            </a>
                            <a class="dropdown-item" href="#" onclick='export_to("{{ route('tour_doc_export', ['id' => $tour->id, 'doc_type' => 'voucher']) }}");'>
                                <i class="ti ti-file-type-doc me-2"></i>DOC
                            </a>
                        </div>
                    </div>
                    
                    {{-- Itinerary Dropdown --}}
                    <div class="btn-group">
                        <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ti ti-route me-1"></i>{!! trans('main.Itinerary') !!}
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="#" onclick='export_to("{{ route('tour_pdf_export', ['id' => $tour->id, 'pdf_type' => 'short']) }}");'>
                                <i class="ti ti-file-type-pdf me-2"></i>PDF
                            </a>
                            <a class="dropdown-item" href="#" onclick='export_to("{{ route('tour_html_export', ['id' => $tour->id, 'type' => 'html']) }}");'>
                                <i class="ti ti-file-code me-2"></i>HTML
                            </a>
                            <a class="dropdown-item" href="#" onclick='export_to("{{ route('tour_doc_export', ['id' => $tour->id, 'doc_type' => 'short']) }}");'>
                                <i class="ti ti-file-type-doc me-2"></i>DOC
                            </a>
                        </div>
                    </div>
                    
                    <button class="btn btn-info" onclick="showLandingPageModal()">
                        <i class="ti ti-world me-1"></i>Landing Page
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Office Selection --}}
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Select Office:</label>
                            <div class="input-group">
                                <select class="form-select selectedOffice">
                                    @foreach($offices as $office)
                                        <option value="{{ $office->id }}" {{ (isset($select_office->id) && $office->id == $select_office->id) ? 'selected' : '' }}>
                                            {{ $office->office_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <button class="btn btn-primary select-office-btn" type="button">
                                    <i class="ti ti-check me-1"></i>Select
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <button class="btn btn-info mt-4" data-bs-toggle="modal" data-bs-target="#legendModal">
                                <i class="ti ti-help me-1"></i>Help
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quotation/Tour Toggle --}}
    @if ($tour->is_quotation)
        <div class="alert alert-warning" role="alert">
            <div class="d-flex">
                <div class="flex-fill">
                    <h4 class="alert-title">
                        <i class="ti ti-exchange me-2"></i>Convert Quotation to Tour
                    </h4>
                </div>
                <div>
                    <div class="toggle">
                        <input type="checkbox" id="check1" onclick="convertQuotationToTour()" checked />
                        <label></label>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-success" role="alert">
            <div class="d-flex">
                <div class="flex-fill">
                    <h4 class="alert-title">
                        <i class="ti ti-exchange me-2"></i>Convert Tour to Quotation
                    </h4>
                </div>
                <div>
                    <div class="toggle">
                        <input type="checkbox" id="check2" onclick="convertTourToQuotation()" />
                        <label></label>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Session Messages --}}
    @if(session('message_buses'))
        <div class="alert alert-info alert-dismissible" role="alert">
            <div class="d-flex">
                <div>
                    <i class="ti ti-info-circle me-2"></i>
                </div>
                <div class="flex-fill">
                    {{ session('message_buses') }}
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    {{-- Tabs --}}
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a href="#frontsheet-tab" class="nav-link active" data-bs-toggle="tab" aria-selected="true" role="tab">
                        <i class="ti ti-file-text me-1"></i>Front Sheet
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="#service-tab" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        <i class="ti ti-list me-1"></i>{!! trans('main.Services') !!}
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="#tour-tab" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        <i class="ti ti-plane me-1"></i>{!! trans('main.Tour') !!}
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="#quotation-tab" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        <i class="ti ti-calculator me-1"></i>{!! trans('main.Quotation') !!}
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="#roomlist-tab" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        <i class="ti ti-bed me-1"></i>{!! trans('main.GuestList') !!}
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="#invoices-tab" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        <i class="ti ti-file-invoice me-1"></i>Invoices
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="#billing-tab" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                        <i class="ti ti-cash me-1"></i>Billing
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
            {{-- Front Sheet Tab --}}
            <div role="tabpanel" class="tab-pane active show" id="frontsheet-tab">
                <h3 class="mb-4"><i class="ti ti-file-text me-2"></i>Front Sheet</h3>
                @if(!empty($quotation) && isset($quotation->id))
                    <div class="row">
                        <div class="col-md-6">
                            <p class="lead">
                                <strong>Rooms:</strong>
                                @php $peopleCount = 0; @endphp
                                @foreach ($listRoomsHotel as $room)
                                    @php
                                        $peopleCount += isset(App\TourPackage::$roomsPeopleCount[$room->room_types->code]) 
                                            ? App\TourPackage::$roomsPeopleCount[$room->room_types->code] * $room->count 
                                            : 0;
                                    @endphp
                                    {{ $room->room_types->code }} : {{ $room->count }}
                                @endforeach
                            </p>
                            @if ($peopleCount != $tour->pax + $tour->pax_free)
                                <div class="alert alert-warning">
                                    <i class="ti ti-alert-triangle"></i>
                                    Pax Count ({{ $tour->pax + $tour->pax_free }}) doesn't match room capacity ({{ $peopleCount }})
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <p class="lead">
                                <strong>Pax:</strong> {{ $tour->pax }} + {{ $tour->pax_free }} (Free)
                            </p>
                        </div>
                    </div>
                    {{-- Add your front sheet table here --}}
                @else
                    <div class="alert alert-info">
                        <i class="ti ti-info-circle"></i> No quotation data available for front sheet.
                    </div>
                @endif
            </div>

            {{-- Services Tab --}}
            <div role="tabpanel" class="tab-pane" id="service-tab">
                <h3 class="mb-4"><i class="ti ti-list me-2"></i>Services</h3>
                <div class="tour-packages"></div>
                
                {{-- Comments Section --}}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ti ti-message me-2"></i>{!! trans('main.Comments') !!}
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="show_comments"></div>
                    </div>
                    <div class="card-footer">
                        <form method="POST" action="{{ route('comment.store') }}" id="form_comment">
                            @csrf
                            <div class="mb-3">
                                <textarea class="form-control" id="content" name="content" rows="3" placeholder="Ctrl + Enter to post comment"></textarea>
                            </div>
                            <input type="hidden" name="reference_id" value="{{ $tour->id }}">
                            <input type="hidden" name="reference_type" value="{{ \App\Comment::$services['tour'] ?? 'tour' }}">
                            <button type="submit" class="btn btn-success">
                                <i class="ti ti-send me-1"></i>{!! trans('main.Send') !!}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Tour Info Tab --}}
            <div role="tabpanel" class="tab-pane" id="tour-tab">
                <h3 class="mb-4"><i class="ti ti-plane me-2"></i>Tour Information</h3>
                <div class="row">
                    <div class="col-md-6">
                        <table class="table card-table table-vcenter">
                            <tbody>
                                <tr>
                                    <td><strong>{!! trans('main.Name') !!}</strong></td>
                                    <td>{{ $tour->name ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>{!! trans('main.ExternalName') !!}</strong></td>
                                    <td>{{ $tour->external_name ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>{!! trans('main.Pax') !!}</strong></td>
                                    <td>{{ $tour->pax ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>{!! trans('main.PaxFree') !!}</strong></td>
                                    <td>{{ $tour->pax_free ?? '—' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table card-table table-vcenter">
                            <tbody>
                                <tr>
                                    <td><strong>{!! trans('main.DepDate') !!}</strong></td>
                                    <td>{{ $tour->departure_date ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>{!! trans('main.RetDate') !!}</strong></td>
                                    <td>{{ $tour->retirement_date ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>{!! trans('main.Status') !!}</strong></td>
                                    <td>{{ $status->name ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>{!! trans('main.Phone') !!}</strong></td>
                                    <td>{{ $tour->phone ?? '—' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Quotations Tab --}}
            <div role="tabpanel" class="tab-pane" id="quotation-tab">
                <h3 class="mb-4"><i class="ti ti-calculator me-2"></i>Quotations</h3>
                @if (Auth::user()->can('quotation.add'))
                    <a href="{{ route('quotation.add', ['id' => $tour->id]) }}" class="btn btn-success mb-3">
                        <i class="ti ti-plus"></i> {!! trans('main.AddQuotation') !!}
                    </a>
                @endif
                
                <table class="table card-table table-vcenter table-striped">
                    <thead>
                        <tr>
                            <th>{!! trans('main.Name') !!}</th>
                            <th>{!! trans('main.Assigned') !!}</th>
                            <th>{!! trans('main.Frontsheet') !!}</th>
                            <th>{!! trans('main.Print') !!}</th>
                            <th>Excel</th>
                            <th>{!! trans('main.CreatedAt') !!}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tour->quotations as $quotation)
                            <tr style="background-color: {{ $quotation->is_confirm == 0 ? '#ff00008f' : '#caffbd' }}">
                                <td>
                                    @if (Auth::user()->can('quotation.edit'))
                                        <a href="{{ route('quotation.edit', ['quotation' => $quotation->id]) }}">
                                            {{ $quotation->name ?? '—' }}
                                        </a>
                                    @else
                                        {{ $quotation->name ?? '—' }}
                                    @endif
                                </td>
                                <td>{{ $quotation->userName() ?? '—' }}</td>
                                <td>
                                    @if (Auth::user()->can('comparison.show'))
                                        <a href="{{ route('comparison.show', ['comparison' => $quotation->id]) }}">
                                            View Front Sheet
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('quotation.pdf', ['id' => $quotation->id]) }}" target="_blank" class="btn btn-sm btn-primary">
                                        <i class="ti ti-printer"></i>
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('quotation.excel', ['id' => $quotation->id]) }}" target="_blank" class="btn btn-sm btn-success">
                                        <i class="ti ti-file-excel"></i>
                                    </a>
                                </td>
                                <td>{{ Carbon\Carbon::parse($quotation->created_at)->format('d-m-Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">
                                    <i class="ti ti-inbox"></i> No quotations found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Guest List Tab --}}
            <div role="tabpanel" class="tab-pane" id="roomlist-tab">
                <h3 class="mb-4"><i class="ti ti-bed me-2"></i>Guest Lists</h3>
                @if (Auth::user()->can('guestList.add'))
                    <a href="{{ route('guestList.add', ['id' => $tour->id]) }}" class="btn btn-success mb-3">
                        <i class="ti ti-plus"></i> Add Guest List
                    </a>
                @endif
                
                <table class="table card-table table-vcenter table-striped">
                    <thead>
                        <tr>
                            <th>Version</th>
                            <th>{!! trans('main.Name') !!}</th>
                            <th>{!! trans('main.Author') !!}</th>
                            <th>{!! trans('main.CreatedAt') !!}</th>
                            <th>{!! trans('main.SentAt') !!}</th>
                            <th>{!! trans('main.Hotels') !!}</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tour->guestLists as $guestList)
                            <tr>
                                <td>{{ $guestList->version }}</td>
                                <td>
                                    @if (Auth::user()->can('guestList.showbyid'))
                                        <a href="{{ route('guestList.showbyid', ['id' => $guestList->id]) }}">
                                            {{ $guestList->name }}
                                        </a>
                                    @else
                                        {{ $guestList->name }}
                                    @endif
                                </td>
                                <td>{{ $guestList->getAuthor()->name ?? '—' }}</td>
                                <td>{{ Carbon\Carbon::parse($guestList->created_at)->format('d-m-Y') }}</td>
                                <td>
                                    @if($guestList->sent_at)
                                        {{ Carbon\Carbon::parse($guestList->sent_at)->format('d-m-Y') }}
                                    @else
                                        <span class="text-muted">Not sent</span>
                                    @endif
                                </td>
                                <td>
                                    @foreach($guestList->getSelectedHotelNames() as $index => $hotelName)
                                        {{ $hotelName }}{{ $index < count($guestList->getSelectedHotelNames()) - 1 ? ', ' : '' }}
                                    @endforeach
                                </td>
                                <td>
                                    @if(!$guestList->sent_at)
                                        <button class="btn btn-sm btn-primary send-guest-list" 
                                                data-url="{{ route('guestlist.send', ['id' => $tour->id, 'guestlistid' => $guestList->id]) }}">
                                            <i class="ti ti-send"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger delete-guest-list"
                                                data-url="{{ route('guestlist.delete', ['id' => $tour->id, 'guestlistid' => $guestList->id]) }}">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">
                                    <i class="ti ti-inbox"></i> No guest lists found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Invoices Tab --}}
            <div role="tabpanel" class="tab-pane" id="invoices-tab">
                <h3 class="mb-4"><i class="ti ti-file-invoice me-2"></i>Invoices</h3>
                {!! \App\Helper\PermissionHelper::getCreateButton(route('invoices.create'), \App\Invoices::class, 'btn btn-success mb-3') !!}
                
                <table class="table card-table table-vcenter table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Invoice No</th>
                            <th>Due Date</th>
                            <th>Received Date</th>
                            <th>Service</th>
                            <th>Office</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoicesData as $invoice)
                            <tr>
                                <td>{{ $invoice['id'] }}</td>
                                <td>{{ $invoice['invoice_no'] }}</td>
                                <td>{{ $invoice['due_date'] }}</td>
                                <td>{{ $invoice['received_date'] }}</td>
                                <td>{{ $invoice['package_name'] }}</td>
                                <td>{{ $invoice['office_name'] }}</td>
                                <td>{{ $invoice['total_amount'] }}</td>
                                <td>{{ $invoice['status'] }}</td>
                                <td>
                                    <a href="{{ route('invoices.show', ['invoice' => $invoice['id']]) }}" class="btn btn-sm btn-info">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                    <a href="{{ route('invoices.edit', ['invoice' => $invoice['id']]) }}" class="btn btn-sm btn-warning">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">
                                    <i class="ti ti-inbox"></i> No invoices found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Billing Tab --}}
            <div role="tabpanel" class="tab-pane" id="billing-tab">
                <h3 class="mb-4"><i class="ti ti-cash me-2"></i>Billing</h3>
                {!! \App\Helper\PermissionHelper::getCreateButton(route('accounting.create'), \App\Tour::class, 'btn btn-success mb-3') !!}
                
                <table class="table card-table table-vcenter table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Office</th>
                            <th>Total Amount</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($billingData as $billing)
                            <tr>
                                <td>{{ $billing['id'] }}</td>
                                <td>{{ \Carbon\Carbon::parse($billing['date'] ?? now())->format('Y-m-d') }}</td>
                                <td>{{ $billing['office_name'] }}</td>
                                <td>{{ $billing['total_amount'] }}</td>
                                <td>
                                    <a href="{{ route('accounting.show', ['accounting' => $billing['id']]) }}" class="btn btn-sm btn-info">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                    <a href="{{ route('accounting.edit', ['accounting' => $billing['id']]) }}" class="btn btn-sm btn-warning">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">
                                    <i class="ti ti-inbox"></i> No billing records found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Hidden Data --}}
<span id="tour_date_id" data-tour-id="{{ $tour->id }}" hidden></span>
<span id="tour_dates" data-departure_date="{{ $tour->departure_date }}" data-retirement_date="{{ $tour->retirement_date }}" hidden></span>
<a id="quotation_to_tour_href" href="{{ route('tour.convert_to_tour', ['id' => $tour->id]) }}" hidden></a>
<a id="tour_to_quotation" href="{{ route('tour.convertToQuotation', ['id' => $tour->id]) }}" hidden></a>

{{-- Service Modal --}}
<div class="modal modal-blur fade" id="service-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{!! trans('main.Addservice') !!}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table id="search-table" class="table card-table table-vcenter table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>{!! trans('main.Name') !!}</th>
                            <th>{!! trans('main.Address') !!}</th>
                            <th>{!! trans('main.Country') !!}</th>
                            <th>{!! trans('main.City') !!}</th>
                            <th>{!! trans('main.Phone') !!}</th>
                            <th>{!! trans('main.ContactName') !!}</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Landing Page Modal --}}
<div class="modal fade" id="landingpage_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Warning</h4>
            </div>
            <div class="modal-body">
                <p>There is no image for landing page. Are you sure you want to generate the page?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="open-landing" onclick='export_to("{{ route('landing_page', ['id' => $tour->id]) }}");'>
                    Agree
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Question Modal --}}
<div class="modal fade" id="question_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Warning</h4>
            </div>
            <div class="modal-body">
                <p>Would you like to send Guest List to selected tour hotels?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="send_agree">Agree</button>
            </div>
        </div>
    </div>
</div>

{{-- Error Modal --}}
<div class="modal fade" id="error_send" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="title_modal_error">Warning!</h4>
            </div>
            <div class="modal-body">
                <h3 class="error_send_message"></h3>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('post_scripts')
<script src="{{ asset('js/tour-interactions.js') }}"></script>
<script src="{{ asset('js/supplier-search.js') }}"></script>
<script src="{{ asset('js/tour.js') }}"></script>
<script src="{{ asset('js/comment.js') }}"></script>
<script src="{{ asset('js/attachments.js') }}"></script>

<script>
$(document).ready(function() {
    // Tab persistence
    var activeTab = localStorage.getItem('tourActiveTab') || 'frontsheet-tab';
    $('.nav-tabs a[href="#' + activeTab + '"]').tab('show');
    
    $('.nav-tabs a').on('shown.bs.tab', function(e) {
        var tabId = $(e.target).attr('href').substring(1);
        localStorage.setItem('tourActiveTab', tabId);
    });

    // Office Selection
    $('.select-office-btn').on('click', function() {
        let officeId = $('.selectedOffice').val();
        if (officeId) {
            $.ajax({
                url: '/update-status/' + officeId,
                type: 'GET',
                success: function() {
                    location.reload(true);
                },
                error: function() {
                    alert('Error updating office');
                }
            });
        }
    });

    // Send Guest List
    var selectedGuestList;
    $('.send-guest-list').on('click', function() {
        selectedGuestList = $(this);
        $('#question_modal').modal('show');
    });

    $('#send_agree').on('click', function() {
        if (!selectedGuestList) return;
        
        let overlay = '<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>';
        let container = selectedGuestList.closest('.box-body');
        container.append(overlay);
        selectedGuestList.hide();
        
        $.ajax({
            method: 'GET',
            url: selectedGuestList.data('url'),
            beforeSend: function() {
                $('#question_modal').modal('hide');
            },
            success: function(res) {
                $('#error_send').find('#title_modal_error').html(res.error === 'error' ? 'Warning!' : 'Success!');
                $('#error_send').find('.error_send_message').html(res.message);
                if (res.broke) {
                    $('#error_send').find('.error_send_message').append('<br><br>' + res.broke);
                }
                $('.overlay').remove();
                $('#error_send').modal('show');
                
                setTimeout(function() {
                    $('#error_send').modal('hide');
                    if (res.error !== 'error') {
                        location.reload();
                    } else {
                        selectedGuestList.show();
                    }
                }, 3000);
            },
            error: function() {
                $('.overlay').remove();
                alert('Error sending guest list');
                selectedGuestList.show();
            }
        });
    });

    // Delete Guest List
    $('.delete-guest-list').on('click', function() {
        if (confirm('Are you sure you want to delete this guest list?')) {
            let url = $(this).data('url');
            $.ajax({
                method: 'GET',
                url: url,
                success: function() {
                    location.reload(true);
                },
                error: function() {
                    alert('Error deleting guest list');
                }
            });
        }
    });

    // Comment form submission with Ctrl+Enter
    $('#content').on('keydown', function(e) {
        if (e.ctrlKey && e.keyCode === 13) {
            $('#form_comment').submit();
        }
    });
});

// Convert Quotation to Tour
function convertQuotationToTour() {
    var url = $('#check1').prop('checked') 
        ? $('#tour_to_quotation').attr('href')
        : $('#quotation_to_tour_href').attr('href');
    
    $.ajax({
        type: 'GET',
        url: url,
        success: function() {
            location.reload();
        },
        error: function() {
            alert('Error converting tour status');
        }
    });
}

// Convert Tour to Quotation
function convertTourToQuotation() {
    var url = !$('#check2').prop('checked')
        ? $('#quotation_to_tour_href').attr('href')
        : $('#tour_to_quotation').attr('href');
    
    $.ajax({
        type: 'GET',
        url: url,
        success: function() {
            location.reload();
        },
        error: function() {
            alert('Error converting tour status');
        }
    });
}

// Show Landing Page Modal
function showLandingPageModal() {
    var img = "{{ $tour->attachments()->first() ? $tour->attachments()->first()->url : '' }}";
    if (!img) {
        $('#landingpage_modal').modal('show');
    } else {
        window.open("{{ route('landing_page', ['id' => $tour->id]) }}", '_blank');
    }
}

// Export function
function export_to(url) {
    window.open(url, '_blank');
}

// Scroll position persistence
$(window).on('scroll', function() {
    localStorage.setItem('tourScrollPosition', $(window).scrollTop());
});

$(document).ready(function() {
    var scrollPos = localStorage.getItem('tourScrollPosition');
    if (scrollPos) {
        $(window).scrollTop(parseInt(scrollPos));
    }
});
</script>
@endsection