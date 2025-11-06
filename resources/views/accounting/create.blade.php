{{-- 
    Accounting Create Page - Tabler Design
    Create new client invoices with modern Tabler styling
--}}
@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Create Invoice')

@section('content')
<div class="container-xl">
    {{-- Page Header --}}
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('/home') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('accounting.index') }}">Invoices</a></li>
                            <li class="breadcrumb-item active">Create Invoice</li>
                        </ol>
                    </nav>
                </div>
                <h2 class="page-title">
                    <i class="ti ti-file-invoice me-2"></i>Create Client Invoice
                </h2>
            </div>
            {{-- Page Actions --}}
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="javascript:history.back()" class="btn btn-ghost-secondary">
                        <i class="ti ti-arrow-left me-1"></i>{!! trans('main.Back') !!}
                    </a>
                    <button type="submit" form="invoice-form" class="btn btn-success">
                        <i class="ti ti-device-floppy me-1"></i>{!! trans('main.Save') !!}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ url('accounting') }}" enctype="multipart/form-data" id="invoice-form">
        @csrf
        <input type="hidden" name="_token" value="{{ Session::token() }}">
        
        @if (empty($quotation))
            <input id="quotation_id" type="hidden" name="quotation_id" value="">
        @else
            <input id="quotation_id" type="hidden" name="quotation_id" value="{{ $quotation->id }}">
        @endif

        @if (count($errors) > 0)
            <div class="alert alert-danger alert-dismissible" role="alert">
                <div class="d-flex">
                    <div><i class="ti ti-alert-circle me-2"></i></div>
                    <div>
                        <h4 class="alert-title">Validation Errors</h4>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Invoice Detail Section --}}
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-file-text me-2"></i>Invoice Detail
                </h3>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    {{-- Currency --}}
                    <div class="col-md-3">
                        <label class="form-label required">Currency</label>
                        <select name="currency" id="currency" class="form-select" required>
                            <option value="" disabled selected>Choose currency...</option>
                            <option value="EUR">Euro (EUR)</option>
                            <option value="USD">Dollar (USD)</option>
                            <option value="CHF">Swiss Franks (CHF)</option>
                        </select>
                    </div>

                    {{-- Office --}}
                    <div class="col-md-3">
                        <label class="form-label required">Office</label>
                        <select name="office_id" id="office_id" class="form-select" required>
                            <option value="" disabled selected>Choose office...</option>
                            @foreach ($offices as $office)
                                <option value="{{ $office->id }}">{{ $office->office_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Tour --}}
                    <div class="col-md-3">
                        <label class="form-label required">{{ trans('main.Tour') }}</label>
                        <select name="tour_id" id="tour_id" class="form-select" required>
                            <option value="" disabled selected>Choose tour...</option>
                            @foreach ($tours as $tour)
                                <option value="{{ $tour->id }}">{{ $tour->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Client --}}
                    <div class="col-md-3">
                        <label class="form-label required">Client</label>
                        <select name="client_id" id="client_id" class="form-select" required>
                            <option value="" disabled selected>Choose client...</option>
                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Extra Cost --}}
                    <div class="col-md-6">
                        <label class="form-label">Extra Cost</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="ti ti-currency-dollar"></i>
                            </span>
                            <input type="number" name="extra_cost" class="form-control" placeholder="0.00" step="0.01">
                        </div>
                    </div>

                    {{-- Note --}}
                    <div class="col-md-6">
                        <label class="form-label">Note</label>
                        <textarea id="note" name="note" class="form-control" rows="3" placeholder="Add any additional notes..."></textarea>
                    </div>

                    {{-- Hidden Service Fields --}}
                    <div class="col-md-12" id="services" style="display:none">
                        <label class="form-label">Service</label>
                        <select id="service" name="service" class="form-select">
                            <option value="" disabled selected>Choose service...</option>
                        </select>
                    </div>
                    <div class="col-md-12" id="service_div"></div>
                </div>
            </div>
        </div>
        {{-- Extra Items Section --}}
        <div class="card mb-3">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="card-title">
                            <i class="ti ti-list me-2"></i>Extra Items
                        </h3>
                        <p class="text-muted mb-0">Add extra items to this invoice</p>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-success" id="add_contact" type="button">
                            <i class="ti ti-plus me-1"></i>Add Item
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div id="items-contacts" class="row g-3">
                    <div class="item-contact col-12">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <label class="form-label">Item Name</label>
                                <input id="item_name" name="items[1][item_name]" type="text" class="form-control" placeholder="Enter item name" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Quantity</label>
                                <input id="item_desc" name="items[1][quantity]" type="number" class="form-control" onchange="calculateItemTotal(this)" placeholder="0" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Price (excl. VAT)</label>
                                <input id="amount" name="items[1][amount]" type="number" class="form-control" onchange="calculateItemTotal(this)" placeholder="0.00" step="0.01" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">VAT Rate</label>
                                <select name="items[{{$count}}][vat]" id="vat" class="form-select" onchange="calculateItemTotal(this)" required>
                                    <option value="" disabled selected>Choose VAT...</option>
                                    @foreach($taxes as $tax)
                                        <option value="{{$tax->value/100}}">{{$tax->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Total Amount</label>
                                <input id="total_amount" name="items[1][total_amount]" type="number" class="form-control item_total" placeholder="0.00" readonly>
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="button" class="btn btn-icon btn-ghost-danger" id="delete_contact_item" title="Delete item">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Payment Section --}}
        <div class="card mb-3">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="card-title">
                            <i class="ti ti-credit-card me-2"></i>Payment
                        </h3>
                        <p class="text-muted mb-0">How is the client paying you?</p>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-success" id="add_feild_button" type="button">
                            <i class="ti ti-plus me-1"></i>Add Payment
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div id="payment-inputs" class="row g-3">
                    {{-- Payments will be dynamically added here --}}
                </div>
            </div>
        </div>

        {{-- Form Footer Actions --}}
        <div class="d-flex justify-content-end gap-2 mt-3 mb-3">
            <a href="javascript:history.back()" class="btn btn-ghost-secondary">
                <i class="ti ti-arrow-left me-1"></i>{!! trans('main.Back') !!}
            </a>
            <button class="btn btn-success" type="submit">
                <i class="ti ti-device-floppy me-1"></i>{!! trans('main.Save') !!}
            </button>
        </div>
    </form>
</div>

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
            const itemContainer = iteminput.closest('.row');
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
