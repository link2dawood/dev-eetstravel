<?php

namespace App\Helper;

use App\Country;
use App\City;  // Correct import for the City model

class Choices
{
    public static function getCountriesArray()
    {
        $countries = ['' => 'Select country'];
        $countries = array_merge($countries, Country::all()->pluck('name', 'alias')->toArray());

        return $countries;
    }

    public static function getCountriesSupplierSearchArray()
    {
        $countries = ['' => 'Country'];
        $countries = array_merge($countries, Country::all()->pluck('name', 'alias')->toArray());

        return $countries;
    }

    public static function getCitiesArray()
    {
        $cities = ['' => 'Select cities'];
        $cities = array_merge($cities, City::all()->pluck('name', 'name')->toArray()); // Using City model
        return $cities;
    }
}
