<?php

namespace App\Http\Controllers\TMSSupplier;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;
use App\Helper\PermissionHelper;
use App\Tour;
use App\TourDay;
use App\TourPackage;
use App\Hotel;
use App\Status;
use View;
use App\Http\Controllers\DatatablesHelperController;
class OfferController extends Controller
{
	public function getShowButton($id, $isQuotation = false, $dataArray, array $perm)
    {
        //$url = array('show'       => route('TMS-Client-tours.show', ['TMS_Client_tour' => $id]));

        return '<div class="d-flex align-items-center gap-2">
                      <a href="'.$dataArray->supplier_url.'" class="action-link btn-primary" >
                        <i class="fas fa-eye"></i>
                      </a>
                    </div>';
//        return DatatablesHelperController::getActionButton($url, $isQuotation, $tour);
    }
	public function data_request(Request $request){

		$package_name = $request->session()->get("package_name");
		$package = TourPackage::where("name",$package_name)->get();
		$dataArray = [];
		foreach($package as $pkg){
			$tourday = DB::select('select * from packages_tour_days where tour_package_id = :tour_package_id', ['tour_package_id' =>  $pkg->id]);
		$tourday = TourDay::find($tourday[0]->tour_day_id);
		$tour = Tour::find($tourday->tour);
			
			if(!empty($tour)){
				$tourdates =  $tour->getTourDaysSortedByDate()??"";
				foreach($tourdates as $tourdate){
				$tourdays = DB::select('select * from packages_tour_days where tour_day_id = :tour_day_id', ['tour_day_id' => $tourdate->id]);
				foreach($tourdays as $tourday){
					
					$packagee = TourPackage::find($tourday->tour_package_id);
					if(!empty($packagee)  && $packagee->name == $package_name){
					 $dataArray[] = $packagee;
					}
				}
				}
			}
				
		}

		$permission_destroy = PermissionHelper::$relationsPermissionDestroy['App\Tour'];
        $permission_edit = PermissionHelper::$relationsPermissionEdit['App\Tour'];
        $permission_show = PermissionHelper::$relationsPermissionShow['App\Tour'];
    
        $perm = [];        
        $perm['show'] = true;        
        $perm['edit'] = true;
        $perm['destroy'] = true;
        $perm['clone'] = true;
		return Datatables::of($dataArray)->addColumn('action', function ($dataArray) use($perm) {
                return $this->getShowButton($dataArray->id, false, $dataArray, $perm);
			
            })
			->addColumn('tourName', function ($dataArray) {
				
               $tourday = DB::select('select * from packages_tour_days where tour_package_id = :tour_package_id', ['tour_package_id' =>  $dataArray->id]);
		$tourday = TourDay::find($tourday[0]->tour_day_id);
				$tour = Tour::find($tourday->tour);
					
				return $tour->name??"";
            })
			->addColumn('statusName', function ($dataArray) {
				$status = Status::where("id",$dataArray->status)->first();
         
					
				return $status->name;
            })
            
            ->make(true);

	}
	
	/**
	 * Append a comment to a package the authenticated supplier owns.
	 *
	 * Hardening (AUDIT.md follow-up):
	 *   - The route now sits inside the `supplierauth` middleware group, so the
	 *     session-stored SUPPLIER_ID is guaranteed present here.
	 *   - The package whose comment is being posted must belong to the
	 *     authenticated supplier — matched by package.name == hotel.name, which
	 *     is the same lookup home() uses to surface offers. Without this an
	 *     authenticated supplier could post comments on *any* package by
	 *     guessing the route id.
	 *   - The comment body is validated (required, max 5000 chars) and stripped
	 *     of any HTML before being persisted.
	 *   - The response is built with htmlspecialchars() so a hostile payload
	 *     can't echo back as live HTML.
	 */
	public function addComment(Request $req, $id)
	{
		$req->validate([
			'comment' => 'required|string|max:5000',
		]);

		$package = TourPackage::find($id);
		if (!$package) {
			return response('Package not found.', 404);
		}

		// Ownership check: the supplier in the session must match the package
		// they're commenting on. The portal joins packages → suppliers by name
		// (see LoginController@home), so we use the same join key here.
		$supplierId   = $req->session()->get('SUPPLIER_ID');
		$supplierName = $req->session()->get('package_name');
		$hotel        = $supplierId ? Hotel::find($supplierId) : null;
		if (!$hotel || $package->name !== $supplierName) {
			return response('Forbidden.', 403);
		}

		// Strip HTML tags from the input. The DB column has no markup contract
		// and the response interpolates the same value back into HTML, so
		// removing tags here removes both stored-XSS and reflected-XSS vectors.
		$body = trim(strip_tags((string) $req->comment));
		if ($body === '') {
			return response('Comment is empty.', 422);
		}

		DB::table('supplier_comments')->insert([
			'supplier_name' => $hotel->name,
			'content'       => $body,
		]);

		// Build the response with explicit escaping. Don't trust DB round-trip
		// to sanitise — keep the encoding at the trust boundary.
		$nameEsc = htmlspecialchars($hotel->name, ENT_QUOTES, 'UTF-8');
		$bodyEsc = nl2br(htmlspecialchars($body, ENT_QUOTES, 'UTF-8'));
		return '<li><strong>' . $nameEsc . '</strong> said:<p>' . $bodyEsc . '</p></li>';
	}
	/*
	public function data(Request $request)
    {
        // (formerly: dd() at method entry, which short-circuited every call)

        //if (Auth::user()->hasRole('admin')) {
       
		

        //} else {
            //$tours = $this->repository->allForAssigned();
        //}

     

        return Datatables::of($package)->addColumn('action', function ($package) use($package) {
		
                return $this->getShowButton($package->id, false, $package, $perm);
			
            })
			->addColumn('tourName', function ($package) {
				
               $tourday = DB::select('select * from packages_tour_days where tour_package_id = :tour_package_id', ['tour_package_id' =>  $package->id]);
		$tourday = TourDay::find($tourday[0]->tour_day_id);
				$tour = Tour::find($tourday->tour);
		
				return $tourday->tour??"";
            })
            
            ->make(true);
    }
	*/
}