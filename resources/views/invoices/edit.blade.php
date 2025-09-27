@extends('scaffold-interface.layouts.app')
@section('title', 'Edit')
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
        <form method='POST' action='{{route('invoice.update', ['invoice' => $invoices->id])}}' enctype="multipart/form-data">
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
							<input id="invoice_id" value="{{$invoices->invoices->id}}" type="hidden">
                           
							<div class="form-group">
                                <label for="office_id">{{ trans('Office') }}</label>
                                <select name="office_id" id="office_id" class="form-control" required>

                                    @foreach ($offices as $office)
                                        <option value="{{ $office->id }}" {{  $invoices->invoices->office_id ===  $office->id? 'selected' : '' }}>{{ $office->office_name }}</option>
                                    @endforeach
                                </select>
                            </div>
							 <div class="form-group">
                                <label for="name">{!! trans('Invoice No') !!} *</label>
								 <input class="form-control" required="" name="invoice_no" type="text" value="{{$invoices->invoices->invoice_no}}">
                            </div>


                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="tours">{{ trans('main.Tour') }}</label>
                                <select name="tours" id="tour_id" class="form-control">

                                    @foreach ($tours as $tour)
                                        <option value="{{ $tour->id }}" {{  $invoices->tours->name ===  $tour->name ? 'selected' : '' }}>{{ $tour->name }}</option>
                                    @endforeach
                                </select>
                            </div>
							
							
                            <div class="form-group" id="services" >
							
                            </div>
                            <div class="form-group" id="service_div">
                            </div>
                        </div>
                        <div class="col-md-3">
                           
							<div class="form-group" required>
                                <label for="name">{!! trans('Total Amount') !!} *</label>
								<input class="form-control" required="" name="total_amount" type="text" value="{{$invoices->invoices->total_amount}}">
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
                            <div>Payments that you were paid 
								to Supplier?</div>
                        </div>
                       
                        
                        <div class="margin_button">
                            <button class="custom-button" id="add_feild_button" type='button'>
                                <i class="fa fa-plus"></i>
                                {!! trans('Add Payment') !!}
                            </button>
                        </div>
    
                        <div class="row">
                         
                                <div id="payment-inputs" class="row col-md-10">
    
                                </div>
                           
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
@endsection
