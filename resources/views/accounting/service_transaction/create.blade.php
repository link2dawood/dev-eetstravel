@extends('scaffold-interface.layouts.app')
@section('title', 'Create')
@section('content')
    @include('layouts.title', [
        'title' => 'Customer Transaction',
        'sub_title' => 'Create Billing',
        'breadcrumbs' => [
            ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
            ['title' => 'Invoices', 'icon' => 'suitcase', 'route' => route('tour.index')],
            ['title' => 'Create', 'route' => null],
        ],
    ])
    <section class="content">
        <div class="box box-primary">
            <div class="box box-body border_top_none">
                
                <form method='POST' action='{!! url('accounting') !!}' enctype="multipart/form-data">
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
                    `<div class="row">
                        <div class="col-md-12">
                            <div class="margin_button">
                                <a href="javascript:history.back()">
                                    <button type="button" class='btn btn-primary back_btn'>{!! trans('main.Back') !!}</button>
                                </a>
                                <button class='btn btn-success' type='submit'>{!! trans('main.Save') !!}</button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <input type='hidden' name='_token' value='{{ Session::token() }}'>
							<div class="form-group">
                                <label for="office_id">{{ trans('Office') }}</label>
                                <select name="office_id" id="office_id" class="form-control">

                                     @foreach ($offices as $office)
                                        <option value="{{ $office->id }}">{{ $office->office_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                           
                            <div class="form-group">
                                <label for="service" >{{ trans('Tour') }}</label>
                                 <input class="form-control" name='tours' value='{{ $tourName }}' disabled>
								 
                            </div>
                            <div class="form-group" id="services" style = "display:none">
                                <label for="service" >{{ trans('Service') }}</label>
                                <select id="service" name="service" id="service" class="form-control">
                                    
                                </select>
                            </div>
							 <div class="form-group" id = "service_div">
                            </div>
							{{--
                          <div  class="form-group">
                                    <label for="name">{!! trans('Service') !!} *</label>
                                    <select id="serviceDropdown"class="form-control" >
                                        <option name = "service"selected>{!! trans('Hotels') !!}</option>
                                        @foreach ($options as $option)
                                            <option>
                                                @if ($option === 'Transfer')
                                                    Bus Company
                                                @else
                                                    {{ $option }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                           --}}
                            <div class="form-group">
                                <label for="name">{!! trans('Total Amount') !!} *</label>
                                {!! Form::text('total_amount', '', ['class' => 'form-control']) !!}
                            </div>
                            <div class="form-group">
                                <label for="name">{!! trans('Extra Amount') !!} *</label>
                                {!! Form::text('extra_amount', '', ['class' => 'form-control']) !!}
                            </div>
                            <div class="form-group">
                                <label for="name">{!! trans('Amount Payable*') !!} *</label>
                                {!! Form::text('amount_payable', '', ['class' => 'form-control']) !!}
                            </div>

                        </div>
                    </div>

                </form>
            </div>
        </div>
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

      
		  APP_URL = '{{url("/supplierdropdown")}}';
        $(document).ready(function() {
			
			$("#tour_id").change(function() {
                var selectedValue = $(this).val();

                $.ajax({
            type: "GET",
            url: APP_URL + '/' + selectedValue,
         
            
            success: function(result) {               
               console.log(result);
				if(result[0] === ""){
					 $("#service_div").show();
					$("#services").hide();
                $("#service_div").html(`<h3> Please Add Service in tour </h3>`);
               }
               else{
				   $("#services").show();
				   $("#service_div").hide();
                $("#service").html(result);
				   
               }
            },
            error: function(result) {
                console.log(result);
            }
        });
                });
            $("#serviceDropdown").change(function() {
                

             
            });
        });
    </script>
@endsection
