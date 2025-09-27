<?php

namespace App\Http\Controllers;

use App\Comparison;
use App\ComparisonRow;
use App\Helper\LaravelFlashSessionHelper;
use App\Hotel;
use App\Quotation;
use App\Tour;
use App\TourPackage;
use App\TourRoomTypeHotel;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ComparisonController extends Controller
{


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
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $quotation = Quotation::findOrFail($id);
        $tour = $quotation->tour;
	    $listRoomsHotel = TourRoomTypeHotel::where('tour_id', $tour->id )->get();
	    $comparison = Comparison::where(['id' => $id])->first();
	    if (!$comparison) {
	    	$newComparison = new Comparison();
	    	$newComparison->id = $id;
	    	$newComparison->save();
	    	$comparison = $newComparison;
	    }
		$this->syncComparisonRows($comparison);

        return view('comparison.show', compact('quotation', 'listRoomsHotel', 'comparison'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
	    $quotation = Quotation::findOrFail($id);
	    $comparison = Comparison::findOrFail($id);

	    foreach ($comparison->comparison_rows as $row) {
			if (in_array($row->id, $request->rooming_list_reserved ?? [])) {
				$row->rooming_list_reserved = true;
			} else {
				$row->rooming_list_reserved = false;
			}
		    if (in_array($row->id, $request->visa_confirmation ?? [])) {
			    $row->visa_confirmation = true;
		    } else {
			    $row->visa_confirmation = false;
		    }
		    if (in_array($row->id, array_keys($request->city_tax) ?? [])) {
				$row->city_tax = $request->city_tax[$row->id];
		    }
		    $tour = $quotation->tour;
			if ($tour) {
				$date = Carbon::parse($row->date);
				$tourDay = $tour->getTourDateByDate($date);
				if ($tourDay && $tourDay->firstHotel()) {
					TourPackage::updateOrInsert(['id' => $tourDay->firstHotel()->id], ['city_tax' => $request->city_tax[$row->id]]);

					if ($tourDay->firstHotel()->service()) {
						if ($tourDay->firstHotel()->service() instanceof Hotel) {
							Hotel::updateOrInsert(['id' => $tourDay->firstHotel()->service()->id], ['city_tax' => $request->city_tax[$row->id]]);
						}
					}
				}

			}
			$row->save();
	    }
	    $comparison->rooming_list_received = $request->rooming_list_received;
	    $comparison->visa_confirmation_sent = $request->visa_confirmation_sent;
	    $comparison->hotel_list_sent = $request->hotel_list_sent;
	    $comparison->final_documents_sent = $request->final_documents_sent;
	    $comparison->comments = $request->comments;
//	    if (!$request->rooming_list_received && $comparison->isAllRoomingListReserved()) {
//	    	$comparison->rooming_list_received  = Carbon::now()->toDateString();
//	    }
//	    if (!$request->visa_confirmation_sent && $comparison->isAllVisasConfirmed()) {
//		    $comparison->visa_confirmation_sent  = Carbon::now()->toDateString();
//
//	    }
//	    if ($request->rooming_list_received && !$comparison->isAllRoomingListReserved()) {
//	    	ComparisonRow::updateOrCreate(['comparison_id' => $comparison->id], ['rooming_list_reserved' => true]);
//	    }
	    $comparison->save();

        LaravelFlashSessionHelper::setFlashMessage("Front sheet quotation #$quotation->name edited");

	    return redirect(route('tour.show', ['id' => $quotation->tour_id, 'tab' => 'quotation_tab']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    protected function syncComparisonRows($comparison)
    {
    	$quotation = Quotation::findOrFail($comparison->id);
    	if ($quotation) {
		    $tour = $quotation->tour;
		    if ($tour) {
			    foreach ($tour->tour_days as $tourDay) {
					$comparisonRow = ComparisonRow::where(['date' => $tourDay->date, 'comparison_id' => $comparison->id])->first();
					if (!$comparisonRow) {
						$newComparisonRow = new ComparisonRow();
						$newComparisonRow->date = $tourDay->date;
						$newComparisonRow->comparison_id = $comparison->id;
						$newComparisonRow->save();
					}
			    }
		    }
	    }
    }

    public function comments($id) {
    	return view('comparison.comments', compact('id'));
    }

}
