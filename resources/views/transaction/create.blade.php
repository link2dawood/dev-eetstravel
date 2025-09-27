@extends('scaffold-interface.layouts.app')
@section('title', 'Create')
@section('content')
    @include('layouts.title', [
        'title' => 'Customer Transactions',
        'sub_title' => 'Create Transaction',
        'breadcrumbs' => [
            ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
            ['title' => 'Invoices', 'icon' => 'suitcase', 'route' => route('tour.index')],
            ['title' => 'Create', 'route' => null],
        ],
    ])
    <section class="content">
        <div class="box box-primary">
            <div class="box box-body border_top_none">
                <form method='POST' action='{!! url('transaction') !!}' id="data-form" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    @if (count($errors) > 0)
                        <br>
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-md-12">
                            <div class="margin_button">
                                <a href="javascript:history.back()">
                                    <button type="button" class='btn btn-primary back_btn'>{!! trans('main.Back') !!}</button>
                                </a>
                                <button class='btn btn-success' type='submit'>{!! trans('main.Save') !!}</button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <input type='hidden' name='_token' value='{{ Session::token() }}'>
                            <div class="alert alert-danger" style="display:none;" id="error_block">
                            </div>
                            <div class="form-group">
                                <label for="name">{!! trans(' Date') !!} *</label>
                                {!! Form::text('date', '', ['class' => 'form-control pull-right datepicker', 'id' => 'date']) !!}
                            </div>

                            <div class="form-group">
                                <label for="account_id">Pay:</label>
                                <select name="pay_to" id="transaction_criteria" required>
                                    <option value="Client">Client</option>
                                    <option value="Supplier">Supplier</option>
                                </select><br>
                            </div>
                            <div class="form-group" id="client_invoices">

                                <label for="account_id">Invoices:</label>
                                <select name="invoice_id" id="client_input" required>
                                    @foreach ($client_invoices as $client_invoice)
                                        <option value="{{ $client_invoice->id }}">{{ $client_invoice->invoice_no }}
                                        </option>
                                    @endforeach
                                </select><br>

                            </div>
                            <div class="form-group" id="supplier_invoices">
                                <label for="account_id">Invoices:</label>
                                <select name="invoice_id" id="supplier_input" required>
                                    @foreach ($invoices as $invoice)
                                        <option value="{{ $invoice->id }}">{{ $invoice->invoice_no }}</option>
                                    @endforeach
                                </select><br>
                            </div>
                            

                            <div class="form-group">
                                <label for="name">{!! trans('Paid') !!} *</label>
                                {!! Form::number('amount', '', ['class' => 'form-control', 'id' => 'amount']) !!}
                            </div>


                        </div>
                    </div>

                </form>
            </div>
        </div>
       
       
    </section>
    <script>
        $("#supplier_invoices").hide();
        $("#supplier_account_div").hide();

        $("#client_invoices").show();
        $("#supplier_input").prop('disabled', true);

        $("#transaction_criteria").on("change", function() {
            let selected_value = $(this).val();

            if (selected_value === "Client") {
                $("#supplier_invoices").hide();
                $("#client_invoices").show();
                $("#supplier_account_div").hide();
                $("#client_account_div").show();
                $("#supplier_input").prop('disabled', true);
                $("#client_input").prop('disabled', false);
            } else {
                $("#supplier_invoices").show();
                $("#client_invoices").hide();
                $("#supplier_account_div").show();
                $("#client_account_div").hide();

                $("#client_input").prop('disabled', true);
                $("#supplier_input").prop('disabled', false);

            }
        });

   
    </script>
@endsection
