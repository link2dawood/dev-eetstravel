<?php

namespace App\Http\Controllers;

use App\Criteria;
use App\GooglePlaces;
use App\Helper\FileTrait;
use App\City;
use App\Country;
use App\Helper\CitiesHelper;
use App\Comment;
use App\Helper\GooglePlacesHelper;
use App\Helper\LaravelFlashSessionHelper;
use App\Helper\PermissionHelper;
use App\Hotel;
use App\HotelContacts;
use App\PricesRoomTypeHotel;
use App\Rate;
use App\Repository\Contracts\HotelRepository;
use App\RoomTypes;
use App\ServicesHasCriteria;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Guide;
use Amranidev\Ajaxis\Ajaxis;
use Illuminate\Support\Facades\View;
use SKAgarwal\GoogleApi\PlacesApi;
use App\Http\Requests\StoreHotelRequest;
use App\Http\Requests\UpdateHotelRequest;
use URL;
use App\TourPackage;
use App\Notification;
use Illuminate\Support\Facades\Crypt;

/**
 * Class HotelsController.
 */
class HotelController extends Controller
{
    use FileTrait;

    protected $hotel;

    public function __construct(HotelRepository $hotel)
    {
        $this->middleware('permissions.required');
        $this->hotel = $hotel;
        $this->middleware('preventBackHistory');
        $this->middleware('auth');
    }

    public function getButton($id)
    {
        $url = array('show'       => route('hotel.show', ['id' => $id]),
                     'edit'       => route('hotel.edit', ['id' => $id]),
                     'delete_msg' => "/hotel/{$id}/deleteMsg");
        return DatatablesHelperController::getActionButton($url);
    }
    
    // Removed data() method - using direct data in index() method instead of DataTables
    // public function data(Request $request)
    // {
    //     return Datatables::of(...)
    // }

    /**
     * Display a listing of the resource.
     *
     * @return  \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $title = 'Index - Hotel';
        $hotels = Hotel::leftJoin('countries', 'countries.alias', '=', 'hotels.country')
            ->leftJoin('cities', 'cities.id', '=', 'hotels.city')
            ->select(
                'hotels.*',
                'cities.name as city_name',
                'countries.name as country_name'
            )
            ->paginate(10);
        return view('hotel.index', compact('hotels', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return  \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'Create - hotel';

        /*this amd route*/
        $criterias = Criteria::leftJoin('criteria_types', 'criterias.criteria_type', '=', 'criteria_types.id')
            ->orderBy('criterias.name','asc')
            ->select('criterias.*', 'criteria_types.name as criteria_name')
            ->where('criteria_types.service_type', '=', 'hotel')
            ->get();
        $rates = Rate::query()->orderBy('sort_order', 'asc')->get();

        $room_types = RoomTypes::query()->orderBy('sort_order', 'ASC')->get();

        /**/
        return view('hotel.create', compact('criterias', 'rates', 'room_types'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param    \Illuminate\Http\Request $request
     * @return  \Illuminate\Http\Response
     */
    public function store(StoreHotelRequest $request)
    {
        $prices_room_type = $request->prices_room_type;


        $this->validate($request, [
            'name' => 'required|string',
            'address_first' => 'required|string'
        ]);

        $request = CitiesHelper::setCityGeneral($request);
        /*this criterias*/
        $hotel = $this->hotel->create($request->except('attach', 'place_id', 'criterias', 'prices_room_type'));
        /*this*/
        if(!empty($request->criterias)){
            $criterias = explode(',', $request->criterias);
            foreach ($criterias as $criteria){
                $services_has_criteria = new ServicesHasCriteria();
                $services_has_criteria->service_id = $hotel->id;
                $services_has_criteria->criteria_id = $criteria;
                $services_has_criteria->service_type = ServicesHasCriteria::$serviceTypes['hotel'];
                $services_has_criteria->save();
            }
        }
        /*this*/


        if(!empty($prices_room_type)){
            foreach ($prices_room_type as $key => $item){
                $obj_price_room_type = new PricesRoomTypeHotel();
                $obj_price_room_type->price = $item == null ? 0 : $item;
                $obj_price_room_type->room_type_id = $key;
                $obj_price_room_type->hotel_id = $hotel->id;
                $obj_price_room_type->save();
            }
        }

        LaravelFlashSessionHelper::setFlashMessage("Hotel $hotel->name created", 'success');


        $this->addFile($request, $hotel);
        $data = ['route' => route('hotel.index')];
        
        $pusher = App::make('pusher');

        //default pusher notification.
        //by default channel=test-channel,event=test-event
        //Here is a pusher notification example when you create a new resource in storage.
        //you can modify anything you want or use it wherever.
        $pusher->trigger(
            'test-channel',
            'test-event',
            [
                'message' => 'A new guide has been created !!'
            ]
        );
		return redirect()->route('hotel.index');
        return response()->json($data);
    }

    /**
     * Display the specified resource.
     *
     * @param    \Illuminate\Http\Request $request
     * @param    int $id
     * @return  \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        $title = 'Show - hotel';

        if ($request->ajax()) {
            return URL::to('hotel/' . $id);
        }

        /*this amd route*/
        $hotel = Hotel::leftJoin('rates', 'rates.id', '=', 'hotels.rate')
            ->select('hotels.*', 'rates.name as rate_name')
            ->where('hotels.id', $id)
            ->first();

        $hotel->getCriterias();
        if($hotel == null){
            return abort(404);
        }

        $criterias = Criteria::leftJoin('criteria_types', 'criterias.criteria_type', '=', 'criteria_types.id')
            ->orderBy('criterias.name','asc')
            ->select('criterias.*', 'criteria_types.name as criteria_name')
            ->where('criteria_types.service_type', '=', 'hotel')
            ->get();


        /*this*/
        $contacts = HotelContacts::query()->where('hotel_id', $id)->get();

        $files = $this->parseAttach($hotel);

        return view('hotel.show', ['title' => $title, 'hotel' => $hotel, 'files' => $files,
            'criterias' => $criterias, 'contacts' => $contacts]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param    \Illuminate\Http\Request $request
     * @param    int $id
     * @return  \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
        $title = 'Edit - hotel';
        if ($request->ajax()) {
            return URL::to('hotel/' . $id . '/edit');
        }

        $hotel = $this->hotel->byId($id);

        /*this and route*/
        $hotel->getCriterias();
        $criterias = Criteria::leftJoin('criteria_types', 'criterias.criteria_type', '=', 'criteria_types.id')
            ->orderBy('criterias.name','asc')
            ->select('criterias.*', 'criteria_types.name as criteria_name')
            ->where('criteria_types.service_type', '=', 'hotel')
            ->get();
        $rates = Rate::query()->orderBy('sort_order', 'asc')->get();
        /*this*/
		if(!empty($hotel->id)){
        //$place = GooglePlacesHelper::getPlace($hotel->id, GooglePlaces::$services['hotel']);
			$place = "";
		}
		else{
		$place = "";	
		}
        $room_types = RoomTypes::query()->orderBy('sort_order', 'ASC')->get();

        foreach ($room_types as $room_type){
            $room_type['price'] = 0;
            foreach ($hotel->prices_room_type as $item){
                if($item->room_type_id == $room_type->id && $hotel->id == $item->hotel_id){
                    $room_type['price'] = $item->price;
                }
            }
        }

        $files = $this->parseAttach($hotel);
        return view('hotel.edit', ['title' => $title, 'hotel' => $hotel, 'files' => $files, 'place' => $place,
            'criterias' => $criterias,
            'rates' => $rates,
            'room_types' => $room_types]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param    \Illuminate\Http\Request $request
     * @param    int $id
     * @return  \Illuminate\Http\Response
     */
    public function sendNotificationHotelClosed($hotel){
        $tourPackages = TourPackage::where('type', 0)
                                    ->where('reference', $hotel->id)
                                    ->get();
                
        foreach($tourPackages as $tourPackage){
            $tour = $tourPackage->getTour();
            if ($tour){
                foreach ($tour->users as $user) {
                                if ($user->id == $tour->author) $checkAuthor = true;
                                $link = "/tour/{$tour->id}";
                                $notification = Notification::query()->create(['content' => "{$hotel->name} closed in {$tour->name}", 'link' => $link]);
                                $user->notifications()->attach($notification);
                            }
            }
        }
    }
    
    
    public function update($id, UpdateHotelRequest $request)
    {

        $previousStatus = Hotel::find($id)->status;
        if($request->status === 'true' ){
            $status = 0;
        } else {
            $status = 1;
        }
        $contacts = $request->get('contacts');
        if($contacts){
            $data = ['hotelContacts' => true, 'fullNameErrorValidate' => trans('main.ContactsshouldnothaveanemptyFullName')];
            foreach ($contacts as $itemContact){
                if(!$itemContact['contact_full_name']){
                    return response()->json($data);
                }
            }
        }

        $this->validate($request, [
            'name' => 'required|string'
        ]);

        $prices_room_type = $request->prices_room_type;

        $request = CitiesHelper::setCityGeneral($request);
        
        /*this*/
       $request->password =  Crypt::encryptString($request->password); 
        $this->hotel->updateById($id, $request->except(
            'attach', 'place_id', 'criterias',
            'prices_room_type', 'contacts'
        ));


        HotelContacts::query()->where('hotel_id', $id)->delete();

        if($contacts){
            foreach ($contacts as $contact) {
                $hotelContact = new HotelContacts();
                $hotelContact->full_name = $contact['contact_full_name'];
                $hotelContact->mobile_phone = $contact['contact_mobile_phone'];
                $hotelContact->work_phone = $contact['contact_work_phone'];
                $hotelContact->email = $contact['contact_email'];
                $hotelContact->hotel_id = $id;
                $hotelContact->save();
            }
        }


        
        
        ServicesHasCriteria::where('service_id', $id)
            ->where('service_type', ServicesHasCriteria::$serviceTypes['hotel'])
            ->delete();
        $hotel = $this->hotel->byId($id);

        if(!empty($request->criterias)){
            $criterias = explode(',', $request->criterias);
            foreach ($criterias as $criteria){
                $services_has_criteria = new ServicesHasCriteria();
                $services_has_criteria->service_id = $hotel->id;
                $services_has_criteria->criteria_id = $criteria;
                $services_has_criteria->service_type = ServicesHasCriteria::$serviceTypes['hotel'];
                $services_has_criteria->save();
            }
        }

        PricesRoomTypeHotel::query()->where('hotel_id', $hotel->id)->delete();


        if(!empty($prices_room_type)){
            foreach ($prices_room_type as $key => $item){
                $obj_price_room_type = new PricesRoomTypeHotel();
                $obj_price_room_type->price = $item == null ? 0 : $item;
                $obj_price_room_type->room_type_id = $key;
                $obj_price_room_type->hotel_id = $hotel->id;
                $obj_price_room_type->save();
            }
        }

        LaravelFlashSessionHelper::setFlashMessage("Hotel $hotel->name edited", 'success');

        // dd($request->attach);
        $this->addFile($request, $hotel);
        $data = ['route' => route('hotel.index')];
        
        if ($status == 0 && $previousStatus != $status){
            $this->sendNotificationHotelClosed($hotel);
        }
        if ($status != $previousStatus){  
            $tourPackages = TourPackage::where('type', 0)->where('reference', $hotel->id)->get();

            $hotel->name = str_replace(" - closed", "", $hotel->name);
            
            if($status == 0){
                $hotel->name = $hotel->name . " - closed";
                foreach($tourPackages as $tourPackage){
                    $tourPackage->name = $tourPackage->name . " - closed";
                    $tourPackage->save();
                }
            } else{
                foreach($tourPackages as $tourPackage){
                    $tourPackage->name = str_replace(" - closed", "", $tourPackage->name);
                    $tourPackage->save();
                }
            }
        }
		$hotel->password  = $request->password ;
        $hotel->status = $status;
        $hotel->save();
       
        return response()->json($data);
    }

    /**
     * Delete confirmation message by Ajaxis.
     *
     * @link      https://github.com/amranidev/ajaxis
     * @param    \Illuminate\Http\Request $request
     * @return  String
     */
    public function DeleteMsg($id, Request $request)
    {
//        $msg = Ajaxis::BtDeleting('Warning!!', 'Would you like to remove This?', '/hotel/' . $id . '/delete');
        $msg = Ajaxis::BtDeleting(trans('main.Warning').'!!',trans('main.WouldyouliketoremoveThis').'?', '/hotel/' . $id . '/delete');

        if ($request->ajax()) {
            return $msg;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param    int $id
     * @return  \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $hotel = $this->hotel->byId($id);


        $elements = \App\TourPackage::where('type', 0)->where('reference', $hotel->id)->get();

        if ($elements->isNotEmpty()) {
            $data = [
                'message' => 'This hotel is used by the tours!',
                'error' => true
            ];
            return response($data);
        } else {
            ServicesHasCriteria::query()->where('service_id', $id)
                ->where('service_type', ServicesHasCriteria::$serviceTypes['hotel'])
                ->delete();

            PricesRoomTypeHotel::query()
                ->where('hotel_id', $hotel->id)
                ->delete();

            LaravelFlashSessionHelper::setFlashMessage("Hotel $hotel->name deleted", 'success');

            $this->removeFile($hotel);
            $this->hotel->deleteById($id);
            Comment::query()->where('reference_type', Comment::$services['hotel'])->where('reference_id', $id)->delete();
            return URL::to('hotel');
        }

    }


    public function getItemContactView(Request $request){
        $count = $request->get('itemCount');
        $view = View::make('component.hotel_contact_form', compact('count'));

        return $view->render();
    }

    public function getItemsContacts(Request $request){
        $count = $request->get('itemCount');
        $hotelId = $request->get('hotelId');

        $hotelContacts = HotelContacts::query()->where('hotel_id', $hotelId)->get();

        foreach ($hotelContacts as $hotelContact){
            $hotelContact['count'] = $count + 1;
            $count++;
        }

        $view = View::make('component.get_hotel_contacts_form', compact('hotelContacts'));

        $data = ['content' => $view->render(), 'count' => $count];
        return $data;
    }
}
