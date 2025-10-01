<?php

namespace App\Http\Controllers;

use App\Helper\LaravelFlashSessionHelper;
use App\Helper\PermissionHelper;
use App\RoomTypes;
use App\SeasonTypes;
use Illuminate\Http\Request;
use App\Seasons;
use App\Hotel;
use App\Tour;
use App\TourPackage;
use App\TourDay;
use App\SeasonsPricesRoomTypeHotels;
use Carbon\Carbon;
//use Underscore\Types\Object;
use Validator;
use View;
use App\Helper\TourPackage\TourService;
use App\Comment;


class SeasonsController extends Controller
{

    public function __construct()
    {
        $this->middleware('permissions.required');
        $this->current_dates = [];
    }

    public function update(Request $request)
    {
        $validator = $this->getValidate($request);

        if ($validator->fails()) {
            return redirect('/season/' . $request->input('hotel_id') . '/edit/' . $request->input('id'))
                ->withErrors($validator)
                ->withInput();
        }

        $startDate  = date("Y") . '-' . $request->input('start_date');
        $endDate    = date("Y") . '-' . $request->input('end_date');

        $agreement = Seasons::where('id', $request->input('id'))->first();
        $agreement->name = $request->input('name');
        $agreement->start_date = $startDate;
        $agreement->end_date = $endDate;
        $agreement->description = $request->input('description');
        $agreement->type = $request->input('type');
        $agreement->hotel_id = $request->input('hotel_id');
        $agreement->save();



        if ($request->get('room_types_price')) {

            $room_types_count = collect($request->get('room_types_price'));
            SeasonsPricesRoomTypeHotels::where('season_id',$agreement->id)->delete();

            foreach ($room_types_count as $key => $item) {
                if($item) {
                    $create_tour_type = new SeasonsPricesRoomTypeHotels();
                    $create_tour_type->room_type_id = $key;
                    $create_tour_type->season_id = $agreement->id;
                    $create_tour_type->price = $item;
                    $create_tour_type->save();
                }
            }
        }

        LaravelFlashSessionHelper::setFlashMessage("Season Price $agreement->name edited", 'success');

        return redirect()->route('hotel.show', ['hotel' => $request->input('hotel_id'), 'tab' => 'season_tab']);
    }

    public function create($id)
    {
        $hotel = Hotel::where('id', $id)->first();
        $room_types = RoomTypes::query()->orderBy('sort_order', 'ASC')->get();
        $season_types = SeasonTypes::all();

        return view('season.create',compact('id','hotel','room_types','season_types'));
    }

    public function store(Request $request)
    {
        $validator = $this->getValidate($request);

        if ($validator->fails()) {
            return redirect('/season/' . $request->input('hotel_id') . '/create')
                ->withErrors($validator)
                ->withInput();
        }

        $startDate  = date("Y") . '-' . $request->input('start_date');
        $endDate    = date("Y") . '-' . $request->input('end_date');

        $paymentDate = \DateTime::createFromFormat('Y-m-d', $startDate);
        $endpaymentDate = \DateTime::createFromFormat('Y-m-d', $endDate);

        if ($paymentDate > $endpaymentDate) {
            $validator->getMessageBag()->add('end_date', 'The specified Start Date greater than End Date');
            return redirect('/season/' . $request->input('hotel_id') . '/create')
                ->withErrors($validator)
                ->withInput();
        }

        $agreements = Seasons::where('hotel_id',  $request->input('hotel_id'))->get();

        foreach ($agreements as $agreement_check) {
            $contractDateBegin = \DateTime::createFromFormat('Y-m-d', $agreement_check->start_date);
            $contractDateEnd = \DateTime::createFromFormat('Y-m-d', $agreement_check->end_date);

            if($contractDateEnd && $contractDateBegin) {

                /*
                if ($paymentDate >= $contractDateBegin && $paymentDate <= $contractDateEnd) {

                    $validator->getMessageBag()->add('start_date', 'The specified Start Date intersects with one of the Seasons');
                    return redirect('/season/' . $request->input('hotel_id') . '/create')
                        ->withErrors($validator)
                        ->withInput();
                }

                if ($endpaymentDate >= $contractDateBegin && $endpaymentDate <= $contractDateEnd) {

                    $validator->getMessageBag()->add('end_date', 'The specified End Date intersects with one of the Seasons');
                    return redirect('/season/' . $request->input('hotel_id') . '/create')
                        ->withErrors($validator)
                        ->withInput();
                }*/
            }

        }


        //  if($request->input('agreement_id')) {
        //    $agreement = Seasons::where('id', $request->input('agreement_id') )->first();
        //}else{
        $agreement = new Seasons();
        //}


        $agreement->name = $request->input('name');
        $agreement->hotel_id = $request->input('hotel_id');
        $agreement->start_date = $startDate;
        $agreement->end_date = $endDate;
        $agreement->type = $request->input('type');
        $agreement->description = $request->input('description');
        $agreement->save();



        if ($request->get('room_types_price')) {

            $room_types_count = collect($request->get('room_types_price'));
            SeasonsPricesRoomTypeHotels::where('season_id', $agreement->id)->delete();

            foreach ($room_types_count as $key => $item) {
                if($item) {
                    $create_tour_type = new SeasonsPricesRoomTypeHotels();
                    $create_tour_type->room_type_id = $key;
                    $create_tour_type->season_id = $agreement->id;
                    $create_tour_type->price = $item;
                    $create_tour_type->save();
                }
            }
        }

        LaravelFlashSessionHelper::setFlashMessage("Season Price $agreement->name created", 'success');
        return redirect()->route('hotel.show', ['hotel' => $request->input('hotel_id'), 'tab' => 'season_tab']);
    }


    public function edit($hotel_id, $id)
    {
        $agreement = Seasons::where('id', $id)->first();
        $hotel = Hotel::where('id', $hotel_id)->first();
        $room_types = RoomTypes::query()->orderBy('sort_order', 'ASC')->get();
        $season_types  = SeasonTypes::all();
        return view('season.edit', compact('agreement','hotel','room_types','season_types'));
    }

    public function destroy($hotel_id, $id)
    {
        $agreement = Seasons::query()->findOrFail($id);

        $agreement->delete();
        LaravelFlashSessionHelper::setFlashMessage("Season Price $agreement->name deleted", 'success');
        SeasonsPricesRoomTypeHotels::where('season_id', $id)->delete();

        return redirect()->route('hotel.show', ['hotel' => $hotel_id, 'tab' => 'season_tab' ]);
    }

    private function getValidate($request){

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
        ]);

        return $validator;
    }

    private function getCity($name)
    {
        $n = strtok($name, '(');
        if ($n) {
            $hotel = Hotel::where('name', $n)->first();
            return $hotel['city'];
        }
        return null;
    }

    private function analyzeDate($agreements, $agreement, $startDate, $endDate)
    {
        $start = Carbon::parse($agreement->start_date); //дата договора
        $end = Carbon::parse($agreement->end_date); // дата договора енд
        $dates = [];
        $check_fail = false;

        $start_agreement = $agreements[0];
        $end_agreement = $agreements[count($agreements) - 1];
        $start_check = Carbon::parse($start_agreement->start_date);
        $end_check = Carbon::parse($end_agreement->end_date);

        if (Carbon::parse($startDate)->timestamp > $start_check->timestamp && Carbon::parse($startDate)->timestamp < $end_check->timestamp) {
        } else
            if (Carbon::parse($endDate)->timestamp > $start_check->timestamp && Carbon::parse($endDate)->timestamp < $end_check->timestamp) {
            } else {
                $check_fail = true;
            }

        if ($check_fail) {
            $dates['start_date'] = $dates['end_date'] = null;
            return $dates;
        }

        if (Carbon::parse($startDate)->timestamp > $start->timestamp && Carbon::parse($startDate)->timestamp < $end->timestamp) {
            $dates['start_date'] = $startDate;
        } else {
            $dates['start_date'] = $agreement->start_date;
        }

        if (Carbon::parse($endDate)->timestamp > $start->timestamp && Carbon::parse($endDate)->timestamp < $end->timestamp) {
            $dates['end_date'] = $endDate;
        } else {
            $dates['end_date'] = $agreement->end_date;
        }

        return $dates;
    }

    public function viewHotelRoomType(Request $request){
        $room_type = json_decode($request->get('room_type'));


        $room_type->price = 0;
        $view = View::make(
            'component.item_hotel_room_type',
            [
                'room_type'   => $room_type
            ]
        );

        $contents = $view->render();

        return response()->json($contents);
    }

    public function viewSeasonHotelRoomType(Request $request){
        /*
        $room_array = json_decode($request->get('room_type'));
        $agreement = SeasonsPricesRoomTypeHotels::where('id', $request->get('agreement'))->first();
        $contents = [];

        if(!$agreement) {
           // $agreement = new Seasons();
            //$agreement->hotel_id =  $request->input('hotel_id');
            //$agreement->save();
        }

        $room_type = new \StdClass;
        $room_type->room_type_id = $room_array->id;
        $room_type->price = 0;

      //  $contents['created_id'] = $agreement->id;

        $view = View::make(
             'component.item_season_hotel_room_type',
            [
                'room_type'   => $room_type,
                'room'=> $room_type ->room_type_id
            ]
        );

        $contents['content'] = $view->render();*/

        $room_type = json_decode($request->get('room_type'));


        $view = View::make(
            'component.item_season_hotel_room_type',
            [
                'room_type'   => $room_type
            ]
        );

        $contents = $view->render();

        return response()->json($contents);

    }

}