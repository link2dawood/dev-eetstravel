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
        $this->middleware('auth');
    }

    public function index(Request $request): JsonResponse
    {
        $request->validate(['per_page' => 'nullable|integer|min:1|max:100', 'page' => 'nullable|integer|min:1', 'search' => 'nullable|string|max:255']);
        $cacheKey = $this->cacheService->generateKey('tasks_index', $request->all());

        $tasks = $this->cacheService->remember($cacheKey, function () use ($request) {
            $query = Task::with(['status', 'tour', 'assignedTo']);

            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where('content', 'like', "%{$search}%");
            }

            if ($request->filled('status')) {
                $query->where('status', $request->input('status'));
            }

            if ($request->filled('priority')) {
                $query->where('priority', $request->input('priority'));
            }

            return $query->orderBy('dead_line', 'asc')->paginate($request->input('per_page', 15));
        }, 0);

        return response()->json($tasks);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'content' => 'required|string',
                'deadline' => 'nullable|date',
                'tour_id' => 'nullable|exists:tours,id',
                'assigned_to' => 'sometimes|required|exists:users,id',
                'status_id' => 'required|exists:status,id',
                'priority' => 'nullable|boolean',
                'task_type' => 'sometimes|required|in:1,2'
            ]);

            $validated = $this->toStorage($validated);
            $validated += ['assign' => (string) $request->user()->id, 'task_type' => '1', 'tour' => null];
            $task = Task::create($validated);
            $task->load(['status', 'tour', 'assignedTo']);

            $this->cacheService->flushTags(['tasks', 'dashboard']);

            return response()->json([
                'success' => true,
                'message' => 'Task created successfully',
                'data' => $task
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
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
                'assigned_to' => 'sometimes|required|exists:users,id',
                'status_id' => 'sometimes|required|exists:status,id',
                'priority' => 'sometimes|nullable|boolean',
                'task_type' => 'sometimes|in:1,2'
            ]);

            $task->update($this->toStorage($validated));
            $task->load(['status', 'tour', 'assignedTo']);

            $this->cacheService->flushTags(['tasks', 'dashboard']);

            return response()->json([
                'success' => true,
                'message' => 'Task updated successfully',
                'data' => $task
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
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

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
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
        $tasks = Task::where('dead_line', '<', now())
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
                'status_id' => 'required|exists:status,id'
            ]);

            $task->update($this->toStorage($validated));
            $task->load(['status', 'tour', 'assignedTo']);

            $this->cacheService->flushTags(['tasks', 'dashboard']);

            return response()->json([
                'success' => true,
                'message' => 'Task status updated successfully',
                'data' => $task
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update task status',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    private function toStorage(array $data): array
    {
        foreach (['deadline' => 'dead_line', 'tour_id' => 'tour', 'assigned_to' => 'assign', 'status_id' => 'status'] as $api => $column) {
            if (array_key_exists($api, $data)) { $data[$column] = $data[$api]; unset($data[$api]); }
        }
        return $data;
    }
}
