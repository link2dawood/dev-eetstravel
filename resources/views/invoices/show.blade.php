@extends('scaffold-interface.layouts.tabler-app')
@section('title','Show')
@section('content')
    @include('layouts.title',
   ['title' => 'Supplier Invoices', 'sub_title' => 'accounting Show',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'accountings', 'icon' => 'handshake-o', 'route' => route('accounting.index')],
   ['title' => 'Show', 'route' => null]]])
<section class="content">
    <div class="box box-primary">
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="margin_button">
                        <a href="{{ route('invoices.index') }}">
                            <button class='btn btn-primary'>{!!trans('main.Back')!!}</button>
                        </a>
						
						{{--
                        <a href="{!! route('invoices.edit', $invoices->id) !!}">
                            <button class='btn btn-warning'>{!!trans('main.Edit')!!}</button>
                        </a>--}}
                    </div>
                </div>
            </div>
            <div id="fixed-scroll" class="nav-tabs-custom">
                <ul class="nav nav-tabs" id="fixed-scroll" role='tablist'>
                    <li role='presentation' class="active"><a class="nav-link active" href="#info-tab" aria-controls='info-tab' role='tab' data-bs-toggle='tab'>{!!trans('main.Info')!!}</a></li>
				</ul>
                 
                   
            </div>
            <div class="tab-content">
                <div class="tab-pane fade show active" role='tabpanel' id='info-tab'>
					<input id="invoice_id" type="hidden" name="invoice_id" value ="{{$invoices->id}}">
					<div class="row">
					<div class="col-lg-6">
					<table class = 'table_show table table-bordered'>
                        <tbody>
                       
                        <tr>
                            <td>
                                <b><i>{!!trans('Due Date')!!} : </i></b>
                            </td>
                            <td class="info_td_show">{{ $invoices->dueDate }}</td>
                        </tr>
						<tr>
                            <td>
                                <b><i>{!!trans('Received Date')!!} : </i></b>
                            </td>
                            <td class="info_td_show">{{ $invoices->receivedDate }}</td>
                        </tr>
                        <tr>
                            <td>
                                <b><i>{!!trans('Invoice No')!!} : </i></b>
                            </td>
                            <td class="info_td_show">{{ $invoices->invoice_no; }}</td>
                        </tr>
                        </tbody>
                    </table>
					</div>
						
					<div class="col-lg-6">
						
                    <table class = 'table_show table table-bordered'>
                        <tbody>
                        <input id="transaction_id" type="hidden" name="transaction_id" value = {{$invoices->id}}>
                        <tr>
                            <td>
                                <b><i>{!!trans('Office Name')!!} : </i></b>
                            </td>
                            <td class="info_td_show">{{$office->office_name}}</td>
                        </tr>
                        <tr>
                            <td>
                                <b><i>{!!trans('Total Price')!!} : </i></b>
                            </td>
                            <td class="info_td_show">{{$invoices->total_amount}}</td>
                        </tr>
							 <tr>
                            <td>
                                <b><i>{!!trans('Status')!!} : </i></b>
                            </td>
                            <td class="info_td_show">{{$invoices->status($invoice_tour)}}</td>
                        </tr>
                        </tbody>
                    </table>
					</div>
					<div class="col-lg-6">
                    <table class = 'table_show table table-bordered'>
                        <tbody>
                       		<tr>
                            <td>
                                <b><i>{!!trans('Tour')!!} : </i></b>
                            </td>
                            <td class="info_td_show">{{$invoice_tour->tours->name??""}}</td>
                        </tr>
							 <tr>
                            <td>
                                <b><i>{!!trans('Service')!!} : </i></b>
                            </td>
                            <td class="info_td_show">{{$invoice_tour->package->name??""}}</td>
                        </tr>
                       
                        </tbody>
                    </table>
					</div>
					</div>
					
                    <div style="clear: both"></div>
					<div class="">
					<h3 class="box-title">Paid To Payment Amount</h3>
					<div class="table-responsive">
					<div id="payment_create" style="margin-bottom : 20px;">
                    	{!! \App\Helper\PermissionHelper::getCreateButton(route('add_payment',$invoices->id), \App\Invoices::class) !!}
               		 </div>
					@include('component.invoice_payment_table', ['invoice' => $invoices, 'payTo' => 'Supplier'])
						</div>
				</div>
           

                    @component('component.files', ['files' => $files, 'invoices' => $invoices]) @endcomponent
                </div>
				
				
				<div class="box box-success" style="position: relative; left: 0px; top: 0px; border-top: none">
                            <div class="box-header ui-sortable-handle" style="cursor: move;">
                                <i class="fa fa-comments-o"></i>

                                <h3 class="box-title">{!! trans('main.Comments') !!}</h3>
                            </div>
                            <div class="box-body">
                                <div class="slimScrollDiv" style="position: relative; overflow-y: scroll;  width: auto;">
                                    <div class="box-body box chat" id="chat-box" style="width: auto; height: auto;">
                                        <div id="show_comments"></div>
                                    </div>
                                    <div class="slimScrollRail"
                                        style="width: 7px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; background: rgb(51, 51, 51) none repeat scroll 0% 0%; opacity: 0.2; z-index: 90; right: 1px;">
                                    </div>
                                </div>
                            </div>
                            <!-- /.chat -->
                            <div class="box-footer">
                                <form method='POST' action='{{ route('comment.store') }}' enctype="multipart/form-data"
                                    id="form_comment">
                                    @csrf
                                    <div class="input-group" style="width: 100%">
                                        <span id="author_name" class="input-group-addon">
                                            <span id="name"></span>
                                            <a href="#" id="reply_close"><i class="fa fa-close"></i></a>
                                        </span>
                                        <textarea class="form-control" id="content" name="content" placeholder="Ctrl + Enter to post comment"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>{!! trans('main.Files') !!}</label>
                                        @component('component.file_upload_field')
                                        @endcomponent
                                    </div>
                                    <input type="text" id="parent_comment" hidden name="parent"
                                        value="{{ null }}">
                                    <input type="text" id="default_reference_id" hidden name="reference_id"
                                        value="{{ $invoices->id }}">
                                    <input type="text" id="default_reference_type" hidden name="reference_type"
                                        value="{{ \App\Comment::$services['invoices'] }}">

                                    <button type="submit" class="btn btn-success pull-right" id="btn_send_comment"
                                        style="margin-top: 5px;">{!! trans('main.Send') !!}
                                    </button>
                                </form>
                            </div>
                        </div>
                               
            </div>
</section>
    <span id="services_name" data-service-name='accounting' data-history-route="{{route('services_history', ['id' => $invoices->id])}}"></span>
@endsection

@section('post_scripts')
    <script src="{{ asset('js/comment.js') }}"></script>
@endsection
