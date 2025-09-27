<?php

namespace App\Http\Controllers;

use App\Helper\LaravelFlashSessionHelper;
use App\Tour;
use Illuminate\Http\Request;
use App\Templates;
use Redirect;
use Illuminate\Support\Facades\Mail;
// use Ddeboer\Imap\Server; // Package not installed
use App\TourDay;
use App\TourPackage;

class RoomListController extends Controller {

    public $client;

    public function __construct() {
        // IMAP functionality disabled - package not installed
        /*
        $server = new Server(
            env('IMAP_HOST', 'localhost'),
            env('IMAP_PORT'),
            '/imap/tls/novalidate-cert'
        );

        $this->client = $server->authenticate(
            env('IMAP_USERNAME', 'root@example.com'),
            env('IMAP_PASSWORD', 'root@example.com')
        );
        */

        $this->middleware('preventBackHistory');
        $this->middleware('auth');
    }

    /**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
	    //
    }

    public function show( $id ) {
	    $template = Templates::where('name',$id)->first();
	    ($template) ? $data = ['content' => $template->content] : $data = ['content' => 'empty'];
        return response()->json($data);
    }

	public function create( $tourId ) {
		$template = new Templates();
		$template->service_id = 8;
		$template->name = $tourId;
		$template->content = '';
		$template->save();

        LaravelFlashSessionHelper::setFlashMessage("Templates #$template->name created");

        return redirect()->route('tour.show', ['id' => $tourId, 'tab' => 'room_list' ]);
	}

    public function update( Request $request ) {
       $id = $request->tourId;
       $content = $request->roomlist_textarea;
       $template =  Templates::where('name',$id)->first();
       $template->content = $content;
       $template->save();

       LaravelFlashSessionHelper::setFlashMessage("Templates #$template->name edited");

       return redirect()->route('tour.show', ['id' => $id, 'tab' => 'room_list' ]);
    }
    
    public function send($tourId, Request $request ) {
        
        $hotelIds = $request->hotelIds;
        $params =[];
        $emails_array = [];
        $broken_emails_array = [];
        $broke = '';
        $template = Templates::where('name',$tourId)->first();
        $content = $template->content;
        $tourDates = TourDay::where('tour', $tourId)->whereNull('deleted_at')->orderBy('date')->get();
        
        if(!$hotelIds){
            $data = ['error' => 'error' , 'message' => 'Please select hotels!'];
            return response()->json($data);
        } else{
            foreach($hotelIds as $hid){
                $tourPackage = TourPackage::find($hid);
                $hotel = \App\Hotel::find($tourPackage->reference);
                array_push($emails_array, $hotel->work_email);
            }
        }
        if(count($emails_array) == 0) {
            $data = ['error' => 'error' , 'message' => 'No emails found'];
            return response()->json($data);
        }

        $params['emails_array'] = array_unique($emails_array);

        Mail::send('email.mail_template', compact('content'), function($message) use ($params){
            $subject = 'Guest List';
            $message->to($params['emails_array']);
            $message->from('gini@eetstravel.com', 'EETS Travel');
            $message->sender('gini@eetstravel.com', 'EETS Travel');
            $message->subject($subject);
            $sendedMailbox = $this->client->getMailbox('INBOX.Sent');
            $sendedMailbox->addMessage($message->getSwiftMessage()->toString());
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
                $data = ['error' => 'clear' , 'message' => trans('main.Allemailsweresent')];
            }

        }

        return response()->json($data);
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

    public function store( Request $request ) {
        //
    }

    public function edit( $id ) {
        //	return view( 'quotation.edit', compact( 'quotation', 'listRoomsHotel') );
    }


}
