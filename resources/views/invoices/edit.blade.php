@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Edit Supplier Invoice')
@section('content')
    @include('layouts.title', [
        'title' => 'Supplier Invoice',
        'sub_title' => 'Edit Invoice',
        'breadcrumbs' => [
            ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
            ['title' => 'Invoices', 'icon' => 'suitcase', 'route' => route('tour.index')],
            ['title' => 'Create', 'route' => null],
        ],
    ])

    <style>
        .cloned-container {
            margin-left: 210px;
            /* Adjust the margin as needed */
        }

        .button_div {
            margin-top: 0px;
            /* Adjust the margin as needed */
        }

        

        /* Style the button */
        .custom-button {
            background-color:darkslategray; /* Green background color */
            color: white; /* White text color */
            border: none; /* No border */
            padding: 10px 20px; /* Padding around the text */
            font-size: 18px; /* Font size */
            border-radius: 5px; /* Rounded corners */
            cursor: pointer; /* Cursor style on hover */
        }

        /* Style the button on hover */
        .custom-button:hover {
            background-color: gray; /* Darker green on hover */
        }

        /* Style the icon inside the button */
        .custom-button i {
            margin-right: 5px; /* Spacing between icon and text */
        }
    </style>
<section class="content">
        <form method='POST' action='{{route('invoice.update', ['id' => $invoices->id])}}' enctype="multipart/form-data">
            <div class="box box-secondry">
                <div class="box box-body ">

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
                        <div class="col-md-2">
                            <div class="form-group">
                                <h1>Invoice Detail</h1>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <input type='hidden' name='_token' value='{{ Session::token() }}'>
			<input id="invoice_id" value="{{$invoices->id}}" type="hidden">
                           
							<div class="form-group">
                                <label for="office_id">{{ trans('Office') }}</label>
                                <select name="office_id" id="office_id" class="form-control" required>

                                    @foreach ($offices as $office)
                                        <option value="{{ $office->id }}" {{  $invoices->office_id == $office->id? 'selected' : '' }}>{{ $office->office_name }}</option>
                                    @endforeach
                                </select>
                            </div>
							 <div class="form-group">
                                <label for="invoice_no">{!! trans('Invoice No') !!} *</label>
								 <input id="invoice_no" class="form-control" required="" name="invoice_no" type="text" value="{{ old('invoice_no', $invoices->invoice_no) }}">
                            </div>


                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>{{ trans('main.Tour') }}</label>
                                <div class="form-control" style="height:auto">{{ $invoices->activeTours->pluck('tours.name')->filter()->unique()->implode(', ') ?: 'No linked tour' }}</div>
                            </div>
							
							
                            <div class="form-group" id="services" >
							
                            </div>
                            <div class="form-group" id="service_div">
                            </div>
                        </div>
                        <div class="col-md-3">
                           
							<div class="form-group" required>
                                <label for="total_amount">{!! trans('Total Amount') !!} *</label>
				<input id="total_amount" class="form-control" required="" name="total_amount" type="number" min="0" step="0.01" value="{{ old('total_amount', $invoices->total_amount) }}">
                            </div>
                            <div class="form-group"><label for="extra_amount">Extra Cost</label><input id="extra_amount" class="form-control" name="extra_amount" type="number" min="0" step="0.01" value="{{ old('extra_amount', $invoices->extra_amount) }}"></div>
                            <div class="form-group"><label for="received_date">Received Date</label><input id="received_date" class="form-control" required name="received_date" type="date" value="{{ old('received_date', \Carbon\Carbon::parse($invoices->receivedDate)->format('Y-m-d')) }}"></div>
                            <div class="form-group"><label for="due_date">Due Date</label><input id="due_date" class="form-control" required name="due_date" type="date" value="{{ old('due_date', \Carbon\Carbon::parse($invoices->dueDate)->format('Y-m-d')) }}"></div>
                            
                        </div>
                    </div>

                </div>
            </div>

            <div class="box box-secondry">
                <div class="box box-body ">
    
    
                    <div class="row">
                        <div class="col-md-12">
                            <h3>Supplier payments</h3>
                            <p>Payments remain attached to this invoice. Use the payment form on the invoice details page to add a payment.</p>
                            <a class="btn btn-outline-primary" href="{{ route('invoices.show', $invoices->id) }}">View invoice and payments</a>
                        </div>
    
                    </div>
                    
    
    
    
    
    
    
                </div>
            </div>
            <a href="javascript:history.back()">
                <button type="button" class='btn btn-primary back_btn'>{!! trans('main.Back') !!}</button>
            </a>
            <button class='btn btn-success' type='submit'>{!! trans('main.Save') !!}</button>
        </form>
    </section>
@endsection
