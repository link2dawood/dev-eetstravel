<?php

namespace App\Http\Controllers;

use App\Email;
use App\Tour;
use App\Helper\LaravelFlashSessionHelper;
use Auth;
use Ddeboer\Imap\Exception\AuthenticationFailedException;
use Illuminate\Http\Request;
use App\Templates;
use App\User;
use App\TourPackage;
use App\RoomTypes;
use App\HotelOffers;
use App\Http\Controllers\TourPackageController;
use Illuminate\Support\Facades\Mail;
use Ddeboer\Imap\Server;
use App\Http\Imap\ImapClient;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class TemplatesController extends Controller
{
    public $client;
    public $server;
    public $user;

    public function __construct()
    {
        $this->middleware('preventBackHistory');
        $this->middleware('auth');
    }

    public $serviceTypes = [
        0   => 'hotel',
        1   => 'event',
        2   => 'guide',
        3   => 'bus company',
        4   => 'restaurant',
        5   => 'tourPackage',
        6   => 'description',
    ];

    public $tags = [
        ['pattern' => 'name', 'tag' => '##name##'],
        ['pattern' => 'date', 'tag' => '##date##'],
        ['pattern' => 'pax', 'tag' => '##pax##'],
        ['pattern' => 'address', 'tag' => '##address##'],
        ['pattern' => 'email', 'tag' => '##email##'],
        ['pattern' => 'phone', 'tag' => '##phone##'],
        ['pattern' => 'description', 'tag' => '##description##'],
        ['pattern' => 'status', 'tag' => '##status##'],
        ['pattern' => 'time_from', 'tag' => '##time_from##'],
        ['pattern' => 'time_to', 'tag' => '##time_to##'],
        ['pattern' => 'supplier_url', 'tag' => '##supplier_url##'],
        ['pattern' => 'price_for_one', 'tag' => '##price_for_one##'],
        ['pattern' => 'menu', 'tag' => '##menu##'],
        ['pattern' => 'roominglist', 'tag' => '##roominglist##'],
        ['pattern' => 'reference', 'tag' => '##reference##'],
        ['pattern' => 'tour_name', 'tag' => '##tour_name##'],
		['pattern' => 'roomingtypes', 'tag' => '##roomingtypes##'],
		['pattern' => 'arrival', 'tag' => '##arrival##'],
		['pattern' => 'offer_refno', 'tag' => '##offer_refno##'],
		['pattern' => 'offer_status', 'tag' => '##offer_status##'],
		['pattern' => 'offer_option_date', 'tag' => '##offer_option_date##'],
		['pattern' => 'offer_city_tax', 'tag' => '##offer_city_tax##'],
		['pattern' => 'offer_porterage_pp', 'tag' => '##offer_porterage_pp##'],
		['pattern' => 'offer_halfboard_pp', 'tag' => '##offer_halfboard_pp##'],
		['pattern' => 'offer_max_per_group', 'tag' => '##offer_max_per_group##'],
    ];
    public function setConnectionToServer()
    {

        $user = Auth::user();

        try {
           
                $this->server = new ImapClient(
                    env('IMAP_HOST', 'eetstravel.com'),
                    "booking-test@eetstravel.com",
                    "Ffans82guQ9h",
                    ImapClient::ENCRYPT_SSL
                );
           

            return $this->server->isConnected();
        } catch (AuthenticationFailedException $e) {
            echo "Authorization failed : " . $user->email_login . "\r\n";
        }
    }
    public function setClient()
    {
        $this->user = Auth::user();
        $emailServer = Auth::user()->email_server;
        try {
            if ($emailServer == Email::TYPE_EETS) {
                $this->client = new ImapClient(
                    env('IMAP_HOST', 'webmail.eetstravel.com'),
                    $this->user->email_login,
                    Crypt::decryptString($this->user->email_password),
                    ImapClient::ENCRYPT_SSL
                );
                return  $this->client;
            } else if ($emailServer == Email::TYPE_VIANET) {
                $this->vianetServer = $server = new Server(
                    env('VIANET_HOST', 'localhost'),
                    env('VIANET_PORT'),
                    env('VIANET_FLAGS')
                );
            } else {
                return false;
            }

            $this->client = $server->authenticate(
                (string) $this->user->email_login,
                (string) $this->user->email_password
            );
        } catch (AuthenticationFailedException $e) {
            return false;
        }
        return true;
    }

    public function index(Request $request)
    {

        $title = 'Index - Services Email Templates';

        // Get all service types data (same as the AJAX data method)
        $templatesData = [];
        $services = $this->serviceTypes;

        foreach ($services as $id => $service) {
            if ($service !== 'tourPackage') {
                $templatesData[] = (object) [
                    "name" => ucfirst($service),
                    "action_buttons" => "<div style='text-align: center;'><a class='btn btn-warning btn-sm show-button' href='" . route('templates.show', ['template' => $id]) . "' ><i class='fa fa-info-circle'></i></a></div>",
                    "id" => $id
                ];
            }
        }

        return view('templates.index', compact('title', 'templatesData'));
    }

    public function data(Request $request)
    {
        $json = [];

        $services = $this->serviceTypes;

        foreach ($services as $id => $service) {
            if ($service !== 'tourPackage') {
                $json[] = [
                    "name" => ucfirst($service),
                    "action" => "<div style='text-align: center;'><a class='btn btn-warning btn-sm show-button' href='" . route('templates.show', ['template' => $id]) . "' ><i class='fa fa-info-circle'></i></a></div>",
                    "id" => $id
                ];
            }
        }

        $data = ["data" => $json];

        return response()->json($data);
    }

    public function show($template, Request $request)
    {
        $title = 'Show - Template';

        $header = Templates::query()->where('service_id', $template)->where('name', 'Header')->first();
        $footer = Templates::query()->where('service_id', $template)->where('name', 'Footer')->first();

        if (!$header) {
            $head = new Templates();
            $head->service_id = $template;
            $head->name = 'Header';
            $head->content = '<----- Place header content here ----->';
            $head->save();
            $header = Templates::query()->where('id', $head->id)->first();
        }

        if (!$footer) {
            $foot = new Templates();
            $foot->service_id = $template;
            $foot->name = 'Footer';
            $foot->content = '<----- Place footer content here ----->';
            $foot->save();
            $footer = Templates::query()->where('id', $foot->id)->first();
        }

        $templates = Templates::query()->where('service_id', $template)->get();

        foreach ($templates as $templateItem) {
            $templateItem->name = ucfirst($templateItem->name);
        }

        $service = (object) ["id" => $template, "name" => $this->serviceTypes[$template]];

        return view('templates.show', compact('title', 'templates', 'service', 'header', 'footer'));
    }

    public function update(Request $request, $id)
    {
        $template = Templates::query()->where('id', $id)->first();
        $template->name = ucfirst($request->input('name'));
        $template->content = $request->input('content');
        $template->save();

        LaravelFlashSessionHelper::setFlashMessage("Template $template->name edited", 'success');
        return redirect()->back();
    }

    public function store(Request $request)
    {
        $template = new Templates();
        $template->name = ucfirst($request->input('name'));
        $template->service_id = $request->input('id');
        $template->content = $request->input('content');
        $template->save();

        LaravelFlashSessionHelper::setFlashMessage("Template $template->name created", 'success');

        return redirect()->back();
    }

    public function destroy(Request $request, $id)
    {
        $template = Templates::query()->findOrFail($id);
        $template->delete();
        LaravelFlashSessionHelper::setFlashMessage("Template $template->name deleted", 'success');

        return redirect()->back();
    }
	public function loadDescTemplate(Request $request){
		$template = Templates::query()->where('id', $request->input('id'))->first();
		if ($template) {

            $content = $template->content;
			$data['content'] = $content;
		}

        return response()->json($data);
	}
    public function loadTemplate(Request $request)
    {
		
        $data = [];
		$rooming_types = '';
		$roomingtypes = RoomTypes::all();
		$count = 0;
			foreach($roomingtypes as $roomingtype){
				$count ++;
				 $rooming_types .= $count.":".$roomingtype->name." ";
			}
		
        $rooming_list = '';
        $template = Templates::query()->where('id', $request->input('id'))->first();
		$tp = TourPackage::find($request->input('package_id'));
		 $selected_room_types = array();
        if ($request->input('package_id')) {
			
		
				
		if($tp->type == 0){
                $room_types = array();
             
                $selected_room_types = array();

                foreach($tp->room_types_hotel as $item){
                    $item->room_types['count_room'] = $item->count;
                    $item->room_types['price_room'] = $item->price;
                    $selected_room_types[] = $item->room_types;
                }
            
		}
			$rooming_list = '<table  style="width: 18%; font-size: 20px;">';

			foreach ($selected_room_types as $selected_room_type) {
				$rooming_list .= '<tr>';
				$rooming_list .= '<td  width:150px; style="padding: 10px;">' . $selected_room_type["name"] . '</td>';
				$rooming_list .= '<td  width:150px; style="padding: 10px;">' . $selected_room_type["count_room"] . '</td>';
				$rooming_list .= '</tr>';
			}

			$rooming_list .= '</table>';
            
        }
		$data['arrival'] = "";
		$data['time_from'] = "";
        $data['time_to'] = "" ;
		if (!empty($tp)) {
		$data['emails'] = DB::table('hotel_contacts')
			->where('hotel_id', $request->service_id??1)
			->pluck('email')
			->toArray();
		}
		// Add the additional emails
		if (!empty($request->input('email'))) {
			$data['emails'][] = $request->input('email');
		}
		if (!empty($tp)) {
		if (!empty($tp->service()->contact_email)) {
			$data['emails'][] = $tp->service()->contact_email;
		}
		$tp['from_date'] = (new Carbon($tp->time_from))->format('F j, Y');
		$tp['from_time'] = (new Carbon($tp->time_from))->format('g:i A');
		$tp['to_date'] = (new Carbon($tp->time_to))->format('F j, Y');
		$tp['to_time'] = (new Carbon($tp->time_to))->format('g:i A');
		$data['arrival'] = $tp['from_time'];
		$data['time_from'] = $tp['from_date'];
        $data['time_to'] = $tp['to_date'] ;
		}
		
        $data['name'] = $request->input('name');
        $data['email'] = $request->input('email');
        $data['id'] = $request->input('id');
        $data['service_id'] = $request->input('service_id');
        $data['content'] = '';
        $data['pax'] = $request->input('pax');
        $data['address'] = $request->input('address');
        $data['emailto'] = $request->input('emailto');
        $data['phone'] = $request->input('phone');
        $data['description'] = $request->input('description');
        $data['status'] = $request->input('status');
        
        $data['supplier_url'] = $request->input('supplier_url')."/".$request->input('package_id');
        $data['price_for_one'] = $request->input('price_for_one');
        $data['menu'] = $request->input('menu');
        $data['roominglist'] = $rooming_list;
        $data['reference'] = $request->input('reference');
        $data['tour_name'] = $request->input('tour_name');
		$data['rooming_types'] = $rooming_types;
		
		
		$data['offer_refno'] = "";
		$data['offer_status'] = "";
		$data['offer_option_date'] = "";
		$data['offer_city_tax'] = "";
		$data['offer_porterage_pp'] = "";
		$data['offer_halfboard_pp'] = "";
		$data['offer_max_per_group'] = "";
		
		if($request->offer_id){
			$offer = HotelOffers::find($request->offer_id);
			$data['offer_refno'] = $offer->ref;
			$data['offer_status'] = $offer->status;
			$data['offer_option_date'] = $offer->option_date;
			$data['offer_city_tax'] = $offer->city_tax;
			$data['offer_porterage_pp'] = $offer->portrage_perperson;
			$data['offer_halfboard_pp'] = $offer->halfboard;
			$data['offer_max_per_group'] = $offer->halfboardMax;
			
		}

        if ($template) {

            $content = $template->content;

            foreach ($this->tags as $tag) {

                switch ($tag['pattern']) {
                    case 'name':
                        $content = str_replace($tag['tag'], $data['name'], $content);
                        break;
                    case 'date':
                        $content = str_replace($tag['tag'], (new \DateTime())->format('Y-m-d H:i:s'), $content);
                        break;
                    case 'pax':
                        $content = str_replace($tag['tag'], $data['pax'], $content);
                        break;
                    case 'address':
                        $content = str_replace($tag['tag'], $data['address'], $content);
                        break;
                    case 'email':
                        $content = str_replace($tag['tag'], $data['emailto'], $content);
                        break;
                    case 'phone':
                        $content = str_replace($tag['tag'], $data['phone'], $content);
                        break;
                    case 'description':
                        $content = str_replace($tag['tag'], $data['description'], $content);
                        break;
                    case 'status':
                        $content = str_replace($tag['tag'], $data['status'], $content);
                        break;
                    case 'time_from':
                        $content = str_replace($tag['tag'], $data['time_from'], $content);
                        break;
                    case 'time_to':
                        $content = str_replace($tag['tag'], $data['time_to'], $content);
                        break;
                    case 'supplier_url':
                        $content = str_replace($tag['tag'], $data['supplier_url'], $content);
                        break;
                    case 'price_for_one':
                        $content = str_replace($tag['tag'], $data['price_for_one'], $content);
                        break;
                    case 'menu':
                        $content = str_replace($tag['tag'], $data['menu'], $content);
                        break;
                    case 'roominglist':
                        $content = str_replace($tag['tag'], $data['roominglist'], $content);
                        break;
                    case 'reference':
                        $content = str_replace($tag['tag'], $data['reference'], $content);
                        break;
                    case 'tour_name':
                        $content = str_replace($tag['tag'], $data['tour_name'], $content);
                        break;
					case 'roomingtypes':
                        $content = str_replace($tag['tag'], $data['rooming_types'], $content);
                        break;
					case 'arrival':
						$content = str_replace($tag['tag'], $data['arrival'], $content);
						break;
					case 'offer_refno':
						$content = str_replace($tag['tag'], $data['offer_refno'], $content);
						break;
					case 'offer_status':
						$content = str_replace($tag['tag'], $data['offer_status'], $content);
						break;
					case 'offer_option_date':
						$content = str_replace($tag['tag'], $data['offer_option_date'], $content);
						break;
					case 'offer_city_tax':
						$content = str_replace($tag['tag'], $data['offer_city_tax'], $content);
						break;
					case 'offer_porterage_pp':
						$content = str_replace($tag['tag'], $data['offer_porterage_pp'], $content);
						break;
					case 'offer_halfboard_pp':
						$content = str_replace($tag['tag'], $data['offer_halfboard_pp'], $content);
						break;
					case 'offer_max_per_group':
						$content = str_replace($tag['tag'], $data['offer_max_per_group'], $content);
						break;
					
                }
            }

            $data['content'] = $content;
        }

        return response()->json($data);
    }

    public function sendTemplate(Request $request)
    {
	
        
            $this->setConnectionToServer();
            $server = $this->server;

       
            $user = Auth::user();
            $to = $request->email;
            $subject = $request->subject;
            $content = $request->templatesContent;
            $content = $content . '<p style="display:none">package_id :' . $request->package_id . '</p>';

			$tour_id = $request->tour_id;
            $tour = Tour::find($tour_id);
            Mail::send('email.mail_template', compact('content'), function ($message) use ($request, $server,$tour) {
                $to = $request->input('email');
                $toArray = explode(',', $to);
                $subject = $request->input('subject');

                $fio = Auth::user();
                $client_name = $fio ? $fio->name : 'EETS Travel';
				
                $message->to($toArray);
                $message->from("booking-test@eetstravel.com", $client_name);
                $message->sender("booking-test@eetstravel.com", $client_name);
                if ($request->file('attachment')) {
                    foreach ($request->file('attachment') as $attachment) {
                        $message->attach($attachment->getRealPath(), [
                            'as' => $attachment->getClientOriginalName(),
                            'mime' => $attachment->getMimeType()
                        ]);
                    }
                }
                $message->subject($subject);

                $server->saveMessageInSent($message->getSwiftMessage()->toString());
                return $message;
            });

            
            foreach ($tour->users as $t_user) {

                if (!empty($t_user->email)) {
                    Mail::send('email.mail_template', compact('content'), function ($message) use ($request, $t_user) {
                        $to = $t_user->email;
                        $toArray = explode(',', $to);
                        $subject = $request->input('subject');

                        $fio = Auth::user();
                        $client_name = $fio ? $fio->name : 'EETS Travel';

                        $message->to($toArray);
                        $message->from("booking-test@eetstravel.com", $client_name);
                        $message->sender("booking-test@eetstravel.com", $client_name);
                        if ($request->file('attachment')) {
                            foreach ($request->file('attachment') as $attachment) {
                                $message->attach($attachment->getRealPath(), [
                                    'as' => $attachment->getClientOriginalName(),
                                    'mime' => $attachment->getMimeType()
                                ]);
                            }
                        }
                        $message->subject($subject);
                    });
                }
            }
			
				$package = TourPackage::find($request->package_id );
			if($request->offer == 1){
				$package->status = 53;
				
				$offer = HotelOffers::find($request->offer_id);
				$offer->tms_status = 55;
				$offer->save();
			}else{
				$package->status = 3;
			}
				$package->save();
			 TourPackage::where('parent_id', $request->package_id)->update([
                'status' => 3,
            ]);
			
            // $sendedMailbox = $this->client->getMailbox('INBOX.Sent');
            // $sendedMailbox->addMessage($message->getSwiftMessage()->toString());
            LaravelFlashSessionHelper::setFlashMessage("Email sended to ", 'success');

            $data = ['result' => 'Email sended successfully'];

            return response()->json($data);
        
    }

    public function loadServiceTemplates(Request $request)
    {
      //  $this->setClient();
        $data = [];
        $templates = Templates::query()->where('service_id', $request->input('id'))->get();
        $data['templates'] = $templates;
        return response()->json($data);
    }
	public function replyEmail($userId,Request $request){
        try{
        $user = User::findOrFail($userId);
		$this ->setConnectionToServer($userId);
		
        $server = $this ->server;

        $email = $request->email_sent;
        
        $content = $request->body.'<p style="display:none">package_id :' . $request->package_id . '</p>';
        $mail = Mail::send( 'email.mail_template', compact( 'content' ), function ( $message) use ( $request, $user,$email, $server) {

            $client_name = $user ? $user->name : 'EETS Travel';
			
            $toArray   = explode( ',', $request->email_sent );
            $emails_to = [];
			
            foreach ( $toArray as $item ) {
                $emails_to[] = trim( $item );
            }
			$emails_to = $request->email_sent;
            $message->to($emails_to);
            $message->from("service@eetstravel.com", $client_name);
            $message->sender("service@eetstravel.com", $client_name);
            $message->replyTo($emails_to, $client_name );
			 
            if ( $request->file( 'files' ) ) {
                foreach ( $request->file( 'files' ) as $attachment ) {
                    $message->attach( $attachment->getRealPath(), [
                        'as'   => $attachment->getClientOriginalName(),
                        'mime' => $attachment->getMimeType()
                    ] );
                }
            }
            $message->subject( $request->email_subject );

			$server->saveMessageInSent($message->getSwiftMessage()->toString());
            
            return $message;
        } );
         LaravelFlashSessionHelper::setFlashMessage("Email sended to ", 'success');

            $data = ['result' => 'Email sended successfully'];

            return response()->json($data);
        } catch (\Exception $e) {
            echo "Email sending failed.";
            LaravelFlashSessionHelper::setFlashMessage("An Error Occur ", 'error');

            $data = ['result' => 'Email not sended successfully','error' => $e];

            return response()->json($data);
        }
	}
}
