<?php 
namespace App\Repository\TourRepository;

use App\Repository\Contracts\TourRepository;
use App\Tour;
use App\User;
use Auth;
/**
* @author yurapif
*/
class EloquentTourRepository implements TourRepository
{

	public function all()
	{
		return Tour::orderBy('departure_date', 'DESC')->get();
	}

	public function allForAssigned()
	{
        $assigned_tours = Auth::user()->tours()->get();

        if($assigned_tours->count() == 0){
            return Tour::where('author', Auth::user()->id)->where('is_quotation', '!=',true)->get();
        }
//		$assigned_tours = Auth::user()->tours() -> where('is_quotation', '!=',true)->orWhereNull('is_quotation') ->get();
//		$assigned_tours = Auth::user()->tours() -> where('is_quotation', '!=',true)->get();
		$author_tours = Tour::where('author', Auth::user()->id)->where('is_quotation', '!=',true)->get();
		$union = $assigned_tours->merge($author_tours);
		$union->all();
		// dd($union);
		$unique = $union->unique('id');
		$unique->all();
		return $unique;
	}
    /* copy of method above for vue.js API*/
    public function allForAssignedWithId($userId)
    {
        $user = User::findOrFail($userId);
        if($user->tours()->count() == 0){
            return Tour::where('author', $user->id)-> where('is_quotation', '!=',true)->get();
        }
//		$assigned_tours = Auth::user()->tours() -> where('is_quotation', '!=',true)->orWhereNull('is_quotation') ->get();
        //$assigned_tours = $user->tours() -> where('is_quotation', '!=',true)->get();
        $assigned_tours = $user->tours()->get();
        $author_tours = Tour::where('author', $user->id)->where('is_quotation', '!=',true)->get();
        $union = $assigned_tours->merge($author_tours);
        $union->all();
        // dd($union);
        $unique = $union->unique('id');
        $unique->all();
        return $unique;
    }

	public function allQuotationToursForAssigned()
	{
		$assigned_tours = Auth::user()->tours()->where('is_quotation', true)->get();
		$author_tours = Tour::where('author', Auth::user()->id)->where('is_quotation', '=',true)->get();
		$union = $assigned_tours->merge($author_tours);
		$union->all();
		// dd($union);
		$unique = $union->unique('id');
		$unique->all();
		return $unique;
	}

	public function byId(int $id) :Tour
	{
		return Tour::findOrFail($id);
	}

	public function allQuotationTours(){
		return Tour::where('is_quotation', true)->get();
	}

	public function getTourByDates($startDate, $endDate){
		return Tour::query()
		    ->where('created_at', '>=', $startDate)
		    ->where('created_at', '<=', $endDate)
		    ->whereColumn('departure_date', '<=', 'retirement_date')
			->where(
				function($query) {
					$query->where('is_quotation', '!=',true)->orWhereNull('is_quotation');
				}
			)
		    ->get();
	}

	public function getToursAttachedToUser($user){
		$tours = collect();
		foreach ($user->tours as $tour){
			if(!$tour->is_quotation) {
				$tours->put($tour->id, $tour);
			}
		}

		$toursAssigned = Tour::query()->where('author', $user->id)
		                              ->where('is_quotation', '!=',true)
		                              ->orWhereNull('is_quotation')->get();
		foreach ($toursAssigned as $tour){
			$tours->put($tour->id, $tour);
		}
		return array_values($tours->all());
	}
	public function getQuotationWithStatus($satatus){
		return Tour::where('status', $satatus)->get();
	}

}