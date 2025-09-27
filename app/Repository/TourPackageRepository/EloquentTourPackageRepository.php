<?php

namespace App\Repository\TourPackageRepository;

use App\Hotel;
use App\Menu;
use App\PackageMenu;
use App\Repository\Contracts\TourPackageRepository;
use App\Repository\Contracts\PackageMenuRepository;
use App\Tour;
use App\TourPackage;
use Auth;
use Carbon\Carbon;

class EloquentTourPackageRepository implements TourPackageRepository {
	public function setCityTaxInPackage( TourPackage $tourPackage ) {
		if ( $tourPackage->service() instanceof Hotel ) {
			$tourPackage->city_tax = $tourPackage->service()->city_tax;
			$tourPackage->save();

		}
	}

	public function setCityTaxInHotel( TourPackage $tourPackage ) {
		if ( $tourPackage->service() instanceof Hotel ) {
			$cityTax = $tourPackage->city_tax;
			$hotel = $tourPackage->service();
			Hotel::updateOrInsert( [ 'id' => $hotel->id ], [ 'city_tax' => $cityTax ] );
		}
	}

	public function syncPackageMenus( TourPackage $tourPackage, $packageMenus ) {
		$menuIds = [];

        $notIds = PackageMenu::query()->where( [ 'tour_package_id' => $tourPackage->id ] )
            ->whereNotIn( 'menu_id', $menuIds )->get()->pluck( [ 'id' ] )->toArray();
        PackageMenu::destroy( $notIds );

		if ( $packageMenus ) {
			foreach ( $packageMenus as $packageMenu ) {
				if ( ! ( $packageMenu['count'] && $packageMenu['menu'] && is_numeric( $packageMenu['count'] ) ) ) {
				    continue;
				}
				$menuIds[] = $packageMenu['menu'];
				PackageMenu::query()->updateOrCreate(
					[
						'menu_id'         => $packageMenu['menu'],
						'tour_package_id' => $tourPackage->id
					],
					[ 'count' => $packageMenu['count'] ]
				);

			}
		}
	}

	public function getHotelRoomTypePrice($tourPackage, $roomTypeId) {
		$hotel = Hotel::where('id', $tourPackage->reference)->first();
		if ($hotel) {
			foreach ($hotel->seasons as $season){
				if(Carbon::parse($tourPackage->time_from)->format('Y-m-d') > $season->start_date && Carbon::parse($tourPackage->time_from)->format('Y-m-d') < $season->end_date){
					foreach($season->seasons_room_types as $room){
						if($room->room_type_id == $roomTypeId) {
							return  $room->price;
						}
					}
				}
			}
		}

		return 0;
	}

	public function getHotelRoomTypeCount($tourPackage, $roomTypeId) {
		$hotel = Hotel::where('id', $tourPackage->reference)->first();
		if($hotel) {
			foreach ($hotel->seasons as $season){
				if(Carbon::parse($tourPackage->time_from)->format('Y-m-d') > $season->start_date && Carbon::parse($tourPackage->time_from)->format('Y-m-d') < $season->end_date){
					foreach($season->seasons_room_types as $room){
						if($room->room_type_id == $roomTypeId) {
							return  $room->count;
						}
					}
				}
			}
		}

		return 0;
	}
}