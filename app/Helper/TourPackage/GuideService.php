<?php

namespace App\Helper\TourPackage;

use App\Guide;
use App\TourPackage;
use View;

class GuideService
{
    public function getItems($criteria = [])
    {
        $results = [];
        if (array_key_exists('serviceType', $criteria)) {
            $query = \DB::table('guides');
            if (array_key_exists('id', $criteria) && $criteria['id']) {
                $query = $query->where('id', $criteria['id']);
            }
            if (array_key_exists('search', $criteria) && $criteria['search']) {
                $query = $query->where('name', 'like', '%'.$criteria['search'].'%');
            }
            $query = $query->where('deleted_at', null);

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
            'service.guide',
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
        return Guide::findOrFail($id);
    }
}
