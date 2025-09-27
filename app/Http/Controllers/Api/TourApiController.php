<?php

namespace App\Http\Controllers\Api;

use App\Tour;
use App\Services\TourService;
use App\Http\Requests\Tour\StoreTourRequest;
use App\Http\Requests\Tour\UpdateTourRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\TourResource;
use App\Http\Resources\TourCollection;
use App\Exceptions\BusinessLogicException;
use App\Exceptions\ResourceNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TourApiController extends Controller
{
    protected $tourService;

    public function __construct(TourService $tourService)
    {
        $this->tourService = $tourService;
        $this->middleware('auth:sanctum');
        $this->middleware('permission:tour.index')->only('index');
        $this->middleware('permission:tour.create')->only('store');
        $this->middleware('permission:tour.show')->only('show');
        $this->middleware('permission:tour.edit')->only('update');
        $this->middleware('permission:tour.delete')->only('destroy');
    }

    /**
     * Display a listing of tours
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $filters = $request->only([
                'status', 'departure_from', 'departure_to', 'search',
                'responsible_user', 'client_id', 'sort_by', 'sort_direction'
            ]);

            $perPage = $request->get('per_page', 15);
            $perPage = min($perPage, 100); // Limit to prevent abuse

            $tours = $this->tourService->getTours($filters, $perPage);

            return response()->json([
                'success' => true,
                'data' => new TourCollection($tours),
                'meta' => [
                    'total' => $tours->total(),
                    'per_page' => $tours->perPage(),
                    'current_page' => $tours->currentPage(),
                    'last_page' => $tours->lastPage(),
                    'from' => $tours->firstItem(),
                    'to' => $tours->lastItem(),
                ],
                'links' => [
                    'first' => $tours->url(1),
                    'last' => $tours->url($tours->lastPage()),
                    'prev' => $tours->previousPageUrl(),
                    'next' => $tours->nextPageUrl(),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('API Error loading tours', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load tours.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Store a newly created tour
     *
     * @param StoreTourRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreTourRequest $request)
    {
        try {
            $tour = $this->tourService->createTour($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Tour created successfully.',
                'data' => new TourResource($tour)
            ], 201);

        } catch (BusinessLogicException $e) {
            Log::warning('API Business logic error creating tour', [
                'error' => $e->getMessage(),
                'context' => $e->getContext(),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->getContext()
            ], $e->getCode());

        } catch (\Exception $e) {
            Log::error('API Unexpected error creating tour', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'request_data' => $request->validated(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred while creating the tour.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Display the specified tour
     *
     * @param Tour $tour
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Tour $tour)
    {
        try {
            $tour->load([
                'status',
                'client',
                'responsibleUsers',
                'assignedUsers',
                'tourDays.tourPackages',
                'tasks',
                'comments.user'
            ]);

            return response()->json([
                'success' => true,
                'data' => new TourResource($tour)
            ]);

        } catch (\Exception $e) {
            Log::error('API Error loading tour details', [
                'tour_id' => $tour->id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load tour details.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Update the specified tour
     *
     * @param UpdateTourRequest $request
     * @param Tour $tour
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateTourRequest $request, Tour $tour)
    {
        try {
            $updatedTour = $this->tourService->updateTour($tour, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Tour updated successfully.',
                'data' => new TourResource($updatedTour)
            ]);

        } catch (BusinessLogicException $e) {
            Log::warning('API Business logic error updating tour', [
                'tour_id' => $tour->id,
                'error' => $e->getMessage(),
                'context' => $e->getContext(),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->getContext()
            ], $e->getCode());

        } catch (\Exception $e) {
            Log::error('API Unexpected error updating tour', [
                'tour_id' => $tour->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'request_data' => $request->validated(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred while updating the tour.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Remove the specified tour
     *
     * @param Tour $tour
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Tour $tour)
    {
        try {
            $tourName = $tour->name;
            $this->tourService->deleteTour($tour);

            return response()->json([
                'success' => true,
                'message' => "Tour '{$tourName}' has been deleted successfully."
            ]);

        } catch (BusinessLogicException $e) {
            Log::warning('API Business logic error deleting tour', [
                'tour_id' => $tour->id,
                'error' => $e->getMessage(),
                'context' => $e->getContext(),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode());

        } catch (\Exception $e) {
            Log::error('API Unexpected error deleting tour', [
                'tour_id' => $tour->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred while deleting the tour.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Clone the specified tour
     *
     * @param Request $request
     * @param Tour $tour
     * @return \Illuminate\Http\JsonResponse
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

            return response()->json([
                'success' => true,
                'message' => 'Tour cloned successfully.',
                'data' => new TourResource($clonedTour)
            ], 201);

        } catch (BusinessLogicException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode());

        } catch (\Exception $e) {
            Log::error('API Unexpected error cloning tour', [
                'tour_id' => $tour->id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred while cloning the tour.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get tour statistics
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function statistics(Request $request)
    {
        try {
            $filters = $request->only(['from_date', 'to_date']);
            $statistics = $this->tourService->getTourStatistics($filters);

            return response()->json([
                'success' => true,
                'data' => $statistics
            ]);

        } catch (\Exception $e) {
            Log::error('API Error getting tour statistics', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load statistics.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Search tours
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2|max:255',
            'limit' => 'nullable|integer|min:1|max:50'
        ]);

        try {
            $query = Tour::with(['status', 'client'])
                        ->where('name', 'like', '%' . $request->q . '%')
                        ->orWhere('external_name', 'like', '%' . $request->q . '%')
                        ->orWhere('overview', 'like', '%' . $request->q . '%');

            $limit = $request->get('limit', 10);
            $tours = $query->limit($limit)->get();

            return response()->json([
                'success' => true,
                'data' => TourResource::collection($tours),
                'meta' => [
                    'query' => $request->q,
                    'total_results' => $tours->count(),
                    'limit' => $limit
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('API Error searching tours', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'search_query' => $request->q,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to search tours.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get tours for calendar view
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function calendar(Request $request)
    {
        $request->validate([
            'start' => 'required|date',
            'end' => 'required|date|after:start',
        ]);

        try {
            $tours = Tour::with(['status', 'responsibleUsers'])
                        ->whereBetween('departure_date', [$request->start, $request->end])
                        ->orWhereBetween('retirement_date', [$request->start, $request->end])
                        ->get();

            $events = $tours->map(function ($tour) {
                return [
                    'id' => $tour->id,
                    'title' => $tour->name,
                    'start' => $tour->departure_date,
                    'end' => $tour->retirement_date,
                    'url' => route('tour.show', ['tour' => $tour->id]),
                    'backgroundColor' => $tour->getStatusColor(),
                    'borderColor' => $tour->getStatusColor(),
                    'textColor' => '#ffffff',
                    'extendedProps' => [
                        'status' => $tour->getStatusName(),
                        'pax' => $tour->pax,
                        'responsible_users' => $tour->responsible_user_names,
                    ]
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $events
            ]);

        } catch (\Exception $e) {
            Log::error('API Error loading calendar tours', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load calendar data.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Bulk operations on tours
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,update_status,assign_user',
            'tour_ids' => 'required|array|min:1|max:100',
            'tour_ids.*' => 'integer|exists:tours,id',
            'status' => 'required_if:action,update_status|exists:statuses,id',
            'user_id' => 'required_if:action,assign_user|exists:users,id',
        ]);

        try {
            $tours = Tour::whereIn('id', $request->tour_ids)->get();
            $results = [
                'success' => 0,
                'failed' => 0,
                'errors' => []
            ];

            foreach ($tours as $tour) {
                try {
                    switch ($request->action) {
                        case 'delete':
                            $this->tourService->deleteTour($tour);
                            break;

                        case 'update_status':
                            $this->tourService->updateTour($tour, ['status' => $request->status]);
                            break;

                        case 'assign_user':
                            $responsibleUsers = $tour->responsibleUsers()->pluck('user_id')->toArray();
                            if (!in_array($request->user_id, $responsibleUsers)) {
                                $responsibleUsers[] = $request->user_id;
                                $tour->responsibleUsers()->sync($responsibleUsers);
                            }
                            break;
                    }

                    $results['success']++;

                } catch (\Exception $e) {
                    $results['failed']++;
                    $results['errors'][] = [
                        'tour_id' => $tour->id,
                        'tour_name' => $tour->name,
                        'error' => $e->getMessage()
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Bulk action completed. {$results['success']} successful, {$results['failed']} failed.",
                'data' => $results
            ]);

        } catch (\Exception $e) {
            Log::error('API Error in bulk action', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to perform bulk action.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}