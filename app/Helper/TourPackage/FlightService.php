<?php

namespace App\Helper\TourPackage;

use App\Flight;
use App\TourPackage;
use View;

class FlightService
{
    public function getItems($criteria = [])
    {
        $results = [];
        if (array_key_exists('serviceType', $criteria)) {
            $query = \DB::table('flights');
            if (array_key_exists('id', $criteria) && $criteria['id']) {
                $query = $query->where('id', $criteria['id']);
            }
            if (array_key_exists('search', $criteria) && $criteria['search']) {
                $query = $query->where('name', 'like', '%'.$criteria['search'].'%');
            }

            $results = $query->paginate(5);
        }

        return $results;
    }


    public function generateTable($parameters)
    {
        $serviceTypes   =   TourService::$serviceTypes;
        $serviceType    =   $parameters['serviceType'];
        $search         =   $parameters['search'];
        $filterType     =   $parameters['filterType'];
        $services       =   $this->getItems(['serviceType' => $serviceType, 'search' => $search]);
        $view           =   View::make(
            'service.flight',
            compact(
                'serviceTypes',
                'serviceType',
                'search',
                'services',
                'filterType'
            )
        );
        $contents = $view->render();

        return $contents;
    }

    public function getItem($id)
    {
        return Flight::findOrFail($id);
    }
}
