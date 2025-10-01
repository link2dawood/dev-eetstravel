<?php

namespace App\Http\Controllers\TMSClient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Client;
use App\Tour;

use App\RoomTypes;
use App\City;
use Auth;
use App\Country;
use App\Status;
use App\TourDay;
use App\Helper\TourPackage\TourService;
use Carbon\Carbon;
use App\Helper\CitiesHelper;
use DB;
use App\Helper\FileTrait;
use App\Helper\HelperTrait;
use App\Helper\PermissionHelper;
use View;
use App\Http\Controllers\DatatablesHelperController;
class TourController extends Controller
{
    //
    use FileTrait;
    use HelperTrait;
	/*
	public function __construct()
    {
		$this->middleware('clientauth', ['except' => 'landingPage']);
	}
	*/
	public function getShowButton($id, $isQuotation = false, $tour, array $perm)
    {
        $url = array('show'       => route('TMS-Client-tours.show', ['id' => $id]));

        return '<div class="d-flex align-items-center gap-2">
                      <a href="'.$url["show"].'" class="action-link btn-primary" >
                        <i class="fas fa-eye"></i>
                      </a>
                    </div>';
//        return DatatablesHelperController::getActionButton($url, $isQuotation, $tour);
    }
    public function store(Request $request)
    {
		
    	$client_id = $request->session()->get("CLIENT_ID");
		if(!empty($client_id)){
        $request['pax'] = $request->get('pax') == null ? 0 : $request->get('pax', 0);
        $request['pax_free'] = $request->get('pax_free') == null ? 0 : $request->get('pax_free', 0);

        $this->validateTour($request);

        $responsible_user = (int) $request->get('responsible_user', 0);
        $data = ['departure_date' => $request->departure_date, 'retirement_date' => $request->retirement_date];
        // $dateRange = $this->findDateRange($data);
        // if (!$dateRange) return back();
  
        // if($request->assigned_user == 'null' && !$request->is_quotation){
        //     $requestData = $request;
        //     $requestData['assigned_user'] = '';
        //     $this->validate($requestData, [
        //         'assigned_user' => 'required'
        //     ]);
        // }

        $request = CitiesHelper::setCityBegin($request);
        $request = CitiesHelper::setCityEnd($request);
        DB::beginTransaction();
        $tour = new Tour();
        $tour->name = $request->name;
        $tour->remark = $request->remark;
        $tour->departure_date = $request->departure_date;
        $tour->retirement_date = $request->retirement_date;
        $tour->pax = $request->pax;
        $tour->pax_free = $request->pax_free;
        $tour->total_amount = $request->total_amount == null ? 0 : $request->total_amount;
        $tour->price_for_one = $request->price_for_one == null ? 0 : $request->price_for_one;
        $tour->itinerary_tl = $request->itinerary_tl;

        $tour->country_begin = $request->country_begin;
        $tour->city_begin = $request->city_from;
        $tour->country_end = $request->city_to;
        $tour->city_end = $request->city_end;
        $tour->invoice = $request->invoice;
        $tour->ga = $request->ga;
    
        $tour->status = 46;
        $tour->is_quotation =1;
        $tour->responsible = $responsible_user;
        $tour->phone = $request->phone == null ? '' : $request->phone;
        //  $tour->save();

        $tour->external_name = $this->generateExternalName($request->country_begin, $tour->id);
		$tour->client_id = $client_id;
         $tour->save();
        DB::commit();

        if ($request->get('room_types_qty')) {
            $room_types_count = collect($request->get('room_types_qty'));

            foreach ($room_types_count as $key => $item) {
                if($item) {
                    $create_tour_type = new TourRoomTypeHotel();
                    $create_tour_type->room_type_id = $key;
                    $create_tour_type->tour_id = $tour->id;
                    $create_tour_type->count = $item;
                    $create_tour_type->save();
                }
            }
        }

        if ($request->assigned_user) {
               $a_users = explode(',',$request->assigned_user);
               $tour->users()->sync($a_users);

               /**

            $a_users = $request->assigned_user;
            $tour->users()->sync($a_users);*/

           
        }
        // $tour->users()->attach($request->assigned_user);
        // if ($tour) {
        //     $data = ['departure_date' => $tour->departure_date, 'retirement_date' => $tour->retirement_date];
        //     $dateRange = $this->findDateRange($data);
        //     $this->createUpdateTourDates($tour->id, $dateRange);
        // }

        $this->addFile($request, $tour);

        
        if (!is_null($request->file('imgToUpload'))){
            $files = $request->file('imgToUpload');
            $id = $request->id;

            $xx = '1440';
            $xy = '650';

            $destinationPath = 'uploads/';
            foreach($files as $file){
            $fileName = preg_replace('/\s+/', '', $file->getClientOriginalName());
            $filePath = $file->move($destinationPath, $fileName)->getPathName();
            $img = Image::make( $filePath)->fit($xx, $xy, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            $fileNameRnd = '/' . $destinationPath . "_".$xx."x" . substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 5) . '_' . $fileName;
            $path = public_path() . $fileNameRnd;
            $img->save($path);
            if(file_exists($filePath)){
                unlink($filePath);
            }
        }
            $url = url('/') . $fileNameRnd;

            $attachment = $tour->attachments()->first();

            if ($attachment){
                if(file_exists($attachment->path)){
                    unlink($attachment->path);
                }
                $attachment->url = $url;
                $attachment->path = $path;
                $attachment->save();
            } else{
                $attachment = \App\Attachment::create([
                    'url'   => $url,
                    'path'  => $path
                ]);
            }
            $tour->attachments()->save($attachment);

            
        }
        
        
        // LaravelFlashSessionHelper::setFlashMessage("Tour {$tour->name} created", 'success');

        if($request->get('modal_create_tour') == 1) {
            $data = ['route' => url('home')];
        }else{
            $data = ['route' => route('tour.create')];
        }
//        return response()->json(json_encode($data));
        return redirect("TMS-Client/quotation_requests");
        return response()->json($data);
		}else{
			return response()->back();
		}
    }
	
	
	public function tour_data(Request $request)
    {
	
        //if (Auth::user()->hasRole('admin')) {
         $client_id = $request->session()->get("CLIENT_ID");
         $tours = Tour::where('status', 39)->where("client_id",$client_id)->get();
           
        //} else {
            //$tours = $this->repository->allForAssigned();
        //}

        $permission_destroy = PermissionHelper::$relationsPermissionDestroy['App\Tour'];
        $permission_edit = PermissionHelper::$relationsPermissionEdit['App\Tour'];
        $permission_show = PermissionHelper::$relationsPermissionShow['App\Tour'];
    
        $perm = [];        
        $perm['show'] = true;        
        $perm['edit'] = true;
        $perm['destroy'] = true;
        $perm['clone'] = true;
   
        return Datatables::of($tours)->addColumn('action', function ($tour) use($perm) {
		
                return $this->getShowButton($tour->id, false, $tour, $perm);
			
            })
            ->addColumn('status_name', function ($tour){
                if(true){
                    $status = View::make('component.tour_status_for_datatable', ['status' => $tour->getStatusName(), 'color' => $tour->getStatusColor()]);
                }else{
                    $status = $tour->getStatusName();
                }
                return $status;
            })
            ->addColumn('select', function ($tour) {
                return DatatablesHelperController::getSelectButton($tour->id, $tour->name);
            })
            ->addColumn('link', function($tour){
                $tourDay = TourDay::where('tour', $tour->id)->first();
                $link = route('tour_package.store');
                if($tourDay){
                return "<button data-link='$link' class='btn btn-success tour_package_add' data-tourDayId='{$tourDay->id}' data-tour_id='{$tour->id}'" .
                    " data-departure_date='{$tour->departure_date}' data-retirement_date='{$tour->retirement_date}'>+</button>";
                }
            })
            ->rawColumns(['select', 'action', 'link'])
            ->make(true);
    }
	
    public function create(){

        $countries = Country::all();
        $cities = City::all();
      
        $room_typesDvo = RoomTypes::query()->orderBy('sort_order', 'ASC')->get();
        $room_types = array();

        foreach ($room_typesDvo as $room_type){
            $room_type['count_room'] = null;
            $room_type['price_room'] = null;
            $room_types[] = $room_type;
        }
        return view('TMSClient.home.tour.create',compact('room_types','countries','cities'));
    }

	public function simple_create(){

        $countries = Country::all();
        $cities = City::all();
      
        $room_typesDvo = RoomTypes::query()->orderBy('sort_order', 'ASC')->get();
        $room_types = array();

        foreach ($room_typesDvo as $room_type){
            $room_type['count_room'] = null;
            $room_type['price_room'] = null;
            $room_types[] = $room_type;
        }
        return view('TMSClient.home.tour.simple_create',compact('room_types','countries','cities'));
    }
    public function generateExternalName($country_code, $id){
        return 'EETS' . $country_code . (100 + $id);
    }

    public function validateTour( $request ) {
        $endDate = '';
        if ($request->departure_date && $request->retirement_date){
            $departure_date = $request->departure_date;
            $retirement_date = $request->retirement_date;
            $endDate = Carbon::createFromFormat('Y-m-d', $departure_date)->addDays(29);
        }
        $this->validate( $request, [
            'name'            => 'required',
            'departure_date'  => 'required|before_or_equal:retirement_date',
            'retirement_date' => 'required|before_or_equal:' . $endDate,
            'pax'             => 'required|numeric',
//            'country_begin'   => 'required',
//            'city_begin'      => 'required',
//            'country_end'     => 'required',
//            'city_end'        => 'required',
 //           'pax_free'        => 'numeric',
        ] );   
    }
    public function show($id, Request $request){
	
        $tour = Tour::find($id);
	
        $status = Status::where('id', $tour->status)->first();
      
        $tourDates = $this->prepareTourPackages($tour, $request)['tourDates'];
		 
        $options = $this->services;
		
		
        return view("TMSClient.home.tour.show",compact("tour","tourDates","options"));
    }

    public function prepareTourPackages($tour, Request $request)
    {
        $tourDates = TourDay::with('packages')->where('tour', $tour->id)->get()->sortBy('date');
        //echo (count($tourDates))."<br>";
        $tourPackageType = TourService::$serviceTypes;
        $last = '';

        foreach ($tourDates as $tourDate) {
            if ($request->pdf_type == 'voucher') $tourDate->packages = $tourDate->packages->where('description_package', null);
            $last_package = $tourDate->packages->last();
            if($last_package) $last = $last_package->id;
            foreach ($tourDate->packages as $package) {
            	if ($package->status) {
		            $package->status = $package->getStatusName();
	            }
                $package->paid = $package->paid ? 'Yes' : 'No';
                // $package->type = $tourPackageType[$package->type];
                $package->issued_by = $request->user()->name??"";
                // $package->assigned_user = User::findOrFail($tour->assigned_user)->name;
            }
        }
        if($request->input('exclude') > 0 && $request->pdf_type  !== 'voucher' ) {
            foreach ($tourDates as $tourDate) {

                if ($request->pdf_type == 'voucher') $tourDate->packages = $tourDate->packages->where('description_package', null);

                foreach ($tourDate->packages as $id => $package) {
                    if (in_array($package->id, $request->input('exclude'))) {
                        unset($tourDate->packages[$id]);
                    }
                }

            }
        }

        return ['tourDates' => $tourDates, 'tour' => $tour, 'last' => $last];
    }
}
