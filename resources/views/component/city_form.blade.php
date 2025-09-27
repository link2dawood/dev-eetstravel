{{-- TODO make form reusable, make city form ajax request--}}
<div class="form-group">
    <label for="{{$country_label}}">{!!trans($country_translation)!!} *</label>
    {!! Form::select($country_label,  \App\Helper\Choices::getCountriesArray(), $country_default, ['class' => 'form-control', 'id' => $country_label]) !!}
</div>
<div class="form-group">
    <label for="{{$city_label}}">{!!trans($city_translation)!!} *</label>
    {!! Form::select($city_label, \App\Helper\Choices::getCitiesArray(), $city_default, ['class' => 'form-control', 'id' => $city_label]) !!}
</div>