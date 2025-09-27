<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

class SettingsApiController extends Controller
{
    protected $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
        $this->middleware('auth:sanctum');
    }

    public function cacheStatus(): JsonResponse
    {
        try {
            $stats = $this->cacheService->getStats();
            $isWorking = $this->cacheService->isWorking();

            return response()->json([
                'success' => true,
                'data' => [
                    'status' => $isWorking ? 'working' : 'not_working',
                    'stats' => $stats
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get cache status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function clearCache(): JsonResponse
    {
        try {
            $result = $this->cacheService->clearAll();

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cache cleared successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to clear cache'
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function warmUpCache(): JsonResponse
    {
        try {
            $results = $this->cacheService->warmUp();

            return response()->json([
                'success' => true,
                'message' => 'Cache warmed up successfully',
                'data' => $results
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to warm up cache',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}