<?php

namespace App\Http\Controllers\SCM;

use App\Http\Controllers\Controller;
use App\Models\TaskBoard;
use Illuminate\Http\Request;

class TaskBoardController extends Controller
{
    public function index(Request $request)
    {
        $query = TaskBoard::where('module', 'scm')
            ->with(['taskable.customer', 'taskable.office']);

        if ($request->filled('status')) {
            $query->where('task_status', $request->status);
        }

        if ($request->filled('search')) {
            $query->whereHas('taskable', function($q) use ($request) {
                $q->where('do_number', 'like', '%' . $request->search . '%');
            });
        }

        $tasks = $query->latest()->paginate(15);

        $stats = [
            'pending' => TaskBoard::where('module', 'scm')->where('task_status', 'pending')->count(),
            'in_progress' => TaskBoard::where('module', 'scm')->where('task_status', 'in_progress')->count(),
            'completed' => TaskBoard::where('module', 'scm')->where('task_status', 'completed')->count(),
        ];

        return view('pages.scm.task_board.index', compact('tasks', 'stats'));
    }

    public function updateStatus(Request $request, TaskBoard $taskBoard)
    {
        $validated = $request->validate([
            'task_status' => 'required|in:pending,in_progress,completed',
        ]);

        $taskBoard->update($validated);

        return back()->with('success', 'Task status updated successfully');
    }
}