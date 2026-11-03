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
		private function offerableHotelPackages()
	{
		return TourPackage::with(['tour', 'status'])
			->where('type', 0)
			->orderBy('id', 'desc')
			->limit(100)
			->get();
	}

	public function select_create_package()
	{
		$packages = $this->offerableHotelPackages();
		return view('offers.select_create_package', compact('packages'));
	}
	public function cancellation_policies(Request $request){
		$room_types = RoomTypes::all();
		$search = trim((string) $request->get('q'));
		$today = Carbon::today()->toDateString();

		$query = OfferCancellationPolicies::query()
			->select('offer_cancellation_policies.*')
			// Only policies whose offer and (not deleted) hotel package still exist
			->join('hotel_offers', 'hotel_offers.id', '=', 'offer_cancellation_policies.offer_id')
			->join('tour_packages', function ($join) {
				$join->on('tour_packages.id', '=', 'hotel_offers.package_id')->whereNull('tour_packages.deleted_at');
			})
			// The policy in force is the one on the package's latest offer (an offer can have several tiers)
			->whereIn('hotel_offers.id', function ($q) {
				$q->from('hotel_offers')->selectRaw('max(id)')->groupBy('package_id');
			})
			// Upcoming or ongoing stays only
			->where(function ($q) use ($today) {
				$q->whereDate('tour_packages.time_to', '>=', $today)
					->orWhere(function ($q) use ($today) {
						$q->whereNull('tour_packages.time_to')->whereDate('tour_packages.time_from', '>=', $today);
					});
			})
			->when($search !== '', function ($q) use ($search) {
				$like = '%' . addcslashes($search, '%_\\') . '%';
				$tourIds = Tour::withTrashed()->where('name', 'like', $like)->select('id');
				$q->where(function ($q) use ($like, $search, $tourIds) {
					$q->where('tour_packages.name', 'like', $like)
						->orWhereIn('hotel_offers.tour_id', $tourIds)
						->orWhereIn('tour_packages.tour_id', $tourIds);
					if (ctype_digit($search)) {
						$q->orWhere('offer_cancellation_policies.id', (int) $search);
					}
				});
			})
			->orderBy('tour_packages.time_from')
			->orderBy('tour_packages.id')
			->orderByDesc('offer_cancellation_policies.cancellation_days')
			->with(['hotelOffer.package.tourDays:id,tour', 'hotelOffer.offer_room_prices']);

		if ($request->get('export') === 'csv') {
			return $this->cancellationPoliciesCsv($this->policyRows($query->get()));
		}

		$policies = $query->paginate(25)->withQueryString();
		$processedOffers = $this->policyRows($policies->getCollection());

		return view("offers.cancellation_policies", compact('room_types', 'processedOffers', 'policies', 'search'));
	}

	/**
	 * Display rows for cancellation policies; hotels, cities, tours and currencies are loaded once.
	 */
	private function policyRows($policies)
	{
		$packages = $policies->map(function ($p) { return $p->hotelOffer->package; });
		$hotels = Hotel::whereIn('id', $packages->pluck('reference')->filter()->unique())->pluck('city', 'id');
		$cities = City::whereIn('id', $hotels->filter()->unique())->pluck('name', 'id');

		// Tour: the offer's own tour, else the package's first tour day, else the package's tour_id
		$tourIdFor = function ($policy) {
			$offer = $policy->hotelOffer;
			$day = $offer->package->tourDays->first();
			return $offer->tour_id ?: (($day ? $day->tour : null) ?: $offer->package->tour_id);
		};
		$tours = Tour::withTrashed()->whereIn('id', $policies->map($tourIdFor)->filter()->unique())->get(['id', 'name', 'deleted_at'])->keyBy('id');
		$currencies = Currencies::whereIn('id', $policies->pluck('hotelOffer.currency')->filter()->unique())->pluck('code', 'id');
		$roomTypeIds = RoomTypes::whereIn('code', ['SIN', 'DOU', 'TRI'])->pluck('id', 'code');

		return $policies->map(function ($policy) use ($hotels, $cities, $tours, $currencies, $roomTypeIds, $tourIdFor) {
			$offer = $policy->hotelOffer;
			$package = $offer->package;
			$tourId = $tourIdFor($policy);
			$tour = $tourId ? $tours->get($tourId) : null;
			$currency = $currencies->get($offer->currency);

			// Room price on this offer for a room type, e.g. "120 EUR"
			$price = function ($code) use ($offer, $roomTypeIds, $currency) {
				$typeId = $roomTypeIds->get($code);
				$prices = $typeId ? $offer->offer_room_prices->where('room_type_id', $typeId) : collect();
				return $prices->isEmpty() ? null : trim($prices->sum('price') . ' ' . $currency);
			};

			return (object)[
				'id' => $policy->id,
				'package_id' => $package->id,
				'hotel_name' => $package->name,
				'city_name' => $cities->get($hotels->get($package->reference)),
				'status' => $offer->status,
				'stay_date' => $package->time_from ? Carbon::parse($package->time_from)->toDateString() : null,
				'stay_end' => $package->time_to ? Carbon::parse($package->time_to)->toDateString() : null,
				'offer_date' => $offer->created_at ? Carbon::parse($offer->created_at)->toDateString() : null,
				'option_date' => $offer->option_date ? Carbon::parse($offer->option_date)->toDateString() : null,
				'tour_name' => $tour ? $tour->name . ($tour->trashed() ? ' (deleted)' : '') : null,
				'cancel_policy' => self::cancellationPolicyText($policy, $currency),
				'SIN' => $price('SIN'),
				'DOU' => $price('DOU'),
				'TRI' => $price('TRI'),
			];
		});
	}

	private function cancellationPoliciesCsv($rows)
	{
		$cell = function ($value) {
			$value = (string) $value;
			// Keep spreadsheet apps from treating text as a formula
			return preg_match('/^[=+\-@]/', $value) ? "'" . $value : $value;
		};

		return response()->streamDownload(function () use ($rows, $cell) {
			$out = fopen('php://output', 'w');
			fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM so Excel shows accents correctly
			fputcsv($out, ['ID', 'Policy', 'Hotel Name', 'City', 'Status', 'Check-in', 'Check-out', 'SIN', 'DOU', 'TRI', 'Offer Date', 'Option Date', 'Tour Name']);
			foreach ($rows as $row) {
				fputcsv($out, array_map($cell, [$row->id, $row->cancel_policy, $row->hotel_name, $row->city_name, $row->status,
					$row->stay_date, $row->stay_end, $row->SIN, $row->DOU, $row->TRI, $row->offer_date, $row->option_date, $row->tour_name]));
			}
			fclose($out);
		}, 'cancellation_policies_' . Carbon::now()->format('Y-m-d') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
	}
	/** Requested (46) is not an offer yet; Cancelled (6) and Finished (58) are no longer current */
	const NOT_CURRENT_OFFER_STATUSES = [6, 46, 58];
	/** Closed offers belong in past offers whatever their dates */
	const CLOSED_OFFER_STATUSES = [6, 58];

	/**
	 * Current and past offers are complementary: a tour is past once it has ended or is closed,
	 * and current while it has not ended (or has no dates yet) and is still an open offer.
	 * Only Requested tours that have not ended are in neither; they are listed under Tour > Requested.
	 */
	private function currentOffersScope($query)
	{
		$today = Carbon::today();

		return $query
			->where(function ($q) use ($today) {
				$q->whereDate('retirement_date', '>=', $today)->orWhereNull('retirement_date');
			})
			// SQL "status <> x" is never true for NULL, so tours without a status need orWhereNull
			->where(function ($q) {
				$q->whereNotIn('status', self::NOT_CURRENT_OFFER_STATUSES)->orWhereNull('status');
			});
	}

	private function pastOffersScope($query)
	{
		return $query->where(function ($q) {
			$q->whereDate('retirement_date', '<', Carbon::today())
				->orWhereIn('status', self::CLOSED_OFFER_STATUSES);
		});
	}

	public function current_offers(Request $request){
		return $this->offersListing($request, 'current', 'departure_date', 'asc');
	}

	public function past_offers(Request $request){
		// Most recently finished first
		return $this->offersListing($request, 'past', 'retirement_date', 'desc');
	}

	/**
	 * Shared listing for current/past offers: search, sort and CSV export run on the server,
	 * so they cover every matching offer rather than the visible page.
	 */
	private function offersListing(Request $request, $list, $defaultSort, $defaultDir)
	{
		$room_types = RoomTypes::all();

		$search = trim((string) $request->get('q'));
		$sortable = ['id', 'name', 'departure_date', 'retirement_date'];
		$sort = in_array($request->get('sort'), $sortable, true) ? $request->get('sort') : $defaultSort;
		$dir = in_array($request->get('dir'), ['asc', 'desc'], true) ? $request->get('dir') : $defaultDir;

		$query = Tour::with(['status']);
		$query = $list === 'past' ? $this->pastOffersScope($query) : $this->currentOffersScope($query);
		$query->when($search !== '', function ($q) use ($search) {
				$like = '%' . addcslashes($search, '%_\\') . '%';
				$q->where(function ($q) use ($like, $search) {
					$q->where('name', 'like', $like)->orWhere('external_name', 'like', $like);
					if (ctype_digit($search)) {
						$q->orWhere('id', (int) $search);
					}
				});
			})
			->orderBy($sort, $dir)
			->orderBy('id', $dir);

		// Export every matching offer, not just the visible page, and without the action buttons
		if ($request->get('export') === 'csv') {
			$tours = $query->get();
			return $this->offersCsv($tours, $this->offerCityNames($tours), "{$list}_offers");
		}

		$tours = $query->paginate(25)->withQueryString();
		$cityNames = $this->offerCityNames($tours->getCollection());

		return view("offers.{$list}_offers", compact('room_types', 'tours', 'search', 'sort', 'dir', 'cityNames'));
	}

	/**
	 * Start-city name per tour id. Tour::$city_begin is an accessor that returns the name with two
	 * queries per call, so the raw column (a city id, or a plain name on some older tours) is read here.
	 */
	private function offerCityNames($tours)
	{
		$raw = $tours->mapWithKeys(function ($tour) {
			return [$tour->id => $tour->getRawOriginal('city_begin')];
		});
		$names = City::whereIn('id', $raw->filter(function ($v) { return is_numeric($v); })->unique())->pluck('name', 'id');

		return $raw->map(function ($value) use ($names) {
			if ($value === null || $value === '') return null;
			return is_numeric($value) ? $names->get($value) : $value;
		});
	}

	private function offersCsv($tours, $cityNames, $filePrefix)
	{
		$cell = function ($value) {
			$value = (string) $value;
			// Keep spreadsheet apps from treating text as a formula
			return preg_match('/^[=+\-@]/', $value) ? "'" . $value : $value;
		};
		$date = function ($value, $format = 'Y-m-d') {
			return $value ? Carbon::parse($value)->format($format) : '';
		};

		return response()->streamDownload(function () use ($tours, $cityNames, $cell, $date) {
			$out = fopen('php://output', 'w');
			fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM so Excel shows accents correctly
			fputcsv($out, ['ID', 'Tour Name', 'City', 'Status', 'Departure Date', 'Return Date', 'PAX', 'Created At']);
			foreach ($tours as $tour) {
				fputcsv($out, array_map($cell, [
					$tour->id,
					$tour->name,
					$cityNames->get($tour->id),
					optional($tour->getRelationValue('status'))->name,
					$date($tour->departure_date),
					$date($tour->retirement_date),
					$tour->pax,
					$date($tour->created_at, 'Y-m-d H:i'),
				]));
			}
			fclose($out);
		}, $filePrefix . '_' . Carbon::now()->format('Y-m-d') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
	}
	/** Hotel packages in this status are confirmed bookings */
	const BOOKING_CONFIRMED_STATUS = 23;

	public function current_bookings(Request $request){
		$room_types = RoomTypes::all();
		$search = trim((string) $request->get('q'));
		$today = Carbon::today();

		$query = TourPackage::with(['status', 'tourDays:id,tour', 'latestHotelOffer.cancellation_policiy', 'latestHotelOffer.payment_policiy'])
			->where('status', self::BOOKING_CONFIRMED_STATUS)
			// Current = the stay has not ended yet (check-out today or later)
			->where(function ($q) use ($today) {
				$q->whereDate('time_to', '>=', $today)
					->orWhere(function ($q) use ($today) {
						$q->whereNull('time_to')->whereDate('time_from', '>=', $today);
					});
			})
			->when($search !== '', function ($q) use ($search) {
				$like = '%' . addcslashes($search, '%_\\') . '%';
				$tourIds = Tour::withTrashed()->where('name', 'like', $like)->select('id');
				$q->where(function ($q) use ($like, $search, $tourIds) {
					$q->where('name', 'like', $like)
						->orWhereIn('tour_id', $tourIds)
						->orWhereHas('tourDays', function ($d) use ($tourIds) { $d->whereIn('tour', $tourIds); });
					if (ctype_digit($search)) {
						$q->orWhere('id', (int) $search);
					}
				});
			})
			->orderBy('time_from')
			->orderBy('id');

		// Export every matching booking, without the action buttons
		if ($request->get('export') === 'csv') {
			return $this->currentBookingsCsv($this->bookingRows($query->get()));
		}

		$packages = $query->paginate(25)->withQueryString();
		$processedBookings = $this->bookingRows($packages->getCollection());

		return view("offers.current_bookings", compact('room_types', 'processedBookings', 'packages', 'search'));
	}

	/**
	 * Display rows for confirmed hotel packages, with hotels, cities, tours and currencies loaded once.
	 */
	private function bookingRows($packages)
	{
		$hotels = Hotel::whereIn('id', $packages->pluck('reference')->filter()->unique())->pluck('city', 'id');
		$cities = City::whereIn('id', $hotels->filter()->unique())->pluck('name', 'id');

		// The tour comes from the first tour day, falling back to the package's own tour_id
		$tourIdFor = function ($package) {
			$day = $package->tourDays->first();
			return ($day ? $day->tour : null) ?: $package->tour_id;
		};
		$tours = Tour::withTrashed()->whereIn('id', $packages->map($tourIdFor)->filter()->unique())->get(['id', 'name', 'deleted_at'])->keyBy('id');
		$currencies = Currencies::whereIn('id', $packages->map(function ($p) {
			return optional($p->latestHotelOffer)->currency;
		})->filter()->unique())->pluck('code', 'id');

		return $packages->map(function ($package) use ($hotels, $cities, $tours, $currencies, $tourIdFor) {
			$tourId = $tourIdFor($package);
			$tour = $tourId ? $tours->get($tourId) : null;
			$tourName = $tour ? ($tour->trashed() ? "Deleted tour #{$tour->id}" : $tour->name) : null;

			$offer = $package->latestHotelOffer;
			$currency = $offer ? $currencies->get($offer->currency) : null;

			return (object)[
				'id' => $package->id,
				'hotel_name' => $package->name,
				'city_name' => $cities->get($hotels->get($package->reference)),
				'stay_date' => $package->time_from ? Carbon::parse($package->time_from)->toDateString() : null,
				'stay_end' => $package->time_to ? Carbon::parse($package->time_to)->toDateString() : null,
				'tour_name' => $tourName,
				'status_name' => optional($package->getRelationValue('status'))->name,
				'cancel_policy' => $offer ? self::cancellationPolicyText($offer->cancellation_policiy, $currency) : null,
				'payment_policy' => $offer ? self::paymentPolicyText($offer->payment_policiy, $currency) : null,
				'model' => $package,
			];
		});
	}

	/** "50%" for percentages, "200 EUR" for fixed amounts; unknown units are left off */
	private static function policyAmount($value, $type, $currency)
	{
		$type = strtolower(trim((string) $type));
		if (in_array($type, ['percentage', '%'], true)) {
			return $value . '%';
		}
		if (in_array($type, ['fixed', 'amount'], true)) {
			return trim($value . ' ' . $currency);
		}
		return (string) $value;
	}

	/** "until 3 days before arrival" / "until the day of arrival" */
	private static function beforeArrival($days)
	{
		$days = (int) $days;
		if ($days <= 0) {
			return 'the day of arrival';
		}

		return $days . ' ' . ($days === 1 ? 'day' : 'days') . ' before arrival';
	}

	/** A policy with no amount is no policy: shown as N/A rather than "0 ... 0" */
	public static function cancellationPolicyText($policy, $currency = null)
	{
		if (!$policy || !(float) $policy->cancellation_percentage) {
			return null;
		}
		$amount = self::policyAmount($policy->cancellation_percentage, $policy->cancellation_type, $currency);

		return 'Until ' . self::beforeArrival($policy->cancellation_days) . ", {$amount} can be cancelled free of charge.";
	}

	public static function paymentPolicyText($policy, $currency = null)
	{
		if (!$policy || !(float) $policy->deposit_percentage) {
			return null;
		}
		$amount = self::policyAmount($policy->deposit_percentage, $policy->deposit_type, $currency);
		$days = (int) $policy->deposit_days;

		return $days > 0
			? "{$amount} deposit due " . self::beforeArrival($days) . '.'
			: "{$amount} deposit due on arrival.";
	}

	private function currentBookingsCsv($rows)
	{
		$cell = function ($value) {
			$value = (string) $value;
			// Keep spreadsheet apps from treating text as a formula
			return preg_match('/^[=+\-@]/', $value) ? "'" . $value : $value;
		};

		return response()->streamDownload(function () use ($rows, $cell) {
			$out = fopen('php://output', 'w');
			fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM so Excel shows accents correctly
			fputcsv($out, ['ID', 'Tour', 'Hotel Name', 'City', 'Status', 'Check-in', 'Check-out', 'Cancellation Policy', 'Payment Policy']);
			foreach ($rows as $row) {
				fputcsv($out, array_map($cell, [$row->id, $row->tour_name, $row->hotel_name, $row->city_name, $row->status_name,
					$row->stay_date, $row->stay_end, $row->cancel_policy, $row->payment_policy]));
			}
			fclose($out);
		}, 'current_bookings_' . Carbon::now()->format('Y-m-d') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
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

                $button .= '<a class="btn btn-warning btn-sm show-button" href="'.url('/').'/offer/'.$offer->id.'/show" data-link="'.url('/').'/offer/'.$offer->id.'/show"><i class="fa fa-info-circle"></i></a>';
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
		  
		   // (a leftover search for the invalid IMAP criterion "test" was removed here)
$results = $this->server->getMessagesByCriteria('SUBJECT "Request #' . (int) $package_id . '"', 0, 0, 'DESC'); // the mail server searches; only matching emails are downloaded
		  
		
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

			$results = $this->server->getMessagesByCriteria('SUBJECT "Request #' . (int) $package_id . '"', 0, 0, 'DESC'); // the mail server searches; only matching emails are downloaded

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

		// Mailbox history is optional: the page must still open when IMAP is unavailable
		try { $emails = $this->getEmails($id); } catch (\Throwable $e) { $emails = []; }
		try { $tms_emails = $this->tmsEmails($id); } catch (\Throwable $e) { $tms_emails = []; }
		if (!is_array($emails)) $emails = [];
		if (!is_array($tms_emails)) $tms_emails = [];
		
		$user = Auth::user();
		return view("tour_package.offers.offer_emails",compact('emails','tms_emails','user','tour_package'));
	}
	public function show($id){
		$offer = HotelOffers::findOrFail($id);
		$package = TourPackage::find($offer->package_id);
		$tour = Tour::find($offer->tour_id);
		$policies = OfferCancellationPolicies::where('offer_id', $offer->id)->get();
		$user = Auth::user();
		$room_types = RoomTypes::all();
		$city_id =  optional(optional($package)->service())->city ?? "";
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
		return view("offers.show",compact('offer','room_types','package','tour','city','stay_date','selected_room_types','policies'));
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

		// Save the copy first and use its real id (it used to guess "latest id + 1")
		$clonedTp = $originalTp->replicate();
		$clonedTp->save();
		$tourDay = TourDay::query()->get()->where('id', $request->tourDayId)->first();
		$clonedTp->assignTourDay($tourDay);

		$offer->tour_id =  $request->tour_id;
		$offer->package_id =  $clonedTp->id;
		$offer->save();

		$tour = Tour::find($request->tour_id);

        LaravelFlashSessionHelper::setFlashMessage("Offer {$offer->id} assigned to tour {$tour->name}", 'success');
		}
		else{
			LaravelFlashSessionHelper::setFlashMessage("Offer cannot be assigned: choose a tour day", 'error');
		}
        // offer.index does not exist; the offers overview is current_offers.index
        return redirect(route('current_offers.index'));
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




