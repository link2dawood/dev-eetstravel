<?php 
namespace App\Repository\Contracts;

interface TaskRepository{
	public function all($limit);
	public function allForAssigned(int $user_id, $limit = null);
	public function getAllTaskForDashboard(int $user_id, $limit = null);
	public function allForAssignedToTour(int $user_id, int $tour_id, $limit = null);
	public function tourTasks(int $tour_id);
	public function findPending(int $limit);
	public function findAssignedPending(int $user_id, int $limit);
	public function tasksPeriodDays($date, $currentDate);
}