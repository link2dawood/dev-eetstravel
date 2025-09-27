<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Task;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TaskApiController extends Controller
{
    protected $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request): JsonResponse
    {
        $cacheKey = $this->cacheService->generateKey('tasks_index', $request->all());

        $tasks = $this->cacheService->remember($cacheKey, function () use ($request) {
            $query = Task::with(['status', 'tour', 'assignedTo']);

            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where('content', 'like', "%{$search}%");
            }

            if ($request->filled('status')) {
                $query->where('status_id', $request->input('status'));
            }

            if ($request->filled('priority')) {
                $query->where('priority', $request->input('priority'));
            }

            return $query->orderBy('deadline', 'asc')->paginate($request->input('per_page', 15));
        }, 300);

        return response()->json($tasks);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'content' => 'required|string',
                'deadline' => 'nullable|date',
                'tour_id' => 'nullable|exists:tours,id',
                'assigned_to' => 'nullable|exists:users,id',
                'status_id' => 'nullable|exists:statuses,id',
                'priority' => 'nullable|in:low,medium,high',
                'task_type' => 'nullable|string'
            ]);

            $task = Task::create($validated);
            $task->load(['status', 'tour', 'assignedTo']);

            $this->cacheService->flushTags(['tasks', 'dashboard']);

            return response()->json([
                'success' => true,
                'message' => 'Task created successfully',
                'data' => $task
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create task',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Task $task): JsonResponse
    {
        $task->load(['status', 'tour', 'assignedTo']);

        return response()->json([
            'success' => true,
            'data' => $task
        ]);
    }

    public function update(Request $request, Task $task): JsonResponse
    {
        try {
            $validated = $request->validate([
                'content' => 'sometimes|string',
                'deadline' => 'sometimes|nullable|date',
                'tour_id' => 'sometimes|nullable|exists:tours,id',
                'assigned_to' => 'sometimes|nullable|exists:users,id',
                'status_id' => 'sometimes|nullable|exists:statuses,id',
                'priority' => 'sometimes|nullable|in:low,medium,high',
                'task_type' => 'sometimes|nullable|string'
            ]);

            $task->update($validated);
            $task->load(['status', 'tour', 'assignedTo']);

            $this->cacheService->flushTags(['tasks', 'dashboard']);

            return response()->json([
                'success' => true,
                'message' => 'Task updated successfully',
                'data' => $task
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update task',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Task $task): JsonResponse
    {
        try {
            $task->delete();
            $this->cacheService->flushTags(['tasks', 'dashboard']);

            return response()->json([
                'success' => true,
                'message' => 'Task deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete task',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function search(Request $request): JsonResponse
    {
        $query = $request->input('q', '');

        $tasks = Task::where('content', 'like', "%{$query}%")
            ->with(['tour', 'assignedTo'])
            ->limit(10)
            ->get();

        return response()->json($tasks);
    }

    public function overdue(Request $request): JsonResponse
    {
        $tasks = Task::where('deadline', '<', now())
            ->whereHas('status', function ($q) {
                $q->where('name', '!=', 'Completed');
            })
            ->with(['status', 'tour', 'assignedTo'])
            ->paginate($request->input('per_page', 15));

        return response()->json($tasks);
    }

    public function updateStatus(Request $request, Task $task): JsonResponse
    {
        try {
            $validated = $request->validate([
                'status_id' => 'required|exists:statuses,id'
            ]);

            $task->update($validated);
            $task->load(['status', 'tour', 'assignedTo']);

            $this->cacheService->flushTags(['tasks', 'dashboard']);

            return response()->json([
                'success' => true,
                'message' => 'Task status updated successfully',
                'data' => $task
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update task status',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}