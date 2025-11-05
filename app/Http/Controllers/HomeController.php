<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Task;   // <-- MAKE SURE THIS IS HERE
use App\Status; // <-- MAKE SURE THIS IS HERE

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // ==========================================================
        // == THIS IS THE REQUIRED CODE ==
        // ==========================================================
        
        // Todo Tasks
        $todoTasks = Task::with(['status', 'assignedTo', 'tour', 'epic', 'assigned_users'])
            ->whereHas('status', function ($query) {
                $query->where('is_completed', false)
                    ->where('is_aborted', false);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'todo_page');

        // Completed Tasks
        $completedTasks = Task::with(['status', 'assignedTo', 'tour', 'epic', 'assigned_users'])
            ->whereHas('status', function ($query) {
                $query->where('is_completed', true);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'completed_page');

        // Aborted Tasks
        $abortedTasks = Task::with(['status', 'assignedTo', 'tour', 'epic', 'assigned_users'])
            ->whereHas('status', function ($query) {
                $query->where('is_aborted', true);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'aborted_page');

        // Statuses
        $statuses = Status::query()
            ->orderBy('sort_order', 'asc')
            ->where('type', 'task')
            ->get();

        // ==========================================================
        // == PASS THE VARIABLES TO YOUR DASHBOARD VIEW ==
        // ==========================================================
        
        // The view name comes from your stack trace
        return view('scaffold-interface.dashboard.dashboard', compact(
            'todoTasks', 
            'completedTasks', 
            'abortedTasks', 
            'statuses'
            // ... add any other variables your dashboard already needs
        ));
    }
}