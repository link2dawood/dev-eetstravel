<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TourResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'external_name' => $this->external_name,
            'overview' => $this->overview,
            'remark' => $this->remark,
            'departure_date' => $this->departure_date,
            'retirement_date' => $this->retirement_date,
            'duration_days' => $this->when($this->retirement_date, function () {
                return \Carbon\Carbon::parse($this->departure_date)
                    ->diffInDays(\Carbon\Carbon::parse($this->retirement_date)) + 1;
            }),
            'pax' => (int) $this->pax,
            'rooms' => (int) $this->rooms,
            'average_pax_per_room' => $this->when($this->rooms > 0, function () {
                return round($this->pax / $this->rooms, 2);
            }),
            'locations' => [
                'start' => [
                    'country' => $this->country_begin,
                    'city' => $this->city_begin,
                ],
                'end' => [
                    'country' => $this->country_end,
                    'city' => $this->city_end,
                ],
            ],
            'invoice' => $this->invoice,
            'ga' => $this->ga,
            'status' => [
                'id' => $this->status,
                'name' => $this->getStatusName(),
                'color' => $this->getStatusColor(),
                'background_color' => $this->getRowBackgroundColor(),
            ],
            'client' => $this->whenLoaded('client', function () {
                return [
                    'id' => $this->client->id,
                    'name' => $this->client->name,
                    'email' => $this->client->work_email,
                    'phone' => $this->client->work_phone,
                ];
            }),
            'responsible_users' => $this->whenLoaded('responsibleUsers', function () {
                return $this->responsibleUsers->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'avatar_url' => $user->avatar_url ?? null,
                    ];
                });
            }),
            'assigned_users' => $this->whenLoaded('assignedUsers', function () {
                return $this->assignedUsers->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'avatar_url' => $user->avatar_url ?? null,
                    ];
                });
            }),
            'responsible_user_names' => $this->responsible_user_names,
            'assigned_user_names' => $this->assigned_user_names,
            'tour_days' => $this->whenLoaded('tourDays', function () {
                return $this->tourDays->map(function ($day) {
                    return [
                        'id' => $day->id,
                        'date' => $day->date,
                        'day_number' => $day->day_number,
                        'title' => $day->title,
                        'description' => $day->description,
                        'packages_count' => $day->tourPackages ? $day->tourPackages->count() : 0,
                        'packages' => $this->when($day->relationLoaded('tourPackages'), function () use ($day) {
                            return $day->tourPackages->map(function ($package) {
                                return [
                                    'id' => $package->id,
                                    'type' => $package->type,
                                    'service_name' => $package->service_name,
                                    'time_from' => $package->time_from,
                                    'time_to' => $package->time_to,
                                    'cost' => $package->cost,
                                    'is_main' => $package->is_main,
                                ];
                            });
                        }),
                    ];
                });
            }),
            'tasks' => $this->whenLoaded('tasks', function () {
                return $this->tasks->map(function ($task) {
                    return [
                        'id' => $task->id,
                        'title' => $task->title,
                        'description' => $task->description,
                        'status' => $task->status,
                        'priority' => $task->priority,
                        'due_date' => $task->due_date,
                        'assigned_to' => $task->assigned_to,
                        'assigned_user_name' => $task->assignedUser ? $task->assignedUser->name : null,
                        'is_overdue' => $task->due_date && $task->due_date < now() && $task->status !== 'completed',
                    ];
                });
            }),
            'comments' => $this->whenLoaded('comments', function () {
                return $this->comments->map(function ($comment) {
                    return [
                        'id' => $comment->id,
                        'content' => $comment->content,
                        'user' => [
                            'id' => $comment->user->id,
                            'name' => $comment->user->name,
                            'avatar_url' => $comment->user->avatar_url ?? null,
                        ],
                        'created_at' => $comment->created_at,
                        'updated_at' => $comment->updated_at,
                        'is_edited' => $comment->created_at != $comment->updated_at,
                    ];
                });
            }),
            'statistics' => $this->when($request->routeIs('api.tours.show'), function () {
                return [
                    'total_packages' => $this->tourPackages ? $this->tourPackages->count() : 0,
                    'total_days' => $this->tourDays ? $this->tourDays->count() : 0,
                    'total_tasks' => $this->tasks ? $this->tasks->count() : 0,
                    'completed_tasks' => $this->tasks ? $this->tasks->where('status', 'completed')->count() : 0,
                    'overdue_tasks' => $this->tasks ? $this->tasks->filter(function ($task) {
                        return $task->due_date && $task->due_date < now() && $task->status !== 'completed';
                    })->count() : 0,
                    'total_cost' => $this->tourPackages ? $this->tourPackages->sum('cost') : 0,
                    'progress_percentage' => $this->calculateProgressPercentage(),
                ];
            }),
            'permissions' => [
                'can_edit' => auth()->user() && auth()->user()->can('tour.edit'),
                'can_delete' => auth()->user() && auth()->user()->can('tour.delete'),
                'can_clone' => auth()->user() && auth()->user()->can('tour.create'),
                'can_manage_packages' => auth()->user() && auth()->user()->can('tour_package.create'),
            ],
            'urls' => [
                'show' => route('tour.show', ['tour' => $this->id]),
                'edit' => route('tour.edit', ['tour' => $this->id]),
                'api_show' => route('api.tours.show', $this->id),
                'api_update' => route('api.tours.update', $this->id),
                'api_delete' => route('api.tours.destroy', $this->id),
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->whenLoaded('creator', function () {
                return [
                    'id' => $this->creator->id,
                    'name' => $this->creator->name,
                ];
            }),
            'last_updated_by' => $this->whenLoaded('updater', function () {
                return [
                    'id' => $this->updater->id,
                    'name' => $this->updater->name,
                ];
            }),
        ];
    }

    /**
     * Calculate tour progress percentage
     */
    protected function calculateProgressPercentage()
    {
        if (!$this->tasks || $this->tasks->isEmpty()) {
            return 0;
        }

        $totalTasks = $this->tasks->count();
        $completedTasks = $this->tasks->where('status', 'completed')->count();

        return round(($completedTasks / $totalTasks) * 100, 2);
    }

    /**
     * Get additional data when transformer is applied.
     */
    public function with($request)
    {
        return [
            'meta' => [
                'version' => '1.0',
                'timestamp' => now()->toISOString(),
                'request_id' => $request->header('X-Request-ID') ?? uniqid(),
            ],
        ];
    }
}