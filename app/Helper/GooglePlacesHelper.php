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

class GooglePlacesHelper
{
    public static function getPlace($service_id, $type){
        $infoPlace = GooglePlaces::where('type', $type)->where('service_id', $service_id)->first();
        if($infoPlace){
            try{
//                $googlePlaces = new PlacesApi('AIzaSyBQt2AhjTda22hWbyq-FvG1xP806ZmzV1A');
                $googlePlaces = new PlacesApi('AIzaSyBV706sie0bSi4QCwu06KbvH3QBiTSNJzY');
//                $googlePlaces = new PlacesApi('AIzaSyDvx-JKMyREociW9aPFqsBrnYysrCeV6us');
//                $googlePlaces = new PlacesApi('AIzaSyDFcQzqTzcEMlfYSCPcSaM95aBLyugwDZk');
//                $googlePlaces = new PlacesApi('AIzaSyCX-j9dFmFtiAZNfxv6oYYpbwO-2yKxvvA');
//                $googlePlaces = new PlacesApi('AIzaSyB-Jw7JkQA2QbVZFQssictsimeqwSXPsBg');
                $place = @$googlePlaces->placeDetails($infoPlace->place_id);
            } catch (GooglePlacesApiException $e){
                return null;
            }
            return $place;

        }else{
            return null;
        }

    }
}