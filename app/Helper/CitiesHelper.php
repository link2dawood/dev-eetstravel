<?php

namespace App\Helper;

use App\City;
use App\Country;
use SKAgarwal\GoogleApi\PlacesApi;
use Illuminate\Http\Request;

class CitiesHelper
{
    public static function getCityById($id)
    {
        return City::where('id', '=', $id)->get()->first();
    }

    public static function getCountryById($id)
    {
        return Country::where('alias', '=', $id)->get()->first();
    }

    public static function changeCityNameToID($cityName){
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
