@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Edit Invoice')

@section('content')
@php
    $inputClass = 'block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600';
    $labelClass = 'block text-sm font-medium text-slate-700 mb-1';
@endphp

<x-ui.page-header
    title="Edit Supplier Invoice"
    description="Update the invoice details and supplier payments."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Supplier Invoices', 'href' => route('invoices.index')],
        ['label' => 'Edit'],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="javascript:history.back()" variant="ghost" icon="arrow-left">
            {!! trans('main.Back') !!}
        </x-ui.button>
    </x-slot>
</x-ui.page-header>

<form method='POST' action='{{ route('invoice.update', ['id' => $invoices->id]) }}' enctype="multipart/form-data">
    {{ csrf_field() }}
    <input type='hidden' name='_token' value='{{ Session::token() }}'>
    <input id="invoice_id" value="{{ $invoices->invoices->id }}" type="hidden">

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
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                <div>
                    <label for="office_id" class="{{ $labelClass }}">{{ trans('Office') }}</label>
                    <select name="office_id" id="office_id" class="{{ $inputClass }}" required>
                        @foreach ($offices as $office)
                            <option value="{{ $office->id }}" {{ $invoices->invoices->office_id === $office->id ? 'selected' : '' }}>{{ $office->office_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="invoice_no" class="{{ $labelClass }}">{!! trans('Invoice No') !!} <span class="text-danger-600">*</span></label>
                    <input class="{{ $inputClass }}" required name="invoice_no" id="invoice_no" type="text" value="{{ $invoices->invoices->invoice_no }}">
                </div>

                <div>
                    <label for="tour_id" class="{{ $labelClass }}">{{ trans('main.Tour') }}</label>
                    <select name="tours" id="tour_id" class="{{ $inputClass }}">
                        @foreach ($tours as $tour)
                            <option value="{{ $tour->id }}" {{ $invoices->tours->name === $tour->name ? 'selected' : '' }}>{{ $tour->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="total_amount" class="{{ $labelClass }}">{!! trans('Total Amount') !!} <span class="text-danger-600">*</span></label>
                    <input class="{{ $inputClass }}" required name="total_amount" id="total_amount" type="text" value="{{ $invoices->invoices->total_amount }}">
                </div>
            </div>

            {{-- Services (dynamically populated via AJAX) --}}
            <div id="services" class="mt-4"></div>
            <div id="service_div" class="mt-4"></div>
        </div>
    </div>

    {{-- Payment --}}
    <div class="rounded border border-slate-200 bg-white mb-6">
        <div class="border-b border-slate-200 px-5 py-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Payment</h2>
                <p class="mt-0.5 text-sm text-slate-500">Payments that you paid to the supplier.</p>
            </div>
            <x-ui.button type="button" id="add_feild_button" icon="plus" variant="secondary">
                {!! trans('Add Payment') !!}
            </x-ui.button>
        </div>
        <div class="px-5 py-5">
            <div id="payment-inputs" class="row"></div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="flex items-center justify-end gap-2 border-t border-slate-200 pt-4">
        <x-ui.button as="a" href="javascript:history.back()" variant="secondary" icon="arrow-left" class="back_btn">
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

        APP_URL = '{{ url('/supplierdropdown') }}';
		function package_dropdown_ajax(selectedValue){
				$.ajax({
                            type: "GET",
                            url: APP_URL + '/' + selectedValue,
                            success: function(result) {

                                if (result[0] === "") {
                                    $("#service_div").show();
                                    $("#services").hide();
                                    $("#service_div").html(
                                        `<h3> Please Add Service in the tour </h3>`);
                                } else {
                                    $("#service_div").hide();
                                    $("#services").show();
                                    $("#services").html(result);
                                }
                            },
                            error: function(result) {
                                console.log(result);
                            }
                        });
			}
		var selectedValue = $(tour_id).val();
		package_dropdown_ajax(selectedValue);

        $(document).ready(function() {

            $("#tour_id").change(function() {
               			var selectedValue = $(this).val();
                		package_dropdown_ajax(selectedValue);

                    });


            function removeDropdown() {
                // Remove the dropdown element if no selection is made
                $('#services').next('select').remove();
                //  $("#service_div").hide();
            }
        });

        var array1 = [1, 2, 3, 4, 5];
        var array2 = [5, 7, 8, 9, 10];

        var allValuesNotInArray2 = true;

        array1.forEach(function(value) {
            if (array2.includes(value)) {
                allValuesNotInArray2 = false;
                return;
            }
        });

        if (allValuesNotInArray2) {
            console.log("All values of array1 are not in array2");
        } else {
            console.log("At least one value of array1 is present in array2");
        }
    </script>
    <script>
      let contactItemCount = 0;
      invoice_payments();
		function invoice_payments() {
			let invoice_id = $("#invoice_id").val();
            $.ajax({
                url: '/api/getInvoicePayments/2',
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
      function payment_view_ajax(){
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
      $('#add_feild_button').on('click', function() {
        payment_view_ajax();
        });
        $(document).on('click', '#delete_contact_item', function() {
            $(this).closest('.item-contact').remove();
        });
    </script>
@endpush
