<?php

namespace App\Http\Controllers\TMSClient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Tour;
use App\Client;
use Session;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ExcelDataImport;

class UploadController extends Controller
{


public function uploadTourFile(Request $request)
{

    $client_id = $request->session()->get("CLIENT_ID");

    if ($request->hasFile('file')) {
		
        $file = $request->file('file');
		$filePath = $file->getRealPath();
		Excel::load($filePath, function($reader) use ($client_id)  {

    // Getting all results
    $results = $reader->get();

    // ->all() is a wrapper for ->get() and will work the same
    $results = $reader->all();
		foreach($results as $result){
			
			$tour = new Tour();
			$tour->name = $result->tour_name;
			$tour->departure_date = $result->dep_date;
			$tour->retirement_date = $result->ret_date;
			$tour->pax = $result->pax;
			$tour->pax_free = $result->paxfree;
			$tour->country_begin = $result->country_from;
			$tour->city_begin = $result->city_begin;
			$tour->country_end = $result->country_to;
			$tour->city_end = $result->city_end;
			
			$tour->external_name = $this->generateExternalName($result->country_from, $tour->id);
			
			$tour->status = 46;
			
			$tour->client_id = $client_id;
			
			//$tour->save();
		}

});

       // Excel::import(new TourImport, $file);
	
		

       return response()->json(['success' => 'Upload Successfully'], 200);
    }

     return response()->json(['error' => 'Please upload a file.'], 400);
	
}
	 public function generateExternalName($country_code, $id){
        return 'EETS' . $country_code . (100 + $id);
    }

	public function downloadSampleExcel()
    {
        $filePath = public_path('uploads/sample.xlsx');
        $fileName = 'sample.xlsx';

        return response()->download($filePath, $fileName);
    }
	public function file_viewer(Request $request){
		 if ($request->hasFile('fileget')) {
            $file = $request->file('fileget');
            $filename = $file->getPathname();

            if (($handle = fopen($filename, 'r')) !== FALSE) {
                $firstname = [];

                while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                    $firstname[] = $data;
                }

                fclose($handle);

                return response()->json($firstname);
            }
        }

        return response()->json(['error' => 'Please upload a file.'], 400);
	}
}
