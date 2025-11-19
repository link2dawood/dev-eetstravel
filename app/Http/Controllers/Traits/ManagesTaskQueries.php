<?php

namespace App\Http\Controllers\Traits;

use App\Task;
use App\Status;
use Illuminate\Support\Facades\Auth;

trait ManagesTaskQueries
{
    /**
     * Fetches To-Do, Completed, and Aborted tasks for the currently logged-in user.
     */
    public function getTaskDataForProfile(\App\User $profileUser)
    {
        // Use the ID of the user whose profile is being viewed/loaded
        $userId = $profileUser->id;

        // 1. To-Do (Pending)
        $todoTasks = Task::with(['status', 'assignedTo', 'tour', 'epic', 'assigned_users'])
            ->where('assign', $userId) 
            ->whereHas('status', function ($query) {
                $query->where('is_completed', false)
                      ->where('is_aborted', false);
            })
            ->orderBy('dead_line', 'asc')
            ->paginate(10, ['*'], 'todo_page');

        // 2. Completed
        $completedTasks = Task::with(['status', 'assignedTo', 'tour', 'epic', 'assigned_users'])
            ->where('assign', $userId)
            ->whereHas('status', function ($query) {
                $query->where('is_completed', true);
            })
            ->orderBy('dead_line', 'desc')
            ->paginate(10, ['*'], 'completed_page');

        // 3. Aborted
        $abortedTasks = Task::with(['status', 'assignedTo', 'tour', 'epic', 'assigned_users'])
            ->where('assign', $userId)
            ->whereHas('status', function ($query) {
                $query->where('is_aborted', true);
            })
            ->orderBy('dead_line', 'desc')
            ->paginate(10, ['*'], 'aborted_page');
            
        // Statuses for the dropdowns
        $statuses = Status::where('type', 'task')->orderBy('sort_order', 'asc')->get();

        return [
            'todoTasks' => $todoTasks,
            'completedTasks' => $completedTasks,
            'abortedTasks' => $abortedTasks,
            'statuses' => $statuses,
        ];
    }
}