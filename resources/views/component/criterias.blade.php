<div class="col-md-12">
	<div class="select_filter mb-2">
		<label class="form-label">{!!trans('main.SelectCriteriatoFilter')!!}:</label>
		@forelse($criterias as $criteria)
			<label class="form-check form-check-inline select_filters">
				<input class="form-check-input" type="checkbox" name="{{$criteria->id}}">
				<span class="form-check-label">{{$criteria->name}}</span>
			</label>
		@empty
			<span class="text-muted">No criteria for this service type.</span>
		@endforelse
	</div>

@if($rates)
	<div class="select_rate">
		<label class="form-label">{!!trans('main.CategoryorInterrateFilter')!!}:</label>
		@foreach($rates as $rate)
		<label class="form-check form-check-inline select_categories_rate">
			<input class="form-check-input" type="radio" name="rates" value="{{$rate->id}}">
			<span class="form-check-label">{{$rate->name}}</span>
		</label>
		@endforeach
	</div>
@endif
	<div class="mt-2">
		<button type="button" class="btn btn-outline-secondary btn-sm" id="clear-filter">{!!trans('main.ClearFilter')!!}</button>
	</div>
</div>
