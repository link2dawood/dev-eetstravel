@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Create Invoice')

@section('post_styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Modern Card Styling */
    .invoice-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        margin-bottom: 24px;
        transition: all 0.3s ease;
    }

    .invoice-card:hover {
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
    }

    .card-header-custom {
        padding: 24px;
        border-bottom: 2px solid #f0f0f0;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px 12px 0 0;
    }

    .card-header-custom h2 {
        color: #fff;
        font-size: 24px;
        font-weight: 600;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .card-header-custom p {
        color: rgba(255, 255, 255, 0.9);
        margin: 8px 0 0 0;
        font-size: 14px;
    }

    .card-body-custom {
        padding: 32px;
    }

    /* Form Group Improvements */
    .form-group {
        margin-bottom: 24px;
    }

    .form-group label {
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 8px;
        display: block;
        font-size: 14px;
    }

    .form-group label .required {
        color: #e53e3e;
        margin-left: 4px;
    }

    .form-control {
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px 16px;
        font-size: 14px;
        transition: all 0.3s ease;
        background-color: #fff;
    }

    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        outline: none;
    }

    textarea.form-control {
        min-height: 100px;
        resize: vertical;
    }

    /* Select2 Customization */
    .select2-container--default .select2-selection--multiple {
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        padding: 6px;
        min-height: 48px;
    }

    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    /* Button Styling */
    .btn-actions {
        display: flex;
        gap: 12px;
        padding: 24px 0;
        justify-content: flex-end;
        border-top: 2px solid #f0f0f0;
        margin-top: 32px;
    }

    .btn-custom {
        padding: 12px 28px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-primary-custom {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
    }

    .btn-primary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .btn-secondary-custom {
        background: #e2e8f0;
        color: #4a5568;
    }

    .btn-secondary-custom:hover {
        background: #cbd5e0;
    }

    .btn-success-custom {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        color: #fff;
    }

    .btn-success-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(72, 187, 120, 0.4);
    }

    .btn-add-payment {
        background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
        color: #fff;
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-add-payment:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(66, 153, 225, 0.4);
    }

    /* Alert Styling */
    .alert-danger-custom {
        background: #fff5f5;
        border-left: 4px solid #e53e3e;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 24px;
    }

    .alert-danger-custom ul {
        margin: 0;
        padding-left: 20px;
        color: #c53030;
    }

    /* File Upload Area */
    .file-upload-wrapper {
        border: 2px dashed #cbd5e0;
        border-radius: 8px;
        padding: 24px;
        text-align: center;
        transition: all 0.3s ease;
        background: #f7fafc;
    }

    .file-upload-wrapper:hover {
        border-color: #667eea;
        background: #edf2f7;
    }

    /* Payment Section */
    .payment-section {
        margin-top: 16px;
    }

    .item-contact {
        background: #f7fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 16px;
        position: relative;
    }

    .item-contact .btn-remove {
        position: absolute;
        top: 12px;
        right: 12px;
        background: #feb2b2;
        color: #742a2a;
        border: none;
        border-radius: 6px;
        padding: 6px 12px;
        font-size: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .item-contact .btn-remove:hover {
        background: #fc8181;
    }

    /* Grid Layout */
    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 24px;
    }

    /* Section Headers */
    .section-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 24px;
        padding-bottom: 12px;
        border-bottom: 2px solid #e2e8f0;
    }

    .section-header i {
        font-size: 24px;
        color: #667eea;
    }

    .section-header h3 {
        font-size: 20px;
        font-weight: 600;
        color: #2d3748;
        margin: 0;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .card-body-custom {
            padding: 20px;
        }

        .form-grid {
            grid-template-columns: 1fr;
        }

        .btn-actions {
            flex-direction: column;
        }

        .btn-custom {
            width: 100%;
            justify-content: center;
        }
    }

    /* Loading State */
    .btn-custom.loading {
        position: relative;
        color: transparent;
    }

    .btn-custom.loading::after {
        content: '';
        position: absolute;
        width: 16px;
        height: 16px;
        top: 50%;
        left: 50%;
        margin-left: -8px;
        margin-top: -8px;
        border: 2px solid #fff;
        border-radius: 50%;
        border-top-color: transparent;
        animation: spinner 0.6s linear infinite;
    }

    @keyframes spinner {
        to {
            transform: rotate(360deg);
        }
    }

    /* Info Box */
    .info-box {
        background: #ebf8ff;
        border-left: 4px solid #4299e1;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 24px;
        display: flex;
        align-items: start;
        gap: 12px;
    }

    .info-box i {
        color: #4299e1;
        font-size: 20px;
        margin-top: 2px;
    }

    .info-box p {
        margin: 0;
        color: #2c5282;
        font-size: 14px;
    }
</style>
@endsection

@section('content')
<div class="container-xl">
    {{-- Page Header --}}
    <div class="page-header d-print-none mb-4">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('/home') }}"><i class="ti ti-home"></i> Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('tour.index') }}">Invoices</a></li>
                            <li class="breadcrumb-item active">Create</li>
                        </ol>
                    </nav>
                </div>
                <h2 class="page-title">
                    <i class="ti ti-file-invoice me-2"></i> Create Supplier Invoice
                </h2>
            </div>
        </div>
    </div>

    <form id="myForm" method="POST" action="{{ url('invoices') }}" enctype="multipart/form-data">
        @csrf

        {{-- Error Messages --}}
        @if (count($errors) > 0)
            <div class="alert-danger-custom">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Invoice Details Card --}}
        <div class="invoice-card">
            <div class="card-header-custom">
                <h2>
                    <i class="ti ti-file-text"></i>
                    Invoice Details
                </h2>
                <p>Enter the basic invoice information</p>
            </div>
            <div class="card-body-custom">
                <div class="form-grid">
                    {{-- Invoice Number --}}
                    <div class="form-group">
                        <label for="invoice_no">
                            Invoice Number
                            <span class="required">*</span>
                        </label>
                        <input type="text" name="invoice_no" id="invoice_no" class="form-control" placeholder="INV-2024-001" required>
                    </div>

                    {{-- Office --}}
                    <div class="form-group">
                        <label for="office_id">
                            Office
                            @if($offices->isNotEmpty())<span class="required">*</span>@endif
                        </label>
                        @if($offices->isEmpty())
                            {{-- If there are no offices available, show a disabled select with a helpful message so the browser won't show the default "Please select an item in the list." tooltip. --}}
                            <select name="office_id" id="office_id" class="form-control" disabled>
                                <option value="">No offices available</option>
                            </select>
                        @else
                            <select name="office_id" id="office_id" class="form-control" required>
                                <option value="">Select Office</option>
                                @foreach ($offices as $office)
                                    <option value="{{ $office->id }}">{{ $office->office_name }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>

                    {{-- Total Amount --}}
                    <div class="form-group">
                        <label for="total_amount">
                            Total Amount
                            <span class="required">*</span>
                        </label>
                        <input type="number" step="0.01" name="total_amount" id="total_amount" class="form-control" placeholder="0.00" required>
                    </div>

                    {{-- Extra Amount --}}
                    <div class="form-group">
                        <label for="extra_amount">Extra Cost</label>
                        <input type="number" step="0.01" name="extra_amount" id="extra_amount" class="form-control" placeholder="0.00">
                    </div>
                </div>

                {{-- Tour Selection --}}
                <div class="form-group">
                    <label for="tour_id">
                        Tours
                        <span class="required">*</span>
                    </label>
                    <select name="tour_id[]" id="tour_id" class="form-control select22" multiple="multiple" required>
                        @foreach ($tours as $tour)
                            <option value="{{ $tour->id }}">{{ $tour->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Services (Dynamic) --}}
                <div id="services" style="display:none"></div>
                <div id="service_div"></div>

                {{-- Note --}}
                <div class="form-group">
                    <label for="note">Notes</label>
                    <textarea name="note" id="note" class="form-control" placeholder="Add any additional notes here..."></textarea>
                </div>

                {{-- File Upload --}}
                <div class="form-group">
                    <label>Attachments</label>
                    <div class="file-upload-wrapper">
                        @component('component.file_upload_field', ['enableAjaxUploads' => false])
                        @endcomponent
                    </div>
                </div>
            </div>
        </div>

        {{-- Payment Section Card --}}
        <div class="invoice-card">
            <div class="card-header-custom">
                <h2>
                    <i class="ti ti-cash"></i>
                    Payment Information
                </h2>
                <p>Add payments made to the supplier</p>
            </div>
            <div class="card-body-custom">
                <div class="info-box">
                    <i class="ti ti-info-circle"></i>
                    <p>Record all payments that you have made to the supplier. Click the button below to add payment entries.</p>
                </div>

                <div class="mb-4">
                    <button class="btn-add-payment" id="add_feild_button" type="button">
                        <i class="ti ti-plus"></i>
                        Add Payment Entry
                    </button>
                </div>

                <div id="payment-inputs" class="payment-section"></div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="btn-actions">
            <a href="javascript:history.back()">
                <button type="button" class="btn-custom btn-secondary-custom">
                    <i class="ti ti-arrow-left"></i>
                    Cancel
                </button>
            </a>
            <button type="submit" class="btn-custom btn-success-custom" id="submitBtn">
                <i class="ti ti-check"></i>
                Save Invoice
            </button>
        </div>
    </form>
</div>
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
        $('#submitBtn').addClass('loading').prop('disabled', true);
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
                            $("#service_div").html('<div class="alert alert-warning"><i class="ti ti-alert-triangle"></i> Please add services to the tour first</div>');
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