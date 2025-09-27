@extends('scaffold-interface.layouts.app')
@section('title', 'Create')
@section('content')
    @include('layouts.title', [
        'title' => 'Client Invoices',
        'sub_title' => 'Create Billing',
        'breadcrumbs' => [
            ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
            ['title' => 'Invoices', 'icon' => 'suitcase', 'route' => route('tour.index')],
            ['title' => 'Create', 'route' => null],
        ],
    ])
    <section class="content">
        <form method='POST' action='{!! url('accounting') !!}' enctype="multipart/form-data">

            <div class="margin_button">
                <a href="javascript:history.back()">
                    <button type="button" class='btn btn-primary back_btn'>{!! trans('main.Back') !!}</button>
                </a>
                <button class='btn btn-success' type='submit'>{!! trans('main.Save') !!}</button>
            </div>

            <div class="box">
                <div class="box box-body border_top_none">


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

                            <div class="form-group">
                                <label for="currency">Currency</label>
                                <select name="currency" id="currency" class="form-control" required>
                                    <option value="" disabled="disabled" selected="selected">Choose option</option>
                                    <option value="EUR">Euro
                                    </option>
                                    <option value="USD">Dollar</option>
                                    <option value="CHF">Swiss Franks</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="office_id">{{ trans('Office') }}</label>
                                <select name="office_id" id="office_id" class="form-control">

                                    @foreach ($offices as $office)
                                        <option value="{{ $office->id }}">{{ $office->office_name }}</option>
                                    @endforeach
                                </select>
                            </div>


                            {{--
                          <div  class="form-group">
                                    <label for="name">{!! trans('Service') !!} *</label>
                                    <select id="serviceDropdown"class="form-control" >
                                        <option name = "service"selected>{!! trans('Hotels') !!}</option>
                                        @foreach ($options as $option)
                                            <option>
                                                @if ($option === 'Transfer')
                                                    Bus Company
                                                @else
                                                    {{ $option }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                           --}}





                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="tour_id">{{ trans('main.Tour') }}</label>
                                <select name="tour_id" id="tour_id" class="form-control">

                                    @foreach ($tours as $tour)
                                        <option value="{{ $tour->id }}">{{ $tour->name }}</option>
                                    @endforeach
                                </select>
                            </div>
							<div class="form-group">
                                <label for="name">{!! trans('Extra Cost') !!} </label>
                                {!! Form::text('extra_cost', '', ['class' => 'form-control', '']) !!}
                            </div>
                            @if (empty($quotation))
                                <input id="quotation_id" type="hidden" name="quotation_id" value="">
                            @else
                                <input id="quotation_id" type="hidden" name="quotation_id" value="{{ $quotation->id }}">
                            @endif


                            <div class="form-group" id="services" style="display:none">
                                <label for="service">{{ trans('Service') }}</label>
                                <select id="service" name="service" id="service" class="form-control">

                                </select>
                            </div>
                            <div class="form-group" id="service_div">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="client_id">{{ trans('Client') }}</label>
                                <select name="client_id" id="client_id" class="form-control">

                                    @foreach ($clients as $client)
                                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                                    @endforeach
                                </select>
                            </div>
							<div class="form-group" id="services">
                                <label for="service">{{ trans('Note') }}</label>
                                <textarea id="note" name="note" id="note" class="form-control" >

                                </textarea>
                            </div>
                        </div>
						
							
                    </div>


                </div>
            </div>
            <div class="box box-secondry">
                <div class="box box-body ">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <h1>Extra Items</h1>
                            </div>
                            <div>Extra Items In this Invoice?</div>
                        </div>
                        <div class="margin_button">
                            <button class='btn btn-success' id="add_contact" type='button'>
                                <i class="fa fa-plus"></i>
                                {!! trans('Extra Items') !!}
                            </button>
                        </div>

                        <div id="items-contacts" class="row col-md-10">
                            <div class="item-contact row " style="margin-bottom: 0px">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="item_name">Item Name</label>
                                        <input id="item_name" name="items[1][item_name]" type="text" class="form-control"
                                            value="" required="">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group" style="padding-left: 0">
                                        <label for="item_desc">Quantity</label>
                                        <input id="item_desc" name="items[1][quantity]" type="number" class="form-control"
                                            onchange="calculateItemTotal(this)" value="" required="">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group " style="padding-right: 0">
                                        <label for="amount">Price(excl. VAT)</label>
                                        <input id="amount" name="items[1][amount]" type="number"
                                            class="form-control" onchange="calculateItemTotal(this)" required="">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="vat">VAT Rate</label>
                                        <select name="items[{{$count}}][vat]"  id="vat" class="form-control" onchange = "calculateItemTotal(this)" required>
									<option value="" disabled="disabled" selected="selected"  >Choose option</option>
									@foreach($taxes as $tax)
									<option value="{{$tax->value/100}}">{{$tax->name}}</option>
									@endforeach
                                  
                                </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group " style="padding-right: 0">
                                        <label for="total_amount">Total Amount</label>
                                        <input id="total_amount" name="items[1][total_amount]" type="number"
                                            class="form-control item_total" value="" readonly="">
                                    </div>
                                </div>
                                


                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="box box-secondry">
                <div class="box box-body ">


                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <h1>Payment</h1>
                            </div>
                            <div>How is the client paying you?</div>
                        </div>


                        <div class="margin_button">
                            <button class='btn btn-success' id="add_feild_button" type='button'>
                                <i class="fa fa-plus"></i>
                                {!! trans('Add Payment') !!}
                            </button>
                        </div>


                        <div id="payment-inputs" class="row col-md-10">

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
    <script type="text/javascript" src='{{ asset('js/rooms.js') }}'></script>
    <script type="text/javascript" src='{{ asset('js/hide_elements.js') }}'></script>

    <script type="text/javascript">
        function readURL(input) {

            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#pic').attr('src', e.target.result);
                    $('#file-caption-name').html(input.files[0].name);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#imgInp").change(function() {
            readURL(this);
        });





        let contactItemCount = 1;


        function item_invoice_ajax() {
            $.ajax({
                url: '/api/getItemInvoiceView',
                method: 'GET',
                data: {
                    itemCount: contactItemCount + 1
                }
            }).done((res) => {
                contactItemCount++;
                $('#items-contacts').append(res);
                $('input[name="_token"]').each(function() {
                    // Replace the 'value' attribute with your CSRF token value
                    $(this).val("{{ csrf_token() }}");
                });
            });
        }

        function payment_view_ajax() {
            $.ajax({
                url: '/api/getPaymentView',
                method: 'GET',
                data: {
                    itemCount: contactItemCount + 1
                }
            }).done((res) => {
                contactItemCount++;
                $('#payment-inputs').append(res);
                $('input[name="_token"]').each(function() {
                    // Replace the 'value' attribute with your CSRF token value
                    $(this).val("{{ csrf_token() }}");
                });
            });
        }

        $('#add_contact').on('click', function() {
            item_invoice_ajax();
        });


        $('#add_feild_button').on('click', function() {
            payment_view_ajax();
        });
        $(document).on('click', '#delete_contact_item', function() {
            $(this).closest('.item-contact').remove();
        });

        $("#tour_id").change(function() {

            let tour_id = $(this).val();

            $.ajax({
                type: "GET",
                url: `api/getTourquotation/${tour_id}`,
                success: function(result) {
                    $("#quotation_id").val(result);
                    console.log(result);
                },
                error: function(result) {
                    console.log(result);
                }
            });
        })


        function calculateItemTotal(iteminput) {

            const itemContainer = iteminput.parentElement.parentElement.parentElement;


            const quantity = parseFloat(itemContainer.querySelector("#item_desc").value) || 0;

            const price = parseFloat(itemContainer.querySelector("#amount").value) || 0;
            const vat = parseFloat(itemContainer.querySelector("#vat").value) || 0;

            const total_price = quantity * price;
            const total_tax = total_price * vat;
            const itemTotal = total_price + total_tax;

            // Update the item total displayed for this item
            itemContainer.querySelector("#total_amount").value = itemTotal.toFixed(2);

            // Recalculate the overall total
            calculateOverallTotal();
        }

        function calculateOverallTotal() {
            const itemTotals = document.querySelectorAll(".item-total");
            let overallTotal = 0;

            itemTotals.forEach(itemTotal => {
                overallTotal += parseFloat(itemTotal.textContent);
            });

            // Update the overall total
            // document.getElementById("total").textContent = overallTotal.toFixed(2);
        }
    </script>
@endsection
