<?php

namespace App\Http\Controllers;

use App\Tour;
use App\Status;
use App\User;
use App\Client;
use App\Services\TourService;
use App\Http\Requests\Tour\StoreTourRequest;
use App\Http\Requests\Tour\UpdateTourRequest;
use App\Exceptions\BusinessLogicException;
use App\Exceptions\ResourceNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class TourControllerImproved extends Controller
{
    protected $tourService;

    public function __construct(TourService $tourService)
    {
        $this->tourService = $tourService;
        $this->middleware('auth');
        $this->middleware('permission:tour.index')->only('index');
        $this->middleware('permission:tour.create')->only(['create', 'store']);
        $this->middleware('permission:tour.show')->only('show');
        $this->middleware('permission:tour.edit')->only(['edit', 'update']);
        $this->middleware('permission:tour.delete')->only('destroy');
    }

    /**
     * Display a listing of tours
     */
    public function index(Request $request)
    {
        try {
            $filters = $request->only([
                'status', 'departure_from', 'departure_to', 'search',
                'responsible_user', 'client_id', 'sort_by', 'sort_direction'
            ]);

            $tours = $this->tourService->getTours($filters, $request->get('per_page', 50));

            // Get filter options
            $statuses = Status::where('type', 'tour')->get();
            $users = User::select('id', 'name')->orderBy('name')->get();
            $clients = Client::select('id', 'name')->orderBy('name')->get();

            // Get client tours (tours requested by clients)
            $clientTours = Tour::whereNotNull('client_id')
                              ->with(['client', 'status'])
                              ->latest()
                              ->take(20)
                              ->get();

            // Get monthly chart data
            $monthlyChartTours = $this->getMonthlyChartData($request);
            $cancelledChartTours = $this->getCancelledChartData($request);
            $archivedTours = $this->getArchivedTours($request);

            // Get years and months for filters
            $years = Tour::selectRaw('YEAR(departure_date) as year')
                        ->distinct()
                        ->orderBy('year', 'desc')
                        ->pluck('year');

            $months = [
                1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
            ];

            return view('tour.index', compact(
                'tours', 'statuses', 'users', 'clients', 'clientTours',
                'monthlyChartTours', 'cancelledChartTours', 'archivedTours',
                'years', 'months'
            ));

        } catch (\Exception $e) {
            Log::error('Error loading tours index', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return back()->withErrors(['error' => 'Failed to load tours. Please try again.']);
        }
    }

    /**
     * Show the form for creating a new tour
     */
    public function create()
    {
        try {
            $statuses = Status::where('type', 'tour')->get();
            $users = User::select('id', 'name')->orderBy('name')->get();
            $clients = Client::select('id', 'name')->orderBy('name')->get();

            return view('tour.create', compact('statuses', 'users', 'clients'));

        } catch (\Exception $e) {
            Log::error('Error loading tour creation form', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return back()->withErrors(['error' => 'Failed to load form. Please try again.']);
        }
    }

    /**
     * Store a newly created tour
     */
    public function store(StoreTourRequest $request)
    {
        try {
            $tour = $this->tourService->createTour($request->validated());

            $message = "Tour '{$tour->name}' has been created successfully.";

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => $tour,
                    'redirect_url' => route('tour.show', ['tour' => $tour->id])
                ], 201);
            }

            return redirect()
                ->route('tour.show', ['tour' => $tour->id])
                ->with('success', $message);

        } catch (BusinessLogicException $e) {
            Log::warning('Business logic error creating tour', [
                'error' => $e->getMessage(),
                'context' => $e->getContext(),
                'user_id' => auth()->id(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'errors' => $e->getContext()
                ], $e->getCode());
            }

            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);

        } catch (\Exception $e) {
            Log::error('Unexpected error creating tour', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
            ]);

            $message = 'An unexpected error occurred while creating the tour. Please try again.';

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 500);
            }

            return back()
                ->withInput()
                ->withErrors(['error' => $message]);
        }
    }

    /**
     * Display the specified tour
     */
    public function show(Tour $tour)
    {
        try {
            $tour->load([
                'status',
                'client',
                'responsibleUsers',
                'assignedUsers',
                'tourDays.tourPackages.hotel',
                'tourDays.tourPackages.restaurant',
                'tourDays.tourPackages.transfer',
                'tourDays.tourPackages.guide',
                'tourDays.tourPackages.event',
                'tasks',
                'comments.user',
            ]);

            // Get tour statistics
            $statistics = [
                'total_packages' => $tour->tourPackages()->count(),
                'total_days' => $tour->tourDays()->count(),
                'total_tasks' => $tour->tasks()->count(),
                'completed_tasks' => $tour->tasks()->where('status', 'completed')->count(),
                'total_cost' => $tour->tourPackages()->sum('cost'),
            ];

            return view('tour.show', compact('tour', 'statistics'));

        } catch (\Exception $e) {
            Log::error('Error loading tour details', [
                'tour_id' => $tour->id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return back()->withErrors(['error' => 'Failed to load tour details. Please try again.']);
        }
    }

    /**
     * Show the form for editing the specified tour
     */
    public function edit(Tour $tour)
    {
        try {
            $tour->load(['responsibleUsers', 'assignedUsers', 'status', 'client']);

            $statuses = Status::where('type', 'tour')->get();
            $users = User::select('id', 'name')->orderBy('name')->get();
            $clients = Client::select('id', 'name')->orderBy('name')->get();

            return view('tour.edit', compact('tour', 'statuses', 'users', 'clients'));

        } catch (\Exception $e) {
            Log::error('Error loading tour edit form', [
                'tour_id' => $tour->id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return back()->withErrors(['error' => 'Failed to load edit form. Please try again.']);
        }
    }

    /**
     * Update the specified tour
     */
    public function update(UpdateTourRequest $request, Tour $tour)
    {
        try {
            $updatedTour = $this->tourService->updateTour($tour, $request->validated());

            $message = "Tour '{$updatedTour->name}' has been updated successfully.";

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => $updatedTour,
                ]);
            }

            return redirect()
                ->route('tour.show', ['tour' => $updatedTour->id])
                ->with('success', $message);

        } catch (BusinessLogicException $e) {
            Log::warning('Business logic error updating tour', [
                'tour_id' => $tour->id,
                'error' => $e->getMessage(),
                'context' => $e->getContext(),
                'user_id' => auth()->id(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'errors' => $e->getContext()
                ], $e->getCode());
            }

            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);

        } catch (\Exception $e) {
            Log::error('Unexpected error updating tour', [
                'tour_id' => $tour->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
            ]);

            $message = 'An unexpected error occurred while updating the tour. Please try again.';

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 500);
            }

            return back()
                ->withInput()
                ->withErrors(['error' => $message]);
        }
    }

    /**
     * Remove the specified tour
     */
    public function destroy(Tour $tour)
    {
        try {
            $tourName = $tour->name;
            $this->tourService->deleteTour($tour);

            $message = "Tour '{$tourName}' has been deleted successfully.";

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                ]);
            }

            return redirect()
                ->route('tour.index')
                ->with('success', $message);

        } catch (BusinessLogicException $e) {
            Log::warning('Business logic error deleting tour', [
                'tour_id' => $tour->id,
                'error' => $e->getMessage(),
                'context' => $e->getContext(),
                'user_id' => auth()->id(),
            ]);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], $e->getCode());
            }

            return back()->withErrors(['error' => $e->getMessage()]);

        } catch (\Exception $e) {
            Log::error('Unexpected error deleting tour', [
                'tour_id' => $tour->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
            ]);

            $message = 'An unexpected error occurred while deleting the tour. Please try again.';

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 500);
            }

            return back()->withErrors(['error' => $message]);
        }
    }

    /**
     * Clone a tour
     */
    public function clone(Request $request, Tour $tour)
    {
        $request->validate([
            'departure_date' => 'required|date|after_or_equal:today',
            'name' => 'nullable|string|max:255',
        ]);

        try {
            $newData = [
                'departure_date' => $request->departure_date,
            ];

            if ($request->name) {
                $newData['name'] = $request->name;
            }

            $clonedTour = $this->tourService->cloneTour($tour, $newData);

            $message = "Tour '{$clonedTour->name}' has been cloned successfully.";

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => $clonedTour,
                    'redirect_url' => route('tour.show', ['tour' => $clonedTour->id])
                ]);
            }

            return redirect()
                ->route('tour.show', ['tour' => $clonedTour->id])
                ->with('success', $message);

        } catch (BusinessLogicException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], $e->getCode());
            }

            return back()->withErrors(['error' => $e->getMessage()]);

        } catch (\Exception $e) {
            Log::error('Unexpected error cloning tour', [
                'tour_id' => $tour->id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            $message = 'An unexpected error occurred while cloning the tour. Please try again.';

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 500);
            }

            return back()->withErrors(['error' => $message]);
        }
    }

    /**
     * Get tour statistics for dashboard
     */
    public function getStatistics(Request $request)
    {
        try {
            $filters = $request->only(['from_date', 'to_date']);
            $statistics = $this->tourService->getTourStatistics($filters);

            return response()->json([
                'success' => true,
                'data' => $statistics
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting tour statistics', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load statistics.'
            ], 500);
        }
    }

    /**
     * Export tours to various formats
     */
    public function export(Request $request)
    {
        try {
            $filters = $request->only([
                'status', 'departure_from', 'departure_to', 'search',
                'responsible_user', 'client_id'
            ]);

            $format = $request->get('format', 'csv');
            $tours = $this->tourService->getTours($filters, 10000); // Large limit for export

            // Handle different export formats
            switch ($format) {
                case 'excel':
                    return $this->exportToExcel($tours);
                case 'pdf':
                    return $this->exportToPdf($tours);
                default:
                    return $this->exportToCsv($tours);
            }

        } catch (\Exception $e) {
            Log::error('Error exporting tours', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return back()->withErrors(['error' => 'Failed to export tours. Please try again.']);
        }
    }

    /**
     * Get monthly chart data
     */
    protected function getMonthlyChartData(Request $request)
    {
        $query = Tour::with(['status', 'responsibleUsers'])
                    ->whereIn('status', [1, 2]); // Active statuses

        if ($request->filled('year-filter')) {
            $query->whereYear('departure_date', $request->get('year-filter'));
        }

        if ($request->filled('month-filter')) {
            $query->whereMonth('departure_date', $request->get('month-filter'));
        }

        return $query->latest('departure_date')->get();
    }

    /**
     * Get cancelled chart data
     */
    protected function getCancelledChartData(Request $request)
    {
        $query = Tour::with(['status', 'responsibleUsers'])
                    ->where('status', 3); // Cancelled status

        if ($request->filled('year-filter')) {
            $query->whereYear('departure_date', $request->get('year-filter'));
        }

        if ($request->filled('month-filter')) {
            $query->whereMonth('departure_date', $request->get('month-filter'));
        }

        return $query->latest('departure_date')->get();
    }

    /**
     * Get archived tours
     */
    protected function getArchivedTours(Request $request)
    {
        return Tour::with(['status', 'responsibleUsers'])
                  ->where('departure_date', '<', now()->subMonths(6))
                  ->orWhere('status', 4) // Completed status
                  ->latest('departure_date')
                  ->get();
    }

    /**
     * Export to CSV
     */
    protected function exportToCsv($tours)
    {
        // Implementation for CSV export
        // This would use a proper export library like Laravel Excel
        return response()->json(['message' => 'CSV export functionality would be implemented here']);
    }

    /**
     * Export to Excel
     */
    protected function exportToExcel($tours)
    {
        // Implementation for Excel export
        return response()->json(['message' => 'Excel export functionality would be implemented here']);
    }

    /**
     * Export to PDF
     */
    protected function exportToPdf($tours)
    {
        // Implementation for PDF export
        return response()->json(['message' => 'PDF export functionality would be implemented here']);
    }
}