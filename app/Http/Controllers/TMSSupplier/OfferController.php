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
        //$url = array('show'       => route('TMS-Client-tours.show', ['id' => $id]));

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
	
	public function  addComment(Request $req, $id){
	
	  $package = TourPackage::find($id);

		DB::table("supplier_comments")->insert([
			"supplier_name" =>$package->name,
			"content" => $req->comment,
				
			]);
		$lastRecord = DB::table('supplier_comments')->orderBy('id', 'desc')->first();
       
		$data = "<li>
									<strong>".$lastRecord->supplier_name."</strong> said:
									<p>".$lastRecord->content."</p>
								</li>";
		return $data;
	}
	/*
	public function data(Request $request)
    {
		dd();

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