@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Show Tour')

@section('post_styles')
<link rel="stylesheet" href="{{ asset('css/tour-shopify.css') }}">
<style>
    /* Tab Navigation Styles */
    .nav-tabs-custom {
        margin-bottom: 20px;
        background: #fff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.12);
        border-radius: 4px;
    }
    
    .nav-tabs-custom > .nav-tabs {
        border-bottom: 2px solid #f4f4f4;
        margin: 0;
    }
    
    .nav-tabs-custom > .nav-tabs > li {
        margin-bottom: -2px;
    }
    
    .nav-tabs-custom > .nav-tabs > li > a {
        border-radius: 0;
        border: none;
        color: #444;
        padding: 12px 20px;
    }
    
    .nav-tabs-custom > .nav-tabs > li.active > a {
        border-bottom: 3px solid #007bff;
        color: #007bff;
        font-weight: 600;
    }
    
    .nav-tabs-custom > .nav-tabs > li > a:hover {
        background-color: #f8f9fa;
    }
    
    .tab-content {
        padding: 20px;
    }
    
    /* Table Styles */
    .table-bordered {
        border: 1px solid #dee2e6;
    }
    
    .table-bordered td,
    .table-bordered th {
        border: 1px solid #dee2e6;
        padding: 12px;
    }
    
    /* Box Styles */
    .box {
        border-radius: 4px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.12);
        margin-bottom: 20px;
        background: #fff;
    }
    
    .box-header {
        padding: 15px;
        border-bottom: 1px solid #f4f4f4;
    }
    
    .box-body {
        padding: 15px;
    }
    
    /* Button Styles */
    .margin_button {
        margin-bottom: 15px;
    }
    
    .margin_button .btn {
        margin-right: 5px;
        margin-bottom: 5px;
    }
    
    /* Modal Styles */
    .modal-dialog {
        margin: 30px auto;
    }
    
    /* Status Badge */
    .badge {
        padding: 5px 10px;
        border-radius: 3px;
        font-size: 12px;
    }
    
    /* Alert Styles */
    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border: 1px solid transparent;
        border-radius: 4px;
    }
    
    .alert-warning {
        background-color: #fff3cd;
        border-color: #ffc107;
        color: #856404;
    }
    
    .alert-info {
        background-color: #d1ecf1;
        border-color: #17a2b8;
        color: #0c5460;
    }
    
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
<div class="container-fluid">
    {{-- Page Header --}}
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="page-title">Tour: {{ $tour->name }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('tour.index') }}"><i class="fa fa-suitcase"></i> Tours</a></li>
                    <li class="breadcrumb-item active">Show</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="margin_button">
                <a href="{{ route('tour.index') }}" class="btn btn-primary">
                    <i class="fa fa-arrow-left"></i> {!! trans('main.Back') !!}
                </a>
                @if (Auth::user()->can('tour.edit'))
                    <a href="{{ route('tour.edit', ['tour' => $tour->id]) }}" class="btn btn-warning">
                        <i class="fa fa-edit"></i> {!! trans('main.Edit') !!}
                    </a>
                @endif
                @if (Auth::user()->can('task.create'))
                    <a href="{{ url('task') }}/create?tour={{ $tour->id }}" class="btn btn-success">
                        <i class="fa fa-plus"></i> {!! trans('main.AddTask') !!}
                    </a>
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <div class="pull-right">
                <div class="dropdown" style="display: inline-block; margin-right: 5px;">
                    <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
                        <i class="fa fa-file-excel-o"></i> Export
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a href="#" onclick='export_to("{{ route('tour_export', ['id' => $tour->id, 'export' => 'csv', 'type' => 'tour']) }}");'>CSV - Tour</a></li>
                        <li><a href="#" onclick='export_to("{{ route('tour_export', ['id' => $tour->id, 'export' => 'csv', 'type' => 'service']) }}");'>CSV - Service</a></li>
                        <li><a href="#" onclick='export_to("{{ route('tour_export', ['id' => $tour->id, 'export' => 'xlsx']) }}");'>Excel</a></li>
                    </ul>
                </div>
                
                <div class="dropdown" style="display: inline-block; margin-right: 5px;">
                    <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
                        <i class="fa fa-file-pdf-o"></i> {!! trans('main.Voucher') !!}
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a href="#" onclick='export_to("{{ route('tour_pdf_export', ['id' => $tour->id, 'pdf_type' => 'voucher']) }}");'>PDF</a></li>
                        <li><a href="#" onclick='export_to("{{ route('tour_doc_export', ['id' => $tour->id, 'doc_type' => 'voucher']) }}");'>DOC</a></li>
                    </ul>
                </div>
                
                <div class="dropdown" style="display: inline-block; margin-right: 5px;">
                    <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
                        <i class="fa fa-file-text-o"></i> {!! trans('main.Itinerary') !!}
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a href="#" onclick='export_to("{{ route('tour_pdf_export', ['id' => $tour->id, 'pdf_type' => 'short']) }}");'>PDF</a></li>
                        <li><a href="#" onclick='export_to("{{ route('tour_html_export', ['id' => $tour->id, 'type' => 'html']) }}");'>HTML</a></li>
                        <li><a href="#" onclick='export_to("{{ route('tour_doc_export', ['id' => $tour->id, 'doc_type' => 'short']) }}");'>DOC</a></li>
                    </ul>
                </div>
                
                <button class="btn btn-default" onclick="showLandingPageModal()">
                    <i class="fa fa-globe"></i> Landing Page
                </button>
            </div>
        </div>
    </div>

    {{-- Office Selection --}}
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label>Select Office:</label>
                            <div class="input-group">
                                <select class="form-control selectedOffice">
                                    @foreach($offices as $office)
                                        <option value="{{ $office->id }}" {{ (isset($select_office->id) && $office->id == $select_office->id) ? 'selected' : '' }}>
                                            {{ $office->office_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="input-group-btn">
                                    <button class="btn btn-primary select-office-btn" type="button">
                                        <i class="fa fa-check"></i> Select
                                    </button>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <span class="pull-right">
                                <button class="btn btn-info" data-toggle="modal" data-target="#legendModal">
                                    <i class="fa fa-question-circle"></i> Help
                                </button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quotation/Tour Toggle --}}
    @if ($tour->is_quotation)
        <div class="alert alert-warning">
            <div class="row">
                <div class="col-md-8">
                    <h5><i class="fa fa-exchange"></i> Convert Quotation to Tour</h5>
                </div>
                <div class="col-md-4">
                    <div class="toggle pull-right">
                        <input type="checkbox" id="check1" onclick="convertQuotationToTour()" checked />
                        <label></label>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-success">
            <div class="row">
                <div class="col-md-8">
                    <h5><i class="fa fa-exchange"></i> Convert Tour to Quotation</h5>
                </div>
                <div class="col-md-4">
                    <div class="toggle pull-right">
                        <input type="checkbox" id="check2" onclick="convertTourToQuotation()" />
                        <label></label>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Session Messages --}}
    @if(session('message_buses'))
        <div class="alert alert-info alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fa fa-info-circle"></i> {{ session('message_buses') }}
        </div>
    @endif

    {{-- Tabs --}}
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active">
                <a href="#frontsheet-tab" aria-controls="frontsheet-tab" role="tab" data-toggle="tab">
                    <i class="fa fa-file-text-o"></i> Front Sheet
                </a>
            </li>
            <li role="presentation">
                <a href="#service-tab" aria-controls="service-tab" role="tab" data-toggle="tab">
                    <i class="fa fa-list"></i> {!! trans('main.Services') !!}
                </a>
            </li>
            <li role="presentation">
                <a href="#tour-tab" aria-controls="tour-tab" role="tab" data-toggle="tab">
                    <i class="fa fa-suitcase"></i> {!! trans('main.Tour') !!}
                </a>
            </li>
            <li role="presentation">
                <a href="#quotation-tab" aria-controls="quotation-tab" role="tab" data-toggle="tab">
                    <i class="fa fa-calculator"></i> {!! trans('main.Quotation') !!}
                </a>
            </li>
            <li role="presentation">
                <a href="#roomlist-tab" aria-controls="roomlist-tab" role="tab" data-toggle="tab">
                    <i class="fa fa-bed"></i> {!! trans('main.GuestList') !!}
                </a>
            </li>
            <li role="presentation">
                <a href="#invoices-tab" aria-controls="invoices-tab" role="tab" data-toggle="tab">
                    <i class="fa fa-file-text"></i> Invoices
                </a>
            </li>
            <li role="presentation">
                <a href="#billing-tab" aria-controls="billing-tab" role="tab" data-toggle="tab">
                    <i class="fa fa-money"></i> Billing
                </a>
            </li>
        </ul>

        <div class="tab-content">
            {{-- Front Sheet Tab --}}
            <div role="tabpanel" class="tab-pane fade in active" id="frontsheet-tab">
                <h3><i class="fa fa-list"></i> Front Sheet</h3>
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
                                    <i class="fa fa-warning"></i>
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
                        <i class="fa fa-info-circle"></i> No quotation data available for front sheet.
                    </div>
                @endif
            </div>

            {{-- Services Tab --}}
            <div role="tabpanel" class="tab-pane fade" id="service-tab">
                <h3><i class="fa fa-list"></i> Services</h3>
                <div class="tour-packages"></div>
                
                {{-- Comments Section --}}
                <div class="box box-success">
                    <div class="box-header">
                        <i class="fa fa-comments-o"></i>
                        <h3 class="box-title">{!! trans('main.Comments') !!}</h3>
                    </div>
                    <div class="box-body">
                        <div id="show_comments"></div>
                    </div>
                    <div class="box-footer">
                        <form method="POST" action="{{ route('comment.store') }}" id="form_comment">
                            @csrf
                            <div class="form-group">
                                <textarea class="form-control" id="content" name="content" rows="3" placeholder="Ctrl + Enter to post comment"></textarea>
                            </div>
                            <input type="hidden" name="reference_id" value="{{ $tour->id }}">
                            <input type="hidden" name="reference_type" value="{{ \App\Comment::$services['tour'] ?? 'tour' }}">
                            <button type="submit" class="btn btn-success">
                                <i class="fa fa-send"></i> {!! trans('main.Send') !!}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Tour Info Tab --}}
            <div role="tabpanel" class="tab-pane fade" id="tour-tab">
                <h3><i class="fa fa-suitcase"></i> Tour Information</h3>
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered">
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
                        <table class="table table-bordered">
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
            <div role="tabpanel" class="tab-pane fade" id="quotation-tab">
                <h3><i class="fa fa-calculator"></i> Quotations</h3>
                @if (Auth::user()->can('quotation.add'))
                    <a href="{{ route('quotation.add', ['id' => $tour->id]) }}" class="btn btn-success mb-3">
                        <i class="fa fa-plus"></i> {!! trans('main.AddQuotation') !!}
                    </a>
                @endif
                
                <table class="table table-bordered table-striped">
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
                                        <i class="fa fa-print"></i>
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('quotation.excel', ['id' => $quotation->id]) }}" target="_blank" class="btn btn-sm btn-success">
                                        <i class="fa fa-file-excel-o"></i>
                                    </a>
                                </td>
                                <td>{{ Carbon\Carbon::parse($quotation->created_at)->format('d-m-Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">
                                    <i class="fa fa-inbox"></i> No quotations found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Guest List Tab --}}
            <div role="tabpanel" class="tab-pane fade" id="roomlist-tab">
                <h3><i class="fa fa-bed"></i> Guest Lists</h3>
                @if (Auth::user()->can('guestList.add'))
                    <a href="{{ route('guestList.add', ['id' => $tour->id]) }}" class="btn btn-success mb-3">
                        <i class="fa fa-plus"></i> Add Guest List
                    </a>
                @endif
                
                <table class="table table-bordered table-striped">
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
                                            <i class="fa fa-send"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger delete-guest-list"
                                                data-url="{{ route('guestlist.delete', ['id' => $tour->id, 'guestlistid' => $guestList->id]) }}">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">
                                    <i class="fa fa-inbox"></i> No guest lists found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Invoices Tab --}}
            <div role="tabpanel" class="tab-pane fade" id="invoices-tab">
                <h3><i class="fa fa-file-text"></i> Invoices</h3>
                {!! \App\Helper\PermissionHelper::getCreateButton(route('invoices.create'), \App\Invoices::class, 'btn btn-success mb-3') !!}
                
                <table class="table table-bordered table-striped">
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
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <a href="{{ route('invoices.edit', ['invoice' => $invoice['id']]) }}" class="btn btn-sm btn-warning">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">
                                    <i class="fa fa-inbox"></i> No invoices found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Billing Tab --}}
            <div role="tabpanel" class="tab-pane fade" id="billing-tab">
                <h3><i class="fa fa-money"></i> Billing</h3>
                {!! \App\Helper\PermissionHelper::getCreateButton(route('accounting.create'), \App\Tour::class, 'btn btn-success mb-3') !!}
                
                <table class="table table-bordered table-striped">
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
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <a href="{{ route('accounting.edit', ['accounting' => $billing['id']]) }}" class="btn btn-sm btn-warning">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">
                                    <i class="fa fa-inbox"></i> No billing records found
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
<div class="modal fade" id="service-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">{!! trans('main.Addservice') !!}</h4>
            </div>
            <div class="modal-body">
                <table id="search-table" class="table table-striped table-bordered">
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