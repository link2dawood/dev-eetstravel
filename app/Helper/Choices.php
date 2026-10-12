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

    /**
     * Cities of one country for a select: [id => name]. City names repeat across
     * countries (e.g. Paris FR / Paris US), so forms submit the id, not the name.
     */
    public static function getCitiesForCountry($countryAlias)
    {
        $cities = ['' => 'Select city'];
        if (!$countryAlias) {
            return ['' => 'Select a country first'];
        }
        return $cities + City::where('country', $countryAlias)->orderBy('name')->pluck('name', 'id')->toArray();
    }

    public static function getCitiesArray()
    {
        $cities = ['' => 'Select cities'];
        $cities = array_merge($cities, City::all()->pluck('name', 'name')->toArray()); // Using City model
        return $cities;
    }
}
