<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreOfferRequest;
use App\Http\Requests\UpdateOfferRequest;
use App\Currencies;
use App\TourPackage;
use Illuminate\Support\Facades\Crypt;
use App\Repository\Contracts\TourPackageRepository;
use Illuminate\Support\Facades\DB;
use App\Status;
use App\City;
use App\Hotel;
use App\Notification;
use App\User;
use App\HotelOffers;
use App\Event;
use App\Transfer;
use App\Bus;
use App\Restaurant;
use App\Guide;
use App\Task;
use App\RoomTypes;
use App\TourDay;
use App\OfferCancellationPolicies;
use App\Helper\TourPackage\TourService;
use App\Http\Imap\ImapClient;
use Carbon\Carbon;
use App\Helper\LaravelFlashSessionHelper;
use App\Helper\PermissionHelper;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Session;
use Auth;
use App\Tour;
use Amranidev\Ajaxis\Ajaxis;
use URL;
class OfferController extends Controller
{
    //
	protected $tourPackageRepository;
	public $serviceTypes = [
		0   => 'hotel',
		1   => 'event',
		2   => 'guide',
		3   => 'transfer',
		4   => 'restaurant',
		5   => 'tourPackage',
		6   => 'cruise',
		7   => 'flight'
	];
	
	public function __construct(TourPackageRepository $tourPackageRepository)
    {
        $this->middleware('permissions.required');
        $this->tourPackageRepository = $tourPackageRepository;
        $this->middleware('preventBackHistory');
        $this->middleware('auth');
    }
	public function cancellation_policies(){
		$room_types = RoomTypes::all();
		$currentDate = Carbon::now(); // Get the current date and time
		$oneWeekLater = $currentDate->copy()->addWeek(); // Add one week to the current date
		$tours = Tour::where('status', '<>', 46)
			->where('retirement_date', '>=', $oneWeekLater)
			->get();

		// Fetch the cancellation policies data directly
		$offers = OfferCancellationPolicies::with('hotelOffer')->get();

		// Process the data to match what was in the DataTables response
		$processedOffers = [];
		foreach($offers as $offer) {
			$package = TourPackage::find($offer->hotelOffer->package_id ?? null);
			$tour = Tour::find($offer->hotelOffer->tour_id ?? null);
			$city = null;

			if($package && $package->service()) {
				$city = City::find($package->service()->city ?? null);
			}

			$stay_date = "";
			if($package && $package->time_from) {
				$stay_date = Carbon::parse($package->time_from)->toDateString();
			}

			$processedOffers[] = (object)[
				'id' => $offer->id,
				'hotel_name' => $package->name ?? '',
				'city_name' => $city->name ?? '',
				'status' => $offer->hotelOffer->status ?? '',
				'stay_date' => $stay_date,
				'offer_date' => $offer->hotelOffer->created_at ?? '',
				'option_date' => $offer->hotelOffer->option_date ?? '',
				'tour_name' => $tour->name ?? '',
				'cancel_policy' => $offer->cancellation_days.' days before arrival: '.$offer->cancellation_percentage.$offer->cancellation_type.' can be cancelled free of charge.',
				'SIN' => '', // Room type data - would need more processing
				'DOU' => '',
				'TRI' => '',
			];
		}

		return view("offers.cancellation_policies", compact('room_types','tours','processedOffers'));
	}
	public function current_offers(){
		$room_types = RoomTypes::all();
		 $currentDate = Carbon::now(); // Get the current date and time
		$oneWeekLater = $currentDate->copy()->addWeek(); // Add one week to the current date
		$tours = Tour::with(['status', 'city_begin', 'city_end'])
			->where('status', '<>', 46)
			->where('retirement_date', '>=', $oneWeekLater)
			->get();
		return view("offers.current_offers",compact('room_types','tours'));
	}
	public function past_offers(){
		$room_types = RoomTypes::all();
		$currentDate = Carbon::now(); // Get the current date and time
		$oneWeekLater = $currentDate->copy()->addWeek(); // Add one week to the current date
		$tours = Tour::with(['status', 'city_begin', 'city_end'])
			->where('status', '=', 46)
			->orWhere('retirement_date', '<', $oneWeekLater)
			->get();

		return view("offers.past_offers",compact('room_types','tours'));
	}
	public function current_bookings(){
		$room_types = RoomTypes::all();

		// Fetch the data directly instead of using DataTables
		$desiredStatuses = [23];
		$tour_packages = TourPackage::whereIn('status', $desiredStatuses)->get();

		// Process the data
		$processedBookings = [];
		foreach($tour_packages as $package) {
			$city = null;
			if($package->service()) {
				$city = City::find($package->service()->city ?? null);
			}

			$stay_date = "";
			if($package->time_from) {
				$stay_date = Carbon::parse($package->time_from)->toDateString();
			}

			$processedBookings[] = (object)[
				'id' => $package->id,
				'hotel_name' => $package->name ?? '',
				'city_name' => $city->name ?? '',
				'stay_date' => $stay_date,
				'tour_name' => $package->getTour()->name ?? '',
				'status_name' => $package->getStatusName() ?? '',
				'cancel_policy' => $package->latestHotelOffer->cancellation_policiy ?? 'N/A',
				'payment_policy' => $package->latestHotelOffer->payment_policiy ?? 'N/A',
			];
		}

		return view("offers.current_bookings", compact('room_types', 'processedBookings'));
	}
	public function getButton($offer,array $perm)
	{
		$url = array(
			'show'       => route('taxes.show', ['id' => $offer->id]),
			'edit'       => route('taxes.edit', ['id' => $offer->id]),
			'delete_msg' => "/offer/{$offer->id}/deleteMsg",
			'id'         => $offer->id
		);
					$package = TourPackage::find($offer->package_id);
			$tour_id = "";
			if(!empty($package)){
				$tour_id = $package->getTour()->id??"";
			}
			$stay_date = "";

			if (!empty($package) && !empty($package->time_from)) {
				$timeFrom = Carbon::parse($package->time_from);
				$stay_date = $timeFrom->toDateString();
			}
		$tour = $package->getTour();
		$status_name = '';
		$menu = '';
		
		$btn = "<button class='btn btn-success btn-sm change-tour-button' data-toggle='modal' data-id='{$url['id']}' data-tour='{$tour_id}' data-target='#tour-clone-modal' ><i class='fa fa-plus'></i></button>";
		
		return  '<div style="width:150px; text-align: center;"><a class="delete btn btn-danger btn-sm" style="margin-right: 5px" data-toggle="modal" data-target="#myModal" data-link="' . $url["delete_msg"] . '"><i class="fa fa-trash-o"></i></a><a class="btn btn-warning btn-sm show-button" href="https://dev.eetstravel.com/offer/'.$offer->id.'/show" data-link="https://dev.eetstravel.com/offer/'.$offer->id.'/show"><i class="fa fa-info-circle"></i></a>'.$btn.'</div>';
		//        return DatatablesHelperController::getActionButton($url, $isQuotation, $tour);
	}
	public function getShowButton($offer,array $perm)
	{
		$url = array(
			'show'       => route('taxes.show', ['id' => $offer->id]),
			'edit'       => route('taxes.edit', ['id' => $offer->id]),
			'delete_msg' => "/offer/{$offer->id}/deleteMsg",
			'id'         => $offer->id
		);
					$package = TourPackage::find($offer->package_id);
			$tour_id = "";
			if(!empty($package)){
				$tour_id = $package->getTour()->id??"";
			}
			$stay_date = "";

			if (!empty($package) && !empty($package->time_from)) {
				$timeFrom = Carbon::parse($package->time_from);
				$stay_date = $timeFrom->toDateString();
			}
		$tour = $package->getTour();
		$status_name = '';
		$menu = '';
		
		$btn = "";
		
		return  '<div style="width:150px; text-align: center;"><a class="delete btn btn-danger btn-sm" style="margin-right: 5px" data-toggle="modal" data-target="#myModal" data-link="' . $url["delete_msg"] . '"><i class="fa fa-trash-o"></i></a><a class="btn btn-warning btn-sm show-button" href="https://dev.eetstravel.com/offer/'.$offer->id.'/show" data-link="https://dev.eetstravel.com/offer/'.$offer->id.'/show"><i class="fa fa-info-circle"></i></a>'.$btn.'</div>';
		//        return DatatablesHelperController::getActionButton($url, $isQuotation, $tour);
	}
	
	public function cancellation_policies_data(Request $request)
	{
		$sevenDaysAgo = Carbon::now()->subDays(7);
		$roomTypes = RoomTypes::all();
	
	
		$desiredStatuses = ["Offered with Option", "Offered No rooms blocked"];
		if($request->offer_id){
			$offers = OfferCancellationPolicies::with('hotelOffer')
			->where('offer_id', $request->offer_id)
			->get();
		}
		else{
		$offers = OfferCancellationPolicies::with('hotelOffer')->get();
		}
		$permission_destroy = PermissionHelper::$relationsPermissionDestroy['App\Invoices'];
		$permission_edit = PermissionHelper::$relationsPermissionEdit['App\Invoices'];
		$permission_show = PermissionHelper::$relationsPermissionShow['App\Invoices'];

		$perm = [];
		$perm['show'] = Auth::user()->can($permission_show);
		$perm['edit'] = Auth::user()->can($permission_edit);
		$perm['destroy'] = Auth::user()->can($permission_destroy);
		$perm['clone'] = Auth::user()->can('accounting.create');

		$dataTable = Datatables::of($offers);


		$dataTable->addColumn('hotel_name', function ($offers) use ($perm) {
			$package = TourPackage::find($offers->hotelOffer->package_id);
			if(empty($package)){
				return "";
				//$offers->delete();
			}
			return $package->name??"";
        });	
		$dataTable->addColumn('city', function ($offers) use ($perm) {
			$package = TourPackage::find($offers->hotelOffer->package_id);
			if(empty($package)){
				return "";
			}
			$city_id =  $package->service()->city??"";
			$city = City::find($city_id);
			return $city->name??"";
        });
		$dataTable->addColumn('stay_date', function ($offers) use ($perm) {
			$package = TourPackage::find($offers->hotelOffer->package_id);
			$stay_date = "";

			if (!empty($package) && !empty($package->time_from)) {
				$timeFrom = Carbon::parse($package->time_from);
				$stay_date = $timeFrom->toDateString();
			}

			return $stay_date;
		});
		$dataTable->addColumn('room_types', function ($offers) use ($perm) {
			return "";
			$hotel = Hotel::find($offers->hotelOffer->package_id);
            return $hotel->city;
        });
		$dataTable->addColumn('offer_price', function ($offers) use ($perm) {
			return "";
			$hotel = Hotel::find($offers->hotelOffer->package_id);
            return $hotel->city;
        });
			
        
		foreach ($roomTypes as $roomType) {
			$columnName = $roomType->code;

			$dataTable->addColumn($columnName, function ($offer) use ($roomType) {
				return "";
				foreach ($offers->hotelOffer->offer_room_prices as $offerRoomPrice) {
					if ($offerRoomPrice->room_type_id == $roomType->id) {
						return $offerRoomPrice->price;
					}
				}
				return "N/A";
			});
		}

		$dataTable->addColumn('tour_name', function ($offers) use ($perm) {
			$tour = Tour::find($offers->hotelOffer->tour_id);
			$tour_name = "";
			if(!empty($tour)){
				$tour_name = $tour->name??"";
			}
            return $tour_name;
        });
		$dataTable->addColumn('cancel_policy', function ($offers) {
               
			$policy_content = "N/A";
			if(isset($offers)){
			$policy_content = $offers->cancellation_days.' days before arrival: '.$offers->cancellation_percentage. $offers->cancellation_type.' can be cancelled free of charge. ';
			}
			return $policy_content;
		});
		$dataTable ->addColumn('status', function ($offers) use ($perm) {
			return $offers->hotelOffer->status;
        });	
		$dataTable ->addColumn('status_tms', function ($offers) use ($perm) {
			return $offers->getStatusName??"";
        });	
		$dataTable ->addColumn('option_date', function ($offers) use ($perm) {
			return $offers->hotelOffer->option_date;
        });
       $dataTable ->addColumn('action', function ($offers) use ($perm) {
			return "";
            return $this->getShowButton($offers->hotelOffer(),$perm);
        });
		return	$dataTable->rawColumns(['select', 'action', 'link'])
			->make(true);
	}
	public function recent_offers_data(Request $request)
{
    $sevenDaysAgo = Carbon::now()->subDays(7);
    $desiredStatuses = ["Offered with Option", "Offered No rooms blocked"];

    // 🚫 DO NOT USE ->get() → pass Query Builder to DataTables
    $query = HotelOffers::with([
        'package',           // assuming relation: package()
        'package.service',   // assuming relation: service()
        'tour',              // assuming relation: tour()
        'offer_room_prices'  // eager load to avoid N+1 in addColumn
    ])
    ->whereIn('status', $desiredStatuses)
    ->where('created_at', '>=', $sevenDaysAgo);

    $permission_destroy = PermissionHelper::$relationsPermissionDestroy['App\Invoices'] ?? '';
    $permission_edit = PermissionHelper::$relationsPermissionEdit['App\Invoices'] ?? '';
    $permission_show = PermissionHelper::$relationsPermissionShow['App\Invoices'] ?? '';

    $perm = [
        'show' => Auth::user()->can($permission_show),
        'edit' => Auth::user()->can($permission_edit),
        'destroy' => Auth::user()->can($permission_destroy),
        'clone' => Auth::user()->can('accounting.create'),
    ];

    return Datatables::of($query)
        ->addColumn('hotel_name', function ($offer) {
            $package = $offer->package; // Use eager loaded relation
            return $package ? $package->name : "N/A";
        })
        ->addColumn('city', function ($offer) {
            $package = $offer->package;
            $cityId = $package?->service?->city ?? null;
            $city = $cityId ? City::find($cityId) : null;
            return $city ? $city->name : "N/A";
        })
        ->addColumn('stay_date', function ($offer) {
            $package = $offer->package;
            if ($package && $package->time_from) {
                return Carbon::parse($package->time_from)->toDateString();
            }
            return "N/A";
        })
        // ✅ Add missing columns expected by frontend
        ->addColumn('offer_date', function ($offer) {
            return $offer->offer_date ? Carbon::parse($offer->offer_date)->toDateString() : "N/A";
        })
        ->addColumn('option_date', function ($offer) {
            return $offer->option_date ? Carbon::parse($offer->option_date)->toDateString() : "N/A";
        })
        ->addColumn('sin', function ($offer) {
            foreach ($offer->offer_room_prices as $price) {
                if ($price->room_type_id == 1) return $price->price;
            }
            return "N/A";
        })
        ->addColumn('dou', function ($offer) {
            foreach ($offer->offer_room_prices as $price) {
                if ($price->room_type_id == 2) return $price->price;
            }
            return "N/A";
        })
        ->addColumn('tri', function ($offer) {
            foreach ($offer->offer_room_prices as $price) {
                if ($price->room_type_id == 3) return $price->price;
            }
            return "N/A";
        })
        ->addColumn('dfs', function ($offer) {
            foreach ($offer->offer_room_prices as $price) {
                if ($price->room_type_id == 4) return $price->price;
            }
            return "N/A";
        })
        ->addColumn('sue', function ($offer) {
            foreach ($offer->offer_room_prices as $price) {
                if ($price->room_type_id == 5) return $price->price;
            }
            return "N/A";
        })
        ->addColumn('twn', function ($offer) {
            foreach ($offer->offer_room_prices as $price) {
                if ($price->room_type_id == 6) return $price->price;
            }
            return "N/A";
        })
        ->addColumn('jsu', function ($offer) {
            foreach ($offer->offer_room_prices as $price) {
                if ($price->room_type_id == 7) return $price->price;
            }
            return "N/A";
        })
        ->addColumn('tour_name', function ($offer) {
            $tour = $offer->tour;
            return $tour ? $tour->name : "N/A";
        })
        ->addColumn('action', function ($offer) use ($perm) {
            $perm["offer"] = "1";
            return $this->getButton($offer, $perm);
        })
        ->rawColumns(['action'])
        ->make(true); // ✅ Must be true for server-side
}
	
	public function past_offers_data(Request $request)
	{
		
		$sevenDaysAgo = Carbon::now()->subDays(7);
		$room_types = RoomTypes::all();
	
	
		$desiredStatuses = ["Offered with Option", "Offered No rooms blocked"];
		$offers = HotelOffers::whereIn('status', $desiredStatuses)
    ->where('created_at', '<', $sevenDaysAgo)
    ->get();

		$permission_destroy = PermissionHelper::$relationsPermissionDestroy['App\Invoices'];
		$permission_edit = PermissionHelper::$relationsPermissionEdit['App\Invoices'];
		$permission_show = PermissionHelper::$relationsPermissionShow['App\Invoices'];

		$perm = [];
		$perm['show'] = Auth::user()->can($permission_show);
		$perm['edit'] = Auth::user()->can($permission_edit);
		$perm['destroy'] = Auth::user()->can($permission_destroy);
		$perm['clone'] = Auth::user()->can('accounting.create');

		return Datatables::of($offers)


		->addColumn('hotel_name', function ($offers) use ($perm) {
			$package = TourPackage::find($offers->package_id);
			if(empty($package)){
				$offers->delete();
			}
			return $package->name??"";
        })	
		->addColumn('city', function ($offers) use ($perm) {
			$package = TourPackage::find($offers->package_id);
			$city_id =  $package->service()->city??"";
			$city = City::find($city_id);
			return $city->name??$city_id??"";
        })
		->addColumn('stay_date', function ($offers) use ($perm) {
			$package = TourPackage::find($offers->package_id);
			$stay_date = "";

			if (!empty($package) && !empty($package->time_from)) {
				$timeFrom = Carbon::parse($package->time_from);
				$stay_date = $timeFrom->toDateString();
			}

			return $stay_date;
		})
		->addColumn('room_types', function ($offers) use ($perm) {
			$hotel = Hotel::find($offers->package_id);
            return $hotel->city??"";
        })
		->addColumn('offer_price', function ($offers) use ($perm) {
			$hotel = Hotel::find($offers->package_id);
            return $hotel->city??"";
        })
		  ->addColumn('sin', function ($offer) use ($perm,$room_types) {
			$package = TourPackage::find($offer->package_id);
		
			
  			$total = 0;
            foreach ($offer->offer_room_prices as $offer_room_price) {
				
                if ($offer_room_price->room_type_id == 1) {
                    $total = $offer_room_price->price+$total;
                }
				
            }
			
			return "N/A";
             // Return 0 if no matching room type is found
 
        })
		->addColumn('sin', function ($offers) use ($perm,$room_types) {
  
            foreach ($offers->offer_room_prices as $offer_room_price) {
                if ($offer_room_price->room_type_id == 1) {
                    return $offer_room_price->price;
                }
            }
            return "N/A"; // Return 0 if no matching room type is found
 
        })
		->addColumn('dou', function ($offers) use ($perm,$room_types) {
			  
			foreach ($offers->offer_room_prices as $offer_room_price) {
				if ($offer_room_price->room_type_id == 2) {
					return $offer_room_price->price;
				}
			}
			return "N/A"; // Return 0 if no matching room type is found
			  
        })
		->addColumn('tri', function ($offers) use ($perm,$room_types) {
			  
			foreach ($offers->offer_room_prices as $offer_room_price) {
				if ($offer_room_price->room_type_id == 3) {
					return $offer_room_price->price;
				}
			}
			return "N/A"; // Return 0 if no matching room type is found
			  
        })
		->addColumn('dfs', function ($offers) use ($perm,$room_types) {
			  
			foreach ($offers->offer_room_prices as $offer_room_price) {
				if ($offer_room_price->room_type_id == 4) {
					return $offer_room_price->price;
				}
			}
			return "N/A"; // Return 0 if no matching room type is found
			  
        })
		->addColumn('sue', function ($offers) use ($perm,$room_types) {
			  
			foreach ($offers->offer_room_prices as $offer_room_price) {
				if ($offer_room_price->room_type_id == 5) {
					return $offer_room_price->price;
				}
			}
			return "N/A"; // Return 0 if no matching room type is found
			  
        })
    	->addColumn('twn', function ($offers) use ($perm,$room_types) {
			 foreach ($offers->offer_room_prices as $offer_room_price) {
							if ($offer_room_price->room_type_id == 6) {
								return $offer_room_price->price;
							}
						}
						return "N/A"; // Return 0 if no matching room type is found
        })

			->addColumn('jsu', function ($offers) use ($perm,$room_types) {
			  
						foreach ($offers->offer_room_prices as $offer_room_price) {
							if ($offer_room_price->room_type_id == 7) {
								return $offer_room_price->price;
							}
						}
						return "N/A"; // Return 0 if no matching room type is found
			  
        })
		->addColumn('tour_name', function ($offers) use ($perm) {
			$tour = Tour::find($offers->tour_id);
			$tour_name = "";
			if(!empty($tour)){
				$tour_name = $tour->name??"";
			}
            return $tour_name;
        })
			
        ->addColumn('action', function ($offers) use ($perm) {

            return $this->getShowButton($offers, $perm);
        })
			->rawColumns(['select', 'action', 'link'])
			->make(true);
	}
	
	public function current_bookings_data(Request $request)
	{
	
		$room_types = RoomTypes::all();
	
	
		$desiredStatuses = [23];
		$tour_package = TourPackage::whereIn('status', $desiredStatuses)
    ->get();

		$permission_destroy = PermissionHelper::$relationsPermissionDestroy['App\Invoices'];
		$permission_edit = PermissionHelper::$relationsPermissionEdit['App\Invoices'];
		$permission_show = PermissionHelper::$relationsPermissionShow['App\Invoices'];

		$perm = [];
		$perm['show'] = Auth::user()->can($permission_show);
		$perm['edit'] = Auth::user()->can($permission_edit);
		$perm['destroy'] = Auth::user()->can($permission_destroy);
		$perm['clone'] = Auth::user()->can('accounting.create');

		return Datatables::of($tour_package)


		->addColumn('hotel_name', function ($tour_package) use ($perm) {
            return $tour_package->name??"";
        })	
		->addColumn('city', function ($tour_package) use ($perm) {
			
			if(isset($tour_package)){
			$city_id =  $tour_package->service()->city??"";
			$city = City::find($city_id);
			return $city->name??"";
			}
        })
		->addColumn('stay_date', function ($tour_package) use ($perm) {
			$stay_date = "";

			if (!empty($tour_package) && !empty($tour_package->time_from)) {
				$timeFrom = Carbon::parse($tour_package->time_from);
				$stay_date = $timeFrom->toDateString();
			}

			return $stay_date;
		})
		->addColumn('tour_name', function ($tour_package) use ($perm) {

            return $tour_package->getTour()->name??"";
        })
		->addColumn('status_name', function ($tour_package) {
               
			$status = $tour_package->getStatusName()??"";

			return $status;
		})
		->addColumn('cancel_policy', function ($tour_package) {
               
			$policy = $tour_package->latestHotelOffer->cancellation_policiy??"";
			$policy_content = "N/A";
			if(!isset($policy)){
			$policy_content = $policy->cancellation_days??"".' days before arrival: '.$policy->cancellation_percentage??"". $policy->cancellation_type??"".' can be cancelled free of charge. ';
			}
			return $policy_content;
		})
			->addColumn('payment_policy', function ($tour_package) {
			$policy = $tour_package->latestHotelOffer->payment_policiy??"";
			$policy_content = "N/A";	
            if(!isset($policy)){  
			$policy_content = $policy->deposit_percentage .' ' .$policy->deposit_type . ' can be paid before '. $policy->deposit_days . ' days before arrival ';
			}
			return $policy_content;
		})
	
			
        
			->rawColumns(['select', 'action', 'link'])
			->make(true);
	}
	
	public function hotel_offers($id, Request $request){
        $title = 'Show - our Package';

        if ($request->ajax()) {
            return URL::to('tour_package/'.$id);
        }

        $tour_package = TourPackage::findOrfail($id);
		$tp = $tour_package;
		 $room_typesDvo = RoomTypes::query()->orderBy('sort_order', 'ASC')->get();
		$selected_room_types = array();
		
		if($tp){
				
		if($tp->type == 0){
                $room_types = array();
                foreach ($room_typesDvo as &$room_type){
                    $room_type['count_room'] = $this->tourPackageRepository->getHotelRoomTypeCount($tp, $room_type->id);
                    $room_type['price_room'] = $this->tourPackageRepository->getHotelRoomTypePrice($tp, $room_type->id);
                }
                $room_types = $room_typesDvo;
                $selected_room_types = array();

                foreach($tp->room_types_hotel as $item){
                    $item->room_types['count_room'] = $item->count;
                    $item->room_types['price_room'] = $item->price;
                    $selected_room_types[] = $item->room_types;
                }
            }
		}
		

        // Get offers data (similar to BookingRequestController@data method)
        $offers = HotelOffers::where('package_id', $id)->get();

        // Process offers data to include room prices and other info
        $offersData = $offers->map(function ($offer) {
            // Add room prices for each room type
            $roomPrices = [];
            foreach (RoomTypes::all() as $roomType) {
                $price = "N/A";
                foreach ($offer->offer_room_prices as $offerRoomPrice) {
                    if ($offerRoomPrice->room_type_id == $roomType->id) {
                        $price = $offerRoomPrice->price;
                        break;
                    }
                }
                $roomPrices[$roomType->code] = $price;
            }
            $offer->room_prices = $roomPrices;

            // Add status name
            $offer->status_tms_name = $offer->getStatusName($offer->tms_status);

            // Add action buttons (similar to getShowButton method)
            $package = TourPackage::find($offer->package_id);
            $tour_id = $package->getTour()->id ?? "";
            $tour = $package->getTour();

            $button = '';
            if($offer->supplier_delete == 0) {
                $button = '<button class="delete btn btn-danger btn-sm" style="margin-right: 5px" data-toggle="modal" data-target="#myModal" data-link="/offer/'.$offer->id.'/supplier_delete"><i class="fa fa-trash-o"></i></button>';
            } else {
                $button = '<button class="delete btn btn-danger btn-sm" style="margin-right: 5px;" data-info="' . htmlspecialchars(json_encode($package ?: ' ')) . '"
                    onclick="loadTemplate(JSON.parse((this.getAttribute(\'data-info\')) ? JSON.parse((this.getAttribute(\'data-info\'))).type : \'\'), \'' . htmlspecialchars($package->service()->work_email) . '\', \'' . htmlspecialchars($package->name) . '\', \'' . htmlspecialchars($package->pax . ' ' . $package->pax_free) . '\', \'\', \'' . htmlspecialchars($package->service()->work_email) . '\', \'' . htmlspecialchars($package->service()->work_phone) . '\', \'' . htmlspecialchars($package->description) . '\', \'\', \'' . htmlspecialchars($package->time_from) . '\', \'' . htmlspecialchars($package->time_to) . '\', \'' . htmlspecialchars($package->supplier_url) . '\', \'' . htmlspecialchars($package->total_amount) . '\', \'\', \'' . htmlspecialchars($tour->id) . '\', \'' . htmlspecialchars($package->reference) . '\', \'' . htmlspecialchars($tour->name) . '\', \'' . htmlspecialchars($package->id) . '\', \'' . htmlspecialchars($offer->id) . '\');"
                    class="btn btn-success btn-xs"
                ><i class="fa fa-envelope" aria-hidden="true"></i></button>';

                $button .= '<a class="btn btn-warning btn-sm show-button" href="https://dev.eetstravel.com/offer/'.$offer->id.'/show" data-link="https://dev.eetstravel.com/tour/5"><i class="fa fa-info-circle"></i></a>';
            }
            $offer->action_buttons = $button;

            return $offer;
        });

        return view('tour_package.offers.index', compact('title', 'tour_package','selected_room_types', 'offersData'));
    }
	public function setConnectionToServer()
	{
		try {

			$this->server = new ImapClient(
				env('IMAP_HOST', 'webmail.eetstravel.com'),
				"service@eetstravel.com",
				"b$5i0e5Y9",
				ImapClient::ENCRYPT_SSL
			);


			return $this->server->isConnected();
		} catch (AuthenticationFailedException $e) {
			echo "Authorization failed : \r\n";
		}
	}
	public function getEmails($package_id){
     

       $folder = "INBOX";
		  $page = 10;
		  $perPage = 100000;

        // dd( self::$folder);
		  $this->setConnectionToServer();
		 
        $this ->server->selectFolder($folder);
	
  try{     
		  
		   $results = $this ->server->getMessagesByCriteria('test',$perPage,$page, 'DESC');
		 
$results = $this ->server->getMessages($perPage,$page, 'DESC');
		  
		
		 $emails = [];
		  foreach($results as $result){
			
			  //$uniqueIdentifier = $result->getHeader('X-Unique-Identifier');
			 $string =$result->header->subject??"";
			  //$pattern = '/Supplier:\s*(.*?)\r\n/';
			 // $pattern = '/package_id\s*:(\d+)/';
			  $pattern = '/Request #(\d+)/';
			  if (preg_match($pattern, $string, $matches)) {
				 
    				$supplierValue = $matches[1];
				  if($supplierValue == $package_id){
					    array_push($emails,$result);
					 
				  }
		  
		  }
		
		  }
		 

//        $this ->server ->close();
        return $emails;
  		}
		 catch (\Exception $e) {
            return $e;
        }
	  }
	
	public function tmsEmails($package_id)
	{


		$folder = "INBOX.Sent";
		$page = 10;
		$perPage = 100000;

		// dd( self::$folder);
		$this->setConnectionToServer();

		$this->server->selectFolder($folder);

		try {

			$results = $this->server->getMessages($perPage, $page, 'DESC');

			$emails = [];
			foreach ($results as $result) {

				$string =$result->header->subject??"";
				  //$pattern = '/Supplier:\s*(.*?)\r\n/';
				 // $pattern = '/package_id\s*:(\d+)/';
				 $pattern = '/Request #(\d+)/';
				
				if (preg_match($pattern, $string, $matches)) {

					$supplierValue = $matches[1];
		
					if ($supplierValue == $package_id) {
						array_push($emails, $result);
					}
				}
			}


			//        $this ->server ->close();
			
			return $emails;
		} catch (\Exception $e) {
			return $e;
		}
	}
	public function offer_emails($id){
		$tour_package = TourPackage::find($id);

		$emails = $this->getEmails($id);
		$tms_emails = $this->tmsEmails($id);
		
		$user = Auth::user();
		return view("tour_package.offers.offer_emails",compact('emails','tms_emails','user','tour_package'));
	}
	public function show($id){
		$offer = HotelOffers::find($id);
		$package = TourPackage::find($offer->package_id);
		$tour = Tour::find($offer->tour_id);
		$user = Auth::user();
		$room_types = RoomTypes::all();
		$city_id =  $package->service()->city??"";
			$city = City::find($city_id);
		$stay_date = "";

			if (!empty($package) && !empty($package->time_from)) {
				$timeFrom = Carbon::parse($package->time_from);
				$stay_date = $timeFrom->toDateString();
			}
		$selected_room_types = array();
		$room_typesDvo = RoomTypes::query()->orderBy('sort_order', 'ASC')->get();
		if($package){
				
		if($package->type == 0){
                $room_types = array();
                foreach ($room_typesDvo as &$room_type){
                    $room_type['count_room'] = $this->tourPackageRepository->getHotelRoomTypeCount($package, $room_type->id);
                    $room_type['price_room'] = $this->tourPackageRepository->getHotelRoomTypePrice($package, $room_type->id);
                }
                $room_types = $room_typesDvo;
                $selected_room_types = array();

                foreach($package->room_types_hotel as $item){
                    $item->room_types['count_room'] = $item->count;
                    $item->room_types['price_room'] = $item->price;
                    $selected_room_types[] = $item->room_types;
                }
            }
		}
		return view("offers.show",compact('offer','room_types','package','tour','city','stay_date','selected_room_types'));
	}
	public function create($id){
		
		$tour_package = TourPackage::find($id);

		$currencies = Currencies::all();

		$serviceTypes = $this->serviceTypes;

        (TourService::$serviceTypes[$tour_package->type] === 'hotel') ?
            $statuses = Status::query()->where('type', 'hotel')->orderBy('sort_order')->get() :
            $statuses = Status::query()->where('type', 'service_in_tour')->orderBy('sort_order')->get();

        if (TourService::$serviceTypes[$tour_package->type] === 'transfer') $statuses = Status::query()->orderBy('sort_order', 'asc')->where('type', 'bus')->get();

		$tp = $tour_package;
		 $room_typesDvo = RoomTypes::query()->orderBy('sort_order', 'ASC')->get();
		$selected_room_types = array();
		
		if($tp){
				
		if($tp->type == 0){
                $room_types = array();
                foreach ($room_typesDvo as &$room_type){
                    $room_type['count_room'] = $this->tourPackageRepository->getHotelRoomTypeCount($tp, $room_type->id);
                    $room_type['price_room'] = $this->tourPackageRepository->getHotelRoomTypePrice($tp, $room_type->id);
                }
                $room_types = $room_typesDvo;
                $selected_room_types = array();

                foreach($tp->room_types_hotel as $item){
                    $item->room_types['count_room'] = $item->count;
                    $item->room_types['price_room'] = $item->price;
                    $selected_room_types[] = $item->room_types;
                }
            }
	
			
	
	
		}
		
		$comments = DB::select('select * from supplier_comments');
		

		return view("tour_package.offers.create",compact('tour_package','selected_room_types','currencies','comments','statuses'));
	}
	
	    public function DeleteMsg($id, Request $request)
    {
        $msg = Ajaxis::BtDeleting('Warning!!', 'Would you like to remove This?', '/offer/' . $id . '/delete');

        if ($request->ajax()) {
            return $msg;
        }
    }
    public function destroy($id)
    {

        $offers = HotelOffers::find($id);
        $offers->find($id)->delete();
        LaravelFlashSessionHelper::setFlashMessage("Offer $offers->id deleted", 'success');

        return URL::to('offer');
    }
	public function assign_to_tour(Request $request,$id){
		if(!empty($request->tourDayId)){
		$offer = HotelOffers::find($id);
		$originalTp  = TourPackage::find($offer->package_id);
		$clonedTp = $originalTp->replicate();
		$latestId = TourPackage::withTrashed()->latest()->pluck('id')->first();
		$latestId = $latestId +1;
		
		$offer->tour_id =  $request->tour_id;
		$offer->package_id =  $latestId;
		$offer->save();
		$clonedTp->save();
		$latestId = Crypt::encryptString($latestId );
		$tourDay = TourDay::query()->get()->where('id', $request->tourDayId)->first();
		$clonedTp->assignTourDay($tourDay);
		
		$clonedTp->supplier_url = "https://dev.eetstravel.com/booking/".$latestId; 
		$clonedTp->save();
		
		$tour = Tour::find($request->tour_id);
		
        LaravelFlashSessionHelper::setFlashMessage("Offee {$offer->id} assign to tour {$tour->name}", 'success');
		}
		else{
			LaravelFlashSessionHelper::setFlashMessage("Offee cannot assign", 'error');
		}
        return redirect(route('offer.index'));
	}
	

	public function supplier_delete(Request $request,$id){
		
		 $offers = HotelOffers::find($id);
		$offers->supplier_delete = 1;
		$offers->save();
		
        return response()->json(['success' => true, 'message' => 'Record deleted successfully.']);
		
	}
	
	 public function statusList(Request $request)
    {
        return response()->json(Status::select('name', 'id')->orderBy('sort_order', 'asc')->where('type', 'offer')->get()->toJson());
    }
	 public function updatestatus($id,Request $request)
    {
		$offers = HotelOffers::find($id);
		 $package = TourPackage::find($offers->package_id);
		 $tour = $package->getTour();
		$offers->tms_status = $request->fieldValue;
		$offers->save();
			$i = 0;
		
			if(!empty($offers->cancellation_policies ) && $request->fieldValue == 56){
			foreach ($offers->cancellation_policies as $cancellation) {
				
				try {
					
					$assigned_user = [];
					$task = new Task();
					//  $task->content = 'Confirm reservation of '.$tour_package->name.' before '. $request->date;
					$task->content = $cancellation->cancellation_days . ' days before arrival: '.$cancellation->cancellation_percentage.$cancellation->cancellation_type.' of rooms can be cancelled free of charge of '.$package->name;
					$task->start_time = Carbon::now(); // Set the start date to the current date and time
					$dep_date = Carbon::parse($tour->departure_date);
					
					$task->dead_line = $dep_date->addDays($cancellation->cancellation_days); // Set the deadline date by adding days to the current date

					$task->tour = $tour->id;
					// $task->assign = $user[$request->get('assign')];
					//$task->assign = Auth::user()->id;
					$task->task_type = 1;
					$task->status = 2;
					$task->priority = 0;

					
					if($tour) {
						$assigned_user = array_merge($tour->users->pluck('id')->toArray(), (array)$assigned_user);
					}
					$task->save();
					if ($assigned_user){
						$task->assigned_users()->sync($assigned_user);
						foreach ($assigned_user as $user) {
							$notification = Notification::create(['content' => "New task {$task->content}", 'link' => '/task/'.$task->id]);
							$user = User::findOrFail($user);
							$user->notifications()->attach($notification);
						}
					
						
						
					}

					

					 $i += 1;
				} catch (\Exception $e) {
					dd($e->getMessage());
				}
			}
			}
		
			$deposit_percentage = $request->deposit_percentage;
			$deposit_type = $request->deposit_type;
			$deposit_days = $request->deposit_days;
			$i = 0;
			if(!empty($offers->payment_policies ) && $request->fieldValue == 56){
			foreach ($offers->payment_policies  as $deposit_day) {
				try {
					
					if( $request->status == 'Offered No rooms blocked'){
					$assigned_user = [];
					$task = new Task();
					//  $task->content = 'Confirm reservation of '.$tour_package->name.' before '. $request->date;
					$task->content =$deposit_day->deposit_percentage. $deposit_day->deposit_type.' can be paid before '.$deposit_day->deposit_days.' days before arrival of '.$package->name;
					$task->start_time = Carbon::now(); // Set the start date to the current date and time
					$dep_date = Carbon::parse($tour->departure_date);
					
					$task->dead_line = $dep_date->addDays($deposit_day); // Set the deadline date by adding days to the current date

					$task->tour = $tour->id;
					// $task->assign = $user[$request->get('assign')];
					//$task->assign = Auth::user()->id;
					$task->task_type = 1;
					$task->status = 2;
					$task->priority = 0;

					
					if($tour) {
						$assigned_user = array_merge($tour->users->pluck('id')->toArray(), (array)$assigned_user);
					}
					$task->save();
					if ($assigned_user){
						$task->assigned_users()->sync($assigned_user);
						foreach ($assigned_user as $user) {
							$notification = Notification::create(['content' => "New task {$task->content}", 'link' => '/task/'.$task->id]);
							$user = User::findOrFail($user);
							$user->notifications()->attach($notification);
						}
					}
						
						
					}
					 $i += 1;
				} catch (\Exception $e) {
					dd($e->getMessage());
				}
			}
			}
        return response()->json(Status::select('name', 'id')->orderBy('sort_order', 'asc')->where('type', 'offer')->get()->toJson());
    }
}
