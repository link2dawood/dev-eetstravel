<?php

namespace App\Http\Controllers\TMSClient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Tour;
use App\TourDay;
use App\TourService;
use App\Hotel;
use App\Event;
use App\Guide;
use App\Restaurant;
use App\Country;
use App\Transfer;
class ModalController extends Controller
{
    //
    public function show($id, Request $request)
    {
        # code...
		
       $date =  $request->date;
	
        $tour = Tour::find($id);
		if(!empty($tour->country_end)){
		 $country = Country::where("name",$tour->country_end)->first();
        $check = [];
        
        $tourDates = TourDay::with('packages')->where('tour', $tour->id)->where('date', $request->date)->get()->sortBy('date');
        $hotels = Hotel::where('demo_hotel',1)->get();
        $events = Event::where('country',$country->alias)->get();
        $guides = Guide::where('country',$country->alias)->get();
        $restaurants = Restaurant::where('country',$country->alias)->get();
     	$tourDayId = $request->tourDayId;
		}
		else{
			$tourDates = TourDay::with('packages')->where('tour', $tour->id)->where('date', $request->date)->get()->sortBy('date');
        $hotels = Hotel::where('demo_hotel',1)->get();
        $events = Event::all();
        $guides = Guide::all();
        $restaurants = Restaurant::all();
     	$tourDayId = $request->tourDayId;
		}
        return view("TMSClient.home.tour.models.services_model",compact("tourDates","date","hotels","events","guides","restaurants","tourDayId"));
    }
	
	public function getData(Request $request){
		
		$tour = Tour::find($request->tourId);
        $country = Country::where("name",$tour->country_begin)->first();
        $hotels = Hotel::where('name', 'like', '%' . request('search') . '%')->where('demo_hotel',1)->get();
        $events = Event::where('name', 'like', '%' . request('search') . '%')->where('country',$country->alias)->get();
        $guides = Guide::where('name', 'like', '%' . request('search') . '%')->where('country',$country->alias)->get();
        $restaurants = Restaurant::where('name', 'like', '%' . request('search') . '%')->where('country',$country->alias)->get();
       
        $tourDayId = $request->tourDayId;

        return view("TMSClient.home.tour.models.model_change_data",compact("hotels","events","guides","restaurants","tourDayId"));
    }

	public function getdropdownData(Request $request){
        $hotels = [];
        $events = [];
        $guides = [];
        $restaurants = [];
		$transfers = [];
		 $tour = Tour::find($request->tourId);
        $country = Country::where("name",$tour->country_begin)->first();
        if($request->dropdown_value == "Hotel"){
        $hotels = Hotel::where('demo_hotel',1)->get();
            }
            elseif ($request->dropdown_value == "Event") {
                $events = Event::all();
            }
            elseif($request->dropdown_value == "Guide"){
                $guides = Guide::all();
                    }
		elseif($request->dropdown_value == "Transfer"){
                $transfers = Transfer::all();
                    }
                    else {
                        $restaurants = Restaurant::all();
                    }
        $tourDayId =  $request->tourDayId;
        return view("TMSClient.home.tour.models.model_change_data",compact("hotels","events","guides","restaurants","transfers","tourDayId"));
    }
	
		public function selectionPackageData(Request $request){
        $hotels = [];
        $events = [];
        $guides = [];
        $restaurants = [];
		$transfers = [];
		 $tour = Tour::find($request->tourId);
        $country = Country::where("name",$tour->country_begin)->first();
        if($request->dropdown_value == 1){
        $hotels = Hotel::where('rate',9)->where('demo_hotel',1)->get();
            }
            elseif ($request->dropdown_value == 2) {
                $hotels = Hotel::where('rate',8)->where('demo_hotel',1)->get();
            }
            elseif($request->dropdown_value == 3){
                $hotels = Hotel::where('rate',7)->where('demo_hotel',1)->get();
                    }
		elseif($request->dropdown_value == 4){
                $hotels = Hotel::where('rate',6)->where('demo_hotel',1)->get();
                    }
                    else {
                        $hotels = Hotel::where('rate',5)->where('demo_hotel',1)->get();
                    }
        $tourDayId =  $request->tourDayId;
        return view("TMSClient.home.tour.models.model_change_data",compact("hotels","events","guides","restaurants","transfers","tourDayId"));
    }
}
