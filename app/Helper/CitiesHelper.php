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

    public static function changeCityNameToID($cityName){
        // Already an id (e.g. resolved during validation): keep it instead of searching by name
        if (is_numeric($cityName) && City::whereKey((int) $cityName)->exists()) {
            return (int) $cityName;
        }
        return City::where('name', '=', $cityName)->pluck('id')->first();
    }

    public static function setCityGeneral(Request $request)
    {
        $request['city'] = self::changeCityNameToID($request['city']);
        return $request;
    }

    public static function setCityFrom(Request $request)
    {
        $request['city_from'] = self::changeCityNameToID($request['city_from']);
        return $request;
    }

    public static function setCityTo(Request $request)
    {
        $request['city_to'] = self::changeCityNameToID($request['city_to']);
        return $request;
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
