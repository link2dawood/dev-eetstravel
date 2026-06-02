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
        // (formerly: dd() + dead code that was never reached after the redirect above)
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

    try {
        $contact_email = $request->input('contact_email');
        $password = Crypt::encryptString($request->input('password'));

        $password = Crypt::decryptString($password );
		
		$hotel = Hotel::where("contact_email", $contact_email)->first();
	
		$hotel_password = "";
		
		if($hotel){
			$hotel_password = Crypt::decryptString($hotel->password );
		}

       // $client = DB::select("SELECT * FROM clients WHERE contact_email = ? AND password = ?`", [$contact_email, $password]);
     	
        if ($hotel and $password == $hotel_password) {
		
            $request->session()->put('SUPPLIER_LOGIN', true);
            $request->session()->put('SUPPLIER_ID', $hotel->id);
            $request->session()->put('package_name', $hotel->name);
			
            return redirect()->route('TMS-Supplier.home')->with([
                'message' => 'You are logged in successfully',
                'alert-type' => 'success'
            ]);
        } else {
            return redirect('TMS-Supplier/login')->with('error', 'Invalid email or password.');
        }
    } catch (\Exception $e) {
        \Log::error('TMS-Supplier login failed', ['error' => $e->getMessage()]);
        return redirect("TMS-Supplier/login")->with('error', 'Login failed. Please try again.');
    }
}




    /**
     * Build the list of TourPackages assigned to the authenticated supplier.
     *
     * The previous implementation walked packages → tour_day → tour →
     * getTourDaysSortedByDate() → packages_tour_days → package and could surface
     * the same package once per tour day it appeared on. It also ran ~N*M
     * single-row queries.
     *
     * The set of packages we want is exactly: TourPackages whose name equals
     * the authenticated supplier's name AND which are linked to at least one
     * tour day. We resolve that set in a single query, then eager-load the
     * tour name and status name to feed the view.
     */
    public function home(Request $request)
    {
        $package_name = $request->session()->get('package_name');

        $packages = TourPackage::query()
            ->where('name', $package_name)
            ->whereIn('id', function ($q) {
                $q->select('tour_package_id')->from('packages_tour_days');
            })
            ->get();

        // Eager-load tour name + status name without N+1 lookups inside Blade.
        $tourDayLinks = DB::table('packages_tour_days')
            ->whereIn('tour_package_id', $packages->pluck('id'))
            ->get()
            ->keyBy('tour_package_id');

        $tourDayIds   = $tourDayLinks->pluck('tour_day_id')->unique()->all();
        $tourDays     = TourDay::whereIn('id', $tourDayIds)->get()->keyBy('id');
        $tours        = Tour::whereIn('id', $tourDays->pluck('tour')->unique())->get()->keyBy('id');
        $statusNames  = Status::whereIn('id', $packages->pluck('status')->unique())->pluck('name', 'id');

        $offers = $packages->map(function ($pkg) use ($tourDayLinks, $tourDays, $tours, $statusNames) {
            $link    = $tourDayLinks->get($pkg->id);
            $tourDay = $link ? $tourDays->get($link->tour_day_id) : null;
            $tour    = $tourDay ? $tours->get($tourDay->tour) : null;
            $pkg->tourName   = $tour ? ($tour->name ?? '') : '';
            $pkg->statusName = $statusNames[$pkg->status] ?? '';
            return $pkg;
        })->values();

		return view("TMSSupplier.home.index", compact('offers'));
    }
	
	public function signOut() {
		
        Session::flush();
        
  
        return Redirect('TMS-Supplier/login');
    }
}
