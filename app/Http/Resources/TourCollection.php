<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TourCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'tours' => $this->collection->transform(function ($tour) {
                return [
                    'id' => $tour->id,
                    'name' => $tour->name,
                    'external_name' => $tour->external_name,
                    'departure_date' => $tour->departure_date,
                    'retirement_date' => $tour->retirement_date,
                    'duration_days' => $tour->retirement_date
                        ? \Carbon\Carbon::parse($tour->departure_date)->diffInDays(\Carbon\Carbon::parse($tour->retirement_date)) + 1
                        : 1,
                    'pax' => (int) $tour->pax,
                    'rooms' => (int) $tour->rooms,
                    'locations' => [
                        'start' => [
                            'country' => $tour->country_begin,
                            'city' => $tour->city_begin,
                        ],
                        'end' => [
                            'country' => $tour->country_end,
                            'city' => $tour->city_end,
                        ],
                    ],
                    'status' => [
                        'id' => $tour->status,
                        'name' => $tour->getStatusName(),
                        'color' => $tour->getStatusColor(),
                        'background_color' => $tour->getRowBackgroundColor(),
                    ],
                    'client' => $tour->client ? [
                        'id' => $tour->client->id,
                        'name' => $tour->client->name,
                    ] : null,
                    'responsible_users' => $tour->responsibleUsers ? $tour->responsibleUsers->map(function ($user) {
                        return [
                            'id' => $user->id,
                            'name' => $user->name,
                        ];
                    }) : [],
                    'responsible_user_names' => $tour->responsible_user_names,
                    'assigned_user_names' => $tour->assigned_user_names,
                    'statistics' => [
                        'total_packages' => $tour->tourPackages ? $tour->tourPackages->count() : 0,
                        'total_tasks' => $tour->tasks ? $tour->tasks->count() : 0,
                        'completed_tasks' => $tour->tasks ? $tour->tasks->where('status', 'completed')->count() : 0,
                        'overdue_tasks' => $tour->tasks ? $tour->tasks->filter(function ($task) {
                            return $task->due_date && $task->due_date < now() && $task->status !== 'completed';
                        })->count() : 0,
                    ],
                    'urls' => [
                        'show' => route('tour.show', ['tour' => $tour->id]),
                        'api_show' => route('api.tours.show', $tour->id),
                    ],
                    'created_at' => $tour->created_at,
                    'updated_at' => $tour->updated_at,
                ];
            }),
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function with($request)
    {
        return [
            'meta' => [
                'version' => '1.0',
                'timestamp' => now()->toISOString(),
                'request_id' => $request->header('X-Request-ID') ?? uniqid(),
                'filters_applied' => $request->only([
                    'status', 'departure_from', 'departure_to', 'search',
                    'responsible_user', 'client_id', 'sort_by', 'sort_direction'
                ]),
                'available_filters' => [
                    'status' => 'Filter by status ID',
                    'departure_from' => 'Filter tours departing after this date (YYYY-MM-DD)',
                    'departure_to' => 'Filter tours departing before this date (YYYY-MM-DD)',
                    'search' => 'Search in tour name, external name, and overview',
                    'responsible_user' => 'Filter by responsible user ID',
                    'client_id' => 'Filter by client ID',
                    'sort_by' => 'Sort by field (departure_date, name, created_at, etc.)',
                    'sort_direction' => 'Sort direction (asc, desc)',
                ],
                'available_includes' => [
                    'status' => 'Include status details',
                    'client' => 'Include client details',
                    'responsibleUsers' => 'Include responsible users',
                    'assignedUsers' => 'Include assigned users',
                    'tourDays' => 'Include tour days',
                    'tourDays.tourPackages' => 'Include tour packages within days',
                    'tasks' => 'Include tasks',
                    'comments' => 'Include comments',
                ],
            ],
            'links' => [
                'documentation' => route('api.documentation'),
                'support' => 'mailto:support@eetstravel.com',
            ],
        ];
    }
}