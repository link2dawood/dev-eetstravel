<div class="col-md-12">
	<div class="select_filter">
		<label style="display: block">{!!trans('main.SelectCriteriatoFilter')!!}: </label>
		@foreach($criterias as $criteria)
			<div class="checkbox checkbox-inline select_filters">
				<input type="checkbox" name="{{$criteria->id}}">{{$criteria->name}}
			</div>
		@endforeach
	</div>

@if($rates)
	<div class="select_rate">
		<label style="display: block">{!!trans('main.CategoryorInterrateFilter')!!}: </label>
		@foreach($rates as $rate)
		<div class="checkbox checkbox-inline select_categories_rate">
			<input type="radio" name="rates" value="{{$rate->id}}">{{$rate->name}}
		</div>
		@endforeach
		<div class="clear_btn_selects_filters">
			<button class="btn btn-warning btn-sm pull-right" id='clear-filter'>{!!trans('main.ClearFilter')!!}</button>
		</div>
	</div>
@endif
</div>