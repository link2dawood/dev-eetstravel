@extends('scaffold-interface.layouts.app')
@section('title', 'Edit')
@section('content')
    @include('layouts.title', [
        'title' => 'Customer Transaction',
        'sub_title' => 'Edit Billing',
        'breadcrumbs' => [
            ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
            ['title' => 'Invoices', 'icon' => 'suitcase', 'route' => route('tour.index')],
            ['title' => 'Create', 'route' => null],
        ],
    ])
    <section class="content">
		<form method='post' action="{{route('inv_payment.store', ['id' => $transactions->id])}}" enctype="multipart/form-data">
		<div class="margin_button">
                <a href="javascript:history.back()">
                    <button type="button" class='btn btn-primary back_btn'>{!! trans('main.Back') !!}</button>
                </a>
                <button class='btn btn-success' type='submit'>{!! trans('main.Save') !!}</button>
            </div>
       
			
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

      
		  $("#tour_id").change(function() {

			  let tour_id = $(this).val(); 
			  
			  $.ajax({
				  type: "GET",
					url:`/accounting/api/getTourquotation/${tour_id}`,
				  success: function(result) {
					  $("#quotation").html(result);
					console.log(result);
				  },
				  error: function(result) {
					console.log(result);
				  }
			  });
		  })
		
		
        let contactItemCount = 0;
        invoice_items();
        invoice_payments();
		function invoice_items() {
			let invoice_id = $("#invoice_id").val();
            $.ajax({
                url: '/api/getInvoiceItem',
                method: 'GET',
                data: {
                    itemCount: contactItemCount + 1,
					invoice_id:invoice_id, 
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
        function item_invoice_ajax() {
            $.ajax({
                url: '/api/getItemInvoiceView',
                method: 'GET',
                data: {
                    itemCount: contactItemCount + 1,
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
		function invoice_payments() {
			let invoice_id = $("#invoice_id").val();
            $.ajax({
                url: '/api/getInvoicePayments/1',
                method: 'GET',
                data: {
                    itemCount: contactItemCount + 1,
					invoice_id:invoice_id,
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
                    $("#quotation").html(result);
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
