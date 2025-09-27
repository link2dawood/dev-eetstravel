<?php 
namespace App\Repository\Contracts;

interface TourRepository{
	public function all();
	public function allForAssigned();
	public function allForAssignedWithId($userId);
	public function byId(int $id);
	public function allQuotationTours();
	public function allQuotationToursForAssigned();
	public function getTourByDates($startDate, $endDate);
	public function getToursAttachedToUser($user);
}
