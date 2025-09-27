@extends('scaffold-interface.layouts.app')
@section('title','Show')
@section('content')
 @include('layouts.title',
   ['title' => 'Customer Transaction', 'sub_title' => 'accounting Show',
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
                        <a href="javascript:history.back()">
                            <button class='btn btn-primary'>{!!trans('main.Back')!!}</button>
                        </a>
                    
						
						{{--
                        <a href="{!! route('invoices.edit', $transaction->id) !!}">
                            <button class='btn btn-warning'>{!!trans('main.Edit')!!}</button>
                        </a>--}}
                    </div>
                </div>
            </div>
            <div id="fixed-scroll" class="nav-tabs-custom">
                <ul class="nav nav-tabs" id="fixed-scroll" role='tablist'>
                    <li role='presentation' class="active"><a href="#info-tab" aria-controls='info-tab' role='tab' data-toggle='tab'>{!!trans('main.Info')!!}</a></li>
                 
                   
            </div>
            <div class="tab-content">
                <div class="tab-pane fade in active" role='tabpanel' id='info-tab'>
					<input id="invoiceId" type="hidden" name="invoiceId" value ="{{$transaction->id}}">
                    <table class = 'table_show table table-bordered col-lg-6'>
                        <tbody>
                        <input id="transaction_id" type="hidden" name="transaction_id" value = {{$transaction->id}}>
						<tr>
                            <td>
                                <b><i>{!!trans('Date')!!} : </i></b>
                            </td>
                            <td class="info_td_show">{{$transaction->date}}</td>
                        </tr>
						<tr>
                            <td>
                                <b><i>{!!trans('Description')!!} : </i></b>
                            </td>
                            <td class="info_td_show">{{$transaction->date}}</td>
                        </tr>
                        <tr>
                            <td>
                                <b><i>{!!trans('Payment To')!!} : </i></b>
                            </td>
                            <td class="info_td_show">{{$transaction->pay_to}}</td>
                        </tr>
                        <tr>
                            <td>
                                <b><i>{!!trans('Invoice No')!!} : </i></b>
                            </td>
                            <td class="info_td_show">{{$transaction->invoice_no}}</td>
                        </tr>
                        </tbody>
                    </table>
                   
                    <table class = 'table_show table table-bordered col-lg-6'>
                        <tbody>
                        <input id="transaction_id" type="hidden" name="transaction_id" value = {{$transaction->id}}>
                        <tr> 
                            <td>
                                <b><i>{!!trans('Transaction No  ')!!} : </i></b>
                            </td>
                            <td class="info_td_show">{{$transaction->date}}</td>
                        </tr>
                        <tr>
                            <td>
                                <b><i>{!!trans('Reference No')!!} : </i></b>
                            </td>
                            <td class="info_td_show">{{$transaction->date}}</td>
                        </tr>
						 
						 <tr>
                            <td>
                                <b><i>{!!trans('Amount')!!} : </i></b>
                            </td>
                            <td class="info_td_show">{{$transaction->amount}}</td>
                        </tr>
                        </tbody>
                    </table>
                    
               
            </div>
</section>
@endsection