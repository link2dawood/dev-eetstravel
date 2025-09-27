<?php

namespace App\Http\Controllers;
use App\Currencies;
use App\TourPackage;
use App\RoomTypes;
use Illuminate\Support\Facades\Crypt;
use App\Repository\Contracts\TourPackageRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Status;
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
use App\Tour;
use App\Helper\TourPackage\TourService;
use App\Http\Imap\ImapClient;
use Carbon\Carbon;
use App\Helper\LaravelFlashSessionHelper;
use Illuminate\Support\Facades\Session;
use Auth;
use View;
use Illuminate\Support\Facades\Mail;
use App\Helper\PermissionHelper;
use Yajra\Datatables\Datatables;
class BookingRequestController extends Controller
{
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
	public function __construct( TourPackageRepository $tourPackageRepository)
    {
        $this->tourPackageRepository = $tourPackageRepository;
    }
	public function getShowButton($offer, array $perm,$supplier)
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
		$tour = $package->getTour();	
		$status_name = '';
		$menu = '';
		
		$button = '';
			if($offer->supplier_delete == 0 && $supplier == 1){
				$button = '<button class="delete btn btn-danger btn-sm" style="margin-right: 5px" data-toggle="modal" data-target="#myModal" data-link="/offer/'.$offer->id.'/supplier_delete"><i class="fa fa-trash-o"></i></button>';
			}
			else if($supplier == 0){
				$button = '<button class="delete btn btn-danger btn-sm" style="margin-right: 5px;" data-info="' . htmlspecialchars(json_encode($package ?: ' ')) . '"
    onclick="loadTemplate(JSON.parse((this.getAttribute(\'data-info\')) ? JSON.parse((this.getAttribute(\'data-info\'))).type : \'\'), \'' . htmlspecialchars($package->service()->work_email) . '\', \'' . htmlspecialchars($package->name) . '\', \'' . htmlspecialchars($package->pax . ' ' . $package->pax_free) . '\', \'\', \'' . htmlspecialchars($package->service()->work_email) . '\', \'' . htmlspecialchars($package->service()->work_phone) . '\', \'' . htmlspecialchars($package->description) . '\', \'' . htmlspecialchars($status_name) . '\', \'' . htmlspecialchars($package->time_from) . '\', \'' . htmlspecialchars($package->time_to) . '\', \'' . htmlspecialchars($package->supplier_url) . '\', \'' . htmlspecialchars($package->total_amount) . '\', \'' . htmlspecialchars($menu) . '\', \'' . htmlspecialchars($tour->id) . '\', \'' . htmlspecialchars($package->reference) . '\', \'' . htmlspecialchars($tour->name) . '\', \'' . htmlspecialchars($package->id) . '\', \'' . htmlspecialchars($offer->id) . '\');"
    class="btn btn-success btn-xs"
><i class="fa fa-envelope" aria-hidden="true"></i></button>';
				
				$button .= '<a class="btn btn-warning btn-sm show-button" href="https://dev.eetstravel.com/offer/'.$offer->id.'/show" data-link="https://dev.eetstravel.com/tour/5"><i class="fa fa-info-circle"></i></a>';
			}
			return $button;
		
	}
	public function data($id, $supplier)
	{
		
		$sevenDaysAgo = Carbon::now()->subDays(7);
		$roomTypes = RoomTypes::all();

		$desiredStatuses = ["Offered with Option", "Offered No rooms blocked"];
		$offers = HotelOffers::where('package_id', $id)->get();

		$permission_destroy = PermissionHelper::$relationsPermissionDestroy['App\Invoices'];
		$permission_edit = PermissionHelper::$relationsPermissionEdit['App\Invoices'];
		$permission_show = PermissionHelper::$relationsPermissionShow['App\Invoices'];

		$perm = [
		];

		$dataTable = Datatables::of($offers);

		foreach ($roomTypes as $roomType) {
			$columnName = $roomType->code;

			$dataTable->addColumn($columnName, function ($offer) use ($roomType) {
				foreach ($offer->offer_room_prices as $offerRoomPrice) {
					if ($offerRoomPrice->room_type_id == $roomType->id) {
						return $offerRoomPrice->price;
					}
				}
				return "N/A";
			});
		}

		$dataTable->addColumn('hotel_name', function ($offers) use ($perm) {
			$package = TourPackage::find($offers->package_id);
			if (empty($package)) {
				$offers->delete();
			}
			return $package->name ?? "";
		});

		// ... (continue adding other columns)

		$dataTable->addColumn('tour_name', function ($offers) use ($perm) {
			$tour = Tour::find($offers->tour_id);
			$tour_name = "";
			if (!empty($tour)) {
				$tour_name = $tour->name ?? "";
			}
			return $tour_name;
		});

		$dataTable->addColumn('action', function ($offers) use ($perm,$supplier) {
			 return $this->getShowButton($offers,$perm,$supplier);
			
		});
		$dataTable ->addColumn('status_tms', function ($offers) use ($perm) {
			return $offers->getStatusName($offers->tms_status);
        });	
		return $dataTable->rawColumns(['select', 'action', 'link'])->make(true);
	}
	public function setConnectionToServer()
    {
        try {
            
                $this->server = new ImapClient(
                    env('IMAP_HOST', 'webmail.eetstravel.com'),
                    "booking-test@eetstravel.com",
                    "Ffans82guQ9h",
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
	public function generated_link($genrated_id,$id){
		


		

		$currencies = Currencies::all();
		
		$tour_package = TourPackage::find($id);
		
		if(empty($tour_package)){
			return "";
		}
	


		$emails = $this->getEmails($id);
		$tms_emails = $this->tmsEmails($id);

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
		

		return view("booking_request.booking_request",compact('tour_package','selected_room_types','currencies','comments','statuses','emails','tms_emails','room_typesDvo'));
	}
	public function sendMailtoAll($email,$request){
		//dd($request->all());
		$requestData = $request->all();
		$tour_package = TourPackage::find($request->package_id);

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
		
		Mail::send('email.offer_template', compact('tour_package','selected_room_types','requestData'), function ($message) use ($request, $email,$tp) {
                        $to = $email;
                        $toArray = explode(',', $to);
                        $subject = $tp->getTour()->name." Request #".$request->package_id;

                        $fio = Auth::user();
                        $client_name = $fio ? $fio->name : 'EETS Travel';

                        $message->to($toArray);
                        $message->from("service@eetstravel.com", $client_name);
                        $message->sender("service@eetstravel.com", $client_name);
                        if ($request->file('attachment')) {
                            foreach ($request->file('attachment') as $attachment) {
                                $message->attach($attachment->getRealPath(), [
                                    'as' => $attachment->getClientOriginalName(),
                                    'mime' => $attachment->getMimeType()
                                ]);
                            }
                        }
                        $message->subject($subject);
			//$message->setBody($content, 'text/html');
                    });
		
	}
	public function offerUpdate(Request $request , $id){
		$emails  = $request->emails;
		$emailArray = json_decode($emails, true);
		$package = TourPackage::find($id);
		$tour = $package->getTour();
		
		foreach ($tour->users as $t_user) {
			$email = $t_user->email;
			if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
				$this->sendMailtoAll($email, $request);
			}
		}
        if(!empty($emailArray)){
		foreach($emailArray as $email){
			$emailValue = $email["value"];

			if (!filter_var($emailValue, FILTER_VALIDATE_EMAIL)) {
			   Session::flash('error', 'Error.');
				$successMessage = 'Please Provid valid email address';
				return redirect()->back()->withError($successMessage);
			}
			
		}
		foreach($emailArray as $email){
			$this->sendMailtoAll( $email["value"], $request);
		}
			 
            
		}
		
		$filename = "";
		if ($request->hasFile('supplier_file') && $request->file('supplier_file')->isValid()) {
			 
            $file = $request->file('supplier_file');
            $filename = $file->getClientOriginalName();
            $path = $file->move('booking_request', $filename); // Store the file in the "uploads" directory
		

        }
		
		
		

		$hotelOffer = HotelOffers::create([
            'package_id' => $id,
			'tour_id' => $tour->id,
            'status' => $request->status,
			'option_date' => $request->option_with_date,
			'currency' => $request->currency,
            'city_tax' => $request->city_tax,
            'halfboard' => $request->halfboard,
			'foc_after_every_pax' => $request->foc_after_every_pax,
			'children_cost' => $request->children_cost,
			'halfboardMax' => $request->halfboardMax,
            'portrage_perperson' => $request->portrage_perperson,
			'other_coditions' => $request->otherConditions,
            'hotel_file' => $filename,
            'hotel_note' => $request->hotel_note,
			'cancellationNote' => $request->cancellationNote,
        ]);
		if( $request->status == 'Waiting List'){
			$package->status = 15;
			TourPackage::where('parent_id',$id)->update([
                'status' => 15,
            ]);
		}
		if( $request->status == 'Unavailable'){
			$package->status = 34;
			TourPackage::where('parent_id', $id)->update([
                'status' => 34,
            ]);
		}
		if( $request->status == 'Offered with Option'){
			$package->status = 43;
			TourPackage::where('parent_id', $request->package_id)->update([
                'status' => 43,
            ]);
			$assigned_user = [];
					$task = new Task();
					//  $task->content = 'Confirm reservation of '.$tour_package->name.' before '. $request->date;
					$task->content = $package->name." Offered with Option "." on Tour: ".$tour->name;
					$task->start_time = Carbon::now(); // Set the start date to the current date and time
					$dep_date = Carbon::parse($tour->departure_date);
					
					$task->dead_line = $request->option_with_date; 

					$task->tour = $tour->id;
					// $task->assign = $user[$request->get('assign')];
					//$task->assign = Auth::user()->id;
					$task->task_type = 1;
					$task->status = 2;
					$task->priority = 1;

					
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
		$package->save();
			
			$room_types = $request->room_type_id;
			if(!empty($room_types)){
			foreach($room_types as $room_type){
				
				$price = "room_rate_".$room_type;
				$price = $request->$price;
				$is_breakfast = "is_breakfast_".$room_type??0;
				$is_breakfast = $request->$is_breakfast??0;
				
				if($is_breakfast =="on"){
					$is_breakfast = 1;
				}
				if(!empty($price)){
				DB::table('offer_room_prices')->insert([
						'offer_id' => $hotelOffer->id,
						'room_type_id' => $room_type,
						'price' =>$price,
						'is_breakfast' =>$is_breakfast,
					]);
				}
			}
			}
			$cancellation_percentage = $request->cancellation_percentage;
			$cancellation_type = $request->cancellation_type;
			$cancellation_days = $request->cancellation_days;
			$i = 0;
		
			if(!empty($cancellation_days)){
			foreach ($cancellation_days as $cancellation_day) {
				try {
					DB::table('offer_cancellation_policies')->insert([
						'offer_id' => $hotelOffer->id,
						'cancellation_percentage' => $cancellation_percentage[$i],
						'cancellation_type' => $cancellation_type[$i],
						'cancellation_days' => $cancellation_day,
					]);
					if( $request->status == 'Offered No rooms blocked'){
					$assigned_user = [];
					$task = new Task();
					//  $task->content = 'Confirm reservation of '.$tour_package->name.' before '. $request->date;
					$task->content = $cancellation_day . ' days before arrival: '.$cancellation_percentage[$i].$cancellation_type[$i].' of rooms can be cancelled free of charge of '.$package->name;
					$task->start_time = Carbon::now(); // Set the start date to the current date and time
					$dep_date = Carbon::parse($tour->departure_date);
					
					$task->dead_line = $dep_date->addDays($cancellation_day); // Set the deadline date by adding days to the current date

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
		
			$deposit_percentage = $request->deposit_percentage;
			$deposit_type = $request->deposit_type;
			$deposit_days = $request->deposit_days;
			$i = 0;
			if(!empty($deposit_days)){
			foreach ($deposit_days as $deposit_day) {
				try {
					DB::table('offer_payment_policies')->insert([
						'offer_id' => $hotelOffer->id,
						'deposit_percentage' => $deposit_percentage[$i],
						'deposit_type' => $deposit_type[$i],
						'deposit_days' => $deposit_day,
					]);
					
					if( $request->status == 'Offered No rooms blocked'){
					$assigned_user = [];
					$task = new Task();
					//  $task->content = 'Confirm reservation of '.$tour_package->name.' before '. $request->date;
					$task->content =$deposit_percentage[$i]. $deposit_type[$i].' can be paid before '.$deposit_day.' days before arrival of '.$package->name;
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
		/*

			$package->total_amount = $request->price_person;
		$package->hotel_status = $request->hotel_status;
		$package->status = $request->hotel_status;
		$package->hotel_note = $request->hotel_note;
		$package->is_expired = 1;
			if(!empty($filename)){
		$package->hotel_file = 'booking_request/' . $filename;
			}
		$package->save();
					*/
		
		$successMessage = 'You are succefully submitted the request';
		 LaravelFlashSessionHelper::setFlashMessage("Offer Created", 'success');
Session::flash('success', 'Record has been successfully created.');
		return redirect()->back()->withSuccess($successMessage);
	}
	public function addemails(Request $request,$package_id){

        $additionalEmails = $request->input('additionalEmail');
		$this->validate($request, [
        'additionalEmail' => 'required',
    ]);

        // Execute a raw SQL query to insert the data
		DB::table('emails_data')->where("package_id", $package_id)->delete();
		foreach($additionalEmails as $additionalEmail){
        DB::insert('INSERT INTO emails_data (package_id, additional_email) VALUES (?, ?)', [$package_id, $additionalEmail]);
		}
		$successMessage = 'Emails Added Successfully';
      LaravelFlashSessionHelper::setFlashMessage("Emails Added", 'success');
Session::flash('success', 'Emails has been added successfully.');
		return redirect()->back()->withSuccess($successMessage);
	}
	
	public function getaddEmails(Request $request)
    {
		
	
        $package_id = $request->get('package_id');
		$additional_emails = DB::table('emails_data')->where('package_id', $package_id)->get();
		
        $view = View::make('booking_request.additional_emails_from', compact('additional_emails'));

        return $view->render();
    }
}