@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Create Invoice')

@section('post_styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Align Select2's multi-select widget with the Tailwind input theme. */
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #cbd5e1; border-radius: 0.375rem; min-height: 2.25rem;
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #0d9488; box-shadow: 0 0 0 2px rgba(13,148,136,0.25);
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #f0fdfa; border-color: #99f6e4; color: #115e59;
    }
</style>
@endsection

@section('content')
@php
    $inputClass = 'block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600';
    $labelClass = 'block text-sm font-medium text-slate-700 mb-1';
@endphp

<x-ui.page-header
    title="Create Supplier Invoice"
    description="Enter the invoice details and record any payments to the supplier."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Supplier Invoices', 'href' => route('invoices.index')],
        ['label' => 'Create'],
    ]"
/>

<form id="myForm" method="POST" action="{{ url('invoices') }}" enctype="multipart/form-data">
    @csrf

    @if (count($errors) > 0)
        <div class="mb-4 rounded border border-danger-200 bg-danger-50 px-4 py-3 text-sm text-danger-800">
            <ul class="list-disc pl-5 space-y-1 m-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Invoice Details --}}
    <div class="rounded border border-slate-200 bg-white mb-6">
        <div class="border-b border-slate-200 px-5 py-4 flex items-center gap-2">
            <x-ui.icon name="file-text" size="sm" class="text-slate-400" />
            <div>
                <h2 class="text-base font-semibold text-slate-900">Invoice Details</h2>
                <p class="text-sm text-slate-500">Enter the basic invoice information.</p>
            </div>
        </div>
        <div class="px-5 py-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                <div>
                    <label for="invoice_no" class="{{ $labelClass }}">Invoice Number <span class="text-danger-600">*</span></label>
                    <input type="text" name="invoice_no" id="invoice_no" class="{{ $inputClass }}" placeholder="INV-2024-001" required>
                </div>

                <div>
                    <label for="office_id" class="{{ $labelClass }}">
                        Office @if($offices->isNotEmpty())<span class="text-danger-600">*</span>@endif
                    </label>
                    @if($offices->isEmpty())
                        <select name="office_id" id="office_id" class="{{ $inputClass }}" disabled>
                            <option value="">No offices available</option>
                        </select>
                    @else
                        <select name="office_id" id="office_id" class="{{ $inputClass }}" required>
                            <option value="">Select Office</option>
                            @foreach ($offices as $office)
                                <option value="{{ $office->id }}">{{ $office->office_name }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>

                <div>
                    <label for="total_amount" class="{{ $labelClass }}">Total Amount <span class="text-danger-600">*</span></label>
                    <input type="number" step="0.01" name="total_amount" id="total_amount" class="{{ $inputClass }}" placeholder="0.00" required>
                </div>

                <div>
                    <label for="extra_amount" class="{{ $labelClass }}">Extra Cost</label>
                    <input type="number" step="0.01" name="extra_amount" id="extra_amount" class="{{ $inputClass }}" placeholder="0.00">
                </div>
            </div>

            <div class="mt-4">
                <label for="tour_id" class="{{ $labelClass }}">Tours <span class="text-danger-600">*</span></label>
                <select name="tour_id[]" id="tour_id" class="form-control select22" multiple="multiple" required style="width:100%;">
                    @foreach ($tours as $tour)
                        <option value="{{ $tour->id }}">{{ $tour->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Services (dynamically populated via AJAX) --}}
            <div id="services" class="mt-4" style="display:none"></div>
            <div id="service_div" class="mt-4"></div>

            <div class="mt-4">
                <label for="note" class="{{ $labelClass }}">Notes</label>
                <textarea name="note" id="note" rows="4"
                          class="block w-full rounded border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600"
                          placeholder="Add any additional notes here..."></textarea>
            </div>

            <div class="mt-4">
                <label class="{{ $labelClass }}">Attachments</label>
                <div class="rounded border border-dashed border-slate-300 bg-slate-50 p-5">
                    @component('component.file_upload_field', ['enableAjaxUploads' => false])
                    @endcomponent
                </div>
            </div>
        </div>
    </div>

    {{-- Payment Information --}}
    <div class="rounded border border-slate-200 bg-white mb-6">
        <div class="border-b border-slate-200 px-5 py-4 flex items-center gap-2">
            <x-ui.icon name="credit-card" size="sm" class="text-slate-400" />
            <div>
                <h2 class="text-base font-semibold text-slate-900">Payment Information</h2>
                <p class="text-sm text-slate-500">Add payments made to the supplier.</p>
            </div>
        </div>
        <div class="px-5 py-5">
            <div class="mb-4 flex items-start gap-2 rounded border border-primary-200 bg-primary-50 px-4 py-3 text-sm text-primary-800">
                <x-ui.icon name="info" size="sm" class="mt-0.5 shrink-0 text-primary-600" />
                <p class="m-0">Record all payments that you have made to the supplier. Click the button below to add payment entries.</p>
            </div>

            <div class="mb-4">
                <x-ui.button type="button" id="add_feild_button" icon="plus" variant="secondary">
                    Add Payment Entry
                </x-ui.button>
            </div>

            <div id="payment-inputs"></div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="flex items-center justify-end gap-2 border-t border-slate-200 pt-4">
        <x-ui.button as="a" href="javascript:history.back()" variant="secondary" icon="arrow-left">Cancel</x-ui.button>
        <x-ui.button type="submit" id="submitBtn" icon="check">Save Invoice</x-ui.button>
    </div>
</form>
@endsection

@section('post_scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script type="text/javascript" src="{{ asset('js/rooms.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/hide_elements.js') }}"></script>

<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select22').select2({
        placeholder: "Select tours",
        allowClear: true,
        width: '100%'
    });

    // File input handling
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

    // Form submission loading state
    $('#myForm').on('submit', function() {
        $('#submitBtn').prop('disabled', true);
    });

    // Tour selection handler
    const APP_URL = '{{ url('/supplierdropdown') }}';
    let previousSelection = [];
    let selected = [];
    let appendedInputs = [];

    $("#tour_id").change(function() {
        const selectedValues = $(this).val() || [];
        selected = [];

        if (selectedValues.length === 0) {
            $("#services").hide();
        } else {
            if (selectedValues.length > previousSelection.length) {
                const newSelection = selectedValues.filter(value => !previousSelection.includes(value));
                selected = selected.concat(newSelection);
            } else if (selectedValues.length < previousSelection.length) {
                const deselected = previousSelection.filter(value => !selectedValues.includes(value));
                selected = selected.filter(value => !deselected.includes(value));
            } else {
                const newSelection = selectedValues.filter(value => !previousSelection.includes(value));
                selected = selected.concat(newSelection);
            }
        }

        console.log("Selected:", selected);

        if (selected.length > 0) {
            const lastSelectedValues = selected.slice(-1);

            $.each(lastSelectedValues, function(index, selectedValue) {
                $.ajax({
                    type: "GET",
                    url: APP_URL + '/' + selectedValue,
                    data: { multiple: 1 },
                    success: function(result) {
                        if (result[0] === "") {
                            $("#service_div").show();
                            $("#services").hide();
                            $("#service_div").html('<div class="rounded border border-warning-200 bg-warning-50 px-4 py-3 text-sm text-warning-700">Please add services to the tour first</div>');
                        } else {
                            $("#service_div").hide();
                            $("#services").show();
                            $("#services").append(result);
                        }
                    },
                    error: function(result) {
                        console.error(result);
                    }
                });
            });
        } else {
            const deselected = previousSelection.filter(value => !selectedValues.includes(value));
            $(`#service${deselected}`).remove();
            $(`#lable-service${deselected}`).remove();

            $.each(appendedInputs, function(index, input) {
                input.remove();
            });
            appendedInputs = [];
        }

        previousSelection = selectedValues;
    });

    // Payment fields management
    let contactItemCount = 0;

    function payment_view_ajax() {
        $.ajax({
            url: '/api/getPaymentView',
            method: 'GET',
            data: { itemCount: contactItemCount + 1 }
        }).done((res) => {
            contactItemCount++;
            $('#payment-inputs').append(res);
            $('input[name="_token"]').each(function() {
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
});
</script>
@endsection
