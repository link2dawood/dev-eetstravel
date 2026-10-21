<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserApiController extends Controller
{
    protected $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
        $this->middleware('auth');
    }

    public function index(Request $request): JsonResponse
    {
        $request->validate(['per_page' => 'nullable|integer|min:1|max:100', 'page' => 'nullable|integer|min:1', 'search' => 'nullable|string|max:255']);
        $cacheKey = $this->cacheService->generateKey('users_index', $request->all());

        $users = $this->cacheService->remember($cacheKey, function () use ($request) {
            $query = User::query();

            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            if ($request->filled('active')) {
                $query->where('is_active', $request->boolean('active'));
            }

            return $query->paginate($request->input('per_page', 15));
        }, 0);

        return response()->json($users);
    }

    public function show(User $user): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        try {
            $data = $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|unique:users,email,' . $user->id,
                'is_active' => 'sometimes|boolean'
            ]);
            $user->update($data);

            $this->cacheService->flushTags(['users']);

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => $user
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function search(Request $request): JsonResponse
    {
        $query = $request->input('q', '');

        $users = User::where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")->orWhere('email', 'like', "%{$query}%");
            })->where('is_active', true)

            ->limit(10)
            ->get(['id', 'name', 'email']);

        return response()->json($users);
    }

    public function tours(User $user): JsonResponse
    {
        $tours = $user->tours()->with('status')->paginate(15);

        return response()->json($tours);
    }

    public function tasks(User $user): JsonResponse
    {
        $tasks = $user->tasks()->with(['status', 'tour'])->paginate(15);

        return response()->json($tasks);
    }
}