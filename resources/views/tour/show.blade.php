@extends('scaffold-interface.layouts.app')
@section('title', 'Show')
@section('content')
    @include('layouts.title', [
        'title' => 'Tour',
        'sub_title' => $tour->name,
        'breadcrumbs' => [
            ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
            ['title' => 'Tours', 'icon' => 'suitcase', 'route' => route('tour.index')],
            ['title' => 'Show', 'route' => null],
        ],
    ])

    {{-- modal for service table --}}

    <div class="modal fade" role='dialog' id="service-modal" style="padding-left: 17px;padding-right: 17px;">
        <div class="modal-dialog modal-lg" role='document' style="width: 90%;">
            <div class="modal-content" style="overflow: hidden;">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss='modal' aria-label="Close"><span
                            aria-hidden='true'>&times;</span></button>
                    <h4 class="modal-title">{!! trans('main.Addservice') !!}</h4>
					
					
                    {{-- <div class="col-md-6"> --}}
                    <form action="{{ route('supplier_show') }}">
                        <div class="form-group" style="margin: 15px;">
                            <div class="form-check" style="display:inline;">
                                <input id="service-selec" type="radio" class="form-check-input option-radio"
                                    name="selected_options[]" value="All">
                                <label class="form-check-label options-label" for="service-select">
                                    All
                                </label>
                            </div>
                            @foreach ($options as $option)
                                <div class="form-check" style="display:inline; margin: 12px;">
                                    <input type="radio" class="form-check-input option-radio"
                                        id="{{ strtolower($option) }}-checkbox" name="selected_options[]"
                                        value="{{ $option }}">
                                    <label class="form-check-label options-label" for="{{ strtolower($option) }}-checkbox">
                                        @if ($option === 'Transfer')
                                            Bus Company
                                        @else
                                            {{ $option }}
                                        @endif
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </form>
					<div id="hotel_service_create" style="display: none;">
						<a class="btn btn-success" href="{{route('hotel.create')}}" target="_blank">
                <i class="fa fa-plus fa-md" aria-hidden="true"></i> New</a>
					</div>
					<div id="guide_service_create" style="display: none;">
						<a class="btn btn-success" href="{{route('guide.create')}}" target="_blank">
                <i class="fa fa-plus fa-md" aria-hidden="true"></i> New</a>
					</div>
					<div id="event_service_create" style="display: none;">
						<a class="btn btn-success" href="{{route('event.create')}}" target="_blank">
                <i class="fa fa-plus fa-md" aria-hidden="true"></i> New</a>
					</div>
					<div id="res_service_create" style="display: none;">
						<a class="btn btn-success" href="{{route('restaurant.create')}}" target="_blank">
                <i class="fa fa-plus fa-md" aria-hidden="true"></i> New</a>
					</div>
					<div id="bus_service_create" style="display: none;">
						<a class="btn btn-success" href="{{route('bus.create')}}" target="_blank">
                <i class="fa fa-plus fa-md" aria-hidden="true"></i> New</a>
					</div>
                    {{-- </div> --}}
                </div>
                <div class="box box-body table-responsive" style="border-top: none">
                    <table id="search-table" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>{!! trans('main.Name') !!}</th>
                                <th>{!! trans('main.Address') !!}</th>
                                <th>{!! trans('main.Country') !!}</th>
                                <th>{!! trans('main.City') !!}</th>
                                <th>{!! trans('main.Phone') !!}</th>
                                <th>{!! trans('main.ContactName') !!}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>{!! trans('main.Name') !!}</th>
                                <th>{!! trans('main.Address') !!}</th>
                                <th>{!! trans('main.Country') !!}</th>
                                <th>{!! trans('main.City') !!}</th>
                                <th>{!! trans('main.Phone') !!}</th>
                                <th>{!! trans('main.ContactName') !!}</th>
                                <th></th>
                            </tr>
                        </tfoot>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade in" id="question_modal" tabindex="-1" role="dialog" aria-labelledby="myQuestionLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">×</span></button>
                    <h4 class="modal-title" id="myModalLabel">{!! trans('main.Warning') !!}!!</h4>
                </div>
                <div class="modal-body">
                    <div class="modal-body">{!! trans('main.WouldyouliketosendGuestList') !!}?</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{!! trans('main.Close') !!}</button>
                    <button type="button" class="btn btn-primary" id="send_agree">{!! trans('main.Agree') !!}</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="error_send">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss='modal' aria-label="Close"><span
                            aria-hidden='true'>&times;</span></button>
                    <h4 class="modal-title" id="title_modal_error">{!! trans('main.Warning') !!}!</h4>
                </div>
                <div class="modal-body">
                    <h3 class="error_send_message"></h3>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" role='dialog' id="service-description">
        <div class="modal-dialog" role='document'  style="
    width: 90%;
">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss='modal' aria-label="Close"><span
                            aria-hidden='true'>&times;</span></button>
                    <h4 class="modal-title">{!! trans('main.Adddescriptionpackage') !!}</h4>
                </div>
                <div class="box box-body" style="border-top: none">
                    <form action="{{ route('description_package') }}" method="Post" id="description-service">
						  {{csrf_field()}}
                        <div class="form-group">
                            <label for="description">{!! trans('main.Text') !!}</label>
							<h2>Select Time</h2>
							<label for="appt">Select a time:</label>
<input type="time" id="time" name="time">
							<div class="form-group">
                        	<div class="input-group">
							 <span class="input-group-addon"> {!! trans('main.Template') !!}</span>
							<select id="desc_template_selector" name="desc_template_selector" class="form-control">
                            </select>
								</div>
							</div>
                            {{-- <input type="text" name="description" class="form-control" required> --}}
                            <textarea name="description" id="description"  class="form-control" style="width: 100%; resize: vertical;"></textarea>
                        </div>
                        <input type="text" hidden="hidden" id="tour_day_id" name="tourDayId">
                        <button type="submit" class="btn btn-primary pre-loader-func">{!! trans('main.Create') !!}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" role='dialog' id="service-description-edit">
        <div class="modal-dialog" role='document'>
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss='modal' aria-label="Close"><span
                            aria-hidden='true'>&times;</span></button>
                    <h4 class="modal-title">{!! trans('main.Editdescriptionpackage') !!}</h4>
                </div>
                <div class="box box-body" style="border-top: none">
                    <form action="{{ route('description_package') }}" method="Post" id="description-service-edit">
						{{csrf_field()}}
                        <div class="form-group">
                            <label for="description-edit">Text</label>
                            {{-- <input type="text" name="description" class="form-control" required> --}}
                            <textarea name="description-edit" id="description-edit" class="form-control" style="width: 100%; resize: vertical;"></textarea>
                        </div>
                        <input type="text" hidden="hidden" id="tour_day_id" name="tourDayId">
                        <button type="button" class="btn btn-primary save-description">{!! trans('main.Save') !!}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="box box-primary">
            <div class="box box-body border_top_none">
                <div class="row">
                    <div class="col-md-3" style="margin-bottom: 10px;">
                        <div class="margin_button">
                            <a href="javascript:history.back()">
                                <button class='btn btn-primary'>{!! trans('main.Back') !!}</button>
                            </a>
                            @if (Auth::user()->can('tour.edit'))
                                <a href="{!! route('tour.edit', ['tour' => $tour->id]) !!}">
                                    <button class='btn btn-warning'>{!! trans('main.Edit') !!}</button>
                                </a>
                            @endif
                            @if (Auth::user()->can('task.create'))
                                <a href="{!! url('task') !!}/create?tour={!! $tour->id !!}"
                                    class='btn btn-success'>{!! trans('main.AddTask') !!}</a>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-5">
                        <ul>
                            <li style="display: inline-block;">
                                <div class="dropdown">
                                    <button class="btn btn-default" id="csvLabel" type="button"
                                        data-toggle="dropdown">
                                        image
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="csvLabel">
                                        <li><a class="btn btn-default" href="#"
                                                onclick='export_to("{{ route('tour_export', ['id' => $tour->id, 'export' => 'csv', 'type' => 'tour']) }}");'
                                                href="">Tour</a></li>
                                        <li><a class="btn btn-default"
                                                onclick='export_to("{{ route('tour_export', ['id' => $tour->id, 'export' => 'csv', 'type' => 'service']) }}");'
                                                href="#">Service</a></li>
                                    </ul>
                                </div>
                            </li>
                            <li style="display: inline-block;"><a class="btn btn-default"
                                    onclick='export_to("{{ route('tour_export', ['id' => $tour->id, 'export' => 'xlsx']) }}");'
                                    href="#">Excel</a></li>
                            
							<li style="display: inline-block;">
                                <div class="dropdown">
                                    <button class="btn btn-default" id="voucherLabel" type="button"
                                        data-toggle="dropdown">
                                        {!! trans('main.Voucher') !!}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="csvLabel">
                                        <li><a class="btn btn-default"
                                                onclick='export_to("{{ route('tour_pdf_export', ['id' => $tour->id, 'pdf_type' => 'voucher']) }}");'
                                                href="#">PDF</a></li>
                                        <li><a class="btn btn-default"
                                                onclick='export_to("{{ route('tour_doc_export', ['id' => $tour->id, 'doc_type' => 'voucher']) }}");'
                                                href="#">DOC</a></li>
                                    </ul>
                                </div>
                            </li>
                            <li style="display: inline-block;">
                            </li>
							

                            <li style="display: inline-block;">
                                <div class="dropdown">
                                    <button class="btn btn-default" id="itenaryLabel" type="button"
                                        data-toggle="dropdown">
                                        {!! trans('main.Itinerary') !!}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="csvLabel">
                                        <li><a class="btn btn-default"
                                                onclick='export_to("{{ route('tour_pdf_export', ['id' => $tour->id, 'pdf_type' => 'short']) }}");'
                                                href="#">PDF</a></li>
                                        <li><a class="btn btn-default"
                                                onclick='export_to("{{ route('tour_html_export', ['id' => $tour->id, 'type' => 'html']) }}");'
                                                href="#">HTML</a></li>
                                        <li><a class="btn btn-default"
                                                onclick='export_to("{{ route('tour_doc_export', ['id' => $tour->id, 'doc_type' => 'short']) }}");'
                                                href="#">DOC</a></li>
                                    </ul>
                                </div>
                            </li>
							
							<li style="display: inline-block;">
                                <div class="dropdown">
                                    <button class="btn btn-default" id="hotellistLabel" type="button"
                                        data-toggle="dropdown">
                                        {!! trans('main.Hotellist') !!}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="csvLabel">
                                        <li><a class="btn btn-default"
                                                onclick='export_to("{{ route('tour_pdf_export', ['id' => $tour->id, 'pdf_type' => 'hotel']) }}");'
                                                href="#">PDF</a></li>
                                        <li><a class="btn btn-default"
                                                onclick='export_to("{{ route('tour_doc_export', ['id' => $tour->id, 'doc_type' => 'hotel']) }}");'
                                                href="#">DOC</a></li>
                                    </ul>
                                </div>
                            </li>
							
                            {{--
                            <li style="display: inline-block;"><a class="btn btn-default"
                                                                  onclick='export_to("{{route('landing_page', ['id' => $tour->id])}}");'
                                                                  href="#">Landing page</a>
                            </li>
--}}
                            <li style="display: inline-block;"><button onclick="showmodal()"
                                    class="btn btn-default">Landing page</button></li>
                            <a id = "quotation_to_tour_href"
                                href="{{ route('tour.convert_to_tour', ['id' => $tour->id]) }}"></a>
                            <a id = "tour_to_quotation"
                                href="{{ route('tour.convertToQuotation', ['id' => $tour->id]) }}"></a>
                        </ul>
                    </div>
                    <div class="col-md-4">
						
                        <span id="help" class="legend_tour btn btn-box-tool pull-right">
                            <i class="fa fa-question-circle " aria-hidden="true"></i>
							
                            @include('legend.tour_service_legend')
                        </span>
						<h2>Select Office</h2>
						<div>
							
						<select class="selectedOfice">
							@foreach($offices as $office)
							@if(isset( $select_office->id))
							<option value="{{$office->id}}" {{ ( $office->id == $select_office->id) ? 'selected' : '' }}>{{$office->office_name}}</option>
							@else
							<option value="{{$office->id}}">{{$office->office_name}}</option>
							@endif
							@endforeach
						</select>
							
							<button class="btn btn-primary btn-sm select" style="margin-bottom:5px;margin-top:5px;">Select</button>
						</div>
                    </div>
                </div>
                @if ($tour->is_quotation)
                    <div class="callout callout-info" style="
    background: rgb(255, 249, 176) !important;color:black !important;
">
                        <div class="row">
                            <div class="col-md-8">
                                <h5>Convert Quotation to Tour</h5>
                            </div>
                            <div class="col-md-4">
                                <div class="toggle" style = "float:right; margin-right:30px">
                                    <input type="checkbox" id= "check1" onclick = "quotation_to_tour()" checked />
                                    <label></label>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="callout callout-info" style="
    background:rgb(202, 255, 189) !important; color:black !important;
">
                        <div class="row">
                            <div class="col-md-8">
                                <h5>Convert Tour to Quotation</h5>
                            </div>
                            <div class="col-md-4">
                                <div class="toggle" style = "float:right; margin-right:30px">
                                    <input type="checkbox" id= "check2" onclick = "tour_to_quotation()" />
                                    <label></label>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="row">

                    <div class="col-md-12 info-row"></div>
                    <div class="col-md-12">
                        <div class="alert alert-warning alert-dismissable msg" hidden>
                            <button type="button" class="close" data-dismiss='alert' aria-hidden='true'>x</button>
                        </div>
                    </div>
                </div>

                <div id="fixed-scroll" class="nav-tabs-custom">
                    <ul class="nav nav-tabs" role='tablist'>
                        <li role='presentation' class="tab" data-tab="frontsheet-tab"><a href="#frontsheet-tab" aria-controls='frontsheet-tab'
                                role='tab' data-toggle='tab' id="frontsheet_tab">Front Sheet</a></li>
                        <li role='presentation' class="  tab" data-tab="service-tab"><a href="#service-tab" aria-controls='service-tab' role='tab'
                                data-toggle='tab' id="service_tab" >{!! trans('main.Services') !!}</a></li>
                        <li role='presentation' class="tab" data-tab="tour-tab"><a href="#tour-tab" aria-controls='tour-tab' role='tab'
                                data-toggle='tab'>{!! trans('main.Tour') !!}</a></li>
                        <li role='presentation' class="tab" data-tab="quotation-tab"><a href="#quotation-tab" aria-controls='quotation-tab' role='tab'
                                data-toggle='tab' id="quotation_tab">{!! trans('main.Quotation') !!}</a></li>
                        <li role='presentation' class="tab" data-tab="roomlist-tab"><a href="#roomlist-tab" aria-controls='roomlist-tab' role='tab'
                                data-toggle='tab' id="roomlist_tab" >{!! trans('main.GuestList') !!}</a></li>
                        <li role='presentation' class="tab" data-tab="invoices-tab"><a href="#invoices-tab" aria-controls='invoices-tab' role='tab'
                                data-toggle='tab' id="invoices_tab" >{!! trans('Invoices') !!}</a></li>
                        <li role='presentation' class="tab" data-tab="billing-tab"><a href="#billing-tab" aria-controls='invoices-tab' role='tab'
                                data-toggle='tab' id="billing_tab">{!! trans('Billing') !!}</a></li>

                    </ul>
                </div>
                <div class="tab-content">

                    <div role='tabpanel' class="tab-pane fade in" id="service-tab">
                        {{-- services tables --}}


                        <div class="tour-packages"></div>

                        {{-- tasks component --}}
                        @include('component.list_tasks_for_tour', [
                            'listIdTasks' => $listIdTasks,
                            'tour' => $tour,
                            'tasksData' => $tasksData,
                        ])
                        <span id="showPreviewBlock" data-info="{{ true }}"></span>
                        <div class="box box-success" style="position: relative; left: 0px; top: 0px; border-top: none">
                            <div class="box-header ui-sortable-handle" style="cursor: move;">
                                <i class="fa fa-comments-o"></i>

                                <h3 class="box-title">{!! trans('main.Comments') !!}</h3>
                            </div>
                            <div class="box-body">
                                <div class="slimScrollDiv" style="position: relative; overflow-y: scroll;  width: auto;">
                                    <div class="box-body box chat" id="chat-box" style="width: auto; height: auto;">
                                        <div id="show_comments"></div>
                                    </div>
                                    <div class="slimScrollRail"
                                        style="width: 7px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; background: rgb(51, 51, 51) none repeat scroll 0% 0%; opacity: 0.2; z-index: 90; right: 1px;">
                                    </div>
                                </div>
                            </div>
                            <!-- /.chat -->
                            <div class="box-footer">
                                <form method='POST' action='{{ route('comment.store') }}' enctype="multipart/form-data"
                                    id="form_comment">
                                    <div class="input-group" style="width: 100%">
                                        <span id="author_name" class="input-group-addon">
                                            <span id="name"></span>
                                            <a href="#" id="reply_close"><i class="fa fa-close"></i></a>
                                        </span>
                                        <textarea class="form-control" id="content" name="content" placeholder="Ctrl + Enter to post comment"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>{!! trans('main.Files') !!}</label>
                                        @component('component.file_upload_field')
                                        @endcomponent
                                    </div>
                                    <input type="text" id="parent_comment" hidden name="parent"
                                        value="{{ null }}">
                                    <input type="text" id="default_reference_id" hidden name="reference_id"
                                        value="{{ $tour->id }}">
                                    <input type="text" id="default_reference_type" hidden name="reference_type"
                                        value="{{ \App\Comment::$services['tour'] }}">

                                    <button type="submit" class="btn btn-success pull-right" id="btn_send_comment"
                                        style="margin-top: 5px;">{!! trans('main.Send') !!}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    {{-- tour info tab --}}
                    <div role='tabpanel' class="tab-pane fade" id="tour-tab">
                        <div class="row">
                            <div class="col-md-6">
                                <table class='table table-bordered' id="pdf-table">
                                    <tbody>
                                        <tr>
                                            <td><b><i>{!! trans('main.Name') !!}</i></b></td>
                                            <td>{!! $tour->name ?? '' !!}</td>
                                        </tr>
                                        <tr>
                                            <td><b><i>{!! trans('main.ExternalName') !!}</i></b></td>
                                            <td>{!! $tour->external_name ?? '' !!}</td>
                                        </tr>

                                        <tr>
                                            <td><b><i>{!! trans('main.Pax') !!}</i></b></td>
                                            <td>{!! $tour->pax ?? '' !!}</td>
                                        </tr>
                                        <tr>
                                            <td><b><i>{!! trans('main.PaxFree') !!}</i></b></td>
                                            <td>{!! $tour->pax_free ?? '' !!}</td>
                                        </tr>

                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class='table table-bordered' id="pdf-table">
                                    <tbody>
                                        <tr>
                                            <td><b><i>{!! trans('main.DepDate') !!}</i></b></td>
                                            <td>{!! $tour->departure_date ?? '' !!}</td>
                                        </tr>
                                        <tr>
                                            <td><b><i>{!! trans('main.RetDate') !!}</i></b></td>
                                            <td>{!! $tour->retirement_date ?? '' !!}</td>
                                        </tr>
                                        <tr>
                                            <td><b><i>{!! trans('main.Status') !!}</i></b></td>
                                            <td>{{ $status->name }}</td>
                                        </tr>

                                        <tr>
                                            <td><b><i>{!! trans('main.RoomsHotel') !!}</i></b></td>
                                            <td>
                                                @php
                                                    $peopleCount = 0;
                                                @endphp
                                                @foreach ($listRoomsHotel as $item)
                                                    @php
                                                        $peopleCount += isset(App\TourPackage::$roomsPeopleCount[$item->room_types->code]) ? App\TourPackage::$roomsPeopleCount[$item->room_types->code] * $item->count : 0;
                                                    @endphp
                                                    <span>
                                                        {{ $item->room_types->code ?? '' }}
                                                        {{ $item->count ?? '' }}
                                                    </span>
                                                @endforeach
                                                <br>
                                                @if ($peopleCount != $tour->pax + $tour->pax_free)
                                                    <div class="alert alert-warning alert-dismissible">
                                                        <button type="button" class="close" data-dismiss="alert"
                                                            aria-hidden="true">×
                                                        </button>
                                                        <i class="icon fa fa-warning"></i>
                                                        Pax Count ({{ $tour->pax + $tour->pax_free }}) is not equal to the
                                                        number of people in the rooms ({{ $peopleCount }})
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                        @if (Auth::user()->hasRole('admin'))
                                            <tr>
                                                <td><b><i>{!! trans('main.AssignedUser') !!}</i></b></td>
                                                <td>
                                                    @foreach ($tour->users as $user)
                                                        {{ $user->name }}
                                                    @endforeach
                                                </td>
                                            </tr>
                                        @else
                                            <tr>
                                                <td><b><i>{!! trans('main.AssignedUser') !!}</i></b></td>
                                                <td>
                                                    @foreach ($tour->users as $user)
                                                        {{ $user->name ?? '' }}
                                                    @endforeach
                                                </td>
                                            </tr>
                                        @endif

                                        <tr>
                                            <td><b><i>{!! trans('main.Phone') !!}</i></b></td>
                                            <td>
                                                {!! $tour->phone ?? '' !!}
                                            </td>
                                        </tr>

                                        <tr>
                                            <td><b><i>{!! trans('main.ResponsibleUser') !!}</i></b></td>
                                            <td>
                                                {{ $tour->getResponsibleUser() ? $tour->getResponsibleUser()->name : '' }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        @component('component.files', ['files' => $files, 'tour' => $tour])
                        @endcomponent
                    </div>

                    <div role='tabpanel' class="tab-pane fade" id="comments-tab">

                    </div>
                    <div role="tabpanel" class="tab-pane fade" id="quotation-tab">
                        <div class="box box-success">
                            <div class="box-header ui-sortable-handle" style="cursor: move;">
                                <i class="fa fa-comments-o"></i>

                                <h3 class="box-title">{!! trans('main.Quotations') !!}</h3>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        @if (Auth::user()->can('quotation.add'))
                                            <a href="{{ route('quotation.add', ['id' => $tour->id]) }}">
                                                <button type="button"
                                                    class="btn btn-block btn-success btn-flat">{!! trans('main.AddQuotation') !!}
                                                </button>
                                            </a>
                                        @endif
                                    </div>
                                    <div class="col-md-9">
                                        <span class="quotation_legend btn btn-box-tool pull-right"><i
                                                class="fa fa-question-circle" aria-hidden="true"></i>
                                            @include('legend.quotation_legend')
                                        </span>
                                    </div>
                                </div>
                                <br>
                                <table class="table table-bordered finder-disable">
                                    <tr>
                                        <td>{!! trans('main.Name') !!}</td>
                                        <td>{!! trans('main.Assigned') !!}</td>
                                        <td>{!! trans('main.Frontsheet') !!}</td>
                                        <td>{!! trans('main.Print') !!}</td>
                                        <td>{!! trans('Excel') !!}</td>
                                        <td>{!! trans('main.CreatedAt') !!}</td>
                                    </tr>
                                    @foreach ($tour->quotations as $key => $quotation)
                                        @php
                                            if ($quotation->is_confirm == 0) {
                                                $style = 'background-color:#ff00008f';
                                            } else {
                                                $style = 'background-color:#caffbd';
                                            }

                                        @endphp
                                        <tr style={{ $style }}>
                                            <td>
                                                @if (Auth::user()->can('quotation.edit'))
                                                    <a href="{{ route('quotation.edit', ['quotation' => $quotation->id]) }}">
                                                        {{ $quotation->name ?? '' }}
                                                    </a>
                                                @else
                                                    <span>{{ $quotation->name ?? '' }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $quotation->userName() ?? '' }}</td>
                                            <td>
                                                @if (Auth::user()->can('comparison.show'))
                                                    <a href="{{ route('comparison.show', ['comparison' => $quotation->id]) }}">{!! trans('main.Front') !!}
                                                        sheet</a>
                                                @else
                                                    <span>{!! trans('main.Nopermission') !!}.</span>
                                                @endif
                                            </td>
                                            <td><a target="_blank"
                                                    href="{{ route('quotation.pdf', ['id' => $quotation->id]) }}"
                                                    class="btn btn-primary btn-xs show-button"><i class="fa fa-print"
                                                        aria-hidden="true"></i></a>
                                            </td>
                                            <td><a target="_blank"
                                                    href="{{ route('quotation.excel', ['id' => $quotation->id]) }}"
                                                    class="btn btn-primary btn-xs show-button"><i
                                                        class="fa fa-file-excel-o" aria-hidden="true"></i></a>
                                            </td>
                                            <td>{{ Carbon\Carbon::parse($quotation->created_at)->format('d-m-Y') }}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane fade" id="roomlist-tab">
                        <div class="box box-success">
                            <div class="box-header ui-sortable-handle" style="cursor: move;">
                                <i class="fa fa-address-card"></i>

                                <h3 class="box-title">{!! trans('main.Guestlists') !!}</h3>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        @if (Auth::user()->can('guestList.add'))
                                            <a href="{{ route('guestList.add', ['id' => $tour->id]) }}">
                                                <button type="button"
                                                    class="btn btn-block btn-success btn-flat">{!! trans('main.Add') !!}
                                                    {!! trans('main.Guestlist') !!}
                                                </button>
                                            </a>
                                        @endif
                                    </div>
                                    <div class="col-md-9">
                                        <span class="guest_list_legend btn btn-box-tool pull-right"><i
                                                class="fa fa-question-circle" aria-hidden="true"></i>
                                            @include('legend.guest_list_legend')
                                        </span>
                                    </div>
                                </div>
                                <br>

                                <table class="table table-bordered finder-disable">
                                    @if (Auth::user()->can('guestlist.index'))
                                        <tr>
                                            <td>Id</td>
                                            <td>{!! trans('main.Name') !!}</td>
                                            <td>{!! trans('main.Author') !!}</td>
                                            <td>{!! trans('main.CreatedAt') !!}</td>
                                            <td>{!! trans('main.SentAt') !!}</td>
                                            <td>{!! trans('main.Hotels') !!}</td>
                                            <td>{!! trans('main.Send') !!}</td>
                                        </tr>
                                        @foreach ($tour->guestLists as $key => $guestList)
                                            <tr>
                                                <td>
                                                    {{ $guestList->version }}
                                                </td>
                                                <td>
                                                    @if (Auth::user()->can('guestList.showbyid'))
                                                        <a href="{{ route('guestList.showbyid', ['id' => $guestList->id]) }}"
                                                            class="pre-loader-func guest_list_show">{{ $guestList->name }}
                                                        </a>
                                                    @else
                                                        <span>{{ $guestList->name ?? '' }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $guestList->getAuthor()->name }} -
                                                    {{ $guestList->getAuthor()->email }}
                                                </td>
                                                <td>
                                                    {{ Carbon\Carbon::parse($guestList->created_at)->format('d-m-Y') }}
                                                </td>
                                                <td class="sent_at">
                                                    @if ($guestList->sent_at)
                                                        {{ Carbon\Carbon::parse($guestList->sent_at)->format('d-m-Y') }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @foreach ($guestList->getSelectedHotelNames() as $hotelName)
                                                        {{ $hotelName }},
                                                        @if ($loop->index > 0)
                                                        @break
                                                    @endif
                                                @endforeach
                                                @if (Auth::user()->can('guestList.showbyid'))
                                                    <a href="{{ route('guestList.showhotelemailsbyid', ['id' => $guestList->id]) }}"
                                                        class="pre-loader-func guest_list_show_email_hotels">
                                                        more...</a>
                                                @endif
                                            </td>
                                            <td>
                                                @if (!$guestList->sent_at)
                                                    <button
                                                        data-url="{{ route('guestlist.send', ['id' => $tour->id, 'guestlistid' => $guestList->id]) }}"
                                                        class="btn btn-primary btn-xs aftersend">
                                                        <i class="fa fa-paper-plane-o" aria-hidden="true"></i>
                                                    </button>
                                                    <button class="btn btn-danger btn-xs delete_guest_list"
                                                        data-url="{{ route('guestlist.delete', ['id' => $tour->id, 'guestlistid' => $guestList->id]) }}">
                                                        <i class="fa fa-trash-o" aria-hidden="true"></i>
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    No permission.
                                @endif
                            </table>

                        </div>
                    </div>
                </div>

                <div role="tabpanel" class="tab-pane fade in" id="invoices-tab">
					<div id="tour_create" style="margin-bottom : 20px;">
                    {!! \App\Helper\PermissionHelper::getCreateButton(route('invoices.create'), \App\Invoices::class) !!}
                </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover bootstrap-table" data-search="true" data-pagination="true" data-page-size="10">
                            <thead>
                                <tr>
                                    <th data-sortable="true">ID</th>
                                    <th data-sortable="true">Invoice No</th>
                                    <th data-sortable="true">Due Date</th>
                                    <th data-sortable="true">Received Date</th>
                                    <th data-sortable="true">Tour</th>
                                    <th data-sortable="true">Service</th>
                                    <th data-sortable="true">Office Name</th>
                                    <th data-sortable="true">Total Price</th>
                                    <th data-sortable="true">Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoicesData as $invoice)
                                <tr>
                                    <td>{{ $invoice['id'] }}</td>
                                    <td>{{ $invoice['invoice_no'] }}</td>
                                    <td>{{ $invoice['due_date'] }}</td>
                                    <td>{{ $invoice['received_date'] }}</td>
                                    <td>{{ $invoice['tour_name'] }}</td>
                                    <td>{{ $invoice['package_name'] }}</td>
                                    <td>{{ $invoice['office_name'] }}</td>
                                    <td>{{ $invoice['total_amount'] }}</td>
                                    <td>{{ $invoice['status'] }}</td>
                                    <td>
                                        <a href="{{ route('invoices.show', ['invoice' => $invoice['id']]) }}" class="btn btn-info btn-sm">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="{{ route('invoices.edit', ['invoice' => $invoice['id']]) }}" class="btn btn-warning btn-sm">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <a class="btn btn-danger btn-sm delete" data-toggle="modal" data-target="#myModal" data-link="/invoices/{{ $invoice['id'] }}/deleteMsg">
                                            <i class="fa fa-trash-o"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>

                <div role="tabpanel" class="tab-pane fade" id="billing-tab">
					<div id="tour_create" style="margin-bottom : 20px;">
                    {!! \App\Helper\PermissionHelper::getCreateButton(route('accounting.create'), \App\Tour::class) !!}
                </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover bootstrap-table" data-search="true" data-pagination="true" data-page-size="10">
                            <thead>
                                <tr>
                                    <th data-sortable="true">ID</th>
                                    <th data-sortable="true">Date</th>
                                    <th data-sortable="true">Tour Name</th>
                                    <th data-sortable="true">Office Name</th>
                                    <th data-sortable="true">Total Amount</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($billingData as $billing)
                                <tr>
                                    <td>{{ $billing['id'] }}</td>
                                    <td>{{ \Carbon\Carbon::parse($billing['date'] ?? now())->format('Y-m-d') }}</td>
                                    <td>{{ $billing['tour_name'] }}</td>
                                    <td>{{ $billing['office_name'] }}</td>
                                    <td>{{ $billing['total_amount'] }}</td>
                                    <td>
                                        <a href="{{ route('accounting.show', ['accounting' => $billing['id']]) }}" class="btn btn-info btn-sm">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="{{ route('accounting.edit', ['accounting' => $billing['id']]) }}" class="btn btn-warning btn-sm">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <a class="btn btn-danger btn-sm delete" data-toggle="modal" data-target="#myModal" data-link="/accounting/{{ $billing['id'] }}/deleteMsg">
                                            <i class="fa fa-trash-o"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>



                </div>
                <div role="tabpanel" class="tab-pane fade" id="frontsheet-tab">
                    <div class="box box-success">
                        <div class="box-header ui-sortable-handle" style="cursor: move;">


                            <div class="box-body">
                                <h2 class="page-header">
                                    <i class="fa fa-list" aria-hidden="true"></i> Front Sheet
                                    [{{ $quotation->name ?? '' }} - {{ $tour->name }}]
                                </h2>
                                <span id="help" class="btn btn-box-tool pull-right"><i
                                        class="fa fa-question-circle" aria-hidden="true"></i>
                                    @include('legend.frontsheet_legend')
                                </span>
                                @if(!empty($quotation) && $quotation->id)
                                <form action="{{ route('comparison.update', ['comparison' => $quotation->id]) }}"
                                    method="POST">
                                    <div style="margin-bottom: 10px;">
                                        <div class="row">
                                            <div class="col-md-12">
                                                {{-- <div class="box-header with-border">
                                                            <a class='btn btn-primary' href="javascript:history.back()">
                                                                {!!trans('main.Back')!!}
                                                            </a>
                                                            <button class='btn btn-success' type='submit'>{!!trans('main.Save')!!}</button>
                                                        </div> --}}
                                            </div>
                                        </div>

                                    </div>

                                    {{ csrf_field() }}
                                    {{ method_field('PUT') }}
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="lead">
                                                Rooms:
                                                @php
                                                    $peopleCount = 0;
                                                @endphp

                                                @foreach ($listRoomsHotel as $room)
                                                    @php
                                                        $peopleCount += isset(App\TourPackage::$roomsPeopleCount[$room->room_types->code]) ? App\TourPackage::$roomsPeopleCount[$room->room_types->code] * $room->count : 0;
                                                    @endphp
                                                    {{ $room->room_types->code }} : {{ $room->count }}
                                                @endforeach
                                                @if ($peopleCount != $tour->pax + $tour->pax_free)
                                                    <div class="alert alert-warning alert-dismissible">
                                                        <button type="button" class="close" data-dismiss="alert"
                                                            aria-hidden="true">×
                                                        </button>
                                                        <i class="icon fa fa-warning"></i>
                                                        {!! trans('main.PaxCount') !!}
                                                        ({{ $tour->pax + $tour->pax_free }}) is not equal to the
                                                        number of people in the rooms ({{ $peopleCount }})
                                                    </div>
                                                @endif
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="lead">
                                                Pax:
                                                {{ $tour->pax }} {{ $tour->pax_free }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="col-md-12" style="overflow: auto">
                                        <table class="table table-bordered finder-disable">
                                            <thead>
                                                <td>{!! trans('main.Date') !!}</td>
                                                <td>{!! trans('main.City') !!}</td>
												<td>{!! trans('Quote Single') !!}</td>
												<td>{!! trans('Quote SS') !!}</td>
												<td>{!! trans('Quote HPP') !!}</td>
                                                
                                                <td>
                                                    CMFD HOTEL
                                                </td>

                                                <td>{!! trans('main.Option') !!}</td>
												
                                                @php
                                                    $selected_room_count = 'selected_room_count';
                                                    $roomValues = [];
                                                @endphp
                                                @foreach ($listRoomsHotel as $room)
												@if ($room->room_types->code == 'SIN')
                                                    <td
                                                        @if ($room->room_types->code == 'SIN') data-container="body" data-toggle="tooltip" data-placement="bottom"
                                                            data-original-title="Single suppl." @endif>
                                                        Offer Single</td>
												@endif
                                                @endforeach

                                                
												<td>{!! trans('Offer SS') !!}</td>
												<td>{!! trans('Offer HPP') !!}</td>
												<td>&reg;</td>
                                                <td>
                                                    VC sent <br>to SHA
                                                </td>
                                                <td></td>
                                                <td>{!! trans('Budjet HPP +/-') !!}</td>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $overallSum = 0;
                                                    $count = 0;
                                                @endphp
                                                @foreach ($tour->getTourDaysSortedByDate() as $tourDay)
                                                    @php
                                                        $tourday_hotels = count($tourDay->hotels()) > 0 ? count($tourDay->hotels()) : 1;

                                                        $offer_hotel_count = 0;
                                                        if (count($tourDay->hotels()) != 0) {
                                                            foreach ($tourDay->hotels() as $hotel) {
                                                                $offer_hotel_count = $offer_hotel_count + count($hotel->hotel_offers);
                                                            }
                                                            $total = count($tourDay->hotels());
                                                        } else {
                                                            $offer_hotel_count = 1;
                                                            $total = 1;
                                                        }
                                                    @endphp
                                                    <tr>
                                                        <td rowspan="{{ $total }}">{{ $tourDay->date ?? '' }}
                                                        </td>

                                                        @if (count($tourDay->hotels()) != 0)
                                                            @foreach ($tourDay->hotels() as $hotel)
                                                                @php
																	if (!empty($quotation)){
														$quotehtlpp = (int)$quotation->getValueByDate($tourDay->date?? '', "htlpp");
                                                                    $quotationBudget = (int)$quotation->getValueByDate($tourDay->date?? '', "SIN") + $quotehtlpp;
														}else{
														$quotehtlpp = 0;
														 $quotationBudget =0;
														}
														
														            
                                                                    $realBudget = 0;
                                                                    $first_hotel = $tourDay->firstHotel();
                                                                    $count += 1;
                                                                    $offer_hotel_count = count($hotel->hotel_offers) > 0 ? count($hotel->hotel_offers) : 1;
                                                                    $offer_hotel_count = $offer_hotel_count + 1;

                                                                @endphp


                                                                <td>
                                                                    @if (!is_null($hotel) && method_exists($hotel, 'service') && isset($hotel->service()->cityObject))
                                                                        {{ $hotel->service()->cityObject->name ?? '' }}
                                                                    @endif
                                                                </td>
														@if (!empty($quotation))
														<td>{{(int)$quotation->getValueByDate($tourDay->date?? '', "SIN")+ (int)$quotation->getValueByDate($tourDay->date?? '', "htlpp")}}</td>
														<td>{{$quotation->getValueByDate($tourDay->date?? '', "SIN")?? ''}}</td>
														<td>{{$quotation->getValueByDate($tourDay->date?? '', "htlpp")?? ''}}</td>
														@else
														<td></td>
														<td></td>
														@endif
                                                                


                                                                <td>

                                                                    {{ $hotel->name ?? '' }}


                                                                </td>
                                                                
																@php $total_count = count($listRoomsHotel); $budjet = true; $count_room = 0; $hotelpp = 0;
														$ssp =0; $single =0;@endphp
                                                                @if (count($hotel->hotel_offers) != 0)
                                                                    <td rowspan="1">
                                                                        {{ $hotel->latestHotelOffer->status }}</td>
														
                                                                    
                                                                    @foreach ($listRoomsHotel as $selected_room_type)
																	
																		@php 
														
														
														
														if($selected_room_type->room_types->code == "SIN"){
														$single += $hotel->latestHotelOffer->offersWithRoomPrice($selected_room_type->room_types); 
														}else if( $selected_room_type->room_types->code == "TWN" || $selected_room_type->room_types->code == "DOU"){
														$hotelpp += $hotel->latestHotelOffer->offersWithRoomPrice($selected_room_type->room_types)/2; 
														
														
														$count_room += 1;
														}
														
														@endphp
														@if ($selected_room_type->room_types->code == 'SIN')
                                                                        <td>{{ $hotel->latestHotelOffer->offersWithRoomPrice($selected_room_type->room_types) }}
                                                                        </td>
																	@endif
                                                                    @endforeach
                                                                @else
                                                                    <td></td>
                                                                    @foreach ($listRoomsHotel as $selected_room_type)
																		@if ($selected_room_type->room_types->code == 'SIN')
                                                                        <td></td>
																		@endif
                                                                    @endforeach
                                                                @endif
														@php
														if( $count_room == 0){
														$count_room = 1;
														}
														$hotelpp = $hotelpp/$count_room ;
														$ssp = abs($single - $hotelpp);
														$realBudget = $ssp + $hotelpp ;
														@endphp
																	<td rowspan="1">{{$ssp??""}}</td>
														<td rowspan="1">{{$hotelpp??""}}</td>
                                                                <td>{{ Form::checkbox('rooming_list_reserved[]', $comparison->comparisonRowByDate($tourDay->date)->id ?? '', $comparison->comparisonRowByDate($tourDay->date)->rooming_list_reserved ?? '', ['class' => 'rooming_list_reserved']) }}
                                                                </td>
                                                                <td>{{ Form::checkbox('visa_confirmation[]', $comparison->comparisonRowByDate($tourDay->date)->id ?? '', $comparison->comparisonRowByDate($tourDay->date)->visa_confirmation ?? '', ['class' => 'visa_confirmation']) }}
                                                                </td>
                                                                <td>
                                                                    <a class="btn btn-block comments-button"
                                                                        data-row-id="{{ $comparison->comparisonRowByDate($tourDay->date)->id ?? '' }}"
                                                                        data-link="{{ route('comparison.comments', ['id' => $comparison->comparisonRowByDate($tourDay->date)->id ?? '']) }}/">
                                                                        <span
                                                                            class="badge bg-yellow">{{ \App\Helper\AdminHelper::getComparisonRowCommentsCount($comparison->comparisonRowByDate($tourDay->date)->id ?? '') }}</span>
                                                                        <i class="fa fa-comment-o"
                                                                            aria-hidden="true"></i>
                                                                    </a>
                                                                </td>
														<td data-toggle="tooltip" data-placement="top"
                                                                    title="({{ $quotehtlpp }} - ({{ $hotelpp }})) ">
                                                                    @php
                                                             
                                                                            $sum = $quotehtlpp - $hotelpp ;
                                                                      
                                                                    @endphp
                                                                    {{ round($sum, 2) }}
                                                                </td>
														{{--
                                                                <td data-toggle="tooltip" data-placement="top"
                                                                    title="({{ $quotationBudget }} - ({{ $realBudget }} + {{ $cityTax }})) / {{ $tour->pax }}">
                                                                    @php
                                                                        if ($tour->pax != 0) {
                                                                            $sum = ($quotationBudget - ($realBudget + $cityTax)) / $tour->pax;
                                                                        } else {
                                                                            $sum = 0;
                                                                        }
                                                                        $overallSum += $sum;
                                                                    @endphp
                                                                    {{ round($sum, 2) }}
                                                                </td>--}}


                                                    </tr>
                                                @endforeach
                                                @php $count = 0; @endphp
                                            @else
                                                @for ($i = 1; $i < 10; $i++)
                                                    <td rowspan="{{ $total }}"></td>
                                                @endfor
                                                @foreach ($listRoomsHotel as $selected_room_type)
                                                    <td></td>
                                                    <td></td>
                                                @endforeach
                                                @endif
                                                @endforeach
                                                <tr rowspan="{{ $offer_hotel_count }}">
                                                    <td colspan="{{ 8 + count($listRoomsHotel) * 2 }}">
                                                        {!! trans('main.ENDOFSERVICE') !!}</td>
                                                    <td>&#931; =</td>
                                                    <td>{{ round($overallSum, 2) }}</td>
                                                </tr>

                                                <!--  Bottom  -->
                                            </tbody>
                                        </table>
                                    </div>
                                </form>
                                @else
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle"></i> No quotation data available for front sheet.
                                </div>
                                @endif
                            </div>

                            <div class="col-md-8">
                                {{-- Popup create --}}
                                <div id="commentModal" class="modal fade in" tabindex="-1" role="dialog"
                                    aria-labelledby="commentModal">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <a class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">x</span>
                                                </a>
                                                <h4 id="modalCreateBusLabel" class="modal-title"></h4>
                                            </div>

                                        </div>

                                        <div class="modal-body">
                                            <div class="modal-body">
                                                <form id="comments">


                                                </form>
                                            </div>
                                        </div>


                                        {{-- <div class="modal-footer">
                                                <a href="close" class='btn btn-default' data-dismiss="modal">{!!trans('main.Close')!!}</a>
                                                <button class='btn btn-primary' id="update_btn_table_bus" type='button'>{!!trans('main.Save')!!}</button>
                                            </div> --}}
                                    </div>
                                </div>
                            </div>
                        </div>



                    </div>

                </div>
            </div>
        </div>

        {{-- @include('component.list_tasks_for_tour', ['listIdTasks' => $listIdTasks, 'tour' => $tour->name]) --}}
    </div>
    </div>
    <span id="tour_date_id" data-tour-id="{{ $tour->id }}"></span>
    <span id="tour_dates" data-departure_date='{{ $tour->departure_date }}'
        data-retirement_date='{{ $tour->retirement_date }}'></span>

    <div class="modal fade" tabindex="-1" id="date_modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="task_submit_form">
                    <div class="modal-header">
                        <button type="button" class="close btn-task-tour-hotel_cancel" data-dismiss='modal'
                            aria-label="Close"><span aria-hidden='true'>&times;</span></button>
                        <h4 class="modal-title">Add Task for the Tour "{{ $tour->name }}" users</h4>
                    </div>

                    <div class="row" style="padding: 2em;">
                        <div class="form-group col-md-6 col-lg-6" style="padding-left: 0">

                            <label for="departure_date">Date</label>

                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input class="form-control pull-right datepicker" id="start_date" name="start_date"
                                    type="text" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="form-group col-md-6 col-lg-6" style="padding-right: 0">

                            <label for="departure_date">Time</label>

                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-clock-o"></i>
                                </div>
                                <input class="form-control pull-right timepicker" id="end_time" name="end_time"
                                    type="text" value="18:00">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" id="tour_id" value="{{ $tour->id }}">
                        <button type="button" class="btn btn-default btn-task-tour-hotel_cancel"
                            data-dismiss="modal">Close</button>
                        <button type="button" id="submit_on_option_task" class="btn btn-primary"
                            id="">Create Task</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" id="error_send">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss='modal' aria-label="Close"><span
                            aria-hidden='true'>&times;</span></button>
                    <h4 class="modal-title" id="title_modal_error">Warning!</h4>
                </div>
                <div class="modal-body">
                    <h3 class="error_send_message"></h3>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" role='dialog' id="guest-list-modal">
        <div class="modal-dialog modal-lg" role='document' style="width: 90% !important; ">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss='modal' aria-label="Close"><span
                            aria-hidden='true'>&times;</span></button>
                    <h4 class="modal-title">Info</h4>
                </div>
                <div class="modal-body">

                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade in" id="question_modal" tabindex="-1" role="dialog" aria-labelledby="myQuestionLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myModalLabel">Warning!!</h4>
            </div>
            <div class="modal-body">
                <div class="modal-body">Would you like to send Guest List to selected tour hotels?</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary pre-loader-func" id="send_agree">Agree</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade in" id="landingpage_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myModalLabel">Warning!!</h4>
            </div>
            <div class="modal-body">
                <div class="modal-body">There is no image for landing page. Are you sure you want to generate the page?
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary pre-loader-func" id="open-landing"
                    onclick='export_to("{{ route('landing_page', ['id' => $tour->id]) }}");'>Agree</button>
            </div>
        </div>
    </div>
</div>

<span hidden id="tourimage"
    data-image="@if ($tour->attachments()->first() != null) {{ $tour->attachments()->first()->url }} @endif">
    @if ($tour->attachments()->first() != null)
        {{ $tour->attachments()->first()->url }}
    @endif
</span>
<span id="url" hidden data-url="{{ route('images.savefile') }}"></span>
<script type="text/javascript">
    var selectedGuestList;

    $(document).on('dblclick', '.price_tour_text', function() {
        $(this).find('span').css({
            'display': 'none'
        });
        $(this).find('input').css({
            'display': 'block'
        });
        $(this).find('input').select();
    });

    $("#checkboxallhotels").click(function() {
        if ($("#checkboxallhotels").is(':checked')) {
            $("#hotelselect > option").prop("selected", "selected");
            $("#hotelselect").trigger("change");
        } else {
            $("#hotelselect > option").removeAttr("selected");
            $("#hotelselect").trigger("change");
        }
    });
    const tour_id = $('#tour_date_id').attr('data-tour-id');

    $(document).on('keydown blur', '#new_price_total_amount', function(e) {
        if (e.type === 'keydown') {
            if (e.keyCode === 13) {
                changeTotalAmount($(this).val(), $('#new_price_for_one').val(), $(this));
            }
        } else {
            changeTotalAmount($(this).val(), $('#new_price_for_one').val(), $(this));
        }
    });

    $(document).on('keydown blur', '#new_price_for_one', function(e) {
        if (e.type === 'keydown') {
            if (e.keyCode === 13) {
                changePriceForOne($(this).val(), $('#new_price_total_amount').val(), $(this));
            }
        } else {
            changePriceForOne($(this).val(), $('#new_price_total_amount').val(), $(this));
        }
    });

    function changeTotalAmount(new_price, def_price, _this) {
        $.ajax({
            method: 'POST',
            url: '/change_tour_price',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                price_total_amount: new_price,
                price_for_one: def_price,
                tour_id: tour_id
            }
        }).done((res) => {
            $(_this).css({
                'display': 'none'
            });
            $(_this).attr('value', res.total_amount);
            $(_this).prev('span').css({
                'display': 'block'
            });
            $(_this).prev('span').text(res.total_amount);
        });
    }

    function changePriceForOne(new_price, def_price, _this) {
        $.ajax({
            method: 'POST',
            url: '/change_tour_price',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                price_total_amount: def_price,
                price_for_one: new_price,
                tour_id: tour_id
            }
        }).done((res) => {
            $(_this).css({
                'display': 'none'
            });
            $(_this).attr('value', res.price_for_one);
            $(_this).prev('span').css({
                'display': 'block'
            });
            $(_this).prev('span').text(res.price_for_one);
        });
    }

    function showmodal() {
        var img = $("#tourimage").data("image");
        if (!img) {
            $('#landingpage_modal').modal();
        } else {
            window.open("{{ route('landing_page', ['id' => $tour->id]) }}", '_blank');
            //                window.location.href = "{{ route('landing_page', ['id' => $tour->id]) }}";
        }
    };

    $(document).keyup(function(e) {
        if (e.keyCode === 27) {
            $('#new_price_for_one').css({
                'display': 'none'
            });
            $('#new_price_total_amount').css({
                'display': 'none'
            });

            $('#new_price_for_one').prev('span').css({
                'display': 'block'
            });
            $('#new_price_total_amount').prev('span').css({
                'display': 'block'
            });
        }
    });

    $('.aftersend').on('click', function() {
        selectedGuestList = $(this);
        $('#question_modal').modal();
    });

    $('.delete_guest_list').on('click', function() {
        if (confirm('Are you sure to delete guest list?')) {
            $.ajax({
                method: 'GET',
                url: jQuery(this).data("url"),
                data: {},
            }).done((res) => {
                document.location.reload(true);

            });

        }
    });

    $('#send_agree').on('click', function() {
        var self = selectedGuestList;
        let block_overlay = '<div class="overlay" id="overlay_delete">\n' +
            '\t\t<i class="fa fa-refresh fa-spin"></i>\n' +
            '\t</div>';
        let overlay_component = self.closest('.box-body');
        overlay_component.append(block_overlay);
        self.hide();
        $.ajax({
            method: 'GET',
            url: self.data("url"),
            data: {},
            beforeSend: function() {
                $('#question_modal').modal('hide');
            },
        }).done((res) => {
            $('#error_send').find('#title_modal_error').html('');

            if (res.error === 'error') {
                $('#error_send').find('.error_send_message').html(res.message);
                $('#error_send').find('#title_modal_error').html('Warning!');
            } else {
                $('#error_send').find('.error_send_message').html(res.message);
                if (res.broke) {
                    $('#error_send').find('.error_send_message').append('<br><br>' + res.broke);
                }
                $('#error_send').find('#title_modal_error').html('Success!');
            }
            $('#overlay_delete').remove();
            $('#error_send').modal();

            setTimeout(function() {
                $('#error_send').modal('hide');
                if (res.error != 'error') {
                    self.closest("tr").find(".sent_at").html(res.sent_at);
                } else {
                    self.show();
                }
            }, 3000);
        });
    });

    $('.guest_list_show').click(function(e) {
        e.preventDefault();
        let block_overlay = '<div class="overlay" id="overlay_delete">\n' +
            '\t\t<i class="fa fa-refresh fa-spin"></i>\n' +
            '\t</div>';
        let overlay_component = $(this).closest('.box-body');
        overlay_component.append(block_overlay);

        var self = $(this);
        $.ajax({
            method: 'GET',
            url: self.attr('href'),
            data: {},
            beforeSend: function() {
                $('#question_modal').modal('hide');
            },
        }).done((res) => {
            $('#guest-list-modal').find('.modal-body').html(res);
            $('#overlay_delete').remove();
            $('#guest-list-modal').modal();

        });
        return false;
    });

    $('.guest_list_show_email_hotels').click(function(e) {
        e.preventDefault();
        let block_overlay = '<div class="overlay" id="overlay_delete">\n' +
            '\t\t<i class="fa fa-refresh fa-spin"></i>\n' +
            '\t</div>';
        let overlay_component = $(this).closest('.box-body');
        overlay_component.append(block_overlay);

        var self = $(this);
        $.ajax({
            method: 'GET',
            url: self.attr('href'),
            data: {},
            beforeSend: function() {
                $('#question_modal').modal('hide');
            },
        }).done((res) => {
            $('#error_send').find('#title_modal_error').html('INFO');
            $('#error_send').find('.error_send_message').html(res);
            $('#overlay_delete').remove();
            $('#error_send').modal();

        });
        return false;
    });
</script>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript" src="{{ asset('js/jspdf.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/autotable.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/2.4.1/lodash.min.js"></script>
{{-- <script type="text/javascript" src="{{asset('js/google_map_for_tour.js')}}"></script>

    {{-- <script type="text/javascript" src="{{asset('js/pdf_export.js')}}"></script> --}}
<script type="text/javascript" src='{{ asset('js/supplier-search.js') }}'></script>
<script type="text/javascript" src='{{ asset('js/tour.js') }}'></script>
<script type="text/javascript" src='{{ asset('js/hide_elements.js') }}'></script>
<script type="text/javascript" src='{{ asset('js/roomlist.js') }}'></script>
<script type="text/javascript" src='{{ asset('js/attachments.js') }}'></script>
@endpush
@section('post_scripts')
<script type="text/javascript">
    jQuery(document).ready(function() {
        if ($(document).find('#description').length > 0) {
            if (CKEDITOR.instances['description']) {
                CKEDITOR.instances['description'].destroy(true);
            }
            CKEDITOR.replace('description', {
                height: '200px',
                title: false
            });
            CKEDITOR.config.toolbar = [
                ['Bold', 'Italic', 'Underline', 'SpellChecker', 'TextColor', 'BGColor', 'Undo', 'Redo',
                    'Link', 'Unlink', '-', 'Format'
                ],

            ];
        }
        if ($(document).find('#description-edit').length > 0) {
            //            if (CKEDITOR.instances['description-edit']) {
            //                CKEDITOR.instances['description-edit'].destroy(true);
            //            }
            CKEDITOR.replace('description-edit', {
                height: '200px',
                //                title: false
            });
            CKEDITOR.config.toolbar = [
                ['Bold', 'Italic', 'Underline', 'SpellChecker', 'TextColor', 'BGColor', 'Undo', 'Redo',
                    'Link', 'Unlink', '-', 'Format'
                ],

            ];
        }

    });
</script>
<script>
    function quotation_to_tour() {
        //confirm("Are you Sure");
        var quotation_url = $("#quotation_to_tour_href").attr("href");
        if ($("#check1").prop('checked')) {
            var quotation_url = $("#tour_to_quotation").attr("href");
        }
        console.log(quotation_url);
        $.ajax({
            type: "GET",
            url: quotation_url,



            success: function(result) {
                location.reload();
                console.log("working");
            },
            error: function(result) {
                console.log(result);
            }
        });
    }

    function tour_to_quotation() {
        var quotation_url = $("#tour_to_quotation").attr("href");
        if (!$("#check2").prop('checked')) {
            var quotation_url = $("#quotation_to_tour_href").attr("href");
        }

        console.log(quotation_url);
        $.ajax({
            type: "GET",
            url: quotation_url,



            success: function(result) {
                location.reload();
                console.log("working");
            },
            error: function(result) {
                console.log(result);
            }
        });
    }
</script>


<script src="{{ asset('js/comment.js') }}"></script>


<script>
    $(document).ready(function() {
        let permission = $('#permission').attr('data-permission');
        let classNameStatus = permission ? 'touredit-status' : '';
        const tour_id = $('#tour_date_id').attr('data-tour-id');

        // Invoices table now uses Bootstrap table with direct controller data

    })
</script>
<script>
	
	$(document).ready(function() {
   
    var activeTab = localStorage.getItem('activeTab');
		console.log(localStorage);
    // If there's no active tab in localStorage, set the default tab
    if (activeTab) {
      //  activeTab = 'frontsheet-tab'; // Set the default active tab
		
		//$( '#frontsheet-tab' ).removeClass( 'active' );
		$( 'li[ data-tab=' + activeTab + ']' ).addClass( 'active' );
		  $('#' + activeTab).addClass('active in');

    }else{
		$( 'li[ data-tab=frontsheet-tab]' ).addClass( 'active' );

		$("#frontsheet-tab").addClass("active in")

	}

    // Show the initially active tab
    // Click event to switch tabs
    $('.tab').on('click', function() {
	  var tabId = $(this).data('tab');

        // Save the active tab to localStorage
        localStorage.setItem('activeTab', tabId);
		console.log(localStorage);
    // $('.tab').removeClass('active');
       // $('.tab-pane').hide();
		
    });
});
	$(document).on('click','.select',function(){
		let selectId = $(".selectedOfice option:selected").val();
		
		 $.ajax({
            url: '/update-status/'+selectId,
            type: 'get',
            success: function () {   
				location.reload(true);
            }
            
        });
	});
	
    $(document).ready(function() {
        let permission = $('#permission').attr('data-permission');
        let classNameStatus = permission ? 'touredit-status' : '';
        // Transactions table now uses Bootstrap table with direct controller data

    })
	
</script>
<script>
    $(document).ready(function () {
        // Check if there is a stored scroll position
        var storedScrollPosition = localStorage.getItem('scrollPosition');

			if (storedScrollPosition) {
				// Scroll to the stored position
				$("div").scrollTop(parseInt(storedScrollPosition));
			}

        // Add a scroll event handler to store the scroll position
        $("div").scroll(function () {
		
            var scrollPosition = $(this).scrollTop();

            // Store the scroll position in localStorage
            localStorage.setItem('scrollPosition', scrollPosition);
        });
    });
</script>
<style>
    .toggle {
        position: relative;
        height: 42px;
        display: flex;
        align-items: center;
        box-sizing: border-box;
    }

    .toggle input[type="checkbox"] {
        position: absolute;
        left: 0;
        top: 0;
        z-index: 10;
        width: 100%;
        height: 100%;
        cursor: pointer;
        opacity: 0;
    }

    .toggle label {
        position: relative;
        display: flex;
        height: 100%;
        align-items: center;
        box-sizing: border-box;
    }

    .toggle label:before,
    .toggle label:after {
        font-size: 18px;
        font-weight: bold;
        font-family: arial;
        transition: 0.2s ease-in;
        box-sizing: border-box;
    }

    .toggle label:before {
        content: "Quotations";
        background: #fff;
        color: #000;
        height: 42px;
        width: 140px;
        display: inline-flex;
        align-items: center;
        padding-left: 15px;
        border-radius: 30px;
        border: 1px solid #eee;
        box-shadow: inset 140px 0px 0 0px #000;
        font-size: 10px
    }

    .toggle label:after {
        content: "GoAhead";
        position: absolute;
        left: 80px;
        line-height: 42px;
        top: 0;
        color: #FFF;
        font-size: 10px
    }

    .toggle input[type="checkbox"]:checked+label:before {
        color: #000;
        box-shadow: inset 0px 0px 0 0px #000;
    }

    .toggle input[type="checkbox"]:checked+label:after {
        color: #FFF;
    }

    .option-radio {
        box-shadow: 0px 0px 0px 1px #6d6d6d;
        font-size: 3em;
        width: 20px;
        height: 20px;
        margin-right: 7px;

        border: 4px solid #fff;
        background-clip: border-box;
        border-radius: 50%;
        appearance: none;
        transition: background-color 0.3s, box-shadow 0.3s;

    }

    .option-radio:checked {
        box-shadow: 0px 0px 0px 4px #eb0000;
        background-color: #ff5151;
    }

    .options-label {
        cursor: pointer;
        font-size: 20px;
        margin-left: 10px;
    }
	
</style>

@endsection
