<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Client;
use App\Http\Requests\Client\StoreClientRequest;
use App\Http\Requests\Client\UpdateClientRequest;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ClientApiController extends Controller
{
    protected $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request): JsonResponse
    {
        $cacheKey = $this->cacheService->generateKey('clients_index', $request->all());

        $clients = $this->cacheService->remember($cacheKey, function () use ($request) {
            $query = Client::with(['country', 'city']);

            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('work_email', 'like', "%{$search}%")
                      ->orWhere('work_phone', 'like', "%{$search}%");
                });
            }

            return $query->paginate($request->input('per_page', 15));
        }, 300);

        return response()->json($clients);
    }

    public function store(StoreClientRequest $request): JsonResponse
    {
        try {
            $client = Client::create($request->validated());
            $client->load(['country', 'city']);

            $this->cacheService->flushTags(['clients']);

            return response()->json([
                'success' => true,
                'message' => 'Client created successfully',
                'data' => $client
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create client',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Client $client): JsonResponse
    {
        $client->load(['country', 'city', 'tours']);

        return response()->json([
            'success' => true,
            'data' => $client
        ]);
    }

    public function update(UpdateClientRequest $request, Client $client): JsonResponse
    {
        try {
            $client->update($request->validated());
            $client->load(['country', 'city']);

            $this->cacheService->flushTags(['clients']);

            return response()->json([
                'success' => true,
                'message' => 'Client updated successfully',
                'data' => $client
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update client',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Client $client): JsonResponse
    {
        try {
            $client->delete();
            $this->cacheService->flushTags(['clients']);

            return response()->json([
                'success' => true,
                'message' => 'Client deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete client',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function search(Request $request): JsonResponse
    {
        $query = $request->input('q', '');

        $clients = Client::where('name', 'like', "%{$query}%")
            ->orWhere('work_email', 'like', "%{$query}%")
            ->limit(10)
            ->get(['id', 'name', 'work_email']);

        return response()->json($clients);
    }

    public function tours(Client $client): JsonResponse
    {
        $tours = $client->tours()->with(['status', 'city'])->paginate(15);

        return response()->json($tours);
    }
}