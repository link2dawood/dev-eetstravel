@extends('scaffold-interface.layouts.app')
@section('title','Show')
@section('content')
	@include('layouts.title',
   ['title' => 'Flight', 'sub_title' => 'Flight Show',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Flights', 'icon' => 'plane', 'route' => route('flights.index')],
   ['title' => 'Show', 'route' => null]]])
	<section class="content">
		<div class="box box-primary">
			<div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="margin_button">
                            <a href="javascript:history.back()">
                                <button class='btn btn-primary'>{!!trans('main.Back')!!}</button>
                            </a>
                            <a href="{!! route('flights.edit', $flight->id) !!}">
                                <button class='btn btn-warning'>{!!trans('main.Edit')!!}</button>
                            </a>
                        </div>
                    </div>
                </div>
                <ul class="nav nav-tabs" role='tablist'>
                    <li role='presentation' class="active"><a href="#info-tab" aria-controls='info-tab' role='tab' data-toggle='tab'>{!!trans('main.Info')!!}</a></li>
                    <li role='presentation'><a href="#history-tab" aria-controls='history-tab' role='tab' data-toggle='tab'>{!!trans('main.History')!!}</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade in active" role='tabpanel' id='info-tab'>
                        <table class="table table-bordered table_show col-lg-6">
                            <tbody>
                        <tr>
                            <td><b>{!!trans('main.Name')!!}:</b></td>
                            <td class="info_td_show">{{$flight->name}}</td>
                        </tr>
                        <tr>
                            <td><b>{!!trans('main.Datefrom')!!} :</b></td>
                            <td class="info_td_show">{{$flight->date_from}}</td>
                        </tr>
                        <tr>
                            <td><b>{!!trans('main.Dateto')!!} :</b></td>
                            <td class="info_td_show">{{$flight->date_to}}</td>
                        </tr>
                        <tr>
                            <td><b>{!!trans('main.CountryFrom')!!}: </b></td>
                            <td class="info_td_show">{!! \App\Helper\CitiesHelper::getCountryById($flight->country_from)['name']!!}</td>
                        </tr>
                        <tr>
                            <td><b>{!!trans('main.CityFrom')!!}: </b></td>
                            <td class="info_td_show">{!! \App\Helper\CitiesHelper::getCityById($flight->city_from)['name']!!}</td>
                        </tr>
                        <tr>
                            <td><b>{!!trans('main.CountryTo')!!}: </b></td>
                            <td class="info_td_show">{!! \App\Helper\CitiesHelper::getCountryById($flight->country_to)['name']!!}</td>
                        </tr>
                        <tr>
                            <td><b>{!!trans('main.CityTo')!!}: </b></td>
                            <td class="info_td_show">{!! \App\Helper\CitiesHelper::getCityById($flight->city_to)['name']!!}</td>
                        </tr>
                        <tr>
                            <td><b>{!!trans('main.Addressfirst')!!}:</b></td>
                            <td class="info_td_show">{{$flight->address_first}}</td>
                        </tr>
                        <tr>
                            <td><b>{!!trans('main.AddressSecond')!!}:</b></td>
                            <td class="info_td_show">{{$flight->address_second}}</td>
                        </tr>
                        <tr>
                            <td><b>{!!trans('main.Code')!!}:</b></td>
                            <td class="info_td_show">{{$flight->code}}</td>
                        </tr>
                        </tbody>
                        </table>
                        <table class="table table-bordered table_show col-lg-6">
                            <tbody>
                            <tr>
                                <td><b>{!!trans('main.WorkPhone')!!}:</b></td>
                                <td class="info_td_show">{{$flight->work_phone}}</td>
                            </tr>
                            <tr>
                                <td><b>{!!trans('main.WorkFax')!!}:</b></td>
                                <td class="info_td_show">{{$flight->work_fax}}</td>
                            </tr>
                            <tr>
                                <td><b>{!!trans('main.WorkEmail')!!}:</b></td>
                                <td class="info_td_show">{{$flight->work_email}}</td>
                            </tr>
                            <tr>
                                <td><b>{!!trans('main.IntComments')!!}:</b></td>
                                <td class="info_td_show">{{$flight->int_comments}}</td>
                            </tr>
                            <tr>
                                <td><b>{!!trans('main.Comments')!!}:</b></td>
                                <td class="info_td_show">{{$flight->comments}}</td>
                            </tr>
                            <tr>
                                <td><b>{!!trans('main.ContactName')!!}:</b></td>
                                <td class="info_td_show">{{$flight->contact_name}}</td>
                            </tr>
                            <tr>
                                <td><b>{!!trans('main.ContactPhone')!!}:</b></td>
                                <td class="info_td_show">{{$flight->contact_phone}}</td>
                            </tr>
                            <tr>
                                <td>
                                    <b><i>{!!trans('main.Criterias')!!} : </i></b>
                                </td>
                                @forelse($criterias as $criteria)
                                    @forelse($flight->criterias as $item)
                                        @if($criteria->id == $item->criteria_id)
                                            <td class="info_td_show criteria_block" style="display: block">{!!$criteria->name!!}</td>
                                        @endif
                                    @empty
                                        <td class="info_td_show"></td>
                                    @endforelse
                                @empty
                                    <td class="info_td_show"></td>
                                @endforelse
                            </tr>
                            <tr>
                                <td>
                                    <b><i>{!!trans('main.Rate')!!} : </i></b>
                                </td>
                                <td class="info_td_show">{!!$flight->rate_name!!}</td>
                            </tr>
                            <tr>
                                <td><b>{!!trans('main.Website')!!}:</b></td>
                                <td class="info_td_show">{{$flight->website}}</td>
                            </tr>
                            </tbody>
                        </table>
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
                    <input type="text" id="default_reference_id" hidden name="reference_id" value="{{ $flight->id }}">
                    <input type="text" id="default_reference_type" hidden name="reference_type" value="{{ \App\Comment::$services['flight']}}">

                    <button type="submit" class="btn btn-success pull-right" id="btn_send_comment" style="margin-top: 5px;">{!!trans('main.Send')!!}</button>
                </form>
            </div>
        </div>
                    </div>
                    <div class="tab-pane fade" role='tabpanel' id='history-tab'>
                        <div id="history-container"></div>
                    </div>
                </div>
				
            </div>
        </div>
	</section>
        <span id="services_name" data-service-name='Flight' data-history-route="{{route('services_history', ['id' => $flight->id])}}"></span>
@endsection

@section('post_scripts')
	<script src="{{ asset('js/comment.js') }}"></script>
@endsection