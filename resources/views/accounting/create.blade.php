@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Create Invoice')

@section('content')
@php
    $inputClass = 'block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600';
    $labelClass = 'block text-sm font-medium text-slate-700 mb-1';
@endphp

<x-ui.page-header
    title="Create Client Invoice"
    description="Create a new client invoice with extra items and payments."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Client Invoices', 'href' => route('accounting.index')],
        ['label' => 'Create Invoice'],
    ]"
>
    <x-slot name="actions">
        <x-ui.button as="a" href="javascript:history.back()" variant="ghost" icon="arrow-left">{!! trans('main.Back') !!}</x-ui.button>
        <x-ui.button type="submit" form="invoice-form" icon="check">{!! trans('main.Save') !!}</x-ui.button>
    </x-slot>
</x-ui.page-header>

<form method="POST" action="{{ url('accounting') }}" enctype="multipart/form-data" id="invoice-form">
    @csrf
    <input type="hidden" name="_token" value="{{ Session::token() }}">

    @if (empty($quotation))
        <input id="quotation_id" type="hidden" name="quotation_id" value="">
    @else
        <input id="quotation_id" type="hidden" name="quotation_id" value="{{ $quotation->id }}">
    @endif

    @if (count($errors) > 0)
        <div class="mb-4 rounded border border-danger-200 bg-danger-50 px-4 py-3 text-sm text-danger-800">
            <h4 class="font-semibold mb-1">Validation Errors</h4>
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
                    <label for="currency" class="{{ $labelClass }}">Currency <span class="text-danger-600">*</span></label>
                    <select name="currency" id="currency" class="{{ $inputClass }}" required>
                        <option value="" disabled selected>Choose currency...</option>
                        <option value="EUR">Euro (EUR)</option>
                        <option value="USD">Dollar (USD)</option>
                        <option value="CHF">Swiss Franks (CHF)</option>
                    </select>
                </div>
                <div>
                    <label for="office_id" class="{{ $labelClass }}">Office <span class="text-danger-600">*</span></label>
                    <select name="office_id" id="office_id" class="{{ $inputClass }}" required>
                        <option value="" disabled selected>Choose office...</option>
                        @foreach ($offices as $office)
                            <option value="{{ $office->id }}">{{ $office->office_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="tour_id" class="{{ $labelClass }}">{{ trans('main.Tour') }} <span class="text-danger-600">*</span></label>
                    <select name="tour_id" id="tour_id" class="{{ $inputClass }}" required>
                        <option value="" disabled selected>Choose tour...</option>
                        @foreach ($tours as $tour)
                            <option value="{{ $tour->id }}">{{ $tour->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="client_id" class="{{ $labelClass }}">Client <span class="text-danger-600">*</span></label>
                    <select name="client_id" id="client_id" class="{{ $inputClass }}" required>
                        <option value="" disabled selected>Choose client...</option>
                        @foreach ($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 mt-4">
                <div>
                    <label for="extra_cost" class="{{ $labelClass }}">Extra Cost</label>
                    <input type="number" name="extra_cost" id="extra_cost" class="{{ $inputClass }}" placeholder="0.00" step="0.01">
                </div>
                <div>
                    <label for="note" class="{{ $labelClass }}">Note</label>
                    <textarea id="note" name="note" rows="3"
                              class="block w-full rounded border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600"
                              placeholder="Add any additional notes..."></textarea>
                </div>
            </div>

            {{-- Hidden service fields (populated via JS) --}}
            <div class="mt-4" id="services" style="display:none">
                <label for="service" class="{{ $labelClass }}">Service</label>
                <select id="service" name="service" class="{{ $inputClass }}">
                    <option value="" disabled selected>Choose service...</option>
                </select>
            </div>
            <div class="mt-4" id="service_div"></div>
        </div>
    </div>

    {{-- Extra Items --}}
    <div class="rounded border border-slate-200 bg-white mb-6">
        <div class="border-b border-slate-200 px-5 py-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Extra Items</h2>
                <p class="mt-0.5 text-sm text-slate-500">Add extra items to this invoice.</p>
            </div>
            <x-ui.button type="button" id="add_contact" icon="plus" variant="secondary">Add Item</x-ui.button>
        </div>
        <div class="px-5 py-5">
            <div id="items-contacts" class="space-y-3">
                <div class="item-contact rounded border border-slate-200 bg-slate-50 p-3">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3">
                        <div class="lg:col-span-3">
                            <label class="{{ $labelClass }}">Item Name</label>
                            <input id="item_name" name="items[1][item_name]" type="text" class="{{ $inputClass }}" placeholder="Enter item name" required>
                        </div>
                        <div class="lg:col-span-2">
                            <label class="{{ $labelClass }}">Quantity</label>
                            <input id="item_desc" name="items[1][quantity]" type="number" class="{{ $inputClass }}" onchange="calculateItemTotal(this)" placeholder="0" required>
                        </div>
                        <div class="lg:col-span-2">
                            <label class="{{ $labelClass }}">Price (excl. VAT)</label>
                            <input id="amount" name="items[1][amount]" type="number" class="{{ $inputClass }}" onchange="calculateItemTotal(this)" placeholder="0.00" step="0.01" required>
                        </div>
                        <div class="lg:col-span-2">
                            <label class="{{ $labelClass }}">VAT Rate</label>
                            <select name="items[{{$count}}][vat]" id="vat" class="{{ $inputClass }}" onchange="calculateItemTotal(this)" required>
                                <option value="" disabled selected>Choose VAT...</option>
                                @foreach($taxes as $tax)
                                    <option value="{{$tax->value/100}}">{{$tax->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="lg:col-span-2">
                            <label class="{{ $labelClass }}">Total Amount</label>
                            <input id="total_amount" name="items[1][total_amount]" type="number" class="{{ $inputClass }} item_total" placeholder="0.00" readonly>
                        </div>
                        <div class="lg:col-span-1 flex items-end">
                            <button type="button" class="inline-flex h-9 w-9 items-center justify-center rounded text-slate-400 hover:bg-danger-50 hover:text-danger-700" id="delete_contact_item" title="Delete item">
                                <x-ui.icon name="trash-2" size="sm" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Payment --}}
    <div class="rounded border border-slate-200 bg-white mb-6">
        <div class="border-b border-slate-200 px-5 py-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Payment</h2>
                <p class="mt-0.5 text-sm text-slate-500">How is the client paying you?</p>
            </div>
            <x-ui.button type="button" id="add_feild_button" icon="plus" variant="secondary">Add Payment</x-ui.button>
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

@section('post_scripts')
<script type="text/javascript" src="{{ asset('js/rooms.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/hide_elements.js') }}"></script>

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        let contactItemCount = 1;

        // File upload preview
        function readURL(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#pic').attr('src', e.target.result);
                    $('#file-caption-name').html(input.files[0].name);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#imgInp").on('change', function() {
            readURL(this);
        });

        // Add extra invoice item
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
                    $(this).val("{{ csrf_token() }}");
                });
            });
        }

        // Add payment field
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
                    $(this).val("{{ csrf_token() }}");
                });
            });
        }

        // Add item button
        $('#add_contact').on('click', function() {
            item_invoice_ajax();
        });

        // Add payment button
        $('#add_feild_button').on('click', function() {
            payment_view_ajax();
        });

        // Delete item button
        $(document).on('click', '#delete_contact_item', function() {
            $(this).closest('.item-contact').remove();
        });

        // Tour change handler - load quotation
        $("#tour_id").on('change', function() {
            const tour_id = $(this).val();
            $.ajax({
                type: "GET",
                url: `api/getTourquotation/${tour_id}`,
                success: function(result) {
                    $("#quotation_id").val(result);
                    console.log('Quotation loaded:', result);
                },
                error: function(result) {
                    console.error('Error loading quotation:', result);
                }
            });
        });

        // Calculate item total with VAT
        window.calculateItemTotal = function(iteminput) {
            const itemContainer = iteminput.closest('.item-contact') || iteminput.closest('.row');
            const quantity = parseFloat(itemContainer.querySelector("#item_desc")?.value) || 0;
            const price = parseFloat(itemContainer.querySelector("#amount")?.value) || 0;
            const vat = parseFloat(itemContainer.querySelector("#vat")?.value) || 0;

            const total_price = quantity * price;
            const total_tax = total_price * vat;
            const itemTotal = total_price + total_tax;

            // Update the item total displayed for this item
            const totalElement = itemContainer.querySelector("#total_amount");
            if (totalElement) {
                totalElement.value = itemTotal.toFixed(2);
            }

            // Recalculate the overall total
            calculateOverallTotal();
        }

        // Calculate overall total
        function calculateOverallTotal() {
            const itemTotals = document.querySelectorAll(".item_total");
            let overallTotal = 0;

            itemTotals.forEach(itemTotal => {
                const value = parseFloat(itemTotal.value) || 0;
                overallTotal += value;
            });

            console.log('Overall total:', overallTotal.toFixed(2));
        }
    });
</script>
@endsection
