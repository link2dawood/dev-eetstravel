<?php
namespace App\Repository\Contracts;

use App\TourPackage;

interface TourPackageRepository
{
	public function setCityTaxInPackage(TourPackage $tourPackage);

	public function setCityTaxInHotel(TourPackage $tourPackage);

	public function syncPackageMenus(TourPackage $tourPackage, $packageMenus);

	public function getHotelRoomTypePrice($tourPackage, $roomTypeId);

	public function getHotelRoomTypeCount($tourPackage, $roomTypeId);

}