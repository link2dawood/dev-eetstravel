<?php
namespace App\Helper;

use Carbon\Carbon;

trait HelperTrait{
    /**
     * list of all services
     * @var array
     */
	public $services = [ 'Event', 'Guide', 'Hotel', 'Restaurant', 'Transfer'];

    public $servicesTypes = [
        0   => 'Hotel',
        1   => 'Event',
        2   => 'Guide',
        3   => 'Transfer',
        4   => 'Restaurant',
        5   => 'TourPackage',
        6   => 'Cruises',
        7   => 'Flight',
        8   => 'rooming list'
    ];
	/**
	 * find diff between to days
	 * @param  $time_from 
	 * @param  $time_to   
	 * @return mixed
	 */
	public function findDateByDiff($time_from, $time_to)
	{
		$start_date = Carbon::parse($time_from);
		$diff = $start_date->diffInDays(Carbon::parse($time_to));
		return $start_date->addDays($diff);
	}
	/**
	 * find new date from two old date
	 * @param  $oldStartDate 
	 * @param  $oldEndDate   
	 * @param  $startDate    
	 * @return mixed               
	 */
	public function findNewDate($oldStartDate, $oldEndDate, $startDate)
	{
		$oldStart = Carbon::parse($oldStartDate);
		$diff = $oldStart->diffInDays(Carbon::parse($oldEndDate));
        $addOne = 0;
        if (Carbon::parse($oldStartDate)->format('Y-m-d') != Carbon::parse($oldEndDate)->format('Y-m-d')){
            $addOne = 1;
        }
		return $endDate = Carbon::parse($startDate)->addDays($diff+$addOne);
	}
	/**
     * find date range for tour package services
     * @param  array $data 
     * @return DatePeriod 
     */
    public function findDateRange(array $data)
    {
        $departureDate = new \DateTime($data['departure_date']);
        $retirementDate = new \DateTime($data['retirement_date']);
        $retirementDate = $retirementDate->modify('+1 day');
        $interval = new \DateInterval('P1D');
        $dateRange = new \DatePeriod($departureDate, $interval, $retirementDate);
        if (iterator_count($dateRange) > 30) {
            // return view('component.error_panel', ['error' => 'Period should be less than 30 days']);
            return null;
        }
        if (iterator_count($dateRange) == 0) {
            // return  view('component.error_panel', ['error' => 'Period should be at least 1 day']);
            return null;
        }
        return $dateRange;
    }
    /**
     * parse date
     * @param  $date
     * @return array
     */
    public function parseFullDate($date)
    {
    	$hours = Carbon::parse($date)->format('H:i:s');
    	$date = Carbon::parse($date)->format('Y-m-d');
    	return ['date' => $date, 'hours' => $hours];
    }
    /**
     * compare two date
     * @param  $bigger date that must be bigger
     * @param  $comp   date that must be smaller
     * @return bool
     */
    public function compareDate($bigger, $comp)
    {
        return $bigger > $comp;
    }
    /**
     * parse hours and minutes from date
     * @param  $date 
     * @return Carbon/Carbon       
     */
    public function convertDateToHoursMinute($date)
    {
        return Carbon::parse($date)->format('H:i');
    }
    /**
     * parse date without hours, minutes, seconds
     * @param  $date
     * @return Carbon/Carbon 
     */
    public function convertDateToDays($date)
    {
        return Carbon::parse($date)->format('Y-m-d');
    }
    /**
     * create date without seconds
     * @param  $date
     * @return Carbon/Carbon
     */
    public function convertDateWithoutSeconds($date)
    {
        return Carbon::parse($date)->format('Y-m-d H:i');
    }
    /**
     * create namespace for model
     * @param  string $name model name
     * @return string
     */
    public function createNamespace(string $name)
    {
        return $namespace = 'App\\' . $name;
    }

    public function getTableName($model)
    {
        return $table = with(new $model)->getTable();
    }
}