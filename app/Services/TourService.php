<?php

namespace App\Services;

use App\Tour;
use App\User;
use App\Status;
use App\TourDay;
use App\Exceptions\BusinessLogicException;
use App\Exceptions\ResourceNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class TourService
{
    /**
     * Create a new tour with all related data
     */
    public function createTour(array $data): Tour
    {
        DB::beginTransaction();

        try {
            // Create the tour
            $tour = Tour::create([
                'name' => $data['name'],
                'overview' => $data['overview'] ?? null,
                'remark' => $data['remark'] ?? null,
                'departure_date' => $data['departure_date'],
                'retirement_date' => $data['retirement_date'] ?? null,
                'pax' => $data['pax'],
                'rooms' => $data['rooms'] ?? null,
                'country_begin' => $data['country_begin'],
                'city_begin' => $data['city_begin'],
                'country_end' => $data['country_end'] ?? null,
                'city_end' => $data['city_end'] ?? null,
                'invoice' => $data['invoice'] ?? null,
                'ga' => $data['ga'] ?? null,
                'status' => $data['status'],
                'external_name' => $data['external_name'] ?? null,
                'client_id' => $data['client_id'] ?? null,
            ]);

            // Assign responsible users
            if (!empty($data['responsible_users'])) {
                $tour->responsibleUsers()->sync($data['responsible_users']);
            }

            // Assign assigned users
            if (!empty($data['assigned_users'])) {
                $tour->assignedUsers()->sync($data['assigned_users']);
            }

            // Generate tour days
            $this->generateTourDays($tour);

            // Log the creation
            Log::info('Tour created successfully', [
                'tour_id' => $tour->id,
                'tour_name' => $tour->name,
                'created_by' => auth()->id(),
            ]);

            // Clear related caches
            $this->clearTourCaches();

            DB::commit();

            return $tour->fresh(['responsibleUsers', 'assignedUsers', 'status', 'client']);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to create tour', [
                'data' => $data,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw new BusinessLogicException(
                'Failed to create tour: ' . $e->getMessage(),
                ['original_data' => $data]
            );
        }
    }

    /**
     * Update an existing tour
     */
    public function updateTour(Tour $tour, array $data): Tour
    {
        DB::beginTransaction();

        try {
            // Check if tour can be modified
            $this->validateTourCanBeModified($tour, $data);

            // Update tour data
            $tour->update([
                'name' => $data['name'],
                'overview' => $data['overview'] ?? null,
                'remark' => $data['remark'] ?? null,
                'departure_date' => $data['departure_date'],
                'retirement_date' => $data['retirement_date'] ?? null,
                'pax' => $data['pax'],
                'rooms' => $data['rooms'] ?? null,
                'country_begin' => $data['country_begin'],
                'city_begin' => $data['city_begin'],
                'country_end' => $data['country_end'] ?? null,
                'city_end' => $data['city_end'] ?? null,
                'invoice' => $data['invoice'] ?? null,
                'ga' => $data['ga'] ?? null,
                'status' => $data['status'],
                'external_name' => $data['external_name'] ?? null,
                'client_id' => $data['client_id'] ?? null,
            ]);

            // Update user assignments
            if (isset($data['responsible_users'])) {
                $tour->responsibleUsers()->sync($data['responsible_users']);
            }

            if (isset($data['assigned_users'])) {
                $tour->assignedUsers()->sync($data['assigned_users']);
            }

            // Regenerate tour days if dates changed
            if ($tour->wasChanged(['departure_date', 'retirement_date'])) {
                $this->regenerateTourDays($tour);
            }

            // Log the update
            Log::info('Tour updated successfully', [
                'tour_id' => $tour->id,
                'tour_name' => $tour->name,
                'updated_by' => auth()->id(),
                'changes' => $tour->getChanges(),
            ]);

            // Clear related caches
            $this->clearTourCaches($tour->id);

            DB::commit();

            return $tour->fresh(['responsibleUsers', 'assignedUsers', 'status', 'client']);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to update tour', [
                'tour_id' => $tour->id,
                'data' => $data,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw new BusinessLogicException(
                'Failed to update tour: ' . $e->getMessage(),
                ['tour_id' => $tour->id, 'update_data' => $data]
            );
        }
    }

    /**
     * Delete a tour (soft delete)
     */
    public function deleteTour(Tour $tour): bool
    {
        DB::beginTransaction();

        try {
            // Check if tour can be deleted
            $this->validateTourCanBeDeleted($tour);

            // Soft delete the tour
            $tour->delete();

            // Log the deletion
            Log::info('Tour deleted successfully', [
                'tour_id' => $tour->id,
                'tour_name' => $tour->name,
                'deleted_by' => auth()->id(),
            ]);

            // Clear related caches
            $this->clearTourCaches($tour->id);

            DB::commit();

            return true;

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to delete tour', [
                'tour_id' => $tour->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw new BusinessLogicException(
                'Failed to delete tour: ' . $e->getMessage(),
                ['tour_id' => $tour->id]
            );
        }
    }

    /**
     * Clone an existing tour
     */
    public function cloneTour(Tour $originalTour, array $newData): Tour
    {
        DB::beginTransaction();

        try {
            $cloneData = $originalTour->toArray();

            // Remove fields that shouldn't be cloned
            unset($cloneData['id'], $cloneData['created_at'], $cloneData['updated_at'], $cloneData['deleted_at']);

            // Override with new data
            $cloneData = array_merge($cloneData, $newData);

            // Ensure unique name
            if (!isset($newData['name'])) {
                $cloneData['name'] = $this->generateUniqueCloneName($originalTour->name);
            }

            // Create the cloned tour
            $clonedTour = $this->createTour($cloneData);

            // Clone related data if needed
            $this->cloneTourRelatedData($originalTour, $clonedTour);

            Log::info('Tour cloned successfully', [
                'original_tour_id' => $originalTour->id,
                'cloned_tour_id' => $clonedTour->id,
                'cloned_by' => auth()->id(),
            ]);

            DB::commit();

            return $clonedTour;

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to clone tour', [
                'original_tour_id' => $originalTour->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw new BusinessLogicException(
                'Failed to clone tour: ' . $e->getMessage(),
                ['original_tour_id' => $originalTour->id]
            );
        }
    }

    /**
     * Get tours with filtering and pagination
     */
    public function getTours(array $filters = [], int $perPage = 50)
    {
        $query = Tour::with(['status', 'client', 'responsibleUsers', 'assignedUsers']);

        // Apply filters
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['departure_from'])) {
            $query->where('departure_date', '>=', $filters['departure_from']);
        }

        if (!empty($filters['departure_to'])) {
            $query->where('departure_date', '<=', $filters['departure_to']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('external_name', 'like', "%{$search}%")
                  ->orWhere('overview', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['responsible_user'])) {
            $query->whereHas('responsibleUsers', function ($q) use ($filters) {
                $q->where('users.id', $filters['responsible_user']);
            });
        }

        if (!empty($filters['client_id'])) {
            $query->where('client_id', $filters['client_id']);
        }

        // Apply sorting
        $sortBy = $filters['sort_by'] ?? 'departure_date';
        $sortDirection = $filters['sort_direction'] ?? 'desc';
        $query->orderBy($sortBy, $sortDirection);

        return $query->paginate($perPage);
    }

    /**
     * Get tour statistics
     */
    public function getTourStatistics(array $filters = []): array
    {
        $cacheKey = 'tour_statistics_' . md5(serialize($filters));

        return Cache::remember($cacheKey, 300, function () use ($filters) {
            $query = Tour::query();

            // Apply date filters
            if (!empty($filters['from_date'])) {
                $query->where('departure_date', '>=', $filters['from_date']);
            }

            if (!empty($filters['to_date'])) {
                $query->where('departure_date', '<=', $filters['to_date']);
            }

            $stats = [
                'total_tours' => (clone $query)->count(),
                'active_tours' => (clone $query)->whereHas('status', function ($q) {
                    $q->where('name', 'Active');
                })->count(),
                'completed_tours' => (clone $query)->whereHas('status', function ($q) {
                    $q->where('name', 'Completed');
                })->count(),
                'cancelled_tours' => (clone $query)->whereHas('status', function ($q) {
                    $q->where('name', 'Cancelled');
                })->count(),
                'total_passengers' => (clone $query)->sum('pax'),
                'average_duration' => $this->calculateAverageTourDuration($query),
                'upcoming_tours' => Tour::where('departure_date', '>', now())->count(),
                'tours_this_month' => Tour::whereMonth('departure_date', now()->month)
                                        ->whereYear('departure_date', now()->year)
                                        ->count(),
            ];

            return $stats;
        });
    }

    /**
     * Generate tour days based on departure and retirement dates
     */
    protected function generateTourDays(Tour $tour): void
    {
        $startDate = Carbon::parse($tour->departure_date);
        $endDate = $tour->retirement_date ? Carbon::parse($tour->retirement_date) : $startDate;

        // Delete existing tour days
        $tour->tourDays()->delete();

        // Generate new tour days
        $currentDate = $startDate->copy();
        $dayNumber = 1;

        while ($currentDate <= $endDate) {
            TourDay::create([
                'tour_id' => $tour->id,
                'date' => $currentDate->toDateString(),
                'day_number' => $dayNumber,
                'title' => "Day {$dayNumber}",
                'description' => null,
            ]);

            $currentDate->addDay();
            $dayNumber++;
        }
    }

    /**
     * Regenerate tour days when dates change
     */
    protected function regenerateTourDays(Tour $tour): void
    {
        $this->generateTourDays($tour);
    }

    /**
     * Validate if tour can be modified
     */
    protected function validateTourCanBeModified(Tour $tour, array $data): void
    {
        // Check if tour has started
        if ($tour->departure_date <= now()) {
            // Allow only certain modifications for started tours
            $restrictedFields = ['departure_date', 'pax'];

            foreach ($restrictedFields as $field) {
                if (isset($data[$field]) && $data[$field] != $tour->$field) {
                    throw new BusinessLogicException(
                        "Cannot modify {$field} for tours that have already started.",
                        ['tour_id' => $tour->id, 'field' => $field]
                    );
                }
            }
        }

        // Check status transitions
        $currentStatus = $tour->status;
        $newStatus = $data['status'] ?? $currentStatus;

        if (!$this->isValidStatusTransition($currentStatus, $newStatus)) {
            throw new BusinessLogicException(
                "Invalid status transition from {$currentStatus} to {$newStatus}.",
                ['tour_id' => $tour->id, 'from_status' => $currentStatus, 'to_status' => $newStatus]
            );
        }
    }

    /**
     * Validate if tour can be deleted
     */
    protected function validateTourCanBeDeleted(Tour $tour): void
    {
        // Check if tour has packages
        if ($tour->tourPackages()->exists()) {
            throw new BusinessLogicException(
                'Cannot delete tour with existing packages. Please remove all packages first.',
                ['tour_id' => $tour->id]
            );
        }

        // Check if tour has started
        if ($tour->departure_date <= now()) {
            throw new BusinessLogicException(
                'Cannot delete tours that have already started.',
                ['tour_id' => $tour->id]
            );
        }
    }

    /**
     * Check if status transition is valid
     */
    protected function isValidStatusTransition(int $fromStatus, int $newStatus): bool
    {
        // Define valid status transitions
        $validTransitions = [
            1 => [2, 3, 4], // Draft -> Active, Cancelled, Completed
            2 => [3, 4],    // Active -> Cancelled, Completed
            3 => [],        // Cancelled -> (no transitions)
            4 => [],        // Completed -> (no transitions)
        ];

        return $fromStatus === $newStatus ||
               in_array($newStatus, $validTransitions[$fromStatus] ?? []);
    }

    /**
     * Generate unique clone name
     */
    protected function generateUniqueCloneName(string $originalName): string
    {
        $baseName = $originalName . ' (Copy)';
        $counter = 1;
        $newName = $baseName;

        while (Tour::where('name', $newName)->exists()) {
            $newName = $baseName . ' ' . $counter;
            $counter++;
        }

        return $newName;
    }

    /**
     * Clone related tour data
     */
    protected function cloneTourRelatedData(Tour $originalTour, Tour $clonedTour): void
    {
        // Clone responsible users
        $responsibleUsers = $originalTour->responsibleUsers()->pluck('user_id')->toArray();
        if (!empty($responsibleUsers)) {
            $clonedTour->responsibleUsers()->sync($responsibleUsers);
        }

        // Clone assigned users
        $assignedUsers = $originalTour->assignedUsers()->pluck('user_id')->toArray();
        if (!empty($assignedUsers)) {
            $clonedTour->assignedUsers()->sync($assignedUsers);
        }
    }

    /**
     * Calculate average tour duration
     */
    protected function calculateAverageTourDuration($query): float
    {
        $tours = $query->whereNotNull('retirement_date')->get(['departure_date', 'retirement_date']);

        if ($tours->isEmpty()) {
            return 0;
        }

        $totalDays = 0;
        foreach ($tours as $tour) {
            $start = Carbon::parse($tour->departure_date);
            $end = Carbon::parse($tour->retirement_date);
            $totalDays += $start->diffInDays($end);
        }

        return round($totalDays / $tours->count(), 2);
    }

    /**
     * Clear tour-related caches
     */
    protected function clearTourCaches(int $tourId = null): void
    {
        $patterns = [
            'tour_statistics_*',
            'tours_list_*',
            'tour_dashboard_*',
        ];

        if ($tourId) {
            $patterns[] = "tour_{$tourId}_*";
        }

        foreach ($patterns as $pattern) {
            Cache::tags(['tours'])->flush();
        }
    }
}