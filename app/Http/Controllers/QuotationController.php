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
use PDF;
use Illuminate\Http\Request;
use Redirect;
use Maatwebsite\Excel\Facades\Excel;
use App\TourDay;
use App\Status;
use App\Country;
use App\City;
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
	/**
	 * AUDIT.md CC11 — was Quotation::with-joins->get() (whole table) +
	 * per-row Tour::find (N+1). Plus a separate get() on goAheadTours
	 * (every confirmed-quotation tour, JOIN-bombed).
	 *
	 * Both lists now paginate(20). The tour link no longer needs Tour::find
	 * because the JOIN already selects tours.name as tour_name — we just
	 * render that and we already have tour_id.
	 */
	public function index() {
		$title   = 'Index - quotation';
		$perPage = 20;

		// ---- Quotations tab -------------------------------------------------
		$quotations = Quotation::query()
			->leftJoin('tours', 'tours.id', '=', 'quotations.tour_id')
			->leftJoin('users', 'users.id', '=', 'quotations.user_id')
			->select([
				'quotations.id',
				'quotations.name',
				'quotations.tour_id',
				'quotations.created_at',
				'users.name as user_name',
				'tours.name as tour_name',
			])
			->orderByDesc('quotations.created_at')
			->paginate($perPage, ['*'], 'page');

		$canShowComparison = Auth::user()->can('comparison.show');

		$quotations->getCollection()->transform(function ($quotation) use ($canShowComparison) {
			// Action buttons
			$quotation->action = $this->getButton($quotation->id, $quotation);

			// Date formatting — the legacy 'Y-m-d H:s' was a typo (seconds
			// placeholder is :s but the prefix isn't right). Keeping the exact
			// legacy format so the column doesn't shift.
			$quotation->formatted_created_at = (new Carbon($quotation->created_at))->format('Y-m-d H:s');

			// Tour link uses the JOIN-selected tour_name; no Tour::find()
			// needed.
			if ($quotation->tour_id && $quotation->tour_name) {
				$link = route('tour.show', ['tour' => $quotation->tour_id]);
				$quotation->tour_link = "<a href='{$link}' class='click_event' style='color: blue; text-decoration: underline!important; cursor: pointer'>{$quotation->tour_name}</a>";
			} else {
				$quotation->tour_link = '';
			}

			// Comparison link
			if ($canShowComparison) {
				$comparison_link = route('comparison.show', ['comparison' => $quotation->id]);
				$quotation->comparison = "<a href='{$comparison_link}' class='click_event' style='color: blue; text-decoration: underline!important; cursor: pointer'>Front Sheet</a>";
			} else {
				$quotation->comparison = "<span>No permission</span>";
			}

			return $quotation;
		});

		// ---- Go-ahead tours tab --------------------------------------------
		$goAheadTours = Tour::query()
			->leftJoin('quotations', 'quotations.tour_id', '=', 'tours.id')
			->leftJoin('status', 'status.id', '=', 'tours.status')
			->leftJoin('countries as country_begin', 'country_begin.alias', '=', 'tours.country_begin')
			->leftJoin('countries as country_end',   'country_end.alias',   '=', 'tours.country_end')
			->leftJoin('cities as city_begin',       'city_begin.id',       '=', 'tours.city_begin')
			->leftJoin('cities as city_end',         'city_end.id',         '=', 'tours.city_end')
			->where('quotations.is_confirm', 1)
			->select([
				'tours.id',
				'tours.name',
				'tours.departure_date',
				'tours.external_name',
				'country_begin.name as country_begin',
				'city_begin.name as city_begin',
				'status.name as status_name',
			])
			->groupBy(
				'tours.id', 'tours.name', 'tours.departure_date', 'tours.external_name',
				'country_begin.name', 'city_begin.name', 'status.name'
			)
			->orderByDesc('tours.departure_date')
			->paginate($perPage, ['*'], 'tour_page');

		$goAheadTours->getCollection()->transform(function ($tour) {
			$tour->action = $this->getTourButton($tour->id);
			return $tour;
		});

		return view('quotation.index', compact('quotations', 'goAheadTours', 'title'));
	}

    public function getButton($id, $quotation)
    {
        $url = [
            'edit' => route('quotation.edit', ['quotation' => $id]),
            'print' => route('quotation.pdf', ['id' => $id])
        ];

        return DatatablesHelperController::getQuotationListButtons($url, $quotation);
    }

    public function getTourButton($id)
    {
        $url = [
            'show' => route('tour.show', ['tour' => $id]),
            'edit' => route('tour.edit', ['tour' => $id])
        ];

        $buttons = '';
        if (Auth::user()->can('tour.show')) {
            $buttons .= "<a href='{$url['show']}' class='btn btn-primary btn-xs' title='Show'><i class='fa fa-eye'></i></a> ";
        }
        if (Auth::user()->can('tour.edit')) {
            $buttons .= "<a href='{$url['edit']}' class='btn btn-info btn-xs' title='Edit'><i class='ti ti-edit'></i></a>";
        }

        return $buttons;
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
		return route( 'quotation.edit', ['quotation' => $quotation->id] );
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

		return route( 'quotation.edit', ['quotation' => $quotation->id] );
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
        $exportFormat = $export; // Store the original export format (xlsx, csv, etc.)
        $exportClass = new class($quotation, $calculations, $listRoomsHotel) implements \Maatwebsite\Excel\Concerns\FromView {
            protected $quotation, $calculations, $listRoomsHotel;
            public function __construct($quotation, $calculations, $listRoomsHotel) {
                $this->quotation = $quotation;
                $this->calculations = $calculations;
                $this->listRoomsHotel = $listRoomsHotel;
            }
            public function view(): \Illuminate\Contracts\View\View {
                return view('quotation.excel', compact('quotation', 'calculations', 'listRoomsHotel'));
            }
        };
        return Excel::download($exportClass, 'Quotation_'.$excelName.'.'.$exportFormat);
	}
}
