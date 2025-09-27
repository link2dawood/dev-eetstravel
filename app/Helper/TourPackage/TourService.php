<?php

namespace App\Helper\TourPackage;

class TourService
{
    public $serviceType;

    public $service;

    public static $serviceTypes = [
        0   => 'hotel',
        1   => 'event',
        2   => 'guide',
        3   => 'transfer',
        4   => 'restaurant',
        5   => 'tourPackage',
        6   => 'cruise',
        7   => 'flight'
    ];

    public static function getService($type)
    {
        switch ($type) {
            case 'tourPackage':
                return new TourPackageService();
            case 'hotel':
                return new HotelService();
            case 'guide':
                return new GuideService();
            case 'restaurant':
                return new RestaurantService();
            case 'transfer':
                return new TransferService();
            case 'event':
                return new EventService();
            case 'cruise':
                return new CruiseService();
            case 'flight':
                return new FlightService();
        }
    }

    public function __construct($serviceType)
    {
        $this->serviceType = $serviceType;
        $this->service = $this->getService($serviceType);
    }

}
