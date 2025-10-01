<?php

namespace App\Http\Controllers;


use App\Helper\LaravelFlashSessionHelper;
use App\Helper\PermissionHelper;
use App\RoomTypes;
use Illuminate\Http\Request;
use App\HotelAgreements;
use App\Hotel;
use App\Tour;
use App\TourPackage;
use App\TourDay;
use App\HotelAgreementsRoomTypeHotels;
use Carbon\Carbon;
use Validator;
use View;
use App\Status;
use App\HotelRoomTypes;
use App\Helper\TourPackage\TourService;
use App\Comment;


class AgreementsController extends Controller
{

    public function __construct()
    {
        $this->current_dates = [];
        $this->used = [];
        $this->reversed = [];
        $this->current_full_dates = [];
        $this->current_agreement_start_date = [];
        $this->current_agreement_start_date_def = [];
        $this->current_agreement_end_date_def = [];
        $this->middleware('permissions.required');
        $this->middleware('preventBackHistory');
        $this->middleware('auth');
    }

    public function update(Request $request)
    {
        $validator = $this->getValidate($request);

        if ($validator->fails()) {
            return redirect('/agreements/' . $request->input('hotel_id') . '/edit/' . $request->input('id'))
                ->withErrors($validator)
                ->withInput();
        }


        $paymentDate = \DateTime::createFromFormat('Y-m-d', $request->input('start_date'));
        $endpaymentDate = \DateTime::createFromFormat('Y-m-d', $request->input('end_date'));

        if ($paymentDate > $endpaymentDate) {
//            $validator->getMessageBag()->add('end_date', 'The specified Start Date greater than End Date');
            $validator->getMessageBag()->add('end_date', trans('main.ThespecifiedStartDategreaterthanEndDate'));
            return redirect('/agreements/' . $request->input('hotel_id') . '/create')
                ->withErrors($validator)
                ->withInput();
        }

        $agreements = HotelAgreements::where('hotel_id', $request->input('hotel_id'))->get();

        foreach ($agreements as $agreement) {
            if ($agreement->id != $request->input('id')) {
                $contractDateBegin = \DateTime::createFromFormat('Y-m-d', $agreement->start_date);
                $contractDateEnd = \DateTime::createFromFormat('Y-m-d', $agreement->end_date);

                if ($paymentDate >= $contractDateBegin && $paymentDate <= $contractDateEnd) {
//                    $validator->getMessageBag()->add('start_date', 'The specified Start Date intersects with one of the Agreements');
                    $validator->getMessageBag()->add('start_date', trans('main.ThespecifiedStartDateintersectswithoneoftheAgreements'));
                    return redirect('/agreements/' . $request->input('hotel_id') . '/edit/' . $request->input('id'))
                        ->withErrors($validator)
                        ->withInput();
                }

                if ($endpaymentDate >= $contractDateBegin && $endpaymentDate <= $contractDateEnd) {
//                    $validator->getMessageBag()->add('end_date', 'The specified End Date intersects with one of the Agreements');
                    $validator->getMessageBag()->add('end_date', trans('main.ThespecifiedEndDateintersectswithoneoftheAgreements'));
                    return redirect('/agreements/' . $request->input('hotel_id') . '/edit/' . $request->input('id'))
                        ->withErrors($validator)
                        ->withInput();
                }
            }

        }

        $agreement = HotelAgreements::where('id', $request->input('id'))->first();
        $agreement->name = $request->input('name');
        $agreement->start_date = $request->input('start_date');
        $agreement->end_date = $request->input('end_date');
        $agreement->description = $request->input('description');
        $agreement->hotel_id = $request->input('hotel_id');
        $agreement->save();

        if ($request->get('room_types_qty')) {

            $room_types_count = collect($request->get('room_types_qty'));
            HotelAgreementsRoomTypeHotels::where('agreement_id', $agreement->id)->delete();

            foreach ($room_types_count as $key => $item) {
                if ($item) {
                    $create_tour_type = new HotelAgreementsRoomTypeHotels();
                    $create_tour_type->room_type_id = $key;
                    $create_tour_type->agreement_id = $agreement->id;
                    $create_tour_type->count = $item;
                    $create_tour_type->save();
                }
            }
        }

        LaravelFlashSessionHelper::setFlashMessage("Agreement $agreement->name edited", 'success');

        return redirect()->route('hotel.show', ['hotel' => $request->input('hotel_id'), 'tab' => 'agreement_tab']);
    }

    public function create($id)
    {
        $hotel = Hotel::where('id', $id)->first();
        $room_types = RoomTypes::query()->orderBy('sort_order', 'ASC')->get();
        return view('kontingent.create', compact('id', 'hotel', 'room_types'));
    }

    public function store(Request $request)
    {
        $validator = $this->getValidate($request);

        if ($validator->fails()) {
            return redirect('/agreements/' . $request->input('hotel_id') . '/create')
                ->withErrors($validator)
                ->withInput();
        }

        $paymentDate = \DateTime::createFromFormat('Y-m-d', $request->input('start_date'));
        $endpaymentDate = \DateTime::createFromFormat('Y-m-d', $request->input('end_date'));

        if ($paymentDate > $endpaymentDate) {
//            $validator->getMessageBag()->add('end_date', 'The specified Start Date greater than End Date');
            $validator->getMessageBag()->add('end_date', trans('main.The specifiedStartDategreaterthanEndDate'));
            return redirect('/agreements/' . $request->input('hotel_id') . '/create')
                ->withErrors($validator)
                ->withInput();
        }

        $agreements = HotelAgreements::where('hotel_id', $request->input('hotel_id'))->get();

        foreach ($agreements as $agreement) {
            $contractDateBegin = \DateTime::createFromFormat('Y-m-d', $agreement->start_date);
            $contractDateEnd = \DateTime::createFromFormat('Y-m-d', $agreement->end_date);

            if ($contractDateEnd && $contractDateBegin) {

                $agreements_tmp = HotelAgreements::where('id', $request->input('agreement_id'))->first();

                if ($paymentDate >= $contractDateBegin && $paymentDate <= $contractDateEnd) {

                    if ($agreements_tmp) {
                        HotelAgreements::where('id', $request->input('agreement_id'))->delete();
                        HotelAgreementsRoomTypeHotels::where('agreement_id', $request->input('agreement_id'))->delete();
                    }

//                    $validator->getMessageBag()->add('start_date', 'The specified Start Date intersects with one of the Agreements');
                    $validator->getMessageBag()->add('start_date', trans('main.ThespecifiedStartDategreaterthanEndDate'));
                    return redirect('/agreements/' . $request->input('hotel_id') . '/create')
                        ->withErrors($validator)
                        ->withInput();
                }

                if ($endpaymentDate >= $contractDateBegin && $endpaymentDate <= $contractDateEnd) {

                    if ($agreements_tmp) {
                        HotelAgreements::where('id', $request->input('agreement_id'))->delete();
                        HotelAgreementsRoomTypeHotels::where('agreement_id', $request->input('agreement_id'))->delete();
                    }

//                    $validator->getMessageBag()->add('end_date', 'The specified End Date intersects with one of the Agreements');
                    $validator->getMessageBag()->add('end_date', trans('main.ThespecifiedEndDateintersectswithoneoftheAgreements'));
                    return redirect('/agreements/' . $request->input('hotel_id') . '/create')
                        ->withErrors($validator)
                        ->withInput();
                }
            }

        }

        if ($request->input('agreement_id')) {
            $agreement = HotelAgreements::where('id', $request->input('agreement_id'))->first();
        } else {
            $agreement = new HotelAgreements();
        }

        $agreement->name = $request->input('name');
        $agreement->hotel_id = $request->input('hotel_id');
        $agreement->start_date = $request->input('start_date');
        $agreement->end_date = $request->input('end_date');
        $agreement->description = $request->input('description');
        $agreement->save();

        LaravelFlashSessionHelper::setFlashMessage("Agreement $agreement->name created", 'success');

        if ($request->get('room_types_qty')) {

            $room_types_count = collect($request->get('room_types_qty'));
            HotelAgreementsRoomTypeHotels::where('agreement_id', $agreement->id)->delete();

            foreach ($room_types_count as $key => $item) {
                if ($item) {
                    $create_tour_type = new HotelAgreementsRoomTypeHotels();
                    $create_tour_type->room_type_id = $key;
                    $create_tour_type->agreement_id = $agreement->id;
                    $create_tour_type->count = $item;
                    $create_tour_type->save();
                }
            }
        }

        return redirect()->route('hotel.show', ['hotel' => $request->input('hotel_id'), 'tab' => 'agreement_tab']);
    }

    public function kontingent_delete(Request $request)
    {
        $tourPackage = TourPackage::findOrfail($request->input('package_id'));
        /*
        if($tourPackage->parrent_package_id){
            return back()->with('retirement_package', 'This is retirement package');
        }*/
        HotelRoomTypes::query()->where('tour_package_id', $request->input('package_id'))->delete();
        $tourPackage->delete();

        if ($tourPackage->type !== null) {
            if (strtolower(TourService::$serviceTypes[$tourPackage->type]) === 'hotel') {
                TourPackage::query()->where('parent_id', $request->input('package_id'))->forceDelete();
            }
        }
        Comment::query()->where('reference_type', Comment::$services['tour_package'])->where('reference_id', $request->input('package_id'))->delete();

        return response()->json($request->input());

    }


    public function kontingent_save(Request $request)
    {
        $arr = json_decode($request->input('replace_id'));
        $hotel_id = $request->input('hotel_id');
        $hotel = Hotel::where('id', $hotel_id)->first();
        /* $dates = $request->input('dates');
         $agreements = HotelAgreements::where('hotel_id', $hotel_id)
             ->where('start_date', '<=', $dates)
             ->where('end_date', '>=', $dates)
             ->first();*/
        $count = 0;
        $quota = 0 ;

        foreach ($arr as $ar) {
            //$item[0] tour package id , $item[1] tour id, $ite[2] quota
            $package = TourPackage::where('id', $ar[0])->first();
            if ($ar[2]) $quota = $ar[2]; //quota the same for all packages

            ($package->parent_id) ? $package_tmp = TourPackage::findOrfail($package->parent_id) : $package_tmp = $package;

            foreach ($package_tmp->room_types_hotel as $room) {
                $count += $room->count;
            }
        }

        if  ($count > $quota ) return response()->json(['error' => true]);


        foreach ($arr as $ar) {
            //$item[0] tour package id , $item[1] tour id, $ite[2] quota
            $package = TourPackage::where('id', $ar[0])->first();

            //hotel parent check for true calculation

            $package['type'] = 0;
            $package['name'] = $hotel['name'];
            $package['reference'] = $hotel_id;
            ///here replace rooms
            //$package->room_types_hotel = $agreements->agreements_room_types;
            //$package['pax'] = $tour['pax'];
            //$package['pax_free'] = $tour['pax_free'];
            //$package['status'] = 34;
            $package->save();
        }

        return response()->json($request->input());
    }

    public function edit($hotel_id, $id)
    {
        $agreement = HotelAgreements::where('id', $id)->first();
        $hotel = Hotel::where('id', $hotel_id)->first();
        $room_types = RoomTypes::query()->orderBy('sort_order', 'ASC')->get();
        return view('kontingent.edit', compact('agreement', 'hotel', 'room_types'));
    }

    public function destroy($hotel_id, $id)
    {
        $agreement = HotelAgreements::query()->findOrFail($id);
        $agreement->delete();
        LaravelFlashSessionHelper::setFlashMessage("Agreement $agreement->name deleted", 'success');
        HotelAgreementsRoomTypeHotels::query()->where('agreement_id', $id)->delete();

        return redirect()->route('hotel.show', ['hotel' => $hotel_id, 'tab' => 'agreement_tab']);
    }

    private function getValidate($request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
        ]);

        return $validator;
    }

    public function kontingent(Request $request)
    {
        $arr = [];

        $hotel_id = $request->input('hotel_id');
        $days = $request->input('days');
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');

        array_push($arr, $this->getRow(0, $days, $hotel_id, $startDate, $endDate)); //Contractual number of rooms
        array_push($arr, $this->getRow(1, $days, $hotel_id, $startDate, $endDate)); //Number of rooms already used
        array_push($arr, $this->getRow(2, $days, $hotel_id, $startDate, $endDate)); //Allotment Reserved
        array_push($arr, $this->getRow(3, $days, $hotel_id, $startDate, $endDate)); //Available quota
        array_push($arr, $this->getRow(4, $days, $hotel_id, $startDate, $endDate)); //Current Booking Status

        $this->reversed = [];
        $this->used = [];
        $this->current_dates = [];
        $this->current_full_dates = [];
        $this->current_agreement_start_date = [];
        $this->current_agreement_start_date_def = [];
        $this->current_agreement_end_date_def = [];

        /*
        if($request->input('num') == '1' && count($agreements)>0 ) {
            array_push($arr, $this->hightlights);
        }*/

        return response()->json($arr);
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

    private function getRow($num, $days, $hotel_id, $startDate, $endDate)
    {
        $d = [];

        $count = 0;
        $out = '';

        $agreements = HotelAgreements::where('hotel_id', $hotel_id)->orderBy('start_date')->get();

        for ($ii = 0; $ii < $days; $ii++) {
            $d[$ii] = '';
        }

        switch ($num) {
            case 0:
                //
                //Contractual number of rooms
                //
                foreach ($agreements as $agreement) {

                    $dates = $this->analyzeDate($agreement, $startDate, $endDate);

                    if ($dates) {

                        $date = date('j', strtotime($dates['start_date']));
                        $date--;
                        $date_end = date('j', strtotime($dates['end_date']));

                        for ($i = $date; $i < $date_end; $i++) {

                            $href = '';
                            $href_close = '';
                            $date_m = date('m', strtotime($dates['start_date']));
                            $date_y = date('Y', strtotime($dates['start_date']));
                            $date_month = date('F', strtotime($dates['start_date']));
                            $dat = $date_y . '-' . $date_m . '-' . ($i + 1);
                            $dat_out = $date_y . ' ' . $date_month . ' ' . ($i + 1);

                            $tourDates = TourDay::where('date', $dat)->whereNull('deleted_at')->get();
                            $hotel = Hotel::where('id', $hotel_id)->first();
                            $hotel_package = '';
                            $count = 0;
                            $content_out = '';

                            foreach ($agreement->agreements_room_types as $item) {
                                $count += $item->count;
                            }

                            $used = 0;
                            $reversed = 0;

                            foreach ($tourDates as $tourDate) {
                                //     $tourDate->packages = $tourDate->packages->sortBy('time_from');

                                foreach ($tourDate->packages as $package) {

                                    $city = $this->getCity($package->name);

                                    $tour = Tour::where('id', $tourDate->tour)->whereNull('deleted_at')->first();

                                    //true name check from service
                                    $selectedService = TourService::getService('hotel');
                                    $service = $selectedService->getItems(
                                        [
                                            'serviceType' => $package->type,
                                            'id' => $package->reference
                                        ]
                                    )->first();

                                    ($service) ? $serviceName = $service->name : $serviceName = '';

                                    if ($tour) {

                                        //hotel parent check for true calculation
//                                       ($package->parent_id) ? $package_tmp = TourPackage::findOrfail($package->parent_id) : $package_tmp = $package;
                                        ($package->parent_id) ? $package_tmp = TourPackage::find($package->parent_id) : $package_tmp = $package;
                                        if(!$package_tmp){
                                            $package_tmp = $package;
                                        }
                                        $status = Status::where('id', $package->status)->first();
                                        //for table calulation
                                        if (preg_match('/' . $hotel->name . '/', $serviceName)) {

                                            $selected_room_types = [];

                                            foreach ($package_tmp->room_types_hotel as $item) {
                                                $room_type = RoomTypes::where('id', $item->room_type_id)->first();
                                                $item->room_types['room_name'] = $room_type->code;
                                                $item->room_types['count_room'] = $item->count;
                                                $item->room_types['price_room'] = $item->price;
                                                $selected_room_types[] = $item->room_types;
                                                if ($package->status == 34) {
                                                    $used += $item->count;
                                                }
                                                if ($package->status == 10) {
                                                    $reversed += $item->count;
                                                }
                                            }

                                        }


                                        //for popups
                                        if (preg_match('/' . $hotel->name . '/', $serviceName) && $package->type == 0 && isset($city) && isset($hotel->city) && $city != '' && $hotel->city != '' && $hotel->city == $city) {

                                            $selected_room_types = [];

                                            foreach ($package_tmp->room_types_hotel as $item) {
                                                $room_type = RoomTypes::where('id', $item->room_type_id)->first();
                                                $item->room_types['room_name'] = $room_type->code;
                                                $item->room_types['count_room'] = $item->count;
                                                $item->room_types['price_room'] = $item->price;
                                                $selected_room_types[] = $item->room_types;
                                            }

                                            $view_out = View::make(
                                                'kontingent.package',
                                                [
                                                    "tour" => $tour,
                                                    "package" => $package,
                                                    "status" => $status['name'],
                                                    'hotel_package_rooms' => $selected_room_types,
                                                    'hotel_package_id' => $hotel_package,
                                                    'hotel' => $hotel,
                                                    'deleted' => true,
                                                ]
                                            )->render();

                                            $content_out .= $view_out;
                                        }

                                        if (!preg_match('/' . $hotel->name . '/', $serviceName) && $package->type == 0 && isset($city) && isset($hotel->city) && $city != '' && $hotel->city != '' && $hotel->city == $city) { //is Hotel

                                            $selected_room_types = [];

                                            foreach ($package_tmp->room_types_hotel as $item) {
                                                $item->room_types['count_room'] = $item->count;
                                                $item->room_types['price_room'] = $item->price;
                                                $selected_room_types[] = $item->room_types;
                                            }

                                            $view_out = View::make(
                                                'kontingent.package',
                                                [
                                                    "tour" => $tour,
                                                    "package" => $package,
                                                    "status" => $status['name'],
                                                    'hotel_package_rooms' => $selected_room_types,
                                                    'hotel_package_id' => $hotel_package,
                                                    'hotel' => $hotel,
                                                    'deleted' => false,
                                                    //"city" => $hotel->city."/".$hotel2."/".$package->id."/".$package->name
                                                ]
                                            )->render();

                                            //if($content_out == '') $content_out = $view_out;
                                            $content_out .= $view_out;
                                        }

                                    }
                                }

                            }


                            if ($content_out == '') {
                                $content_out = 'No tours';
                            } else {
                                $href = '<a href="#"  onclick="kontingentModal(\'' . $dat_out . '\',' . ($i + 1) . ',\'' . $date_m . '\',\'' . $hotel->name . '\')" >';
                                $href_close = '</a>';
                            }

                            $d[$i] = $href . $count . '<div data-dates="' . $dat . '" id="pop_' . $date_m . '_' . ($i + 1) . '" class="hidden">' . $content_out . '</div>' . $href_close;

                            $this->current_dates[$i] = $count;
                            $this->used[$i] = $used;
                            $this->reversed[$i] = $reversed;
                            $this->current_full_dates[$i] = $date_y . "-" . $date_m . "-" . ($i + 1);
                            $this->current_agreement_start_date[$i] = $dates['start_date'];
                            $this->current_agreement_start_date_def[$i] = $dates['start_date_def'];
                            $this->current_agreement_end_date_def[$i] = $dates['end_date_def'];
                        }
                    }

                }
                // for( $i= 2 ; $i < 5; $i++){
                ///    array_push($this->hightlights, $i);
                // }

                break;
            case 1:

                //
                //Allotment Used
                //

                foreach ($this->used as $i => $item) {
                    $d[$i] = $item;
                }

                break;

            case 2:

                //
                //Allotment Reserved
                //

                foreach ($this->reversed as $i => $item) {
                    $d[$i] = $item;
                }

                break;
            case 3:

                //
                //Available quota
                //

                foreach ($this->current_dates as $i => $item) {

                    if ($this->used[$i] != 0) {
                        $d[$i] = $item - $this->used[$i];
                    }
                    if($this->used[$i] == $this->current_dates[$i]){
                        $d[$i] = '0';
                    }
                }

                break;
            case 4:

                //
                //Current booking status %
                //

                foreach ($this->current_dates as $i => $item) {

                    /*
                    $dd = Carbon::parse($startDate)->format('m');
                    $yy = Carbon::parse($startDate)->format('Y');
                    $days1 = cal_days_in_month(CAL_GREGORIAN, $dd, $yy);
                    $days2 = cal_days_in_month(CAL_GREGORIAN, $dd + 1, $yy);
                    $days3 = cal_days_in_month(CAL_GREGORIAN, $dd + 2, $yy);
                    */

                    $end = Carbon::parse($this->current_agreement_start_date[$i]);
                    $now = Carbon::parse($this->current_full_dates[$i]);
                    $length = $end->diffInDays($now);
                    $end = Carbon::parse($this->current_agreement_start_date_def[$i]);
                    $now = Carbon::parse($this->current_agreement_end_date_def[$i]);
                    $length_day = $end->diffInDays($now);

                    if ($this->used[$i] != 0) {

                        if (is_numeric($item)) {
                            $count = 0;


                            for ($j = 0; $j < ($length + 1); $j++) {
                                if ($this->used[$j]) {
                                    $count += $this->used[$j];
                                }
                            }
                            $days_all = $item * ($length_day + 1);
                            $d[$i] = round(($count / $days_all) * 100, 2);
                        } else {
                            $d[$i] = '';
                        }

                    }

                }

                break;

        }

        return $d;
    }

    private function analyzeDate($agreement, $startDate, $endDate)
    {
        $start = Carbon::parse($agreement->start_date)->format('Y-m-d');; //дата договора
        $end = Carbon::parse($agreement->end_date)->format('Y-m-d');; // дата договора енд
        $dates = [];

        if (Carbon::parse($startDate) > $start && Carbon::parse($startDate) < $end || Carbon::parse($startDate)->format('m') == Carbon::parse($agreement->start_date)->format('m')) {
        } else
            if (Carbon::parse($endDate) > $start && Carbon::parse($endDate) < $end || Carbon::parse($endDate)->format('m') == Carbon::parse($agreement->end_date)->format('m')) {
            } else {
                return false;
            }

        if (Carbon::parse($startDate)->format('Y-m-d') > $start && Carbon::parse($startDate)->format('Y-m-d') < $end) {
            $dates['start_date'] = $startDate;
        } else {
            $dates['start_date'] = $agreement->start_date;
        }

        if (Carbon::parse($endDate)->format('Y-m-d') > $start && Carbon::parse($endDate)->format('Y-m-d') < $end) {
            $dates['end_date'] = $endDate;
        } else {
            $dates['end_date'] = $agreement->end_date;
        }

        $dates['start_date_def'] = $agreement->start_date;
        $dates['end_date_def'] = $agreement->end_date;

        return $dates;
    }

    public function viewAgreementHotelRoomType(Request $request)
    {
        $room_array = json_decode($request->get('room_type'));
        $agreement = HotelAgreements::where('id', $request->get('agreement'))->first();
        $contents = [];

        if (!$agreement) {
            $agreement = new HotelAgreements();
            $agreement->hotel_id = $request->input('hotel_id');
            $agreement->save();
        }

        $room_type = new \StdClass;
        $room_type->room_type_id = $room_array->id;
        $room_type->count = 1;

        $contents['created_id'] = $agreement->id;

        $view = View::make(
            'component.item_agreement_hotel_room_type',
            [
                'room_type' => $room_type,
                'room' => $agreement->getRoom($room_type->room_type_id)
            ]
        );

        $contents['content'] = $view->render();

        return response()->json($contents);
    }

}