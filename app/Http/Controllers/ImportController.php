<?php

namespace App\Http\Controllers;

use App\City;
use App\File;
use App\Client;
use App\Country;
use App\Helper\CitiesHelper;
use App\Helper\LaravelFlashSessionHelper;
use App\Hotel;
use Illuminate\Http\Request;
use SKAgarwal\GoogleApi\PlacesApi;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\QueryException;
use App\GooglePlaces;

class ImportController extends Controller
{
	protected $google = '';

	public function __construct()
	{
//		$this->google = new PlacesApi('AIzaSyBQt2AhjTda22hWbyq-FvG1xP806ZmzV1A');
//		$this->google = new PlacesApi('AIzaSyBV706sie0bSi4QCwu06KbvH3QBiTSNJzY');
//		$this->google = new PlacesApi('AIzaSyBHlcnK5k1u04PA6g9oylP7fKsRnTgf9P4');
//		$this->google = new PlacesApi('AIzaSyB__xHx2wL72MgX5BD4f27QzDlvIHVby9s');
//		$this->google = new PlacesApi('AIzaSyB60frOyV9wmXl9VBTY9oFJa3YB2tlkVLk');
//		$this->google = new PlacesApi('AIzaSyDvx-JKMyREociW9aPFqsBrnYysrCeV6us');
//		$this->google = new PlacesApi('AIzaSyDFcQzqTzcEMlfYSCPcSaM95aBLyugwDZk');
//        $this->google = new PlacesApi('AIzaSyB-Jw7JkQA2QbVZFQssictsimeqwSXPsBg');
        
        $this->google = new PlacesApi('AIzaSyBV706sie0bSi4QCwu06KbvH3QBiTSNJzY');

        $this->middleware('preventBackHistory');
        $this->middleware('auth');
	}

    public function getModal()
    {
    	return view('component.service_import');
    }

    public function getFile(Request $request)
    {
		
		try{
//dd($request->all());
        if ($request->service_name == 'clients') return $this->clientsImport($request);
//    	$google = new PlacesApi('AIzaSyBQt2AhjTda22hWbyq-FvG1xP806ZmzV1A');
//    	$google = new PlacesApi('AIzaSyBV706sie0bSi4QCwu06KbvH3QBiTSNJzY');
//    	$google = new PlacesApi('AIzaSyBHlcnK5k1u04PA6g9oylP7fKsRnTgf9P4');
//    	$google = new PlacesApi('AIzaSyB__xHx2wL72MgX5BD4f27QzDlvIHVby9s');
//      $this->google = new PlacesApi('AIzaSyB60frOyV9wmXl9VBTY9oFJa3YB2tlkVLk');
//        $this->google = new PlacesApi('AIzaSyDvx-JKMyREociW9aPFqsBrnYysrCeV6us');
//        $this->google = new PlacesApi('AIzaSyDFcQzqTzcEMlfYSCPcSaM95aBLyugwDZk');
//        $this->google = new PlacesApi('AIzaSyB-Jw7JkQA2QbVZFQssictsimeqwSXPsBg');
        
        $this->google = new PlacesApi('AIzaSyBV706sie0bSi4QCwu06KbvH3QBiTSNJzY');
    	$namespace = "App\\" . $request->service_name;
    	$datas = $this->getData($request);
        //$datas = $datas[0];
        set_time_limit(0);
        $i = 0;        
        $namespace::where('id', 'like', '%%')->delete();
	    foreach ($datas as $data) {
        $i++;

           // $google_name = preg_replace('/\s+/', '+', $data['name']);
           // $google_address = preg_replace('/\s+/', '+', $data['address_first']);
           // $google_country = preg_replace('/\s+/', '+', $data['country']);
         //   $google_city = preg_replace('/\s+/', '+', $data['city']);

//dd($data);
		    // $country = $data['country'];
		    // if(is_numeric($country) || is_null($country)){
		    // 	$data = $this->checkAddressFirst($data);
		    // } else {
		    // 	$data = $this->checkCountryExist($data, $country);
		    // };
            
            
//            $data = $this->checkCity($data);

// Temporary search city - while google api not work;            
//***************************************************    
//            if($i > 10) break;
            if ( array_key_exists('city', $data)){ 
/*
                $cityName = $data['city'];
                $city = City::where('name', $cityName)->first();
                
                $data['country'] = null;
                $data['city'] = null;
                
                if ($city){
                    $data['city'] = $city->id;
                    if($city->country){
                        $data['country'] = $city->country;
                    }    
                }
 * 
 */
                //$data['country'] = null;
                $city = $data['city'];
                if ($data['city']){
//                    sleep(1);
//                    $data = $this->getCountryCity($data, $data['city']);
                    $cityData = $this->getCountryAndCity($data['city']);

                    if(isset($cityData['city'])){
                        $data['city'] = $cityData['city'];
                       // $data['country'] = $cityData['country'];
//                        $data['code'] = $cityData['code'];

                    } else {
//echo "<pre>";
//print_r($cityData);
//echo "</pre>";
//dd("!!!!!!!!!!!!!!!!!!!!!!!!!");
                    }
//                    $search = $this->google->textSearch($data['city']);
//                    $results = $this->google->placeDetails($search['results'][0]['place_id']);
//dd($results );                    
                } else {
                    $data['city'] = null;
                }
            }
//            if ( array_key_exists('country', $data)){ 
//                $countryId = $data['country'];
//                $country = Country::find($countryId);
//                if ($country){
//                    $data['country'] = $country->alias;
//                } else {
//                    $data['country'] = null;
//                }
//            }
                        if ( array_key_exists('address', $data)){ 
                $data['address_first'] = $data['address'];
                unset($data['address']);
            }
            
            if ( array_key_exists('code', $data)){ 
                $data['code'] = $data['code'];
            }
            
            if ( array_key_exists('phone', $data)){ 
                $data['work_phone'] = $data['phone'];
                unset($data['phone']);
            }
            if ( array_key_exists('email', $data)){ 
                $data['work_email'] = $data['email'];
                unset($data['email']);
            }
			 if ( array_key_exists('status', $data)){ 
                //$data['actions'] = $data['actions'];
                unset($data['status']);
            }
			if ( array_key_exists('created_at', $data)){ 
                //$data['actions'] = $data['actions'];
                unset($data['created_at']);
            }
            if ( array_key_exists('updated_at', $data)){ 
                //$data['actions'] = $data['actions'];
                unset($data['updated_at']);
            }
            if ( array_key_exists('deleted_at', $data)){ 
                //$data['actions'] = $data['actions'];
                unset($data['deleted_at']);
            }
			if ( array_key_exists('password', $data)){ 
                //$data['actions'] = $data['actions'];
                unset($data['password']);
            }
			if ( array_key_exists('password', $data)){ 
                //$data['actions'] = $data['actions'];
                unset($data['password']);
            }
			if ( array_key_exists('demo_hotel', $data)){ 
                //$data['actions'] = $data['actions'];
                unset($data['demo_hotel']);
            }
            if ( array_key_exists('actions', $data)){ 
                //$data['actions'] = $data['actions'];
                unset($data['actions']);
            }
            if ( array_key_exists('id', $data)){ 
                
                $id = $data['id'];
            }

		    if(array_key_exists('address2', $data)) {
			    $data['address_second'] = $data['address2'];
			    unset($data['address2']);
		    }

//		    if(array_key_exists('hotelcat', $data)) {
//			    $data['data_hotelcat'] = intval($data['hotelcat']);
//			    unset($data['hotelcat']);
//		    }
            if(array_key_exists('hotelcat', $data)) {
                $rate = \App\Rate::where('name', $data['hotelcat'])->first();
//			    $data['data_hotelcat'] = $data['hotelcat'];
			    if($rate){
                    $data['rate'] = $rate->id;
                }
                
			    unset($data['hotelcat']);
		    }

		    $data =  $this->modifyValues($data);
    		unset($data['deleted'], $data[0]);

//            $order = $namespace::findOrFail($data['id']);

            
// Temporary search city - while google api not work;            
//***************************************************            
            $res = $namespace::create($data);
/*
            if($google_address !== '') {

                $responce = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=" . $google_name . "+" . $google_address . "+" . $google_city . "&component=" . $google_country."&key=AIzaSyBV706sie0bSi4QCwu06KbvH3QBiTSNJzY");

                $out = json_decode($responce);

                if (isset($out->results[0]) && $out->status == 'OK') {
                    $googlePlaces = new GooglePlaces();
                    $googlePlaces->place_id = $out->results[0]->place_id;
                    $googlePlaces->type = GooglePlaces::$services[strtolower($request->service_name)];
                    $googlePlaces->service_id = $res['id'];
                    $googlePlaces->save();
                }
            }

*/

/*
            $order = $namespace::withTrashed()->find($data['id']);
            unset($data['id']);
            if ( !isset($data['deleted']) || $data['deleted'] == '0'){
                $data['deleted_at'] = null;
            } else{
                $data['deleted_at'] = date("Y-m-d h:i:s",time());
            }
            unset($data['deleted']);
            if($order){
                $order->fill($data);
                $order->push();
            } else{
                $namespace::create($data);
            }
*/
/*            
            try {
                //$namespace::create($data);
                $order = $namespace::findOrFail($data['id']);
                $order->fill($data);
                $order->push();
		    } catch (QueryException $e) {
			    LaravelFlashSessionHelper::setFlashMessage(trans('main.ImportErrorFileisnotvalid'), 'error');
			    return back();
		    }
            catch (\Exception $e) {
	            LaravelFlashSessionHelper::setFlashMessage(trans('main.ImportErrorFileisnotvalid'), 'error');
	            return back();
            }
 */
    	}
			LaravelFlashSessionHelper::setFlashMessage("Csv has been uploaded succesfully", 'success');
    	return back()->with('service_created');
		}
		catch(\Exception  $e){
			LaravelFlashSessionHelper::setFlashMessage("Incorrect Csv ", 'error');
        return back()->with('service_created');
    }
    }

    public function addPlaces(Request $request)
    {

        //ini_set('max_input_time', 60);
        //ini_set('max_execution_time', 180);
        //ini_set('memory_limit','8M');
        ini_set('memory_limit', '800M');
        set_time_limit(0);

        $updated = 0;
        $service_name = $request->name;
        if (!$service_name) {
            return view('component.places_config');
        }

        $namespace = "App\\" . $service_name;

        foreach ($namespace::All() as $i => $service) {

            echo ($i ."/". $service->name."<br>");

            $google_name = preg_replace('/\s+/', '+',  mb_convert_encoding($service->name, 'UTF-8', 'UTF-8'));
            $google_address = preg_replace('/\s+/', '+', mb_convert_encoding($service->address_first, 'UTF-8', 'UTF-8'));

            if ($service_name === 'Flight' || $service_name === 'Cruises') {
                $google_country = preg_replace('/\s+/', '+', $this->getCountryName($service->country_from));
                $google_city = preg_replace('/\s+/', '+', $this->getCityName($service->city_from));
            } else {
                $google_country = preg_replace('/\s+/', '+', $this->getCountryName($service->country));
                $google_city = preg_replace('/\s+/', '+', $this->getCityName($service->city));
            }

            if ($google_address !== '') {

                $place = GooglePlaces::where('service_id', $service->id)->where('type', GooglePlaces::$services[strtolower($service_name)])->first();

                if (!$place) {

                    try {

                        $responce = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=" . $google_name . "+" . $google_address . "+" . $google_city . "&component=" . $google_country . "&key=AIzaSyBV706sie0bSi4QCwu06KbvH3QBiTSNJzY");

                        $out = json_decode($responce);

                        if (isset($out->results[0]) && $out->status == 'OK') {
                            $googlePlaces = new GooglePlaces();
                            $googlePlaces->place_id = $out->results[0]->place_id;
                            $googlePlaces->type = GooglePlaces::$services[strtolower($service_name)];
                            $googlePlaces->service_id = $service->id;
                            $googlePlaces->save();
                            $updated++;

                        }

                    } catch(\Exception $e) {
                        continue;
                    }

                }
            }

        }

        echo "Service: '". $service_name. "' All: ". count($namespace::All()). " Updated: ". $updated;

    }

    public function getCityName($id){
	    $name = '';
        $city = City::where('id', $id)->first();
        if($city) $name= $city->name;
        return $name;
    }

    public function getCountryName($code){
        $name = '';
        $country = Country::where('alias', $code)->first();
        if($country) $name = $country->name;
        return $name;
    }

    public function getFileSeasons(Request $request)
    {
        $file = File::create($request->except('service_name'));
        $resource = fopen($file->attach->path(), 'r');
        \Storage::put('public/seasons.xlsx', $resource );
        return back()->with('seasons uploaded');
    }

    public function modifyValues($data)
    {
		$values = [
				'address1'  => 'address_first',
				'address2'  => 'address_second',
				'workphone'  => 'work_phone',
				'workfax'  => 'work_fax',
				'workemail'  => 'work_email',
				'contactname'  => 'contact_name',
				'contactphone'  => 'contact_phone',
				'contactemail'  => 'contact_email',
				'intcomments'  => 'int_comments',
				'data.cb'  => 'data_cb',
				'data.cd'  => 'data_cd',
				'data.ct'  => 'data_ct',
				'data.lmb'  => 'data_lmb',
				'data.lmd'  => 'data_lmd',
				'data.lmt'  => 'data_lmt',
//				'data.hotelcat'  => 'data_hotelcat',
//				'rate'  => 'data_hotelcat',
		];
		foreach ($values as $key => $item) {
			if(array_key_exists($key, $data)) {
				$data[$item] = $data[$key];
				unset($data[$key]);
			}
		}

		return $data;
    }

    public function clientsImport($request)
    {
        $datas = $this->getData($request);
        foreach ($datas as $data) {
            Client::create($data);
        }
        return back()->with('service_created');
    }

    public function checkCountryExist($datas, $country_check)
    {
    	$def_country = Country::where('name', $country_check)->first();
    	if (!$def_country) return $datas = $this->checkAddressFirst($datas);
    	$datas['country'] = $def_country;
    	return $datas;
    }

    public function getData(Request $request)
    {
//dd($request->except('service_name'));        
        $file = File::create($request->except('service_name'));
        $datas = Excel::load($file->attach->path())->get();
        $file->delete();
        return $datas->toArray();
    }

    public function checkAddressFirst($data)
    {
        if (is_null($data['address_first']) || is_numeric($data['address_first'])) return $data = $this->checkCity($data);
        $data = $this->getCountryCity($data, $data['address_first']);
        return $data;
    }

    public function checkCity($data)
    {
    	//@ToDo: check for pages
        $city = array_key_exists('city', $data) ? $data['city'] : null;
        if (is_numeric($city) || is_null($city)) return $data;
        $data = $this->getCountryCity($data, $data['city']);
        return $data;
    }

    
    public function getCountryAndCity($search_loc)
    {
		$alias = '';
        $result = [];
        $cityFromBase = City::where('name', $search_loc)->first();

//        if($cityFromBase && $cityFromBase->country){
        if($cityFromBase){
            $result['country'] = $cityFromBase->country;
            $result['city'] = $cityFromBase->id ?? '';
            $result['code'] = $cityFromBase->code ?? '';
            return $result;
        }
     
        
//        $result['city'] = '';
//        $result['country'] = '';
//        $result['code'] = '';        
//        return $result;



        try {
            $search = $this->google->textSearch($search_loc);
            $formattedAddr = $search['results'][0]['formatted_address'];

            $expl = explode(',', $formattedAddr);
            $countryExpl = $expl[count($expl) - 1];

            $countryExpl = str_replace(' ', '', $countryExpl);

            $country = Country::where('name', $countryExpl)->first();

            $city = City::create([
                                'name' => $search_loc,
                                'code' => $search['results'][0]['place_id'],
                                'country' => $country->alias]);
//          if('Alsfeld' == $search_loc) dd($city);

            $result['city'] = $city->id ?? '';
            $result['country'] = $city->country ?? '';
            $result['code'] = $city->code ?? '';
            
            return $result;

            dd($countryExpl);
            dd($search['results'][0]['formatted_address']);
            dd($search['results'][0]['place_id']);
            $results = $this->google->placeDetails($search['results'][0]['place_id']);
            
//echo "<pre>";
//print_r($search['results'][0]);
//echo "</pre>";
//dd($results);            
//if($search['results'][0]['place_id'] && $results == []){
//    
//    dd($this->google,$search['results'][0]['place_id']);
//}



            $json = json_decode($results);
            
            foreach ($json->result->address_components as $full_address) {
                if(in_array('country', $full_address->types)){
                    $country = Country::where('name', $full_address->long_name)->first();
                    $alias = $country->alias ?? '';
                    $result['country'] = $alias;
                }
                if(in_array('locality', $full_address->types) && in_array('political', $full_address->types)){
                    $city_search = $this->google->textSearch($full_address->long_name);
                    $city_detail = $this->google->placeDetails($city_search['results'][0]['place_id']);
                    $city_json = json_decode($city_detail);
                    $city = City::where('code', $city_json->result->place_id)->first();
                    if (!$city){
                        $city = City::create([
                            'name' => $city_json->result->name,
                            'code' => $city_json->result->place_id,
                            'country' => $alias ?? '']);
                    };
                    $result['city'] = $city->id ?? '';
                    $result['country'] = $city->country ?? '';
                    $result['code'] = $city->code ?? '';
                }
            }
        } catch (GooglePlacesApiException $e) {

        } finally {
            return $result;
        };
    }
    

    public function getCountryCity($data, $search_loc)
    {
		$alias = '';
        try {
            $search = $this->google->textSearch($search_loc);
            $results = $this->google->placeDetails($search['results'][0]['place_id']);
            
            $json = json_decode($results);
            foreach ($json->result->address_components as $full_address) {
                if(in_array('country', $full_address->types)){
                    $country = Country::where('name', $full_address->long_name)->first();
                    $alias = $country->alias ?? '';
                    $data['country'] = $alias;
                }
                if(in_array('locality', $full_address->types) && in_array('political', $full_address->types)){
                    $city_search = $this->google->textSearch($full_address->long_name);
                    $city_detail = $this->google->placeDetails($city_search['results'][0]['place_id']);
                    $city_json = json_decode($city_detail);
                    $city = City::where('code', $city_json->result->place_id)->first();
                    if (!$city){
                        $city = City::create([
                            'name' => $city_json->result->name,
                            'code' => $city_json->result->place_id,
                            'country' => $alias ?? '']);
                    };
                    $data['city'] = $city->id ?? '';
                }
            }
        } catch (GooglePlacesApiException $e) {

        } finally {
            return $data;
        };
    }

    public function check() {
	    $namespace = "App\\Hotel";
	    $datas = Excel::load(storage_path('hotels.xlsx'))->get();
	    $datas =  $datas->toArray();
	    $datas = $datas[0];
	    set_time_limit(0);

	    foreach ($datas as $data) {
		    // $country = $data['country'];
		    // if(is_numeric($country) || is_null($country)){
		    // 	$data = $this->checkAddressFirst($data);
		    // } else {
		    // 	$data = $this->checkCountryExist($data, $country);
		    // };
		    $data = $this->checkCity($data);
		    if ( array_key_exists('address', $data)){
			    $data['address_first'] = $data['address'];
			    unset($data['address']);
		    }

		    if ( array_key_exists('phone', $data)){
			    $data['work_phone'] = $data['phone'];
			    unset($data['phone']);
		    }
		    if ( array_key_exists('email', $data)){
			    $data['work_email'] = $data['email'];
			    unset($data['email']);
		    }
		    if ( array_key_exists('actions', $data)){
			    //$data['actions'] = $data['actions'];
			    unset($data['actions']);
		    }
		    if ( array_key_exists('id', $data)){

			    $id = $data['id'];
		    }

		    if(array_key_exists('address2', $data)) {
			    $data['address_second'] = $data['address2'];
			    unset($data['address2']);
		    }
//dd("!!!!!!!!!");
		    if(array_key_exists('hotelcat', $data)) {
                $rate = \App\Rate::where('name', $data['hotelcat']);
//			    $data['data_hotelcat'] = $data['hotelcat'];
			    $data['rate'] = $rate->id;
			    unset($data['hotelcat']);
		    }

		    $data =  $this->modifyValues($data);

		    unset($data['deleted'], $data[0], $data['id']);
		    
		    try {
			    $namespace::create($data);
		    } catch (QueryException $e) {
			    LaravelFlashSessionHelper::setFlashMessage(trans('main.ImportErrorFileisnotvalid'), 'error');
			    return back();
		    }
	    }
	    return back()->with('service_created');
    }

    public function checkServicesCitiesCountries(Request $request)
    {
    	$hotels = Hotel::where('country', 'DE')->get();
    	foreach ($hotels as $hotel) {
		    if(!is_numeric($hotel->city)) {
			    dump($hotel->id);
			    $placeData = ($this->getCountryCity([], $hotel->city));
			    if (array_key_exists('city', $placeData)) {
				    $existedHotel = Hotel::find($hotel->id);
				    $existedHotel->city = $placeData['city'];
				    $existedHotel->save();
			    }

		    }
	    }
    }
    
    public function cityDatas($cityName)
    {
        $data=[];
        $alias = '';
        try {
            $search = $this->google->textSearch($cityName);
            $results = $this->google->placeDetails($search['results'][0]['place_id']);
 
            $json = json_decode($results);
            foreach ($json->result->address_components as $full_address) {
                if(in_array('country', $full_address->types)){
                    $country = Country::where('name', $full_address->long_name)->first();
                    $alias = $country->alias ?? '';
                    $data['country'] = $alias;
                }
                  
                if(in_array('locality', $full_address->types) && in_array('political', $full_address->types)){
                    $city_search = $this->google->textSearch($full_address->long_name);
                    $city_detail = $this->google->placeDetails($city_search['results'][0]['place_id']);
                    $city_json = json_decode($city_detail);
                    $city = City::where('code', $city_json->result->place_id)->first();
                    if (!$city){
                        $city = City::create([
                            'name' => $city_json->result->name,
                            'code' => $city_json->result->place_id,
                            'country' => $alias ?? '']);
                    } 
                    $data['city'] = $city->id ?? '';
                }
            }
        } catch (GooglePlacesApiException $e) {

        } finally {
            return $data;
        };
    }
    
    public function getGoogleCities(Request $request)
    {
//        $city = City::find(103);
//        if(!$city->country){
//                echo $city->name.' - ok<br>';
//                $cityDatas = $this->cityDatas($city->name);
//                if(isset($cityDatas['country'])){
//                    $city->country = $this->cityDatas($city->name)['country'];
//                    $city->save();
//                }
//            }    
//        
        
        $cities = City::all();
    	foreach ($cities as $city){
//            echo $city->created_at.": ".$city->name." - ".$city->country."<br>";
            if(!$city->country){
                    echo $city->name.' - ok ';
                    $cityDatas = $this->cityDatas($city->name);
                    if(isset($cityDatas['country'])){
                        $city->country = $cityDatas['country'];
                        $city->save();
                        echo $cityDatas['country']. "<br>";
                    }
                }    
        }
        
        dd("Success");
    }
}
