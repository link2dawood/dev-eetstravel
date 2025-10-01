<?php

namespace App\Http\Controllers\TMSClient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Tour;
use App\Client;
use Session;
use Illuminate\Support\Facades\Crypt;

class LoginController extends Controller
{
    //
   
	
    public function index(){
	
        return view('TMSClient.auth.index');
    }
    
    
 /*  public function customLogin(Request $req){
	
		$clients = Client::where("contact_email", $req->contact_email)->get();
		foreach($clients as $client){
			$client = $client->name;
		}
		
		if(!empty($client) and $req->password == "password"){
			 return redirect()->route('TMS-Client.home')->with([
                'message' => 'Your logged in successfully',
                'alert-type' => 'success'
            ]);
		}
		else{
			 return redirect("TMS-Client/login")->withSuccess('Login details are not valid');
		}
		dd();
        $auth = Auth::user();
       
        $credentials = $req->only('email', 'password');
      
        if (Auth::attempt($credentials)) {
          
           
            return redirect()->route('home')->with([
                'message' => 'Your logged in successfully',
                'alert-type' => 'success'
            ]);
            
        }
  
        return redirect("/login")->withSuccess('Login details are not valid');
       
    }      */
public function Clientauth(Request $request)
{
	
    try {
        $contact_email = $request->input('contact_email');
        $password = Crypt::encryptString($request->input('password'));

        $password = Crypt::decryptString($password );
		
		$client = Client::where("contact_email", $contact_email)->first();
	
		$client_password = "";
		
		if($client){
			$client_password = Crypt::decryptString($client->password );
		}
       // $client = DB::select("SELECT * FROM clients WHERE contact_email = ? AND password = ?`", [$contact_email, $password]);
     	
        if ($client and $password == $client_password) {
            $request->session()->put('CLIENT_LOGIN', true);
            $request->session()->put('CLIENT_ID', $client->id);
            
            return redirect()->route('TMS-Client.home')->with([
                'message' => 'You are logged in successfully',
                'alert-type' => 'success'
            ]);
        } else {
            return redirect('TMS-Client/login')->with('error', 'Invalid email or password.');
        }
    } catch (\Exception $e) {
        dd($e->getMessage());
    }
}




    public function home()
		
    {
	
   
		 $tours = Tour::where("status",46)->get();
            return view("TMSClient.home.tour.index",compact("tours"));
        if(Auth::check()){
            $tours = Tour::where("status",46)->get();
            return view("TMSClient.home.tour.index",compact("tours"));
        }
  
        return redirect("TMS-Client/login")->withSuccess('You are not allowed to access');
    }

    public function quotation_requests(Request $request)
    {
        $client_id = $request->session()->get("CLIENT_ID");

        if(!$client_id) {
            return redirect("TMS-Client/login")->withSuccess('You are not allowed to access');
        }

        // Get tours for the specific client with status 46 (requested) and 6
        $tours = Tour::whereIn('status', [6, 46])
                     ->where("client_id", $client_id)
                     ->get();

        // Process tours data to include action buttons and formatted data
        $toursData = $tours->map(function ($tour) {
            // Add action buttons
            $tour->action_buttons = '<div class="d-flex align-items-center gap-2">
                                        <a href="'.route('TMS-Client-tours.show', ['id' => $tour->id]).'" class="action-link btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>';

            // Format departure date
            $tour->formatted_departure_date = $tour->departure_date ?
                \Carbon\Carbon::parse($tour->departure_date)->format('Y-m-d') : '';

            // Get status information
            $tour->status_name = $tour->getStatusName();
            $tour->status_color = $tour->getStatusColor();

            // Add status CSS class based on status
            if ($tour->status_name == 'Requested') {
                $tour->status_class = 'status pending';
            } elseif ($tour->status_name == 'Cancelled') {
                $tour->status_class = 'status rejected';
            } else {
                $tour->status_class = 'status active';
            }

            return $tour;
        });

        return view("TMSClient.home.tour.quotation_requests", compact("toursData"));
    }
	
	public function signOut() {
		
        Session::flush();
        
  
        return Redirect('TMS-Client/login');
    }
}
