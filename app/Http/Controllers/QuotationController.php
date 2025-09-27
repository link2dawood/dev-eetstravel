<?php

namespace App\Http\Controllers;

use App\Helper\LaravelFlashSessionHelper;
use App\Quotation;
use App\QuotationRow;
use App\QuotationValue;
use App\Tour;
use App\TourRoomTypeHotel;
use Auth;
use Carbon\Carbon;
use PDF;use Illuminate\Http\Request;
use Redirect;
use Yajra\Datatables\Datatables;
use Maatwebsite\Excel\Facades\Excel;
use App\TourDay;
class QuotationController extends Controller {

    /**
     * QuotationController constructor.
     */
    public function __construct()
    {
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
        $title = 'Index - quotation';
        $quotation = Quotation::query()->get();

        return view('quotation.index', compact('quotation', 'title'));
	}

    public function getButton($id, $quotation)
    {
        $url = [
            'edit' => route('quotation.edit', ['id' => $id]),
            'print' => route('quotation.pdf', ['id' => $id])
        ];

        return DatatablesHelperController::getQuotationListButtons($url, $quotation);
    }

    public function data(Request $request)
    {
        $quotations = Quotation::query();

        return Datatables::of($quotations
            ->leftJoin('tours', 'tours.id', '=', 'quotations.tour_id')
            ->leftJoin('users', 'users.id', '=', 'quotations.user_id')
            ->select([
                'quotations.id',
                'quotations.name',
                'quotations.tour_id',
                'quotations.created_at',
                'users.name as user_name'
            ]))
            ->addColumn('action', function ($quotation) {
                return $this->getButton($quotation->id, $quotation);
            })
            ->addColumn('user_name', function ($quotation) {
                return $quotation->user_name;
            })
            ->addColumn('tour_name', function ($quotation) {

                $tour = Tour::query()->where('id', $quotation->tour_id)->first();


                if(!$tour){
                    return '';
                }

                $tour_id = $tour->id;
                $tour_name = $tour->name;

                $link = route('tour.show', ['tour' => $tour_id]);
                return "<a href='{$link}' class='click_event' style='color: blue; text-decoration: underline!important; cursor: pointer'>$tour_name</a>";
//                if($tour->isMyTour()){
//                    return "<a href='{$link}' class='click_event' style='color: blue; text-decoration: underline!important; cursor: pointer'>$tour_name</a>";
//                }
//                    return "<a href='' class='click_event' style='color: grey; text-decoration: underline!important; cursor: pointer'>$tour_name</a>";
            })
            ->addColumn('created_at', function ($quotation) {
                $created_at = (new Carbon($quotation->created_at))->format('Y-m-d H:s');

                return $created_at;
            })
            ->addColumn('comparison', function ($quotation) {
                $link = route('comparison.show', ['id' => $quotation->id]);

                if(Auth::user()->can('comparison.show')){
                    $link = "<a href='{$link}' class='click_event' style='color: blue; text-decoration: underline!important; cursor: pointer'>Front Sheet</a>";
                }else{
                    $link = "<span>No permission</span>";
                }

                return $link;
            })
            ->rawColumns(['action', 'tour_name', 'comparison'])
            ->make(true);
    }

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create( $tourId ) {
		$tour = Tour::findOrFail( $tourId );
		$listRoomsHotel = TourRoomTypeHotel::where('tour_id', $tour->id )->get();
		$quotation = Quotation::query()->where('tour_id',$tourId)->latest()->first();
		return view( 'quotation.create', compact( 'tour', 'listRoomsHotel','quotation') );
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function store( Request $request ) {
		//
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

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int $id
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function edit( $id ) {
		$quotation = Quotation::findOrFail( $id );
		$listRoomsHotel = TourRoomTypeHotel::where('tour_id', $quotation->tour->id )->get();

		return view( 'quotation.edit', compact( 'quotation', 'listRoomsHotel') );
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
	public function update( Request $request, $id ) {
		$quotationName = $request->name;
		$quotation     = Quotation::findOrFail( $id );
		$quotation->calculation = $request->calculation;
		$quotation->additional_columns = $request->additional_columns;
		$quotation->additional_column_values = $request->additional_column_values;
		$quotation->calculation = $request->calculation;
		$quotation->note_show = $request->note_show == 'true'? true : false;
		$quotation->update($request->only('name', 'note', 'rate', 'mark_up', 'additional_persons'));
		foreach ( $request->data as $row ) {
			$rowId = $row['row_id'];
			unset( $row['row_id'] );
			foreach ( $row as $key => $value ) {
				$quotationValue        = QuotationValue::updateOrCreate(
					[
						'quotation_row_id' => $rowId,
						'key'              => $key
					],
					[
						'value' => $value??""
					]
				);
			}
		}

		LaravelFlashSessionHelper::setFlashMessage("Quotation #$quotation->name edited");
		$route = route('tour.show', ['tour' => $quotation->tour_id]);
       //return "$route?tab=quotation_tab";
		return route( 'quotation.edit', [ 'id' => $quotation->id ] );
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

	public function save( Request $request ) {
	    $user_id = Auth::user()->id;
		$tourId          = $request->tourId;
		$tour            = Tour::findOrFail( $tourId );
		$quotationData   = $request->data;
		$quotationName   = $request->name;
		$quotation       = new Quotation();
		$quotation->name = $quotationName;
		$quotation->user_id = $user_id;
		$quotation->tour()->associate( $tour )->save();
		foreach ( $quotationData as $row ) {
			$quotationRow = new QuotationRow();
			$quotationRow->quotation()->associate( $quotation )->save();
			foreach ( $row as $key => $value ) {
				$quotationValue        = new QuotationValue();
				$quotationValue->key   = $key;
				$quotationValue->value = $value ?? '';
				$quotationValue->row()->associate( $quotationRow )->save();
			}
		}

        LaravelFlashSessionHelper::setFlashMessage("Quotation #{$quotation->name} created", 'success');

		return route( 'quotation.edit', [ 'id' => $quotation->id ] );
	}

	public function pdf( Request $request, $id ) {
        $quotation = Quotation::findOrFail( $id );
        $tour = Tour::find($quotation-> tour_id);
        $tourdays = $tour-> tour_days;
        $calculations = $quotation -> calculation;
        $pdf = PDF::loadView('quotation.pdf', compact('quotation', 'tourdays', 'calculations'));
        $name = "Quotation-$quotation->id";
        return $pdf->stream($name . '.pdf');
//        return view('quotation.pdf', compact('quotation', 'tourdays', 'calculations'));
    }

    public function addColumnMessage(Request $request) {
	    return view('quotation.column_type');
    }
	public function confirm($id,Request $request){
		$quotation = Quotation::findOrFail( $id );
		//dd();
		$quotation->is_confirm = 1;
		$quotation->save();
		return $id;

	}
	public function confirm_cancel($id,Request $request){
		$quotation = Quotation::findOrFail( $id );
		//dd();
		$quotation->is_confirm = 0;
		$quotation->save();
		return $id;

	}
	
	public function excel(int $id,string $export = "xlsx")
    {
		$quotation = Quotation::findOrFail( $id );
		$listRoomsHotel = TourRoomTypeHotel::where('tour_id', $quotation->tour->id )->get();
		
        $tour = Tour::find($quotation-> tour_id);
        $tourdays = $tour-> tour_days;
        $calculations = $quotation -> calculation;


        // dd($tour);
        if ($export == 'csv') {

           // $this->csvExport($tour, $type);
        } else $this->prepareExport($quotation, $export, $tour, $calculations,$listRoomsHotel);

        return back();
    }
	public function prepareExport($quotation, string $export, $tour  ,$calculations,$listRoomsHotel,$request = null){
        $this->request =$request;
        $excelName = str_replace(" ","_",$quotation->name);
        return Excel::create('Quotation_'.$excelName, function($excel) use($quotation ,$calculations,$listRoomsHotel){
              	$excel->sheet('Tour Information', function($sheet) use($quotation ,$calculations,$listRoomsHotel){
                	$sheet->loadView('quotation.excel', compact('quotation','calculations','listRoomsHotel'));
            	});
            	
            })->export($export);
	}
}
