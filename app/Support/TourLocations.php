<?php
namespace App\Support;
class TourLocations {
    public static function normalize(array $data): array {
        foreach(['begin','end'] as $side) {
            $country=$data['country_'.$side]??null;
            if(is_string($country) && $country!=='') {
                $alias=\App\Country::where('alias',$country)->orWhere('name',$country)->value('alias');
                if($alias) $data['country_'.$side]=$alias;
            }
            $city=$data['city_'.$side]??null;
            if(is_string($city) && $city!=='' && !ctype_digit($city)) {
                $query=\App\City::where('name',trim($city));
                if(isset($data['country_'.$side])) $query->where('country',$data['country_'.$side]);
                $id=$query->value('id');
                if($id) $data['city_'.$side]=$id;
            }
        }
        return $data;
    }
}
