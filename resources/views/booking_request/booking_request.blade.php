<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('assets/scss_2/style.css') }}">

    <link rel="stylesheet" href="{{ asset('css/adminlte-app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/font-awesome-4.7.0/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ionicons.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tagify/4.17.9/tagify.min.css"
        integrity="sha512-yWu5jVw5P8+IsI7tK+Uuc7pFfQvWiBfFfVlT5r0KP6UogGtZEc4BxDIhNwUysMKbLjqCezf6D8l6lWNQI6MR7Q=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" type="text/css" href="{{asset('css/bootstrap-tables.css')}}"/>
	
</head>
<title>Document</title>
<style>
    body {
        background-color: #ecf0f5 !important;

    }
	label{
		font-weight:700;
	}

    .card {
        border-radius: 0px;
    }

    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        appearance: none;
        margin: 0;

    }

    input[type="number"] {
        width: 60px;
        height: 35px;
    }

    input[type="text"],
    input[type="date"] {
        height: 35px;
    }

    .form-control {

        border-radius: 5px !important;
		    padding: 2px !important;
    }

    .form-select {
        font-size: 12px;
    }

    .td1 {
        width: 350px;
        height 10px;
        padding: 24px;
    }

    .keys {
        font-size: 20px;
        font-weight: 550;
        width: 150px;
    }

    .values {
        font-size: 20px;
        font-weight: 500;
    }

    h1 {
        font-size: 60px;
    }

    .custom-file-input::-webkit-file-upload-button {
        visibility: hidden;
        width: 20px;
    }

    .custom-file-input::before {
        content: 'Select file';
        display: inline-block;
        background: linear-gradient(top, #f9f9f9, #e3e3e3);
        border: 1px solid #999;
        border-radius: 3px;
        padding: 5px 8px;
        outline: none;
        white-space: nowrap;
        -webkit-user-select: none;
        cursor: pointer;
        text-shadow: 1px 1px #fff;
        font-weight: 700;
        font-size: 10pt;
    }

    .custom-file-input:hover::before {
        border-color: black;
    }

    .custom-file-input:active::before {
        background: -webkit-linear-gradient(top, #e3e3e3, #f9f9f9);
    }
	th{
		width: 104px;
	}
</style>
</head>

<body>
    <div class="main">
        <div class="main-content">
            <section class="default-sec offer">
                <div class="container">
                    <div class="card mb-5 mb-lg-0">
                        <div class="card-body">
                            <div class = "ps-lg-5">
                                @if (Session::has('success'))
                                    <div class="alert alert-success">
                                        {{ Session::get('success') }}
                                    </div>
                                @endif
                                @if (Session::has('error'))
                                    <div class="alert alert-danger">
                                        {{ Session::get('error') }}
                                    </div>
                                @endif
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h1>Request a Quote</h1>

                                        <p class="card-title">Tour:{{ $tour_package->getTour()->name }}</p>

                                    </div>
                                    <div>
                                        <img style="height:160px" src="{{ asset('/img/eets_logo_small.jpg') }}" />
                                        <p class="date text-end"></p>
                                    </div>

                                </div>
                                <table class="mt-3" border= "1">
                                    <tr>
                                        <td class="td1">{{ $tour_package->name }}</td>
                                        <td class="td1">
                                            <b>{{ date('F j Y, h:i a', strtotime($tour_package->time_from)) }}</b></td>
                                    </tr>
                                </table>

                                <p class="mt-3" style="font-size:20px">Please quote from the following</p>
                                <table class="mt-3">
                                    <tr>
                                        <td class="keys">Supplier : </td>
                                        <td class="values"><b>{{ $tour_package->name }}</b></td>
                                    </tr>
                                    <tr>
                                        <td class="keys">Check in :</td>
                                        <td class="values"><b>{{ date('F j, Y', strtotime($tour_package->time_from)) }}
                                            </b></td>
                                    </tr>
                                    <tr>
                                        <td class="keys">Check out : </td>
                                        <td class="values">
                                            <b>{{ date('F j, Y', strtotime($tour_package->time_to)) }}</b></td>
                                    </tr>
                                    <tr>
                                        <td class="keys">Arrival :</td>
                                        <td class="values"><b>{{ date('h:m', strtotime($tour_package->time_from)) }}
                                            </b></td>
                                    </tr>
                                </table>
                                <br>
                                <table class="mt-10">
                                    @foreach ($selected_room_types as $selected_room_type)
                                        <tr style ="margin-top:10px">
                                            <td class="keys">{{ $selected_room_type->name }}:</td>
                                            <td class="values"><b>{{ $selected_room_type->count_room }}</b></td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>

							
                            <div class="ps-lg-5">
                                @if (count($errors) > 0)
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif



                                <p class="mt-3" style="font-size:20px">Please create your offer for us </p>
                                <h1 class="main-title mt-5">Create Offer</h1>
                                {{-- <p style="color:red">Please Login to see your Offer History</p>
                                <div class="input-wrapper">
                                    <a class="btn btn-secondary"
                                        href="{{ url('TMS-Supplier/login') }}">Login</a>
                                </div> --}}


                                <form method="POST" id="hoteloffers_add_form" class="form-light"
                                    action='{!! url('tour_package') !!}/{!! $tour_package->id !!}/offer_update'
                                    id="tour_package_add_form" enctype="multipart/form-data">
                                    <input type='hidden' name='_token' value='{{ Session::token() }}'>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type='hidden' name='_token' value='{{ Session::token() }}'>
											<div class="input-wrapper col-md-4">
												 <label for="tags-input">Send copy of reply to this email address</label>

                                    <input type="text" id="tags-input" name="emails"
                                        value="{{ $tour_package->service()->work_email }}" style = "width:60rem">
                                            </div>
                                            <div class="input-wrapper col-md-4">
												 <label for="reference">{!! trans('Ref No:') !!}</label>
                                                <input type="text" class="form-control"
                                                    placeholder="Your Booking Refrence" id="reference"name="reference">
                                                <input type="hidden" value="{{ $tour_package->pax }}" id="pax">
                                                <input type="hidden" value="{{ $tour_package->id }}"
                                                    name = "package_id">
                                            </div>
                                            <div class="input-wrapper col-md-4">
                                                <label for="status">{!! trans('main.Status') !!}</label>
                                                <select name="status" id="status"
                                                    class="form-select">
                                                    <option value="Offered No rooms blocked" >Offered No rooms
                                                        blocked
                                                    </option>
                                                    <option value="Offered with Option" selected>Offered with Option</option>
                                                    <option value="Waiting List">Waiting List</option>
                                                    <option value="Unavailable">Unavailable</option>

                                                </select>
                                            </div>
                                            <div class="input-wrapper col-md-4" id="option_with_date">
                                                <label for="status">{!! trans('Option Date') !!}</label>
                                                <input type="date" class="form-control" placeholder=""
                                                    name="option_with_date" required>
                                            </div>
											
                                            <div class="">
												@php
											$printedRoomNames = [];
										@endphp
												
												@foreach ($selected_room_types as $selected_room_type)
												<input type="hidden" name ="room_type_id[]"
                                                        value="{{ $selected_room_type->id }}">
												@if (!in_array($selected_room_type->name, $printedRoomNames))
													<div class ="row">
                                                            <label for="singleRate">{{ $selected_room_type->name }}</label>
                                                            <div class="input-wrapper" style="width: 113px;">
                                                                <input class="form-control" type="text"
                                                                    id="singleRate"
                                                                    name="room_rate_{{ $selected_room_type->id }}"
                                                                    placeholder="Rate" onkeyup="myFunction(this.value,'{{ $selected_room_type->name }}pp')">
                                                            </div>
                                                            <div class="input-wrapper col-md-5 mt-2"
                                                                style = "margin-left:5rem">
                                                                <label for="singleBreakfastIncluded">Breakfast
                                                                    included</label>
                                                                <input type="checkbox" id="singleBreakfastIncluded"
                                                                    name="is_breakfast_{{ $selected_room_type->id }}"
                                                                    checked>
                                                            </div>
                                                        </div>
													@php
														$printedRoomNames[] = $selected_room_type->name;
													@endphp
												@endif
										@endforeach
                                          @php
											$printedRoomNames = [];
										@endphp
												
												@foreach ($selected_room_types as $selected_room_type)
												@if (!in_array($selected_room_type->name, $printedRoomNames))
													<table class="table table-striped table-bordered table-hover">
														<tr >
                                                            <th>{{ $selected_room_type->name }} per person</th>
														</tr>
														<tr >
															<td id="{{ $selected_room_type->name }}pp">0</td>
														</tr>
                                                        </table>
													@php
														$printedRoomNames[] = $selected_room_type->name;
													@endphp
												@endif
										@endforeach     
                                                <div class="row">
                                                    <div class="input-wrapper" style="width: 113px;">
                                                        <label for="halfboardMax">City Tax:</label>
                                                        <input class="form-control" type="city_tax" id="city_tax"
                                                            name="city_tax" min="0" style="width: 60px;">
                                                    </div>

                                                    <div class="input-wrapper" style="width: 150px;">
                                                        <label for="portrage_perperson">Porterage P.P:</label>
                                                        <input class="form-control" type="portrage_perperson"
                                                            id="portrage_perperson" name="portrage_perperson"
                                                            min="0" style="width: 60px;">
                                                    </div>
                                                    <div class="input-wrapper" style="width: 200px;">
                                                        <label for="halfboard">Halfboard Supp P.P:</label>
                                                        <input class="form-control" type="number" id="halfboard"
                                                            name="halfboard" min="0" max="999999"
                                                            style="width: 60px;">
                                                    </div>
                                                </div>
                                                <div class="row">



                                                    <div class="input-wrapper" style="width: 150px;">
                                                        <label for="foc_after_every_pax">Childeren Cost:</label>
                                                        <input class="form-control" type="number"
                                                            id="children_cost" name="children_cost"
                                                            min="0" style="width: 50px;">
                                                    </div>
													<div class="input-wrapper" style="width:  109px;">
                                                        <label for="foc_after_every_pax">F.O.C:</label>
                                                        <input class="form-control" type="number"
                                                            id="foc_after_every_pax" name="foc_after_every_pax"
                                                            min="0" style="width: 50px;">
                                                    </div>

                                                    <div class="input-wrapper" style="width: 150px;">
                                                        <label for="halfboardMax">Max allowed per group:</label>
                                                        <input class="form-control" type="number" id="halfboardMax"
                                                            name="halfboardMax" min="0" style="width: 50px;">
                                                    </div>
                                                </div>

                                                <div class="input-wrapper col-md-4">
                                                    <label for="currency">Currency</label>
                                                    <select name="currency" id="currency"
                                                        class=" form-select">
                                                        @foreach ($currencies as $currency)
                                                            <option value="{{ $currency->id }}"
                                                                {{ $currency->id == $tour_package->currency ? 'selected' : '' }}>
                                                                {{ $currency->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                            </div>

                                            <div class="mb-3">

                                                <input class="form-control" type="file" id="formFileDisabled"
                                                    name="supplier_file">
                                            </div>
                                            <div class="input-wrapper">
                                                <textarea name="hotel_note" id="" rows="4" class="form-control" placeholder="Note"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h3>Other Conditions</h3>
                                            <textarea class="form-control" id="otherConditions" name="otherConditions" rows="4" cols="50"></textarea>

                                            <h2>Cancellation Policies</h2>
                                            <div class="row">
                                                <div class="input-wrapper" style="width: 113px;">
                                                    <input type="number" class="form-control" id="cancellationDays"
                                                        min="0">
                                                </div>
                                                <div class="input-wrapper col-md-2">
                                                    <label for="cancellationDays">Days before Arrival:</label>
                                                </div>

                                                <div class="input-wrapper" style="width: 113px;">
                                                    <input type="number" class=" form-control"
                                                        id="cancellationPercentage" min="0">
                                                </div>
                                                <div class="input-wrapper col-md-4">
                                                    <div class="input-group-append">
                                                        <select class="form-select"
                                                            id="cancellationType">
                                                            <option value="percentage">Percentage</option>
                                                            <option value="amount">Amount</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div>
                                                    <p>of rooms can be cancelled free of charge</p>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-primary"
                                                id="addCancellation">+</button>
                                            <div id="cancellationRequirements">
                                                <!-- Cancellation requirements will be added here dynamically -->
                                            </div>
                                            <div class="input-wrapper mt-3">
                                                <label for="cancellationNote">Additional Cancellation
                                                    Policies:</label>
                                                <textarea class="form-control" id="cancellationNote" name="cancellationNote" rows="4"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <button class="btn btn-primary" type = "submit">Submit</button>
                                </form>
                            </div>

                        </div>
                    </div>

                </div>

                <div class="container card mt-5">
                    <div class="card-body" >
                        <h1>Hotel Offers</h1>
						

                        @foreach ($tour_package->hotel_offers as $offer)
                        @endforeach
                        @if (empty($offer))
                            <p>We dont have any offers yes please send email to get offer from supplier</p>
                        @else
						
                            <div class="table-responsive" style="max-height: 400px;">
								<table id="offers-table" class="table table-striped table-bordered table-hover" style='background:#fff; width: 100%; table-layout: auto ;'>
                    <thead>
                    <th>ID</th>
                    <th style="width: 104px;">{!!trans('Status')!!}</th>
						<th style="width: 104px;">{!!trans('Delete')!!}</th>
						@php
											$printedRoomNames = [];
										@endphp

										@foreach ($selected_room_types as $selected_room_type)
											@if (!in_array($selected_room_type->name, $printedRoomNames))
												<th class="rooms-title">{{ $selected_room_type->name }}</th>
												@php
													$printedRoomNames[] = $selected_room_type->name;
												@endphp
											@endif
										@endforeach
                    <th>{!!trans('Currency')!!}</th>
					<th>{!!trans('City Tax')!!}</th>
                    <th>{!!trans('Halfboard Supp p.p')!!}</th>
						
					<th>{!!trans('foc')!!}</th>
                    <th>{!!trans('Max per group')!!}</th>
					<th>{!!trans('Portrage pp')!!}</th>
					<th>{!!trans('Hotel File')!!}</th>
					<th>{!!trans('Hotel Note')!!}</th>
                    <th class="actions-button" style="width: 140px!important">{!!trans('main.Actions')!!}</th>
                    </thead>
                    <tfoot>
                    <tr>
                        <th class="not"></th>
                    <th>{!!trans('Status')!!}</th>
						@php
											$printedRoomNames = [];
										@endphp

										@foreach ($selected_room_types as $selected_room_type)
											@if (!in_array($selected_room_type->name, $printedRoomNames))
												<th class="rooms-title">{{ $selected_room_type->name }}</th>
												@php
													$printedRoomNames[] = $selected_room_type->name;
												@endphp
											@endif
										@endforeach
                    <th>{!!trans('Currency')!!}</th>
					<th>{!!trans('City Tax')!!}</th>
                    <th>{!!trans('Halfboard Supp p.p')!!}</th>
					<th>{!!trans('foc')!!}</th>
                    <th>{!!trans('Max per group')!!}</th>
					<th>{!!trans('Portrage pp')!!}</th>
					<th>{!!trans('Hotel File')!!}</th>
					<th>{!!trans('Hotel Note')!!}</th>
                        <th class="not"></th>
                    </tr>
                    </tfoot>
                </table>
                                
                            </div>
                        @endif
                        <h1 class="mt-5">Comments From Suppliers</h1>


                        <ul id="comment_list">
                            @foreach ($comments as $comment)
                            @endforeach
                            @if (empty($offer))
                                <p>We dont have any offers yes please send email to get offer from supplier</p>
                            @else
                                @foreach ($comments as $comment)
                                    @if ($comment->supplier_name == $tour_package->name)
                                        <li>
                                            <strong>{{ $comment->supplier_name ?? '' }}</strong> said:
                                            <p>{{ $comment->content ?? '' }}</p>
                                        </li>
                                    @endif
                                @endforeach


                            @endif
                        </ul>

                        <form id="commentForm" method="POST" class="form-light"
                            action='{{ route('add_comment', [$tour_package->id]) }}' id="tour_package_add_form">
                            <input type='hidden' name='_token' value='{{ Session::token() }}'>
                            <div class="input-wrapper">
                                <textarea name="comment" id="" rows="4" class="form-control" placeholder="Write you comment"></textarea>
                            </div>
                            <button class="btn btn-primary">Add Comment</button>
                        </form>
                        <h1 class="mt-5">Emails From TMS</h1>
                        <div class="row">
                            <div class="col-md-12">
                                @if (!empty($tms_emails))
                                    <ul class="timeline">

                                        @foreach ($tms_emails as $tmsemail)
                                            @php
                                                $dateString = $tmsemail->header->date;

                                                // Create a DateTime object from the string
                                                $dateTime = new DateTime($dateString);

                                                // Get the date and time separately
                                                $date = $dateTime->format('D m.Y'); // Format the date as desired
                                                $time = $dateTime->format('H:i:s');
                                            @endphp
                                            {{-- {{dd($email)}} --}}
                                            <li class="time-label">
                                                <span class="bg-red">
                                                    {{ $date }}
                                                </span>
                                            </li>


                                            <li>
                                                <i class="fa fa-envelope bg-blue"></i>
                                                <div class="timeline-item">
                                                    <span class="time"><i class="fa fa-clock-o"></i>
                                                        {{ $time }}</span>
                                                    <h3 class="timeline-header"><a
                                                            href="#">{{ $tmsemail->header->from ?? '' }}</a>
                                                        sent
                                                        email to Supplier<b>
                                                            :{{ $tmsemail->header->subject ?? '' }}</b>
                                                    </h3>
                                                    <div class="timeline-body">
                                                        {!! $tmsemail->message->html ?? '' !!}
                                                    </div>
                                                    {{--     <div class="timeline-footer">
                                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#exampleModal">reply to supplier</button>
                                                    </div>
                                                </div> --}}
                                            </li>
                                        @endforeach

                                    </ul>
                                @else
                                    <p> Required Service does not reply yet Contact them for furthur Inquiry</p>
                                    <p>Or You do not include work email in tms dashboard yet</p>
                                @endif
                            </div>

                        </div>


                        <h1>Emails From Supplier</h1>
                        <div class="row">
                            <div class="col-md-12">
                                @if (!empty($emails))
                                    <ul class="timeline">

                                        @foreach ($emails as $email)
                                            @php
                                                $dateString = $email->header->date;

                                                // Create a DateTime object from the string
                                                $dateTime = new DateTime($dateString);

                                                // Get the date and time separately
                                                $date = $dateTime->format('D m.Y'); // Format the date as desired
                                                $time = $dateTime->format('H:i:s');
                                            @endphp

                                            <li class="time-label">
                                                <span class="bg-red">
                                                    {{ $date }}
                                                </span>
                                            </li>


                                            <li>
                                                <i class="fa fa-envelope bg-blue"></i>
                                                <div class="timeline-item">
                                                    <span class="time"><i class="fa fa-clock-o"></i>
                                                        {{ $time }}</span>
                                                    <h3 class="timeline-header"><a
                                                            href="#">{{ $email->header->from ?? '' }}</a> reply
                                                        to
                                                        your
                                                        email<b> :{{ $email->header->subject ?? '' }}</b></h3>
                                                    <div class="timeline-body">
                                                        {!! $email->message->html ?? '' !!}
                                                    </div>
                                                    {{--     <div class="timeline-footer">
                                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#exampleModal">reply to supplier</button>
                                                    </div>
                                                </div> --}}
                                            </li>
                                        @endforeach
                                    @else
                                        <p> Required Service does not reply yet Contact them for furthur Inquiry</p>
                                        <p>Or You do not include work email in tms dashboard yet</p>
                                @endif

                                </ul>

                            </div>

                        </div>




                    </div>
                </div>
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">New message</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form>

                                    <div class="mb-3">
                                        <label for="message-text" class="col-form-label">Message:</label>
                                        <textarea class="form-control" id="message-text"></textarea>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                    data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary">Send message</button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmDeleteModalLabel">Confirm Delete</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete this record?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
         <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
      </div>
    </div>
  </div>
</div>	

    <script type="text/javascript" src="{{ asset('js/lib/jquery.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <script src="{{ asset('https://cdnjs.cloudflare.com/ajax/libs/gsap/1.14.2/TweenMax.min.js') }}"></script>
    {{--<script src="{{ asset('https://cdnjs.cloudflare.com/ajax/libs/ScrollMagic/1.3.0/jquery.scrollmagic.js') }}"></script>
    <script src="{{ asset('https://cdnjs.cloudflare.com/ajax/libs/ScrollMagic/1.3.0/jquery.scrollmagic.debug.js') }}">
    </script>--}}
    <!-- <script src="assets/js/scrollmagic.min.js"></script>
    <script src="assets/js/debug.addindicators.js"></script> -->
   {{--<script src="{{ asset('assets/js/custom.js') }}"></script>--}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tagify/4.17.9/tagify.js"
        integrity="sha512-rwhfF2uV7kLu7CBnyhxtxXpDNksNqePq6uHvv+yPaxjtVBfTvlalKFmxJWrkMD2fERPzjedRUGZaRKcsJhC9Xg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>


</body>
<script type="text/javascript" src="{{asset('js/bootstrap-tables.js')}}"></script>
<script>
    const input = document.querySelector('#tags-input');
    const tagify = new Tagify(input, {
        duplicates: false, // Prevent duplicate tags
        whitelist: [{
            value: '{{ $tour_package->service()->work_email }}',
            readonly: true
        }],
    });
    $("#status").change(function() {
        let val = $(this).val();
        if (val === "Offered with Option") {
            $("#option_with_date").show();
            $("#option_with_date input").prop("required",
            true); // Set the input inside #option_with_date as required
        } else {
            $("#option_with_date").hide();
            $("#option_with_date input").prop("required", false); // Remove the required attribute
        }
    });
    /*
	$('#price_person').on('input', function() {
    var value1 = parseFloat($('#price_person').val());
    var value2 = parseFloat($('#pax').val());
		
    
    // Perform the multiplication operation
    var result = value1 * value2;

    // Update the result in the target element
    $('#total_price').val(result);
  });
	*/
    $('#commentForm').on('submit', function(e) {

        e.preventDefault(); // Prevent default form submission

        // Get the form data
        var formData = $(this).serialize();

        // Make an AJAX POST request to the form's action URL
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            success: function(response) {

                $("#comment_list").append(response);
            },
            error: function(xhr) {
                // Handle the error response
                // Display an error message or take appropriate action
            }
        });
    });

    document.getElementById('addCancellation').addEventListener('click', function() {
        const daysBeforeArrival = document.getElementById('cancellationDays').value;
        const percentageOrAmount = document.getElementById('cancellationPercentage').value;
        const cancellationType = document.getElementById('cancellationType').value;

        if (daysBeforeArrival && percentageOrAmount) {
            const daysInput = document.createElement('input');
            daysInput.type = 'hidden';
            daysInput.name = `cancellation_days[]`;
            daysInput.value = percentageOrAmount;
            const amountInput = document.createElement('input');
            amountInput.type = 'hidden';
            amountInput.name = `cancellation_percentage[]`;
            amountInput.value = daysBeforeArrival;
            const cancellationTypeInput = document.createElement('input');
            cancellationTypeInput.type = 'hidden';
            cancellationTypeInput.name = `cancellation_type[]`;
            cancellationTypeInput.value = cancellationType;


            const form = document.getElementById('hoteloffers_add_form');
            form.appendChild(daysInput);
            form.appendChild(amountInput);
            form.appendChild(cancellationTypeInput);
            const cancellationRequirements = document.getElementById('cancellationRequirements');
            const newRequirement = document.createElement('div');
            newRequirement.innerHTML =
                `<p>${daysBeforeArrival} days before arrival: ${percentageOrAmount} ${cancellationType} can be cancelled free of charge</p>`;
            cancellationRequirements.appendChild(newRequirement);
            daysBeforeArrival.value = '';
            percentageOrAmount.value = '';
        }
    });

 

    invoice_items();

    function invoice_items() {
        let package_id = $("#package_id").val();
        $.ajax({
            url: '/tp/getaddEmails',
            method: 'GET',
            data: {
                package_id: package_id,
            }
        }).done((res) => {
            $('#additionalEmails').append(res);
            $('input[name="_token"]').each(function() {
                // Replace the 'value' attribute with your CSRF token value
                $(this).val("{{ csrf_token() }}");
            });
        });
    }

    $(document).on('click', '#delete_contact_item', function() {
        $(this).closest('.item-contact').remove();
    });

    document.addEventListener('DOMContentLoaded', function() {
        var form = document.getElementById('hoteloffers_add_form');
        var emailsInput = document.getElementById('tags-input');
/*
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            // Check if the emails input is empty
            if (!emailsInput.checkValidity()) {
                // Prevent the form from submitting
                event.preventDefault();

                // Display your custom validation message
                alert('Please enter a valid email.');
            }
        });*/
    });
	
	    $(document).ready(function() {
        let table = $('#offers-table').DataTable({
            dom: 	"<'row'<'col-sm-5'l><'col-sm-2'B><'col-sm-5'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            buttons: [
                {
                    extend: 'csv',
                    title: 'Current Offers List',
                    exportOptions: {
                        columns: ':not(.actions-button)'
                    }
                },
                {
                    extend: 'excel',
                    title: 'Offers List',
                    exportOptions: {
                        columns: ':not(.actions-button)'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Offer List',
					orientation: 'landscape',
                    exportOptions: {
                        columns: ':not(.actions-button)'
                    }
                }
            ],
            processing: true,
            serverSide: true,
            pageLength: 50,
            ajax: {
                url: "{{route('offers_data',[$tour_package->id,1])}}",
            },
            columns: [
				{data: 'id', name: 'id'},
				{data: 'status', name: 'status'},
				{data: 'supplier_delete', name: 'supplier_delete'},
				@foreach ($selected_room_types as $selected_room_type)
                { "data": "{{ $selected_room_type->code }}" },
            	@endforeach
                {data: 'currency', name: 'currency'},
				{data: 'city_tax', name: 'city_tax'},
				{data: 'halfboard', name: 'halfboard'},
				{data: 'foc_after_every_pax', name: 'foc_after_every_pax'},
				{data: 'halfboardMax', name: 'halfboardMax'},
				{data: 'portrage_perperson', name: 'portrage_perperson'},
				{data: 'hotel_file', name: 'hotel_file'},
                {data: 'hotel_note', name: 'hotel_note'},
               
                {data: 'action', name: 'action', searchable: false, sorting: false, orderable: false}
               
            ],
			"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
			console.log(aData);
			if(aData.supplier_delete == 1){
			$(nRow).css('background', '#ffbbb2');
			}
               
            }
		 });
        $('#offers-table tfoot th').each( function () {
            let column = this;
            if (column.className !== 'not') {
                let title = $(this).text();
                $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');
            }
        });
        table.columns().every( function () {
            let that = this;

            $('input', this.footer()).on('keyup change', function() {
                if(that.search() !== this.value) {
                    that.search(this.value).draw();
                }
            });
        });
        $('#offers-table tfoot th').appendTo('#offers-table thead');
    });
</script>
<script>
  $(document).ready(function () {
    var recordToDeleteId;
setTimeout(function () {
    $('.delete').on('click', function () {
      recordToDeleteId = $(this).data('link');
      $('#confirmDeleteModal').modal('show');
    });

    $('#confirmDelete').on('click', function () {
      // Make an AJAX request to delete the record
      $.ajax({
        url: '' + recordToDeleteId,
        type: 'get',
        success: function (data) {
          // Assuming the server returns success
          if (data.success) {
            // Remove the row from the table
            $('tr[data-id="' + recordToDeleteId + '"]').remove();
          } else {
            // Handle deletion failure
            alert('Failed to delete record.');
          }
        },
        error: function () {
          // Handle AJAX error
          alert('Error occurred during deletion.');
        },
        complete: function () {
          // Hide the modal regardless of success or failure
          $('#confirmDeleteModal').modal('hide');
        }
      });
    });
 }, 3000);
  });
	
	function myFunction(val,val2){
		let x = document.getElementById(val2);
  		x.innerHTML = val/{{$tour_package->pax}};
	}
</script>

</html>
