<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 19.07.17
 * Time: 17:28
 */

namespace App\Helper;


use App\GooglePlaces;
use SKAgarwal\GoogleApi\PlacesApi;
use SKAgarwal\GoogleApi\Exceptions\GooglePlacesApiException;
use GuzzleHttp\Exception\GuzzleException;

class GooglePlacesHelper
{
    public static function getPlace($service_id, $type){
        $infoPlace = GooglePlaces::where('type', $type)->where('service_id', $service_id)->first();
        if($infoPlace){
            try{
//                $googlePlaces = new PlacesApi('AIzaSyBQt2AhjTda22hWbyq-FvG1xP806ZmzV1A');
                $googlePlaces = app(PlacesApi::class, ['key' => config('google.places.key')]);
//                $googlePlaces = new PlacesApi('AIzaSyDvx-JKMyREociW9aPFqsBrnYysrCeV6us');
//                $googlePlaces = new PlacesApi('AIzaSyDFcQzqTzcEMlfYSCPcSaM95aBLyugwDZk');
//                $googlePlaces = new PlacesApi('AIzaSyCX-j9dFmFtiAZNfxv6oYYpbwO-2yKxvvA');
//                $googlePlaces = new PlacesApi('AIzaSyB-Jw7JkQA2QbVZFQssictsimeqwSXPsBg');
                $place = @$googlePlaces->placeDetails($infoPlace->place_id);
            } catch (GooglePlacesApiException | GuzzleException $e){
                return null;
            }
            return $place;

        }else{
            return null;
        }

    }
}
