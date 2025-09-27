@extends('scaffold-interface.layouts.app')
@section('title','Show')
@section('content')
    @include('layouts.title',
   ['title' => 'Hotel', 'sub_title' => 'Hotel Show',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Hotels', 'icon' => 'hotel', 'route' => route('hotel.index')],
   ['title' => 'Show', 'route' => null]]])
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div style="margin-bottom: 10px;">
                    <a href="javascript:history.back()">
                        <button class='btn btn-primary'>{!!trans('main.Back')!!}</button>
                    </a>
                    <a href="{!! route('hotel.edit', $hotel->id) !!}">
                        <button class='btn btn-warning'>{!!trans('main.Edit')!!}</button>
                        @if (isset($tab)) {{ $tab }} @endif
                    </a>
                </div>
                <div id="fixed-scroll" class="nav-tabs-custom">
                    <ul class="nav nav-tabs" id="fixed-scroll" role='tablist'>
                        <li role='presentation' class="active"><a href="#info-tab" aria-controls='info-tab' role='tab' data-toggle='tab'>{!!trans('main.Info')!!}</a></li>
                        <li role='presentation'><a href="#contacts-tab" aria-controls='contacts-tab' role='tab' data-toggle='tab'>{!!trans('main.Contacts')!!}</a></li>
                        <li role='presentation'><a href="#history-tab" aria-controls='history-tab' role='tab' data-toggle='tab'>{!!trans('main.History')!!}</a></li>
                        <li role='presentation'><a href="#agreement-tab" aria-controls='agreement-tab' role='tab' data-toggle='tab' id="agreement_tab" >{!!trans('main.Agreements')!!}</a></li>

                        <li role='presentation'><a href="#kontingent-tab" aria-controls='kontingent-tab' role='tab' data-toggle='tab' id="kontingent_tab" >{!!trans('main.Allotment')!!}</a></li>
                        <li role='presentation'><a href="#menu-tab" aria-controls='menu-tab' role='tab' data-toggle='tab'>{!!trans('main.Menu')!!}</a></li>
                        <li role='presentation'><a href="#season-tab" aria-controls='season-tab' role='tab' data-toggle='tab' id="season_tab">{!!trans('main.Seasonprice')!!}</a></li>
						<li role='presentation' class="tab" data-tab="invoices-tab"><a href="#invoices-tab" aria-controls='invoices-tab' role='tab'
                                data-toggle='tab' id="invoices_tab" >{!! trans('Invoices') !!}</a></li>
                    </ul>
                </div>
                <div class="tab-content">
                    <div class="tab-pane fade in active" role='tabpanel' id='info-tab'>
{{--                    <div class="block-table-list" style="grid-template-columns: 2fr 2fr 0fr;" >--}}
                        <div>
                            <table class='table table-bordered' style="width:50%; float:left">
                                <tbody>
                                <tr>
                                    <td>
                                        <b><i>{!!trans('main.Name')!!} : </i></b>
                                    </td>
                                    <td class="info_td_show">{!!$hotel->name!!}</td>
                                </tr>
                                <tr>
                                    <td>
                                        <b><i>{!!trans('main.AddressFirst')!!} : </i></b>
                                    </td>
                                    <td class="info_td_show">{!!$hotel->address_first!!}</td>
                                </tr>
                                <tr>
                                    <td>
                                        <b><i>{!!trans('main.AddressSecond')!!} : </i></b>
                                    </td>
                                    <td class="info_td_show">{!!$hotel->address_second!!}</td>
                                </tr>
                                <tr>
                                    <td>
                                        <b><i>{!!trans('main.Code')!!} : </i></b>
                                    </td>
                                    <td class="info_td_show">{!!$hotel->code!!}</td>
                                </tr>
                                <tr>
                                    <td>
                                        <b><i>{!!trans('main.Country')!!} : </i></b>
                                    </td>
                                    <td class="info_td_show">{!! \App\Helper\CitiesHelper::getCountryById($hotel->country)['name']??""!!}</td>
                                </tr>
                                <tr>
                                    <td>
                                        <b><i>{!!trans('main.City')!!} : </i></b>
                                    </td>
									@if(!empty($hotel->city))
                                    <td class="info_td_show">{!! \App\Helper\CitiesHelper::getCityById($hotel->city)['name']??""!!}</td>
									@else
									<td class="info_td_show"></td>
									@endif
                                </tr>
                                <tr>
                                    <td>
                                        <b><i>{!!trans('main.WorkPhone')!!} : </i></b>
                                    </td>
                                    <td class="info_td_show">{!!$hotel->work_phone??""!!}</td>
                                </tr>

                                <tr>
                                    <td>
                                        <b><i>{!!trans('main.WorkFax')!!} : </i></b>
                                    </td>
                                    <td class="info_td_show">{!!$hotel->work_fax??""!!}</td>
                                </tr>
                                <tr>
                                    <td>
                                        <b><i>{!!trans('main.WorkEmail')!!} : </i></b>
                                    </td>
                                    <td class="info_td_show">{!!$hotel->work_email!!}</td>
                                </tr>
                                </tbody>
                            </table>
                            <table class='table table-bordered' style="width:50%; float:left">
                                <tbody>
                                <tr>
                                    <td>
                                        <b><i>{!!trans('main.ContactName')!!} : </i></b>
                                    </td>
                                    <td class="info_td_show">{!!$hotel->contact_name!!}</td>
                                </tr>
                                <tr>
                                    <td>
                                        <b><i>{!!trans('main.ContactPhone')!!} : </i></b>
                                    </td>
                                    <td class="info_td_show">{!!$hotel->contact_phone!!}</td>
                                </tr>
                                <tr>
                                    <td>
                                        <b><i>{!!trans('main.ContactEmail')!!} : </i></b>
                                    </td>
                                    <td class="info_td_show">{!!$hotel->contact_email!!}</td>
                                </tr>
                                <tr>
                                    <td>
                                        <b><i>{!!trans('main.Comments')!!} : </i></b>
                                    </td>
                                    <td class="info_td_show">{!!$hotel->comments!!}</td>
                                </tr>
                                <tr>
                                    <td>
                                        <b><i>{!!trans('main.IntComments')!!} : </i></b>
                                    </td>
                                    <td class="info_td_show">{!!$hotel->int_comments!!}</td>
                                </tr>
                                <tr>
                                    <td>
                                        <b><i>{!!trans('main.Criterias')!!} : </i></b>
                                    </td>
<?php                                    
    $empty = 0;
?>
                                    @forelse($criterias as $criteria)
                                        @forelse($hotel->criterias as $item)
                                            @if($criteria->id == $item->criteria_id)
                                                <td class="info_td_show criteria_block" style="width:100%">{!!$criteria->name!!}</td>
<?php                                    
    $empty = 1;
?>
                                                
{{--                                                <td class="info_td_show">{!!$criteria->name!!}</td>--}}
                                            @endif
                                        @empty
{{--                                            <td class="info_td_show"></td>--}}
                                        @endforelse
                                    @empty
{{--                                        <td class="info_td_show"></td> --}}
                                    @endforelse
                                    @if($empty == 0)
                                        <td class="info_td_show"></td>
                                    @endif
                                </tr>
                                <tr>
                                    <td>
                                        <b><i>{!!trans('main.Rate')!!} : </i></b>
                                    </td>
                                    <td class="info_td_show">{!!$hotel->rate_name!!}</td>
                                </tr>
                                <tr>
                                    <td>
                                        <b><i>{!!trans('main.Website')!!} : </i></b>
                                    </td>
                                    <td class="info_td_show">{!!$hotel->website!!}</td>
                                </tr>
                                <tr>
                                    <td>
                                        <b><i>{!!trans('main.CityTax')!!} : </i></b>
                                    </td>
                                    <td class="info_td_show">{!!$hotel->city_tax!!}</td>
                                </tr>

                                <tr>
                                    <td>
                                        <b><i>{!!trans('main.Note')!!} : </i></b>
                                    </td>
                                    <td class="info_td_show">{!!$hotel->note!!}</td>
                                </tr>

                                </tbody>
                            </table><!--
                            <table class="table table-bordered">
                            <tbody>
                            <tr>
                                <td>
                                    <b><i>Prices Room Type : </i></b>
                                </td>
                                @foreach($hotel->prices_room_type as $item)

                            <td style="display: block">{!!$item->room_types->code . ' - ' . $item->price!!}</td>
                                @endforeach
                                </tr>
                                </tbody>
                            </table>-->
                        </div>
                        <div class="clearfix"></div>
                        @component('component.files', ['files' => $files])@endcomponent
                        <span id="showPreviewBlock" data-info="{{ true }}"></span>
                        <div class="box box-success" style="position: relative; left: 0px; top: 0px;">
                            <div class="box-header ui-sortable-handle" style="cursor: move;">
                                <i class="fa fa-comments-o"></i>

                                <h3 class="box-title">{!!trans('main.Comments')!!}</h3>
                            </div>
                            <div class="box-body">
                                <div class="slimScrollDiv" style="position: relative; overflow-y: scroll;  width: auto;">
                                    <div class="box-body box chat" id="chat-box" style="width: auto; height: auto;">
                                        <div id="show_comments"></div>
                                    </div>
                                    <div class="slimScrollRail" style="width: 7px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; background: rgb(51, 51, 51) none repeat scroll 0% 0%; opacity: 0.2; z-index: 90; right: 1px;"></div>
                                </div>
                            </div>
                            <!-- /.chat -->
                            <div class="box-footer">
                                <form method='POST' action='{{route('comment.store')}}' enctype="multipart/form-data" id="form_comment">
                                    <div class="input-group" style="width: 100%">
                                        <span id="author_name" class="input-group-addon">
                                            <span id="name"></span>
                                            <a href="#" id="reply_close"><i class="fa fa-close"></i></a>
                                        </span>
                                        <textarea class="form-control" id="content" name="content" placeholder="Ctrl + Enter to post comment"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>{!!trans('main.Files')!!}</label>
                                        @component('component.file_upload_field')@endcomponent
                                    </div>
                                    <input type="text" id="parent_comment" hidden name="parent" value="{{ null }}">
                                    <input type="text" id="default_reference_id" hidden name="reference_id" value="{{ $hotel->id }}">
                                    <input type="text" id="default_reference_type" hidden name="reference_type" value="{{ \App\Comment::$services['hotel']}}">

                                    <button type="submit" class="btn btn-success pull-right" id="btn_send_comment" style="margin-top: 5px;">{!!trans('main.Send')!!}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" role='tabpanel' id='contacts-tab'>
                        <div>
                            @if($contacts->count())
                            <table class='table table-bordered' style="width:50%; float:left">
                                <tbody>
                                    <tr>
                                        <td><b>{!!trans('main.FullName')!!}</b></td>
                                        <td><b>{!!trans('main.MobilePhone')!!}</b></td>
                                        <td><b>{!!trans('main.WorkPhone')!!}</b></td>
                                        <td><b>{!!trans('main.Email')!!}</b></td>
                                    </tr>
                                    @foreach($contacts as $contact)
                                        <tr>
                                            <td>{{ $contact->full_name }}</td>
                                            <td>{!!$contact->mobile_phone!!}</td>
                                            <td>{!!$contact->work_phone!!}</td>
                                            <td>{!!$contact->email!!}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @else
                                <h2>{!!trans('main.Hoteldonthavecontacts')!!}!</h2>
                            @endif
                        </div>
                    </div>
                    <div class="tab-pane fade" role='tabpanel' id='history-tab'>
                        <div id='history-container'></div>
                    </div>

                    <div class="tab-pane fade" role='tabpanel' id='agreement-tab'>
                        <br>
                        <div class="box box-success">
                            <div class="box-header ui-sortable-handle" style="cursor: move;">
                                <i class="fa fa-handshake-o"></i>
                                <h3 class="box-title">{!!trans('main.Agreements')!!}</h3>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        @if(Auth::user()->can('create_agreements'))
                                        <a href="{{route('create_agreements', ['id' => $hotel->id])}}">
                                            <button type="button" class="btn btn-block btn-success btn-flat">{!!trans('main.AddAgreement')!!}</button>
                                        </a>
                                        @endif
                                    </div>
                                </div>
                                <br>
                                <table class="table table-bordered finder-disable ">
                                    <thead>
                                    <tr role="row">
                                        <th>{!!trans('main.StartDate')!!}</th>
                                        <th>{!!trans('main.EndDate')!!}</th>
                                        <th>{!!trans('main.Hotel')!!}</th>
                                        <th>{!!trans('main.Name')!!}</th>
                                        <th>{!!trans('main.Rooms')!!}</th>
                                        <th>{!!trans('main.Description')!!}</th>
                                        <th>{!!trans('main.Actions')!!}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach( $hotel->agreements as $agreement)

                                        <tr>
                                            <td>
                                                {{ Carbon\Carbon::parse($agreement->start_date)->format('d-m-Y') }}
                                            </td>
                                            <td>
                                                {{ Carbon\Carbon::parse($agreement->end_date)->format('d-m-Y') }}
                                            </td>
                                            <td>
                                                {{$hotel->name}}
                                            </td>
                                            <td>
                                                {{$agreement->name}}

                                            </td>
                                            <td>
                                                @foreach($agreement->agreements_room_types as $item)
                                                    <p> {{$item->count}} - {{$agreement->getRoom($item->room_type_id)->name}} </p>
                                                @endforeach
                                            </td>
                                            <td style="width:50em;">
                                                <div id="desc_agreement" > {{$agreement->description}} </div>
                                            </td>
                                            <td align="center" style="min-width: 100px;" >
                                                @if(Auth::user()->can('edit_agreements'))
                                                <a href="{{route('edit_agreements', ['id' => $agreement->id,'hotel_id' => $hotel->id])}}" type="button" class="btn btn-primary btn-sm" ><i class="fa fa-pencil-square-o"></i></a>
                                                @endif
                                                @if(Auth::user()->can('delete_agreements'))
                                                    <a href="#" onclick="checkAgreement( {{$agreement->id}},{{$hotel->id}} );" type="button" class="btn btn-danger btn-sm" ><i class="fa fa-trash-o"></i></a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                    {{-- @endforeach --}}
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" role='tabpanel' id='kontingent-tab'>
                        <br>
                        <div class="container">

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">

                                        <label for="">&nbsp;&nbsp;{!!trans('main.StartMonth')!!}</label>
                                        <div class="input-group date margin">

                                            <div class="input-group-addon date_calendar" >
                                                <i class="fa fa-calendar" ></i>
                                            </div>
                                            <input type="text" value="" name="start_date" id="start_date" class="form-control pull-right datepicker"  >
                                            <span class="input-group-btn">
                                                 <button type="button" class="btn btn-primary btn-flat" id="showMonth">{!!trans('main.Show')!!}</button>
                                            </span>
                                        </div>

                                    </div>
                                </div>

                                <div class="col-sm-6" style="margin-top: 34px;">

                                      <span id="help" class="btn btn-box-tool" style="margin-left: 105%;"><i class="fa fa-question-circle" aria-hidden="true" style="font-size: 25px;"></i>
                                          @include('legend.kontingent_legend')
                                    </span>

                                </div>


                            </div>


                            <div class="row">
                                <div class="col-sm-12">
                                    <table class="calendar" id="calendar1" align="left"
                                           style="table-layout: fixed;height: 100%;border-collapse:collapse;">
                                        <thead id="calendar_head1">
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <table class="calendar" id="calendar2" align="left"
                                           style="table-layout: fixed;height: 100%;border-collapse:collapse;">
                                        <thead id="calendar_head2">
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <table class="calendar" id="calendar3" align="left"
                                           style="table-layout: fixed;height: 100%;border-collapse:collapse;">
                                        <thead id="calendar_head3">
                                        </thead>
                                    </table>
                                </div>
                            </div>


                        </div>
                        <br>
                    </div>

                    <div class="tab-pane fade" role='tabpanel' id='menu-tab'>
                        <br>
                        <div class="box box-success">
                            <div class="box-header ui-sortable-handle" style="cursor: move;">
                                <h3 class="box-title">{!!trans('main.Menu')!!}</h3>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        @if(Auth::user()->can('menu.create'))
                                        <a href="{{route('menu.create', ['type' => 'hotel', 'id' => $hotel->id])}}">
                                            <button type="button" class="btn btn-block btn-success btn-flat">{!!trans('main.AddMenu')!!}</button>
                                        </a>
                                        @endif
                                    </div>
                                </div>
                                <br>
                                <table class="table table-bordered finder-disable" >
                                    <thead>
                                    <tr role="row">
                                        <th >{!!trans('main.Name')!!}</th>
                                        <th>{!!trans('main.Price')!!}</th>
                                        <th>{!!trans('main.Description')!!}</th>
                                        <th>{!!trans('main.Actions')!!}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($hotel->menus as $menu)
                                        <tr>
                                            <td><a href="{{route('menu.show', ['menu' => $menu->id])}}">{{$menu->name}}</a></td>
                                            <td>{{$menu->price}}</td>
                                            <td>{!! $menu->description !!}</td>
                                            <td style="width: 100px;">
                                                @if(Auth::user()->can('menu.edit'))
                                                <a href="{{route('menu.edit', ['id' => $menu->id])}}" class="btn btn-primary btn-sm edit-button" data-link="{{route('menu.edit', ['id' => $menu->id])}}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                                @endif
                                                @if(Auth::user()->can('menu.destroy_menu'))
                                                    <a  data-toggle="modal" data-target="#myModal" class="btn btn-danger btn-sm delete" data-link="{{route('menu.delete', ['id' => $menu->id], false)}}"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                                @endif
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" role='tabpanel' id='season-tab'>
                        <br>
                        <div class="box box-success">
                            <div class="box-header ui-sortable-handle" style="cursor: move;">
                                <i class="fa fa-snowflake-o"></i>
                                <h3 class="box-title">{!!trans('main.Seasonprice')!!}</h3>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        @if(Auth::user()->can('create_season'))
                                        <a href="{{route('create_season', ['id' => $hotel->id])}}">
                                            <button type="button" class="btn btn-block btn-success btn-flat">{!!trans('main.AddSeason')!!}</button>
                                        </a>
                                        @endif
                                    </div>
                                </div>
                                <br>
                                <table class="table table-bordered finder-disable"  >
                                    <thead>
                                    <tr role="row">
                                        <th >{!!trans('main.StartDate')!!}</th>
                                        <th>{!!trans('main.EndDate')!!}</th>
                                        <th>{!!trans('main.Hotel')!!}</th>
                                        <th>{!!trans('main.Name')!!}</th>
                                        <th>{!!trans('main.Type')!!}</th>
                                        <th>{!!trans('main.Prices')!!}</th>
                                        <th>{!!trans('main.Description')!!}</th>
                                        <th>{!!trans('main.Actions')!!}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach( $hotel->seasons as $agreement)

                                        <tr>
                                            <td>
                                                {{ Carbon\Carbon::parse($agreement->start_date)->format('d.m') }}
                                            </td>
                                            <td>
                                                {{ Carbon\Carbon::parse($agreement->end_date)->format('d.m') }}
                                            </td>
                                            <td>
                                                {{$hotel->name}}
                                            </td>
                                            <td>
                                                {{$agreement->name}}
                                            </td>
                                            <td>
                                                {{ !empty($agreement->getType($agreement->type)->name) ? $agreement->getType($agreement->type)->name : '' }}
                                            </td>
                                            <td>
                                                @foreach($agreement->seasons_room_types as $item)
                                                    {{ $agreement->getRoom($item->room_type_id)->name }} - {{ $item->price }} <br>
                                                @endforeach
                                            </td>
                                            <td style="width:50em;">
                                                <div id="desc_season" > {{$agreement->description}} </div>
                                            </td>
                                            <td align="center" style="min-width: 100px;" >
                                                @if(Auth::user()->can('edit_season'))
                                                <a href="{{route('edit_season', ['id' => $agreement->id,'hotel_id' => $hotel->id])}}" type="button" class="btn btn-primary btn-sm" ><i class="fa fa-pencil-square-o"></i></a>
                                                @endif
                                                @if(Auth::user()->can('delete_season'))
                                                    <a href="#" onclick="checkAgreement( {{$agreement->id}},{{$hotel->id}},1);" type="button" class="btn btn-danger btn-sm" ><i class="fa fa-trash-o"></i></a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                    {{-- @endforeach --}}
                                </table>
                            </div>
                        </div>

                    </div>
						<div role="tabpanel" class="tab-pane fade in" id="invoices-tab">
					<div id="tour_create" style="margin-bottom : 20px;">
                    {!! \App\Helper\PermissionHelper::getCreateButton(route('invoices.create'), \App\Invoices::class) !!}
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" id="hotelInvoiceSearchInput" class="form-control" placeholder="Search invoices..." onkeyup="filterHotelInvoiceTable()">
                        </div>
                    </div>
                    <div class="col-md-6 text-right">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-primary" onclick="exportHotelInvoicesToCSV()">Export CSV</button>
                            <button type="button" class="btn btn-success" onclick="exportHotelInvoicesToExcel()">Export Excel</button>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="inovices-table" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Invoice No</th>
                                <th>Due Date</th>
                                <th>Received Date</th>
                                <th>Tour</th>
                                <th>Service</th>
                                <th>Office Name</th>
                                <th>Total Price</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($invoices) && $invoices->count() > 0)
                                @foreach($invoices as $invoice)
                                    <tr>
                                        <td>{{ $invoice->id }}</td>
                                        <td>{{ $invoice->invoice_no ?? 'N/A' }}</td>
                                        <td>{{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('Y-m-d') : 'N/A' }}</td>
                                        <td>{{ $invoice->received_date ? \Carbon\Carbon::parse($invoice->received_date)->format('Y-m-d') : 'N/A' }}</td>
                                        <td>{{ $invoice->tour_name ?? 'N/A' }}</td>
                                        <td>{{ $invoice->service_name ?? 'N/A' }}</td>
                                        <td>{{ $invoice->office_name ?? 'N/A' }}</td>
                                        <td>{{ number_format($invoice->total_amount ?? 0, 2) }}</td>
                                        <td>
                                            <span class="badge badge-{{ $invoice->status == 'paid' ? 'success' : ($invoice->status == 'pending' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($invoice->status ?? 'pending') }}
                                            </span>
                                        </td>
                                        <td>
                                            @include('component.action_buttons', [
                                                'routePrefix' => 'invoices',
                                                'item' => $invoice,
                                                'showEdit' => true,
                                                'showDelete' => true,
                                                'showView' => true
                                            ])
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="10" class="text-center">No invoices found for this hotel</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                @if(isset($invoices) && method_exists($invoices, 'links'))
                    <div class="row">
                        <div class="col-md-12">
                            {{ $invoices->links() }}
                        </div>
                    </div>
                @endif



                </div>
                </div>
            </div>
        </div>
    </section>
    <div id="modalCreateKontingent" class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="modalCreateTourLabel" style="padding-left: 17px;padding-right: 17px;z-index:9999;">
        <div class="modal-dialog modal-lg" style="width: 90%;">
            <div class="modal-content" style="overflow: hidden;">
                <div class="modal-header">
                    <a class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">x</span>
                    </a>
                    <h4 id="modalCreateKontigentLabel" class="modal-title"></h4>
                    <input type="hidden" id="modalCreateKontigentQuota" value="">
                    <input type="hidden" id="modalCreateKontigentValue" value="">
                    <div class="container-fluid; hide" style="margin: 3px;" >
                        <div class="row">
                            <div class="col-xs-1" style="background-color: #f4f4f4;width: 8em;text-align: center;">{!!trans('main.Rooms')!!}</div>
                            <div class="col-xs-5" id="hotel_rooms">2 US 3 AS 4 SIN </div>
                            <div class="col-xs-1" style="background-color: #f4f4f4;width: 8em;text-align: center;">Pax</div>
                            <div class="col-xs-5" id="hotel_pax">23</div>
                        </div>
                    </div>
                </div>

                <div class="modal-body" style="overflow: hidden" >

                    <input type='hidden' name='_token' value='{{Session::token()}}'>
                    <input type='hidden' name='modal_create_tour' value="1">



                    <div class="box box-solid" >
                        <div class="box-header with-border">

                            <div class="box-body" style="overflow-y: scroll; height:250px;">
                                <table class="table table-striped table-bordered table-hover package-service-table" style="background:#fff">
                                    <colgroup>
                                        <col style="width: auto">
                                        <col style="width: auto">
                                        <col style="width: auto;">
                                        <col style="width: auto;">
                                        <col style="width: auto;">
                                        <col style="width: auto;">
                                        <col style="width: auto;">
                                        <col style="width: auto;">
                                        <col style="width: auto;">
                                        <col style="width: auto">
                                        <col style="width: auto;">
                                        <col style="width: 100px;">
                                    </colgroup>
                                    <thead>
                                    <tr>
                                        <th>{!!trans('main.Tour')!!}</th>
                                        <th>{!!trans('main.FromTime')!!}</th>
                                        <th style="width: 15%; min-width: 100px;">{!!trans('main.Name')!!}</th>
                                        <th style="min-width: 150px">{!!trans('main.Status')!!}</th>
                                        <th>{!!trans('main.Paid')!!}</th>
                                        <th>{!!trans('main.Rooms')!!}</th>
                                        <th>Pax</th>
                                        <th>{!!trans('main.Address')!!}</th>
                                        <th>{!!trans('main.Email')!!}</th>
                                        <th>{!!trans('main.Phone')!!}</th>
                                        <th>{!!trans('main.Description')!!}</th>

                                        <!-- <th style="width: 120px">Rooms Hotel</th>-->
                                        <th style="width: 100px; min-width: 100px">{!!trans('main.Actions')!!}</th>
                                    </tr>
                                    </thead>
                                    <tbody class="ui-sortable" id="from_div">
                                    </tbody>
                                </table>

                            </div>
                        </div>

                    </div>





                    <div class="box box-solid" >
                        <div class="box-header with-border">

                            <div class="box-body" style="overflow-y: scroll; height:250px;" >
                                <table class="table table-striped table-bordered table-hover package-service-table" style="background:#fff" >
                                    <colgroup>
                                        <col style="width: auto">
                                        <col style="width: auto">
                                        <col style="width: auto;">
                                        <col style="width: auto;">
                                        <col style="width: auto;">
                                        <col style="width: auto;">
                                        <col style="width: auto;">
                                        <col style="width: auto;">
                                        <col style="width: auto;">
                                        <col style="width: auto;">
                                        <col style="width: auto;">
                                        <col style="width: 130px;">
                                    </colgroup>
                                    <thead>
                                    <tr>
                                        <th>{!!trans('main.Tour')!!}</th>
                                        <th>{!!trans('main.FromTime')!!}</th>
                                        <th style="width: 15%; min-width: 100px;">{!!trans('main.Name')!!}</th>
                                        <th style="min-width: 150px">{!!trans('main.Status')!!}</th>
                                        <th>{!!trans('main.Paid')!!}</th>
                                        <th>{!!trans('main.Rooms')!!}</th>
                                        <th>Pax</th>
                                        <th>{!!trans('main.Address')!!}</th>
                                        <th>{!!trans('main.Email')!!}</th>
                                        <th>{!!trans('main.Phone')!!}</th>
                                        <th>{!!trans('main.Description')!!}</th>

                                        <!-- <th style="width: 120px">Rooms Hotel</th>-->
                                        <th style="width: 130px; min-width: 130px">{!!trans('main.Actions')!!}</th>
                                    </tr>
                                    </thead>
                                    <tbody class="ui-sortable" id="to_div">
                                    </tbody>
                                </table>

                            </div>
                        </div>

                    </div>



                </div>





                <div class="modal-footer">
                    <a href="close" class='btn btn-default' data-dismiss="modal">{!!trans('main.Close')!!}</a>
                    <!--<button class='btn btn-primary' type="button" onclick="saveRemoved();" >Replace</button>-->
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="warnReplace" tabindex="-1" role="dialog" aria-labelledby="myModalLabelDelete" >
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title" id="myModalLabel">{!!trans('main.Warning')!!}!!</h4></div>
                <div class="modal-body">
                    <div class="modal-body">{!!trans('main.Themaximumnumberofroomsavailable')!!}</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Ok</button>
                </div></div></div>
    </div>

    <div class="modal fade" id="deleteAgreement" tabindex="-1" role="dialog" aria-labelledby="myModalLabelDelete" >
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title" id="myModalLabel">{!!trans('main.Warning')!!}!!</h4></div>
                <div class="modal-body">
                    <div class="modal-body">{!!trans('main.WouldyouliketoremoveThis')!!}?</div></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{!!trans('main.Close')!!}</button>
                    <button type="button" class="destroy btn btn-primary" onclick="deleteAgreement();" >{!!trans('main.Agree')!!}</button>
                    <input type="hidden" id="id_field" value="" >
                    <input type="hidden" id="hotel_id_field" value="" >
                </div></div></div>
    </div>

    <div class="modal fade" id="replacePackage" tabindex="-1" role="dialog" aria-labelledby="myModalLabelDelete" >
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title" id="myModalLabel">{!!trans('main.Warning')!!}!!</h4></div>
                <div class="modal-body">
                    <div class="modal-body">{!!trans('main.WouldyouliketoreplaceThis')!!}?</div></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{!!trans('main.Close')!!}</button>
                    <button type="button" class="destroy btn btn-primary" onclick="addFromAgreement();" >{!!trans('main.Agree')!!}</button>
                    <input type="hidden" id="id_package" value="0" >
                </div></div></div>
    </div>

    <span id="services_name" data-service-name='Hotel' data-history-route="{{route('services_history', ['id' => $hotel->id])}}"></span>
    <style>
        .popover .close {
            position: absolute;
            top: 8px;
            right: 10px;
        }
        .popover-title {
            padding-right: 30px;
        }

        .datepicker{z-index:1151 !important;}

    </style>
@endsection

@section('post_scripts')
    <script src="{{ asset('js/comment.js') }}"></script>
    <script src="{{ asset('js/bootstrap-tables.js') }}"></script>
    <script>

        var TodayDate = new Date();
        var removed = [];
        var show = true;
        var current_date = '';

        Date.prototype.getDayOfYear = function () {
            var fd = new Date(this.getFullYear(), 0, 0);
            var sd = new Date(this.getFullYear(), this.getMonth(), this.getDate());
            return Math.ceil((sd - fd) / 86400000);
        };

        Date.prototype.getLastDayOfMonth = function () {
            var y = this.getFullYear();
            var m = this.getMonth();
            return new Date(y, m + 1, 0);
        };

        Date.locale = {
            en: {
                month_names: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                month_names_short: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
            }
        };

        Date.prototype.getMonthName = function (lang) {
            lang = lang && (lang in Date.locale) ? lang : 'en';
            return Date.locale[lang].month_names[this.getMonth()];
        };

        Date.prototype.getMonthNameShort = function (lang) {
            lang = lang && (lang in Date.locale) ? lang : 'en';
            return Date.locale[lang].month_names_short[this.getMonth()];
        };

        Date.isLeapYear = function (year) {
            return (((year % 4 === 0) && (year % 100 !== 0)) || (year % 400 === 0));
        };

        Date.getDaysInMonth = function (year, month) {
            return [31, (Date.isLeapYear(year) ? 29 : 28), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31][month];
        };

        Date.prototype.isLeapYear = function () {
            return Date.isLeapYear(this.getFullYear());
        };

        Date.prototype.getDaysInMonth = function () {
            return Date.getDaysInMonth(this.getFullYear(), this.getMonth());
        };

        Date.prototype.addMonths = function (value) {
            var n = this.getDate();
            this.setDate(1);
            this.setMonth(this.getMonth() + value);
            this.setDate(Math.min(n, this.getDaysInMonth()));
            return this;
        };


        function dateDifference(d1, d2) {
            var fd = new Date(d1.getFullYear(), d1.getMonth(), d1.getDate());
            var sd = new Date(d2.getFullYear(), d2.getMonth(), d2.getDate());
            return Math.ceil((sd - fd) / 86400000);
        }

        function setModalMaxHeight(element) {
            this.$element     = $(element);
            this.$content     = this.$element.find('.modal-content');
            var borderWidth   = this.$content.outerHeight() - this.$content.innerHeight();
            var dialogMargin  = $(window).width() < 768 ? 20 : 60;
            var contentHeight = $(window).height() - (dialogMargin + borderWidth);
            var headerHeight  = this.$element.find('.modal-header').outerHeight() || 0;
            var footerHeight  = this.$element.find('.modal-footer').outerHeight() || 0;
            var maxHeight     = contentHeight - (headerHeight + footerHeight);

            this.$content.css({
                'overflow': 'hidden'
            });

            this.$element
                .find('.modal-body').css({
                'max-height': maxHeight,
                'overflow-y': 'auto'
            });
        }

        $('#modalCreateKontingent').on('show.bs.modal', function() {
            $(this).show();
            setModalMaxHeight(this);
        });

        $('#warnReplace').on('hidden.bs.modal', function () {
            $('#modalCreateKontingent').modal('show');
        });

        $('#modalCreateKontingent').on('hidden.bs.modal', function () {
            restore_reload();
        });


        $(window).resize(function() {
            if ($('.modal.in').length != 0) {
                setModalMaxHeight($('.modal.in'));
            }
        });

        function setMonths() {
            if(!show) return;
            var date = $("#start_date").datepicker('getDate');
            var startDate = new Date(date.getFullYear(), (date.getMonth()), 1);
            show = false;
            $('#calendar_head1').html('');
            $('#calendar_head2').html('');
            $('#calendar_head3').html('');
            $('#line1').html('');
            $('#line2').html('');
            $('#line3').html('');
            genMonths(startDate, 1);
        }

        function genRow(text, startDate, endDate, arr, mode , line, month) {

            var days = dateDifference(startDate, endDate) + 1;
            if (days > 31) days--;
            var backcolor = '';
            var backtext = '';
            var num = '';
            var popover = '';
            var id = '';
            var data = '';

            if (mode) {
                text = '<span style="font-size: 15px;"><b>' + text + '</b></span>';
                backtext = '#dbdee1';
                backcolor = '#dbdee1';
            } else {
                backtext = '#dbdee1';
                backcolor = '#ffffff';
                text = '<span style="font-size: 14px;">' + text + '</span>';
            }

            var numbers2 = '<td style="background-color: ' + backtext + ';border: 1px solid #9ea5a8;text-align: center;min-width: 200px;min-height: 20px;">' + text + '</td>';
            for (var ii = 0; ii < days; ii++) {

                num = '';
                id = '';
                popover = '';
                data = '';




                if (mode) {
                    num = (ii + 1);
                    //id = "day_" + num;
                } else {
                    if (arr[ii]) {
                        num = arr[ii];

                        if (line == '1') {

                            startDate.getMonthName() + " " + startDate.getFullYear();
                            popover = ' data-title="Tours at '+(ii+1)+' '+ startDate.getMonthName()+' '+ startDate.getFullYear()+'" class="my-popover" ';

                            data = ' data-pop="' +(ii + 1)+'"';


                        }

                        if (line == '4') {
                            id = "quota_"+month+"_" +(ii + 1);
                        }


                    }

                }
                numbers2 += '<td id="' + id + '"'+ data +'  style="background-color: ' + backcolor + '; border: 1px solid #9ea5a8;text-align: center;min-width: 30px;min-height: 20px;"' + popover + ' >' + num + '</td>';
            }

            return numbers2;

        }



        function genMonths(startDate, num) {

            var endDate = startDate;
            endDate.setDate(1);
            endDate.setMonth(endDate.getMonth() + 1);
            endDate.setDate(endDate.getDate() - 1);
            var startDate = new Date(endDate.getFullYear(), endDate.getMonth(), 1);
            var days = dateDifference(startDate, endDate) + 1;
            if(days>31) days--;
            ///ajax get by date ;

            $.ajax({
                type: "GET",
                url: '{{ route('kontingent_agreements') }}',
                data: {hotel_id: $('#default_reference_id').val(), days : days, startDate: moment(startDate).format('Y-M-D'), endDate: moment(endDate).format('Y-M-D'), num : num},

                success: function (data) {

                    //set data array
//                    console.log(data);

                    $('#calendar_head' + num).append("<tr><td><br></td></tr>");
                    $('#calendar_head' + num).append("<tr>" + genRow(startDate.getMonthName() + " " + startDate.getFullYear(), startDate, endDate,data,true,0) + "</tr>");
                    $('#calendar_head' + num).append("<tr>" + genRow('Contractual number of rooms', startDate, endDate,data[0],null,1) + "</tr>");
                    $('#calendar_head' + num).append("<tr>" + genRow('Number of rooms already used', startDate, endDate,data[1],null,2) + "</tr>");
                    $('#calendar_head' + num).append("<tr>" + genRow('Allotment Reserved', startDate, endDate,data[2],null,3) + "</tr>");
                    $('#calendar_head' + num).append("<tr>" + genRow('Available quota', startDate, endDate,data[3],null,4,moment(endDate).format('MM')) + "</tr>");
                    $('#calendar_head' + num).append("<tr>" + genRow('Current booking status %', startDate, endDate,data[4],null,5) + "</tr>");
                    /*
                   if(data[4][1] != '0') {
                        $('#calendar' + num).after(
                            "<div id='line" + num + "'><table class='calendar' align='left' style='table-layout: fixed;height: 100%;border-collapse:collapse'>" +
                            "    <thead>" +
                            "    <tr>" +
                            "        <td style='background-color: #dbdee1;border-left: 1px solid #9ea5a8;border-right: 1px solid #9ea5a8;border-bottom: 1px solid #9ea5a8;text-align: center;min-width: 200px;min-height: 20px;'>" +
                            "            <span style='font-size: 15px;'>" + data[4][0] + "</span></td>" +
                            "        <td width='120' " +
                            "            style='border-bottom: 1px solid #9ea5a8;border-right: 1px solid #9ea5a8;text-align: center;min-height: 135px;'>" +
                            +data[4][1] + "/" + data[4][2] + " " + data[4][3] + "%" + "</td>" +
                            "<tr>" +
                            "</tr>" +
                            "</thead>" +
                            "</table></div>");
                   }*/

                    /*
                    if(data[4]) {
                        for (var ii = 0; ii < data[4].length; ii++) {
                            $('#calendar_head' + num).find('#day_' + data[4][ii]).css({
                                'background-color': '#3c8cbb',
                                'color': '#ffffff'
                            });
                        }
                    }*/
                    //next month
                    num++;
                    if (num < 5) {
                        genMonths(startDate.addMonths(1), num);
                    }else{
                        show = true;
                    }

                },
                error: function (data) {
                }
            });
        }


        function kontingentModal(date, id, pop, hotel_name) {

            //  var idpack= $(document).find("#pop_"+pop+"_"+id).find('#package_id').val();
            //$(document).find('#modalCreateKontingent').find('#agreement_from_'+idpack).addClass('hidden');





            var content = $(document).find("#pop_"+pop+"_"+id).html();


            current_date = $(document).find("#pop_"+pop+"_"+id).data('dates');

            //    content = content.replace(/<url/g,"<a");
            content = content.replace(/div/g,"tr");
            content = content.replace(/span/g,"td");
            //    content = content.replace(/<font>[\s\S]*?<\/font>/g, hotel_name );

            $('#modalCreateKontingent').find('#modalCreateKontigentLabel').html(date);
            $('#modalCreateKontingent').find('#from_div').html(content);

            $('#modalCreateKontingent').find('#modalCreateKontigentQuota').val($(document).find("#quota_"+pop+"_"+id).text());
            $('#modalCreateKontingent').find('#modalCreateKontigentValue').val($(document).find("#pop_"+pop+"_"+id).closest('a').text().split("\n")[0]);

            $('#modalCreateKontingent').find('#from_div .ui-sortable-handle .add-service-button').parent().parent().removeClass('hidden');
            $('#modalCreateKontingent').find('#from_div .ui-sortable-handle .delete-service-button').parent().parent().removeClass('hidden');
            $('#modalCreateKontingent').find('#from_div .ui-sortable-handle .add-service-button').parent().parent().addClass('hidden');
            // content = content.replace(/<tr/g,"tr class='hidden' ");

            //   content = $(document).find('#from_div').html();
            //  content = content.replace(/span/g,"<td class='hidden' ");
            content = content.replace(/agreement_from_/g,"agreement_to_");
            //   content = content.replace(/addFromAgreement/g,"removeFromAgreement");
            //  content = content.replace(/Add/g,"Remove");

            //   content = content.replace(/<link>[\s\S]*?<\/link>/g, '<a href="" target="_blank">'+33333333333+'</a>' );

            $('#modalCreateKontingent').find('#to_div').html(content);

            $('#modalCreateKontingent').find('#to_div .ui-sortable-handle .add-service-button').parent().parent().removeClass('hidden');
            $('#modalCreateKontingent').find('#to_div .ui-sortable-handle .delete-service-button').parent().parent().removeClass('hidden');
            $('#modalCreateKontingent').find('#to_div .ui-sortable-handle .delete-service-button').parent().parent().addClass('hidden');

            //   $('#modalCreateKontingent').find('#to_div').closest('table').removeClass('hidden');
            //  $('#modalCreateKontingent').find('#from_div').closest('table').removeClass('hidden');
            //  $('#modalCreateKontingent').find('#empty_message').removeClass('hidden');
            setModalMaxHeight('#modalCreateKontingent');

            $('#modalCreateKontingent').modal();

        }

        function checkAgreement(id,hotel_id,season) {
            $('#deleteAgreement').find('#id_field').val(id);
            $('#deleteAgreement').find('#hotel_id_field').val(hotel_id);

            $('#deleteAgreement').off('hidden');

            $('#deleteAgreement').modal();
            if(season){
                $('#deleteAgreement').find('.btn-primary').on('click', function (e) {
                    deleteAgreement(1);
                });
            }
        }

        function deleteAgreement(season) {

            $('#deleteAgreement').off('hidden');

            var id = $('#deleteAgreement').find('#id_field').val(),
                hotel_id = $('#deleteAgreement').find('#hotel_id_field').val();
            if(season){
                url = "/season/" + hotel_id + "/delete/" + id;
            }else {
                url = "/agreements/" + hotel_id + "/delete/" + id;
            }
            $('#deleteAgreement').modal('hide');
            location.href = url;
        }

        function replaceFromPackages(id) {

            $('#modalCreateKontingent').modal('hide');

            $('#replacePackage').on('hidden.bs.modal', function () {
                $('#modalCreateKontingent').modal('show');
            });

            $('#replacePackage').find('#id_package').val(id);

            $('#replacePackage').modal();

        }

        function deleteHotelFromPackages(package_id, hotel_id, date, hotel_name, tour_name) {

            $('#modalCreateKontingent').modal('hide');

            $('#deleteAgreement').on('hidden.bs.modal', function () {
                $('#modalCreateKontingent').modal('show');
            });

            $('#deleteAgreement').find('.btn-primary').prop('onclick',null).off('click');

            $('#deleteAgreement').modal();

            $('#deleteAgreement').find('.btn-primary').on('click', function (e) {

                $('#deleteAgreement').off('hidden');
                $('#deleteAgreement').modal('hide');
                $('#modalCreateKontingent').modal('hide');


                $.ajax({
                    type: "GET",
                    url: '{{ route('kontingent_delete') }}',
                    data: { package_id:  package_id },

                    success: function (data) {

                        $('#deleteAgreement').find('.btn-primary').prop('onclick',null).off('click');
                        $('#deleteAgreement').find('.btn-primary').on('click', function (e) {
                            deleteAgreement();
                        });

                        $('#kontingent_tab').click();

                        restore_reload();

                        setMonths();

                    },

                    error: function (data) {
                        console.log("kontingent delete error2")
                    }
                });
            });

        }

        function restore_reload() {
            removed = [];
        }

        function addFromAgreement() {

            var id = $('#replacePackage').find('#id_package').val(),
                tour = $('#modalCreateKontingent').find('#agreement_from_'+id+ ' #tour_id').val(),
                quota = $('#modalCreateKontingent').find('#modalCreateKontigentQuota').val();
            value = $('#modalCreateKontingent').find('#modalCreateKontigentValue').val();

            if(quota.length == 0) quota = value;

            removed.push([id, parseInt(tour),parseInt(quota)]);

            $('#replacePackage').off('hidden');
            $('#replacePackage').modal('hide');

            $.ajax({
                type: "GET",
                url: '{{ route('kontingent_save') }}',
                data: { replace_id: JSON.stringify(removed), hotel_id : {{$hotel->id}} ,dates : current_date},
                success: function (data) {
                    if(data['error']) {
                        $('#warnReplace').modal();
                    }else {

                        $('#modalCreateKontingent').modal('hide');
                        setMonths();
                    }
                },

                error: function (data) {
                    console.log("kontingent data error")
                }
            });
        }

        function breakString(text) {
            return text.replace(/(.{100})/g, '$1\n');
        }


        $(document).ready(function () {

            var text = $('#desc_season').html();
            if(text) $('#desc_season').html(breakString(text));

            text = $('#desc_agreement').html();
            if(text) $('#desc_agreement').html(breakString(text));

            $('.date').css("z-index",0);

            $('#start_date').datepicker("remove");
            $('#start_date').datepicker({
                format: "mm-yyyy",
                startView: "months",
                minViewMode: "months"
            });



            $("#start_date").datepicker('setDate', (TodayDate.getMonth() + 1) + '-' + TodayDate.getFullYear());

            $("#start_date").datepicker({
                onSelect: function (dateText) {
                    $(this).change();
                }
            }).on("change", function () {
                $(this).datepicker("hide");
            });

            $('#showMonth').on('click', function (e) {
                setMonths();
            });

            $('.date_calendar').on('click', function (e) {
                $("#start_date").datepicker('show');
            });

            setMonths();

        });
		
		
        // Initialize Bootstrap table functionality for hotel invoices
        initializeBootstrapTable('inovices-table');

        // Hotel invoice table search functionality
        function filterHotelInvoiceTable() {
            const input = document.getElementById('hotelInvoiceSearchInput');
            const filter = input.value.toUpperCase();
            const table = document.getElementById('inovices-table');
            const tr = table.getElementsByTagName('tr');

            for (let i = 1; i < tr.length; i++) {
                let display = false;
                const td = tr[i].getElementsByTagName('td');

                for (let j = 0; j < td.length - 1; j++) { // Exclude action column
                    if (td[j]) {
                        const txtValue = td[j].textContent || td[j].innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            display = true;
                            break;
                        }
                    }
                }

                tr[i].style.display = display ? '' : 'none';
            }
        }

        // Export functions for hotel invoices
        function exportHotelInvoicesToCSV() {
            exportTableToCSV('inovices-table', 'hotel-invoices.csv');
        }

        function exportHotelInvoicesToExcel() {
            exportTableToExcel('inovices-table', 'hotel-invoices');
        }

    </script>
@endsection