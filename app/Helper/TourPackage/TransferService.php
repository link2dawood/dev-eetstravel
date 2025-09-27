<?php

namespace App\Helper\TourPackage;

use App\TourPackage;
use App\Transfer;
use View;

class TransferService
{
    public function getItems($criteria = [])
    {
        $results = [];
        if (array_key_exists('serviceType', $criteria)) {
            $query = \DB::table('transfers');
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
            'service.transfer',
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
        return Transfer::findOrFail($id);
    }
}
