<?php

namespace App\Services;

use App\Tour;
use App\User;
use App\Client;
use App\Task;
use App\Services\CacheService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardService
{
    protected $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Get comprehensive dashboard data
     *
     * @param array $filters
     * @return array
     */
    public function getDashboardData(array $filters = []): array
    {
        $cacheKey = $this->cacheService->generateKey('dashboard_data', auth()->id(), $filters);

        return $this->cacheService->cacheDashboard($cacheKey, function () use ($filters) {
            return [
                'overview' => $this->getOverviewStats($filters),
                'tours' => $this->getTourStats($filters),
                'financial' => $this->getFinancialStats($filters),
                'recent_activity' => $this->getRecentActivity($filters),
                'upcoming_deadlines' => $this->getUpcomingDeadlines($filters),
                'performance' => $this->getPerformanceMetrics($filters),
                'charts' => $this->getChartData($filters),
            ];
        }, 600); // Cache for 10 minutes
    }

    /**
     * Get overview statistics
     *
     * @param array $filters
     * @return array
     */
    protected function getOverviewStats(array $filters = []): array
    {
        $dateFrom = $filters['date_from'] ?? now()->startOfMonth();
        $dateTo = $filters['date_to'] ?? now()->endOfMonth();

        return [
            'total_tours' => Tour::count(),
            'active_tours' => Tour::where('status', 2)->count(),
            'tours_this_month' => Tour::whereBetween('departure_date', [$dateFrom, $dateTo])->count(),
            'total_clients' => Client::count(),
            'active_users' => User::where('is_active', true)->count(),
            'pending_tasks' => Task::where('status', 'pending')->count(),
            'overdue_tasks' => Task::where('due_date', '<', now())
                                  ->where('status', '!=', 'completed')
                                  ->count(),
        ];
    }

    /**
     * Get tour-related statistics
     *
     * @param array $filters
     * @return array
     */
    protected function getTourStats(array $filters = []): array
    {
        $dateFrom = $filters['date_from'] ?? now()->startOfYear();
        $dateTo = $filters['date_to'] ?? now()->endOfYear();

        $tourQuery = Tour::whereBetween('departure_date', [$dateFrom, $dateTo]);

        $stats = $tourQuery->selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as draft,
            SUM(CASE WHEN status = 2 THEN 1 ELSE 0 END) as active,
            SUM(CASE WHEN status = 3 THEN 1 ELSE 0 END) as cancelled,
            SUM(CASE WHEN status = 4 THEN 1 ELSE 0 END) as completed,
            SUM(pax) as total_passengers,
            AVG(pax) as avg_passengers_per_tour,
            MAX(pax) as max_passengers,
            MIN(pax) as min_passengers
        ')->first();

        return [
            'total_tours' => $stats->total,
            'by_status' => [
                'draft' => $stats->draft,
                'active' => $stats->active,
                'cancelled' => $stats->cancelled,
                'completed' => $stats->completed,
            ],
            'passengers' => [
                'total' => $stats->total_passengers,
                'average_per_tour' => round($stats->avg_passengers_per_tour, 1),
                'max_in_tour' => $stats->max_passengers,
                'min_in_tour' => $stats->min_passengers,
            ],
            'upcoming_tours' => Tour::where('departure_date', '>', now())->count(),
            'tours_starting_this_week' => Tour::whereBetween('departure_date', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
            'tours_starting_next_week' => Tour::whereBetween('departure_date', [
                now()->addWeek()->startOfWeek(),
                now()->addWeek()->endOfWeek()
            ])->count(),
        ];
    }

    /**
     * Get financial statistics
     *
     * @param array $filters
     * @return array
     */
    protected function getFinancialStats(array $filters = []): array
    {
        $dateFrom = $filters['date_from'] ?? now()->startOfMonth();
        $dateTo = $filters['date_to'] ?? now()->endOfMonth();

        // Get tour packages cost data
        $packageCosts = DB::table('tour_packages')
            ->join('tour_days', 'tour_packages.tour_day_id', '=', 'tour_days.id')
            ->join('tours', 'tour_days.tour_id', '=', 'tours.id')
            ->whereBetween('tours.departure_date', [$dateFrom, $dateTo])
            ->selectRaw('
                SUM(tour_packages.cost) as total_cost,
                COUNT(tour_packages.id) as total_packages,
                AVG(tour_packages.cost) as avg_package_cost
            ')
            ->first();

        return [
            'total_tour_cost' => $packageCosts->total_cost ?? 0,
            'total_packages' => $packageCosts->total_packages ?? 0,
            'average_package_cost' => round($packageCosts->avg_package_cost ?? 0, 2),
            'cost_per_passenger' => $this->calculateCostPerPassenger($dateFrom, $dateTo),
            'revenue_projection' => $this->calculateRevenueProjection($dateFrom, $dateTo),
        ];
    }

    /**
     * Get recent activity
     *
     * @param array $filters
     * @return array
     */
    protected function getRecentActivity(array $filters = []): array
    {
        $limit = $filters['activity_limit'] ?? 10;

        return [
            'recent_tours' => Tour::with(['status', 'responsibleUsers'])
                                 ->latest()
                                 ->limit($limit)
                                 ->get()
                                 ->map(function ($tour) {
                                     return [
                                         'id' => $tour->id,
                                         'name' => $tour->name,
                                         'status' => $tour->getStatusName(),
                                         'departure_date' => $tour->departure_date,
                                         'responsible_users' => $tour->responsible_user_names,
                                         'created_at' => $tour->created_at,
                                     ];
                                 }),
            'recent_tasks' => Task::with(['assignedUser', 'tour'])
                                 ->latest()
                                 ->limit($limit)
                                 ->get()
                                 ->map(function ($task) {
                                     return [
                                         'id' => $task->id,
                                         'title' => $task->title,
                                         'status' => $task->status,
                                         'due_date' => $task->due_date,
                                         'assigned_user' => $task->assignedUser ? $task->assignedUser->name : null,
                                         'tour_name' => $task->tour ? $task->tour->name : null,
                                         'created_at' => $task->created_at,
                                     ];
                                 }),
        ];
    }

    /**
     * Get upcoming deadlines
     *
     * @param array $filters
     * @return array
     */
    protected function getUpcomingDeadlines(array $filters = []): array
    {
        $daysAhead = $filters['days_ahead'] ?? 7;

        return [
            'upcoming_departures' => Tour::with(['status', 'responsibleUsers'])
                                       ->whereBetween('departure_date', [
                                           now(),
                                           now()->addDays($daysAhead)
                                       ])
                                       ->orderBy('departure_date')
                                       ->get()
                                       ->map(function ($tour) {
                                           return [
                                               'id' => $tour->id,
                                               'name' => $tour->name,
                                               'departure_date' => $tour->departure_date,
                                               'days_until_departure' => now()->diffInDays($tour->departure_date),
                                               'status' => $tour->getStatusName(),
                                               'pax' => $tour->pax,
                                           ];
                                       }),
            'urgent_tasks' => Task::with(['assignedUser', 'tour'])
                                 ->where('due_date', '<=', now()->addDays($daysAhead))
                                 ->where('status', '!=', 'completed')
                                 ->orderBy('due_date')
                                 ->get()
                                 ->map(function ($task) {
                                     return [
                                         'id' => $task->id,
                                         'title' => $task->title,
                                         'due_date' => $task->due_date,
                                         'days_until_due' => now()->diffInDays($task->due_date),
                                         'is_overdue' => $task->due_date < now(),
                                         'assigned_user' => $task->assignedUser ? $task->assignedUser->name : null,
                                         'tour_name' => $task->tour ? $task->tour->name : null,
                                     ];
                                 }),
        ];
    }

    /**
     * Get performance metrics
     *
     * @param array $filters
     * @return array
     */
    protected function getPerformanceMetrics(array $filters = []): array
    {
        $dateFrom = $filters['date_from'] ?? now()->startOfMonth();
        $dateTo = $filters['date_to'] ?? now()->endOfMonth();

        return [
            'tour_completion_rate' => $this->calculateTourCompletionRate($dateFrom, $dateTo),
            'average_tour_duration' => $this->calculateAverageTourDuration($dateFrom, $dateTo),
            'task_completion_rate' => $this->calculateTaskCompletionRate($dateFrom, $dateTo),
            'client_satisfaction' => $this->calculateClientSatisfaction($dateFrom, $dateTo),
            'user_productivity' => $this->calculateUserProductivity($dateFrom, $dateTo),
        ];
    }

    /**
     * Get chart data for visualizations
     *
     * @param array $filters
     * @return array
     */
    protected function getChartData(array $filters = []): array
    {
        $dateFrom = $filters['date_from'] ?? now()->startOfYear();
        $dateTo = $filters['date_to'] ?? now()->endOfYear();

        return [
            'tours_by_month' => $this->getToursByMonth($dateFrom, $dateTo),
            'tours_by_status' => $this->getToursByStatus($dateFrom, $dateTo),
            'passengers_trend' => $this->getPassengersTrend($dateFrom, $dateTo),
            'revenue_trend' => $this->getRevenueTrend($dateFrom, $dateTo),
            'user_activity' => $this->getUserActivity($dateFrom, $dateTo),
        ];
    }

    /**
     * Calculate cost per passenger
     */
    protected function calculateCostPerPassenger($dateFrom, $dateTo): float
    {
        $totalCost = DB::table('tour_packages')
            ->join('tour_days', 'tour_packages.tour_day_id', '=', 'tour_days.id')
            ->join('tours', 'tour_days.tour_id', '=', 'tours.id')
            ->whereBetween('tours.departure_date', [$dateFrom, $dateTo])
            ->sum('tour_packages.cost');

        $totalPassengers = Tour::whereBetween('departure_date', [$dateFrom, $dateTo])
                              ->sum('pax');

        return $totalPassengers > 0 ? round($totalCost / $totalPassengers, 2) : 0;
    }

    /**
     * Calculate revenue projection
     */
    protected function calculateRevenueProjection($dateFrom, $dateTo): array
    {
        // This is a simplified calculation - in real world you'd have pricing data
        $upcomingTours = Tour::where('departure_date', '>', now())
                            ->whereBetween('departure_date', [$dateFrom, $dateTo])
                            ->get();

        $projectedRevenue = $upcomingTours->sum(function ($tour) {
            // Assume average revenue per passenger
            return $tour->pax * 1000; // $1000 per passenger (example)
        });

        return [
            'projected_revenue' => $projectedRevenue,
            'upcoming_tours_count' => $upcomingTours->count(),
            'projected_passengers' => $upcomingTours->sum('pax'),
        ];
    }

    /**
     * Calculate tour completion rate
     */
    protected function calculateTourCompletionRate($dateFrom, $dateTo): float
    {
        $totalTours = Tour::whereBetween('departure_date', [$dateFrom, $dateTo])->count();
        $completedTours = Tour::whereBetween('departure_date', [$dateFrom, $dateTo])
                             ->where('status', 4)
                             ->count();

        return $totalTours > 0 ? round(($completedTours / $totalTours) * 100, 2) : 0;
    }

    /**
     * Calculate average tour duration
     */
    protected function calculateAverageTourDuration($dateFrom, $dateTo): float
    {
        $tours = Tour::whereBetween('departure_date', [$dateFrom, $dateTo])
                    ->whereNotNull('retirement_date')
                    ->get(['departure_date', 'retirement_date']);

        if ($tours->isEmpty()) {
            return 0;
        }

        $totalDays = $tours->sum(function ($tour) {
            return Carbon::parse($tour->departure_date)->diffInDays(Carbon::parse($tour->retirement_date));
        });

        return round($totalDays / $tours->count(), 1);
    }

    /**
     * Calculate task completion rate
     */
    protected function calculateTaskCompletionRate($dateFrom, $dateTo): float
    {
        $totalTasks = Task::whereBetween('created_at', [$dateFrom, $dateTo])->count();
        $completedTasks = Task::whereBetween('created_at', [$dateFrom, $dateTo])
                             ->where('status', 'completed')
                             ->count();

        return $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0;
    }

    /**
     * Calculate client satisfaction (placeholder)
     */
    protected function calculateClientSatisfaction($dateFrom, $dateTo): float
    {
        // This would be based on actual feedback/rating system
        // For now, return a sample value
        return 85.5;
    }

    /**
     * Calculate user productivity
     */
    protected function calculateUserProductivity($dateFrom, $dateTo): array
    {
        $users = User::withCount([
            'responsibleTours' => function ($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('departure_date', [$dateFrom, $dateTo]);
            },
            'assignedTasks' => function ($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('created_at', [$dateFrom, $dateTo]);
            }
        ])->limit(10)->get();

        return $users->map(function ($user) {
            return [
                'name' => $user->name,
                'tours_count' => $user->responsible_tours_count,
                'tasks_count' => $user->assigned_tasks_count,
                'productivity_score' => ($user->responsible_tours_count * 10) + ($user->assigned_tasks_count * 2),
            ];
        })->sortByDesc('productivity_score')->values()->toArray();
    }

    /**
     * Get tours by month data
     */
    protected function getToursByMonth($dateFrom, $dateTo): array
    {
        return DB::table('tours')
            ->whereBetween('departure_date', [$dateFrom, $dateTo])
            ->selectRaw('
                YEAR(departure_date) as year,
                MONTH(departure_date) as month,
                COUNT(*) as count,
                SUM(pax) as passengers
            ')
            ->groupBy('year', 'month')
            ->orderBy('year', 'month')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => Carbon::create($item->year, $item->month, 1)->format('Y-m'),
                    'tours' => $item->count,
                    'passengers' => $item->passengers,
                ];
            })
            ->toArray();
    }

    /**
     * Get tours by status data
     */
    protected function getToursByStatus($dateFrom, $dateTo): array
    {
        return DB::table('tours')
            ->join('statuses', 'tours.status', '=', 'statuses.id')
            ->whereBetween('tours.departure_date', [$dateFrom, $dateTo])
            ->select('statuses.name', DB::raw('COUNT(*) as count'))
            ->groupBy('statuses.name')
            ->get()
            ->map(function ($item) {
                return [
                    'status' => $item->name,
                    'count' => $item->count,
                ];
            })
            ->toArray();
    }

    /**
     * Get passengers trend data
     */
    protected function getPassengersTrend($dateFrom, $dateTo): array
    {
        return DB::table('tours')
            ->whereBetween('departure_date', [$dateFrom, $dateTo])
            ->selectRaw('
                DATE(departure_date) as date,
                SUM(pax) as passengers
            ')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'passengers' => $item->passengers,
                ];
            })
            ->toArray();
    }

    /**
     * Get revenue trend data (placeholder)
     */
    protected function getRevenueTrend($dateFrom, $dateTo): array
    {
        // This would be based on actual revenue data
        // For now, generate sample data based on tour packages
        return $this->getToursByMonth($dateFrom, $dateTo);
    }

    /**
     * Get user activity data
     */
    protected function getUserActivity($dateFrom, $dateTo): array
    {
        return User::withCount([
            'responsibleTours' => function ($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('departure_date', [$dateFrom, $dateTo]);
            }
        ])
        ->orderByDesc('responsible_tours_count')
        ->limit(10)
        ->get()
        ->map(function ($user) {
            return [
                'name' => $user->name,
                'tours_count' => $user->responsible_tours_count,
            ];
        })
        ->toArray();
    }

    /**
     * Clear dashboard caches
     */
    public function clearCache(): bool
    {
        return $this->cacheService->flushTags([
            CacheService::CACHE_TAGS['dashboard'],
            CacheService::CACHE_TAGS['statistics'],
        ]);
    }
}