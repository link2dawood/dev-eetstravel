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

    public function quotation_requests()
    {

   
		 $tours = Tour::where("status",46)->get();
            return view("TMSClient.home.tour.quotation_requests",compact("tours"));
        if(Auth::check()){
            $tours = Tour::where("status",46)->get();
            return view("TMSClient.home.tour.quotation_requests",compact("tours"));
        }
  
        return redirect("TMS-Client/login")->withSuccess('You are not allowed to access');
    }
	
	public function signOut() {
		
        Session::flush();
        
  
        return Redirect('TMS-Client/login');
    }
}
