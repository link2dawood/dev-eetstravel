@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Add Payment')
@section('content')
<x-ui.page-header
    title="Add Payment"
    description="Record a payment received from the client for this invoice."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Client Invoices', 'href' => route('accounting.index')],
        ['label' => 'Add Payment'],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="javascript:history.back()" variant="ghost" icon="arrow-left">
            {!! trans('main.Back') !!}
        </x-ui.button>
    </x-slot>
</x-ui.page-header>

<form method='post' action="{{ route('inv_payment.store', ['id' => $transactions->id]) }}" enctype="multipart/form-data">
    {{ csrf_field() }}

    @if (count($errors) > 0)
        <div class="mb-4 rounded border border-danger-200 bg-danger-50 px-4 py-3 text-sm text-danger-800">
            <ul class="list-disc pl-5 space-y-1 m-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Payment</h2>
                <p class="mt-0.5 text-sm text-slate-500">How is the client paying you?</p>
            </div>
            <x-ui.button type="button" id="add_feild_button" icon="plus" variant="secondary">
                {!! trans('Add Payment') !!}
            </x-ui.button>
        </div>

        <div class="px-5 py-5">
            <div id="payment-inputs" class="row"></div>
        </div>
    </div>

    <div class="mt-4 flex items-center gap-2">
        <x-ui.button as="a" href="javascript:history.back()" variant="secondary" class="back_btn">
            {!! trans('main.Back') !!}
        </x-ui.button>
        <x-ui.button type="submit" icon="check">{!! trans('main.Save') !!}</x-ui.button>
    </div>
</form>
@endsection

@push('scripts')
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
@endpush
