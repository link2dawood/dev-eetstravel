<?php
namespace App\Helper;
use App\Offices;
use App\Status;
use App\TourRoomTypeHotel;
use App\User;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Eloquent\Model;
use App\TourDay;
use App\TourPackage;
use PDF;
use Carbon\Carbon;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Illuminate\Http\Response;
use View;
use HTMLPurifier;
use HTMLPurifier_Config;

trait ExportTrait{

    /**
    * export tour
    * @param  Model  $tour   specific tour
    * @param  string $export document type
    * @return void
    */
	public function prepareExport($tour, string $export,  $request = null ){
        $this->request =$request;
        $excelName = str_replace(" ","_",$tour->name);
        
        // For Laravel Excel v3.x, we need to use export classes
        // Create a simple export class on the fly
        $tourExport = new class($tour) implements \Maatwebsite\Excel\Concerns\WithMultipleSheets {
            protected $tour;
            
            public function __construct($tour) {
                $this->tour = $tour;
            }
            
            public function sheets(): array {
                return [
                    new class($this->tour) implements \Maatwebsite\Excel\Concerns\FromView {
                        protected $tour;
                        public function __construct($tour) { $this->tour = $tour; }
                        public function view(): \Illuminate\Contracts\View\View {
                            return view('export.export', ['tour' => $this->tour]);
                        }
                    },
                    new class($this->tour) implements \Maatwebsite\Excel\Concerns\FromView {
                        protected $tour;
                        public function __construct($tour) { $this->tour = $tour; }
                        public function view(): \Illuminate\Contracts\View\View {
                            // Root fix: Pass both tour and tourDates to the view
                            $tourDates = \App\TourDay::get(['id', 'date', 'tour'])->where('tour', $this->tour->id)->sortBy('date');
                            return view('export.package', ['tour' => $this->tour, 'tourDates' => $tourDates]);
                        }
                    },
                ];
            }
        };
        
        return Excel::download($tourExport, 'Tour_'.$excelName.'.'.$export);
	}
    /**
     * export tour or services to csv
     * @param  $tour 
     * @param  $type for what export calls
     * @return mixed
     */
    public function csvExport($tour, string $type, $request = null ){
        $this->request =$request;
        $excelName = str_replace(" ","_",$tour->name);
        if ($type == 'tour') {
            $export = new class($tour) implements \Maatwebsite\Excel\Concerns\FromView {
                protected $tour;
                public function __construct($tour) { $this->tour = $tour; }
                public function view(): \Illuminate\Contracts\View\View {
                    return view('export.export', ['tour' => $this->tour]);
                }
            };
            return Excel::download($export, 'Tour_'.$excelName.'.csv', \Maatwebsite\Excel\Excel::CSV);
        } else {
            $export = new class($tour) implements \Maatwebsite\Excel\Concerns\FromView {
                protected $tour;
                public function __construct($tour) { $this->tour = $tour; }
                public function view(): \Illuminate\Contracts\View\View {
                    $tourDates = \App\TourDay::get(['id', 'date', 'tour'])->where('tour', $this->tour->id)->sortBy('date');
                    return view('export.package', ['tourDates' => $tourDates, 'tour' => $this->tour]);
                }
            };
            return Excel::download($export, 'Services_'.$excelName.'.csv', \Maatwebsite\Excel\Excel::CSV);
        }
    }
    public function exportPdfVoucher($tour, $data, $request)
    {
		$office=Offices::where('status',1)->first();
        $issued_time = Carbon::now()->format('Y-m-d');
        $tourDays = TourDay::where('tour', $tour->id)->get()->sortBy('date');
        $checkedExcludeVch = [];

        $exclude_cvh = $request->get('exclude_vch', []);


            foreach ($tour->transfers as $id => $transfer){
                if ($package->vch == 0) {
                    unset($tour->transfers[$id]);
                }
            }

            foreach ($tourDays as $tourDate) {

                if ($request->pdf_type == 'voucher') $tourDate->packages = $tourDate->packages->where('description_package', null);
                foreach ($tourDate->packages as $id => $package) {
                    if ($package->vch == 0) {
                        unset($tourDate->packages[$id]);
                    }
                }

            }
        
        
        if ($request->input('exclude_vch')) $checkedExcludeVch = $request->input('exclude_vch');

        $listRoomsHotel = TourRoomTypeHotel::where('tour_id', $tour->id )->get();

        view()->share([
			'office'=>$office,
            'tour' => $tour,
            'tourDays' => $tourDays,
            'issued' => $issued_time,
            'last' => $data['last'],
            'checkedExcludeVch' => $checkedExcludeVch,
            'param' => 'someVal',
            'listRoomsHotel' => $listRoomsHotel
        ]);
        PDF::setOptions(['isHtml5ParserEnabled' => true]);
        $pdf = PDF::loadView('export.pdf_voucher');
//        return $pdf->download('tour_voucher_list.pdf');


        $pdfName = 'voucher_'.$tour->name.'.pdf';
        return $pdf->download(str_replace(" ","_",$pdfName));
    }
	
	 public function exportVoucherdoc($tour, $data, $request)
    {
		 $office=Offices::where('status',1)->first();
        $issued_time = Carbon::now()->format('Y-m-d');
        $tourDays = TourDay::where('tour', $tour->id)->get()->sortBy('date');
        $checkedExcludeVch = [];

        $exclude_cvh = $request->get('exclude_vch', []);

        // Filter transfers by vch flag
        if ($tour->transfers && is_iterable($tour->transfers)) {
            foreach ($tour->transfers as $id => $transfer){
                if (isset($transfer->vch) && $transfer->vch == 0) {
                    unset($tour->transfers[$id]);
                }
            }
        }

        // Filter packages by vch flag
        foreach ($tourDays as $tourDate) {
            if ($request->doc_type == 'voucher' || $request->pdf_type == 'voucher') {
                $tourDate->packages = $tourDate->packages->where('description_package', null);
            }
            foreach ($tourDate->packages as $id => $package) {
                if (isset($package->vch) && $package->vch == 0) {
                    unset($tourDate->packages[$id]);
                }
            }
        }
        
        
        if ($request->input('exclude_vch')) $checkedExcludeVch = $request->input('exclude_vch');

        $listRoomsHotel = TourRoomTypeHotel::where('tour_id', $tour->id )->get();

     
        $htmlContent =  View::make('export.doc_voucher', [
			'office'=>$office,
            'tour' => $tour,
            'tourDays' => $tourDays,
            'issued' => $issued_time,
            'last' => $data['last'],
            'checkedExcludeVch' => $checkedExcludeVch,
            'param' => 'someVal',
            'listRoomsHotel' => $listRoomsHotel,
			'office' => $office,
        ])->render();
	
// Sanitize the HTML content
$config = HTMLPurifier_Config::createDefault();
// Root fix: Set writable cache directory for HTMLPurifier
$cacheDir = storage_path('app/htmlpurifier');
if (!is_dir($cacheDir)) {
    @mkdir($cacheDir, 0755, true);
}
$config->set('Cache.SerializerPath', $cacheDir);
$purifier = new HTMLPurifier($config);

// Sanitize the HTML content
$sanitizedHtml = $purifier->purify($htmlContent);
    // Create a new PHPWord object
    $phpWord = new PhpWord();

    // Add content to the Word document
$section = $phpWord->addSection();
	 $section->getStyle()->setMarginLeft(1000);

    \PhpOffice\PhpWord\Shared\Html::addHtml($section, $sanitizedHtml);
ini_set('upload_max_filesize', '62M');
ini_set('post_max_size', '62M');
    // Save the document to a temporary file
    $tempFile = storage_path('app/temp.docx');
    $phpWord->save($tempFile, 'Word2007');

    // Define the response to return the Word document
     $response = new Response(file_get_contents($tempFile));
    $response->header('Content-Type', 'octet-stream');
    $response->header('Content-Disposition', 'attachment; filename="Voucher.docx"');

    // Delete the temporary file after sending
    unlink($tempFile);

    return $response;


        $pdfName = 'voucher_'.$tour->name.'.pdf';
        return $pdf->download(str_replace(" ","_",$pdfName));
    }
    
    public function exportPdfHotels($tour, $data, $request)
    {
        $serviceTypes = [
            'hotel',
            'event',
            'guide',
            'transfer',
            'restaurant',
            'tourPackage',
            'cruise',
            'flight'
        ];
        $tourDays = TourDay::where('tour', $tour->id)->get()->sortBy('date');
        $selectedArray = $request->input('exclude_vch') ? $request->input('exclude_vch') : [];
        
        $tourTransfers = $tour->transfers;

  
            foreach ($tourDays as $tourDate) {

                if ($request->pdf_type == 'voucher') $tourDate->packages = $tourDate->packages->where('description_package', null);

                foreach ($tourDate->packages as $id => $package) {
                    if ($package->vch == 0) {
                        unset($tourDate->packages[$id]);
                    }
                }
            }
        

        $sum = 0;
        foreach ($tourDays as $tourDay) {
            $tourDay->queue = $sum;
            $sum++;
            $tourDay->packages = $tourDay->packages->sortBy('time_from');
            foreach ($tourDay->packages as $package) {               
//                $package->time_from = Carbon::parse($package->time_from)->format('g:i A');
            }
        }
        $usersResponsible = User::find($tour->responsible);
        
        $listRoomsHotel = \App\TourRoomTypeHotel::where('tour_id', $tour->id )->get();        
        
        view()->share(['tour' => $tour, 'tourDays' => $tourDays, 'tourTransfers' => $tourTransfers, 'listRoomsHotel' => $listRoomsHotel, 'usersResponsible' => $usersResponsible, 'serviceTypes' => $serviceTypes]);
        // $data = ['tour' => $tour, 'tourDays' => $tourDays];
        PDF::setOptions(['isHtml5ParserEnabled' => true ]);
       // $pdf = PDF::loadView('export.pdf_simple');
        $pdf = PDF::loadView('export.bootstrap_hotels');
        //return $pdf->download('itinerary_list.pdf');
        $pdfName = 'hotels_'.$tour->name.'.pdf';
        return $pdf->download(str_replace(" ","_",$pdfName));
    }
	public function exportHotelsdoc($tour, $data, $request)
    {
		$office=Offices::where('status',1)->first();
        $serviceTypes = [
            'hotel',
            'event',
            'guide',
            'transfer',
            'restaurant',
            'tourPackage',
            'cruise',
            'flight'
        ];
        $tourDays = TourDay::where('tour', $tour->id)->get()->sortBy('date');
        $selectedArray = $request->input('exclude_vch') ? $request->input('exclude_vch') : [];
        
        $tourTransfers = $tour->transfers;

  
            foreach ($tourDays as $tourDate) {

                if ($request->pdf_type == 'voucher') $tourDate->packages = $tourDate->packages->where('description_package', null);

                foreach ($tourDate->packages as $id => $package) {
                    if ($package->vch == 0) {
                        unset($tourDate->packages[$id]);
                    }
                }
            }
        

        $sum = 0;
        foreach ($tourDays as $tourDay) {
            $tourDay->queue = $sum;
            $sum++;
            $tourDay->packages = $tourDay->packages->sortBy('time_from');
            foreach ($tourDay->packages as $package) {               
//                $package->time_from = Carbon::parse($package->time_from)->format('g:i A');
            }
        }
        $usersResponsible = User::find($tour->responsible);
        
        $listRoomsHotel = \App\TourRoomTypeHotel::where('tour_id', $tour->id )->get();        
        
        $htmlContent =  View::make('export.doc_hotels', ['tour' => $tour, 'tourDays' => $tourDays, 'tourTransfers' => $tourTransfers, 'listRoomsHotel' => $listRoomsHotel, 'usersResponsible' => $usersResponsible, 'serviceTypes' => $serviceTypes,'office' => $office])->render();
	
// Sanitize the HTML content
$config = HTMLPurifier_Config::createDefault();
// Root fix: Set writable cache directory for HTMLPurifier
$cacheDir = storage_path('app/htmlpurifier');
if (!is_dir($cacheDir)) {
    @mkdir($cacheDir, 0755, true);
}
$config->set('Cache.SerializerPath', $cacheDir);
$purifier = new HTMLPurifier($config);

// Sanitize the HTML content
$sanitizedHtml = $purifier->purify($htmlContent);
    // Create a new PHPWord object
    $phpWord = new PhpWord();

    // Add content to the Word document
$section = $phpWord->addSection();
	 $section->getStyle()->setMarginLeft(1000);

    \PhpOffice\PhpWord\Shared\Html::addHtml($section, $sanitizedHtml);
ini_set('upload_max_filesize', '62M');
ini_set('post_max_size', '62M');
    // Save the document to a temporary file
    $tempFile = storage_path('app/temp.docx');
    $phpWord->save($tempFile, 'Word2007');

    // Define the response to return the Word document
     $response = new Response(file_get_contents($tempFile));
    $response->header('Content-Type', 'octet-stream');
    $response->header('Content-Disposition', 'attachment; filename="Hotel List.docx"');

    // Delete the temporary file after sending
    unlink($tempFile);

    return $response;
    }

    public function exportHtmlShort($tour, $data, $request)
    {
        $serviceTypes = [
            'hotel',
            'event',
            'guide',
            'transfer',
            'restaurant',
            'tourPackage',
            'cruise',
            'flight'
        ];
        $tourDays = TourDay::where('tour', $tour->id)->get()->sortBy('date');
//        $selectedArray = $request->input('exclude_vch') ? $request->input('exclude_vch') : [];

        $tourTransfers = $tour->transfers;

        if(is_array($request->input('exclude')) AND count($request->input('exclude')) > 0 ) {
            $exclude = $request->input('exclude');
        } else  {
            $exclude = [];
        }
        /*
                if(count($request->input('exclude')) > 0 ) {
                    foreach ($tourDays as $tourDate) {

                        if ($request->pdf_type == 'voucher') $tourDate->packages = $tourDate->packages->where('description_package', null);

                        foreach ($tourDate->packages as $id => $package) {
                            if (in_array($package->id, $request->input('exclude'))) {
                                unset($tourDate->packages[$id]);
                            }
                        }
                    }
                }

        */
        /*
                $sum = 0;
                foreach ($tourDays as $tourDay) {
                    $tourDay->queue = $sum;
                    $sum++;
                    $tourDay->packages = $tourDay->packages->sortBy('time_from');
        //            foreach ($tourDay->packages as $package) {
        //                $package->time_from = Carbon::parse($package->time_from)->format('g:i A');
        //            }
                }
        */
//        $usersResponsible = User::query()->where('id', $tour->responsible)->first();
        $usersResponsible = User::find($tour->responsible);

        $listRoomsHotel = \App\TourRoomTypeHotel::where('tour_id', $tour->id )->get();
		        $tourDates = TourDay::get(['id', 'date', 'tour'])->where('tour', $tour->id)->sortBy('date');
        $arr = array();
        $i = 0;
        foreach ($tourDates as $tourDate){
            $tour_packages = collect();
            foreach ($tourDate->packages as $package_item){
                $package_item['time_from_new'] = (new Carbon($package_item->time_from))->format('H:i:s');
                $tour_packages->push($package_item);
            }
            // dd($tourDate->packages->sortBy('time_from'));
            $tourDate->packages = $tour_packages->sortBy('time_from_new');
            foreach ($tourDate->packages as $package){
                $arr[] = $package->status;
                $package->time_from = $this->convertDateToHoursMinute($package->time_from);
                $package->time_to = $this->convertDateToHoursMinute($package->time_to);
            }
        }
        $statusPackages = Status::query()->whereIn('id', $arr)->orderBy('sort_order')->get(); 
            
        view()->share([ 'tour' => $tour,
                        'serviceTypes' => $serviceTypes,
                        'tourDays' => $tourDays,
                        'tourTransfers' => $tourTransfers,
                        'usersResponsible' => $usersResponsible,
                        'listRoomsHotel' => $listRoomsHotel,
                        'statusPackage' => $statusPackages,
                        'exclude' => $exclude,'isHtml' => false ]);        // $data = ['tour' => $tour, 'tourDays' => $tourDays];

        $setting = [ 'tour' => $tour,
            'serviceTypes' => $serviceTypes,
            'tourDays' => $tourDays,
            'tourTransfers' => $tourTransfers,
            'usersResponsible' => $usersResponsible,
            'listRoomsHotel' => $listRoomsHotel,
            'exclude' => $exclude ];


        return view('export.html', compact('setting'));

    }
    
    public function exportPdfShort($tour, $data, $request)
    {
		$office=Offices::where('status',1)->first();
        $serviceTypes = [
            'hotel',
            'event',
            'guide',
            'transfer',
            'restaurant',
            'tourPackage',
            'cruise',
            'flight'
        ];
        $tourDays = TourDay::where('tour', $tour->id)->get()->sortBy('date');
//        $selectedArray = $request->input('exclude_vch') ? $request->input('exclude_vch') : [];
        
        $tourTransfers = $tour->transfers;

        $exclude = $request->get('exclude', []);
/*        
        if(count($request->input('exclude')) > 0 ) {
            foreach ($tourDays as $tourDate) {

                if ($request->pdf_type == 'voucher') $tourDate->packages = $tourDate->packages->where('description_package', null);

                foreach ($tourDate->packages as $id => $package) {
                    if (in_array($package->id, $request->input('exclude'))) {
                        unset($tourDate->packages[$id]);
                    }
                }
            }
        }

*/
/*        
        $sum = 0;
        foreach ($tourDays as $tourDay) {
            $tourDay->queue = $sum;
            $sum++;
            $tourDay->packages = $tourDay->packages->sortBy('time_from');
//            foreach ($tourDay->packages as $package) {
//                $package->time_from = Carbon::parse($package->time_from)->format('g:i A');
//            }
        }
*/
//        $usersResponsible = User::query()->where('id', $tour->responsible)->first();
        $usersResponsible = User::find($tour->responsible);

        $listRoomsHotel = \App\TourRoomTypeHotel::where('tour_id', $tour->id )->get();        
        $tourDates = TourDay::get(['id', 'date', 'tour'])->where('tour', $tour->id)->sortBy('date');
        $arr = array();
        $i = 0;
        foreach ($tourDates as $tourDate){
            $tour_packages = collect();
            foreach ($tourDate->packages as $package_item){
                $package_item['time_from_new'] = (new Carbon($package_item->time_from))->format('H:i:s');
                $tour_packages->push($package_item);
            }
            // dd($tourDate->packages->sortBy('time_from'));
            $tourDate->packages = $tour_packages->sortBy('time_from_new');
            foreach ($tourDate->packages as $package){
                $arr[] = $package->status;
                $package->time_from = $this->convertDateToHoursMinute($package->time_from);
                $package->time_to = $this->convertDateToHoursMinute($package->time_to);
            }
        }
        $statusPackages = Status::query()->whereIn('id', $arr)->orderBy('sort_order')->get(); 
        //   'isDoc' => true,'download_name'=>"test." 
        view()->share([ 'tour' => $tour,
					   'office'=>$office,
                        'serviceTypes' => $serviceTypes,
                        'tourDays' => $tourDays,
                        'tourTransfers' => $tourTransfers,
                        'usersResponsible' => $usersResponsible,
                        'listRoomsHotel' => $listRoomsHotel,
                        'statusPackage' => $statusPackages,
                        'exclude' => $exclude,'isHtml' => false,]);
        // $data = ['tour' => $tour, 'tourDays' => $tourDays];



        $setting = [ 'tour' => $tour,
            'serviceTypes' => $serviceTypes,
            'tourDays' => $tourDays,
            'tourTransfers' => $tourTransfers,
            'usersResponsible' => $usersResponsible,
            'listRoomsHotel' => $listRoomsHotel,
            'exclude' => $exclude ];

       // return view('export.bootstrap', compact('setting'));

        PDF::setOptions(['isHtml5ParserEnabled' => true,'defaultPaperSize' =>'a3']);
       // $pdf = PDF::loadView('export.pdf_simple');
//return View('export.bootstrap');
        $pdf = PDF::loadView('export.bootstrap');

        //return $pdf->download('itinerary_list.pdf');

/*
        $setting = [ 'tour' => $tour,
            'serviceTypes' => $serviceTypes,
            'tourDays' => $tourDays,
            'tourTransfers' => $tourTransfers,
            'usersResponsible' => $usersResponsible,
            'listRoomsHotel' => $listRoomsHotel,
            'exclude' => $exclude ];
        return view('export.bootstrap', compact('setting'));*/

        $pdfName = 'itinerary_'.$tour->name.'.pdf';
        return $pdf->stream(str_replace(" ","_",$pdfName));
    }


public function exportDocShort($tour, $data, $request)
{
	$office=Offices::where('status',1)->first();
    $serviceTypes = [
        'hotel',
        'event',
        'guide',
        'transfer',
        'restaurant',
        'tourPackage',
        'cruise',
        'flight'
    ];
    $tourDays = TourDay::where('tour', $tour->id)->get()->sortBy('date');
    $tourTransfers = $tour->transfers;

    if (is_array($request->input('exclude')) && count($request->input('exclude')) > 0) {
        $exclude = $request->input('exclude');
    } else {
        $exclude = [];
    }

    $usersResponsible = User::find($tour->responsible);
    $listRoomsHotel = \App\TourRoomTypeHotel::where('tour_id', $tour->id)->get();
    
    $tourDates = TourDay::get(['id', 'date', 'tour'])->where('tour', $tour->id)->sortBy('date');
    $arr = [];
    $i = 0;
    foreach ($tourDates as $tourDate) {
        $tour_packages = collect();
        foreach ($tourDate->packages as $package_item) {
            $package_item['time_from_new'] = (new Carbon($package_item->time_from))->format('H:i:s');
            $tour_packages->push($package_item);
        }
        $tourDate->packages = $tour_packages->sortBy('time_from_new');
        foreach ($tourDate->packages as $package) {
            $arr[] = $package->status;
            $package->time_from = $this->convertDateToHoursMinute($package->time_from);
            $package->time_to = $this->convertDateToHoursMinute($package->time_to);
        }
    }
    $statusPackages = Status::query()->whereIn('id', $arr)->orderBy('sort_order')->get(); 

    // Share the variables with the view
	$isHtml =true;
    $htmlContent =  View::make('export.doc', [ 'tour' => $tour,
                        'serviceTypes' => $serviceTypes,
											  'office'=>$office,
                        'tourDays' => $tourDays,
                        'tourTransfers' => $tourTransfers,
                        'usersResponsible' => $usersResponsible,
                        'listRoomsHotel' => $listRoomsHotel,
                        'statusPackage' => $statusPackages,
                        'exclude' => $exclude,'isHtml' => false ])->render();
 $htmlContent2 = view('export.doc', [ 'tour' => $tour,
                        'serviceTypes' => $serviceTypes,
											  'office'=>$office,
                        'tourDays' => $tourDays,
                        'tourTransfers' => $tourTransfers,
                        'usersResponsible' => $usersResponsible,
                        'listRoomsHotel' => $listRoomsHotel,
                        'statusPackage' => $statusPackages,
                        'exclude' => $exclude,'isHtml' => false ])->render();
	
// Sanitize the HTML content
$config = HTMLPurifier_Config::createDefault();
// Root fix: Set writable cache directory for HTMLPurifier
$cacheDir = storage_path('app/htmlpurifier');
if (!is_dir($cacheDir)) {
    @mkdir($cacheDir, 0755, true);
}
$config->set('Cache.SerializerPath', $cacheDir);
$purifier = new HTMLPurifier($config);

// Sanitize the HTML content
$sanitizedHtml = $purifier->purify($htmlContent);
    // Create a new PHPWord object
    $phpWord = new PhpWord();

    // Add content to the Word document
$section = $phpWord->addSection();
	 $section->getStyle()->setMarginLeft(1000);

    \PhpOffice\PhpWord\Shared\Html::addHtml($section, $sanitizedHtml);
ini_set('upload_max_filesize', '62M');
ini_set('post_max_size', '62M');
    // Save the document to a temporary file
    $tempFile = storage_path('app/temp.docx');
    $phpWord->save($tempFile, 'Word2007');

    // Define the response to return the Word document
     $response = new Response(file_get_contents($tempFile));
    $response->header('Content-Type', 'octet-stream');
    $response->header('Content-Disposition', 'attachment; filename="Tour Itenary.docx"');

    // Delete the temporary file after sending
    unlink($tempFile);

    return $response;
}


}