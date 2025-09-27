<?php

namespace App\Http\Controllers;

use App\Helper\LaravelFlashSessionHelper;
use App\GuestList;
use App\Hotel;
use App\Tour;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Redirect;
use Yajra\Datatables\Datatables;
use Ddeboer\Imap\Server;
use Illuminate\Support\Facades\Mail;

class GuestListController extends Controller {


    /**
     * QuotationController constructor.
     */
    public $client;

    public function __construct() {
        //$server = new Server(
           // env('IMAP_HOST', 'localhost'),
           // env('IMAP_PORT'),
          //  '/imap/tls/novalidate-cert'
      //  );

       // $this->client = $server->authenticate(
          //  env('IMAP_USERNAME', 'root@example.com'),
          //  env('IMAP_PASSWORD', 'root@example.com')
        //);
        $this->middleware('permissions.required');
        $this->middleware('preventBackHistory');
        $this->middleware('auth');
    }
    
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
        $title = 'Index - guest list';
        $guestList = \App\GuestList::all();
                //Quotation::query()->get();

        return view('guest_list.index', compact('guestList', 'title'));
	}



	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create( $tourId ) {
	
		$tour = Tour::findOrFail( $tourId );

		return view( 'guest_list.create', compact( 'tour') );
	}
	

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 *
	 * @return \Illuminate\Http\Response
	 */
    
    
    
	public function store( Request $request ) {
       $id = $request->tourId;
       $content = $request->roomlist_textarea;
       $guestList = new GuestList;
       $guestList->name         =  $request->name;
       $guestList->hotel_ids    =  implode(',', $request->hotelIds);
       $guestList->content      =  $content;
       $guestList->author_id    =  Auth::id();
       $guestList->tour_id      =  $id;
       $guestList->save();
       
       $guestList->version      =  $guestList->id;
       $guestList->save();

       LaravelFlashSessionHelper::setFlashMessage("Guest list #$request->name saved");

       return redirect()->route('tour.show', ['tour' => $id, 'tab' => 'room_list' ]);
	}

    
    public function send($tourId, $guestlistid = null, Request $request ) 
    {      
		
        if(!$guestlistid){
			
            $content = $request->roomlist_textarea;
            $guestList = new GuestList;
            $guestList->name         = $request->name;
            $guestList->hotel_ids    = implode(',', $request->hotelIds);
            $guestList->content      = $content;
            $guestList->author_id    = Auth::id();
            $guestList->tour_id      = $tourId;
            $guestList->sent_at      = Carbon::now(); 
            $guestList->save();

            $guestList->version      =  $guestList->id;
            $guestList->save();
            $hotelIds = $request->hotelIds;
        } else{
			
		
            $guestList = GuestList::find($guestlistid);
            $guestList->author_id    = Auth::id();
            $guestList->sent_at      = Carbon::now();
            $hotelIds                = explode(',', $guestList->hotel_ids);
            $guestList->save();
        }
		
        $params =[];
        $emails_array = [];
        $broken_emails_array = [];
        $broke = '';
        $content = $guestList->content;
	
       $tourDates = \App\TourDay::where('tour', $tourId)->whereNull('deleted_at')->orderBy('date')->get();
		
        if(!$hotelIds){
            $data = ['error' => 'error' , 'message' => 'Please select hotels!'];
            return response()->json($data);
        } else{
            foreach($hotelIds as $hid){
               $tourPackage = \App\TourPackage::find($hid);
                $hotel = \App\Hotel::find($tourPackage->service()->id);
                array_push($emails_array, $hotel->work_email);

            }
        }
		$params['emails_array'] = array_unique($emails_array);
		foreach($params['emails_array'] as $mail)
		{
			if($mail == "")
			{
				$data = ['error' => 'error' , 'message' => 'No emails found'];
				 return response()->json($data);
			}
			
		}
		
	 Mail::send('email.mail_template', compact('content'), function($message) use ($params){
            $subject = 'Guest List';
            $message->to($params['emails_array'] );
            $message->from('gini@eetstravel.com', 'EETS Travel');
            $message->sender('gini@eetstravel.com', 'EETS Travel');
            $message->subject($subject);
           //$sendedMailbox = $this->client->getMailbox('INBOX.Sent');
           // $sendedMailbox->addMessage($message->getSwiftMessage()->toString());
			
        });

       

        if (Mail::failures()) {
            $data = ['error' => 'error', 'message' => trans('main.Errorsendingemails')];
        }else{
            if(count($broken_emails_array)>0){
                $broken_emails_array_result = array_unique($broken_emails_array);
                foreach ($broken_emails_array_result as $name){
                    $broke .= $name.trans('main.emailwaswrongorempty');
                }
                $data = ['error' => 'clear' , 'message' => trans('main.Emailsweresent'), 'broke' => $broke];
            }else{
                $data = ['error' => 'clear' , 'message' => trans('main.Allemailsweresent'), 'sent_at' => Carbon::parse($guestList->sent_at)->format('d-m-Y')];
            }

        }

        return response()->json($data);
    }

    public function delete($tourId, $guestlistid){

	    $questlist = GuestList::findOrFail($guestlistid);

	    if($questlist ->delete()){
            return response()->json(['result'=>'succes']);
        }

        return response()->json(['result'=>'error']);

    }

	/**
	 * Display the specified resource.
	 *
	 * @param  int $id
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function show( $id ) {
		//
	}
    
	public function showById( $id ) {
        $guestList = GuestList::findorfail($id);
        $respHTML = $guestList->content;
        
        return $respHTML;
	}

	public function showHotelEmailsById( $id ) {
        $guestList = GuestList::findorfail($id);
        
        $respHTML = "<table style='width: 100%; padding: 0 50px 0 50px;'";
        
        $respHTML .="<tr>
                        <td> Emails - Hotels: </td>
                        <td> " . $guestList->getSelectedHotelNamesEmails() . "</td>
                    </tr>";
        
        $respHTML .= '</table>';
        
        return $respHTML;
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int $id
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function edit( $id ) {
	}

	/**
	 * Update the specified resource in storage.
	 * @ToDo: add removing
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  int $id
	 *
	 * @return \Illuminate\Http\Response
	 */
	
    public function update( Request $request ) {

    }
	

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int $id
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function destroy( $id ) {
		//
	}

}
