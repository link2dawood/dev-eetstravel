<?php

namespace App\Http\Requests\Concerns;

use App\City;

/**
 * Tour forms send cities as names (Google Places autocomplete) while validation
 * expects cities.id; resolve them before validation runs.
 */
trait ResolvesCityIds
{
    protected function mergeResolvedCityIds()
    {
        $resolved = [];
        foreach (['begin', 'end'] as $side) {
            if ($this->has("city_{$side}")) {
                $resolved["city_{$side}"] = $this->resolveCityId(
                    $this->input("city_{$side}"),
                    $this->input("country_{$side}"),
                    $this->input("city_{$side}_code")
                );
            }
        }
        $this->merge($resolved);
    }

    /**
     * Turn a city name into a cities.id, preferring a match in the selected country.
     * Unknown cities picked from Google Places are created from their place code,
     * the same way TourController@update does.
     */
    protected function resolveCityId($city, $country = null, $code = null)
    {
        if ($city === null || $city === '' || is_numeric($city)) {
            return $city;
        }

        $name = trim($city);
        $query = City::query()->where('name', $name);
        if ($country) {
            $match = (clone $query)->where('country', $country)->value('id');
            if ($match) {
                return $match;
            }
        }
        if ($id = $query->value('id')) {
            return $id;
        }

        if ($code) {
            $existing = City::query()->where('code', $code)->value('id');
            if ($existing) {
                return $existing;
            }
            return City::create(['code' => $code, 'name' => $name, 'country' => $country])->id;
        }

        return $city; // unknown name without a place code: let validation report it
    }
}
