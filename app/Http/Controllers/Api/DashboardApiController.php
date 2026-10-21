<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Tour;
use App\Task;
use App\Client;
use Illuminate\Http\Request;

class DashboardApiController extends Controller
{
    public function index()
    {
        return response()->json(['success' => true, 'data' => [
            'statistics' => $this->counts(),
            'upcoming_deadlines' => $this->deadlines()->limit(10)->get(),
        ]]);
    }

    private function counts(): array
    {
        return ['total_tours' => Tour::count(), 'total_clients' => Client::count(), 'total_tasks' => Task::count()];
    }

    private function deadlines()
    {
        return Task::whereNotNull('dead_line')->where('dead_line', '>=', now())->orderBy('dead_line');
    }

    public function statistics() { return response()->json(['success' => true, 'data' => $this->counts()]); }

    public function recentActivity()
    {
        return response()->json(['success' => true, 'data' => \Spatie\Activitylog\Models\Activity::latest()->limit(20)->get()]);
    }

    public function upcomingDeadlines() { return response()->json(['success' => true, 'data' => $this->deadlines()->paginate(15)]); }

    public function charts()
    {
        return response()->json(['success' => true, 'data' => Tour::select('status')->selectRaw('COUNT(*) as total')->groupBy('status')->get()]);
    }

    public function clearCache(\App\Services\CacheService $cache)
    {
        $ok = $cache->clearAll();
        return response()->json(['success' => $ok], $ok ? 200 : 500);
    }
}
