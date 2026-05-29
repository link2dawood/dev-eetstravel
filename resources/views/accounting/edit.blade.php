@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Edit Client Invoice')

@section('content')
@php
    $inputClass = 'block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600';
    $labelClass = 'block text-sm font-medium text-slate-700 mb-1';
@endphp

<x-ui.page-header
    title="Edit Client Invoice"
    description="Update the client invoice details, extra items and payments."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Client Invoices', 'href' => route('accounting.index')],
        ['label' => 'Edit'],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="javascript:history.back()" variant="ghost" icon="arrow-left">{!! trans('main.Back') !!}</x-ui.button>
        <x-ui.button type="submit" form="invoice-edit-form" icon="check">{!! trans('main.Save') !!}</x-ui.button>
    </x-slot>
</x-ui.page-header>

<form method='post' action="{{ route('accounts.update', ['id' => $transactions->id]) }}" enctype="multipart/form-data" id="invoice-edit-form">
    {{ csrf_field() }}
    <input type='hidden' name='_token' value='{{ Session::token() }}'>
    <input id="invoice_id" value="{{ $transactions->id }}" type="hidden">
    @if (empty($quotation))
        <input id="quotation_id" type="hidden" name="quotation_id" value="">
    @else
        <input id="quotation_id" type="hidden" name="quotation_id" value="{{ $quotation->id }}">
    @endif

    @if (count($errors) > 0)
        <div class="mb-4 rounded border border-danger-200 bg-danger-50 px-4 py-3 text-sm text-danger-800">
            <ul class="list-disc pl-5 space-y-1 m-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Invoice Detail --}}
    <div class="rounded border border-slate-200 bg-white mb-6">
        <div class="border-b border-slate-200 px-5 py-4 flex items-center gap-2">
            <x-ui.icon name="file-text" size="sm" class="text-slate-400" />
            <h2 class="text-base font-semibold text-slate-900">Invoice Detail</h2>
        </div>
        <div class="px-5 py-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-x-6 gap-y-4">
                <div>
                    <label for="currency" class="{{ $labelClass }}">Currency</label>
                    <select name="currency" id="currency" class="{{ $inputClass }}" required>
                        @php $currencies = ["Euro", "Dollar", "Swiss Franks"]; @endphp
                        @foreach ($currencies as $currency)
                            <option value="{{ $currency }}" {{ $currency == $transactions->currency ? 'selected' : '' }}>{{ $currency }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="office_id" class="{{ $labelClass }}">{{ trans('Office') }}</label>
                    <select name="office_id" id="office_id" class="{{ $inputClass }}">
                        @foreach ($offices as $office)
                            <option value="{{ $office->id }}">{{ $office->office_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="tour_id" class="{{ $labelClass }}">{{ trans('main.Tour') }}</label>
                    <select name="tour_id" id="tour_id" class="{{ $inputClass }}">
                        @foreach ($tours as $tour)
                            <option value="{{ $tour->id }}" {{ $transactions->tour_id === $tour->id ? 'selected' : '' }}>{{ $tour->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="client_id" class="{{ $labelClass }}">{{ trans('Client') }}</label>
                    <select name="client_id" id="client_id" class="{{ $inputClass }}">
                        @foreach ($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Hidden service fields (populated via JS) --}}
            <div class="mt-4" id="services" style="display:none">
                <label for="service" class="{{ $labelClass }}">{{ trans('Service') }}</label>
                <select id="service" name="service" class="{{ $inputClass }}"></select>
            </div>
            <div class="mt-4" id="service_div"></div>
        </div>
    </div>

    {{-- Extra Items --}}
    <div class="rounded border border-slate-200 bg-white mb-6">
        <div class="border-b border-slate-200 px-5 py-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Extra Items</h2>
                <p class="mt-0.5 text-sm text-slate-500">Extra items in this invoice.</p>
            </div>
            <x-ui.button type="button" id="add_contact" icon="plus" variant="secondary">{!! trans('Extra Items') !!}</x-ui.button>
        </div>
        <div class="px-5 py-5">
            <div id="items-contacts" class="row"></div>
        </div>
    </div>

    {{-- Payment --}}
    <div class="rounded border border-slate-200 bg-white mb-6">
        <div class="border-b border-slate-200 px-5 py-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Payment</h2>
                <p class="mt-0.5 text-sm text-slate-500">How is the client paying you?</p>
            </div>
            <x-ui.button type="button" id="add_feild_button" icon="plus" variant="secondary">{!! trans('Add Payment') !!}</x-ui.button>
        </div>
        <div class="px-5 py-5">
            <div id="payment-inputs" class="row"></div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="flex items-center justify-end gap-2 border-t border-slate-200 pt-4">
        <x-ui.button as="a" href="javascript:history.back()" variant="secondary" icon="arrow-left">{!! trans('main.Back') !!}</x-ui.button>
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
