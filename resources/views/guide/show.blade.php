@extends('scaffold-interface.layouts.app')
@section('title','Show')
@section('content')
    @include('layouts.title',
   ['title' => 'Guide', 'sub_title' => 'Guide Show',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Guides', 'icon' => 'street-view', 'route' => route('guide.index')],
   ['title' => 'Show', 'route' => null]]])
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="margin_button">
                            <a href="javascript:history.back()">
                                <button class='btn btn-primary'>{!!trans('main.Back')!!}</button>
                            </a>
                            <a href="{!! route('guide.edit', $guide->id) !!}">
                                <button class='btn btn-warning'>{!!trans('main.Edit')!!}</button>
                            </a>
                        </div>
                    </div>
                </div>
                <ul class="nav nav-tabs" role='tablist'>
                    <li role='presentation' class="active"><a href="#info-tab" aria-controls='info-tab' role='tab' data-toggle='tab'>{!!trans('main.Info')!!}</a></li>
                    <li role='presentation'><a href="#history-tab" aria-controls='history-tab' role='tab' data-toggle='tab'>{!!trans('main.History')!!}</a></li>
					<li role='presentation' class="tab" data-tab="invoices-tab"><a href="#invoices-tab" aria-controls='invoices-tab' role='tab'
                                data-toggle='tab' id="invoices_tab" >{!! trans('Invoices') !!}</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade in active" role='tabpanel' id='info-tab'>
                        <table class='table_show col-lg-6 table table-bordered'>
                            <tbody>
                            <tr>
                                <td>
                                    <b><i>{!!trans('main.Name')!!} : </i></b>
                                </td>
                                <td class="info_td_show">{!!$guide->name!!}</td>
                            </tr>
                            <tr>
                                <td>
                                    <b><i>{!!trans('main.Company')!!} : </i></b>
                                </td>
                                <td class="info_td_show">{!!$guide->company!!}</td>
                            </tr>

                            <tr>
                                <td>
                                    <b><i>{!!trans('main.AddressFirst')!!} : </i></b>
                                </td>
                                <td class="info_td_show">{!!$guide->address_first!!}</td>
                            </tr>
                            <tr>
                                <td>
                                    <b><i>{!!trans('main.AddressSecond')!!} : </i></b>
                                </td>
                                <td class="info_td_show">{!!$guide->address_second!!}</td>
                            </tr>
                            <tr>
                                <td>
                                    <b><i>{!!trans('main.Country')!!} : </i></b>
                                </td>
								@if(!empty($guide->country))
								<td class="info_td_show">{!! \App\Helper\CitiesHelper::getCountryById($guide->country)['name']!!}</td>
								@else
								<td class="info_td_show"></td>
								@endif
                            </tr>
                            <tr>
                                <td>
                                    <b><i>{!!trans('main.City')!!} : </i></b>
                                </td>
								@if(!empty($guide->city))
								<td class="info_td_show">{!! \App\Helper\CitiesHelper::getCityById($guide->city)['name']!!}</td>
								@else
								<td class="info_td_show"></td>
								@endif
                            </tr>
                            <tr>
                                <td>
                                    <b><i>{!!trans('main.Code')!!} : </i></b>
                                </td>
                                <td class="info_td_show">{!!$guide->code!!}</td>
                            </tr>

                            <tr>
                                <td>
                                    <b><i>{!!trans('main.WorkPhone')!!} : </i></b>
                                </td>
                                <td class="info_td_show">{!!$guide->work_phone!!}</td>
                            </tr>
                            <tr>
                                <td>
                                    <b><i>{!!trans('main.WorkFax')!!} : </i></b>
                                </td>
                                <td class="info_td_show">{!!$guide->work_fax!!}</td>
                            </tr>
                            </tbody>
                        </table>
                        <table class='table_show col-lg-6 table table-bordered'>
                            <tbody>
                            <tr>
                                <td>
                                    <b><i>{!!trans('main.WorkEmail')!!} : </i></b>
                                </td>
                                <td class="info_td_show">{!!$guide->work_email!!}</td>
                            </tr>
                            <tr>
                                <td>
                                    <b><i>{!!trans('main.IntComments')!!} : </i></b>
                                </td>
                                <td class="info_td_show">{!!$guide->int_comments!!}</td>
                            </tr>
                            <tr>
                                <td>
                                    <b><i>{!!trans('main.Comments')!!} : </i></b>
                                </td>
                                <td class="info_td_show">{!!$guide->comments!!}</td>
                            </tr>
                            <tr>
                                <td>
                                    <b><i>{!!trans('main.ContactName')!!} : </i></b>
                                </td>
                                <td class="info_td_show">{!!$guide->contact_name!!}</td>
                            </tr>
                            <tr>
                                <td>
                                    <b><i>{!!trans('main.ContactPhone')!!} : </i></b>
                                </td>
                                <td class="info_td_show">{!!$guide->contact_phone!!}</td>
                            </tr>
                            <tr>
                                <td>
                                    <b><i>{!!trans('main.ContactEmail')!!} : </i></b>
                                </td>
                                <td class="info_td_show">{!!$guide->contact_email!!}</td>
                            </tr>
                            <tr>
                                <td>
                                    <b><i>{!!trans('main.Criterias')!!} : </i></b>
                                </td>
                                @forelse($criterias as $criteria)
                                    @forelse($guide->criterias as $item)
                                        @if($criteria->id == $item->criteria_id)
                                            <td class="info_td_show criteria_block" style="display: block">{!!$criteria->name!!}</td>
                                        @endif
                                    @empty
                                        <td class="info_td_show"></td>
                                    @endforelse
                                @empty
                                    <td class="info_td_show"></td>
                                @endforelse
                            </tr>
                            <tr>
                                <td>
                                    <b><i>{!!trans('main.Rate')!!} : </i></b>
                                </td>
                                <td class="info_td_show">{!!$guide->rate_name!!}</td>
                            </tr>
                            <tr>
                                <td>
                                    <b><i>{!!trans('main.Website')!!} : </i></b>
                                </td>
                                <td class="info_td_show">{!!$guide->website!!}</td>
                            </tr>
                            </tbody>
                        </table>
                        <div class="clearfix"></div>
                @component('component.files', ['files' => $files])@endcomponent
        <span id="showPreviewBlock" data-info="{{ true }}"></span>
        <div class="box box-success" style="position: relative; left: 0px; top: 0px;">
            <div class="box-header ui-sortable-handle" style="cursor: move;">
                <i class="fa fa-comments-o"></i>

                <h3 class="box-title">{!!trans('main.Comments')!!}</h3>
            </div>
            <div class="box-body">
                <div class="slimScrollDiv" style="position: relative; overflow-y: scroll;  width: auto;">
                    <div class="box-body box chat" id="chat-box" style="width: auto; height: auto;">
                        <div id="show_comments"></div>
                    </div>
                    <div class="slimScrollRail" style="width: 7px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; background: rgb(51, 51, 51) none repeat scroll 0% 0%; opacity: 0.2; z-index: 90; right: 1px;"></div>
                </div>
            </div>
            <!-- /.chat -->
            <div class="box-footer">
                <form method='POST' action='{{route('comment.store')}}' enctype="multipart/form-data" id="form_comment">
                    <div class="input-group" style="width: 100%">
                                        <span id="author_name" class="input-group-addon">
                                            <span id="name"></span>
                                            <a href="#" id="reply_close"><i class="fa fa-close"></i></a>
                                        </span>
                        <textarea class="form-control" id="content" name="content" placeholder="Ctrl + Enter to post comment"></textarea>
                    </div>
                    <div class="form-group">
                        <label>{!!trans('main.Files')!!}</label>
                        @component('component.file_upload_field')@endcomponent
                    </div>
                    <input type="text" id="parent_comment" hidden name="parent" value="{{ null }}">
                    <input type="text" id="default_reference_id" hidden name="reference_id" value="{{ $guide->id }}">
                    <input type="text" id="default_reference_type" hidden name="reference_type" value="{{ \App\Comment::$services['guide']}}">

                    <button type="submit" class="btn btn-success pull-right" id="btn_send_comment" style="margin-top: 5px;">{!!trans('main.Send')!!}</button>
                </form>
            </div>
        </div>
                    </div>
                    <div class="tab-pane fade" role='tabpanel' id='history-tab'>
                        <div id='history-container'></div>
                    </div>
					<div role="tabpanel" class="tab-pane fade in" id="invoices-tab">
					<div id="tour_create" style="margin-bottom : 20px;">
                    {!! \App\Helper\PermissionHelper::getCreateButton(route('invoices.create'), \App\Invoices::class) !!}
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" id="guideInvoiceSearchInput" class="form-control" placeholder="Search invoices..." onkeyup="filterGuideInvoiceTable()">
                        </div>
                    </div>
                    <div class="col-md-6 text-right">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-primary" onclick="exportGuideInvoicesToCSV()">Export CSV</button>
                            <button type="button" class="btn btn-success" onclick="exportGuideInvoicesToExcel()">Export Excel</button>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="inovices-table" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Invoice No</th>
                                <th>Due Date</th>
                                <th>Received Date</th>
                                <th>Tour</th>
                                <th>Service</th>
                                <th>Office Name</th>
                                <th>Total Price</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($invoices) && $invoices->count() > 0)
                                @foreach($invoices as $invoice)
                                    <tr>
                                        <td>{{ $invoice->id }}</td>
                                        <td>{{ $invoice->invoice_no ?? 'N/A' }}</td>
                                        <td>{{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('Y-m-d') : 'N/A' }}</td>
                                        <td>{{ $invoice->received_date ? \Carbon\Carbon::parse($invoice->received_date)->format('Y-m-d') : 'N/A' }}</td>
                                        <td>{{ $invoice->tour_name ?? 'N/A' }}</td>
                                        <td>{{ $invoice->service_name ?? 'N/A' }}</td>
                                        <td>{{ $invoice->office_name ?? 'N/A' }}</td>
                                        <td>{{ number_format($invoice->total_amount ?? 0, 2) }}</td>
                                        <td>
                                            <span class="badge badge-{{ $invoice->status == 'paid' ? 'success' : ($invoice->status == 'pending' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($invoice->status ?? 'pending') }}
                                            </span>
                                        </td>
                                        <td>
                                            @include('component.action_buttons', [
                                                'routePrefix' => 'invoices',
                                                'item' => $invoice,
                                                'showEdit' => true,
                                                'showDelete' => true,
                                                'showView' => true
                                            ])
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="10" class="text-center">No invoices found for this guide</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                @if(isset($invoices) && method_exists($invoices, 'links'))
                    <div class="row">
                        <div class="col-md-12">
                            {{ $invoices->links() }}
                        </div>
                    </div>
                @endif



                </div>
                </div>
                
            </div>
        </div>
    </section>
<span id="services_name" data-service-name='Guide' data-history-route="{{route('services_history', ['id' => $guide->id])}}"></span>
@endsection

@section('post_scripts')
    <script src="{{ asset('js/comment.js') }}"></script>
    <script src="{{ asset('js/bootstrap-tables.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Initialize Bootstrap table functionality
            initializeBootstrapTable('inovices-table');
        });

        // Guide invoice table search functionality
        function filterGuideInvoiceTable() {
            const input = document.getElementById('guideInvoiceSearchInput');
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

        // Export functions for guide invoices
        function exportGuideInvoicesToCSV() {
            exportTableToCSV('inovices-table', 'guide-invoices.csv');
        }

        function exportGuideInvoicesToExcel() {
            exportTableToExcel('inovices-table', 'guide-invoices');
        }
    </script>
@endsection