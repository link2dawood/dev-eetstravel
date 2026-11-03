<?php

namespace App\Helper;

use App\City;
use App\Country;
use SKAgarwal\GoogleApi\PlacesApi;
use Illuminate\Http\Request;

class CitiesHelper
{
    // Views read getCityById(...)['name'] directly: a record without a (valid) city
    // used to crash the page, so an empty City/Country is returned instead of null.
    public static function getCityById($id)
    {
        return ($id ? City::where('id', '=', $id)->first() : null) ?? new City();
    }

    public static function getCountryById($id)
    {
        return ($id ? Country::where('alias', '=', $id)->first() : null) ?? new Country();
    }

    /**
     * @param mixed       $cityName     a city id (from the form's city select) or a city name
     * @param string|null $countryAlias the country posted with it; names are matched within it,
     *                                  since many names exist in several countries (Paris FR/US)
     */
    public static function changeCityNameToID($cityName, $countryAlias = null){
        // Already an id (e.g. resolved during validation): keep it instead of searching by name
        if (is_numeric($cityName) && City::whereKey((int) $cityName)->exists()) {
            return (int) $cityName;
        }
        if ($cityName === null || $cityName === '') {
            return null;
        }
        if ($countryAlias) {
            // No match in that country means unknown, not "the first city of that name anywhere"
            return City::where('name', '=', $cityName)->where('country', $countryAlias)->value('id');
        }
        return City::where('name', '=', $cityName)->pluck('id')->first();
    }

    /**
     * Resolve one city field in place. A field that was not sent is left untouched, so partial
     * updates (API, other callers) do not wipe the stored city.
     */
    private static function resolveCityField(Request $request, $cityField, $countryField)
    {
        if ($request->exists($cityField)) {
            $request[$cityField] = self::changeCityNameToID($request[$cityField], $request[$countryField] ?? null);
        }
        return $request;
    }

    public static function setCityGeneral(Request $request)
    {
        return self::resolveCityField($request, 'city', 'country');
    }

    public static function setCityFrom(Request $request)
    {
        return self::resolveCityField($request, 'city_from', 'country_from');
    }

    public static function setCityTo(Request $request)
    {
        return self::resolveCityField($request, 'city_to', 'country_to');
    }

    /** TODO This function is only used by tours could be refactored to setCityTo
     * @param Request $request
     * @return Request
     */
    public static function setCityBegin(Request $request)
    {
        $request['city_begin'] = self::changeCityNameToID($request['city_begin']);
        return $request;
    }
    /** TODO This function is only used by tours and busController could be refactored to setCityTo
     * @param Request $request
     * @return Request
     */
    public static function setCityEnd(Request $request)
    {
        $request['city_end'] = self::changeCityNameToID($request['city_end']);
        return $request;
    }
}
