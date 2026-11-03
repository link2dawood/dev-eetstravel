<?php

namespace App\Http\Controllers\TMSSupplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Tour;
use App\Hotel;
use App\TourPackage;
use App\TourDay;
use App\Status;
use Session;
use Illuminate\Support\Facades\Crypt;

class LoginController extends Controller
{
    //
   
	
    public function index(){
	
        return view('TMSSupplier.auth.index');
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
public function Supplierauth(Request $request)
{

    $this->validate($request, [
        'contact_email' => 'required|email',
        'password' => 'required|string',
    ]);

    $hotel = Hotel::where("contact_email", $request->input('contact_email'))->first();

    if ($hotel && $this->supplierPasswordMatches($hotel, (string) $request->input('password'))) {
        $request->session()->regenerate();
        $request->session()->put('SUPPLIER_LOGIN', true);
        $request->session()->put('SUPPLIER_ID', $hotel->id);
        $request->session()->put('package_name', $hotel->name);

        return redirect()->route('TMS-Supplier.home')->with([
            'message' => 'You are logged in successfully',
            'alert-type' => 'success'
        ]);
    }

    return redirect('TMS-Supplier/login')->with('error', 'Invalid email or password.');
}

/**
 * Supplier portal passwords are bcrypt hashes. Older ones were stored with Crypt (reversible);
 * those still work once and are re-hashed on that login. An empty stored password never matches.
 */
private function supplierPasswordMatches(Hotel $hotel, string $password)
{
    $stored = (string) $hotel->password;
    if ($stored === '' || $password === '') {
        return false;
    }

    if (password_get_info($stored)['algo']) {
        return \Illuminate\Support\Facades\Hash::check($password, $stored);
    }

    try {
        $legacy = Crypt::decryptString($stored);
    } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
        return false;
    }
    if ($legacy === '' || !hash_equals($legacy, $password)) {
        return false;
    }

    $hotel->password = \Illuminate\Support\Facades\Hash::make($password);
    $hotel->save();

    return true;
}




    public function home(Request $request)
    {
        $package_name = $request->session()->get("package_name");
        $packages = TourPackage::where("name", $package_name)->get();
        $offers = [];

        foreach($packages as $pkg){
            $tourday = DB::select('select * from packages_tour_days where tour_package_id = :tour_package_id', ['tour_package_id' =>  $pkg->id]);
            if(!empty($tourday)){
                $tourday = TourDay::find($tourday[0]->tour_day_id);
                $tour = Tour::find($tourday->tour);

                if(!empty($tour)){
                    $tourdates = $tour->getTourDaysSortedByDate() ?? "";
                    foreach($tourdates as $tourdate){
                        $tourdays = DB::select('select * from packages_tour_days where tour_day_id = :tour_day_id', ['tour_day_id' => $tourdate->id]);
                        foreach($tourdays as $tourday_item){
                            $package = TourPackage::find($tourday_item->tour_package_id);
                            if(!empty($package) && $package->name == $package_name){
                                $package->tourName = $tour->name ?? "";
                                $status = Status::where("id", $package->status)->first();
                                $package->statusName = $status ? $status->name : "";
                                $offers[] = $package;
                            }
                        }
                    }
                }
            }
        }

		return view("TMSSupplier.home.index", compact('offers'));
    }

    public function quotation_requests()
    {

   
		
            return view("TMSSupplier.home.index");
        if(Auth::check()){
            $tours = Tour::where("status",46)->get();
            return view("TMSClient.home.tour.quotation_requests",compact("tours"));
        }
  
        return redirect("TMS-Client/login")->withSuccess('You are not allowed to access');
    }
	
	public function signOut() {
		
        Session::flush();
        
  
        return Redirect('TMS-Supplier/login');
    }
}
