@extends('scaffold-interface.layouts.app')
@section('title', 'Create')
@section('content')
    @include('layouts.title', [
        'title' => 'Supplier Invoices',
        'sub_title' => 'Create Invoice',
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
        <form id="myForm" method='POST' action='{!! url('invoices') !!}' enctype="multipart/form-data">
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
                                <h1>Invoice Detail</h1>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <input type='hidden' name='_token' value='{{ Session::token() }}'>
                            <div class="form-group">
                                <label>{!! trans('main.Files') !!}</label>
                                @component('component.file_upload_field')
                                @endcomponent
                            </div>


                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="office_id">{{ trans('Office') }} *</label>
                                <select name="office_id" id="office_id" class="form-control" required>

                                    @foreach ($offices as $office)
                                        <option value="{{ $office->id }}">{{ $office->office_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group" required>
                                <label for="name">{!! trans('Total Amount') !!} *</label>
                                {!! Form::text('total_amount', '', ['class' => 'form-control', 'required']) !!}
                            </div>
							<div class="form-group">
                                <label for="name">{!! trans('Extra Cost') !!} </label>
                                {!! Form::text('extra_amount', '', ['class' => 'form-control', '']) !!}
                            </div>
							{{--
							<div class="form-group" >
                                <label for="name">{!! trans('Only Extra Cost') !!} </label>
                                <input  name="" id="only_extra_cost" type="checkbox" value="">
                            </div>--}}
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="name">{!! trans('Invoice No') !!} *</label>
                                {!! Form::text('invoice_no', '', ['class' => 'form-control', 'required']) !!}
                            </div>

                            <div class="form-group">
                                <label for="tour_id">{{ trans('main.Tour') }} *</label>
                                <select name="tour_id[]" id="tour_id" class="form-control select22" multiple="multiple" required>

                                    @foreach ($tours as $tour)
                                        <option value="{{ $tour->id }}">{{ $tour->name }}</option>
                                    @endforeach
                                </select>
                            </div>
							
                            <div class="form-group" id="services" style="display:none">

                            </div>
                            <div class="form-group" id="service_div">
                            </div>
							<div class="form-group">
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
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
	$(document).ready(function() {
		$('.select22').select2({
			placeholder: "Select permissions",
			allowClear: true
		});
	});
</script>

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

        $(document).ready(function() {
            var previousSelection = [];
            var selected = [];
            var appendedInputs = []; // Store the appended input elements

            $("#tour_id").change(function() {
	
                var selectedValues = $(this).val();
		
                // Clear selected array when changing tour selection
                selected = [];
                if (selectedValues === null) {
                    selectedValues = [];
                    $("#services").hide();
                   
                } else {

                    if (selectedValues.length > previousSelection.length) {
                        // New tour(s) selected

                        var newSelection = selectedValues.filter(function(value) {
                            return !previousSelection.includes(value);
                        });

                        selected = selected.concat(newSelection);
                    } else if (selectedValues.length < previousSelection.length) {
                        // Tour(s) deselected
		
                        var deselected = previousSelection.filter(function(value) {
                            return !selectedValues.includes(value);
                        });

                        selected = selected.filter(function(value) {
                            return !deselected.includes(value);
                        });
                    }else{
						 var newSelection = selectedValues.filter(function(value) {
                            return !previousSelection.includes();
                        });
						selected = selected.concat(newSelection);
					}
                }
                console.log("selected"+selected);

                if (selected.length > 0) {
                    var lastSelectedValues = selected.slice(-1); // Get the latest selected values

                    $.each(lastSelectedValues, function(index, selectedValue) {
                        $.ajax({
                            type: "GET",
                            url: APP_URL + '/' + selectedValue,
							 data: {
									multiple: 1,
								},
                            success: function(result) {

                                if (result[0] === "") {
                                    $("#service_div").show();
                                    $("#services").hide();
                                    $("#service_div").html(
                                        `<h3> Please Add Service in the tour </h3>`);
                                } else {
                                    $("#service_div").hide();
                                    $("#services").show();
                                    $("#services").append(result);
                                }
                            },
                            error: function(result) {
                                console.log(result);
                            }
                        });
                    });
                } else {
                    // No tours selected

                    //  $.each(appendedInputs, function(index, input) {
                    //  input.remove(); // Remove the specific appended input fields
                    //  });
                    //  appendedInputs = []; // Clear the stored appended inputs
                    //$("#service_div").hide();
                    //  alert('ok');



                    var deselected = previousSelection.filter(function(value) {
                        return !selectedValues.includes(value);
                    });


                    $(`#service${deselected}`).remove();
                    $(`#lable-service${deselected}`).remove();


                    $.each(appendedInputs, function(index, input) {
                        input.remove(); // Remove the specific appended input fields
                    });
                    appendedInputs = []; // Clear the stored appended inputs
                    //$("#service_div").hide();

                }

                previousSelection = selectedValues;
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
		$('#only_extra_cost').on('click',function(){
			let val = $(this).val();
		
			});
		/*
		$('#only_extra_cost').on('click',function(){
			let val = $(this).prop('checked');
			if(val){
				 $('#services').remove();
				$('#tour_id').val(null);
			}else{
				var newDiv = $('<div class="form-group" id="services" style="display:none">');
          
				$('#service_div').after(newDiv);
				$('#tour_id').val(null);
				selected = [];
				
			}
		});*/
		

    </script>
@endsection
