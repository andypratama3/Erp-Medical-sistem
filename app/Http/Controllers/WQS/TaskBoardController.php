<?php
namespace App\Http\Controllers\WQS;

use App\Http\Controllers\Controller;
use App\Models\TaskBoard;
use Illuminate\Http\Request;

class TaskBoardController extends Controller
{
    public function index(Request $request)
    {
        $query = TaskBoard::where('module', 'wqs')
            ->with(['taskable.customer', 'taskable.office']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('task_status', $request->status);
        }

        // Filter by search
        if ($request->filled('search')) {
            $query->whereHas('taskable', function($q) use ($request) {
                $q->where('do_number', 'like', '%' . $request->search . '%');
            });
        }

        $tasks = $query->latest()->paginate(15);

        // Stats
        $stats = [
            'pending' => TaskBoard::where('module', 'wqs')->where('task_status', 'pending')->count(),
            'in_progress' => TaskBoard::where('module', 'wqs')->where('task_status', 'in_progress')->count(),
            'completed' => TaskBoard::where('module', 'wqs')->where('task_status', 'completed')->count(),
        ];

        return view('pages.wqs.task_board.index', compact('tasks', 'stats'));
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
