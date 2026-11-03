<?php

namespace App\Http\Controllers;

use App\Helper\ActivityDescription;
use App\Services\ActivityAccess;
use Illuminate\Http\Request;

class ActivitiesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permissions.required');
    }

    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => 'nullable|string|max:255',
            'user' => 'nullable|integer|min:1',
            'module' => 'nullable|string|max:255',
            'from' => 'nullable|date_format:Y-m-d',
            'to' => 'nullable|date_format:Y-m-d' . ($request->filled('from') ? '|after_or_equal:from' : ''),
            'per_page' => 'nullable|integer|in:10,25,50,100',
        ]);
        $query = ActivityAccess::query($request->user())
            ->leftJoin('users', function ($join) {
                $join->on('users.id', '=', 'activity_log.causer_id')
                    ->where('activity_log.causer_type', '=', \App\User::class);
            })
            ->select('activity_log.*', 'users.name as causer');
        if (!empty($filters['user'])) $query->where('activity_log.causer_id', $filters['user'])->where('activity_log.causer_type', \App\User::class);
        if (!empty($filters['module'])) $query->where('activity_log.log_name', $filters['module']);
        if (!empty($filters['from'])) $query->where('activity_log.created_at', '>=', $filters['from'] . ' 00:00:00');
        if (!empty($filters['to'])) $query->where('activity_log.created_at', '<', \Carbon\Carbon::parse($filters['to'])->addDay()->format('Y-m-d'));
        if (isset($filters['q']) && $filters['q'] !== '') {
            $pattern = '%' . strtr($filters['q'], ['!' => '!!', '%' => '!%', '_' => '!_']) . '%';
            $query->where(function ($query) use ($pattern) {
                $query->whereRaw("activity_log.description LIKE ? ESCAPE '!'", [$pattern])
                    ->orWhereRaw("users.name LIKE ? ESCAPE '!'", [$pattern]);
            });
        }
        $activitiesData = $query->orderByDesc('activity_log.created_at')->orderByDesc('activity_log.id')
            ->paginate($filters['per_page'] ?? 50)->withQueryString();
        $activitiesData->getCollection()->each(function ($activity) {
            $activity->formatted_description = ActivityDescription::plainText($activity->description);
            $activity->safe_link = ActivityAccess::link($activity->getExtraProperty('link'));
        });
        return view('activities.index', compact('activitiesData'));
    }
}
