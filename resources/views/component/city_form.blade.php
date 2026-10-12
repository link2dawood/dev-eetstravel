{{-- Country + city pair. The city list holds only the chosen country's cities (value = city id);
     public/js/cities.js reloads it when the country changes. city_default may be an id or a name. --}}
@php
    $cityOptions = \App\Helper\Choices::getCitiesForCountry($country_default ?: null);
    $citySelected = $city_default ?: null;
    if ($citySelected !== null && !array_key_exists($citySelected, $cityOptions)) {
        $citySelected = array_search($citySelected, $cityOptions, true) ?: null;
    }
@endphp
<div class="form-group">
    <label for="{{$country_label}}">{!!trans($country_translation)!!} *</label>
    {!! Form::select($country_label,  \App\Helper\Choices::getCountriesArray(), $country_default, ['class' => 'form-control', 'id' => $country_label]) !!}
</div>
<div class="form-group">
    <label for="{{$city_label}}">{!!trans($city_translation)!!} *</label>
    {!! Form::select($city_label, $cityOptions, $citySelected, ['class' => 'form-control', 'id' => $city_label]) !!}
</div>
