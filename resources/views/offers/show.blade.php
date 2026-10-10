@extends('scaffold-interface.layouts.tabler-app')
@section('title','Show')
@section('content')
    @include('layouts.title',
   ['title' => 'Hotel Offer ', 'sub_title' => 'offer Show',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
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
                    </div>
                </div>
            </div>
            <div id="fixed-scroll" class="nav-tabs-custom">
                <ul class="nav nav-tabs" id="fixed-scroll" role='tablist'>
                    <li role='presentation' class="active"><a href="#info-tab" aria-controls='info-tab' role='tab' data-toggle='tab'>{!!trans('main.Info')!!}</a></li>
             
                 
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane fade in active" role='tabpanel' id='info-tab'>
					
					<input id="invoice_id" type="hidden" name="invoice_id" value ="{{$offer->id}}">
					<table class = 'table_show table table-bordered col-lg-6'>
                        <tbody>
                       
                        <tr>
                            <td>
                                <b><i>{!!trans('Hotel name')!!} : </i></b>
                            </td>
                            <td class="info_td_show">{!!$package->name??""!!}</td>
                        </tr>
                        <tr>
                            <td>
                                <b><i>{!!trans('City')!!} : </i></b>
                            </td>
                            <td class="info_td_show">{!!$city->name??"";!!}</td>
                        </tr>
                        </tbody>
                    </table>
                    <table class = 'table_show table table-bordered col-lg-6'>
                        <tbody>
                       
                        <tr>
                            <td>
                                <b><i>{!!trans('Tour Name')!!} : </i></b>
                            </td>
                            <td class="info_td_show">{!!$tour->name ?? ''!!}</td>
                        </tr>
                        <tr>
                            <td>
                                <b><i>{!!trans('Status')!!} : </i></b>
                            </td>
                            <td class="info_td_show">{!!$offer->status;!!}</td>
                        </tr>
                        </tbody>
                    </table>
                    <table class = 'table_show table table-bordered col-lg-6'>
                        <tbody>
                        
                        
                        <tr>
                            <td>
                                <b><i>{!!trans('Supplier Status')!!} : </i></b>
                            </td>
                            <td class="info_td_show">{!!$offer->getStatusName($offer->tms_status)??""!!}</td>
                        </tr>
                        <tr>
                            <td>
                                <b><i>{!!trans('Option Date')!!} : </i></b>
                            </td>
                            <td class="info_td_show">{!!$offer->option_date!!}</td>
                        </tr>
                        </tbody>
                    </table>
					
					<table class = 'table_show table table-bordered col-lg-6'>
                        <tbody>
                        
                        
                        <tr>
                            <td>
                                <b><i>{!!trans('Offer Date')!!} : </i></b>
                            </td>
                            <td class="info_td_show">{{ $offer->created_at ? \Carbon\Carbon::parse($offer->created_at)->toDateString() : '' }}</td>
                        </tr>
                        <tr>
                            <td>
                                <b><i>{!!trans('Date of stay')!!} : </i></b>
                            </td>
                            <td class="info_td_show">{{ $stay_date ?? '' }}</td>
                        </tr>
                        <tr>
                            <td>
                                <b><i>{!!trans('City Tax')!!} : </i></b>
                            </td>
                            <td class="info_td_show">{!!$offer->city_tax!!}</td>
                        </tr>
                        </tbody>
                    </table>
					
					<table class = 'table_show table table-bordered col-lg-6'>
                        <tbody>
                        
                        
                        <tr>
                            <td>
                                <b><i>{!!trans('Halfboard Supp p.p')!!} : </i></b>
                            </td>
                            <td class="info_td_show">{!!$offer->halfboardMax!!}</td>
                        </tr>
                        <tr>
                            <td>
                                <b><i>{!!trans('foc')!!} : </i></b>
                            </td>
                            <td class="info_td_show">{!!$offer->foc_after_every_pax!!}</td>
                        </tr>
                        </tbody>
                    </table>
					
					<table class = 'table_show table table-bordered col-lg-6'>
                        <tbody>
                        
                        
                        <tr>
                            <td>
                                <b><i>{!!trans('Porterage pp')!!} : </i></b>
                            </td>
                            <td class="info_td_show">{{ $offer->portrage_perperson ?? '' }}</td>
                        </tr>
                        <tr>
                            <td>
                                <b><i>{!!trans('Hotel File')!!} : </i></b>
                            </td>
                            <td class="info_td_show">{!!$offer->hotel_file!!}</td>
                        </tr>
                        </tbody>
                    </table>
					
					<table class = 'table_show table table-bordered col-lg-6'>
                        <tbody>
                        
                        
                        <tr>
                            <td>
                                <b><i>{!!trans('Hotel Note')!!} : </i></b>
                            </td>
                            <td class="info_td_show">{{ $offer->hotel_note ?? '' }}</td>
                        </tr>
						@php
							$printedRoomNames = [];
							@endphp

							@foreach ($selected_room_types as $selected_room_type)
							@if (!in_array($selected_room_type->name, $printedRoomNames))
							<tr>
								<td>
									<b>{{$selected_room_type->name }}</b>
								</td>
								<td class="info_td_show">{!!$offer->offersWithRoomPrice($selected_room_type)??""!!}</td>
							</tr>
							@php
							$printedRoomNames[] = $selected_room_type->name;
							@endphp
							@endif
							@endforeach
                       
                        </tbody>
                    </table>
                    <div style="clear: both"></div>
                   
                </div>
				<div class="">
					<h3 class="box-title">Cancellation Policies</h3>
					<table id="recent-offers-table" class="table table-striped table-bordered table-hover" style='background:#fff; width: 100%'>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>{!!trans('Days before arrival')!!}</th>
                        <th>{!!trans('Free cancellation')!!}</th>
                        <th>{!!trans('Policy')!!}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($policies as $policy)
                        <tr>
                            <td>{{ $policy->id }}</td>
                            <td>{{ $policy->cancellation_days }}</td>
                            <td>{{ $policy->cancellation_percentage }} {{ $policy->cancellation_type }}</td>
                            <td>{{ $policy->cancellation_days }} days before arrival: {{ $policy->cancellation_percentage }}{{ $policy->cancellation_type }} can be cancelled free of charge.</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted">No cancellation policies for this offer.</td></tr>
                    @endforelse
                    </tbody>
                </table>
                @if($offer->cancellationNote)
                    <p><b>{!!trans('Additional Cancellation Policies')!!}:</b> {{ $offer->cancellationNote }}</p>
                @endif
				</div>
           
                
					
            </div>
            </div>
</section>
@endsection

@section('post_scripts')
    <script src="{{ asset('js/comment.js') }}"></script>
	
@endsection
